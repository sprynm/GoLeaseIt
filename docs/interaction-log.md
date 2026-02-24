# Interaction Log

Concise summaries of assistant interactions to preserve context across compaction.

## 2026-02-20
- CSS bloat review: `stylesheet-new.css` is ~2x size vs `stylesheet.css` due to more selectors and page-specific `.page*` rules; duplication is minimal.
- Hero simplification requested: replaced Poland/Truck-specific hero styles with generic banner in SCSS (`webroot/css/scss/_block-hero.scss`). Set max height 350px at largest breakpoint; simplified overlay and typography; kept image background mechanism. Hero diagonal rule removed only from `webroot/css/stylesheet-new.css` (per user request).
- Footer update requested: SCSS (`webroot/css/scss/_block-footer.scss`) simplified to a single-row layout, removed menu columns, retained contact + social, added thin divider and centered copyright.
- Direct CSS edits were made earlier to `webroot/css/stylesheet-opt.css` and `webroot/css/stylesheet-new.css` to mirror SCSS, but user later instructed: “do not update any CSS files directly.”
- AGENTS/docs review: no legacy-site references found in `docs/AGENTS.md`, `docs/client-information.md`, `docs/ai/context.md`. No decision/history/log files present; only mentions of logging practices. Legacy Poland logo assets found under `docs/customer images/logo/`.
