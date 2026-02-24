# Plugins Runbook

Last reviewed: 2026-02-24

This document explains how plugins fit into Pyramid/Cake publishing, what problem they solve, and when to use them instead of Prototypes or Content Blocks.

## 1. What A Plugin Is

A plugin is a self-contained application module under `Plugin/<Name>/` with its own:
1. Controllers/routes.
2. Models/schema.
3. Events/helpers/components.
4. Admin and frontend views.
5. Activation/install behavior.

A plugin is the right tool when you need custom application behavior, not just templated content output.

## 2. Typical Plugin Structure

Common folders/files:
1. `Plugin/<Name>/Config/plugin.json` for plugin metadata.
2. `Plugin/<Name>/Config/routes.php` for route registration.
3. `Plugin/<Name>/Config/events.php` + `Event/<Name>EventListener.php` for CMS event hooks.
4. `Plugin/<Name>/Config/Schema/schema.php` for DB tables.
5. `Plugin/<Name>/Lib/<Name>Activation.php` for activation/install logic.
6. `Plugin/<Name>/Controller/*` and `View/*` for admin/frontend UI.
7. `Plugin/<Name>/Model/*` and behaviors for domain logic.

## 3. What Plugins Are Good For (Publishing Requirements)

Use a custom plugin when requirements include one or more of:
1. A custom editorial workflow (review gates, statuses, approvals).
2. Domain-specific data model with multiple related entities.
3. Complex validation and business rules.
4. External integrations (API sync, feeds, imports/exports).
5. Dedicated admin screens and permissions.
6. Nonstandard frontend routes and controller logic.

In short: plugins solve application-level publishing problems.

## 4. Decision Matrix: Plugin vs Prototype vs Content Block vs Page Fields

Use Page Fields when:
1. You need a few single values tied to one page (hero title, subtitle, CTA).

Use Content Blocks when:
1. You need reusable rich-text fragments.
2. Editors should inject content into RTE or template locations.
3. You do not need repeatable item records.

Use Prototypes when:
1. You need repeatable structured items (cards, stories, staff).
2. You need item ordering/publishing and template control by slug.
3. A section should be editor-driven but still design-structured.

Use a custom Plugin when:
1. You need custom workflow/business logic beyond Prototype/ContentBlock capabilities.
2. You need new domain entities and behaviors with their own lifecycle.

## 5. Current Repo State (Observed)

There is no active standalone custom plugin scaffold in this repo today (for example, no `Plugin/Lance/` application plugin tree).

Practical conclusion:
1. Existing publishing needs should continue to prefer Page fields, Content Blocks, and Prototypes.
2. Introduce a new custom plugin only when requirements exceed those systems (workflow/business logic/data model complexity).

## 6. Production-Ready Plugin Checklist

Before using a custom plugin for live publishing:
1. Define domain model and add schema tables/indexes.
2. Implement model validation and publishability behaviors.
3. Implement admin CRUD with permission checks.
4. Implement frontend read routes and templates.
5. Add activation/install tasks (permissions, defaults, migrations).
6. Add event listeners only where needed.
7. Document data contract and admin workflow.
8. Add QA checks for publish states, access control, and cache behavior.

## 7. Recommended Working Method

1. Start with the smallest viable tool:
   - Page fields, then Content Block, then Prototype.
2. Move to custom plugin only when requirements prove those tools are insufficient.
3. Keep editor-managed content out of hardcoded template strings.
4. Document the ownership of every homepage section (field/block/prototype/plugin).

## 8. Idiosyncrasies (Observed)

1. The label "plugin" is overloaded in this CMS: core feature plugins (Pages/Media/Galleries/etc.) are always present, while custom application plugins are optional.
2. Some behaviors commonly assumed to be "editor output rules" are actually defined in plugin view helpers/elements (for example block replacement helpers).
3. Event-listener wiring is a critical part of plugin behavior; missing events can make a plugin appear installed but functionally inert.

## 9. Homepage Guidance (This Project)

For current homepage needs:
1. Keep section cards/lists in Prototypes.
2. Keep reusable copy platters in Content Blocks.
3. Keep page-specific hero values in Page fields.
4. Introduce Lance only for future requirements that need custom workflow/domain logic.
