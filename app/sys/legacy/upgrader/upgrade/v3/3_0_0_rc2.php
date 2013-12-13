<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: index.php 3839 2007-05-15 14:39:13Z chris $
// +-------------------------------------------------------------+
// | File Details:
// | -
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_6 extends upgrade_base_v3 {

	var $version = '3.0.0 RC 2';

	var $version_number = 6;

	var $pages = array(
		array('Database Changes', 'options.gif'),
		array('Database Changes', 'options.gif'),
		array('Database Changes', 'options.gif')
	);

	/***************************************************
	* DB changes
	***************************************************/

	function step1() {

		global $db, $settings;

		$this->start('Adding additional fields to user table');
		$db->query("
			ALTER TABLE `user` 
			ADD `authid` INT UNSIGNED NOT NULL AFTER `id`, 
			ADD `last_autosession` VARCHAR( 32 ) NOT NULL AFTER `awaiting_register_validate_user`
		");
		$this->yes();

	}

	/***************************************************
	* DB changes
	***************************************************/

	function step2() {

		global $db, $settings;

		$this->start('Adding new indexes to tables');
		$db->query("ALTER TABLE `manual_pages` ADD FULLTEXT `allcontent` (`title`,`content`)");
		$this->yes();
	
		$this->start('Modifying error log tables');
		$db->query("ALTER TABLE `error_log` CHANGE `summary` `summary` TEXT NOT NULL");
		$db->query("ALTER TABLE `error_log` ADD `backtrace` TEXT NOT NULL AFTER `details`");
		$db->query("ALTER TABLE `error_log` ADD `gateway_error` INT UNSIGNED NOT NULL");
		$db->query("ALTER TABLE `gateway_error` ADD `error_log` INT UNSIGNED NOT NULL");
		$this->yes();

	}

	/***************************************************
	* DB changes
	***************************************************/

	function step3() {

		global $db, $settings;

		$this->start('Adding new indexes to tables');
		$db->query("ALTER TABLE `gateway_source` ADD INDEX ( `timestamp` ) ");
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
$upgrade = new upgrade_6();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

s
?>