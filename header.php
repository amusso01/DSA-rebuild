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
		if( get_field('subtitle') ){
			echo get_field('subtitle').' '.get_the_title().' - '.get_bloginfo( 'description' );
		}else{
			echo get_the_title().' - '.get_bloginfo( 'description' );
		}
		 ?></title>
	<script src="https://player.vimeo.com/api/player.js"></script>

	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<?php wp_head(); ?>

	<script type="text/javascript" src=https://secure.leadforensics.com/js/136822.js></script>
	<noscript><img src=https://secure.leadforensics.com/136822.png style="display:none;" /></noscript>
	
	<script type="text/javascript" src=https://secure.leadforensics.com/js/sc/136822.js></script>

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-JXHSK6GKG1"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'G-JXHSK6GKG1');
	</script> 

	<script async type='text/javascript' src='https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=SfCKqT'></script>

	<script>function initApollo(){var n=Math.random().toString(36).substring(7),o=document.createElement("script"); o.src="https://assets.apollo.io/micro/website-tracker/tracker.iife.js?nocache="+n,o.async=!0,o.defer=!0, o.onload=function(){window.trackingFunctions.onLoad({appId:"69f49e2c942edc001d5e4dd6"})}, document.head.appendChild(o)}initApollo();</script>
</head>

<body <?php body_class(  ); ?>>


<section class="top">
	<div class="container text-align-right">
		<?php  wp_nav_menu( array( 'theme_location' => 'top-menu',  'container' => false ) ); ?>
	</div>
</section>


