<?php
/**
 * CMR Post Sync API
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'cmr/v1', '/sync-posts', array(
        'methods'             => 'GET, POST',
        'callback'            => 'cmr_sync_posts_callback',
        'permission_callback' => '__return_true', // Open for ad-hoc syncing
    ) );
    
    register_rest_route( 'cmr/v1', '/sync-missing-posts', array(
        'methods'             => 'GET, POST',
        'callback'            => 'cmr_sync_missing_posts_callback',
        'permission_callback' => '__return_true', 
    ) );
    
    register_rest_route( 'cmr/v1', '/migrate-pr', array(
        'methods'             => 'GET',
        'callback'            => 'cmr_migrate_press_releases_callback',
        'permission_callback' => '__return_true',
    ) );
    
    register_rest_route( 'cmr/v1', '/sync-sheet-categories', array(
        'methods'             => 'GET, POST',
        'callback'            => 'cmr_sync_sheet_categories_callback',
        'permission_callback' => '__return_true',
    ) );
} );

// Helper to require media functions for sideloading images
function cmr_require_media_functions() {
    if ( ! function_exists( 'media_sideload_image' ) ) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
}

function cmr_sync_posts_callback( WP_REST_Request $request ) {
    cmr_require_media_functions();

    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 50; // Increased batch size

    $target_url = "https://cmrindia.com/wp-json/wp/v2/posts?_embed=true&page={$page}&per_page={$per_page}";
    $response = wp_remote_get( $target_url, array( 'timeout' => 300 ) );

    if ( is_wp_error( $response ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => $response->get_error_message() ), 500 );
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code == 400 ) {
        return new WP_REST_Response( array( 'success' => true, 'message' => 'No more posts found. Sync complete.' ), 200 );
    }

    $body = wp_remote_retrieve_body( $response );
    $posts = json_decode( $body );

    if ( empty( $posts ) || ! is_array( $posts ) ) {
        return new WP_REST_Response( array( 'success' => false, 'message' => 'No posts found or invalid response.' ), 400 );
    }

    $created = 0;
    $skipped = 0;
    $log = array();

    foreach ( $posts as $post ) {
        if ( empty( $post->slug ) || empty( $post->title->rendered ) ) {
            continue;
        }

        // Check if post exists locally
        global $wpdb;
        $post_id_exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'post'", $post->slug ) );
        
        if ( $post_id_exists ) {
            $skipped++;
            $log[] = "Skipped existing post: {$post->slug}";
            continue;
        }

        // 1. Process Categories
        $category_ids = array();
        if ( isset( $post->_embedded->{'wp:term'} ) && is_array( $post->_embedded->{'wp:term'} ) ) {
            // The first array in 'wp:term' usually holds categories
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

        // 2. Process Author
        $author_id = 1; // Fallback to admin if author not found
        if ( isset( $post->_embedded->author[0] ) ) {
            $author_data = $post->_embedded->author[0];
            $local_user = get_user_by( 'login', $author_data->slug );
            if ( $local_user ) {
                $author_id = $local_user->ID;
            } else {
                // Automatically create author if missing
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

        // 3. Create Post
        $post_data = array(
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

        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            $log[] = "Failed to create post: {$post->slug} - " . $post_id->get_error_message();
            continue;
        }

        // 4. Process Featured Image
        if ( isset( $post->_embedded->{'wp:featuredmedia'}[0] ) ) {
            $media = $post->_embedded->{'wp:featuredmedia'}[0];
            if ( isset( $media->source_url ) ) {
                $image_url = $media->source_url;
                $image_id = media_sideload_image( $image_url, $post_id, null, 'id' );
                if ( ! is_wp_error( $image_id ) ) {
                    set_post_thumbnail( $post_id, $image_id );
                } else {
                    $log[] = "Failed to download image for post {$post_id}: " . $image_id->get_error_message();
                }
            }
        }

        $created++;
        $log[] = "Created new post: {$post->slug} (ID: {$post_id})";
    }

    return new WP_REST_Response( array(
        'success'   => true,
        'message'   => "Batch {$page} completed.",
        'next_page' => $page + 1,
        'stats'     => array(
            'fetched' => count( $posts ),
            'created' => $created,
            'skipped' => $skipped,
        ),
        'log'       => $log,
    ), 200 );
}

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
    
    // Only process up to 10 at a time to be absolutely safe against timeouts
    $batch_to_process = array_slice($missing_slugs, 0, 10);
    
    foreach ( $batch_to_process as $slug ) {
        $post_res = wp_remote_get( "https://cmrindia.com/wp-json/wp/v2/posts?_embed=true&slug={$slug}", array('timeout' => 60) );
        if ( is_wp_error( $post_res ) ) continue;
        
        $post_body = wp_remote_retrieve_body( $post_res );
        $post_data = json_decode( $post_body );
        
        if ( empty( $post_data ) || ! is_array( $post_data ) ) continue;
        
        $post = $post_data[0]; 
        
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
                        if ( ! is_wp_error( $new_term ) ) $category_ids[] = $new_term['term_id'];
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
                if ( ! is_wp_error( $user_id ) ) $author_id = $user_id;
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
                    if ( ! is_wp_error( $image_id ) ) set_post_thumbnail( $post_id, $image_id );
                }
            }
            $created++;
            $log[] = "Successfully synced missing post: {$post->slug}";
        } else {
            $log[] = "Failed to sync: {$post->slug} - " . $post_id->get_error_message();
        }
    }
    
    $remaining = count($missing_slugs) - count($batch_to_process);
    $msg = "Synced {$created} missing posts. ";
    if ($remaining > 0) {
        $msg .= "There are still {$remaining} missing posts. Please refresh this page to sync the next batch.";
    } else {
        $msg .= "All missing posts have now been synced!";
    }
    
    return new WP_REST_Response( array(
        'success' => true,
        'message' => $msg,
        'missing_found' => count($missing_slugs),
        'created_this_batch' => $created,
        'remaining_missing' => max(0, $remaining),
        'log' => $log
    ), 200 );
}

/**
 * Sync Google Sheet categories to WordPress posts
 */
