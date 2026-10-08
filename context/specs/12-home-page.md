# Unit 12: Home page

## Goal

The Next.js home page renders the Home page’s blocks in WordPress order. The copy, photographs, and links come from that page. An empty marquee uses the phrases on LiftedPath settings.

## Design

Section order follows the Home payload: hero, marquee, about tabs, program cards, action grid, sponsor callout, stewardship, process, and stat group. That is the Design 01 home sequence. Copy sits in the 1120px column. The hero, marquee, and section backgrounds bleed. At 390px the inset is 18px, the hero corner figure is hidden, the action grid and process steps are one column, and section headings use the 520px size.

The about switcher uses the same radio controls as the WordPress host. The first tab is selected. The marquee loops in CSS and wraps still when the visitor prefers reduced motion. Program-card zoom does not run in that case. This unit does not add GSAP.

## Implementation

### Home route

`app/(site)/page.tsx` replaces the placeholder band with `BlockList`. The document title still comes from the page SEO fields. Header and footer stay as Unit 11 left them. `/about` stays the missing-page frame.

### Block components

`components/blocks/` has one component per Unit 05 and Unit 06 block: Hero, Page hero, Stat, Stat group, Marquee, CTA band, About tabs, Program cards, Action grid, Sponsor callout, Stewardship, and Process. The home seed uses the first list except Page hero and CTA band. Those two still render when a payload includes them. The home seed uses no core blocks, so this unit does not render core blocks.

Attributes stay the ACF field names from `GET /page`. Repeaters are the flat `name`, `name_0_field` keys. A link is `{ title, url, target }` and becomes a public path when it points at this WordPress host. An image field is an attachment ID. The page collects those IDs and reads `GET /wp/v2/media?include=` for `id`, `source_url`, `alt_text`, width, and height. Portrait images render with empty alt. Other photographs use the media-library alt text.

An empty marquee repeater uses `marquee_phrases` from `GET /wp-json/liftedpath/v1/settings`, a list of strings. `form_recipient_email` and `share_image` stay off that route. The stewardship call note uses settings `phone` and hides when the phone is empty.

Presentation is the host block stylesheet, with WordPress preset variables pointed at the frontend tokens.

## Dependencies

- Unit 11, for the header, footer, and settings route.
- The Home page seeded in Unit 08.
- The public WordPress media route.

## Verify when done

- [x] The home page shows the WordPress block order and the Home copy
- [x] A heading change in WordPress appears after the published cache refreshes, then the original heading is restored
- [x] Desktop column is 1120px and the action grid is four columns
- [x] At 390px the inset is 18px, the section heading is 2.15rem, and the action grid is one column
- [x] Reduced motion keeps the marquee still
- [x] Header, footer, and the `/about` missing-page frame still behave as Unit 11 left them
- [x] No Google Fonts request
- [x] Theme loads with no PHP fatal
- [x] `npm run lint` and `npm run build` pass
- [x] `context/progress-tracker.md` updated
