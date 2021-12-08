<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://netwarriorservices.com
 * @since      1.0.0
 *
 * @package    Task_Manager
 * @subpackage Task_Manager/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Task_Manager
 * @subpackage Task_Manager/includes
 * @author     Louie Sanchez <netwarriorservices@gmail.com>
 */
class Task_Manager_Uninstall {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		/**
		 * Global Wordpress DB
		 */
//		global $wpdb;
//
//		$tmp_tables = [
//			$wpdb->prefix . 'tmp_users',
//			$wpdb->prefix . 'tmp_user_groups',
//			$wpdb->prefix . 'tmp_user_access_types',
//			$wpdb->prefix . 'tmp_projects',
//			$wpdb->prefix . 'tmp_project_categories',
//			$wpdb->prefix . 'tmp_project_status',
//			$wpdb->prefix . 'tmp_project_groups',
//			$wpdb->prefix . 'tmp_project_users',
//			$wpdb->prefix . 'tmp_tasks',
//			$wpdb->prefix . 'tmp_task_status',
//			$wpdb->prefix . 'tmp_task_types',
//			$wpdb->prefix . 'tmp_task_labels',
//			$wpdb->prefix . 'tmp_task_priorities',
//			$wpdb->prefix . 'tmp_task_comments',
//			$wpdb->prefix . 'tmp_task_groups',
//			$wpdb->prefix . 'tmp_task_users',
//			$wpdb->prefix . 'tmp_tickets',
//			$wpdb->prefix . 'tmp_ticket_types',
//		];
//
//		foreach ($tmp_tables as $tmp_table) {
//			$wpdb->query("DROP TABLE IF EXISTS $tmp_table");
//		}

	}

}
