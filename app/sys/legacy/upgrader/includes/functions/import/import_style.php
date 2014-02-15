<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: import_style.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - import settings
// +-------------------------------------------------------------+

require_once(INC . 'classes/class_XMLDecode.php');

function import_style() {

	global $db;

	require_once(INC . 'functions/css_functions.php');

	$xml = getXML('install/data/style/default.xml');

	$style = $db->query_return("
		SELECT * FROM style WHERE ref = 'default'
	");

	if (is_array($style)) {
		$exists = true;
	}

	################ STYLESHEET ################

	$old_stylesheet = $db->query_return("
		SELECT *
		FROM template_stylesheets
		WHERE id = 1 OR ref = 'default'
	");

	$old_css = $old_stylesheet['stylesheet'];
	$old_css_rtl = $old_stylesheet['stylesheet'];

	$new_css = file_get_contents(ROOT . '/install/data/style/default.css');
	$new_css_rtl = file_get_contents(ROOT . '/install/data/style/rtl.css');


	$db->query("
		DELETE FROM template_stylesheets WHERE id = 1 OR ref = 'default'
	");

	$db->query("
		INSERT INTO template_stylesheets SET
			id = 1,
			name = '" . $db->escape($xml['template_stylesheets']['name']['value']) . "',
			editor = '" . $db->escape($xml['template_stylesheets']['editor']['value']) . "',
			ref = 'default',
			stylesheet = '" . $db->escape($new_css) . "',
			stylesheet_rtl = '" . $db->escape($new_css_rtl) . "'
	");


	/*************************
	* Add new CSS rules to all
	* other stylesheets
	*************************/

	// If there is no oldcss, then this is
	// a clean install
	if ($old_css) {

		$add_css = css_get_new_rules($old_css, $new_css);
		$add_css_rtl = css_get_new_rules($old_css_rtl, $new_css_rtl);

		if ($add_css OR $add_css_rtl) {

			$all_other_stylesheets = (array)$db->query_return_array("
				SELECT id, stylesheet, stylesheet_rtl
				FROM template_stylesheets
				WHERE id != 1 AND ref != 'default'
			");

			foreach ($all_other_stylesheets as $other_stylesheet) {

				$sql_update = array();

				if ($add_css) {
					$sql_update['stylesheet'] = $other_stylesheet['stylesheet'] . "\n\n\n\n/* NEW RULES - Automatically added */\n\n" . $add_css;
				}

				if ($add_css_rtl) {
					$sql_update['stylesheet_rtl'] = $other_stylesheet['stylesheet_rtl'] . "\n\n\n\n/* NEW RULES - Automatically added */\n\n" . $add_css_rtl;
				}

				// Update the CSS
				$db->query("
					UPDATE template_stylesheets
					SET " . array2sqlinsert($sql_update) . "
					WHERE id = $other_stylesheet[id]
				");
			}

			// Now update each styles
			$all_other_styleids = (array)$db->query_return_array_id("SELECT id FROM style WHERE id != 1 AND ref != 'default'", 'id', '');

			foreach ($all_other_styleids as $other_styleid) {
				$db->query("
					UPDATE style SET
						css = '" . $db->escape(css_merge_elements_with_css($other_styleid)) . "'
					WHERE id = $other_styleid
				");
			}
		}
	}


	################ TEMPLATE SET ################

	$db->query("
		DELETE FROM template_set WHERE id = 1 OR ref = 'default'
	");

	$db->query("
		INSERT INTO template_set SET
			id = 1,
			name = '" . $db->escape($xml['template_set']['name']['value']) . "',
			parent = 0,
			ref = 'default'
	");

	################ STYLE ################

	$db->query("
		DELETE FROM style WHERE id = 1 OR ref = 'default'
	");

	// insert everything except the css
	$db->query("
		INSERT INTO style SET
			id = " . intval($xml['style']['id']['value']) . ",
			name = '" . $db->escape($xml['style']['name']['value']) . "',
			images = '" . $db->escape($xml['style']['images']['value']) . "',
			templateset = " . intval($xml['style']['templateset']['value']) . ",
			header = '" . $db->escape($style['header']) . "',
			header_include = '" . $db->escape($style['header_include']) . "',
			footer = '" . $db->escape($style['footer']) . "',
			header_unparsed = '" . $db->escape($style['header_unparsed']) . "',
			header_include_unparsed = '" . $db->escape($style['header_include_unparsed']) . "',
			footer_unparsed = '" . $db->escape($style['footer_unparsed']) . "',
			extracss = '" . $db->escape($style['extracss']) . "',
			extracss_unparsed = '" . $db->escape($style['extracss_unparsed']) . "',
			cssstyle = " . intval($xml['style']['cssstyle']['value']) . ",
			active = " . intval(iff($exists, $style['active'], $xml['style']['active']['value'])) . ",
			is_default = " . intval(iff($exists, $style['is_default'], $xml['style']['is_default']['value'])) . ",
			elements = '" . $db->escape($style['elements']) . "',
			ref = '" . $db->escape($xml['style']['ref']['value']) . "'
	");

	// update all styles that have been affected by the change in the CSS
	css_style_editors_update(1);

	return;

}