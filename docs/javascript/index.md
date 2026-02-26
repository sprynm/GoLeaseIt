# JavaScript Docs Index

Last reviewed: 2026-02-26

This is the canonical JavaScript document for frontend load paths, behavior contracts, legacy inventory, and QA rules.

## Scope and Policy

1. Keep frontend behavior vanilla-first.
2. Baseline scripts for primary frontend path:
   - `library.js`
   - `navigation-modern.js`
   - `observers.js`
   - `media-lightbox.js`
3. Load `forms.js` only when form selectors are present.
4. Treat jQuery as legacy except where legacy layouts/plugins still depend on it.

## Script Loading Map

### Primary frontend path

`View/Elements/layout/footer.ctp`:
1. Debug-only: jQuery CDN (`ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js`)
2. Always loaded via `$scriptArray`:
   - `library`
   - `navigation-modern`
   - `observers`
   - `media-lightbox`
   - `legal-notice`
3. Conditionally lazy-loaded:
   - `forms.js` (inline bootstrap when form markers exist)
4. Extension point:
   - `pluginScriptBottom` block

### Legacy layout paths

`View/Layouts/referrals.ctp`:
1. jQuery CDN (`3.5.1`)
2. `lazyload.min`, `jquery.cookie`, `cms`, `forms`
3. Status: legacy, not migrated

`View/Layouts/offline.ctp`:
1. jQuery CDN (`3.4.1`), WebFont loader CDN
2. `jquery-ui.min`, `cms`
3. Conditional recaptcha scripts (`ReCaptcha.invisible`)
4. Status: legacy, not migrated

## Active Script Inventory

### `webroot/js/library.js`
1. Shared lightweight behavior (for example close-trigger handling)
2. Status: modern vanilla JS
3. Keep globally loaded in primary frontend layout

### `webroot/js/navigation-modern.js`
1. Mobile drawer open/close
2. Desktop submenu popovers
3. Keyboard support (`Escape`, arrow navigation in popovers)
4. Status: modern vanilla JS

### `webroot/js/observers.js`
1. Toggles `.observe` elements to `.visible` via `IntersectionObserver`
2. Supports `data-observer-*` options (`once`, `threshold`, `margin`, `root`)
3. Reduced-motion remains CSS-owned (`prefers-reduced-motion`)
4. Fallback: mark observed content visible when `IntersectionObserver` is unavailable
5. Status: modern vanilla JS

### `webroot/js/forms.js`
1. Client-side error visibility
2. Radio/checkbox required helpers
3. Recipient-dependent field visibility
4. Status: modern vanilla JS
5. Load conditionally

### `webroot/js/media-lightbox.js`
1. Declarative lightbox via `data-lightbox="<config>"`
2. Group navigation + keyboard controls
3. Auto-detection fallback for image anchors containing `<img>`
4. Status: modern vanilla JS
5. Keep globally loaded in primary frontend layout

## Legacy JS Inventory (Repo Presence)

- `webroot/js/cms.js`
- `webroot/js/header-notice.js`
- `webroot/js/jquery.cookie.js`
- `webroot/js/jquery-ui.min.js`
- `webroot/js/jquery-ui-timepicker-addon.js`
- `webroot/js/datepicker.js`
- `webroot/js/timepicker.js`
- `webroot/js/sort.js`
- `webroot/js/fancybox-init.js`
- `webroot/js/jquery.fancybox.js`
- `webroot/js/passive.js`
- `webroot/js/jquery.passive-listeners.js`

## Migration and Removal Rules

1. Migrate active user-facing layouts first.
2. Migrate plugin script paths when the related plugin is being touched.
3. Remove from load paths first, then remove files in a separate change.
4. Before deletion, confirm no references in layouts/elements/plugins/admin views.

## QA Checklist After JS Changes

1. Mobile nav drawer works with mouse, touch, and keyboard.
2. Desktop submenu popovers open/close correctly and remain keyboard reachable.
3. Form validation/recipient visibility behavior still works.
4. `.observe` content remains visible with JS disabled.
5. Reduced-motion users are not forced into animated transitions.
6. `data-lightbox` links open, close, and navigate correctly with mouse and keyboard.
