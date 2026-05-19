<?php

/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme ecohost for publication on ThemeForest
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */



/**
 * Include the TGM_Plugin_Activation class.
 */
require_once QUANTO_DIR_PATH_INC . 'Quanto-framework/plugins-activation/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'quanto_register_required_plugins' );
function quanto_register_required_plugins() {

    /*
    * Array of plugin arrays. Required keys are name and slug.
    * If the source is NOT from the .org repo, then source is also required.
    */

    $plugins = array(
        
        array(
            'name'                  => esc_html__( 'Quanto Core', 'quanto' ),
            'slug'                  => 'quanto-core',
            'version'               => '1.0',
            'source'                => 'https://kit.framerpeak.com/plugins/quanto-core.zip',
            'required'              => true,
        ),
        array(
            'name'                  => esc_html__( 'Mas Animation', 'quanto' ),
            'slug'                  => 'mas-animation',
            'version'               => '1.0',
            'source'                => 'https://kit.framerpeak.com/plugins/mas-animation.zip',
            'required'              => true,
        ),
        array(
            'name'                  => esc_html__( 'One Click Demo Importer', 'quanto' ),
            'slug'                  => 'one-click-demo-import',
            'required'              => true,
        ),
        array(
            'name'      => esc_html__( 'Elementor', 'quanto' ),
            'slug'      => 'elementor',
            'version'   => '',
            'required'  => true,
        ),
        array(
            'name'      => esc_html__( 'Redux Framework', 'quanto' ),
            'slug'      => 'redux-framework',
            'version'   => '',
            'required'  => true,
        ),
        array(
            'name'      => esc_html__( 'CMB2', 'quanto' ),
            'slug'      => 'cmb2',
            'required'  => true,
        ),
        array(
            'name'      => esc_html__( 'Contact Form 7', 'quanto' ),
            'slug'      => 'contact-form-7',
            'version'   => '',
            'required'  => true,
        ),

    );

    $config = array(
        'id'           => 'quanto',
        'default_path' => '',
        'menu'         => 'tgmpa-install-plugins',
        'has_notices'  => true,
        'dismissable'  => true,
        'dismiss_msg'  => '',
        'is_automatic' => false,
        'message'      => '',
    );

    tgmpa( $plugins, $config );
}