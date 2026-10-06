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
footer-new.php         2026 footer (loaded by get_footer('new')): markup only, parts in components-2026/footer/
inc/                   2026 PHP
  function-dev.php        entry: requires the other inc files + theme support (custom logo, 2026 nav menus)
  function-assets.php     fonts/CSS/JS on 2026 pages, dequeues legacy assets there
  function-helpers.php    dsa_2026_get_svg()
  function-navigation.php Main Menu 2026 filters (toggles, arrows, accordion classes) + dsa_2026_menu_button()
  function-acf.php        ACF options pages (Options > Footer) + dsa_2026_option()
  function-layout.php     Dynamic Layout 2026: no editor, A–Z "Add section" menu, dsa_2026_render_sections()
components-2026/       markup components, loaded with get_template_part()
  header/              logo.php, hamburger.php, navigation.php
  footer/              partner.php, logo.php, info.php, social.php, navigation.php, contact.php, bottom.php
  partials/            reusable: button.php
  sections/            Dynamic Layout 2026 sections, one file per flexible layout: hero-page.php, two-column-image-text.php, wysiwyg-editor.php, introduction.php
  page/                (next)
svg-templates/         inline SVGs: arrow, chevron-down, linkedin, x, youtube, map-pin, mail, phone
acf-json/              ACF field groups as JSON (synced with live)
templates-2026/
  page-contact-2026.php "Contact 2026" page template (main.site-main--contact)
  page-layout-2026.php  "Dynamic Layout 2026" page template (main.site-main--layout)
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
      _footer.scss     certification strip, footer columns, bottom bar
      _hero-page.scss  Dynamic Layout section: hero page (breadcrumb, title, accent lines)
      _two-column-image-text.scss  Dynamic Layout section: image + content, reverse, background colour
      _wysiwyg-editor.scss  Dynamic Layout section: editor content (h4 bar, dot lists, 12/30px rhythm)
      _introduction.scss  Dynamic Layout section: H2 left, text + two buttons right, background/text colour
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
- **2026 pages are a clean slate: no Bootstrap and no legacy CSS.** The whole page (header, content, footer) has to be built with 2026 markup and styles. 2026 templates end with **`get_footer('new')`**. One that still calls `get_footer()` gets the legacy `footer.php`, unstyled.

**Preview on the live site:** create a **private** page in WP admin and pick a 2026 template ("Contact 2026" or "Dynamic Layout 2026"). Only logged-in editors can see it.

