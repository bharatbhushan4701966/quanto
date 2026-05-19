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

/**
 *
 * Define constant
 *
 */

// Base URI
if ( ! defined( 'QUANTO_DIR_URI' ) ) {
    define('QUANTO_DIR_URI', get_parent_theme_file_uri().'/' );
}

// Assist URI
if ( ! defined( 'QUANTO_DIR_ASSIST_URI' ) ) {
    define( 'QUANTO_DIR_ASSIST_URI', get_theme_file_uri('/assets/') );
}


// Css File URI
if ( ! defined( 'QUANTO_DIR_CSS_URI' ) ) {
    define( 'QUANTO_DIR_CSS_URI', get_theme_file_uri('/assets/css/') );
}

// Skin Css File
if ( ! defined( 'QUANTO_DIR_SKIN_CSS_URI' ) ) {
    define( 'QUANTO_DIR_SKIN_CSS_URI', get_theme_file_uri('/assets/css/skins/') );
}


// Js File URI
if (!defined('QUANTO_DIR_JS_URI')) {
    define('QUANTO_DIR_JS_URI', get_theme_file_uri('/assets/js/'));
}


// External PLugin File URI
if (!defined('QUANTO_DIR_PLUGIN_URI')) {
    define('QUANTO_DIR_PLUGIN_URI', get_theme_file_uri( '/assets/plugins/'));
}

// Base Directory
if (!defined('QUANTO_DIR_PATH')) {
    define('QUANTO_DIR_PATH', get_parent_theme_file_path() . '/');
}

//Inc Folder Directory
if (!defined('QUANTO_DIR_PATH_INC')) {
    define('QUANTO_DIR_PATH_INC', QUANTO_DIR_PATH . 'inc/');
}

//QUANTO framework Folder Directory
if (!defined('QUANTO_DIR_PATH_FRAM')) {
    define('QUANTO_DIR_PATH_FRAM', QUANTO_DIR_PATH_INC . 'quanto-framework/');
}

//Classes Folder Directory
if (!defined('QUANTO_DIR_PATH_CLASSES')) {
    define('QUANTO_DIR_PATH_CLASSES', QUANTO_DIR_PATH_INC . 'classes/');
}

//Hooks Folder Directory
if (!defined('QUANTO_DIR_PATH_HOOKS')) {
    define('QUANTO_DIR_PATH_HOOKS', QUANTO_DIR_PATH_INC . 'hooks/');
}

//Demo Data Folder Directory Path
if( !defined( 'QUANTO_DEMO_DIR_PATH' ) ){
    define( 'QUANTO_DEMO_DIR_PATH', QUANTO_DIR_PATH_INC.'demo-data/' );
}
    
//Demo Data Folder Directory URI
if( !defined( 'QUANTO_DEMO_DIR_URI' ) ){
    define( 'QUANTO_DEMO_DIR_URI', QUANTO_DIR_URI.'inc/demo-data/' );
}