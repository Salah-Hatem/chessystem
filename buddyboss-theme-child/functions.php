<?php

/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
  // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action('after_setup_theme', 'buddyboss_theme_child_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  // Styles
  wp_enqueue_style('buddyboss-child-css', get_stylesheet_directory_uri() . '/assets/css/custom.css');
  // wp_enqueue_style('buddyboss-child-css3', get_stylesheet_directory_uri() . '/assets/css/theme.min.css');

  wp_enqueue_style('font-s2', 'https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&family=Rajdhani:wght@300;400;500;600;700&display=swap', array(), '5', 'all');

  // Javascript
  wp_enqueue_script('buddyboss-child-js', get_stylesheet_directory_uri() . '/assets/js/custom.js');
}
add_action('wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999);


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here



/*************************  Login Redirect ******************************/

// function custom_login_redirect($user_login, $user)
// {
//   // Check if there is a user to test
//   if (isset($user->roles) && is_array($user->roles)) {
//     // Check for subscriber
//     if (in_array('subscriber', $user->roles)) {
//       wp_redirect(home_url('/student-dashboard'));
//       exit();
//     }
//     // Check for instructor but not admin
//     if (in_array('tutor_instructor', $user->roles) && !in_array('administrator', $user->roles)) {
//       wp_redirect(home_url('/dashboard'));
//       exit();
//     }
//     // Add more roles and redirects as needed
//   }

//   // Default redirect to the homepage if no specific role matches
//   wp_redirect(home_url());
//   exit();
// }
// add_action('wp_login', 'custom_login_redirect', 999, 2); // Set high priority by using a lower number


function custom_login_redirect($user_login, $user) {
    // Get the current URL
    $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Define the part of the URL that indicates the payment gateway
    $payment_gateway_url_part = '/membership-checkout/?pmpro_level=';

    // If the current URL contains the payment gateway URL part, do not redirect
    if (strpos($current_url, $payment_gateway_url_part) !== false) {
        return;
    }

    // Check if there is a user to test
    if (isset($user->roles) && is_array($user->roles)) {
        // Check for subscriber
        if (in_array('subscriber', $user->roles)) {
            wp_redirect(home_url('/members/me'));
            exit();
        }
        // Check for instructor but not admin
        if (in_array('tutor_instructor', $user->roles) && !in_array('administrator', $user->roles)) {
            wp_redirect(home_url('/dashboard'));
            exit();
        }
        // Add more roles and redirects as needed
    }

    // Default redirect to the homepage if no specific role matches
    wp_redirect(home_url());
    exit();
}
add_action('wp_login', 'custom_login_redirect', 999, 2);

function redirect_non_admin_users_from_admin()
{
    if (!current_user_can('administrator') && !wp_doing_ajax()) {
        $user = wp_get_current_user();

        // Check if there is a user to test
        if (isset($user->roles) && is_array($user->roles)) {
            // Check for subscriber
            if (in_array('subscriber', $user->roles)) {
                wp_safe_redirect(home_url('/members/me'));
                exit();
            }
            // Check for instructor but not admin
            if (in_array('tutor_instructor', $user->roles) && !in_array('administrator', $user->roles)) {
                wp_safe_redirect(home_url('/dashboard'));
                exit();
            }
            // Add more roles and redirects as needed
        }

        // Default redirect to the homepage if no specific role matches
        wp_safe_redirect(home_url());
        exit();
    }
}
add_action('admin_init', 'redirect_non_admin_users_from_admin', 1);

function redirect_non_admin_users_from_admin_access()
{
    if (is_admin() && !current_user_can('administrator') && !wp_doing_ajax()) {
        redirect_non_admin_users_from_admin();
    }
}
add_action('template_redirect', 'redirect_non_admin_users_from_admin_access', 1);





// Remove BuddyPanel on certain pages


function bb_remove_buddypanel_menu()
{
  $pageID = get_option('page_on_front');
  // Please change the number inside the array to the
  // Page IDs you need to exclude
  if ((is_page(array($pageID)))) {
    unregister_nav_menu('buddypanel-loggedin');
    unregister_nav_menu('buddypanel-loggedout');
  }
}
add_action('template_redirect', 'bb_remove_buddypanel_menu', 20);


add_filter('tutor_dashboard/nav_items', 'remove_some_links_dashboard');
function remove_some_links_dashboard($links)
{
  unset($links['reviews']);
  unset($links['wishlist']);
  unset($links['enrolled-courses']);
  unset($links['my-quiz-attempts']);
  unset($links['purchase_history']);
  unset($links['calendar']);

  return $links;
}


add_filter('tutor_dashboard/instructor_nav_items', 'remove_some_links', 21);
function remove_some_links($links)
{
  unset($links['withdraw']);
  return $links;
}

add_filter('tutor_dashboard/nav_items/settings/nav_items', function ($nav) {
  unset($nav['withdrawal']);
  return $nav;
});

/************************************************* */
//Redirect Courses Archive Page to the profile courses
function restrict_tutor_lms_courses_page($query) {
    // Check if the user is logged in
    if (is_user_logged_in() && !is_admin() && $query->is_main_query()) {
        // Get the current user
        $user = wp_get_current_user();

        // Define the role to check and the target page to redirect from
        $role_to_check = 'subscriber';
        $redirect_url = home_url('members/me/courses'); // Replace with the actual redirect URL

        // Check if the user has the specific role
        if (in_array($role_to_check, $user->roles)) {
            // Check if the current page is the Tutor LMS "Courses" page
            if (is_post_type_archive('courses') || is_singular('courses')) {
                wp_redirect($redirect_url);
                exit();
            }
			 // Check if the current page is a Tutor LMS course category page
            if (is_tax('course-category')) {
                wp_redirect($redirect_url);
                exit();
			}
        }
    }
}
add_action('pre_get_posts', 'restrict_tutor_lms_courses_page');

/*******************************************/
// Hide billing address fields at checkout
function hide_billing_address_fields_pmpro($hide) {
    // Set this to true to hide the fields
    return true;
}
add_filter('pmpro_hide_billing_address_fields', 'hide_billing_address_fields_pmpro');
// Hide the language dropdown on the login page
add_filter('login_display_language_dropdown', '__return_false');





// Handle AJAX request
function load_simple_test_template()
{
    // Ensure only logged-in users can access this if needed
    if (! is_user_logged_in()) {
        wp_send_json_error('User not logged in');
        wp_die();
    }

    // Load the template part
    ob_start();
    get_template_part('template-parts/simple-test');
    $content = ob_get_clean();

    // Return the content
    wp_send_json_success($content);
    wp_die(); // Terminate immediately to return the response
}
add_action('wp_ajax_load_simple_test', 'load_simple_test_template');
add_action('wp_ajax_nopriv_load_simple_test', 'load_simple_test_template'); // For non-logged-in users if needed





function enqueue_custom_scripts()
{
    // Ensure the path points to the child theme directory
    wp_enqueue_script('custom-ajax', get_stylesheet_directory_uri() . '/assets/js/custom-ajax.js', array('jquery'), null, true);

    // Pass the AJAX URL to the script
    wp_localize_script('custom-ajax', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');


//Adding the Open Graph in the Language Attributes
function add_opengraph_doctype( $output ) {
        return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
    }
add_filter('language_attributes', 'add_opengraph_doctype');
  
//Lets add Open Graph Meta Info
  
function insert_fb_in_head() {
    global $post;
    if ( !is_singular()) //if it is not a post or a page
        return;
       
      
      
        echo '<meta property="og:url" content="' . get_permalink() . '"/>';
        echo '<meta property="og:site_name" content="Chessystem"/>';
    if(!has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
        $default_image="https://chessystem.com/wp-content/uploads/2024/09/Chessystem-Logo-1-1.webp"; //replace this with a default image on your server or an image in your media library
        echo '<meta property="og:image" content="' . $default_image . '"/>';
    }
    else{
        $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
        echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
    }
    echo "
";
}
add_action( 'wp_head', 'insert_fb_in_head', 5 );




