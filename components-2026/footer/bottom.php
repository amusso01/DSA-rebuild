<?php
/**
 * Footer: bottom bar, copyright + legal menu (Footer Legal 2026).
 *
 * @package FDRY
 */
?>
<p class="footer-bottom__copy">&copy; <?php echo esc_html(wp_date('Y')); ?> DSA Connect. All rights reserved. Registered in England &amp; Wales.</p>
<?php if (has_nav_menu('footer-legal-2026')) : ?>
	<nav aria-label="<?php esc_attr_e('Legal'); ?>">
		<?php
		wp_nav_menu(array(
			'theme_location' => 'footer-legal-2026',
			'container'      => false,
			'menu_class'     => 'footer-legal',
			'depth'          => 1,
			'fallback_cb'    => false,
		));
		?>
	</nav>
<?php endif; ?>