**Status (2026-10-05):** the header and footer are built, and "Contact 2026" uses both. The "Dynamic Layout 2026" template and its `page_sections` field exist, with four sections so far: Hero page, Two-column image text, WYSIWYG editor and Introduction. No public page uses `get_header('new')` yet.

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
  - `<area>` is `header`, `footer`, `page`…, `sections` for the Dynamic Layout builder, or `partials` for reusable pieces.
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
- **Outline variant:** pass `'class' => 'btn--outline'`.
  - It's transparent, with a 1px `currentColor` border, and the text inherits the colour. On a dark section with light text, it turns light by itself.
  - Its padding is 11px 27px, so it's the same size as the filled `.btn`.
  - **Hover and focus:** it fills with `$color__link`, with `$color__main-light` text (Andrea's spec). The focus outline is `$color__link`.

### SVGs (`svg-templates/`)

- One file per icon: `svg-templates/svg-<name>.php`.
- **In templates:** `get_template_part('svg-templates/svg-arrow')`.
- **Where you need a string** (filters, concatenation): `dsa_2026_get_svg('arrow')`.
- **Wrap decorative icons** in `<span aria-hidden="true">`.
- **A part that changes colour on hover** gets `fill="currentColor"` (or `stroke`), so CSS sets its colour, as in the footer's social icons.
- **Remove Figma's `clipPath` wrappers when they clip nothing.** Their IDs repeat when an icon appears twice on a page.

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
  - **Top-level hover and active state** (current page, or the parent of the current page) is an underline, with no background or colour change.
- **Dropdown:** `$color__text-light` background, 16px radius. Items are Manrope 14px, weight 600, `$color__main-light`, with 10px 12px padding and a 12px radius. They stay on one line and end in a right-aligned arrow (`svg-arrow`).
  - **Item hover and active state** (current page): the whole anchor gets the `$color__dropdown-hover` (`#C9DB001A`) background. The text stays `$color__main-light`, with no underline. **This colour is Andrea's spec, so don't remove it.** On mobile the items stay `$color__text-light` on the dark panel.
  - **Use only the colours in `_variables.scss`.** Don't add new ones (tints, alphas…) unless Andrea gives the value. `$color__dropdown-hover` is one he gave.
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

## Footer (`footer-new.php`)

```
section.footer-partners.content-block > .content-max     components-2026/footer/partner.php (above the footer)
footer.dark-footer > .content-block > .content-max
	.footer-inner
		.footer-logo         logo.php (custom logo, 81px high) + info.php + social.php
		.footer-navigation   navigation.php (Services, Company) + contact.php (Get in Touch)
	.footer-bottom           bottom.php (copyright + Footer Legal 2026 menu)
```

- **Content:** everything except the menus and logo comes from **Options > Footer**, the ACF group "Footer 2026" (`acf-json/group_6ac3c858bc43e.json`). Partials read it with `dsa_2026_option()`, and each one renders nothing while its fields are empty.

  | Tab | Field | Name | Type |
  | --- | --- | --- | --- |
  | Certifications | Certifications | `footer_certifications` | repeater of `image` (image ID) |
  | Info | Footer info | `footer_info` | WYSIWYG |
  | Social | LinkedIn, X, YouTube | `footer_linkedin`, `footer_x`, `footer_youtube` | link |
  | Contact | Map, Email, Phone number | `footer_map`, `footer_email`, `footer_phone` | link |

  - **Contact links:** the link text is what's shown. The URLs are a Maps URL, `mailto:…` and `tel:+44…`.
  - **Social links:** they show only an icon, with the network name as the `aria-label`. The icon is a 36px SVG whose circle is `currentColor`: `$color__text-light`, and `$color__link` on hover.
- **Menus:** the Footer Service 2026 and Footer Company 2026 locations become the "Services" and "Company" columns. The column titles are in `navigation.php`. A column is skipped while its location has no menu.
- **Copyright:** hard-coded in `bottom.php`, with the year from `wp_date('Y')`.
- **Styles:**
  - The certification strip is `$color__main-light`, with logos 100px high (48px below `phone`).
  - The footer is `$color__main`. All its text is `$color__text-light` at full opacity, in Manrope 400. Links turn `$color__link` on hover.
  - The column text and links are 14px. The bottom bar (copyright + legal menu) is 13px.
  - The logo is 81px high.
  - Columns are 1fr / 2fr, and the menus are a 3-column grid.
  - Below `tablet` it becomes one column, with 2 menu columns below `phone-land` and 1 below `phone`.
  - The bottom bar stacks below `phone-land`.

## Dynamic Layout 2026 (`templates-2026/page-layout-2026.php`)

Editors build these pages themselves: a list of sections, top to bottom, picked from an ACF flexible content field. The pages have no editor.

```
main#main.site-main.site-main--layout
	section.<name>.content-block > .content-max    components-2026/sections/<name>.php (one per row)
	…
```

- **Template:** "Dynamic Layout 2026". Inside the loop, `<main>` only calls `dsa_2026_render_sections()`. It never adds wrappers: each section brings its own.
- **Field:**
  - **Group:** the ACF group "Dynamic Layout 2026" (`acf-json/group_6ac4033ed38b9.json`), shown when Page Template is `templates-2026/page-layout-2026.php`.
  - **The field:** one flexible content field, `page_sections` ("Page sections", button "Add section").
  - **Layouts:** every section is a layout of this field. Its sub fields are defined **directly in the layout**, with no clone groups (see "Decisions").
  - **"Add section" menu:** always in alphabetical order by label.
    - `dsa_2026_sort_section_layouts()` (`acf/load_field/name=page_sections`) sorts the layouts, so a new layout doesn't need to be dragged into place.
    - It only changes the menu. Saved rows keep the order the editor gave them.
- **No editor:** `inc/function-layout.php` changes the edit screen of pages on this template only. `dsa_2026_is_layout_page()` is the only place the template path is written.
  - **Block editor:** turned off with the `use_block_editor_for_post` filter.
  - **Classic content box:** removed with `remove_post_type_support()` on `load-post.php`.
  - **The editor only goes away after a reload.** Create the page, pick the template, save the draft, then reload.
  - The old `post_content` stays in the database and isn't shown. It comes back if the page switches to another template.

### How the loop works (`dsa_2026_render_sections()`)

1. A password-protected page shows only the password form, as `the_content()` would.
2. `get_field('page_sections')` returns every row. With no rows, or with ACF inactive, nothing is printed.
3. Each row loads one component. The file name is the layout name with `_` replaced by `-`:

   | Layout name | Component | SCSS | Section class |
   | --- | --- | --- | --- |
   | `hero_page` | `components-2026/sections/hero-page.php` | `_hero-page.scss` | `.hero-page` |
   | `two_columns` | `components-2026/sections/two-columns.php` | `_two-columns.scss` | `.two-columns` |

4. The component gets the row as `$args`: every sub field by name, plus `acf_fc_layout` and `index`. `index` is the row's position, `0` for the first section. **Never name a sub field `index` or `acf_fc_layout`.**
5. A layout without a component file prints nothing. Logged-in editors see `<!-- dsa-2026: no component for section "x" -->` in the page source.

### Adding a section

1. **ACF:** add a layout to `page_sections`, with a snake_case name (`two_columns`) and its sub fields. Then sync the JSON (see "ACF fields").
   - **If the section has settings, split its fields into two tabs: Options first, then Content** (e.g. Two-column image text).
2. **Markup:** create `components-2026/sections/<name>.php`, with hyphens in the name. Read `$args` with `wp_parse_args()` defaults, escape everything on output, and wrap the section itself:

   ```php
   <?php
   /**
    * Section: two columns (layout two_columns in Dynamic Layout 2026).
    *
    * @package FDRY
    */

   $args = wp_parse_args($args, array(
   	'title' => '',
   	'left'  => '',
   	'right' => '',
   	'index' => 0,
   ));
   ?>
   <section class="two-columns content-block">
   	<div class="content-max">
   		…
   	</div>
   </section>
   ```
3. **Styles:** create `src-2026/scss/components/_<name>.scss` and add its `@use` to `main.scss`.
4. **JS:** only if the section needs it. Create `src-2026/js/modules/<sectionName>.js` and call it from `main.js` (see "JS conventions").
5. **Headings:** every page needs exactly one `<h1>`. The Hero page section prints it by default, and every other section starts at `<h2>`.

**Reuse outside the builder:** a section is a normal component. Any template can render it by passing an array of the same shape, e.g. from an ACF Group field: `get_template_part('components-2026/sections/hero-page', null, get_field('hero'))`. No extra render helper is needed.

### Hero page (`sections/hero-page.php` + `_hero-page.scss`)

Layout `hero_page` ("Hero page"). Figma: file `ZaphlvDdgp3I9EhmdhDnFe`, node `223:2966` (1440 × 237).

```
section.hero-page.content-block
	img.hero-page__image                  (background, only when set)
	.content-max
		nav.hero-page__breadcrumb > ol.hero-page__crumbs > li.hero-page__crumb…
		h1|h2|h3.hero-page__title.h1
		span.hero-page__lines           (the two accent lines, ::before + ::after)
```

| Label | Name | Type | Fallback |
| --- | --- | --- | --- |
| Breadcrumb | `breadcrumb` | repeater of `link` (Link) | none: only Home |
| Breadcrumb actual page | `breadcrumb_actual_page` | text | the page title |
| Hero title (50%) | `title` | text | the page title |
| H tag (50%) | `title_tag` | select `h1` / `h2` / `h3` | `h1` |
| Background image | `image` | image (ID) | `$color__main-light` background |

- **Breadcrumb:** Home (`home_url('/')`) is **always** first, then the repeater links in order (a row without a URL or text is skipped), then the current page.
  - The current page is the "actual page" text, or the page title. It's never a link, and it carries `aria-current="page"`.
  - The `/` separators are `aria-hidden`.
- **Title:**
  - **Highlighting:** wrap words in `<span class="accent">…</span>` to colour them `$color__link`. The title goes through `wp_kses()`, which keeps only `<span class>`.
  - **H tag:** it changes only the tag, for SEO. The `.h1` class gives h1, h2 and h3 the same look.
- **Image:**
  - It's printed with `wp_get_attachment_image()` (`alt=""`, it's decorative) and covers the whole section (`object-fit: cover`).
  - **First section:** `loading="eager"` + `fetchpriority="high"`, because it's above the fold. Lower down it's lazy-loaded.
