<?php
/**
 * Footer: social icons.
 * Link fields footer_linkedin / footer_facebook / footer_instagram on Options > Footer
 * (acf-json "Footer 2026"). Icons from svg-templates/svg-{network}.php; empty links are hidden.
 *
 * @package FDRY
 */

$networks = array(
	'linkedin'  => array('field' => 'footer_linkedin', 'label' => 'LinkedIn'),
	'facebook'  => array('field' => 'footer_facebook', 'label' => 'Facebook'),
	'instagram' => array('field' => 'footer_instagram', 'label' => 'Instagram'),
);

$links = array();
foreach ($networks as $icon => $network) {
	$link = dsa_2026_option($network['field']);
	if (!empty($link['url'])) {
		$links[$icon] = array('link' => $link, 'label' => $network['label']);
	}
}

if (!$links) {
	return;
}
?>
<ul class="footer-social">
	<?php foreach ($links as $icon => $item) : ?>
		<li>
			<a class="footer-social__link" href="<?php echo esc_url($item['link']['url']); ?>" aria-label="<?php echo esc_attr($item['label']); ?>"<?php if (($item['link']['target'] ?? '') === '_blank') : ?> target="_blank" rel="noopener"<?php endif; ?>>
				<?php get_template_part('svg-templates/svg-' . $icon); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
