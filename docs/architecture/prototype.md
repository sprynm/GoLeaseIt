# Prototype System Runbook

Last reviewed: 2026-02-24

This document is the implementation runbook for creating, administering, and using Prototypes for design-driven sections.

It is specific to this repo and the Pyramid/Cake Prototype plugin implementation.

## 1. What A Prototype Is

A Prototype is a repeatable content collection managed in admin and rendered by Prototype templates.

Use Prototypes when content is:
1. Repeated (cards, stories, logos, FAQs, staff, testimonials).
2. Ordered and publishable.
3. Reused on one or more pages/layouts.

Do not use Prototypes for one-off static copy blocks. Use page content or Content Blocks for that.

## 2. Where The System Lives

- Core plugin code: `Plugin/Prototype/CorePlugin/`
- Site override code: `Plugin/Prototype/`
- Core prototype templates: `Plugin/Prototype/CorePlugin/View/<slug>/...`
- Site-level template overrides: `Plugin/Prototype/View/<slug>/...`
- Preconfigured starter definitions: `Plugin/Prototype/CorePlugin/Config/preconfigured.php`

Related runtime wiring:
- Controller base: `Plugin/Prototype/CorePlugin/Controller/CmsPrototypeAppController.php`
- Instance model: `Plugin/Prototype/CorePlugin/Model/CmsPrototypeInstance.php`
- Item model: `Plugin/Prototype/CorePlugin/Model/CmsPrototypeItem.php`
- Helper access (`instanceItems`, assets): `Plugin/Prototype/CorePlugin/View/Helper/CmsPrototypeHelper.php`

## 3. Rendering Lifecycle

1. Admin creates or installs a Prototype Instance.
2. Instance has a slug (for example `industries_served`).
3. Frontend routes for that instance use Prototype controllers (`prototype_instances`, `prototype_items`, `prototype_categories`).
4. Event listener injects instance-specific view paths, in order:
   - `APP/Plugin/Prototype/View/<slug>/`
   - `APP/Plugin/Prototype/CorePlugin/View/<slug>/`
   - `CMS/Plugin/Prototype/View/<slug>/`
5. Cake renders `PrototypeInstances/view.ctp`, `PrototypeItems/view.ctp`, and optional category/search/featured templates.

Implication:
- Site-level overrides in `Plugin/Prototype/View/<slug>/` win over CorePlugin templates.

## 4. Two Ways To Create A Prototype

### 4.1 Install From Starter (Admin Dropdown)

This is the normal and fastest path.

1. Go to Prototype Instances admin index.
2. Pick a starter from the preconfigured dropdown.
3. Install.
4. Configure instance tabs (Basic, Categories, Items).
5. Add items and publish.

Starter source of truth:
- `Plugin/Prototype/CorePlugin/Config/preconfigured.php`

### 4.2 Create A Custom Starter In Code

Use when you need a reusable starter across environments.

1. Add starter definition in `Plugin/Prototype/CorePlugin/Config/preconfigured.php`.
2. Add matching template folder in `Plugin/Prototype/CorePlugin/View/<slug>/` with:
   - `PrototypeInstances/view.ctp`
   - `PrototypeItems/view.ctp`
   - `PrototypeItems/featured.ctp` (if needed)
   - `PrototypeItems/search.ctp` (if needed)
   - `PrototypeCategories/view.ctp` (if categories enabled)
3. Commit and deploy.
4. Install from admin dropdown.

Note:
- The dropdown is driven by `loadPreconfigured()` in `CmsPrototypeInstance`.
- The displayed starter list is not discovered from folders alone.

## 5. Admin Model: What Editors Manage

### 5.1 Instance Level (PrototypeInstance)

Managed in `PrototypeInstances/admin_edit.ctp` across tabs:
- Basic Info
- Categories
- Items
- Image Versions

### 5.2 Item Level (PrototypeItem)

Managed in `PrototypeItems/admin_edit.ctp`.

Always present core fields:
- `name` (label controlled by `name_field_label`)
- `head_title` (super admin visibility)
- `slug` (super admin visibility)

Optional item extras come from instance custom fields (`PrototypeItemField`).

### 5.3 Category Level (PrototypeCategory)

Only when `use_categories = 1`.

## 6. PrototypeInstance Field Reference (Runtime Meaning)

The table below explains how keys under `'PrototypeInstance' => array(...)` are used in runtime behavior.

