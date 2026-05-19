<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

// Block direct access
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

echo '<!-- Single Post -->';
?>
<div <?php post_class(); ?> >
<?php

    // Blog Post Thumbnail
    do_action( 'quanto_blog_post_thumb' );
    // Blog Post Content
    do_action( 'quanto_blog_post_content' );
    
    
echo '</div>';
echo '<!-- End Single Post -->';