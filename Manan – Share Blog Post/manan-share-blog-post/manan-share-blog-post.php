<?php
/*
Plugin Name: Manan - Share Blog Post
Plugin URI: https://mananacademy.com/
Description: Adds social share buttons for Facebook, X (Twitter), LinkedIn, and custom sites at the bottom of each blog post.
Version: 1.0
Author: Manan Ahmed Broti
Author URI: https://ahmedmanan.com/
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Prevent direct access

// Enqueue minimal CSS
function manan_share_blog_post_styles() {
    echo '<style>
        .manan-share-box {
            margin-top: 30px;
            padding: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }
        .manan-share-box span {
            font-weight: 600;
            margin-right: 10px;
        }
        .manan-share-button {
            display: inline-block;
            padding: 8px 14px;
            background: #f5f5f5;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .manan-share-button:hover {
            background: #0073aa;
            color: #fff;
        }
    </style>';
}
add_action('wp_head', 'manan_share_blog_post_styles');

// Generate share buttons HTML
function manan_generate_share_buttons($content) {
    if (is_single() && in_the_loop() && is_main_query()) {
        global $post;
        $url   = urlencode(get_permalink($post->ID));
        $title = urlencode(get_the_title($post->ID));

        $share_html = '<div class="manan-share-box">';
        $share_html .= '<span>Share this post:</span>';

        // Facebook
        $share_html .= '<a class="manan-share-button" href="https://www.facebook.com/sharer/sharer.php?u='.$url.'" target="_blank" rel="noopener">Facebook</a>';

        // X (Twitter)
        $share_html .= '<a class="manan-share-button" href="https://twitter.com/intent/tweet?url='.$url.'&text='.$title.'" target="_blank" rel="noopener">X</a>';

        // LinkedIn
        $share_html .= '<a class="manan-share-button" href="https://www.linkedin.com/sharing/share-offsite/?url='.$url.'" target="_blank" rel="noopener">LinkedIn</a>';

        // Example custom site (Reddit)
        $share_html .= '<a class="manan-share-button" href="https://www.reddit.com/submit?url='.$url.'&title='.$title.'" target="_blank" rel="noopener">Reddit</a>';

        // Example custom site (Pinterest)
        $share_html .= '<a class="manan-share-button" href="https://pinterest.com/pin/create/button/?url='.$url.'&description='.$title.'" target="_blank" rel="noopener">Pinterest</a>';

        $share_html .= '</div>';

        return $content . $share_html;
    }
    return $content;
}
add_filter('the_content', 'manan_generate_share_buttons');

// Optional shortcode [manan_share_buttons]
function manan_share_shortcode() {
    global $post;
    $url   = urlencode(get_permalink($post->ID));
    $title = urlencode(get_the_title($post->ID));

    ob_start();
    ?>
    <div class="manan-share-box">
        <span>Share this post:</span>
        <a class="manan-share-button" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="noopener">Facebook</a>
        <a class="manan-share-button" href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" target="_blank" rel="noopener">X</a>
        <a class="manan-share-button" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $url; ?>" target="_blank" rel="noopener">LinkedIn</a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('manan_share_buttons', 'manan_share_shortcode');
