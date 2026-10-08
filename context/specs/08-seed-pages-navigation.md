# Unit 08: Seed pages, navigation, and settings

## Goal

Every public path in the information architecture is a published WordPress page. `GET /wp-json/liftedpath/v1/page` returns that page, and `/` is the Home page. Primary and footer navigation posts exist for the header and footer that Next.js builds later.

## Design

The WordPress host renders each page with the blocks already styled. The content column stays 1120px, with an 18px inset and a 2.15rem section heading below 520px. The designed header, nested menu, and footer columns are Unit 11. This unit stores the menu. It does not draw it.

Child pages use the shared inner pattern: a page hero, the one sentence that already exists, and the join band. Donor-question answers on Impact are empty.

## Implementation

### Pages

`inc/seed.php` creates a page only when that path has no content. A later edit is left alone. Home is the static front page.

| Path | Title | Content |
| --- | --- | --- |
| `/` | Home | Hero, marquee, about tabs, program cards, action grid, sponsor callout, stewardship, process, stat group |
| `/about` | About Us | Page hero, story, mission, vision, commitment, pillars, goals, join band |
| `/what-we-do` | What We Do | Page hero and the three areas |
| `/learning` | LiftedPath Learning | Page hero and the four pathways |
| `/learning/lift-root` | LIFT-ROOT | Page hero, the existing sentence, join band |
| `/learning/lift-grow` | LIFT-GROW | Same pattern |
| `/learning/lift-beyond` | LIFT-BEYOND | Same pattern |
| `/learning/lift-equip` | LIFT-EQUIP | Same pattern |
| `/what-we-do/path-guide` | Path Guide | Same pattern |
| `/what-we-do/mentorship-coaching` | Mentorship & Coaching | Same pattern. Parent is What We Do |
| `/what-we-do/community-initiatives` | Community Initiatives | Same pattern |
| `/what-we-do/lift-beyond-barriers` | Lift Beyond Barriers | Same pattern |
| `/what-we-do/lift-beyond-borders` | Lift Beyond Borders | Same pattern |
| `/what-we-do/orphanage-outreach` | Orphanage Outreach | Same pattern |
| `/what-we-do/share-the-joy-of-the-season` | Share the Joy of the Season | Same pattern |
| `/impact` | Impact | Page hero, four figures, four stewardship points, four questions with empty answers |
| `/contact` | Contact Us | Page hero and contact form. The form does not send |
| `/donate` | Donate | Page hero and donate panel. The button does not submit |

`learning` is a top-level page. Its public path is `/learning`, and the header still lists it under What We Do.

Each page stores `seo_title`, `seo_description`, and `hero_summary`.

### Navigation

Two `wp_navigation` posts, created only when that slug is absent:

- `primary`: Home, About Us, What We Do and its nested areas, Impact, Contact Us. Donate Now is the `/donate` page, not a primary item.
- `footer`: a Quick Links group and a What We Do group. Quick Links includes Donate.

### Settings

Organization facts stay on the settings screen from Unit 03. The seed does not overwrite them. If the site title is still the local default, it becomes LiftedPath and the tagline becomes the settings tagline. The logo is set only when none is chosen.

## Dependencies

- Units 04, 05, 06, and 07.
- ACF PRO, already active.
- Photographs already in the media library.

## Verify when done

- [x] Every path in the information architecture returns a published page from `GET /wp-json/liftedpath/v1/page`
- [x] `/` is Home, and a child path reports its parent
- [x] Impact questions are present and their answers are empty
- [x] Primary and footer navigation posts exist, and an existing page is not overwritten
- [x] Home, a child page, Contact, and Donate render on the WordPress host at desktop width and at 390px
- [x] Reduced motion keeps the home marquee still
- [x] Contact and donate buttons do not submit
- [x] Theme loads with no PHP fatal
- [x] `context/progress-tracker.md` records this unit and the verification table
