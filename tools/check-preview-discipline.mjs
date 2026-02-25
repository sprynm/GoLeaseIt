import fs from "fs/promises";
import path from "path";
import { fileURLToPath } from "url";

const projectRoot = fileURLToPath(new URL("..", import.meta.url));
const styleGuideDir = path.join(projectRoot, "webroot", "style-guide");
const previewPrefix = "preview-";

const readText = async (filePath) => fs.readFile(filePath, "utf8");

const issues = [];

function addIssue(file, message) {
  issues.push({ file, message });
}

function hasPattern(content, pattern) {
  return pattern.test(content);
}

async function checkScssEntrypoints() {
  const styleGuideScssPath = path.join(projectRoot, "webroot", "css", "scss", "style-guide.scss");
  const mockScssPath = path.join(projectRoot, "webroot", "css", "scss", "stylesheet-mock.scss");

  const styleGuideScss = await readText(styleGuideScssPath);
  const mockScss = await readText(mockScssPath);

  if (!/^\s*@use\s+"stylesheet";/m.test(styleGuideScss)) {
    addIssue("webroot/css/scss/style-guide.scss", 'Style guide must import `@use "stylesheet";` to inherit tokens/components.');
  }

  if (!/^\s*@use\s+"theme"\s+as\s+\*;/m.test(mockScss)) {
    addIssue("webroot/css/scss/stylesheet-mock.scss", "Mock stylesheet must import theme tokens before preview overrides.");
  }
}

async function checkPreviewFiles() {
  const dirEntries = await fs.readdir(styleGuideDir, { withFileTypes: true });
  const previewFiles = dirEntries
    .filter((entry) => entry.isFile() && entry.name.startsWith(previewPrefix) && entry.name.endsWith(".html"))
    .map((entry) => entry.name)
    .sort();

  if (previewFiles.length === 0) {
    addIssue("webroot/style-guide", "No preview-*.html files found.");
    return;
  }

  for (const fileName of previewFiles) {
    const fullPath = path.join(styleGuideDir, fileName);
    const rel = path.join("webroot/style-guide", fileName).replace(/\\/g, "/");
    const content = await readText(fullPath);

    const hasHeaderInclude = hasPattern(content, /data-include=["'][^"']*partials\/preview-header\.html["']/i);
    const hasFooterInclude = hasPattern(content, /data-include=["'][^"']*partials\/preview-footer\.html["']/i);
    const hasLoaderScript = hasPattern(content, /<script[^>]+src=["']\.\/assets\/preview-includes\.js["'][^>]*><\/script>/i);
    const hasInlineHeader = hasPattern(content, /<header[^>]+class=["'][^"']*site-header/i);
    const hasInlineFooter = hasPattern(content, /<footer\b/i);
    const hasInlineStyle = hasPattern(content, /\sstyle=["'][^"']*["']/i);
    const expectsNavJs = hasPattern(content, /\sdata-load-nav-js(?:\s|>)/i);

    if (hasInlineHeader) {
      addIssue(rel, "Inline header markup found. Use shared include `./partials/preview-header.html`.");
    }

    if (hasInlineFooter) {
      addIssue(rel, "Inline footer markup found. Use shared include `./partials/preview-footer.html`.");
    }

    if (!hasLoaderScript && (hasHeaderInclude || hasFooterInclude || expectsNavJs)) {
      addIssue(rel, "Missing `./assets/preview-includes.js` loader script.");
    }

    if (fileName.includes("home") && !hasFooterInclude) {
      addIssue(rel, "Home/full preview should include shared footer partial.");
    }

    if (expectsNavJs && !hasHeaderInclude) {
      addIssue(rel, "Pages opting into nav JS should include shared header partial.");
    }

    if (hasInlineStyle) {
      addIssue(rel, "Inline `style=\"...\"` found; move preview styling into SCSS.");
    }

  }
}

async function run() {
  await checkScssEntrypoints();
  await checkPreviewFiles();

  if (issues.length > 0) {
    console.error("[preview-check] Discipline check failed:");
    for (const issue of issues) {
      console.error(`- ${issue.file}: ${issue.message}`);
    }
    process.exitCode = 1;
    return;
  }

  console.log("[preview-check] OK: preview discipline checks passed.");
}

run().catch((error) => {
  console.error("[preview-check] Unexpected failure:", error);
  process.exitCode = 1;
});
