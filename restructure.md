# DSA theme – 2026 restructure

We are rebuilding the layout of this live WordPress theme **alongside the legacy code**. The live site must keep working, unchanged, until the new layout is switched on. Every piece of new work lives in new files and folders. The legacy code is frozen.

## Rules

1. **Don't modify legacy code.** Legacy means:
   - `src/`, `dist/` and `webpack.config.js`
   - `style.css`, `functions.php` and `library/function-setup.php`
   - every existing template: `header.php`, `footer.php`, `index.php`, `page.php`, `front-page.php`, `single.php`, `templates/`, `template-parts/`

   If a task seems to need a change to one of these, stop and ask Andrea first.

   **Only exception:** `functions.php` has one approved line (2026-10-05), `require … '/inc/function-dev.php'`. Don't add more there. Register new 2026 PHP files in `inc/function-dev.php` instead.
2. **New work goes into new files:**
   - Front-end source goes in `src-2026/`.
   - PHP functions go in `inc/`.
   - Page templates go in `templates-2026/`.
   - Header and footer variants go in the theme root as `header-new.php` and `footer-new.php`, because that's where `get_header('new')` and `get_footer('new')` look. `header-new.php` started as a copy of `header.php`.
3. **`dist-2026/` is generated.** Never edit it by hand: it's wiped on every build.
4. **pnpm only.** Never run `yarn` or `npm install`: they would create a second lockfile and fight pnpm over `node_modules`.
5. **Commit production output only.** Run `pnpm build:2026` before committing. Source maps are git-ignored.

## Build

| Command | What it does |
| --- | --- |
| `pnpm dev:2026` | Watches `src-2026/` and rebuilds unminified on save, with source maps |
| `pnpm build:2026` | Production build: minified `dist-2026/main.css`, `dist-2026/main.js` and `dist-2026/fonts/` |
| `pnpm build` | Legacy build (`src/index.js` to `dist/bundle.js`). You shouldn't need to run it |

Config: `webpack.2026.config.js`. It is separate from the legacy `webpack.config.js`, which stays untouched.

- **Entry:** `main` is `src-2026/js/fonts.js` plus `src-2026/js/main.js` plus `src-2026/scss/main.scss`, which outputs `dist-2026/main.js` and `dist-2026/main.css`. Lazy `import()` chunks go to `dist-2026/chunks/`.
- **SCSS pipeline:** sass-loader (Dart Sass) runs first, then postcss-loader (autoprefixer), then css-loader, and the CSS is extracted to a file.
- **In SCSS, `url()` is left exactly as written** (`css-loader` with `url: false`). Paths resolve relative to `dist-2026/main.css`: a legacy image is `../dist/images/x.svg`.
- **Plain `.css` files** (the Fontsource packages) go through their own rule, where `url()` *is* resolved. The font files are copied to `dist-2026/fonts/[name].[hash].woff2`.
- **Browser targets are set inline in the config.** Don't add a `browserslist` field or a `.browserslistrc` file: webpack would also use it for the legacy build and change `dist/bundle.js`.
- **JS target is `es2020`, with no Babel.**
- **`jquery` is an external.** `import $ from 'jquery'` uses the global jQuery 3.1.1 that the legacy setup loads from CDN. It isn't dequeued on 2026 pages.
- **`quietDeps`:** Sass deprecation warnings from `node_modules` (e.g. include-media) are hidden. Warnings from our own code still show.

The legacy SCSS (`dist/styles/main.scss` and `map/*`) is compiled **outside** this repo by an editor extension and isn't part of any build here. Leave it alone.

## Folder structure

```
functions.php          legacy + one require of inc/function-dev.php
header-new.php         2026 header (loaded by get_header('new')), sections already wrapped
inc/                   2026 PHP
  function-dev.php     entry: requires the other inc files + theme support (custom logo)
  function-assets.php  fonts/CSS/JS on 2026 pages, dequeues legacy assets there
templates-2026/
  page-2026.php        "2026 Layout (preview)" page template
  page-contact-2026.php "Contact 2026" page template (main#contact)
src-2026/
  js/
    fonts.js           Fontsource imports (self-hosted fonts)
    main.js            entry: list of eager modules, init on DOM ready
    modules/           one file per feature, each exports init()
    utils/ready.js     DOM-ready helper
  scss/
    main.scss          entry: only @use lines (common first, then components)
    common/
      _reset.scss      modern CSS reset (global!)
      _variables.scss  colors, fonts, type scale, no CSS output
      _media.scss      include-media + breakpoints (single source of truth)
      _general.scss    base typography: html, headings, links
      _helper.scss     layout helpers: .content-block, .content-max, .content-narrow, %cover…
    components/
      _button.scss
dist-2026/             build output (generated, committed)
webpack.2026.config.js
```

