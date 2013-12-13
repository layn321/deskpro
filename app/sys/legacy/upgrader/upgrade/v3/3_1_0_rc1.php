<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id$
// +-------------------------------------------------------------+
// | File Details:
// | - Upgrade to 3.1.0 rc1
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3010002 extends upgrade_base_v3 {

	var $version = '3.1.0 RC1';

	var $version_number = 3010002;

	var $pages = array(
		array('Fix Email Bans', 'options.gif'),
		array('Chat Changes', 'options.gif'),
		array('User System - Part 1', 'options.gif'),
		array('User System - Part 2', 'options.gif'),
		array('User System - Part 3', 'options.gif'),
		array('User System - Part 4', 'options.gif'),
		array('Misc Database Changes', 'options.gif'),
		array('Ticket Table', 'options.gif')
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db;

		$this->start('Fixing email bans');
		// fix email_ban table and make sure there are no standard emails there
		$emails = get_data('email_ban');

		if (is_array($emails)) {
			foreach ($emails AS $key => $email) {

				// check if regex or not
				if (!in_string('%', $email)) {

					// remove
					unset($emails[$key]);

					// if is email, make sure we have in ban_email table
					if (is_email($email)) {
						$db->query("REPLACE INTO ban_email SET email = '" . $db->escape($email) . "'");
					}

				}
			}
		}

		$this->yes();

		update_data('email_ban', $emails, 1);

	}

	/***************************************************
	* Chat changes
	*   - Just dropping and recreating them
	***************************************************/

	function step2() {

		global $db;

		$this->start('Recreating chat_chat');
		$db->query("DROP TABLE IF EXISTS `chat_chat`");
		$db->query("
			CREATE TABLE `chat_chat` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `ref` varchar(255) NOT NULL,
			  `depid` int(10) unsigned NOT NULL,
			  `authcode` varchar(32) NOT NULL,
			  `userid` int(10) unsigned NOT NULL,
			  `userdisplayname` varchar(255) NOT NULL,
			  `useremail` varchar(255) NOT NULL,
			  `sessionid` varchar(32) NOT NULL,
			  `techid` int(10) unsigned NOT NULL,
			  `subject` text NOT NULL,
			  `transcript_sent` int(1) NOT NULL default '0',
			  `timestamp_ping` int(10) NOT NULL,
			  `timestamp_start` decimal(12,2) NOT NULL,
			  `timestamp_end` decimal(12,2) NOT NULL,
			  `timestamp_assigned` decimal(12,2) NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `techid` (`techid`),
			  KEY `timestamp_start` (`timestamp_start`),
			  KEY `timestamp_end` (`timestamp_end`),
			  KEY `timestamp_assigned` (`timestamp_assigned`),
			  KEY `timestamp_ping` (`timestamp_ping`)
			)
		");
		$this->yes();


		$this->start('Recreating chat_dep');
		$db->query("DROP TABLE IF EXISTS `chat_dep`");
		$db->query("
			CREATE TABLE `chat_dep` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `name` varchar(255) NOT NULL,
			  `displayorder` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();


		$this->start('Recreating chat_message');
		$db->query("DROP TABLE IF EXISTS `chat_message`");
		$db->query("
			CREATE TABLE `chat_message` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `chatid` int(10) unsigned NOT NULL,
			  `authorid` varchar(32) NOT NULL,
			  `authortype` enum('user','tech','system') NOT NULL,
			  `sessionid` varchar(32) NOT NULL,
			  `authorname` varchar(255) NOT NULL,
			  `message` text NOT NULL,
			  `formatting` enum('none','html') NOT NULL default 'none',
			  `visibility` enum('all','tech','user') NOT NULL,
			  `timestamp_sent` decimal(12,2) NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `chatid` (`chatid`),
			  KEY `timestamp_sent` (`timestamp_sent`)
			)
		");
		$this->yes();
	}


	/***************************************************
	* Database changes for user system
	***************************************************/

	function step3() {

		global $db;

		$this->start("New user_deskpro table for local auth");
		$db->query("
			CREATE TABLE `user_deskpro` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `username` varchar(255) NOT NULL,
			  `password` varchar(32) NOT NULL,
			  `salt` varchar(15) NOT NULL,
			  `password_change_key` varchar(8) NOT NULL,
			  `password_change_timestamp` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();

		$this->start("New user_map table");
		$db->query("
			CREATE TABLE `user_map` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `localid` int(10) unsigned NOT NULL,
			  `remoteid` varchar(255) NOT NULL,
			  `username` varchar(255) NOT NULL,
			  `sourceid` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `localid` (`localid`,`remoteid`)
			)
		");
		$this->yes();

		$this->start("New user_map_validate table");
		$db->query("
			CREATE TABLE `user_map_validate` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `localid` int(10) unsigned NOT NULL,
			  `remoteid` varchar(255) NOT NULL,
			  `sourceid` int(10) unsigned NOT NULL,
			  `auth` varchar(8) NOT NULL,
			  `timestamp_requested` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();

		$this->start("New user_source table");
		$db->query("
			CREATE TABLE `user_source` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `module` varchar(255) NOT NULL,
			  `enabled` tinyint(1) NOT NULL default '0',
			  `runorder` int(11) NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  `user_title` varchar(255) NOT NULL,
			  `user_description` text NOT NULL,
			  `url` varchar(255) NOT NULL,
			  `config` text NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();

		$this->start('Updating last_autosession in user table');
		$db->query("UPDATE user SET last_autosession = '0'");
		$this->yes();

		$this->start("New fields in user table");
		$db->query("
			ALTER TABLE `user`
				CHANGE `last_autosession` `last_autosession` INT UNSIGNED NOT NULL,
				ADD `default_emailid` int(10) unsigned,
				ADD `last_cache_update` int(11)
		");
		$this->yes();

		$this->start("New field in user_email table");
		$db->query("ALTER TABLE `user_email`  ADD `id` int(10) unsigned  AUTO_INCREMENT PRIMARY KEY , ADD `mapid` int(10)");
		$this->yes();
	}

	/***************************************************
	* Add usersource if not using default
	***************************************************/

	function step4() {

		global $settings, $db;

		$this->start("Insert " . DP_NAME . " Source");

		$source_config = array(
			'disable_secure_passwords' => 1,
			'done_setup' => 1
		);

		$db->query("
			INSERT INTO `user_source`
			SET
				module = 'Dp',
				enabled = 1,
				runorder = -1000,
				title = '" . DP_NAME . "',
				description = 'The default " . DP_NAME . " user source',
				user_title = 'Helpdesk',
				user_description = 'This helpdesk',
				url = '" . $db->escape($settings['helpdesk_url']) . "',
				config = '" . $db->escape(serialize($source_config)) . "'
		");
		$this->yes();



		$authdata = get_data('userauth');

		switch ($settings['userauth_module']) {
			case 'UserAuthCustom':
				$this->start("Insert Custom Source");

				$source_config = array(
					'db_type' => $authdata['db_type'],
					'db_host' => $authdata['db_host'],
					'db_user' => $authdata['db_user'],
					'db_pass' => $authdata['db_pass'],
					'db_name' => $authdata['db_name'],
					'table' => $authdata['table'],
					'field_id' => $authdata['id_field'],
					'field_username' => $authdata['user_field'],
					'field_password' => $authdata['pass_field'],
					'field_email' => $authdata['email_field'],
					'password_php' => $authdata['pass_php'],
					'test_user' => $authdata['test_user'],
					'test_pass' => $authdata['test_pass'],
					'done_setup' => 1
				);

				$db->query("
					INSERT INTO `user_source`
					SET
						module = 'Custom',
						enabled = 1,
						runorder = 1,
						title = 'Custom',
						description = 'Your custom user source',
						user_title = 'Website',
						config = '" . $db->escape(serialize($source_config)) . "'
				");

				$this->yes();
				break;

			case 'UserAuthVbulletin':
				$this->start("Insert vBulletin Source");

				$source_config = array(
					'db_type' => $authdata['db_type'],
					'db_host' => $authdata['db_host'],
					'db_user' => $authdata['db_user'],
					'db_pass' => $authdata['db_pass'],
					'db_name' => $authdata['db_name'],
					'table_prefix' => $authdata['prefix'],
					'cookie_prefix' => $authdata['cookie_prefix'],
					'test_user' => $authdata['test_user'],
					'test_pass' => $authdata['test_pass'],
					'done_setup' => 1
				);

				$db->query("
					INSERT INTO `user_source`
					SET
						module = 'vBulletin',
						enabled = 1,
						runorder = 1,
						title = 'vBulletin',
						description = 'Your vBulletin user source',
						user_title = 'vBulletin',
						user_description = '',
						config = '" . $db->escape(serialize($source_config)) . "'
				");

				$this->yes();
				break;

			case 'UserAuthPhpbb':
				$this->start("Insert phpBB Source");

				$source_config = array(
					'db_type' => $authdata['db_type'],
					'db_host' => $authdata['db_host'],
					'db_user' => $authdata['db_user'],
					'db_pass' => $authdata['db_pass'],
					'db_name' => $authdata['db_name'],
					'table_prefix' => $authdata['prefix'],
					'cookie_prefix' => $authdata['cookie_prefix'],
					'test_user' => $authdata['test_user'],
					'test_pass' => $authdata['test_pass'],
					'done_setup' => 1
				);

				$db->query("
					INSERT INTO `user_source`
					SET
						module = 'phpBB',
						enabled = 1,
						runorder = 1,
						title = 'phpBB',
						description = 'Your phpBB user source',
						user_title = 'phpBB',
						user_description = '',
						config = '" . $db->escape(serialize($source_config)) . "'
				");

				$this->yes();
				break;

			case 'UserAuthLdap':
				$this->start("Insert LDAP Source");

				$source_config = array(
					'ldap_version' => $authdata['ldap_version'],
					'ldap_host' => $authdata['ldap_host'],
					'ldap_port' => $authdata['ldap_port'],
					'ldap_tls' => $authdata['ldap_tls'],
					'ldap_service_dn' => $authdata['ldap_service_dn'],
					'ldap_service_pass' => $authdata['ldap_service_pass'],
					'ldap_base_dn' => $authdata['ldap_base_dn'],
					'ldap_attr_uid' => $authdata['ldap_attr_uid'],
					'ldap_attr_mail' => $authdata['ldap_attr_mail'],
					'test_user' => $authdata['test_user'],
					'test_pass' => $authdata['test_pass'],
					'done_setup' => 1
				);

				$db->query("
					INSERT INTO `user_source`
					SET
						module = 'LDAP',
						enabled = 1,
						runorder = 1,
						title = 'LDAP',
						description = 'Your LDAP user source',
						user_title = 'LDAP',
						user_description = '',
						config = '" . $db->escape(serialize($source_config)) . "'
				");

				$this->yes();
				break;

			default:
			case 'UserAuth':

				// They're using DeskPRO, we should add that notice about
				// secure passwords
				add_admin_notice(
					'upgrade_secure_passwords',
					'Secure User Passwords',
					'
						Prior to version 3.1, user passwords were all stored in plaintext. This means that the password
						is readable to anyone who has access to your database (other admins, sysadmins etc). In 3.1 you now
						have the option to store passwords in a non-reversible obscured format. This means that anyone who
						can access the database will never be able to know what the users real password is.
						<br /><br />
						If you have not already, you can <a href="usersources.php?do=setup&id=1">setup the ' . DP_NAME . ' user source</a> to use
						secure passwords. The only reason you would not want to enable secure password storage is if you have other
						applications interacting with ' . DP_NAME . ' that require the passwords to be in plaintext. In any other case,
						we strongly recommend you enable this feature.
					'
				);

				break;
		}

		$this->start('Setting registration URL');

		// If there is a URL, all reg settings go to 'force'
		if ($settings['userauth_signup_url']) {
			legacy_update_setting('register_url', $settings['userauth_signup_url']);
			legacy_update_setting('gateway_guest_deny', 1);

			$db->query("
				UPDATE user_groups
				SET p_ticket_new = 0
				WHERE system_name = 'guest'
			");
		}
		$this->yes();
	}


	/***************************************************
	* Move deskpro user info to user_deskpro
	***************************************************/

	function step5($page) {

		global $db;

		/*************************
		* Get the user source
		*************************/

		// - If there is only one source then it is
		// the DeskPRO source.
		// - If there are two, then the second one will
		// be a custom source (first will always be deskpro)
		// and we dont create deskpro records for them, just maps

		$all_sources = $db->query_return_array("SELECT * FROM user_source ORDER BY id");

		if (count($all_sources) == 1) {
			$source = $all_sources[0];
		} else {
			$source = $all_sources[1];
		}

		/*************************
		* Get the users
		*************************/

		// get total we have (dosen't change)
		$total = $db->query_return_first("
			SELECT COUNT(*)
			FROM user
		");

		if (!$total) {
			$this->start("Updating users");
			$this->yes();
			return;
		}

		$pages = ceil($total / 250);

		$user_start = ($page - 1) * 250;
		if ($user_start) {
			$limit = "LIMIT $user_start, 250";
		} else {
			$limit = "LIMIT 250";
		}


		/*************************
		* Go over each user
		*************************/

		$this->start("Updating to new user system. Page $page of $pages");

		$userdata = $db->query_return_array("SELECT id, username, password, email FROM user ORDER BY id $limit");

		foreach ($userdata as $user) {

			// user_deskpro record
			if ($source['module'] == 'Dp') {
				$db->query("
					INSERT INTO user_deskpro
					SET
						username = '" . $db->escape($user['username']) . "',
						password = '" . $db->escape($user['password']) . "'
				");

				$remoteid = $db->insert_id();

			// External record
			} else {
				$remoteid = $user['authid'];
			}

			// user_map record
			$db->query("
				INSERT INTO user_map
				SET
					localid = $user[id],
					remoteid = '" . $db->escape($remoteid) . "',
					username = '" . $db->escape($user['username']) . "',
					sourceid = $source[id]
			");

			$mapid = $db->insert_id();

			// Dupes for some reason sometimes,
			// so delete the email address before adding it
			$db->query("
				DELETE FROM user_email
				WHERE email = '" . $db->escape($user['email']) . "'
			");

			// Add email
			$db->query("
				INSERT INTO user_email
				SET
					email = '" . $db->escape($user['email']) . "',
					userid = $user[id],
					validated = 1,
					timestamp = " . TIMENOW . "
			");

			$emailid = $db->insert_id();

			if ($source['module'] != 'Dp') {
				$db->query("
					UPDATE user_email
					SET mapid = $mapid
					WHERE userid = $user[id]
				");
			}

			// The inserted email will be the default
			$db->query("
				UPDATE user
				SET
					default_emailid = $emailid
				WHERE id = $user[id]
			");
		}

		$this->yes();

		unset($userdata);

		// do we still have more to do?
		if ($page != $pages) {
			$page++;
			$this->redoStep(5, $page);
		}
	}


	/***************************************************
	* Final changes to user table
	***************************************************/

	function step6() {

		global $db;

		$this->start('Removing old fields from user table');
		$db->query("ALTER TABLE `user`  DROP `authid`,  DROP `email`, DROP `password`");
		$this->yes();

		$this->start('Adding reg_from to user table');
		$db->query("ALTER TABLE  `user` ADD  `reg_from` VARCHAR( 60 ) NOT NULL");
		$this->yes();
	}


	/***************************************************
	* Misc db changes
	***************************************************/

	function step7() {

		global $db;

		$this->start('Adding manual permissions table');
		$db->query("
			CREATE TABLE `manual_manuals_perms` (
			  `manualid` int(10) unsigned NOT NULL,
			  `usergroup` int(10) unsigned NOT NULL,
			  `perm_type` varchar(60) NOT NULL,
			  PRIMARY KEY  (`manualid`,`usergroup`,`perm_type`),
			  KEY `manualid` (`manualid`)
			)
		");
		$this->yes();

		$this->start('Update KB inherit option');

		// Get which should be inherit
		$all_perms = $db->query_return_group("SELECT * FROM faq_permissions", 'catid');
		$cats_both = array();

		if ($all_perms) {
			foreach ($all_perms as $catid => $perms) {
				if (in_array(1, $perms) AND in_array(2, $perms)) {
					$cats_both[] = $catid;
				}
			}
		}

		$db->query("UPDATE faq_cats SET perm_inherit = 1 WHERE id IN " . array2sql($cats_both));

		// Make sure cats with inherit dont have permission entries
		$cats = $db->query_return_array_id("SELECT id FROM faq_cats WHERE perm_inherit = 1", 'id', '');
		$db->query("DELETE FROM faq_permissions WHERE catid IN " . array2sql($cats));

		$this->yes();

		$this->start('Adding missing company criteria to escalate table');
		$db->query("ALTER TABLE `escalate`  ADD `criteria_company` int(10) unsigned ");
		$this->yes();
	}

	/***************************************************
	* Ticket table
	***************************************************/

	function step8() {

		global $db;

		$this->start('Updating ticket table');
		$db->query("
			ALTER TABLE `ticket` ADD `rule_mail_id` INT( 10 ) NOT NULL ;
		");
		$this->yes();

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
$upgrade = new upgrade_3010002();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));