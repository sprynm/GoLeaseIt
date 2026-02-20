# Utilities Catalog

This document lists the current utility classes defined in `webroot/css/scss/_utilities.scss` and what they are for.

## Visibility
- `.u-visually-hidden` — hides content visually but keeps it available to screen readers.
- `.u-sr-only` — alias for `.u-visually-hidden`.

## Text / Alignment
- `.u-text-left` — left-align text.
- `.u-text-center` — center-align text.
- `.u-text-right` — right-align text.
- `.u-text-uppercase` — uppercase transform.

## Typography
- `.u-font-sans` — force sans stack (`--font-sans`).
- `.u-font-serif` — force serif stack (`--font-serif`).
- `.u-step-0` — font-size `--step-0`.
- `.u-step-1` — font-size `--step-1`.
- `.u-step-2` — font-size `--step-2`.

## Spacing Helpers
- `.u-pad-block-sm` — apply `padding-block: var(--space-sm)`.
- `.u-pad-block-md` — apply `padding-block: var(--space-md)`.
- `.u-pad-block-lg` — apply `padding-block: var(--space-lg)`.
- `.u-pad-block-xl` — apply `padding-block: var(--space-xl)`.
- `.u-pad-inline-sm` — apply `padding-inline: var(--space-sm)`.
- `.u-pad-inline-md` — apply `padding-inline: var(--space-md)`.
- `.u-pad-inline-lg` — apply `padding-inline: var(--space-lg)`.
- `.u-gap-sm` — apply `gap: var(--space-sm)`.
- `.u-gap-md` — apply `gap: var(--space-md)`.
- `.u-gap-lg` — apply `gap: var(--space-lg)`.
- `.u-mt-sm` — apply `margin-top: var(--space-sm)`.
- `.u-mt-md` — apply `margin-top: var(--space-md)`.
- `.u-mt-lg` — apply `margin-top: var(--space-lg)`.
- `.u-pt-2xl` — apply `padding-top: var(--space-2xl)`.
- `.u-mb-sm` — apply `margin-bottom: var(--space-sm)`.
- `.u-mb-md` — apply `margin-bottom: var(--space-md)`.
- `.u-mb-lg` — apply `margin-bottom: var(--space-lg)`.
- `.u-stack-sm > * + *` — vertical stack spacing with `var(--space-sm)`.
- `.u-stack-md > * + *` — vertical stack spacing with `var(--space-md)`.
- `.u-stack-lg > * + *` — vertical stack spacing with `var(--space-lg)`.

## Layout Helpers
- `.u-full-width` — apply `width: 100%`.
- `.u-max-w-narrow` — apply `max-width: 70ch`.
- `.u-max-w-normal` — apply `max-width: 75rem`.
- `.u-max-w-full` — apply `max-width: 90rem`.
- `.u-bg-muted` — apply `background: var(--color-surface-muted)`.
- `.u-flex` — apply `display: flex`.
- `.u-inline-flex` — apply `display: inline-flex`.
- `.u-block` — apply `display: block`.
- `.u-inline` — apply `display: inline`.
- `.u-hidden` — apply `display: none`.

## Media
- `.u-img-cover` — `object-fit: cover; width/height: 100%`.
- `.u-img-contain` — `object-fit: contain; width/height: 100%`.

## Links
- `.u-tel-static` — on desktop, removes underline and default cursor for `tel:` links.

## Misc
- `.avoid-break` — prevents line breaks (uses `white-space: nowrap`).
- `.observe.animate` — initial reveal-on-scroll state (hidden + translate) when JS observer is active.
- `.observe.animate.visible` — visible state after observer activation.

## Motion Accessibility
- `.observe.animate` disables transition and renders visible under `prefers-reduced-motion: reduce`.
- Without JS, `.observe.animate` remains visible (animation state is gated by `.js-observers` on `<html>`).

## Notes
- Utilities should remain single‑purpose and safe to apply in any context.
- If a utility starts to encode component structure, convert it to a composition or block.

## When To Use Utilities (Last‑Mile Policy)
Use utilities only after you have ruled out composition or block changes.

### Decision Flow
1. Is this **layout structure** (columns, rails, stacking, grid)?  
   Use compositions (`.c-*`, `.l-*`), not utilities.
2. Is this **component styling** (visual identity, state, variants)?  
   Use a block or modifier (`.block`, `.block--variant`).
3. Is this a **small, single‑property adjustment** that is safe anywhere?  
   Use a utility.

### Examples
- Good utility: add `u-mt-md` to add a small top margin.  
- Bad utility: using multiple utilities to recreate a complex component layout.