- **Styles:**
  - **Breadcrumb:** Manrope 13px, `line-height: 1`, `$color__text-light`.
    - The links are weight 500 and turn `$color__link` on hover.
    - The current page is weight 600.
    - The `/` is weight 400.
    - There are 7px gaps around each `/`.
  - **Title:** Titillium Web (`$header__fontname`), weight 400, `line-height: 1.05`, `$color__text-light`, max-width 670px.
    - Its size follows the global h1 scale: 54px, ×0.9 below `desktop`, ×0.75 below `phone-land`.
  - **Lines:** 10px under the title, 4px high, 2px radius, a 7px gap.
    - The first is 48px wide, `$color__link`.
    - The second is 15px wide, `$color__accent-hover`. Figma has 14px; Andrea's spec says 15px.
  - **Spacing:** `padding-block` only, because the sides come from `.content-block`. At 1440px with a one-line title, the hero is 237px high, as in Figma.

    | Width | Padding top / bottom | Breadcrumb → title |
    | --- | --- | --- |
    | Base (desktop) | 60px / 62px | 31px |
    | `<tablet` | 48px / 48px | 24px |
    | `<phone` | 40px / 40px | 20px |

    The `tablet` and `phone` values aren't in Figma.

### Two-column image text (`sections/two-column-image-text.php` + `_two-column-image-text.scss`)

