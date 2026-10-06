<?php
/**
 * 2026 layout: template helpers.
 *
 * @package FDRY
 */

/*==================================================================================
  SVG
==================================================================================*/
// Return an svg-templates/svg-{name}.php as a string, for places that need markup
// as a value (e.g. menu filters). In templates just call get_template_part() directly.
function dsa_2026_get_svg($name)
{
	ob_start();
	get_template_part('svg-templates/svg-' . $name);
	return ob_get_clean();
}

/*==================================================================================
  PASSWORD
==================================================================================*/
// Password-protected page, password not entered yet: print the form (as the_content() would)
// and return true, so the template skips its sections. Otherwise they'd be public.
function dsa_2026_password_gate()
{
	if (!post_password_required()) {
		return false;
	}

	echo '<section class="content-block"><div class="content-max">' . get_the_password_form() . '</div></section>';
	return true;
}
