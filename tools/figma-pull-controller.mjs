#!/usr/bin/env node
import fs from "node:fs";
import path from "node:path";

const DEFAULT_CACHE_FILE = "docs/design/figma-context-cache/go-lease-it-slice.cache.json";
const DEFAULT_BASE_DIR = "docs/design/figma-context-cache";
const DEFAULT_DEPTH = 3;

function nowIso() {
  return new Date().toISOString();
}

function safeNodeId(nodeId) {
  return String(nodeId).replace(/[:/\\]/g, "-");
}

function ensureDir(dirPath) {
  fs.mkdirSync(dirPath, { recursive: true });
}

function readJson(filePath, fallback = null) {
  if (!fs.existsSync(filePath)) return fallback;
  return JSON.parse(fs.readFileSync(filePath, "utf8"));
}

function writeJson(filePath, data) {
  ensureDir(path.dirname(filePath));
  fs.writeFileSync(filePath, JSON.stringify(data, null, 2) + "\n", "utf8");
}

function appendJsonl(filePath, record) {
  ensureDir(path.dirname(filePath));
  fs.appendFileSync(filePath, JSON.stringify(record) + "\n", "utf8");
}

function parseArgs(argv) {
  const [, , command, ...rest] = argv;
  const args = { command, _: [] };
  for (let i = 0; i < rest.length; i += 1) {
    const token = rest[i];
    if (!token.startsWith("--")) {
      args._.push(token);
      continue;
    }
    const [flag, inlineValue] = token.split("=", 2);
    const key = flag.slice(2);
    if (inlineValue !== undefined) {
      args[key] = inlineValue;
      continue;
    }
    const next = rest[i + 1];
    if (!next || next.startsWith("--")) {
      args[key] = true;
      continue;
    }
    args[key] = next;
    i += 1;
  }
  return args;
}

function printHelp() {
  process.stdout.write(
    [
      "Figma Pull Controller",
      "",
      "Commands:",
      "  pull-node --file-key <key> --node-id <id> [--depth 3] [--cache <path>] [--alias <name>] [--force]",
      "  status [--cache <path>]",
      "",
      "Environment:",
      "  FIGMA_ACCESS_TOKEN (required for pull-node)",
      "",
      "Notes:",
      "  - Writes durable pull journal and local artifacts before/after network calls.",
      "  - Cache-first: skips repeated pulls when a successful artifact already exists (unless --force).",
      ""
    ].join("\n")
  );
}

function getBasePaths(cachePathInput) {
  const cachePath = path.resolve(cachePathInput || DEFAULT_CACHE_FILE);
  const baseDir = path.resolve(path.dirname(cachePath) || DEFAULT_BASE_DIR);
  return {
    cachePath,
    baseDir,
    rawDir: path.join(baseDir, "raw"),
    summaryDir: path.join(baseDir, "summaries"),
    journalPath: path.join(baseDir, "pull-journal.jsonl")
  };
}

function findNodeInCache(cache, nodeId) {
  if (!cache) return null;
  const page = Array.isArray(cache.pages) ? cache.pages.find((p) => p.id === nodeId) : null;
  if (page) return { type: "page", data: page };
  if (cache.quick_lookup && typeof cache.quick_lookup === "object") {
    const quickKey = Object.keys(cache.quick_lookup).find((k) => cache.quick_lookup[k] === nodeId);
    if (quickKey) return { type: "quick_lookup", key: quickKey };
  }
  return null;
}

function getLatestSuccessArtifact(baseDir, nodeId) {
  const summaryFile = path.join(baseDir, "summaries", `${safeNodeId(nodeId)}.summary.json`);
  if (!fs.existsSync(summaryFile)) return null;
  const summary = readJson(summaryFile, null);
  if (!summary || !summary.last_success) return null;
  const rawFile = path.join(baseDir, "raw", summary.last_success.raw_file);
  if (!fs.existsSync(rawFile)) return null;
  return summary;
}

function upsertQuickLookup(cache, alias, nodeId) {
  if (!alias) return cache;
  if (!cache.quick_lookup || typeof cache.quick_lookup !== "object") {
    cache.quick_lookup = {};
  }
  cache.quick_lookup[alias] = nodeId;
  return cache;
}

function updateLastPull(cache, patch) {
  cache.last_pull = {
    ...(cache.last_pull || {}),
    ...patch
  };
  return cache;
}

async function fetchNodeContext({ fileKey, nodeId, depth, token }) {
  const url = new URL(`https://api.figma.com/v1/files/${encodeURIComponent(fileKey)}/nodes`);
  url.searchParams.set("ids", nodeId);
  url.searchParams.set("depth", String(depth));

  const response = await fetch(url, {
    headers: {
      "X-Figma-Token": token
    }
  });

  const bodyText = await response.text();
  let body = null;
  try {
    body = JSON.parse(bodyText);
  } catch {
    body = { parse_error: true, raw: bodyText };
  }

  if (!response.ok) {
    const error = new Error(`Figma API error ${response.status}`);
    error.status = response.status;
    error.body = body;
    throw error;
  }
  return body;
}

function summarizeNodePayload(payload, nodeId) {
  const node = payload?.nodes?.[nodeId]?.document || null;
  const page = payload?.name || null;
  const childCount = Array.isArray(node?.children) ? node.children.length : 0;
  return {
    node_id: nodeId,
    node_name: node?.name || null,
    node_type: node?.type || null,
    parent_file_name: page,
    width: node?.absoluteBoundingBox?.width ?? null,
    height: node?.absoluteBoundingBox?.height ?? null,
    child_count: childCount
  };
}

