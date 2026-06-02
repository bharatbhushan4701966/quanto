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
                            <?php
                            // Custom Author Box for Media Releases
                            if ( get_post_type() === 'cmr_news' ) {
                                $custom_author_id = get_post_meta( get_the_ID(), '_cmr_news_custom_author', true );
                                if ( $custom_author_id ) {
                                    $author_data = get_userdata( $custom_author_id );
                                    if ( $author_data ) {
                                        $designation = get_user_meta( $custom_author_id, 'designation', true );
                                        $linkedin = get_user_meta( $custom_author_id, 'linkedin_url', true );
                                        $x_url = get_user_meta( $custom_author_id, 'x_url', true );
                                        $bio = get_user_meta( $custom_author_id, 'description', true );
                                        ?>
                                        <div class="cmr-custom-author-box mt-5 mb-5 p-4 border rounded bg-white">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="cmr-custom-author-avatar me-4">
                                                    <?php echo get_avatar( $custom_author_id, 80, '', '', array( 'class' => 'rounded-circle' ) ); ?>
                                                </div>
                                                <div class="cmr-custom-author-info">
                                                    <h4 class="mb-1" style="font-weight: 700; font-size: 20px; font-family: 'Instrument Sans', sans-serif; letter-spacing: 0.5px;"><?php echo esc_html( $author_data->display_name ); ?></h4>
                                                    <?php if ( $designation ) : ?>
                                                        <p class="mb-2 text-muted" style="font-size: 14px; font-family: 'Instrument Sans', sans-serif;"><?php echo esc_html( $designation ); ?></p>
                                                    <?php endif; ?>
                                                    <div class="cmr-custom-author-social d-flex gap-2">
                                                        <?php if ( $linkedin ) : ?>
                                                            <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" style="color: #000; font-size: 18px;"><i class="fa-brands fa-linkedin"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ( $x_url ) : ?>
                                                            <a href="<?php echo esc_url( $x_url ); ?>" target="_blank" rel="noopener noreferrer" style="color: #000; font-size: 18px;"><i class="fa-brands fa-square-x-twitter"></i></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ( $bio ) : ?>
                                                <div class="cmr-custom-author-bio mt-3">
                                                    <p style="font-size: 14px; line-height: 1.6; color: #555;"><?php echo wp_kses_post( $bio ); ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
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

                            if ( get_post_type() === 'cmr_news' ) {
                                ?>
                                <div class="insights-cta-banner">
                                    <div class="insights-cta-content">
                                        <h3 class="insights-cta-title">Want deeper insights?</h3>
                                        <p class="insights-cta-text">Connect with our analysts to understand how these<br>trends impact your specific vertical.</p>
                                    </div>
                                    <div class="insights-cta-actions">
                                        <div class="elementor-element elementor-element-b3cba9e download-btn animejs-disable elementor-widget elementor-widget-button" data-id="b3cba9e" data-element_type="widget" data-e-type="widget" data-settings="{&quot;mas-animation&quot;:&quot;none&quot;}" data-widget_type="button.default">
                                            <a class="elementor-button elementor-button-link elementor-size-sm insights-cta-button primary" href="#">
                                                <span class="elementor-button-content-wrapper">
                                                    <span class="elementor-button-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none"><path d="M2.04895 4.52205V3.54978C2.07104 3.12993 2.4025 2.79848 2.80025 2.79848L11.396 2.77638C11.8159 2.79848 12.1473 3.12993 12.1473 3.52768V12.1455C12.1473 12.5433 11.8159 12.8747 11.396 12.8968H10.4237C10.0039 12.8747 9.67244 12.5433 9.65035 12.1234L9.78293 6.90853L3.44106 13.2504C3.1317 13.5598 2.68976 13.5598 2.3804 13.2504L1.6733 12.5433C1.38603 12.256 1.36394 11.792 1.6733 11.4826L8.01516 5.14077L2.82234 5.29545C2.4025 5.27335 2.04895 4.96399 2.04895 4.52205Z" fill="#1A1C1E"></path></svg>
                                                    </span>
                                                    <span class="elementor-button-text">Talk to Analyst</span>
                                                </span>
                                            </a>
                                        </div>
                                        <a href="#" class="insights-cta-button secondary">
                                            Explore Press Release <i class="fa-solid fa-arrow-right" style="font-size: 14px; margin-left: 6px; transform: rotate(-45deg);"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }

                        echo '</div>';
                    echo '</div>';

                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';




   