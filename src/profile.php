<?php
	if ( ! isset($included)) {
		die('!included');
	}

	$userid_int = intval($_SESSION['userid']);

	if (isset($_POST['name'])) {
		$name_dbescaped = sqlescape($_POST['name']);
		$language_dbescaped = sqlescape($_POST['language']);
		$color_dbescaped = '#FFFFFF';
		if (preg_match('/^#?[0-9a-zA-Z]+$/', $_POST['color'])) {
			$color_dbescaped = sqlescape($_POST['color']);
		}
		db("UPDATE users
		    SET name = '$name_dbescaped', language = '$language_dbescaped', color = '$color_dbescaped'
		    WHERE id = '$userid_int'");
		header('Location: ?profile');
		exit;
	}

	if (isset($_POST['action'])) {
		if ($_POST['action'] == 'createLink') {
			$access_token = generateSecureToken();
			$access_token_last_used_int = time();
			db("UPDATE users
			    SET access_token = '$access_token', access_token_last_used = '$access_token_last_used_int'
			    WHERE id = '$userid_int'");
			header('Location: ?profile');
		}
		if ($_POST['action'] == 'disableLink') {
			db("UPDATE users
			    SET access_token = NULL
			    WHERE id = '$userid_int'");
			header('Location: ?profile');
		}
	}

	$name_htmlescaped = htmlescape($GLOBALS['name']);
	$email_htmlescaped = htmlescape($GLOBALS['email']);
	$color_htmlescaped = '#FFFFFF';
	if ($GLOBALS['color'] != null) {
		$color_htmlescaped = htmlescape($GLOBALS['color']);
	}

	$result = db("SELECT access_token, access_token_last_used FROM users WHERE id = '$userid_int'")->fetch_row();
	$loginSecretLinkExpired = ($result[1] != null && $result[1] <= time() - $secretLinkDays * 3600 * 24);
	$loginSecretLinkEnabled = ($result[0] != null);
	if ($result[1] != null) {
		$lastUse = substr(str_replace('T', ' ', date('c', $result[1])), 0, strlen('0000-00-00 00:00:00')) . ' ' . date('e', $result[1]);
	}
	else {
		$lastuse = null;
	}
	$secretLoginLink = getCurrentUrl($params=false) . '?loginSecret=' . $result[0];
	$unusedYears = round($secretLinkDays / 365.25, 1);

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

		<section>
			<form method=POST>
				<div class=title><?php echo tr('Secret login link'); ?></div>

				<p>
					<?php echo tr('login link info', ['unusedYears' => $unusedYears]); ?>
				</p>

				<?php
					if ( ! $loginSecretLinkEnabled || $loginSecretLinkExpired) {
						if ($loginSecretLinkExpired) {
							echo '<p>' . tr('deleted login link', ['lastuse' => $lastUse]) . '</p>';
						}
						else {
							echo '<p>' . tr('No secret login link is active for your account.') . '</p>';
							if ($lastUse !== null) {
								echo '<p>' . tr('It was last used or previously created on:') . ' ' . $lastUse . '</p>';
							}
						}
						echo '<button type=submit name=action value=createLink>' . tr('Create secret link') . '</button>';
					}
					else {
						echo '<p>' . tr('secret link active', ['secretloginlink' => $secretLoginLink]) . '<br>' . tr('recreate link howto') . '</p>';
						echo '<p>' . tr('It was last used or previously created on:') . ' ' . $lastUse . '</p>';
						echo '<button type=submit name=action value=disableLink>' . tr('Delete secret link') . '</button>';
					}
				?>
			</form>
		</section>
	<?php

	renderFooter();

