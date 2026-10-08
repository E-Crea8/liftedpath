# Build Plan

Units run in order. Each unit gets a spec at `context/specs/NN-name.md`, written from `_template.md` immediately before that unit is built. A later unit may start only after the earlier unit’s checklist is true.

Dependencies are the units that must already be complete. “Visible result” is what a person can confirm when the unit is done.

## Phase A — WordPress theme

### Unit 01 — Block theme scaffold

- **Builds:** A theme WordPress can activate. Generate it from Underscores (slug `liftedpath`, no WooCommerce, no Sass), then add the block-theme files: `theme.json` stub, `templates/index.html`, `parts/header.html`, `parts/footer.html`, and a minimal `index.php`.
- **Boundary:** Theme shell.
- **Depends on:** Nothing.
- **Visible result:** The LiftedPath theme activates on `http://liftedpath.local` and the front of the WordPress host shows the block template.

### Unit 02 — Tokens, theme supports, editor canvas

- **Builds:** `theme.json` version 3 from `ui-context.md` (palette, fonts, font sizes, spacing, radii, layout, element styles). Theme supports, image sizes, wide alignment, and editor CSS. Design 01 loads Plus Jakarta Sans and Stack Sans Headline from theme font files.
- **Boundary:** Theme design system.
- **Depends on:** 01.
- **Visible result:** The Site Editor palette, type sizes, and spacing sizes match the token tables.

### Unit 03 — Content model and ACF

- **Builds:** ACF local JSON, the LiftedPath settings options page, page-level SEO fields, the private `submission` post type, and an admin notice when ACF is missing. Requires ACF PRO installed on the site.
- **Boundary:** WordPress data model.
- **Depends on:** 02.
- **Visible result:** An administrator sees LiftedPath settings, and a page shows the SEO fields.

### Unit 04 — REST contract

- **Builds:** `GET /wp-json/liftedpath/v1/page`, `GET /wp-json/liftedpath/v1/paths`, and `GET /wp-json/liftedpath/v1/design`, with the shapes in `architecture.md`.
- **Boundary:** Read API.
- **Depends on:** 03.
- **Visible result:** A published sample page returns JSON with its path, title, and blocks array. The design route returns the token payload.

### Unit 05 — Blocks: heroes, stats, marquee, CTA

- **Builds:** `liftedpath/hero`, `page-hero`, `stat`, `stat-group`, `marquee`, `cta-band`, each with `block.json`, fields, and `render.php`.
- **Boundary:** Editor blocks.
- **Depends on:** 03.
- **Visible result:** Each block can be inserted in the editor and shows its fields on the WordPress host.

### Unit 06 — Blocks: home sections

- **Builds:** `about-tabs`, `program-cards`, `action-grid`, `sponsor-callout`, `stewardship`, `process`.
- **Boundary:** Editor blocks.
- **Depends on:** 05.
- **Visible result:** The home sections can be composed in the editor.

### Unit 07 — Blocks: inner sections and forms

- **Builds:** `pillars`, `goals`, `faq`, `contact-form`, `donate-panel`. Form blocks render fields. They submit in a later unit.
- **Boundary:** Editor blocks.
- **Depends on:** 05.
- **Visible result:** About, Impact, Contact, and Donate can be composed in the editor.

### Unit 08 — Seed pages, navigation, and settings

- **Builds:** The pages and parent relationships in `project-overview.md`, primary and footer navigation, settings values, and block content using the seed copy. Empty FAQ answers. Child pages receive their title and the one-line description that exists today.
- **Boundary:** WordPress content.
- **Depends on:** 04, 05, 06, 07.
- **Visible result:** Every public path resolves to a page in wp-admin, and the REST page route returns that page.

## Phase B — Next.js frontend

### Unit 09 — Application shell

- **Builds:** The Next.js app at `/Users/user/Local Sites/liftedpath/frontend` with TypeScript, Tailwind, the CSS variables from `ui-context.md`, the three fonts, and a static header, footer, and home frame. No WordPress fetch yet.
- **Boundary:** Frontend shell.
- **Depends on:** 02.
- **Visible result:** `http://localhost:3000` shows the LiftedPath frame, type, and color at desktop and 390px.

### Unit 10 — WordPress client

