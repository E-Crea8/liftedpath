# Unit 04: REST contract

## Goal

A published page is readable as JSON from `GET /wp-json/liftedpath/v1/page?path=/sample-page`, including its path, title, and blocks. `GET /wp-json/liftedpath/v1/paths` lists published pages. `GET /wp-json/liftedpath/v1/design` returns the resolved design tokens and a version hash.

## Design

This unit has no public layout. The visible result is JSON from the three routes.

## Implementation

### `inc/rest.php`

Register namespace `liftedpath/v1` on `rest_api_init`. All three routes are `GET` and public. Drafts, private pages, and password-protected pages are omitted. A missing or unpublished page returns 404 with the same message, so a draft is not distinguishable from a missing path.

Errors use `WP_Error`, which the REST API renders as `code`, `message`, and `data.status`.

Organization settings stay on the LiftedPath options page. This unit does not add a settings route. Navigation, preview, and revalidation stay in later units.

### `GET /liftedpath/v1/page`

Query argument `path` is required. `/` is the static front page. Any other value is a slash-separated page path such as `/learning/lift-root`. Each segment is passed through `sanitize_title`. `..`, backslashes, schemes, `?`, and `#` are rejected with 400.

Until a static front page is set, `/` returns 404. That page is seeded in Unit 08.

```json
{
  "id": 2,
  "slug": "sample-page",
  "path": "/sample-page",
  "title": "Sample Page",
  "status": "publish",
  "modified": "2026-10-07T00:00:00+00:00",
  "parent_path": null,
  "featured_image": null,
  "seo_title": "",
  "seo_description": "",
  "hero_summary": "",
  "blocks": [
    {
      "name": "core/paragraph",
      "attributes": {},
      "html": "<p>Hello</p>"
    }
  ]
}
```

`modified` is RFC 3339 in GMT. `parent_path` is the parent’s public path, or `null` for a top-level page. The static front page’s public path is `/`, including when it is someone else’s parent.

`featured_image` is `null` or `{ id, url, alt, width, height }` for the full attachment.

SEO values are the page fields from Unit 03. An empty field is `""`.

`blocks` follows post order. Whitespace-only blocks are dropped. A classic fragment with no block name is `core/freeform`. Each item is `{ name, attributes, html }`.

- Core blocks: `attributes` are the parsed block attributes.
- LiftedPath blocks: `attributes` are the ACF field values. Field keys that start with `_` are dropped.
- Nested blocks live in `attributes.innerBlocks` with the same three-key shape.
- `html` is `render_block()` passed through `wp_kses_post`.

An empty attribute set is a JSON object, not an array.

### `GET /liftedpath/v1/paths`

```json
{
  "paths": [
    { "id": 2, "path": "/sample-page", "modified": "2026-10-07T00:00:00+00:00" }
  ]
}
```

Published pages only, ordered by menu order then title. Each page appears once, at its public path.

### `GET /liftedpath/v1/design`

Resolved from theme data merged with user global styles. Core default presets stay out of the payload, because the Site Editor hides them.

```json
{
  "version": "<sha256 of the maps below>",
  "palette": { "paper": "#FCFDFD", "teal": "#043C42" },
  "fontSizes": { "body": "1rem", "display": "clamp(3rem, 6.25vw, 5rem)" },
  "spacingSizes": { "18": "18px", "24": "24px" },
  "layout": { "contentSize": "1120px", "wideSize": "1120px" }
}
```

Keys are the preset slugs. A custom preset with the same slug replaces the theme value. `version` is the SHA-256 of those four maps, so an administrator’s global-styles save changes the hash. The hash is not part of its own input.

## Dependencies

- Unit 03, for the page SEO fields.
- WordPress theme JSON resolver. No new plugin.

## Verify when done

- [x] A published page returns JSON with its path, title, and a blocks array
- [x] `/paths` lists that page
- [x] `/design` returns palette, font sizes, spacing sizes, layout sizes, and a version hash
- [x] An anonymous request for a draft path returns 404
- [x] An invalid path returns 400
- [x] Theme loads with no PHP fatal
- [x] `context/progress-tracker.md` updated
