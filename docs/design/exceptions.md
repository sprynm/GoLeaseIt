# Exceptions Layer Policy

Last reviewed: 2026-02-17  
Primary file: `webroot/css/scss/_exceptions.scss`

## Purpose
The `exceptions` layer is for temporary, tightly-scoped rules that cannot yet be expressed cleanly via tokens, utilities, compositions, or block modifiers.

It is **not** a place for permanent overrides or `!important`-driven styling.

## What Belongs Here
- Transitional rules during refactors.
- One-off compatibility fixes tied to legacy markup.
- Time-boxed page-specific patches pending a proper block/composition update.

## What Does Not Belong Here
- New component styling.
- Reusable layout patterns.
- Long-term visual decisions.
- Cross-site design tokens.

## Preferred Order of Solutions
1. Token update in `_theme.scss`.
2. Utility/composition/block adjustment.
3. Block modifier/state class.
4. Exception rule (last resort).

## Required Metadata (Comment each exception)
Every exception should include:
- Why it exists.
- Intended replacement path.
- Date added.

Example:
```scss
// TEMP: Legacy home spacing mismatch after nav refactor.
// Replace with utility class once page template is updated.
// Added: 2026-02-17
.home .l-single {
  padding-top: var(--space-2xl);
}
```

## Exit Criteria
An exception should be removed when:
- Template/markup has been normalized, or
- Equivalent behavior exists in tokens/utilities/blocks.

## Hygiene Rule
An empty `_exceptions.scss` file is acceptable and preferred.
