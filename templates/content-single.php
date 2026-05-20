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

    // WHO WE SERVE section from homepage
    if ( function_exists( 'quanto_render_homepage_who_we_serve_section' ) ) {
        quanto_render_homepage_who_we_serve_section();
    }


   