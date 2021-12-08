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
if ($_GET['page'] == 'tickets' && isset($_POST['act']) && ($_POST['act'] == 'add_ticket' || $_POST['act'] == 'edit_ticket') && isset($_POST['name'])) {
    if (!empty($_POST['name'])) {
        $name = $_POST['name'];
    }

    if (isset($_POST['ticket_type_id'])) {
        $ticket_type_id = $_POST['ticket_type_id'];
    } else {
        $ticket_type_id = null;
    }

    if (isset($_POST['ticket_status_id'])) {
        $ticket_status_id = $_POST['ticket_status_id'];
    } else {
        $ticket_status_id = null;
    }

    if (isset($_POST['ticket_for'])) {
        $ticket_for = $_POST['ticket_for'];
    } else {
        $ticket_for = null;
    }

    if (isset($_POST['description'])) {
        $description = $_POST['description'];
    } else {
        $description = null;
    }

    if (isset($_POST['ticket_for'])) {
        $ticket_for = $_POST['ticket_for'];
    } else {
        $ticket_for = null;
    }


    // Project Table Name
    $ticket_table_name = $wpdb->prefix . "tmp_tickets";
    /**
     * Insert Project Queries
     */

	$currentTime = date('Y-m-d H:i:s');
    if ($_POST['act'] == 'add_ticket') {
        $wpdb->insert($ticket_table_name, array('id' => null, 'name' => $name, 'description' => $description,
            'ticket_type_id' => $ticket_type_id, 'ticket_status_id' => $ticket_status_id, 'ticket_for' => $ticket_for,
            'created_by' => $owner_id, 'last_update_by' => $owner_id, 'created_at' => $currentTime, 'updated_at' => $currentTime));
    }

    if ($_POST['act'] == 'edit_ticket') {
        $wpdb->update($ticket_table_name, array('name' => $name, 'description' => $description,
            'ticket_type_id' => $ticket_type_id, 'ticket_status_id' => $ticket_status_id, 'ticket_for' => $ticket_for,
            'last_update_by' => $owner_id, 'updated_at' => $currentTime), array('id' => $_POST['ticket_id']));

    }

}

/*--------------------------------------
-------- Execute Query To Delete Project
---------------------------------------*/
if ($_GET['page'] == 'tickets' && isset($_POST['act']) && $_POST['act'] == 'delete' && isset($_POST['ticket_id']) && !empty($_POST['ticket_id'])) {
    $ticket_id = $_POST['ticket_id'];
    $ticket_table = "{$wpdb->prefix}tmp_tickets";
    
    // Delete Ticket by id
    $wpdb->delete($ticket_table, array('id' => $ticket_id));
}

/*---------------- End Executed Query ----------------*/


//print_r($projectQueryData['total_users']);exit;

/*------------------------------------------ Project Table List Data Manage ------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/ticket-list-table-class.php';
require_once plugin_dir_path(__FILE__) . '../partials/ProjectsPageCustomQuery.php';

//Create an instance of our package class...
$ticketTableData = new Task_Manager_Table();
$projectQueryData = new ProjectsPageCustomQuery();
$projectQueryData = $projectQueryData->getProjectsTotalCount();
//Fetch, prepare, sort, and filter our data...
$ticketTableData->prepare_items();

// Get Total Counting Ticket
$open_ticket_count = $wpdb->get_results("SELECT COUNT(`id`) AS open FROM {$wpdb->prefix}tmp_tickets WHERE ticket_status_id = 2");
$total_open_ticket = $open_ticket_count[0]->open;

$close_ticket_count = $wpdb->get_results("SELECT COUNT(`id`) AS close FROM {$wpdb->prefix}tmp_tickets WHERE ticket_status_id = 4");
$total_close_ticket = $close_ticket_count[0]->close;

$total_ticket_count = $wpdb->get_results("SELECT COUNT(`id`) AS total FROM {$wpdb->prefix}tmp_tickets");
$total_ticket = $total_ticket_count[0]->total;

/*------------------------------------------ Project Table List Data Manage ------------------------------------*/


?>
<div class="wrap projects tmp">



        <div class="section">
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($total_open_ticket) {
                                echo $total_open_ticket;
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title">Open Tickets</span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-editor-spellcheck"></span>
                    </div>
                    <div class="footer-content">From <?php if ($total_ticket) {
                            echo $total_ticket;
                        } else {
                            echo 0;
                        } ?> total ticket
                    </div>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($total_close_ticket) {
                                echo $total_close_ticket;
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title">Closed Tickets</span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-hidden"></span>
                    </div>
                    <div class="footer-content">From <?php if ($total_ticket) {
                            echo $total_ticket;
                        } else {
                            echo 0;
                        } ?> total ticket
                    </div>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="cover task-info total">
                    <div class="left-content">
                        <span class="task-count"><?php if ($total_ticket) {
                                echo $total_ticket;
                            } else {
                                echo 0;
                            } ?></span>
                        <span class="count-title">Total Tickets</span>
                    </div>
                    <div class="right-content">
                        <span class="dashicons dashicons-admin-users"></span>
                    </div>
                    <div class="footer-content"><a href="?page=tasks">Go to Tasks <span
                                class="dashicons dashicons-migrate"></span></a></div>
                </div>
            </div>
        </div>


    <div class="clr"></div>

    <h1 class="wp-heading-inline">Tickets</h1>


        <a href="?page=ticket-new" class="page-title-action">Add New</a>
        <a href="admin-ajax.php?action=export_ticket_csv" class="page-title-action"><?php _e( 'Export CSV', 'task-manager' ); ?></a>
    

    <hr class="wp-header-end">

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="tasks-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <!-- Now we can render the completed list table -->
        <?php $ticketTableData->display() ?>
    </form>


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="ticket_id" name="ticket_id">
        <input type="hidden" name="act" value="delete">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation">You are about to delete the Project. All of the tasks associate with this project
                will
                be delete. Are you sure ?</p>
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
    // Change Background Color

    jQuery('#wpcontent').css('background', '#ffffff');
    deleteTargetData = function (id) {
        event.preventDefault();
        jQuery('#ticket_id').val(id);
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

