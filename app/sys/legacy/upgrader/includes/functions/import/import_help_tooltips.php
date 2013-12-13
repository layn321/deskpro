<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_help_tooltips.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - export tool tip help to xml file
// +-------------------------------------------------------------+

require_once(INC . 'functions/export_functions.php');
require_once(INC . 'functions/import_functions.php');
require_once(INC . 'classes/class_XMLDecode.php');

function import_help_tooltips() {

	global $db;
	
	/*********************
	* Delete all the data
	**********************/
	
	$db->query("TRUNCATE TABLE deskpro_help_tooltip");	
	
	/*********************
	* Import from file
	**********************/		
	
	$xml = getXML('install/data/help_tooltips.xml');
	
	foreach ($xml['section'] AS $section) {
		
		unset($tips);
		
		if ($section['tips']['name']) {
		    
		    $section['tips']['value'] = str_replace('{$DP_NAME}', DP_NAME, $section['tips']['value']);

			$tips = array($section['tips']['name'] => $section['tips']['value']);

		} else if (is_array($section['tips'])) {
			
			$tips = array();
			foreach ($section['tips'] AS $var) {
			    $var['value'] = str_replace('{$DP_NAME}', DP_NAME, $var['value']);
				$tips[$var['name']] = $var['value'];
			}
		
		}
		
		$section['mainhelp']['value'] = str_replace('{$DP_NAME}', DP_NAME, $section['mainhelp']['value']);
		$section['maintitle']['value'] = str_replace('{$DP_NAME}', DP_NAME, $section['maintitle']['value']);
		
		$db->query("
			INSERT INTO deskpro_help_tooltip SET
				section = '" . $db->escape($section['name']) . "',
				mainhelp = '" . $db->escape($section['mainhelp']['value']) . "',
				maintitle = '" . $db->escape($section['maintitle']['value']) . "',
				tips = '" . $db->escape(serialize($tips)) . "'
		");
	}
}

?>