<?php
if (!defined('ABSPATH')) exit;

// Add menu page
function manan_share_menu() {
    add_options_page(
        'Manan Share Settings',
        'Manan Share',
        'manage_options',
        'manan-share-settings',
        'manan_share_settings_page'
    );
}
add_action('admin_menu', 'manan_share_menu');

// Register settings
function manan_share_register_settings() {
    $options = ['facebook', 'twitter', 'linkedin', 'reddit', 'pinterest', 'copylink'];
    foreach ($options as $opt) {
        register_setting('manan_share_settings_group', "manan_share_$opt");
    }
}
add_action('admin_init', 'manan_share_register_settings');

// Settings page content
function manan_share_settings_page() { ?>
    <div class="wrap">
        <h1>Manan - Share Blog Post Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('manan_share_settings_group'); ?>
            <table class="form-table">
                <tr><th>Enable Share Buttons</th><td>
                    <label><input type="checkbox" name="manan_share_facebook" value="1" <?php checked(1, get_option('manan_share_facebook')); ?>> Facebook</label><br>
                    <label><input type="checkbox" name="manan_share_twitter" value="1" <?php checked(1, get_option('manan_share_twitter')); ?>> X (Twitter)</label><br>
                    <label><input type="checkbox" name="manan_share_linkedin" value="1" <?php checked(1, get_option('manan_share_linkedin')); ?>> LinkedIn</label><br>
                    <label><input type="checkbox" name="manan_share_reddit" value="1" <?php checked(1, get_option('manan_share_reddit')); ?>> Reddit</label><br>
                    <label><input type="checkbox" name="manan_share_pinterest" value="1" <?php checked(1, get_option('manan_share_pinterest')); ?>> Pinterest</label><br>
                    <label><input type="checkbox" name="manan_share_copylink" value="1" <?php checked(1, get_option('manan_share_copylink')); ?>> Copy Link</label>
                </td></tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }
