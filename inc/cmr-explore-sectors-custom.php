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
