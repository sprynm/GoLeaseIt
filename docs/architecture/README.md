# Architecture Docs

This folder describes how the system works at a structural level.
Use it for runtime behavior, system organization, and platform-specific rules.

## Purpose
- Explain how the CMS is structured and how it runs.
- Capture system-level workflows (e.g., prototype installation/activation).
- Document folder meaning and ownership boundaries.

## Recommended Reading Order
1. `docs/architecture/system-overview.md`
2. `docs/architecture/pyramid-cms.md`
3. `docs/architecture/frontend-structure.md`
4. `docs/architecture/layout-system.md`
5. `docs/design/atomic-reuse.md`
6. `docs/architecture/agent-first.md`
7. `docs/architecture/optional-components.md`
8. `docs/architecture/admin-javascript.md`
9. `docs/architecture/script-loading-map.md`
10. `docs/architecture/publishing-contract-matrix.md`
11. `docs/architecture/prototype.md`
12. `docs/architecture/content-blocks.md`
13. `docs/architecture/galleries.md`
14. `docs/architecture/plugins.md`
15. `docs/architecture/prototype-catalog.md`
16. `docs/architecture/new-site-playbook.md`

## Scope (What belongs here)
- CakePHP stack overview and CMS behavior.
- Prototype system lifecycle (admin install/enable/override flow).
- Section-by-section publishing contract mapping (how/where/why).
- Content block lifecycle and injection model.
- Gallery block lifecycle, rendering contract, and override behavior.
- Plugin lifecycle and custom-module decision criteria.
- Directory structure and ownership (Core vs site overrides).
- Non-UI runtime constraints.
- Agent-first workflow constraints and optional component policy.

## Out of Scope
- Detailed styling rules (see `docs/design/`).
- Linting/checklists (see `docs/quality/`).
