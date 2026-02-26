# Spacing System Plan

Spacing tokens are defined in `webroot/css/scss/_theme.scss` and consumed through `var(--space-*)`.

## Active Spacing Tokens

| Token | Intent |
| --- | --- |
| `--space-2xs` | Micro gaps (icon/text pairs, tight metadata spacing) |
| `--space-xs` | Tight internal spacing (small label gaps, compact stacks) |
| `--space-sm` | Small component padding and compact gutters |
| `--space-md` | Default vertical rhythm between controls and paragraphs |
| `--space-lg` | Standard section-internal spacing and grid gaps |
| `--space-xl` | Major section padding and large component spacing |
| `--space-2xl` | Large section separation and feature-region spacing |

## Related Layout Spacing Tokens

| Token | Intent |
| --- | --- |
| `--region-space` | Default `c-region` vertical padding (mapped to `--space-xl`) |
| `--form-space-tight` | Tight form control spacing (mapped to `--space-2xs`) |
| `--frame-gutter` | Container side gutter (layout width token, not component spacing) |

## Rules

1. Use only the active tokens above for component spacing.
2. If a new spacing value repeats, add a semantic token in `_theme.scss` first.
3. Do not introduce undocumented aliases in templates or SCSS.
