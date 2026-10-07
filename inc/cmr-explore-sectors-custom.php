<?php
/**
 * Shortcode for Custom Explore Sectors Section (New Page)
 * Shortcode: [cmr_explore_sectors_custom]
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_explore_sectors_custom_shortcode' ) ) {
    function cmr_explore_sectors_custom_shortcode( $atts ) {
        // Enqueue explore sectors CSS
        wp_enqueue_style( 'cmr-explore-sectors' );

        // Unique ID to avoid GSAP conflict if multiple instances exist
        $unique_id = uniqid('cmr_explore_');

        /* ==========================================================================
           👇 YAHAN AAP APNE CARDS KA CONTENT EDIT / ADD KAR SAKTE HAIN 👇
           ========================================================================== */
        $sectors = array(
            array(
                'number' => '.01',
                'title'  => 'Syndicate Events',
                'desc'   => 'Curated, high-impact gatherings that bring together key decision-makers for focused discussions, meaningful networking, and strategic collaborations',
                'link'   => '#', // Apna Link yahan dalein
            ),
            array(
                'number' => '.02',
                'title'  => 'Executive Round Tables',
                'desc'   => 'Engage with C-suite executives and industry leaders in intimate, high-value conversations to explore market trends, challenges, and growth opportunities.',
                'link'   => '#',
            ),
            array(
                'number' => '.03',
                'title'  => 'Awards & Recognition',
                'desc'   => 'Position your brand as an industry leader by recognizing and celebrating excellence, fostering brand credibility and deeper market engagement.',
                'link'   => '#',
            ),
            array(
                'number' => '.04',
                'title'  => 'Bespoke Programs',
                'desc'   => 'Customized engagement strategies designed to align with your business objectives,amplify brand influence, and drive customer engagement.',
                'link'   => '#',
            ),
            array(
                'number' => '.05',
                'title'  => 'Marketing Campaigns',
                'desc'   => 'Craft and execute high-impact campaigns that enhance brand visibility, generate quality leads, and position your solutions as industry must-haves.',
                'link'   => '#',
            ),
            array(
                'number' => '.06',
                'title'  => 'Webinars',
                'desc'   => 'Host insightful, interactive sessions featuring top industry experts, delivering thought leadership while engaging directly with your target audience.',
                'link'   => '#',
            ),
            array(
                'number' => '.07',
                'title'  => 'Offsites',
                'desc'   => 'Interact with your target customers in an uninterrupted, remote environment to establish thought leadership of your brand.',
                'link'   => '#',
            ),
            array(
                'number' => '.08',
                'title'  => 'Influencer Marketing',
                'desc'   => 'Leverage trusted industry voices and technology influencers to amplify brand awareness, build credibility, and drive high-quality engagement with your target audience.',
                'link'   => '#',
            ),
            
            // 👉 Naya card add karne ke liye upar wala block copy karke yahan paste kar sakte hain.
        );
        /* ========================================================================== */

        ob_start();
        ?>
        <div class="cmr-explore-sectors-section" id="<?php echo esc_attr( $unique_id ); ?>_section">
            <div class="explore-sectors-container">
                <!-- Section Title: Aap title ko yahan se edit kar sakte hain -->
                <h2 class="explore-sectors-title">Our Offerings </h2>
            </div>
            

            <style>
                #<?php echo esc_attr( $unique_id ); ?>_section {
                    overflow: hidden !important;
                    position: relative !important;
                    width: 100% !important;
                    box-sizing: border-box !important;
                }
                #<?php echo esc_attr( $unique_id ); ?>_section .explore-sectors-track-wrapper {
                    width: 100% !important;
                    overflow: hidden !important;
                    box-sizing: border-box !important;
                }
                #<?php echo esc_attr( $unique_id ); ?>_track {
                    display: flex !important;
                    flex-wrap: nowrap !important;
                    width: max-content !important;
                    min-width: max-content !important;
                    will-change: transform;
                    gap: 20px !important;
                    box-sizing: border-box !important;
                }
                #<?php echo esc_attr( $unique_id ); ?>_track .explore-sector-card {
                    flex: 0 0 323px !important;
                    min-width: 323px !important;
                    max-width: 323px !important;
                    box-sizing: border-box !important;
                }
                #<?php echo esc_attr( $unique_id ); ?>_section .sector-explore-link {
                    display: none !important;
                }
                #<?php echo esc_attr( $unique_id ); ?>_section .sector-content {
                    margin-bottom: 0 !important;
                }

                @media (max-width: 768px) {
                    #<?php echo esc_attr( $unique_id ); ?>_section {
                        padding: 30px 0 !important;
                    }
                    #<?php echo esc_attr( $unique_id ); ?>_section .explore-sectors-container {
                        padding: 0 20px !important;
                        margin-bottom: 20px !important;
                    }
                    #<?php echo esc_attr( $unique_id ); ?>_section .explore-sectors-title {
                        font-size: 26px !important;
                        line-height: 1.25 !important;
                        letter-spacing: -0.5px !important;
                        margin-bottom: 20px !important;
                    }
                    #<?php echo esc_attr( $unique_id ); ?>_track {
                        gap: 16px !important;
                        padding-left: 20px !important;
                        padding-right: 20px !important;
                    }
                    #<?php echo esc_attr( $unique_id ); ?>_track .explore-sector-card {
                        flex: 0 0 280px !important;
                        width: 280px !important;
                        max-width: 280px !important;
                        min-width: 280px !important;
                        height: auto !important;
                        min-height: 280px !important;
                        padding: 28px 20px !important;
                    }
                }
            </style>

            <div class="explore-sectors-track-wrapper">
                <div class="explore-sectors-track" id="<?php echo esc_attr( $unique_id ); ?>_track">
                    <?php foreach ( $sectors as $sector ) : ?>
                        <div class="explore-sector-card">
                            <span class="sector-number"><?php echo esc_html( $sector['number'] ); ?></span>
                            <div class="sector-content">
                                <h3 class="sector-title"><?php echo esc_html( $sector['title'] ); ?></h3>
                                <p class="sector-desc"><?php echo esc_html( $sector['desc'] ); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                function initExploreCustomScroll() {
                    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
                    gsap.registerPlugin(ScrollTrigger);

                    let track   = document.getElementById("<?php echo esc_js( $unique_id ); ?>_track");
                    let section = document.getElementById("<?php echo esc_js( $unique_id ); ?>_section");
                    let wrapper = section ? section.querySelector('.explore-sectors-track-wrapper') : null;
                    if (!track || !section || !wrapper) return;

                    ScrollTrigger.getAll().forEach(function(st) {
                        if (st.trigger === section) st.kill();
                    });

                    gsap.set(track, { clearProps: "transform,x" });

                    function getScrollAmount() {
                        return -(track.scrollWidth - wrapper.clientWidth);
                    }

                    const tween = gsap.to(track, {
                        x: () => getScrollAmount(),
                        ease: "none",
                        invalidateOnRefresh: true
                    });

                    const isMobile = window.innerWidth <= 768;
                    const startPosition = isMobile ? "top 70px" : "center center";

                    ScrollTrigger.create({
                        trigger: section,
                        start: startPosition,
                        end: () => `+=${Math.abs(getScrollAmount())}`,
                        pin: true,
                        pinSpacing: true,
                        animation: tween,
                        scrub: 0.5,
                        invalidateOnRefresh: true,
                        anticipatePin: 1
                    });
                }

                initExploreCustomScroll();
                window.addEventListener('load', function() {
                    initExploreCustomScroll();
                    if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
                });
                window.addEventListener('resize', function() {
                    initExploreCustomScroll();
                    if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
                });
                if (window.jQuery) {
                    jQuery(window).on('elementor/frontend/init', function() {
                        setTimeout(function() {
                            initExploreCustomScroll();
                            if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
                        }, 300);
                    });
                }
            });
            </script>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Register Shortcode
add_shortcode( 'cmr_explore_sectors_custom', 'cmr_explore_sectors_custom_shortcode' );
