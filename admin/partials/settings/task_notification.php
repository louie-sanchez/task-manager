<?php

/**
 * The setting for automagically inserting the modules in the archives
 *
 * This file is used to setup a settings field
 *
 * @link       http://tigerton.se
 * @since      1.1.1
 *
 * @package    Beautiful_Taxonomy_Filters
 * @subpackage Beautiful_Taxonomy_Filters/admin/partials
 */
?>
<?php $get_task_notification = get_option('tmp_task_notification'); ?>


<p><label for="task_notification_checkbox">
		<input type="checkbox" id="task_notification_checkbox" value="yes" name="tmp_task_notification" <?php if( $get_task_notification == 'yes' ){ echo 'checked'; } ?> /> <?php _e('Yes', 'task-manager'); ?>
	</label></p>
