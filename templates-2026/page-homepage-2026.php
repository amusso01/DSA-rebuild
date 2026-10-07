<?php

/**
 * Template Name: Homepage 2026
 *
 * Homepage built from sections: ACF flexible content page_sections (group "Dynamic Layout 2026"),
 * the same field as the Dynamic Layout 2026 template, in its own template so the homepage can change alone.
 * Test it on a private page. At go-live front-page-new.php (which loads this file) becomes front-page.php.
 * No editor on these pages (inc/function-layout.php). The page is identified by .site-main--homepage.
 *
 * @package FDRY
 */

get_header('new'); ?>

<main id="main" class="site-main site-main--homepage" role="main">
	<?php
	while (have_posts()) : the_post();
		dsa_2026_render_sections();
	endwhile;
	?>
</main>

<?php get_footer('new'); ?>
