## Summary

Describe what changed and why.

## Scope

- In scope:
- Out of scope:

## Runtime Surface

Select the primary implementation surface:

- [ ] `View/Elements` (reusable section/component)
- [ ] `View/Layouts` (page assembly/wiring)
- [ ] Content Block
- [ ] Prototype
- [ ] Gallery
- [ ] Plugin

Notes:

## Design Delivery Checklist (Required)

- [ ] Phase 1 complete: concept iterated in static HTML files in `webroot/style-guide/preview-nnnn.html` (+ `stylesheet-mock.scss` as needed).
- [ ] Phase 2 complete: approved concept migrated to reusable SCSS system files (`_block-*` / `_prototype-*`) and style-guide coverage updated.
- [ ] Phase 3 complete: runtime code generated in the correct publishing surface.
- [ ] Phase 4 complete: deployment guidance updated in docs/runbook.

## Visual Fidelity + Regression (Required)

- [ ] `npm run css:build` passed.
- [ ] `npm run visual:capture` run.
- [ ] `npm run visual:compare` run and reviewed.
- [ ] `npm run cube:check` passed.
- [ ] Any visual diffs are intentional and documented below.

Visual diff notes:

## Template/Runtime Safety (Required)

- [ ] `node tools/check-ctp-balance.cjs` passed (or existing unrelated issues noted).
- [ ] `php -l` passed for touched `.ctp` / `.php` files.
- [ ] `npm run contracts:check` run when layout/field wiring changed.

Exceptions/failures and rationale:

## Content/Admin Contract (When Applicable)

- [ ] Required page fields documented or updated.
- [ ] Required prototype fields documented or updated.
- [ ] Menu/navigation IDs and bindings verified.
- [ ] Asset source/path expectations documented (icons/images).

## Deployment Notes (Required)

Link the runbook updates and include exact rollout steps.

- Runbook/doc links:
- Admin setup steps:
- Post-deploy validation steps:
- Rollback plan:

## QA Evidence

Attach screenshots or links for desktop/tablet/mobile states and key changed sections.

- Desktop:
- Tablet:
- Mobile:

## Risks / Follow-ups

- Risks:
- Follow-ups:
