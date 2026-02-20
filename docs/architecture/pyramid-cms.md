# Pyramid CMS + CakePHP Reference (Observed)

This document summarizes what is currently observable in this repository about the Pyramid CMS stack and its CakePHP integration. It is intentionally scoped to evidence in-repo; items marked as Unknown or TBD should be confirmed against the external CMS core or admin UI.

## Stack Facts (Observed)
- Framework: CakePHP (legacy, PHP 5 era). See `Config/core.php` and `Config/pyramid.php` headers.
- CMS: Radarhill "pyramid" CMS.
- CMS core path: `Config/bootstrap.php` sets `CAKE_CORE_INCLUDE_PATH` to `...\pyramidcms` and `CMS` to `...\V2\lib\Cms\`.
- CMS version: `Configure::write('Cms.version', '2.2.0')` in `Config/bootstrap.php`.
- Admin routing prefix: `Routing.prefixes = array('admin')` in `Config/pyramid.php`.
- Install gating: CMS is considered installed only when `Config/database.php` and `Config/pyramid.php` exist (`Config/core.php`).

## App Structure (Observed)
- Standard Cake app layout: `Config/`, `Controller/`, `Model/`, `View/`, `webroot/`.
- Plugins are first-class modules under `Plugin/` (see list below).
- Templates: `.ctp` files under `View/Layouts/` and `View/Elements/`.
- Styles: SCSS under `webroot/css/scss` compiled to `webroot/css/stylesheet.css`.

## Core Plugins (Observed In Repo)
These are present under `Plugin/` with a CorePlugin + site-level wrapper pattern in several cases.
- `Pages`: page tree, routing, layout selection, page content.
- `Navigation`: menus, nav trees, breadcrumbs, current item detection.
- `Settings`: site settings and admin settings UI.
- `CustomFields`: custom fields tied to models (e.g., `PageField`).
- `ContentBlocks`: reusable WYSIWYG blocks.
- `Media`: attachments (images/documents) used by pages and settings.
- `Metas`: meta tags for pages.
- `EmailForms`: embedded forms and merge fields.
- `Galleries`: media grouping (details not explored here).
- `Prototype`: CMS prototype instances and view overrides.

## Page Rendering (Observed)
### Request flow
- Public page display is handled by `Plugin/Pages/CorePlugin/Controller/CmsPagesController::view()`.
- It resolves the requested path to a page via `Page::findForView($this->request->path)` and sets view vars:
  - `page` (page record)
  - `banner` (page image attachment in group `Image`)
- It also handles:
  - `Site.status` and `Site.maintenance_mode` gating via `Cms::online()` / `Cms::maintenance_mode()`.
  - Page password protection.
  - Email form merge fields (content substitution) via `Page::strReplace()`.

### Layout and partials
- `View/Layouts/default.ctp` is the primary layout entry point for interior pages.
- Layout partials live in `View/Elements/layout/`:
  - `head.ctp` (head tags, styles)
  - `nav.ctp` (primary nav)
  - `body_masthead.ctp` (hero/banner)
  - `footer.ctp` (footer and scripts)

### Page model behavior
The `Page` model (`Plugin/Pages/CorePlugin/Model/CmsPage.php`) uses several behaviors:
- `AppTree` for the page hierarchy (parent/child tree).
- `Sluggable` for `slug` generation.
- `CustomFields.CustomField` + `CustomFields.Expandable` for extra page fields.
- `Metas.MetaTag` for metadata.
- `Versioning.Revision` + `Versioning.SoftDelete` for revision and soft delete.
- `Users.Lockable` for edit locking.

Key routing fields:
- `Page.path` is derived from tree + slugs and updated on save.
- `Page.action_map` is a computed field that concatenates plugin/controller/action/extra.
- `Page.pageLink($id)` links to either the CMS page path or an action map (controller action).

## Navigation (Observed)
- Navigation helpers live in `Plugin/Navigation/CorePlugin/View/Helper/CmsNavigationHelper.php`.
- Common usage: `$this->Navigation->show(1)` to render a menu by ID (see `View/Elements/layout/nav.ctp`).
- The helper supports:
  - Nested menus (`show()` / `_generateTree()`).
  - Flat lists (`showTop()` / `_generateFlatList()`).
  - `showChildren()` for subnav under the current top-level item.
  - `breadcrumbs()` and `breadcrumbsList()`.
- Current page detection supports full URLs, routes, and prototype instances.

## Settings (Observed)
- Settings are read via `$this->Settings->show('Key')` (helper extends `CmsSettingsHelper`).
- `CmsSettingsHelper::show()` checks `Configure::read('Settings.<key>')` first, then DB.
- Settings can be of type `text`, `checkbox`, `wysiwyg`, `image`, or `document`.
  - `image` and `document` return a related `Media.Attachment` entry.
- Defaults are inserted via `Plugin/Settings/CorePlugin/Config/Schema/schema.php` (e.g., `Site.name`, `Site.status`, `Site.Contact.*`).

## Content Blocks (Observed)
- `ContentBlocks` use `CmsContentBlock` (`Plugin/ContentBlocks/CorePlugin/Model/CmsContentBlock.php`).
- They are versioned and publishable (`Versioning.SoftDelete`, `Publishing.Publishable`).
- WYSIWYG integration references content blocks for insertion (`findForTinyMce`).

## Media (Observed)
- Pages and settings attach media via `Media.Attachment`:
  - Pages: `Page hasMany Image` with `group = 'Image'` and `model = 'Page'`.
  - Settings: `CmsSettingsHelper` displays existing `Image` or `Document` attachments.

## Prototype System (Observed)
- Core prototype templates live in `Plugin/Prototype/CorePlugin/View/...`.
- Site-level overrides live in `Plugin/Prototype/View/<slug>/...` and are created when a prototype is installed.
- Guidance in `docs/architecture/system-overview.md`: edit site-level overrides, avoid CorePlugin unless necessary.

## Prototype System (Expanded)

### Admin workflow (observed + inferred from layout)
1. Admin creates a **Prototype Instance** from a core type (or custom prototype).
2. Each instance gets a unique **slug** (required for multiple installs of the same core type).
3. On install, the system creates a site‑level override tree under:
   - `Plugin/Prototype/View/<slug>/...`
4. Rendering uses the site‑level override if it exists; otherwise it falls back to CorePlugin views.

### Where rendering happens
- **Instance summary/list view**: `Plugin/Prototype/*/View/<slug>/PrototypeInstances/view.ctp`
- **Category view** (if categories enabled): `.../PrototypeCategories/view.ctp`
- **Item detail view**: `.../PrototypeItems/view.ctp`
- Shared elements used by core templates live in:
  - `Plugin/Prototype/CorePlugin/View/Elements/` (e.g., `item_summary.ctp`, `category_summary.ctp`, `item_search.ctp`)

### Instance assets (CSS/JS)
`CmsPrototypeHelper::instanceCss()` and `instanceJs()` look for assets named by **instance slug** in:
- `APP/Plugin/Prototype/webroot/css/<slug>.css`
- `APP/Plugin/Prototype/webroot/js/<slug>.js`
(and fall back to CMS core paths if present)

### Core prototype types (how the CorePlugin templates use data)
These are the built‑in types listed in `Plugin/Prototype/CorePlugin/View/`:

- **Document Repositories**  
  File: `document_repositories/PrototypeInstances/view.ctp`  
  Renders a list of items linking to the first `ItemDocument` attachment; prints `footer_text`.

- **FAQ**  
  File: `faq/PrototypeInstances/view.ctp`  
  Renders each item’s `name` as a question and `answer` as the toggle body; uses inline JS to expand/collapse.

- **Feature Boxes**  
  Files:  
  - `feature_boxes/PrototypeInstances/view.ctp` (summary list or category summary)  
  - `feature_boxes/PrototypeCategories/view.ctp`  
  - `feature_boxes/PrototypeItems/view.ctp` (tile‑style card)  
  Item view uses `heading`, `title`, `subheading`, `text/content`, `cta_link_text/cta_text`, `cta_link`, and optional image/caption.

- **Links**  
  File: `links/PrototypeInstances/view.ctp`  
  Lists item name + URL and description.

- **News**  
  Files:  
  - `news/PrototypeInstances/view.ctp` (summary list with image, date, excerpt)  
  - `news/PrototypeItems/view.ctp` (detail with date, description, custom fields, images/documents)  

- **Staff**  
  File: `staff/PrototypeInstances/view.ctp`  
  Lists item `name`, `position`, optional image, and description.

- **Testimonials**  
  File: `testimonials/PrototypeInstances/view.ctp`  
  Renders testimonial text, name, optional byline and link in a blockquote.

### Local overrides in this repo
This repo already has site‑level overrides:
- `Plugin/Prototype/View/feature-boxes/...`
- `Plugin/Prototype/View/service-boxes/...`
These override the CorePlugin templates for those instance slugs.

## Pages: Concept to Rendered Output

### Page concept (admin UI)
At creation time, a page starts with:
- **Page Title**
- **Content (WYSIWYG)**

Common tabs in the editor include:
- **Banner Image**
- **Schema Code**
- **Advanced**
  - Head title override
  - IA placement (parent + URL slug)
  - Layout template
  - Header + footer code additions
- **Metas**
  - Description
  - Application name
  - Robots
  - OG image
- **Super Admin**
  - Advanced routing fields
  - Internal Name
  - Custom Fields

### What Plugin / Controller / Action / Extra do (Observed)
These fields map the page to a **controller action** instead of a standard CMS page path.
In code, `Page.action_map` is computed as:
```
plugin/controller/action/extra
```
When **action_map is present**, `Page::pageLink()` returns a route to that controller action.  
When **action_map is empty**, the page renders the normal CMS path via `PagesController::view`.

Practical meaning:
- **Plugin**: the Cake plugin name that owns the controller.
- **Controller**: the controller name (without `Controller` suffix).
- **Action**: the method to call.
- **Extra**: an optional parameter appended to the route.

This is used to create “pages” that actually run a controller action (e.g., a custom page type) instead of the default WYSIWYG page.

### Internal Name (Observed)
UI note in the admin view indicates that **Internal Name is shown in the admin page index**.  
It is useful for human‑friendly labels that don’t affect the public URL.

### Custom Fields (How they fit)
Custom fields are suitable for **single‑value content** tied to a page (e.g., `page_subtitle` for a hero).
Prototypes are better for **collections** that can appear on multiple pages (e.g., staff lists, testimonials).

### Better ways to keep fields consistent across pages
If you want the same custom fields on every page:
- Use the **Default Fields** admin workflow (`CmsPagesController::admin_default_fields`) to define fields that apply to all pages.
- Use a **shared element** (e.g., `View/Elements/layout/body_masthead.ctp`) and pull from a common field name or site setting.

This avoids adding fields manually per page while still keeping templates consistent.

### Default Page Fields (UI Confirmation)
The **Pages → Manage Default Page Fields** screen is the UI for `admin_default_fields`.
Fields defined here become **defaults for all pages**, and the form supports the same field types shown in the editor
(text, textarea, WYSIWYG, checkbox, radio, select, image, document, read‑only, email, telephone, URL, date, file).

### Banner Image Renditions (Observed)
The banner image attachment generates the following versions (based on current CMS settings). These are the sizes to expect for `<picture>` sources:
- `thumb` — 100x100 (Crop & Fit, `image/jpeg`)
- `banner-lrg` — 1920x700 (Crop & Fit, `image/jpeg`)
- `banner-med` — 1440x700 (Crop & Fit, `image/jpeg`)
- `banner-sm` — 800x450 (Crop & Fit, `image/jpeg`)
- `banner-xsm` — 540x375 (Crop & Fit, `image/jpeg`)
All versions share a background color of `#0f2744`.

### Standard Hero `<picture>` Source Pattern (Production)
Use this source order for homepage hero contexts (`home_masthead.ctp`):

```php
<picture>
  <source srcset="...banner-lrg..." media="(min-width: 1441px)">
  <source srcset="...banner-med..." media="(min-width: 801px)">
  <source srcset="...banner-sm..." media="(min-width: 641px)">
  <source srcset="...banner-xsm...">
  <img src="...banner-lrg..." alt="..." loading="lazy" decoding="async">
</picture>
```

For interior hero contexts (`body_masthead.ctp`), current usage is:

```php
<picture>
  <source srcset="...banner-fhdl..." media="(min-width: 1441px)">
  <source srcset="...banner-med..." media="(min-width: 801px)">
  <source srcset="...banner-sm..." media="(min-width: 641px)">
  <source srcset="...banner-xsm...">
  <img src="...banner-fhdl..." alt="..." loading="lazy" decoding="async">
</picture>
```

Observed hero usage:
- `View/Elements/layout/home_masthead.ctp` (hero background)
- `View/Elements/layout/body_masthead.ctp` (interior hero background)

Feature/service tile usage:
- Components such as `View/Elements/feature_boxes.ctp` may use custom image versions when the layout needs different crop behavior.
- Default policy for general CMS responsive image rendering remains the banner version pattern above unless a component-specific exception is defined.

### Default Site Settings (Built‑In)
The Settings plugin ships with a **default schema**. The following keys are available by default (partial list, see `Plugin/Settings/CorePlugin/Config/Schema/schema.php`):
- `Site.name`, `Site.title_separator`, `Site.common_head_title`
- `Site.status` (Site Online), `Site.maintenance_mode`
- `Site.copyright_name`, `Site.copyright_start_year`
- `Site.email`
- `Site.Contact.name`, `Site.Contact.address`, `Site.Contact.city`, `Site.Contact.province_state`, `Site.Contact.postal_zip`, `Site.Contact.country`, `Site.Contact.phone`, `Site.Contact.toll_free`, `Site.Contact.fax`, `Site.Contact.email`
- `Site.Google.maps_api_key`, `Site.Google.gtm_association_code`
- `Site.Bing.verification_code`
- `Site.SocialMedia.*` (facebook, twitter, instagram, youtube, linkedin)

Use these built‑in keys before adding custom settings.

## Layouts: When, Why, How

### What a layout does
Layouts are the **page‑level wrappers** that assemble shared elements (head, nav, masthead, footer) and define the main structure of the page.  
They live in `View/Layouts/` and are selected per page in the **Advanced → Layout** field.

### Available layouts (observed in this repo)
- `default.ctp` — standard interior pages with optional sub‑navigation.
- `home.ctp` — homepage layout with `home_masthead.ctp` hero and optional feature boxes.
- `contact.ctp` — contact page layout with main content + form.
- `offline.ctp` / `maintenance-mode.ctp` — special system states.

### When you need a new layout
Create a new layout only when the **page‑level structure** differs in a way that cannot be handled by:
- Shared elements (e.g., `layout/body_masthead.ctp`)
- Composition classes in the template
- Optional sections injected via elements

Examples that justify a new layout:
- A page needs a **different wrapper structure** (e.g., no nav, alternate content rail).
- A page needs **different header/footer orchestration** (e.g., microsite landing page).
- A page is a **system state** (offline/maintenance) that should ignore normal chrome.

Examples that do **not** justify a new layout:
- Different hero content or variant (use masthead element + custom fields).
- Section ordering or additions (use elements in the existing layout).
- One‑off styling (use blocks/modifiers).

### How layouts are used
1. In the admin page editor, choose the layout in **Advanced → Layout**.
2. The layout renders:
   - `layout/head`, `layout/nav`, `layout/body_masthead`, and `layout/footer` as needed.
   - A container wrapper (e.g., `.c-frame`, `.c-container`).
   - The page content via `echo $this->fetch('content');`.
3. If the layout expects certain elements or variables (`$pageIntro`, `$banner`, `$pageHeading`), ensure the controller/page data provides them.

### Routing: CMS Page vs Controller Action
```
Page.action_map present?
├─ Yes → route: plugin/controller/action/extra
│       → controller action renders (custom page type)
└─ No  → route: /<page path>
        → PagesController::view renders the CMS page
```

### Plugins for Specialized Collections
The CMS can install **feature plugins** that provide specialized collection types and admin tools beyond standard pages and prototypes. The admin UI includes an “Add Plugins” screen for installing these (examples shown in the UI include Blog, Calendars, Header Slideshow, Products, Property Listings, Rentals, RETS/VREB listings).  
These are separate from Prototypes: plugins typically introduce their own controllers, models, and admin workflows, while prototypes are content collections rendered through the Prototype system.

## Known Unknowns / Need Confirmation
- Where CMS core classes live in `...\pyramidcms\V2\lib\Cms\` (outside this repo).
- How `Settings` and `CustomFields` are surfaced in the admin UI (field groups, UI layout).
- Cache configuration names like `navigation` referenced in helpers (search in core CMS config).
- Whether PHP runtime version and CakePHP minor version are fixed in production.

## Practical Gotchas (Observed)
- Template parse errors can drop users into fallback/maintenance screens; keep CTP logic tight.
- Admin routes rely on prefix `admin_`; ensure actions follow that pattern.
- `Page.path` is auto-maintained; manual changes should go through model saves.

## Related Docs
- `docs/architecture/system-overview.md`
- `docs/architecture/frontend-structure.md`
- `docs/architecture/prototype-catalog.md`
- `docs/design/style-system.md`
