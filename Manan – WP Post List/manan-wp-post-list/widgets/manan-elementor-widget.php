<?php
if (!defined('ABSPATH')) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Manan_Elementor_Post_List_Widget extends Widget_Base {

    public function get_name() {
        return 'manan_post_list';
    }

    public function get_title() {
        return __('Manan Post List', 'manan-wp-post-list');
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function render() {
        echo do_shortcode('[manan_post_list]');
    }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Manan_Elementor_Post_List_Widget());
