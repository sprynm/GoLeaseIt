# Success Stories Conversion Plan

## Summary
Convert the current homepage `Home Stories` prototype into a Blog-backed `Success Stories` feature set for listing and detail pages, while keeping the homepage itself on the existing Prototype-driven curation model. The homepage will continue to render manually curated `Home Stories` prototype items, with each item linking to the appropriate Success Story article. The public listing and detail views will be restyled to match the current site design system and homepage visual language rather than the legacy blog templates.

## Key Changes
- Reuse `Plugin/Blog` rather than creating a new plugin.
  - Treat Blog as the Success Stories engine by branding the section as `Success Stories` in labels, headings, and route/page context.
  - Keep the Blog schema and admin form intact: title, summary, body, image, categories/tags, publish state, sticky.
- Keep homepage story sourcing in `View/Layouts/home.ctp` and `View/Elements/home/story_panels.ctp`.
  - Preserve the runtime dependency on `home_stories_instance_id` / `home_stories_instance_slug`.
  - Keep manual curation in the `Home Stories` Prototype.
  - Update prototype item links so homepage cards point to the corresponding Success Story article URLs.
  - Preserve graceful states:
    - `0` stories: section does not render.
    - `1` story: render a single platter without broken alternating layout.
    - `2+` stories: preserve alternating media/content pattern.
- Restyle the homepage story platter to align with the rest of the site.
  - Keep the current structural pattern and CSS hooks where possible.
  - Shift copy hierarchy to use the same section/heading/button/surface language already used by the homepage and article system.
  - Ensure story CTA text defaults sensibly, likely `Read Story`, when not explicitly derived from content.
- Update Blog public listing into a branded Success Stories index.
  - Rework the current blog index template so cards/rows visually align with the current homepage system.
  - Use summary + image + category metadata in a cleaner, more editorial layout.
  - Show only published stories and keep pagination behavior.
- Update Blog detail view to use the site’s article layout conventions.
  - Bring it closer to the article/page patterns documented in the repo rather than the legacy blog-detail structure.
  - Put hero/title/body/image/meta into the same spacing and typography system as the rest of the site.
  - Keep category/tag rendering only if it supports the Success Stories experience cleanly; otherwise reduce it.
- Branding and route/page context changes.
  - Configure Blog alias/page heading to read as `Success Stories`.
  - Plan for public URLs to live under a Success Stories section rather than generic Blog wording.
  - Keep the implementation small by using Blog’s existing controller/actions and route structure, with alias/slug-level changes instead of parallel controllers unless routing constraints force it.

## Public Interfaces / Contracts
- Homepage contract:
  - Preserve `home_stories_limit`.
  - Preserve `home_stories_instance_id` and `home_stories_instance_slug`.
  - Homepage source contract remains: manually curated `Home Stories` Prototype items.
- Editorial contract:
  - Story content is authored in Blog posts.
  - Homepage curation remains manual through the `Home Stories` Prototype.
  - Prototype item CTA links should point to the matching Blog story URLs.
  - Categories remain available for industry grouping on Blog posts.
- Public section contract:
  - Listing label/page heading becomes `Success Stories`.
  - Detail pages remain Blog-post-backed records, but present as Success Stories.

## Test Plan
- Data/query behavior:
  - Homepage with `0`, `1`, `2`, and `3+` Prototype items.
  - Homepage cards can point to any published Success Story URL.
  - Listing shows published stories in sticky/date order with pagination intact.
- Rendering/design:
  - Homepage story platter parity at desktop, tablet, and mobile.
  - Listing matches site spacing, buttons, type scale, and surfaces.
  - Detail page matches article rhythm and does not regress CTA/footer behavior.
- Safety/regression:
  - `php -l` on touched templates/controllers/helpers.
  - `npm run css:build`.
  - Homepage, listing, and detail pages render with no PHP notices when image, summary, or categories are missing.
  - Existing testimonials/process/industries sections remain unaffected.

## Assumptions
- `Plugin/Blog` is the intended Success Stories engine.
- The existing Blog admin is functional enough to create/edit/publish posts.
- Homepage curation should remain in the existing `Home Stories` Prototype, not Blog tags, custom fields, or `blog_posts` schema changes.
- Success Stories should be branded as a first-class public section, not exposed as a generic blog.
- Migration of existing prototype story content into Blog entries is a follow-on implementation task, not a separate architecture decision.

## Temporary SEO Hold
- Public Blog-backed Success Stories pages are temporarily marked with `<meta name="robots" content="noindex,follow">`.
- Current implementation point: `Plugin/Blog/CorePlugin/Controller/CmsBlogPostsController::beforeRender()`.
- Removal trigger: remove this once the final Success Stories IA/URL decision is implemented and the section is ready for indexing.
- Intended scope: public Blog listing, archive, category/tag, and detail pages only; admin pages are excluded.

## Homepage Curation
- Homepage story order remains controlled manually by Prototype item rank.
- `sticky` remains a blog-listing control only and should not be reused for homepage promotion.
