# Progress Tracker

Update this file after every meaningful implementation change.

## Current Phase

- Phase B — Next.js frontend. Unit 20 is complete. The build plan is finished.

## Current Goal

- None. Units 01–20 are complete.

## Completed

- Read the Six-File Context Methodology, the Lifted Path design system, Design 01 at https://liftedpath-design.lovable.app/, and the headless WordPress references.
- Wrote the six context files, `AGENTS.md`, `CLAUDE.md`, the build plan, and the spec template.
- Confirmed the local site: WordPress 7.1.3, PHP 8.2.29.
- Unit 01 — Block theme scaffold. LiftedPath is the active block theme on `http://liftedpath.local`. Spec: `context/specs/01-block-theme-scaffold.md`.
- Unit 02 — Design 01 tokens and editor fonts. Spec: `context/specs/02-tokens-editor-canvas.md`.
- Unit 03 — Content model and ACF. Spec: `context/specs/03-content-model.md`.
- Unit 04 — REST contract. Spec: `context/specs/04-rest-contract.md`.
- Unit 05 — Heroes, stats, marquee, and CTA band. Spec: `context/specs/05-blocks-heroes-stats-marquee-cta.md`.
- Unit 06 — Home section blocks. Spec: `context/specs/06-blocks-home-sections.md`.
- Unit 07 — Inner sections and forms. Spec: `context/specs/07-blocks-inner-sections-forms.md`.
- Unit 08 — Pages, navigation, and settings seed. Spec: `context/specs/08-seed-pages-navigation.md`.
- Unit 09 — Next.js application shell. Spec: `context/specs/09-application-shell.md`.
- Unit 10 — WordPress client. Spec: `context/specs/10-wordpress-client.md`.
- Unit 11 — Header and footer from WordPress navigation and settings. Spec: `context/specs/11-header-footer.md`.
- Unit 12 — Home page from the WordPress blocks. Spec: `context/specs/12-home-page.md`.
- Unit 13 — About, What We Do, Learning, and Impact from their page payloads. Spec: `context/specs/13-inner-pages.md`.
- Unit 14 — Pathway and initiative pages from their own page payloads. Spec: `context/specs/14-pathway-pages.md`.
- Unit 15 — Contact, donate, styled email, and hosted checkout. Spec: `context/specs/15-contact-donate.md`.
- Unit 16 — Draft preview. Spec: `context/specs/16-draft-preview.md`.
- Unit 17 — On-demand revalidation. Spec: `context/specs/17-on-demand-revalidation.md`.
- Unit 18 — Metadata, sitemap, redirects, and verification. Spec: `context/specs/18-metadata-sitemap-redirects.md`.
- Unit 19 — Motion. Spec: `context/specs/19-motion.md`.
- Unit 20 — Editor round-trip and speed. Spec: `context/specs/20-editor-round-trip.md`.
- Connected this theme to GitHub at https://github.com/E-Crea8/liftedpath over SSH. Remote, key, and related paths are in `context/github.md`.

## In Progress

- None.

## Next Up

- None. The build plan in `context/specs/00-build-plan.md` is complete.

## Open Questions

- Production hostnames for SiteGround and Vercel are not chosen yet.
- Where the logo files should be copied from (the Design 01 assets versus a file the foundation supplies).
- Answers for the four donor questions on the Impact page.
- Unique body copy for each learning pathway and community initiative. The prototype repeats the parent page.
- Whether the public Next.js domain should also redirect `http://liftedpath.local` visitors in production, or whether the WordPress host stays a private admin host only.

## Architecture Decisions