<header class="header">
       <div class="container">
			<div class="row align-items-center">
				<div class="col-xl-2 col-9"> 
					
					<a href="<?php echo get_site_url("/");?>">
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="87.742" height="58.825" viewBox="0 0 87.742 58.825">
						<defs>
							<linearGradient id="linear-gradient" x1="-14.427" y1="3.619" x2="21.788" y2="-3.943" gradientUnits="objectBoundingBox">
							<stop offset="0" stop-color="#08c4c4"/>
							<stop offset="1" stop-color="#ffed00"/>
							</linearGradient>
							<linearGradient id="linear-gradient-2" x1="-0.075" y1="0.859" x2="2.772" y2="-1.032" xlink:href="#linear-gradient"/>
							<linearGradient id="linear-gradient-3" x1="-13.068" y1="3.337" x2="23.146" y2="-4.225" xlink:href="#linear-gradient"/>
							<linearGradient id="linear-gradient-4" x1="-7.123" y1="1.718" x2="4.948" y2="-0.173" xlink:href="#linear-gradient"/>
							<linearGradient id="linear-gradient-5" x1="-19.414" y1="4.659" x2="16.801" y2="-2.902" xlink:href="#linear-gradient"/>
							<linearGradient id="linear-gradient-6" x1="-20.773" y1="4.942" x2="15.442" y2="-2.62" xlink:href="#linear-gradient"/>
							<linearGradient id="linear-gradient-7" x1="-17.297" y1="4.218" x2="18.917" y2="-3.344" xlink:href="#linear-gradient"/>
							<linearGradient id="linear-gradient-8" x1="-18.656" y1="4.5" x2="17.559" y2="-3.062" xlink:href="#linear-gradient"/>
						</defs>
						<g id="dsa-logo" transform="translate(0.001 0.001)">
							<g id="Group_161" data-name="Group 161" transform="translate(-0.001 -0.001)">
							<rect id="Rectangle_80" data-name="Rectangle 80" width="3.865" height="3.865" transform="translate(52.866 3.866)" fill="url(#linear-gradient)"/>
							<path id="Path_116" data-name="Path 116" d="M45.29,7.73V3.865h3.865V0H.42A.4.4,0,0,0,0,.42V15.041a.4.4,0,0,0,.42.42H45.29V11.6h3.865V7.73Z" transform="translate(0.001 0.001)" fill="url(#linear-gradient-2)"/>
							<rect id="Rectangle_81" data-name="Rectangle 81" width="3.865" height="3.865" transform="translate(49.135 11.596)" fill="url(#linear-gradient-3)"/>
							<g id="Group_160" data-name="Group 160" transform="translate(60.684)">
								<path id="Path_117" data-name="Path 117" d="M105.375,0h-7.31V3.865H94.2V7.73h3.865V11.6H94.2v3.865h11.175a.4.4,0,0,0,.42-.42V.42a.4.4,0,0,0-.42-.42Z" transform="translate(-78.739 0.001)" fill="url(#linear-gradient-4)"/>
								<rect id="Rectangle_82" data-name="Rectangle 82" width="3.865" height="3.865" transform="translate(7.73 7.731)" fill="url(#linear-gradient-5)"/>
								<rect id="Rectangle_83" data-name="Rectangle 83" width="3.865" height="3.865" transform="translate(11.596 0.001)" fill="url(#linear-gradient-6)"/>
								<rect id="Rectangle_84" data-name="Rectangle 84" width="3.865" height="3.865" transform="translate(0 11.596)" fill="url(#linear-gradient-7)"/>
								<rect id="Rectangle_85" data-name="Rectangle 85" width="3.865" height="3.865" transform="translate(3.865 3.866)" fill="url(#linear-gradient-8)"/>
							</g>
							</g>
							<g id="Group_162" data-name="Group 162" transform="translate(0 20.913)">
							<path id="Path_118" data-name="Path 118" d="M0,42.036v-14.2c0-.252.084-.336.336-.336H5.378a6.967,6.967,0,0,1,2.689.5,3.887,3.887,0,0,1,1.765,1.512,4.162,4.162,0,0,1,.588,2.269V38a3.977,3.977,0,0,1-.588,2.269,3.887,3.887,0,0,1-1.765,1.512,6.674,6.674,0,0,1-2.689.5H.336C.084,42.373,0,42.2,0,42.036Zm2.6-1.765H5.546A2.407,2.407,0,0,0,7.31,39.6a2.835,2.835,0,0,0,.672-1.933V32.289a2.835,2.835,0,0,0-.672-1.933,2.3,2.3,0,0,0-1.849-.672H2.6a.181.181,0,0,0-.168.168V40.188a.293.293,0,0,0,.168.084Z" transform="translate(0 -27.322)" fill="#051033"/>
							<path id="Path_119" data-name="Path 119" d="M18.021,42a3.708,3.708,0,0,1-1.849-1.512,4.226,4.226,0,0,1-.672-2.269V37.8c0-.252.084-.336.336-.336H17.6c.252,0,.336.084.336.336v.336a1.82,1.82,0,0,0,.84,1.6,3.824,3.824,0,0,0,2.269.672,2.667,2.667,0,0,0,1.933-.588,1.8,1.8,0,0,0,.672-1.428,1.339,1.339,0,0,0-.336-1.008,5.237,5.237,0,0,0-1.008-.756,13.3,13.3,0,0,0-2.017-.84,14.285,14.285,0,0,1-2.437-1.008,4.879,4.879,0,0,1-1.6-1.344,3.439,3.439,0,0,1-.588-2.1,3.515,3.515,0,0,1,1.344-2.941A5.456,5.456,0,0,1,20.626,27.3a5.639,5.639,0,0,1,2.773.588A4.342,4.342,0,0,1,25.331,29.4,4.226,4.226,0,0,1,26,31.668v.252c0,.252-.084.336-.336.336H23.9a.308.308,0,0,1-.252-.084c-.084-.084-.084-.084-.084-.168v-.252a1.961,1.961,0,0,0-.84-1.681,3.477,3.477,0,0,0-2.269-.672,3.193,3.193,0,0,0-1.849.5,1.633,1.633,0,0,0-.672,1.344,1.339,1.339,0,0,0,.336,1.008,5.237,5.237,0,0,0,1.008.756,15.215,15.215,0,0,0,2.1.84c1.008.42,1.765.756,2.437,1.008a5.5,5.5,0,0,1,1.512,1.26,3.485,3.485,0,0,1,.672,2.1,3.949,3.949,0,0,1-.672,2.185,4.059,4.059,0,0,1-1.849,1.428,7.1,7.1,0,0,1-2.773.5A6.966,6.966,0,0,1,18.021,42Z" transform="translate(-2.476 -27.288)" fill="#051033"/>
							<path id="Path_120" data-name="Path 120" d="M40.219,42.136l-.672-2.185c0-.084-.084-.084-.168-.084H33.833c-.084,0-.168,0-.168.084l-.672,2.185a.388.388,0,0,1-.42.252H30.64a.308.308,0,0,1-.252-.084.157.157,0,0,1,0-.252l4.537-14.2a.388.388,0,0,1,.42-.252h2.437a.462.462,0,0,1,.42.252l4.537,14.2v.168c0,.168-.084.252-.336.252H40.471a1.159,1.159,0,0,1-.252-.336ZM34.505,37.85h4.285c.084,0,.084-.084.084-.168L36.69,30.456c0-.084,0-.084-.084-.084a.084.084,0,0,0-.084.084l-2.185,7.226C34.337,37.85,34.421,37.85,34.505,37.85Z" transform="translate(-5.823 -27.337)" fill="#051033"/>
							<path id="Path_121" data-name="Path 121" d="M2.468,68.92a4.4,4.4,0,0,1-1.681-1.6A4.559,4.559,0,0,1,.2,64.887v-6.05a4.263,4.263,0,0,1,.588-2.353,5.092,5.092,0,0,1,1.681-1.6A5.075,5.075,0,0,1,4.989,54.3a5.075,5.075,0,0,1,2.521.588,3.859,3.859,0,0,1,1.681,1.6,4.263,4.263,0,0,1,.588,2.353c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084H8.938c-.168,0-.252-.084-.252-.168v-.168a3.17,3.17,0,0,0-1.008-2.521,3.627,3.627,0,0,0-2.689-.924,4.009,4.009,0,0,0-2.689.924,3.42,3.42,0,0,0-1.008,2.521v6.218A3.17,3.17,0,0,0,2.3,67.492,3.679,3.679,0,0,0,4.989,68.5a3.679,3.679,0,0,0,2.689-1.008,3.42,3.42,0,0,0,1.008-2.521.223.223,0,0,1,.252-.252h.588c.168,0,.252,0,.252.084v.168a4.263,4.263,0,0,1-.588,2.353,5.092,5.092,0,0,1-1.681,1.6,5.075,5.075,0,0,1-2.521.588,6.175,6.175,0,0,1-2.521-.588Z" transform="translate(-0.031 -31.603)" fill="#051033"/>
							<path id="Path_122" data-name="Path 122" d="M18.268,68.836a4.045,4.045,0,0,1-1.681-1.681A5.075,5.075,0,0,1,16,64.634V59.089a5.075,5.075,0,0,1,.588-2.521,4.6,4.6,0,0,1,1.681-1.681,5.186,5.186,0,0,1,2.6-.588,5.186,5.186,0,0,1,2.6.588,4.853,4.853,0,0,1,1.765,1.681,5.075,5.075,0,0,1,.588,2.521v5.546a5.075,5.075,0,0,1-.588,2.521,4.254,4.254,0,0,1-1.765,1.681,5.186,5.186,0,0,1-2.6.588A5.186,5.186,0,0,1,18.268,68.836Zm5.378-1.428a3.747,3.747,0,0,0,1.008-2.773V59.089a4.025,4.025,0,0,0-1.008-2.773,3.747,3.747,0,0,0-2.773-1.008A4.025,4.025,0,0,0,18.1,56.316a3.747,3.747,0,0,0-1.008,2.773v5.63a3.865,3.865,0,0,0,6.554,2.689Z" transform="translate(-2.555 -31.603)" fill="#051033"/>
							<path id="Path_123" data-name="Path 123" d="M32.484,69.2c-.084-.084-.084-.084-.084-.168V54.752c0-.084,0-.168.084-.168.084-.084.084-.084.168-.084h.672a.437.437,0,0,1,.336.168L41.223,67.02c0,.084,0,.084.084.084a.084.084,0,0,0,.084-.084V54.752c0-.084,0-.168.084-.168.084-.084.084-.084.168-.084h.588c.084,0,.168,0,.168.084.084.084.084.084.084.168V69.037c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084h-.672a.437.437,0,0,1-.336-.168L33.66,56.769c0-.084,0-.084-.084-.084a.084.084,0,0,0-.084.084V69.037c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084h-.588a.26.26,0,0,1-.168-.084Z" transform="translate(-6.191 -31.635)" fill="#051033"/>
							<path id="Path_124" data-name="Path 124" d="M49.684,69.2c-.084-.084-.084-.084-.084-.168V54.752c0-.084,0-.168.084-.168.084-.084.084-.084.168-.084h.672a.437.437,0,0,1,.336.168L58.423,67.02c0,.084,0,.084.084.084a.084.084,0,0,0,.084-.084V54.752c0-.084,0-.168.084-.168.084-.084.084-.084.168-.084h.588c.084,0,.168,0,.168.084.084.084.084.084.084.168V69.037c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084h-.672a.437.437,0,0,1-.336-.168L50.86,56.769c0-.084,0-.084-.084-.084a.084.084,0,0,0-.084.084V69.037c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084h-.588C49.768,69.289,49.684,69.289,49.684,69.2Z" transform="translate(-9.478 -31.635)" fill="#051033"/>
							<path id="Path_125" data-name="Path 125" d="M76.111,55.44c-.084.084-.084.084-.168.084H67.96c-.084,0-.084,0-.084.084v5.714c0,.084,0,.084.084.084h5.63c.084,0,.168,0,.168.084.084.084.084.084.084.168v.5c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084H67.96c-.084,0-.084,0-.084.084v5.8c0,.084,0,.084.084.084h7.982c.084,0,.168,0,.168.084.084.084.084.084.084.168v.5c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084H66.952c-.084,0-.168,0-.168-.084-.084-.084-.084-.084-.084-.168V54.852c0-.084,0-.168.084-.168.084-.084.084-.084.168-.084h8.907c.084,0,.168,0,.168.084.084.084.084.084.084.168v.588Z" transform="translate(-12.729 -31.651)" fill="#051033"/>
							<path id="Path_126" data-name="Path 126" d="M83.768,68.92a4.4,4.4,0,0,1-1.681-1.6,4.559,4.559,0,0,1-.588-2.437v-6.05a4.263,4.263,0,0,1,.588-2.353,5.307,5.307,0,0,1,6.722-1.6,3.858,3.858,0,0,1,1.681,1.6,4.263,4.263,0,0,1,.588,2.353c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084h-.588c-.168,0-.252-.084-.252-.168v-.168a3.17,3.17,0,0,0-1.008-2.521,3.627,3.627,0,0,0-2.689-.924,4.009,4.009,0,0,0-2.689.924,3.42,3.42,0,0,0-1.008,2.521v6.218A3.17,3.17,0,0,0,83.6,67.492a4.09,4.09,0,0,0,5.378,0,3.42,3.42,0,0,0,1.008-2.521.223.223,0,0,1,.252-.252h.588c.168,0,.252,0,.252.084v.168a4.262,4.262,0,0,1-.588,2.353,4.679,4.679,0,0,1-4.2,2.185A5.866,5.866,0,0,1,83.768,68.92Z" transform="translate(-16.026 -31.603)" fill="#051033"/>
							<path id="Path_127" data-name="Path 127" d="M106.031,54.584c.084.084.084.084.084.168v.5c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084h-4.2c-.084,0-.084,0-.084.084V69.121c0,.084,0,.168-.084.168-.084.084-.084.084-.168.084h-.588c-.084,0-.168,0-.168-.084-.084-.084-.084-.084-.084-.168V55.592c0-.084,0-.084-.084-.084H96.452c-.084,0-.168,0-.168-.084-.084-.084-.084-.084-.084-.168v-.5c0-.084,0-.168.084-.168.084-.084.084-.084.168-.084h9.495A.084.084,0,0,1,106.031,54.584Z" transform="translate(-18.375 -31.635)" fill="#051033"/>
							</g>
						</g>
						</svg>

					</a>
				</div>
				<div id="header-menu" class="col-xl-10 d-xl-inline-block  d-none text-align-right">
					<?php  wp_nav_menu( array( 'theme_location' => 'header-menu',  'container' => false ) ); ?>
				</div>
				<div class="col-xl-2 col-3 text-align-right icon-menu d-xl-nonde d-inline-block">
					<span class="hamburger d-xl-none d-inline-block">&#9776;</span>
				</div>
				
			</div>
	   </div>
</header> 

