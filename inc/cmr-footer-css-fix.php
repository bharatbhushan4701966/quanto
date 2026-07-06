<?php
/**
 * CMR Footer Dynamic CSS Fix
 * This script injects dynamic classes into the Elementor footer so that
 * the CSS applies correctly even if the user re-publishes and the data-id changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var footer = document.querySelector('footer.footer');
        if (!footer) return;
        
        var headings = footer.querySelectorAll('.elementor-heading-title');
        headings.forEach(function(h) {
            if (h.innerText.includes("Your challenge")) {
                var container = h.closest('.elementor-container');
                if (container) container.classList.add('cmr-dynamic-challenge-container');
                
                var col = h.closest('.elementor-column');
                if (col) col.classList.add('cmr-dynamic-challenge-col');
                
                var widget = h.closest('.elementor-widget');
                if(widget) widget.classList.add('cmr-dynamic-challenge-heading');
            }
            if (h.innerText.includes("Let's connect") || h.innerText.includes("Let&#8217;s connect") || h.innerText.includes("Let’s connect")) {
                var widget = h.closest('.elementor-widget');
                if(widget) widget.classList.add('cmr-dynamic-lets-connect');
            }
        });
        
        var texts = footer.querySelectorAll('.elementor-widget-text-editor');
        texts.forEach(function(t) {
            if (t.innerText.includes("Every business faces")) {
                t.classList.add('cmr-dynamic-challenge-text');
            }
        });
        
        var iconLists = footer.querySelectorAll('.elementor-widget-icon-list');
        iconLists.forEach(function(list) {
            if (list.closest('.cmr-dynamic-challenge-container')) {
                list.classList.add('cmr-dynamic-challenge-icon-list');
            }
        });
    });
    </script>
    <?php
}, 100);
