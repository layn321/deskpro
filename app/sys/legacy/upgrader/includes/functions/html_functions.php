<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: html_functions.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - Utility functions for the administration and user interfaces
// +-------------------------------------------------------------+

function get_ext() {

	$html = get_javascript('../3rdparty/ext2/adapter/ext/ext-base.js');
	$html .= get_javascript('../3rdparty/ext2/ext-all.js');
	$html .= get_css('./../3rdparty/ext2/resources/css/ext-all.css');

	return $html;

}

function javascript($data) {

	echo "<script language=\"javascript\">";
	echo $data;
	echo "</script>";

}

/**
* Returns starting html anchor tag
* @access Public
* @param string $name	-	name of tag
* @return string		-	html of anchor tag
*/
function anchor($name) {
	return "<a name=\"$name\">";
}

/**
* Returns complete html anchor tag
* @access Public
* @param string $name	-	link to redirect
* @param string $link	-	Text of anchor
* @return string		-	html of anchor tag
*/
function anchor_link($name, $link) {
	return "<a href=\"#$name\">$link</a>";
}


/**
* Returns html for listing
* @access Public
* @param string array $array	-	array of text to be displayed in list
* @return string		-	html of list
*/
function array2list($array, $style='', $style2='') {

	if (is_array($array)) {
		$html = "<ul $style>";
		foreach ($array AS $var) {
			$html .= "<li $style2>$var</li>";
		}
		$html .= "</ul>";
		return $html;
	}
}

/**
* creates an image
* @access Public
* @param string $name		-	Name of the image on-disk
* @param string $alt		-	Alternate text to display for the image
* @param string $width		-	Width of the image
* @param string $height		-	Height of the image
* @param string $javascript	-	JavaScript to include in the <IMG> tag
* @return string			-	html of image tag
*/
function html_image($name, $alt='', $attributes='', $width='', $height='') {

	return;

	// check the file exists
	if (defined('DESKPRO_DEBUG_DEVELOPERMODE')) {
		if (!is_file(ROOT . '/images/' . $name)) {
			die("Image $name not found");
		}
	}

	$size = @getimagesize(ROOT . '/images/' . $name);
	if (!$width) {
		$width = $size[0];
	}
	if (!$height) {
		$height = $size[1];
	}

	$html = "<img src=\"" . constant('LOC_IMAGES') . $name . "\"";

	if ($alt) {
		$html .= " alt=\"$alt\" title=\"$alt\"";
	}
	if ($width) {
		$html .= " width=\"$width\"";
	}
	if ($height) {
		$html .= " height=\"$height\"";
	}
	if ($attributes) {
		$html .= ' ' . $attributes;
	}
	$html .= " border=\"0\" />";

	return $html;

}

/**
* seperate array in number of rows based on number of columns per row
* @access Public
* @param mixed array $data		-	array of data to be displayed
* @param int $columns			-	number of columns per row
* @return string array			-	array of rows
*/
function table_multicolumns($data, $columns) {

	// lower columns if we don't have enough data
	$i = count($data);
	$columns = iff($i < $columns, $i, $columns);

	if (is_array($data)) {
		foreach ($data AS $key => $var) {

			$count++;
			$row[] = $var;

			if ($count == $columns) {
				$table[] = $row;
				unset($row);
				unset($count);
			}
		}
	}

	// extra row
	if (is_array($row)) {
		$extra = $columns - count($row);
		if ($extra) {
			while ($extra > 0) {
				$row[] = '';
				$extra = $extra - 1;
			}
		}
		$table[] = $row;
	}
	return $table;
}

/**
* prints javascript to close window
* @access Public
*/
function close() {
	echo "<script language=\"javascript\">
	self.close();
	</script>";
}

/**
* creates close widnow image that closes window onclick
* @access Public
* @return string	-	html
*/
function close_page() {
	return "<br /><br /><div style=\"float:right\"><table><tr><td>" . html_image('icons/delete.gif') . "</td><td>&nbsp;&nbsp;<a href=\"javascript:self.close();\">Close Window</a>" . str_repeat("&nbsp;", 20) . "</td></tr></table></div>";
}

