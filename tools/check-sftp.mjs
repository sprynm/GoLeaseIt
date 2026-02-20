import 'dotenv/config';
import SftpClient from 'ssh2-sftp-client';

const required = ['SFTP_HOST', 'SFTP_PORT', 'SFTP_USER', 'SFTP_PASS', 'REMOTE_BASE'];
const missing = required.filter((k) => !process.env[k]);
if (missing.length) {
  console.error(`Missing required env vars: ${missing.join(', ')}`);
  process.exit(2);
}

const sftp = new SftpClient();
const host = process.env.SFTP_HOST;
const port = Number(process.env.SFTP_PORT || 22);
const username = process.env.SFTP_USER;
const password = process.env.SFTP_PASS;
const remoteBase = process.env.REMOTE_BASE.replace(/\\/g, '/');
const targetPath = `${remoteBase.replace(/\/$/, '')}/stylesheet.css`;

try {
  await sftp.connect({ host, port, username, password });
  const exists = await sftp.exists(targetPath);
  if (exists) {
    console.log('connection ok');
    console.log(`Found: ${targetPath}`);
    process.exit(0);
  }

  console.error('error');
  console.error(`File not found: ${targetPath}`);
  process.exit(3);
} catch (err) {
  console.error('error');
  console.error(err?.message || err);
  process.exit(1);
} finally {
  try {
    await sftp.end();
  } catch {}
}
