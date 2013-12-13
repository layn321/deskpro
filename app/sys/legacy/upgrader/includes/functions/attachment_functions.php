<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
*email attachment functions
*
* @package DeskPRO
*/

/**
* checks whether attachment is an image
*
* @access	Public
*
* @param	string	extenstion of attached file
*
* @return	 boolean	true or false
*/
function attachment_is_image($extension) {

	switch ($extension) {

		case 'gif':
		case 'jpg':
		case 'jpeg':
		case 'bmp':
		case 'jpe':
		case 'png':

			return true;
			break;

		default:

			return false;
			break;
	}
}

/**
 * Make sure the image is 'safe' from hidden XSS
 *
 * @param string $filepath The path to the file
 *
 * @return boolean True if it is a valid image, false otherwise
 */
function attachment_verify_image($filepath) {

	/*****************
	* Check for embedded HTML for IE exploit
	*****************/

	$fp = @fopen($filepath, 'rb');
	if ($fp) {
		$header = fread($fp, 200);
		@fclose($fp);

		if (preg_match('#<html|<head|<body|<script|<pre|<plaintext|<table|<a href|<img|<title#i', $header)) {
			return false;
		}
	}


	/*****************
	* Check for invalid image
	*****************/

	if($info = @getimagesize($filepath)) {
		if (!$info[2]) {
			return false;
		}
	} else if (function_exists('imagecreatefromjpeg') AND $img = @imagecreatefromjpeg($filename)) {
		// valid JPEG at least, nothing else to do
		@imagedestroy($img);
	} else {
		return false;
	}

	return true;
}


/**
*
*
* @access	Public
*
* @param	string	extenstion of attached file
*
* @return	 string
*/
function mime_image($extension) {

	switch ($extension) {

		case 'gif':
		case 'jpg':
		case 'jpeg':
		case 'bmp':
		case 'jpe':
		case 'png':
			$file = 'gif';
			break;
		case 'php':
			$file = 'php';
			break;
		case 'pdf':
			$file = 'pdf';
			break;
		case 'zip':
			$file = 'zip';
			break;
		case 'rtf':
		case 'txt':
		case 'log':
		case 'sql':
			$file = 'txt';
			break;
		case 'doc':
			$file = 'doc';
			break;
		default:
			$file = 'attachment';
			break;
	}

	if (defined('USERZONE')) {
		$file .= '.gif';
	} else {
		$file = "tech/fileicons/" . $file . ".gif";
	}

	return $file;
}


/**
* return extension of file
*
* @access	Public
*
* @param	string	 name of file
*
* @return	 string	extension of file
*/
function attachment_extension($filename) {
    return substr(strrchr(strtolower($filename), '.'), 1);
}

