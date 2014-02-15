<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id$
// +-------------------------------------------------------------+
// | File Details:
// | - Upgrade to 3.3.0
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3030001 extends upgrade_base_v3 {

	var $version = '3.3.0';

	var $version_number = 3030001;

	var $pages = array(
		array('Misc. DB Changes', 'options.gif'),
		array('Gateway Process Changes', 'options.gif'),
		array('Hash Tech Passwords', 'options.gif'),
		array('Spam Filtering - DB Changes', 'options.gif'),
	);

	/***************************************************
	* DB Changes
	***************************************************/

	function step1() {

		global $db;

		$this->start('Add new nodisplay status');
		$db->query("ALTER TABLE  `ticket` CHANGE  `nodisplay`  `nodisplay` ENUM(  'spam',  'validate_user',  'validate_tech',  'kb_suggest' ) NULL DEFAULT NULL");
		$db->query("ALTER TABLE  `ticket` ADD  `kb_suggest` ENUM(  'no',  'sent',  'should_send' ) NOT NULL DEFAULT  'no'");
		$this->yes();

		$this->start('Add new table for reply drafts');
		$db->query("
			CREATE TABLE `ticket_message_draft` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `ticketid` int(10) unsigned NOT NULL,
			  `techid` int(10) unsigned NOT NULL,
			  `created_at` int(10) unsigned NOT NULL,
			  `updated_at` int(10) unsigned NOT NULL,
			  `message` text NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `ticketid` (`ticketid`),
			  KEY `techid` (`techid`)
			) ENGINE=MyISAM
		");
		$this->yes();
		
		$this->start('Add new table for company custom fields');
		$db->query("
            	CREATE TABLE `user_company_def` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `name` varchar(250) default NULL,
              `display_name` mediumtext NOT NULL,
              `description` mediumtext,
              `formtype` varchar(250) NOT NULL default 'input',
              `default_value` varchar(250) default NULL,
              `parsed_default_value` varchar(250) default NULL,
              `data` mediumtext NOT NULL,
              `maxoptions` smallint(4) NOT NULL default '0',
              `minoptions` smallint(4) NOT NULL default '0',
              `maxlength` smallint(6) NOT NULL default '0',
              `minlength` smallint(6) NOT NULL default '0',
              `regex` varchar(250) default NULL,
              `error_message` varchar(250) default NULL,
              `required` int(1) NOT NULL default '0',
              `displayorder` int(10) NOT NULL default '0',
              `multiselect` int(1) NOT NULL default '0',
              `display_name_language` mediumtext NOT NULL,
              `description_language` mediumtext NOT NULL,
              `error_language` mediumtext NOT NULL,
              `php_default_value` mediumtext NOT NULL,
              `perm_user_view` enum('user','role','none') NOT NULL default 'user',
              `perm_user_edit` enum('user','role','none') NOT NULL default 'user',
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM
		");
		$this->yes();

		$this->start('Add new field to keep track of failed logins');
		$db->query("ALTER TABLE  `tech_login_log` ADD  `is_failed` TINYINT( 1 ) NOT NULL DEFAULT  '0'");
		$this->yes();

		$this->start('Add new tech option for email on failed logins');
		$db->query("ALTER TABLE  `tech` ADD  `email_on_failed_login` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `email_on_login`");
		$this->yes();

		$this->start('Add last activity fields');
		$db->query("ALTER TABLE  `tech` ADD  `last_activity` INT UNSIGNED NOT NULL AFTER  `forum_expire`");
		$db->query("ALTER TABLE  `user` ADD  `last_activity` INT UNSIGNED NOT NULL AFTER  `date_registered`");
		$this->yes();

		$this->start('Add "show on search page" setting to custom ticket/user fields');
		$db->query("ALTER TABLE  `user_def` ADD  `show_on_search` TINYINT( 1 ) NOT NULL DEFAULT  '1'");
		$db->query("ALTER TABLE  `ticket_def` ADD  `show_on_search` TINYINT( 1 ) NOT NULL DEFAULT  '1'");
		$db->query("ALTER TABLE  `tech` ADD  `fields_show_search` TEXT NOT NULL AFTER  `cats_user`");
		$this->yes();
		
		$this->start('Get rid of obsolete tech preferences');
		$db->query("ALTER TABLE `tech` DROP `copy_to_clipboard`, DROP `pagewidth`");
		$this->yes();
		
		$this->start('Add salt to tech table');
		$db->query('ALTER TABLE `tech` ADD `salt` VARCHAR( 15 ) NOT NULL AFTER `password`');
		$this->yes();
		
		$this->start('Add new ticket-merge permission');
		$db->query("ALTER TABLE `tech` ADD `p_merge_ticket` INT( 1 ) NOT NULL DEFAULT '0' AFTER `p_close_ticket`");
		$db->query("UPDATE tech SET p_merge_ticket = 1");
		$this->yes();
		
		$this->start('Adding new ticket particpants table');
		$db->query("
            CREATE TABLE `ticket_participant` (
              `id` int(10) unsigned NOT NULL auto_increment,
              `user` int(10) unsigned NOT NULL,
              `user_type` enum('user','tech') NOT NULL,
              `ticket` int(10) unsigned NOT NULL,
              `email` varchar(255) NOT NULL,
              `code` varchar(10) default NULL,
              PRIMARY KEY  (`id`),
              UNIQUE KEY `user` (`user`,`user_type`,`ticket`),
              KEY `ticket` (`ticket`)
            ) ENGINE=MyISAM
		");
		$this->yes();
		
		$this->start('Adding new tech participant permissions');
		$db->query("
			ALTER TABLE `tech` ADD `p_manage_participants` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `p_merge_ticket` ,
			ADD `p_manage_participants_other` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `p_manage_participants`
		");
		$this->yes();
		
		$this->start('Adding new tech participant notification settings');
		$db->query("
			ALTER TABLE `tech` ADD `email_add_participant` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `email_faq` ,
			ADD `email_reply_participant` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `email_add_participant`
		");
		$this->yes();
		
		$this->start('Adding default values for tech perms/notification settings');
		$db->query("UPDATE tech SET `p_manage_participants` = 1, `p_manage_participants_other` = 1, `email_add_participant` = 1, `email_reply_participant` = 1");
		$this->yes();
		
		$this->start('Adding new prefs for front tickets grouping on participated');
		$db->query("
			ALTER TABLE `tech` ADD `front_part_1` VARCHAR( 255 ) NOT NULL AFTER `front_other_3` ,
            ADD `front_part_2` VARCHAR( 255 ) NOT NULL AFTER `front_part_1` ,
            ADD `front_part_3` VARCHAR( 255 ) NOT NULL AFTER `front_part_2`
		");
		$this->yes();
		
		$this->start('Add new email uid table');
		$db->query("
			CREATE TABLE `gateway_email_uid` (
              `uid` varchar(255) NOT NULL,
              `account_id` int(10) unsigned NOT NULL,
              PRIMARY KEY  (`uid`,`account_id`)
            ) ENGINE=MyISAM
		");
		$this->yes();
		
		$this->start('Adding index to login log table');
		$db->query("ALTER TABLE `tech_login_log` ADD INDEX ( `techid` )");
		$this->yes();
		
		$this->start('Adding new company permissions');
		$db->query("ALTER TABLE `user_company` ADD `p_ticket_company_addself` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `p_ticket_company_viewothers`");
		$db->query("
    		ALTER TABLE `user_company` ADD `p_company_field_view` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `p_ticket_company_addself` ,
            ADD `p_company_field_edit` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `p_company_field_view`
		");
		$this->yes();
		
		$this->start('Adding new email tpl fields to tech table');
		$db->query("
			ALTER TABLE `tech` ADD `email_tpl` VARCHAR( 50 ) NOT NULL AFTER `last_activity` ,
			ADD `email2_tpl` VARCHAR( 50 ) NOT NULL AFTER `email_tpl`
		");
		$this->yes();
		
		$this->start('Adding new admin notice about the new cron jobs');
        add_admin_notice('upgrade_gateway_crons', 'New Scheduled Tasks For Email Gateways', 'IMPORTANT: You must set up 2 new cron jobs for the email gateways.', '
        	If you are using your servers scheduled task software (cron for *nix, or Scheduled Tasks on Windows), you MUST set up two
        	new tasks. If you do not use the gateways or if you are using DeskPRO to run gateway tasks (<a href="gateway_user.php">see bottom of this page</a>),
        	then you do not need to do anything.
        	<br /><br />
        	The new tasks are:
        	<ul><li>/email/decode.php</li><li>/email/tickets.php</li></ul>
        	More information can be found in <a href="http://helpdesk.deskpro.com/kb_article.php?ref=7386-HJZC-7055">this article</a>.
        ');
	    $this->yes();
	}


	/***************************************************
	* Changes for 3-step decoding process
	***************************************************/

	function step2() {

		global $db;

		$this->start('Re-creating gateway source tables');
		$db->query("DROP TABLE `gateway_source`");
		$db->query("DROP TABLE `gateway_source_parts`");

		$db->query("
			CREATE TABLE `gateway_source` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `size` int(10) unsigned NOT NULL,
			  `gateway` varchar(255) NOT NULL,
			  `accountid` int(10) unsigned NOT NULL,
			  `created_at` int(10) unsigned NOT NULL,
			  `is_inserted` tinyint(1) NOT NULL default '0',
			  `is_toobig` tinyint(1) NOT NULL default '0',
			  `is_decoded` tinyint(1) NOT NULL default '0',
			  `is_complete` tinyint(1) NOT NULL default '0',
			  `in_process` tinyint(1) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `is_inserted` (`is_inserted`),
			  KEY `is_decoded` (`is_decoded`),
			  KEY `in_process` (`in_process`),
			  KEY `is_complete` (`is_complete`)
			) ENGINE=MyISAM
		");

		$db->query("
			CREATE TABLE `gateway_source_parts` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `sourceid` int(10) unsigned NOT NULL,
			  `source` blob NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `sourceid` (`sourceid`)
			) ENGINE=MyISAM
		");
		$this->yes();

		$this->start('Adding new gateway decoded tables');
		$db->query("
			CREATE TABLE `gateway_decoded_parts` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `sourceid` int(10) unsigned NOT NULL,
			  `data` blob NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `sourceid` (`sourceid`)
			) ENGINE=MyISAM
		");
		$this->yes();
	}
	
	/***************************************************
	* Hash tech passwords
	***************************************************/
	
	function step3() {
	    
	    global $db, $cache2;
	    
	    $all_techs = $db->query_return_array("SELECT * FROM tech");
	    
	    foreach ($all_techs as $tech) {
	        
	        // We've already processed it
	        // - Shouldnt really happen, but in the case of a timeout etc
	        if ($tech['salt']) {
	            continue;
	        }
	        
	        $this->start('Processing tech ' . $tech['username']);
	        
			// TODO replace this with something! $salt = Orb_String::random(15);
	        $new_password = hash_tech_password($tech['password'], $salt);
	        
	        $db->query_update('tech', array('password' => $new_password, 'salt' => $salt), "id = {$tech['id']}");
	        
	        $this->yes();
	    }
	}
	
	/***************************************************
	* Create spam filter tables
	***************************************************/
	
	function step4() {
	    
	    global $db;
	    
	    $this->start('Create bayesian categorization table');
	    $db->query("
            CREATE TABLE `spam_filter_cat` (
              `id` int(10) unsigned NOT NULL,
              `word_count` bigint(20) NOT NULL default '0',
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM
	    ");
	    $this->yes();
	    
	    $this->start('Inserting bayesian categories nospam and spam');
	    $db->query("INSERT INTO `spam_filter_cat` (`id`, `word_count`) VALUES (1, 0), (2, 0)");
	    $this->yes();
	    
	    $this->start('Creating filter reference table');
	    $db->query("
            CREATE TABLE `spam_filter_doc` (
              `id` varchar(250) NOT NULL default '',
              `catid` int(10) unsigned NOT NULL,
              `content` text NOT NULL,
              PRIMARY KEY  (`id`),
              KEY `catid` (`catid`)
            ) ENGINE=MyISAM
	    ");
	    $this->yes();
	    
	    $this->start('Creating word frequency table');
	    $db->query("
            CREATE TABLE `spam_filter_word` (
              `word` varchar(250) NOT NULL default '',
              `catid` int(10) unsigned NOT NULL,
              `count` bigint(20) NOT NULL default '0',
              PRIMARY KEY  (`word`,`catid`)
            ) ENGINE=MyISAM
	    ");
	    $this->yes();
	    
	    $this->start('Adding spam fields to ticket table');
	    $db->query("ALTER TABLE `ticket` ADD `is_autospam` TINYINT( 1 ) NOT NULL DEFAULT '0'");
	    $this->yes();
	    
	    $this->start('Adding has_validated to user table');
	    $db->query("ALTER TABLE `user` ADD `has_validated` TINYINT( 1 ) NOT NULL DEFAULT '0'");
	    $this->yes();
	    
	    $this->start('Adding new admin notice about the spam filter');
	    add_admin_notice('upgrade_spamfilter', 'Train Spam Filter', 'Starting in version 3.3, the software now has a built in spam-filter.', '
        	To enable this filter, navigate to the <a href="user_reg_settings.php">User Registration Settings</a>. The filter is only
        	relevant when you do not validate new users, so the option is only available when the "Validation" is set to "Disable Validation
        	Requirements".<br /><br />
        	
        	After you have enabled the spam filter you can jump-start the training process by running the special tool:
        	<a href="gateway_spam_auto_traintickets.php">Auto-Train with Tickets</a>. You should only run the tool once.<br /><br />
        	
        	After, you can mark tickets as spam from the tech interface as usual, and the filter will learn as it goes.
        ');
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
$upgrade = new upgrade_3030001();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));