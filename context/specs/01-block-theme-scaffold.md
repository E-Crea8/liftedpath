# Unit 01: Block theme scaffold

## Goal

The LiftedPath theme activates on `http://liftedpath.local`. The front of the WordPress host renders `templates/index.html`, with the header and footer template parts, and wp-admin shows no PHP fatal.

## Design

This unit does not apply the Design 01 visual system. Colors, type, spacing sizes, and editor fonts are Unit 02.

The scaffold layout uses one content column of 1024px (`--content-max` in `context/ui-context.md`). Header, main, and footer stack in that column and remain readable at 390px because the blocks are constrained and not placed side by side.

The header shows the WordPress site title and tagline. The main area lists posts. When there are no posts, it shows the word LiftedPath. The footer shows “LiftedPath Empowerment Foundation”. Those strings live in the site settings and in the template parts, so an administrator can change them in the Site Editor.

## Implementation

Generate the theme in this directory from the Underscores starter (slug `liftedpath`, no WooCommerce, no Sass). Keep the PHP bootstrap. Add the block-theme files below. Do not add classic PHP templates, `js/navigation.js`, `inc/custom-header.php`, `inc/customizer.php`, Jetpack or WooCommerce includes, sidebar layouts, or a front-end CSS framework.

### `style.css`

Theme header only, plus the Underscores GPL attribution. No classic normalize stylesheet.

- Theme Name: LiftedPath
- Author: LiftedPath Empowerment Foundation
- Description: Block theme for the LiftedPath Empowerment Foundation. Editors manage content, layout, and global styles in WordPress. The public website is rendered by Next.js.
- Requires at least: 6.7
- Tested up to: 7.1
- Requires PHP: 8.2
- Version: 0.1.0
- License: GNU General Public License v2 or later
- Text Domain: `liftedpath`
- Tags: `full-site-editing`, `block-patterns`, `custom-logo`, `editor-style`, `featured-images`, `translation-ready`

### `theme.json`

Version 3. Schema `https://schemas.wp.org/trunk/theme.json`.

Settings: `appearanceTools` true, `useRootPaddingAwareAlignments` true, layout `contentSize` `1024px`, `wideSize` `1200px`.

Declare template parts `header` (area header) and `footer` (area footer).

Set `styles.color.background` to `#FCFDFD` (paper) and `styles.color.text` to `#043C42` (deep teal) so the WordPress host is readable. Do not add the color palette, font families, font sizes, or spacing scale. Unit 02 fills those.

### `templates/index.html`

Block markup, in order:

1. Template part `header`, rendered as a `header` element.
2. A constrained `main` group containing a `core/query` with `inherit` true. The post template shows the post title (linked) and the excerpt. `core/query-no-results` contains a paragraph whose text is `LiftedPath`.
3. Template part `footer`, rendered as a `footer` element.

Do not reference spacing presets. Those tokens do not exist yet.

### `parts/header.html`

A constrained group containing `core/site-title` (level 0, so it is not a heading that skips the page content) and `core/site-tagline`.

### `parts/footer.html`

A constrained group containing one paragraph: `LiftedPath Empowerment Foundation`.

### `index.php`

A file comment only. Block templates render the theme. This file does not print markup.

### `functions.php`

Underscores setup, prefixed `liftedpath_`, text domain `liftedpath`, version constant `LIFTEDPATH_VERSION` set to `0.1.0`.

`liftedpath_setup` on `after_setup_theme`:

- `load_theme_textdomain`
- `automatic-feed-links`
- `title-tag`
- `post-thumbnails`
- `html5` for search-form, comment-form, comment-list, gallery, caption, style, script
- `custom-logo` with flexible width and height

`liftedpath_content_width` sets the global `$content_width` to 1024 through the `liftedpath_content_width` filter.

`liftedpath_scripts` enqueues `comment-reply` only when a singular view has threaded comments open. Do not enqueue `style.css` again: WordPress loads it for block themes. Do not enqueue `js/navigation.js`.

Require `inc/template-functions.php`. Do not require a custom header, customizer, Jetpack, or WooCommerce file.

Do not register a classic sidebar or a classic menu location. Navigation is a later unit.

Leave align-wide, image sizes, and editor styles for Unit 02.

### `inc/template-functions.php`

- `liftedpath_body_classes` adds `hfeed` on non-singular views and always adds `liftedpath`.
- `liftedpath_pingback_header` prints a pingback link on singular views when pings are open.

No sidebar class. There is no sidebar.

### `readme.txt` and `phpcs.xml.dist`

A short WordPress readme matching the style.css header. A PHPCS ruleset for the WordPress standard, text domain `liftedpath`, prefixes `liftedpath` and `LIFTEDPATH`, PHP `8.2-`, minimum WordPress `6.7`.

### `LICENSE`

The GPL v2 license text shipped with Underscores.

## Dependencies

- None. WordPress 7.1.3 and PHP 8.2 are already running on Local. ACF PRO is not required for this unit.

## Verify when done

- [x] The LiftedPath theme is active on `http://liftedpath.local`
- [x] The front page shows the header site title, a main area, and the footer line, with no PHP fatal
- [x] The page source includes the block-template wrapper (`wp-site-blocks`)
- [x] `wp_is_block_theme()` is true for the active theme
- [x] The layout is readable at desktop width and at 390px
- [x] No invariant in `context/architecture.md` was broken
- [x] `context/progress-tracker.md` updated
