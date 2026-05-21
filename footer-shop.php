<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

do_action( 'quanto_footer_content' );

do_action( 'quanto_after_content' );

if ( ! is_404() ) {
    do_action( 'quanto_back_to_top' );
}

wp_footer();
?>
</body>
</html>
