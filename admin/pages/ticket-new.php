<?php

//if (!current_user_can('delete_pages')) {
//    return;
//}

function pr($arg)
{
//    echo "<pre>";
//    print_r($arg);
//    echo "</pre>";
//    exit;
}


global $wpdb; //This is used only if making any database queries


/*--------------------------------------------------------------------
--------------------- Additional Query for create project ------------
---------------------------------------------------------------------*/
// Get Project Category
$ticket_types = $wpdb->get_results("
        SELECT tt.id, tt.name
        FROM {$wpdb->prefix}tmp_ticket_types as tt
         ");

// Get Project Status
$project_status = $wpdb->get_results("
        SELECT ps.id, ps.name
        FROM {$wpdb->prefix}tmp_project_status as ps
         ");

// Get  User Groups
$tmp_usersArr = array();
$tmp_users = $wpdb->get_results("SELECT tu.user_id FROM {$wpdb->prefix}tmp_users as tu");
foreach ($tmp_users as $tmp_user) { $tmp_usersArr[] = $tmp_user->user_id; }

$get_system_user_query = new WP_User_Query(array('fields' => array('ID', 'display_name'), 'exclude' => $tmp_usersArr));
$wp_system_users = $get_system_user_query->get_results();

//pr($wp_system_users);


/*--------- END - Additional Query for create project -----------------------*/

?>
<div class="wrap tmp" id="newProject">

    <form name="project-new" method="post" action="?page=tickets">

        <input type="hidden" name="act" value="add_ticket">

        <h1 class="wp-heading-inline">Create A New Ticket</h1>
        <hr class="wp-header-end">
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">

                <div class="form-input">
                    <h2 class="title">Subject</h2>
                    <div class="input">
                        <span class="dashicons dashicons-sos"></span>
                        <input type="text" name="name" size="30" value="" spellcheck="true" autocomplete="off">
                    </div>
                    <p>The name of the ticket - where you can define customer ticket.</p>
                </div>

        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24">
                <div class="form-select">
                    <h2 class="title">Type</h2>
                    <div class="select">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <select id="" data-custom="" name="ticket_type_id">
                            <?php foreach ($ticket_types as $type): ?>
                                <option value="<?php echo $type->id; ?>">
                                    <?php esc_html_e($type->name, 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p>The ticket type - type of the ticket.</p>
                </div>
            </div>
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24">
                <div class="form-select">
                    <h2 class="title">Status</h2>
                    <div class="select">
                        <span class="dashicons dashicons-welcome-view-site"></span>
                        <select id="" data-custom="" name="ticket_status_id">
                            <?php foreach ($project_status as $status): ?>
                                <option value="<?php echo $status->id; ?>">
                                    <?php esc_html_e($status->name, 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p>The ticket status - current status of the ticket.</p>
                </div>
            </div>
        </div>
        <div class="clr"></div>
        <div class="wrapper form-wrap p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24">
                <div class="details">
                    <h2 class="title">Details</h2>
                    <?php wp_editor('', 'description', array('media_buttons' => true, 'editor_height' => 220, 'textarea_rows' => 10,)); ?>
                </div>
            </div>

            <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24">
                <div class="form-select">
                    <h2 class="title">Ticket For</h2>
                    <div class="select">
                        <span class="dashicons dashicons-admin-users"></span>
                        <select id="" data-custom="" name="ticket_for">
                            <?php foreach ($wp_system_users as $user): ?>
                                <option value="<?php echo $user->ID; ?>">
                                    <?php esc_html_e($user->display_name, 'task_manager'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p>Select any user who is not included in the ticket.</p>
                </div>
            </div>
        </div>

        <hr>

        <div class="p-u-sm-24-24 p-u-md-24-24 p-u-lg-24-24">
            <div class="form-input">
                <div id="submit-action">
                    <span class="spinner"></span>
                    <input type="submit" name="publish" id="publish" class="button button-primary button-large"
                           value="Create">
                </div>
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
    });
</script>
