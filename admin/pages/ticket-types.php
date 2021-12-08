<?php


/**----------------------------------------------------------------------------
 * ----------------------- New Project Insertion Query -----------------------
 * ----------------------------------------------------------------------------
 */

//This is used only if making any database queries
global $wpdb;

if (isset($_POST['action']) && $_POST['action'] == 'add_type' && isset($_POST['type_name']) && !empty($_POST['type_name'])) {

    $name = $_POST['type_name'];


    if (isset($name) && !empty($name)) {

        $ticket_type_table_name = $wpdb->prefix . "tmp_ticket_types";

        /**
         * New Type Queries
         */
        $insert_type = $wpdb->insert($ticket_type_table_name, array('name' => $name));

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
        $table = "{$wpdb->prefix}tmp_ticket_types";
        $wpdb->delete($table, array('id' => $_POST['type_id']));
    }
}
/*---------------- End Executed Delete Query ------------------------*/


/*---------------------------------------------------------
-------- Execute Query To Modify Project Category
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'edit') && isset($_POST['edit_type_id']) && !empty($_POST['edit_type_id']) && isset($_POST['edit_type_name']) && !empty($_POST['edit_type_name'])) {
    $table = "{$wpdb->prefix}tmp_ticket_types";
    $edit_type_id = $_POST['edit_type_id'];
    $wpdb->update($table, array('name' => $_POST['edit_type_name']), array('id' => $_POST['edit_type_id']));
}

/*---------------- End Executed Query ------------------------*/


/*---------------------------------------------------------------------
        Project Category List Table
/*-------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/ticket-type-table-class.php';

/**
 * Category Table Here
 */

//Create an instance of our package class...
$ticketTableData = new Task_Manager_Table();

//Fetch, prepare, sort, and filter our data...
$ticketTableData->prepare_items();


?>


<div class="wrap nosubsub tmp">
    <h1>Ticket Types</h1>

    <div id="ajax-response"></div>


    <div id="col-container" class="wp-clearfix">

        <div id="col-left">
            <div class="col-wrap">


                <div class="form-wrap">
                    <h2>Add New Type</h2>
                    <form id="addtag" method="post" action="" class="validate">
                        <input type="hidden" name="action" value="add_type">
                        <div class="form-field form-required term-name-wrap">
                            <label for="type-name">Name</label>
                            <input name="type_name" id="type-name" type="text" value="" size="40" aria-required="true">
                            <p>The name is how it appears on your ticket.</p>
                        </div>

                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                                 value="Add New Type"></p></form>
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
                    <?php $ticketTableData->display() ?>
                </form>

                <!-- Category Table Here -->

                <div class="form-wrap edit-term-notes">
                    <p>
                        <strong>Note:</strong><br>Deleting a ticket type can effect on tickets.</p>
                </div>

            </div>
        </div><!-- /col-right -->

    </div><!-- /col-container -->
    <div id="edit-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <div class="title">Edit Ticket Type</div>

            <div class="form-input">
                <label id="type_name">
                    Type Name
                </label>
                <input type="text" placeholder="Type Name" id="type_name" value=""/>
            </div>

            <br>
            <p class="submit"><input type="submit" name="submit" id="submit" onclick="submitEditAction()"
                                     class="button button-primary" value="Modify Task Type"></p>
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
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation">You are about to delete the task type. Are you sure ?</p>
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
        jQuery('#edit-popup #type_name').val(name);

        // Set hiddent edit submission form values
        jQuery('#edit_type_id').val(id);
        jQuery('#edit_type_name').val(name);

        // Hide Popup
        jQuery('#edit-popup').addClass('is-visible');
    }


    jQuery("#edit-popup #type_name").on("change paste keyup", function () {
        jQuery('#edit_type_name').val(jQuery(this).val());
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
