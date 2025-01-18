<?php

/**
 * Plugin Name: Tutor-Buddyboss-extra
 * Add Custom fields to the pmpro checkout and restrict access to logged in users.
 * Version: 1.0
 * Author: Salah
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}



/****************************** Add Custom Fields to PMPRO checkout************************************/
function my_pmpro_custom_fields()
{
    $user_first_name = '';
    $user_last_name = '';
    if (!empty($_REQUEST['first_name'])) {
        $user_first_name = sanitize_text_field($_REQUEST['first_name']);
    } elseif (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_first_name = $current_user->user_firstname;
    }
    if (!empty($_REQUEST['last_name'])) {
        $user_last_name = sanitize_text_field($_REQUEST['last_name']);
    } elseif (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_last_name = $current_user->user_lastname;
    }
?>
    <div class="pmpro_checkout ">
        <div class="pmpro_checkout-field pmpro_checkout-field-name pmpro_cols-2 ">
            <div class="pmpro_checkout-field-first_name">
                <label for="first_name"><?php _e('First Name', 'paid-memberships-pro'); ?></label>
                <input id="first_name" name="first_name" type="text" class=" input pmpro_form_input pmpro_form_input-text" size="30" value="<?php echo esc_attr($user_first_name); ?>" />
            </div>
            <div class="pmpro_checkout-field-last_name">
                <label for="last_name"><?php _e('Last Name', 'paid-memberships-pro'); ?></label>
                <input id="last_name" name="last_name" type="text" class="input input pmpro_form_input pmpro_form_input-text" size="30" value="<?php echo esc_attr($user_last_name); ?>" />
            </div>
        </div>
    </div>
    
<?php
}
add_action('pmpro_checkout_after_username', 'my_pmpro_custom_fields', 5);

function my_pmpro_save_custom_fields($user_id)
{
    if (!empty($_REQUEST['first_name'])) {
        update_user_meta($user_id, 'first_name', sanitize_text_field($_REQUEST['first_name']));
    }
    if (!empty($_REQUEST['last_name'])) {
        update_user_meta($user_id, 'last_name', sanitize_text_field($_REQUEST['last_name']));
    }
}
add_action('pmpro_after_checkout', 'my_pmpro_save_custom_fields');



/********************************************/

function restrict_site_to_logged_in_users()
{

    // Get the ID of the homepage
    $home_page_id = get_option('page_on_front');
    // Array of page IDs or slugs that are accessible to non-logged-in users
    $allowed_pages = array(
        $home_page_id, // Replace with the actual slug of the landing page
        'membership-checkout', // Replace with the actual slug of the membership page
        'membership-levels',
        'terms-of-service',
        'register',
        'en',
        'chess-for-schools'
        // Add more pages as needed
        // You can also use page IDs
    );

    // Check if the user is not logged in
    if (!is_user_logged_in()) {
        // Get the current page ID or slug
        global $post;
        $current_page = $post->ID; // Use ID
        $current_slug = $post->post_name; // Use slug

        // Check if the current page is not in the allowed pages
        if (!in_array($current_page, $allowed_pages) && !in_array($current_slug, $allowed_pages)) {
            // Redirect to the login page
            wp_redirect(wp_login_url());
            exit();
        }
    }
}
add_action('template_redirect', 'restrict_site_to_logged_in_users');