function bottom_link($icon, $name, $link) {
	return "<br /><br /><table align=\"right\"><tr><td>" . html_image($icon) . "</td><td>&nbsp;&nbsp;<a href=\"$link\">$name</a>" . str_repeat("&nbsp;", 20) . "</td></tr></table><br /><br />";
}

/**
* adds title to row
* @access private
* @param string $text	-	title to be displayed
* @return string		-	html
*/
function row_title($text) {
	return "<span class=\"row_title\">$text</span>";
}
/**
* starts a block with div tag
* @access private
* @return string		-	html
*/
function tabs_internal_start() {
	return "<div style=\"margin-left:5px; margin-right:5px\">";
}

/**
* ends a block with div tag
* @access private
* @return string		-	html
*/
function tabs_internal_end() {
	return "</div>";
}


function fieldset_start($title, $intspace=FALSE, $extra = '') {

	$html = "<fieldset $extra><legend>$title</legend>";
	if ($intspace) {
		$html .= "<div style=\"padding:6px\">";
	}
	return $html;
}

function fieldset_end($intspace=FALSE) {

	$html = '';
	if ($intspace) {
		$html = "</div>";
	}
	return $html . "</fieldset>";

}

function checkbox_option() {
	return "" . html_image('tech/arrow_ltr.gif') . "&nbsp;";
}

/**
* starts a section block with div tag
* @access private
* @return string		-	html
*/
function div_section_start() {
	return "<div class=\"div_section\">";
}

/**
* ends a section block with div tag
* @access private
* @return string		-	html
*/
function div_section_end() {
	return "</div>";
}

/**
* displays a message block with optional title
* @access public
* @param string $message	-	Message to be displayed
* @param string $title		-	optional title of message block
* @param string $icon		-	optional icon for message
* @return string			-	html
*/
function message_display($message, $title='', $icon='', $class='message_display') {

	$html = '';

	if ($title) {
		$html .= '<div class="content_head"><div class="p1"><div class="p2">';
		$html .= "$title";
		$html .= '</div></div></div>';
	}

	if (!$message) {
		return $html;
	}

	$html .= "<div class=\"$class\">";

	if ($icon) {
		$html .= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td style=\"padding:0;padding-right:10px\" valign=\"middle\">" . html_image($icon) . "</td><td width=\"100%\" valign=\"middle\" style=\"padding:0\">$message</td></tr></table>";
	} else {
		$html .= "$message";
	}

	$html .= "</div>";

	return $html;

}

###############################################################################
#######################		    RPC FUNCTIONS           #######################
###############################################################################

/*****************************************************
	function rpc_reload

-----DESCRIPTION: -----------------------------------
	generate element for tabs

*****************************************************/

function rpc_reload($content = '', $noecho = false, $time_offset = 0) {

	global $user;

	give_min($user['alert_frequency'], 1);
	$freq = $user['alert_frequency'] * 60 * 1000;

	echo '<script type="text/javascript">
		if (typeof alertTimeoutId != "undefined") {
			clearTimeout(alertTimeoutId);
		}
		var alertTimeoutId = setTimeout("callToServer(\'' . WEB . 'tech/rpc/alert.php?time=' . (TIMENOW - $time_offset) . '\');", ' . ($freq - $time_offset) . ');
		</script>
	';

	if (!$noecho) {
		echo $content;
	}

	return $content;

}




/*****************************************************
	function create_rpc

-----DESCRIPTION: -----------------------------------
	generate element for tabs

*****************************************************/

function create_rpc() {

	echo '<div id="rpc_content" style="display:hidden;width:1px;height:1px;overflow:hidden;"></div>';

}

function html_giveimage($image, $content) {

	return html_image($image, '', "style=\"vertical-align:middle\"") . "<span style=\"padding-left:8px\">$content</span>";

}

