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

class upgrade_v1 extends upgrade_base_v1 {

	var $pages = array(
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif'),
		array('Datbase Updates', 'database.gif')
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db, $db2, $settings;

		$this->start('Creating Tables');
		
		$db->query("DROP TABLE gateway_error");
		$db->query("DROP TABLE template");

		$db->query("CREATE TABLE `admin_help_cat` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `admin_help_entry` (
		  `id` int(10) NOT NULL auto_increment,
		  `category` int(10) NOT NULL default '0',
		  `entry` mediumtext NOT NULL,
		  `displayorder` int(10) NOT NULL default '0',
		  `title` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `calendar_task_iteration` (
		  `task_techid` int(11) NOT NULL default '0',
		  `taskid` int(11) default NULL,
		  `completed` int(11) default NULL,
		  `date` date default NULL,
		  `time` time default NULL
		) 
		");

		$db->query("CREATE TABLE `cron_options` (
		  `id` int(10) NOT NULL auto_increment,
		  `title` varchar(250) NOT NULL default '',
		  `options` mediumtext NOT NULL,
		  `day` int(2) NOT NULL default '0',
		  `hour` int(1) NOT NULL default '0',
		  `scriptname` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `nextrun` int(10) NOT NULL default '0',
		  `templates` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `data` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `data` longtext NOT NULL,
		  `isdefault` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `escalate` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `dayswaiting` int(10) NOT NULL default '0',
		  `daysopen` int(10) NOT NULL default '0',
		  `category` int(10) NOT NULL default '0',
		  `priority` int(10) NOT NULL default '0',
		  `tech` int(10) NOT NULL default '0',
		  `techemail` varchar(250) NOT NULL default '',
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `faq_cats_related` (
		  `show_cat` int(10) NOT NULL default '0',
		  `related_cat` int(10) NOT NULL default '0'
		) 
		");

		$db->query("CREATE TABLE `faq_comments` (
		  `id` int(10) NOT NULL auto_increment,
		  `userid` int(10) NOT NULL default '0',
		  `useremail` varchar(250) NOT NULL default '',
		  `comments` mediumtext NOT NULL,
		  `articleid` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `faq_keywords` (
		  `articles` varchar(100) NOT NULL default '',
		  `word` varchar(50) NOT NULL default '',
		  PRIMARY KEY  (`word`)
		) 
		");

		$db->query("CREATE TABLE `faq_permissions` (
		  `catid` int(10) NOT NULL default '0',
		  `groupid` int(10) NOT NULL default '0',
		  UNIQUE KEY `groupid` (`groupid`,`catid`)
		) 
		");
		
		$db->query("CREATE TABLE `faq_rating` (
		  `ipaddress` varchar(20) NOT NULL default '',
		  `faqid` int(10) NOT NULL default '0',
		  `rating` int(10) NOT NULL default '0',
		  `timestamp` int(10) NOT NULL default '0',
		  `userid` int(10) NOT NULL default '0',
		  `session` int(10) NOT NULL default '0'
		) 
		");

		$db->query("CREATE TABLE `faq_searchlog` (
		  `id` int(10) NOT NULL auto_increment,
		  `time` int(10) NOT NULL default '0',
		  `query` mediumtext NOT NULL,
		  `results` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `faq_subscriptions` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `catid` int(10) NOT NULL default '0',
		  `articleid` int(10) NOT NULL default '0',
		  `new` int(1) NOT NULL default '0',
		  `edit` int(1) NOT NULL default '0',
		  `userid` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `articleid` (`articleid`),
		  KEY `catid` (`catid`)
		) 
		");
		
		$db->query("CREATE TABLE `faq_word` (
		  `id` int(10) NOT NULL default '0',
		  `word` varchar(250) NOT NULL default '',
		  `distribution` decimal(10,0) NOT NULL default '0',
		  `total` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `faq_wordindex` (
		  `wordid` int(10) NOT NULL default '0',
		  `articleid` int(10) NOT NULL default '0',
		  `distribution` int(10) NOT NULL default '0',
		  `in_keyword` int(1) NOT NULL default '0',
		  `in_comment` int(1) NOT NULL default '0',
		  `in_title` int(1) NOT NULL default '0'
		) 
		");

		$db->query("CREATE TABLE `gateway_auto` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `email` varchar(250) NOT NULL default '',
		  `type` varchar(50) NOT NULL default '',
		  `timestamp` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `gateway_error` (
		  `id` int(10) NOT NULL default '0',
		  `logid` int(10) NOT NULL default '0',
		  `error` varchar(250) NOT NULL default '',
		  `timestamp` int(10) NOT NULL default '0',
		  `sourceid` int(10) NOT NULL default '0'
		) 
		");
		
		$db->query("CREATE TABLE `gateway_pop_accounts` (
		  `id` int(11) NOT NULL auto_increment,
		  `accountid` int(11) default NULL,
		  `server` varchar(64) default NULL,
		  `username` varchar(64) default NULL,
		  `password` varchar(64) default NULL,
		  `target` enum('user','tech','return') NOT NULL default 'user',
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `gateway_pop_failures` (
		  `id` int(11) NOT NULL auto_increment,
		  `messageid` varchar(255) default NULL,
		  `reason` varchar(255) default NULL,
		  `stamp` int(11) default NULL,
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `gateway_source` (
		  `id` int(10) NOT NULL auto_increment,
		  `source` mediumtext NOT NULL,
		  `headers` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `gateway_spam` (
		  `id` int(10) NOT NULL auto_increment,
		  `type` varchar(250) NOT NULL default '',
		  `regex` int(1) NOT NULL default '0',
		  `textmatch` mediumtext NOT NULL,
		  `is_delete` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `languages` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `is_selectable` int(1) NOT NULL default '0',
		  `custom` int(1) NOT NULL default '0',
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `query_log` (
		  `id` int(11) NOT NULL auto_increment,
		  `query` mediumtext ,
		  `explain_log` mediumtext ,
		  `duration` float default NULL,
		  `stamp` int(11) default NULL,
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `quickreply` (
		  `id` int(10) NOT NULL auto_increment,
		  `category` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `global` int(1) NOT NULL default '0',
		  `name` varchar(50) NOT NULL default '',
		  `response` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		"); 
		
		$db->query("CREATE TABLE `quickreply_cat` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `name` varchar(50) NOT NULL default '',
		  `techid` int(10) NOT NULL default '0',
		  `global` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `report` (
		  `id` int(10) NOT NULL auto_increment,
		  `title` varchar(250) NOT NULL default '',
		  `name` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `lastrun` int(10) NOT NULL default '0',
		  `format` varchar(250) NOT NULL default '',
		  `email` varchar(250) NOT NULL default '',
		  `repeattype` varchar(250) NOT NULL default '',
		  `value1` varchar(250) NOT NULL default '',
		  `value2` varchar(250) NOT NULL default '',
		  `style` int(10) NOT NULL default '0',
		  `path` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `report_relations` (
		  `reportid` int(10) NOT NULL default '0',
		  `statid` int(10) NOT NULL default '0'
		) 
		");

		$db->query("CREATE TABLE `report_stat` (
		  `id` int(10) NOT NULL auto_increment,
		  `title` varchar(250) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `variable1` varchar(250) NOT NULL default '',
		  `variable2` varchar(250) NOT NULL default '',
		  `variable1times` mediumtext NOT NULL,
		  `variable2times` mediumtext NOT NULL,
		  `dateaffect` varchar(250) NOT NULL default '',
		  `displaytype` varchar(50) NOT NULL default '',
		  `appendix` int(1) NOT NULL default '0',
		  `displayfields` mediumtext NOT NULL,
		  `fixed_general` mediumtext NOT NULL,
		  `fixed_user` mediumtext NOT NULL,
		  `fixed_ticket` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `report_style` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `title_colour` varchar(250) NOT NULL default '',
		  `description_colour` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  KEY `id` (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `search` (
		  `id` int(10) NOT NULL auto_increment,
		  `query` mediumtext NOT NULL,
		  `results` mediumtext NOT NULL,
		  `time` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `total` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  KEY `techid` (`techid`),
		  KEY `techid_2` (`techid`)
		) 
		");
		
		$db->query("CREATE TABLE `settings_cat` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(255) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  `defaultdisplay` int(1) NOT NULL default '0',
		  `displayorder` int(11) default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `spellwords` (
		  `word` varchar(25) NOT NULL default '0',
		  `soundex` varchar(25) NOT NULL default '',
		  PRIMARY KEY  (`word`),
		  UNIQUE KEY `word` (`word`),
		  KEY `word_2` (`word`),
		  KEY `sx` (`soundex`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_bookmarks` (
		  `id` int(10) NOT NULL auto_increment,
		  `url` varchar(250) NOT NULL default '',
		  `comments` mediumtext NOT NULL,
		  `displayorder` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `name` varchar(250) NOT NULL default '',
		  `category` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_folders` (
		  `techid` int(10) NOT NULL default '0',
		  `type` varchar(250) NOT NULL default '',
		  `categories` mediumtext NOT NULL,
		  UNIQUE KEY `techid` (`techid`,`type`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_help_cat` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_help_entry` (
		  `id` int(10) NOT NULL auto_increment,
		  `category` int(10) NOT NULL default '0',
		  `entry` mediumtext NOT NULL,
		  `displayorder` int(10) NOT NULL default '0',
		  `title` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_internal_help_cat` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_internal_help_entry` (
		  `id` int(10) NOT NULL auto_increment,
		  `category` int(10) NOT NULL default '0',
		  `entry` mediumtext NOT NULL,
		  `displayorder` int(10) NOT NULL default '0',
		  `title` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `tech_news` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `title` varchar(255) NOT NULL default '',
		  `details` mediumtext NOT NULL,
		  `techid` int(10) NOT NULL default '0',
		  `date` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `tech_notes` (
		  `id` int(10) NOT NULL auto_increment,
		  `title` varchar(250) NOT NULL default '',
		  `note` mediumtext NOT NULL,
		  `timestamp` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `category` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_session` (
		  `sessionid` char(32) NOT NULL default '',
		  `techid` int(10) unsigned NOT NULL default '0',
		  `useragent` char(250) NOT NULL default '',
		  `lastactivity` int(10) unsigned NOT NULL default '0',
		  `location` char(255) NOT NULL default '',
		  `language` int(10) NOT NULL default '0',
		  `techzone` int(1) default '0',
		  PRIMARY KEY  (`sessionid`)
		) 
		"); 
		
		$db->query("CREATE TABLE `tech_start_tickets` (
		  `techid` int(10) NOT NULL default '0',
		  `userid` int(10) NOT NULL default '0',
		  KEY `techid` (`techid`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_ticket_save` (
		  `ticketid` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `message` mediumtext NOT NULL,
		  `category` int(10) NOT NULL default '0',
		  `id` int(10) unsigned NOT NULL auto_increment,
		  KEY `techid` (`techid`),
		  KEY `id` (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_ticket_search` (
		  `id` int(11) NOT NULL auto_increment,
		  `techid` int(11) NOT NULL default '0',
		  `save_name` varchar(255) NOT NULL default '',
		  `save_type` enum('search','result') NOT NULL default 'search',
		  `data` mediumtext ,
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `tech_timelog` (
		  `id` int(11) NOT NULL auto_increment,
		  `techid` int(11) default '0',
		  `activity` char(255) default NULL,
		  `stamp` int(11) default NULL,
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `tech_timelog_archive` (
		  `id` int(11) NOT NULL auto_increment,
		  `techid` int(11) default '0',
		  `activity` char(255) default NULL,
		  `stamp` int(11) default NULL,
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `template` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `name` varchar(255) NOT NULL default '',
		  `template` mediumtext ,
		  `category` int(10) default NULL,
		  `description` varchar(255) NOT NULL default '',
		  `upgraded` int(1) NOT NULL default '0',
		  `changed` int(1) NOT NULL default '0',
		  `custom` int(1) NOT NULL default '0',
		  `version_upgrade` int(1) NOT NULL default '0',
		  `template_unparsed` mediumtext NOT NULL,
		  `displayorder` int(3) NOT NULL default '0',
		  `backup` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");

		$db->query("CREATE TABLE `template_cat` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(255) NOT NULL default '',
		  `description` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `template_email` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `name` varchar(255) NOT NULL default '',
		  `template` mediumtext ,
		  `category` varchar(50) NOT NULL default '0',
		  `description` varchar(255) NOT NULL default '',
		  `upgraded` int(1) NOT NULL default '0',
		  `changed` int(1) NOT NULL default '0',
		  `custom` int(1) NOT NULL default '0',
		  `version_upgrade` int(1) NOT NULL default '0',
		  `template_unparsed` mediumtext NOT NULL,
		  `displayorder` int(3) NOT NULL default '0',
		  `backup` int(1) NOT NULL default '0',
		  `language` int(1) NOT NULL default '0',
		  `subject` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  KEY `name` (`name`)
		) 
		");

		$db->query("CREATE TABLE `template_replace` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `name` varchar(75) NOT NULL default '',
		  `value` mediumtext NOT NULL,
		  `description` mediumtext ,
		  `displayorder` int(10) NOT NULL default '0',
		  `custom` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `template_words` (
		  `wordref` varchar(50) NOT NULL default '',
		  `language` int(10) NOT NULL default '0',
		  `text` mediumtext NOT NULL,
		  `category` int(10) NOT NULL default '0',
		  UNIQUE KEY `wordref` (`wordref`,`language`)
		) 
		");

		$db->query("CREATE TABLE `template_words_cat` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `ticket_fielddisplay` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `code` mediumtext NOT NULL,
		  `name` varchar(250) NOT NULL default '',
		  `example` varchar(250) NOT NULL default '',
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `ticket_log` (
		  `id` int(10) NOT NULL auto_increment,
		  `ticketid` int(10) NOT NULL default '0',
		  `actionid` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `timestamp` int(10) NOT NULL default '0',
		  `userid` int(10) NOT NULL default '0',
		  `id_before` int(10) NOT NULL default '0',
		  `id_after` int(10) NOT NULL default '0',
		  `detail_before` mediumtext NOT NULL,
		  `detail_after` mediumtext NOT NULL,
		  `extra` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  KEY `ticketid` (`ticketid`)
		) 
		");
		
		$db->query("CREATE TABLE `ticket_merge` (
		  `old_id` int(10) NOT NULL default '0',
		  `old_ref` varchar(20) NOT NULL default '',
		  `new_id` int(10) NOT NULL default '0',
		  `new_ref` varchar(20) NOT NULL default ''
		) 
		");
		
		$db->query("CREATE TABLE `ticket_message_source` (
		  `messageid` int(10) NOT NULL default '0',
		  `source` mediumtext NOT NULL,
		  PRIMARY KEY  (`messageid`),
		  KEY `id` (`messageid`)
		) 
		");

		$db->query("CREATE TABLE `ticket_notes` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `ticketid` int(10) NOT NULL default '0',
		  `date` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `note` mediumtext NOT NULL,
		  UNIQUE KEY `id` (`id`),
		  KEY `techid` (`techid`)
		) 
		");

		$db->query("CREATE TABLE `user_bill` (
		  `id` int(10) NOT NULL auto_increment,
		  `userid` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `ticketid` int(10) NOT NULL default '0',
		  `time` int(10) NOT NULL default '0',
		  `paid` int(1) NOT NULL default '0',
		  `billable` int(1) default '0',
		  `charge` float default '0',
		  `stamp` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `user_email` (
		  `email` varchar(250) NOT NULL default '',
		  `userid` int(10) NOT NULL default '0',
		  `validated` int(1) NOT NULL default '0',
		  `authcode` varchar(20) NOT NULL default '',
		  KEY `email` (`email`),
		  KEY `userid` (`userid`)
		) 
		");
		
		$db->query("CREATE TABLE `user_groups` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `user_help` (
		  `id` int(10) NOT NULL auto_increment,
		  `category` int(10) NOT NULL default '0',
		  `name` varchar(250) NOT NULL default '',
		  `displayorder` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `category` (`category`)
		) 
		");
		
		$db->query("CREATE TABLE `user_help_cats` (
		  `id` int(10) NOT NULL auto_increment,
		  `displayorder` int(10) NOT NULL default '0',
		  `is_custom` int(1) NOT NULL default '0',
		  `name` varchar(250) NOT NULL default '',
		  KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `user_help_cats_entries` (
		  `id` int(10) NOT NULL auto_increment,
		  `categoryid` int(10) NOT NULL default '0',
		  `languageid` int(10) NOT NULL default '0',
		  `entry` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`)
		) 
		");
		
		$db->query("CREATE TABLE `user_help_entries` (
		  `id` int(10) NOT NULL auto_increment,
		  `helpid` int(10) NOT NULL default '0',
		  `language` int(10) NOT NULL default '0',
		  `title` varchar(250) NOT NULL default '',
		  `helpentry` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `language` (`language`,`helpid`)
		) 
		");
		
		$db->query("CREATE TABLE `user_notes` (
		  `id` int(10) NOT NULL auto_increment,
		  `userid` int(10) NOT NULL default '0',
		  `techid` int(10) NOT NULL default '0',
		  `note` mediumtext NOT NULL,
		  `timestamp` int(10) NOT NULL default '0',
		  `global` int(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`)
		) 
		");

		$db->query("CREATE TABLE `user_session` (
		  `sessionid` char(32) NOT NULL default '',
		  `userid` int(10) unsigned NOT NULL default '0',
		  `useragent` char(250) NOT NULL default '',
		  `lastactivity` int(10) unsigned NOT NULL default '0',
		  `location` char(255) NOT NULL default '',
		  `language` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`sessionid`)
		) 
		");

		$this->yes();
		
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step2() {

		global $db, $db2, $settings;

		$this->start("Updating category table");

		$db->query("RENAME TABLE categories TO ticket_cat");
		
		$db->query("ALTER TABLE ticket_cat 
				ADD COLUMN user_select int(1) NOT NULL DEFAULT '0',
				ADD COLUMN show_category int(10) NOT NULL DEFAULT '0',
				ADD COLUMN require_registration int(1) NOT NULL DEFAULT '0',
				ADD COLUMN auto_assign_tech int(1) NOT NULL DEFAULT '0'
		");
		
		$db->query("UPDATE ticket_cat SET user_select = '1'");

		$this->yes();

		$this->start("Updating priority table");

		$db->query("RENAME TABLE priority TO ticket_pri");
		
		$db->query("ALTER TABLE ticket_pri
				ADD COLUMN user_view int(1) NOT NULL DEFAULT '0',
				ADD COLUMN user_select int(1) NOT NULL DEFAULT '0',
				ADD COLUMN show_priority int(10) NOT NULL DEFAULT '0',
				ADD COLUMN require_registration int(1) NOT NULL DEFAULT '0',
				ADD COLUMN auto_assign_tech int(1) NOT NULL DEFAULT '0'
		");
		
		$db->query("UPDATE ticket_pri SET user_view = '1', user_select = '1'");

		$this->yes();
		
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step3() {

		global $db, $db2, $settings;

		$this->start("Updating banned emails");
		
		$bans = $db->query_return_array_id("SELECT * FROM email_ban", 'email');
		if (is_array($bans)) {
			$bans = array_values($bans);
			$db->query("INSERT INTO data SET name = 'email_ban', data = '" . $db->escape(serialize($bans)) . "', isdefault = '1'");
		}
		$db->query("DROP TABLE email_ban");

		$this->yes();
	
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step4() {

		global $db, $db2, $settings;

		$this->start("Updating gateway accounts");

		$queries = array(
			"RENAME TABLE gateway TO gateway_accounts",
			"ALTER TABLE gateway_accounts CHANGE technician tech int(10) NOT NULL DEFAULT 0,
				ADD COLUMN is_default int(1) NOT NULL DEFAULT 0"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$gateway = $db->query_return("SELECT min(id) AS id FROM gateway_accounts");
		$db->query("UPDATE gateway_accounts SET is_default = '1' WHERE id = '$gateway[id]'");

		$this->yes();
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step5() {

		global $db, $db2, $settings;

		$this->start("Updating announcements");

		$queries = array(
			"RENAME TABLE announcements TO news",
			"ALTER TABLE news ADD COLUMN date int(10) NOT NULL DEFAULT 0,
				ADD COLUMN logged_in int(1) NOT NULL DEFAULT 0,
				ADD COLUMN logged_out int(1) NOT NULL DEFAULT 0",
			"UPDATE news SET date = unix_timestamp()",
			"UPDATE news SET logged_in = 1",
			"UPDATE news SET logged_out = 1"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();
	
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step6() {

		global $db, $db2, $settings;
	
		$this->start("Create private message tables");

		$queries = array(
			"CREATE TABLE pm_relations (
				pmid int(10) NOT NULL DEFAULT 0, 
				techid int(10) NOT NULL DEFAULT 0, 
				is_read int(1) NOT NULL DEFAULT 0
			)",
			"CREATE TABLE pm_source (
				id int(10) NOT NULL AUTO_INCREMENT,
				fromid int(10) NOT NULL default '0', 
				title varchar(250) NOT NULL default '', 
				message mediumtext NOT NULL, 
				timestamp int(10) NOT NULL default '0',
				PRIMARY KEY  (id)
			)"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();

		$messages = $db->query_return_array("SELECT * FROM messages");

		$this->start("Update private messages");
		if (is_array($messages)) {
			foreach ($messages AS $message) {
				$db->query("INSERT INTO pm_source SET
					fromid = '$message[fromid]',
					title = '" . $db->escape($message['subject']) . "',
					message = '" . $db->escape($message['message']) . "',
					timestamp = '$message[send_date]'"
				);
				if($id = $db->insert_id()) {
					$db->query("INSERT INTO pm_relations SET
						pmid = " . intval($id) . ",
						techid = '$message[toid]',
						is_read = '$message[is_read]'"
					);
				}
			}
		}

		$db->query("DROP TABLE messages");
		$this->yes();
	
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step7() {

		global $db, $db2, $settings;

		$this->start("Getting technician data");

		$categories = $db->query_return_array_id("SELECT id FROM ticket_cat", 'id');

		$db->query("SELECT * FROM tech_cat");
		while ($data = $db->row_array()) {
			if ($data['admin_p']) {
				$tech_cats[$data['techid']][] = $data['catid'];
			}
		}

		if (is_array($tech_cats)) {
			foreach ($tech_cats AS $tech => $cats) {
				$tech_cats[$tech] = array_diff($categories, $cats);
			}
		}

		$tech_email = $db->query_return_array("SELECT * FROM tech_email");

		$this->yes();
		$this->start("Removing technician tables");

		$queries = array(
			"DROP TABLE tech_email",
			"CREATE TABLE tech_email ( 
				techid int(10) NOT NULL default '0', 
				fieldname varchar(250) NOT NULL default '', 
				value varchar(250) NOT NULL default '', 
				newreply int(1) NOT NULL default '0', 
				newticket int(1) NOT NULL default '0', 
				email int(1) NOT NULL default '0', 
				sms int(1) NOT NULL default '0', 
				KEY fieldname (fieldname,value)
			)",
			"DROP TABLE tech_sendmail",
			"RENAME TABLE tech_ticketwatch TO tech_ticket_watch",
			"ALTER TABLE tech_ticket_watch ADD COLUMN created int(10) DEFAULT '0',
				CHANGE COLUMN date_todo datetodo date DEFAULT '0000-00-00'"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();

		$this->start("Updating tech email notifications");

		if (is_array($tech_email)) {
			foreach ($tech_email AS $tech_email) {
				if ($tech_email['cat_id']) {
					$data = "fieldname = 'category', value = '$tech_email[cat_id]'";
				} else {
					$data = "fieldname = 'priority', value = '$tech_email[pri_id]'";
				}
				if ($tech_email['email']) {
					$email = 1;
				} else {
					$email = 0;
				}
				$db->query("INSERT INTO tech_email SET
					techid = '$tech_email[tech_id]',
					newreply = '$tech_email[replys]',
					newticket = '$tech_email[tickets]',
					email = '$email',
					sms = '',
					$data"
				);
			}
		}

		$this->yes();
		$this->start("Updating the user table");

		// Now alter and update the actual tech table.

		$queries = array(
			"ALTER TABLE tech DROP COLUMN real_name,
				CHANGE COLUMN all_newticket email_new_email int(1) NOT NULL DEFAULT '0',
				CHANGE COLUMN all_replyticket email_reply_email int(1) NOT NULL DEFAULT '0',
				CHANGE COLUMN all_ownticket email_own_email int(1) NOT NULL DEFAULT '0',
				DROP COLUMN p_cat_control,
				ADD COLUMN fielddisplay MEDIUMTEXT NOT NULL DEFAULT '',
				ADD COLUMN alert_reply_your int(1) NOT NULL DEFAULT '1',
				ADD COLUMN alert_reply_cat int(1) NOT NULL DEFAULT '0',
				ADD COLUMN alert_reply_all int(1) NOT NULL DEFAULT '0',
				ADD COLUMN alert_new_cat int(1) NOT NULL DEFAULT '1',
				ADD COLUMN alert_new_all int(1) NOT NULL DEFAULT '0',
				ADD COLUMN alert_pm int(1) NOT NULL DEFAULT '0',
				ADD COLUMN alert_sound int(1) NOT NULL DEFAULT '0',
				ADD COLUMN alert_popup int(1) NOT NULL DEFAULT '0',
				ADD COLUMN alert_time int(10) NOT NULL DEFAULT '0',
				ADD COLUMN alert_frequency int(1) NOT NULL DEFAULT '0',
				ADD COLUMN cats_user varchar(255) NOT NULL DEFAULT '',
				ADD COLUMN cats_admin varchar(255) NOT NULL DEFAULT '',
				ADD COLUMN email_new_sms int(1) NOT NULL DEFAULT '0',
				ADD COLUMN email_reply_sms int(1) NOT NULL DEFAULT '0',
				ADD COLUMN email_own_sms int(1) NOT NULL DEFAULT '0',
				ADD COLUMN sms varchar(255) NOT NULL DEFAULT '',
				ADD COLUMN faq_editor_yes int(1) NOT NULL DEFAULT '0',
				ADD COLUMN faq_editor_no int(1) NOT NULL DEFAULT '0',
				ADD COLUMN disabled int(1) NOT NULL DEFAULT '0',
				ADD COLUMN email_assigned int(1) NOT NULL DEFAULT '0',
				ADD COLUMN email_pm int(1) NOT NULL DEFAULT '0',
				ADD COLUMN weekstart int(11) NOT NULL DEFAULT '0',
				ADD COLUMN p_approve_new_registrations int(1) NOT NULL DEFAULT '1',
				ADD COLUMN password_cookie varchar(8) NOT NULL DEFAULT '',
				ADD COLUMN disabled_reason varchar(255) NOT NULL DEFAULT '',
				ADD COLUMN email_attachments int(1) NOT NULL DEFAULT '0',
				ADD COLUMN email_own_attachments int(1) NOT NULL DEFAULT '0',
				ADD COLUMN copy_to_clipboard int(1) NOT NULL DEFAULT '0',
				ADD COLUMN p_user_expire int(1) NOT NULL DEFAULT '1',
				ADD COLUMN selected_sound varchar(255) NOT NULL DEFAULT '',
				ADD COLUMN p_quickedit int(1) NOT NULL DEFAULT '1',
				ADD COLUMN p_global_note int(1) NOT NULL DEFAULT '1',
				ADD COLUMN footer int(10) NOT NULL DEFAULT '0'",
			"UPDATE tech SET password_cookie = md5('username' + rand())"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();
		$this->start("Updating tech restricted categories");

		$techs = $db->query_return_array_id("SELECT id FROM tech");

		if (is_array($techs)) {
			foreach($techs AS $tech) {
				if (is_array($tech_cats[$tech['id']])) {
					$db->query("UPDATE tech SET cats_admin = '" . join(',', $tech_cats[$tech['id']]) . "' WHERE id = '$tech[id]'");
				}
			}
		}

		$this->yes();
		
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step8() {

		global $db, $db2, $settings;

		$this->start("Updating calendar tables");

		$tasks = $db->query_return_array("SELECT * FROM tech_todo");

		$queries = array(
			"DROP TABLE tech_todo",
			"CREATE TABLE calendar_task ( 
				id int(10) NOT NULL auto_increment, 
				title varchar(250) NOT NULL default '', 
				description mediumtext NOT NULL, 
				techmaker int(10) NOT NULL default '0', 
				multistaff int(1) NOT NULL default '0', 
				globalcomplete int(1) NOT NULL default '0', 
				notifycompletion int(1) NOT NULL default '0', 
				repeattype int(1) NOT NULL default '0', 
				value1 int(10) NOT NULL default '0', 
				value2 varchar(250) NOT NULL default '0', 
				starttime time NOT NULL default '00:00:00', 
				startdate date NOT NULL default '0000-00-00', 
				enddate date NOT NULL default '0000-00-00', 
				endtime time NOT NULL default '00:00:00', 
				PRIMARY KEY  (id), 
				UNIQUE KEY id (id), 
				KEY repeattype (repeattype), 
				KEY startdate (startdate), 
				KEY enddate (enddate))",
			"CREATE TABLE calendar_task_tech ( 
				id int(10) NOT NULL auto_increment, 
				eventid int(10) NOT NULL default '0', 
				email_due int(1) NOT NULL default '0', 
				email_before1 int(3) NOT NULL default '0', 
				email_before2 int(3) NOT NULL default '0', 
				techid int(1) NOT NULL default '0', 
				completed int(1) NOT NULL default '0', 
				stamp int(10) default '0', 
				PRIMARY KEY  (id), 
				KEY eventid (eventid))"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();
		$this->start("Updating calendar tasks");

		if (is_array($tasks)) {
			foreach ($tasks AS $task) {
				$db->query("INSERT INTO calendar_task SET
					title = '" . $db->escape($task['title']) . "',
					description = '" . $db->escape($task['todo']) . "',
					startdate = '" . date('Y-m-d', $task['date_added']) . "',
					enddate = '" . $db->escape($task['date_todo']) . "',
					techmaker = '" . $db->escape($task['techid']) . "'"
				);
				$id = $db->insert_id();
				$db->query("INSERT INTO calendar_task_tech SET
					eventid = " . intval($id) . ",
					techid = '" . $db->escape($task['techid']) . "',
					completed = '" . $db->escape($task['completed']) . "',
					stamp = '" . $db->escape($task['date_added']) . "'"
				);
			}
		}

		$this->yes();
		
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step9() {

		global $db, $db2, $settings;

		$this->start("Updating user custom field table");

		$queries = array(
			"RENAME TABLE user_table TO user_def",
			"UPDATE user_def SET formtype = 'input' WHERE formtype = 'textfield'",
			"ALTER TABLE user_def CHANGE reg_ex regex varchar(255),
				CHANGE formlength length int(10),
				CHANGE rows height int(10),
				CHANGE field_order displayorder int(10),
				CHANGE description description mediumtext,
				CHANGE display_name display_name mediumtext,
				CHANGE parse_default_value parsed_default_value varchar(255),
				DROP COLUMN sql_type,
				DROP COLUMN listvalues,
				DROP COLUMN admin_editable,
				CHANGE formtype formtype enum('input','select','textarea','multiselect','radio','checkbox','system') DEFAULT 'select',
				ADD COLUMN data mediumtext,
				ADD COLUMN extrainput int(1) DEFAULT '0',
				ADD COLUMN maxoptions smallint(4) DEFAULT '0',
				ADD COLUMN minoptions smallint(4) DEFAULT '0',
				ADD COLUMN minlength smallint(6) DEFAULT '0',
				ADD COLUMN maxlength smallint(6) DEFAULT '0',
				ADD COLUMN error_message varchar(255),
				ADD COLUMN required int(1) DEFAULT '0',
				ADD COLUMN perline int(2) DEFAULT '0',
				ADD COLUMN extrainput_location int(1) DEFAULT '0',
				ADD COLUMN extrainput_text varchar(255) DEFAULT '0',
				ADD COLUMN multiselect int(1) DEFAULT '0'",
			"DELETE FROM user_def WHERE name NOT like '%custom%'"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();
		$this->start("Updating user custom fields");

		$user_customs = $db->query_return_array("SELECT * FROM user_def WHERE name like '%custom%'");

		if (is_array($user_customs)) {
			foreach ($user_customs AS $user_custom) {
				if ($user_custom['formtype'] == 'radio' or $user_custom['formtype'] == 'select') {
					// We have to load the data array
					$values = explode('###', $user_custom['listvalues']);
					if (is_array($values)) {
						$i = 0;
						$data = array();
						foreach($values AS $value) {
							$data[] = array($i, $i, $value, 0);
						}
					}
				}
				$data = $db->escape(serialize($data));
				$display_name = serialize(array('1' => $user_custom['display_name']));
				$description = serialize(array('1' => $user_custom['description']));
				$error_message = serialize(array('1' => ''));
				$display_name = $db->escape($display_name);
				$error_message = $db->escape($error_message);
				$description = $db->escape($description);
				$db->query("UPDATE user_def SET
					data = '$data',
					display_name = '$display_name',
					error_message = '$error_message',
					description = '$description'
					WHERE id = '$user_custom[id]'"
				);
			}
		}

		$this->yes();
	
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step10() {

		global $db, $db2, $settings;

		$this->start("Updating ticket custom field table");

		$queries = array(
			"RENAME TABLE ticket_table TO ticket_def",
			"UPDATE ticket_def SET formtype = 'input' WHERE formtype = 'textfield'",
			"ALTER TABLE ticket_def CHANGE reg_ex regex varchar(255),
				CHANGE formlength length int(10),
				CHANGE rows height int(10),
				CHANGE field_order displayorder int(10),
				CHANGE description description mediumtext,
				CHANGE display_name display_name mediumtext,
				CHANGE user_start ticket_start int(1),
				CHANGE parse_default_value parsed_default_value varchar(255),
				DROP COLUMN sql_type,
				DROP COLUMN listvalues,
				DROP COLUMN admin_editable,
				CHANGE formtype formtype enum('input','select','textarea','multiselect','radio','checkbox','system') DEFAULT 'select',
				ADD COLUMN data mediumtext,
				ADD COLUMN extrainput int(1) DEFAULT '0',
				ADD COLUMN maxoptions smallint(4) DEFAULT '0',
				ADD COLUMN minoptions smallint(4) DEFAULT '0',
				ADD COLUMN minlength smallint(6) DEFAULT '0',
				ADD COLUMN maxlength smallint(6) DEFAULT '0',
				ADD COLUMN error_message varchar(255),
				ADD COLUMN required int(1) DEFAULT '0',
				ADD COLUMN perline int(2) DEFAULT '0',
				ADD COLUMN extrainput_location int(1) DEFAULT '0',
				ADD COLUMN extrainput_text varchar(255) DEFAULT '0',
				ADD COLUMN multiselect int(1) DEFAULT '0'",
			"DELETE FROM ticket_def WHERE name NOT like '%custom%'"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();
		$this->start("Updating ticket custom fields");

		$ticket_customs = $db->query_return_array("SELECT * FROM ticket_def WHERE name like '%custom%'");

		if (is_array($ticket_customs)) {
			foreach ($ticket_customs AS $ticket_custom) {
				if ($ticket_custom['formtype'] == 'radio' or $ticket_custom['formtype'] == 'select') {
					// We have to load the data array
					$values = explode('###', $ticket_custom['listvalues']);
					$data = array();
					if (is_array($values)) {
						$i = 0;
						foreach($values AS $value) {
							$data[] = array($i, $i, $value, 0);
						}
					}
				}
				$data = $db->escape(serialize($data));
				$display_name = array('1' => $ticket_custom['display_name']);
				$description = array('1' => $user_custom['description']);
				$error_message = array('1' => '');
				$display_name = $db->escape(serialize($display_name));
				$error_message = $db->escape(serialize($error_message));
				$description = $db->escape(serialize($description));
				$db->query("UPDATE ticket_def SET
					data = '$data',
					display_name = '$display_name',
					error_message = '$error_message'
					WHERE id = '$ticket_custom[id]'"
				);
			}
		}
		
		$db->query(
			"CREATE TABLE user2 ( 
				id int(10) unsigned NOT NULL auto_increment, 
				username varchar(250) NOT NULL default '', 
				email varchar(250) NOT NULL default '', 
				validate_key varchar(6) NOT NULL default '0', 
				awaiting_register_validate_user int(1) NOT NULL default '0', 
				password varchar(250) NOT NULL default '', 
				date_registered int(10) NOT NULL default '0', 
				language int(10) NOT NULL default '0', 
				password_cookie varchar(8) NOT NULL default '', 
				password_url varchar(8) NOT NULL default '', 
				autoresponds int(1) NOT NULL default '0', 
				disabled int(1) NOT NULL default '0', 
				awaiting_register_validate_tech int(1) NOT NULL default '0', 
				expire_tickets int(11) default NULL, 
				expire_date int(10) unsigned default NULL, 
				expire_type enum('none','ticket','date','both') default 'none', 
				disabled_reason varchar(255) default NULL, 
				timezone varchar(32) DEFAULT '', 
				PRIMARY KEY  (id), 
				UNIQUE KEY id (id)) 
		");	
			
		$user_fields = $db->query_return_array("SELECT name FROM user_def");
		if (is_array($user_fields)) {
			foreach ($user_fields AS $field) {
				$db->query("ALTER TABLE user2 ADD $field[name] MEDIUMTEXT");
			}
		}		

		$this->yes();
		
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step11() {

		global $db, $db2, $settings;

		$user_fields = $db->query_return_array("SELECT name FROM user_def");

		function make_username_2($email) {
			$username = substr($email, 0, strpos($email, '@'));
			$username = preg_replace('([^_a-zA-Z0-9\-\.])', '', $username);
			return trim($username);
		}
		
		$users = $db->query_return_array("SELECT * FROM user LIMIT 5000");
		$this->start("Updating " . $db->num_rows() . " Users");

		if (is_array($users)) {
			
			$data = array();
			$usernames = array();
			$total = 0;
			
			foreach ($users AS $user) {
				
				$ids[] = $user['id'];
				
				$hash = md5($username . TIMENOW . rand());
				$hash = $db->escape(substr($hash, 0, 8));
				$username = make_username_2($user['email']);
				$i = 0;
				if (in_array($username, $usernames)) {
					$i++;
					while (in_array(($username . $i), $usernames)) {
						$i++;
					}
					$username = ($username . $i);
				}
				$usernames[] = $username;
				$data_tmp = array(
					$user['id'], # ID
					$username, # Username
					$user['email'], # Email
					$user['validate_number'], # Validate_key
					0, # awaiting_register_validate_user
					$user['password'], # password
					mktime(), # date_registered
					0, # language
					$hash, # password_cookie
					$hash, # password_url
					0, # autoresponds
					0, # disabled
					0, # awaiting_register_validate_tech
					NULL, # expire_tickets
					NULL, # expire_date
					'none', # expire_type
					NULL, # disabled_reason
					''); # timezone

				if (is_array($user_fields)) {
					foreach ($user_fields AS $field) {
						$data_tmp[] = $user[$field['name']];
					}
				}
				
				$data[] = $data_tmp;
				$count++;
				if ($count >= 100) {
					$db->query("INSERT INTO user2 VALUES " . multi_array2sql($data));
					$db->query("DELETE FROM user WHERE id IN " . array2sql($ids));
					unset($ids);
					unset($data);
					$total += $count;
					$count = 0;
				}
			}
			
			if ($count) {
				$db->query("INSERT INTO user2 VALUES " . multi_array2sql($data));
				$db->query("DELETE FROM user WHERE id IN " . array2sql($ids));
			}
		}

		$this->yes();
		
		$total = $db->query_count('user');
		if ($total) {
			echo "$total Users remaining";
			$this->redoStep(11);		
		}
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step12() {

		global $db, $db2, $settings;

		$this->start("Updating ticket table");
		
		$db->query("DROP TABLE user");
		$db->query("RENAME TABLE user2 TO user");	

		$queries = array(
			"ALTER TABLE ticket
				CHANGE COLUMN user_id userid int(10) NOT NULL default '0', 
				CHANGE COLUMN admin_owner tech int(10) NOT NULL default '0', 
				ADD COLUMN language int(10) NOT NULL default '0', 
				CHANGE COLUMN priority priority int(10) NOT NULL default '0',
				CHANGE COLUMN category category int(10) NOT NULL default '0', 	
				CHANGE COLUMN awaiting_reply awaiting_tech int(1) NOT NULL default '0', 
				CHANGE COLUMN date_started date_opened int(10) NOT NULL default '0', 
				CHANGE COLUMN last_reply date_lastreply int(10) NOT NULL default '0', 
				ADD COLUMN date_lastreply_tech int(10) NOT NULL default '0', 
				CHANGE COLUMN lock_id lock_techid int(10) NOT NULL default '0', 
				CHANGE COLUMN onhold_date date_locked int(10) NOT NULL default '0', 
				ADD COLUMN ref varchar(20) NOT NULL default '', 
				ADD COLUMN gatewayid int(10) NOT NULL default '0', 
				ADD COLUMN ticketemail varchar(250) NOT NULL default '', 
				ADD COLUMN nodisplay int(1) NOT NULL default '0', 
				ADD COLUMN date_awaiting_toggled INT(10) NOT NULL DEFAULT '0', 
				CHANGE COLUMN ticket_pass authcode varchar(8) NOT NULL default '',
				DROP COLUMN gateway,
				DROP COLUMN is_email,
				ADD KEY userid (userid), 
				ADD KEY techid (tech), 
				ADD KEY category (category), 
				ADD KEY priority (priority), 
				ADD KEY ref (ref), 
				ADD KEY is_open (is_open), 
				ADD KEY awaiting_tech (awaiting_tech), 
				ADD KEY nodisplay (nodisplay)
			",
			"RENAME TABLE reply TO ticket_message",
			"ALTER TABLE ticket_message
				CHANGE COLUMN admin_id techid int(1) NOT NULL DEFAULT '0',
				ADD COLUMN sourceid int(10) NOT NULL DEFAULT '0',
				ADD COLUMN userid int(10) NOT NULL DEFAULT '0',
				ADD COLUMN striptags int(1) NOT NULL DEFAULT '0'",
			"CREATE INDEX techid ON ticket_message (techid)"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();
		
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step13() {

		global $db, $db2, $settings;

		$this->start("Updating 5,000 tickets");
		
		// make lots of refs
		for ($i = 0; $i < 5000; $i++) {
			$ref = make_ref();
			$refs[$ref] = $ref;
		}
		
		$refs = array_unique($refs);
		
		// check we are not using any
		$db->query("SELECT ref FROM ticket WHERE ref IN " . array2sql($refs));
		while ($result = $db->row_array()) {
			unset($refs[$result['ref']]);
		}
		
		foreach ($refs AS $key => $var) {
			$db->query("UPDATE ticket SET ref = '$var' WHERE ref = '' LIMIT 1");
		}

		$this->yes();
		
		$total = $db->query_count('ticket', "ref = ''");

		if ($total) {
			echo "$total tickets remaining";
			$this->redoStep(13);
		}
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step14() {

		global $db, $db2, $settings;

		$this->start("Create & Alter attachment tables");

		$queries = array(
			"CREATE TABLE faq_attachments ( 
				id int(10) NOT NULL auto_increment, 
				blobid int(10) NOT NULL default '0', 
				filename varchar(255) NOT NULL default '0', 
				filesize varchar(255) NOT NULL default '0', 
				extension varchar(10) NOT NULL default '0', 
				articleid int(10) NOT NULL default '0', 
				techid int(10) NOT NULL default '0', 
				timestamp int(10) NOT NULL default '0', 
				PRIMARY KEY  (id), 
				UNIQUE KEY id (id), 
				KEY articleid (articleid))",
			"CREATE TABLE tech_attachments ( 
				id int(10) NOT NULL auto_increment, 
				blobid int(10) NOT NULL default '0', 
				filename varchar(250) NOT NULL default '', 
				filesize varchar(50) NOT NULL default '', 
				techid int(10) NOT NULL default '0', 
				category int(10) NOT NULL default '0', 
				extension varchar(10) NOT NULL default '', 
				timestamp int(10) NOT NULL default '0', 
				comments mediumtext NOT NULL, 
				PRIMARY KEY  (id), 
				UNIQUE KEY id (id))",
			"CREATE TABLE blobs ( 
				id int(10) unsigned NOT NULL auto_increment, 
				blobdata longblob NOT NULL, 
				PRIMARY KEY  (id), 
				UNIQUE KEY id (id))",
			"RENAME TABLE attachments TO ticket_attachments",
			"ALTER TABLE ticket_attachments
				DROP COLUMN description,
				DROP COLUMN uploaded,
				CHANGE COLUMN filetype extension varchar(5) NOT NULL DEFAULT '0',
				ADD COLUMN blobid int(10) NOT NULL DEFAULT '0',
				ADD COLUMN techid int(10) NOT NULL DEFAULT '0',
				ADD COLUMN userid int(10) NOT NULL DEFAULT '0',
				ADD COLUMN temporaryid int(10) NOT NULL DEFAULT '0',
				ADD COLUMN timestamp int(10) NOT NULL DEFAULT '0',
				ADD COLUMN toemail int(1) NOT NULL DEFAULT '0'",
			"ALTER TABLE ticket_attachments ADD INDEX (blobid)"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();
	}
	
	/***************************************************
	* Database changes
	***************************************************/

	function step15() {

		global $db, $db2, $settings;

		$this->start("Updating 100 attachments");
		
		require_once(INC . 'functions/attachment_functions.php');

		for ($i = 0; $i < 10; $i++) {

			$db->query("SELECT * FROM ticket_attachments WHERE blobid = 0 LIMIT 10");
		
			while ($result = $db->row_array()) {
		
				$db2->query("
					INSERT INTO blobs SET
						blobdata  = '" . $db->escape($result['attachment']) . "'
				");
		
				$id = $db2->insert_id();
				
				$db2->query("
					UPDATE ticket_attachments SET
						blobid = '" . $id . "',
						extension = '" . addslashes(attachment_extension($result['filename'])) . "'
					WHERE id = '$result[id]'
				");
			}
		}

		$this->yes();

		$total = $db->query_count('ticket_attachments', "blobid = 0");

		if ($total) {
			echo "$total attachments remaining";
			$this->redoStep(15);
		}
	
	}

	/***************************************************
	* Database changes
	***************************************************/

	function step16() {

		global $db, $db2, $settings;

		$this->start("Updating knowledge base");
		
		$db->query("ALTER TABLE ticket_attachments DROP attachment");

		$queries = array(
			"ALTER TABLE ticket_attachments DROP INDEX `blobid`",
			"RENAME TABLE kb_cats TO faq_cats",
			"ALTER TABLE faq_cats
				CHANGE COLUMN article_number totalarticles int(10),
				ADD COLUMN articles int(10) NOT NULL DEFAULT '0',
				ADD COLUMN p_loggedin int(1) NOT NULL DEFAULT '0',
				ADD COLUMN p_restricted int(1) NOT NULL DEFAULT '0',
				ADD COLUMN parentlist varchar(250) NOT NULL DEFAULT '',
				ADD COLUMN newdate int(10) NOT NULL DEFAULT '0',
				ADD COLUMN editdate int(10) NOT NULL DEFAULT '0'",
			"UPDATE faq_cats SET newdate = unix_timestamp(), editdate = unix_timestamp()",
			"RENAME TABLE kb_articles TO faq_articles",
			"ALTER TABLE faq_articles
				CHANGE COLUMN made_by techid_made int(10) NOT NULL DEFAULT '0',
				CHANGE COLUMN modified_by techid_modified int(10) NOT NULL DEFAULT '0',
				CHANGE COLUMN category_id category int(10) NOT NULL DEFAULT '0',
				ADD COLUMN show_order int(10) NOT NULL DEFAULT '0',
				ADD COLUMN to_validate int(10) NOT NULL DEFAULT '0',
				ADD COLUMN keywords mediumtext NOT NULL DEFAULT '',
				ADD COLUMN userid int(10) NOT NULL DEFAULT '0',
				ADD COLUMN question_html int(1) NOT NULL DEFAULT '0',
				ADD COLUMN answer_html int(1) NOT NULL DEFAULT '0',
				ADD COLUMN rating int(10) NOT NULL DEFAULT '0',
				ADD COLUMN votes int(10) NOT NULL DEFAULT '0',
				ADD COLUMN ref varchar(20) NOT NULL DEFAULT ''",
			"RENAME TABLE kb_related TO faq_articles_related"
		);

		// run the queries
		foreach ($queries AS $key => $var) {
			$db->query($var);
		}

		$this->yes();
		$this->start("Updating knowledge base articles");

		$faqs = $db->query_return_array_id("SELECT id FROM faq_articles", 'id');
		if (is_array($faqs)) {
			foreach ($faqs AS $id) {
				$db->query("UPDATE faq_articles SET ref = '" . make_table_ref('faq_articles') . "' WHERE id = $id");
			}
		}

		$db->query("SELECT keywords, id FROM faq_articles");
		while ($result = $db->row_array()) {
			$words = explode(',', $result['keywords']);
			if (@is_array($words)) {
				foreach($words AS $key => $var) {
					if (trim($var) != '') {
						if ($data[$var]) {
							$data[$var] .= ',' . $result['id'];
						} else {
							$data[$var] = $result['id'];
						}
					}
				}
			}
		}

		$db->query("DELETE FROM faq_keywords");
		if (is_array($data)) {
			$db->query("INSERT INTO faq_keywords (word, articles) VALUES " . insertsql($data));
		}

		$this->yes();

		$this->start("Updating version number to v2.0.0");
		$db->query("UPDATE settings SET value = '2.0.0' WHERE settings = 'deskpro_version'");
		$this->yes();

	}

	/***************************************************
	* Database changes
	***************************************************/

	function step17() {

		global $db, $db2, $settings;

		$this->start("Loading Installation Data");
		
		$db->query("INSERT INTO user_groups VALUES (1,'Guests');");
		$db->query("INSERT INTO user_groups VALUES (2,'Registered');");
		
		$db->query("INSERT INTO settings SET name = 'deskpro_version_internal', value = 0");
		
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
$upgrade = new upgrade_v1();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>