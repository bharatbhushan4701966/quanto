<?php
/**
 * CMR Author Sync API
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'cmr/v1', '/sync-authors', array(
        'methods'             => 'GET, POST',
        'callback'            => 'cmr_sync_authors_callback',
        'permission_callback' => '__return_true', // Open for ad-hoc syncing. Can be restricted to current_user_can('manage_options') later.
    ) );
} );

function cmr_sync_authors_callback( WP_REST_Request $request ) {
    // We fetch up to 100 users. If the live site has more, we can implement pagination.
    $target_url = 'https://cmrindia.com/wp-json/wp/v2/users?per_page=100';
    $response = wp_remote_get( $target_url, array( 'timeout' => 30 ) );

    if ( is_wp_error( $response ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => $response->get_error_message() ), 500 );
    }

    $body = wp_remote_retrieve_body( $response );
    $authors = json_decode( $body );

    if ( empty( $authors ) || ! is_array( $authors ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => 'No authors found or invalid response.' ), 400 );
    }

    $created = 0;
    $skipped = 0;
    $log = array();

    foreach ( $authors as $author ) {
        if ( empty( $author->slug ) || empty( $author->name ) ) {
            continue;
        }

        $user_login = sanitize_user( $author->slug, true );
        $existing_user = get_user_by( 'login', $user_login );

        if ( $existing_user ) {
            // User exists. Skip it to protect existing data and avoid deletions/overwrites.
            $skipped++;
            $log[] = "Skipped existing user: {$user_login}";
            continue;
        }

        // Create new user securely
        $userdata = array(
            'user_login'   => $user_login,
            'user_pass'    => wp_generate_password( 24, true, true ), // Random strong password
            'display_name' => sanitize_text_field( $author->name ),
            'first_name'   => sanitize_text_field( $author->name ), // Fallback for some themes
            'description'  => sanitize_textarea_field( $author->description ),
            'role'         => 'author',
        );

        $user_id = wp_insert_user( $userdata );

        if ( ! is_wp_error( $user_id ) ) {
            $created++;
            $log[] = "Created new user: {$user_login} (ID: {$user_id})";
        } else {
            $log[] = "Failed to create user: {$user_login} - " . $user_id->get_error_message();
        }
    }

    return new WP_REST_Response( array(
        'success' => true,
        'message' => 'Sync completed successfully.',
        'stats'   => array(
            'total_fetched' => count( $authors ),
            'created'       => $created,
            'skipped'       => $skipped,
        ),
        'log'     => $log,
    ), 200 );
}
