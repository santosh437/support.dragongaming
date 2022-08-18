<?php
/**
* HT Core - Post Format Module
*/

$ht_support_pf = '';

/*
* Load Post Format Admin Script
*/
function ht_pf_js($hook) {
	if ($hook == 'post.php' || $hook == 'post-new.php') {
		wp_enqueue_script('ht_pf-post-meta', get_template_directory_uri() . '/inc/ht-core/js/ht-post-formats.js', array( 'jquery' ));
	}
}
add_action('admin_enqueue_scripts','ht_pf_js',10,1);


if ( current_theme_supports( 'post-formats', 'gallery' ) ) {
	add_filter( 'cmb2_meta_boxes', 'ht_pf_metabox_gallery' );
}
if ( current_theme_supports( 'post-formats', 'image' ) ) {
	add_filter( 'cmb2_meta_boxes', 'ht_pf_metabox_image' );
}
if ( current_theme_supports( 'post-formats', 'link' ) ) {
	add_filter( 'cmb2_meta_boxes', 'ht_pf_metabox_link' );
}
if ( current_theme_supports( 'post-formats', 'quote' ) ) {
	add_filter( 'cmb2_meta_boxes', 'ht_pf_metabox_quote' );
}
if ( current_theme_supports( 'post-formats', 'status' ) ) {
	add_filter( 'cmb2_meta_boxes', 'ht_pf_metabox_status' );
}
if ( current_theme_supports( 'post-formats', 'video' ) ) {
	add_filter( 'cmb2_meta_boxes', 'ht_pf_metabox_video' );
}
if ( current_theme_supports( 'post-formats', 'audio' ) ) {
	add_filter( 'cmb2_meta_boxes', 'ht_pf_metabox_audio' );
}
if ( current_theme_supports( 'post-formats', 'chat' ) ) {
	add_filter( 'cmb2_meta_boxes', 'ht_pf_metabox_chat' );
}

/**
* Gallery Meta Box
*/
function ht_pf_metabox_gallery( array $meta_boxes ) {

	$prefix = '_ht_pf_gallery_';

	$default_supported_post_types = array('post');
	$supported_post_types = apply_filters('ht_post_formats_post_types', $default_supported_post_types );

	$meta_boxes['ht_pf_metabox_gallery'] = array(
		'id'         => 'ht_pf_metabox_gallery',
		'title'      => __( 'Gallery Post Format', 'helpguru' ),
		'object_types'      => $supported_post_types,
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'cmb_styles' => true,
		'fields'     => array(
			array(
				'name'         => __( 'Upload Images', 'helpguru' ),
				'desc'         => __( 'Upload or add multiple images/attachments.', 'helpguru' ),
				'id'           => $prefix . 'upload',
				'type'         => 'file_list',
				'preview_size' => array( 200, 200 ), // Default: array( 50, 50 )
			),
		),
	);

	return $meta_boxes;
}

/**
* Image Meta Box
*/
function ht_pf_metabox_image( array $meta_boxes ) {

	$prefix = '_ht_pf_image_';

	$default_supported_post_types = array('post');
	$supported_post_types = apply_filters('ht_post_formats_post_types', $default_supported_post_types );

	$meta_boxes['ht_pf_metabox_image'] = array(
		'id'         => 'ht_pf_metabox_image',
		'title'      => __( 'Image Post Format', 'helpguru' ),
		'object_types'      => $supported_post_types,
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'cmb_styles' => true,
		'fields'     => array(
			array(
				'name'         => __( 'Upload Image', 'helpguru' ),
				'desc'         => __( 'Select your image using the Featured Image box below', 'helpguru' ),
				'id'           => $prefix . 'image',
				'type'         => 'title',
			),
		),
	);

	return $meta_boxes;
}


/**
* Link Meta Box
*/
function ht_pf_metabox_link( array $meta_boxes ) {

	$prefix = '_ht_pf_link_';

	$default_supported_post_types = array('post');
	$supported_post_types = apply_filters('ht_post_formats_post_types', $default_supported_post_types );

	$meta_boxes['ht_pf_metabox_link'] = array(
		'id'         => 'ht_pf_metabox_link',
		'title'      => __( 'Link Post Format', 'helpguru' ),
		'object_types'      => $supported_post_types,
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'cmb_styles' => true,
		'fields'     => array(
			array(
				'name' => __( 'Link URL', 'helpguru' ),
				'desc' => __( 'Enter the URL of the link', 'helpguru' ),
				'id' => $prefix . 'url',
				'type' => 'text_url',
				// 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Link Title', 'helpguru' ),
				'desc' => __( 'Enter a title for the link', 'helpguru' ),
				'id' => $prefix . 'name',
				'type' => 'text',
			),
		),
	);

	return $meta_boxes;
}


/**
* Image Meta Box
*/

// No meta box needed - featured image is used instead

/**
* Quote Meta Box
*/
function ht_pf_metabox_quote( array $meta_boxes ) {

	$prefix = '_ht_pf_quote_';

	$default_supported_post_types = array('post');
	$supported_post_types = apply_filters('ht_post_formats_post_types', $default_supported_post_types );

	$meta_boxes['ht_pf_metabox_quote'] = array(
		'id'         => 'ht_pf_metabox_quote',
		'title'      => __( 'Quote Post Format', 'helpguru' ),
		'object_types'      => $supported_post_types,
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'cmb_styles' => true,	
		'fields'     => array(
			array(
				'name' => __( 'Quote', 'helpguru' ),
				'desc' => __( 'The quote text', 'helpguru' ),
				'id' => $prefix . 'test_textareasmall',
				'type' => 'textarea_small',
			),
			array(
				'name' => __( 'Quote Author', 'helpguru' ),
				'desc' => __( 'The quote attribution', 'helpguru' ),
				'id' => $prefix . 'test_textmedium',
				'type' => 'text_medium',
			),
		),
	);

	return $meta_boxes;
}


