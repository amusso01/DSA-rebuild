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
  function-helpers.php    dsa_2026_get_svg(), dsa_2026_password_gate()
  function-navigation.php Main Menu 2026 filters (toggles, arrows, accordion classes) + dsa_2026_menu_button()
  function-acf.php        ACF options pages (Options > Footer) + dsa_2026_option(), dsa_2026_field()
  function-layout.php     Dynamic Layout 2026: A–Z "Add section" menu, dsa_2026_render_sections(), dsa_2026_section_padding_style(); no editor on Layout + Contact 2026 pages
  function-blog.php       blog posts query + cards (dsa_2026_blog_query/cards) and the Load more REST route
  function-contact-form.php Contact Form 7 on 2026 pages: ACF form picker, Multi Step plugin CSS off, no autop
components-2026/       markup components, loaded with get_template_part()
  header/              logo.php, hamburger.php, navigation.php
  footer/              partner.php, logo.php, info.php, social.php, navigation.php, contact.php, bottom.php
  partials/            reusable: button.php, blog-card.php
  sections/            Dynamic Layout 2026 sections, one file per flexible layout: hero-page.php, two-column-image-text.php, wysiwyg-editor.php, introduction.php, blog-row.php, service-cards.php
  page/                components of fixed page templates: get-in-touch.php, contact-form.php (Contact 2026)
svg-templates/         inline SVGs: arrow, arrow-right, chevron-down, linkedin, x, youtube, map-pin, mail, phone
acf-json/              ACF field groups as JSON (synced with live)
templates-2026/
  page-contact-2026.php "Contact 2026" page template (main.site-main--contact): ACF tabs Hero, Get in touch, Contact form
  page-layout-2026.php  "Dynamic Layout 2026" page template (main.site-main--layout)
src-2026/
  js/
    fonts.js           Fontsource imports (self-hosted fonts)
    main.js            entry: list of eager modules, init on DOM ready
    modules/           one file per component: default-export function, called from main.js
      headerNavigation.js header dropdowns, mobile panel, accordion-js submenus
      blogRow.js       Blog row "Load more" (REST fetch, count, focus)
      contactForm.js   Contact form: progress steps + step errors follow the Multi Step plugin
    utils/ready.js     DOM-ready helper
  scss/
    main.scss          entry: only @use lines (common first, then components)
    common/
      _reset.scss      modern CSS reset (global!)
      _variables.scss  colors, fonts, type scale, no CSS output
      _media.scss      include-media + breakpoints (single source of truth)
      _general.scss    base typography: html, headings, links
      _helper.scss     layout helpers: .content-block, .content-max, .content-narrow, .section-padding, %cover…
    components/        one partial per component (mirrors components-2026/)
      _button.scss     .btn, .btn--outline
      _blog-card.scss  blog post card (partials/blog-card.php)
      _accordion.scss  accordion-js base styles
      _header.scss     header bar, logo, hamburger
      _navigation.scss main nav, dropdowns, mobile panel
      _footer.scss     certification strip, footer columns, bottom bar
      _hero-page.scss  Dynamic Layout section: hero page (breadcrumb, title, accent lines)
      _two-column-image-text.scss  Dynamic Layout section: image + content, reverse, background colour
      _wysiwyg-editor.scss  Dynamic Layout section: editor content (h4 bar, dot lists, 12/30px rhythm)
      _introduction.scss  Dynamic Layout section: H2 left, text + two buttons right, background/text colour
      _blog-row.scss   Dynamic Layout section: title + count, blog card grid, Load more
      _service-cards.scss  Dynamic Layout section: H2 + accent lines, notched cards (full-card link), checkerboard fills
      _get-in-touch.scss  Contact 2026: centred intro, Call and Email cards
      _contact-form.scss  Contact 2026: progress steps, CF7 multi-step form (chips, fields, buttons, errors)
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

**Status (2026-10-06):** the header and footer are built, and "Contact 2026" uses both, with a Hero, a Get in touch and a Contact form section filled from its own ACF tabs. The "Dynamic Layout 2026" template and its `page_sections` field exist, with six sections so far: Hero page, Two-column image text, WYSIWYG editor, Introduction, Blog row and Service cards. No public page uses `get_header('new')` yet.

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

### Section padding (`.section-padding`)

This is for sections whose editors set the top and bottom padding with ACF sliders: today the WYSIWYG editor, Blog row and Service cards.

- **The slider value is the desktop padding.** Smaller screens multiply it by the same ratios as the default (76 / 64 / 35), so a section left at the default looks exactly as before.

  | Width | Factor | 76 (default) | 100 | 40 |
  | --- | --- | --- | --- | --- |
  | Base (desktop) | 1 | 76px | 100px | 40px |
  | `<tablet` | 64 / 76 | 64px | 84px | 34px |
  | `<phone` | 35 / 76 | 35px | 46px | 18px |

- **CSS:** `.section-padding` in `_helper.scss` reads `--padding-top` and `--padding-bottom` (px, set inline). It multiplies them by `--padding-scale`, which is set per breakpoint. Without the custom properties the padding is 76px.
- **PHP:** `dsa_2026_section_padding_style($top, $bottom, $max = 100)` (`inc/function-layout.php`) returns the inline style, e.g. `--padding-top: 100px; --padding-bottom: 40px`.
  - It only includes numeric values, capped at `$max`.
  - **`$max` is the slider's max.** It's 100 by default. A section whose default is higher passes its own: Service cards (default 104) has 0–150 sliders and passes `150`.
  - It returns `''` when neither is set, e.g. on rows saved before the sliders existed.