## How 2026 pages work

A page is a 2026 page when its template calls **`get_header('new')`**. Nothing else is needed.

- WordPress fires the `get_header` action with `'new'` before it loads `header-new.php`. `inc/function-assets.php` hooks into that action and, for that request only:
  - **dequeues the legacy assets** from `library/function-setup.php`:
    - styles `bootstrap-styles`, `foundry-styles`, `foundry-slick`, `foundry-slick-theme`
    - scripts `bootstrap-jquery`, `slick-jquery`, `main-jquery`, `bundle`
  - **keeps `jquery`**, because plugins may need it;
  - **enqueues `dist-2026/main.css` and `main.js`**, the JS in the footer, both versioned with `filemtime()`;
  - **adds the `layout-2026` class to `<body>`.**
- Every other page behaves exactly as before.
- **2026 pages are a clean slate: no Bootstrap and no legacy CSS.** The whole page (header, content, footer) has to be built with 2026 markup and styles. Until a `footer-new.php` exists, the legacy `footer.php` renders unstyled on 2026 pages.

**Preview on the live site:** create a **private** page in WP admin and pick the template "2026 Layout (preview)". Only logged-in editors can see it.

**Status (2026-10-05):** wired, but no public page uses `get_header('new')` yet. `header-new.php` is still a copy of the legacy header markup.

## Layout wrappers (`.content-block` + `.content-max`)

Every new section and component, the header and footer sections included, uses the same two wrappers. They are defined in `src-2026/scss/common/_helper.scss`.

```html
<section class="hero content-block">   <!-- full width: background + side padding -->
	<div class="content-max">           <!-- max-width, centred -->
		…section content…
	</div>
</section>
```

- **Section level only.** Put the wrappers on each section or component as you build it. **Never** put them on `<main>` or around a whole template: a template's `<main>` stays bare and holds a list of wrapped sections.
- **Always nest them, in this order:** `.content-block` outside, `.content-max` inside. The section stays full width, so backgrounds run edge to edge even above 1920px.
- **`.content-block`** is the side padding (gutter):

  | Width | Padding |
  | --- | --- |
  | Base (desktop) | 120px |
  | `<desktop` (1440) | 80px |
  | `<tablet` (1140) | 48px |
  | `<phone` (640) | 25px |

  There is no `.content-block--footer`. The footer uses `.content-block` like everything else.
- **`.content-max`** is the max-width: 1920px, then 1520px below `wideScreen`, then 1360px below `desktop`. **Don't change these values.**
- **`.content-narrow`** (max-width 1024px, centred) is optional. Use it inside `.content-max` for text-heavy content.

## Fonts

Self-hosted from npm (Fontsource) and bundled by webpack. No requests to Google.

| Family | Weights | SCSS variable | Used for |
| --- | --- | --- | --- |
| Manrope | 400, 500, 600, 700 | `$text__fontname` | body text |
| Titillium Web | 400, 700 | `$header__fontname` | headings |

- **Only the Latin and Latin-extended subsets are loaded.** `unicode-range` means browsers fetch Latin-extended only when a page needs it.
- **Only the weights in the table exist.** Any other weight is faked by the browser.
- **To add a weight**, add both `@fontsource/<family>/latin-<weight>.css` and `latin-ext-<weight>.css` to `src-2026/js/fonts.js`, then update this table.
- **To add a family**, run `pnpm add -D @fontsource/<family>` and do the same.

## PHP conventions (`inc/`)

