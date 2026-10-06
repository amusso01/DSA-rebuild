<?php
/**
 * Section: two-column image text (layout two_column_image_text in Dynamic Layout 2026).
 * Image + content (eyebrow, H2 title, text, optional button) side by side.
 * Options: background colour, text colour, reverse (content first). Rendered by dsa_2026_render_sections().
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'background_color' => '',      // hex; empty or invalid: $color__main from the SCSS
	'text_color'       => 'light', // light | dark (dark: for light backgrounds)
	'grid_reverse'     => false,   // true: content, then image
	'eyebrow'          => '',
	'title'            => '',      // textarea (new lines → <br>); <span class="accent"> highlights
	'content'          => '',      // WYSIWYG
	'link'             => null,    // ACF Link array → button partial
	'image'            => 0,       // image ID
	'index'            => 0,
));

$bg      = sanitize_hex_color($args['background_color']);
$classes = 'two-column-image-text content-block'
	. ($args['grid_reverse'] ? ' two-column-image-text--reverse' : '')
	. ($args['text_color'] === 'dark' ? ' two-column-image-text--dark' : '');
?>
<section class="<?php echo esc_attr($classes); ?>"<?php if ($bg) : ?> style="background-color: <?php echo esc_attr($bg); ?>"<?php endif; ?>>
	<div class="content-max">
		<div class="two-column-image-text__inner">
			<?php if ($args['image']) : ?>
				<div class="two-column-image-text__media">
					<?php echo wp_get_attachment_image($args['image'], 'large', false, array(
						'class'   => 'two-column-image-text__img',
						'sizes'   => '(max-width: 920px) 100vw, 50vw',
						'loading' => $args['index'] === 0 ? 'eager' : 'lazy',
					)); ?>
				</div>
			<?php endif; ?>
			<div class="two-column-image-text__content">
				<?php if ($args['eyebrow']) : ?>
					<p class="two-column-image-text__eyebrow"><?php echo esc_html($args['eyebrow']); ?></p>
				<?php endif; ?>
				<?php if ($args['title']) : ?>
					<h2 class="two-column-image-text__title"><?php echo wp_kses($args['title'], array('span' => array('class' => true), 'br' => array())); ?></h2>
				<?php endif; ?>
				<?php if ($args['content']) : ?>
					<div class="two-column-image-text__text"><?php echo wp_kses_post($args['content']); ?></div>
				<?php endif; ?>
				<?php get_template_part('components-2026/partials/button', null, array(
					'link'  => $args['link'],
					'class' => 'two-column-image-text__button',
				)); ?>
			</div>
		</div>
	</div>
</section>
