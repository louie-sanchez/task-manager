<?php


if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Task_Manager_Table extends WP_List_Table {

    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query()
     *
     * In a real-world scenario, you would make your own custom query inside
     * this class' prepare_items() method.
     *
     * @var array
     **************************************************************************/



    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'task',     //singular name of the listed records
            'plural'    => 'tasks',    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ) );

    }

    /*---------------------- Task Access Info ---------------------------------------*/

    public function getAccessId(){
        global $wpdb;
        $currentUserId = wp_get_current_user()->ID;
        $userAccess = array();

        /*--------------------------- Get User Access Information ----------------------*/
        if( in_array('administrator', wp_get_current_user()->roles) ){
            $userAccess['project'] = 13;
            $userAccess['task'] = 13;
        }else{
            $getAccessId = $wpdb->get_results("
        SELECT tug.project_access_id as project_access, tug.task_access_id as task_access
        FROM {$wpdb->prefix}tmp_users tu
        INNER JOIN {$wpdb->prefix}tmp_user_groups tug ON tug.id = tu.user_group_id
        WHERE tu.user_id = $currentUserId
         ");
            if( !empty($getAccessId) ){
                $userAccess['project'] = $getAccessId[0]->project_access;
                $userAccess['task'] = $getAccessId[0]->task_access;
            }else{
                $userAccess['project'] = 0;
                $userAccess['task'] = 0;
            }
        }
        return $userAccess;
    }

    /*---------------------- Task Access Info ---------------------------------------*/

    /**
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
//  id, name, project_name, task_status, task_priority, progress, due_date, task_label
    function column_default($item, $column_name){
        switch($column_name){
            case 'name':
            case 'project_name':
            case 'task_status':
            case 'task_priority':
            case 'progress':
            case 'task_members':
            case 'due_date':
            case 'created_at':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
//    function column_name($item){
//
//        //Return the title contents
//        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>',
//            /*$1%s*/ $item['name'],
//            /*$2%s*/ $item['id']
//        );
//    }


    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }


    // Column Action Customize
    function column_action($item){

        $task_access = $this->getAccessId()['task'];

        if ( in_array($task_access, array(13,12,11,16)) ) {
            $editButton = sprintf('<a class="dashicons dashicons-edit" href="?page=%s&task=%s"></a>', 'task-edit', $item['id']) ;
        }else{
            $editButton = '';
        }

        if ( in_array($task_access, array(13,12,16)) ) {
            $deleteButton = sprintf('<span class="dashicons dashicons-trash" onclick="deleteTargetData(%s)"></span>',$item['id']);
            $duplicateButton = sprintf('<a class="dashicons dashicons-admin-page copy_task" href="admin-ajax.php?action=duplicate_task&task_id=%s" title="Clone task"></a>',$item['id']);
        }else{
            $deleteButton = '';
            $duplicateButton = '';
        }

        return
            sprintf('<a class="dashicons dashicons-welcome-view-site" href="?page=%s&task=%s"></a>', 'task-details', $item['id']) .' '.
            $duplicateButton.'  '.
            $editButton  .'  '.
            $deleteButton;
    }

    // Customize Progress Column
    function column_progress($item){
        return  sprintf('<span class="progress-bar-parcent-cover"><span class="progress-bar-percent" style="width:%s&#37"></span></span>%s&#37;',$item['progress'],$item['progress']);
    }

    // Customize Name Column
    function column_name($item){
        return  sprintf('<a href="?page=task-details&task=%s">%s</a>',$item['id'],$item['name']);
    }


    // Customize Project Name Column
    function column_project_name($item){
        $project_access = $this->getAccessId()['project'];

        if ( $project_access == 14 || $project_access == 8 ){
            $project_anchor = $item['project_name'];
        }else{
            $project_anchor = sprintf('<a href="?page=project-details&project=%s">%s</a>',$item['project_id'],$item['project_name']) ;
        }
        return $project_anchor ;
    }

    // Task Priority Column
    function column_task_priority( $item ){
        $icon_dir_url =  plugins_url( '../images/icons/', __FILE__ );
        return  sprintf('<img src="'.$icon_dir_url. $item['task_priority_icon'] .'" alt=""/> ' . $item['task_priority']);
    }

    // Due Data Column Customize
    function column_due_date( $item ){
        if( strtotime($item['due_date'])>0 ){
            $dueDate = date("F j, Y", strtotime($item['due_date']));
        }else{
            $dueDate = null;
        }
        return  sprintf('%s',$dueDate);
    }

    // Due Data Column Customize
    function column_created_at( $item ){
        if( strtotime($item['created_at'])>0 ){
            $createdAt = date("F j, Y", strtotime($item['created_at']));
        }else{
	        $createdAt = null;
        }
        return  sprintf('%s',$createdAt);
    }

    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'name'     => __( 'Name', 'task-manager' ),
            'project_name'    => __( 'Project Name', 'task-manager' ),
            'task_status'    => __( 'Task Status', 'task-manager' ),
            'task_priority'    => __( 'Task Priority', 'task-manager' ),
            'progress'    => __( 'Progress', 'task-manager' ),
            'task_members'    => __( 'Assigned To', 'task-manager' ),
            'due_date'    => __( 'Due Date', 'task-manager' ),
            'created_at'    => __( 'Created At', 'task-manager' ),
            'action'  => __( 'Action', 'task-manager' ),
        );
        return $columns;
    }

    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/

    function get_sortable_columns() {
        $sortable_columns = array(
            'name'     => array('name',false),     //true means it's already sorted
            'project_name'  => array('project_name',false),
            'task_status'  => array('task_status',false),
            'task_priority'  => array('task_priority',false),
            'progress'  => array('progress',false),
            'task_members'  => array('task_members',false),
            'due_date'  => array('due_date',false),
            'created_at'  => array('created_at',false),
        );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {

        if( 'delete' === $this->current_action() ) {
            global $wpdb;
            $task_table_name = $wpdb->prefix . "tmp_tasks";
            $task_groups_table = "{$wpdb->prefix}tmp_task_groups";
            $task_users_table = "{$wpdb->prefix}tmp_task_users";

            if( isset($_REQUEST['task']) && !empty($_REQUEST['task']) ){
                $ids = implode( ',', array_map( 'absint', $_REQUEST['task'] ) );

                $wpdb->query( "DELETE FROM {$task_table_name} WHERE id IN($ids)" );
                $wpdb->query( "DELETE FROM {$task_groups_table} WHERE task_id IN($ids)" );
                $wpdb->query( "DELETE FROM {$task_users_table} WHERE task_id IN($ids)" );

                echo "<script>location.href='?page=tasks';</script>";
            }
        }

    }


    function prepare_items($search = '') {
        global $wpdb; //This is used only if making any database queries
        $currentUserId = wp_get_current_user()->ID;
        $task_access = $this->getAccessId()['task'];


        /**
         * Project Query Data
         */

	    $task_status = $wpdb->get_results("
        SELECT tts.id
        FROM {$wpdb->prefix}tmp_task_status as tts WHERE tts.group_name = 'close'
         ", "ARRAY_A");

	    $nts = array();
	    foreach ($task_status as $tts){
	    	$nts[]= $tts['id'];
	    }

	    $tshct = get_option('tmp_show_hide_completed_tasks');

	    $nts = implode (",", $nts);

	    $cQ = '0';
	    if($tshct == 'yes'){
		    $cQ = $nts;
	    }

	    $finalTaskIds = '0';

	    $query = "SELECT tsk.id, tsk.project_id, tp.name as project_name, tsk.name, tts.name as task_status,ttp.name as task_priority, 
            ttp.icon as task_priority_icon, tsk.due_date, tsk.created_at, tsk.progress, group_concat(usr.display_name) as task_members
            FROM {$wpdb->prefix}tmp_tasks tsk
            LEFT JOIN {$wpdb->prefix}tmp_projects tp ON tsk.project_id = tp.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_status tts ON tsk.task_status_id = tts.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_priorities ttp ON tsk.task_priority_id = ttp.id 
                LEFT JOIN {$wpdb->prefix}tmp_task_users ttu on ttu.task_id = tsk.id
                LEFT JOIN {$wpdb->prefix}users as usr ON usr.id = ttu.user_id";

	    $query .= " WHERE tts.id NOT IN ({$cQ}) ";

        if ( in_array($task_access, [8,14,16]) ){
            require_once plugin_dir_path(__FILE__) . 'user-based-custom-query-class.php';
            $currentUserTasks = User_Based_Custom_Query::getCurrentUserTasks();
            if(!empty($currentUserTasks)){
	            $finalTaskIds = implode(",",$currentUserTasks);
            }
	        $query .= " AND tsk.id IN ({$finalTaskIds}) ";
	        if(!empty($search)){
		        $query .= " AND (tsk.name LIKE '%{$search}%')";
	        }

        }elseif($task_access == 13){
	        if(!empty($search)){
		        $query .= " AND (tsk.name LIKE '%{$search}%')";
	        }
        }else{
	        $query .= " AND tsk.id IN ({$finalTaskIds}) ";
	        if(!empty($search)){
		        $query .= " AND (tsk.name LIKE '%{$search}%')";
	        }
        }
	    $query .= " group by tsk.id";

	    $tasks = $wpdb->get_results($query);


        /** End - Task Query Data */
//
//        echo '<pre>';
//        print_r($tasks);
//        echo '</pre>';
//        exit;

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 10;


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);


        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */


//        print_r($this->example_data);
//        $data = $this->example_data();

        $data = json_decode(json_encode($tasks), true);







        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'name'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}