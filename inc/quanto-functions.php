<?php

/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */


// Block direct access
if( ! defined( 'ABSPATH' ) ){
    exit;
}

 // theme option callback
function quanto_opt( $id = null, $url = null ){
    global $quanto_opt;

    if( $id && $url ){

        if( isset( $quanto_opt[$id][$url] ) && $quanto_opt[$id][$url] ){
            return $quanto_opt[$id][$url];
        }
    }else{
        if( isset( $quanto_opt[$id] )  && $quanto_opt[$id] ){
            return $quanto_opt[$id];
        }
    }
}


// theme logo
function quanto_theme_logo() {
    // escaping allow html
    $allowhtml = array(
        'a'    => array(
            'href' => array()
        ),
        'span' => array(),
        'i'    => array(
            'class' => array()
        )
    );
    $siteUrl = home_url('/');
    if( has_custom_logo() ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $siteLogo = '';
        $siteLogo .= '<a class="logo" href="'.esc_url( $siteUrl ).'">';
        $siteLogo .= quanto_img_tag( array(
            "class" => "img-fluid logo-img",
            "url"   => esc_url( wp_get_attachment_image_url( $custom_logo_id, 'full') )
        ) );
        $siteLogo .= '</a>';

        return $siteLogo;
    } elseif( !quanto_opt('quanto_text_title') && quanto_opt('dark_logo', 'url' )  ){
        $siteLogo = '<img class="" src="'.esc_url( quanto_opt('dark_logo', 'url' ) ).'" alt="'.esc_attr__( 'logo', 'quanto' ).'" />';
        return '<a class="" href="'.esc_url( $siteUrl ).'">'.$siteLogo.'</a>';
    }elseif( quanto_opt('quanto_text_title') ){
        return '<h5><a class="logo" href="'.esc_url( $siteUrl ).'">'.wp_kses( quanto_opt('quanto_text_title'), $allowhtml ).'</a></h5>';
    }else{
        return '<h5><a class="logo" href="'.esc_url( $siteUrl ).'">'.esc_html( get_bloginfo('name') ).'</a></h5>';
    }
}

// Quanto Coming Soon Logo
function quanto_coming_soon_logo() {
    // escaping allow html
    $allowhtml = array(
        'a'    => array(
            'href' => array()
        ),
        'span' => array(),
        'i'    => array(
            'class' => array()
        )
    );
    $siteUrl = home_url('/');
    // site logo
    if( quanto_opt( 'quanto_coming_logo', 'url' )  ){

        $siteLogo = '<img src="'.esc_url( quanto_opt('quanto_coming_logo', 'url' ) ).'" alt="'.esc_attr__( 'logo', 'quanto' ).'" />';

        return '<a class="logo" href="'.esc_url( $siteUrl ).'">'.$siteLogo.'</a>';

    }elseif( quanto_opt('quanto_coming_site_title') ){
        return '<h2 class="mb-0"><a class="text-logo" href="'.esc_url( $siteUrl ).'">'.wp_kses( quanto_opt('quanto_coming_site_title'), $allowhtml ).'</a></h2>';
    }else{
        return '<h2 class="mb-0"><a class="text-logo" href="'.esc_url( $siteUrl ).'">'.esc_html( get_bloginfo('name') ).'</a></h2>';
    }
}

// custom meta id callback
function quanto_meta( $id = '' ){
    $value = get_post_meta( get_the_ID(), '_quanto_'.$id, true );
    return $value;
}


// Robust Elementor Template Shortcode with forced CSS
function quanto_elementor_template_shortcode( $atts ) {
    $id = isset( $atts['id'] ) ? intval( $atts['id'] ) : 0;
    if ( ! $id || ! class_exists( '\\Elementor\\Plugin' ) ) {
        return '';
    }
    
    // The second parameter 'true' forces Elementor to output inline CSS for the template.
    // This is much safer than echoing a raw <link> tag which can break layout.
    return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id, true );
}
add_shortcode( 'quanto-template', 'quanto_elementor_template_shortcode' );


