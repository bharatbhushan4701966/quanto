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
 * Include File
 *
 */

// Constants
require_once get_parent_theme_file_path() . '/inc/quanto-constants.php';

//theme setup
require_once QUANTO_DIR_PATH_INC . 'theme-setup.php';

//essential scripts
require_once QUANTO_DIR_PATH_INC . 'essential-scripts.php';

//template helper
require_once QUANTO_DIR_PATH_INC . 'template-helper.php';

// plugin activation
require_once QUANTO_DIR_PATH_INC . 'Quanto-framework/plugins-activation/quanto-active-plugins.php';

// meta options
require_once QUANTO_DIR_PATH_INC . 'Quanto-framework/quanto-meta/quanto-config.php';

// page breadcrumbs
require_once QUANTO_DIR_PATH_INC . 'quanto-breadcrumbs.php';

// sidebar register
require_once QUANTO_DIR_PATH_INC . 'quanto-widgets-reg.php';

//essential functions
require_once QUANTO_DIR_PATH_INC . 'quanto-functions.php';

// theme dynamic css
require_once QUANTO_DIR_PATH_INC . 'quanto-commoncss.php';

// helper function
require_once QUANTO_DIR_PATH_INC . 'wp-html-helper.php';

// pagination
require_once QUANTO_DIR_PATH_INC . 'wp_bootstrap_pagination.php';

// quanto options
function quanto_setup_ab() { 
    require_once QUANTO_DIR_PATH_INC . 'Quanto-framework/quanto-options/quanto-options.php';
}
add_action( 'after_setup_theme', 'quanto_setup_ab', 20 );

// hooks
require_once QUANTO_DIR_PATH_HOOKS . 'hooks.php';

// hooks funtion
require_once QUANTO_DIR_PATH_HOOKS . 'hooks-functions.php';

// Force enable WooCommerce product reviews and ratings
add_action( 'init', 'quanto_force_enable_woocommerce_reviews' );
function quanto_force_enable_woocommerce_reviews() {
    if ( class_exists( 'WooCommerce' ) ) {
        if ( get_option( 'woocommerce_enable_reviews' ) !== 'yes' ) {
            update_option( 'woocommerce_enable_reviews', 'yes' );
        }
        if ( get_option( 'woocommerce_enable_review_rating' ) !== 'yes' ) {
            update_option( 'woocommerce_enable_review_rating', 'yes' );
        }
    }
}

