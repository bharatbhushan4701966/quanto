<?php
add_action( 'wp_ajax_cmr_insights_ajax_search', 'cmr_insights_ajax_search_callback' );
add_action( 'wp_ajax_nopriv_cmr_insights_ajax_search', 'cmr_insights_ajax_search_callback' );

function cmr_insights_ajax_search_callback() {
    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
    $prefix = isset($_POST['prefix']) ? sanitize_text_field($_POST['prefix']) : 'cmr-mui-';
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';

    $query_args = array(
        'post_type'      => array('post', 'cmr_news'),
        'posts_per_page' => 21,
        'post_status'    => 'publish',
    );
    
    if (!empty($search_term)) {
        $query_args['s'] = $search_term;
    }
    
    if (!empty($category)) {
        $query_args['category_name'] = $category;
    }

    $all_query = new WP_Query($query_args);
    $posts = $all_query->posts;

    if (empty($posts)) {
        echo '<p style="grid-column: 1 / -1; font-size: 18px; color: #444; text-align: center; padding: 40px 20px 0;">No insights found matching "'.esc_html($search_term).'". Here are some related articles you might like:</p>';
        
        // Fallback query
        $fallback_args = array(
            'post_type'      => array('post', 'cmr_news'),
            'posts_per_page' => 3,
            'post_status'    => 'publish',
        );
        if (!empty($category)) {
            $fallback_args['category_name'] = $category;
        }
        $fallback_query = new WP_Query($fallback_args);
        $posts = $fallback_query->posts;
        
        if (empty($posts)) {
            echo '<p style="grid-column: 1 / -1; text-align: center; color: #888;">No articles available.</p>';
            wp_die();
        }
    }

    foreach ( $posts as $post_obj ) : 
        $thumbnail_url = get_the_post_thumbnail_url( $post_obj->ID, 'large' );
        if ( ! $thumbnail_url ) {
            $thumbnail_url = 'https://via.placeholder.com/600x400?text=Insight+Image';
        }
        
        $category_name = 'Uncategorized';
        $terms = get_the_terms( $post_obj->ID, 'category' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $category_name = $terms[0]->name;
        }
        
        $post_date = get_the_date('d M Y', $post_obj);
        
        $content = $post_obj->post_content;
        $word_count = str_word_count( strip_tags( $content ) );
        $read_time = ceil( $word_count / 200 );
        if ($read_time < 1) $read_time = 1;

        $excerpt = get_the_excerpt($post_obj);
        if ( empty( $excerpt ) ) {
            $excerpt = wp_trim_words( $content, 20 );
        }
    ?>
    <div class="<?php echo esc_attr($prefix); ?>card">
        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title($post_obj)); ?>" class="<?php echo esc_attr($prefix); ?>card-img">
        <div class="<?php echo esc_attr($prefix); ?>card-meta">
            <div class="<?php echo esc_attr($prefix); ?>card-cat-date">
                <span><?php echo esc_html($category_name); ?></span> | 
                <span><?php echo esc_html($post_date); ?></span>
            </div>
            <div class="<?php echo esc_attr($prefix); ?>card-read"><?php echo esc_html($read_time); ?> min read</div>
        </div>
        <h3 class="<?php echo esc_attr($prefix); ?>card-title"><?php echo esc_html(get_the_title($post_obj)); ?></h3>
        <p class="<?php echo esc_attr($prefix); ?>card-excerpt"><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p>
        <a href="<?php echo esc_url(get_permalink($post_obj->ID)); ?>" class="<?php echo esc_attr($prefix); ?>read-more">
            Read More 
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="7" y1="17" x2="17" y2="7"></line>
                <polyline points="7 7 17 7 17 17"></polyline>
            </svg>
        </a>
    </div>
    <?php endforeach;
    wp_die();
}
