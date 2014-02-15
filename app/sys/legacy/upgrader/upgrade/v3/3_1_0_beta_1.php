<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id$
// +-------------------------------------------------------------+
// | File Details:
// | - Upgrade to 3.1.0 beta 1
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3010001 extends upgrade_base_v3 {

	var $version = '3.1.0 Beta 1';

	var $version_number = 3010001;

	var $pages = array(
		array('Main Database Changes', 'options.gif'),
		array('Usergroups and Companies', 'options.gif'),
		array('Ticket Category Permissions', 'options.gif'),
		array('Temp Ticket Changes', 'options.gif'),
		array('Ticket Priority Permissions', 'options.gif'),
		array('Custom Field Permissions', 'options.gif'),
		array('Misc Database Changes 1', 'options.gif'),
		array('Misc Database Chagnes 2', 'options.gif'),
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db, $settings;

		$this->start('Create blob_merge table');
		$db->query("
			CREATE TABLE `blob_merge` (
			  `blobid` int(10) NOT NULL,
			  `tablename` varchar(250) NOT NULL,
			  `tableid` int(10) NOT NULL,
			  KEY `blobid` (`blobid`),
			  KEY `tableid` (`tableid`,`tablename`)
			)
		");
		$this->yes();


		$this->start('Add some user fields');

		$db->query("ALTER TABLE `user`
			ADD `user_ticketlist_settings` MEDIUMTEXT NOT NULL,
			ADD `dst_auto_adjust` INT( 1 ) NOT NULL DEFAULT '0' AFTER `timezone_dst`,
			ADD `default_company` INT UNSIGNED NOT NULL,
			ADD `btechid` INT UNSIGNED NOT NULL AFTER `authid`
		");
		$this->yes();

		$this->start('Add global column to tech files');
		$db->query('ALTER TABLE `tech_attachments` ADD `isglobal` INT( 1 ) NOT NULL ;');
		$this->yes();

		$this->start('Remember tech search ordering and field matching between searches');
		$db->query("ALTER TABLE `tech` ADD `user_search_settings` MEDIUMTEXT NOT NULL");
		$this->yes();

		$this->start('Adding tech setting to get note notifications');
		$db->query("ALTER TABLE `tech` ADD `email_note` INT( 1 ) NOT NULL ;");
		$this->yes();

		$this->start('Adding fields to ticket category table');
		$db->query("ALTER TABLE `ticket_cat` ADD `parent` INT UNSIGNED NOT NULL AFTER `id`");
		$this->yes();

		$this->start('Adding fields to tech table');
		$db->query("ALTER TABLE `tech` ADD `name` VARCHAR( 250 ) NOT NULL AFTER `is_admin`");
		$this->yes();

		$this->start('Adding pinned field to table');
		$db->query("
			ALTER TABLE `ticket` ADD `pinned` INT(1) NOT NULL,
			ADD INDEX (`pinned`)
		");
		$this->yes();

		$this->start('Adding tech login log table');
		$db->query("
			CREATE TABLE `tech_login_log` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`techid` INT UNSIGNED NOT NULL ,
				`ipaddress` VARCHAR( 250 ) NOT NULL ,
				`altipaddress` VARCHAR( 250 ) NOT NULL ,
				`useragent` VARCHAR( 255 ) NOT NULL ,
				`timestamp` INT UNSIGNED NOT NULL ,
				`section` ENUM( 'tech', 'admin' ) NOT NULL
			)
		");
		$this->yes();

		$this->start('Removing old tech IP table');
		$db->query("DROP TABLE tech_ips");
		$this->yes();

		$this->start('Adding "email on login" option for techs');
		$db->query("ALTER TABLE `tech` ADD `email_on_login` INT( 1 ) NOT NULL DEFAULT '0' AFTER `display_faq_advanced`");
		$this->yes();

		$this->start('Adding new escalation criteria');
		$db->query("
			ALTER TABLE `escalate` ADD `criteria_usergroup` VARCHAR( 250 ) NOT NULL AFTER `criteria_tech` ,
			ADD `criteria_useremail` VARCHAR( 250 ) NOT NULL AFTER `criteria_usergroup`
		");
		$this->yes();

		$this->start('Adding new user rules table');
		$db->query("
			CREATE TABLE `user_rules` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `criteria` text NOT NULL,
			  `actions` text NOT NULL,
			  `run_web` tinyint(1) NOT NULL default '0',
			  `run_order` int(11) NOT NULL default '0',
			  `link_company` int(10) unsigned NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();

		$this->start('Add company field');

		$db->query("ALTER TABLE `ticket`
			ADD `company` INT( 10 ) NOT NULL,
			ADD INDEX ( `company` ),
			ADD `btech` INT UNSIGNED NOT NULL AFTER `tech`,
			ADD `btech_handle` TINYINT( 1 ) NOT NULL DEFAULT '0'
		");

		$this->yes();

		$this->start('Adding new fields to settings table');
		$db->query('
			ALTER TABLE `settings` ADD `php_verify` TEXT NOT NULL ,
			ADD `php_generate` TEXT NOT NULL
		');
		$this->yes();

		$this->start('Adding default company fields to user table and user_email tables');
		$db->query("ALTER TABLE `user_email` ADD `default_company` INT UNSIGNED NOT NULL");
		$this->yes();

		$this->start('Modifying formtype field of custom field tables');
		$db->query("ALTER TABLE `ticket_def` CHANGE `formtype` `formtype` VARCHAR( 250 ) NOT NULL DEFAULT 'input'");
		$db->query("ALTER TABLE `user_def` CHANGE `formtype` `formtype` VARCHAR( 250 ) NOT NULL DEFAULT 'input'");
		$db->query("ALTER TABLE `calendar_def` CHANGE `formtype` `formtype` VARCHAR( 250 ) NOT NULL DEFAULT 'input'");
		$db->query("ALTER TABLE `faq_def` CHANGE `formtype` `formtype` VARCHAR( 250 ) NOT NULL DEFAULT 'input'");
		$this->yes();

		$this->start('Modifying faq_cats table');
		$db->query("ALTER TABLE `faq_cats` ADD `perm_inherit` TINYINT( 1 ) NOT NULL DEFAULT '1'");

		// Were using usergroups in 3.0.x already, these were never used
		$db->query("ALTER TABLE `faq_cats` DROP `p_loggedin`, DROP `p_restricted`");
		$this->yes();

		$this->start('Adding reports permission to tech table');
		$db->query("ALTER TABLE `tech` ADD `p_reports` INT( 1 ) NOT NULL ;");
		$db->query("UPDATE tech SET p_reports = 1 WHERE is_admin = 1");
		$this->yes();

		$this->start('Add global ticket searches');
		$db->query("ALTER TABLE `ticket_filters` ADD `isglobal` INT( 1 ) NOT NULL ;");
		$this->yes();

		$this->start('Add tech permission for task assignment');
		$db->query("ALTER TABLE `tech` ADD `p_assign_task` INT( 1 ) NOT NULL ;");
		$db->query("UPDATE tech SET p_assign_task = 1");
		$this->yes();

		$this->start('Add tech permission for forum deletion');
		$db->query("ALTER TABLE `tech` ADD `p_forum_deleteforum` INT( 1 ) NOT NULL ;");
		$this->yes();

		$this->start('Add allow_permissions field for KB articles');
		$db->query("ALTER TABLE `faq_articles` ADD `allow_comments` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `timestamp_modified`");
		$this->yes();

		$this->start('Add display order field for file categories');
		$db->query("ALTER TABLE `files_cats` ADD `displayorder` INT NOT NULL");
		$this->yes();

		$this->start('Add indexes');
		add_table_indexes('ticket', array(array('btech_handle', 'INDEX', array('btech_handle')), array('btech', 'INDEX', array('btech'))));
		add_table_indexes('user', array(array('btechid', 'INDEX', array('btechid'))));
		$this->yes();

		$this->start('Combining max message size options');
		$newsize = max(1, $settings['max_message_size_pipe'], $settings['max_message_size_pop']);
		legacy_update_setting('max_message_size', $newsize);
		$this->yes();

	}

	/***************************************************
	* Usergroup changes
	***************************************************/

	function step2() {

		global $db, $settings;

		$this->start('Adding companies table');
		$db->query("
			CREATE TABLE `user_company` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `name` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  `p_ticket_company_viewothers` tinyint(1) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();

		$this->start('Adding member company table');
		$db->query("
			CREATE TABLE `user_member_company` (
			  `user` int(10) unsigned NOT NULL,
			  `company` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`user`,`company`)
			)
		");
		$this->yes();

		$this->start('Adding company to groups table');
		$db->query("
			CREATE TABLE `user_company2group` (
			  `companyid` int(10) unsigned NOT NULL,
			  `groupid` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`companyid`,`groupid`)
			)
		");
		$this->yes();

		$this->start('Adding company roles table');
		$db->query("
			CREATE TABLE `user_company_role` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `name` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  `overrides` text NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();

		$this->start('Adding company roles member table');
		$db->query("
			CREATE TABLE `user_member_company_role` (
			  `user` int(10) unsigned NOT NULL,
			  `role` int(10) unsigned NOT NULL,
			  `company` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`user`,`role`,`company`)
			)
		");
		$this->yes();

		$this->start('Adding member groups table');
		$db->query("
			CREATE TABLE `user_member_groups` (
			  `user` int(10) unsigned NOT NULL,
			  `usergroup` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`user`,`usergroup`)
			)
		");
		$this->yes();

		$this->start('Re-creating user groups table');
		$db->query("DROP TABLE IF EXISTS `user_groups`");
		$db->query("
			CREATE TABLE `user_groups` (
				`id` int(10) NOT NULL auto_increment,
				`name` varchar(250) NOT NULL default '',
				`description` text NOT NULL,
				`is_system` tinyint(1) NOT NULL default '0',
				`system_name` varchar(50) NOT NULL,
				`p_kb` tinyint(1) NOT NULL default '0',
				`p_kb_new` tinyint(1) NOT NULL default '0',
				`p_kb_subscribe` tinyint(1) NOT NULL default '0',
				`p_kb_comment` tinyint(1) NOT NULL default '1',
				`p_kb_rate` tinyint(1) NOT NULL default '1',
				`p_dl` tinyint(1) NOT NULL default '0',
				`p_trouble` tinyint(1) NOT NULL default '0',
				`p_trouble_rate` tinyint(1) NOT NULL default '0',
				`p_ticket` tinyint(1) NOT NULL default '0',
				`p_ticket_new` tinyint(1) NOT NULL default '0',
				`p_ticket_new_email` tinyint(1) NOT NULL default '0',
				`p_ticket_rate` tinyint(1) NOT NULL default '0',
				`p_ticket_reopen` tinyint(1) NOT NULL default '0',
				PRIMARY KEY  (`id`)
			)
		");
		$this->yes();

		$this->start('Insert default usergroups');
		$db->query("
			INSERT INTO `user_groups`
			SET
				id = 1,
				name = 'Guests',
				description = 'Guests are users who are not logged in. Every guest will be added to this usergroup for the duration of their stay. Use this usergroup to control access permissions for guests.',
				is_system = 1,
				system_name = 'guest',
				p_kb = ".intval(!$settings['faq_restrict']).",
				p_kb_new = ".intval($settings['faq_new']).",
				p_kb_comment = 1,
				p_kb_rate = ".intval($settings['faq_rating']).",
				p_dl = 1,
				p_trouble = ".intval(!$settings['trouble_restrict']).",
				p_trouble_rate = 1,
				p_ticket = 1,
				p_ticket_new = ".intval(!$settings['require_registration']).",
				p_ticket_new_email = ".intval(!$settings['gateway_require_registration']).",
				p_ticket_reopen = ".intval(($settings['gateway_ticket_reopen'] OR $settings['user_reopen']))."
		");

		$db->query("
			INSERT INTO `user_groups`
			SET
				id = 2,
				name = 'Registered',
				description = 'Users who are fully registered all belong to this usergroup. A user can belong to any amount of additional usergroups, but they will always belong to this one. Use this usergroup to control the base permissions applied to all users.',
				is_system = 1,
				system_name = 'registered',
				p_kb = 1,
				p_kb_new = ".intval($settings['faq_new']).",
				p_kb_subscribe = 1,
				p_kb_comment = 1,
				p_kb_rate = ".intval($settings['faq_rating']).",
				p_dl = 1,
				p_trouble = 1,
				p_trouble_rate = 1,
				p_ticket = 1,
				p_ticket_new = 1,
				p_ticket_new_email = 1,
				p_ticket_reopen = ".intval(($settings['user_reopen'] OR $settings['gateway_ticket_reopen'])).",
				p_ticket_rate = 1
		");
		$this->yes();

	}



	/***************************************************
	* Ticket cats
	***************************************************/

	function step3() {

		global $db, $settings;

		/********************
		* Ticket fields
		********************/

		$this->start('Removing old custom field permissions');
		$db->query("ALTER TABLE `user_def`
			 DROP `tech_viewable`,
			 DROP `tech_editable`;
		");
		$db->query("ALTER TABLE `ticket_def`
			 DROP `tech_viewable`,
			 DROP `tech_editable`;
		");
		$this->yes();

		$this->start('Adding custom field control to category table');
		$db->query("ALTER TABLE `ticket_cat` ADD `custom_inherit` INT( 1 ) NOT NULL ,
		ADD `custom_all` INT( 1 ) NOT NULL ;");
		$this->yes();

		$this->start('Adding permission control to category table');
		$db->query("ALTER TABLE `ticket_cat` ADD `perm_inherit` INT( 1 ) NOT NULL DEFAULT '1'");
		$this->yes();

		$this->start('Creating ticket_def_cat table');
		$db->query("
			CREATE TABLE `ticket_def_cat` (
				`fieldid` int(11) NOT NULL,
				`catid` int(11) NOT NULL,
				PRIMARY KEY  (`fieldid`,`catid`)
			)
		");
		$this->yes();

		$this->start('Updating custom fields for existing categories');
		$db->query("UPDATE ticket_cat SET custom_all = 1 WHERE parent = 0");
		$db->query("UPDATE ticket_cat SET custom_inherit = 1 WHERE parent != 0");
		$this->yes();

		/********************
		* Cat perms
		********************/

		$this->start('Creating ticket_cat_permissions table');
		$db->query("
			CREATE TABLE `ticket_cat_permissions` (
				`usergroup` int(10) unsigned NOT NULL,
				`category` int(11) NOT NULL,
				PRIMARY KEY  (`usergroup`,`category`)
			)
		");
		$this->yes();

		// Set permissions
		$this->start('Updating category permissions for existing categories');

		$categories = (array)$db->query_return_array("SELECT * FROM ticket_cat");

		$cats_all = array();
		$cats_reg = array();
		$cats_none = array();

		foreach ($categories as $cat) {
			// If user selectable and doesnt require reg, then use the 'all' option
			if ($cat['user_select'] AND !$cat['require_registration']) {
				$cats_all[] = $cat['id'];

			// Registered users only
			} else if ($cat['user_select'] AND $cat['require_registration']) {
				$cats_reg[] = $cat['id'];

			// Non-user selectable
			} else {
				$cats_none[] = $cat['id'];
			}
		}

		if ($cats_all) {
			$db->query("
				UPDATE ticket_cat
				SET perm_inherit = 1 WHERE id IN " . array2sql($cats_all)
			);
		}

		if ($cats_reg OR $cats_none) {
			$db->query("
				UPDATE ticket_cat
				SET perm_inherit = 0 WHERE id IN " . array2sql(array_merge($cats_reg, $cats_none))
			);
		}

		if ($cats_reg) {
			foreach ($cats_reg as $catid) {
				$db->query("
					INSERT INTO ticket_cat_permissions
					SET usergroup = 2, category = $catid
				");
			}
		}

		$this->yes();


		$this->start('Removing old ticket category permissions');
		$db->query("
			ALTER TABLE `ticket_cat` DROP `user_select` ,
			DROP `require_registration`
		");
		$this->yes();
	}




	/***************************************************
	* Registration changes
	***************************************************/

	function step4() {

		global $db;

		$this->start('Add temporarytype to ticket_attachments');
		$db->query("
			ALTER TABLE `ticket_attachments` ADD
			`temporarytype` VARCHAR( 25 ) NOT NULL AFTER `temporaryid`
		");
		$this->yes();

		$this->start('Add ticket_temp table');
		$db->query("
			CREATE TABLE `ticket_temp` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `email` varchar(250) NOT NULL,
			  `ticket_data` mediumtext NOT NULL,
			  `message_data` mediumtext NOT NULL,
			  `validate_key` varchar(6) NOT NULL,
			  `timestamp_submitted` int(11) NOT NULL,
			  `timestamp_reminder1` int(11) NOT NULL,
			  `timestamp_reminder2` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();
	}



	/***************************************************
	* Ticket priority permissions
	***************************************************/

	function step5() {

		global $db;

		/********************
		* Table changes
		********************/

		$this->start('Adding ticket_pri_permissions table');
		$db->query("
			CREATE TABLE `ticket_pri_permissions` (
			`usergroup` INT UNSIGNED NOT NULL ,
			`priority` INT UNSIGNED NOT NULL ,
			PRIMARY KEY ( `usergroup` , `priority` )
			)
		");
		$this->yes();

		$this->start('Modifying ticket_pri table');
		$db->query("ALTER TABLE `ticket_pri` ADD `perm_all` INT NOT NULL DEFAULT '1'");
		$this->yes();

		/********************
		* Copy permissions
		********************/

		$this->start('Updating priority permissions for existing priorities');

		$pris_all = array();
		$pris_reg = array();

		$priorities = (array)$db->query_return_array("SELECT * FROM ticket_pri");

		foreach ($priorities as $pri) {
			if ($pri['user_select']) {
				if ($pri['require_registration']) {
					$pris_reg[] = $pri['id'];
				} else {
					$pris_all[] = $pri['id'];
				}
			}
		}

		$db->query("
			UPDATE ticket_pri
			SET perm_all = 0
		");

		if ($pris_all) {
			$db->query("
				UPDATE ticket_pri
				SET perm_all = 1
				WHERE id IN " . array2sql($pris_all)
			);
		}

		if ($pris_reg) {
			foreach ($pris_reg as $priid) {
				$db->query("
					INSERT INTO ticket_pri_permissions
					SET usergroup = 2, priority = $priid
				");
			}
		}

		$this->yes();

		/********************
		* Remove old settings
		********************/

		$this->start('Removing old settings from ticket_pri');
		$db->query("
			ALTER TABLE `ticket_pri` DROP `user_select` ,
			DROP `require_registration`
		");
		$this->yes();
	}


	/***************************************************
	* Custom fileds permissions
	***************************************************/

	function step6() {

		global $db;

		/********************
		* Create new table
		********************/

		$this->start('Adding new custom fields permissions table');
		$db->query("
			CREATE TABLE `def_permissions` (
			  `permid` int(10) unsigned NOT NULL auto_increment,
			  `id` int(10) unsigned NOT NULL,
			  `tablename` varchar(25) NOT NULL,
			  `usergroup` int(10) unsigned NOT NULL,
			  `perm_type` varchar(255) NOT NULL,
			  PRIMARY KEY  (`permid`),
			  KEY `lookup` (`id`,`tablename`)
			)
		");
		$this->yes();


		/********************
		* TICKET FIELDS
		********************/

		$this->start('Copying over old user permissions for ticket fields');

		$def_tickets = (array)$db->query_return_array("
			SELECT id, user_editable, user_viewable, ticket_start
			FROM ticket_def
		");

		$insert_perms = array();

		foreach ($def_tickets as $def) {
			if ($def['user_editable']) {
				$db->query("
					INSERT INTO def_permissions
					SET
						id = $def[id],
						tablename = 'ticket_def',
						usergroup = 0,
						perm_type = 'user_editable'
				");
			}

			if ($def['user_viewable']) {
				$db->query("
					INSERT INTO def_permissions
					SET
						id = $def[id],
						tablename = 'ticket_def',
						usergroup = 0,
						perm_type = 'user_viewable'
				");
			}

			if ($def['ticket_start']) {
				$db->query("
					INSERT INTO def_permissions
					SET
						id = $def[id],
						tablename = 'ticket_def',
						usergroup = 0,
						perm_type = 'ticket_start'
				");
			}
		}

		$this->yes();

		$this->start('Deleting old fields from ticket_def');
		$db->query("
			ALTER TABLE `ticket_def` DROP `user_editable` ,
			DROP `user_viewable` ,
			DROP `ticket_start
		");
		$this->yes();

		/********************
		* USER FIELDS
		********************/

		$this->start('Copying over old user permissions for user fields');

		$def_users = (array)$db->query_return_array("
			SELECT id, user_editable, user_viewable, user_start
			FROM user_def
		");

		$insert_perms = array();

		foreach ($def_users as $def) {
			if ($def['user_editable']) {
				$db->query("
					INSERT INTO def_permissions
					SET
						id = $def[id],
						tablename = 'user_def',
						usergroup = 0,
						perm_type = 'user_editable'
				");
			}

			if ($def['user_viewable']) {
				$db->query("
					INSERT INTO def_permissions
					SET
						id = $def[id],
						tablename = 'user_def',
						usergroup = 0,
						perm_type = 'user_viewable'
				");
			}

			// Ticket start only applies to
			// guests, so make usergroup 1
			if ($def['ticket_start']) {
				$db->query("
					INSERT INTO def_permissions
					SET
						id = $def[id],
						tablename = 'user_def',
						usergroup = 1,
						perm_type = 'user_start'
				");
			}
		}

		$this->yes();

		$this->start('Deleting old fields from user_def');
		$db->query("
			ALTER TABLE `user_def` DROP `user_editable` ,
			DROP `user_viewable` ,
			DROP `user_start`
		");
		$this->yes();

		/********************
		* FAQ FIELDS
		********************/

		$this->start('Copying over old user permissions for faq fields');

		$def_users = (array)$db->query_return_array("
			SELECT id, user_viewable, user_start
			FROM faq_def
		");

		$insert_perms = array();

		foreach ($def_users as $def) {
			if ($def['user_editable']) {
				$db->query("
					INSERT INTO def_permissions
					SET
						id = $def[id],
						tablename = 'faq_def',
						usergroup = 0,
						perm_type = 'user_editable'
				");
			}

			if ($def['ticket_start']) {
				$db->query("
					INSERT INTO def_permissions
					SET
						id = $def[id],
						tablename = 'faq_def',
						usergroup = 0,
						perm_type = 'user_start'
				");
			}
		}

		$this->yes();

		$this->start('Deleting old fields from faq_def');
		$db->query("
			ALTER TABLE `faq_def` DROP `user_viewable` ,
			DROP `user_start`
		");
		$this->yes();


	}

	/***************************************************
	* Ticket rules, KB resolve
	***************************************************/

	function step7() {

		global $db;

		$this->start('Adding field to plugins table');
		$db->query("ALTER TABLE `plugins` ADD `url` VARCHAR( 250 ) NOT NULL ;");
		$this->yes();

		$this->start('Adding fields to ticket_rules mail table (Part 1)');
		$db->query("ALTER TABLE `ticket_rules_mail`
	 	 DROP `template_tech_reply`,
	 	 DROP `template_user_new`,
	 	 DROP `template_user_reply`,
	 	 DROP `template_tech_reply_word`,
	 	 DROP `template_user_new_word`,
		  DROP `template_user_reply_word`");
		$this->yes();

		$this->start('Adding fields to ticket_rules mail table (Part 2)');
	 	 $db->query("ALTER TABLE `ticket_rules_mail` ADD `template_new_user` VARCHAR( 250 ) NOT NULL ,
		ADD `template_reply_user` VARCHAR( 250 ) NOT NULL ,
		ADD `template_reply_tech` VARCHAR( 250 ) NOT NULL ,
		ADD `template_permission` VARCHAR( 250 ) NOT NULL");
	 	$this->yes();

	 	$this->start('Adding fields to ticket_rules mail table (Part 3)');
		$db->query("ALTER TABLE `ticket_rules_mail` ADD `template_new_user_phrase_on` INT( 1 ) NOT NULL ,
		ADD `template_reply_user_phrase_on` INT( 1 ) NOT NULL ,
		ADD `template_reply_tech_phrase_on` INT( 1 ) NOT NULL ,
		ADD `template_permission_phrase_on` INT( 1 ) NOT NULL ,
		ADD `template_new_user_phrase` MEDIUMTEXT NOT NULL ,
		ADD `template_reply_user_phrase` MEDIUMTEXT NOT NULL ,
		ADD `template_reply_tech_phrase` MEDIUMTEXT NOT NULL ,
		ADD `template_permission_phrase` MEDIUMTEXT NOT NULL");
		$this->yes();

		$this->start('Adding fields to ticket_rules mail table (Part 4)');
		$db->query("ALTER TABLE `ticket_rules_mail` ADD `guest_handling_method` VARCHAR( 250 ) NOT NULL ,
		ADD `email_validate` VARCHAR( 250 ) NOT NULL ,
		ADD `tech_validate` VARCHAR( 250 ) NOT NULL");
		$this->yes();

		$this->start('Adding new faq_article_resolve table');
		$db->query(query_engine_replace("
			CREATE TABLE `faq_article_resolve` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `articleid` int(10) unsigned NOT NULL,
			  `userid` int(10) unsigned NOT NULL,
			  `question_subject` text NOT NULL,
			  `question_message` text NOT NULL,
			  `timestamp_submitted` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  FULLTEXT KEY `search` (`question_subject`,`question_message`)
			) ENGINE=MyISAM
		"));
		$this->yes();

		$this->start('Altering tech table');
		$db->query("ALTER TABLE `tech` DROP `display_faq_advanced` ");
		$this->yes();
	}

	/***************************************************
	* Fulltext index on articles
	***************************************************/

	function step8() {

		global $db, $settings;

		// Make sure it is a MyISAM table for the fulltext
		$this->start('Checking if faq_articles table is using MyISAM storage engine');

		$tinfo = $db->query_return("SHOW TABLE STATUS LIKE 'faq_articles'");

		if ($tinfo) {

			$this->yes();

			if (strtolower($tinfo['Engine']) != 'myisam') {
				$this->start('Converting table engine to MyISAM');

				$db->query(query_engine_replace("ALTER TABLE `faq_articles` ENGINE=MyISAM"));

				$this->yes();
			}
		}

		$add_indexes = array(
			array('search', 'FULLTEXT', array('title', 'question', 'answer'))
		);

		$this->start('Adding fulltext index to `faq_articles`');
		add_table_indexes('faq_articles', $add_indexes);
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
$upgrade = new upgrade_3010001();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>