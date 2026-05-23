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


    // cursor hook function
    if( ! function_exists( 'quanto_cursor_wrap_cb' ) ) {
        function quanto_cursor_wrap_cb() {
            $cursor_display =  quanto_opt('quanto_display_cursor');

            if( class_exists('ReduxFramework') ){
                if( $cursor_display ){
                    echo '<div class="cursor d-none d-lg-block">';
                    echo '</div>';
                }
            }
        }
    };

    
    // preloader hook function
    if( ! function_exists( 'quanto_preloader_wrap_cb' ) ) {
        function quanto_preloader_wrap_cb() {
            $preloader_display =  quanto_opt('quanto_display_preloader');
            $preloader_image   = quanto_opt('preloader_image');

            if( class_exists('ReduxFramework') ){
                if( $preloader_display ){
                    echo '<div class="preloader">';
                        echo '<div class="spinner-wrap">';
                            echo '<div class="preloader-logo">';
                                if ( ! empty( $preloader_image['url'] ) ) {
                                    echo '<img src="' . esc_url( $preloader_image['url'] ) . '" alt="' . esc_attr__('Preloader', 'quanto') . '">';
                                }  
                            echo '</div>';
                            echo '<div class="spinner"></div>';
                        echo '</div>';
                    echo '</div>';
                }
            }else{
                echo '<div class="preloader">';
                    echo '<div class="spinner-wrap">';
                        echo '<div class="preloader-logo">';
                        echo '</div>';
                        echo '<div class="spinner"></div>';
                    echo '</div>';
                echo '</div>';
            }
        }
    };

    // Header Hook function
    if( !function_exists('quanto_header_cb') ) {
        function quanto_header_cb( ) {
            get_template_part('templates/header');
        }
    } 

    // Breadcrumb Hook function
    if( !function_exists('quanto_breadcrumb_cb') ) {
        function quanto_breadcrumb_cb( ) {
            if ( class_exists('ReduxFramework') ) {
                $breadcrumb_switcher = quanto_opt('quanto_full_breadcrumb_switcher');
            } else {
                $breadcrumb_switcher = 1; // Default ON if Redux not present
            }

            if ( $breadcrumb_switcher == 1 ) {
                get_template_part('templates/header-menu-bottom');
            }
        }
    } 

    // Blog Start Wrapper Function
    if( !function_exists('quanto_blog_section_title_cb') ) {
        function quanto_blog_section_title_cb() {
            if( class_exists( 'ReduxFramework' ) ){
                $breadcrumb_switcher = quanto_opt('quanto_full_breadcrumb_switcher');
                $quanto_blog_section_custom_title_tag    = quanto_opt('quanto_blog_section_custom_title_tag');
            }else{
                $breadcrumb_switcher = 0; // Default Off if Redux not present
                $quanto_blog_section_custom_title_tag    = 'h1';
            }

            $quanto_blog_section_title_switcher = quanto_opt('quanto_blog_section_title_switcher');
            $quanto_blog_section_custom_title = quanto_opt('quanto_blog_section_custom_title');
            if( $quanto_blog_section_title_switcher == 1 ){
                echo '<section class="quanto-hero-blog-section overflow-hidden">';
                    echo '<div class="container custom-container">';
                        echo '<div class="row g-4">';
                            echo '<div class="col-lg-12 col-xxl-11">';
                                echo '<div class="quanto-hero-blog__content move-anim" data-delay="0.45">';
                                    echo quanto_heading_tag(
                                        array(
                                            "tag"   => esc_attr( $quanto_blog_section_custom_title_tag ),
                                            "text"  => !empty( $quanto_blog_section_custom_title ) ? esc_html( $quanto_blog_section_custom_title) : esc_html__( 'Blog', 'quanto' ),
                                            'class' => 'title'
                                        )
                                    );
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</section>';
            }
        }
    }

    // Blog Start Wrapper Function
    if( !function_exists('quanto_blog_start_wrap_cb') ) {
        function quanto_blog_start_wrap_cb() {
            echo '<section class="quanto-blog-section section-padding-top section-padding-bottom overflow-hidden">';
                echo '<div class="container custom-container">';
                    if( is_active_sidebar( 'quanto-blog-sidebar' ) ){
                        $quanto_gutter_class = 'gx-30';
                    }else{
                        $quanto_gutter_class = '';
                    }
                    echo '<div class="row '.esc_attr( $quanto_gutter_class ).'">';
        }
    }

    // Blog End Wrapper Function
    if( !function_exists('quanto_blog_end_wrap_cb') ) {
        function quanto_blog_end_wrap_cb() {
                    echo '</div>';
                echo '</div>';
            echo '</section>';
        }
    }

    // Blog Column Start Wrapper Function
    if( !function_exists('quanto_blog_col_start_wrap_cb') ) {
        function quanto_blog_col_start_wrap_cb() {
            if( class_exists('ReduxFramework') ) {
                $quanto_blog_sidebar = quanto_opt('quanto_blog_sidebar');
                if( $quanto_blog_sidebar == '2' && is_active_sidebar('quanto-blog-sidebar') ) {
                    echo '<div class="col-lg-8 order-lg-last">';
                } elseif( $quanto_blog_sidebar == '3' && is_active_sidebar('quanto-blog-sidebar') ) {
                    echo '<div class="col-lg-8">';
                } else {
                    echo '<div class="col-lg-12">';
                }

            } else {
                if( is_active_sidebar('quanto-blog-sidebar') ) {
                    echo '<div class="col-lg-8">';
                } else {
                    echo '<div class="col-lg-12">';
                }
            }
        }
    }
    // Blog Column End Wrapper Function
    if( !function_exists('quanto_blog_col_end_wrap_cb') ) {
        function quanto_blog_col_end_wrap_cb() {
            echo '</div>';
        }
    }

    // Blog Sidebar
    if( !function_exists('quanto_blog_sidebar_cb') ) {
        function quanto_blog_sidebar_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_blog_sidebar = quanto_opt('quanto_blog_sidebar');
            } else {
                $quanto_blog_sidebar = 2;
            }
            if( $quanto_blog_sidebar != 1 && is_active_sidebar('quanto-blog-sidebar') ) {
                // Sidebar
                get_sidebar();
            }
        }
    }


    if( !function_exists('quanto_blog_details_sidebar_cb') ) {
        function quanto_blog_details_sidebar_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_blog_single_sidebar = quanto_opt('quanto_blog_single_sidebar');
            } else {
                $quanto_blog_single_sidebar = 4;
            }
            if( $quanto_blog_single_sidebar != 1 ) {
                // Sidebar
                get_sidebar();
            }

        }
    }

    // Blog Pagination Function
    if( !function_exists('quanto_blog_pagination_cb') ) {
        function quanto_blog_pagination_cb( ) {
            get_template_part('templates/pagination');
        }
    }

    // Blog Content Function
    if( !function_exists('quanto_blog_content_cb') ) {
        function quanto_blog_content_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_blog_grid = quanto_opt('quanto_blog_grid');
            } else {
                $quanto_blog_grid = '1';
            }

            if( $quanto_blog_grid == '1' ) {
                $quanto_blog_grid_class = 'col-lg-12';
            } elseif( $quanto_blog_grid == '2' ) {
                $quanto_blog_grid_class = 'col-md-6';
            } else {
                $quanto_blog_grid_class = 'col-md-6 col-lg-4';
            }

            echo '<div class="row gx-4 gy-5">';
                if( have_posts() ) {
                    while( have_posts() ) {
                        the_post();
                        echo '<div class="'.esc_attr($quanto_blog_grid_class).'">';
                            if( class_exists( 'ReduxFramework' )){
                                $quanto_blog_style = quanto_opt('quanto_blog_style');

                                if('blog_style_one' == $quanto_blog_style ){
                                    echo '<div class="quanto-blog-box fade-anim" data-delay="0.30" data-direction="right">';
                                }elseif('blog_style_two' == $quanto_blog_style ){
                                    echo '<div class="quanto-blog-box style-2 fade-anim" data-delay="0.30" data-direction="right">';
                                }
                            }else{
                                echo '<div class="quanto-blog-box fade-anim" data-delay="0.30" data-direction="right">';
                            }
                                get_template_part('templates/content',get_post_format());
                            echo '</div>';
                        echo '</div>';
                    }
                    wp_reset_postdata();
                } else{
                    if( class_exists( 'ReduxFramework' )){
                        $quanto_blog_style = quanto_opt('quanto_blog_style');

                        if('blog_style_one' == $quanto_blog_style ){
                            echo '<div class="quanto-blog-box fade-anim" data-delay="0.30" data-direction="right">';
                        }elseif('blog_style_two' == $quanto_blog_style ){
                            echo '<div class="quanto-blog-box style-2 fade-anim" data-delay="0.30" data-direction="right">';
                        }
                    }else{
                        echo '<div class="quanto-blog-box fade-anim" data-delay="0.30" data-direction="right">';
                    }
                        get_template_part('templates/content','none');
                    echo '</div>';
                }
            echo '</div>';
        }
    }

    if ( ! function_exists( 'quanto_find_footer_post_by_slug' ) ) {
        function quanto_find_footer_post_by_slug() {
            foreach ( array( 'main-footer', 'main-fotter' ) as $slug ) {
                $posts = get_posts( array(
                    'name'        => $slug,
                    'post_type'   => 'quanto_footer',
                    'post_status' => 'publish',
                    'numberposts' => 1,
                ) );

                if ( ! empty( $posts ) ) {
                    return (int) $posts[0]->ID;
                }
            }

            return false;
        }
    }

    if ( ! function_exists( 'quanto_get_resolved_footer_id' ) ) {
        function quanto_get_resolved_footer_id() {
            if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
                return false;
            }

            $footer_id = quanto_find_footer_post_by_slug();
            if ( $footer_id ) {
                return $footer_id;
            }

            if ( ! class_exists( 'ReduxFramework' ) ) {
                return false;
            }

            if ( is_page() || is_page_template( 'template-builder.php' ) ) {
                $post_id               = get_queried_object_id();
                $footer_enable_disable = '';
                $footer_settings       = '';
                $footer_local          = '';

                if (
                    class_exists( '\\Elementor\\Core\\Settings\\Manager' ) &&
                    method_exists( '\\Elementor\\Core\\Settings\\Manager', 'get_settings_managers' )
                ) {
                    try {
                        $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
                        if ( $page_settings_manager && method_exists( $page_settings_manager, 'get_model' ) ) {
                            $page_settings_model = $page_settings_manager->get_model( $post_id );
                            if ( $page_settings_model ) {
                                $footer_settings       = $page_settings_model->get_settings( 'quanto_footer_style' );
                                $footer_local          = $page_settings_model->get_settings( 'quanto_footer_builder_option' );
                                $footer_enable_disable = $page_settings_model->get_settings( 'quanto_footer_choice' );
                            }
                        }
                    } catch ( Exception $e ) {
                        return false;
                    }
                }

                if ( $footer_enable_disable === 'yes' ) {
                    if ( $footer_settings === 'footer_builder' && ! empty( $footer_local ) ) {
                        return (int) $footer_local;
                    }

                    if ( quanto_opt( 'quanto_footer_builder_trigger' ) === 'footer_builder' ) {
                        return (int) quanto_opt( 'quanto_footer_builder_select' );
                    }
                }

                return false;
            }

            if ( is_archive() || is_home() || is_search() || is_singular() ) {
                $archive_id = quanto_opt( 'quanto_archive_footer_select_options' );
                if ( ! empty( $archive_id ) ) {
                    return (int) $archive_id;
                }
            }

            if ( quanto_opt( 'quanto_footer_builder_trigger' ) === 'footer_builder' ) {
                return (int) quanto_opt( 'quanto_footer_builder_select' );
            }

            return false;
        }
    }

    if ( ! function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
        function quanto_enqueue_elementor_post_assets( $post_id ) {
            $post_id = (int) $post_id;
            if ( ! $post_id || ! class_exists( '\\Elementor\\Plugin' ) ) {
                return;
            }

            $frontend = \Elementor\Plugin::instance()->frontend;
            if ( $frontend && method_exists( $frontend, 'enqueue_styles' ) ) {
                $frontend->enqueue_styles();
            }

            $upload_dir = wp_upload_dir();
            if ( ! empty( $upload_dir['basedir'] ) && ! empty( $upload_dir['baseurl'] ) ) {
                $css_path = trailingslashit( $upload_dir['basedir'] ) . 'elementor/css/';
                $css_url  = trailingslashit( $upload_dir['baseurl'] ) . 'elementor/css/';
                $devices  = array( 'desktop', 'laptop', 'tablet', 'mobile' );

                $active_kit_id = (int) get_option( 'elementor_active_kit' );
                if ( $active_kit_id ) {
                    $kit_file = 'post-' . $active_kit_id . '.css';
                    if ( file_exists( $css_path . $kit_file ) ) {
                        wp_enqueue_style( 'elementor-post-' . $active_kit_id, $css_url . $kit_file, array(), null );
                    }
                }

                $post_css_file = 'post-' . $post_id . '.css';
                if ( file_exists( $css_path . $post_css_file ) ) {
                    wp_enqueue_style( 'elementor-post-' . $post_id, $css_url . $post_css_file, array(), null );
                }

                foreach ( $devices as $device ) {
                    $base_file = 'base-' . $device . '.css';
                    if ( file_exists( $css_path . $base_file ) ) {
                        wp_enqueue_style( 'base-' . $device, $css_url . $base_file, array(), null );
                    }
                }

                foreach ( $devices as $device ) {
                    $local_file = 'local-' . $post_id . '-frontend-' . $device . '.css';
                    if ( file_exists( $css_path . $local_file ) ) {
                        wp_enqueue_style( 'local-' . $post_id . '-frontend-' . $device, $css_url . $local_file, array(), null );
                    }
                }
            }

            if ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $post_id );
                $css_file->enqueue();
            }
        }
    }

    // Early footer CSS enqueue: runs during wp_enqueue_scripts so CSS lands in <head>
    if ( ! function_exists( 'quanto_enqueue_footer_css_early' ) ) {
        function quanto_enqueue_footer_css_early() {
            $footer_id = quanto_get_resolved_footer_id();
            if ( $footer_id ) {
                quanto_enqueue_elementor_post_assets( $footer_id );
            }

            // Also enqueue homepage CSS early on single posts and products 
            // so the pulled tail sections have their CSS loaded in <head>
            if ( is_single() || is_singular( 'product' ) || is_singular( 'post' ) ) {
                $homepage_id = get_option( 'page_on_front' );
                if ( ! $homepage_id ) {
                    $homepage_id = 14;
                }
                quanto_enqueue_elementor_post_assets( $homepage_id );
            }
        }
    }

    // Helper: render an Elementor footer post inside a <footer> tag
    // Passing `true` as 2nd arg to get_builder_content_for_display forces inline CSS output,
    // which works regardless of when wp_head() has fired.
    if ( ! function_exists( 'quanto_render_elementor_footer' ) ) {
        function quanto_render_elementor_footer( $post_id, $class = 'footer' ) {
            quanto_enqueue_elementor_post_assets( $post_id );
            echo '<footer class="' . esc_attr( $class ) . '">';
            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
            echo '</footer>';
        }
    }



    // footer content Function
    if( !function_exists('quanto_footer_content_cb') ) {
        function quanto_footer_content_cb( ) {
            // Try to find and render a quanto_footer post by slug first
            if ( class_exists( '\\Elementor\\Plugin' ) ) {
                $plugin_instance = \Elementor\Plugin::instance();
                if ( ! empty( $plugin_instance->frontend ) && method_exists( $plugin_instance->frontend, 'get_builder_content_for_display' ) ) {
                    $footer_slugs = array( 'main-footer', 'main-fotter' );
                    $footer_id    = false;
                    foreach ( $footer_slugs as $slug ) {
                        $posts = get_posts( array(
                            'name'        => $slug,
                            'post_type'   => 'quanto_footer',
                            'post_status' => 'publish',
                            'numberposts' => 1,
                        ) );
                        if ( ! empty( $posts ) ) {
                            $footer_id = $posts[0]->ID;
                            break;
                        }
                    }
                    if ( $footer_id ) {
                        quanto_render_elementor_footer( $footer_id );
                        return;
                    }
                }
            }

            if ( class_exists( 'ReduxFramework' ) && did_action( 'elementor/loaded' ) && class_exists( '\\Elementor\\Plugin' ) ) {

                if ( is_page() || is_page_template( 'template-builder.php' ) ) {
                    $post_id               = get_the_ID();
                    $footer_enable_disable = '';
                    $footer_settings       = '';
                    $footer_local          = '';

                    if (
                        class_exists( '\\Elementor\\Core\\Settings\\Manager' ) &&
                        method_exists( '\\Elementor\\Core\\Settings\\Manager', 'get_settings_managers' )
                    ) {
                        try {
                            $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
                            if ( $page_settings_manager && method_exists( $page_settings_manager, 'get_model' ) ) {
                                $page_settings_model = $page_settings_manager->get_model( $post_id );
                                if ( $page_settings_model ) {
                                    $footer_settings       = $page_settings_model->get_settings( 'quanto_footer_style' );
                                    $footer_local          = $page_settings_model->get_settings( 'quanto_footer_builder_option' );
                                    $footer_enable_disable = $page_settings_model->get_settings( 'quanto_footer_choice' );
                                }
                            }
                        } catch ( Exception $e ) {
                            // fall through
                        }
                    }

                    if ( $footer_enable_disable === 'yes' ) {
                        if ( $footer_settings === 'footer_builder' && ! empty( $footer_local ) ) {
                            $quanto_local_footer = get_post( $footer_local );
                            if ( $quanto_local_footer ) {
                                quanto_render_elementor_footer( $quanto_local_footer->ID );
                            }
                        } else {
                            $trigger = quanto_opt( 'quanto_footer_builder_trigger' );
                            if ( $trigger === 'footer_builder' ) {
                                $global = get_post( quanto_opt( 'quanto_footer_builder_select' ) );
                                if ( $global ) {
                                    quanto_render_elementor_footer( $global->ID );
                                } else {
                                    quanto_footer_global_option();
                                }
                            } else {
                                quanto_footer_global_option();
                            }
                        }
                    } else {
                        quanto_footer_global_option();
                    }

                } elseif ( is_archive() || is_home() || is_search() || is_singular() ) {

                    $archive_footer_id = quanto_opt( 'quanto_archive_footer_select_options' );

                    if ( ! empty( $archive_footer_id ) ) {
                        $footer_post = get_post( $archive_footer_id );
                        if ( $footer_post ) {
                            quanto_render_elementor_footer( $footer_post->ID );
                        } else {
                            quanto_footer_global_option();
                        }
                    } else {
                        $trigger = quanto_opt( 'quanto_footer_builder_trigger' );
                        if ( $trigger === 'footer_builder' ) {
                            $global = get_post( quanto_opt( 'quanto_footer_builder_select' ) );
                            if ( $global ) {
                                quanto_render_elementor_footer( $global->ID );
                            } else {
                                quanto_footer_global_option();
                            }
                        } else {
                            quanto_footer_global_option();
                        }
                    }

                } else {

                    $trigger = quanto_opt( 'quanto_footer_builder_trigger' );
                    if ( $trigger === 'footer_builder' ) {
                        $global = get_post( quanto_opt( 'quanto_footer_builder_select' ) );
                        if ( $global ) {
                            quanto_render_elementor_footer( $global->ID );
                        } else {
                            quanto_footer_global_option();
                        }
                    } else {
                        quanto_footer_global_option();
                    }
                }

            } else {
                // Elementor or Redux not active – use prebuilt footer
                quanto_footer_global_option();
            }

        }
    }

    // blog details wrapper start hook function
    if( !function_exists('quanto_blog_details_wrapper_start_cb') ) {
        function quanto_blog_details_wrapper_start_cb( ) {
            if( class_exists('ReduxFramework') ) {
                echo '<section class="blog-page-sec blog-detail-page section-padding-bottom">';
            } else {
                echo '<section class="blog-page-sec blog-detail-page local-blog-detail-page section-padding-bottom">';
            }
                echo '<div class="container custom-container">';
                    echo '<div class="row">';
        }
    }

    // blog details column wrapper start hook function
    if( !function_exists('quanto_blog_details_col_start_cb') ) {
        function quanto_blog_details_col_start_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_blog_single_sidebar = quanto_opt('quanto_blog_single_sidebar');
                if( $quanto_blog_single_sidebar == '2' && is_active_sidebar('quanto-blog-sidebar') ) {
                    echo '<div class="col-lg-8 order-last">';
                } elseif( $quanto_blog_single_sidebar == '3' && is_active_sidebar('quanto-blog-sidebar') ) {
                    echo '<div class="col-lg-8">';
                } else {
                    echo '<div class="col-lg-12">';
                }

            } else {
                if( is_active_sidebar('quanto-blog-sidebar') ) {
                    echo '<div class="col-lg-8">';
                } else {
                    echo '<div class="col-lg-12">';
                }
            }
        }
    }


    // blog post meta hook function
    if( !function_exists('quanto_blog_post_meta_cb') ) {
        function quanto_blog_post_meta_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_display_post_date      =  quanto_opt('quanto_display_post_date');
                $quanto_display_post_category   =  quanto_opt('quanto_display_post_category');

            } else {
                $quanto_display_post_date      = '1';
                $quanto_display_post_category   = '1';
            }

            echo '<!-- Blog Meta -->';
            echo '<div class="blog-meta">';

                if( $quanto_display_post_category ){
                    quanto_blog_category();
                }
                
                if( $quanto_display_post_date ){
                    echo '<span class="quanto-blog-date">';
                        echo esc_html( get_the_date() );
                    echo '</span>';
                }

            echo '</div>';

        }
    }

    
    // blog details post meta hook function
    if( !function_exists('quanto_blog_details_post_meta_cb') ) {
        function quanto_blog_details_post_meta_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_display_post_details_date      =  quanto_opt('quanto_display_post_details_date');
                $quanto_display_post_details_category   =  quanto_opt('quanto_display_post_details_category');
                $quanto_display_post_author      =  quanto_opt('quanto_display_post_author');

            } else {
                $quanto_display_post_details_date      = '1';
                $quanto_display_post_details_category   = '1';
                $quanto_display_post_author   = '1';
            }

            echo '<!-- Blog Meta -->';
            echo '<div class="meta-box">';
                echo '<ul class="custom-ul meta-info d-flex">';

                    if( $quanto_display_post_details_date ){
                        echo '<li><span><a href="'.esc_url( quanto_blog_date_permalink() ).'">';
                            echo esc_html( get_the_date( 'F d, Y' ) );
                        echo '</a></span></li>';

                    }
                    
                    if( $quanto_display_post_details_category ){
                        quanto_blog_category();
                    }

                    echo '<li><span><a href="' . esc_url( get_author_posts_url( get_the_author_meta('ID') ) ) . '">';
                        echo 'by ' . esc_html( ucwords( get_the_author() ) );
                    echo '</a></span></li>';

                echo '</ul>';
            echo '</div>';

        }
    }

    // blog details share options hook function
    if( !function_exists('quanto_blog_details_share_options_cb') ) {
        function quanto_blog_details_share_options_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_post_details_share_options = quanto_opt('quanto_post_details_share_options');
            } else {
                $quanto_post_details_share_options = false;
            }
            if( function_exists( 'quanto_social_sharing_buttons' ) && $quanto_post_details_share_options ) {
                    echo '<ul class="custom-ul">';
                        echo quanto_social_sharing_buttons();
                    echo '</ul>';
            }
        }
    }

    // Blog Details Comments hook function
    if( !function_exists('quanto_blog_details_comments_cb') ) {
        function quanto_blog_details_comments_cb( ) {
            if ( ! comments_open() ) {
                echo '<div class="blog-comment-area">';
                    echo quanto_heading_tag( array(
                        "tag"   => "h3",
                        "text"  => esc_html__( 'Comments are closed', 'quanto' ),
                        "class" => "inner-title"
                    ) );
                echo '</div>';
            }

            // comment template.
            if ( comments_open() || get_comments_number() ) {
                comments_template();
            }
        }
    }

    // Blog Details Related Post hook function
    if( !function_exists('quanto_blog_details_related_post_cb') ) {
        function quanto_blog_details_related_post_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_excerpt_length = '4';
                $quanto_post_details_related_post = quanto_opt('quanto_post_details_related_post');
            } else {
                $quanto_excerpt_length = '4';
                $quanto_post_details_related_post = false;
            }
            $relatedpost = new WP_Query( array(
                "post_type"         => "post",
                "posts_per_page"    => "3",
                "category__in"      => wp_get_post_categories(get_the_ID()),
                "post__not_in"      =>  array( get_the_ID() )
            ) );
            if( $relatedpost->have_posts() && $quanto_post_details_related_post ) {
                echo '<!-- Related Post -->';
                echo '<div class="quanto-blog-section section-padding-bottom overflow-hidden">';
                    echo '<div class="container custom-container">';
                        echo '<div class="row">';
                            echo '<div class="col-12">';
                                echo '<div class="quanto__header text-center text-md-start row-padding-bottom">';
                                    echo '<h3 class="title fade-anim" data-delay="0.30" data-direction="left">'.esc_html__( 'Related Articles', 'quanto' ).'</h3>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';

                        echo '<div class="row gx-4 gy-5">';
                            while( $relatedpost->have_posts() ) {
                                $relatedpost->the_post();
                                echo '<div class="col-md-6 col-lg-4">';
                                    echo '<!-- Single Post -->';
                                    echo '<div class="quanto-blog-box fade-anim" data-delay="0.45" data-direction="right">';
                                        if( has_post_thumbnail(  ) ){
                                            echo '<div class="quanto-blog-thumb">';
                                                echo '<a href="'.esc_url( get_permalink() ).'" class="post-thumbnail">';
                                                    the_post_thumbnail( 'quanto-more-detail' );
                                                echo '</a>';
                                            echo '</div>';
                                        }

                                        echo '<div class="quanto-blog-content">';

                                            // Blog Post Meta
                                            do_action( 'quanto_blog_post_meta' );

                                            if( get_the_title() ){
                                                echo '<!-- Post Title -->';
                                                echo '<h5 class="line-clamp-2"><a href="'.esc_url( get_permalink() ).'">'.esc_html( wp_trim_words( get_the_title(), '12', '' ) ).'</a></h5>';
                                                echo '<!-- End Post Title -->';
                                            }

                                            // Excerpt And Read More Button
                                            do_action( 'quanto_blog_postexcerpt_read_content' );
                                            
                                        echo '</div>';
                                    echo '</div>';
                                    echo '<!-- End Single Post -->';
                                echo '</div>';
                            }
                            wp_reset_postdata();
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
                echo '<!-- End Related Post -->';
            }
        }
    }

    // Blog Details Column end hook function
    if( !function_exists('quanto_blog_details_col_end_cb') ) {
        function quanto_blog_details_col_end_cb( ) {
            echo '</div>';
        }
    }

    // Blog Details Wrapper end hook function
    if( !function_exists('quanto_blog_details_wrapper_end_cb') ) {
        function quanto_blog_details_wrapper_end_cb( ) {
                    echo '</div>';
                echo '</div>';
            echo '</section>';
        }
    }

    // page start wrapper hook function
    if( !function_exists('quanto_page_start_wrap_cb') ) {
        function quanto_page_start_wrap_cb( ) {
            if( is_page( 'cart' ) ){
                $section_class = "quanto-cart-wrapper quanto-section-padding blog-details";
            }elseif( is_page( 'checkout' ) ){
                $section_class = "quanto-checkout-wrapper quanto-section-padding blog-details";
            }else{
                $section_class = "quanto-page-section";
            }
            echo '<section class="'.esc_attr( $section_class ).'">';
                echo '<div class="container">';
                    echo '<div class="row">';
        }
    }

    // page wrapper end hook function
    if( !function_exists('quanto_page_end_wrap_cb') ) {
        function quanto_page_end_wrap_cb( ) {
                    echo '</div>';
                echo '</div>';
            echo '</section>';
        }
    }

    // page column wrapper start hook function
    if( !function_exists('quanto_page_col_start_wrap_cb') ) {
        function quanto_page_col_start_wrap_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_page_sidebar = quanto_opt('quanto_page_sidebar');
            }else {
                $quanto_page_sidebar = '1';
            }
            if( $quanto_page_sidebar == '2' && is_active_sidebar('quanto-page-sidebar') ) {
                echo '<div class="col-lg-8 order-last">';
            } elseif( $quanto_page_sidebar == '3' && is_active_sidebar('quanto-page-sidebar') ) {
                echo '<div class="col-lg-8">';
            } else {
                echo '<div class="col-lg-12">';
            }

        }
    }

    // page column wrapper end hook function
    if( !function_exists('quanto_page_col_end_wrap_cb') ) {
        function quanto_page_col_end_wrap_cb( ) {
            echo '</div>';
        }
    }

    // page sidebar hook function
    if( !function_exists('quanto_page_sidebar_cb') ) {
        function quanto_page_sidebar_cb( ) {
            if( class_exists('ReduxFramework') ) {
                $quanto_page_sidebar = quanto_opt('quanto_page_sidebar');
            }else {
                $quanto_page_sidebar = '1';
            }

            if( class_exists('ReduxFramework') ) {
                $quanto_page_layoutopt = quanto_opt('quanto_page_layoutopt');
            }else {
                $quanto_page_layoutopt = '3';
            }

            if( $quanto_page_layoutopt == '1' && $quanto_page_sidebar != 1 ) {
                get_sidebar('page');
            } elseif( $quanto_page_layoutopt == '2' && $quanto_page_sidebar != 1 ) {
                get_sidebar();
            }
        }
    }

    // page content hook function
    if( !function_exists('quanto_page_content_cb') ) {
        function quanto_page_content_cb( ) {
            
            echo '<div class="page--content clearfix">';
                the_content();

                // Link Pages
                quanto_link_pages();

            echo '</div>';
            // comment template.
            if ( comments_open() || get_comments_number() ) {
                comments_template();
            }

        }
    }
    if( !function_exists('quanto_blog_post_thumb_cb') ) {
        function quanto_blog_post_thumb_cb( ) {
            if( get_post_format() ) {
                $format = get_post_format();
            }else{
                $format = 'standard';
            }

            $quanto_post_slider_thumbnail = quanto_meta( 'post_format_slider' );

            if( !empty( $quanto_post_slider_thumbnail ) ){
                if ( ! is_single() ) {
                    echo '<div class="quanto-blog-thumb quanto-carousel" data-arrows="true" data-slide-show="1" data-fade="true">';
                    foreach ( $quanto_post_slider_thumbnail as $single_image ) {
                        if( class_exists( 'ReduxFramework' )){
                            $quanto_blog_style = quanto_opt('quanto_blog_style');

                            if('blog_style_one' == $quanto_blog_style ){
                                echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
                            }elseif('blog_style_two' == $quanto_blog_style ){
                                echo '<a href="' . esc_url( get_permalink() ) . '" class="d-inline-block overflow-hidden">';
                            }
                        }else{
                            echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
                        }
                            echo quanto_img_tag( array(
                                'url' => esc_url( $single_image )
                            ) );
                        echo '</a>';
                    }
                    echo '</div>';
                } else {
                    echo '<div class="img-box overflow-hidden">';
                    foreach ( $quanto_post_slider_thumbnail as $single_image ) {
                        echo quanto_img_tag( array(
                            'url' => esc_url( $single_image )
                        ) );
                    }
                    echo '</div>';
                }
            }elseif( has_post_thumbnail() && $format == 'standard' ) {
                if( ! is_single() ){
                    echo '<div class="quanto-blog-thumb">';
                        if( class_exists( 'ReduxFramework' )){
                            $quanto_blog_style = quanto_opt('quanto_blog_style');

                            if('blog_style_one' == $quanto_blog_style ){
                                echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
                            }elseif('blog_style_two' == $quanto_blog_style ){
                                echo '<a href="' . esc_url( get_permalink() ) . '" class="d-inline-block overflow-hidden">';
                            }
                        }else{
                            echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
                        }
                            the_post_thumbnail();
                        echo '</a>';
                    echo '</div>';
                } else {
                    echo '<div class="img-box overflow-hidden">';
                        the_post_thumbnail( 'full', array(
                            'class' => 'w-100 d-block',
                            'alt'   => get_the_title(),
                            'data-speed' => '0.8',
                        ) );
                    echo '</div>';
                }
            }elseif( $format == 'video' ){
                if( has_post_thumbnail() && !empty ( quanto_meta( 'post_format_video' ) ) ){
                    if( ! is_single() ){
                        echo '<div class="blog-video quanto-blog-thumb">';
                            if( class_exists( 'ReduxFramework' )){
                                $quanto_blog_style = quanto_opt('quanto_blog_style');

                                if('blog_style_one' == $quanto_blog_style ){
                                    echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
                                }elseif('blog_style_two' == $quanto_blog_style ){
                                    echo '<a href="' . esc_url( get_permalink() ) . '" class="d-inline-block overflow-hidden">';
                                }
                            }else{
                                echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
                            }
                                the_post_thumbnail();
                            echo '</a>';
                            echo '<a href="'.esc_url( quanto_meta( 'post_format_video' ) ).'" class="play-btn popup-video">';
                            echo '<i class="fas fa-play"></i>';
                            echo '</a>';
                        echo '</div>';
                    } else {
                        echo '<div class="img-box overflow-hidden">';
                            the_post_thumbnail( 'full', array(
                                'class' => 'w-100 d-block',
                                'alt'   => get_the_title(),
                                'data-speed' => '0.8',
                            ) );
                            echo '<a href="'.esc_url( quanto_meta( 'post_format_video' ) ).'" class="play-btn popup-video">';
                                echo '<i class="fas fa-play"></i>';
                            echo '</a>';
                        echo '</div>';
                    }

                }elseif( ! has_post_thumbnail() && ! is_single() ){
                    echo '<div class="blog-video">';
                        if( ! is_single() ){
                            if( class_exists( 'ReduxFramework' )){
                                $quanto_blog_style = quanto_opt('quanto_blog_style');

                                if('blog_style_one' == $quanto_blog_style ){
                                    echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
                                }elseif('blog_style_two' == $quanto_blog_style ){
                                    echo '<a href="' . esc_url( get_permalink() ) . '" class="d-inline-block overflow-hidden">';
                                }
                            }else{
                                echo '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';
                            }
                        }
                            echo quanto_embedded_media( array( 'video', 'iframe' ) );
                        if( ! is_single() ){
                            echo '</a>';
                        }
                       
                    echo '</div>';
                }
            }elseif( $format == 'audio' ){
                $quanto_audio = quanto_meta( 'post_format_audio' );
                if( !empty( $quanto_audio ) ){
                    echo '<div class="blog-audio blog-image">';
                            echo wp_oembed_get( $quanto_audio );
                           
                    echo '</div>';
                }elseif( !is_single() ){
                    echo '<div class="blog-audio blog-image">';
                            echo quanto_embedded_media( array( 'audio', 'iframe' ) );
                           
                    echo '</div>';
                }
            }

        }
    }

    if( !function_exists( 'quanto_blog_post_content_cb' ) ) {
        function quanto_blog_post_content_cb( ) {
            $allowhtml = array(
                'p'         => array(
                    'class'     => array()
                ),
                'span'      => array(),
                'a'         => array(
                    'href'      => array(),
                    'title'     => array()
                ),
                'br'        => array(),
                'em'        => array(),
                'strong'    => array(),
                'b'         => array(),
                'sup'       => array(),
                'sub'       => array(),
            );
            echo '<!-- blog-content -->';

            echo '<div class="quanto-blog-content">';

                if( class_exists( 'ReduxFramework' )){
                    $quanto_blog_style = quanto_opt('quanto_blog_style');

                    if('blog_style_one' == $quanto_blog_style ){
                        // Blog Post Meta
                        do_action( 'quanto_blog_post_meta' );

                        if( ! is_single() ){
                            echo '<h5 class="line-clamp-2"><a href="'.esc_url( get_permalink() ).'">'.wp_kses( get_the_title(), $allowhtml ).'</a></h5>';
                        }
                    }elseif('blog_style_two' == $quanto_blog_style ){
                        // Blog Post Meta
                        do_action( 'quanto_blog_post_meta' );

                        if( ! is_single() ){
                            echo '<h5 class="line-clamp-3"><a href="'.esc_url( get_permalink() ).'">'.wp_kses( get_the_title(), $allowhtml ).'</a></h5>';
                        }
                    }
                }else{
                    // Blog Post Meta
                    do_action( 'quanto_blog_post_meta' );

                    if( ! is_single() ){
                        echo '<h5 class="line-clamp-2"><a href="'.esc_url( get_permalink() ).'">'.wp_kses( get_the_title(), $allowhtml ).'</a></h5>';
                    }
                }

                // Excerpt And Read More Button
                do_action( 'quanto_blog_postexcerpt_read_content' );

            echo '</div>';
            echo '<!-- End Post Content -->';
        }
    }

    if( ! function_exists( 'quanto_blog_postexcerpt_read_content_cb') ) {
        function quanto_blog_postexcerpt_read_content_cb( ) {
            if( class_exists( 'ReduxFramework' ) ) {
                $quanto_excerpt_length = quanto_opt('quanto_blog_postExcerpt');
            } else {
                $quanto_excerpt_length = '24';
            }
            $allowhtml = array(
                'p'         => array(
                    'class'     => array()
                ),
                'span'      => array(),
                'a'         => array(
                    'href'      => array(),
                    'title'     => array()
                ),
                'br'        => array(),
                'em'        => array(),
                'strong'    => array(),
                'b'         => array(),
            );

            if( class_exists( 'ReduxFramework' ) ) {
                $quanto_blog_admin = quanto_opt( 'quanto_blog_post_author' );
                $quanto_blog_readmore_setting_val = quanto_opt('quanto_blog_readmore_setting');
                if( $quanto_blog_readmore_setting_val == 'custom' ) {
                    $quanto_blog_readmore_setting = quanto_opt('quanto_blog_custom_readmore');
                } else {
                    $quanto_blog_readmore_setting = __( 'Read More', 'quanto' );
                }
            } else {
                $quanto_blog_readmore_setting = __( 'Read More', 'quanto' );
                $quanto_blog_admin = true;
            }

            echo '<!-- Post Summary -->';
                echo quanto_paragraph_tag( array(
                    "text"  => wp_kses( wp_trim_words( get_the_excerpt(), $quanto_excerpt_length, '' ), $allowhtml ),
                    "class" => 'blog-text',
                ) );
            echo '<!-- End Post Summary -->';
            

            if( $quanto_blog_admin || !empty( $quanto_blog_readmore_setting ) ){
                if( !empty( $quanto_blog_readmore_setting ) ){
                    echo '<!-- Button -->';
                        if( class_exists( 'ReduxFramework' )){
                            $quanto_blog_style = quanto_opt('quanto_blog_style');

                            if('blog_style_one' == $quanto_blog_style ){
                                echo '<a href="'.esc_url( get_permalink() ).'" class="quanto-link-btn btn-pill">';
                            }elseif('blog_style_two' == $quanto_blog_style ){
                                echo '<a href="'.esc_url( get_permalink() ).'" class="quanto-link-btn">';
                            }
                        }else{
                            echo '<a href="'.esc_url( get_permalink() ).'" class="quanto-link-btn btn-pill">';
                        }
                            echo esc_html( $quanto_blog_readmore_setting );
                            echo '<span>';
                                echo '<i class="fa-solid fa-arrow-right arry1"></i>';
                                echo '<i class="fa-solid fa-arrow-right arry2"></i>';
                            echo '</span>';
                        echo '</a>';
                    echo '<!-- End Button -->';
                }
            }




        }
    }


    add_action( 'quanto_before_content', function () {
        echo '<div id="smooth-wrapper"><div id="smooth-content">';
    } );
    add_action( 'quanto_after_content', function () {
        echo '</div></div>';
    } );

    if ( ! function_exists( 'quanto_elementor_tree_contains_id' ) ) {
        function quanto_elementor_tree_contains_id( $element, $target_id ) {
            if ( ! is_array( $element ) ) {
                return false;
            }

            if ( isset( $element['id'] ) && $element['id'] === $target_id ) {
                return true;
            }

            if ( empty( $element['elements'] ) || ! is_array( $element['elements'] ) ) {
                return false;
            }

            foreach ( $element['elements'] as $child_element ) {
                if ( quanto_elementor_tree_contains_id( $child_element, $target_id ) ) {
                    return true;
                }
            }

            return false;
        }
    }

    if ( ! function_exists( 'quanto_find_elementor_top_level_element' ) ) {
        function quanto_find_elementor_top_level_element( $elements, $target_id ) {
            if ( ! is_array( $elements ) ) {
                return null;
            }

            foreach ( $elements as $element ) {
                if ( quanto_elementor_tree_contains_id( $element, $target_id ) ) {
                    return $element;
                }
            }

            return null;
        }
    }

    if ( ! function_exists( 'quanto_get_homepage_elementor_data' ) ) {
        function quanto_get_homepage_elementor_data( &$homepage_id = null ) {
            $homepage_id = get_option( 'page_on_front' );
            if ( ! $homepage_id ) {
                $homepage_id = 14; // Fallback
            }

            if ( function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
                quanto_enqueue_elementor_post_assets( $homepage_id );
            } elseif ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();
            }

            $meta = get_post_meta( $homepage_id, '_elementor_data', true );
            if ( ! $meta ) {
                return null;
            }

            $data = json_decode( $meta, true );
            return is_array( $data ) ? array_values( $data ) : null;
        }
    }

    if ( ! function_exists( 'quanto_render_homepage_section_from_end' ) ) {
        function quanto_render_homepage_section_from_end( $offset_from_end ) {
            if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
                return false;
            }

            $homepage_id = null;
            $data        = quanto_get_homepage_elementor_data( $homepage_id );
            if ( empty( $data ) ) {
                return false;
            }

            $index = count( $data ) - absint( $offset_from_end );
            if ( ! isset( $data[ $index ] ) ) {
                return false;
            }

            $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $data[ $index ] );
            if ( ! $element_instance ) {
                return false;
            }

            echo '<div data-elementor-type="wp-page" data-elementor-id="' . esc_attr( $homepage_id ) . '" class="elementor elementor-' . esc_attr( $homepage_id ) . '">';
            $element_instance->print_element();
            echo '</div>';

            return true;
        }
    }

    if ( ! function_exists( 'quanto_filter_elementor_section_siblings' ) ) {
        function quanto_filter_elementor_section_siblings( $elements ) {
            if ( ! is_array( $elements ) ) {
                return array();
            }

            return array_values( array_filter( $elements, function( $element ) {
                return is_array( $element )
                    && ! empty( $element['elType'] )
                    && in_array( $element['elType'], array( 'section', 'container', 'e-flexbox' ), true );
            } ) );
        }
    }

    if ( ! function_exists( 'quanto_find_elementor_section_group' ) ) {
        function quanto_find_elementor_section_group( $elements, $minimum_count = 3 ) {
            $section_siblings = quanto_filter_elementor_section_siblings( $elements );
            if ( count( $section_siblings ) >= $minimum_count ) {
                return $section_siblings;
            }

            if ( ! is_array( $elements ) ) {
                return array();
            }

            foreach ( $elements as $element ) {
                if ( empty( $element['elements'] ) || ! is_array( $element['elements'] ) ) {
                    continue;
                }

                $found = quanto_find_elementor_section_group( $element['elements'], $minimum_count );
                if ( ! empty( $found ) ) {
                    return $found;
                }
            }

            return array();
        }
    }

    if ( ! function_exists( 'quanto_print_homepage_tail_inline_styles' ) ) {
        function quanto_print_homepage_tail_inline_styles() {
            static $printed = false;

            if ( $printed ) {
                return;
            }

            echo '<style id="quanto-homepage-tail-inline-css">
                .quanto-homepage-tail-sections{width:100%;overflow:hidden}
                .quanto-homepage-tail-sections>.elementor{width:100%}
                .quanto-homepage-tail-sections .elementor-section .elementor-container{display:flex;margin-left:auto;margin-right:auto;max-width:min(100%,var(--content-width,1320px));width:100%}
                .quanto-homepage-tail-sections .elementor-column{display:flex;min-height:1px;position:relative}
                .quanto-homepage-tail-sections .elementor-column-wrap,.quanto-homepage-tail-sections .elementor-widget-wrap{align-content:flex-start;display:flex;flex-wrap:wrap;position:relative;width:100%}
                .quanto-homepage-tail-sections .elementor-widget,.quanto-homepage-tail-sections .elementor-widget-container{max-width:100%}
                .quanto-homepage-tail-sections .elementor-heading-title{margin:0}
                @media (max-width:767px){.quanto-homepage-tail-sections .e-con,.quanto-homepage-tail-sections .e-con>.e-con-inner{flex-direction:var(--mobile-flex-direction,column)}}
                .elementor .e-flexbox-base {
                    padding: 10px;
                    display: flex;
                    flex-direction: row;
                }
            </style>';
            
            // Explicitly print the Elementor CSS for the homepage, otherwise the sections will lose their styling when rendered late on other pages.
            $homepage_id = get_option( 'page_on_front' );
            if ( ! $homepage_id ) {
                $homepage_id = 14; // Fallback
            }
            if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();
                $css_file->print_css();
            }
            
            // Set elementor in page to true so that print_element() properly enqueues widget CSS
            if ( class_exists( '\Elementor\Plugin' ) ) {
                \Elementor\Plugin::$instance->frontend->has_elementor_in_page( true );
                
                // Print core Elementor styles to ensure flexbox and widget layouts are correct
                if ( function_exists( 'wp_print_styles' ) ) {
                    wp_print_styles( array(
                        'elementor-frontend',
                        'e-flexbox',
                        'e-container'
                    ) );
                }
            }
            
            $printed = true;
        }
    }

    if ( ! function_exists( 'quanto_render_homepage_tail_sections' ) ) {
        function quanto_render_homepage_tail_sections( $count = 3 ) {
            static $rendered = false;

            if ( $rendered || ! class_exists( '\\Elementor\\Plugin' ) ) {
                return $rendered;
            }

            $homepage_id = null;
            $data        = quanto_get_homepage_elementor_data( $homepage_id );

            if ( ! $homepage_id || empty( $data ) ) {
                return false;
            }

            $section_group = quanto_find_elementor_section_group( $data, absint( $count ) );
            $elements      = array_slice( $section_group, - absint( $count ) );
            if ( empty( $elements ) ) {
                return false;
            }

            quanto_print_homepage_tail_inline_styles();

            echo '<div class="quanto-homepage-tail-sections" style="--quanto-homepage-tail-count:' . esc_attr( absint( $count ) ) . ';">';

            echo '<div data-elementor-type="wp-page" data-elementor-id="' . esc_attr( $homepage_id ) . '" class="elementor elementor-' . esc_attr( $homepage_id ) . '">';

            foreach ( $elements as $element_data ) {
                $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );
                if ( $element_instance ) {
                    $element_instance->print_element();
                }
            }

            echo '</div>';
            echo '</div>';

            $rendered = true;
            return true;
        }
    }

    if ( ! function_exists( 'quanto_render_homepage_who_we_serve_section' ) ) {
        function quanto_render_homepage_who_we_serve_section() {
            if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
                return;
            }

            $homepage_id = get_option( 'page_on_front' );
            if ( ! $homepage_id ) {
                $homepage_id = 14; // Fallback
            }

            if ( function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
                quanto_enqueue_elementor_post_assets( $homepage_id );
            } elseif ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();
            }

            $meta = get_post_meta( $homepage_id, '_elementor_data', true );
            if ( ! $meta ) {
                return;
            }

            $data = json_decode( $meta, true );
            if ( ! is_array( $data ) ) {
                return;
            }

            // Helper function to find element recursively
            $find_element = null;
            $find_element = function( $elements, $id ) use ( &$find_element ) {
                foreach ( $elements as $element ) {
                    if ( isset( $element['id'] ) && $element['id'] === $id ) {
                        return $element;
                    }
                    if ( isset( $element['elements'] ) && ! empty( $element['elements'] ) ) {
                        $found = $find_element( $element['elements'], $id );
                        if ( $found ) {
                            return $found;
                        }
                    }
                }
                return null;
            };

            $element_data = $find_element( $data, '5e656d6' );
            if ( $element_data ) {
                $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );
                if ( $element_instance ) {
                    quanto_print_homepage_tail_inline_styles();
                    echo '<div data-elementor-type="wp-page" data-elementor-id="' . esc_attr( $homepage_id ) . '" class="elementor quanto-homepage-tail-sections elementor-' . esc_attr( $homepage_id ) . '">';
                    $element_instance->print_element();
                    echo '</div>';
                }
            }
        }
    }



    if ( ! function_exists( 'quanto_render_homepage_client_testimonials_section' ) ) {
        function quanto_render_homepage_client_testimonials_section() {
            if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
                return;
            }

            $homepage_id = get_option( 'page_on_front' );
            if ( ! $homepage_id ) {
                $homepage_id = 14; // Fallback
            }

            if ( function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
                quanto_enqueue_elementor_post_assets( $homepage_id );
            } elseif ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();
            }

            $meta = get_post_meta( $homepage_id, '_elementor_data', true );
            if ( ! $meta ) {
                return;
            }

            $data = json_decode( $meta, true );
            if ( ! is_array( $data ) ) {
                return;
            }

            // Helper function to find element recursively
            $find_element = null;
            $find_element = function( $elements, $id ) use ( &$find_element ) {
                foreach ( $elements as $element ) {
                    if ( isset( $element['id'] ) && $element['id'] === $id ) {
                        return $element;
                    }
                    if ( isset( $element['elements'] ) && ! empty( $element['elements'] ) ) {
                        $found = $find_element( $element['elements'], $id );
                        if ( $found ) {
                            return $found;
                        }
                    }
                }
                return null;
            };

            $element_data = $find_element( $data, '82ef444' );
            if ( $element_data ) {
                $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );
                if ( $element_instance ) {
                    quanto_print_homepage_tail_inline_styles();
                    echo '<div data-elementor-type="wp-page" data-elementor-id="' . esc_attr( $homepage_id ) . '" class="elementor quanto-homepage-tail-sections elementor-' . esc_attr( $homepage_id ) . '">';
                    $element_instance->print_element();
                    echo '</div>';
                }
            }
        }
    }


    if ( ! function_exists( 'quanto_render_homepage_global_brands_section' ) ) {
        function quanto_render_homepage_global_brands_section() {
            if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
                return;
            }

            $homepage_id = get_option( 'page_on_front' );
            if ( ! $homepage_id ) {
                $homepage_id = 14; // Fallback
            }

            if ( function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
                quanto_enqueue_elementor_post_assets( $homepage_id );
            } elseif ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();
            }

            $meta = get_post_meta( $homepage_id, '_elementor_data', true );
            if ( ! $meta ) {
                return;
            }

            $data = json_decode( $meta, true );
            if ( ! is_array( $data ) ) {
                return;
            }

            // Helper function to find element recursively
            $find_element = null;
            $find_element = function( $elements, $id ) use ( &$find_element ) {
                foreach ( $elements as $element ) {
                    if ( isset( $element['id'] ) && $element['id'] === $id ) {
                        return $element;
                    }
                    if ( isset( $element['elements'] ) && ! empty( $element['elements'] ) ) {
                        $found = $find_element( $element['elements'], $id );
                        if ( $found ) {
                            return $found;
                        }
                    }
                }
                return null;
            };

            $element_data = $find_element( $data, 'c9502e0' );
            if ( $element_data ) {
                $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );
                if ( $element_instance ) {
                    quanto_print_homepage_tail_inline_styles();
                    echo '<div data-elementor-type="wp-page" data-elementor-id="' . esc_attr( $homepage_id ) . '" class="elementor quanto-homepage-tail-sections elementor-' . esc_attr( $homepage_id ) . '">';
                    $element_instance->print_element();
                    echo '</div>';
                }
            }
        }
    }


    if ( ! function_exists( 'quanto_render_homepage_challenge_research_section' ) ) {
        function quanto_render_homepage_challenge_research_section() {
            if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
                return;
            }

            $homepage_id = get_option( 'page_on_front' );
            if ( ! $homepage_id ) {
                $homepage_id = 14; // Fallback
            }

            if ( function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
                quanto_enqueue_elementor_post_assets( $homepage_id );
            } elseif ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();
            }

            $meta = get_post_meta( $homepage_id, '_elementor_data', true );
            if ( ! $meta ) {
                return;
            }

            $data = json_decode( $meta, true );
            if ( ! is_array( $data ) ) {
                return;
            }

            // Helper function to find element recursively
            $find_element = null;
            $find_element = function( $elements, $id ) use ( &$find_element ) {
                foreach ( $elements as $element ) {
                    if ( isset( $element['id'] ) && $element['id'] === $id ) {
                        return $element;
                    }
                    if ( isset( $element['elements'] ) && ! empty( $element['elements'] ) ) {
                        $found = $find_element( $element['elements'], $id );
                        if ( $found ) {
                            return $found;
                        }
                    }
                }
                return null;
            };

            $element_data = $find_element( $data, 'ac412d3' );
            if ( $element_data ) {
                $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );
                if ( $element_instance ) {
                    quanto_print_homepage_tail_inline_styles();
                    echo '<div data-elementor-type="wp-page" data-elementor-id="' . esc_attr( $homepage_id ) . '" class="elementor quanto-homepage-tail-sections elementor-' . esc_attr( $homepage_id ) . '">';
                    $element_instance->print_element();
                    echo '</div>';
                }
            }
        }
    }

    if ( ! function_exists( 'quanto_render_homepage_connect_trends_section' ) ) {
        function quanto_render_homepage_connect_trends_section() {
            if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
                return;
            }

            $homepage_id = get_option( 'page_on_front' );
            if ( ! $homepage_id ) {
                $homepage_id = 14; // Fallback
            }

            if ( function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
                quanto_enqueue_elementor_post_assets( $homepage_id );
            } elseif ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();
            }

            $meta = get_post_meta( $homepage_id, '_elementor_data', true );
            if ( ! $meta ) {
                return;
            }

            $data = json_decode( $meta, true );
            if ( ! is_array( $data ) ) {
                return;
            }

            // Helper function to find element recursively
            $find_element = null;
            $find_element = function( $elements, $id ) use ( &$find_element ) {
                foreach ( $elements as $element ) {
                    if ( isset( $element['id'] ) && $element['id'] === $id ) {
                        return $element;
                    }
                    if ( isset( $element['elements'] ) && ! empty( $element['elements'] ) ) {
                        $found = $find_element( $element['elements'], $id );
                        if ( $found ) {
                            return $found;
                        }
                    }
                }
                return null;
            };

            $element_data = $find_element( $data, '7c312a9' );
            if ( $element_data ) {
                $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );
                if ( $element_instance ) {
                    quanto_print_homepage_tail_inline_styles();
                    echo '<div data-elementor-type="wp-page" data-elementor-id="' . esc_attr( $homepage_id ) . '" class="elementor quanto-homepage-tail-sections elementor-' . esc_attr( $homepage_id ) . '">';
                    $element_instance->print_element();
                    echo '</div>';
                }
            }
        }
    }

    if ( ! function_exists( 'quanto_render_homepage_connect_footer_section' ) ) {
        function quanto_render_homepage_connect_footer_section() {
            if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
                return;
            }

            $homepage_id = get_option( 'page_on_front' );
            if ( ! $homepage_id ) {
                $homepage_id = 14; // Fallback
            }

            if ( function_exists( 'quanto_enqueue_elementor_post_assets' ) ) {
                quanto_enqueue_elementor_post_assets( $homepage_id );
            } elseif ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
                $css_file = new \Elementor\Core\Files\CSS\Post( $homepage_id );
                $css_file->enqueue();
            }

            $meta = get_post_meta( $homepage_id, '_elementor_data', true );
            if ( ! $meta ) {
                return;
            }

            $data = json_decode( $meta, true );
            if ( ! is_array( $data ) ) {
                return;
            }

            // Helper function to find element recursively
            $find_element = null;
            $find_element = function( $elements, $id ) use ( &$find_element ) {
                foreach ( $elements as $element ) {
                    if ( isset( $element['id'] ) && $element['id'] === $id ) {
                        return $element;
                    }
                    if ( isset( $element['elements'] ) && ! empty( $element['elements'] ) ) {
                        $found = $find_element( $element['elements'], $id );
                        if ( $found ) {
                            return $found;
                        }
                    }
                }
                return null;
            };

            $element_data = $find_element( $data, '2f45980' );
            if ( $element_data ) {
                $element_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );
                if ( $element_instance ) {
                    quanto_print_homepage_tail_inline_styles();
                    echo '<div data-elementor-type="wp-page" data-elementor-id="' . esc_attr( $homepage_id ) . '" class="elementor quanto-homepage-tail-sections elementor-' . esc_attr( $homepage_id ) . '">';
                    $element_instance->print_element();
                    echo '</div>';
                }
            }
        }
    }



