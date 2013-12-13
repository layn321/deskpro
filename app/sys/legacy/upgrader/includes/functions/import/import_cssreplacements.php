<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_cssreplacements.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - import css replacements from a XML file
// +-------------------------------------------------------------+

require_once(INC . 'classes/class_XMLDecode.php');

function import_cssreplacements($styleid, $filepath) {

	global $db;

	require_once(INC . 'classes/class_XMLDecode.php');
	$xml = new class_XMLDecode();
	$data = $xml->parse_xml(file_get_contents($filepath));
	unset($xml);

	/******************************
	* Editor values
	******************************/
	// New editor
	$editor = array();

	if (is_array($data['groups']['group'])) {
		foreach ($data['groups']['group'] as $group) {

			// Build array
			$array = array(
				'name' => $group['name']['value'],
				'description' => $group['description']['value'],
				'type' => $group['type'],
				'value' => $group['val'],
				'fields' => array()
			);

			if ($group['fields']['selector']) {
				$group['fields']['selector'] = array($group['fields']['selector']);
			}

			foreach ($group['fields'] as $field) {
				$array['fields'][] = array(
					'selector' => $field['selector'],
					'element' => $field['element']
				);
			}

			$editor[] = $array;
		}
	}

	$db->query("
		UPDATE template_stylesheets
			SET editor = '" . $db->escape(serialize($editor)) . "'
		WHERE id = " . intval($id) . "
	");


	/******************************
	* Style values
	******************************/
	// New elements
	$elements = array();

	if (is_array($data['stylecss']['element'])) {
		foreach ($data['stylecss']['element'] as $elm) {
			$elements[] = array(
				'selector' => $elm['selector'],
				'element' => $elm['element'],
				'value' => $elm['val']
			);
		}
	}

	$db->query("
		UPDATE style SET
			elements = '" . $db->escape(serialize($elements)) . "'
		WHERE id = $styleid
	");

	$db->query("
		UPDATE style SET
			css = '" . $db->escape(css_merge_elements_with_css($styleid)) . "'
		WHERE id = $styleid
	");
}

?>