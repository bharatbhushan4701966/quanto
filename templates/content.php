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

if( class_exists( 'ReduxFramework' )){
    $quanto_blog_style = quanto_opt('quanto_blog_style');

    if('blog_style_one' == $quanto_blog_style ){
        get_template_part( 'templates/blog-style-one' );
    }elseif('blog_style_two' == $quanto_blog_style ){
        get_template_part( 'templates/blog-style-two' );
    }
}else{
    get_template_part( 'templates/blog-style-one' );
}