<?php
// This function enqueues the Normalize.css for use. The first parameter is a name for the stylesheet, the second is the URL. Here we
// use an online version of the css file.
/*
 * Enable support for Post Thumbnails on posts and pages.
 */
add_theme_support('post-thumbnails');
add_post_type_support('page', 'excerpt');
add_action('wp_enqueue_scripts', 'add_normalize_CSS');

function add_normalize_CSS()
{

	//BOOTSTRAP
	wp_register_style('bootstrap-styles',   'https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css');
	wp_enqueue_style('bootstrap-styles');

	// STYLE
	wp_register_style('foundry-styles',   get_template_directory_uri() . '/dist/styles/main.css?num=' . rand(), array(), '1.0', 'all');
	wp_enqueue_style('foundry-styles');

	//slick slider
	wp_register_style('foundry-slick',   get_template_directory_uri() . '/dist/slick/slick.css', array(), '1.0', 'all');
	wp_register_style('foundry-slick-theme',   get_template_directory_uri() . '/dist/slick/slick-theme.css', array(), '1.0', 'all');
	wp_enqueue_style('foundry-slick');
	wp_enqueue_style('foundry-slick-theme');




	// SCRIPT
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js');
	wp_enqueue_script('bootstrap-jquery', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js');
	wp_enqueue_script('slick-jquery',  get_template_directory_uri() . '/dist/slick/slick.min.js');
	wp_enqueue_script('main-jquery',  get_template_directory_uri() . '/dist/scripts/main.js');
	wp_enqueue_script('bundle',  get_template_directory_uri() . '/dist/bundle.js');

	wp_enqueue_script('jquery');
	wp_enqueue_script('slick-jquery');
	wp_enqueue_script('bootstrap-jquery');
	wp_enqueue_script('main-jquery');
	wp_enqueue_script('bundle');




	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}

// Register a new sidebar simply named 'sidebar'
function add_widget_Support()
{
	register_sidebar(array(
		'name'          => 'Sidebar',
		'id'            => 'sidebar',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
	));
}
// Hook the widget initiation and run our function
add_action('widgets_init', 'add_Widget_Support');

// Register a new navigation menu
function add_Main_Nav()
{
	register_nav_menus(
		array(
			'header-menu' => __('Header Menu'),
			'top-menu' => __('Top Menu'),
			'services-menu' => __('Services'),
			'about-menu' => __('About'),
			'insights-menu' => __('Insights'),
			'sectors-menu' => __('Sectors'), 
		)
	);
}
// Hook to the init action hook, run our navigation menu function
add_action('init', 'add_Main_Nav');

//ACF Option page
if (function_exists('acf_add_options_page')) {

	acf_add_options_page();
}


/*
* Creating a function to create our CPT
*/

function custom_post_type()
{

	// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x('Projects', 'Post Type General Name', 'twentytwentyone'),
		'singular_name'       => _x('Project', 'Post Type Singular Name', 'twentytwentyone'),
		'menu_name'           => __('Projects', 'twentytwentyone'),
		'parent_item_colon'   => __('Parent Project', 'twentytwentyone'),
		'all_items'           => __('All Projects', 'twentytwentyone'),
		'view_item'           => __('View Project', 'twentytwentyone'),
		'add_new_item'        => __('Add New Project', 'twentytwentyone'),
		'add_new'             => __('Add New', 'twentytwentyone'),
		'edit_item'           => __('Edit Project', 'twentytwentyone'),
		'update_item'         => __('Update Project', 'twentytwentyone'),
		'search_items'        => __('Search Project', 'twentytwentyone'),
		'not_found'           => __('Not Found', 'twentytwentyone'),
		'not_found_in_trash'  => __('Not found in Trash', 'twentytwentyone'),
	);

	// Set other options for Custom Post Type

	$args = array(
		'label'               => __('Projects', 'twentytwentyone'),
		'description'         => __('Project news and reviews', 'twentytwentyone'),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		'taxonomies'          => array('genres'),
		/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest' => true,

	);

	// Registering your Custom Post Type
	register_post_type('project', $args);
}

/* Hook into the 'init' action so that the function
	* Containing our post type registration is not 
	* unnecessarily executed. 
	*/

add_action('init', 'custom_post_type', 0);



