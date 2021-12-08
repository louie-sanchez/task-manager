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



/*------------------ Call Global Variable ----------------------*/
global $wpdb;
$owner_id = wp_get_current_user()->ID;

/*--------------------------------------------------
--------------- Add New Project Query --------------
---------------------------------------------------*/
//?page=projects
if ($_GET['page'] == 'projects' && isset($_POST['act']) && ($_POST['act'] == 'add_project' || $_POST['act'] == 'edit_project') && isset($_POST['project_name'])) {


    // Include Data Insertion classs
    require_once plugin_dir_path(__FILE__) . '../partials/ProjectsPageCustomQuery.php';
    //Create an instance of our package class...
    $userBasedCustomQuery = new User_Based_Custom_Query();

	$projectDetails = stripslashes($_POST['projectDetails']);
	$userGroup = isset($_POST['user_group']) ? $_POST['user_group'] : array();

    if ($_POST['act'] == 'add_project') {
        $notifyMembers = $userBasedCustomQuery->insertProjectData( $_POST['project_name'], $_POST['project_category'], $_POST['project_status'],
            $_POST['live_url'], $_POST['test_url'], $_POST['design_date'], $_POST['development_date'], $_POST['test_date'],
	        $projectDetails, $_POST['go_live'], $_POST['team_members'], $userGroup );
    }

    if ($_POST['act'] == 'edit_project') {
        $notifyMembers = $userBasedCustomQuery->editProjectData( $_POST['project_name'], $_POST['project_category'], $_POST['project_status'],
            $_POST['live_url'], $_POST['test_url'], $_POST['design_date'], $_POST['development_date'], $_POST['test_date'],
	        $projectDetails, $_POST['go_live'], $_POST['team_members'], $userGroup, $_POST['project_id'] );
    }
    
}

/*--------------------------------------
-------- Execute Query To Delete Project
---------------------------------------*/
if ($_GET['page'] == 'projects' && isset($_POST['act']) && $_POST['act'] == 'delete' && isset($_POST['project_id']) && !empty($_POST['project_id'])) {
    $project_id = $_POST['project_id'];
    $project_table = "{$wpdb->prefix}tmp_projects";
    $project_groups_table = "{$wpdb->prefix}tmp_project_groups";
    $project_users_table = "{$wpdb->prefix}tmp_project_users";
    $task_table = "{$wpdb->prefix}tmp_tasks";
    $task_groups_table = "{$wpdb->prefix}tmp_task_groups";
    $task_users_table = "{$wpdb->prefix}tmp_task_users";

    // Delete Tasks Users and Groups Table Data
    $get_tasks_from_group = $wpdb->get_results("SELECT tt.id FROM {$wpdb->prefix}tmp_tasks tt WHERE tt.project_id = {$project_id}");
    $get_tasks_from_groupIds = array();
    foreach ($get_tasks_from_group as $gTask) {
        $get_tasks_from_groupIds[] = $gTask->id;
    }
    $get_tasks_from_groupIds = implode(',', $get_tasks_from_groupIds);
    $wpdb->query("DELETE FROM {$wpdb->prefix}tmp_task_users WHERE task_id IN($get_tasks_from_groupIds)");
    $wpdb->query("DELETE FROM {$wpdb->prefix}tmp_task_groups WHERE task_id IN($get_tasks_from_groupIds)");

    // Delete Tasks by Project
    $wpdb->delete($task_table, array('project_id' => $project_id));

    // Delete Project by Project ID
    $wpdb->delete($project_table, array('id' => $project_id));

    // Delete Project Users
    $wpdb->delete($project_groups_table, array('project_id' => $project_id));
    $wpdb->delete($project_users_table, array('project_id' => $project_id));
}

/*---------------- End Executed Query ----------------*/


//print_r($projectQueryData['total_users']);exit;

/*------------------------------------------ Project Table List Data Manage ------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/project-list-table-class.php';
require_once plugin_dir_path(__FILE__) . '../partials/ProjectsPageCustomQuery.php';

//Create an instance of our package class...
$projectTableData = new Task_Manager_Table();
$projectQueryData = new ProjectsPageCustomQuery();
$projectQueryData = $projectQueryData->getProjectsTotalCount();
//Fetch, prepare, sort, and filter our data...
if(isset($_REQUEST['s'])&& !empty($_REQUEST['s'])){
	$projectTableData->prepare_items($_REQUEST['s']);
}else{
	$projectTableData->prepare_items();
}

/*------------------------------------------ Project Table List Data Manage ------------------------------------*/

