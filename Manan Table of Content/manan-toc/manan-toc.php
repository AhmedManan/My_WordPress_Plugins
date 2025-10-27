<?php
/**
 * Plugin Name: Manan Table of Content
 * Description: A sleek Table of Content plugin that allows selecting which post sections to include.
 * Version: 1.0
 * Author: Manan Ahmed Broti
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('MANAN_TOC_PATH', plugin_dir_path(__FILE__));
define('MANAN_TOC_URL', plugin_dir_url(__FILE__));

// Include admin metabox
require_once MANAN_TOC_PATH . 'admin/metabox.php';

// Enqueue frontend styles
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('manan-toc-style', MANAN_TOC_URL . 'assets/toc.css');
});

// Generate TOC before post content
add_filter('the_content', 'manan_toc_add_to_content');
function manan_toc_add_to_content($content) {
    if (is_singular('post')) {
        global $post;
        $selected_headings = get_post_meta($post->ID, '_manan_toc_headings', true);
        if (!$selected_headings) return $content;

        // Extract headings
        preg_match_all('/<h([2-4])[^>]*>(.*?)<\/h\1>/', $content, $matches, PREG_SET_ORDER);
        $toc = '<div class="manan-toc"><strong>Table of Contents</strong><ul>';

        foreach ($matches as $match) {
            $heading_text = strip_tags($match[2]);
            $anchor = sanitize_title($heading_text);

            if (in_array($heading_text, $selected_headings)) {
                // Add id for scrolling
                $content = str_replace($match[0], '<h'.$match[1].' id="'.$anchor.'">'.$match[2].'</h'.$match[1].'>', $content);
                $toc .= '<li><a href="#'.$anchor.'">'.$heading_text.'</a></li>';
            }
        }

        $toc .= '</ul></div>';

        // Insert TOC at the beginning of the content
        $content = $toc . $content;
    }

    return $content;
}
