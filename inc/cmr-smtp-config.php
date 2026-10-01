<?php
/**
 * CMR Email Configuration
 * Respects all custom email IDs configured inside Contact Form 7 settings.
 * Ensures clean From Name "CyberMedia Research (CMR)" and Indian Standard Time (IST).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Universal WordPress Email From Name Filter (Replaces "WordPress" or "Staging - cmrindia.com" with clean company name)
add_filter( 'wp_mail_from_name', 'cmr_custom_wp_mail_from_name', 999 );
function cmr_custom_wp_mail_from_name( $original_name ) {
    if ( empty( $original_name ) || $original_name === 'WordPress' || $original_name === 'Quanto Careers' || stripos( $original_name, 'Staging' ) !== false ) {
        return 'CyberMedia Research (CMR)';
    }
    return $original_name;
}

// 2. Clean up sender display name in Contact Form 7 while keeping whatever Email ID you configure in CF7
add_filter( 'wpcf7_mail_components', 'cmr_clean_cf7_mail_headers', 999, 3 );
function cmr_clean_cf7_mail_headers( $components, $form, $mail ) {
    // If sender has "Staging - cmrindia.com" or "WordPress", replace the name portion with "CyberMedia Research (CMR)"
    // but keep the exact email ID configured in your form
    if ( ! empty( $components['sender'] ) ) {
        if ( stripos( $components['sender'], 'Staging' ) !== false || stripos( $components['sender'], 'WordPress' ) !== false ) {
            $components['sender'] = preg_replace( '/^.*<([^>]+)>/', 'CyberMedia Research (CMR) <$1>', $components['sender'] );
        }
    }

    if ( ! empty( $components['additional_headers'] ) ) {
        $components['additional_headers'] = str_ireplace( 'Staging - cmrindia.com', 'CyberMedia Research (CMR)', $components['additional_headers'] );
    }

    // Automatically convert newlines to line breaks so every field appears on a separate line
    if ( ! empty( $components['body'] ) && strpos( $components['body'], '<br' ) === false && strpos( $components['body'], '<p>' ) === false ) {
        $components['body'] = nl2br( $components['body'] );
    }

    return $components;
}

// 3. Automatically enforce accurate India Timezone (Asia/Kolkata UTC+5:30) for WordPress and Email Logs
add_action( 'init', 'cmr_enforce_site_timezone' );
function cmr_enforce_site_timezone() {
    if ( get_option( 'timezone_string' ) !== 'Asia/Kolkata' ) {
        update_option( 'timezone_string', 'Asia/Kolkata' );
        update_option( 'gmt_offset', '5.5' );
    }
}


