# Homepage Build Execution Plan (Design Slice -> Production)

Last updated: 2026-02-24

## Goal

Implement a homepage that matches the visual sequence in:

- `webroot/style-guide/assets/design-slice/page-01.png`
- `webroot/style-guide/assets/design-slice/page-02.png`
- `webroot/style-guide/assets/design-slice/page-03.png`
- `webroot/style-guide/assets/design-slice/page-04.png`
- `webroot/style-guide/assets/design-slice/page-05.png`

using CMS-managed content (Prototypes + Page Fields + Content Blocks), with minimal hardcoded copy.

## Executed In This Pass

### New homepage render elements (CTP)

1. `View/Elements/home/process_steps.ctp`
2. `View/Elements/home/story_panels.ctp`
3. `View/Elements/home/why_testimonials.ctp`

### Homepage layout wiring

- Updated `View/Layouts/home.ctp` to resolve and render:
1. Industries prototype section (existing path, retained)
2. Process steps prototype section
3. Story panels prototype section
4. Why + testimonials prototype section
5. Mid content block (existing path, retained)
6. Legacy body content (existing path, retained)
7. Bottom CTA content block (existing path, retained)

### Installable prototype starters added

Updated `Plugin/Prototype/CorePlugin/Config/preconfigured.php` with:

1. `Home Process Steps` (slug: `home-process-steps`)
2. `Home Stories` (slug: `home-stories`)
3. `Home Testimonials` (slug: `home-testimonials`)

## Development Plan

1. Build reusable section CTPs first (done).
2. Wire section lookup by page field override and slug fallback (done).
3. Create installable prototype starters with minimal required field contracts (done).
4. Keep one-off highly editorial sections available through content blocks (retained in layout).
5. Compile and validate CSS/PHP on each iteration.
6. Perform visual QA against `style-guide/full-page-preview.html` and design-slice images.
7. Finalize admin population checklist for content entry.

## Production Plan

1. Deploy code and clear app/cache.
2. In admin, install prototype starters:
   1. `Home Process Steps`
   2. `Home Stories`
   3. `Home Testimonials`
3. Populate prototype content and publish all items.
4. Set homepage page fields for instance IDs/slugs and section intro copy.
5. Configure nav menus (header/footer IDs 1/2/3/4).
6. Configure content block IDs for any remaining one-off sections:
   1. `home_mid_content_block_id`
   2. `home_bottom_cta_block_id`
7. Run regression checks:
   1. Desktop/tablet/mobile visual check
   2. Mobile nav and keyboard nav check
   3. CTA link checks
8. Promote after sign-off.

## Prototype Populate Instructions

### Home Process Steps (`home-process-steps`)

Target: 5 items, ranked in display order.

Required:

1. `name` (Step Title)
2. `description` (Step Description)
3. `icon_file` (preferred; served from `/webroot/img/home-process-icons/`)

Tips:

1. Keep step titles short so `1. <title>` fits one line on desktop.
2. Legacy item image upload is still supported as fallback when `icon_file` is empty.

### Home Stories (`home-stories`)

Target: 2 items, ranked in display order.

Item 1 renders as featured dark panel.
Item 2 renders as success split panel.

Required:

1. `name` (Story Title)
2. `description` (Story Body)
3. `cta_link`
4. `cta_text`
5. Item image

Optional:

1. `kicker`

### Home Testimonials (`home-testimonials`)

Target: 2+ items.

Required:

1. `testimonial`

Optional:

1. `byline`
2. `rating` (1-5; defaults to 5 if blank)
3. `name` (used as fallback attribution)

## Content Block Outline

Use content blocks for one-off rich content not suited to repeatable prototype items.

1. Mid-page editorial section:
   - `home_mid_content_block_id`
   - optional wrapper: `home_mid_content_block_wrapper_class`
2. Bottom CTA band/platter:
   - `home_bottom_cta_block_id` (fallback: `home_cta_block_id`)
   - optional wrapper: `home_bottom_cta_wrapper_class`

## Navigation Plan

Header/footer navigation stays menu-driven and layout-safe:

1. Main nav: `Navigation->show(1)` in `View/Elements/layout/nav.ctp`
2. Hero CTA nav (optional): `Navigation->show(2)` in `View/Elements/layout/home_masthead.ctp`
3. Footer services: `Navigation->show(3)` in `View/Elements/layout/footer.ctp`
4. Footer company: `Navigation->show(4)` in `View/Elements/layout/footer.ctp`

Mobile + desktop interactions are driven by `webroot/js/navigation-modern.js`.

## Required Page Fields (Home)

### Process section

1. `home_process_instance_id` or `home_process_instance_slug`
2. `home_process_heading`
3. `home_process_body`
4. `home_process_cta_text`
5. `home_process_cta_link`

### Stories section

1. `home_stories_instance_id` or `home_stories_instance_slug`

### Testimonials section

1. `home_testimonials_instance_id` or `home_testimonials_instance_slug`
2. `home_testimonials_heading`
3. `home_testimonials_body`
4. `home_testimonials_cta_text`
5. `home_testimonials_cta_link`
6. `home_testimonials_limit`