// Blog Date Permalink
function quanto_blog_date_permalink() {
    $year  = get_the_time('Y');
    $month_link = get_the_time('m');
    $day   = get_the_time('d');
    $link = get_day_link( $year, $month_link, $day);
    return $link;
}

//audio format iframe match
function quanto_iframe_match() {
    $audio_content = quanto_embedded_media( array('audio', 'iframe') );
    $iframe_match = preg_match("/\iframe\b/i",$audio_content, $match);
    return $iframe_match;
}


//Post embedded media
function quanto_embedded_media( $type = array() ){
    $content = do_shortcode( apply_filters( 'the_content', get_the_content() ) );
    $embed   = get_media_embedded_in_content( $content, $type );


    if( in_array( 'audio' , $type) ){
        if( count( $embed ) > 0 ){
            $output = str_replace( '?visual=true', '?visual=false', $embed[0] );
        }else{
           $output = '';
        }

    }else{
        if( count( $embed ) > 0 ){
            $output = $embed[0];
        }else{
           $output = '';
        }
    }
    return $output;
}


// WP post link pages
function quanto_link_pages(){
    wp_link_pages( array(
        'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'quanto' ) . '</span>',
        'after'       => '</div>',
        'link_before' => '<span>',
        'link_after'  => '</span>',
        'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'quanto' ) . ' </span>%',
        'separator'   => '<span class="screen-reader-text">, </span>',
    ) );
}


// Data Background image attr
function quanto_data_bg_attr( $imgUrl = '' ){
    return 'data-bg-img="'.esc_url( $imgUrl ).'"';
}

// image alt tag
function quanto_image_alt( $url = '' ){
    if( $url != '' ){
        // attachment id by url
        $attachmentid = attachment_url_to_postid( esc_url( $url ) );
       // attachment alt tag
        $image_alt = get_post_meta( esc_html( $attachmentid ) , '_wp_attachment_image_alt', true );
        if( $image_alt ){
            return $image_alt ;
        }else{
            $filename = pathinfo( esc_url( $url ) );
            $alt = str_replace( '-', ' ', $filename['filename'] );
            return $alt;
        }
    }else{
       return;
    }
}


// Flat Content wysiwyg output with meta key and post id

