# New Site Playbook (Pyramid + CakePHP)

Last reviewed: 2026-02-18

This is a platform-level checklist for standing up a new site on the Pyramid CMS stack.

## 1. Base Setup
1. Confirm app boots with `Config/core.php`, `Config/bootstrap.php`, `Config/pyramid.php`, `Config/database.php`.
2. Confirm CMS install state and admin routing are active.
3. Confirm writable paths for media/cache.

## 2. Layout Skeleton
1. Start from `View/Layouts/default.ctp` and `View/Layouts/home.ctp`.
2. Keep shared layout elements in `View/Elements/layout/`:
   - `head.ctp`
   - `nav.ctp`
   - `body_masthead.ctp`
   - `footer.ctp`
3. Create new layouts only when page-level structure changes, not for one-off content variants.
4. Use current hero structure as baseline:
   - interior pages: `body_masthead.ctp` (`.page-hero`)
   - homepage: `home_masthead.ctp` (`.page-hero--home`)

## 3. Navigation + Menus
1. Use `Navigation->show(<id>)` for menu render points.
2. Keep menu IDs documented per project (header, footer services, footer company, CTA collections).
3. Ensure keyboard behavior remains valid with `navigation-modern.js`.

## 4. Page Model Strategy
1. Use page title + content for baseline.
2. Use default page fields for repeated single-value fields across pages.
3. Use prototypes for repeatable collections.
4. Use action-map fields only when a page must route to a controller action.

## 5. Prototypes Strategy
1. Install prototype instance from admin.
2. Edit site-level overrides in `Plugin/Prototype/View/<slug>/...`.
3. Avoid editing `Plugin/Prototype/CorePlugin/...` unless unavoidable.
4. For each active prototype:
   - define render templates (`PrototypeInstances`, `PrototypeItems`, optional `PrototypeCategories`)
   - add/enable corresponding `_prototype-<slug>.scss` only when needed
   - document migration status in `docs/architecture/prototype-catalog.md`

## 6. Design System Adoption
1. SCSS source of truth is `webroot/css/scss/`.
2. Use token-driven values (`--space-*`, `--step-*`, `--color-*`) over raw values.
3. Reuse composition/utilities/atoms before creating new block classes.
4. Keep exceptions minimal and documented.

## 7. Responsive Images
1. Use `<picture>` for hero/banner.
2. Homepage hero source order:
   - `banner-lrg` `(min-width: 1441px)`
   - `banner-med` `(min-width: 801px)`
   - `banner-sm` `(min-width: 641px)`
   - `banner-xsm` default
3. Interior hero source order:
   - `banner-fhdl` `(min-width: 1441px)` and fallback `<img>`
   - `banner-med` `(min-width: 801px)`
   - `banner-sm` `(min-width: 641px)`
   - `banner-xsm` default
4. Allow prototype-level image versions only where crop/aspect needs differ by component.

## 8. JavaScript Baseline
1. Keep primary frontend scripts lean:
   - `navigation-modern.js`
   - `observers.js` (if `.observe` is used)
   - `forms.js` loaded conditionally
2. Treat jQuery as legacy except for legacy layouts/plugins still depending on it.
3. See script map before removals:
   - `docs/architecture/script-loading-map.md`

## 9. Accessibility Baseline
1. Preserve visible focus states.
2. Respect `prefers-reduced-motion`.
3. Maintain readable text contrast and target size for touch controls.
4. Validate keyboard path for nav, drawers, and forms.

## 10. Delivery Checklist
1. Build CSS (`npm run css:build`).
2. Lint PHP touched files (`php -l <file>`).
3. Validate no broken element references in layouts.
4. Verify homepage, interior page, contact form, and mobile nav.
5. Update docs for any new pattern introduced.

## Related
- `docs/architecture/pyramid-cms.md`
- `docs/architecture/frontend-structure.md`
- `docs/architecture/admin-javascript.md`
- `docs/architecture/script-loading-map.md`
- `docs/design/style-system.md`
