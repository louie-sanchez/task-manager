<?php

// Creating the widget
class tmp_widgets extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
// Base ID of your widget
            'tmp_widgets', __('Task Manager Pro', 'task_manager'),
            array('description' => __('Show user based task lists', 'task_manager'),)
        );
    }

// Creating widget front-end
// This is where the action happens
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
// before and after widget arguments are defined by themes
        echo $args['before_widget'];

        // Import User Base Query Class
        require_once plugin_dir_path(__FILE__) . '../partials/user-based-custom-query-class.php';
        $getProjectAccessId = User_Based_Custom_Query::getUserAccessId()['project'];
        $getTaskAccessId = User_Based_Custom_Query::getUserAccessId()['task'];
        $getAllAccessIds = User_Based_Custom_Query::getAllAccessIds();

        if(in_array( $getProjectAccessId , $getAllAccessIds ) || in_array( $getTaskAccessId , $getAllAccessIds )){
            if (!empty($title)){ echo $args['before_title'] . $title . $args['after_title']; }
        }

        // This is where you run the code and display the output
        if ( in_array( $getProjectAccessId , $getAllAccessIds ) ){
            if($instance['project_count'] == 'on'){
                $userProjects = User_Based_Custom_Query::getCurrentUserProjectCount();
            }

            if($instance['project_list'] == 'on'){
                $projectList = User_Based_Custom_Query::getCurrentUserProjectNames();
            }

            if(isset($userProjects) && $userProjects > 0){
                echo "<h5>You have ".$userProjects." assigned<a href='".admin_url('admin.php?page=projects')."'> project(s)</a>.</h5>";
            }

            if(isset($projectList) && count((array)$projectList) > 0):
                echo '<div id="project-list" class="tmp_site_widget"><div class="title">Project List</div>';
                echo '<ul class="project-list">';
                foreach ($projectList as $project){
                    echo "<li><a href='".admin_url('admin.php?page=project-details&project=').$project->id."'>".$project->name."</a></li>";
                }
                echo '</ul>';
                echo '</div>';
            endif;
        }

        if ( in_array( $getTaskAccessId , $getAllAccessIds ) ){
            if($instance['task_count'] == 'on'){
                $userTasks = User_Based_Custom_Query::getCurrentUserTaskCount();
            }

            if($instance['task_list'] == 'on'){
                $taskList = User_Based_Custom_Query::getCurrentUserTaskNames();

            }

            if(isset($userTasks) && $userTasks > 0){
                echo "<h5>You have ".$userTasks." assigned <a href='".admin_url('admin.php?page=tasks')."'>task(s)</a>.</h5>";
            }

            if(isset($taskList) && count((array)$taskList) > 0):
                echo '<div id="task-list" class="tmp_site_widget"><div class="title">Task List</div>';
                echo '<ul class="task-list">';
                foreach ($taskList as $task){
                    echo "<li><a href='".admin_url('admin.php?page=task-details&task=').$task->id."'>".$task->name."</a></li>";
                }
                echo '</ul>';
                echo '</div>';
            endif;

        }

        echo $args['after_widget'];
    }

// Widget Backend
    public function form($instance)
    {
        if (isset($instance['title'])) { $title = $instance['title']; } else { $title = __('Task Manager', 'task_manager'); }
        if (isset($instance['project_count'])) { $project_count = $instance['project_count']; } else { $project_count = 'on'; }
        if (isset($instance['project_list'])) {$project_list = $instance['project_list'];} else {$project_list = '';}
        if (isset($instance['task_count'])) {$task_count = $instance['task_count'];} else {$task_count = 'on';}
        if (isset($instance['task_list'])) {$task_list = $instance['task_list'];} else {$task_list = '';}
// Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('project_count'); ?>" name="<?php echo $this->get_field_name('project_count'); ?>" <?php if(esc_attr($project_count) == 'on') echo 'checked'; ?>>
            <label for="widget-categories-3-dropdown"><?php _e( 'Show User Project Count', 'task-manager' ); ?></label><br>

            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('project_list'); ?>" name="<?php echo $this->get_field_name('project_list'); ?>" <?php if(esc_attr($project_list) == 'on') echo 'checked'; ?>>
            <label for="widget-categories-3-dropdown"><?php _e( 'Show User Project List', 'task-manager' ); ?></label><br>

            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('task_count'); ?>" name="<?php echo $this->get_field_name('task_count'); ?>" <?php if(esc_attr($task_count) == 'on') echo 'checked'; ?>>
            <label for="widget-categories-3-dropdown"><?php _e( 'Show User Task Count', 'task-manager' ); ?></label><br>

            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('task_list'); ?>" name="<?php echo $this->get_field_name('task_list'); ?>" <?php if(esc_attr($task_list) == 'on') echo 'checked'; ?>>
            <label for="widget-categories-3-dropdown"><?php _e( 'Show User Task List', 'task-manager' ); ?></label><br>
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['project_count'] = (!empty($new_instance['project_count'])) ? strip_tags($new_instance['project_count']) : '';
        $instance['project_list'] = (!empty($new_instance['project_list'])) ? strip_tags($new_instance['project_list']) : '';
        $instance['task_count'] = (!empty($new_instance['task_count'])) ? strip_tags($new_instance['task_count']) : '';
        $instance['task_list'] = (!empty($new_instance['task_list'])) ? strip_tags($new_instance['task_list']) : '';
        return $instance;
    }
} // Class wpb_widget ends here

?>