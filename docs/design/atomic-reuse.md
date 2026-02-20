# Atomic Reuse Guidance (CUBE-Aligned)

This project favors **reusing utilities and atom-sized blocks first** before creating new, context-specific SCSS components.
Goal: improve cohesion and reduce drift across prototypes and pages without abandoning CUBE.

## Priority Order (Authoring)
1. Reuse existing utilities (`u-*`) and atoms (e.g., `.btn`, `.c-stack`, `.c-grid`).
2. Extend atoms via modifiers or scoped wrappers before creating new blocks.
3. Only create a new block when reuse would be semantically wrong or create coupling.

## Examples
- Prefer extending `.btn` with a modifier or context wrapper instead of creating a hero-only button.
- Prefer utility spacing/token usage (`--space-*`, `--step-*`) over custom per-block values.

## When to Create a New Block
- The element has unique behavior or structure that cannot be expressed via utilities + modifiers.
- Reuse would cause hidden coupling or confusing selectors.
- The block is expected to appear in more than one template.

## Tradeoff Policy
- Prototype-level visual control can be reduced if it improves global cohesion.
- If a prototype needs exceptions, use minimal, scoped overrides in the exceptions layer.

## Decision Notes
If a new block is created, document why the existing utilities/atoms were insufficient.
