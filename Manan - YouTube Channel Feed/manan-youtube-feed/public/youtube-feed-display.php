<?php
if (!defined('ABSPATH')) exit;

function manan_youtube_feed_shortcode() {
    $api_key = get_option('manan_yt_api_key');
    $channel_id = get_option('manan_yt_channel_id');
    $video_count = get_option('manan_yt_video_count', 6);

    if (!$api_key || !$channel_id) {
        return '<p style="color:red;">Please set your API Key and Channel ID in the plugin settings.</p>';
    }

    $api_url = "https://www.googleapis.com/youtube/v3/search?key=$api_key&channelId=$channel_id&part=snippet,id&order=date&maxResults=$video_count";

    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) return '<p>Unable to fetch YouTube feed.</p>';

    $videos = json_decode(wp_remote_retrieve_body($response));

    if (empty($videos->items)) return '<p>No videos found.</p>';

    ob_start(); ?>
    <div class="manan-youtube-gallery">
        <?php foreach ($videos->items as $video):
            if (isset($video->id->videoId)): ?>
                <div class="yt-video">
                    <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($video->id->videoId); ?>" frameborder="0" allowfullscreen></iframe>
                    <h4><?php echo esc_html($video->snippet->title); ?></h4>
                </div>
            <?php endif;
        endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('manan_youtube_feed', 'manan_youtube_feed_shortcode');