function quanto_get_textareahtml_output( $content ) {
    global $wp_embed;

    $content = $wp_embed->autoembed( $content );
    $content = $wp_embed->run_shortcode( $content );
    $content = wpautop( $content );
    $content = do_shortcode( $content );

    return $content;
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */

function quanto_pingback_header() {
    if ( is_singular() && pings_open() ) {
        echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
    }
}
add_action( 'wp_head', 'quanto_pingback_header' );





// Excerpt More
function quanto_excerpt_more( $more ) {
    return '...';
}

add_filter( 'excerpt_more', 'quanto_excerpt_more' );


// quanto comment template callback
function quanto_comment_callback( $comment, $args, $depth ) {
        $add_below = 'comment';
    ?>
    <li <?php comment_class( array('comment-item') ); ?>>
        <div id="comment-<?php comment_ID() ?>" class="post-comment">
            <?php
                if( get_avatar( $comment, 110 )  ) :
            ?>
            <!-- Author Image -->
            <div class="comment-avater">
                <?php
                    if ( $args['avatar_size'] != 0 ) {
                        echo get_avatar( $comment, 110 );
                    }
                ?>
            </div>
            <!-- Author Image -->
            <?php
                endif;
            ?>
            <!-- Comment Content -->
            <div class="comment-content">
                <h6 class="name d-inline-block me-3"><?php echo esc_html( ucwords( get_comment_author() ) ); ?></h6>
                <?php
                if ( get_post_type() === 'cmr_news' ) {
                    $r = get_comment_meta( get_comment_ID(), 'rating', true );
                    if ( $r && $r >= 1 && $r <= 5 ) {
                        echo '<span class="comment-rating-stars text-warning" style="font-size: 14px;">';
                        echo str_repeat('<i class="fa-solid fa-star"></i>', $r);
                        echo str_repeat('<i class="fa-regular fa-star"></i>', 5 - $r);
                        echo '</span>';
                    }
                }
                ?>

                <span class="commented-on"> 
                    <?php printf( esc_html__('%1$s | %2$s', 'quanto'), get_comment_date(), get_comment_time() ); ?> 
                </span>

                <?php comment_text(); ?>

                
                
                <div class="reply_and_edit">
                    <?php
                        comment_reply_link(
                            array_merge( 
                                $args, 
                                array( 
                                    'add_below' => $add_below, 
                                    'depth' => 1, 
                                    'max_depth' => 5, 
                                    'reply_text' => 'Reply<span>
                                                            <i class="fa-solid fa-arrow-right arry1"></i>
                                                            <i class="fa-solid fa-arrow-right arry2"></i>
                                                        </span>' 
                                ) 
                            ) 
                        );
                    ?>
                    <span class="comment-edit-link pl-10"><?php edit_comment_link( '<i class="fas fa-pencil"></i>'.esc_html__( 'Edit', 'quanto' ), '  ', '' ); ?></span>
                </div>

                <?php if ( $comment->comment_approved == '0' ) : ?>
                <p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'quanto' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <!-- Comment Content -->
<?php
}

//body class
add_filter( 'body_class', 'quanto_body_class' );
function quanto_body_class( $classes ) {
    if( class_exists('ReduxFramework') ) {
        $quanto_blog_single_sidebar = quanto_opt('quanto_blog_single_sidebar');
        if( ($quanto_blog_single_sidebar != '2' && $quanto_blog_single_sidebar != '3' ) || ! is_active_sidebar('quanto-blog-sidebar') ) {
            $classes[] = 'no-sidebar';
        }
    } else {
        if( !is_active_sidebar('quanto-blog-sidebar') ) {
            $classes[] = 'no-sidebar';
        }
    }
    return $classes;
}


function quanto_footer_global_option(){
    return;

    // Quanto Footer Bottom Enable Disable
    if( class_exists( 'ReduxFramework' ) ){
        $quanto_footer_bottom_active = quanto_opt( 'quanto_disable_footer_bottom' );
    }else{
        $quanto_footer_bottom_active = '1';
    }

    $allowhtml = array(
        'p'         => array(
            'class'     => array()
        ),
        'span'      => array(
            'class'     => array(),
        ),
        'a'         => array(
            'href'      => array(),
            'title'     => array()
        ),
        'br'        => array(),
        'em'        => array(),
        'strong'    => array(),
        'b'         => array(),
    );

    if( $quanto_footer_bottom_active == '1' ){
        echo '<!-- Footer -->';
        echo '<footer class="footer-wrapper footer-layout1">';

            if( $quanto_footer_bottom_active == '1' ){
                $allowhtml = array(
                    'p'         => array(
                        'class'     => array()
                    ),
                    'span'      => array(),
                    'a'         => array(
                        'href'      => array(),
                        'title'     => array(),
                        'class'     => array(),
                    ),
                    'br'        => array(),
                    'em'        => array(),
                    'strong'    => array(),
                    'b'         => array(),
                );
                echo '<div class="copyright-wrap">';
                    echo '<div class="container">';
                        echo '<div class="row align-items-center">';
                            if( ! empty( quanto_opt( 'quanto_copyright_text' ) ) ){
                                $copyright_text = quanto_opt( 'quanto_copyright_text' );
                                if ( strpos( $copyright_text, 'Mirrortheme' ) !== false || strpos( $copyright_text, 'Quanto' ) !== false ) {
                                    $copyright_text = 'Copyright &copy; {year} <a href="{site_url}">{site_name}</a>. All Rights Reserved.';
                                }
                                $copyright_text = str_replace(
                                    array( '{year}', '{site_url}', '{site_name}' ),
                                    array( date('Y'), esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) ),
                                    $copyright_text
                                );
                                echo '<p class="copyright-text">'.wp_kses( $copyright_text, $allowhtml ).'</p>';
                            }
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            }
        echo '</footer>';
        echo '<!-- End Footer -->';
    }
}