/**
* Status Meta Box
*/
function ht_pf_metabox_status( array $meta_boxes ) {

	$prefix = '_ht_pf_status_';

	$default_supported_post_types = array('post');
	$supported_post_types = apply_filters('ht_post_formats_post_types', $default_supported_post_types );

	$meta_boxes['ht_pf_metabox_status'] = array(
		'id'         => 'ht_pf_metabox_status',
		'title'      => __( 'Status Post Format', 'helpguru' ),
		'object_types'      => $supported_post_types,
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'cmb_styles' => true,
		'fields'     => array(
			array(
				'name' => __( 'Status Type', 'helpguru' ),
				'desc' => __( 'Select the type of status you wish to use', 'helpguru' ),
				'id' => $prefix . 'picker',
				'type' => 'radio_inline',
				'options' => array(
					'oembed' => __( 'oEmbed', 'helpguru' ),
					'custom' => __( 'Custom', 'helpguru' ),
				),
			),
			array(
				'name' => __( 'Status oEmbed', 'helpguru' ),
				'desc' => __( 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'helpguru' ),
				'id' => $prefix . 'oembed',
				'type' => 'oembed',
			),
			array(
				'name' => __( 'Custom Status', 'helpguru' ),
				'desc' => __( 'field description (optional)', 'helpguru' ),
				'id' => $prefix . 'custom',
				'type' => 'textarea_small',
			),
		),
	);

	return $meta_boxes;
}


/**
* Video Meta Box
*/
function ht_pf_metabox_video( array $meta_boxes ) {

	$prefix = '_ht_pf_video_';

	$default_supported_post_types = array('post');
	$supported_post_types = apply_filters('ht_post_formats_post_types', $default_supported_post_types );

	$meta_boxes['ht_pf_metabox_video'] = array(
		'id'         => 'ht_pf_metabox_video',
		'title'      => __( 'Video Post Format', 'helpguru' ),
		'object_types'      => $supported_post_types,
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'cmb_styles' => true,
		'fields'     => array(
			array(
				'name' => __( 'Video Type', 'helpguru' ),
				'desc' => __( 'Select the type of video you wish to use', 'helpguru' ),
				'id' => $prefix . 'picker',
				'type' => 'radio_inline',
				'options' => array(
					'oembed' => __( 'oEmbed', 'helpguru' ),
					'custom' => __( 'Custom', 'helpguru' ),
				),
			),
			array(
				'name' => __( 'Video Upload', 'helpguru' ),
				'desc' => __( 'Upload a video', 'helpguru' ),
				'id' => $prefix . 'upload',
				'type' => 'file',
			),
			array(
				'name' => __( 'Video oEmbed', 'helpguru' ),
				'desc' => __( 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'helpguru' ),
				'id' => $prefix . 'oembed',
				'type' => 'oembed',
			),
		),
	);

	return $meta_boxes;
}


/**
* Audio Meta Box
*/
function ht_pf_metabox_audio( array $meta_boxes ) {

	$prefix = '_ht_pf_audio_';

	$default_supported_post_types = array('post');
	$supported_post_types = apply_filters('ht_post_formats_post_types', $default_supported_post_types );

	$meta_boxes['ht_pf_metabox_audio'] = array(
		'id'         => 'ht_pf_metabox_audio',
		'title'      => __( 'Audio Format', 'helpguru' ),
		'object_types'      => $supported_post_types,
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'cmb_styles' => true,
		'fields'     => array(
			array(
				'name' => __( 'Audio Type', 'helpguru' ),
				'desc' => __( 'Select the type of audio you wish to use', 'helpguru' ),
				'id' => $prefix . 'picker',
				'type' => 'radio_inline',
				'options' => array(
					'oembed' => __( 'oEmbed', 'helpguru' ),
					'custom' => __( 'Custom', 'helpguru' ),
				),
			),
			array(
				'name' => __( 'Audio Upload', 'helpguru' ),
				'desc' => __( 'Upload a audio file', 'helpguru' ),
				'id' => $prefix . 'upload',
				'type' => 'file',
			),
			array(
				'name' => __( 'Audio oEmbed', 'helpguru' ),
				'desc' => __( 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'helpguru' ),
				'id' => $prefix . 'oembed',
				'type' => 'oembed',
			),
		),
	);

	return $meta_boxes;
}


/**
* Chat Meta Box
*/
function ht_pf_metabox_chat( array $meta_boxes ) {

	$prefix = '_ht_pf_chat_';

	$default_supported_post_types = array('post');
	$supported_post_types = apply_filters('ht_post_formats_post_types', $default_supported_post_types );

	$meta_boxes['ht_pf_metabox_chat'] = array(
		'id'         => 'ht_pf_metabox_chat',
		'title'      => __( 'Chat Post Format', 'helpguru' ),
		'object_types'      => $supported_post_types,
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true,
		'cmb_styles' => true,
		'fields'     => array(
			array(
				'name' => __( 'Chat Transcript', 'helpguru' ),
				'desc' => __( 'The transcript of the chat', 'helpguru' ),
				'id' => $prefix . 'transcript',
				'type' => 'textarea',
				),
		),
	);

	return $meta_boxes;
}