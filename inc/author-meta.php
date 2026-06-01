<?php
/**
 * Custom Author Meta Box and User Profile Fields
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// 1. Add Meta Box to cmr_news post type
add_action( 'add_meta_boxes', 'cmr_news_add_author_meta_box' );
function cmr_news_add_author_meta_box() {
    add_meta_box(
        'cmr_news_author_meta_box', // ID
        __( 'Media Release Author', 'quanto' ), // Title
        'cmr_news_author_meta_box_callback', // Callback
        'cmr_news', // Post type
        'side', // Context
        'default' // Priority
    );
}

function cmr_news_author_meta_box_callback( $post ) {
    wp_nonce_field( 'cmr_news_author_save', 'cmr_news_author_nonce' );
    $current_author = get_post_meta( $post->ID, '_cmr_news_custom_author', true );
    
    $users = get_users();
    
    echo '<p><label for="cmr_news_custom_author">' . esc_html__( 'Select an author for this media release:', 'quanto' ) . '</label></p>';
    echo '<select name="cmr_news_custom_author" id="cmr_news_custom_author" style="width:100%;">';
    echo '<option value="">' . esc_html__( '-- Select Author --', 'quanto' ) . '</option>';
    foreach ( $users as $user ) {
        $selected = selected( $current_author, $user->ID, false );
        echo '<option value="' . esc_attr( $user->ID ) . '" ' . $selected . '>' . esc_html( $user->display_name ) . '</option>';
    }
    echo '</select>';
}

add_action( 'save_post', 'cmr_news_save_author_meta_box' );
function cmr_news_save_author_meta_box( $post_id ) {
    if ( ! isset( $_POST['cmr_news_author_nonce'] ) || ! wp_verify_nonce( $_POST['cmr_news_author_nonce'], 'cmr_news_author_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    if ( isset( $_POST['cmr_news_custom_author'] ) ) {
        update_post_meta( $post_id, '_cmr_news_custom_author', sanitize_text_field( $_POST['cmr_news_custom_author'] ) );
    }
}

// 2. Add Custom Fields to User Profile
add_action( 'show_user_profile', 'cmr_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'cmr_extra_user_profile_fields' );

function cmr_extra_user_profile_fields( $user ) {
    ?>
    <h3><?php esc_html_e( 'Extra Profile Information', 'quanto' ); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="designation"><?php esc_html_e( 'Designation', 'quanto' ); ?></label></th>
            <td>
                <input type="text" name="designation" id="designation" value="<?php echo esc_attr( get_the_author_meta( 'designation', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php esc_html_e( 'Please enter the user designation (e.g. Senior Analyst - Smart Mobility Practice, CMR).', 'quanto' ); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="linkedin_url"><?php esc_html_e( 'LinkedIn URL', 'quanto' ); ?></label></th>
            <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 24px; color: #0077b5;" class="dashicons dashicons-linkedin"></span>
                    <input type="url" name="linkedin_url" id="linkedin_url" value="<?php echo esc_attr( get_the_author_meta( 'linkedin_url', $user->ID ) ); ?>" class="regular-text" />
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="x_url"><?php esc_html_e( 'X (Twitter) URL', 'quanto' ); ?></label></th>
            <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 24px; color: #1da1f2;" class="dashicons dashicons-twitter"></span>
                    <input type="url" name="x_url" id="x_url" value="<?php echo esc_attr( get_the_author_meta( 'x_url', $user->ID ) ); ?>" class="regular-text" />
                </div>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'personal_options_update', 'cmr_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'cmr_save_extra_user_profile_fields' );

function cmr_save_extra_user_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    
    update_user_meta( $user_id, 'designation', sanitize_text_field( $_POST['designation'] ) );
    update_user_meta( $user_id, 'linkedin_url', esc_url_raw( $_POST['linkedin_url'] ) );
    update_user_meta( $user_id, 'x_url', esc_url_raw( $_POST['x_url'] ) );
}
