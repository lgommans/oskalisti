<?php
	// TODO support for variants such as nl-BE or en-US, rather than just nl and en

	function chooseLanguage($language='detect') {
		global $translations, $chosenLanguage;

		// allow the user to override the language, no matter what parameter you pass here, because why not
		if (isset($_GET['lang'])) {
			$chosenLanguage = $_GET['lang'];
		}
		else if ($language === 'detect' || empty($language)) {
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				// TODO parse the header properly and find any supported language
				$preference = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				if (isset($translations[$preference])) {
					$chosenLanguage = $preference;
				}
			}
			// "else" we could still look at the IP address or some such. For now, just leave the default
		}
		else {
			$chosenLanguage = $language;
		}
	}

	function getLanguage() {
		global $chosenLanguage;

		return $chosenLanguage;
	}

	function getSupportedLanguages() {
		global $translations;

		$supported = [];
		foreach ($translations as $languagecode=>$values) {
			$supported[$languagecode] = $values['_name'];
		}
		return $supported;
	}

	function tr($str, $parameters=[]) {
		global $translations, $chosenLanguage;

		// if we have the string in the chosen language, return that
		// if not, check if we have it in the fallback language
		// if not, return the key
		// TODO should we write the latter case, or even the latter two cases, to a log file?

		$translation = $str;
		if (isset($translations[$chosenLanguage])) {
			if (isset($translations[$chosenLanguage][$str])) {
				$translation = $translations[$chosenLanguage][$str];
			}
			else if (isset($translations[$chosenLanguage]['_fallback'])) {
				$fallback = $translations[$chosenLanguage]['_fallback'];
				if (isset($translations[$fallback][$str])) {
					$translation = $translations[$fallback][$str];
				}
			}
		}

		foreach ($parameters as $parameter=>$replacement) {
			$translation = str_replace("[$parameter]", $replacement, $translation);
		}

		return $translation;
	}

	$chosenLanguage = $defaultLanguage;
	$translations = [
		'en' => [
			'_name' => 'English',

			'Log in to' => 'Log in to',
			'Email address:' => 'Email address:',
			'Send login email' => 'Send login email',
			'Or log in with password:' => 'Or log in with password:',
			'Log in with password' => 'Log in with password',
			'Email address cannot be empty' => 'Email address cannot be empty',
			'Unknown email address' => 'Unknown email address',
			'Login link sent' => 'Login link sent',
			'Login email failed to send' => 'Login email failed to send',
			'login email' => 'login email',
			'login email text' => 'A login link was requested for your email address. Click here to log in to [APPNAME]:',
			'login link unknown error' => 'Unknown login link. Possibly the link expired, already used, or was not copied in full',
			'administration' => 'administration',
			'Log in' => 'Log in',
			'Log out' => 'Log out',
			'Logout failed' => 'Logout failed',
			'Logout successful' => 'Logout successful',
			'Welcome' => 'Welcome',
			'profile' => 'profile',
			'personid not found' => 'A person with the provided ID could not be found',
			'person has no wishes' => 'No wishes are currently registered for this person',
			'By' => 'By',
			'Edit' => 'Edit',
			'Save' => 'Save',
			'Delete' => 'Delete',
			'Add wish' => 'Add wish',
			'Edit wish' => 'Edit wish',
			'Strike off' => 'Strike off',
			'Add comment' => 'Add comment',
			'Comment_text_placeholder' => 'I will gift books 2 and 3',
			'Struck wishes' => 'Struck wishes',
			'Unstrike' => 'Un-strike',
			'spoiler_alert_confirm' => 'That is your own list! Are you sure you want to spoil potential surprises?',
			'Personal' => 'Personal',
			"Only visible to you and the list's owner" => "Only visible to you and the list's owner",
			'Title' => 'Title',
			'Description' => 'Description',
			'title placeholder' => 'Harry Potter books 2 through 7',
			'description placeholder' => 'Optional description or further details of the present idea',
			'Add wish to your list without visiting' => '<b>Add a wish to your list</b>,<br>without needing to open it and potentially spoil surprises.',
			'Wish added to list' => 'The wish was added to your list!',
			'Are you sure you want to delete this?' => 'Are you sure you want to permanently delete this?',
			'Added user with ID' => 'Added user with ID',
			'Choose a list' => 'Choose whose wishlist you want to view',
			'Add a wish' => 'Add a wish',
			'Wishlist of' => 'Wishlist of',
			'Back to the overview' => 'Back to the overview',
			'Are you sure to strike this' => 'Are you sure you want to remove this from the list?',
			'Are you sure to unstrike this' => 'Are you sure that you want to put this wish back on the list?',
			'Add a comment to this wish' => 'Add a comment for this wish',
			'Added by' => 'Added by',
			'Edited by' => 'Edited by',
			'Commented' => 'Commented',
			'Picture' => 'Picture',
			'Personal wish (only visible to A and B)' => 'Personal wish (only visible to PERSON1 and PERSON2)',
			'Delete current image' => 'Delete current image',
			'su' => 'su',
			'su-title' => '&quot;su&quot; is an old Unix command for using administrative permissions',
			'Leave administrator mode' => 'Leave administrator mode',
		],

		'de' => [
			'_name' => 'Deutsch',
			'_fallback' => 'en',

			'Log in to' => 'Anmelden bei',
			'Email address:' => 'E-Mail-Adresse:',
			'Send login email' => 'Anmelde-E-Mail schicken',
			'Or log in with password:' => 'Oder melde dich an mit Passwort:',
			'Log in with password' => 'Anmelden mit Passwort',
			'Email address cannot be empty' => 'E-Mail-Adresse darf nicht leer sein',
			'Unknown email address' => 'Unbekannte E-Mail-Adresse',
			'login link unknown error' => 'Unbekannter Anmeldelink. Möglicherweise ist der Link abgelaufen, schon benutzt, oder nicht wurde nicht vollstandig kopiert',
			'Login link sent' => 'Anmeldelink verschickt',
			'login email text' => 'Einen Anmeldelink wurde für deine E-Mail-Adresse angefragt. Klick hier um dich bei [APPNAME] einzuloggen:',
			'administration' => 'Administration',
			'Log in' => 'Anmelden',
			'Log out' => 'Ausloggen',
			'Welcome' => 'Hallo',
			'person has no wishes' => 'Für diese Person sind im Moment keine Wünsche gespeichert',
			'Logout successful' => 'Erfolgreich abgemeldet',
			'Comment_text_placeholder' => 'Ich werde Bücher 2 und 3 schenken',
			'Add comment' => 'Kommentar hinzufügen',
			'commented' => 'kommentiert',
			'Strike off' => 'Abhaken',
			'Struck wishes' => 'Abgehakte Wünsche',
			'Unstrike' => 'Unabhaken',
			'spoiler_alert_confirm' => 'Das ist deine Liste! Sicher, dass du dir die Überraschung verderben willst?',
			'Add wish' => 'Wunsch hinzufügen',
			'Edit' => 'Bearbeiten',
			'Save' => 'Speichern',
			'Delete' => 'Entfernen',
			'Are you sure you want to delete this?' => 'Sicher, dass du dies endgültig entfernen möchtest?',
			"Personal" => "Persönlich",
			"Only visible to you and the list's owner" => 'Nur sichtbar für Dich und den/die Eigentümer*in der Liste',
			'title placeholder' => 'Harry Potter-Bücher 2 bis 7',
			'description placeholder' => 'Extra Beschreibung oder weitere Details zur Geschenk-Idee',
			'Description' => 'Beschreibung',
			'Add wish to your list without visiting' => '<b>Füge einen Wunsch zu Deiner Liste hinzu</b><br>ohne diese öffnen zu müssen und potenziell eine Überraschung zu verderben.',
			'Wish added to list' => 'Der Wunsch wurde zu Deiner Liste hinzugefügt!',
			'Choose a list' => 'Wähle aus, wessen Wunschliste du sehen möchtest',
			'Add a wish' => 'Einen Wunsch hinzufügen',
			'Wishlist of' => 'Wunschliste von',
			'Back to the overview' => 'Zurück zur Übersicht',
			'Are you sure to strike this' => 'Sicher, dass Du dies von der Liste entfernen möchtest?',
			'Are you sure to unstrike this' => 'Sicher, dass Du diesen Wunsch wieder zu der Liste hinzufügen möchtest?',
			'Personal wish (only visible to A and B)' => 'Persönlicher Wunsch (nur sichtbar für PERSON1 und PERSON2)',
			'Title' => 'Überschrift',
			'Added by' => 'Hinzugefügt von',
			'Edit wish' => 'Wunsch bearbeiten',
			'Edited by' => 'Bearbeitet von',
			'Add a comment to this wish' => 'Einen Kommentar zu diesem Wunsch hinzufügen',
			'Commented' => 'Kommentar von',
			'Picture' => 'Bild',
			'Delete current image' => 'Jetziges Bild entfernen',
			'su' => 'su',
			'su-title' => '&quot;su&quot; ist ein altes Unix-Befehl um Administratorrechten zu benutzen',
			'Leave administrator mode' => 'Administrator-Modus verlassen',
		],

		'nl' => [
			'_name' => 'Nederlands',
			'_fallback' => 'en',

			'Log in to' => 'Inloggen op',
			'Email address:' => 'E-mailadres:',
			'Send login email' => 'Stuur inlog-e-mail',
			'Or log in with password:' => 'Of log in met wachtwoord:',
			'Log in with password' => 'Inloggen met wachtwoord',
			'Email address cannot be empty' => 'E-mailadres mag niet leeg zijn',
			'Unknown email address' => 'Onbekend e-mailadres',
			'login link unknown error' => 'Onbekende inloglink. Wellicht is de link verlopen, al gebruikt, of niet volledig gekopiëerd',
			'Login link sent' => 'Inloglink verstuurd',
			'login email text' => 'Een inloglink is voor jouw e-mailadres aangevraagd. Klik hier om bij [APPNAME] in te loggen:',
			'administration' => 'beheer',
			'Log in' => 'Inloggen',
			'Log out' => 'Uitloggen',
			'Welcome' => 'Welkom',
			'Logout successful' => 'Uitloggen gelukt',
			'profile' => 'profiel',
			'personid not found' => 'De persoon met het opgegeven ID kon niet worden gevonden',
			'person has no wishes' => 'Er zijn momenteel geen wensen bekend voor deze persoon',
			'Edit' => 'Bewerken',
			'Save' => 'Opslaan',
			'Delete' => 'Verwijderen',
			'Add wish' => 'Wens toevoegen',
			'Edit wish' => 'Wens aanpassen',
			'Strike off' => 'Afstrepen',
			'Add comment' => 'Opmerking toevoegen',
			'Comment_text_placeholder' => 'Ik ga boeken 2 en 3 cadeau doen',
			'Struck wishes' => 'Afgestreepte wensen',
			'Unstrike' => 'Ontafstrepen',
			'spoiler_alert_confirm' => 'Dat is jouw eigen lijst! Weet je zeker dat je potentiële verrassingen wil inzien?',
			'Personal' => 'Persoonlijk',
			"Only visible to you and the list's owner" => "Enkel zichtbaar voor jou en de eigenaar van de lijst",
			'Title' => 'Titel',
			'Description' => 'Beschrijving',
			'title placeholder' => 'Harry Potter boeken 2 t/m 7',
			'description placeholder' => 'Verdere beschrijving of details van het cadeau-idee',
			'Add wish to your list without visiting' => '<b>Voeg een wens aan jouw lijst toe</b>,<br>zonder die te hoeven openen en potentiëel een verrassing te zien.',
			'Wish added to list' => 'De wens is aan jouw lijst toegevoegd!',
			'Are you sure you want to delete this?' => 'Weet je zeker dat je dit permanent wil verwijderen?',
			'Added user with ID' => 'Gebruiker toegevoegd met ID',
			'Choose a list' => 'Kies wiens verlanglijst je wil bekijken',
			'Add a wish' => 'Voeg een wens toe',
			'Wishlist of' => 'Verlanglijst van',
			'Back to the overview' => 'Terug naar het overzicht',
			'Are you sure to strike this' => 'Weet je zeker dat je dit van de lijst wil verwijderen?',
			'Are you sure to unstrike this' => 'Weet je zeker dat je deze wens terug op de lijst wil plaatsen?',
			'Add a comment to this wish' => 'Voeg een opmerking aan deze wens toe',
			'Added by' => 'Toegevoegd door',
			'Edited by' => 'Bewerkt door',
			'commented' => 'schreef',
			'Picture' => 'Afbeelding',
			'Personal wish (only visible to A and B)' => 'Persoonlijke wens (alleen zichtbaar voor PERSON1 en PERSON2)',
			'Delete current image' => 'Huidige afbeelding verwijderen',
			'su' => 'su',
			'su-title' => '&quot;su&quot; is een oud Unix-commando om administratorrechten te gebruiken',
			'Leave administrator mode' => 'Administratormodus verlaten',
		],
	];