Layout `two_column_image_text` ("Two-column image text"). Figma: file `ZaphlvDdgp3I9EhmdhDnFe`, both frames 1440 × 694:
- node `223:3270`: the default order, `#091C1E`, no button;
- node `223:3278`: reversed, `#122C29`, with a button.

```
section.two-column-image-text.content-block[.two-column-image-text--reverse][.two-column-image-text--dark]   (style="background-color: …")
	.content-max > .two-column-image-text__inner                  (grid 1fr 1fr)
		.two-column-image-text__media > img.two-column-image-text__img
		.two-column-image-text__content
			p.__eyebrow, h2.__title, div.__text, a.btn.__button  (each only when filled)
```

| Tab | Label | Name | Type | Notes |
| --- | --- | --- | --- | --- |
| Options | Background color (50%) | `background_color` | color picker | default `#091C1E` |
| Options | Text color (50%) | `text_color` | select `light` / `dark` | default `light` |
| Options | Image layout grid reverse (50%) | `grid_reverse` | true/false | off: image, content. On: content, image |
| Content | Eyebrow | `eyebrow` | text | optional |
| Content | Content title | `title` | textarea (new lines → `<br>`) | always an `<h2>` |
| Content | Content | `content` | WYSIWYG (basic, no media) | |
| Content | Button | `link` | link | optional, rendered with `partials/button.php` |
| Content | Image | `image` | image (ID) | **required** |

- **Background:**
  - **Valid colour:** it's printed as an inline `style`, after `sanitize_hex_color()`.
  - **Empty or invalid colour:** the SCSS default `$color__main` applies.
- **Text color:** pair it with the background.
  - **Light** (default): `$color__text-light`, for dark backgrounds.
  - **Dark:** adds `.two-column-image-text--dark`, which sets `$color__text`, for light backgrounds.
  - The colour is set on the section, and the title and text inherit it.
  - The accent span, eyebrow, WYSIWYG links and button keep their own colours.
- **Reverse:** it only changes the visual order (CSS `order`). The DOM is always image, then content. In one column (below `phone-land`) the image is always on top.
- **Title:**
  - It goes through `wp_kses()`, which keeps only `<span class>` and `<br>`.
  - **Highlighting:** wrap words in `<span class="accent">` to colour them `$color__link`.
  - **Base h2:** its size, weight, line-height and font come from the base `h2` in `_general.scss` (Titillium 400, 44px / 1.2, with the h2 scale). Don't redeclare them in the section.
