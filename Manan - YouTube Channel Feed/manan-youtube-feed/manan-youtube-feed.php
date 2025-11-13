<?php
/*
Plugin Name: Manan - YouTube Channel Feed
Plugin URI: https://mananacademy.com/
Description: Display customizable YouTube channel feeds using the YouTube Data API.
Version: 1.0
Author: Manan Ahmed Broti
Author URI: https://ahmedmanan.com/
License: GPL2
*/

if (!defined('ABSPATH')) exit;

// Define constants
define('MANAN_YT_FEED_PATH', plugin_dir_path(__FILE__));
define('MANAN_YT_FEED_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once MANAN_YT_FEED_PATH . 'admin/settings-page.php';
require_once MANAN_YT_FEED_PATH . 'public/youtube-feed-display.php';

// Enqueue frontend styles
function manan_yt_enqueue_scripts() {
    wp_enqueue_style('manan-yt-feed-style', MANAN_YT_FEED_URL . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'manan_yt_enqueue_scripts');
