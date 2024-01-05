<?php
	function dbsetup() {
		global $dbconnection, $dbhost, $dbuser, $dbpass, $dbname;

		$dbconnection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($dbconnection->connection_error) {
			die('Could not connect to database.');
		}

		// "SHOW TABLES LIKE 'tblname'" is about twice as fast as "DESCRIBE tblname", including query overhead
		$result = $dbconnection->query('SHOW TABLES LIKE \'wishes\'');
		if ($result->num_rows < 1) {
			$setupsql = @file_get_contents('db.sql', $length=1024*1024);
			// suppress file reading error output and check the success status manually
			if ($setupsql === false) {
				// Not localized because it's a technical problem for the admin to see which they might want to look up or report
				die('The database does not seem to be set up because a table called "wishes" is missing, and the file "db.sql" could not be read for setting up the database.<br><br>

				     Does the file exist, and does the webserver have read permissions? Alternatively, you can also set up the database by running the SQL setup statements manually.');
			}
			$dbconnection->multi_query($setupsql);
			while ($dbconnection->more_results()) {
				// prevent "commands out of sync"
				$dbconnection->next_result();
			}
		}
	}

	function db($q) {
		global $dbconnection;

		if ( ! isset($dbconnection)) {
			dbsetup();
		}

		$result = $dbconnection->query($q);
		if ($result === false) {
			throw new Exception($dbconnection->error);
		}

		// for non-selects, only a boolean true/false is returned, which we've already checked now so there's no point returning that
		if (gettype($result) == 'boolean') {
			return $dbconnection; // so you can get insert_id for inserts or affected_rows for updates
		}
		else {
			return $result;
		}
	}

	function base64url($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	function sqlescape($str) {
		global $dbconnection;

		if ( ! isset($dbconnection)) {
			dbsetup();
		}

		return $dbconnection->escape_string($str);
	}

	function htmlescape($str) {
		// prevent breaking out of json strings or html comments
		return str_replace('-', '&#45;',
			str_replace('/', '&#47;',
			htmlentities($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401)));
	}

	function generateSecureToken() {
		return substr(
            strtr(
                base64_encode( // base64 instead of default hex to pack more entropy into fewer bytes
                    hash('sha256', // hash entropy before potentially putting it on the wire
						random_bytes(20), // docs say it raises an exception instead of falling back to insecure random
					true)
                ),
            '+/', '-_'),  // base64url variant
        0, 22);  // 22 base64 chars corresponds to 16.5 bytes or 132 bits entropy
	}

	function getCurrentUrl($params=true) {
		$url = (($_SERVER["HTTPS"] == "off" || empty($_SERVER["HTTPS"])) ? "http://" : "https://") . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		if ($params === false && strpos($url, '?') !== false) {
			$url = substr($url, 0, strpos($url, '?'));
		}
		return $url;
	}

    function bbcode($input_htmlencoded) {
        return nl2br(
            str_replace("\n</ul>", '</ul>',
            str_replace("\n</ol>", '</ol>',
            str_replace("\n<ul>", '<ul>',
            str_replace("\n<ol>", '<ol>',
            str_replace("</ul>\n", '</ul>',
            str_replace("</ol>\n", '</ol>',
            str_replace("<ul>\n", '<ul>',
            str_replace("<ol>\n", '<ol>',
            str_replace("\r", '',
            str_replace('[b]', '<strong>',
            str_replace('[/b]', '</strong>',
            str_replace('[i]', '<i>',
            str_replace('[/i]', '</i>',
            str_replace('[u]', '<u>',
            str_replace('[/u]', '</u>',
            str_replace('[ul]', '<ul>',
            str_replace('[/ul]', '</ul>',
            str_replace('[ol]', '<ol>',
            str_replace('[/ol]', '</ol>',
            str_replace('[li]', '<li>',
            $input_htmlencoded)))))))))))))))))))));
    }

	function linkify($input) {
		// input: "Check out https://example.org/!productid=93&awesome=true!"
		// result: "Check out <a href='https://example.org/!productid=93&amp;awesome=true'>example.org/!productid=93&amp;awesome=true</a>!"

		// input: "Have you seen the new collection (http://example.com/new)? Or the old at http://example.com/old?"
		// result: "Have you seen the new collection (<a href='http://example.com/new'>example.com/new</a>)? Or the old one at <a href='http://example.com/old'>example.com/old</a>?"

		/* Characters such as ? and ) are valid in a URL, but often used in text.
		   Ideally, we'd do parenthesis counting/matching and include the ) if one was opened inside the URL, but I don't know how to do that in regex and don't feel like writing a full parser.
		   After the protocol, hostname/ipv[46], and optional port, there is a mandatory slash and two [allowed_character_sets] with a lot of overlap, but the first one includes the
		   characters which we'd only want to match in the middle of the URL and not as the final character.
		 */
		return preg_replace('@(https?://)(([a-zA-Z0-9.-]{1,254}|\[[0-9a-f:]{2,45}\])(:[0-9]{1,5})?(/[a-zA-Z0-9%_.~:;/!?*+=&\@\'$()#[\]-]*[a-zA-Z0-9%_~/*+=&\@$-]+)?)@',
			'<a href="${0}" rel=noopener target=_blank class=autolink>${2}</a>',
			$input
		);
	}

	function linkifiedbbcode($input) {
		//$ampersandtoken = base64url(random_bytes(8));
		//$input_htmlescaped_noamp = str_replace('&amp;', $ampersandtoken, htmlentities($input, ENT_COMPAT | ENT_HTML401, 'UTF-8'));
		$input_htmlescaped = htmlentities($input, ENT_COMPAT | ENT_HTML401, 'UTF-8');
		$input_renderedbb = bbcode($input_htmlescaped);
		$input_rendered = linkify($input_renderedbb);
		return $input_rendered;
		//$input_rendered = str_replace($ampersandtoken, '&amp;', $input_rendered_noamp);
	}

