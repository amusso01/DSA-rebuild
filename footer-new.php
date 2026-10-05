<?php

/**
 * 2026 footer (loaded by get_footer('new')).
 * Certification strip, then logo/info/social, menu columns + contact, bottom bar.
 * Partials in components-2026/footer/, content from Options > Footer (ACF) and the Footer 2026 menus.
 *
 * @package FDRY
 */

get_template_part('components-2026/footer/partner'); ?>

<footer class="dark-footer">
	<div class="content-block">
		<div class="content-max">
			<div class="footer-inner">
				<div class="footer-logo">
					<?php get_template_part('components-2026/footer/logo'); ?>
					<?php get_template_part('components-2026/footer/info'); ?>
					<?php get_template_part('components-2026/footer/social'); ?>
				</div>
				<div class="footer-navigation">
					<?php get_template_part('components-2026/footer/navigation'); ?>
					<?php get_template_part('components-2026/footer/contact'); ?>
				</div>
			</div>
			<div class="footer-bottom">
				<?php get_template_part('components-2026/footer/bottom'); ?>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
