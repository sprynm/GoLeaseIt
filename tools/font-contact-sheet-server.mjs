import http from "node:http";
import { execFile } from "node:child_process";

const PORT = 8787;
const CORS_HEADERS = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "GET,OPTIONS",
  "Access-Control-Allow-Headers": "Content-Type",
  "Access-Control-Allow-Private-Network": "true"
};

const stripTags = (html) => {
  return html
    .replace(/<script[\s\S]*?<\/script>/gi, " ")
    .replace(/<style[\s\S]*?<\/style>/gi, " ")
    .replace(/<[^>]+>/g, " ")
    .replace(/\s+/g, " ")
    .trim();
};

const extractLinks = (html, origin) => {
  const hrefs = [];
  const regex = /href\s*=\s*["']([^"']+)["']/gi;
  let match;
  while ((match = regex.exec(html)) !== null) {
    const href = match[1];
    if (!href || href.startsWith("#")) {
      continue;
    }
    try {
      const absolute = new URL(href, origin).href;
      if (absolute.startsWith(origin)) {
        hrefs.push(absolute);
      }
    } catch (error) {
      continue;
    }
  }
  return hrefs;
};

const fetchHtml = (url) =>
  new Promise((resolve, reject) => {
    execFile("curl", ["-L", "-s", url], { maxBuffer: 5 * 1024 * 1024 }, (err, stdout) => {
      if (err) {
        reject(err);
        return;
      }
      resolve(stdout.toString());
    });
  });

const crawl = async (startUrl, maxPages) => {
  const queue = [startUrl];
  const visited = new Set();
  let text = "";
  const origin = new URL(startUrl).origin;

  while (queue.length && visited.size < maxPages) {
    const current = queue.shift();
    if (!current || visited.has(current)) {
      continue;
    }
    visited.add(current);

    const html = await fetchHtml(current);
    text += ` ${stripTags(html)}`;

    const links = extractLinks(html, origin);
    for (const link of links) {
      if (!visited.has(link) && queue.length + visited.size < maxPages) {
        queue.push(link);
      }
    }
  }

  return { text, pages: visited.size };
};

const server = http.createServer(async (req, res) => {
  const url = new URL(req.url, `http://${req.headers.host}`);

  if (req.method === "OPTIONS") {
    res.writeHead(204, CORS_HEADERS);
    res.end();
    return;
  }

  if (url.pathname !== "/crawl") {
    res.writeHead(404, { ...CORS_HEADERS, "Content-Type": "text/plain" });
    res.end("Not Found");
    return;
  }

  const target = url.searchParams.get("url");
  const pages = Math.min(10, Math.max(1, Number(url.searchParams.get("pages") || 1)));

  res.setHeader("Access-Control-Allow-Origin", CORS_HEADERS["Access-Control-Allow-Origin"]);
  res.setHeader(
    "Access-Control-Allow-Private-Network",
    CORS_HEADERS["Access-Control-Allow-Private-Network"]
  );
  res.setHeader("Content-Type", "application/json");

  if (!target) {
    res.writeHead(400);
    res.end(JSON.stringify({ error: "Missing url parameter" }));
    return;
  }

  try {
    const result = await crawl(target, pages);
    res.writeHead(200);
    res.end(JSON.stringify(result));
  } catch (error) {
    res.writeHead(500);
    res.end(JSON.stringify({ error: "Crawl failed" }));
  }
});

server.listen(PORT, () => {
  // eslint-disable-next-line no-console
  console.log(`Font contact sheet server running on http://localhost:${PORT}`);
});
