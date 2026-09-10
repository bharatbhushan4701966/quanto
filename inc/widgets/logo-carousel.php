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

        $gallery_items = [];
        foreach ($gallery as $image) {
            $img_url = '';
            if (is_array($image) && !empty($image['url'])) {
                $img_url = $image['url'];
            } elseif (is_array($image) && !empty($image['id'])) {
                $img_url = wp_get_attachment_image_url($image['id'], 'full');
            } elseif (is_string($image)) {
                $img_url = $image;
            }
            if (!empty($img_url)) {
                $gallery_items[] = $img_url;
            }
        }
        if (empty($gallery_items)) return;

        $uid = uniqid('logo-carousel-');
        $uid_safe = str_replace('-', '_', $uid);
        $is_continuous = isset($settings['continuous']) && $settings['continuous'] === 'yes';
        $is_grayscale = isset($settings['grayscale']) && $settings['grayscale'] === 'yes';
        $slides_count = !empty($settings['slides_to_show']) ? intval($settings['slides_to_show']) : 5;

        // Ensure enough slides for a completely seamless loop in continuous mode
        $group_items = $gallery_items;
        if ($is_continuous) {
            while (count($group_items) < max(10, $slides_count * 2)) {
                $group_items = array_merge($group_items, $gallery_items);
            }
            $duration = max(20, count($group_items) * 3.5);
        }
        ?>
        
        <style>
            .quanto-logo-carousel-container {
                overflow: hidden;
                width: 100%;
                border: 1px solid #e5e7eb;
                box-sizing: border-box;
                position: relative;
            }
            .quanto-logo-slide {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 12px 25px;
                border-right: 1px solid #e5e7eb;
                box-sizing: border-box;
                height: 120px;
                flex-shrink: 0;
            }
            .quanto-logo-slide img {
                max-width: 88%;
                max-height: 72px;
                width: auto;
                height: auto;
                object-fit: contain;
                display: block;
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

            @media (max-width: 768px) {
                .quanto-logo-slide {
                    height: 90px;
                    padding: 10px 15px;
                }
                .quanto-logo-slide img {
                    max-height: 48px;
                    max-width: 85%;
                }
            }

            <?php if ($is_continuous): ?>
            #<?php echo esc_attr($uid); ?> {
                --slides-visible: <?php echo $slides_count; ?>;
            }
            @media (max-width: 1024px) {
                #<?php echo esc_attr($uid); ?> {
                    --slides-visible: <?php echo max(3, $slides_count - 2); ?>;
                }
            }
            @media (max-width: 768px) {
                #<?php echo esc_attr($uid); ?> {
                    --slides-visible: 2;
                }
            }
            #<?php echo esc_attr($uid); ?> .quanto-logo-marquee-track {
                display: flex;
                width: max-content;
                will-change: transform;
                animation: quantoMarquee_<?php echo $uid_safe; ?> <?php echo $duration; ?>s linear infinite;
            }
            #<?php echo esc_attr($uid); ?>:hover .quanto-logo-marquee-track {
                animation-play-state: paused;
            }
            @keyframes quantoMarquee_<?php echo $uid_safe; ?> {
                0% {
                    transform: translate3d(0, 0, 0);
                }
                100% {
                    transform: translate3d(-50%, 0, 0);
                }
            }
            #<?php echo esc_attr($uid); ?> .quanto-logo-marquee-group {
                display: flex;
                align-items: center;
                flex-shrink: 0;
            }
            #<?php echo esc_attr($uid); ?> .quanto-logo-slide {
                width: calc(var(--carousel-width, 100vw) / var(--slides-visible));
                flex: 0 0 calc(var(--carousel-width, 100vw) / var(--slides-visible));
            }
            <?php else: ?>
            .quanto-logo-carousel-wrapper {
                display: flex;
                align-items: center;
            }
            <?php endif; ?>
        </style>

        <?php if ($is_continuous): ?>
        <div class="quanto-logo-carousel-container quanto-logo-marquee" id="<?php echo esc_attr($uid); ?>">
            <div class="quanto-logo-marquee-track">
                <div class="quanto-logo-marquee-group">
                    <?php foreach ($group_items as $img_url): ?>
                        <div class="quanto-logo-slide">
                            <img src="<?php echo esc_url($img_url); ?>" alt="Logo">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="quanto-logo-marquee-group" aria-hidden="true">
                    <?php foreach ($group_items as $img_url): ?>
                        <div class="quanto-logo-slide">
                            <img src="<?php echo esc_url($img_url); ?>" alt="Logo">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script>
        (function() {
            var el = document.getElementById("<?php echo esc_js($uid); ?>");
            if (!el) return;
            function updateWidth() {
                if (el && el.offsetWidth) {
                    el.style.setProperty('--carousel-width', el.offsetWidth + 'px');
                }
            }
            updateWidth();
            window.addEventListener('resize', updateWidth, { passive: true });
            if (window.ResizeObserver) {
                try {
                    new ResizeObserver(function(entries) {
                        for (var i = 0; i < entries.length; i++) {
                            var w = entries[i].contentRect ? entries[i].contentRect.width : entries[i].target.offsetWidth;
                            if (w && el) el.style.setProperty('--carousel-width', w + 'px');
                        }
                    }).observe(el);
                } catch(e) {}
            }
            if (window.jQuery) {
                jQuery(window).on('elementor/frontend/init', function() {
                    if (window.elementorFrontend && elementorFrontend.hooks) {
                        elementorFrontend.hooks.addAction('frontend/element_ready/quanto_logo_carousel.default', function() {
                            updateWidth();
                        });
                    }
                });
            }
        })();
        </script>

        <?php else: ?>

        <div class="quanto-logo-carousel-container swiper-container" id="<?php echo esc_attr($uid); ?>">
            <div class="quanto-logo-carousel-wrapper swiper-wrapper">
                <?php foreach ($gallery_items as $img_url): ?>
                    <div class="quanto-logo-slide swiper-slide">
                        <img src="<?php echo esc_url($img_url); ?>" alt="Logo">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script>
        (function() {
            function initLogoSwiper_<?php echo $uid_safe; ?>() {
                var el = document.getElementById("<?php echo esc_js($uid); ?>");
                if (!el) return;
                if (el.dataset.swiperInit === 'true' && el.swiper) return;
                if (typeof Swiper !== 'undefined') {
                    try {
                        if (el.swiper) {
                            el.swiper.destroy(true, true);
                        }
                        el.dataset.swiperInit = 'true';
                        new Swiper(el, {
                            slidesPerView: <?php echo $slides_count; ?>,
                            spaceBetween: 0,
                            loop: true,
                            speed: 800,
                            autoplay: {
                                delay: 3000,
                                disableOnInteraction: false,
                                pauseOnMouseEnter: true,
                            },
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
                document.addEventListener("DOMContentLoaded", initLogoSwiper_<?php echo $uid_safe; ?>);
            } else {
                initLogoSwiper_<?php echo $uid_safe; ?>();
            }
            if (window.jQuery) {
                jQuery(window).on('elementor/frontend/init', function() {
                    if (window.elementorFrontend && elementorFrontend.hooks) {
                        elementorFrontend.hooks.addAction('frontend/element_ready/quanto_logo_carousel.default', function() {
                            initLogoSwiper_<?php echo $uid_safe; ?>();
                        });
                    }
                });
            }
        })();
        </script>
        <?php endif; ?>
        <?php
    }
}
