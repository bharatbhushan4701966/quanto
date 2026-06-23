<?php
// Job Application Form Backend Handling

// 1. Register Custom Post Type to store applications
add_action('init', 'cmr_register_job_applicant_cpt');
function cmr_register_job_applicant_cpt() {
    $labels = array(
        'name'               => 'Job Applications',
        'singular_name'      => 'Job Application',
        'menu_name'          => 'Job Applications',
        'name_admin_bar'     => 'Job Application',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Application',
        'new_item'           => 'New Application',
        'edit_item'          => 'Edit Application',
        'view_item'          => 'View Application',
        'all_items'          => 'All Applications',
        'search_items'       => 'Search Applications',
        'not_found'          => 'No applications found.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 30,
        'menu_icon'          => 'dashicons-id',
        'supports'           => array('title', 'custom-fields'),
    );

    register_post_type('cmr_job_applicant', $args);
}

// Helper Function to Send the Email
function cmr_send_job_application_email($post_id) {
    $full_name  = get_post_meta($post_id, 'applicant_name', true);
    $email      = get_post_meta($post_id, 'applicant_email', true);
    $phone      = get_post_meta($post_id, 'applicant_phone', true);
    $location   = get_post_meta($post_id, 'applicant_location', true);
    $experience = get_post_meta($post_id, 'applicant_experience', true);
    $salary     = get_post_meta($post_id, 'applicant_salary', true);
    $portfolio  = get_post_meta($post_id, 'applicant_portfolio', true);
    $job_title  = get_post_meta($post_id, 'applicant_job', true);
    $file_url   = get_post_meta($post_id, 'applicant_resume_url', true);
    
    // We need to resolve the absolute file path from the URL for wp_mail attachments
    $upload_dir = wp_get_upload_dir();
    $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);

    // If the file path doesn't exist or replacement failed, we just use the url (attachment might fail, but email sends)
    if (!file_exists($file_path)) {
        $file_path = ''; 
    }

    $to = 'beastbad270@gmail.com';
    $subject = sprintf('New Job Application: %s for %s', $full_name, $job_title);
    
    $body = "<h2>New Job Application Received</h2>";
    $body .= "<p><strong>Job Title:</strong> {$job_title}</p>";
    $body .= "<p><strong>Name:</strong> {$full_name}</p>";
    $body .= "<p><strong>Email:</strong> {$email}</p>";
    $body .= "<p><strong>Phone:</strong> {$phone}</p>";
    $body .= "<p><strong>Location:</strong> {$location}</p>";
    $body .= "<p><strong>Total Experience:</strong> {$experience}</p>";
    $body .= "<p><strong>Expected Salary:</strong> {$salary}</p>";
    if ($portfolio) {
        $body .= "<p><strong>Portfolio:</strong> <a href='{$portfolio}'>{$portfolio}</a></p>";
    }

    if ($file_path) {
        $body .= "<br><p>The applicant's resume is attached to this email.</p>";
        $attachments = array($file_path);
    } else {
        $body .= "<br><p><strong>Resume URL:</strong> <a href='{$file_url}'>Click here to download resume</a></p>";
        $attachments = array();
    }

    $site_domain = parse_url(home_url(), PHP_URL_HOST);
    $sender_email = 'no-reply@' . $site_domain;
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Quanto Job Application <' . $sender_email . '>'
    );

    return wp_mail($to, $subject, $body, $headers, $attachments);
}

// 2. Handle AJAX Form Submission
add_action('wp_ajax_nopriv_cmr_submit_application', 'cmr_handle_job_application_submit');
add_action('wp_ajax_cmr_submit_application', 'cmr_handle_job_application_submit');

