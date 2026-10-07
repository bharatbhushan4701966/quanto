<?php
/**
 * Shortcode for Explore Sectors Section
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_explore_sectors_shortcode' ) ) {
    function cmr_explore_sectors_shortcode( $atts ) {
        wp_enqueue_style( 'cmr-explore-sectors' );

        $atts = shortcode_atts( array(
            'tagline'  => 'WHO WE SERVE',
            'title'    => 'Every industry has a question. Our insights<br>deliver the answer and the impact.',
        ), $atts );

        $sectors = array(
            array(
                'number' => '.01',
                'title'  => 'Automotive',
                'desc'   => 'Mapping mobility technology, markets, and adoption.',
                'link'   => home_url( '/automotive/' ),
            ),
            array(
                'number' => '.02',
                'title'  => 'Consumer Tech',
                'desc'   => 'Decoding consumers, markets, and technology dynamics.',
                'link'   => home_url( '/consumer-tech/' ),
            ),
            array(
                'number' => '.03',
                'title'  => 'Digital Supply Chain',
                'desc'   => 'Research for connected, resilient supply chains.',
                'link'   => home_url( '/digital-supply-chain/' ),
            ),
            array(
                'number' => '.04',
                'title'  => 'IT & Telecom',
                'desc'   => 'Strategic research across technology, connectivity, and markets.',
                'link'   => home_url( '/it-telecom/' ),
            ),
            array(
                'number' => '.05',
                'title'  => 'Semiconductors',
                'desc'   => 'Tracking markets, innovation, and demand shifts.',
                'link'   => home_url( '/semiconductors/' ),
            ),
            array(
                'number' => '.06',
                'title'  => 'MSME',
                'desc'   => 'Actionable intelligence on India’s small businesses.',
                'link'   => home_url( '/msme-2/' ),
            ),
            array(
                'number' => '.07',
                'title'  => 'AI',
                'desc'   => 'Navigating enterprise AI transformation and ROI.',
                'link'   => home_url( '/ai/' ),
            ),
            array(
                'number' => '.08',
                'title'  => 'Enterprise Tech',
                'desc'   => 'Strategic insights across cloud, infrastructure, and IT.',
                'link'   => home_url( '/enterprise-tech/' ),
            ),
        );

        ob_start();
        ?>
        <div class="cmr-explore-sectors-section" id="cmr-explore-section">
            <div class="explore-sectors-container">
                <?php if ( ! empty( $atts['tagline'] ) ) : ?>
                    <div class="explore-sectors-tagline"><?php echo esc_html( $atts['tagline'] ); ?></div>
                <?php endif; ?>
                <h2 class="explore-sectors-title"><?php echo wp_kses_post( $atts['title'] ); ?></h2>
            </div>
            

            <style>
                #cmr-explore-section {
                    overflow: hidden !important;
                    position: relative !important;
                    width: 100% !important;
                    box-sizing: border-box !important;
                }
                #cmr-explore-section .explore-sectors-track-wrapper {
                    width: 100% !important;
                    overflow: hidden !important;
                    box-sizing: border-box !important;
                }
                #cmr-explore-track {
                    display: flex !important;
                    flex-wrap: nowrap !important;
                    width: max-content !important;
                    min-width: max-content !important;
                    will-change: transform;
                    gap: 20px !important;
                    box-sizing: border-box !important;
                }
                #cmr-explore-track .explore-sector-card {
                    flex: 0 0 323px !important;
                    min-width: 323px !important;
                    max-width: 323px !important;
                    box-sizing: border-box !important;
                }
                @media (max-width: 768px) {
                    #cmr-explore-section {
                        padding: 30px 0 !important;
                    }
                    #cmr-explore-section .explore-sectors-container {
                        padding: 0 20px !important;
                        margin-bottom: 20px !important;
                    }
                    #cmr-explore-section .explore-sectors-title {
                        font-size: 26px !important;
                        line-height: 1.25 !important;
                        letter-spacing: -0.5px !important;
                        margin-bottom: 20px !important;
                    }
                    #cmr-explore-track {
                        gap: 16px !important;
                        padding-left: 20px !important;
                        padding-right: 20px !important;
                    }
                    #cmr-explore-track .explore-sector-card {
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
                <div class="explore-sectors-track" id="cmr-explore-track">
                    <?php foreach ( $sectors as $sector ) : ?>
                        <div class="explore-sector-card">
                            <span class="sector-number"><?php echo esc_html( $sector['number'] ); ?></span>
                            <div class="sector-content">
                                <h3 class="sector-title">
                                    <a href="<?php echo esc_url( ! empty( $sector['link'] ) ? $sector['link'] : '#' ); ?>" style="color: inherit; text-decoration: none;">
                                        <?php echo esc_html( $sector['title'] ); ?>
                                    </a>
                                </h3>
                                <p class="sector-desc"><?php echo esc_html( $sector['desc'] ); ?></p>
                            </div>
                            <a href="<?php echo esc_url( ! empty( $sector['link'] ) ? $sector['link'] : '#' ); ?>" class="sector-explore-link">Explore <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                function initExploreScroll() {
                    let track   = document.getElementById("cmr-explore-track");
                    let section = document.getElementById("cmr-explore-section");
                    let wrapper = section ? section.querySelector('.explore-sectors-track-wrapper') : null;
                    if (!track || !section || !wrapper) return;

                    // Kill any pre-existing ScrollTrigger on this section to avoid duplicates
                    if (typeof ScrollTrigger !== 'undefined') {
                        ScrollTrigger.getAll().forEach(function(st) {
                            if (st.trigger === section) st.kill(true);
                        });
                    }

                    // Mobile view: disable GSAP pinning to prevent horizontal layout break & black space
                    if (window.innerWidth <= 768) {
                        if (typeof gsap !== 'undefined') {
                            gsap.set(track, { clearProps: "all" });
                        }
                        track.style.transform = '';
                        return;
                    }

                    // Desktop view: GSAP horizontal scroll with pinning
                    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
                    gsap.registerPlugin(ScrollTrigger);

                    gsap.set(track, { clearProps: "transform,x" });

                    function getScrollAmount() {
                        return -(track.scrollWidth - wrapper.clientWidth);
                    }

                    const tween = gsap.to(track, {
                        x: () => getScrollAmount(),
                        ease: "none",
                        invalidateOnRefresh: true
                    });

                    ScrollTrigger.create({
                        trigger: section,
                        start: "center center",
                        end: () => `+=${Math.abs(getScrollAmount())}`,
                        pin: true,
                        pinSpacing: true,
                        animation: tween,
                        scrub: 0.5,
                        invalidateOnRefresh: true,
                        anticipatePin: 1
                    });
                }

                initExploreScroll();
                window.addEventListener('load', function() {
                    initExploreScroll();
                    if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
                });
                window.addEventListener('resize', function() {
                    initExploreScroll();
                    if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
                });
                if (window.jQuery) {
                    jQuery(window).on('elementor/frontend/init', function() {
                        setTimeout(function() {
                            initExploreScroll();
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
add_shortcode( 'cmr_explore_sectors', 'cmr_explore_sectors_shortcode' );
