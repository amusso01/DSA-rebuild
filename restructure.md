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
   - Markup components (PHP partials) go in `components-2026/`, with their SCSS in `src-2026/scss/components/`.
   - Inline SVGs go in `svg-templates/`.
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

- **Entry:** `main` is `src-2026/js/fonts.js` plus `src-2026/js/main.js` plus `src-2026/scss/main.scss`, which outputs `dist-2026/main.js` and `dist-2026/main.css`.
- **All JS, libraries included, is bundled into `main.js` with normal `import` statements.** See "Decisions" below.
- **`dist-2026/chunks/` doesn't exist today.** Webpack only creates it when code uses a dynamic `import('…')`, which is reserved for a large library needed on one page.
  - If it ever appears, the files in it (`<name>.<hash>.js`) are build output that `main.js` fetches on demand: never edit them, and always deploy them with `main.js`.
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
header-new.php         2026 header (loaded by get_header('new')): markup only, parts in components-2026/header/
inc/                   2026 PHP
  function-dev.php        entry: requires the other inc files + theme support (custom logo, 2026 nav menus)
  function-assets.php     fonts/CSS/JS on 2026 pages, dequeues legacy assets there
  function-helpers.php    dsa_2026_get_svg()
  function-navigation.php Main Menu 2026 filters (toggles, arrows, accordion classes) + dsa_2026_menu_button()
components-2026/       markup components, loaded with get_template_part()
  header/              logo.php, hamburger.php, navigation.php
  partials/            reusable: button.php
  footer/  page/       (next)
svg-templates/         inline SVGs: svg-arrow.php, svg-chevron-down.php
acf-json/              ACF field groups as JSON (synced with live)
templates-2026/
  page-contact-2026.php "Contact 2026" page template (main.site-main--contact)
src-2026/
  js/
    fonts.js           Fontsource imports (self-hosted fonts)
    main.js            entry: list of eager modules, init on DOM ready
    modules/           one file per component: default-export function, called from main.js
      headerNavigation.js header dropdowns, mobile panel, accordion-js submenus
    utils/ready.js     DOM-ready helper
  scss/
    main.scss          entry: only @use lines (common first, then components)
    common/
      _reset.scss      modern CSS reset (global!)
      _variables.scss  colors, fonts, type scale, no CSS output
      _media.scss      include-media + breakpoints (single source of truth)
      _general.scss    base typography: html, headings, links
      _helper.scss     layout helpers: .content-block, .content-max, .content-narrow, %cover…
    components/        one partial per component (mirrors components-2026/)
      _button.scss     .btn
      _accordion.scss  accordion-js base styles
      _header.scss     header bar, logo, hamburger
      _navigation.scss main nav, dropdowns, mobile panel
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
- **2026 pages are a clean slate: no Bootstrap and no legacy CSS.** The whole page (header, content, footer) has to be built with 2026 markup and styles. Until the templates call `get_footer('new')`, the legacy `footer.php` renders unstyled on 2026 pages.

**Preview on the live site:** create a **private** page in WP admin and pick a 2026 template (currently "Contact 2026"). Only logged-in editors can see it.

**Status (2026-10-05):** the header is built. `footer-new.php` is started: it has markup, but its `components-2026/footer/` parts don't exist yet, and templates still call the legacy `get_footer()`. No public page uses `get_header('new')` yet.

## Layout wrappers (`.content-block` + `.content-max`)

Every new section and component, the header and footer sections included, uses the same two wrappers. They are defined in `src-2026/scss/common/_helper.scss`.

```html
<section class="hero content-block">   <!-- full width: background + side padding -->
	<div class="content-max">           <!-- max-width, centred -->
		…section content…
	</div>
</section>
```