- **Text:** `wp_kses_post()`. In WYSIWYG lists, the padding is put back (`1.25em`). Links are `$color__link` and underlined.
- **Image:**
  - `wp_get_attachment_image()` at size `large`, with `sizes` `(max-width: 920px) 100vw, 50vw` and the alt text from the media library.
  - Eager-loaded only when it's the first section.
- **Styles:**
  - **Eyebrow:** Titillium Web 600, 14px, `line-height: 1.5`, uppercase, `$color__link`.
  - **Text:** Manrope 16px 400, `line-height: 1.6`, `$color__text-light`. Paragraphs have no gap, as in Figma.
  - **Button:** `.btn` as it is. It already matches Figma.
  - **Image:** `aspect-ratio: 556 / 542` (Figma), `object-fit: cover`, 12px radius on every image.
  - **Grid:** content vertically centred (`align-items: center`).
  - **Column spacing:** eyebrow → title 18px, then 24px before the text and 24px before the button. These are margins, so a missing element leaves no gap.
  - **Padding and gap:** `padding-block` only. At 1440px the section is 694px high, as in Figma.

    | Width | Padding top / bottom | Grid |
    | --- | --- | --- |
    | Base (desktop) | 76px | 2 columns, gap 88px |
    | `<desktop` | 76px | gap 64px |
    | `<tablet` | 64px | gap 48px |
    | `<phone-land` | 64px | 1 column, image on top, gap 40px |
    | `<phone` | 35px | gap 32px |

    Only the desktop values are in Figma.

### WYSIWYG editor (`sections/wysiwyg-editor.php` + `_wysiwyg-editor.scss`)

Layout `wysiwyg_editor` ("WYSIWYG editor"): free text from the editor. Figma: file `ZaphlvDdgp3I9EhmdhDnFe`, node `223:3103` (the h4 with its bar, a paragraph, a dot list).

```
section.wysiwyg-editor.content-block
	.content-max
		.wysiwyg-editor__content[.content-narrow]   (the editor HTML)
```

| Tab | Label | Name | Type | Notes |
| --- | --- | --- | --- | --- |
| Options | Container (50%) | `container` | select `default` / `narrow` | default `default` |
| Content | Content | `content` | WYSIWYG (full toolbar, no media) | |

- **Container:**
  - `default` is the `.content-max` width.
  - `narrow` adds `.content-narrow` to the content div, inside `.content-max`.
- **Empty content:** the section prints nothing.
- **Output:** `wp_kses_post()`.
  - It keeps the editor's `style="text-align: …"`. That's how editors centre text, so there's no centring option.
- **Full toolbar:** the `basic` toolbar has no Paragraph/Heading dropdown.
- **Headings:** start at H2 (the field instructions say so).
  - Sizes, weight and line-height come from the global `h2`…`h6` in `_general.scss`. The section doesn't redeclare them.
  - **Figma's "H3" text style (26px) is our h4.** Figma's heading names are one level off; the global scale is the reference.
- **Styles:**
  - **Section:** `$color__main` background, `$color__text-light` text.
  - **Text:** Manrope 16px 400, `line-height: 1.6`.
  - **h4:** an accent bar before the text: 4 × 28px, 2px radius, `$color__link`, 12px before the text.
    - It's an inline-block `::before`, centred on the first line. It isn't flex, so inline tags (`<strong>`, links) stay in the line and `text-align` still works.
  - **Lists:**
    - **`ul`:** no bullets or padding. Each `li` has a 14px `$color__link` dot (`::before`, centred on the first line) and 10px before the text, so wrapped lines hang.
    - **`ol`:** keeps its numbers, with `padding-left: 1.25em`.
    - **Items:** the same text as `p`: they set no font rules and inherit Manrope 16px 400, `line-height: 1.6`. Figma's 15px 500 was overruled by Andrea.
  - **Links:** `$color__link`, underlined.
  - **Rhythm:** every gap is a `margin-top` between the content's direct children.

    | Before → after | Gap |
    | --- | --- |
    | anything → p, list (p+p, h+p, p/h+ul, ul+p) | 12px |
    | anything → heading (h+h, p+h, ul+h) | 30px |
    | li → li, li → nested list | 12px |

  - **Padding:** `padding-block` only.

    | Width | Padding top / bottom |
    | --- | --- |
    | Base (desktop) | 76px |
    | `<tablet` | 64px |
    | `<phone` | 35px |

    The padding isn't in Figma. It matches Two-column image text.

