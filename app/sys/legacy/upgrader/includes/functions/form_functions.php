<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);


/**
* Utility functions for HTML form widget generation.
*
* @package DeskPRO
*/

###############################################################################################
############################################        DATE / TIME         #####################################
###############################################################################################

/**
* displays a check box with title as "Treat as HTML" on right hand
*
* @access	Public
*
* @param	string	name of check box
* @param	bool	optional flag whether check box is checked or not
*
* @return	 string	html code
*/
function html_checkbox($variable, $checked='') {

	$variable .= '_ishtml';

	return '<div style="padding:3px; width:110px; border: 1px #CCCCCC solid;">' . form_checkbox($variable, 1, $checked) . "&nbsp;&nbsp;&nbsp<label for=\"$variable\">Treat as HTML</label></div>";

}

/**
* create an input box for date entry using a dhtml menu
*
* @access	Public
*
* @param	string	optional name of date input field
* @param	string	optional date to be displayed by default possible values
* "NOW" to display current date
* "FORM" - to display already entered value any date value
*
* @return	 string	html code
*/
function form_date($variable='date', $date='', $nopop = false) {

	global $request;

	/***********
	* Select the current date
	***********/

	// use now
	if ($date == 'NOW') {

		$date = convert_gmt_timestamp_to_local_input(TIMENOW);

	// use the value submitted by form
	} elseif ($date == 'FORM') {

		$date = $request->getArrayString($variable);

	// use a numeric
	} elseif (is_numeric($date) AND $date > 0) {
		$date = convert_gmt_timestamp_to_local_input($date);
	}

	// the other option is an array for $date; which all the others are converted to so it is covered
	if (dpcheckdate($date)) {
		$month = $date['month'];
		$day = $date['day'];
		$year = $date['year'];
	}

	// we load the javascript & css if this is first time here
	if (!defined('DESKPRO_JSLOADED_DATA')) {
		$html .= get_javascript('./../3rdparty/selectcalendar/calendar.js');
		$html .= get_javascript('./../3rdparty/selectcalendar/lang/calendar-en.js');
		$html .= get_javascript('./../3rdparty/selectcalendar/calendar-setup.js');
		$html .= get_css('./../3rdparty/selectcalendar/calendar-win2k-cold-1.css');
		define('DESKPRO_JSLOADED_DATA', 1);
	}

	// random button link
	$button = 'data' . dp_rand(1,1000000);

	// the html for creating the calendar
	$html .= "
	<table cellspacing=\"0\" cellpadding=\"0\"><tr>
		<td style=\"padding-right:4px\">" . form_select_day($variable . '[day]', $day, $variable . '_day') . "</td>
		<td style=\"padding-right:4px\">" . form_select_month($variable . '[month]', $month, $variable . '_month'). "</td>
		<td style=\"padding-right:4px\">" . form_select_year($variable . '[year]', $year, $variable . '_year') . "</td>
		<td style=\"padding-right:4px\">

	<input style=\"display:none\" type=\"text\" value=\"$current\" name=\"$variable" . "_selector\" id=\"$variable\" /></td>";

	if (!$nopop) {

		$html .= "<td>" . html_image('icons/view_calendar.gif', '', "id=\"$button\" title=\"Date selector\"
      onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\"");

		if ($time) {

			$html .= "
			<script type=\"text/javascript\">
				Calendar.setup({
					inputField     :    \"$variable" . "_selector\",
					ifFormat       :    \"%Y-%m-%d %H:%M\",
					showsTime      :    true,
					button         :    \"$button\",
					singleClick    :    true,
					align			:	'Bl',
					step           :    1,
					onSelect       :   onSelect
				});
			</script>
			";

		} else {

			$html .= "
			<script type=\"text/javascript\">
				Calendar.setup({
					inputField     :    \"$variable\",
					ifFormat       :    \"%Y-%m-%d\",
					showsTime      :    false,
					button         :    \"$button\",
					singleClick    :    true,
					align			:	'Bl',
					step           :    1,
					onSelect       :   onSelect
				});
			</script>
			";

		}

		$html .= "</td>";

	}

	$html .= "</tr></table>";

	return $html;
}


