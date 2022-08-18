<?php
/**
* Functions and definitions
* by HeroThemes (http://herothemes.com)
*/

//theme active constant
if(!defined('HT_HELPGURU_PARENT_THEME_ACTIVE')){
		define('HT_HELPGURU_PARENT_THEME_ACTIVE', true);
}

/**
* Set the content width based on the theme's design and stylesheet.
*/
if ( ! isset( $content_width ) ) $content_width = 920;

//temporary fix for redux option panel control
if(!defined('HT_KB_THEME_MANAGED_UPDATES')){
		define('HT_KB_THEME_MANAGED_UPDATES', true);
}

if ( ! function_exists( 'ht_theme_setup' ) ):
/**
* Sets up theme defaults and registers support for various WordPress features.
*/
function ht_theme_setup() {
		
	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 */
	load_theme_textdomain( 'helpguru', get_template_directory() . '/languages' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary-nav' => __( 'Primary Navigation', 'helpguru' )
	));
	
	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, and column width.
	 */
	//add_editor_style( array( 'css/editor-style.css', ht_google_font_url() ) );
	add_editor_style( 'css/editor-style.css' );

	$font_url = str_replace( ',', '%2C', '//fonts.googleapis.com/css?family=Open+Sans:400italic,400,600,700|Nunito:400,700' );
    add_editor_style( $font_url );
	
	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 60, 60, true );	
	add_image_size( 'post', 920, '', true );
	add_image_size( 'post-mid', 600, '', true );
	add_image_size( 'post-small', 320, '', true );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );


	// Enable HTML5 markup
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

	// Enable Custom BG
	$ht_custom_background_defaults = array(
		'default-color'          => 'cccccc',
		'default-image'          => '',
		'default-repeat'         => '',
		'default-position-x'     => '',
		'wp-head-callback'       => '_custom_background_cb',
		'admin-head-callback'    => '',
		'admin-preview-callback' => ''
	);
	add_theme_support( 'custom-background', $ht_custom_background_defaults );

	add_theme_support( 'hero-voting-frontend-styles' );
	add_theme_support( 'ht-knowledge-base-templates' );
	add_theme_support( 'ht-kb-category-icons' );
	add_theme_support( 'ht-kb-theme-managed-updates' );
	add_theme_support( 'ht-kb-theme-voting-suggested' );

	// Add HT Posts Widget Support
	add_theme_support( 'ht_posts_widget_styles' );

	// Add post-formats to Knowledge Base
	add_theme_support( 'post-formats', array( 'video' ) );

	// Load HT Core (must be loaded last in ht_theme_setup)
	add_theme_support( 'ht-core', 'font-awesome', 'custom-customizer' );
	require_if_theme_supports( 'ht-core', get_template_directory() . '/inc/ht-core/ht-core.php' );
	
}
endif; // ht_theme_setup
add_action( 'after_setup_theme', 'ht_theme_setup' );

add_filter( 'ht_post_formats_post_types', 'ht_post_formats_post_types_supported');

function ht_post_formats_post_types_supported($supported){
	if(is_array($supported)){
		//could remove
		if (($key = array_search('post', $supported)) !== false) {
    		unset($supported[$key]);
		}
	}

	array_push($supported, 'ht_kb');

	return $supported;
}

/**
* Enqueues scripts and styles for front-end.
*/
 
require("inc/scripts.php");
require("inc/styles.php");

/**
 * Register widgetized & Add Widgets
 */
require("inc/register-sidebars.php");


// Meta Boxes Framework
require("inc/meta-boxes.php");

/**
* Add Template Navigation Functions
*/
require("inc/template-tags.php");

/**
* Comment Functions
*/
require("inc/comment-functions.php");

/**
* Theme Customizer Functions
*/
require("inc/theme-customizer.php");


// Inlcude TGM Plugins
require("inc/tgm-load-plugins.php");


// Return logo src
if ( ! function_exists( 'ht_theme_logo' ) ) {
	function ht_theme_logo() {

		$ht_theme_logo = get_theme_mod( 'ht_site_logo' );
		
		if ( !empty($ht_theme_logo) ) {
			$ht_theme_logo_src = get_theme_mod( 'ht_site_logo' );
		} else {
			$ht_theme_logo_src = get_template_directory_uri()."/images/logo.png";
		}
		return $ht_theme_logo_src;

	}
}


// Change default widget tag cloud settings
add_filter('widget_tag_cloud_args','ht_set_tag_cloud_args');
function ht_set_tag_cloud_args($args) {
	$args['largest'] = 16;
	$args['smallest'] = 10;
	$args['unit'] = 'px';
	return $args;
}

