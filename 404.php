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
    $quanto404title       = quanto_opt( 'quanto_fof_title' );
    $quanto404subtitle    = quanto_opt( 'quanto_fof_subtitle' );
    $quanto404btntext     = quanto_opt( 'quanto_fof_btn_text' );
    $quanto404btnlink_raw = quanto_opt( 'quanto_fof_btn_link' );
    $quanto404btnlink     = ! empty( $quanto404btnlink_raw ) ? $quanto404btnlink_raw : home_url('/');
} else {
    $quanto404title     = __( 'Oops! That page can’t be found.', 'quanto' );
    $quanto404subtitle  = __( 'The page you’ve requested is not available or has been moved.', 'quanto' );
    $quanto404btntext   = __( 'Return To Home', 'quanto' );
    $quanto404btnlink   = home_url('/');
}

if ( empty( $quanto404title ) ) {
    $quanto404title = __( 'Oops! That page can’t be found.', 'quanto' );
}
if ( empty( $quanto404subtitle ) ) {
    $quanto404subtitle = __( 'The page you’ve requested is not available or has been moved.', 'quanto' );
}
if ( empty( $quanto404btntext ) ) {
    $quanto404btntext = __( 'Return To Home', 'quanto' );
}

// Optional 404 bottom image
$error_img = '';
if ( ! empty( quanto_opt('quanto_error_bottom_img') ) ) {
    $img_array = quanto_opt('quanto_error_bottom_img');
    $error_img = isset($img_array['url']) ? $img_array['url'] : '';
}

// Get header
get_header();
?>

<style id="cmr-404-styles">
.cmr-404-section {
    min-height: calc(85vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 90px 20px 130px 20px;
    background: radial-gradient(ellipse at 50% 25%, rgba(99, 102, 241, 0.06) 0%, rgba(255, 255, 255, 0) 70%);
    position: relative;
    overflow: hidden;
}

.cmr-404-card {
    max-width: 760px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 2;
}

.cmr-404-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(99, 102, 241, 0.08);
    color: #4f46e5;
    padding: 6px 18px;
    border-radius: 100px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 20px;
    border: 1px solid rgba(99, 102, 241, 0.2);
}

.cmr-404-badge .cmr-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #4f46e5;
    display: inline-block;
    animation: cmr404Pulse 2s infinite ease-in-out;
}

@keyframes cmr404Pulse {
    0%, 100% { opacity: 0.4; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.2); }
}

.cmr-404-hero-num {
    font-size: clamp(80px, 11vw, 130px);
    font-weight: 900;
    line-height: 1;
    letter-spacing: -3px;
    margin-bottom: 18px;
    background: linear-gradient(135deg, #0f172a 0%, #312e81 40%, #4f46e5 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    user-select: none;
}

.cmr-404-title {
    font-size: clamp(28px, 4vw, 44px) !important;
    font-weight: 700 !important;
    line-height: 1.25 !important;
    color: #0f172a !important;
    letter-spacing: -0.025em;
    margin: 0 auto 16px auto !important;
    max-width: 640px;
}

.cmr-404-desc {
    font-size: 18px !important;
    line-height: 1.65 !important;
    color: #64748b !important;
    max-width: 500px;
    margin: 0 auto 38px auto !important;
}

.cmr-404-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

.cmr-404-btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: #0f172a;
    color: #ffffff !important;
    font-size: 15px;
    font-weight: 600;
    padding: 14px 30px;
    border-radius: 100px;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
}

.cmr-404-btn-primary:hover {
    background: #4f46e5;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3);
}

.cmr-404-btn-primary svg {
    transition: transform 0.3s ease;
}

.cmr-404-btn-primary:hover svg {
    transform: translateX(-4px);
}

.cmr-404-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #ffffff;
    color: #0f172a !important;
    font-size: 15px;
    font-weight: 600;
    padding: 13px 28px;
    border-radius: 100px;
    border: 1px solid #e2e8f0;
    text-decoration: none !important;
    transition: all 0.3s ease;
}

.cmr-404-btn-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #4f46e5 !important;
    transform: translateY(-2px);
}

@media (max-width: 767.98px) {
    .cmr-404-section {
        padding: 60px 16px 90px 16px;
        min-height: auto;
    }
    .cmr-404-desc {
        font-size: 15px !important;
        margin-bottom: 28px !important;
    }
    .cmr-404-actions {
        flex-direction: column;
        width: 100%;
    }
    .cmr-404-btn-primary,
    .cmr-404-btn-secondary {
        width: 100%;
    }
}
</style>

<div class="cmr-404-section">
    <div class="container custom-container">
        <div class="cmr-404-card">
            
            <div class="cmr-404-badge">
                <span class="cmr-dot"></span>
                <span>404 Error</span>
            </div>

            <?php 
                $error_main_img = '';
                if ( ! empty( quanto_opt('quanto_error_img') ) ) {
                    $main_img_array = quanto_opt('quanto_error_img');
                    $error_main_img = isset($main_img_array['url']) ? $main_img_array['url'] : '';
                }

                if ( ! empty( $error_main_img ) ) {
                    echo '<div class="cmr-404-img-wrap mb-4"><img src="' . esc_url( $error_main_img ) . '" alt="404" style="max-width: 280px; height: auto;"></div>';
                } else {
                    echo '<div class="cmr-404-hero-num">404</div>';
                }
            ?>

            <!-- Dynamic Title -->
            <?php if ( ! empty( $quanto404title ) ) : ?>
                <h1 class="cmr-404-title"><?php echo esc_html( $quanto404title ); ?></h1>
            <?php endif; ?>

            <!-- Dynamic Subtitle -->
            <?php if ( ! empty( $quanto404subtitle ) ) : ?>
                <p class="cmr-404-desc"><?php echo esc_html( $quanto404subtitle ); ?></p>
            <?php endif; ?>

            <!-- Actions -->
            <div class="cmr-404-actions">
                <?php if ( ! empty( $quanto404btntext ) ) : ?>
                    <a class="cmr-404-btn-primary" href="<?php echo esc_url( $quanto404btnlink ); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span><?php echo esc_html( $quanto404btntext ); ?></span>
                    </a>
                <?php endif; ?>
                
                <a class="cmr-404-btn-secondary" href="<?php echo esc_url( home_url('/contact-us') ); ?>">
                    <span><?php echo esc_html__( 'Contact Support', 'quanto' ); ?></span>
                </a>
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
