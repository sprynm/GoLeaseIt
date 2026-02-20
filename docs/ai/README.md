# AI Collaboration Guide

This folder is the single source of truth for how we work with AI agents (Codex, Claude) on this repo.
Keep this short, current, and durable. Avoid repeating details that are already in other docs.

## 1) Start Here (Fast Path)
- Read `docs/ai/context.md` for the minimum viable project context.
- If the task touches architecture or design, jump to the linked docs listed there.
- For end-of-project guardrails, use `docs/ai/closeout-rules.md` as the compact philosophy/rules baseline.

## 2) Context Hygiene (Avoid Context Rot)
- Prefer short, task-scoped context. Do not load multiple long docs unless needed.
- If a doc is obsolete, delete it or replace it with an updated durable version.
- When changing behavior, update the one most relevant doc, not several.
- Keep instructions declarative and stable; avoid ad hoc narrative logs.

## 3) Token Budget Discipline (Avoid Burn)
- Use the smallest doc set that answers the current task.
- Avoid inlining large assets or full file dumps in agent prompts.
- Summarize long sections with bullet points and link the source doc.
- If a prompt needs examples, include only the minimal snippet.

## 4) Cadence (Execution Rhythm)
- For multi-file edits, confirm scope and order of operations.
- Make edits in small batches and re-check for drift before continuing.
- After a change, update the relevant doc immediately or capture a decision.

## 5) Learning Loops (Make Progress Durable)
- When a pattern repeats twice, codify it as a rule or a checklist entry.
- When a decision narrows future choices, log it with date + reason + consequences.
- Favor clear ownership: a rule lives in one place, referenced elsewhere.

## 6) Durability Rules
- If a detail might change soon (temporary fixes, experiments), do not put it in core context.
- Date-stamp unstable guidance.
- Prefer "how" and "why" in durable docs.

## 7) Document Map
- `docs/ai/context.md`: concise project context and links.
- `docs/ai/closeout-rules.md`: compact learnings, philosophy, and final working rules.
- `docs/design/atomic-reuse.md`: reuse utilities + atom-sized blocks before new components.
- `docs/architecture/agent-first.md`: strict workflow when agents make most changes.
- `docs/architecture/optional-components.md`: CSS size control via optional `@use` gates.
- `docs/architecture/admin-javascript.md`: frontend JS loading policy and migration backlog.
- `docs/architecture/script-loading-map.md`: where scripts are loaded by layout.
- `docs/architecture/prototype-catalog.md`: core prototypes and migration status.
- `docs/architecture/new-site-playbook.md`: platform-level checklist for launching a new site.
- `docs/architecture/`: system structure + layout architecture.
- `docs/design/`: tokens, typography, layout guidelines, hero.
- `docs/quality/`: lint rules and checklists.
- `docs/prompts/`: reusable prompts/specs (navigation, etc.).
