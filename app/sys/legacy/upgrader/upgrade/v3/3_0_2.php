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

class upgrade_9 extends upgrade_base_v3 {

	var $version = '3.0.2';

	var $version_number = 9;

	var $pages = array(
		array('Update Ticket Cats', 'options.gif'),
		array('Update Image', 'options.gif'),
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
	
	/***************************************************
	* DB changes
	***************************************************/

	function step2() {

		global $db, $settings;
		
		$this->start('Fixing Knowledgebase images');

		$images = array();
		
		// get orphaned images
		$db->query("SELECT * FROM images WHERE tempkey != '' AND content_id = 0 AND content_type = 'faq_article'");
		while ($result = $db->row_array()) {
			$images[] = $result['id'];
		}
		
		// get faq articles (question & answer)
		$db->query("
			SELECT * FROM faq_articles
		");

		$datas = array();
		
		while ($result = $db->row_array()) {
			
			$datas[] = array('id' => $result['id'], 'data' => $result['question']);
			$datas[] = array('id' => $result['id'], 'data' => $result['answer']);
			
		}
		
		// now find matches
		foreach ($images AS $imageid) {
			
			foreach ($datas AS $data) {
				
				if (in_string("getimage.php?id=$imageid\"", $data['data'])) {
					
					$db->query("
						UPDATE images SET
							content_id = $data[id],
							tempkey = ''
						WHERE id = $imageid
					");
					
					break;
					
				}	
			}
		}
		
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
$upgrade = new upgrade_9();

// do the header
$upgrade->header();

// run the next step
$upgrade->runStep($request->getNumber('step', 'request'));