- **Every `<main>` is `<main id="main" class="site-main …" role="main">`,** in every current and future template. `#main` is the target of the "Skip to content" link in `header-new.php`, so never change or remove the id. To target one page, add a modifier class, e.g. `site-main--contact`.
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
- **`.content-narrow`** (max-width 960px, then 800px below `desktop`, centred) is optional. Use it inside `.content-max` for text-heavy content.

## Components (`components-2026/`)

- **One component, two files:**
  - the markup is `components-2026/<area>/<name>.php`;
  - the styles are `src-2026/scss/components/_<name>.scss`, registered in `main.scss`.
  - `<area>` is `header`, `footer`, `page`…, or `partials` for reusable pieces.
- **Load a component with `get_template_part()`.** Pass data through `$args` (WP 5.5+) and read it with `wp_parse_args()`. Don't use globals.
- **Escape everything on output:** `esc_html()`, `esc_attr()`, `esc_url()`.

### Button (`partials/button.php` + `_button.scss`)

Reusable `.btn`: 16px, weight 600, `$color__link` background (`$color__accent-hover` on hover), `$color__main-light` text, 12px 28px padding, 6px radius.

```php
get_template_part('components-2026/partials/button', null, array(
	'link'  => get_field('my_link'),   // ACF Link array (url/title/target), or:
	'url'   => '/contact/', 'label' => 'Contact', 'target' => '',
	'class' => 'my-section__button',   // extra classes
));
```

- It renders nothing without a URL and a label.
- `target="_blank"` gets `rel="noopener"`.

### SVGs (`svg-templates/`)

- One file per icon: `svg-templates/svg-<name>.php`.
- **In templates:** `get_template_part('svg-templates/svg-arrow')`.
- **Where you need a string** (filters, concatenation): `dsa_2026_get_svg('arrow')`.
- **Wrap decorative icons** in `<span aria-hidden="true">`.

### Accordions (accordion-js)

