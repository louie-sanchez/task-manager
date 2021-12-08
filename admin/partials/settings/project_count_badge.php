<?php $get_project_count_badge = get_option('tmp_project_count_badge'); ?>


<p><label for="project-count-badge-checkbox">
		<input type="checkbox" id="project-count-badge-checkbox" value="yes" name="tmp_project_count_badge" <?php if( $get_project_count_badge == 'yes' ){ echo 'checked'; } ?> /> <?php _e('Yes', 'task-manager'); ?>
	</label></p>