<?php

class User_Based_Custom_Query
{

    public static function getUserAccessId()
    {
        global $wpdb;
        $currentUserId = wp_get_current_user()->ID;
        $userAccess = array();

        /*--------------------------- Get User Access Information ----------------------*/
        if (in_array('administrator', wp_get_current_user()->roles)) {
            $userAccess['project'] = 13;
            $userAccess['task'] = 13;
        } else {
            $getAccessId = $wpdb->get_results("
        SELECT tug.project_access_id as project_access, tug.task_access_id as task_access
        FROM {$wpdb->prefix}tmp_users tu
        INNER JOIN {$wpdb->prefix}tmp_user_groups tug ON tug.id = tu.user_group_id
        WHERE tu.user_id = $currentUserId
         ");
            if (!empty($getAccessId)) {
                $userAccess['project'] = $getAccessId[0]->project_access;
                $userAccess['task'] = $getAccessId[0]->task_access;
            } else {
                $userAccess['project'] = 0;
                $userAccess['task'] = 0;
            }
        }

        return $userAccess;
    }

    public static function getAllAccessIds()
    {
        global $wpdb;
        $allAccessIds = array();
        $getAllAccessIds = $wpdb->get_results("
        SELECT tuat.id
        FROM {$wpdb->prefix}tmp_user_access_types tuat
         ");
        foreach ($getAllAccessIds as $gaai) {
            $allAccessIds[] = $gaai->id;
        }

        return $allAccessIds;
    }

    public static function getCurrentUserTasks()
    {
        global $wpdb;
        $currentUserId = wp_get_current_user()->ID;

        $usersTaskIds = array();
        $groupsTaskIds = array();
        $getUsersTaskIds = $wpdb->get_results("
            SELECT ttu.task_id FROM {$wpdb->prefix}tmp_task_users ttu WHERE ttu.user_id = {$currentUserId}");
        foreach ($getUsersTaskIds as $cTU) {
            $usersTaskIds[] = $cTU->task_id;
        }


        $getGroupsTaskIds = $wpdb->get_results("
            SELECT ttg.task_id FROM {$wpdb->prefix}tmp_task_groups ttg
            INNER JOIN {$wpdb->prefix}tmp_users tu ON tu.user_id = {$currentUserId}
             WHERE ttg.group_id = tu.user_group_id ");
        foreach ($getGroupsTaskIds as $tTU) {
            $groupsTaskIds[] = $tTU->task_id;
        }
        $getfinalTaskIds = array_unique(array_merge($usersTaskIds, $groupsTaskIds));
        return $getfinalTaskIds;
    }

    public static function getCurrentUserProjects(){
        global $wpdb;
        $currentUserId = wp_get_current_user()->ID;

        $usersProjectIds = array();
        $groupsProjectIds = array();
        $getUsersProjectIds = $wpdb->get_results("
            SELECT tpu.project_id FROM {$wpdb->prefix}tmp_project_users tpu WHERE tpu.user_id = {$currentUserId}");
        foreach ($getUsersProjectIds as $cTU) {
            $usersProjectIds[] = $cTU->project_id;
        }

        $getGroupsProjectIds = $wpdb->get_results("
            SELECT tpg.project_id FROM {$wpdb->prefix}tmp_project_groups tpg
            INNER JOIN {$wpdb->prefix}tmp_users tu ON tu.user_id = {$currentUserId}
             WHERE tpg.group_id = tu.user_group_id ");
        foreach ($getGroupsProjectIds as $tTU) {
            $groupsProjectIds[] = $tTU->project_id;
        }
        $getfinalProjectIds = array_unique(array_merge($usersProjectIds, $groupsProjectIds));

        return $getfinalProjectIds;
    }

    public static function getCurrentUserTaskCount()
    {
        $getfinalTaskIds = self::getCurrentUserTasks();
        return count($getfinalTaskIds);
    }

    public static function getCurrentUserProjectCount()
    {
        $getfinalProjectIds = self::getCurrentUserProjects();
        return count($getfinalProjectIds);
    }

    public static function getCurrentUserProjectNames($limit = null)
    {
        global $wpdb;
        $getfinalProjectIds = implode(",", self::getCurrentUserProjects());
        if ($limit) {
            $getProjectNames = $wpdb->get_results(" SELECT tp.id, tp.name FROM {$wpdb->prefix}tmp_projects tp WHERE tp.id IN ({$getfinalProjectIds}) limit {$limit}");
        } else {
            $getProjectNames = $wpdb->get_results(" SELECT tp.id, tp.name FROM {$wpdb->prefix}tmp_projects tp WHERE tp.id IN ({$getfinalProjectIds})");
        }

        return $getProjectNames;
    }

    public static function getCurrentUserTaskNames($limit = null)
    {
        global $wpdb;
        $getfinalTaskIds = implode(",", self::getCurrentUserTasks());
        if ($limit) {
            $getTaskNames = $wpdb->get_results(" SELECT tt.id, tt.name FROM {$wpdb->prefix}tmp_tasks tt WHERE tt.id IN ({$getfinalTaskIds}) limit {$limit}");
        } else {
            $getTaskNames = $wpdb->get_results(" SELECT tt.id, tt.name FROM {$wpdb->prefix}tmp_tasks tt WHERE tt.id IN ({$getfinalTaskIds})");
        }
        return $getTaskNames;
    }

	public static function getAllTaskList($limit = null)
	{
		global $wpdb;
		if ($limit) {
			$getTaskNames = $wpdb->get_results("SELECT tsk.id, tsk.project_id, tp.name as project_name, tsk.name, tts.name as task_status, ttp.name as task_priority, 
            ttp.icon as task_priority_icon, ttl.name task_label, tsk.due_date, tsk.progress
            FROM {$wpdb->prefix}tmp_tasks tsk
            LEFT JOIN {$wpdb->prefix}tmp_projects tp ON tsk.project_id = tp.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_status tts ON tsk.task_status_id = tts.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_priorities ttp ON tsk.task_priority_id = ttp.id 
            LEFT JOIN {$wpdb->prefix}tmp_task_labels ttl ON tsk.task_label_id = ttl.id");
		} else {
			$getTaskNames = $wpdb->get_results(" SELECT tt.id, tt.name FROM {$wpdb->prefix}tmp_tasks tt");
		}
		return $getTaskNames;
	}

    public static function getTmpAccess(){
        global $wpdb;
        $currentUserId = wp_get_current_user()->ID;
    }

    public static function insertProjectData( $name, $category = null, $status = null, $live_url = null, $test_url = null,
                                             $design_date = null, $development_date = null, $test_date = null,
                                             $details = null, $go_live = null, $team_members = null, $user_group = null ){
        global $wpdb;
        $porject_table_name = $wpdb->prefix . "tmp_projects";
        $owner_id = wp_get_current_user()->ID;
	    $currentTime = date('Y-m-d H:i:s');

        $wpdb->insert($porject_table_name, array('id' => null, 'name' => $name, 'description' => $details,
            'project_category_id' => $category, 'project_status_id' => $status, 'live_url' => $live_url, 'test_url' => $test_url,
            'design_date' => $design_date, 'development_date' => $development_date, 'test_date' => $test_date, 'go_live_date' => $go_live,
            'owner_id' => $owner_id, 'created_at' => $currentTime, 'updated_at' => $currentTime));

        /*------------------------------------------------------------------
        ----------------------------- Task User Insert ----------------------
        -------------------------------------------------------------------*/

	    $followerGroup = $wpdb->get_results("SELECT tug.id
        FROM {$wpdb->prefix}tmp_user_groups as tug WHERE tug.task_access_id = '8' AND tug.project_access_id = '8'
         ");
	    if(!count($followerGroup)){
		    $wpdb->insert($wpdb->prefix . "tmp_user_groups", array('name' => 'Follower', 'project_access_id' => '8', 'task_access_id' => '8'));
		    $followerGroupId = $wpdb->insert_id;
	    }else{
		    $followerGroupId = $followerGroup[0]->id;
	    }

        $nmMembers = array();
        $nmGroups = array();
        $nmGroupMembers = array();
        $lastCreatedId = $wpdb->insert_id;
        if ($team_members && !empty($team_members)) {
            $project_users_table_name = $wpdb->prefix . "tmp_project_users";
            $team_membes_insert = explode(',', $team_members);
            foreach ($team_membes_insert as $member) {
	            $tmp_current_user = $wpdb->get_results("SELECT tu.user_group_id
        				FROM {$wpdb->prefix}tmp_users as tu WHERE tu.user_id = {$member}
         			");
	            if(!count($tmp_current_user)){
		            $wpdb->insert($wpdb->prefix . "tmp_users", array('user_id' => $member, 'user_group_id' => $followerGroupId));
	            }
                $wpdb->insert($project_users_table_name, array('user_id' => $member, 'project_id' => $lastCreatedId));
                $nmMembers[] = $member;
            }
        }

        /*------------------------------------------------------------------
        ----------------------------- Task Group Insert ----------------------
        -------------------------------------------------------------------*/
        if (isset($user_group) && !empty($user_group)) {
            $project_groups_table_name = $wpdb->prefix . "tmp_project_groups";
            foreach ($user_group as $group) {
                $wpdb->insert($project_groups_table_name, array('group_id' => $group, 'project_id' => $lastCreatedId));
                $nmGroups[] = $group;
            }
        }

        // Send Message To Assigned User
        // Merge Task User IDS
        $groupIdsImplode = implode(',', $nmGroups);
        if(!empty($groupIdsImplode)){
            $eTaskGroupUsers = $wpdb->get_results(" SELECT tu.user_id FROM {$wpdb->prefix}tmp_users tu 
            WHERE tu.user_group_id in ({$groupIdsImplode})");
            // Get Group user ids as array
            foreach ($eTaskGroupUsers as $tu) {
                $nmGroupMembers[] = $tu->user_id;
            }
        }




        $notifyMembers = array_unique(array_merge($nmMembers, $nmGroupMembers));

        $returnData = array();
        $returnData['id'] = $lastCreatedId;
        $returnData['type'] = 'project';
        $returnData['name'] = $name;
        $returnData['status'] = 'new';
        $returnData['notification'] = $notifyMembers;

        return $returnData;
    }

    public static function editProjectData( $name, $category = null, $status = null, $live_url = null, $test_url = null,
                                             $design_date = null, $development_date = null, $test_date = null,
                                             $details = null, $go_live = null, $team_members = null, $user_group = null, $project = null ){
        global $wpdb;
        $porject_table_name = $wpdb->prefix . "tmp_projects";
        $owner_id = wp_get_current_user()->ID;
	    $currentTime = date('Y-m-d H:i:s');

        $wpdb->update($porject_table_name, array('name' => $name, 'description' => $details,
            'project_category_id' => $category, 'project_status_id' => $status, 'live_url' => $live_url, 'test_url' => $test_url,
            'design_date' => $design_date, 'development_date' => $development_date, 'test_date' => $test_date, 'go_live_date' => $go_live,
            'owner_id' => $owner_id, 'updated_at' => $currentTime ), array('id' => $project));

        /*------------------------------------------------------------------
        ----------------------------- Task User Insert ----------------------
        -------------------------------------------------------------------*/

	    $followerGroup = $wpdb->get_results("SELECT tug.id
        FROM {$wpdb->prefix}tmp_user_groups as tug WHERE tug.task_access_id = '8' AND tug.project_access_id = '8'
         ");
	    if(!count($followerGroup)){
		    $wpdb->insert($wpdb->prefix . "tmp_user_groups", array('name' => 'Follower', 'project_access_id' => '8', 'task_access_id' => '8'));
		    $followerGroupId = $wpdb->insert_id;
	    }else{
		    $followerGroupId = $followerGroup[0]->id;
	    }


        $nmMembers = array();
        $nmGroups = array();
        $nmGroupMembers = array();
        $project_users_table_name = $wpdb->prefix . "tmp_project_users";
        $wpdb->delete($project_users_table_name, array('project_id' => $project));
        if ($team_members && !empty($team_members)) {
            $team_membes_insert = explode(',', $team_members);
            foreach ($team_membes_insert as $member) {
	            $tmp_current_user = $wpdb->get_results("SELECT tu.user_group_id
        				FROM {$wpdb->prefix}tmp_users as tu WHERE tu.user_id = {$member}
         			");
	            if(!count($tmp_current_user)){
		            $wpdb->insert($wpdb->prefix . "tmp_users", array('user_id' => $member, 'user_group_id' => $followerGroupId));
	            }
                $wpdb->insert($project_users_table_name, array('user_id' => $member, 'project_id' => $project));
                $nmMembers[] = $member;
            }
        }

        /*------------------------------------------------------------------
        ----------------------------- Task Group Insert ----------------------
        -------------------------------------------------------------------*/
        $project_groups_table_name = $wpdb->prefix . "tmp_project_groups";
        $wpdb->delete($project_groups_table_name, array('project_id' => $project));
        if (isset($user_group) && !empty($user_group)) {
            foreach ($user_group as $group) {
                $wpdb->insert($project_groups_table_name, array('group_id' => $group, 'project_id' => $project));
                $nmGroups[] = $group;
            }
        }

        // Send Message To Assigned User
        // Merge Task User IDS
        $groupIdsImplode = implode(',', $nmGroups);
        if(!empty($groupIdsImplode)){
            $eTaskGroupUsers = $wpdb->get_results(" SELECT tu.user_id FROM {$wpdb->prefix}tmp_users tu 
            WHERE tu.user_group_id in ({$groupIdsImplode})");

            // Get Group user ids as array
            foreach ($eTaskGroupUsers as $tu) {
                $nmGroupMembers[] = $tu->user_id;
            }
        }


        $notifyMembers = array_unique(array_merge($nmMembers, $nmGroupMembers));

        $returnData = array();
        $returnData['id'] = $project;
        $returnData['type'] = 'project';
        $returnData['name'] = $name;
        $returnData['status'] = 'update';
        $returnData['notification'] = $notifyMembers;

        return $returnData;

    }

    public static function insertTaskData($task_name, $project_id = null, $task_type = null, $task_label = null, $task_status = null,
                                          $task_priority = null, $estimation_time = null, $start_date = null, $due_date = null,
                                          $task_progress = 0, $task_details = null, $team_members = null, $user_group = null, $amount = 0 ) {
        global $wpdb;

        $currentUser = wp_get_current_user()->ID;
        $currentTime = date('Y-m-d H:i:s');
        // Task Table Name
        $task_table_name = $wpdb->prefix . "tmp_tasks";
        /**
         * Insert Task Queries
         */
            $wpdb->insert($task_table_name, array('project_id' => $project_id, 'task_type_id' => $task_type,
                'name' => $task_name, 'task_status_id' => $task_status, 'task_priority_id' => $task_priority, 'task_label_id' => $task_label,
                'description' => $task_details, 'created_by' => $currentUser, 'amount' => $amount,
                'esitmate_time' => $estimation_time, 'start_date' => $start_date, 'due_date' => $due_date, 'progress' => $task_progress,
                'last_update_by' => $currentUser, 'created_at' => $currentTime, 'updated_at' => $currentTime));
	    $lastCreatedId = $wpdb->insert_id;

            /*------------------------------------------------------------------
        ----------------------------- Task User Insert ----------------------
        -------------------------------------------------------------------*/
	    /*** New Code Here ***/
	    $followerGroup = $wpdb->get_results("SELECT tug.id
        FROM {$wpdb->prefix}tmp_user_groups as tug WHERE tug.task_access_id = '8' AND tug.project_access_id = '8'
         ");
	    if(!count($followerGroup)){
		    $wpdb->insert($wpdb->prefix . "tmp_user_groups", array('name' => 'Follower', 'project_access_id' => '8', 'task_access_id' => '8'));
		    $followerGroupId = $wpdb->insert_id;
	    }else{
		    $followerGroupId = $followerGroup[0]->id;
	    }
	    /*** New Code Here ***/

            $nmMembers = array();
            $nmGroups = array();
            $nmGroupMembers = array();
            if (isset($team_members) && !empty($team_members)) {
                $task_users_table_name = $wpdb->prefix . "tmp_task_users";
                $team_membes_insert = explode(',', $team_members);
                foreach ($team_membes_insert as $member) {
	                $tmp_current_user = $wpdb->get_results("SELECT tu.user_group_id
        				FROM {$wpdb->prefix}tmp_users as tu WHERE tu.user_id = {$member}
         			");
	                if(!count($tmp_current_user)){
		                $wpdb->insert($wpdb->prefix . "tmp_users", array('user_id' => $member, 'user_group_id' => $followerGroupId));
	                }
                    $wpdb->insert($task_users_table_name, array('user_id' => $member, 'task_id' => $lastCreatedId));
                    $nmMembers[] = $member;
                }
            }

            /*------------------------------------------------------------------
        ----------------------------- Task Group Insert ----------------------
        -------------------------------------------------------------------*/
            if (isset($user_group) && !empty($user_group)) {
                $task_groups_table_name = $wpdb->prefix . "tmp_task_groups";
                foreach ($user_group as $group) {
                    $wpdb->insert($task_groups_table_name, array('group_id' => $group, 'task_id' => $lastCreatedId));
                    $nmGroups[] = $group;
                }
            }

        // Merge Task User IDS
        $groupIdsImplode = implode(',', $nmGroups);
        if(!empty($groupIdsImplode)){
            $eTaskGroupUsers = $wpdb->get_results(" SELECT tu.user_id FROM {$wpdb->prefix}tmp_users tu 
                WHERE tu.user_group_id in ({$groupIdsImplode})");

            // Get Group user ids as array
            foreach ($eTaskGroupUsers as $tu) {
                $nmGroupMembers[] = $tu->user_id;
            }
        }


        $notifyMembers = array_unique(array_merge($nmMembers, $nmGroupMembers));
        // Merge Task User IDS

        $returnData = array();
        $returnData['id'] = $lastCreatedId;
        $returnData['type'] = 'task';
        $returnData['name'] = $task_name;
        $returnData['status'] = 'new';
        $returnData['notification'] = $notifyMembers;

        return $returnData;
        
    }

    public static function editTaskData($task_name, $project_id = null, $task_type = null, $task_label = null, $task_status = null,
                                          $task_priority = null, $estimation_time = null, $start_date = null, $due_date = null,
                                          $task_progress = null, $task_details = null, $team_members = null, $user_group = null, $task_id, $amount = null ) {

        // Global Variable
        global $wpdb;
        // Current User
        $currentUser = wp_get_current_user()->ID;
        // Task Table Name
        $task_table_name = $wpdb->prefix . "tmp_tasks";
        /**
         * Insert Task Queries
         */
        $updated_at = date('Y-m-d H:i:s');
//        print_r(array('project_id' => $project_id, 'task_type_id' => $task_type,
//                      'name' => $task_name, 'task_status_id' => $task_status,
//                      'task_priority_id' => $task_priority, 'task_label_id' => $task_label,
//                      'description' => $task_details, 'esitmate_time' => $estimation_time,
//                      'start_date' => $start_date, 'due_date' => $due_date,
//                      'progress' => $task_progress, 'amount' => $amount,
//                      'last_update_by' => $currentUser,
//                      'updated_at' => $updated_at));
//        exit;
        $wpdb->update($task_table_name, array('project_id' => $project_id, 'task_type_id' => $task_type,
                                              'name' => $task_name, 'task_status_id' => $task_status,
                                              'task_priority_id' => $task_priority, 'task_label_id' => $task_label,
                                              'description' => $task_details, 'esitmate_time' => $estimation_time,
                                              'start_date' => $start_date, 'due_date' => $due_date,
                                              'progress' => $task_progress, 'amount' => $amount,
                                              'last_update_by' => $currentUser,
                                              'updated_at' => $updated_at), array('id' => $task_id ));

        /*------------------------------------------------------------------
        ----------------------------- Task User Insert ----------------------
        -------------------------------------------------------------------*/
	    /*** New Code Here ***/
	    $followerGroup = $wpdb->get_results("SELECT tug.id
        FROM {$wpdb->prefix}tmp_user_groups as tug WHERE tug.task_access_id = '8' AND tug.project_access_id = '8'
         ");
	    if(!count($followerGroup)){
		    $wpdb->insert($wpdb->prefix . "tmp_user_groups", array('name' => 'Follower', 'project_access_id' => '8', 'task_access_id' => '8'));
		    $followerGroupId = $wpdb->insert_id;
	    }else{
		    $followerGroupId = $followerGroup[0]->id;
	    }
	    /*** New Code Here ***/

        $nmMembers = array();
        $nmGroups = array();
        $nmGroupMembers = array();
        $task_users_table_name = $wpdb->prefix . "tmp_task_users";
        $wpdb->delete($task_users_table_name, array('task_id' => $task_id ));
        if (isset($team_members) && !empty($team_members)) {
            $team_membes_insert = explode(',', $team_members);
            foreach ($team_membes_insert as $member) {
	            $tmp_current_user = $wpdb->get_results("SELECT tu.user_group_id
        				FROM {$wpdb->prefix}tmp_users as tu WHERE tu.user_id = {$member}
         			");
	            if(!count($tmp_current_user)){
		            $wpdb->insert($wpdb->prefix . "tmp_users", array('user_id' => $member, 'user_group_id' => $followerGroupId));
	            }

	            $tmp_current_task_user = $wpdb->get_results("SELECT tu.user_id
        				FROM {$task_users_table_name} as tu WHERE tu.user_id = {$member} AND tu.task_id = {$task_id}
         			");
	            if(!count($tmp_current_task_user)){
		            $wpdb->insert($task_users_table_name, array('user_id' => $member, 'task_id' => $task_id ));
	            }

                $nmMembers[] = $member;
            }
        }
        /*------------------------------------------------------------------
        ----------------------------- Task Group Insert ----------------------
        -------------------------------------------------------------------*/
        $task_groups_table_name = $wpdb->prefix . "tmp_task_groups";
        $wpdb->delete($task_groups_table_name, array('task_id' => $task_id));
        if (isset($user_group) && !empty($user_group)) {
            foreach ($user_group as $group) {
                $wpdb->insert($task_groups_table_name, array('group_id' => $group, 'task_id' => $task_id));
                $nmGroups[] = $group;
            }
        }
        
        // Merge Task User IDS
        $groupIdsImplode = implode(',', $nmGroups);
        if(!empty($groupIdsImplode)){
            $eTaskGroupUsers = $wpdb->get_results(" SELECT tu.user_id FROM {$wpdb->prefix}tmp_users tu 
                WHERE tu.user_group_id in ({$groupIdsImplode})");

            // Get Group user ids as array
            foreach ($eTaskGroupUsers as $tu) {
                $nmGroupMembers[] = $tu->user_id;
            }
        }


        $notifyMembers = array_unique(array_merge($nmMembers, $nmGroupMembers));
        // Merge Task User IDS

        $returnData = array();
        $returnData['id'] = $task_id;
        $returnData['type'] = 'task';
        $returnData['name'] = $task_name;
        $returnData['status'] = 'update';
        $returnData['notification'] = $notifyMembers;

        return $returnData;
    }

    public static function sendMessage( $id, $subject, $message, $fromID = null, $ccID = null, $bccID = null, $attachment = null ){

//        $headers[] = 'From: Me Myself <me@example.net>';
//        $headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
//        $headers[] = 'Cc: iluvwp@wordpress.org'; // note you can just use a simple email address


        // Header Information
        $attachments = array();
        $headers = array();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        if ( $fromID ){
            $fromUserInfo = get_user_by( 'ID', $fromID );
            $headers[] = 'From: '.$fromUserInfo->display_name.' <'.$fromUserInfo->user_email.'>';
        }

        if ( $ccID ){
            $ccUserInfo = get_user_by( 'ID', $ccID );
            $headers[] = 'Cc: '.$ccUserInfo->display_name.' <'.$ccUserInfo->user_email.'>';
        }

        if ( $bccID ){
            $bccUserInfo = get_user_by( 'ID', $bccID );
            $headers[] = 'Bcc: '.$bccUserInfo->display_name.' <'.$bccUserInfo->user_email.'>';
        }

        if ( $attachment ){

//            $attachmentDirTest = WP_CONTENT_DIR . '/uploads/file_to_attach.zip';

            if ( is_array($attachment) ){
                $attachments = $attachment;
            }else{
                $attachments[] = $attachment;
            }

        }

        // To User
        $toUserInfo = get_user_by( 'ID', $id );
        $to = $toUserInfo->user_email;

        wp_mail( $to, $subject, $message, $headers, $attachments );
        return true;
    }

    public static function sendNotificationMessage( $notify ){


	    $get_task_notification = get_option('tmp_task_notification');
	    $get_project_notification = get_option('tmp_project_notification');
	    $slug = [];

	        if ( $get_task_notification == 'yes' || $get_project_notification == 'yes' ){
		        $id =  $notify['id'];
		        $type = $notify['type'];
		        $name = $notify['name'];
		        $status = $notify['status'];

		        $notifyMembers = $notify['notification'];

		        $email_change_text = __(
			        '###HI###,

###NOTICE###

###REVIEW###

###LINK###'
		        );

		        $email_change_email = array(
			        'to'      => "",
			        'subject' => "",
			        'message' => $email_change_text,
			        'headers' => "",
		        );
		        $nOwner = '';
		        if(isset($notify['owner'])){
		        	$nOwner = $notify['owner'];
		        }
		        $email_change_email['message'] = str_replace( '###REGARDS_EMAIL###', $nOwner, $email_change_email['message'] );
		        $email_change_email['message'] = str_replace( '###SITEURL###', get_site_url(), $email_change_email['message'] );


		        foreach ( $notifyMembers as $nm ){
			        $fromUserInfo = get_user_by( 'ID', $nm );

			        $email_change_email['to'] = $fromUserInfo->user_email;

			        $email_change_email['message'] = str_replace( '###HI###', __('Hi', 'task-manager')." ".$fromUserInfo->display_name, $email_change_email['message'] );
			        if( $type == 'project' && $get_project_notification == 'yes' ){
				        if( $status == 'new'){
					        $email_change_email['subject'] = "Project(New) - ". $name . "";
					        $email_change_email['message'] = str_replace( '###NOTICE###', __('You have assigned in a new project.', 'task-manager'), $email_change_email['message'] );
				        }else{
					        $email_change_email['subject'] = "Project(Modified) - ". $name . "";
					        $email_change_email['message'] = str_replace( '###NOTICE###', __('The project has been updated. Can you please take a look on that project.', 'task-manager'), $email_change_email['message'] );
				        }
				        $email_change_email['message'] = str_replace( '###REVIEW###', __("Can you please take a look on that project?"), $email_change_email['message'] );
				        $email_change_email['message'] = str_replace( '###LINK###', get_site_url()."/wp-admin/admin.php?page=project-details&project=".$id, $email_change_email['message'] );

                        // Send from Gmail
				        wp_mail( $email_change_email['to'], $email_change_email['subject'], $email_change_email['message'], $email_change_email['headers'] );
			        }

			        if( $type == 'task' && $get_task_notification == 'yes' ){
				        if( $status == 'new'){
					        $email_change_email['subject'] = "Task(New) - ". $name . "";
					        $email_change_email['message'] = str_replace( '###NOTICE###', __('You have assigned in a new task.', 'task-manager'), $email_change_email['message'] );
				        }else{
					        $email_change_email['subject'] = "Task(Modified) - ". $name . "";
					        $email_change_email['message'] = str_replace( '###NOTICE###', __('The task has been updated.', 'task-manager'), $email_change_email['message'] );
				        }
				        $email_change_email['message'] = str_replace( '###REVIEW###', __("Can you please take a look on that task?"), $email_change_email['message'] );
				        $email_change_email['message'] = str_replace( '###LINK###', get_site_url()."/wp-admin/admin.php?page=task-details&task=".$id, $email_change_email['message'] );


				        // Send Mail
				        wp_mail( $email_change_email['to'], $email_change_email['subject'], $email_change_email['message'], $email_change_email['headers'] );
			        }
		        }
		         return true;
	        }else{
                 return true;
	        }
    }


}


?>