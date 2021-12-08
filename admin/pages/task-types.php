<?php


/**----------------------------------------------------------------------------
 * ----------------------- New Project Insertion Query -----------------------
 * ----------------------------------------------------------------------------
 */

//This is used only if making any database queries
global $wpdb;

if (isset($_POST['action']) && $_POST['action'] == 'add_type' && isset($_POST['type_name']) && !empty($_POST['type_name'])) {

    $name = $_POST['type_name'];


    if (isset($_POST['type_slug']) && !empty($_POST['type_slug'])) {

        $slug = $_POST['type_slug'];

    } else {
        $slug = strtolower($name);
        $slug = preg_replace('/\s+/', '-', $slug);
    }

    if (!empty($slug)) {
        $found_duplicate = $wpdb->get_results("
            SELECT tt.id
            FROM {$wpdb->prefix}tmp_task_types as tt
            WHERE tt.slug = '$slug'
         ");

        if (!empty($found_duplicate)) {
            $slug = $slug . '_' . rand(10, 100);
        }
    }

    if (isset($name) && !empty($name) && !empty($slug)) {

        $task_type_table_name = $wpdb->prefix . "tmp_task_types";

        /**
         * New Project Queries
         */
        $insert_type = $wpdb->insert($task_type_table_name, array('id' => null, 'name' => $name, 'slug' => $slug));

        if ($insert_type) {
            echo '';
        }
    }
}

/*--------- END - New Project Insertion Query -----------------------*/

/*---------------------------------------------------------
-------- Execute Query To Delete Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'delete') && $_POST['type_id'] && !empty($_POST['type_id'])) {
    if ($_POST['_token'] = date('H:i')) {
        $table = "{$wpdb->prefix}tmp_task_types";
        $wpdb->delete($table, array('id' => $_POST['type_id']));
    }
}
/*---------------- End Executed Delete Query ------------------------*/


/*---------------------------------------------------------
-------- Execute Query To Modify Project Category
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'edit') && isset($_POST['edit_type_id']) && !empty($_POST['edit_type_id']) && isset($_POST['edit_type_name']) && !empty($_POST['edit_type_name']) && isset($_POST['edit_type_slug']) && !empty($_POST['edit_type_slug'])) {
    $table = "{$wpdb->prefix}tmp_task_types";

    $slug = $_POST['edit_type_slug'];
    $edit_type_id = $_POST['edit_type_id'];
    if (!empty($slug)) {
        $found_duplicate = $wpdb->get_results("
            SELECT tt.id
            FROM {$wpdb->prefix}tmp_task_types as tt
            WHERE tt.slug = '$slug' AND tt.id != $edit_type_id
         ");

        if (!empty($found_duplicate)) {
            $slug = $slug . '_' . rand(10, 100);
        }
    }

    $wpdb->update($table, array('name' => $_POST['edit_type_name'], 'slug' => $slug), array('id' => $_POST['edit_type_id']));
}

/*---------------- End Executed Query ------------------------*/


/*---------------------------------------------------------------------
        Project Category List Table
/*-------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/task-type-table-class.php';

/**
 * Category Table Here
 */

//Create an instance of our package class...
$taskTableData = new Task_Manager_Table();

//Fetch, prepare, sort, and filter our data...
$taskTableData->prepare_items();


?>


<div class="wrap nosubsub tmp">
    <h1><?php _e( 'Task Types', 'task-manager' ); ?></h1>

    <div id="ajax-response"></div>


    <div id="col-container" class="wp-clearfix">

        <div id="col-left">
            <div class="col-wrap">


                <div class="form-wrap">
                    <h2><?php _e( 'Add Task Type', 'task-manager' ); ?></h2>
                    <form id="addtag" method="post" action="" class="validate">
                        <input type="hidden" name="action" value="add_type">
                        <div class="form-field form-required term-name-wrap">
                            <label for="type-name"><?php _e( 'Name', 'task-manager' ); ?></label>
                            <input name="type_name" id="type-name" type="text" value="" size="40" aria-required="true">
                            <p><?php _e( 'The name is how it appears on your task.', 'task-manager' ); ?></p>
                        </div>
                        <div class="form-field term-slug-wrap">
                            <label for="type-slug"><?php _e( 'Slug', 'task-manager' ); ?></label>
                            <input name="type_slug" id="type-slug" type="text" value="" size="40">
                            <p><?php _e( 'The “slug” is the URL-friendly version of the name. It is usually all lowercase and
                                contains only letters, numbers, and hyphens.', 'task-manager' ); ?></p>
                        </div>

                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                                 value="<?php _e( 'Add New Type', 'task-manager' ); ?>"></p></form>
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
                        <strong><?php _e( 'Note:', 'task-manager' ); ?></strong><br><?php _e( 'Deleting a task type can effect on tasks.', 'task-manager' ); ?></p>
                </div>

            </div>
        </div><!-- /col-right -->

    </div><!-- /col-container -->
    <div id="edit-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <div class="title"><?php _e( 'Edit Task Type', 'task-manager' ); ?></div>

            <div class="form-input">
                <label id="type_name">
                    <?php _e( 'Type Name', 'task-manager' ); ?>
                </label>
                <input type="text" placeholder="<?php _e( 'Type Name', 'task-manager' ); ?>" id="type_name" value=""/>
            </div>

            <div class="form-input">
                <label id="type_slug">
                    <?php _e( 'Type Slug', 'task-manager' ); ?>
                </label>
                <input type="text" placeholder="<?php _e( 'Type Slug', 'task-manager' ); ?>" id="type_slug" value=""/>
            </div>
            <br>
            <p class="submit"><input type="submit" name="submit" id="submit" onclick="submitEditAction()"
                                     class="button button-primary" value="<?php _e( 'Modify Task Type', 'task-manager' ); ?>"></p>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="delete_type_id" name="type_id">
        <input type="hidden" name="action" value="delete">
    </form>

    <!-----------------------------------------------------
        -------------- Hidden Edit Form For Post Action -----------
        ------------------------------------------------------->
    <form id="action-edit-form" method="POST" action="" autocomplete="off" style="display: none;">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="edit_type_id" name="edit_type_id">
        <input type="hidden" id="edit_type_name" name="edit_type_name">
        <input type="hidden" id="edit_type_slug" name="edit_type_slug">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation"><?php _e( 'You are about to delete the task type. Are you sure ?', 'task-manager' ); ?></p>
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
        var slug = jQuery(param).attr('data-slug');
        jQuery('#edit-popup #type_name').val(name);
        jQuery('#edit-popup #type_slug').val(slug);

        // Set hiddent edit submission form values
        jQuery('#edit_type_id').val(id);
        jQuery('#edit_type_name').val(name);
        jQuery('#edit_type_slug').val(slug);

        // Hide Popup
        jQuery('#edit-popup').addClass('is-visible');
    }


    jQuery("#edit-popup #type_name").on("change paste keyup", function () {
        jQuery('#edit_type_name').val(jQuery(this).val());
    });

    jQuery("#edit-popup #type_slug").on("change paste keyup", function () {
        jQuery('#edit_type_slug').val(jQuery(this).val());
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
        jQuery('#action-delete-form #delete_type_id').val(id);
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
