<?php
/**
 * CMR News & Media Releases Feature
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Register Custom Post Type and Taxonomy
add_action( 'init', 'cmr_news_register_post_type' );
function cmr_news_register_post_type() {
    $labels = array(
        'name'                  => 'CMR News',
        'singular_name'         => 'CMR News Item',
        'menu_name'             => 'CMR News',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New News Item',
        'edit_item'             => 'Edit News Item',
        'new_item'              => 'New News Item',
        'view_item'             => 'View News Item',
        'all_items'             => 'All News Items',
        'search_items'          => 'Search News',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'cmr-news' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-media-document',
        'supports'           => array( 'title', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true,
    );

    register_post_type( 'cmr_news', $args );

    $tax_labels = array(
        'name'              => 'News Categories',
        'singular_name'     => 'News Category',
        'search_items'      => 'Search Categories',
        'all_items'         => 'All Categories',
        'parent_item'       => 'Parent Category',
        'parent_item_colon' => 'Parent Category:',
        'edit_item'         => 'Edit Category',
        'update_item'       => 'Update Category',
        'add_new_item'      => 'Add New Category',
        'new_item_name'     => 'New Category Name',
        'menu_name'         => 'Categories',
    );

    $tax_args = array(
        'hierarchical'      => true,
        'labels'            => $tax_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'cmr-news-category' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'cmr_news_category', array( 'cmr_news' ), $tax_args );
}

add_action( 'init', 'cmr_news_insert_default_terms' );
function cmr_news_insert_default_terms() {
    $terms = array( 'CMR In News', 'Media Releases', 'Quarterly Results' );
    foreach ( $terms as $term ) {
        if ( ! term_exists( $term, 'cmr_news_category' ) ) {
            wp_insert_term( $term, 'cmr_news_category' );
        }
    }
}

// 2. Add Custom Meta Boxes
add_action( 'add_meta_boxes', 'cmr_news_add_meta_boxes' );
function cmr_news_add_meta_boxes() {
    add_meta_box(
        'cmr_news_meta_box',
        'News Details',
        'cmr_news_render_meta_box',
        'cmr_news',
        'normal',
        'high'
    );
}

function cmr_news_render_meta_box( $post ) {
    wp_nonce_field( 'cmr_news_save_meta_box_data', 'cmr_news_meta_box_nonce' );

    $external_link = get_post_meta( $post->ID, '_cmr_news_external_link', true );
    $source_logo_id = get_post_meta( $post->ID, '_cmr_news_source_logo_id', true );
    $source_logo_url = '';
    if ( $source_logo_id ) {
        $source_logo_url = wp_get_attachment_url( $source_logo_id );
    }
    
    $reading_time = get_post_meta( $post->ID, '_cmr_news_reading_time', true );
    $publisher_name = get_post_meta( $post->ID, '_cmr_news_publisher_name', true );
    $is_featured = get_post_meta( $post->ID, '_cmr_news_is_featured', true );
    
    ?>
    <style>
        .cmr-meta-row { margin-bottom: 15px; }
        .cmr-meta-row label { display: block; font-weight: bold; margin-bottom: 5px; }
        .cmr-meta-row input[type="text"] { width: 100%; max-width: 600px; }
        .cmr-logo-preview { max-width: 150px; margin-top: 10px; display: block; }
    </style>
    
    <div class="cmr-meta-row">
        <label for="cmr_news_is_featured" style="font-weight: normal; cursor: pointer;">
            <input type="checkbox" id="cmr_news_is_featured" name="cmr_news_is_featured" value="1" <?php checked( $is_featured, '1' ); ?> />
            <strong>Pin this to the top</strong> (Show this item first in its tab, regardless of date)
        </label>
    </div>

    <div class="cmr-meta-row">
        <label for="cmr_news_publisher_name">Publisher Name (e.g., CNN, Times of India)</label>
        <input type="text" id="cmr_news_publisher_name" name="cmr_news_publisher_name" value="<?php echo esc_attr( $publisher_name ); ?>" />
    </div>

    <div class="cmr-meta-row">
        <label for="cmr_news_external_link">External Link URL</label>
        <input type="text" id="cmr_news_external_link" name="cmr_news_external_link" value="<?php echo esc_attr( $external_link ); ?>" placeholder="https://" />
    </div>
    
    <div class="cmr-meta-row">
        <label for="cmr_news_reading_time">Reading Time (e.g., 8 mins)</label>
        <input type="text" id="cmr_news_reading_time" name="cmr_news_reading_time" value="<?php echo esc_attr( $reading_time ); ?>" placeholder="8 mins" />
    </div>

    <div class="cmr-meta-row">
        <label for="cmr_news_source_logo">Source Logo Image (Link Image)</label>
        <input type="hidden" id="cmr_news_source_logo_id" name="cmr_news_source_logo_id" value="<?php echo esc_attr( $source_logo_id ); ?>" />
        <button type="button" class="button cmr_upload_logo_btn">Select/Upload Logo</button>
        <button type="button" class="button cmr_remove_logo_btn" <?php echo ! $source_logo_id ? 'style="display:none;"' : ''; ?>>Remove Logo</button>
        <img src="<?php echo esc_url( $source_logo_url ); ?>" class="cmr-logo-preview" <?php echo ! $source_logo_id ? 'style="display:none;"' : ''; ?> />
    </div>
    
    <script>
    jQuery(document).ready(function($){
        var mediaUploader;
        $('.cmr_upload_logo_btn').click(function(e) {
            e.preventDefault();
            if (mediaUploader) { mediaUploader.open(); return; }
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Source Logo',
                button: { text: 'Choose Logo' }, multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#cmr_news_source_logo_id').val(attachment.id);
                $('.cmr-logo-preview').attr('src', attachment.url).show();
                $('.cmr_remove_logo_btn').show();
            });
            mediaUploader.open();
        });
        $('.cmr_remove_logo_btn').click(function(e){
            e.preventDefault();
            $('#cmr_news_source_logo_id').val('');
            $('.cmr-logo-preview').attr('src', '').hide();
            $(this).hide();
        });
    });
    </script>
    <?php
}

add_action( 'save_post', 'cmr_news_save_meta_box_data' );
function cmr_news_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['cmr_news_meta_box_nonce'] ) ) { return; }
    if ( ! wp_verify_nonce( $_POST['cmr_news_meta_box_nonce'], 'cmr_news_save_meta_box_data' ) ) { return; }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

    if ( isset( $_POST['cmr_news_external_link'] ) ) {
        update_post_meta( $post_id, '_cmr_news_external_link', sanitize_text_field( $_POST['cmr_news_external_link'] ) );
    }
    if ( isset( $_POST['cmr_news_source_logo_id'] ) ) {
        update_post_meta( $post_id, '_cmr_news_source_logo_id', sanitize_text_field( $_POST['cmr_news_source_logo_id'] ) );
    }
    if ( isset( $_POST['cmr_news_reading_time'] ) ) {
        update_post_meta( $post_id, '_cmr_news_reading_time', sanitize_text_field( $_POST['cmr_news_reading_time'] ) );
    }
    if ( isset( $_POST['cmr_news_publisher_name'] ) ) {
        update_post_meta( $post_id, '_cmr_news_publisher_name', sanitize_text_field( $_POST['cmr_news_publisher_name'] ) );
    }
    
    update_post_meta( $post_id, '_cmr_news_is_featured', isset( $_POST['cmr_news_is_featured'] ) ? '1' : '0' );
}

// 3. Enqueue Admin Scripts for Media Uploader
add_action( 'admin_enqueue_scripts', 'cmr_news_admin_scripts' );
function cmr_news_admin_scripts() {
    global $typenow;
    if ( $typenow == 'cmr_news' ) {
        wp_enqueue_media();
    }
}

// 4. Register Shortcode
add_shortcode( 'cmr_news_tabs', 'cmr_news_tabs_shortcode' );
function cmr_news_tabs_shortcode( $atts ) {
    // Enqueue frontend assets using get_template_directory_uri()
    wp_enqueue_style( 'cmr-news-style', get_template_directory_uri() . '/assets/css/cmr-news.css', array(), time() );
    wp_enqueue_script( 'cmr-news-script', get_template_directory_uri() . '/assets/js/cmr-news.js', array('jquery'), time(), true );

    $terms = get_terms( array(
        'taxonomy'   => 'cmr_news_category',
        'hide_empty' => true,
    ) );

    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return '<p>No news content available.</p>';
    }

    ob_start();
    ?>
    <div class="cmr-news-container">
        <!-- Tabs -->
        <div class="cmr-news-tabs">
            <?php 
            $first = true;
            foreach ( $terms as $term ) : 
                $active_class = $first ? 'active' : '';
            ?>
                <button class="cmr-news-tab-btn <?php echo esc_attr( $active_class ); ?>" data-target="cmr-tab-<?php echo esc_attr( $term->term_id ); ?>">
                    <?php echo esc_html( $term->name ); ?>
                </button>
            <?php 
                $first = false;
            endforeach; 
            ?>
        </div>

        <!-- Tab Contents -->
        <div class="cmr-news-content-wrapper">
            <?php 
            $first = true;
            foreach ( $terms as $term ) : 
                $active_class = $first ? 'active' : '';
            ?>
                <div class="cmr-news-tab-pane <?php echo esc_attr( $active_class ); ?>" id="cmr-tab-<?php echo esc_attr( $term->term_id ); ?>">
                    <?php
                    $is_media_releases = ( $term->slug === 'media-releases' );
                    $grid_class = $is_media_releases ? 'cmr-media-grid' : 'cmr-news-grid';
                    ?>
                    <div class="<?php echo esc_attr( $grid_class ); ?>">
                        <?php
                        $target_count = $is_media_releases ? 4 : 5;
                        
                        $pinned_query = new WP_Query( array(
                            'post_type' => 'cmr_news',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'cmr_news_category',
                                    'field'    => 'term_id',
                                    'terms'    => $term->term_id,
                                )
                            ),
                            'meta_query' => array(
                                array(
                                    'key'     => '_cmr_news_is_featured',
                                    'value'   => '1',
                                    'compare' => '='
                                )
                            ),
                            'orderby' => 'date',
                            'order'   => 'DESC',
                            'posts_per_page' => $target_count,
                        ) );
                        
                        $all_posts = $pinned_query->posts;
                        $remaining = $target_count - count($all_posts);
                        
                        if ( $remaining > 0 ) {
                            $pinned_ids = wp_list_pluck( $all_posts, 'ID' );
                            $normal_args = array(
                                'post_type' => 'cmr_news',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'cmr_news_category',
                                        'field'    => 'term_id',
                                        'terms'    => $term->term_id,
                                    )
                                ),
                                'orderby' => 'date',
                                'order'   => 'DESC',
                                'posts_per_page' => $remaining,
                            );
                            if ( ! empty( $pinned_ids ) ) {
                                $normal_args['post__not_in'] = $pinned_ids;
                            }
                            $normal_query = new WP_Query( $normal_args );
                            $all_posts = array_merge( $all_posts, $normal_query->posts );
                        }

                        if ( ! empty( $all_posts ) ) {
                            $count = 0;
                            $total_posts = count( $all_posts );
                            global $post;
                            foreach ( $all_posts as $post ) {
                                setup_postdata( $post );
                                $post_id = get_the_ID();
                                $bg_image = get_the_post_thumbnail_url( $post_id, 'large' );
                                $logo_id = get_post_meta( $post_id, '_cmr_news_source_logo_id', true );
                                $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
                                $ext_link = get_post_meta( $post_id, '_cmr_news_external_link', true );
                                $reading_time = get_post_meta( $post_id, '_cmr_news_reading_time', true );
                                $publisher = get_post_meta( $post_id, '_cmr_news_publisher_name', true );
                                $date = get_the_date( 'M j, Y' );
                                
                                if ( $is_media_releases ) {
                                    if ( $count === 0 ) {
                                        // Left Featured
                                        ?>
                                        <div class="cmr-media-left">
                                            <a href="<?php echo esc_url( $ext_link ); ?>" target="_blank" class="cmr-card-link-wrapper">
                                                <div class="cmr-card-image-wrap">
                                                    <?php if ( $bg_image ) : ?>
                                                        <img src="<?php echo esc_url( $bg_image ); ?>" class="cmr-card-bg" alt="<?php the_title_attribute(); ?>">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="cmr-card-content">
                                                    <div class="cmr-card-meta">
                                                        <span class="cmr-category-tag">&mdash; Media Releases</span>
                                                    </div>
                                                    <h3 class="cmr-card-title"><?php the_title(); ?></h3>
                                                    <span class="cmr-read-coverage">More Details <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg" class="cmr-arrow-icon" alt="Arrow"></span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="cmr-media-right">
                                        <?php
                                    } else {
                                        // Right List Items
                                        ?>
                                        <div class="cmr-media-horizontal-card">
                                            <a href="<?php echo esc_url( $ext_link ); ?>" target="_blank" class="cmr-card-link-wrapper">
                                                <div class="cmr-card-image-wrap">
                                                    <?php if ( $bg_image ) : ?>
                                                        <img src="<?php echo esc_url( $bg_image ); ?>" class="cmr-card-bg" alt="<?php the_title_attribute(); ?>">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="cmr-card-content">
                                                    <div class="cmr-card-meta">
                                                        <span class="cmr-category-tag">&mdash; Media Releases</span>
                                                    </div>
                                                    <h3 class="cmr-card-title"><?php the_title(); ?></h3>
                                                    <span class="cmr-read-coverage">More Details <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg" class="cmr-arrow-icon" alt="Arrow"></span>
                                                </div>
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    if ( $count === $total_posts - 1 && $count > 0 ) {
                                        echo '</div>'; // Close cmr-media-right
                                    } elseif ( $count === 0 && $total_posts === 1 ) {
                                        echo '<div class="cmr-media-right"></div>'; // Empty right column if only 1 post
                                    }
                                } else {
                                    // Original CMR In News Layout
                                    $card_class = ( $count === 0 ) ? 'cmr-card cmr-card-featured' : 'cmr-card cmr-card-standard';
                                    ?>
                                    <div class="<?php echo esc_attr( $card_class ); ?>">
                                        <a href="<?php echo esc_url( $ext_link ); ?>" target="_blank" class="cmr-card-link-wrapper">
                                            <div class="cmr-card-image-wrap">
                                                <?php if ( $bg_image ) : ?>
                                                    <img src="<?php echo esc_url( $bg_image ); ?>" class="cmr-card-bg" alt="<?php the_title_attribute(); ?>">
                                                <?php endif; ?>
                                                <?php if ( $logo_url ) : ?>
                                                    <img src="<?php echo esc_url( $logo_url ); ?>" class="cmr-card-logo" alt="Source Logo">
                                                <?php endif; ?>
                                            </div>
                                            <div class="cmr-card-content">
                                                <div class="cmr-card-meta">
                                                    <div class="cmr-meta-left">
                                                        <?php if ( $publisher ) : ?>
                                                            <span class="cmr-publisher"><?php echo esc_html( $publisher ); ?></span> <span class="cmr-separator">|</span> 
                                                        <?php endif; ?>
                                                        <span class="cmr-date">Published <?php echo esc_html( $date ); ?></span>
                                                    </div>
                                                    <?php if ( $reading_time ) : ?>
                                                        <div class="cmr-meta-right">
                                                            <span class="cmr-read-time"><?php echo esc_html( $reading_time ); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <h3 class="cmr-card-title"><?php the_title(); ?></h3>
                                                <?php
                                                $arrow_url = ( $count === 0 ) ? 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol.svg' : 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg';
                                                ?>
                                                <span class="cmr-read-coverage">Read Coverage <img src="<?php echo esc_url($arrow_url); ?>" class="cmr-arrow-icon" alt="Arrow"></span>
                                            </div>
                                        </a>
                                    </div>
                                    <?php
                                }
                                $count++;
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </div>
                </div>
            <?php 
                $first = false;
            endforeach; 
            ?>
        </div>
        
        <div class="cmr-news-footer">
            <a href="#" class="cmr-explore-all">Explore All ↗</a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