/*---------------------------------------- Get User Access Id Info --------------------------------------------*/
$project_access = $projectTableData->getAccessId()['project'];
/*---------------------------------------- Get User Access Id Info --------------------------------------------*/

?>
<div class="wrap projects tmp">


    <?php if (in_array($project_access, array(13, 16))): ?>
        <div class="section">
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($projectQueryData['total_open_project']) {
                                echo $projectQueryData['total_open_project'];
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title"><?php _e( 'Open Projects', 'task-manager' ); ?></span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-editor-spellcheck"></span>
                    </div>
                    <div class="footer-content">From <?php if ($projectQueryData['total_project']) {
                            echo $projectQueryData['total_project'];
                        } else {
                            echo 0;
                        } ?> <?php _e( 'total projects', 'task-manager' ); ?>
                    </div>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($projectQueryData['total_close_project']) {
                                echo $projectQueryData['total_close_project'];
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title"> <?php _e( 'Closed Projects', 'task-manager' ); ?></span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-hidden"></span>
                    </div>
                    <div class="footer-content">From <?php if ($projectQueryData['total_project']) {
                            echo $projectQueryData['total_project'];
                        } else {
                            echo 0;
                        } ?> <?php _e( 'total projects', 'task-manager' ); ?>
                    </div>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($projectQueryData['total_task']) {
                                echo $projectQueryData['total_task'];
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title"><?php _e( 'Total Tasks', 'task-manager' ); ?></span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-editor-paste-text"></span>
                    </div>
                    <div class="footer-content"><a href="?page=tasks"><?php _e( 'Go to tasks', 'task-manager' ); ?><span
                                class="dashicons dashicons-migrate"></span></a></div>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($projectQueryData['total_users']) {
                                echo $projectQueryData['total_users'];
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title"><?php _e( 'People Involved', 'task-manager' ); ?></span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-admin-users"></span>
                    </div>
                    <div class="footer-content"><a href="?page=manage-users"> <?php _e( 'Go to Manage Users', 'task-manager' ); ?><span
                                class="dashicons dashicons-migrate"></span></a></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="clr"></div>


    <h1 class="wp-heading-inline"><?php _e( 'Projects', 'task-manager' ); ?></h1>

    <?php if (in_array($project_access, array(12, 13, 16))) { ?>
        <a href="?page=project-new" class="page-title-action">
            <span class="dashicons dashicons-plus-alt"></span>
            <?php _e( 'Add New', 'task-manager' ); ?>
        </a>
<!--        <a href="admin-ajax.php?action=export_project_csv" class="page-title-action">--><?php //_e( 'Export CSV', 'task-manager' ); ?><!--</a>-->
    <?php } ?>

    <hr class="wp-header-end">
    <form method="get">
    <p class="search-box">
        <label class="screen-reader-text" for="projectSearch">Search Project:</label>
        <input type="hidden" name="page" value="projects" />
        <input type="search" id="projectSearch" name="s" value="<?php _admin_search_query(); ?>" />
		<?php submit_button( 'Search Project', '', '', false, array( 'id' => 'projectSearch' ) ); ?>
    </p>
    </form>
    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="tasks-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <!-- Now we can render the completed list table -->
        <?php $projectTableData->display() ?>
    </form>


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="project_id" name="project_id">
        <input type="hidden" name="act" value="delete">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation"><?php _e( 'You are about to delete the Project. All of the tasks associate with this project
                will
                be delete. Are you sure ?', 'task-manager' ); ?></p>
            <ul class="cd-buttons">
                <li><a href="javascript:void(0);" onclick="deleteTargetConfirm()"><?php _e( 'Yes', 'task-manager' ); ?></a></li>
                <li><a href="javascript:void(0);" class="closeDialogue"><?php _e( 'No', 'task-manager' ); ?></a></li>
            </ul>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->

</div>

<script>

    /*---------------------------------------------
     ----------------- Delete Action ---------------
     ----------------------------------------------*/
    // Change Background Color

    jQuery('#wpcontent').css('background', '#ffffff');
    deleteTargetData = function (id) {
        event.preventDefault();
        jQuery('#project_id').val(id);
        jQuery('#delete-popup').addClass('is-visible');
    }

    function deleteTargetConfirm() {
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

$actionArray = array('add_project', 'edit_project');

if ( isset($_POST['act']) && in_array($_POST['act'], $actionArray) && count($notifyMembers)>0 ){
    $userBasedCustomQuery->sendNotificationMessage($notifyMembers);
}

?>