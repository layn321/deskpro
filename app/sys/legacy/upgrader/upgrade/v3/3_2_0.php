<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id$
// +-------------------------------------------------------------+
// | File Details:
// | - Upgrade to 3.2.0 gold
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3020001 extends upgrade_base_v3 {

	var $version = '3.2.0';

	var $version_number = 3020001;

	var $pages = array(
		array('Chat Changes', 'options.gif'),
		array('Tracking Changes', 'options.gif'),
		array('Misc Changes', 'options.gif'),
	);

	/***************************************************
	* Chat changes
	***************************************************/

	function step1() {

		global $db;

		$this->start('Add canned response table');
		$db->query("
			CREATE TABLE `chat_canned` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`techid` INT NOT NULL ,
			`category` VARCHAR( 255 ) NOT NULL ,
			`name` VARCHAR( 255 ) NOT NULL ,
			`content` TEXT NOT NULL
			)
		");
		$this->yes();
		
		$this->start('Modify tech table');
		$db->query("ALTER TABLE  `tech` ADD  `chat_prefs` TEXT NOT NULL AFTER  `chat_timestamp_ping`");
		$db->query("ALTER TABLE  `tech` ADD  `chat_timestamp_lastauto` INT NOT NULL AFTER  `chat_prefs`");
		$db->query("
			ALTER TABLE  `tech` ADD  `p_chat` INT( 1 ) NOT NULL DEFAULT  '0' AFTER  `chat_timestamp_lastauto` ,
			ADD  `p_chat_global_canned` INT( 1 ) NOT NULL DEFAULT  '0' AFTER  `p_chat`
		");
		$db->query("ALTER TABLE  `tech` ADD  `deny_normal_access` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `is_admin`");
		$db->query("ALTER TABLE  `tech` ADD  `chat_away` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `chat_timestamp_ping`");
		$db->query("ALTER TABLE  `tech` ADD  `chat_state` TEXT NOT NULL AFTER  `chat_prefs`");
		$this->yes();
		
		$this->start('Modify chat departments table');
		$db->query("ALTER TABLE  `chat_dep` ADD  `techs` TEXT NOT NULL AFTER  `name`");
		$this->yes();
		
		$this->start('Modify chat table');
		$db->query("
			ALTER TABLE  `chat_chat` ADD  `rating` TINYINT( 100 ) NOT NULL DEFAULT  '-1' AFTER  `subject` ,
			ADD  `feedback` TEXT NOT NULL AFTER  `rating`
		");
		$db->query("ALTER TABLE  `chat_chat` ADD  `timestamp_timeout` INT NOT NULL AFTER  `timestamp_ping`");
		$db->query("ALTER TABLE  `chat_chat` ADD  `is_proactive_chat` TINYINT( 1 ) NOT NULL AFTER  `feedback`");
		$db->query("ALTER TABLE `chat_chat` DROP `transcript_sent`");
		$db->query("ALTER TABLE  `chat_chat` ADD  `has_started` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `is_proactive_chat`");
		$db->query("ALTER TABLE  `chat_chat` ADD  `techid_proactive_chat` INT UNSIGNED NOT NULL AFTER  `is_proactive_chat`");
		$this->yes();
		
		$this->start('Modify usergroup table for chat permission');
		$db->query("ALTER TABLE  `user_groups` ADD  `p_chat` TINYINT( 1 ) NOT NULL");
		$this->yes();
		
		$this->start('Creating chat departments permissions table');
		$db->query("
			CREATE TABLE  `chat_dep_perms` (
			`depid` INT UNSIGNED NOT NULL ,
			`groupid` INT UNSIGNED NOT NULL ,
			PRIMARY KEY (  `depid` ,  `groupid` )
			)
		");
		$this->yes();
		
		$this->start('Give default chat prefs');
		$db->query("
			UPDATE tech
			SET chat_prefs = 'a:2:{s:16:\"sound_my_new_msg\";s:9:\"chat1.wav\";s:19:\"sound_wait_new_chat\";s:10:\"online.wav\";}'
		");
		$this->yes();
	}
	
	function step2() {
		
		global $db;
		
		$this->start('Re-create tracking table');
		$db->query("DROP TABLE `tracking`");
		$db->query("
			CREATE TABLE `tracking` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `userid` int(10) unsigned NOT NULL,
			  `sessionid` varchar(32) NOT NULL,
			  `external_id` varchar(32) NOT NULL,
			  `timestamp_created` int(11) NOT NULL,
			  `timestamp_visit` int(11) NOT NULL,
			  `timestamp_last` int(11) NOT NULL,
			  `lastlogid` int(10) unsigned NOT NULL,
			  `logcount` int(10) unsigned NOT NULL,
			  `chatinit_timestamp` int(10) unsigned NOT NULL,
			  `chatinit_techid` int(10) unsigned NOT NULL,
			  `chatinit_message` varchar(255) NOT NULL,
			  `chatinit_accepted` tinyint(1) NOT NULL,
			  `chatinit_ignored` tinyint(1) NOT NULL,
			  `chatinit_disable` tinyint(1) NOT NULL default '0',
			  PRIMARY KEY  (`id`),
			  KEY `userid` (`userid`,`sessionid`),
			  KEY `external_id` (`external_id`)
			)
		");
		$this->yes();
		
		$this->start('Create tracking log table');
		$db->query("
			CREATE TABLE `tracking_log` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `trackid` int(10) unsigned NOT NULL,
			  `userid` int(10) unsigned NOT NULL,
			  `sessionid` varchar(32) NOT NULL,
			  `locid` int(10) unsigned NOT NULL,
			  `ipaddr` varchar(50) NOT NULL,
			  `useragent` text NOT NULL,
			  `pagetitle` text NOT NULL,
			  `pageinfo` text NOT NULL,
			  `data` text NOT NULL,
			  `url` text NOT NULL,
			  `ref` text NOT NULL,
			  `timestamp` int(11) NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `trackid` (`trackid`),
			  KEY `userid` (`userid`)
			)
		");
		$this->yes();
		
		$this->start('Create tracking search table');
		$db->query("
			CREATE TABLE `tracking_search` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `trackid` int(10) unsigned NOT NULL,
			  `logid` int(10) unsigned NOT NULL,
			  `botname` varchar(255) NOT NULL,
			  `query` text NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();
		
		$this->start('Add tracking location groups table');
		$db->query("
			CREATE TABLE `tracking_loc_group` (
			  `sysname` varchar(50) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  PRIMARY KEY  (`sysname`)
			)
		");	
		$this->yes();
		
		$this->start('Add tracking location table');
		$db->query("
			CREATE TABLE `tracking_loc` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `locgroup` varchar(50) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `pattern` text NOT NULL,
			  `infoclass` varchar(255) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();
	}
	
	function step3() {
		
		global $db;
		
		$this->start('Adding published field to KB articles table');
		$db->query("ALTER TABLE  `faq_articles` ADD  `published` TINYINT( 1 ) NOT NULL DEFAULT  '0'");
		$db->query("UPDATE faq_articles SET published = 1");
		$this->yes();
		
		$this->start('Create service appkey table');
		$db->query("
			CREATE TABLE `service_appkey` (
			  `appkey` varchar(50) NOT NULL,
			  `comment` varchar(255) NOT NULL,
			  `access` text NOT NULL,
			  PRIMARY KEY  (`appkey`)
			)
		");
		$this->yes();
		
		$this->start('Make sure mapid in user_email is not null');
		$table_info = $db->query_return_array("DESCRIBE user_email", 'Field');
		$map_field = $table_info['mapid'];
		
		if ($map_field['Null'] == 'YES') {
			$db->query("ALTER TABLE  `user_email` CHANGE  `mapid`  `mapid` INT( 10 ) NOT NULL DEFAULT  '0'");
		}
		$this->yes();

		$this->start('Adding global flag to ticket fields table');
		$db->query("ALTER TABLE  `ticket_def` ADD  `is_global` TINYINT( 1 ) NOT NULL DEFAULT  '0'");
		$this->yes();
		
		$this->start('Adding notifier grouping fields to tech table');
		$db->query("
			ALTER TABLE  `tech` ADD  `notifier_1_level` VARCHAR( 60 ) NOT NULL DEFAULT  'category' AFTER  `notifier_plugin` ,
			ADD  `notifier_2_level` VARCHAR( 60 ) NOT NULL DEFAULT  'workflow' AFTER  `notifier_1_level`
		");
		$this->yes();
		
		$this->start('Adding info dismiss field to tech table');
		$db->query("ALTER TABLE  `tech` ADD  `info_dismiss` TEXT NOT NULL AFTER  `cats_user`");
		$this->yes();
		
		$this->start('Adding failed emails tables');
		$db->query("
			CREATE TABLE `failed_email` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `timestamp` int(10) NOT NULL,
			  `failcount` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		
		$db->query("
			CREATE TABLE `failed_email_part` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `failed_email_id` int(10) unsigned NOT NULL,
			  `data` longblob NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `failed_email_id` (`failed_email_id`)
			)
		");
		$this->yes();
		
		// Note about phpcode being changed
		add_admin_notice(
			'upgrade_phpcode_changed',
			'Custom Fields "Parsed Default Value" Change',
			'The "Parsed Default Value" option in custom fields (used both in fields of type \'custom\' or to provide default values) has had a small
			change in the way it is applied.',
			'
				<em>Note: If you do not use this option with your custom fields, you can simply ignore this notice.</em><br /><br />
				
				This option exists so you can execute PHP code to obtain the value of a single variable. In previous builds of ' . DP_NAME . ', the value
				was parsed in such a way that you could also perform function calls.<br /><br />
				
				For security reasons, this option is now exclusively used for variable replacement. Your code is placed directly into a double-quoted string and evaluated.
				You can no longer call functions, nor can you "jump out" of the string to concatenate the values of function calls.<br /><br />
				
				<hr />
				<a href="#" onclick="$(\'#upgrade_phpcode_changed_info1\').show(); $(this).hide(); return false;">What do I need to change?</a>
				<div style="display:none" id="upgrade_phpcode_changed_info1">
					For simple variable replacements such as "$myvar" or "$myarray[item]", nothing has changed. (Note that if you put quotes around array keys, you will need
					to surround the variable with curly braces: {$myarray[\'item\']} (PHP will get "confused" otherwise and give an error).<br /><br />
					
					If you were building up a value by concatenating strings, you just have to imagine that your code is now in a double string. This code:
					<pre style="background:#fff;margin:2px 0 2px 10px;padding:4px;border:1px solid #000;">$myvar . " is part of " . $myvar2</pre>
					Becomes this:
					<pre style="background:#fff;margin:2px 0 2px 10px;padding:4px;border:1px solid #000;">$myvar is part of $myvar2</pre>
				</div>
				
				' .	(!defined('MANAGED') ? '
							<hr />
							<a href="#" onclick="$(\'#upgrade_phpcode_changed_info2\').show(); $(this).hide(); return false;">I need to be able to call functions, how do I do it?</a>
								<div style="display:none" id="upgrade_phpcode_changed_info2">
								If you need to call functions you should use the other option, "Parsed PHP Code". Using this option is just as easy. For example, before you might have
								had this code:
									<pre style="background:#fff;margin:2px 0 2px 10px;padding:4px;border:1px solid #000;">"Time created: " . date(\'F j, Y, g:i a\', time())</pre>
								Now, you should use "Parsed PHP Code" with this code:
									<pre style="background:#fff;margin:2px 0 2px 10px;padding:4px;border:1px solid #000;">$str = "Time created: " . date(\'F j, Y, g:i a\', time());</pre>
								</div>
					' : '')
				. '
			'
		);
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
$upgrade = new upgrade_3020001();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));