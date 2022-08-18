<?php

require get_stylesheet_directory() . '/sections/process.php';
require get_stylesheet_directory() . '/sections/services.php';
require get_stylesheet_directory() . '/customizer/options-process.php';
require get_stylesheet_directory() . '/customizer/options-services.php';

add_action( 'wp_enqueue_scripts', 'green_globe_chld_thm_parent_css' );
function green_globe_chld_thm_parent_css() {

    wp_enqueue_style( 
    	'green_globe_chld_css', 
    	trailingslashit( get_template_directory_uri() ) . 'style.css', 
    	array( 
    		'bootstrap',
    		'font-awesome-5',
    		'bizberg-main',
    		'bizberg-component',
    		'bizberg-style2',
    		'bizberg-responsive' 
    	) 
    );

    if ( is_rtl() ) {
        wp_enqueue_style( 
            'green_globe_parent_rtl',
            trailingslashit( get_template_directory_uri() ) . 'rtl.css'
        );
    }
    
}

add_filter( 'bizberg_sidebar_settings', 'green_globe_sidebar_settings' );
function green_globe_sidebar_settings(){
	return '4';
}

add_action( 'after_setup_theme', 'green_globe_setup_theme' );
function green_globe_setup_theme() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
}

add_filter( 'bizberg_footer_social_links' , 'green_globe_footer_social_links' );
function green_globe_footer_social_links(){
    return [];
}

add_filter( 'bizberg_theme_output_css', 'green_globe_theme_output_css' );
function green_globe_theme_output_css( $css ){
    $css[] = array(
        'element'       => '.give-form input[type="submit"], .give-donor__load_more, .nacep-ee-list .event-content input[type="submit"], .nacep-form .em-tickets-form button[type=button], .nacep-form button[type="submit"], .nacep-form input[type="submit"],.nacep-form button[type="submit"]:hover, .nacep-form button[type="submit"]:focus, .give-form input[type="submit"]:hover, .give-form input[type="submit"]:focus, .give-donor__load_more:hover, .give-donor__load_more:focus, .nacep-ee-list .event-content input[type="submit"]:hover, .nacep-ee-list .event-content input[type="submit"]:focus, .nacep-form .em-tickets-form button[type=button]:hover, .nacep-form .em-tickets-form button[type=button]:focus, .nacep-form input[type="submit"]:hover, .nacep-form input[type="submit"]:focus',
        'property'      => 'background',
        'value_pattern' => '$'
    );
    return $css;
}

add_filter( 'bizberg_recommended_plugins', 'green_globe_recommended_plugins' );
function green_globe_recommended_plugins( $plugins ){

    array_push( $plugins , array(
        'name'     => esc_html__( 'Charity Addon for Elementor', 'green-globe' ),
        'slug'     => 'charity-addon-for-elementor',
        'required' => false,
    ));

    return $plugins;

}

add_filter( 'bizberg_theme_color', 'green_globe_change_theme_color' );
add_filter( 'bizberg_header_menu_color_hover_sticky_menu', 'green_globe_change_theme_color' );
add_filter( 'bizberg_header_button_color_sticky_menu', 'green_globe_change_theme_color' );
add_filter( 'bizberg_header_button_color_hover_sticky_menu', 'green_globe_change_theme_color' );
function green_globe_change_theme_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_header_button_border_color', 'green_globe_btn_border_color' );
add_filter( 'bizberg_header_button_border_color_sticky_menu', 'green_globe_btn_border_color' );
function green_globe_btn_border_color(){
    return '#478a41';
}

add_filter( 'bizberg_header_menu_color_hover', 'green_globe_header_menu_color_hover' );
function green_globe_header_menu_color_hover(){
    return '#6ab43e';
}

add_filter( 'bizberg_header_button_color', 'green_globe_header_button_color' );
function green_globe_header_button_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_header_button_color_hover', 'green_globe_header_button_color_hover' );
function green_globe_header_button_color_hover(){
    return '#6ab43e';
}

