# Design Delivery Workflow (Preview -> System -> Runtime)

This is the required process for building new page sections and visual features.

## Goal

Ship UI with high visual fidelity while keeping implementation reusable, testable, and deployable in Pyramid/CakePHP.

## Phase 1: Concept Iteration In Full-Page Preview

Primary working surface:
1. `webroot/style-guide/preview-home-full.html`
2. `webroot/css/scss/stylesheet-mock.scss`

Rules:
1. Prototype concepts in preview/mock first, not in runtime templates.
2. Iterate layout rhythm, spacing, color, corner treatment, and responsive behavior until approved.
3. Keep experiments scoped to mock/preview files.
4. For global shell elements (header/nav/footer), use shared preview partials instead of per-page duplicated markup.

Required checks:
1. `npm run css:build`
2. Manual review in preview at desktop/tablet/mobile widths.

Exit criteria:
1. Visual concept approved by stakeholder.
2. No unresolved spacing/interaction ambiguities.

## Phase 2: Migrate Approved Concept To Reusable Style System

Primary targets:
1. `webroot/css/scss/_block-*.scss` for shared blocks.
2. `webroot/css/scss/_prototype-*.scss` for prototype/page-specific patterns.
3. `webroot/style-guide/index.html` and related preview pages as visual references.

Rules:
1. Convert ad hoc preview CSS into reusable classes/tokens.
2. Prefer existing tokens/utilities/compositions before adding new ones.
3. Keep style-guide coverage for each reusable pattern so it becomes testable baseline.

Quality gate (source of truth):
1. `npm run visual:capture`
2. `npm run visual:compare`
3. Resolve visual diffs before runtime rollout.

## Phase 3: Generate Runtime Code In Correct Publishing Tool

Choose implementation surface using publishing contract intent:

1. Element (`View/Elements/...`):
   Use for reusable template fragments in layouts/sections.
2. Layout (`View/Layouts/...`):
   Use for page assembly, section orchestration, and top-level wiring.
3. Content Block:
   Use for one-off editorial sections needing rich text flexibility.
4. Prototype:
   Use for repeatable card/list/step/story/testimonial collections.
5. Gallery:
   Use when media-first rendering and gallery behavior are required.
6. Plugin:
   Use when functionality is cross-cutting, custom logic-heavy, or not suitable for content primitives.

Rules:
1. Keep content-model logic in admin-managed fields (page fields/prototype fields), not hardcoded copy.
2. Keep alternation/layout behavior in template/CSS logic, not by manual content ordering hacks.
3. Update existing contracts/docs when the data model changes.

## Phase 4: Deployment Documentation And Release

For every delivered feature, provide:
1. Admin setup steps (what to create/install).
2. Field population map (required/optional keys with examples).
3. Menu/navigation mapping requirements.
4. Build/check commands.
5. Post-deploy validation checklist and rollback notes.

Canonical runbook location:
1. `docs/architecture/design-deployment-runbook.md`

## Mandatory PR/Closeout Checklist

1. Concept approved in full-page preview.
2. Reusable styles migrated to SCSS system files.
3. Style-guide baseline updated.
4. Visual capture/compare completed.
5. Runtime code generated in correct tool surface.
6. Deployment runbook updated for the change.
