<?php

/**
 * Fired during plugin activation
 *
 * @link       http://netwarriorservices.com
 * @since      1.0.0
 *
 * @package    Task_Manager
 * @subpackage Task_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Task_Manager
 * @subpackage Task_Manager/includes
 * @author     Louie Sanchez <netwarriorservices@gmail.com>
 */
class Task_Manager_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

	}

	/**
	 * Table Function
	 */
	public static function createTables(){


		/**
		 * Global Wordpress DB
		 */
		global $wpdb;

		$version = get_option( 'my_plugin_version', '1.0' );

		/**
		 * Sql Task Table Initial Variables
		 */
		$charset_collate = $wpdb->get_charset_collate();

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



		/**
		 * User Query
		 */
		$user_query = "CREATE TABLE $users_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		user_id int(11) NOT NULL,
		user_group_id int(11) NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * User Group Query
		 */

		$user_group = "CREATE TABLE $user_group_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		project_access_id int(11) NULL,
		task_access_id int(11) NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";


		/**
		 * User Access Type Query
		 */
		$user_access_type = "CREATE TABLE $user_access_type_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";


		/**
		 * Project Table Query
		 */
		$projects = "CREATE TABLE $project_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		description text NULL,
		project_category_id int(11) NULL,
		project_status_id int(11) NULL,
		live_url varchar(255) NULL,
		test_url varchar(255) NULL,
		design_date date NULL,
		development_date date NULL,
		test_date date NULL,
		go_live_date date NULL,
		owner_id int(11) NOT NULL,
		created_at datetime NULL,
		updated_at datetime NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Project Category Table Query
		 */
		$project_category = "CREATE TABLE $project_category_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		slug varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";


		/**
		 * Project Status Table Query
		 */
		$project_status = "CREATE TABLE $project_status_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Task Groups Table Query
		 */
		$project_groups = "CREATE TABLE $project_groups_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		group_id int(11) NOT NULL,
		project_id int(11) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Task Users Table Query
		 */
		$project_users = "CREATE TABLE $project_users_table (
		user_id int(11) NOT NULL,
		project_id int(11) NOT NULL
	) $charset_collate;";


		/**
		 * Task Table Query
		 */
		$tasks = "CREATE TABLE $task_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		project_id int(11) NOT NULL,
		task_type_id int(11) NULL,
		name varchar(255) NOT NULL,
		task_status_id int(11) NULL,
		task_priority_id int(11) NULL,
		task_label_id int(11) NULL,
		description text NULL,
		created_by int(11) NULL,
		esitmate_time varchar(125) NULL,
		start_date date NULL,
		due_date date NULL,
		progress int(8) NULL DEFAULT 0,
		amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
		last_update_by int(11) NULL,
		created_at datetime NULL,
		updated_at datetime NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";


		/**
		 * Task Status Table Query
		 */
		$task_status = "CREATE TABLE $task_status_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		group_name varchar(64) default NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Task Type Table Query
		 */
		$task_type = "CREATE TABLE $task_type_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		slug varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Task Label Table Query
		 */
		$task_label = "CREATE TABLE $task_label_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Task Priority Table Query
		 */
		$task_priority = "CREATE TABLE $task_priority_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		icon varchar(255) NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Task Comments Table Query
		 */
		$task_comment = "CREATE TABLE $task_comment_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		task_id int(11) NOT NULL,
		comment_by int(11) NOT NULL,
		comment text NULL,
		created_at datetime NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Task Groups Table Query
		 */
		$task_groups = "CREATE TABLE $task_groups_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		group_id int(11) NOT NULL,
		task_id int(11) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Task Users Table Query
		 */
		$task_users = "CREATE TABLE $task_users_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		user_id int(11) NOT NULL,
		task_id int(11) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";


		/**
		 * Tickets Table Query
		 */
		$tickets = "CREATE TABLE $ticket_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		description text NULL,
		ticket_type_id int(11) NULL,
		ticket_status_id int(11) NULL,
		ticket_for int(11) NULL,
		created_by int(11) NULL,
		last_update_by int(11) NULL,
		created_at datetime NULL,
		updated_at datetime NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/**
		 * Ticket Type Table Query
		 */
		$ticket_type = "CREATE TABLE $ticket_type_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		/** New Update */
		/** Add Amount Table */
		$singleTask = $wpdb->get_results(  "SHOW COLUMNS FROM `$task_table` LIKE 'amount'"  );
		if(empty($singleTask)){
			$wpdb->query("ALTER TABLE $task_table ADD amount DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER progress");
		}

		


		/**
		 * Query Execution
		 */
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		if( $wpdb->get_var( "SHOW TABLES LIKE '{$users_table}'" ) != $users_table ){
            dbDelta( $user_query );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$user_group_table}'" ) != $user_group_table ){
            dbDelta( $user_group );

            // Insert User Groups
            $wpdb->insert( $user_group_table, array( 'id' => 2, 'name' => 'Assignee', 'project_access_id' => 14, 'task_access_id' => 14));
            $wpdb->insert( $user_group_table, array( 'id' => 3, 'name' => 'Developer', 'project_access_id' => 14, 'task_access_id' => 14));
            $wpdb->insert( $user_group_table, array( 'id' => 4, 'name' => 'Manager', 'project_access_id' => 12, 'task_access_id' => 12));
            $wpdb->insert( $user_group_table, array( 'id' => 5, 'name' => 'Admin', 'project_access_id' => 13, 'task_access_id' => 13));
            $wpdb->insert( $user_group_table, array( 'id' => 6, 'name' => 'Follower', 'project_access_id' => 8, 'task_access_id' => 8));
            $wpdb->insert( $user_group_table, array( 'id' => 7, 'name' => 'Editor', 'project_access_id' => 11, 'task_access_id' => 11));
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$user_access_type_table}'" ) != $user_access_type_table ){
            dbDelta( $user_access_type );

            // Insert User Access Type
            $wpdb->insert( $user_access_type_table, array( 'id' => 8, 'name' => 'Read Only - For Assigned'));
            $wpdb->insert( $user_access_type_table, array( 'id' => 11, 'name' => 'Read and Edit - For All'));
            $wpdb->insert( $user_access_type_table, array( 'id' => 12, 'name' => 'Create, Read and Write - For All'));
            $wpdb->insert( $user_access_type_table, array( 'id' => 13, 'name' => 'All Access With Setting'));
            $wpdb->insert( $user_access_type_table, array( 'id' => 14, 'name' => 'Developer/Assignee Access - For Assigned'));
            $wpdb->insert( $user_access_type_table, array( 'id' => 15, 'name' => 'Read Only - For All '));
            $wpdb->insert( $user_access_type_table, array( 'id' => 16, 'name' => 'Create, Read and Write - For Assigned'));
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$project_table}'" ) != $project_table ){
            dbDelta( $projects );

            // Insert Project Data
            $wpdb->insert( $project_table, array( 'id' => 1, 'name' => 'W3BD', 'description' => 'W3BD is a web development company. So all design and development process will go as a development website.', 'project_category_id' =>17, 'project_status_id' => 2,
                'live_url' => 'http://w3bd.com', 'test_url' => 'http://dev.w3bd.com', 'design_date' => '2017-04-01', 'development_date' => '2017-04-06', 'test_date' => '2017-04-16', 'go_live_date' => '2017-04-30', 'created_at' => '2017-04-18 19:21:33', 'updated_at' => '2017-04-18 19:21:33',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco 
			laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse', 'owner_id' => 1) );

            $wpdb->insert( $project_table, array( 'id' => 2, 'name' => 'Data Entry', 'description' => 'This is data entry based project.', 'project_category_id' =>18, 'project_status_id' => 2,
                'go_live_date' => '2017-04-28', 'created_at' => '2017-04-18 19:22:45', 'updated_at' => '2017-04-18 19:22:45',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco 
			laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse', 'owner_id' => 1) );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$project_status_table}'" ) != $project_status_table ){
            dbDelta( $project_status );

            // Import Project Status Data
            $wpdb->insert( $project_status_table, array( 'id' => 2, 'name' => 'Open'));
            $wpdb->insert( $project_status_table, array( 'id' => 3, 'name' => 'On Hold'));
            $wpdb->insert( $project_status_table, array( 'id' => 4, 'name' => 'Closed'));
            $wpdb->insert( $project_status_table, array( 'id' => 5, 'name' => 'Cancelled'));
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$project_category_table}'" ) != $project_category_table ){
            dbDelta( $project_category );

            // Import Project Category/Type Data
            $wpdb->insert( $project_category_table, array( 'id' => 16, 'name' => 'Support', 'slug' => 'support'));
            $wpdb->insert( $project_category_table, array( 'id' => 17, 'name' => 'New Site', 'slug' => 'new-site'));
            $wpdb->insert( $project_category_table, array( 'id' => 18, 'name' => 'Internal', 'slug' => 'internal'));
            $wpdb->insert( $project_category_table, array( 'id' => 19, 'name' => 'Business', 'slug' => 'business'));
            $wpdb->insert( $project_category_table, array( 'id' => 20, 'name' => 'Wordpress Site', 'slug' => 'wordpress-site'));
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$project_groups_table}'" ) != $project_groups_table ){
            dbDelta( $project_groups );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$project_users_table}'" ) != $project_users_table ){
            dbDelta( $project_users );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$task_table}'" ) != $task_table ){
            dbDelta( $tasks );

            // Import Task Data
            $wpdb->insert( $task_table, array( 'id' => 1, 'project_id' => 1, 'task_type_id' => 11, 'name' =>'Scrapping Data', 'task_status_id' => 7,
                'task_priority_id' => 7, 'task_label_id' => 7, 'amount' => 3, 'description' => 'Go to the website medium.com.   And list up top - popular
			10 article link in the excel sheet.', 'esitmate_time' => '5 Days', 'start_date' => '2017-04-01', 'due_date' => '2017-04-30',
                'progress' => '5', 'created_at' => '2017-04-18 19:27:33', 'updated_at' => '2017-04-18 19:27:33', 'created_by' => 1, 'last_update_by' => 1) );

            $wpdb->insert( $task_table, array( 'id' => 2, 'project_id' => 1, 'task_type_id' => 11, 'name' =>'Estimation Cost', 'task_status_id' => 7,
                'task_priority_id' => 8, 'task_label_id' => 10, 'amount' => 2, 'description' => 'Go to w3bd.com. Visit the site all over and Estimate the design
			and development cost for this site.', 'esitmate_time' => '3 Hours', 'due_date' => '2017-04-19', 'last_update_by' => 1,
                'progress' => '57', 'created_at' => '2017-04-18 19:31:19', 'updated_at' => '2017-04-18 19:31:19', 'created_by' => 1) );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$task_status_table}'" ) != $task_status_table ){
            dbDelta( $task_status );

            // Import Task Status
            $wpdb->insert( $task_status_table, array( 'id' => 1, 'name' => 'Re-opened', 'group_name' => 'open'));
            $wpdb->insert( $task_status_table, array( 'id' => 2, 'name' => 'Done', 'group_name' => 'done'));
            $wpdb->insert( $task_status_table, array( 'id' => 6, 'name' => 'Waiting Assessment', 'group_name' => 'open'));
            $wpdb->insert( $task_status_table, array( 'id' => 7, 'name' => 'Open', 'group_name' => 'open'));
            $wpdb->insert( $task_status_table, array( 'id' => 8, 'name' => 'Completed', 'group_name' => 'close'));
            $wpdb->insert( $task_status_table, array( 'id' => 9, 'name' => 'Paid', 'group_name' => 'close'));
            $wpdb->insert( $task_status_table, array( 'id' => 10, 'name' => 'Suspended', 'group_name' => 'close'));
            $wpdb->insert( $task_status_table, array( 'id' => 11, 'name' => 'Lost', 'group_name' => 'close'));
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$task_type_table}'" ) != $task_type_table ){
            dbDelta( $task_type );

            // Import Task Type
            $wpdb->insert( $task_type_table, array( 'id' => 9, 'name' => 'Change Priority Rate (Hourly rate $25.00)', 'slug' => 'change-priority-rate-(hourly-rate-$25.00)'));
            $wpdb->insert( $task_type_table, array( 'id' => 10, 'name' => 'Changes (Hourly rate $15.00)', 'slug' => 'changes-(hourly-rate-$15.00)'));
            $wpdb->insert( $task_type_table, array( 'id' => 11, 'name' => 'Defects (Hourly rate $0.00)', 'slug' => 'defects-(hourly-rate-$0.00)'));
            $wpdb->insert( $task_type_table, array( 'id' => 13, 'name' => 'Development New (Hourly rate $25.00)', 'slug' => 'development-new-(hourly-rate-$25.00)-'));
            $wpdb->insert( $task_type_table, array( 'id' => 14, 'name' => 'Design New Fixed Task', 'slug' => 'design-new-fixed-task'));
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$task_label_table}'" ) != $task_label_table ){
            dbDelta( $task_label );

            // Import Task Label Data
            $wpdb->insert( $task_label_table, array( 'id' => 5, 'name' => 'Change'));
            $wpdb->insert( $task_label_table, array( 'id' => 6, 'name' => 'Plugin'));
            $wpdb->insert( $task_label_table, array( 'id' => 7, 'name' => 'Task'));
            $wpdb->insert( $task_label_table, array( 'id' => 8, 'name' => 'Bug'));
            $wpdb->insert( $task_label_table, array( 'id' => 9, 'name' => 'Idea'));
            $wpdb->insert( $task_label_table, array( 'id' => 10, 'name' => 'Quote'));
            $wpdb->insert( $task_label_table, array( 'id' => 11, 'name' => 'Issue'));
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$task_priority_table}'" ) != $task_priority_table ){
            dbDelta( $task_priority );

            // Import Task Priority Data
            $wpdb->insert( $task_priority_table, array( 'id' => 4, 'name' => 'Unknown', 'icon' => 'icon_3.png'));
            $wpdb->insert( $task_priority_table, array( 'id' => 5, 'name' => 'Low', 'icon' => 'icon_10.png'));
            $wpdb->insert( $task_priority_table, array( 'id' => 6, 'name' => 'Medium', 'icon' => 'icon_12.png'));
            $wpdb->insert( $task_priority_table, array( 'id' => 7, 'name' => 'High', 'icon' => 'icon_9.png'));
            $wpdb->insert( $task_priority_table, array( 'id' => 8, 'name' => 'Urgent', 'icon' => 'icon_5.png'));
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$task_comment_table}'" ) != $task_comment_table ){
            dbDelta( $task_comment );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$task_groups_table}'" ) != $task_groups_table ){
            dbDelta( $task_groups );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$task_users_table}'" ) != $task_users_table ){
            dbDelta( $task_users );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$ticket_table}'" ) != $ticket_table ){
            dbDelta( $tickets );
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$ticket_type_table}'" ) != $ticket_type_table ){
            dbDelta( $ticket_type );
	        $wpdb->insert( $ticket_type_table, array( 'id' => 1, 'name' => 'Service'));
	        $wpdb->insert( $ticket_type_table, array( 'id' => 2, 'name' => 'Sales'));
	        $wpdb->insert( $ticket_type_table, array( 'id' => 3, 'name' => 'Support'));
	        $wpdb->insert( $ticket_type_table, array( 'id' => 4, 'name' => 'Business'));
        }


	}
	

}