/**
* same as form_date_multi, just none of the time options
*
* @access	Public
*
* @param	string	optional name of date input field
* @param	string array	optional date to be displayed by default in both fields
* @param 	bool if set and an end date is set, it will be set as 23:99 for the time
* @return	 string	html code
*/
function form_date_only_multi($variable, $start='', $end='') {

	global $request;

	// timestamps get converted to dates (treat as in GMT)
	if (is_numeric($start)) {
		$start = array(
			'day' => fetch_day($start),
			'month' => fetch_month($start),
			'year' => fetch_year($start)
		);
	}

	// timestamps get converted to dates (treat as in GMT)
	if (is_numeric($end)) {
		$end = array(
			'day' => fetch_day($end),
			'month' => fetch_month($end),
			'year' => fetch_year($end)
		);
	}

	if (!is_array($start)) {
		$start = array();
	}

	if (!is_array($end)) {
		$end = array();
	}

	if ($end['day'] AND $end_add_time) {
		$end['hour'] = 23;
		$end['minute'] = 59;
	}

	$calendar ="
		<table id=\"" . $variable . "_date\" cellspacing=\"0\" cellpadding=\"0\"><tr>
			<td>From&nbsp;</td>
			<td style=\"padding-right:4px\">" . form_date($variable . '_start', $start)  . "</td>
			<td> ago until &nbsp; " .
			"<td>" . form_date($variable . '_end', $end)  . "</td>
			</td>
		</tr></table>
	";

	return "<div style=\"width:545px\">" . $calendar . "<script>$visno</script></div>";

}

/**
* create an input box for from and to date entry using a dhtml menu
* Creates two arrays $variable_start and $variable_end with:
*			number, datetype, day, month, year variables
*
* @access	Public
*
* @param	string	optional name of date input field
* @param	string array	optional date to be displayed by default in both fields
* @param 	bool if set and an end date is set, it will be set as 23:99 for the time
* @return	 string	html code
*/
function form_date_multi($variable, $start='', $end='') {

	global $request;

	// timestamps get converted to dates (treat as in GMT)
	if (is_numeric($start)) {
		$start = array(
			'day' => fetch_day($start),
			'month' => fetch_month($start),
			'year' => fetch_year($start)
		);
	}

	// timestamps get converted to dates (treat as in GMT)
	if (is_numeric($end)) {
		$end = array(
			'day' => fetch_day($end),
			'month' => fetch_month($end),
			'year' => fetch_year($end)
		);
	}

	if (!is_array($start)) {
		$start = array();
	}

	if (!is_array($end)) {
		$end = array();
	}

	if ($start['year'] AND !$start['day']) $start['day'] = 1;
	if ($start['year'] AND !$start['month']) $start['month'] = 1;

	if ($end['year'] AND !$end['day']) $end['day'] = 1;
	if ($end['year'] AND !$end['month']) $end['month'] = 1;

	if ($start['day'] AND $end['day']) {
		$visno = "showCalendar('$variable', 1);";
	} elseif ($start['day']) {
		$visno = "showCalendar('$variable');";
	} elseif ($start['number'] AND $end['number']) {
		$visno = "showTime('$variable', 1);";
	} elseif ($start['number']) {
		$visno = "showTime('$variable');";
	} else {
		$visno = "showCalendar('$variable');";
	}

	$time = "
		<table id=\"" . $variable . "_time\" cellspacing=\"0\" cellpadding=\"0\"><tr>
			<td style=\"padding-right:5px\">" . html_image('icons/todays_tasks.gif', '', "onclick=\"showCalendar('$variable');\"") . "</td>

			<td>From " . form_time_options($variable . '_start', $start['number'], $start['datetype']) . " ago until
				<span id=\"" . $variable . "_time_closed\"><a onclick=\"showTime('$variable', 1);\">now</a>.</span>
			</td>

			<td>
				<span id=\"" . $variable . "_time_open\">&nbsp;" . form_time_options($variable . '_end', $end['number'], $end['datetype']) . " ago [<a onclick=\"showTime('$variable');\">X</a>]</span>
			</td>

		</tr></table>
	";

	$calendar = "
		<table id=\"" . $variable . "_date\" cellspacing=\"0\" cellpadding=\"0\"><tr>

			<td style=\"padding-right:5px\">" . html_image('icons/view_calendar.gif', '', "onclick=\"showTime('$variable');\"") . "</td>
			<td>From&nbsp;</td>
			<td style=\"padding-right:4px\">" . form_date($variable . '_start', $start)  . "</td>
			<td> ago until
				<span id=\"" . $variable . "_date_closed\"><a onclick=\"showCalendar('$variable', 1, document.form);\">now</a>.</span></td>
			<td>&nbsp;</td>
			<td>
				<span id=\"" . $variable . "_date_open\">" . form_date($variable . '_end', $end)  . "</span></td>
			<td>
				<span id=\"" . $variable . "_date_open2\">&nbsp; ago [<a onclick=\"showCalendar('$variable');\">X</a>]</td></span>
			</td>

		</tr></table>
	";

	return "<div style=\"width:545px\">" . $time . $calendar . "<script>$visno</script></div>";

}

