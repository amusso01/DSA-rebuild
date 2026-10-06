<?php
/**
 * 2026 blog: the posts query and the card markup, shared by the Blog row section
 * (components-2026/sections/blog-row.php) and its "Load more" REST route.
 *
 * @package FDRY
 */

/*==================================================================================
  QUERY
==================================================================================*/
// Posts per load in "Show all" mode: the first render and every Load more.
define('DSA_2026_BLOG_PER_PAGE', 9);

// $ids null: the latest posts, page $page. $ids array (picked posts): only those, in that order.
function dsa_2026_blog_query($page = 1, $ids = null)
{
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	);

	if (is_array($ids)) {
		$ids = array_filter(array_map('absint', $ids));

		$args['post__in']       = $ids ?: array(0); // an empty post__in would return every post
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count($args['post__in']);
		$args['no_found_rows']  = true;
	} else {
		$args['posts_per_page'] = DSA_2026_BLOG_PER_PAGE;
		$args['paged']          = max(1, absint($page));
	}

	return new WP_Query($args);
}

// The query's cards as HTML: components-2026/partials/blog-card.php for each post.
function dsa_2026_blog_cards($query)
{
	ob_start();

	while ($query->have_posts()) {
		$query->the_post();
		get_template_part('components-2026/partials/blog-card');
	}

	wp_reset_postdata();

	return ob_get_clean();
}

/*==================================================================================
  LOAD MORE (REST)
==================================================================================*/
// GET /wp-json/dsa-2026/v1/blog?page=2 → the next cards, for src-2026/js/modules/blogRow.js.
// Public: published posts only, and the page size is fixed here, not by the request.
add_action('rest_api_init', 'dsa_2026_blog_rest_route');

function dsa_2026_blog_rest_route()
{
	register_rest_route('dsa-2026/v1', '/blog', array(
		'methods'             => 'GET',
		'callback'            => 'dsa_2026_blog_rest_page',
		'permission_callback' => '__return_true',
		'args'                => array(
			'page' => array(
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
		),
	));
}

function dsa_2026_blog_rest_page($request)
{
	$page  = max(1, (int) $request['page']);
	$query = dsa_2026_blog_query($page);
	$total = (int) $query->found_posts;

	return rest_ensure_response(array(
		'html'     => dsa_2026_blog_cards($query),
		'shown'    => min($page * DSA_2026_BLOG_PER_PAGE, $total),
		'total'    => $total,
		'has_more' => $page < $query->max_num_pages,
	));
}
