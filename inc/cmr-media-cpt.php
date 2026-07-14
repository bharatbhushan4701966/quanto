<?php
/**
 * CMR Media Custom Post Type & Meta Boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Register Custom Post Type
function cmr_register_media_cpt() {
    $labels = array(
        'name'                  => _x( 'CMR Media', 'Post Type General Name', 'quanto' ),
        'singular_name'         => _x( 'CMR Media', 'Post Type Singular Name', 'quanto' ),
        'menu_name'             => __( 'CMR Media', 'quanto' ),
        'name_admin_bar'        => __( 'CMR Media', 'quanto' ),
        'archives'              => __( 'Media Archives', 'quanto' ),
        'attributes'            => __( 'Media Attributes', 'quanto' ),
        'parent_item_colon'     => __( 'Parent Media:', 'quanto' ),
        'all_items'             => __( 'All Media', 'quanto' ),
        'add_new_item'          => __( 'Add New Media', 'quanto' ),
        'add_new'               => __( 'Add New', 'quanto' ),
        'new_item'              => __( 'New Media', 'quanto' ),
        'edit_item'             => __( 'Edit Media', 'quanto' ),
        'update_item'           => __( 'Update Media', 'quanto' ),
        'view_item'             => __( 'View Media', 'quanto' ),
        'view_items'            => __( 'View Media', 'quanto' ),
        'search_items'          => __( 'Search Media', 'quanto' ),
        'not_found'             => __( 'Not found', 'quanto' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'quanto' ),
        'featured_image'        => __( 'Thumbnail Image', 'quanto' ),
        'set_featured_image'    => __( 'Set thumbnail image', 'quanto' ),
        'remove_featured_image' => __( 'Remove thumbnail image', 'quanto' ),
        'use_featured_image'    => __( 'Use as thumbnail image', 'quanto' ),
        'insert_into_item'      => __( 'Insert into media', 'quanto' ),
        'uploaded_to_this_item' => __( 'Uploaded to this media', 'quanto' ),
        'items_list'            => __( 'Media list', 'quanto' ),
        'items_list_navigation' => __( 'Media list navigation', 'quanto' ),
        'filter_items_list'     => __( 'Filter media list', 'quanto' ),
    );
    $args = array(
        'label'                 => __( 'CMR Media', 'quanto' ),
        'description'           => __( 'Podcasts, Videos, Top Views, Webinars', 'quanto' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-audio',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type( 'cmr_media', $args );
}
add_action( 'init', 'cmr_register_media_cpt', 0 );


// 2. Add Meta Boxes
function cmr_media_add_meta_box() {
    add_meta_box(
        'cmr_media_meta_box',
        'Media Details',
        'cmr_media_meta_box_html',
        'cmr_media',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'cmr_media_add_meta_box' );

function cmr_media_meta_box_html( $post ) {
    wp_nonce_field( 'cmr_media_meta_box_nonce', 'cmr_media_meta_box_nonce_field' );

    $media_type = get_post_meta( $post->ID, '_cmr_media_type', true );
    $media_source = get_post_meta( $post->ID, '_cmr_media_source', true );
    $media_url = get_post_meta( $post->ID, '_cmr_media_url', true );
    $media_duration = get_post_meta( $post->ID, '_cmr_media_duration', true );

    if(empty($media_type)) $media_type = 'PODCAST';
    if(empty($media_source)) $media_source = 'link';

    ?>
    <style>
        .cmr-meta-row { margin-bottom: 15px; }
        .cmr-meta-row label { display: block; font-weight: bold; margin-bottom: 5px; }
        .cmr-meta-row select, .cmr-meta-row input[type="text"] { width: 100%; max-width: 400px; }
    </style>
    
    <div class="cmr-meta-row">
        <label for="cmr_media_type">Media Format Type</label>
        <select name="cmr_media_type" id="cmr_media_type">
            <option value="PODCAST" <?php selected( $media_type, 'PODCAST' ); ?>>Podcast</option>
            <option value="TOP VIEW" <?php selected( $media_type, 'TOP VIEW' ); ?>>Top View</option>
            <option value="WEBINAR" <?php selected( $media_type, 'WEBINAR' ); ?>>Webinar</option>
            <option value="VIDEO" <?php selected( $media_type, 'VIDEO' ); ?>>Video</option>
        </select>
    </div>

    <div class="cmr-meta-row">
        <label for="cmr_media_source">Media Source</label>
        <select name="cmr_media_source" id="cmr_media_source">
            <option value="link" <?php selected( $media_source, 'link' ); ?>>External Link (YouTube, Spotify, etc.)</option>
            <option value="upload" <?php selected( $media_source, 'upload' ); ?>>File Upload (MP3, MP4, etc.)</option>
        </select>
    </div>

    <div class="cmr-meta-row">
        <label for="cmr_media_url">Media URL / File URL</label>
        <div style="display: flex; gap: 10px;">
            <input type="text" name="cmr_media_url" id="cmr_media_url" value="<?php echo esc_attr( $media_url ); ?>" />
            <button type="button" class="button cmr_media_upload_btn" id="cmr_media_upload_btn" style="<?php echo ($media_source == 'upload') ? '' : 'display:none;'; ?>">Upload / Select File</button>
        </div>
        <p class="description">Paste a link or upload a file. (Upload supports all standard WP media extensions like mp3, mp4, wav, ogg).</p>
    </div>

    <div class="cmr-meta-row">
        <label for="cmr_media_duration">Duration (e.g. 12:30 MINS)</label>
        <input type="text" name="cmr_media_duration" id="cmr_media_duration" value="<?php echo esc_attr( $media_duration ); ?>" />
    </div>

    <script>
    jQuery(document).ready(function($){
        // Toggle upload button visibility
        $('#cmr_media_source').on('change', function(){
            if($(this).val() == 'upload'){
                $('#cmr_media_upload_btn').show();
            } else {
                $('#cmr_media_upload_btn').hide();
            }
        });

        // Media Uploader
        var mediaUploader;
        $('#cmr_media_upload_btn').on('click', function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Media File',
                button: {
                    text: 'Select File'
                },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#cmr_media_url').val(attachment.url);
            });
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

// 3. Save Meta Box Data
function cmr_media_save_meta_box( $post_id ) {
    if ( ! isset( $_POST['cmr_media_meta_box_nonce_field'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['cmr_media_meta_box_nonce_field'], 'cmr_media_meta_box_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['cmr_media_type'] ) ) {
        update_post_meta( $post_id, '_cmr_media_type', sanitize_text_field( $_POST['cmr_media_type'] ) );
    }
    if ( isset( $_POST['cmr_media_source'] ) ) {
        update_post_meta( $post_id, '_cmr_media_source', sanitize_text_field( $_POST['cmr_media_source'] ) );
    }
    if ( isset( $_POST['cmr_media_url'] ) ) {
        update_post_meta( $post_id, '_cmr_media_url', esc_url_raw( $_POST['cmr_media_url'] ) );
    }
    if ( isset( $_POST['cmr_media_duration'] ) ) {
        update_post_meta( $post_id, '_cmr_media_duration', sanitize_text_field( $_POST['cmr_media_duration'] ) );
    }
}
add_action( 'save_post_cmr_media', 'cmr_media_save_meta_box' );

// 4. Enqueue Admin Scripts for Media Uploader
function cmr_media_admin_scripts($hook) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( isset($post) && 'cmr_media' === $post->post_type ) {
            wp_enqueue_media();
        }
    }
}
add_action( 'admin_enqueue_scripts', 'cmr_media_admin_scripts' );
