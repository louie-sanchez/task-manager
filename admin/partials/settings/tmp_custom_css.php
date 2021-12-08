<?php  $tmp_custom_js = get_option('tmp_custom_js'); ?>

<p>
    <label for="tmp_custom_js_text">
        <textarea id="tmp_custom_js_text" name="tmp_custom_js" rows="10"><?php if(isset($tmp_custom_js) && !empty($tmp_custom_js)){echo $tmp_custom_js;} ?></textarea>
    </label>
</p>
