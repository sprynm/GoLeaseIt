import path from 'node:path';
import fs from 'node:fs/promises';
import { pathToFileURL } from 'node:url';
import { buildCSS, cssOutput } from './build-css.mjs';
import { ftpTest } from './upload-css.mjs';

async function ensureLocalCSS(target, { buildIfMissing = true, log = console.log } = {}) {
  try {
    await fs.access(target);
  } catch (error) {
    if (error?.code === 'ENOENT' && buildIfMissing) {
      log(`[ftp-test] Local CSS missing, running build...`);
      await buildCSS({ mode: 'dev', log });
      return ensureLocalCSS(target, { buildIfMissing: false, log });
    }
    throw error;
  }
  return target;
}

async function run() {
  const args = process.argv.slice(2);
  const localFile = args[0] ? path.resolve(process.cwd(), args[0]) : cssOutput;

  const log = (msg) => console.log(msg);
  await ensureLocalCSS(localFile, { log });
  const result = await ftpTest(localFile, { log });

  log(
    `[ftp-test] Result: ${JSON.stringify(
      {
        remotePath: result.remotePath,
        exists: result.exists,
        matches: result.matches
      },
      null,
      2
    )}`
  );
}

const invokedDirectly =
  typeof process !== 'undefined' &&
  process.argv[1] &&
  pathToFileURL(process.argv[1]).href === import.meta.url;

if (invokedDirectly) {
  run().catch((error) => {
    console.error('[ftp-test] Failed:', error);
    process.exitCode = 1;
  });
}
