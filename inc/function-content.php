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
