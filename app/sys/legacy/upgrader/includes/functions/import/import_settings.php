<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

require_once(INC . 'classes/class_XMLDecode.php');

// +-------------------------------------------------------------+
// | $Id: import_settings.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - import settings
// +-------------------------------------------------------------+

/*
	- import settings
	- used for both install & upgrade

	OVERWRITE TOP LEVEL CATEGORIES

	LOGIC:
		- if setting does not exist, create it
		- if setting exists, update everything but value
		- if setting exists, but not in XML delete it, except if custom = 1

*/
function import_settings($dolicense = FALSE) {

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
	    
	    $category['display_name']['value'] = str_replace('{$DP_NAME}', DP_NAME, $category['display_name']['value']);
	    $category['description']['value'] = str_replace('{$DP_NAME}', DP_NAME, $category['description']['value']);
	    

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

			if ($setting['name'] == 'license' AND $dolicense AND $setting['value']['value'] != 'LICENSE_CODE') {
				unset($settings_data['license']);
			}
			
			$setting['display_name']['value'] = str_replace('{$DP_NAME}', DP_NAME, $setting['display_name']['value']);
			$setting['description']['value'] = str_replace('{$DP_NAME}', DP_NAME, $setting['description']['value']);

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
					category = '" . $db->escape($category['name']) . "',
					php_verify = '" . $db->escape($setting['php_verify']['value']) . "',
					php_generate = '" . $db->escape($setting['php_generate']['value']) . "'
			");
		}
	}

	// Reset settings array after import
	global $settings;

	$settings = get_settings();

}

?>