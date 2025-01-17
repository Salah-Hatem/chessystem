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

function custom_login_redirect($user_login, $user)
{
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

add_action('wp_login', 'custom_login_redirect', 999, 2); // Set high priority by using a lower number



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
