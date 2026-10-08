# Unit 13: About, What We Do, Learning, Impact

## Goal

`/about`, `/what-we-do`, `/learning`, and `/impact` render their own WordPress pages. Child pathways, Contact, and Donate stay on the missing-page frame.

## Design

Each page starts with the page hero. Copy sits in the 1120px column. Section backgrounds bleed. At 390px the inset is 18px and section headings use the 520px size.

About continues with the story, a photograph, three columns, pillars, goals, and the join band. What We Do and Learning continue with program cards. Impact continues with the stat group, stewardship, and the donor questions. FAQ keeps one question open at a time. An empty answer still shows the question. The chevron turns instantly when reduced motion is on.

## Implementation

### Routes

`app/(site)/about/page.tsx`, `what-we-do/page.tsx`, `learning/page.tsx`, and `impact/page.tsx` read `GET /page?path=` for that path. The document title uses the page SEO fields. The same block list as Home renders the payload. Header and footer stay as they are.

### Blocks

Pillars, Goals, and FAQ get React components. Page hero, program cards, stat group, stewardship, and CTA band already exist. These pages also use `core/heading`, `core/paragraph`, `core/image`, and `core/columns`. Those four get React counterparts. Their text comes from the sanitized block HTML. An image ID is resolved through the media route, and the alt text is the one stored on the image block.

Contact form and donate panel stay out of this unit.

## Dependencies

- Unit 12, for the block list, page hero, and media lookup.
- The pages seeded in Unit 08.

## Verify when done

- [x] `/about`, `/what-we-do`, `/learning`, and `/impact` show their WordPress copy
- [x] A heading change on About appears after the published cache refreshes, then the original heading is restored
- [x] Desktop column is 1120px and the pillars are four columns
- [x] At 390px the inset is 18px, the section heading is 2.15rem, and the pillars are one column
- [x] Opening one FAQ question closes another, and reduced motion keeps the chevron instant
- [x] `/learning/lift-root`, `/contact`, and Home still behave as before
- [x] No Google Fonts request
- [x] `npm run lint` and `npm run build` pass
- [x] `context/progress-tracker.md` updated
