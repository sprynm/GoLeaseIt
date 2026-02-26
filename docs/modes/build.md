# Build Mode

Goal: implement code changes in the correct runtime layers and file ownership boundaries.

## Load First
1. `docs/AGENTS.md`

## Load Client Context When Needed
1. Read `client-information.md` only if implementation affects client-facing copy, IA, UX priorities, or client-specific business rules.

## Build Context
1. `docs/architecture/frontend-structure.md`
2. `docs/architecture/layout-system.md`
3. `docs/architecture/system-overview.md`
4. `docs/architecture/pyramid-cms.md`
5. `docs/javascript/index.md`

## Key Layout Entry Points
1. Layouts: `View/Layouts/` (`default.ctp`, `home.ctp`, `contact.ctp`, `offline.ctp`).
2. Partials: `View/Elements/layout/` (`head.ctp`, `nav.ctp`, `body_masthead.ctp`, `footer.ctp`).
3. Main wrapper / skip-link target: `#content.site-wrapper`.

## Conditional Add-Ons
1. Content blocks: `docs/architecture/content-blocks.md`
2. Galleries: `docs/architecture/galleries.md`
3. Prototypes: `docs/architecture/prototype.md`
4. Plugin work: `docs/architecture/plugins.md`
