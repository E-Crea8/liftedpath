# Unit 10: WordPress client

## Goal

The Next.js app reads a published page, the path index, and the design payload from the local WordPress REST API, parses them into typed models, and caches those published reads for 3600 seconds. The home route shows the WordPress title of `/`.

## Design

The Unit 09 shell stays. The sample section adds one caption line, in muted type, above the existing eyebrow. That line is the fetched page title. The document title uses the page SEO title when WordPress has one. Design values from WordPress are written onto the same CSS variables already defined in `styles/globals.css`, so the frame keeps the Design 01 colors, type sizes, spacing, and 1120px column. Unknown design keys are ignored. A missing or unsafe value leaves the stylesheet token in place. This unit adds no motion. At desktop and at 390px the column, header, menu, and footer stay as they were in Unit 09.

## Implementation

### Environment

`frontend/.env.local` is not committed. It sets `WORDPRESS_API_URL` to `http://liftedpath.local/wp-json`. `WORDPRESS_PREVIEW_USER`, `WORDPRESS_PREVIEW_APP_PASSWORD`, `REVALIDATE_SECRET`, and `PREVIEW_SECRET` are present and empty. `frontend/.env.example` records the same keys and is committed. The local admin password is not written into either file. None of these values use a `NEXT_PUBLIC_` name.

### `lib/wp/types.ts`

Parsed models use camelCase. `Page` has `parentPath`, `featuredImage`, `seoTitle`, `seoDescription`, and `heroSummary`. A block is `{ name, html, attributes }`. Nested blocks stay on `attributes.innerBlocks`. Other attribute values are JSON. `PathIndex` is `{ paths: [{ id, path, modified }] }`. `Design` has `version`, `palette`, `fontSizes`, `spacingSizes`, and `layout`. `WordpressError` carries `code`, `message`, and `status`.

### `lib/wp/map.ts`

The REST body arrives as `unknown`. A page, path index, or design payload that does not match the contract throws `WordpressError` and is not returned. Empty block attributes stay an object. Keys that start with `_` are dropped. `html` is passed through `sanitizeHtml` before it leaves the mapper. A public path is `/` or lowercase segments separated by single slashes.

### `lib/wp/sanitize.ts`

Removes `script`, `style`, `iframe`, `object`, `embed`, `link`, `meta`, and `base` tags, event-handler attributes, and `javascript:` or `data:` URLs. The home route does not insert `html` into the document.

### `lib/wp/client.ts`

`getPage(path)`, `getPaths()`, and `getDesign()` run on the server. Each uses `use cache` and `cacheLife` with `revalidate: 3600` (`stale` 300 seconds, `expire` one day). `getPage` is tagged with `page` and the public path. `getPaths` is tagged `sitemap`. `getDesign` is tagged `design`. A non-OK WordPress response throws `WordpressError` using the API `code` and `message`. Draft reads and `POST /api/revalidate` are later units.

### `lib/tokens.ts`

Maps known palette, font-size, spacing, and `layout.contentSize` slugs onto the CSS variables in `context/ui-context.md`. A value must be a `#` color, a `px` or `rem` size, or a `clamp()` of rem and vw. Anything else is skipped.

### Home route and shell

`app/(site)/page.tsx` loads the page and the path index and passes the title into `HomeFrame`. `generateMetadata` uses the SEO title, then the page title. The root layout loads the design payload and sets the mapped variables on `html`, plus `data-design-version`. `next.config.ts` allows `next/image` to load `/wp-content/uploads/` on the host in `WORDPRESS_API_URL`. No remote image is rendered in this unit.

## Dependencies

- Unit 04 REST routes on `http://liftedpath.local`.
- Unit 09 application shell.
- Next.js cache components, already enabled. No new package.

## Verify when done

- [x] The home route shows the WordPress title `Home` for `/`
- [x] A child page parsed from the API keeps `parentPath` `/learning` for `/learning/lift-root`
- [x] The design version is a 64-character SHA-256 and known tokens still match Design 01
- [x] Published reads use a 3600 second revalidate and the tags above
- [x] Editor-controlled values for this unit are the REST page title, SEO title, and design payload
- [x] No invariant in `context/architecture.md` was broken
- [x] Desktop and 390px checked
- [x] Reduced motion checked: this unit adds no animation
- [x] `npm run build` and `npm run lint` pass
- [x] `context/progress-tracker.md` updated