/**
* creates an input and drop down to get a time interval
*
* @access	Public
*
* @param	string	optional name of input field
* @param	string	optional time to be displayed by default
* @param	string	optional default time unit
* possible values 'minutes', 'hours', 'days', 'weeks', 'months', 'years'
*
* @return	 string	html code
*/
function form_time_options($variable='date', $number='', $type='hours') {

	$types = array('minutes', 'hours', 'days', 'weeks', 'months', 'years');
	return form_input($variable . '[number]', $number, 4, '', '', 'id="' . $variable . '_number"') . "&nbsp;&nbsp;&nbsp;" . form_select($variable . '[datetype]', $types, '', $type, 1, '', '', '', '', $variable . '_datetype');

}


function form_multi_time($variable, $start, $end) {

	return "
	<table>
	<tr>
		<td>At least </td>
		<td>" . form_time_options($variable . '_start', $start['number'], $start['datetype']) . "</td>
		<td> and less than </td>
		<td>" . form_time_options($variable . '_end', $end['number'], $end['datetype']) . "</td>
	</tr>
	</table>
	";

}


/**
* displays hours and minitues selection drop down boxes
*
* @access	Public
*
* @param	string	name of input field
* @param	string	optional default time to display
*	hours and minutes separated by :
* @param	bool	optional time format 12 hours or 24 hours
* true for 12 hours and false for 24 hours
*
* @return	 string	html code
*/
function form_time($name, $data) {

	$hours = make_numberarray(0, 23);
	$minutes = make_numberarray(0, 59);

	$hours = form_select('hours', $hours, $name, $data['hours'], '1');
	$minutes = form_select('minutes', $minutes, $name, $data['minutes'], 1);

	return $hours . " hours " . $minutes . ' minutes';

}

/**
* displays 1 to 31 numbers in a drop down for date selection
*
* @access	Public
*
* @param	string	optional name of input field
* @param	int	 optional default date to be displayed
* @param	string	optional id of the field
*
* @return	 string	html for the <select> field
*/
function form_select_day($name='', $day='', $id='') {

	global $cache2;

	$id = ifr($id, $name);

	return form_select($name, $cache2->getCalendarData('days'), '', $day, '', '', '', '', '', $id);

}