add_filter( 'bizberg_slider_title_box_highlight_color', 'green_globe_slider_title_box_highlight_color' );
function green_globe_slider_title_box_highlight_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_slider_arrow_background_color', 'green_globe_slider_arrow_background_color' );
function green_globe_slider_arrow_background_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_slider_dot_active_color', 'green_globe_slider_dot_active_color' );
function green_globe_slider_dot_active_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_slider_gradient_primary_color', 'green_globe_slider_gradient_primary_color' );
function green_globe_slider_gradient_primary_color(){
    return 'rgba(106,180,62,0.65)';
}

add_filter( 'bizberg_read_more_background_color', 'green_globe_read_more_background_color' );
function green_globe_read_more_background_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_read_more_background_color_2', 'green_globe_read_more_background_color_2' );
function green_globe_read_more_background_color_2(){
    return '#6ab43e';
}

add_filter( 'bizberg_link_color', 'green_globe_link_color' );
function green_globe_link_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_link_color_hover', 'green_globe_link_color_hover' );
function green_globe_link_color_hover(){
    return '#6ab43e';
}

add_filter( 'bizberg_blog_listing_pagination_active_hover_color', 'green_globe_blog_listing_pagination_active_hover_color' );
function green_globe_blog_listing_pagination_active_hover_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_sidebar_widget_link_color_hover', 'green_globe_sidebar_widget_link_color_hover' );
function green_globe_sidebar_widget_link_color_hover(){
    return '#6ab43e';
}

add_filter( 'bizberg_sidebar_widget_title_color', 'green_globe_widget_title_color' );
function green_globe_widget_title_color(){
    return '#6ab43e';
}

add_filter( 'bizberg_footer_social_icon_background', 'green_globe_footer_social_icon_background' );
function green_globe_footer_social_icon_background(){
    return '#6ab43e';
}

add_filter( 'bizberg_footer_social_icon_color', 'green_globe_footer_social_icon_color' );
function green_globe_footer_social_icon_color(){
    return '#fff';
}

add_filter( 'bizberg_sidebar_spacing_status', 'green_globe_sidebar_spacing_status' );
function green_globe_sidebar_spacing_status(){
    return '0px';
}

add_filter( 'bizberg_sidebar_widget_border_color', 'green_globe_sidebar_widget_background_color' );
add_filter( 'bizberg_sidebar_widget_background_color', 'green_globe_sidebar_widget_background_color' );
function green_globe_sidebar_widget_background_color(){
    return 'rgba(251,251,251,0)';
}

add_filter( 'bizberg_three_col_listing_radius', 'green_globe_three_col_listing_radius' );
function green_globe_three_col_listing_radius(){
    return '0';
}

add_filter( 'bizberg_banner_opacity_primary_color', 'green_globe_banner_opacity_primary_color' );
function green_globe_banner_opacity_primary_color(){
    return 'rgba(0,0,0,0.3)';
}

add_filter( 'bizberg_banner_opacity_secondary_color', 'green_globe_banner_opacity_secondary_color' );
function green_globe_banner_opacity_secondary_color(){
    return 'rgba(0,0,0,0.3)';
}

add_filter( 'bizberg_transparent_header_homepage', 'green_globe_transparent_header_homepage' );
function green_globe_transparent_header_homepage(){
    return true;
}

add_filter( 'bizberg_transparent_navbar_background', 'green_globe_transparent_navbar_background' );
function green_globe_transparent_navbar_background(){
    return 'rgba(10,10,10,0)';
}

add_filter( 'bizberg_header_blur', 'green_globe_header_blur' );
function green_globe_header_blur(){
    return 0;
}

add_filter( 'bizberg_transparent_header_menu_sticky_background', 'green_globe_transparent_header_menu_sticky_background' );
function green_globe_transparent_header_menu_sticky_background(){
    return '#fff';
}

add_filter( 'bizberg_transparent_header_menu_color_hover', 'green_globe_transparent_header_menu_color_hover' );
function green_globe_transparent_header_menu_color_hover(){
    return '#6ab43e';
}

add_filter( 'bizberg_transparent_header_menu_sticky_text_color', 'green_globe_transparent_header_menu_sticky_text_color' );
function green_globe_transparent_header_menu_sticky_text_color(){
    return '#64686d';
}

