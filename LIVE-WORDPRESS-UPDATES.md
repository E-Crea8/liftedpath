# Live WordPress updates on the Next.js site

This is the flow that makes a published change in WordPress appear on the next load of the public site. It was confirmed live on 9 October 2026: adding a word to the Home hero heading appeared on `https://www.liftedpathfoundation.org/`, and restoring the original heading appeared on the next load.

## Leave this flow alone

Do not change, remove, replace, cache, or “simplify” this flow during any other update to the theme, the Next.js app, WordPress, or Vercel. Leave it in place no matter what else is being built.

Change it only when the user explicitly asks to change how a published WordPress edit reaches the public site.

That includes these files and the behavior described below:

- `frontend/lib/wp/client.ts`
- `frontend/proxy.ts`
- `frontend/app/api/revalidate/route.ts`
- `frontend/app/layout.tsx` (`export const instant = false`)
- `frontend/app/not-found.tsx`
- `frontend/app/(site)/page.tsx`
- `frontend/app/(site)/about/page.tsx`
- `frontend/app/(site)/what-we-do/page.tsx`
- `frontend/app/(site)/what-we-do/[slug]/page.tsx`
- `frontend/app/(site)/learning/page.tsx`
- `frontend/app/(site)/learning/[slug]/page.tsx`
- `frontend/app/(site)/impact/page.tsx`
- `frontend/app/(site)/contact/page.tsx`
- `frontend/app/(site)/donate/page.tsx`
- `frontend/app/(site)/donate/complete/page.tsx`
- `frontend/app/(site)/not-found.tsx`
- `inc/revalidate.php`
- `frontend/next.config.ts` settings `cacheComponents` and `partialPrefetching`

## What an editor save does

1. The editor publishes a page, menu, global style, or LiftedPath setting at `https://cms.liftedpathfoundation.org/wp-admin/`.
2. WordPress writes the content to its database.
3. The public REST routes under `/wp-json/liftedpath/v1/` serve that content. The home hero heading is `blocks[].attributes.heading` on `GET /wp-json/liftedpath/v1/page?path=/`.
4. On shutdown, `inc/revalidate.php` POSTs `{ secret, action, path or tag }` to `https://www.liftedpathfoundation.org/api/revalidate`. The public origin comes from LiftedPath Forms `public_site_url` through `liftedpath_preview_public_base()`. The secret in the WordPress option `liftedpath_revalidate_secret` must match the Vercel env `REVALIDATE_SECRET`.
5. `frontend/app/api/revalidate/route.ts` checks the secret, expires the index tags, and calls `revalidatePath` for the page, the root layout, the sitemap, robots, and `llms.txt`.
6. The save does not start a Vercel deployment. Pushing Next.js code to `main` deploys the app. A content edit only changes the WordPress database.

The webhook is an extra cache drop. The public page still reads WordPress on the next visit when the webhook fails. A failed webhook must not block the editor’s save. The last status is stored in `liftedpath_revalidate_status` without the secret and shown on LiftedPath settings.

## What a visit does

1. Vercel serves the Next.js app from `frontend/`. The project root on Vercel is `frontend`, not the theme root.
2. `frontend/proxy.ts` runs for HTML GET and HEAD requests. It sets:
   - `Cache-Control: private, no-store, max-age=0, must-revalidate`
   - `CDN-Cache-Control: no-store`
   - `Vercel-CDN-Cache-Control: no-store`
   - `x-liftedpath-read: request`
3. The page, navigation, settings, design, SEO, and media reads call `connection()` in `readOnRequest()` before the fetch. `connection()` waits for a real visitor, so the production build cannot finish the fetch and store the HTML.
4. `readJson()` then fetches with `cache: "no-store"`, sends `Cache-Control: no-cache`, and adds a unique `lp_fresh` query. A prerender bailout thrown inside `fetch` is rethrown with `unstable_rethrow`. It must not be turned into a normal error and stored.
5. The visitor receives the current WordPress content. A reload is the check that matters. A tab that is already open can keep the previous page for a few minutes.

`getPaths()` does not call `connection()` and does not add `lp_fresh`. It only feeds `generateStaticParams` at build time. Adding `connection()` there can hang the build.

Draft preview and the preview-token exchange stay on `cache: "no-store"` and do not use `readOnRequest()`.

`export const instant = false` stays on the root layout and the route files listed above. With Cache Components enabled, that is what lets those routes render on the request and keeps the instant-navigation check from treating `connection()` as a mistake. Leave it there.

## What must stay true

- Published reads stay outside `"use cache"`. `connection()` inside `"use cache"` does not keep the page fresh.
- `cache: "no-store"` is not enough by itself. With Cache Components, a finished fetch can still be written into the prerender when the segment is allowed to block. `connection()` is the stop.
- `lp_fresh` stays on the request-time reads. The plain page URL can return an older `modified` time even when SiteGround sends `x-proxy-cache-info: DT:1`. The unique query is how the public site bypasses that copy.
- Do not add a caching plugin to make the public site update. No cache plugin is part of this flow. Active plugins at the time of the live check were Advanced Custom Fields PRO, LiftedPath Forms, Yoast SEO, and Yoast SEO Premium.
- The SiteGround theme deploy does not publish the public pages. `.github/workflows/deploy-siteground.yml` never uploads `frontend/`. Use it only when theme PHP on the CMS host must change.

## How this was arrived at

These earlier attempts left the public site on an old copy. Do not put them back.

| Attempt | Result |
| --- | --- |
| Tagged `"use cache"` with a 60-second lifetime (`cd4d3d6`) | An editor could still wait up to a minute, and `draftMode()` was inside the cached function. |
| `fetch` with `cache: "no-store"` and no request wait (`818b592`) | The build finished the fetch and stored the HTML. The live homepage kept “Breaking Barriers tos Education & Opportunity” for about 19 hours after WordPress already said “to”. `revalidateTag` had nothing tagged to expire. |
| `io()` before the same fetch (`5cc0125`) | The commit was the Production deployment on Vercel, and the public URL still returned that same frozen page: 111141 bytes, `x-vercel-cache: HIT`, no `x-nextjs-postponed`. |

The working code is `621b4ed` on `main`: `connection()` for the rendered reads, `lp_fresh` on those fetches, and the no-store headers in `proxy.ts`.

A local production build of that code marks the public routes as dynamic and does not write the page heading into `.next`. The live check used the admin account on the CMS, confirmed the new response header `x-liftedpath-read: request`, changed the Home hero, reloaded the public homepage, and restored the original heading.
