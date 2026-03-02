# Figma Context Cache

Purpose: reduce repeat broad Figma pulls and preserve stable node targeting between sessions.

Primary cache file:
- `docs/design/figma-context-cache/go-lease-it-slice.cache.json`

## Workflow
1. Read cache file first for page and node IDs.
2. Run targeted pull script for the exact node(s) being changed.
3. Use MCP `get_design_context` only after node targeting is confirmed.
4. Update cache aliases/bundles only when IDs or groupings change.

## Durable Pull Controller
Use the controller to avoid compaction-loss loops by journaling every pull to disk.

Commands:
- `npm run figma:status`
- `npm run figma:pull -- --file-key lz33juiSBrbQAIR9beOjsH --node-id 10902:470 --depth 3 --alias feature_page`
- Add `--force` only when you intentionally want a refresh.

Environment:
- `FIGMA_ACCESS_TOKEN` must be set in your shell.

Artifacts written by the controller:
- `docs/design/figma-context-cache/raw/*.raw.json`
- `docs/design/figma-context-cache/summaries/*.summary.json`
- `docs/design/figma-context-cache/pull-journal.jsonl`
- `docs/design/figma-context-cache/go-lease-it-slice.cache.json` (`last_pull` + optional alias updates)

Behavior:
- Cache-first: if a successful summary already exists for the node, the script returns a cache hit and skips network pull (unless `--force`).
- Durable journaling: writes `pull_pending`, then `pull_success`/`pull_failed` records.
- Narrow scope only: node-level requests with explicit `ids` and `depth`.

## Refresh Rule
- Full-file reseed only when structure changes significantly (new page/frame moves, renamed bundles, major redesign).
- Otherwise do narrow node updates only.

## Current Source
- File key: `lz33juiSBrbQAIR9beOjsH`
- URL: `https://www.figma.com/design/lz33juiSBrbQAIR9beOjsH/Go-Lease-It-Slice`
- Last seeded: `2026-02-25`
