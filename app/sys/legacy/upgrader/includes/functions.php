<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: functions.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - basic installation functions
// +-------------------------------------------------------------+

function serverinfo() {

	global $db;

	$mysql = $db->query_return("SELECT VERSION() AS version");
	$apache_modules = apache_get_modules();

	$data = array(
		'os' => php_uname('s'),
		'architecture' => php_uname('m'),
		'web_server' => $_SERVER['SERVER_SOFTWARE'],
		'apache_modules' => $apache_modules,
		'sapi_name' => php_sapi_name(),
		'php' => phpversion(),
		'php_extensions' => get_loaded_extensions(),
		'php_safe_mode' => (get_cfg_var('safe_mode') ? 'On' : 'Off'),
		'php_open_basedir' => ((($bd = get_cfg_var('open_basedir')) AND $bd != '/') ? 'On' : 'Off'),
		'mysql' => $mysql['version']
	);

	return $data;

}


function upgrade_sub_header($pages) {

	global $request;

	$step = $request->getString('step', 'request');

	for ($i = 0; $i < count($pages); $i++) {
		if ($step == $i) {
			$html .= "<tr><td style=\"border-top:1px solid #CCCCCC; border-bottom:1px solid #CCCCCC\">" . html_image("icons_large/" . $pages[$i][1]) . "<td style=\"border-top:1px solid #CCCCCC; border-bottom:1px solid #CCCCCC\"><strong>" . $pages[$i][0] . "</strong></td><td style=\"border-top:1px solid #CCCCCC; border-bottom:1px solid #CCCCCC\">&nbsp;</td></tr>";
		} elseif ($step >= $i) {
			$html .= "<tr><td>" . html_image("icons_large/" . $pages[$i][1]) . "<td>" . $pages[$i][0] . "</td><td>" . html_image("icons_large/apply.gif") . "</td></tr>";
		} else {
			$html .= "<tr><td>" . html_image("icons_large/" . $pages[$i][1], '', "style=\"filter: alpha(opacity=30); moz-opacity: 0.3; khtml-opacity: 0.3; opacity: 0.3;\"") . "<td>" . $pages[$i][0] . "</td><td>" . html_image("icons_large/del.gif") . "</td></tr>";
		}
	}

	echo "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" align=\"center\"><tr><td width=\"265\" valign=\"top\" style=\"padding-right:25px\">";

	echo "<table style=\"border:1px #CCCCCC solid; border-collapse: collapse;\" cellspacing=\"0\" cellpadding=\"4\" width=\"265\">$html</table>";

	if (is_array($new)) {
		echo "<br /><br />";
		foreach ($new AS $var) {
			echo "Upgrade to $var <br />";
		}
	}

	echo "</td><td valign=\"top\" width=\"100%\">";

}

function set_deskpro_version($string, $internal) {

	global $db;

	// Convert legacy builds to new build numbers
	require_once(INC . 'classes/class_DpBuilds.php');
	$dpbuilds = new DpBuilds();

	if (!$dpbuilds->isBuild($internal)) {
		$internal = $dpbuilds->convertLegacyBuild($internal);
	}

	$db->query("UPDATE settings SET value = '" . $db->escape($string) . "' WHERE name = 'deskpro_version'");
	$db->query("UPDATE settings SET value = " . intval($internal) . " WHERE name = 'deskpro_version_internal'");

}

/**
* Checks that we are in the install system
*
* @access	Public
*
* @return	 string
*/
function install_check() {
	if (!defined('INSTALLER')) {
		die("Security Alert : Script not called by installation system. Please contact support@deskpro.com.");
	}
}


/**
* Message to display when we are about to do something
*
* @access	Public
*
* @return	 string
*/
function start($message) {
	echo "$message<br /><br />";
}

/**
* Action completed
*
* @access	Public
*
* @return	 string
*/
function yes() {
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"install_green\"><strong>.......... Completed</strong></span><br /><br />";
}

/**
* Log an install step
*
* @access	Public
*
* @return	 string
*/
function install_log($type, $step, $extrastep='') {

	$install_log = array(
		'action' => $type,
		'nextstep' => $step,
		'extrastep' => $extrastep
	);

	update_data('install', $install_log);

}

/**
*	Force Refs
*
*	Create refs for a table that does not have one
* @access	Public
*
* @return	 string
*/
function force_refs($table = 'ticket') {

	$db =& database_object_factory();

	$db->query("SELECT id FROM $table WHERE !ref");
	while ($res = $db->row_array()) {
		$process[] = $res['id'];
	}

	foreach($process AS $id) {
		$db->query("UPDATE $table SET ref = '" . $db->escape(make_table_ref($table)) . "' WHERE id = " . intval($id));
	}
}

/**
 * Replace ENGINE= with TYPE= for MySQL 3, and <4.0.18
 * and <4.1.2.
 *
 * @param string $query The query to do the replacement in
 * @return string The same query with ENGINE replaced with TYPE if required
 */