- Use [accordion-js](https://github.com/michu2k/Accordion) (installed with pnpm) with its **default classes**: `.ac` (item), `.ac-trigger` (button), `.ac-panel` (content). The active class is `.is-active`.
- **Base styles** are in `components/_accordion.scss`: only the library's functional rules, without its demo visuals. Style each accordion in its own component.
- **Never put padding on `.ac-panel`.** The library animates its height down to 0, and padding would stay visible. Put the spacing on the panel's children.
- **Import it normally in the component's module.** It's bundled into `main.js`:

  ```js
  // src-2026/js/modules/footerAccordion.js
  import Accordion from 'accordion-js'

  export default function footerAccordion() {
  	const container = document.querySelector('.footer-accordion')

  	if (!container) {
  		return
  	}

  	new Accordion(container, {
  		duration: 500,
  		showMultiple: false,
  	})
  }
  ```
- **`destroy()` opens every item.** If you destroy an accordion (e.g. on a breakpoint change), remove `is-active` and the panels' inline `style` afterwards. `modules/headerNavigation.js` shows how.

## Header (`header-new.php`)

```
header.header[data-2026="header"] > .content-block > .content-max > .header-inner
	.header-logo         components-2026/header/logo.php       (custom logo, 65px high)
	.header-navigation   components-2026/header/hamburger.php + navigation.php
```

- **Bar:** `$color__main` background, a 1px `$color__main-light` bottom border, 14px top and bottom padding (the sides come from `.content-block`). Logo and navigation use `space-between`.
- **Menu:** the **Main Menu 2026** location, up to 2 levels. Links are Manrope 15px, weight 600, `$color__text-light`, with a 32px gap.
  - **Hover and active state** (current page, or the parent of the current page) is an underline, on top-level links and dropdown items alike. There's no background or colour change.
- **Dropdown:** `$color__text-light` background, 16px radius. Items have 10px 12px padding and a 12px radius, stay on one line, and end in a right-aligned arrow (`svg-arrow`).
  - It opens on hover, or by clicking the chevron toggle, which updates `aria-expanded`.
  - Escape, a click outside or focus leaving the item closes it.
  - The parent link is **not** repeated inside the dropdown.
- **Menu markup comes from filters** in `inc/function-navigation.php`, which only touch `main-menu-2026`:
  - the chevron `<button class="submenu-toggle ac-trigger">` after each parent link;
  - the `ac` class on parents and `ac-panel` on `.sub-menu`;
  - the arrow and `.sub-menu__label` inside each dropdown link.
- **Button:** the last item. It's the ACF Link field `menu_button_` (group "Menu Main 2026") on the menu itself: edit it in Appearance > Menus > Main Menu 2026. It's read with `dsa_2026_menu_button()` and rendered with the button partial.
- **Below `tablet` (1140px):**
  - The hamburger opens a full-width panel under the header and adds `body.noscroll`.
  - Submenus become accordion-js accordions. The library is bundled in `main.js`, and the accordion is only created on mobile.
  - When the screen goes back to desktop, the accordion is destroyed and everything is reset to closed.
- **Accessibility:**
  - The skip link (`.skip-link.screen-reader-text`) appears on keyboard focus.
  - Links and buttons show a `:focus-visible` outline. The global helper sets `:focus { outline: 0 }`, so every new component must add its own `:focus-visible` style.

## ACF fields (`acf-json/`)

- ACF saves every field group as JSON in `acf-json/`, which it detects automatically in the theme. Commit these files.
- **The JSON is written on the server** when a group is saved in WP admin. After editing fields on live, download the changed `group_*.json` files into the repo and commit them.
- **Never upload an older `acf-json/` over the server's copy.** Groups showing "Awaiting save" just haven't been written to JSON yet: open the group and save it.
- **Read 2026 fields with `get_field()`,** always behind a `function_exists('get_field')` check in `inc/` code.

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
- **Theme support** (`add_theme_support`) and **2026 nav menu locations** (`register_nav_menus`) go in `dsa_2026_theme_support()` in `function-dev.php`, hooked on `after_setup_theme`.
- **Custom logo is enabled.** Editors set it in Appearance > Customize > Site Identity > Logo, and templates print it with `the_custom_logo()` (or `get_custom_logo()` to get it as a string).
- **2026 menu locations** (legacy menus unchanged in `library/function-setup.php`):

  | Theme location slug | Admin label |
  | --- | --- |
  | `main-menu-2026` | Main Menu 2026 |
  | `footer-service-2026` | Footer Service 2026 |
  | `footer-company-2026` | Footer Company 2026 |
  | `footer-legal-2026` | Footer Legal 2026 |

  Assign menus under Appearance > Menus. Output with `wp_nav_menu( array( 'theme_location' => 'main-menu-2026', 'container' => false ) )` (and the other slugs as needed).

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

- **One module per component:** `src-2026/js/modules/<componentName>.js`, with a **default-export function named after the component** (e.g. `headerNavigation`, `footerAccordion`).
- **The function finds its element and returns early if it isn't there.** A module must never throw on a page that doesn't use it.
- **Libraries are imported normally at the top of the module** (`import Accordion from 'accordion-js'`), so they're bundled into `main.js`. Don't lazy-load with `import()` unless it's a large library used on one page (see "Decisions").
- **`main.js` imports every module and calls each one inside `ready()`:**

  ```js
  import { ready } from './utils/ready'
  import headerNavigation from './modules/headerNavigation'
  import footerAccordion from './modules/footerAccordion'

  ready(() => {
  	headerNavigation()
  	footerAccordion()
  })
  ```
- **Never assign `window.onload`.** Use `ready()` or `addEventListener`. The legacy `dist/bundle.js` assigns it, and only the last assignment wins.
- **jQuery is optional.** If you need it, `import $ from 'jquery'` gives you the global copy.

## Deploying to the live server

1. Run `pnpm build:2026` and commit the result.
2. Upload `inc/`, `components-2026/`, `svg-templates/`, `dist-2026/`, `templates-2026/`, `acf-json/` and `header-new.php` **first**.
3. Upload `functions.php` **last**. Its `require` of `inc/function-dev.php` is a fatal error if `inc/` isn't on the server yet.
4. Never upload `node_modules/` or `src-2026/`. The server only needs the built `dist-2026/`.

## Decisions

Choices that were weighed and settled. Don't reopen them without a new reason.

### 2026-10-05: JS libraries are bundled with normal imports, not lazy-loaded chunks

**Context:** accordion-js (mobile header submenus) was first lazy-loaded with `import()`, which made webpack write it to a separate file, `dist-2026/chunks/accordion.<hash>.js`, fetched by `main.js` only on mobile. Andrea's usual approach is a normal `import` in the component module, bundled into `main.js`.

**Lazy chunk (`import()`)**
- **Pro:** desktop visitors never download the library.
- **Con:** an extra file to deploy, and if `chunks/` is missing on the server the mobile submenus silently stop working.
- **Con:** the chunk is found relative to `main.js` at runtime. WordPress optimisation plugins that combine or move JS (WP Rocket, Autoptimize…) can break that path.
- **Con:** asynchronous code needs a promise cache and race guards, which makes it harder to read and maintain.
- **Con:** an extra request on mobile before the accordion works.

**Normal import (bundled in `main.js`)**
- **Pro:** one file and nothing extra to deploy.
- **Pro:** robust under optimisation plugins, and the library is available immediately.
- **Pro:** simpler code, and the pattern Andrea already uses.
- **Con:** every visitor downloads the library.

**Measured:**

| | Desktop downloads | Mobile downloads |
| --- | --- | --- |
| Before (lazy chunk) | `main.js` 2,353 B gzip | 4,368 B gzip in 2 files |
| After (normal import) | `main.js` 2,695 B gzip, **+0.3 KB** | 2,695 B gzip in 1 file |

Removing the chunk also removes webpack's chunk-loading code, which is why mobile ends up smaller than before.

**Decision:** normal imports by default, with Andrea's module pattern: a default-export function per component, called from `main.js` (see "JS conventions").

**Revisit only for** a large library (roughly 20 KB+ gzipped) needed on a single page, e.g. a map or a heavy animation library. Lazy-load just that one with `import()`, then deploy the `dist-2026/chunks/` folder it creates.

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
- **2026-10-05**
  - Registered four 2026 nav menu locations in `dsa_2026_theme_support()`: Main Menu 2026, Footer Service/Company/Legal 2026.
- **2026-10-05**
  - Built the 2026 header:
    - `components-2026/header/` (logo, hamburger, navigation);
    - `inc/function-navigation.php` (menu filters + ACF `menu_button_`);
    - `inc/function-helpers.php` (`dsa_2026_get_svg`).
  - Reusable button component: `components-2026/partials/button.php` + `_button.scss`.
  - Mobile panel below `tablet` with accordion-js submenus, lazy-loaded (`_accordion.scss`, `modules/header.js`). The example JS module was removed.
  - Variables:
    - `$color-text-light` renamed `$color__text-light`, to match the `$color__` naming;
    - the dropdown background is `$color__text-light`;
    - hover and active are an underline.
  - Added `.screen-reader-text` and the skip-link focus style to `_helper.scss`.
  - `page-2026.php` was removed by Andrea. The preview now uses "Contact 2026".
- **2026-10-05**
  - Every `<main>` keeps `id="main"` (the skip-link target). The Contact template now uses `.site-main--contact` instead of `id="contact"`.
  - Documented `dist-2026/chunks/` (the lazy-loaded accordion-js library).
- **2026-10-05**
  - accordion-js is now a normal import, bundled into `main.js`, and `dist-2026/chunks/` is gone. See "Decisions".
  - JS modules follow Andrea's pattern: `modules/header.js` became `modules/headerNavigation.js` with `export default function headerNavigation()`, called from `main.js`.
