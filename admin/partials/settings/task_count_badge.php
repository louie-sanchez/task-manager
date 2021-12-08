<?php $get_task_count_badge = get_option('tmp_task_count_badge'); ?>

<p>
    <label for="task-count-badge-checkbox">
		<input type="checkbox" id="task-count-badge-checkbox" value="yes" name="tmp_task_count_badge" <?php if( $get_task_count_badge == 'yes' ){ echo 'checked'; } ?> /> <?php _e('Yes', 'task-manager'); ?>
	</label>
</p>
