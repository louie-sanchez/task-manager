<?php

class ProjectsPageCustomQuery{
    public static function getProjectsTotalCount(){
        global $wpdb;
        $puCount = array();

        $project_count = $wpdb->get_results("SELECT COUNT(`id`) as projects FROM {$wpdb->prefix}tmp_projects");
        $puCount['total_project'] = $project_count[0]->projects;


        $project_count = $wpdb->get_results("SELECT COUNT(`id`) as projects FROM {$wpdb->prefix}tmp_projects WHERE project_status_id = 2");
        $puCount['total_open_project'] = $project_count[0]->projects;

        $project_count = $wpdb->get_results("SELECT COUNT(`id`) as projects FROM {$wpdb->prefix}tmp_projects WHERE project_status_id = 4");
        $puCount['total_close_project'] = $project_count[0]->projects;

        $users_count = $wpdb->get_results("SELECT COUNT(`id`) as users FROM {$wpdb->prefix}tmp_users");
        $puCount['total_users'] = $users_count[0]->users;

        $task_count = $wpdb->get_results("SELECT COUNT(`id`) AS tasks FROM {$wpdb->prefix}tmp_tasks");
        $puCount['total_task'] = $task_count[0]->tasks;

        return $puCount;
    }


}


?>