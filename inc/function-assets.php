<?php
/**
 * 2026 layout: front-end assets.
 * Loads dist-2026 (fonts, CSS, JS) only on pages that call get_header('new')
 * and dequeues the legacy assets there. Every other page is untouched.
 *
 * @package FDRY
 */

// get_header fires before header-new.php is loaded, so before its wp_head() runs wp_enqueue_scripts
add_action('get_header', 'dsa_2026_setup_page');

function dsa_2026_setup_page($name)
{
	if ($name !== 'new') {
		return;
	}
	// priority 20: after the legacy add_normalize_CSS() (10) has enqueued what we dequeue
	add_action('wp_enqueue_scripts', 'dsa_2026_enqueue_assets', 20);
	add_filter('body_class', 'dsa_2026_body_class');
	add_action('wp_head', 'dsa_2026_reveal_script', 1);
}

function dsa_2026_body_class($classes)
{
	$classes[] = 'layout-2026';
	return $classes;
}

// Reveal (src-2026/js/modules/reveal.js): html.reveal-on hides [data-reveal] from the first paint, so
// nothing on screen blinks before main.js runs. If main.js hasn't started within 3s (failed, or delayed
// by an optimisation plugin), the class is removed and everything shows.
function dsa_2026_reveal_script()
{
	echo "<script>(function(h){h.classList.add('reveal-on');setTimeout(function(){if(!h.classList.contains('reveal-ready'))h.classList.remove('reveal-on')},3000)})(document.documentElement)</script>\n";
}

function dsa_2026_enqueue_assets()
{
	// Legacy assets from library/function-setup.php.
	// jQuery stays: plugins may need it and the 2026 bundle treats it as an external.
	foreach (array('bootstrap-styles', 'foundry-styles', 'foundry-slick', 'foundry-slick-theme') as $handle) {
		wp_dequeue_style($handle);
	}
	foreach (array('bootstrap-jquery', 'slick-jquery', 'main-jquery', 'bundle') as $handle) {
		wp_dequeue_script($handle);
	}

	$dir = get_template_directory() . '/dist-2026';
	$uri = get_template_directory_uri() . '/dist-2026';

	// filemtime: browsers re-download only when a new build is deployed
	wp_enqueue_style('dsa-2026', $uri . '/main.css', array(), filemtime($dir . '/main.css'));
	wp_enqueue_script('dsa-2026', $uri . '/main.js', array(), filemtime($dir . '/main.js'), true);
}
