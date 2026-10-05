<?php
/**
 * Header: Main Menu 2026 + the ACF menu button as last item.
 * Toggle buttons, arrows and accordion classes come from inc/function-navigation.php.
 *
 * @package FDRY
 */

$button = dsa_2026_menu_button('main-menu-2026');
?>
<nav id="header-nav" class="main-nav" aria-label="<?php esc_attr_e('Main'); ?>">
	<?php
	wp_nav_menu(array(
		'theme_location' => 'main-menu-2026',
		'container'      => false,
		'menu_class'     => 'main-nav__menu',
		'depth'          => 2,
		'fallback_cb'    => false,
	));

	if ($button) {
		get_template_part('components-2026/partials/button', null, array(
			'link'  => $button,
			'class' => 'main-nav__button',
		));
	}
	?>
</nav>
