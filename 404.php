<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : mirrortheme
 * @Author URI : https://www.mirrortheme.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Get Redux options or fallback
if ( class_exists( 'ReduxFramework' ) ) {
    $quanto404title     = quanto_opt( 'quanto_fof_title' );
    $quanto404subtitle  = quanto_opt( 'quanto_fof_subtitle' );
    $quanto404btntext   = quanto_opt( 'quanto_fof_btn_text' );
    $quanto404btnlink_raw = quanto_opt( 'quanto_fof_btn_link' );
    $quanto404btnlink = ! empty( $quanto404btnlink_raw ) ? $quanto404btnlink_raw : home_url('/');

} else {
    $quanto404title     = __( 'Sorry there’s nothing here', 'quanto' );
    $quanto404subtitle  = __( 'The page you are looking for was moved, removed, renamed or never existed.', 'quanto' );
    $quanto404btntext   = __( 'Get in touch', 'quanto' );
    $quanto404btnlink   = home_url('/');
}

// Optional 404 image (bottom image)
$error_img = '';
if ( ! empty( quanto_opt('quanto_error_bottom_img') ) ) {
    $img_array = quanto_opt('quanto_error_bottom_img');
    $error_img = isset($img_array['url']) ? $img_array['url'] : '';
}

// Get header
get_header();
?>

<?php if ( class_exists( 'ReduxFramework' ) ) : ?>
    <div class="error-section error-section-padding section-padding-top-bottom">
<?php else : ?>
    <div class="error-section section-padding-top-bottom">
<?php endif; ?>

    <div class="container custom-container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-xxl-6">
                <div class="error__content text-center">
                    <?php 
                        $error_main_img = '';
                        if ( ! empty( quanto_opt('quanto_error_img') ) ) {
                            $main_img_array = quanto_opt('quanto_error_img');
                            $error_main_img = isset($main_img_array['url']) ? $main_img_array['url'] : '';
                        }

                        if ( ! empty( $error_main_img ) ) {
                            echo '<img src="' . esc_url( $error_main_img ) . '" alt="404">';
                        } else {
                            echo '<h3>404</h3>';
                        }
                    ?>


                    <!-- Dynamic Title and Subtitle -->
                    <?php if ( ! empty( $quanto404title ) ) : ?>
                        <h1 class="title"><?php echo esc_html( $quanto404title ); ?></h1>
                    <?php endif; ?>

                    <?php if ( ! empty( $quanto404subtitle ) ) : ?>
                        <p><?php echo esc_html( $quanto404subtitle ); ?></p>
                    <?php endif; ?>

                    <!-- Dynamic Button -->
                    <?php if ( ! empty( $quanto404btntext ) ) : ?>
                        <a class="quanto-link-btn btn-pill" href="<?php echo esc_url( $quanto404btnlink ); ?>">
                            <span>
                                <i class="fa-solid fa-arrow-left arry1"></i>
                                <i class="fa-solid fa-arrow-left arry2"></i>
                            </span>
                            <?php echo esc_html( $quanto404btntext ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Optional Bottom Image -->
        <?php if ( ! empty( $error_img ) ) : ?>
            <div class="error__thumb position-absolute bottom-0 z-n1">
                <img src="<?php echo esc_url( $error_img ); ?>" alt="Error 404" class="w-100" />
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
