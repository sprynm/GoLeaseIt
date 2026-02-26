# Font Sizing Principles

Typography is driven by a fluid base and a modular scale.
The fluid base keeps text readable across viewport sizes instead of relying on breakpoint jumps.
Scale steps then derive proportionally from that base so headings and body stay in rhythm.
Use the shared semantic type tokens (`--type-*`) and step tokens (`--step-*`) as the only sizing system.
Do not introduce per-page token scales or ad hoc clamp ladders for type.

Line-heights should remain unitless so they scale naturally with fluid type sizes.
Use tighter line-heights for large headings and looser line-heights for body copy.
Preserve readable measure and vertical rhythm before adding one-off size exceptions.

Source of truth for all numeric values is:
`webroot/css/scss/_theme.scss`

If typography needs to change, update the tokens there and let components inherit via the shared scale.
