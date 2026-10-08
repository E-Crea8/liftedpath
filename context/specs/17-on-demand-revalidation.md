# Unit 17: On-demand revalidation

## Goal

Publishing a page updates the public Next.js page on the next visit. The editor does not wait for the hourly revalidate. A failed webhook is logged and does not stop the save.

## Implementation

### Theme `inc/revalidate.php`

Queue a webhook when a page, a navigation post, the LiftedPath settings, or global styles are saved. Send one request per distinct target at the end of the WordPress request, after the save has been written.

`POST {public site}/api/revalidate` with JSON `{ "secret", "action", "path" }` or `{ "secret", "action", "tag" }`. `action` is `update` or `delete`. A published page sends its public path. Leaving publish sends `delete` for that path. Navigation sends `nav`. Settings send `settings`. Global styles send `design`. Autosaves and revisions do not send.

The public origin is the same one the preview link uses. The secret is the option `liftedpath_revalidate_secret`, shared with `REVALIDATE_SECRET` in `frontend/.env.local`. It is not written into the theme. The request waits up to 8 seconds. A transport error or a non-200 response is written to the PHP error log without the secret, and the editor’s save still succeeds.

### Next.js `POST /api/revalidate`

The route accepts only `secret`, `action`, `path`, and `tag`. It compares the secret in constant time. A missing or wrong secret returns 401. An unknown field, a bad action, a bad path, or a tag outside `nav`, `settings`, `design`, `sitemap`, and `media` returns 400.

A valid request expires the index tags `nav`, `settings`, `design`, `sitemap`, and `media`. A path also expires that page’s cache tag and the page path. The expire window is `0`, so the next visit renders the published content instead of serving the stale page. The response is `{ "revalidated": true }`.

## Dependencies

- Units 11 and 16 for the cached header, footer, and page reads, and for the public site origin.
- `REVALIDATE_SECRET` in `frontend/.env.local` and the matching WordPress option. Created locally. Not committed.

## Verify when done

- [x] Publishing a change to an existing page shows that change on the public Next.js URL without restarting Next.js
- [x] The published page is restored afterward
- [x] A wrong secret returns 401
- [x] A draft save does not publish that page on the public URL
- [x] Navigation, settings, and global styles saves are wired to the webhook
- [x] A failed webhook does not stop the save
- [x] Home still shows “Breaking Barriers to Education & Opportunity”
- [x] No Google Fonts request
- [x] Theme activates with no PHP fatal
- [x] `npm run lint` and `npm run build` pass
- [x] Published pages still revalidate in 1 hour
- [x] `context/progress-tracker.md` updated
