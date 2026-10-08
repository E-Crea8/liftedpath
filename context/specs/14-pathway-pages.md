# Unit 14: Pathway and initiative pages

## Goal

Each child URL under `/learning` and `/what-we-do` renders that WordPress page’s title and blocks. `/contact` and `/donate` stay on the missing-page frame.

## Design

These pages use the page hero, the short paragraph, and the join band already stored on the page. Copy sits in the 1120px column. At 390px the inset is 18px and a section heading uses the 520px size. The hero heading is the page’s own heading, so LIFT-ROOT does not repeat the Learning hub heading.

## Implementation

### Routes

`app/(site)/learning/[slug]/page.tsx` and `app/(site)/what-we-do/[slug]/page.tsx` read `GET /page?path=` for `/${parent}/${slug}`. The document title uses that page’s SEO fields. The same block list as the hub pages renders the payload.

Known published children come from `GET /paths`. A slug that is not a published page uses the missing-page frame. A slug with characters outside the public path pattern does too.

The eleven pages are `/learning/lift-root`, `/learning/lift-grow`, `/learning/lift-beyond`, `/learning/lift-equip`, `/what-we-do/path-guide`, `/what-we-do/mentorship-coaching`, `/what-we-do/community-initiatives`, `/what-we-do/lift-beyond-barriers`, `/what-we-do/lift-beyond-borders`, `/what-we-do/orphanage-outreach`, and `/what-we-do/share-the-joy-of-the-season`.

## Dependencies

- Unit 13, for the shared page reader, page hero, paragraph, and join band.
- The child pages seeded in Unit 08.

## Verify when done

- [x] Each of the eleven URLs shows its own WordPress title and hero heading
- [x] A heading change on LIFT-ROOT appears after the published cache refreshes, then the original heading is restored
- [x] Desktop column is 1120px
- [x] At 390px the inset is 18px and the section heading is 2.15rem
- [x] `/contact`, `/donate`, and an unknown child slug stay on the missing-page frame
- [x] Home, `/learning`, and `/what-we-do` still show their own pages
- [x] No Google Fonts request
- [x] `npm run lint` and `npm run build` pass
- [x] `context/progress-tracker.md` updated
