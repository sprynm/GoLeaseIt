import fs from "fs/promises";
import path from "path";
import { fileURLToPath } from "url";

const projectRoot = fileURLToPath(new URL("..", import.meta.url));

const sourceRoots = [
  path.join(projectRoot, "View"),
  path.join(projectRoot, "Plugin"),
  path.join(projectRoot, "webroot", "style-guide"),
  path.join(projectRoot, "webroot", "css", "scss")
];

const sourceExt = new Set([".ctp", ".php", ".html", ".js", ".scss"]);

const blockedChecks = [
  {
    id: "legacy-button-classes",
    message: "Legacy button classes detected. Use `.btn--primary` / `.btn--secondary`.",
    pattern: /\bbtn-primary\b|\bbtn-secondary\b/g,
    include: [/\.(ctp|php|html|js|scss)$/i],
    exclude: [
      /webroot[\\/]css[\\/]/i,
      /Plugin[\\/]Media[\\/]/i,
      /docs[\\/]/i
    ]
  },
  {
    id: "legacy-notification-classes",
    message: "Legacy notification state class detected. Use `.notification notification--*`.",
    pattern: /notification(?:\.|\s+)(attention|information|success|error)\b/g,
    include: [/\.(ctp|php|html|js|scss)$/i],
    exclude: [
      /webroot[\\/]css[\\/]/i,
      /Plugin[\\/]Media[\\/]/i,
      /docs[\\/]/i
    ]
  },
  {
    id: "hardcoded-open-sans",
    message: "Hardcoded `Open Sans` found in SCSS. Use `var(--font-sans-alt)`.",
    pattern: /["']Open Sans["']/g,
    include: [/\.scss$/i],
    exclude: [
      /_fonts\.scss$/i,
      /_theme\.scss$/i,
      /archive[\\/]/i
    ]
  },
  {
    id: "contextual-button-sizing",
    message: "Context-coupled button sizing selector found. Use utility class in markup.",
    pattern: /\.cta-band--article\s+\.cta-band__actions\s+\.btn\b/g,
    include: [/\.scss$/i],
    exclude: []
  },
  {
    id: "deprecated-hero-button-classes",
    message: "Deprecated button class detected. Use semantic modifier + utility (e.g. `.btn--primary u-btn-lg`).",
    pattern: /\bbtn--hero(?:-secondary)?\b|\bbtn--lg\b/g,
    include: [/\.(ctp|php|html|js|scss)$/i],
    exclude: [
      /webroot[\\/]css[\\/]/i,
      /Plugin[\\/]Media[\\/]/i,
      /docs[\\/]/i
    ]
  },
  {
    id: "unsupported-range-media-syntax",
    message: "Range media syntax detected in SCSS. Use `min-width` / `max-width` mixins for prod-safe minification.",
    pattern: /@media\s*\(\s*(?:width\s*[<>]=|(?:\d+(?:\.\d+)?(?:px|rem|em))\s*<=\s*width|width\s*<=)/g,
    include: [/\.scss$/i],
    exclude: [/archive[\\/]/i]
  }
];

const clampBudget = {
  max: 70,
  include: [/webroot[\\/]css[\\/]scss[\\/].+\.scss$/i],
  exclude: [/archive[\\/]/i, /_theme\.scss$/i, /_block-style-guide\.scss$/i]
};

const sizeBudget = {
  file: path.join(projectRoot, "webroot", "css", "stylesheet.css"),
  maxBytes: 80000
};

const issues = [];

function toRel(filePath) {
  return path.relative(projectRoot, filePath).replace(/\\/g, "/");
}

function matchesAny(list, value) {
  return list.some((re) => re.test(value));
}

async function walk(dir) {
  const out = [];
  const entries = await fs.readdir(dir, { withFileTypes: true });

  for (const entry of entries) {
    if (entry.name.startsWith(".")) {
      continue;
    }

    const full = path.join(dir, entry.name);

    if (entry.isDirectory()) {
      out.push(...(await walk(full)));
      continue;
    }

    if (!entry.isFile()) {
      continue;
    }

    if (!sourceExt.has(path.extname(entry.name).toLowerCase())) {
      continue;
    }

    out.push(full);
  }

  return out;
}

async function getAllFiles() {
  const all = [];
  for (const root of sourceRoots) {
    try {
      const stat = await fs.stat(root);
      if (!stat.isDirectory()) {
        continue;
      }
      all.push(...(await walk(root)));
    } catch {
      // ignore missing roots
    }
  }
  return all;
}

function getLine(content, index) {
  let line = 1;
  for (let i = 0; i < index; i += 1) {
    if (content.charCodeAt(i) === 10) {
      line += 1;
    }
  }
  return line;
}

function addIssue(file, line, checkId, message, matchText) {
  issues.push({ file, line, checkId, message, matchText });
}

async function runBlockedChecks(files) {
  for (const file of files) {
    const rel = toRel(file);

    for (const check of blockedChecks) {
      if (!matchesAny(check.include, rel)) {
        continue;
      }
      if (check.exclude.length && matchesAny(check.exclude, rel)) {
        continue;
      }

      const content = await fs.readFile(file, "utf8");
      check.pattern.lastIndex = 0;
      let match;
      while ((match = check.pattern.exec(content)) !== null) {
        addIssue(rel, getLine(content, match.index), check.id, check.message, match[0]);
      }
    }
  }
}

async function runClampBudget(files) {
  let clampCount = 0;

  for (const file of files) {
    const rel = toRel(file);
    if (!matchesAny(clampBudget.include, rel)) {
      continue;
    }
    if (clampBudget.exclude.length && matchesAny(clampBudget.exclude, rel)) {
      continue;
    }

    const content = await fs.readFile(file, "utf8");
    const matches = content.match(/clamp\(/g);
    clampCount += matches ? matches.length : 0;
  }

  if (clampCount > clampBudget.max) {
    issues.push({
      file: "webroot/css/scss",
      line: 0,
      checkId: "clamp-budget",
      message: `Clamp budget exceeded (${clampCount} > ${clampBudget.max}).`,
      matchText: `clamp-count:${clampCount}`
    });
  }
}

async function runSizeBudget() {
  try {
    const stat = await fs.stat(sizeBudget.file);
    if (stat.size > sizeBudget.maxBytes) {
      issues.push({
        file: toRel(sizeBudget.file),
        line: 0,
        checkId: "stylesheet-size",
        message: `Compiled stylesheet exceeds size budget (${stat.size} > ${sizeBudget.maxBytes} bytes).`,
        matchText: `size:${stat.size}`
      });
    }
  } catch {
    issues.push({
      file: toRel(sizeBudget.file),
      line: 0,
      checkId: "stylesheet-size",
      message: "Compiled stylesheet missing. Run `npm run css:build:prod` before architecture check.",
      matchText: "missing"
    });
  }
}

async function run() {
  const files = await getAllFiles();

  await runBlockedChecks(files);
  await runClampBudget(files);
  await runSizeBudget();

  if (issues.length) {
    console.error("[css-arch] FAILED");
    for (const issue of issues) {
      const line = issue.line > 0 ? `:${issue.line}` : "";
      console.error(`- ${issue.file}${line} [${issue.checkId}] ${issue.message} (${issue.matchText})`);
    }
    process.exitCode = 1;
    return;
  }

  console.log("[css-arch] OK: CSS architecture checks passed.");
}

run().catch((error) => {
  console.error("[css-arch] Unexpected failure:", error);
  process.exitCode = 1;
});
