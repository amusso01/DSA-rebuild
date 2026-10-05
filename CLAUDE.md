# DSA WordPress theme

This theme is being rebuilt alongside the live legacy code. The full context, build and conventions are in restructure.md, imported below. Follow it for every task.

@restructure.md

## Must-follow rules
- **Don't edit legacy files:** `src/`, `dist/`, `webpack.config.js`, `style.css`, `functions.php`, `library/function-setup.php` and the existing PHP templates. If a change seems needed, stop and ask first.
  - `functions.php` has exactly one approved 2026 line, the require of `inc/function-dev.php`. Don't add more.
- **New front-end source lives in `src-2026/`.** `pnpm build:2026` builds it into `dist-2026/`, which is generated, so never edit it by hand.
- **New PHP:**
  - Functions go in `inc/function-<topic>.php`, required from `inc/function-dev.php`.
  - Page templates go in `templates-2026/`.
  - Header and footer variants are `header-new.php` and `footer-new.php`.
  - Never modify the originals.
- **Every section is wrapped** `<section class="content-block"><div class="content-max">…</div></section>`, applied per section and never to `<main>` or a whole template (see "Layout wrappers" in restructure.md).
- **SCSS is desktop first.** Use only the include-media breakpoints listed in restructure.md.
- **pnpm only.** Never use yarn or npm install.
- **Keep restructure.md current.** When the structure, build or wiring changes, update it and add a line to its Log.
