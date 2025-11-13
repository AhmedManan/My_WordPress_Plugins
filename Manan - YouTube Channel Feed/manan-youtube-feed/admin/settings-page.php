<?php
if (!defined('ABSPATH')) exit;

function manan_yt_add_admin_menu() {
    add_menu_page(
        'YouTube Feed Settings',
        'YouTube Feed',
        'manage_options',
        'manan-youtube-feed',
        'manan_yt_settings_page_html',
        'dashicons-video-alt3'
    );
}
add_action('admin_menu', 'manan_yt_add_admin_menu');

function manan_yt_register_settings() {
    register_setting('manan_yt_options', 'manan_yt_api_key');
    register_setting('manan_yt_options', 'manan_yt_channel_id');
    register_setting('manan_yt_options', 'manan_yt_video_count');
}
add_action('admin_init', 'manan_yt_register_settings');

function manan_yt_settings_page_html() {
    ?>
    <div class="wrap">
        <h1>🎬 Manan - YouTube Channel Feed Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('manan_yt_options'); ?>
            <?php do_settings_sections('manan_yt_options'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">YouTube API Key</th>
                    <td><input type="text" name="manan_yt_api_key" value="<?php echo esc_attr(get_option('manan_yt_api_key')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Channel ID</th>
                    <td><input type="text" name="manan_yt_channel_id" value="<?php echo esc_attr(get_option('manan_yt_channel_id')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Number of Videos</th>
                    <td><input type="number" name="manan_yt_video_count" value="<?php echo esc_attr(get_option('manan_yt_video_count', 6)); ?>" min="1" max="50"></td>
                </tr>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>

        <h2>Usage</h2>
        <p>Use the shortcode <code>[manan_youtube_feed]</code> to display your YouTube channel feed anywhere on your site.</p>
    </div>
    <?php
}
