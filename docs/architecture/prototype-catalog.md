# Prototype Catalog And Migration Status

Last reviewed: 2026-02-17

This document maps core prototype types to their purpose and migration status (template override + modern CSS/JS).

Status keys:
- `Core only`: using CorePlugin templates only.
- `Override`: has site-level override in `Plugin/Prototype/View/<slug>/`.
- `Modern CSS`: has active `_prototype-*.scss` in `webroot/css/scss/stylesheet.scss`.
- `Modern JS`: no jQuery dependency in active frontend path for this prototype.

## Core Prototype Types (`Plugin/Prototype/CorePlugin/View`)

### `document_repositories`
- Function:
  - Renders document repository lists and file links.
- Status:
  - Core only.
  - Modern CSS: no active prototype-specific partial.
  - Modern JS: not evaluated in detail, no dedicated modern script path documented.

### `faq`
- Function:
  - FAQ question/answer lists (accordion-style behavior in core template).
- Status:
  - Core only.
  - Modern CSS partial exists (`_prototype-faq.scss`) but not currently enabled in `stylesheet.scss`.
  - Modern JS: core template behavior should be reviewed before migration declaration.

### `feature_boxes`
- Function:
  - Card/tile-based feature collections with heading, text, image, and CTA fields.
- Status:
  - Core type exists.
  - Site uses custom override slug `service-boxes` for this pattern.
  - Modern CSS: yes (`_prototype-feature-boxes.scss` is active).
  - Modern JS: yes for current usage (no jQuery required in primary path).

### `links`
- Function:
  - Simple list of links with labels/descriptions.
- Status:
  - Core only.
  - Modern CSS: no active prototype-specific partial.
  - Modern JS: no dedicated JS path documented.

### `news`
- Function:
  - News listing and detail pages with date, summary, body, optional media/documents.
- Status:
  - Core only.
  - Modern CSS partial exists (`_prototype-news.scss`) but not currently enabled in `stylesheet.scss`.
  - Modern JS: no dedicated JS path documented.

### `projects`
- Function:
  - Project listings/categories/detail structures.
- Status:
  - Core only.
  - Modern CSS partial exists (`_prototype-projects.scss`) but not currently enabled in `stylesheet.scss`.
  - Modern JS: no dedicated JS path documented.

### `staff`
- Function:
  - Staff profile listings with role/title and description.
- Status:
  - Core only.
  - Modern CSS partial exists (`_prototype-staff.scss`) but not currently enabled in `stylesheet.scss`.
  - Modern JS: no dedicated JS path documented.

### `testimonials`
- Function:
  - Quote/testimonial list rendering.
- Status:
  - Core only.
  - Modern CSS partial exists (`_prototype-testimonials.scss`) but not currently enabled in `stylesheet.scss`.
  - Modern JS: no dedicated JS path documented.

## Site-Level Prototype Overrides (`Plugin/Prototype/View`)

### `service-boxes`
- Purpose:
  - Home/service tile collection.
- Status:
  - Override present.
  - Modern CSS: yes (`_prototype-feature-boxes.scss` active).
  - Modern JS: yes (no jQuery dependency in primary path).

### `our-services`
- Purpose:
  - Services platter/section collection for services page.
- Status:
  - Override present.
  - Modern CSS: yes (`_prototype-our-services.scss` active).
  - Modern JS: yes (no jQuery dependency in primary path).

## Migration Notes
1. A prototype is considered migrated only when:
   - site-level override exists (or core template is explicitly accepted),
   - styling is token-driven and in active modern SCSS path,
   - no jQuery dependency exists in its active render path.
2. For dormant prototypes, keep status `Core only` until activated and verified.