/*****************************************************
	function new_form

-----DESCRIPTION: -----------------------------------
	generate element for tabs

	name	: name of the element
	show	: if it should be initially visible

*****************************************************/

function new_form($action, $do='', $hidden='', $name='', $extra='', $target='') {

	give_default($name, 'dpform');

	$html = "<form method=\"post\" enctype=\"multipart/form-data\" " . iff($target, "target=\"$target\"") . " $extra name=\"$name\" action=\"$action\">";

	if ($do) {
		$html .= form_hidden('do', $do);
	}

	$html .= form_hidden('formaction', 1);

	// this creates a div that can be used to add stuff to the form, is especially used by pagenav() function
	if (!isset($nojump) OR !$nojump) {
		$html .= "<div id=\"hiddenbit_$name\"></div>";
	}

	if (is_array($hidden)) {
		foreach ($hidden AS $key => $var) {
			$html .= form_hidden($key, $var);
		}
	}
	return $html;
}


/**
 * From an array of data, create a series of hidden form fields. Handles arrays
 * just fine.
 *
 * @param array $data The data to work with
 * @param string $array The name of an array to make all the data be part of
 * @return string
 */
function array_to_form_hidden($data, $array = false) {

	if (!$data OR !is_array($data)) {
		return '';
	}

	$str = '';

	foreach ($data as $k => $v) {
		$name = htmlspecialchars($k, ENT_QUOTES);

		if ($array) {
			$name = "{$array}[$name]";
		}

		if (is_array($v)) {
			$str .= array_to_form_hidden($v, $name);
		} else {
			$value = htmlspecialchars($v);
			$str .= "<input type='hidden' name='$name' value='$value' />\n";
		}
	}

	return $str;
}


/*****************************************************
	function end_form

-----DESCRIPTION: -----------------------------------
	generate element for tabs

	name	: name of the element
	show	: if it should be initially visible

*****************************************************/

function end_form($text='', $js='', $nobr='') {

	if (!$text) {
		return "</form>";
	}
	if (!$nobr) {
		$html = "<br />";
	}

	$html .= "<div align=\"center\" style=\"padding-bottom:9px\"><input type=\"submit\" name=\"submittheform\" onclick=\"$js\" value=\"$text\"></div></form>";
	return $html;

}

/*****************************************************
	function generate_div

-----DESCRIPTION: -----------------------------------
	generate element for tabs

	name	: name of the element
	show	: if it should be initially visible

*****************************************************/

function generate_div($name, $class="section_div2") {
	echo "<div id=\"$name\" class=\"$class\" style=\"display:none; width:100%;\">";
}

/*****************************************************
	function end_div

-----DESCRIPTION: -----------------------------------
	generate element for tabs

*****************************************************/

function end_div() {
	echo "</div>";
}

###############################################################################
#######################		    TAB FUNCTIONS           #######################
###############################################################################

/*****************************************************
	function tabs_middle

-----DESCRIPTION: -----------------------------------
	generate element for tabs

*****************************************************/

function tabs_middle() {
	return html_image('spacer.gif', '', '', '100%', '5');
}

/*****************************************************
	function section_nav

-----DESCRIPTION: -----------------------------------
	creates the base for a horizontal section

	sections	: [array]
		[0] : Name of field
		[1] : Reference for field
	name		: name of the tab (to have multiple per page)
	formname	: name of the form we are in; is used to store the hidden variable that reloads the correct tabs next time
	inputstart	: create hidden input field so that we know which page we where on when submitting

*****************************************************/

