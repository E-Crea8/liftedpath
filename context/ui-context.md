# UI Context

The public site implements Design 01, “Original”, at [https://liftedpath-design.lovable.app/](https://liftedpath-design.lovable.app/).

The brand source is `Lifted Path Design System.md` in the theme root. This file is the implementation contract. Tokens here are registered in `theme.json` and consumed as CSS variables. Design 01, as shipped at the Lovable URL and in `liftedpath-design-main`, decides color, type, spacing, and component treatment. Record any new disagreement in this file before building the component.

## Resolved against the brand note

- Type is Plus Jakarta Sans for body, navigation, and buttons, and Stack Sans Headline for display and headings. Fraunces, Marcellus, and Source Sans 3 are not loaded.
- The content column is 1120px. The brand note’s 1024px column is not used.
- Links on paper are teal and turn bronze on hover. Text gold `#896A38` is not a text color.
- Focus is a 2px solid bronze outline with a 3px offset.
- Cards use an 18px radius. Photographs use 16px.
- Surfaces use the Design 01 mixes measured from the live page. The brand note’s teal and gold tint ramps are not painted.

## Theme

Warm, rooted, and spacious. Deep teal carries the page. Gold appears the way sunlight does in the logo: one accent in a section, never a flood of gold. Surfaces are paper white. Photography shows people in action, in natural light, with agency. Corners are soft. The hero is a full-bleed photograph under a teal veil, with paper-white type and a gold pill button.

The site is light. There is no dark theme. Dark sections (hero veil, footer, an occasional band) are teal surfaces with paper text, still inside the same palette.

Headings are sentence case. The program codes LIFT-ROOT, LIFT-GROW, LIFT-BEYOND, and LIFT-EQUIP stay in capitals. Keep a line of body text under about 70 characters.

## Colors

Register every row in `theme.json` palette or as a custom property. Components use the token name.

### Brand

| Role | CSS variable | Value | Use |
| --- | --- | --- | --- |
| Paper | `--color-paper` | `#FCFDFD` | Page background, text on teal |
| Teal | `--color-teal` | `#043C42` | Text, headings, header and footer surfaces, button label on gold |
| Deep | `--color-deep` | `#02272B` | Hero veil. Teal mixed 76% toward black |
| Gold | `--color-gold` | `#B78D4B` | Primary button fill, icon discs, large accents |
| Bronze | `--color-bronze` | `#A57D3C` | Link hover on paper, focus outline |
| Sand | `--color-sand` | `#D6B886` | Accent text and link hover on dark surfaces, program labels |

### Surfaces

Measured from the live Design 01 mixes. Surfaces and borders. Muted is the secondary text color.

| Token | Value | Mix |
| --- | --- | --- |
| `--color-surface` | `#E7ECEC` | Teal 8% into paper, in oklch |
| `--color-warm` | `#F3ECE0` | Sand 24% into paper, in oklab |
| `--color-cool` | `#D3DBDD` | Teal 16% into paper, in oklab |
| `--color-muted` | `#4C6E71` | Teal 72% into paper, in oklch |
| `--color-line` | `#D3DCDC` | Teal 16% into paper, in oklch |

The How we work icon and hover washes use the Design 01 oklch brand values directly. Mixing the hex tokens above changes those pale tints. A saved hover color on an action replaces that position’s wash.

### Interface

Error and success stay off marketing sections. They are custom properties, not palette swatches.

| Role | CSS variable | Value |
| --- | --- | --- |
| Hero veil | `--color-veil` | Deep at 96%, 68%, and 35% opacity, left to right |
| Card shadow | `--shadow-card` | `0 14px 34px rgba(2, 39, 43, 0.10)` |
| Note shadow | `--shadow-note` | `0 15px 30px rgba(2, 39, 43, 0.12)` |
| Error | `--color-error` | `#8C3A32` |
| Success | `--color-success` | `#1B5E3B` |

### Contrast

These pairs are the allowed text treatments. A new pair gets measured and added here before it ships.

| Pairing | Ratio | Use |
| --- | --- | --- |
| Teal on paper | 11.9 : 1 | Body, headings, links |
| Paper on teal | 11.9 : 1 | Text on the footer and other teal bands |
| Paper on deep | 15.5 : 1 | Hero type |
| Sand on teal | 6.4 : 1 | Accent text on dark |
| Sand on deep | 8.3 : 1 | Accent text on the hero |
| Muted on paper | 5.5 : 1 | Secondary lines |
| Muted on surface | 4.7 : 1 | Secondary lines on the surface tint |
| Teal on gold | 4.0 : 1 | Primary button label, as Design 01 paints it |
| Gold on deep | 5.2 : 1 | Large accent type on the hero |
| Gold or bronze on paper | 3.0 to 3.7 : 1 | Decorative fills and large display only |

Gold and bronze are never body text on paper. The primary button keeps the Design 01 pairing: teal label on a gold pill.

## Typography

Design 01 uses these two families. The files live in `assets/fonts` and are registered in `theme.json`. They are the faces the public site loads.

An administrator can install another family from the Site Editor’s Google Fonts collection (Styles → Typography → Manage fonts). WordPress copies that file onto this site and serves it from here. Public pages do not request `fonts.googleapis.com` or `fonts.gstatic.com`, so choosing a font does not add a Google lookup to the visitor’s first paint.

| Role | Family | CSS variable | Notes |
| --- | --- | --- | --- |
| Display and headings | Stack Sans Headline | `--font-display` | Variable weight 200–700. Falls back to Plus Jakarta Sans |
| Body, navigation, buttons | Plus Jakarta Sans | `--font-body` | Variable weight 200–800 |

| Style | Family | Size / line height | Weight | Token |
| --- | --- | --- | --- | --- |
| Display | Stack Sans Headline | `clamp(3rem, 6.25vw, 5rem)` / 1.15 | 600 | `--text-display` |
| Heading | Stack Sans Headline | `clamp(2.25rem, 4.38vw, 3.5rem)` / 1.15 | 500 | `--text-heading` |
| Heading 3 | Stack Sans Headline | 1.28rem / 1.28 | 600 | `--text-h3` |
| Stat | Stack Sans Headline | 2.6rem / 1.15 | 650 | `--text-stat` |
| Body large | Plus Jakarta Sans | 1.03rem / 1.58 | 400 | `--text-body-lg` |
| Body | Plus Jakarta Sans | 1rem / 1.58 | 400 | `--text-body` |
| Eyebrow | Plus Jakarta Sans | 0.85rem / 1.2 | 600 | `--text-eyebrow` |
| Navigation | Plus Jakarta Sans | 0.86rem / 1.2 | 700 | `--text-nav` |
| Button | Plus Jakarta Sans | 0.83rem / 1.2 | 800 | `--text-button` |
| Caption | Plus Jakarta Sans | 0.78rem / 1.4 | 400 | `--text-caption` |

At 520px and below, display becomes `clamp(2.45rem, 11vw, 3.3rem)` and the section heading becomes `2.15rem`. Body stays 1rem. Headings use letter-spacing 0.

The wordmark and the hero stat use weight 650. Eyebrows are sentence case, in a surface pill with a line border.

## Spacing

| Token | Value |
| --- | --- |
| `--space-4` | 4px |
| `--space-8` | 8px |
| `--space-12` | 12px |
| `--space-16` | 16px |
| `--space-18` | 18px |
| `--space-20` | 20px |
| `--space-24` | 24px |
| `--space-28` | 28px |
| `--space-32` | 32px |
| `--space-40` | 40px |
| `--space-48` | 48px |
| `--space-64` | 64px |
| `--space-65` | 65px |
| `--space-80` | 80px |
| `--space-96` | 96px |
| `--space-128` | 128px |

Section padding is 128px on desktop, 80px at 760px and below, and 65px at 520px and below. The page inset is 24px, and 18px at 760px and below. Stack related text with 16px or 24px.

## Layout

| Token | Value | Use |
| --- | --- | --- |
| `--content-max` | 1120px | Text and card columns. `theme.json` content and wide sizes |
| `--gutter` | 24px, 18px below 760px | Minimum page inset |
| `--logo-height` | From LiftedPath settings, default 104px | Header logo height |
| `--header-height` | Logo height plus 16px | Header bar. Taller than the Design 01 89px mark so the stacked logo’s “Empowerment Foundation” line stays readable |
| Columns | 12 | Card grids inside the content column |

- The header is fixed and transparent over the hero, then teal after scroll. Logo at the start, menu button on small screens, Donate Now at the end. Desktop shows the full navigation. The bar is the logo height plus 16px. Below 760px the logo is 82% of that height.
- Heroes and the marquee bleed to the viewport edges. Copy inside them sits in the content column.
- Inner pages start with a page hero, then sections, then the join band, then the footer.
- The footer is a teal surface with three groups: brand and tagline, quick links, what we do, plus the address block. On small screens the groups stack.

## Border Radius

| Context | Token | Value |
| --- | --- | --- |
| Small controls and inputs | `--radius-sm` | 8px |
| Notes and small panels | `--radius-control` | 14px |
| Photographs | `--radius-image` | 16px |
| Cards and panels | `--radius-card` | 18px |
| Buttons, tags, avatar group | `--radius-pill` | 999px |

A card that sits above a photograph uses `--shadow-card`. The call note uses `--shadow-note`.

## Buttons and Links

Every interactive element has a 2px solid bronze outline with a 3px offset.

| Variant | Fill | Label | Where |
| --- | --- | --- | --- |
| Primary | Gold | Teal | Donate Now and the main action. Hover fills with teal and the label turns paper |
| Primary on a gold panel | Teal | Paper | The sponsor card |
| Outline | Transparent, paper border | Paper | A second action on a dark band |
| Text link | None | Teal, bronze on hover | Inline links on paper. On dark bands the hover is sand |

Buttons are pills, at least 48px tall. The donate and send actions include a simple arrow icon after the label, from Lucide (`ArrowRight`), 18px, stroke 2. On Design 01 the arrow rotates 45 degrees on hover. The button itself does not lift.

## Component Patterns

Match these to Design 01. Copy for each pattern is editor content, listed in `project-overview.md`.

- **Hero.** Full-bleed photo, teal veil, logo row already in the header. Inside the column: overlapping circular portraits, a tagline pill, display heading, body large intro, gold primary button.
- **Stat.** A large Stack Sans Headline figure and a short Plus Jakarta Sans label.
- **Marquee.** One line of tracked tagline phrases in capitals, separated by ✦, looping. CSS animation. Pause the loop for reduced motion and show the phrases as a wrapping line.
- **Tabs.** Mission, Vision, Commitment. One panel visible. The tab list is a row of pills. The selected pill uses the gold button fill. The others use the surface fill.
- **Program cards.** Image, pillar label, title, short line, “Read more”. Radius medium. Cards sit in a responsive grid: one column on small screens, two then three as the viewport allows inside the content width.
- **Action grid.** Four items, each with a Design 01 symbol (give, partner, empower, reachout), a title, a sentence, and a text action.
- **Numbered list.** Stewardship, process, pillars, and goals share one pattern: a two-digit index, a heading 3, and one sentence.
- **CTA band.** A teal or paper band with one heading, one sentence, and one button.
- **FAQ.** An accordion. One item open at a time.
- **Forms.** Labels above fields. Inputs use radius small, a teal border, paper fill, and the bronze focus outline. The reason and designation controls are native selects or a radio group styled as pills.
- **Donate panel.** Frequency as two pills, currency as three pills, amount as a grid of choices plus a custom field, then the designation, then the arrange button. A line under the button states that the site does not collect payment.
- **Join band.** “Join the journey”, the sentence “Let’s lift young people together.”, and a link to Contact.

## Header and Menu

- Logo is the full lockup on paper or, on the teal header, the all-white or all-sand version. Clear space around the lockup is the height of the letter L. Below 140px wide, use the circular mark alone.
- Do not recolor the teal in the logo, stretch the mark, add a shadow, or place the full-color logo on a busy photograph.
- The mobile menu is a dialog that covers the page, listing the same tree as the desktop menu, with Donate Now still available.
- Desktop nested menus open on hover and on focus, and they stay open while the pointer or focus is inside.

## Icons

Lucide, stroke 2, `currentColor`. 16px beside inline text, 18px in buttons, 20px for a standalone control such as the menu button. The marquee star is the ✦ character, not an icon. Do not mix a second icon set.

## Photography

People are the subject, in action, in natural light and warm tones. The hero veil keeps type readable. Cards crop to a consistent ratio once that ratio is measured from Design 01 and written here. Alt text comes from the media library.

## Motion

- Marquee: CSS, linear, looping. Reduced motion: static wrapped text.
- Section entrance and the hero text: GSAP, short, eased, run once when the section enters. Reduced motion: content is visible immediately.
- Tabs, accordion, and the mobile menu: a short opacity and height transition. Reduced motion: instant.
- Editors do not configure durations in wp-admin. Motion is part of the presentation layer.

## shadcn/ui

Initialize shadcn against the CSS variables above, not the default zinc theme. Use the CLI to add `button`, `dialog`, `tabs`, `accordion`, `input`, `label`, `select`, and `textarea` when the unit that needs them starts. Restyle each added component to the radius, type, and color tokens in this file. Keep the files in `components/ui/` and change only what the LiftedPath tokens require.

## Logo Files

The header logo and its height are on the Header tab of LiftedPath settings. The footer logo, footer logo height, and the large bottom word are on the Footer tab. The public site reads them from `GET /wp-json/liftedpath/v1/settings`. The artwork is the stacked lockup, so “Empowerment Foundation” stays readable. The footer word stretches to the viewport width.
