# Publishing Contract Matrix

Last reviewed: 2026-02-24

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

| Section | Primitive | Data Contract (Keys/Fields) | Type | Required | Validation/Notes | Admin Source | Render Path | Status | Why |
|---|---|---|---|---|---|---|---|---|---|
| Header notice strip | Settings + element | `HeaderNotice.display_header_notice` | boolean | Optional | `1` renders `header-notice`; `0` hides | Settings | `View/Elements/layout/nav.ctp` -> `header-notice` | Active | Site-wide optional notice behavior |
| Header phone | Settings | `Site.Contact.phone` (fallback `Site.Contact.toll_free`) | text/phone | Optional | Sanitized to `tel:` digits for href | Settings | `View/Elements/layout/nav.ctp` | Active | Global contact info |
| Header CTA | Settings | `HeaderNotice.link`, `HeaderNotice.link_text` | URL + text | Optional | Falls back to `/contact` and `Apply Online →` when empty | Settings | `View/Elements/layout/nav.ctp` | Active | Global CTA in site chrome |
| Main nav | Navigation | Menu `id=1` | menu ref | Required | Must exist/publish for expected header IA | Navigation admin | `View/Elements/layout/nav.ctp` | Active | Centralized IA/menu management |
| Hero image | Page media + settings fallback | Page banner image (`Image` group), fallback `Site.common_head_image` | media + setting | Required | If page banner missing, `Site.common_head_image` used | Page Images tab + Settings | `View/Elements/layout/home_masthead.ctp` | Active | Page-owned hero asset with global fallback |
| Hero eyebrow/badge | Settings/Page | `Site.service_area`, fallback `banner_header` | text | Optional | Service-area setting overrides page eyebrow | Settings + Page fields | `View/Elements/layout/home_masthead.ctp` | Active | Supports global badge or page-specific eyebrow |
| Hero tagline/subtitle | Page fields/custom fields | `masthead_tagline` / `page_masthead_tagline`, `page_subtitle`, `banner_summary` | HTML + text | Optional | Tagline accepts HTML; subtitle fallback chain applies | Page fields + custom fields | `View/Elements/layout/home_masthead.ctp` | Active | Page-owned marketing copy |
| Hero CTA row | Navigation or page fields | Primary: Nav `id=2`; fallback: `banner_cta`, `banner_cta_link`, `banner_cta_secondary`, `banner_cta_secondary_link` | menu ref or field pair(s) | Optional | Nav `id=2` overrides field CTA buttons when present | Navigation admin + Page fields | `View/Elements/layout/home_masthead.ctp` | Active | Nav-driven CTA set when available, field-driven fallback |
| Industries section intro | Page fields | `home_industries_heading`, `home_industries_body`, `home_industries_cta_text`, `home_industries_cta_link` | text + URL | Recommended | No hardcoded default copy injected by template | Home page fields/custom fields | `View/Layouts/home.ctp` -> `View/Elements/home/industries_served.ctp` | Active | Editor-owned section copy, no hardcoded fallback copy |
| Industries item grid | Prototype | Instance select: `home_industries_instance_id` or `home_industries_instance_slug`; item fields: `heading`/`name`/`title`, `cta_link`/`url`, image | prototype ref + item fields | Required for section | If item link empty, tile renders non-clickable; image strongly expected | Prototype admin + Home page field for instance binding | `View/Layouts/home.ctp` -> `View/Elements/home/industries_served.ctp` | Active | Structured repeatable card data |
| Process section intro | Page fields | `home_process_heading`, `home_process_body`, `home_process_cta_text`, `home_process_cta_link` | heading text + RTE HTML + URL | Recommended | `home_process_body` renders as rich text (RTE), no hardcoded defaults | Home page fields/custom fields | `View/Layouts/home.ctp` -> `View/Elements/home/process_steps.ctp` | Active | Editor-owned section framing copy |
| Process step list | Prototype | Instance select: `home_process_instance_id` or `home_process_instance_slug`; item fields: `name`, `description`, `icon_file` (preferred), image (fallback) | prototype ref + item fields | Required for section | Step number is derived from rank order; `icon_file` resolves from `/img/home-process-icons/` | Prototype admin + Home page field for instance binding | `View/Layouts/home.ctp` -> `View/Elements/home/process_steps.ctp` | Active | Structured repeatable process cards |
| Featured + success story panels | Prototype | Instance select: `home_stories_instance_id` or `home_stories_instance_slug`; item fields: `name`, `kicker`, `description`, `cta_text`, `cta_link`, image | prototype ref + item fields | Required for section | Rank 1 renders featured panel, rank 2 renders success panel | Prototype admin + Home page field for instance binding | `View/Layouts/home.ctp` -> `View/Elements/home/story_panels.ctp` | Active | Managed editorial story rows without hardcoded content |
| Testimonials intro | Page fields | `home_testimonials_heading`, `home_testimonials_body`, `home_testimonials_cta_text`, `home_testimonials_cta_link`, `home_testimonials_limit` | text + URL + numeric | Recommended | `home_testimonials_limit` defaults to 2 if empty/invalid | Home page fields/custom fields | `View/Layouts/home.ctp` -> `View/Elements/home/why_testimonials.ctp` | Active | Editor-owned testimonial framing copy and card count |
| Testimonials cards | Prototype | Instance select: `home_testimonials_instance_id` or `home_testimonials_instance_slug`; item fields: `testimonial`, `byline`/`name`, `rating` | prototype ref + item fields | Required for section | `rating` clamped to 1-5; defaults to 5 | Prototype admin + Home page field for instance binding | `View/Layouts/home.ctp` -> `View/Elements/home/why_testimonials.ctp` | Active | Structured testimonial content with rating display |
| Mid-page platter | Content Block by ID | `home_mid_content_block_id`, optional `home_mid_content_block_wrapper_class` | block ID + class | Optional | Block must be published; wrapper class optional | Home page fields + Content Blocks admin | `View/Layouts/home.ctp` -> `View/Elements/home/content_block_by_id.ctp` | Active | Reusable rich content injected at controlled location |
| Main body content | Page content | `Page.content` (`$this->fetch('content')`) | rich text | Optional | Renders standard page body flow | Page editor WYSIWYG | `View/Layouts/home.ctp` | Active | Standard page body stream |
| Bottom CTA platter | Content Block by ID | `home_bottom_cta_block_id` (fallback `home_cta_block_id`), optional `home_bottom_cta_wrapper_class` | block ID + class | Optional | Block must be published; legacy key still supported | Home page fields + Content Blocks admin | `View/Layouts/home.ctp` -> `View/Elements/home/content_block_by_id.ctp` | Active | Reusable CTA/footer-adjacent platter |

