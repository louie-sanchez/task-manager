<?php


// To Get User Access Id
require_once plugin_dir_path( __FILE__ ) . '../partials/user-based-custom-query-class.php';
$userCustomQueryObject = new User_Based_Custom_Query();
$project_access = $userCustomQueryObject->getUserAccessId()['project'];


global $wpdb;

$owner_id = wp_get_current_user()->ID;

/*--------------------------------------------------
------------ Custom Debug Function ------------------
-----------------------------------------------------*/
//function pr( $arg ){
//    echo "<pre>";
//    print_r( $arg );
//    echo "</pre>";
//    exit;
//}

/*--------------------- Check Get Request Method -------------------*/
if ( isset($_REQUEST['ticket']) && !empty($_REQUEST['ticket']) ){
    $ticket = $_REQUEST['ticket'];
}else{
    $ticket = null;
}

/*------------------------------------------------------------------
--------------------- Query To get Project Data -------------------
-------------------------------------------------------------------*/
$get_ticket = $wpdb->get_results("
            SELECT tt.id, tt.name, tt.description,
            usr.display_name as ticket_for, usr2.display_name as last_updated_by, usr3.display_name as created_by, 
            tt.updated_at, ttt.name as category, ps.name as status
            FROM {$wpdb->prefix}tmp_tickets tt
            LEFT JOIN {$wpdb->prefix}tmp_ticket_types ttt ON tt.ticket_type_id = ttt.id 
            LEFT JOIN {$wpdb->prefix}tmp_project_status ps ON tt.ticket_status_id = ps.id 
            LEFT JOIN {$wpdb->prefix}users usr ON tt.ticket_for = usr.id 
            LEFT JOIN {$wpdb->prefix}users usr2 ON tt.last_update_by = usr2.id
            LEFT JOIN {$wpdb->prefix}users usr3 ON tt.created_by = usr3.id
            WHERE tt.id = $ticket
         ");
$ticket_details = $get_ticket[0];




?>

<div class="wrap tmp project_details">
    <div class="padding-20"></div>
    <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24 left">
        <div class="heading-title">
            <?php if (isset($ticket_details->name) && !empty($ticket_details->name)){ ?>
            <div class="title-image">
                <?php echo substr(ucwords($ticket_details->name), 0, 2); ?>
            </div>
            <?php } ?>
            <div class="heading">
                <?php if (isset($ticket_details->name) && !empty($ticket_details->name)){ ?>
                <h2><?php echo $ticket_details->name ; ?></h2>
                <?php } ?>
                <?php if (isset($ticket_details->category) && !empty($ticket_details->category)){ ?>
                <span><?php echo $ticket_details->category ; ?></span>
                <?php } ?>
            </div>
            <div class="edit-option">
                <?php if ( in_array($project_access, array(11, 12, 13, 16)) ) { ?>
                <div class="edit_project">
                    <a href="?page=ticket-edit&ticket=<?php echo $ticket; ?>" title="Edit Ticket">
                        <span class="dashicons dashicons-edit"></span>
                        <span class="title">Edit Ticket</span>
                    </a>

                </div>
                <?php } ?>
            </div>
        </div>
        <div class="clr"></div>
        <?php if (isset($ticket_details->description) && !empty($ticket_details->description)){ ?>
        <div class="details">
            <div class="title"><span>Details</span></div>
            <div class="description">
                <?php print htmlspecialchars_decode($ticket_details->description) ; ?>
            </div>
        </div>
        <div class="clr"></div>
        <?php } ?>
        <div class="project_others">
            <div class="p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24 left">
                <ul class="other-info">
                    <?php if($ticket_details->updated_at){ ?>
                    <li>
                        <label>Last Updated at</label>
                        <span><?php echo date("F j, Y", strtotime($ticket_details->updated_at)) ; ?></span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="clr"></div>
        <hr class="hr">
    </div>
    <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24 right">
        <div class="container">
            <?php if(isset($ticket_details->status) && !empty($ticket_details->status)){ ?>
            <div class="project-status">
                <div class="title"><span>Ticket Status</span></div>
                <div class="content">
                    <div class="status-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                    <span class="status-name"><?php echo $ticket_details->status; ?></span>
                </div>
            </div>
            <?php } ?>
            <?php if(isset($ticket_details->ticket_for) && !empty($ticket_details->ticket_for)){ ?>
            <div class="project-owner">
                <div class="title"><span>Ticket Created For</span></div>
                <div class="content">
                   <span class="round-name"><?php echo substr($ticket_details->ticket_for, 0, 2); ?></span> <span class="name"><?php echo $ticket_details->ticket_for; ?></span>
                </div>
            </div>
            <?php } ?>
            <?php if(isset($ticket_details->created_by) && !empty($ticket_details->created_by)){ ?>
                <div class="project-owner">
                    <div class="title"><span>Created By</span></div>
                    <div class="content">
                        <span class="round-name"><?php echo substr($ticket_details->created_by, 0, 2); ?></span> <span class="name"><?php echo $ticket_details->created_by; ?></span>
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($ticket_details->last_updated_by) && !empty($ticket_details->last_updated_by)){ ?>
                <div class="project-owner">
                    <div class="title"><span>Last Updated By</span></div>
                    <div class="content">
                        <span class="round-name"><?php echo substr($ticket_details->last_updated_by, 0, 2); ?></span> <span class="name"><?php echo $ticket_details->last_updated_by; ?></span>
                    </div>
                </div>
            <?php } ?>
            <div class="clr"></div>

        </div>


    </div>
</div>

    <!----------------------------------------------------
           ------------------ Page Specific Script --------------
           ----------------------------------------------------->
    <script>
        jQuery('#wpcontent').css('background', '#ffffff');
    </script>

