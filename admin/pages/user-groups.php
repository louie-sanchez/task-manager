<?php


/**----------------------------------------------------------------------------
 * ----------------------- New Project Insertion Query -----------------------
 * ----------------------------------------------------------------------------
 */

//This is used only if making any database queries
global $wpdb;

if (isset($_POST) && isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['project_access_id']) && !empty($_POST['project_access_id']) && isset($_POST['task_access_id']) && !empty($_POST['task_access_id'])) {

    if (!empty($_POST['name'])) {
        $name = $_POST['name'];
    }

    if (!empty($_POST['project_access_id'])) {
        $project_access = $_POST['project_access_id'];
    }

    if (!empty($_POST['task_access_id'])) {
        $task_access = $_POST['task_access_id'];
    }


    if ($name && $project_access && $task_access) {

        $user_group_table = $wpdb->prefix . "tmp_user_groups";

        /**
         * New Project Queries
         */
        $insert_user_group = $wpdb->insert($user_group_table, array('id' => null, 'name' => $name, 'project_access_id' => $project_access, 'task_access_id' => $task_access));

        if ($insert_user_group) {
            echo '';
        }
    }
}

/*--------- END - New Project Insertion Query -----------------------*/

/*---------------------------------------------------------
-------- Execute Query To Delete Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'delete') && $_POST['group_id'] && !empty($_POST['group_id'])) {
    if ($_POST['_token'] = date('H:i')) {
        $table = "{$wpdb->prefix}tmp_user_groups";
        $wpdb->delete($table, array('id' => $_POST['group_id']));
    }
}

/*---------------- End Executed Query ------------------------*/

/*---------------------------------------------------------
-------- Execute Query To Modify Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'edit') && isset($_POST['group_id']) && !empty($_POST['group_id']) && isset($_POST['group_name']) && !empty($_POST['group_name']) && isset($_POST['group_project']) && !empty($_POST['group_project']) && isset($_POST['group_task']) && !empty($_POST['group_task'])) {
    $table = "{$wpdb->prefix}tmp_user_groups";
    $wpdb->update($table, array('name' => $_POST['group_name'], 'project_access_id' => $_POST['group_project'], 'task_access_id' => $_POST['group_task']), array('id' => $_POST['group_id']));
}

/*---------------- End Executed Query ------------------------*/


/**----------------------------------------------------------------------------
 * ----------------------- Get All User Access Type  -----------------------
 * ----------------------------------------------------------------------------
 */

