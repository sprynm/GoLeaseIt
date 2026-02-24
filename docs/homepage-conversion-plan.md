# Homepage Conversion Plan (Design System -> CMS Components)

## Scope
- Convert the homepage from static preview into reusable design-system + CMS-driven implementation.
- Use homepage as the baseline pattern for the rest of the site.
- Prioritize system reuse over one-off styling.

## Delivery Rules
- Prefer existing tokens/vars/utilities; do not add new ones for minor visual deltas.
- Round visual values to existing scale where possible.
- Add bespoke components sparingly and only when patterns cannot be composed from existing blocks/utilities.
- Every new homepage element must appear in the style guide with a representative example.

## Phase 0: Baseline + Mapping
### Tasks
1. Freeze a baseline screenshot set of current homepage states (desktop/tablet/mobile).
2. Map source-of-truth files for homepage rendering:
   - `View/Layouts/home.ctp`
   - `View/Elements/layout/nav.ctp`
   - `View/Elements/layout/home_masthead.ctp`
   - `View/Elements/layout/footer.ctp`
3. Inventory existing homepage-ready prototype/content assets:
   - Prototype: `Plugin/Prototype/View/industries_served/...`
   - Bottom CTA content block (existing, by admin ID).

### Tests
1. `npm run css:build` passes.
2. Visual baseline capture exists for 390/834/1920 widths.
3. No PHP rendering errors on homepage.

## Phase 1: Tokens, Vars, Utilities Hardening
### Tasks
1. Audit current token usage in homepage-related SCSS (`_theme.scss`, `_utilities.scss`, `_prototype-home.scss`, header/nav/button blocks).
2. Normalize values to existing token scale:
   - typography (h1-h4/body/nav/labels),
   - spacing,
   - radius,
   - colors,
   - elevation.
3. Add new tokens only when there is repeated need (>=3 real usages) and no acceptable existing value.
4. Ensure utility classes cover repeated layout patterns required by homepage sections.

### Tests
1. Build passes: `npm run css:build`.
2. Token diff review: every new token has documented rationale and usage count.
3. Home preview at 1920 shows no major spacing/typography regressions against approved comp.

## Phase 2: Componentization (System-first)
### Tasks
1. Convert homepage sections into reusable component patterns:
   - Header + nav + header CTA,
   - Hero,
   - Industry grid,
   - Process steps,
   - Split story blocks (media/text and text/media),
   - Testimonial cards,
   - CTA band/platter,
   - Footer.
2. Keep section-specific styles in scoped files (`_prototype-home.scss`) and elevate reusable pieces into shared block/component files only when reused.
3. Reduce duplicate section-specific selectors through utility + composition patterns.

### Tests
1. Component-level visual checks at 390/834/1920.
2. Keyboard navigation and focus visibility pass for all CTAs and nav interactions.
3. Heading structure check: single `h1`, logical section heading flow.

## Phase 3: Style Guide Coverage
### Tasks
1. Update style guide pages to demonstrate all homepage elements and states:
   - default,
   - hover/focus,
   - dark/light surfaces,
   - compact/mobile variants.
2. Add examples for each reusable component and relevant utility combos.
3. Keep full-page preview aligned with component output.

### Tests
1. Style guide renders without missing assets/classes.
2. Every homepage element has a corresponding style-guide example.
3. Visual spot-check against approved design slices.

## Phase 4: CMS Integration (Prototypes + Content Blocks)
### Tasks
1. Finalize and wire existing prototype:
   - `industries_served` (already created).
2. Finalize and wire existing bottom CTA content block (already created).
3. Create additional prototypes/content blocks only when needed by repeatable content sections.
4. Ensure CTP/RTE integration supports ID-based embedding and graceful empty states.
5. Align prototype item fields (heading, text, image, CTA text/link) with component expectations.

### Tests
1. Prototype renders correctly with 0, 1, and N items.
2. Missing image/text/CTA states degrade gracefully.
3. Content block render by ID works in both CTP and RTE contexts.

## Phase 5: Homepage Layout Assembly (CTP Flow)
### Tasks
1. Update homepage flow in CTP/layout files to match approved section order:
   - masthead/hero,
   - industries,
   - process,
   - featured/success content,
   - testimonials,
   - bottom CTA platter,
   - footer.
2. Keep section rendering data-driven from Page fields, Prototypes, Nav menus, and Content Blocks.
3. Preserve backward compatibility for existing fields where feasible.

### Tests
1. Homepage renders end-to-end with real CMS data.
2. No PHP notices/warnings when optional fields are blank.
3. CTA links/nav links resolve correctly.

## Phase 6: QA + Regression Gates
### Tasks
1. Establish homepage QA checklist (visual, functional, content integrity).
2. Add repeatable visual regression run for key breakpoints.
3. Validate responsive behavior (layout shifts, nav drawer, CTA stacking).
4. Validate performance and accessibility basics.

### Tests
1. Visual compare (baseline vs candidate) at 390/834/1920.
2. Lighthouse spot-check (home): no critical accessibility/performance failures.
3. Manual CMS-edit cycle test:
   - update prototype item,
   - update content block,
   - update page field,
   - confirm frontend updates correctly.

## Phase 7: Rollout Pattern for Remaining Pages
### Tasks
1. Capture reusable homepage-derived patterns as migration templates.
2. Apply same sequence (tokens -> components -> style guide -> CMS binding -> layout assembly) to remaining templates.
3. Maintain a per-page parity checklist.

### Tests
1. Each migrated page passes the same visual + functional gate set.
2. Shared components remain consistent across templates.

---

## Homepage Implementation Backlog (Immediate)
1. Wire `View/Elements/layout/nav.ctp` to finalized header CTA structure (phone + apply in header CTA, not notice bar).
2. Wire homepage section sequence in `View/Layouts/home.ctp` using:
   - `industries_served` prototype for industries,
   - bottom CTA content block by ID.
3. Ensure `View/Elements/layout/home_masthead.ctp` maps cleanly to approved hero design and CTA/nav source.
4. Update style guide demonstrations for each homepage section and variation.

## Admin Setup Checklist (What you need to configure)
1. **Page fields (home page)**:
   - Hero tagline/subtitle/summary/CTA fields used by `home_masthead`.
   - Any additional section headings/copy fields chosen in implementation.
2. **Navigation menus**:
   - Main nav menu (`Navigation->show(1)`),
   - Hero CTA nav (`Navigation->show(2)`) if used,
   - Footer service/company menus (`Navigation->show(3)` and `show(4)`).
3. **Prototypes**:
   - Confirm `industries_served` instance exists, published, ordered, and populated.
   - Add item images/heading/copy/CTA fields per card.
4. **Content blocks**:
   - Confirm bottom CTA platter block exists and is published.
   - Capture its block ID for CTP/RTE embedding.
5. **Page content placement**:
   - If sections are RTE-driven, insert block shortcodes by ID.
   - If section is CTP-driven, bind IDs in template config/variables.
6. **Publishing checks**:
   - Verify published flags on page, prototype instance/items, and content block.
   - Clear cache after structural content updates where needed.

