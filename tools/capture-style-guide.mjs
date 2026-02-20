import http from "http";
import fsSync from "fs";
import fs from "fs/promises";
import path from "path";
import puppeteer from "puppeteer";

const DEFAULT_OUTPUT = path.resolve("docs", "visual-regression", "current");
const DEFAULT_WAIT_MS = 250;
const WEBROOT_DIR = path.resolve("webroot");

const SECTION_IDS = [
  "sg-reset-base",
  "sg-typography",
  "sg-layout-page",
  "sg-css-components",
  "sg-active-prototypes",
  "sg-cms-components"
];

const VIEWPORTS = [
  { name: "mobile", width: 390, height: 844 },
  { name: "tablet", width: 834, height: 1194 },
  { name: "desktop", width: 1440, height: 2200 }
];

function parseArgs(argv) {
  const args = {};
  for (const arg of argv) {
    if (!arg.startsWith("--")) {
      continue;
    }
    const [key, ...rest] = arg.slice(2).split("=");
    args[key] = rest.join("=") || "true";
  }
  return args;
}

async function ensureDir(dir) {
  await fs.mkdir(dir, { recursive: true });
}

async function wait(ms) {
  await new Promise((resolve) => setTimeout(resolve, ms));
}

function getContentType(filePath) {
  const ext = path.extname(filePath).toLowerCase();
  switch (ext) {
    case ".html":
      return "text/html; charset=utf-8";
    case ".css":
      return "text/css; charset=utf-8";
    case ".js":
      return "application/javascript; charset=utf-8";
    case ".json":
      return "application/json; charset=utf-8";
    case ".svg":
      return "image/svg+xml";
    case ".png":
      return "image/png";
    case ".jpg":
    case ".jpeg":
      return "image/jpeg";
    case ".webp":
      return "image/webp";
    case ".woff2":
      return "font/woff2";
    default:
      return "application/octet-stream";
  }
}

async function startStaticServer(rootDir) {
  const server = http.createServer(async (request, response) => {
    try {
      const requestUrl = new URL(request.url || "/", "http://127.0.0.1");
      const pathname = decodeURIComponent(requestUrl.pathname);
      let requestedPath = pathname;

      if (requestedPath.endsWith("/")) {
        requestedPath += "index.html";
      }

      const filePath = path.resolve(rootDir, `.${requestedPath}`);
      const relative = path.relative(rootDir, filePath);
      if (relative.startsWith("..") || path.isAbsolute(relative)) {
        response.writeHead(403, { "Content-Type": "text/plain; charset=utf-8" });
        response.end("Forbidden");
        return;
      }

      const stat = await fs.stat(filePath);
      if (!stat.isFile()) {
        response.writeHead(404, { "Content-Type": "text/plain; charset=utf-8" });
        response.end("Not Found");
        return;
      }

      response.writeHead(200, { "Content-Type": getContentType(filePath) });
      fsSync.createReadStream(filePath).pipe(response);
    } catch (_error) {
      response.writeHead(404, { "Content-Type": "text/plain; charset=utf-8" });
      response.end("Not Found");
    }
  });

  await new Promise((resolve, reject) => {
    server.once("error", reject);
    server.listen(0, "127.0.0.1", resolve);
  });

  const address = server.address();
  const port = typeof address === "object" && address ? address.port : 0;
  return {
    server,
    url: `http://127.0.0.1:${port}/style-guide/`
  };
}

const args = parseArgs(process.argv.slice(2));
const outputDir = path.resolve(args.output || DEFAULT_OUTPUT);
const waitMs = Number(args.waitMs || DEFAULT_WAIT_MS);
const useExternalUrl = Boolean(args.url);
let targetUrl = args.url || "";
let localServer = null;

if (!useExternalUrl) {
  localServer = await startStaticServer(WEBROOT_DIR);
  targetUrl = localServer.url;
}

await ensureDir(outputDir);

const browser = await puppeteer.launch({
  headless: "new"
});

const page = await browser.newPage();
const captures = [];

for (const viewport of VIEWPORTS) {
  await page.setViewport({ width: viewport.width, height: viewport.height });
  await page.goto(targetUrl, { waitUntil: "networkidle0" });

  await page.evaluate(async () => {
    if (document.fonts && document.fonts.ready) {
      await document.fonts.ready;
    }

    document.documentElement.classList.remove("js-observers");
    window.scrollTo(0, 0);
  });

  await page.addStyleTag({
    content: `
      *, *::before, *::after {
        animation: none !important;
        transition: none !important;
        caret-color: transparent !important;
      }
    `
  });

  await wait(waitMs);

  const fullPath = path.join(outputDir, `${viewport.name}-full.png`);
  await page.screenshot({ path: fullPath, fullPage: true });
  captures.push(path.basename(fullPath));

  for (const sectionId of SECTION_IDS) {
    const section = await page.$(`#${sectionId}`);
    if (!section) {
      continue;
    }

    const sectionPath = path.join(outputDir, `${viewport.name}-${sectionId}.png`);
    await section.screenshot({ path: sectionPath });
    captures.push(path.basename(sectionPath));
  }
}

await browser.close();
if (localServer) {
  await new Promise((resolve, reject) => {
    localServer.server.close((error) => {
      if (error) {
        reject(error);
        return;
      }
      resolve();
    });
  });
}

const manifest = {
  generatedAt: new Date().toISOString(),
  url: targetUrl,
  viewports: VIEWPORTS,
  sections: SECTION_IDS,
  files: captures.sort()
};

await fs.writeFile(
  path.join(outputDir, "manifest.json"),
  JSON.stringify(manifest, null, 2),
  "utf8"
);

console.log(`[visual] Captured ${captures.length} screenshots to ${outputDir}`);