### Introduction (`sections/introduction.php` + `_introduction.scss`)

Layout `introduction` ("Introduction"): an H2 on the left, with text and up to two buttons on the right. Figma: file `ZaphlvDdgp3I9EhmdhDnFe`, node `223:3244` ("Methods introduction", 1440 wide).

```
section.introduction.content-block[.introduction--light]   (style="background-color: …")
	.content-max > .introduction__inner                      (grid 480px | 1fr)
		h2.introduction__title
		.introduction__summary
			div.introduction__text                           (only when filled)
			.introduction__buttons > a.btn + a.btn.btn--outline  (each only when set)
```

| Tab | Label | Name | Type | Notes |
| --- | --- | --- | --- | --- |
| Options | Background color (50%) | `background_color` | color picker | default `#F3F3F1` |
| Options | Text color (50%) | `text_color` | select `dark` / `light` | default `dark` |
| Content | Title | `title` | textarea (new lines → `<br>`) | **required**, always an `<h2>` |
| Content | Content | `content` | WYSIWYG (basic, no media) | |
| Content | Primary button (50%) | `button_primary` | link | optional, filled `.btn` |
| Content | Secondary button (50%) | `button_secondary` | link | optional, `.btn--outline` |

- **Background:** like Two-column image text. A valid colour is an inline `style` (`sanitize_hex_color()`). An empty or invalid one falls back to the SCSS default, `$color__text-light`.
- **Text color:**
  - **Dark** (default) is `$color__text`, for light backgrounds.
  - **Light** adds `.introduction--light`, which sets `$color__text-light`, for dark backgrounds.
  - The title, the text and the outline button inherit it. The accent, the links and the filled button keep their own colours.
- **Title:**
  - It's required, because it holds the left column. Without it, the section prints nothing.
  - `wp_kses()` keeps only `<span class>` and `<br>`, and `<span class="accent">` highlights words in `$color__link`.
  - Its look comes from the base `h2`: don't redeclare it.
- **Text:** `wp_kses_post()`, Manrope 16px 400, `line-height: 1.6`, max-width 580px (Figma).
  - Paragraphs have no gap, as in Figma.
  - Links are `$color__link` and underlined. Lists get their padding back.
- **Buttons:** both use `partials/button.php`, and the secondary one gets `btn--outline`.
  - The row is `flex-wrap` with a 12px gap, 24px under the text.
  - It's only printed when at least one button has a URL.
- **Height:** Figma draws the buttons 46px high, but the shared `.btn` is 44px. It's left as it is, so at 1440px the section is 368px high (Figma ~372).
- **Grid and padding:** `padding-block` only. The title column is top-aligned.

  | Width | Padding top / bottom | Grid |
  | --- | --- | --- |
  | Base (desktop) | 86px (Figma) | `480px 1fr`, gap 64px (Figma) |
  | `<tablet` | 64px | `1fr 1fr`, gap 48px |
  | `<phone-land` | 64px | 1 column, gap 24px |
  | `<phone` | 35px | 1 column, gap 24px |

## ACF fields (`acf-json/`)

- ACF saves every field group as JSON in `acf-json/`, which it detects automatically in the theme. Commit these files.
- **The JSON is written on the server** when a group is saved in WP admin. After editing fields on live, download the changed `group_*.json` files into the repo and commit them.
- **Never upload an older `acf-json/` over the server's copy.** Groups showing "Awaiting save" just haven't been written to JSON yet: open the group and save it.
- **Read 2026 fields with `get_field()`,** always behind a `function_exists('get_field')` check in `inc/` code.
  - The Dynamic Layout builder reads the whole flexible field once and hands each row to its section as `$args`. Sections never call `get_sub_field()` (see "Dynamic Layout 2026").
- **Options pages:**
  - Register them in `inc/function-acf.php` on `acf/init`, as children of the legacy "Options" page (`parent_slug` `acf-options`).
  - **Read options with `dsa_2026_option('name')`.**
  - **Prefix option field names** (`footer_…`): every options page shares one namespace with the legacy Options fields (`cert_1`, `logos`…).
  - **Keep `dsa_2026_options_parent_no_redirect()`.** Without it, ACF turns "Options" into a link to its first child, and the legacy Options fields can't be edited.

