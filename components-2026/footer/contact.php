<?php
/**
 * Footer: "Get in Touch" column (map, email, phone).
 * Link fields footer_map / footer_email / footer_phone on Options > Footer
 * (acf-json "Footer 2026"). The link text is shown; empty links are hidden.
 *
 * @package FDRY
 */

if (!function_exists('dsa_2026_footer_contact_href')) {
	/**
	 * @param string               $icon map-pin|mail|phone
	 * @param array<string, mixed> $link ACF link array (url, title, target).
	 */
	function dsa_2026_footer_contact_href(string $icon, array $link): string
	{
		$url = (string) ($link['url'] ?? '');
		$title = (string) ($link['title'] ?? '');

		if ($icon === 'map-pin') {
			return $url;
		}

		if ($icon === 'mail') {
			$raw = preg_replace('#^(mailto:|https?://)#i', '', $url);
			if ($raw === $url && $title !== '') {
				$raw = preg_replace('#^(mailto:|https?://)#i', '', $title);
			}
			$email = sanitize_email($raw);
			if (!is_email($email)) {
				return '';
			}

			return 'mailto:' . antispambot($email);
		}

		if ($icon === 'phone') {
			$raw = preg_replace('#^(tel:|https?://)#i', '', $url);
			if (!preg_match('/[0-9]/', $raw) && $title !== '') {
				$raw = preg_replace('#^(tel:|https?://)#i', '', $title);
			}
			if (!preg_match('/[0-9]/', $raw)) {
				return '';
			}

			return 'tel:' . preg_replace('/[^0-9+]/', '', $raw);
		}

		return $url;
	}
}

$contacts = array(
	'map-pin' => dsa_2026_option('footer_map'),
	'mail'    => dsa_2026_option('footer_email'),
	'phone'   => dsa_2026_option('footer_phone'),
);

$contacts = array_filter($contacts, function ($link, $icon) {
	if (empty($link['url'])) {
		return false;
	}

	return dsa_2026_footer_contact_href($icon, $link) !== '';
}, ARRAY_FILTER_USE_BOTH);

if (!$contacts) {
	return;
}
?>
<div class="footer-col">
	<h2 class="footer-col__title"><?php esc_html_e('Get in Touch'); ?></h2>
	<ul class="footer-contact">
		<?php foreach ($contacts as $icon => $link) :
			$href = dsa_2026_footer_contact_href($icon, $link);
			?>
			<li>
				<a class="footer-contact__link" href="<?php echo esc_url($href); ?>"<?php if ($icon === 'map-pin') : ?> target="_blank" rel="noopener noreferrer"<?php elseif (($link['target'] ?? '') === '_blank') : ?> target="_blank" rel="noopener"<?php endif; ?>>
					<span class="footer-contact__icon" aria-hidden="true"><?php get_template_part('svg-templates/svg-' . $icon); ?></span>
					<span><?php echo esc_html($link['title'] ?: preg_replace('#^(mailto:|tel:|https?://)#', '', $link['url'])); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
