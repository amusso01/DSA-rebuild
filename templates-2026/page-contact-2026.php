<?php

/**
 * Template Name: Contact 2026
 *
 * Contact page in the 2026 layout: same structure as page-2026.php, main#contact.
 * Wrappers go per section (.content-block > .content-max), never on <main>.
 *
 * @package FDRY
 */

get_header('new'); ?>

<main id="contact" class="site-main" role="main">
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

<?php get_footer(); ?>