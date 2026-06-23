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

// 2. Handle AJAX Form Submission
add_action('wp_ajax_nopriv_cmr_submit_application', 'cmr_handle_job_application_submit');
add_action('wp_ajax_cmr_submit_application', 'cmr_handle_job_application_submit');

function cmr_handle_job_application_submit() {
    // Basic security check (nonce can be added if needed, omitting for simplicity of this shortcode setup)
    
    // Sanitize and gather inputs
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
        
        // Allowed file types
        $supported_types = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg');
        if (!in_array($uploadedfile['type'], $supported_types)) {
            wp_send_json_error(array('message' => 'Invalid file type. Only PDF, DOC/DOCX, and JPG are allowed.'));
            wp_die();
        }

        // Limit size to 5MB
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
    $to = 'beastbad270@gmail.com';
    $subject = sprintf('New Job Application: %s for %s', $full_name, $job_title);
    
    $body = "<h2>New Job Application Received</h2>";
    $body .= "<p><strong>Job Title:</strong> {$job_title}</p>";
    $body .= "<p><strong>Name:</strong> {$full_name}</p>";
    $body .= "<p><strong>Email:</strong> {$email}</p>";
    $body .= "<p><strong>Phone:</strong> +91 {$phone}</p>";
    $body .= "<p><strong>Location:</strong> {$location}</p>";
    $body .= "<p><strong>Total Experience:</strong> {$experience}</p>";
    $body .= "<p><strong>Expected Salary:</strong> {$salary}</p>";
    if ($portfolio) {
        $body .= "<p><strong>Portfolio:</strong> <a href='{$portfolio}'>{$portfolio}</a></p>";
    }
    $body .= "<br><p>The applicant's resume is attached to this email.</p>";

    $site_domain = parse_url(home_url(), PHP_URL_HOST);
    $sender_email = 'no-reply@' . $site_domain;
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Quanto Job Application <' . $sender_email . '>'
    );
    $attachments = array($file_path);

    $mail_sent = wp_mail($to, $subject, $body, $headers, $attachments);

    wp_send_json_success(array('message' => 'Application submitted successfully!', 'mail_sent' => $mail_sent));
    wp_die();
}
