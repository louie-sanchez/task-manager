<?php
//if (isset($_POST['awesome_text'])) {
//update_option('awesome_text', $_POST['awesome_text']);
//$value = $_POST['awesome_text'];
//}

//$options = get_option('task_manager_settings');

//print_r($options);

?>


<div class="wrap">

<h2><?php _e( 'Task Manager Pro Settings', 'task_manager' ) ?></h2>


<br>

	<?php flush_rewrite_rules(); ?>
    <form method="post" action="options.php">
		<?php settings_fields('task-manager-options'); ?>
		<?php do_settings_sections('task-manager-options'); ?>
		<?php submit_button('Save Changes'); ?>
    </form>




</div>