# Testing Mode

Goal: verify correctness, regressions, and publish/runtime safety before handoff.

## Load First
1. `docs/AGENTS.md`

## Load Client Context When Needed
1. Read `client-information.md` only if validation criteria depend on client-specific content, IA, UX behavior, or business constraints.

## Testing Context
1. `docs/quality/lint.md`
2. `docs/ai/closeout-rules.md`
3. `docs/architecture/publishing-contract-matrix.md`

## Required Checks
1. CSS builds cleanly: `npm run css:build`
2. CTP structure check: `node tools/check-ctp-balance.cjs`
3. PHP syntax for touched files: `php -l <file>`
4. Visual baseline capture: `npm run visual:capture`
5. Visual diff check: `npm run visual:compare`
6. CUBE discipline check: `npm run cube:check`

## Conditional Add-Ons
1. Prototype sections: `docs/architecture/prototype.md`
2. Content block sections: `docs/architecture/content-blocks.md`
3. Gallery sections: `docs/architecture/galleries.md`
4. Release/deploy checklist: `docs/architecture/design-deployment-runbook.md`
