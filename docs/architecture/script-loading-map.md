# Script Loading Map

Last reviewed: 2026-02-18

This map shows where frontend scripts are loaded, so migration work can be scoped safely.
It is the canonical source for load locations; behavior details live in `docs/architecture/admin-javascript.md`.

## Primary Frontend Path

### `View/Elements/layout/footer.ctp`
- Debug-only:
  - jQuery CDN (`ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js`)
- Always loaded via `$scriptArray`:
  - `navigation-modern`
  - `observers`
- Conditionally lazy-loaded:
  - `forms.js` (loaded by inline bootstrap when form selectors are present)
- Extension point:
  - `pluginScriptBottom` block can inject scripts from plugins.

## Legacy Layout Paths

### `View/Layouts/referrals.ctp`
- Always loaded:
  - jQuery CDN (`3.5.1`)
  - `lazyload.min`
  - `jquery.cookie`
  - `cms`
  - `forms`
- Status: legacy path, not migrated.

### `View/Layouts/offline.ctp`
- Always loaded:
  - jQuery CDN (`3.4.1`)
  - WebFont loader CDN
  - `jquery-ui.min`
  - `cms`
- Conditionally loaded:
  - Recaptcha scripts (`ReCaptcha.invisible` toggle)
- Status: legacy path, not migrated.

## Plugin Injection Path

### `pluginScriptBottom` block
- Location:
  - `View/Elements/layout/footer.ctp`
- Behavior:
  - Any view/plugin can inject scripts through Cake blocks.
- Migration impact:
  - Grep this block usage before removing legacy JS libraries.

## Legacy Files Present (Not Primary Path)
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

## Removal Safety Rules
1. Confirm script is not referenced in any layout/element/plugin/admin view.
2. Confirm feature-level behavior still works on relevant pages.
3. Remove from load path first, then remove file in a second change.
