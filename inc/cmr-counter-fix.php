<?php
/**
 * CMR Counter Widget Fix
 * Enables HTML line breaks (<br>) inside the Counter widget text.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. PHP filter on Elementor widget output
add_filter( 'elementor/widget/render_content', 'cmr_fix_counter_widget_html', 10, 2 );
function cmr_fix_counter_widget_html( $content, $widget ) {
    if ( ! empty( $content ) && ( strpos( $content, 'funfact-info' ) !== false || ( is_object( $widget ) && method_exists( $widget, 'get_name' ) && $widget->get_name() === 'quanto_counter' ) ) ) {
        // Decode escaped <br> tags so they render as actual line breaks
        $content = preg_replace( '/&lt;br\s*\/?&gt;/i', '<br>', $content );
    }
    return $content;
}

// 2. Client-side script for instant rendering and Elementor live editor
add_action( 'wp_footer', 'cmr_fix_counter_br_script', 99 );
add_action( 'elementor/frontend/after_enqueue_scripts', 'cmr_fix_counter_br_script', 99 );
function cmr_fix_counter_br_script() {
    ?>
    <script>
    (function($) {
        if (!$) return;
        function decodeCounterBr() {
            $('.funfact-info').each(function() {
                var html = $(this).html();
                if (html && html.indexOf('&lt;br') !== -1) {
                    $(this).html(html.replace(/&lt;br\s*\/?&gt;/gi, '<br>'));
                }
            });
        }
        $(document).ready(decodeCounterBr);
        $(window).on('load', decodeCounterBr);
        // Elementor live editor hook
        $(window).on('elementor/frontend/init', function() {
            if (window.elementorFrontend && elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/quanto_counter.default', function($scope) {
                    decodeCounterBr();
                });
                elementorFrontend.hooks.addAction('frontend/element_ready/global', function() {
                    decodeCounterBr();
                });
            }
        });
    })(window.jQuery);
    </script>
    <?php
}
