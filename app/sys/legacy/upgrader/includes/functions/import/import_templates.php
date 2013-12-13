<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_templates.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - import templates
// +-------------------------------------------------------------+

/*
	function sort_templates

	- This function sets the templates to the current version

	- 1. If template does not exist, create it
	- 2. If template exists and has not been modified, updated it
	- 3. If template exists, and has been modified, upgrade the backup and mark the change.

*/
require_once(INC . 'functions/conditional_functions.php');
require_once(INC . 'classes/class_XMLDecode.php');

function import_templates_web_xml() {

	global $db;

	/*****************
	* Delete the Categories
	*****************/

	$db->query("
		DELETE FROM template_cat
	");

	/*****************
	* Read Categories
	*****************/

	$xml = getXML('install/data/categories.xml');

	/*****************
	* Loop and insert the categories
	*****************/

	foreach ($xml['templatecat'] AS $category) {

		// create the category
		$db->query("
			INSERT INTO template_cat SET
				name = '" . $db->escape($category['name']['value']) . "',
				description = '" . $db->escape($category['description']['value']) . "',
				displayorder = '" . $db->escape($category['displayorder']) . "',
				intname = '" . $db->escape($category['intname']) . "'
		");
	}

	/*****************
	* Get Template Set
	*****************/

	$all_templatesets = $db->query_return_array("
		SELECT *
		FROM template_set
	", 'id');

	foreach ($all_templatesets as $templateset => $templatesetinfo) {

		/*****************
		* Get HF_Footer
		*****************/

		$result = $db->query_return("
			SELECT *
			FROM template
			WHERE
				templateset = " . intval($templateset) . "
			AND name = 'HF_footer'
		");

		$template = $result['template_unparsed'];

		if (!in_string('$license->copyright', $template)) {
			$update_copyright = true;
		}

		/*****************
		* Get Template Status
		*****************/

		// get the status of the templates
		$db->query("
			SELECT changed, backup, name
			FROM template
			WHERE
				templateset = " . intval($templateset) . "
			AND custom = 0
		");

		$changed_templates = array();
		$changed_noupdate_templates = array();
		$unchanged_templates = array();

		// changed only means something if the backup template is different from what
		// we are inserting

		while ($result = $db->row_array()) {

			$backups[$result['name']] = $result['backup'];

			if ($result['changed'] == 1) {

				if ($result['name'] == 'HF_footer' AND $update_copyright) {
					$unchanged_templates[] = $result['name'];
				} else {
					$changed_templates[$result['name']] = $result['name'];
				}

			} else {
				$unchanged_templates[] = $result['name'];
			}
		}

		/*****************
		* Read Templates
		*****************/

		$names = array();
		$done_files = array();

		$handle = opendir(ROOT . '/install/data/style/default/');
		while (false !== ($file = readdir($handle))) {

			$name = str_replace('.xml', '', $file);

			// Only XML files
			if (!in_string('.xml', $file) OR in_array($file, $done_files)) {
				continue;
			}

			// We dont need to worry about templates
			// that havent been created for the template set
			if ($templatesetinfo['ref'] != 'default' AND !isset($backups[$name])) {
				continue;
			}

			$done_files[] = $file;

			$template = getXML('install/data/style/default/' . $file);

			$template_unparsed = $template['content']['value'];
			$template_parsed = parse_conditionals($template_unparsed);

			$names[] = $name;

			// check on if it should be changed
			if (in_array($name, $changed_templates) AND $template_unparsed == $backups[$name]) {
				unset($changed_templates[$name]);
				$changed_noupdate_templates[] = $name;
			}

			/*****************
			* Update Changed Template
			*****************/

			if (in_array($name, $changed_templates)) {

				$db->query("
					UPDATE template SET
						backup = '" . $db->escape($template_unparsed) . "',
						description = '" . $db->escape($template['description']['value']) . "',
						category = '" . $db->escape($template['category']) . "',
						upgraded = 1
					WHERE
						name = '" . $db->escape($name) . "'
					AND templateset = " . intval($templateset) . "
					AND custom = 0
				");

				if ($db->affected_rows()) {

					// if changed we need to alert that
					$messages[] = "Template $name (set: $templatesetinfo[name]) backup template updated. Awaiting confirmation to change template";
				}

			/*****************
			* Custom, but we made no change, so they dont need to update
			*****************/

			} else if (in_array($name, $changed_noupdate_templates)) {

				// Nothing to do other then make sure the desc/cat is up to date
				// The backup will be the same, and since no changes were made, their edits
				// dont need updated.

				$db->query("
					UPDATE template SET
						description = '" . $db->escape($template['description']['value']) . "',
						category = '" . $db->escape($template['category']) . "'
					WHERE
						name = '" . $db->escape($name) . "'
					AND templateset = " . intval($templateset) . "
					AND custom = 0
				");


			/*****************
			* Update Unchanged Template
			*****************/

			} else if (in_array($name, $unchanged_templates)) {

				$db->query("
					UPDATE template SET
						template = '" . $db->escape($template_parsed) . "',
						template_unparsed = '" . $db->escape($template_unparsed) . "',
						backup = '" . $db->escape($template_unparsed) . "',
						description = '" . $db->escape($template['description']['value']) . "',
						category = '" . $db->escape($template['category']) . "'
					WHERE
						name = '" . $db->escape($name) . "'
					AND templateset = " . intval($templateset) . "
					AND custom = 0
				");

				if ($db->affected_rows()) {
					$messages[] = "Template $name (set: $templatesetinfo[name]) updated";
				}

			/*****************
			* Insert New Template
			*****************/

			// Only insert if the default templateset
			} else if ($templatesetinfo['ref'] == 'default') {

				$db->query("
					INSERT INTO template SET
						template = '" . $db->escape($template_parsed) . "',
						template_unparsed = '" . $db->escape($template_unparsed) . "',
						backup = '" . $db->escape($template_unparsed) . "',
						description = '" . $db->escape($template['description']['value']) . "',
						category = '" . $db->escape($template['category']) . "',
						name = '" . $db->escape($name) . "',
						templateset = " . intval($templateset) . ",
						custom = 0
				");

				if ($db->affected_rows()) {
					$messages[] = "Template $name (set: $templatesetinfo[name]) was created";
				}
			}
		}

		/*****************
		* Delete Unused Tempaltes
		*****************/

		if ($names) {
			$db->query("
				DELETE FROM template
				WHERE templateset = " . intval($templateset) . "
					AND custom = 0
					AND name NOT IN " . array2sql($names) . "
			");
		}
	}

	return $messages;

}

/*
	function sort_templates

	- This function sets the templates to the current version

	- 1. If template does not exist, create it
	- 2. If template exists and has not been modified, updated it
	- 3. If template exists, and has been modified, upgrade the backup and mark the change.

*/

function import_templates_email_tech_xml() {

	global $db;

	// get the status of the templates
	$db->query("
		SELECT changed, name
		FROM template_tech_email
		WHERE custom = 0
	");

	$changed_templates = array();
	$unchanged_templates = array();

	while ($result = $db->row_array()) {

		if ($result['changed'] == 1) {
			$changed_templates[] = $result['name'];
		} else {
			$unchanged_templates[] = $result['name'];
		}
	}

	$done_files = array();

	$handle = opendir(ROOT . '/install/data/techemails/');
	while (false !== ($file = readdir($handle))) {

		if (!in_string('.xml', $file) OR in_array($file, $done_files)) {
			continue;
		}

		$done_files[] = $file;

		$template = getXML('install/data/techemails/' . $file);

		$name = str_replace('.xml', '', $file);
		$template_unparsed = $template['email']['value'];
		$template_parsed = parse_conditionals($template_unparsed);

		$subject_unparsed = $template['subject']['value'];
		$subject = parse_conditionals($subject_unparsed);

		if (in_array($name, $changed_templates)) {

			$db->query("
				UPDATE template_tech_email SET
					backup_template = '" . $db->escape($template_unparsed) . "',
					backup_subject = '" . $db->escape($subject_unparsed) . "',
					description = '" . $db->escape($template['description']['value']) . "'
				WHERE
					name = '" . $db->escape($name) . "'
			");

			if ($db->affected_rows()) {

				// if changed we need to alert that
				$messages[] = "Template $name backup template updated. Awaiting confirmation to change template";
			}

		} else if (in_array($name, $unchanged_templates)) {

			$db->query("
				UPDATE template_tech_email SET
					template = '" . $db->escape($template_parsed) . "',
					subject = '" . $db->escape($subject) . "',
					template_unparsed = '" . $db->escape($template_unparsed) . "',
					subject_unparsed = '" . $db->escape($subject_unparsed) . "',
					backup_template = '" . $db->escape($template_unparsed) . "',
					backup_subject = '" . $db->escape($subject_unparsed) . "',
					description = '" . $db->escape($template['description']['value']) . "'
				WHERE
					name = '" . $db->escape($name) . "'
			");

			if ($db->affected_rows()) {
				$messages[] = "Template $name updated";
			}

		} else {

			$db->query("
				INSERT INTO template_tech_email SET
					name = '" . $db->escape($name) . "',
					template = '" . $db->escape($template_parsed) . "',
					subject = '" . $db->escape($subject) . "',
					template_unparsed = '" . $db->escape($template_unparsed) . "',
					subject_unparsed = '" . $db->escape($subject_unparsed) . "',
					backup_template = '" . $db->escape($template_unparsed) . "',
					backup_subject = '" . $db->escape($subject_unparsed) . "',
					description = '" . $db->escape($template['description']['value']) . "'
			");
		}
	}

	return $messages;
}

/*
	function sort_templates

	- This function sets the templates to the current version

	- 1. If template does not exist, create it
	- 2. If template exists and has not been modified, updated it
	- 3. If template exists, and has been modified, upgrade the backup and mark the change.

*/

function import_templates_email_user_xml() {

	global $db;

	// get the status of the templates
	$db->query("
		SELECT changed, name
		FROM template_user_email
		WHERE custom = 0
	");

	$changed_templates = array();
	$unchanged_templates = array();

	while ($result = $db->row_array()) {

		if ($result['changed'] == 1) {
			$changed_templates[] = $result['name'];
		} else {
			$unchanged_templates[] = $result['name'];
		}
	}

	$done_files = array();

	$handle = opendir(ROOT . '/install/data/useremails/');
	while (false !== ($file = readdir($handle))) {

		if (!in_string('.xml', $file) OR in_array($file, $done_files)) {
			continue;
		}

		$done_files[] = $file;

		$template = getXML('install/data/useremails/' . $file);

		$name = str_replace('.xml', '', $file);
		$template_unparsed = $template['email']['value'];
		$template_parsed = parse_conditionals($template_unparsed);

		$subject_unparsed = $template['subject']['value'];
		$subject = parse_conditionals($subject_unparsed);

		if (in_array($name, $changed_templates)) {

			$db->query("
				UPDATE template_user_email SET
					backup_template = '" . $db->escape($template_unparsed) . "',
					backup_subject = '" . $db->escape($subject_unparsed) . "',
					description = '" . $db->escape($template['description']['value']) . "'
				WHERE
					name = '" . $db->escape($name) . "'
			");

			if ($db->affected_rows()) {

				// if changed we need to alert that
				$messages[] = "Template $name backup template updated. Awaiting confirmation to change template";
			}

		} else if (in_array($name, $unchanged_templates)) {

			$db->query("
				UPDATE template_user_email SET
					template = '" . $db->escape($template_parsed) . "',
					subject = '" . $db->escape($subject) . "',
					template_unparsed = '" . $db->escape($template_unparsed) . "',
					subject_unparsed = '" . $db->escape($subject_unparsed) . "',
					backup_template = '" . $db->escape($template_unparsed) . "',
					backup_subject = '" . $db->escape($subject_unparsed) . "',
					description = '" . $db->escape($template['description']['value']) . "'
				WHERE
					name = '" . $db->escape($name) . "'
			");

			if ($db->affected_rows()) {
				$messages[] = "Template $name updated";
			}

		} else {


			$db->query("
				INSERT INTO template_user_email SET
					name = '" . $db->escape($name) . "',
					template = '" . $db->escape($template_parsed) . "',
					subject = '" . $db->escape($subject) . "',
					template_unparsed = '" . $db->escape($template_unparsed) . "',
					subject_unparsed = '" . $db->escape($subject_unparsed) . "',
					backup_template = '" . $db->escape($template_unparsed) . "',
					backup_subject = '" . $db->escape($subject_unparsed) . "',
					description = '" . $db->escape($template['description']['value']) . "'
			");
		}
	}

	return $messages;

}

?>