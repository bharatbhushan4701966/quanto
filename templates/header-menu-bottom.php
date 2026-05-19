<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

    // Block direct access
    if( !defined( 'ABSPATH' ) ){
        exit();
    }

    if( defined( 'CMB2_LOADED' )  ){
        if( !empty( quanto_meta('page_breadcrumb_area') ) ) {
            $quanto_page_breadcrumb_area  = quanto_meta('page_breadcrumb_area');
        } else {
            $quanto_page_breadcrumb_area = '1';
        }
    }else{
        $quanto_page_breadcrumb_area = '1';
    }

    $allowhtml = array(
        'p'         => array(
            'class'     => array()
        ),
        'span'      => array(
            'class'     => array(),
        ),
        'a'         => array(
            'href'      => array(),
            'title'     => array()
        ),
        'br'        => array(),
        'em'        => array(),
        'strong'    => array(),
        'b'         => array(),
        'sub'       => array(),
        'sup'       => array(),
    );

    if(  is_page() || is_page_template( 'template-builder.php' )  ) {
        if( $quanto_page_breadcrumb_area == '1' ) {
            echo '<!-- Page title -->';
            echo '<div class="breadcumb-wrapper">';
                echo '<div class="container custom-container">';
                    echo '<div class="breadcumb-content">';
                        if( defined('CMB2_LOADED') || class_exists('ReduxFramework') ) {
                            if( quanto_meta('page_breadcrumb_settings') == 'page' ) {
                                $quanto_page_title_switcher = quanto_meta('page_title');
                            } elseif( quanto_opt('quanto_page_title_switcher') == true ) {
                                $quanto_page_title_switcher = quanto_opt('quanto_page_title_switcher');
                            }else{
                                $quanto_page_title_switcher = '1';
                            }
                        } else {
                            $quanto_page_title_switcher = '1';
                        }

                        if( $quanto_page_title_switcher == '1' ){
                            if( class_exists( 'ReduxFramework' ) ){
                                $quanto_page_title_tag    = quanto_opt('quanto_page_title_tag');
                            }else{
                                $quanto_page_title_tag    = 'h2';
                            }

                            if( defined( 'CMB2_LOADED' )  ){
                                if( !empty( quanto_meta('page_title_settings') ) ) {
                                    $quanto_custom_title = quanto_meta('page_title_settings');
                                } else {
                                    $quanto_custom_title = 'default';
                                }
                            }else{
                                $quanto_custom_title = 'default';
                            }

                            if( $quanto_custom_title == 'default' ) {
                                echo quanto_heading_tag(
                                    array(
                                        "tag"   => esc_attr( $quanto_page_title_tag ),
                                        "text"  => esc_html( get_the_title( ) ),
                                        'class' => 'breadcumb-title'
                                    )
                                );
                            } else {
                                echo quanto_heading_tag(
                                    array(
                                        "tag"   => esc_attr( $quanto_page_title_tag ),
                                        "text"  => esc_html( quanto_meta('custom_page_title') ),
                                        'class' => 'breadcumb-title'
                                    )
                                );
                            }

                        }
                        if( defined('CMB2_LOADED') || class_exists('ReduxFramework') ) {

                            if( quanto_meta('page_breadcrumb_settings') == 'page' ) {
                                $quanto_breadcrumb_switcher = quanto_meta('page_breadcrumb_trigger');
                            } else {
                                $quanto_breadcrumb_switcher = quanto_opt('quanto_enable_breadcrumb');
                            }

                        } else {
                            $quanto_breadcrumb_switcher = '1';
                        }

                        if( $quanto_breadcrumb_switcher == '1' && (  is_page() || is_page_template( 'template-builder.php' ) )) {
                            echo '<div class="breadcumb-menu-wrap">';
                                quanto_breadcrumbs(
                                    array(
                                        'breadcrumbs_classes' => 'nav',
                                    )
                                );
                            echo '</div>';
                        }

                        echo '</div>';
                echo '</div>';
            echo '</div>';
            echo '<!-- End of Page title -->';
        }
    } else {
        echo '<!-- Page title -->';
        $extra_class = '';
        
        if ( class_exists('Redux') ) {
            if ( is_singular() || is_singular('product') || is_singular('quanto_class') || is_singular('quanto_event') || is_singular('quanto_teacher') ) {
                $extra_class = ' detail-page';
            }
        }

        echo '<div class="breadcumb-wrapper' . esc_attr($extra_class) . '">';
            echo '<div class="container custom-container">';
                echo '<div class="breadcumb-content">';
                    if( class_exists( 'ReduxFramework' )  ){
                        $quanto_page_title_switcher  = quanto_opt('quanto_page_title_switcher');
                    }else{
                        $quanto_page_title_switcher = '1';
                    }

                    if( $quanto_page_title_switcher ){
                        if( class_exists( 'ReduxFramework' ) ){
                            $quanto_page_title_tag    = quanto_opt('quanto_page_title_tag');
                        }else{
                            $quanto_page_title_tag    = 'h2';
                        }
                        if ( is_archive() ){
                            echo quanto_heading_tag(
                                array(
                                    "tag"   => esc_attr( $quanto_page_title_tag ),
                                    "text"  => wp_kses( get_the_archive_title(), $allowhtml ),
                                    'class' => 'breadcumb-title'
                                )
                            );
                        }elseif ( is_home() ){
                            $quanto_blog_page_title_setting = quanto_opt('quanto_blog_page_title_setting');
                            $quanto_blog_page_title_switcher = quanto_opt('quanto_blog_page_title_switcher');
                            $quanto_blog_page_custom_title = quanto_opt('quanto_blog_page_custom_title');
                            if( class_exists('ReduxFramework') ){
                                if( $quanto_blog_page_title_switcher ){
                                    echo quanto_heading_tag(
                                        array(
                                            "tag"   => esc_attr( $quanto_page_title_tag ),
                                            "text"  => !empty( $quanto_blog_page_custom_title ) && $quanto_blog_page_title_setting == 'custom' ? esc_html( $quanto_blog_page_custom_title) : esc_html__( 'Blog', 'quanto' ),
                                            'class' => 'breadcumb-title'
                                        )
                                    );
                                }
                            }else{
                                echo quanto_heading_tag(
                                    array(
                                        "tag"   => "h2",
                                        "text"  => esc_html__( 'Blog', 'quanto' ),
                                        'class' => 'breadcumb-title',
                                    )
                                );
                            }
                        }elseif( is_search() ){
                            echo quanto_heading_tag(
                                array(
                                    "tag"   => esc_attr( $quanto_page_title_tag ),
                                    "text"  => esc_html__( 'Search Result', 'quanto' ),
                                    'class' => 'breadcumb-title'
                                )
                            );
                        }elseif( is_404() ){
                            echo quanto_heading_tag(
                                array(
                                    "tag"   => esc_attr( $quanto_page_title_tag ),
                                    "text"  => esc_html__( '404 PAGE', 'quanto' ),
                                    'class' => 'breadcumb-title'
                                )
                            );
                        }elseif( is_singular( 'quanto_class' ) ){
                            echo quanto_heading_tag(
                                array(
                                    "tag"   => "h2",
                                    "text"  => esc_html__( 'Class Details', 'quanto' ),
                                    'class' => 'breadcumb-title text-start',
                                )
                            );
                        }elseif( is_singular( 'quanto_event' ) ){
                            echo quanto_heading_tag(
                                array(
                                    "tag"   => "h2",
                                    "text"  => esc_html__( 'Event Details', 'quanto' ),
                                    'class' => 'breadcumb-title text-start',
                                )
                            );
                        }elseif( is_singular( 'quanto_teacher' ) ){
                            echo quanto_heading_tag(
                                array(
                                    "tag"   => "h2",
                                    "text"  => esc_html__( 'Teacher Details', 'quanto' ),
                                    'class' => 'breadcumb-title text-start',
                                )
                            );
                        }elseif( is_singular( 'product' ) ){
                            $posttitle_position  = quanto_opt('quanto_product_details_title_position');
                            $postTitlePos = false;
                            if( class_exists( 'ReduxFramework' ) ){
                                if( $posttitle_position && $posttitle_position != 'header' ){
                                    $postTitlePos = true;
                                }
                            }else{
                                $postTitlePos = false;
                            }
                            
                            if( $postTitlePos != true ){
                                echo quanto_heading_tag(
                                    array(
                                        "tag"   => esc_attr( $quanto_page_title_tag ),
                                        "text"  => wp_kses( get_the_title( ), $allowhtml ),
                                        'class' => 'breadcumb-title text-start'
                                    )
                                );
                            } else {
                                if( class_exists( 'ReduxFramework' ) ){
                                    $quanto_post_details_custom_title  = quanto_opt('quanto_product_details_custom_title');
                                }else{
                                    $quanto_post_details_custom_title = __( 'Shop Details','quanto' );
                                }

                                if( !empty( $quanto_post_details_custom_title ) ) {
                                    echo quanto_heading_tag(
                                        array(
                                            "tag"   => esc_attr( $quanto_page_title_tag ),
                                            "text"  => wp_kses( $quanto_post_details_custom_title, $allowhtml ),
                                            'class' => 'breadcumb-title text-start'
                                        )
                                    );
                                }
                            }
                        }else{
                            $posttitle_position  = quanto_opt('quanto_post_details_title_position');
                            $postTitlePos = false;
                            if( is_single() ){
                                if( class_exists( 'ReduxFramework' ) ){
                                    if( $posttitle_position && $posttitle_position != 'header' ){
                                        $postTitlePos = true;
                                    }
                                }else{
                                    $postTitlePos = false;
                                }
                            }
                            if( is_singular( 'product' ) ){
                                $posttitle_position  = quanto_opt('quanto_product_details_title_position');
                                $postTitlePos = false;
                                if( class_exists( 'ReduxFramework' ) ){
                                    if( $posttitle_position && $posttitle_position != 'header' ){
                                        $postTitlePos = true;
                                    }
                                }else{
                                    $postTitlePos = false;
                                }
                            }

                            if( $postTitlePos != true ){
                                echo quanto_heading_tag(
                                    array(
                                        "tag"   => esc_attr( $quanto_page_title_tag ),
                                        "text"  => wp_kses( get_the_title( ), $allowhtml ),
                                        'class' => 'breadcumb-title text-start'
                                    )
                                );
                            } else {
                                if( class_exists( 'ReduxFramework' ) ){
                                    $quanto_post_details_custom_title  = quanto_opt('quanto_post_details_custom_title');
                                }else{
                                    $quanto_post_details_custom_title = __( 'Blog Details','quanto' );
                                }

                                if( !empty( $quanto_post_details_custom_title ) ) {
                                    echo quanto_heading_tag(
                                        array(
                                            "tag"   => esc_attr( $quanto_page_title_tag ),
                                            "text"  => wp_kses( $quanto_post_details_custom_title, $allowhtml ),
                                            'class' => 'breadcumb-title text-start'
                                        )
                                    );
                                }
                            }
                        }
                    }
                    if( class_exists('ReduxFramework') ) {
                        $quanto_breadcrumb_switcher = quanto_opt( 'quanto_enable_breadcrumb' );
                    } else {
                        $quanto_breadcrumb_switcher = '1';
                    }
                    if( $quanto_breadcrumb_switcher == '1' ) {
                        echo '<div class="breadcumb-menu-wrap">';
                            quanto_breadcrumbs(
                                array(
                                    'breadcrumbs_classes' => 'nav',
                                )
                            );
                        echo '</div>';
                    }
                echo '</div>';
            echo '</div>';
        echo '</div>';
        echo '<!-- End of Page title -->';
    }