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
<?php $get_project_notification = get_option('tmp_project_notification'); ?>


<p><label for="project-notification-checkbox">
		<input type="checkbox" id="project-notification-checkbox" value="yes" name="tmp_project_notification" <?php if( $get_project_notification == 'yes' ){ echo 'checked'; } ?> /> <?php _e('Yes', 'task-manager'); ?>
	</label></p>
