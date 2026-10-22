<?php
/**
 * CMR Email Configuration
 * Respects all custom email IDs configured inside Contact Form 7 settings.
 * Ensures clean From Name "Cyber Media Research (CMR)" and Indian Standard Time (IST).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Universal WordPress Email From Name Filter (Replaces "WordPress" or "Staging - cmrindia.com" with clean company name)
add_filter( 'wp_mail_from_name', 'cmr_custom_wp_mail_from_name', 999 );
function cmr_custom_wp_mail_from_name( $original_name ) {
    if ( empty( $original_name ) || $original_name === 'WordPress' || $original_name === 'Quanto Careers' || stripos( $original_name, 'Staging' ) !== false ) {
        return 'Cyber Media Research (CMR)';
    }
    return $original_name;
}

// 2. Clean up sender display name in Contact Form 7 and auto-resolve tag mismatches (full-name vs your-name, email-address vs your-email)
add_filter( 'wpcf7_mail_components', 'cmr_clean_cf7_mail_headers', 999, 3 );
function cmr_clean_cf7_mail_headers( $components, $form, $mail ) {
    // If sender has "Staging - cmrindia.com" or "WordPress", replace the name portion with "Cyber Media Research (CMR)"
    // but keep the exact email ID configured in your form
    if ( ! empty( $components['sender'] ) ) {
        if ( stripos( $components['sender'], 'Staging' ) !== false || stripos( $components['sender'], 'WordPress' ) !== false ) {
            $components['sender'] = preg_replace( '/^.*<([^>]+)>/', 'Cyber Media Research (CMR) <$1>', $components['sender'] );
        }
    }

    if ( ! empty( $components['additional_headers'] ) ) {
        $components['additional_headers'] = str_ireplace( 'Staging - cmrindia.com', 'Cyber Media Research (CMR)', $components['additional_headers'] );
    }

    // Auto-resolve field tags from submission data (fixes Mail (2) failed recipient)
    $submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;
    if ( $submission ) {
        $posted_data = $submission->get_posted_data();

        // 1. Extract submitted user email
        $user_email = '';
        foreach ( array( 'email-address', 'your-email', 'email', 'user-email', 'contact-email' ) as $key ) {
            if ( ! empty( $posted_data[ $key ] ) && is_email( $posted_data[ $key ] ) ) {
                $user_email = sanitize_email( $posted_data[ $key ] );
                break;
            }
        }

        // 2. Extract submitted user name
        $user_name = '';
        foreach ( array( 'full-name', 'your-name', 'name', 'first-name' ) as $key ) {
            if ( ! empty( $posted_data[ $key ] ) ) {
                $user_name = sanitize_text_field( $posted_data[ $key ] );
                break;
            }
        }

        // 3. Fix empty or unreplaced recipient tag in Mail (2) / auto-responder
        if ( empty( $components['recipient'] ) || strpos( $components['recipient'], '[' ) !== false ) {
            if ( ! empty( $user_email ) ) {
                $components['recipient'] = $user_email;
            }
        }

        // 4. Auto-replace placeholders like [your-name] / [full-name] / [your-email] in body and subject
        if ( ! empty( $components['body'] ) ) {
            if ( ! empty( $user_name ) ) {
                $components['body'] = str_replace( array( '[your-name]', '[full-name]', '[name]' ), $user_name, $components['body'] );
            }
            if ( ! empty( $user_email ) ) {
                $components['body'] = str_replace( array( '[your-email]', '[email-address]', '[email]' ), $user_email, $components['body'] );
            }
        }

        if ( ! empty( $components['subject'] ) ) {
            if ( ! empty( $user_name ) ) {
                $components['subject'] = str_replace( array( '[your-name]', '[full-name]', '[name]' ), $user_name, $components['subject'] );
            }
        }
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

// 4. Universal Site Branding Filters: Replace "CyberMedia" with "Cyber Media" (keeps URLs and emails intact)
function cmr_format_cyber_media_text( $text ) {
    if ( empty( $text ) || ! is_string( $text ) ) {
        return $text;
    }
    return preg_replace( '/(?<![\/@\.a-zA-Z0-9_-])CyberMedia(?!\.(co|com|in|org|net|io))/i', 'Cyber Media', $text );
}

add_filter( 'option_blogname', 'cmr_format_cyber_media_text', 999 );
add_filter( 'bloginfo', function( $output, $show ) {
    if ( $show === 'name' || $show === 'description' ) {
        return cmr_format_cyber_media_text( $output );
    }
    return $output;
}, 999, 2 );

add_filter( 'document_title_parts', function( $title_parts ) {
    if ( isset( $title_parts['site'] ) ) {
        $title_parts['site'] = cmr_format_cyber_media_text( $title_parts['site'] );
    }
    if ( isset( $title_parts['title'] ) ) {
        $title_parts['title'] = cmr_format_cyber_media_text( $title_parts['title'] );
    }
    return $title_parts;
}, 999 );

add_filter( 'wp_title', 'cmr_format_cyber_media_text', 999 );
add_filter( 'the_title', 'cmr_format_cyber_media_text', 999 );

// 5. Universal Frontend Filter for Website Content (Pages, Footers, Headers, Elementor Widgets)
add_action( 'template_redirect', function() {
    if ( is_admin() || wp_doing_ajax() ) {
        return;
    }
    ob_start( function( $buffer ) {
        if ( ! $buffer ) {
            return $buffer;
        }
        return preg_replace( '/(?<![\/@\.a-zA-Z0-9_-])CyberMedia(?!\.(co|com|in|org|net|io))/i', 'Cyber Media', $buffer );
    } );
} );


