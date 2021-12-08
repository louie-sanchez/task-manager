<?php

$get_mail_host = get_option('tmp_setting_mail_host') ? get_option('tmp_setting_mail_host') : 'smtp.gmail.com' ; ?>

<p>
    <label for="tmp_setting_mail_host_input">
        <input type="text" id="tmp_setting_mail_host_input" name="tmp_setting_mail_host"
               value="<?php echo $get_mail_host; ?>"
               placeholder="Enter host"/>
    </label>
</p>