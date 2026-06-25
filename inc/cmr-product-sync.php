<?php
/**
 * CMR WooCommerce Product Sync API
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'cmr/v1', '/sync-products', array(
        'methods'             => 'GET, POST',
        'callback'            => 'cmr_sync_products_callback',
        'permission_callback' => '__return_true', // Open for ad-hoc syncing
    ) );
} );

function cmr_sync_products_callback( WP_REST_Request $request ) {
    cmr_require_media_functions(); // ensure media functions are loaded

    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 10; 

    $target_url = "https://cmrindia.com/wp-json/wc/store/products?page={$page}&per_page={$per_page}";
    $response = wp_remote_get( $target_url, array( 'timeout' => 300 ) );

    if ( is_wp_error( $response ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => $response->get_error_message() ), 500 );
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code == 400 ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'No more products found. Sync complete.' ), 200 );
    }

    $body = wp_remote_retrieve_body( $response );
    $products = json_decode( $body );

    if ( empty( $products ) || ! is_array( $products ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => 'No products found or invalid response.' ), 400 );
    }

    $created = 0;
    $skipped = 0;
    $log = array();

    foreach ( $products as $product ) {
        if ( empty( $product->slug ) || empty( $product->name ) ) {
            continue;
        }

        // Check if product exists locally
        global $wpdb;
        $post_id_exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'product'", $product->slug ) );
        
        if ( $post_id_exists ) {
            $skipped++;
            $log[] = "Skipped existing product: {$product->slug}";
            continue;
        }

        // 1. Process Categories (product_cat)
        $category_ids = array();
        if ( ! empty( $product->categories ) && is_array( $product->categories ) ) {
            foreach ( $product->categories as $term ) {
                $local_term = get_term_by( 'slug', $term->slug, 'product_cat' );
                if ( $local_term ) {
                    $category_ids[] = $local_term->term_id;
                } else {
                    $new_term = wp_insert_term( $term->name, 'product_cat', array( 'slug' => $term->slug ) );
                    if ( ! is_wp_error( $new_term ) ) {
                        $category_ids[] = $new_term['term_id'];
                    }
                }
            }
        }

        // 2. Create Product
        $post_data = array(
            'post_name'    => $product->slug,
            'post_title'   => wp_kses_post( $product->name ),
            'post_content' => wp_kses_post( $product->description ),
            'post_excerpt' => wp_kses_post( $product->short_description ),
            'post_status'  => 'publish',
            'post_type'    => 'product',
        );

        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            $log[] = "Failed to create product: {$product->slug} - " . $post_id->get_error_message();
            continue;
        }

        // Set terms
        if ( !empty( $category_ids ) ) {
            wp_set_object_terms( $post_id, $category_ids, 'product_cat' );
        }

        // Set basic WooCommerce metas
        if ( isset($product->prices) ) {
            $price = isset($product->prices->price) ? $product->prices->price : '0';
            $regular_price = isset($product->prices->regular_price) ? $product->prices->regular_price : '0';
            // WooCommerce prices in store API are typically in minor units
            $minor_unit = isset($product->prices->currency_minor_unit) ? $product->prices->currency_minor_unit : 0;
            if ( $minor_unit > 0 && is_numeric($price) ) {
                $price = $price / pow(10, $minor_unit);
            }
            if ( $minor_unit > 0 && is_numeric($regular_price) ) {
                $regular_price = $regular_price / pow(10, $minor_unit);
            }
            update_post_meta( $post_id, '_price', $price );
            update_post_meta( $post_id, '_regular_price', $regular_price );
        }
        
        update_post_meta( $post_id, '_sku', isset($product->sku) ? $product->sku : '' );
        update_post_meta( $post_id, '_visibility', 'visible' );

        // 3. Process Featured Image
        if ( ! empty( $product->images ) && is_array( $product->images ) && isset( $product->images[0]->src ) ) {
            $image_url = $product->images[0]->src;
            $image_id = media_sideload_image( $image_url, $post_id, null, 'id' );
            if ( ! is_wp_error( $image_id ) ) {
                set_post_thumbnail( $post_id, $image_id );
            } else {
                $log[] = "Failed to download image for product {$post_id}: " . $image_id->get_error_message();
            }
        }

        $created++;
        $log[] = "Created new product: {$product->slug} (ID: {$post_id})";
    }

    return new WP_REST_Response( array(
        'success'   => true,
        'message'   => "Batch {$page} completed.",
        'next_page' => $page + 1,
        'stats'     => array(
            'fetched' => count( $products ),
            'created' => $created,
            'skipped' => $skipped,
        ),
        'log'       => $log,
    ), 200 );
}
