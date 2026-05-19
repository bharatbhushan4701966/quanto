<?php
// Block direct access
if( !defined( 'ABSPATH' ) ){
    exit();
}
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

// enqueue css
function quanto_common_custom_css(){


    $CustomCssOpt  = quanto_opt( 'quanto_css_editor' );
	if( $CustomCssOpt ){
		$CustomCssOpt = $CustomCssOpt;
	}else{
		$CustomCssOpt = '';
	}

    $customcss = "";

    if( get_header_image() ){
        $quanto_header_bg =  get_header_image();
    }else{
        if( quanto_meta( 'page_breadcrumb_settings' ) == 'page' && is_page() ){
            if( ! empty( quanto_meta( 'breadcumb_image' ) ) ){
                $quanto_header_bg = quanto_meta( 'breadcumb_image' );
            }
        }
    }

    if( !empty( $quanto_header_bg ) ){
        $customcss .= ".breadcumb-wrapper{
            background-image:url('{$quanto_header_bg}')!important;
        }";
    }

	// Check if logo_max_width_desktop option is set and append CSS
	if ( !empty( quanto_opt( 'logo_max_width_desktop' ) ) ) {
		$logo_max_width_desktop = quanto_opt('logo_max_width_desktop' );
		$customcss .= "
			.header-logo img {
				width: {$logo_max_width_desktop}px;
			}
		";
	}

	// Check if logo_max_width_mobile option is set and append CSS for mobile view
	if ( !empty( quanto_opt('logo_max_width_mobile') ) ) {
		$logo_max_width_mobile = quanto_opt('logo_max_width_mobile');
		$customcss .= "
			@media (max-width: 680px) {
				.header-logo img {
					width: {$logo_max_width_mobile}px;
				}
			}
		";
	}

	// theme color
	$quanto_primary 	   = quanto_opt('quanto_primary');
	$quanto_heading_color  = quanto_opt('quanto_heading_color');
	$quanto_body_color     = quanto_opt('quanto_body_color');
	

	
	if( !empty( $quanto_primary ) ) {
		$customcss .= ":root {
		  --accent-color: {$quanto_primary};
		}";
	}

	if( !empty( $quanto_heading_color ) ) {
		$customcss .= ":root {
			--heading-color: {$quanto_heading_color};
		}";
	}

	if( !empty( $quanto_body_color ) ) {
		$customcss .= ":root {
			--body-color: {$quanto_body_color};
		}";
	}


	if( !empty( $CustomCssOpt ) ){
		$customcss .= $CustomCssOpt;
	}

    wp_add_inline_style( 'quanto-color-schemes', $customcss );
}
add_action( 'wp_enqueue_scripts', 'quanto_common_custom_css', 100 );