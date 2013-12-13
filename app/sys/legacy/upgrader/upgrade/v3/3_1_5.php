<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id$
// +-------------------------------------------------------------+
// | File Details:
// | - Upgrade to 3.1.5
// +-------------------------------------------------------------+

/*************************************
* UPGRADE CLASS
*************************************/

class upgrade_3010501 extends upgrade_base_v3 {

	var $version = '3.1.5';

	var $version_number = 3010501;

	var $pages = array(
		array('None', 'options.gif'),
	);

	/***************************************************
	* Database changes
	***************************************************/

	function step1() {
		$this->start('No database changes are required for this upgrade');
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
$upgrade = new upgrade_3010501();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));