import { fileURLToPath } from 'url';
import path from 'path';
import fs from 'fs/promises';
import postcss from 'postcss';

const projectRoot = fileURLToPath(new URL('..', import.meta.url));
const cssInput = path.join(projectRoot, 'webroot', 'css', 'stylesheet.css');
const cssOutput = path.join(projectRoot, 'webroot', 'css', 'stylesheet-opt.css');

const scanRoots = [
  path.join(projectRoot, 'View'),
  path.join(projectRoot, 'Plugin'),
  path.join(projectRoot, 'CoreFiles', 'View'),
  path.join(projectRoot, 'webroot', 'js'),
  path.join(projectRoot, 'docs', 'temp')
];

const textExtensions = new Set([
  '.php',
  '.ctp',
  '.html',
  '.htm',
  '.md',
  '.js',
  '.json',
  '.txt'
]);

async function walk(dir) {
  const entries = await fs.readdir(dir, { withFileTypes: true });
  const files = [];

  for (const entry of entries) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...(await walk(full)));
      continue;
    }
    if (textExtensions.has(path.extname(entry.name).toLowerCase())) {
      files.push(full);
    }
  }

  return files;
}

function addClassesFromText(text, classes) {
  const classAttr = /class\s*=\s*["']([^"']+)["']/g;
  let match;
  while ((match = classAttr.exec(text))) {
    match[1]
      .split(/\s+/)
      .map((token) => token.trim())
      .filter(Boolean)
      .forEach((token) => classes.add(token));
  }

  const classListOps = /classList\.(?:add|remove|toggle|contains)\(([^)]*)\)/g;
  while ((match = classListOps.exec(text))) {
    const args = match[1];
    const quoted = args.match(/["']([^"']+)["']/g) || [];
    quoted
      .map((token) => token.slice(1, -1))
      .filter(Boolean)
      .forEach((token) => classes.add(token));
  }

  const querySelectors = /querySelector(?:All)?\(\s*["']([^"']+)["']\s*\)/g;
  while ((match = querySelectors.exec(text))) {
    const selector = match[1];
    const classMatches = selector.match(/\.(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)/g) || [];
    classMatches
      .map((token) => token.slice(1))
      .filter(Boolean)
      .forEach((token) => classes.add(token));
  }
}

async function collectUsedClasses() {
  const classes = new Set();

  for (const root of scanRoots) {
    let stat;
    try {
      stat = await fs.stat(root);
    } catch {
      continue;
    }
    if (!stat.isDirectory()) continue;

    const files = await walk(root);
    for (const file of files) {
      const text = await fs.readFile(file, 'utf8');
      addClassesFromText(text, classes);
    }
  }

  return classes;
}

function selectorClasses(selector) {
  const matches = selector.match(/\.(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)/g) || [];
  return matches.map((token) => token.slice(1));
}

function shouldKeepRule(node, usedClasses) {
  const selector = node.selector || '';

  if (selector.includes(':root')) {
    return true;
  }

  const classes = selectorClasses(selector);
  if (classes.length === 0) {
    return false;
  }

  return classes.some((className) => usedClasses.has(className));
}

function filterNode(node, usedClasses) {
  if (node.type === 'rule') {
    return shouldKeepRule(node, usedClasses) ? node.clone() : null;
  }

  if (node.type === 'atrule') {
    if (node.name === 'font-face' || node.name === 'keyframes') {
      return node.clone();
    }

    if (!node.nodes || node.nodes.length === 0) {
      return null;
    }

    const clone = node.clone({ nodes: [] });
    for (const child of node.nodes) {
      const filteredChild = filterNode(child, usedClasses);
      if (filteredChild) clone.append(filteredChild);
    }
    return clone.nodes.length > 0 ? clone : null;
  }

  if (node.type === 'comment') {
    return null;
  }

  return null;
}

async function buildOptimizedStylesheet() {
  const css = await fs.readFile(cssInput, 'utf8');
  const root = postcss.parse(css);
  const usedClasses = await collectUsedClasses();

  const nextRoot = postcss.root();
  for (const node of root.nodes) {
    const filteredNode = filterNode(node, usedClasses);
    if (filteredNode) nextRoot.append(filteredNode);
  }

  await fs.writeFile(cssOutput, nextRoot.toString(), 'utf8');
  console.log(`[css] Wrote ${path.relative(projectRoot, cssOutput)} with ${usedClasses.size} discovered classes`);
}

buildOptimizedStylesheet().catch((error) => {
  console.error('[css] Opt build failed:', error);
  process.exitCode = 1;
});
