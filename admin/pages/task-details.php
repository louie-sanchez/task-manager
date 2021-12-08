<?php

global $wpdb;

$currentUserId = wp_get_current_user()->ID;

/*--------------------------- Get User Access Information ----------------------*/
// To Get User Access Id
require_once plugin_dir_path(__FILE__) . '../partials/user-based-custom-query-class.php';
$userCustomQueryObject = new User_Based_Custom_Query();
$task_access = $userCustomQueryObject->getUserAccessId()['task'];
$task_table_name = $wpdb->prefix . "tmp_tasks";

/*--------------------------------------------------------------- Top Functionalities -------------------------------------*/

/*---------------------------------------------------------------------------
--------------------------- Change Task Status ------------------------------
----------------------------------------------------------------------------*/
$task_table_name = $wpdb->prefix . "tmp_tasks";
if (isset($_POST['action']) && $_POST['action'] == 'changeStatus' && isset($_POST['task_id']) && !empty($_POST['task_id']) && isset($_POST['edit_task_status_id']) && !empty($_POST['edit_task_status_id'])) {
    $updated_at = date('Y-m-d H:i:s');
    $wpdb->update($task_table_name, array('task_status_id' => intval($_POST['edit_task_status_id']), 'last_update_by' => $currentUserId, 'updated_at' => $updated_at), array('id' => $_POST['task_id']));
}

/*---------------------------------------------------------------------------
--------------------------- Change Task Progress ------------------------------
----------------------------------------------------------------------------*/
if (isset($_POST['action']) && $_POST['action'] == 'changeProgress' && isset($_POST['task_id']) && !empty($_POST['task_id']) && isset($_POST['edit_task_progress']) && !empty($_POST['edit_task_progress'])) {
    $updated_at = date('Y-m-d H:i:s');
    $wpdb->update($task_table_name, array('progress' => $_POST['edit_task_progress'], 'last_update_by' => $currentUserId, 'updated_at' => $updated_at), array('id' => $_POST['task_id']));
}

/*---------------------------------------------------------------------------
--------------------------- Add A New Comment ------------------------------
----------------------------------------------------------------------------*/
$comment_table_name = $wpdb->prefix . "tmp_task_comments";
if (isset($_POST['action']) && $_POST['action'] == 'addComment' && isset($_POST['task_id']) && !empty($_POST['task_id']) && isset($_POST['commentArea']) && !empty($_POST['commentArea'])) {
    $created_at = date('Y-m-d H:i:s');
    $commentAreaData =  stripslashes($_POST['commentArea'] );
    $wpdb->insert($comment_table_name, array('task_id' => $_POST['task_id'], 'comment_by' => $currentUserId, 'comment' => $commentAreaData, 'created_at' => $created_at));
}


/*--------------------- Check Get Request Method -------------------*/
if (isset($_REQUEST['task']) && !empty($_REQUEST['task'])) {
    $task = $_REQUEST['task'];
} else {
    $task = null;
}

// Set Image Directory
$img_dir_url = plugins_url('../images/', __FILE__);

