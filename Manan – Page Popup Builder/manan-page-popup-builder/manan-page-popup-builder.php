<?php
/*
 Plugin Name: Manan - Page Popup Builder
 Plugin URI: https://mananacademy.com/
 Description: Create Gutenberg-powered popups and assign them to specific pages.
 Version: 1.1
 Author: Manan Ahmed Broti
 Author URI: https://ahmedmanan.com/
 License: GPL2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* --------------------------------------------------------------------------
 * Register CPT
 * -------------------------------------------------------------------------- */
function manan_register_popup_cpt() {
    $labels = array(
        'name'               => __( 'Popups', 'manan' ),
        'singular_name'      => __( 'Popup', 'manan' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-welcome-widgets-menus',
        'supports'           => array( 'title', 'editor' ),
        'capability_type'    => 'post',
    );

    register_post_type( 'manan_popup', $args );
}
add_action( 'init', 'manan_register_popup_cpt' );

/* --------------------------------------------------------------------------
 * Meta box: Page assignment
 * -------------------------------------------------------------------------- */
function manan_popup_meta_box() {
    add_meta_box(
        'manan_popup_settings',
        __( 'Popup Settings', 'manan' ),
        'manan_popup_settings_html',
        'manan_popup',
        'side'
    );
}
add_action( 'add_meta_boxes', 'manan_popup_meta_box' );

function manan_popup_settings_html( $post ) {
    // nonce for verification
    wp_nonce_field( 'manan_popup_meta_save', 'manan_popup_meta_nonce' );

    $assigned_page = get_post_meta( $post->ID, 'manan_popup_page', true );
    $pages = get_pages();

    $delay = get_post_meta($post->ID, 'manan_popup_delay', true);


    echo '<label for="manan_popup_page">' . esc_html__( 'Select Page', 'manan' ) . '</label>';
    echo '<select id="manan_popup_page" name="manan_popup_page" style="width:100%;">';
    echo '<option value="">' . esc_html__( '-- None --', 'manan' ) . '</option>';

    foreach ( $pages as $page ) {
        $selected = ( (string) $assigned_page === (string) $page->ID ) ? 'selected' : '';
        echo sprintf(
            '<option value="%s" %s>%s</option>',
            esc_attr( $page->ID ),
            $selected,
            esc_html( $page->post_title )
        );
    }

    echo '</select>';

    echo '<br><br><label for="manan_popup_delay">Popup Delay (seconds):</label>';
    echo '<input type="number" name="manan_popup_delay" id="manan_popup_delay" value="' . esc_attr($delay) . '" style="width:100%;" min="0">';
}

/* --------------------------------------------------------------------------
 * Save meta safely
 * -------------------------------------------------------------------------- */
function manan_save_popup_meta( $post_id ) {
    // Save delay Field
    if (isset($_POST['manan_popup_delay'])) {
    update_post_meta($post_id, 'manan_popup_delay', intval($_POST['manan_popup_delay']));
    }

    // Verify nonce
    if ( ! isset( $_POST['manan_popup_meta_nonce'] ) || ! wp_verify_nonce( $_POST['manan_popup_meta_nonce'], 'manan_popup_meta_save' ) ) {
        return;
    }

    // Autosave or revision? bail.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Only for our CPT
    if ( isset( $_POST['post_type'] ) && 'manan_popup' !== $_POST['post_type'] ) {
        return;
    }

    if ( isset( $_POST['manan_popup_page'] ) ) {
        $page_id = sanitize_text_field( wp_unslash( $_POST['manan_popup_page'] ) );
        if ( $page_id === '' ) {
            delete_post_meta( $post_id, 'manan_popup_page' );
        } else {
            update_post_meta( $post_id, 'manan_popup_page', $page_id );
        }
    }
}
add_action( 'save_post', 'manan_save_popup_meta' );

/* --------------------------------------------------------------------------
 * Output popup on front-end for assigned page
 * -------------------------------------------------------------------------- */
function manan_display_popup() {
    if ( is_admin() ) {
        return; // don't output in admin
    }

    if ( ! is_page() ) {
        return;
    }

    $current_page = get_queried_object_id();

    $popup_query = new WP_Query( array(
        'post_type'      => 'manan_popup',
        'meta_key'       => 'manan_popup_page',
        'meta_value'     => $current_page,
        'posts_per_page' => 1,
    ) );

    if ( ! $popup_query->have_posts() ) {
        return;
    }

    while ( $popup_query->have_posts() ) {
        $popup_query->the_post();
        $post_obj = get_post();
        $popup_content = apply_filters( 'the_content', $post_obj->post_content );
        $popup_title = get_the_title( $post_obj );

        // Output (safe-ish — content comes from WP editor)
        ?>
        <style>
            /* Simple responsive modal styles */
            #manan-popup-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.65);display:flex;align-items:center;justify-content:center;z-index:999999;}
            #manan-popup-box{background:#fff;width:90%;max-width:820px;padding:22px;border-radius:12px;position:relative;box-shadow:0 8px 30px rgba(0,0,0,0.3);overflow:auto;max-height:90vh;}
            #manan-popup-close{position:absolute;top:10px;right:10px;cursor:pointer;font-size:18px;background:#111;color:#fff;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:none;}
            @media(min-width:900px){ #manan-popup-box{width:70%;} }
        </style>

        <div id="manan-popup-overlay" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $popup_title ); ?>">
            <div id="manan-popup-box">
                <button id="manan-popup-close" aria-label="<?php esc_attr_e( 'Close popup', 'manan' ); ?>">&times;</button>
                <?php echo $popup_content; ?>
            </div>
        </div>

<script>
(function(){
    var overlay = document.getElementById('manan-popup-overlay');
    var closeBtn = document.getElementById('manan-popup-close');
    var popupDelay = <?php echo intval(get_post_meta(get_the_ID(), 'manan_popup_delay', true)); ?> * 1000;

    if (!overlay || !closeBtn) return;

    // Session storage: show once per session
    try {
        var key = 'manan_popup_shown_page_<?php echo esc_js($current_page); ?>';
        if (sessionStorage.getItem(key)) {
            overlay.remove();
            return;
        }
    } catch(e) {}

    // Hide overlay first
    overlay.style.display = 'none';

    // Show after delay
    setTimeout(function() {
        overlay.style.display = 'flex';

        // Mark popup as shown in session
        try { sessionStorage.setItem(key, '1'); } catch(e){}
    }, popupDelay);

    // Close popup
    closeBtn.addEventListener('click', function(){ overlay.remove(); });

    overlay.addEventListener('click', function(e){
        var box = document.getElementById('manan-popup-box');
        if (!box.contains(e.target)) overlay.remove();
    });

})();
</script>

        <?php
    }

    wp_reset_postdata();
}
add_action( 'wp_footer', 'manan_display_popup', 100 );