/**
* displays JAN to DEC short forms of month names in a drop down for month selection
*
* @access	Public
*
* @param	string	name of input field
* @param	int	default month to be displayed
* @param	string	optional id of the field
*
* @return	 string	html for the <select> field
*/
function form_select_month($name, $month, $id='') {

	global $cache2;

	// are we dealing with timestamp
	if ($month > 10000) {
		$month = fetch_month($month);
	}

	$id = ifr($id, $name);

	return form_select($name, $cache2->getCalendarData('months_short'), '', $month, '', '', '', '', '', $id);

}

/**
* displays 2001 to 2010 in a drop down for year selection
*
* @access	Public
*
* @param	string	name of input field
* @param	int	default year to be displayed
* @param	string	optional id of the field
*
* @return	 string	html for the <select> field
*/
function form_select_year($name, $year, $id='') {

	global $cache2;

	// are we dealing with timestamp
	if ($year > 10000) {
		$year = fetch_year($year);
	}

	$id = ifr($id, $name);

	return form_select($name, $cache2->getCalendarData('years'), '', $year, '', '', '', '', '', $id);

}

/**
* displays 1 to 24 in a drop down for hour selection
*
* @access	Public
*
* @param	string	name of input field
* @param	int	default hour to be displayed
* @param	string	optional id of the field
*
* @return	 string	html for the <select> field
*/
function form_select_hour($name, $hour, $id='') {

	global $cache2;

	$id = ifr($id, $name);

	return form_select($name, $cache2->getCalendarData('hours'), '', $hour, '', '', '', '', '', $id);

}

/**
* displays 1 to 59 in a drop down for year selection
*
* @access	Public
*
* @param	string	name of input field
* @param	int	default year to be displayed
* @param	string	optional id of the field
*
* @return	 string	html for the <select> field
*/
function form_select_minute($name, $minute, $id='') {

	global $cache2;
	$id = ifr($id, $name);

	return form_select($name, $cache2->getCalendarData('minutes'), '', $minute, '', '', '', '', '', $id);

}

###############################################################################################
############################################ STANDARD HTML ELEMENTS #################################
###############################################################################################

/**
* displays a form input box
*
* @access	Public
*
* @param	string	name of text field
* @param	string	default value to be displayed
* @param	int	optional size of text field, default 30
* @param	bool	optional whether text field is part of array or not
* true / false
* @param	bool	optional if we do not want to dp_html() the value
* true / false
* @param	string	optional any extra code in the <input> like js code
* @param	bool	 optional flag whether field is disabled or not
* true / false
*
* @return	 string	html for the <input> field
*/
function form_input($name, $value=NULL, $size="30", $array='', $override = NULL, $extra='', $disabled='') {

	if ($disabled) {
		$extra .= ' disabled="disabled"';
	}

	if (is_numeric($size)) {
		$size = "size=\"$size\"";
	} else {
		$size = "style=\"width:$size\"";
	}

	$value = html_form_escape($value, $override);

	if ($array) {
		$name = $array . "[" . $name . "]";
	}
	return "<input $extra onfocus=\"this.select()\" id=\"$name\" type=\"text\" name=\"$name\" value=\"$value\" $size />";
}

/**
* create a form hidden box
*
* @access	Public
*
* @param	string	name of hidden field
* @param	string	default value to be displayed
* @param	bool	optional whether text field is part of array or not
* true / false
* @param	bool	optional if we do not want to dp_html() the value
* true / false
*
* @return	 string	html for the <input> field
*/
function form_hidden($name, $value='', $arrayto='', $override = NULL) {

	if ($arrayto) {
		$name = $arrayto . "[" . $name . "]";
	}

	$value = html_form_escape($value, $override);

	return "<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\" />\n";
}

/**
* create a form simple button
*
* @access	Public
*
* @param	string	name and value of button
* @param	string	optional extra javascript code for button
*
* @return	 string	html for the <input type=button> field
*/
function form_button($value, $js='', $name = '') {

	if (!$name) {
		$name = $value;
	}

	$override = NULL;
	$value = html_form_escape($value, $override);

	return "<input type=\"button\" name=\"$name\" value=\"$value\" $js />";

}

