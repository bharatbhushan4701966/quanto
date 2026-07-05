<?php

if (!defined('ABSPATH')) {
    exit;
}

class Quanto_Service_Theme extends \Elementor\Widget_Base {

    public function get_name() {
        return 'quanto_services';
    }

    public function get_title() {
        return esc_html__('Services', 'quanto');
    }

    public function get_icon() {
        return 'eicon-code';
    }

    public function get_categories() {
        return ['quanto-addons'];
    }

    public function get_keywords() {
        return ['service', 'normal', 'slider'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'service_section',
            [
                'label' => esc_html__('Service Section', 'quanto'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'service_layout',
            [
                'label' => esc_html__('Select Style', 'quanto'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '1' => esc_html__('Style 1', 'quanto'),
                    '2' => esc_html__('Style 2', 'quanto'),
                    '3' => esc_html__('Style 3', 'quanto'),
                    '4' => esc_html__('Style 4', 'quanto'),
                    '5' => esc_html__('Style 5', 'quanto'),
                    '6' => esc_html__('Style 6', 'quanto'),
                ],
                'default' => '1',
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'select_post',
            [
                'label' => __('Select a Post', 'quanto'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'default' => 'none',
                'options' => $this->get_all_services(),
            ]
        );

        $repeater->add_control(
            'service_icon_select',
            [
                'label' => esc_html__('Icon Select', 'quanto'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'quanto_service_image' => esc_html__('Image', 'quanto'),
                    'quanto_service_icon' => esc_html__('Icon', 'quanto'),
                ],
                'default' => 'quanto_service_image',
            ]
        );

        $repeater->add_control(
            'quanto_service_image',
            [
                'label' => esc_html__('Image', 'quanto'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'condition' => [
                    'service_icon_select' => 'quanto_service_image'
                ]
            ]
        );

        $repeater->add_control(
            'quanto_service_icon',
            [
                'label' => __('Icon', 'quanto'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'label_block' => true,
                'separator' => 'after',
                'condition' => [
                    'service_icon_select' => 'quanto_service_icon'
                ]
            ]
        );

        
        $repeater->add_control(
            'service_btn_text',
            [
                'label' => esc_html__('Button Text', 'quanto'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default' => esc_html__('View details', 'quanto'),
            ]
        );

        $repeater->add_control(
            'service_custom_link',
            [
                'label' => __('Custom Link', 'quanto'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'quanto'),
                'show_external' => true,
                'description' => __('Leave empty to use the service post permalink.', 'quanto'),
            ]
        );

        $repeater->add_control(
            'service_discription_text',
            [
                'label' => esc_html__('Discription Text', 'quanto'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'label_block' => true,
                'default' => esc_html__('Brand identity design a the have to success whether you breath onfire quanto agency. ', 'quanto'),
            ]
        );
        $repeater->add_control(
            'service_number',
            [
                'label' => esc_html__('Service Number', 'quanto'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default' => esc_html__('01', 'quanto'),
                'description' => __('Number only for style 3 and style 4', 'optech'),
            ]
        );
        $repeater->add_control(
            'show_service_feature',
            [
                'label' => esc_html__('Show Service Feature', 'quanto'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'quanto'),
                'label_off' => esc_html__('Hide', 'quanto'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('Its use only for style 6', 'optech'),
            ]
        );
        $repeater->add_control(
            'service_feature_text',
            [
                'label' => esc_html__('Feature  List', 'quanto'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'label_block' => true,
                'condition' => [
                    'show_service_feature' => 'yes', // Show only if switcher is ON
                ],
            ]
        );
        // Add control for data-delay
        $repeater->add_control(
            'item_delay',
            [
                'label' => __('Item Delay', 'quanto'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '0.30', // Set default delay value
                'description' => __('Set the delay time in seconds', 'quanto'),
            ]
        );
        // Add control for data-direction
        $repeater->add_control(
            'item_direction',
            [
                'label' => __('Item Direction', 'quanto'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'right' => __('Right', 'quanto'),
                    'left' => __('Left', 'quanto'),
                    'up' => __('Up', 'quanto'),
                    'down' => __('Down', 'quanto'),
                ],
                'default' => 'right', // Set default direction
                'description' => __('Select the direction for the animation', 'quanto'),
            ]
        );
        

        $this->add_control(
            'service_lists',
            [
                'label' => __('Service Lists', 'quanto'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),

            ]
        );

        $this->end_controls_section();
        // start Image style
        $this->start_controls_section(
            'service_icon_style',
            [
                    'label' => __('Icon', 'quanto'),
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'icon_gap',
            [
                'label' => esc_html__( 'Icon Gap', 'quanto' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .quanto-service-box .quanto-iconbox-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-service-box5 ' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-service-box4 img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-service-box4 svg' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-service-box6 .service-icon img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-service-box6 .service-icon svg' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => esc_html__( 'Size', 'quanto' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .quanto-iconbox-icon svg' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-iconbox-icon img' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-service-box6 .service-icon svg' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .quanto-service-box6 .service-icon img' => 'width: {{SIZE}}{{UNIT}};',
                ],

            ]
        );
        $this->add_control(
			'icon_color',
			[
				'label' => esc_html__( ' Icon Color', 'optech' ),
				'type' => \Elementor\Controls_Manager::COLOR,
                'description' => esc_html__( 'Color will be enabled for icons') , 'quanto',
				'selectors' => [
					'{{WRAPPER}} .quanto-iconbox-icon svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box4 svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box6 .service-icon svg path' => 'fill: {{VALUE}}',
				],
			]
		);
        $this->end_controls_section();

        // start Title style
        $this->start_controls_section(
            'team_title_style',
            [
                    'label' => __('Title', 'optech'),
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs(
            'title_style_tabs'
        );
        $this->start_controls_tab(
                'title_style_normal_tab',
                [
                        'label' => __('Normal', 'optech'),
                ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .quanto-iconbox-data h5',
				'selector' => '{{WRAPPER}} .quanto-service-box5 .quanto-content h5',
				'selector' => '{{WRAPPER}} .quanto-service-box4 .service-title',
				'selector' => '{{WRAPPER}} .quanto-service-box6 .service-content h5',
				'selector' => '{{WRAPPER}} .quanto-service-box3 .box-content h4',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'optech' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quanto-iconbox-data h5' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box5 .quanto-content h5' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box4 .service-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box6 .service-content h5' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box3 .box-content h4' => 'color: {{VALUE}}',
				],
			]
		);
        $this->add_responsive_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-iconbox-data h5' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box5 .quanto-content h5' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box4 .service-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box6 .service-content h5' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box3 .box-content h4' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->add_responsive_control(
			'title_padding',
			[
				'label' => esc_html__( 'Padding', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-iconbox-data h5' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box5 .quanto-content h5' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box4 .service-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box6 .service-content h5' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box3 .box-content h4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
        $this->end_controls_tab();

        $this->start_controls_tab(
            'title_style_hover_tab',
            [
                    'label' => __('Hover', 'optech'),
            ]
        );
        $this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__( 'Color', 'optech' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quanto-iconbox-data h5:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box5 .quanto-content h5:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box4 .service-title:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box6 .service-content h5:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box3 .service-content h4:hover' => 'color: {{VALUE}}',
				],
			]
		);
        $this->end_controls_tab();
        $this->end_controls_tabs();
		$this->end_controls_section();
        // Description Text style
        $this->start_controls_section(
            'service_description_style',
            [
                    'label' => __('Description Text', 'optech'),
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'designation_typography',
				'selector' => '{{WRAPPER}} .quanto-service-box5 .quanto-content p',
				'selector' => '{{WRAPPER}} .quanto-service-box4 .service-info p',
				'selector' => '{{WRAPPER}} .quanto-service-box6 .service-content p',
				'selector' => '{{WRAPPER}} .quanto-service-box3 .box-content p',
			]
		);
		$this->add_control(
			'designation_color',
			[
				'label' => esc_html__( 'Color', 'optech' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quanto-service-box5 .quanto-content p' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box4 .service-info p' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box6 .service-content p' => 'color: {{VALUE}}',
					'{{WRAPPER}} .quanto-service-box3 .box-content p' => 'color: {{VALUE}}',
				],
			]
		);
        $this->add_responsive_control(
			'designation_margin',
			[
				'label' => esc_html__( 'Margin', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-service-box5 .quanto-content p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box4 .service-info p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box6 .service-content p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box3 .box-content p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->end_controls_section();

        // Button style
        $this->start_controls_section(
            'service_button_style',
            [
                    'label' => __('Button', 'optech'),
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                    'condition' => [
                    'service_layout' => ['1', '2', '3', '5'],
                    ],
                    
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .quanto-link-btn',
				'selector' => '{{WRAPPER}} .quanto-link-btn',
                
			]
		);
		$this->add_control(
			'button_color',
			[
				'label' => esc_html__( 'Color', 'optech' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} a.quanto-link-btn' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .quanto-link-btn span i' => 'color: {{VALUE}} !important;',
                    // '{{WRAPPER}} .quanto-link-btn' => 'border-color: {{VALUE}} !important;',
                ],

			]
		);
        $this->add_control(
            'button_hover_color',
            [
                'label' => esc_html__( 'Hover Color', 'optech' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .quanto-service-box3:hover a.quanto-link-btn span i' => 'color: {{VALUE}} !important;',
                ],
                'condition' => [
                    'service_layout' => '5',
                ],
            ]
        );
        $this->add_responsive_control(
			'button_bg_color',
			[
                'label'     => __('Background Color', 'optech'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                        '{{WRAPPER}} .quanto-link-btn' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                'service_layout' => '5',
                ],

			]
		);
        $this->add_responsive_control(
			'button_bg_hover_color',
			[
					'label'     => __('Background Hover Color', 'optech'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
							'{{WRAPPER}} .quanto-service-box3:hover a.quanto-link-btn' => 'background-color: {{VALUE}}; border-color: {{VALUE}} !important;',
					],
                    'condition' => [
                    'service_layout' => '5',
                    ],

			]
		);
        $this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__( 'Margin', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-link-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
        $this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-link-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->end_controls_section();

        // service feature list
        $this->start_controls_section(
            'service_feature_list_style',
            [
                    'label' => __('Feature List', 'optech'),
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                    'condition' => [
                    'service_layout' => '6',
                    ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'feature_list_typography',
				'selector' => '{{WRAPPER}} .quanto-service-box6 .service-list ul li',
			]
		);
		$this->add_control(
			'feature_list_color',
			[
				'label' => esc_html__( 'Color', 'optech' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .quanto-service-box6 .service-list ul li' => 'color: {{VALUE}}',
				],
			]
		);
        $this->add_responsive_control(
			'feature_list_margin',
			[
				'label' => esc_html__( 'Margin', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-service-box6 .service-list ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->end_controls_section();
        // start box style
        $this->start_controls_section(
            'service_box_style',
            [
                    'label' => __('Box', 'optech'),
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'box_bg_color',
			[
					'label'     => __('Background Color', 'optech'),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => [
							'{{WRAPPER}} .quanto-service-box' => 'background-color: {{VALUE}}',
							'{{WRAPPER}} .quanto-service-box4' => 'background-color: {{VALUE}}',
							'{{WRAPPER}} .quanto-service-box6' => 'background-color: {{VALUE}}',
							'{{WRAPPER}} .quanto-service-box3' => 'background-color: {{VALUE}}',
					],
			]
		);
        $this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Padding', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-service-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box6' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
        $this->add_responsive_control(
			'box_margin',
			[
				'label' => esc_html__( 'Margin', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-service-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box4' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box6' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_hover_shadow',
				'label' => esc_html__( 'Box Hover Shadow', 'optech' ),
				'selector' => '{{WRAPPER}} .quanto-service-box:hover',
				'selector' => '{{WRAPPER}} .quanto-service-box4:hover',
				'selector' => '{{WRAPPER}} .quanto-service-box3:hover',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' 		=> 'box_border',
				'label' 	=> __( 'Border', 'optech' ),
                'selector' 	=> '{{WRAPPER}} .quanto-service-box',
                'selector' 	=> '{{WRAPPER}} .quanto-service-box4',
                'selector' 	=> '{{WRAPPER}} .quanto-service-box6',
                'selector' 	=> '{{WRAPPER}} .quanto-service-box3',
			]
		);
        $this->add_responsive_control(
			'box_radius',
			[
				'label' => esc_html__( 'Border Radius', 'optech' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .quanto-service-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box4' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box6' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .quanto-service-box3' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
			]
		);

		$this->end_controls_section();
        // start box style
        $this->start_controls_section(
            'service_number_style',
            [
                    'label' => __('Number', 'optech'),
                    'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                    'condition' => [
                    'service_layout' => ['3', '4'],
                    ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'number_typography',
                'selector' => '{{WRAPPER}} .quanto-service-box5 .service-number',
                'selector' => '{{WRAPPER}} .quanto-service-box4 .service-title span',
                
            ]
        );
        $this->add_control(
            'number_color',
            [
                'label' => esc_html__( 'Color', 'optech' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .quanto-service-box5 .service-number' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}}  .quanto-service-box4 .service-title span' => 'color: {{VALUE}} !important;',
                ],

            ]
        );

        $this->end_controls_section();

    }
    
    // Get All service

    public function get_all_services() {
        $wp_query = get_posts([
            'post_type' => 'quanto_service',
            'orderby' => 'date',
            'posts_per_page' => -1,
        ]);

        $options = ['none' => 'None'];
        foreach ($wp_query as $services) {
            $options[$services->ID] = $services->post_title;
        }

        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $service_lists = $settings['service_lists'];
        $service_layout = $settings[ 'service_layout' ];

        if( 1 == $service_layout || 6 == $service_layout || 5 == $service_layout){
            $this->add_render_attribute('service_version', 'class', ['row g-4']);
        }elseif(2 == $service_layout){
            $this->add_render_attribute('service_version', 'class', ['row g-114 quanto-service2__row']);
        }elseif(3 == $service_layout){
            $this->add_render_attribute('service_version', 'class', ['row gx-4 gy-5']);
        }else{
            $this->add_render_attribute('service_version', 'class', ['row']);
        }
        
        ?>

            <div <?php echo $this->get_render_attribute_string('service_version'); ?>>
                <?php 	
                    if (!empty($service_layout)) {
                    	include('service/'.$service_layout.'.php');
                    }
                ?>
            </div>
        <?php
    }
}


