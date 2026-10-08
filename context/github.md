# GitHub

The theme in this directory is the GitHub repository [E-Crea8/liftedpath](https://github.com/E-Crea8/liftedpath).

## Repository

| Item | Value |
| --- | --- |
| Web URL | https://github.com/E-Crea8/liftedpath |
| Owner | E-Crea8 |
| Default branch | `main` |
| Remote name | `origin` |
| SSH remote | `git@github-ecrea8:E-Crea8/liftedpath.git` |

`main` is the branch that holds this theme. Feature work uses `feat/NN-feature-name`, with `NN` matching the unit number in `context/specs/`.

## SSH

`~/.ssh/config` sends plain `github.com` through the StackForgeAI key. This repository belongs to the E-Crea8 account, which has its own host alias.

| Item | Value |
| --- | --- |
| Host alias | `github-ecrea8` |
| Host name | `github.com` |
| Key | `~/.ssh/id_ed25519_ecrea8` |
| Public key comment | `e-crea8@github` |

Use the alias in every remote URL. `git@github.com:E-Crea8/liftedpath.git` authenticates as a different account and is rejected.

Check the connection with:

```
ssh -T git@github-ecrea8
```

A successful reply is `Hi E-Crea8!`.

## What this repository tracks

- Theme PHP, `theme.json`, templates, parts, blocks, and `acf-json/`
- Editor assets, fonts, and logo files in this directory
- Context files, specs, and `AGENTS.md` / `CLAUDE.md`
- The Design 01 reference in `liftedpath-design-main/`
- The root `README.md` already on GitHub

Left out of git: `.DS_Store`, editor settings, logs, `.env` files, `node_modules`, `vendor`, secrets, application passwords, and the local wp-admin password.

## Related code that is not this repository

These pieces belong to the same product. They are not commits on `E-Crea8/liftedpath`.

| Piece | Local path | Git |
| --- | --- | --- |
| Next.js public site | `/Users/user/Local Sites/liftedpath/frontend` | No repository yet |
| LiftedPath Forms plugin | `/Users/user/Local Sites/liftedpath/app/public/wp-content/plugins/liftedpath-forms` | No repository yet |
| WordPress core, uploads, and other plugins | `/Users/user/Local Sites/liftedpath/app/public` | Stay out of this repository |

Production hosting stays as recorded in `context/architecture.md`: WordPress on SiteGround, the public site on Vercel. Those hosts are separate from this GitHub remote.

## Everyday git

```
git status
git pull origin main
git push origin HEAD
```

Push a feature branch with `git push -u origin HEAD`. Open the pull request against `main` on GitHub.
