# Accessibility Checklist (Design + Frontend)

Last reviewed: 2026-02-17

Use this checklist when implementing or reviewing templates, components, and interaction states.

## 1. Typography
- Body text remains readable at default zoom on mobile and desktop.
- Heading hierarchy is meaningful (`h1` -> `h2` -> `h3`).
- Avoid all-caps for long paragraphs.

## 2. Contrast
- Text on background meets contrast requirements for normal UI use.
- Hover/active/focus states remain readable, not just decorative.
- Icon-only controls include sufficient contrast and visible focus.

## 3. Focus + Keyboard
- All interactive controls are reachable with keyboard.
- Focus indicator is visible on links, buttons, menu controls, and form fields.
- Drawer/menu close control is keyboard operable.

## 4. Motion
- Non-essential animations are optional.
- `prefers-reduced-motion: reduce` disables or minimizes movement.
- Content remains readable if animation never runs.

## 5. Targets + Touch
- Tap/click targets are large enough for mobile use.
- Header notice and nav controls prioritize target size over visual density.

## 6. Forms
- Required fields and validation messages are clear.
- Error state is not color-only; text cue is present.
- Recipient-dependent field toggles do not hide required context unexpectedly.

## 7. Images + Media
- Important images include meaningful alt text.
- Decorative images use empty alt where appropriate.
- Responsive images use appropriate sources and do not force large desktop assets on mobile unless intentional.

## 8. Final QA
1. Test keyboard-only navigation.
2. Test reduced-motion mode.
3. Test at mobile width and desktop width.
4. Test with JS disabled for core content visibility.
