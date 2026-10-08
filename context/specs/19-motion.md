# Unit 19: Motion

## Goal

The public home page plays a short entrance once: the hero text on load, and each later section when it enters the viewport. The gold marquee loops in CSS. A visitor who prefers reduced motion sees the content immediately, and the marquee does not scroll.

## Design

Motion follows Design 01. Durations are part of the presentation. Editors do not set them in wp-admin.

Desktop and 390px keep the existing layout. The content column stays 1120px. At 390px the inset stays 18px and a section heading stays 2.15rem. The hero photograph stays still while the eyebrow, heading, introduction, button, and corner figure rise into place. Below the hero, a section’s heading and its cards, steps, or columns rise once when that piece reaches the viewport. Scrolling back does not replay the entrance.

The marquee keeps its linear CSS loop. With reduced motion it wraps onto static lines and the duplicate phrase group stays hidden. Tabs, the FAQ chevron, and the mobile menu keep the short transitions they already have, and those stay instant with reduced motion.

## Implementation

### Page entrance

`components/motion/PageMotion.tsx` is the only client component that runs GSAP. `BlockList` wraps the page blocks in it. The header, footer, and marquee are outside the tweens.

Hero and page-hero text use a load timeline: 34px rise, 0.9s, stagger 0.13s, a 0.15s delay, `power3.out`. Section pieces use ScrollTrigger once, start `top 92%`: 62px rise, 1.05s, `power3.out`, then inline transform and opacity are cleared. Tweens use transform and opacity. `gsap.matchMedia()` skips them when `prefers-reduced-motion: reduce` matches, so nothing is left at opacity 0.

### Marquee

The loop already lives in `styles/blocks.css`: `liftedpath-marquee`, 24s linear, infinite. The reduced-motion block sets `animation: none`, wraps the phrases, and hides the aria-hidden copy. This unit does not move that loop into GSAP.

## Dependencies

- `gsap` — entrance tweens and ScrollTrigger.
- `@gsap/react` — `useGSAP`, so the tweens revert when the page unmounts.

## Verify when done

- [x] The visible result named in `context/specs/00-build-plan.md` is true
- [x] Editor-controlled values for this unit are fields, blocks, menus, media, or global styles
- [x] No invariant in `context/architecture.md` was broken
- [x] Desktop and 390px checked, when the unit has UI
- [x] Reduced motion checked, when the unit animates
- [x] Theme activates with no PHP fatal, when the unit touches PHP
- [x] `npm run build` passes, when the unit touches the frontend
- [x] `context/progress-tracker.md` updated
