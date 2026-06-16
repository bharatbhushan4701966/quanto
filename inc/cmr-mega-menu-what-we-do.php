<?php
/* Mega Menu Shortcode: [cmr_mega_menu_what_we_do] */

add_shortcode('cmr_mega_menu_what_we_do', 'cmr_mega_menu_what_we_do_shortcode');

function cmr_get_mmw_posts($slug, $fallback_offset) {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category_name'  => $slug
    );
    $posts = get_posts($args);
    if (count($posts) < 3) {
        $args['category_name'] = '';
        $args['offset'] = $fallback_offset;
        $posts = get_posts($args);
    }
    return $posts;
}

function cmr_mega_menu_what_we_do_shortcode($atts) {
    ob_start();

    // Fetch 3 distinct sets of posts
    $posts_industry = cmr_get_mmw_posts('industry-intelligence', 0);
    $posts_consulting = cmr_get_mmw_posts('consulting-advisory', 3);
    $posts_marketing = cmr_get_mmw_posts('marketing-services', 6);

    ?>
    <style>
        .cmr-mmw-wrapper {
            position: relative;
            font-family: 'Instrument Sans', sans-serif;
            width: 900px !important;
            max-width: none !important;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            background: #fff;
            overflow: hidden;
            display: flex;
        }
        
        .cmr-mmw-wrapper::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            width: 16px;
            height: 16px;
            background: #f3f5ff; 
            box-shadow: -3px -3px 5px rgba(0,0,0,0.03);
            border-radius: 2px;
            z-index: 0;
        }

        .cmr-mmw-left {
            width: 35%;
            padding: 30px;
            background: #fff;
            position: relative;
            z-index: 2;
        }

        .cmr-mmw-right {
            width: 65%;
            padding: 30px;
            background: linear-gradient(135deg, #f3f5ff 0%, #edf9fb 50%, #e6faf7 100%);
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
        }

        .cmr-mmw-label {
            font-size: 13px;
            font-weight: 600;
            color: #9ba4b5;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .cmr-mmw-menu-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .cmr-mmw-item {
            text-decoration: none;
            display: block;
            position: relative;
        }
        
        .cmr-mmw-item:hover h4 {
            color: #6A35FF;
        }

        .cmr-mmw-item-header {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
        }

        .cmr-mmw-item h4 {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0px;
            color: #111;
            margin: 0;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .cmr-mmw-item p {
            font-size: 14px;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }

        .cmr-mmw-item.active h4 {
            color: #6A35FF !important;
        }

        .cmr-mmw-item.active::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -38px;
            transform: translateY(-50%) rotate(45deg);
            width: 16px;
            height: 16px;
            background: #fff;
            border-top-right-radius: 2px;
            z-index: 2;
        }

        .cmr-mmw-text-inner {
            border-bottom: 2px solid transparent;
            padding-bottom: 2px;
            display: inline-flex;
            align-items: center;
            transition: border-color 0.2s;
        }

        .cmr-mmw-item.active .cmr-mmw-text-inner {
            border-bottom: 2px solid #6A35FF;
        }

        .cmr-mmw-arrow {
            display: none;
            margin-left: 6px;
        }

        .cmr-mmw-item.active .cmr-mmw-arrow {
            display: inline-block;
        }

        /* POSTS LIST */
        .cmr-mmw-posts-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            flex: 1;
        }

        .cmr-mmw-post-card {
            display: flex !important;
            flex-direction: row !important;
            align-items: stretch;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .cmr-mmw-post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .cmr-mmw-post-img {
            width: 140px;
            flex-shrink: 0;
            background: #eaeaea;
        }

        .cmr-mmw-post-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cmr-mmw-post-content {
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }

        .cmr-mmw-post-date {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .cmr-mmw-post-title {
            font-size: 15px;
            font-weight: 600;
            color: #111;
            line-height: 1.3;
            margin: 0 0 10px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cmr-mmw-post-readmore {
            font-size: 13px;
            font-weight: 600;
            color: #111;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-bottom: 1px solid #111;
            padding-bottom: 1px;
            width: max-content;
        }

        .cmr-mmw-explore-btn {
            text-align: center;
            margin-top: 20px;
        }

        .cmr-mmw-explore-btn a {
            font-size: 16px;
            font-weight: 600;
            color: #000 !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s ease;
        }

        .cmr-mmw-explore-btn a:hover {
            color: #6A35FF;
        }

        @media (max-width: 768px) {
            .cmr-mmw-wrapper {
                flex-direction: column;
            }
            .cmr-mmw-left, .cmr-mmw-right {
                width: 100%;
            }
        }
    </style>

    <div class="cmr-mmw-wrapper" id="cmr-mmw-wrapper-inner">
        <div class="cmr-mmw-left">
            <div class="cmr-mmw-label">WHAT WE DO</div>
            <div class="cmr-mmw-menu-list">
                <a href="/industry-intelligence" class="cmr-mmw-item cmr-mmw-item-hover-trigger active" data-target="cmr-mmw-list-industry">
                    <div class="cmr-mmw-item-header">
                        <h4>
                            <span class="cmr-mmw-text-inner">
                                Industry Intelligence
                                <svg class="cmr-mmw-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                            </span>
                        </h4>
                    </div>
                    <p>Market research and strategic insights</p>
                </a>
                
                <a href="/consulting-advisory" class="cmr-mmw-item cmr-mmw-item-hover-trigger" data-target="cmr-mmw-list-consulting">
                    <div class="cmr-mmw-item-header">
                        <h4>
                            <span class="cmr-mmw-text-inner">
                                Consulting & Advisory 
                                <svg class="cmr-mmw-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                            </span>
                        </h4>
                    </div>
                    <p>Expert guidance for business growth</p>
                </a>
                
                <a href="/marketing-services" class="cmr-mmw-item cmr-mmw-item-hover-trigger" data-target="cmr-mmw-list-marketing">
                    <div class="cmr-mmw-item-header">
                        <h4>
                            <span class="cmr-mmw-text-inner">
                                Marketing Services
                                <svg class="cmr-mmw-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                            </span>
                        </h4>
                    </div>
                    <p>Research-backed marketing solutions</p>
                </a>
            </div>
        </div>

        <div class="cmr-mmw-right">
            <div class="cmr-mmw-label">LATEST POSTS</div>
            
            <?php
            $tabs = array(
                'cmr-mmw-list-industry' => $posts_industry,
                'cmr-mmw-list-consulting' => $posts_consulting,
                'cmr-mmw-list-marketing' => $posts_marketing
            );
            $first_tab = true;
            foreach ($tabs as $tab_id => $tab_posts) :
            ?>
            <div class="cmr-mmw-posts-list" id="<?php echo esc_attr($tab_id); ?>" style="<?php echo $first_tab ? 'display: flex;' : 'display: none;'; ?>">
                <?php if ( ! empty( $tab_posts ) ) : ?>
                    <?php foreach ( $tab_posts as $post ) : 
                        $thumbnail_url = get_the_post_thumbnail_url( $post->ID, 'medium' );
                        if ( ! $thumbnail_url ) {
                            $thumbnail_url = 'https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/Why-Chipsets-are-the-New-Frontier-in-Smartphones1.jpg';
                        }
                    ?>
                        <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="cmr-mmw-post-card">
                            <div class="cmr-mmw-post-img">
                                <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>">
                            </div>
                            <div class="cmr-mmw-post-content">
                                <div class="cmr-mmw-post-date"><?php echo get_the_date( 'M j, Y', $post->ID ); ?></div>
                                <div class="cmr-mmw-post-title"><?php echo esc_html( get_the_title( $post->ID ) ); ?></div>
                                <div class="cmr-mmw-post-readmore">
                                    Read More 
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No recent posts found.</p>
                <?php endif; ?>
            </div>
            <?php 
            $first_tab = false;
            endforeach; 
            ?>
            
            <div class="cmr-mmw-explore-btn">
                <a href="/insights">
                    Explore More industry insights 
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Use a unique function to avoid global scope pollution if called multiple times
        (function() {
            var wrapper = document.getElementById('cmr-mmw-wrapper-inner');
            if (!wrapper) return;
            var items = wrapper.querySelectorAll('.cmr-mmw-item-hover-trigger');
            var lists = wrapper.querySelectorAll('.cmr-mmw-posts-list');
            
            items.forEach(function(item) {
                item.addEventListener('mouseenter', function() {
                    // remove active class
                    items.forEach(function(i) { i.classList.remove('active'); });
                    // hide all lists
                    lists.forEach(function(l) { l.style.display = 'none'; });
                    
                    // activate this
                    this.classList.add('active');
                    var targetId = this.getAttribute('data-target');
                    var targetList = document.getElementById(targetId);
                    if (targetList) {
                        targetList.style.display = 'flex';
                    }
                });
            });
        })();
    </script>
    <?php
    return ob_get_clean();
}

// Automatically inject the mega menu into the nav bar
add_action('wp_footer', 'cmr_inject_what_we_do_mega_menu', 100);
function cmr_inject_what_we_do_mega_menu() {
    $mega_menu_html = do_shortcode('[cmr_mega_menu_what_we_do]');
    ?>
    <div id="cmr-hidden-mega-menu-do" style="display: none;">
        <?php echo $mega_menu_html; ?>
    </div>

    <style>
        .cmr-has-mega-menu-do {
            position: relative !important;
        }
        
        .cmr-mmw-wrapper-outer {
            position: absolute !important;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: max-content !important;
            max-width: none !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
            padding-top: 15px;
            z-index: 9999;
        }

        .cmr-has-mega-menu-do:hover .cmr-mmw-wrapper-outer {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .cmr-has-mega-menu-do > a .sub-arrow {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var megaMenuDo = document.getElementById('cmr-hidden-mega-menu-do');
            if (!megaMenuDo) return;

            var navLinks = document.querySelectorAll('.menu-item > a, .elementor-item');
            
            navLinks.forEach(function(link) {
                var text = link.innerText.trim().toLowerCase();
                if (text === 'what we do') {
                    var parentLi = link.closest('li, .menu-item');
                    if (parentLi) {
                        parentLi.classList.add('cmr-has-mega-menu-do');
                        
                        var wrapperOuter = document.createElement('div');
                        wrapperOuter.className = 'cmr-mmw-wrapper-outer';
                        
                        while (megaMenuDo.childNodes.length > 0) {
                            wrapperOuter.appendChild(megaMenuDo.childNodes[0]);
                        }
                        
                        parentLi.appendChild(wrapperOuter);
                    }
                }
            });
            
            if (megaMenuDo) {
                megaMenuDo.remove();
            }
        });
    </script>
    <?php
}