- WordPress is the content hub. Next.js on Vercel is the public site. Editors keep the block editor, the Site Editor, and ACF.
- The theme is a block theme generated from Underscores and extended with `theme.json`, templates, parts, and ACF blocks. Underscores is not a parent theme.
- The content API is the WordPress REST API. ACF PRO is required for fields. LiftedPath Forms handles contact, donate, notification email, and hosted checkout. Yoast SEO is the per-page search editor. Next.js prints the public tags.
- Public pages use ISR, with a webhook from WordPress for on-demand revalidation. Drafts use Next.js draft mode.
- Tailwind and restyled shadcn/ui primitives implement the tokens in `ui-context.md`. GSAP handles entrance and scroll motion. The marquee is CSS.
- The Next.js app will live at `/Users/user/Local Sites/liftedpath/frontend`, outside this theme.
- Donate opens a hosted checkout on the provider an administrator chooses in LiftedPath Forms: Stripe, Paystack, or Flutterwave. The site stores the gift record and never stores a card number.
- Design 01 is the visual target. Design 02 is excluded.
- Design 01 type is Plus Jakarta Sans and Stack Sans Headline, from local font files. The content column is 1120px. The brand note’s Fraunces, Marcellus, Source Sans 3, and 1024px column are not used.
- Google Fonts is an administrator option in the Site Editor font library. Installed families are copied onto the site. Public pages do not request Google.
- The local admin password is intentionally absent from the repository.
- The theme repository is https://github.com/E-Crea8/liftedpath on branch `main`. SSH uses the `github-ecrea8` host alias and `~/.ssh/id_ed25519_ecrea8`. The Next.js app and the LiftedPath Forms plugin are not in that repository.

## Session Notes

