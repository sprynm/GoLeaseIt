# Font Sizing Principles

Typography should use one shared public scale: `--font-size-*`.
Normal document typography must consume that scale directly.
Do not introduce per-page token scales, semantic size alias layers, or ad hoc clamp ladders for normal typography.

Line-heights should remain separate from font-size tokens.
Use tighter line-heights for large headings and looser line-heights for body copy.
Preserve readable measure and vertical rhythm before adding one-off size exceptions.

## Decisions

The runtime CSS now uses the shared `--font-size-*` scale in `webroot/css/scss/_theme.scss`.

The migration removed:
- `--fluid-base`
- `--fluid-body`
- `--step-*`
- `--type-*-size`
- `--type-body-line`

Body line-height uses `--lh-body`.
Normal document typography consumes `--font-size-*` directly.
The public token names stay stable across viewport ranges even when their formulas are redefined at larger query boundaries.

## Figma Source Of Truth

The current typography design spec comes from the Figma `Styles` frame:
- File: `Go Lease It Slice`
- URL: `https://www.figma.com/design/lz33juiSBrbQAIR9beOjsH/Go-Lease-It-Slice`
- Node: `2901:33`
- Local cache: `docs/design/figma-context-cache/go-lease-it-slice.cache.json`

Captured Figma text sizes:
- Label: `20px`
- Main Navigation: `24px`
- Paragraph / Feature Text: `26px`
- Heading 4: `32px`
- Feature h3: `35px`
- Heading 3: `40px`
- Feature h2: `48px`
- Heading 2: `56px`
- Heading 1: `80px`

## Approved Shared Scale Contract

Public shared tokens:
- `--font-size--1`
- `--font-size-0`
- `--font-size-1`
- `--font-size-2`
- `--font-size-3`
- `--font-size-4`
- `--font-size-5`
- `--font-size-6`

Three target values per token:
- mobile target
- laptop target
- screen target

Approved values:
- `--font-size--1`: `14px // 16px // 20px`
- `--font-size-0`: `16px // 18px // 24px`
- `--font-size-1`: `18px // 20px // 26px`
- `--font-size-2`: `22px // 26px // 32px`
- `--font-size-3`: `28px // 34px // 40px`
- `--font-size-4`: `32px // 42px // 48px`
- `--font-size-5`: `36px // 50px // 56px`
- `--font-size-6`: `40px // 74px // 80px`

Meaning of the three values:
- mobile: target feel below or around a `720px` container
- laptop: target feel at the project content frame width of `75rem` (`$frameMax`, about `1200px`)
- screen: target feel on wide screens approaching the original Figma desktop intent

## Token Definition Rule

The public token name must stay the same across all viewport ranges.
Do not expose helper names like `--font-size-4-stage-2`.

Implementation pattern:
1. Define each `--font-size-*` token in `:root` as a `clamp()` from mobile to laptop.
2. Redefine that same token at the larger query boundary using the project query system.
3. The larger query boundary must use the project `75rem` / `$frameMax` handoff, not a hardcoded media query.

The typography scale may use query boundaries internally, but consumers must still see a single token contract.

## Role Mapping

Default shared mapping:
- label, microcopy, h5, h6: `--font-size--1`
- body copy: `--font-size-0`
- component emphasis / intermediate copy: `--font-size-1`
- h4: `--font-size-2`
- h3: `--font-size-3`
- interior h2: `--font-size-4`
- interior h1: `--font-size-5`
- display h1 / large hero lockups: `--font-size-6`

Rule:
- `--font-size-6` is display-tier by default
- normal document flow should not depend on `--font-size-6` unless visual review proves it necessary

## Exception Policy

Bespoke display sizes are allowed only when they are not normal document typography and there is a clear reason they cannot use the shared type scale, such as:
- hero lockups
- icon/title pairings
- intentionally constrained composite display components

Exceptions should:
- use local tokens or local `clamp()` values
- not redefine the shared typography system
- be documented as exceptions

## Learnings

- Figma is the source for role ordering and upper-bound intent, not a direct instruction to make all runtime typography feel like a 4K comp on a 1080p screen.
- A single global `--fluid-base` was too blunt. Body copy, headings, navigation, and display lockups do not all scale well from one multiplier.
- The repo-level query system and the content frame matter more than raw viewport width. Wide-screen targets must be judged inside the constrained layout, not only by mathematical interpolation.
- One public token per size is easier to maintain than semantic size aliases. Consumers should not need to know about intermediate stages or alternate naming layers.
- Browser capture and image diffing are separate concerns. Real-browser rendering is still required to produce fidelity screenshots, but regression comparison itself can run as a local image diff without Chromium.

## Runtime Source Of Truth

The active CSS token definitions live in:
`webroot/css/scss/_theme.scss`

Keep the Figma cache in sync when design spec data is used as the justification for future type changes.
