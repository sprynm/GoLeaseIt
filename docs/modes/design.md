# Design Mode

Goal: implement UI with consistent tokens, layers, and composition rules.

## Load First
1. `docs/AGENTS.md`
2. `client-information.md`

## Design Context
1. `docs/design/style-system.md`
2. `docs/design/tokens.md`
3. `docs/design/layers.md`
4. `docs/design/components.md`
5. `docs/design/compositions.md`
6. `docs/design/atomic-reuse.md`
7. `docs/design/prototypes.md`
8. `docs/design/accessibility.md`
9. `docs/architecture/design-delivery-workflow.md`
10. `docs/design/figma-context-cache/README.md`

## Design System / SCSS Specifics
1. Entry point: `webroot/css/scss/stylesheet.scss`.
2. System partials: `webroot/css/scss/_*.scss` (layered by purpose).
3. Theme tokens/primitives: `webroot/css/scss/_theme.scss`.
4. Prototype-specific styles: `webroot/css/scss/_prototype-<slug>.scss`.
5. Do not edit `webroot/css/stylesheet.css` directly.

## Atomic Reuse Priority (CUBE-Aligned)
1. Reuse utilities and atom-sized blocks before creating context-specific components.
2. Extend existing atoms (for example `.btn`) via modifiers or scoped wrappers.
3. Accept reduced prototype-level control if it improves cohesion.

## Key Layout Entry Points
1. Layouts: `View/Layouts/` (`default.ctp`, `home.ctp`, `contact.ctp`, `offline.ctp`).
2. Partials: `View/Elements/layout/` (`head.ctp`, `nav.ctp`, `body_masthead.ctp`, `footer.ctp`).
3. Main wrapper / skip-link target: `#content.site-wrapper`.

## Conditional Add-Ons
1. Theme-level token changes: `docs/design/theme.md`
2. Layout concerns: `docs/architecture/layout-system.md`, `docs/architecture/frontend-structure.md`
3. Gallery/article behavior: `docs/architecture/galleries.md`

## Figma Context Budget
1. Start with local cache: `docs/design/figma-context-cache/go-lease-it-slice.cache.json`.
2. Pull narrow `get_design_context` requests for only the node IDs being implemented.
3. Reseed full-file metadata only when page/node structure has changed.
