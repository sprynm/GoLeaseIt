# Spacing System Adoption Plan

The global spacing tokens are now defined in `webroot/css/scss/stylesheet.scss:21`. This plan documents where each token should be applied and the code locations that need to be updated so the system is consistently adopted.

## Spacing Tokens

| Token | Suggested Use |
| --- | --- |
| `--space-2xs` | Micro gaps (badge stacks, chip spacing, meta rows) |
| `--space-xs` | Tight stacks, headline-to-subhead spacing, button padding on narrow controls |
| `--space-sm` | Small component padding, compact card gutters |
| `--space-md` | Standard vertical rhythm between paragraphs, label + control spacing |
| `--space-md-plus` | Medium padding blocks (card interiors, callouts) |
| `--space-lg` | Section internals, grid column gutters at tablet sizes |
| `--space-lg-plus` | Hero cards, CTA blocks that require extra breathing room |
| `--space-xl` | Page section padding, large stat blocks |
| `--space-2xl` | Section separation, large grid gaps on desktop |
| `--space-3xl` | Oversized callouts, hero offsets, large decorative rings |
| `--space-section` | Bottom padding for major sections |
| `--space-mini` | Icon/text gaps inside compact components |

## Priority Application Areas

1. **Layout wrappers and hero**
   - `webroot/css/scss/stylesheet.scss` defines mixins (`wrapperWide`, `wrapperMedium`, `wrapperNarrow`) that still rely on raw calculations. Audit the vertical padding in `View/Layouts/default.ctp`, `View/Layouts/home.ctp`, and `View/Elements/layout/body_masthead.ctp` and standardise with tokens (`--space-xl`, `--space-lg`).
   - Ensure masthead/hero content uses token-based gaps between headings, summaries, and CTAs.

2. **Global components**
   - **Forms** (`webroot/css/scss/_block-forms.scss`): replace ad-hoc pixel margins with `--space-sm` or `--space-md`.
   - **Prototype blocks**: if/when reintroduced, ensure spacing uses tokens in `webroot/css/scss/_prototype-<slug>.scss`.
   - **Footer + nav spacing**: evaluate the nav padding in `View/Elements/layout/nav.ctp` and footer rules in `webroot/css/scss/_block-footer.scss` to transition to `--space-lg` / `--space-md`.

## Implementation Steps

1. Sweep existing SCSS files (`_base.scss`, `_block-forms.scss`, `_block-content.scss`, `_block-footer.scss`, `_block-nav.scss`) for `margin`/`padding` declarations that match prior values (1rem, 4rem, 10px, 32px) and map them to the closest token.
2. Update view templates if they embed inline spacing attributes; move those values into the SCSS layer using the token system.
3. After refactors, recompile the stylesheet and visually QA key pages: home hero, priority pages, candidate pages, forms, and footer to confirm rhythm consistency.

## Porting Notes for Archived Stash

1. **Forms (`webroot/css/scss/_block-forms.scss`)** - Reapply spacing adjustments (label padding, checkbox margins, search bar spacing) using `var(--space-*)` while keeping typography and color hooks mapped to the active token system.
2. **Prototype partials (`webroot/css/scss/_prototype-*.scss`)** - If reintroduced, apply the shared spacing scale and swap any palette references to the current token set.
3. **Global styles (`webroot/css/scss/_base.scss` + `_block-content.scss`)** - Reintroduce blockquote and layout rhythm tweaks using tokens such as `var(--space-3xl)`.
4. **Token definitions (`webroot/css/scss/stylesheet.scss`)** - Confirm `--space-3xl` remains documented and add examples or comments for oversized callouts that depend on it.
5. Update this document as items are ported so the audit trail stays accurate after the stash is removed.
