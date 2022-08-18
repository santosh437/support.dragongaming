<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'cmb_meta_boxes', 'ht_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 * @deprecated - see ht_metaboxes_cmb2
 * DEPRECATED - SEE FUNCTION BELOW
 */
function ht_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_ht_';
	
	// Get deafult values
	$ht_sidebar_position_default = "sidebar-right";  

	$meta_boxes['ht_metabox_page_options'] = array(
		'id'         => 'ht_metabox_page_options',
		'title'      => __( 'Page Options', 'helpguru' ),
		'pages'      => array( 'page', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
                'name' => __( 'Sidebar Position', 'helpguru' ),
                'desc' => '',
                'id' => $prefix . 'sidebar_pos',
				'std' => $ht_sidebar_position_default,
                'type' => 'select',
                'options' => array(
                        array( 'name' => __( 'Sidebar Right', 'helpguru' ), 'value' => 'sidebar-right', ),
                        array( 'name' => __( 'Sidebar Left', 'helpguru' ), 'value' => 'sidebar-left', ),
                    	array( 'name' => __( 'Sidebar Off', 'helpguru' ), 'value' => 'sidebar-off', ),
                	),
                ),
		),
	);



	// Add other metaboxes as needed

	return $meta_boxes;
}

add_filter( 'cmb2_meta_boxes', 'ht_metaboxes_cmb2' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 * @deprecated - see ht_metaboxes_cmb2
 */
function ht_metaboxes_cmb2( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_ht_';
	
	// Get deafult values
	$ht_sidebar_position_default = "sidebar-right";  

	$meta_boxes['ht_metabox_page_options'] = array(
		'id'         => 'ht_metabox_page_options',
		'title'      => __( 'Page Options', 'helpguru' ),
		'object_types'  => array( 'page', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
                'name' => __( 'Sidebar Position', 'helpguru' ),
                'desc' => '',
                'id' => $prefix . 'sidebar_pos',
				'std' => $ht_sidebar_position_default,
                'type' => 'select',
                'options' => array(
                		'sidebar-right' => __( 'Sidebar Right', 'helpguru' ),
                		'sidebar-left' => __( 'Sidebar Left', 'helpguru' ),
                		'sidebar-off' => __( 'Sidebar Off', 'helpguru' ),
                	),
                ),
		),
	);



	// Add other metaboxes as needed

	return $meta_boxes;
}