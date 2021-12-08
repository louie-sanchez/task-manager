<?php

//if (!current_user_can('upload_files')) {
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


global $wpdb; //This is used only if making any database queries


/*--------------------------------------------------------------------
--------------------- Additional Query for create task ------------
---------------------------------------------------------------------*/
// Get Project Name
$projects = $wpdb->get_results("
        SELECT pr.id, pr.name
        FROM {$wpdb->prefix}tmp_projects as pr
         ");

// Get task Types
$task_types = $wpdb->get_results("
        SELECT tt.id, tt.name
        FROM {$wpdb->prefix}tmp_task_types as tt
         ");

// Get task Types
$task_labels = $wpdb->get_results("
        SELECT tl.id, tl.name
        FROM {$wpdb->prefix}tmp_task_labels as tl
         ");

// Get task Status
$task_status = $wpdb->get_results("
        SELECT ts.id, ts.name, ts.group_name
        FROM {$wpdb->prefix}tmp_task_status as ts
        ORDER BY ts.group_name DESC
         ");

// Get Task Priority
$task_priority = $wpdb->get_results("
        SELECT tp.id, tp.name, tp.icon
        FROM {$wpdb->prefix}tmp_task_priorities as tp
         ");

// Get  User Groups
$user_groups = $wpdb->get_results("
        SELECT ug.id, ug.name
        FROM {$wpdb->prefix}tmp_user_groups as ug
         ");

// Get TMP Users
$tmp_users = $wpdb->get_results("
        SELECT usr.id as id, usr.display_name as name
        FROM {$wpdb->prefix}users as usr
         ");


// Get Existing info for the task
$modify_task_id = $_REQUEST['task'];
$eTask = $wpdb->get_results("
        SELECT tsk.id, tsk.project_id, tsk.task_type_id, tsk.name, tsk.task_status_id, tsk.task_priority_id, 
            tsk.task_label_id, tsk.description, tsk.created_by, tsk.esitmate_time, tsk.amount,
            tsk.start_date, tsk.due_date, tsk.progress, tsk.last_update_by
            FROM {$wpdb->prefix}tmp_tasks tsk WHERE tsk.id = {$modify_task_id} limit 1
         ");

$cTask = $eTask[0];
$cTaskGroups = array();

// Get Users For this task
$cTaskUsers = $wpdb->get_results("
        SELECT ttu.user_id as id, usr.display_name as name
            FROM {$wpdb->prefix}tmp_task_users ttu
            INNER JOIN {$wpdb->prefix}users as usr ON usr.id = ttu.user_id
            WHERE ttu.task_id = {$modify_task_id} 
         ");

// Get User Groups For this task
$eTaskGroups = $wpdb->get_results("
        SELECT ttg.group_id
            FROM {$wpdb->prefix}tmp_task_groups ttg
            WHERE ttg.task_id = {$modify_task_id} 
         ");

// Get Group id as array
foreach ($eTaskGroups as $group) {
    $cTaskGroups[] = $group->group_id;
}


/*--------- END - Additional Query for create Task --------------*/

?>
<div class="wrap tmp" id="newTask">

    <form name="task-edit" method="post" action="?page=tasks">

        <input type="hidden" name="act" value="edit_task">
        <input type="hidden" name="task_id" value="<?php echo $modify_task_id; ?>">

        <h1 class="wp-heading-inline"><?php _e( 'Edit Existing Task', 'task-manager' ); ?></h1>
        <hr class="wp-header-end">
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Project', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-image-filter"></span>
                        <select id="" data-custom="" name="project_id">
                            <?php foreach ($projects as $project): ?>
                                <option
                                    value="<?php echo $project->id; ?>" <?php if ($cTask->project_id == $project->id) {
                                    echo 'selected';
                                } ?>>
                                    <?php esc_html_e($project->name, 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The name of the Task - where you can define your Task.', 'task-manager' ); ?></p>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Task Type', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-welcome-widgets-menus"></span>
                        <select id="" data-custom="" name="task_type">
                            <?php foreach ($task_types as $task_type): ?>
                                <option
                                    value="<?php echo $task_type->id; ?>" <?php if ($cTask->task_type_id == $task_type->id) {
                                    echo 'selected';
                                } ?>>
                                    <?php esc_html_e($task_type->name, 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The type of Task you are creating.', 'task-manager' ); ?></p>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Label', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-editor-spellcheck"></span>
                        <select id="" data-custom="" name="task_label">
                            <?php foreach ($task_labels as $task_label): ?>
                                <option
                                    value="<?php echo $task_label->id; ?>" <?php if ($cTask->task_label_id == $task_label->id) {
                                    echo 'selected';
                                } ?>>
                                    <?php esc_html_e($task_label->name, 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The type of Task you are creating.', 'task-manager' ); ?></p>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-6-24 p-u-lg-6-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Amount', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-money-alt"></span>
                        <input type="text" name="amount" size="30" value="<?php echo $cTask->amount; ?>" placeholder="<?php _e( 'e.g. 10', 'task-manager' ); ?>"
                               spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'The budget amount for this task', 'task-manager' ); ?></p>
                </div>
            </div>

        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Task Name', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-editor-paste-text"></span>
                        <input type="text" name="task_name" size="30" value="<?php echo $cTask->name; ?>"
                               spellcheck="true"
                               autocomplete="off">
                    </div>
                    <p><?php _e( 'The name of the task - where you can define your task.', 'task-manager' ); ?></p>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Task Status', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-editor-expand"></span>
                        <select id="" data-custom="" name="task_status">
                            <?php foreach ($task_status as $status): ?>
                                <option
                                    value="<?php echo $status->id; ?>" <?php if ($cTask->task_status_id == $status->id) {
                                    echo 'selected';
                                } ?>>
                                    <?php esc_html_e($status->name, 'task_manager'); ?>
                                    (<?php echo $status->group_name; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The Task status - current status of task.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-select">
                    <h2 class="title"><?php _e( 'Priority', 'task-manager' ); ?></h2>
                    <div class="select">
                        <span class="dashicons dashicons-chart-bar"></span>
                        <select id="" data-custom="" name="task_priority">
                            <?php foreach ($task_priority as $priority): ?>
                                <option
                                    value="<?php echo $priority->id; ?>" <?php if ($cTask->task_priority_id == $priority->id) {
                                    echo 'selected';
                                } ?>>
                                    <?php esc_html_e($priority->name, 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p><?php _e( 'The type of Task you are creating.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Estimation Time', 'task-manager' ); ?></h2>
                    <div class="input t__s">
                        <span class="dashicons dashicons-clock"></span>
<!--                        <input type="text" name="estimation_time" size="30" value="--><?php //echo $cTask->esitmate_time; ?><!--"-->
<!--                               spellcheck="true" autocomplete="off">-->
                        <?php
                        $estimateTime = '';
                        $et_mh = explode(":", $cTask->esitmate_time);
                        if(!empty($et_mh)){
                            $estimateTime = $et_mh[0].':'.$et_mh[1];
                        }
                        ?>
                        <div class="time_selection">
                            <select id="estimateHour">
			                    <?php
			                    for($i = 0; $i<=200; $i++) {
				                    echo '<option value="'.$i.'" '. ($et_mh[0] == $i ? 'selected' : '') .'>';
				                    echo $i . ($i >1 ?' hours':' hour');
				                    echo '</option>';
			                    }
			                    ?>
                            </select>
                            <select id="estimateMinute">
			                    <?php
			                    for($i = 0; $i<=59; $i++) {
				                    echo '<option value="'.$i.'"'. ($et_mh[1] == $i ? 'selected="selected"' : '') .'>';
				                    echo $i . ($i >1 ?' minutes':' minute');
				                    echo '</option>';
			                    }
			                    ?>
                            </select>
                            <input type="hidden" name="estimation_time" id="estimationTime" value="<?php echo $estimateTime; ?>">
                        </div>
                    </div>
                    <p><?php _e( 'The estimation for the task.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title">Start Date</h2>
                    <div class="input">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <input class="datepicker" type="text" name="start_date" size="30"
                               value="<?php echo $cTask->start_date; ?>" spellcheck="true" autocomplete="off">
                    </div>
                    <p><?php _e( 'The start date for the task.', 'task-manager' ); ?></p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-input">
                    <h2 class="title"><?php _e( 'Due Date', 'task-manager' ); ?></h2>
                    <div class="input">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <input class="datepicker" type="text" name="due_date" size="30"
                               value="<?php echo $cTask->due_date; ?>" spellcheck="true" autocomplete="off">
                    </div>
                    <p><?php _e( 'The due date for the task.', 'task-manager' ); ?></p>
                </div>
            </div>
        </div>
        <div class="clr"></div>

        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <h2 class="title"><?php _e( 'Task Progress', 'task-manager' ); ?></h2>
            <div class="task_progress edit">
                <div class="ui_slider">
                    <div id="custom-handle" class="ui-slider-handle"></div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task_progress" id="task_progress_value" value="<?php echo $cTask->progress; ?>">
        <div class="clr"></div>

        <div class="wrapper p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24">
                <div class="details">
                    <h2 class="title"><?php _e( 'Task Details', 'task-manager' ); ?></h2>
                    <?php wp_editor($cTask->description, 'taskDetails', array('media_buttons' => true, 'editor_height' => 220, 'textarea_rows' => 10,)); ?>
                </div>
            </div>


            <div class="form-wrap p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="assign">


                    <h2 class="title">Assign to</h2>
                    <div class="user-groups">
                        <?php foreach ($user_groups as $group): ?>
                        <div class="group-item">
                            <input type="checkbox" name="user_group[]"
                                   value="<?php echo $group->id; ?>" <?php if (is_array($cTaskGroups) && in_array($group->id, $cTaskGroups)) {
                                echo 'checked';
                            } ?>> <?php echo $group->name; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="clr"></div>
                    <hr>
                    <div class="form-input">
                        <div class="input">
                            <span class="dashicons dashicons-search"></span>
                            <input type="text" size="30" value="" id="userSearch"
                                   placeholder="<?php _e( 'Search for specific users..', 'task-manager' ); ?>"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div id="userList" style="display: none;">
                        <ul>
                            <?php foreach ($tmp_users as $user): ?>
                                <li><a href="javascript:void(0)" data-id="<?php echo $user->id; ?>"
                                       data-name="<?php echo $user->name; ?>"><?php echo $user->name; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <p><?php _e( 'To assign specific user.', 'task-manager' ); ?></p>
                    <div class="clr"></div>
                    <div id="userListAdded">
                        <ul>
                            <?php if (is_array($cTaskUsers)) {
                                foreach ($cTaskUsers as $user): ?>
                                    <li><span class="tab"
                                              data-id="<?php echo $user->id; ?>"><?php echo $user->name; ?></span>
                                        <i class="close"></i>
                                    </li>
                                <?php endforeach;
                            } ?>
                        </ul>
                    </div>
                    <input type="hidden" id="finalUserListHere" name="team_members">
                </div>

            </div>
        </div>
        <div class="clr"></div>

        <hr>

        <div class="form-input">
            <div id="submit-action">
                <span class="spinner"></span>
                <input type="submit" name="publish" id="publish" class="button button-primary button-large"
                       value="<?php _e( 'Save', 'task-manager' ); ?>">
            </div>
        </div>


    </form>

</div>
<!----------------------------------------------------
------------------ Page Specific Script --------------
----------------------------------------------------->
<script>
    jQuery('#wpcontent').css('background', '#ffffff');
    jQuery(document).ready(function () {
        jQuery('.tmp .datepicker').datepicker({
            dateFormat: "yy-mm-dd"
        });

        var handle = jQuery("#custom-handle");
        var taskProgress = jQuery("#task_progress_value").val();
        jQuery('.tmp .ui_slider').slider({
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
                jQuery('#task_progress_value').val(ui.value);
            }
        });
        handle.text(handle.text() + '%');

        var esmSel = jQuery("#estimateMinute");
        var eshSel = jQuery("#estimateHour");

        eshSel.on('change', function (){
            jQuery('#estimationTime').val(jQuery(this).val()+':' + esmSel.val());
        })

        esmSel.on('change', function (){
            jQuery('#estimationTime').val(eshSel.val() + ':' + jQuery(this).val());
        })
    });


</script>
