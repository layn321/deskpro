<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_cron.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - import settings
// +-------------------------------------------------------------+

require_once(INC . 'classes/class_XMLDecode.php');

function import_cron() {

	global $db;
	
	/************
	* Delete old
	************/	

	$db->query("
		SELECT * FROM cron_options WHERE custom = 0
	");
	
	while ($result = $db->row_array()) {
		$crons[$result['scriptname']] = $result;
	}
	
	/************
	* Create Entries
	************/	
	
	$xml = getXML('install/data/cron.xml');
	
	/************
	* Update Entries
	************/		

	foreach ($xml['cron'] AS $key => $cron) {
		
		$fields[] = $cron['scriptname']['value'];
		
		// updating
		if ($crons[$cron['scriptname']['value']]) {
			
			$db->query("
				UPDATE cron_options SET
					title = '" . $db->escape($cron['title']['value']) . "',
					description = '" . $db->escape($cron['description']['value']) . "',
					frequency = " . intval($cron['frequency']['value']) . "
				WHERE scriptname = '" . $db->escape($cron['scriptname']['value']) . "'
			");

		} else {
			
			$db->query("
				INSERT INTO cron_options SET
					title = '" . $db->escape($cron['title']['value']) . "',
					description = '" . $db->escape($cron['description']['value']) . "',
					frequency = " . intval($cron['frequency']['value']) . ",
					scriptname = '" . $db->escape($cron['scriptname']['value']) . "'
			");	
			
	
		}
	}
	
	/************
	* Delete Old Entries
	************/		
	
	$db->query("SELECT * FROM cron_options WHERE custom = 0 AND scriptname NOT IN " . array2sql($fields));
	while ($result = $db->row_array()) {
		$delete[] = $result['id'];
	}
	
	$db->query("DELETE FROM cron_options WHERE custom = 0 AND scriptname NOT IN " . array2sql($fields));
	$db->query("DELETE FROM cron_log WHERE id IN " . array2sql($delete));
	
}