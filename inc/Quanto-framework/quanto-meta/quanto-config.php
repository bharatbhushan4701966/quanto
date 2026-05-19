<?php

/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 *
 * Be sure to replace all instances of 'yourprefix_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Demo_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

 /**
 * Only return default value if we don't have a post ID (in the 'post' query variable)
 *
 * @param  bool  $default On/Off (true/false)
 * @return mixed          Returns true or '', the blank default
 */
function quanto_set_checkbox_default_for_new_post( $default ) {
	return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
}

add_action( 'cmb2_admin_init', 'quanto_register_metabox' );

/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */

function quanto_register_metabox() {

	$prefix = '_quanto_';

	$prefixpage = '_quantopage_';

	$quanto_post_meta = new_cmb2_box( array(
		'id'            => $prefixpage . 'blog_post_control',
		'title'         => esc_html__( 'Post Thumb Controller', 'quanto' ),
		'object_types'  => array( 'post' ), // Post type
		'closed'        => true
	) );
	$quanto_post_meta->add_field( array(
		'name' => esc_html__( 'Post Format Video', 'quanto' ),
		'desc' => esc_html__( 'Use This Field When Post Format Video', 'quanto' ),
		'id'   => $prefix . 'post_format_video',
        'type' => 'text_url',
    ) );
	$quanto_post_meta->add_field( array(
		'name' => esc_html__( 'Post Format Audio', 'quanto' ),
		'desc' => esc_html__( 'Use This Field When Post Format Audio', 'quanto' ),
		'id'   => $prefix . 'post_format_audio',
        'type' => 'oembed',
    ) );
	$quanto_post_meta->add_field( array(
		'name' => esc_html__( 'Post Thumbnail For Slider', 'quanto' ),
		'desc' => esc_html__( 'Use This Field When You Want A Slider In Post Thumbnail', 'quanto' ),
		'id'   => $prefix . 'post_format_slider',
        'type' => 'file_list',
    ) );

	$quanto_page_meta = new_cmb2_box( array(
		'id'            => $prefixpage . 'page_meta_section',
		'title'         => esc_html__( 'Page Meta', 'quanto' ),
		'object_types'  => array( 'page' ), // Post type
        'closed'        => true
    ) );

    $quanto_page_meta->add_field( array(
		'name' => esc_html__( 'Page Breadcrumb Area', 'quanto' ),
		'desc' => esc_html__( 'check to display page breadcrumb area.', 'quanto' ),
		'id'   => $prefix . 'page_breadcrumb_area',
        'type' => 'select',
        'default' => '1',
        'options'   => array(
            '1'   => esc_html__('Show','quanto'),
            '2'     => esc_html__('Hide','quanto'),
        )
    ) );


    $quanto_page_meta->add_field( array(
		'name' => esc_html__( 'Page Breadcrumb Settings', 'quanto' ),
		'id'   => $prefix . 'page_breadcrumb_settings',
        'type' => 'select',
        'default'   => 'global',
        'options'   => array(
            'global'   => esc_html__( 'Global Settings', 'quanto' ),
            'page'     => esc_html__( 'Page Settings', 'quanto' ),
        )
	) );

	$quanto_page_meta->add_field( array(
	    'name'    => esc_html__( 'Breadcumb Image', 'quanto' ),
	    'desc'    => esc_html__( 'Upload an image or enter an URL.', 'quanto' ),
	    'id'      => $prefix . 'breadcumb_image',
	    'type'    => 'file',
	    // Optional:
	    'options' => array(
	        'url' => false, // Hide the text input for the url
	    ),
	    'text'    => array(
	        'add_upload_file_text' => __( 'Add File', 'quanto' ) // Change upload button text. Default: "Add or Upload File"
	    ),
	    'preview_size' => 'large', // Image size to use when previewing in the admin.
	) );

    $quanto_page_meta->add_field( array(
		'name' => esc_html__( 'Page Title', 'quanto' ),
		'desc' => esc_html__( 'check to display Page Title.', 'quanto' ),
		'id'   => $prefix . 'page_title',
        'type' => 'select',
        'default' => '1',
        'options'   => array(
            '1'   	=> esc_html__( 'Show','quanto'),
            '2'     => esc_html__( 'Hide','quanto'),
        )
	) );

    $quanto_page_meta->add_field( array(
		'name' => esc_html__( 'Page Title Settings', 'quanto' ),
		'id'   => $prefix . 'page_title_settings',
        'type' => 'select',
        'options'   => array(
            'default'  => esc_html__('Default Title','quanto'),
            'custom'  => esc_html__('Custom Title','quanto'),
        ),
        'default'   => 'default'
    ) );

    $quanto_page_meta->add_field( array(
		'name' => esc_html__( 'Custom Page Title', 'quanto' ),
		'id'   => $prefix . 'custom_page_title',
        'type' => 'text'
    ) );

    $quanto_page_meta->add_field( array(
		'name' => esc_html__( 'Breadcrumb', 'quanto' ),
		'desc' => esc_html__( 'Select Show to display breadcrumb area', 'quanto' ),
		'id'   => $prefix . 'page_breadcrumb_trigger',
        'type' => 'switch_btn',
        'default' => quanto_set_checkbox_default_for_new_post( true ),
    ) );

    $quanto_layout_meta = new_cmb2_box( array(
		'id'            => $prefixpage . 'page_layout_section',
		'title'         => esc_html__( 'Page Layout', 'quanto' ),
        'context' 		=> 'side',
        'priority' 		=> 'high',
        'object_types'  => array( 'page' ), // Post type
        'closed'        => true
	) );

	$quanto_layout_meta->add_field( array(
		'desc'       => esc_html__( 'Set page layout container,container fluid,fullwidth or both. It\'s work only in template builder page.', 'quanto' ),
		'id'         => $prefix . 'custom_page_layout',
		'type'       => 'radio',
        'options' => array(
            '1' => esc_html__( 'Container', 'quanto' ),
            '2' => esc_html__( 'Container Fluid', 'quanto' ),
            '3' => esc_html__( 'Fullwidth', 'quanto' ),
        ),
	) );

	$quanto_product_meta = new_cmb2_box( array(
		'id'            => $prefixpage . 'product_meta_section',
		'title'         => esc_html__( 'Swap Image', 'quanto' ),
		'object_types'  => array( 'product' ), // Post type
		'closed'        => true,
		'context'		=> 'side',
		'priority'		=> 'default'
	) );

	$quanto_product_meta->add_field( array(
		'name' 		=> esc_html__( 'Product Swap Image', 'quanto' ),
		'desc' 		=> esc_html__( 'Set Product Swap Image', 'quanto' ),
		'id'   		=> $prefix.'product_swap_image',
		'type'    	=> 'file',
		// Optional:
		'options' 	=> array(
			'url' 		=> false, // Hide the text input for the url
		),
		'text'    	=> array(
			'add_upload_file_text' => __( 'Add Swap Image', 'quanto' ) // Change upload button text. Default: "Add or Upload File"
		),
	) );
}

