<?php

/**
 * Template Name: Dynamic Layout 2026
 *
 * Page built from sections: ACF flexible content page_sections (group "Dynamic Layout 2026").
 * No editor on these pages (inc/function-layout.php). Each section is a component in
 * components-2026/sections/ with its own .content-block > .content-max wrappers.
 *
 * @package FDRY
 */

get_header('new'); ?>

<main id="main" class="site-main site-main--layout" role="main">
	<?php
	while (have_posts()) : the_post();
		dsa_2026_render_sections();
	endwhile;
	?>
</main>

<?php get_footer('new'); ?>