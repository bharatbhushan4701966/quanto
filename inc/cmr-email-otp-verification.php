<?php
/**
 * CMR Universal Email OTP Verification System
 * 
 * Automatically attaches an OTP "Verify Email" workflow to all forms with an email field.
 * - Attaches directly to input.parentElement with position:relative !important
 * - Input keeps 100% exact same height, border, radius, font, and styling as all other fields (Name, Phone, etc.)
 * - Badge sits neatly inside the right side of the input field across ALL modals and forms
 * - Compact OTP box slides down directly below the field without stretching or distorting the form
 * - Blocks form submission if email is not verified (frontend + backend CF7 validation)
 *
 * @package Quanto
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =========================================================================
// 1. AJAX Handlers: Send OTP & Verify OTP
// =========================================================================

add_action( 'wp_ajax_cmr_send_email_otp', 'cmr_send_email_otp_ajax' );
add_action( 'wp_ajax_nopriv_cmr_send_email_otp', 'cmr_send_email_otp_ajax' );

function cmr_send_email_otp_ajax() {
    check_ajax_referer( 'cmr_otp_nonce', 'nonce' );

    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

    if ( empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
    }

    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    $rate_limit_key = 'cmr_otprate_' . md5( $ip . '|' . strtolower( $email ) );
    $request_count  = (int) get_transient( $rate_limit_key );

    // Rate Limit: Max 4 requests per 10 minutes per IP/email
    if ( $request_count >= 4 ) {
        wp_send_json_error( array( 'message' => 'Too many OTP requests. Please wait 5 minutes before trying again.' ) );
    }
    set_transient( $rate_limit_key, $request_count + 1, 10 * MINUTE_IN_SECONDS );

    // Generate 6-digit numeric OTP
    $otp = (string) wp_rand( 100000, 999999 );

    // Store OTP in transient for 10 minutes
    $transient_key = 'cmr_otp_' . md5( strtolower( $email ) );
    $otp_data = array(
        'otp'      => $otp,
        'email'    => strtolower( $email ),
        'created'  => time(),
        'attempts' => 0,
    );
    set_transient( $transient_key, $otp_data, 10 * MINUTE_IN_SECONDS );

    // Send the OTP Email
    $sent = cmr_dispatch_otp_email( $email, $otp );

    if ( $sent ) {
        wp_send_json_success( array(
            'message' => 'Verification code sent! Please check your inbox (and spam folder).',
            'email'   => $email,
        ) );
    } else {
        wp_send_json_error( array(
            'message' => 'Failed to send verification email. Please check your email address and try again.',
        ) );
    }
}

add_action( 'wp_ajax_cmr_verify_email_otp', 'cmr_verify_email_otp_ajax' );
add_action( 'wp_ajax_nopriv_cmr_verify_email_otp', 'cmr_verify_email_otp_ajax' );

function cmr_verify_email_otp_ajax() {
    check_ajax_referer( 'cmr_otp_nonce', 'nonce' );

    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $otp   = isset( $_POST['otp'] ) ? sanitize_text_field( wp_unslash( $_POST['otp'] ) ) : '';

    if ( empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Invalid email address.' ) );
    }

    if ( empty( $otp ) || strlen( $otp ) !== 6 ) {
        wp_send_json_error( array( 'message' => 'Please enter a valid 6-digit OTP code.' ) );
    }

    $transient_key = 'cmr_otp_' . md5( strtolower( $email ) );
    $otp_data      = get_transient( $transient_key );

    if ( ! $otp_data || ! is_array( $otp_data ) ) {
        wp_send_json_error( array( 'message' => 'OTP has expired or was not requested. Please request a new code.' ) );
    }

    // Attempt tracking (max 5 failed attempts)
    $attempts = isset( $otp_data['attempts'] ) ? (int) $otp_data['attempts'] : 0;
    if ( $attempts >= 5 ) {
        delete_transient( $transient_key );
        wp_send_json_error( array( 'message' => 'Too many incorrect attempts. Please request a new code.' ) );
    }

    if ( trim( (string) $otp_data['otp'] ) !== trim( $otp ) ) {
        $otp_data['attempts'] = $attempts + 1;
        set_transient( $transient_key, $otp_data, 10 * MINUTE_IN_SECONDS );
        $remaining = 5 - $otp_data['attempts'];
        wp_send_json_error( array( 'message' => 'Incorrect OTP code. Please try again (' . $remaining . ' attempts left).' ) );
    }

    // OTP is valid! Delete OTP transient and set verified token
    delete_transient( $transient_key );

    // Generate secure verification token valid for 30 minutes (single-use upon form submission)
    $token = wp_hash( strtolower( $email ) . '|' . wp_salt( 'auth' ) . '|' . date( 'Ymd' ) );
    $verified_key = 'cmr_ver_' . md5( strtolower( $email ) );
    set_transient( $verified_key, $token, 30 * MINUTE_IN_SECONDS );

    wp_send_json_success( array(
        'message' => 'Email verified successfully!',
        'email'   => $email,
        'token'   => $token,
    ) );
}

/**
 * Helper to check if an email is verified
 */
function cmr_is_email_verified( $email, $token = '' ) {
    $email = strtolower( trim( $email ) );
    if ( empty( $email ) || ! is_email( $email ) ) {
        return false;
    }

    $verified_key = 'cmr_ver_' . md5( $email );
    $stored_token = get_transient( $verified_key );

    if ( ! $stored_token ) {
        return false;
    }

    if ( ! empty( $token ) ) {
        return hash_equals( (string) $stored_token, (string) $token );
    }

    return true;
}

// =========================================================================
// 2. Contact Form 7 Backend Validation Filter & Single-Use Consumption
// =========================================================================