function section_nav($sections, $name='tabs', $formname='', $class='section_div', $nomemory='', $extra='') {

	global $request;

	$j_sections = "";

	give_default($formname, 'dpform');
	give_default($name, 'tabs');

	echo "<input type=\"hidden\" name=\"tabs_selected_$name\">";

	echo "<div id=\"div_$name\"><ul id=\"tabs\" class=\"tabs\">";
	if (is_array($sections)) {

		foreach ($sections AS $key => $var) {
			$j_sections .= "'$var[1]', ";
			echo "<li id=\"" . $name . '_' . $var[1] . "\"><a href=\"javascript:show_section_$name('$var[1]');";
			if (isset($extra[$var[1]])) {
				echo $extra[$var[1]];
			}
			echo "\">$var[0]</a></li>";
		}
	}
	echo "</ul></div>";
	$j_sections = substr($j_sections, 0, -2);

	?>

	<script language="javascript">

	/*
		function used to display/hide sections where one section
		should always be visible
	*/

	function show_section_<?php echo $name;?>(section, section2) {

		<?php
			// defined('ADMINZONE') AND
			if (!$nomemory) {
				echo "document.forms.$formname.tabs_selected_$name.value = section;";
			}
		?>

		if (visyes(section) == 'error') {
			visyes(section2);
			section = section2;
		}

		change_class('<?php echo $name;?>_' + section, 'tabs_selected');

		var sections = new Array(<?php echo $j_sections;?>);

		for (var i = 0; i < sections.length; i++) {
			var thissection = sections[i];
			if (thissection != section) {
				visno(thissection);
				change_class('<?php echo $name;?>_' + thissection, 'tabs');
			}
		}
	}

	</script>

	<?php

	echo "<div class=\"$class\">";

}

/*****************************************************
	function section_nav_end

-----DESCRIPTION: -----------------------------------
	creates the base for a horizontal section

	start		: this is the element to show if we are not submitting
	name		: this is the id of the tabs we are dealing with

*****************************************************/

function section_nav_end($start='', $name='tabs') {

	global $request;

	echo "</div>";

	// get what has been submitted
	$var = 'tabs_selected_' . $name;

	if ($request->getString($var, 'request')) {
		$var = $request->getString($var, 'request');
	} else {
		$var = '';
	}

	if (!$var OR $var == '') {
		$var = $start;
	}

	echo "
	<script language=\"javascript\">
		show_section_$name('$var', '$start');
	</script>

	";
}



function frameforce() {

	?>
	<script language="javascript">
	if (parent.frames.length > 0) {
		parent.location.href = self.document.location
	}
	</script>
	<?php

}

###############################################################################
#######################	  OTHER DISPLAY FUNCTIONS       #######################
###############################################################################

/*****************************************************
	function alert

-----DESCRIPTION: -----------------------------------
	Create a JavaScript alert to display a message.

-----ARGUMENTS: -------------------------------------
	text	Text to display in the alert box.

-----RETURNS:----------------------------------------
	None; directly generates output

*****************************************************/

function alert($text) {

	global $header;

	$header->build();

	if (is_array($text)) {
		foreach ($text AS $key => $var) {
			$tmp .= "$var \n";
		}
		$text = $tmp;
	}

	$text = addslashes_js($text);

	if ($text) {
		echo "<script language=\"javascript\">
		alert('$text');
		</script>";
	}
}

function alert_parent($text) {

	if (is_array($text)) {
		foreach ($text AS $key => $var) {
			$tmp .= "$var \n";
		}
		$text = $tmp;
	}

	$text = addslashes_js($text);

	if ($text) {
		echo "<script language=\"javascript\">
		alert('$text');
		</script>";
	}
}

/*****************************************************
	function redirect_button

-----DESCRIPTION: -----------------------------------
	Display a "no permission" error page

-----ARGUMENTS: -------------------------------------
	message		Message to display

-----RETURNS:----------------------------------------
	None; directly generates output and exits

*****************************************************/

function redirect_button($value, $url, $name="button") {

	if (defined('ADMINZONE')) {
		return "<input type=\"button\" name=\"$name\" value=\"$value\" onclick=\"javascript:location='$url'\">";
	} else {
		return "<input type=\"button\" name=\"$name\" value=\"$value\" onclick=\"javascript:top.center.location='$url'\">";
	}

}