//create a custom taxonomy name it subjects for your posts
add_action('init', 'create_subjects_hierarchical_taxonomy', 0);
function create_subjects_hierarchical_taxonomy()
{

	// Add new taxonomy, make it hierarchical like categories
	//first do the translations part for GUI

	$labels = array(
		'name' => _x('Categories', 'taxonomy general name'),
		'singular_name' => _x('Categories', 'taxonomy singular name'),
		'search_items' =>  __('Search Categories'),
		'all_items' => __('All Categories'),
		'parent_item' => __('Parent Category'),
		'parent_item_colon' => __('Parent Category:'),
		'edit_item' => __('Edit Category'),
		'update_item' => __('Update Category'),
		'add_new_item' => __('Add New Category'),
		'new_item_name' => __('New Category Name'),
		'menu_name' => __('Categories'),
	);

	// Now register the taxonomy
	register_taxonomy('project_category', array('project'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_rest' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'project-cat'),
	));
}
add_action('init', 'create_plot_type_hierarchical_taxonomy', 0);
function create_plot_type_hierarchical_taxonomy()
{

	// Add new taxonomy, make it hierarchical like categories
	//first do the translations part for GUI

	$labels = array(
		'name' => _x('Plot types', 'taxonomy general name'),
		'singular_name' => _x('Plot type', 'taxonomy singular name'),
		'search_items' =>  __('Search Plot type'),
		'all_items' => __('All Plot types'),
		'parent_item' => __('Parent Plot type'),
		'parent_item_colon' => __('Parent Plot type:'),
		'edit_item' => __('Edit Plot type'),
		'update_item' => __('Update Plot type'),
		'add_new_item' => __('Add New Plot type'),
		'new_item_name' => __('New Plot type Name'),
		'menu_name' => __('Plot type'),
	);

	// Now register the taxonomy
	register_taxonomy('project_plot_type', array('project'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_rest' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'project-plot-type'),
	));
}


/* Shortcodes */
add_shortcode('options-accordeon', 'options_accordeon_func');
function options_accordeon_func($atts)
{
	$information = $atts['id'];
	$accordeon = "";
	if (have_rows($information, 'options')):
		$accordeon .= '<div class="accordion accordion-flush" id="' . $information . '">';
		while (have_rows($information, 'options')) : the_row();
			$accordeon .= '<div class="accordion-item">
							<h3 class="accordion-header" id="flush-heading' . get_row_index() . '">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' . get_row_index() . '" aria-expanded="false" aria-controls="flush-collapse' . get_row_index() . '">
								' . get_sub_field('title') . '
							</button>
							</h3>
							<div id="flush-collapse' . get_row_index() . '" class="accordion-collapse collapse" aria-labelledby="flush-heading' . get_row_index() . '" data-bs-parent="#' . $information . '">
							<div class="accordion-body">' . get_sub_field('description') . '</div> 
							</div>
						</div>';
		endwhile;
		$accordeon .= '</div>';
	endif;

	return $accordeon;
}


add_shortcode('options-columns', 'options_columns_func');
function options_columns_func($atts)
{
	$information = $atts['id'];
	$accordeon = "";
	if (have_rows($information, 'options')):
		$accordeon .= '<div class="column-box">';
		while (have_rows($information, 'options')) : the_row();
			$accordeon .= '<div class="column-item">
							<img src="' . get_sub_field('icon') . '" />
							<h3>' . get_sub_field('description') . '</h3>
						</div>';
		endwhile;
		$accordeon .= '</div>';
	endif;

	return $accordeon;
}


// The shortcode function
function wpb_my_contact()
{

	// Advertisement code pasted inside a variable
	
	if(  false ){
		$string .= '<div class="row gx-5 my-contact"> 
					<div class="col-lg-6 col-12 complete-line"> 
						
						<p><strong>Phone:</strong> 0208 167 7320</p>
						<p><strong>Email:</strong> <a href="mailto:nick@dsa-connect.co.uk">nick@dsa-connect.co.uk</a></p> 
					</div>
					<div class="col-lg-6 col-12">
						<p><strong>Get in touch</strong></p>
						<p>Fill in the form to get in touch with a member of our team, we look forward to hearing from you.</p>';
	
		$string .= do_shortcode('[contact-form-7 id="2012" title="Contact form 1"]');
		$string .= '</div>
					</div>';

	}else{
		$string .= '<div class="row gx-5 my-contact"> 
						<div class=" col-12 big-contetn-step" >
							<p>Fill in the form to get in touch with a member of our team, we look forward to hearing from you.</p>
							<svg xmlns="http://www.w3.org/2000/svg" width="77.145" height="77.145" viewBox="0 0 77.145 77.145">
								<path id="paper-plane-svgrepo-com" d="M76.931,3.069A3.649,3.649,0,0,1,77.8,6.854L53.776,75.48a5.473,5.473,0,0,1-10.166.415L33.87,53.98,61.1,21.47a1.824,1.824,0,0,0-2.57-2.57L26.02,46.13,4.106,36.39A5.473,5.473,0,0,1,4.52,26.224L73.146,2.2A3.649,3.649,0,0,1,76.931,3.069Z" transform="translate(-0.855 -2)" fill="#071031"/>
							</svg>
						</div>
					</div>';
	}

	// Ad code returned
	return $string;
}
// Register shortcode
add_shortcode('my_contact', 'wpb_my_contact');


// The shortcode function
function wpb_data_destruction()
{

	// Advertisement code pasted inside a variable
	$string = "";
	if (get_field('data_destruction_01')) {

		$string .= '<div class="row gx-5 my-contact data-box">
					
					<div class="col-lg-6 col-12 ">
						<p class="column-01">' . get_field('data_destruction_01') . '</p> 
					</div>
					<div class="col-lg-6 col-12"> 
						<p  class="column-02">' . get_field('data_destruction_02') . '</p>';
		$string .= '</div>
				</div>';
	}


	// Ad code returned
	return $string;
}
// Register shortcode
add_shortcode('data_destruction', 'wpb_data_destruction');

