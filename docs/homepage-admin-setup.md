# Homepage Admin Setup (Current Implementation)

This runbook matches the current homepage template wiring in:
- `View/Layouts/home.ctp`
- `View/Elements/layout/nav.ctp`
- `View/Elements/layout/home_masthead.ctp`
- `View/Elements/home/industries_served.ctp`
- `View/Elements/home/content_block_by_id.ctp`

## 1. Homepage Page Fields
Open the Home page in admin and set these fields.

### Hero/Masthead (already wired)
- `banner_header`
- `banner_summary`
- `banner_cta`
- `banner_cta_link`
- `banner_cta_secondary`
- `banner_cta_secondary_link`
- Custom field fallbacks already supported:
  - `masthead_tagline` (or `page_masthead_tagline`)
  - `page_subtitle`

### Industries section (new wiring)
- `home_industries_heading`
- `home_industries_body`
- `home_industries_cta_text`
- `home_industries_cta_link`
- Optional prototype selector overrides (for testing/switching):
  - `home_industries_instance_id` (numeric instance ID; highest priority)
  - `home_industries_instance_slug` (slug string, e.g. `industries-we-serve`)

No hardcoded default copy is injected for industries intro values. Populate these fields in admin.

### Mid-page content block (new wiring)
- `home_mid_content_block_id`
- Optional wrapper class:
  - `home_mid_content_block_wrapper_class`

### Process section (new wiring)
- `home_process_instance_id`
- `home_process_instance_slug`
- `home_process_heading`
- `home_process_body` (RTE/HTML)
- `home_process_cta_text`
- `home_process_cta_link`

### Stories section (new wiring)
- `home_stories_instance_id`
- `home_stories_instance_slug`

### Testimonials section (new wiring)
- `home_testimonials_instance_id`
- `home_testimonials_instance_slug`
- `home_testimonials_heading`
- `home_testimonials_body`
- `home_testimonials_cta_text`
- `home_testimonials_cta_link`
- `home_testimonials_limit`

### Bottom CTA platter (new wiring)
- `home_bottom_cta_block_id`
  - fallback key supported: `home_cta_block_id`
- Optional wrapper class:
  - `home_bottom_cta_wrapper_class`

## 2. Navigation Menus
Confirm these menus exist and are assigned:

1. Main header nav: `Navigation->show(1)`  
2. Hero CTA nav (optional): `Navigation->show(2)`  
3. Footer services: `Navigation->show(3)`  
4. Footer company: `Navigation->show(4)`

## 3. Header CTA Settings
The header right-side CTA block uses:
- phone: `Site.Contact.phone` (fallback `Site.Contact.toll_free`)
- apply button URL/text:
  - `HeaderNotice.link`
  - `HeaderNotice.link_text`

If `HeaderNotice.link` is empty, template falls back to `/contact`.
If `HeaderNotice.link_text` is empty, template falls back to `Apply Online →`.

## 4. Prototype: Industries Served
You already created this prototype. Confirm:

1. Prototype instance slug is either:
   - `industries_served` (preferred), or
   - `industries-served`
2. Instance is published/not deleted.
3. Items are published and ranked.
4. Each item has:
   - heading/name,
   - image,
   - `cta_link` (recommended so card is clickable).

## 5. Content Block: Bottom CTA Platter
You already created this content block. Confirm:

1. Content Block is published.
2. Capture its numeric ID.
3. Put that ID into Home page field `home_bottom_cta_block_id`.

## 6. Homepage Body Content
Current home layout still renders normal page body content (`$this->fetch('content')`) between industries and bottom CTA.

Use this body area for sections not yet moved to dedicated prototype/content-block wiring.

## 7. New Prototype Starters To Install
From Prototype Instances admin dropdown:

1. `Home Process Steps` (`home-process-steps`)
2. `Home Stories` (`home-stories`)
3. `Home Testimonials` (`home-testimonials`)

## 8. Recommended Item Counts
1. Home Process Steps: 5 ranked items.
2. Home Stories: 2 ranked items (item 1 = featured panel, item 2 = success panel).
3. Home Testimonials: 2+ ranked items (homepage limit controlled by `home_testimonials_limit`).

## 9. Process Step Icon Picker (File-Based)
Home process steps now support an `icon_file` custom field that renders from:

- `/webroot/img/home-process-icons/`

Starter default options:
1. `computer.svg`
2. `people.svg`
3. `notepad.svg`
4. `money.svg`
5. `handshake.svg`

If your `Home Process Steps` instance was installed before this update, add `icon_file` manually under that instance's Item Extra Fields (type `select`) and reuse the options above.

## 10. Recommended Publish Sequence
1. Save nav menu changes.
2. Save global settings (`Site.Contact.*`, `HeaderNotice.*`).
3. Install/populate/save prototype instance/items.
4. Save content block.
5. Save Home page fields.
6. Clear CMS cache if changes do not appear immediately.
