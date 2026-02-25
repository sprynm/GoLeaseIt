import fs from "fs/promises";
import path from "path";
import { fileURLToPath } from "url";

const projectRoot = fileURLToPath(new URL("..", import.meta.url));
const scssDir = path.join(projectRoot, "webroot", "css", "scss");
const stylesheetPath = path.join(scssDir, "stylesheet.scss");

const issues = [];

function addIssue(file, message) {
  issues.push({ file, message });
}

async function getScssFiles() {
  const entries = await fs.readdir(scssDir, { withFileTypes: true });
  return entries
    .filter((entry) => entry.isFile() && entry.name.endsWith(".scss"))
    .map((entry) => entry.name)
    .filter((name) => !name.startsWith("archive"));
}

function getUseOrder(content) {
  const useRe = /^\s*@use\s+"([^"]+)"/gm;
  const imports = [];
  let match;
  while ((match = useRe.exec(content)) !== null) {
    imports.push(match[1]);
  }
  return imports;
}

function indexOfUse(imports, target) {
  return imports.findIndex((name) => name === target);
}

async function checkEntrypointOrder() {
  const content = await fs.readFile(stylesheetPath, "utf8");
  const imports = getUseOrder(content);

  const idxTheme = indexOfUse(imports, "theme");
  const idxUtilities = indexOfUse(imports, "utilities");
  const idxExceptions = indexOfUse(imports, "exceptions");

  if (idxTheme === -1) {
    addIssue("webroot/css/scss/stylesheet.scss", 'Missing `@use "theme"` import.');
  }

  if (idxUtilities === -1) {
    addIssue("webroot/css/scss/stylesheet.scss", 'Missing `@use "utilities"` import.');
  }

  if (idxExceptions === -1) {
    addIssue("webroot/css/scss/stylesheet.scss", 'Missing `@use "exceptions"` import.');
  }

  if (idxTheme >= 0 && idxUtilities >= 0 && idxUtilities < idxTheme) {
    addIssue("webroot/css/scss/stylesheet.scss", "Utilities must load after theme/tokens.");
  }

  if (idxExceptions >= 0 && idxUtilities >= 0 && idxExceptions < idxUtilities) {
    addIssue("webroot/css/scss/stylesheet.scss", "Exceptions must load after utilities.");
  }

  // CUBE rule: exceptions are last resort; keep them as the last import in stylesheet.
  if (idxExceptions >= 0 && idxExceptions !== imports.length - 1) {
    addIssue("webroot/css/scss/stylesheet.scss", "`exceptions` must be the last @use import.");
  }
}

async function checkLayerDiscipline() {
  const files = await getScssFiles();

  for (const fileName of files) {
    const fullPath = path.join(scssDir, fileName);
    const rel = path.join("webroot/css/scss", fileName).replace(/\\/g, "/");
    const content = await fs.readFile(fullPath, "utf8");

    const hasExceptionsLayer = /@layer\s+exceptions\b/.test(content);
    const hasBlocksLayer = /@layer\s+blocks\b/.test(content);
    const hasUtilitiesLayer = /@layer\s+utilities\b/.test(content);

    if (fileName === "_exceptions.scss") {
      if (!hasExceptionsLayer) {
        addIssue(rel, "Exceptions file must define `@layer exceptions`.");
      }
      continue;
    }

    if (hasExceptionsLayer) {
      addIssue(rel, "Only `_exceptions.scss` may define `@layer exceptions`.");
    }

    if (fileName === "_utilities.scss" && !hasUtilitiesLayer) {
      addIssue(rel, "Utilities file must define `@layer utilities`.");
    }

    if ((fileName.startsWith("_block-") || fileName.startsWith("_prototype-")) && !hasBlocksLayer) {
      addIssue(rel, "Block/prototype styles must be in `@layer blocks`.");
    }
  }
}

async function run() {
  await checkEntrypointOrder();
  await checkLayerDiscipline();

  if (issues.length) {
    console.error("[cube-check] Discipline check failed:");
    for (const issue of issues) {
      console.error(`- ${issue.file}: ${issue.message}`);
    }
    process.exitCode = 1;
    return;
  }

  console.log("[cube-check] OK: CUBE discipline checks passed.");
}

run().catch((error) => {
  console.error("[cube-check] Unexpected failure:", error);
  process.exitCode = 1;
});
