<?php

$get_mail_password = get_option('tmp_setting_mail_password') ? get_option('tmp_setting_mail_password') : 'www.netwarriorservices.com' ; ?>

<p>
    <label for="tmp_setting_mail_password_input">
        <input type="password" id="tmp_setting_mail_password_input" name="tmp_setting_mail_password"
               value="<?php echo $get_mail_password; ?>"
               placeholder=""/>
    </label>
</p>