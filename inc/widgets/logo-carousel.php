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
                    '{{WRAPPER}} .quanto-logo-carousel-container' => 'border: 1px solid {{VALUE}}; border-right: none;',
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
        
        if (empty($settings['gallery'])) {
            return;
        }

        $uid = uniqid('logo-carousel-');
        $is_continuous = $settings['continuous'] === 'yes';
        $is_grayscale = $settings['grayscale'] === 'yes';
        ?>
        
        <style>
            .quanto-logo-carousel-container {
                overflow: hidden;
                width: 100%;
                /* Border defaults fall back to CSS from controls, but we set a default here */
                border: 1px solid #e5e7eb;
                border-right: none;
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
                padding: 20px 30px;
                border-right: 1px solid #e5e7eb;
                box-sizing: border-box;
                height: 120px;
            }
            .quanto-logo-slide img {
                max-width: 100%;
                max-height: 100%;
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
                <?php foreach ($settings['gallery'] as $image): ?>
                    <div class="quanto-logo-slide swiper-slide">
                        <img src="<?php echo esc_url($image['url']); ?>" alt="Logo">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var initSwiper = function() {
                    if (typeof Swiper !== 'undefined') {
                        var swiper = new Swiper("#<?php echo esc_js($uid); ?>", {
                            slidesPerView: <?php echo intval($settings['slides_to_show']); ?>,
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
                                768: { slidesPerView: <?php echo max(3, intval($settings['slides_to_show']) - 2); ?> },
                                1024: { slidesPerView: <?php echo intval($settings['slides_to_show']); ?> }
                            }
                        });
                    } else {
                        setTimeout(initSwiper, 100);
                    }
                };
                initSwiper();
            });
        </script>
        <?php
    }
}
