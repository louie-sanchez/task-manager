<?php
require_once plugin_dir_path(__FILE__) . '../partials/user-based-custom-query-class.php';
?>

<?php $taskCount = User_Based_Custom_Query::getCurrentUserTaskCount(); ?>
<?php $projectCount = User_Based_Custom_Query::getCurrentUserProjectCount(); ?>

<?php if ($taskCount && $taskCount > 0) { ?>
    <p>You have <a href="admin.php?page=tasks"><?php echo $taskCount; ?>
            assigned tasks</a> on the task list. Can you please take a look on the <a
            href="admin.php?page=tasks">task</a> page.</p>
<?php } ?>

<?php if ($projectCount && $projectCount > 0) { ?>
    <p>You have <a href="admin.php?page=tasks"><?php echo $projectCount; ?>
            assigned projects</a> on the project list. Can you please take a look on the <a
            href="admin.php?page=projects">project</a> page.</p>
<?php } ?>