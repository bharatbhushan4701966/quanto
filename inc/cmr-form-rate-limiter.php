<?php
/**
 * CMR Form Rate Limiter & Database Submission Tracker
 * 
 * Rules:
 * 1. Har form ki limit alag hogi (Separately tracked by Form ID + IP Address).
 * 2. Ek IP se har form par 24 ghante mein maximum 3 submissions allowed.
 * 3. Count database mein automatically save hoga ({$wpdb->prefix}cmr_form_rate_limits).
 * 4. 4th submission reject hogi, aur rejected attempts bhi database mein record hongi.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CMR_FORM_RATE_LIMIT_MAX', 3 );
define( 'CMR_FORM_RATE_LIMIT_WINDOW_HOURS', 24 );

// 1. Initialize Database Table
add_action( 'init', 'cmr_init_form_rate_limit_table' );
function cmr_init_form_rate_limit_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cmr_form_rate_limits';

    // Check if table needs to be created or updated
    if ( get_option( 'cmr_form_rate_limit_db_ver' ) !== '1.1' ) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id VARCHAR(100) NOT NULL,
            form_title VARCHAR(255) DEFAULT '',
            ip_address VARCHAR(64) NOT NULL,
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

        dbDelta( $sql );
        update_option( 'cmr_form_rate_limit_db_ver', '1.1' );
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

// 3. Check Rate Limit For Specific Form and IP (Dry Run / Validation)
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
            'allowed'          => true,
            'current_count'    => 0,
            'remaining'        => CMR_FORM_RATE_LIMIT_MAX,
            'blocked_count'    => 0,
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
            'allowed'          => true,
            'current_count'    => 0,
            'remaining'        => CMR_FORM_RATE_LIMIT_MAX,
            'blocked_count'    => 0,
            'is_first_in_window'=> true,
        );
    }

    // Within 24-hour window: check count
    $count = intval( $row->submission_count );
    if ( $count >= CMR_FORM_RATE_LIMIT_MAX ) {
        return array(
            'allowed'          => false,
            'current_count'    => $count,
            'remaining'        => 0,
            'blocked_count'    => intval( $row->blocked_count ),
            'is_first_in_window'=> false,
        );
    }

    return array(
        'allowed'          => true,
        'current_count'    => $count,
        'remaining'        => CMR_FORM_RATE_LIMIT_MAX - $count,
        'blocked_count'    => intval( $row->blocked_count ),
        'is_first_in_window'=> false,
    );
}

// 4. Record Blocked Attempt in Database
function cmr_record_blocked_submission( $form_id, $form_title, $ip ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cmr_form_rate_limits';
    $now = current_time( 'mysql' );

    $wpdb->query( $wpdb->prepare(
        "INSERT INTO {$table_name} (form_id, form_title, ip_address, submission_count, blocked_count, first_submission_at, last_submission_at, status)
         VALUES (%s, %s, %s, %d, 1, %s, %s, 'blocked')
         ON DUPLICATE KEY UPDATE 
            blocked_count = blocked_count + 1,
            last_submission_at = %s,
            status = 'blocked',
            form_title = IF(%s != '', %s, form_title)",
        $form_id,
        $form_title,
        $ip,
        CMR_FORM_RATE_LIMIT_MAX,
        $now,
        $now,
        $now,
        $form_title,
        $form_title
    ) );
}

// 5. Increment Successful Submission in Database
function cmr_record_successful_submission( $form_id, $form_title, $ip ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cmr_form_rate_limits';
    $now = current_time( 'mysql' );
    $check = cmr_check_form_rate_limit( $form_id, $ip );

    if ( $check['is_first_in_window'] ) {
        // First submission in new 24h window (or reset)
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO {$table_name} (form_id, form_title, ip_address, submission_count, blocked_count, first_submission_at, last_submission_at, status)
             VALUES (%s, %s, %s, 1, 0, %s, %s, 'allowed')
             ON DUPLICATE KEY UPDATE 
                submission_count = 1,
                blocked_count = 0,
                first_submission_at = %s,
                last_submission_at = %s,
                status = 'allowed',
                form_title = IF(%s != '', %s, form_title)",
            $form_id,
            $form_title,
            $ip,
            $now,
            $now,
            $now,
            $now,
            $form_title,
            $form_title
        ) );
    } else {
        // Subsequent submission (2nd or 3rd) within 24 hours
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table_name}
             SET submission_count = submission_count + 1,
                 last_submission_at = %s,
                 status = IF(submission_count + 1 >= %d, 'limit_reached', 'allowed'),
                 form_title = IF(%s != '', %s, form_title)
             WHERE form_id = %s AND ip_address = %s",
            $now,
            CMR_FORM_RATE_LIMIT_MAX,
            $form_title,
            $form_title,
            $form_id,
            $ip
        ) );
    }
}

// 6. Contact Form 7 Integration
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
        // 4th submission rejected! Record in database
        cmr_record_blocked_submission( $form_id, $form_title, $ip );

        $error_msg = 'You have reached the maximum limit of 3 submissions for this form in 24 hours. Please try again tomorrow.';

        // Invalidate form so CF7 stops immediately without sending email
        if ( ! empty( $tags ) && is_array( $tags ) && isset( $tags[0] ) ) {
            $result->invalidate( $tags[0], $error_msg );
        } else {
            $result->invalidate( '', $error_msg );
        }
    }

    return $result;
}

add_action( 'wpcf7_mail_sent', 'cmr_cf7_rate_limit_on_success' );
function cmr_cf7_rate_limit_on_success( $contact_form ) {
    if ( ! $contact_form ) {
        return;
    }
    $form_id = (string) $contact_form->id();
    $form_title = $contact_form->title();
    $ip = cmr_get_client_ip_address();

    cmr_record_successful_submission( $form_id, $form_title, $ip );
}

// 7. Elementor Pro Form Integration
add_action( 'elementor_pro/forms/validation', 'cmr_elementor_rate_limit_validator', 5, 2 );
function cmr_elementor_rate_limit_validator( $record, $ajax_handler ) {
    $form_id = $record->get_form_settings( 'form_id' ) ?: $record->get_form_settings( 'id' ) ?: 'elementor_form';
    $form_title = $record->get_form_settings( 'form_name' ) ?: 'Elementor Form';
    $ip = cmr_get_client_ip_address();

    $check = cmr_check_form_rate_limit( $form_id, $ip );

    if ( ! $check['allowed'] ) {
        cmr_record_blocked_submission( $form_id, $form_title, $ip );

        $ajax_handler->add_error_message( 'You have reached the maximum limit of 3 submissions for this form in 24 hours. Please try again tomorrow.' );
        $ajax_handler->is_success = false;
    }
}

add_action( 'elementor_pro/forms/new_record', 'cmr_elementor_rate_limit_on_success', 10, 2 );
function cmr_elementor_rate_limit_on_success( $record, $ajax_handler ) {
    if ( ! empty( $ajax_handler->errors ) ) {
        return;
    }
    $form_id = $record->get_form_settings( 'form_id' ) ?: $record->get_form_settings( 'id' ) ?: 'elementor_form';
    $form_title = $record->get_form_settings( 'form_name' ) ?: 'Elementor Form';
    $ip = cmr_get_client_ip_address();

    cmr_record_successful_submission( $form_id, $form_title, $ip );
}

// 8. Job Application Form Integration Hook
function cmr_job_form_check_rate_limit() {
    $form_id = 'job_application';
    $form_title = 'Job Application Form';
    $ip = cmr_get_client_ip_address();

    $check = cmr_check_form_rate_limit( $form_id, $ip );
    if ( ! $check['allowed'] ) {
        cmr_record_blocked_submission( $form_id, $form_title, $ip );
        return false;
    }
    return true;
}

function cmr_job_form_record_success() {
    $form_id = 'job_application';
    $form_title = 'Job Application Form';
    $ip = cmr_get_client_ip_address();
    cmr_record_successful_submission( $form_id, $form_title, $ip );
}

// 9. Admin Dashboard UI (Tools -> Form Rate Limits)
add_action( 'admin_menu', 'cmr_register_form_rate_limit_admin_menu' );
function cmr_register_form_rate_limit_admin_menu() {
    add_management_page(
        'Form Rate Limits',
        'Form Rate Limits',
        'manage_options',
        'cmr-form-rate-limits',
        'cmr_render_form_rate_limit_admin_page'
    );
}

function cmr_render_form_rate_limit_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cmr_form_rate_limits';

    // Handle Manual Reset Action
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'reset_ip' && isset( $_GET['id'] ) ) {
        check_admin_referer( 'cmr_reset_rate_limit_' . intval( $_GET['id'] ) );
        $wpdb->delete( $table_name, array( 'id' => intval( $_GET['id'] ) ) );
        echo '<div class="notice notice-success is-dismissible"><p>Rate limit reset successfully for the selected IP and form.</p></div>';
    }

    // Handle Clear Expired Action
    if ( isset( $_POST['cmr_clear_expired_logs'] ) ) {
        check_admin_referer( 'cmr_clear_expired_action' );
        $cutoff = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) - ( 24 * 3600 ) );
        $deleted = $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$table_name} WHERE last_submission_at < %s AND blocked_count = 0",
            $cutoff
        ) );
        echo '<div class="notice notice-success is-dismissible"><p>Cleared ' . intval( $deleted ) . ' expired log(s) older than 24 hours.</p></div>';
    }

    // Fetch Stats
    $total_records = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
    $total_submissions = $wpdb->get_var( "SELECT SUM(submission_count) FROM {$table_name}" ) ?: 0;
    $total_blocked = $wpdb->get_var( "SELECT SUM(blocked_count) FROM {$table_name}" ) ?: 0;
    $blocked_ips = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name} WHERE status = 'blocked' OR blocked_count > 0" ) ?: 0;

    // Search / Filter
    $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
    $where_clause = '';
    if ( $search ) {
        $where_clause = $wpdb->prepare( "WHERE ip_address LIKE %s OR form_id LIKE %s OR form_title LIKE %s", '%' . $search . '%', '%' . $search . '%', '%' . $search . '%' );
    }

    $logs = $wpdb->get_results( "SELECT * FROM {$table_name} {$where_clause} ORDER BY last_submission_at DESC LIMIT 100" );
    ?>
    <div class="wrap">
        <h1 style="display:flex; align-items:center; gap:10px;">
            <span class="dashicons dashicons-shield" style="font-size:32px; width:32px; height:32px; color:#6241ca;"></span>
            Form Submissions &amp; Rate Limits (Max 3 / 24 Hours)
        </h1>
        <p>Har form ki limit alag track hoti hai. Ek IP se ek form par 24 ghante mein maximum 3 submissions allow hain, 4th attempt automatically reject hoti hai aur database mein record hoti hai.</p>

        <!-- Stats Overview Cards -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:15px; margin: 20px 0;">
            <div style="background:#fff; border-left:4px solid #6241ca; padding:15px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); border-radius:4px;">
                <div style="font-size:12px; color:#666; text-transform:uppercase; font-weight:600;">Tracked Records</div>
                <div style="font-size:26px; font-weight:700; color:#111; margin-top:5px;"><?php echo number_format_i18n( $total_records ); ?></div>
            </div>
            <div style="background:#fff; border-left:4px solid #00BFBC; padding:15px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); border-radius:4px;">
                <div style="font-size:12px; color:#666; text-transform:uppercase; font-weight:600;">Total Submissions</div>
                <div style="font-size:26px; font-weight:700; color:#111; margin-top:5px;"><?php echo number_format_i18n( $total_submissions ); ?></div>
            </div>
            <div style="background:#fff; border-left:4px solid #d63638; padding:15px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); border-radius:4px;">
                <div style="font-size:12px; color:#666; text-transform:uppercase; font-weight:600;">Blocked Attempts (4th+)</div>
                <div style="font-size:26px; font-weight:700; color:#d63638; margin-top:5px;"><?php echo number_format_i18n( $total_blocked ); ?></div>
            </div>
            <div style="background:#fff; border-left:4px solid #dba617; padding:15px 20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); border-radius:4px;">
                <div style="font-size:12px; color:#666; text-transform:uppercase; font-weight:600;">Currently Blocked IPs</div>
                <div style="font-size:26px; font-weight:700; color:#111; margin-top:5px;"><?php echo number_format_i18n( $blocked_ips ); ?></div>
            </div>
        </div>

        <!-- Search & Actions Bar -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
            <form method="get" action="">
                <input type="hidden" name="page" value="cmr-form-rate-limits" />
                <input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Search IP or Form..." style="min-width:240px;" />
                <input type="submit" class="button" value="Search" />
                <?php if ( $search ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'tools.php?page=cmr-form-rate-limits' ) ); ?>" class="button">Reset Search</a>
                <?php endif; ?>
            </form>

            <form method="post" action="">
                <?php wp_nonce_field( 'cmr_clear_expired_action' ); ?>
                <input type="submit" name="cmr_clear_expired_logs" class="button button-secondary" value="Clear Expired Logs (>24 Hours)" onclick="return confirm('Clear entries older than 24 hours?');" />
            </form>
        </div>

        <!-- Database Records Table -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:120px;">Form ID</th>
                    <th>Form Name</th>
                    <th style="width:140px;">IP Address</th>
                    <th style="width:110px; text-align:center;">Submissions</th>
                    <th style="width:120px; text-align:center;">Blocked</th>
                    <th>First Submitted (IST)</th>
                    <th>Last Attempt (IST)</th>
                    <th style="width:140px;">Status</th>
                    <th style="width:90px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $logs ) ) : ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:20px; color:#888;">No form submissions recorded yet in the database.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $logs as $row ) : 
                        $is_blocked = ( $row->status === 'blocked' || $row->blocked_count > 0 );
                        $sub_count = intval( $row->submission_count );
                        $badge_color = $is_blocked ? '#d63638' : ( $sub_count >= 3 ? '#dba617' : '#00a32a' );
                        $badge_text = $is_blocked ? 'Blocked (4th+)' : ( $sub_count >= 3 ? 'Limit Reached (3/3)' : 'Allowed (' . $sub_count . '/3)' );
                        $reset_url = wp_nonce_url( admin_url( 'tools.php?page=cmr-form-rate-limits&action=reset_ip&id=' . $row->id ), 'cmr_reset_rate_limit_' . $row->id );
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html( $row->form_id ); ?></strong></td>
                            <td><?php echo esc_html( $row->form_title ?: 'Form #' . $row->form_id ); ?></td>
                            <td><code><?php echo esc_html( $row->ip_address ); ?></code></td>
                            <td style="text-align:center; font-weight:600;"><?php echo esc_html( $row->submission_count ); ?> / <?php echo CMR_FORM_RATE_LIMIT_MAX; ?></td>
                            <td style="text-align:center; font-weight:600; color:<?php echo $row->blocked_count > 0 ? '#d63638' : '#888'; ?>;">
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
                                <a href="<?php echo esc_url( $reset_url ); ?>" class="button button-small" onclick="return confirm('Reset submission count for this IP on this form?');">Reset</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
