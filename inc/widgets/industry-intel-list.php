<?php

if (!defined('ABSPATH')) {
    exit;
}

class Quanto_Industry_Intel_List_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'cmr_industry_intel_list_widget';
    }

    public function get_title() {
        return esc_html__('Industry Intel List', 'quanto');
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return ['quanto-addons', 'general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content Settings', 'quanto'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $categories = get_terms([
            'taxonomy' => 'category',
            'hide_empty' => false,
        ]);
        
        $options = ['' => 'All Categories'];
        if (!is_wp_error($categories) && !empty($categories)) {
            foreach ($categories as $cat) {
                $options[$cat->slug] = $cat->name;
            }
        }

        $this->add_control(
            'category',
            [
                'label' => esc_html__('Select Category', 'quanto'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => $options,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $category = isset($settings['category']) ? $settings['category'] : '';
        echo do_shortcode('[cmr_industry_intel_list category="' . esc_attr($category) . '"]');
    }
}
