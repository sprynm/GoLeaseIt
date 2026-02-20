# Frontend JavaScript Guide

Last reviewed: 2026-02-18

This guide documents what frontend JavaScript is loaded now, what each script does, and what still remains in legacy paths.

## Scope

Use this file for behavior/policy.  
Use `docs/architecture/script-loading-map.md` as the canonical source for exact load locations and layout-specific script lists.

Primary frontend policy:
- Keep `navigation-modern.js` and `observers.js` as baseline scripts.
- Load `forms.js` only when form selectors are present.
- Treat jQuery as legacy (debug and legacy-layout compatibility only).

## Script Inventory

### `webroot/js/navigation-modern.js`
- Responsibility:
  - Mobile drawer open/close behavior.
  - Desktop submenu popover behavior.
  - Keyboard support (`Escape`, arrow navigation in popovers).
- Status: modern vanilla JS.
- Keep globally loaded in primary frontend layout.

### `webroot/js/observers.js`
- Responsibility:
  - Observes `.observe` elements and toggles `.visible` based on viewport intersection.
  - Supports per-element options through `data-observer-*` attributes.
- Default behavior:
  - `once=true`
  - `threshold=0.1`
  - `rootMargin="0px 0px -8% 0px"`
- Supported options:
  - `data-observer="once=false; threshold=0.25; margin=0px 0px -12% 0px; root=#container"` (shorthand)
  - `data-observer-once="true|false"`
  - `data-observer-threshold="0.1"` or `"0,0.25,0.5"`
  - `data-observer-margin="0px 0px -8% 0px"`
  - `data-observer-root="#selector"`
- Accessibility behavior:
  - Reduced-motion is handled in CSS (`prefers-reduced-motion`), not in JS.
  - If `IntersectionObserver` is unavailable, `.observe` elements are marked `.visible` immediately.
- Status: modern vanilla JS.
- Keep globally loaded while `.observe` pattern is in use.

### `webroot/js/forms.js`
- Responsibility:
  - Client-side error-message toggling.
  - Radio/checkbox required helpers.
  - Recipient-dependent field visibility for email forms.
- Status: modern vanilla JS.
- Loading policy:
  - Loaded only when form markers are present in DOM.

## Legacy Inventory (Repo Presence)

These files exist in the repo and should be treated as legacy unless explicitly used by mapped load paths.

- `cms.js`
- `header-notice.js`
- `jquery.cookie.js`
- `jquery-ui.min.js`
- `jquery-ui-timepicker-addon.js`
- `datepicker.js`
- `timepicker.js`
- `sort.js`
- `fancybox-init.js`
- `jquery.fancybox.js`
- `passive.js`
- `jquery.passive-listeners.js`

## Migration Policy
1. Migrate active user-facing layouts first.
2. Migrate plugin script paths only when those plugins/features are being touched.
3. Delete legacy files only after grep confirms no references in layouts, elements, plugins, or admin templates.

## QA Checklist After JS Changes
1. Mobile nav drawer works with mouse, touch, and keyboard.
2. Desktop submenu popovers open/close correctly and are keyboard reachable.
3. Email forms still show/hide validation and recipient-specific fields correctly.
4. `.observe` content is visible with JS disabled.
5. Reduced-motion users are not forced into animated transitions.

## Related
- `docs/architecture/script-loading-map.md`
