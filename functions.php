<?php
//* Start the engine
require_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'executive', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'executive' ) );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', __( 'Executive Pro Theme', 'executive' ) );
define( 'CHILD_THEME_URL', 'http://my.studiopress.com/themes/executive/' );
define( 'CHILD_THEME_VERSION', '3.1.2' );

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'executive_load_scripts' );
function executive_load_scripts() {

//* Uncomment to use Genesis responsive-menu
	// wp_enqueue_script( 'executive-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );

	wp_enqueue_style( 'dashicons' );

	wp_enqueue_style( 'google-font', '//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700', array(), CHILD_THEME_VERSION );

}

//* Add new image sizes
add_image_size( 'featured', 300, 100, TRUE );
add_image_size( 'portfolio', 300, 200, TRUE );
add_image_size( 'slider', 1140, 445, TRUE );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'width'           => 260,
	'height'          => 100,
	'header-selector' => '.site-title a',
	'header-text'     => false
) );

//* Add support for additional color style options
add_theme_support( 'genesis-style-selector', array(
	'executive-pro-brown'  => __( 'Executive Pro Brown', 'executive' ),
	'executive-pro-green'  => __( 'Executive Pro Green', 'executive' ),
	'executive-pro-orange' => __( 'Executive Pro Orange', 'executive' ),
	'executive-pro-purple' => __( 'Executive Pro Purple', 'executive' ),
	'executive-pro-red'    => __( 'Executive Pro Red', 'executive' ),
	'executive-pro-teal'   => __( 'Executive Pro Teal', 'executive' ),
) );

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

//* Unregister secondary sidebar
unregister_sidebar( 'sidebar-alt' );