function query_engine_replace($query) {

	static $do_replace = null;

	if (is_null($do_replace)) {

		global $db;

		$version = $db->query_return("SELECT VERSION() AS v");
		$version = trim($version['v']);
		$version = preg_replace('#[^\.0-9]#', '', $version);

		// Anything older then 4.0.18 (including 3.x)
		if (version_compare($version, '4.0.18', '<')) {
			$do_replace = true;

		// And anything in the 4.1 branch before 4.1.2
		} elseif (version_compare($version, '4.1.0', '>=') AND version_compare($version, '4.1.2', '<'))  {
			$do_replace = true;

		// Otherwise, we're good to go
		} else {
			$do_replace = false;
		}
	}

	if (!$do_replace) {
		return $query;
	} else {
		return str_replace('ENGINE=', 'TYPE=', $query);
	}
}





/**
 * Get the indexes that are defined on a table
 *
 * @param string $table The table name to get indexes for
 * @param bool $force True to get the indexes again via a query, false to use the cached
 *                    information from a previous call.
 * @param bool $include_primary Include the primary key in the list (if there is one)?
 *
 * @return array The array of index names
 */
function get_table_indexes($table, $force = false, $include_primary = true) {
	static $cache = array();

	if (!isset($cache[$table]) OR $force) {
		global $db;
		$cache[$table] = (array)$db->query_return_array_id("SHOW INDEX FROM $table", 'Key_name', '');
		$cache[$table] = array_unique($cache[$table]);
	}

	$arr = $cache[$table];

	if (!$include_primary AND ($key = array_search('PRIMARY', $arr)) !== false) {
		unset($arr[$key]);
	}

	return $arr;
}





/**
 * Check if an index exists on a table.
 *
 * @param string $table The table to check the index
 * @param string $name The name of the index to check
 *
 * @return bool True if the index exists, false otherwise
 */
function check_table_index($table, $name) {

	$indexes = get_table_indexes($table);

	if (!is_array($indexes) OR !in_array($name, $indexes)) {
		return false;
	}

	return true;
}


/**
 * Clear the table indexes. Note that the PK won't be dropped.
 *
 * @param string $table The table to clear indexes on.
 * @return integer The number of indexes that were dropped
 */
function clear_table_indexes($table) {

	global $db;

	$indexes = get_table_indexes($table, true, false);

	if (!$indexes) {
		return 0;
	}

	$sql = "ALTER TABLE `$table` DROP INDEX " . implode(", DROP INDEX ", $indexes);
	$db->query($sql);

	get_table_indexes($table, true);

	return count($indexes);
}

/**
 * Add indexes to a table all in one go, making sure
 * there aren't duplicates.
 *
 * The indexes array should contain an array of arrays:
 * <code>array(
 * 		array(indexname, indextype, array(columns)),
 * 		array(userid, index, array(userid))
 * )</code>
 *
 * @param string $table The table to add indexes to
 * @param array $add_indexes The indexes to add
  @return integer The number of indexes that were added
 */
function add_table_indexes($table, $add_indexes) {

	global $db;

	$indexes = get_table_indexes($table, true, false);

	$numadded = 0;
	$sql = "ALTER TABLE `$table` ";

	foreach ($add_indexes as $indexdata) {

		list($indexname, $type, $columns) = $indexdata;

		if (in_array($indexname, $indexes)) {
			continue;
		}

		$type = strtoupper($type);
		$columns = implode("`,`", $columns);

		$sql .= "ADD $type `$indexname` (`$columns`), ";

		$numadded++;
	}

	if (!$numadded) {
		return 0;
	}

	// trailing coma
	$sql = trim($sql);
	$sql = substr($sql, 0, -1);

	$db->query($sql);

	// Recache
	get_table_indexes($table, true);

	return $numadded;
}

/**
 * Create a quick admin session to validate user who wants to upgrade
 */
function check_admin_login() {

	global $db, $request, $cache2;

	if ($request->getString('username') AND $request->getString('password')) {

		$bad = true;

		$tech = $db->query_return("
			SELECT *
			FROM tech
			WHERE username = '" . $db->escape($request->getString('username')) . "'
		");

		if ($tech AND check_tech_password($tech, $request->getString('password'))) {
			$bad = false;
			
	    // This might be an upgrade, try the old unhashed passwords from <3.3
		} elseif ($tech AND $tech['password'] == $request->getString('password')) {
		    $bad = false;
		}
		
		if (!$bad) {
		    dp_setcookie('dp_install_code', substr(md5($tech['password_cookie'] . $tech['username']), 0, 8));
			dp_setcookie('dp_install_id', $tech['id']);
			$loggedin = true;
		}

	} else if ($_COOKIE['dp_install_code'] AND $_COOKIE['dp_install_id']) {

		$tech = $db->query_return("SELECT * FROM tech WHERE id = " . intval($_COOKIE['dp_install_id']) . "");

		if ($_COOKIE['dp_install_code'] = substr(md5($tech['password_cookie'] . $tech['username']), 0, 8)) {
			$loggedin = true;
		}

	}

	if (!$loggedin) {

		global $header;

		$header->build();

		if ($bad) {
			echo "<center>Username/Password combination is invalid</center><br />";
		}

		$content = new content('admin_login', 'Admin login is required to perform an upgrade');
		$content->buildInput('Username', 'username');
		$content->buildPassword('Password', 'password');

		$hidden_fields = array();
		foreach ($request->getAll() as $k => $v) {
			$hidden_fields[$k] = $v;
		}

		$hidden_fields['dologin'] = 1;

		$content->setForm('index.php', $request->getSafeString('do'), 'Login', $hidden_fields);

		$content->build();

		$header->footer();

		exit;
	}
}

?>