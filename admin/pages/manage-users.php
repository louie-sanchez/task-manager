<?php


/**----------------------------------------------------------------------------
 * ----------------------- New Project Insertion Query -----------------------
 * ----------------------------------------------------------------------------
 */

//This is used only if making any database queries
global $wpdb;

if (isset($_POST) && isset($_POST['user_id']) && !empty($_POST['user_id']) && isset($_POST['group_id']) && !empty($_POST['group_id'])) {

//    echo "<pre>";
//    print_r($_POST['user_id']);
//    echo "</pre>";

    if (!empty($_POST['user_id'])) {
        $userIDs = $_POST['user_id'];
    }

    if (!empty($_POST['group_id'])) {
        $group_id = $_POST['group_id'];
    }


    if ($userIDs && $group_id) {

        $user_table = $wpdb->prefix . "tmp_users";

        /**
         * New Project Queries
         */
        if( is_array( $userIDs ) ){
            foreach ( $userIDs as $user_id ){
                $wpdb->insert($user_table, array('id' => null, 'user_id' => $user_id, 'user_group_id' => $group_id));
            }
        }else{
            $wpdb->insert($user_table, array('id' => null, 'user_id' => $userIDs, 'user_group_id' => $group_id));
        }




    }
}

/*--------- END - New Project Insertion Query -----------------------*/

/*---------------------------------------------------------
-------- Execute Query To Delete Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'delete') && $_POST['user_id'] && !empty($_POST['user_id'])) {
    if ($_POST['_token'] = date('H:i')) {
        $table = "{$wpdb->prefix}tmp_users";
        $wpdb->delete($table, array('id' => $_POST['user_id']));
    }
}

/*---------------- End Executed Query ------------------------*/

/*---------------------------------------------------------
-------- Execute Query To Modify User
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'edit') && isset($_POST['edit_user_id']) && !empty($_POST['edit_user_id']) && isset($_POST['edit_user_wp_id']) && !empty($_POST['edit_user_wp_id']) && isset($_POST['edit_user_group_id']) && !empty($_POST['edit_user_group_id'])) {
    $table = "{$wpdb->prefix}tmp_users";
    $wpdb->update($table, array('user_id' => $_POST['edit_user_wp_id'], 'user_group_id' => $_POST['edit_user_group_id']), array('id' => $_POST['edit_user_id']));
}

/*---------------- End Executed Query ------------------------*/


/**----------------------------------------------------------------------------
 * ----------------------- Get All User And User Group  -----------------------
 * ----------------------------------------------------------------------------
 */


