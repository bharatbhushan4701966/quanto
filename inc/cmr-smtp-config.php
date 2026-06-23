<?php
// SMTP Configuration for outbound emails
// Fill in your "host credentials" below to bypass the staging server's default mailer

add_action( 'phpmailer_init', 'cmr_custom_smtp_mailer' );
function cmr_custom_smtp_mailer( $phpmailer ) {
    $phpmailer->isSMTP();
    
    // ---------------------------------------------------------
    // USE GMAIL AS YOUR FREE MAIL SERVER
    // 1. Go to Google Account -> Security -> App Passwords
    // 2. Generate a 16-digit password and paste it below
    // ---------------------------------------------------------
    $phpmailer->Host       = 'smtp.gmail.com'; 
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587; 
    $phpmailer->Username   = 'beastbad270@gmail.com'; // Your Gmail
    $phpmailer->Password   = 'YOUR_16_DIGIT_APP_PASSWORD'; // Paste the 16-digit app password here
    $phpmailer->SMTPSecure = 'tls'; 
    // ---------------------------------------------------------

    $phpmailer->From       = 'beastbad270@gmail.com';
    $phpmailer->FromName   = 'Quanto Careers';
}
