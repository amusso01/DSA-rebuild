<?php

/**
 * Template Name: Contact 2026
 *
 * Contact page in the 2026 layout.
 * Every <main> keeps id="main" (skip-link target); the page is identified by .site-main--contact.
 * Wrappers go per section (.content-block > .content-max), never on <main>.
 *
 * @package FDRY
 */

get_header('new'); ?>

<main id="main" class="site-main site-main--contact" role="main">
	<section class="content-block">
		<div class="content-narrow">
			<?php
			while (have_posts()) : the_post();
				the_content();
			endwhile;
			?>
		</div>
	</section>
</main>

<?php get_footer('new'); ?>