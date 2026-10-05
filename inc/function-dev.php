<?php
/**
 * 2026 layout: theme functions (see restructure.md)
 * Included once from functions.php. Register new 2026 function files in the requires below.
 *
 * @package FDRY
 */

/*==================================================================================
  2026 FILES
==================================================================================*/
// fonts, CSS and JS from dist-2026 on pages that use header-new.php
require get_template_directory() . '/inc/function-assets.php';
// small template helpers (SVG as string…)
require get_template_directory() . '/inc/function-helpers.php';
// Main Menu 2026 markup: submenu toggles, dropdown arrows, ACF menu button
require get_template_directory() . '/inc/function-navigation.php';
// ACF options pages (Options > Footer) + dsa_2026_option() helper
require get_template_directory() . '/inc/function-acf.php';

/*==================================================================================
  THEME SUPPORT
==================================================================================*/
add_action('after_setup_theme', 'dsa_2026_theme_support');

function dsa_2026_theme_support()
{
	// Logo: Appearance > Customize > Site Identity > Logo. Output it with the_custom_logo().
	// flex-width/height: no forced crop, width/height are only the suggested size.
	add_theme_support('custom-logo', array(
		'height'      => 100,
		'width'       => 300,
		'flex-height' => true,
		'flex-width'  => true,
	));

	register_nav_menus(
		array(
			'main-menu-2026'         => __('Main Menu 2026'),
			'footer-service-2026'    => __('Footer Service 2026'),
			'footer-company-2026'    => __('Footer Company 2026'),
			'footer-legal-2026'      => __('Footer Legal 2026'),
		)
	);
}
