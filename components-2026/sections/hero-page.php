<?php
/**
 * Section: hero page (layout hero_page in Dynamic Layout 2026).
 * Breadcrumb (Home / repeater links / current page), title with the two accent lines,
 * optional background image over $color__main-light. Rendered by dsa_2026_render_sections(),
 * and by Contact 2026 from its ACF group field "hero" (same sub fields).
 *
 * @package FDRY
 */

$args = wp_parse_args($args, array(
	'breadcrumb'             => array(), // repeater rows: array('link' => ACF Link array)
	'breadcrumb_actual_page' => '',      // last crumb; empty: the page title
	'title'                  => '',      // empty: the page title; <span class="accent"> highlights
	'title_tag'              => 'h1',
	'image'                  => 0,       // image ID
	'index'                  => 0,
));

// Home is always first, then the repeater links. The current page is printed after them, not as a link.
$crumbs = array(
	array('url' => home_url('/'), 'title' => __('Home'), 'target' => ''),
);

foreach ((array) $args['breadcrumb'] as $row) {
	if (!empty($row['link']['url']) && !empty($row['link']['title'])) {
		$crumbs[] = $row['link'];
	}
}

$current = $args['breadcrumb_actual_page'] ?: get_the_title();
$title   = $args['title'] ?: get_the_title();
$tag     = in_array($args['title_tag'], array('h1', 'h2', 'h3'), true) ? $args['title_tag'] : 'h1';

// the first section is above the fold: load its image right away
$first = $args['index'] === 0;
?>
<section class="hero-page content-block">
	<?php if ($args['image']) : ?>
		<?php echo wp_get_attachment_image($args['image'], 'full', false, array(
			'class'         => 'hero-page__image',
			'alt'           => '',
			'sizes'         => '100vw',
			'loading'       => $first ? 'eager' : 'lazy',
			'fetchpriority' => $first ? 'high' : 'auto',
		)); ?>
	<?php endif; ?>
	<div class="content-max">
		<nav class="hero-page__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb'); ?>">
			<ol class="hero-page__crumbs">
				<?php foreach ($crumbs as $crumb) : ?>
					<li class="hero-page__crumb">
						<a href="<?php echo esc_url($crumb['url']); ?>"<?php if (($crumb['target'] ?? '') === '_blank') : ?> target="_blank" rel="noopener"<?php endif; ?>><?php echo esc_html($crumb['title']); ?></a>
						<span class="hero-page__separator" aria-hidden="true">/</span>
					</li>
				<?php endforeach; ?>
				<li class="hero-page__crumb hero-page__crumb--current" aria-current="page"><?php echo esc_html($current); ?></li>
			</ol>
		</nav>
		<<?php echo tag_escape($tag); ?> class="hero-page__title"><?php echo wp_kses($title, array('span' => array('class' => true))); ?></<?php echo tag_escape($tag); ?>>
		<span class="hero-page__lines" aria-hidden="true"></span>
	</div>
</section>
