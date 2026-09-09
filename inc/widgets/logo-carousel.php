<?php

if (!defined('ABSPATH')) {
    exit;
}

class Quanto_Logo_Carousel_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'quanto_logo_carousel';
    }

    public function get_title() {
        return esc_html__('Logo Carousel', 'quanto');
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return ['quanto-addons', 'general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Logos', 'quanto'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'gallery',
            [
                'label' => esc_html__('Add Images', 'quanto'),
                'type' => \Elementor\Controls_Manager::GALLERY,
                'default' => [],
            ]
        );

        $this->add_control(
            'slides_to_show',
            [
                'label' => esc_html__('Slides to Show', 'quanto'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5,
            ]
        );

        $this->add_control(
            'continuous',
            [
                'label' => esc_html__('Continuous Ticker?', 'quanto'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'quanto'),
                'label_off' => esc_html__('No', 'quanto'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Styling section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Box Style', 'quanto'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'box_height',
            [
                'label' => esc_html__('Box Height (px)', 'quanto'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 120,
                'selectors' => [
                    '{{WRAPPER}} .quanto-logo-slide' => 'height: {{VALUE}}px;',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => esc_html__('Border Color', 'quanto'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e5e7eb',
                'selectors' => [
                    '{{WRAPPER}} .quanto-logo-carousel-container' => 'border: 1px solid {{VALUE}};',
                    '{{WRAPPER}} .quanto-logo-slide' => 'border-right: 1px solid {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'grayscale',
            [
                'label' => esc_html__('Grayscale Images?', 'quanto'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'quanto'),
                'label_off' => esc_html__('No', 'quanto'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $gallery = !empty($settings['gallery']) && is_array($settings['gallery']) ? $settings['gallery'] : [];
        if (empty($gallery)) {
            if ( class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance) && isset(\Elementor\Plugin::$instance->editor) && method_exists(\Elementor\Plugin::$instance->editor, 'is_edit_mode') && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding: 20px; text-align: center; background: #f9f9f9; border: 1px dashed #ccc;">' . esc_html__('Please select logos in the widget settings.', 'quanto') . '</div>';
            }
            return;
        }

        $uid = uniqid('logo-carousel-');
        $is_continuous = isset($settings['continuous']) && $settings['continuous'] === 'yes';
        $is_grayscale = isset($settings['grayscale']) && $settings['grayscale'] === 'yes';
        $slides_count = !empty($settings['slides_to_show']) ? intval($settings['slides_to_show']) : 5;
        ?>
        
        <style>
            .quanto-logo-carousel-container {
                overflow: hidden;
                width: 100%;
                border: 1px solid #e5e7eb;
                box-sizing: border-box;
            }
            .quanto-logo-carousel-wrapper {
                display: flex;
                align-items: center;
                <?php if ($is_continuous): ?>
                transition-timing-function: linear !important;
                <?php endif; ?>
            }
            .quanto-logo-slide {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 15px 20px;
                border-right: 1px solid #e5e7eb;
                box-sizing: border-box;
                height: 110px;
            }
            .quanto-logo-slide img {
                max-width: 85%;
                max-height: 52px;
                object-fit: contain;
                <?php if ($is_grayscale): ?>
                filter: grayscale(100%) opacity(0.8);
                transition: filter 0.3s ease;
                <?php endif; ?>
            }
            <?php if ($is_grayscale): ?>
            .quanto-logo-slide img:hover {
                filter: grayscale(0%) opacity(1);
            }
            <?php endif; ?>
        </style>

        <div class="quanto-logo-carousel-container swiper-container" id="<?php echo esc_attr($uid); ?>">
            <div class="quanto-logo-carousel-wrapper swiper-wrapper">
                <?php foreach ($settings['gallery'] as $image): 
                    $img_url = '';
                    if (is_array($image) && !empty($image['url'])) {
                        $img_url = $image['url'];
                    } elseif (is_array($image) && !empty($image['id'])) {
                        $img_url = wp_get_attachment_image_url($image['id'], 'full');
                    } elseif (is_string($image)) {
                        $img_url = $image;
                    }
                    if (empty($img_url)) continue;
                ?>
                    <div class="quanto-logo-slide swiper-slide">
                        <img src="<?php echo esc_url($img_url); ?>" alt="Logo">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
        (function() {
            function initLogoSwiper_<?php echo str_replace('-', '_', $uid); ?>() {
                var el = document.getElementById("<?php echo esc_js($uid); ?>");
                if (!el) return;
                if (typeof Swiper !== 'undefined') {
                    try {
                        new Swiper(el, {
                            slidesPerView: <?php echo $slides_count; ?>,
                            spaceBetween: 0,
                            loop: true,
                            <?php if ($is_continuous): ?>
                            speed: 3000,
                            autoplay: {
                                delay: 0,
                                disableOnInteraction: false,
                            },
                            <?php else: ?>
                            speed: 800,
                            autoplay: {
                                delay: 3000,
                                disableOnInteraction: false,
                            },
                            <?php endif; ?>
                            breakpoints: {
                                320: { slidesPerView: 2 },
                                768: { slidesPerView: <?php echo max(3, $slides_count - 2); ?> },
                                1024: { slidesPerView: <?php echo $slides_count; ?> }
                            }
                        });
                    } catch(e) {}
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener("DOMContentLoaded", initLogoSwiper_<?php echo str_replace('-', '_', $uid); ?>);
            } else {
                initLogoSwiper_<?php echo str_replace('-', '_', $uid); ?>();
            }
            if (window.jQuery) {
                jQuery(window).on('elementor/frontend/init', function() {
                    if (window.elementorFrontend && elementorFrontend.hooks) {
                        elementorFrontend.hooks.addAction('frontend/element_ready/quanto_logo_carousel.default', function() {
                            setTimeout(initLogoSwiper_<?php echo str_replace('-', '_', $uid); ?>, 50);
                        });
                    }
                });
            }
        })();
        </script>
        <?php
    }
}
