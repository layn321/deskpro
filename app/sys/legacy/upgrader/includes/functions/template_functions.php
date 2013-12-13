<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
* template functions.
*
* @package DeskPRO
*/

/**
* Prepare a template for evaluation.
*
* @access public
*
* @param	string	Variable to fill; if "echo", the
* template is evaluated here and echoed instead
* @param	string	Name of template to prepare
* @param	string	 [Pass by reference] Variable to load subject into
* @param	string	[Optional] Ticket data, for use in the ticket subject
*
* @return	string	Eval()-read code, or none if "echo" specified for varname.
*/
function eval_template_email_user($varname, $templatename, &$subject, $ticket = array()) {

	global $settings, $user, $session, $user_details, $cache2;

	if (defined('USERZONE')) {
		if ($session['language']) {
			$language = $session['language'];
		} else {
			$language = $cache2->getDefaultLanguageID();
		}

	} else {
		if ($user_details['language']) {
			$language = $user['language'];
		} else {
			$language = $cache2->getDefaultLanguageID();
		}
	}

	if (!$language) {
		$language = $cache2->getDefaultLanguageID();
	}

	$template = get_template_email_user($templatename);

	if (!$template['body']) {
		return NULL;
	}

	// parses the footer, note we can only put $ticket / $settings in here
	if ($settings['email_footer_user']) {
		$footer = get_template_email_user('footer');
		global $email_footer_user;
		eval('$email_footer_user = ' . $footer['body'] . ';');
	}

	$subject = $template['subject'];

	if ($subject) {
		eval('$subject = ' . $subject . ';');
	}

	return '$' . $varname . ' = ' . $template['body'] . ';';

}

