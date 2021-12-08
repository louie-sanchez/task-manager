<?php


/**----------------------------------------------------------------------------
 * ----------------------- New Project Insertion Query -----------------------
 * ----------------------------------------------------------------------------
 */

//This is used only if making any database queries
global $wpdb;

if (isset($_POST['action']) && $_POST['action'] == 'add_category' && isset($_POST['category_name']) && !empty($_POST['category_name'])) {

    $name = $_POST['category_name'];


    if (isset($_POST['category_slug']) && !empty($_POST['category_slug'])) {

        $slug = $_POST['category_slug'];

    } else {
        $slug = strtolower($name);
        $slug = preg_replace('/\s+/', '-', $slug);
    }

    if (!empty($slug)) {
        $found_duplicate = $wpdb->get_results("
            SELECT {$wpdb->prefix}tmp_project_categories.id
            FROM {$wpdb->prefix}tmp_project_categories
            WHERE {$wpdb->prefix}tmp_project_categories.slug = '$slug'
         ");

        if (!empty($found_duplicate)) {
            $slug = $slug . '_' . rand(10, 100);
        }
    }

    if (isset($name) && !empty($name) && !empty($slug)) {

        $project_category_table_name = $wpdb->prefix . "tmp_project_categories";

        /**
         * New Project Queries
         */
        $insert_category = $wpdb->insert($project_category_table_name, array('id' => null, 'name' => $name, 'slug' => $slug));

        if ($insert_category) {
            echo '';
        }
    }
}

/*--------- END - New Project Insertion Query -----------------------*/

/*---------------------------------------------------------
-------- Execute Query To Delete Access Type
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'delete') && $_POST['category_id'] && !empty($_POST['category_id'])) {
    if ($_POST['_token'] = date('H:i')) {
        $table = "{$wpdb->prefix}tmp_project_categories";
        $wpdb->delete($table, array('id' => $_POST['category_id']));
    }
}
/*---------------- End Executed Delete Query ------------------------*/


/*---------------------------------------------------------
-------- Execute Query To Modify Project Category
------------------------------------------------------------*/
if ((isset($_POST['action']) && $_POST['action'] == 'edit') && isset($_POST['edit_category_id']) && !empty($_POST['edit_category_id']) && isset($_POST['edit_category_name']) && !empty($_POST['edit_category_name']) && isset($_POST['edit_category_slug']) && !empty($_POST['edit_category_slug'])) {
    $table = "{$wpdb->prefix}tmp_project_categories";

    $slug = $_POST['edit_category_slug'];
    $edit_category_id = $_POST['edit_category_id'];
    if (!empty($slug)) {
        $found_duplicate = $wpdb->get_results("
            SELECT pc.id
            FROM {$wpdb->prefix}tmp_project_categories pc
            WHERE pc.slug = '$slug' AND pc.id != $edit_category_id
         ");

        if (!empty($found_duplicate)) {
            $slug = $slug . '_' . rand(10, 100);
        }
    }

    $wpdb->update($table, array('name' => $_POST['edit_category_name'], 'slug' => $slug), array('id' => $_POST['edit_category_id']));
}

/*---------------- End Executed Query ------------------------*/


/*---------------------------------------------------------------------
        Project Category List Table
/*-------------------------------------------------------------------*/
require_once plugin_dir_path(__FILE__) . '../partials/category-list-table-class.php';

/**
 * Category Table Here
 */

//Create an instance of our package class...
$categoryTableData = new Task_Manager_Pro_Table();

//Fetch, prepare, sort, and filter our data...
$categoryTableData->prepare_items();


?>


<div class="wrap nosubsub tmp">
    <h1><?php _e( 'Project Categories', 'task-manager-pro' ); ?></h1>

    <div id="ajax-response"></div>


    <div id="col-container" class="wp-clearfix">

        <div id="col-left">
            <div class="col-wrap">


                <div class="form-wrap">
                    <h2><?php _e( 'Add New Category', 'task-manager-pro' ); ?></h2>
                    <form id="addtag" method="post" action="" class="validate">
                        <input type="hidden" name="action" value="add_category">
                        <div class="form-field form-required term-name-wrap">
                            <label for="category-name"><?php _e( 'Name', 'task-manager-pro' ); ?></label>
                            <input name="category_name" id="category-name" type="text" value="" size="40"
                                   aria-required="true">
                            <p><?php _e( 'The name is how it appears on your project.', 'task-manager-pro' ); ?></p>
                        </div>
                        <div class="form-field term-slug-wrap">
                            <label for="category-slug"><?php _e( 'Slug', 'task-manager-pro' ); ?></label>
                            <input name="category_slug" id="category-slug" type="text" value="" size="40">
                            <p><?php _e( 'The “slug” is the URL-friendly version of the name. It is usually all lowercase and
                                contains only letters, numbers, and hyphens.', 'task-manager-pro' ); ?></p>
                        </div>

                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                                 value="<?php _e( 'Add New Category', 'task-manager-pro' ); ?>"></p></form>
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
                        <strong><?php _e( 'Note:', 'task-manager-pro' ); ?></strong><br><?php _e( 'Deleting a category does not delete the projects in that category.', 'task-manager-pro' ); ?></p>
                </div>

            </div>
        </div><!-- /col-right -->

    </div><!-- /col-container -->
    <div id="edit-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <div class="title"><?php _e( 'Edit Project Category', 'task-manager-pro' ); ?></div>

            <div class="form-input">
                <label id="category_name">
                    <?php _e( 'Category Name', 'task-manager-pro' ); ?>
                </label>
                <input type="text" placeholder="<?php _e( 'Category Name', 'task-manager-pro' ); ?>" id="category_name" value=""/>
            </div>

            <div class="form-input">
                <label id="category_slug">
                    <?php _e( 'Category Slug', 'task-manager-pro' ); ?>
                </label>
                <input type="text" placeholder="<?php _e( 'Category Slug', 'task-manager-pro' ); ?>" id="category_slug" value=""/>
            </div>
            <br>
            <p class="submit"><input type="submit" name="submit" id="submit" onclick="submitEditAction()"
                                     class="button button-primary" value="Modify User Group"></p>
            <a href="#0" class="cd-popup-close closeDialogue img-replace"></a>
        </div> <!-- cd-popup-container -->
    </div> <!-- cd-popup -->


    <!-----------------------------------------------------
    -------------- Hidden Form For Post Action -----------
    ------------------------------------------------------->
    <form id="action-delete-form" action="" method="POST" autocomplete="off" style="display: none;">
        <input type="hidden" name="_token" value="<?php echo date('H:i'); ?>">
        <input type="hidden" id="delete_category_id" name="category_id">
        <input type="hidden" name="action" value="delete">
    </form>

    <!-----------------------------------------------------
        -------------- Hidden Edit Form For Post Action -----------
        ------------------------------------------------------->
    <form id="action-edit-form" method="POST" action="" autocomplete="off" style="display: none;">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="edit_category_id" name="edit_category_id">
        <input type="hidden" id="edit_category_name" name="edit_category_name">
        <input type="hidden" id="edit_category_slug" name="edit_category_slug">
    </form>


    <div id="delete-popup" class="cd-popup" role="alert">
        <div class="cd-popup-container">
            <p class="confirmation"><?php _e( 'You are about to delete the Category. Are you sure ?', 'task-manager-pro' ); ?></p>
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
        var name = jQuery(param).attr('data-name');
        var slug = jQuery(param).attr('data-slug');
        jQuery('#edit-popup #category_name').val(name);
        jQuery('#edit-popup #category_slug').val(slug);

        // Set hiddent edit submission form values
        jQuery('#edit_category_id').val(id);
        jQuery('#edit_category_name').val(name);
        jQuery('#edit_category_slug').val(slug);

        // Hide Popup
        jQuery('#edit-popup').addClass('is-visible');
    }


    jQuery("#edit-popup #category_name").on("change paste keyup", function () {
        jQuery('#edit_category_name').val(jQuery(this).val());
    });

    jQuery("#edit-popup #category_slug").on("change paste keyup", function () {
        jQuery('#edit_category_slug').val(jQuery(this).val());
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
    function deleteTargetData(id) {
        jQuery('#action-delete-form #delete_category_id').val(id);
        jQuery('#delete-popup').addClass('is-visible');
    }

    function deleteTargetConfirm() {
        jQuery('#action-delete-form').submit();
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
