# LiftedPath Empowerment Foundation

## Overview

LiftedPath Empowerment Foundation (LiftedPath) is a purpose-driven non-governmental organization in Lagos, Nigeria. This project is its public website: a calm, warm, credible place where donors, partners, schools, and families can understand the work, see the impact, and give, volunteer, or get in touch.

WordPress is the content system editors use every day. The public website is a Next.js application that renders the approved design. Editors change colors, type, spacing, copy, images, menus, layouts, and pages from wp-admin, and those changes reach the public site without a code deploy for ordinary content edits.

The visual target is Design 01, “Original”, at [https://liftedpath-design.lovable.app/](https://liftedpath-design.lovable.app/). The brand rules live in `Lifted Path Design System.md` in the theme root and are implemented through `context/ui-context.md`.

## Goals

1. Publish every Design 01 route as an editable WordPress page, rendered by Next.js to match that design.
2. Let an Administrator change a global color, font size, or spacing value in the Site Editor, and let an Editor change copy, images, blocks, and menu items, with the public page updating after revalidation and no code change.
3. Keep the public request path on static or incrementally regenerated Next.js pages so visitors do not wait on PHP.
4. Ship with one required plugin, Advanced Custom Fields PRO, and with WordPress core for everything else.
5. Give LiftedPath a contact path and a donation-intent path that record the request in WordPress and email the foundation. Card payments stay outside this version.

## Audiences

- **Donors and sponsors** need a clear account of where money goes and a way to arrange a gift.
- **Partners and schools** need the programs, the process, and a way to start a conversation.
- **Families and volunteers** need the initiatives and a contact form.
- **Editors** (WordPress Editor role) publish and rearrange content.
- **Administrators** manage global styles, templates, options, and the theme.

There is no public user account and no logged-in experience on the website.

## Core User Flow

### Visitor

1. Land on the home page and read the mission, the five initiatives, how the foundation works, and the impact figures.
2. Open a program from the header: LiftedPath Learning, Path Guide, or Community Initiatives, then a specific pathway or initiative.
3. Read About or Impact when they want the story or the stewardship detail.
4. Choose Donate and select a frequency, currency, amount, and program, then send that intent to the foundation.
5. Or choose Contact, pick a reason, and send a message.

### Editor

1. Sign in at `http://liftedpath.local/wp-admin/` on the local site, or the production wp-admin on SiteGround.
2. Edit a page in the block editor, an image in the media library, a menu in the Navigation, or a site-wide fact on the LiftedPath settings screen.
3. Preview the draft on the Next.js preview route.
4. Publish. WordPress notifies the Next.js app. The next request serves the updated page.

### Administrator

1. Open the Site Editor to change palette, type, spacing, or a template part (header, footer).
2. Publish the global styles. The public site picks up the new tokens on revalidation.

## Information Architecture

Design 01 routes. Each route is a WordPress page. Child pages supply the public path.

| Public path | Page | Role |
| --- | --- | --- |
| `/` | Home | Front page |
| `/about` | About Us | Story, mission, vision, pillars, future goals |
| `/what-we-do` | What We Do | Three areas of work |
| `/learning` | LiftedPath Learning | Four learning pathways |
| `/learning/lift-root` | LIFT-ROOT | Early education |
| `/learning/lift-grow` | LIFT-GROW | Secondary education |
| `/learning/lift-beyond` | LIFT-BEYOND | Higher education |
| `/learning/lift-equip` | LIFT-EQUIP | Skills and vocational development |
| `/what-we-do/path-guide` | Path Guide | Mentorship area |
| `/what-we-do/mentorship-coaching` | Mentorship & Coaching | Path Guide initiative |
| `/what-we-do/community-initiatives` | Community Initiatives | Community area |
| `/what-we-do/lift-beyond-barriers` | Lift Beyond Barriers | Special needs education |
| `/what-we-do/lift-beyond-borders` | Lift Beyond Borders | Juvenile and correctional center education outreach |
| `/what-we-do/orphanage-outreach` | Orphanage Outreach | Community initiative |
| `/what-we-do/share-the-joy-of-the-season` | Share the Joy of the Season | Community initiative |
| `/impact` | Impact | Figures, stewardship, donor questions |
| `/contact` | Contact | Phone, email, office, message form |
| `/donate` | Donate | Gift checkout through the provider chosen in wp-admin |

The prototype currently repeats the parent hub’s body on several child URLs (the learning pathways and the individual initiatives). Those URLs still exist and each becomes its own editable page. Seed them with the title, the short line already written in the prototype, and the shared page pattern. Do not invent long program essays. Editors write the rest.

Design 02 (`/design-two`, “New direction”) is a rejected alternate home. It is not a page on this site.

### Header

- Logo links home.
- Nested navigation:
  - Home
  - About Us
  - What We Do
    - LiftedPath Learning → LIFT-ROOT, LIFT-GROW, LIFT-BEYOND, LIFT-EQUIP
    - Path Guide → Mentorship & Coaching
    - Community Initiatives → Lift Beyond Barriers, Lift Beyond Borders, Orphanage Outreach, Share the Joy of the Season
  - Impact
  - Contact Us
- A persistent Donate Now action.
- On small screens the links collapse into a menu button.

### Footer

- Tagline: “Lifting Potential, Inspiring Purpose and Creating Futures.”
- Quick Links: Home, About Us, What We Do, Impact, Contact Us, Donate.
- What We Do: LiftedPath Learning, Path Guide, Community Initiatives.
- Location: 19, George Tienne Crescent, Hilltop Estate, Aboru, Iyana Ipaja, Lagos, Nigeria.
- Phone: +234 806 812 8785
- Email: liftedpathfoundation@gmail.com
- Copyright line for the current year, “LiftedPath Empowerment Foundation. All Rights Reserved.”
- Registration: RC 9872033

Organization facts are settings in WordPress, so a change of phone, email, address, or registration number is an editor change.

## Features

### Home (Design 01 section order)

- Hero on a photograph with a teal overlay, avatar group, eyebrow “Lifting Potentials and Inspiring Purpose”, heading “Breaking Barriers to Education & Opportunity”, intro, and Donate Now.
- Stat: 25+ students supported.
- Marquee of brand phrases separated by ✦.
- “2+ years of impact” beside supporting photography.
- About band with Mission, Vision, and Commitment tabs, plus links to About and Impact.
- “Initiatives that empower”: five program cards.
- Sponsor band: “Sponsor a child or a whole program.”
- “How we work”: Give, Partner, Empower, Reachout.
- Sponsor callout: ₦50,000 keeps one student in school for a term.
- “Thoughtful Actions with Measurable Impact”: three stewardship points on the home page.
- Process: Identify real needs, Fund & pay directly, Mentor & follow up.
- Impact figures: 25+ students, 15 families, 6 school partnerships, 2 years of orphanage visits.

### About

- Hero: “Restoring hope, creating sustainable pathways.”
- Story and the same mission statement used on the home page.
- Vision: a world where every young person facing financial and social barriers can reach education, skills, and opportunity.
- Commitment: every contribution is managed with integrity, accountability, and purpose.
- Pillars: Give, Partner, Empower, Reachout.
- Six future goals: more learners, skills, mentorship, partnerships, families, and lasting systems.
- Closing “Join the journey” band.

### What We Do, Learning, and initiatives

- What We Do introduces the three areas and links into each.
- Learning introduces the four pathways.
- Each pathway and initiative page is editable and uses the shared inner-page pattern (page hero, body blocks, join band).

### Impact

- Figures listed above.
- Stewardship: 85% of funds go directly to programs; direct payment to institutions where possible; annual audits and reports; donation tracking and receipts.
- Donor questions, as an accordion editors can rewrite:
  - Where does my donation actually go?
  - Can I give in kind instead of cash?
  - Can I sponsor one specific student?
  - How do I volunteer?
- Answers are editor content. Leave them empty in the seed until LiftedPath supplies them.

### Contact

- Phone, email, and office, all from settings.
- Form fields: full name, email, reason, message.
- Reasons: volunteer, sponsor a child or program, partner as a school or organisation, in-kind donation, something else.
- Submitting stores a private submission in WordPress and emails the foundation address.

### Donate

- Frequency: one-off or monthly.
- Currency: NGN (₦), USD ($), GBP (£).
- Preset amounts in naira, editable: ₦15,000 School essentials, ₦50,000 Education support, ₦120,000 Skills & tools, ₦300,000 Family care, plus a custom amount.
- Direct the gift to: wherever the need is greatest, Education Sponsorship / Scholarship, Skills Acquisition & Vocational Training, Mentorship & Coaching, Orphanage Visitations, or Share the Joy of the Season.
- “Arrange my gift” stores the selection and opens Stripe, Paystack, or Flutterwave, whichever an administrator selected. The confirmed gift is emailed to the foundation. The page never asks for a card number.

### Editorial control

Editors can, from wp-admin:

- Rewrite any visible string, including headings, buttons, stats, FAQs, and footer facts.
- Replace any image through the media library.
- Reorder and insert the registered LiftedPath blocks, core blocks, and the core Custom HTML block.
- Change the navigation.
- Add, unpublish, and nest pages. A page’s public path follows its WordPress permalink.
- Administrators can change the palette, font sizes, and spacing scale in Global Styles, and can edit header and footer template parts.

### Motion

GSAP runs on the public Next.js site for entrance and scroll moments. The marquee loops with CSS. Visitors who prefer reduced motion get the page without those animations. Editors do not author timelines in wp-admin.

## Seed Copy Already Written

Use this copy in the seed. It is the foundation’s language, not placeholder lorem.

**Mission.** To empower supported individuals by providing access to education, vocational training, and essential support systems, while fostering hope, dignity, and long-term self-reliance through community driven initiatives.

**Vision.** To create a world where every young person, especially those facing financial and social barriers, has access to the education, skills, and opportunities needed to discover their purpose, reach their potential, and contribute meaningfully to society.

**Commitment.** We manage every contribution with integrity, accountability, and purpose, directing resources toward meaningful opportunities for the communities we serve.

**Home intro.** LiftedPath Empowerment Foundation is a purpose driven non-governmental organization founded on a deep commitment to breaking barriers to education and opportunity for children, teenagers, and young adults.

**About story.** What began as a small initiative two years ago has grown into a structured mission to restore hope and create sustainable pathways for individuals who have the passion to succeed but lack the resources to pursue their dreams.

Program card lines:

- Education Sponsorship / Scholarship — Opportunity Today = Possibility Tomorrow — pillar “Stronger People”
- Skills Acquisition & Vocational Training — Learn. Build. Grow. — “Stronger People”
- Mentorship & Coaching — Guidance. Support. Success. — “Brighter Communities”
- Orphanage Visitations — Love. Care. Hope. — “Brighter Communities”
- Share the Joy of the Season — Food. Gifts. Smiles. — “Greater Impact”

Marquee phrases: Brighter communities, Greater impact, Opportunity today, possibility tomorrow, Learn. Build. Grow., Lifting potential, Inspiring purpose, Creating futures.

## Scope

### In Scope

- A WordPress 7.1 block theme in this directory, started from the Underscores generator, with Full Site Editing, `theme.json`, template parts, patterns, and ACF blocks.
- ACF PRO field groups stored as JSON in the theme, exposed in the REST API.
- The WordPress REST API as the only content API.
- A Next.js (App Router, TypeScript) frontend, styled with Tailwind CSS and shadcn/ui primitives restyled to the LiftedPath tokens, animated with GSAP.
- Incremental Static Regeneration, draft preview, and on-demand revalidation when content or global styles change.
- The routes, sections, and interactions listed above.
- Core posts remain available so editors can publish news later. No news index is part of the first public navigation.
- Local development on Local WP (`http://liftedpath.local`). Production WordPress on SiteGround. Production frontend on Vercel.
- Metadata, canonical URLs, a sitemap, and redirects owned by the Next.js app.

### Out of Scope

- Design 02 (“New direction”) and the Lovable design switcher or badge.
- Card payments, Paystack, Stripe, WooCommerce, GiveWP, or any donation checkout.
- Public accounts, a donor portal, a volunteer portal, or a membership area.
- Multilingual content and translation plugins.
- A second frontend (native app, digital signage).
- WPGraphQL, Faust.js, a headless-WordPress plugin suite, page builders, and third-party form plugins. LiftedPath Forms is the foundation’s own plugin for these two forms. Yoast SEO is the per-page search editor. Next.js prints the public tags.
- A blog index, search results page, or comments on the public site.
- An animation editor inside wp-admin.
- Using Underscores as a parent theme, the Customizer, or the Underscores sidebar layouts.
- Hosting the Next.js app inside this theme directory.

## Success Criteria

1. With the `liftedpath` theme active, an Editor can change the home heading, a stat, a menu label, and a photograph in wp-admin, and the public Next.js page shows those changes after revalidation without a code edit.
2. An Administrator can change a palette color and a spacing size in Global Styles, and the public site uses the new values after revalidation.
3. Every path in the information architecture returns a designed page whose content comes from WordPress.
4. The home page matches Design 01: section order, type, color, pill buttons, and the teal photographic hero.
5. A contact submission creates a private WordPress record and an email. A donation creates a private record, opens the selected payment provider, and emails the foundation when the provider confirms payment. The donate page never collects a card number.
6. The public HTML is served by Next.js. A visitor request does not execute the theme’s PHP templates.
7. The frontend `npm run build` passes. The theme activates on WordPress 7.1.3 and PHP 8.2 with no fatal error.
8. Body text and UI labels meet the contrast pairs in `context/ui-context.md`. Motion is still when the visitor prefers reduced motion.

## References

- Design 01: [https://liftedpath-design.lovable.app/](https://liftedpath-design.lovable.app/)
- Brand source in this repo: `Lifted Path Design System.md`
- WordPress handbook: [https://developer.wordpress.org/](https://developer.wordpress.org/)
- Underscores starter: [https://underscores.me/](https://underscores.me/)
- Headless WordPress with Next.js and ACF: [https://www.advancedcustomfields.com/blog/nextjs-wordpress/](https://www.advancedcustomfields.com/blog/nextjs-wordpress/)
- Headless architecture trade-offs: [https://wpvip.com/resources/ebooks/headless-cms-guide/](https://wpvip.com/resources/ebooks/headless-cms-guide/)
- Implementation sequence reference: [https://www.iflair.com/the-ultimate-guide-to-building-a-headless-wordpress/](https://www.iflair.com/the-ultimate-guide-to-building-a-headless-wordpress/)
