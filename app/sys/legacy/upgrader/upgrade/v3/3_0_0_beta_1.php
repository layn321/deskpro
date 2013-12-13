<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: 3_0_0_beta_1.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | -
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_2 extends upgrade_base_v3 {

	var $version = '3.0.0 Beta 1';

	var $version_number = 2;

	var $pages = array(
		array('Database Changes', 'options.gif')
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {

		global $db, $settings;

		$this->start('No Database Changes For This Upgrade');
		
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
$upgrade = new upgrade_2();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>