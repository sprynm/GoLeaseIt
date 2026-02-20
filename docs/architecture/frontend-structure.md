# Frontend Structure (CMS Reference)

This document describes the current frontend structure for the site. Keep this file updated as layouts, components, and tooling evolve.

## Layout entry points
- **Primary layout elements** live under `View/Elements/layout/`.
  - `head.ctp` renders the document head, meta, CSS, and opens `<body>`.
  - `nav.ctp` renders the sticky header + primary navigation.
  - `body_masthead.ctp` renders the interior hero (`.page-hero`) and opens `#content`.
  - `footer.ctp` closes the wrapper and includes footer/nav/scripts.
- **Page layouts** live under `View/Layouts/`.
  - `default.ctp` = standard content layout with optional subnav.
  - `home.ctp` = homepage layout (banner + main content).
  - `contact.ctp` = contact page with form + contact info.
  - `offline.ctp` / `maintenance-mode.ctp` = special states.

### When to create a new layout
Create a new layout only when the **page‑level structure** changes in a way that cannot be handled by:
- shared elements (e.g., `layout/body_masthead.ctp`)
- composition classes in the template
- optional sections injected via elements

Examples that justify a new layout:
- different wrapper structure (no nav, alternate content rail)
- different header/footer orchestration (microsite landing page)
- system state layout (offline/maintenance)

Examples that do **not** justify a new layout:
- hero variants (use masthead + custom fields)
- section order changes (use elements)
- one‑off styling (use blocks/modifiers)

## Navigation & subnav
- Main navigation is rendered by `View/Elements/layout/nav.ctp` via `$this->Navigation->show(1)`.
- Sub-navigation appears in `View/Layouts/default.ctp` when the current top-level item has children.

## Hero / banner behavior
- `body_masthead.ctp` renders the interior `.page-hero` system (`.page-hero--single`, optional `.page-hero--has-media`).
- `home_masthead.ctp` renders homepage hero variant (`.page-hero--home`).
- Home hero (`View/Elements/layout/home_masthead.ctp`) uses a standardized `<picture>` source order for banner images:
  - `banner-lrg` at `(min-width: 1441px)`
  - `banner-med` at `(min-width: 801px)`
  - `banner-sm` at `(min-width: 641px)`
  - `banner-xsm` default source
  - fallback `<img>` points to `banner-lrg`
- Interior hero (`View/Elements/layout/body_masthead.ctp`) currently uses:
  - `banner-fhdl` at `(min-width: 1441px)` and fallback `<img>`
  - `banner-med`, `banner-sm`, `banner-xsm` for smaller breakpoints
- Service/feature tiles may define custom image versions when their layout needs different crops.
- Unless a component has a defined exception, follow the same banner version pattern as the default responsive standard.

## Build tooling
- `npm run css:build` compiles SCSS into `webroot/css/stylesheet.css`.
- `npm run css:watch` watches for changes.
- Optional deploy tooling lives in `tools/` (upload + FTP test).

## Going-Forward Implementation Rules
- New visual sections should start with existing compositions/utilities before introducing new block classes.
- Component responsiveness should be container-first where practical; use viewport media queries for page-level orchestration.
- Navigation behavior should continue through `webroot/js/navigation-modern.js` and the `nav.ctp` structure.
- Prototype-specific section styling belongs in `_prototype-<slug>.scss`, not generic block files.
- Preserve progressive enhancement: core content and primary navigation must remain usable without JS.
- Keep layout-level CTP logic explicit and compact to minimize template parse/fallback issues.
