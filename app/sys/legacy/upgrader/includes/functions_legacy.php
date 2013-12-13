<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: functions.php 4315 2007-08-09 21:10:18Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - functions that have new versions we can not use (e.g. because of a db change)
// +-------------------------------------------------------------+

/**
* Populates the global $settings array
* @access Public
* @return mixed array	-	array of settings
*/
function legacy_get_settings() {

	global $db;

	$settings = $db->query_return_array_id('SELECT name, value FROM settings', 'value', 'name');

	return $settings;

}

/**
* updates a single setting
*
* @access	Public
*
* @param	string	name of setting
* @param	mixed	value of setting
*
* @return	 boolean	true if updated else false
*/
function legacy_update_setting($name, $value) {

	global $db, $settings;

	// we don't have this setting, create it (e.g. hidden setting)
	if (is_array($settings) AND !array_key_exists($name, $settings)) {

		$db->query("
			INSERT INTO settings SET
				value = '" . $db->escape($value) . "',
				name = '" . $db->escape($name) . "'
		");

	// we have the setting, just update
	} else {

		$db->query("
			UPDATE settings SET
				value = '" . $db->escape($value) . "'
			WHERE
				name = '" . $db->escape($name) . "'
		");

	}

	$settings[$name] = $value;

}

function legacy_import_settings() {

	global $db;

	/******************
	* Read top level categories
	******************/

	$xml = getXML('install/data/settings_topcats.xml');

	/******************
	* Form the top level category array
	******************/

	$array = array();
	foreach ($xml['category'] AS $result) {
		$array[$result['key']] = $result['value'];
	}

	/******************
	* Write the top level category array
	******************/

	update_data('settings_master_cats', $array);
	unset($array);

	/******************
	* Get current settings value
	******************/

	$settings_data = $db->query_return_array_id("SELECT name, value FROM settings WHERE !custom", 'value', 'name');
	$settings_data_default_value = $db->query_return_array_id("SELECT name, default_value FROM settings WHERE !custom", 'default_value', 'name');

	/******************
	* Delete non custom settings & settings cat
	******************/

	$db->query('DELETE FROM settings');
	$db->query('DELETE FROM settings_cat');

	/******************
	* Get settings
	******************/

	$xml_parser = new class_XMLDecode();
	$xml = $xml_parser->parse_file('install/data/settings.xml');

	foreach ($xml['settingscat'] AS $category) {

		// insert the category
		$db->query("
		INSERT INTO settings_cat SET
			parent = '" . $db->escape($category['parent']) . "',
			name = '" . $db->escape($category['name']) . "',
			displayorder = '" . $db->escape($category['displayorder']) . "',
			description = '" . $db->escape($category['description']['value']) . "',
			display_name = '" . $db->escape($category['display_name']['value']) . "'
		");

		// skip if no settings for this category
		if (!is_array($category['setting'])) {
			continue;
		}

		// force array
		if (isset($category['setting']['name'])) {
			$category['setting'] = array($category['setting']);
		}

		foreach ($category['setting'] AS $setting) {

			/*****************
			* Use old value if it existed
			*****************/
			if (isset($settings_data[$setting['name']])) {
				$value = $settings_data[$setting['name']];
			} else {
				$value = $setting['value']['value'];
			}

			$db->query("
				INSERT INTO settings SET
					value = '" . $db->escape($value) . "',
					default_value  = '" . $db->escape($setting['value']['value']) . "',
					display_name = '" . $db->escape($setting['display_name']['value']) . "',
					description = '" . $db->escape($setting['description']['value']) . "',
					options  = '" . $db->escape($setting['options']['value']) . "',
					field_type  = '" . $db->escape($setting['field_type']) . "',
					displayorder  = '" . $db->escape($setting['displayorder']) . "',
					name  = '" . $db->escape($setting['name']) . "',
					category = '" . $db->escape($category['name']) . "'
			");
		}
	}
}


function legacy_merge_user($masterid, $mergeid, $quick = false) {

	global $db;

	$masterid = intval($masterid);
	$mergeid = intval($mergeid);

	/********************************
	* Need to handle unique index situations
	********************************/

	$emails = $db->query_return_array("
		SELECT email FROM user_email WHERE (userid = $masterid OR userid = $mergeid) AND validated = 1
	");

	$db->query("
		DELETE FROM user_email
		WHERE userid = $masterid
		OR userid = $mergeid
	");

	$done_emails = array();

	if (is_array($emails)) {
		foreach ($emails AS $email) {

			if (in_array($email['email'], $done_emails)) {
				continue;
			}
			$done_emails[] = $email['email'];

			$db->query("
				INSERT INTO user_email SET
					email = '" . $db->escape($email['email']) . "',
					userid = $masterid,
					validated = 1
			");
		}
	}

	$db->query("UPDATE ticket_message SET userid = $masterid WHERE userid = $mergeid");
	$db->query("UPDATE ticket SET userid = $masterid WHERE userid = $mergeid");

	// update the ids
	if (!$quick) {

		$db->query("UPDATE ticket_log SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE user_bill SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE user_notes SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_articles SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_comments SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_rating SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_subscriptions SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_searchlog SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE email_send SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE email_send_log SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE faq_searchlog_solved SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE ticket_attachments SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE trouble_comments SET userid = $masterid WHERE userid = $mergeid");
		$db->query("UPDATE trouble_rating SET userid = $masterid WHERE userid = $mergeid");


		// some deletes necessary
		$db->query("DELETE FROM user_session WHERE userid = $mergeid");
		$db->query("DELETE FROM tech_start_tickets WHERE userid = $mergeid");

	}

	// delete the old user
	$db->query("DELETE FROM user WHERE id = $mergeid");

}



/*
	old format:
		0 -> key
		1 -> order
		2 -> name
		3 -> selected or not

	new format:
		0 -> key
		1 -> nothing
		2 -> name

	order is by order in array but is not stored.

*/

function fix_custom_field_data($table, $def) {

	global $db;

	/***************************
	* 1) Updates the custom field data to the new format
	* 2) Sets all the keys to 1+ and updates these entries.
	***************************/

	// custom user fields
	$fields = $db->query_return_array("
		SELECT * FROM $def WHERE formtype IN ('select', 'radio', 'checkbox')
	");

	if (!is_array($fields)) {
		return;
	}

	foreach ($fields AS $key => $var) {

		$newid = 0;
		$crazyid = 999;
		unset($data2);
		unset($array);

		$data = unserialize($var['data']);

		// we need to get in order for the db replacement later
		foreach ($data AS $var2) {
			$data2[$var2[0]] = $var2;
		}

		// do this so we get 11 before 1 updating.
		$data = $data2;

		$query[] = "$var[name] = REPLACE($var[name], '|||[None]|||', '')";

		foreach ($data AS $row) {

			// $row[1] is the id
			$oldid = $row[0];

			$newid++;
			$crazyid++;

			$array[] = array(
				$newid,
				0,
				$row[2]
			);

			if ($oldid != $newid) {

				$row[2] = $db->escape($row[2]);
				$oldid = $db->escape($oldid);
				$crazyid = $db->escape($crazyid);

				$query_alpha1[] = "$var[name] = REPLACE ($var[name], '|||$row[2]|||', '|||$crazyid|||')";
				$query_alpha2[] = "$var[name] = REPLACE ($var[name], '$row[2]', '|||$crazyid|||')";
				$query_number[] = "$var[name] = REPLACE ($var[name], '|||$oldid|||', '|||$crazyid|||')";
				$query_clean[] = "$var[name] = REPLACE ($var[name], '|||$crazyid|||', '|||$newid|||')";
			}
		}

		$db->query("
			UPDATE $def SET data = '" . $db->escape(serialize($array)) . "' WHERE id = $var[id]
		");

		// lovely regex that enforces |||X|||(|||X|||) on these custom fields. no more bad data
		$cleanups[] = "UPDATE $table SET $var[name] = null WHERE $var[name] NOT REGEXP '" . '\\\|\\\|\\\|[0-9]+\\\|\\\|\\\|([0-9]+\\\|\\\|\\\|)*' . "'";

	}

	$query = array_merge_values($query, $query_alpha1, $query_alpha2, $query_number, $query_clean);

	$db->query("
		UPDATE $table SET
		" . implode(',', $query) . "
	");

	foreach ($cleanups AS $query) {
		$db->query($query);
	}

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
function legacy_make_username($email, $name='') {

	global $db;

	$name = preg_replace('#[^a-z0-9_]#i', '', $name);

	$i = 0;

	if (strlen($name) > 4) {

		$username = trim($name);

	} else {

		// Grab everything leaving up to the @ and strip out everything that's not alphanumeric.
		$username = substr($email, 0, strpos($email, '@'));
		$username = preg_replace('([^_a-zA-Z0-9\-\.])', '', $username);

	}

	// If its under the required length, make a new one
	if (strlen($username) < 5) {
		$username = make_pass();
	}

	// Check to see if we need to add a number to it
	if ($db->query_match('user', "username='" . $db->escape($username) . "'")) {

		// Username without trailing numbers
		$username = preg_replace('#^(.*?)([0-9]*)$#', '$1', $username);

		// If it's too short without numbers, we'll make a random one
		if (strlen($username) < 5) {
			return legacy_make_username('', make_pass());
		}

		$db->query("
			SELECT username
			FROM user
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
	}

	return $username;

}

?>