<?php

$get_mail_smtp_auth = get_option('tmp_setting_mail_smtp_auth');

?>

<p>
    <label for="tmp_setting_mail_smtp_auth_checkbox">
        <input type="checkbox" id="tmp_setting_mail_smtp_auth_checkbox" value="yes" name="tmp_setting_mail_smtp_auth" <?php if( $get_mail_smtp_auth == 'yes' ){ echo 'checked'; } ?> /> <?php _e('Yes', 'task-manager'); ?>
    </label>
</p>
