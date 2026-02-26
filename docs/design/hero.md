# Hero Layout Guide

The hero system is one shared pattern for homepage and interior mastheads.
Use this document for intent and ownership, not fixed values.

Source of truth:
1. Tokens and sizing primitives: `webroot/css/scss/_theme.scss`
2. Hero structure and responsive behavior: `webroot/css/scss/_block-hero.scss`
3. Interior rendering: `View/Elements/layout/body_masthead.ctp`
4. Homepage rendering: `View/Elements/layout/home_masthead.ctp`

Core principles:
1. Keep the hero token-driven (`--nav-offset`, `--hero-max`, `--hero-inline`, `--hero-vspace` and home hero tokens).
2. Keep layer order consistent: media/background, overlay, then content rail.
3. Align hero content to the same horizontal system used by navigation/container rails.
4. Use hero variants via classes (`.page-hero--home`, `.page-hero--single`) instead of one-off templates.
5. Keep content editorially driven from page/settings/prototype data, not hardcoded copy.
6. Extend behavior with class modifiers and token mappings, not ad hoc pixel overrides.
7. If a new spacing/sizing value is needed repeatedly, add a semantic token in `_theme.scss`.
8. Validate hero changes on desktop and mobile with navigation overlap and CTA visibility checks.

Operational note:
Treat this file as architecture guidance. For exact numbers, always read the SCSS source files above.
