# Hero Layout Guide

This document explains the shared interior hero component (`.page-hero`), the layers it relies on, and how to configure it across subpages. The homepage keeps its legacy `.banner` masthead; everything else now flows through this system.

## 1. Conceptual Layers

| Layer | Responsibility | Key selectors / files |
|-------|----------------|------------------------|
| **Layout Wrapper** | Full-width hero section with navy background and optional media. | `.page-hero` in `_block-hero.scss`; rendered by `View/Elements/layout/body_masthead.ctp` |
| **Media Layer** | Responsive `<picture>` element and fallback imagery. | `.page-hero__media` |
| **Overlay Layer** | Navy gradient drawn above the media for legibility. | `.page-hero__overlay` |
| **Content Rail** | Grid-aligned container that keeps hero text locked to the navigation rails. | `.page-hero__inner`, `.page-hero__content` |
| **Variants** | Toggle single-column vs split layouts, sidebar sizing, and overlap depth. | `.page-hero--single`, `.page-hero--split` |
| **Injected Blocks** | Optional content rendered below the hero (filters, intro copy). | `.layout-rail` wrapper emitted by the layout |

## 2. Core CSS Settings

Important custom properties declared in `stylesheet.scss` and `_block-hero.scss`:

- `--nav-offset`: pushes the hero below the fixed navigation (defaults to `96px`).
- `--page-hero-padding`: symmetric top/bottom padding for hero content (`clamp(72px, 13vw, 140px)`).
- `--page-hero-inline`: inline gutters that match the navigation rails (`clamp(28px, 6vw, 96px)`).
- `--page-hero-gap`: responsive grid gap inside the hero (`clamp(24px, 6vw, 64px)`).
- `--page-hero-overlap`: overlap allowance (0–50px) for the portrait/sidebar column.
- `--page-hero-sidebar-max`: maximum width for the portrait/sidebar column (`340px`).

### Base Defaults

```scss
.page-hero {
  --page-hero-columns: 1fr;
  --page-hero-overlap: 0px;
}
```

### Modifiers

- `.page-hero--single`: single-column layout capped around 700px.
- `.page-hero--split`: two columns (`minmax(0, 1fr) clamp(60px, 24vw, var(--page-hero-sidebar-max))`) with a default 40px overlap.

The wrapper is a three-column CSS grid (`edge / content / edge`), so the content rail shares the same 1200px maximum width as the navigation container and the hero block sits flush with the top of the page.

## 3. PHP Integration

`View/Elements/layout/body_masthead.ctp` renders the component for every interior layout:

- Reads `$banner` data to populate the optional background `<picture>` using the same responsive sources as the homepage banner.
- Pulls CTA content from page metadata (`banner_header`, `banner_summary`, `banner_cta`, `banner_cta_link`).
- Applies hero modifiers automatically based on available metadata.

## 4. Managing Intro Copy & Overlap

Layouts wrap `$pageIntro` in `.layout-rail`, keeping copy aligned with the hero rail:

```php
if (!empty($pageIntro)) {
    echo $this->Html->div('layout-rail', $pageIntro);
}
```

```scss
.layout-rail {
  max-width: min(1200px, calc(100% - 2 * var(--page-hero-inline)));
  margin: 0 auto;
  padding-inline: var(--page-hero-inline);
}
```

When the hero uses a non-zero overlap (`--page-hero-overlap`), downstream sections should add matching `padding-top` or `margin-top` to avoid content collisions.

## 5. Common Configurations

### A. Standard Internal Page

- Layout: `default`
- Hero classes: `page-hero page-hero--single`
- Optional metadata: `banner_header` (eyebrow), `banner_summary` (body copy), `banner_cta` + `banner_cta_link` (primary CTA), `banner_cta_secondary` + `banner_cta_secondary_link` (secondary CTA)

### B. Home Page

- Layout: `home`
- Uses the legacy `.banner` component unchanged to preserve the existing homepage hero.

## 6. Extending the System

1. Add a modifier class to `.page-hero` and override the relevant custom properties (`--page-hero-padding`, `--page-hero-columns`, etc.).
2. Push the modifier into `$pageHeroClasses` inside `body_masthead.ctp` based on controller logic or page metadata.
3. Extend the template if you need extra slots (e.g., stats, breadcrumbs, secondary CTAs).

## 7. Troubleshooting

- **Intro text too wide** – always emit it inside `.layout-rail`.
- **Hero collides with the nav** – adjust `--nav-offset` if the navigation height changes.
- **Portrait doesn’t “hang”** – ensure the hero has `page-hero--split`; that modifier sets a non-zero `--page-hero-overlap` and sidebar column.

---

Use this guide as the canonical reference when adjusting hero spacing or adding new hero variants. Concentrating layout logic in CSS variables and routing content through `body_masthead.ctp` keeps every interior masthead visually consistent.

