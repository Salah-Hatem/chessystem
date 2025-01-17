<?php

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly


if (!is_user_logged_in()) {
    wp_redirect(esc_url(site_url('/')));
    exit;
  }
get_header();
?>

<?php
$page = get_page_by_path('members');
$page_url = esc_url(get_permalink($page->ID));
$current_user_id = get_current_user_id();
$user_data = get_userdata($current_user_id);
$user_slug = $user_data->user_nicename;
?>

<div id="primary" class="content-area bb-grid-cell">
    <main id="main" class="site-main">
        <div class="parent-container">
            <div class="left-side-dashboard">
                <div class="next-session-container">
                    <span class="your-next-session">Your Next Session</span>
                    <div class="session-title-container">
                        <h3 class="session-title"><?php the_field('session_name'); ?></h3>
                    </div>
                    <div class="session-join-container">
                        <div class="session-date"><?php the_field('next_session_time'); ?></div>
                        <a href="<?php the_field('session_url') ?>">
                            <button class="session-join">Join</button>
                        </a>


                    </div>
                </div>
                <div class="user-points-container">
                    <div class="coin-view coin-type-1">
                        <a class="how-to-collect" href="">How to collect?</a>
                        <?php echo do_shortcode('[gamipress_user_points type="coin" columns="1" thumbnail="yes" thumbnail_size="100"  align="center"]') ?>
                    </div>
                    <div class="coin-view coin-type-2">
                        <a class="how-to-collect" href="">How to collect?</a>
                        <?php echo do_shortcode('[gamipress_user_points type="exp" columns="1" thumbnail="yes" thumbnail_size="100"  align="center"]') ?>
                    </div>
                </div>


            </div>
            <div class="right-side-dashboard">
                <div class="user-info-container">
                    <div class="user-info-wrapper">
                        <div class="user-avatar-1">
                            <span> <?php echo get_avatar(get_current_user_id(), 120) ?> </span>
                        </div>
                        <div class="user-data-view">
                            <?php $user = wp_get_current_user();
                            $display_name = $user->display_name;
                            // print_r($user);
                            ?>
                            <h2>Welcome, <a href="<?php echo $page_url . $user_slug; ?>"><span style="text-decoration: underline;"><?php echo $display_name ?></span></a> </h2>
                            <div>
                                <span id="current-user-latest-acievements">
                                    <?php echo do_shortcode('[gamipress_inline_last_achievements_earned type="badge" limit="4" link="no" label="" thumbnail_size="30"]') ?>
                                </span>
                            </div>

                            <a href="<?php echo $page_url . $user_slug . '/profile/edit'; ?>">Edit Profile</a>
                        </div>
                    </div>
                    <hr class="section-break-v1">
                    <div class="user-current-rank-v1">
                        <h4>Your Rank</h4>
                        <span><?php echo do_shortcode('[gamipress_user_rank type="level" prev_rank="no" current_rank="yes" next_rank="no" excerpt="no" requirements="no" current_user="yes" columns="1" title="yes" align="center" layout="top" link="no" ]') ?></span>

                    </div>
                </div>


            </div>
            <div class="progress-rank user-current-rank-v1">
                <h4>Your Next Rank</h4>
                <?php echo do_shortcode('[gamipress_user_rank type="level" prev_rank="no" current_rank="" next_rank="yes" excerpt="no" requirements="yes" current_user="yes" columns="1" title="yes" align="center" layout="top"  link="no" ]') ?>
                <p>“He who has no patience has nothing”</p>
            </div>

            <div class="bottom-section-container">
                <div class="latest-achievements-title">
                    <h3>Latest Achievements</h3>
                    <a href="<?php echo $page_url . $user_slug . '/achievements'; ?>">View All</a>
                </div>
                <div class="latest-achievements-section">
                    <?php echo do_shortcode('[gamipress_inline_last_achievements_earned type="badge" limit="4" link="no" label="yes"]') ?>

                </div>

            </div>

        </div>



    </main><!-- #main -->
</div><!-- #primary -->

<?php
if (is_search()) {
    get_sidebar('search');
} else {
    get_sidebar('page');
}
?>

<?php
get_footer();