/**
 * Generate a select box which will redirect the user based on choice
 *
 * @param array $options An array in the form of url=>text
 * @param bool $show_go Show a "go" button instead of relying on JS onchange
 * @param bool $html_escape Escape HTML
 * @return string The HTML for the select
 */
function select_jump($options, $show_go = true, $html_escape = true) {

	static $counter = 0;
	$counter++;

	$name = 'select_jump_' . $counter;
	$extra = "onchange=\"go_select('$name');\"";

	$html = form_select($name, $options, null, null, null, null, null, $extra);

	if ($show_go) {
		// If $show_go is a string then use it as text
		if ($show_go === true OR is_numeric($show_go)) {
			$show_go = "Go";
		}
		$html .= " <input type=\"button\" value=\"$show_go\" onclick=\"go_select('$name');\" />";
	}

	$html = '<div nowrap="nowrap" style="white-space:nowrap;">' . $html . '</div>';

	return $html;
}



/*****************************************************
	function form_jump

-----DESCRIPTION: -----------------------------------
	Display a popup message, then redirect to the specified
	form handler (passing hidden form values along).

-----ARGUMENTS: -------------------------------------
	url			URL to submit to
	message		Message to display before jumping
	hidden		Form values to submit

-----RETURNS: ---------------------------------------
	Nothing.

*****************************************************/

function form_jump($url, $message, $hidden='') {

	global $header;

	echo "<html><head><title>" . DP_NAME . "</title></head><body onload=\"deskpro.submit();\">";

	echo "<form method=\"post\" name=\"deskpro\" action=\"$url\">";

	if (is_array($hidden)) {
		foreach ($hidden AS $key => $var) {
			echo form_hidden($key, $var);
		}
	}

	exit();

}


/**
 * Output a Javascript-powered timed redirect with a countdown message.
 *
 * @param string $url The URL to redirect to
 * @param string $secs The number of seconds until the redirect
 * @param array $data Data you need to pass on to the next page (will be included in the form of hidden fields)
 * @param string $message The message to display
 * @param string $method The form method (get or post)
 */
