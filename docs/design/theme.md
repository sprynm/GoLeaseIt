# Theme System

Theme is SCSS-first. The theme file defines the primitive values and emits CSS tokens.

## Source of Truth
- `webroot/css/scss/_theme.scss` defines SCSS primitives (colors, type, spacing, radii, layout constants).
- CSS custom properties are emitted from those SCSS values for runtime alignment only.

## Guidance
- If a value is purely theme/config, use SCSS variables.
- Use CSS custom properties only when multiple components must align at runtime or when values must be adjusted via media/container queries.
