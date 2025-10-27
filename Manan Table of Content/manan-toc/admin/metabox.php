<?php
if (!defined('ABSPATH')) exit;

add_action('add_meta_boxes', function () {
    add_meta_box('manan_toc_box', 'Manan TOC Sections', 'manan_toc_metabox_callback', 'post', 'side', 'default');
});

function manan_toc_metabox_callback($post) {
    $content = get_post_field('post_content', $post->ID);
    preg_match_all('/<h([2-4])[^>]*>(.*?)<\/h\1>/', $content, $matches);
    $headings = array_unique($matches[2]);

    $saved_headings = get_post_meta($post->ID, '_manan_toc_headings', true) ?: [];
    $start_collapsed = get_post_meta($post->ID, '_manan_toc_collapsed', true);

    if (empty($headings)) {
        echo '<p>No headings found in this post.</p>';
        return;
    }

    echo '<p><strong>Select headings to include in TOC:</strong></p>';
    foreach ($headings as $heading) {
        $checked = in_array($heading, $saved_headings) ? 'checked' : '';
        echo '<label style="display:block;margin-bottom:3px;">
                <input type="checkbox" name="manan_toc_headings[]" value="'.esc_attr($heading).'" '.$checked.'> 
                '.esc_html($heading).'
              </label>';
    }

    echo '<hr><label style="margin-top:8px;display:block;">
    <p><strong>Select Default State:</strong></p>
            <input type="checkbox" name="manan_toc_collapsed" value="1" '.checked($start_collapsed, 1, false).'>
            Start closed by default
          </label>';

    wp_nonce_field('manan_toc_nonce_action', 'manan_toc_nonce');
}

add_action('save_post', function ($post_id) {
    if (!isset($_POST['manan_toc_nonce']) || !wp_verify_nonce($_POST['manan_toc_nonce'], 'manan_toc_nonce_action')) return;

    $headings = isset($_POST['manan_toc_headings']) ? array_map('sanitize_text_field', $_POST['manan_toc_headings']) : [];
    update_post_meta($post_id, '_manan_toc_headings', $headings);

    $collapsed = isset($_POST['manan_toc_collapsed']) ? 1 : 0;
    update_post_meta($post_id, '_manan_toc_collapsed', $collapsed);
});