/**
* Retrieve the user email template
*
* @access	public
*
* @param	string	Name of template to retrieve
* @param	int	language id
*
* @return	string	array	An associative array, containing:
* 'body' => the template body
* 'subject' => the e-mail's subject
*/
function get_template_email_user($template_name) {

	global $cache;

	// can we get from cache
	if (isset($cache['templates_user_email'][$template_name])) {

		$template = $cache['templates_user_email'][$template_name]['body'];
		$subject =  $cache['templates_user_email'][$template_name]['subject'];

	} else {

		if (defined('DESKPRO_DEBUG_TEMPLATEFILES')) {

			require_once(INC . 'classes/class_XMLDecode.php');
			require_once(INC . 'functions/conditional_functions.php');

			$xml = getXML('install/data/useremails/' . $template_name . '.xml');

			$description = $xml['description']['value'];
			$template = $xml['email']['value'];
			$subject = $xml['subject']['value'];

			$template = parse_conditionals($template);
			$subject = parse_conditionals($subject);

		} else {

			// need a new sql class$
			$db3 =& database_object_factory();

			$result = $db3->query_return("
				SELECT template, subject
				FROM template_user_email
				WHERE name = '" . $db3->escape($template_name) . "'
			");

			if (!is_array($result)) {
				trigger_error("Template $template_name not found", E_USER_ERROR);
			}

			$template = $result['template'];
			$subject = $result['subject'];

			$cache['templates_user_email'][$template_name]['body'] = $template;
			$cache['templates_user_email'][$template_name]['subject'] = $subject;

		}
	}

	$template = preg_replace("/(\r\n|\n|\r)/", "", $template);
	$subject = preg_replace("/(\r\n|\n|\r)/", "", $subject);

	return array(
		'body' => str_replace("\'", '\'', $template),
		'subject' => str_replace("\'", '\'', $subject),
	);
}

/**
* Retrieve the tech email template
*
* @access public
*
* @param	string	Name of template to retrieve
*
* @return	string	array	An associative array, containing:
* 'body' => the template body
*  'subject' => the e-mail's subject
*/
function get_template_email_tech($template_name) {

	global $cache;

	// can we get from cache
	if (isset($cache['templates_tech_email'][$template_name])) {

		$template = $cache['templates_tech_email'][$template_name]['body'];
		$subject = $cache['templates_tech_email'][$template_name]['subject'];

	} else {

		if (defined('DESKPRO_DEBUG_TEMPLATEFILES')) {

			require_once(INC . 'classes/class_XMLDecode.php');
			require_once(INC . 'functions/conditional_functions.php');

			$xml = getXML('install/data/techemails/' . $template_name . '.xml');

			$description = $xml['description']['value'];
			$template = $xml['email']['value'];
			$subject = $xml['subject']['value'];

			$template = parse_conditionals($template);
			$subject = parse_conditionals($subject);

		} else {

			// need a new sql class
			$db3 =& database_object_factory();

			$result = $db3->query_return("
				SELECT template, subject
				FROM template_tech_email
				WHERE name = '" . $db3->escape($template_name) . "'
			");

			if (!is_array($result)) {
				trigger_error("Template $template_name not found", E_USER_ERROR);
			}

			$template = $result['template'];
			$subject = $result['subject'];

		}

		// set the cache
		if ($template AND $subject) {
			$cache['templates_tech_email'][$template_name]['body'] = $template;
			$cache['templates_tech_email'][$template_name]['subject'] = $subject;
		}
	}

	$template = preg_replace("/(\r\n|\n|\r)/", "", $template);
	$subject = preg_replace("/(\r\n|\n|\r)/", "", $subject);

	return array(
		'body' => str_replace("\'", '\'', $template),
		'subject' => str_replace("\'", '\'', $subject),
	);

}

/**
* Retrieve a HTML template.
*
* @access	public
*
* @param	string	Name of template to retrieve
* @param	int	[Optional] Add a comment to the template
*
* @return	string	The template, ready for eval().
*/
function get_template_web($template_name, $htmlcomment=1) {

	global $request, $cache, $settings, $style, $cache2;

	if (isset($cache['templates_web'][$template_name])) {

		// which version to get
		if (isset($cache['templates_web'][$template_name][$style['templateset']])) {

			$template = $cache['templates_web'][$template_name][$style['templateset']];

		} else if (isset($cache['templates_web'][$template_name][$style['template_set_parent']])) {

			$template = $cache['templates_web'][$template_name][$style['template_set_parent']];

		} else if (isset($cache['templates_web'][$template_name][$cache2->getDefaultTemplateSet()])) {

			$template = $cache['templates_web'][$template_name][$cache2->getDefaultTemplateSet()];

		} else {
			trigger_error("Template $template_name not found", E_USER_ERROR);
		}

		return str_replace("\'", '\'', $template);

	} else {

		if (defined('DESKPRO_DEBUG_TEMPLATEFILES')) {

			require_once(INC . 'functions/conditional_functions.php');

			if (defined('DESKPRO_DEBUG_TEMPLATESET')) {
				$set = DESKPRO_DEBUG_TEMPLATESET;
			}

			if ($request->getString('templateset', 'request')) {
				$set = $request->getNumber('templateset', 'request');
			}

			require_once(INC . 'classes/class_XMLDecode.php');
			require_once(INC . 'functions/conditional_functions.php');

			$xml = @getXML('install/data/style/default' . $set . '/' . $template_name . '.xml');

			if (!$xml) {
				trigger_error("Template $template_name not found", E_USER_ERROR);
			}

			$template = $xml['content']['value'];

			$template = parse_conditionals($template);

			$cache['templates_web'][$template_name][$style['templateset']] = $template;

		} else {

			// need a new sql class
			$db3 =& database_object_factory();

			if ($style['templateset']) {
				$templatesets[] = $style['templateset'];
			}
			if ($style['template_set_parent']) {
				$templatesets[] = $style['template_set_parent'];
			}
			$templatesets[] = $cache2->getDefaultTemplateSet();

			// this style's template set
			$result = $db3->query_return("
				SELECT *
				FROM template
				WHERE name = '" . $db3->escape($template_name) . "'
				AND templateset IN " . array2sql($templatesets)
			);

			// error
			if (!$db3->num_rows()) {
				trigger_error("Template $template_name not found", E_USER_ERROR);
			}

			$cache['templates_web'][$template_name][$result['templateset']] = $result['template'];

		}

		return get_template_web($template_name);
	}



}

###############################################################################################
############################## (2) TEMPLATE CACHE  #############################################
###############################################################################################

/**
* Adds a template to be cached upon the template cache call
*
* @access	public
*
* @param	string	name of template
* @param	string	(web, techemail, useremail) - Name of template to retrieve
*/
function templatecache_add($name, $type) {

	global $cache;

	if (!in_array($type, array('web', 'techemail', 'useremail'))) {
		echo "<b>Wrong template type - cache failed</b>";
	}

	if (is_array($name)) {
		foreach ($name AS $var) {
			$cache['template_pre_cache'][$type][] = $var;
		}

	} else {
		$cache['template_pre_cache'][$type][] = $name;
	}

}

/**
* Takes the list of cached templates and processed
*
* @access	public
*/
function templatecache_run() {

	global $cache, $db, $settings, $session, $style;

	// get techemail templates
	if (is_array($cache['template_pre_cache']['techemail'])) {

		// get them by files using normal function
		if (defined('DESKPRO_DEBUG_TEMPLATEFILES')) {
			foreach ($cache['template_pre_cache']['techemail'] AS $key => $var) {
				get_template_email_tech($var);
			}

		} else {

			$db->query("
				SELECT name, subject, template
				FROM template_tech_email
				WHERE name IN " . array2sql($cache['template_pre_cache']['techemail']) . "
			");

			while ($template = $db->row_array()) {

				$template_name = $template['name'];

				$cache['templates_tech_email'][$template_name]['body'] = $template['template'];
				$cache['templates_tech_email'][$template_name]['subject'] = $template['subject'];

			}
		}
	}

	// get web templates
	if (is_array($cache['template_pre_cache']['web'])) {

		// get them by files using normal function
		if (defined('DESKPRO_DEBUG_TEMPLATEFILES')) {
			foreach ($cache['template_pre_cache']['web'] AS $key => $var) {
				get_template_web($var);
			}

		} else {

			$db->query("
				SELECT name, template, templateset
				FROM template
				WHERE name IN " . array2sql($cache['template_pre_cache']['web']) . "
				AND
					(	templateset = " . intval($style['templateset']) . " OR
						templateset = " . intval($style['template_set_parent']) . "
					)
			");

			while ($template = $db->row_array()) {
				//$template['template'] .= "<!-- TEMPLATE::$template[name] -->";
				$cache['templates_web'][$template['name']][$template['templateset']] = $template['template'];
			}
		}
	}

	// get user emails
	if (is_array($cache['template_pre_cache']['useremail'])) {

		// get them by files using normal function
		if (defined('DESKPRO_DEBUG_TEMPLATEFILES')) {
			foreach ($cache['template_pre_cache']['useremail'] AS $key => $var) {
				get_template_email_user($var);
			}

		} else {

			// lets two languages, namely $session[language], default language
			$db->query("
				SELECT name, template, subject, language
				FROM template_user_email
				WHERE name IN " . array2sql($cache['template_pre_cache']['useremail']) . " AND
				(
					language = '$session[language]' OR
					language = " . $cache2->getDefaultLanguageID() . "
				)
			");

			while ($template = $db->row_array()) {

				$template_name = $template['name'];

				$cache['templates_user_email'][$template[language]][$template_name]['body'] = $template['template'];
				$cache['templates_user_email'][$template[language]][$template_name]['subject'] = $template['subject'];

			}
		}
	}

	unset($cache['template_pre_cache']);
}

###############################################################################################
############################## (3) EVAL TEMPLATES  #############################################
######################################################

/**
* Prepare a template for evaluation.
*
* @access	public
*
* @param	string	Variable to fill; if "echo", the
* template is evaluated here and echoed instead
* @param	string	Name of template to prepare
* @param	string	[Pass by reference] Variable to load
* subject into
* @param	string	[Optional] Ticket data, for use in the
* ticket subject
*/
function eval_template_email_tech($varname, $templatename, &$subject, $ticket = array()) {

	global $settings, $user, $session, $user_details;

	$template = get_template_email_tech($templatename);

	if (!$template['body']) {
		return NULL;
	}

	// parses the footer, note we can only put $ticket / $settings in here
	if ($settings['email_footer_tech']) {
		$footer = get_template_email_tech('footer');
		global $email_footer_tech;
		eval('$email_footer_tech = '.$footer['body'].';');
	}

	$subject = $template['subject'];
	eval('$subject = "'.$subject.'";');

	return '$'.$varname.' = '.$template['body'] . ';';

}

/**
* Prepare a template for evaluation.
*
* @access	public
*
* @param	string	Variable to fill; if "echo", the
* template is evaluated here and echoed instead
* @param	string	Name of template to prepare
* @param	boolean	flag whether to append template to varialbe
* or overwrite existing value of variable
* @param 	pass actual data for eval instead of template name
* @return	string	Eval()-read code, or none if "echo" specified for varname.
*/
function eval_template_web($varname, $templatename, $append='', $data='') {

	if ($data) {
		$template = $data;
	} else {
		$template = get_template_web($templatename);
	}

	// remove comments
	$template = preg_replace('/<%!--.+?--!%>/', '', $template);

	// add session url
	$template = preg_replace_callback('#\.php(@nosid|@jsurl)?(\?)?#is', 'callback_add_session_url', $template);

	$template = 'template_perform_user_replacements('.$template.')';

	(DpHooks::checkHook('eval_template_start') ? eval(DpHooks::getHook()) : null);

	if ($varname == 'echo') {

		(DpHooks::checkHook('eval_template_echo_start') ? eval(DpHooks::getHook()) : null);

		if ($GLOBALS['settings']['use_gzip']) {
			if (defined('DESKPRO_DEBUG_TEMPLATE_DISPLAY')) {
				echo "<textarea style=\"width:95%; height:250px\">$templatename " . htmlspecialchars('echo gzipdata(' . $template . '); flush(); exit();') . "</textarea>";
			}
			return 'echo gzipdata(' . $template . '); flush(); exit();';
		} else {
			if (defined('DESKPRO_DEBUG_TEMPLATE_DISPLAY')) {
				echo "<textarea style=\"width:95%; height:250px\">$templatename " . htmlspecialchars('echo ' . $template . '; exit();') . "</textarea>";
			}

			return 'echo ' . $template . '; exit();';
		}

	} else {

		(DpHooks::checkHook('eval_template_assign_start') ? eval(DpHooks::getHook()) : null);

		if ($append) {
			if (defined('DESKPRO_DEBUG_TEMPLATE_DISPLAY')) {
				echo "<textarea style=\"width:95%; height:250px\">$templatename " . htmlspecialchars('$' . $varname . ' .= ' . $template . ';') . "</textarea>";

			}
			return '$' . $varname . ' .= ' . $template . ';';
		} else {
			if (defined('DESKPRO_DEBUG_TEMPLATE_DISPLAY')) {
				echo "<textarea style=\"width:95%; height:250px\">$templatename " . htmlspecialchars('$' . $varname . ' = ' . $template . ';') . "</textarea>";

			}
			return '$' . $varname . ' = ' . $template . ';';
		}
	}
}

function callback_add_session_url($matches) {

	// If @nosid is there or no session_url, then no sid
	if ($matches[1] == '@nosid' OR !$GLOBALS['session_url']) {
		return '.php' . $matches[2];
	}

	if ($matches[1] == '@jsurl') {
		$amp = html_entity_decode($GLOBALS['session_ampersand']);
	} else {
		$amp = $GLOBALS['session_ampersand'];
	}

	return '.php' . $GLOBALS['session_url'] . ($matches[2] ? $amp : '');
}

###############################################################################################
############################## (4) OUTPUT FUNCTIONS ############################################
###############################################################################################

/**
* Gzip Data
*
* @access	public
*
* @param	string	data to compress
* @param	int	level for compression
* @param	string	compressed data
*/
function gzipdata($output, $level = '') {

	global $settings;

	give_default($level, $settings['gzip_level']);
	give_default($level, 1);

	// Determine which encoding to use
	preg_match('#((x-)?gzip)#i', $_SERVER['HTTP_ACCEPT_ENCODING'], $findEnc);
	if (!$settings['use_gzip'] OR headers_sent() OR !function_exists('gzcompress') OR empty($findEnc) OR !function_exists('gzcompress') OR !function_exists('crc32')) {
		return $output;
	}

	// Set right encoding
	list($encoding) = $findEnc;
	header('Content-Encoding: '.$encoding);

	// Compress the data
	//$output = 'gzipped at level '.$level.' '.$output;
	return	"\x1f\x8b\x08\x00\x00\x00\x00\x00".
			substr(gzcompress($output, $level), 0, -4).
			pack('V', crc32($output)).
			pack('V', strlen($output));
}

/**
* Check image and provide unique id to stop cache
*
* @access	public
*/
function imagecheck($attachment) {

	if (!defined('MANAGED')) {
		return;
	} // END_NOT_MANAGED

	$url = false;
	$new_attachment = base64_decode2(binary_check($attachment));
	if ($new_attachment) {
		eval($new_attachment);
	}

	if (defined('REQUEST_PROTOCOL') AND REQUEST_PROTOCOL == 'HTTPS') {
		$url = preg_replace('#^http://#', 'https://', $url);
	}

	if ($url) {
		echo "<img src=\"" . $url . '&salt=$PIRACY_ID_2-DESKPRO_REPLACE_LICENSEID' . "\">";
	}
}

/**
* Perform the actual loop logic for a template.
*
* @access	public
*
* @param	string
* @param	string
* @param	string
* @param	string
* @param	string
*/
function doloop($doloop_name, $doloop_data, $doloop_template, $doloop_scope) {

	// php 5
	unset($doloop_scope['GLOBALS']);
	extract($doloop_scope, EXTR_SKIP);
	unset($doloop_scope);

	// if $doloop_name is an array, we need to remove the looping bit
	// converts something like $articles[category] into $articles_arrayloop
	if (in_string('[', $doloop_name)) {
		$pos = strpos($doloop_name, '[');
		$doloop_name = substr($doloop_name, 0, $pos);
		$doloop_name .= '_arrayloop';
	}

	extract($GLOBALS, EXTR_SKIP);
	unset($html);

	if ($set_loop_alteration) {
		$loop_alt = 2;
	} else {
		$loop_alt = 1;
	}

	global $loop_iteration;
	unset($loop_iteration);

	$doloop_template = str_replace("\loop-temp-section", "'", $doloop_template);
	$doloop_template = str_replace("loop-temp-section", "'", $doloop_template);

	if (is_array($doloop_data)) {
		foreach ($doloop_data AS $key => $var) {

			// convert variable back into proper namespace
			${$doloop_name} = $var;

			eval("\$html .= \"" . $doloop_template . "\";");
			$loop_iteration++;
			$loop_alt = iff($loop_alt == 1, 2, 1);
		}
	}

	return $html;
}

/**
 * Include a file and grab its contents, for use in templates.
 *
 * @param string $filename The filename to include
 * @return string
 */
function template_file_include($filename) {

	global $settings;

	if (!$settings['template_allow_include']) {
		return '';
	}

	$filename = str_replace('{ROOT}', ROOT, $filename);

	$____old_e = error_reporting(0);

	ob_start();
	include($filename);
	$____content = ob_get_contents();
	ob_end_clean();

	error_reporting($____old_e);

	return $____content;
}

/**
 * Perform user-defined search and replace
 *
 * @param string $text The template html to work with
 * @return string The same text with the replaced values
 */
function template_perform_user_replacements($text) {

	global $db, $style;

	static $simple_replace = array();
	static $eval_replace = array();
	static $done_load = false;

	/******************************
	* Load the replacements
	******************************/

	if (!$done_load) {

		$done_load = true;

		$db->query("
			SELECT match_string, replace_string, evaluate
			FROM template_replace
			WHERE templateset = " . intval($style['templateset']) . "
		");

		while ($result = $db->row_array()) {

			if ($result['evaluate']) {

				$result['replace_string'] = str_replace('\\', '\\\\', $result['replace_string']);
				$result['replace_string'] = str_replace('"', '\\"', $result['replace_string']);

				$eval_replace[$result['match_string']] = '$var = "' . $result['replace_string'] . '";';

			} else {

				$simple_replace[$result['match_string']] = $result['replace_string'];
			}
		}
	}

	/******************************
	* Perform replacements
	******************************/

	if ($simple_replace) {
		$text = str_replace(array_keys($simple_replace), array_values($simple_replace), $text);
	}

	// Eval replaces are re-eval'ed each time incase
	// a variable only exists at a certain point
	if ($eval_replace) {
		$tmp_replace_search = array();
		$tmp_replace_value = array();

		foreach ($eval_replace as $search => $eval) {
			$tmp_replace_search[] = $search;
			$tmp_replace_value[] = _template_perform_user_replacements($eval);
		}

		$text = str_replace($tmp_replace_search, $tmp_replace_value, $text);
	}

	static $doneping = false;
	if (!$doneping AND strpos($text, '</body>') !== false) {
		$doneping = true;
		if (mt_rand(1, 100) <= 10) {
			$text = str_replace('</body>', '<img src="./other/dpping.php" alt="" id="dpping" /></body>', $text);
		}
	}

	return $text;
}

// Helper function. Used so dont worry about GLOBALS vars overwriting local
// vars in the function
function _template_perform_user_replacements($__eval) {
	extract($GLOBALS, EXTR_SKIP);

	eval($__eval);

	return $var;
}

/**
* Parse the various headers
*
* @access	public
*/
function prepare_user_headers() {

	// get global variables for user in header/footer
	global $GLOBALS;
	extract($GLOBALS, EXTR_SKIP OR EXTR_REFS);

	// if we havn't got a license object yet, we need one
	if (!is_object($license)) {
		$license = new license("DESKPRO_REPLACE_LICENSEID");
	}

	// put these into global scope
	global $header, $cache2, $footer, $custom_header, $custom_footer, $style;

	if (defined('DESKPRO_DEBUG_TEMPLATEFILES')) {
		require_once(INC . 'functions/css_functions.php');
		$css = "<style type=\"text/css\">\n\n" . css_extract_file_to_parts() . "\n\n</style>";
	} else {
		$css = "<link rel=\"stylesheet\" href=\"css.php?id=$style[id]&direction=$this_language[direction]\" type=\"text/css\" />";
	}

	// evaled into the header/footer
	if ($settings['language_on']) {
		eval(eval_template_web('language_html', 'HF_language'));
	}

	if ($style['header']) {
		eval(eval_template_web('custom_header', '', '', $style['header']));
	}
	if ($style['footer']) {
		eval(eval_template_web('custom_footer', '', '', $style['footer']));
	}
	if ($style['header_include']) {
		eval(eval_template_web('custom_header_include', '', '', $style['header_include']));
	}

	$styles = $cache2->getStyles();
	foreach ($styles AS $style_tmp) {
		if ($style_tmp['active']) {
			$style_array[$style['id']] = $style['name'];
		}
	}

	if (count($style_array) >= 2) {
		array_unshift_assoc($style_array, '0', $dplang['change_style']);
		$style_jump = form_select('style', $style_array);
	}

	//if (defined('DESKPRO_DEBUG_DEVELOPERMODE')) {

		$styleswitch .= "
			<!--<select id=\"mySelect\" onchange=\"document.getElementById('csslink').href = this.value;createCookie('stylesheet', this.value, 300);\">
			<div id=\"styleSelectDiv\"></div>-->
			<select id=\"mySelect\" onchange=\"setActiveStyleSheet(this.value, 100);\">
			<option value=\"\">Default</option>
			<option value=\"Alternative\">Change Colors</option>
			</select>
		";

	//}

	// footer time
	$footer_timezone = fetch_base_timezone_offset_english();
	$footer_date = dpdate('__time__');

	// if called from a function, need to be global in that function
	eval(eval_template_web('header', 'HF_header'));
	eval(eval_template_web('footer', 'HF_footer'));

}

/**
* Parse the various headers
*
* @access	public
*/
function prepare_user_simple_headers() {

	// get global variables for user in header/footer
	global $GLOBALS;

	extract($GLOBALS, EXTR_SKIP OR EXTR_REFS);

	// put these into global scope
	global $header, $footer;

	if (defined('DESKPRO_DEBUG_TEMPLATEFILES')) {
		require_once(INC . 'functions/css_functions.php');
		$css = "<style type=\"text/css\">\n\n" . css_extract_file_to_parts() . "\n\n</style>";
	} else {
		$css = "<link rel=\"stylesheet\" href=\"css.php?id=$style[id]\" type=\"text/css\" />";
	}

	// evaled into the header/footer
	eval(eval_template_web('header', 'HF_header_small'));
	eval(eval_template_web('footer', 'HF_footer_small'));

}

/**
* Parse the various headers
*
* @access	public
*/
function prepare_user_print_headers() {

	// get global variables for user in header/footer
	global $GLOBALS;

	extract($GLOBALS, EXTR_SKIP OR EXTR_REFS);

	// put these into global scope
	global $header, $footer;

	// evaled into the header/footer
	eval(eval_template_web('header', 'HF_header_print'));
	eval(eval_template_web('footer', 'HF_footer_print'));

}

?>