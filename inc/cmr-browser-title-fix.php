<?php
/**
 * CMR Browser Tab Title Fix (Global)
 * Converts browser tab titles to proper Title Case across ALL pages, posts, archives, and sections.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_to_title_case_clean' ) ) {
    function cmr_to_title_case_clean( $str ) {
        if ( empty( $str ) || ! is_string( $str ) ) {
            return $str;
        }

        $acronyms = array(
            'CMR', 'AI', 'IT', 'SME', 'SMES', 'EV', 'EVS', 'ADAS', 'SSD', 'HDD',
            'B2B', 'D2C', 'B2C', 'OEM', 'OEMS', 'GTM', 'MSME', 'MSMES', 'BFSI',
            '5G', '4G', '3G', 'USA', 'UK', 'UAE', 'ROI', 'KPI', 'API'
        );

        // Split by all common title separators (supports en-dash –, em-dash —, hyphen -, pipe |)
        $tokens = preg_split( '/(\s+[-–—|]\s+)/u', $str, -1, PREG_SPLIT_DELIM_CAPTURE );

        foreach ( $tokens as &$token ) {
            // Keep separators and domain names unchanged
            if ( preg_match( '/\s+[-–—|]\s+/u', $token ) || stripos( $token, 'cmrindia' ) !== false ) {
                continue;
            }

            // Convert all uppercase words to Title Case while preserving recognized acronyms
            $token = preg_replace_callback( '/[A-Za-z0-9]+/u', function( $matches ) use ( $acronyms ) {
                $word = $matches[0];
                $upper = strtoupper( $word );
                if ( in_array( $upper, $acronyms, true ) ) {
                    return $upper;
                }
                if ( $upper === 'IOT' ) return 'IoT';
                if ( $upper === 'SAAS' ) return 'SaaS';

                // Only change if the word is in ALL CAPS and length > 1, or format to Title Case
                return ucfirst( strtolower( $word ) );
            }, $token );
        }

        return implode( '', $tokens );
    }
}

// 1. Filter document_title_parts globally across all pages, archives, posts, etc.
add_filter( 'document_title_parts', 'cmr_global_document_title_parts', 99999 );
function cmr_global_document_title_parts( $parts ) {
    if ( is_array( $parts ) ) {
        foreach ( $parts as $k => $v ) {
            if ( $k === 'site' ) continue;
            $parts[$k] = cmr_to_title_case_clean( $v );
        }
    }
    return $parts;
}

// 2. Filter pre_get_document_title for SEO plugins / full title overrides
add_filter( 'pre_get_document_title', 'cmr_to_title_case_clean', 99999 );

// 3. Filter wp_title for themes/plugins using legacy title output
add_filter( 'wp_title', 'cmr_to_title_case_clean', 99999 );

// 4. Client-side script in <head>: Formats browser tab title globally for all pages
add_action( 'wp_head', 'cmr_global_tab_title_script', 1 );
function cmr_global_tab_title_script() {
    ?>
    <script>
    (function() {
        var acronyms = ['CMR', 'AI', 'IT', 'SME', 'SMES', 'EV', 'EVS', 'ADAS', 'SSD', 'HDD', 'B2B', 'D2C', 'OEM', 'GTM', 'MSME', 'MSMES', 'BFSI', '5G', '4G', '3G', 'USA', 'UK'];

        function formatTabTitle(title) {
            if (!title) return title;
            // Split by all common separators: " – ", " - ", " — ", " | "
            var sepRegex = /(\s+[-–—|]\s+)/;
            var tokens = title.split(sepRegex);

            return tokens.map(function(token) {
                if (sepRegex.test(token) || /cmrindia\.com/i.test(token)) {
                    return token;
                }
                return token.replace(/[A-Za-z0-9]+/g, function(word) {
                    var upper = word.toUpperCase();
                    if (acronyms.indexOf(upper) !== -1) return upper;
                    if (upper === 'IOT') return 'IoT';
                    if (upper === 'SAAS') return 'SaaS';
                    return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                });
            }).join('');
        }

        function applyTabTitle() {
            if (!document.title) return;
            var formatted = formatTabTitle(document.title);
            if (formatted !== document.title) {
                document.title = formatted;
            }
        }

        applyTabTitle();
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyTabTitle);
        }
        window.addEventListener('load', applyTabTitle);

        if (window.MutationObserver) {
            var titleEl = document.querySelector('title');
            if (titleEl) {
                var observer = new MutationObserver(function() {
                    observer.disconnect();
                    applyTabTitle();
                    observer.observe(titleEl, { childList: true });
                });
                observer.observe(titleEl, { childList: true });
            }
        }
    })();
    </script>
    <?php
}
