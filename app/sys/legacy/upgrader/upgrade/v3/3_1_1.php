<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id$
// +-------------------------------------------------------------+
// | File Details:
// | - Upgrade to 3.1.0 gold
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3010101 extends upgrade_base_v3 {

	var $version = '3.1.1';

	var $version_number = 3010101;

	var $pages = array(
		array('Main Database Changes', 'options.gif'),
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db;

		$this->start('Altering size of password field');
		$db->query("ALTER TABLE  `user_deskpro` CHANGE  `password`  `password` VARCHAR( 255 )  NOT NULL");
		$this->yes();

		$this->start('Adding time fields to manuals table');
		$db->query("
			ALTER TABLE  `manual_manuals` ADD  `time_gen_single` INT UNSIGNED NOT NULL ,
			ADD  `time_gen_print` INT UNSIGNED NOT NULL ,
			ADD  `time_gen_zip` INT UNSIGNED NOT NULL
		");
		$this->yes();

		$this->start('Adding permission for tech Javascript use');
		$db->query("ALTER TABLE  `tech` ADD  `p_use_js` INT( 1 ) NOT NULL DEFAULT  '0'");
		$this->yes();

		$this->start('Adding email lookup table for PIPE mechanism');
		$db->query("
			CREATE TABLE `pipe_email_lookup` (
			  `id` int(10) unsigned NOT NULL auto_increment,
			  `email` varchar(255) NOT NULL,
			  `timestamp` int(10) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `email` (`email`)
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
$upgrade = new upgrade_3010101();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));