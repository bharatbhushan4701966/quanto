<?php
/**
 * Shortcode for Dark Media Coverage Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. AJAX Handler
add_action( 'wp_ajax_cmr_dark_media_filter', 'cmr_dark_media_filter_ajax' );
add_action( 'wp_ajax_nopriv_cmr_dark_media_filter', 'cmr_dark_media_filter_ajax' );

if ( ! function_exists( 'cmr_dark_media_filter_ajax' ) ) {
    function cmr_dark_media_filter_ajax() {
        $publisher = isset( $_POST['publisher'] ) ? sanitize_text_field( $_POST['publisher'] ) : '';
        $posts_per_page = isset( $_POST['posts_per_page'] ) ? intval( $_POST['posts_per_page'] ) : 4;

        echo cmr_dark_media_render_posts( $publisher, $posts_per_page );
        wp_die();
    }
}

if ( ! function_exists( 'cmr_dark_media_render_posts' ) ) {
    function cmr_dark_media_render_posts( $publisher = '', $posts_per_page = 4 ) {
        $query_args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
        // Sort by featured meta flag first, then date
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                'featured_clause' => array(
                    'key'     => '_cmr_news_is_featured',
                    'compare' => 'EXISTS',
                ),
                'not_featured_clause' => array(
                    'key'     => '_cmr_news_is_featured',
                    'compare' => 'NOT EXISTS',
                )
            )
        ),
        'orderby'        => array(
            'featured_clause' => 'DESC',
            'date'            => 'DESC'
        ),
        'tax_query'      => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'cmr_news_category',
                'field'    => 'slug',
                'terms'    => array('media-releases', 'media-release', 'media_releases', 'media_release'),
                'operator' => 'NOT IN'
            ),
        ),
    );

    if ( ! empty( $publisher ) ) {
        $query_args['meta_query'][] = array(
            'key'     => '_cmr_news_publisher_name',
            'value'   => $publisher,
            'compare' => '='
        );
    }

        $media_query = new WP_Query( $query_args );

        ob_start();

        if ( $media_query->have_posts() ) {
            $count = 0;
            echo '<div class="cmr-news-grid">';
            
            while ( $media_query->have_posts() ) {
                $media_query->the_post();
                $post_id = get_the_ID();
                $bg_image = get_the_post_thumbnail_url( $post_id, 'large' );
                $logo_id = get_post_meta( $post_id, '_cmr_news_source_logo_id', true );
                $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
                $document_id = get_post_meta( $post_id, '_cmr_news_document_id', true );
                $ext_url = get_post_meta( $post_id, '_cmr_news_external_link', true );
                
                if ( $document_id ) {
                    $link = wp_get_attachment_url( $document_id );
                    $target = '_blank';
                } elseif ( $ext_url ) {
                    $link = $ext_url;
                    $target = '_blank';
                } else {
                    $link = get_permalink( $post_id );
                    $target = '_self';
                }
                
                $reading_time = get_post_meta( $post_id, '_cmr_news_reading_time', true );
                $publisher = get_post_meta( $post_id, '_cmr_news_publisher_name', true );
                $date = get_the_date( 'M j, Y' );

                $card_class = ( $count === 0 ) ? 'cmr-card cmr-card-featured' : 'cmr-card cmr-card-standard';
                ?>
                <div class="<?php echo esc_attr( $card_class ); ?>">
                    <a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="cmr-card-link-wrapper">
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
                            $arrow_url = 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol.svg';
                            ?>
                            <span class="cmr-read-coverage">Read Coverage <img src="<?php echo esc_url($arrow_url); ?>" class="cmr-arrow-icon" alt="Arrow"></span>
                        </div>
                    </a>
                </div>
                <?php
                $count++;
            }
            echo '</div>'; // Close cmr-news-grid
        } else {
            echo '<p style="color: #ccc;">No coverage found for this publisher.</p>';
        } 
        wp_reset_postdata();

        return ob_get_clean();
    }
}

if ( ! function_exists( 'cmr_dark_media_coverage_shortcode' ) ) {
    function cmr_dark_media_coverage_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'posts_per_page' => 4,
        ), $atts );

        // Enqueue the news styling
        wp_enqueue_style( 'cmr-news-style', get_template_directory_uri() . '/assets/css/cmr-news.css', array(), time() );

        global $wpdb;
        $publishers = $wpdb->get_col("
            SELECT DISTINCT meta_value 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_cmr_news_publisher_name' 
            AND meta_value != ''
            ORDER BY meta_value ASC
            LIMIT 4
        ");

        if ( empty($publishers) ) {
            $publishers = array('CNN', 'Times of India', 'BBC News', 'Your Story');
        }

        ob_start();
        ?>
        <style>
            .cmr-dmc-wrapper {
                font-family: 'Instrument Sans', sans-serif !important;
                background-color: #000000;
                color: #fff;
                padding: 40px 20px;
                border-radius: 12px;
                max-width: 1280px;
                margin: 50px auto;
            }
            .cmr-dmc-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
                flex-wrap: wrap;
                gap: 20px;
            }
            .cmr-dmc-filters {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }
            .cmr-dmc-filter-btn {
                background: transparent;
                border: 1px solid #333;
                color: #ccc;
                padding: 8px 24px;
                border-radius: 30px;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .cmr-dmc-filter-btn:hover, .cmr-dmc-filter-btn.active {
                border-color: #fff;
                color: #fff;
            }
            .cmr-dmc-explore-all {
                color: #fff;
                text-decoration: none;
                font-size: 15px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border-bottom: 1px solid #fff;
                padding-bottom: 2px;
                transition: opacity 0.3s;
            }
            .cmr-dmc-explore-all:hover {
                opacity: 0.8;
            }



            .cmr-dmc-content-area {
                transition: opacity 0.3s ease;
            }
            .cmr-dmc-content-area.loading {
                opacity: 0.5;
                pointer-events: none;
            }
        </style>

        <div class="cmr-dmc-wrapper">
            <div class="cmr-dmc-header">
                <div class="cmr-dmc-filters">
                    <button class="cmr-dmc-filter-btn active" data-pub="">All</button>
                    <?php foreach($publishers as $pub): ?>
                        <button class="cmr-dmc-filter-btn" data-pub="<?php echo esc_attr($pub); ?>"><?php echo esc_html($pub); ?></button>
                    <?php endforeach; ?>
                </div>
                <a href="/media-coverage" class="cmr-dmc-explore-all">
                    Explore All 
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </a>
            </div>

            <div class="cmr-dmc-content-area cmr-news-container cmr-news-black-bg" style="padding: 0; background-color: transparent !important;" id="cmr-dmc-content-<?php echo esc_attr($atts['posts_per_page']); ?>">
                <?php echo cmr_dark_media_render_posts('', $atts['posts_per_page']); ?>
            </div>
            
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.querySelector('.cmr-dmc-wrapper');
            if (!wrapper) return;

            const buttons = wrapper.querySelectorAll('.cmr-dmc-filter-btn');
            const contentArea = wrapper.querySelector('.cmr-dmc-content-area');
            const ajaxUrl = '<?php echo admin_url("admin-ajax.php"); ?>';
            const postsPerPage = <?php echo intval($atts['posts_per_page']); ?>;

            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update active state
                    buttons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const publisher = this.getAttribute('data-pub');
                    contentArea.classList.add('loading');

                    const formData = new FormData();
                    formData.append('action', 'cmr_dark_media_filter');
                    formData.append('publisher', publisher);
                    formData.append('posts_per_page', postsPerPage);

                    fetch(ajaxUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(html => {
                        contentArea.innerHTML = html;
                        contentArea.classList.remove('loading');
                    })
                    .catch(error => {
                        console.error('Error fetching media coverage:', error);
                        contentArea.classList.remove('loading');
                    });
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_dark_media_coverage', 'cmr_dark_media_coverage_shortcode' );
