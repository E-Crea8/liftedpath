# Unit 03: Content model and ACF

## Goal

An administrator opens LiftedPath settings and sees the organization facts. A page shows SEO title, SEO description, and an optional hero summary. Submissions exist as a private type an editor can open in wp-admin.

## Design

This unit has no public layout. The settings screen and the page sidebar are wp-admin screens.

## Implementation

### Google Fonts option

Recorded with this unit because it landed in the same change. Design 01 faces stay the local files from Unit 02. An administrator can install another family from the Site Editor Google Fonts collection. WordPress copies that file onto the site. Public pages drop any `fonts.googleapis.com` or `fonts.gstatic.com` font source, stylesheet, or preconnect, so a font choice cannot add a Google request to the front.

### `inc/content-types.php`

Post type `submission`.

- `public` false, `show_ui` true, `show_in_rest` true, no archive, no rewrite.
- New and updated submissions are stored as `private`, except auto-drafts and trash.
- `/wp/v2/submissions` requires a logged-in user. Anonymous requests get 401.

### `acf-json/`

ACF loads these files from the theme. Field groups are shown in the REST API.

Options page `liftedpath-settings`, capability `manage_options`, menu title LiftedPath.

Settings fields, stored on the `option` id:

| Name | Type | Seed |
| --- | --- | --- |
| `legal_name` | text | LiftedPath Empowerment Foundation |
| `short_name` | text | LiftedPath |
| `tagline` | text | Lifting Potential, Inspiring Purpose and Creating Futures. |
| `registration_number` | text | RC 9872033 |
| `phone` | text | +234 806 812 8785 |
| `email` | email | liftedpathfoundation@gmail.com |
| `postal_address` | textarea | 19, George Tienne Crescent, Hilltop Estate, Aboru, Iyana Ipaja, Lagos, Nigeria. |
| `copyright_text` | text | LiftedPath Empowerment Foundation. All Rights Reserved. |
| `marquee_phrases` | repeater of `phrase` | The Design 01 ticker phrases |
| `share_image` | image, returns an attachment ID | empty |
| `form_recipient_email` | email | liftedpathfoundation@gmail.com |

Page fields, on the page sidebar:

| Name | Type |
| --- | --- |
| `seo_title` | text |
| `seo_description` | textarea |
| `hero_summary` | textarea |

Submission fields:

| Name | Type |
| --- | --- |
| `submission_type` | select: `contact`, `donate` |
| `full_name` | text |
| `email` | email |
| `reason` | select: volunteer, sponsor, partner, in-kind, other |
| `message` | textarea |
| `currency` | select: `NGN`, `USD`, `GBP` |
| `amount` | number |
| `frequency` | select: `one-off`, `monthly` |
| `designation` | select: greatest need, education, skills, mentorship, orphanage, season |

`inc/acf.php` shows a warning in wp-admin when ACF PRO is inactive, and seeds the settings once when they are still empty. The theme still loads without ACF. The submission type does not depend on ACF.

## Dependencies

- Unit 02.
- ACF PRO 6.7.1, already active on the local site.

## Verify when done

- [x] LiftedPath settings opens and shows the seeded organization facts
- [x] A page edit screen shows SEO title, SEO description, and hero summary
- [x] Submissions is a private type in wp-admin
- [x] Anonymous `GET /wp-json/wp/v2/submissions` is rejected
- [x] The public front HTML does not request Google Fonts, and the local faces still load
- [x] No PHP fatal
- [x] `context/progress-tracker.md` updated
