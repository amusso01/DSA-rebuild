<?php
/**
 * Section: blog row (layout blog_row in Dynamic Layout 2026).
 * Show all: the latest posts, 9 at a time, with the "Showing x of y" count and Load more
 * (src-2026/js/modules/blogRow.js + the REST route in inc/function-blog.php).
 * Otherwise: only the picked posts, in order. Rendered by dsa_2026_render_sections().
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'padding_top'    => '',      // slider, desktop px; empty: 80px from .blog-row
	'padding_bottom' => '',      // empty: 62px
	'show_all'       => true,
	'posts'          => array(), // post IDs, used when show_all is off
	'title'          => '',
	'index'          => 0,
));

$show_all = (bool) $args['show_all'];
$query    = dsa_2026_blog_query(1, $show_all ? null : (array) $args['posts']);

if (!$query->have_posts()) {
	return;
}

$style = dsa_2026_section_padding_style($args['padding_top'], $args['padding_bottom']);
?>
<section class="blog-row content-block section-padding"<?php if ($style) : ?> style="<?php echo esc_attr($style); ?>"<?php endif; ?><?php if ($show_all) : ?> data-blog-rest="<?php echo esc_url(rest_url('dsa-2026/v1/blog')); ?>"<?php endif; ?>>
	<div class="content-max">
		<?php if ($args['title'] || $show_all) : ?>
			<div class="blog-row__heading" data-reveal="fade-up">
				<?php if ($args['title']) : ?>
					<div class="blog-row__heading-group">
						<h2 class="blog-row__title"><?php echo esc_html($args['title']); ?></h2>
						<span class="blog-row__lines" aria-hidden="true"></span>
					</div>
				<?php endif; ?>
				<?php if ($show_all) : ?>
					<p class="blog-row__count" aria-live="polite"><?php printf(
						esc_html__('Showing %1$s of %2$s insights'),
						'<span class="blog-row__shown">' . esc_html($query->post_count) . '</span>',
						esc_html($query->found_posts)
					); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="blog-row__grid"><?php echo dsa_2026_blog_cards($query); ?></div>
		<?php if ($show_all && $query->max_num_pages > 1) : ?>
			<div class="blog-row__more">
				<button class="btn blog-row__load" type="button" data-page="1"><span class="blog-row__load-label"><?php esc_html_e('Load more'); ?></span></button>
			</div>
		<?php endif; ?>
	</div>
</section>
