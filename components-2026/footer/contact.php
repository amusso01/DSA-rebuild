<?php
/**
 * Footer: "Get in Touch" column (map, email, phone).
 * Link fields footer_map / footer_email / footer_phone on Options > Footer
 * (acf-json "Footer 2026"). The link text is shown; empty links are hidden.
 *
 * @package FDRY
 */

$contacts = array(
	'map-pin' => dsa_2026_option('footer_map'),
	'mail'    => dsa_2026_option('footer_email'),
	'phone'   => dsa_2026_option('footer_phone'),
);

$contacts = array_filter($contacts, function ($link) {
	return !empty($link['url']);
});

if (!$contacts) {
	return;
}
?>
<div class="footer-col">
	<h2 class="footer-col__title"><?php esc_html_e('Get in Touch'); ?></h2>
	<ul class="footer-contact">
		<?php foreach ($contacts as $icon => $link) : ?>
			<li>
				<a class="footer-contact__link" href="<?php echo esc_url($link['url']); ?>"<?php if (($link['target'] ?? '') === '_blank') : ?> target="_blank" rel="noopener"<?php endif; ?>>
					<span class="footer-contact__icon" aria-hidden="true"><?php get_template_part('svg-templates/svg-' . $icon); ?></span>
					<span><?php echo esc_html($link['title'] ?: preg_replace('#^(mailto:|tel:|https?://)#', '', $link['url'])); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
