<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_manual.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - Export manual
// +-------------------------------------------------------------+

/**
 * Import a manual from a file
 *
 * The callback function can provide updates for the user. It
 * must accept two strings: The first is the currently processed item, and
 * the second is the update type, either a 'proc' (ex. 'comments', 'pages')
 * or a 'current' (ex. comment set 1 of 10)
 *
 * @param string The file where the XML export is located
 * @param string The directory where image data is held, if importing images
 * @param callback The callback function to provide feedback to user
 */
function import_manual($filepath, $imagedir = '', $callback = false) {

	global $db, $settings;

	/******************************
	* Check callback
	******************************/

	// If its not a valid callbakc, just create an empty one
	if (!is_callable($callback)) {
		$callback = create_function('$info, $type', '');
	}


	/******************************
	* Get the XML file and data
	******************************/

	require_once(INC . 'classes/class_XMLDecode.php');
	$xml = new class_XMLDecode();
	$data = $xml->parse_xml(file_get_contents($filepath));
	unset($xml);

	if (!$data) {
		call_user_func($callback, "No data to import -- please make sure you specified a correct manual XML file.", 'message');
		call_user_func($callback, "(Tried reading from file: $filepath)", 'message');
		return;
	}

	$data_pages = $data['pages']['page'];
	unset($data['pages']);
	if ($data_pages['id']) {
		$data_pages = array($data_pages);
	}

	if (!$data_pages) {
		call_user_func($callback, '[Warning] No pages were imported -- no page data.', 'message');
	}

	$data_com = $data['comments']['comment'];
	unset($data['comments']);
	if ($data_com['pageid']) {
		$data_com = array($data_com);
	}

	$data_rev = $data['revisions']['revision'];
	unset($data['revisions']);
	if ($data_rev['revisionid']) {
		$data_rev = array($data_rev);
	}

	$data_img = $data['images']['image'];
	unset($data['images']);
	if ($data_img['content_id']) {
		$data_img = array($data_img);
	}

	$data_styles = $data['styles']['style'];
	unset($data['styles']);
	if ($data_styles['title']) {
		$data_styles = array($data_styles);
	}

	$data_man = $data;

	unset($data);


	/******************************
	* Create the manual
	******************************/

	call_user_func($callback, 'Manual', 'proc');
	call_user_func($callback, 'Creating manual', 'current');

	$db->query("
		INSERT INTO manual_manuals SET
			name = '" . $db->escape($data_man['name']) . "',
			description = '" . $db->escape($data_man['description']['value']) . "',
			published = '" . intval((bool) $data_man['published']) . "',
			displayorder = '" . intval($data_man['displayorder']) . "'
	");

	$manualid = $db->insert_id();


	/******************************
	* Import parts
	******************************/

	$info = array(
		'manualid' => $manualid,
		'makerev' => !empty($data_rev),
		'imagedir' => $imagedir,
		'origurl' => $data_man['helpdeskurl'],
		'newurl' => $settings['helpdesk_url'],
	);

	$idmap = import_manual_pages($data_pages, $info, $callback);

	$info['idmap'] = $idmap;

	if ($data_rev) {
		import_manual_revisions($data_rev, $info, $callback);
	}

	if ($data_com) {
		import_manual_comments($data_com, $info, $callback);
	}

	if ($data_img) {
		$imageidmap = import_manual_images($data_img, $info, $callback);
	} else {
		$imageidmap = array();
	}

	if ($data_styles) {
		import_manual_styles($data_styles, $info, $callback);
	}

	import_manual_update_content_ids($idmap, $imageidmap, $info, $callback);
}





/**
 * Import manual styles
 *
 * $info should contain
 *  - manualid: The manualid to import into
 *
 * @param array Array of style data
 * @param array Misc information
 * @param callback The callback function to call with progress updates
 */
function import_manual_styles($data, $info, $callback) {

	global $db;

	call_user_func($callback, 'Manual Styles', 'proc');
	call_user_func($callback, 'Loading...', 'current');

	$manualid = $info['manualid'];

	/******************************
	* Loop through styles
	******************************/

	for ($i = 0, $num = count($data); $i < $num; $i++) {

		$style = $data[$i];


		/******************************
		* Get attributes
		******************************/

		$attributes = array();

		if ($style['attributes']) {

			if ($style['attributes']['attribute']['attr']) {
				$style['attributes']['attribute'] = array($style['attributes']['attribute']);
			}

			foreach ($style['attributes']['attribute'] as $attr) {
				$attributes[$attr['attr']] = $attr['val'];
			}

		}


		/******************************
		* Get CSS
		******************************/

		$css = '';

		if ($style['css']) {
			$css = $style['css']['value'];
		}


		/******************************
		* Insert into db
		******************************/

		$db->query("
			INSERT INTO manual_manual_styles SET
				manualid = $manualid,
				title = '" . $db->escape($style['title']) . "',
				name = '" . $db->escape($style['name']) . "',
				element = '" . $db->escape($style['element']) . "',
				attributes = '" . $db->escape(serialize($attributes)) . "',
				css = '" . $db->escape($css) . "'
		");
	}
}





/**
 * Import manual pages
 *
 * $info should contain:
 *  - manualid: The manualid to import into
 *  - makerev: To create the first revision or not (should not if importing revisions later)
 *
 * Function returns an 'ID Map'. This map is an array of oldid=>newid, since we cannot keep
 * the old page ID's used on the original manual. We need this map to make sure imported
 * comments, revisions, images are connected to the correct page.
 *
 * @param array Array of page data
 * @param array Misc information
 * @param callback The callback function to call with progress updates
 *
 * @return array The 'idmap'
 */
function import_manual_pages($data, $info, $callback) {

	global $db, $user;


	/******************************
	* Call callbacks, init vars
	******************************/

	call_user_func($callback, 'Pages', 'proc');
	call_user_func($callback, 'Loading...', 'current');

	$manualid = $info['manualid'];
	$makerev = $info['makerev'];

	// This maps IDs from the XML file
	// into the new IDs when we insert them into the DB now
	// oldid=>newid
	$idmap = array();

	// Counts how many children each page has
	// If no children, dont need to update with new ID
	$id_child_count = array();


	/******************************
	* Loop through pages
	******************************/

	for ($i = 0, $num = count($data); $i < $num; $i++) {

		$p = $data[$i];

		call_user_func($callback, $p['title'], 'current');

		// If not importing revisions,
		// then we need to make the first after we
		// have the new pageid
		if (!$makerev) {
			$p['revisionid'] = 1;
		}

		// Replace vars
		$p['value'] = manual_content_replace_varnames($p['value']);

		$db->query("
			INSERT INTO manual_pages SET
				parent = 0,
				displayorder = " . intval($p['displayorder']) . ",
				title = '" . $db->escape($p['title']) . "',
				content = '" . $db->escape($p['value']) . "',
				revisionid = " . intval($p['revisionid']) . ",
				timestamp_revision = " . intval($p['timestamp_revision']) .",
				timestamp_creation = " . intval($p['timestamp_creation']) .",
				manualid = $manualid,
				allow_comments = " . intval((bool) $p['allow_comments']) . ",
				published = " . intval((bool) $p['published']) . ",
				old_parent = " . intval($p['parent']) . "
		");

		$idmap[$p['id']] = $pageid = $db->insert_id();

		// If we're not importing revisions, then we
		// need to create this first rev (using this user as creator)
		if (!$makerev) {
			$db->query("
				INSERT INTO manual_revisions SET
					pageid = $pageid,
					revisionid = 1,
					content = '" . $db->escape($p['value']) . "',
					timestamp = " . TIMENOW . ",
					techid = $user[id]
			");
		}

		$id_child_count[$p['parent']]++;
		$id_child_count[$p['id']]++;
	}


	/******************************
	* Update parent ID's with new pageids
	******************************/

	call_user_func($callback, 'Updating page hierarchy with new page IDs', 'proc');
	call_user_func($callback, 'Loading...', 'current');

	foreach ($id_child_count as $id => $num) {

		if ($id == 0 || $num == 0) {
			continue;
		}

		$old_id = $id;
		$new_id = $idmap[$old_id];

		$db->query("
			UPDATE manual_pages
			SET parent = $new_id, old_parent = 0
			WHERE old_parent = $old_id
		");

	}


	return $idmap;
}





/**
 * Import manual revisions
 *
 * $info should contain:
 *  - manualid: The manualid to import into
 *  - idmap: The 'id map' generated when importing the pages
 *
 * @param array Array of revision data
 * @param array Misc information
 * @param callback The callback function to call with progress updates
 */
function import_manual_revisions($data, $info, $callback) {

	global $db;

	call_user_func($callback, 'Importing Revisions', 'proc');
	call_user_func($callback, 'Loading...', 'current');

	$idmap = $info['idmap'];

	for ($i = 0, $num = count($data); $i < $num; $i++) {

		if ($i % 100 == 0) {
			call_user_func($callback, 'Loading set ' . (($i%0) + 1) . ' of ' . (ceil($num / 100)), 'current');
		}

		/******************************
		* Get revision and map pageid to new ID
		******************************/

		$rev = $data[$i];

		if (!$idmap[$rev['pageid']]) {
			continue;
		}

		$rev['pageid'] = $idmap[$rev['pageid']];

		/******************************
		* Insert revision
		******************************/

		$db->query("
			INSERT INTO manual_revisions SET
				pageid = $rev[pageid],
				revisionid = " . intval($rev['revisionid']) . ",
				content = '" . $db->escape($rev['value']) . "',
				timestamp = " . intval($rev['timestamp']) . ",
				techid = " . intval($rev['techid']) . "
		");
	}
}





/**
 * Import manual comments
 *
 * $info should contain:
 *  - manualid: The manualid to import into
 *  - idmap: The 'id map' generated when importing the pages
 *
 * @param array Array of comment data
 * @param array Misc information
 * @param callback The callback function to call with progress updates
 */
function import_manual_comments($data, $info, $callback) {

	global $db;

	call_user_func($callback, 'Importing Comments', 'proc');
	call_user_func($callback, 'Loading...', 'current');

	$idmap = $info['idmap'];

	for ($i = 0, $num = count($data); $i < $num; $i++) {

		if ($i % 100 == 0) {
			call_user_func($callback, 'Loading set ' . (($i%0) + 1) . ' of ' . (ceil($num / 100)), 'current');
		}

		/******************************
		* Get comment and map pageid to new ID
		******************************/

		$comment = $data[$i];

		if (!$idmap[$comment['pageid']]) {
			continue;
		}

		$comment['pageid'] = $idmap[$comment['pageid']];

		/******************************
		* Insert comment
		******************************/

		$db->query("
			INSERT INTO manual_comments SET
				userid = " . intval($comment['userid']) . ",
				email = '" . $db->escape($comment['email']) . "',
				pageid = $comment[pageid],
				timestamp_created = " . intval($comment['timestamp_created']) . ",
				timestamp_validated = " . intval($comment['timestamp_validated']) . ",
				is_validated = " . intval((bool) $comment['is_validated']) . ",
				comments = '" . $db->escape($comment['value']) . "
		");
	}
}





/**
 * Import manual comments
 *
 * $info should contain:
 *  - manualid: The manualid to import into
 *  - idmap: The 'id map' generated when importing the pages
 *  - imagedir: The dir to read images from
 *
 * Function returns an 'image id map' that works similar to the id map
 * generated by the import_manual_pages function. This should be used
 * later in import_manual_updateids to update the ID's in the content.
 *
 * @param array Array of comment data
 * @param array Misc information
 * @param callback The callback function to call with progress updates
 *
 * @return array Image id map
 */
function import_manual_images($data, $info, $callback) {

	global $db;

	call_user_func($callback, 'Importing Images', 'proc');
	call_user_func($callback, 'Loading...', 'current');

	// Maps old image IDs to new IDs
	// pageid=>array(oldid=>newid)
	$imageidmap = array();

	$idmap = $info['idmap'];
	$data_len = $db->max_allowed_packet();
	$imagedir = $info['imagedir'];

	for ($i = 0, $num = count($data); $i < $num; $i++) {

		if ($i % 100 == 0) {
			call_user_func($callback, 'Loading set ' . (($i%0) + 1) . ' of ' . (ceil($num / 100)), 'current');
		}


		/******************************
		* Get image and map pageid to new ID
		******************************/

		$img = $data[$i];

		if (!$idmap[$img['content_id']]) {
			continue;
		}

		$img['content_id'] = $idmap[$img['content_id']];


		/******************************
		* Get image data and insert new blob
		******************************/

		$filename = $img['value'];
		$image_data = @file_get_contents($imagedir . '/' . $filename);

		if ($image_data === false) {
			call_user_func($callback, "[Importing Image] $filename does not exist in imagedir ($imagedir)", 'message');
			continue;
		}

		$image_data = str_split($image_data, $data_len);

		$db->query("INSERT INTO blobs SET thumbnail = ''");
		$blobid = $db->insert_id();


		/******************************
		* Insert blob parts
		******************************/

		for ($data_k = 0, $num_data = count($image_data); $data_k < $num_data; $data_k++) {
			$db->query("
				INSERT INTO blob_parts SET
					blobid = $blobid,
					blobdata = '" . $db->escape($image_data[$data_k]) . "',
					displayorder = $data_k
			");
		}


		/******************************
		* Save the image
		******************************/

		$db->query("
			INSERT INTO images SET
				blobid = $blobid,
				filename = '" . $db->escape($img['filename']) . "',
				filesize = " . intval($img['filesize']) . ",
				extension = '" . $db->escape($img['extension']) . "',
				content_type = 'manual_pages',
				content_id = $img[content_id],
				timestamp = " . intval($img['timestamp']) . "
		");

		$image_id = $db->insert_id();


		/******************************
		* Add to imageid map
		******************************/

		$imageidmap[$img['id']] = $image_id;
	}

	return $imageidmap;
}





/**
 * Updates the ID's in the content associated with the newly imported manual data.
 * This may be a slow process.
 *
 * Info should contain
 *  - manualid: The manualid
 *  - origurl: Original helpdesk URL
 *  - newurl: New helpdesk URL
 *
 * @param array The page id map
 * @param array The image id map
 * @param array Misc info
 * @param callback The callback function to call with progress updates
 */
function import_manual_update_content_ids($idmap, $imageidmap, $info, $callback) {

	global $db;

	call_user_func($callback, 'Updating IDs in the content', 'proc');
	call_user_func($callback, 'Loading...', 'current');

	$manualid = $info['manualid'];

	/******************************
	* Build the search and replace arrays
	******************************/

	$find = array();
	$replace = array();

	// Links to manual pages
	foreach ($idmap as $oldid => $newid) {
		$find[] = 'manual.php?p=' . $oldid . '"';
		$replace[] = 'manual.php?p=' . $newid . '"';
	}

	// Images
	foreach ($imageidmap as $oldid => $newid) {
		$find[] = 'getimage.php?id=' . $oldid . '"';
		$replace[] = 'getimage.php?id=' . $newid . '"';
	}


	/******************************
	* Replace in content
	******************************/

	$q_pages = $db->query("
		SELECT * FROM manual_pages
		WHERE manualid = $manualid
	");

	$update = array();

	while ($page = $db->row_array($q_pages)) {

		$content = str_replace($find, $replace, $page['content']);

		if ($content != $page['content']) {
			$update[$page[id]] = $content;
		}
	}

	foreach ($update as $pageid => $content) {
		$db->query("
			UPDATE manual_pages
			SET content = '" . $db->escape($content) . "'
			WHERE id=$pageid
		");
	}
}





/**
 * Small callback function used with import_manual* functions
 * to provide the user visual feedback.
 */
function manual_import_feedback($info, $type) {

	$info = addslashes($info);

	if ($type == 'proc') {
		$type = 'p';
	} elseif ($type == 'current') {
		$type = 'c';
	} else {
		$type = 'm';
	}

	echo "<script type=\"text/javascript\">$type('$info');</script>";
}

?>