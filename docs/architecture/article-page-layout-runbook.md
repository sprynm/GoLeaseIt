# Article and Generic Page Layout Runbook (Figma Nodes `10902:330` and `10902:161`)

Last reviewed: 2026-02-25

This runbook defines implementation and publishing for both interior-page variants in `Go-Lease-It-Slice`:
1. Node `10902:330` (`Generic Page`) - banner + long-form article layout.
2. Node `10902:161` (`Generic Page - no banner`) - no masthead banner, body-led heading flow.

## 1. Implementation Surfaces

Code surfaces used for these layouts:
1. Runtime layout: `View/Layouts/default.ctp`
2. Hero shell: `View/Elements/layout/body_masthead.ctp`
3. Article styles: `webroot/css/scss/_block-article.scss`
4. CTA styles: `webroot/css/scss/_block-cta-band.scss`
5. Hero tuning: `webroot/css/scss/_block-hero.scss`
6. Gallery base block: `webroot/css/scss/_block-gallery.scss`
7. Lightbox styles + runtime: `webroot/css/scss/_block-lightbox.scss`, `webroot/js/media-lightbox.js`
8. Preview QA pages:
   1. `webroot/style-guide/preview-article-full.html`
   2. `webroot/style-guide/preview-generic-no-banner.html`
9. Living style guide references: `webroot/style-guide/index.html`, `webroot/style-guide/screenshots.html`

## 2. Variant Selection

| Variant | Figma Node | Hero Banner | Body Starts With | Optional Sections |
|---|---|---|---|---|
| Generic article | `10902:330` | Yes (`.page-hero--single`) | `h2` | gallery strip, quote callout, image callout |
| Generic no banner | `10902:161` | No | `h1` | none required beyond CTA |

## 3. Content Model Map

| Section | Source Type | Required | Optional | Notes |
|---|---|---|---|---|
| Hero banner + title | Page fields + banner image | Banner image + title for banner variant | summary, CTA fields | Rendered by `layout/body_masthead.ctp` |
| No-banner title | Page body WYSIWYG | `h1` at top of body | none | Uses `.article-layout--no-banner` + `.article-body h1` rhythm |
| Intro/body copy | Page body WYSIWYG | headings + paragraphs | list blocks, dividers | Styled by `.article-body` rules |
| Gallery strip | Gallery block token | no | image captions | Use only when needed for article variant |
| Testimonial callout | Content block or inline HTML | no | quote body + attribution | `.article-split--quote` + `.article-callout--quote` |
| Text + image callout | Inline body HTML + image | no | image + alt + extra copy | `.article-split--media` + `.article-callout--media` |
| Bottom CTA band | Layout-level CTA | yes | Site settings overrides | Rendered in `View/Layouts/default.ctp` |

## 4. Block/Token/Prototype Decisions

Use this decision table for interior pages:
1. Use page body WYSIWYG for editorial text and section headings.
2. Use Gallery block token only when media strip behavior is needed.
3. Use Content Block when quote/callout text must be reused across multiple pages.
4. Use Prototype only for repeatable related-content modules.

Prototype requirement for both base variants:
1. No prototype is required for base article/no-banner layouts.
2. Optional module: `news` prototype for "Related Articles" cards below article body.

## 5. Authoring Contract (Exact Markup Patterns)

### 5.1 No-Banner Generic Body Contract

```html
<div id="content" class="site-wrapper site-wrapper--default article-layout article-layout--no-banner">
  <div class="c-container c-container--article c-region">
    <article class="article-body article-layout__body">
      <h1>Success Stories</h1>
      <h2>Heading 2</h2>
      <p>...</p>
      <h3>Heading 3</h3>
      <p>...</p>
    </article>
  </div>

  <section class="cta-band cta-band--article">...</section>
</div>
```

### 5.2 Gallery Insert In WYSIWYG

```text
{{block type="Gallery" id="123"}}
```

### 5.3 Quote Split Block

```html
<section class="article-split article-split--quote">
  <h3>Heading 3</h3>
  <aside class="article-callout article-callout--quote">
    <blockquote class="article-quote">
      <div class="article-quote__rating" aria-label="5 out of 5 stars">★★★★★</div>
      <p class="article-quote__text">Quote text...</p>
      <p class="article-quote__attr">- Attribution</p>
    </blockquote>
  </aside>
  <p>Body copy that should wrap around the callout...</p>
  <p>Continuation paragraph...</p>
</section>
```

### 5.4 Text + Image Split Block

```html
<section class="article-split article-split--media">
  <h2>Heading 2</h2>
  <aside class="article-callout article-callout--media">
    <figure class="article-media-card">
      <a href="/media/filter/large/img/example.jpg" data-lightbox='type:image;group:article-callout;caption:"Descriptive caption"'>
        <img class="article-inline-image" src="/media/filter/large/img/example.jpg" alt="Descriptive alt text">
      </a>
    </figure>
  </aside>
  <p>Body copy that wraps around the image callout...</p>
</section>
```

### 5.5 Declarative Lightbox Contract

```html
<a href="/media/filter/large/img/example.jpg"
   data-lightbox='type:image;group:article-gallery;caption:"Commercial truck on a mountain roadway"'>
  <img src="/media/filter/thumb/img/example.jpg" alt="Commercial truck on a mountain roadway">
</a>
```

Supported forms:
1. Key/value string: `type:image;group:article-gallery;caption:"..."`
2. JSON object string: `{"type":"image","group":"article-gallery","caption":"..."}`

Runtime:
1. `media-lightbox.js` parses both formats.
2. Grouped items share next/previous navigation by `group` key.

## 6. Site Settings Used By Layout CTA

`View/Layouts/default.ctp` checks these Site settings first, then falls back:
1. `Site.article_cta_heading`
2. `Site.article_cta_body`
3. `Site.article_cta_link`
4. `Site.article_cta_text`

Fallback output:
1. Heading: `Ready to Move Forward?`
2. Body: equipment-financing support copy
3. Link: `/contact`
4. Button text: `APPLY ONLINE ->`

## 7. Preview and QA Surfaces

Primary parity previews:
1. `webroot/style-guide/preview-article-full.html` (node `10902:330`)
2. `webroot/style-guide/preview-generic-no-banner.html` (node `10902:161`)

Support previews:
1. `webroot/style-guide/preview-article-body.html`
2. `webroot/style-guide/screenshots.html` (iframe breakpoint QA)

## 8. Build and Validation Steps

1. `npm run css:build`
2. `npm run preview:check`
3. `npm run visual:capture`
4. `npm run visual:compare` (when baseline is present)
5. `node tools/check-ctp-balance.cjs`
6. `php -l View/Layouts/default.ctp`

## 9. Accessibility Notes

1. Keep heading order sequential (`h1` then `h2` and lower).
2. Ensure all media have meaningful `alt` text.
3. Preserve visible focus states for gallery links and CTA button.
4. Do not encode quote attribution only by color; keep text label (`- Name`).

## 10. When To Introduce A Prototype

Create/use a prototype only if interior pages need a repeatable structured module such as:
1. Related articles cards
2. Reusable testimonial collection below article content
3. Repeating equipment cards with image/title/link

If one-off on a single page, keep the section in page body plus gallery/content-block tokens.