// Add support for WP 4.1 title tag
if ( ! function_exists( '_wp_render_title_tag' ) ) :
	function theme_slug_render_title() {
		?>
			<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'theme_slug_render_title' );
endif;

//fix for bbpress user profile page not found issue
function ht_fix_bbpress_404(){
    global $wp_query;
    if( function_exists('bbp_is_single_user') && bbp_is_single_user() && isset($wp_query) ){
        $wp_query->is_404 = false;
    }
}

add_action( 'template_redirect', 'ht_fix_bbpress_404' );


//analytics not supported in HelpGuru
add_filter('hkb_analytics_supported', '__return_false');

/**
 * Generates custom logout URL
 */
function getLogoutUrl($redirectUrl = ''){
    if(!$redirectUrl) $redirectUrl = site_url();
    $return = str_replace("&amp;", '&', wp_logout_url($redirectUrl));
    return $return;
}

/**
 * Bypass logout confirmation on nonce verification failure
 */
function logout_without_confirmation($action, $result){
    if(!$result && ($action == 'log-out')){ 
        wp_safe_redirect(getLogoutUrl()); 
        exit(); 
    }
}
add_action( 'check_admin_referer', 'logout_without_confirmation', 1, 2);

/* Main redirection of the default login page */
function redirect_login_page() {
	$login_page  = home_url('/login/');
	$page_viewed = basename($_SERVER['REQUEST_URI']);

	if($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
		wp_redirect($login_page);
		exit;
	}
}
add_action('init','redirect_login_page');

/* Where to go if a login failed */
function custom_login_failed() {
	$login_page  = home_url('/login/');
	wp_redirect($login_page . '?login=failed');
	exit;
}
add_action('wp_login_failed', 'custom_login_failed');

/* Where to go if any of the fields were empty */
function verify_user_pass($user, $username, $password) {
	$login_page  = home_url('/login/');
	if($username == "" || $password == "") {
		wp_redirect($login_page . "?login=empty");
		exit;
	}
}
add_filter('authenticate', 'verify_user_pass', 1, 3);

/* What to do on logout */
function logout_redirect() {
	$login_page  = home_url('/login/');
	wp_redirect($login_page . "?login=false");
	exit;
}
add_action('wp_logout','logout_redirect');


function custom_str( $string, $length = 200 ) {

    if ( empty( $string ) ) {
        return;
    }

    if ( strlen( $string ) <= $length ) {
        return $string;
    }

    return substr( $string, 0, $length ) . '...';
}


function meks_which_template_is_loaded() {

        global $template;
        print_r( $template );

}

//add_action( 'wp_footer', 'meks_which_template_is_loaded' );

if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title' 	=> 'Theme General Settings',
        'menu_title'	=> 'Theme Settings',
        'menu_slug' 	=> 'theme-general-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));

    acf_add_options_sub_page(array(
        'page_title' 	=> 'Navigation',
        'menu_title'	=> 'Navigation',
        'parent_slug'	=> 'theme-general-settings',
    ));
}

function ht_get_categories(){
        $categories = wp_get_post_categories(get_post()->ID);
        $x = count($categories);
        $p = 0;
        foreach ($categories as $cat){
            $link = get_category_link($cat);
            $name = get_category($cat)->name;
            echo "<a href='$link' rel='category tag'>$name</a>";
            if ($p != $x -1){
                echo ' / ';
            }
            $p+=1;
        }
}

/**
 * Prepend category base to url when requesting a category page (category.php)
 * @return void
 */
function category_redirect(){
    if (!function_exists('acf_get_current_url')){
        return;
    }
    if (is_category()){
        $url = acf_get_current_url();
        $cat_base = (get_option('category_base')) ? get_option('category_base') : 'category' ;
        if (!strpos($url, $cat_base)){
            $prefix = $cat_base . '/' . pathinfo($url)['basename'];
            $redirect_url = str_replace(pathinfo($url)['basename'], $prefix, $url);
            wp_redirect($redirect_url);
            exit();
        }
    }
}
add_action( 'template_redirect', 'category_redirect' );

/**
 * Redirect knowledge base requests (URL with 'article-categories')
 * @return void
 */
function knowledge_base_redirect(){
    if (!function_exists('acf_get_current_url')){
        return;
    }
    $url = acf_get_current_url();
    if (is_archive() && strpos($url, 'article-categories')){
        wp_redirect(str_replace('/article-categories', '', $url));
        exit();
    }
}
add_action( 'template_redirect', 'knowledge_base_redirect' );
