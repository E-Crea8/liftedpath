# Architecture Context

## Product Shape

LiftedPath is one product with two deployable applications.

WordPress is the content hub. Editors work in the block editor, the Site Editor, the media library, and ACF screens. The theme is a block theme, so colors, type, spacing, templates, and template parts are WordPress data.

Next.js is the public renderer. It requests content from the WordPress REST API and serves HTML from Vercel with Incremental Static Regeneration. Visitors never receive a PHP-rendered theme page.

This is decoupled delivery: one content hub, a custom frontend, and the WordPress editor kept intact. The theme still registers a real block template so the editor canvas and a direct visit to the WordPress host show a readable fallback. Production traffic for the public domain goes to Vercel.

Local facts, measured on this machine:

- WordPress 7.1.3, PHP 8.2.29, nginx, at `http://liftedpath.local`
- REST index: `http://liftedpath.local/wp-json/`
- Theme directory: this workspace
- ACF PRO 6.7.1 is active and required for fields. LiftedPath Forms handles the two public forms. Yoast SEO is the per-page search editor. The public tags are printed by Next.js.

## Stack

| Layer | Technology | Role |
| --- | --- | --- |
| CMS | WordPress 7.1.3 | Content, media, users, navigation, global styles |
| Theme | Block theme started from Underscores | `theme.json`, templates, parts, patterns, blocks, ACF JSON |
| Theme language | PHP 8.2 | Block registration, CPT, REST shaping, mail, webhooks |
| Structured fields | ACF PRO | Options, repeaters, block fields, REST exposure. Required for fields |
| Per-page search editor | Yoast SEO | Title, description, and social image in wp-admin. Next.js prints the public tags |
| Content API | WordPress REST API | Pages, media, navigation, global styles, settings, submissions |
| Public app | Next.js App Router + TypeScript | Routing, rendering, metadata, sitemap, preview, revalidation |
| UI | Tailwind CSS + shadcn/ui | Tokens and accessible primitives, restyled to the LiftedPath palette |
| Motion | GSAP | Entrance and scroll animation on the public site |
| Public cache | Next.js ISR (time-based plus on-demand) | Fast HTML, refreshed when WordPress publishes |
| CMS hosting | SiteGround | PHP, MySQL, media, wp-admin |
| Frontend hosting | Vercel | Next.js production and preview deployments |
| Local CMS | Local WP | `http://liftedpath.local` |
| Local frontend | Next.js dev server | `http://localhost:3000`, app directory outside this theme |

## Decisions

