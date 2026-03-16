<?php
	$dbhost = 'localhost';
	$dbuser = 'oskalisti';
	$dbpass = (change me);
	$dbname = 'oskalisti';

	$email_from = 'oskalisti@localhost';  // Where do login emails originate from?
	$email_bounceto = 'bounces@localhost';  // What email address should bounced login emails be returned to (the "envelope from" address)
	$email_csp_report = 'cspreport@localhost';  // When someone uploads a SVG with Javascript in it and it gets executed, where should that be reported to?

	$defaultLanguage = 'en';  // When no language could be determined from the browser, which one be used?
	$secretLinkDays = 2.5 * 365; // after how many days does a secret login link disable itself due to inactivity? This is for the links that can be created in one's profile.
	$loginLinkTime = 60 * 120; // after how many seconds do login links expire? These are the one-time-use links sent by email.
	$maxConcurrentEmails = 2; // how many email login links may be valid at the same time? This is meant to limit that you can spam one person's inbox with 10000 login emails per hour
	$appName = 'Óskalisti';

	// use a unique name (perhaps use some random bytes) to scope the session to this application
	$sessionCookieName = 'oskalisti_n0a8_ses';

