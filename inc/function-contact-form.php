<?php
/**
 * 2026 layout: Contact Form 7 + "Multi Step for Contact Form 7" (free) on 2026 pages.
 * Used by the Contact 2026 "Contact form" tab (components-2026/page/contact-form.php).
 * The legacy multi-step layout ([progressbar], dist/scripts/main.js, _form.scss) never loads here.
 *
 * @package FDRY
 */

/*==================================================================================
  ACF: FORM PICKER
==================================================================================*/
// "Form" select in the Contact 2026 group (acf-json group_6ac50cfa10bbb): every CF7 form, A–Z.
// A select, not a post object: CF7's post type isn't public and may not be listed there.
add_filter('acf/load_field/key=field_6ac519eb20123', 'dsa_2026_contact_form_choices');

function dsa_2026_contact_form_choices($field)
{
	$field['choices'] = array();

	$forms = get_posts(array(
		'post_type'   => 'wpcf7_contact_form',
		'numberposts' => -1,
		'orderby'     => 'title',
		'order'       => 'ASC',
	));

	foreach ($forms as $form) {
		$field['choices'][$form->ID] = $form->post_title;
	}

	return $field;
}

/*==================================================================================
  2026 PAGES
==================================================================================*/
// get_header fires before header-new.php runs wp_head(), so before the plugins enqueue.
add_action('get_header', 'dsa_2026_contact_form_setup');

function dsa_2026_contact_form_setup($name)
{
	if ($name !== 'new') {
		return;
	}
	// The multi-step plugin's CSS (floats, button colours, sliding fieldsets) fights the 2026
	// layout: _contact-form.scss replaces it. CF7's own CSS stays (screen reader text, spinner).
	add_filter('is_using_cf7mls_css', '__return_false');
	// No automatic <p> and <br> in the form: the markup is exactly the form template.
	add_filter('wpcf7_autop_or_not', '__return_false');
}
