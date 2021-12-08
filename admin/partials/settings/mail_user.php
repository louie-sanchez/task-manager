<?php

$get_mail_user = get_option('tmp_setting_mail_user') ? get_option('tmp_setting_mail_user') : 'task.manager.netwarriorservices.com' ; ?>

<p>
    <label for="tmp_setting_mail_user_input">
        <input type="text" id="tmp_setting_mail_user_input" name="tmp_setting_mail_user"
               value="<?php echo $get_mail_user; ?>"
               placeholder="Enter Username"/>
    </label>
</p>