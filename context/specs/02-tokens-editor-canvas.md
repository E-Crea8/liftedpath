# Unit 02: Tokens and editor canvas

## Goal

The Site Editor palette, type sizes, and spacing sizes match Design 01. The editor canvas and the WordPress front use Plus Jakarta Sans for text and Stack Sans Headline for headings, on a paper page with teal text and a 1120px content column.

## Design

Tokens live in `context/ui-context.md`. They were measured from Design 01 (`.site.design-one`) at https://liftedpath-design.lovable.app/ and from `liftedpath-design-main/src/styles.css`. This unit does not build page sections.

Desktop content sits in a 1120px column with 24px of root padding on each side. At 760px and below, that inset is 18px. At 390px the column shrinks with the viewport and the text stays inside it.

Headings use Stack Sans Headline. The display heading is `clamp(3rem, 6.25vw, 5rem)`, weight 600, line-height 1.15. At 520px and below it becomes `clamp(2.45rem, 11vw, 3.3rem)`. Section headings are `clamp(2.25rem, 4.38vw, 3.5rem)`, weight 500, line-height 1.15, and 2.15rem at 520px. Heading 3 is 1.28rem, weight 600. Body is Plus Jakarta Sans, 1rem, weight 400, line-height 1.58.

The primary button is a gold pill with a teal label, Plus Jakarta Sans weight 800 at 0.83rem, at least 48px tall. Hover turns the fill teal and the label paper. Links are teal and turn bronze on hover. Focus is a 2px bronze outline, 3px outside the control.

## Implementation

### theme.json

Version 3. Turn off the default palette, gradients, duotones, font sizes, and spacing sizes.

Palette, in this order: paper `#FCFDFD`, teal `#043C42`, deep `#02272B`, gold `#B78D4B`, bronze `#A57D3C`, sand `#D6B886`, surface `#E7ECEC`, warm `#F3ECE0`, cool `#D3DBDD`, muted `#4C6E71`, line `#D3DCDC`.

Font families, with `file:` sources under `assets/fonts/` so the editor does not call Google at runtime:

- Body, slug `body`: Plus Jakarta Sans, weight axis 200–800, latin and latin-ext.
- Display, slug `display`: Stack Sans Headline, weight axis 200–700, latin and latin-ext. The face falls back to Plus Jakarta Sans.

Font size presets, fluid generation off, using the Design 01 clamps and fixed sizes: display, heading, heading-3, stat (2.6rem), body-large (1.03rem), body (1rem), eyebrow (0.85rem), nav (0.86rem), button (0.83rem), caption (0.78rem).

Spacing presets: 4, 8, 12, 16, 18, 20, 24, 28, 32, 40, 48, 64, 65, 80, 96, 128, all in px.

Custom properties: header height 89px, heading line-height 1.15, body line-height 1.58, radii (8, 14, 16, 18, 999), the two Design 01 shadows, error `#8C3A32`, success `#1B5E3B`, and the hero veil gradient.

Layout: `contentSize` and `wideSize` are both `1120px`. Full-bleed sections use alignfull. Root padding is 24px left and right. `styles.css` sets that inset to 18px at 760px, and applies the 520px heading sizes.

Element styles: body uses the body family; headings use the display family; h1, h2, and h3 use the sizes and weights above; links and buttons use the treatments above.

### functions.php

`liftedpath_content_width` returns 1120. Theme supports: align-wide, responsive-embeds, editor-styles. Editor style: `assets/editor.css`. Image sizes: `liftedpath-hero` 1920×1080, `liftedpath-card` 720×960, `liftedpath-portrait` 900×1200, all hard-cropped. Custom logo box 230×82, still flexible. Theme version 0.2.0.

### assets/editor.css

Repeats the focus ring, the 48px button height, and the 520px heading sizes inside `.editor-styles-wrapper`, so the canvas matches when global styles are not the winner.

### Context

Update `ui-context.md` so the token tables are Design 01, not the brand note’s Fraunces scale or 1024px column. Update the build-plan sentence that named the old families. Update the content-width and focus lines in `code-standards.md`.

## Dependencies

- Unit 01 theme scaffold.
- No plugin. Font files are SIL Open Font License; the license texts sit beside the files.

## Verify when done

- [x] Site Editor palette is Paper, Teal, Deep, Gold, Bronze, Sand, Surface, Warm, Cool, Muted, and Line
- [x] Site Editor fonts are Plus Jakarta Sans and Stack Sans Headline, with the Design 01 size presets
- [x] Site Editor spacing presets are 4, 8, 12, 16, 18, 20, 24, 28, 32, 40, 48, 64, 65, 80, 96, and 128
- [x] Layout content width and wide width are 1120px, with 24px horizontal padding
- [x] The editor canvas and the front page use those fonts, paper, and teal
- [x] At 1280px the column is 1120px. At 390px the inset is 18px, the heading is 2.15rem, and nothing overflows
- [x] No PHP fatal. This unit does not animate and does not change the Next.js app
- [x] `context/progress-tracker.md` updated
