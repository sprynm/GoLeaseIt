# Fonts

## What Is Loaded

| Font | File | Role |
|---|---|---|
| Roboto | `Roboto-vf.woff2` | UI, navigation, headings |
| Open Sans | `open-sans-vf.woff2` | Body copy, article text, quotes |

Both are variable fonts covering weight 100–900. Loaded in `_fonts.scss` via `@font-face` with `font-display: swap`.

## Token Entry Points

Use only these tokens — never reference a font family name directly in a block or utility:

- `--font-sans` — Roboto. Default for UI elements, navigation, and headings.
- `--font-display` — Roboto. Identical stack; use for display/heading context to allow future divergence without touching every component.
- `--font-serif` — System serif fallback chain. Used sparingly for decorative or editorial contexts.
- `--font-sans-alt` — Open Sans. For components where it is intentionally specified (article quotes, testimonial body copy). Not a general-purpose substitute for `--font-sans`.

## Rules

- Do not hardcode `"Roboto"` or `"Open Sans"` in block or prototype files. Use the token.
- Do not introduce a new font without updating `_fonts.scss`, `_theme.scss`, and this document.
- If a component uses `--font-sans-alt`, document why in the block file.
