<?php
/*
Plugin Name: Manan - Share Blog Post
Plugin URI: https://mananacademy.com/
Description: Adds social share buttons for Facebook, X (Twitter), LinkedIn, and custom sites at the bottom of each blog post.
Version: 1.1.1
Author: Manan Ahmed Broti
Author URI: https://ahmedmanan.com/
License: GPL2
*/

if (!defined('ABSPATH')) exit;

// ===== Enqueue Styles and Font Awesome =====
function manan_share_enqueue_assets() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');

    // Inline style with CSS variable fallback
    wp_add_inline_style('font-awesome', '
        :root {
            --manan-theme-color: var(--wp--preset--color--primary, #0073aa);
        }
        .manan-share-box {
            margin-top: 30px;
            padding: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }
        .manan-share-box span {
            font-weight: 600;
            margin-right: 10px;
        }
        .manan-share-button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: #f5f5f5;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .manan-share-button:hover {
            background: var(--manan-theme-color);
            color: #fff;
            border-color: var(--manan-theme-color);
        }
        .manan-copy-success {
            color: var(--manan-theme-color);
            font-size: 13px;
            display: none;
        }
    ');
}
add_action('wp_enqueue_scripts', 'manan_share_enqueue_assets');

// ===== Copy Link JavaScript =====
function manan_share_copy_script() {
    ?>
    <script>
    function mananCopyLink(url, el) {
        navigator.clipboard.writeText(url).then(() => {
            el.nextElementSibling.style.display = 'inline';
            setTimeout(() => {
                el.nextElementSibling.style.display = 'none';
            }, 2000);
        });
    }

    // Auto-detect theme color dynamically from DOM
    document.addEventListener('DOMContentLoaded', function() {
        const rootStyle = getComputedStyle(document.documentElement);
        let themeColor = rootStyle.getPropertyValue('--wp--preset--color--primary').trim();
        if (!themeColor) {
            const linkColor = getComputedStyle(document.querySelector('a')).color;
            themeColor = linkColor || '#0073aa';
        }
        document.documentElement.style.setProperty('--manan-theme-color', themeColor);
    });
    </script>
    <?php
}
add_action('wp_footer', 'manan_share_copy_script');

// ===== Default Options =====
function manan_share_default_options() {
    $defaults = [
        'facebook' => 1,
        'twitter' => 1,
        'linkedin' => 1,
        'reddit' => 1,
        'pinterest' => 0,
        'copylink' => 1,
    ];
    foreach ($defaults as $key => $value) {
        if (get_option("manan_share_$key") === false) {
            update_option("manan_share_$key", $value);
        }
    }
}
register_activation_hook(__FILE__, 'manan_share_default_options');

// ===== Generate Share Buttons =====
function manan_generate_share_buttons($content) {
    if (is_single() && in_the_loop() && is_main_query()) {
        global $post;
        $url   = urlencode(get_permalink($post->ID));
        $title = urlencode(get_the_title($post->ID));
        $plain_url = esc_url(get_permalink($post->ID));

        $buttons = '';

        if (get_option('manan_share_facebook')) {
            $buttons .= '<a class="manan-share-button" href="https://www.facebook.com/sharer/sharer.php?u='.$url.'" target="_blank"><i class="fa fa-facebook"></i> Facebook</a>';
        }
        if (get_option('manan_share_twitter')) {
            $buttons .= '<a class="manan-share-button" href="https://x.com/intent/post?url='.$url.'&text='.$title.'" target="_blank"><i class="fa-solid fa-x"></i> X</a>';
        }
        if (get_option('manan_share_linkedin')) {
            $buttons .= '<a class="manan-share-button" href="https://www.linkedin.com/sharing/share-offsite/?url='.$url.'" target="_blank"><i class="fa fa-linkedin"></i> LinkedIn</a>';
        }
        if (get_option('manan_share_reddit')) {
            $buttons .= '<a class="manan-share-button" href="https://www.reddit.com/submit?url='.$url.'&title='.$title.'" target="_blank"><i class="fa fa-reddit"></i> Reddit</a>';
        }
        if (get_option('manan_share_pinterest')) {
            $buttons .= '<a class="manan-share-button" href="https://pinterest.com/pin/create/button/?url='.$url.'&description='.$title.'" target="_blank"><i class="fa fa-pinterest"></i> Pinterest</a>';
        }
        if (get_option('manan_share_copylink')) {
            $buttons .= '<a class="manan-share-button" href="javascript:void(0)" onclick="mananCopyLink(\''.$plain_url.'\', this)"><i class="fa fa-link"></i> Copy Link</a><span class="manan-copy-success">Copied!</span>';
        }

        $share_html = '<div class="manan-share-box"><span>Share this post:</span>' . $buttons . '</div>';
        return $content . $share_html;
    }
    return $content;
}
add_filter('the_content', 'manan_generate_share_buttons');

// ===== Include Admin Settings =====
require_once plugin_dir_path(__FILE__) . 'admin-settings.php';
