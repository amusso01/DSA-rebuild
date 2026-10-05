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
- **Every `<main>` has `id="main"`** (the skip-link target), in all current and future templates. Identify a page with a modifier class (`site-main--contact`), never by changing the id.
- **Every section is wrapped** `<section class="content-block"><div class="content-max">…</div></section>`, applied per section and never to `<main>` or a whole template (see "Layout wrappers" in restructure.md).
- **Markup components go in `components-2026/<area>/<name>.php`** (loaded with `get_template_part()` + `$args`), with their SCSS in `src-2026/scss/components/_<name>.scss`. SVGs go in `svg-templates/`. Reuse `components-2026/partials/button.php` for buttons (see "Components" in restructure.md).
- **JS: one module per component** in `src-2026/js/modules/<componentName>.js`, as a default-export function (early return if its element is missing) called from `main.js` inside `ready()`. Import libraries normally. No lazy `import()` unless it's a large library used on one page (see "Decisions" in restructure.md).
- **SCSS is desktop first.** Use only the include-media breakpoints listed in restructure.md.
- **pnpm only.** Never use yarn or npm install.
- **Keep restructure.md current.** When the structure, build or wiring changes, update it and add a line to its Log.
