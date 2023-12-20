<?php
	// TODO allow deleting wishes which you added
	$included = true;
	require('config.php');
	require('src/languages.php');
	require('src/functions.php');

	function renderHeader($title=null) {
		global $appName;

		$hyphenatedtitle = '';
		if ( ! empty($title)) {
			$hyphenatedtitle = htmlescape(ucfirst($title)) . ' - ';
		}
		?>
			<!DOCTYPE html>
			<meta charset='utf-8'>
			<title><?php echo $hyphenatedtitle . $appName; ?></title>
			<link rel=stylesheet href='res/main.css'>
			<link rel='icon' href='res/logo.png' type='image/png'>
			<meta name='viewport' content='width=450'>
			<meta name='format-detection' content='telephone=no'>
		<?php

		if ( ! empty($_SESSION["session"])) {
			print('<header>');
			print('<a class=logo href="."><img src="res/logo.png" border=0 height=56 align=middle></a>');
			print('<div class=user>');
			print(tr('Welcome') . ', '
				. '<a href="?profile" style="/*color: ' . htmlescape($GLOBALS['color']) . '*/">' . htmlescape($GLOBALS['name']) . '</a> | <a href="?logout">' . tr('Log out') . '</a>');
			if ($_SESSION["isadmin"] === true && $_SESSION['session'] != 'admin') {
				print(' | <a href="?sudo">' . tr('su') . '</a>');
			}
			print('</div>');
			print('</header>');
		}
	}

	function renderFooter() {
	}

	if (isset($_GET['cspreport'])) {
		$data = file_get_contents('php://input');
		$subject = '=?utf-8?B?' . base64_encode('CSP Report ' . $appName) . '?='; // UTF-8 characters fail to send otherwise
		mail($email_csp_report, $subject, $data, "From: $email_from\r\nContent-Type: text/plain", "-f$email_bounceto");
		header('HTTP/1.1 204 No Content');
		exit;
	}

	session_name($sessionCookieName);
	session_start([
		// samesite=Lax: you're logged in when navigating to this site (e.g., from a search engine), but any cross-origin POST or background requests bear no session cookie
		'cookie_samesite' => 'Lax',
		// use_strict_mode: don't accept session fixation
		'use_strict_mode' => true,
	]);

	if (empty($_SESSION['session'])) {
		chooseLanguage('detect');
		require('src/guest.php');
		exit;
	}

	list($GLOBALS['name'], $GLOBALS['color'], $GLOBALS['language'], $GLOBALS['email']) = db('
		SELECT name, color, language, email
		FROM users
		WHERE id = "' . sqlescape($_SESSION["userid"]) . '"'
	)->fetch_row();
	chooseLanguage($GLOBALS['language']);

	if (isset($_GET['logout'])) {
		// PHP docs: "session_destroy does exactly what you'd expect, but don't use it, unset all session variables instead". Eh? If it removes the session file (I checked: it does),
		// why'd one want to run extra code and disk writes to maintain the session ID between users, which is a potential vulnerability anyway?!
		// So I chose to use the function that seems to unlink(2) the session file, which is fast and efficient.
		$retval = session_destroy();
		if ($retval !== true) {  // docs don't say why or under which circumstances this could fail, but it can, so...
			renderHeader();
			// can't show a more precise error message because we're not given that information :/
			print('<span class=error>' . tr('Logout failed') . ' (<tt>session_destroy() == false</tt>)</span>');
			renderFooter();
			exit;
		}
		// Docs also say to write some code that manually removes the cookie. Because this redirect sends you to a page which will
		// immediately assign you a new session cookie (overwriting the old), this would be futile in our situation.
		header('Location: ?loggedout');
		exit;
	}

	if ($_SESSION["session"] === 'admin') {
		require('src/admin.php');
		exit;
	}

	if (isset($_GET['sudo']) && $_SESSION["isadmin"] === true) {
		$_SESSION["session"] = 'admin';
		header('Location: .');
		exit;
	}

	if (isset($_GET['profile'])) {
		require('src/profile.php');
	}
	else if (isset($_GET['person'])) {
		require('src/person.php');
	}
	else {
		require('src/overview.php');
	}

