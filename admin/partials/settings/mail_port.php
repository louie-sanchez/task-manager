<?php

$get_mail_port = get_option('tmp_setting_mail_port') ? get_option('tmp_setting_mail_port') : '587' ; ?>

<p>
    <label for="tmp_setting_mail_port_input">
        <input type="text" id="tmp_setting_mail_port_input" name="tmp_setting_mail_port"
               value="<?php echo $get_mail_port; ?>"
               placeholder="Enter Port"/>
    </label>
</p>