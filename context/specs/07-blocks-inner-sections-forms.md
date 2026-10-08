# Unit 07: Inner sections and forms

## Goal

An editor can insert Pillars, Goals, FAQ, Contact form, and Donate panel from the LiftedPath category. About, Impact, Contact, and Donate can be composed on the WordPress host. The forms show their fields and do not send a message.

## Design

Copy sits in the 1120px column. Section backgrounds bleed. Padding is 128px, 80px below 760px, and 65px below 520px.

Pillars are four numbered cards, two columns below 1050px, and one column below 520px. Goals are paper cards on a surface band: the introduction beside a two-column list, stacked below 760px, and one card column below 760px. FAQ is one open question at a time. The chevron turns instantly when reduced motion is on.

Contact is two columns, stacked below 760px. Phone, email, and office come from LiftedPath settings. Donate is the gift configurator beside a photograph note, stacked below 760px. Frequency, currency, and amount are choices on the page. Changing currency shows that currency’s figure. A line under the button says the site does not collect payment.

Sending a contact message or a gift intent is Unit 15.

## Implementation

Register the five blocks from `inc/blocks.php`, using the Unit 05 contract. Presentation stays in `assets/blocks.css`.

### `liftedpath/pillars`

Fields: `eyebrow`, `heading`, `items` (repeater of `title`, `text`). The index is generated from the order, starting at 01.

### `liftedpath/goals`

Fields: `eyebrow`, `heading`, `intro`, `goals` (repeater of `title`, `text`). The index is generated from the order, starting at 01.

### `liftedpath/faq`

Fields: `eyebrow`, `heading`, `items` (repeater of `question`, `answer`). An empty answer still shows the question. Opening one question closes the others.

### `liftedpath/contact-form`

Fields: `eyebrow`, `heading`, `intro`, `form_eyebrow`, `form_heading`, `reasons` (repeater of `reason` and `label`), `button_label`, `note`. Reason values are `volunteer`, `sponsor`, `partner`, `in-kind`, and `other`. An empty repeater uses those five labels. The phone, email, and office rows read LiftedPath settings. The button does not submit.

### `liftedpath/donate-panel`

Fields: `eyebrow`, `heading`, `intro`, `one_off_label`, `monthly_label`, `amounts` (repeater of `amount`, `title`, `detail`), `usd_rate`, `gbp_rate`, `designations` (repeater of `designation` and `label`), `button_label`, `note`, `aside_image`, `aside_heading`, `aside_text`. Designation values are `greatest-need`, `education`, `skills`, `mentorship`, `orphanage`, and `season`. Rates are naira per one dollar and one pound. An empty amount or designation repeater uses the Design 01 choices. The button does not submit.

An empty block shows a short prompt in the editor and nothing on the host front.

## Dependencies

- Unit 05, for the block registration pattern.
- Unit 03, for the settings phone, email, and address.
- ACF PRO, already active.

## Verify when done

- [x] Each of the five blocks can be inserted from the LiftedPath category
- [x] Pillars, Goals, FAQ, Contact form, and Donate panel show their fields
- [x] A typed value appears in the canvas
- [x] Opening one FAQ question closes the other
- [x] Choosing another currency changes the gift figures
- [x] Contact and donate buttons do not submit
- [x] A page on the WordPress host renders the sections at desktop width and at 390px
- [x] Theme loads with no PHP fatal
- [x] `context/progress-tracker.md` updated
