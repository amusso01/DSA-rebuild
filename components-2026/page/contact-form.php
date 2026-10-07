<?php
/**
 * Page: contact form (Contact 2026, ACF group field "contact_form").
 * Centred H2 and text, then the card: the progress steps and the Contact Form 7 form.
 * The form markup comes from CF7 ("Multi Step 2026") and the Multi Step plugin's fieldsets;
 * styles in _contact-form.scss, step sync in src-2026/js/modules/contactForm.js.
 * Figma: DSA, node 223:3175 ("Contact flow").
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'title'   => '', // <span class="accent"> highlights
	'content' => '', // WYSIWYG
	'form'    => 0,  // CF7 form ID (ACF select)
));

// The free Multi Step plugin can't rename its steps (it shows "Step 1/2/3"), so the labels live here.
// Keep this list in the form's step order.
$steps = array(__('Service'), __('Sector'), __('Details'));

$form_id   = absint($args['form']);
$has_form  = $form_id && shortcode_exists('contact-form-7') && get_post_type($form_id) === 'wpcf7_contact_form';
$has_intro = $args['title'] || $args['content'];

if (!$has_intro && !$has_form) {
	return;
}
?>
<section class="contact-form content-block">
	<div class="content-max">
		<div class="contact-form__inner">
			<?php if ($has_intro) : ?>
				<div class="contact-form__intro" data-reveal="fade-up">
					<?php if ($args['title']) : ?>
						<h2 class="contact-form__title h3"><?php echo wp_kses($args['title'], array('span' => array('class' => true))); ?></h2>
					<?php endif; ?>
					<?php if ($args['content']) : ?>
						<div class="contact-form__text"><?php echo wp_kses_post($args['content']); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ($has_form) : ?>
				<div class="contact-form__card" data-reveal="fade-up">
					<ol class="contact-form__steps" aria-label="<?php esc_attr_e('Form steps'); ?>">
						<?php foreach ($steps as $i => $label) : ?>
							<li class="contact-form__step<?php echo $i === 0 ? ' is-current' : ''; ?>"<?php if ($i === 0) : ?> aria-current="step"<?php endif; ?>><?php echo esc_html($label); ?></li>
						<?php endforeach; ?>
					</ol>
					<?php echo do_shortcode('[contact-form-7 id="' . $form_id . '"]'); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
