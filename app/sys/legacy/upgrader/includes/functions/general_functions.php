<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: general_functions.php 6662 2010-03-09 02:06:18Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - general functions for the administration and user interfaces
// +-------------------------------------------------------------+

function plugin_uninstall($id) {

	global $db;

	if (is_numeric($id)) {
		$plugin = $db->query_return("
			SELECT * FROM plugins WHERE id = $id
		");
	} else {
		$plugin = $db->query_return("
			SELECT * FROM plugins WHERE intname = '" . $db->escape($id) . "'
		");
	}

	$id = $plugin['id'];

	$db->query("
		UPDATE plugins SET installed = 0 WHERE id = $id
	");

	$plugins = get_data('plugin_settings');
	$plugins[$plugin['intname']]['installed'] = false;

	update_data('plugin_settings', $plugins);

}

function secure_get_action_create() {

	static $code;
	if ($code) {
		return $code;
	}

	global $session;

	$code = md5($session->sessionid . get_serialize_secure_string());

	return $code;

}

function secure_get_action_link($second_var = true) {

	$code = secure_get_action_create();

	if ($second_var) {
		return '&getauth=' . $code;
	} else {
		return '?getauth=' . $code;
	}

}

function secure_get_action_check() {

	global $request, $header;

	$code = secure_get_action_create();

	if ($code != $request->getString('getauth', 'get')) {

		$header->build();

		echo message_display('For some requests in ' . DP_NAME . ' a special auth code is added to a link URL. This is an added security check designed to prevent a special type of attack (called CSRF).<br /><br />The action you tried to take has been prevented because this code was not valid. The most likely reason for this is that you have re logged into your account and then clicked an old link which would have been made invalid when you logged back in.<br /><br />Please refresh the page you came from and try again.', 'Security Check : Auth Key Invalid');

		$header->footer();

		exit();

	}

}

function default_error($log_level, $log_text, $error_file, $error_line) {

	if (error_reporting() == 0)  {
		return;
	}

	// we don't want to do anything for a notice
	if ($log_level == E_NOTICE OR $log_level == E_STRICT OR $log_level == 8192) {
		return;
	}

	log_error('php', $log_text . "\nLine $error_line :: $error_file");

	return false;

}

function relative_link($url) {

	if (defined('ADMINZONE')) {
		return "./../" . $url;
	} else if (defined('TECHZONE')) {
		return "./../../" . $url;
	} else {
		return "./" . $url;
	}
}

function header_very_simple($title = '') {

	?>

	<html>
	<head>
	<title><?php echo $title;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

	<style type="text/css">
	body  {
		background-color : #ffffff;
	}

	html, td, tr  {
		font : 10pt Tahoma, Verdana, Arial, Helvetica, sans-serif;
		color : #000;
	}
	</style></head>

	<body>

	<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0px" marginheight="0" link="#444444" vlink="#444444" alink="#444444" <?php echo $body;?>>

	<?php

}

function install_jump($force = false) {

	if ($force) {
		if (!is_file(ROOT . '/install/index.php')) {
			die(DP_NAME . ' needs to be installed/upgraded but your /install/index.php file seems to be missing');
		}
	} else {
		if (!is_file(ROOT . '/install/index.php') OR is_file(ROOT . '/install/noinstall.dat')) {
			return false;
		}
	}

	if (defined('ADMINZONE')) {
		header("Location: ../install/index.php");
	} else if (defined('TECHZONE')) {
		header("Location: ../../install/index.php");
	} else {
		header("Location: install/index.php");
	}

	exit();
}

// add value and get back key
function array_add(&$array, $value) {
	$array[] = $value;
	end($array);
	return key($array);
}

if (!function_exists('memory_get_usage')) {
	function memory_get_usage() {
		return 0;
	}
}

function dp_preg_match($regex, $value) {

	// handle the start/end of the regex string
	$regex = str_replace('#', '\#', $regex);

	// run the check and return 0/1
	return preg_match('#' . $regex . '#', $value);

}

function columnSort($unsorted, $column) {
   $sorted = $unsorted;
   for ($i=0; $i < sizeof($sorted)-1; $i++) {
     for ($j=0; $j<sizeof($sorted)-1-$i; $j++)
       if ($sorted[$j][$column] > $sorted[$j+1][$column]) {
         $tmp = $sorted[$j];
         $sorted[$j] = $sorted[$j+1];
         $sorted[$j+1] = $tmp;
     }
   }
   return $sorted;
}

/**
* Determines whether the current tech has permission to perform specific tasks involving the current ticket.
*
* @access	Public
*
* @param	string	The action the tech is trying to perform
* edit	Edit the ticket (or its attachments, messages, etc.)
* view	View the ticket (or its attachments, messages, etc.)
* delete	Delete the ticket entirely
* close	Close or re-open the ticket
* @param	string	optional Ticket data array
*
* @return	 boolean	TRUE if the technician has permission, FALSE if not.
*/
function p_ticket($action, $ticket='', $user='', $options = array()) {

	if (!is_array($user) AND defined('USERZONE')) {
		return true;
	} elseif (!is_array($user)) {
		global $user;
	}

	if (defined('ADMINZONE') OR defined('CRONZONE')) {
		return true;
	}

	if (!$ticket) {
		global $ticket;
	}

	if (!is_array($ticket)) {
		return;
	}

	// Check to see if we have participant info, and if the tech is a particpant
	$user_is_participant = false;
	if ($ticket['@participants'] AND $ticket['@participants']['techs_ids']) {
        if (in_array($user['id'], $ticket['@participants']['techs_ids'])) {
            $user_is_participant = true;
        }
	}

	// We're not letting the user do *anything* if he's banned from this category and dosen't own the ticket
    $bannedcats = explode(',', $user['cats_admin']);
    $bannedcats = Orb_Array::removeEmptyString($bannedcats);
    if (in_array($ticket['category'], $bannedcats) AND $ticket['tech'] != $user['id']) {
        if (!$user_is_participant) {
            return 0;
        }
    }

	/*************
	* EDIT
	*************/

	if ($action == 'edit') {

		// edit own
		if ($ticket['tech'] == $user['id']) {
			return 1;

		// bad if ticket is locked by someone else
		} elseif ($ticket['is_locked'] AND $ticket['lock_techid'] != $user['id'] AND !($options['check_lock_perm'] AND $user['p_unlock'])) {
			return 0;

		// edit unassigned
		} elseif ($ticket['tech'] == 0) {
			return 1;

		// edit assigned
		} else {
			return $user['p_tech_edit'];
		}

	/*************
	* REPLY
	*************/

	} elseif ($action == 'reply') {

		// reply own
		if ($ticket['tech'] == $user['id']) {
			return 1;

		// bad if ticket is locked by someone else
		} elseif ($ticket['is_locked'] AND $ticket['lock_techid'] != $user['id']) {
			return 0;

		// reply unassigned
		} elseif ($ticket['tech'] == 0) {
			return 1;

		} elseif ($user_is_participant) {
		    return 1;

		// reply assigned
		} else {
			return $user['p_tech_reply'];
		}


	/*************
	* VIEW
	*************/

	} elseif ($action == 'view') {

		// view own
		if ($ticket['tech'] == $user['id']) {
			return 1;

	   // view particpants
		} elseif ($user_is_participant) {
            return 1;

		// view unassigned
		} elseif ($ticket['tech'] == 0) {
			return $user['p_unassigned_view'];

		// view assigned
		} else {
			return $user['p_tech_view'];
		}

	/*************
	* DELETE
	*************/

	} elseif ($action == 'delete') {

		// delete own
		if ($ticket['tech'] == $user['id']) {
			return $user['p_delete_own'];

		// bad if ticket is locked by someone else
		} elseif ($ticket['is_locked'] AND $ticket['lock_techid'] != $user['id']) {
			return 0;

		// delete unassigned
		} elseif ($ticket['tech'] == 0) {
			return $user['p_delete_other'];

		// delete assigned
		} else {
			return $user['p_delete_other'];
		}

	/*************
	* CLOSE
		- need to be able to edit the ticket
		- and also be able to close it
	*************/

	} elseif ($action == 'close') {
		return p_ticket('edit', $ticket, $user) AND $user['p_close_ticket'];

	/*************
	* RE-OPEN
		- need to be able to edit the ticket
		- and also be able to open it
	*************/

	} elseif ($action == 'open') {
		return p_ticket('edit', $ticket, $user) AND $user['p_open_ticket'];

	/*************
	* MANAGE PARTICIPANTS
	*************/

	} elseif ($action == 'manage_participants') {

	    if ($ticket['tech'] == $user['id']) {
	        if ($user['p_manage_participants']) {
	            return true;
	        }
	    } else {
	        if ($user['p_manage_participants_other']) {
	            return true;
	        }
	    }

	    return false;
	}

}

/**
* get array of a specific field
*
* @access	Public
*
* @param	int	file size
*
* @return	 string	converted file size with unit appended
*/
function array_inside($array, $field) {

	if (!is_array($array)) {
		return;
	}

	foreach ($array AS $var) {
		$tmp[] = $var[$field];
	}

	return $tmp;

}

function standard_eol($text) {

	return str_replace(array("\r\n", "\n"), "\n", $text);

}

function get_full_url() {

	$url = 'http';
	if ($_SERVER['HTTPS'] == 'on') {
		$url .=  's';
	}
	$url .=  '://';
	if ($_SERVER['SERVER_PORT'] != '80') {
		$url .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['SCRIPT_NAME'];
	} else{
		$url .=  $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
	}
	if ($_SERVER['QUERY_STRING'] > ' '){
		$url .=  '?' . $_SERVER['QUERY_STRING'];
	}

	return $url;

}

/**
* converts file size in kb or mb
*
* @access	Public
*
* @param	int	file size
*
* @return	 string	converted file size with unit appended
*/
function filesize_display($filesize) {

	if (!$filesize) {
		return;
	}

	// sort out decimal places etc.
	$end = " Bytes";
	if ($filesize > 103) {
		$filesize = $filesize / 1024;
		$end = " KB";
	}
	if ($filesize > 500) {
		$filesize = $filesize / 1024;
		$end = " MB";
	}
	if ($filesize > 10000) {
		$fiesize = $filesize / 1024;
		$end = " GB";
	}

	$filesize = round($filesize, 1);
	return $filesize . $end;
}

/**
 * Converts a filesize string such as "10MB" or "50 K" to bytes
 *
 * @param string $filesize The filesize string to parse
 * @return integer The number of bytes
 */
function return_bytes($filesize) {

	$matches = array();
	if (!preg_match('#^([0-9]+)(.*?)$#', $filesize, $matches)) {
		return -1;
	}

	$bytes = $matches[1];
	$type = strtolower(trim($matches[2]));

	// No breaks on purpose, each level falls through
	switch ($type) {
		case 'g':
		case 'gb':
			$bytes *= 1024;

		case 'm':
		case 'mb':
			$bytes *= 1024;

		case 'k':
		case 'kb':
			$bytes *= 1024;
	}

	return $bytes;
}


function get_gateway_source($id) {

	global $db;

	$source = $db->query_return("
		SELECT * FROM gateway_source
		WHERE id = " . intval($id)
	);

	$db->query("
		SELECT source
		FROM gateway_source_parts
		WHERE sourceid = " . intval($id) . "
		ORDER BY id ASC
	");

	$data = '';
	while ($result = $db->row_array()) {
		$data .= $result['source'];
	}

	return array('source' => $source, 'data' => $data);

}

function array_merge_values() {

	foreach (func_get_args() AS $array) {

		if (is_array($array)) {
			foreach ($array AS $value) {
				$data[] = $value;
			}
		}
	}

	return $data;

}

/**
 * Same as PHP array_merge but treats array as an assoc. Orders
 * are kept, even if the key is numeric. PHP's array_merge would disregard
 * numeric keys and add the values with a different numeric index.
 *
 * Values from later passed params will overwrite values from previous
 * arrays if same key exists. Can take any number of arrays
 *
 * @param array The initial array
 * @param array Merge array ...
 * @return array
 */
function array_merge_assoc() {

	$new_array = (array)func_get_arg(0);

	for ($i = 1, $size = func_num_args(); $i < $size; $i++) {

		$arr = (array)func_get_arg($i);

		foreach ($arr as $key => $val) {
			$new_array[$key] = $val;
		}
	}

	return $new_array;
}

function get_graph_id() {

	global $settings, $user;

	$id = $settings['graph_count'] + 1;

	update_setting('graph_count', $id);

	return $user['id'] . '_' . $id . '_' . make_randomstring(20);

}

function display_graph($id, $return = false) {

	if ($id > 0) {

		if (defined('REPORTZONE')) {

			// need to know where we are logged in
			global $session_tech, $session_admin;

			if ($session_admin->isValid()) {
				$html = "<img src=\"" . WEB . "admincp/graph.php?id=$id\">";
			} else {
				$html = "<img src=\"" . WEB . "tech/home/graph.php?id=$id\">";
			}

		} else if (defined('ADMINZONE')) {
			$html = "<img src=\"" . WEB . "admincp/graph.php?id=$id\">";
		} else if (defined('TECHZONE')) {
			$html = "<img src=\"" . WEB . "tech/home/graph.php?id=$id\">";
		}

	} else {

		if ($id == -1) {
			$html = html_image('reports/max_barchart.gif');
		} else if ($id == -2) {
			$html = html_image('reports/max_piechart.gif');
		}
	}

	if ($return) {
		return $html;
	} else {
		echo $html;
	}
}

/**
* returns index of a key in array
* @access public
* @param mixed array $array -   error message to be displayed
* @param string $offset_key -   key for which index has to find out
* @return int               -   index of key, -1 if index not found
*/
function array_offset($array, $offset_key) {
  $offset = 0;
  foreach($array as $key=>$val) {
   if($key == $offset_key)
     return $offset;
   $offset++;
  }
  return -1;
}

/**
* generates html to show error related to GD library
* @access public
* @return string			-	html for error message
*/
function gd_error() {
	$str = "<font color=\"red\"><strong>This PHP installation is not configured with the GD library. Please recompile PHP with GD support.</strong></font>";

	if (defined('ADMINZONE')) {
		$str .= "<br /><br />Your <a href=\"./diagnose.php?do=phpinfo\">PHP Info</a> output shows which PHP modules are installed.";
	}

	return $str;
}

/**
* Checks GD library is installed on server or now
* @access public
* @return boolean	-	True if library is installed else false
*/
function gd_check() {

	if (!function_exists("imagetypes") OR !function_exists('imagecreatefromstring')) {
		return false;
	} else {
		return true;
	}
}

/**
* appends / at the end of provided path
* @access public
* @param string $path		-	path to which / has to be appended
* @return string			-	provided path with / appended
*/
function givetrailingslash($path) {
	return preg_replace('|/$|', '', $path) . '/';
}

/**
* Creates a copy of provided object and returns it
* @access public
* @param mixed $object		-	object that has to be copied
* @return mixed				-	copy of provided object
*/
if (version_compare(phpversion(), '5.0') < 0) {
	eval('
    function clone($object) {
      return $object;
    }
    ');
}

function debugstop($max) {

	static $count = 0;

	if ($max == $count) {
		echo "Exiting after $max times";
		exit();
	}

	$count++;

}

############################################# HTML FUNCTIONS #############################################

function html_form_escape($value, $override) {

	if (!$override) {
		return dp_html($value);
	} else {
		return $value;
	}
}

/**
* makes HTML for an unchecked text source to display in browser
* removes real HTML (security)
* to be expanded to support <b><i> etc type replacements
* @access Public
* @param string $text		-	the text to format
* @param int $nohtml		-	if 0 removes html tags from text
* @return string			-	formatted text
*/
function dp_html_format($text, $use_nl2p = false) {

	// prevent long words
	$text = do_wordwrap($text);

	// get rid of html
	$text = dp_html($text);

	// turn links into html
	$codes = array(
		// www. and ftp. to http:// and ftp://
		'#(^|\s+)www\.([^\s<>\'\"]+)(\s+|$)#i' => '$1http://www.$2$3',
		'#(^|\s+)ftp\.([^\s<>\'\"]+)(\s+|$)#i' => '$1ftp://ftp.$2$3',

		// URLs
		'#(http://|https://|ftp://)([a-zA-Z0-9%+&:;,\[\]\+\.\-=_\/~\?\#]+)(?<![\.\?:])#i' => '<a href="$0">$0</a>',

		// Emails
		'#([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))#i' => '<a href="mailto:$1">$1</a>',
	);

	$text = preg_replace(array_keys($codes), array_values($codes), $text);

	if ($use_nl2p) {
		$text = Orb_String::nl2p($text);
	} else {
		$text = nl2br($text);
	}

	$text = trim($text);

	return $text;
}

function dp_html_noformat($text) {
	return trim(nl2br(dp_html($text)));
}

/**
* htmlspecialchars() but works with unicode text
* @access Public
* @param string $text		-	string on which filter has to be applied
* @return string			-	parsed text
*/
function dp_html($text) {

	$text = preg_replace('/&(?!#[0-9]+;)/si', '&amp;', $text);
	return trim(str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $text));

}

/**
 * Reverse of dp_html: Replaces entities with their real characters
 *
 * @param string $text The string on which the filter should be applied
 * @return string The text with the filter applied
 */
function un_dp_html($text) {
	$text = preg_replace_callback('/&#([0-9]+);/', function($match) {
		return chr_uni($match[1]);
	}, $text);
	return str_replace(array('&lt;', '&gt;', '&quot;', '&amp;'), array('<', '>', '"', '&'), $text);
}

/**
* convert int value to utf 8 value
*
* @access	public
*
* @param	int	integer value
*
* @return	 string	utf 8 value of int
*/
function chr_uni($intval) {

	$intval = intval($intval);
	switch ($intval) {

		// 1 byte, 7 bits
		case 0:
			return chr(0);
		case ($intval & 0x7F):
			return chr($intval);

		// 2 bytes, 11 bits
		case ($intval & 0x7FF):
			return chr(0xC0 | (($intval >> 6) & 0x1F)) .
				chr(0x80 | ($intval & 0x3F));

		// 3 bytes, 16 bits
		case ($intval & 0xFFFF):
			return chr(0xE0 | (($intval >> 12) & 0x0F)) .
				chr(0x80 | (($intval >> 6) & 0x3F)) .
				chr (0x80 | ($intval & 0x3F));

		// 4 bytes, 21 bits
		case ($intval & 0x1FFFFF):
			return chr(0xF0 | ($intval >> 18)) .
				chr(0x80 | (($intval >> 12) & 0x3F)) .
				chr(0x80 | (($intval >> 6) & 0x3F)) .
				chr(0x80 | ($intval & 0x3F));
	}
}

/**
* returns keys of array starting from a index
* @access Public
* @param mixed array $array -	array
* @param int $offset		-	starting index from that keys will be returned
* @param int $len			-	number of elements to return
* @return mixed array		-	array of keys
*/
function array_splice_key($array, $offset, $len=-1) {

   if (!is_array($array))
       return FALSE;

   $length = $len >= 0? $len: count($array);
   $keys = array_slice(array_keys($array), $offset, $length);
   foreach($keys as $key) {
       $return[$key] = $array[$key];
   }

   return $return;
}


/*******************************************************
	function user_category_array

---- DESCRIPTION: --------------------------------------
	Replace names of non-user-viewable categories with the
	display name for the selected display category.

---- RETURN: -------------------------------------------
	An array containing re-mapped categories (categories
	that are not meant to be shown to users are given
	the "displayed" name here)

	id is an override, it is for a set category which if
	set the user can still edit
********************************************************/

/**
* Replace names of non-user-viewable categories with the
* display name for the selected display category.
* @access Public
* @param string $location	-
* @param int $id			-
* @return
*/
function user_category_array($location='new', $allow_catids='', $is_user = true, $language, $select = true, $parent = 0) {

	global $db, $settings, $cache2, $dplang, $userobj;

	/*********
	* Permission checks based on settings
	*********/

	// categories not viewable to users
	if (!$settings['category_user_viewable']) {
		return false;
	}

	// do users get to pick the category on new ticket?
	if ($location == 'new' AND !$settings['category_user_start']) {
		return false;
	}

	// do users get to edit the ticket category?
	if ($location == 'edit' AND !$settings['category_user_editable']) {
		return false;
	}

	/*********
	* Get Categories
	*********/

	if ($parent > -1) {
		$catids = $cache2->getCategoryChildren($parent);
	} else {
		$catids = array_keys($cache2->getCategories());
	}

	// no categories?
	if (!is_array($catids)) {
		return false;
	}

	/*********
	* Get permissions
	*********/

	static $cats_permissions;

	if (!isset($cats_permissions)) {
		$cats_permissions = array();
		$perms = $cache2->getUserCategoryPermissions();
		$membergroups = $userobj->getMemberGroups();

		foreach ($perms as $catid => $groups) {
			foreach ($groups as $groupid) {
				if (in_array($groupid, $membergroups)) {
					$cats_permissions[] = $catid;
					break;
				}
			}
		}

		// Make sure a parent cat has allowable children
		foreach ($cache2->category_parents as $parent_id => $child_ids) {

			if (($key = array_search($parent_id, $cats_permissions)) === false) {
				continue;
			}

			foreach ($child_ids as $catid) {
				if (in_array($catid, $cats_permissions)) {
					continue 2;
				}
			}

			// If we continue break yet, then no access to children so
			// deny the parent too
			unset($cats_permissions[$key]);
		}
	}

	/*********
	* Reset based on permissions
	*********/

	$allow_these = array();
	$allow_catids = (array)$allow_catids;

	if ($allow_catids) {
		foreach ($allow_catids as $catid) {
			$cat = $cache2->getCategory($catid);

			$allow_these[] = $catid;

			if ($cat['parent']) {
				$allow_these[] = $cat['parent'];
			}
		}
	}

	foreach ($catids AS $catid) {

		$in_allow = ($allow_these AND in_array($catid, $allow_these));

		if (!in_array($catid, $cats_permissions) AND !$in_allow) {
			continue;
		}

		$category = $cache2->getCategory($catid);

		// use standard name if in default language
		if ($language == $cache2->getDefaultLanguageID()) {
			$cats[$category['id']] = $category['name'];

		} else {

			$name_language = unserialize($category['name_language']);

			// check this category name is translated
			if ($name_language[$language] AND $name_language[$language]['name']) {
				$cats[$category['id']] = $name_language[$language]['name'];

			// use standard name if we have no translation
			} else {
				$cats[$category['id']] = $category['name'];
			}
		}
	}

	// remove any rubish, unset array
	$cats = array_remove_empty($cats);

	// add an empty option
	if ($select AND is_array($cats)) {
		array_unshift_assoc($cats, '0', $dplang['please_select']);
	} else if (!$select) {
		array_unshift_assoc($cats, '0', $dplang['ticketlist_property_none']);
	}

	return $cats;
}

/*******************************************************
	function user_priority_array

---- DESCRIPTION: --------------------------------------
	Replace names of non-user-viewable priorities with the
	display name for the selected display priority.

---- RETURN: -------------------------------------------
	An array containing re-mapped priorities (priorities
	that are not meant to be shown to users are given
	the "displayed" name here)
********************************************************/

/**
* Replace names of non-user-viewable categories with the
* display name for the selected display category.
* @access Public
* @param string $location	-
* @param int $id			-
* @return
*/
function user_priority_array($location='new', $allow_priids='', $is_user = true, $language, $select = true) {

	global $db, $settings, $cache2, $dplang, $userobj;

	/*********
	* Permission checks based on settings
	*********/

	// priority not viewable to users
	if (!$settings['priority_user_viewable']) {
		return false;
	}

	// do users get to pick the priority on new ticket?
	if ($location == 'new' AND !$settings['priority_user_start']) {
		return false;
	}

	// do users get to edit the ticket priority?
	if ($location == 'edit' AND !$settings['priority_user_editable']) {
		return false;
	}

	/*********
	* Get Categories
	*********/

	$priorities = $cache2->getPriorities();

	// no categories?
	if (!is_array($priorities)) {
		return false;
	}

	/*********
	* Get permissions
	*********/

	static $pris_permissions;

	if (!isset($pris_permissions)) {

		$pris_permissions = array();
		$perms = $cache2->getUserPrioritiesPermissions();
		$membergroups = $userobj->getMemberGroups();

		foreach ($perms as $priid => $groups) {
			foreach ($groups as $groupid) {
				if (in_array($groupid, $membergroups)) {
					$pris_permissions[] = $priid;
					break;
				}
			}
		}
	}

	/*********
	* Reset based on permissions
	*********/

	$allow_these = (array)$allow_priids;

	foreach ($priorities AS $priority) {

		$in_allow = ($allow_these AND in_array($priority['id'], $allow_these));

		if (!in_array($priority['id'], $pris_permissions) AND !$in_allow) {
			continue;
		}

		// use standard name if in default language
		if ($language == $cache2->getDefaultLanguageID()) {
			$pris[$priority['id']] = $priority['name'];

		} else {

			$name_language = unserialize($priority['name_language']);

			// check this category name is translated
			if ($name_language[$language]['name']) {
				$pris[$priority['id']] = $name_language[$language]['name'];

			// use standard name if we have no translation
			} else {
				$pris[$priority['id']] = $priority['name'];
			}
		}
	}

	// remove any rubish, unset array
	$pris = array_remove_empty($pris);

	// add a choose option
	if ($select AND is_array($pris)) {
		array_unshift_assoc($pris, '0', $dplang['please_select']);
	} else if (!$select) {
		array_unshift_assoc($pris, '0', $dplang['ticketlist_property_none']);
	}

	return $pris;
}

/**
 * Get the companies a user can select
 *
 * @param bool $select These will be used as options in a select?
 * @return array
 */
function user_company_array($select = true, $show_none = true) {

	global $userobj, $cache2, $dplang;

	$add = array();
	if ($show_none) {
		$add[-1] = $dplang['no_company'];
	}

	if ($select) {
		$comp_name = $cache2->getCompanyName($userobj->user['default_company']);
		$add[0] = phrase($dplang['default_company'], $comp_name);
	}

	$companies = $cache2->getCompanyNames($add, $userobj->getMemberCompanies(), true);

	return $companies;
}

/**
* adds page header for no catch
* @access Public
*/
function no_cache_headers() {

	// deal with AOL's funky caching
	// ref: http://ilia.ws/archives/59-guid.html#extended
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'AOL') !== false) {

		header("Cache-Control: no-store, private, must-revalidate, proxy-revalidate, post-check=0, pre-check=0, max-age=0, s-maxage=0");

	} else {

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

		// always modified
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

		// HTTP/1.1
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);

		// HTTP/1.0
		header("Pragma: no-cache");

	}
}

/**
* Generate a unique, valid ref.
* @access Public
* @param string $table	-	name of table in which duplication of session id has to check
* @param string $field  -   name of the field that contains the sessionid
* @return string		-	session id
*/
function make_session_id($table, $field = 'sessionid') {

	global $db;

	do {

		$sessionid = md5(uniqid('', dp_rand().time()).get_serialize_secure_string());

		if ($db->query_amatch($table, "$field = '" . $db->escape($sessionid) . "'")) {
			unset($sessionid);
		}

	} while (!$sessionid);

	return $sessionid;

}

/**
* checks if demo mode is set or not
* @access Public
* @return boolean		-	true or false
*/
function is_demo() {
	if (defined('DESKPRO_DEBUG_DEMOMODE')) {
		return true;
	}
}

/**
* parses text and return html safe text
* @access Public
* @param string $string	-	string that has to be parsed
* @return string		-	parsed string
*/
function xss_parse($string) {

	require_once(INC . '3rdparty/safehtml/safehtml.php');

	$safehtml = new SafeHTML();
	return $safehtml->parse($string);
}

/**
 * Get the first line of a string.
 *
 * @param string $string The string to work on
 * @return string The first line of the string, or the string itself if there is only one line
 */
function get_first_line($string) {
	$string = preg_replace("#(\r\n|\r|\n)#", "\n", $string);

	if (($pos = strpos($string, "\n")) !== false) {
		$string = substr($string, 0, $pos);
	}

	return trim($string);
}

/**
* serialize url
* @access Public
* @param string $string	-
* @param string $name	-
* @return string		-
*/
function dp_serialize_url($string, $name) {

	$ser = dp_serialize($string);
	return '&' . $name . '=' . urlencode($ser) . '&' . $name . '_ser=' . $ser[1];

}
/**
* Unserialize text and verify it using the contained MD5 hash
*
* @param string $string	The string to unserialize
* @return mixed The unserialized value or false on error
*/
function dp_unserialize($string) {

	// Not long enough
	if (strlen($string) < 33) {
		return false;
	}

	$md5 = substr($string, 0, 32);
	$ser = substr($string, 33);
	$md5_check = md5($ser . md5(get_serialize_secure_string()));

	if ($md5_check == $md5) {
		return @unserialize($ser);
	} else {
		return false;
	}
}

/**
* Serialize text in a safe way by also providing an MD5 hash
* that is used when unserializing to verify the content.
*
* @access Public
* @param string $string	-
* @return string		-
*/
function dp_serialize($value) {

	$ser = serialize($value);
	$md5 = md5($ser . md5(get_serialize_secure_string()));

	return $md5 . '|' . $ser;
}


/**
 * Get the string to use to secure the MD5 checksum of the serialized data.
 *
 * @return string The string
 */
function get_serialize_secure_string() {

	global $settings;

	if ($settings['serialize_secure_string'] == 'SECURE_STRING' OR !$settings['serialize_secure_string']) {
		require_once(INC . 'functions/admin_tech_functions.php');

		$str = make_randomstring(dp_rand(11, 20));
		update_setting('serialize_secure_string', $str);
	}

	return $settings['serialize_secure_string'];
}

/**
 * Encrypt some data.
 *
 * @param mixed $data Any data you wish to encrypt. Will be automatically serialized if needed.
 * @param string $key The key to use, or false to use default
 * @return string
 */
function simple_encrypt($data, $key = false) {

	if (!$key) {
		$key = get_serialize_secure_string();
	}

	require_once(INC . '/classes/class_SimpleEncryption.php');

	$crypt = new SimpleEncryption();
	$encrypted_string = $crypt->encrypt($key, $data);

	$encrypted_string = @base64_encode($encrypted_string);

	return $encrypted_string;
}

/**
 * Decrypt some data that was encrypted using simple_encrypt.
 *
 * @param mixed $source The source youwish to decrypt. Will be automatically unserialized if needed.
 * @param string $key The key to use, or false to use default
 * @return mixed
 */
function simple_decrypt($source, $key = false) {

	if (!$key) {
		$key = get_serialize_secure_string();
	}

	require_once(INC . '/classes/class_SimpleEncryption.php');

	$source = @base64_decode($source);

	$crypt = new SimpleEncryption();
	$data = $crypt->decrypt($key, $source);

	return $data;
}

/**
*
* @access Public
* @return
*/
function request_build_query_remove() {

	global $request;

	$array = $request->getAll('request');

	$values = func_get_args();
	foreach ($values AS $key => $var) {
		unset ($array[$var]);
	}

	return http_build_query($array);

}

/**
*
* @access Public
* @return
*/
function request_build_query_add() {

	global $request;

	$variable_names = func_get_args();
	foreach ($variable_names AS $varname) {
		$values[$varname] = $request->getRaw($varname, 'request');
	}

	return http_build_query($values);
}

/**
*
* @access Public
* @return
*/
function phrase() {

	$args = func_get_args();

	if ($args[0] == '') {

		if (defined('DESKPRO_DEBUG_DEVELOPERMODE')) {
			print__rr($args);
			return 'Empty phrase';
		}

		log_error('lang', 'Empty phrase with phrase tag', print_r($args, true));
	}

	$numargs = sizeof($args);

	// converts $GLOBALS[session_url]
	$args = str_replace('{$GLOBALS[session_url]}', $GLOBALS['session_url'], $args);
	$args = str_replace('{$GLOBALS[session_ampersand]}', $GLOBALS['session_ampersand'], $args);

	// Compat for JS style {0} to %1
	// Unfortun. numbering starts from 0, so we need to essentally +1 to them all
	// to get them to work this "php way"
	$args = preg_replace_callback('#\{([0-9]+)\}#', 'phrase_fix_js_num', $args);

	// converts %1 to %1$s for use in sprintf
	$args = preg_replace('/%([0-9])+/siU', '%\\1$s', $args);

	// if we have only one argument, just return the argument
	if ($numargs < 2) {

		return $args[0];

	} else {

		// call sprintf() on the first argument of this function
		if ($phrase = @call_user_func_array('sprintf', $args)) {
			return $phrase;

		} else {

			// failed, perhaps not enough arguments
			for ($i = $numargs; $i < 10; $i++) {
				$args["$i"] = "%$i variable is not defined";
			}

			if ($phrase = @call_user_func_array('sprintf', $args)) {
				return $phrase;

			// still does not work, return text.
			} else {
				return $args[0];
			}
		}
	}
}

// see phrase()
function phrase_fix_js_num($matches) {
    return '%' . ($matches[1]+1);
}

/**
* merges two arrays
* @access Public
* @param mixed array $arraya	-	first array to merge
* @param mixed array $arrayb	-	second array to merge
* @param boolean $maintainkeys	-	if true original keys of array elements will be used
*									if false means do not use original keys
*/
function array_merge_error($arraya, $arrayb, $maintainkeys=FALSE) {

	$arrayc = array();

	if (is_array($arraya)) {
		foreach ($arraya AS $key => $var) {
			if ($maintainkeys) {
				$arrayc[$key] = $var;
			} else {
				$arrayc[] = $var;
			}
		}
	}
	if (is_array($arrayb)) {
		foreach ($arrayb AS $key => $var) {
			if ($maintainkeys) {
				$arrayc[$key] = $var;
			} else {
				$arrayc[] = $var;
			}
		}
	}

	return $arrayc;
}

/**
* checks value exists in array or not
* @access Public
* @param mixed $match			-	value that has to be checked in array for existance
* @param mixed array $array		-	array in which value has to check
* @return boolean				-	true or false
*/
function in_array_string($match, $array) {

	if ($match == $array) {
		return true;
	}
	if (is_array($array) AND in_array($match, $array)) {
		return true;
	}

	return false;

}

/**
 * Generates a token for the user
 *
 * @param string $type The type of token
 * @return string The token saved
 */
function set_user_token($type) {

	global $db, $session, $user;

	$token = make_randomstring(20);

	$db->query("
		INSERT INTO user_token SET
			" . ($user['id'] ? "userid = $user[id]" : "sessionid = '" . $db->escape($session['sessionid']) . "'") . ",
			timestamp = " . time() . ",
			token_type = '" . $db->escape($type) . "',
			token_value = '" . $db->escape($token) . "'
	");

	return $token;
}

/**
 * Confirm a user token
 *
 * @param string $type The type of token
 * @param string $token The token to confirm
 * @param boolean $justreturn True to just return true/false depending on success. Otherwise an error page will be presented to the user.
 * @return boolean True if the token is good, false otherwise.
 */
function check_user_token($type, $token = '', $justreturn = false) {

	global $db, $session, $user, $request;

	give_default($token, $request->getString('token'));

	// max 1 hour to get it done
	$time = mktime() - (60 * 60);

	$found = $db->query_amatch(
		'user_token',
		($user['id'] ? "userid = $user[id]" : "sessionid = '" . $db->escape($session['sessionid']) . "'") . "
			AND token_type = '" . $db->escape($type) . "'
			AND token_value = '" . $db->escape($token) . "'
			AND timestamp > $time
	");

	if ($justreturn) {
		return $found;
	} else {
		if ($found) {
			return true;
		} else {
			display_user_error('error_invalid_token');
			return false;
		}
	}
}

/**
* generates a token for technician
* @access Public
* @param string $type			-	type of token
* @return string				-	token string
*/
function set_tech_token($type) {

	global $db, $user;

	$token = make_randomstring(20);

	$db->query("
		INSERT INTO tech_token SET
			techid = " . intval($user['id']) . ",
			timestamp = " . mktime() . ",
			token_type = '" . $db->escape($type) . "',
			token_value = '" . $db->escape($token) . "'
	");

	return $token;

}

/**
* validate token for the user
* @access Public
* @param string $type			-	type of token
* @param string $token			-	token to validate
* @param boolea $justreturn		-	if TRUE then returns true or false based on validation
*									if FALSE and token not valdiated prints error
*									other wise returns true
* @return boolean				-	true or false
*/
function check_tech_token($type, $token='', $justreturn=FALSE) {

	global $db, $user, $request;

	give_default($token, $request->getString('token'));

	// max 1 hour to get it done
	$time = mktime() - (60 * 60);

	$found = $db->query_amatch('
		tech_token',
		"techid = " . intval($user['id']) . "
			AND token_type = '" . $db->escape($type) . "'
			AND token_value = '" . $db->escape($token) . "'
			AND timestamp > $time
	");

	if ($justreturn) {
		return $found;
	} else {
		if ($found) {
			return 1;
		} else {
			mistake('Please login again to complete this action');
		}
	}
}

function or2sql($or) {
	if (!is_array($or)) {
		return;
	}
	return implode($or, ' OR ');
}

/**
* generates where clause
* @access Public
* @param string array $where	-	array that contains field names and values
* @param boolea $addwhere		-	if set to TRUE adds "where" word to clause
* @return string				-	where clause
*/
function where2sql($where, $addwhere = true) {

	if (!is_array($where)) {
		return;
	}

	// turn array of conditions into SQL
	foreach ($where AS $key => $var) {
		if (trim($var) != '') {
			if (!$i) {
				if ($addwhere) {
					$query = "WHERE $var";
				} else {
					$query = " $var ";
				}
				$i = true;
			} else {
				$query .= " AND $var";
			}
		}
	}
	return $query;

}

/**
* Adds a key-value pair to the beginning of an array
* @access	Public
* @param	array	$array	The array to be added to
* @param	mixed	$key	Key of the element
* @param	mixed	$value	Value of the new element
* @param	bool	$return	Return the array instead of using references?
* @return	mixed	array | null
*/
function array_unshift_assoc(&$array, $key, $value, $return = false) {

	if (!is_array($array)) {
		$array = array();
	}

	if ($return) {

		$newarray[$key] = $value;
		foreach ($array AS $key => $value) {
			if (!isset($newarray[$key])) {
				$newarray[$key] = $value;
			}
		}

		return $newarray;

	} else {

		$newarray[$key] = $value;
		foreach ($array AS $key => $value) {
			if (!isset($newarray[$key])) {
				$newarray[$key] = $value;
			}
		}

		$array = $newarray;

	}
}

/**
* log error and save it in database
* @access Public
* @param string $type			-	type of error
* @param string $details		-	details of error
* @param string @urgency		-	urgency of error to solve
* @param array $link_gateway_error Create a gateway error as well, linking with this one
* @return int The log entry ID
*/
function log_error($type, $summary, $details= '', $link_gateway_error = array()) {

	static $ran;

	if (defined('NO_LOG_ERROR')) {
		return 0;
	}

	/*
		- only log a single error. That way we won't get into an infinite loop regarding db errors
	*/

	global $db;

	// we only want to log twice to prevent loop. Twice allows for a logged db error and logged failed email to alert to that error.
	if (!is_object($db) OR $ran >= 2) {
		return 0;
	}

	$ran++;

	$db->nodebug = true;

	$backtrace = '';

	if (function_exists('debug_print_backtrace')) {
		ob_start();
		debug_print_backtrace();
		$backtrace = ob_get_clean();
	}

	// Add some more debug info
	$details = (array)$details;

	if (defined('IPADDRESS')) $details['ip'] = IPADDRESS;
	if (defined('ALTIPADDRESS')) $details['altip'] = ALTIPADDRESS;
	if (defined('PATH')) $details['path'] = PATH;
	if (defined('FILEPATH')) $details['filepath'] = FILEPATH;
	if ($_SERVER['HTTP_REFERER']) $details['referrer'] = $_SERVER['HTTP_REFERER'];
	if (defined('REQUEST_METHOD')) $details['requestmethod'] = REQUEST_METHOD;

	global $request;
	if ($request) $details['requestobj'] = print_r($request, true);

	global $user;
	if ($user) $details['userinfo'] = print_r($user, true);

	$db->query("
		INSERT INTO error_log SET
			timestamp = '" . TIMENOW . "',
			`type` = '" . $db->escape($type) . "',
			summary = '" . $db->escape($summary) . "',
			details = '" . $db->escape(serialize($details)) . "',
			backtrace = '" . $db->escape(serialize($backtrace)) . "'
	");

	$err_id = $db->insert_id();

	if ($link_gateway_error) {
		$db->query("
			INSERT INTO gateway_error SET
				error = 'error',
				timestamp = '" . TIMENOW . "',
				sourceid = " . intval($link_gateway_error['sourceid']) . ",
				gateway = '" . strtolower($link_gateway_error['gateway']) . "',
				subject = '" . $db->escape($link_gateway_error['subject']) . "',
				email = '" . $db->escape($link_gateway_error['email']) . "',
				error_log = $err_id
		");

		$gerr_id = $db->insert_id();

		$db->query("
			UPDATE error_log SET gateway_error = $gerr_id
			WHERE id = $err_id
		");
	}

	if (defined('MANAGED')) {

		$mandb = managed_get_db_conn();
		if ($mandb) {
			try {
				$mandb->query("
					INSERT INTO dp_error_log SET
						site_id = ".MANAGED_SITEID.",
						site_error_id = ".$err_id.",
						timestamp = ".TIMENOW.",
						`type` = '" . $db->escape($type) . "',
						summary = '" . $db->escape($summary) . "',
						details = '" . $db->escape(serialize($details)) . "',
						backtrace = '" . $db->escape(serialize($backtrace)) . "'
				");
			} catch (Exception $e) {}
		}

	} // END_MANAGED

	return $id;
}

/**
* invert binary value
* @access Public
* @param int $i			-	value that to be inverted
* @return int			-	inverted value
*/
function binary_invert($i) {
	return iff($i, 0, 1);
}

/**
* removes empty elements from an array
* @access Public
* @param mixed array $array	-	array from which empty elements has to be removed
* @return mixed array 		-	array without any empty element
*/
function array_remove_empty($array) {

	if (!is_array($array)) {
		return;
	}

	foreach($array as $key => $value) {

		if ($value == '') {
			unset($array[$key]);
		}
	}

	// if we removed the only elements, we don't want an array any more
	if (count($array) < 1) {
		return;
	}

	return $array;

}

/*****************************************************
	function generate_order

-----DESCRIPTION: -----------------------------------
	generate the order

	Relies on a number of global variables:

	i) orderdirection, array of elements keyed 1,2,3 etc
	ii) orderfield, array of elements keyed 1,2,3 etc
	iii) a sort / d sort - variables that override

*****************************************************/

class queryOrder {

	var $fields;
	var $fieldnames;
	var $orders;
	var $default;

	/**
	* set an overwriding order from a url / form submit
	* @access public
	* @param string $field		-	order key
	*/
	function setAscOrder($field) {

		if (in_array($field, $this->fields)) {
			$this->orders['0'] = array('field' => $field);
		}
	}

	/**
	* set an overwriding order from a url / form submit
	* @access public
	* @param string $field		-	order key
	*/
	function setDescOrder($field) {

		if (in_array($field, $this->fields)) {
			$this->orders['0'] = array('field' => $field, 'direction' => 'DESC');
		}
	}

	/**
	* set a default order field if none other is set
	* @access public
	* @param string $default		-	default key
	*/
	function setDefault($default) {
		$this->default = $default;
	}

	/**
	* set the fields that are allowed. Prevents creation of database errors by improper orders submitted.
	* @access public
	* @param array $fields		-	array of allowed fields
	* @param array $fieldnames - array of field=>title for use with getOrderText
	*/
	function setFields($fields, $fieldnames = array()) {
		$this->fields = $fields;

		if (!$fieldnames) {
			$fieldnames = array_combine($fields, $fields);
		}

		$this->fieldnames = $fieldnames;
	}

	function setSpecificField($num, $field = null, $direction = null) {

		$order = $this->orders[$num];

		if (!is_null($field)) {
			$order['field'] = $field;
		}

		if (!is_null($direction)) {
			$order['direction'] = $direction;
		}

		$this->orders[$num] = $order;

	}

	/**
	* set an order. Function can be called multiple times, order determines which order the fields are used
	* @access public
	* @param array $fields		-	key to order on
	*/
	function setOrder($data) {
		$this->orders[] = $data;
	}

	function isOrderField($field) {
		foreach ($this->orders as $key => $order) {
			if ($order['field'] == $field) {
				return $key;
			}
		}
		return false;
	}

	function getOrderText() {

		$text = array();

		if ($this->orders) {
			$last = false;
			$last_ord = false;
			foreach ($this->orders as $info) {
				// Dont show two in a row
				if ($info['field'] == $last) {
					continue;
				}

				$last = $info['field'];
				$last_ord = $info['direction'];
				$text[] = $this->fieldnames[$info['field']] . ' (' . $info['direction'] . ')';
			}
		} else {
			$last = $this->default;
			$last_ord = 'ASC';
			$text[] = $this->fieldnames[$this->default] . ' (ASC)';
		}

		if (count($text) == 1 AND $last == $this->default AND strtoupper($last_ord) == 'ASC') {
			return '';
		}

		return implode(', ', $text);
	}

	/**
	* build the ordering SQL
	* @access public
	* @return the SQL fragment
	*/
	function build() {

		$this->fields = array_remove_empty($this->fields);

		if (is_array($this->orders)) {

			foreach ($this->orders AS $key => $order) {

				if (in_array($order['field'], $this->fields) AND !@in_array($order['field'], $doneorders)) {

					if (!$i) {
						$sql = ' ORDER BY ' . $order['field'];
						$i = true;

					} else {

						if (trim($sql) == 'ORDER BY') {
							$sql .= $order['field'];
						} else {
							$sql .= ', ' . $order['field'];
						}
					}

					$doneorders[] = $order['field'];

					// add direction
					if ($order['direction'] == 'DESC' AND $order['field'] != "") {
						$sql .= ' DESC';
					}
				}
			}
		}

		// add default
		if (!$sql AND $this->default) {
			$sql = "ORDER BY " . $this->default;
		}

		return ' ' . $sql . ' ';

	}
}

/**
* find key for a value in an array; mimics array_search() prior to PHP 4.2.0
* @access Public
* @param mixed $var					-	value for which key has to find out
* @param mixed array $array			-	array to search for key
* @return string					-	key if found
*/
function array_search_safe($var, $array) {
	if (($key = array_search($var, $array)) === false)
	{
		return;
	}
	else
	{
		return $key;
	}
}

/**
* write data to a file
* @access Public
* @param string $filename			-	path of file to which data to write
* @param string $data				-	data to be write
*/
function write_to_file($filename, $data) {

	$handle= fopen(ROOT . '/' . $filename, 'wb');
	fwrite($handle, $data);
	fclose($handle);

}

/**
* generates a random string
* @access Public
* @param int $length	-	length of string
* @return string		-	random string
*/
function make_randomstring($length, $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz') {

	for ($index = 1; $index <= $length; $index++) {

		// pick random number
		$randomnumber = dp_rand(1, strlen($chars));
		$password .= substr($chars, $randomnumber-1, 1);
	}

  return $password;

}

/**
 * Custom random number generator.
 *
 * @param int $min Min number to return
 * @param int $max Max number to return
 * @return int
 */
function dp_rand($min = 0, $max = 0) {

    // We seed and reseed so the random number isn't
    // "leaked" out and guessed.

    mt_srand(crc32(microtime()));

    if (!$max OR $max > mt_getrandmax()) {
        $max = mt_getrandmax();
    }

    $num = mt_rand($min, $max);

    mt_srand();

    return $num;
}

/**
* Return HTML to generate a link that produces a confirmation
* popup, redirecting to the specified URL if the user
* selects 'okay'
* @access Public
* @param string $message	-	Message to display
* @param string $url		-	URL to redirect to
* @param string $text		-	Link text to display
* @param string $danger		-	[Deprecated]
* @return string			-	html to generate link
*/
function jprompt($message, $url, $text, $danger=0, $extra = '') {

	$nonjs = "<a href=\"$url\" $extra>$text</a>";

	$message = addslashes_js($message);
	$url = addslashes_js($url);
	$text = addslashes_js($text);

	$js = "<a href=\"javascript:jprompt(\'$message\', \'$url\')\" $extra>$text</a>";
	return "<script language=\"JavaScript\">document.write('$js');</script><noscript>$nonjs</noscript>";

}

function jprompt_modal($text, $content, $url, $data = array(), $btn_pos = 'Continue', $btn_neg = 'Cancel') {

	global $_FOOTCONTENT;
	static $count = 0;

	++$count;

	$id = 'jprompt_modal_' . $count;
	$ret = '';

	if (!$_FOOTCONTENT) {
		$_FOOTCONTENT = '';
	}

	echo get_javascript_once('jqModal.js', LOC_INCLUDES . '3rdparty/jquery/jqModal/');

	/****************
	* Header part : content, form
	****************/

	$_FOOTCONTENT .= '<div style="display:none;" id="'.$id.'" class="jqmWindow"><form action="' . $url . '" method="post" style="margin:0;padding:0;">';

	foreach ($data as $name => $value) {
		$_FOOTCONTENT .= form_hidden($name, $value);
	}

	$_FOOTCONTENT .= "<div class=\"content\">$content</div>";

	/****************
	* Footer
	****************/
	$_FOOTCONTENT .= '<div class="footer">';

	if ($btn_pos) {
		$_FOOTCONTENT .= "&nbsp; <input type=\"submit\" name=\"submit\" value=\"$btn_pos\" /> &nbsp;";
	}

	if ($btn_neg) {
		$_FOOTCONTENT .= "&nbsp; <input type=\"button\" value=\"$btn_neg\" onclick=\"\$('#$id').jqmHide();\" /> &nbsp;";
	}

	$_FOOTCONTENT .= '</div>';

	$_FOOTCONTENT .= '</form></div><script type="text/javascript">$("#'.$id.'").jqm();</script>';

	/****************
	* Finish
	****************/

	$ret .= '<a href="#" onclick="$(\'#'.$id.'\').jqmShow(); return false;">'.$text.'</a>';

	return $ret;
}

/**
 * Create a custom modal display. To show, need to call:
 * <code>$('#$id').jqmShow()</code>
 *
 * @param string $content
 * @param footer $footer
 * @param string $id
 * @return string
 */
function custom_modal($content, $footer = '', $id = '__auto__') {

	static $count = 0;
	++$count;

	if ($id == '__auto__') {
		$id = 'custom_modal_' . $count;
	}

	$html = get_javascript_once('jqModal/jqModal.js', LOC_INCLUDES . '3rdparty/jquery/');

	/****************
	* Header part : content, form
	****************/

	$html .= '<div style="display:none;" id="'.$id.'" class="jqmWindow">';

	$html .= "<div class=\"content\">$content</div>";

	/****************
	* Footer
	****************/

	if ($footer) {
		$html .= '<div class="footer">';
		$html .= $footer;
		$html .= '</div>';
	}

	$html .= '</div><script type="text/javascript">$("#'.$id.'").jqm();</script>';

	return $html;
}

/**
*
* @access Public
* @param mixed array $array	-
* @param string $sub_key	-
* @return mixed array 		-
*/
function unique_multi_array($array, $sub_key) {

	$existing_sub_key_values = array();
	if (is_array($array)) {
		foreach ($array as $key=>$sub_array) {
			if (!in_array($sub_array[$sub_key], $existing_sub_key_values)) {
				$existing_sub_key_values[] = $sub_array[$sub_key];
				$target[$key] = $sub_array;
			}
		}
	}
   return $target;
}

/**
* Checks we are in the cron environment
* @access Public
* @return boolean	-	1 or exit
*/
function cron_check() {

	if (!defined('CRONZONE')) {
		exit();
	} else {
		return true;
	}
}

/**
* Function to check if a string is found in another string
* @access Public
* @param string $needle		-	the string to find
* @param string $haystack	-	the string to look in
* @return boolean			-	true or false
*/
function in_string($needle, $haystack) {

	if (strpos($haystack, $needle) === false) {
		return false;
	} else {
		return true;
	}
}

/**
* Transform input text into a database-safe, de-HTML-ized
* version, and return the result
* @access Public
* @param string $text		- Text to transform
* @return string			- transformed text
*/
function htmlchars($text) {
    $text = preg_replace('#&(?!\#[0-9]+;)#si', '&amp;', $text);
    $text = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $text);
    return $text;
}

/**
* Transform input text into a database-safe, de-HTML-ized
* version, and return the result
* @access Public
* @param string $text		- Text to transform
* @return boolean			- transformed text
*/
function unhtmlchars($text) {
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    $trans_tbl = array_flip ($trans_tbl);
    return strtr ($text, $trans_tbl);
}

/**
 * Convert HTML to plaintext with styling. Instead of simply tripping
 * HTML, it converts it into logical text: <b>bold</b> to *bold* etc.
 *
 * @param string $html The HTML to convert
 * @return string
 */
function text_from_html($html) {
	require_once(INC . '3rdparty/html2text/html2text.php');

	$h2t = new html2text(addslashes($html));

	return trim($h2t->get_text());
}

/**
* checks whether in developer mode or not
* @access Public
* @param boolean $noexit	-	a flag whether to show message or return false
* @return boolean			-	true or false
*/
function developer_check($noexit='') {

	if (defined('DESKPRO_DEBUG_DEVELOPERMODE')) {
		return true;
	} else {
		if (!$noexit) {
			mistake("You need to be in developer mode to complete this action");
		} else {
			return false;
		}
	}
}

/**
* Populates the global $settings array
* @access Public
* @return mixed array	-	array of settings
*/
function get_settings() {

	global $db;

	$settings = $db->query_return_array_id('SELECT name, value FROM settings', 'value', 'name');

	// do this to use cache and save a query
	get_detault_data();

	// When debug mode, do the query. Means enabling plugins without
	// worrying about the datastore
	if (defined('DESKPRO_DEBUG_DEVELOPERMODE')) {
		$settings['plugins'] = $db->query_return_array("SELECT * FROM plugins", 'intname');
	} else {
		$settings['plugins'] = get_data('plugin_settings');
	}

	load_plugin_files($settings['plugins']);

	return $settings;

}

function load_plugin_files($plugins) {

	static $loaded = array();

	if (!$plugins) {
		return;
	}

	foreach ($plugins as $name => $info) {

		if (!$info['installed']) {
			continue;
		}

		if (in_array($name, $loaded)) {
			continue;
		}

		$loaded[] = $name;

		if ($info['plugin_dir']) {
			@include_once(INC . "plugins/{$info['plugin_dir']}/load_plugins.php");
		}
	}
}

/**
* gets some data from the data table. Used to save
* running unecessary querie
* @access Public
* @param $name		-	name of the data field
* @return mixed		-	data for that name
*/
function get_data($name) {

	global $db, $datastore;

	if (isset($datastore['data'][$name])) {
		return $datastore['data'][$name];
	}

	$result = $db->query_return("
		SELECT data
		FROM data
		WHERE name = '" . $db->escape($name) . "'
	");

	if (!is_array($result)) {
		return array();
	}

	$data = unserialize($result['data']);

	$datastore['data'][$name] = $data;

	return $data;

}

/**
* gets all data from the data table. Used to save
* running unecessary querie
* @access Public
* @param string $name		-	name of the data field
*/
function get_detault_data() {

	global $db, $datastore;

	$db->query("
		SELECT name, data
		FROM data
		WHERE isdefault = 1
	");

	while ($result = $db->row_array()) {
		$datastore['data'][$result['name']] = unserialize($result['data']);
	}

}

function delete_data($name) {

	global $db;

	$db->query("
		DELETE FROM data
		WHERE name = '" . $db->escape($name) . "'
	");

}

/**
* updates data in database
* updated cached data
* name is a unique key so can just REPLACE INTO
* @access Public
* @param string $name		-	name of the data field
* @param string $data		-	the new data
* @param string				-	data
*/
function update_data($name, $data, $isdefault = 0) {

	global $db, $datastore;

	$datastore['data'][$name] = $data;

	$data = serialize($data);

	if ($isdefault) {

		$db->query("
			REPLACE INTO data SET
				name = '" . $db->escape($name) . "',
				data = '" . $db->escape($data) . "',
				isdefault = 1
		");

	} else {

		$db->query("
			REPLACE INTO data SET
				name = '" . $db->escape($name) . "',
				data = '" . $db->escape($data) . "'
		");

	}

	return $data;

}

/**
* ensures there are no long (non HTML) words
* @access Public
* @param string $text		-	the text to format
* @param int $cols			-	max length
* @param string	$cut		-	what to cut with
* @return string			-	wrapped text
*/
function do_wordwrap($text, $cols='100', $cut=' ') {

	$len=strlen($text);

	$tag=0;

	for ($i=0;$i<$len;$i++) {
		$chr = substr($text,$i,1);
		if ($chr=="<") {
			$tag++;
		} elseif ($chr==">") {
			$tag--;
		} elseif (!$tag AND ($chr==" " OR $chr=="\n" OR $chr=="\r")) {
			$wordlen=0;
			$spacer = 1;
		} elseif (!$tag) {
			$wordlen++;
		}

		if (!$tag AND !($wordlen%$cols)) {
			if (!$spacer) {
				$chr .= $cut;
				$spacer = 0;
			}
		}
		$result .= $chr;
	}

  return $result;
}


/**
* return mime type for email message
* @access Public
* @param string $type		-	type of mime
* @return string			-	mime type
*/
function get_mimetype($type) {

	require_once(INC . 'data/mimetypes.php');

	if (isset($mimetypes[$type])) {
		return $mimetypes[$var['extension']];
	} else {
		return 'application/octet-stream';
	}
}

/**
 * Try to guess an extension based on a mimetype
 *
 * @param string $mimetype The mime type specified (ie. image/gif)
 * @return string The guessed extension (may be empty if could not guess)
 */
function get_mimetype_extension($mimetype) {
	static $mimetypes = false;

	if (!$mimetypes) {
		include(INC . 'data/mimetypes.php');

		// Want type=>ext instead of ext=>type
		$mimetypes = array_flip($mimetypes);

		// Overwrite some that might be multiple things
		$mimetypes['application/msword'] = 'doc';
		$mimetypes['application/octet-stream'] = 'bin';
		$mimetypes['application/vnd.ms-excel'] = 'xls';
		$mimetypes['application/vnd.ms-powerpoint'] = 'ppt';
		$mimetypes['text/html'] = 'html';
		$mimetypes['text/plain'] = 'txt';
		$mimetypes['video/mpeg'] = 'mpeg';
		$mimetypes['video/quicktime'] = 'mov';
	}

	$ext = '';

	if (isset($mimetypes[$mimetype])) {
		$ext = $mimetypes[$mimetype];
	}

	return $ext;
}

/**
* get category name by id
* @access Public
* @param int $id		-	id of category
* @return string		-	category name
*/
function get_category_name($id) {

	global $db;

	if (is_numeric($id)) {
		$tmp = $db->query_return("SELECT name FROM ticket_cat WHERE id = '" . $db->escape($id) . "'");
		$id = $tmp['name'];
	}
	if (!$id) {
		$id = 'Not categorized';
	}
	return $id;
}

/**
 * Make an array of options for a full category select.
 *
 * @param array $cats Categories to add to the array
 * @param int $parent The parent to process
 * @return array Options ready to use in a select
 */
function get_full_category_options($cats, $parent = 0, $indent_str = '- ') {

	global $cache2;

	$options = array();

	foreach ($cats as $id => $name) {

		$catinfo = $cache2->getCategory($id);
		$subcats = array();

		if ($catinfo AND $catinfo['parent'] != $parent) {
			continue;
		} else {

			unset($cats[$id]);

			// Only 2 levels deep
			if ($catinfo AND $catinfo['parent'] == 0) {
				$subcats = get_full_category_options($cats, $catinfo['id'], $indent_str);
			}

			$options[$id] = ($parent != 0 ? $indent_str : '') . $name;

			if ($subcats) {
				foreach ($subcats as $sub_id => $sub_name) {
					$options[$sub_id] = $sub_name;
				}
			}
		}
	}

	return $options;
}

/**
 * Generate the Javascript array for subcategories
 *
 * @param string $type 'user' or 'tech', which area to get the cats from
 * @param string $when 'new' or 'edit', when the selects are being displayed (only used for $type 'user')
 */
function get_subcategory_preload_js($type = 'user', $when = 'new', $show_none = true, $varname = 'DP_Subcats') {

	if ($type == 'user') {
		global $session;
		$parent_cats = user_category_array($when, NULL, $session['userid'], $session['language'], true, 0);
	} else {
		global $cache2;

		if ($type == 'admin') {
			$parent_cats = $cache2->getCategoryNames($show_none, '', 0);
		} else {
			$parent_cats = $cache2->getCategoryNamesPermission($show_none, '', 0);
		}
	}

	$subcats_js = '';

	if ($varname) {
		$subcats_js = "var $varname = ";
	}

	$subcats_js .= "{\n\n";

	foreach ($parent_cats as $catid => $name) {

		$subcats = false;

		if ($type == 'user') {
			$subcats = user_category_array($when, NULL, $session['userid'], $session['language'], true, $catid);
		} else if ($type == 'admin') {
			$subcats = $cache2->getCategoryNames(false, '', $catid);
		} else {
			$subcats = $cache2->getCategoryNamesPermission(false, '', $catid);
		}


		if ($subcats AND $catid) {
			if ($type == 'user') {
				$subcats_js .= "parent_$catid: \"" . addslashes_js(form_select('subcategory', $subcats), false) . "\",\n";
			} else {
				$subcats_js .= "parent_$catid: \"" . addslashes_js(form_select('subcategory', $subcats), false) . "\",\n";
			}
		} else {
			$subcats_js .= "parent_$catid: false,\n";
		}
	}

	$subcats_js = substr($subcats_js, 0, -2);
	$subcats_js .= "\n\n};";

	return $subcats_js;
}

function get_incoming_category($parent_name = 'category', $sub_name = 'subcategory', $from = 'request') {

	global $request, $cache2;

	$parent = $request->getNumber($parent_name, $from);

	if (!$parent) {
		return 0;
	}

	$parent_info = $cache2->getCategory($parent);
	$parent_childs = $cache2->getCategoryChildren($parent);

	if (!$parent_info) {
		return 0;
	}

	if (!$parent_childs) {
		return $parent;
	}


	$child = $request->getNumber($sub_name, $from);

	if (!in_array($child, $parent_childs)) {
		return $parent_childs[0];
	} else {
		return $child;
	}
}

/**
* get category name by id
* @access Public
* @param int $id		-	id of priority
* @return string		-	priority name
*/
function get_priority_name($id) {

	global $db;

	if (is_numeric($id)) {
		$tmp = $db->query_return("SELECT name FROM ticket_pri WHERE id = '" . $db->escape($id) . "'");
		$id = $tmp['name'];
	}
	if (!$id) {
		$id = 'Not prioritized';
	}
	return $id;
}

/**
* displays print_r in <pre> tags for easy viewing
* @access Public
* @param mixed array $var	-	array that to has to be displayed
* @return string		-	priority name
*/
function print_rr($var, $echo = true) {
	$str = '<pre>' . print_r($var, true) . '</pre>';

	if ($echo) {
		echo $str;
		return '';
	} else {
		return $str;
	}
}

// this is an alias to print__rr that is meant to remain in the code
// as opposed to being there for debug.
function print__rr($var, $echo = true) {
	return print_rr($var, $echo);
}

/**
 * Like print_r but returns instead of outputs
 *
 * @param mixed $var
 * @return string
 */
function print__rr_return($var) {
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	return $str;
}

/**
* get ip address of user computer
* @access Public
* @return string		-	ip address
*/
function fetchip() {
	global $_SERVER;

	//get useful vars:
	$client_ip = $_SERVER['HTTP_CLIENT_IP'];
	$x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote_addr = $_SERVER['REMOTE_ADDR'];

	// then the script itself
	if (!empty ($client_ip) ) {
	// Turning the ip adress around if it's saved backwards
		$ip_expl = explode('.',$client_ip);
		$referer = explode('.',$remote_addr);

		if($referer[0] != $ip_expl[0]) {
			$ip=array_reverse($ip_expl);
			$return=implode('.',$ip);
		} else {
			$return = $client_ip;
		}
	} elseif (!empty($x_forwarded_for) ) {
		if (strstr($x_forwarded_for,',')) { // making sure the ip adress isn't a large chain of proxy's, and retrieving only the real one.
			$ip_expl = explode(',',$x_forwarded_for);
			return end($ip_expl);
		} else {
			return $x_forwarded_for;
		}
	} else {
		return $remote_addr;
	}
	return $return;
}

/**
* Add escape sequence for special characters like ', " and new line.
* @access Public
* @param string $text Text to which escape sequence has to be added
* @param bool $single_quote True if the  string will be used in a single-quoted string
* @return string Replaced string
*/
function addslashes_js($text, $single_quote = false) {
    $str = str_replace(array('\\', '\'', '"', "\n", "\r"), array('\\\\', "\'", '\\"', "\\n", "\\r"), trim($text));

    $quote = '"';
    if ($single_quote) {
    	$quote = "'";
    }

    $str = preg_replace('#((scr)(ipt))#i', "\\2{$quote} + {$quote}\\3", $str);
    return $str;
}

/**
 * Convert a simple PHP array into a simple JS obj (json)
 *
 * @param array $array Array to convert
 * @return string JS
 */
function array_to_js_obj($array) {

	$is_array = false;
	if ($array[0]) {
		$is_array = true;
	}

	if ($is_array) {
		$js = "[\n";
	} else {
		$js = "{\n";
	}

	foreach ($array as $key => $item) {

		if (!$is_array) {
			$js .= "'$key': ";
		}

		if (is_numeric($item)) {
			$js .= "$item,\n";
		} else if (is_array($item)) {
			$js .= array_to_js_obj($item) . ",\n";
		} else if (is_bool($item)) {
			$js .= ($item ? 'true' : 'false') . ",\n";
		} else if (preg_match('#^\(JS LITERAL:\)#', $item)) {
			$js .= preg_replace('#^\(JS LITERAL:\)#', '', $item, 1) . ",\n";
		} else {
			$item = addslashes_js($item, true);
			$js .= "'$item',\n";
		}
	}

	if (count($array)) {
		$js = substr($js, 0, -2);
	}

	if ($is_array) {
		$js .= "\n]";
	} else {
		$js .= "\n}";
	}

	return $js;
}

/**
* checks if value is blank then assigns default value to variable
* @access Public
* @param string $var	-	reference to variable to check
* @param string $value	-	default value of varialbe
*/
function give_default(&$var, $default) {

	if (!isset($var) OR trim($var) == '' OR !$var) {
		$var = $default;
	}
}

/**
 * Return the first truthy value
 *
 * @return mixed
 */
function coalesce() {
	foreach (func_get_args() as $v) {
		if ($v) {
			return $v;
		}
	}

	return false;
}

/**
 * Check if arg1 is any of the other arguments.
 * Basically a quick way of in_array($arg1, array(...)).
 *
 * @param mixed $check The thing to check for
 * @param mixed ... The thing to check against
 * @return boolean
 */
function ifin() {

	if (func_num_args() < 2) {
		return false;
	}

	$things = func_get_args();
	$check = array_shift($things);

	if (in_array($check, $things)) {
		return true;
	}

	return false;
}

/**
 * Return $param if it is set, else return $or
 *
 * @param mixed $param
 * @param mixed $or
 * @return mixed
 */
function ifsetor(&$param, $or) {
	if(isset($param)) {
		return $param;
	}
	return $or;
}

/**
 * Return $param if it is truthy, else return $or
 *
 * @param mixed $param
 * @param mixed $or
 * @return mixed
 */
function ifvalor($param, $or) {
	if($param) {
		return $param;
	}
	return $or;
}

/**
* set a minimum value for the variable
* @access Public
* @param string $var	-	reference to variable to check
* @param string $value	-	minimum value of varialbe
*/
function give_min(&$var, $min) {

	if ($var < $min) {
		$var = $min;
	}
}

/**
* set a maxmimum value for the variable
* @access Public
* @param string $var	-	reference to variable to check
* @param string $value	-	maximum value of varialbe
*/
function give_max(&$var, $max) {

	if ($var > $max) {
		$var = $max;
	}
}

/**
* compare two values
*
* @access	Public
*
* @param	mixed	 first value
* @param	mixed	second value
* @param	int	check for zero or blank value of both variables if yes return false
*
* @return	 boolean	true or false
*/
function compare_value($key, $var, $strict) {

	if ($strict) {
		if ($var == '0' AND $key != '0') {
			return false;
		}
		if ($var === '' AND $key !== '') {
			return false;
		}
		if ($key == '0' AND $var != '0') {
			return false;
		}
		if ($key === '' AND $var !== '') {
			return false;
		}
	}

	if ($key == $var) {
		return true;
	}
	return false;
}

/**
* checks if value is an element of array other wise returns default value
* @access Public
* @param string $value1			-	value to check in array
* @param string $value2			-	default value
* @param mixed array $options	-	array to search
* @return string				-	if element exists then original value otherwise default value
*/
function default_in_array($value1, $value2, $options) {

	if (trim($value1) != '' AND in_array($value1, $options)) {
		return $value1;
	} else {
		return $value2;
	}
}

/**
* Make ticketlog
* @access Public
* @param int $ticketid		-	Ticket ID for the event
* @param int action			-	Name for the event
* @param int id_before		-	[Optional] The changed attribute's previous ID
* @param int id_after		-	[Optional] The changed attribute's new ID
* @param int detail_before	-	[Optional] The changed attribute's previous detail
* @param int detail_after	-	[Optional] The changed attribute's new detail
* @param int extra			-	[Optional] Additional data for the entry
* @return int				-	0 or 1
*/
function ticketlog($ticketid, $action, $id_before=NULL, $id_after=NULL, $detail_before=NULL, $detail_after=NULL, $extra='', $force_agent = '') {

	global $cache, $user, $db;

	if (is_array($extra)) {
		$extra = serialize($extra);
	}

	// checks to prevent ticketlogs for data not being changed
	if ($id_before != NULL) {
		if ($id_before == $id_after) {
			return;
		}
	}
	if ($detail_before != NULL) {
		if ($detail_before == $detail_after) {
			return;
		}
	}

	if (defined('ESCALATEZONE')) {

		$agent = 'escalate';

	// userzone (we have $user array for the user)
	} else if (defined('USERZONE')) {

		$techid = 0;
		$userid = $user['id'];

	// can be either tech or user. global variables used.
	} elseif (defined('GATEWAYZONE')) {

		global $gateway_ticketlog_userid, $gateway_ticketlog_techid;

		if ($gateway_ticketlog_userid) {
			$userid = $gateway_ticketlog_userid;
		} else {
			$techid = $gateway_ticketlog_techid;
		}

		$agent = 'gateway';

	// techzone / adminzone (we have $user for tech/admin)
	} else {

		$techid = $user['id'];
		$userid = 0;

	}

	if ($force_agent) {
		$agent = $force_agent;
	}

	$cache['ticketlog'][] = array(
		'ticketid' => $ticketid,
		'timestamp' => TIMENOW,
		'action' => $action,
		'techid' => intval($techid),
		'userid' => intval($userid),
		'id_before' => intval($id_before),
		'id_after' => intval($id_after),
		'detail_before' => $detail_before,
		'detail_after' => $detail_after,
		'extra' => $extra,
		'agent' => $agent
	);

	// need to run query if not shutdown functions
	if (defined('NOSHUTDOWNFUNCTIONS')) {
		$db->query("
			INSERT INTO ticket_log (ticketid, timestamp, actionlog, techid, userid, id_before, id_after, detail_before, detail_after, extra, agent) VALUES " . multi_array2sql($cache['ticketlog']) . "
		");
		unset($cache);
	}

	return 1;
}

/**
* This runs all the ticket log queries
* @access Public
*/
function ticketlog_run() {

	global $cache, $db;

	if (isset($cache['ticketlog']) AND is_array($cache['ticketlog'])) {

		$db->query("
			INSERT INTO ticket_log (ticketid, timestamp, actionlog, techid, userid, id_before, id_after, detail_before, detail_after, extra, agent) VALUES " . multi_array2sql($cache['ticketlog']) . "
		");

	}

	unset($cache['ticketlog']);

}

/**
* Verifies the specified e-mail is not currently in use by another user
* @access Public
* @param string $email	-	Address to check for
* @return boolean		-	true if unique or false
*/
function unique_email($email, $only_valid = false) {

	global $db;

	if ($db->query_amatch('user_email',  "email = '" . $db->escape($email) . "' " . ($only_valid ? " AND validated = 1" : ''))) {
		return false;
	}

	return true;

}

function user_from_field($field, $value) {

	global $db;

	if (!$value) {
		return false;
	}

	if ($field == 'id') {
		$userid = $value;
	} else {
		$userid = UserFind::find($value, $field);
	}

	if ($userid) {
		return $db->query_return("
			SELECT user.*, user_email.email AS email
			FROM user
			LEFT JOIN user_email ON (user_email.userid = user.id AND user_email.id = user.default_emailid)
			WHERE user.id = $userid
		");
	}

	return false;
}


/**
* Look up a user's ID by the specified e-mail address
* @access Public
* @param string $email			-	E-mail address to search by
* @param boolean $validated		-	Only check validated userids
* @return int					-	The user's ID if found, false if not found
*/
function userid_from_email($email) {
	return UserFind::findByEmail($email);
}

/**
* Look up a user's details by the specified e-mail address
* @access Public
* @param string $email	-	E-mail address to search by
* @return int			-	mysql record set id
*/
function user_from_email($email) {
	return user_from_field('email', $email);

}

/**
 * Lookup a users details by their username. There may be
 * multiple sources that result in a username belonging to more
 * then one user. Only the first result is returned.
 *
 * @param unknown_type $name
 */
function user_from_username($name) {
	return user_from_field('username', $name);
}

/**
 * Get the local userid form a remote userid (looks it up in user_map)
 *
 * @param mixed $remoteid
 * @param integer $sourceid
 * @return integer
 */
function userid_from_remoteid($remoteid, $sourceid) {

	global $db;

	$userid = $db->query_return_first("
		SELECT localid FROM user_map
		WHERE remoteid = '" . $db->escape($remoteid) . "' AND sourceid = $sourceid
	");

	return $userid;
}

/**
 * Get the local user record from a remote id. This looks up the local
 * userid in user_map, and then fetches the user record.
 *
 * @param mixed $remoteid
 * @param integer $sourceid
 * @return array
 */
function user_from_remoteid($remoteid, $sourceid) {

	$userid = userid_from_remoteid($remoteid, $sourceid);

	if (!$userid) {
		return false;
	}

	return user_from_field('id', $userid);
}

/**
 * Get the user record for a deskpro-managed user. Only
 * useful if the despro source exists. Pretty much only used
 * in lost password form, editing a username/passsword from tech etc.
 *
 * @param mixed $id
 * @param string $type
 * @return UserSrcRec_Dp
 */
function get_deskpro_userrec($id, $type = false) {
	global $db, $cache2;

	$sourceinfo = array_shift($cache2->getUsersourcesOfType('dp'));

	// deskpro source not enabled
	if (!$sourceinfo) {
		return 0;
	}

	/******************************
	* Check email
	******************************/

	if (is_email($id)) {
		$userid = UserFind::findByEmail($id, false);

		// Check to make sure the user IS a deskpro user
		$remoteid = $db->query_return_first("
			SELECT remoteid FROM user_map
			WHERE localid = $userid AND sourceid = $sourceinfo[id]
		");

		if ($remoteid) {
			$id = $remoteid;
			$type = 'id';
		}
	}


	$source = UserSrc::createSource($sourceinfo);
	$source->loadUsers($id, $type);

	if ($source->size()) {
		return $source->getUserRecord(0);
	}

	return false;
}

/**
 * Get user details from a deskpro id.
 *
 * @param unknown_type $id
 * @return unknown
 */
function user_from_deskpro($id, $type = false) {

	global $db, $cache2;

	$sourceinfo = array_shift($cache2->getUsersourcesOfType('dp'));

	// deskpro source not enabled
	if (!$sourceinfo) {
		return 0;
	}

	/******************************
	* Check emails first
	******************************/

	if (is_email($id)) {
		$userid = UserFind::findByEmail($id, false);

		// Check to make sure the user IS a deskpro user
		$is_local = $db->query_return_first("
			SELECT remoteid FROM user_map
			WHERE localid = $userid AND sourceid = $sourceinfo[id]
		");

		if (!$is_local) {
			$userid = 0;
		}
	}

	/******************************
	* Nothing from the email, try a lookup
	******************************/

	if (!$userid) {
		$userrec = get_deskpro_userrec($id, $type);

		if (!$userrec) {
			return false;
		}

		$userid = $db->query_return_first("
			SELECT localid FROM user_map
			WHERE remoteid = " . $userrec->getId() . " AND sourceid = $sourceinfo[id]
		");
	}


	if (!$userid) {
		return false;
	}

	$user_details = user_from_field('id', $userid);

	return $user_details;
}

/**
 * Get user details from passed param. Use this when
 * you need user_details array but don't know if the
 * passed variable is an array already, or an object
 * already loaded, or an id/email etc.
 *
 * @return array|boolean user details or false on error
 */
function get_user_details($getuser) {

	if (is_object($checkuser) AND is_a($getuser, 'User')) {
		$user_details = $getuser->getUser();

	} elseif (is_object($getuser) AND is_a($getuser, 'UserAuth')) {
		$user_details = $getuser->getUser();

	} elseif (is_array($getuser)) {
		$user_details = $getuser;

	} elseif (is_email($getuser)) {
		$user_details = user_from_email($getuser);

	} elseif (is_numeric($getuser)) {
		$user_details = user_from_field('id', $getuser);

	} else {
		$user_details = false;
	}

	return $user_details;
}

/**
 * Test for a match in a simple wildcard string like *@hotmail.com
 * using asterisk as a wildcard.
 *
 * @param string $pattern Pattern to match against
 * @param string $string String to test
 * @param bool $icase Case insensitive test
 * @return bool
 */
function wildcard_pattern_match($pattern, $string, $icase = false) {

	$pattern = str_replace('*', 'DESKPRO_REPLACE_WILDCARD', $pattern);
	$pattern = preg_quote($pattern, '#');
	$pattern = str_replace('DESKPRO_REPLACE_WILDCARD', '(.*?)', $pattern);

	$flags = ($icase ? 'i' : '');

	return preg_match("#^{$pattern}$#{$flags}", $string);
}

/**
* Validate a specified URL
* @access Public
* @param string $url		-	URL to validate
* @return boolean			-	True if valid, false if invalid
*/
function validate_url($url) {

	$regex = "/(ftp|http|https|telnet|news|nntp|file|gopher):\/\/([a-z0-9~#%@&:;=!',_???\(\)\?\/\.\-\+\[\]\|\*\$\^\{\}]+)/i" ;

	if (preg_match($regex, $url)) {
		return true;
	} else {
		return false;
	}
}

/**
* Validate a specified refL
* @access Public
* @param string $ref		-	[0000-AAAA-0000]
* @return boolean			-	True if valid, false if invalid
*/
function is_ref($ref) {

	$regex = "/[0-9]{4}-[A-Za-z]{4}-[0-9]{4}/";

	if (preg_match($regex, $ref)) {
		return true;
	} else {
		return false;
	}
}

/**
* validates an email address
* @access Public
* @param string $email		-	the email adddress
* @return string			-	the email address on match, null if it fail
*/
function is_email($email) {

	$email = trim($email);

	if (substr_count($email, '@') != 1) {
		return false;
	}

	list($name, $domain) = explode('@', $email);

	// Match the part before the @
	$regex_name = "#^[a-z0-9!\\#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!\\#$%&'*+/=?^_`{|}~-]+)*$#i";

	// Match a regular domain name after the @
	$regex_domain = "#^(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2,6})$#i";

	// Match a IP address after the @
	$regex_ip = '#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#i';

	if (!preg_match($regex_name, $name)) {
		return false;
	}

	if (!preg_match($regex_domain, $domain) AND !preg_match($regex_ip, $domain)) {
		return false;
	}

    return true;
}

/**
* Determine whether a specified e-mail address is in
* the banned e-mail address list.
* @access Public
* @param string $email		-	E-mail address to check
* @return boolean			-	True if the address is banned, false otherwise
*/
function banned_email($email) {

	global $db;

	// check for unique email addresses
	if ($db->query_amatch('ban_email', "email = '" . $db->escape($email) . "'")) {
		return 1;
	}

	// check for wildcard matching
	return regex_match($email, get_data('email_ban'));
}

/**
* Validates a username
* @access Public
* @param string $username	-	Username to validate
* @return boolean			-	True if the address is banned, false otherwise
*/
function validate_username($username) {

	global $settings;

	// length of username
	if (strlen($username) < $settings['min_username_length']) {
		return 0;
	}
	if (preg_match('#[^\w_\-\.@]#i', $username)) {
		return 0;
	}

	return 1;
}

/**
* Verifies a username is not already in use
* @access Public
* @param string $username	-	Username to validate
* @return boolean			-	True if the address is banned, false otherwise
*/
function unique_username_deskpro($username) {

	global $db;

	if ($db->query_amatch('user_deskpro', "username = '" . $db->escape($username) . "'")) {
		return false;
	}

	return true;

}

function preg_match_array($pattern, $subjects, $retainkey = false) {

	if (!is_array($subjects)) {
		$subjects = array($subjects);
	}

	foreach ($subjects AS $subject) {
		if (preg_match($pattern, $subject)) {
			$newarray[] = $subject;
		}
	}

	return $newarray;

}

/**
* Checks whether a value exists in a string by
* regular expression, or whether an exact value
* exists within a specified array.
* @access Public
* @param string / array $string	-	String to search or Array to search through
* @param string $checks			-	Regular expression to match against string
*									if "string" is a regular string, or an
*									exact value to check for within "string"
*									if it is an array.
* @return boolean				-	True if a match is found, false if not.
*/
function regex_match($string, $checks) {

	if (!is_array($checks) AND !$checks) {
		return false;
	} elseif (!is_array($checks)) {
		$checks = array($checks);
	}

	foreach ($checks AS $key => $var) {

		if (isset($var) AND $var != '') {

			// preg checks on * wildcard
			if (strstr($var, '*')) {

				// get wildcards and quote
				$check = str_replace('\*', '(.*)', preg_quote($var, '#'));

				// delimiter + assert start and end
				$check = '#^'. $check .'$#';

				if (!empty($check) AND preg_match($check, $string)) {
					return 1;
				}

			// standard check for identical
			} elseif (strval($string) === strval($var)) {
				return 1;
			}
		}
	}

	return 0;
}

/**
* Set a cookie.
* @access Public
* @param string $name	-	Cookie name
* @param string $value	-	Cookie value
* @param int $length	-	[Optional] Cookie lifetime (in seconds)
* @param bool $httponly -   HttpOnly flag?
*/
function dp_setcookie($name, $value, $length=NULL, $httponly = true) {
	global $settings;

	$time = (int)time();
	if ($length == 'ever') {
		$time += (int)(60 * 60 * 24 * 365 * 10); // 10 years
	} elseif ($length > 0) {
		if ($length > $time) {
			$time = $length;
		} else {
			$time += (int)$length;
		}
	} elseif ($length == -1) {
		$time = 1; $value = '';
	} else {
		$time = NULL;
	}

	if (!$settings['cookie_path']) {
		$settings['cookie_path'] = '/';
	}

	if (!$httponly) {
		setcookie($name, $value, $time, $settings['cookie_path']);
	} else {
		// Before 5.2 we need to create our own HttpOnly cookies
		if (version_compare(PHP_VERSION, '5.2', '<')) {

			$name = urlencode($name);
			$value = urlencode($value);
			$cookie = "Set-Cookie: $name=$value";

			if ($time) {
				$cookie .= "; expires=" . gmdate('D, d-M-Y H:i:s T', $time);
			}

			if ($settings['cookie_path']) {
				$cookie .= "; path=$settings[cookie_path]";
			}

			$cookie .= '; HttpOnly';

			header($cookie, false);

		// We can use the HttpOnly param with PHP>=5.2
		} else {
			setcookie($name, $value, $time, $settings['cookie_path'], null, null, true);
		}
	}
}

function frq_check() {

	if (dp_rand(1,1000) == 1) {
		return true;
	}
	return false;
}

/**
* Generate the data subclause for MySQL's multiple-insert
* query syntax from the given flat array.
* @access Public
* @param string array $array	-	Array of items to add (should be one-dimensional)
* @return string				-	The subclause, suitable for use in a query like
									"INSERT INTO TABLE (col1, ... coln) VALUES $foo",
									where $foo is the return value of this function.
*/
function insertsql($array) {

	global $db;

	if (!is_array($array)) {
		return NULL;
	}

	foreach ($array AS $key => $var) {
		$sql .= "(";

		if (is_array($var)) { // multi column array
			foreach ($var AS $key2 => $bit) {
				$sql .= '\'' . $db->escape($bit) . '\' ,';
			}
		} else { // only two column array
			$sql .= "'" . $db->escape($key) . '\',\'' . $db->escape($var) . '\',';
		}

		$sql = substr($sql, 0, -1);
		$sql .= '), ';
	}

	$sql = substr($sql,0,-2);
	return $sql;
}

/**
* Search in an array for a specific key only in
* non-empty elements
* @access Public
* @param string $needle		-	Item to search for
* @param string $haystack	-	Array containing items to search
* @return boolean			-	True if found, false if not.
*/
function in_array_keys($needle, $haystack) {

	if (is_array($haystack)) {
		foreach ($haystack AS $key => $var) {
			if ($needle == $key) {
				return true;
			}
		}
	}
	return false;
}

/**
* an overwrite default function, ie we send what is submitted,
* unless given a certain lack of a condition send the default
* @access Public
* @param string $yes		-
* @param boolean $condition	-
* @param string $default	-
* @return string			-
*/
function if_default($yes, $condition='', $default='') {

	if ($yes) {
		return $yes;
	}
	if (!$condition) {
		return $default;
	}
}

###################### function iff() #######################

/**
* returns either $yes or $no depending upon value of $condition
* @access Public
* @param boolean $condition	-	condition output
* @param mixed $yes			-	if condition is true return this value
* @param mixed $no			-	if condition is false return this value
* @return mixed				-	either $yes or $no
*/
function iff($condition, $yes=1, $no='') {
	return $condition ? $yes : $no;
}

###################### function ifr() #######################

/**
* returns either $yes or $no depending upon existance of variable $yes
* @access Public
* @param mixed $yes			-	first variable
* @param mixed $no			-	second variable
* @return mixed				-	either $yes or $no
*/
function ifr($yes = 1, $no = '') {
	return $yes ? $yes : $no;
}

###################### function ifyn() #######################

/**
* returns either "yes" or "no" depending upon value of $condition
* @access Public
* @param boolean $condition	-	condition output
* @return string			-	either "yes" or "no"
*/
function ifyn($condition) {
	return $condition ? 'Yes' : 'No';
}

###################### function ifyn() #######################

/**
* returns either "yes" or "no" depending upon value of $condition
* @access Public
* @param boolean $condition	-	condition output
* @return string			-	either "yes" or "no"
*/
function ifny($condition) {
	return $condition ? 'No' : 'Yes';
}

###################### function ifynb() #######################

/**
* returns 1 if condition is true and 0 if false
* @access Public
* @param boolean $condition	-	condition output
* @return boolean			-	1 / 0
*/
function ifynb($condition) {
	return $condition ? 1 : 0;
}

###################### function multi_array2sql($array) #######################

/**
* generates comma separated string for values in multiple arrays
* @access Public
* @param mixed array $array	-	array containing values
* @return string			-	comma separated values
*/
function multi_array2sql($array) {

	global $db;

	if (!is_array($array)) {
		return false;
	}

	$sql = array();

	foreach ($array AS $key => $var) {
		$tmp = array();

		foreach ($var AS $key2 => $var2) {
			$tmp[] = '\'' . $db->escape(trim($var2)) . '\'';
		}

		$sql[] = '(' . join(',', $tmp) . ')';
	}

	$sql = join(',', $sql);
	return $sql;

}

###################### function array2sql() #######################

/**
* generates comma separated string for values in a array
* @access Public
* @param mixed array $array	-	array containing values
* @return string			-	comma separated values
*/
function array2sql($array) {

	global $db;

	$sql = '(';

	$array = @array_unique($array);
	if ((is_array($array)) AND (count($array))) {
		foreach($array AS $key => $var) {
			if (is_array($var)) {
				$sql .= $db->escape(trim($var)) . ',';
			} else {
				$sql .= '"' . $db->escape(trim($var)) . '",';
			}
		}
		$sql = substr($sql,0,-1);
		$sql .= ")";
	} else {
		$sql = "('')";
	}

	return $sql;
}

/**
* generates insert statement for keys and values in a array
* @access Public
* @param mixed array $array	-	array containing keys and values
* @return string			-	insert statement
*/
function array2sqlinsert($array) {

	global $db;

	$sql = '';

	if (!is_array($array)) {
		return;
	}

	foreach ($array AS $key => $var) {
	    if (is_array($var) AND $var[0] == 'NULL') {
	        $var = 'NULL ';
	    } else {
	        $var = "'" . $db->escape($var) . "'";
	    }
		$sql .= "$key = $var, ";
	}

	$sql = substr($sql, 0, -2);

	return ' ' . $sql . ' ';

}

function trimstring_chars($string, $length) {
	if (!$string) {
		return;
	}
	return substr($string, 0, $length) . "&nbsp;...";
}

/**
* trim string to specified length
* @access Public
* @param string $string		-	string to be trimmed
* @param int $length		-	desired length
* @param boolean $dots		-	flag whether to append dots to trimmed string
* @return string			-	trimmed string
*/
function trimstring($string, $length, $dots='') {

	// trim word, remove broken words
	if (strlen($string) > $length) {

		$string_c = substr($string, 0, strrpos(substr($string, 0, $length), ' '));

		// check we are not left with nothing
		if (!strlen(trim($string_c))) {
			$string_c = substr($string, 0, $length);
		}

		// add dots
		if ($dots) {
			// dot string provided
			if (is_string($dots)) {
				$string_c .= $dots;
			} else {
				$string_c .= '&nbsp;...';
			}
		}

		return $string_c;

	} else {

		return $string;

	}
}

/**
* Generate a random password of specified length.
* @access Public
* @param int $length		-	Length of password to generate
* @return string			-	New random password
*/
function make_pass($length='8'){

    $vowels = array("a", "e", "i", "o", "u");
    $cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr",
    "cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");

    $num_vowels = count($vowels);
    $num_cons = count($cons);

    for($i = 0; $i < $length; $i++){
        $password .= $cons[dp_rand(0, $num_cons - 1)] . $vowels[dp_rand(0, $num_vowels - 1)];
    }

    return substr($password, 0, $length);
}

/**
* generates a username from a name or email address
* numbers are added to get a unique username
*
* @access Public
* @param string $email		-	E-mail address
* @param string $name		-	[Optional] Requested name
* @return string			-	New username.
*/
function make_username_deskpro($email, $name='') {

	global $db, $settings;

	$i = 0;

	if (strlen($name) > 2) {
		$username = trim($name);
	} else {
		// Grab everything leaving up to the @ and strip out everything that's not alphanumeric.
		$username = substr($email, 0, strpos($email, '@'));
	}

	$username = preg_replace('([^_a-zA-Z0-9\-\.])', '', $username);

	// If its under the required length, make a new one
	if (strlen($username) < $settings['min_username_length']) {
		if (!$username) {
			$username = make_pass();
		} else {
			$username .= date('dm', TIMENOW);
		}
	}

	// Check to see if we need to add a number to it
	if ($db->query_match('user_deskpro', "username='" . $db->escape($username) . "'")) {

		// Start with .1
		// - We use the dot so if a username is 'user1002346540540650465404', we dont try
		// to increment that huge number (would result in an integer overflow)
		$username .= '.1';

		// Still exists? Then keep adding +1 til its unique
		if ($db->query_match('user_deskpro', "username='" . $db->escape($username) . "'")) {

			// Username without trailing numbers
			$username = preg_replace('#^(.*?)([0-9]+)$#', '$1', $username);

			$db->query("
				SELECT username
				FROM user_deskpro
				WHERE username LIKE '" . $db->escape_like($username) ."%'
			");

			$highest = 0;

			while ($row = $db->row_array()) {

				$matches = array();

				if (preg_match('#^' . preg_quote($username) . '([0-9]+)$#i', $row['username'], $matches)) {
					if ($matches[1] > $highest) {
						$highest = $matches[1];
					}
				}
			}

			$append_num = $highest + 1;
			$username .= $append_num;

			// Final resort, a totally random name
			while ($db->query_match('user_deskpro', "username='" . $db->escape($username) . "'")) {
				$username = make_pass(10);
			}
		}
	}

	return $username;
}


/**
* explode a string into an array but remove any
* emtyy elements and do not return an array if no matches
* @access Public
* @param string $split		-	what we are splitting on
* @param string $string		-	the string
* @return array				-	array or null
*/
function explode_empty($split, $string) {

	$tmp = explode($split, $string);
	foreach ($tmp AS $key => $var) {
		if ($var != '') {
			$array[] = $var;
		}
	}
	return $array;
}

/**
* Generate a unique, valid ticketref.
* @access Public
* @return string		-	New ticketref.
*/
function make_ticket_ref() {

	global $db;

	do {

		$ref = make_table_ref('ticket');

		if ($ref AND $db->query_amatch('ticket_delete_log', "ticketref = '" . $db->escape($ref) . "'")) {
			unset($ref);
		}

		if ($ref AND $db->query_amatch('ticket_merge', "old_ref = '" . $db->escape($ref) . "'")) {
			unset($ref);
		}

	} while (!$ref);

	return $ref;

}

/**
* Generate a unique, valid ref.
* @access Public
* @param string $table	-	name of table
* @return string		-	New ref.
*/
function make_table_ref($table) {

	$ref = false;
	(DpHooks::checkHook('make_table_ref') ? eval(DpHooks::getHook()) : null);

	if ($ref !== false) {
		return $ref;
	}

	$db =& database_object_factory();

	do {

		$ref = make_ref();

		if ($db->query_amatch($table, "ref = '" . $db->escape($ref) . "'")) {
			unset($ref);
		}

	} while (!$ref);

	return $ref;
}

/**
* Create an array of numbers.
* @access Public
* @param int $start		-	first number
* @param int $end		-	first number
* @param boolean $empty	-	flag whether to generate an empty first value or not
* @return int arrray	-	Array containing the generated number sequence.
*/
function make_numberarray($start, $end, $empty='') {

	if ($empty) {
		$array[0] = '';
	}

	for ($i = $start; $i < ($end + 1); $i++) {
		$array[$i] = $i;
	}

	return $array;

}

###################### FUCTION check_license() ############################

/**
* generates html code for form to validate licence
* @access Public
*/
function check_license() {

?>

	<form method="post" action="http://www.deskpro.com/licensecheck.php">
	<input type="hidden" name="encrypt_license" value="%%%md5license%%%">
	<input type="submit" name="submit" value="Click here to validate license">
	</form>

<?php
	exit();
}

/**
* Generate a random ticketref.
* @access Public
* @return string	-	random ticketref
*/
function make_ref() {

	/* used to make a NNNN-AAAA-NNNN reference */

	$alpha = array('Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M');

	// get first 4 digit number (we don't want to start with 0)
	$bit = substr(dp_rand(1000, 999999999), 0, 4);

	// get 4 alpha key
	$alpha_keys = array_rand($alpha, 4);
	$bit .= '-' . $alpha[$alpha_keys[0]] . $alpha[$alpha_keys[1]] . $alpha[$alpha_keys[2]] . $alpha[$alpha_keys[3]];

	// get another 4 digit random number
	$bit .= '-' . substr(dp_rand(1000000, 99999999), 3, 4);

	return $bit;
}


/**
* checks server OS is windows or not
* @access Public
* @return boolean	-	TRUE if OS is windows other wise false
*/
function is_win() {
	return (strtoupper(substr(PHP_OS, 0, 3)) == "WIN");
}

// #################### Start is browser ##########################

/**
* detects user browser
* @access Public
* @return string	-	name of browser
*/
function dp_get_browser() {

	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

	if (in_string('opera', $useragent)) {
		return 'opera';
	} elseif (in_string('msie', $useragent)) {
		return 'ie';
	} elseif (in_string('safari', $useragent)) {
		return 'safari';
	} elseif (in_string('konqueror', $useragent)) {
		return 'knoqueror';
	} elseif (in_string('gecko', $useragent)) {
		return 'gecko';
	}

}

/**
* prints current date and time details
* @access Public
*/
function datedebug() {

	echo "Years GMT : " . NOWYEAR . " Years LOCAL : " . LOCALYEAR . "<br />";
	echo "Months GMT : " . NOWMONTH . " Years LOCAL : " . LOCALMONTH . "<br />";
	echo "Days GMT : " . NOWDAY . " Years LOCAL : " . LOCALDAY . "<br />";
	echo "Hours GMT : " . NOWHOUR . " Years LOCAL : " . LOCALHOUR . "<br />";
	echo "Minutes GMT : " . NOWMINUTE . " Years LOCAL : " . LOCALMINUTE . "<br />";

}

/**
* the double function approach is so that this function is always run last and we
* thus see all queries
*/
function do_display_queries() {
	register_shutdown_function('display_queries');
}

function display_queries() {

	if (defined('NODISPLAYQUERIES')) {
		return;
	}

	global $datastore;
	$html = "";
	$count = 0;

	if (is_array($datastore['query_log'])) {
		foreach ($datastore['query_log'] AS $query) {

			$html .= "
			<table cellSpacing=0 cellPadding=6 width=\"95%\" align=center bgColor=#ffe8f3 border=1>
			<tr><td style=\"FONT-SIZE: 14px\" bgColor=\"#ffc5cb\" colSpan=\"8\"><B>" . $query['count'] . ": Query (Memory Usage: $query[memory])</td></tr>
			<tr><td colspan=\"100\">" . $query['query_string'] . "</td></tr>
			";

			if (is_array($query['explain_log'])) {

				$html .= "<tr bgColor=#ffc5cb><td>table</td><td>type</td><td>possible_keys</td><td>key</td><td>key_len</td><td>ref</td><td>rows</td><td>Extra</td></tr>";

				foreach ($query['explain_log'] AS $key => $var) {
					$html .= "<tr bgColor=#ffffff>";

					$html .= "<td>";
					if(isset($var['table']))
						$html .= $var['table'];
					$html .= "&nbsp;</td>";

					$html .= "<td bgColor=#d8ffd4>";
					if(isset($var['type']))
						$html .= $var['type'];
					$html .= "&nbsp;</td>";

					$html .= "<td>";
					if(isset($var['possible_keys']))
						$html .= $var['possible_keys'];
					$html .= "&nbsp;</td>";

					$html .= "<td>";
					if(isset($var['key']))
						$html .= $var['key'];
					$html .= "&nbsp;</td>";

					$html .= "<td>";
					if(isset($var['key_len']))
						$html .= $var['key_len'];
					$html .= "&nbsp;</td>";

					$html .= "<td>";
					if(isset($var['ref']))
						$html .= $var['ref'];
					$html .= "&nbsp;</td>";

					$html .= "<td>";
					if(isset($var['rows']))
						$html .= $var['rows'];
					$html .= "&nbsp;</td>";

					$html .= "<td>";
					if(isset($var['Extra']))
						$html .= $var['Extra'];
					$html .= "&nbsp;</td>";

					$html .= "</tr>";
				}
			}

			if ($query['duration'] > 0.1) {
				$html .=  "<tr><td style=\"border: 3px #000000 solid; FONT-SIZE: 14px\" bgColor=\"#ffc5cb\" colSpan=\"8\"><b>MySQL time:</b> $query[duration]</td></tr>";
				$count++;
			} else {
				$html .=  "<tr><td style=\"FONT-SIZE: 14px\" bgColor=\"#ffc5cb\" colSpan=\"8\"><b>MySQL time:</b> $query[duration]</td></tr>";
			}
			$html .=  "</table><br />";

		}

		if ($count) {
			echo "<h3>$count slow queries</h3>";
		}
		echo "<a href=\"javascript:oc('show_queries');\">Show Queries</a><div id=\"show_queries\" style=\"display:none\"><br /><br />$html</div>";
	}
}

function display_dev_footer() {

	if (!defined('DESKPRO_DEBUG_DEVELOPERMODE_FOOTER') OR defined('NODISPLAYQUERIES')) {
		return;
	}

	global $profile, $cache2;

	?>
	<a href="#" onclick="document.getElementById('dev_footer').style.display=''; this.style.display='none'; return false;" style="font-size:150%;">View Developer Footer</a>
	<style type="text/css">
	#dev_footer { text-align:center; }
	#dev_footer_inner {
		text-align:left;
		margin: 0 auto 0 auto;
		width: 700px;
	}

	#dev_footer .part {
		background-color: #C5D1E6;
		padding: 3px;
		margin: 10px 0 10px 0;
	}

	#dev_footer .part h1 {
		margin: 0;
		padding: 2px 0 3px 5px;
		font-size: 12pt;
	}

	#dev_footer .part table {
		border: 1px solid 1F3253;
		background-color: #33363B;
	}

	#dev_footer .part table tr, #dev_footer .part table td, #dev_footer .part table th {
		background-color: #fff;
	}

	#dev_footer .part table thead th {
		background-color: #1F3253;
		color: #fff;
		font-size: 11px;
		text-align: center;
	}
	</style>
	<div id="dev_footer" style="display:none">
	<div id="dev_footer_inner">

		<!-- FROM XDEBUG -->
		<?php if (function_exists('xdebug_memory_usage')): ?>
		<div class="part">
			<h1>xdebug</h1>

			<table cellpadding="3" cellspacing="1" width="100%">
				<thead><tr>
					<th>Total Time</th>
					<th>Memory</th>
					<th>Peak Memory</th>
				</tr></thead>
				<tbody><tr>
					<td align="center"><?php echo xdebug_time_index() ?></td>
					<td align="center"><?php echo xdebug_memory_usage() ?></td>
					<td align="center"><?php echo xdebug_peak_memory_usage() ?></td>
				</tr></tbody>
			</table>
		</div>
		<?php endif ?>

		<!-- TIMES -->
		<div class="part">
			<h1>Times</h1>

			<table cellpadding="3" cellspacing="1" width="100%">
				<thead><tr>
					<th>Total Time</th>
					<th>PHP Time</th>
					<th>MySQL Time</th>
				</tr></thead>
				<tbody><tr>
					<td align="center"><?php echo $profile->total_time; ?></td>
					<td align="center"><?php echo $profile->php_time; ?></td>
					<td align="center"><?php echo $profile->mysql_time; ?></td>
				</tr></tbody>
			</table>

			<?php if ($profile->data): ?>
			<br />
			<table cellpadding="3" cellspacing="1" width="100%">
				<thead><tr>
					<th>Message</th>
					<th>Start</th>
					<th>Time</th>
				</tr></thead>
				<tbody>
					<?php foreach ($profile->data as $item): ?>
						<tr>
							<td><?php echo $item['message']; ?></td>
							<td><?php echo $item['start']; ?></td>
							<td><?php echo $item['time']; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>
		</div>

		<!-- MESSAGES -->
		<?php if ($profile->messages): ?>
		<div class="part">
			<h1>Messages</h1>

			<table cellpadding="3" cellspacing="1" width="100%">
				<thead><tr>
					<th>Title</th>
					<th>Message</th>
				</tr></thead>
				<tbody>
					<?php foreach ($profile->messages as $msg): ?>
						<tr>
							<?php if (is_array($msg)): ?>
								<td><?php echo $msg[0]; ?></td>
								<td><?php echo $msg[1]; ?></td>
							<?php else: ?>
								<td colspan="2"><?php echo $msg; ?></td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
		
		<!-- CACHE LOG -->
		<?php if ($cache2 AND $cache2->cache_logger_arrwriter): ?>
			<div class="part">
				<h1>Cache Log</h1>

				<table cellpadding="3" cellspacing="1" width="100%">
					<tbody>
						<?php foreach ($cache2->cache_logger_arrwriter->getArray() as $msg): ?>
							<tr>
								<td><?php echo htmlspecialchars($msg) ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif ?>

		<!-- INCLUDED FILES -->
		<div class="part">
			<h1>Included Files</h1>

			<?php global $profile; ?>
			<table cellpadding="3" cellspacing="1" width="100%">
				<thead><tr>
					<th width="10">#</th>
					<th>File Path</th>
				</tr></thead>
				<tbody>
					<?php foreach (get_included_files() as $num => $path): ?>
						<tr>
							<td><?php echo $num; ?></td>
							<td><?php echo $path; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>


	</div>
	</div>
	<?php

}


/**
 * Try to find the PHP path
 *
 * @return string|bool The PHP path or false if it could not be found
 */
function find_php_path()
{
	/*************************
	 * See about using system()
	 *************************/
	if (!is_win()) {
		$php_path = false;

		ob_start();
		$php_path = @system('which php');
		ob_end_clean();

		if ($php_path !== false && @file_exists($php_path)) {
			return $php_path;
		}
	}

	/*************************
	 * Check some common paths
	 *************************/
	if (is_win()) {

		$check_paths = array(
			'C:\\php\\bin\\php.exe',
			'C:\\php\\php.exe',
			'C:\\PHP\\php.exe',
			'C:\\php\\cli\\php.exe',
			'C:\\PHP\\cli\\php.exe'
		);

	}

	else {

		$check_paths = array(
			'/usr/bin/php',
			'/usr/bin/php5',
			'/usr/local/bin/php',
			'/usr/local/bin/php5',
			'/usr/local/php/bin/php'
		);

	}

	foreach ($check_paths as $path) {

		// If the file exists and is indeed a file (not a dir)
		// then it seems like we found PHP
		if (@is_file($path)) {
			return $path;
		}
	}

	/*************************
	 * Couldn't find it
	 *************************/
	return false;
}

/**
* get microtime
* @access public
*/
function microtime_float()  {

   list($usec, $sec) = explode(' ', microtime());
   return ((float)$usec + (float)$sec);

}

/**
 * Check to see if a URL is a helpdesk URL
 *
 * @param string $url THe URL to test
 * @param bool $filecheck To also check if the file exists
 * @return bool
 */
function verify_helpdesk_url($url = '', $filecheck = true) {

	global $settings;

	if (!$url) {
		return false;
	}

	$dp_info = @parse_url($settings['helpdesk_url']);
	$url_info = @parse_url($url);

	if (!$dp_info OR !$url_info) {
		return false;
	}

	if ($dp_info['scheme'] != $url_info['scheme']) {
		return false;
	}

	if ($dp_info['host'] != $url_info['host']) {
		return false;
	}

	if (preg_match('#("|\'|>|<)#', $url)) {
		return false;
	}

	if ($filecheck) {
		$test_file = preg_replace('#^' . preg_quote($dp_info['path'], '#') . '#', '', $url_info['path']);
		$test_file = ROOT . '/' . $test_file;

		if (is_file($test_file)) {

			// Make sure its within the deskpro dir (not getting out by using ../)
			$test_file = @realpath($test_file);

			if ($test_file AND !preg_match('#^' . preg_quote(ROOT, '#') . '#', $test_file)) {
				return false;
			}
		} else {
			return false;
		}
	}

	return true;
}



/**
 * Simply pass relative URLs, but still verify full URLs
 *
 * @param string $url
 * @return bool
 */
function verify_helpdesk_url_simple($url) {

	if (!$url) {
		return false;
	}

	$url_info = @parse_url($url);

	// Relative paths are all okay
	if (!$url_info OR (!$url_info['host'] AND !$url_info['scheme'])) {

		if (!preg_match('#("|\'|>|<)#', $url)) {
			return true;
		} else {
			return false;
		}

	// Otherwise we need to verify the URL
	} else {
		return verify_helpdesk_url($url, false);
	}
}





/**
 * See if chat is available (any active techs)
 *
 * @return boolean
 */
function chat_available() {

	static $is_available = null;

	if (is_null($is_available)) {
		global $db;
		$is_available = $db->query_match('tech', 'chat_away = 0 AND chat_timestamp_ping > ' . (TIMENOW - 20));
	}

	return $is_available;
}




/**
 * Fully validate a user. This makes them valid and makes all their tickets
 * unhidden.
 *
 * @param integer|array $user A user ID or an array of user details
 * @return integer|boolean The number of tickets unhidden or false on error
 */
function fully_validate_user($user) {

	global $db;

	if (is_array($user)) {
		$user_details = $user;
	} else {
		$user_details = user_from_field('id', $user);
	}

	$userid = $user_details['id'];

	if (!$user_details OR !($user_details['awaiting_register_validate_user'] OR $user_details['awaiting_register_validate_tech'])) {
		return false;
	}

	$db->query("
		UPDATE user
		SET
			awaiting_register_validate_user = 0,
			awaiting_register_validate_tech = 0
		WHERE id = $userid
	");

	return process_tickets_uservalidated($user_details);
}


/**
 * Get details about an application key.
 *
 * @param string $appkey
 * @return array
 */
function get_appkey($appkey) {

	if (strlen($appkey) != 50) {
		return false;
	}

	global $db;

	$row = $db->query_return("
		SELECT * FROM service_appkey
		WHERE appkey = '" . $db->escape($appkey) . "'
	");

	if (!$row) {
		return false;
	}

	$info = array(
		'appkey' => $row['appkey'],
		'comment' => $row['comment'],
		'access' => array()
	);

	$access_lines = explode("\n", $row['access']);
	foreach ($access_lines as $line) {
		list($group, $level) = explode('=', $line);
		$info['access'][$group] = $level;
	}

	return $info;
}





/**
 * Short, easy way of checking one or more strings against
 * the current requests 'do'
 *
 * Returns true if any of the passed strings matches the do
 *
 * @param string $check The string to test for
 * @param string ...
 * @return boolean
 */
function check_do() {

	global $request;
	$args = func_get_args();
	return in_array($request->getString('do', 'request'), $args);
}





/**
 * Send a POST web request to a URL with given data.
 *
 * @param string $url The URL to reuqest
 * @param array $data Array of key/value pairs to send along
 * @return string Result from server
 */
function send_web_request($url, $data = array()) {

	$postdata = array();

	foreach ($data as $k => $v) {
		$postdata[] = $k . '=' . urlencode($v);
	}

	$postdata = implode('&', $postdata);

	// Use CURL
	if (function_exists('curl_init')) {

		$ch = @curl_init();
		@curl_setopt($ch, CURLOPT_URL, $url);
		@curl_setopt($ch, CURLOPT_POST, true);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

		$res = @curl_exec($ch);
		@curl_close($ch);

	// A home-baked POST request then
	} else {

		$urlinfo = @parse_url($url);

		if (!$urlinfo) {
			return '';
		}

		if (!$urlinfo['port']) {
			$urlinfo['port'] = 80;
		}

	    $fp = @fsockopen($urlinfo['host'], $urlinfo['port']);

	    if (!$fp) {
	    	return '';
	    }

	    $headers = array(
	    	"POST {$urlinfo['path']} HTTP/1.0",
	    	"Host: {$urlinfo['host']}",
	    	"Content-type: application/x-www-form-urlencoded",
	    	"Content-length: " . strlen($postdata),
	    	"Connection: close",
	    );

	    @fwrite($fp, implode("\r\n", $headers) . "\r\n\r\n{$postdata}");

        $res = '';
	    while (!@feof($fp)) {
	        $res .= @fgets($fp, 1024);
	    }

	    $res = standard_eol($res);

	    // Get rid of the header reply
	    $body_pos = strpos($res, "\n\n");
	    $res = substr($res, $body_pos + 2);

	    @fclose($fp);
	}

	return $res;
}


/**
 * This checks the request for certain params that match a keyword.
 * If the keyword exists, then the associated 'do' action is set.
 *
 * This allows you cleaner URLs: page.php?find=something
 * Instead of: page.php?do=find&amp;find=something
 *
 * If 'do' is already set in the request, this function will not overwrite it
 * (i.e., it wont do anything).
 *
 * @param array $keywords Array of keyword=>do
 */
function request_keyword_to_do(array $keywords) {

	global $request;

	if ($request->getString('do')) {
		return;
	}

	foreach ($keywords as $word => $do) {
		if ($request->getIsset($word)) {
			$request->setVariable('do', $do);
			return;
		}
	}
}





/**
 * Tests a UTF8 string to see if it is valid. If it isn't, it'll strip out
 * the offending bytes to make it safe.
 *
 * @param string $string
 * @return string
 */
function utf8_get_safe($string)
{
	if (!is_string($string) OR utf8_compliant($string)) {
		return $string;
	}

	return utf8_bad_strip($string);
}