## Fonts

Self-hosted from npm (Fontsource) and bundled by webpack. No requests to Google.

| Family | Weights | SCSS variable | Used for |
| --- | --- | --- | --- |
| Manrope | 400, 500, 600, 700 | `$text__fontname` | body text |
| Titillium Web | 400, 600, 700 | `$header__fontname` | headings (600: section eyebrows) |

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
2. Upload `inc/`, `components-2026/`, `svg-templates/`, `dist-2026/`, `templates-2026/`, `header-new.php` and `footer-new.php` **first**.
   - For `acf-json/`, upload only the **new or changed** `group_*.json` files, then sync them under Custom Fields > Field Groups.
3. Upload `functions.php` **last**. Its `require` of `inc/function-dev.php` is a fatal error if `inc/` isn't on the server yet.
4. Never upload `node_modules/` or `src-2026/`. The server only needs the built `dist-2026/`.

## Decisions

Choices that were weighed and settled. Don't reopen them without a new reason.

### 2026-10-05: Dynamic Layout sections use one flexible field, `get_template_part()` + `$args`, and fields in the layout

**Context:** this is Andrea's pre-Gutenberg "Template Block" pattern, updated:
- `get_field('blocks')` + `foreach`;
- the layout `block-hero` is stripped to `hero.php`;
- `file_exists()` + `include` through a `render_theme_block()` helper;
- each layout holds one clone field (`block-<name>`) of a per-block field group.

**Kept:**
- `get_field()` + `foreach`;
- the layout name maps to a file;
- the row's fields are the component's data;
- sections can be reused on other templates.

**Changed:**
- `get_template_part()` replaces `file_exists()` + `include` + `render_theme_block()`. It finds the file, returns `false` when it's missing, runs in its own scope, and it's how every 2026 component is loaded.
- `$args` with `wp_parse_args()` defaults replaces `$fields`, so empty fields don't raise undefined-index warnings.
- There's no `block-` prefix: the layout `two_columns` loads `two-columns.php`.
- There are guards for:
  - no rows (on PHP 8, `foreach` over `null` warns);
  - ACF being inactive;
  - password-protected pages.

**Fields defined in the layout, not in per-section clone groups:**

| | Pro | Con |
| --- | --- | --- |
| Clone (per-section group) | Fields defined once and reusable anywhere; one JSON file per section | Two things to set up per section; a list of inactive groups in admin |
| In the layout | One group, one place, the simplest option | Another field group can't reuse a section's fields |

**Decision:** fields go in the layout. Sections are built for the builder, so the simpler option wins.

**Revisit only if** another field group (another template or an options page) needs a section's fields.
- Move just that section's fields into their own group, and clone it into the layout as **seamless**. `$args` keeps the same shape, so the component doesn't change.
- Moving fields can affect content already saved on live pages, so check the data first.

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
- **2026-10-05**
  - Built the 2026 footer:
    - `components-2026/footer/` (certification strip, logo, info, social, menus, contact, bottom bar);
    - `_footer.scss`;
    - six new icons in `svg-templates/`.
  - Added `inc/function-acf.php`:
    - the **Options > Footer** sub-page;
    - `dsa_2026_option()`;
    - the no-redirect filter that keeps the legacy Options page editable.
  - New ACF group "Footer 2026" (`group_6ac3c858bc43e.json`).
  - "Contact 2026" now calls `get_footer('new')`.
  - Footer icons replaced with Andrea's SVGs: the social circles are `currentColor` (teal on hover) and the no-op `clipPath` wrappers were removed.
- **2026-10-05**
  - Dropdown items: Manrope 14px, weight 500, `$color__main-light`.
  - On hover and on the current page, an item now gets the `$color__dropdown-hover` (`#C9DB001A`) background instead of an underline. Top-level links keep the underline.
- **2026-10-05**
  - Dropdown item hover and active state: the text is now `$color__main`, with no background. `$color__dropdown-hover` was removed, because it wasn't a project colour.
  - Footer: the `rgba()` borders are gone. The bottom divider is `$color__main-light`, like the header border, and the certification strip has no borders.