/**
* create a form file browse field
*
* @access	Public
*
* @param	string	optional  name of the field
*
* @return	 string	html for the <input type=file> field
*/
function form_file($name = '') {

	if ($name == 'attachment') {
		unset($name);
		$name = "";
	}

	$value = html_form_escape($value, $override);

	$name = 'attachment' . $name;
	return "<input type=\"file\" name=\"$name\" />";

}

/**
* create a form password field
*
* @access	Public
*
* @param	string	name of the field
* @param	string	optional default value to be displayed
* @param	int	optional size of password field, default 30
* @param	bool	optional whether text field is part of array or not
* true / false
* @param	bool	optional if we do not want to dp_html() the value
* true / false
*
* @return	 string	html for the <input> field
*/
function form_password($name, $value="", $size="30", $array='', $override = NULL, $extra = '') {

	$value = html_form_escape($value, $override);

	if ($array) {
		$name = $array . "[" . $name . "]";
	}
	return "<input $extra onfocus=\"this.select()\" type=\"password\" id=\"$name\" name=\"$name\" value=\"$value\" size=\"$size\" />";
}

/**
* displays a form text area
*
* @access	Public
*
* @param	string	name of text field
* @param	int	optional number of columns - default 30
* @param	int	optional number of rows - default 5
* @param	string	default value to be displayed
* @param	bool	 optional whether text field is part of array or not
* true / false
* @param	bool	optional if we do not want to dp_html() the value
* true / false
* @param	string	optional any extra code in the <input> like js code
* @param	bool	 optional flag whether field is disabled or not
* true / false
*
* @return	 string	html for the <textarea> field
*/
function form_textarea($name, $cols='30', $rows='5', $value='', $to_array='', $override = NULL, $extra='', $disabled='') {

	$value = html_form_escape($value, $override);

	if ($disabled) {
		$extra .= ' disabled="disabled"';
	}
	if (is_numeric($cols)) {
		$cols = "cols=\"$cols\"";
	} elseif ($cols == '' OR $cols == 0) {
		$cols = "cols=\"60\"";
	} else {
		$cols = "style=\"width:$cols\"";
	}
	if ($rows == '' OR $rows == 0) {
		$rows = 5;
	}
	if ($to_array != "") {
		$name = $to_array . "[" . $name . "]";
	}
	$temp = "<textarea onfocus=\"this.select()\" name=\"$name\" id=\"$name\" $cols rows=\"$rows\" $extra>$value</textarea>\n";
	return $temp;
}

