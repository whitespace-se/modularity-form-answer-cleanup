<?php
/*
Plugin Name: Modularity Form Answer Cleanup
Description: Removes old form data from Modularity Forms
Author: Whitespace AB
Version: 1.0.1
*/

register_activation_hook(__FILE__, "form_answer_cleanup_register_schedule");
function form_answer_cleanup_register_schedule()
{
  if (!wp_next_scheduled("form_answer_cleanup_daily")) {
    wp_schedule_event(strtotime("06:00:00"), "daily", "form_answer_cleanup_daily");
  }
}

add_action("form_answer_cleanup_daily", "form_answer_cleanup");
function form_answer_cleanup($months = '-100 days', $no_of_submissions = 500)
{
  $time = strtotime($months, time());
  $date = date("Y-m-d", $time);

  $query = new \WP_Query(array(
    'posts_per_page' => $no_of_submissions,
    'post_type' => 'form-submissions',
    'orderby' => 'date',
    'order'   => 'ASC',
    'date_query' => array(
      'column' => 'post_date',
      'before' => $date
    ),
  ));

  $submissions = $query->get_posts();

  foreach ($submissions as $submission) {
    wp_delete_post($submission->ID, true);
  }
}

register_deactivation_hook(__FILE__, "form_answer_cleanup_remove_schedule");
function form_answer_cleanup_remove_schedule()
{
  wp_clear_scheduled_hook("form_answer_cleanup_daily");
}
