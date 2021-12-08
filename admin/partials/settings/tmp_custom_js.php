<?php  $tmp_custom_css = get_option('tmp_custom_css'); ?>

<p>
    <label for="tmp_custom_css-checkbox">
        <textarea id="tmp_custom_css-checkbox" name="tmp_custom_css" rows="10"><?php if(isset($tmp_custom_css) && !empty($tmp_custom_css)){echo $tmp_custom_css;} ?></textarea>
    </label>
</p>
