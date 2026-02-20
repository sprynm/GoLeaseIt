# System Overview

## Platform
- CMS: Radarhill "pyramid" CMS.
- Framework: CakePHP (legacy MVC).
- Templates: `.ctp` under `View/Layouts` and `View/Elements`.
- Assets: SCSS in `webroot/css/scss`, compiled to `webroot/css/stylesheet.css`.
- JavaScript in `webroot/js`.
- Runtime PHP is believed to be 5.x (minor unknown).
- Important: this repository is a partial application workspace; a notable share of Pyramid/Cake runtime files are outside the repo in shared infrastructure.
- Consequence: local PHP execution/linting can produce mixed or incomplete results compared to the deployed environment.

## Prototype System (How It Works)
- Core source templates live in `Plugin/Prototype/CorePlugin/View/...`.
- Site-level overrides live in `Plugin/Prototype/View/...` and are created on install.
- The plugin may be available even when a specific prototype is not installed.

### Admin Flow (High Level)
- Prototypes are initiated in the CMS admin by installing/activating a prototype.
- Installation creates the site-level override tree under `Plugin/Prototype/View/<slug>/...`.
- After install, edits should be made in the site-level override tree, not CorePlugin.

## Folder Organization (Meaning)
- `View/Layouts/`: page layouts (default, home, contact, offline).
- `View/Elements/layout/`: shared layout partials (head, nav, body_masthead, footer).
- `Plugin/Prototype/CorePlugin/`: vendor-like core templates.
- `Plugin/Prototype/View/`: site-level override templates created on install.
- `webroot/css/scss/`: SCSS sources (authoritative).
- `webroot/css/stylesheet.css`: compiled output (do not edit directly).
- `docs/`: documentation grouped by purpose.

## Edit Boundaries
- Prefer site-level overrides in `Plugin/Prototype/View/` after a prototype is installed.
- Avoid touching CorePlugin files unless strictly necessary and tracked in Git.
- Follow `docs/design/atomic-reuse.md` for reuse-first styling.

## Operational Rules (Going Forward)
- Treat `webroot/css/scss/_theme.scss` as the theme source of truth.
- Keep CSS layer responsibilities stable; avoid cross-layer leakage of responsibilities.
- Prefer composition/layout reuse (`.c-*`, `.l-*`) before adding page-specific wrappers.
- Prefer block modifiers over new blocks unless DOM structure actually changes.
- Keep prototype-specific styles in `webroot/css/scss/_prototype-<slug>.scss`.
- Keep template control flow concise and local to one PHP section block when feasible.
- Use modern frontend JS modules as default behavior; keep legacy dependencies only when required by admin/debug/runtime constraints.
