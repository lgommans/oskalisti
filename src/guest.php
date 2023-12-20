<?php
	if ( ! isset($included)) {
		die('!included');
	}

	if (isset($_GET['loggedout'])) {
		$message = tr('Logout successful');
	}

	if (isset($_GET['loginToken'])) {
		// general cleanup
		$time = time();
		db("DELETE FROM loginlinks WHERE expires < '$time'");

		$token_dbescaped = sqlescape($_GET['loginToken']);
		$result = db("
			SELECT ll.logs_in_to, u.admin, u.language
			FROM loginlinks ll
			INNER JOIN users u
				ON ll.logs_in_to = u.id
			WHERE ll.expires > '$time'
				AND ll.token = '$token_dbescaped'
		");
		if ($result->num_rows == 0) {
			$error = tr('login link unknown error');
		}
		else {
			list($userid, $isadmin, $language) = $result->fetch_row();
			db("DELETE FROM loginlinks WHERE token = '$token_dbescaped'");
			$_SESSION["userid"] = $userid;
			if ($isadmin == 1) {
				$_SESSION["session"] = 'admin';
				$_SESSION["isadmin"] = true;
			}
			else {
				$_SESSION["session"] = 'user';
				$_SESSION["isadmin"] = false;
			}
			header('Location: .');
			exit;
		}
	}

	if (isset($_POST['action']) && $_POST['action'] == 'login') {
		if (empty($_POST['email'])) {
			$error = tr('Email address cannot be empty');
		}
		else {
			if (empty($_POST['password'])) {
				$email_dbescaped = sqlescape($_POST['email']);
				$result = db("SELECT id FROM users WHERE email = '$email_dbescaped'");
				if ($result->num_rows == 0) {
					$error = tr('Unknown email address');
				}
				else {
					$userid = intval($result->fetch_row()[0]);
					if (db("SELECT id FROM loginlinks WHERE logs_in_to = '$userid'")->num_rows >= $maxConcurrentEmails) {
						$error = tr('Too many emails sent');
					}
					else {
						$expires = time() + $loginLinkTime;
						$token = generateSecureToken();
						db("INSERT INTO loginlinks (expires, token, logs_in_to) VALUES('$expires', '$token', '$userid')");
						$subject = $appName . ' ' . tr('login email');
						$subject = '=?utf-8?B?' . base64_encode($subject) . '?='; // UTF-8 characters fail to send otherwise. Body with UTF-8 works fine...
						$loginlink = getCurrentUrl($params=false) . '?loginToken=' . $token;
						$loginlinkReadable = urldecode($loginlink);
						$msg = tr('login email text', ['APPNAME' => $appName]) . "<br><a href='$loginlink'>$loginlinkReadable</a>";
						$sent = @mail($_POST['email'], $subject, $msg, "From: $email_from\r\nContent-Type: text/html; charset=UTF-8", "-f$email_bounceto");
						if ($sent) {
							$message = tr('Login link sent');
						}
						else {
							$error = tr('Login email failed to send');
						}
					}
				}
			}
			else {
				$error = tr('Function not yet supported');
			}
		}
	}

	renderHeader($title=tr('Log in'));

	print('<h1>' . htmlescape(tr('Log in to')) . ' ' . $appName . '</h1>');

	if (isset($error)) {
		print('<span class=error>' . htmlescape($error) . '</span>');
	}
	if (isset($message)) {
		print('<span class=message>' . htmlescape($message) . '</span>');
	}
	?>
		<form method=POST>
			<input type=hidden name=action value=login>

			<?php echo htmlescape(tr('Email address:')); ?><br>
			<input type=email name=email><br>
			<input type=submit value="<?php echo htmlescape(tr('Send login email')); ?>"><br><br>

			<!--
			<?php echo htmlescape(tr('Or log in with password:')); ?><br>
			<input type=password name=password><br>
			<input type=submit value="<?php echo htmlescape(tr('Log in with password')); ?>">
			-->
		</form>
	<?php
	renderFooter();