/*------------------------------------------------------------------
--------------------- Query To get Project Data -------------------
-------------------------------------------------------------------*/
$get_task = $wpdb->get_results("
        SELECT tsk.id, tp.name as project, tskt.name as task_type, tsk.name, tsk.amount, tts.name as status, ttp.name as task_priority, ttp.icon as priority_icon,
            ttl.name as label, tsk.description, tsk.esitmate_time, tsk.task_status_id,
            tsk.start_date, tsk.due_date, tsk.progress, tsk.created_by, tsk.last_update_by,
            tsk.created_at, tsk.updated_at
            FROM {$wpdb->prefix}tmp_tasks tsk 
            LEFT JOIN {$wpdb->prefix}tmp_projects tp ON tsk.project_id = tp.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_types tskt ON tsk.task_type_id = tskt.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_status tts ON tsk.task_status_id = tts.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_priorities ttp ON tsk.task_priority_id = ttp.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_labels ttl ON tsk.task_label_id = ttl.id 
            WHERE tsk.id = {$task} limit 1
         ");


$task_details = $get_task[0];
$owner = $task_details->created_by;

$task_details->created_by = get_user_by('id', $task_details->created_by)->display_name;
$task_details->last_update_by = get_user_by('id', $task_details->last_update_by)->display_name;


// Get Users For this task
$cTaskUsers = $wpdb->get_results("
        SELECT ttu.user_id as id, usr.display_name as name
            FROM {$wpdb->prefix}tmp_task_users ttu
            INNER JOIN {$wpdb->prefix}users as usr ON usr.id = ttu.user_id
            WHERE ttu.task_id = {$task} 
         ");

// Get user ids as array
$cTaskUsersIds = array();
foreach ($cTaskUsers as $tuser) {
    $cTaskUsersIds[] = $tuser->id;
}

// Get User Groups For this task
$eTaskGroupUsers = $wpdb->get_results(" SELECT tu.user_id FROM {$wpdb->prefix}tmp_task_groups ttg 
      INNER JOIN {$wpdb->prefix}tmp_users tu ON tu.user_group_id = ttg.group_id
      WHERE ttg.task_id = {$task}");

// Get Group user ids as array
$eTaskGroupUsersIds = array();
foreach ($eTaskGroupUsers as $tguser) {
    $eTaskGroupUsersIds[] = $tguser->user_id;
}

// Get All user ids as unique way
$fTaskUsersIds = array_unique(array_merge($cTaskUsersIds, $eTaskGroupUsersIds));
$fTaskUsersImplode = implode(',', $fTaskUsersIds);

if(!empty($fProjectUsersImplode)) {
	$task_detailsUsers = $wpdb->get_results( "SELECT usr.ID as id, usr.display_name as name FROM {$wpdb->prefix}users usr WHERE usr.ID IN ({$fTaskUsersImplode}) " );
}else{
	$task_detailsUsers = array();
}

/*------------------------ Get Task Status ----------------------*/
if (isset($task_details->task_status_id) && !empty($task_details->task_status_id)) {
    $statusid = $task_details->task_status_id;
} else {
    $statusid = 0;
}
$get_task_status = $wpdb->get_results("
        SELECT ts.id, ts.name
        FROM {$wpdb->prefix}tmp_task_status as ts WHERE ts.id NOT IN ({$statusid})
         ");

/*-------------------------------------------------------------
--------------------- Get All Comments -----------------------
--------------------------------------------------------------*/
$get_task_comments = $wpdb->get_results("
        SELECT tc.comment, tc.created_at, usr.display_name as name
        FROM {$comment_table_name} as tc 
        LEFT JOIN {$wpdb->prefix}users usr ON tc.comment_by = usr.id 
        WHERE tc.task_id = {$task}
         ");


/*--------------------------------------------------------------
-------------------- Notification Information ------------------
---------------------------------------------------------------*/
$notificationData = array();
$notificationData['id'] = $task;
$notificationData['type'] = 'task';
$notificationData['name'] = $task_details->name;
$notificationData['status'] = 'update';
$notificationData['notification'] = $fTaskUsersIds;
$notificationData['owner'] = get_user_by('id', $owner)->user_email;

$tmp_currency_symbol = get_option( 'tmp_currency_symbol', '$' );

?>


<div class="wrap tmp project_details task_details">
    <div id="goBack" class="back-button"><span class="dashicons dashicons-undo"></span> <span class="btn">Back</span></div>
    <div class="padding-20"></div>
    <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24 left">
        <div class="heading-title">
            <?php if (isset($task_details->name) && !empty($task_details->name)) { ?>
                <div class="title-image">
                    <?php echo substr(ucwords($task_details->name), 0, 2); ?>
                </div>
            <?php } ?>
            <div class="heading">
                <?php if (isset($task_details->name) && !empty($task_details->name)) { ?>
                    <h2><?php echo $task_details->name; ?> <span>(<?php echo $task_details->label; ?>)</span></h2>
                <?php } ?>
                <?php if (isset($task_details->task_type) && !empty($task_details->task_type)) { ?>
                    <span><?php echo $task_details->task_type; ?></span>
                <?php } ?>
            </div>
            <div class="edit-option">
                <?php if (in_array($task_access, array(11, 12, 13, 16))) { ?>
                    <div class="edit_project">
                        <a href="?page=task-edit&task=<?php if (isset($task) && !empty($task)) {
                            echo $task;
                        } ?>" title="Edit Project">
                            <span class="dashicons dashicons-edit"></span>
                            <span class="title"><?php _e( 'Edit Task', 'task-manager' ); ?></span>
                        </a>

                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="clr"></div>
        <?php if (isset($task_details->description) && !empty($task_details->description)) { ?>
            <div class="details">
                <div class="title"><span><?php _e( 'Details', 'task-manager' ); ?></span></div>
                <div class="description">
                    <?php print htmlspecialchars_decode($task_details->description) ; ?>
                </div>
            </div>
            <div class="clr"></div>
        <?php } ?>
        <div class="project_others">
            <div class="p-u-sm-24-24 p-u-md-7-24 p-u-lg-7-24 left">
                <ul class="other-info">
                    <?php if (isset($task_details->task_priority) && !empty($task_details->task_priority)) { ?>
                        <li>
                            <label><?php _e( 'Task Priority', 'task-manager' ); ?></label>
                                <span>
                                    <?php if ($task_details->priority_icon) { ?> <img
                                        src="<?php echo $img_dir_url . 'icons/' . $task_details->priority_icon; ?>"
                                        alt=""/> <?php } ?>
                                    <?php echo $task_details->task_priority; ?>
                                </span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
	        <?php if (isset($task_details->amount) && !empty($task_details->amount)) { ?>
            <div class="p-u-sm-24-24 p-u-md-7-24 p-u-lg-7-24 left">
                <ul class="other-info">
                    <li>
                        <label><?php _e( 'Amount', 'task-manager' ); ?></label>
                        <span><?php echo $tmp_currency_symbol.' '.$task_details->amount; ?></span>
                    </li>
                </ul>
            </div>
	        <?php } ?>
            <div class="p-u-sm-24-24 p-u-md-10-24 p-u-lg-10-24 right">
                <ul class="other-info">
                    <?php $et_mh = explode(":", $task_details->esitmate_time); ?>
                    <?php if (isset($task_details->esitmate_time) && !empty($task_details->esitmate_time)) { ?>
                        <li>
                            <label><?php _e( 'Time Estimation', 'task-manager' ); ?></label>
                                <span>
                                    <?php echo $et_mh[0] . ($et_mh[0] >1 ?' hours':' hour'); ?>
                                    <?php if($et_mh[1]){
	                                    echo ' and '.$et_mh[1] . ($et_mh[1] >1 ?' minutes':' minute');
                                    }?>
                                </span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="clr"></div>
        <div class="project_others">
            <div class="title"><span><?php _e( 'Task Progress', 'task-manager' ); ?></span></div>
            <input type="hidden" name="task_progress" id="task_progress_value"
                   value="<?php echo $task_details->progress; ?>">
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24 left">
                <div class="task_progress_chart">
                    <svg viewBox="0 0 32 32">
                        <circle class='task' stroke-dasharray="<?php echo $task_details->progress; ?> 100"></circle>
                        <span id="task_progress_chart_val"><?php echo $task_details->progress; ?>%</span>
                    </svg>
                </div>
            </div>
            <?php if( in_array($task_access, array(13, 12, 14, 11, 16, 8)) ): ?>
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24 right">
                <h2 class="progress_title"></h2>
                <div class="clr"></div>
                <div class="task_progress">
                    <div class="ui_slider">
                        <div id="custom-handle" class="ui-slider-handle"></div>
                    </div>
                </div>
                <div class="save-button" style="display: none">
                    <span id="saveProgress" class="button button-primary button-large"><?php _e( 'Save', 'task-manager' ); ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="clr"></div>
        <div class="project_others">
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24 left">
                <ul class="other-info">
                    <?php if (isset($task_details->start_date) && !empty($task_details->start_date)) { ?>
                        <li>
                            <label><?php _e( 'Task Start From', 'task-manager' ); ?></label>
                                <span>
                                    <?php echo date("F j, Y", strtotime($task_details->start_date)); ?>
                                </span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24 right">
                <ul class="other-info">
                    <?php if (isset($task_details->due_date) && !empty($task_details->due_date)) { ?>
                        <li>
                            <label><?php _e( 'Due Date', 'task-manager' ); ?></label>
                                <span>
                                    <?php echo date("F j, Y", strtotime($task_details->due_date)); ?>
                                </span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="clr"></div>

        <div class="project_others">
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24 left">
                <ul class="other-info">
                    <?php if (isset($task_details->created_at) && !empty($task_details->created_at)) { ?>
                        <li>
                            <label><?php _e( 'Task Created', 'task-manager' ); ?></label>
                                <span>
                                    <?php echo date("F j, Y - g:i a", strtotime($task_details->created_at)); ?>
                                </span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24 right">
                <ul class="other-info">
                    <?php if (isset($task_details->updated_at) && !empty($task_details->updated_at)) { ?>
                        <li>
                            <label><?php _e( 'Last Updated', 'task-manager' ); ?></label>
                                <span>
                                    <?php echo date("F j, Y - g:i a", strtotime($task_details->updated_at)); ?>
                                </span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="clr"></div>
        <hr class="hr">
        <div class="p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="details">
                <div class="title"><span><?php _e( 'Task Comments', 'task-manager' ); ?></span></div>
                <div class="comment-content">
                    <?php if (isset($get_task_comments) && !empty($get_task_comments)) { ?>
                        <ul>
                            <?php foreach ($get_task_comments as $comment): ?>
                                <li>
                                    <div class="p-u-sm-3-24 p-u-md-3-24 p-u-lg-3-24 user-avatar">
                                        <span class="round-name"><?php echo substr($comment->name, 0, 2); ?></span>
                                    </div>
                                    <div class="p-u-sm-21-24 p-u-md-21-24 p-u-lg-21-24 name-details">
                                        <div class="comment-user">
                                            <span class="user-name"><?php echo $comment->name; ?></span>
                                            <span
                                                class="send-date"><?php echo date("F j, Y, g:i a", strtotime($comment->created_at)); ?></span>
                                        </div>
                                        <div class="comment-details">
	                                        <?php print htmlspecialchars_decode($comment->comment) ; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php } else { ?>
                        <p> <?php _e( 'No Comment Added Yet', 'task-manager' ); ?></p>
                    <?php } ?>

                </div>
            </div>
        </div>

    </div>
    <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24 right">
        <div class="container">
            <?php if (isset($task_details->status) && !empty($task_details->status)) { ?>
                <div class="project-status">
                    <div class="title"><span><?php _e( 'Task Status', 'task-manager' ); ?></span></div>
                    <div class="content">
                        <div class="status-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                        <span id="task_status_id" class="status-name"
                              data-status="<?php echo $task_details->status; ?>"><?php echo $task_details->status; ?></span>
                    </div>
                </div>
                <div class="clr"></div>
            <?php } ?>
            <?php if( in_array($task_access, array(13, 12, 14, 11, 16)) ): ?>
            <div class="tmp_wrapper_round_bar_design">
                <div class="title"><span><?php _e( 'Assign Task As', 'task-manager' ); ?></span></div>
                <div class="content assign_task">
                    <div class="form-select">
                        <select id="select_task_status_id" data-custom="" name="project_id">
                            <option value=""><?php _e( 'Select Status', 'task-manager' ); ?></option>
                            <?php foreach ($get_task_status as $status): ?>
                                <option value="<?php echo $status->id; ?>">
                                    <?php esc_html_e($status->name, 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="submit" name="publish" id="change" class="button button-primary button-large"
                               value="<?php _e( 'Change', 'task-manager' ); ?>">
                        <span id="status-save-notification" class="data-saved" style="display: none"></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
	        <?php if (isset($cTaskUsers) && !empty($cTaskUsers)) { ?>
                <div class="tmp_wrapper_round_bar_design">
                    <div class="title"><span><?php _e( 'Assigned To', 'task-manager' ); ?></span></div>
                    <?php foreach ($cTaskUsers as $user): ?>
                    <div class="content">
                        <span class="round-name"><?php echo substr($user->name, 0, 2); ?></span>
                        <span class="name"><?php echo $user->name; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="clr"></div>
	        <?php } ?>
            <div class="clr"></div>
            <div class="tmp_wrapper_round_bar_design comment">
                <div class="title"><span><?php _e( 'Add a comment', 'task-manager' ); ?></span></div>
                <div class="content">
                    <!-- Add New Comment form -->
                    <form id="add-new-comment-form" method="POST" action="" autocomplete="off">
                        <input type="hidden" name="action" value="addComment">
                        <input type="hidden" name="task_id" value="<?php echo $task_details->id; ?>">
                        <div class="form-textarea">
<!--                            <textarea id="commentArea" name="add-new-comment-value"></textarea>-->
	                        <?php wp_editor('', 'commentArea', array('media_buttons' => true, 'editor_height' => 200, 'textarea_rows' => 10, 'tinymce' => array('toolbar1'=> 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo'))); ?>
                        </div>
                    </form>
                    <br>
                    <div class="form-input">
                        <input type="submit" name="comment" id="addComment" class="button button-primary button-large"
                               value="<?php _e( 'Add', 'task-manager' ); ?>">
                    </div>

                </div>
            </div>
            <?php if (isset($task_details->created_by) && !empty($task_details->created_by)) { ?>
                <div class="tmp_wrapper_round_bar_design">
                    <div class="title"><span><?php _e( 'Created By', 'task-manager' ); ?></span></div>
                    <div class="content">
                        <span class="round-name"><?php echo substr($task_details->created_by, 0, 2); ?></span> <span
                            class="name"><?php echo $task_details->created_by; ?></span>
                    </div>
                </div>
                <div class="clr"></div>
            <?php } ?>
            <?php if (isset($task_details->last_update_by) && !empty($task_details->last_update_by)) { ?>
                <div class="tmp_wrapper_round_bar_design">
                    <div class="title"><span><?php _e( 'Last Updated By', 'task-manager' ); ?></span></div>
                    <div class="content">
                        <span class="round-name"><?php echo substr($task_details->last_update_by, 0, 2); ?></span>
                        <span class="name"><?php echo $task_details->last_update_by; ?></span>
                    </div>
                </div>
                <div class="clr"></div>
            <?php } ?>
            <?php if (isset($task_detailsUsers) && !empty($task_detailsUsers)) { ?>
                <div class="tmp_wrapper_round_bar_design">
                    <div class="title"><span><?php _e( 'Contacts Involved With This Task', 'task-manager' ); ?></span></div>
                    <div class="contacts-list">
                        <ul>
                            <?php foreach ($task_detailsUsers as $user): ?>
                                <li><span class="round-name"><?php echo substr($user->name, 0, 2); ?></span>
                                    <span class="name"><?php echo $user->name; ?></span></li>

                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>


    </div>


    <!--********************************************************
    ********************* Edit Task Status ******************
    **********************************************************-->
    <form id="action-task-edit-form" method="POST" action="" autocomplete="off" style="display: none;">
        <input type="hidden" name="action" value="changeStatus">
        <input type="hidden" name="task_id" value="<?php echo $task_details->id; ?>">
        <input type="hidden" name="edit_task_status_id" id="edit_task_status_id"
               value="<?php echo $task_details->task_status_id; ?>">
    </form>

    <!--********************************************************
    ********************* Edit Task Progress ******************
    **********************************************************-->
    <form id="task-edit-progress-form" method="POST" action="" autocomplete="off" style="display: none;">
        <input type="hidden" name="action" value="changeProgress">
        <input type="hidden" name="task_id" value="<?php echo $task_details->id; ?>">
        <input type="hidden" name="edit_task_progress" id="edit_task_progress_value"
               value="<?php echo $task_details->progress; ?>">
    </form>


</div>

<!--********************************************************
********************* Page Specific Script ******************
**********************************************************-->
<script>
    jQuery('#wpcontent').css('background', '#ffffff');
    jQuery(document).ready(function () {
        var handle = jQuery("#custom-handle");
        var taskProgress = jQuery("#task_progress_value").val();
        jQuery('.ui_slider').slider({
            value: taskProgress,
            orientation: "horizontal",
            range: "min",
            animate: true,
            max: 100,
            create: function () {
                handle.text(jQuery(this).slider("value"));
            },
            slide: function (event, ui) {
                handle.text(ui.value + '%');
                jQuery('#task_progress_chart_val').text(ui.value + '%');
                jQuery('.project_others .save-button').css('display', 'block');
                jQuery('#task_progress_value').val(ui.value);
                jQuery('#edit_task_progress_value').val(ui.value);
                jQuery('circle.task').attr('stroke-dasharray', ui.value + ' ' + 100);
            }
        });
        handle.text(handle.text() + '%');


        /*-----------------------------------------------------
         ----------------- Change Task Status -------------------
         -----------------------------------------------------*/
        jQuery('#select_task_status_id').on('change', function () {
            jQuery('#edit_task_status_id').val(jQuery(this).val());
        });
        jQuery(".assign_task #change").click(function () {
            jQuery('#action-task-edit-form').submit();
        });

        /*-------------------------------------------------------
         --------------------- Change Task Progress --------------
         -------------------------------------------------------*/
        jQuery("#saveProgress").click(function () {
            jQuery('#task-edit-progress-form').submit();
        });


        /*-------------------------------------------------------
         --------------------- Add A New Comment --------------
         -------------------------------------------------------*/
        jQuery("#addComment").click(function () {
            // var commentValue = jQuery('#commentArea').val();
            // var commentValue = jQuery("input[name=commentArea]").val();;
            // console.log(commentValue);
            // if (commentValue.length) {
            //     jQuery('#add-new-comment-form').submit();
            // }
            jQuery('#add-new-comment-form').submit();
        });

        jQuery("#goBack").on('click', function () {
            window.history.back();
        });

        /*------------------------------------------------------
        ----------- Add Media Button to Comment Area -----------
         ------------------------------------------------------*/

    });


</script>

<?php


$actionArray = array('changeStatus', 'changeProgress', 'addComment');

if(isset($_POST['action']) && in_array($_POST['action'], $actionArray)){
    $userCustomQueryObject->sendNotificationMessage($notificationData);
}
?>