function jump_countdown($url, $secs = 5, $data = array(), $message = 'Continuing in {#} seconds', $method = 'get') {

	// Get proper method
	$method = strtolower($method);

	if (!in_array($method, array('get', 'post'))) {
		$method = 'get';
	}

	if (!$message) {
		$message = 'Continuing in {#} seconds';
	}

	// Message needs to be JS safe
	$message = addslashes_js($message);

	/********************
	* Form
	********************/

	echo "\n\n\n\n<form action=\"{$url}\" method=\"{$method}\" name=\"jump_countdown\" id=\"jump_countdown\" style=\"margin:0;padding:0;display:inline;\">\n";

	if (is_array($data) AND $data) {
		foreach ($data as $k => $v) {
			echo form_hidden($k, $v);
		}
	}

	echo "<span id=\"jump_countdown_message\"></span>\n";

	echo "</form>\n\n\n\n";

	/********************
	* JS
	********************/

	?>
	<script type="text/javascript">
	var jump_countdown = {
		message: "<?php echo $message; ?>",
		formId: "jump_countdown",
		messageId: "jump_countdown_message",
		formEl: false,
		messageEl: false,
		timer: false,
		count: 0,
		maxCount: <?php echo $secs; ?>,

		init: function() {
			this.formEl = document.getElementById(this.formId);
			this.messageEl = document.getElementById(this.messageId);

			this.updateMessage();
			this.timer = setTimeout("jump_countdown.countDown();", 1000);
		},

		countDown: function() {
			this.count++;

			if (this.count >= this.maxCount) {
				this.performJump();
			} else {
				this.updateMessage();
				this.timer = setTimeout("jump_countdown.countDown();", 1000);
			}
		},

		updateMessage: function() {
			var newMessage = this.message.replace(/\{#\}/g, (this.maxCount - this.count));
			this.messageEl.innerHTML = newMessage;
		},

		performJump: function() {
			this.formEl.submit();
		}
	};
	jump_countdown.init();
	</script>

	<?php

}





/*****************************************************
	function jump

-----DESCRIPTION: -----------------------------------
	Redirect to another URL

-----ARGUMENTS: -------------------------------------
	url			URL to redirect to
	message		Message to show during redirection

-----RETURNS: ---------------------------------------
	Nothing.

*****************************************************/

function jump($url, $message = '', $speed = '') {

	global $header, $settings;

	$do_redirect = true;
	if (defined('DESKPRO_DEBUG_NO_AUTO_REDIRECT')) {
		$do_redirect = false;
	}

	/*************************
	* Immediate redirect
	*************************/

	if ($settings['tech_immediate_redirects'] AND $do_redirect) {

	    if (!headers_sent()) {
	        header('Location: ' . $url);
	    } elseif ($header->done) {

	        ?>
	        <a href="<?php echo $url; ?>">Continue...</a>
	        <script type="text/javascript">
            window.location = '<?php echo $url; ?>';
            </script>
            <?php
            $header->simplefooter();

	    } else {
	       ?>
	        <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
            <html>
            <head>
                <meta http-equiv="refresh" content="0;url=<?php echo $url; ?>" />
                <script type="text/javascript">
                window.location = '<?php echo $url; ?>';
                </script>
            </head>
            <body>
                <a href="<?php echo $url; ?>">Continue...</a>
            </body>
            </html>
            <?php
	    }

	    exit;
	}



	/*************************
	* Normal redirect
	*************************/

	if ($speed == 'slow') {
		$time = 5;
	} else {
		$time = 2;
	}

	if ($do_redirect) {
		$header->simple('', "<meta http-equiv=\"refresh\" content=\"$time;url=" . $url . "\">");
	}

	?>

	<div style="margin-top:200px; text-align:center;">
	<b><?php echo $message; ?></b>
	<br />
	<br />
	You are now being re-directed<br />
	<a href="<?php echo $url;?>">Click here if you do not want to wait any longer (or if your browser does not automatically forward you).</a></smallfont>
	</div>

	<br /><br />

	<?php if ($do_redirect): ?>
	<script type="text/javascript">
	setTimeout(function() {
		window.location = '<?php echo $url; ?>';
	}, <?php echo $time * 1000; ?>);
	</script>
	<?php endif; ?>

	<?php

	$header->simplefooter();

	exit();

}

/*****************************************************
	function jprompt_multi

-----DESCRIPTION: -----------------------------------
	Return HTLM to generate a link that produces a double
	popup; the first confirms that an action is desired, and
	the second selects between two actions.

-----ARGUMENTS: -------------------------------------
	message		First message to display
	message2	Second message to display
	urltrue		URL to redirect to if second message is answered with "true"
	urlfalse	URL to redirect to if second message is answered with "false"
	text		Link text to display

-----RETURNS: ---------------------------------------
	HTML to generate the link.
*****************************************************/

function jprompt_multi($message, $message2, $urltrue, $urlfalse, $text) {
	return "<a href=\"javascript:jprompt_multi('$message', '$message2', '$urltrue', '$urlfalse')\">$text</a>";
}

/**
 * Output a script tag for an external JS file.
 *
 * @param string $loc The filename
 * @param stirng $frompath Where the file is located. Defaults to LOC_JAVASCRIPT
 * @param boolean $cache_refresh True to add the internal version to the path to update cache after upgrades etc
 * @return string
 */
function get_javascript($loc, $frompath = LOC_JAVASCRIPT, $cache_refresh = true) {

	global $settings;

	$path = $frompath . $loc;

	$cache_refresh = $cache_refresh ? "?v=$settings[deskpro_version_internal]" : '';

	if (defined('DESKPRO_DEBUG_ALWAYS_CACHEBUST')) {
		$cache_refresh = '?v=' . TIMENOW . dp_rand(1, 99);
	}

	// Just to add it to the included ones
	get_javascript_once($path, '');

	// But always return it
	return "<script type=\"text/javascript\" src=\"{$path}{$cache_refresh}\"></script>\n";
}

/**
 * Output a script tag for an external JS file but make sure its only included once.
 * Exactly the same as get_javascript() except that if it is called twice with the same
 * path, the second time will simply return an empty string.
 *
 * @param string $loc The filename
 * @param stirng $frompath Where the file is located. Defaults to LOC_JAVASCRIPT
 * @param boolean $cache_refresh True to add the internal version to the path to update cache after upgrades etc
 * @return string
 */
function get_javascript_once($loc, $frompath = LOC_JAVASCRIPT, $cache_refresh = true) {

	global $settings;

	static $included = array();

	$path = $frompath . $loc;

	$cache_refresh = $cache_refresh ? "?v=$settings[deskpro_version_internal]" : '';

	if (!in_array($path, $included)) {
		$included[] = $path;
		return "<script type=\"text/javascript\" src=\"{$path}{$cache_refresh}\"></script>\n";
	}

	return '';
}

/**
 * Output a link rel tag for an external CSS file.
 *
 * @param string $loc The filename
 * @param stirng $frompath Where the file is located. Defaults to LOC_CSS
 * @param boolean $cache_refresh True to add the internal version to the path to update cache after upgrades etc
 * @return string
 */
function get_css($loc, $frompath = LOC_CSS, $cache_refresh = true) {

	global $settings;

	$path = $frompath . $loc;
	$cache_refresh = $cache_refresh ? "?v=$settings[deskpro_version_internal]" : '';

	if (defined('DESKPRO_DEBUG_ALWAYS_CACHEBUST')) {
		$cache_refresh = '?v=' . TIMENOW . dp_rand(1, 99);
	}

	return "\n<link rel=\"stylesheet\" href=\"{$path}{$cache_refresh}\" type=\"text/css\" />\n";
}

/*****************************************************
	function quick_jump

-----DESCRIPTION: -----------------------------------
	- gets a css; takes account of where we are

-----ARGUMENTS: -------------------------------------
	action			: location of the css file

-----RETURNS:----------------------------------------
	The html to the css

*****************************************************/

function jump_quick($url) {

	if (!headers_sent() AND !defined('DESKPRO_DEBUG_NO_AUTO_REDIRECT')) {
		header("Location: $url");
		exit;
	}

	jump($url);
}

/*****************************************************
	function footer_html

-----DESCRIPTION: -----------------------------------
	- Generates the html for the footer in the tech area

-----ARGUMENTS: -------------------------------------
	onload		:	If specified, add an onLoad specifier to the BODY tag.
	searchid	:	If specified, tack on a $searchid value to the Ticket
					Control / Saved Tickets widget
	faq			:	If specified, the Open Footer Contents link needs to
					re-open this to 220 pixels high, not 120.

-----RETURNS: ---------------------------------------
	Nothing; directly produces output.
*****************************************************/

function footer_html_footer() {

	echo "</body></html>";


}

function footer_html($onload = '') {

	?>

	<html>
	<head>
	<title><?php echo DP_NAME; ?> Tech Center</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

	<?php
		echo get_css('default.css');
		echo get_javascript('general.js');
	?>

	<script language="javascript">
		function open_frame() {
			visno('close');
			visyes('open');
			top.document.getElementById('rowframeset').rows = '*, 120, 0';

		}
		function close_frame() {
			visno('open');
			visyes('close');
			top.document.getElementById('rowframeset').rows = '*, 20, 0';
		}
	</script>

	</head>

	<body text="#000000" leftmargin="0" marginwidth="0px" marginheight="0" topmargin="0" onload="open_frame(); <?php echo $onload;?>">

	<div id="close">
	<table width="780px" cellpadding="0" cellspacing="2"><tr><td width="20px" align="center"><a onclick="open_frame();"><?php echo html_image('tech/bul100.gif');?></a>
	</td><td><a onclick="open_frame();">Open Footer</a></td></tr></table>
	</div>

	<div id="open">

	<table cellspacing="0" cellpadding="0"><form name="form" method="post" action="./../home/quicksearch.php" target="center"><tr>
	<td width="20px" align="center"><a onclick="close_frame();"><?php echo html_image('tech/bul101.gif') ?></a></td>
	<td>

	<ul class="footertabs">

	<?php

	$tabs = array(
		'footer_search.php' => 'Ticket Search',
		'footer_select.php' => 'Ticket Select',
		'footer_searches.php' => 'Searches',
		'footer_users.php' => 'Users',
		'footer_links.php' => 'Links',
		'footer_tickets.php' => 'Ticket Control'
	);

	foreach ($tabs AS $filename => $title) {

		if ($filename == FILENAME) {
			$class = 'footertabs_selected';
		} else {
			$class = 'footertabs';
		}

		echo "<li class=\"$class\"><a href=\"./../home/" . $filename . "?searchid=$searchid\">$title</a></li>";

	}

	?>

	</ul>
	</td>

	</form>
	</table>

	<div class="footer_div" style="height:100px;">

	<?php

}


function swapbox($nameleft, $optionsleft, $nameright, $optionsright, $width="200px") {

	return "
		<TABLE BORDER=0 width=\"100%\">
		<TR>
			<TD VALIGN=MIDDLE ALIGN=CENTER width=\"35%\">
				<b>$nameleft</b><br />
				<SELECT  style=\"width:$width\" NAME=\"list11\" MULTIPLE SIZE=10 onDblClick=\"moveSelectedOptions(this.form.list11,this.form.list21,false)\">
					$optionsleft
				</SELECT>
			</TD>
			<td>&nbsp;</td>
			<TD VALIGN=MIDDLE ALIGN=CENTER width=\"20%\">
				<INPUT TYPE=\"button\" NAME=\"right\" VALUE=\"Add &gt;&gt;\" onClick=\"moveSelectedOptions(document.forms[0].list11,document.forms[0].list21,false);return false;\"><BR><BR>
				<INPUT TYPE=\"button\" NAME=\"right\" VALUE=\"Add All &gt;&gt;\" onClick=\"moveAllOptions(document.forms[0].list11,document.forms[0].list21,false); return false;\"><BR><BR>
				<INPUT TYPE=\"button\" NAME=\"left\" VALUE=\"&lt;&lt; Delete\" onClick=\"moveSelectedOptions(document.forms[0].list21,document.forms[0].list11,false); return false;\"><BR><BR>
				<INPUT TYPE=\"button\" NAME=\"left\" VALUE=\"&lt;&lt; Delete All\" onClick=\"moveAllOptions(document.forms[0].list21,document.forms[0].list11,false); return false;\">
			</TD>
			<td>&nbsp;</td>
			<TD VALIGN=MIDDLE ALIGN=CENTER width=\"35%\">
				<b>$nameright</b><br />
				<SELECT style=\"width:$width\" NAME=\"list21\" MULTIPLE SIZE=10 onDblClick=\"moveSelectedOptions(this.form.list21,this.form.list11,false)\">
					$optionsright
				</SELECT>
			</TD>
			<td>&nbsp;</td>
			<TD ALIGN=\"CENTER\" VALIGN=\"MIDDLE\" width=\"10%\">
				<b>Change Order</b><br />
				<input type=\"hidden\" name=\"stats\">
				<INPUT TYPE=\"button\" VALUE=\"&nbsp;Up&nbsp;\" onClick=\"moveOptionUp(this.form.list21)\">
				<INPUT TYPE=\"button\" VALUE=\"Down\" onClick=\"moveOptionDown(this.form.list21)\">
			</TD>
		</TR>
	</TABLE>";
}

?>