/**
* displays a form select field
*
* @access	Public
*
* @param	string	name of text field
* @param	array	text data for the select field
* @param	string	 create an array based on this field
* @param	string	optional default selected value
* @param	int	makes the value the same as the displayed value,
* otherwise it would use either the specified values if an associatve array or
* would use incrementing numbers if standard array
* @param	bool	optional if set to true displays "None Selected" as first element in list
* @param	int	number of rows displayed
* @param	string	 optional add some custom code in the <select > element.
* Generally used for javascript actions
* @param	bool	optional if we do not want to dp_html()
* @param	string	optional id of the field
* @param	bool	optional flag whether field is disabled or not
* @param	string	optional Check type for in_array, useful where 0 is different to null
*
* @return	 string	html for the <select> field
*/
function form_select($name, $array, $to_array='', $start='', $same='0', $extra='', $size='', $top='', $override = NULL, $id = NULL, $disabled = NULL, $strict=NULL) {

	$html = '';

	if ($size AND count($array) < $size) {
		$size = count($array) + intval($extra) + 1;
	}

	if ($to_array != '') {
		$name = $to_array . "[" . $name . "]";
	}

	if ($size) {
		$name = $name . "[]";
	}

	// don't show two rows if only 1, show 3 if two.
	if ($size == 1) {
		unset($size);
	} elseif ($size == 2) {
		$size = 3;
	}

	if (!$id) {
		$id = $name;
	}

	if ($disabled) {
		$disabled = " DISABLED=disabled ";
		$name = $name."_disable";
	}
	if ($size) {
		$html .= "<select name=\"$name\" id=\"$id\" size=\"$size\" $disabled $top MULTIPLE>\n";
	} else {
		$html .= "<select name=\"$name\" id=\"$id\" $disabled $top>\n";
	}
	if ($extra) {
		$html .= "<option value=\"-----\">None Selected</option>\n";
	}

	if (is_array($array)) {

		foreach ($array AS $key => $val) {

			if (is_array($val)) {
				if ($val[0] == 'OPTGROUP') {
					$html .= "<optgroup LABEL=\"" . $val[1] . "\">";
				}
			} else {

				if ($same) {
					$key = $val;
				}

				$val = html_form_escape($val, $override);
				$key = html_form_escape($key, $override);

				if (is_array($start)) {
					if (in_array($key, $start, $strict)) {
						$html .= "<option selected=\"selected\" value=\"$key\">$val</option>\n";
					} else {
						$html .= "<option value=\"$key\">$val</option>\n";
					}
				} else {
					if (compare_value($key, $start, $strict)) {
						$html .= "<option selected=\"selected\" value=\"$key\">$val</option>\n";
					} else {
						$html .= "<option value=\"$key\">$val</option>\n";
					}
				}
			}
		}
	}

	$html .= "</select>\n";
	return $html;
}

/**
* displays a form radio button
*
* @access	Public
*
* @param	string	name of text field
* @param	string	value to be displayed
* @param	bool	optional flag whether radio button is preselected or not - true / false
* @param	string	extra code to go in the <input > fields. Used for js actions
* @param	bool	optional flag whether radio button is part of array or not - true / false
*
* @return	 string	html for the <input type=radio> field
*/
function form_radio($name, $value, $start='', $extra='', $array='', $id='') {

	$value = html_form_escape($value, $override);

	if ($array != "") {
		$name = $array . "[" . $name . "]";
	}

	if (!$id) {
		$id = $name;
	}

	if ($start) {
		return "<input type=\"radio\" name=\"$name\" id=\"$id\" value=\"$value\" checked=\"checked\" $extra />";
	} else {
		return "<input type=\"radio\" name=\"$name\" id=\"$id\" value=\"$value\" $extra />";
	}
}

