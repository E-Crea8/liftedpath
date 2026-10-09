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

Never push to `main`, and never deploy, until the user explicitly says to in that request. A commit or a live error is not permission.
