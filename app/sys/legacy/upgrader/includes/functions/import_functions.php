<?php

require_once(INC . 'classes/class_XMLDecode.php');

/**
* Run queries from XML file. Quick way to import generic data
*
* @access	Public
*
* @return	 string
*/
function run_xml_queries($file) {

	global $db;

	$xml_parser = new class_XMLDecode();
	$xml = $xml_parser->parse_file($file);

	if (!is_array($xml['query'])) {
		return;
	}

	if (!$xml['query']['0']) {
		$xml['query'] = array($xml['query']);
	}

	$vars_find = array('###TIMENOW###');
	$vars_repl = array(TIMENOW);

	foreach ($xml['query'] AS $query) {

		$array = array();

		// loop on the fields
		foreach ($query AS $key => $value) {

			// don't get the table name
			if ($key != 'table') {
				$array[strtolower($key)] = str_replace($vars_find, $vars_repl, $value['value']);
			}
		}

		// run query
		$db->query("
			INSERT INTO " . strtolower($query['table']) . " SET " .
			array2sqlinsert($array) . "
		");

		// get any error
		if ($error = $db->geterrdesc()) {
			$errors[] = $error;
		}
	}

	// return errors
	return $errors;
}

?>