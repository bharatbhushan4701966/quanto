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
    /**
    *
    * Hook for Footer Content
    *
    * Hook quanto_footer_content
    *
    * @Hooked quanto_footer_content_cb 10
    *
    */
    do_action( 'quanto_footer_content' );



    do_action( 'quanto_after_content' );
    

    if( !is_404( ) ) {
        /**
        *
        * Hook for Back to Top Button
        *
        * Hook quanto_back_to_top
        *
        * @Hooked quanto_back_to_top_cb 10
        *
        */
        do_action( 'quanto_back_to_top' );
    }

    wp_footer();
    ?>
</body>
</html>