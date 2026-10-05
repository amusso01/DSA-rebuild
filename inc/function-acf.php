<?php
/**
 * 2026 layout: ACF options pages and option helpers.
 * The parent "Options" page (slug acf-options) is registered by the legacy
 * acf_add_options_page() in library/function-setup.php.
 *
 * @package FDRY
 */

/*==================================================================================
  OPTIONS PAGES
==================================================================================*/
// Options > Footer: fields in acf-json "Footer 2026" (footer-new.php, components-2026/footer/)
add_action('acf/init', 'dsa_2026_options_pages');

function dsa_2026_options_pages()
{
	if (!function_exists('acf_add_options_sub_page')) {
		return;
	}

	acf_add_options_sub_page(array(
		'page_title'  => __('Footer'),
		'menu_title'  => __('Footer'),
		'menu_slug'   => 'acf-options-footer',
		'parent_slug' => 'acf-options',
	));
}

// With a child page, ACF's default 'redirect' => true turns the parent into a link to
// its first child and hides the legacy Options fields. Keep the parent as its own page.
add_filter('acf/get_options_page', 'dsa_2026_options_parent_no_redirect', 10, 2);

function dsa_2026_options_parent_no_redirect($page, $slug)
{
	if ($slug === 'acf-options') {
		$page['redirect'] = false;
	}
	return $page;
}

/*==================================================================================
  HELPERS
==================================================================================*/
// Value of an options field, or null when ACF is inactive.
// Option field names share one namespace across all options pages: prefix them (footer_…).
function dsa_2026_option($name)
{
	return function_exists('get_field') ? get_field($name, 'option') : null;
}
