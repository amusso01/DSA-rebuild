<?php
/**
 * Template Name: 2026 Layout (preview)
 *
 * The 2026 layout: header-new.php + dist-2026 assets, legacy assets dequeued.
 * Assign it to a private page while in progress, so only logged-in editors see it.
 * Wrappers go per section (.content-block > .content-max), never on <main>.
 *
 * @package FDRY
 */

get_header('new'); ?>

<main id="main" class="site-main" role="main">
	<section class="content-block">
		<div class="content-max">
			<?php
			while (have_posts()) : the_post();
				the_content();
			endwhile;
			?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
