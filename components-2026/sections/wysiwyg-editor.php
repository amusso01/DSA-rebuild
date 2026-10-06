<?php
/**
 * Section: WYSIWYG editor (layout wysiwyg_editor in Dynamic Layout 2026).
 * Free editor content (headings, text, lists) in a default or narrow container.
 * Alignment comes from the editor itself. Rendered by dsa_2026_render_sections().
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'container' => 'default', // default: .content-max width; narrow: .content-narrow inside it
	'content'   => '',        // WYSIWYG
	'index'     => 0,
));

if (!$args['content']) {
	return;
}

$classes = 'wysiwyg-editor__content' . ($args['container'] === 'narrow' ? ' content-narrow' : '');
?>
<section class="wysiwyg-editor content-block">
	<div class="content-max">
		<div class="<?php echo esc_attr($classes); ?>"><?php echo wp_kses_post($args['content']); ?></div>
	</div>
</section>
