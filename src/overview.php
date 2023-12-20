<?php
	if ( ! isset($included)) {
		die('!included');
	}

	renderHeader();

	if (isset($_GET['addedWish'])) {
		print('<span class=message>' . tr('Wish added to list') . '</span>');
	}

	$result = db('SELECT id, name, color FROM users ORDER BY name');
	$mypersonlink = false;
	print('<section>');
	print(tr('Choose a list'));
	print('<ul>');
	while ($row = $result->fetch_row()) {
		$personid_int = intval($row[0]);
		$name_htmlescaped = htmlescape($row[1]);
		$color_htmlescaped = htmlescape($row[2]);
		$personlink = "?person=$personid_int";
		$onclick = '';
		if ($_SESSION['userid'] == $row[0]) {
			$spoiler_alert = tr('spoiler_alert_confirm');
			$onclick = " onclick='return confirm(\"$spoiler_alert\");'";
			$mypersonlink = $personlink;
		}
		print("<li style='color: $color_htmlescaped'> <a href='$personlink'$onclick>$name_htmlescaped</a>");
	}
	print('</ul>');
	print('</section>');

	?>
		<section>
			<?php echo tr('Add wish to your list without visiting'); ?><br><br>

			<form method=POST action="<?php echo htmlescape($mypersonlink); ?>" enctype='multipart/form-data'>
				<input type=hidden name=backToOverview value=yes>
				<?php $autofocus=false; require('src/add-or-edit-form.php'); ?>
			</form>
		</section>
	<?php

	renderFooter();

