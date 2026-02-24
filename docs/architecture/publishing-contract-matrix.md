# Publishing Contract Matrix

Last reviewed: 2026-02-23

This is the working contract sheet for the build.

It maps:
1. How a section is implemented.
2. Where data is edited and rendered.
3. Why the chosen CMS primitive is used.

Use this as the source of truth before adding fields, templates, prototypes, or content blocks.

## 1. Primitive Selection Matrix

| Primitive | How | Where (Admin/Data) | Why |
|---|---|---|---|
| Page fields | Direct scalar values on a page (or page custom fields) | Page editor | Best for single page-specific values (hero title/subtitle/CTA) |
| Content Block | Reusable rich-text fragment by block ID/token | Content Blocks admin (`name`, `content`) | Best for reusable editorial platters/snippets |
| Prototype | Structured repeatable items with media/links | Prototype Instances + Prototype Items admin | Best for card grids/lists where items are ordered/published |
| Custom Plugin | Domain-specific module with own schema/controllers/views | Plugin-specific admin screens | Best for workflow/business logic beyond prototype/content-block scope |

## 2. Homepage Section Matrix (Current Implementation)

| Section | Primitive | Data Contract (Keys/Fields) | Admin Source | Render Path | Status | Why |
|---|---|---|---|---|---|---|
| Header notice strip | Settings + element | `HeaderNotice.display_header_notice` | Settings | `View/Elements/layout/nav.ctp` -> `header-notice` | Active | Site-wide optional notice behavior |
| Header phone | Settings | `Site.Contact.phone` (fallback `Site.Contact.toll_free`) | Settings | `View/Elements/layout/nav.ctp` | Active | Global contact info |
| Header CTA | Settings | `HeaderNotice.link`, `HeaderNotice.link_text` | Settings | `View/Elements/layout/nav.ctp` | Active | Global CTA in site chrome |
| Main nav | Navigation | Menu `id=1` | Navigation admin | `View/Elements/layout/nav.ctp` | Active | Centralized IA/menu management |
| Hero image | Page media + settings fallback | Page banner image (`Image` group), fallback `Site.common_head_image` | Page Images tab + Settings | `View/Elements/layout/home_masthead.ctp` | Active | Page-owned hero asset with global fallback |
| Hero eyebrow/badge | Settings/Page | `Site.service_area`, fallback `banner_header` | Settings + Page fields | `View/Elements/layout/home_masthead.ctp` | Active | Supports global badge or page-specific eyebrow |
| Hero tagline/subtitle | Page fields/custom fields | `masthead_tagline` / `page_masthead_tagline`, `page_subtitle`, `banner_summary` | Page fields + custom fields | `View/Elements/layout/home_masthead.ctp` | Active | Page-owned marketing copy |
| Hero CTA row | Navigation or page fields | Primary: Nav `id=2`; fallback: `banner_cta`, `banner_cta_link`, `banner_cta_secondary`, `banner_cta_secondary_link` | Navigation admin + Page fields | `View/Elements/layout/home_masthead.ctp` | Active | Nav-driven CTA set when available, field-driven fallback |
| Industries section intro | Page fields | `home_industries_heading`, `home_industries_body`, `home_industries_cta_text`, `home_industries_cta_link` | Home page fields/custom fields | `View/Layouts/home.ctp` -> `View/Elements/home/industries_served.ctp` | Active | Editor-owned section copy, no hardcoded fallback copy |
| Industries item grid | Prototype | Instance select: `home_industries_instance_id` or `home_industries_instance_slug`; item fields: `heading`/`name`/`title`, `cta_link`/`url`, image | Prototype admin + Home page field for instance binding | `View/Layouts/home.ctp` -> `View/Elements/home/industries_served.ctp` | Active | Structured repeatable card data |
| Mid-page platter | Content Block by ID | `home_mid_content_block_id`, optional `home_mid_content_block_wrapper_class` | Home page fields + Content Blocks admin | `View/Layouts/home.ctp` -> `View/Elements/home/content_block_by_id.ctp` | Active | Reusable rich content injected at controlled location |
| Main body content | Page content | `Page.content` (`$this->fetch('content')`) | Page editor WYSIWYG | `View/Layouts/home.ctp` | Active | Standard page body stream |
| Bottom CTA platter | Content Block by ID | `home_bottom_cta_block_id` (fallback `home_cta_block_id`), optional `home_bottom_cta_wrapper_class` | Home page fields + Content Blocks admin | `View/Layouts/home.ctp` -> `View/Elements/home/content_block_by_id.ctp` | Active | Reusable CTA/footer-adjacent platter |

## 3. Footer/Global Chrome Matrix (Current Implementation)

| Section | Primitive | Data Contract (Keys/Fields) | Admin Source | Render Path | Status | Why |
|---|---|---|---|---|---|---|
| Footer services nav | Navigation | Menu `id=3` | Navigation admin | `View/Elements/layout/footer.ctp` | Active | Reusable footer services IA |
| Footer company nav | Navigation | Menu `id=4` | Navigation admin | `View/Elements/layout/footer.ctp` | Active | Reusable footer company IA |
| Footer contact | Settings | `Site.email`, plus site contact values in layout context | Settings | `View/Elements/layout/footer.ctp` | Active | Centralized contact details |
| Footer portfolio link | Settings | `Site.Footer.portfolio_link` | Settings | `View/Elements/layout/footer.ctp` | Active | Configurable attribution URL |

## 4. Build Rules (Enforced)

| Rule | Contract |
|---|---|
| No hardcoded editor-owned copy | If editors should manage it, it must be field/block/prototype data |
| Layouts orchestrate only | `View/Layouts/home.ctp` binds sections and IDs/slugs, section logic lives in elements |
| Prototypes for structured repeats | Cards/lists/sliders use prototype items, not content blocks |
| Content blocks for rich fragments | Reusable prose/platters use content blocks with wrapper classes in templates |
| Minimal field contracts | Add only required fields to satisfy design and content operations |

## 5. Open Contract Backlog (Rest Of Build)

Define rows in this matrix before implementing each new section:
1. Easy Process steps section.
2. Success Stories section(s).
3. Testimonials strip.
4. Any additional homepage platters/feature rows.

For each new section, add:
1. Primitive decision.
2. Exact field keys.
3. Admin source.
4. Template render path.
5. Status (`Planned`, `In Progress`, `Active`).
