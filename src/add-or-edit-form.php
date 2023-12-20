<?php
	if ( ! isset($included)) {
		die('!included');
	}
?>
<input type=hidden name=wishid value=-1>

<label>
	<?php echo tr('Title'); ?><br>
	<input type=text name=title placeholder="<?php echo tr('title placeholder'); ?>"<?php echo ($autofocus ? ' autofocus' : ''); ?> required>
</label><br><br>

<label>
	<?php echo tr('Description'); ?><br>
	<textarea name=description rows=5 cols=70 placeholder="<?php echo tr('description placeholder'); ?>"></textarea>
</label><br><br>

<span class=delpic style='display: none'>
	<img>
	<label><input type=checkbox name=delpic> <?php echo tr('Delete current image'); ?></label>
	<br><br>
</span>

<label>
	<?php echo tr('Picture'); ?><br>
	<input type=file name=pic accept='image/png, image/jpeg, image/webp, image/avif, image/svg+xml, image/gif, image/bmp, image/tiff'>
</label><br><br>

<span class=personal style='display: none;'>
	<label><input type=checkbox name=personal> <?php echo tr('Personal'); ?></label><br>
	<?php echo tr("Only visible to you and the list's owner"); ?><br><br>
</span>

<input type=submit class='add jumbobutton' value="<?php echo tr('Add wish'); ?>">
<input type=submit class='edit jumbobutton' value="<?php echo tr('Edit wish'); ?>" style='display: none;'>
