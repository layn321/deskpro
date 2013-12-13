<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: index.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | -
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_v2 extends upgrade_base_v2 {

	var $version = '3.0.0 Alpha 1';

	var $version_number = 1;

	var $pages = array(
		array('v2.0.0 - v2.0.1', 'database.gif'),
		array('v2.0.0 - v2.0.1', 'database.gif'),
		array('Private Messages', 'database.gif'),
		array('Attachments', 'database.gif'),
		array('Banned Emails', 'database.gif'),
		array('Unused Tables', 'database.gif'),
		array('Recreate Tables', 'database.gif'),
		array('New Tables', 'database.gif'),
		array('Ticket Table', 'database.gif'),
		array('Tech Table', 'database.gif'),
		array('Table Changes', 'database.gif'),
		array('Email Gateway', 'database.gif'),
		array('Ticket Message', 'database.gif'),
		array('Gateway Source', 'database.gif'),
		array('Calendar', 'database.gif'),
		array('User Index', 'database.gif'),
		array('User Table', 'database.gif'),
		array('Settings Table', 'database.gif'),
		array('Ticket Log', 'database.gif'),
		array('Custom Fields', 'database.gif'),
		array('Data', 'database.gif'),
		array('Message Sourceid', 'database.gif'),
		array('Indexes', 'database.gif')
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db, $db2, $settings;

		$this->start('Check/Perform Upgrade to ' . DP_NAME . ' v2.0.1');

		$queries[] = "ALTER TABLE `gateway_error` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `gateway_error` ADD INDEX (`id`)";
		$queries[] = "ALTER TABLE `gateway_error` CHANGE `id` `id` INT( 10 ) DEFAULT '0' NOT NULL AUTO_INCREMENT";
		$queries[] = "ALTER TABLE `gateway_error` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` ) ";
		$queries[] = "ALTER TABLE `gateway_error` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `escalate` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` ) ";
		$queries[] = "ALTER TABLE `languages` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` ) ";
		$queries[] = "ALTER TABLE `user_help_cats` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` ) ";
		$queries[] = "ALTER TABLE `ticket_notes` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` ) ";
		$queries[] = "ALTER TABLE `tech_ticket_save` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` ) ";
		$queries[] = "ALTER TABLE `ticket_fielddisplay` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` ) ";
		$queries[] = "ALTER TABLE `blobs ` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `calendar_task` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `faq_articles` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `faq_attachments` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `faq_cats` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `faq_comments` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `faq_searchlog` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `faq_searchlog` DROP INDEX `id_2`";
		$queries[] = "ALTER TABLE `faq_word` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `ticket_rules_mail` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `gateway_spam` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `gateway_spam` DROP INDEX `id_2`";
		$queries[] = "ALTER TABLE `news` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `quickreply` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `quickreply_cat` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `report` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `report_stat` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `report_style` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `search` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `search` DROP INDEX `techid_2`";
		$queries[] = "ALTER TABLE `settings` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `settings_cat` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `tech` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `tech_attachments` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `tech_news` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `tech_sendmail` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `tech_ticket_watch` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `template_cat` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `template_email` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `template_replace` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `ticket_attachments` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `ticket_cat` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `ticket_def` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `ticket_log` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `ticket_message` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `ticket_pri` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `user` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `user_def` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `user_notes` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `user_help_cats` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `tech_ticket_save` DROP INDEX `id`";
		$queries[] = "ALTER TABLE `ticket` DROP INDEX `id`";
		$queries[] = "ALTER TABLE template_words ADD cust INT(1) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE calendar_task ADD timezone_dst int(1) NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE calendar_task ADD timezone int(10) NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE user ADD name varchar(250) NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE user ADD timezone INT(10) NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE user ADD timezone_dst INT(1) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE calendar_task ADD weekstart int(11) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE faq_articles ADD views int(10) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE faq_comments ADD new int(1) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE languages ADD isocode varchar(250) NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE languages ADD contenttype varchar(250) NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE languages ADD direction enum('ltr', 'rtl') default 'ltr' NOT NULL";
		$queries[] = "ALTER TABLE settings ADD options varchar(250)  NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE tech ADD email_faq int(1) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE tech ADD userfield_selection mediumtext NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE tech ADD timezone varchar(32)  NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE tech ADD p_html_tech int(1) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE tech ADD p_html_user int(1) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE tech ADD timezone_dst int(11) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE user ADD timezone varchar(32) NOT NULL DEFAULT ''";
		$queries[] = "ALTER TABLE user ADD weekstart int(11) NOT NULL DEFAULT '0'";
		$queries[] = "ALTER TABLE `settings` CHANGE category category int(10) NOT NULL default '0'";
		$queries[] = "ALTER TABLE `pm_relations` CHANGE `pmid` `pmid` INT( 10 ) DEFAULT '0' NOT NULL";
		$queries[] = "ALTER TABLE `pm_relations` DROP INDEX `pmid`";
		$queries[] = "ALTER TABLE `pm_relations` DROP INDEX techid";
		$queries[] = "ALTER TABLE `pm_relations` DROP PRIMARY KEY , ADD PRIMARY KEY ( `pmid`, `techid` ) ";
		$queries[] = "ALTER TABLE quickreply DROP global";
		$queries[] = "ALTER TABLE settings DROP `int`";
		$queries[] = "ALTER TABLE settings DROP `type`";
		$queries[] = "ALTER TABLE tech DROP faq_editor_yes";
		$queries[] = "ALTER TABLE tech DROP faq_editor_no";
		$queries[] = "ALTER TABLE ticket DROP date_reply";
		$queries[] = "ALTER TABLE ticket DROP closed_by";
		$queries[] = "DROP TABLE settingcat";
		$queries[] = "DROP TABLE ticket_message_source";
		$queries[] = "DROP TABLE faq_wordindex";
		$queries[] = "DROP TABLE spellwords";
		$queries[] = "DROP TABLE tech_cat";
		$queries[] = "DROP TABLE template_backup";
		$queries[] = "DROP TABLE templatecat";
		$queries[] = "
			CREATE TABLE tech_sendmail (
			id int(10) NOT NULL auto_increment,
			parent int(10) NOT NULL default '0',
			techid int(10) NOT NULL default '0',
			subject varchar(250) NOT NULL default '',
			message mediumtext NOT NULL,
			from_email varchar(250) NOT NULL default '',
			to_email varchar(250) NOT NULL default '',
			tracking int(1) NOT NULL default '0',
			awaiting_reply int(1) NOT NULL default '0',
			pass varchar(7) NOT NULL default '',
			date_sent int(10) NOT NULL default '0',
			PRIMARY KEY  (id),
			UNIQUE KEY id (id)
		)
		";
		$queries[] = "ALTER TABLE `tech_ticket_watch` DROP INDEX techid";
		$queries[] = "ALTER TABLE `tech_ticket_watch` ADD INDEX (`techid`,`created`)";
		$queries[] = "ALTER TABLE `ticket_def` DROP INDEX name";
		$queries[] = "ALTER TABLE `user` DROP INDEX id_2";
		$queries[] = "ALTER TABLE `ticket_cat` DROP INDEX user_view";
		$queries[] = "ALTER TABLE `template_email` DROP INDEX name";
		$queries[] = "ALTER TABLE `template_email` ADD INDEX (`name`)";
		$queries[] = "ALTER TABLE `template` DROP INDEX name";
		$queries[] = "ALTER TABLE `template` ADD INDEX (`name`)";
		$queries[] = "ALTER TABLE `faq_cats_related` DROP INDEX show_cat";
		$queries[] = "ALTER TABLE `faq_cats_related` ADD INDEX (`show_cat`)";
		$queries[] = "ALTER TABLE `faq_comments` DROP INDEX new";
		$queries[] = "ALTER TABLE `faq_comments` ADD INDEX (`new`)";
		$queries[] = "ALTER TABLE `faq_comments` DROP INDEX articleid";
		$queries[] = "ALTER TABLE `faq_comments` ADD INDEX (`articleid`)";
		$queries[] = "ALTER TABLE `quickreply` DROP INDEX techid";
		$queries[] = "ALTER TABLE `quickreply` ADD INDEX (`techid`)";
		$queries[] = "ALTER TABLE `tech_attachments` DROP INDEX techid";
		$queries[] = "ALTER TABLE `tech_attachments` ADD INDEX (`techid`)";
		$queries[] = "ALTER TABLE `tech_attachments` DROP INDEX category";
		$queries[] = "ALTER TABLE `tech_attachments` ADD INDEX (`category`)";
		$queries[] = "ALTER TABLE `tech_email` DROP INDEX fieldname";
		$queries[] = "ALTER TABLE `tech_help_entry` DROP INDEX category";
		$queries[] = "ALTER TABLE `tech_help_entry` ADD INDEX (`category`)";
		$queries[] = "ALTER TABLE `tech_notes` DROP INDEX category";
		$queries[] = "ALTER TABLE `tech_notes` DROP INDEX techid";
		$queries[] = "ALTER TABLE `tech_notes` ADD INDEX (`techid`,`category`) ";
		$queries[] = "ALTER TABLE `tech_email` DROP INDEX techid";
		$queries[] = "ALTER TABLE `tech_email` ADD INDEX techid";
		$queries[] = "ALTER TABLE `tech_bookmarks` DROP INDEX category";
		$queries[] = "ALTER TABLE `tech_bookmarks` DROP INDEX techid";
		$queries[] = "ALTER TABLE `tech_bookmarks` ADD INDEX (`techid`,`category`) ";
		$queries[] = "ALTER TABLE `tech_session` DROP INDEX techid";
		$queries[] = "ALTER TABLE `tech_session` ADD INDEX (`techid`) ";
		$queries[] = "ALTER TABLE `faq_articles` DROP INDEX ref ";
		$queries[] = "ALTER TABLE `faq_articles` ADD UNIQUE (`ref`) ";
		$queries[] = "ALTER TABLE `tech_ticket_search` DROP INDEX techid";
		$queries[] = "ALTER TABLE `tech_ticket_search` ADD INDEX (`techid`) ";
		$queries[] = "ALTER TABLE `template_words` DROP INDEX language";
		$queries[] = "ALTER TABLE `template_words` ADD INDEX (`language`) ";
		$queries[] = "ALTER TABLE `template_words` DROP INDEX category";
		$queries[] = "ALTER TABLE `template_words` DROP INDEX wordref";
		$queries[] = "ALTER TABLE `ticket` DROP INDEX timestamp_opened";
		$queries[] = "ALTER TABLE `ticket` DROP INDEX date_opened";
		$queries[] = "ALTER TABLE `ticket` ADD INDEX (`timestamp_opened`) ";
		$queries[] = "ALTER TABLE `ticket_log` DROP INDEX techid";
		$queries[] = "ALTER TABLE `ticket_log` ADD INDEX (`techid`) ";
		$queries[] = "ALTER TABLE `ticket_message` DROP INDEX date";
		$queries[] = "ALTER TABLE `user` DROP INDEX username";
		$queries[] = "ALTER TABLE `user` ADD INDEX (`username`) ";
		$queries[] = "ALTER TABLE `user` DROP INDEX email";
		$queries[] = "ALTER TABLE `user` ADD INDEX (`email`) ";
		$queries[] = "ALTER TABLE `user` DROP INDEX awaiting_register_validate_tech";
		$queries[] = "ALTER TABLE `user` ADD INDEX (`awaiting_register_validate_tech`) ";
		$queries[] = "ALTER TABLE `user_bill` DROP INDEX ticketid";
		$queries[] = "ALTER TABLE `user_bill` DROP INDEX userid";
		$queries[] = "ALTER TABLE `user_bill` ADD INDEX (`ticketid`, `userid`) ";
		$queries[] = "ALTER TABLE `user_notes` DROP INDEX userid";
		$queries[] = "ALTER TABLE `user_notes` ADD INDEX (`userid`, `techid`) ";
		$queries[] = "ALTER TABLE `user_session` DROP INDEX userid";
		$queries[] = "ALTER TABLE `user_session` ADD INDEX (`userid`) ";
		$queries[] = "ALTER TABLE calendar_task CHANGE timezone timezone int(10) NOT NULL default '0'";
		$queries[] = "ALTER TABLE calendar_task CHANGE weekstart weekstart int(10) NOT NULL default '0'";
		$queries[] = "ALTER TABLE calendar_task CHANGE timezone_dst timezone_dst int(1) NOT NULL default '0'";
		$queries[] = "ALTER TABLE gateway_pop_accounts CHANGE target target varchar(64) NOT NULL default 'user'";
		$queries[] = "ALTER TABLE tech CHANGE timezone timezone int(10) NOT NULL default '0'";
		$queries[] = "ALTER TABLE tech CHANGE weekstart weekstart int(10) NOT NULL default '0'";
		$queries[] = "ALTER TABLE tech CHANGE timezone_dst timezone_dst int(1) NOT NULL default '0'";
		$queries[] = "ALTER TABLE user CHANGE timezone timezone int(10) NOT NULL default '0'";
		$queries[] = "ALTER TABLE user CHANGE timezone_dst timezone_dst int(1) NOT NULL default '0'";
		$queries[] = "ALTER TABLE template_email CHANGE language language int(10) NOT NULL default '0'";

		foreach ($queries AS $query) {
			$db->query_silent_extra($query);
		}

		$this->yes();

	}

	/***************************************************
	* - Upgrade to v2.0.1
	***************************************************/

	function step2() {

	global $db, $db2, $settings;

		$this->start("Checking Tickets");

		$refs = array();
		$db->query("SELECT ref, COUNT(*) AS total FROM ticket GROUP BY ref HAVING total >= 2");
		while ($result = $db->row_array()) {
			$refs[] = $result['ref'];
		}

		// create a new temp column to indicate which need updating
		if ($db->num_rows()) {

			$db2->query("ALTER TABLE ticket ADD ref_temp int(1) NOT NULL DEFAULT '0'");

			foreach ($refs AS $key => $var) {

				$db->query("
					SELECT id, userid
					FROM ticket
					WHERE ref = '" . addslashes($var) . "'
					ORDER BY id ASC
				");

				while ($result = $db->row_array()) {

					// first ticket?
					if ($i) {
						$db2->query("UPDATE ticket SET ref_temp = '1' WHERE id = '$result[id]'");
					} else {
						$db2->query("UPDATE ticket SET ref_temp = '2' WHERE id = '$result[id]'");
					}
					$i++;
				}
			}

			$db->query("SELECT id FROM ticket WHERE ref_temp = '1'");
			while ($result = $db->row_array()) {
				$ids[] = $result['id'];
			}

			$db->query("SELECT id FROM ticket WHERE ref_temp = '2'");
			while ($result = $db->row_array()) {
				$ids_2[] = $result['id'];
			}

			foreach ($ids AS $key => $var) {

				$authcode = substr(md5(rand(0,100000) . mktime()), 0, 8);

				$db2->query("
					UPDATE ticket SET
						ref = '" . make_ticket_ref() . "',
						authcode = '" . $db->escape($authcode) . "'
					WHERE id = '$var'
				");
			}

			foreach ($ids_2 AS $key => $var) {

				$authcode = substr(md5(rand(0,100000) . mktime()), 0, 8);

				$db2->query("
					UPDATE ticket SET
						authcode = '" . $db->escape($authcode) . "'
					WHERE id = '$var'
				");
			}

			$db->query("ALTER TABLE ticket DROP ref_temp");
		}

		$this->yes();

		$this->start("Creating ticket index");

		$db->query("ALTER TABLE `ticket` DROP INDEX `ref` ");
		$db->query("ALTER TABLE `ticket` ADD UNIQUE (`ref`)");

		$this->yes();

		$this->start("Checking Ticket Auth Code");

		$db->query("SELECT id FROM ticket WHERE authcode = ''");
		if ($db->num_rows()) {
			while ($res = $db->row_array()) {
				$process[] = $res['id'];
			}

			foreach($process AS $id) {
				$authcode = substr(md5(rand(0,100000) . mktime()), 0, 8);
				$db->query("UPDATE ticket SET authcode = '" . $db->escape($authcode) . "' WHERE id = '$id'");
			}
		}

		$this->yes();

	}

	function step3() {

	global $db, $db2, $settings;

		$db->query_silent_extra("DROP TABLE manual_comments");
		$db->query_silent_extra("DROP TABLE manual_manuals");
		$db->query_silent_extra("DROP TABLE manual_pages");
		$db->query_silent_extra("DROP TABLE manual_revisions");
		$db->query_silent_extra("DROP TABLE manual_searchlog");

		$db->query_silent_extra("ALTER TABLE calendar_task DROP timezone_dst");

		$this->start('Creating new Private Message Tables');

		$db->query("
		CREATE TABLE `tech_pms` (
		  `id` int(11) NOT NULL auto_increment,
		  `fromid` int(10) NOT NULL default '0',
		  `toid` int(10) NOT NULL default '0',
		  `is_read` int(1) NOT NULL default '0',
		  `title` varchar(250)  NOT NULL default '',
		  `message` mediumtext  NOT NULL,
		  `timestamp` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `toid` (`toid`)
		)
		");

		$this->yes();

		$this->start('Importing Private Messages');

		$db->query("
			SELECT pm_source.*, pm_relations.*
			FROM pm_source
			LEFT JOIN pm_relations ON (pm_source.id = pm_relations.pmid)
		");

		while ($result = $db->row_array()) {

			$db2->query("
				INSERT INTO tech_pms SET
					toid = " . intval($result['toid']) . ",
					fromid = " . intval($result['fromid']) . ",
					is_read = " . intval($result['is_read']) . ",
					title = '" . $db->escape($result['title']) . "',
					message = '" . $db->escape($result['title']) . "',
					timestamp = " . intval($result['timestamp']) . "
			");
		}

		$this->yes();

		$this->start('Deleting old Private Message Tables');

		$db->query("DROP TABLE `pm_source`");
		$db->query("DROP TABLE `pm_relations`");

		$this->yes();

	}

	/***************************************************
	* - Fixing Blobs
	***************************************************/

	function step4() {

		global $db, $db2, $settings;

		$this->start('Changing Blobs to Blobs Parts Table');

		$db->query("RENAME TABLE blobs TO blob_parts");

		$db->query("
			ALTER TABLE `blob_parts`
			CHANGE `id` `blobid` INT( 10 ) NOT NULL DEFAULT '0',
			ADD displayorder int (10) DEFAULT '0',
			DROP PRIMARY KEY,
			ADD INDEX (blobid)
		");

		$this->yes();

		$this->start('Creating Blobs Table');

		$db->query("
			CREATE TABLE `blobs` (
		  	`id` int(10) unsigned NOT NULL auto_increment,
		  	`thumbnail` mediumblob NOT NULL,
			PRIMARY KEY  (`id`)
			)
		");

		$this->yes();

		$this->start('Creating Blob Data');

		$results = $db->query_return_array("
			SELECT blobid FROM blob_parts
		");

		if (is_array($results)) {
			foreach ($results AS $result) {
				$db->query("INSERT INTO blobs SET id = " . intval($result['blobid']));
			}
		}

		$this->yes();

	}

	/***************************************************
	* - Fixing Banned Emails
	***************************************************/

	function step5() {

		global $db, $db2, $settings;

		$this->start('Creating Banned Emails Table');

		$db->query("
			CREATE TABLE `ban_email` (
			  `email` varchar(250)  NOT NULL default '',
			  `tech` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`email`)
			)
		");

		$this->yes();

		$result = $db->query_return("
			SELECT * FROM data WHERE name = 'email_ban'
		");

		$emails = unserialize($result['data']);

		if (is_array($emails)) {

			foreach ($emails AS $key => $email) {
				$emails[$key] = strtolower($email);
			}

			$emails = array_unique($emails);

			$this->start('Inserting Emails into Database');

			foreach ($emails AS $key => $var) {

				if (is_email($var)) {
					$db->query("INSERT INTO ban_email SET email = '" . $db->escape($var) . "'");
				} else {
					$new_email_bans[] = $var;
				}
			}

			$this->yes();
		}

		$this->start('Updating Old Email Ban Data Store');

		update_data('email_ban', $new_email_bans);

		$this->yes();

	}

	/***************************************************
	* - Dropping Tables
	***************************************************/

	function step6() {

		global $db, $db2, $settings;

		$queries[] = "DROP TABLE `admin_help_cat`";
		$queries[] = "DROP TABLE `admin_help_entry`";
		$queries[] = "DROP TABLE `faq_word`";
		$queries[] = "DROP TABLE `gateway_auto";
		$queries[] = "DROP TABLE `report_style";
		$queries[] = "DROP TABLE `tech_internal_help_cat`";
		$queries[] = "DROP TABLE `tech_internal_help_entry`";
		$queries[] = "DROP TABLE `tech_timelog_archive`";
		$queries[] = "DROP TABLE `template_email`";

		$this->start('Dropping Unused Database Tables');

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();

		$db->query_silent_extra("DROP TABLE plugin_manual_articles");
		$db->query_silent_extra("DROP TABLE plugin_manual_entries");
		$db->query_silent_extra("DROP TABLE plugin_manual_manuals");
		$db->query_silent_extra("DROP TABLE plugin_manual_styles");

		$this->start('Updating Knowledgebase Articles');

		function dp_code($text, $no_ent = 0) {

			// prevent long words
			$text = do_wordwrap($text);

			// remove html
			if (!$no_ent) {
				$text = strip_tags($text, '<a><b><i><u>');
			}

			// turn links into html
			$text = eregi_replace("([ \t]|^)www\.", " http://www.", $text);
			$text = eregi_replace("([ \t]|^)ftp\.", " ftp://ftp.", $text);
			$text = eregi_replace("(http://[^ )\r\n]+)", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $text);
			$text = eregi_replace("(https://[^ )\r\n]+)", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $text);
			$text = eregi_replace("(ftp://[^ )\r\n]+)", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $text);
			$text = eregi_replace("([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))", "<a href=\"mailto:\\1\">\\1</a>", $text);

			// line breaks
			$text = nl2br($text);

			return $text;
		}

		$db->query("SELECT * FROM faq_articles WHERE question_html = 0 OR answer_html = 0");
		while ($result = $db->row_array()) {

			if (!$result['answer_html']) {
				$result['answer'] = dp_code($result['answer']);
			}
			if (!$result['question_html']) {
				$result['question'] = dp_code($result['question']);
			}

			$db2->query("
				UPDATE faq_articles SET
					question = '" . $db->escape($result['question']) . "',
					answer = '" . $db->escape($result['answer']) . "'
				WHERE id = $result[id]
			");
		}

		$this->yes();

	}

	function step7() {

		global $db, $db2, $settings;

		$this->start('Re Creating Tables');

		$queries[] = "DROP TABLE `faq_searchlog`";

		$queries[] = "CREATE TABLE `faq_searchlog` (
		  `id` int(10) NOT NULL auto_increment,
		  `timestamp` int(10) NOT NULL default '0',
		  `query` mediumtext  NOT NULL,
		  `results` mediumtext  NOT NULL,
		  `total` int(10) NOT NULL default '0',
		  `searchwords` varchar(250)  NOT NULL default '',
		  `sessionid` varchar(32)  NOT NULL default '0',
		  `solved_refs` mediumtext  NOT NULL,
		  `userid` int(10) NOT NULL default '0',
		  `category` mediumtext  NOT NULL,
		  `extra` mediumtext  NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `searchwords` (`searchwords`)
		)
		";

		$queries[] = "DROP TABLE `user_session`";

		$queries[] = "CREATE TABLE `user_session` (
		  `sessionid` varchar(32) NOT NULL default '',
		  `userid` int(10) unsigned NOT NULL default '0',
		  `useragent` varchar(250) NOT NULL default '',
		  `lastactivity` int(10) unsigned NOT NULL default '0',
		  `language` int(10) NOT NULL default '0',
		  `host` varchar(15) NOT NULL default '',
		  `path` varchar(250) NOT NULL default '',
		  `pagetype` varchar(250) NOT NULL default '',
		  `pagevalue` varchar(250) NOT NULL default '',
		  `style` int(10) NOT NULL default '0',
		  `extra` mediumtext NOT NULL,
		  PRIMARY KEY  (`sessionid`),
		  KEY `userid` (`userid`)
		)
		";

		$queries[] = "DROP TABLE `user_help`";

		$queries[] = "CREATE TABLE `user_help` (
		  `category` varchar(250) NOT NULL default '0',
		  `name` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL default '0',
		  `is_custom` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`name`)
		)
		";

		$queries[] = "DROP TABLE `user_help_cats`";

		$queries[] = "CREATE TABLE `user_help_cats` (
		  `displayorder` int(10) NOT NULL default '0',
		  `is_custom` int(1) NOT NULL default '0',
		  `name` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`name`)
		)
		";

		$queries[] = "DROP TABLE `user_help_cats_entries`";

		$queries[] = "CREATE TABLE `user_help_cats_entries` (
		  `catname` varchar(250) NOT NULL default '',
		  `language` int(10) NOT NULL default '0',
		  `entry` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`language`,`catname`)
		)
		";

		$queries[] = "DROP TABLE `user_help_entries`";

		$queries[] = "CREATE TABLE `user_help_entries` (
		  `language` int(10) NOT NULL default '0',
		  `title` varchar(250) NOT NULL default '',
		  `helpentry` mediumtext NOT NULL,
		  `helpname` varchar(250) NOT NULL default '',
		  `changed` int(1) NOT NULL default '0',
		  `backup` mediumtext NOT NULL,
		  PRIMARY KEY  (`language`,`helpname`)
		)
		";

		$queries[] = "DROP TABLE `template_words`";

		$queries[] = "CREATE TABLE `template_words` (
		  `wordref` varchar(50) NOT NULL default '',
		  `language` int(10) NOT NULL default '0',
		  `text` mediumtext NOT NULL,
		  `cust` int(1) NOT NULL default '0',
		  `backuptext` mediumtext NOT NULL,
		  PRIMARY KEY  (`language`,`wordref`)
		)
		";

		$queries[] = "DROP TABLE `template_words_cat`";

		$queries[] = "CREATE TABLE `template_words_cat` (
		  `intname` varchar(250) NOT NULL default '',
		  `name` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL default '0'
		)
		";

		$queries[] = "DROP TABLE `template_replace`";

		$queries[] = "CREATE TABLE `template_replace` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `match_string` varchar(75) NOT NULL default '',
		  `templateset` int(1) NOT NULL default '0',
		  `replace_string` mediumtext NOT NULL,
		  `evaluate` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		)
		";

		$queries[] = "DROP TABLE `languages`";

		$queries[] = "CREATE TABLE `languages` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `is_selectable` int(1) NOT NULL default '0',
		  `isocode` varchar(250) NOT NULL default '',
		  `contenttype` varchar(250) NOT NULL default '',
		  `direction` enum('ltr','rtl') NOT NULL default 'ltr',
		  `deskproid` varchar(20) NOT NULL default '',
		  `version` varchar(5) NOT NULL default '',
		  `base` int(1) NOT NULL default '0',
		  `credits` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`)
		)
		";

		$queries[] = "DROP TABLE `template`";

		$queries[] = "CREATE TABLE `template` (
		  `name` varchar(250) NOT NULL default '',
		  `template` mediumtext NOT NULL,
		  `templateset` int(1) NOT NULL default '0',
		  `category` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `upgraded` int(1) NOT NULL default '0',
		  `changed` int(1) NOT NULL default '0',
		  `custom` int(1) NOT NULL default '0',
		  `version_upgrade` int(1) NOT NULL default '0',
		  `template_unparsed` mediumtext NOT NULL,
		  `backup` mediumtext NOT NULL,
		  UNIQUE KEY `name` (`name`,`templateset`)
		)
		";

		$queries[] = "DROP TABLE `template_cat`";

		$queries[] = "CREATE TABLE `template_cat` (
		  `intname` varchar(250) NOT NULL default '',
		  `name` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `displayorder` int(10) NOT NULL default '0',
		  `custom` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`intname`)
		)
		";

		$queries[] = "DROP TABLE `tech_session`";

		$queries[] = "CREATE TABLE `tech_session` (
		  `sessionid` varchar(32) NOT NULL default '',
		  `techid` int(10) unsigned NOT NULL default '0',
		  `useragent` varchar(250) NOT NULL default '',
		  `lastactivity` int(10) unsigned NOT NULL default '0',
		  `techzone` int(1) default '0',
		  `path` varchar(250) NOT NULL default '',
		  `host` varchar(20) NOT NULL default '',
		  `firstactivity` int(10) NOT NULL default '0',
		  `extra` mediumtext NOT NULL,
		  PRIMARY KEY  (`sessionid`),
		  KEY `techid` (`techid`)
		)
		";

		$queries[] = "DROP TABLE `tech_help_cat`";

		$queries[] = "CREATE TABLE `tech_help_cat` (
		  `id` int(10) NOT NULL auto_increment,
		  `title` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		)
		";

		$queries[] = "DROP TABLE `tech_help_entry`";

		$queries[] = "CREATE TABLE `tech_help_entry` (
		  `id` int(10) NOT NULL auto_increment,
		  `category` int(10) NOT NULL default '0',
		  `entry` mediumtext NOT NULL,
		  `displayorder` int(10) NOT NULL default '0',
		  `title` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  KEY `category` (`category`)
		)
		";

		$queries[] = "DROP TABLE `search`";

		$queries[] = "CREATE TABLE `search` (
		  `id` int(10) NOT NULL auto_increment,
		  `results` mediumtext NOT NULL,
		  `timestamp` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `total` int(10) NOT NULL default '0',
		  `searchvalues` mediumtext NOT NULL,
		  `total_page` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `techid` (`techid`)
		)
		";

		$queries[] = "DROP TABLE `gateway_error`";

		$queries[] = "CREATE TABLE `gateway_error` (
		  `id` int(10) NOT NULL auto_increment,
		  `error` varchar(250) NOT NULL default '',
		  `timestamp` int(10) NOT NULL default '0',
		  `sourceid` int(10) NOT NULL default '0',
		  `subject` varchar(250) NOT NULL default '',
		  `email` varchar(250) NOT NULL default '',
		  `gateway` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  KEY `error` (`error`)
		)
		";

		$queries[] = "DROP TABLE `gateway_pop_failures`";

		$queries[] = "CREATE TABLE `gateway_pop_failures` (
		  `id` int(11) NOT NULL auto_increment,
		  `accountid` int(10) NOT NULL default '0',
		  `timestamp` int(11) default NULL,
		  `error_id` int(10) NOT NULL default '0',
		  `error_int_id` int(10) NOT NULL default '0',
		  `error_text` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`)
		)
		";

		$queries[] = "DROP TABLE `settings_cat`";

		$queries[] = "CREATE TABLE `settings_cat` (
		  `name` varchar(250) NOT NULL default '',
		  `display_name` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `displayorder` int(10) default '0',
		  `parent` varchar(250) NOT NULL default '',
		  `custom` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`name`)
		)
		";

		$queries[] = "DROP TABLE `report_stat`";

		$queries[] = "CREATE TABLE `report_stat` (
		  `id` int(10) NOT NULL auto_increment,
		  `title` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `variable1` varchar(250) NOT NULL default '',
		  `variable2` varchar(250) NOT NULL default '',
		  `fixed_general` mediumtext NOT NULL,
		  `fixed_user` mediumtext NOT NULL,
		  `fixed_ticket` mediumtext NOT NULL,
		  `datefield` varchar(250) NOT NULL default '',
		  `generate_frequency` int(1) NOT NULL default '0',
		  `generate_pie` int(1) NOT NULL default '0',
		  `generate_bar` int(1) NOT NULL default '0',
		  `generate_combined_frequency` int(1) NOT NULL default '0',
		  `generate_combined_bar` int(1) NOT NULL default '0',
		  `generate_split_frequency` int(1) NOT NULL default '0',
		  `generate_split_pie` int(1) NOT NULL default '0',
		  `generate_split_bar` int(1) NOT NULL default '0',
		  `generate_ticketlist` int(1) NOT NULL default '0',
		  `ticketlist_fields` mediumtext NOT NULL,
		  `ref` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL,
		  `variable1_time` varchar(250) NOT NULL,
		  `variable2_time` varchar(250) NOT NULL,
		  PRIMARY KEY  (`id`)
		)
		";

		$queries[] = "DROP TABLE `report`";

		$queries[] = "CREATE TABLE `report` (
		  `id` int(10) NOT NULL auto_increment,
		  `ref` varchar(250) NOT NULL,
		  `name` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `lastrun` int(10) NOT NULL default '0',
		  `format` varchar(250) NOT NULL default '',
		  `email` varchar(250) NOT NULL default '',
		  `repeattype` varchar(250) NOT NULL default '',
		  `value1` varchar(250) NOT NULL default '',
		  `value2` varchar(250) NOT NULL default '',
		  `path` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		)
		";

		$queries[] = "DROP TABLE `query_log`";

		$queries[] = "CREATE TABLE `query_log` (
		  `id` int(11) NOT NULL auto_increment,
		  `query` mediumtext,
		  `explain_log` mediumtext,
		  `duration` decimal(15,10) default NULL,
		  `stamp` int(11) default NULL,
		  `filename` varchar(250) NOT NULL default '',
		  `keytype` varchar(250) NOT NULL default '',
		  `matches` int(10) NOT NULL default '0',
		  `slow1` int(1) NOT NULL default '0',
		  `slow2` int(1) NOT NULL default '0',
		  `slow3` int(1) NOT NULL default '0',
		  `slowmatches` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `duration` (`duration`),
		  KEY `stamp` (`stamp`),
		  KEY `matches` (`matches`)
		)
		";

		$queries[] = "DROP TABLE `cron_options`";

		$queries[] = "CREATE TABLE `cron_options` (
		  `id` int(10) NOT NULL auto_increment,
		  `title` varchar(250) NOT NULL default '',
		  `scriptname` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `nextrun` int(10) NOT NULL default '0',
		  `frequency` varchar(250) NOT NULL default '',
		  `custom` int(1) NOT NULL default '0',
		  `lastrun` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `scriptname` (`scriptname`)
		)
		";

		$queries[] = "DROP TABLE `escalate`";

		$queries[] = "CREATE TABLE `escalate` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `time_type` varchar(250) NOT NULL default '0',
		  `time_amount` int(10) NOT NULL default '0',
		  `criteria_category` int(10) NOT NULL default '0',
		  `criteria_priority` int(10) NOT NULL default '0',
		  `criteria_tech` int(10) NOT NULL default '0',
		  `actions_email_owner` int(1) NOT NULL default '0',
		  `actions_pm_owner` int(1) NOT NULL default '0',
		  `actions_email_techs` mediumtext NOT NULL,
		  `actions_pm_techs` mediumtext NOT NULL,
		  `actions_category` int(1) NOT NULL default '0',
		  `actions_priority` int(1) NOT NULL default '0',
		  `actions_tech` int(1) NOT NULL default '0',
		  `repeat_this` int(1) NOT NULL default '0',
		  `repeat_other` int(1) NOT NULL default '0',
		  `criteria_workflow` int(1) NOT NULL default '0',
		  `actions_workflow` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		)
		";

		$queries[] = "DROP TABLE `tech_timelog`";

		$queries[] = "CREATE TABLE `tech_timelog` (
		  `id` int(10) NOT NULL auto_increment,
		  `techid` int(10) NOT NULL default '0',
		  `startstamp` int(10) NOT NULL default '0',
		  `endstamp` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		)
		";

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();

	}

	/***************************************************
	* - Creating Tables
	***************************************************/

	function step8() {

		global $db, $db2, $settings;

		$queries[] = array(
			'description' => 		'Creating table admin_session',
			'query' => "

			CREATE TABLE `admin_session` (
			  `sessionid` varchar(32)  NOT NULL default '',
			  `adminid` int(10) unsigned NOT NULL default '0',
			  `useragent` varchar(250)  NOT NULL default '',
			  `lastactivity` int(10) unsigned NOT NULL default '0',
			  `path` varchar(250)  NOT NULL default '',
			  `host` varchar(20)  NOT NULL default '',
			  `firstactivity` int(10) NOT NULL default '0',
			  `extra` mediumtext  NOT NULL,
			  PRIMARY KEY  (`sessionid`),
			  KEY `techid` (`adminid`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table calendar_def',
			'query' => "

			CREATE TABLE `calendar_def` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `name` varchar(250)  default NULL,
			  `display_name` mediumtext  NOT NULL,
			  `description` mediumtext ,
			  `formtype` enum('input','select','textarea','multiselect','radio','checkbox','system')  NOT NULL default 'select',
			  `default_value` varchar(250)  default NULL,
			  `parsed_default_value` varchar(250)  default NULL,
			  `data` mediumtext  NOT NULL,
			  `extrainput` int(1) NOT NULL default '0',
			  `maxoptions` smallint(4) NOT NULL default '0',
			  `minoptions` smallint(4) NOT NULL default '0',
			  `maxlength` smallint(6) NOT NULL default '0',
			  `minlength` smallint(6) NOT NULL default '0',
			  `regex` varchar(250)  default NULL,
			  `error_message` varchar(250)  default NULL,
			  `required` int(1) NOT NULL default '0',
			  `displayorder` int(10) NOT NULL default '0',
			  `multiselect` int(1) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table cron_log',
			'query' => "

			CREATE TABLE `cron_log` (
			  `id` int(10) NOT NULL auto_increment,
			  `scriptid` int(10) NOT NULL default '0',
			  `timestamp` int(10) NOT NULL default '0',
			  `overdue` int(10) NOT NULL default '0',
			  `logtext` mediumtext  NOT NULL,
			  `logdetail` mediumtext  NOT NULL,
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table deskpro_help_glossary',
			'query' => "

			CREATE TABLE `deskpro_help_glossary` (
			  `id` int(10) NOT NULL auto_increment,
			  `word` varchar(250)  NOT NULL default '',
			  `content` mediumtext  NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `word` (`word`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table deskpro_help_tech_articles',
			'query' => "

			CREATE TABLE `deskpro_help_tech_articles` (
			  `id` int(10) NOT NULL auto_increment,
			  `intname` varchar(250)  NOT NULL default '',
			  `title` varchar(250)  NOT NULL default '',
			  `content` mediumtext  NOT NULL,
			  `category` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `intname` (`intname`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table deskpro_help_tech_cats',
			'query' => "

			CREATE TABLE `deskpro_help_tech_cats` (
			  `id` int(10) NOT NULL auto_increment,
			  `intname` varchar(250)  NOT NULL default '',
			  `displayorder` int(10) NOT NULL default '0',
			  `title` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table deskpro_help_tooltip',
			'query' => "

			CREATE TABLE `deskpro_help_tooltip` (
			  `id` int(10) NOT NULL auto_increment,
			  `section` varchar(250)  NOT NULL default '',
			  `tips` mediumtext  NOT NULL,
			  `mainhelp` mediumtext  NOT NULL,
			  `maintitle` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `section` (`section`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table dev_errors',
			'query' => "

			CREATE TABLE `dev_errors` (
			  `id` int(10) NOT NULL auto_increment,
			  `error` mediumtext  NOT NULL,
			  `variables` mediumtext  NOT NULL,
			  `filename` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table email_send',
			'query' => "

			CREATE TABLE `email_send` (
			  `id` int(10) NOT NULL auto_increment,
			  `from_email` varchar(250)  NOT NULL default '',
			  `userid` int(10) NOT NULL default '0',
			  `subject` mediumtext  NOT NULL,
			  `message` mediumtext  NOT NULL,
			  `timestamp_startsend` int(10) NOT NULL default '0',
			  `timestamp_activity` int(10) NOT NULL default '0',
			  `timestamp_completed` int(10) NOT NULL default '0',
			  `total` int(10) NOT NULL default '0',
			  `timestamp_created` int(10) NOT NULL default '0',
			  `from_name` varchar(250)  NOT NULL default '',
			  `ref_name` varchar(250)  NOT NULL default '',
			  `ref_description` mediumtext  NOT NULL,
			  `log_message` int(1) NOT NULL default '0',
			  `total_sent` int(10) NOT NULL default '0',
			  `tech_aborted` int(1) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table email_send_log',
			'query' => "

			CREATE TABLE `email_send_log` (
			  `id` int(10) NOT NULL auto_increment,
			  `userid` int(10) NOT NULL default '0',
			  `emailid` int(10) NOT NULL default '0',
			  `timestamp` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `userid` (`userid`),
			  KEY `emailid` (`emailid`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table email_send_pending',
			'query' => "

			CREATE TABLE `email_send_pending` (
			  `id` int(10) NOT NULL auto_increment,
			  `emailid` int(10) NOT NULL default '0',
			  `userids` mediumtext  NOT NULL,
			  `number` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table error_log',
			'query' => "

			CREATE TABLE `error_log` (
			  `id` int(10) NOT NULL auto_increment,
			  `timestamp` int(10) NOT NULL default '0',
			  `type` varchar(250)  NOT NULL default '',
			  `details` mediumtext  NOT NULL,
			  `summary` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  KEY `type` (`type`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table escalate_log',
			'query' => "

			CREATE TABLE `escalate_log` (
			  `id` int(10) NOT NULL auto_increment,
			  `timestamp` int(10) NOT NULL default '0',
			  `ticketid` int(10) NOT NULL default '0',
			  `escalateid` int(10) NOT NULL default '0',
			  `timestamp_criteria` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `ticketid` (`ticketid`,`timestamp_criteria`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table faq_def',
			'query' => "

			CREATE TABLE `faq_def` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `name` mediumtext ,
			  `display_name` varchar(250)  NOT NULL default '',
			  `description` mediumtext ,
			  `formtype` enum('input','select','textarea','multiselect','radio','checkbox','system')  NOT NULL default 'select',
			  `data` mediumtext  NOT NULL,
			  `extrainput` int(1) NOT NULL default '0',
			  `maxoptions` smallint(4) NOT NULL default '0',
			  `minoptions` smallint(4) NOT NULL default '0',
			  `maxlength` smallint(6) NOT NULL default '0',
			  `minlength` smallint(6) NOT NULL default '0',
			  `displayorder` int(10) NOT NULL default '0',
			  `multiselect` int(1) NOT NULL default '0',
			  `display_name_language` mediumtext  NOT NULL,
			  `description_language` mediumtext  NOT NULL,
			  `user_viewable` int(1) NOT NULL default '0',
			  `user_start` int(1) NOT NULL default '0',
			  `tech_start` int(1) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table faq_keywords_articles',
			'query' => "

			CREATE TABLE `faq_keywords_articles` (
			  `articleid` int(10) NOT NULL default '0',
			  `wordid` int(10) NOT NULL default '0',
			  UNIQUE KEY `wordid` (`wordid`,`articleid`),
			  KEY `articleid` (`articleid`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table faq_keywords_words',
			'query' => "

			CREATE TABLE `faq_keywords_words` (
			  `wordid` int(250) NOT NULL auto_increment,
			  `word` varchar(50)  NOT NULL default '',
			  PRIMARY KEY  (`wordid`),
			  UNIQUE KEY `word` (`word`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table faq_searchlog_solved',
			'query' => "

			CREATE TABLE `faq_searchlog_solved` (
			  `id` int(10) NOT NULL auto_increment,
			  `articleid` int(10) NOT NULL default '0',
			  `searchid` int(10) NOT NULL default '0',
			  `userid` int(10) NOT NULL default '0',
			  `sessionid` varchar(32)  NOT NULL default '',
			  `solved` int(1) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `articleid` (`articleid`,`searchid`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table files',
			'query' => "

			CREATE TABLE `files` (
			  `id` int(10) NOT NULL auto_increment,
			  `blobid` int(10) NOT NULL default '0',
			  `faq_attach_id` int(10) NOT NULL default '0',
			  `faq_id` int(10) NOT NULL default '0',
			  `filename` varchar(250)  NOT NULL default '',
			  `description` mediumtext  NOT NULL,
			  `category` int(10) NOT NULL default '0',
			  `downloads` int(10) NOT NULL default '0',
			  `filesize` varchar(250)  NOT NULL default '',
			  `extension` varchar(250)  NOT NULL default '0',
			  `timestamp` int(10) NOT NULL default '0',
			  `techid` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table files_cats',
			'query' => "

			CREATE TABLE `files_cats` (
			  `id` int(10) NOT NULL auto_increment,
			  `name` varchar(250)  NOT NULL default '',
			  `description` mediumtext  NOT NULL,
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table files_permissions',
			'query' => "

			CREATE TABLE `files_permissions` (
			  `catid` int(10) NOT NULL default '0',
			  `groupid` int(10) NOT NULL default '0',
			  UNIQUE KEY `groupid` (`groupid`,`catid`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table gateway_emails',
			'query' => "

			CREATE TABLE `gateway_emails` (
			  `id` int(10) NOT NULL auto_increment,
			  `name` varchar(250)  NOT NULL default '',
			  `email` varchar(250)  NOT NULL default '',
			  `is_default` int(1) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table images',
		'query' => "

			CREATE TABLE `images` (
			  `id` int(10) NOT NULL auto_increment,
			  `blobid` int(10) NOT NULL default '0',
			  `filename` varchar(250)  NOT NULL default '',
			  `filesize` int(10) NOT NULL default '0',
			  `extension` varchar(50)  NOT NULL default '',
			  `content_type` varchar(250)  NOT NULL default '',
			  `content_id` int(10) NOT NULL default '0',
			  `tempkey` varchar(50)  NOT NULL default '',
			  `timestamp` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `tempkey` (`tempkey`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table style',
		'query' => "

			CREATE TABLE `style` (
			  `id` int(10) NOT NULL auto_increment,
			  `name` varchar(250)  NOT NULL default '',
			  `images` varchar(250)  NOT NULL default '',
			  `templateset` int(10) NOT NULL default '0',
			  `header` mediumtext  NOT NULL,
			  `header_include` mediumtext  NOT NULL,
			  `footer` mediumtext  NOT NULL,
			  `header_unparsed` mediumtext  NOT NULL,
			  `header_include_unparsed` mediumtext  NOT NULL,
			  `footer_unparsed` mediumtext  NOT NULL,
			  `css` mediumtext  NOT NULL,
			  `css_rtl` mediumtext NOT NULL,
			  `extracss` mediumtext  NOT NULL,
			  `cssstyle` int(10) NOT NULL default '0',
			  `active` int(1) NOT NULL default '0',
			  `is_default` int(1) NOT NULL default '0',
			  `elements` mediumtext  NOT NULL,
			  `ref` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table tech_forum_forum',
		'query' => "

			CREATE TABLE `tech_forum_forum` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `title` varchar(250)  NOT NULL default '',
			  `description` mediumtext  NOT NULL,
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table tech_forum_message',
		'query' => "

			CREATE TABLE `tech_forum_message` (
			  `id` int(10) NOT NULL auto_increment,
			  `techid` int(10) NOT NULL default '0',
			  `topicid` int(10) NOT NULL default '0',
			  `message` varchar(250)  NOT NULL default '',
			  `timestamp` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `topicid` (`topicid`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table tech_forum_topic',
		'query' => "

			CREATE TABLE `tech_forum_topic` (
			  `id` int(10) NOT NULL auto_increment,
			  `techid` int(10) NOT NULL default '0',
			  `techid_lastreply` int(10) NOT NULL default '0',
			  `created_timestamp` int(10) NOT NULL default '0',
			  `message_timestamp` varchar(10)  NOT NULL default '',
			  `title` varchar(250)  NOT NULL default '',
			  `forumid` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `forumid` (`forumid`),
			  KEY `message_timestamp` (`message_timestamp`),
			  FULLTEXT KEY `title` (`title`)
			) TYPE=MyISAM

		");

		$queries[] = array(
		'description' => 		'Creating table tech_forum_view',
		'query' => "

			CREATE TABLE `tech_forum_view` (
			  `id` int(10) NOT NULL auto_increment,
			  `topicid` int(10) NOT NULL default '0',
			  `techid` int(10) NOT NULL default '0',
			  `timestamp` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `topicid` (`topicid`,`techid`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table tech_ips',
		'query' => "

			CREATE TABLE `tech_ips` (
			  `id` int(10) NOT NULL auto_increment,
			  `techid` int(10) NOT NULL default '0',
			  `ip` varchar(250)  NOT NULL default '',
			  `timestamp` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `techid` (`techid`,`ip`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table tech_news_read',
		'query' => "

			CREATE TABLE `tech_news_read` (
			  `techid` int(10) NOT NULL default '0',
			  `newsid` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`techid`,`newsid`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table tech_token',
		'query' => "

			CREATE TABLE `tech_token` (
			  `id` int(10) NOT NULL auto_increment,
			  `techid` int(10) NOT NULL default '0',
			  `timestamp` int(10) NOT NULL default '0',
			  `token_type` varchar(250)  NOT NULL default '',
			  `token_value` varchar(20)  NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  KEY `timestamp` (`timestamp`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table template_set',
		'query' => "

			CREATE TABLE `template_set` (
			  `id` int(10) NOT NULL auto_increment,
			  `name` varchar(250)  NOT NULL default '',
			  `parent` int(10) NOT NULL default '0',
			  `ref` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table template_stylesheets',
		'query' => "

			CREATE TABLE `template_stylesheets` (
			  `id` int(10) NOT NULL auto_increment,
			  `name` varchar(250)  NOT NULL default '',
			  `stylesheet` mediumtext  NOT NULL,
			  `editor` mediumtext  NOT NULL,
			  `ref` varchar(250)  NOT NULL default '',
			  `stylesheet_rtl` mediumtext NOT NULL,
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table template_tech_email',
		'query' => "

			CREATE TABLE `template_tech_email` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `name` varchar(250)  NOT NULL default '',
			  `template` mediumtext ,
			  `description` varchar(250)  NOT NULL default '',
			  `upgraded` int(1) NOT NULL default '0',
			  `changed` int(1) NOT NULL default '0',
			  `custom` int(1) NOT NULL default '0',
			  `version_upgrade` int(1) NOT NULL default '0',
			  `template_unparsed` mediumtext  NOT NULL,
			  `subject` varchar(250)  NOT NULL default '',
			  `backup_template` mediumtext  NOT NULL,
			  `backup_subject` varchar(250)  NOT NULL default '',
			  `subject_unparsed` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `name` (`name`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table template_user_email',
		'query' => "

			CREATE TABLE `template_user_email` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `name` varchar(250)  NOT NULL default '',
			  `template` mediumtext ,
			  `description` varchar(250)  NOT NULL default '',
			  `upgraded` int(1) NOT NULL default '0',
			  `changed` int(1) NOT NULL default '0',
			  `custom` int(1) NOT NULL default '0',
			  `version_upgrade` int(1) NOT NULL default '0',
			  `template_unparsed` mediumtext  NOT NULL,
			  `subject` varchar(250)  NOT NULL default '',
			  `backup_template` mediumtext  NOT NULL,
			  `backup_subject` varchar(250)  NOT NULL default '',
			  `emailtype` varchar(250)  NOT NULL default '',
			  `subject_unparsed` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `name` (`name`)
			)

		");

		$queries[] = array(
		'description' => 		'Creating table template_words_cat_link',
		'query' => "

			CREATE TABLE `template_words_cat_link` (
			  `wordref` varchar(250)  NOT NULL default '',
			  `category` varchar(250)  NOT NULL default '0',
			  PRIMARY KEY  (`wordref`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table ticket_delete_log',
			'query' => "

			CREATE TABLE `ticket_delete_log` (
			  `id` int(10) NOT NULL auto_increment,
			  `techid` int(10) NOT NULL default '0',
			  `timestamp` int(10) NOT NULL default '0',
			  `reason` mediumtext  NOT NULL,
			  `ticketref` varchar(250)  NOT NULL default '',
			  `subject` varchar(250)  NOT NULL default '',
			  `ticketid` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `ticketref` (`ticketref`),
			  KEY `ticketid` (`ticketid`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table ticket_rules_mail',
			'query' => "

			CREATE TABLE `ticket_rules_mail` (
			  `id` int(10) NOT NULL auto_increment,
			  `auto_reply` int(1) NOT NULL default '0',
			  `auto_new` int(1) NOT NULL default '0',
			  `is_default` int(1) NOT NULL default '0',
			  `displayorder` int(10) NOT NULL default '0',
			  `actions` mediumtext  NOT NULL,
			  `criteria` mediumtext  NOT NULL,
			  `template_tech_reply` int(10) NOT NULL default '0',
			  `template_user_new` int(10) NOT NULL default '0',
			  `template_user_reply` int(10) NOT NULL default '0',
			  `template_tech_reply_word` varchar(250)  NOT NULL default '',
			  `template_user_new_word` varchar(250)  NOT NULL default '',
			  `template_user_reply_word` varchar(250)  NOT NULL default '',
			  `accountid` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table ticket_rules_web',
			'query' => "

			CREATE TABLE `ticket_rules_web` (
			  `id` int(10) NOT NULL auto_increment,
			  `auto_reply` int(1) NOT NULL default '0',
			  `auto_new` int(1) NOT NULL default '0',
			  `is_default` int(1) NOT NULL default '0',
			  `displayorder` int(10) NOT NULL default '0',
			  `criteria` mediumtext  NOT NULL,
			  `actions` mediumtext  NOT NULL,
			  `accountid` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table ticket_views',
			'query' => "

			CREATE TABLE `ticket_views` (
			  `id` int(10) NOT NULL auto_increment,
			  `techid` int(10) NOT NULL default '0',
			  `content` mediumtext  NOT NULL,
			  `isglobal` int(1) NOT NULL default '0',
			  `tickets` int(10) NOT NULL default '0',
			  `description` mediumtext  NOT NULL,
			  `name` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table ticket_workflow',
			'query' => "

			CREATE TABLE `ticket_workflow` (
			  `id` int(10) NOT NULL auto_increment,
			  `name` varchar(250)  NOT NULL default '',
			  `displayorder` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table trouble',
			'query' => "

			CREATE TABLE `trouble` (
			  `id` int(10) NOT NULL auto_increment,
			  `description` mediumtext  NOT NULL,
			  `displayorder` int(10) NOT NULL default '0',
			  `techid` int(10) NOT NULL default '0',
			  `timestamp_created` int(10) NOT NULL default '0',
			  `timestamp_updated` int(10) NOT NULL default '0',
			  `publish` int(1) NOT NULL default '0',
			  `auth` varchar(50)  NOT NULL default '',
			  `content` mediumtext  NOT NULL,
			  `end_good` mediumtext  NOT NULL,
			  `end_bad` mediumtext  NOT NULL,
			  `name` varchar(250)  NOT NULL default '',
			  `choices_title` varchar(250)  NOT NULL default '',
			  PRIMARY KEY  (`id`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table trouble_comments',
			'query' => "

			CREATE TABLE `trouble_comments` (
			  `id` int(10) NOT NULL auto_increment,
			  `userid` int(10) NOT NULL default '0',
			  `troubleid` int(10) NOT NULL default '0',
			  `comments` mediumtext  NOT NULL,
			  `session` varchar(32)  NOT NULL default '',
			  `timestamp` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `troubleid` (`troubleid`),
			  KEY `userid` (`userid`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table trouble_permissions',
			'query' => "

			CREATE TABLE `trouble_permissions` (
			  `troubleid` int(10) NOT NULL default '0',
			  `groupid` int(10) NOT NULL default '0',
			  UNIQUE KEY `groupid` (`groupid`,`troubleid`)
			)

		");

			$queries[] = array(
			'description' => 		'Creating table trouble_questions',
			'query' => "

			CREATE TABLE `trouble_questions` (
			  `id` int(10) NOT NULL auto_increment,
			  `question` mediumtext  NOT NULL,
			  `troubleid` int(10) NOT NULL default '0',
			  `parent` int(10) NOT NULL default '0',
			  `answer` mediumtext  NOT NULL,
			  `end` int(1) NOT NULL default '0',
			  `title` mediumtext  NOT NULL,
			  `success` int(1) NOT NULL default '0',
			  `failure` int(1) NOT NULL default '0',
			  `choices_title` text  NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `troubleid` (`troubleid`)
			)

		");

		$queries[] = array(
			'description' => 		'Creating table trouble_rating',
			'query' => "

			CREATE TABLE `trouble_rating` (
			  `id` int(10) NOT NULL auto_increment,
			  `troubleid` int(10) NOT NULL default '0',
			  `rating` int(1) NOT NULL default '0',
			  `userid` int(10) NOT NULL default '0',
			  `sessionid` varchar(32)  NOT NULL default '',
			  `ipaddress` varchar(20)  NOT NULL default '',
			  `timestamp` int(10) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `troubleid` (`troubleid`)
		)

		");

		// run the queries
		foreach ($queries AS $key => $var) {

			$this->start($var['description']);
			$db->query($var['query']);
			$this->yes();

		}
	}

	/***************************************************
	* - Fixing Ticket Table
	***************************************************/

	function step9() {

		global $db, $db2, $settings;

		// Make sure it is a MyISAM table for the fulltext
		$this->start('Checking if ticket table is using the MyISAM storage engine');

		$tinfo = $db->query_return("SHOW TABLE STATUS LIKE 'ticket'");

		if ($tinfo) {
			$this->yes();

			if (strtolower($tinfo['Engine']) != 'myisam') {
				$this->start('Converting table engine to MyISAM');

				$db->query("ALTER TABLE `ticket` TYPE=MyISAM");

				$this->yes();
			}
		}

		$this->start('Updating Ticket Table');

		$db->query("
			ALTER TABLE `ticket`
			CHANGE `date_opened` `timestamp_opened` INT( 10 ) NOT NULL default '0',
			CHANGE `date_closed` `timestamp_closed` INT( 10 ) NOT NULL default '0',
			CHANGE `date_lastreply` `timestamp_lastreply` INT( 10 ) NOT NULL default '0',
			CHANGE `date_lastreply_tech` `timestamp_lastreply_tech` INT( 10 ) NOT NULL default '0',
			CHANGE `date_locked` `timestamp_locked` INT( 10 ) NOT NULL default '0',
			ADD `closed_techid` INT(10) NOT NULL default '0',
			ADD `closed_user` INT(10) NOT NULL default '0',
			ADD `timestamp_lastreply_user` INT(10) NOT NULL default '0',
			ADD `rating` INT(1) NOT NULL default '0',
			ADD `timestamp_rating` INT(10) NOT NULL default '0',
			ADD `workflow` INT(10) NOT NULL default '0',
			ADD timestamp_user_waiting INT(10) NOT NULL default '0',
			ADD total_user_waiting INT(10) NOT NULL default '0',
			ADD timestamp_tech_waiting INT(10) NOT NULL default '0',
			ADD timestamp_first_tech_reply INT(10) NOT NULL default '0',
			CHANGE gatewayid accountid INT(10) NOT NULL default '0',
			ADD auto_reply INT(1) NOT NULL default '0',
			ADD auto_new INT(1) NOT NULL default '0',
			ADD `creation` ENUM( 'web', 'gateway', 'tech', 'split') NOT NULL default 'web',
			ADD `status` ENUM('awaiting_tech','awaiting_user','closed','nodisplay') NOT NULL default 'awaiting_tech',
			CHANGE `nodisplay` `nodisplay2` enum('spam','validate_user','validate_tech') default NULL,
			ADD `nodisplay` enum('spam','validate_user','validate_tech') default NULL,
			DROP `ticketemail`,
			CHANGE `email` `ticketemail` varchar(250)  NOT NULL default '',
			ADD INDEX (timestamp_opened),
			ADD INDEX (timestamp_closed),
			ADD INDEX (status, category),
			ADD INDEX (tech, status),
			ADD FULLTEXT INDEX (subject),
			ADD INDEX (is_locked)
		");

		// our version did not have this
		$db->query_silent_extra("DROP INDEX date_opened");
		$db->query_silent_extra("DROP INDEX techid ON ticket");

		$this->yes();

		$this->start('Updatings Tickets with new Status Options');

		// set status
		$db->query("
			UPDATE ticket
			SET status = 'awaiting_tech'
			WHERE is_open = 1 AND awaiting_tech = 1
		");

		$db->query("
			UPDATE ticket
			SET status = 'awaiting_user'
			WHERE is_open = 1 AND awaiting_tech = 0
		");

		$db->query("
			UPDATE ticket
			SET status = 'closed'
			WHERE is_open = 0
		");

		$this->yes();

		$this->start('Updatings Tickets with new Hidden Options');

		$db->query("
			UPDATE ticket
			SET status = 'nodisplay'
			WHERE nodisplay2 != 0
		");

		// set nodisplay
		$db->query("
			UPDATE ticket
			SET nodisplay = 'spam'
			WHERE nodisplay2 = 1
		");

		$db->query("
			UPDATE ticket
			SET nodisplay = 'validate_user'
			WHERE nodisplay2 = 2
		");

		$db->query("
			UPDATE ticket
			SET nodisplay = 'validate_tech'
			WHERE nodisplay2 = 3
		");

		$this->yes();

		$this->start('Settings User & Tech Waiting Time');

		$db->query("
			UPDATE ticket SET
				timestamp_user_waiting = date_awaiting_toggled
			WHERE status = 'awaiting_tech'
		");

		$db->query("
			UPDATE ticket SET
				timestamp_tech_waiting = date_awaiting_toggled
			WHERE status = 'awaiting_user'
		");

		$db->query("
			UPDATE ticket SET
				timestamp_tech_waiting = 0
			WHERE status = 'awaiting_tech' OR status = 'closed'
		");

		$db->query("
			UPDATE ticket SET
				timestamp_user_waiting = 0
			WHERE status = 'awaiting_user' OR status = 'closed'
		");

		$this->yes();

		$this->start('Removing Unused Columns');

		$db->query("
			ALTER TABLE `ticket`
			DROP nodisplay2,
			DROP awaiting_tech,
			DROP is_open,
			DROP date_awaiting_toggled
		");

		$this->yes();

	}

	/***************************************************
	* - Handling Techs
	***************************************************/

	function step10() {

		global $db, $db2, $settings;

		$this->start('Altering Tech Table');

		$db->query("
			SELECT * FROM tech
		");

		$emails = array();
		$usernames = array();

		$i = 0;

		while ($result = $db->row_array()) {

			if (in_array($result['email'], $emails)) {

				$i++;

				$db2->query("
					UPDATE tech SET
						email = '" . $i . "@domain_duplicate.com'
					WHERE id = $result[id]
				");

				$unique_email = true;

			}

			if (in_array($result['username'], $usernames)) {

				$i++;

				$db2->query("
					UPDATE tech SET
						username = '" . $i . "username_duplicate.com'
					WHERE id = $result[id]
				");

				$unique_username = true;

			}

			$emails[] = $result['email'];
			$username[] = $result['username'];

		}

		$db->query("
			ALTER TABLE `tech`
			ADD `front_select` INT(1) NOT NULL default '0',
			ADD `p_tech_reply` INT(1) NOT NULL default '0',
			ADD `display_faq_advanced` INT(1) NOT NULL default '0',
			ADD `ticketview_faq` INT(1) NOT NULL default '0',
			ADD `ticketview_messages` INT(10) NOT NULL default '0',
			CHANGE `disabled` `active` INT(1) NOT NULL default '0',
			ADD `defaultview` INT(10) NOT NULL default '0',
			ADD `read_walkthrough` INT(1) NOT NULL default '0',
			ADD `front_count` INT(10) NOT NULL default '0',
			ADD `forum_expire` INT(10) NOT NULL default '0',
			ADD `searchresults` INT(10) NOT NULL default '20',
			ADD `p_publish_k` INT(1) NOT NULL default '0',
			ADD `p_quickedit_cats` INT(1) NOT NULL default '0',
			ADD `p_forum_newforum` INT(1) NOT NULL default '0',
			ADD `p_forum_newtopic` INT(1) NOT NULL default '0',
			ADD `p_forum_replytopic` INT(1) NOT NULL default '0',
			ADD `p_unlock` INT(1) NOT NULL default '0',
			ADD `p_change_email` INT(1) NOT NULL default '0',
			ADD `p_change_signature` INT(1) NOT NULL default '0',
			ADD `p_change_password` INT(1) NOT NULL default '0',
			ADD `p_ticket_filters` INT(1) NOT NULL default '0',
			ADD `p_ticket_views` INT(1) NOT NULL default '0',
			ADD `p_add_t` INT(1) NOT NULL default '0',
			ADD `p_publish_t` INT(1) NOT NULL default '0',
			ADD `p_edit_t` INT(1) NOT NULL default '0',
			ADD `p_delete_t` INT(1) NOT NULL default '0',
			ADD `p_add_f` INT(1) NOT NULL default '0',
			ADD `p_add_c_f` INT(1) NOT NULL default '0',
			ADD `p_delete_f` INT(1) NOT NULL default '0',
			ADD `p_delete_c_f` INT(1) NOT NULL default '0',
			ADD `p_start_ticket` INT(1) NOT NULL default '0',
			ADD `p_add_technews` INT(1) NOT NULL default '0',
			CHANGE p_user_expire p_user_expire INT(1) NOT NULL default '0',
			ADD `p_edit_technews` INT(1) NOT NULL default '0',
			ADD `p_delete_technews` INT(1) NOT NULL default '0',
			ADD `p_ticketlog` INT(1) NOT NULL default '0',
			ADD `p_unassigned_view` INT(1) NOT NULL default '0',
			ADD `p_open_ticket` INT(1) NOT NULL default '0',
			ADD `forum_email_newtopics` INT(1) NOT NULL default '0',
			ADD `forum_email_newmessages` INT(1) NOT NULL default '0',
			ADD `forum_email_mynewmessages` INT(1) NOT NULL default '0',
			ADD `email_tech_reply` INT(1) NOT NULL default '0',
			ADD `email_user_registered` INT(1) NOT NULL default '0',
			ADD `email_user_registered_validation` INT(1) NOT NULL default '0',
			ADD `frames` INT(1) NOT NULL default '0',
			ADD `rsspassword` VARCHAR(20) NOT NULL default '',
			ADD `pagewidth` VARCHAR(250) NOT NULL default '',
			ADD `front_own_1` VARCHAR(250) NOT NULL default '',
			ADD `front_own_2` VARCHAR(250) NOT NULL default '',
			ADD `front_own_3` VARCHAR(250) NOT NULL default '',
			ADD `front_new_1` VARCHAR(250) NOT NULL default '',
			ADD `front_new_2` VARCHAR(250) NOT NULL default '',
			ADD `front_new_3` VARCHAR(250) NOT NULL default '',
			ADD `front_other_1` VARCHAR(250) NOT NULL default '',
			ADD `front_other_2` VARCHAR(250) NOT NULL default '',
			ADD `front_other_3` VARCHAR(250) NOT NULL default '',
			CHANGE `disabled_reason` `disabled_reason` VARCHAR(250) NOT NULL default '',
			CHANGE `timezone` `timezone` VARCHAR(4) NOT NULL default '',
			CHANGE `cats_admin` `cats_admin` MEDIUMTEXT NOT NULL default '',
			CHANGE `cats_user` `cats_user` MEDIUMTEXT NOT NULL default '',
			CHANGE `footer` `footer` enum('ticket_search','ticket_select','saved_searches','users','links','ticket_control') NOT NULL default 'ticket_search',
			CHANGE `alert_sound` `alert_sound` VARCHAR(250) NOT NULL default '',
			ADD UNIQUE INDEX (username),
			ADD UNIQUE INDEX (email),
			DROP fielddisplay,
			DROP selected_sound,
			DROP p_html_tech,
			DROP p_html_user
		");

		$this->yes();

		$this->start('Setting Default Tech Data');

		$db->query("
			UPDATE tech SET timezone = '' WHERE timezone = 0
		");

		// convert disabled -> active
		$db->query("
			SELECT active, id FROM tech
		");
		while ($result = $db->row_array()) {

			if ($result['active'] == 1) {
				$db2->query("UPDATE tech SET active = 0 WHERE id = $result[id]");
			} else {
				$db2->query("UPDATE tech SET active = 1 WHERE id = $result[id]");
			}
		}

		$db->query("
			UPDATE tech SET
				display_faq_advanced = 1,
				cats_user = '',
				cats_admin = '',
				ticketview_faq	= 1,
				pagewidth = '100%',
				active = 1,
				p_publish_k = 1,
				p_quickedit_cats = 1,
				p_forum_newforum = 1,
				p_forum_newtopic = 1,
				p_forum_replytopic = 1,
				p_change_email = 1,
				p_change_signature = 1,
				p_change_password = 1,
				p_ticket_filters = 1,
				p_ticket_views = 1,
				p_quickedit_cats = 1,
				p_forum_newforum = 1,
				p_forum_newtopic = 1,
				p_forum_replytopic = 1,
				p_change_email = 1,
				p_change_signature = 1,
				p_change_password = 1,
				p_ticket_filters = 1,
				p_ticket_views = 1,
				p_add_t = 1,
				p_publish_t = 1,
				p_edit_t = 1,
				p_delete_t = 1,
				p_add_f = 1,
				p_add_c_f = 1,
				p_delete_f = 1,
				p_delete_c_f = 1,
				p_start_ticket = 1,
				p_add_technews = 1,
				p_edit_technews = 1,
				p_delete_technews = 1,
				p_ticketlog = 1,
				p_unassigned_view = 1,
				p_open_ticket = 1,
				forum_email_newtopics = 1,
				forum_email_newmessages = 1,
				forum_email_mynewmessages = 1,
				email_user_registered_validation = 1,
				frames = 1,
				p_unlock = 1,
				p_tech_reply = 1
		");

		$db->query("
			SELECT id FROM tech
		");

		// give rss password
		while ($result = $db->row_array()) {

			$db2->query("
				UPDATE tech
					SET rsspassword = '" . $db->escape(make_randomstring(20)) . "'
				WHERE id = $result[id]
			");
		}

		$this->yes();

	}

	/***************************************************
	* - Handling some other tables
	***************************************************/

	function step11() {

		global $db, $db2, $settings;

		/********
		* Ticket Def
		********/

		$this->start('Updating Ticket Def Table');

		$db->query("
			ALTER TABLE ticket_def
			DROP length,
			DROP height,
			DROP perline,
			DROP extrainput_location,
			DROP extrainput_text,
			ADD `display_name_language` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD `description_language` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD `error_language` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD `php_default_value` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD INDEX (user_viewable),
			ADD INDEX (tech_viewable),
			CHANGE formtype `formtype` enum('input','select','textarea','multiselect','radio','checkbox','custom') NOT NULL default 'select'
		");

		$db->query_silent_extra("
			ALTER TABLE ticket_def
			CHANGE parse_default_value parsed_default_value VARCHAR (250)
		");


		unset($data);
		// sort out language
		$db->query("SELECT * FROM ticket_def");
		while ($result = $db->row_array()) {
			$data[$result['id']] = $result;
		}

		if (is_array($data)) {
			foreach ($data AS $key => $result) {

				$description = '';
				$display_name = '';
				$error_message = '';

				$result['description'] = unserialize($result['description']);
				$result['display_name'] = unserialize($result['display_name']);
				$result['error_message'] = unserialize($result['error_message']);

				if (is_array($result['description'])) {
					foreach ($result['description'] AS $key => $var) {
						if (trim($var)) {
							$description = $var;
							break;
						}
					}
				}
				foreach ($result['display_name'] AS $key => $var) {
					if (trim($var)) {
						$display_name = $var;
						break;
					}
				}
				foreach ($result['error_message'] AS $key => $var) {
					if (trim($var)) {
						$error_message = $var;
						break;
					}
				}

				$db->query("
					UPDATE ticket_def SET
						description = '" . $db->escape($description) . "',
						display_name = '" . $db->escape($display_name) . "',
						error_message = '" . $db->escape($error_message) . "'
					WHERE id = $result[id]
				");
			}
		}

		/********
		* User Def
		********/

		$this->yes();
		$this->start('Updating User Def Table');

		$db->query("
			ALTER TABLE user_def
			DROP length,
			DROP height,
			DROP perline,
			DROP extrainput_location,
			DROP extrainput_text,
			ADD `display_name_language` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD `description_language` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD `error_language` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD `php_default_value` MEDIUMTEXT NOT NULL DEFAULT '',
			CHANGE `formtype` `formtype` enum('input','select','textarea','multiselect','radio','checkbox','custom') NOT NULL default 'select',
			CHANGE displayorder displayorder INT(10) NOT NULL DEFAULT '0'
		");

		$db->query_silent_extra("
			ALTER TABLE user_def
			CHANGE parse_default_value parsed_default_value VARCHAR (250)
		");

		unset($data);
		// sort out language
		$db->query("SELECT * FROM user_def");
		while ($result = $db->row_array()) {
			$data[$result['id']] = $result;
		}

		if (is_array($data)) {
			foreach ($data AS $key => $result) {

				$description = '';
				$display_name = '';
				$error_message = '';

				$result['description'] = unserialize($result['description']);
				$result['display_name'] = unserialize($result['display_name']);
				$result['error_message'] = unserialize($result['error_message']);

				foreach ($result['description'] AS $key => $var) {
					if (trim($var)) {
						$description = $var;
						break;
					}
				}
				foreach ($result['display_name'] AS $key => $var) {
					if (trim($var)) {
						$display_name = $var;
						break;
					}
				}
				foreach ($result['error_message'] AS $key => $var) {
					if (trim($var)) {
						$error_message = $var;
						break;
					}
				}

				$db->query("
					UPDATE user_def SET
						description = '" . $db->escape($description) . "',
						display_name = '" . $db->escape($display_name) . "',
						error_message = '" . $db->escape($error_message) . "'
					WHERE id = $result[id]
				");
			}
		}

		/********
		* User Bill
		********/

		$this->yes();
		$this->start('Updating User Bill Table');

		$db->query("
			ALTER TABLE `user_bill`
			DROP billable,
			DROP paid,
			CHANGE `charge` `charge` VARCHAR (250) NOT NULL DEFAULT '',
			CHANGE `time` `timecharge` INT (10) NOT NULL DEFAULT '0',
			ADD comments MEDIUMTEXT NOT NULL DEFAULT '',
			CHANGE stamp `timestamp` INT (10) NOT NULL DEFAULT '0',
			ADD INDEX (userid)
		");

		/********
		* User Email
		********/

		$this->yes();
		$this->start('Updating User Email Table');

		$db->query("
			ALTER TABLE `user_email`
			ADD `timestamp` INT( 10 ) NOT NULL DEFAULT '0',
			DROP INDEX email,
			ADD UNIQUE (email, userid)
		");

		/********
		* Faq Articles
		********/

		$this->yes();
		$this->start('Updating Faq Articles Table');

		$db->query("
			ALTER TABLE `faq_articles`
			ADD featured INT (1) NOT NULL DEFAULT '0',
			DROP question_html,
			DROP answer_html,
			CHANGE `show_order` `displayorder` INT( 10 ) NOT NULL DEFAULT '0',
			CHANGE `date_made` `timestamp_made` INT( 10 ) NOT NULL DEFAULT '0',
			CHANGE `date_modified` `timestamp_modified` INT( 10 ) NOT NULL DEFAULT '0',
			ADD INDEX (timestamp_made),
			ADD INDEX (timestamp_modified),
			ADD INDEX (to_validate, category),
			ADD INDEX (featured),
			DROP INDEX ref,
			ADD UNIQUE (ref)
		");

		/********
		* Faq Articles Related
		********/

		$this->yes();
		$this->start('Updating Faq Articles Related Table');

		$db->query("
			ALTER TABLE `faq_articles_related`
			DROP INDEX show_article,
			ADD PRIMARY KEY (show_article, related_article)
		");

		/********
		* Faq Attachments
		********/

		$this->yes();
		$this->start('Updating Faq Attachments Table');

		$db->query("
			ALTER TABLE `faq_attachments`
			ADD downloads INT (10) NOT NULL DEFAULT '0'
		");

		/********
		* Faq Cats
		********/

		$this->yes();
		$this->start('Updating Faq Cats Table');

		$db->query("
			ALTER TABLE `faq_cats`
			CHANGE `show_order` `displayorder` INT( 10 ) NOT NULL DEFAULT '0',
			CHANGE `newdate` `timestamp_created` INT( 10 ) NOT NULL DEFAULT '0',
			CHANGE `editdate` `timestamp_article_activity` INT( 10 ) NOT NULL DEFAULT '0',
			ADD extracontent MEDIUMTEXT NOT NULL DEFAULT '',
			DROP parentlist,
			DROP totalarticles
		");

		/********
		* Faq Comments
		********/

		$this->yes();
		$this->start('Updating Faq Comments Table');

		$db->query("
			ALTER TABLE `faq_comments`
			ADD timestamp_created INT (10) NOT NULL DEFAULT '0',
			ADD published INT (1) NOT NULL DEFAULT '0',
			ADD logged INT (1) NOT NULL DEFAULT '0',
			ADD tech_publisher INT (10) NOT NULL DEFAULT '0',
			ADD timestamp_published INT (10)NOT NULL DEFAULT '0',
			ADD INDEX (logged)
		");

		$db->query("UPDATE faq_comments SET published = 1, logged = 1 WHERE new = 0");

		$db->query("
			ALTER TABLE faq_comments
			DROP `new`
		");

		/********
		* Faq Ratings
		********/

		$this->yes();
		$this->start('Updating Faq Ratings Table');

		$db->query("
			ALTER TABLE faq_rating
			CHANGE session sessionid VARCHAR (32) NOT NULL DEFAULT ''
		");

		/********
		* Faq Subscriptions
		********/

		$this->yes();
		$this->start('Updating Faq Subscriptions Table');

		$db->query("
			ALTER TABLE faq_subscriptions
			DROP `id`,
			ADD INDEX (userid)
		");

		/********
		* Gateway Spam
		********/

		$this->yes();
		$this->start('Updating Gateway Spam Table');

		$db->query("
			ALTER TABLE gateway_spam
			ADD `action` enum('delete','spam') NOT NULL default 'spam',
			DROP is_delete
		");

		$db->query("UPDATE gateway_spam SET action = 'spam'");

		/********
		* Ticket Atachments
		********/

		$this->yes();
		$this->start('Updating Ticket Attachments Table');

		$db->query("
			ALTER TABLE ticket_attachments
			ADD techtmp INT (1) NOT NULL DEFAULT '0',
			ADD messageid INT (10) NOT NULL DEFAULT '0',
			CHANGE temporaryid temporaryid VARCHAR(20) NOT NULL DEFAULT '',
			ADD INDEX (extension),
			DROP toemail
		");

		/********
		* Category
		********/

		$this->yes();
		$this->start('Updating Category Table');


		$db->query("
			ALTER TABLE ticket_cat
			DROP auto_assign_tech,
			CHANGE `cat_order` `displayorder` INT(10) NOT NULL DEFAULT '0',
			DROP user_view,
			DROP show_category,
			ADD name_language MEDIUMTEXT NOT NULL DEFAULT ''
		");

		/********
		* Priority
		********/


		$this->yes();
		$this->start('Updating Priority Table');


		$db->query("
			ALTER TABLE ticket_pri
			DROP user_view,
			DROP auto_assign_tech,
			DROP show_priority,
			ADD name_language MEDIUMTEXT NOT NULL DEFAULT '',
			CHANGE `pri_order` `displayorder` INT( 10 ) NOT NULL DEFAULT '0',
			ADD color VARCHAR (250) NOT NULL DEFAULT ''
		");

		/********
		* News
		********/


		$this->yes();
		$this->start('Updating New Table');


		$db->query("
			ALTER TABLE news
			CHANGE `date` timestamp INT (10) NOT NULL DEFAULT '0'
		");

		/********
		* Data
		********/

		$this->yes();
		$this->start('Updating Data Table');

		$db->query("
			DELETE FROM data WHERE name NOT IN ('email_ban', 'ip_ban')
		");

		// we may have duplicates
		$db->query("
			SELECT * FROM data WHERE name = 'email_ban'
		");

		unset($length, $maxlength, $id);
		while ($result = $db->row_array()) {
			$length = strlen($result['data']);
			if ($length > $maxlength) {
				$id = $result['id'];
				$maxlength = $length;
			}
		}

		$db->query("DELETE FROM data WHERE name = 'email_ban' AND id != " . intval($id));

		$db->query("
			DELETE FROM data WHERE name NOT IN ('email_ban', 'ip_ban')
		");

		// we may have duplicates
		$db->query("
			SELECT * FROM data WHERE name = 'ip_ban'
		");

		unset($length, $maxlength, $id);
		while ($result = $db->row_array()) {
			$length = strlen($result['data']);
			if ($length > $maxlength) {
				$id = $result['id'];
				$maxlength = $length;
			}
		}

		$db->query("DELETE FROM data WHERE name = 'ip_ban' AND id != " . intval($id));

		$db->query("
			ALTER TABLE data
			DROP `id`,
			ADD PRIMARY KEY (name),
			ADD INDEX (isdefault)
		");

		/********
		* Tech Ticket Save
		********/

		$this->yes();
		$this->start('Updating Tech Ticket Save Table');


		$db->query("
			ALTER TABLE tech_ticket_save
			ADD INDEX (ticketid)
		");


		/********
		* Ticket Notes
		********/

		$this->yes();
		$this->start('Updating Ticket Attachments Table');

		$db->query("
			ALTER TABLE `ticket_notes`
			CHANGE `date` `timestamp` INT( 10 ) NOT NULL DEFAULT '0'
		");

		/********
		* Ticket Field Display
		********/

		$this->yes();
		$this->start('Updating Ticket Fielddisplay Table');

		$db->query("
			ALTER TABLE ticket_fielddisplay
			ADD description MEDIUMTEXT NOT NULL DEFAULT ''
		");

		/********
		* Tech News
		********/

		$this->yes();
		$this->start('Updating Tech News Table');


		$db->query("
			ALTER TABLE tech_news
			CHANGE `date` `timestamp` INT( 10 ) NOT NULL DEFAULT '0',
			ADD frontpage INT (1) NOT NULL DEFAULT '0'
		");

		$db->query("UPDATE tech_news SET frontpage = 1");

		/********
		* Ticket Log
		********/

		$this->yes();
		$this->start('Updating Ticket Log Table');

		$db->query("
			ALTER TABLE ticket_log
			CHANGE actionid actionlog VARCHAR(250) NOT NULL DEFAULT '',
			ADD `agent` VARCHAR(250) NOT NULL DEFAULT '',
			ADD INDEX (timestamp)
		");

		/********
		* Faq Keywords
		********/

		$db->query("SELECT * FROM faq_keywords");
		while ($result = $db->row_array()) {
			$keywords[] = array('word' => $result['word'], 'articles' => $result['articles']);
		}

		if (is_array($keywords)) {
			foreach ($keywords AS $key => $var) {

				// check we don't have already
				$result = $db->query_return("
					SELECT wordid FROM faq_keywords_words
					WHERE word = '" . $db->escape($var['word']) . "'
				");

				if (is_array($result)) {
					$wordid = $result['wordid'];

				} else {

					// insert the keyword
					$db->query("
						INSERT INTO faq_keywords_words
						SET word = '" . $db->escape($var['word']) . "'
					");

					$wordid = $db->insert_id();
				}

				// now insert the keywords
				$keywords = explode(',', $var['articles']);

				foreach ($keywords AS $key => $articleid) {

					if (is_numeric($var) AND $articleid > 0) {
						$db->query("
							INSERT INTO faq_articles SET
							articleid = $articleid,
							wordid = $wordid
						");
					}
				}
			}
		}

		// drop table
		$db->query("DROP TABLE `faq_keywords`");

		/********
		* Gateway Pop Accounts
		********/

		$this->yes();
		$this->start('Updating Gateway Pop Accounts Table');

		$db->query("
			ALTER TABLE gateway_pop_accounts
			ADD port INT (10) NOT NULL DEFAULT '0',
			ADD usessl INT (1) NOT NULL DEFAULT '0',
			ADD showpop INT (1) NOT NULL DEFAULT '0',
			ADD active INT (1) NOT NULL DEFAULT '0',
			ADD INDEX (server)
		");

		$db->query("UPDATE gateway_pop_accounts SET active = 1, showpop = 1, port = 110");

		$this->yes();

	}

	/***************************************************
	* - Handle POP3 accounts / rules etc
	***************************************************/

	function step12() {

		global $db, $db2, $settings;

		// lets get all the old gateway account data
		$db->query("SELECT * FROM gateway_accounts");
		while ($result = $db->row_array()) {
			$accounts[$result['id']] = $result;
			$accounts_email[$result['email']] = $result;
		}

		// also need the pop accounts
		$db->query("SELECT * FROM gateway_pop_accounts WHERE target = 'user'");
		while ($result = $db->row_array()) {
			$pop_accounts[$result['id']] = $result;
		}

		$this->start('Creating Mail Accounts');

		$done = false;

		if (is_array($accounts)) {

			// we need a gateway_email for each of these. Lets also use the same ID number
			foreach ($accounts AS $account) {

				$done = true;

				// we only want one default account
				if ($done_default) {
					$account['is_default'] = 0;
				}
				if ($account['is_default']) {
					$done_default = true;
					$default_rule_id = $account['id'];
				}

				$db->query("
					INSERT INTO gateway_emails SET
						id = " . intval($account['id']) . ",
						email = '" . $db->escape($account['email']) . "',
						name = '" . $db->escape($name) . "',
						is_default = " . intval($account['is_default']) . "
				");

			}

		}

		if ($done AND !$done_default) {
			$db->query("UPDATE gateway_emails SET is_default = 1 LIMIT 1");
		}

		$this->yes();

		// create default rule
		$db->query("
			INSERT INTO ticket_rules_mail SET
			auto_reply = 1,
			auto_new = 1,
			accountid = " . intval($default_rule_id) . "
		");

		if (!is_win()) {

			$this->start('Creating Mail Rules');

			if (is_array($accounts)) {

				// we now need to create some rules (these are for PIPE only)
				foreach ($accounts AS $account) {

					unset($actions);

					if ($account['priority']) {
						$actions['priority'] = $account['priority'];
					}
					if ($account['category']) {
						$actions['category'] = $account['category'];
					}
					if ($account['tech']) {
						$actions['tech'] = $account['tech'];
					}

					$criteria['email_to'] = $account['email'];

					$db->query("
						INSERT INTO ticket_rules_mail SET
							auto_reply = " . intval($account['auto_reply']) . ",
							auto_new = " . intval($account['auto_new']) . ",
							actions = '" . $db->escape(serialize($actions)) . "',
							criteria = '" . $db->escape(serialize($criteria)) . "',
							accountid = " . intval($account['id']) . "
					");
				}

			}

			$this->yes();

		}

		if (is_array($pop_accounts)) {

			$this->start('Creating Mail POP3 Rules');

			foreach ($pop_accounts AS $pop) {

				// firstly we get the old style rule we where using
				$account = $accounts[$pop['accountid']];

				unset($actions);

				if ($account['priority']) {
					$actions['priority'] = $account['priority'];
				}
				if ($account['category']) {
					$actions['category'] = $account['category'];
				}
				if ($account['tech']) {
					$actions['tech'] = $account['tech'];
				}

				$criteria['pop'] = $pop['id'];

				$db->query("
					INSERT INTO ticket_rules_mail SET
						auto_reply = " . intval($account['auto_reply']) . ",
						auto_new = " . intval($account['auto_new']) . ",
						actions = '" . $db->escape(serialize($actions)) . "',
						criteria = '" . $db->escape(serialize($criteria)) . "',
						accountid = " . intval($account['id']) . "
				");

			}

			$this->yes();

		}

		$this->start('Modifying Mail Rule Tables');

		// we no longer need the gateway accounts table
		$db->query("DROP TABLE `gateway_accounts`");

		$db->query("
			ALTER TABLE gateway_pop_accounts
			DROP accountid
		");

		$this->yes();

	}

	/***************************************************
	* - Ticket Message
	***************************************************/

	function step13() {

		global $db, $db2, $settings;

		$this->start('Checking if ticket_message table is using the MyISAM storage engine');

		$tinfo = $db->query_return("SHOW TABLE STATUS LIKE 'ticket_message'");

		if ($tinfo) {
			$this->yes();

			if (strtolower($tinfo['Engine']) != 'myisam') {
				$this->start('Converting table engine to MyISAM');

				$db->query("ALTER TABLE `ticket_message` TYPE=MyISAM");

				$this->yes();
			}
		}

		/********
		* Ticket Message
		********/

		$this->start('Updating Ticket Message Table');

		$db->query("
			ALTER TABLE `ticket_message`
			CHANGE `date` `timestamp` INT( 10 ) NOT NULL DEFAULT '0',
			ADD `note` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD `strip_tags` INT(1) NOT NULL DEFAULT '0',
			ADD `charset` VARCHAR(250) NOT NULL DEFAULT '',
			ADD `messagehash` VARCHAR(50) NOT NULL DEFAULT '',
			CHANGE `techid` `techid` INT(10) NOT NULL DEFAULT '0',
			ADD `messagesource` MEDIUMTEXT NOT NULL DEFAULT '',
			ADD INDEX (timestamp),
			ADD INDEX (userid),
			ADD INDEX (sourceid),
			DROP striptags
		");

		$this->yes();

	}

	/***************************************************
	* - Gateway Sources
	***************************************************/

	function step14() {

		global $db, $db2, $settings;

		/********
		* Gateway Source Parts
			- copy (because lots of data) from gateway_source
			- updated the ids to be links back to the gateway_source table
		********/

		$this->start('Creating Gateway Source Parts Table');

		$db->query("
			CREATE TABLE gateway_source_parts SELECT * FROM gateway_source
		");

		$this->yes();

		$this->start('Updating Gateway Source Parts Table');

		$db->query("
			ALTER TABLE gateway_source_parts
			CHANGE `id` `sourceid` INT (10) NOT NULL DEFAULT '0',
			ADD `id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			DROP headers,
			CHANGE `source` `source` BLOB NOT NULL DEFAULT '',
			ADD displayorder INT (10) NOT NULL DEFAULT '0',
			ADD INDEX (sourceid)
		");

		$this->yes();

		/********
		* Now alter the gateway_source table
		********/

		$this->start('Updating Gateway Source Table');

		$db->query("
			ALTER TABLE gateway_source
			DROP source,
			ADD inserted INT (1) NOT NULL DEFAULT '0',
			ADD decoded INT (1) NOT NULL DEFAULT '0',
			ADD completed INT (1) NOT NULL DEFAULT '0',
			ADD timestamp INT (10) NOT NULL DEFAULT '0',
			ADD gateway VARCHAR(50) NOT NULL DEFAULT ''
		");

		$db->query("
			UPDATE gateway_source SET
			inserted = 1,
			decoded = 1,
			completed = 1,
			timestamp = " . TIMENOW
		);

		$this->yes();

	}

	/***************************************************
	* - Calendar
	***************************************************/

	function step15() {

		global $db, $db2, $settings;


		$this->start('Updating Calendar Tables');

		/***********
		* Calendar Tasks
		************/
		$db->query("
			ALTER TABLE calendar_task
			ADD startstamp INT(10) NOT NULL DEFAULT '0',
			ADD endstamp INT (10) NOT NULL DEFAULT '0',
			ADD allday INT (1) NOT NULL DEFAULT '0'
		");

		$db->query("SELECT * FROM calendar_task");
		while ($result = $db->row_array()) {

			if ($result['starttime'] == '00:00:00') {

				$parts = explode('-', $result['startdate']);
				$years = $parts[0];
				$months = $parts[1];
				$days = $parts[2];

				$startstamp = dp_gmmktime($years, $months, $days, 0, 0, 0);

				$parts = explode('-', $result['startdate']);
				$years = $parts[0];
				$months = $parts[1];
				$days = $parts[2];

				$endstamp = dp_gmmktime($years, $months, $days, 0, 0, 0);

				// no start time so all day event
				$db2->query("
					UPDATE calendar_task SET
						startstamp = '$startstamp',
						endstamp = '$endstamp',
						allday = 1
					WHERE id = $result[id]
				");

			} else {

				$parts = explode('-', $result['startdate']);
				$years = $parts[0];
				$months = $parts[1];
				$days = $parts[2];

				$parts = explode(':', $result['starttime']);
				$hours = $parts[0];
				$minutes = $parts[1];

				$startstamp = dp_gmmktime($years, $months, $days, $hours, $minutes, 0);

				$parts = explode('-', $result['startdate']);
				$years = $parts[0];
				$months = $parts[1];
				$days = $parts[2];

				$parts = explode(':', $result['starttime']);
				$hours = $parts[0];
				$minutes = $parts[1];

				$endstamp = dp_gmmktime($years, $months, $days, $hours, $minutes, 0);

				// specific start time
				$db2->query("
					UPDATE calendar_task SET
						startstamp = '$startstamp',
						endstamp = '$endstamp'
					WHERE id = $result[id]
				");
			}
		}

		$db->query("
			ALTER TABLE calendar_task
			DROP startdate,
			DROP enddate,
			DROP starttime,
			DROP endtime
		");

		$db->query_silent_extra("ALTER TABLE calendar_task DROP timezone");

		/***********
		* Calendar Task Iteration
		************/

		$db->query("
			ALTER TABLE calendar_task_iteration
			ADD timestamp INT(10) NOT NULL DEFAULT '0',
			ADD INDEX (taskid)
		");

		$db->query("DELETE FROM calendar_task_iteration");

		$db->query("
			ALTER TABLE calendar_task_iteration
			DROP date,
			DROP time
		");

		/***********
		* Calendar Task Tech
		************/

		$db->query("
			ALTER TABLE calendar_task_tech
			CHANGE eventid taskid INT (10) NOT NULL DEFAULT '0',
			DROP stamp
		");

		/***********
		* Tech Ticket Reminders
		************/

		$db->query("
			ALTER TABLE tech_ticket_watch
			ADD timestamp_complete INT (10) NOT NULL DEFAULT '0'
		");

		$db->query("
			SELECT * FROM tech_ticket_watch
		");

		while ($result = $db->row_array()) {

			$parts = explode('-', $result['datetodo']);
			$years = $parts[0];
			$months = $parts[1];
			$days = $parts[2];

			$timestamp_complete = dp_gmmktime($years, $months, $days, 0, 0, 0);

			$db2->query("
				UPDATE tech_ticket_watch
				SET timestamp_complete = '$timestamp_complete'
				WHERE id = $result[id]
			");
		}

		$db->query("
			ALTER TABLE tech_ticket_watch
			CHANGE created timestamp_created INT (10) NOT NULL DEFAULT '0',
			DROP datetodo
		");

		/***********
		* Delete Repeating Tasks
		************/

		$db->query("
			SELECT id FROM calendar_task WHERE repeattype > 0
		");

		unset($ids);
		while ($result = $db->row_array()) {
			$ids[] = $result['id'];
		}

		$db->query("
			DELETE FROM calendar_task WHERE repeattype > 0
		");

		$db->query("
			DELETE FROM calendar_task_tech WHERE taskid IN " . array2sql($ids)
		);

		$db->query("DELETE FROM calendar_task_iteration");

		$this->yes();

	}

	/***************************************************
	* - User Table
	***************************************************/

	function step16() {

		global $db, $db2, $settings;


		$this->start('Creating User Indexes');

		require_once(INC . 'functions/useractions_functions.php');

		/********
		* The Odd Chance of a Duplicate Emails (e.g. from double submission of form)
		********/

		// add an index to the ticket_log column or this takes a long time.
		$db->query("ALTER TABLE `ticket_log` ADD INDEX (`userid`), ADD INDEX (`actionlog`)");

		// get the dups
		$db->query("
			SELECT email, COUNT(*) AS total FROM user GROUP BY email HAVING total >= 2
		");

		while ($result = $db->row_array()) {
			$duplicate_emails[] = $result['email'];
		}

		if (is_array($duplicate_emails)) {
			foreach ($duplicate_emails AS $key => $email) {

				$ids = array();

				$db->query("
					SELECT id FROM user
					WHERE email = '" . $db->escape($email) . "'
					ORDER BY id DESC
				");
				while ($result = $db->row_array()) {
					$ids[] = $result['id'];
				}

				$masterid = array_pop($ids);

				foreach ($ids AS $id) {
					legacy_merge_user($masterid, $id, $true);
				}
			}
		}

		/********
		* The Odd Chance of a Duplicate Username (e.g. from double submission of form)
		********/

		$db->query("
			SELECT username, COUNT(*) AS total FROM user GROUP BY username HAVING total >= 2
		");

		while ($result = $db->row_array()) {
			$duplicate_usernames[] = $result['username'];
		}

		if (is_array($duplicate_usernames)) {
			foreach ($duplicate_usernames AS $key => $username) {

				$users = array();

				$db->query("
					SELECT id, email FROM user
					WHERE username = '" . $db->escape($username) . "'
					ORDER BY id DESC
				");

				while ($result = $db->row_array()) {
					$users[] = $result;
				}

				// get rid of one
				$masterid = array_pop($users);

				foreach ($users AS $user_details) {

					$username2 = legacy_make_username($user_details['email']);

					$db->query("
						UPDATE user SET username = '" . $db->escape($username2) . "' WHERE id = $user_details[id]
					");

				}
			}
		}

		$this->yes();

	}

	/***************************************************
	* User Table
	***************************************************/

	function step17() {

		global $db, $db2, $settings;

		/********
		* User
		********/

		$this->start('Updating User Table');

		$db->query("
			ALTER TABLE user
			ADD `style` INT (10) NOT NULL DEFAULT '0',
			ADD `password_change_timestamp` INT (10) NOT NULL DEFAULT '0',
			ADD `password_change_key` VARCHAR (250) NOT NULL DEFAULT '',
			DROP expire_type,
			DROP INDEX username,
			ADD UNIQUE (username),
			DROP INDEX email,
			ADD UNIQUE (email),
			ADD INDEX (name),
			CHANGE timezone timezone VARCHAR (4) NOT NULL DEFAULT ''
		");

		// some queries that might need to be run
		$db->query_silent_extra("DROP INDEX techid ON ticket");
		$db->query_silent_extra("ALTER TABLE user CHANGE `awaiting_manual_validation` `awaiting_register_validate_tech` INT (1) NOT NULL DEFAULT '0'");
		$db->query_silent_extra("ALTER TABLE user CHANGE `awaiting_validation` `awaiting_register_validate_user` INT (1) NOT NULL DEFAULT '0'");
		$db->query_silent_extra("UPDATE user SET name = CONCAT(firstname, ' ', surname) WHERE name = ''");
		$db->query_silent_extra("ALTER TABLE user DROP firstname, DROP surname");
		$db->query_silent_extra("ALTER TABLE user DROP weekstart");
		$db->query_silent_extra("ALTER TABLE user DROP website");
		$db->query_silent_extra("ALTER TABLE user ADD disabled_reason VARCHAR(250) NOT NULL");

		$this->yes();

	}

	/***************************************************
	* - Settings
	***************************************************/

	function step18() {

		global $db, $db2, $settings;

		$this->start('Updating Settings Table');

		// get settings name and values
		$db->query("
			SELECT settings, value FROM settings
		");

		while ($result = $db->row_array()) {
			$old_settings[$result['settings']] = $result['value'];
		}

		// delete old settings
		$db->query("DELETE FROM settings");

		$db->query("
			ALTER TABLE settings
			DROP `id`,
			DROP `show_order`,
			CHANGE `name` display_name VARCHAR(75) NOT NULL default '',
			CHANGE `settings` `name` VARCHAR (250) NOT NULL default '',
			CHANGE category category VARCHAR (250) NOT NULL default '',
			ADD `default_value` mediumtext NOT NULL,
			ADD displayorder INT (10) NOT NULL default '0',
			CHANGE options options MEDIUMTEXT NOT NULL default '',
			ADD `custom` INT (1) NOT NULL default '0',
			ADD PRIMARY KEY (name)
		");

		$this->yes();

		$this->start('Import New Settings');

		// import settings
		legacy_import_settings();
		$settings = legacy_get_settings();

		require_once(INC . 'functions/settings_functions.php');

		// settings that we want to keep
		$settings_keep = array('allow_registration', 'priority_user_viewable', 'priority_user_editable', 'priority_require_selection', 'category_user_viewable', 'category_user_editable', 'use_smtp', 'category_require_selection', 'smtp_host', 'smtp_port', 'smtp_helo', 'smtp_auth', 'smtp_user', 'smtp_pass', 'faq_days', 'gateway_ticket_reopen', 'faq_columns', 'user_reopen', 'cookie_path', 'require_registration', 'helpdesk_url', 'site_url', 'site_name', 'email_from');

		foreach ($settings_keep AS $set) {
			legacy_update_setting($set, $old_settings[$set]);
		}

		// set value for some settings that have changed
		legacy_update_setting('gateway_tech_email', $old_settings['email_tech']);
		legacy_update_setting('gateway_return_email', $old_settings['email_return']);
		legacy_update_setting('gateway_webmail_email', $old_settings['tech_send_email']);
		legacy_update_setting('gateway_require_registration', $old_settings['gateway_require_reg']);

		// Empty HELO or localhost, then update it to the current domain
		if ($old_settings['smtp_helo'] == 'localhost' OR !$old_settings['smtp_helo']) {

			$url_parts = @parse_url($settings['helpdesk_url']);
			if ($url_parts['host']) {
				legacy_update_setting('smtp_helo', $url_parts['host']);
			}
		}

		// gateway email accounts
		$db->query("
			UPDATE gateway_emails SET name = '" . $db->escape($old_settings['email_from_name']) . "'
		");

		// set version
		legacy_update_setting('deskpro_version_internal', 1);
		legacy_update_setting('deskpro_version', '3.0.0 Alpha 1');

		$this->yes();

	}

	/***************************************************
	* - Ticket Log
	***************************************************/

	function step19() {

		global $db, $db2, $settings;

		$this->start('Updating Ticket Log Data');

		$query[] = "UPDATE ticket_log SET actionlog = 'lock' WHERE actionlog = 1;";
		$query[] = "UPDATE ticket_log SET actionlog = 'reply' WHERE actionlog = 2;";

		$query[] = "UPDATE ticket_log SET actionlog = 'status', detail_after = 'awaiting_user' WHERE actionlog = 4";
		$query[] = "UPDATE ticket_log SET actionlog = 'status', detail_after = 'awaiting_user' WHERE actionlog = 6";
		$query[] = "UPDATE ticket_log SET actionlog = 'status', detail_after = 'awaiting_tech' WHERE actionlog = 5";
		$query[] = "UPDATE ticket_log SET actionlog = 'status', detail_after = 'closed' WHERE actionlog = 3";

		$query[] = "UPDATE ticket_log SET actionlog = 'status' WHERE actionlog = 35;";

		$query[] = "UPDATE ticket_log SET actionlog = 'created' WHERE actionlog = 8;";

		$query[] = "UPDATE ticket_log SET actionlog = 'reply_user' WHERE actionlog = 9;";
		$query[] = "UPDATE ticket_log SET actionlog = 'reply_tech' WHERE actionlog = 14;";

		$query[] = "UPDATE ticket_log SET actionlog = 'note' WHERE actionlog = 10;";
		$query[] = "UPDATE ticket_log SET actionlog = 'unlock' WHERE actionlog = 11;";
		$query[] = "UPDATE ticket_log SET actionlog = 'tech' WHERE actionlog = 12;";

		$query[] = "UPDATE ticket_log SET actionlog = 'category' WHERE actionlog = 15;";
		$query[] = "UPDATE ticket_log SET actionlog = 'custom' WHERE actionlog = 16;";
		$query[] = "UPDATE ticket_log SET actionlog = 'subject' WHERE actionlog = 17;";
		$query[] = "UPDATE ticket_log SET actionlog = 'priority' WHERE actionlog = 18;";
		$query[] = "UPDATE ticket_log SET actionlog = 'message_edit' WHERE actionlog = 19;";
		$query[] = "UPDATE ticket_log SET actionlog = 'add_attach' WHERE actionlog = 20;";
		$query[] = "UPDATE ticket_log SET actionlog = 'del_attach' WHERE actionlog = 21;";
		$query[] = "UPDATE ticket_log SET actionlog = 'merge' WHERE actionlog = 22;";
		$query[] = "UPDATE ticket_log SET actionlog = 'billing_added' WHERE actionlog = 23;";

		// billing changed, email->user, email->tech, sms->tech, digest, email_close, cc, email changed
		$query[] = "DELETE FROM ticket_log WHERE actionlog IN (24, 27, 28, 29, 31, 13, 32, 7)";

		$query[] = "UPDATE ticket_log SET actionlog = 'billing_deleted' WHERE actionlog = 25;";
		$query[] = "UPDATE ticket_log SET actionlog = 'note_deleted' WHERE actionlog = 26;";

		$query[] = "UPDATE ticket_log SET actionlog = 'escalate' WHERE actionlog = 30;";


		$query[] = "UPDATE ticket_log SET actionlog = 'spam' WHERE actionlog = 33;";
		$query[] = "UPDATE ticket_log SET actionlog = 'nospam' WHERE actionlog = 34;";

		// run the queries
		foreach ($query AS $key => $var) {
			$db->query($var);
		}

		// now remove the index
		$db->query("ALTER TABLE `ticket_log` DROP INDEX userid, DROP INDEX actionlog");

		$this->yes();

	}

	/***************************************************
	* - Custom Fields
	***************************************************/

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

	function step20() {

		global $db, $db2, $settings;

		$this->start('Updating Ticket Custom Fields');

		fix_custom_field_data('ticket', 'ticket_def');

		$this->yes();

		$this->start('Updating User Custom Fields');

		fix_custom_field_data('user', 'user_def');

		$this->yes();

	}

	/***************************************************
	* - Set Version
	***************************************************/

	function step21() {

		global $db, $db2, $settings;

		$this->start('Loading Some Data');

		do {

			$results = $db->query_return_array("
				SELECT ticket_message.id
				FROM ticket_message
				LEFT JOIN gateway_source ON gateway_source.id = ticket_message.sourceid
				WHERE sourceid != 0 AND gateway_source.id IS NULL
				LIMIT 10000
			");

			if (is_array($results)) {

				unset($ids);

				foreach ($results AS $result) {
					$ids[] = $result['id'];
				}

				$db->query("UPDATE ticket_message SET sourceid = 0 WHERE id IN " . array2sql($ids));

			}

		} while (is_array($results));

		run_xml_queries('install/data/upgradedata.xml');

		$this->yes();

	}

	/***************************************************
	* - Delete from Gateway_Source
	***************************************************/

	function step22() {

		global $db, $db2, $settings;

		$this->start('Updating (max 10,000) Sourceids');

		for ($i = 0; $i < 10; $i++) {

			$results = $db->query_return_array("
				SELECT gateway_source.id
				FROM gateway_source
				LEFT JOIN ticket_message ON gateway_source.id = ticket_message.sourceid
				WHERE ticket_message.id IS NULL
				LIMIT 1000
			");

			if (is_array($results)) {
				unset($ids);
				foreach ($results AS $result) {
					$ids[] = $result['id'];
				}
				$ids = array2sql($ids);
				$db->query("DELETE FROM gateway_source WHERE id IN " . $ids);
				$db->query("DELETE FROM gateway_source_parts WHERE sourceid IN " . $ids);

			} else {
				break;
			}
		}

		$result = $db->query_return("
			SELECT COUNT(*) AS total
			FROM gateway_source
			LEFT JOIN ticket_message ON gateway_source.id = ticket_message.sourceid
			WHERE ticket_message.id IS NULL
		");

		$this->yes();

		// still more to do?
		if ($result['total']) {

			$this->start($result['total'] . ' remaining');
			$this->redoStep(22);

		}
	}

	/***************************************************
	* - Set Version
	***************************************************/

	function step23() {

		global $db, $db2, $settings;

		$this->start('Cleaning Indexes');

		// more potential cleanup
		$db->query_silent_extra("ALTER TABLE blob_parts DROP INDEX id");
		$db->query_silent_extra("ALTER TABLE tech_ticket_save DROP index id, ADD PRIMARY KEY (`id`)");
		$db->query_silent_extra("ALTER TABLE tech_ticket_save DROP index techid, ADD UNIQUE (`ticketid`, `techid`)");

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
$upgrade = new upgrade_v2();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>