<?php
/**
 * Footer: certification logos strip above the footer.
 * Repeater "footer_certifications" on Options > Footer (acf-json "Footer 2026").
 * Renders nothing until a logo is added.
 *
 * @package FDRY
 */

$certifications = dsa_2026_option('footer_certifications');
if (empty($certifications)) {
	return;
}
?>
<section class="footer-partners content-block" aria-label="<?php esc_attr_e('Certifications'); ?>">
	<div class="content-max">
		<ul class="footer-partners__list">
			<?php foreach ($certifications as $row) : ?>
				<?php if (!empty($row['image'])) : ?>
					<li class="footer-partners__item">
						<?php echo wp_get_attachment_image($row['image'], 'medium', false, array('class' => 'footer-partners__img', 'loading' => 'lazy')); ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