function cmr_sync_sheet_categories_callback( WP_REST_Request $request ) {
    global $wpdb;

    @set_time_limit( 300 );
    @ini_set( 'memory_limit', '512M' );

    $items = $request->get_param( 'items' );

    // Optional: Fetch directly from Google Sheets if fetch_gid is provided
    $fetch_gid = $request->get_param( 'fetch_gid' );
    if ( ! empty( $fetch_gid ) || ( empty( $items ) && $request->get_param( 'fetch_all' ) ) ) {
        $sheet_id = '1YMoUBqDGlsZPmDDHv4gPStisElH8yIf1I1KvNxIExPk';
        $gid = ! empty( $fetch_gid ) ? $fetch_gid : '0';
        $csv_url = "https://docs.google.com/spreadsheets/d/{$sheet_id}/export?format=csv&gid={$gid}";
        $resp = wp_remote_get( $csv_url, array( 'timeout' => 60 ) );
        if ( ! is_wp_error( $resp ) && wp_remote_retrieve_response_code( $resp ) === 200 ) {
            $csv_body = wp_remote_retrieve_body( $resp );
            $lines = explode( "\n", $csv_body );
            if ( ! empty( $lines ) ) {
                $header = str_getcsv( array_shift( $lines ) );
                $col_url = -1;
                $col_target = -1;
                $col_current = -1;
                $col_title = -1;
                foreach ( $header as $idx => $h ) {
                    $hl = strtolower( trim( $h ) );
                    if ( strpos( $hl, 'page url' ) !== false || ( strpos( $hl, 'page' ) !== false && strpos( $hl, 'url' ) !== false ) ) {
                        $col_url = $idx;
                    } elseif ( strpos( $hl, 'add this' ) !== false ) {
                        $col_target = $idx;
                    } elseif ( strpos( $hl, 'current' ) !== false ) {
                        $col_current = $idx;
                    } elseif ( in_array( $hl, array( 'heading', 'title' ) ) && $col_title === -1 ) {
                        $col_title = $idx;
                    }
                }
                $items = array();
                foreach ( $lines as $line ) {
                    $row = str_getcsv( $line );
                    if ( empty( $row ) || count( $row ) < 2 ) continue;
                    $target = ( $col_target !== -1 && isset( $row[ $col_target ] ) ) ? trim( $row[ $col_target ] ) : '';
                    if ( empty( $target ) ) continue;
                    $p_url = ( $col_url !== -1 && isset( $row[ $col_url ] ) ) ? trim( $row[ $col_url ] ) : '';
                    $t_title = ( $col_title !== -1 && isset( $row[ $col_title ] ) ) ? trim( $row[ $col_title ] ) : '';
                    if ( empty( $p_url ) && strpos( $t_title, 'http' ) === 0 ) {
                        $p_url = $t_title;
                    }
                    $slug = '';
                    if ( ! empty( $p_url ) ) {
                        $parsed_path = trim( parse_url( $p_url, PHP_URL_PATH ), '/' );
                        $segments = explode( '/', $parsed_path );
                        $slug = end( $segments );
                    }
                    $current = ( $col_current !== -1 && isset( $row[ $col_current ] ) ) ? trim( $row[ $col_current ] ) : '';
                    $items[] = array(
                        'slug'        => $slug,
                        'title'       => $t_title,
                        'target_cat'  => $target,
                        'current_cat' => $current,
                    );
                }
            }
        }
    }

    if ( empty( $items ) || ! is_array( $items ) ) {
        return new WP_REST_Response( array(
            'success' => false,
            'message' => 'No items provided in request. Pass items array via POST or fetch_gid via GET.',
        ), 400 );
    }

    $updated = 0;
    $not_found = 0;
    $log = array();
    $cat_cache = array();
    $news_cat_cache = array();

    // Map common current category names to standardized term names
    $current_cat_map = array(
        'viewpoint'          => 'Viewpoints',
        'viewpoints'         => 'Viewpoints',
        'press release'      => 'Press Releases',
        'press releases'     => 'Press Releases',
        'smb connect'        => 'SMB Connect',
        'enterprise connect' => 'Enterprise Connect',
        'channel connect'    => 'Channel Connect',
        'market updates'     => 'Market Updates',
    );

    foreach ( $items as $item ) {
        $target_cat = ! empty( $item['target_cat'] ) ? trim( $item['target_cat'] ) : '';
        if ( empty( $target_cat ) ) {
            continue;
        }

        $slug        = ! empty( $item['slug'] ) ? trim( sanitize_title( $item['slug'] ) ) : '';
        $raw_slug    = ! empty( $item['slug'] ) ? trim( $item['slug'] ) : '';
        $title       = ! empty( $item['title'] ) ? trim( $item['title'] ) : '';
        $current_cat = ! empty( $item['current_cat'] ) ? trim( $item['current_cat'] ) : '';

        // 1. Resolve Target Category in 'category'
        if ( ! isset( $cat_cache[ $target_cat ] ) ) {
            $t = get_term_by( 'name', $target_cat, 'category' );
            if ( ! $t ) {
                $t = get_term_by( 'slug', sanitize_title( $target_cat ), 'category' );
            }
            if ( ! $t ) {
                $ins = wp_insert_term( $target_cat, 'category', array( 'slug' => sanitize_title( $target_cat ) ) );
                if ( ! is_wp_error( $ins ) && isset( $ins['term_id'] ) ) {
                    $cat_cache[ $target_cat ] = (int) $ins['term_id'];
                } else {
                    $cat_cache[ $target_cat ] = 0;
                }
            } else {
                $cat_cache[ $target_cat ] = (int) $t->term_id;
            }
        }
        $target_cat_id = $cat_cache[ $target_cat ];

        // 2. Resolve Target Category in 'cmr_news_category'
        if ( ! isset( $news_cat_cache[ $target_cat ] ) ) {
            $nt = get_term_by( 'name', $target_cat, 'cmr_news_category' );
            if ( ! $nt ) {
                $nt = get_term_by( 'slug', sanitize_title( $target_cat ), 'cmr_news_category' );
            }
            if ( ! $nt ) {
                $ins_nt = wp_insert_term( $target_cat, 'cmr_news_category', array( 'slug' => sanitize_title( $target_cat ) ) );
                if ( ! is_wp_error( $ins_nt ) && isset( $ins_nt['term_id'] ) ) {
                    $news_cat_cache[ $target_cat ] = (int) $ins_nt['term_id'];
                } else {
                    $news_cat_cache[ $target_cat ] = 0;
                }
            } else {
                $news_cat_cache[ $target_cat ] = (int) $nt->term_id;
            }
        }
        $target_news_cat_id = $news_cat_cache[ $target_cat ];

        // 3. Resolve Current Category in 'category' if specified
        $current_cat_id = 0;
        if ( ! empty( $current_cat ) ) {
            $cur_clean = strtolower( $current_cat );
            $cur_name = isset( $current_cat_map[ $cur_clean ] ) ? $current_cat_map[ $cur_clean ] : $current_cat;
            if ( ! isset( $cat_cache[ $cur_name ] ) ) {
                $ct = get_term_by( 'name', $cur_name, 'category' );
                if ( ! $ct ) {
                    $ct = get_term_by( 'slug', sanitize_title( $cur_name ), 'category' );
                }
                $cat_cache[ $cur_name ] = $ct ? (int) $ct->term_id : 0;
            }
            $current_cat_id = $cat_cache[ $cur_name ];
        }

        // 4. Find matching post(s)
        $found_post_ids = array();

        // 4a. Match by slug
        if ( ! empty( $slug ) ) {
            $ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_status = 'publish'",
                $slug
            ) );
            if ( ! empty( $ids ) ) {
                $found_post_ids = array_merge( $found_post_ids, $ids );
            }
        }

        // 4b. Match by raw slug if different
        if ( empty( $found_post_ids ) && ! empty( $raw_slug ) && $raw_slug !== $slug ) {
            $ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_status = 'publish'",
                $raw_slug
            ) );
            if ( ! empty( $ids ) ) {
                $found_post_ids = array_merge( $found_post_ids, $ids );
            }
        }

        // 4c. Match by exact title
        if ( empty( $found_post_ids ) && ! empty( $title ) ) {
            $ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_status = 'publish'",
                $title
            ) );
            if ( ! empty( $ids ) ) {
                $found_post_ids = array_merge( $found_post_ids, $ids );
            }
        }

        // 4d. Match by sanitized title as post_name
        if ( empty( $found_post_ids ) && ! empty( $title ) ) {
            $title_slug = sanitize_title( $title );
            if ( ! empty( $title_slug ) ) {
                $ids = $wpdb->get_col( $wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_status = 'publish'",
                    $title_slug
                ) );
                if ( ! empty( $ids ) ) {
                    $found_post_ids = array_merge( $found_post_ids, $ids );
                }
            }
        }

        // 4e. Match by LIKE search using first significant words of title
        if ( empty( $found_post_ids ) && ! empty( $title ) ) {
            $clean = preg_replace( '/[^a-zA-Z0-9\s]/', ' ', $title );
            $words = array_values( array_filter( explode( ' ', $clean ), function( $w ) {
                return strlen( $w ) >= 3;
            } ) );
            if ( count( $words ) >= 3 ) {
                $search_pattern = '%' . implode( '%', array_slice( $words, 0, 4 ) ) . '%';
                $ids = $wpdb->get_col( $wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s AND post_status = 'publish' LIMIT 2",
                    $search_pattern
                ) );
                if ( ! empty( $ids ) ) {
                    $found_post_ids = array_merge( $found_post_ids, $ids );
                }
            }
        }

        $found_post_ids = array_unique( array_map( 'intval', $found_post_ids ) );

        if ( empty( $found_post_ids ) ) {
            $not_found++;
            $log[] = "Not found: '{$slug}' / '{$title}'";
            continue;
        }

        // 5. Assign categories
        foreach ( $found_post_ids as $pid ) {
            $ptype = get_post_type( $pid );

            // Categories to append
            $cats_to_add = array();
            if ( $target_cat_id > 0 ) {
                $cats_to_add[] = $target_cat_id;
            }
            if ( $current_cat_id > 0 ) {
                $cats_to_add[] = $current_cat_id;
            }
            if ( ! empty( $cats_to_add ) ) {
                wp_set_post_categories( $pid, $cats_to_add, true ); // true = append
            }

            // If cmr_news, also append in cmr_news_category
            if ( $ptype === 'cmr_news' && $target_news_cat_id > 0 ) {
                wp_set_object_terms( $pid, array( $target_news_cat_id ), 'cmr_news_category', true ); // append
            }

            $updated++;
            $log[] = "Updated ID {$pid} ({$ptype}): added '{$target_cat}' (slug: {$slug})";
        }
    }

    return new WP_REST_Response( array(
        'success'   => true,
        'updated'   => $updated,
        'not_found' => $not_found,
        'total'     => count( $items ),
        'log'       => array_slice( $log, 0, 100 ),
    ), 200 );
}