function quanto_social_icon(){
    $quanto_social_icon = quanto_opt( 'quanto_social_links' );
    if( ! empty( $quanto_social_icon ) && isset( $quanto_social_icon ) ){
        echo '<div class="author-links">';
        foreach( $quanto_social_icon as $social_icon ){
            if( ! empty( $social_icon['title'] ) ){
                echo '<a href="'.esc_url( $social_icon['url'] ).'"><i class="'.esc_attr( $social_icon['title'] ).'"></i>'.esc_html( $social_icon['description'] ).'</a>';
            }
        }
        echo '</div>';
    }
}


// global header
function quanto_global_header_option() {
    quanto_global_header();
    echo '<header class="quanto-header main-header bg-color-white prebuilt-header" id="sticky-menu">';
        echo '<div class="sticky-wrap">';
            echo '<div class="sticky-active">';
                echo '<div class="container custom-container">';
                    echo '<div class="row gx-3 align-items-center justify-content-between">';
                        echo '<div class="col-auto align-self-center">';
                            echo '<div class="header-logo">';
                                echo quanto_theme_logo();
                            echo '</div>';
                        echo '</div>';
                        echo '<div class="col text-end">';
                            if( has_nav_menu( 'primary-menu' ) ){
                                echo '<nav class="main-menu menu-style1 d-none d-lg-block">';
                                    wp_nav_menu( array(
                                        "theme_location"    => 'primary-menu',
                                        "container"         => '',
                                        "menu_class"        => ''
                                    ) );
                                echo '</nav>';
                            }
                            
                        echo '</div>';
                        
                        echo '<div class="col-auto d-inline-block d-lg-none">';
                            echo '<!-- Mobile Menu Toggler -->';
                            echo '<button class="menuBar-toggle quanto-menu-toggle d-inline-block d-lg-none">';
                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                        <path d="M24.4444 26V28H0V26H24.4444ZM40 19V21H0V19H40ZM40 12V14H15.5556V12H40Z" fill="currentColor"></path>
                                    </svg>';
                            echo '</button>';
                        echo '</div>';

                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</header>';
}

// Quanto Default Header
if( ! function_exists( 'quanto_global_header' ) ){
    function quanto_global_header(){
        // quanto-body-visible
        // Mobile Menu
        echo '<div class="quanto-menu-wrapper">';
            echo '<div class="quanto-menu-area text-center">';
                echo '<div class="quanto-menu-mobile-top">';
                    echo '<div class="mobile-logo">';
                        echo quanto_theme_logo();
                    echo '</div>';
                    echo '<button class="quanto-menu-toggle mobile"><i class="ri-close-line"></i></button>';
                echo '</div>';
                if( has_nav_menu( 'mobile-menu' ) ){
                    echo '<div class="quanto-mobile-menu">';
                        wp_nav_menu( array(
                            "theme_location"    => 'mobile-menu',
                            "container"         => '',
                            "menu_class"        => ''
                        ) );
                    echo '</div>';
                }
            echo '</div>';
        echo '</div>';
    }
}


function quanto_custom_search_form( $class ) {
    echo '<!-- Search Form -->';
    echo '<form method="get" action="'.esc_url( home_url( '/' ) ).'" class="'.esc_attr( $class ).'">';
        echo '<label class="searchIcon">';
            echo '<input value="'.esc_html( get_search_query() ).'" name="s" required type="search" placeholder="'.esc_attr__('What are you looking for?', 'quanto').'">';
        echo '</label>';
    echo '</form>';
    echo '<!-- End Search Form -->';
}



