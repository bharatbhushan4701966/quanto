<?php
/**
 * CMR Browser Tab Title Fix
 * Synchronizes the browser tab title with the page's title, formatted in clean Title Case.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cmr_clean_page_title' ) ) {
    function cmr_clean_page_title( $text ) {
        if ( empty( $text ) ) {
            return '';
        }
        if ( function_exists( 'cmr_format_breadcrumb_title' ) ) {
            return html_entity_decode( cmr_format_breadcrumb_title( $text ), ENT_QUOTES, 'UTF-8' );
        }
        $text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
        $text = ucwords( mb_strtolower( wp_strip_all_tags( $text ), 'UTF-8' ) );

        $replacements = array(
            '/\bIt & Telecom\b/i' => 'IT & Telecom',
            '/\bIt Telecom\b/i'   => 'IT & Telecom',
            '/\bIt\b/'            => 'IT',
            '/\bAi\b/'            => 'AI',
            '/\bIot\b/'           => 'IoT',
            '/\b5g\b/i'           => '5G',
            '/\b4g\b/i'           => '4G',
            '/\bPc\b/'            => 'PC',
            '/\bPcs\b/'           => 'PCs',
            '/\bMsme\b/i'         => 'MSME',
            '/\bCmr\b/i'          => 'CMR',
            '/\bSme\b/i'          => 'SME',
            '/\bSmes\b/i'         => 'SMEs',
            '/\bEv\b/i'           => 'EV',
            '/\bEvs\b/i'          => 'EVs',
            '/\bAdas\b/i'         => 'ADAS',
            '/\bSsd\b/i'          => 'SSD',
            '/\bHdd\b/i'          => 'HDD',
            '/\bB2b\b/i'          => 'B2B',
            '/\bD2c\b/i'          => 'D2C',
            '/\bGtm\b/i'          => 'GTM',
        );

        foreach ( $replacements as $pattern => $replacement ) {
            $text = preg_replace( $pattern, $replacement, $text );
        }

        return $text;
    }
}

// 1. Filter document_title_parts: set title part to match the cleaned page title
add_filter( 'document_title_parts', 'cmr_sync_document_title_parts', 9999 );
function cmr_sync_document_title_parts( $title_parts ) {
    if ( is_array( $title_parts ) ) {
        if ( is_singular() ) {
            $title_parts['title'] = cmr_clean_page_title( get_the_title() );
        } elseif ( is_category() || is_tax() ) {
            $title_parts['title'] = cmr_clean_page_title( single_term_title( '', false ) );
        } elseif ( ! empty( $title_parts['title'] ) ) {
            $title_parts['title'] = cmr_clean_page_title( $title_parts['title'] );
        }
    }
    return $title_parts;
}

// 2. Filter pre_get_document_title
add_filter( 'pre_get_document_title', 'cmr_sync_pre_get_document_title', 9999 );
function cmr_sync_pre_get_document_title( $title ) {
    if ( ! empty( $title ) && is_string( $title ) ) {
        $delimiters = array( ' - ', ' | ', ' — ', ' – ' );
        foreach ( $delimiters as $delim ) {
            if ( strpos( $title, $delim ) !== false ) {
                $segments = explode( $delim, $title );
                $segments[0] = cmr_clean_page_title( $segments[0] );
                return implode( $delim, $segments );
            }
        }
        return cmr_clean_page_title( $title );
    }
    return $title;
}

// 3. Filter wp_title
add_filter( 'wp_title', 'cmr_clean_page_title', 9999 );

// 4. Client-side: Sync browser tab title directly with page title from breadcrumb / h1
add_action( 'wp_head', 'cmr_sync_browser_tab_title_script', 1 );
function cmr_sync_browser_tab_title_script() {
    ?>
    <script>
    (function() {
        var acronyms = {'It': 'IT', 'Ai': 'AI', 'Iot': 'IoT', 'Cmr': 'CMR', 'Msme': 'MSME', 'Sme': 'SME', 'Smes': 'SMEs', 'Ev': 'EV', 'Evs': 'EVs', 'Adas': 'ADAS', 'Ssd': 'SSD', 'Hdd': 'HDD', 'B2b': 'B2B', 'D2c': 'D2C', 'Gtm': 'GTM', '5g': '5G', '4g': '4G'};

        function formatTitleText(str) {
            if (!str) return str;
            var formatted = str.toLowerCase().replace(/(?:^|\s|\/|-)\w/g, function(m) {
                return m.toUpperCase();
            });
            for (var k in acronyms) {
                var reg = new RegExp('\\b' + k + '\\b', 'g');
                formatted = formatted.replace(reg, acronyms[k]);
            }
            return formatted;
        }

        function syncTabTitle() {
            if (!document.title) return;

            // Find visible page title on page (breadcrumb current page, page title h1, etc.)
            var pageTitleEl = document.querySelector('.cmr-breadcrumbs .current-page, h1.page-title, h1');
            var pageTitleText = pageTitleEl ? pageTitleEl.innerText.trim() : '';

            var parts = document.title.split(' - ');

            if (pageTitleText && pageTitleText.length > 1 && pageTitleText.length < 80) {
                var cleanTitle = formatTitleText(pageTitleText);
                if (parts[0] !== cleanTitle) {
                    parts[0] = cleanTitle;
                    document.title = parts.join(' - ');
                }
            } else if (parts[0] && parts[0] === parts[0].toUpperCase() && parts[0].length > 2) {
                parts[0] = formatTitleText(parts[0]);
                document.title = parts.join(' - ');
            }
        }

        syncTabTitle();
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncTabTitle);
        }
        window.addEventListener('load', syncTabTitle);

        if (window.MutationObserver) {
            var titleEl = document.querySelector('title');
            if (titleEl) {
                var observer = new MutationObserver(function() {
                    observer.disconnect();
                    syncTabTitle();
                    observer.observe(titleEl, { childList: true });
                });
                observer.observe(titleEl, { childList: true });
            }
        }
    })();
    </script>
    <?php
}
