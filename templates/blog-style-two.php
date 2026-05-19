<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo '<!-- Single Post -->';

    // Excerpt And Read More Button
    do_action( 'quanto_blog_post_content' );

    // Blog Post Content
    do_action( 'quanto_blog_post_thumb' );

echo '<!-- End Single Post -->';