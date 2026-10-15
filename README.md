# LiftedPath

Public website for the LiftedPath Empowerment Foundation. Editors work in WordPress. Visitors receive a Next.js site that reads the WordPress REST API.

This repository has two deployable parts:

| Part | Path | Host |
| --- | --- | --- |
| WordPress block theme | Repository root | SiteGround (wp-admin, media, and the REST API) |
| Next.js public site | `frontend/` | Vercel |

Connect the GitHub repository [E-Crea8/liftedpath](https://github.com/E-Crea8/liftedpath) to Vercel and set the **Root Directory** to `frontend`. Vercel builds that Next.js app. The theme root is PHP, so Vercel should not use the repository root as the project root.

## How a page is published

1. An editor changes a page, a menu, a global style, or a LiftedPath setting in wp-admin.
2. WordPress stores that content and exposes it on the REST API under `liftedpath/v1`.
3. The next visit reads that content from WordPress. Published pages are not stored as a build snapshot.
4. On save, WordPress also calls `frontend` at `/api/revalidate` so any route cache is dropped.

The exact request path, and the rule to leave it unchanged, is `LIVE-WORDPRESS-UPDATES.md`.

Draft preview uses `/api/preview` and a WordPress application password. Contact and donate forms post to Next.js, which calls WordPress. Card details stay on the payment provider’s hosted page.

## WordPress theme

The theme is LiftedPath `0.18.0`, a block theme. It requires WordPress 6.7 or newer (built on 7.1) and PHP 8.2. Advanced Custom Fields PRO supplies the fields. The LiftedPath Forms plugin, which lives outside this repository, handles contact, donate, notification email, and hosted checkout. Yoast SEO is the per-page search editor. Next.js prints the public tags.

Direct visits to the WordPress host show the block template as a readable fallback. Production traffic for the public domain goes to Vercel.

### Theme root

| File | Role |
| --- | --- |
| `style.css` | Theme header required by WordPress, plus the small amount of editor CSS that `theme.json` cannot express |
| `theme.json` | Palette, type, spacing, radii, and layout. The content column is 1120px |
| `functions.php` | Theme setup and includes. Text domain `liftedpath` |
| `index.php` | Silence for directory requests |
| `readme.txt` | WordPress theme readme |
| `phpcs.xml.dist` | PHP coding standards config |
| `LICENSE` | GPL v2 or later. The theme is based on Underscores |
| `Lifted Path Design System.md` | Brand source |
| `AGENTS.md`, `CLAUDE.md` | Entry notes for coding agents in this workspace |
| `.gitignore` | Keeps secrets, dependencies, and the local-only folders out of GitHub |

### `templates/` and `parts/`

Block templates and template parts for the editor canvas and the WordPress fallback:

- `templates/index.html` — default template
- `templates/page.html` — page template
- `parts/header.html` — header
- `parts/footer.html` — footer

### `blocks/`

Each folder is one ACF block: `block.json` plus `render.php`. The block name is the contract the matching React component implements.

| Block | Public section |
| --- | --- |
| `hero`, `page-hero` | Home and inner heroes, including an optional background video |
| `marquee` | Scrolling phrase band |
| `stat`, `stat-group` | Impact figures |
| `cta-band` | Closing call to action |
| `about-tabs` | Story, mission, and vision |
| `program-cards` | Overlay initiative cards |
| `pathway-cards` | Learning pathway grid |
| `work-layout` | What We Do / Learning work pages (sidebar, contact card, article) |
| `footer-cta-2` | Work-page closing band (row layout) with optional photograph + overlay |
| `action-grid` | How the foundation works |
| `sponsor-callout` | Sponsor line |
| `process` | Process steps |
| `stewardship` | How gifts are used |
| `pillars`, `goals` | About page |
| `faq` | Donor questions |
| `contact-form` | Contact form |
| `donate-panel` | Gift intent and hosted checkout |

Core blocks (paragraph, heading, image, buttons, list, quote, group, columns, spacer, separator, custom HTML) are also allowed in page content.

### `inc/`

| File | Role |
| --- | --- |
| `acf.php` | Options page and field-group loading |
| `blocks.php` | Block registration and shared block helpers |
| `content-types.php` | Private `submission` type for contact and donate records |
| `fonts.php` | Plus Jakarta Sans and Stack Sans Headline for the editor |
| `preview.php` | One-time preview token and the authenticated draft read |
| `rest.php` | Public REST routes for a page, the path index, design tokens, navigation, and organization settings |
| `revalidate.php` | Save hooks that call the Next.js revalidate webhook |
| `seed.php` | Inserts the public pages and navigation when they are missing |
| `work-layout.php` | What We Do / Learning work-layout seed catalog and page migration |
| `seo.php` | Resolved search fields, the Search & verification screen, and `GET /liftedpath/v1/seo` |
| `template-functions.php` | Small template helpers |

### `acf-json/`

Versioned ACF field groups and the LiftedPath settings page. Edit fields in wp-admin. ACF writes the JSON. Content stays in the database. This folder holds the schema.

### `assets/`

- `assets/blocks.css` — editor and fallback styles for the sections
- `assets/editor.css` — canvas styles so wp-admin uses the public type and color
- `assets/fonts/` — Plus Jakarta Sans and Stack Sans Headline, with their license files

## `frontend/`

Next.js 16 App Router application. TypeScript, React 19, Tailwind CSS 4, and GSAP. This is the directory Vercel builds.

```bash
cd frontend
cp .env.example .env.local
npm install
npm run dev
```

The dev server runs at `http://localhost:3000`. `npm run lint` and `npm run build` check the app. Dependencies, `.next`, and `.env.local` are gitignored. `.env.example` lists the variable names with empty secrets.

### Environment

Set these in `.env.local` locally and in the Vercel project for production. Do not commit the values.

| Variable | Role |
| --- | --- |
| `WORDPRESS_API_URL` | WordPress REST root. Production must be `https://cms.liftedpathfoundation.org/wp-json` (never the public www host) |
| `WORDPRESS_PREVIEW_USER` | Administrator username |
| `WORDPRESS_PREVIEW_APP_PASSWORD` | Application password for that user, not the wp-admin password |
| `REVALIDATE_SECRET` | Shared with the WordPress option `liftedpath_revalidate_secret` |
| `PREVIEW_SECRET` | Shared with the WordPress option `liftedpath_preview_secret` |
| `FORM_SECRET` | Shared with the WordPress option `liftedpath_forms_secret` |

LiftedPath settings → Vercel Environment Variables shows these six values for the WordPress site you are logged into. Copy them from that tab.

`next.config.ts` allows `next/image` to load `/wp-content/uploads/` from the host in `WORDPRESS_API_URL`.

### App shell

| Path | Role |
| --- | --- |
| `app/layout.tsx` | Header, footer, design tokens, and default metadata |
| `app/sitemap.ts` | Public sitemap |
| `app/robots.ts` | Robots file pointing at the sitemap |
| `app/llms.txt/route.ts` | Plain-language site summary from LiftedPath settings |
| `app/not-found.tsx` | Unknown URL frame |
| `proxy.ts` | Redirects stored in WordPress |
| `styles/globals.css` | Tailwind layers and design tokens |
| `styles/blocks.css` | Section styles |
| `styles/forms.css` | Contact and donate styles |
| `styles/preview.css` | Draft-preview banner |
| `public/fonts/` | The same two families, served to visitors from the Next.js app |

### Public routes

| Route | Page |
| --- | --- |
| `app/(site)/page.tsx` | Home |
| `app/(site)/about/page.tsx` | About |
| `app/(site)/what-we-do/page.tsx` | What We Do |
| `app/(site)/what-we-do/[slug]/page.tsx` | A community initiative |
| `app/(site)/learning/page.tsx` | LiftedPath Learning |
| `app/(site)/learning/[slug]/page.tsx` | A learning pathway |
| `app/(site)/impact/page.tsx` | Impact |
| `app/(site)/contact/page.tsx` | Contact |
| `app/(site)/donate/page.tsx` | Donate |
| `app/(site)/donate/complete/page.tsx` | Return from hosted checkout |
| `app/(site)/not-found.tsx` | Missing page inside the site frame |

### Route handlers

| Route | Role |
| --- | --- |
| `app/api/revalidate/route.ts` | On-demand cache refresh from WordPress |
| `app/api/preview/route.ts` | Draft preview |
| `app/api/contact/route.ts` | Contact form |
| `app/api/donate/route.ts` | Donate form, then the provider’s hosted page |
| `app/api/payments/confirm/route.ts` | Records a confirmed gift once |

### Components

| Folder | Role |
| --- | --- |
| `components/blocks/` | One component per LiftedPath block, plus core content, icons, and shared parts |
| `components/layout/` | Header, footer, logo, donate button, and the missing-page frame |
| `components/pages/WordPressPage.tsx` | Renders a WordPress page payload in block order |
| `components/motion/` | GSAP entrance motion and the count-up figure. Reduced motion shows the content still |
| `components/preview/PreviewBanner.tsx` | Banner while a draft preview is open |
| `components/seo/` | Site identity and page schema |

### `lib/`

| Path | Role |
| --- | --- |
| `lib/wp/client.ts` | Fetches WordPress |
| `lib/wp/types.ts` | Parsed types |
| `lib/wp/map.ts` | Turns REST JSON into those types |
| `lib/wp/sanitize.ts` | Strips unsafe HTML from WordPress content |
| `lib/wp/children.ts` | Child pages under a hub |
| `lib/blocks/fields.ts` | Reads block fields |
| `lib/blocks/html.ts` | Block HTML helpers |
| `lib/tokens.ts` | Applies the WordPress design payload to CSS variables |
| `lib/forms.ts` | Contact and donate request helpers |
| `lib/seo/json.ts`, `lib/seo/llms.ts` | Structured data and the `llms.txt` body |

## Vercel

1. Import `E-Crea8/liftedpath`.
2. Set the Root Directory to `frontend`.
3. Leave the framework as Next.js. The build command is `npm run build`.
4. Add the environment variables from the table above, with the production WordPress URL and the shared secrets.
5. Deploy.

WordPress and media stay on the WordPress host. Only `frontend/` is the Vercel project.

## SiteGround

The theme deploys with the manual action **Deploy to SiteGround** (`.github/workflows/deploy-siteground.yml`). Open Actions, choose the workflow, and run it on `main`.

| Input | What to enter |
| --- | --- |
| Deployment target | `staging`, `live`, or `both` |
| Confirmation | `DEPLOY` |
| Deployment mode | `changed_files` (last commit, or a range), `specific_files`, or `full` |
| Commit range | Optional, such as `677a07e..499619a`. Empty uses the last commit |
| Specific files | Theme paths, separated by commas, when the mode is `specific_files` |

`changed_files` and `specific_files` send theme files only: PHP, CSS, JS, HTML, JSON, SVG, fonts, and `readme.txt`. `frontend/` is never synced. A commit that only changes the Next.js app skips the SiteGround upload. `full` syncs the theme and leaves `frontend/`, docs, and this workflow on GitHub.

Add these repository secrets before the first run. The port secrets are optional and default to SiteGround’s `18765`.

| Secret | Staging | Live |
| --- | --- | --- |
| Private key | `SITEGROUND_SSH_KEY` | `SITEGROUND_LIVE_SSH_KEY` |
| SSH host | `SITEGROUND_HOST` | `SITEGROUND_LIVE_HOST` |
| SSH port | `SITEGROUND_PORT` | `SITEGROUND_LIVE_PORT` |
| SSH user | `SITEGROUND_USER` | `SITEGROUND_LIVE_USER` |
| Theme directory on the server | `SITEGROUND_PATH` | `SITEGROUND_LIVE_PATH` |
| WordPress root, for `wp cache flush` | `SITEGROUND_WP_PATH` | `SITEGROUND_LIVE_WP_PATH` |

`SITEGROUND_PATH` is the remote `wp-content/themes/liftedpath` directory. The public Next.js site still deploys from `frontend/` on Vercel.

## Local-only folders

These directories remain on the development machine and are listed in `.gitignore`. They are not on `main`.

| Folder | What it is |
| --- | --- |
| `context/` | Product, architecture, design tokens, specs, and the progress tracker |
| `Six-File+Context+Methodology/` | The writing method those context files follow |
| `liftedpath-design-main/` | Design 01 reference export |
| `logos/` | Source logo files. The live logo is the one set in WordPress |

Also outside this repository:

- The LiftedPath Forms plugin at `wp-content/plugins/liftedpath-forms`
- WordPress core, uploads, and other plugins
- `.env.local`, application passwords, and the local wp-admin password

## Git

`main` is the branch Vercel and this theme both track. The SSH remote is `git@github-ecrea8:E-Crea8/liftedpath.git`.
