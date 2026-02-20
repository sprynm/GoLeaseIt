# Project Closeout Rules (Week of 2026-02-17)

This is the compact summary of what we learned at the end of implementation.
Use this as the default philosophy for final polish and any post-launch fixes.

## Learnings
- The fastest changes were made by extending existing system classes (`.btn`, shared hero/layout patterns) instead of creating bespoke variants.
- Most frontend breakages came from script loading assumptions, not from CSS.
- Form behavior and recaptcha need defensive loading because pages vary widely by template and plugin context.
- Documentation drift happened when behavior changed faster than docs; one canonical rule file reduces that drift.

## Philosophy
- Prefer convergence over novelty: align with shared patterns before inventing new ones.
- Prefer explicit runtime guards over optimistic assumptions.
- Prefer smaller dependency surface (vanilla-first JS) over convenience dependencies.
- Prefer one canonical rule source over repeated summaries across many docs.

## Working Rules
- Reuse-first UI rule: if a shared class can express the design, use it before adding a new class.
- Script policy rule: load only scripts required for the current page context.
- JS dependency rule: keep frontend behavior vanilla-first; legacy libraries require explicit justification.
- Forms safety rule: initialize validation behavior only when form selectors are present.
- Recaptcha safety rule: branch script loading from settings (`invisible` vs standard) to avoid runtime errors.
- Build rule: optimization tooling is allowed only when deterministic, documented, and optional to the normal deploy path.
- Documentation rule: keep durable guidance in the appropriate architecture/design doc and avoid separate narrative history logs.
