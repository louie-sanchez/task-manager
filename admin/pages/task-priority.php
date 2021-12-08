<?php


/**----------------------------------------------------------------------------
 * ----------------------- New Project Insertion Query -----------------------
 * ----------------------------------------------------------------------------
 */

//This is used only if making any database queries
global $wpdb;

if (isset($_POST['action']) && $_POST['action'] == 'add_priority' && isset($_POST['priority_name']) && !empty($_POST['priority_name'])) {

    $name = $_POST['priority_name'];


    if (isset($_POST['priority_icon']) && !empty($_POST['priority_icon'])) {

        $icon = $_POST['priority_icon'];

    } else {
        $icon = null;
    }


    if (!empty($name)) {

        $task_priority_table_name = $wpdb->prefix . "tmp_task_priorities";

        /**
         * New Priority Queries
         */
        $insert_type = $wpdb->insert($task_priority_table_name, array('id' => null, 'name' => $name, 'icon' => $icon));

        if ($insert_type) {
            echo '';
        }
    }
}

/*--------- END - New Project Insertion Query -----------------------*/

/*---------------------------------------------------------
-------- Execute Query To Delete Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'delete') && $_POST['priority_id'] && !empty($_POST['priority_id'])) {
    if ($_POST['_token'] = date('H:i')) {
        $table = "{$wpdb->prefix}tmp_task_priorities";
        $wpdb->delete($table, array('id' => $_POST['priority_id']));
    }
}
/*---------------- End Executed Delete Query ------------------------*/


/*---------------------------------------------------------
-------- Execute Query To Modify Project Category
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'edit') && isset($_POST['edit_p_id']) && !empty($_POST['edit_p_id']) && isset($_POST['edit_p_name']) && !empty($_POST['edit_p_name'])) {
    $table = "{$wpdb->prefix}tmp_task_priorities";

    if (isset($_POST['edit_p_icon']) && !empty($_POST['edit_p_icon'])) {
        $icon = $_POST['edit_p_icon'];
    } else {
        $icon = null;
    }

    $wpdb->update($table, array('name' => $_POST['edit_p_name'], 'icon' => $icon), array('id' => $_POST['edit_p_id']));
}

/*---------------- End Executed Query ------------------------*/


/*----------------------------------------------------------------
------------------------ Task Priority Icons ----------------------
------------------------------------------------------------------*/
$task_priority_icons = array('icon_1.png', 'icon_2.png', 'icon_3.png', 'icon_4.png', 'icon_5.png', 'icon_6.png', 'icon_7.png', 'icon_8.png'
, 'icon_9.png', 'icon_10.png', 'icon_11.png', 'icon_12.png', 'icon_13.png', 'icon_14.png', 'icon_15.png', 'icon_16.png');
$icon_dir_url = plugins_url('../images/icons/', __FILE__);

/* --------------End --------------- Task Priority Icon-------------*/

/*---------------------------------------------------------------------
        Project Category List Table
/*-------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/task-priority-table-class.php';

/**
 * Category Table Here
 */

//Create an instance of our package class...
$taskTableData = new Task_Manager_Table();

//Fetch, prepare, sort, and filter our data...
$taskTableData->prepare_items();


?>


<div class="wrap nosubsub tmp">
    <h1><?php _e( 'Task Priorities', 'task-manager' ); ?></h1>

    <div id="ajax-response"></div>


    <div id="col-container" class="wp-clearfix">

        <div id="col-left">
            <div class="col-wrap">


                <div class="form-wrap">
                    <h2><?php _e( 'Add New Priority', 'task-manager' ); ?></h2>
                    <form id="addtag" method="post" action="" class="validate">
                        <input type="hidden" name="action" value="add_priority">
                        <div class="form-field form-required term-name-wrap">
                            <label for="priority-name"><?php _e( 'Name', 'task-manager' ); ?></label>
                            <input name="priority_name" id="priority-name" type="text" value="" size="40"
                                   aria-required="true">
                            <p><?php _e( 'The name is how it appears on your task.', 'task-manager' ); ?></p>
                        </div>


                        <label class="control-label"><?php _e( 'Task Priority Icon', 'task-manager' ); ?></label>
                        <select id="icon" class="form-control" name="priority_icon">
                            <?php foreach ($task_priority_icons as $icon): ?>
                                <option value="<?php echo $icon; ?>"> <?php echo ucfirst($icon); ?></option>
                            <?php endforeach; ?>
                        </select>


