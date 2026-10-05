<?php
/**
 * Footer: short text under the logo.
 * WYSIWYG "footer_info" on Options > Footer (acf-json "Footer 2026").
 *
 * @package FDRY
 */

$info = dsa_2026_option('footer_info');
if (!$info) {
	return;
}
?>
<div class="footer-info"><?php echo wp_kses_post($info); ?></div>
