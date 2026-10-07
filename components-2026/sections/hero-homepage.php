<?php
/**
 * Section: hero homepage (layout hero_homepage in Dynamic Layout 2026).
 * The two accent lines (optional) above the H1 title, text and up to two buttons (filled + outline),
 * optional background image over $color__main. Options: padding sliders, show title highlight.
 * Rendered by dsa_2026_render_sections().
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'padding_top'      => '',    // slider, desktop px (0–100); empty: 72px from .hero-homepage
	'padding_bottom'   => '',    // empty: 82px
	'show_highlight'   => true,  // the two accent lines above the title
	'title'            => '',    // empty: the page title; <span class="accent"> highlights
	'content'          => '',    // WYSIWYG
	'button_primary'   => null,  // ACF Link array → filled button
	'button_secondary' => null,  // ACF Link array → outline button
	'image'            => 0,     // image ID
	'index'            => 0,
));

$title       = $args['title'] ?: get_the_title();
$style       = dsa_2026_section_padding_style($args['padding_top'], $args['padding_bottom']);
$has_buttons = !empty($args['button_primary']['url']) || !empty($args['button_secondary']['url']);

// the first section is above the fold: load its image right away
$first = $args['index'] === 0;
?>
<section class="hero-homepage content-block section-padding"<?php if ($style) : ?> style="<?php echo esc_attr($style); ?>"<?php endif; ?>>
	<?php if ($args['image']) : ?>
		<?php echo wp_get_attachment_image($args['image'], 'full', false, array(
			'class'         => 'hero-homepage__image',
			'alt'           => '',
			'sizes'         => '100vw',
			'loading'       => $first ? 'eager' : 'lazy',
			'fetchpriority' => $first ? 'high' : 'auto',
		)); ?>
	<?php endif; ?>
	<div class="content-max">
		<div class="hero-homepage__copy" data-reveal="fade-up">
			<?php if ($args['show_highlight']) : ?>
				<span class="hero-homepage__lines" aria-hidden="true"></span>
			<?php endif; ?>
			<h1 class="hero-homepage__title"><?php echo wp_kses($title, array('span' => array('class' => true))); ?></h1>
			<?php if ($args['content']) : ?>
				<div class="hero-homepage__text"><?php echo wp_kses_post($args['content']); ?></div>
			<?php endif; ?>
			<?php if ($has_buttons) : ?>
				<div class="hero-homepage__buttons">
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
</section>
