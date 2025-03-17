<?php
	if ( ! isset($included)) {
		die('!included');
	}

	if (isset($_GET['pic'])) {
		// allow safe SVG hosting
		header("Content-Security-Policy: script-src 'none'; object-src 'none'; report-uri ?cspreport");

		$wishid_int = intval($_GET['pic']);
		list($picturetype, $picture) = db("SELECT picturetype, picture FROM wishes WHERE id = '$wishid_int'")->fetch_row();
		header('Content-Type: image/' . $picturetype);
		print($picture);
		exit;
	}

	$loggedinuserid_int = intval($_SESSION['userid']);

	$personid_int = intval($_GET['person']);
	$result = db("SELECT name, color FROM users WHERE id = '$personid_int'");
	if ($result->num_rows != 1) {
		die(tr('personid not found'));
	}
	list($listownername, $listownercolor) = $result->fetch_row();
	$listownercolor_htmlescaped = htmlescape($listownercolor);
	$listownername_htmlescaped = htmlescape($listownername);

	if (isset($_POST['comment'])) {
		$wishid_int = intval($_POST['wishid']);
		$time = time();
		$comment = sqlescape($_POST['comment']);
		db("INSERT INTO comments (for_wish, added_timestamp, added_by, comment)
			VALUES('$wishid_int', $time, '$loggedinuserid_int', '$comment')");
	}

	if (isset($_POST['deleteWish'])) {
		$deleteid_int = intval($_POST['deleteWish']);
		$adminOrSelf = '';
		if ($_SESSION['isadmin'] !== true) {
			$adminOrSelf = 'AND added_by = ' . intval($_SESSION['userid']);
		}
		db("
			DELETE FROM wishes
			WHERE struck = 1
				$adminOrSelf
				AND id = '$deleteid_int'
		");
		// delete any (now-)orphan comments
		db("
			DELETE c FROM comments AS c
			WHERE (SELECT COUNT(*) FROM wishes w WHERE w.id = c.for_wish) = 0
		");
	}

	if (isset($_POST['deleteComment'])) {
		$deleteid_int = intval($_POST['deleteComment']);
		db("
			DELETE FROM comments
			WHERE added_by = '$loggedinuserid_int'
				AND id = '$deleteid_int'
		");
	}

	if (isset($_POST['toggleStrike'])) {
		$time = time();
		$wishid_int = intval($_POST['toggleStrike']);
		$loggedinuserid_int = intval($_SESSION['userid']);
		db("
			UPDATE wishes
			SET
				struck = !struck,
				edited_by = '$loggedinuserid_int',
				edited_timestamp = '$time'
			WHERE id = '$wishid_int'
		");
	}

	if (isset($_POST['title'])) {
		$time = time();
		$loggedinuserid_int = intval($_SESSION['userid']);
		$title_dbescaped = sqlescape($_POST['title']);
		$description_dbescaped = sqlescape($_POST['description']);
		$personal = (isset($_POST['personal']) ? 1 : 0);
		$addpic = false;
		$delpic = (isset($_POST['delpic']) ? true : false);

		$picdata_dbquoted = 'NULL';
		$pictype_dbquoted = 'NULL';
		if (isset($_FILES['pic']) && $_FILES['pic']['size'] > 0) {
			// TODO make a nicer flow for error conditions, perhaps check the size client-side with JS and trust the accept='*' field on the file upload so all checking is already client-side and so any errors are intentional on the user's part and don't need a nice flow
			if ($_FILES['pic']['size'] >= 1024 * 1024 * 16) {
				die('Picture too large');
			}
			// We cannot just allow MIME image/* because a type such as image/svg+xml is dangerous and needs special handling. We've got SVG handling now, but there might be more such types (now or later).
			$imagetype = explode('/', $_FILES['pic']['type'])[1];
			if ( ! in_array($imagetype, ['png', 'jpeg', 'webp', 'avif', 'svg+xml', 'gif', 'bmp', 'tiff'])) {
				die('Disallowed file type. The file selector should show exactly the types that are allowed.');
			}
			$picdata_dbquoted = '"' . sqlescape(file_get_contents($_FILES['pic']['tmp_name'])) . '"';
			$pictype_dbquoted = '"' . sqlescape($imagetype) . '"';
			$addpic = true;
		}
		if ($_POST['wishid'] == -1) {
			db("
				INSERT INTO wishes (`for`, title, description, added_by, added_timestamp, personal, picture, picturetype)
				VALUES ('$personid_int', '$title_dbescaped', '$description_dbescaped', '$loggedinuserid_int', '$time', '$personal', $picdata_dbquoted, $pictype_dbquoted)
			");
		}
		else {
			$wishid_int = intval($_POST['wishid']);
			db("
				UPDATE wishes
				SET
					title = '$title_dbescaped',
					description = '$description_dbescaped',
					edited_by = '$loggedinuserid_int',
					edited_timestamp = '$time',
					personal = '$personal'
				WHERE id = '$wishid_int'
					AND (personal = 0 OR added_by = '$loggedinuserid_int')
			");
			if ($addpic || $delpic) {
				db("
					UPDATE wishes
					SET
						picture = $picdata_dbquoted,
						picturetype = $pictype_dbquoted
					WHERE id = '$wishid_int'
				");
			}
		}

		if (isset($_POST['backToOverview'])) {
			header('Location: ?addedWish');
			exit;
		}
	}

	$R_U_sure_delete = tr('Are you sure you want to delete this?');

	$active_wishes = '';
	$struck_wishes = '';

	$result = db("
		SELECT id,
			title,
			description,
			added_by,
			added_timestamp,
			(SELECT name FROM users u WHERE u.id = w.added_by) AS added_by_name,
			(SELECT color FROM users u WHERE u.id = w.added_by) AS added_by_color,
			edited_by,
			edited_timestamp,
			(SELECT name FROM users u WHERE u.id = w.edited_by) AS edited_by_name,
			(SELECT color FROM users u WHERE u.id = w.edited_by) AS edited_by_color,
			struck,
			personal,
			picturetype
		FROM wishes w
		WHERE `for` = '$personid_int'
			AND (personal = 0 OR added_by = '$loggedinuserid_int')
		ORDER BY struck ASC, added_timestamp DESC
	");
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$wishid_int = intval($row['id']);
			$struck = ($row['struck'] == 1);

			$Edit = tr('Edit');
			$Strike = ($struck ? tr('Unstrike') : tr('Strike off'));
			$Comment = tr('Add comment');
			$Comment_text_placeholder = tr('Comment_text_placeholder');
			$R_U_sure_strike = ($row['struck'] == 1 ? tr('Are you sure to unstrike this') : tr('Are you sure to strike this'));

			if ($_SESSION['isadmin'] === true || $row['added_by'] == $_SESSION['userid']) {
				$Delete = tr('Delete');
				$deletewishbtn = "
					<form class='inline delete' method=POST onsubmit='return confirm(\"$R_U_sure_delete\");'>
						<input type=hidden name=deleteWish value='$wishid_int'>
						<input type=submit value='$Delete'>
					</form>
				";
			}

			$comments = '';
			$commentsresult = db("
				SELECT
					id,
					comment,
					added_by,
					added_timestamp,
					(SELECT name FROM users u WHERE u.id = c.added_by) AS added_by_name,
					(SELECT color FROM users u WHERE u.id = c.added_by) AS added_by_color
				FROM comments c
				WHERE for_wish = '$wishid_int'
				ORDER BY added_timestamp DESC
			");
			$commented = tr('Commented');
			while ($commentrow = $commentsresult->fetch_assoc()) {
				$datetime = date('Y-m-d', $commentrow['added_timestamp']);
				$commenter_color_htmlescaped = htmlescape($commentrow['added_by_color']);
				$commenter_htmlescaped = htmlescape($commentrow['added_by_name']);
				$comments .= "$datetime $commented <span style='color: $commenter_color_htmlescaped;'>$commenter_htmlescaped</span>: <i>" . htmlescape($commentrow['comment']) . '</i>';
				if ($commentrow['added_by'] == $_SESSION['userid']) {
					$commentid_int = intval($commentrow['id']);
					$comments .= "
						<form method=POST class=inline onsubmit='return confirm(\"$R_U_sure_delete\");'>
							<input type=hidden name=deleteComment value='$commentid_int'>
							<input type=submit value='&times;'>
						</form>
					";
				}
				$comments .= '<br>';
			}

			$added_by = tr('Added by');
			$added_on = date('Y-m-d', $row['added_timestamp']);
			$added_by_color_htmlescaped = htmlescape($row['added_by_color']);
			$added_by_htmlescaped = htmlescape($row['added_by_name']);
			$added_by_us = ($row['added_by'] == $_SESSION['userid'] ? 1 : 0);
			$added = "$added_on $added_by: <span style='color: $added_by_color_htmlescaped;'>$added_by_htmlescaped</span><br>";

			if ($row['edited_timestamp'] != null) {
				$edited_by = tr('Edited by');
				$edited_on = date('Y-m-d', $row['edited_timestamp']);
				$edited_by_color_htmlescaped = htmlescape($row['edited_by_color']);
				$edited_by_htmlescaped = htmlescape($row['edited_by_name']);
				$edited = "$edited_on $edited_by: <span style='color: $edited_by_color_htmlescaped;'>$edited_by_htmlescaped</span><br>";
			}

			$personalClass = '';
			$personalText = '';
			if ($row['personal'] == 1) {
				$personalClass = ' personal';
				$personalText = str_replace('PERSON1', "<span style='color: $added_by_color_htmlescaped;'>$added_by_htmlescaped</span>",
				                    str_replace('PERSON2', "<span style='color: $listownercolor_htmlescaped;'>$listownername_htmlescaped</span>",
				                        tr('Personal (this wish is only visible to PERSON1 and PERSON2)')))
				                . '<br>';
			}

			$struckClass = '';
			if ($struck) {
				$struckClass = ' struck';
			}

			$pic = '';
			if ($row['picturetype'] != null) {
				$pic = "<img src='?person=$personid_int&pic=$wishid_int'>";
			}

			$title_htmlescaped = htmlescape($row['title']);
			$description_htmlescaped = htmlescape($row['description']);
			if (strlen($row['description']) > 0) {
				$description_rendered = '<div class=wishinfo>' . linkifiedbbcode($row['description']) . '</div>';
			}
			else {
				$description_rendered = '';
			}
			$html = "
				<section id='wish$wishid_int' class='wish$personalClass$struckClass' data-id='$wishid_int' data-descriptionbb='$description_htmlescaped' data-addedbyus=$added_by_us>
					<div class=titleandbuttons>
						<div class=title>$title_htmlescaped</div>
						<button class=edit>$Edit</button>
						<form class=inline method=POST onsubmit='return confirm(\"$R_U_sure_strike\");'>
							<input type=hidden name=toggleStrike value='$wishid_int'>
							<input type=submit value='$Strike'>
						</form>
						<button class=comment>$Comment</button>
						$deletewishbtn
					</div>
					$pic
					$description_rendered
					$added$edited
					$personalText
					$comments
				</section>
			";

			if ($struck) {
				if (strlen($struck_wishes) == 0) {
					$struck_wishes = '<hr><h2>' . tr('Struck wishes') . '</h2>';
				}
				$struck_wishes .= $html;
			}
			else {
				$active_wishes .= $html;
			}
		}
	}
	if (strlen($active_wishes) == 0) {
		$active_wishes = tr('person has no wishes');
	}

	renderHeader($title=$listownername);

	?>
		<a href='.'>&#x25C2; <?php echo tr('Back to the overview'); ?></a><br><br>

		<h2>
			<?php echo tr('Wishlist of'); ?>
			<span style="color: <?php echo $listownercolor_htmlescaped; ?>"><?php echo htmlescape($listownername); ?></span>
		</h2>

		<section>
			<button id=addwish class=jumbobutton><?php echo tr('Add a wish'); ?></button>
		</section>

		<?php echo $active_wishes; ?>

		<?php echo $struck_wishes; ?>

		<dialog id=addoreditmodal>
			<button class=close>&times;</button>
			<form method=POST enctype='multipart/form-data'>
				<strong>
					<span class=add><?php echo tr('Add wish'); ?></span>
					<span class=edit style="display: none;"><?php echo tr('Edit wish'); ?></span>
				</strong><br><br>

				<?php $autofocus=true; require('src/add-or-edit-form.php'); ?>
			</form>
		</dialog>

		<dialog id=commentmodal>
			<button class=close>&times;</button>
			<form method=POST onsubmit='location.hash = "wish" + querySelector("#commentmodal input[name=wishid]").value; return true;'>
				<input type=hidden name=wishid value=-1>

				<div class=wishinfo><span class=title></span></div><br>

				<?php echo tr('Add a comment to this wish'); ?>:<br>
				<input type=text name=comment class=comment placeholder="<?php echo tr('Comment_text_placeholder'); ?>" required autofocus><br><br>

				<input type=submit value='<?php echo tr('Add comment'); ?>'>
			</form>
		</dialog>

		<script>
			let aoemodal = document.querySelector('#addoreditmodal');
			let aoewishidfield = aoemodal.querySelector('input[name=wishid]');
			let cmodal = document.querySelector('#commentmodal');

			document.querySelectorAll('button.close').forEach(function(btn) {
				btn.addEventListener('click', function(ev) {
					ev.target.parentNode.close();
				});
			});

			document.querySelectorAll('.wish .comment').forEach(function(btn) {
				btn.addEventListener('click', function(ev) {
					let wishel = btn.closest('.wish');
					cmodal.querySelector('input[name=wishid]').value = wishel.dataset.id;
					cmodal.querySelector('.title').innerText = wishel.querySelector('.title').innerText;
					cmodal.showModal();
				});
			});

			document.querySelectorAll('.wish .edit').forEach(function(btn) {
				btn.addEventListener('click', function(ev) {
					let wishel = btn.closest('.wish');
					if (aoewishidfield.value == wishel.dataset.id) {
						// they were already editing this wish: don't overwrite what they entered
						aoemodal.showModal();
						return;
					}
					aoewishidfield.value = wishel.dataset.id;
					aoemodal.querySelector('input[name=title]').value = wishel.querySelector('.title').innerText;
					aoemodal.querySelector('textarea[name=description]').value = wishel.dataset.descriptionbb;
					aoemodal.querySelector('input[name=personal]').checked = wishel.classList.contains('personal');
					aoemodal.querySelector('input.add').style.display = 'none';
					aoemodal.querySelector('span.add').style.display = 'none';
					aoemodal.querySelector('input.edit').style.display = 'inline';
					aoemodal.querySelector('span.edit').style.display = 'inline';
					if (wishel.querySelector('img')) {
						aoemodal.querySelector('img').src = wishel.querySelector('img').src;
						aoemodal.querySelector('.delpic').style.display = 'inline';
					}
					else {
						aoemodal.querySelector('.delpic').style.display = 'none';
					}
					if (wishel.dataset.addedbyus == 1) {
						aoemodal.querySelector('span.personal').style.display = 'inline';
					}
					else {
						aoemodal.querySelector('span.personal').style.display = 'none';
					}
					aoemodal.showModal();
				});
			});

			document.querySelector('#addwish').addEventListener('click', function(ev) {
				aoewishidfield.value = -1;
				aoemodal.querySelector('input[name=title]').value = '';
				aoemodal.querySelector('textarea[name=description]').value = '';
				aoemodal.querySelector('input[name=personal]').checked = false;
				aoemodal.querySelector('input.add').style.display = 'inline';
				aoemodal.querySelector('span.add').style.display = 'inline';
				aoemodal.querySelector('input.edit').style.display = 'none';
				aoemodal.querySelector('span.edit').style.display = 'none';
				aoemodal.querySelector('.delpic').style.display = 'none';
				aoemodal.querySelector('span.personal').style.display = 'inline';
				aoemodal.showModal();
			});

			document.querySelectorAll('img').forEach(function(img) {
				img.addEventListener('click', function(ev) {
					img.classList.toggle('fullsize');
				});
			});

			addEventListener('keydown', function(ev) {
				if (ev.keyCode == 65) {
					if (['INPUT', 'TEXTAREA'].indexOf(document.activeElement.tagName.toUpperCase()) === -1) {
						document.querySelector('#addwish').click();
						ev.preventDefault();
						return false;
					}
				}
			});
		</script>
	<?php

	renderFooter();