- Workspace: `/Users/user/Local Sites/liftedpath/app/public/wp-content/themes/liftedpath`
- GitHub remote: `git@github-ecrea8:E-Crea8/liftedpath.git`. Plain `git@github.com` uses a different account key and is not the remote for this theme.
- Local WordPress: `http://liftedpath.local` — wp-admin user `admin`. Password is in the Local app only.
- Design reference: https://liftedpath-design.lovable.app/ (Design 01, Original).
- Brand source: `Lifted Path Design System.md`.
- Context entry points: `AGENTS.md` and `CLAUDE.md`.
- Resume by reading this file, then the spec for the unit named under Next Up.
- Unit 01 verified: front page renders the block template (`wp-site-blocks`). Site Editor opens. `wp_is_block_theme()` is true.
- Unit 02 verified: Site Editor palette, font families, font sizes, and spacing sizes match Design 01. Content and wide width are 1120px. At 1280px the column is 1120px; at 390px the inset is 18px and the section heading is 2.15rem. Editor canvas and the front page load Plus Jakarta Sans and Stack Sans Headline from `assets/fonts`.
- Google Fonts stays available under Styles → Typography → Manage fonts → Install Fonts. WordPress downloads a chosen family and stores it on the site. The public front HTML has no `fonts.googleapis.com` or `fonts.gstatic.com` request.
- Unit 03 verified: LiftedPath settings shows the seeded organization facts. A new page shows SEO title, SEO description, and hero summary. Submissions is a private type. Anonymous `GET /wp-json/wp/v2/submissions` returns 401.
- Unit 04 verified: `GET /wp-json/liftedpath/v1/page?path=/sample-page` returns the published Sample Page with its path, title, and five blocks. `/paths` lists that page. `/design` returns the Design 01 palette, font sizes, spacing sizes, 1120px layout, and a version hash. A draft path returns 404. An invalid path returns 400. `/` returns 404 until Unit 08 sets a static front page.
- Unit 05 verified: Hero, Page hero, Stat, Stat group, Marquee, and CTA band are in the LiftedPath inserter. Their fields show in the sidebar, and a typed hero heading appears in the canvas. `http://liftedpath.local/block-preview/` renders them. At 390px the corner figure is hidden and the stat grid is two columns. With reduced motion the marquee does not scroll.
- Unit 06 verified: About tabs, Program cards, Action grid, Sponsor callout, Stewardship, and Process are in the LiftedPath inserter. Their fields show in the sidebar, and a typed about heading appears in the canvas. Choosing Vision shows that panel and the gold pill. `http://liftedpath.local/home-sections/` renders the six sections. At 1280px the action grid is four columns and the content column is 1120px. At 390px each section is one column, the inset is 18px, and the section heading is 2.15rem. This page is a preview, not the homepage.
- Design 01 photographs and logo files from the Lovable project are in the media library and used on the home-sections preview. Section eyebrows use the gold dot and a 20px gap. Action symbols are the Lucide 0.575 set. Pill buttons stay on one line.
- Unit 07 verified: Pillars, Goals, FAQ, Contact form, and Donate panel are in the LiftedPath inserter. Their fields show in the sidebar, and a typed pillars heading appears in the canvas. Opening one donor question closes the other. Choosing USD changes the gift figures. Send message and Arrange my gift stay on the page. `http://liftedpath.local/inner-sections/` renders the five sections. At 1280px the pillar row is four columns and the content column is 1120px. At 390px each section is one column, the inset is 18px, and the section heading is 2.15rem. With reduced motion the FAQ chevron does not animate. This page is a preview, not the homepage. The forms do not send.
- ACF PRO is active.
- Unit 08 verified this session: all 18 public paths return a published page from `GET /wp-json/liftedpath/v1/page`. `/` is Home. `/learning/lift-root` reports parent `/learning`. Impact has four questions and no answers. Running the seed again does not change existing page content. `http://liftedpath.local/` renders the home sections. At desktop the action grid is four columns and the content column is 1120px. At 390px Home, LIFT-ROOT, Contact, and Donate are one column, the inset is 18px, and the section heading is 2.15rem. With reduced motion the home marquee does not scroll. Send message stays on `/contact/`. Arrange my gift stays on `/donate/`. Theme version is 0.8.0.
- The WordPress header and footer are still the scaffold. `primary` and `footer` navigation posts exist for Unit 11 to draw. A WordPress default navigation post titled Navigation is not used.
- Unit 09 verified this session: `http://localhost:3000` shows the static header, deep home band, paper sample section, and teal footer. Body is Plus Jakarta Sans and headings are Stack Sans Headline, loaded from the theme font files. No Google Fonts request. At desktop the column is 1120px, the header is 89px, and the top-level links plus Donate Now are visible. After scroll the header is teal. At 390px the column is 354px, the inset is 18px, the section heading is 2.15rem, the header Donate button is hidden, and the menu dialog lists the links. Reduced motion makes the header color change instant. An unknown path keeps the frame. `npm run build` passes. The labels are placeholders until Unit 11, and the home band is a placeholder until Unit 12.
- Unit 10 verified this session: the home route prints the WordPress title `Home` for `/`, the document title is `Home – LiftedPath`, and the path index includes that path. `/learning/lift-root` parses with parent `/learning`. The design version is a 64-character SHA-256 and the applied tokens stay Design 01 (`#FCFDFD`, 1120px column). Published routes revalidate in 1 hour. At desktop the column is 1120px and the header is 89px. At 390px the column is 354px, the inset is 18px, and the section heading is 2.15rem. Reduced motion keeps the header color change instant. `/about` still shows the shell’s missing-page frame. No Google Fonts request. `npm run lint` and `npm run build` pass. The header and footer labels stay static until Unit 11, and the home band stays a placeholder until Unit 12.
- Unit 11 verified this session: the header and footer read the primary and footer navigation posts and the public settings. Desktop shows the nested What We Do menu, including LIFT-ROOT, and Donate Now. At 390px the dialog lists the same tree and Donate Now, the column is 354px, the inset is 18px, and the section heading is 2.15rem. Reduced motion makes the menu open instant. Renaming the primary About Us label to “About the foundation” changed the header after a cache refresh, and the footer label stayed About Us. The label was restored. The logo is the Site Editor custom logo. The home band stays a placeholder until Unit 12. Theme version is 0.11.0. `npm run lint` and `npm run build` pass.
- Unit 12 verified this session: `http://localhost:3000/` renders the Home blocks in WordPress order, with copy and photographs from that page. The about tabs switch to Vision. Desktop column is 1120px and the action grid is four columns. At 390px the column is 354px, the inset is 18px, the section heading is 2.15rem, the action grid is one column, and the hero corner figure is hidden. Reduced motion keeps the marquee still. Changing the hero heading to “Breaking Barriers to Education & Futures” appeared after a cache refresh, and the original heading was restored. Header, footer, and `/about` still show the Unit 11 frame. No Google Fonts request. Theme version is 0.12.0. `npm run lint` and `npm run build` pass.
- Unit 13 verified this session: `/about`, `/what-we-do`, `/learning`, and `/impact` render their WordPress pages. About shows the story, photograph, three columns, four pillars, six goals, and the join band. What We Do and Learning show their program cards. Impact shows the stat group, stewardship, and four donor questions. Opening one question closes the other. With reduced motion the FAQ chevron does not animate. Desktop column is 1120px and the pillars are four columns. At 390px the inset is 18px, the section heading is 2.15rem, and the pillars are one column. Changing the About hero heading to “Restoring hope, creating sustainable pathways today.” appeared after a cache refresh, and the original heading was restored. `/learning/lift-root` and `/contact` still show the missing-page frame. Home still shows “Breaking Barriers to Education & Opportunity”. No Google Fonts request. Theme version stays 0.12.0. `npm run lint` and `npm run build` pass.
- Unit 14 verified this session: the eleven child URLs under `/learning` and `/what-we-do` each show that page’s title, hero heading, short paragraph, and join band. LIFT-ROOT does not repeat the Learning hub heading. Desktop column is 1120px. At 390px the inset is 18px and the section heading is 2.15rem. Changing the LIFT-ROOT heading to “LIFT-ROOT Today” appeared after a cache refresh, and the original heading was restored. `/contact`, `/donate`, and `/learning/not-a-pathway` stay on the missing-page frame. Home, `/learning`, and `/what-we-do` still show their own pages. No Google Fonts request. Theme version stays 0.12.0. `npm run lint` and `npm run build` pass.
- Unit 15 verified this session: `/contact` and `/donate` render their WordPress pages. A contact message creates a private submission and WordPress builds the styled email. Changing the contact subject in Form settings changes that email. Donate without a provider key stays on the page and says payments are not connected. Stripe, Paystack, and Flutterwave checkout requests are built for each provider’s hosted page. A confirmed gift is recorded once, a second confirmation does not send another email, and the submission has no card number. Desktop contact and donate are two columns inside the 1120px column. At 390px each is one column, the inset is 18px, and the section heading is 2.15rem. Anonymous submission reads return 401. `/donate/complete` with an unknown reference stays inside the site frame and shows no personal details. Home still shows “Breaking Barriers to Education & Opportunity”. No Google Fonts request. Theme version is 0.15.0. The LiftedPath Forms plugin is 1.0.0. `npm run lint` and `npm run build` pass.
- Unit 16 verified this session: the editor preview link points at the Next.js preview route with a one-time token. An About autosave heading was visible on that preview, including after refresh, and absent from the public About page and from anonymous `GET /wp-json/liftedpath/v1/page`. Using the token again returned 401. Anonymous preview reads returned 401. A draft page was readable with the preview credentials and returned 404 on the public page route; that draft was deleted. Exit preview removed the banner and restored the published About heading. The autosave was deleted afterward. Desktop column stayed 1120px and the header stayed 89px. At 390px the banner wrapped, the column was 354px, the inset was 18px, and the section heading was 2.15rem. The banner does not animate. Home still shows “Breaking Barriers to Education & Opportunity”. No Google Fonts request. Theme version is 0.16.0. Published pages still revalidate in 1 hour. `npm run lint` and `npm run build` pass.
- Unit 17 verified this session: changing the About hero heading and sending the revalidate webhook updated `http://localhost:3000/about` on the next visit, without restarting Next.js. The heading was restored and the public page showed the original sentence again. A wrong secret returned 401. An unknown field, a bad path, and a bad tag were rejected. A draft save did not queue a webhook, and the public page route returned 404 for that draft. A blocked webhook returned without stopping the save. Navigation, settings, and global styles are hooked to the same webhook. Home still shows “Breaking Barriers to Education & Opportunity”. No Google Fonts request. Theme version is 0.17.0. Published pages still revalidate in 1 hour. `npm run lint` and `npm run build` pass.
- Unit 18 verified this session: About’s document title is “About the foundation – LiftedPath” and its description is the generated Lagos summary. Changing the Yoast title to “About the foundation today” appeared on the next visit, and the original title was restored. A Search Console code appeared as a meta tag after save, and was removed. `/former-name` redirected to `/about` with 301, and stopped redirecting when the row was cleared. With both Analytics and Tag Manager filled, only Tag Manager was printed. `/sitemap.xml` lists 18 public paths and leaves out the block-preview pages. `/robots.txt` points at the sitemap. `/llms.txt` describes the foundation from LiftedPath settings. Home still shows “Breaking Barriers to Education & Opportunity”. About still shows “Restoring hope, creating sustainable pathways.” No Google Fonts request. Theme version is 0.18.0. Published pages still revalidate in 1 hour. `npm run lint` and `npm run build` pass.
- Unit 19 verified this session: the home hero text rises once on entry, and a section below the fold rises once when it enters. Scrolling back does not replay either entrance. The marquee loops in CSS. With reduced motion the hero and sections are visible immediately, nothing is armed for GSAP, and the marquee is wrapped and still. Desktop column is 1120px, the header is 89px, and the action grid is four columns. At 390px the column is 354px, the inset is 18px, the section heading is 2.15rem, the action grid is one column, and the hero corner figure is hidden. Home still shows “Breaking Barriers to Education & Opportunity”. About still shows “Restoring hope, creating sustainable pathways.” No Google Fonts request. Theme version stays 0.18.0. `npm run lint` and `npm run build` pass. The home page remains static HTML.
- Unit 20 verified this session: a temporary home heading, hero photograph, “Years of impact” label, primary About Us label, paper color, and 24px spacing size each appeared on `http://localhost:3000` after the revalidate webhook, and each was restored. The home heading is again “Breaking Barriers to Education & Opportunity”. About is again “Restoring hope, creating sustainable pathways.” Paper is again `#FCFDFD` and spacing 24 is again 24px. All 18 public paths return their own pages. The home section order, pill buttons, and photographic hero match Design 01. Contact and donate render their forms, the donate form has no card field, and anonymous submission reads return 401. Desktop column is 1120px and the header is 89px. At 390px the column is 354px, the inset is 18px, and the section heading is 2.15rem. Body text is teal on paper and the hero button is teal on gold. With reduced motion the hero is visible immediately and the marquee does not scroll. No Google Fonts request. The production build serves `/` as static HTML. Theme version stays 0.18.0. No code change was required.

