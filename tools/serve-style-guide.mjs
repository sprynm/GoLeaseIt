import http from "node:http";
import { promises as fs } from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const repoRoot = path.resolve(__dirname, "..");
const webrootPath = path.resolve(repoRoot, "webroot");

const argPortIndex = process.argv.indexOf("--port");
const argPort =
  argPortIndex > -1 && process.argv[argPortIndex + 1]
    ? Number(process.argv[argPortIndex + 1])
    : null;
const envPort = process.env.PORT ? Number(process.env.PORT) : null;
const requestedPort = argPort || envPort || 4173;
const allowPortFallback = !argPort && !envPort;
const host = "127.0.0.1";

const CONTENT_TYPES = {
  ".css": "text/css; charset=utf-8",
  ".gif": "image/gif",
  ".html": "text/html; charset=utf-8",
  ".ico": "image/x-icon",
  ".jpg": "image/jpeg",
  ".jpeg": "image/jpeg",
  ".js": "application/javascript; charset=utf-8",
  ".json": "application/json; charset=utf-8",
  ".png": "image/png",
  ".svg": "image/svg+xml; charset=utf-8",
  ".txt": "text/plain; charset=utf-8",
  ".webp": "image/webp",
  ".woff": "font/woff",
  ".woff2": "font/woff2",
};

function getContentType(filePath) {
  const ext = path.extname(filePath).toLowerCase();
  return CONTENT_TYPES[ext] || "application/octet-stream";
}

function resolveRequestPath(urlPathname) {
  let pathname = urlPathname || "/";

  if (pathname === "/") {
    pathname = "/style-guide/index.html";
  }

  const decoded = decodeURIComponent(pathname);
  const trimmed = decoded.replace(/^\/+/, "");
  const absolute = path.resolve(webrootPath, trimmed);

  if (!absolute.startsWith(webrootPath)) {
    return null;
  }

  return absolute;
}

async function getFilePath(resolvedPath) {
  const stat = await fs.stat(resolvedPath);
  if (stat.isDirectory()) {
    return path.join(resolvedPath, "index.html");
  }
  return resolvedPath;
}

async function serveFile(filePath, res) {
  const contents = await fs.readFile(filePath);
  res.writeHead(200, {
    "Cache-Control": "no-cache",
    "Content-Type": getContentType(filePath),
  });
  res.end(contents);
}

function createServer(port) {
  return http.createServer(async (req, res) => {
    try {
      const url = new URL(req.url || "/", `http://${host}:${port}`);
      const resolvedPath = resolveRequestPath(url.pathname);

      if (!resolvedPath) {
        res.writeHead(403, { "Content-Type": "text/plain; charset=utf-8" });
        res.end("Forbidden");
        return;
      }

      const filePath = await getFilePath(resolvedPath);
      await serveFile(filePath, res);
    } catch (error) {
      const statusCode = error && error.code === "ENOENT" ? 404 : 500;
      const message = statusCode === 404 ? "Not Found" : "Internal Server Error";
      res.writeHead(statusCode, { "Content-Type": "text/plain; charset=utf-8" });
      res.end(message);
    }
  });
}

function listen(server, port) {
  return new Promise((resolve, reject) => {
    server.once("error", reject);
    server.listen(port, host, () => {
      server.removeListener("error", reject);
      resolve();
    });
  });
}

async function startServer() {
  let port = requestedPort;
  const maxAttempts = allowPortFallback ? 25 : 1;

  for (let attempt = 0; attempt < maxAttempts; attempt += 1) {
    const server = createServer(port);

    try {
      await listen(server, port);
      // eslint-disable-next-line no-console
      console.log(`Style guide server running at http://${host}:${port}/style-guide/index.html`);
      return;
    } catch (error) {
      if (error && error.code === "EADDRINUSE" && allowPortFallback && attempt < maxAttempts - 1) {
        port += 1;
        continue;
      }
      throw error;
    }
  }
}

startServer().catch((error) => {
  if (error && error.code === "EADDRINUSE") {
    // eslint-disable-next-line no-console
    console.error(
      `Port ${requestedPort} is already in use. Run with a different port: npm run SG:serve -- --port 4174`
    );
    process.exitCode = 1;
    return;
  }
  // eslint-disable-next-line no-console
  console.error(error);
  process.exitCode = 1;
});
