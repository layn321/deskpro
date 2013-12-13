<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: 3_0_0_beta_2.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | -
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3 extends upgrade_base_v3 {

	var $version = '3.0.0 Beta 2';

	var $version_number = 3;

	var $pages = array(
		array('Database Changes', 'options.gif')
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db, $settings;

		$this->start('Creating Plugins Table');
	
		$db->query("
			CREATE TABLE `plugins` (
			`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`intname` VARCHAR( 250 ) NOT NULL ,
			`name` VARCHAR( 250 ) NOT NULL ,
			`installed` INT( 1 ) NOT NULL
			)
		");
	
		$this->yes();
	
		$this->start('Creating Manual Plugin Tables');
	
		$db->query("
			CREATE TABLE `manual_comments` (
				`id` int(10) NOT NULL auto_increment,
				`userid` int(10) NOT NULL default '0',
				`email` varchar(250)  NOT NULL default '',
				`pageid` int(10) NOT NULL default '0',
				`timestamp_created` int(10) NOT NULL default '0',
				`timestamp_validated` int(10) NOT NULL default '0',
				`is_validated` int(1) NOT NULL default '0',
				`comments` mediumtext  NOT NULL,
				PRIMARY KEY  (`id`)
			)
		");
	
		$db->query("
			CREATE TABLE `manual_manuals` (
				`id` int(10) NOT NULL auto_increment,
				`name` varchar(250)  NOT NULL default '',
				`description` mediumtext  NOT NULL,
				`published` int(1) NOT NULL default '0',
				`displayorder` int(10) NOT NULL default '0',
				PRIMARY KEY  (`id`)
			)
		");
	
		$db->query(query_engine_replace("
			CREATE TABLE `manual_pages` (
				`id` int(10) NOT NULL auto_increment,
				`parent` int(10) NOT NULL default '0',
				`displayorder` int(10) NOT NULL default '0',
				`title` varchar(250)  NOT NULL,
				`content` mediumtext  NOT NULL,
				`revisionid` int(10) NOT NULL default '0',
				`timestamp_revision` int(10) NOT NULL default '0',
				`timestamp_creation` int(10) NOT NULL default '0',
				`manualid` int(1) NOT NULL default '0',
				`allow_comments` tinyint(1) NOT NULL default '1',
				`published` tinyint(1) NOT NULL default '1',
				`old_parent` int(10) unsigned NOT NULL,
				PRIMARY KEY  (`id`),
				KEY `manualid` (`manualid`),
				FULLTEXT KEY `title` (`title`),
				FULLTEXT KEY `content` (`content`)
			) ENGINE=MyISAM
		"));
	
		$db->query("
			CREATE TABLE `manual_revisions` (
				`id` int(10) NOT NULL auto_increment,
				`pageid` int(10) NOT NULL default '0',
				`revisionid` int(10) NOT NULL default '0',
				`content` mediumtext  NOT NULL,
				`timestamp` int(10) NOT NULL default '0',
				`techid` int(10) NOT NULL default '0',
				PRIMARY KEY  (`id`)
			)
		");
	
		$db->query("
			CREATE TABLE `manual_searchlog` (
				`id` int(10) NOT NULL auto_increment,
				`searchwords` varchar(250)  NOT NULL default '',
				`matches` int(10) NOT NULL default '0',
				`timestamp` int(10) NOT NULL default '0',
			PRIMARY KEY  (`id`)
			)
		");
	
		$this->yes();
	
		$this->start('Inserting new permissions');
	
		$db->query("
			ALTER TABLE `tech`
			ADD `p_man_create` TINYINT( 1 ) NOT NULL DEFAULT '0',
			ADD `p_man_edit` TINYINT( 1 ) NOT NULL DEFAULT '0',
			ADD `p_man_del` TINYINT( 1 ) NOT NULL DEFAULT '0',
			ADD `p_manpage_create` TINYINT( 1 ) NOT NULL DEFAULT '0',
			ADD `p_manpage_edit` TINYINT( 1 ) NOT NULL DEFAULT '0',
			ADD `p_manpage_del` TINYINT( 1 ) NOT NULL DEFAULT '0',
			ADD `p_mancomment_manage` TINYINT( 1 ) NOT NULL DEFAULT '0'
		");
	
		$db->query("
			UPDATE tech SET
				p_man_create = 1,
				p_man_edit = 1,
				p_man_del = 1,
				p_manpage_create = 1,
				p_manpage_edit = 1,
				p_manpage_del = 1,
				p_mancomment_manage = 1
		");
	
		$this->yes();
	
		$this->start('Adding Language Flag Column');
	
		$db->query("ALTER TABLE `languages` ADD `flag` VARCHAR( 50 ) NOT NULL");
	
		$this->yes();
	
		$this->start('Dropping Unused Template Column');
	
		$db->query("ALTER TABLE `template` DROP `version_upgrade` ");
	
		$this->yes();
	
		$this->start('Add Index To Knowledgebase Rating Table');
	
		$db->query("ALTER TABLE `faq_rating` ADD INDEX ( `faqid` ) ");
	
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
$upgrade = new upgrade_3();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>