<?php
	if ( ! isset($included)) {
		die('!included');
	}

	if (isset($_POST['name'])) {
		$userid_int = intval($_SESSION['userid']);
		$name_dbescaped = sqlescape($_POST['name']);
		$language_dbescaped = sqlescape($_POST['language']);
		$color_dbescaped = '#FFFFFF';
		if (preg_match('/^#?[0-9a-zA-Z]+$/', $_POST['color'])) {
			$color_dbescaped = sqlescape($_POST['color']);
		}
		db("UPDATE users
			SET name = '$name_dbescaped', language = '$language_dbescaped', color = '$color_dbescaped'
			WHERE id = '$userid_int'");
		header('Location: .');
		exit;
	}

	$name_htmlescaped = htmlescape($GLOBALS['name']);
	$email_htmlescaped = htmlescape($GLOBALS['email']);
	$color_htmlescaped = '#FFFFFF';
	if ($GLOBALS['color'] != null) {
		$color_htmlescaped = htmlescape($GLOBALS['color']);
	}

	renderHeader($title=tr('profile'));

	?>
		<section>
			<form method=POST>
				<input name=name placeholder='Nils Holgersson' value='<?php echo $name_htmlescaped; ?>' style='color: <?php echo $color_htmlescaped; ?>'><br>
				<input type=email placeholder='user@example.org' value='<?php echo $email_htmlescaped; ?>' disabled><br>
				<select name=language>
					<?php
						foreach (getSupportedLanguages() as $languagecode=>$name) {
							$selected = '';
							if ($GLOBALS['language'] == $languagecode) {
								$selected = ' selected';
							}
							print('<option value="' . htmlescape($languagecode) . '"' . $selected . '>' . htmlescape($name) . '</option>');
						}
					?>
				</select><br>
				<input type=color name=color value='<?php echo $color_htmlescaped; ?>'><br>
				<input type=submit value="<?php echo tr('Save'); ?>">
			</form>
		</section>
	<?php

	renderFooter();

