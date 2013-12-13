<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id$
// +-------------------------------------------------------------+
// | File Details:
// | - Upgrade to 3.1.0 gold
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3010003 extends upgrade_base_v3 {

	var $version = '3.1.0';

	var $version_number = 3010003;

	var $pages = array(
		array('Main Database Changes', 'options.gif'),
		array('Update Language Versions', 'options.gif'),
		array('Fixing Emails', 'options.gif'),
		array('Clear Indexes', 'options.gif'),
		array('Add Indexes', 'options.gif'),
		array('Normalize Indexes', 'options.gif'),
		array('Creating Translator Languages', 'options.gif'),
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db;

		// Add note about we are now enforcing perms even in gateway
		$this->start('Add admin note about gateway permissions');
		add_admin_notice(
			'upgrade_enforce_perms_gateway',
			'Permissions are enforced in the Email Gateway',
			'An important change was made in version 3.1: ticket category and priority permissions are now enforced even in the email gateway...',
			'
				In previous versions, there were two general permissions for categories and priorities: user selectable, and require registration.
				These permissions were only enforced when users were submitting tickets form the web interface.<br /><br />

				This has changed in ' . DP_NAME . ' v3.1. Permissions are now also checked in the gateway. This means that a guest for example cannot submit
				an email that will be placed into a category for registered members only, they will get a permission denied notification.
				This gives you the ability to create "priority" email addresses.<br /><br />

				Due to this change you should look over your <a href="ticket_rules_mail.php">mail rules</a> and ensure your ticket
				<a href="ticket_category.php">categories</a> and <a href="ticket_priority.php">priorities</a> have the correct permissions.<br /><br />

				Bottom line: If the user cannot use the category or priority in the user interface, then they cannot use them in the gateway
				either (the same permission	checks are performed). The only difference is that you, the administrator, control which category
				or priority a ticket will have via mail rules -- which is why it is so important to double check your setup.
			'
		);
		$this->yes();

		$this->start('Insert default permissons for manuals');

		$perm_types = array('download_zip', 'view', 'view_print', 'view_single');
		$manids = (array)$db->query_return_array_id("SELECT id FROM manual_manuals", 'id', '');

		foreach ($manids as $manid) {

			// Dont process manual if it already has perms
			if ($db->query_count('manual_manuals_perms', "manualid = $manid")) {
				continue;
			}

			foreach ($perm_types as $perm_type) {
				$db->query("
					INSERT INTO manual_manuals_perms
					SET manualid = $manid, usergroup = 0, perm_type = '$perm_type'
				");
			}
		}

		$this->yes();

		$this->start('Add code type to ticket field display table');
		$db->query("ALTER TABLE  `ticket_fielddisplay` ADD  `code_type` VARCHAR( 3 ) NOT NULL DEFAULT  'var' AFTER  `code`");
		$this->yes();

		$this->start('Add new extracss field for styles');
		$extracss_check = $db->query_return("SELECT * FROM style LIMIT 1");
		if (!isset($extracss_check['extracss_unparsed'])) {
			$db->query("ALTER TABLE  `style` ADD  `extracss_unparsed` MEDIUMTEXT NOT NULL AFTER  `extracss`");
		}
		$this->yes();

		$this->start('Add field to languages table');
		$db->query("ALTER TABLE  `languages` CHANGE  `version`  `version` VARCHAR( 20 ) NOT NULL");
		$db->query("ALTER TABLE  `languages` ADD  `has_submitted` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `deskproid`");
		$this->yes();

		$this->start('Add hash to ticket_notes table');
		$db->query("ALTER TABLE  `ticket_notes` ADD  `hash` VARCHAR( 32 ) NOT NULL");
		$this->yes();

		$this->start('Add hash to faq_comments table');
		$db->query("ALTER TABLE  `faq_comments` ADD  `hash` VARCHAR( 32 ) NOT NULL");
		$this->yes();

		$this->start('Add hash to manual_comments table');
		$db->query("ALTER TABLE  `manual_comments` ADD  `hash` VARCHAR( 32 ) NOT NULL AFTER  `comments`");
		$this->yes();

		$this->start('Add auto-DST field to tech table');
		$db->query("ALTER TABLE  `tech` ADD  `dst_auto_adjust` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `timezone_dst`");
		$this->yes();

		$this->start('Add column for ticket notifier');
		$db->query("ALTER TABLE `tech` ADD `notifier_plugin` INT (1) NOT NULL DEFAULT '0'");
		$this->yes();

		$this->start('Add/remove columns to plugin table');
		$db->query("
			ALTER TABLE  `plugins` ADD  `admin_url` VARCHAR( 255 ) NOT NULL ,
			ADD  `info_url` VARCHAR( 255 ) NOT NULL,
			ADD  `plugin_dir` VARCHAR( 255 ) NOT NULL
		");
		$db->query("ALTER TABLE  `plugins` DROP  `url`");
		$this->yes();

		$this->start('Change column type on forum messages table');
		$db->query("ALTER TABLE  `tech_forum_message` CHANGE  `message`  `message` TEXT NOT NULL");
		$this->yes();

		$this->start('Adding field to languages table');
		$db->query("ALTER TABLE  `languages` ADD  `linked_deskproid` VARCHAR( 20 ) NOT NULL AFTER  `deskproid`");
		$this->yes();

		$this->start('Fix mapid column in user_email to be not null');
		$db->query("ALTER TABLE  `user_email` CHANGE  `mapid`  `mapid` INT( 10 ) NOT NULL DEFAULT  '0'");
		$this->yes();

		$this->start('Fix null columns in calendar_task_iteration');
		$db->query("
			ALTER TABLE  `calendar_task_iteration` CHANGE  `taskid`  `taskid` INT( 11 ) NOT NULL DEFAULT  '0',
			CHANGE  `completed`  `completed` INT( 11 ) NOT NULL DEFAULT  '0'
		");
		$this->yes();
	}


	/***************************************************
	* Language version numbers
	***************************************************/

	function step2() {

		global $db;

		require_once(INC . 'classes/class_DpBuilds.php');
		$dpbuilds = new DpBuilds();

		// Langs
		$this->start('Update language version numbers');

		foreach ($dpbuilds->legacy_versions as $legacy => $newbuild) {

			$db->query("
				UPDATE languages
				SET master = $newbuild
				WHERE master = $legacy
			");

			$db->query("
				UPDATE template_words
				SET version = $newbuild
				WHERE version = $legacy
			");
		}

		$this->yes();

		// Update lang upgrade
		$this->start('Update language upgrade information');
		$data = get_data('language_upgrade');

		if (is_array($data)) {

			foreach ($data as $langid => $langdata) {
				foreach ($langdata as $legacy => $version_data) {

					unset($data[$langid][$legacy]);

					$newbuild = $dpbuilds->convertIfLegacy($legacy);
					$data[$langid][$newbuild] = $version_data;
				}
			}

			update_data('language_upgrade', $data);

		}

		$this->yes();
	}


	/***************************************************
	* Need to recreate indexes on email
	***************************************************/

	function step3($page) {

		global $db;

		$this->start('Fixing duplicate emails - Page ' . $page);

		$ids = $db->query_return_array_id("
			SELECT id, COUNT(*) AS total
			FROM user_email
			GROUP BY email, mapid
			HAVING total > 1
			LIMIT 100
		", 'id', '');

		if ($ids) {

			$db->query("
				DELETE FROM user_email
				WHERE id IN " . array2sql($ids) . "
			");

			$this->yes();

			// Just keep going until theres nothing left
			$this->redoStep(3, $page++);
		} else {
			$this->yes();
		}
	}

	function step4() {
		$this->start('Clearing indexes on user_email');
		clear_table_indexes('user_email');
		$this->yes();
	}

	function step5() {

		$this->start('Adding indexes to user_email');
		add_table_indexes(
			'user_email',
			array(
				array('email', 'UNIQUE', array('email', 'mapid')),
				array('userid', 'INDEX', array('userid')),
				array('mapid', 'INDEX', array('mapid'))
			)
		);
		$this->yes('Adding indexes to user_email');
	}

	/***************************************************
	* Normalizing Indexes
	***************************************************/

	function step6($page) {

		global $db;

		$max_page = 14;

		$this->start("Normalizing Old Indexes - Page $page of $max_page");

		switch ($page) {

			/********************
			* calendar_task_tech
			********************/
			case 1:
				$cur_indexes = get_table_indexes('calendar_task_tech');

				if (in_array('eventid', $cur_indexes)) {
					$db->query("ALTER TABLE `calendar_task_tech` DROP INDEX `eventid`");
				}

				if (in_array('taskid', $cur_indexes)) {
					$db->query("ALTER TABLE `calendar_task_tech` DROP INDEX `taskid`");
				}
				break;

			case 2:
				$db->query("ALTER TABLE  `calendar_task_tech` ADD UNIQUE  `taskid` (  `taskid` ,  `techid` )");
				break;

			/********************
			* gateway_pop_failures
			********************/
			case 3:
				$cur_indexes = get_table_indexes('gateway_pop_failures');

				if (in_array('accountid', $cur_indexes)) {
					$db->query("ALTER TABLE `gateway_pop_failures` DROP INDEX `accountid`");
				}
				break;

			/********************
			* gateway_pop_accounts
			********************/
			case 4:
				$cur_indexes = get_table_indexes('gateway_pop_accounts');

				if (in_array('server', $cur_indexes)) {
					$db->query("ALTER TABLE `gateway_pop_accounts` DROP INDEX `server`");
				}
				break;

			case 5:
				$db->query("ALTER TABLE  `gateway_pop_accounts` ADD UNIQUE  `server` (  `server` ,  `username` )");
				break;

			/********************
			* ticket : ref unique
			********************/
			case 6:
				$cur_indexes_info = $db->query_return_array("SHOW INDEX FROM ticket");

				foreach ($cur_indexes_info as $index) {
					if ($index['Key_name'] == 'ref' AND $index['Non_unique']) {
						$db->query("ALTER TABLE `ticket` DROP INDEX `ref`");
						break;
					}
				}

				break;

			case 7:
				$cur_indexes = get_table_indexes('ticket');

				if (!in_array('ref', $cur_indexes)) {
					$db->query("ALTER TABLE  `ticket` ADD UNIQUE  `ref` (  `ref` )");
				}
				break;

			/********************
			* ticket : tech/status
			********************/
			case 8:
				$cur_indexes_info = $db->query_return_array("SHOW INDEX FROM ticket");

				// Should be on two cols
				$col_count = 0;
				foreach ($cur_indexes_info as $index) {
					if ($index['Key_name'] == 'tech') {
						$col_count++;
					}
				}

				if ($col_count != 2 AND $col_count) {
					$db->query("ALTER TABLE `ticket` DROP INDEX `tech`");
				}

				break;

			case 9:
				$cur_indexes = get_table_indexes('ticket');

				if (!in_array('tech', $cur_indexes)) {
					$db->query("ALTER TABLE  `ticket` ADD INDEX  `tech` (  `tech` ,  `status` )");
				}
				break;

			/********************
			* ticket : tech/status
			********************/
			case 10:
				$cur_indexes_info = $db->query_return_array("SHOW INDEX FROM ticket");

				// Should be on two cols
				$col_count = 0;
				foreach ($cur_indexes_info as $index) {
					if ($index['Key_name'] == 'priority') {
						$col_count++;
					}
				}

				if ($col_count != 2 AND $col_count) {
					$db->query("ALTER TABLE `ticket` DROP INDEX `priority`");
				}

				break;

			case 11:
				$cur_indexes = get_table_indexes('ticket');

				if (!in_array('priority', $cur_indexes)) {
					$db->query("ALTER TABLE  `ticket` ADD INDEX  `priority` (  `priority` ,  `status` )");
				}
				break;

			/********************
			* calendar_task_iteration
			********************/
			case 12:

				$cur_indexes_info = $db->query_return_array("SHOW INDEX FROM calendar_task_iteration");

				// Should be on three cols
				$col_count = 0;
				foreach ($cur_indexes_info as $index) {
					if ($index['Key_name'] == 'PRIMARY') {
						$col_count++;
					}
				}

				if ($col_count != 3 AND $col_count) {
					$db->query("ALTER TABLE `calendar_task_iteration` DROP PRIMARY KEY");
				}

				break;

			case 13:

				$cur_indexes = get_table_indexes('calendar_task_iteration', true, true);

				if (!in_array('PRIMARY', $cur_indexes)) {
					$db->query("ALTER TABLE  `calendar_task_iteration` ADD PRIMARY KEY (  `taskid` ,  `task_techid` ,  `timestamp` )");
				}

				break;


			/********************
			* user
			********************/
			case 14:

				// Username should be INDEX now, not unique

				$cur_indexes = get_table_indexes('user', true, true);

				if (in_array('username', $cur_indexes)) {
					$db->query("ALTER TABLE  `user` DROP INDEX  `username` , ADD INDEX  `username` (  `username` )");

				// I think RC, there just was no index in tables.php
				// so only for new installs, we add it instead of delete it
				} else {
					$db->query("ALTER TABLE  `user` ADD INDEX  `username` (  `username` )");
				}
				break;
		}

		$this->yes();

		// do we still have more to do?
		if ($page != $max_page) {
			$page++;
			$this->redoStep(6, $page);
		}
	}

	function step7($page) {

		global $db;

		/********************
		* Ge the langs that need updating still
		********************/

		$update_langs = $db->query_return_array_id("
			SELECT languages.*
			FROM languages
				LEFT JOIN languages AS languages2 ON (languages2.linked_deskproid = languages.deskproid)
			WHERE
				languages.deskproid != ''
				AND languages2.id IS NULL
				AND languages.id != 1
		", '', 'id');


		/********************/


		$this->start('Creating Translator languages - Page ' . $page);

		if ($update_langs) {
			$cust_lang = array_shift($update_langs);
		}

		if ($cust_lang) {

			/********************
			* Insert new lang
			********************/

			$values = array(
				'name' => $cust_lang['name'] . ' (Translator)',
				'is_selectable' => 0,
				'isocode' => $cust_lang['isocode'],
				'contenttype' => $cust_lang['contenttype'],
				'direction' => $cust_lang['direction'],
				'deskproid' => $cust_lang['deskproid'],
				'has_submitted' => $cust_lang['has_submitted'],
				'version' => $cust_lang['version'],
				'flag' => $cust_lang['flag'],
				'master' => $cust_lang['master'],
			);

			$db->query("INSERT INTO languages SET " . array2sqlinsert($values));
			$langid = $db->insert_id();

			// Update old lang to link them. It doesnt have a deskproid for itself
			$db->query("
				UPDATE languages
				SET deskproid = '', linked_deskproid = '{$cust_lang['deskproid']}'
				WHERE id = {$cust_lang['id']}
			");


			/********************
			* Copy words over
			* - Alwyas the backuptext so its the original
			********************/

			$words = $db->query_return_array_id("
				SELECT wordref, backuptext, version
				FROM template_words
				WHERE cust = 0 AND language = {$cust_lang['id']}
			", '', 'wordref');

			foreach ($words as $wordref => $wordinfo) {
				$values = array(
					'language' => $langid,
					'wordref' => $wordref,
					'text' => $wordinfo['backuptext'],
					'backuptext' => $wordinfo['backuptext'],
					'version' => $wordinfo['version']
				);
				$db->query("INSERT INTO template_words SET " . array2sqlinsert($values));
			}
		}

		$this->yes();

		// If there are any left, do another page
		if ($update_langs) {
			$page++;
			$this->redoStep(7, $page);
		}
	}
}



/***************************************************
* - RUN CLASS
***************************************************/

// check we are in correct location
install_check();

// display header
$header->build();

// create the installer
$upgrade = new upgrade_3010003();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));