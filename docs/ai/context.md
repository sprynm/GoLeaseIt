# Project Context (Concise)

Use this file as the minimum viable context. It is intentionally short.

## Stack Summary
- CMS: Radarhill "pyramid" CMS.
- Framework: CakePHP (legacy MVC).
- Templates: `.ctp` under `View/Layouts` and `View/Elements`.
- Assets: SCSS in `webroot/css/scss`, compiled to `webroot/css/stylesheet.css`.
- JavaScript: `webroot/js`.
- Runtime PHP is believed to be 5.x (minor unknown).

## Key Layout Entry Points
- Layouts: `View/Layouts/` (`default.ctp`, `home.ctp`, `contact.ctp`, `offline.ctp`).
- Partials: `View/Elements/layout/` (`head.ctp`, `nav.ctp`, `body_masthead.ctp`, `footer.ctp`).
- Main wrapper / skip-link target: `#content.site-wrapper`.

## Design System / SCSS
- Entry point: `webroot/css/scss/stylesheet.scss`.
- System partials: `webroot/css/scss/_*.scss` (layered by purpose).
- Theme: `webroot/css/scss/_theme.scss` (SCSS-first primitives + emitted tokens).
- Prototype styles must live in `webroot/css/scss/_prototype-<slug>.scss`.
- Do not edit `webroot/css/stylesheet.css` directly.

## Atomic Reuse Priority (CUBE-Aligned)
- Reuse utilities and atom-sized blocks before creating new context-specific components.
- Extend existing atoms (e.g., `.btn`) via modifiers or scoped wrappers.
- Accept reduced prototype-level control if it improves cohesion.
- See `docs/design/atomic-reuse.md`.

## Build & Lint
- SCSS build: `npm run css:build`.
- PHP lint: `php -l <file>`.
- Alt-syntax heuristic: `node tools/check-ctp-balance.cjs`.

## Runtime / Deployment Risk
- A template parse error can trigger the CMS fallback/maintenance page.
- Keep CTP logic in a single PHP block per section when possible.
- This repo does not include the full runtime stack; significant Pyramid/Cake files are external/shared.
- Treat local `php` execution/lint outcomes as partial signals, not full-environment guarantees.

## Where To Go Next
- System overview: `docs/architecture/system-overview.md`.
- Atomic reuse policy: `docs/design/atomic-reuse.md`.
- Frontend JavaScript (admin-facing): `docs/architecture/admin-javascript.md`.
- Script loading matrix: `docs/architecture/script-loading-map.md`.
- Prototype coverage and migration status: `docs/architecture/prototype-catalog.md`.
- New site implementation baseline: `docs/architecture/new-site-playbook.md`.
- Agent-first rules: `docs/architecture/agent-first.md`.
- Optional components: `docs/architecture/optional-components.md`.
- Style system rules: `docs/design/style-system.md`.
- Theme system: `docs/design/theme.md`.
- Token inventory: `docs/design/tokens.md`.
- Exceptions policy: `docs/design/exceptions.md`.
- Prototype styling rules: `docs/design/prototypes.md`.
- Accessibility checklist: `docs/design/accessibility.md`.
- Layout architecture: `docs/architecture/layout-system.md`.
- Frontend structure: `docs/architecture/frontend-structure.md`.
- Design system details: `docs/design/`.
- Linting rules: `docs/quality/lint.md`.
