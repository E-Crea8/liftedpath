## Application Building Context

This workspace is the LiftedPath WordPress block theme for the LiftedPath Empowerment Foundation website. The public site is a separate Next.js application. Its path, stack, and rules are defined in the context files below.

Read the following files in order before implementing or making any architectural decision:

1. `context/project-overview.md` — product definition, goals, features, and scope
2. `context/architecture.md` — system structure, boundaries, storage model, and invariants
3. `context/ui-context.md` — theme, colors, typography, and component conventions
4. `context/code-standards.md` — implementation rules and conventions
5. `context/ai-workflow-rules.md` — development workflow, scoping rules, and delivery approach
6. `context/progress-tracker.md` — current phase, completed work, open questions, and next steps
7. `context/github.md` — GitHub repository, SSH remote, and related code that is not in this repo

`context/`, `logos/`, `liftedpath-design-main/`, and `Six-File+Context+Methodology/` stay on this machine. They are not on GitHub.

The build order lives in `context/specs/00-build-plan.md`. Implement one unit at a time, from a spec file written immediately before that unit.

Update `context/progress-tracker.md` after each meaningful implementation change.

If implementation changes the architecture, scope, or standards documented in the context files, update the relevant file before continuing.

Published WordPress edits reach the public site through `LIVE-WORDPRESS-UPDATES.md`. Do not change that flow unless the user asks.

**STRICT RULE: NEVER push to `main` without permission.** Push only when the user says **push to main** in that same request. “Fix it”, “fix once and for all”, “continue”, a commit, a live outage, or SiteHold is not permission. Never deploy until the user says to deploy. Full rule: `context/github.md`.

**STRICT RULE: never stall on a browser check.** Confirm a page with one HTTP request. Do not wait on the in-IDE browser, and do not retry it if it stalls. Full rule: `context/ai-workflow-rules.md`. Incident: `context/incidents/2026-10-25-browser-check-stall.md`.

### Public site must stay up

The Next.js site on Vercel is the live public website. A green Vercel build is not enough. After any `frontend/` change that reaches Production, confirm the live HTML still shows real page content (not SiteHold “kept online”) and that `https://www.liftedpathfoundation.org/api/wp-health` returns `ok: true` once that route is deployed. Vercel `WORDPRESS_API_URL` must be `https://cms.liftedpathfoundation.org/wp-json` — never the www/apex public host and never `liftedpath.local`. Incident checklist: `context/incidents/2026-10-14-site-hold-outage.md`.
