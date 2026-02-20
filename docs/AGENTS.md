# AGENTS.md

## Project Entry
This repository is a bootstrap build for the Go Lease It website on the Radarhill Pyramid/CakePHP stack.

Start here for every task:
1. Read `client-information.md` for client goals, positioning, IA, and content constraints.
2. Read `docs/ai/context.md` for technical context and codebase map.
3. Follow architecture/design docs only for the area you are editing.

## Core Objective
Use this repo as a clean, reusable implementation baseline while delivering Go Lease It-specific content and UX.

## Working Rules
- Keep the codebase client-agnostic at the framework/component level.
- Keep client-specific business direction in `client-information.md` and page content.
- Prefer small, reversible edits and validate with local build/lint checks.
- Do not edit compiled CSS directly (`webroot/css/stylesheet.css` is build output).
- Use existing design system patterns before adding new component patterns.

## Key Paths
- Runtime templates: `View/Layouts/`, `View/Elements/`
- SCSS source: `webroot/css/scss/`
- JS: `webroot/js/`
- Build tools: `tools/`
- Architecture docs: `docs/architecture/`
- Design docs: `docs/design/`
- AI workflow docs: `docs/ai/`
- Client brief: `client-information.md`

## Local Build + Checks
- Install deps: `npm install`
- Build CSS: `npm run css:build`
- Watch CSS: `npm run css:watch`
- Template balance heuristic: `node tools/check-ctp-balance.cjs`
- PHP syntax (per file): `php -l <file>`

## PHP Validation Caveat
- This is a PHP application, but a substantial part of the runtime/framework files live outside this repo in the shared Pyramid/Cake environment.
- Local `php` checks are useful, but results can be mixed because not all dependencies are present in-repo.

## Documentation Policy
- Keep durable implementation rules in `docs/`.
- Keep client strategy and messaging constraints in `client-information.md`.
- Avoid reintroducing decision-history/archive logs unless explicitly requested.
