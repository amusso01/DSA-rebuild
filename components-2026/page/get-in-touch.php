<?php
/**
 * Page: get in touch (Contact 2026, ACF group field "get_in_touch").
 * Centred H2 and text, then a Call card and an Email card (icon tile, label, value).
 * Figma: DSA, node 223:3151 ("Contact details section").
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'title'   => '', // <span class="accent"> highlights
	'content' => '', // WYSIWYG
	'phone'   => '', // shown as typed; the tel: link keeps only digits and +
	'email'   => '',
));

$cards = array();

if (preg_match('/[0-9]/', (string) $args['phone'])) {
	$cards[] = array(
		'icon'  => 'phone',
		'label' => __('Call'),
		'url'   => 'tel:' . preg_replace('/[^0-9+]/', '', $args['phone']),
		'value' => $args['phone'],
	);
}

$email = sanitize_email((string) $args['email']);

if (is_email($email)) {
	// antispambot(): the address is entity-encoded in the source, the browser shows it as normal
	$cards[] = array(
		'icon'  => 'mail',
		'label' => __('Email'),
		'url'   => 'mailto:' . antispambot($email),
		'value' => antispambot($email),
	);
}

$has_intro = $args['title'] || $args['content'];

if (!$has_intro && !$cards) {
	return;
}
?>
<section class="get-in-touch content-block">
	<div class="content-max">
		<div class="get-in-touch__inner">
			<?php if ($has_intro) : ?>
				<div class="get-in-touch__intro" data-reveal="fade-up">
					<?php if ($args['title']) : ?>
						<h2 class="get-in-touch__title h3"><?php echo wp_kses($args['title'], array('span' => array('class' => true))); ?></h2>
					<?php endif; ?>
					<?php if ($args['content']) : ?>
						<div class="get-in-touch__text"><?php echo wp_kses_post($args['content']); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ($cards) : ?>
				<ul class="get-in-touch__cards" data-reveal="fade-up">
					<?php foreach ($cards as $card) : ?>
						<li class="get-in-touch__card">
							<a class="get-in-touch__link" href="<?php echo esc_url($card['url']); ?>">
								<span class="get-in-touch__icon" aria-hidden="true"><?php get_template_part('svg-templates/svg-' . $card['icon']); ?></span>
								<span class="get-in-touch__detail">
									<span class="get-in-touch__label"><?php echo esc_html($card['label']); ?></span>
									<span class="get-in-touch__value"><?php echo esc_html($card['value']); ?></span>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
