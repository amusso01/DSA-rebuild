<?php
/**
 * Component: button (.btn). Reusable across the 2026 layout.
 *
 * get_template_part('components-2026/partials/button', null, array(
 *     'link'  => get_field('my_link'),   // ACF Link array (url/title/target), or…
 *     'url'   => '', 'label' => '', 'target' => '',
 *     'class' => '',                       // extra classes, e.g. a modifier
 * ));
 *
 * Renders nothing without a url and a label.
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'link'   => null,
	'url'    => '',
	'label'  => '',
	'target' => '',
	'class'  => '',
));

if (is_array($args['link'])) {
	$args['url']    = $args['link']['url'] ?? '';
	$args['label']  = $args['link']['title'] ?? '';
	$args['target'] = $args['link']['target'] ?? '';
}

if (!$args['url'] || !$args['label']) {
	return;
}

$classes = trim('btn ' . $args['class']);
?>
<a class="<?php echo esc_attr($classes); ?>" href="<?php echo esc_url($args['url']); ?>"<?php if ($args['target'] === '_blank') : ?> target="_blank" rel="noopener"<?php endif; ?>><?php echo esc_html($args['label']); ?></a>
