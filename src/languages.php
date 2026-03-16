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

			'Add a comment to this wish' => 'Add a comment for this wish',
			'Add a wish' => 'Add a wish',
			'Add comment' => 'Add comment',
			'Added by' => 'Added by',
			'Added user with ID' => 'Added user with ID',
			'Add wish' => 'Add wish',
			'Add wish to your list without visiting' => '<b>Add a wish to your list</b>,<br>without needing to open it and potentially spoil surprises.',
			'administration' => 'administration',
			'Are you sure to strike this' => 'Are you sure you want to strike this item from the list?',
			'Are you sure to unstrike this' => 'Are you sure that you want to put this wish back on the list?',
			'Are you sure you want to delete this?' => 'Are you sure you want to permanently delete this?',
			'Back to the overview' => 'Back to the overview',
			'Back to wishlist' => 'Back to the wishlist',
			'By' => 'By',
			'Choose a list' => 'Choose whose wishlist you want to view',
			'Commented' => 'Commented',
			'Comment_text_placeholder' => 'I will gift books 2 and 3',
			'Create secret link' => 'Enable a secret login link',
			'Delete current image' => 'Delete current image',
			'Delete' => 'Delete',
			'deleted login link' => 'Your secret login link was last used on [lastuse] and has been disabled due to inactivity.',
			'Delete secret link' => 'Disable this secret login link',
			'Description' => 'Description',
			'description placeholder' => 'Optional description or further details of the present idea',
			'Edited by' => 'Edited by',
			'Edit' => 'Edit',
			'Edit wish' => 'Edit wish',
			'Email address cannot be empty' => 'Email address cannot be empty',
			'Email address:' => 'Email address:',
			'It was last used or enabled on:' => 'It was last used or enabled on:',
			'Leave administrator mode' => 'Leave administrator mode',
			'Login email failed to send' => 'Login email failed to send',
			'login email' => 'login email',
			'login email text' => 'A login link was requested for your email address. Click here to log in to [APPNAME]:',
			'login link info' => "You can bookmark a secret link that can be used to log in automatically. Be aware that if someone gets access to this link, they have full access to your account and everyone else's wishlist! Use this feature with care and disable it when not in use. It will automatically disable itself when not used for [unusedYears] years.",
			'Login link sent' => 'Login link sent',
			'login link unknown error' => 'Unknown login link. Possibly the link is expired, already used, or was not copied in full',
			'Log in' => 'Log in',
			'Log in to' => 'Log in to',
			'Log in with password' => 'Log in with password',
			'Logout failed' => 'Logout failed',
			'Log out' => 'Log out',
			'Logout successful' => 'Logout successful',
			'never' => 'never',
			'No secret login link is active for your account.' => 'No reuable login link is active for your account.',
			"Only visible to you and the list's owner" => "Only visible to you and the list's owner",
			'Or log in with password:' => 'Or log in with password:',
			'Personal' => 'Personal',
			'Personal wish (only visible to A and B)' => 'Personal wish (only visible to PERSON1 and PERSON2)',
			'person has no wishes' => 'No wishes are currently registered for this person',
			'personid not found' => 'A person with the provided ID could not be found',
			'Picture' => 'Picture',
			'profile' => 'profile',
			'recreate link howto' => 'To generate a new link, disable and enable it again.',
			'Save' => 'Save',
			'secret link active' => "Your secret login link is active. Copy <a href='[secretloginlink]'>this link</a>'s target to use it.",
			'Secret login link' => 'Secret login link',
			'Send login email' => 'Send login email',
			'spoiler_alert_confirm' => 'That is your own list! Are you sure you want to spoil potential surprises?',
			'Strike off' => 'Strike off',
			'wish already unstruck.' => 'This wish is already put back on the list, perhaps in another tab or by another person.',
			'wish already struck.' => 'This wish is already struck, perhaps in another tab or by another person.',
			'Struck wishes' => 'Struck wishes',
			'su' => 'su',
			'su-title' => '&quot;su&quot; is an old Unix command for using administrative permissions',
			'title placeholder' => 'Harry Potter books 2 through 7',
			'Title' => 'Title',
			'Unknown email address' => 'Unknown email address',
			'Unstrike' => 'Un-strike',
			'Welcome' => 'Welcome',
			'Wish added to list' => 'The wish was added to your list!',
			'wishid not found' => 'The wish ID could not be found. The item may have been deleted after you opened the previous page.',
			'Wishlist of' => 'Wishlist of',
		],

		'de' => [
			'_name' => 'Deutsch',
			'_fallback' => 'en',

			'Add a comment to this wish' => 'Einen Kommentar zu diesem Wunsch hinzufügen',
			'Add a wish' => 'Einen Wunsch hinzufügen',
			'Add comment' => 'Kommentar hinzufügen',
			'Added by' => 'Hinzugefügt von',
			'Add wish to your list without visiting' => '<b>Füge einen Wunsch zu Deiner Liste hinzu</b><br>ohne diese öffnen zu müssen und potenziell eine Überraschung zu verderben.',
			'Add wish' => 'Wunsch hinzufügen',
			'administration' => 'Administration',
			'Are you sure to strike this' => 'Sicher, dass Du dies abhaken möchtest?',
			'Are you sure to unstrike this' => 'Sicher, dass Du diesen Wunsch wieder zu der Liste hinzufügen möchtest?',
			'Are you sure you want to delete this?' => 'Sicher, dass du dies endgültig entfernen möchtest?',
			'Back to the overview' => 'Zurück zur Übersicht',
			'Back to wishlist' => 'Zurück zum Wunschliste',
			'Choose a list' => 'Wähle aus, wessen Wunschliste du sehen möchtest',
			'Commented' => 'Kommentar von',
			'commented' => 'kommentiert',
			'Comment_text_placeholder' => 'Ich werde Bücher 2 und 3 schenken',
			'Create secret link' => 'Geheimer Einloglink einschalten',
			'Delete current image' => 'Jetziges Bild entfernen',
			'deleted login link' => 'Dein geheimer Einloglink wurde [lastuse] zuletzt benutzt und wurde wegen des Nichtgebrauchs ausgeschaltet.',
			'Delete' => 'Entfernen',
			'Delete secret link' => 'Geheimer Einloglink ausschalten',
			'Description' => 'Beschreibung',
			'description placeholder' => 'Extra Beschreibung oder weitere Details zur Geschenk-Idee',
			'Edit' => 'Bearbeiten',
			'Edited by' => 'Bearbeitet von',
			'Edit wish' => 'Wunsch bearbeiten',
			'Email address cannot be empty' => 'E-Mail-Adresse darf nicht leer sein',
			'Email address:' => 'E-Mail-Adresse:',
			'It was last used or previously created on:' => 'Erstellt oder zuletzt benutzt (der jeweils spätere Zeitpunkt):',
			'Leave administrator mode' => 'Administrator-Modus verlassen',
			'Log in' => 'Anmelden',
			'login email text' => 'Einen Anmeldelink wurde für deine E-Mail-Adresse angefragt. Klick hier um dich bei [APPNAME] einzuloggen:',
			'login link info' => 'Du kannst einen geheimen Link als Lesezeichen speichern, um automatisch einzuloggen. Beachte dass, wenn jemand diesen Link in die Hände bekommt, er oder sie kompletten Zugang zu deinem Konto und alle Wunschlisten erhält. Benutze diese Funktion mit Vorsicht und schalte sie bei Nichtgebrauch aus. Sie deaktiviert sich selbst, wenn sie [unusedYears] Jahre lang nicht benutzt wird.',
			'Login link sent' => 'Anmeldelink verschickt',
			'login link unknown error' => 'Unbekannter Anmeldelink. Möglicherweise ist der Link abgelaufen, schon benutzt, oder nicht wurde nicht vollstandig kopiert',
			'Log in to' => 'Anmelden bei',
			'Log in with password' => 'Anmelden mit Passwort',
			'Log out' => 'Ausloggen',
			'Logout successful' => 'Erfolgreich abgemeldet',
			'never' => 'nie',
			'No secret login link is active for your account.' => 'Für dein Konto ist kein wiederverwendbarer Einloglink aktiv.',
			"Only visible to you and the list's owner" => 'Nur sichtbar für Dich und den/die Eigentümer*in der Liste',
			'Or log in with password:' => 'Oder melde dich an mit Passwort:',
			"Personal" => "Persönlich",
			'Personal wish (only visible to A and B)' => 'Persönlicher Wunsch (nur sichtbar für PERSON1 und PERSON2)',
			'person has no wishes' => 'Für diese Person sind im Moment keine Wünsche gespeichert',
			'personid not found' => 'Die Person mit der angegebenen ID konnte nicht gefunden werden',
			'Picture' => 'Bild',
			'profile' => 'Profil',
			'recreate link howto' => 'Um ein neuen Link zu generieren, schalte es aus und wieder ein.',
			'Save' => 'Speichern',
			'secret link active' => 'Dein geheimer Einloglink ist aktiv. Um es zu benutzen, kopiere die Adresse <a href="[secretloginlink]">dieser Link</a>.',
			'Secret login link' => 'Geheimer Einloglink',
			'Send login email' => 'Anmelde-E-Mail schicken',
			'spoiler_alert_confirm' => 'Das ist deine Liste! Sicher, dass du dir die Überraschung verderben willst?',
			'Strike off' => 'Abhaken',
			'wish already unstruck.' => 'Dieser Wunsch ist schon unabgehakt (zurück auf die Liste gesetzt), vielleicht in einem anderen Tab oder durch einer anderen Person.',
			'wish already struck.' => 'Dieser Wunsch ist schon abgehakt, vielleicht in einem anderen Tab oder durch einer anderen Person.',
			'Struck wishes' => 'Abgehakte Wünsche',
			'su' => 'su',
			'su-title' => '&quot;su&quot; ist ein altes Unix-Befehl um Administratorrechten zu benutzen',
			'title placeholder' => 'Harry Potter-Bücher 2 bis 7',
			'Title' => 'Überschrift',
			'Unknown email address' => 'Unbekannte E-Mail-Adresse',
			'Unstrike' => 'Unabhaken',
			'Welcome' => 'Hallo',
			'Wish added to list' => 'Der Wunsch wurde zu Deiner Liste hinzugefügt!',
			'wishid not found' => 'Der Wunsch mit der angegebenen ID konnte nicht gefunden werden. Der Eintrag wurde in der Zwischenzeit möglich entfernt, nachdem du die vorherige Seite geöffnet hast.',
			'Wishlist of' => 'Wunschliste von',
		],

		'nl' => [
			'_name' => 'Nederlands',
			'_fallback' => 'en',

			'Add a comment to this wish' => 'Voeg een opmerking aan deze wens toe',
			'Add a wish' => 'Voeg een wens toe',
			'Add comment' => 'Opmerking toevoegen',
			'Added by' => 'Toegevoegd door',
			'Added user with ID' => 'Gebruiker toegevoegd met ID',
			'Add wish to your list without visiting' => '<b>Voeg een wens aan jouw lijst toe</b>,<br>zonder die te hoeven openen en potentiëel een verrassing te zien.',
			'Add wish' => 'Wens toevoegen',
			'administration' => 'beheer',
			'Are you sure to strike this' => 'Weet je zeker dat je dit van de lijst wil afstrepen?',
			'Are you sure to unstrike this' => 'Weet je zeker dat je deze wens terug op de lijst wil plaatsen?',
			'Are you sure you want to delete this?' => 'Weet je zeker dat je dit permanent wil verwijderen?',
			'Back to the overview' => 'Terug naar het overzicht',
			'Back to wishlist' => 'Terug naar de verlanglijst',
			'Choose a list' => 'Kies wiens verlanglijst je wil bekijken',
			'commented' => 'schreef',
			'Comment_text_placeholder' => 'Ik ga boeken 2 en 3 cadeau doen',
			'Create secret link' => 'Inloglink inschakelen',
			'Delete current image' => 'Huidige afbeelding verwijderen',
			'deleted login link' => 'Jouw geheime inloglink werd voor het laatst gebruikt op [lastuse] en is vanwege inactiviteit automatisch uitgeschakeld.',
			'Delete secret link' => 'Inloglink uitschakelen',
			'Delete' => 'Verwijderen',
			'Description' => 'Beschrijving',
			'description placeholder' => 'Verdere beschrijving of details van het cadeau-idee',
			'Edit' => 'Bewerken',
			'Edited by' => 'Bewerkt door',
			'Edit wish' => 'Wens aanpassen',
			'Email address cannot be empty' => 'E-mailadres mag niet leeg zijn',
			'Email address:' => 'E-mailadres:',
			'It was last used or previously created on:' => 'Aanmaakdatum of laatste gebruik (de recentste van de twee):',
			'Leave administrator mode' => 'Administratormodus verlaten',
			'login email text' => 'Een inloglink is voor jouw e-mailadres aangevraagd. Klik hier om bij [APPNAME] in te loggen:',
			'Log in' => 'Inloggen',
			'login link info' => "Je kan een geheime link aan jouw favorieten toevoegen om daarmee automatisch in te loggen. Let op: indien iemand de link in handen krijgt, heeft diegene volledige toegang tot jouw account en alle andere verlanglijstjes. Ben voorzichtig met deze functie en schakel het uit wanneer je het niet gebruikt. Als het niet gebruikt wordt, schakelt het zichzelf na [unusedYears] jaar uit.",
			'Login link sent' => 'Inloglink verstuurd',
			'login link unknown error' => 'Onbekende inloglink. Wellicht is de link verlopen, al gebruikt, of niet volledig gekopiëerd',
			'Log in to' => 'Inloggen op',
			'Log in with password' => 'Inloggen met wachtwoord',
			'Logout successful' => 'Uitloggen gelukt',
			'Log out' => 'Uitloggen',
			'never' => 'nooit',
			'No secret login link is active for your account.' => 'Geen herbruikbare inloglink is actief voor jouw account.',
			"Only visible to you and the list's owner" => "Enkel zichtbaar voor jou en de eigenaar van de lijst",
			'Or log in with password:' => 'Of log in met wachtwoord:',
			'Personal' => 'Persoonlijk',
			'Personal wish (only visible to A and B)' => 'Persoonlijke wens (alleen zichtbaar voor PERSON1 en PERSON2)',
			'person has no wishes' => 'Er zijn momenteel geen wensen bekend voor deze persoon',
			'personid not found' => 'De persoon met het opgegeven ID kon niet worden gevonden',
			'Picture' => 'Afbeelding',
			'profile' => 'profiel',
			'recreate link howto' => 'Een nieuwe link genereren kan door de functie uit en weer in te schakelen.',
			'Save' => 'Opslaan',
			'secret link active' => 'Een geheime inloglink is actief. Kopieer het doel van <a href="[secretloginlink]">deze link</a> om het te gebruiken.',
			'Secret login link' => 'Geheime inloglink',
			'Send login email' => 'Stuur inlog-e-mail',
			'spoiler_alert_confirm' => 'Dat is jouw eigen lijst! Weet je zeker dat je potentiële verrassingen wil inzien?',
			'Strike off' => 'Afstrepen',
			'wish already unstruck.' => 'Deze wens is al onafgestreept (terug op de lijst gezet), wellicht in een andere tab of door een andere persoon.',
			'wish already struck.' => 'Deze wens is al afgestreept, wellicht in een andere tab of door een andere persoon.',
			'Struck wishes' => 'Afgestreepte wensen',
			'su' => 'su',
			'su-title' => '&quot;su&quot; is een oud Unix-commando om administratorrechten te gebruiken',
			'title placeholder' => 'Harry Potter boeken 2 t/m 7',
			'Title' => 'Titel',
			'Unknown email address' => 'Onbekend e-mailadres',
			'Unstrike' => 'Onafstrepen',
			'Welcome' => 'Welkom',
			'Wish added to list' => 'De wens is aan jouw lijst toegevoegd!',
			'wishid not found' => 'De wens met het gegeven ID kon niet worden gevonden. Het item is mogelijk verwijderd in de tussentijd, nadat je de vorige pagina opende.',
			'Wishlist of' => 'Verlanglijst van',
		],
	];