| Key | Runtime Effect | Where It Is Used |
|---|---|---|
| `name` | Admin label, permission description, model/controller naming context | `CmsPrototypeInstance`, admin views |
| `slug` | Routing/view-path key, instance URL key, asset key | `CmsPrototypeEventListener::onPluginPaths`, `copyDefaultViews`, helper asset loader |
| `head_title` | Browser title fallback for instance pages | `CmsPrototypeInstancesController::view` |
| `override_title_format` | Title formatting override behavior in page-title stack | Consumed by CMS title/meta system |
| `description` | Instance intro content (`pageIntro`) | `CmsPrototypeInstancesController::view`, `CmsPrototypeItemsController::featured/search` |
| `layout` | Layout selection for frontend render | `CmsPrototypeAppController::beforeRender` |
| `allow_instance_view` | Enables instance summary route/view | Instance route + admin usage links |
| `use_categories` | Enables category system | Controllers/helpers/templates |
| `allow_category_views` | Enables category detail routes | `CmsPrototypeCategoriesController::view` |
| `allow_item_views` | Enables item detail routes | `CmsPrototypeItemsController::view` |
| `item_order` | Sort order for item summaries and admin sort behavior | `summaryQuery`, `admin_index` |
| `item_summary_pagination` | Enables paginated summaries | `CmsPrototypeInstancesController::_itemSummary`, `CmsPrototypeCategoriesController::view` |
| `item_summary_pagination_limit` | Page size when pagination is on | Same as above |
| `item_image_type` | `none/single/multiple` image UI + display behavior | Item admin edit + templates |
| `item_document_type` | `none/single/multiple` document UI + display behavior | Item admin edit + templates |
| `category_image_type` | Category image support level | Category admin + category templates |
| `category_document_type` | Category document support level | Category admin + category templates |
| `use_page_banner_images` | Enables instance banner behavior where supported | Instance controller/view behavior |
| `use_page_banner_image_items` | Enables item banner image support | `CmsPrototypeItemsController::view/admin_edit` |
| `use_page_banner_image_categories` | Enables category banner image support | `CmsPrototypeCategoriesController::view/admin_edit` |
| `fallback_to_instance_banner_image` | Fallback to instance image when item/category banner is missing | Item/category controllers |
| `category_changefreq` | Sitemap metadata for categories | sitemap logic |
| `item_changefreq` | Sitemap metadata for items | `CmsPrototypeItem::findForSitemap` |
| `name_field_label` | Relabels item `name` field in admin (for example “Heading”, “Anchor Text”) | `PrototypeItems/admin_edit.ctp` |
| `use_featured_items` | Enables featured item mechanics | event listener + admin |
| `all_items_featured` | Load all items as featured variable | `_loadFeaturedItems` |
| `number_of_featured_items` | Limits featured count when not all | `_loadFeaturedItems` |
| `autoload_featured_items_in_layouts` | Comma list of layouts that auto-inject featured variable | `CmsPrototypeEventListener::_loadFeaturedItems` |
| `public` | Publication visibility control in broader CMS model | Publishable behavior |

## 7. Why Item Forms Sometimes Show “Too Many Required Fields”

Prototype item edit forms combine:
1. Core item fields (`name`, `head_title`, `slug`).
2. Instance custom fields from `PrototypeItemField` definitions.

If a starter defines many required item fields, every item will require them.

For design-card prototypes, keep the contract minimal.

Recommended minimal card contract:
1. Core `name` (label it “Heading” or “Anchor Text” with `name_field_label`).
2. One link field (`cta_link`) as required.
3. Image enabled (`item_image_type = single`).

Everything else optional unless design requires it.

## 8. Design Integration Pattern (System-First)

1. Define data contract from component markup first.
2. Keep instance fields and item custom fields minimal.
3. Build templates in `Plugin/Prototype/View/<slug>/...` for project-specific design.
4. Use shared blocks/utilities first; add `_prototype-<slug>.scss` only when pattern is truly prototype-specific.
5. Render in layouts/elements via:
   - `$this->Prototype->instanceItems($instanceId, $options)`
   - or standard prototype routes/views where appropriate.
6. Keep graceful empty states in page templates.

## 9. Operational Admin Workflow

For each prototype used in design:

1. Install starter from admin.
2. Confirm instance slug and layout.
3. Configure tabs:
   - Categories on/off
   - Item media type
   - Pagination
   - Featured settings
4. Configure custom fields under Item/Category/Instance “Extra Fields”.
5. Add and publish items.
6. Assign images/documents.
7. Order items (manual sort only works when `item_order` uses `PrototypeItem.rank`).
8. Validate frontend output and publishing state.