add_filter( 'wpcf7_validate_email', 'cmr_cf7_enforce_email_otp_verified', 30, 2 );
add_filter( 'wpcf7_validate_email*', 'cmr_cf7_enforce_email_otp_verified', 30, 2 );

function cmr_cf7_enforce_email_otp_verified( $result, $tag ) {
    $tag = new WPCF7_FormTag( $tag );
    $name = $tag->name;

    if ( empty( $name ) ) {
        return $result;
    }

    $email = isset( $_POST[ $name ] ) ? sanitize_email( wp_unslash( $_POST[ $name ] ) ) : '';
    if ( empty( $email ) ) {
        return $result; // Handled by required filter
    }

    $token = isset( $_POST['cmr_email_verified_token'] ) ? sanitize_text_field( wp_unslash( $_POST['cmr_email_verified_token'] ) ) : '';

    if ( ! cmr_is_email_verified( $email, $token ) ) {
        $result->invalidate( $tag, 'Please verify your email address using the OTP verification code first.' );
    }

    return $result;
}

/**
 * Helper to extract email from CF7 posted data
 */
function cmr_extract_email_from_cf7_posted( $posted ) {
    if ( empty( $posted ) || ! is_array( $posted ) ) {
        return '';
    }
    foreach ( array( 'your-email', 'email', 'email-address' ) as $key ) {
        if ( ! empty( $posted[ $key ] ) && is_email( $posted[ $key ] ) ) {
            return sanitize_email( $posted[ $key ] );
        }
    }
    foreach ( $posted as $key => $val ) {
        if ( is_string( $val ) && is_email( $val ) ) {
            return sanitize_email( $val );
        }
    }
    return '';
}

/**
 * Invalidate / consume verified email OTP token once the form submission is processed
 */
add_action( 'wpcf7_before_send_mail', 'cmr_cf7_consume_email_otp_token', 20, 3 );
function cmr_cf7_consume_email_otp_token( $contact_form, &$abort, $submission ) {
    if ( ! $submission ) {
        return;
    }
    $posted = $submission->get_posted_data();
    $email  = cmr_extract_email_from_cf7_posted( $posted );
    if ( ! empty( $email ) ) {
        delete_transient( 'cmr_ver_' . md5( strtolower( $email ) ) );
    }
}

add_action( 'wpcf7_submit', 'cmr_cf7_consume_email_otp_token_on_submit', 20, 2 );
function cmr_cf7_consume_email_otp_token_on_submit( $contact_form, $result ) {
    $status = is_array( $result ) && isset( $result['status'] ) ? $result['status'] : '';
    if ( in_array( $status, array( 'mail_sent', 'mail_failed' ), true ) ) {
        $submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;
        if ( $submission ) {
            $posted = $submission->get_posted_data();
            $email  = cmr_extract_email_from_cf7_posted( $posted );
            if ( ! empty( $email ) ) {
                delete_transient( 'cmr_ver_' . md5( strtolower( $email ) ) );
            }
        }
    }
}

/**
 * AJAX endpoint to immediately consume / invalidate verified email status
 */
add_action( 'wp_ajax_cmr_consume_verified_email', 'cmr_ajax_consume_verified_email' );
add_action( 'wp_ajax_nopriv_cmr_consume_verified_email', 'cmr_ajax_consume_verified_email' );
function cmr_ajax_consume_verified_email() {
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    if ( ! empty( $email ) ) {
        delete_transient( 'cmr_ver_' . md5( strtolower( $email ) ) );
        wp_send_json_success( array( 'message' => 'Token consumed successfully.' ) );
    }
    wp_send_json_error( array( 'message' => 'Invalid email address.' ) );
}

// =========================================================================
// 3. Branded HTML Email Dispatcher
// =========================================================================

function cmr_dispatch_otp_email( $to_email, $otp_code ) {
    $subject = 'Your Verification Code: ' . $otp_code . ' - Cyber Media Research';

    $site_name = get_bloginfo( 'name' ) ?: 'Cyber Media Research (CMR)';
    $site_url  = home_url( '/' );

    $body = '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Verification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 520px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); padding: 32px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">Cyber Media Research (CMR)</h1>
                            <p style="margin: 6px 0 0 0; color: #93c5fd; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">Email Verification</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 35px 35px 25px 35px;">
                            <p style="margin: 0 0 16px 0; font-size: 16px; line-height: 24px; color: #334155;">Hello,</p>
                            <p style="margin: 0 0 25px 0; font-size: 15px; line-height: 24px; color: #475569;">
                                Please use the following One-Time Password (OTP) to verify your email address on <strong>' . esc_html( $site_name ) . '</strong>:
                            </p>
                            <div style="text-align: center; margin: 30px 0; padding: 22px; background: #f8fafc; border: 2px dashed #0284c7; border-radius: 10px;">
                                <div style="font-size: 34px; font-weight: 800; letter-spacing: 8px; color: #0284c7; font-family: Consolas, Monaco, monospace; display: inline-block;">' . esc_html( $otp_code ) . '</div>
                                <div style="margin-top: 10px; font-size: 12px; color: #64748b; font-weight: 500;">Valid for 10 minutes</div>
                            </div>
                            <p style="margin: 25px 0 0 0; font-size: 14px; line-height: 22px; color: #64748b;">
                                If you did not request this verification code, please disregard this email. Your account remains completely secure.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8fafc; padding: 22px 35px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; font-size: 12px; line-height: 18px; color: #94a3b8;">
                                &copy; ' . date( 'Y' ) . ' <a href="' . esc_url( $site_url ) . '" style="color: #0284c7; text-decoration: none;">Cyber Media Research &amp; Services Ltd.</a> All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

    $site_domain  = parse_url( home_url(), PHP_URL_HOST ) ?: 'cmrindia.com';
    $clean_domain = preg_replace( '/^www\./', '', $site_domain );
    $default_from = 'no-reply@' . $clean_domain;

    $from_email = apply_filters( 'cmr_otp_from_email', $default_from );
    $from_name  = apply_filters( 'cmr_otp_from_name', 'Cyber Media Research (CMR)' );

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
    );

    return wp_mail( $to_email, $subject, $body, $headers );
}

