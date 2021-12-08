<?php

//if (!current_user_can('edit_posts')) {
//    return;
//}


/*--------------------------------------------------
------------ Custom Debug Function ------------------
-----------------------------------------------------*/
function pr($arg)
{
    echo "<pre>";
    print_r($arg);
    echo "</pre>";
    exit;
}

/*---------- End * Custom Debug Function ---------*/

// Import Global WP DB
global $wpdb;
$currentUser = wp_get_current_user()->ID;

$notifyMembers = [];

//
//$tugId = '6';
//
//$ticket_types = $wpdb->get_results("SELECT tug.id
//        FROM {$wpdb->prefix}tmp_user_groups as tug WHERE tug.id = {$tugId};
//         ");
//pr(count($ticket_types));


/*--------------------------------------------------------------------
-------------------- New Task Creation Process ------------------------
---------------------------------------------------------------------*/
if (isset($_POST['act']) && ($_POST['act'] == 'add_task' || $_POST['act'] == 'edit_task') && isset($_POST['task_name']) && !empty($_POST['task_name'])) {
    /*------------------------------------ For Add Task Data -----------------------------------*/
    require_once plugin_dir_path(__FILE__) . '../partials/user-based-custom-query-class.php';
    //Create an instance of our package class...
    $userBasedCustomQuery = new User_Based_Custom_Query();

	$taskDetails = stripslashes($_POST['taskDetails']);
	$userGroup = isset($_POST['user_group']) ? $_POST['user_group'] : array();

    if ($_POST['act'] == 'add_task') {
        $notifyMembers = $userBasedCustomQuery->insertTaskData($_POST['task_name'], $_POST['project_id'],
            $_POST['task_type'], $_POST['task_label'], $_POST['task_status'], $_POST['task_priority'],
            $_POST['estimation_time'], $_POST['start_date'], $_POST['due_date'], $_POST['task_progress'],
            $taskDetails, $_POST['team_members'], $userGroup, $_POST['amount']);
    }


    /**
     * Modify Existing Task Queries
     */
    if ($_POST['act'] == 'edit_task' && isset($_POST['task_id']) && !empty($_POST['task_id'])) {
        $notifyMembers = $userBasedCustomQuery->editTaskData(
                $_POST['task_name'], $_POST['project_id'], $_POST['task_type'], $_POST['task_label'],
                $_POST['task_status'], $_POST['task_priority'], $_POST['estimation_time'], $_POST['start_date'],
                $_POST['due_date'], $_POST['task_progress'], $taskDetails, $_POST['team_members'], $userGroup,
                $_POST['task_id'], $_POST['amount']
        );
    }
}

/*--------------- End ________ New Task Creation ---------------*/