//* Load Admin Stylesheet
add_action( 'admin_enqueue_scripts', 'executive_load_admin_styles' );
function executive_load_admin_styles() {

	wp_register_style( 'custom_wp_admin_css', get_stylesheet_directory_uri() . '/lib/admin-style.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );

}

//* Create Portfolio Type custom taxonomy
add_action( 'init', 'executive_type_taxonomy' );
function executive_type_taxonomy() {

	register_taxonomy( 'portfolio-type', 'portfolio',
		array(
			'labels' => array(
				'name'          => _x( 'Types', 'taxonomy general name', 'executive' ),
				'add_new_item'  => __( 'Add New Portfolio Type', 'executive' ),
				'new_item_name' => __( 'New Portfolio Type', 'executive' ),
			),
			'exclude_from_search' => true,
			'has_archive'         => true,
			'hierarchical'        => true,
			'rewrite'             => array( 'slug' => 'portfolio-type', 'with_front' => false ),
			'show_ui'             => true,
			'show_tagcloud'       => false,
		)
	);

}

//* Create portfolio custom post type
add_action( 'init', 'executive_portfolio_post_type' );
function executive_portfolio_post_type() {

	register_post_type( 'portfolio',
		array(
			'labels' => array(
				'name'          => __( 'Portfolio', 'executive' ),
				'singular_name' => __( 'Portfolio', 'executive' ),
			),
			'has_archive'  => true,
			'hierarchical' => true,
			'menu_icon'    => get_stylesheet_directory_uri() . '/lib/icons/portfolio.png',
			'public'       => true,
			'rewrite'      => array( 'slug' => 'portfolio', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions', 'page-attributes', 'genesis-seo', 'genesis-cpt-archives-settings' ),
			'taxonomies'   => array( 'portfolio-type' ),

		)
	);

}

//* Add Portfolio Type Taxonomy to columns
add_filter( 'manage_taxonomies_for_portfolio_columns', 'executive_portfolio_columns' );
function executive_portfolio_columns( $taxonomies ) {

    $taxonomies[] = 'portfolio-type';
    return $taxonomies;

}

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Reposition the secondary navigation menu
// remove_action( 'genesis_after_header', 'genesis_do_subnav' );
// add_action( 'genesis_footer', 'genesis_do_subnav', 7 );

//* Reduce the secondary navigation menu to one level depth
add_filter( 'wp_nav_menu_args', 'executive_secondary_menu_args' );
function executive_secondary_menu_args( $args ){

if( 'secondary' != $args['theme_location'] )
return $args;

$args['depth'] = 1;
return $args;

}

//* Relocate the post info
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'genesis_post_info', 5 );

//* Relocate the post meta
//remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
//add_action( 'genesis_entry_header', 'genesis_post_meta', 12 );

//* Change the number of portfolio items to be displayed (props Bill Erickson)
add_action( 'pre_get_posts', 'executive_portfolio_items' );
function executive_portfolio_items( $query ) {

	if( $query->is_main_query() && !is_admin() && is_post_type_archive( 'portfolio' ) ) {
		$query->set( 'posts_per_page', '12' );
	}

}

//* Customize Portfolio post info and post meta
add_filter( 'genesis_post_info', 'executive_portfolio_post_info_meta' );
add_filter( 'genesis_post_meta', 'executive_portfolio_post_info_meta' );
function executive_portfolio_post_info_meta( $output ) {

     if( 'portfolio' == get_post_type() )
        return '';

    return $output;

}

//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'executive_remove_comment_form_allowed_tags' );
function executive_remove_comment_form_allowed_tags( $defaults ) {

	$defaults['comment_notes_after'] = '';
	return $defaults;

}

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Add support for after entry widget
add_theme_support( 'genesis-after-entry-widget-area' );

//* Relocate after entry widget
remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );
add_action( 'genesis_after_entry', 'genesis_after_entry_widget_area', 5 );

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'home-slider',
	'name'        => __( 'Home - Slider', 'executive' ),
	'description' => __( 'This is the slider section on the home page.', 'executive' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-top',
	'name'        => __( 'Home - Top', 'executive' ),
	'description' => __( 'This is the top section of the home page.', 'executive' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-cta',
	'name'        => __( 'Home - Call To Action', 'executive' ),
	'description' => __( 'This is the call to action section on the home page.', 'executive' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle',
	'name'        => __( 'Home - Middle Left', 'executive' ),
	'description' => __( 'This is the middle left section of the home page.', 'executive' ),
) );
//* YDESF
genesis_register_sidebar( array(
	'id'          => 'home-middle-center',
	'name'        => __( 'Home - Middle Center', 'executive' ),
	'description' => __( 'This is the middle center section of the home page.', 'executive' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle-right',
	'name'        => __( 'Home - Middle Right', 'executive' ),
	'description' => __( 'This is the middle right section of the home page.', 'executive' ),
) );
//* YDESFEND

function new_nav_menu_items($items,$args) {

    // uncomment this to find your theme's menu location

    //echo "args: <pre>"; print_r($args); echo "</pre>";

    if ($args->theme_location == '') {

        if (function_exists('icl_get_languages')) {

            $languages = icl_get_languages('skip_missing=0');

            if(1 < count($languages)){

                foreach($languages as $l){

                    if(!$l['active']){

                        $items = $items.'<li class="menu-item lang-'.$l['language_code'].'"><a href="'.$l['url'].'"/>'.$l['native_name'].'</a></li>';

                    }

                }

            }

        }

    }

    return $items;

}



add_filter('wp_nav_menu_items', 'new_nav_menu_items',10,2 );

//* Do NOT include the opening php tag shown above. Copy the code shown below.

// Customize Read More text
//add_filter( 'excerpt_more', 'nabm_more_link' );
//add_filter( 'get_the_content_more_link', 'nabm_more_link' );
//add_filter( 'the_content_more_link', 'nabm_more_link' );
//function nabm_more_link() {
//return '... <div><a class="more-link" href="' . get_permalink() . '" rel="nofollow">Lire plus</a></div>';
//}
//add_filter( 'get_the_content_more_link', 'sp_read_more_link' );
//function sp_read_more_link() {
//return '... <a class="more-link">...</a>';
//}
/* YDESF */
add_filter( 'excerpt_more', 'nabm_more_link' );
add_filter( 'get_the_content_more_link', 'nabm_more_link' );
add_filter( 'the_content_more_link', 'nabm_more_link' );
function nabm_more_link() {
return '...';
}

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'custom_enqueue_scripts_styles' );
function custom_enqueue_scripts_styles() {

	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css' );

	wp_enqueue_script( 'global', get_bloginfo( 'stylesheet_directory' ) . '/js/global.js', array( 'jquery' ), '1.0.0', true );

}
