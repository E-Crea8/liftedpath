# Unit 20: Editor round-trip and speed

## Goal

An editor change in wp-admin reaches the public Next.js page after revalidation, without a code deploy. The home page is static HTML. This unit runs the success criteria and fixes only a check that fails.

## Design

The public page keeps Design 01. Desktop stays a 1120px column. At 390px the inset stays 18px and a section heading stays 2.15rem. Temporary edits made for the pass are restored before the unit closes, including the home heading “Breaking Barriers to Education & Opportunity” and the About heading “Restoring hope, creating sustainable pathways.”

## Implementation

### Pass

Check these against `http://localhost:3000`, then put every temporary value back:

- Home hero heading, one home stat, one photograph on the home page, and one primary menu label. Each change is stored in WordPress and the public page shows it after the revalidate webhook.
- One palette color and one spacing size in Global Styles. The public page uses the new CSS variables after the design webhook.
- All 18 information-architecture paths return that page’s own content.
- Home section order, type, pill buttons, and the photographic hero still match Design 01.
- Contact and donate render from WordPress. Anonymous submission reads stay private. The donate form has no card field. The hosted-checkout path stays the one verified in Unit 15.
- A visitor request for the home page is Next.js static HTML, not a theme PHP template.
- Body and button pairings stay inside the contrast table in `context/ui-context.md`. Reduced motion still shows content immediately and holds the marquee still.
- `npm run build` passes. The theme is active on WordPress 7.1.3 and PHP 8.2.

### Fixes

Change code only when one of those checks fails. A design, navigation, or settings webhook that does not refresh the shared layout is the expected fix: revalidate the root layout for those tags, the same way the SEO tag already does.

## Dependencies

- None. This unit uses the webhook, the design route, and the pages already built.

## Verify when done

- [x] The visible result named in `context/specs/00-build-plan.md` is true
- [x] Editor-controlled values for this unit are fields, blocks, menus, media, or global styles
- [x] No invariant in `context/architecture.md` was broken
- [x] Desktop and 390px checked, when the unit has UI
- [x] Reduced motion checked, when the unit animates
- [x] Theme activates with no PHP fatal, when the unit touches PHP
- [x] `npm run build` passes, when the unit touches the frontend
- [x] `context/progress-tracker.md` updated
