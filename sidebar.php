<?php

/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */


// Block direct access
if( !defined( 'ABSPATH' ) ){
    exit;
}

if ( ! is_active_sidebar( 'quanto-blog-sidebar' ) ) {
    return;
}
?>

<div class="col-lg-4">
    <div class="blog__sidebar">
    <?php dynamic_sidebar( 'quanto-blog-sidebar' ); ?>
    </div>
</div>