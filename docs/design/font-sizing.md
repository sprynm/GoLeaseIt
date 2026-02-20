# Responsive Typography Scheme

## 1. Fluid Base
 ```css 
html {
  /* 18px at narrow viewports, 24px at wide screens */
  font-size: clamp(18px, 1.25vw + 14px, 24px);
}
body {
  font-size: 1rem;          /* inherits 18–24 px */
  line-height: 1.5;         /* proportional rhythm */
}
 ``` 

## 2. Modular Scale Tokens
| Token | Clamp Example | Typical Use |
| --- | --- | --- |
|  `--step--2` | `clamp(1.1rem, 0.8rem + 0.6vw, 1.4rem)` | overline / tiny caps | 
|  `--step--1` | `clamp(1.4rem, 1.2rem + 0.4vw, 1.6rem)` | labels / meta | 
|  `--step-0` | `clamp(1.8rem, 1.2rem + 1.2vw, 2.4rem)` | paragraph base | 
|  `--step-1` | `clamp(1.6rem, 1.2rem + 0.8vw, 2rem)` | feature text | 
|  `--step-2` | `clamp(2rem, 1.4rem + 1.2vw, 2.6rem)` | h4 | 
|  `--step-3` | `clamp(2.2rem, 1.1rem + 2.2vw, 3.3rem)` | h3 | 
|  `--step-4` | `clamp(3rem, 1.5rem + 3vw, 4.5rem)` | h2 | 
|  `--step-5` | `clamp(3.5rem, 0.6rem + 5.8vw, 6.4rem)` | h1 | 
|  `--step-6` | `clamp(4rem, 8vw, 8rem)` | hero/tagline | 

> Adjust endpoints if you need tighter or looser caps; the ratios above follow a major-third scale (~1.25× per step).

## 3. Suggested Utility Classes
 ```css 
.typ--body        { font-size: var(--step-0); line-height: 1.5; }
.typ--feature     { font-size: var(--step-1); line-height: 1.45; }
.typ--h4          { font-size: var(--step-2); line-height: 1.35; }
.typ--h3          { font-size: var(--step-3); line-height: 1.3; }
.typ--h2          { font-size: var(--step-4); line-height: 1.25; }
.typ--h1          { font-size: var(--step-5); line-height: 1.2; }
.typ--hero        { font-size: var(--step-6); line-height: 1.1; }
.typ--label       { font-size: var(--step--1); line-height: 1.4; }
.typ--overline    { font-size: var(--step--2); line-height: 1.4; letter-spacing: 0.12em; }
.nav--main        { font-size: clamp(20px, 0.9rem + 0.5vw, 28px); line-height: 1.3; }
 ``` 

## 4. Implementation Notes
- Keep line-heights unitless; they scale with the  `clamp()` output automatically. 
- Pair tightened line-heights (hero/heading) with letter-spacing tweaks as needed, not smaller font sizes.
- Consider wrapping this in Sass mixins (e.g.,  `@include type("h2")`) so components read cleanly. 
- With the  `html` clamp in place, paragraphs settle between 18–24px, keeping body copy readable from mobile through desktop. 
