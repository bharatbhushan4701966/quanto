<?php
/**
 * CMR News Automotive Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'cmr_news_automotive', 'cmr_news_automotive_shortcode' );
function cmr_news_automotive_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'category' => 'automotive', 
    ), $atts, 'cmr_news_automotive' );

    wp_enqueue_style( 'cmr-news-style', get_template_directory_uri() . '/assets/css/cmr-news.css', array(), time() );

    ob_start();
    ?>
    <div class="cmr-news-container cmr-news-black-bg" style="padding-bottom: 60px;">
        <!-- Tab Contents without tabs -->
        <div class="cmr-news-content-wrapper">
            <div class="cmr-news-tab-pane active" style="display: block;">
                <div class="cmr-news-grid">
                    <?php
                    $normal_args = array(
                        'post_type' => 'cmr_news',
                        'tax_query' => array(
                            'relation' => 'AND',
                            array(
                                'taxonomy' => 'cmr_news_category',
                                'field'    => 'slug',
                                'terms'    => 'cmr-in-news',
                            )
                        ),
                        'orderby' => 'date',
                        'order'   => 'DESC',
                        'posts_per_page' => 5,
                    );
                    
                    $news_query = new WP_Query( $normal_args );
                    $all_posts = $news_query->posts;

                    if ( ! empty( $all_posts ) ) {
                        $count = 0;
                        global $post;
                        foreach ( $all_posts as $post ) {
                            setup_postdata( $post );
                            $post_id = get_the_ID();
                            $bg_image = get_the_post_thumbnail_url( $post_id, 'large' );
                            $logo_id = get_post_meta( $post_id, '_cmr_news_source_logo_id', true );
                            $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
                            $reading_time = get_post_meta( $post_id, '_cmr_news_reading_time', true );
                            $publisher = get_post_meta( $post_id, '_cmr_news_publisher_name', true );
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
                                        <span class="cmr-read-coverage">Read Coverage <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol.svg" class="cmr-arrow-icon" alt="Arrow"></span>
                                    </div>
                                </a>
                            </div>
                            <?php
                            $count++;
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<p>No news content available.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
