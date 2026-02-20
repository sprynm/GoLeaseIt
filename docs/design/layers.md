# CSS Layering Guide

This document defines what belongs in each `@layer` and how to keep the stylesheet ordered and predictable.

## Layer Order (Authoritative)
Defined in `webroot/css/scss/_layers.scss`:
```
@layer reset, tokens, base, compositions, utilities, blocks, exceptions;
```

## Layer Responsibilities
- `reset`:
  - Browser normalization and low-level element resets.
  - Files: `_reset.scss`.

- `tokens`:
  - Design tokens emitted as CSS custom properties.
  - Source of truth is SCSS, but tokens are emitted here for runtime alignment.
  - Files: `_theme.scss`, `stylesheet.scss` (token declarations).

- `base`:
  - Base element styling and typography defaults.
  - Global element selectors that set defaults for `body`, headings, lists, tables.
  - Files: `_base.scss`.

- `compositions`:
  - Layout primitives and composition utilities (containers, grids, stacks, rails).
  - Structural patterns that can be reused across sections.
  - Files: `_compositions.scss`.

- `utilities`:
  - Single‑purpose helper classes.
  - Should not encode layout context or page-specific styling.
  - Files: `_utilities.scss`.

- `blocks`:
  - Component and section styling.
  - Block‑level patterns, section modifiers, and visual variants.
  - Files: `_block-*.scss` and `_prototype-*.scss` when enabled.

- `exceptions`:
  - Minimal overrides to resolve conflicts or legacy edge cases.
  - Must include a comment that states why the exception exists and how to remove it.
  - Files: `_exceptions.scss`.

## Rules of Thumb
- If it describes the **global layout system**, it belongs in `compositions`.
- If it is a **one‑off fix**, it belongs in `exceptions` (with a removal note).
- If it styles a **component or section**, it belongs in `blocks`.
- If it can be applied anywhere without context, it belongs in `utilities`.

## Common Mistakes
- Putting tokens in `base` (tokens should be in the `tokens` layer only).
- Putting multi‑rule components in `utilities`.
- Adding exception rules without a removal plan.

