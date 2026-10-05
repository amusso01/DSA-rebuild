<?php
/**
 * 2026 layout: Main Menu 2026 markup.
 * The filters only touch the main-menu-2026 location. Parents get the accordion-js
 * classes (ac / ac-trigger / ac-panel): a dropdown on desktop, an accordion on mobile.
 *
 * @package FDRY
 */

function dsa_2026_is_main_menu($args)
{
	return isset($args->theme_location) && $args->theme_location === 'main-menu-2026';
}

function dsa_2026_has_children($item)
{
	return in_array('menu-item-has-children', (array) $item->classes, true);
}

/*==================================================================================
  PARENT ITEMS: toggle button + accordion classes
==================================================================================*/
add_filter('walker_nav_menu_start_el', 'dsa_2026_menu_toggle', 10, 4);

function dsa_2026_menu_toggle($item_output, $item, $depth, $args)
{
	if (!dsa_2026_is_main_menu($args) || $depth !== 0 || !dsa_2026_has_children($item)) {
		return $item_output;
	}

	$label = sprintf(__('Show %s submenu'), wp_strip_all_tags($item->title));

	return $item_output
		. '<button class="submenu-toggle ac-trigger" type="button" aria-expanded="false" aria-label="' . esc_attr($label) . '">'
		. '<span aria-hidden="true">' . dsa_2026_get_svg('chevron-down') . '</span>'
		. '</button>';
}

add_filter('nav_menu_css_class', 'dsa_2026_menu_item_class', 10, 4);

function dsa_2026_menu_item_class($classes, $item, $args, $depth)
{
	if (dsa_2026_is_main_menu($args) && $depth === 0 && dsa_2026_has_children($item)) {
		$classes[] = 'ac';
	}
	return $classes;
}

add_filter('nav_menu_submenu_css_class', 'dsa_2026_submenu_class', 10, 3);

function dsa_2026_submenu_class($classes, $args, $depth)
{
	if (dsa_2026_is_main_menu($args)) {
		$classes[] = 'ac-panel';
	}
	return $classes;
}

/*==================================================================================
  SUBMENU ITEMS: arrow inside the link
==================================================================================*/
add_filter('nav_menu_item_title', 'dsa_2026_submenu_arrow', 10, 4);

function dsa_2026_submenu_arrow($title, $item, $args, $depth)
{
	if (!dsa_2026_is_main_menu($args) || $depth !== 1) {
		return $title;
	}
	return '<span class="sub-menu__label">' . $title . '</span>'
		. '<span class="sub-menu__arrow" aria-hidden="true">' . dsa_2026_get_svg('arrow') . '</span>';
}

/*==================================================================================
  MENU BUTTON (ACF)
==================================================================================*/
// ACF Link field "menu_button_" on the menu assigned to $location
// (group "Menu Main 2026", acf-json/group_6ac3ae82a8c1c.json). Returns the link array or null.
function dsa_2026_menu_button($location = 'main-menu-2026')
{
	if (!function_exists('get_field')) {
		return null;
	}

	$locations = get_nav_menu_locations();
	if (empty($locations[$location])) {
		return null;
	}

	$menu = wp_get_nav_menu_object($locations[$location]);
	if (!$menu) {
		return null;
	}

	$button = get_field('menu_button_', $menu);
	return !empty($button['url']) ? $button : null;
}
