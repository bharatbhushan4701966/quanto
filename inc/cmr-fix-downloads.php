<?php
if ( isset( $_GET['fix_downloads'] ) && current_user_can( 'manage_options' ) ) {
    add_action( 'init', function() {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_downloadable_files'" );
        $count = 0;
        foreach ( $results as $row ) {
            $files = maybe_unserialize( $row->meta_value );
            $updated = false;
            if ( is_array( $files ) ) {
                foreach ( $files as $id => $file ) {
                    if ( strpos( $file['file'], 'https://cmrindia.com' ) !== false ) {
                        $files[$id]['file'] = str_replace( 'https://cmrindia.com', 'https://qai8358l95-staging.onrocket.site', $file['file'] );
                        $updated = true;
                    }
                }
                if ( $updated ) {
                    update_post_meta( $row->post_id, '_downloadable_files', $files );
                    $count++;
                }
            }
        }
        wp_die( "Updated $count products successfully! The downloadable links have been replaced from cmrindia.com to qai8358l95-staging.onrocket.site." );
    });
}
?>
