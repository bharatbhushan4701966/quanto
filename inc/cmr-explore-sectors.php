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
            'all'      => 'no',
        ), $atts );

        $sectors = array(
            array(
                'number' => '.01',
                'title'  => 'Automotive',
                'desc'   => 'EV adoption, connected mobility and the consumer shifts reshaping the industry.',
                'link'   => home_url( '/automotive/' ),
            ),
            array(
                'number' => '.02',
                'title'  => 'Consumer Tech',
                'desc'   => 'Device ecosystems, buying behaviour and the technologies redefining how people live.',
                'link'   => home_url( '/consumer-tech/' ),
            ),
            array(
                'number' => '.03',
                'title'  => 'Digital Supply Chain',
                'desc'   => 'Automation, transformation and resilience strategies for markets that never stand still.',
                'link'   => home_url( '/digital-supply-chain/' ),
            ),
            array(
                'number' => '.04',
                'title'  => 'IT & Telecom',
                'desc'   => 'Connectivity trends, network evolution and enterprise adoption driving the next wave.',
                'link'   => home_url( '/it-telecom/' ),
            ),
            array(
                'number' => '.05',
                'title'  => 'Semiconductors',
                'desc'   => 'Chip innovation, supply dynamics and the global forces shaping the technology economy.',
                'link'   => home_url( '/semiconductors/' ),
            ),
        );

        $include_all = ( ! empty( $atts['all'] ) && in_array( strtolower( $atts['all'] ), array( 'yes', 'true', '1', 'all' ), true ) );

        if ( $include_all ) {
            $sectors[] = array(
                'number' => '.06',
                'title'  => 'MSME',
                'desc'   => 'Empowering small & medium enterprise growth with tailored market insights.',
                'link'   => home_url( '/msme-2/' ),
            );
            $sectors[] = array(
                'number' => '.07',
                'title'  => 'AI',
                'desc'   => 'Artificial Intelligence & transformation insights for future-ready enterprises.',
                'link'   => home_url( '/ai/' ),
            );
            $sectors[] = array(
                'number' => '.08',
                'title'  => 'Enterprise Tech',
                'desc'   => 'Cloud, infrastructure & enterprise IT solutions powering digital acceleration.',
                'link'   => home_url( '/enterprise-tech/' ),
            );
        }

        ob_start();
        ?>
        <div class="cmr-explore-sectors-section" id="cmr-explore-section">
            <div class="explore-sectors-container">
                <?php if ( ! empty( $atts['tagline'] ) ) : ?>
                    <div class="explore-sectors-tagline"><?php echo esc_html( $atts['tagline'] ); ?></div>
                <?php endif; ?>
                <h2 class="explore-sectors-title"><?php echo wp_kses_post( $atts['title'] ); ?></h2>
            </div>
            
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
                    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                        gsap.registerPlugin(ScrollTrigger);
                        
                        let track = document.getElementById("cmr-explore-track");
                        let section = document.getElementById("cmr-explore-section");
                        
                        if (track && section) {
                            function getScrollAmount() {
                                let trackWidth = track.scrollWidth;
                                // Move left enough to show the end of the track. Add padding offset
                                return -(trackWidth - window.innerWidth + 40); 
                            }
                            
                            const tween = gsap.to(track, {
                                x: getScrollAmount,
                                ease: "none"
                            });
            
                            ScrollTrigger.create({
                                trigger: section,
                                start: "center center", // Pin when section reaches center
                                end: () => `+=${getScrollAmount() * -1}`, // Scroll length based on track width
                                pin: true,
                                animation: tween,
                                scrub: 1,
                                invalidateOnRefresh: true
                            });
                        }
                    }
                }

                initExploreScroll();
                if (window.jQuery) {
                    jQuery(window).on('elementor/frontend/init', initExploreScroll);
                }
            });
            </script>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_explore_sectors', 'cmr_explore_sectors_shortcode' );
