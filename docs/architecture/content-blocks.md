# Content Blocks Runbook

Last reviewed: 2026-02-24

This document defines how Content Blocks work in this CMS and how to use them safely in design/system work.

## 1. What Content Blocks Are

A Content Block is a single reusable WYSIWYG content record (`name` + `content`) managed in admin and injected into rendered output.

Use Content Blocks when:
1. The content is a reusable rich-text fragment.
2. Editors need to update content without code deploys.
3. You want to place the same block in multiple pages/sections.

Do not use Content Blocks when you need repeatable structured items (cards, stories, staff rows). Use Prototypes for that.

## 2. Content Blocks vs Prototypes

| Capability | Content Blocks | Prototypes |
|---|---|---|
| Data shape | Single rich-text record | Instance + repeatable items (+ optional categories) |
| View templates by slug | No | Yes (`Plugin/Prototype/View/<slug>/...`) |
| Item-level fields | No | Yes (custom fields per item/category/instance) |
| Best for | Reusable text sections / CTA snippets | Structured repeatable collections |
| Styling control | Through surrounding layout/classes and CSS | Through dedicated CTP templates + CSS |

## 3. Runtime Behavior (Observed)

The Content Blocks plugin is wired via:
1. Model: `Plugin/ContentBlocks/CorePlugin/Model/CmsContentBlock.php`
2. Helper: `Plugin/ContentBlocks/CorePlugin/View/Helper/CmsContentBlockHelper.php`
3. Base block parser: `CoreFiles/View/Helper/CmsBlockHelper.php`

Replacement flow:
1. Rendered page content is scanned for block placeholders.
2. Matching placeholder tokens are replaced with the block `content`.
3. Results are cached using cache key `content_block_<id>` in cache config `block`.

Important:
1. Replacement does not run on admin requests.
2. Block content is output as stored HTML (not escaped).

## 4. Injection Syntax

Canonical token syntax:

```text
{{block type="ContentBlock" id="1"}}
```

Rules:
1. Use `block` in lowercase.
2. `type` must be `ContentBlock`.
3. Use double quotes around attributes.
4. `id` should be the numeric block ID from admin.

The parser also tolerates WYSIWYG wrappers when TinyMCE wraps the token in:
1. `<p class="block...">...</p>`
2. `<span class="block...">...</span>`
3. `<div class="block...">...</div>`

## 5. Where You Can Inject Blocks

### 5.1 In RTE/WYSIWYG Content

Insert the token directly in page/prototype/content rich text, or use TinyMCE block insertion UI when available.

### 5.2 In CTP Templates

You have two practical patterns:
1. Token-in-markup pattern:
   - Print/include the token string in template output and let the block helper replace it post-render.
2. Direct-fetch pattern:
   - Query by ID and render content inside a controlled wrapper element.
   - Existing example: `View/Elements/home/content_block_by_id.ctp`

Use direct-fetch when the section needs explicit wrapper classes and deterministic layout structure.

## 6. Styling Strategy (Key Limitation)

Content Blocks do not have per-block CTP template folders like Prototypes.

Implications:
1. Do not expect `Plugin/ContentBlocks/View/<slug>/...` style template overrides.
2. Style blocks via:
   - parent section wrappers in CTP/layout elements,
   - reusable CSS components/utilities,
   - semantic classes in block HTML when necessary.

Recommended:
1. Keep block markup clean and semantic.
2. Avoid inline styles in WYSIWYG content.
3. Keep presentation owned by SCSS/CSS, not editor inline formatting.

## 7. Admin Workflow

1. Create/edit block in Content Blocks admin.
2. Set `name` for editor clarity.
3. Enter `content` (WYSIWYG).
4. Publish block (and set schedule if needed).
5. Record block ID in implementation docs where used.
6. Inject by token or by CTP wrapper element.

## 8. QA Checklist

1. Token syntax is exact.
2. Block is published.
3. Block ID is correct.
4. Frontend shows rendered HTML, not raw token.
5. Wrapper classes are applied where layout/styling depends on them.
6. No inline styling regressions from WYSIWYG content.

## 9. Troubleshooting

1. Raw token appears on page:
   - token syntax is wrong, type is wrong, or helper did not process that output path.
2. Block missing:
   - wrong ID, unpublished block, or empty content.
3. Changes do not appear:
   - cached output still active (`content_block_<id>` in `block` cache).
4. Styling is inconsistent:
   - block HTML lacks expected wrapper context/classes.

## 10. Working Method (System Rule)

1. Use Prototypes for structured repeatable content.
2. Use Content Blocks for reusable rich text fragments.
3. Keep content out of hardcoded template strings whenever it is editor-managed.
4. Document each block's purpose, ID, and insertion points in the relevant page runbook.

## 11. Idiosyncrasies (Observed)

1. TinyMCE can wrap block tokens with extra `<p>/<span>/<div class="block...">` markup; do not treat those wrappers as stable frontend contract.
2. Token replacement happens after view rendering; wrapper output can differ from what editors see in source mode.
3. Block output is cached per ID (`content_block_<id>`), which can mask template/content updates during QA.
4. For sections needing strict wrapper classes/layout structure, prefer direct-fetch element rendering over free-form token placement.
