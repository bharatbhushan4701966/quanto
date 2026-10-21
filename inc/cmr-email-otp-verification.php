<?php
/**
 * CMR Universal Email OTP Verification System
 * 
 * Automatically attaches an OTP "Verify Email" workflow to all forms with an email field.
 * - Shows "Verify Email" button when user enters email
 * - Sends 6-digit OTP via clean CyberMedia Research branded HTML email
 * - Displays inline OTP input below email field
 * - On verification, hides OTP input and displays "Verified Email" badge in the email field
 * - Blocks form submission if email is not verified
 * - Backend validation via Contact Form 7 filter & helper functions
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
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); padding: 32px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">CyberMedia Research (CMR)</h1>
                            <p style="margin: 6px 0 0 0; color: #93c5fd; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">Email Verification</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 35px 35px 25px 35px;">
                            <p style="margin: 0 0 16px 0; font-size: 16px; line-height: 24px; color: #334155;">Hello,</p>
                            <p style="margin: 0 0 25px 0; font-size: 15px; line-height: 24px; color: #475569;">
                                Please use the following One-Time Password (OTP) to verify your email address on <strong>' . esc_html( $site_name ) . '</strong>:
                            </p>

                            <!-- OTP Box -->
                            <div style="text-align: center; margin: 30px 0; padding: 22px; background: #f8fafc; border: 2px dashed #0284c7; border-radius: 10px;">
                                <div style="font-size: 34px; font-weight: 800; letter-spacing: 8px; color: #0284c7; font-family: Consolas, Monaco, monospace; display: inline-block;">' . esc_html( $otp_code ) . '</div>
                                <div style="margin-top: 10px; font-size: 12px; color: #64748b; font-weight: 500;">Valid for 10 minutes</div>
                            </div>

                            <p style="margin: 25px 0 0 0; font-size: 14px; line-height: 22px; color: #64748b;">
                                If you did not request this verification code, please disregard this email. Your account remains completely secure.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
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

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: CyberMedia Research (CMR) <no-reply@cmrindia.com>',
    );

    return wp_mail( $to_email, $subject, $body, $headers );
}

// =========================================================================
// 4. Frontend Styles & JavaScript Controller
// =========================================================================

add_action( 'wp_footer', 'cmr_render_email_otp_frontend', 995 );

function cmr_render_email_otp_frontend() {
    $ajax_url = admin_url( 'admin-ajax.php' );
    $nonce    = wp_create_nonce( 'cmr_otp_nonce' );
    ?>
    <style id="cmr-email-otp-css">
    /* Email Field Wrapper & Verify Button */
    .cmr-email-field-wrapper {
        position: relative !important;
        display: block !important;
        width: 100% !important;
    }

    .cmr-email-field-wrapper input[type="email"] {
        width: 100% !important;
        box-sizing: border-box !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .cmr-email-field-wrapper.cmr-has-verify-btn input[type="email"] {
        padding-right: 110px !important;
    }

    .cmr-email-field-wrapper.cmr-is-verified input[type="email"] {
        padding-right: 135px !important;
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
        color: #065f46 !important;
    }

    /* "Verify Email" Button Inside Input */
    .cmr-verify-email-trigger-btn {
        position: absolute !important;
        right: 6px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        z-index: 10 !important;
        background: #0284c7 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 6px 12px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.3px !important;
        line-height: 1.2 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        box-shadow: 0 1px 3px rgba(2, 132, 199, 0.3) !important;
        transition: all 0.2s ease !important;
        white-space: nowrap !important;
        outline: none !important;
        height: auto !important;
        min-height: 28px !important;
        margin: 0 !important;
    }

    .cmr-verify-email-trigger-btn:hover {
        background: #0369a1 !important;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.45) !important;
        transform: translateY(-50%) scale(1.02) !important;
    }

    .cmr-verify-email-trigger-btn:active {
        transform: translateY(-50%) scale(0.98) !important;
    }

    .cmr-verify-email-trigger-btn:disabled {
        opacity: 0.65 !important;
        cursor: not-allowed !important;
        transform: translateY(-50%) !important;
    }

    .cmr-verify-email-trigger-btn.cmr-pulse-attention {
        animation: cmrBtnPulse 1.2s infinite !important;
        background: #dc2626 !important;
    }

    @keyframes cmrBtnPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
        50% { box-shadow: 0 0 0 6px rgba(220, 38, 38, 0); }
    }

    /* "Verified Email" Badge Inside Input */
    .cmr-email-verified-badge {
        position: absolute !important;
        right: 6px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        z-index: 10 !important;
        background: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
        border-radius: 6px !important;
        padding: 4px 8px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        white-space: nowrap !important;
        box-shadow: 0 1px 2px rgba(16, 185, 129, 0.15) !important;
    }

    .cmr-email-verified-badge svg {
        width: 13px !important;
        height: 13px !important;
        stroke: #059669 !important;
        stroke-width: 3 !important;
    }

    .cmr-email-change-btn {
        background: none !important;
        border: none !important;
        padding: 0 !important;
        margin-left: 4px !important;
        font-size: 10px !important;
        color: #64748b !important;
        text-decoration: underline !important;
        cursor: pointer !important;
        line-height: 1 !important;
    }

    .cmr-email-change-btn:hover {
        color: #0f172a !important;
    }

    /* OTP Container Box Below Email Field */
    .cmr-otp-box {
        margin-top: 8px !important;
        margin-bottom: 12px !important;
        background: #f8fafc !important;
        border: 1px solid #bfdbfe !important;
        border-radius: 8px !important;
        padding: 12px 14px !important;
        box-shadow: 0 2px 8px rgba(2, 132, 199, 0.08) !important;
        animation: cmrSlideDown 0.25s ease-out !important;
        box-sizing: border-box !important;
    }

    @keyframes cmrSlideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .cmr-otp-box-inner {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
    }

    .cmr-otp-box-title {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-size: 12px !important;
        color: #334155 !important;
        line-height: 1.4 !important;
    }

    .cmr-otp-box-title strong {
        color: #0284c7 !important;
        font-weight: 700 !important;
    }

    .cmr-otp-input-row {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }

    .cmr-otp-code-input {
        flex: 1 1 140px !important;
        max-width: 170px !important;
        height: 38px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 6px !important;
        background: #ffffff !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        letter-spacing: 4px !important;
        text-align: center !important;
        color: #0f172a !important;
        padding: 0 8px !important;
        outline: none !important;
        box-sizing: border-box !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .cmr-otp-code-input:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
    }

    .cmr-otp-code-input.cmr-shake {
        animation: cmrShake 0.4s ease-in-out !important;
        border-color: #ef4444 !important;
    }

    @keyframes cmrShake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-6px); }
        40%, 80% { transform: translateX(6px); }
    }

    .cmr-otp-confirm-btn {
        background: #0284c7 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 6px !important;
        height: 38px !important;
        padding: 0 16px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        white-space: nowrap !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .cmr-otp-confirm-btn:hover {
        background: #0369a1 !important;
    }

    .cmr-otp-confirm-btn:disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
    }

    .cmr-otp-resend-btn {
        background: none !important;
        border: none !important;
        color: #64748b !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        padding: 0 6px !important;
        height: 38px !important;
        text-decoration: underline !important;
        white-space: nowrap !important;
    }

    .cmr-otp-resend-btn:hover:not(:disabled) {
        color: #0284c7 !important;
    }

    .cmr-otp-resend-btn:disabled {
        opacity: 0.55 !important;
        cursor: not-allowed !important;
        text-decoration: none !important;
    }

    .cmr-otp-feedback {
        font-size: 11px !important;
        line-height: 1.4 !important;
        margin-top: 2px !important;
        padding: 4px 8px !important;
        border-radius: 4px !important;
    }

    .cmr-otp-feedback.cmr-success {
        color: #059669 !important;
        background: #ecfdf5 !important;
        border: 1px solid #a7f3d0 !important;
    }

    .cmr-otp-feedback.cmr-error {
        color: #dc2626 !important;
        background: #fef2f2 !important;
        border: 1px solid #fecaca !important;
    }

    .cmr-otp-feedback.cmr-info {
        color: #0369a1 !important;
        background: #f0f9ff !important;
        border: 1px solid #bae6fd !important;
    }

    /* Unverified Notice Prompt */
    .cmr-email-unverified-notice {
        font-size: 12px !important;
        color: #ef4444 !important;
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

        // Cache of verified emails in this user session
        var verifiedEmailTokens = {};

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(String(email).trim());
        }

        // Initialize OTP UI on an email input
        function setupEmailOtpField(input) {
            if (!input || input.getAttribute('data-cmr-otp-ready') === 'true') {
                return;
            }

            // Exclude search inputs, coupon inputs, login/register forms
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
                    return;
                }
            }

            input.setAttribute('data-cmr-otp-ready', 'true');

            // 1. Ensure input is inside a dedicated wrapper
            var wrapper = input.parentElement;
            if (!wrapper || !wrapper.classList.contains('cmr-email-field-wrapper')) {
                wrapper = document.createElement('div');
                wrapper.className = 'cmr-email-field-wrapper';
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);
            }

            // 2. Create "Verify Email" button
            var verifyBtn = wrapper.querySelector('.cmr-verify-email-trigger-btn');
            if (!verifyBtn) {
                verifyBtn = document.createElement('button');
                verifyBtn.type = 'button';
                verifyBtn.className = 'cmr-verify-email-trigger-btn';
                verifyBtn.style.display = 'none';
                verifyBtn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> Verify Email';
                wrapper.appendChild(verifyBtn);
            }

            // 3. Create "Verified Email" badge
            var verifiedBadge = wrapper.querySelector('.cmr-email-verified-badge');
            if (!verifiedBadge) {
                verifiedBadge = document.createElement('span');
                verifiedBadge.className = 'cmr-email-verified-badge';
                verifiedBadge.style.display = 'none';
                verifiedBadge.innerHTML = '<svg viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> Verified Email <button type="button" class="cmr-email-change-btn" title="Change email">Change</button>';
                wrapper.appendChild(verifiedBadge);
            }

            // 4. Create OTP input box below wrapper
            var otpBox = wrapper.nextElementSibling;
            if (!otpBox || !otpBox.classList.contains('cmr-otp-box')) {
                otpBox = document.createElement('div');
                otpBox.className = 'cmr-otp-box';
                otpBox.style.display = 'none';
                otpBox.innerHTML = '' +
                    '<div class="cmr-otp-box-inner">' +
                        '<div class="cmr-otp-box-title">' +
                            '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>' +
                            '<span>Verification code sent to <strong class="cmr-otp-dest-email"></strong></span>' +
                        '</div>' +
                        '<div class="cmr-otp-input-row">' +
                            '<input type="text" class="cmr-otp-code-input" maxlength="6" inputmode="numeric" pattern="[0-9]*" placeholder="Enter OTP" autocomplete="one-time-code" />' +
                            '<button type="button" class="cmr-otp-confirm-btn">Verify</button>' +
                            '<button type="button" class="cmr-otp-resend-btn" disabled>Resend in <span class="cmr-otp-timer">30</span>s</button>' +
                        '</div>' +
                        '<div class="cmr-otp-feedback" style="display:none;"></div>' +
                    '</div>';
                wrapper.parentNode.insertBefore(otpBox, wrapper.nextSibling);
            }

            var otpCodeInput   = otpBox.querySelector('.cmr-otp-code-input');
            var otpConfirmBtn  = otpBox.querySelector('.cmr-otp-confirm-btn');
            var otpResendBtn   = otpBox.querySelector('.cmr-otp-resend-btn');
            var otpTimerSpan   = otpBox.querySelector('.cmr-otp-timer');
            var otpFeedback    = otpBox.querySelector('.cmr-otp-feedback');
            var otpDestEmail   = otpBox.querySelector('.cmr-otp-dest-email');
            var changeEmailBtn = verifiedBadge.querySelector('.cmr-email-change-btn');

            var resendInterval = null;

            function showOtpFeedback(msg, type) {
                if (!otpFeedback) return;
                otpFeedback.className = 'cmr-otp-feedback cmr-' + type;
                otpFeedback.textContent = msg;
                otpFeedback.style.display = 'block';
            }

            function clearOtpFeedback() {
                if (!otpFeedback) return;
                otpFeedback.style.display = 'none';
                otpFeedback.textContent = '';
            }

            function startResendTimer() {
                if (resendInterval) clearInterval(resendInterval);
                var seconds = 30;
                otpResendBtn.disabled = true;
                otpResendBtn.innerHTML = 'Resend in <span class="cmr-otp-timer">' + seconds + '</span>s';

                resendInterval = setInterval(function() {
                    seconds--;
                    if (seconds <= 0) {
                        clearInterval(resendInterval);
                        otpResendBtn.disabled = false;
                        otpResendBtn.textContent = 'Resend OTP';
                    } else {
                        var tSpan = otpResendBtn.querySelector('.cmr-otp-timer');
                        if (tSpan) tSpan.textContent = seconds;
                    }
                }, 1000);
            }

            // Sync visual UI state based on value and verified cache
            function updateFieldState() {
                var email = (input.value || '').trim().toLowerCase();

                // Clear unverified notice if user is typing
                var notice = wrapper.parentNode ? wrapper.parentNode.querySelector('.cmr-email-unverified-notice') : null;
                if (notice) notice.remove();

                if (!email || !isValidEmail(email)) {
                    verifyBtn.style.display = 'none';
                    verifiedBadge.style.display = 'none';
                    wrapper.classList.remove('cmr-has-verify-btn');
                    wrapper.classList.remove('cmr-is-verified');
                    otpBox.style.display = 'none';
                    input.removeAttribute('data-email-verified');
                    input.readOnly = false;
                    return;
                }

                // If email is already verified in this session
                if (verifiedEmailTokens[email]) {
                    verifyBtn.style.display = 'none';
                    verifiedBadge.style.display = 'inline-flex';
                    wrapper.classList.remove('cmr-has-verify-btn');
                    wrapper.classList.add('cmr-is-verified');
                    otpBox.style.display = 'none';
                    input.setAttribute('data-email-verified', 'true');
                    input.readOnly = true;

                    // Ensure hidden token is set in form
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

                // Email is valid but NOT verified yet -> Show "Verify Email" button
                verifyBtn.style.display = 'inline-flex';
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> Verify Email';
                verifiedBadge.style.display = 'none';
                wrapper.classList.add('cmr-has-verify-btn');
                wrapper.classList.remove('cmr-is-verified');
                input.removeAttribute('data-email-verified');
                input.readOnly = false;
            }

            input.addEventListener('input', updateFieldState);
            input.addEventListener('change', updateFieldState);
            input.addEventListener('focus', function() {
                verifyBtn.classList.remove('cmr-pulse-attention');
            });

            // Action: Click "Verify Email" -> Send OTP
            function triggerSendOtp() {
                var email = (input.value || '').trim();
                if (!isValidEmail(email)) {
                    input.focus();
                    return;
                }

                verifyBtn.disabled = true;
                verifyBtn.textContent = 'Sending OTP...';
                verifyBtn.classList.remove('cmr-pulse-attention');

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
                        verifyBtn.textContent = 'OTP Sent';
                        if (otpDestEmail) otpDestEmail.textContent = email;
                        otpBox.style.display = 'block';
                        clearOtpFeedback();
                        showOtpFeedback(res.data.message || 'OTP sent! Please check your email inbox.', 'info');
                        startResendTimer();
                        if (otpCodeInput) {
                            otpCodeInput.value = '';
                            otpCodeInput.focus();
                        }
                    } else {
                        verifyBtn.disabled = false;
                        verifyBtn.textContent = 'Verify Email';
                        alert(res.data.message || 'Failed to send OTP. Please try again.');
                    }
                })
                .catch(function() {
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify Email';
                    alert('Network error while sending OTP. Please try again.');
                });
            }

            verifyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                triggerSendOtp();
            });

            // Action: Click "Resend OTP"
            otpResendBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                triggerSendOtp();
            });

            // Action: Click "Verify" OTP button
            function triggerVerifyOtp() {
                var email = (input.value || '').trim();
                var code = (otpCodeInput.value || '').trim();

                if (!code || code.length !== 6) {
                    showOtpFeedback('Please enter the complete 6-digit OTP code.', 'error');
                    otpCodeInput.classList.add('cmr-shake');
                    setTimeout(function() { otpCodeInput.classList.remove('cmr-shake'); }, 500);
                    otpCodeInput.focus();
                    return;
                }

                otpConfirmBtn.disabled = true;
                otpConfirmBtn.textContent = 'Verifying...';
                clearOtpFeedback();

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
                    otpConfirmBtn.disabled = false;
                    otpConfirmBtn.textContent = 'Verify';

                    if (res.success) {
                        // Store verified token in cache
                        verifiedEmailTokens[email.toLowerCase()] = res.data.token;

                        showOtpFeedback('✓ ' + (res.data.message || 'Email verified successfully!'), 'success');

                        setTimeout(function() {
                            otpBox.style.display = 'none';
                            updateFieldState();
                        }, 700);
                    } else {
                        showOtpFeedback(res.data.message || 'Invalid OTP code. Please try again.', 'error');
                        otpCodeInput.classList.add('cmr-shake');
                        setTimeout(function() { otpCodeInput.classList.remove('cmr-shake'); }, 500);
                    }
                })
                .catch(function() {
                    otpConfirmBtn.disabled = false;
                    otpConfirmBtn.textContent = 'Verify';
                    showOtpFeedback('Network error. Please try again.', 'error');
                });
            }

            otpConfirmBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                triggerVerifyOtp();
            });

            // Allow pressing Enter in OTP input to trigger verify
            otpCodeInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    triggerVerifyOtp();
                }
            });

            // Auto-verify if user enters 6 digits
            otpCodeInput.addEventListener('input', function() {
                var val = this.value.replace(/\D/g, '').slice(0, 6);
                this.value = val;
                if (val.length === 6) {
                    triggerVerifyOtp();
                }
            });

            // Action: Click "Change" -> reset verification to enter a new email
            changeEmailBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var curEmail = (input.value || '').trim().toLowerCase();
                delete verifiedEmailTokens[curEmail];
                input.readOnly = false;
                input.removeAttribute('data-email-verified');
                updateFieldState();
                input.focus();
                input.select();
            });

            // Initial state check
            updateFieldState();
        }

        // Scan page and bind OTP controls to all email fields
        function initAllEmailOtpFields() {
            var emailFields = document.querySelectorAll(
                'input[type="email"]:not([data-cmr-otp-ready]), ' +
                'input[name="your-email"]:not([data-cmr-otp-ready]), ' +
                'input[name="email-address"]:not([data-cmr-otp-ready]), ' +
                'input[name="email"]:not([type="hidden"]):not([data-cmr-otp-ready])'
            );

            emailFields.forEach(function(input) {
                setupEmailOtpField(input);
            });
        }

        // Initialize on DOM load and dynamic popup events
        document.addEventListener('DOMContentLoaded', initAllEmailOtpFields);
        window.addEventListener('load', initAllEmailOtpFields);
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('elementor/popup/show', function() {
                setTimeout(initAllEmailOtpFields, 100);
                setTimeout(initAllEmailOtpFields, 300);
            });
        }

        // MutationObserver to catch dynamically injected popups/forms
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
