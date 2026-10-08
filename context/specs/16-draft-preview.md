# Unit 16: Draft preview

## Goal

An editor can open a page preview on the Next.js site. The preview shows unpublished block markup. The public URL keeps the published page. A second visit to the same preview link does nothing.

## Design

The preview banner sits on top of the page, full width, fixed to the bottom. It uses the deep background, paper text, and a gold pill button at the existing 48px control height. It does not move the header or the 1120px column. At 390px the sentence and the Exit preview button wrap, and the button becomes full width. The banner does not animate.

## Implementation

### Theme `inc/preview.php`

Filter `preview_post_link` for pages the current user can edit. The link is `{public site}/api/preview?token=` plus a 64-character hex token. The public site origin is LiftedPath Forms’ public site URL when that value is an http or https origin, otherwise `http://localhost:3000`. Store only `sha256(token)` in a transient named `lp_prev_` plus that hash, value the page ID, for 10 minutes. Delete the transient when it is exchanged.

A draft opened on the WordPress `?preview=true` URL redirects to that same Next.js link. Published pages already receive the Next.js link from the editor’s autosave `preview_link`.

`POST /wp-json/liftedpath/v1/preview/exchange` accepts `{ "token" }`. `GET /wp-json/liftedpath/v1/preview?path=` returns the same page shape as the public page route. Both require an application-password user who can `edit_pages`, and the header `X-LiftedPath-Preview-Secret` compared in constant time with the option `liftedpath_preview_secret`. A missing or reused token returns 404. The path in the exchange response comes from WordPress.

The preview page read includes `publish`, `draft`, `pending`, and `future`. Private, trash, and password-protected pages return the same 404 as a missing page. When an autosave exists, its title, content, excerpt, and modified time replace the saved page’s on that response only. The post ID and public path stay on the saved page, so an autosave slug is never the public path.

`GET /wp-json/liftedpath/v1/page` stays published-only.

WordPress allows application passwords on HTTPS or when the environment type is `local`. This unit also allows them for the hosts `liftedpath.local`, `localhost`, and `127.0.0.1`. Other hosts stay as WordPress left them.

The preview user, application password, and `PREVIEW_SECRET` live in `frontend/.env.local`. They are not written into the theme.

### Next.js preview route

`GET /api/preview?token=` exchanges the token, enables draft mode, and redirects to the path WordPress returned. An invalid token returns 401 and does not set the cookie. The redirect target is not taken from the query string.

`getPage` stays behind `use cache`. Inside that function, `draftMode().isEnabled` chooses the preview route and sends the application password. Draft mode skips the cache and does not store the preview result. The published fetch is unchanged when the cookie is absent. `cache: "no-store"` is not passed inside `use cache`, because Next.js rejects it there. The mapped page object does not include the password.

The root layout renders a cached preview banner with the same one-hour cache lifetime as published pages. Exit preview is a POST form to `/api/preview`, not a link, so the header and footer do not prefetch it. The route disables draft mode and returns to the page the editor was previewing when that path is on this site.

## Dependencies

- Units 10 and 12 for the WordPress client and the rendered home page.
- Next.js draft mode. `enable()` and `disable()` run in `GET` and `POST /api/preview`.
- A WordPress application password for the preview user. Created locally and stored in `frontend/.env.local`.

## Verify when done

- [x] The editor preview link points at `http://localhost:3000/api/preview?token=`
- [x] An unpublished autosave heading is visible on the preview URL
- [x] That heading is absent from the public URL and from anonymous `GET /wp-json/liftedpath/v1/page`
- [x] Refreshing the preview page still shows the unpublished heading
- [x] Using the same token again returns 401 and does not enable draft mode
- [x] Anonymous `GET /wp-json/liftedpath/v1/preview` returns 401
- [x] A draft page is readable by the preview user and returns 404 to anonymous `GET /page`
- [x] Exit preview removes the banner and shows the published page
- [x] Desktop column stays 1120px and the header stays 89px with the banner visible
- [x] At 390px the banner wraps, the column is 354px, the inset is 18px, and the section heading is 2.15rem
- [x] The banner does not animate
- [x] No Google Fonts request
- [x] Theme activates with no PHP fatal
- [x] `npm run lint` and `npm run build` pass
- [x] `context/progress-tracker.md` updated
