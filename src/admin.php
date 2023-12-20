<?php
	if ( ! isset($included)) {
		die('!included');
	}

	if (isset($_GET['dropPrivileges'])) {
		$_SESSION["session"] = 'user';
		header('Location: .');
		exit;
	}

	if (isset($_POST['name'])) {
		$name_dbescaped = sqlescape($_POST['name']);
		$email_dbescaped = sqlescape($_POST['email']);
		$language_dbescaped = sqlescape($_POST['language']);
		$result = db("INSERT INTO users (name, email, language) VALUES('$name_dbescaped', '$email_dbescaped', '$language_dbescaped')");
		$message = tr('Added user with ID') . ' ' . $result->insert_id;
	}

	renderHeader($title=tr('administration'));

	if (isset($message)) {
		print('<span class=message>' . htmlescape($message) . '</span>');
	}
	?>
		<section>
			<a href="?dropPrivileges">Drop privileges</a>
		</section>
		<section>
			<form method=POST>
				<input name=name placeholder='Nils Holgersson'><br>
				<input name=email type=email placeholder='user@example.org'><br>
				<select name=language>
					<?php
						foreach (getSupportedLanguages() as $languagecode=>$name) {
							print('<option value="' . htmlescape($languagecode) . '">' . htmlescape($name) . '</option>');
						}
					?>
				</select><br>
				<input type=submit value='Create user'>
			</form>
		</section>
	<?php

	renderFooter();

