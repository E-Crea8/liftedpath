# AI Workflow Rules

## Approach

Build LiftedPath one unit at a time from `context/specs/00-build-plan.md`.

Before writing code for a unit, write `context/specs/NN-name.md` using `context/specs/_template.md`. Implement that spec. Stop at the end of the unit.

Read the context files listed in `AGENTS.md` at the start of every session. The context files outrank memory of an earlier session. Design 01 at [https://liftedpath-design.lovable.app/](https://liftedpath-design.lovable.app/) is the visual reference. `context/ui-context.md` is the token contract.

WordPress 7.1.3 and the handbooks at [https://developer.wordpress.org/](https://developer.wordpress.org/) decide block, theme, and PHP questions. When a handbook page and a habit disagree, follow the handbook and record the choice in the spec.

## Scoping Rules

- Work on one feature unit at a time.
- Implement what the spec lists. Leave the next unit’s files alone.
- Keep a change inside one boundary: theme PHP, editor blocks, WordPress content model, or the Next.js app. A unit that already names two boundaries in the build plan is the exception, and its spec says so.
- Do not add a plugin, a package, a page, or a block that the current spec does not name.
- Do not restyle a finished section while building a different one.
- Preserve editor-entered content. A seed routine may insert content only when that content is absent.

## When to Split Work

Split the unit before coding if the spec combines any of these:

- A new REST route and a new visual page
- Theme PHP and a Next.js page in the same change, unless the build plan already pairs them
- Two unrelated blocks
- A behavior the context files do not define

If the result cannot be checked in the browser or with one REST request in the same session, the unit is too big. Split it in the build plan first, then write the narrower spec.

## Handling Missing Requirements

- Leave product behavior unspecified in the context files unimplemented. Ask by adding an open question to `progress-tracker.md`, then stop that part of the unit.
- When Design 01 shows a treatment this file set does not mention, add the treatment to `ui-context.md` or `project-overview.md` before coding it.
- When a child page in the prototype repeats its parent, seed the title and the short line that do exist. Leave the long copy for an editor. Do not write new program essays.
- When a donor-question answer is missing, seed the question and an empty answer.
- Choose the option already recorded in `architecture.md` when two implementations would both satisfy the spec.

## Protected Files

Do not modify these unless the current spec says to, or the user asks:

- `components/ui/*` beyond the token restyle described in `ui-context.md`
- `acf-json/*` by hand when ACF can write the JSON from the field group editor
- `Six-File+Context+Methodology/` — the playbook source, kept as reference
- `Lifted Path Design System.md` — the brand source. Change tokens in `ui-context.md`, and change the brand source only when the user changes the brand
- WordPress core, plugin internals, and `node_modules`
- Environment files that contain secrets, other than adding a named key the spec requires with an empty or local placeholder

## WordPress Editor Rule

A change an editor must be able to make belongs in a field, a block attribute, a menu, the media library, or global styles. Hardcoding that string, image, or color in a React or PHP component fails the unit.

Check this before closing a UI unit: open the relevant field in wp-admin, change the value, revalidate, and confirm the public page changed.

## Keeping Docs in Sync

Update the matching context file in the same unit when implementation changes any of these:

- A route, a block name, or a field
- The stack, a boundary, the storage model, or an invariant
- A token, a type style, or a component pattern
- The scope of the product

Then update `progress-tracker.md`.

## Verification Before the Next Unit

1. The unit’s verification checklist is true.
2. No invariant in `architecture.md` was broken.
3. `progress-tracker.md` shows the unit complete and names the next unit.
4. For a frontend unit, `npm run build` in `/Users/user/Local Sites/liftedpath/frontend` passes.
5. For a theme unit, the theme is active on `http://liftedpath.local` and wp-admin loads with no fatal error.
6. For a visual unit, the route was checked in a browser at desktop width and at 390px, including the empty or unpublished state the spec mentions.
7. Reduced motion was checked when the unit adds animation.

## Local Environment

- WordPress: `http://liftedpath.local`
- wp-admin: `http://liftedpath.local/wp-admin/`
- REST: `http://liftedpath.local/wp-json/`
- The local administrator username is `admin`. The password stays out of files. Use the Local app’s admin access.
- Frontend, once unit 09 exists: `http://localhost:3000`

## Session Start

1. Read `context/progress-tracker.md` and the spec for the current unit.
2. Continue that unit. Do not restart the theme scaffold if it is already marked complete.
3. If the previous session left the unit unverified, verify it before writing new code.
