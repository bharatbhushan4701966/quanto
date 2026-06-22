<?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    $base_url = isset( <?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
POST['base_url'] ) ? sanitize_text_field( <?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
POST['base_url'] ) : '/';
    $pagination = '';
    if ( $paged >= 3 || $paged >= $total_pages ) {
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $total_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more,
        'pagination' => $pagination
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    $base_url = isset( <?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
POST['base_url'] ) ? sanitize_text_field( <?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
POST['base_url'] ) : '/';
    $pagination = '';
    if ( $paged >= 3 || $paged >= $total_pages ) {
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $total_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more,
        'pagination' => $pagination
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    $base_url = isset( <?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
POST['base_url'] ) ? sanitize_text_field( <?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
POST['base_url'] ) : '/';
    $pagination = '';
    if ( $paged >= 3 || $paged >= $total_pages ) {
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $total_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more,
        'pagination' => $pagination
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    $base_url = isset( <?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
POST['base_url'] ) ? sanitize_text_field( <?php
/**
 * AJAX Handlers for CMR Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_intel', 'cmr_load_more_intel_ajax' );

function cmr_load_more_intel_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $base_url = isset( $_POST['base_url'] ) ? sanitize_text_field( $_POST['base_url'] ) : '/';
    
    $query_args = array(
        'post_type'      => array( 'post', 'cmr_news' ),
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => 'infotech',
            ),
        ),
    );

    $insights_query = new WP_Query( $query_args );

    ob_start();
    if ( $insights_query->have_posts() ) {
        while ( $insights_query->have_posts() ) {
            $insights_query->the_post();
            $post_title = get_the_title();
            $post_link = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            if ( ! $thumbnail_url ) {
                $thumbnail_url = 'https://via.placeholder.com/600x400?text=No+Image';
            }
            
            $content = get_post_field( 'post_content', get_the_ID() );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            
            ?>
            <div class="intel-card">
                <div class="intel-card-img">
                    <a href="<?php echo esc_url( $post_link ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" />
                    </a>
                </div>
                <div class="intel-card-content">
                    <div class="intel-meta">
                        <span class="intel-category">Industry Intelligence</span>
                        <span class="intel-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="intel-title">
                        <a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
                    </h3>
                    <div class="intel-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt();
                        if ( empty( $excerpt ) ) {
                            $excerpt = wp_trim_words( $content, 12 );
                        } else {
                            $excerpt = wp_trim_words( $excerpt, 12 );
                        }
                        echo wp_kses_post( $excerpt ); 
                        ?>
                    </div>
                    <a href="<?php echo esc_url( $post_link ); ?>" class="intel-read-more">
                        More Details 
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $pagination = '';
    if ( $paged >= 3 || $paged >= $insights_query->max_num_pages ) {
        // Construct standard pagination links for the current page
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $insights_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'pagination' => $pagination
    ) );
}

add_action( 'wp_ajax_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_media_releases', 'cmr_load_more_media_releases_ajax' );

function cmr_load_more_media_releases_ajax() {
    $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    $year  = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    
    $offset_base = ( empty($year) && empty($search) ) ? 4 : 0;
    $offset = $offset_base + ( ($paged - 1) * 6 );
    
    $args = array(
        'post_type'      => 'cmr_news',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => $offset,
    );

    if ( ! empty( $year ) ) {
        $args['date_query'] = array(
            array(
                'year'  => $year,
            ),
        );
    }

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-mrg-card">
                <div class="cmr-mrg-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-mrg-card-meta">
                    <div class="cmr-mrg-card-label">Press Release <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-mrg-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-mrg-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-mrg-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-mrg-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}


add_action( 'wp_ajax_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_smb_connect', 'cmr_load_more_smb_connect_ajax' );

function cmr_load_more_smb_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_smb_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'smb-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-smbcgd-card">
                <div class="cmr-smbcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-smbcgd-card-meta">
                    <div class="cmr-smbcgd-card-label">SMB Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-smbcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-smbcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-smbcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-smbcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_enterprise_connect', 'cmr_load_more_enterprise_connect_ajax' );

function cmr_load_more_enterprise_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_enterprise_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'enterprise-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-enterprisecgd-card">
                <div class="cmr-enterprisecgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-enterprisecgd-card-meta">
                    <div class="cmr-enterprisecgd-card-label">Enterprise Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-enterprisecgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-enterprisecgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-enterprisecgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-enterprisecgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}



add_action( 'wp_ajax_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );
add_action( 'wp_ajax_nopriv_cmr_load_more_channel_connect', 'cmr_load_more_channel_connect_ajax' );

function cmr_load_more_channel_connect_ajax() {
    $paged  = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $year   = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // We only use the unique IDs if there is NO year/search filter
    if ( empty($year) && empty($search) ) {
        $unique_ids = cmr_get_unique_channel_post_ids();
        $offset_base = 4;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $sliced_ids = array_slice( $unique_ids, $offset, 6 );
        
        if ( empty($sliced_ids) ) {
            wp_send_json_success(array('html' => '', 'has_more' => false));
        }
        
        $args = array(
            'post_type'      => 'post',
            'post__in'       => $sliced_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 6,
        );
        $query = new WP_Query( $args );
        
        $total_pages = ceil( max( 0, count( $unique_ids ) - 4 ) / 6 );
        $has_more = $paged < $total_pages;
    } else {
        $offset_base = 0;
        $offset = $offset_base + ( ($paged - 1) * 6 );
        
        $args = array(
            'post_type'      => 'post',
            'category_name'  => 'channel-connect',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'offset'         => $offset
        );

        if ( !empty($year) ) {
            $args['year'] = $year;
        }

        if ( !empty($search) ) {
            $args['s'] = $search;
        }
        
        $query = new WP_Query( $args );
        $has_more = $query->max_num_pages > $paged;
    }

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $link = get_permalink();
            $excerpt = wp_trim_words( get_the_excerpt(), 18 );
            if ( empty($excerpt) ) {
                $excerpt = wp_trim_words( get_post_field('post_content', $post_id), 18 );
            }
            $bg_image = get_the_post_thumbnail_url( $post_id, 'medium_large' );
            if ( ! $bg_image ) {
                $bg_image = 'https://via.placeholder.com/600x400';
            }
            
            $content = get_post_field( 'post_content', $post_id );
            $word_count = str_word_count( strip_tags( $content ) );
            $read_time = ceil( $word_count / 200 );
            if ($read_time < 1) $read_time = 1;
            $date = get_the_date('d M Y');
            ?>
            <div class="cmr-channelcgd-card">
                <div class="cmr-channelcgd-card-img-wrap">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </a>
                </div>
                <div class="cmr-channelcgd-card-meta">
                    <div class="cmr-channelcgd-card-label">Channel Connect <span>|</span> <?php echo esc_html($date); ?></div>
                    <div class="cmr-channelcgd-card-time"><?php echo esc_html($read_time); ?> min read</div>
                </div>
                <h3 class="cmr-channelcgd-card-title">
                    <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                </h3>
                <p class="cmr-channelcgd-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo esc_url($link); ?>" class="cmr-channelcgd-card-link">
                    Read full Release 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
            <?php
        }
    }
    $html = ob_get_clean();

    $total_pages = ceil( max( 0, $query->found_posts - $offset_base ) / 6 );
    $has_more = $paged < $total_pages;

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more
    ) );
}
POST['base_url'] ) : '/';
    $pagination = '';
    if ( $paged >= 3 || $paged >= $total_pages ) {
        $full_base = home_url( $base_url );
        $pagination = paginate_links( array(
            'base'    => trailingslashit( $full_base ) . '%_%',
            'format'  => '?paged=%#%',
            'total'   => $total_pages,
            'current' => $paged,
            'prev_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'next_text' => '<svg width="12" height="18" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ) );
    }

    wp_reset_postdata();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $has_more,
        'pagination' => $pagination
    ) );
}