// Get TMP Users
$tmp_users = $wpdb->get_results("
        SELECT usr.user_id
        FROM {$wpdb->prefix}tmp_users as usr
         ");
foreach ($tmp_users as $tmp_user) {
    $tmp_usersArr[] = $tmp_user->user_id;
}

// Get System Users
if(isset($tmp_usersArr) && is_array($tmp_usersArr)){
	$get_system_user_query = new WP_User_Query(array( 'fields' => array('ID', 'display_name'),
//                                                  'role__in' => array( 'administrator', 'editor', 'author', 'contributor' ),
                                                      'exclude' => $tmp_usersArr));
}else{
	$get_system_user_query = new WP_User_Query(array( 'fields' => array('ID', 'display_name')));
}

$wp_system_users = $get_system_user_query->get_results();



//Get All System Users
$get_system_user_query_for_all = new WP_User_Query(array('fields' => array('ID', 'display_name')));
$wp_system_users_for_all = $get_system_user_query_for_all->get_results();


// Get User Groups
$user_groups = $wpdb->get_results("
        SELECT ug.id, ug.name
        FROM {$wpdb->prefix}tmp_user_groups as ug
         ");


/*---------------- End * Get All User And User Group ------------------------*/

/*---------------------------------------------------------------------
        Project Category List Table
/*-------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/manage-user-table-class.php';

/**
 * Category Table Here
 */

//Create an instance of our package class...
$categoryTableData = new Task_Manager_Pro_Table();

//Fetch, prepare, sort, and filter our data...
$categoryTableData->prepare_items();

$get_tmp_multiple_user_assign = get_option('tmp_multiple_user_assign');

?>


<div class="wrap nosubsub tmp">
    <h1><?php _e( 'User Groups', 'task-manager-pro' ); ?></h1>

    <div id="ajax-response"></div>


    <div id="col-container" class="wp-clearfix">

        <div id="col-left">
            <div class="col-wrap">


                <div class="form-wrap">
                    <h2><?php _e( 'Add A User To Task Manager', 'task-manager-pro' ); ?></h2>
                    <form id="addtag" method="post" action="" class="validate">
                        <label class="control-label"><?php _e( 'Select User', 'task-manager-pro' ); ?></label>
                        <?php if($get_tmp_multiple_user_assign === 'yes'){  ?>
                        <select multiple class="form-control" name="user_id[]">
                            <?php foreach ($wp_system_users as $user): ?>
                                <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php  }else{ ?>
                            <select class="form-control" name="user_id">
                                <?php foreach ($wp_system_users as $user): ?>
                                    <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php } ?>
                        <p><?php _e( 'Select a user form system users.', 'task-manager-pro' ); ?></p>

                        <label class="control-label"><?php _e( 'Assign User to Group', 'task-manager-pro' ); ?></label>


                        <select class="form-control" name="group_id">
                            <?php foreach ($user_groups as $ug): ?>
                                <option value="<?php echo $ug->id; ?>"><?php echo $ug->name; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <p><?php _e( 'The Task access type is how it appears on the user list.', 'task-manager-pro' ); ?></p>

                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                                 value="<?php _e( 'Add User', 'task-manager-pro' ); ?>"></p></form>
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
            <div class="title"><?php _e( 'Edit User Group', 'task-manager-pro' ); ?></div>
            <br>
            <label class="control-label"><?php _e( 'Select User', 'task-manager-pro' ); ?></label>
            <select id="wp_user_id" class="form-control">
                <?php foreach ($wp_system_users_for_all as $user): ?>
                    <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                <?php endforeach; ?>
            </select>
            <label class="control-label"><?php _e( 'Assign User to Group', 'task-manager-pro' ); ?></label>
            <select id="data_user_group" class="form-control">
                <?php foreach ($user_groups as $ug): ?>
                    <option value="<?php echo $ug->id; ?>"><?php echo $ug->name; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <p class="submit"><input type="submit" name="submit" id="submit" onclick="submitEditAction()"
                                     class="button button-primary" value="<?php _e( 'Modify User Group', 'task-manager-pro' ); ?>"></p>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="delete_user_id" name="user_id">
        <input type="hidden" name="action" value="delete">
    </form>

    <!-----------------------------------------------------
        -------------- Hidden Edit Form For Post Action -----------
        ------------------------------------------------------->
    <form id="action-edit-form" method="POST" action="" autocomplete="off" style="display: none;">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="edit_user_id" name="edit_user_id">
        <input type="hidden" id="edit_user_wp_id" name="edit_user_wp_id">
        <input type="hidden" id="edit_user_group_id" name="edit_user_group_id">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation"><?php _e( 'Removing a user from group will remove access from from the task/project.<br> Are you sure ?', 'task-manager-pro' ); ?></p>
            <ul class="cd-buttons">
                <li><a href="javascript:void(0);" onclick="deleteTargetConfirm()"><?php _e( 'Yes', 'task-manager-pro' ); ?></a></li>
                <li><a href="javascript:void(0);" class="closeDialogue"><?php _e( 'No', 'task-manager-pro' ); ?></a></li>
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
        var wp_user_id = jQuery(param).attr('data-wp_user_id');
        var data_user_group = jQuery(param).attr('data-user_group');
        jQuery('#edit-popup #wp_user_id').val(wp_user_id);
        jQuery('#edit-popup #data_user_group').val(data_user_group);

        // Set hiddent edit submission form values
        jQuery('#edit_user_id').val(id);
        jQuery('#edit_user_wp_id').val(wp_user_id);
        jQuery('#edit_user_group_id').val(data_user_group);

        // Hide Popup
        jQuery('#edit-popup').addClass('is-visible');
    }


    jQuery('#edit-popup #wp_user_id').on('change', function () {
        jQuery('#edit_user_wp_id').val(jQuery(this).val());
    })

    jQuery('#edit-popup #data_user_group').on('change', function () {
        jQuery('#edit_user_group_id').val(jQuery(this).val());
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
        jQuery('#delete_user_id').val(id);
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


