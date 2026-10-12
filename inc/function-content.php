<?php
/**
 * 2026 layout: editor content (the_content()) on 2026 pages.
 * Blog posts and pages (single.php, page.php) print their Gutenberg content as it is: once they
 * call get_header('new'), it's cleaned up here and styled by src-2026/scss/pages/_singular.scss.
 *
 * @package FDRY
 */

/*==================================================================================
  2026 PAGES
==================================================================================*/
// get_header fires before the template's loop, so the filter is in place for the_content().
add_action('get_header', 'dsa_2026_content_setup');

function dsa_2026_content_setup($name)
{
	if ($name !== 'new') {
		return;
	}
	add_filter('the_content', 'dsa_2026_strip_content_meta');
}

// <meta charset="utf-8"> pasted from Google Docs is never content. A heading holding only meta
// would print an empty h4 with its accent bar: stripped, the block is empty and CSS :empty hides it.
function dsa_2026_strip_content_meta($content)
{
	return preg_replace('/<meta\b[^>]*>/i', '', $content);
}

/*==================================================================================
  ACF WYSIWYG FIELDS
==================================================================================*/
// Print an ACF WYSIWYG value with only what the editor's own buttons make: paragraphs, headings,
// bold/italic/strikethrough, links, lists, quotes, lines, and text-align (how editors centre text).
// Pasted formatting (spans, inline styles, colours, b/i wrappers, classes, ids) is dropped, its text kept,
// so content saved before this is cleaned too. Use it instead of wp_kses_post() for 2026 WYSIWYG output.
function dsa_2026_editor_html($html, $allow_images = false)
{
	$align   = array('style' => true); // only text-align survives (dsa_2026_editor_styles)
	$allowed = array(
		'p'          => $align,
		'h1'         => $align,
		'h2'         => $align,
		'h3'         => $align,
		'h4'         => $align,
		'h5'         => $align,
		'h6'         => $align,
		'li'         => $align,
		'ul'         => array(),
		'ol'         => array(),
		'blockquote' => array(),
		'br'         => array(),
		'hr'         => array(),
		'strong'     => array(),
		'em'         => array(),
		'del'        => array(),
		'a'          => array('href' => true, 'target' => true, 'rel' => true),
	);

	if ($allow_images) {
		$allowed['img'] = array(
			'src'    => true,
			'alt'    => true,
			'width'  => true,
			'height' => true,
			'srcset' => true,
			'sizes'  => true,
		);
	}

	add_filter('safe_style_css', 'dsa_2026_editor_styles');
	$html = wp_kses($html, $allowed);
	remove_filter('safe_style_css', 'dsa_2026_editor_styles');

	return $html;
}

// The only inline style kept in editor content: the alignment buttons' text-align.
function dsa_2026_editor_styles()
{
	return array('text-align');
}
