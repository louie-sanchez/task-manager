<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://netwarriorservices.com
 * @since      1.0.0
 *
 * @package    Task_Manager
 * @subpackage Task_Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Task_Manager
 * @subpackage Task_Manager/public
 * @author     Louie Sanchez <netwarriorservices@gmail.com>
 */
class Task_Manager_Public {

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
	 * @param      string    $task_manager       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $task_manager, $version ) {

		$this->task_manager = $task_manager;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Task_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Task_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'tmp-public-custom-style', plugin_dir_url( __FILE__ ) . 'css/task-manager-public.css', array(), $this->version, 'all' );

        $tmp_custom_css = get_option('tmp_custom_css');
        wp_add_inline_style( 'tmp-public-custom-style', $tmp_custom_css );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Task_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Task_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'tmp_public_script', plugin_dir_url( __FILE__ ) . 'js/task-manager-public.js', array( 'jquery' ), $this->version, false );

        wp_localize_script( 'tmp_public_script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'user_id' => get_current_user_id() ) );

	}


	public function tmp_shortcodes( $args ){

		require_once plugin_dir_path( __FILE__ ) . '../admin/partials/user-based-custom-query-class.php';
		$output = '';

//		print_r($args);
		if(isset($args['task'])){
			if ( in_array( User_Based_Custom_Query::getUserAccessId()['task'] , User_Based_Custom_Query::getAllAccessIds()) ){
				if(!empty($args['task'])){ $limit = $args['task']; }else{ $limit = 5; }
				$userTasks = User_Based_Custom_Query::getCurrentUserTaskNames( $limit );


				$output = '<div class="tmp_task_list">';
				$output = $output.'<ul>';
				foreach ( $userTasks as $task ){
					$output = $output.'<li><a href="'.admin_url("admin.php?page=task-details&task=").$task->id.'">'.$task->name.'</a></li>';
				}
				$output = $output.'</ul>';
				$output = $output.'</div>';
			}
		}elseif(isset($args['project'])){
			if ( in_array( User_Based_Custom_Query::getUserAccessId()['project'] , User_Based_Custom_Query::getAllAccessIds()) ){
				if(!empty($args['project'])){ $limit = $args['project']; }else{ $limit = 5; }
				$userProjects = User_Based_Custom_Query::getCurrentUserProjectNames( $limit );
				$output = '<div class="tmp_project_list">';
				$output = $output.'<ul>';
				foreach ( $userProjects as $project ){
					$output = $output.'<li><a href="'.admin_url("admin.php?page=task-details&task=").$project->id.'">'.$project->name.'</a></li>';
				}
				$output = $output.'</ul>';
				$output = $output.'</div>';
			}
		}elseif(isset($args['task-table-view'])){
			if(!empty($args['task-table-view'])){ $limit = $args['task-table-view']; }else{ $limit = 10; }
			$allTasks = User_Based_Custom_Query::getAllTaskList( $limit );
			$icon_dir_url =  plugins_url( '../admin/images/icons/', __FILE__ );
			$output = '<div class="tmp_task_list tmp"> ';
			$output = $output.'<table style="width:100%">
			<tr>
				<th>Task Name</th>
			    <th>Project Name</th> 
			    <th>Task Status</th>
			    <th>Task Priority</th>
			    <th>Progress</th>
			    <th>Due Date</th>
			    <th>Task Type</th>
			 </tr>';
			foreach ( $allTasks as $task ){
				$output = $output.' 
			<tr>
    			<td><a href="'.admin_url("admin.php?page=task-details&task=").$task->id.'">'.$task->name.'</a></td>
    			<td><a href="'.admin_url("admin.php?page=project-details&project=").$task->project_id.'">'.$task->project_name.'</a></td>
    			<td>'.$task->task_status.'</td>
    			<td><img src="'.$icon_dir_url. $task->task_priority_icon .'" alt=""/> '.$task->task_priority.'</td>
    			<td>'.sprintf('<span class="progress-bar-parcent-cover"><span class="progress-bar-percent" style="width:%s&#37"></span></span>%s&#37;',$task->progress,$task->progress).'</td>
    			<td>'.$task->due_date.'</td>
    			<td>'.$task->task_label.'</td>
  			</tr>';
//				$output = $output.'<li><a href="'.admin_url("admin.php?page=task-details&task=").$task->id.'">'.$task->name.'</a></li>';
			}

$output = $output.'</table>';
			$output = $output.'</div>';
		}

		return $output;

	}


	public function tmp_new_ticket_shortcode(){
		if ( is_user_logged_in() ){
			global $wpdb;
			// Get Project Category
			$ticket_types = $wpdb->get_results("SELECT tt.id, tt.name FROM {$wpdb->prefix}tmp_ticket_types as tt");
			// Get Project Status
			$project_status = $wpdb->get_results("SELECT ps.id, ps.name FROM {$wpdb->prefix}tmp_project_status as ps");


			$output = '';
			$output .= '<div class="tmp new_ticket_p" id="newTicket">
							<input type="hidden" name="act" value="add_ticket">
        					<div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
                				<div class="form-input">
                    				<h4 class="subject_title">Subject</h4>
                    				<div class="input ticket_subject">
                        				<input type="text" id="ticketSubject" name="name" size="30" value="" spellcheck="true" autocomplete="off">
                    				</div>
                    				<p class="hint">Ticket Subject - to define us a little brief of ticket you are going to submit.</p>
                				</div>
        					</div>
        					<div class="clr"></div>
        					<div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            					
                					<div class="form-select">
                    					<h4 class="type_title">Type</h4>
                    					<div class="select">	
                        					<select id="ticketTypeId" data-custom="" name="ticket_type_id" class="ticket_type">';
                             foreach ($ticket_types as $type):
	                             $output .='<option value="'.$type->id.'">' .$type->name;
                                $output .='</option>';
                             endforeach;
			$output .='</select>
                    </div>
                    <p class="hint">Type of the ticket.</p>
                </div>
            
            
        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
                <div class="details">
                    <h4 class="details_title">Details</h4>';
					$output .='<textarea id="ticketDetails" class="details" name="details" rows="5"></textarea>
                    <p class="hint">Describe your ticket in details here.</p>
				</div>
        </div>

        

        <div class="p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="form-input">
                <div id="submit-action">
                    <span class="spinner"></span>
                    <input type="button" name="publish" id="submitTicket" class="button button-primary button-large submit_button"
                           value="Create">
                </div>
            </div>
        </div>
   		</div>';
			return $output;
		}else{
            $output = '<div class="tmp" id="newTicket"><br><a href="'.wp_login_url().'" title="Login">Login</a> to submit new ticket.</div>';
			return $output;
		}
	}

}
