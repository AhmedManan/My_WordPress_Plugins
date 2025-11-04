<?php
/*
Plugin Name: Manan - WP Post List
Plugin URI: https://mananacademy.com/
Description: Display latest posts using the theme’s default post design. Control post count, filter by tagline or category, and display via shortcode or Elementor widget.
Version: 1.0
Author: Manan Ahmed Broti
Author URI: https://ahmedmanan.com/
License: GPL2
*/

if (!defined('ABSPATH')) exit;

class Manan_WP_Post_List {

    public function __construct() {
        // Admin settings
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));

        // Shortcode
        add_shortcode('manan_post_list', array($this, 'render_post_list'));

        // Elementor widget
        add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widget'));
    }

    /*---------------------
    * ADMIN SETTINGS
    ----------------------*/
    public function add_admin_menu() {
        add_options_page(
            'Manan WP Post List',
            'Manan WP Post List',
            'manage_options',
            'manan-wp-post-list',
            array($this, 'settings_page')
        );
    }

    public function settings_init() {
        register_setting('manan_settings_group', 'manan_post_count');
        register_setting('manan_settings_group', 'manan_filter_type');
        register_setting('manan_settings_group', 'manan_filter_value');

        add_settings_section(
            'manan_section',
            __('Post List Settings', 'manan-wp-post-list'),
            function() {
                echo __('Configure how your posts will be shown.', 'manan-wp-post-list');
            },
            'manan_settings_group'
        );

        add_settings_field(
            'manan_post_count',
            __('Number of Posts', 'manan-wp-post-list'),
            function() {
                $value = get_option('manan_post_count', 5);
                echo "<input type='number' name='manan_post_count' value='{$value}' min='1' style='width:100px;'>";
            },
            'manan_settings_group',
            'manan_section'
        );

        add_settings_field(
            'manan_filter_type',
            __('Filter Type', 'manan-wp-post-list'),
            function() {
                $value = get_option('manan_filter_type', '');
                ?>
                <select name='manan_filter_type'>
                    <option value='' <?php selected($value, ''); ?>>None</option>
                    <option value='tagline' <?php selected($value, 'tagline'); ?>>Tagline</option>
                    <option value='category' <?php selected($value, 'category'); ?>>Category</option>
                </select>
                <?php
            },
            'manan_settings_group',
            'manan_section'
        );

        add_settings_field(
            'manan_filter_value',
            __('Filter Value', 'manan-wp-post-list'),
            function() {
                $value = get_option('manan_filter_value', '');
                echo "<input type='text' name='manan_filter_value' value='{$value}' placeholder='Enter tagline or category name' style='width:300px;'>";
            },
            'manan_settings_group',
            'manan_section'
        );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Manan - WP Post List</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('manan_settings_group');
                do_settings_sections('manan_settings_group');
                submit_button();
                ?>
            </form>
            <hr>
            <p><strong>Shortcode:</strong> <code>[manan_post_list]</code></p>
            <p><strong>Elementor:</strong> Search for <em>“Manan Post List”</em> widget.</p>
        </div>
        <?php
    }

    /*---------------------
    * SHORTCODE OUTPUT
    ----------------------*/
    public function render_post_list($atts = []) {
        $count = get_option('manan_post_count', 5);
        $filter_type = get_option('manan_filter_type', '');
        $filter_value = get_option('manan_filter_value', '');

        $args = [
            'posts_per_page' => $count,
            'post_status' => 'publish'
        ];

        if ($filter_type === 'category' && !empty($filter_value)) {
            $args['category_name'] = sanitize_text_field($filter_value);
        } elseif ($filter_type === 'tagline' && !empty($filter_value)) {
            $args['s'] = sanitize_text_field($filter_value);
        }

        $query = new WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="manan-post-list">';
            while ($query->have_posts()) {
                $query->the_post();
                get_template_part('template-parts/content', get_post_format());
            }
            echo '</div>';
        } else {
            echo '<p>No posts found.</p>';
        }

        wp_reset_postdata();
        return ob_get_clean();
    }

    /*---------------------
    * ELEMENTOR WIDGET
    ----------------------*/
    public function register_elementor_widget() {
        if (!did_action('elementor/loaded')) return;

        require_once plugin_dir_path(__FILE__) . 'widgets/manan-elementor-widget.php';
    }
}

new Manan_WP_Post_List();