$user_access_type = $wpdb->get_results("
        SELECT {$wpdb->prefix}tmp_user_access_types.id, {$wpdb->prefix}tmp_user_access_types.name
        FROM {$wpdb->prefix}tmp_user_access_types
         ");


/*---------------- End * Get All User Access Type ------------------------*/

/*---------------------------------------------------------------------
        Project Category List Table
/*-------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/user-group-table-class.php';

/**
 * Category Table Here
 */

//Create an instance of our package class...
$categoryTableData = new Task_Manager_Table();

//Fetch, prepare, sort, and filter our data...
$categoryTableData->prepare_items();


?>


<div class="wrap nosubsub tmp">
    <h1><?php _e( 'User Groups', 'task-manager' ); ?></h1>

    <div id="ajax-response"></div>


    <div id="col-container" class="wp-clearfix">

        <div id="col-left">
            <div class="col-wrap">


                <div class="form-wrap">
                    <h2><?php _e( 'Add New User Group', 'task-manager' ); ?></h2>
                    <form id="addtag" method="post" action="" class="validate">
                        <div class="form-field form-required term-name-wrap">
                            <label for="group-name"><?php _e( 'Name', 'task-manager' ); ?></label>
                            <input name="name" id="group-name" type="text" value="" size="40" aria-required="true">
                            <p><?php _e( 'The access type is how it appears on the user list.', 'task-manager' ); ?></p>
                        </div>
                        <label class="control-label"><?php _e( 'Project Access', 'task-manager' ); ?></label>
                        <select class="form-control" name="project_access_id">
                            <?php foreach ($user_access_type as $uat): ?>
                                <option value="<?php echo $uat->id; ?>"><?php echo $uat->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p><?php _e( 'The Project access type is how it appears on the user list.', 'task-manager' ); ?></p>
                        <label class="control-label"><?php _e( 'Task Access', 'task-manager' ); ?></label>
                        <select class="form-control" name="task_access_id">
                            <?php foreach ($user_access_type as $uat): ?>
                                <option value="<?php echo $uat->id; ?>"><?php echo $uat->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p><?php _e( 'The Task access type is how it appears on the user list.', 'task-manager' ); ?></p>

                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                                 value="<?php _e( 'Add New User Group', 'task-manager' ); ?>"></p></form>
                </div>

            </div>
        </div><!-- /col-left -->

        <div id="col-right">
            <div class="col-wrap">


                <!-- Category Table Here -->

                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                <form id="tasks-filter" method="get">
                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                    <!-- Now we can render the completed list table -->
                    <?php $categoryTableData->display() ?>
                </form>

                <!-- Category Table Here -->

            </div>
        </div><!-- /col-right -->

    </div><!-- /col-container -->

    <div id="edit-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <div class="title"><?php _e( 'Edit User Group', 'task-manager' ); ?></div>
            <div class="form-input">
                <label id="name" class="control-label">
                    <?php _e( 'Access Name', 'task-manager' ); ?>
                </label>
                <input type="text" placeholder="Process something" name="access_name" id="name" value=""/>
            </div>
            <label class="control-label">Project Access</label>
            <select id="project" class="form-control">
                <?php foreach ($user_access_type as $uat): ?>
                    <option value="<?php echo $uat->id; ?>"><?php echo $uat->name; ?></option>
                <?php endforeach; ?>
            </select>
            <label class="control-label">Task Access</label>
            <select id="task" class="form-control">
                <?php foreach ($user_access_type as $uat): ?>
                    <option value="<?php echo $uat->id; ?>"><?php echo $uat->name; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <p class="submit"><input type="submit" name="submit" id="submit" onclick="submitEditAction()"
                                     class="button button-primary" value="<?php _e( 'Modify User Group', 'task-manager' ); ?>"></p>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="group_id" name="group_id">
        <input type="hidden" name="action" value="delete">
    </form>

    <!-----------------------------------------------------
        -------------- Hidden Edit Form For Post Action -----------
        ------------------------------------------------------->
    <form id="action-edit-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" id="group_edit_id" name="group_id">
        <input type="hidden" id="group_name" name="group_name">
        <input type="hidden" id="group_project" name="group_project">
        <input type="hidden" id="group_task" name="group_task">
        <input type="hidden" name="action" value="edit">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation"><?php _e( 'You are about to delete the group. Are you sure ?', 'task-manager' ); ?></p>
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

    editTargetData = function (id, param) {
        event.preventDefault();
        var name = jQuery(param).attr('data-name');
        var project_access_id = jQuery(param).attr('data-project');
        var task_access_id = jQuery(param).attr('data-task');
        jQuery('#edit-popup #name').val(name);
        jQuery('#edit-popup #project').val(project_access_id);
        jQuery('#edit-popup #task').val(task_access_id);

        // Set hiddent edit submission form values
        jQuery('#group_edit_id').val(id);
        jQuery('#group_name').val(name);
        jQuery('#group_project').val(project_access_id);
        jQuery('#group_task').val(task_access_id);

        // Hide Popup
        jQuery('#edit-popup').addClass('is-visible');
    }

    jQuery("#edit-popup input#name").on("change paste keyup", function () {
        jQuery('#group_name').val(jQuery(this).val());
    });

    jQuery('#edit-popup #project').on('change', function () {
        jQuery('#group_project').val(jQuery(this).val());
    })

    jQuery('#edit-popup #task').on('change', function () {
        jQuery('#group_task').val(jQuery(this).val());
    })

    submitEditAction = function () {
        event.preventDefault();
        jQuery('.cd-popup').removeClass('is-visible');
        jQuery('#action-edit-form').submit();
    }
    /*------------- End - Edit Action -------------*/


    /*---------------------------------------------
     ----------------- Delete Action ---------------
     ----------------------------------------------*/
    deleteTargetData = function (id) {
        event.preventDefault();
        jQuery('#group_id').val(id);
        jQuery('#delete-popup').addClass('is-visible');
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
