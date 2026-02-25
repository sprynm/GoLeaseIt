# AGENTS.md

## Project Entry
This repository is a bootstrap build for the Go Lease It website on the Radarhill Pyramid/CakePHP stack.

Start here for every task:
1. Read `docs/AGENTS.md` as the technical context baseline.
2. Select a mode pack in `docs/modes/` based on task function.
3. Read `client-information.md` only when client intent affects the task (copy, IA, UX priorities, branding, content strategy).
4. Follow architecture/design docs only for the area you are editing.

## Core Objective
Use this repo as a clean, reusable implementation baseline while delivering Go Lease It-specific content and UX.

## Stack Summary
- CMS: Radarhill "pyramid" CMS.
- Framework: CakePHP (legacy MVC).
- Templates: `.ctp` under `View/Layouts` and `View/Elements`.
- Assets: SCSS in `webroot/css/scss`, compiled to `webroot/css/stylesheet.css`.
- JavaScript: `webroot/js/`.
- Runtime PHP is believed to be 5.x (minor unknown).

## Design Source
- Figma: https://www.figma.com/design/lz33juiSBrbQAIR9beOjsH/Go-Lease-It-Slice?node-id=0-1&p=f&t=lU3DWDKtISZVT48K-0
- Contains: typography, home page, article page, no-banner page, feature page.

## Working Rules
- Keep the codebase client-agnostic at the framework/component level.
- Keep client-specific business direction in `client-information.md` and page content.
- Prefer small, reversible edits and validate with local build/lint checks.
- Do not edit compiled or build artifacts directly (for example `webroot/css/stylesheet.css`).
- Preserve design-system consistency across all changes; avoid ad hoc visual patterns.
- Use the design delivery workflow in `docs/architecture/design-delivery-workflow.md`:
  1. iterate in full-page preview,
  2. migrate to reusable style-guide/system,
  3. generate runtime code in correct publishing surface,
  4. document deployment via `docs/architecture/design-deployment-runbook.md`.

## Key Paths
- Runtime templates: `View/Layouts/`, `View/Elements/`
- SCSS source: `webroot/css/scss/`
- JS: `webroot/js/`
- Build tools: `tools/`
- Architecture docs: `docs/architecture/`
- Design docs: `docs/design/`
- AI workflow docs: `docs/ai/`
- Client brief: `client-information.md`

## Universal Design Guardrails
- Use existing design-system primitives before creating new UI patterns.
- Reuse utilities and atom-sized blocks where possible.
- Keep presentation rules in SCSS source, never in compiled CSS output.
- For detailed design implementation rules, load `docs/modes/design.md`.

## Local Build + Checks
- Install deps: `npm install`
- Build CSS: `npm run css:build`
- Watch CSS: `npm run css:watch`
- Template balance heuristic: `node tools/check-ctp-balance.cjs`
- PHP syntax (per file): `php -l <file>`

## PHP Validation Caveat
- This is a PHP application, but a substantial part of the runtime/framework files live outside this repo in the shared Pyramid/Cake environment.
- Local `php` checks are useful, but results can be mixed because not all dependencies are present in-repo.

## Runtime / Deployment Risk
- A template parse error can trigger the CMS fallback/maintenance page.
- Keep CTP logic in a single PHP block per section when possible.
- This repo does not include the full runtime stack; significant Pyramid/Cake files are external/shared.
- Treat local `php` execution/lint outcomes as partial signals, not full-environment guarantees.

## Where To Go Next
- Mode router: `docs/modes/README.md`.
- Architecture index: `docs/architecture/README.md`.
- Design docs hub: `docs/design/`.
- Quality checks: `docs/quality/lint.md`.

## Mode Packs (Load On Demand)
- Planning mode: `docs/modes/planning.md`
- Design mode: `docs/modes/design.md`
- Build mode: `docs/modes/build.md`
- Testing mode: `docs/modes/testing.md`

## Mode Trigger Rules
- Use `docs/modes/planning.md` when task intent includes: planning, scoping, sequencing, architecture choice, publishing-contract decisions.
- Use `docs/modes/design.md` when task intent includes: UI, layout, CSS/SCSS, tokens, accessibility, visual fidelity.
- Use `docs/modes/build.md` when task intent includes: implementation, refactor, template/controller/helper/plugin edits.
- Use `docs/modes/testing.md` when task intent includes: verification, linting, regression checks, release readiness.
- If intent is mixed, start in planning mode, then load the secondary mode pack only when execution reaches that phase.
- Load `client-information.md` in planning/design modes by default; in build/testing modes only when changes could affect messaging, IA, UX behavior, or client-specific business rules.

## Subagent Delegation Policy
- For multi-function tasks, delegate by function when subagents are available:
1. Planning subagent: produce scope, assumptions, and ordered implementation plan.
2. Design subagent: define/validate visual-system decisions and CSS strategy.
3. Build subagent: implement code changes in templates/helpers/controllers/assets.
4. Testing subagent: run checks, summarize findings, and identify residual risk.
- Keep each subagent on one mode pack plus the minimum required supporting docs.
- Merge outputs in this order: planning -> design/build -> testing.
- If subagents are unavailable, emulate the same workflow as sequential role passes with the same mode-pack boundaries.

## Documentation Policy
- Keep durable implementation rules in `docs/`.
- Keep client strategy and messaging constraints in `client-information.md`.
- Avoid reintroducing decision-history/archive logs unless explicitly requested.
