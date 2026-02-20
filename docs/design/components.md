# Components Catalog (SCSS Blocks)

This document maps the block‑level SCSS files to the components/sections they style. It is a quick index for reuse and modification.

## Block Files
- `_block-breadcrumbs.scss` — breadcrumb UI.
- `_block-content.scss` — CMS content styling (typography inside `main`, rich text elements).
- `_block-footer.scss` — site footer.
- `_block-forms.scss` — form controls, labels, and validation states.
- `_block-gallery.scss` — gallery display patterns.
- `_block-header.scss` — header + topbar + structural header elements.
- `_block-hero.scss` — `.page-hero` and interior hero system.
- `_block-nav.scss` — primary navigation styling.
- `_block-notifications.scss` — alerts/flash messages.
- `_block-pagination.scss` — pagination controls.
- `_block-subnav.scss` — sub‑navigation menu.
- `_block-tables.scss` — table styling.
- `_block-tiles.scss` — tile/card grids.

## Prototype Blocks
These are prototype‑specific blocks and should only be enabled when the prototype is active.
- `_prototype-faq.scss`
- `_prototype-feature-boxes.scss`
- `_prototype-news.scss`
- `_prototype-our-services.scss`
- `_prototype-projects.scss`
- `_prototype-staff.scss`
- `_prototype-testimonials.scss`

## Notes
- If a new section can be expressed as a variant of an existing block, prefer modifiers.
- If a new block is created, add it here and document why existing blocks were insufficient.
  - Add a short “reuse decision” note (what existing block/composition was considered and why it didn’t fit).

## Block + Modifier Convention
Use a consistent modifier pattern to avoid unnecessary new blocks.

- Block: `.block`
- Element: `.block__element`
- Modifier: `.block--variant`
- Element modifier: `.block__element--state`

### Decision Rule
1. Can this be expressed by changing spacing, colors, or layout within the same structure?  
   Use a modifier (`.block--variant`).
2. Does it introduce new internal structure or a different DOM shape?  
   Create a new block and document why.

### Example
```scss
.tile { ... }
.tile--service { ... }
.tile__icon { ... }
.tile__icon--emphasis { ... }
```

