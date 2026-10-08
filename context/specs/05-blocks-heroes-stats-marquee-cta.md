# Unit 05: Heroes, stats, marquee, CTA

## Goal

An editor can insert Hero, Page hero, Stat, Stat group, Marquee, and CTA band from the LiftedPath block category. Each block shows its fields in the editor and renders on the WordPress host.

## Design

These are the Design 01 sections. Copy sits in the 1120px column. The hero, page hero, marquee, and CTA band bleed to the viewport edges. At 390px the column inset is 18px, the display heading uses the 520px clamp, and the hero’s corner figure is hidden. The marquee loops in CSS and becomes a wrapping line when the visitor prefers reduced motion.

React components for these blocks are Unit 12. This unit is the editor and the WordPress host.

## Implementation

### Registration

`inc/blocks.php` registers the six blocks from `blocks/<name>/block.json` when ACF PRO is active. Block category slug `liftedpath`. Each `block.json` is `apiVersion` 3, name `liftedpath/<name>`, with an `acf` render template and ACF block version 3, so the canvas shows `render.php` and the fields sit in the sidebar.

`templates/page.html` renders the page content between the existing header and footer parts, so a page on the WordPress host shows the blocks. `assets/blocks.css` is the host and editor presentation. It uses the theme tokens only.

Anchor is supported. Alignment, color, and spacing controls are off, except Hero, Page hero, Marquee, and CTA band default to full width so they can bleed. The React side in Unit 12 honors the same supports.

### `liftedpath/hero`

Home hero. Fields: `image`, `portraits` (repeater of `image`), `eyebrow`, `heading`, `intro`, `button` (link), and an optional corner figure `figure`, `label`, `detail`. Portrait images render with empty alt. The photograph’s alt comes from the media library.

### `liftedpath/page-hero`

Inner hero. Fields: `image`, `eyebrow`, `heading`, `intro`, `breadcrumb_label` (default Home). The second crumb is the eyebrow. The Home link points at the site root.

### `liftedpath/stat`

One figure. Fields: `figure`, `label`, `detail`.

### `liftedpath/stat-group`

A grid of Stat blocks through InnerBlocks. No fields of its own. Four columns, two from 760px down.

### `liftedpath/marquee`

Repeater `phrases` of `phrase`. An empty repeater uses `marquee_phrases` from LiftedPath settings. Phrases are separated by ✦.

### `liftedpath/cta-band`

Fields: `tone` (`photo`, `teal`, or `paper`), `image`, `eyebrow`, `heading`, `text`, `button`. `photo` is the join band: photograph, deep veil, paper type, gold button.

Image fields store an attachment ID in the block. `render.php` prints the image. An empty block shows a short prompt in the editor and renders no placeholder on the host front.

## Dependencies

- Unit 03, for ACF and the settings phrases.
- ACF PRO, already active.

## Verify when done

- [x] Each of the six blocks can be inserted from the LiftedPath category
- [x] Hero, Page hero, Stat, Marquee, and CTA band show their fields, and a typed value appears in the canvas
- [x] Stat group accepts Stat blocks and lays them out
- [x] A page on the WordPress host renders the blocks at desktop width and at 390px
- [x] The marquee does not scroll when reduced motion is set
- [x] Theme loads with no PHP fatal
- [x] `context/progress-tracker.md` updated
