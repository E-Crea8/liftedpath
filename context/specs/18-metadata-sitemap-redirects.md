# Unit 18: Metadata, sitemap, redirects, and verification

## Goal

View source on a public page shows the title and description stored in WordPress. `/sitemap.xml` lists the public paths. An administrator can edit search text, the local organization summary, redirects, and the codes for Google Search Console, Analytics, Tag Manager, Business Profile, and Bing. The next visit to the public site uses the saved values.

## Design

Public pages keep their current layout. This unit changes the document head, `/sitemap.xml`, `/robots.txt`, `/llms.txt`, and redirects. The new WordPress screen is a standard settings form under the LiftedPath menu. It has no public visual layout, so a 390px pass is not required.

## Implementation

### Per-page search text

Yoast SEO is the editor for a page’s search title, description, and social image. The public Next.js site prints the tags. The theme does not print Yoast’s front end.

`seo_title` and `seo_description` on the page remain the fallback when the matching Yoast field is empty. The page REST payload returns the resolved title and description, `seo_image` (Yoast social image, then the featured image, then the LiftedPath settings share image), and `seo_noindex`.

The first load writes a search title, description, and focus keyphrase for each public page into empty Yoast fields, and into the page SEO fields when those fields are still the original short seed. A later edit in Yoast is left as the editor saved it. Page content is not rewritten.

The block-preview pages and the sample page are set to noindex when Yoast’s index setting is still empty, so they stay out of the sitemap. An editor can change that in Yoast.

### LiftedPath → Search & verification

One submenu, capability `manage_options`, option `liftedpath_discovery`.

- Organization summary and area served, used for local identity.
- Profile links, one web address per line, including a Google Business Profile URL when the foundation has one.
- Verification codes, stored only after an administrator pastes them. Empty fields print nothing. Accepted codes are the token itself, or the token extracted from a meta tag the platform copied.
- Google Analytics (`G-…`) is printed only when Tag Manager is empty, so a site with both is not counted twice.
- Google Tag Manager (`GTM-…`) is printed when set.
- Google Business Profile verification is a meta tag only when Google supplies a website code. Profile verification by phone, email, or postcard has no site code.
- Bing Webmaster (`msvalidate.01`) is the extra webmaster field.
- Redirect rows: a public path, a public path or `https` destination, and 301 or 302. At most 20.

`GET /wp-json/liftedpath/v1/seo` returns the public site origin, the summary, the profile links, the share image, the verification tokens, the redirects, and the published pages’ path, modified time, title, description, and index flag. `share_image` stays off `GET /liftedpath/v1/settings`.

### Public site

`generateMetadata` uses the resolved title and description, a canonical URL on the public origin, Open Graph, and Twitter. The layout prints Organization and WebSite structured data from the organization settings plus the summary. Each page prints WebPage structured data. `/llms.txt` is the plain-text description for answer engines.

`app/sitemap.ts` lists indexable pages. `app/robots.ts` allows the public site, disallows `/api/`, and points at the sitemap.

`proxy.ts` applies the redirect list on the next visit. It reads WordPress for that list and continues when WordPress does not answer.

Saving the submenu, or saving Yoast fields on a published page, calls the Unit 17 webhook. A page save refreshes that page. The submenu refreshes the `seo` tag, which covers the layout head, sitemap, robots, and `/llms.txt`.

## Dependencies

- Unit 13 for the published pages whose text this unit describes.
- Unit 17 for the revalidate webhook. The accepted tags gain `seo`.
- Yoast SEO, already installed by the administrator. The theme still renders titles from the page SEO fields when Yoast is inactive. Yoast SEO Premium is not required.

## Verify when done

- [x] The visible result named in `context/specs/00-build-plan.md` is true
- [x] Editor-controlled values for this unit are fields, blocks, menus, media, or global styles
- [x] No invariant in `context/architecture.md` was broken
- [x] Desktop and 390px checked, when the unit has UI
- [x] Reduced motion checked, when the unit animates
- [x] Theme activates with no PHP fatal, when the unit touches PHP
- [x] `npm run build` passes, when the unit touches the frontend
- [x] `context/progress-tracker.md` updated
