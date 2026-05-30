<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

    // Block direct access
    if( ! defined( 'ABSPATH' ) ){
        exit();
    }

    quanto_setPostViews( get_the_ID() );

    ?>
    <?php echo '<div class="blog-item-wrapper">'; ?>
        <?php echo '<div class="blog-item blog-item-details">'; ?>
            <div <?php post_class(); ?> >
                <?php
                if( class_exists('ReduxFramework') ) {
                    $quanto_post_details_title_position = quanto_opt('quanto_post_details_title_position');
                } else {
                    $quanto_post_details_title_position = 'header';
                }

                $allowhtml = array(
                    'p'         => array(
                        'class'     => array()
                    ),
                    'span'      => array(),
                    'a'         => array(
                        'href'      => array(),
                        'title'     => array(),
                        'class'     => array(),
                    ),
                    'br'        => array(),
                    'em'        => array(),
                    'strong'    => array(),
                    'b'         => array(),
                );

                echo '<div class="row justify-content-center row-padding-bottom">';
                    if ( class_exists('ReduxFramework') ) {
                        $column_class = quanto_opt('quanto_blog_details_title_column');
                        if ( empty($column_class) ) {
                            $column_class = 'col-xl-9 col-xxl-9';
                        }
                        echo '<div class="' . esc_attr($column_class) . '">';
                    } else {
                        if ( is_active_sidebar('quanto-blog-sidebar') ) {
                            echo '<div class="col-xl-12 col-xxl-12">';
                        } else {
                            echo '<div class="col-xl-9 col-xxl-9">';
                        }
                    }

                        // Inject breadcrumbs above the title
                        if ( function_exists('quanto_breadcrumbs') ) {
                            echo '<div class="breadcumb-menu-wrap move-anim" data-delay="0.45" style="margin-bottom: 15px;">';
                            quanto_breadcrumbs( array( 'breadcrumbs_classes' => 'nav' ) );
                            echo '</div>';
                        }

                        if( $quanto_post_details_title_position != 'header' ) {
                            echo '<div class="title-box blog-title move-anim" data-delay="0.45">';
                                //title
                                echo '<h2>'.wp_kses( get_the_title(), $allowhtml ).'</h2>';
                            echo '</div>';
                        }

                        // Blog Post Meta
                        do_action( 'quanto_blog_details_post_meta' );
                    echo '</div>';
                echo '</div>';


                // Blog Post Thumbnail
                do_action( 'quanto_blog_post_thumb' );
                

                echo '<div class="content-box">';

                    // Share Links
                    if( class_exists('ReduxFramework') ) {
                        $quanto_post_details_share_options = quanto_opt('quanto_post_details_share_options');
                    } else {
                        $quanto_post_details_share_options = false;
                    }

                    if( function_exists( 'quanto_social_sharing_buttons' ) && $quanto_post_details_share_options ){
                        echo '<div class="social-links">';
                            /**
                            *
                            * Hook for Blog Details Share Options
                            *
                            * Hook quanto_blog_details_share_options
                            *
                            * @Hooked quanto_blog_details_share_options_cb 10
                            *
                            */
                            do_action( 'quanto_blog_details_share_options' );
                        echo '</div>';
                    }

                    echo '<div class="row justify-content-center social-links-scroll position-relative">';
                        if( class_exists('ReduxFramework') ) {
                            $quanto_post_details_share_options = quanto_opt('quanto_post_details_share_options');
                        } else {
                            $quanto_post_details_share_options = false;
                        }
                        if( function_exists( 'quanto_social_sharing_buttons' ) && $quanto_post_details_share_options ) {
                            echo '<div class="col-xl-9 col-xxl-8">';
                        } else {
                            echo '<div class="col-xl-12 col-xxl-12">';
                        }

                            echo '<div class="blog-body">';
                                // Blog Content
                                the_content();
                            echo '</div>';

                            // Auto-generated download button for Media Releases
                            if ( get_post_type() === 'cmr_news' ) {
                                $document_id = get_post_meta( get_the_ID(), '_cmr_news_document_id', true );
                                if ( $document_id ) {
                                    $document_url = wp_get_attachment_url( $document_id );
                                    if ( $document_url ) {
                                        ?>
                                        <div class="cmr-media-download-action" style="margin-top: 30px; margin-bottom: 30px;">
                                            <a href="<?php echo esc_url( $document_url ); ?>" target="_blank" download class="insights-cta-button" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 12px 24px; background: var(--quanto-primary-color, #000); color: #fff; border-radius: 4px; text-decoration: none; font-weight: 600;">
                                                <span><?php esc_html_e( 'Download Media Release', 'quanto' ); ?></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                            </a>
                                        </div>
                                        <?php
                                    }
                                }
                            }

                            // Tags
                            $quanto_post_tag = get_the_tags();
                            if( is_array( $quanto_post_tag ) && ! empty( $quanto_post_tag ) ){
                                echo '<div class="blog-tags">';
                                    echo '<ul class="custom-ul">';
                                        foreach( $quanto_post_tag as $tags ){
                                            echo '<li><a href="'.esc_url( get_tag_link( $tags->term_id ) ).'">'.esc_html( $tags->name ).'</a></li>';
                                        }
                                    echo '</ul>';
                                echo '</div>';
                            }

                            ?>
                            <!-- Need Deeper Insights CTA Banner -->
                            <div class="insights-cta-banner">
                                <div class="insights-cta-content">
                                    <h3 class="insights-cta-title"><?php esc_html_e( 'Need deeper insights?', 'quanto' ); ?></h3>
                                    <p class="insights-cta-text"><?php esc_html_e( 'Talk to our analysts for tailored recommendations across your sector.', 'quanto' ); ?></p>
                                </div>
                                <div class="insights-cta-action">
                                    <a href="#" class="insights-cta-button">
                                        <span><?php esc_html_e( 'Get Industry Insights', 'quanto' ); ?></span>
                                        <svg class="insights-cta-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="7" y1="17" x2="17" y2="7"></line>
                                            <polyline points="7 7 17 7 17 17"></polyline>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <?php
                            /**
                            *
                            * Hook for Blog Details Comments
                            *
                            * Hook quanto_blog_details_comments
                            *
                            * @Hooked quanto_blog_details_comments_cb 10
                            *
                            */
                            do_action( 'quanto_blog_details_comments' );

                        echo '</div>';
                    echo '</div>';

                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';




   