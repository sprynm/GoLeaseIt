# Design Tokens

Source of truth: `webroot/css/scss/_theme.scss`
For the full token inventory, read that file directly — the list here would drift.

---

## Naming Conventions

Tokens follow a `--[category]-[subcategory]-[variant]` pattern:

| Prefix | Category |
|---|---|
| `--color-surface-*` | Background surfaces |
| `--color-ink-*` | Text and foreground |
| `--color-brand-*` | Brand palette |
| `--color-state-*` | UI feedback states (error, success, warning) |
| `--space-*` | Spacing scale (2xs → 2xl) |
| `--step-*` | Fluid type scale (-1 → 5) |
| `--type-*` | Named type roles (body, h1–h4, nav, label) |
| `--radius-*` | Border radius (xs → max) |
| `--shadow-*` | Box shadow levels |
| `--font-*` | Font stacks |
| `--font-weight-*` | Weight values |
| `--[component]-*` | Component geometry (lightbox, article, hero, btn) |

---

## Rules

**1. Never hardcode a value that a token covers.**
If `--space-md` fits, use it. If the value is unique to one element in one file, it may not need a token — but if it appears twice, it does.

**2. Add the token before you write the rule.**
New values go into `_theme.scss` first. Writing the rule first leads to hardcoded values that never get promoted.

**3. Component geometry belongs in `_theme.scss`, not in the block file.**
If a component has tunable dimensions (offsets, sizes, z-index), define them as `--[component]-*` tokens in `_theme.scss` and consume them in the block file. See `_block-lightbox.scss` as the model.

**4. SCSS `$variables` are for build-time constants only.**
Do not use SCSS variables for values that belong in the CSS cascade (sizes, colors, spacing). Those must be CSS custom properties so they can be overridden and inspected at runtime.

**5. For color transparency, compose from tokens — do not inline opacity.**
Prefer `rgb(var(--color-brand-primary-rgb) / var(--alpha-16))` over `rgba(61, 108, 174, 0.16)`.
Prefer `rgb(var(--white-rgb) / var(--alpha-85))` over `rgba(255, 255, 255, 0.85)`.

**6. For hover/active color shifts, use `color-mix()` with a token — not a hardcoded hex.**
`color-mix(in srgb, var(--color-brand-punch) 82%, var(--color-ink-absolute) 18%)` — not `#000`.

**7. The type scale has one entry point: `--step-*` and `--type-*`.**
Do not define parallel type scales in block or prototype files. If a named role is missing (e.g. a new feature heading), add a `--type-*` token in `_theme.scss`.

---

## When to Add a New Token

Add a token when:
- A value is used in more than one rule or file
- A value represents a design decision (not just a calculation result)
- A component has tunable geometry that might need adjustment without touching the block file

Do not add a token for:
- A one-off magic number that belongs in an exception
- A derived value that is just `calc()` of an existing token — write the calc inline
