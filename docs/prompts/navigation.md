# Mobile-First Navigation System Prompt

Use this prompt to generate accessible, progressively-enhanced navigation systems with modern browser APIs.

---

## Prompt

Create a mobile-first website navigation system with the following requirements:

**Architecture:**
- Mobile (< 768px): Slide-in drawer using `<dialog>` element
- Desktop (≥ 768px): Horizontal navigation with dropdown submenus
- Progressive enhancement: Core functionality works, JavaScript enhances
- Zero dependencies: Vanilla JS only

**Mobile Drawer Specifications:**
- Use `<dialog>` element with `showModal()` for proper modal behavior
- Slide in from left side with smooth CSS transitions
- Width: `min(80vw, 320px)`
- Semi-transparent backdrop with light dismiss (click outside closes)
- Close button in header
- Fixed positioning to overlay content without breaking layout
- Nested `<details>` elements for submenu accordions
- Auto-close drawer when navigation links are clicked
- Use `@starting-style` for entrance animations

**Desktop Navigation Specifications:**
- Horizontal menu bar with inline links
- Dropdown submenus using Popover API
- JavaScript positions popovers below trigger buttons using `getBoundingClientRect()`
- Light dismiss via Popover API (click outside closes)
- Minimum submenu width: 200px
- Fixed positioning for popovers

**Accessibility Requirements:**
- Semantic HTML: `<nav>`, `<dialog>`, `<details>`, `<button>`
- ARIA attributes: `aria-label`, `aria-expanded`, `aria-controls`, `aria-haspopup`
- Keyboard navigation: Tab, Enter, Escape, Arrow keys
- Focus management: Trap focus in drawer, return focus to trigger on close
- Screen reader friendly: Role attributes where needed

**Styling Approach:**
- CSS custom properties for theming
- Mobile-first media queries
- Smooth transitions for all interactive states
- Clear visual hierarchy
- Adequate touch targets (minimum 44x44px)

**JavaScript Features:**
- Open/close mobile drawer
- Position desktop popovers relative to triggers
- Accordion behavior: Close other submenus when opening one
- Update `aria-expanded` states
- Reposition popovers on scroll/resize
- Arrow key navigation within submenus
- Close on Escape key

**Browser API Usage:**
- `<dialog>` with `showModal()` for mobile drawer
- Popover API for desktop dropdown menus
- `<details>`/`<summary>` for mobile submenus
- CSS `@starting-style` for transition-on-entry

**Output Format:**
Provide a single HTML file with embedded CSS and JavaScript that can be copy-pasted into CodePen or run locally. Include:
1. Semantic HTML structure
2. Complete CSS with mobile and desktop styles
3. Progressive enhancement JavaScript
4. Demo content showing 2-3 top-level items with submenus
5. Brief feature list in the page content

**Example Menu Structure:**
```
- Home
- About  
- Services (has submenu)
  - Web Development
  - SEO
  - Consulting
- Products (has submenu)
  - Product A
  - Product B
  - Product C
- Contact
```

---

## Expected Output

A complete, production-ready navigation component that:
- Works without JavaScript (drawer requires JS, but gracefully degrades)
- Respects user preferences (respects reduced motion)
- Handles edge cases (long menu items, many submenus, small viewports)
- Follows modern best practices (logical properties, cascade layers-ready)
- Includes smooth, performant animations
- Positions correctly on scroll/resize

## Customization Points

The generated code should use CSS custom properties for easy theming:
- Colors (background, text, primary, borders)
- Spacing (gaps, padding)
- Sizing (container widths, drawer width)
- Typography (font families, sizes, weights)
- Border radius
- Transition durations

## Testing Checklist

The output should be tested for:
- [ ] Mobile drawer slides in/out smoothly
- [ ] Backdrop closes drawer on click
- [ ] Desktop dropdowns position correctly below triggers
- [ ] Keyboard navigation works (Tab, Enter, Escape, Arrows)
- [ ] ARIA states update correctly
- [ ] Focus returns to trigger after closing
- [ ] Works on narrow viewports (320px)
- [ ] Submenus don't overflow viewport
- [ ] Links close drawer/popover when clicked
- [ ] Resize from mobile to desktop works seamlessly
