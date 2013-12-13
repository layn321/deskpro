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

class upgrade_10 extends upgrade_base_v3 {

	var $version = '3.0.3';

	var $version_number = 10;

	var $pages = array(
		array('Update Ticket Cats', 'options.gif')
	);

	/***************************************************
	* DB changes
	***************************************************/

	function step1() {

		global $db, $settings;
		
		$this->start('Fixing tickets that belong to non-existant categories');
		
		$catids = $db->query_return_array_id("
			SELECT id FROM ticket_cat
		", 'id');
	
		$catids = array2sql($catids);
	
		$db->query("
			UPDATE ticket
			SET category = 0
			WHERE category NOT IN $catids
		");
		
		$this->yes();

		$this->start('Fix max length of tech forum message');

		$db->query("ALTER TABLE `tech_forum_message` CHANGE `message` `message` MEDIUMTEXT NOT NULL");

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
$upgrade = new upgrade_10();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));