// function that runs when shortcode is called
function wpb_progressbar($atts)
{
    $atts = shortcode_atts(
        array(
            'type' => '',
        ),
        $atts,
        'progressbar'
    );

    if ($atts['type'] == 'services') {
        $first_step = 'Next Steps';
        $number_steps = 'steps-3';
        $title_form = ' <h2>Learn more about ' . get_the_title() . '</h2>';
    } else {
        $first_step = 'Service';
        $number_steps = 'steps-3';
        $title_form = ' ';
    }

    $message = '<div id="cm7-progress" class="progress-bar-content ' . $number_steps . '" columns="' . $number_steps . '" service="' . get_the_title() . '" >
                    ' . $title_form . '
                    <div class="progress-bar-steps">
                        <div id="item-1" class="bar-item active">
							<div class="svg-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="56.817" height="57" viewBox="0 0 56.817 57">
									<path id="package-settings-svgrepo-com" d="M33.929,20.831a12.165,12.165,0,0,0-9,4c-.163.163-.846-.169-1,0l11,5c2.256.91,3.91,3.744,3,6l-1,2c-.91,2.256-3.744,3.91-6,3l-10-4c.413,2.806,1.842,4.842,4,7,3.6,3.6,8.414,5.27,13,4l28,28a6.48,6.48,0,0,0,9,0l1-1a5.311,5.311,0,0,0,0-8l-28-28c1.374-4.644-.335-10.335-4-14A14.423,14.423,0,0,0,33.929,20.831Zm38,5a6.142,6.142,0,0,0-4,2l-14,14,2,2,11-10,4-5a.707.707,0,0,1,1,1l-15,15,2,2,15-15a.707.707,0,1,1,1,1l-15,15,2,2,14-14a5.718,5.718,0,0,0,0-8A6.161,6.161,0,0,0,71.929,25.831Zm-28,31-7,7h-4l-7,11,2,3,12-7v-4l7-7Zm27,11a2.621,2.621,0,0,1,2,1,2.55,2.55,0,0,1,0,4,3.712,3.712,0,0,1-5,0,2.55,2.55,0,0,1,0-4A5.059,5.059,0,0,1,70.929,67.831Z" transform="translate(-20.929 -20.831)" fill="#071031"/>
								</svg>
                            	Service
							</div>
                        </div>';
    $message .= '  <div id="item-2" class="bar-item">

						<div class="svg-icon">
							<svg xmlns="http://www.w3.org/2000/svg" width="55" height="57" viewBox="0 0 55 57">
								<g id="Shopicon" transform="translate(120.27 -12.772)">
								<path id="Path_6" data-name="Path 6" d="M57,41,30,50,15,6H4v6h7L26,58,59,47Z" transform="translate(-124.269 6.772)" fill="#071031"/>
								<path id="Path_7" data-name="Path 7" d="M45.011,12l-25,9,6,18,25-9Z" transform="translate(-116.28 9.768)" fill="#5cc3be"/>
								<circle id="Ellipse_1" data-name="Ellipse 1" cx="3" cy="3" r="3" transform="translate(-90.269 63.772)" fill="#071031"/>
								</g>
							</svg>
  
                            Sector

						</div>
                    </div>';

    

    $message .= '  <div id="item-3" class="bar-item">

					<div class="svg-icon">
							<svg xmlns="http://www.w3.org/2000/svg" width="51" height="57" viewBox="0 0 51 57">
								<path id="id-svgrepo-com" d="M49,7H37a8.825,8.825,0,0,0-8-6,8.825,8.825,0,0,0-8,6H9C5.871,7,3,8.871,3,12V52a6.253,6.253,0,0,0,6,6H49c3.129,0,5-2.871,5-6V12A4.6,4.6,0,0,0,49,7ZM29,7c1.565,0,2,1.435,2,3s-.435,2-2,2-3-.435-3-2A3.127,3.127,0,0,1,29,7Zm0,11c4.722,0,8,4.278,8,9a7.641,7.641,0,0,1-8,8c-4.722,0-9-3.278-9-8A9.338,9.338,0,0,1,29,18ZM46,52H12V48c0-5.689,11.311-9,17-9s17,3.311,17,9Z" transform="translate(-3 -1)" fill="#071031"/>
							</svg>
						
                            Details

                            
                        </div>
                </div>';
    $message .= '</div>
                    <div class="progress">
                        <div id="per-progress" class="progress-bar" role="progressbar" aria-valuenow="0"
                        aria-valuemin="0" aria-valuemax="100" style="width:0%">
                            <span class="sr-only">70% Complete</span>
                        </div>
                    </div>
                </div>';


    return $message;
}
add_shortcode('progressbar', 'wpb_progressbar');