- **Name files `inc/function-<topic>.php`**, the same pattern as the legacy `library/function-setup.php`, and require each one from `inc/function-dev.php`.
- **Prefix functions with `dsa_2026_`** and asset handles with `dsa-2026`.
- **Code style:** tabs, `array()` and the `/*===…*/` section headers, like the legacy files.
- **Theme support** (`add_theme_support`) goes in `dsa_2026_theme_support()` in `function-dev.php`, hooked on `after_setup_theme`.
- **Custom logo is enabled.** Editors set it in Appearance > Customize > Site Identity > Logo, and templates print it with `the_custom_logo()` (or `get_custom_logo()` to get it as a string).

## SCSS conventions

- Use the module system, `@use`/`@forward`. **Never `@import`**, which is deprecated in Dart Sass.
- **New partial:** create `_name.scss` in the right folder, then add `@use 'folder/name';` to `main.scss`.
- **Each partial pulls in only what it needs:**
  - Inside `common/`: `@use 'variables' as *;` and `@use 'media' as *;`
  - Elsewhere: `@use '../common/variables' as *;` and `@use '../common/media' as *;`
- **Variable naming:** `$color__name`, `$text__h1`…`$text__p`, `$text__fontname`, `$header__fontname`.
- **Breakpoints:** [include-media](https://eduardoboucas.github.io/include-media/), defined only in `common/_media.scss`:

  | Name | Width |
  | --- | --- |
  | `phone` | 640px |
  | `phone-land` | 920px |
  | `tablet` | 1140px |
  | `desktop` | 1440px |
  | `wideScreen` | 1920px |

- **Always desktop first.** Write the base styles for desktop, then override for smaller screens with `<` queries, from the largest to the smallest. Don't write mobile-first `>` queries.

  ```scss
  .card {
  	padding: 48px; // desktop

  	@include media('<tablet') {
  		padding: 32px;
  	}
  	@include media('<phone') {
  		padding: 20px;
  	}
  }
  ```

  Only the names in the table above exist. There is no `mobile`: use `phone` (or `phone-land`). An unknown name breaks the build.
- **Don't `@use` legacy SCSS** from `dist/styles/`. It outputs CSS rules as well as variables. Copy any value you need into `_variables.scss`.
- **The 2026 CSS is global and starts with a reset.** It only loads on 2026 pages (see above), and it must never be enqueued on legacy pages.

## JS conventions

- `main.js` keeps a list of eager modules and runs `module.init()` for each inside `ready()`.
- **Each module exports `init()`.** It finds its root element (e.g. `document.querySelector('[data-2026="header"]')`) and **returns early if the element isn't there**. A module must never throw on a page that doesn't use it.
- **Never assign `window.onload`.** Use `ready()` or `addEventListener`. The legacy `dist/bundle.js` assigns it, and only the last assignment wins.
- **Load heavy, page-specific modules lazily** with `import('./modules/x')`.
- **jQuery is optional.** If you need it, `import $ from 'jquery'` gives you the global copy.

## Deploying to the live server

1. Run `pnpm build:2026` and commit the result.
2. Upload `inc/`, `dist-2026/`, `templates-2026/` and `header-new.php` **first**.
3. Upload `functions.php` **last**. Its `require` of `inc/function-dev.php` is a fatal error if `inc/` isn't on the server yet.
4. Never upload `node_modules/` or `src-2026/`. The server only needs the built `dist-2026/`.

## Log

- **2026-10-05**
  - Migrated yarn to pnpm. The legacy `dist/bundle.js` was verified byte-identical afterwards.
  - Added `webpack.2026.config.js` and the `src-2026/` skeleton.
  - Added include-media.
  - SCSS restructured into `common/` and `components/`.
- **2026-10-05**
  - Self-hosted fonts via Fontsource: Manrope 400–700 and Titillium Web 400/700, Latin and Latin-extended only.
  - Added `inc/`: `function-dev.php` (custom logo support) and `function-assets.php` (2026 assets on `get_header('new')` pages, legacy assets dequeued there).
  - Added the `templates-2026/page-2026.php` preview template.
  - `functions.php` +1 require line, approved by Andrea.
- **2026-10-05**
  - `.content-block` padding is now 120/80/48/25 and `.content-block--footer` is removed. `.content-max` is unchanged.
  - Section wrappers documented as the standard.
  - Applied to `page-2026.php`, the new `page-contact-2026.php` and the two sections of `header-new.php`.
