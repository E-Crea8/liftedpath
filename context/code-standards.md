# Code Standards

These rules apply to both applications. The theme is PHP under this directory. The public app is TypeScript in `/Users/user/Local Sites/liftedpath/frontend`. Follow the WordPress coding standards, the block editor handbook, and the theme handbook for the installed WordPress 7.1.3.

Handbook: [https://developer.wordpress.org/](https://developer.wordpress.org/)

## General

- Build the unit described in its spec file. Leave neighboring features untouched.
- Keep one responsibility in each file. A block folder owns that block. A route handler owns that route.
- Fix the cause of a bug. A second condition that hides the bug is not a fix.
- Share a mapper, a token, or a component once the same code appears a second time. Do not invent a shared abstraction for a single call site.
- Name things with the domain’s words: `program`, `submission`, `stewardship`, `marquee`. The text domain and function prefix are `liftedpath`.
- Comments explain a constraint a reader cannot see from the code. They do not restate the line.
- Secrets, application passwords, and the local wp-admin password stay in environment variables and hosting dashboards.

## PHP and WordPress

- Target PHP 8.2. Use typed parameters and return types on new functions.
- Procedural hooks in `functions.php` and `inc/` use the prefix `liftedpath_`. Classes use the namespace `LiftedPath`.
- Sanitize on input (`sanitize_text_field`, `sanitize_email`, `absint`, `esc_url_raw`, `wp_kses_post` for rich text). Escape on output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
- Queries go through `WP_Query`, `get_posts`, or the REST layer. Write SQL only when those APIs cannot express the query, and then use `$wpdb->prepare`.
- Capability checks sit in front of every mutating REST callback. Public form routes authenticate the Next.js server with the shared secret, then sanitize.
- Nonces protect editor-triggered actions. Server-to-server routes use the shared secret, compared in constant time.
- Text strings that an editor does not already control through a field are wrapped in `liftedpath` translation functions.
- Enqueue editor CSS with `add_editor_style` and `wp_enqueue_style` on `enqueue_block_editor_assets`. The public Next.js app loads its own CSS. The theme does not enqueue a front-end framework.
- Register every block from its `block.json` via `register_block_type`. ACF field groups sync from `acf-json/`.
- `theme.json` is version 3. Use the settings and styles the WordPress 7.1 handbook documents. When a value belongs in `theme.json` (color, font size, spacing, radius, layout), put it there rather than in ad-hoc CSS.
- Image sizes are registered for the photographs the design actually uses (hero, card, portrait). Do not generate unused sizes.
- Admin notices are the recovery path when ACF PRO is inactive. The theme still loads.
- Follow the WordPress PHP coding standards for spacing, Yoda conditions, and file documentation headers on PHP files.

## TypeScript and Next.js

- `"strict": true`. A value of type `any` is a bug. Use `unknown` at the REST boundary and parse it into a named type before the rest of the app sees it.
- The WordPress response is untrusted input. Parse it in `lib/wp/` and export typed values. Components receive those types.
- Server Components are the default. Add `"use client"` only for a component that uses state, a browser event, or GSAP.
- GSAP runs inside a client component, cleans up on unmount, and checks `prefers-reduced-motion` before it animates.
- Route handlers do one job: revalidate, preview, contact, or donate.
- Data fetching for published pages uses the ISR options in `architecture.md`. Draft fetches are `no-store` and run only in draft mode.
- Environment variables used by server code are read on the server. `NEXT_PUBLIC_` is reserved for values that are safe to show a visitor. The WordPress application password and the revalidate secret are not public.
- Import aliases use `@/` for the frontend root.
- A block component file is named after the block, in PascalCase (`Hero.tsx`), and it is the only production renderer for that block name.

## React Components

- A component renders one piece of UI. Page files in `app/` compose blocks; they do not contain section markup.
- Props are a named interface next to the component.
- Interactive controls are real `button`, `a`, `form`, `input`, `label`, and `nav` elements. shadcn primitives are the starting point when they match the control (tabs, accordion, dialog, field).
- Images use `next/image` with the alt text from WordPress. Width and height, or `fill` inside a sized frame, are set so the layout does not jump.
- Internal links use `next/link` and the public path stored on the page.
- Lists of editor-controlled items render from arrays. A missing array renders nothing for that region, and the rest of the page still renders.

## Styling

- Tokens are CSS custom properties, defined once in `styles/globals.css` and overridden by the WordPress design payload when it loads.
- Tailwind classes refer to those variables (`bg-paper`, `text-teal`, `rounded-pill`). A hex value, an arbitrary color, or an arbitrary radius in a component is a defect.
- Spacing uses the scale in `ui-context.md`. A one-off pixel value needs a reason recorded in the spec for that unit.
- The content column is `max-w-content` (1120px) with a 24px gutter, 18px below 760px. Heroes, the marquee, and the footer background run full width.
- Focus is a 2px solid bronze outline with a 3px offset on every interactive element, including links that look like text.
- Editor CSS in the theme mirrors the same tokens so the block editor is a faithful canvas. It does not try to load Tailwind.

## API and Forms

- Validate and parse the body before any write. Reject unknown fields.
- Return one error shape: `{ "code": string, "message": string }` with an accurate HTTP status.
- Contact and donate writes create a `submission` and call `wp_mail`. Donate mail waits until the provider confirms the payment. A mail failure still keeps the submission. The mail error is logged for an administrator. The site never accepts or stores a card number.
- Rate-limit the form route handlers by IP in the Next.js process for this version (a small in-memory window is enough locally; note its limit in the unit spec).
- HTML that originated in WordPress passes through a sanitizer that allows the tags the editor can produce (paragraphs, lists, links, emphasis, headings, images with safe attributes). Event-handler attributes and scripts are stripped.

## Data and Storage

- Editable content, media references, and tokens live in WordPress.
- Field definitions live in `acf-json/`.
- Rendered HTML and fetch caches live on Vercel.
- Submissions are private posts. Their REST routes require authentication.
- Do not store form payloads in Next.js, in a file, or in `localStorage`.

## File Organization

Theme:

- `functions.php` loads includes and nothing else of substance.
- `inc/` holds one concern per file, as listed in `architecture.md`.
- `blocks/<name>/` holds that block’s `block.json`, `render.php`, and optional editor assets.
- `acf-json/` is machine-written by ACF’s local JSON feature. Edit field groups in wp-admin and commit the JSON ACF writes.
- `templates/`, `parts/`, and `patterns/` are block markup. Keep PHP out of them.

Frontend:

- `app/(site)/` holds the public routes.
- `app/api/` holds route handlers.
- `components/blocks/` maps one-to-one with block names.
- `components/layout/` holds header, footer, and the menu.
- `components/ui/` holds shadcn components. Add them with the shadcn CLI.
- `lib/wp/client.ts` fetches. `lib/wp/types.ts` holds the parsed types. `lib/wp/map.ts` turns REST JSON into those types.

## Block Contract

A new section is done when all of these exist:

1. `blocks/<name>/block.json` with `"name": "liftedpath/<name>"` and `"apiVersion": 3`.
2. ACF fields for that block, saved under `acf-json/`, with the field group shown in REST.
3. `render.php` that prints semantic, escaped HTML.
4. `components/blocks/<Name>.tsx` that renders the same fields.
5. The block is inserted in a pattern or a seeded page so an editor can find it.

Core blocks the frontend must render when they appear in page content: `core/paragraph`, `core/heading`, `core/image`, `core/buttons`, `core/button`, `core/list`, `core/quote`, `core/group`, `core/columns`, `core/column`, `core/spacer`, `core/separator`, `core/html`. Any other core block renders its sanitized HTML inside a plain wrapper until a spec adds a designed component.

## Verification

- PHP: the theme activates, `wp-admin` loads, and the changed REST route returns the documented shape. Run PHPCS against the WordPress standard when it is available.
- TypeScript: `npm run build` in the frontend passes, which includes the type check.
- UI: the changed route matches Design 01 at desktop and at a 390px-wide viewport, and it still works with reduced motion.
- Editor: the changed field or block is editable in wp-admin and the public page reflects it after revalidation.

## Dependencies

Add a PHP or npm dependency in the unit that first needs it, and name it in that unit’s spec. The expected frontend set, installed when its unit arrives, is Next.js, React, TypeScript, Tailwind CSS, shadcn/ui, and GSAP. The theme has no npm build.
