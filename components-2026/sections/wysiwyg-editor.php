<?php
/**
 * Section: WYSIWYG editor (layout wysiwyg_editor in Dynamic Layout 2026).
 * Free editor content (headings, text, lists) in a default or narrow container.
 * Alignment comes from the editor itself. Rendered by dsa_2026_render_sections().
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'padding_top'    => '',        // slider, desktop px; empty: 76px from .section-padding
	'padding_bottom' => '',
	'container'      => 'default', // default: .content-max width; narrow: .content-narrow inside it
	'content'        => '',        // WYSIWYG
	'index'          => 0,
));

if (!$args['content']) {
	return;
}

$style   = dsa_2026_section_padding_style($args['padding_top'], $args['padding_bottom']);
$classes = 'wysiwyg-editor__content' . ($args['container'] === 'narrow' ? ' content-narrow' : '');
?>
<section class="wysiwyg-editor content-block section-padding"<?php if ($style) : ?> style="<?php echo esc_attr($style); ?>"<?php endif; ?>>
	<div class="content-max">
		<div class="<?php echo esc_attr($classes); ?>" data-reveal="fade-up"><?php echo wp_kses_post($args['content']); ?></div>
	</div>
</section>
