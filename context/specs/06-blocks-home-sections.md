# Unit 06: Home section blocks

## Goal

An editor can insert About tabs, Program cards, Action grid, Sponsor callout, Stewardship, and Process from the LiftedPath category. Each block shows its fields, and a page on the WordPress host renders the Design 01 home sections.

## Design

Copy sits in the 1120px column. Section backgrounds bleed. Padding is 128px, 80px below 760px, and 65px below 520px. The inset is 24px, and 18px below 760px. At 520px headings use the smaller section size.

About is two columns, stacked below 760px: photographs and a figure beside the tabs. Program cards are three columns, two below 760px, and one below 520px. The action grid is four columns, two below 1050px, and one below 520px. Process steps are three, two, then one at those same widths. Tabs switch without a page load. Image zoom does not run when the visitor prefers reduced motion.

React components for these blocks are Unit 12. This unit is the editor and the WordPress host.

## Implementation

Register the six blocks from `inc/blocks.php`, using the Unit 05 block contract: `apiVersion` 3, ACF block version 3, anchor on, color and spacing controls off, full-width alignment so the section background can bleed. Presentation stays in `assets/blocks.css` and uses theme tokens.

### `liftedpath/about-tabs`

The about band. Fields: `primary_image`, `secondary_image`, `figure`, `figure_label`, `eyebrow`, `heading`, `intro`, `tabs` (repeater of `label`, `title`, `body`), `primary_link`, `secondary_link`. The first tab is selected. Up to six tabs. The photograph alt text comes from the media library.

### `liftedpath/program-cards`

Fields: `eyebrow`, `heading`, `intro`, `cards` (repeater of `image`, `kicker`, `title`, `line`, `link`), and an optional grid-end card `callout_heading`, `callout_text`, `callout_button`. The callout is the “Sponsor a child” cell. It is omitted when the heading is empty.

### `liftedpath/action-grid`

Fields: `eyebrow`, `heading`, `intro`, `items` (repeater of `title`, `text`, `link`). The first four items use the Design 01 symbols in order: give, partner, empower, reachout. Those symbols are presentation, not fields.

### `liftedpath/sponsor-callout`

The ₦50,000 strip. Fields: `figure`, `text`, `link`.

### `liftedpath/stewardship`

Fields: `eyebrow`, `heading`, `intro`, `points` (repeater of `title`, `text`), `button`, `image`, `inset_image`, `note_label`. The phone on the photograph comes from LiftedPath settings. An empty settings phone hides the note.

### `liftedpath/process`

Fields: `eyebrow`, `heading`, `button`, `steps` (repeater of `title`, `text`). The index is generated from the order, starting at 01.

An empty block shows a short prompt in the editor and nothing on the host front.

## Dependencies

- Unit 05, for the block registration pattern.
- ACF PRO, already active.

## Verify when done

- [x] Each of the six blocks can be inserted from the LiftedPath category
- [x] About tabs, Program cards, Action grid, Sponsor callout, Stewardship, and Process show their fields
- [x] A typed value appears in the canvas
- [x] Choosing another about tab shows that panel
- [x] A page on the WordPress host renders the sections at desktop width and at 390px
- [x] Theme loads with no PHP fatal
- [x] `context/progress-tracker.md` updated