/*---------------------------------------------------------
-------- Execute Query To Delete Existing Task ------------
----------------------------------------------------------*/
if ($_GET['page'] == 'tasks' && isset($_POST['act']) && $_POST['act'] == 'delete' && isset($_POST['task_id']) && !empty($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $task_table = "{$wpdb->prefix}tmp_tasks";
    $task_groups_table = "{$wpdb->prefix}tmp_task_groups";
    $task_users_table = "{$wpdb->prefix}tmp_task_users";

    $wpdb->delete($task_table, array('id' => $task_id));
    $wpdb->delete($task_groups_table, array('task_id' => $task_id));
    $wpdb->delete($task_users_table, array('task_id' => $task_id));
}


/*--------------------------------------------------------
----------------- Task Counting Data ---------------------
---------------------------------------------------------*/
$task_count = $wpdb->get_results("SELECT COUNT(`id`) AS tasks, COUNT(Distinct `project_id`) as projects FROM {$wpdb->prefix}tmp_tasks WHERE project_id IS NOT NULL");
$total_task = $task_count[0]->tasks;
$total_project = $task_count[0]->projects;


$open_task_count = $wpdb->get_results("SELECT COUNT(`id`) AS tasks, COUNT(Distinct `project_id`) as projects FROM {$wpdb->prefix}tmp_tasks WHERE project_id IS NOT NULL AND task_status_id = 7");
$total_open_task = $open_task_count[0]->tasks;
$total_open_project = $open_task_count[0]->projects;

$complete_task_count = $wpdb->get_results("SELECT COUNT(`id`) AS tasks, COUNT(Distinct `project_id`) as projects FROM {$wpdb->prefix}tmp_tasks WHERE project_id IS NOT NULL AND task_status_id = 8");
$total_complete_task = $complete_task_count[0]->tasks;
$total_complete_project = $complete_task_count[0]->projects;

$project_total_count = $wpdb->get_results("SELECT COUNT('id') as projects FROM {$wpdb->prefix}tmp_projects");
$project_total = $project_total_count[0]->projects;

//print_r($project_total);


/*------------------------------------ For Manage Table Data -----------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/task-list-table-class.php';
/**
 * Task Table
 */
//Create an instance of our package class...
$taskTableData = new Task_Manager_Table();
//Fetch, prepare, sort, and filter our data...
if(isset($_REQUEST['s'])&& !empty($_REQUEST['s'])){
	$taskTableData->prepare_items($_REQUEST['s']);
}else{
	$taskTableData->prepare_items();
}
/*------------------------------------ For Manage Table Data -----------------------------------*/


/*--------------------------- Get User Access Information ----------------------*/
$task_access = $taskTableData->getAccessId()['task'];



?>
<div class="wrap tasks tmp">

    <?php if (in_array($task_access, array(13, 16))): ?>
        <div class="section">
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($total_task) {
                                echo $total_task;
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title"><?php _e( 'Total Tasks', 'task-manager' ); ?></span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-editor-paste-text"></span>
                    </div>
                    <div class="footer-content"><?php _e( 'From', 'task-manager' ); ?> <?php if ($total_project) {
                            echo $total_project;
                        } else {
                            echo 0;
                        } ?> <?php _e( 'projects', 'task-manager' ); ?>
                    </div>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($total_open_task) {
                                echo $total_open_task;
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title"><?php _e( 'Open Tasks', 'task-manager' ); ?></span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-welcome-view-site"></span>
                    </div>
                    <div class="footer-content"><?php _e( 'From', 'task-manager' ); ?> <?php if ($total_open_project) {
                            echo $total_open_project;
                        } else {
                            echo 0;
                        } ?> <?php _e( 'projects', 'task-manager' ); ?>
                    </div>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($total_complete_task) {
                                echo $total_complete_task;
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title"><?php _e( 'Completed Tasks', 'task-manager' ); ?></span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-yes"></span>
                    </div>
                    <div class="footer-content"> <?php if ($total_complete_project) {
                            _e( 'From ', 'task-manager' );
                            echo $total_complete_project;
                            _e( 'projects', 'task-manager' );
                        } else {
                            echo '&nbsp;';
                        } ?>
                    </div>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($project_total) {
                                echo $project_total;
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title"><?php _e( 'Total Projects', 'task-manager' ); ?></span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-admin-generic"></span>
                    </div>
                    <div class="footer-content"><a href="?page=projects"><?php _e( 'Go to projects', 'task-manager' ); ?> <span
                                class="dashicons dashicons-migrate"></span></a></div>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <div class="clr"></div>

    <h1 class="wp-heading-inline"><?php _e( 'Tasks', 'task-manager' ); ?></h1>

    <?php if (in_array($task_access, array(12, 13, 16))) { ?>
        <a href="?page=task-new" class="page-title-action">
            <span class="dashicons dashicons-plus-alt"></span>
            <?php _e( 'Add New', 'task-manager' ); ?>
        </a> &nbsp;
        <a href="javascript:void(0)" onclick="importTargetData()" class="page-title-action">
            <span class="dashicons dashicons-database-import"></span>
            <?php _e( 'Import CSV', 'task-manager' ); ?>
        </a>
        <a href="admin-ajax.php?action=export_task_csv" class="page-title-action">
            <span class="dashicons dashicons-database-export"></span>
            <?php _e( 'Export CSV', 'task-manager' ); ?>
        </a>
    <?php } ?>

    <hr class="wp-header-end">
    <form method="get">
        <p class="search-box">
            <label class="screen-reader-text" for="taskSearch">Search Task:</label>
            <input type="hidden" name="page" value="tasks" />
            <input type="search" id="taskSearch" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( 'Search Task', '', '', false, array( 'id' => 'searchBox' ) ); ?>
        </p>
    </form>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="tasks-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <!-- Now we can render the completed list table -->
        <?php $taskTableData->display() ?>
    </form>


    <!-------------------------------------------------------
       -------------- Hidden Form For Post Action -----------
       ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="task_id" name="task_id">
        <input type="hidden" name="act" value="delete">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation"><?php _e( 'You are about to delete the Task. Please check the task progress before delete.
                Do you still want to delete the task ?', 'task-manager' ); ?></p>
            <ul class="cd-buttons">
                <li><a href="javascript:void(0);" onclick="deleteTargetConfirm()"><?php _e( 'Yes', 'task-manager' ); ?></a></li>
                <li><a href="javascript:void(0);" class="closeDialogue"><?php _e( 'No', 'task-manager' ); ?></a></li>
            </ul>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->


    <!-- CSV Import -->
    <div id="csv-import" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <div class="csv_import_container">
                <h2 class="title">Import Tasks From the CSV file</h2>
                <small>If you don't have any existing imported file please use <a href="<?php echo esc_url( plugins_url( '../public/example_import.csv', dirname(__FILE__) ) ) ?>">this file</a> structure to make csv correctly</small>
                <form action="admin-ajax.php?action=import_task_csv" class="input-form" method="post" enctype="multipart/form-data">
                    <div class="button-wrap">
                        <label class="button" for="upload">Upload File</label>
                        <input type="hidden" name="hidden_csv" value="1">
                        <input id="upload" name="upload" type="file">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                </form>
                <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
            </div>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->
    <!-- CSV Import -->

</div>



<script>
    jQuery('#wpcontent').css('background', '#ffffff');
    /*---------------------------------------------
     ----------------- Delete Action ---------------
     ----------------------------------------------*/
    deleteTargetData = function (id) {
        event.preventDefault();
        jQuery('#task_id').val(id);
        jQuery('#delete-popup').addClass('is-visible');
    }

    importTargetData = function () {
        event.preventDefault();
        jQuery('#csv-import').addClass('is-visible');
    }

    deleteTargetConfirm = function () {
        jQuery('#action-delete-form').submit();
        event.preventDefault();
        jQuery('.cd-popup').removeClass('is-visible');
    }

    /*------------- End - Delete Action -------------*/

    jQuery('.cd-popup').on('click', function (event) {
        if (jQuery(event.target).is('.closeDialogue')) {
            event.preventDefault();
            jQuery(this).removeClass('is-visible');
        }
    });
</script>


<?php

$actionArray = array('add_task', 'edit_task');

if ( isset($_POST['act']) && in_array($_POST['act'], $actionArray) && count($notifyMembers)>0 ){
    $userBasedCustomQuery->sendNotificationMessage($notifyMembers);
}


?>