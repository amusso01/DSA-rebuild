<?php

/**
 * Template Name: Contact 2026
 *
 * Contact page in the 2026 layout, built only from the ACF group "Contact 2026" (one tab per section).
 * The editor is removed on these pages (inc/function-layout.php).
 * Every <main> keeps id="main" (skip-link target); the page is identified by .site-main--contact.
 * Each section brings its own wrappers (.content-block > .content-max), never <main>.
 *
 * @package FDRY
 */

get_header('new'); ?>

<main id="main" class="site-main site-main--contact" role="main">
	<?php
	while (have_posts()) : the_post();
		if (dsa_2026_password_gate()) {
			continue;
		}

		$acf = function_exists('get_field');

		// Hero tab: same fields as the hero_page layout, so the same component
		get_template_part('components-2026/sections/hero-page', null, (array) ($acf ? get_field('hero') : array()));
		// Get in touch tab: title, text, phone and email cards
		get_template_part('components-2026/page/get-in-touch', null, (array) ($acf ? get_field('get_in_touch') : array()));
	endwhile;
	?>
</main>

<?php get_footer('new'); ?>
