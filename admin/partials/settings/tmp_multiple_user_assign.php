<?php $get_tmp_multiple_user_assign = get_option('tmp_multiple_user_assign'); ?>

<p><label for="tmp_multiple_user_assign-checkbox">
        <input type="checkbox" id="tmp_multiple_user_assign-checkbox" value="yes" name="tmp_multiple_user_assign" <?php if( $get_tmp_multiple_user_assign == 'yes' ){ echo 'checked'; } ?> /> <?php _e('Yes', 'task-manager'); ?>
    </label></p>