function cmr_handle_job_application_submit() {
    $full_name = sanitize_text_field($_POST['full_name'] ?? '');
    $location = sanitize_text_field($_POST['location'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $experience = sanitize_text_field($_POST['experience'] ?? '');
    $salary = sanitize_text_field($_POST['salary'] ?? '');
    $portfolio = esc_url_raw($_POST['portfolio'] ?? '');
    $job_title = sanitize_text_field($_POST['job_title'] ?? 'General Application');

    if (empty($full_name) || empty($email) || empty($phone)) {
        wp_send_json_error(array('message' => 'Please fill in all required fields.'));
        wp_die();
    }

    // Handle File Upload
    $file_url = '';
    $file_path = '';
    if (!empty($_FILES['resume']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $uploadedfile = $_FILES['resume'];
        
        $supported_types = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg');
        if (!in_array($uploadedfile['type'], $supported_types)) {
            wp_send_json_error(array('message' => 'Invalid file type. Only PDF, DOC/DOCX, and JPG are allowed.'));
            wp_die();
        }

        if ($uploadedfile['size'] > 5 * 1024 * 1024) {
            wp_send_json_error(array('message' => 'File exceeds 5MB limit.'));
            wp_die();
        }

        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            $file_url = $movefile['url'];
            $file_path = $movefile['file'];
        } else {
            wp_send_json_error(array('message' => 'Failed to upload resume: ' . $movefile['error']));
            wp_die();
        }
    } else {
        wp_send_json_error(array('message' => 'Resume file is required.'));
        wp_die();
    }

    // Create Post
    $post_data = array(
        'post_title'    => sprintf('%s - %s', $full_name, $job_title),
        'post_type'     => 'cmr_job_applicant',
        'post_status'   => 'publish',
    );

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => 'Failed to save application to database.'));
        wp_die();
    }

    // Save Meta Data
    update_post_meta($post_id, 'applicant_name', $full_name);
    update_post_meta($post_id, 'applicant_email', $email);
    update_post_meta($post_id, 'applicant_phone', '+91 ' . $phone);
    update_post_meta($post_id, 'applicant_location', $location);
    update_post_meta($post_id, 'applicant_experience', $experience);
    update_post_meta($post_id, 'applicant_salary', $salary);
    update_post_meta($post_id, 'applicant_portfolio', $portfolio);
    update_post_meta($post_id, 'applicant_job', $job_title);
    update_post_meta($post_id, 'applicant_resume_url', $file_url);

    // Send Email
    $mail_sent = cmr_send_job_application_email($post_id);

    wp_send_json_success(array('message' => 'Application submitted successfully!', 'mail_sent' => $mail_sent));
    wp_die();
}

// 3. Add "Resend Email" Button Meta Box
add_action('add_meta_boxes', 'cmr_add_job_applicant_meta_box');
function cmr_add_job_applicant_meta_box() {
    add_meta_box(
        'cmr_applicant_actions',
        'Application Actions',
        'cmr_render_job_applicant_meta_box',
        'cmr_job_applicant',
        'side',
        'high'
    );
}

function cmr_render_job_applicant_meta_box($post) {
    $resend_url = wp_nonce_url(
        admin_url('admin-post.php?action=cmr_resend_application_email&post_id=' . $post->ID),
        'cmr_resend_email_' . $post->ID
    );
    ?>
    <p>If you did not receive the notification email for this application, you can manually trigger it to send again.</p>
    <a href="<?php echo esc_url($resend_url); ?>" class="button button-primary">Resend Email Notification</a>
    <?php
}

// 4. Handle "Resend Email" Admin Post Action
add_action('admin_post_cmr_resend_application_email', 'cmr_handle_resend_application_email');
function cmr_handle_resend_application_email() {
    if (!current_user_can('edit_posts')) {
        wp_die('Unauthorized access.');
    }

    $post_id = intval($_GET['post_id'] ?? 0);
    
    check_admin_referer('cmr_resend_email_' . $post_id);

    if ($post_id) {
        $mail_sent = cmr_send_job_application_email($post_id);
        
        if ($mail_sent) {
            $redirect_url = add_query_arg(array('post' => $post_id, 'action' => 'edit', 'cmr_email_resent' => '1'), admin_url('post.php'));
        } else {
            $redirect_url = add_query_arg(array('post' => $post_id, 'action' => 'edit', 'cmr_email_resent' => '0'), admin_url('post.php'));
        }
        
        wp_redirect($redirect_url);
        exit;
    }
}

// 5. Show Success/Error Notice on Post Edit Screen
add_action('admin_notices', 'cmr_resend_email_admin_notice');
function cmr_resend_email_admin_notice() {
    if (isset($_GET['cmr_email_resent'])) {
        $status = $_GET['cmr_email_resent'];
        if ($status === '1') {
            echo '<div class="notice notice-success is-dismissible"><p><strong>Success:</strong> Job Application email has been re-sent!</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Error:</strong> Failed to re-send the email. Please check your SMTP configuration.</p></div>';
        }
    }
}
