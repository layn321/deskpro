<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_glossary.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - import settings
// +-------------------------------------------------------------+

require_once(INC . 'classes/class_XMLDecode.php');

function import_glossary() {

	global $db;

	$xml = getXML('install/data/tech_glossary.xml');	
	
	/************
	* Delete old
	************/	

	$db->query("
		DELETE FROM deskpro_help_glossary
	");
	
	/************
	* Insert entries
	************/	

	foreach ($xml['entry'] AS $entry) {
	    
	    $entry['content']['value'] = str_replace('{$DP_NAME}', DP_NAME, $entry['content']['value']);

		$db->query("
			INSERT INTO deskpro_help_glossary SET
				word = '" . $db->escape($entry['word']['value']) . "',
				content = '" . $db->escape($entry['content']['value']) . "'
		");
	}
}