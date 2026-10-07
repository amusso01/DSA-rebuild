<?php
/**
 * Section: certification showcase (layout certification_showcase in Dynamic Layout 2026).
 * Accent lines, H2 title and text on the left; up to 3 certification images stacked on the right.
 * The images already include their tile background: they're shown as they are. Rendered by dsa_2026_render_sections().
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'padding_top'    => '',      // slider, desktop px (0–100); empty: 54px from .certification-showcase
	'padding_bottom' => '',
	'title'          => '',      // textarea (new lines → <br>); <span class="accent"> highlights
	'content'        => '',      // WYSIWYG; its headings are the bold closing line
	'images'         => array(), // repeater rows: image (ID)
	'index'          => 0,
));

$images = array_filter((array) $args['images'], function ($row) {
	return !empty($row['image']);
});

if (!$args['title'] && !$args['content'] && !$images) {
	return;
}

$style = dsa_2026_section_padding_style($args['padding_top'], $args['padding_bottom']);
?>
<section class="certification-showcase content-block section-padding"<?php if ($style) : ?> style="<?php echo esc_attr($style); ?>"<?php endif; ?>>
	<div class="content-max">
		<div class="certification-showcase__inner">
			<?php if ($args['title'] || $args['content']) : ?>
				<div class="certification-showcase__copy" data-reveal="fade-up">
					<?php if ($args['title']) : ?>
						<span class="certification-showcase__lines" aria-hidden="true"></span>
						<h2 class="certification-showcase__title"><?php echo wp_kses($args['title'], array('span' => array('class' => true), 'br' => array())); ?></h2>
					<?php endif; ?>
					<?php if ($args['content']) : ?>
						<div class="certification-showcase__text"><?php echo wp_kses_post($args['content']); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ($images) : ?>
				<ul class="certification-showcase__images" data-reveal="fade-up" aria-label="<?php esc_attr_e('Certifications'); ?>">
					<?php foreach ($images as $row) : ?>
						<li class="certification-showcase__item">
							<?php echo wp_get_attachment_image($row['image'], 'medium', false, array(
								'class'   => 'certification-showcase__img',
								'sizes'   => '215px',
								'loading' => $args['index'] === 0 ? 'eager' : 'lazy',
							)); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
