<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

    // Block direct access
    if ( ! defined( 'ABSPATH' ) ) {
        exit();
    }

    if ( class_exists( 'ReduxFramework' ) && defined( 'ELEMENTOR_VERSION' ) ) {

        // ✅ Handle archive, blog, Single Post and search pages first
        if ( is_archive() || is_home() || is_search() || is_singular( ['post', 'cmr_news', 'cmr_media_release', 'cmr_quarterly', 'product', 'job'] ) ) {

            $header_post = get_page_by_path( 'blog-header', OBJECT, 'quanto_header' );
            
            if ( ! $header_post ) {
                $archive_header_id = quanto_opt('quanto_archive_header_select_options');
                if ( ! empty( $archive_header_id ) ) {
                    $header_post = get_post( $archive_header_id );
                }
            }

            if ( ! empty( $header_post ) ) {
                echo '<header class="header">';
                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_post->ID, true );
                echo '</header>';
            } else {
                $header_post = get_page_by_path( 'main', OBJECT, 'quanto_header' );
                if ( ! $header_post ) {
                    $global_header_id = quanto_opt('quanto_header_select_options');
                    if ( ! empty( $global_header_id ) ) {
                        $header_post = get_post( $global_header_id );
                    }
                }
                
                if ( $header_post ) {
                    echo '<header class="header">';
                    echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_post->ID, true );
                    echo '</header>';
                } else {
                    quanto_global_header_option(); // fallback
                }
            }

        } 
        // ✅ Handle pages
        elseif ( is_page() || is_page_template('template-builder.php') ) {

            $quanto_post_id = get_the_ID();
            $settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
            $settings_model = $settings_manager->get_model( $quanto_post_id );
            $header_style = $settings_model->get_settings( 'quanto_header_style' );
            $builder_option = $settings_model->get_settings( 'quanto_header_builder_option' );

            if ( $header_style == 'header_builder' && ! empty( $builder_option ) ) {
                $header_post = get_post( $builder_option );
                echo '<header class="header">';
                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_post->ID, true );
                echo '</header>';
            } else {
                $header_post = get_page_by_path( 'main', OBJECT, 'quanto_header' );
                if ( ! $header_post ) {
                    $header_post = get_post( quanto_opt( 'quanto_header_select_options' ) );
                }
                
                if ( $header_post ) {
                    echo '<header>';
                    echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_post->ID, true );
                    echo '</header>';
                } else {
                    quanto_global_header_option();
                }
            }

        }  
        // ✅ Fallback for all others
        else {
            $header_post = get_page_by_path( 'main', OBJECT, 'quanto_header' );
            if ( ! $header_post ) {
                $header_post = get_post( quanto_opt( 'quanto_header_select_options' ) );
            }

            if ( $header_post ) {
                echo '<header class="header">';
                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_post->ID, true );
                echo '</header>';
            } else {
                quanto_global_header_option();
            }
        }

    } else {
        quanto_global_header_option(); // Elementor or Redux not active
    }