/**
* displays a form two radio buttons with labels Yes and No
*
* @access	Public
*
* @param	string	name of text field
* @param	bool	optional flag whether radio button is part of array or not - true / false
* @param	int	 optional flag whether Yes radio button is preselected or not - 0 / 1, default 0
* @param	int	 optional flag whether to display a third radio button with no label - 0 / 1, default 0
* @param	string	extra code to go in the <input > fields. Used for js actions
*
* @return	 string	html for the <input type=radio> field
*/
function form_radio_yn($name, $array='', $yes='0', $empty='0', $extra='', $value_y = 1, $value_n = 0) {

	if ($array) {
		$name = $array . "[" . $name . "]";
	}

	$id = 'radio_yn' . $name . $array;

	$html = "<div style=\"white-space: nowrap;\">";

	if ($empty) {
		if ($yes == "1") {
			$html .= "<label for=\"either_$id\">Either:</label> <input id=\"either_$id\" type=\"radio\" name=\"$name\" value=\"xxxNULLxxx\" $extra>\n";
			$html .= "<label for=\"yes_$id\">Yes:</label> <input id=\"yes_$id\" type=\"radio\" name=\"$name\" value=\"$value_y\"  checked=\"checked\"  $extra />\n";
			$html .= "<label for=\"no_$id\">No:</label> <input id=\"no_$id\" type=\"radio\" name=\"$name\" value=\"$value_n\"  $extra />\n";
		} elseif ($yes == "0") {
			$html .= "<label for=\"either_$id\">Either:</label> <input id=\"either_$id\" type=\"radio\" name=\"$name\" value=\"xxxNULLxxx\" $extra>\n";
			$html .= "<label for=\"yes_$id\">Yes:</label> <input id=\"yes_$id\" type=\"radio\" name=\"$name\" value=\"$value_y\" $extra />\n";
			$html .= "<label for=\"no_$id\">No:</label> <input id=\"no_$id\" type=\"radio\" name=\"$name\" value=\"$value_n\"  checked=\"checked\" $extra />\n";
		} else {
			$html .= "<label for=\"either_$id\">Either:</label> <input id=\"either_$id\" type=\"radio\" name=\"$name\" value=\"xxxNULLxxx\"  checked=\"checked\" $extra>\n";
			$html .= "<label for=\"yes_$id\">Yes:</label> <input id=\"yes_$id\" type=\"radio\" name=\"$name\" value=\"$value_y\" $extra />\n";
			$html .= "<label for=\"no_$id\">No:</label> <input id=\"no_$id\" type=\"radio\" name=\"$name\" value=\"$value_n\" $extra />\n";
		}
	} else {
		if ($yes) {
			$html .= "<label for=\"yes_$id\">Yes:</label> <input id=\"yes_$id\" type=\"radio\" name=\"$name\" value=\"$value_y\" checked=\"checked\" $extra />\n";
			$html .= "<label for=\"no_$id\">No:</label> <input id=\"no_$id\" type=\"radio\" name=\"$name\" value=\"$value_n\" $extra />\n";
		} else {
			$html .= "<label for=\"yes_$id\">Yes:</label> <input id=\"yes_$id\" type=\"radio\" name=\"$name\" value=\"$value_y\" $extra />\n";
			$html .= "<label for=\"no_$id\">No:</label> <input id=\"no_$id\" type=\"radio\" name=\"$name\" value=\"$value_n\" checked=\"checked\" $extra />\n";
		}
	}

	$html .= "</div>";

	return $html;
}

/**
* displays a form submit button
*
* @access	Public
*
* @param	string	text to be dislayed on button
* @param	string	otional name of text field
* @param	bool	optional if we do not want to dp_html() the value
* true / false
* @param	string	 image file source path
*
* @return	 string	html for the <input type=submit> field
*/
function form_submit($value, $name='submittheform', $override = NULL, $image = NULL) {

	$value = html_form_escape($value, $override);

	if ($image) {
		$image = " src=\"$image\"";
		$type = "image";
	} else {
		$type = "submit";
	}

	return "<input type=\"$type\" name=\"$name\" value=\"$value\"$image />";
}

/**
* displays a form check box
*
* @access	Public
*
* @param	string	otional name of text field
* @param	string	text to be dislayed on button
* @param	bool	optional flag whether check box is checked or not - true / false
* @param	bool	optional flag whether check box is part of an array or not
* true / false
* @param	bool	optional if we do not want to dp_html() the value
* true / false
* @param	string	optional javascript code
*
* @return	 string	html for the <input type=submit> field
*/
function form_checkbox($name='', $value, $checked=NULL, $arrayto='', $override = NULL, $js='', $id='') {

	$value = html_form_escape($value, $override);

	if ($arrayto) {
		$name = $arrayto . "[" . $name . "]";
	}

	if (!$id) {
		$id = $name;
	}

	if ($checked) {
		return "<input type=\"checkbox\" name=\"$name\" id=\"$id\" value=\"$value\" checked $js />";
	} else {
		return "<input type=\"checkbox\" name=\"$name\" id=\"$id\" value=\"$value\" $js />";
	}

}

###############################################################################################
############################################        SPECIAL         ######################################
###############################################################################################

