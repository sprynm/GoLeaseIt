# Token Inventory

Last reviewed: 2026-02-19  
Source of truth: `webroot/css/scss/_theme.scss`

SCSS variables in `_theme.scss` are the primary source. CSS custom properties emitted in `:root` are runtime tokens consumed by components/utilities.

## Fonts
- `--font-sans`
- `--font-display`
- `--font-serif`

## Surface Colors
- `--color-surface-base`
- `--color-surface-muted`
- `--color-surface-soft`
- `--color-surface-inverse`
- `--color-surface-inverse-rgb`
- `--color-surface-dark`
- `--color-surface-hero`
- `--color-surface-footer`

## Text/Ink Colors
- `--color-ink-absolute`
- `--color-ink-absolute-rgb`
- `--color-ink-primary`
- `--color-ink-strong`
- `--color-ink-muted`
- `--color-ink-meta`
- `--color-ink-on-dark`
- `--color-ink-inverse`
- `--color-ink-on-accent`

## Brand and UI Colors
- `--color-border-muted`
- `--color-brand-primary`
- `--color-brand-primary-rgb`
- `--color-brand-primary-bright`
- `--color-brand-primary-hover`
- `--color-brand-primary-visited`
- `--color-brand-secondary`
- `--color-brand-punch`
- `--color-brand-accent`
- `--color-brand-accent-rgb`
- `--color-brand-accent-hover`
- `--color-brand-notice`
- `--color-brand-notice-rgb`
- `--color-focus-ring`

Normalization rule: prefer `*-rgb` as the source token, and derive the color token from it (for example `--color-brand-notice: rgb(var(--color-brand-notice-rgb));`).

## Alpha and Transparency Tokens
- `--white-rgb`
- `--alpha-95`
- `--alpha-85`
- `--alpha-75`
- `--alpha-60`
- `--alpha-42`
- `--alpha-35`
- `--alpha-25`
- `--alpha-16`
- `--alpha-10`
- `--alpha-04`
- `--alpha-02`

Use pattern: `rgb(var(--white-rgb) / var(--alpha-85))` or `rgb(var(--color-brand-primary-rgb) / var(--alpha-16))`.

## Component RGB Tokens
- `--color-nav-backdrop-rgb`
- `--color-hero-overlay-start-rgb`
- `--color-hero-overlay-end-rgb`
- `--color-footer-muted-rgb`
- `--color-footer-soft-rgb`
- `--color-footer-meta-rgb`

## Typography Scale
- `--fluid-body`
- `--font-weight-light`
- `--font-weight-regular`
- `--font-weight-medium`
- `--font-weight-semibold`
- `--font-weight-strong`
- `--font-weight-bold`
- `--font-weight-black`
- `--step--1`
- `--step-0`
- `--step-1`
- `--step-2`
- `--step-3`
- `--step-4`
- `--step-5`
- `--lh-body`
- `--lh-title`
- `--type-body-size`
- `--type-body-line`
- `--type-h1-size`
- `--type-h1-line`
- `--type-h2-size`
- `--type-h2-line`
- `--type-h3-size`
- `--type-h3-line`
- `--type-h4-size`
- `--type-h4-line`
- `--type-feature-h2-size`
- `--type-feature-h2-line`
- `--type-feature-h3-size`
- `--type-feature-h3-line`
- `--type-nav-size`
- `--type-nav-line`
- `--type-label-size`
- `--type-label-line`

## Spacing Scale
- `--space-2xs`
- `--space-xs`
- `--space-sm`
- `--space-md`
- `--space-lg`
- `--space-xl`
- `--space-2xl`

## Radius
- `--radius-xs`
- `--radius-sm`
- `--radius-md`
- `--radius-lg`
- `--radius-max`

## Shadow and Gradients
- `--shadow-sm`
- `--shadow-md`
- `--surface-gradient-dark`

## Layout/System Tokens
- `--frame-max`
- `--frame-gutter`
- `--region-space`
- `--nav-offset`
- `--sidebar-col`
- `--hero-max`
- `--hero-inline`
- `--hero-vspace`

## Usage Rules
1. Do not hardcode raw spacing/color/weight values when an equivalent token exists.
2. If a new value is repeated, add a semantic token in `_theme.scss` first.
3. Prefer semantic component tokens mapped to global tokens when intent matters.
4. For transparency, prefer shared alpha tokens with RGB tokens over inline `rgb(... / 0.xx)`.
5. Use `color-mix()` for blending two colors; use alpha tokens for single-color translucency.
