<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_tech_help.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - Export help contents to xml file
// +-------------------------------------------------------------+

require_once(INC . 'classes/class_XMLDecode.php');

function import_tech_help() {

	global $db;
	
	/******************
	* Delete all current content
	******************/		
	
	$db->emptytable('deskpro_help_tech_articles');
	$db->emptytable('deskpro_help_tech_cats');

	/******************
	* Get the help XML file
	******************/	

	$xml = getXML('install/data/tech_help.xml');

	/******************
	* Loop through XML file
	******************/	
	
	foreach ($xml['category'] AS $category) {
		
		/******************
		* Add Category
		******************/	
	    
	    $category['title']['value'] = str_replace('{$DP_NAME}', DP_NAME, $category['title']['value']);
		
		$db->query("
			INSERT INTO deskpro_help_tech_cats SET
				intname = '" . $db->escape($category['intname']['value']) . "',
				displayorder = " . intval($category['displayorder']['value']) . ",
				title = '" . $db->escape($category['title']['value']) . "'
		");	
		
		/******************
		* Add Articles for this Category
		******************/			
		
		if (is_array($category['article'])) {
			
			if (!$category['article']['0']) {
				$category['article'] = array($category['article']);
			}

			foreach ($category['article'] AS $article) {
			    
			    $article['title']['value'] = str_replace('{$DP_NAME}', DP_NAME, $article['title']['value']);
			    $article['content']['value'] = str_replace('{$DP_NAME}', DP_NAME, $article['content']['value']);
				
				$db->query("
					INSERT INTO deskpro_help_tech_articles SET
						intname = '" . $db->escape($article['intname']['value']) . "',
						title = '" . $db->escape($article['title']['value']) . "',
						category = '" . $db->escape($category['intname']['value']) . "',
						content = '" . $db->escape($article['content']['value']) . "'
				");	
			}	
		}
	}
}

?>