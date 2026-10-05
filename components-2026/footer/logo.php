<?php
/**
 * Footer: logo from Appearance > Customize > Site Identity > Logo (same as the header).
 * Falls back to the site name until a logo is set.
 *
 * @package FDRY
 */

if (has_custom_logo()) {
	the_custom_logo();
} else {
	?>
	<a class="footer-logo__text" href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
	<?php
}