add_action( 'cmb2_admin_init', 'quanto_register_taxonomy_metabox' );
/**
 * Hook in and add a metabox to add fields to taxonomy terms
 */
function quanto_register_taxonomy_metabox() {

    $prefix = '_quanto_';
	/**
	 * Metabox to add fields to categories and tags
	 */
	$quanto_term_meta = new_cmb2_box( array(
		'id'               => $prefix.'term_edit',
		'title'            => esc_html__( 'Category Metabox', 'quanto' ),
		'object_types'     => array( 'term' ),
		'taxonomies'       => array( 'category'),
	) );
	$quanto_term_meta->add_field( array(
		'name'     => esc_html__( 'Extra Info', 'quanto' ),
		'id'       => $prefix.'term_extra_info',
		'type'     => 'title',
		'on_front' => false,
	) );
	$quanto_term_meta->add_field( array(
		'name' => esc_html__( 'Category Image', 'quanto' ),
		'desc' => esc_html__( 'Set Category Image', 'quanto' ),
		'id'   => $prefix.'term_avatar',
        'type' => 'file',
        'text'    => array(
			'add_upload_file_text' => esc_html__('Add Image','quanto') // Change upload button text. Default: "Add or Upload File"
		),
	) );



	/**
	 * Metabox to add fields to events
	 */
	$quanto_term_events = new_cmb2_box( array(
		'id'               => $prefix.'events',
		'title'            => esc_html__( 'Events Metabox', 'quanto' ),
		'object_types'     => array( 'quanto_event' ),
	) );
	$quanto_term_events->add_field( array(
		'name'     => esc_html__( 'Event Date', 'quanto' ),
		'id'       => $prefix.'event_date',
		'type'     => 'text_date_timestamp',
	) );
	$quanto_term_events->add_field( array(
		'name'     => esc_html__( 'Event Time', 'quanto' ),
		'id'       => $prefix.'event_time',
		'type'     => 'text',
		'default'  => esc_html__( '8:00 AM - 5:00 PM','quanto' ),
	) );
	$quanto_term_events->add_field( array(
		'name'     => esc_html__( 'Event Type', 'quanto' ),
		'id'       => $prefix.'event_type',
		'type'     => 'text',
		'default'  => esc_html__( 'Online','quanto' ),
	) );
	$quanto_term_events->add_field( array(
		'name'     => esc_html__( 'Event Speaker', 'quanto' ),
		'id'       => $prefix.'event_speaker',
		'type'     => 'text',
		'default'  => esc_html__( '20 Speaker','quanto' ),
	) );

	

	/**
	 * Metabox to add fields to class widget
	 */
	
	 $quanto_term_class = new_cmb2_box( array(
		'id'               => $prefix.'class',
		'title'            => esc_html__( 'class Metabox', 'quanto' ),
		'object_types'     => array( 'quanto_class' ),
	) );

	$quanto_term_class->add_field( array(
		'name'     => esc_html__( 'Class Info', 'quanto' ),
		'id'       => $prefix.'class_list',
		'type'     => 'wysiwyg',
		'default'  => esc_html__( '11 - 13 Years','quanto' ),
	) );


	$quanto_term_class->add_field( array(
		'name'     => esc_html__( 'Price', 'quanto' ),
		'id'       => $prefix.'class_price',
		'type'     => 'text',
		'default'  => esc_html__( '$29','quanto' ),
	) );
	
	$quanto_term_class->add_field( array(
		'name'     => esc_html__( 'Duration', 'quanto' ),
		'id'       => $prefix.'class_duration',
		'type'     => 'text',
		'default'  => esc_html__( '/ month','quanto' ),
	) );


	/**
	 * Metabox to add fields to Teachers widget
	 */
	
	 $quanto_term_teachers = new_cmb2_box( array(
		'id'               => $prefix.'teachers',
		'title'            => esc_html__( 'Teachers Metabox', 'quanto' ),
		'object_types'     => array( 'quanto_teacher' ),
	) );

	$quanto_term_teachers->add_field( array(
		'name'     => esc_html__( 'Designation', 'quanto' ),
		'id'       => $prefix.'teachers_designation',
		'type'     => 'text',
		'default'  => esc_html__( 'Principal and Manager','quanto' ),
	) );

	$quanto_term_teachers->add_field( array(
		'name'     => esc_html__( 'Phone Number', 'quanto' ),
		'id'       => $prefix.'teachers_number',
		'type'     => 'text',
		'default'  => esc_html__( '+44 (0) 207 689 7888','quanto' ),
	) );

	$profiles_group = $quanto_term_teachers->add_field( array(
        'id'          => $prefix . 'profiles',
        'type'        => 'group',
        'description' => __( 'Add social profiles', 'quanto' ),
        'options'     => array(
            'group_title'   => __( 'Profile {#}', 'quanto' ),
            'add_button'    => __( 'Add Another Profile', 'quanto' ),
            'remove_button' => __( 'Remove Profile', 'quanto' ),
            'sortable'      => true,
        ),
    ) );

    // Add fields to the repeatable group
    $quanto_term_teachers->add_group_field( $profiles_group, array(
        'name' => __( 'Social Network', 'quanto' ),
        'id'   => 'network',
        'type' => 'text',
    ) );

    $quanto_term_teachers->add_group_field( $profiles_group, array(
        'name' => __( 'Profile URL', 'quanto' ),
        'id'   => 'url',
        'type' => 'text_url',
    ) );

}
