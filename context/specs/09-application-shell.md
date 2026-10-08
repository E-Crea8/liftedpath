# Unit 09: Application shell

## Goal

The public Next.js app exists at `/Users/user/Local Sites/liftedpath/frontend`. `http://localhost:3000` shows the LiftedPath frame: fixed header, home band, sample section, and footer, in Design 01 type and color, at desktop width and at 390px.

## Design

Tokens come from `context/ui-context.md`. The page background is paper. The header is 89px, fixed, and transparent over a deep home band, then teal after the page scrolls. Paper type sits on that band. The sample section heading uses Stack Sans Headline. Body, navigation, and the gold pill use Plus Jakarta Sans.

The content column is 1120px. The minimum inset is 24px, and 18px below 760px. At 520px and below, the display heading uses `clamp(2.45rem, 11vw, 3.3rem)` and the section heading is 2.15rem. Focus is a 2px bronze outline with a 3px offset.

Desktop shows the top-level links and Donate Now. Below 1050px those links move into a dialog opened by the menu button, and below 760px the header Donate button is hidden because the dialog still lists it. The footer is teal. Its four groups sit in a row on desktop and in two columns from 1050px down. The groups stack only if a column would be narrower than the address line.

This unit does not animate. Reduced motion still leaves the header color change instant.

## Implementation

### App

Create the Next.js App Router app with TypeScript, Tailwind CSS, and ESLint. Do not install shadcn/ui or GSAP in this unit. Do not fetch WordPress.

`styles/globals.css` defines the token variables. Tailwind utilities (`bg-paper`, `text-teal`, `font-display`, `max-w-content`, `rounded-pill`) point at those variables. Components do not add hex colors.

`app/layout.tsx` loads two families from the theme font files, each with a latin file and a latin-ext file:

- Plus Jakarta Sans, weight 200–800, as `--font-body`
- Stack Sans Headline, weight 200–700, as `--font-display`, falling back to Plus Jakarta Sans

The build plan’s “three fonts” is these two families plus the generic `sans-serif` fallback. Fraunces, Marcellus, and Source Sans 3 stay unloaded. Public CSS does not request Google Fonts.

### Frame

`components/layout/Header.tsx` and `components/layout/Footer.tsx` are the static frame. Labels, the logo file, the phone number, the address, and the tagline are placeholders. Unit 11 replaces them with navigation and settings. The logo file is `public/brand/liftedpath-logo-light.png`, copied from the media library for this shell only.

The header links are Home, About Us, What We Do, Impact, and Contact Us, plus Donate Now to `/donate`. What We Do is one link. The nested menu is Unit 11.

`app/(site)/page.tsx` composes `components/layout/HomeFrame.tsx`: a deep band with the display heading, one sentence, and a gold Donate Now pill, then one paper section with an eyebrow, a section heading, and one paragraph. Unit 12 replaces this frame with the WordPress home blocks.

`app/(site)/not-found.tsx` keeps the same header and footer so an unknown path still shows the frame.

The menu button opens a native dialog. Escape and the close button dismiss it. No route other than `/` is built.

## Dependencies

- Next.js, React, TypeScript, Tailwind CSS, ESLint. This is the first frontend unit.
- `lucide-react` for the menu, close, and arrow icons named in `ui-context.md`.
- No WordPress package and no environment secret.

## Verify when done

- [x] `http://localhost:3000` shows the header, home band, sample section, and footer
- [x] The static labels are documented as placeholders for Unit 11, and this unit does not fetch WordPress
- [x] No invariant in `context/architecture.md` was broken
- [x] Desktop column is 1120px and the header shows the links
- [x] At 390px the column inset is 18px, the section heading is 2.15rem, and the menu dialog lists the links
- [x] Reduced motion keeps the header color change instant and adds no animation
- [x] The page does not request Google Fonts
- [x] `npm run build` passes
- [x] `context/progress-tracker.md` updated
