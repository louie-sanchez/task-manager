<?php $tmp_show_hide_completed_tasks = get_option('tmp_show_hide_completed_tasks'); ?>

<p><label for="tmp_show_hide_completed_tasks-checkbox">
		<input type="checkbox" id="tmp_show_hide_completed_tasks-checkbox" value="yes" name="tmp_show_hide_completed_tasks" <?php if( $tmp_show_hide_completed_tasks == 'yes' ){ echo 'checked'; } ?> /> <?php _e('Yes', 'task-manager'); ?>
	</label></p>
