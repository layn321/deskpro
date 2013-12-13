<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_plugins.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - import settings
// +-------------------------------------------------------------+

require_once(INC . 'classes/class_XMLDecode.php');

function import_plugins() {

	global $db;

	/************
	* Get Old (for install)
	************/

	$plugins = $db->query_return_array_id("SELECT * FROM plugins", '', 'intname');

	$db->query("DELETE FROM plugins");

	/************
	* GET XML
	************/

	$xml = getXML('install/data/plugins.xml');

	if ($xml['plugin']['intname']) {
		$xml = array('plugin' => array($xml['plugin']));
	}

	/************
	* Create Entries
	************/

	foreach ($xml['plugin'] AS $key => $plugin) {

		$db->query("
			INSERT INTO plugins SET
				name = '" . $db->escape($plugin['name']['value']) . "',
				intname = '" . $db->escape($plugin['intname']) . "',
				installed = " . intval($plugins[$plugin['intname']]['installed']) . ",
				version = '" . $db->escape($plugin['version']['value']) . "',
				admin_url = '" . $db->escape($plugin['admin_url']['value']) . "',
				info_url = '" . $db->escape($plugin['info_url']['value']) . "',
				plugin_dir = '" . $db->escape($plugin['plugin_dir']['value']) . "'
		");
	}
}