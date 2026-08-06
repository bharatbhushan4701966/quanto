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

        $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $is_home_page = is_front_page() || is_home() || (is_page() && (get_the_title() == 'Home' || $current_path == '' || $current_path == 'home'));

        if ( $is_home_page ) {
            // Main Header for Home Page
            $header_post = get_page_by_path( 'main', OBJECT, 'quanto_header' );
            if ( ! $header_post ) {
                $global_header_id = quanto_opt('quanto_header_select_options');
                if ( ! empty( $global_header_id ) ) {
                    $header_post = get_post( $global_header_id );
                }
            }

            if ( ! empty( $header_post ) ) {
                echo '<header class="header">';
                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_post->ID, true );
                echo '</header>';
            } else {
                quanto_global_header_option();
            }
        } else {
            // Blog Header for ALL Rest Pages
            $header_post = get_page_by_path( 'blog-header', OBJECT, 'quanto_header' );
            
            if ( ! $header_post ) {
                $archive_header_id = quanto_opt('quanto_archive_header_select_options');
                if ( ! empty( $archive_header_id ) ) {
                    $header_post = get_post( $archive_header_id );
                }
            }

            // Fallback to main header if blog-header does not exist
            if ( ! $header_post ) {
                $header_post = get_page_by_path( 'main', OBJECT, 'quanto_header' );
                if ( ! $header_post ) {
                    $global_header_id = quanto_opt('quanto_header_select_options');
                    if ( ! empty( $global_header_id ) ) {
                        $header_post = get_post( $global_header_id );
                    }
                }
            }

            if ( ! empty( $header_post ) ) {
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

