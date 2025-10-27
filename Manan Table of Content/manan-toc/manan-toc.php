<?php
/**
 * Plugin Name: Manan Table of Content
 * Description: A sleek, collapsible Table of Content plugin that allows selecting which post sections to include and set default collapse state.
 * Version: 1.1
 * Author: Manan Ahmed Broti
 */

if (!defined('ABSPATH')) exit;

define('MANAN_TOC_PATH', plugin_dir_path(__FILE__));
define('MANAN_TOC_URL', plugin_dir_url(__FILE__));

require_once MANAN_TOC_PATH . 'admin/metabox.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('manan-toc-style', MANAN_TOC_URL . 'assets/toc.css');
    wp_enqueue_script('manan-toc-script', MANAN_TOC_URL . 'assets/toc.js', [], '1.2', true);
});

add_filter('the_content', 'manan_toc_add_to_content');
function manan_toc_add_to_content($content) {
    if (is_singular('post')) {
        global $post;
        $selected_headings = get_post_meta($post->ID, '_manan_toc_headings', true);
        $start_collapsed = get_post_meta($post->ID, '_manan_toc_collapsed', true);

        if (!$selected_headings) return $content;

        preg_match_all('/<h([2-4])[^>]*>(.*?)<\/h\1>/', $content, $matches, PREG_SET_ORDER);

        $isCollapsed = $start_collapsed ? 'true' : 'false';
        $buttonText = $start_collapsed ? 'Show' : 'Hide';
        $collapsedClass = $start_collapsed ? 'collapsed' : '';

        $toc = '<div class="manan-toc">';
        $toc .= '<div class="manan-toc-header">';
        $toc .= '<strong>Table of Contents</strong>';
        $toc .= '<button class="manan-toc-toggle" aria-expanded="'.(!$start_collapsed ? 'true' : 'false').'">'.$buttonText.'</button>';
        $toc .= '</div><ul class="manan-toc-list '.$collapsedClass.'">';

        foreach ($matches as $match) {
            $heading_text = strip_tags($match[2]);
            $anchor = sanitize_title($heading_text);

            if (in_array($heading_text, $selected_headings)) {
                $content = str_replace($match[0], '<h'.$match[1].' id="'.$anchor.'">'.$match[2].'</h'.$match[1].'>', $content);
                $toc .= '<li><a href="#'.$anchor.'">'.$heading_text.'</a></li>';
            }
        }

        $toc .= '</ul></div>';

        $content = $toc . $content;
    }

    return $content;
}