- **2026-10-05**
  - Restored the dropdown item hover/active background `$color__dropdown-hover` (`#C9DB001A`) at Andrea's explicit request. It had been removed in the previous entry. The text stays `$color__main-light`.
- **2026-10-05**
  - Dropdown items are now weight 600 (was 500).
- **2026-10-05**
  - Added the Dynamic Layout 2026 template (`templates-2026/page-layout-2026.php`):
    - the editor is removed on its pages;
    - a new ACF group "Dynamic Layout 2026" (`group_6ac4033ed38b9.json`) holds the flexible field `page_sections`, with no layouts yet;
    - `dsa_2026_render_sections()` in the new `inc/function-layout.php` renders `components-2026/sections/`.
  - See "Dynamic Layout 2026" and "Decisions".
- **2026-10-05**
  - First Dynamic Layout section, **Hero page**: layout `hero_page`, `components-2026/sections/hero-page.php` and `_hero-page.scss`.
    - It has a breadcrumb (Home, the repeater links, then the current page), a title with an H-tag select and `<span class="accent">` highlighting, two accent lines, and an optional background image.
    - Spacing comes from Figma node `223:2966`.
  - `main.scss` has a new "Sections" group for section partials.
- **2026-10-05**
  - Second Dynamic Layout section, **Two-column image text**: layout `two_column_image_text`, `components-2026/sections/two-column-image-text.php` and `_two-column-image-text.scss`.
    - The **Options** tab has the background colour and the reverse switch. The **Content** tab has the eyebrow, H2 title, WYSIWYG, button and image.
    - Figma nodes `223:3270` and `223:3278`.
    - Andrea chose a 12px radius on every image and 76px padding (as in Figma).
  - The Options/Content tabs are now the convention for sections with settings (see "Adding a section").
  - The base `h2, .h2` in `_general.scss` now has `font-weight: 400; line-height: 1.2` (Figma "H2" style). The footer column titles and the hero title keep their own values.
  - Added Titillium Web 600 (Latin and Latin-extended), for section eyebrows.
- **2026-10-05**
  - Third Dynamic Layout section, **WYSIWYG editor**: layout `wysiwyg_editor`, `components-2026/sections/wysiwyg-editor.php` and `_wysiwyg-editor.scss`.
    - The **Options** tab has the container (default or narrow). The **Content** tab has one WYSIWYG.
    - The styles come from Figma node `223:3103`: the h4 accent bar, dot lists, and a 12px / 30px rhythm.
  - Figma's "H3" text style (26px) is our h4: Figma's heading names are one level off, so the global type scale stays and is the reference.
  - The global `h3`…`h6` (and `.h3`…`.h6`) in `_general.scss` now have `font-weight: 400; line-height: 1.3` (Figma heading style). Sizes are unchanged.
  - Phone padding (`<phone`) is now 35px instead of 48px: Two-column image text, and the dark footer's top (`35px 0 32px`).
- **2026-10-06**
  - Two-column image text: a new **Text color** option (`text_color`, Options tab, next to Background color).
    - Light is the default. Dark adds `--dark`, which sets `$color__text` (`#122c29`) on the section.
    - The accent, eyebrow, links and button are unchanged.
  - `__title` and `__text` no longer set their own colour. They inherit it from the section.
- **2026-10-06**
  - WYSIWYG editor: list items now match the body text, at Andrea's request. The 15px / 500 / 1.5 from Figma was removed, so they inherit 16px / 400 / 1.6 like `p`. The dot is re-centred on the 25.6px line.
- **2026-10-06**
  - Fourth Dynamic Layout section, **Introduction**: layout `introduction`, `components-2026/sections/introduction.php` and `_introduction.scss`. Figma node `223:3244`.
    - **Options tab:** background colour (default `#F3F3F1`) and text colour (default dark).
    - **Content tab:** a required H2 title, a WYSIWYG, and primary and secondary buttons.
    - The fields were proposed in a plan and approved by Andrea.
  - New `.btn--outline` button variant in `_button.scss`: a `currentColor` border and the same size as `.btn`. On hover it fills with `$color__link` (Andrea's spec).
- **2026-10-06**
  - The "Add section" menu of `page_sections` is now alphabetical, by label. `dsa_2026_sort_section_layouts()` in `inc/function-layout.php` sorts it on `acf/load_field`.
