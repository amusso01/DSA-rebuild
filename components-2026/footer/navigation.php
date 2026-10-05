<?php
/**
 * Footer: Services and Company menu columns.
 * Menus assigned to Footer Service 2026 / Footer Company 2026 (Appearance > Menus).
 * A column is skipped while its location has no menu.
 *
 * @package FDRY
 */

$columns = array(
	'footer-service-2026' => __('Services'),
	'footer-company-2026' => __('Company'),
);

foreach ($columns as $location => $title) :
	if (!has_nav_menu($location)) {
		continue;
	}
	?>
	<nav class="footer-col" aria-label="<?php echo esc_attr($title); ?>">
		<h2 class="footer-col__title"><?php echo esc_html($title); ?></h2>
		<?php
		wp_nav_menu(array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => 'footer-menu',
			'depth'          => 1,
			'fallback_cb'    => false,
		));
		?>
	</nav>
<?php endforeach; ?>
