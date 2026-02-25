# Figma Context Cache

Purpose: reduce repeat broad Figma pulls and preserve stable node targeting between sessions.

Primary cache file:
- `docs/design/figma-context-cache/go-lease-it-slice.cache.json`

## Workflow
1. Read cache file first for page and node IDs.
2. Pull `get_design_context` only for node(s) being changed.
3. Update the cache file if IDs or bundle groupings change.

## Refresh Rule
- Full-file reseed only when structure changes significantly (new page/frame moves, renamed bundles, major redesign).
- Otherwise do narrow node updates only.

## Current Source
- File key: `lz33juiSBrbQAIR9beOjsH`
- URL: `https://www.figma.com/design/lz33juiSBrbQAIR9beOjsH/Go-Lease-It-Slice`
- Last seeded: `2026-02-25`
