<?php
/**
 * CMR Form Submissions Database Storage & IP Rate Limiter
 * 
 * Features:
 * 1. Dedicated Database Storage for EVERY form submission (Name, Email, Phone, Company, Message, All Fields JSON, IP, Date/Time).
 * 2. Per-form IP Rate Limiter (Max 3 submissions per IP per form within 24 hours).
 * 3. 4th submission automatically rejected and logged.
 * 4. Admin Dashboard under Tools -> Form Submissions & Rate Limits with CSV Export.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CMR_FORM_RATE_LIMIT_MAX', 3 );
define( 'CMR_FORM_RATE_LIMIT_WINDOW_HOURS', 24 );

// 1. Initialize Database Tables
add_action( 'init', 'cmr_init_form_database_tables' );
function cmr_init_form_database_tables() {
    global $wpdb;
    $limits_table = $wpdb->prefix . 'cmr_form_rate_limits';
    $records_table = $wpdb->prefix . 'cmr_form_submissions';

    if ( get_option( 'cmr_form_submissions_db_ver' ) !== '2.0' ) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset_collate = $wpdb->get_charset_collate();

        // Table 1: Rate Limiter & IP Tracking
        $sql1 = "CREATE TABLE {$limits_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id VARCHAR(100) NOT NULL,
            form_title VARCHAR(255) DEFAULT '',
            ip_address VARCHAR(64) NOT NULL,
            user_name VARCHAR(150) DEFAULT '',
            user_email VARCHAR(150) DEFAULT '',
            user_phone VARCHAR(50) DEFAULT '',
            submission_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
            blocked_count INT(11) UNSIGNED NOT NULL DEFAULT 0,
            first_submission_at DATETIME NOT NULL,
            last_submission_at DATETIME NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'allowed',
            PRIMARY KEY (id),
            UNIQUE KEY form_ip (form_id, ip_address),
            KEY ip_address (ip_address),
            KEY last_submission_at (last_submission_at),
            KEY status (status)
        ) {$charset_collate};";
        dbDelta( $sql1 );

        // Table 2: Complete Submissions Archive (Full Form Entry Details)
        $sql2 = "CREATE TABLE {$records_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id VARCHAR(100) NOT NULL,
            form_title VARCHAR(255) DEFAULT '',
            user_name VARCHAR(255) DEFAULT '',
            user_email VARCHAR(255) DEFAULT '',
            user_phone VARCHAR(100) DEFAULT '',
            company_name VARCHAR(255) DEFAULT '',
            user_message TEXT,
            all_fields_json LONGTEXT,
            ip_address VARCHAR(64) NOT NULL,
            user_agent VARCHAR(500) DEFAULT '',
            submitted_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY form_id (form_id),
            KEY user_email (user_email),
            KEY ip_address (ip_address),
            KEY submitted_at (submitted_at)
        ) {$charset_collate};";
        dbDelta( $sql2 );

        update_option( 'cmr_form_submissions_db_ver', '2.0' );
    }
}

// 2. Safe Client IP Detection (Supports Cloudflare, Proxies & IPv4/IPv6)
function cmr_get_client_ip_address() {
    $ip = '';
    if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        $parts = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
        $ip = trim( $parts[0] );
    } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $ip = trim( $ip );
    if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
        return $ip;
    }
    return '0.0.0.0';
}

// 3. Save Complete Form Submission to Database (Archive Table)
function cmr_save_full_form_submission( $form_id, $form_title, $fields, $ip ) {
    global $wpdb;
    $table = $wpdb->prefix . 'cmr_form_submissions';

    $name = $fields['your-name'] ?? $fields['name'] ?? $fields['full_name'] ?? $fields['first_name'] ?? '';
    $email = $fields['your-email'] ?? $fields['email'] ?? $fields['work_email'] ?? '';
    $phone = $fields['your-phone'] ?? $fields['phone'] ?? $fields['tel'] ?? $fields['mobile'] ?? '';
    $company = $fields['your-company'] ?? $fields['company'] ?? $fields['organization'] ?? '';
    $message = $fields['your-message'] ?? $fields['message'] ?? $fields['requirement'] ?? $fields['comments'] ?? '';

    // Filter out internal tokens from JSON
    $clean_json = array();
    foreach ( (array)$fields as $k => $v ) {
        if ( strpos( $k, '_wpcf7' ) === 0 || $k === '_wpnonce' ) continue;
        $clean_json[$k] = is_string( $v ) ? sanitize_text_field( $v ) : $v;
    }

    $wpdb->insert(
        $table,
        array(
            'form_id'         => sanitize_text_field( $form_id ),
            'form_title'      => sanitize_text_field( $form_title ),
            'user_name'       => sanitize_text_field( $name ),
            'user_email'      => sanitize_email( $email ),
            'user_phone'      => sanitize_text_field( $phone ),
            'company_name'    => sanitize_text_field( $company ),
            'user_message'    => sanitize_textarea_field( $message ),
            'all_fields_json' => wp_json_encode( $clean_json ),
            'ip_address'      => $ip,
            'user_agent'      => substr( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ), 0, 500 ),
            'submitted_at'    => current_time( 'mysql' ),
        ),
        array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
    );

    return $wpdb->insert_id;
}

// 4. Check Rate Limit For Specific Form and IP (Dry Run / Validation)
function cmr_check_form_rate_limit( $form_id, $ip ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cmr_form_rate_limits';

    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE form_id = %s AND ip_address = %s",
        $form_id,
        $ip
    ) );

    if ( ! $row ) {
        return array(
            'allowed'           => true,
            'current_count'     => 0,
            'remaining'         => CMR_FORM_RATE_LIMIT_MAX,
            'blocked_count'     => 0,
            'is_first_in_window'=> true,
        );
    }

    $now = current_time( 'timestamp' );
    $first_sub_time = strtotime( $row->first_submission_at );
    $seconds_since_first = $now - $first_sub_time;
    $window_seconds = CMR_FORM_RATE_LIMIT_WINDOW_HOURS * 3600;

    // If 24 hours have passed, window resets
    if ( $seconds_since_first >= $window_seconds ) {
        return array(
            'allowed'           => true,
            'current_count'     => 0,
            'remaining'         => CMR_FORM_RATE_LIMIT_MAX,
            'blocked_count'     => 0,
            'is_first_in_window'=> true,
        );
    }

    // Within 24-hour window: check count
    $count = intval( $row->submission_count );
    if ( $count >= CMR_FORM_RATE_LIMIT_MAX ) {
        return array(
            'allowed'           => false,
            'current_count'     => $count,
            'remaining'         => 0,
            'blocked_count'     => intval( $row->blocked_count ),
            'is_first_in_window'=> false,
        );
    }

    return array(
        'allowed'           => true,
        'current_count'     => $count,
        'remaining'         => CMR_FORM_RATE_LIMIT_MAX - $count,
        'blocked_count'     => intval( $row->blocked_count ),
        'is_first_in_window'=> false,
    );
}

// 5. Record Blocked Attempt in Database
function cmr_record_blocked_submission( $form_id, $form_title, $ip, $user_name = '', $user_email = '', $user_phone = '' ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cmr_form_rate_limits';
    $now = current_time( 'mysql' );

    $wpdb->query( $wpdb->prepare(
        "INSERT INTO {$table_name} (form_id, form_title, ip_address, user_name, user_email, user_phone, submission_count, blocked_count, first_submission_at, last_submission_at, status)
         VALUES (%s, %s, %s, %s, %s, %s, %d, 1, %s, %s, 'blocked')
         ON DUPLICATE KEY UPDATE 
            blocked_count = blocked_count + 1,
            last_submission_at = %s,
            status = 'blocked',
            form_title = IF(%s != '', %s, form_title),
            user_name = IF(%s != '', %s, user_name),
            user_email = IF(%s != '', %s, user_email),
            user_phone = IF(%s != '', %s, user_phone)",
        $form_id,
        $form_title,
        $ip,
        $user_name,
        $user_email,
        $user_phone,
        CMR_FORM_RATE_LIMIT_MAX,
        $now,
        $now,
        $now,
        $form_title,
        $form_title,
        $user_name,
        $user_name,
        $user_email,
        $user_email,
        $user_phone,
        $user_phone
    ) );
}

// 6. Increment Successful Submission in Rate Limiter Table
function cmr_record_successful_submission( $form_id, $form_title, $ip, $user_name = '', $user_email = '', $user_phone = '' ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cmr_form_rate_limits';
    $now = current_time( 'mysql' );
    $check = cmr_check_form_rate_limit( $form_id, $ip );

    if ( $check['is_first_in_window'] ) {
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO {$table_name} (form_id, form_title, ip_address, user_name, user_email, user_phone, submission_count, blocked_count, first_submission_at, last_submission_at, status)
             VALUES (%s, %s, %s, %s, %s, %s, 1, 0, %s, %s, 'allowed')
             ON DUPLICATE KEY UPDATE 
                submission_count = 1,
                blocked_count = 0,
                first_submission_at = %s,
                last_submission_at = %s,
                status = 'allowed',
                form_title = IF(%s != '', %s, form_title),
                user_name = IF(%s != '', %s, user_name),
                user_email = IF(%s != '', %s, user_email),
                user_phone = IF(%s != '', %s, user_phone)",
            $form_id,
            $form_title,
            $ip,
            $user_name,
            $user_email,
            $user_phone,
            $now,
            $now,
            $now,
            $now,
            $form_title,
            $form_title,
            $user_name,
            $user_name,
            $user_email,
            $user_email,
            $user_phone,
            $user_phone
        ) );
    } else {
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table_name}
             SET submission_count = submission_count + 1,
                 last_submission_at = %s,
                 status = IF(submission_count + 1 >= %d, 'limit_reached', 'allowed'),
                 form_title = IF(%s != '', %s, form_title),
                 user_name = IF(%s != '', %s, user_name),
                 user_email = IF(%s != '', %s, user_email),
                 user_phone = IF(%s != '', %s, user_phone)
             WHERE form_id = %s AND ip_address = %s",
            $now,
            CMR_FORM_RATE_LIMIT_MAX,
            $form_title,
            $form_title,
            $user_name,
            $user_name,
            $user_email,
            $user_email,
            $user_phone,
            $user_phone,
            $form_id,
            $ip
        ) );
    }
}

// 7. Contact Form 7 Integration
add_filter( 'wpcf7_validate', 'cmr_cf7_rate_limit_validator', 5, 2 );
function cmr_cf7_rate_limit_validator( $result, $tags ) {
    $contact_form = class_exists( 'WPCF7_ContactForm' ) ? WPCF7_ContactForm::get_current() : null;
    if ( ! $contact_form ) {
        return $result;
    }

    $form_id = (string) $contact_form->id();
    $form_title = $contact_form->title();
    $ip = cmr_get_client_ip_address();

    $check = cmr_check_form_rate_limit( $form_id, $ip );

    if ( ! $check['allowed'] ) {
        $submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;
        $user_name = ''; $user_email = ''; $user_phone = '';
        if ( $submission ) {
            $posted = $submission->get_posted_data();
            $user_name = $posted['your-name'] ?? $posted['name'] ?? $posted['full_name'] ?? '';
            $user_email = $posted['your-email'] ?? $posted['email'] ?? '';
            $user_phone = $posted['your-phone'] ?? $posted['phone'] ?? $posted['tel'] ?? '';
        }

        cmr_record_blocked_submission( $form_id, $form_title, $ip, $user_name, $user_email, $user_phone );

        $error_msg = 'You have reached the maximum limit of 3 submissions for this form in 24 hours. Please try again tomorrow.';

        if ( ! empty( $tags ) && is_array( $tags ) && isset( $tags[0] ) ) {
            $result->invalidate( $tags[0], $error_msg );
        } else {
            $result->invalidate( '', $error_msg );
        }
    }

    return $result;
}

// Save Full Submission and Update Rate Limiter on valid submission
add_action( 'wpcf7_before_send_mail', 'cmr_cf7_record_submission_action', 10, 3 );
function cmr_cf7_record_submission_action( $contact_form, &$abort, $submission ) {
    if ( $abort || ! $contact_form ) {
        return;
    }

    $form_id = (string) $contact_form->id();
    $form_title = $contact_form->title();
    $ip = cmr_get_client_ip_address();

    static $recorded_requests = array();
    $req_key = $form_id . '_' . $ip;
    if ( isset( $recorded_requests[ $req_key ] ) ) {
        return;
    }
    $recorded_requests[ $req_key ] = true;

    $posted = array();
    if ( $submission ) {
        $posted = $submission->get_posted_data();
    }

    $name = $posted['your-name'] ?? $posted['name'] ?? $posted['full_name'] ?? '';
    $email = $posted['your-email'] ?? $posted['email'] ?? '';
    $phone = $posted['your-phone'] ?? $posted['phone'] ?? $posted['tel'] ?? '';

    // 1. Save Full Lead Record in Database Table (wp_cmr_form_submissions)
    cmr_save_full_form_submission( $form_id, $form_title, $posted, $ip );

    // 2. Increment Counter in Rate Limit Table (wp_cmr_form_rate_limits)
    cmr_record_successful_submission( $form_id, $form_title, $ip, $name, $email, $phone );
}

// Disable CF7 Submit Button directly if 24h limit reached for current IP
add_filter( 'wpcf7_form_elements', 'cmr_cf7_disable_submit_if_limit_reached' );
function cmr_cf7_disable_submit_if_limit_reached( $content ) {
    $contact_form = class_exists( 'WPCF7_ContactForm' ) ? WPCF7_ContactForm::get_current() : null;
    if ( ! $contact_form ) {
        return $content;
    }

    $form_id = (string) $contact_form->id();
    $ip = cmr_get_client_ip_address();
    $check = cmr_check_form_rate_limit( $form_id, $ip );

    if ( ! $check['allowed'] ) {
        $notice = '<div class="cmr-limit-notice" style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:13px; font-weight:600; text-align:center; margin-top:12px; line-height:1.4;">⚠️ You have reached the maximum daily limit of 3 submissions for this form. Please try again after 24 hours.</div>';
        
        // Disable input[type="submit"]
        $content = preg_replace(
            '/<input([^>]*?)type=["\']submit["\']([^>]*?)value=["\']([^"\']*)["\']([^>]*?)>/i',
            '<input$1type="submit"$2value="Daily Limit Reached (3/3)"$4 disabled="disabled" class="cmr-limit-disabled wpcf7-submit" style="opacity:0.55!important; cursor:not-allowed!important; pointer-events:none!important; background:#9ca3af!important; border-color:#9ca3af!important; filter:grayscale(1)!important;" title="Maximum daily limit reached">' . $notice,
            $content
        );
        // Disable button[type="submit"]
        $content = preg_replace(
            '/<button([^>]*?)type=["\']submit["\']([^>]*?)>(.*?)<\/button>/i',
            '<button$1type="submit"$2 disabled="disabled" class="cmr-limit-disabled" style="opacity:0.55!important; cursor:not-allowed!important; pointer-events:none!important; background:#9ca3af!important; border-color:#9ca3af!important; filter:grayscale(1)!important;" title="Maximum daily limit reached">Daily Limit Reached (3/3)</button>' . $notice,
            $content
        );
    }

    return $content;
}

// Append Rate Limit Details to Contact Form 7 JSON Response
add_filter( 'wpcf7_feedback_response', 'cmr_cf7_append_rate_limit_info', 10, 2 );
function cmr_cf7_append_rate_limit_info( $response, $result ) {
    $contact_form = class_exists( 'WPCF7_ContactForm' ) ? WPCF7_ContactForm::get_current() : null;
    if ( $contact_form ) {
        $form_id = (string) $contact_form->id();
        $ip = cmr_get_client_ip_address();
        $check = cmr_check_form_rate_limit( $form_id, $ip );
        $response['cmr_rate_limit'] = array(
            'form_id' => $form_id,
            'count'   => $check['current_count'],
            'max'     => CMR_FORM_RATE_LIMIT_MAX,
            'blocked' => ( $check['current_count'] >= CMR_FORM_RATE_LIMIT_MAX ),
        );
    }
    return $response;
}

// Inject Current IP's Form Limits into Frontend Footer
add_action( 'wp_footer', 'cmr_inject_rate_limits_to_footer', 5 );
function cmr_inject_rate_limits_to_footer() {
    global $wpdb;
    $table = $wpdb->prefix . 'cmr_form_rate_limits';
    $ip = cmr_get_client_ip_address();
    $cutoff = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) - ( CMR_FORM_RATE_LIMIT_WINDOW_HOURS * 3600 ) );

    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT form_id, submission_count, blocked_count, first_submission_at 
         FROM {$table} 
         WHERE ip_address = %s AND first_submission_at >= %s",
        $ip,
        $cutoff
    ) );

    $limits_data = array();
    foreach ( (array)$rows as $r ) {
        $count = intval( $r->submission_count );
        $limits_data[ (string)$r->form_id ] = array(
            'count'   => $count,
            'max'     => CMR_FORM_RATE_LIMIT_MAX,
            'blocked' => ( $count >= CMR_FORM_RATE_LIMIT_MAX || intval( $r->blocked_count ) > 0 ),
        );
    }
    ?>
    <script id="cmr-rate-limits-config">
        window.CMR_RATE_LIMITS = <?php echo wp_json_encode( $limits_data ); ?>;
        window.CMR_RATE_LIMIT_MAX = <?php echo CMR_FORM_RATE_LIMIT_MAX; ?>;
    </script>
    <?php
}

// 8. Elementor Pro Form Integration
add_action( 'elementor_pro/forms/validation', 'cmr_elementor_rate_limit_validator', 5, 2 );
function cmr_elementor_rate_limit_validator( $record, $ajax_handler ) {
    $form_id = $record->get_form_settings( 'form_id' ) ?: $record->get_form_settings( 'id' ) ?: 'elementor_form';
    $form_title = $record->get_form_settings( 'form_name' ) ?: 'Elementor Form';
    $ip = cmr_get_client_ip_address();

    $check = cmr_check_form_rate_limit( $form_id, $ip );

    if ( ! $check['allowed'] ) {
        $fields = $record->get( 'fields' );
        $user_name = ''; $user_email = ''; $user_phone = '';
        foreach ( (array)$fields as $fid => $f ) {
            if ( stripos( $fid, 'name' ) !== false && ! $user_name ) $user_name = $f['value'] ?? '';
            if ( stripos( $fid, 'email' ) !== false && ! $user_email ) $user_email = $f['value'] ?? '';
            if ( ( stripos( $fid, 'phone' ) !== false || stripos( $fid, 'tel' ) !== false ) && ! $user_phone ) $user_phone = $f['value'] ?? '';
        }

        cmr_record_blocked_submission( $form_id, $form_title, $ip, $user_name, $user_email, $user_phone );

        $ajax_handler->add_error_message( 'You have reached the maximum limit of 3 submissions for this form in 24 hours. Please try again tomorrow.' );
        $ajax_handler->is_success = false;
    }
}

add_action( 'elementor_pro/forms/new_record', 'cmr_elementor_record_submission_action', 10, 2 );
function cmr_elementor_record_submission_action( $record, $ajax_handler ) {
    if ( ! empty( $ajax_handler->errors ) ) {
        return;
    }
    $form_id = $record->get_form_settings( 'form_id' ) ?: $record->get_form_settings( 'id' ) ?: 'elementor_form';
    $form_title = $record->get_form_settings( 'form_name' ) ?: 'Elementor Form';
    $ip = cmr_get_client_ip_address();

    $fields = $record->get( 'fields' );
    $flat_fields = array();
    $user_name = ''; $user_email = ''; $user_phone = '';
    foreach ( (array)$fields as $fid => $f ) {
        $val = $f['value'] ?? '';
        $flat_fields[$fid] = $val;
        if ( stripos( $fid, 'name' ) !== false && ! $user_name ) $user_name = $val;
        if ( stripos( $fid, 'email' ) !== false && ! $user_email ) $user_email = $val;
        if ( ( stripos( $fid, 'phone' ) !== false || stripos( $fid, 'tel' ) !== false ) && ! $user_phone ) $user_phone = $val;
    }

    cmr_save_full_form_submission( $form_id, $form_title, $flat_fields, $ip );
    cmr_record_successful_submission( $form_id, $form_title, $ip, $user_name, $user_email, $user_phone );
}

// 9. Job Application Form Integration Hook
function cmr_job_form_check_rate_limit() {
    $form_id = 'job_application';
    $form_title = 'Job Application Form';
    $ip = cmr_get_client_ip_address();

    $check = cmr_check_form_rate_limit( $form_id, $ip );
    if ( ! $check['allowed'] ) {
        $user_name = sanitize_text_field( $_POST['full_name'] ?? '' );
        $user_email = sanitize_email( $_POST['email'] ?? '' );
        $user_phone = sanitize_text_field( $_POST['phone'] ?? '' );
        cmr_record_blocked_submission( $form_id, $form_title, $ip, $user_name, $user_email, $user_phone );
        return false;
    }
    return true;
}

function cmr_job_form_record_success() {
    $form_id = 'job_application';
    $form_title = 'Job Application Form';
    $ip = cmr_get_client_ip_address();
    $user_name = sanitize_text_field( $_POST['full_name'] ?? '' );
    $user_email = sanitize_email( $_POST['email'] ?? '' );
    $user_phone = sanitize_text_field( $_POST['phone'] ?? '' );
    
    $clean_post = $_POST;
    unset( $clean_post['action'], $clean_post['_wpnonce'] );
    cmr_save_full_form_submission( $form_id, $form_title, $clean_post, $ip );
    cmr_record_successful_submission( $form_id, $form_title, $ip, $user_name, $user_email, $user_phone );
}

// 10. Admin Dashboard UI & CSV Export (Tools -> Form Submissions & Rate Limits)
// Helper to build SQL WHERE clause for submissions filtering
function cmr_build_submissions_where_clause( $filter_form, $start_date, $end_date, $search ) {
    global $wpdb;
    $clauses = array();
    $params  = array();

    if ( ! empty( $filter_form ) ) {
        $clauses[] = "form_id = %s";
        $params[]  = $filter_form;
    }

    $valid_start = ! empty( $start_date ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $start_date );
    $valid_end   = ! empty( $end_date ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $end_date );

    if ( $valid_start && $valid_end ) {
        $clauses[] = "submitted_at >= %s AND submitted_at <= %s";
        $params[]  = $start_date . ' 00:00:00';
        $params[]  = $end_date . ' 23:59:59';
    } elseif ( $valid_start ) {
        // Single date selected - filters that entire day
        $clauses[] = "submitted_at >= %s AND submitted_at <= %s";
        $params[]  = $start_date . ' 00:00:00';
        $params[]  = $start_date . ' 23:59:59';
    } elseif ( $valid_end ) {
        $clauses[] = "submitted_at <= %s";
        $params[]  = $end_date . ' 23:59:59';
    }

    if ( ! empty( $search ) ) {
        $like = '%' . $wpdb->esc_like( $search ) . '%';
        $clauses[] = "(user_name LIKE %s OR user_email LIKE %s OR user_phone LIKE %s OR company_name LIKE %s OR user_message LIKE %s OR ip_address LIKE %s OR form_title LIKE %s)";
        for ( $i = 0; $i < 7; $i++ ) {
            $params[] = $like;
        }
    }

    if ( empty( $clauses ) ) {
        return '';
    }

    $sql = 'WHERE ' . implode( ' AND ', $clauses );
    if ( ! empty( $params ) ) {
        $sql = $wpdb->prepare( $sql, $params );
    }
    return $sql;
}

// 10. Admin Dashboard UI & CSV Export (Tools -> Form Submissions & Rate Limits)
add_action( 'admin_init', 'cmr_handle_submissions_csv_export' );
function cmr_handle_submissions_csv_export() {
    if ( isset( $_GET['cmr_export_csv'] ) && current_user_can( 'manage_options' ) ) {
        check_admin_referer( 'cmr_export_csv_nonce' );
        global $wpdb;
        $table = $wpdb->prefix . 'cmr_form_submissions';

        $filter_form = isset( $_GET['filter_form'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_form'] ) ) : '';
        $start_date  = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
        $end_date    = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';
        $search      = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

        $where_sql = cmr_build_submissions_where_clause( $filter_form, $start_date, $end_date, $search );
        $rows = $wpdb->get_results( "SELECT * FROM {$table} {$where_sql} ORDER BY submitted_at DESC" );

        $filename_parts = array( 'cmr-submissions' );
        if ( ! empty( $filter_form ) ) {
            $filename_parts[] = 'form-' . sanitize_title( $filter_form );
        }
        if ( ! empty( $start_date ) ) {
            $filename_parts[] = $start_date;
        }
        $filename = implode( '-', $filename_parts ) . '-' . date( 'Y-m-d' ) . '.csv';

        header( 'Content-Type: text/csv; charset=UTF-8' );
        header( 'Content-Disposition: attachment; filename=' . $filename );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $out = fopen( 'php://output', 'w' );
        fputs( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM for Excel compatibility
        fputcsv( $out, array( 'ID', 'Submitted At (IST)', 'Form ID', 'Form Name', 'Full Name', 'Email', 'Phone', 'Company', 'Message', 'IP Address' ) );

        foreach ( (array)$rows as $r ) {
            fputcsv( $out, array(
                $r->id,
                $r->submitted_at,
                $r->form_id,
                $r->form_title,
                $r->user_name,
                $r->user_email,
                $r->user_phone,
                $r->company_name,
                $r->user_message,
                $r->ip_address,
            ) );
        }
        fclose( $out );
        exit;
    }
}

add_action( 'admin_menu', 'cmr_register_form_rate_limit_admin_menu' );
function cmr_register_form_rate_limit_admin_menu() {
    add_management_page(
        'Form Submissions & Limits',
        'Form Submissions & Limits',
        'manage_options',
        'cmr-form-rate-limits',
        'cmr_render_form_rate_limit_admin_page'
    );
}

function cmr_render_form_rate_limit_admin_page() {
    global $wpdb;
    $limits_table = $wpdb->prefix . 'cmr_form_rate_limits';
    $records_table = $wpdb->prefix . 'cmr_form_submissions';

    $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'submissions';

    // Handle Manual Reset Action
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'reset_ip' && isset( $_GET['id'] ) ) {
        check_admin_referer( 'cmr_reset_rate_limit_' . intval( $_GET['id'] ) );
        $wpdb->delete( $limits_table, array( 'id' => intval( $_GET['id'] ) ) );
        echo '<div class="notice notice-success is-dismissible"><p>Rate limit reset successfully for the selected IP and form.</p></div>';
    }

    // Handle Delete Single Lead Record
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete_submission' && isset( $_GET['id'] ) ) {
        check_admin_referer( 'cmr_delete_submission_' . intval( $_GET['id'] ) );
        $wpdb->delete( $records_table, array( 'id' => intval( $_GET['id'] ) ) );
        echo '<div class="notice notice-success is-dismissible"><p>Submission record deleted successfully.</p></div>';
    }

    // Stats
    $total_leads = $wpdb->get_var( "SELECT COUNT(*) FROM {$records_table}" ) ?: 0;
    $total_blocked = $wpdb->get_var( "SELECT SUM(blocked_count) FROM {$limits_table}" ) ?: 0;
    $total_tracked_ips = $wpdb->get_var( "SELECT COUNT(*) FROM {$limits_table}" ) ?: 0;

    $filter_form = isset( $_GET['filter_form'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_form'] ) ) : '';
    $start_date  = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
    $end_date    = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';
    $search      = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

    $has_active_filters = ( ! empty( $filter_form ) || ! empty( $start_date ) || ! empty( $end_date ) || ! empty( $search ) );
    ?>
    <div class="wrap">
        <h1 style="display:flex; align-items:center; gap:10px;">
            <span class="dashicons dashicons-shield" style="font-size:32px; width:32px; height:32px; color:#6241ca;"></span>
            Form Submissions &amp; Rate Limits
        </h1>
        <p>Complete submission data for all website forms is stored here in the database, with a rate limit of maximum 3 submissions per IP address every 24 hours per form.</p>

        <!-- Stats Overview Cards -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:15px; margin: 20px 0;">
            <div style="background:#fff; border-left:4px solid #00a32a; padding:15px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); border-radius:4px;">
                <div style="font-size:12px; color:#666; text-transform:uppercase; font-weight:600;">Total Forms Submitted</div>
                <div style="font-size:26px; font-weight:700; color:#111; margin-top:5px;"><?php echo number_format_i18n( $total_leads ); ?></div>
            </div>
            <div style="background:#fff; border-left:4px solid #6241ca; padding:15px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); border-radius:4px;">
                <div style="font-size:12px; color:#666; text-transform:uppercase; font-weight:600;">Tracked IPs (Active)</div>
                <div style="font-size:26px; font-weight:700; color:#111; margin-top:5px;"><?php echo number_format_i18n( $total_tracked_ips ); ?></div>
            </div>
            <div style="background:#fff; border-left:4px solid #d63638; padding:15px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); border-radius:4px;">
                <div style="font-size:12px; color:#666; text-transform:uppercase; font-weight:600;">Blocked Attempts (4th+)</div>
                <div style="font-size:26px; font-weight:700; color:#d63638; margin-top:5px;"><?php echo number_format_i18n( $total_blocked ); ?></div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <h2 class="nav-tab-wrapper" style="margin-bottom:15px;">
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=cmr-form-rate-limits&tab=submissions' ) ); ?>" class="nav-tab <?php echo $active_tab === 'submissions' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-list-view" style="vertical-align:text-bottom; margin-right:5px;"></span>Form Submissions Log (Database Entries)
            </a>
            <a href="<?php echo esc_url( admin_url( 'tools.php?page=cmr-form-rate-limits&tab=limits' ) ); ?>" class="nav-tab <?php echo $active_tab === 'limits' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-shield" style="vertical-align:text-bottom; margin-right:5px;"></span>IP Rate Limits &amp; Blocked IPs (Max 3 / 24h)
            </a>
        </h2>

        <?php if ( $active_tab === 'submissions' ) : ?>
            <!-- SUBMISSIONS ARCHIVE TAB -->
            <?php
            // Build Forms List for Dropdown
            $known_forms = array(
                '7935' => '#7935 - Request a Consultation (Popup / Floating Form)',
                '8573' => '#8573 - Get Expert Insights (Download / Intelligence Form)',
                '8655' => '#8655 - Start a Conversation (Consultation Form)',
                '3672' => '#3672 - Contact Us Form',
                '3770' => '#3770 - Header Consultation Popup',
                'job_application' => 'Job Application Form',
            );
            $db_forms = $wpdb->get_results( "SELECT DISTINCT form_id, form_title FROM {$records_table} WHERE form_id IS NOT NULL AND form_id != '' ORDER BY form_title ASC" );
            $available_forms = $known_forms;
            if ( ! empty( $db_forms ) ) {
                foreach ( $db_forms as $df ) {
                    $fid = strval( $df->form_id );
                    $title = $df->form_title ? $df->form_title : 'Form #' . $fid;
                    if ( ! isset( $available_forms[ $fid ] ) ) {
                        $available_forms[ $fid ] = '#' . $fid . ' - ' . $title;
                    }
                }
            }

            $where_sub = cmr_build_submissions_where_clause( $filter_form, $start_date, $end_date, $search );
            $filtered_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$records_table} {$where_sub}" ) ?: 0;
            $leads = $wpdb->get_results( "SELECT * FROM {$records_table} {$where_sub} ORDER BY submitted_at DESC LIMIT 200" );

            // Filter-aware Export URL
            $export_args = array(
                'page'           => 'cmr-form-rate-limits',
                'cmr_export_csv' => '1',
            );
            if ( ! empty( $filter_form ) ) {
                $export_args['filter_form'] = $filter_form;
            }
            if ( ! empty( $start_date ) ) {
                $export_args['start_date'] = $start_date;
            }
            if ( ! empty( $end_date ) ) {
                $export_args['end_date'] = $end_date;
            }
            if ( ! empty( $search ) ) {
                $export_args['s'] = $search;
            }
            $export_url = wp_nonce_url( add_query_arg( $export_args, admin_url( 'tools.php' ) ), 'cmr_export_csv_nonce' );
            ?>

            <!-- Advanced Filter Bar (Form + Date + Search) -->
            <form method="get" action="" style="background:#fff; border:1px solid #ccd0d4; padding:16px 20px; border-radius:6px; margin-bottom:18px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <input type="hidden" name="page" value="cmr-form-rate-limits" />
                <input type="hidden" name="tab" value="submissions" />

                <div style="display:flex; flex-wrap:wrap; gap:15px; align-items:flex-end;">
                    <!-- Form Filter Dropdown -->
                    <div style="flex:1 1 250px; min-width:220px;">
                        <label for="cmr_filter_form" style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; color:#333; margin-bottom:5px;">
                            <span class="dashicons dashicons-feedback" style="font-size:16px; width:16px; height:16px; line-height:16px; color:#555;"></span> Select Form:
                        </label>
                        <select id="cmr_filter_form" name="filter_form" style="width:100%; height:34px;">
                            <option value="">-- All Forms (Show Everything) --</option>
                            <?php foreach ( $available_forms as $fid => $fname ) : ?>
                                <option value="<?php echo esc_attr( $fid ); ?>" <?php selected( $filter_form, strval( $fid ) ); ?>>
                                    <?php echo esc_html( $fname ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Single Date or Start Date -->
                    <div style="flex:1 1 160px; min-width:150px;">
                        <label for="cmr_start_date" style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; color:#333; margin-bottom:5px;">
                            <span class="dashicons dashicons-calendar-alt" style="font-size:16px; width:16px; height:16px; line-height:16px; color:#555;"></span> Date (Single or From):
                        </label>
                        <input type="date" id="cmr_start_date" name="start_date" value="<?php echo esc_attr( $start_date ); ?>" style="width:100%; height:34px;" />
                    </div>

                    <!-- End Date (Optional range) -->
                    <div style="flex:1 1 160px; min-width:150px;">
                        <label for="cmr_end_date" style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; color:#333; margin-bottom:5px;">
                            <span class="dashicons dashicons-calendar" style="font-size:16px; width:16px; height:16px; line-height:16px; color:#555;"></span> To Date (Optional Range):
                        </label>
                        <input type="date" id="cmr_end_date" name="end_date" value="<?php echo esc_attr( $end_date ); ?>" style="width:100%; height:34px;" />
                    </div>

                    <!-- Search Input -->
                    <div style="flex:2 1 220px; min-width:200px;">
                        <label for="cmr_search" style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; color:#333; margin-bottom:5px;">
                            <span class="dashicons dashicons-search" style="font-size:16px; width:16px; height:16px; line-height:16px; color:#555;"></span> Keyword Search:
                        </label>
                        <input type="text" id="cmr_search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Name, Email, Phone, Company, IP..." style="width:100%; height:34px;" />
                    </div>

                    <!-- Filter & Reset Buttons -->
                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="button button-primary" style="height:34px; line-height:32px; display:inline-flex; align-items:center; gap:5px; font-weight:600;">
                            <span class="dashicons dashicons-filter" style="margin-top:-2px;"></span> Filter Records
                        </button>
                        <?php if ( $has_active_filters ) : ?>
                            <a href="<?php echo esc_url( admin_url( 'tools.php?page=cmr-form-rate-limits&tab=submissions' ) ); ?>" class="button" style="height:34px; line-height:32px;">
                                Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Active Filter Badges Info -->
                <?php if ( $has_active_filters ) : ?>
                    <div style="margin-top:12px; padding-top:10px; border-top:1px dashed #e2e4e7; font-size:12px; color:#555; display:flex; align-items:center; flex-wrap:wrap; gap:8px;">
                        <strong>Active Filters:</strong>
                        <?php if ( $filter_form ) : ?>
                            <span style="background:#e8f0fe; color:#1a73e8; padding:2px 8px; border-radius:12px; font-weight:500;">
                                Form: <?php echo esc_html( isset( $available_forms[ $filter_form ] ) ? $available_forms[ $filter_form ] : '#' . $filter_form ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ( $start_date && $end_date ) : ?>
                            <span style="background:#fce8e6; color:#c5221f; padding:2px 8px; border-radius:12px; font-weight:500;">
                                Date: <?php echo esc_html( $start_date . ' to ' . $end_date ); ?>
                            </span>
                        <?php elseif ( $start_date ) : ?>
                            <span style="background:#fce8e6; color:#c5221f; padding:2px 8px; border-radius:12px; font-weight:500;">
                                Single Date: <?php echo esc_html( $start_date ); ?>
                            </span>
                        <?php elseif ( $end_date ) : ?>
                            <span style="background:#fce8e6; color:#c5221f; padding:2px 8px; border-radius:12px; font-weight:500;">
                                Up to Date: <?php echo esc_html( $end_date ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ( $search ) : ?>
                            <span style="background:#e6f4ea; color:#137333; padding:2px 8px; border-radius:12px; font-weight:500;">
                                Keyword: "<?php echo esc_html( $search ); ?>"
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </form>

            <!-- Table Header Info & CSV Export -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:10px;">
                <div style="font-size:13px; color:#444;">
                    Showing <strong><?php echo number_format_i18n( count( $leads ) ); ?></strong> of <strong><?php echo number_format_i18n( $filtered_count ); ?></strong> matching submissions
                    <?php if ( $has_active_filters ) : ?>
                        <em style="color:#666;">(Filters Applied)</em>
                    <?php endif; ?>
                </div>

                <a href="<?php echo esc_url( $export_url ); ?>" class="button button-secondary" style="display:inline-flex; align-items:center; gap:5px;">
                    <span class="dashicons dashicons-download" style="margin-top:-2px;"></span>
                    Export <?php echo $has_active_filters ? 'Filtered Data' : 'All Data'; ?> to CSV (Excel)
                </a>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width:60px;">#ID</th>
                        <th style="width:140px;">Date &amp; Time (IST)</th>
                        <th>Form Name</th>
                        <th>Submitter Name</th>
                        <th>Email Address</th>
                        <th style="width:130px;">Phone</th>
                        <th>Requirement / Message</th>
                        <th style="width:120px;">IP Address</th>
                        <th style="width:70px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $leads ) ) : ?>
                        <tr>
                            <td colspan="9" style="text-align:center; padding:25px; color:#888;">
                                No form submissions recorded yet. New submissions will appear here automatically.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $leads as $lead ) : 
                            $del_url = wp_nonce_url( admin_url( 'tools.php?page=cmr-form-rate-limits&tab=submissions&action=delete_submission&id=' . $lead->id ), 'cmr_delete_submission_' . $lead->id );
                        ?>
                            <tr>
                                <td><strong>#<?php echo esc_html( $lead->id ); ?></strong></td>
                                <td><?php echo esc_html( $lead->submitted_at ); ?></td>
                                <td>
                                    <strong><?php echo esc_html( $lead->form_title ?: 'Form #' . $lead->form_id ); ?></strong>
                                    <div style="font-size:11px; color:#888;">ID: #<?php echo esc_html( $lead->form_id ); ?></div>
                                </td>
                                <td><strong><?php echo esc_html( $lead->user_name ?: 'N/A' ); ?></strong></td>
                                <td><a href="mailto:<?php echo esc_attr( $lead->user_email ); ?>"><?php echo esc_html( $lead->user_email ?: 'N/A' ); ?></a></td>
                                <td><?php echo esc_html( $lead->user_phone ?: '&mdash;' ); ?></td>
                                <td>
                                    <?php if ( $lead->company_name ) : ?>
                                        <div style="font-size:11px; color:#555;"><strong>Company:</strong> <?php echo esc_html( $lead->company_name ); ?></div>
                                    <?php endif; ?>
                                    <div style="font-size:12px; color:#333; max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo esc_attr( $lead->user_message ); ?>">
                                        <?php echo esc_html( $lead->user_message ?: '&mdash;' ); ?>
                                    </div>
                                </td>
                                <td><code><?php echo esc_html( $lead->ip_address ); ?></code></td>
                                <td style="text-align:center;">
                                    <a href="<?php echo esc_url( $del_url ); ?>" class="button button-small" onclick="return confirm('Delete this submission record?');" style="color:#d63638;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php else : ?>
            <!-- RATE LIMITS & BLOCKED IPS TAB -->
            <?php
            $where_limit = '';
            if ( $search ) {
                $where_limit = $wpdb->prepare( "WHERE ip_address LIKE %s OR form_id LIKE %s OR form_title LIKE %s OR user_name LIKE %s", '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%' );
            }
            $logs = $wpdb->get_results( "SELECT * FROM {$limits_table} {$where_limit} ORDER BY last_submission_at DESC LIMIT 100" );
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width:110px;">Form ID</th>
                        <th>Form Name</th>
                        <th>Last Submitter</th>
                        <th style="width:130px;">IP Address</th>
                        <th style="width:100px; text-align:center;">Count (24h)</th>
                        <th style="width:90px; text-align:center;">Blocked</th>
                        <th>First Submit (IST)</th>
                        <th>Last Attempt (IST)</th>
                        <th style="width:140px;">Status</th>
                        <th style="width:80px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $logs ) ) : ?>
                        <tr>
                            <td colspan="10" style="text-align:center; padding:20px; color:#888;">No rate limit tracking records yet.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $logs as $row ) : 
                            $is_blocked = ( $row->status === 'blocked' || $row->blocked_count > 0 );
                            $sub_count = intval( $row->submission_count );
                            $badge_color = $is_blocked ? '#d63638' : ( $sub_count >= 3 ? '#dba617' : '#00a32a' );
                            $badge_text = $is_blocked ? 'Blocked (4th+)' : ( $sub_count >= 3 ? 'Limit Reached (3/3)' : 'Allowed (' . $sub_count . '/3)' );
                            $reset_url = wp_nonce_url( admin_url( 'tools.php?page=cmr-form-rate-limits&tab=limits&action=reset_ip&id=' . $row->id ), 'cmr_reset_rate_limit_' . $row->id );
                        ?>
                            <tr>
                                <td><strong>#<?php echo esc_html( $row->form_id ); ?></strong></td>
                                <td><?php echo esc_html( $row->form_title ?: 'Form #' . $row->form_id ); ?></td>
                                <td>
                                    <?php if ( $row->user_name || $row->user_email ) : ?>
                                        <strong><?php echo esc_html( $row->user_name ?: 'N/A' ); ?></strong><br>
                                        <small style="color:#555;"><?php echo esc_html( $row->user_email ); ?></small>
                                    <?php else : ?>
                                        <span style="color:#999;">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo esc_html( $row->ip_address ); ?></code></td>
                                <td style="text-align:center; font-weight:700;"><?php echo esc_html( $row->submission_count ); ?> / <?php echo CMR_FORM_RATE_LIMIT_MAX; ?></td>
                                <td style="text-align:center; font-weight:700; color:<?php echo $row->blocked_count > 0 ? '#d63638' : '#888'; ?>;">
                                    <?php echo esc_html( $row->blocked_count ); ?>
                                </td>
                                <td><?php echo esc_html( $row->first_submission_at ); ?></td>
                                <td><?php echo esc_html( $row->last_submission_at ); ?></td>
                                <td>
                                    <span style="display:inline-block; padding:3px 8px; border-radius:3px; font-size:11px; font-weight:600; color:#fff; background:<?php echo esc_attr( $badge_color ); ?>;">
                                        <?php echo esc_html( $badge_text ); ?>
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <a href="<?php echo esc_url( $reset_url ); ?>" class="button button-small" onclick="return confirm('Reset count for this IP on this form?');">Reset</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}
