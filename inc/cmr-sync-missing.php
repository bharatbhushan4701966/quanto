
function cmr_sync_missing_posts_callback( WP_REST_Request $request ) {
    cmr_require_media_functions();
    
    // 1. Get total pages from live site
    $head_response = wp_remote_head( 'https://cmrindia.com/wp-json/wp/v2/posts' );
    if ( is_wp_error( $head_response ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => 'Failed to reach live site.' ), 500 );
    }
    
    $total_pages = wp_remote_retrieve_header( $head_response, 'X-WP-TotalPages' );
    if ( ! $total_pages ) $total_pages = 20; // Fallback
    
    // 2. Fetch all live slugs
    $live_slugs = array();
    for ( $p = 1; $p <= $total_pages; $p++ ) {
        $res = wp_remote_get( "https://cmrindia.com/wp-json/wp/v2/posts?_fields=slug&per_page=100&page=$p", array('timeout' => 30) );
        if ( ! is_wp_error( $res ) && wp_remote_retrieve_response_code($res) == 200 ) {
            $body = wp_remote_retrieve_body( $res );
            $posts = json_decode( $body );
            if ( ! empty( $posts ) ) {
                foreach ( $posts as $post ) {
                    $live_slugs[] = $post->slug;
                }
            }
        }
    }
    
    // 3. Get all local slugs
    global $wpdb;
    $local_slugs = $wpdb->get_col( "SELECT post_name FROM $wpdb->posts WHERE post_type = 'post' AND post_status IN ('publish', 'draft', 'pending')" );
    
    // 4. Find missing slugs
    $missing_slugs = array_diff( $live_slugs, $local_slugs );
    
    if ( empty( $missing_slugs ) ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'No missing posts found. Everything is synced!' ), 200 );
    }
    
    // 5. Sync the missing posts
    $created = 0;
    $log = array();
    
    foreach ( $missing_slugs as $slug ) {
        // Fetch full post data for this slug
        $post_res = wp_remote_get( "https://cmrindia.com/wp-json/wp/v2/posts?_embed=true&slug={$slug}", array('timeout' => 60) );
        if ( is_wp_error( $post_res ) ) continue;
        
        $post_body = wp_remote_retrieve_body( $post_res );
        $post_data = json_decode( $post_body );
        
        if ( empty( $post_data ) || ! is_array( $post_data ) ) continue;
        
        $post = $post_data[0]; // The post object
        
        // --- Process Category and Author (Same as standard sync) ---
        $category_ids = array();
        if ( isset( $post->_embedded->{'wp:term'} ) && is_array( $post->_embedded->{'wp:term'} ) ) {
            $terms = $post->_embedded->{'wp:term'}[0]; 
            foreach ( $terms as $term ) {
                if ( $term->taxonomy === 'category' ) {
                    $local_term = get_term_by( 'slug', $term->slug, 'category' );
                    if ( $local_term ) {
                        $category_ids[] = $local_term->term_id;
                    } else {
                        $new_term = wp_insert_term( $term->name, 'category', array( 'slug' => $term->slug ) );
                        if ( ! is_wp_error( $new_term ) ) {
                            $category_ids[] = $new_term['term_id'];
                        }
                    }
                }
            }
        }
        
        $author_id = 1;
        if ( isset( $post->_embedded->author[0] ) ) {
            $author_data = $post->_embedded->author[0];
            $local_user = get_user_by( 'login', $author_data->slug );
            if ( $local_user ) {
                $author_id = $local_user->ID;
            } else {
                $user_id = wp_insert_user( array(
                    'user_login'   => sanitize_user( $author_data->slug, true ),
                    'user_pass'    => wp_generate_password( 24, true, true ),
                    'display_name' => sanitize_text_field( $author_data->name ),
                    'role'         => 'author',
                ) );
                if ( ! is_wp_error( $user_id ) ) {
                    $author_id = $user_id;
                }
            }
        }
        
        $new_post_data = array(
            'post_name'    => $post->slug,
            'post_title'   => wp_kses_post( $post->title->rendered ),
            'post_content' => wp_kses_post( $post->content->rendered ),
            'post_excerpt' => wp_kses_post( $post->excerpt->rendered ),
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_author'  => $author_id,
            'post_category'=> !empty($category_ids) ? $category_ids : array(),
            'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( $post->date ) ),
        );
        
        $post_id = wp_insert_post( $new_post_data );
        
        if ( ! is_wp_error( $post_id ) ) {
            if ( isset( $post->_embedded->{'wp:featuredmedia'}[0] ) ) {
                $media = $post->_embedded->{'wp:featuredmedia'}[0];
                if ( isset( $media->source_url ) ) {
                    $image_id = media_sideload_image( $media->source_url, $post_id, null, 'id' );
                    if ( ! is_wp_error( $image_id ) ) {
                        set_post_thumbnail( $post_id, $image_id );
                    }
                }
            }
            $created++;
            $log[] = "Successfully synced missing post: {$post->slug}";
        } else {
            $log[] = "Failed to sync: {$post->slug} - " . $post_id->get_error_message();
        }
    }
    
    return new WP_REST_Response( array(
        'success' => true,
        'message' => "Found and synced missing posts.",
        'missing_slugs' => array_values($missing_slugs),
        'created' => $created,
        'log' => $log
    ), 200 );
}
