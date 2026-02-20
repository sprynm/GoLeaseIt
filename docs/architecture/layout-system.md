# Layout System (Container-Centric) — FINAL

This document defines the **layout and responsiveness architecture** for the design system.

The system is **container-first**: components adapt to the space they are given, not to the viewport or device class. Viewport breakpoints exist only to orchestrate macro layout.

---

## 1. Support Policy

### Browser support
- **Evergreen browsers, current − 2 major versions**
  - Chrome / Edge: current − 2
  - Firefox: current − 2
  - Safari / iOS Safari: current − 2 major

### Implication
- **CSS Container Queries (`@container`) are baseline** under this policy
- No full legacy parallel system is maintained
- Progressive enhancement is required

---

## 2. Core Principles

1. **Containers are the foundation**
2. **Components do not assume viewport context**
3. **Viewport media queries are orchestration only**
4. **Base layouts must work without container queries**
5. **Fewer breakpoints, stronger intent**
6. **Layout responds to space; typography responds to user preference**

---

## 3. Units Policy (Authoritative)

We’re trying to satisfy two competing goals:
- **Accessibility**: typography scales with user preference.
- **Stability**: layout switch points should be minimally sensitive to user text scaling.

Important technical note:
- You **cannot** use `clamp()` to define `@media` / `@container` query thresholds. Queries require concrete lengths (e.g. `480px`, `30rem`). `clamp()` can be used *inside* component styles to create smooth “bands”, but not to *trigger* the band.

**Authoring policy:**
- **Switch points (reflow thresholds): use `px` by default.** This keeps macro layout changes stable if a user changes their default font size.
- **Sizing (spacing, type, component dimensions): use `rem` + container units and `clamp()`** so components adapt smoothly inside each band.

Deliberate exception:
- Use `rem` thresholds only when you explicitly want a component’s reflow to track typographic scale.

---

## 4. Architecture Layers

| Layer | Purpose | Queries Allowed |
|------|--------|----------------|
| Blade | Visual wrapper | None |
| Container | Alignment boundary | None |
| Container Query | Adaptive region | Enables `@container` |

---

## 5. Containers

### Standard utilities
```css
.cq { container-type: inline-size; }

.cq-main    { container-type: inline-size; container-name: main; }
.cq-sidebar { container-type: inline-size; container-name: sidebar; }
.cq-card    { container-type: inline-size; container-name: card; }
```

Default to 2–3 named containers per page.

---

## 6. Container Size Bands

| Band | Inline size |
|-----:|-------------|
| c1 | < 480px (≈ 30rem @ 16px root) |
| c2 | ≥ 480px (≈ 30rem @ 16px root) |
| c3 | ≥ 720px (≈ 45rem @ 16px root) |

---

## 7. Component Responsiveness Contract

- Base layout works at c1
- c2 enhancement is typical
- c3 enhancement is rare

---

## 8. SCSS Query System (Authoritative)

SCSS is the **single source of truth** for query thresholds.

### Variables
```scss
$cq-c2: 480px; // ≈ 30rem @ 16px root
$cq-c3: 720px; // ≈ 45rem @ 16px root

$vp-md: 48rem;
$vp-lg: 64rem;
```

### Mixins
```scss
@mixin cq-min($min) {
  @container (min-width: #{$min}) { @content; }
}

@mixin cq-name-min($name, $min) {
  @container #{$name} (min-width: #{$min}) { @content; }
}

@mixin vp-min($min) {
  @media (min-width: #{$min}) { @content; }
}
```

---

## 9. Linting & Enforcement

- No `@media` in `scss/components`
- Viewport queries allowed only in `scss/layout`
- All components must use SCSS query variables

---

## 10. Definition of Done

- Works at c1 without container queries
- Enhancements documented
- Uses SCSS variables
- Lint passes
