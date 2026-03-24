# Prototype Styling Guide

Last reviewed: 2026-02-17

This guide defines how prototype-specific styles should be authored in the CUBE/style-system structure.

## When To Create `_prototype-<slug>.scss`
Create a prototype SCSS partial only when the pattern is specific to that prototype and cannot be expressed cleanly using:
1. existing compositions (`.c-*`, `.l-*`)
2. existing utilities (`.u-*`)
3. existing blocks/modifiers (`.block`, `.block--variant`)

If the pattern is reusable across pages/prototypes, add to shared block/composition/utilities instead.

## Naming
- File: `_prototype-<slug>.scss`
- Wrapper class: `.prototype-<slug>` or a prototype-specific root class used by template markup.
- Avoid generic selectors that can bleed into non-prototype pages.

## Enabling In Build
1. Add the partial in `webroot/css/scss/`.
2. Enable via `@use` in `webroot/css/scss/stylesheet.scss`.
3. Place near related prototype imports.

## Token Rules
- Use design tokens for spacing, color, type, radius, and shadow.
- Avoid raw values unless no suitable token exists.
- If needed, add semantic token in `_theme.scss` first.

## JS Rules For Prototypes
- Prefer no JS for purely visual behavior.
- If JS is required:
  - use vanilla JS,
  - scope selectors to prototype root,
  - respect reduced-motion and keyboard requirements.

## Component Notes

### `industries_served` — Industries We Serve grid

File: `View/Elements/home/industries_served.ctp` / `_prototype-home.scss`

Two layout modes, auto-selected from item count:

| Mode | Trigger | Intro position |
|---|---|---|
| **Inline** | Odd item count | First cell in the grid, same width as tiles |
| **Top-banner** | Even item count | Full-width row above tiles (`industry-grid--intro-top`) |

**Top-banner intro content layout** (at `$vp-md`+): two-column grid — heading and paragraph (max 65ch) on the left, CTA button on the right. Collapses to stacked at mobile.

**Tile grid**: CSS grid, `repeat(3, 1fr)` at `$vp-lg`, `repeat(2, 1fr)` at `$vp-sm`. Top-banner mode overrides `grid-template-rows` at desktop to `auto repeat(2, minmax(...))` so the intro row is auto-height and the two tile rows keep their defined minimum.

**Homepage runtime rule**: `View/Layouts/home.ctp` lets the industries section expand automatically and caps the request at `20` items as a safety limit. The odd/even layout mode is derived from the rendered item count, not an admin toggle.

No JS. No PHP parameter needed for mode selection — count drives it.

---

## Migration Checklist For Existing Core Prototypes
1. Create site override in `Plugin/Prototype/View/<slug>/`.
2. Move visual rules to `_prototype-<slug>.scss`.
3. Replace raw values with tokens.
4. Remove jQuery dependence from active render path.
5. Update status in `docs/architecture/prototype-catalog.md`.