async function pullNode(args) {
  const fileKey = args["file-key"] || args.fileKey;
  const nodeId = args["node-id"] || args.nodeId;
  const depth = Number(args.depth || DEFAULT_DEPTH);
  const alias = args.alias || null;
  const force = Boolean(args.force);

  if (!fileKey || !nodeId) {
    throw new Error("Missing required --file-key and --node-id");
  }
  if (!Number.isFinite(depth) || depth < 1 || depth > 8) {
    throw new Error("depth must be a number between 1 and 8");
  }
  const token = process.env.FIGMA_ACCESS_TOKEN;
  if (!token) {
    throw new Error("Missing FIGMA_ACCESS_TOKEN");
  }

  const paths = getBasePaths(args.cache);
  const cache = readJson(paths.cachePath, {});
  ensureDir(paths.rawDir);
  ensureDir(paths.summaryDir);

  const existing = getLatestSuccessArtifact(paths.baseDir, nodeId);
  if (existing && !force) {
    const short = {
      status: "cache_hit",
      node_id: nodeId,
      last_success: existing.last_success
    };
    process.stdout.write(JSON.stringify(short, null, 2) + "\n");
    return;
  }

  const startedAt = nowIso();
  const pullId = `${startedAt.replace(/[:.]/g, "-")}-${safeNodeId(nodeId)}`;
  appendJsonl(paths.journalPath, {
    at: startedAt,
    event: "pull_pending",
    pull_id: pullId,
    file_key: fileKey,
    node_id: nodeId,
    depth
  });

  try {
    const payload = await fetchNodeContext({ fileKey, nodeId, depth, token });
    const finishedAt = nowIso();
    const rawFileName = `${pullId}.raw.json`;
    const rawPath = path.join(paths.rawDir, rawFileName);
    writeJson(rawPath, payload);

    const summary = summarizeNodePayload(payload, nodeId);
    const summaryPath = path.join(paths.summaryDir, `${safeNodeId(nodeId)}.summary.json`);
    const existingSummary = readJson(summaryPath, { pulls: [] });
    existingSummary.node_id = nodeId;
    existingSummary.last_success = {
      at: finishedAt,
      pull_id: pullId,
      file_key: fileKey,
      depth,
      raw_file: rawFileName,
      summary
    };
    existingSummary.pulls = [
      {
        at: finishedAt,
        pull_id: pullId,
        depth,
        raw_file: rawFileName
      },
      ...(Array.isArray(existingSummary.pulls) ? existingSummary.pulls : [])
    ].slice(0, 20);
    writeJson(summaryPath, existingSummary);

    upsertQuickLookup(cache, alias, nodeId);
    updateLastPull(cache, {
      at: finishedAt,
      node_id: nodeId,
      file_key: fileKey,
      depth,
      summary_file: path.relative(process.cwd(), summaryPath).replace(/\\/g, "/"),
      cache_match: findNodeInCache(cache, nodeId)
    });
    writeJson(paths.cachePath, cache);

    appendJsonl(paths.journalPath, {
      at: finishedAt,
      event: "pull_success",
      pull_id: pullId,
      file_key: fileKey,
      node_id: nodeId,
      depth,
      raw_file: rawFileName
    });

    process.stdout.write(
      JSON.stringify(
        {
          status: "ok",
          pull_id: pullId,
          node_id: nodeId,
          summary_file: path.relative(process.cwd(), summaryPath).replace(/\\/g, "/")
        },
        null,
        2
      ) + "\n"
    );
  } catch (error) {
    const failedAt = nowIso();
    appendJsonl(paths.journalPath, {
      at: failedAt,
      event: "pull_failed",
      pull_id: pullId,
      file_key: fileKey,
      node_id: nodeId,
      depth,
      error: {
        message: error.message,
        status: error.status || null,
        body: error.body || null
      }
    });
    throw error;
  }
}

function status(args) {
  const paths = getBasePaths(args.cache);
  const cache = readJson(paths.cachePath, {});
  const journalExists = fs.existsSync(paths.journalPath);
  const journalTail = journalExists
    ? fs
        .readFileSync(paths.journalPath, "utf8")
        .trim()
        .split("\n")
        .filter(Boolean)
        .slice(-8)
        .map((line) => JSON.parse(line))
    : [];

  process.stdout.write(
    JSON.stringify(
      {
        cache_file: path.relative(process.cwd(), paths.cachePath).replace(/\\/g, "/"),
        source: cache.source || null,
        last_pull: cache.last_pull || null,
        quick_lookup_count: cache.quick_lookup ? Object.keys(cache.quick_lookup).length : 0,
        journal_tail: journalTail
      },
      null,
      2
    ) + "\n"
  );
}

async function main() {
  const args = parseArgs(process.argv);
  if (!args.command || args.help || args.command === "--help" || args.command === "-h") {
    printHelp();
    process.exit(0);
  }

  if (args.command === "pull-node") {
    await pullNode(args);
    return;
  }
  if (args.command === "status") {
    status(args);
    return;
  }

  throw new Error(`Unknown command: ${args.command}`);
}

main().catch((error) => {
  process.stderr.write(`${error.message}\n`);
  process.exit(1);
});
