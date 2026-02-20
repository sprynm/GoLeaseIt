// upload-css.mjs
import 'dotenv/config';
import SFTPClient from 'ssh2-sftp-client';
import path from 'node:path';
import fs from 'node:fs/promises';
import crypto from 'node:crypto';

const {
  SFTP_HOST,
  SFTP_PORT = 2022,
  SFTP_USER,
  SFTP_PASS,
  REMOTE_BASE
} = process.env;

function requireConfig() {
  if (!SFTP_HOST || !SFTP_USER || !SFTP_PASS || !REMOTE_BASE) {
    throw new Error(
      'Missing SFTP configuration. Ensure SFTP_HOST, SFTP_USER, SFTP_PASS, and REMOTE_BASE are defined.'
    );
  }
}

function normaliseRemoteDir(base) {
  return base.replace(/\\/g, '/').replace(/\/$/, '');
}

function createClient() {
  requireConfig();
  return new SFTPClient();
}

export async function uploadCSS(localFile, { log = console.log } = {}) {
  if (!localFile) {
    throw new Error('uploadCSS requires a localFile path.');
  }

  const sftp = createClient();
  const remoteDir = normaliseRemoteDir(REMOTE_BASE);
  const remotePath = `${remoteDir}/${path.basename(localFile)}`;
  const tmp = `${remotePath}.tmp`;

  try {
    await sftp.connect({
      host: SFTP_HOST,
      port: Number(SFTP_PORT),
      username: SFTP_USER,
      password: SFTP_PASS
    });
    await sftp.mkdir(remoteDir, true);
    await sftp.fastPut(localFile, tmp);
    let renamed = false;
    try {
      await sftp.rename(tmp, remotePath); // preferred atomic swap
      renamed = true;
    } catch (error) {
      const message = error?.message ?? '';
      const code = error?.code;
      const isGenericFailure = code === 4 || /failure/i.test(message);

      if (isGenericFailure) {
        // Some servers require removing the destination first.
        await sftp.delete(remotePath).catch(() => {});
        try {
          await sftp.rename(tmp, remotePath);
          renamed = true;
        } catch (retryError) {
          // Fallback: upload directly to the destination.
          await sftp.fastPut(localFile, remotePath);
          renamed = true;
          // Clean up temp file if it still exists.
          await sftp.delete(tmp).catch(() => {});
          log(
            `[upload] rename failed (${retryError?.message ?? retryError}), uploaded directly.`
          );
        }
      } else {
        // Unknown failure, attempt fallback then rethrow if needed.
        await sftp.fastPut(localFile, remotePath);
        renamed = true;
        await sftp.delete(tmp).catch(() => {});
        log(
          `[upload] rename failed (${message}), uploaded directly instead.`
        );
      }
    }

    if (!renamed) {
      throw new Error('uploadCSS: Unable to move temp file into place.');
    }
    log(`[upload] ${localFile} -> ${remotePath}`);
  } finally {
    try {
      await sftp.end();
    } catch {}
  }
}

function hashBuffer(buffer) {
  return crypto.createHash('sha256').update(buffer).digest('hex');
}

async function hashFile(filePath) {
  const file = await fs.readFile(filePath);
  return hashBuffer(file);
}

export async function ftpTest(localFile, { log = console.log } = {}) {
  if (!localFile) {
    throw new Error('ftpTest requires a localFile path to compare.');
  }

  const remoteDir = normaliseRemoteDir(REMOTE_BASE);
  const remotePath = `${remoteDir}/${path.basename(localFile)}`;
  const sftp = createClient();

  log(`[ftp-test] Connecting to ${SFTP_USER}@${SFTP_HOST}:${SFTP_PORT}`);

  try {
    await sftp.connect({
      host: SFTP_HOST,
      port: Number(SFTP_PORT),
      username: SFTP_USER,
      password: SFTP_PASS
    });

    const cwd = await sftp.cwd().catch(() => null);
    if (cwd) {
      log(`[ftp-test] Server working directory: ${cwd}`);
    }

    log(`[ftp-test] Checking ${remotePath}`);

    const remoteStat = await sftp.stat(remotePath).catch((error) => {
      if (error?.code === 2 || /no such file/i.test(error?.message ?? '')) {
        return null;
      }
      throw error;
    });

    if (!remoteStat) {
      log(`[ftp-test] Remote file not found at ${remotePath}`);
      return { connected: true, remotePath, exists: false, matches: false };
    }

    const localStat = await fs.stat(localFile).catch((error) => {
      if (error?.code === 'ENOENT') {
        throw new Error(`Local file not found: ${localFile}`);
      }
      throw error;
    });

    const [localHash, remoteBuffer] = await Promise.all([
      hashFile(localFile),
      sftp.get(remotePath)
    ]);

    const remoteHash = hashBuffer(remoteBuffer);
    const matches = localHash === remoteHash;

    log(
      `[ftp-test] Local size ${localStat.size} bytes; remote size ${remoteStat.size} bytes`
    );
    log(`[ftp-test] Hash compare: ${matches ? 'MATCH' : 'DIFFER'}`);

    return {
      connected: true,
      remotePath,
      exists: true,
      matches,
      localHash,
      remoteHash
    };
  } finally {
    try {
      await sftp.end();
      log('[ftp-test] Connection closed.');
    } catch {}
  }
}
