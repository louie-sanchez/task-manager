<?php


/**----------------------------------------------------------------------------
 * ----------------------- New Task Insertion Query -----------------------
 * ----------------------------------------------------------------------------
 */

global $wpdb;

if (isset($_POST['action']) && $_POST['action'] == 'add_status' && isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['group']) && !empty($_POST['group'])) {


    $name = $_POST['name'];
    $group = $_POST['group'];


    if (!empty($name) && !empty($group)) {

        $task_status_table_name = $wpdb->prefix . "tmp_task_status";

        /**
         * New Task Queries
         */
        $insert_task_status = $wpdb->insert($task_status_table_name, array('id' => null, 'name' => $name, 'group_name' => $group));

        if ($insert_task_status) {
            echo '';
        }
    }
}

/*--------- END - New Task Insertion Query -----------------------*/

/*---------------- Status Group Query -----------------------------*/
$task_group = array('open', 'done', 'close');
/*---------------- End - Status Group Query -----------------------------*/

/*---------------------------------------------------------
-------- Execute Query To Modify Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'edit') && $_POST['edit_status_id'] && !empty($_POST['edit_status_id']) && $_POST['edit_status_name'] && !empty($_POST['edit_status_name']) && $_POST['edit_status_group'] && !empty($_POST['edit_status_group'])) {

    $table = "{$wpdb->prefix}tmp_task_status";
    $wpdb->update($table, array('name' => $_POST['edit_status_name'], 'group_name' => $_POST['edit_status_group']), array('id' => $_POST['edit_status_id']));
}
/*---------------- End Executed Query ------------------------*/

/*---------------------------------------------------------
-------- Execute Query To Delete Access Type
------------------------------------------------------------*/
if (isset($_POST['action']) && $_POST['action'] == 'delete_status' && $_POST['status_id'] && !empty($_POST['status_id'])) {
    if ($_POST['_token'] = date('H:i')) {
        $table = "{$wpdb->prefix}tmp_task_status";
        $wpdb->delete($table, array('id' => $_POST['status_id']));
    }
}
/*---------------- End Executed Query ------------------------*/


/*---------------------------------------------------------------------
        Project Category List Table
/*-------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/task-status-list-table-class.php';

/**
 * Category Table Here
 */

//Create an instance of our package class...
$categoryTableData = new Task_Manager_Table();

//Fetch, prepare, sort, and filter our data...
$categoryTableData->prepare_items();


?>


<div class="wrap nosubsub tmp">
    <h1><?php _e( 'Task Status', 'task-manager' ); ?></h1>

    <div id="ajax-response"></div>


    <div id="col-container" class="wp-clearfix">

        <div id="col-left">
            <div class="col-wrap">


                <div class="form-wrap">
                    <h2><?php _e( 'Add New Task Status', 'task-manager' ); ?></h2>
                    <form id="addtag" method="post" action="" class="validate">
                        <input type="hidden" name="action" value="add_status">
                        <div class="form-field form-required term-name-wrap">
                            <label for="status-name"><?php _e( 'Name', 'task-manager' ); ?></label>
                            <input name="name" id="status-name" type="text" value="" size="40" aria-required="true">
                            <p><?php _e( 'The name is how it appears on your task.', 'task-manager' ); ?></p>
                        </div>
                        <label class="control-label">Status Group</label>
                        <select class="form-control" name="group">
                            <?php foreach ($task_group as $tg): ?>
                                <option value="<?php echo $tg; ?>">
                                    <?php esc_html_e(ucfirst($tg), 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p><?php _e( 'The project status group is how it appears on the task.', 'task-manager' ); ?></p>


                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                                 value="<?php _e( 'Add New Status', 'task-manager' ); ?>"></p></form>
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

                <div class="form-wrap edit-term-notes">
                    <p>
                        <strong><?php _e( 'Note:', 'task-manager' ); ?></strong><br><?php _e( 'Deleting a task status can be effect on tasks..', 'task-manager' ); ?></p>
                </div>

            </div>
        </div><!-- /col-right -->

    </div><!-- /col-container -->
    <div id="edit-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <div class="title"><?php _e( 'Edit Status', 'task-manager' ); ?></div>

            <div class="form-input">
                <label for="status_name">
                    <?php _e( 'Name', 'task-manager' ); ?>
                </label>
                <input type="text" placeholder="<?php _e( 'Status Name', 'task-manager' ); ?>" id="status_name" value=""/>
            </div>

            <label class="control-label"><?php _e( 'Status Group', 'task-manager' ); ?></label>
            <select id="status_group" class="form-control">
                <?php foreach ($task_group as $tg): ?>
                    <option value="<?php echo $tg; ?>"><?php echo ucfirst($tg); ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <p class="submit"><input type="submit" name="submit" id="submit" onclick="submitEditAction()"
                                     class="button button-primary" value="<?php _e( 'Modify Task Status', 'task-manager' ); ?>"></p>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="status_id" name="status_id">
        <input type="hidden" name="action" value="delete_status">
    </form>

    <!-----------------------------------------------------
        -------------- Hidden Edit Form For Post Action -----------
        ------------------------------------------------------->
    <form id="action-edit-form" method="POST" action="" autocomplete="off" style="display: none;">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="edit_status_id" name="edit_status_id">
        <input type="hidden" id="edit_status_name" name="edit_status_name">
        <input type="hidden" id="edit_status_group" name="edit_status_group">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation"><?php _e( 'You are about to delete the task status. Are you sure ?', 'task-manager' ); ?></p>
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
        var group = jQuery(param).attr('data-status_group');
        jQuery('#edit-popup #status_name').val(name);
        jQuery('#edit-popup #status_group').val(group);


        // Set hiddent edit submission form values
        jQuery('#edit_status_id').val(id);
        jQuery('#edit_status_name').val(name);
        jQuery('#edit_status_group').val(group);

        // Hide Popup
        jQuery('#edit-popup').addClass('is-visible');
    }


    jQuery("#edit-popup #status_name").on("change paste keyup", function () {
        jQuery('#edit_status_name').val(jQuery(this).val());
    });

    jQuery("#edit-popup #status_group").on("change paste keyup", function () {
        jQuery('#edit_status_group').val(jQuery(this).val());
    });


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
        jQuery('#action-delete-form #status_id').val(id);
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

