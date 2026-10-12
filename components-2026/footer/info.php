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
<div class="footer-info"><?php echo dsa_2026_editor_html($info); ?></div>
