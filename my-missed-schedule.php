<?php
/*
Plugin Name: MY Missed Schedule
Author: 水脉烟香
Author URI: https://wptao.com/smyx
Plugin URI: https://wptao.com/my-missed-schedule.html
Description: 重发定时失败的文章。Find missed schedule posts and it republish them correctly. Once every five minutes.
Version: 1.0.1
*/

function add_minute_interval($schedules) {
	$schedules['every5min'] = array('interval' => 300, 'display' => __('Every five minutes'));
	return $schedules;
} 

function missed_schedule() {
	global $wpdb;
	$scheduledIDs = $wpdb -> get_col("SELECT ID FROM $wpdb->posts WHERE post_status='future' AND post_date<=CURRENT_TIMESTAMP() LIMIT 5");
	if ($scheduledIDs) {
		foreach($scheduledIDs as $postID) {
			wp_publish_post($postID);
		} 
	} 
} 
// 插件启用时候的动作
function missed_schedule_activate() {
	if (!wp_next_scheduled('missed_schedule_cron')) {
		wp_schedule_event(time(), 'every5min', 'missed_schedule_cron');
	} 
} 
// 插件停用时候的动作
function missed_schedule_deactivate() {
	if (wp_next_scheduled('missed_schedule_cron')) {
		wp_clear_scheduled_hook('missed_schedule_cron');
	} 
} 
// add_action('init','missed_schedule_activate');
add_filter('cron_schedules', 'add_minute_interval');
add_action('missed_schedule_cron', 'missed_schedule', 10);
register_activation_hook(__FILE__, 'missed_schedule_activate');
register_deactivation_hook(__FILE__, 'missed_schedule_deactivate');

?>