- Header logo and its height are set on LiftedPath settings. The header logo is 104px until an editor changes Logo height. “Empowerment Foundation” stays readable.
- The public footer matches Design 01: triangle ground, round phone and email buttons, sand column headings, and a viewport-wide word. Header and Footer tabs on LiftedPath settings hold the logo, footer logo, logo heights, and the large footer word.
- The large footer word sits flush with the bottom of the footer. A hero accepts one video link, with mute, auto replay, and autoplay. The photograph remains the fallback. The link is a text field so the editor keeps the address on save. Menu subtitles come from each page’s Menu subtitle field. Home and inner heroes accept a photograph and an optional MP4 or WebM background video from the block fields. Visitors who prefer less motion see the photograph.
- How we work matches Design 01: 490px cards, the sponsor line 45px under the cards, and the give, partner, empower, and reachout symbols drifting with the Design 01 GSAP motion. Each action has a Hover color field. A blank color keeps that position’s Design 01 tint. Action titles use Stack Sans Headline at Bold, and Title weight on the block can change that.
- The footer location column uses the Design 01 gap between the address, phone, and email. Copyright line is on LiftedPath settings, Footer tab. The site writes the current year in front of that text.
- Our Process keeps Learn More beside the heading until 520px. Below that it sits under the heading at its own width, with 24px between the heading and the button. On that same width the footer space above the rule is 32px.

