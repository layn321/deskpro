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

class upgrade_3020101 extends upgrade_base_v3 {

	var $version = '3.2.1';

	var $version_number = 3020101;

	var $pages = array(
		array('Chat Changes', 'options.gif'),
	);

	/***************************************************
	* Chat changes
	***************************************************/

	function step1() {

		global $db;

		$this->start('Add typing status fields to chat table');
		$db->query("
			ALTER TABLE  `chat_chat` ADD  `tech_typing` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `has_started` ,
			ADD  `user_typing` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `tech_typing` ;
		");
		$this->yes();
		
		$this->start('Adding chat_attachment table');
		$db->query("
			CREATE TABLE `chat_attachment` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `blobid` int(10) unsigned NOT NULL,
			  `techid` int(10) unsigned NOT NULL,
			  `chatid` int(10) unsigned NOT NULL,
			  `filename` varchar(255) NOT NULL,
			  `filesize` varchar(255) NOT NULL,
			  `extension` varchar(50) NOT NULL,
			  `timestamp` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`)
			)
		");
		$this->yes();
		
		$this->start('Making sure chat tables are MyISAM');
		$db->query_silent('ALTER TABLE  `chat_chat` ENGINE = MYISAM');
		$db->query_silent('ALTER TABLE  `chat_message` ENGINE = MYISAM');
		$this->yes();

		$this->start('Adding new indexes on chat tables');
		$db->query('ALTER TABLE  `chat_chat` ADD FULLTEXT (`subject`)');
		$db->query('ALTER TABLE  `chat_message` ADD INDEX  `author` (  `authortype` ,  `authorid` )');
		$db->query('ALTER TABLE  `chat_message` ADD FULLTEXT (`message`)');
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
$upgrade = new upgrade_3020101();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));