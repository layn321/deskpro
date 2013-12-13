<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: 3_0_0_rc1.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | -
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_5 extends upgrade_base_v3 {

	var $version = '3.0.0 RC 1';

	var $version_number = 5;

	var $pages = array(
		array('Ticket Subjects', 'options.gif'),
		array('Database Changes', 'options.gif')
	);

	/***************************************************
	* dp_html() subjects
	***************************************************/

	function step1($page) {

		global $db, $settings;
	
		if ($page == 1) {
			$page = 0;
		}
	
		$total = $db->query_count('ticket');
	
		if ($page > $total) {
			$this->start("Updating last Ticket Subjects");
		} else {
			$this->start("Updating 5,000 Ticket Subjects ($page of $total so far)");
		}
	
		$tickets = $db->query_return_array("SELECT subject, id FROM ticket LIMIT $page, 5000");
	
		if (is_array($tickets)) {

			foreach ($tickets AS $result) {
				
				$db->query("
					UPDATE ticket SET
						subject = '" . $db->escape(dp_html($result['subject'])) . "'
					WHERE id = $result[id]
				");
				
			}

			$db->free();
			unset($tickets);
	
			$this->yes();
	
			if ($page < $total) {
				
				$page += 5000;
				$this->redoStep(1, $page);
	
			}
		}
	}
		
	/***************************************************
	* Database Changes
	***************************************************/

	function step2() {
		
		global $db, $settings;
	
		$this->start('Adding Secondary Email for Techs');
		$db->query_silent_extra("ALTER TABLE `tech` ADD `email_secondary` VARCHAR( 250 ) NOT NULL ;");
		$this->yes();
	
		$this->start('Update Language Table');
		$db->query("ALTER TABLE `languages` ADD `master` VARCHAR( 20 ) NOT NULL ;");
		$this->yes();
	
		$this->start('Updating instant chat tables');
		$db->query_silent_extra("ALTER TABLE `chat_chat` CHANGE `timestamp_start` `timestamp_start` DECIMAL( 12, 2 ) NOT NULL");
		$db->query_silent_extra("ALTER TABLE `chat_chat` CHANGE `timestamp_end` `timestamp_end` DECIMAL( 12, 2 ) NOT NULL");
		$db->query_silent_extra("ALTER TABLE `chat_chat` CHANGE `timestamp_assigned` `timestamp_assigned` DECIMAL( 12, 2 ) NOT NULL");
		$db->query_silent_extra("ALTER TABLE `chat_chat` ADD `creatoremail` VARCHAR( 250 ) NOT NULL AFTER `creatorname`");
		$db->query_silent_extra("ALTER TABLE `chat_chat` ADD `transcript_sent` INT( 1 ) NOT NULL DEFAULT '0' AFTER `subject`");
	
		$db->query_silent_extra("ALTER TABLE `chat_dep` ADD `is_default` INT( 1 ) NOT NULL DEFAULT '0'");
		$db->query_silent_extra("INSERT INTO `chat_dep` ( `name` , `require_reg` , `staff_only` , `is_default` ) VALUES ( 'Default', '0', '0', '1' )");
	
		$db->query_silent_extra("ALTER TABLE `chat_message` CHANGE `timestamp_sent` `timestamp_sent` DECIMAL( 12, 2 ) NOT NULL");
		$db->query_silent_extra("ALTER TABLE `chat_message` ADD `formatting` ENUM( 'none', 'html' ) NOT NULL DEFAULT 'none' AFTER `message`");
	
		// lets fix tech_ticket_search
		$db->query("RENAME TABLE tech_ticket_search TO ticket_filters");
		$db->query("ALTER TABLE `ticket_filters` CHANGE `id` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ");
		$db->query("ALTER TABLE `ticket_filters` CHANGE `techid` `techid` INT( 10 ) NOT NULL DEFAULT '0'");
		$db->query("ALTER TABLE ticket_filters DROP save_type");
	
		$db->query("
			ALTER TABLE `report_stat`
		  DROP `fixed_general`,
		  DROP `fixed_user`,
		  ADD `ticket_restrictions` mediumtext  NOT NULL,
		  DROP `fixed_ticket`;
		");
	
		// New user_token table
		$db->query("
			CREATE TABLE `user_token` (
				`id` int(10) NOT NULL auto_increment,
				`userid` int(10) unsigned NOT NULL,
				`sessionid` varchar(32) NOT NULL default '',
				`timestamp` int(10) NOT NULL default '0',
				`token_type` varchar(250) NOT NULL default '',
				`token_value` varchar(20) NOT NULL default '',
				PRIMARY KEY  (`id`),
				KEY `timestamp` (`timestamp`)
			)
		");
	
		// New image verify table
		$db->query("
			CREATE TABLE `image_verify` (
				`ref` varchar(50) NOT NULL,
				`newcount` tinyint(4) NOT NULL default '0',
				`code` varchar(255) NOT NULL,
				`timestamp` int(10) unsigned NOT NULL default '0',
				PRIMARY KEY  (`ref`)
			)
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
$upgrade = new upgrade_5();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>