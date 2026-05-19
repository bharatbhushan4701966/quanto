<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head();?>
</head>
<body <?php body_class(); ?>>

<?php
    wp_body_open();

    /**
    *
    * Cursor
    *
    * Hook quanto_cursor_wrap
    *
    * @Hooked quanto_cursor_wrap_cb 10
    *
    */
    do_action( 'quanto_cursor_wrap' );

    /**
    *
    * Preloader
    *
    * Hook quanto_preloader_wrap
    *
    * @Hooked quanto_preloader_wrap_cb 10
    *
    */
    do_action( 'quanto_preloader_wrap' );

    /**
    *
    * quanto header
    *
    * Hook quanto_header
    *
    * @Hooked quanto_header_cb 10
    *
    */
    do_action( 'quanto_header' );



    do_action( 'quanto_before_content' );


    /**
    *
    * quanto breadcrumb
    *
    * Hook quanto_breadcrumb
    *
    * @Hooked quanto_breadcrumb_cb 10
    *
    */
    do_action( 'quanto_breadcrumb' );