<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://netwarriorservices.com
 * @since      1.0.0
 *
 * @package    Task_Manager
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Wp Global variable
global $wpdb;

// Table Lists
$users_table = $wpdb->prefix . 'tmp_users';
$user_group_table = $wpdb->prefix . 'tmp_user_groups';
$user_access_type_table = $wpdb->prefix . 'tmp_user_access_types';

$project_table = $wpdb->prefix . 'tmp_projects';
$project_category_table = $wpdb->prefix . 'tmp_project_categories';
$project_status_table = $wpdb->prefix . 'tmp_project_status';
$project_groups_table = $wpdb->prefix . 'tmp_project_groups';
$project_users_table = $wpdb->prefix . 'tmp_project_users';

$task_table = $wpdb->prefix . 'tmp_tasks';
$task_status_table = $wpdb->prefix . 'tmp_task_status';
$task_type_table = $wpdb->prefix . 'tmp_task_types';
$task_label_table = $wpdb->prefix . 'tmp_task_labels';
$task_priority_table = $wpdb->prefix . 'tmp_task_priorities';
$task_comment_table = $wpdb->prefix . 'tmp_task_comments';
$task_groups_table = $wpdb->prefix . 'tmp_task_groups';
$task_users_table = $wpdb->prefix . 'tmp_task_users';

$ticket_table = $wpdb->prefix . 'tmp_tickets';
$ticket_type_table = $wpdb->prefix . 'tmp_ticket_types';


// Delete table if exists
//$wpdb->query("DROP TABLE IF EXISTS {$users_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$user_group_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$user_access_type_table}");
//
//$wpdb->query("DROP TABLE IF EXISTS {$project_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$project_category_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$project_status_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$project_groups_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$project_users_table}");
//
//$wpdb->query("DROP TABLE IF EXISTS {$task_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$task_status_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$task_type_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$task_label_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$task_priority_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$task_comment_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$task_groups_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$task_users_table}");
//
//$wpdb->query("DROP TABLE IF EXISTS {$ticket_table}");
//$wpdb->query("DROP TABLE IF EXISTS {$ticket_type_table}");
