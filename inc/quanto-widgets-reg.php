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

function quanto_widgets_init() {

    //sidebar widgets register
    register_sidebar( array(
        'name'          => esc_html__( 'Blog Sidebar', 'quanto' ),
        'id'            => 'quanto-blog-sidebar',
        'description'   => esc_html__( 'Add Blog Sidebar Widgets Here.', 'quanto' ),
        'before_widget' => '<div id="%1$s" class="sidebar__widget widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="sidebar__widget-title widget_title">',
        'after_title'   => '</h4>',
    ) );

    // page sidebar widgets register
    register_sidebar( array(
        'name'          => esc_html__( 'Page Sidebar', 'quanto' ),
        'id'            => 'quanto-page-sidebar',
        'description'   => esc_html__( 'Add Page Sidebar Widgets Here.', 'quanto' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sidebar__widget-title widget_title">',
        'after_title'   => '</h3>',
    ) );

}

add_action( 'widgets_init', 'quanto_widgets_init' );