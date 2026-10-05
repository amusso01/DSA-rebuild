<?php

/**
 * Main Site Header Template
 * @author   FDRY
 * @package  FDRY
 */



?>


<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="We are your secure IT Asset Disposal specialists, deeply committed to our ESG (Environmental, Social, Governance) responsibilities, as well as those of our valued customers and partners.">
	<title><?php
					if (get_field('subtitle')) {
						echo get_field('subtitle') . ' ' . get_the_title() . ' - ' . get_bloginfo('description');
					} else {
						echo get_the_title() . ' - ' . get_bloginfo('description');
					}
					?></title>
	<script src="https://player.vimeo.com/api/player.js"></script>

	<meta charset="<?php bloginfo('charset'); ?>">

	<?php wp_head(); ?>

	<script type="text/javascript" src=https://secure.leadforensics.com/js/136822.js></script>
	<noscript><img src=https://secure.leadforensics.com/136822.png style="display:none;" /></noscript>

	<script type="text/javascript" src=https://secure.leadforensics.com/js/sc/136822.js></script>

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-JXHSK6GKG1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'G-JXHSK6GKG1');
	</script>

	<script async type='text/javascript' src='https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=SfCKqT'></script>

	<script>
		function initApollo() {
			var n = Math.random().toString(36).substring(7),
				o = document.createElement("script");
			o.src = "https://assets.apollo.io/micro/website-tracker/tracker.iife.js?nocache=" + n, o.async = !0, o.defer = !0, o.onload = function() {
				window.trackingFunctions.onLoad({
					appId: "69f49e2c942edc001d5e4dd6"
				})
			}, document.head.appendChild(o)
		}
		initApollo();
	</script>
</head>

<body <?php body_class(); ?>>
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'foundry'); ?></a>
	<header class="header" data-2026="header">
		<div class="content-block">
			<div class="content-max">
				<div class="header-inner">
					<div class="header-logo">
						<?php get_template_part('components-2026/header/logo'); ?>
					</div>
					<div class="header-navigation">
						<?php get_template_part('components-2026/header/hamburger'); ?>
						<?php get_template_part('components-2026/header/navigation'); ?>
					</div>
				</div>
			</div>
		</div>
	</header>