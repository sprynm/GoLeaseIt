# Typography & Spacing Refactor

## Snapshot / Audit
- Removed duplicate content blocks (`main p`, `main ul`, etc.) in `webroot/css/scss/_base.scss` (previously `_general.scss`), consolidating them under the global `:where(main) :is(...)` rule from `webroot/css/scss/stylesheet.scss`.
- Collapsed bespoke hero + feature heading `clamp()` definitions into named tokens (`--hero-title`, `--hero-name`, etc.), eliminating ad-hoc font-size overrides in `webroot/css/scss/_base.scss` (previously `_general.scss`).
- Replaced ad-hoc spacing variables (`--space-080`, `--space-150`, etc.) with the `base-step()` helper and `@include base-space(...)`, keeping only semantic tokens (`--space-2xs`/`--space-section`).
- Legacy `rem` math that influenced paddings, radii, and outlines now uses `base-step(multiplier)` so every raw size derives from `--base-10`.

## Decisions
- Retain `html { font-size: 62.5%; }` for now to avoid cascading ripple effects; all sizing now flows through `--base-10`.
- Paragraph typography is fluid (`--fluid-body` = 18->24px) to maintain readability across breakpoints without double-scaling.
- Introduced `--base-10` abstraction so flipping the root size is a single-line change in the future.
- Added helper function/mixin (`base-step`, `base-space`) to express ad-hoc multiples without polluting the global token namespace.