//Fire the wp_body_open action.
if ( ! function_exists( 'wp_body_open' ) ) {
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

//Remove Tag-Clouds inline style
add_filter( 'wp_generate_tag_cloud', 'quanto_remove_tagcloud_inline_style',10,1 );
function quanto_remove_tagcloud_inline_style( $input ){
   return preg_replace('/ style=("|\')(.*?)("|\')/','',$input );
}

// password protected form
add_filter('the_password_form','quanto_password_form',10,1);
function quanto_password_form( $output ) {
    $output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post"><div class="theme-input-group">
        <input name="post_password" type="password" class="theme-input-style" placeholder="'.esc_attr__( 'Enter Password','quanto' ).'">
        <button type="submit" class="submit-btn btn-fill">'.esc_html__( 'Enter','quanto' ).'</button></div></form>';
    return $output;
}

function quanto_setPostViews( $postID ) {
    $count_key  = 'post_views_count';
    $count      = get_post_meta( $postID, $count_key, true );
    if( $count == '' ){
        $count = 0;
        delete_post_meta( $postID, $count_key );
        add_post_meta( $postID, $count_key, '0' );
    }else{
        $count++;
        update_post_meta( $postID, $count_key, $count );
    }
}

function quanto_getPostViews( $postID ){
    $count_key  = 'post_views_count';
    $count      = get_post_meta( $postID, $count_key, true );
    if( $count == '' ){
        delete_post_meta( $postID, $count_key );
        add_post_meta( $postID, $count_key, '0' );
        return __( '0', 'quanto' );
    }
    return $count;
}

/* This code filters the Categories widget to include the post count inside the link */
function quanto_cat_add_count_span($output) {
    // Modify the category list output to include count in a <span> element
    $output = preg_replace('/<\/a>\s*\(([0-9]+)\)/', '</a> <span>($1)</span>', $output);
    return $output;
}
add_filter('wp_list_categories', 'quanto_cat_add_count_span');

/* This code filters the Archive widget to include the post count inside the link */
add_filter( 'get_archives_link', 'quanto_archive_remove_count' );
function quanto_archive_remove_count( $links ) {
    $links = preg_replace('/<\/a>&nbsp;\([0-9]+\)/', '</a>', $links);
    return $links;
}

// Blog Category
if( ! function_exists( 'quanto_blog_category' ) ){
    function quanto_blog_category(){
        $quanto_post_categories = get_the_category();
        if( is_array( $quanto_post_categories ) && ! empty( $quanto_post_categories ) ){
            if( is_single() ) {
                echo '<li><span><a href="'.esc_url( get_term_link( $quanto_post_categories[0]->term_id ) ).'">'.esc_html( $quanto_post_categories[0]->name ).'</a></span></li>';
            } else{
                echo '<li><span><a href="'.esc_url( get_term_link( $quanto_post_categories[0]->term_id ) ).'">'.esc_html( $quanto_post_categories[0]->name ).'</a></span></li>';
            }
                
        }
    }
}

// Add Extra Class On Comment Reply Button
function quanto_custom_comment_reply_link( $content ) {
    $extra_classes = 'replay-btn';
    return preg_replace( '/comment-reply-link/', 'quanto-link-btn ' . $extra_classes, $content);
}

add_filter('comment_reply_link', 'quanto_custom_comment_reply_link', 99);

// Add Extra Class On Edit Comment Link
function quanto_custom_edit_comment_link( $content ) {
    $extra_classes = 'replay-btn';
    return preg_replace( '/comment-edit-link/', 'comment-edit-link ' . $extra_classes, $content);
}

add_filter('edit_comment_link', 'quanto_custom_edit_comment_link', 99);


function quanto_post_classes( $classes, $class, $post_id ) {
    if ( get_post_type() === 'post' ) {
        if( ! is_single() ){
            if( quanto_opt( 'quanto_blog_style' ) == '3' ){
                $classes[] = "quanto-blog blog-grid grid-wide";
            }else{
                $classes[] = "quanto-blog blog-single";
            }
        }else{
            $classes[] = "quanto-blog";
        }
    }elseif( get_post_type() === 'product' ){
        // Return Class
    }elseif( get_post_type() === 'page' ){
        $classes[] = "page--item";
    }

    return $classes;
}
add_filter( 'post_class', 'quanto_post_classes', 10, 3 );