## 10. Using Prototypes In Homepage/Section Layouts

Pattern in this repo:

1. Resolve instance ID by explicit page field first.
2. Fallback by known slugs.
3. Render an element with instance items.

Example implementation:
- `View/Layouts/home.ctp`
- `View/Elements/home/industries_served.ctp`

## 11. QA Checklist Before Publishing

1. Instance and items are published.
2. Expected fields exist and required validation matches the design contract.
3. Slug in code matches installed instance slug.
4. Images exist for each card item where design expects media.
5. Links resolve.
6. Empty-state behavior is acceptable.
7. No PHP warnings/notices on related pages.

## 12. Troubleshooting

1. Starter not in dropdown:
   - verify `preconfigured.php` entry and deploy.
2. Prototype installs but uses wrong template:
   - verify slug folder path under `Plugin/Prototype/View/<slug>/` or `CorePlugin/View/<slug>/`.
3. Item form has unexpected required fields:
   - check `PrototypeItemField` definitions on the instance.
4. No manual reorder controls:
   - set `item_order = PrototypeItem.rank ASC`.
5. Item/category view 404:
   - confirm `allow_item_views` / `allow_category_views`.
6. Instance content not showing in target section:
   - verify page field bindings and instance ID/slug resolution in the consuming layout.

## 13. Recommended Starter Defaults For Design Cards

Use these defaults unless requirements demand otherwise:

- `use_categories = 0`
- `allow_instance_view = 1`
- `allow_item_views = 0` (turn on only if detail page is needed)
- `item_image_type = single`
- `item_document_type = none`
- `item_summary_pagination = 0`
- `item_order = PrototypeItem.rank ASC`
- `name_field_label = Heading` or `Anchor Text`
- `use_featured_items = 0` (enable only when layout relies on featured variable)

Then add only required item custom fields for that design pattern.

## 14. Working Method (No Hardcoded Content Values)

Use this process for new prototype-driven sections.

### 14.1 Content Source Rule

Do not hardcode display copy in CTP templates for production sections.

All section text, links, and labels must come from one of:
1. Prototype item fields.
2. Prototype instance fields (including Extra Fields).
3. Page fields or Content Blocks (when section-level and not item-level).

Hardcoded strings are allowed only for temporary diagnostics/dev markers and must be removed before release.

### 14.2 Build + Admin Workflow

1. Build/install starter templates under `Plugin/Prototype/CorePlugin/View/<slug>/` and update the preconfigured starter definition.
2. Push/deploy so the starter appears in the admin install dropdown.
3. Install the prototype via admin.
4. Configure and enable required Extra Fields based on the documented field contract.
5. Populate items/content in admin.
6. Sync environment changes/files back to local via FTP.
7. Continue implementation/refinement in:
   - `Plugin/Prototype/View/<slug>/` (project overrides),
   - `View/Elements/` (page/layout composition).

### 14.3 Field Contract First

Before wiring templates, define the minimum field contract in docs:
1. Required fields.
2. Optional fields.
3. Exact field keys used in templates.
4. Where each key is edited in admin.

If a value is meant to be editor-managed, expose a field for it instead of adding a template fallback string.

### 14.4 Post-Sync Reconciliation Checklist (FTP Workflow)

FTP sync is an environment reality for this project. After syncing, reconcile explicitly:
1. Confirm prototype starter files exist in:
   - `Plugin/Prototype/CorePlugin/View/<slug>/...` (starter definition source),
   - `Plugin/Prototype/View/<slug>/...` (site override target, if installed).
2. Confirm installed instance slug in admin matches template slug used in code.
3. Confirm required instance/item custom fields exist and keep expected keys.
4. Confirm instance and items are published.
5. Confirm images/documents are attached where the section contract expects them.
6. Confirm consuming layout/element bindings still resolve:
   - instance ID/slug selectors,
   - content block IDs,
   - page field keys.
7. Clear relevant caches if output appears stale.
8. Run visual QA at 1920px and compare against baseline before sign-off.

## 15. Idiosyncrasies (Observed)

1. Prototype slug controls multiple runtime concerns at once: route identity, template override path, and optional per-instance asset naming.
2. A starter existing in files is not enough; it must be registered in `preconfigured.php` to appear in admin install UI.
3. Installed instances can diverge from code expectations if slug, extra fields, or publish state changes in admin after deployment.
4. FTP-sync workflows can create local/remote drift; always reconcile slug, field keys, and published state before debugging template code.
