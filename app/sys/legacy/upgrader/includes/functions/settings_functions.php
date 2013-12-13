<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
* Utility functions for settings handling (administration interface)
*
* @package DeskPRO
*/

/**
* update settings quickly with no verification
*
* @access	Public
*/
function update_settings() {

	demo_check();

	global $request, $db;

	// update the settings
	if (!is_array($request->getArray('settings', 'request'))) {
		return;
	}

	foreach ($request->getArray('settings', 'request') AS $key => $var) {

		$db->query("
			UPDATE settings SET
				value = '" . $db->escape($var) . "'
			WHERE name = '" . $db->escape($key) . "'
		");
	}

	return get_settings();

}

/**
 * Update settings and also run verification code on values
 *
 * @param array $errors A reference to an array where any errors will be placed
 * @return array New settings
 */
function update_settings_verify(&$errors) {

	demo_check();

	global $request, $db, $settings;

	if (!is_array($request->getArray('settings', 'request'))) {
		return;
	}

	$settings_verify = $db->query_return_array_id("
		SELECT name, value, php_verify FROM settings
	", '', 'name');

	foreach ($request->getArray('settings', 'request') AS $name => $value) {

		$set = $settings_verify[$name];

		// Invalid or same value
		if (!$set OR $value == $settings_verify[$name]['value']) {
			continue;
		}

		// If there is PHP for verification, eval it
		$error = false;
		if ($set['php_verify']) {
			eval($set['php_verify']);
		}

		if ($error) {
			$errors[$name] = $error;
		} else {

			$db->query("
				UPDATE settings SET
					value = '" . $db->escape($value) . "'
				WHERE name = '" . $db->escape($name) . "'
			");

			$settings[$name] = $value;
		}
	}
}

/**
* shows basic settings
*
* @access	Public
*
* @param	string	title of settings block
* @param	string|array	array of setting names
* @param	string|array	extra data to display
* @param    array           Additonal information about how the settings should display (ie setting => password for password field)
*/
function show_settings_wizard($title, $settings, $extra='', $settinginfo = array()) {

	global $db;

	$db->query("
		SELECT *
		FROM settings
		WHERE name IN " . array2sql($settings) . "
		ORDER BY displayorder
	");
	$content = new content('', $title);

	// organise the settings into categories
	while ($set = $db->row_array()) {
		$name = $set['name'];
		$set['extrainfo'] = $settinginfo[$name];
		$content->newRow(settings_create_row($set['display_name'], $set['name'], settings_create_html($set), $set['description'], $set['default_value'], 1, $set));
	}

	if (is_array($extra)) {
		foreach ($extra AS $key => $var) {
			$content->newRow(settings_extra_row($var[0], $var[1], $var[2]));
		}
	}
	$content->build();
}

/**
* shows settings under a selected category
*
* @access	Public
*
* @param	string	name of category
*/
function show_settings_category($ref) {

	global $db;

	// get category data
	$category = $db->query_return("
		SELECT * FROM settings_cat
		WHERE name = '" . $db->escape($ref) . "'
	");

	// get settings data
	$db->query("
		SELECT settings.*
		FROM settings
		WHERE category = '" . $db->escape($category['name']) . "'
		ORDER BY category, displayorder
	");

	$content = new content('');

	while ($result = $db->row_array()) {
		$result['html'] = settings_create_html($result);
		$content->newRow(settings_create_row($result['display_name'], $result['name'], $result['html'], $result['description'], $result['default_value']));
	}

	$content->setTitle($category['display_name'] . ' : ' . $category['description']);
	$content->build();

}

/**
* shows settings grouped by category
*
* @access	Public
*
* @param	string|array	array of name of categories
* @param	string
*/
function show_settings_groups($cats, $ref) {

	global $db, $cache2;

	if (!$cache2->usersourceTypeEnabled('dp')) {
		$exclude_cat = array('users');
		$exclude_opt = array(
			'gateway_require_validation', 'register_gateway_welcome',
		);
	}

	if (defined('MANAGED')) {

		$exclude_cat[] = 'smtp';
		$exclude_cat[] = 'gateway';
		$exclude_cat[] = 'templates';

		$exclude_opt[] = 'cron_auto_pop';
		$exclude_opt[] = 'cron_auto_cron';
		$exclude_opt[] = 'graphing';
		$exclude_opt[] = 'safe_mode_upload';
		$exclude_opt[] = 'safe_mode_dir';
		$exclude_opt[] = 'cookie_path';
		$exclude_opt[] = 'use_gzip';
		$exclude_opt[] = 'email_linereturns';

		$exclude_opt[] = 'template_allow_include';

	} // END_MANAGED

	if (is_array($exclude_cat)) {
		$exclude_cat = "AND settings_cat.name NOT IN " . array2sql($exclude_cat);
		$exclude_opt = "AND settings.name NOT IN " . array2sql($exclude_opt);
	}

	$db->query("
		SELECT *
		FROM settings_cat
		WHERE name IN " . array2sql($cats) . " $exclude_cat
		ORDER BY displayorder
	");

	while ($setcat = $db->row_array()) {
		$settingcats[$setcat['name']] = $setcat;
		$thecategorys[] = $setcat['name'];
	}

	$start = "";
	foreach ($settingcats AS $key => $var) {
		if (!$start) {
			$start = $var['name'];
		}
		$sections[] = array($var['display_name'], $var['name']);
	}

	echo section_nav($sections, $ref);

	// get settings data
	$db->query("
		SELECT settings.*
		FROM settings
		LEFT JOIN settings_cat ON (settings.category = settings_cat.name)
		WHERE category IN " . array2sql($thecategorys) . " $exclude_opt
		ORDER BY settings.category, settings.displayorder
	");

	// organise the settings into categories
	while ($set = $db->row_array()) {

		$set['html'] = settings_create_html($set);
		$thesettings[$set['category']][] = $set;

	}

	foreach ($thesettings AS $key => $var) {

		if (is_array($var)) {
			foreach ($var AS $key2 => $var2) {
				$table[] = settings_create_row($var2['display_name'], $var2['name'], $var2['html'], $var2['description'], $var2['default_value'], FALSE, $var2);
			}
		}

		generate_div($settingcats[$key]['name']);

		$content = new Content('', $settingcats[$key]['display_name'] . ' : ' . $settingcats[$key]['description'], FALSE, FALSE);
		$content->addRows($table);
		$content->build();

		unset($table);



		end_div();

	}

	section_nav_end($start, $ref);
}

/**
* generates html for provided control type
*
* @access	Public
*
* @public	string|array	details about control that is type, name and value
*
* @return	 string	html
*/
function settings_create_html($set) {

	if ($set['field_type'] == "text") {
		if ($set['extrainfo']['password']) {
			$html = form_password($set['name'], $set['value'], '30', 'settings');
		} else {
			$html = form_input($set['name'], $set['value'], '30', 'settings');
		}
	} elseif ($set['field_type'] == "area") {
		$html =  form_textarea($set['name'], '30', '5', $set['value'], 'settings');
	} elseif ($set['field_type'] == "radio") {
		$html =  form_radio_yn($set['name'], 'settings', $set['value']);
	} elseif ($set['field_type'] == "select") {

		if ($set['name'] == 'timezone') {

			$html = construct_timezone_select($set['value'], 'settings');

		} else {

			if ($set['php_generate']) {
				eval($set['php_generate']);
			} else {
				$options = unserialize($set['options']);
			}
			$html = form_select($set['name'], $options, 'settings', $set['value']);

		}
	} elseif ($set['field_type'] == 'custom') {
		eval($set['php_generate']);
	}

	return $html;

}

/*
	- function takes a $content variable and adds a setting row to it
	- does not add the Revert to Default button
*/
function settings_add_row($name, &$content) {

	global $db;

	$set = $db->query_return("
		SELECT *
		FROM settings
		WHERE name = '$name'
	");

	$content->newRow(settings_create_row($set['display_name'], $set['name'], settings_create_html($set), $set['description'], $set['default_value'], true, $set));

}

/**
*
*/
function settings_create_row($name, $setting, $html, $description, $default, $nodefault='', $set) {

	$var = "<table width=\"100%\"><tr><td><b>$name</b>" .iff(defined('DESKPRO_DEBUG_DEVELOPERMODE'), " ($setting)") . "</td><td align=\"right\"><p align=\"right\"></td></tr><tr><td>" . $description . "</td></tr></table>";

	$array = array($var, $html);

	if (!$nodefault) {
		if ($set['field_type'] == 'radio') {
			if ($default) {
				$array[] = "<input type=\"button\" onclick=\"this.form['settings[$setting]'][0].checked=true;\" value=\"Revert to Default\">";
			} else {
				$array[] = "<input type=\"button\" onclick=\"this.form['settings[$setting]'][1].checked=true;\" value=\"Revert to Default\">";
			}
		} else if ($set['field_type'] == 'text') {
			$array[] = "<input type=\"button\" onclick=\"this.form['settings[$setting]'].value='" . addslashes_js(htmlspecialchars($default)) . "';\" value=\"Revert to Default\">";
		} else {
			$array[] = "&nbsp;";
		}
	}

	return $array;

}

/**
*
*/
function settings_extra_row($name, $description, $form) {

	return array("<table width=\"100%\"><tr><td><b>$name</b></td><td align=\"right\"><p align=\"right\"></td></tr><tr><td>" . $description . "</td></tr></table>", $form);

}

?>