1. **Block theme, Underscores as the starter.** Generate the theme at [underscores.me](https://underscores.me/) with slug `liftedpath`, theme name LiftedPath, author LiftedPath Empowerment Foundation, no WooCommerce boilerplate, and no Sass. Then make it a block theme: `theme.json`, `templates/index.html`, and template parts. Keep the Underscores PHP bootstrap (`style.css` header, `functions.php`, `inc/`). Drop the classic sidebar layouts, `js/navigation.js`, and the custom header. The theme is the theme itself. It is never a child of Underscores.
2. **Editor control is the point of the theme.** Anything an editor is allowed to change is stored in WordPress (post content, post meta, options, navigation, global styles, media) and leaves through the REST API. The Next.js components read that payload.
3. **REST, not a second API plugin.** Pages, media, and the block editor are a natural fit for the core REST API, and ACF PRO can attach its fields to those responses. A GraphQL plugin would be a second moving part this site does not need.
4. **ACF Blocks registered with `block.json`.** Each LiftedPath section is an ACF block on the current block API (`apiVersion` 3), with its field group in `acf-json/`. The block name is the contract the React component implements. Core blocks (paragraph, heading, image, buttons, list, quote, group, columns, spacer, separator, custom HTML) are also allowed inside page content and have React counterparts.
5. **ISR with on-demand revalidation.** Public pages are static. A WordPress save sends a webhook to a Next.js route with a shared secret. That route revalidates the changed path and any page that lists it (home, the parent hub, the sitemap). A time-based revalidate covers a missed webhook. Draft preview uses Next.js draft mode and a WordPress application password, and it is the only public path that renders on each request.
6. **The browser talks to Next.js.** Contact and donate forms post to Next.js route handlers. Those handlers validate input and call WordPress. The WordPress origin stays a server-side dependency.
7. **Styling system.** Tailwind utilities consume CSS variables. shadcn/ui supplies behavior for button, dialog, tabs, accordion, and form controls, restyled to these tokens. CSS Modules are reserved for a component whose GSAP or marquee CSS cannot be expressed as utilities.
8. **Frontend location.** The Next.js app lives at `/Users/user/Local Sites/liftedpath/frontend`, outside `wp-content`, so `node_modules` is never inside a public theme directory.
9. **Payments.** Donate records an intent. It does not charge a card.
10. **Global styles authority.** Administrators edit tokens in the Site Editor. Editors edit content. The resolved `theme.json` plus user global styles are what Next.js applies.

## System Boundaries

WordPress theme, this directory:

- `style.css` — theme header and the small amount of editor-facing CSS that `theme.json` cannot express
- `functions.php` — theme setup, includes, text domain `liftedpath`
- `theme.json` — palette, typography, spacing, radii, layout, element styles
- `templates/` — block templates (`index.html`, `front-page.html`, `page.html`, `single.html`, `404.html`)
- `parts/` — header and footer template parts
- `patterns/` — starter patterns for home and inner pages
- `blocks/` — one folder per ACF block: `block.json`, `render.php`, editor script only when the block needs it
- `acf-json/` — versioned field groups and the options page
- `inc/setup.php` — theme supports, image sizes, menus
- `inc/content-types.php` — the private `submission` type
- `inc/rest.php` — published page, path index, resolved design tokens, navigation, and the public organization settings. `marquee_phrases` is on that settings route. `form_recipient_email` and `share_image` stay off it.
- `inc/seo.php` — resolved page search fields, the Search & verification screen, and `GET /liftedpath/v1/seo`.
- `inc/preview.php` — editor preview link, one-time token, and the authenticated draft page read.
- `inc/revalidate.php` — save hooks for pages, navigation, settings, global styles, Yoast page fields, and Search & verification that call the Next.js webhook
- `inc/forms.php` — not used. Contact, donate, email, and payment routes live in the LiftedPath Forms plugin.
- `assets/editor.css` — editor canvas so wp-admin resembles the public type and color
- `context/` — these documents

Next.js app, `/Users/user/Local Sites/liftedpath/frontend`:

- `app/` — routes matching the information architecture, route handlers for preview, revalidation, contact, donate, and `/llms.txt`, plus `sitemap.ts`, `robots.ts`, and `proxy.ts`
- `components/ui/` — shadcn primitives
- `components/blocks/` — one component per block name (`liftedpath/hero` → `Hero.tsx`)
- `components/layout/` — header, footer, menu
- `components/preview/` — draft-mode banner
- `lib/wp/` — fetch client, types, mappers, sanitizer
- `lib/tokens.ts` — applies the WordPress token payload to CSS variables, with the values in `ui-context.md` as the build-time fallback
- `styles/globals.css` — Tailwind layers and the token definitions

LiftedPath Forms plugin, `/Users/user/Local Sites/liftedpath/app/public/wp-content/plugins/liftedpath-forms`:

- Settings screen under Submissions → Form settings. An administrator edits the notification recipient, subject, message, sender, public site URL, and the active payment provider.
- Secret keys and `FORM_SECRET` stay in WordPress options and the Next.js server environment. They are not in the public REST payload or the browser.
- Checkout uses the provider’s hosted page: [Stripe Checkout](https://docs.stripe.com/payments/checkout), [Paystack](https://paystack.com/docs/payments/accept-payments/), or [Flutterwave](https://developer.flutterwave.com/). Notification mail follows the same idea as [WPForms notifications](https://wpforms.com/developers/): a recipient, a subject, a message with smart tags, and an HTML email.

WordPress core owns users, media, revisions, the block editor, global styles, and navigation (`wp_navigation`). The theme does not reimplement them.

## Content Model

### Pages

Every public route is a page. The WordPress permalink is the public path:

- Home is the static front page.
- `learning` is the parent of `lift-root`, `lift-grow`, `lift-beyond`, and `lift-equip`.
- `what-we-do` is the parent of `path-guide`, `mentorship-coaching`, `community-initiatives`, `lift-beyond-barriers`, `lift-beyond-borders`, `orphanage-outreach`, and `share-the-joy-of-the-season`.

Page content is a list of blocks. ACF fields that belong to the whole page live on the page, not in a parallel store: `seo_title`, `seo_description`, `hero_summary`, and `menu_subtitle`. `menu_subtitle` is the optional line under that page in the site menu.

### LiftedPath settings (ACF options)

One options page, menu slug `liftedpath-settings`, holds organization facts and shared fragments. Only an administrator (`manage_options`) can edit it. Field names:

- Header tab: `logo`, an attachment ID, and `logo_height`, the header logo height in pixels (64–160)
- Footer tab: `footer_logo` (optional; the header logo is used when it is empty), `footer_logo_height` (48–200), `footer_wordmark` (the large bottom word), `location_heading`, plus the tagline, phone, email, address, copyright text, and registration number shown in the footer
- `legal_name`, `short_name`, `tagline`, `registration_number`
- `phone`, `email`, `postal_address`
- `copyright_text`
- `marquee_phrases`, a repeater of `phrase`
- `share_image`, an attachment ID
- `form_recipient_email`, which defaults to the public email

### Submissions

Custom post type `submission`, `public` false, `show_in_rest` true for authenticated users only.

- `submission_type`: `contact` or `donate`
- `full_name`, `email`, `reason`, `message`, `currency`, `amount`, `frequency`, `designation`
- Status stays private. Editors read submissions in wp-admin. Anonymous REST requests are rejected.

### Posts

Core posts stay enabled for a future news item. They are not in the first navigation and have no public index in this version. Their REST route still works.

### Navigation

Two navigation posts: `primary` and `footer`. Editors change them in the Site Editor Navigation screen. The classic Appearance → Menus screen is not used. `GET /wp-json/liftedpath/v1/navigation` returns `{ primary, footer }`. Each item is `{ label, path, detail, children }`. `path` is a public path, an external `http` or `https` URL, or `null` for a group heading. `detail` is the linked page’s `menu_subtitle`, or an empty string. `GET /wp-json/liftedpath/v1/settings` returns the public organization facts, the logo, `logo_height`, and `marquee_phrases` as a list of strings. The header logo is the Header tab image, then the Site Editor custom logo. The footer logo is the Footer tab image, then the header logo. `footer_wordmark` is the large footer word. Next.js renders the header and footer from those payloads. An empty marquee block uses `marquee_phrases`.

`primary` is Home, About Us, What We Do (LiftedPath Learning and its four pathways, Path Guide and Mentorship & Coaching, Community Initiatives and its four initiatives), Impact, and Contact Us. Donate Now is the `/donate` page, not a primary item. `footer` is two groups: Quick Links (Home, About Us, What We Do, Impact, Contact Us, Donate) and What We Do (LiftedPath Learning, Path Guide, Community Initiatives). Learning stays a top-level page at `/learning` even though the header lists it under What We Do.

### Media

Images and uploaded background videos live in the WordPress media library and are served from the SiteGround (or Local) uploads URL. Next.js `images.remotePatterns` allows that host. Block image fields and the uploaded video field stay attachment IDs. The page resolves them with `GET /wp-json/wp/v2/media?include=`, including `mime_type`. Editors set alt text in the media library. The frontend requires alt text on meaningful images and treats decorative images as empty alt. A hero can also store a video link, such as a Cloudinary MP4. The link is used first. An uploaded file is used when the link is empty. The photograph stays behind the video and is what visitors see if the video cannot play, or when they prefer less motion. Mute, auto replay, and autoplay are fields on the hero.

### Blocks

Namespace `liftedpath`. Each block’s `block.json` sets `apiVersion` to 3, `acf` render template, and `supports` that match what the React component actually honors (anchor, align where the design uses it, spacing where the section participates in the spacing scale).

| Block | Used for |
| --- | --- |
| `liftedpath/hero` | Home hero |
| `liftedpath/page-hero` | Inner page hero |
| `liftedpath/stat` | Single figure |
| `liftedpath/stat-group` | Repeating figures |
| `liftedpath/marquee` | Phrase loop |
| `liftedpath/about-tabs` | Mission, vision, commitment |
| `liftedpath/program-cards` | Initiative cards |
| `liftedpath/cta-band` | Sponsor and join bands |
| `liftedpath/action-grid` | Give, Partner, Empower, Reachout |
| `liftedpath/sponsor-callout` | The ₦50,000 callout |
| `liftedpath/stewardship` | Numbered transparency points |
| `liftedpath/process` | Numbered steps |
| `liftedpath/pillars` | About pillars |
| `liftedpath/goals` | Future goals |
| `liftedpath/faq` | Donor questions |
| `liftedpath/contact-form` | Contact fields and reasons |
| `liftedpath/donate-panel` | Gift configurator |
| `liftedpath/rich-text` | Constrained freeform area when a page needs prose between sections. Core blocks are preferred when they are enough |

`render.php` returns semantic HTML for the editor and the WordPress fallback. The React component is the production renderer. Both read the same fields.

Unit 05 field names:

| Block | Fields |
| --- | --- |
| `liftedpath/hero` | `image`, `video_url`, `video_muted`, `video_loop`, `video_autoplay`, `video`, `portraits` (repeater of `image`), `eyebrow`, `heading`, `intro`, `button`, `figure`, `label`, `detail` |
| `liftedpath/page-hero` | `image`, `video_url`, `video_muted`, `video_loop`, `video_autoplay`, `video`, `eyebrow`, `heading`, `intro`, `breadcrumb_label` |
| `liftedpath/stat` | `figure`, `label`, `detail` |
| `liftedpath/stat-group` | Inner `liftedpath/stat` blocks |
| `liftedpath/marquee` | `phrases` (repeater of `phrase`). Empty uses settings `marquee_phrases` |
| `liftedpath/cta-band` | `tone` (`photo`, `teal`, `paper`), `image`, `eyebrow`, `heading`, `text`, `button` |

Unit 06 field names:

| Block | Fields |
| --- | --- |
| `liftedpath/about-tabs` | `primary_image`, `secondary_image`, `figure`, `figure_label`, `eyebrow`, `heading`, `intro`, `tabs` (repeater of `label`, `title`, `body`; first tab selected; six maximum), `primary_link`, `secondary_link` |
| `liftedpath/program-cards` | `eyebrow`, `heading`, `intro`, `cards` (repeater of `image`, `kicker`, `title`, `line`, `link`), `callout_heading`, `callout_text`, `callout_button`. An empty callout heading hides that cell |
| `liftedpath/action-grid` | `eyebrow`, `heading`, `intro`, `title_weight` (`400`, `500`, `600`, or `700`; blank uses Bold), `items` (repeater of `title`, `text`, `link`, `hover_color`). The first four items use the give, partner, empower, and reachout symbols. A blank hover color keeps the Design 01 tint for that position |
| `liftedpath/sponsor-callout` | `figure`, `text`, `link` |
| `liftedpath/stewardship` | `eyebrow`, `heading`, `intro`, `points` (repeater of `title`, `text`), `button`, `image`, `inset_image`, `note_label`. The call-note phone is settings `phone` |
| `liftedpath/process` | `eyebrow`, `heading`, `button`, `steps` (repeater of `title`, `text`) |

Unit 07 field names:

| Block | Fields |
| --- | --- |
| `liftedpath/pillars` | `eyebrow`, `heading`, `items` (repeater of `title`, `text`). The index is generated from the order |
| `liftedpath/goals` | `eyebrow`, `heading`, `intro`, `goals` (repeater of `title`, `text`). The index is generated from the order |
| `liftedpath/faq` | `eyebrow`, `heading`, `items` (repeater of `question`, `answer`). An empty answer still shows the question |
| `liftedpath/contact-form` | `eyebrow`, `heading`, `intro`, `form_eyebrow`, `form_heading`, `reasons` (repeater of `reason` and `label`), `button_label`, `note`. Reason values are `volunteer`, `sponsor`, `partner`, `in-kind`, and `other`. Phone, email, and office read settings `phone`, `email`, and `postal_address`. The public site posts the message through Next.js. The editor canvas does not submit |
| `liftedpath/donate-panel` | `eyebrow`, `heading`, `intro`, `one_off_label`, `monthly_label`, `amounts` (repeater of `amount`, `title`, `detail`; amount is naira), `usd_rate`, `gbp_rate`, `designations` (repeater of `designation` and `label`), `button_label`, `note`, `aside_image`, `aside_heading`, `aside_text`. Designation values are `greatest-need`, `education`, `skills`, `mentorship`, `orphanage`, and `season`. Rates are naira per one dollar and one pound. The public form also collects the visitor’s name and email, then opens hosted checkout. The editor canvas shows those fields and does not submit |

An image field stores an attachment ID. A link field stores `title`, `url`, and `target`. On these blocks that includes `button`, `primary_link`, `secondary_link`, `link`, and `callout_button`.

### Global styles payload

`GET /wp-json/liftedpath/v1/design` returns the resolved palette, font sizes, spacing sizes, and layout sizes after user global styles are merged, plus a `version` hash. Core default presets are left out. Keys are preset slugs (`palette.paper`, `fontSizes.display`, `spacingSizes.24`, `layout.contentSize`, `layout.wideSize`). A user preset with the same slug replaces the theme value. `version` is the SHA-256 of those four maps and does not include itself. Next.js stores that hash and maps the values onto the CSS variables in `ui-context.md`. Unknown keys are ignored. Missing keys fall back to the token table.

### Page payload

The frontend fetches a page by path through a theme route:

`GET /wp-json/liftedpath/v1/page?path=/learning/lift-root`

The path argument is required. `/` is the static front page. Drafts, private pages, and password-protected pages return 404. An unsafe path returns 400.

Response fields: `id`, `slug`, `path`, `title`, `status`, `modified` (RFC 3339), `parent_path`, `featured_image` (`null` or `{ id, url, alt, width, height }`), `seo_title`, `seo_description`, `seo_image` (the same shape as `featured_image`, or `null`), `seo_noindex`, `hero_summary`, and an ordered `blocks` array. `seo_title` and `seo_description` are the Yoast values when those fields are set, and the page SEO fields otherwise. `seo_image` is the Yoast social image, then the featured image, then the settings share image. Each block is `{ name, attributes, html }`. `attributes` are the ACF field values for LiftedPath blocks and the parsed attributes for core blocks. Nested blocks are `attributes.innerBlocks` in the same shape. `html` is the rendered block, sanitized with `wp_kses_post`, before React renders it.

The sitemap index is:

`GET /wp-json/liftedpath/v1/paths`

It returns `{ paths: [{ id, path, modified }] }` for published pages, one public path each.

`GET /wp-json/liftedpath/v1/seo` returns the public origin, organization summary, area served, profile links, share image, verification tokens, redirects, and `{ path, modified, title, description, indexable }` for published pages. Verification tokens are empty until an administrator saves them. The share image stays off the settings route.

## Rendering and Freshness

The published client in `lib/wp/client.ts` reads the page, path index, design, navigation, settings, media, and SEO routes. Each read revalidates after 3600 seconds. A page read is tagged `page` and the public path. The path index is tagged `sitemap`. The design read is tagged `design`. Navigation is tagged `nav`. Settings are tagged `settings`. Media is tagged `media`. The SEO read is tagged `seo`. `lib/tokens.ts` maps known design slugs onto the CSS variables in `ui-context.md` and leaves unknown or unsafe values on the stylesheet fallback. Draft fetches stay `no-store` and run only in draft mode.

| Kind of page | Strategy |
| --- | --- |
| Published pages | ISR. `revalidate` of 3600 seconds, plus on-demand revalidation |
| Draft preview | Server-rendered in draft mode, `no-store`, only with a valid preview cookie |
| Sitemap, robots | Regenerated with the same webhook |

Webhook contract:

1. `save_post`, navigation save, options save, and global-styles save call the Next.js route `POST /api/revalidate`.
2. The body carries the secret, the path or tag, and the action (`update` or `delete`).
3. The route checks the secret in constant time, expires the path and the index tags (`nav`, `settings`, `design`, `sitemap`, `media`, `seo`) immediately, and returns 200. The next visit renders the published content. A Search & verification save sends the `seo` tag. A Yoast field save on a published page sends that page’s path. WordPress stores the secret in the option `liftedpath_revalidate_secret`.
4. A failed webhook is logged in PHP and does not block the editor’s save. The time-based revalidate still refreshes the page.

Preview contract:

1. The theme filters `preview_post_link` for pages. The link is `{public site}/api/preview?token=` plus a one-time token. Only the SHA-256 of the token is stored, for 10 minutes. A draft opened on the WordPress preview URL redirects to that link.
2. `GET /api/preview` posts the token to `POST /wp-json/liftedpath/v1/preview/exchange`, enables draft mode, and redirects to the path WordPress returns.
3. While the draft-mode cookie is set, `getPage` reads `GET /wp-json/liftedpath/v1/preview?path=`. The response uses the page payload shape. An autosave, when one exists, supplies the unpublished title and block markup. The public path stays the saved page’s path. Draft mode serves that response `no-store` and does not write it into the published cache.
4. `GET /wp-json/liftedpath/v1/page` stays published-only.
5. Exit preview is a form action that clears the draft-mode cookie. It is not a link.
6. Preview requests authenticate to WordPress with an application password stored in the frontend environment, never in the theme files. They also send `PREVIEW_SECRET`. WordPress keeps that secret in the option `liftedpath_preview_secret`.

## Storage Model

- **MySQL:** pages, posts, submissions, post meta, ACF values, options, navigation, global styles, users, revisions.
- **`wp-content/uploads`:** media files. Next.js references their URLs and optimizes delivery with `next/image`.
- **Theme `acf-json/`:** field definitions. The database holds the content, the JSON holds the schema.
- **Vercel cache:** rendered HTML and fetch cache for published pages.
- **Environment variables:** `WORDPRESS_API_URL`, `WORDPRESS_PREVIEW_USER`, `WORDPRESS_PREVIEW_APP_PASSWORD`, `REVALIDATE_SECRET`, `PREVIEW_SECRET`. Local values live in `frontend/.env.local`. Production values live in the Vercel project. None of them are committed.

No Redis, object-cache plugin, or media-offload plugin is required for this version.

## Auth and Access Model

- Visitors are anonymous. The public site has no login.
- **Editor** can create and edit pages, submissions (read), media, and navigation.
- **Administrator** can also edit global styles, templates, theme files, plugins, and LiftedPath settings.
- The LiftedPath Forms plugin registers the mutating routes `POST /liftedpath/v1/contact`, `POST /liftedpath/v1/donate`, and `POST /liftedpath/v1/payments/confirm`. They accept only server-to-server calls that present `FORM_SECRET`. They do not trust a public cookie. `POST /liftedpath/v1/payments/webhook` is public and accepts a call only when the active provider’s signature matches. `GET /liftedpath/v1/forms` returns the provider name and whether payments are configured. It does not return keys.
- Read routes for published content are public. Draft content requires the preview user.
- Application passwords stay available on `liftedpath.local`, `localhost`, and `127.0.0.1` when WordPress would otherwise require HTTPS or a local environment type. Other hosts are unchanged.
- The local wp-admin account is a Local development account. Its password is not written into this repository, these context files, or the frontend environment.

## Hosting

| Environment | WordPress | Next.js |
| --- | --- | --- |
| Local | `http://liftedpath.local` | `http://localhost:3000` |
| Production | SiteGround, the foundation’s domain for wp-admin and media | Vercel, the public domain |

The public DNS name points at Vercel. `wp-admin` and `/wp-content/uploads` stay on the WordPress host. Exact production hostnames are recorded in `progress-tracker.md` when they exist.

The theme is versioned at [E-Crea8/liftedpath](https://github.com/E-Crea8/liftedpath). The SSH remote, branch names, and the frontend and forms plugin (both still outside git) are recorded in `context/github.md`.

CORS is unnecessary for content fetches because Next.js performs them on the server.

## Invariants

1. WordPress is the only source of truth for copy, media, navigation, page structure, submissions, and design tokens. The frontend caches and renders that data. It does not keep a second editable copy.
2. A visitor request for a published page is served by Next.js ISR. The theme’s PHP templates are the editor canvas and the fallback on the WordPress host, and they are not the production public site.
3. Every LiftedPath block has a `block.json`, a PHP render, an ACF field group, and a React component that share one block name. Shipping one side without the others is incomplete.
4. Colors, type, spacing, radii, and shadows in the UI come from the named tokens in `ui-context.md` and from the design payload WordPress returns. Components do not introduce new hex values.
5. ACF PRO is required for fields. LiftedPath Forms is the forms plugin. Yoast SEO is the per-page search editor and is recorded in `progress-tracker.md`. The theme still serves page SEO fields when Yoast is inactive. Adding another plugin is an architecture change recorded in `progress-tracker.md` before that plugin is installed.
6. The Next.js application lives outside this theme directory.
7. Secrets and the local admin password never enter git, context files, or client-side code.
8. Contact and donate endpoints validate and sanitize every field, store a private submission, and send mail. Donate starts a hosted checkout at the provider selected in LiftedPath Forms. The site never accepts or stores a card number.
9. Public HTML from the Custom HTML block or from `render.php` is sanitized before it is rendered in React.
10. GSAP and the marquee animation are disabled when the visitor prefers reduced motion.
11. An Editor can change content. Changing global styles, plugins, or theme files stays with an Administrator.
12. Field groups are loaded from `acf-json/` so the schema is in version control. Content stays in the database.

## References

- [WordPress developer handbook](https://developer.wordpress.org/)
- [Block editor handbook](https://developer.wordpress.org/block-editor/)
- [Theme handbook, block themes](https://developer.wordpress.org/themes/)
- [Underscores](https://underscores.me/)
- [ACF with Next.js](https://www.advancedcustomfields.com/blog/nextjs-wordpress/)
- [Headless CMS guide](https://wpvip.com/resources/ebooks/headless-cms-guide/)
- [Headless WordPress implementation guide](https://www.iflair.com/the-ultimate-guide-to-building-a-headless-wordpress/)
