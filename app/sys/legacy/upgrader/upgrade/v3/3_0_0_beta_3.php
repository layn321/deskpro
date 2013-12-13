<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: 3_0_0_beta_3.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | -
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_4 extends upgrade_base_v3 {

	var $version = '3.0.0 Beta 3';

	var $version_number = 4;

	var $pages = array(
		array('Database Changes', 'options.gif'),
		array('Database Changes', 'options.gif'),
		array('Database Changes', 'options.gif'),
		array('Database Changes', 'options.gif'),
		array('Database Changes', 'options.gif'),
		array('Ticket Times', 'options.gif'),
		array('Ticket Times', 'options.gif'),
		array('Ticket Times', 'options.gif'),
		array('Ticket Times', 'options.gif'),
		array('Ticket Message', 'options.gif')
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db, $settings;

		$this->start('Checking Helpdesk URL Settings');

		if (substr($settings['helpdesk_url'], -1, 1) != '/') {
			require_once(INC . "functions/settings_functions.php");
			legacy_update_setting('helpdesk_url', $settings['helpdesk_url'] . '/');
		}

		$db->query("REPLACE INTO data SET name = 'plugin_settings'");
		$db->query("UPDATE data SET isdefault = 1 WHERE name IN ('email_ban', 'ip_ban', 'plugin_settings')");

		$this->yes();

		$this->start('Remove Extrainput for Custom Fields');

		$db->query("ALTER TABLE ticket_def DROP extrainput");
		$db->query("ALTER TABLE user_def DROP extrainput");
		$db->query("ALTER TABLE faq_def DROP extrainput");
		$db->query("ALTER TABLE calendar_def DROP extrainput");

		$db->query("ALTER TABLE `gateway_pop_failures` ADD INDEX ( `accountid` ) ");

		$this->yes();

		$this->start('Creating Manual Styles Table');

		$db->query("
			CREATE TABLE `manual_manual_styles` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`manualid` INT NOT NULL ,
				`title` varchar(250) NOT NULL,
				`name` VARCHAR( 250 ) NOT NULL ,
				`element` VARCHAR( 250 ) NOT NULL ,
				`attributes` TEXT NOT NULL ,
				`css` TEXT NOT NULL
			)
		");

		$db->query("ALTER TABLE `gateway_pop_accounts` DROP `showpop` ");

		$this->yes();

		$this->start('Updating Plugin Table');

		$db->query("
			ALTER TABLE `plugins` ADD `version` VARCHAR( 250 ) NOT NULL
		");

		$db->query("
			ALTER TABLE `plugins` ADD UNIQUE (
				`intname`
			)
		");

		$this->yes();

		$db->query("
			DROP TABLE `dev_errors`
		");

		$db->query("ALTER TABLE `error_log` CHANGE `summary` `summary` MEDIUMTEXT NOT NULL ");

		$this->start('Removing saved ticket searches');

		$db->query("DELETE FROM tech_ticket_search");

		$this->yes();

		$this->start('Adding Instant Chat Plugin Tables');

		$db->query("
			CREATE TABLE `chat_chat` (
			  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `ref` VARCHAR(250) NOT NULL,
			  `depid` INT(10) UNSIGNED NOT NULL,
			  `authcode` VARCHAR(32) NOT NULL,
			  `creatorid` VARCHAR(32) NOT NULL,
			  `creatortype` ENUM('user','tech','session') NOT NULL,
			  `creatorname` VARCHAR(250) NOT NULL,
			  `assigned_techid` INT(10) UNSIGNED NOT NULL,
			  `subject` TEXT NOT NULL,
			  `timestamp_ping` INT(10) NOT NULL,
			  `timestamp_start` INT(10) NOT NULL,
			  `timestamp_end` INT(10) NOT NULL,
			  `timestamp_assigned` INT(10) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");

		$db->query("
			CREATE TABLE `chat_dep` (
			  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `name` VARCHAR(250) NOT NULL,
			  `require_reg` INT(1) NOT NULL DEFAULT '0',
			  `staff_only` INT(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY  (`id`)
			)
		");

		$db->query("
			CREATE TABLE `chat_message` (
			  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `chatid` INT(10) UNSIGNED NOT NULL,
			  `authorid` VARCHAR(32) NOT NULL,
			  `authortype` ENUM('user','tech','session','system') NOT NULL,
			  `authorname` VARCHAR(250) NOT NULL,
			  `message` TEXT NOT NULL,
			  `visibility` ENUM('all','tech','user') NOT NULL,
			  `timestamp_sent` INT(10) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");

		$db->query("
			CREATE TABLE `tracking` (
			  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `userid` INT(10) UNSIGNED NOT NULL,
			  `sessionid` VARCHAR(32) NOT NULL,
			  `url` TEXT NOT NULL,
			  `referer` TEXT NOT NULL,
			  `timestamp` INT(10) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");

		$db->query("ALTER TABLE `tech` ADD `chat_timestamp_ping` INT ( 10 ) NOT NULL");

		$this->yes();

	}

	/***************************************************
	* Database changes
	***************************************************/

	function step2() {

		global $db, $settings;

		$this->start('Database Structure Updates');

		$db->query("
			ALTER TABLE `ticket_message` ADD `bounce` INT( 1 ) NOT NULL
		");

		$this->yes();

	}

	/***************************************************
	* Database changes
	***************************************************/

	function step3() {

		global $db, $settings;

		$this->start('Update Ticket Table');

		$db->query("ALTER TABLE `ticket`
				CHANGE `priority` `priority` INT( 10 ) NOT NULL DEFAULT '0',
				CHANGE `subject` `subject` VARCHAR( 250 ) NOT NULL

		");

		$this->yes();

	}

	/***************************************************
	* Database changes
	***************************************************/

	function step4() {

		global $db, $settings;

		$this->start('Update User Table');

		$db->query("ALTER TABLE `user` CHANGE `disabled_reason` `disabled_reason` VARCHAR( 250 ) NULL DEFAULT NULL, CHANGE `password` `password` VARCHAR( 250 ) NOT NULL, CHANGE `username` `username` VARCHAR( 250 ) NOT NULL, CHANGE `email` `email` VARCHAR( 250 ) NOT NULL  ");

		$this->yes();

	}

	/***************************************************
	* Database changes
	***************************************************/

	function step5() {

		global $db, $settings;

		$this->start('Database Changes');

		if (!check_table_index('tech_email', 'techid')) {
			$db->query_silent_extra("ALTER TABLE `tech_email` ADD INDEX ( `techid` ) ");
		}

		$db->query("ALTER TABLE `faq_articles` CHANGE `to_validate` `to_validate` INT( 1 ) NOT NULL DEFAULT '0'");
		$db->query("ALTER TABLE `tech` CHANGE `alert_frequency` `alert_frequency` INT( 2 ) NOT NULL DEFAULT '0'");
		$db->query("ALTER TABLE `ticket_pri` CHANGE `require_registration` `require_registration` INT( 1 ) NOT NULL DEFAULT '0'");
		$db->query("ALTER TABLE `ticket_cat` CHANGE `require_registration` `require_registration` INT( 1 ) NOT NULL DEFAULT '0'");
		$db->query("ALTER TABLE `ticket_def` CHANGE `display_name` `display_name` MEDIUMTEXT NOT NULL, CHANGE `error_message` `error_message` MEDIUMTEXT NOT NULL ");
		$db->query("ALTER TABLE `ticket_def` CHANGE `name` `name` VARCHAR( 250 ) NULL DEFAULT NULL ");
		$db->query("ALTER TABLE `faq_def` CHANGE `name` `name` VARCHAR( 250 ) NULL DEFAULT NULL ");
		$db->query("ALTER TABLE `faq_def` CHANGE `display_name` `display_name` MEDIUMTEXT NOT NULL ");
		$db->query("ALTER TABLE `user_def` CHANGE `name` `name` VARCHAR( 250 ) NULL DEFAULT NULL ");
		$db->query("ALTER TABLE `faq_articles` CHANGE `title` `title` VARCHAR( 250 ) NOT NULL ");
		$db->query("ALTER TABLE `faq_attachments` CHANGE `filename` `filename` VARCHAR( 250 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `faq_attachments` CHANGE `filesize` `filesize` VARCHAR( 250 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `faq_cats` CHANGE `name` `name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `faq_cats` CHANGE `description` `description` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `manual_manual_styles` CHANGE `title` `title` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `manual_manual_styles` CHANGE `name` `name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `manual_manual_styles` CHANGE `element` `element` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `news` CHANGE `title` `title` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `settings` CHANGE `name` `name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `settings` CHANGE `field_type` `field_type` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `settings` CHANGE `display_name` `display_name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `settings_cat` CHANGE `display_name` `display_name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `tech` CHANGE `username` `username` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `tech` CHANGE `password` `password` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `tech` CHANGE `email` `email` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `tech` CHANGE `weekstart` `weekstart` INT( 1 ) NOT NULL DEFAULT '7' ");
		$db->query("ALTER TABLE `tech` CHANGE `p_man_create` `p_man_create` INT( 1 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `tech` CHANGE `p_man_edit` `p_man_edit` INT( 1 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `tech` CHANGE `p_man_del` `p_man_del` INT( 1 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `tech` CHANGE `p_manpage_create` `p_manpage_create` INT( 1 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `tech` CHANGE `p_manpage_edit` `p_manpage_edit` INT( 1 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `tech` CHANGE `p_manpage_del` `p_manpage_del` INT( 1 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `tech` CHANGE `p_mancomment_manage` `p_mancomment_manage` INT( 1 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `tech_news` CHANGE `title` `title` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `tech_sendmail` CHANGE `subject` `subject` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `tech_sendmail` CHANGE `from_email` `from_email` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `tech_sendmail` CHANGE `to_email` `to_email` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `tech_ticket_search` CHANGE `save_name` `save_name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `template` CHANGE `name` `name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `template_cat` CHANGE `name` `name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `template_tech_email` CHANGE `name` `name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `template_tech_email` CHANGE `description` `description` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `template_user_email` CHANGE `name` `name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `template_user_email` CHANGE `description` `description` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `ticket_attachments` CHANGE `filename` `filename` VARCHAR( 250 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `ticket_attachments` CHANGE `filesize` `filesize` VARCHAR( 250 ) NOT NULL DEFAULT '0' ");
		$db->query("ALTER TABLE `ticket_cat` CHANGE `name` `name` VARCHAR( 250 ) NULL DEFAULT NULL  ");
		$db->query("ALTER TABLE `ticket_def` CHANGE `default_value` `default_value` VARCHAR( 250 ) NULL DEFAULT NULL  ");
		$db->query("ALTER TABLE `ticket_def` CHANGE `regex` `regex` VARCHAR( 250 ) NULL DEFAULT NULL  ");
		$db->query("ALTER TABLE `ticket_def` CHANGE `parsed_default_value` `parsed_default_value` VARCHAR( 250 ) NULL DEFAULT NULL  ");
		$db->query("ALTER TABLE `ticket_pri` CHANGE `name` `name` VARCHAR( 250 ) NOT NULL  ");
		$db->query("ALTER TABLE `user_def` CHANGE `default_value` `default_value` VARCHAR( 250 ) NULL DEFAULT NULL  ");
		$db->query("ALTER TABLE `user_def` CHANGE `parsed_default_value` `parsed_default_value` VARCHAR( 250 ) NULL DEFAULT NULL  ");
		$db->query("ALTER TABLE `user_def` CHANGE `regex` `regex` VARCHAR( 250 ) NULL DEFAULT NULL  ");
		$db->query("ALTER TABLE `tech` CHANGE `sms` `sms` VARCHAR( 250 ) NULL DEFAULT NULL  ");
		$db->query("ALTER TABLE `user_def` CHANGE `error_message` `error_message` VARCHAR( 250 ) NULL DEFAULT NULL  ");

		// some settings
		$db->query("UPDATE settings SET value = '' WHERE name = 'autoclose_tickets'");

		$this->yes();

	}

	/***************************************************
	* Database changes
	***************************************************/

	function step6($page) {

		global $db, $settings;

		// get total we have (dosen't change)
		$result = $db->query_return("
			SELECT COUNT(distinct ticketid) AS total
			FROM ticket_log
			WHERE actionlog = 'reply_user'
		");

		$total = $result['total'];

		if (!$total) {
			$this->start("Setting Timestamps");
			$this->yes();
			return;
		}

		$pages = ceil($total / 2500);

		$this->start("Setting Timestamps (User Reply). 2,500 Tickets Per Page Load. Page $page of $pages");

		$ticket_start = ($page - 1) * 2500;
		if ($ticket_start) {
			$limit = "LIMIT $ticket_start, 2500";
		} else {
			$limit = "LIMIT 2500";
		}

		$data = $db->query_return_array("
			SELECT ticketid, timestamp
			FROM ticket_log
			WHERE actionlog = 'reply_user'
			GROUP BY ticketid
			ORDER BY timestamp DESC
			$limit
		");

		if (is_array($data)) {

			foreach ($data AS $ticket) {

				// no timestamp_lastreply_user has been set
				$db->query("
					UPDATE ticket SET
						timestamp_lastreply_user = $ticket[timestamp]
					WHERE id = $ticket[ticketid]
				");

				// this was set as toggled from before, so only update if we have nothing
				$db->query("
					UPDATE ticket SET
						timestamp_user_waiting = $ticket[timestamp]
					WHERE id = $ticket[ticketid]
						AND status = 'awaiting_tech'
						AND timestamp_user_waiting = 0
				");
			}
		}

		$this->yes();

		$db->free();
		unset($data);

		// do we still have more to do?
		if ($page != $pages) {
			$page++;
			$this->redoStep(6, $page);
		}
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step7($page) {

		global $db, $settings;

		// get total we have (dosen't change)
		$result = $db->query_return("
			SELECT COUNT(distinct ticketid) AS total
			FROM ticket_log
			WHERE actionlog = 'reply_tech'
		");

		$total = $result['total'];

		if (!$total) {
			$this->start("Setting Timestamps");
			$this->yes();
			return;
		}

		$pages = ceil($total / 2500);

		$this->start("Setting Timestamps (Tech Reply). 2,500 Tickets Per Page Load. Page $page of $pages");

		$ticket_start = ($page - 1) * 2500;
		if ($ticket_start) {
			$limit = "LIMIT $ticket_start, 2500";
		} else {
			$limit = "LIMIT 2500";
		}

		$data = $db->query_return_array("
			SELECT ticketid, timestamp
			FROM ticket_log
			WHERE actionlog = 'reply_tech'
			GROUP BY ticketid
			ORDER BY timestamp DESC
			$limit
		");

		if (is_array($data)) {

			foreach ($data AS $ticket) {

				$db->query("
					UPDATE ticket SET
						timestamp_lastreply_tech = $ticket[timestamp]
					WHERE id = $ticket[ticketid]
				");

				$db->query("
					UPDATE ticket SET
						timestamp_tech_waiting = $ticket[timestamp]
					WHERE id = $ticket[ticketid]
						AND status = 'awaiting_user'
						AND timestamp_tech_waiting = 0
				");
			}
		}

		$this->yes();

		$db->free();
		unset($data);

		// do we still have more to do?
		if ($page != $pages) {
			$page++;
			$this->redoStep(7, $page);
		}
	}

	/***************************************************
	* Ticket Times From TicketLog
	***************************************************/

	function step8($page) {

		global $db, $settings;

		// get total we have (dosen't change)
		$result = $db->query_return("
			SELECT COUNT(distinct ticketid) AS total
			FROM ticket_log
			WHERE actionlog = 'reply_tech' OR actionlog = 'reply_user'
		");

		$total = $result['total'];

		if (!$total) {
			$this->start("Setting Timestamps");
			$this->yes();
			return;
		}

		$pages = ceil($total / 2500);

		$this->start("Setting Timestamps (Last Reply). 2,500 Tickets Per Page Load. Page $page of $pages");

		$ticket_start = ($page - 1) * 2500;
		if ($ticket_start) {
			$limit = "LIMIT $ticket_start, 2500";
		} else {
			$limit = "LIMIT 2500";
		}

		$data = $db->query_return_array("
			SELECT ticketid, timestamp
			FROM ticket_log
			WHERE actionlog = 'reply_tech' OR actionlog = 'reply_user'
			GROUP BY ticketid
			ORDER BY timestamp DESC
			$limit
		");

		if (is_array($data)) {

			foreach ($data AS $ticket) {
				$db->query("
					UPDATE ticket SET
						timestamp_lastreply = $ticket[timestamp]
					WHERE id = $ticket[ticketid]
				");
			}
		}

		$this->yes();

		$db->free();
		unset($data);

		// do we still have more to do?
		if ($page != $pages) {
			$page++;
			$this->redoStep(8, $page);
		}

	}

	/***************************************************
	* Ticket Times From TicketLog
	***************************************************/

	function step9($page) {

		global $db, $settings;

		// get total we have (dosen't change)
		$result = $db->query_return("
			SELECT COUNT(distinct ticketid) AS total
			FROM ticket_log
			WHERE actionlog = 'reply_tech'
		");

		$total = $result['total'];

		if (!$total) {
			$this->start("Setting Timestamps");
			$this->yes();
			return;
		}

		$pages = ceil($total / 2500);

		$this->start("Setting Timestamps (First Tech Reply). 2,500 Tickets Per Page Load. Page $page of $pages");

		$ticket_start = ($page - 1) * 2500;
		if ($ticket_start) {
			$limit = "LIMIT $ticket_start, 2500";
		} else {
			$limit = "LIMIT 2500";
		}

		$data = $db->query_return_array("
			SELECT ticketid, timestamp
			FROM ticket_log
			WHERE actionlog = 'reply_tech'
			GROUP BY ticketid
			ORDER BY timestamp ASC
			$limit
		");

		if (is_array($data)) {

			foreach ($data AS $ticket) {
				$db->query("
					UPDATE ticket SET
						timestamp_first_tech_reply = $ticket[timestamp]
					WHERE id = $ticket[ticketid]
				");
			}
		}

		$this->yes();

		$db->free();
		unset($data);

		// do we still have more to do?
		if ($page != $pages) {
			$page++;
			$this->redoStep(9, $page);
		}
	}

	/***************************************************
	* Ticket Times From TicketLog
	***************************************************/

	function step10() {

		global $db, $settings;

		$this->start('Updating Ticket Message Table');

		$db->query_silent_extra("ALTER TABLE tech_ticket_save DROP index `ticketid`");
		$db->query_silent_extra("ALTER TABLE tech_ticket_save DROP index `ticketid_2`");
		$db->query_silent_extra("ALTER TABLE tech_ticket_save ADD UNIQUE (`ticketid`, `techid`)");
		$db->query("ALTER TABLE tech_ticket_save ADD INDEX (`techid`)");

		// USER WAITING WITH NO TECH REPLY
		$db->query("
			UPDATE ticket SET
				timestamp_user_waiting = timestamp_opened
			WHERE status = 'awaiting_tech'
				AND timestamp_first_tech_reply = 0
				AND timestamp_lastreply_tech = 0
		");

		// LANGUAGE
		$db->query("
			UPDATE user SET language = 1 WHERE language = 0
		");
		$db->query("
			UPDATE ticket SET language = 1 WHERE language = 0
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
$upgrade = new upgrade_4();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>