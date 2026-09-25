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
                'title'  => 'Automotive',
                'desc'   => 'EV adoption, connected mobility and the consumer shifts reshaping the industry.',
                'link'   => '#', // Apna Link yahan dalein
            ),
            array(
                'number' => '.02',
                'title'  => 'Consumer Tech',
                'desc'   => 'Device ecosystems, buying behaviour and the technologies redefining how people live.',
                'link'   => '#',
            ),
            array(
                'number' => '.03',
                'title'  => 'Digital Supply Chain',
                'desc'   => 'Automation, transformation and resilience strategies for markets that never stand still.',
                'link'   => '#',
            ),
            array(
                'number' => '.04',
                'title'  => 'Healthcare & Pharma',
                'desc'   => 'Digital health transformation, AI diagnostics and patient care innovations.',
                'link'   => '#',
            ),
            array(
                'number' => '.05',
                'title'  => 'IT & Telecom',
                'desc'   => 'Connectivity trends, network evolution and enterprise adoption driving the next wave.',
                'link'   => '#',
            ),
            array(
                'number' => '.06',
                'title'  => 'Energy & Sustainability',
                'desc'   => 'Renewable tech, smart grids, and global decarbonization strategies.',
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
                <h2 class="explore-sectors-title">Explore Industry Intelligence<br>Across Sectors</h2>
            </div>
            
            <div class="explore-sectors-track-wrapper">
                <div class="explore-sectors-track" id="<?php echo esc_attr( $unique_id ); ?>_track">
                    <?php foreach ( $sectors as $sector ) : ?>
                        <div class="explore-sector-card">
                            <span class="sector-number"><?php echo esc_html( $sector['number'] ); ?></span>
                            <div class="sector-content">
                                <h3 class="sector-title"><?php echo esc_html( $sector['title'] ); ?></h3>
                                <p class="sector-desc"><?php echo esc_html( $sector['desc'] ); ?></p>
                            </div>
                            <a href="<?php echo esc_url( !empty($sector['link']) ? $sector['link'] : '#' ); ?>" class="sector-explore-link">Explore <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                    gsap.registerPlugin(ScrollTrigger);
                    
                    let track = document.getElementById("<?php echo esc_js( $unique_id ); ?>_track");
                    let section = document.getElementById("<?php echo esc_js( $unique_id ); ?>_section");
                    
                    if (track && section) {
                        function getScrollAmount() {
                            let trackWidth = track.scrollWidth;
                            return -(trackWidth - window.innerWidth + 40); 
                        }
                        
                        const tween = gsap.to(track, {
                            x: getScrollAmount,
                            ease: "none"
                        });
        
                        ScrollTrigger.create({
                            trigger: section,
                            start: "center center",
                            end: () => `+=${getScrollAmount() * -1}`,
                            pin: true,
                            animation: tween,
                            scrub: 1,
                            invalidateOnRefresh: true
                        });
                    }
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
