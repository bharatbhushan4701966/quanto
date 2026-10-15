<?php
/**
 * CMR Browser Tab Title Capitalization Fix
 * Converts ALL CAPS browser tab titles (e.g. 'MARKETING SERVICES') to Title Case ('Marketing Services') globally.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function cmr_format_browser_tab_title( $title ) {
    if ( empty( $title ) || ! is_string( $title ) ) {
        return $title;
    }

    $acronyms = array(
        'CMR', 'AI', 'IT', 'SME', 'SMES', 'EV', 'EVS', 'ADAS', 'SSD', 'HDD',
        'B2B', 'D2C', 'B2C', 'OEM', 'OEMS', 'GTM', 'MSME', 'MSMES', 'BFSI',
        'IOT', '5G', '4G', '3G', 'USA', 'UK', 'UAE', 'ROI', 'KPI', 'API', 'SaaS'
    );

    // Split by common separators: " - ", " | ", " — ", " – "
    $delimiters = array( ' - ', ' | ', ' — ', ' – ' );
    $primary_delimiter = ' - ';
    foreach ( $delimiters as $delim ) {
        if ( strpos( $title, $delim ) !== false ) {
            $primary_delimiter = $delim;
            break;
        }
    }

    $parts = explode( $primary_delimiter, $title );
    foreach ( $parts as $idx => &$part ) {
        // Skip site domain/staging identifier
        if ( $idx > 0 && ( stripos( $part, 'cmrindia' ) !== false || stripos( $part, 'staging' ) !== false ) ) {
            continue;
        }

        $words = preg_split( '/(\s+)/', $part, -1, PREG_SPLIT_DELIM_CAPTURE );
        $word_count = 0;
        $upper_count = 0;

        foreach ( $words as $w ) {
            $clean = preg_replace( '/[^A-Za-z]/', '', $w );
            if ( strlen( $clean ) > 1 ) {
                $word_count++;
                if ( ctype_upper( $clean ) ) {
                    $upper_count++;
                }
            }
        }

        // If part is predominantly ALL CAPS (e.g. "MARKETING SERVICES")
        if ( $word_count > 0 && ( $upper_count / $word_count ) >= 0.5 ) {
            $non_space_index = 0;
            foreach ( $words as &$word ) {
                if ( trim( $word ) === '' ) continue;
                
                $clean = strtoupper( preg_replace( '/[^A-Za-z0-9]/', '', $word ) );
                if ( in_array( $clean, $acronyms ) ) {
                    if ( $clean === 'IOT' ) {
                        $word = 'IoT';
                    } elseif ( $clean === 'SAAS' ) {
                        $word = 'SaaS';
                    } else {
                        $word = $clean;
                    }
                } else {
                    $lower = strtolower( $word );
                    $minor_words = array( 'and', 'or', 'in', 'on', 'at', 'to', 'for', 'with', 'by', 'of', '&' );
                    if ( in_array( $lower, $minor_words ) && $non_space_index > 0 ) {
                        $word = $lower;
                    } else {
                        $word = mb_convert_case( mb_strtolower( $word ), MB_CASE_TITLE, 'UTF-8' );
                    }
                }
                $non_space_index++;
            }
            $part = implode( '', $words );
        }
    }

    return implode( $primary_delimiter, $parts );
}

// 1. Filter document title parts (standard WordPress wp_get_document_title)
add_filter( 'document_title_parts', 'cmr_filter_document_title_parts', 999 );
function cmr_filter_document_title_parts( $title_parts ) {
    if ( is_array( $title_parts ) ) {
        if ( ! empty( $title_parts['title'] ) ) {
            $title_parts['title'] = cmr_format_browser_tab_title( $title_parts['title'] );
        }
    }
    return $title_parts;
}

// 2. Filter pre_get_document_title (SEO plugins, Yoast, RankMath, etc.)
add_filter( 'pre_get_document_title', 'cmr_format_browser_tab_title', 999 );

// 3. Filter wp_title
add_filter( 'wp_title', 'cmr_format_browser_tab_title', 999 );

// 4. Client-side script in <head> to immediately fix document.title and watch for client modifications
add_action( 'wp_head', 'cmr_browser_title_script', 1 );
function cmr_browser_title_script() {
    ?>
    <script>
    (function() {
        var acronyms = ['CMR', 'AI', 'IT', 'SME', 'SMES', 'EV', 'EVS', 'ADAS', 'SSD', 'HDD', 'B2B', 'D2C', 'OEM', 'GTM', 'MSME', 'IOT', 'SAAS', '5G', '4G'];
        var minorWords = ['and', 'or', 'in', 'on', 'at', 'to', 'for', 'with', 'by', 'of', '&'];

        function titleCase(text) {
            if (!text) return text;
            var parts = text.split(/(\s*[-|–—]\s*)/);
            var changed = false;

            for (var i = 0; i < parts.length; i += 2) {
                var segment = parts[i];
                if (i > 0 && /cmrindia|staging/i.test(segment)) continue;

                var words = segment.split(/(\s+)/);
                var wordCount = 0;
                var upperCount = 0;

                words.forEach(function(w) {
                    var clean = w.replace(/[^A-Za-z]/g, '');
                    if (clean.length > 1) {
                        wordCount++;
                        if (clean === clean.toUpperCase()) upperCount++;
                    }
                });

                if (wordCount > 0 && (upperCount / wordCount) >= 0.5) {
                    changed = true;
                    var nonSpaceIndex = 0;
                    parts[i] = words.map(function(w) {
                        if (!w.trim()) return w;
                        var clean = w.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
                        if (acronyms.indexOf(clean) !== -1) {
                            nonSpaceIndex++;
                            return clean === 'IOT' ? 'IoT' : (clean === 'SAAS' ? 'SaaS' : clean);
                        }
                        var lower = w.toLowerCase();
                        if (nonSpaceIndex > 0 && minorWords.indexOf(lower) !== -1) {
                            nonSpaceIndex++;
                            return lower;
                        }
                        nonSpaceIndex++;
                        return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();
                    }).join('');
                }
            }

            return changed ? parts.join('') : text;
        }

        function runTitleFix() {
            if (document.title) {
                var fixed = titleCase(document.title);
                if (fixed !== document.title) {
                    document.title = fixed;
                }
            }
        }

        runTitleFix();
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runTitleFix);
        }
        window.addEventListener('load', runTitleFix);

        // Observe title changes by plugins/Elementor
        if (window.MutationObserver) {
            var titleEl = document.querySelector('title');
            if (titleEl) {
                var observer = new MutationObserver(function() {
                    observer.disconnect();
                    runTitleFix();
                    observer.observe(titleEl, { childList: true });
                });
                observer.observe(titleEl, { childList: true });
            }
        }
    })();
    </script>
    <?php
}