// =========================================================================
// 4. Frontend Bootstrap Badge Styles & Controller
// =========================================================================

add_action( 'wp_footer', 'cmr_render_email_otp_frontend', 995 );

function cmr_render_email_otp_frontend() {
    $ajax_url = admin_url( 'admin-ajax.php' );
    $nonce    = wp_create_nonce( 'cmr_otp_nonce' );
    ?>
    <style id="cmr-email-otp-css">
    /* Ensure the input's direct parent container is strictly bound */
    .wpcf7-form-control-wrap,
    .wpcf7-form-control-wrap[data-name*="email"],
    .wpcf7-form-control-wrap[data-name*="your-email"],
    .xoo-aff-input-group,
    .xoo-aff-cont-email .xoo-aff-input-group {
        position: relative !important;
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
    }

    /* Badge container inside the email input (Right-Aligned) */
    .cmr-email-badge-container,
    .xoo-aff-input-group .cmr-email-badge-container,
    .custom-consultation-form .cmr-email-badge-container,
    .custom-subscribe-form .cmr-email-badge-container,
    .elementor-popup-modal .cmr-email-badge-container,
    .cmr-form .cmr-email-badge-container {
        position: absolute !important;
        right: 14px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        z-index: 50 !important;
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 4px !important;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1 !important;
        pointer-events: auto !important;
    }

    /* Bootstrap Badges (Strictly insulated) */
    .cmr-email-badge-container .badge,
    .custom-consultation-form .cmr-email-badge-container .badge,
    .custom-subscribe-form .cmr-email-badge-container .badge,
    .elementor-popup-modal .cmr-email-badge-container .badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 3px !important;
        font-family: 'Instrument Sans', sans-serif !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        height: 24px !important;
        min-height: 24px !important;
        max-height: 24px !important;
        padding: 0 10px !important;
        border-radius: 50rem !important;
        white-space: nowrap !important;
        text-transform: none !important;
        letter-spacing: 0.2px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15) !important;
        cursor: pointer !important;
        border: none !important;
        margin: 0 !important;
        width: auto !important;
        min-width: unset !important;
        text-decoration: none !important;
    }

    /* Unverified "Verify Email" Badge */
    .cmr-email-badge-container .cmr-verify-badge {
        background-color: #0d6efd !important;
        background-image: none !important;
        color: #ffffff !important;
    }

    .cmr-email-badge-container .cmr-verify-badge:hover {
        background-color: #0b5ed7 !important;
        transform: scale(1.03) !important;
    }

    .cmr-email-badge-container .cmr-verify-badge.cmr-pulse-attention {
        animation: cmrBadgePulse 1.2s infinite !important;
        background-color: #dc3545 !important;
    }

    @keyframes cmrBadgePulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        50% { box-shadow: 0 0 0 5px rgba(220, 53, 69, 0); }
    }

    /* Verified Badge */
    .cmr-email-badge-container .cmr-verified-badge {
        background-color: #198754 !important;
        background-image: none !important;
        color: #ffffff !important;
        cursor: default !important;
    }

    .cmr-email-badge-container .cmr-verified-badge svg {
        width: 11px !important;
        height: 11px !important;
        stroke: #ffffff !important;
        stroke-width: 3 !important;
    }

    /* Change Badge */
    .cmr-email-badge-container .cmr-change-badge {
        background-color: #6c757d !important;
        background-image: none !important;
        color: #ffffff !important;
        font-size: 10px !important;
        height: 20px !important;
        min-height: 20px !important;
        max-height: 20px !important;
        padding: 0 6px !important;
    }

    .cmr-email-badge-container .cmr-change-badge:hover {
        background-color: #5c636a !important;
    }

    /* Premium Compact OTP Box below field */
    .cmr-otp-box,
    .xoo-el-fields-cont .cmr-otp-box,
    .custom-consultation-form .cmr-otp-box,
    .custom-subscribe-form .cmr-otp-box,
    .elementor-popup-modal .cmr-otp-box {
        display: none;
        width: 100% !important;
        max-width: 100% !important;
        background: #f8fafc !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 12px !important;
        margin-top: 6px !important;
        margin-bottom: 12px !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        box-sizing: border-box !important;
        animation: cmrOtpSlide 0.25s ease-out !important;
    }

    @keyframes cmrOtpSlide {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .cmr-otp-box .cmr-otp-header-row,
    .custom-consultation-form .cmr-otp-box .cmr-otp-header-row,
    .custom-subscribe-form .cmr-otp-box .cmr-otp-header-row,
    .elementor-popup-modal .cmr-otp-box .cmr-otp-header-row {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-start !important;
        margin-bottom: 8px !important;
        font-family: 'Instrument Sans', sans-serif !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        color: #475569 !important;
        line-height: 1.35 !important;
        width: 100% !important;
    }

    .cmr-otp-box .cmr-otp-controls-row,
    .custom-consultation-form .cmr-otp-box .cmr-otp-controls-row,
    .custom-subscribe-form .cmr-otp-box .cmr-otp-controls-row,
    .elementor-popup-modal .cmr-otp-box .cmr-otp-controls-row {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 6px !important;
        width: 100% !important;
    }

    .cmr-otp-box input.cmr-otp-code-input,
    .custom-consultation-form .cmr-otp-box input.cmr-otp-code-input,
    .custom-subscribe-form .cmr-otp-box input.cmr-otp-code-input,
    .elementor-popup-modal .cmr-otp-box input.cmr-otp-code-input {
        width: 130px !important;
        min-width: 130px !important;
        max-width: 130px !important;
        height: 40px !important;
        min-height: 40px !important;
        max-height: 40px !important;
        border: 1.5px solid #d1d5db !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        font-family: 'Instrument Sans', sans-serif !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        letter-spacing: 3px !important;
        text-align: center !important;
        color: #111827 !important;
        padding: 0 4px !important;
        outline: none !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        display: inline-block !important;
        flex: 0 0 130px !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .cmr-otp-box input.cmr-otp-code-input::placeholder {
        font-size: 12px !important;
        letter-spacing: 2px !important;
        font-weight: 400 !important;
        color: #9ca3af !important;
    }

    .cmr-otp-box input.cmr-otp-code-input:focus {
        border-color: #6A42E5 !important;
        box-shadow: 0 0 0 3px rgba(106, 66, 229, 0.15) !important;
    }

    .cmr-otp-box input.cmr-otp-code-input.cmr-shake {
        animation: cmrShake 0.4s ease-in-out !important;
        border-color: #dc3545 !important;
    }

    @keyframes cmrShake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-4px); }
        40%, 80% { transform: translateX(4px); }
    }

    .cmr-otp-box .cmr-otp-confirm-btn,
    .custom-consultation-form .cmr-otp-box .cmr-otp-confirm-btn,
    .custom-subscribe-form .cmr-otp-box .cmr-otp-confirm-btn,
    .elementor-popup-modal .cmr-otp-box .cmr-otp-confirm-btn {
        background: #6A42E5 !important;
        background-color: #6A42E5 !important;
        background-image: linear-gradient(135deg, #6A42E5 0%, #4B2DBF 100%) !important;
        color: #ffffff !important;
        font-family: 'Instrument Sans', sans-serif !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        height: 40px !important;
        min-height: 40px !important;
        max-height: 40px !important;
        padding: 0 16px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        border: none !important;
        box-shadow: 0 2px 6px rgba(106, 66, 229, 0.25) !important;
        text-transform: none !important;
        width: auto !important;
        margin: 0 !important;
        flex: 0 0 auto !important;
        white-space: nowrap !important;
        transition: all 0.2s ease !important;
    }

    .cmr-otp-box .cmr-otp-confirm-btn:hover {
        background: #582cd7 !important;
        background-color: #582cd7 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(106, 66, 229, 0.35) !important;
    }

    .cmr-otp-box .cmr-otp-resend-btn,
    .custom-consultation-form .cmr-otp-box .cmr-otp-resend-btn,
    .custom-subscribe-form .cmr-otp-box .cmr-otp-resend-btn,
    .elementor-popup-modal .cmr-otp-box .cmr-otp-resend-btn {
        background-color: #f1f5f9 !important;
        background-image: none !important;
        color: #64748b !important;
        font-family: 'Instrument Sans', sans-serif !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        height: 40px !important;
        min-height: 40px !important;
        max-height: 40px !important;
        padding: 0 10px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        border: 1px solid #e2e8f0 !important;
        text-transform: none !important;
        width: auto !important;
        margin: 0 !important;
        flex: 0 0 auto !important;
        white-space: nowrap !important;
        transition: all 0.2s ease !important;
    }

    .cmr-otp-box .cmr-otp-resend-btn:not(.disabled) {
        background-color: #ffffff !important;
        color: #6A42E5 !important;
        border-color: #c7d2fe !important;
        cursor: pointer !important;
    }

    .cmr-otp-box .cmr-otp-resend-btn:not(.disabled):hover {
        background-color: #eef2ff !important;
        border-color: #6A42E5 !important;
    }

    .cmr-otp-box .cmr-otp-resend-btn.disabled {
        opacity: 0.65 !important;
        cursor: not-allowed !important;
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }

    .cmr-otp-feedback {
        font-family: 'Instrument Sans', sans-serif !important;
        font-size: 11.5px !important;
        margin-top: 8px !important;
        padding: 5px 8px !important;
        border-radius: 6px !important;
        line-height: 1.35 !important;
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
    }

    .cmr-otp-feedback.cmr-success {
        color: #15803d !important;
        background-color: #f0fdf4 !important;
        border: 1px solid #bbf7d0 !important;
    }

    .cmr-otp-feedback.cmr-error {
        color: #b91c1c !important;
        background-color: #fef2f2 !important;
        border: 1px solid #fecaca !important;
    }

    .cmr-otp-feedback.cmr-info {
        color: #4338ca !important;
        background-color: #eef2ff !important;
        border: 1px solid #c7d2fe !important;
    }

    @media (max-width: 480px) {
        .cmr-otp-box .cmr-otp-controls-row {
            gap: 5px !important;
        }
        .cmr-otp-box input.cmr-otp-code-input {
            width: 100px !important;
            min-width: 100px !important;
            flex: 0 0 100px !important;
            font-size: 14px !important;
            letter-spacing: 2px !important;
        }
        .cmr-otp-box .cmr-otp-confirm-btn {
            padding: 0 10px !important;
            font-size: 11.5px !important;
        }
        .cmr-otp-box .cmr-otp-resend-btn {
            padding: 0 8px !important;
            font-size: 10.5px !important;
        }
    }

    .cmr-email-unverified-notice {
        font-size: 12px !important;
        color: #dc3545 !important;
        font-weight: 600 !important;
        margin-top: 4px !important;
        display: block !important;
    }
    </style>

    <script id="cmr-email-otp-js">
    (function() {
        var CMR_OTP_CONFIG = {
            ajaxUrl: <?php echo wp_json_encode( $ajax_url ); ?>,
            nonce: <?php echo wp_json_encode( $nonce ); ?>
        };

        var verifiedEmailTokens = {};
        var resendIntervals = {};

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(String(email).trim());
        }

        function isEmailField(input) {
            if (!input || input.tagName !== 'INPUT') return false;
            var type = (input.type || '').toLowerCase();
            if (type === 'hidden' || type === 'checkbox' || type === 'radio' || type === 'submit' || type === 'button') {
                return false;
            }

            var name = (input.name || '').toLowerCase();
            var id = (input.id || '').toLowerCase();

            // Skip coupon inputs and search
            if (name === 'coupon_code' || id === 'coupon_code' || input.classList.contains('cmr-coupon-input') || input.closest('.cmr-promo-box, .cmr-coupon-form')) {
                return false;
            }

            // Skip xoo-el, account registration, and login inputs completely
            if (name.indexOf('xoo') !== -1 || 
                name === 'user_login' || 
                name === 'log' || 
                name === 'username' ||
                input.closest('.xoo-el-container, .xoo-el-form-container, .xoo-el-wrap, .xoo-el-section, .woocommerce-account, .woocommerce-checkout, .woocommerce-cart, .cmr-checkout-wrap')) {
                return false;
            }

            var form = input.closest('form');
            if (form) {
                var formClass = form.className || '';
                var formId = form.id || '';
                if (formClass.indexOf('search') !== -1 ||
                    form.getAttribute('role') === 'search' ||
                    formClass.indexOf('woocommerce-checkout') !== -1 ||
                    formClass.indexOf('woocommerce-cart') !== -1 ||
                    formClass.indexOf('woocommerce-form') !== -1 ||
                    formClass.indexOf('woocommerce-account') !== -1 ||
                    formClass.indexOf('xoo-el') !== -1 ||
                    formClass.indexOf('xoo-aff') !== -1 ||
                    formClass.indexOf('checkout') !== -1 ||
                    form.querySelector('input[name="_xoo_el_form"], input[name^="xoo_el_"]') ||
                    formId === 'loginform' ||
                    formId === 'registerform' ||
                    formId === 'lostpasswordform' ||
                    formId === 'customer_login') {
                    return false;
                }
            }

            var cls = (input.className || '').toLowerCase();

            return type === 'email' ||
                   name === 'your-email' ||
                   name === 'email' ||
                   name === 'email-address' ||
                   name.indexOf('email') !== -1 ||
                   cls.indexOf('wpcf7-email') !== -1;
        }

        function positionBadgeContainer(input, badgeContainer) {
            if (!input || !badgeContainer) return;
            badgeContainer._targetInput = input;
            var h = input.offsetHeight;
            var t = input.offsetTop;
            if (h > 0) {
                var centerTop = t + (h / 2);
                badgeContainer.style.setProperty('top', centerTop + 'px', 'important');
                badgeContainer.style.setProperty('transform', 'translateY(-50%)', 'important');
            } else {
                badgeContainer.style.setProperty('top', '50%', 'important');
                badgeContainer.style.setProperty('transform', 'translateY(-50%)', 'important');
            }
        }

        function getOrCreateBadgeContainer(input) {
            var parent = input.parentElement;
            if (!parent) return null;

            if (parent.style.position !== 'relative') {
                parent.style.setProperty('position', 'relative', 'important');
                parent.style.setProperty('display', 'block', 'important');
                parent.style.setProperty('width', '100%', 'important');
                parent.style.setProperty('max-width', '100%', 'important');
                parent.style.setProperty('box-sizing', 'border-box', 'important');
            }

            var badgeContainer = parent.querySelector('.cmr-email-badge-container');
            if (!badgeContainer) {
                badgeContainer = document.createElement('div');
                badgeContainer.className = 'cmr-email-badge-container';
                parent.appendChild(badgeContainer);
            }
            badgeContainer._targetInput = input;
            positionBadgeContainer(input, badgeContainer);
            return badgeContainer;
        }

        function getOrCreateOtpBox(input) {
            var parent = input.parentElement;
            if (!parent) return null;

            // In xoo-el, input is inside .xoo-aff-group (.xoo_el_reg_email_cont)
            // In CF7, input can be in <p><label>...</label></p> or just <label>...</label>
            // We MUST ensure the OTP box is inserted AFTER the outer form field row, NEVER inside an input group
            var insertTarget = input.closest('.xoo-aff-group, .form-field, .cmr-input-group, p') || input.closest('label') || parent;
            var parentNode = insertTarget.parentNode;
            if (!parentNode) return null;

            var existing = insertTarget.nextElementSibling;
            if (existing && existing.classList.contains('cmr-otp-box')) {
                existing._targetInput = input;
                return existing;
            }

            var inParent = parentNode.querySelector(':scope > .cmr-otp-box');
            if (inParent) {
                inParent._targetInput = input;
                return inParent;
            }

            var otpBox = document.createElement('div');
            otpBox.className = 'cmr-otp-box';
            otpBox.style.setProperty('display', 'none', 'important');
            otpBox._targetInput = input;
            otpBox.innerHTML = '' +
                '<div class="cmr-otp-header-row">' +
                    '<span class="cmr-otp-header-text">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6A42E5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-1.5px; margin-right:4px;"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>' +
                        'Enter 6-digit code sent to <strong class="cmr-otp-dest-email" style="color:#0f172a; word-break:break-all;"></strong>' +
                    '</span>' +
                '</div>' +
                '<div class="cmr-otp-controls-row">' +
                    '<input type="text" class="cmr-otp-code-input" maxlength="6" inputmode="numeric" placeholder="• • • • • •" autocomplete="one-time-code" />' +
                    '<span role="button" class="cmr-otp-confirm-btn">Verify</span>' +
                    '<span role="button" class="cmr-otp-resend-btn disabled">Resend (30s)</span>' +
                '</div>' +
                '<div class="cmr-otp-feedback" style="display:none;"></div>';

            if (insertTarget.nextSibling) {
                parentNode.insertBefore(otpBox, insertTarget.nextSibling);
            } else {
                parentNode.appendChild(otpBox);
            }

            return otpBox;
        }

        function showOtpFeedback(otpBox, msg, type) {
            if (!otpBox) return;
            var feedback = otpBox.querySelector('.cmr-otp-feedback');
            if (!feedback) return;
            feedback.className = 'cmr-otp-feedback cmr-' + type;
            feedback.textContent = msg;
            feedback.style.display = 'block';
        }

        function clearOtpFeedback(otpBox) {
            if (!otpBox) return;
            var feedback = otpBox.querySelector('.cmr-otp-feedback');
            if (!feedback) return;
            feedback.style.display = 'none';
            feedback.textContent = '';
        }

        function startResendTimer(otpBox) {
            if (!otpBox) return;
            var resendBtn = otpBox.querySelector('.cmr-otp-resend-btn');
            if (!resendBtn) return;

            var boxId = otpBox._resendId || (otpBox._resendId = Math.random().toString(36).slice(2));
            if (resendIntervals[boxId]) clearInterval(resendIntervals[boxId]);

            var seconds = 30;
            resendBtn.classList.add('disabled');
            resendBtn.textContent = 'Resend (' + seconds + 's)';

            resendIntervals[boxId] = setInterval(function() {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(resendIntervals[boxId]);
                    delete resendIntervals[boxId];
                    resendBtn.classList.remove('disabled');
                    resendBtn.textContent = 'Resend OTP';
                } else {
                    resendBtn.textContent = 'Resend (' + seconds + 's)';
                }
            }, 1000);
        }

        function updateFieldState(input) {
            if (!isEmailField(input)) return;

            var email = (input.value || '').trim().toLowerCase();
            var badgeContainer = getOrCreateBadgeContainer(input);
            var otpBox = getOrCreateOtpBox(input);

            // Remove unverified error notice if user is typing
            var formField = input.closest('.form-field, .cmr-input-group, p') || input.parentElement;
            if (formField && formField.parentNode) {
                var notices = formField.parentNode.querySelectorAll('.cmr-email-unverified-notice');
                notices.forEach(function(n) { n.remove(); });
            }
            if (input.parentElement) {
                var innerNotices = input.parentElement.querySelectorAll('.cmr-email-unverified-notice');
                innerNotices.forEach(function(n) { n.remove(); });
            }
            positionBadgeContainer(input, badgeContainer);

            // 1. Invalid or empty email
            if (!email || !isValidEmail(email)) {
                if (badgeContainer && badgeContainer.innerHTML !== '') {
                    badgeContainer.innerHTML = '';
                }
                if (otpBox) otpBox.style.setProperty('display', 'none', 'important');
                if (input.hasAttribute('data-email-verified')) {
                    input.removeAttribute('data-email-verified');
                }
                input.readOnly = false;
                input.style.removeProperty('padding-right');
                return;
            }

            // 2. Already verified email for this specific input
            if (input._cmrVerifiedEmail === email && input._cmrVerifiedToken) {
                if (otpBox) otpBox.style.setProperty('display', 'none', 'important');
                input.setAttribute('data-email-verified', 'true');
                input.readOnly = true;
                input.style.setProperty('padding-right', '135px', 'important');

                if (badgeContainer && !badgeContainer.querySelector('.cmr-verified-badge')) {
                    badgeContainer.innerHTML = '' +
                        '<span class="badge cmr-verified-badge"><svg viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> Verified</span>' +
                        '<span role="button" class="badge cmr-change-badge" title="Change email">Change</span>';
                }

                var parentForm = input.closest('form');
                if (parentForm) {
                    var hiddenToken = parentForm.querySelector('input[name="cmr_email_verified_token"]');
                    if (!hiddenToken) {
                        hiddenToken = document.createElement('input');
                        hiddenToken.type = 'hidden';
                        hiddenToken.name = 'cmr_email_verified_token';
                        parentForm.appendChild(hiddenToken);
                    }
                    hiddenToken.value = input._cmrVerifiedToken;
                }
                return;
            }

            // 3. Valid email, NOT verified -> Show "Verify Email" Bootstrap Badge
            if (input.hasAttribute('data-email-verified')) {
                input.removeAttribute('data-email-verified');
            }
            input.readOnly = false;
            input.style.setProperty('padding-right', '115px', 'important');

            if (badgeContainer && !badgeContainer.querySelector('.cmr-verify-badge')) {
                badgeContainer.innerHTML = '<span role="button" class="badge cmr-verify-badge">Verify Email</span>';
            }
        }

        // Send OTP
        function triggerSendOtp(input, isResend) {
            if (!input) return;
            var email = (input.value || '').trim();
            if (!isValidEmail(email)) {
                input.focus();
                return;
            }

            var badgeContainer = getOrCreateBadgeContainer(input);
            var otpBox = getOrCreateOtpBox(input);
            var vBadge = badgeContainer ? badgeContainer.querySelector('.cmr-verify-badge') : null;

            if (vBadge) {
                vBadge.textContent = 'Sending...';
                vBadge.classList.remove('cmr-pulse-attention');
            }

            var fd = new FormData();
            fd.append('action', 'cmr_send_email_otp');
            fd.append('email', email);
            fd.append('nonce', CMR_OTP_CONFIG.nonce);

            fetch(CMR_OTP_CONFIG.ajaxUrl, {
                method: 'POST',
                body: fd
            })
            .then(function(res) { return res.json(); })
            .then(function(res) {
                if (res.success) {
                    if (vBadge) vBadge.textContent = 'Code Sent';
                    if (otpBox) {
                        var destEmail = otpBox.querySelector('.cmr-otp-dest-email');
                        if (destEmail) destEmail.textContent = email;
                        otpBox.style.setProperty('display', 'block', 'important');
                        clearOtpFeedback(otpBox);
                        if (isResend) {
                            showOtpFeedback(otpBox, '✓ New verification code sent! Please check your inbox.', 'success');
                        }
                        startResendTimer(otpBox);

                        var codeInp = otpBox.querySelector('.cmr-otp-code-input');
                        if (codeInp) {
                            codeInp.value = '';
                            codeInp.focus();
                        }
                    }
                } else {
                    if (vBadge) vBadge.textContent = 'Verify Email';
                    alert(res.data.message || 'Failed to send OTP. Please try again.');
                }
            })
            .catch(function() {
                if (vBadge) vBadge.textContent = 'Verify Email';
                alert('Network error while sending OTP. Please try again.');
            });
        }

        // Verify OTP
        function triggerVerifyOtp(input, otpBox) {
            if (!input || !otpBox) return;
            var email = (input.value || '').trim();
            var codeInp = otpBox.querySelector('.cmr-otp-code-input');
            var confirmBtn = otpBox.querySelector('.cmr-otp-confirm-btn');
            var code = codeInp ? (codeInp.value || '').trim() : '';

            if (!code || code.length !== 6) {
                showOtpFeedback(otpBox, 'Please enter the complete 6-digit OTP code.', 'error');
                if (codeInp) {
                    codeInp.classList.add('cmr-shake');
                    setTimeout(function() { codeInp.classList.remove('cmr-shake'); }, 500);
                    codeInp.focus();
                }
                return;
            }

            if (confirmBtn) confirmBtn.textContent = 'Verifying...';
            clearOtpFeedback(otpBox);

            var fd = new FormData();
            fd.append('action', 'cmr_verify_email_otp');
            fd.append('email', email);
            fd.append('otp', code);
            fd.append('nonce', CMR_OTP_CONFIG.nonce);

            fetch(CMR_OTP_CONFIG.ajaxUrl, {
                method: 'POST',
                body: fd
            })
            .then(function(res) { return res.json(); })
            .then(function(res) {
                if (confirmBtn) confirmBtn.textContent = 'Verify';

                if (res.success) {
                    var cleanEmail = email.toLowerCase();
                    input._cmrVerifiedEmail = cleanEmail;
                    input._cmrVerifiedToken = res.data.token;
                    verifiedEmailTokens[cleanEmail] = res.data.token;
                    showOtpFeedback(otpBox, '✓ ' + (res.data.message || 'Email verified successfully!'), 'success');

                    setTimeout(function() {
                        otpBox.style.setProperty('display', 'none', 'important');
                        updateFieldState(input);
                    }, 600);
                } else {
                    showOtpFeedback(otpBox, res.data.message || 'Invalid OTP code. Please try again.', 'error');
                    if (codeInp) {
                        codeInp.classList.add('cmr-shake');
                        setTimeout(function() { codeInp.classList.remove('cmr-shake'); }, 500);
                    }
                }
            })
            .catch(function() {
                if (confirmBtn) confirmBtn.textContent = 'Verify';
                showOtpFeedback(otpBox, 'Network error. Please try again.', 'error');
            });
        }

        // Global Event Delegation for ALL Input & Change Events (NO focusin)
        ['input', 'change', 'keyup'].forEach(function(eventType) {
            document.addEventListener(eventType, function(e) {
                var target = e.target;
                if (isEmailField(target)) {
                    updateFieldState(target);
                }
            }, true);
        });

        // Global Event Delegation for OTP Code Input numeric limits
        document.addEventListener('input', function(e) {
            if (e.target && e.target.classList.contains('cmr-otp-code-input')) {
                var codeInp = e.target;
                var val = codeInp.value.replace(/\D/g, '').slice(0, 6);
                codeInp.value = val;
                if (val.length === 6) {
                    var box = codeInp.closest('.cmr-otp-box');
                    if (box && box._targetInput) {
                        triggerVerifyOtp(box._targetInput, box);
                    }
                }
            }
        }, true);

        // Global Event Delegation for Enter key in OTP input
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target && e.target.classList.contains('cmr-otp-code-input')) {
                e.preventDefault();
                var box = e.target.closest('.cmr-otp-box');
                if (box && box._targetInput) {
                    triggerVerifyOtp(box._targetInput, box);
                }
            }
        }, true);

        // Global Event Delegation for All Clicks (Verify, Change, Resend, Confirm)
        document.addEventListener('click', function(e) {
            // 1. Click "Verify Email" Badge or "Code Sent"
            var vBadge = e.target.closest('.cmr-verify-badge');
            if (vBadge) {
                e.preventDefault();
                e.stopPropagation();
                var badgeWrap = vBadge.closest('.cmr-email-badge-container');
                var fieldWrap = badgeWrap ? badgeWrap.parentElement : null;
                var input = (badgeWrap && badgeWrap._targetInput) || (fieldWrap ? fieldWrap.querySelector('input') : null);
                if (input) {
                    var otpBox = getOrCreateOtpBox(input);
                    if (otpBox && otpBox.style.display !== 'none') {
                        var codeInp = otpBox.querySelector('.cmr-otp-code-input');
                        if (codeInp) codeInp.focus();
                    } else {
                        triggerSendOtp(input, false);
                    }
                }
                return;
            }

            // 2. Click "Change" Badge
            var cBadge = e.target.closest('.cmr-change-badge');
            if (cBadge) {
                e.preventDefault();
                e.stopPropagation();
                var badgeWrap = cBadge.closest('.cmr-email-badge-container');
                var fieldWrap = badgeWrap ? badgeWrap.parentElement : null;
                var input = (badgeWrap && badgeWrap._targetInput) || (fieldWrap ? fieldWrap.querySelector('input') : null);
                if (input) {
                    var curEmail = (input.value || '').trim().toLowerCase();
                    delete verifiedEmailTokens[curEmail];
                    delete input._cmrVerifiedEmail;
                    delete input._cmrVerifiedToken;
                    input.readOnly = false;
                    input.removeAttribute('data-email-verified');

                    var parentForm = input.closest('form');
                    if (parentForm) {
                        var hToken = parentForm.querySelector('input[name="cmr_email_verified_token"]');
                        if (hToken) hToken.remove();
                    }

                    if (curEmail) {
                        try {
                            var fd = new FormData();
                            fd.append('action', 'cmr_consume_verified_email');
                            fd.append('email', curEmail);
                            fd.append('nonce', CMR_OTP_CONFIG.nonce);
                            fetch(CMR_OTP_CONFIG.ajaxUrl, { method: 'POST', body: fd });
                        } catch(err) {}
                    }

                    updateFieldState(input);
                    input.focus();
                    input.select();
                }
                return;
            }

            // 3. Click "Verify" OTP button inside box
            var confirmBtn = e.target.closest('.cmr-otp-confirm-btn');
            if (confirmBtn) {
                e.preventDefault();
                e.stopPropagation();
                var box = confirmBtn.closest('.cmr-otp-box');
                if (box && box._targetInput) {
                    triggerVerifyOtp(box._targetInput, box);
                }
                return;
            }

            // 4. Click "Resend OTP" button inside box
            var resendBtn = e.target.closest('.cmr-otp-resend-btn:not(.disabled)');
            if (resendBtn) {
                e.preventDefault();
                e.stopPropagation();
                var box = resendBtn.closest('.cmr-otp-box');
                if (box && box._targetInput) {
                    triggerSendOtp(box._targetInput, true);
                }
                return;
            }
        }, true);

        // Global Helper: Invalidate / Expire OTP verification status across page & server
        window.cmrInvalidateVerifiedEmail = function(email) {
            var targetEmail = email ? String(email).trim().toLowerCase() : '';

            if (targetEmail) {
                delete verifiedEmailTokens[targetEmail];
            } else {
                verifiedEmailTokens = {};
            }

            // Immediately call backend to invalidate transient on server
            try {
                var fd = new FormData();
                fd.append('action', 'cmr_consume_verified_email');
                if (targetEmail) fd.append('email', targetEmail);
                fd.append('nonce', CMR_OTP_CONFIG.nonce);
                fetch(CMR_OTP_CONFIG.ajaxUrl, { method: 'POST', body: fd });
            } catch(e) {}

            // Reset all email inputs matching this email (or all if targetEmail is empty)
            var inputs = document.querySelectorAll('input');
            inputs.forEach(function(inp) {
                if (isEmailField(inp)) {
                    var val = (inp.value || '').trim().toLowerCase();
                    if (!targetEmail || val === targetEmail || inp._cmrVerifiedEmail === targetEmail) {
                        delete inp._cmrVerifiedEmail;
                        delete inp._cmrVerifiedToken;
                        inp.removeAttribute('data-email-verified');
                        inp.readOnly = false;
                        inp.style.removeProperty('padding-right');

                        var parentForm = inp.closest('form');
                        if (parentForm) {
                            var hToken = parentForm.querySelector('input[name="cmr_email_verified_token"]');
                            if (hToken) hToken.remove();
                        }

                        var bc = parentForm ? parentForm.querySelector('.cmr-email-badge-container') : null;
                        if (bc) bc.innerHTML = '';

                        var ob = parentForm ? parentForm.querySelector('.cmr-otp-box') : null;
                        if (ob) ob.style.setProperty('display', 'none', 'important');

                        updateFieldState(inp);
                    }
                }
            });
        };

        // Listen for CF7 native successful submission to immediately expire verified status
        document.addEventListener('wpcf7mailsent', function(e) {
            var form = e.target;
            if (form) {
                var emailInp = form.querySelector('input[type="email"], input[name*="email"], input.wpcf7-email');
                var emailVal = emailInp ? (emailInp.value || '').trim().toLowerCase() : '';
                window.cmrInvalidateVerifiedEmail(emailVal);
            }
        });

        // Universal Scanner to initialize all email inputs in document
        function initAllEmailOtpFields() {
            var inputs = document.querySelectorAll(
                'input[type="email"], ' +
                'input[name="your-email"], ' +
                'input[name="email"], ' +
                'input[name="email-address"], ' +
                'input.wpcf7-email'
            );

            for (var i = 0; i < inputs.length; i++) {
                var inp = inputs[i];
                if (isEmailField(inp)) {
                    getOrCreateBadgeContainer(inp);
                    getOrCreateOtpBox(inp);
                    updateFieldState(inp);
                }
            }
        }

        // Run once on load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAllEmailOtpFields);
        } else {
            initAllEmailOtpFields();
        }
        window.addEventListener('load', initAllEmailOtpFields);

        // Run when Elementor popups open or Easy Login popups/tabs open
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('elementor/popup/show', function() {
                setTimeout(initAllEmailOtpFields, 60);
            });
            jQuery(document).on('xoo_el_popup_opened xoo_el_form_rendered xoo_el_tab_changed', function() {
                setTimeout(initAllEmailOtpFields, 50);
            });
        }
        document.addEventListener('click', function(e) {
            if (e.target.closest('.xoo-el-reg-tgr, .xoo-el-login-tgr, [data-tab="register"], a[href*="my-account"]')) {
                setTimeout(initAllEmailOtpFields, 60);
                setTimeout(initAllEmailOtpFields, 200);
            }
        });
    })();
    </script>
    <?php
}
