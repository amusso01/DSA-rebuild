<?php
/**
 * Section: service cards (layout service_cards in Dynamic Layout 2026).
 * Optional H2 title, then a 2-column grid of notched cards: title, text and a link.
 * The card's link covers the whole card: one link per card. Rendered by dsa_2026_render_sections().
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'padding_top'    => '',      // slider, desktop px (0–150); empty: 104px from .service-cards
	'padding_bottom' => '',
	'title'          => '',      // textarea (new lines → <br>); <span class="accent"> highlights
	'cards'          => array(), // repeater rows: title, content (textarea, <br>), link (ACF Link array)
	'index'          => 0,
));

// a row with neither title nor text would be an empty box
$cards = array_filter((array) $args['cards'], function ($card) {
	return !empty($card['title']) || !empty($card['content']);
});

if (!$cards) {
	return;
}

$style = dsa_2026_section_padding_style($args['padding_top'], $args['padding_bottom'], 150);
?>
<section class="service-cards content-block section-padding"<?php if ($style) : ?> style="<?php echo esc_attr($style); ?>"<?php endif; ?>>
	<div class="content-max">
		<?php if ($args['title']) : ?>
			<div class="service-cards__heading" data-reveal="fade-up">
				<h2 class="service-cards__title"><?php echo wp_kses($args['title'], array('span' => array('class' => true), 'br' => array())); ?></h2>
				<span class="service-cards__lines" aria-hidden="true"></span>
			</div>
		<?php endif; ?>
		<ul class="service-cards__list">
			<?php foreach ($cards as $card) :
				$link = !empty($card['link']['url']) ? $card['link'] : null;
			?>
				<li class="service-card" data-reveal="fade-up">
					<?php if (!empty($card['title'])) : ?>
						<h3 class="service-card__title h5"><?php echo esc_html($card['title']); ?></h3>
					<?php endif; ?>
					<?php if (!empty($card['content'])) : ?>
						<p class="service-card__text"><?php echo wp_kses($card['content'], array('br' => array())); ?></p>
					<?php endif; ?>
					<?php if ($link) : ?>
						<a class="service-card__link" href="<?php echo esc_url($link['url']); ?>"<?php if (($link['target'] ?? '') === '_blank') : ?> target="_blank" rel="noopener"<?php endif; ?>>
							<span><?php echo esc_html(!empty($link['title']) ? $link['title'] : __('Read more')); ?></span>
							<span class="service-card__arrow" aria-hidden="true"><?php get_template_part('svg-templates/svg-arrow-right'); ?></span>
						</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