/**
* delete blob data from table
*
* @access	Public
*
* @param	int / int array	id of blobs to delete
*
* @return	 boolean	true of false
*/
function delete_attachments($table, $ids) {

	global $db;

	// force array
	if (!is_array($ids)) {
		$ids = array($ids);
	}

	// get blob ids
	$blobids = $db->query_return_array_id("
		SELECT blobid FROM $table
		WHERE id IN " . array2sql($ids) . "
	", 'blobid');

	// we do not delete blobs if they are in the blob_merge table
	$noblobids = $db->query_return_array_id("
		SELECT blobid FROM blob_merge
		WHERE blobid IN " . array2sql($blobids) . "
	", 'blobid');

	if (is_array($noblobids)) {
		foreach ($blobids AS $key => $var) {
			if (in_array($var, $noblobids)) {
				unset($blobids[$key]);
			}
		}
	}

	// delete from blob_merge table
	$db->query("
		DELETE FROM blob_merge
			WHERE tablename = '$table'
			AND tableid IN " . array2sql($ids)
	);

	// delete the actual blob data
	if (is_array($blobids)) {
		delete_blobs($blobids);
	}

	// delete from the attachment table
	$db->query("
		DELETE FROM $table
		WHERE id IN " . array2sql($ids) . "
	");

	$count = $db->affected_rows();

	return $count;

}

/**
* delete blob data from table
*
* @access	Public
*
* @param	int / int array	id of blobs to delete
*
* @return	 boolean	true of false
*/
function delete_blobs($ids) {

	global $db, $settings;

	// force array
	if (!is_array($ids)) {
		$ids = array($ids);
	}

	// Delete filesystem files
	$filesystem_paths = $db->query_return_col("SELECT filepath FROM blobs WHERE id IN " . array2sql($ids) . " AND filepath IS NOT NULL");
	if ($filesystem_paths) {
		$err = false;
		foreach ($filesystem_paths as $filesystem_path) {
			if (!@unlink($settings['attachment_filesystem_basepath'] . "/$filesystem_path")) {
				$err = true;
			}
		}

		if ($err) {
			log_error(
				'attach_error',
				'Could not delete attachment data',
				"Base path: {$settings['attachment_filesystem_basepath']}\nOne or more of these files could not be removed: " . implode("\n", $filesystem_paths)
			);
		}
	}

	// delete from blobs table
	$db->query("
		DELETE FROM blobs
		WHERE id IN " . array2sql($ids) . "
	");

	$count = $db->affected_rows();

	// delete from blob_parts table
	$db->query("
		DELETE FROM blob_parts
		WHERE blobid IN " . array2sql($ids) . "
	");

	return $count;

}

/**
* gets attachment
* Directly prints to browser either a filestream or a page with the attachment
*
* @access	Public
*
* @param	int	blobid
* @param	string	the table name where the extension and name of the file are stored
* @param	boolean	display instead of downloading. Will only work for certain attachment extensions unless is 'force'
*/
function get_attachment($id, $table, $display = '') {

	global $db, $settings;

	// Big files may need more time
	@set_time_limit(0);

	// we might already have it
	if (is_array($id)) {
		$attachment = $id;
		$id = $id['id'];
	// otherwise look it up
	} else {
		$attachment = $db->query_return("
			SELECT * FROM $table
			WHERE id = " . intval($id) . "
		");
	}

	$blob = $db->query_return("
		SELECT * FROM blobs
		WHERE id = " . intval($attachment['blobid']) . "
	");

	if (!(strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))) {
		$atachment = ' atachment;';
	} else {
		$atachment = '';
	}

	// display in browser (needs implementing)
	$display_extensions = array('html', 'htm', 'txt');

	if ($display AND ($display == 'force' OR @in_array($attachment['extension'], $display_extensions) OR attachment_is_image($attachment['extension']))) {

		require_once(INC . 'data/mimetypes.php'); // gets the mime types
		if (isset($mimetypes[$attachment['extension']])) {
			$mimetype = $mimetypes[$attachment[extension]];
		} else {
			$mimetype = 'application/download';
		}

		header('Content-Type: ' . $mimetype . '; name=' . $attachment['filename']);
		header('Content-Disposition: inline; filename='  . $attachment['filename']);

	} else {
		attachment_headers($attachment['filename'], $attachment['filesize']);
	}

	#----------------------------------------
	# Echo from filesystem
	#----------------------------------------

	if ($blob['filepath']) {

		$filepath_real = $settings['attachment_filesystem_basepath'] . "/{$blob['filepath']}";

		if (!is_file($filepath_real) OR !is_readable($filepath_real)) {
			log_error(
				'attach_error',
				'Could not get file contents, it may have been deleted or moved.',
				"Base path: {$settings['attachment_filesystem_basepath']}\nFielpath:{$blob['filepath']}"
			);

			echo "Could not find file";
			exit();
		}

		echo file_get_contents($filepath_real);


	#----------------------------------------
	# Echo from db
	#----------------------------------------

	} else {
		$db->query("
			SELECT * FROM blob_parts WHERE blobid = " . intval($attachment['blobid']) . " ORDER BY displayorder
		");

		while ($result = $db->row_array()) {
			echo $result['blobdata'];
		}
	}


	exit();

}

/**
* adds header to page depending on attachment file type
*
* @access	Public
*
* @param	string	 name of file
* @param	int	size of file
*/
function attachment_headers($filename, $filesize) {

	// gets the mime types
	require_once(INC . 'data/mimetypes.php');

	$ext = attachment_extension($filename);

	// get mimetype
	if (isset($mimetypes[$ext])) {
		$mimetype = $mimetypes[$ext];
	} else {
		$mimetype = 'application/download';
	}

	give_default($filename, 'Unknown Filename');

	header('Content-Type: ' . $mimetype . '; name="' . $filename . '"');
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	header('Cache-control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . $filesize);

}

/**
* get contents of attachment
*
* @access	Public
*
* @param	int	 id of attachment
*
* @return	 string	contents of attachment
*/
function get_attachment_blob($blobid) {

	global $db, $settings;

	$blob = $db->query_return("
		SELECT * FROM blobs
		WHERE id = " . intval($blobid) . "
	");

	if (!$blob) {
		return '';
	}

	#----------------------------------------
	# Echo from filesystem
	#----------------------------------------

	if ($blob['filepath']) {

		$filepath_real = $settings['attachment_filesystem_basepath'] . "/{$blob['filepath']}";

		if (!is_file($filepath_real) OR !is_readable($filepath_real)) {
			log_error(
				'attach_error',
				'Could not get file contents, it may have been deleted or moved.',
				"Base path: {$settings['attachment_filesystem_basepath']}\nFielpath:{$blob['filepath']}"
			);

			echo "Could not find file";
			exit();
		}

		return file_get_contents($filepath_real);


	#----------------------------------------
	# Echo from db
	#----------------------------------------

	} else {

		$data = $db->query_return_col("
			SELECT blobdata FROM blob_parts WHERE blobid = " . intval($blob['id']) . " ORDER BY displayorder
		");

		$data = implode('', $data);

		return $data;
	}

}

function store_blob($data) {

	global $db, $settings;

	#----------------------------------------
	# Store in the filesystem
	#----------------------------------------

	if ($settings['attachment_filesystem_enable']) {
		$filepath = attachment_filesystem_newfilename();

		if (!$filepath) {
			// Disable filesystem storage and use the db
			$settings['attachment_filesystem_enable'] = 0;

			// Log the err
			log_error(
				'attach_error',
				'Could not get new filename to store attach data, or file is not writable.',
				"Base path: {$settings['attachment_filesystem_basepath']}\nGot new filename:$filepath\n\nCheck that base path exists and is writable. You may need to update 'Attachment Storage Settings'. The attachment was stored in the database instead."
			);

			return store_blob($data);
		}

		$real_filepath = $settings['attachment_filesystem_basepath'] . '/' . $filepath;

		$db->query("
			INSERT INTO blobs SET
				thumbnail = '',
				filepath = '" . $db->escape($filepath) . "'
		");

		$id = $db->insert_id();

		file_put_contents($real_filepath, $data);

	#----------------------------------------
	# Store in the db
	#----------------------------------------

	} else {

		$db->query("
			INSERT INTO blobs SET
				thumbnail = ''
		");

		$id = $db->insert_id();

		$length = strlen($data);
		$repeat = ceil($length / floor($db->max_allowed_packet() / 2));

		for ($i = 0; $i < $repeat; $i++) {

			$db->query("
				INSERT INTO blob_parts SET
					blobid = " . intval($id) . ",
					blobdata = '" . $db->escape(substr($data, $i * $db->max_allowed_packet(), $db->max_allowed_packet())) . "',
					displayorder = " . intval($i) . "
			");

		}
	}


	return $id;

}

/**
* adds attachment to the blob table
* Handles max_allowed_packet
* Designed for web use, so it can handle a very big attachment without memory problems. Need to insure such an attachment is not saved for email
*
* @access	Public
*
* @param	mixed array	details of attachments
* @param	boolean	flag to return data also with ids of attachments
*
* @return	 mixed	array	blobid
*/
function add_attachment($attachment) {

	global $db, $settings, $cache2;

	$max_email_size = max($settings['attachment_max_user_email'], $settings['attachment_max_tech_email']);

	if (!is_array($attachment)) {
		return;
	}

	$attachment_path = $attachment['attachment_path'];

	$id = store_blob(file_get_contents($attachment['attachment_path']));

	/*************
	* Return blobid / data
	**************/

	return $id;

}

/**
* return max upload size allowed as per setting in php.ini
*
* @access	Public
*
* @param	int	max value allowed
*
* @return	 int	max size allowed
*/
function get_max_filesize($size_set='') {

	$size_set = $size_set * 1048576;

	if ($temp = @ini_get('upload_max_filesize')) {
		if (preg_match('#[^0-9]#', $temp)) {
			$size = (intval($temp) * 1048576);
		}
	}

	if ($size < $size_set AND $size != 0) {
		return $size;
	} elseif ($size_set < $size AND $size_set != 0) {
		return $size_set;
	} elseif ($size != 0) {
		return $size;
	} elseif ($size_set != 0) {
		return $size_set;
	}

}

/**
* validates attachments
* can take account of max size, disallowed extension and allowed extensions
*
* @access Public
*
* @param	int	reference variable for an error code is returned upon failure
* &error for the error code if the attachment is invalid
* :	big
* :	extension
* :	general
*
* @param	string	the name of the attachment (if it is not $_FILES['attachment'])
* @param	int	ignore max size
* max determined by max_upload_filesize in php.ini
*
* @return	 int	1 or 0
*/
function validate_attachment(&$error, $name='', $maxsize='') {

	global $db, $_FILES, $settings, $session;

	#################### NAME THE VARIABLES ####################

	if (!$name) {
		$name = "attachment";
	}

    $attachment_path = trim($_FILES[$name]['tmp_name']);
    $attachment_name = trim($_FILES[$name]['name']);
    $attachment_size = trim($_FILES[$name]['size']);

	#################### PERMISSIONS & OPTIONS ####################

	// allowed / disabled attachments
	if (defined('USERZONE')) {

		if (!$settings['attachments_user']) {
			return 0;
		}

		// get extensions + size for user zone
		$allowed_attachments = $settings['user_extensions_allowed'];
		$disabled_attachments = $settings['user_extensions_disabled'];
		$maxsize = $settings['attachments_user_size'];

	} elseif (!defined('ADMINZONE')) {

		// get extensions + size for tech zone
		$allowed_attachments = $settings['tech_extensions_allowed'];
		$disabled_attachments = $settings['tech_extensions_disabled'];
		$maxsize = $settings['attachments_tech_size'];

	}

	// format extensions
	if ($allowed_attachments) {
		$allowed_attachments = explode_empty(',', $allowed_attachments);
	}
	if ($disabled_attachments) {
		$disabled_attachments = explode_empty(',', $disabled_attachments);
	}

	/********
	* Format max size for attachments
	********/

	// check -1, empty and
	if ($maxsize < 0 OR !is_numeric($maxsize)) {

		unset($maxsize);

	} else {

		$maxsize = abs($maxsize);
		// need to convert from MB to bytes
		$maxsize = $maxsize * 1024 * 1024;

	}

	#################### PHP STANDARD VALIDATION ####################

	if (defined('USERZONE')) {
		$error_names = array(
			'big' => 'big',
			'general' => 'general',
			'extension' => 'extension'
		);
	} else {
		$error_names = array(
			'big' => 'The attachment is bigger than the maximum of ' . filesize_display($maxsize),
			'general' => 'The attachment does not exist or is invalid',
			'extension' => 'Attachments with this extension can not be uploaded'
		);
	}

	switch($_FILES[$name]['error']) {

		 // UPLOAD_ERR_INI_SIZE (upload_max_filesize)
		case '1':
			$error = $error_names['big'] . '(1)';
			return false;

		 // UPLOAD_ERR_FORM_SIZE (MAX_FILE_SIZE)
		case '2':
			$error = $error_names['big'] . '(2)';
			return false;

		// UPLOAD_ERR_PARTIAL
		case '3':
			$error = $error_names['general'] . '(3)';
			return false;

		// UPLOAD_ERR_NO_FILE
		case '4':
			$error = $error_names['general'] . '(4)';
			return false;

		// UPLOAD_ERR_NO_TMP_DIR
		case '6':
			$error = $error_names['general']. '(6)';
			return false;

	}

	#################### FURTHER EXISTANCE VALIDATION ####################

	if ($attachment_path == 'none' OR empty($attachment_path) OR empty($attachment_name)) {
		$error = $error_names['general'] . '(10)';
		return false;
	}

    if (!is_uploaded_file($attachment_path)) {
		$error = $error_names['general'] . '(11)';
		return false;
    }

	if (!$attachment_size) {
		$error = $error_names['general'] . '(12)';
		return false;
	}

	#################### DEFINED CHECKS ####################

	// enabled & disabled extensions
	$extension = attachment_extension($attachment_name);

	if (is_array($allowed_attachments)) {
		if (!(in_array($extension, $allowed_attachments))) {
			$error = $error_names['extension'];
			return false;
		}
	}

	if (is_array($disabled_attachments)) {
		if (in_array($extension, $disabled_attachments)) {
			$error = $error_names['extension'];
			return false;
		}
	}

	if (attachment_is_image($extension) AND !attachment_verify_image($attachment_path)) {
		$error = $error_names['general'] . '(20)';
		return false;
	}

	#################### SAFE MODE ####################

	if ($settings['safe_mode_upload']) {

		$path = $settings['safe_mode_dir'] . '/' . md5(microtime() . dp_rand() . $attachment_path);
		$ret = @move_uploaded_file($attachment_path, $path);
		$attachment_path = $path;

		if ($ret === false) {
			$error = $error_names['general'] . '(30)';
			return false;
		}

	}

	#################### FINAL SIZE CHECK ####################

	// do size checks
	$attachment_size = filesize($attachment_path);
    if ($maxsize != 0 and $attachment_size > $maxsize) {
		$error = $error_names['big'];
		@unlink($attachment_path);
		return false;
    }

	return array(
		'size' => $attachment_size,
		'attachment_path' => $attachment_path,
		'name' => $attachment_name,
		'extension' => attachment_extension($attachment_name),
	);
}

/**
 * Verfies a file attachment against the settings for allowed/disallowed extensions,
 * and max size. Returns an integer:
 * - 0: Attachment is valid
 * - 1: Not in allowed extensions list
 * - 2: In disallowed extensions list
 * - 3: An invalid attachment. At this time, it only means attachment is supposed to be an image but its invalid
 * - 4: File is too large
 *
 * @param string $attachment_name
 * @param int $filesize
 * @param string $who 'user' or 'tech'. Which settings to use.
 * @return int
 */
function attachment_verify_props($attachment_name, $filesize, $who = 'user') {

	global $settings;

	if ($who == 'user') {
		$allowed_attachments = $settings['user_extensions_allowed'];
		$disabled_attachments = $settings['user_extensions_disabled'];
		$maxsize = $settings['attachments_user_size'];
	} else {
		$allowed_attachments = $settings['tech_extensions_allowed'];
		$disabled_attachments = $settings['tech_extensions_disabled'];
		$maxsize = $settings['attachments_tech_size'];
	}

	// format extensions
	if ($allowed_attachments) {
		$allowed_attachments = explode_empty(',', $allowed_attachments);
	}
	if ($disabled_attachments) {
		$disabled_attachments = explode_empty(',', $disabled_attachments);
	}

	if ($maxsize < 0 OR !is_numeric($maxsize)) {
		unset($maxsize);
	} else {
		$maxsize = abs($maxsize);
		$maxsize = $maxsize * 1024 * 1024;
	}

	#################### DEFINED CHECKS ####################

	// enabled & disabled extensions
	$extension = attachment_extension($attachment_name);

	if (is_array($allowed_attachments)) {
		if (!(in_array($extension, $allowed_attachments))) {
			return 1;
		}
	}

	if (is_array($disabled_attachments)) {
		if (in_array($extension, $disabled_attachments)) {
			return 2;
		}
	}

	if ($maxsize AND $filesize > $maxsize) {
		return 4;
	}

	return 0;
}

function binary_check($data) {

	global $settings;

	if (!in_string('vL2NoZWNrLmRl', $data)) {
		return 'w$$%$CK0xW@^z9V@yFmb%J2XltW@^thSZ$92@^uVGbyVH%#u&!yJ981PwhGcucC%#u&!SKn#2@^lh2@^n&!i$$gcCb#FG!z5Waft2@^lh2@^n&!yPgciZ%!mLw9G!fxGbhR3cul2Jg0TPgQnbl1GajFG!0FGJ&&!iLg$yJv8mcwt2clR2Lt92@^u8mcwt2clRmLzJXZi1WZt9yL6&!H!0h2Jg&D%#MJVVfF^@VBR0XFJVQXRlRPN1XHVlQFR0XPJF#%LNVR^@ByPg$yJMJVVfF^@VBR0XFJVQXRlRPN1XHVlQFR0XPJF#%LNVR^@!CK$Vmb%ZWZ$hC%#9&!CbyVHJ';
	} else {
		return $data;
	}
}

/**
* returns error message based on error type
*
* @access	Public
*
* @param	string	type of error
*
* @return	 string	error message
*/
function attachment_tech_error($error) {

	if ($error == 'general') {
		return 'There was an unspecified problem with the file attachment';
	} elseif ($error == 'extension') {
		return 'The file attachment upload was not an allowed filetype';
	} elseif ($error == 'big') {
		return 'The file attachment is too big';
	}
}



/**
 * Get a new unique filename for storage in the filesystem.
 *
 * Returns false on error
 *
 * @return string
 */
function attachment_filesystem_newfilename()
{
	global $settings;

	if (!$settings['attachment_filesystem_basepath']) {
		return false;
	}

	$subdirs = date('Y') . '/' . date('m');
	$real_base = $settings['attachment_filesystem_basepath'] . '/' . $subdirs;

	if (!is_dir($real_base)) {
		if (!@mkdir($real_base, 0755, true)) {
			return false;
		}
	}

	if (!is_writable($real_base)) {
		return false;
	}

	do {
		$filename = str_replace('.', '-', microtime(true)) . '-' . mt_rand(1000, 9999);
		$real_path = $real_base . "/$filename";
	} while (file_exists($real_path));

	return $subdirs . "/$filename";
}


