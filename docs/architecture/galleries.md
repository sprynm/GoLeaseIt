# Galleries Runbook

Last reviewed: 2026-02-24

This document defines how Gallery blocks are inserted, rendered, styled, and debugged in this CMS.

## 1. What Galleries Are

A Gallery is a managed media collection (`Galleries.Gallery`) that editors insert into rich text using a block token.

Use Galleries when:
1. Editors need to manage image sets from admin.
2. The frontend should render a consistent gallery pattern.
3. Lightbox behavior is required (fancybox integration).

Do not use Galleries when:
1. The section is a structured repeatable card/list with non-image data (use Prototypes).
2. You need one-off image placement controlled entirely by template markup.

## 2. Runtime Path (Observed)

Gallery block replacement is handled by:
1. Event registration: `Plugin/Galleries/CorePlugin/Event/CmsGalleriesEventListener.php`
2. Block helper: `Plugin/Galleries/CorePlugin/View/Helper/CmsGalleryBlockHelper.php`
3. Render element: `Plugin/Media/CorePlugin/View/Elements/basic_gallery.ctp`

Replacement flow:
1. Content contains `{{block type="Gallery" id="<id>"}}`.
2. `CmsGalleryBlockHelper::afterRenderFile()` finds the token and resolves the gallery record.
3. Helper renders `Media.basic_gallery` (or an override element if present).
4. Rendered output is cached under key `gallery_<id>` in cache config `block`.

## 3. Insertion Syntax

Canonical token:

```text
{{block type="Gallery" id="1"}}
```

TinyMCE often inserts it wrapped (for example):

```html
<div class="block Gallery">{{block type="Gallery" id="1"}}</div>
```

The wrapper may not survive final frontend output in all pipelines. Do not rely on `.block.Gallery` for critical styling behavior.

## 4. Frontend Markup Contract

Current gallery element contract (`Media.basic_gallery`) outputs:

```html
<div class="c-gallery">
  <div class="gallery">
    <!-- anchors/images -->
  </div>
</div>
```

Key rule:
1. Layout-critical behavior (for example full-bleed interruptor patterns) must target `.c-gallery`, not editor wrappers.

## 5. Override Resolution

`CmsGalleryBlockHelper::_findElement()` resolves element files in this order:
1. `APP/Plugin/Galleries/View/Elements/<gallery-id>.ctp`
2. `APP/Plugin/Galleries/View/Elements/<gallery-type>.ctp`
3. `APP/Plugin/Media/View/Elements/basic_gallery.ctp`
4. `CMS/Plugin/Media/View/Elements/basic_gallery.ctp`

Implication:
1. You can customize one gallery ID, one gallery type, or all galleries globally.

## 6. Styling And Behavior

Base grid styling:
1. `webroot/css/scss/_block-gallery.scss` styles `.gallery` children.

Article full-bleed interruptor behavior:
1. `webroot/css/scss/_block-article.scss` styles `.article-body .c-gallery`.

Lightbox behavior:
1. `basic_gallery.ctp` includes `fancybox` element.
2. Runtime scripts/styles include `jquery.fancybox` + `fancybox-init` per layout/script loading rules.

## 7. Idiosyncrasies (Important)

1. TinyMCE wrapper classes are not a stable frontend contract.
2. Gallery HTML class names come from element templates, not from the token text itself.
3. Cache can hide changes in element/template output until cache refresh.
4. Fancybox dependencies are runtime/layout-dependent; missing includes can make gallery appear uninteractive.

## 8. Way Of Working

1. Keep editor workflow simple: editors insert Gallery blocks via admin/TinyMCE tool only.
2. Put structural wrappers needed for layout behavior in `basic_gallery.ctp` (or its override), not in editor-authored HTML.
3. Use CSS targets based on render contracts (`.c-gallery`, `.gallery`), not ephemeral editor wrappers.
4. If a custom design needs variant markup, create an override element by gallery ID/type instead of inline editor hacks.

## 9. QA Checklist

1. Token renders to gallery HTML (no raw token text on frontend).
2. Gallery images and links resolve to expected thumb/large versions.
3. `.c-gallery` is present where layout behavior depends on it.
4. Fancybox opens and cycles images.
5. Full-bleed/article patterns do not introduce horizontal scroll.

## 10. Troubleshooting

1. Raw token appears:
   - invalid token syntax or helper not running in that render path.
2. Gallery does not render:
   - wrong ID, unpublished/empty gallery, or missing element resolution.
3. Markup not matching expectation:
   - check which element path won in `_findElement()` resolution order.
4. Styling mismatch:
   - ensure CSS targets rendered `.c-gallery`/`.gallery`, not editor wrapper classes.
5. Changes not visible:
   - check `block` cache entries (`gallery_<id>`).
