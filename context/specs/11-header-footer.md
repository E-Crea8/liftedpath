# Unit 11: Header and footer from WordPress

## Goal

The Next.js header and footer read the `primary` and `footer` navigation posts and the public organization settings. Changing a menu label in wp-admin changes the header after the published cache revalidates.

## Design

The Unit 09 frame stays: a fixed 89px header, transparent over the deep home band and teal after scroll, paper type, and a gold Donate Now pill. Desktop, from 1050px, shows the primary menu. An item with children opens on hover and on focus and stays open while the pointer or focus is inside. Escape closes it. The first panel is paper with teal type. A child that has its own children opens a second panel to the right.

Below 1050px the links move into the existing dialog. The dialog lists the same tree. A parent row is a link plus a button that opens its children. Donate Now stays in the dialog. The header pill is hidden there because the dialog lists it. Below 760px the logo is 48px tall. The dialog scrolls, and the close button stays at the top.

The footer stays teal. Its columns are the logo and tagline, each footer navigation group, and the address block. Phone, email, address, copyright, registration number, and the large wordmark come from LiftedPath settings. The logo is the custom logo from the Site Editor. At 1049px and below the footer groups sit in two columns. This unit adds a short open and close on the menus. Reduced motion makes that change instant, and the header color change stays instant.

## Implementation

### `GET /wp-json/liftedpath/v1/navigation`

Returns `{ primary, footer }`. Each list is the published `wp_navigation` post with that slug. An item is `{ label, path, children }`. `path` is a public path, an `http` or `https` URL on another host, or `null` when the item is a group heading. Nested blocks become `children`. A missing navigation post is an empty list.

### `GET /wp-json/liftedpath/v1/settings`

Returns `legal_name`, `short_name`, `tagline`, `registration_number`, `phone`, `email`, `postal_address`, `copyright_text`, and `logo`. `logo` is `null` or `{ id, url, alt, width, height }` from the custom logo. `form_recipient_email`, `marquee_phrases`, and `share_image` stay off this route.

### Frontend

`getNavigation()` is tagged `nav`. `getSettings()` is tagged `settings`. Both revalidate after 3600 seconds. The root layout passes the parsed values into the header and footer. Donate Now links to `/donate` and is not a primary menu item. The home band stays the Unit 10 placeholder.

## Dependencies

- Unit 08 navigation posts and organization settings.
- Unit 10 WordPress client.
- No new package.

## Verify when done

- [x] The visible result named in `context/specs/00-build-plan.md` is true
- [x] Editor-controlled values for this unit are fields, blocks, menus, media, or global styles
- [x] No invariant in `context/architecture.md` was broken
- [x] Desktop and 390px checked, when the unit has UI
- [x] Reduced motion checked, when the unit animates
- [x] Theme activates with no PHP fatal, when the unit touches PHP
- [x] `npm run build` passes, when the unit touches the frontend
- [x] `context/progress-tracker.md` updated
