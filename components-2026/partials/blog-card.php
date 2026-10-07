<?php
/**
 * Component: blog card, for the current post in the loop. Rendered by dsa_2026_blog_cards()
 * (Blog row section and its Load more). The title link covers the card: one link per card.
 *
 * @package FDRY
 */
?>
<article class="blog-card" data-reveal="fade-up">
	<div class="blog-card__media">
		<?php if (has_post_thumbnail()) : ?>
			<?php the_post_thumbnail('medium_large', array(
				'class'   => 'blog-card__img',
				'alt'     => '', // decorative: the title is the link text
				'sizes'   => '(max-width: 640px) 100vw, (max-width: 1140px) 50vw, 33vw',
				'loading' => 'lazy',
			)); ?>
		<?php endif; ?>
	</div>
	<div class="blog-card__content">
		<time class="blog-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('j F Y')); ?></time>
		<h3 class="blog-card__title h5"><a class="blog-card__link" href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
		<span class="blog-card__more" aria-hidden="true"><?php esc_html_e('Read now'); ?><?php get_template_part('svg-templates/svg-arrow-right'); ?></span>
	</div>
</article>
