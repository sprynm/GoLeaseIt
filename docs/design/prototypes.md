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

## Migration Checklist For Existing Core Prototypes
1. Create site override in `Plugin/Prototype/View/<slug>/`.
2. Move visual rules to `_prototype-<slug>.scss`.
3. Replace raw values with tokens.
4. Remove jQuery dependence from active render path.
5. Update status in `docs/architecture/prototype-catalog.md`.
