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

class upgrade_8 extends upgrade_base_v3 {

	var $version = '3.0.1';

	var $version_number = 8;

	var $pages = array(
		array('Update Settings', 'options.gif'),
		array('Resetting Indexes - 1', 'options.gif'),
		array('Resetting Indexes - 2', 'options.gif'),
	);

	/***************************************************
	* DB changes
	***************************************************/

	function step1() {

		global $db, $settings;

		$this->start('Updating admin session timeout');
		if ($settings['admin_session_length'] == '36000') {
			legacy_update_setting('admin_session_length', '3600');
		}
		$this->yes();

		$this->start('Updating tech session timeout');
		if ($settings['tech_session_length'] == '36000') {
			legacy_update_setting('tech_session_length', '3600');
		}
		$this->yes();

		$this->start('Updating tech session timeout');
		if ($settings['user_session_length'] == '36000') {
			legacy_update_setting('user_session_length', '3600');
		}
		$this->yes();

	}

	/***************************************************
	* Resetting indexs on manual pages
	***************************************************/

	function step2() {

		global $db, $settings;

		$this->start('Clearing indexes on `manual_pages`');
		clear_table_indexes('manual_pages');
		$this->yes();

	}

	/***************************************************
	* Resetting indexs on manual pages
	***************************************************/

	function step3() {

		global $db, $settings;

		$add_indexes = array(
			array('manualid', 'INDEX', array('manualid')),
			array('title', 'FULLTEXT', array('title')),
			array('content', 'FULLTEXT', array('content')),
			array('allcontent', 'FULLTEXT', array('title', 'content'))
		);

		$this->start('Adding indexes to `manual_pages`');
		add_table_indexes('manual_pages', $add_indexes);
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
$upgrade = new upgrade_8();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>