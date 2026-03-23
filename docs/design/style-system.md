# Style System Quick Rules

These are short, durable rules for styling and layout consistency.

## Spacing
- Spacing comes from `--space-*` tokens, not raw values.

## Surfaces
- Use `u-surface-base` and `u-surface-muted` for section backgrounds.
- `site-wrapper` variants (`site-wrapper--default`, `site-wrapper--platter`, `site-wrapper--dark`) map to the same surface token family (`--surface-bg-*`).
- `section-white` and `section-soft` are legacy aliases; do not introduce them in new markup.

## Typography
- Typography uses one scale only: `--step-*` (core scale) and `--type-*` (semantic aliases mapped to the step scale).
- Do not introduce parallel page/prototype type scales (`--article-*`, bespoke heading tokens, ad hoc clamp ladders).

## Layout
- Container widths come from theme primitives (e.g., `$frameMax` / `--frame-max`) or `.c-container` modifiers.
- Hero layout is controlled via CSS custom properties on `.page-hero`, not inline styles.
- Use block modifiers for layout variants (for example `.article-layout--no-banner`) rather than creating separate one-off page classes.
- Reuse existing classes where sensible, especially global constructs:
  - layout/composition classes (`.c-*`, `.l-*`)
  - utilities (`.u-*`)
  - molecule-level blocks (for example `.btn`)
- New classes should be introduced only when reuse cannot express the requirement cleanly.

## Theme (SCSS vs CSS Variables)
- **SCSS variables are the source of truth** for theming.
- CSS custom properties are emitted from SCSS for runtime alignment only.
- If a value is purely theme/config, prefer SCSS; if it must vary at runtime, use `var(--*)`.

## Templates
- Keep template logic in a single PHP block per section to avoid tag-juggling bugs.

## Token-Driven Components
- Use spacing and typography tokens (`--space-*`, `--step-*`, `--lh-*`) instead of raw values.
- Prefer color tokens from theme (`--color-*`, `--shadow-*`, `--radius-*`).
- For transparent colors, use RGB + alpha tokens:
  - `rgb(var(--white-rgb) / var(--alpha-85))`
  - `rgb(var(--color-brand-primary-rgb) / var(--alpha-16))`
- Avoid raw values. If a token does not exist, create a **semantic token** (e.g., `--cta-pad-y`, `--card-gap`, `--hero-cut-angle`) in the theme/token layer so intent is explicit and can map to theme primitives later.
- Use `color-mix()` when intentionally blending two colors, not as a replacement for simple opacity.

## Motion
- Observer state should be generic:
  - `.observe` = managed by observer script
  - `.visible` = currently in viewport
- Interactive media overlays use declarative trigger contracts in markup (`data-lightbox="..."`) with shared runtime behavior (`media-lightbox.js`), not per-template JS.
- Animation is opt-in and declarative in CSS:
  - `.observe.animate` = neutral/pre-entry state
  - `.observe.animate.visible` = animated/entered state
- Progressive enhancement:
  - animation-only hidden states should be gated by `.js-observers` on `<html>` so content remains visible with JS disabled.
- Reduced-motion handling belongs in CSS only (`prefers-reduced-motion`), not in observer JS.

## Related
- `docs/design/style-system-gaps.md` (known documentation gaps and suggested additions)
- `docs/design/layers.md` (CSS layer responsibilities)
- `docs/design/utilities.md` (utility class catalog)
- `docs/design/compositions.md` (layout primitives)
- `docs/design/components.md` (block component index)
- `docs/design/prototypes.md` (prototype styling rules)
- `docs/design/accessibility.md` (interaction and accessibility checklist)

## Normalized Legacy Decisions
- Keep SCSS partial responsibilities clear: tokens/base/compositions/utilities/blocks/exceptions.
- Do not reintroduce broad global component selectors where block roots exist.
- Avoid one-off values in components when an equivalent token exists.
- Keep exception styles narrowly scoped and easy to remove.

## Lessons Learned
- Classify before styling. For any new section, first separate:
  - shared page shell
  - shared component paint
  - section-specific composition
  Only the last category should live in the section block.
- Do not let a feature block own a page type. If a section renders inside the same shell as plain content pages, put shell rules in a shared article/content partial and keep the feature block incremental.
- Shared primitives must live in shared layers. If something is reused outside its original surface, move it:
  - kickers/eyebrows into utilities or a shared component layer
  - card paint into a shared utility/component
  - article/page rhythm into a shared article/content layer
- Prefer composition overrides over local paint. Feature blocks should describe layout, ordering, and spacing before introducing new backgrounds, shadows, radii, or colors.
- Hardcoded values are a design-system smell. If a color, radius, or spacing value feels “special,” create or reuse a semantic token instead of freezing it in a block.
- Reuse global components in templates early. It is cheaper to render `.btn`, shared kickers, or shared cards directly in markup than to recreate their appearance later in SCSS.
- Keep responsive intent explicit. Similar-looking breakpoint blocks are not duplicates unless their ranges actually overlap. Verify token values before collapsing them.
- Do not hide shared behavior inside prototype-only stylesheets. If a pattern is useful beyond one prototype or page family, move it out immediately to avoid accidental dependencies.
