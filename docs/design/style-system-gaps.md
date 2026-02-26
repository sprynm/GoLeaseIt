# Design + Style System Gaps

Last reviewed: 2026-02-25

This file tracks what is still missing in the design-system docs, and what has already been closed.

## Resolved
1. CSS layer conventions are documented. — `docs/design/layers.md`
2. Utilities are cataloged. — `docs/design/utilities.md`
3. Compositions are documented. — `docs/design/compositions.md`
4. Block/component inventory exists. — `docs/design/components.md`
5. Token policy and naming conventions documented. — `docs/design/tokens.md`
6. Exceptions policy exists. — `docs/design/exceptions.md`
7. Prototype styling guidance exists. — `docs/design/prototypes.md`
8. Accessibility checklist exists. — `docs/design/accessibility.md`
9. Font loading and token entry points documented. — `docs/design/fonts.md`
10. Font sizing principles documented (no drifting values). — `docs/design/font-sizing.md`
11. Spacing token intent table cleaned to match active tokens only. — `docs/design/spacing-plan.md`
12. Hero guide rewritten as principles + source references only. — `docs/design/hero.md`

## Open
1. Token usage cookbook examples are still sparse.
   - Policy is documented in `docs/design/style-system.md` and `docs/design/tokens.md`, but practical copyable patterns for common cases (card components, section rails, button variants) are not yet written.
2. CSS discipline enforcement is manual.
   - No automated check catches hardcoded values, SCSS variables used instead of custom properties, or clamp() expressions outside `_theme.scss`. A `check-css-discipline.mjs` tool has been discussed but not built.
