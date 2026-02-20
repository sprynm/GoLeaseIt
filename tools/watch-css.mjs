// tools/watch-css.mjs
import chokidar from 'chokidar';
import path from 'path';
import { fileURLToPath } from 'url';
import { buildCSS, cssOutputs } from './build-css.mjs';
import { uploadCSS } from './upload-css.mjs';
import notifier from 'node-notifier';

const projectRoot = fileURLToPath(new URL('..', import.meta.url));
const watchGlobs = [path.join(projectRoot, 'webroot', 'css', 'scss', '**', '*.scss')];

let building = false;
let needsRebuild = false;

function toast(message) {
  try {
    notifier.notify({
      title: 'CSS Uploaded',
      message,
      appID: 'Go Lease It Watch',   // shown in Windows notifications
      wait: false,            // we don’t handle click/timeout events
      sound: false            // set true if you want a chime
    });
  } catch {}
}

function hhmm() {
  return new Date().toLocaleTimeString('en-CA', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  });
}

async function triggerBuild(event, filePath) {
  if (event && filePath) {
    const relative = path.relative(projectRoot, filePath);
    if (event === 'change') {
      console.log(`[watch] ${hhmm()} ${event} ${relative}`);
    } else {
      console.log(`[watch] ${event} ${relative}`);
    }
  }

  if (building) {
    needsRebuild = true;
    return;
  }

  building = true;
  try {
    await buildCSS({ mode: 'dev', log: (msg) => console.log(`[watch] ${msg}`) });
    for (const output of cssOutputs) {
      await uploadCSS(output, { log: (msg) => console.log(`[watch] ${msg}`) });
    }
    toast(`${cssOutputs.map((file) => path.basename(file)).join(', ')} deployed at ${hhmm()}`);
  } catch (error) {
    console.error('[watch] Build error:', error);
  } finally {
    building = false;
    if (needsRebuild) {
      needsRebuild = false;
      await triggerBuild();
    }
  }
}

async function run() {
  await triggerBuild('initial', watchGlobs[0]);
  const watcher = chokidar.watch(watchGlobs, {
    ignoreInitial: true,
    awaitWriteFinish: { stabilityThreshold: 150, pollInterval: 50 }
  });

  watcher.on('all', triggerBuild);
  console.log('[watch] Watching SCSS for changes…');
}

run().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
