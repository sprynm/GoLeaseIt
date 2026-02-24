# Planning Mode

Goal: scope work, pick the right publishing mechanism, and define implementation order.

## Load First
1. `docs/AGENTS.md`
2. `client-information.md`

## Planning Context
1. `docs/architecture/publishing-contract-matrix.md`
2. `docs/architecture/system-overview.md`
3. `docs/architecture/pyramid-cms.md`
4. `docs/architecture/new-site-playbook.md`

## Conditional Add-Ons
1. Prototypes: `docs/architecture/prototype.md`, `docs/architecture/prototype-catalog.md`
2. Content blocks: `docs/architecture/content-blocks.md`
3. Galleries: `docs/architecture/galleries.md`
4. Custom plugin decisions: `docs/architecture/plugins.md`

## Plan Format

Use this structure for code-change plans.

### 1. Mode + Scope Header
1. Mode: planning/design/build/testing.
2. In scope: systems/components and affected areas.
3. Out of scope: explicit non-goals to prevent drift.

### 2. Requirements (EARS)

Write compact, testable requirements about the system/component under change (not the agent). Name the system explicitly.

- Ubiquitous: The `<system>` shall `<response>`.
- State-driven: While `<precondition(s)>`, the `<system>` shall `<response>`.
- Event-driven: When `<trigger>`, the `<system>` shall `<response>`.
- Optional scope: Where `<feature/scope applies>`, the `<system>` shall `<response>`.
- Unwanted behavior: If `<unwanted condition>`, then the `<system>` shall `<mitigation>`.
- Complex: While `<precondition(s)>`, when `<trigger>`, the `<system>` shall `<response>`.

Rules:
1. Use IDs (`R1`, `R2`, ...).
2. Prefer observable behavior/invariants.
3. Avoid implementation details unless they are part of external contract.

### 3. Implementation

Describe agent actions as concrete steps.

Rules:
1. Size steps to change scope (small fix: few steps; larger change: git-committable chunks).
2. One concrete outcome per step.
3. Add a USER checkpoint for major/risky changes.
4. Use IDs (`S1`, `S2`, ...).

### 4. Verification

Add explicit checks mapped to requirement IDs.

Rules:
1. Each item references one or more requirements (`R#`).
2. Name the check (`npm test`, `npm run css:build`, `php -l`, manual validation).
3. For each critical requirement, include at least one concrete evidence-producing check.
4. If not verified, mark as `Not verified` with reason.

### Template (Shape Only)

Requirements:
R1: When `<trigger>`, the `<system>` shall `<response>`.
R2: While `<state>`, the `<system>` shall `<response>`.

Implementation:
S1: `<edit(s) that satisfy R1/R2>`.
S2: USER checkpoint: `<review/commit chunk>`.

Verification:
V1 (R1,R2): `<check command or targeted manual validation>`.