add_filter( 'bizberg_transparent_header_menu_toggle_color_mobile', 'green_globe_transparent_header_menu_toggle_color_mobile' );
function green_globe_transparent_header_menu_toggle_color_mobile(){
    return '#fff';
}

add_filter( 'bizberg_banner_spacing', 'green_globe_consultant_banner_spacing' );
function green_globe_consultant_banner_spacing(){
    return [
        'padding-top'    => '150px',
        'padding-bottom' => '100px',
        'padding-left'   => '0px',
        'padding-right'  => '400px',
    ];
}

add_filter( 'bizberg_banner_image', 'green_globe_banner_image' );
function green_globe_banner_image(){
    return [
        'background-color'      => 'rgba(20,20,20,.8)',
        'background-image'      => get_stylesheet_directory_uri() . '/assets/images/StockSnap_DCXGZAZ79C.jpg',
        'background-repeat'     => 'repeat',
        'background-position'   => 'center center',
        'background-size'       => 'cover',
        'background-attachment' => 'scroll'
    ];
}

add_filter( 'bizberg_banner_title', 'green_globe_banner_title' );
function green_globe_banner_title(){
    return esc_html__( 'WELCOME TO GREEN GLOBE' , 'green-globe' );
}

add_filter( 'bizberg_banner_subtitle', 'green_globe_banner_subtitle' );
function green_globe_banner_subtitle(){
    return esc_html__( "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form by injected humour." , 'green-globe' );
}

add_filter( 'bizberg_banner_title_font_status' , 'green_globe_banner_title_font_status' );
function green_globe_banner_title_font_status(){
    return true;
}

add_filter( 'bizberg_banner_title_font_desktop' , 'green_globe_banner_title_font_desktop' );
function green_globe_banner_title_font_desktop(){
    return [
        'font-family'    => 'Poppins',
        'variant'        => '900',
        'font-size'      => '100px',
        'line-height'    => '1',
        'letter-spacing' => '0',
        'text-transform' => 'none'
    ];
}

add_filter( 'bizberg_banner_title_font_tablet' , 'green_globe_banner_title_font_tablet' );
function green_globe_banner_title_font_tablet(){
    return [
        'font-size'      => '70px',
        'line-height'    => '1',
        'letter-spacing' => '0'
    ];
}

add_filter( 'bizberg_banner_title_font_mobile' , 'green_globe_banner_title_font_mobile' );
function green_globe_banner_title_font_mobile(){
    return [
        'font-size'      => '55px',
        'line-height'    => '1',
        'letter-spacing' => '0'
    ];
}

add_filter( 'bizberg_banner_subtitle_font_status' , 'green_globe_banner_subtitle_font_status' );
function green_globe_banner_subtitle_font_status(){
    return true;
}

add_filter( 'bizberg_banner_subtitle_font_settings_desktop' , 'green_globe_banner_subtitle_font_settings_desktop' );
function green_globe_banner_subtitle_font_settings_desktop(){
    return [
        'font-family'    => 'Poppins',
        'variant'        => 'regular',
        'font-size'      => '20px',
        'line-height'    => '1.4',
        'letter-spacing' => '0',
        'text-transform' => 'none'
    ];
}

add_filter( 'bizberg_transparent_header_sticky_menu_toggle_color_mobile' , 'green_globe_transparent_header_sticky_menu_toggle_color_mobile' );
function green_globe_transparent_header_sticky_menu_toggle_color_mobile(){
    return '#434343';
}

add_filter( 'bizberg_getting_started_screenshot', 'green_globe_getting_started_screenshot' );
function green_globe_getting_started_screenshot(){
    return true;
}

add_action( 'after_switch_theme', 'green_globe_switch_theme' );
function green_globe_switch_theme() {

    $flag = get_theme_mod( 'green_globe_copy_settings', false );

    if ( true === $flag ) {
        return;
    }

    foreach( Kirki::$fields as $field ) {
        set_theme_mod( $field["settings"],$field["default"] );
    }

    //Set flag
    set_theme_mod( 'green_globe_copy_settings', true );
    
}