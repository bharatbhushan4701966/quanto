<?php
/**
 * Shortcode for What We Think Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_what_we_think_shortcode' ) ) {
    function cmr_what_we_think_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'section_label'  => 'What We Think',
            'section_title'  => 'Insights That Drive Strategic<br> Business Decisions',
        ), $atts );

        $query_args = array(
            'post_type'      => array( 'post', 'cmr_news' ),
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'     => '_thumbnail_id',
                    'compare' => 'EXISTS'
                ),
            ),
        );

        $wwt_posts = get_posts( $query_args );
        
        $posts_data = [];
        if ( !empty($wwt_posts) ) {
            foreach ( $wwt_posts as $post_obj ) {
                
                $post_title = get_the_title($post_obj);
                $post_link = get_permalink($post_obj->ID);
                $thumbnail_url = get_the_post_thumbnail_url( $post_obj->ID, 'large' );
                if ( ! $thumbnail_url ) {
                    $thumbnail_url = 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=600&q=80';
                }
                
                $category_name = 'Insights';
                $terms = get_the_terms( $post_obj->ID, 'category' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $category_name = $terms[0]->name;
                }
                
                $posts_data[] = array(
                    'title' => $post_title,
                    'link'  => $post_link,
                    'image' => $thumbnail_url,
                    'cat'   => $category_name,
                );
            }
        }
                // Group into slides (chunks of 2)
        $slides = array_chunk( $posts_data, 2 );
        
        // If no posts found, provide some dummy slides to match the original design layout exactly
        if (empty($slides)) {
            $slides = [
                [
                    ['title' => 'What the UAE Is Doing With Agentic AI — And What India Can Learn?', 'cat' => 'Market Updates', 'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=600&q=80', 'link' => '#'],
                    ['title' => 'The Philippines: Charting a Course as Southeast Asia\'s Digital Economy Leader', 'cat' => 'Market Updates', 'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&q=80', 'link' => '#'],
                ],
                [
                    ['title' => 'How AI Is Reshaping Supply Chain Management Across Asia-Pacific', 'cat' => 'Industry Insights', 'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&q=80', 'link' => '#'],
                    ['title' => 'Digital Transformation in Healthcare: Lessons from Leading Markets', 'cat' => 'Industry Insights', 'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&q=80', 'link' => '#'],
                ],
                [
                    ['title' => 'Global Cloud Infrastructure Spending Report Q1 2026', 'cat' => 'Research Reports', 'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&q=80', 'link' => '#'],
                    ['title' => 'The Future of Work: Hybrid Models and Their Impact on Productivity', 'cat' => 'Research Reports', 'image' => 'https://images.unsplash.com/photo-1497215842964-222b430dc094?w=600&q=80', 'link' => '#'],
                ]
            ];
        }

        ob_start();
        ?>
        <style>
        .cmr-wwt-wrap, .cmr-wwt-wrap * { box-sizing:border-box; }

        .cmr-wwt-wrap {
            position: relative;
            background: #ffffff;
            color: #1a1a2e;
            overflow: visible;
        }

        .cmr-wwt-panel {
            width: 100%;
            height: calc(100vh - 80px); /* Leave room for sticky headers */
            display: flex;
            align-items: center; /* Vertically center the content */
            justify-content: center;
            z-index: 2;
            padding-top: 0;
            padding-bottom: 20px;
        }

        .cmr-wwt-inner {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 1280px;
            padding: 0 20px;
            margin: 0 auto;
        }

        .cmr-wwt-header { margin-bottom: 20px; }

        .cmr-wwt-section-label {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: #000;
            margin-bottom: 12px;
            font-family: 'Instrument Sans', sans-serif;
        }

        .cmr-wwt-heading {
            font-size: 42px; /* Slightly smaller to save vertical space */
            font-weight: 600;
            line-height: 1.25;
            color: #1a1a2e;
            max-width: 100%;
            font-family: 'Instrument Sans', sans-serif;
            margin: 0;
            letter-spacing: -1px;
            letter-spacing: -1px;
        }

        .cmr-wwt-card { flex: 1; min-width: 0; }

        .cmr-wwt-card-img {
            height: 220px; /* Reduced from 272px to ensure text below is always visible */
            width: 100%;
            max-width: 354px;
            overflow: hidden;
            margin-bottom: 14px;
        }
        .cmr-wwt-card-img img {
            height: 100%;
            width: 100%;
            object-fit: cover; 
            display: block; 
        }

        .cmr-wwt-card-cat { display:flex; align-items:center; gap:10px; margin-bottom:10px; cursor:pointer; }
        .cmr-wwt-card-cat-line { width:24px; height:2px; background:#6B3FA0; flex-shrink:0; }
        .cmr-wwt-card-cat-text { font-size:12px; font-weight:400; color:#6B3FA0; letter-spacing:0.3px; text-transform: uppercase; }

        .cmr-wwt-card-title {
            font-size: 18px; font-weight: 600; line-height: 1.4;
            color: #1a1a2e; margin-bottom: 14px; min-height: 44px;
            font-family: 'Instrument Sans', sans-serif;
        }

        .cmr-wwt-card-link {
            display:inline-flex; align-items:center; gap:8px;
            font-family:"Instrument Sans", sans-serif;
            font-size:14px; font-weight:600; line-height:1.2; color:#111;
            text-decoration:none; background:none; border:none;
            padding:0 0 6px 0; border-bottom:2px solid #111;
        }
        .cmr-wwt-card-link:hover { color:#6B3FA0; border-bottom-color: #6B3FA0; }
        .cmr-wwt-card-link::after {
            content:""; width:12px; height:12px;
            background-image:url('https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg');
            background-repeat:no-repeat; background-size:contain; background-position:center;
            display:inline-block;
        }

        .cmr-wwt-menu-item:hover .cmr-wwt-bullet,
        .cmr-wwt-menu-item.cmr-wwt-on .cmr-wwt-bullet { background: #6B3FA0; }

        .cmr-wwt-right-col { flex: 1; min-width: 0; position: relative; }

        .cmr-wwt-slide {
            position: absolute; top: 0; left: 0; width: 100%;
            display: flex; gap: 24px;
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 1.5s ease, transform 1.5s ease;
            pointer-events: none;
        }
        .cmr-wwt-slide:first-child { position: relative; }
        .cmr-wwt-slide.cmr-wwt-show { 
            opacity: 1; 
            transform: translateY(0);
            pointer-events: auto; 
        }

        @media (max-width: 1024px) {
            .cmr-wwt-heading { font-size: 34px; }
            .cmr-wwt-left-col { flex: 0 0 200px; }
            .cmr-wwt-menu-label { font-size: 20px; }
            .cmr-wwt-desktop-content { display: flex; align-items: flex-start; gap: 30px; margin-top: 20px; }
            .cmr-wwt-mobile-content { display: none; }
        }
        
        @media (min-width: 1025px) {
            .cmr-wwt-desktop-content { display: flex; align-items: flex-start; gap: 100px; margin-top: 20px; }
            .cmr-wwt-mobile-content { display: none; }
        }

        @media (max-width: 768px) {
            .cmr-wwt-wrap { height: auto !important; }
            .cmr-wwt-panel { position: relative !important; height: auto !important; padding: 40px 0; }
            .cmr-wwt-heading { font-size: 28px; }
            .cmr-wwt-header { margin-bottom: 20px; }
            .cmr-wwt-desktop-content { display: none; }
            .cmr-wwt-mobile-content { display: block; margin-top: 20px; }
            
            .cmr-wwt-acc-item { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
            .cmr-wwt-acc-item:last-child { border-bottom: none; }
            
            .cmr-wwt-acc-header { 
                display: flex; align-items: center; justify-content: space-between; 
                padding: 15px 0; cursor: pointer; 
            }
            .cmr-wwt-acc-title-wrap { display: flex; align-items: center; gap: 12px; }
            .cmr-wwt-acc-bullet { width: 10px; height: 10px; border-radius: 50%; background: #111; transition: background 0.3s; }
            .cmr-wwt-acc-title { font-size: 20px; font-weight: 600; color: #111; margin: 0; font-family: 'Instrument Sans', sans-serif; letter-spacing: -0.5px; transition: color 0.3s; }
            
            .cmr-wwt-acc-icon { 
                width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;
                transition: transform 0.3s ease; color: #6B3FA0;
            }
            .cmr-wwt-acc-icon svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
            
            .cmr-wwt-acc-content { 
                max-height: 0; overflow: hidden; transition: max-height 0.4s ease; 
            }
            .cmr-wwt-acc-inner { padding: 10px 0 20px 22px; display: flex; flex-direction: column; gap: 24px; }
            
            /* Active State */
            .cmr-wwt-acc-item.active .cmr-wwt-acc-bullet { background: #6B3FA0; }
            .cmr-wwt-acc-item.active .cmr-wwt-acc-title { color: #6B3FA0; }
            .cmr-wwt-acc-item.active .cmr-wwt-acc-icon { transform: rotate(90deg); }
            
            .cmr-wwt-card-img { width: 100%; max-width: 100%; height: 240px; }
            .cmr-wwt-card-title { font-size: 16px; min-height: auto; }
        }
        </style>

        <div class="cmr-wwt-wrap">
            <div class="cmr-wwt-panel">
                <div class="cmr-wwt-inner">
                    <div class="cmr-wwt-header">
                        <div class="cmr-wwt-section-label"><?php echo esc_html( $atts['section_label'] ); ?></div>
                        <h2 class="cmr-wwt-heading"><?php echo wp_kses_post( $atts['section_title'] ); ?></h2>
                    </div>
                    
                    <div class="cmr-wwt-desktop-content">
                        <div class="cmr-wwt-left-col">
                            <?php 
                                $preset_labels = ['Market Updates', 'Industry Insights', 'Research Reports'];
                                foreach($slides as $index => $slide): 
                                $menu_label = isset($preset_labels[$index]) ? $preset_labels[$index] : (isset($slide[0]['cat']) ? $slide[0]['cat'] : 'Insights');
                            ?>
                                <div class="cmr-wwt-menu-item <?php echo $index === 0 ? 'cmr-wwt-on' : ''; ?>" data-i="<?php echo esc_attr($index); ?>">
                                    <span class="cmr-wwt-bullet"></span>
                                    <span class="cmr-wwt-menu-label"><?php echo esc_html($menu_label); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="cmr-wwt-right-col">
                            <?php foreach($slides as $index => $slide): ?>
                                <div class="cmr-wwt-slide <?php echo $index === 0 ? 'cmr-wwt-show' : ''; ?>" data-s="<?php echo esc_attr($index); ?>">
                                    <?php foreach($slide as $post): ?>
                                        <div class="cmr-wwt-card">
                                            <div class="cmr-wwt-card-img">
                                                <a href="<?php echo esc_url($post['link']); ?>">
                                                    <img src="<?php echo esc_url($post['image']); ?>" alt="<?php echo esc_attr($post['title']); ?>">
                                                </a>
                                            </div>
                                            <div class="cmr-wwt-card-cat" data-i="<?php echo esc_attr($index); ?>">
                                                <span class="cmr-wwt-card-cat-line"></span>
                                                <?php $card_cat = isset($preset_labels[$index]) ? $preset_labels[$index] : $post['cat']; ?>
                                                <span class="cmr-wwt-card-cat-text"><?php echo esc_html($card_cat); ?></span>
                                            </div>
                                            <div class="cmr-wwt-card-title">
                                                <a href="<?php echo esc_url($post['link']); ?>" style="text-decoration: none; color: inherit;">
                                                    <?php echo esc_html($post['title']); ?>
                                                </a>
                                            </div>
                                            <a class="cmr-wwt-card-link" href="<?php echo esc_url($post['link']); ?>">More Details</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="cmr-wwt-mobile-content">
                        <?php foreach($slides as $index => $slide): 
                            $menu_label = isset($preset_labels[$index]) ? $preset_labels[$index] : (isset($slide[0]['cat']) ? $slide[0]['cat'] : 'Insights');
                        ?>
                            <div class="cmr-wwt-acc-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="cmr-wwt-acc-header">
                                    <div class="cmr-wwt-acc-title-wrap">
                                        <span class="cmr-wwt-acc-bullet"></span>
                                        <h3 class="cmr-wwt-acc-title"><?php echo esc_html($menu_label); ?></h3>
                                    </div>
                                    <div class="cmr-wwt-acc-icon">
                                        <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"></path></svg>
                                    </div>
                                </div>
                                <div class="cmr-wwt-acc-content" style="<?php echo $index === 0 ? 'max-height: 2000px;' : ''; ?>">
                                    <div class="cmr-wwt-acc-inner">
                                        <?php foreach($slide as $post): ?>
                                            <div class="cmr-wwt-card">
                                                <div class="cmr-wwt-card-img">
                                                    <a href="<?php echo esc_url($post['link']); ?>">
                                                        <img src="<?php echo esc_url($post['image']); ?>" alt="<?php echo esc_attr($post['title']); ?>">
                                                    </a>
                                                </div>
                                                <div class="cmr-wwt-card-cat">
                                                    <span class="cmr-wwt-card-cat-line"></span>
                                                    <?php $card_cat = isset($preset_labels[$index]) ? $preset_labels[$index] : $post['cat']; ?>
                                                    <span class="cmr-wwt-card-cat-text"><?php echo esc_html($card_cat); ?></span>
                                                </div>
                                                <div class="cmr-wwt-card-title">
                                                    <a href="<?php echo esc_url($post['link']); ?>" style="text-decoration: none; color: inherit;">
                                                        <?php echo esc_html($post['title']); ?>
                                                    </a>
                                                </div>
                                                <a class="cmr-wwt-card-link" href="<?php echo esc_url($post['link']); ?>">More Details</a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Failsafe wait for GSAP
            function initWWTScroll() {
                if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                    setTimeout(initWWTScroll, 50);
                    return;
                }
                
                // If on mobile, ignore GSAP pinning entirely for better UX
                if (window.innerWidth <= 768) {
                    initMobileWWT();
                    return;
                }
                
                gsap.registerPlugin(ScrollTrigger);
                
                const wraps = document.querySelectorAll('.cmr-wwt-wrap');
                
                wraps.forEach(wrap => {
                    const panel = wrap.querySelector('.cmr-wwt-panel');
                    const slides = wrap.querySelectorAll('.cmr-wwt-slide');
                    const menuItems = wrap.querySelectorAll('.cmr-wwt-menu-item');
                    const totalSlides = slides.length;
                    
                    if (totalSlides <= 1) return; // No need to pin if only 1 slide
                    
                    // Set scroll duration (amount of pinning)
                    const scrollDuration = totalSlides * window.innerHeight;
                    
                    ScrollTrigger.create({
                        trigger: wrap,
                        start: "top top+=80", // Account for sticky headers if any
                        end: "+=" + scrollDuration,
                        pin: panel,
                        scrub: true,
                        onUpdate: self => {
                            // Calculate current active slide index based on progress
                            let rawIndex = self.progress * totalSlides;
                            let idx = Math.min(totalSlides - 1, Math.floor(rawIndex));
                            
                            // Prevent precision issues at the very end
                            if (self.progress > 0.99) idx = totalSlides - 1;
                            
                            // Update active classes
                            menuItems.forEach((m, i) => m.classList.toggle("cmr-wwt-on", i === idx));
                            slides.forEach((s, i) => s.classList.toggle("cmr-wwt-show", i === idx));
                        }
                    });
                    
                    // Click handlers for menu items to scroll smoothly to their respective points
                    menuItems.forEach((item, i) => {
                        item.addEventListener('click', function() {
                            // Find the ScrollTrigger instance associated with this wrap
                            const st = ScrollTrigger.getAll().find(t => t.trigger === wrap);
                            if (st) {
                                // Calculate pixel offset for the targeted slide
                                const targetY = st.start + (i / totalSlides) * (st.end - st.start) + 10;
                                window.scrollTo({ top: targetY, behavior: 'smooth' });
                            }
                        });
                    });
                });
            }
            
            function initMobileWWT() {
                const wraps = document.querySelectorAll('.cmr-wwt-wrap');
                wraps.forEach(wrap => {
                    const accItems = wrap.querySelectorAll('.cmr-wwt-acc-item');
                    
                    accItems.forEach((item) => {
                        const header = item.querySelector('.cmr-wwt-acc-header');
                        const content = item.querySelector('.cmr-wwt-acc-content');
                        
                        if(header && content) {
                            header.addEventListener('click', function() {
                                const isActive = item.classList.contains('active');
                                
                                // Close all others
                                accItems.forEach(acc => {
                                    acc.classList.remove('active');
                                    const accContent = acc.querySelector('.cmr-wwt-acc-content');
                                    if(accContent) {
                                        accContent.style.maxHeight = '0px';
                                    }
                                });
                                
                                // Open this one if it wasn't active
                                if(!isActive) {
                                    item.classList.add('active');
                                    content.style.maxHeight = content.scrollHeight + 'px';
                                }
                            });
                        }
                    });
                    
                    // On initial load, calculate height for the already active item
                    const activeItem = wrap.querySelector('.cmr-wwt-acc-item.active');
                    if(activeItem) {
                        const content = activeItem.querySelector('.cmr-wwt-acc-content');
                        if(content) {
                            // setTimeout to ensure DOM is fully rendered for scrollHeight
                            setTimeout(() => {
                                content.style.maxHeight = content.scrollHeight + 'px';
                            }, 100);
                        }
                    }
                });
            }
            
            initWWTScroll();
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_what_we_think', 'cmr_what_we_think_shortcode' );

