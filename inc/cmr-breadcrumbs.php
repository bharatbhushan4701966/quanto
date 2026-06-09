<?php
/**
 * Shortcode for dynamic breadcrumbs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_breadcrumbs_shortcode' ) ) {
    function cmr_breadcrumbs_shortcode() {
        // Return nothing if on the home page or front page
        if ( is_front_page() || is_home() ) {
            return '';
        }

        $separator = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 12px; color: #888;"><polyline points="9 18 15 12 9 6"></polyline></svg>';
        $home = 'Home';

        $breadcrumbs = '<style>
            .cmr-breadcrumbs {
                font-family: \'Instrument Sans\', sans-serif !important;
                font-size: 15px;
                color: #111;
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                margin-bottom: 30px;
                line-height: 1.5;
            }
            .cmr-breadcrumbs a {
                color: #555;
                text-decoration: none;
                transition: color 0.2s ease;
            }
            .cmr-breadcrumbs a:hover {
                color: #111;
            }
            .cmr-breadcrumbs span.current-page {
                color: #111;
                font-weight: 500;
            }
        </style>';

        $breadcrumbs .= '<div class="cmr-breadcrumbs">';
        $breadcrumbs .= '<a href="' . home_url() . '">' . $home . '</a>';

        global $post;

        if ( is_category() || is_single() ) {
            $breadcrumbs .= $separator;
            
            if ( is_single() ) {
                // Try to get categories
                $categories = get_the_category();
                
                if ( ! empty( $categories ) ) {
                    $cat = $categories[0];
                    // Get all parent categories of the first category
                    $cat_parents = get_ancestors( $cat->term_id, 'category' );
                    $cat_parents = array_reverse( $cat_parents );
                    
                    foreach ( $cat_parents as $parent_id ) {
                        $parent = get_term( $parent_id, 'category' );
                        if ( $parent && ! is_wp_error( $parent ) ) {
                            $breadcrumbs .= '<a href="' . get_category_link( $parent_id ) . '">' . $parent->name . '</a>';
                            $breadcrumbs .= $separator;
                        }
                    }
                    
                    // Add the category itself
                    $breadcrumbs .= '<a href="' . get_category_link( $cat->term_id ) . '">' . $cat->name . '</a>';
                    $breadcrumbs .= $separator;
                } else {
                    // Check if it's a CPT without categories
                    $post_type = get_post_type();
                    if ( $post_type !== 'post' && $post_type !== 'page' ) {
                        $post_type_obj = get_post_type_object( $post_type );
                        if ( $post_type_obj ) {
                            $archive_link = get_post_type_archive_link( $post_type );
                            if ( $archive_link ) {
                                $breadcrumbs .= '<a href="' . $archive_link . '">' . $post_type_obj->labels->name . '</a>';
                                $breadcrumbs .= $separator;
                            }
                        }
                    }
                }
                $breadcrumbs .= '<span class="current-page">' . get_the_title() . '</span>';
            } else {
                // It's a category archive
                $breadcrumbs .= '<span class="current-page">' . single_cat_title( '', false ) . '</span>';
            }
        } elseif ( is_page() ) {
            if ( $post->post_parent ) {
                $parent_id  = $post->post_parent;
                $parent_links = array();
                while ( $parent_id ) {
                    $page = get_page( $parent_id );
                    $parent_links[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
                    $parent_id  = $page->post_parent;
                }
                $parent_links = array_reverse( $parent_links );
                foreach ( $parent_links as $link ) {
                    $breadcrumbs .= $separator . $link;
                }
            }
            $breadcrumbs .= $separator;
            $breadcrumbs .= '<span class="current-page">' . get_the_title() . '</span>';
        } elseif ( is_search() ) {
            $breadcrumbs .= $separator;
            $breadcrumbs .= '<span class="current-page">Search Results for "' . get_search_query() . '"</span>';
        } else {
            // Fallback for other archives, 404, etc.
            if ( ! is_home() && ! is_front_page() ) {
                $breadcrumbs .= $separator;
                $title = get_the_title();
                if ( is_archive() ) {
                    $title = get_the_archive_title();
                } elseif ( is_404() ) {
                    $title = 'Page Not Found';
                }
                $breadcrumbs .= '<span class="current-page">' . $title . '</span>';
            }
        }

        $breadcrumbs .= '</div>';
        
        return $breadcrumbs;
    }
}
add_shortcode( 'cmr_breadcrumbs', 'cmr_breadcrumbs_shortcode' );
