<?php


/**----------------------------------------------------------------------------
 * ----------------------- New Project Insertion Query -----------------------
 * ----------------------------------------------------------------------------
 */

//This is used only if making any database queries
global $wpdb;

if (isset($_POST) && isset($_POST['name'])) {

    if (isset($_POST['name'])) {
        $name = $_POST['name'];
    }


    if (isset($name) && !empty($name)) {

        $access_type_table_name = $wpdb->prefix . "tmp_user_access_types";

        /**
         * New Project Queries
         */
        $insert_access_type = $wpdb->insert($access_type_table_name, array('id' => null, 'name' => $name));

        if ($insert_access_type) {
            echo '';
        }
    }
}

/*---------------------------------------------------------
-------- Execute Query To Delete Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'delete') && $_POST['access_type'] && !empty($_POST['access_type'])) {
    if ($_POST['_token'] = date('H:i')) {
        $table = "{$wpdb->prefix}tmp_user_access_types";
        $wpdb->delete($table, array('id' => $_POST['access_type']));
    }
}
/*---------------- End Executed Query ------------------------*/

/*---------------------------------------------------------
-------- Execute Query To Modify Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'edit') && $_POST['access_id'] && !empty($_POST['access_id']) && $_POST['access_name'] && !empty($_POST['access_name'])) {
    $table = "{$wpdb->prefix}tmp_user_access_types";
    $wpdb->update($table, array('name' => $_POST['access_name']), array('id' => $_POST['access_id']));
}

/*---------------- End Executed Query ------------------------*/


/*--------- END - New Project Insertion Query -----------------------*/


/*---------------------------------------------------------------------
        Project Category List Table
/*-------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/user-access-type-table-class.php';

/**
 * Category Table Here
 */

//Create an instance of our package class...
$categoryTableData = new Task_Manager_Table();

//Fetch, prepare, sort, and filter our data...
$categoryTableData->prepare_items();


?>


<div class="wrap nosubsub tmp">
    <h2><?php _e( 'User Access Types', 'task-manager' ); ?></h2>

    <div id="ajax-response"></div>

    <p>
        <?php _e( 'You can modify title as your way but don\'t change the topics for as per
        access. So you could no be track which user group as which access.', 'task-manager' ); ?></p>

    <div id="col-container" class="wp-clearfix">

        <div id="user-access-type">
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
            <div class="title"><?php _e( 'Edit Access Type', 'task-manager' ); ?></div>
            <br>
            <div class="form-input">
                <label id="name">
                </label>
                <input type="text" placeholder="<?php _e( 'Name', 'task-manager' ); ?>" name="access_name" id="name" value=""/>
            </div>
            <p class="submit"><input type="submit" name="submit" id="submit" onclick="submitEditAction()"
                                     class="button button-primary" value="<?php _e( 'Modify Access Type', 'task-manager' ); ?>"></p>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="access_type" name="access_type">
        <input type="hidden" name="action" value="delete">
    </form>

    <!-----------------------------------------------------
        -------------- Hidden Edit Form For Post Action -----------
        ------------------------------------------------------->
    <form id="action-edit-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="access_id" name="access_id">
        <input type="hidden" id="access_name" name="access_name">
        <input type="hidden" name="action" value="edit">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation"><?php _e( 'You are about to delete the Access type. Are you sure ?', 'task-manager' ); ?></p>
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
        jQuery('#edit-popup #name').val(name);
        jQuery('#access_id').val(id);
        jQuery('#edit-popup').addClass('is-visible');
    }

    jQuery("#edit-popup input#name").on("change paste keyup", function () {
        jQuery('#access_name').val(jQuery(this).val());
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
        jQuery('#access_type').val(id);
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

