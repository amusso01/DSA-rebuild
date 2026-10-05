<?php
/**
 * 2026 layout: "Dynamic Layout 2026" template (templates-2026/page-layout-2026.php).
 * Pages on it are built only from the ACF flexible content field page_sections:
 * the editor is removed, and each row renders components-2026/sections/<layout>.php.
 *
 * @package FDRY
 */

/*==================================================================================
  TEMPLATE
==================================================================================*/
// True when the page uses the Dynamic Layout 2026 template (the only place its path is written).
function dsa_2026_is_layout_page($post = null)
{
	return get_page_template_slug($post) === 'templates-2026/page-layout-2026.php';
}

/*==================================================================================
  NO EDITOR
==================================================================================*/
// Block editor off: the page opens on the classic screen, with only the ACF fields.
// The switch shows after a reload: pick the template, save, then reload.
add_filter('use_block_editor_for_post', 'dsa_2026_layout_no_block_editor', 10, 2);

function dsa_2026_layout_no_block_editor($use_block_editor, $post)
{
	return dsa_2026_is_layout_page($post) ? false : $use_block_editor;
}

// Classic content box off too. load-post.php fires before post.php prints the edit screen,
// and only for this request. The post_content stays in the database, just hidden.
add_action('load-post.php', 'dsa_2026_layout_no_classic_editor');

function dsa_2026_layout_no_classic_editor()
{
	$post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;

	if ($post_id && dsa_2026_is_layout_page($post_id)) {
		remove_post_type_support('page', 'editor');
	}
}

/*==================================================================================
  SECTIONS
==================================================================================*/
// Render every row of page_sections, in order. Call it inside the loop.
// Layout name → component: two_columns → components-2026/sections/two-columns.php.
// The component gets the row in $args: its sub fields by name, plus 'index' (0 = first section).
function dsa_2026_render_sections()
{
	// the_content() would show the password form: without this the sections would be public
	if (post_password_required()) {
		echo '<section class="content-block"><div class="content-max">' . get_the_password_form() . '</div></section>';
		return;
	}

	$rows = function_exists('get_field') ? get_field('page_sections') : null;

	if (!$rows) {
		return;
	}

	foreach ($rows as $index => $row) {
		$layout = $row['acf_fc_layout'];
		$args   = array_merge($row, array('index' => $index));

		// get_template_part() returns false when the component file doesn't exist (yet)
		if (get_template_part('components-2026/sections/' . str_replace('_', '-', $layout), null, $args) === false && current_user_can('edit_pages')) {
			echo '<!-- dsa-2026: no component for section "' . esc_html($layout) . '" -->';
		}
	}
}
