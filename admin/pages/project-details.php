<?php

// For Tasks Table
require_once plugin_dir_path( __FILE__ ) . '../partials/projects-task-table-class.php';

// To Get User Access Id
require_once plugin_dir_path( __FILE__ ) . '../partials/user-based-custom-query-class.php';
$userCustomQueryObject = new User_Based_Custom_Query();
$project_access = $userCustomQueryObject->getUserAccessId()['project'];


//Create an instance of our package class...
$taskTableData = new Task_Manager_Table();
//Fetch, prepare, sort, and filter our data...
$taskTableData->prepare_items();

global $wpdb;

$owner_id = wp_get_current_user()->ID;

/*--------------------------- Get User Access Information ----------------------*/
$getAccessId = $wpdb->get_results("
        SELECT tug.project_access_id as project_access, tug.task_access_id as task_access
        FROM {$wpdb->prefix}tmp_users tu
        INNER JOIN {$wpdb->prefix}tmp_user_groups tug ON tug.id = tu.user_group_id
        WHERE tu.user_id = $owner_id
         ");
if( !empty($getAccessId) ){
    $userAccess = $getAccessId[0];
}else{
    $userAccess = 0;
}

/*--------------------------------------------------
------------ Custom Debug Function ------------------
-----------------------------------------------------*/
function pr( $arg ){
    echo "<pre>";
    print_r( $arg );
    echo "</pre>";
    exit;
}

/*--------------------- Check Get Request Method -------------------*/
if ( isset($_REQUEST['project']) && !empty($_REQUEST['project']) ){
    $project = $_REQUEST['project'];
}else{
    $project = null;
}

/*------------------------------------------------------------------
--------------------- Query To get Project Data -------------------
-------------------------------------------------------------------*/
$get_project = $wpdb->get_results("
            SELECT p.id, p.name, p.project_category_id, p.project_status_id, p.live_url, p.test_url, p.design_date, p.development_date, p.test_date,
        p.go_live_date, p.description, pc.name as category, ps.name as status, p.owner_id
        FROM {$wpdb->prefix}tmp_projects p
        LEFT JOIN {$wpdb->prefix}tmp_project_categories pc ON p.project_category_id = pc.id 
        LEFT JOIN {$wpdb->prefix}tmp_project_status ps ON p.project_status_id = ps.id
        WHERE p.id = $project
         ");
$project_details = $get_project[0];
$project_details->owner = get_user_by('id', $project_details->owner_id)->display_name;

//pr($project_details);


// Get Users For this task
$cProjectUsers = $wpdb->get_results("SELECT tpu.user_id FROM {$wpdb->prefix}tmp_project_users tpu WHERE tpu.project_id = {$project} ");

// Get user ids as array
$cProjectUsersIds = array();
$eProjectGroupUsersIds = array();
foreach ($cProjectUsers as $pUser) { $cProjectUsersIds[] = $pUser->user_id; }

// Get User Groups For this task
$eProjectGroupUsers = $wpdb->get_results(" SELECT tu.user_id FROM {$wpdb->prefix}tmp_project_groups tpg 
      INNER JOIN {$wpdb->prefix}tmp_users tu ON tu.user_group_id = tpg.group_id
      WHERE tpg.project_id = {$project}");


// Get Group user ids as array
foreach ($eProjectGroupUsers as $pGUser) { $eProjectGroupUsersIds[] = $pGUser->user_id; }

// Get All user ids as unique way
$fGroupUsersIds = array_unique(array_merge($cProjectUsersIds, $eProjectGroupUsersIds));
$fProjectUsersImplode = implode(',', $fGroupUsersIds);

if(!empty($fProjectUsersImplode)){
	$project_detailsUsers = $wpdb->get_results("SELECT usr.ID as id, usr.display_name as name FROM {$wpdb->prefix}users usr WHERE usr.ID IN ({$fProjectUsersImplode}) ");
}else{
	$project_detailsUsers = array();
}


?>

<div class="wrap tmp project_details">
    <div class="padding-20"></div>
    <div class="p-u-sm-24-24 p-u-md-16-24 p-u-lg-16-24 left">
        <div class="heading-title">
            <?php if (isset($project_details->name) && !empty($project_details->name)){ ?>
            <div class="title-image">
                <?php echo substr(ucwords($project_details->name), 0, 2); ?>
            </div>
            <?php } ?>
            <div class="heading">
                <?php if (isset($project_details->name) && !empty($project_details->name)){ ?>
                <h2><?php echo $project_details->name ; ?></h2>
                <?php } ?>
                <?php if (isset($project_details->category) && !empty($project_details->category)){ ?>
                <span><?php echo $project_details->category ; ?></span>
                <?php } ?>
            </div>
            <div class="edit-option">
                <?php if ( in_array($project_access, array(11, 12, 13, 16)) ) { ?>
                <div class="edit_project">
                    <a href="?page=project-edit&project=<?php echo $project; ?>" title="Edit Project">
                        <span class="dashicons dashicons-edit"></span>
                        <span class="title"><?php _e( 'Edit Project', 'task-manager-pro' ); ?></span>
                    </a>

                </div>
                <?php } ?>
            </div>
        </div>
        <div class="clr"></div>
        <?php if (isset($project_details->description) && !empty($project_details->description)){ ?>
        <div class="details">
            <div class="title"><span><?php _e( 'Details', 'task-manager' ); ?></span></div>
            <div class="description">
                <?php print htmlspecialchars_decode($project_details->description) ; ?>
            </div>
        </div>
        <div class="clr"></div>
        <?php } ?>
        <div class="project_others">
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24 left">
                <ul class="other-info">
                    <?php if(isset($project_details->live_url) && !empty($project_details->live_url)){ ?>
                    <li>
                        <label><?php _e( 'Live Url', 'task-manager' ); ?></label>
                        <span> <a href="<?php echo $project_details->live_url ; ?>"><?php echo $project_details->live_url ; ?></a></span>
                    </li>
                    <?php } ?>
                    <?php if(isset($project_details->test_url) && !empty($project_details->test_url)){ ?>
                    <li>
                        <label><?php _e( 'Test Url', 'task-manager' ); ?></label>
                        <span> <a href="<?php echo $project_details->test_url ; ?>"><?php echo $project_details->test_url ; ?></a></span>
                    </li>
                     <?php } ?>
                    <?php if(isset($project_details->design_date) && !empty($project_details->design_date)){ ?>
                    <li>
                        <label><?php _e( 'Design Date', 'task-manager' ); ?></label>
                        <span><?php echo date("F j, Y", strtotime($project_details->design_date)) ; ?></span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="p-u-sm-24-24 p-u-md-12-24 p-u-lg-12-24 right">
                <ul class="other-info">
                    
                    <?php if(isset($project_details->development_date) && !empty($project_details->development_date)){ ?>
                    <li>
                        <label><?php _e( 'Development Date', 'task-manager' ); ?></label>
                        <span> <?php echo date("F j, Y", strtotime($project_details->development_date)); ?></span>
                    </li>
                    <?php } ?>
                    
                    <?php if(isset($project_details->test_date) && !empty($project_details->test_date)){ ?>
                    <li>
                        <label><?php _e( 'Test Date', 'task-manager' ); ?></label>
                        <span> <?php echo date("F j, Y", strtotime($project_details->test_date)) ; ?></span>
                    </li>
                    <?php } ?>
                    <?php if(isset($project_details->go_live_date) && !empty($project_details->go_live_date)){ ?>
                    <li>
                        <label><?php _e( 'Do Live Date', 'task-manager' ); ?></label>
                        <span> <?php echo date("F j, Y", strtotime($project_details->go_live_date)) ; ?></span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="clr"></div>
        <hr class="hr">
        <div class="task-table">
            <div class="title"><span><?php _e( 'Tasks of This Project', 'task-manager' ); ?></span>
                <a href="?page=task-new&project=<?php echo $project_details->id; ?>" class="page-title-action">
                    <?php _e( 'Add a new task', 'task-manager' ); ?>
                </a>
            </div>
            <div class="table-data">
                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                <form id="tasks-filter" method="get">
                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                    <!-- Now we can render the completed list table -->
                    <?php $taskTableData->display() ?>
                </form>
            </div>
        </div>
        <div class="clr"></div>
        <hr class="hr">
    </div>
    <div class="p-u-sm-24-24 p-u-md-8-24 p-u-lg-8-24 right">
        <div class="container">
            <?php if(isset($project_details->status) && !empty($project_details->status)){ ?>
            <div class="project-status">
                <div class="title"><span><?php _e( 'Project Status', 'task-manager' ); ?></span></div>
                <div class="content">
                    <div class="status-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                    <span class="status-name"><?php echo $project_details->status; ?></span>
                </div>
            </div>
            <?php } ?>
            <?php if(isset($project_details->owner) && !empty($project_details->owner)){ ?>
            <div class="project-owner">
                <div class="title"><span><?php _e( 'Project Owner', 'task-manager' ); ?></span></div>
                <div class="content">
                   <span class="round-name"><?php echo substr($project_details->owner, 0, 2); ?></span> <span class="name"><?php echo $project_details->owner; ?></span>
                </div>
            </div>
            <?php } ?>
            <div class="clr"></div>
            <?php if(isset($project_detailsUsers) && !empty($project_detailsUsers)){ ?>
            <div class="contact-person">
                <div class="title"><span><?php _e( 'Contacts Involved With This Project', 'task-manager' ); ?></span></div>
                <div class="contacts-list">
                    <ul>
                        <?php foreach ($project_detailsUsers as $user): ?>
                        <li><span class="round-name"><?php echo substr($user->name, 0, 2); ?></span> <span class="name"><?php echo $user->name; ?></span> </li>
                       
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php } ?>
        </div>


    </div>
</div>

    <!----------------------------------------------------
           ------------------ Page Specific Script --------------
           ----------------------------------------------------->
    <script>
        jQuery('#wpcontent').css('background', '#ffffff');
    </script>

