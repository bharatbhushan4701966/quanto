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

    // Generate secure verification token valid for 2 hours
    $token = wp_hash( strtolower( $email ) . '|' . wp_salt( 'auth' ) . '|' . date( 'Ymd' ) );
    $verified_key = 'cmr_ver_' . md5( strtolower( $email ) );
    set_transient( $verified_key, $token, 2 * HOUR_IN_SECONDS );

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
// 2. Contact Form 7 Backend Validation Filter
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

// =========================================================================
// 3. Branded HTML Email Dispatcher
// =========================================================================

function cmr_dispatch_otp_email( $to_email, $otp_code ) {
    $subject = 'Your Verification Code: ' . $otp_code . ' - CyberMedia Research';

    $site_name = get_bloginfo( 'name' ) ?: 'CyberMedia Research (CMR)';
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
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">CyberMedia Research (CMR)</h1>
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
                                &copy; ' . date( 'Y' ) . ' <a href="' . esc_url( $site_url ) . '" style="color: #0284c7; text-decoration: none;">CyberMedia Research &amp; Services Ltd.</a> All rights reserved.
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
    $from_name  = apply_filters( 'cmr_otp_from_name', 'CyberMedia Research (CMR)' );

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
    .wpcf7-form-control-wrap[data-name*="your-email"] {
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
    .custom-consultation-form .cmr-email-badge-container,
    .custom-subscribe-form .cmr-email-badge-container,
    .elementor-popup-modal .cmr-email-badge-container,
    .cmr-form .cmr-email-badge-container {
        position: absolute !important;
        right: 8px !important;
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

    /* Compact OTP Box below field */
    .cmr-otp-box,
    .custom-consultation-form .cmr-otp-box,
    .custom-subscribe-form .cmr-otp-box,
    .elementor-popup-modal .cmr-otp-box {
        display: none;
        width: 100% !important;
        max-width: 100% !important;
        background: #f8fafc !important;
        border: 1px solid #cfe2ff !important;
        border-radius: 8px !important;
        padding: 8px 12px !important;
        margin-top: 6px !important;
        margin-bottom: 8px !important;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.08) !important;
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
        justify-content: space-between !important;
        margin-bottom: 6px !important;
        font-size: 11.5px !important;
        color: #495057 !important;
        width: 100% !important;
    }

    .cmr-otp-box .cmr-otp-header-row .badge,
    .custom-consultation-form .cmr-otp-box .cmr-otp-header-row .badge,
    .custom-subscribe-form .cmr-otp-box .cmr-otp-header-row .badge {
        background-color: #cfe2ff !important;
        color: #084298 !important;
        font-size: 9.5px !important;
        font-weight: 700 !important;
        padding: 2px 6px !important;
        border-radius: 50rem !important;
        text-transform: uppercase !important;
        display: inline-block !important;
        width: auto !important;
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
        width: 125px !important;
        min-width: 125px !important;
        max-width: 125px !important;
        height: 32px !important;
        min-height: 32px !important;
        max-height: 32px !important;
        border: 1.5px solid #ced4da !important;
        border-radius: 6px !important;
        background: #ffffff !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        letter-spacing: 2px !important;
        text-align: center !important;
        color: #212529 !important;
        padding: 0 4px !important;
        outline: none !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        display: inline-block !important;
        flex: 0 0 125px !important;
    }

    .cmr-otp-box input.cmr-otp-code-input:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.15) !important;
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
        background-color: #0d6efd !important;
        background-image: none !important;
        color: #ffffff !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        height: 32px !important;
        min-height: 32px !important;
        max-height: 32px !important;
        padding: 0 12px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        border: none !important;
        box-shadow: 0 1px 2px rgba(13, 110, 253, 0.3) !important;
        text-transform: none !important;
        width: auto !important;
        margin: 0 !important;
        flex: 0 0 auto !important;
        white-space: nowrap !important;
    }

    .cmr-otp-box .cmr-otp-confirm-btn:hover {
        background-color: #0b5ed7 !important;
    }

    .cmr-otp-box .cmr-otp-resend-btn,
    .custom-consultation-form .cmr-otp-box .cmr-otp-resend-btn,
    .custom-subscribe-form .cmr-otp-box .cmr-otp-resend-btn,
    .elementor-popup-modal .cmr-otp-box .cmr-otp-resend-btn {
        background-color: #e9ecef !important;
        background-image: none !important;
        color: #495057 !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
        height: 32px !important;
        min-height: 32px !important;
        max-height: 32px !important;
        padding: 0 8px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        border: 1px solid #ced4da !important;
        text-transform: none !important;
        width: auto !important;
        margin: 0 !important;
        flex: 0 0 auto !important;
        white-space: nowrap !important;
    }

    .cmr-otp-box .cmr-otp-resend-btn.disabled {
        opacity: 0.55 !important;
        cursor: not-allowed !important;
    }

    .cmr-otp-feedback {
        font-size: 11.5px !important;
        margin-top: 5px !important;
        padding: 3px 6px !important;
        border-radius: 4px !important;
        line-height: 1.3 !important;
    }

    .cmr-otp-feedback.cmr-success {
        color: #0f5132 !important;
        background-color: #d1e7dd !important;
        border: 1px solid #badbcc !important;
    }

    .cmr-otp-feedback.cmr-error {
        color: #842029 !important;
        background-color: #f8d7da !important;
        border: 1px solid #f5c2c7 !important;
    }

    .cmr-otp-feedback.cmr-info {
        color: #084298 !important;
        background-color: #cff4fc !important;
        border: 1px solid #b6effb !important;
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

            var form = input.closest('form');
            if (form) {
                var formClass = form.className || '';
                var formId = form.id || '';
                if (formClass.indexOf('search') !== -1 ||
                    formClass.indexOf('woocommerce-checkout') !== -1 ||
                    formClass.indexOf('woocommerce-cart') !== -1 ||
                    formClass.indexOf('xoo-el') !== -1 ||
                    formId === 'loginform' ||
                    input.classList.contains('cmr-coupon-input') ||
                    input.name === 'coupon_code') {
                    return false;
                }
            }

            var name = (input.name || '').toLowerCase();
            var cls = (input.className || '').toLowerCase();

            return type === 'email' ||
                   name === 'your-email' ||
                   name === 'email' ||
                   name === 'email-address' ||
                   name.indexOf('email') !== -1 ||
                   cls.indexOf('wpcf7-email') !== -1;
        }

        function getOrCreateBadgeContainer(input) {
            var parent = input.parentElement;
            if (!parent) return null;

            parent.style.setProperty('position', 'relative', 'important');
            parent.style.setProperty('display', 'block', 'important');
            parent.style.setProperty('width', '100%', 'important');
            parent.style.setProperty('max-width', '100%', 'important');
            parent.style.setProperty('box-sizing', 'border-box', 'important');

            var badgeContainer = parent.querySelector('.cmr-email-badge-container');
            if (!badgeContainer) {
                badgeContainer = document.createElement('div');
                badgeContainer.className = 'cmr-email-badge-container';
                parent.appendChild(badgeContainer);
            }
            return badgeContainer;
        }

        function getOrCreateOtpBox(input) {
            var parent = input.parentElement;
            if (!parent) return null;

            var insertTarget = input.closest('.form-field, .cmr-input-group, p') || parent;
            var parentNode = insertTarget.parentNode;
            if (!parentNode) return null;

            var existing = insertTarget.nextElementSibling;
            if (existing && existing.classList.contains('cmr-otp-box')) {
                existing._targetInput = input;
                return existing;
            }

            var otpBox = document.createElement('div');
            otpBox.className = 'cmr-otp-box';
            otpBox.style.setProperty('display', 'none', 'important');
            otpBox._targetInput = input;
            otpBox.innerHTML = '' +
                '<div class="cmr-otp-header-row">' +
                    '<span><span class="badge">OTP Sent</span> Code sent to <strong class="cmr-otp-dest-email"></strong></span>' +
                '</div>' +
                '<div class="cmr-otp-controls-row">' +
                    '<input type="text" class="cmr-otp-code-input" maxlength="6" inputmode="numeric" placeholder="OTP" autocomplete="one-time-code" />' +
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
                var notice = formField.parentNode.querySelector('.cmr-email-unverified-notice');
                if (notice) notice.remove();
            }

            // 1. Invalid or empty email
            if (!email || !isValidEmail(email)) {
                if (badgeContainer) badgeContainer.innerHTML = '';
                if (otpBox) otpBox.style.setProperty('display', 'none', 'important');
                input.removeAttribute('data-email-verified');
                input.readOnly = false;
                input.style.removeProperty('padding-right');
                return;
            }

            // 2. Already verified email
            if (verifiedEmailTokens[email]) {
                if (otpBox) otpBox.style.setProperty('display', 'none', 'important');
                input.setAttribute('data-email-verified', 'true');
                input.readOnly = true;
                input.style.setProperty('padding-right', '135px', 'important');

                if (badgeContainer) {
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
                    hiddenToken.value = verifiedEmailTokens[email];
                }
                return;
            }

            // 3. Valid email, NOT verified -> Show "Verify Email" Bootstrap Badge
            input.removeAttribute('data-email-verified');
            input.readOnly = false;
            input.style.setProperty('padding-right', '115px', 'important');

            if (badgeContainer) {
                badgeContainer.innerHTML = '<span role="button" class="badge cmr-verify-badge">✉ Verify Email</span>';
            }
        }

        // Send OTP
        function triggerSendOtp(input) {
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
                    if (vBadge) vBadge.textContent = 'OTP Sent';
                    if (otpBox) {
                        var destEmail = otpBox.querySelector('.cmr-otp-dest-email');
                        if (destEmail) destEmail.textContent = email;
                        otpBox.style.setProperty('display', 'block', 'important');
                        clearOtpFeedback(otpBox);
                        showOtpFeedback(otpBox, res.data.message || 'OTP sent! Please check your email inbox.', 'info');
                        startResendTimer(otpBox);

                        var codeInp = otpBox.querySelector('.cmr-otp-code-input');
                        if (codeInp) {
                            codeInp.value = '';
                            codeInp.focus();
                        }
                    }
                } else {
                    if (vBadge) vBadge.textContent = '✉ Verify Email';
                    alert(res.data.message || 'Failed to send OTP. Please try again.');
                }
            })
            .catch(function() {
                if (vBadge) vBadge.textContent = '✉ Verify Email';
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
                    verifiedEmailTokens[email.toLowerCase()] = res.data.token;
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

        // Global Event Delegation for ALL Input & Change Events
        ['input', 'change', 'keyup', 'focusin'].forEach(function(eventType) {
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
            // 1. Click "Verify Email" Badge
            var vBadge = e.target.closest('.cmr-verify-badge');
            if (vBadge) {
                e.preventDefault();
                e.stopPropagation();
                var badgeWrap = vBadge.closest('.cmr-email-badge-container');
                var fieldWrap = badgeWrap ? badgeWrap.parentElement : null;
                var input = fieldWrap ? fieldWrap.querySelector('input') : null;
                if (input) triggerSendOtp(input);
                return;
            }

            // 2. Click "Change" Badge
            var cBadge = e.target.closest('.cmr-change-badge');
            if (cBadge) {
                e.preventDefault();
                e.stopPropagation();
                var badgeWrap = cBadge.closest('.cmr-email-badge-container');
                var fieldWrap = badgeWrap ? badgeWrap.parentElement : null;
                var input = fieldWrap ? fieldWrap.querySelector('input') : null;
                if (input) {
                    var curEmail = (input.value || '').trim().toLowerCase();
                    delete verifiedEmailTokens[curEmail];
                    input.readOnly = false;
                    input.removeAttribute('data-email-verified');
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
                    triggerSendOtp(box._targetInput);
                }
                return;
            }
        }, true);

        // Universal Scanner to initialize all email inputs in document
        function initAllEmailOtpFields() {
            var inputs = document.querySelectorAll(
                'input[type="email"], ' +
                'input[name="your-email"], ' +
                'input[name="email"], ' +
                'input[name="email-address"], ' +
                'input.wpcf7-email'
            );

            inputs.forEach(function(inp) {
                if (isEmailField(inp)) {
                    getOrCreateBadgeContainer(inp);
                    getOrCreateOtpBox(inp);
                    updateFieldState(inp);
                }
            });
        }

        // Execute immediately
        initAllEmailOtpFields();

        // Standard Lifecycle Hooks
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAllEmailOtpFields);
        }
        window.addEventListener('load', initAllEmailOtpFields);

        // Elementor Popup Hooks
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('elementor/popup/show', function() {
                setTimeout(initAllEmailOtpFields, 50);
                setTimeout(initAllEmailOtpFields, 250);
                setTimeout(initAllEmailOtpFields, 600);
            });
        }

        // Periodic Sync: Every 1.5s ensures even dynamically injected popups/AJAX forms are synced
        setInterval(initAllEmailOtpFields, 1500);

        // MutationObserver for dynamic DOM insertions
        if (window.MutationObserver) {
            var observer = new MutationObserver(function() {
                initAllEmailOtpFields();
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
    })();
    </script>
    <?php
}
