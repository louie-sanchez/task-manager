<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://netwarriorservices.com
 * @since      1.0.0
 *
 * @package    Task_Manager
 * @subpackage Task_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Task_Manager
 * @subpackage Task_Manager/admin
 * @author     Louie Sanchez <netwarriorservices@gmail.com>
 */
class Task_Manager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $task_manager    The ID of this plugin.
	 */
	private $task_manager;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $task_manager       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $task_manager, $version ) {

		$this->task_manager = $task_manager;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in task_manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The task_manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'tmp-admin-custom-style', plugin_dir_url( __FILE__ ) . 'css/task-manager-admin.css', array(), $this->version, 'all' );
//		wp_enqueue_style( 'date-picker-ui-min', plugin_dir_url( __FILE__ ) . 'css/date-picker-ui.min.css', array(), $this->version, 'all' );
//		wp_enqueue_style( 'min-grid-css', plugin_dir_url( __FILE__ ) . 'css/grid-min.css', array(), $this->version, 'all' );

        $tmp_custom_css = get_option('tmp_custom_css');
        wp_add_inline_style( 'tmp-admin-custom-style', $tmp_custom_css );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in task_manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The task_manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->task_manager, plugin_dir_url( __FILE__ ) . 'js/task-manager-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->version, false );



	}

	public function task_manager_tasks_page()
	{

		if ( current_user_can('contributor') && !current_user_can('upload_files') )
			add_action('admin_init', 'allow_contributor_uploads');
		function allow_contributor_uploads() {
			$contributor = get_role('contributor');
			$contributor->add_cap('upload_files');
		}

		/*---------------------------------------------------- Check Users For Menu ---------------------------------*/
		global $wpdb;
		$currentUserId = wp_get_current_user()->ID;

		require_once plugin_dir_path(__FILE__) . 'partials/user-based-custom-query-class.php';
		$tp_access = User_Based_Custom_Query::getUserAccessId();


		// Get All AccessIDS


		/*------------------------------------------ Project Menus ---------------------------------------------------------*/
        $userAccessIds = User_Based_Custom_Query::getAllAccessIds();
		// All User Access
		if ( in_array( $tp_access['project'] , $userAccessIds) ) {
			add_menu_page(__( 'Projects', 'task-manager' ), __( 'Projects', 'task-manager' ), 'read', 'projects', 'projects_file_func', 'dashicons-image-filter', 20);
			add_submenu_page( null, __( 'Project Details', 'task-manager' ), __( 'Project Details', 'task-manager' ), 'read', 'project-details', 'project_details_file_function');
		}

		// Editor Access
		if ( in_array($tp_access['project'], array(12,13,16))) {
			add_submenu_page('projects', __( 'Add New', 'task-manager' ), __( 'Add New', 'task-manager' ), 'read', 'project-new', 'new_project_file_function');
		}

		// Only Admin Access
		if ( in_array($tp_access['project'], array(13,16)) ){
			add_submenu_page( 'projects', __( 'Categories', 'task-manager' ), __( 'Categories', 'task-manager' ), 'read', 'project-categories', 'project_category_file_function');
			add_submenu_page( 'projects', __( 'Project Status', 'task-manager' ), __( 'Project Status', 'task-manager' ), 'read', 'project-status', 'project_status_file_function');
		}

		// Moderator Access
		if ( in_array($tp_access['project'], array(11, 12, 13, 14, 16))) {
			add_submenu_page(null, __( 'Edit Project', 'task-manager' ), __( 'Edit Project', 'task-manager' ), 'read', 'project-edit', 'edit_project_file_function');
		}


		/*------------------------------------------ Task Menus ---------------------------------------------------------*/
		// All User Access
		if ( in_array( $tp_access['task'] , $userAccessIds) ) {
			add_menu_page( __( 'Tasks', 'task-manager' ), __( 'Tasks', 'task-manager' ), 'read', 'tasks', 'all_task_file_function', 'dashicons-editor-paste-text', 20);
			add_submenu_page( null, __( 'Task Details', 'task-manager' ), __( 'Task Details', 'task-manager' ), 'read', 'task-details', 'task_details_file_function');
		}

		// Editor Access
		if ( in_array($tp_access['task'], array(12,13,16))) {
			add_submenu_page('tasks', __( 'Add New', 'task-manager' ), __( 'Add New', 'task-manager' ), 'read', 'task-new', 'new_task_file_function');
		}

		// Only Admin Access
		if ( in_array($tp_access['task'], array(13,16)) ) {
			add_submenu_page('tasks', __( 'Task Types', 'task-manager' ), __( 'Task Types', 'task-manager' ), 'read', 'task-types', 'task_type_file_function');
			add_submenu_page('tasks', __( 'Task Status', 'task-manager' ), __( 'Task Status', 'task-manager' ), 'read', 'task-status', 'task_status_file_function');
			add_submenu_page('tasks', __( 'Task Priorities', 'task-manager' ), __( 'Task Priorities', 'task-manager' ), 'read', 'task-priorities', 'task_priority_file_function');
			add_submenu_page('tasks', __( 'Task Labels/Tags', 'task-manager' ), __( 'Task Labels/Tags', 'task-manager' ), 'read', 'task-labels', 'task_label_file_function');
		}

		// Moderator Access
		if ( in_array($tp_access['task'], array(11,12,13,14,16)) ) {
			add_submenu_page(null, __( 'Edit Task', 'task-manager' ),  __( 'Edit Task', 'task-manager' ), 'read', 'task-edit', 'edit_task_file_function');
		}



		/*------------------------------------- Manage Users Menu --------------------------------------------------------*/
		// Users Menu
		if ( in_array($tp_access['project'], array(13,16)) && in_array($tp_access['task'], array(13,16)) ) {
			add_menu_page( __( 'Manage Users', 'task-manager' ),  __( 'Manage Users', 'task-manager' ), 'read', 'manage-users', 'manage_users_file_function', 'dashicons-businessman', 22);
			add_submenu_page('manage-users', __( 'User Groups', 'task-manager' ), __( 'User Groups', 'task-manager' ), 'read', 'user-groups', 'user_groups_file_function');
			add_submenu_page('manage-users', __( 'User Access Types', 'task-manager' ), __( 'User Access Types', 'task-manager' ), 'read', 'user-access-type', 'access_type_file_function');
		}

		/*------------------------------------- Tickets Menu -----------------------------------------------------------*/
		// Moderator Access
		if ( in_array($tp_access['project'], array(11,12,13,16)) ) {
			add_menu_page( __( 'Tickets', 'task-manager' ), __( 'Tickets', 'task-manager' ), 'read', 'tickets', 'tickets_file_func', 'dashicons-sos', 21);
			add_submenu_page('tickets', __( 'Add New Ticket', 'task-manager' ), __( 'Add New Ticket', 'task-manager' ), 'read', 'ticket-new', 'new_ticket_file_function');
			add_submenu_page(null, __( 'Ticket Details', 'task-manager' ), __( 'Ticket Details', 'task-manager' ), 'read', 'ticket-details', 'ticket_details_file_function');
			add_submenu_page(null, __( 'Edit Ticket', 'task-manager' ), __( 'Edit Ticket', 'task-manager' ), 'read', 'ticket-edit', 'edit_ticket_file_function');
		}

		// Editor Access
		if ( in_array($tp_access['project'], array(13,16))) {
			add_submenu_page('tickets', __( 'Ticket Types', 'task-manager' ), __( 'Ticket Types', 'task-manager' ), 'read', 'ticket-types', 'ticket_types_file_function');
		}

		// Task/Project Manager Option Page
//        if ( $tp_access['task'] == 13 ) {
//		    add_options_page(__( 'Task Manager Pro', 'task_manager' ), __( 'Task Manager Pro', 'task_manager' ), 'read','task-manager', 'task_manager_setting_func');
//        }

		function access_type_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/user-access-type.php';
		}

		function user_groups_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/user-groups.php';
		}

		function manage_users_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/manage-users.php';
		}

		function projects_file_func(){
			include plugin_dir_path( __FILE__ ) . 'pages/projects.php';
		}

		function new_project_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/project-new.php';
		}

		function project_category_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/project-category.php';
		}

		function project_status_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/project-status.php';
		}

		function edit_project_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/project-edit.php';
		}

		function project_details_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/project-details.php';
		}

		function all_task_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/tasks.php';
		}

		function new_task_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/task-new.php';
		}

		function task_type_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/task-types.php';
		}

		function task_status_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/task-status.php';
		}

		function task_priority_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/task-priority.php';
		}

		function task_label_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/task-labels.php';
		}

		function edit_task_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/task-edit.php';
		}

		function task_details_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/task-details.php';
		}


		function task_manager_setting_func(){
			include plugin_dir_path( __FILE__ ) . 'pages/setting.php';
		}

		function tickets_file_func(){
			include plugin_dir_path( __FILE__ ) . 'pages/tickets.php';
		}

		function new_ticket_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/ticket-new.php';
		}

		function ticket_types_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/ticket-types.php';
		}

		function ticket_details_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/ticket-details.php';
		}

		function edit_ticket_file_function(){
			include plugin_dir_path( __FILE__ ) . 'pages/ticket-edit.php';
		}



	}

	public function tmp_widgets(){
		require_once plugin_dir_path(__FILE__) . 'widget/task-manager-widget.php';
		register_widget( 'tmp_widgets' );
	}

	public function tmp_dashboard_widgets(){
		global $wp_meta_boxes;
		wp_add_dashboard_widget('tmp_task_widget', 'Your Assigned Tasks And Projects', 'tmp_dashboard_widget_file_func');

		function tmp_dashboard_widget_file_func() {
			include plugin_dir_path( __FILE__ ) . 'widget/dashboard_into_page_tab.php';
		}

		// Add At a Glance Task Items
		add_filter( 'dashboard_glance_items', 'tmp_glance_items', 10, 1 );
		function tmp_glance_items( $items = array() ) {

			$items[] = sprintf( '<a href="admin.php?page=tasks" title="Task">%s Tasks</a>', User_Based_Custom_Query::getCurrentUserTaskCount());
			$items[] = sprintf( '<a href="admin.php?page=projects" title="Project">%s Project</a>', User_Based_Custom_Query::getCurrentUserProjectCount());
			return $items;
		}
	}

	public function tmp_modify_admin_menu_badge(){
		global $menu;
		$get_project_count_badge = get_option('tmp_project_count_badge');
		$get_task_count_badge = get_option('tmp_task_count_badge');
		include plugin_dir_path( __FILE__ ) . 'partials/ProjectsPageCustomQuery.php';
		$allCounts = ProjectsPageCustomQuery::getProjectsTotalCount();
		$project_label = sprintf( __( 'Projects %s' ), "<span class='update-plugins count' title='Total Project'><span class='update-count'>".$allCounts['total_project']."</span></span>" );
		$task_label = sprintf( __( 'Tasks %s' ), "<span class='update-plugins count' title='Total Task'><span class='update-count'>".$allCounts['total_task']."</span></span>" );
		if( $get_project_count_badge == 'yes' ){
			$menu['20.6844'][0] = $project_label;
		}

		if( $get_task_count_badge == 'yes' ){
			$menu['20.22868'][0] = $task_label;
		}

	}

	public function export_task_csv(){
		ini_set('display_errors', false);
		error_reporting(0);
		//Prepare query to get selected column
			global $wpdb;
			$getTable = $wpdb->prefix . 'tmp_tasks';
			if (ob_get_contents())
				ob_clean();
			$field = '';
			$getField = '';
			$query = "SELECT tsk.id, tsk.name, tsk.amount, tsk.description, tts.name as task_status, tp.name as project_name,ttt.name as task_type, ttp.name as task_priority, 
            ttl.name task_label, tsk.progress, tsk.start_date, tsk.due_date, tsk.created_at, tsk.updated_at
            FROM {$wpdb->prefix}tmp_tasks tsk
            LEFT JOIN {$wpdb->prefix}tmp_projects tp ON tsk.project_id = tp.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_status tts ON tsk.task_status_id = tts.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_types ttt ON tsk.task_type_id = ttt.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_priorities ttp ON tsk.task_priority_id = ttp.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_labels ttl ON tsk.task_label_id = ttl.id";
			$results = $wpdb->get_results($wpdb->prepare($query, NULL));
			//$wpdb->print_error();
			//echo "<pre>"; print_r($results); echo "</pre>"; die; //just to see everything
			//Set csv file name
			$output_filename = $getTable . '_' . date('Ymd_His') . '.csv'; # CSV FILE NAME WILL BE table_name_yyyymmdd_hhmmss.csv
			$output_handle = @fopen('php://output', 'w');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename=' . $output_filename);
			header('Expires: 0');
			header('Pragma: public');
			// Insert header row
			fputcsv($output_handle, $csv_fields);
			//Parse results to csv format
			$first = true;
			// Parse results to csv format
			foreach ($results as $row) {
				// Add table headers
				if ($first) {
					$titles = array();
					foreach ($row as $key => $val) {
						$titles[] = $key;
					}
					fputcsv($output_handle, $titles);
					$first = false;
				}

				$leadArray = (array) $row; // Cast the Object to an array
				// Add row to file
				fputcsv($output_handle, $leadArray);
			}
			//Flush DB cache and die process after actions
			echo $wpdb->flush();
			die();
	}

	public function import_task_csv(){
		ini_set('display_errors', false);
		error_reporting(0);
		if ( ! defined( 'IS_IU_CSV_DELIMITER' ) )
			define ( 'IS_IU_CSV_DELIMITER', ',' );

		$errors = [];

		global $wpdb;
		if(isset($_POST['hidden_csv'])){
			$extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
			// If file extension is 'csv'
			if(!empty($_FILES['upload']['name']) && $extension == 'csv'){

				$totalInserted = 0;

				// Open file in read mode
				$csvFile = fopen($_FILES['upload']['tmp_name'], 'r');
				$task_fields = ['name', 'amount', 'description', 'task_status', 'project_name', 'task_type', 'task_priority',
                    'task_label', 'progress', 'start_date', 'due_date', 'created_at', 'updated_at'];

				require plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-readcsv.php';
				$file_handle = @fopen( $_FILES['upload']['tmp_name'], 'r' );
				if($file_handle) {
					$csv_reader = new ReadCSV( $file_handle, IS_IU_CSV_DELIMITER, "\xEF\xBB\xBF" ); // Skip any UTF-8 byte order mark.

					$first = true;
					$totalImport = 0;


					require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/user-based-custom-query-class.php';
					$userBasedCustomQuery = new User_Based_Custom_Query();


					while ( ( $line = $csv_reader->get_row() ) !== NULL ) {
						if ( empty( $line ) ) {
							if ( $first )
								break;
							else
								continue;
						}


						if ( $first ) {
							$headers = $line;
							$first = false;
							continue;
						}

						$taskdata = array();
						foreach ( $line as $ckey => $column ) {
							$column_name = $headers[$ckey];
							if(!empty($column_name)){
								$column_name = strtolower(preg_replace('/\s+/', '_', $column_name));
                            }

							$column = trim( $column );

							if ( in_array( $column_name, $task_fields ) ) {
								$taskdata[$column_name] = $column;
							}
						}


						if ( empty( $taskdata ) )
						    continue;


						$task = $wpdb->get_results("SELECT tsk.id FROM {$wpdb->prefix}tmp_tasks tsk WHERE tsk.name='{$taskdata['name']}'", ARRAY_A);
						if(empty($task)){
							$projectQuery = $wpdb->get_results("SELECT p.id FROM {$wpdb->prefix}tmp_projects p WHERE p.name = '{$taskdata['project_name']}'", ARRAY_A);
							$project_id = null;
							if(empty($projectQuery)){
								$projectInsertQuery = $userBasedCustomQuery->insertProjectData( $taskdata['project_name'], null, 2,
									null, null, null, null, null, $taskdata['project_name'] );
								$project_id = $projectInsertQuery['id'];
							}elseif(isset($projectQuery[0]['id'])){
								$project_id = $projectQuery[0]['id'];
							}

							$taskTypeQuery = $wpdb->get_results("SELECT tp.id FROM {$wpdb->prefix}tmp_task_types tp WHERE tp.name = '{$taskdata['task_type']}'", ARRAY_A);
							$task_type_id = null;
							if(empty($taskTypeQuery)){
								$slug = preg_replace('/\s+/', '-', $taskdata['task_type']);
								$wpdb->insert($wpdb->prefix . "tmp_task_types", array('name' => $taskdata['task_type'], 'slug' => $slug));
								$task_type_id = $wpdb->insert_id;
                            }elseif(isset($taskTypeQuery[0]['id'])){
								$task_type_id = $taskTypeQuery[0]['id'];
							}

							$taskStatusQuery = $wpdb->get_results("SELECT ts.id FROM {$wpdb->prefix}tmp_task_status ts WHERE ts.name = '{$taskdata['task_status']}'", ARRAY_A);
							$task_status_id = null;
							if(empty($taskStatusQuery)){
								$wpdb->insert($wpdb->prefix . "tmp_task_status", array('name' => $taskdata['task_status'], 'group_name' => 'open'));
								$task_status_id = $wpdb->insert_id;
							}elseif(isset($taskStatusQuery[0]['id'])){
								$task_status_id = $taskStatusQuery[0]['id'];
							}

							$taskStatusQuery = $wpdb->get_results("SELECT ts.id FROM {$wpdb->prefix}tmp_task_priorities ts WHERE ts.name = '{$taskdata['task_priority']}'", ARRAY_A);
							$task_priority_id = null;
							if(empty($taskStatusQuery)){
								$wpdb->insert($wpdb->prefix . "tmp_task_priorities", array('name' => $taskdata['task_priority'], 'icon' => 'icon_3.png'));
								$task_priority_id = $wpdb->insert_id;
							}elseif(isset($taskStatusQuery[0]['id'])){
								$task_priority_id = $taskStatusQuery[0]['id'];
							}

							$taskStatusQuery = $wpdb->get_results("SELECT tl.id FROM {$wpdb->prefix}tmp_task_labels tl WHERE tl.name = '{$taskdata['task_label']}'", ARRAY_A);
							$task_label_id = null;
							if(empty($taskStatusQuery)){
								$wpdb->insert($wpdb->prefix . "tmp_task_labels", array('name' => $taskdata['task_label']));
								$task_label_id = $wpdb->insert_id;
							}elseif(isset($taskStatusQuery[0]['id'])){
								$task_label_id = $taskStatusQuery[0]['id'];
							}

							$taskDetails = stripslashes($taskdata['description']);

							$userGroup = array();
							$taskQuery = $userBasedCustomQuery->insertTaskData($taskdata['name'], $project_id, $task_type_id, $task_label_id,
								$task_status_id, $task_priority_id, $taskdata['estimation_time'], $taskdata['start_date'], $taskdata['due_date'],
								$taskdata['task_progress'], $taskDetails, null, $userGroup, $taskdata['amount']);
							if(isset($taskQuery['id'])){
								$totalImport+= 1;
                            }
						}
                    }

					add_action( 'admin_notices', function () use ($totalImport){ ?>
                        <div class="updated notice">
                            <p><?php _e( $totalImport. ' tasks are imported from CSV!', 'task-manager' ); ?></p>
                        </div>
					<?php });

					fclose( $file_handle );
				}else{
					$errors[] = new WP_Error('file_read', 'Unable to open CSV file.');
                }
			}else{
				add_action( 'admin_notices', function () use ($error){ ?>
                    <div class="updated notice">
                        <p><?php _e( "<h3 style='color: red;'>Invalid Extension</h3>", 'task-manager' ); ?></p>
                    </div>
				<?php });
			}
        }

		echo $wpdb->flush();
		wp_redirect( 'admin.php?page=tasks' );
		die();
    }

	public function export_ticket_csv(){
		ini_set('display_errors', false);
		error_reporting(0);
		//Prepare query to get selected column
		global $wpdb;
		$getTable = $wpdb->prefix . 'tmp_tickets';
		if (ob_get_contents())
			ob_clean();
		$field = '';
		$getField = '';
		$query = "SELECT tt.id as ticketID, tt.name Name,
            usr.display_name as TicketFor, tt.updated_at as UpdatedAt,
            ttt.name as Type, ps.name as Status
            FROM {$wpdb->prefix}tmp_tickets tt
            LEFT JOIN {$wpdb->prefix}tmp_ticket_types ttt ON tt.ticket_type_id = ttt.id 
            LEFT JOIN {$wpdb->prefix}tmp_project_status ps ON tt.ticket_status_id = ps.id 
            LEFT JOIN {$wpdb->prefix}users usr ON tt.ticket_for = usr.id";
		$results = $wpdb->get_results($wpdb->prepare($query, NULL));
		//$wpdb->print_error();
		//echo "<pre>"; print_r($results); echo "</pre>"; die; //just to see everything
		//Set csv file name
		$output_filename = $getTable . '_' . date('Ymd_His') . '.csv'; # CSV FILE NAME WILL BE table_name_yyyymmdd_hhmmss.csv
		$output_handle = @fopen('php://output', 'w');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename=' . $output_filename);
		header('Expires: 0');
		header('Pragma: public');
		// Insert header row
		fputcsv($output_handle, $csv_fields);
		//Parse results to csv format
		$first = true;
		// Parse results to csv format
		foreach ($results as $row) {
			// Add table headers
			if ($first) {
				$titles = array();
				foreach ($row as $key => $val) {
					$titles[] = $key;
				}
				fputcsv($output_handle, $titles);
				$first = false;
			}

			$leadArray = (array) $row; // Cast the Object to an array
			// Add row to file
			fputcsv($output_handle, $leadArray);
		}
		//Flush DB cache and die process after actions
		echo $wpdb->flush();
		die();
	}

	public function wpse8170_phpmailer_init($phpmailer){
        $phpmailer->Host = get_option('tmp_setting_mail_host') ? get_option('tmp_setting_mail_host') : 'smtp.gmail.com';
        $phpmailer->Port = get_option('tmp_setting_mail_port') ? get_option('tmp_setting_mail_port') : 587; // could be different
        $phpmailer->Username = get_option('tmp_setting_mail_user') ? get_option('tmp_setting_mail_user') : ''; // if required
        $phpmailer->Password = get_option('tmp_setting_mail_password') ? get_option('tmp_setting_mail_password') : ''; // if required
        $phpmailer->SMTPAuth = get_option('tmp_setting_mail_smtp_auth') == 'yes' ? true : false; // if required
        $phpmailer->SMTPSecure = 'tls';
        $phpmailer->From = get_option('tmp_setting_mail_user') ? get_option('tmp_setting_mail_user') : 'user.known@gmail.com';
        $phpmailer->FromName = 'Sabbir Khan Hossain';
        $phpmailer->IsSMTP();
    }

    public function tmp_top_nav_menu(){
        register_nav_menus(array(
            'task-new' => __('Task'),
            'project-new' => __('Project'),
        ));
    }

	public function duplicate_project(){
		ini_set('display_errors', true);
		error_reporting(1);

		global $wpdb;
		$project_id =  $_REQUEST['project_id'];
		$project_table = $wpdb->prefix . 'tmp_projects';
		$task_table = $wpdb->prefix . 'tmp_tasks';

		$project_query = "INSERT INTO {$project_table}(`name`, `description`, `project_category_id`, `project_status_id`, 
			`live_url`, `test_url`, `design_date`, `development_date`, `test_date`, `go_live_date`, `owner_id`, 
			`created_at`, `updated_at`) SELECT `name`, `description`, `project_category_id`, `project_status_id`, 
			`live_url`, `test_url`, `design_date`, `development_date`, `test_date`, `go_live_date`, `owner_id`, 
			`created_at`, `updated_at` from {$project_table} WHERE id = {$project_id}";

		$wpdb->get_results($wpdb->prepare($project_query, NULL));
		$latest_project_id = $wpdb->insert_id;

		$task_query = "INSERT INTO {$task_table}(`project_id`, `task_type_id`, `name`, `task_status_id`, 
			`task_priority_id`, `task_label_id`, `description`, `created_by`, `esitmate_time`, `start_date`, 
			`due_date`, `progress`, `last_update_by`, `created_at`, `updated_at`) SELECT 0 as `project_id`, 
			`task_type_id`, `name`, `task_status_id`, `task_priority_id`, `task_label_id`, `description`, 
			`created_by`, `esitmate_time`, `start_date`, `due_date`, `progress`, `last_update_by`, 
			`created_at`, `updated_at` from {$task_table} WHERE project_id = {$project_id}";

		$wpdb->get_results($wpdb->prepare($task_query, NULL));

		$wpdb->get_results($wpdb->prepare("UPDATE {$task_table} SET project_id = $latest_project_id WHERE project_id = 0", NULL));

		add_action( 'admin_notices', function (){ ?>
            <div class="updated notice">
                <p><?php _e( 'The selected project clone done including tasks!', 'task-manager' ); ?></p>
            </div>
        <?php } );

		echo $wpdb->flush();
		wp_redirect( 'admin.php?page=projects' );
		exit;
	}

    public function duplicate_task(){
        ini_set('display_errors', true);
        error_reporting(1);

        global $wpdb;
        $task_id =  $_REQUEST['task_id'];
        $task_table = $wpdb->prefix . 'tmp_tasks';
        $task_group_table = $wpdb->prefix . 'tmp_task_groups';
        $task_user_table = $wpdb->prefix . 'tmp_task_users';

        $task_query = "INSERT INTO {$task_table}(`project_id`, `task_type_id`, `name`, `task_status_id`, 
			`task_priority_id`, `task_label_id`, `description`, `created_by`, `esitmate_time`, `start_date`, `due_date`, 
			`progress`, `last_update_by`, `created_at`, `updated_at`) SELECT `project_id`, `task_type_id`, `name`, 
			`task_status_id`, `task_priority_id`, `task_label_id`, `description`, `created_by`, `esitmate_time`, 
			`start_date`, `due_date`, `progress`, `last_update_by`, `created_at`, `updated_at` from {$task_table} 
            WHERE id = {$task_id}";

        $wpdb->get_results($wpdb->prepare($task_query, NULL));
        $latest_task_id = $wpdb->insert_id;

        $task_group_query = "INSERT INTO {$task_group_table} (`group_id`, `task_id`) 
                             SELECT `group_id`, {$latest_task_id}
                             from {$task_group_table} WHERE task_id = {$task_id}";
        $wpdb->get_results($wpdb->prepare($task_group_query, NULL));

        $task_user_query = "INSERT INTO {$task_user_table} (`user_id`, `task_id`) 
                                     SELECT `user_id`, {$latest_task_id}
                                     from {$task_user_table} WHERE task_id = {$task_id}";
        $wpdb->get_results($wpdb->prepare($task_user_query, NULL));

        function my_update_notice() {
            ?>
            <div class="updated notice">
                <p><?php _e( 'The selected task clone done!', 'task-manager' ); ?></p>
            </div>
            <?php
        }
        add_action( 'admin_notices', 'my_update_notice' );

        echo $wpdb->flush();
        wp_redirect( 'admin.php?page=tasks' );
        exit;
    }

	public function export_project_csv(){
		ini_set('display_errors', false);
		error_reporting(0);
		//Prepare query to get selected column
		global $wpdb;
		$getTable = $wpdb->prefix . 'tmp_tasks';
		if (ob_get_contents())
			ob_clean();
		$field = '';
		$getField = '';
		$query = "SELECT pr.id, pr.name Name,
            pr.live_url as LiveUrl,
            pc.name as Category, ps.name as Status,pr.go_live_date LiveDate, pr.created_at created
            FROM {$wpdb->prefix}tmp_projects pr
            LEFT JOIN {$wpdb->prefix}tmp_project_categories pc ON pr.project_category_id = pc.id 
            LEFT JOIN {$wpdb->prefix}tmp_project_status ps ON pr.project_status_id = ps.id";
		$results = $wpdb->get_results($wpdb->prepare($query, NULL));
		//$wpdb->print_error();
		//echo "<pre>"; print_r($results); echo "</pre>"; die; //just to see everything
		//Set csv file name
		$output_filename = $getTable . '_' . date('Ymd_His') . '.csv'; # CSV FILE NAME WILL BE table_name_yyyymmdd_hhmmss.csv
		$output_handle = @fopen('php://output', 'w');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename=' . $output_filename);
		header('Expires: 0');
		header('Pragma: public');
		// Insert header row
		fputcsv($output_handle, $csv_fields);
		//Parse results to csv format
		$first = true;
		// Parse results to csv format
		foreach ($results as $row) {
			// Add table headers
			if ($first) {
				$titles = array();
				foreach ($row as $key => $val) {
					$titles[] = $key;
				}
				fputcsv($output_handle, $titles);
				$first = false;
			}

			$leadArray = (array) $row; // Cast the Object to an array
			// Add row to file
			fputcsv($output_handle, $leadArray);
		}
		//Flush DB cache and die process after actions
		echo $wpdb->flush();
		die();
	}

	public function add_new_ticket(){
        global $wpdb;

        if($_POST['ticket_data'] && count($_POST['ticket_data'])){
            $ticket_data =  $_POST['ticket_data'];

            $ticket_table = $wpdb->prefix . 'tmp_tickets';
            $wpdb->insert( $ticket_table, $ticket_data);
            $latest_ticket_id = $wpdb->insert_id;
            echo $latest_ticket_id;
        }
        wp_die();
    }


	public function settings_api_init(){
        add_settings_section(
            'task_manager_settings_section',
            __('Notification & Other Settings', 'task-manager'),
            array($this, 'setting_section_callback_function'),
            'task-manager-options'
        );


//        $phpmailer->Host = 'smtp.gmail.com';
//        $phpmailer->Port = 587; // could be different
//        $phpmailer->Username = 'task.manager.pro.w3bd@gmail.com'; // if required
//        $phpmailer->Password = 'www.w3bd.com'; // if required
//        $phpmailer->SMTPAuth = true; // if required
//        $phpmailer->IsSMTP()

        add_settings_field(
            'tmp_project_notification',
	        __('Get Project Notification', 'task-manager'),
            array($this, 'tmp_project_notification_callback_function'),
            'task-manager-options',
            'task_manager_settings_section'
        );

		add_settings_field(
			'tmp_project_count_badge',
			__('Show Project Count Badge On Menu', 'task-manager'),
			array($this, 'tmp_project_count_badge_callback_function'),
			'task-manager-options',
			'task_manager_settings_section'
		);

		add_settings_field(
			'tmp_task_notification',
			__('Get Task Notification', 'task-manager'),
			array($this, 'tmp_task_notification_callback_function'),
			'task-manager-options',
			'task_manager_settings_section'
		);

		add_settings_field(
			'tmp_task_count_badge',
			__('Show Task Count Badge On Menu', 'task-manager'),
			array($this, 'tmp_task_count_badge_callback_function'),
			'task-manager-options',
			'task_manager_settings_section'
		);

		add_settings_field(
			'tmp_multiple_user_assign',
			__('Enable Multiple User Assign Option', 'task-manager'),
			array($this, 'tmp_multiple_user_assign_callback_function'),
			'task-manager-options',
			'task_manager_settings_section'
		);

		add_settings_field(
			'tmp_show_hide_completed_tasks',
			__('Hide completed tasks', 'task-manager'),
			array($this, 'tmp_show_hide_completed_tasks_callback_function'),
			'task-manager-options',
			'task_manager_settings_section'
		);

//        add_settings_field(
//            'tmp_default_project_status',
//            __('Default Project Status', 'task-manager'),
//            array($this, 'tmp_default_project_status_callback_function'),
//            'task-manager-options',
//            'task_manager_settings_section'
//        );

		add_settings_field(
			'tmp_currency_symbol',
			__('Currency Symbol', 'task-manager'),
			array($this, 'tmp_currency_symbol_callback_function'),
			'task-manager-options',
			'task_manager_settings_section'
		);

        add_settings_field(
            'tmp_custom_css',
            __('Custom Css', 'task-manager'),
            array($this, 'tmp_custom_css_callback_function'),
            'task-manager-options',
            'task_manager_settings_section'
        );


		register_setting( 'task-manager-options', 'tmp_project_notification' );
		register_setting( 'task-manager-options', 'tmp_project_count_badge' );
		register_setting( 'task-manager-options', 'tmp_task_notification' );
		register_setting( 'task-manager-options', 'tmp_task_count_badge' );
		register_setting( 'task-manager-options', 'tmp_multiple_user_assign' );
		register_setting( 'task-manager-options', 'tmp_show_hide_completed_tasks' );
		register_setting( 'task-manager-options', 'tmp_default_project_status' );
		register_setting( 'task-manager-options', 'tmp_custom_css' );

		// Mail Settings Save
        register_setting( 'task-manager-options', 'tmp_setting_mail_host' );
        register_setting( 'task-manager-options', 'tmp_setting_mail_port' );
        register_setting( 'task-manager-options', 'tmp_setting_mail_user' );
        register_setting( 'task-manager-options', 'tmp_setting_mail_password' );
        register_setting( 'task-manager-options', 'tmp_setting_mail_smtp_auth' );


    }

	// The basic section
	function setting_section_callback_function(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/setting_init.php';
	}
	function mail_section_callback_function(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/setting_mail_init.php';
	}

	function tmp_setting_mail_host_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/mail_host.php';
	}

	function mail_port_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/mail_port.php';
	}

	function mail_user_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/mail_user.php';
	}

	function mail_password_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/mail_password.php';
	}

	function mail_smtp_auth_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/mail_smtp_auth.php';
	}

	function tmp_project_notification_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/project_notification.php';
	}

	function tmp_project_count_badge_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/project_count_badge.php';
	}

	function tmp_task_notification_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/task_notification.php';
	}

	function tmp_task_count_badge_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/task_count_badge.php';
	}

	function tmp_multiple_user_assign_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/tmp_multiple_user_assign.php';
	}

	function tmp_show_hide_completed_tasks_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/tmp_show_hide_completed_tasks.php';
	}

	function tmp_currency_symbol_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/tmp_currency_symbol.php';
	}

	function tmp_custom_css_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/tmp_custom_css.php';
	}

	function tmp_default_project_status_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/tmp_default_project_status.php';
	}

	function tmp_custom_js_callback_function() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/tmp_custom_js.php';
	}

	public function add_admin_menu() {
		add_options_page( 'Task Manager Pro', __('Task Manager', 'task-manager'), 'manage_options', 'task-manager-options', array($this, 'create_admin_interface'));
	}


    public function create_admin_interface(){

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/setting.php';

    }
	

}
