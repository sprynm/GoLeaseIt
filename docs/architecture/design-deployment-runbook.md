# Design Deployment Runbook

Use this runbook after design approval to deploy safely and repeatably.

## 1. Pre-Deploy Preparation

1. Confirm approved preview implementation exists in:
   1. `webroot/style-guide/preview-home-full.html`
   2. Relevant SCSS source files.
2. Build assets:
   1. `npm run css:build`
3. Run required checks:
   1. `node tools/check-ctp-balance.cjs`
   2. `php -l <each touched .ctp/.php file>`
   3. `npm run contracts:check` when layout/field wiring changed.
4. Run visual gates:
   1. `npm run visual:capture`
   2. `npm run visual:compare`

## 2. Map Feature To Runtime Surface

Select implementation target:
1. Element: reusable section/component template.
2. Layout: page assembly/wiring and section orchestration.
3. Prototype: repeatable item collections.
4. Content block: one-off editorial composition.
5. Gallery: media-centric structured section.
6. Plugin: custom logic/integration beyond content primitives.

Document this choice in the feature notes before deploying.

## 3. Admin Data Setup

For each new/changed section, define:
1. Required fields (must be present to render correctly).
2. Optional fields (safe defaults).
3. Slug/instance fallback behavior.
4. Menu IDs required (if nav-driven output).
5. Image/icon source rules and expected asset path.

Record this in:
1. Section-specific docs (for example homepage docs),
2. Or `docs/architecture/prototype.md` / `content-blocks.md` / `galleries.md` updates.

## 4. Deployment Sequence

1. Deploy code to target environment.
2. Clear application/cache as required by environment.
3. In admin:
   1. Install/enable needed prototype starters.
   2. Create/populate instances and item content.
   3. Configure page fields and menu bindings.
   4. Publish items/pages.
4. Rebuild/reload frontend assets if environment requires it.

## 5. Post-Deploy Validation

1. Visual validation:
   1. Home + affected templates at desktop/tablet/mobile.
   2. Key spacing/rhythm checks against approved preview.
2. Functional validation:
   1. CTA links.
   2. Navigation (desktop + mobile drawer).
   3. Prototype/content-block fallback states.
3. Accessibility baseline:
   1. Keyboard traversal.
   2. Focus visibility.
   3. Contrast spot checks for altered sections.

## 6. Rollback Plan

1. Revert code to previous stable commit.
2. Restore prior admin bindings/instance IDs if changed.
3. Re-run validation checks.
4. Log rollback reason and required follow-up fix.

## 7. Required Delivery Notes

Every deployment handoff must include:
1. Files changed (templates, SCSS, JS, docs).
2. Commands run and pass/fail status.
3. Admin settings populated (exact field keys).
4. Known limitations or deferred items.