- **To add it to another section:**
  1. **ACF:** add two **Range** fields first in its Options tab: `padding_top` and `padding_bottom`, 50% each, 0–100, step 1, `px`, default 76. If the default is above 100, raise the max, and pass the same max to the helper.
  2. **Markup:** add the `section-padding` class to the `<section>`, and print the helper's string as its `style` (through `esc_attr()`). If the section already has an inline style, join the two strings with `; `.
  3. **Styles:** remove the section's own `padding-block` and its media queries. Component CSS loads after `common/`, so it would override the helper.

  A section with a different default (e.g. Introduction's 86px) would also set its own fallback with `--padding-top: 86px; --padding-bottom: 86px` on its class.

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

### Blog card (`partials/blog-card.php` + `_blog-card.scss`)

A card for the **current post in the loop**. `dsa_2026_blog_cards()` renders it, for the Blog row section and its Load more. A future blog archive can reuse it too.

```
article.blog-card
	.blog-card__media > img.blog-card__img      (featured image, medium_large, alt=""; empty $color__main box when missing)
	.blog-card__content
		time.blog-card__date[datetime]           (j F Y: "13 August 2026")
		h3.blog-card__title.h5 > a.blog-card__link
		span.blog-card__more[aria-hidden]        "Read now" + svg-arrow-right
```

- **One link per card:** the title link's `::after` covers the whole card. Screen readers hear one link, named after the title.
  - "Read now" is visual only (`aria-hidden`).
  - The focus outline is drawn on the `::after`, inside the card, because `overflow: hidden` would clip it outside.
- **Styles** (Figma node `223:2978`, "Insight card"):
  - **Card:** `$color__main-light` background, a 1px `$color__border-alt` border, 12px radius. It's a flex column, so cards in a grid row are the same height.
  - **Image:** a **fixed 218px high** box (Figma) on every card and every screen, with `object-fit: cover`, so it's cropped, never stretched.
    - The height is known before the image loads, so the layout doesn't shift. Every image is the same height, whatever its ratio.
    - It used to be `aspect-ratio`, but a tall image pushed its box taller, because the box's minimum height followed the content.
  - **Content:** padding 22px 24px 24px, `min-height: 230px`, so the card is 450px high at 1440 (1 + 218 + 230 + 1).
    - It has `margin-top: 0`, because the reset gives `article > * + *` a 1em margin.
  - **Date:** Manrope 12px, `$color__text-muted`.
  - **Title:** 12px below the date. The `.h5` class gives 22px; on top of that, weight 700, `line-height: 1.28`, `$color__text-light`.
  - **Read now:** Manrope 700 14px, a 6px gap, a 15px arrow. It sits at the bottom (`margin-top: auto`), with at least 24px above it.
  - **Hover and focus (not in Figma):** "Read now" and its arrow (`currentColor`) turn `$color__link`.
- **Colours:** `$color__border-alt` (`#004242`, Figma "Light Alt") and `$color__text-muted` (`#667c79`) were added for this card, with Andrea's approval.
  - Figma's count grey `#6b7b79` uses `$color__text-muted` too.

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
- **No editor:** `inc/function-layout.php` changes the edit screen of pages on this template and on Contact 2026, the two templates built only from ACF.
  - `dsa_2026_has_no_editor()` decides which pages: `dsa_2026_is_layout_page()` (the only place the layout template path is written) or the Contact 2026 template. Add a template there when it stops printing `the_content()`.
  - **Block editor:** turned off with the `use_block_editor_for_post` filter.
  - **Classic content box:** removed with `remove_post_type_support()` on `load-post.php`.
  - **The editor only goes away after a reload.** Create the page, pick the template, save the draft, then reload.
  - The old `post_content` stays in the database and isn't shown. It comes back if the page switches to another template.

### How the loop works (`dsa_2026_render_sections()`)

1. A password-protected page shows only the password form, as `the_content()` would (`dsa_2026_password_gate()` in `inc/function-helpers.php`).
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
| Options | Padding top (50%) | `padding_top` | range 0–100, step 1, `px` | default 76; desktop value, scaled down below |
| Options | Padding bottom (50%) | `padding_bottom` | range 0–100, step 1, `px` | default 76 |
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

  - **Padding:** set with the two sliders, and applied by `.section-padding` (see "Section padding").
    - The default is 76px, then 64px `<tablet`, then 35px `<phone`. This matches Two-column image text; the padding isn't in Figma.
    - Other values scale with the same ratios.

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

### Blog row (`sections/blog-row.php` + `_blog-row.scss`)

Layout `blog_row` ("Blog row"): a grid of blog cards. Figma: file `ZaphlvDdgp3I9EhmdhDnFe`, node `223:2978` ("Latest insights", 1440 wide).

```
section.blog-row.content-block.section-padding   [style=padding]   [data-blog-rest="…/wp-json/dsa-2026/v1/blog", Show all only]
	.content-max
		.blog-row__heading                           (when there's a title or a count)
			.blog-row__heading-group > h2.blog-row__title + span.blog-row__lines
			p.blog-row__count[aria-live=polite]      "Showing <span.blog-row__shown>9</span> of 24 insights"   (Show all only)
		.blog-row__grid                              (blog cards)
		.blog-row__more > button.btn.blog-row__load[data-page] > span.blog-row__load-label   (Show all, only while there are more pages)
```

| Tab | Label | Name | Type | Notes |
| --- | --- | --- | --- | --- |
| Options | Padding top (50%) | `padding_top` | range 0–100, `px` | default 80 (Figma) |
| Options | Padding bottom (50%) | `padding_bottom` | range 0–100, `px` | default 62 (Figma) |
| Options | Show all posts | `show_all` | true/false (switch) | default on |
| Options | Posts | `posts` | post object, multiple, `post`, published, return ID | only when Show all is off |
| Content | Title | `title` | text | default "Latest"; empty: no title |

- **Show all on:**
  - It shows the latest published posts, **9 at a time** (`DSA_2026_BLOG_PER_PAGE`, a fixed number, Andrea's choice).
  - It adds the "Showing x of y insights" count and the Load more button.
- **Show all off:** only the picked posts, in the picked order (drag to reorder). There's no count and no Load more.
- **No posts** (none published, none picked, or only unpublished ones): the section prints nothing.
- **Query and cards:** `inc/function-blog.php`.
  - `dsa_2026_blog_query($page, $ids)`:
    - with `$ids === null`, the latest posts, page `$page`;
    - with an array, only those IDs, ordered by `post__in`. An empty array gives no posts, never every post.
  - `dsa_2026_blog_cards($query)` returns the cards' HTML.
- **Load more:**
  - **Route:** `GET /wp-json/dsa-2026/v1/blog?page=N`. It's public and returns only published posts. The page size is fixed on the server.
  - **Response:** `{ html, shown, total, has_more }`.
  - **`modules/blogRow.js`:**
    - **While loading:**
      - it adds `.is-loading` and `aria-disabled="true"` to the button;
      - the label hides but keeps its width, so the button doesn't change size;
      - a 20px ring spins in the text colour (`::after`, `@keyframes blog-row-spin`, 0.7s);
      - clicks are ignored until the posts arrive.
      - It's `aria-disabled`, not `disabled`, so the button keeps keyboard focus.
      - With reduced motion it still turns, slower (1.5s), because it's the only sign that something is loading. It overrides the reset's `!important` rule.
    - it appends the `html`;
    - it updates `.blog-row__shown`, which the `aria-live` count announces;
    - it moves focus to the first new card;
    - it removes the button when `has_more` is false;
    - on an error, the button stays and the next click retries.
  - The URL is built with the URL API, so plain permalinks (`?rest_route=`) work too.
  - Each Blog row on a page works on its own.
- **Padding:** `.section-padding` with the sliders. The section's defaults are on its class (`--padding-top: 80px; --padding-bottom: 62px`), and they scale down like every slider.
- **Styles:**
  - **Section:** `$color__main` background, `$color__text-light` text.
  - **Heading:**
    - flex, `space-between`, `align-items: end`, wrapping, 34px above the grid;
    - the title is the base h2;
    - the lines are 42×3 `$color__link` + 12×3 `$color__accent-hover`, with a 2px radius, a 7px gap and 7px under the title;
    - the count is Manrope 13px, `$color__text-muted`, and stays on the right (`margin-left: auto`).
  - **Load more:** the `.btn`, centred, 42px under the grid (Figma 34 + 8).
  - **Grid:** 24px gap.

    | Width | Columns |
    | --- | --- |
    | Base (desktop) | 3 (384px cards at 1440, as in Figma) |
    | `<tablet` | 2 |
    | `<phone` | 1 |

### Service cards (`sections/service-cards.php` + `_service-cards.scss`)

Layout `service_cards` ("Service cards"): an optional H2 with the accent lines, then a grid of notched cards. Each card has a title, a text and a link, and the link covers the whole card. Figma: file `ZaphlvDdgp3I9EhmdhDnFe`, node `399:5611` ("Services", 1440 wide).

```
section.service-cards.content-block.section-padding   [style=padding]
	.content-max
		.service-cards__heading                     (only with a title)
			h2.service-cards__title + span.service-cards__lines
		ul.service-cards__list                      (grid)
			li.service-card                         (::before border + ::after fill: the notched shape)
				h3.service-card__title.h5
				p.service-card__text
				a.service-card__link                (only with a URL; ::after covers the card)
					span (link text) + span.service-card__arrow > svg-arrow-right
```

| Tab | Label | Name | Type | Notes |
| --- | --- | --- | --- | --- |
| Options | Padding top (50%) | `padding_top` | range 0–150, step 1, `px` | default 104 (Figma) |
| Options | Padding bottom (50%) | `padding_bottom` | range 0–150, step 1, `px` | default 104 (Figma) |
| Content | Title | `title` | textarea (new lines → `<br>`) | optional, always an `<h2>` |
| Content | Cards | `cards` | repeater (block, "Add card", collapsed on the title) | |
| ↳ | Title | `title` | text | required |
| ↳ | Content | `content` | textarea (new lines → `<br>`) | plain text |
| ↳ | Link | `link` | link | optional |

- **Empty:** a card with no title and no text is skipped. With no cards left, the section prints nothing. The title is optional.
- **Title:** `wp_kses()` keeps only `<span class>` and `<br>`, and `<span class="accent">` highlights words in `$color__link`. Its look comes from the base `h2`.
- **Full-card link:** this is the stretched-link pattern of the blog card.
  - **The `<a>` is the CTA.** Its text is the link's text ("Explore IT Asset Disposal"), or "Read more" when that's empty ("Read more" is the Figma component's own label).
  - **Its `::after` covers the card,** so a click anywhere follows the link. Screen readers get one short, descriptive link per card, and the title stays a real `<h3>`.
  - **Why the card isn't wrapped in an `<a>`:** the link name would be every word on the card.
  - **Content is a textarea, not a WYSIWYG:** a link inside the card would be a nested `<a>`.
  - A card without a URL has no link and isn't clickable. `target="_blank"` gets `rel="noopener"`.
  - **Focus:** a 2px `$color__link` outline on the `::after`, 2px outside the card. It's a rectangle, and the notch corners are clickable too.
- **Notched shape:** Figma's "Subtract" vector is a rectangle with 31 × 46px cut out of the top-left and bottom-right corners, and a 1px border inside.
  - **Two pseudo-elements, the same `clip-path` polygon (`--card-shape`):**
    - `::before`, `inset: 0`, is the border colour;
    - `::after`, `inset: 1px`, is the fill.
  - Drawn this way, the same polygon gives an even 1px border on every edge, the notches included.
  - The card is `isolation: isolate`, so the `z-index: -1` layers stay behind its content and above the section background.
  - **Not `corner-shape: notch`:** that property draws this exact shape natively (`border-radius: 31px 0 / 46px 0`), but Safari only has it in Technology Preview, and iOS not at all. Andrea chose the pseudo-elements so the shape works everywhere.
- **Colours** (Figma, approved by Andrea):
  - **Border:** `$color__service-card-border` `#24433f`.
  - **Fills:** `$color__service-card` `#173632` (dark) and `$color__border-alt` `#004242` (teal), set through `--card-fill`.
  - **Two columns:** a checkerboard, as in Figma. `:nth-child(4n + 2)` and `:nth-child(4n + 3)` are teal (1 dark, 2 teal / 3 teal, 4 dark…).
  - **One column:** odd cards are dark, even cards teal.
- **Card:**
  - It's 208px high at least (Figma), with `padding: 25px 52px` and the content centred.
    - A two-line text gets Figma's 38px of top space.
    - A three-line text stays 208px high, as in Figma.
  - `<phone`: the padding is 32px 40px, still clear of the notch.
  - The title, text and link are 16px apart (margins, so a missing one leaves no gap).
- **Card text:**
  - **Title:** `.h5` (22px), with weight 700 and `line-height: 1.28` (Figma "H4"), the same as the blog card title.
  - **Text:** Manrope 16px, `line-height: 1.6`.
  - **Link:** Manrope 14px 700, `$color__link`, with the 15px `svg-arrow-right` after a 6px gap.
    - Its colour is also set on `:hover` and `:focus`, against the global `a:hover { color: inherit }`.
    - **Hover and focus (not in Figma, Andrea's choice):** the arrow slides 4px right. The transform is on the arrow's span, because on the `<a>` it would become the containing block of the link's `::after`.
- **Heading:** the lines sit 16px under the title. They're 58 × 4px `$color__link` + 18 × 4px `$color__accent-hover`, with a 2px radius and a 7px gap. The heading is 46px above the cards.
- **Padding and grid:** padding comes from `.section-padding`, with the 104px defaults on the class. At 1440 the cards are 580px wide, as in Figma.

  | Width | Padding top / bottom (default) | Grid |
  | --- | --- | --- |
  | Base (desktop) | 104px (Figma) | 2 columns, gap 30px / 40px (Figma) |
  | `<tablet` | 88px | 2 columns |
  | `<phone-land` | 88px | 1 column, gap 24px |
  | `<phone` | 48px | 1 column, gap 24px |

  Only the desktop values are in Figma.

## Contact 2026 (`templates-2026/page-contact-2026.php`)

A fixed page: the template sets the sections and their order, and editors fill one ACF tab per section. The pages have no editor (see "No editor" under Dynamic Layout 2026).

```
main#main.site-main.site-main--contact
	section.hero-page.content-block       components-2026/sections/hero-page.php   (tab Hero, get_field('hero'))
	section.get-in-touch.content-block    components-2026/page/get-in-touch.php    (tab Get in touch, get_field('get_in_touch'))
	section.contact-form.content-block    components-2026/page/contact-form.php     (tab Contact form, get_field('contact_form'))
```

- **Group:** the ACF group "Contact 2026" (`acf-json/group_6ac50cfa10bbb.json`), shown under the title when Page Template is `templates-2026/page-contact-2026.php`.
- **One tab per section, one Group field per tab** (`hero`, `get_in_touch`, `contact_form`). `get_field('<group>')` returns the section's `$args` as they are, and both tabs can have a `title`.
- **Template:** inside the loop, `dsa_2026_password_gate()` comes first (password form only), then one `get_template_part()` per section, with `(array) dsa_2026_field('<group>')` as its `$args`.
  - The template sets **no variables** (see "Page templates run in the global scope" in PHP conventions).
  - With ACF inactive, the hero falls back to Home and the page title, and Get in touch prints nothing.
- **To add a section:**
  1. Add a tab and a Group field to the group.
  2. Add one `get_template_part()` line to the template.
  3. Reuse a `sections/` component when the design matches. Otherwise, create the component in `components-2026/page/`.

### Hero tab

- The `hero` Group has **the same sub fields as the `hero_page` layout**: same names, labels and settings, only the keys differ.
- It renders with the same component, `sections/hero-page.php` (see "Hero page"). It's the first section, so its image is eager-loaded.
- The fields are a **copy, not a clone** (see "Decisions"). When you change a `hero_page` field, change this group too.

### Get in touch (`page/get-in-touch.php` + `_get-in-touch.scss`)

Figma: file `ZaphlvDdgp3I9EhmdhDnFe`, node `223:3151` ("Contact details section", 1440 wide).

```
section.get-in-touch.content-block
	.content-max > .get-in-touch__inner                 (flex column, centred)
		.get-in-touch__intro                            (when there's a title or content)
			h2.get-in-touch__title.h3
			div.get-in-touch__text
		ul.get-in-touch__cards                          (when there's a phone or an email)
			li.get-in-touch__card > a.get-in-touch__link[href=tel:…|mailto:…]
				span.get-in-touch__icon[aria-hidden] > svg-phone | svg-mail
				span.get-in-touch__detail > span.__label + span.__value
```

| Label | Name | Type | Notes |
| --- | --- | --- | --- |
| Title | `title` | text | always an `<h2>`; `<span class="accent">` highlights |
| Content | `content` | WYSIWYG (basic, no media) | |
| Phone number (50%) | `phone` | text | shown as typed |
| Email (50%) | `email` | email | |

- **Nothing filled:** the section prints nothing. Each card is printed only when its field is set.
- **Phone:** the card shows the number as typed (`0800 000 0000`).
  - The `tel:` link keeps only the digits and `+`.
  - A value without digits gives no card.
- **Email:** `sanitize_email()` + `is_email()`. The address and the `mailto:` go through `antispambot()`, so they're entity-encoded in the source.
- **Labels:** "Call" and "Email" are hard-coded.
- **Icons:** the footer's `svg-phone` and `svg-mail` (16px, `$color__link` stroke), drawn at Figma's 19px.
- **Title:**
  - `wp_kses()` keeps only `<span class>`.
  - Figma's style is Titillium Web Bold 36 / 1.2, so the `<h2>` takes the `.h3` class (36px and its scale) plus `font-weight: 700; line-height: 1.2`.
- **Text:** `wp_kses_post()`, Manrope 16px, `line-height: 1.6`, max-width 560px, centred.
  - Paragraphs have no gap.
  - Links are `$color__link` and underlined.
- **Colours:** the section is `$color__main` with `$color__text-light` text. The cards use four Figma colours that Andrea approved:

  | Variable | Value | Used for |
  | --- | --- | --- |
  | `$color__card-border` | `#1f3a35` | card border and divider |
  | `$color__card-icon` | `#0b1f1d` | icon tile |
  | `$color__card-label` | `#c9d8d5` | "Call" / "Email" |
  | `$color__card-value` | `#e7f9f7` | number and address |

- **Styles:**
  - **Intro:** max-width 760px, centred text, 12px between the title and the text, 32px above the cards.
  - **Cards:** a 760px row with a 24px gap. Each card is half the row; a single card stays half width, centred.
    - **Card:** `$color__main-light`, a 1px border, 12px radius, 24px padding. The detail comes first, then a 1px divider 20px below it (Figma).
    - **Icon tile:** 42px with an 8px radius, 14px from the text.
    - **Text:** the label is Manrope 12px 600, the value 15px 700, 3px apart, `line-height: normal`. Long addresses wrap.
    - **Hover:** the value turns `$color__link`. **Focus:** a `$color__link` outline.
  - **Not built:** Figma's empty 60×20 "Text-hover" element above the title. Because of it, at 1440px the section is about 421px high (Figma: 453px).

    | Width | Padding top / bottom | Cards |
    | --- | --- | --- |
    | Base (desktop) | 72px (Figma) | 2 columns |
    | `<tablet` | 64px | 2 columns |
    | `<phone-land` | 64px | 1 column, full width |
    | `<phone` | 35px | 1 column |

    Only the desktop values are in Figma.

### Contact form (`page/contact-form.php` + `_contact-form.scss` + `modules/contactForm.js`)

The multi-step **Contact Form 7** form, with the free **Multi Step for Contact Form 7** plugin (NinjaTeam, `cf7mls`), in a new layout. Figma: file `ZaphlvDdgp3I9EhmdhDnFe`, node `223:3175` ("Contact flow", 1440 wide). It comes after Get in touch, whose text points to "the service finder".

```
section.contact-form.content-block
	.content-max > .contact-form__inner                 (flex column, centred, gap 48)
		.contact-form__intro > h2.contact-form__title.h3 + div.contact-form__text
		.contact-form__card[.is-sent]                    (only when the picked form exists and CF7 is active)
			ol.contact-form__steps > li.contact-form__step[.is-current|.is-done][aria-current=step]
			div.wpcf7 > form.wpcf7-form                  (CF7's markup, below)
				.fieldset-cf7mls-wrapper > fieldset.fieldset-cf7mls[.cf7mls_current_fs]…   (one per step, the plugin's)
					div.form-chips | div.form-fields      (from the CF7 form, below)
					p.form-error[role=alert][hidden]
					div.cf7mls-btns > button.cf7mls_back + button.cf7mls_next
				div.wpcf7-response-output
```

| Label | Name | Type | Notes |
| --- | --- | --- | --- |
| Title | `title` | text | always an `<h2>`; `<span class="accent">` highlights |
| Content | `content` | WYSIWYG (basic, no media) | |
| Form | `form` | select | every CF7 form (ID → title), filled at runtime by `dsa_2026_contact_form_choices()`; empty: no card |

- **Form picker:** a select rather than an ACF post object, because CF7's post type isn't public. Its choices come from the `acf/load_field/key=field_6ac519eb20123` filter in `inc/function-contact-form.php`.
- **Step labels:** "Service", "Sector" and "Details" are **hard-coded** in `contact-form.php`, Andrea's choice. The free plugin can't rename its steps; that's a Pro feature. Keep the list in the form's step order.
- **On 2026 pages only** (`dsa_2026_contact_form_setup()` on `get_header` `'new'`):
  - `is_using_cf7mls_css` → false: the plugin's CSS (floats, button colours, sliding absolute fieldsets) is off, and `_contact-form.scss` replaces it.
  - `wpcf7_autop_or_not` → false: CF7 adds no `<p>`/`<br>`, so the markup is exactly the form's.
  - **CF7's own CSS stays.** It hides the screen-reader response and the hidden fields, and draws the spinner. Some of our selectors are stronger than its on purpose.
- **JS (`contactForm.js`):** the plugin's jQuery switches steps by moving `.cf7mls_current_fs`, after an AJAX validation. A `MutationObserver` on the form's classes (one sync per frame):
  - sets `is-current` and `aria-current="step"` on the step, and `is-done` on the earlier ones;
  - un-hides a step's `.form-error` while that step has a `.wpcf7-not-valid` field (`role="alert"` announces it);
  - adds `.is-sent` to the card while the form has `sent`. The steps are hidden, and CF7's message shows (CF7 Messages tab).
- **Last step:** the plugin's editor writes `[submit]` *before* its Back button. The last fieldset is a wrapping row with `order`, so Back comes first and Complete second, centred, as in the other steps. CF7's spinner is absolutely positioned beside them.
- **Styles** (Figma; every colour is a variable):
  - **Section:** like Get in touch (`$color__main`, padding 72 / 64 / 35, the title `.h3` + 700). 14px between title and text, and 48px before the card (32px `<phone`).
  - **Card:** max-width 760px, `$color__main-light`, 1px `$color__card-border`, no radius.
  - **Steps:** equal columns, each with a 4px top bar (`$color__link` when current or done, else `$color__card-border`). Labels are Manrope 14px: current 700 `$color__card-value`, the others 600 `$color__card-label`.
  - **Step content:** padding 36px (24px `<phone-land`, 20px `<phone`), with a 28px gap.
  - **Chips (checkboxes):**
    - The input is visually hidden, and the label is the chip: `$color__card-icon`, 1px `$color__card-border`, 5px radius, 12px 16px padding, 14px 600.
    - **Checked** (`:has(input:checked)`): `$color__link` border. **Hover:** `$color__card-label` border. **Keyboard focus:** `$color__link` outline.
  - **Error:** 13px `$color__error`, centred, after Figma's alert-circle (a CSS mask in `currentColor`, inline so it stays beside wrapped text). CF7's own tip is hidden for chips.
  - **Fields** (step 3, not in Figma): a grid of 3 columns (2 `<phone-land`, 1 `<phone`). Labels are 12px 600 `$color__card-label`. Inputs match the chips, with a `$color__link` border on focus and `$color__error` when invalid, with CF7's tip under them.
  - **Buttons:** 46px high (Figma), min-width 96px, padding 0 28px, radius 6px, 16px 600.
    - **Next / Complete:** `$color__link` with `$color__text` text, `$color__accent-hover` on hover, like `.btn`.
    - **Plugin colours ignored:** the plugin prints its Multi-Step Settings colours as inline styles, which a duplicated form inherits. The buttons' `background-color` and `color` are `!important`, the only way past an inline style, so those settings have no effect.
    - **Back:** `$color__card-icon` with a `$color__link` border and text, and fills like `.btn--outline`.
  - **Response:** no border, centred, 14px `$color__error`. When sent, 16px `$color__link`, in place of the steps.
  - **Plugin step message:** "One or more fields have an error…" is appended to the step by the plugin's JS, with its own icon (`svg.wpcf7-icon-wraning`, sic) and an inline `display: block`. While it has `.wpcf7-validation-errors`, it's `display: flex !important` (to beat the inline style), centred, with a 12px column gap, and the icon is `$color__text-light`. The plugin removes the class to hide it. It sits under the buttons in every step (`order: 3` in the last step).
- **Not built:**
  - the old step icons (in Figma they're dark on dark, so invisible);
  - Figma's bottom divider (it sits on the card border);
  - a Back button on step 1 (the plugin has none on the first step).

#### CF7 setup (WP admin)

The 2026 page uses its own copy of the form, **"Multi Step 2026"**. The legacy contact page (page 16) keeps "Multi Step".

1. **Duplicate:** Contact > Contact Forms > Multi Step > **Duplicate**, and rename it "Multi Step 2026".
2. **Form tab:** the plugin shows one box per step. Replace each box's text with its snippet, and set the Back/Next labels: STEP 1 "Next", STEP 2 "Back" + "Next", STEP 3 "Back".
3. **Multi-Step Settings tab:** the Back/Next colours can stay as they are: the 2026 CSS overrides them. Type the button labels as they should read ("Next", not "NEXT"): the CSS doesn't change their case.
4. **Mail tab:** unchanged, because the field names are the same.
5. **Contact 2026 page:** pick "Multi Step 2026" in the **Contact form** tab.

STEP 1:
```
<div class="form-chips">[checkbox* service use_label_element "IT disposal" "Data destruction" "Data centre decommissioning"]</div>
<p class="form-error" role="alert" hidden>Please select at least one service to continue.</p>
```
STEP 2:
```
<div class="form-chips">[checkbox* equipment use_label_element "Laptops" "PCs" "Monitors" "Printers" "Servers" "Networking" "Other"]</div>
<p class="form-error" role="alert" hidden>Please select at least one type of equipment to continue.</p>
```
STEP 3:
```
<div class="form-fields">
<label class="form-field"><span>First name *</span>[text* first-name autocomplete:given-name akismet:author]</label>
<label class="form-field"><span>Last name *</span>[text* last-name autocomplete:family-name akismet:author]</label>
<label class="form-field"><span>Telephone</span>[text your-telephone autocomplete:tel]</label>
<label class="form-field"><span>Email *</span>[email* your-email autocomplete:email]</label>
<label class="form-field"><span>Company *</span>[text* your-company autocomplete:organization]</label>
<label class="form-field"><span>Post code *</span>[text* your-postcode autocomplete:postal-code]</label>
</div>
[submit "Complete"]
```

- **Class names the CSS and JS rely on:** `form-chips`, `form-error`, `form-fields`, `form-field`. Keep them when you edit the form.
- **Telephone** is optional here. It was `text*` in the legacy form.

#### Where the legacy layout lives (for the switch-over)

None of this loads on 2026 pages: their legacy CSS and JS are dequeued, and `[progressbar]` isn't called.
- `templates/page-blog.php` (page 16 only): `[progressbar]`, then `[contact-form-7 id="e3ba6e6" title="Multi Step"]`. With `?thank=1` it shows the thank-you block instead (paper plane, blog link, social SVGs).
- `[progressbar]` is `wpb_progressbar()` in `library/function-setup.php`: the three step SVGs (Service, Sector, Details) and the bar.
- `dist/scripts/main.js` moves the bar into the form, moves it on `cf7mls_current_fs`, toggles `.active` on checkboxes, and redirects to `/contact/?thank=1` on `wpcf7mailsent`.
- `dist/styles/map/_form.scss` holds the multistep styles.
- The legacy form shows its red hints as static `<p style="color:red">` lines.

## ACF fields (`acf-json/`)

- ACF saves every field group as JSON in `acf-json/`, which it detects automatically in the theme. Commit these files.
- **The JSON is written on the server** when a group is saved in WP admin. After editing fields on live, download the changed `group_*.json` files into the repo and commit them.
- **Never upload an older `acf-json/` over the server's copy.** Groups showing "Awaiting save" just haven't been written to JSON yet: open the group and save it.
- **Read 2026 fields with `get_field()`,** always behind a `function_exists('get_field')` check in `inc/` code.
  - The Dynamic Layout builder reads the whole flexible field once and hands each row to its section as `$args`. Sections never call `get_sub_field()` (see "Dynamic Layout 2026").
  - Fixed templates (Contact 2026) use one tab per section, holding one **Group** field: `get_field('<group>')` is that section's `$args`, and two sections can both have a `title`.
  - **In page templates, read fields with `dsa_2026_field('name')`** (`inc/function-acf.php`): `get_field()` for the current post, or `null` with ACF inactive. It keeps the ACF check out of the template, so the template needs no variable.
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
- **Page templates run in the global scope.** WordPress `include`s them from `template-loader.php`, so a variable set in a template is a global and can overwrite one that WordPress or a plugin uses.
  - **Example:** on 2026-10-06, `$acf = function_exists('get_field');` in the Contact template replaced ACF's own `$acf` instance. Every `get_field()` then died with "Call to a member function init() on bool".
  - **Don't set variables in `templates-2026/`.** Put the logic in an `inc/` helper or in a component. Components loaded with `get_template_part()` run inside a function, so their variables are local.
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

**2026-10-06:** Contact 2026 needed the Hero page fields. Andrea chose to **copy** them into a `hero` Group in the "Contact 2026" group (`group_6ac50cfa10bbb.json`) instead of cloning, so the Dynamic Layout group stayed untouched. **When you change a `hero_page` field, change the Contact 2026 `hero` group too.**

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
- **2026-10-06**
  - WYSIWYG editor: new **Padding top / Padding bottom** sliders (ACF Range, 0–100px, default 76) in the Options tab, before Container.
  - New reusable section padding (see "Section padding"):
    - `.section-padding` in `_helper.scss`: inline `--padding-top` / `--padding-bottom`, scaled by 64/76 `<tablet` and 35/76 `<phone`;
    - `dsa_2026_section_padding_style()` in `inc/function-layout.php`.
  - `.wysiwyg-editor` no longer sets `padding-block`, and its default spacing is unchanged (verified 76 / 64 / 35).
- **2026-10-06**
  - Fifth Dynamic Layout section, **Blog row**: layout `blog_row`, `components-2026/sections/blog-row.php` and `_blog-row.scss`. Figma node `223:2978`.
    - **Options tab:**
      - padding sliders (default 80 / 62);
      - **Show all posts**: the latest 9, then Load more and the count;
      - when it's off, a **Posts** post object picker, with no Load more and no count.
    - **Content tab:** the title ("Latest").
  - New reusable **blog card**: `components-2026/partials/blog-card.php` + `_blog-card.scss`.
  - New `inc/function-blog.php`, with the query, the cards and the REST route `dsa-2026/v1/blog`. It's required from `function-dev.php`.
  - New `src-2026/js/modules/blogRow.js` (Load more), called from `main.js`.
  - New icon `svg-arrow-right` (Figma asset, `currentColor` stroke).
  - New colours, approved by Andrea: `$color__border-alt` `#004242` and `$color__text-muted` `#667c79`.
- **2026-10-06**
  - **Contact 2026** is now built only from ACF tabs, in the new group "Contact 2026" (`group_6ac50cfa10bbb.json`). `the_content()` is gone. See "Contact 2026".
    - **Hero tab:** a `hero` Group with the `hero_page` layout's fields, copied (Andrea's choice, see "Decisions"). It's rendered by `sections/hero-page.php`.
    - **Get in touch tab:** a title, a WYSIWYG, a phone (text) and an email (email field). It's rendered by the new `components-2026/page/get-in-touch.php` + `_get-in-touch.scss`. Figma node `223:3151`.
  - No editor on Contact 2026 pages either: the new `dsa_2026_has_no_editor()` checks both templates. The callbacks are renamed `dsa_2026_no_block_editor` / `dsa_2026_no_classic_editor`.
  - The password-form guard moved into `dsa_2026_password_gate()` (`function-helpers.php`). `dsa_2026_render_sections()` and the Contact template both use it.
  - New colours, approved by Andrea (Figma): `$color__card-border` `#1f3a35`, `$color__card-icon` `#0b1f1d`, `$color__card-label` `#c9d8d5`, `$color__card-value` `#e7f9f7`.
  - `main.scss` has a new "Page components" group for `components-2026/page/`.
- **2026-10-06**
  - Blog card images are now a **fixed 218px** box with `object-fit: cover`, instead of `aspect-ratio`. On the live page, tall images stretched their cards. Every image is now the same height, whatever its ratio, and the layout doesn't shift while images load. Andrea confirmed the Figma value, 218px.
- **2026-10-06**
  - Blog row **Load more** now shows that it's loading, as Andrea asked.
    - The label is swapped for a spinning ring inside the button, which keeps its width.
    - The button gets `.is-loading` + `aria-disabled` (it keeps focus), and a double-click sends one request.
    - The spinner still turns, slower, with reduced motion.
  - The button label is now wrapped in `span.blog-row__load-label`.
- **2026-10-06**
  - **Fix:** Contact 2026 showed a critical error: "Call to a member function init() on bool" in ACF.
    - **Cause:** `$acf = function_exists('get_field');` in the template. Templates run in the global scope, so it overwrote ACF's global `$acf` instance.
    - **Fix:** the new `dsa_2026_field()` (`inc/function-acf.php`) reads the fields, and the template no longer sets any variable.
    - The rule is now in "PHP conventions".
- **2026-10-06**
  - Sixth Dynamic Layout section, **Service cards**: layout `service_cards`, `components-2026/sections/service-cards.php` and `_service-cards.scss`. Figma node `399:5611`.
    - **Options tab:** padding sliders, 0–150px, default 104 (Figma).
    - **Content tab:** an optional H2 title (accent span), and a **Cards** repeater: title, text, link.
    - The link covers the whole card (stretched link, as in the blog card). Its text is the CTA, "Read more" when empty.
    - The notched shape is two pseudo-elements with the same `clip-path` polygon, the border and the fill. `corner-shape: notch` was rejected because Safari and iOS don't support it.
    - The fills are a checkerboard in two columns, and alternate in one.
    - The hover (the arrow slides 4px right) isn't in Figma; Andrea chose it.
  - `dsa_2026_section_padding_style()` has a new optional `$max` argument (default 100). Service cards passes 150, and the other calls are unchanged.
  - New colours, approved by Andrea (Figma): `$color__service-card` `#173632` and `$color__service-card-border` `#24433f`. The teal fill is the existing `$color__border-alt`.
- **2026-10-06**
  - Contact 2026: a third tab, **Contact form** (`contact_form` group: title, content, form picker). It renders the CF7 multi-step form in the Figma layout (node `223:3175`). See "Contact form" under Contact 2026.
    - New `components-2026/page/contact-form.php`, `_contact-form.scss` and `modules/contactForm.js` (called from `main.js`).
    - New `inc/function-contact-form.php`: the ACF form picker, and on 2026 pages the Multi Step plugin's CSS and CF7's autop are off.
    - The step labels (Service, Sector, Details) are hard-coded, because the free plugin can't rename steps. The old step icons are dropped.
    - Andrea copies the form in CF7 as "Multi Step 2026" with the new markup, so the legacy contact page keeps its form.
  - New colour, approved by Andrea (Figma): `$color__error` `#ff7a7a`, for form validation.
- **2026-10-06**
  - Contact form: the Next/Complete text is now `$color__text`. On live it was grey: the form copy inherited the original's Multi-Step Settings colours, which the plugin prints as inline styles. The buttons' colours are now `!important`, so those settings are ignored on 2026 pages.
  - Contact form: the plugin's step message is a centred flex row (12px gap), with its icon in `$color__text-light`. See "Plugin step message".
