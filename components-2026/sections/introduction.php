<?php
/**
 * Section: introduction (layout introduction in Dynamic Layout 2026).
 * H2 title on the left; text and up to two buttons (filled + outline) on the right.
 * Options: background colour, text colour. Rendered by dsa_2026_render_sections().
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'background_color' => '',     // hex; empty or invalid: $color__text-light from the SCSS
	'text_color'       => 'dark', // dark | light (light: for dark backgrounds)
	'title'            => '',     // textarea (new lines → <br>); <span class="accent"> highlights
	'content'          => '',     // WYSIWYG
	'button_primary'   => null,   // ACF Link array → filled button
	'button_secondary' => null,   // ACF Link array → outline button
	'index'            => 0,
));

// the title holds the left column: without it the layout falls apart
if (!$args['title']) {
	return;
}

$bg          = sanitize_hex_color($args['background_color']);
$classes     = 'introduction content-block' . ($args['text_color'] === 'light' ? ' introduction--light' : '');
$has_buttons = !empty($args['button_primary']['url']) || !empty($args['button_secondary']['url']);
?>
<section class="<?php echo esc_attr($classes); ?>"<?php if ($bg) : ?> style="background-color: <?php echo esc_attr($bg); ?>"<?php endif; ?>>
	<div class="content-max">
		<div class="introduction__inner">
			<h2 class="introduction__title" data-reveal="fade-up"><?php echo wp_kses($args['title'], array('span' => array('class' => true), 'br' => array())); ?></h2>
			<div class="introduction__summary" data-reveal="fade-up">
				<?php if ($args['content']) : ?>
					<div class="introduction__text"><?php echo wp_kses_post($args['content']); ?></div>
				<?php endif; ?>
				<?php if ($has_buttons) : ?>
					<div class="introduction__buttons">
						<?php get_template_part('components-2026/partials/button', null, array(
							'link' => $args['button_primary'],
						)); ?>
						<?php get_template_part('components-2026/partials/button', null, array(
							'link'  => $args['button_secondary'],
							'class' => 'btn--outline',
						)); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