<!--                        <label for="priority-name">--><?php //_e( 'Icon Preview', 'task-manager' ); ?><!--</label>-->
                        <span class="icon-preview"><img src="<?php echo $icon_dir_url . $task_priority_icons[0]; ?>"
                                                        alt=""/></span>

                        <hr>
                        <div class="clr"></div>
                        <br>
                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                                 value="<?php _e( 'Add New Priority', 'task-manager' ); ?>"></p>
                    </form>
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
                    <?php $taskTableData->display() ?>
                </form>

                <!-- Category Table Here -->

                <div class="form-wrap edit-term-notes">
                    <p>
                        <strong><?php _e( 'Note:', 'task-manager' ); ?></strong><br><?php _e( 'Deleting a task priority can be effect on tasks.', 'task-manager' ); ?></p>
                </div>

            </div>
        </div><!-- /col-right -->

    </div><!-- /col-container -->
    <div id="edit-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <div class="title"><?php _e( 'Edit Task Priority', 'task-manager' ); ?></div>

            <div class="form-input">
                <label id="priority_name">
                   <?php _e( 'Priority Name', 'task-manager' ); ?>
                </label>
                <input type="text" placeholder="<?php _e( 'Type Name', 'task-manager' ); ?>" id="priority_name" value=""/>
            </div>

            <label class="control-label"><?php _e( 'Task Priority Icon', 'task-manager' ); ?></label>
            <select id="priority_icon" class="form-control">
                <?php foreach ($task_priority_icons as $icon): ?>
                    <option value="<?php echo $icon; ?>"> <?php echo ucfirst($icon); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="priority-name"><?php _e( 'Icon Preview', 'task-manager' ); ?></label>
            <span class="edit_icon_preview"><img src="" alt=""/></span>
            <hr>
            <p class="submit"><input type="submit" name="submit" id="submit" onclick="submitEditAction()"
                                     class="button button-primary" value="<?php _e( 'Modify Icon Priority', 'task-manager' ); ?>"></p>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="delete_priority_id" name="priority_id">
        <input type="hidden" name="action" value="delete">
    </form>

    <!-----------------------------------------------------
        -------------- Hidden Edit Form For Post Action -----------
        ------------------------------------------------------->
    <form id="action-edit-form" method="POST" action="" autocomplete="off" style="display: none;">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="edit_p_id" name="edit_p_id">
        <input type="hidden" id="edit_p_name" name="edit_p_name">
        <input type="hidden" id="edit_p_icon" name="edit_p_icon">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation">You are about to delete the task priority. Are you sure ?</p>
            <ul class="cd-buttons">
                <li><a href="javascript:void(0);" onclick="deleteTargetConfirm()">Yes</a></li>
                <li><a href="javascript:void(0);" class="closeDialogue">No</a></li>
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
        var icon = jQuery(param).attr('data-icon');
        jQuery('#edit-popup #priority_name').val(name);

        if (icon.length) {
            var iconDir = "<?php echo $icon_dir_url; ?>";
            var iconFullLink = iconDir + icon;
            jQuery('.edit_icon_preview > img').attr('src', iconFullLink);
        }


        // Set hiddent edit submission form values
        jQuery('#edit_p_id').val(id);
        jQuery('#edit_p_name').val(name);
        jQuery('#edit_p_icon').val(icon);

        // Hide Popup
        jQuery('#edit-popup').addClass('is-visible');
    }


    jQuery("#edit-popup #priority_name").on("change paste keyup", function () {
        jQuery('#edit_p_name').val(jQuery(this).val());
    });

    jQuery("#edit-popup #priority_icon").on("change", function () {
        jQuery('#edit_p_icon').val(jQuery(this).val());
        var iconName = jQuery(this).val();
        var iconDir = "<?php echo $icon_dir_url; ?>";
        var iconFullLink = iconDir + iconName;
        jQuery('.edit_icon_preview > img').attr('src', iconFullLink);
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
        jQuery('#action-delete-form #delete_priority_id').val(id);
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


    /*------------- On Icon Change ------------------*/
    jQuery('#icon').on('change', function (event) {
        var iconName = jQuery(this).val();
        var iconDir = "<?php echo $icon_dir_url; ?>";
        var iconFullLink = iconDir + iconName;
        jQuery('.icon-preview > img').attr('src', iconFullLink);
    });

</script>