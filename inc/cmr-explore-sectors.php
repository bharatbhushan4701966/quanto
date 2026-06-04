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

        $sectors = array(
            array(
                'number' => '.01',
                'title'  => 'Automotive',
                'desc'   => 'EV adoption, connected mobility and the consumer shifts reshaping the industry.',
            ),
            array(
                'number' => '.02',
                'title'  => 'Consumer Tech',
                'desc'   => 'Device ecosystems, buying behaviour and the technologies redefining how people live.',
            ),
            array(
                'number' => '.03',
                'title'  => 'Digital Supply Chain',
                'desc'   => 'Automation, transformation and resilience strategies for markets that never stand still.',
            ),
            array(
                'number' => '.04',
                'title'  => 'Digital Supply Chain', // from mockup
                'desc'   => 'Insights on automation, resilience, and digital transformation in supply chains.',
            ),
            array(
                'number' => '.05',
                'title'  => 'IT & Telecom',
                'desc'   => 'Connectivity trends, network evolution and enterprise adoption driving the next wave.',
            ),
        );

        ob_start();
        ?>
        <div class="cmr-explore-sectors-section">
            <div class="explore-sectors-container">
                <h2 class="explore-sectors-title">Explore Industry Intelligence<br>Across Sectors</h2>
                
                <div class="explore-sectors-track">
                    <?php foreach ( $sectors as $sector ) : ?>
                        <div class="explore-sector-card">
                            <span class="sector-number"><?php echo esc_html( $sector['number'] ); ?></span>
                            <div class="sector-content">
                                <h3 class="sector-title"><?php echo esc_html( $sector['title'] ); ?></h3>
                                <p class="sector-desc"><?php echo esc_html( $sector['desc'] ); ?></p>
                            </div>
                            <a href="#" class="sector-explore-link">Explore <i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'cmr_explore_sectors', 'cmr_explore_sectors_shortcode' );