- **Builds:** `lib/wp` fetch, parsers, typed page and design models, ISR defaults, and env keys.
- **Boundary:** Frontend data.
- **Depends on:** 04, 09.
- **Visible result:** A server component prints a page title fetched from local WordPress.

### Unit 11 — Header and footer from WordPress

- **Builds:** Logo, nested menu, mobile dialog, Donate action, and footer columns from navigation and settings.
- **Boundary:** Frontend chrome.
- **Depends on:** 08, 10.
- **Visible result:** Changing a menu label in wp-admin changes the Next.js header after revalidation.

### Unit 12 — Home page

- **Builds:** React components for the Unit 05 and 06 blocks, composed by the home page payload, including core blocks the seed uses.
- **Boundary:** Frontend page.
- **Depends on:** 11.
- **Visible result:** The home page matches Design 01 section order and reads its copy from WordPress.

### Unit 13 — About, What We Do, Learning, Impact

- **Builds:** Those four routes from their page payloads, plus the inner-page hero, pillars, goals, stewardship, and FAQ components.
- **Boundary:** Frontend pages.
- **Depends on:** 12.
- **Visible result:** Each route renders its own WordPress page.

### Unit 14 — Pathway and initiative pages

- **Builds:** The eleven child routes under `/learning` and `/what-we-do`, using the shared page pattern. Four learning pathways, Path Guide, Mentorship & Coaching, Community Initiatives, and its four initiatives.
- **Boundary:** Frontend pages.
- **Depends on:** 13.
- **Visible result:** Each child URL shows that page’s title and blocks, not a hardcoded parent.

### Unit 15 — Contact and donate

- **Builds:** The LiftedPath Forms plugin, the contact and donate pages, styled notification email, and hosted checkout through Stripe, Paystack, or Flutterwave. The active provider, keys, recipient, subject, and message are edited in wp-admin. Card numbers stay with the provider.
- **Boundary:** Forms. The plugin, the theme’s form markup, and the Next.js form routes ship together because a submission is one behavior.
- **Depends on:** 07, 08, 11.
- **Visible result:** A contact message creates a private submission and a styled email. A gift opens the chosen provider’s payment page, and a confirmed payment is recorded and emailed.

### Unit 16 — Draft preview

- **Builds:** Preview link in the editor, token exchange, Next.js draft mode, draft fetch with the application password.
- **Boundary:** Preview.
- **Depends on:** 10, 12.
- **Visible result:** An unpublished change is visible on the preview URL and hidden on the public URL.

### Unit 17 — On-demand revalidation

- **Builds:** `inc/revalidate.php` and `POST /api/revalidate`, covering page, navigation, settings, and global styles saves.
- **Boundary:** Freshness. Theme hook plus one route handler.
- **Depends on:** 11, 16.
- **Visible result:** Publishing a page updates the public Next.js page without waiting for the hourly revalidate.

### Unit 18 — Metadata, sitemap, redirects

- **Builds:** Per-page title and description from Yoast, with the page SEO fields as the fallback, Open Graph image, `sitemap.xml`, `robots.txt`, `/llms.txt`, a redirect list, and the LiftedPath Search & verification screen for the organization summary and webmaster codes.
- **Boundary:** Frontend discovery.
- **Depends on:** 13.
- **Visible result:** View source on a page shows the editor-supplied title and description. The sitemap lists the public paths. A saved verification code or search title is on the public site on the next visit.

### Unit 19 — Motion

- **Builds:** GSAP entrance on the hero and sections, CSS marquee, and the reduced-motion path.
- **Boundary:** Frontend motion.
- **Depends on:** 12, 13, 14, 15.
- **Visible result:** The home page animates once on entry. With reduced motion, content is present immediately and the marquee does not scroll.

### Unit 20 — Editor round-trip and speed

- **Builds:** The success-criteria pass. Fix only the gaps that pass fails.
- **Boundary:** Verification across the finished path.
- **Depends on:** 17, 18, 19.
- **Visible result:** An editor changes a heading, an image, a menu item, and a palette color in wp-admin, and the public page shows each change. The home page is served as static HTML.

## Spec File Rule

Write the spec, then code. The spec’s verification checklist is the unit’s definition of done. Update `progress-tracker.md` when the checklist passes.
