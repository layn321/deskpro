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

class upgrade_7 extends upgrade_base_v3 {

	var $version = '3.0.0';

	var $version_number = 7;

	var $pages = array(
		array('Remove Old Languages', 'options.gif'),
		array('Reset Indexes - Part 1', 'options.gif'),
		array('Reset Indexes - Part 2', 'options.gif'),
		array('Reset Indexes - Part 3', 'options.gif'),
		array('Reset Indexes - Part 4', 'options.gif'),
		array('Reset Indexes - Part 5', 'options.gif'),
		array('Reset Indexes - Part 6', 'options.gif'),
		array('Database Changes', 'options.gif')
	);

	/***************************************************
	* DB changes
	***************************************************/

	function step1() {

		global $db, $settings;

		require_once(INC . 'classes/class_Cache.php');
		$cache2 = new Cache();
	
		require_once(INC . 'functions/import/import_language.php');
	
		$this->start('Removing old languages');
	
		$langs = $db->query_return_array("SELECT id FROM languages WHERE deskproid != '' AND id != 1");
		if (is_array($langs)) {
			foreach ($langs AS $lang) {
				delete_language($lang['id'], true);
			}
		}
	
		$this->yes();

	}

	/***************************************************
	* Reset indexes on table `ticket`
	***************************************************/

	function step2() {

		global $db, $settings;

		$this->start('Clearing indexes on `ticket`');
		clear_table_indexes('ticket');
		$this->yes();

	}

	/***************************************************
	* Reset indexes on table `ticket`
	***************************************************/	

	function step3() {
		
		global $db, $settings;

		$add_indexes = array(
			array('ref', 'INDEX', array('ref')),
			array('userid', 'INDEX', array('userid')),
			array('category', 'INDEX', array('category')),
			array('is_locked', 'INDEX', array('is_locked')),
			array('tech', 'INDEX', array('tech')),
			array('status', 'INDEX', array('status','category')),
			array('timestamp_opened', 'INDEX', array('timestamp_opened')),
			array('timestamp_closed', 'INDEX', array('timestamp_closed')),
			array('priority', 'INDEX', array('priority')),
			array('subject', 'FULLTEXT', array('subject')),
		);

		$this->start('Adding indexes to `ticket`');
		add_table_indexes('ticket', $add_indexes);
		$this->yes();
	}

	/***************************************************
	* Reset indexes on table `ticket_message`
	***************************************************/

	function step4() {
		
		global $db, $settings;

		$this->start('Clearing indexes on `ticket_message`');
		clear_table_indexes('ticket_message');
		$this->yes();
		
	}

	/***************************************************
	* Reset indexes on table `ticket_message`
	***************************************************/

	function step5() {
		
		global $db, $settings;

		$add_indexes = array(
			array('techid', 'INDEX', array('techid')),
			array('ticketid', 'INDEX', array('ticketid`, `timestamp')),
			array('timestamp', 'INDEX', array('timestamp')),
			array('sourceid', 'INDEX', array('sourceid')),
			array('userid', 'INDEX', array('userid')),
			array('message', 'FULLTEXT', array('message')),
		);
	
		$this->start('Adding indexes to `ticket_message`');
		add_table_indexes('ticket_message', $add_indexes);
		$this->yes();
		
	}

	/***************************************************
	* Reset indexes on table `user`
	***************************************************/
	
	function step6() {
		
		global $db, $settings;	

		$this->start('Clearing indexes on `user`');
		clear_table_indexes('user');
		$this->yes();
		
	}

	/***************************************************
	* Reset indexes on table `user`
	***************************************************/
	
	function step7() {
		
		global $db, $settings;	
	
		$add_indexes = array(
			array('username', 'UNIQUE', array('username')),
			array('email', 'UNIQUE', array('email')),
			array('awaiting_register_validate_tech', 'INDEX', array('awaiting_register_validate_tech')),
			array('name', 'INDEX', array('name')),
		);
	
		$this->start('Adding indexes to `user`');
		add_table_indexes('user', $add_indexes);
		$this->yes();

	}

	function step8() {
		
		global $db, $settings;	

		$this->start('Some cleaning of ticket log');
		$db->query("DELETE FROM ticket_log WHERE actionlog = 'email_tech' AND extra = ''");
		$this->yes();
	
		$this->start('Add tech_creator field to `ticket`');
		$db->query("ALTER TABLE `ticket` ADD `tech_creator` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `tech`");
		$this->yes();
	
		$this->start('Add language version to phrases');
		$db->query("ALTER TABLE `template_words` ADD `version` INT( 10 ) NOT NULL ;");
		$this->yes();
		
	}
}

// check we are in correct location
install_check();

// display header
$header->build();

// create the installer
$upgrade = new upgrade_7();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));

?>