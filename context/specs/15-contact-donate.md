# Unit 15: Contact and donate

## Goal

`/contact` and `/donate` render their WordPress pages. A message creates a private submission and a styled email. A gift opens the payment provider an administrator chose, and a confirmed payment is stored and emailed.

## Design

Contact is two columns, stacked below 760px. Phone, email, and office come from LiftedPath settings. The form panel is the surface band. Donate is the gift choices beside the photograph note, stacked below 760px. Frequency, currency, and amount stay the choices already on the page. Name and email are collected so the provider can receipt the gift.

The notification email is a paper card on the warm ground, with a teal header, a gold rule, and the submitted fields in a table. Mail clients do not load the site fonts, so the email uses Georgia and Arial. The colors are the Design 01 palette written inline.

The payment page belongs to Stripe, Paystack, or Flutterwave. This site never shows a card field.

## Implementation

### LiftedPath Forms

Plugin `liftedpath-forms`. Submissions → Form settings is limited to administrators.

The screen edits the public site URL, the sender, each form’s recipient, subject, and message, whether the visitor gets a copy, the provider (Stripe, Paystack, or Flutterwave), the mode label, and the secret keys. An empty secret field keeps the saved key. The screen shows the webhook URL and a preview of the email with sample details. Smart tags are `{name}`, `{email}`, `{reason}`, `{message}`, `{amount}`, `{currency}`, `{frequency}`, `{designation}`, `{reference}`, `{provider}`, `{site_name}`, and `{all_fields}`.

`FORM_SECRET` is generated when the plugin is activated. Next.js sends it on server-to-server form calls. It is not in the browser.

`GET /liftedpath/v1/forms` reports the provider and whether a secret key is saved. `POST /contact` and `POST /donate` require the form secret, validate the fields, and store a private submission through the existing submission fields. Contact mail sends after the record is stored. Donate creates a pending record and a hosted checkout, then marks it paid and sends mail when the provider confirms it, either on the return URL or on the signed webhook. A second confirmation does not send a second email.

Stripe and Paystack amounts are sent in minor units. Flutterwave amounts are sent in major units. Monthly gifts use a Stripe subscription. Paystack and Flutterwave charge the selected amount once and record that the giver asked for a monthly gift.

### Public pages

`/contact` and `/donate` use the shared page reader. The contact and donate blocks submit to Next.js route handlers, which call the plugin. `/donate/complete` reports whether the provider confirmed the reference. An unknown reference and a missing provider stay on a clear message. Card data is not posted to this site.

## Dependencies

- Unit 07, for the contact and donate blocks.
- Unit 08, for the Contact and Donate pages.
- Unit 14, for the shared page reader.
- ACF PRO, already active, for the submission fields.

## Verify when done

- [x] `/contact` and `/donate` show their WordPress pages
- [x] A contact message creates a private submission and asks WordPress to send the styled email
- [x] Changing the contact subject in Form settings changes the email that is built
- [x] Donate without a secret key explains that payments are not connected and does not invent a charge
- [x] The checkout request for a configured provider is built for that provider’s hosted page
- [x] A confirmed payment is recorded once and the card number is absent from the submission
- [x] Desktop contact and donate columns sit side by side; at 390px each is one column and the inset is 18px
- [x] Anonymous submission reads stay rejected
- [x] No Google Fonts request
- [x] `npm run lint` and `npm run build` pass
- [x] `context/progress-tracker.md` updated