## Verification

Passed means the checks in that unit’s spec were run and matched. Units 01–07 were checked in the session that built them. They were not re-run while seeding Unit 08. A dash means that check was outside the unit.

| Unit | Status | Editor | Desktop | 390px | Reduced motion | REST |
| --- | --- | --- | --- | --- | --- | --- |
| 01 Scaffold | Passed | Site Editor opens | Front page renders | — | — | — |
| 02 Tokens | Passed | Palette and type match | Column 1120px | Inset 18px, heading 2.15rem | — | — |
| 03 Content model | Passed | Settings and page fields | — | — | — | Submissions stay private |
| 04 REST | Passed | — | — | — | — | Page, paths, design, draft 404 |
| 05 Heroes and stats | Passed | Fields and canvas | Preview renders | Corner figure hidden, stats in two columns | Marquee still | — |
| 06 Home sections | Passed | Fields and canvas | Four action columns, column 1120px | One column, inset 18px, heading 2.15rem | — | — |
| 07 Inner sections | Passed | Fields and canvas | Four pillar columns, column 1120px | One column, inset 18px, heading 2.15rem | FAQ chevron still | — |
| 08 Pages and menus | Passed | Existing content kept | Home, child, Contact, Donate; column 1120px | Same pages, one column, inset 18px, heading 2.15rem | Home marquee still | All 18 paths, Home at `/` |
| 09 App shell | Passed | Static labels, replaced in Unit 11 | Column 1120px, links and Donate Now | Inset 18px, heading 2.15rem, menu dialog | Header color change instant | No fetch yet |
| 10 WordPress client | Passed | Title and design payload from REST | Column 1120px, title Home | Inset 18px, heading 2.15rem, title Home | Header color change instant | Page, paths, design; child parent `/learning` |
| 11 Header and footer | Passed | Menu label and settings from WordPress | Nested What We Do menu, column 1120px | Dialog tree, inset 18px, heading 2.15rem | Menu open instant | Navigation and settings; label change refreshed |
| 12 Home page | Passed | Hero heading from the Home page | Column 1120px, four action columns | Inset 18px, heading 2.15rem, one action column | Marquee still | Page blocks and settings phrases; heading change refreshed |
| 13 Inner pages | Passed | About heading from the About page | Column 1120px, four pillar columns | Inset 18px, heading 2.15rem, one pillar column | FAQ chevron still | Four page payloads; heading change refreshed |
| 14 Pathway pages | Passed | LIFT-ROOT heading from that page | Column 1120px | Inset 18px, heading 2.15rem | — | Eleven child payloads; heading change refreshed |
| 15 Contact and donate | Passed | Form settings, email, and provider | Two columns, column 1120px | One column, inset 18px, heading 2.15rem | — | Private submission, styled email, hosted checkout, anonymous 401 |
| 16 Draft preview | Passed | Preview link with a one-time token | Column 1120px, header 89px, banner on one row | Banner wraps, column 354px, inset 18px, heading 2.15rem | Banner does not animate | Draft read with the preview user; public page route stays 404 for drafts |
| 17 Revalidation | Passed | Page, navigation, settings, and global styles saves | About heading updated on the next visit, then restored | — | — | Webhook 200; wrong secret 401; draft page stays 404 |
| 18 Discovery | Passed | Yoast titles, Search & verification screen | About title and description, sitemap, redirect | — | — | SEO payload, 18 indexable paths, webhook refreshes the next visit |
| 19 Motion | Passed | Durations stay in the presentation | Column 1120px, four action columns, header 89px | Column 354px, inset 18px, heading 2.15rem, one action column | Hero and sections visible immediately, marquee still | — |
| 20 Round-trip | Passed | Heading, photograph, stat, menu label, palette color, spacing size | Column 1120px, header 89px, home section order | Column 354px, inset 18px, heading 2.15rem | Hero visible, marquee still | 18 paths; webhook refreshed the next visit; submissions stay private |
