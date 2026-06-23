<?php
// SMTP Configuration for outbound emails
// Fill in your "host credentials" below to bypass the staging server's default mailer

add_action( 'phpmailer_init', 'cmr_custom_smtp_mailer' );
function cmr_custom_smtp_mailer( $phpmailer ) {
    $phpmailer->isSMTP();
    
    // ---------------------------------------------------------
    // CHANGE YOUR HOST CREDENTIALS HERE
    // ---------------------------------------------------------
    $phpmailer->Host       = 'smtp.example.com'; // e.g., smtp.gmail.com or smtp.office365.com
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587; // Usually 587 (TLS) or 465 (SSL)
    $phpmailer->Username   = 'your-email@example.com';
    $phpmailer->Password   = 'your-email-password';
    $phpmailer->SMTPSecure = 'tls'; // 'tls' or 'ssl'
    // ---------------------------------------------------------

    // Optional: Force the "From" address to match your authenticated SMTP Username
    $phpmailer->From       = 'your-email@example.com';
    $phpmailer->FromName   = 'Quanto Website';
}