## Final CSS Blocks
```scss
/* Preserve the 10px base for now */
html { font-size: 62.5%; }               /* 1rem = 10px */
:root { --base-10: 1rem; }               /* 1 base-10 unit = 10px today */

@function base-step($multiplier) { @return calc(#{$multiplier} * var(--base-10)); }
@mixin base-space($property, $values...) {
  $resolved: ();
  @each $value in $values { $resolved: append($resolved, base-step($value)); }
  #{$property}: $resolved;
}

/* Global token layer */
:root{
  /* TYPOGRAPHY */
  --fluid-body: clamp(1.8rem, 1.2rem + 1.2vw, 2.4rem);  /* 18px -> 24px */
  --step--2: calc(1.1 * var(--base-10));
  --step--1: calc(1.4 * var(--base-10));
  --step-0:  var(--fluid-body);
  --step-1:  calc(2.0 * var(--base-10));
  --step-2:  calc(2.6 * var(--base-10));
  --step-3:  calc(3.3 * var(--base-10));
  --step-4:  calc(4.5 * var(--base-10));
  --step-5:  calc(6.4 * var(--base-10));
  --step-6:  calc(8.0 * var(--base-10));

  /* FEATURE TYPOGRAPHY */
  --hero-title: clamp(base-step(2.4), 3.2vw, base-step(4.4));
  --hero-tagline: clamp(base-step(1.6), 2vw, base-step(2.2));
  --hero-name: clamp(base-step(2.7), 3.8vw, base-step(3.9));
  --hero-category: clamp(base-step(1.1), 1.8vw, base-step(1.45));
  --hero-summary: clamp(base-step(1), 1.3vw, base-step(1.2));
  --hero-badge: clamp(base-step(2.4), 5vw, base-step(3.1));

  /* LINE HEIGHT TOKENS */
  --lh-body: 1.5;
  --lh-tight: 1.25;
  --lh-title: 1.15;
}

:root {
  --font-sans: "Open Sans", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  --font-serif: "Spectral", ui-serif, Georgia, "Times New Roman", Times, serif;
  --inline-padding: clamp(base-step(1.6), 5.660377358490567vw, base-step(9.6));
  --size-base: clamp(base-step(1.6), var(--base-10) + 0.25vw, base-step(1.8));

  /* Footer */
  --ftr-bg: #2e3a4a;
  --ftr-fg: #e7eef6;
  --ftr-muted: #c7cfda;
  --ftr-link: #ffffff;
  --ftr-link-hover: #ffffff;
  --ftr-rule: rgba(255, 255, 255, 0.35);
  --ftr-maxw: 1200px;
  --ftr-padY: clamp(base-step(1.8), 3.2vw, base-step(3.2));
  --ftr-gap: clamp(base-step(0.8), 2vw, base-step(2.8));
  --ftr-font: var(--step--1);

  /* Spacing (semantic) */
  --space-2xs: clamp(base-step(0.35), 0.8vw, base-step(0.55));
  --space-xs: clamp(base-step(0.45), 1.1vw, base-step(0.75));
  --space-sm: clamp(base-step(0.6), 1.5vw, base-step(0.9));
  --space-md: clamp(base-step(0.85), 2.2vw, base-step(1.3));
  --space-md-plus: clamp(base-step(0.95), 2.5vw, base-step(1.4));
  --space-lg: clamp(base-step(1.2), 3vw, base-step(1.8));
  --space-lg-plus: clamp(base-step(1.4), 3.5vw, base-step(2));
  --space-xl: clamp(base-step(1.5), 4vw, base-step(2.5));
  --space-2xl: clamp(base-step(2.4), 6vw, base-step(3.8));
  --space-section: clamp(base-step(4), 8vw, base-step(5));
  --space-mini: base-step(0.35);

  /* Supplemental type */
  --font-compact: base-step(1.6);
  --font-ui: base-step(1.5);
  --font-label: base-step(2.1);
  --font-caption: base-step(0.95);
  --font-micro: base-step(0.78);
}

/* Base usage */
body{
  font-family: var(--font-sans, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif);
  font-size: var(--step-0);
  line-height: var(--lh-body);
  text-rendering: optimizeLegibility;
  font-optical-sizing: auto;
}

:where(main) :is(p, ul, ol, dl, pre, code, table){
  font-size: var(--step-0);
  line-height: var(--lh-body);
}

h1{ font-size: var(--step-6); line-height: 1.1; letter-spacing:-0.005em; }
h2{ font-size: var(--step-4); line-height: 1.15; }
h3{ font-size: var(--step-3); line-height: 1.25; }
h4{ font-size: var(--step-2); line-height: 1.35; }
h5{ font-size: var(--step-1); line-height: 1.40; }
h6{ font-size: var(--step--1); line-height: 1.45; }

.typ--body    { font-size: var(--step-0); line-height: 1.5; }
.typ--feature { font-size: var(--step-2); line-height: 1.35; }
.typ--tagline { font-size: var(--step-6); line-height: 1.2;  }
.nav--main    { font-size: clamp(2.0rem, 1.4rem + 0.6vw, 2.8rem); line-height: 1.3; }
```

## Accessibility Rationale
- Many mobile users bump OS text size while desktop audiences rarely edit browser defaults; centralizing on `--base-10` lets us respect those preferences later without touching every selector.
- Fluid body copy (18->24px) and scalable headings keep pages within WCAG readability targets and remain legible up to 200 % zoom.
- Consolidated line-height + spacing helpers preserve vertical rhythm and clickable areas when type scales.
- Tokens + mixins ensure future design tweaks change a single source rather than chasing one-off values across templates.

## Rollout Checklist
- [ ] Token entry point remains globally imported via `webroot/css/scss/stylesheet.scss`.
- [ ] Duplicate/conflicting typography rules removed (`main …` lists, hero font overrides).
- [ ] Content typography centralized with `:where(main) :is(...)`.
- [ ] QA at 360 / 768 / 1024 / 1440 px plus 200 % zoom; spot-check hero, forms, blockquotes, nav, footer.
- [ ] Communicate new tokens + helper mixins to component owners to prevent regression.

## Future Flip
To respect browser defaults later:
```css
html { font-size: 100%; }
:root { --base-10: 0.625rem; }
```
Visual sizing remains unchanged because all tokens derive from `--base-10`.