/**
* displays a form text field with an dhtm menu for color selection
*
* @access	Public
*
* @param	string	otional name of text field
* @param	string	optional default value to be displayed
* @param	bool	optional flag whether check box is part of an array or not
* true / false
*
* @return	 string	html code
*/
function form_color($name, $value='', $array='') {

	if ($array) {
		$formname = $array . "[" . $name . "]";
	} else {
		$formname = $name;
	}

	$name .= dp_rand(1,10000);

	$name_img = $name . 'img';

	return "<input type=\"text\" maxlength=\"6\" name=\"$formname\" id=\"$name\" size=\"10\" value=\"$value\" onchange=\"ColorChanged(document.$name_img, this);\">&nbsp;<a href=\"javascript:void 0;\" onclick=\"ChangeColor(document.$name_img, document.getElementById('$name'));return false;\"><img type=\"image\" name=\"$name_img\" src=\"./../includes/3rdparty/colorpicker/dropper.gif\" id=\"$name_img\" style=\"BACKGROUND-COLOR:#" . $result['value'] . "\" class=\"ColorPicker\" align=\"absMiddle\"></a><script>ColorChanged(document.$name_img, document.getElementById('$name'));</script>";

}

/**
* displays a form select field with two options "ASC" and "DESC"
*
* @access	Public
*
* @param	string array	list of options
* @param	string	optional id of field
*
* @return	 string	html code
*/
function form_order($name, $order_fields, $data='') {

	global $request;

	$html = form_select('field', $order_fields, $name, $data['field']);
	$html .= '&nbsp;&nbsp;&nbsp;';
	$html .= form_select('direction', array('ASC' => 'ASC', 'DESC' => 'DESC'), $name, $data['direction']);

	return $html;

}

/**
* displays a wysiwyg editor
*
* @access	Public
*
* @param	string namename of editor
* @param	string	optional value
*
* @return	 string	html code
*/
function form_wysiwyg_simple($name, $value='') {

	require_once(INC . 'classes/class_DpFCKeditor.php') ;

	$wysiwyg = new DpFCKeditor($name);
	$wysiwyg->BasePath = WEB . 'includes/3rdparty/fckeditor/';
	$wysiwyg->Value	= $value;
	$wysiwyg->ToolbarSet = 'Basic';
	$wysiwyg->Height = '150';
	$wysiwyg->Width = '550';

	return $wysiwyg->CreateHtml();

}

/**
* displays a wysiwyg editor
*
* @access	Public
*
* @param	string namename of editor
* @param	string	optional value
*
* @return	 string	html code
*/
function form_wysiwyg($name, $value, $content_type='', $content_id='', $tempkey='', $height = 300) {

	require_once(INC . 'classes/class_DpFCKeditor.php') ;

	$wysiwyg = new DpFCKeditor($name) ;
	$wysiwyg->BasePath = WEB . 'includes/3rdparty/fckeditor/';
	$wysiwyg->Value	= $value;
	$wysiwyg->Height = $height;
	$wysiwyg->Config['ImageUploadURL'] = '../../../../../tech/home/uploadimage.php?content_type=' . $content_type . '&content_id=' . $content_id . '&tempkey=' . $tempkey;

	return $wysiwyg->CreateHtml();

}

/**
* displays a button that will open a url
*
* @access	Public
*
* @param	string	name and text of button
* @param	string	url that will be opened in new window
* @param	bool	optional target frame to open url in
* true to open new window	and false to open in same window
*
* @return	 string	html code
*/
function form_button_link($value, $url, $blank='', $prevent_double = false, $js = '') {

	if ($blank) {
		$action = "openWindow2('$url');";
	} else {
		$action = "window.location='$url';";
	}

	if ($prevent_double) {
		$action .= "this.disabled=true;";
	}

	if ($js) {
		$action = "var cancelproceed = false; $js if (!cancelproceed) { $action }";
	}

	return "<input type=\"button\" name=\"$value\" value=\"$value\" onclick=\"$action\"  />";

}

?>