## 3. Footer/Global Chrome Matrix (Current Implementation)

| Section | Primitive | Data Contract (Keys/Fields) | Type | Required | Validation/Notes | Admin Source | Render Path | Status | Why |
|---|---|---|---|---|---|---|---|---|---|
| Footer services nav | Navigation | Menu `id=3` | menu ref | Optional | Renders only if menu has items | Navigation admin | `View/Elements/layout/footer.ctp` | Active | Reusable footer services IA |
| Footer company nav | Navigation | Menu `id=4` | menu ref | Optional | Renders only if menu has items | Navigation admin | `View/Elements/layout/footer.ctp` | Active | Reusable footer company IA |
| Footer contact | Settings | `Site.email`, plus site contact values in layout context | settings group | Optional | Rows hide when values are empty | Settings | `View/Elements/layout/footer.ctp` | Active | Centralized contact details |
| Footer portfolio link | Settings | `Site.Footer.portfolio_link` | URL | Optional | Used in attribution link | Settings | `View/Elements/layout/footer.ctp` | Active | Configurable attribution URL |

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
1. Any additional homepage platters/feature rows not yet in active templates.
2. Any interior-page section that introduces new `home_`-style field contracts.

For each new section, add:
1. Primitive decision.
2. Exact field keys.
3. Type.
4. Required.
5. Validation/notes.
6. Admin source.
7. Template render path.
8. Status (`Planned`, `In Progress`, `Active`).

## 6. Automation Checks (Scoped)

Keep checker scope intentionally narrow:
1. `home_` key coverage check:
   - grep for page-field usage patterns in homepage templates,
   - compare discovered `home_` keys against keys listed in this matrix.
2. Hardcoded-copy check:
   - grep homepage layout/section elements for hardcoded copy in editor-owned sections.

Out of scope for this checker:
1. Full semantic parsing of all PHP variable paths.
2. Automatic discovery of every undocumented key across the full app.

## 7. Visual QA + Publish-State Checks

Visual tooling already exists:
1. `npm run visual:capture`
2. `npm run visual:compare`

Contract requirement:
1. Capture/compare must be executed at `1920px` viewport target for homepage sign-off.

Publish-state checklist for visual/functional QA:
1. Prototype instance unpublished: section should not render.
2. Prototype items unpublished/empty: grid should not render items.
3. Content block unpublished/missing ID: block section should not render.
4. Navigation menu empty: section renders without broken chrome/spacing.
5. Missing optional page fields: section remains structurally stable without placeholder junk.
