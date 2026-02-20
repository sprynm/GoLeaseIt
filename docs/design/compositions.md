# Composition Primitives

This document describes layout‑level composition classes defined in `webroot/css/scss/_compositions.scss` and how to use them.

## Containers and Frames
- `.c-frame`, `.c-container`
  - Establish the content rail and `container-type: inline-size`.
  - Grid columns: full / content / full.
  - Place layout sections inside these to align with nav and hero rails.

- `.c-container--narrow`
  - Sets `--frame-max: 70ch`.

- `.c-container--normal`
  - Sets `--frame-max: 75rem`.

- `.c-container--full`
  - Sets `--frame-max: 90rem`.

## Regions
- `.c-region`
  - Applies section vertical padding via `var(--region-space)`.

## Stack Utilities (Composition Level)
- `.c-stack > * + *`
  - Default vertical rhythm using `var(--space-md)`.

- `.c-stack[data-space="sm"]`
  - Uses `var(--space-sm)`.

- `.c-stack[data-space="lg"]`
  - Uses `var(--space-lg)`.

## Grid
- `.c-grid`
  - Base grid with `gap: var(--space-lg)` and one column at c1.

- `.c-grid--3`
  - Auto steps to 2 columns at c2, 3 columns at c3.

- `.c-grid[data-cols-c2="2"]`
  - 2 columns from c2 and up.

- `.c-grid[data-cols-c2="3"]`
  - 3 columns from c2 and up.

- `.c-grid[data-cols-c3="3"]`
  - 3 columns from c3 and up.

- `.c-grid[data-cols-c3="4"]`
  - 4 columns from c3 and up.

## Sidebar Layouts
- `.c-sidebar`, `.l-with-subnav`
  - Two column layout at large viewport (`$vp-lg`), stacked on smaller sizes.

- `.l-single`
  - Single column layout with grid gap and region padding.

## Layout Rails
- `.layout-rail`
  - Constrains child width to `70ch`.

## Usage Notes
- Composition classes control structural layout, not component visuals.
- Prefer composition classes over one‑off layout rules inside blocks.

## First‑Class Layout Policy
Use compositions for layout before adding block‑level layout rules.

### Decision Rule
1. Does the change affect **layout structure** (columns, rails, stacking, grid)?  
   Use a composition class (`.c-*`, `.l-*`) or add a new composition.
2. Is it **visual styling** (color, shadows, borders, imagery)?  
   Use a block or modifier.
3. Is it **page‑level orchestration** (section spacing, wrapper background)?  
   Use wrapper classes + composition tokens, not bespoke block CSS.

### Example: Prefer Composition
```html
<section class="c-stack" data-space="lg">
  ...
</section>
```

### Example: Avoid Per‑Section Layout CSS
```scss
/* Avoid: layout logic hidden inside a block */
.cta { display: grid; grid-template-columns: 2fr 1fr; }
```

### If You Need a New Composition
Add it to `webroot/css/scss/_compositions.scss` and document it here.

