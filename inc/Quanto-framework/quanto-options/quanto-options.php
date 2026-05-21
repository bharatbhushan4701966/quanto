<?php
    /**
     * ReduxFramework Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "quanto_opt";

    // This line is only for altering the demo. Can be easily removed.
    $opt_name = apply_filters( 'redux_demo/opt_name', $opt_name );

    /*
     *
     * --> Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
     *
     */

    $sampleHTML = '';
    if ( file_exists( dirname( __FILE__ ) . '/info-html.html' ) ) {
        Redux_Functions::initWpFilesystem();

        global $wp_filesystem;

        $sampleHTML = $wp_filesystem->get_contents( dirname( __FILE__ ) . '/info-html.html' );
    }


    $alowhtml = array(
        'p' => array(
            'class' => array()
        ),
        'span' => array()
    );


    // Background Patterns Reader
    $sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
    $sample_patterns_url  = ReduxFramework::$_url . '../sample/patterns/';
    $sample_patterns      = array();

    if ( is_dir( $sample_patterns_path ) ) {

        if ( $sample_patterns_dir = opendir( $sample_patterns_path ) ) {
            $sample_patterns = array();

            while ( ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) !== false ) {

                if ( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
                    $name              = explode( '.', $sample_patterns_file );
                    $name              = str_replace( '.' . end( $name ), '', $sample_patterns_file );
                    $sample_patterns[] = array(
                        'alt' => $name,
                        'img' => $sample_patterns_url . $sample_patterns_file
                    );
                }
            }
        }
    }

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        // TYPICAL -> Change these values as you need/desire
        'opt_name'             => $opt_name,
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name'         => $theme->get( 'Name' ),
        // Name that appears at the top of your panel
        'display_version'      => $theme->get( 'Version' ),
        // Version that appears at the top of your panel
        'menu_type'            => 'menu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu'       => true,
        // Show the sections below the admin menu item or not
        'menu_title'           => esc_html__( 'Quanto Options', 'quanto' ),
        'page_title'           => esc_html__( 'Quanto Options', 'quanto' ),
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key'       => '',
        // Set it you want google fonts to update weekly. A google_api_key value is required.
        'google_update_weekly' => false,
        // Must be defined to add google fonts to the typography module
        'async_typography'     => false,
        // Use a asynchronous font on the front end or font string
        //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
        'admin_bar'            => true,
        // Show the panel pages on the admin bar
        'admin_bar_icon'       => 'dashicons-portfolio',
        // Choose an icon for the admin bar menu
        'admin_bar_priority'   => 50,
        // Choose an priority for the admin bar menu
        'global_variable'      => '',
        // Set a different name for your global variable other than the opt_name
        'dev_mode'             => false,
        // Show the time the page took to load, etc
        'update_notice'        => true,
        
        // OPTIONAL -> Give you extra features
        'page_priority'        => null,
        // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_parent'          => 'themes.php',
        // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
        'page_permissions'     => 'manage_options',
        // Permissions needed to access the options panel.
        'menu_icon'            => '',
        // Specify a custom URL to an icon
        'last_tab'             => '',
        // Force your panel to always open to a specific tab (by id)
        'page_icon'            => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug'            => '',
        // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
        'save_defaults'        => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show'         => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark'         => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export'   => true,
        // Shows the Import/Export panel when not used as a field.

        // CAREFUL -> These options are for advanced use only
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output_tag'           => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
        // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

        // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
        'database'             => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
        'use_cdn'              => true,
        // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

        // HINTS
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'red',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        )
    );


    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */


    /*
     * ---> START HELP TABS
     */

    $tabs = array(
        array(
            'id'      => 'redux-help-tab-1',
            'title'   => esc_html__( 'Theme Information 1', 'quanto' ),
            'content' => esc_html__( '<p>This is the tab content, HTML is allowed.</p>', 'quanto' )
        ),
        array(
            'id'      => 'redux-help-tab-2',
            'title'   => esc_html__( 'Theme Information 2', 'quanto' ),
            'content' => esc_html__( '<p>This is the tab content, HTML is allowed.</p>', 'quanto' )
        )
    );
    Redux::set_help_tab( $opt_name, $tabs );

    // Set the help sidebar
    $content = esc_html__( '<p>This is the sidebar content, HTML is allowed.</p>', 'quanto' );
    Redux::set_help_sidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */


    // -> START General Fields

    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Cursor', 'quanto' ),
        'id'               => 'quanto_cursor',
        'subsection'       => false,
        'icon'             => 'el el-hand-up',
        'fields'           => array(
            array(
                'id'       => 'quanto_display_cursor',
                'type'     => 'switch',
                'title'    => esc_html__( 'cursor', 'quanto' ),
                'subtitle' => esc_html__( 'Switch Enabled to Display cursor.', 'quanto' ),
                'default'  => true,
                'on'       => esc_html__('Enabled','quanto'),
                'off'      => esc_html__('Disabled','quanto'),
            ),
        )
    ));

    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Preloader', 'quanto' ),
        'id'               => 'quanto_preloader',
        'subsection'       => false,
        'icon'             => 'el el-refresh',
        'fields'           => array(
            array(
                'id'       => 'quanto_display_preloader',
                'type'     => 'switch',
                'title'    => esc_html__( 'Preloader', 'quanto' ),
                'subtitle' => esc_html__( 'Switch Enabled to Display Preloader.', 'quanto' ),
                'default'  => true,
                'on'       => esc_html__('Enabled','quanto'),
                'off'      => esc_html__('Disabled','quanto'),
            ),
            array(
                'id'       => 'preloader_image',
                'type'     => 'media',
                'title'    => esc_html__( 'Preloader Image', 'quanto' ),
                'subtitle' => esc_html__( 'Choose the Preloader image', 'quanto' ),
            ),
        )
    ));
    /* End General Fields */


    /* Start Color  Fields */
    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Brand Color', 'quanto' ),
        'id'               => 'quanto_brand_color',
        'customizer_width' => '450px',
        'icon'             => 'el el-brush',
        'fields'           => array(
            array(
                'id'       => 'quanto_primary',
                'type'     => 'color',
                'title'    => esc_html__( 'Primary Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Primary Theme Color', 'quanto' ),
                'default'  => '#0f0f0f',
            ),
            array(
                'id'       => 'quanto_heading_color',
                'type'     => 'color',
                'title'    => esc_html__( 'Heading Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Heading Color', 'quanto' ),
                'default'  => '#0f0f0f',
            ),
            array(
                'id'       => 'quanto_body_color',
                'type'     => 'color',
                'title'    => esc_html__( 'Body Text Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Body Text Color', 'quanto' ),
                'default'  => '#0f0f0f',
            ),

        )

    ) );
    /* End Color  Fields */

    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Typography', 'quanto' ),
        'id'               => 'quanto_typography',
        'customizer_width' => '450px',
        'icon'             => 'el el-text-height',
        'fields'           => array(
            array(
                'id'          => 'title_typography',
                'type'        => 'typography', 
                'title'       => __( 'Title Typography', 'quanto' ),
                'google'      => true, 
                'font-backup' => true,
                'output'      => array('
                    h1, h2, h3, h4, h5, h6,.h1, .h2, .h3, .h4, .h5, .h6
                '),
                'units'       =>'px',
                'subtitle'    => __('Typography option with each property can be called individually.', 'quanto'),
            ),
            array(
                'id'          => 'body_typography',
                'type'        => 'typography', 
                'title'       => __( 'Body Typography', 'quanto' ),
                'google'      => true, 
                'font-backup' => true,
                'output'      => array('
                    body,
                    label,
                    p,
                    span
                '),
                'units'       =>'px',
                'subtitle'    => __('Typography option with each property can be called individually.', 'quanto'),
            ),
        )

    ) );

    /* Admin Lebel Fields */
    Redux::setSection( $opt_name, array(
        'title'             => esc_html__( 'Admin Label', 'quanto' ),
        'id'                => 'quanto_admin_label',
        'customizer_width'  => '450px',
        'subsection'        => true,
        'fields'            => array(
            array(
                'title'     => esc_html__( 'Admin Login Logo', 'quanto' ),
                'subtitle'  => esc_html__( 'It belongs to the back-end of your website to log-in to admin panel.', 'quanto' ),
                'id'        => 'quanto_admin_login_logo',
                'type'      => 'media',
            ),
            array(
                'title'     => esc_html__( 'Custom CSS For admin', 'quanto' ),
                'subtitle'  => esc_html__( 'Any CSS your write here will run in admin.', 'quanto' ),
                'id'        => 'quanto_theme_admin_custom_css',
                'type'      => 'ace_editor',
                'mode'      => 'css',
                'theme'     => 'chrome',
                'full_width'=> true,
            ),
        ),
    ) );


    // -> START Basic Fields
    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Header', 'quanto' ),
        'id'               => 'quanto_header',
        'customizer_width' => '400px',
        'icon'             => 'el el-credit-card',
        'fields'           => array(
            array(
                'id'       => 'quanto_header_options',
                'type'     => 'button_set',
                'default'  => '1',
                'options'  => array(
                    "1"   => esc_html__('Prebuilt','quanto'),
                    "2"      => esc_html__('Header Builder','quanto'),
                ),
                'title'    => esc_html__( 'Global Header Options', 'quanto' ),
                'subtitle' => esc_html__( 'Select header options.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_header_select_options',
                'type'     => 'select',
                'data'     => 'posts',
                'args'     => array(
                    'post_type'     => 'quanto_header'
                ),
                'title'    => esc_html__( 'Global Header', 'quanto' ),
                'subtitle' => esc_html__( 'Select header.', 'quanto' ),
                'required' => array( 'quanto_header_options', 'equals', '2' )
            ),
            array(
                'id'       => 'quanto_archive_header_select_options',
                'type'     => 'select',
                'data'     => 'posts',
                'args'     => array(
                    'post_type' => 'quanto_header',
                ),
                'title'    => esc_html__( 'Archive Header', 'quanto' ),
                'subtitle' => esc_html__( 'Select header for archive pages.', 'quanto' ),
            ),
        ),
    ) );
    // -> START Header Logo
    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Logo', 'quanto' ),
        'id'               => 'quanto_header_logo_option',
        'subsection'       => true,
        'fields'           => array(

            array(
                'id'       => 'dark_logo',
                'type'     => 'media',
                'url'      => true,
                'title'    => esc_html__( 'Dark Logo', 'quanto' ),
                'subtitle' => esc_html__( 'Choose the site Dark logo', 'quanto' ),
                'default'  => array(
                    'url' => 'https://wp.framerpeak.com/quanto/wp-content/uploads/2025/03/dark-logo.svg',
                ),
            ),
            array(
                'id'       => 'white_logo',
                'type'     => 'media',
                'title'    => esc_html__( 'White Logo', 'quanto' ),
                'subtitle' => esc_html__( 'Choose the site White logo', 'quanto' ),
                'default'  => array(
                    'url' => 'https://wp.framerpeak.com/quanto/wp-content/uploads/2025/03/white-logo.svg',
                ),
            ),

            array(
                'id'       => 'logo_max_width_desktop',
                'type'     => 'slider',
                'units'    => array('px'),
                'title'    => esc_html__('Logo Dimensions (Width/Height).', 'quanto'),
                'output'   => array('.header-logo img'),
                'subtitle' => esc_html__('Set logo slider to choose width ', 'quanto'),
                "default"  => 150,
                "min"      => 0,
                "step"     => 2,
                "max"      => 270,
            ),

            array(
                'id'       => 'logo_max_width_mobile',
                'type'     => 'slider',
                'units'    => array('px'),
                'title'    => esc_html__('Mobile Device', 'quanto'),
                'output'   => array('.header-logo img'),
                'subtitle' => esc_html__('Set logo slider to choose width and unit.', 'quanto'),
                "default"  => 150,
                "min"      => 0,
                "step"     => 2,
                "max"      => 270,
            ),

            array(
                'id'       => 'quanto_text_title',
                'type'     => 'text',
                'validate' => 'html',
                'title'    => esc_html__( 'Text Logo', 'quanto' ),
                'subtitle' => esc_html__( 'Write your logo text use as logo', 'quanto' ),
            )
        )
    ) );
    // -> End Header Logo

    // -> START Blog Page
    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( 'Blog', 'quanto' ),
        'id'         => 'quanto_blog_page',
        'icon'  => 'el el-blogger',
        'fields'     => array(

            array(
                'id'       => 'quanto_blog_sidebar',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Layout', 'quanto' ),
                'subtitle' => esc_html__( 'Choose blog layout from here. If you use this option then you will able to change three type of blog layout ( Default Left Sidebar Layour ). ', 'quanto' ),
                'options'  => array(
                    '1' => array(
                        'alt' => esc_attr__('1 Column','quanto'),
                        'img' => esc_url( get_template_directory_uri(). '/assets/img/no-sideber.png')
                    ),
                    '2' => array(
                        'alt' => esc_attr__('2 Column Left','quanto'),
                        'img' => esc_url( get_template_directory_uri() .'/assets/img/left-sideber.png')
                    ),
                    '3' => array(
                        'alt' => esc_attr__('2 Column Right','quanto'),
                        'img' => esc_url(  get_template_directory_uri() .'/assets/img/right-sideber.png' )
                    ),

                ),
                'default'  => '3'
            ),
            array(
                'id'       => 'quanto_blog_grid',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Post Column', 'quanto' ),
                'subtitle' => esc_html__( 'Select your blog post column from here. If you use this option then you will able to select three type of blog post layout ( Default Two Column ).', 'quanto' ),
                //Must provide key => value(array:title|img) pairs for radio options
                'options'  => array(
                    '1' => array(
                        'alt' => esc_attr__('1 Column','quanto'),
                        'img' => esc_url( get_template_directory_uri(). '/assets/img/1column.png')
                    ),
                    '2' => array(
                        'alt' => esc_attr__('2 Column Left','quanto'),
                        'img' => esc_url( get_template_directory_uri() .'/assets/img/2column.png')
                    ),
                    '3' => array(
                        'alt' => esc_attr__('2 Column Right','quanto'),
                        'img' => esc_url(  get_template_directory_uri() .'/assets/img/3column.png' )
                    ),

                ),
                'default'  => '1'
            ),

            array(
                'id'      => 'quanto_blog_style',
                'type'     => 'select',
                'options'  => array(
                    'blog_style_one' => esc_html__('Blog Style One','quanto'),
                    'blog_style_two' => esc_html__('Blog Style Two','quanto'),
                ),
                'default'  => 'blog_style_one',
                'title'   => esc_html__('Blog Style', 'quanto'),
            ),
            array(
                'id'       => 'quanto_blog_section_title_switcher',
                'type'     => 'switch',
                'default'  => '0',
                'on'       => esc_html__('Show','quanto'),
                'off'      => esc_html__('Hide','quanto'),
                'title'    => esc_html__('Blog Section Title', 'quanto'),
                'subtitle' => esc_html__('Control blog Section title show / hide. If you use this option then you will able to show / hide your blog page title ( Default Setting Show ).', 'quanto'),
            ),
            array(
                'id'       => 'quanto_blog_section_custom_title',
                'type'     => 'text',
                'title'    => esc_html__('Blog Section Title', 'quanto'),
                'subtitle' => esc_html__('Set blog Section custom title form here. If you use this option then you will able to set your won title text.', 'quanto'),
                'required' => array('quanto_blog_section_title_switcher','equals','1')
            ),
            array(
                'id'       => 'quanto_blog_section_custom_title_tag',
                'type'     => 'select',
                'options'  => array(
                    'h1'        => esc_html__('H1','quanto'),
                    'h2'        => esc_html__('H2','quanto'),
                    'h3'        => esc_html__('H3','quanto'),
                    'h4'        => esc_html__('H4','quanto'),
                    'h5'        => esc_html__('H5','quanto'),
                    'h6'        => esc_html__('H6','quanto'),
                ),
                'default'  => 'h1',
                'title'    => esc_html__( 'Section Title Tag', 'quanto' ),
                'subtitle' => esc_html__( 'Select section title tag. If you use this option then you can able to change title tag H1 - H6 ( Default tag H1 )', 'quanto' ),
                'required' => array('quanto_blog_section_title_switcher','equals','1')
            ),
            array(
                'id'       => 'quanto_blog_section_title_color',
                'output'   => array( '.quanto-hero-blog__content h1'),
                'type'     => 'color',
                'title'    => esc_html__( 'Blog Section Title Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Blog Section Title Color.', 'quanto' ),
                'required' => array('quanto_blog_section_title_switcher','equals','1')
            ),
            array(
                'id'       => 'quanto_blog_page_title_switcher',
                'type'     => 'switch',
                'default'  => 1,
                'on'       => esc_html__('Show','quanto'),
                'off'      => esc_html__('Hide','quanto'),
                'title'    => esc_html__('Blog Page Title', 'quanto'),
                'subtitle' => esc_html__('Control blog page title show / hide. If you use this option then you will able to show / hide your blog page title ( Default Setting Show ).', 'quanto'),
            ),
            array(
                'id'       => 'quanto_blog_page_title_setting',
                'type'     => 'button_set',
                'title'    => esc_html__('Blog Page Title Setting', 'quanto'),
                'subtitle' => esc_html__('Control blog page title setting. If you use this option then you can able to show default or custom blog page title ( Default Blog ).', 'quanto'),
                'options'  => array(
                    "predefine"   => esc_html__('Default','quanto'),
                    "custom"      => esc_html__('Custom','quanto'),
                ),
                'default'  => 'predefine',
                'required' => array("quanto_blog_page_title_switcher","equals","1")
            ),
            array(
                'id'       => 'quanto_blog_page_custom_title',
                'type'     => 'text',
                'title'    => esc_html__('Blog Custom Title', 'quanto'),
                'subtitle' => esc_html__('Set blog page custom title form here. If you use this option then you will able to set your won title text.', 'quanto'),
                'required' => array('quanto_blog_page_title_setting','equals','custom')
            ),
            array(
                'id'            => 'quanto_blog_postExcerpt',
                'type'          => 'slider',
                'title'         => esc_html__('Blog Posts Excerpt', 'quanto'),
                'subtitle'      => esc_html__('Control the number of characters you want to show in the blog page for each post.. If you use this option then you can able to control your blog post characters from here ( Default show 10 ).', 'quanto'),
                "default"       => 46,
                "min"           => 0,
                "step"          => 1,
                "max"           => 100,
                'resolution'    => 1,
                'display_value' => 'text',
            ),
            array(
                'id'       => 'quanto_blog_readmore_setting',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Read More Text Setting', 'quanto' ),
                'subtitle' => esc_html__( 'Control read more text from here.', 'quanto' ),
                'options'  => array(
                    "default"   => esc_html__('Default','quanto'),
                    "custom"    => esc_html__('Custom','quanto'),
                ),
                'default'  => 'default',
            ),
            array(
                'id'       => 'quanto_blog_custom_readmore',
                'type'     => 'text',
                'title'    => esc_html__('Read More Text', 'quanto'),
                'subtitle' => esc_html__('Set read moer text here. If you use this option then you will able to set your won text.', 'quanto'),
                'required' => array('quanto_blog_readmore_setting','equals','custom')
            ),
            array(
                'id'       => 'quanto_blog_title_color',
                'output'   => array( '.quanto-blog-box .quanto-blog-content h5'),
                'type'     => 'color',
                'title'    => esc_html__( 'Blog Title Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Blog Title Color.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_blog_title_hover_color',
                'output'   => array( '.quanto-blog-box .quanto-blog-content h5:hover'),
                'type'     => 'color',
                'title'    => esc_html__( 'Blog Title Hover Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Blog Title Hover Color.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_blog_contant_color',
                'output'   => array( '.quanto-blog-box .quanto-blog-content .blog-text'),
                'type'     => 'color',
                'title'    => esc_html__( 'Blog Excerpt / Content Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Blog Excerpt / Content Color.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_blog_read_more_button_color',
                'output'   => array( '.quanto-link-btn.btn-pill, .quanto-link-btn.btn-pill span .arry1, .quanto-link-btn.btn-pill span .arry2'),
                'type'     => 'color',
                'title'    => esc_html__( 'Read More Button Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Read More Button Color.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_blog_read_more_button_bg_color',
                'type'     => 'color',
                'title'    => esc_html__( 'Button Background Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set button Background Color.', 'quanto' ),
                'output'   => array( 'background-color' =>'.quanto-link-btn.btn-pill' ),
            ),
            array(
                'id'       => 'quanto_blog_pagination_color',
                'output'   => array( '.blog-pagination .pagination .page-item .page-link'),
                'type'     => 'color',
                'title'    => esc_html__('Blog Pagination Color', 'quanto'),
                'subtitle' => esc_html__('Set Blog Pagination Color.', 'quanto'),
            ),
            array(
                'id'       => 'quanto_blog_pagination_bg_color',
                'type'     => 'color',
                'title'    => esc_html__( 'Pagination Background Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Pagination Background Color.', 'quanto' ),
                'output'   => array( 'background-color' =>'.blog-pagination .pagination .page-item .page-link:not(.next):not(.prev)' ),
            ),
            array(
                'id'       => 'quanto_blog_pagination_hover_color',
                'output'   => array( '.blog-pagination .pagination .page-item .page-link:hover, .blog-pagination .pagination .page-item .page-link.active'),
                'type'     => 'color',
                'title'    => esc_html__('Blog Pagination Hover Color', 'quanto'),
                'subtitle' => esc_html__('Set Blog Pagination Hover Color.', 'quanto'),
            ),
            array(
                'id'       => 'quanto_blog_pagination_hover_bg_color',
                'type'     => 'color',
                'title'    => esc_html__( 'Pagination Hover Background Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Pagination Hover Background Color.', 'quanto' ),
                'output'   => array( 'background-color' =>'.blog-pagination .pagination .page-item .page-link:hover:not(.next):not(.prev), .blog-pagination .pagination .page-item .page-link.active:not(.next):not(.prev)' ),
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( 'Single Blog Page', 'quanto' ),
        'id'         => 'quanto_post_detail_styles',
        'subsection' => true,
        'fields'     => array(

            array(
                'id'       => 'quanto_blog_single_sidebar',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Layout', 'quanto' ),
                'subtitle' => esc_html__( 'Choose blog single page layout from here. If you use this option then you will able to change three type of blog single page layout ( Default Left Sidebar Layour ). ', 'quanto' ),
                'options'  => array(
                    '1' => array(
                        'alt' => esc_attr__('1 Column','quanto'),
                        'img' => esc_url( get_template_directory_uri(). '/assets/img/no-sideber.png')
                    ),
                    '2' => array(
                        'alt' => esc_attr__('2 Column Left','quanto'),
                        'img' => esc_url( get_template_directory_uri() .'/assets/img/left-sideber.png')
                    ),
                    '3' => array(
                        'alt' => esc_attr__('2 Column Right','quanto'),
                        'img' => esc_url(  get_template_directory_uri() .'/assets/img/right-sideber.png' )
                    ),

                ),
                'default'  => '3'
            ),
            array(
                'id'       => 'quanto_post_details_title_position',
                'type'     => 'button_set',
                'default'  => 'header',
                'options'  => array(
                    'header'        => esc_html__('On Header','quanto'),
                    'below'         => esc_html__('Below Breadcrumb','quanto'),
                ),
                'title'    => esc_html__('Blog Post Title Position', 'quanto'),
                'subtitle' => esc_html__('Control blog post title position from here.', 'quanto'),
            ),
            array(
                'id'       => 'quanto_post_details_custom_title',
                'type'     => 'text',
                'title'    => esc_html__('Blog Details Custom Title', 'quanto'),
                'subtitle' => esc_html__('This title will show in Breadcrumb title.', 'quanto'),
                'required' => array('quanto_post_details_title_position','equals','below')
            ),
            array(
                'id'       => 'quanto_blog_details_title_column',
                'type'     => 'select',
                'title'    => __('Single Blog Column Width', 'your-textdomain'),
                'subtitle' => __('Choose the column width for the single blog content area.'),
                'options'  => array(
                    'col-xl-12 col-xxl-12' => 'Full Width (12 Columns)',
                    'col-xl-9 col-xxl-9'   => '9 Columns',
                    'col-xl-8 col-xxl-8'   => '8 Columns',
                    'col-xl-6 col-xxl-6'   => '6 Columns',
                ),
                'default'  => 'col-xl-9 col-xxl-9',
                'required' => array('quanto_post_details_title_position','equals','below')
            ),
            array(
                'id'       => 'quanto_display_post_tags',
                'type'     => 'switch',
                'title'    => esc_html__( 'Tags', 'quanto' ),
                'subtitle' => esc_html__( 'Switch On to Display Tags.', 'quanto' ),
                'default'  => true,
                'on'        => esc_html__('Enabled','quanto'),
                'off'       => esc_html__('Disabled','quanto'),
            ),
            array(
                'id'       => 'quanto_post_details_share_options',
                'type'     => 'switch',
                'title'    => esc_html__('Share Options', 'quanto'),
                'subtitle' => esc_html__('Control post share options from here. If you use this option then you will able to show or hide post share options.', 'quanto'),
                'on'        => esc_html__('Show','quanto'),
                'off'       => esc_html__('Hide','quanto'),
                'default'   => '0',
            ),
            array(
                'id'       => 'quanto_post_details_related_post',
                'type'     => 'switch',
                'title'    => esc_html__('Related Post', 'quanto'),
                'subtitle' => esc_html__('Control related post from here. If you use this option then you will able to show or hide related post ( Default setting Show ).', 'quanto'),
                'on'        => esc_html__('Show','quanto'),
                'off'       => esc_html__('Hide','quanto'),
                'default'   => false,
            ),
        )
    ));

    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( 'Meta Data', 'quanto' ),
        'id'         => 'quanto_common_meta_data',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'quanto_blog_meta_before_color',
                'output'   => array( '.quanto-blog-box .quanto-blog-content .quanto-blog-date::before'),
                'type'     => 'color',
                'title'    => esc_html__('Blog Meta before Color', 'quanto'),
                'subtitle' => esc_html__('Set Blog Meta before Color.', 'quanto'),
            ),
            array(
                'id'       => 'quanto_blog_meta_text_color',
                'output'   => array( '.quanto-blog-box .quanto-blog-content span, .blog-item .meta-box ul.meta-info li span a'),
                'type'     => 'color',
                'title'    => esc_html__( 'Blog Meta Text Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Blog Meta Text Color.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_blog_meta_text_hover_color',
                'output'   => array( '.blog-item .meta-box ul.meta-info li span a:hover'),
                'type'     => 'color',
                'title'    => esc_html__( 'Blog Meta Hover Text Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Blog Meta Hover Text Color.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_display_post_date',
                'type'     => 'switch',
                'title'    => esc_html__( 'Post Date', 'quanto' ),
                'subtitle' => esc_html__( 'Switch On to Display Post Date.', 'quanto' ),
                'default'  => true,
                'on'        => esc_html__('Enabled','quanto'),
                'off'       => esc_html__('Disabled','quanto'),
            ),
            array(
                'id'       => 'quanto_display_post_details_date',
                'type'     => 'switch',
                'title'    => esc_html__( 'Post Details Date', 'quanto' ),
                'subtitle' => esc_html__( 'Switch On to Display Post Details Date.', 'quanto' ),
                'default'  => true,
                'on'        => esc_html__('Enabled','quanto'),
                'off'       => esc_html__('Disabled','quanto'),
            ),
            array(
                'id'       => 'quanto_display_post_author',
                'type'     => 'switch',
                'title'    => esc_html__( 'Post Details Author', 'quanto' ),
                'subtitle' => esc_html__( 'Switch On to Display Post Details Author.', 'quanto' ),
                'default'  => false,
                'on'        => esc_html__( 'Enabled', 'quanto'),
                'off'       => esc_html__( 'Disabled', 'quanto'),
            ),
            array(
                'id'       => 'quanto_display_post_category',
                'type'     => 'switch',
                'title'    => esc_html__( 'Post Category', 'quanto' ),
                'subtitle' => esc_html__( 'Switch On to Display Category.', 'quanto' ),
                'default'  => true,
                'on'        => esc_html__('Enabled','quanto'),
                'off'       => esc_html__('Disabled','quanto'),
            ),
            array(
                'id'       => 'quanto_display_post_details_category',
                'type'     => 'switch',
                'title'    => esc_html__( 'Post Details Category', 'quanto' ),
                'subtitle' => esc_html__( 'Switch On to Display Category.', 'quanto' ),
                'default'  => true,
                'on'        => esc_html__('Enabled','quanto'),
                'off'       => esc_html__('Disabled','quanto'),
            ),
        )
    ));

    /* Sidebar Start */
    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( 'Sidebar Options', 'quanto' ),
        'id'         => 'quanto_sidebar_options',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'      => 'quanto_sidebar_bg_color',
                'type'    => 'color',
                'title'   => esc_html__('Widgets Background Color', 'quanto'),
                'output'  => array('background-color'   => '.blog-sidebar')
            ),
            array(
                'id'      => 'quanto_sidebar_padding_margin_box_shadow_trigger',
                'type'    => 'switch',
                'title'   => esc_html__('Widgets Custom Box Shadow/Padding/Margin/border', 'quanto'),
                'on'      => esc_html__('Show','quanto'),
                'off'     => esc_html__('Hide','quanto'),
                'default' => false
            ),
            array(
                'id'      => 'box-shadow',
                'type'    => 'box_shadow',
                'title'   => esc_html__('Box Shadow', 'quanto'),
                'units'   => array( 'px', 'em', 'rem' ),
                'output'  => ( '.blog-sidebar .widget' ),
                'opacity' => true,
                'rgba'    => true,
                'required'=> array( 'quanto_sidebar_padding_margin_box_shadow_trigger', 'equals' , '1' )
            ),
            array(
                'id'      => 'quanto_sidebar_widget_margin',
                'type'    => 'spacing',
                'title'   => esc_html__('Widget Margin', 'quanto'),
                'units'   => array('em', 'px'),
                'output'  => ( '.blog-sidebar .widget' ),
                'mode'    => 'margin',
                'required'=> array( 'quanto_sidebar_padding_margin_box_shadow_trigger', 'equals' , '1' )
            ),
            array(
                'id'      => 'quanto_sidebar_widget_padding',
                'type'    => 'spacing',
                'title'   => esc_html__('Widget Padding', 'quanto'),
                'units'   => array('em', 'px'),
                'output'  => ( '.blog-sidebar .widget' ),
                'mode'    => 'padding',
                'required'=> array( 'quanto_sidebar_padding_margin_box_shadow_trigger', 'equals' , '1' )
            ),
            array(
                'id'      => 'quanto_sidebar_widget_border',
                'type'    => 'border',
                'title'   => esc_html__('Widget Border', 'quanto'),
                'units'   => array('em', 'px'),
                'output'  => ( '.blog-sidebar .widget' ),
                'all'     => false,
                'required'=> array( 'quanto_sidebar_padding_margin_box_shadow_trigger', 'equals' , '1' )
            ),
            array(
                'id'      => 'quanto_sidebar_widget_title_margin',
                'type'    => 'spacing',
                'title'   => esc_html__('Widget Title Margin', 'quanto'),
                'mode'    => 'margin',
                'output'  => array('.blog-sidebar .widget .widget_title'),
                'units'   => array('em', 'px'),
            ),
            array(
                'id'      => 'quanto_sidebar_widget_title_padding',
                'type'    => 'spacing',
                'title'   => esc_html__('Widget Title Padding', 'quanto'),
                'mode'    => 'padding',
                'output'  => array('.blog-sidebar .widget .widget_title'),
                'units'   => array('em', 'px'),
            ),
            array(
                'id'       => 'quanto_sidebar_widget_title_color',
                'output'   =>  array('.blog-sidebar .widget .widget_title h1,.blog-sidebar .widget .widget_title h2,.blog-sidebar .widget .widget_title h3,.blog-sidebar .widget .widget_title h4,.blog-sidebar .widget .widget_title h5,.blog-sidebar .widget .widget_title h6'),
                'type'     => 'color',
                'title'    => esc_html__('Widget Title Color', 'quanto'),
                'subtitle' => esc_html__('Set Widget Title Color.', 'quanto'),
            ),
            array(
                'id'       => 'quanto_sidebar_widget_text_color',
                'output'   => array('.blog-sidebar .widget'),
                'type'     => 'color',
                'title'    => esc_html__('Widget Text Color', 'quanto'),
                'subtitle' => esc_html__('Set Widget Text Color.', 'quanto'),
            ),
            array(
                'id'       => 'quanto_sidebar_widget_anchor_color',
                'type'     => 'color',
                'output'   => array('.blog-sidebar .widget a'),
                'title'    => esc_html__('Widget Anchor Color', 'quanto'),
                'subtitle' => esc_html__('Set Widget Anchor Color.', 'quanto'),
            ),
            array(
                'id'       => 'quanto_sidebar_widget_anchor_hover_color',
                'type'     => 'color',
                'output'   => array('.blog-sidebar .widget a:hover'),
                'title'    => esc_html__('Widget Hover Color', 'quanto'),
                'subtitle' => esc_html__('Set Widget Anchor Hover Color.', 'quanto'),
            )
        )
    ));
    /* Sidebar End */

    /* End blog Page */

    // -> START Page Option
    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( 'Page', 'quanto' ),
        'id'         => 'quanto_page_page',
        'icon'  => 'el el-file',
        'fields'     => array(
            array(
                'id'       => 'quanto_page_sidebar',
                'type'     => 'image_select',
                'title'    => esc_html__( 'Select layout', 'quanto' ),
                'subtitle' => esc_html__( 'Choose your page layout. If you use this option then you will able to choose three type of page layout ( Default no sidebar ). ', 'quanto' ),
                //Must provide key => value(array:title|img) pairs for radio options
                'options'  => array(
                    '1' => array(
                        'alt' => esc_attr__('1 Column','quanto'),
                        'img' => esc_url( get_template_directory_uri(). '/assets/img/no-sideber.png')
                    ),
                    '2' => array(
                        'alt' => esc_attr__('2 Column Left','quanto'),
                        'img' => esc_url( get_template_directory_uri() .'/assets/img/left-sideber.png')
                    ),
                    '3' => array(
                        'alt' => esc_attr__('2 Column Right','quanto'),
                        'img' => esc_url(  get_template_directory_uri() .'/assets/img/right-sideber.png' )
                    ),

                ),
                'default'  => '1'
            ),
            array(
                'id'       => 'quanto_page_layoutopt',
                'type'     => 'button_set',
                'title'    => esc_html__('Sidebar Settings', 'quanto'),
                'subtitle' => esc_html__('Set page sidebar. If you use this option then you will able to set three type of sidebar ( Default no sidebar ).', 'quanto'),
                //Must provide key => value pairs for options
                'options' => array(
                    '1' => esc_html__( 'Page Sidebar', 'quanto' ),
                    '2' => esc_html__( 'Blog Sidebar', 'quanto' )
                 ),
                'default' => '1',
                'required'  => array('quanto_page_sidebar','!=','1')
            ),
            array(
                'id'       => 'quanto_page_title_switcher',
                'type'     => 'switch',
                'title'    => esc_html__('Title', 'quanto'),
                'subtitle' => esc_html__('Switch enabled to display page title. Fot this option you will able to show / hide page title.  Default setting Enabled', 'quanto'),
                'default'  => '1',
                'on'        => esc_html__('Enabled','quanto'),
                'off'       => esc_html__('Disabled','quanto'),
            ),
            array(
                'id'       => 'quanto_page_title_tag',
                'type'     => 'select',
                'options'  => array(
                    'h1'        => esc_html__('H1','quanto'),
                    'h2'        => esc_html__('H2','quanto'),
                    'h3'        => esc_html__('H3','quanto'),
                    'h4'        => esc_html__('H4','quanto'),
                    'h5'        => esc_html__('H5','quanto'),
                    'h6'        => esc_html__('H6','quanto'),
                ),
                'default'  => 'h1',
                'title'    => esc_html__( 'Title Tag', 'quanto' ),
                'subtitle' => esc_html__( 'Select page title tag. If you use this option then you can able to change title tag H1 - H6 ( Default tag H1 )', 'quanto' ),
                'required' => array("quanto_page_title_switcher","equals","1")
            ),
            array(
                'id'       => 'quanto_allHeader_title_color',
                'type'     => 'color',
                'title'    => esc_html__( 'Title Color', 'quanto' ),
                'subtitle' => esc_html__( 'Set Title Color', 'quanto' ),
                'output'   => array( 'color' => '.breadcumb-title' ),
            ),
            array(
                'id'       => 'quanto_allHeader_bg',
                'type'     => 'background',
                'title'    => esc_html__( 'Background', 'quanto' ),
                'output'   => array('.breadcumb-wrapper'),
                'subtitle' => esc_html__( 'Setting page header background. If you use this option then you will able to set Background Color, Background Image, Background Repeat, Background Size, Background Attachment, Background Position.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_full_breadcrumb_switcher',
                'type'     => 'switch',
                'default'  => 1,  // ON by default
                'on'       => esc_html__('Show','quanto'),
                'off'      => esc_html__('Hide','quanto'),
                'title'    => esc_html__( 'Full Breadcrumb Hide/Show', 'quanto' ),
                'subtitle' => esc_html__( 'Hide / Show full breadcrumb from all pages and posts.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_enable_breadcrumb',
                'type'     => 'switch',
                'title'    => esc_html__( 'Breadcrumb Hide/Show', 'quanto' ),
                'subtitle' => esc_html__( 'Hide / Show breadcrumb from all pages and posts ( Default settings hide ).', 'quanto' ),
                'default'  => '1',
                'on'       => 'Show',
                'off'      => 'Hide',
            ),
            array(
                'id'      => 'quanto_breadcrumb_padding',
                'type'    => 'spacing',
                'title'   => esc_html__('Breadcrumb Padding', 'quanto'),
                'units'   => array('em', 'px'),
                'output'  => array('.breadcumb-wrapper'), 
                'mode'    => 'padding',
            ),
            array(
                'id'      => 'quanto_breadcrumb_details_padding',
                'type'    => 'spacing',
                'title'   => esc_html__('Details Page Breadcrumb Padding', 'quanto'),
                'units'   => array('em', 'px'),
                'output'  => array('.breadcumb-wrapper.detail-page'), 
                'mode'    => 'padding',
            ),
            array(
                'id'       => 'quanto_allHeader_breadcrumbtextcolor',
                'type'     => 'color',
                'title'    => esc_html__( 'Breadcrumb Color', 'quanto' ),
                'subtitle' => esc_html__( 'Choose page header breadcrumb text color here.If you user this option then you will able to set page breadcrumb color.', 'quanto' ),
                'required' => array("quanto_page_title_switcher","equals","1"),
                'output'   => array( 'color' => '.breadcumb-menu-wrap .breadcumb-menu ul li a' ),
            ),
            array(
                'id'       => 'quanto_allHeader_breadcrumbtextactivecolor',
                'type'     => 'color',
                'title'    => esc_html__( 'Breadcrumb Active Color', 'quanto' ),
                'subtitle' => esc_html__( 'Choose page header breadcrumb text active color here.If you user this option then you will able to set page breadcrumb active color.', 'quanto' ),
                'required' => array( "quanto_page_title_switcher", "equals", "1" ),
                'output'   => array( 'color' => '.breadcumb-menu-wrap .breadcumb-menu ul li.active' ),
            ),
            array(
                'id'       => 'quanto_allHeader_dividercolor',
                'type'     => 'color',
                'output'   => array( 'color'=>'.breadcumb-menu-wrap .breadcumb-menu ul .arrow i' ),
                'title'    => esc_html__( 'Breadcrumb Divider Color', 'quanto' ),
                'subtitle' => esc_html__( 'Choose breadcrumb divider color.', 'quanto' ),
            ),
        ),
    ) );
    /* End Page option */

    // -> START 404 Page

    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( '404 Page', 'quanto' ),
        'id'         => 'quanto_404_page',
        'icon'       => 'el el-ban-circle',
        'fields'     => array(
            array(
                'title'     => esc_html__( 'Error Image', 'quanto' ),
                'subtitle'  => esc_html__( 'Add Your 404 Page Image', 'quanto' ),
                'id'        => 'quanto_error_img',
                'type'      => 'media',
            ),
            array(
                'title'     => esc_html__( 'Bottom Side Image', 'quanto' ),
                'subtitle'  => esc_html__( 'Add Your 404 Page Bottom Side Image', 'quanto' ),
                'id'        => 'quanto_error_bottom_img',
                'type'      => 'media',
            ),
            array(
                'id'       => 'quanto_fof_title',
                'type'     => 'text',
                'title'    => esc_html__( 'Page Title', 'quanto' ),
                'subtitle' => esc_html__( 'Set Page Title', 'quanto' ),
                'default'  => esc_html__( 'Oops! That page can\'t be found.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_fof_subtitle',
                'type'     => 'text',
                'title'    => esc_html__( 'Page Subtitle', 'quanto' ),
                'subtitle' => esc_html__( 'Set Page Subtitle ', 'quanto' ),
                'default'  => esc_html__( 'The page you\'ve requested is not available.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_fof_btn_text',
                'type'     => 'text',
                'title'    => esc_html__( 'Button Text', 'quanto' ),
                'subtitle' => esc_html__( 'Set Button Text ', 'quanto' ),
                'default'  => esc_html__( 'Return To Home', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_fof_title_color',
                'type'     => 'color',
                'output'   => array( '.error__content .title' ),
                'title'    => esc_html__( 'Title Color', 'quanto' ),
                'subtitle' => esc_html__( 'Pick a title color', 'quanto' ),
                'validate' => 'color'
            ),
            array(
                'id'       => 'quanto_fof_text_color',
                'type'     => 'color',
                'output'   => array( '.error__content p' ),
                'title'    => esc_html__( 'Text Color', 'quanto' ),
                'subtitle' => esc_html__( 'Pick a text color', 'quanto' ),
                'validate' => 'color'
            ),
            array(
                'id'       => 'quanto_fof_btn_color',
                'type'     => 'color',
                'output'   => array( '.quanto-link-btn.btn-pill, .quanto-link-btn.btn-pill span .arry1' ),
                'title'    => esc_html__('Button Color', 'quanto'),
                'subtitle' => esc_html__('Pick Button Color', 'quanto'),
                'validate' => 'color'
            ),
            array(
                'id'       => 'quanto_fof_btn_color_hover',
                'type'     => 'color',
                'output'   => array( '.quanto-link-btn.btn-pill:hover, .quanto-link-btn.btn-pill span .arry2' ),
                'title'    => esc_html__( 'Button Hover Color', 'quanto'),
                'subtitle' => esc_html__( 'Pick Button Color', 'quanto'),
                'validate' => 'color'
            ),
            array(
                'id'       => 'quanto_fof_btn_bg_color',
                'type'     => 'color',
                'title'    => esc_html__( 'Button Background Color', 'quanto' ),
                'subtitle' => esc_html__( 'Pick Button Background Color', 'quanto' ),
                'output'   => array( 'background-color' => '.quanto-link-btn.btn-pill' ),
                'validate' => 'color'
            ),
            array(
                'id'       => 'quanto_fof_btn_hover_bg_color',
                'type'     => 'color',
                'title'    => esc_html__( 'Button Hover Background Color', 'quanto' ),
                'subtitle' => esc_html__( 'Pick Button Hover Background Color', 'quanto' ),
                'output'   => array( 'background-color' => '.quanto-link-btn.btn-pill:hover' ),
                'validate' => 'color'
            ),
        ),
    ) );

    /* End 404 Page */
    
    // -> START Social Media

    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( 'Social', 'quanto' ),
        'id'         => 'quanto_social_media',
        'icon'      => 'el el-globe',
        'desc'      => esc_html__( 'Social', 'quanto' ),
        'fields'     => array(
            array(
                'id'          => 'quanto_social_links',
                'type'        => 'slides',
                'title'       => esc_html__('Social Profile Links', 'quanto'),
                'subtitle'    => esc_html__('Add social icon and url.', 'quanto'),
                'show'        => array(
                    'title'          => true,
                    'description'    => true,
                    'progress'       => false,
                    'facts-number'   => false,
                    'facts-title1'   => false,
                    'facts-title2'   => false,
                    'facts-number-2' => false,
                    'facts-title3'   => false,
                    'facts-number-3' => false,
                    'url'            => true,
                    'project-button' => false,
                    'image_upload'   => false,
                ),
                'placeholder'   => array(
                    'icon'          => esc_html__( 'Icon (example: fa fa-facebook) ','quanto'),
                    'title'         => esc_html__( 'Social Icon Class', 'quanto' ),
                    'description'   => esc_html__( 'Social Icon Title', 'quanto' ),
                ),
            ),
        ),
    ) );

    // -> START Footer Gallery Widget
    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( 'Footer Widget', 'quanto' ),
        'id'         => 'quanto_gallery_media',
        'icon'      => 'el el-camera',
        'desc'      => esc_html__( 'Footer Widget', 'quanto' ),
        'fields'     => array(
            array(
                'id'          => 'quanto_gallery_image_widget',
                'type'        => 'slides',
                'title'       => esc_html__('Gallery Images', 'quanto'),
                'show'        => array(
                    'title'          => false,
                    'description'    => false,
                    'progress'       => false,
                    'facts-number'   => false,
                    'facts-title1'   => false,  
                    'facts-title2'   => false,
                    'facts-number-2' => false,
                    'facts-title3'   => false,
                    'facts-number-3' => false,
                    'url'            => false,
                    'project-button' => false,
                    'image_upload'   => true,
                ),
            ),
        ),
    ) );


    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Video Box', 'quanto' ),
        'id'               => 'quanto_video_box',
        'subsection'       => true,
        'fields'           => array(
            array(
                'id'       => 'video_image',
                'type'     => 'media',
                'title'    => esc_html__( 'Image', 'quanto' ),
            ),
            array(
                'id'       => 'video_url',
                'type'     => 'text',
                'title'    => esc_html__( 'Video Url', 'quanto' ),
                'subtitle' => esc_html__( 'Youtube Video Url', 'quanto' ),
             ),
            array(
                'id'       => 'video_title',
                'type'     => 'text',
                'title'    => esc_html__( 'Title', 'quanto' ),
                'default'  => esc_html__('A very warm welcome to our new Treasurer', 'quanto'),
             ),
        )
    ));

    // -> START Footer Media
    Redux::setSection( $opt_name , array(
       'title'            => esc_html__( 'Footer', 'quanto' ),
       'id'               => 'quanto_footer',
       'desc'             => esc_html__( 'quanto Footer', 'quanto' ),
       'customizer_width' => '400px',
       'icon'              => 'el el-photo',
   ) );

   Redux::setSection( $opt_name, array(
       'title'      => esc_html__( 'Pre-built Footer / Footer Builder', 'quanto' ),
       'id'         => 'quanto_footer_section',
       'subsection' => true,
       'fields'     => array(
            array(
               'id'       => 'quanto_footer_builder_trigger',
               'type'     => 'button_set',
               'default'  => 'prebuilt',
               'options'  => array(
                   'footer_builder'        => esc_html__('Footer Builder','quanto'),
                   'prebuilt'              => esc_html__('Pre-built Footer','quanto'),
               ),
               'title'    => esc_html__( 'Global Footer Builder', 'quanto' ),
            ),
            array(
               'id'       => 'quanto_footer_builder_select',
               'type'     => 'select',
               'required' => array( 'quanto_footer_builder_trigger','equals','footer_builder'),
               'data'     => 'posts',
               'args'     => array(
                   'post_type'     => 'quanto_footer'
               ),
               'on'       => esc_html__( 'Enabled', 'quanto' ),
               'off'      => esc_html__( 'Disable', 'quanto' ),
               'title'    => esc_html__( 'Select Global Footer', 'quanto' ),
               'subtitle' => esc_html__( 'First make your footer from footer custom types then select it from here.', 'quanto' ),
            ),
            array(
                'id'       => 'quanto_archive_footer_select_options',
                'type'     => 'select',
                'data'     => 'posts',
                'args'     => array(
                    'post_type' => 'quanto_footer',
                ),
                'title'    => esc_html__( 'Archive footer', 'quanto' ),
                'subtitle' => esc_html__( 'Select footer for archive pages.', 'quanto' ),
            ),
            array(
               'id'       => 'quanto_disable_footer_bottom',
               'type'     => 'switch',
               'title'    => esc_html__( 'Footer Bottom?', 'quanto' ),
               'default'  => 1,
               'on'       => esc_html__('Enabled','quanto'),
               'off'      => esc_html__('Disable','quanto'),
               'required' => array('quanto_footer_builder_trigger','equals','prebuilt'),
            ),
            array(
               'id'       => 'quanto_footer_bottom_background',
               'type'     => 'color',
               'title'    => esc_html__( 'Footer Bottom Background Color', 'quanto' ),
               'required' => array( 'quanto_disable_footer_bottom','=','1' ),
               'output'   => array( 'background-color'   =>   '.footer-copyright' ),
            ),
            array(
               'id'       => 'quanto_copyright_text',
               'type'     => 'text',
               'title'    => esc_html__( 'Copyright Text', 'quanto' ),
               'subtitle' => esc_html__( 'Add Copyright Text', 'quanto' ),
               'default'  => sprintf( 'Copyright &copy; %s <a href="%s">%s</a>. All Rights Reserved.', date('Y'), esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) ),
               'required' => array( 'quanto_disable_footer_bottom','equals','1' ),
            ),
            array(
               'id'       => 'quanto_footer_copyright_color',
               'type'     => 'color',
               'title'    => esc_html__( 'Footer Copyright Text Color', 'quanto' ),
               'subtitle' => esc_html__( 'Set footer copyright text color', 'quanto' ),
               'required' => array( 'quanto_disable_footer_bottom','equals','1'),
               'output'   => array( '.footer-copyright p' ),
            ),
            array(
               'id'       => 'quanto_footer_copyright_acolor',
               'type'     => 'color',
               'title'    => esc_html__( 'Footer Copyright Ancor Color', 'quanto' ),
               'subtitle' => esc_html__( 'Set footer copyright ancor color', 'quanto' ),
               'required' => array( 'quanto_disable_footer_bottom','equals','1'),
               'output'   => array( '.footer-copyright p a' ),
            ),
            array(
               'id'       => 'quanto_footer_copyright_a_hover_color',
               'type'     => 'color',
               'title'    => esc_html__( 'Footer Copyright Ancor Hover Color', 'quanto' ),
               'subtitle' => esc_html__( 'Set footer copyright ancor Hover color', 'quanto' ),
               'required' => array( 'quanto_disable_footer_bottom','equals','1'),
               'output'   => array( '.footer-copyright p a:hover' ),
            ),

       ),
   ) );


    /* End Footer Media */

    // -> START Custom Css
    Redux::setSection( $opt_name, array(
        'title'      => esc_html__( 'Custom Css', 'quanto' ),
        'id'         => 'quanto_custom_css_section',
        'icon'  => 'el el-css',
        'fields'     => array(
            array(
                'id'       => 'quanto_css_editor',
                'type'     => 'ace_editor',
                'title'    => esc_html__('CSS Code', 'quanto'),
                'subtitle' => esc_html__('Paste your CSS code here.', 'quanto'),
                'mode'     => 'css',
                'theme'    => 'monokai',
            )
        ),
    ) );

    /* End custom css */



    if ( file_exists( dirname( __FILE__ ) . '/../README.md' ) ) {
        $section = array(
            'icon'   => 'el el-list-alt',
            'title'  => __( 'Documentation', 'quanto' ),
            'fields' => array(
                array(
                    'id'       => '17',
                    'type'     => 'raw',
                    'markdown' => true,
                    'content_path' => dirname( __FILE__ ) . '/../README.md', // FULL PATH, not relative please
                    //'content' => 'Raw content here',
                ),
            ),
        );
        Redux::setSection( $opt_name, $section );
    }
    /*
     * <--- END SECTIONS
     */


    /*
     *
     * YOU MUST PREFIX THE FUNCTIONS BELOW AND ACTION FUNCTION CALLS OR ANY OTHER CONFIG MAY OVERRIDE YOUR CODE.
     *
     */

    /**
     * This is a test function that will let you see when the compiler hook occurs.
     * It only runs if a field    set with compiler=>true is changed.
     * */
    if ( ! function_exists( 'compiler_action' ) ) {
        function compiler_action( $options, $css, $changed_values ) {
            echo '<h1>The compiler hook has run!</h1>';
            echo "<pre>";
            print_r( $changed_values ); // Values that have changed since the last save
            echo "</pre>";
            //print_r($options); //Option values
            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )
        }
    }

    /**
     * Custom function for the callback validation referenced above
     * */
    if ( ! function_exists( 'redux_validate_callback_function' ) ) {
        function redux_validate_callback_function( $field, $value, $existing_value ) {
            $error   = false;
            $warning = false;

            //do your validation
            if ( $value == 1 ) {
                $error = true;
                $value = $existing_value;
            } elseif ( $value == 2 ) {
                $warning = true;
                $value   = $existing_value;
            }

            $return['value'] = $value;

            if ( $error == true ) {
                $field['msg']    = 'your custom error message';
                $return['error'] = $field;
            }

            if ( $warning == true ) {
                $field['msg']      = 'your custom warning message';
                $return['warning'] = $field;
            }

            return $return;
        }
    }

    /**
     * Custom function for the callback referenced above
     */
    if ( ! function_exists( 'redux_my_custom_field' ) ) {
        function redux_my_custom_field( $field, $value ) {
            print_r( $field );
            echo '<br/>';
            print_r( $value );
        }
    }

    /**
     * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
     * Simply include this function in the child themes functions.php file.
     * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
     * so you must use get_template_directory_uri() if you want to use any of the built in icons
     * */
    if ( ! function_exists( 'dynamic_section' ) ) {
        function dynamic_section( $sections ) {
            //$sections = array();
            $sections[] = array(
                'title'  => __( 'Section via hook', 'quanto' ),
                'desc'   => __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'quanto' ),
                'icon'   => 'el el-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }
    }

    /**
     * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
     * */
    if ( ! function_exists( 'change_arguments' ) ) {
        function change_arguments( $args ) {
            //$args['dev_mode'] = true;

            return $args;
        }
    }

    /**
     * Filter hook for filtering the default value of any given field. Very useful in development mode.
     * */
    if ( ! function_exists( 'change_defaults' ) ) {
        function change_defaults( $defaults ) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }
    }