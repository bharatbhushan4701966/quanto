<?php
/**
 * CMR Custom Image Carousel Widget with Width & Height Controls
 * Allows fixing and setting exact width and height for carousel in Elementor.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Quanto_Custom_Carousel_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'quanto_custom_carousel';
    }

    public function get_title() {
        return esc_html__('CMR Image Carousel (Width & Height)', 'quanto');
    }

    public function get_icon() {
        return 'eicon-slider-album';
    }

    public function get_categories() {
        return ['quanto-addons', 'general'];
    }

    public function get_keywords() {
        return ['carousel', 'slider', 'image', 'banner', 'hero', 'width', 'height', 'quanto'];
    }

    protected function register_controls() {
        // CONTENT TAB - Images
        $this->start_controls_section(
            'section_content_images',
            [
                'label' => esc_html__('Images', 'quanto'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'gallery',
            [
                'label'       => esc_html__('Add Images', 'quanto'),
                'type'        => \Elementor\Controls_Manager::GALLERY,
                'default'     => [],
                'description' => esc_html__('Select one or more images for the carousel.', 'quanto'),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
            [
                'name'    => 'image',
                'default' => 'full',
            ]
        );

        $this->end_controls_section();

        // CONTENT TAB - Carousel Settings
        $this->start_controls_section(
            'section_content_settings',
            [
                'label' => esc_html__('Carousel Settings', 'quanto'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'effect',
            [
                'label'   => esc_html__('Effect', 'quanto'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => esc_html__('Slide', 'quanto'),
                    'fade'  => esc_html__('Fade', 'quanto'),
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label'        => esc_html__('Autoplay', 'quanto'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'quanto'),
                'label_off'    => esc_html__('No', 'quanto'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_delay',
            [
                'label'     => esc_html__('Autoplay Speed (ms)', 'quanto'),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 3500,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label'        => esc_html__('Pause on Hover', 'quanto'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'loop',
            [
                'label'        => esc_html__('Infinite Loop', 'quanto'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'quanto'),
                'label_off'    => esc_html__('No', 'quanto'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'speed',
            [
                'label'   => esc_html__('Animation Speed (ms)', 'quanto'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 600,
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'label'        => esc_html__('Show Navigation Arrows', 'quanto'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'quanto'),
                'label_off'    => esc_html__('No', 'quanto'),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );

        $this->add_control(
            'show_dots',
            [
                'label'        => esc_html__('Show Pagination Dots', 'quanto'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'quanto'),
                'label_off'    => esc_html__('No', 'quanto'),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );

        $this->end_controls_section();

        // STYLE TAB - Width, Height & Layout
        $this->start_controls_section(
            'section_style_dimensions',
            [
                'label' => esc_html__('Width & Height', 'quanto'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'carousel_width',
            [
                'label'      => esc_html__('Width', 'quanto'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range'      => [
                    'px' => [
                        'min'  => 50,
                        'max'  => 1600,
                        'step' => 10,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .quanto-custom-carousel' => 'width: {{SIZE}}{{UNIT}} !important; max-width: 100%;',
                ],
            ]
        );

        $this->add_responsive_control(
            'carousel_height',
            [
                'label'      => esc_html__('Height', 'quanto'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range'      => [
                    'px' => [
                        'min'  => 100,
                        'max'  => 1400,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 450,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .quanto-custom-carousel' => 'height: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .quanto-custom-carousel .swiper' => 'height: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .quanto-custom-carousel .swiper-wrapper' => 'height: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .quanto-custom-carousel .swiper-slide' => 'height: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .quanto-custom-carousel .quanto-carousel-img' => 'height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label'     => esc_html__('Alignment', 'quanto'),
                'type'      => \Elementor\Controls_Manager::CHOOSE,
                'options'   => [
                    'left' => [
                        'title' => esc_html__('Left', 'quanto'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'quanto'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'quanto'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .quanto-custom-carousel-wrapper' => 'display: flex; justify-content: {{VALUE}};',
                ],
                'selectors_dictionary' => [
                    'left'   => 'flex-start',
                    'center' => 'center',
                    'right'  => 'flex-end',
                ],
            ]
        );

        $this->add_control(
            'object_fit',
            [
                'label'     => esc_html__('Image Fit', 'quanto'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'cover',
                'options'   => [
                    'cover'      => esc_html__('Cover (Fill & Crop)', 'quanto'),
                    'contain'    => esc_html__('Contain (Fit without cropping)', 'quanto'),
                    'fill'       => esc_html__('Fill (Stretch)', 'quanto'),
                    'scale-down' => esc_html__('Scale Down', 'quanto'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .quanto-custom-carousel .quanto-carousel-img' => 'object-fit: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'object_position',
            [
                'label'     => esc_html__('Image Position', 'quanto'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'center center',
                'options'   => [
                    'center center' => esc_html__('Center Center', 'quanto'),
                    'top center'    => esc_html__('Top Center', 'quanto'),
                    'bottom center' => esc_html__('Bottom Center', 'quanto'),
                    'center left'   => esc_html__('Center Left', 'quanto'),
                    'center right'  => esc_html__('Center Right', 'quanto'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .quanto-custom-carousel .quanto-carousel-img' => 'object-position: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label'      => esc_html__('Border Radius', 'quanto'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .quanto-custom-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-custom-carousel .swiper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-custom-carousel .swiper-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-custom-carousel .quanto-carousel-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'box_shadow',
                'label'    => esc_html__('Box Shadow', 'quanto'),
                'selector' => '{{WRAPPER}} .quanto-custom-carousel',
            ]
        );

        $this->end_controls_section();

        // STYLE TAB - Arrows
        $this->start_controls_section(
            'section_style_arrows',
            [
                'label'     => esc_html__('Navigation Arrows', 'quanto'),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_arrows' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label'     => esc_html__('Arrow Color', 'quanto'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .quanto-carousel-arrow' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_bg',
            [
                'label'     => esc_html__('Background Color', 'quanto'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(0,0,0,0.4)',
                'selectors' => [
                    '{{WRAPPER}} .quanto-carousel-arrow' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_size',
            [
                'label'     => esc_html__('Arrow Size', 'quanto'),
                'type'      => \Elementor\Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 20,
                        'max' => 80,
                    ],
                ],
                'default'   => [
                    'size' => 44,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .quanto-carousel-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: calc({{SIZE}}{{UNIT}} * 0.45);',
                ],
            ]
        );

        $this->end_controls_section();

        // STYLE TAB - Dots
        $this->start_controls_section(
            'section_style_dots',
            [
                'label'     => esc_html__('Pagination Dots', 'quanto'),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_dots' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dot_color',
            [
                'label'     => esc_html__('Dot Color', 'quanto'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(255,255,255,0.5)',
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dot_active_color',
            [
                'label'     => esc_html__('Active Dot Color', 'quanto'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}} !important;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $gallery  = !empty($settings['gallery']) && is_array($settings['gallery']) ? $settings['gallery'] : [];

        if (empty($gallery)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
                <div style="border: 2px dashed #9ca3af; padding: 40px; text-align: center; border-radius: 12px; background: #f9fafb; color: #4b5563;">
                    <i class="eicon-slider-album" style="font-size: 36px; color: #3b82f6; display: block; margin-bottom: 12px;"></i>
                    <strong style="font-size: 16px;"><?php esc_html_e('CMR Image Carousel', 'quanto'); ?></strong>
                    <p style="margin: 8px 0 0; font-size: 13px;"><?php esc_html_e('Click here to select images in the sidebar and configure height and width.', 'quanto'); ?></p>
                </div>
                <?php
            }
            return;
        }

        $uid        = uniqid('cmr-carousel-');
        $uid_safe   = str_replace('-', '_', $uid);
        $count      = count($gallery);
        $is_slider  = ($count > 1);

        $effect     = !empty($settings['effect']) ? $settings['effect'] : 'slide';
        $autoplay   = ($settings['autoplay'] === 'yes');
        $delay      = !empty($settings['autoplay_delay']) ? intval($settings['autoplay_delay']) : 3500;
        $hover      = ($settings['pause_on_hover'] === 'yes');
        $loop       = ($settings['loop'] === 'yes' && $is_slider);
        $speed      = !empty($settings['speed']) ? intval($settings['speed']) : 600;
        $show_arrows= ($settings['show_arrows'] === 'yes' && $is_slider);
        $show_dots  = ($settings['show_dots'] === 'yes' && $is_slider);
        ?>

        <style>
            #<?php echo esc_attr($uid); ?>-wrapper {
                width: 100%;
            }
            #<?php echo esc_attr($uid); ?> {
                position: relative;
                overflow: hidden;
                box-sizing: border-box;
            }
            #<?php echo esc_attr($uid); ?> .swiper {
                width: 100%;
                height: 100%;
                position: relative;
                overflow: hidden;
            }
            #<?php echo esc_attr($uid); ?> .swiper-wrapper {
                display: flex;
                width: 100%;
                height: 100%;
            }
            #<?php echo esc_attr($uid); ?> .swiper-slide {
                width: 100%;
                height: 100%;
                flex-shrink: 0;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            #<?php echo esc_attr($uid); ?> .quanto-carousel-img {
                width: 100% !important;
                height: 100% !important;
                display: block !important;
            }
            #<?php echo esc_attr($uid); ?> .quanto-carousel-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                border-radius: 50%;
                transition: all 0.25s ease;
                border: none;
                outline: none;
                user-select: none;
            }
            #<?php echo esc_attr($uid); ?> .quanto-carousel-prev {
                left: 16px;
            }
            #<?php echo esc_attr($uid); ?> .quanto-carousel-next {
                right: 16px;
            }
            #<?php echo esc_attr($uid); ?> .quanto-carousel-arrow:hover {
                transform: translateY(-50%) scale(1.08);
            }
            #<?php echo esc_attr($uid); ?> .swiper-pagination {
                position: absolute;
                bottom: 16px;
                left: 0;
                width: 100%;
                text-align: center;
                z-index: 10;
            }
            #<?php echo esc_attr($uid); ?> .swiper-pagination-bullet {
                display: inline-block;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                margin: 0 4px;
                cursor: pointer;
                transition: all 0.25s ease;
            }
        </style>

        <div class="quanto-custom-carousel-wrapper" id="<?php echo esc_attr($uid); ?>-wrapper">
            <div class="quanto-custom-carousel <?php echo $is_slider ? 'swiper' : ''; ?>" id="<?php echo esc_attr($uid); ?>">
                <div class="<?php echo $is_slider ? 'swiper-wrapper' : 'quanto-single-wrapper'; ?>" style="width:100%;height:100%;">
                    <?php foreach ($gallery as $img): 
                        $image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html($settings, 'image', $img);
                        // Inject class into img tag
                        if (!empty($image_html)) {
                            $image_html = str_replace('<img ', '<img class="quanto-carousel-img" ', $image_html);
                        } else {
                            $img_url = !empty($img['url']) ? $img['url'] : '';
                            $image_html = '<img src="' . esc_url($img_url) . '" alt="' . esc_attr__('Carousel Image', 'quanto') . '" class="quanto-carousel-img" />';
                        }
                    ?>
                        <div class="<?php echo $is_slider ? 'swiper-slide' : 'quanto-single-slide'; ?>" style="width:100%;height:100%;">
                            <?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($show_arrows): ?>
                    <button class="quanto-carousel-arrow quanto-carousel-prev" aria-label="<?php esc_attr_e('Previous', 'quanto'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button class="quanto-carousel-arrow quanto-carousel-next" aria-label="<?php esc_attr_e('Next', 'quanto'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                <?php endif; ?>

                <?php if ($show_dots): ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($is_slider): ?>
        <script>
        (function() {
            function initSwiper_<?php echo $uid_safe; ?>() {
                var container = document.getElementById("<?php echo esc_js($uid); ?>");
                if (!container) return;
                if (container.dataset.swiperInit === 'true' && container.swiper) return;

                if (typeof Swiper !== 'undefined') {
                    try {
                        if (container.swiper) {
                            container.swiper.destroy(true, true);
                        }
                        container.dataset.swiperInit = 'true';

                        var swiperOptions = {
                            slidesPerView: 1,
                            spaceBetween: 0,
                            effect: <?php echo json_encode($effect); ?>,
                            speed: <?php echo intval($speed); ?>,
                            loop: <?php echo $loop ? 'true' : 'false'; ?>,
                            watchOverflow: true,
                        };

                        <?php if ($autoplay): ?>
                        swiperOptions.autoplay = {
                            delay: <?php echo intval($delay); ?>,
                            disableOnInteraction: false,
                            <?php if ($hover): ?>
                            pauseOnMouseEnter: true,
                            <?php endif; ?>
                        };
                        <?php endif; ?>

                        <?php if ($show_arrows): ?>
                        var prevBtn = container.querySelector('.quanto-carousel-prev');
                        var nextBtn = container.querySelector('.quanto-carousel-next');
                        if (prevBtn && nextBtn) {
                            swiperOptions.navigation = {
                                prevEl: prevBtn,
                                nextEl: nextBtn,
                            };
                        }
                        <?php endif; ?>

                        <?php if ($show_dots): ?>
                        var paginationEl = container.querySelector('.swiper-pagination');
                        if (paginationEl) {
                            swiperOptions.pagination = {
                                el: paginationEl,
                                clickable: true,
                            };
                        }
                        <?php endif; ?>

                        new Swiper(container, swiperOptions);
                    } catch(err) {
                        console.error('Swiper Init Error:', err);
                    }
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSwiper_<?php echo $uid_safe; ?>);
            } else {
                initSwiper_<?php echo $uid_safe; ?>();
            }

            if (window.jQuery) {
                jQuery(window).on('elementor/frontend/init', function() {
                    if (window.elementorFrontend && elementorFrontend.hooks) {
                        elementorFrontend.hooks.addAction('frontend/element_ready/quanto_custom_carousel.default', function() {
                            initSwiper_<?php echo $uid_safe; ?>();
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
