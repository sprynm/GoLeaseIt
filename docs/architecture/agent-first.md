# Agent-First Workflow (90% AI Updates)

These rules assume most changes are made by AI agents. Keep them strict and short.

## Single Source of Truth
- Every rule lives in one file only.
- If you touch behavior, update the one doc that owns it.

## Change Order (Required)
1. Identify the single source file(s).
2. Make the smallest viable change.
3. Update the owning doc or decision log.
4. Stop. Don’t “also tweak” adjacent files.

## Optional Components (Size Control)
- Optional components must be gated by a commented `@use` in `webroot/css/scss/stylesheet.scss`.
- Do not auto-enable optional components unless asked.
- If you add a new optional component, add a one-line note in `docs/architecture/optional-components.md`.

## CSS Size Strategy
- Prefer shared blocks/atoms over one-off components.
- When a component is page-specific, isolate it and keep it optional.
- Tree shaking is acceptable if it’s deterministic and documented.

## Safety
- Avoid broad refactors without explicit request.
- If behavior is unclear, ask before touching multiple files.
