<?php
// +-------------------------------------------------------------+
// | $Id: date_functions.php 6899 2010-06-30 06:59:30Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - functions that handle date formatting and conversion
// +-------------------------------------------------------------+

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
* Date functions
*
* @package DeskPRO
*/

/*
	NOTICE:
		This file contains a nummber of functions from the PHP Licensed Calendar Class
		Any function in this file can be used under the PHP License.
*/

/**
* Current UNIX timestamp
* @var	integer
*/
define('TIMENOW', time());


// Cant use microtime_float() because the function isn't
// included at the time date_functions.php is
list($usec, $sec) = explode(' ', microtime());
$microtime_float = ((float)$usec + (float)$sec);

/**
 * Current UNIX timestamp with microseconds
 * @var float
 */
define('TIMENOWF', $microtime_float);

// ##########################################################################
/**
* The current second
* @var	integer
*/
define('NOWSECOND', fetch_seconds(TIMENOW));

/**
* The current minute
* @var	integer
*/
define('NOWMINUTE', fetch_minute(TIMENOW));

/**
* Current hour
* @var	integer
*/
define('NOWHOUR', fetch_hour(TIMENOW));

/**
* Current month
* @var	integer
*/
define('NOWMONTH', fetch_month(TIMENOW));

/**
* Current day
* @var	integer
*/
define('NOWDAY', fetch_day(TIMENOW));

/**
* Current year
* @var	integer
*/
define('NOWYEAR', fetch_year(TIMENOW));

// ##########################################################################
/**
* Start of today in GMT
* @var	integer
*/
define('DAYSTART', dp_gmmktime_now('', '', '', 0, 0, 0));

/**
* End of today in GMT
* @var	integer
*/
define('DAYEND', dp_gmmktime_now('', '', '', 23, 59, 59));

// ##########################################################################
/**
* Seconds in a minute
* @var	integer
*/
define('TIMEMINUTE', 60);

/**
* Seconds in an hour
* @var	integer
*/
define('TIMEHOUR', 60 * TIMEMINUTE);

/**
* Seconds in a day
* @var	integer
*/
define('TIMEDAY', TIMEHOUR * 24);

/**
* Seconds in a week
* @var	integer
*/
define('TIMEWEEK', TIMEDAY * 7);

/**
* Seconds in a month (28 days or 4 weeks)
* @var	integer
*/
define('TIMEMONTH', TIMEDAY * 28);

/**
* Seconds in a year
* @var	integer
*/
define('TIMEYEAR', TIMEDAY * 365);


// ##########################################################################
/**
* Returns the timestamp for the start of the local day
* - We can not just add the timezone difference because the day may be different.
* For example GMT may be 11pm on the 1st of January. To work out the start of day we
* work out GMT 0 for 1st January. But in GMT + 4 it is 2nd of January so we need to work
* out the start date for 2nd of January in GMT +4. The difference is more than 4 hours.
*
* @access	public
*
* @return	integer	Total number of seconds from GMT we should calculate for
*/
function date_local_daystart() {

	// get local stamp
	$stamp = convert_gmt_to_local(TIMENOW);

	// we now want the start of the day based on this local stamp
	return dp_gmmktime(fetch_year($stamp), fetch_month($stamp), fetch_day($stamp), 0, 0, 0);

}

function date_local_dayend() {

	// get local stamp
	$stamp = convert_gmt_to_local(TIMENOW);

	// we now want the start of the day based on this local stamp
	return dp_gmmktime(fetch_year($stamp), fetch_month($stamp), fetch_day($stamp), 23, 59, 59);

}

// ##########################################################################
/**
* Calculates the user or global time zone and DST data
*
* @access	public
*
* @return	integer	Time zone for either the user or the global setting
*/
function fetch_base_timezone_offset($use_user = null) {

	global $settings, $user;

	if ($use_user === null) {
		$use_user = $user;
	}

	if (is_numeric($use_user['timezone'])) {
		$timezone = $use_user['timezone'];
		if ($use_user['timezone_dst'] == 1) {
			$timezone++;
		}
	} else {
		$timezone = $settings['timezone'];
		if ($settings['dst']) {
			$timezone++;
		}
	}

	return $timezone;
}

/**
 * Returns if DST is enabled for the user
 *
 * @return boolean True if it is, false otherwise
 */
function fetch_dst_status() {

	global $settings, $user;

	$dst = false;

	if (is_numeric($user['timezone'])) {
		if ($user['timezone_dst'] == 1) {
			$dst = true;
		}
	} else {
		if ($settings['dst']) {
			$dst = true;
		}
	}

	return $dst;
}

// ##########################################################################
/**
* Returns the total time zone offset for timestamps
*
* @access	public
*
* @return	integer	Total number of seconds from GMT we should calculate for
*/
function fetch_timezone_offset($use_user = null) {

	// We dont cache specific users offset in static var below, so always fetch it again
	if ($use_user !== null) {
		return $offset = TIMEHOUR * fetch_base_timezone_offset($use_user);
	}

	static $offset;

	if ($offset !== null) {
		return $offset;
	}

	return $offset = TIMEHOUR * fetch_base_timezone_offset();
}

// ##########################################################################
/**
* Get the english number for relation to GMT
*
* @access	public
*
* @return	string	Information string
*/
function fetch_base_timezone_offset_english() {

	$timezone = fetch_base_timezone_offset();
	$dst = fetch_dst_status();

	if ($dst) {
		$timezone -= 1;
	}

	$ret = $timezone;

	if ($timezone == 0) {
		$ret = '';
	}
	if ($timezone > 0) {
		$ret = '+' . $timezone;
	}

	if ($dst) {
		$ret .= ' (DST)';
	}

	return $ret;
}

// ##########################################################################
/**
* Constructs an information string about the current time
*
* @access	public
*
* @return	string	Information string
*/
function construct_time_explain($phrase1 = '', $phrase2 = '', $format = '__time__') {

	give_default($phrase1, 'Times are in GMT ');
	give_default($phrase2, 'The time now is ');

	return $phrase1 . fetch_base_timezone_offset_english() . ' : ' . $phrase2 . dpdate($format) . '.';

}

// ##########################################################################
/**
* Takes a locale timestamp and converts it to a GMT one. Use
* this for all date input.
*
* @access	public
*
* @param	integer	User timestamp
*
* @return	integer	GMT timestamp
*/
function convert_local_to_gmt($timestamp) {
	return $timestamp - fetch_timezone_offset();
}

// ##########################################################################
/**
* Takes a GMT timestamp and applies timezone offset
* information. Use this for outputting date/time data.
*
* @access	public
*
* @param	integer	GMT timestamp
*
* @return	integer	User timestamp
*/
function convert_gmt_to_local($timestamp) {
	return $timestamp + fetch_timezone_offset();
}

// ##########################################################################
/**
* Returns a timestamp
*
* @access	protected
*
* @param	integer	Year (2003)
* @param	integer	Month (9)
* @param	integer	Day (13)
* @param 	integer	Hour (13)
* @param	integer	Minute (34)
* @param	integer	Second (53)
*
* @return	integer	UNIX timestamp
*/
function dp_gmmktime($year, $month, $day, $hour = 0, $minute = 0, $seconds = 0) {

	static $dates = array();

	if (!isset($stamps["$year"]["$month"]["$day"]["$hour"]["$minute"]["$seconds"])) {
		$dates["$year"]["$month"]["$day"]["$hour"]["$minute"]["$seconds"] = @gmmktime($hour, $minute, $seconds, $month, $day, $year);
	}

	return $dates["$year"]["$month"]["$day"]["$hour"]["$minute"]["$seconds"];
}

function fetch_timestamp_add_day($timestamp) {
	$date = parse_timestamp_gmt($timestamp);
	return dp_gmmktime($date['0'], $date['1'], $date['2'] + 1, $date['3'], $date['4'], $date['5'], $date['6']);
}

// ##########################################################################
/**
* Returns a timestamp. Any unset values will be filled with
* the current value in GMT.
*
* @access	public
*
* @param	integer	Year (2003)
* @param	integer	Month (9)
* @param	integer	Day (13)
* @param	integer	Hour (13)
* @param	integer	Minute (34)
* @param	integer	Second (53)
*
* @return	integer	UNIX timestamp
*/
function dp_gmmktime_now($years = null, $months = null, $days = null, $hours = null, $minutes = null, $seconds = null) {

	global $settings;

	$hours = is_numeric($hours) ? $hours : NOWHOUR;
	$minutes = is_numeric($minutes) ? $minutes : NOWMINUTE;
	$seconds = is_numeric($seconds) ? $seconds : NOWSECOND;
	$months = is_numeric($months) ? $months : NOWMONTH;
	$days = is_numeric($days) ? $days : NOWDAY;
	$years = is_numeric($years) ? $years : NOWYEAR;

	return dp_gmmktime($years, $months, $days, $hours, $minutes, $seconds);
}

// ##########################################################################
/**
* Takes a UNIX timestamp, parses it, and then caches it
* so no more processing time is ever spent doing the
* transaction again. Output returned is: <pre>
* array (
*	[0] => year (2003)
* 	[1] => month (9)
*	[2] => day (5)
*	[3] => hour (15)
*	[4] => minute (39)
*	[5] => second (57)
*	[6] => days in month (31)
*	[7] => weeks in the year (50)
*	[8] => days in the week (0 for sunday)
* )</pre>
*
* @access	public
*
* @param	integer	UNIX timestamp
*
* @return	array	Parsed version of the UNIX timestamp in GMT
*/
function parse_timestamp_gmt($timestamp) {

	static $stamps = array();

	if ($timestamp <= 0) {
		if (function_exists('debug_print_backtrace')) {
			debug_print_backtrace();
		} else {
			print_r(debug_backtrace());
		}
		return false;
	}

	if (!isset($stamps["$timestamp"])) {
		$date = gmdate('Y n j H i s t W w', $timestamp);
		$stamps["$timestamp"] = sscanf($date, '%d %d %d %d %d %d %d %d %d');
	}

	return $stamps["$timestamp"];
}

// ##########################################################################
/**
* Returns the year of a specified timestamp
*
* @access	public
*
* @param	integer	UNIX timestamp
*
* @return	integer	Year (2004)
*/
function fetch_year($timestamp) {

	$date = parse_timestamp_gmt($timestamp);
	return intval($date[0]);
}

// ##########################################################################
/**
* Returns the month of a specified timestamp
*
* @access	public
*
* @param	integer UNIX timestamp
*
* @param	integer	Month (9)
*/
function fetch_month($timestamp) {

	$date = parse_timestamp_gmt($timestamp);
	return intval($date[1]);
}

// ##########################################################################
/**
* Returns the day for a specified timestamp
*
* @access	public
*
* @param	integer	UNIX timestamp
*
* @return	integer	Day (28)
*/
function fetch_day($timestamp) {

	$date = parse_timestamp_gmt($timestamp);
	return intval($date[2]);
}

// ##########################################################################
/**
* Returns the hour for the specified timestamp
*
* @access	public
*
* @param	integer	UNIX timestamp
*
* @return	integer	Hour (18)
*/
function fetch_hour($timestamp) {

	$date = parse_timestamp_gmt($timestamp);
	return intval($date[3]);
}

// ##########################################################################
/**
* Returns the minute of a specified timestamp
*
* @access	public
*
* @param	integer	UNIX timestamp
*
* @return	integer	Minute (35)
*/
function fetch_minute($timestamp) {

	$date = parse_timestamp_gmt($timestamp);
	return intval($date[4]);
}

// ##########################################################################
/**
* Returns the seconds of a specified timestamp
*
* @access	public
*
* @param	integer	UNIX timestamp
*
* @return	integer	Seconds (57)
*/
function fetch_seconds($timestamp) {

	$date = parse_timestamp_gmt($timestamp);
	return intval($date[5]);
}

// ##########################################################################
/**
* Returns the number of months between two timestamps
*
* @access	protected
*
* @param	integer	First timestamp
* @param	integer	Second timestamp
*
* @return	integer	Number of months
*/
function fetch_month_diff($first, $second) {

	if ($first > $second) {
		$first = $big;
		$second = $small;
	} else {
		$big = $second;
		$small = $first;
	}

	return fetch_month($big) - fetch_month($small) + (12 * (fetch_year($big) - fetch_year($small)));
}

// ##########################################################################
/**
* Finds the difference in days between two timestamps
*
* @access	public
*
* @param	integer First timestamp
* @param	integer	Second timestamp
*
* @return	integer	Number of days
*/
function fetch_day_diff($first, $second) {

	if ($first > $second) {
		$big = $first;
		$small = $second;
	} else {
		$big = $second;
		$small = $first;
	}

	return round(($big - $small) / TIMEDAY);
}

// ##########################################################################
/**
* Returns the number of days in a given month-year combo
*
* @access	public
*
* @param	integer	Year (2004)
* @param	integer	Month (11)
*/
function fetch_days_in_month($year, $month) {

	$date = parse_timestamp_gmt(dp_gmmktime($year, $month, 1));
	return intval($date[6]);
}

// ##########################################################################
/**
* Returns the first day of the month for a given month and year
*
* @access	public
*
* @param	integer	Year (2004)
* @param	integer	Month (11)
*
* @return	integer	Day (0 (Sunday) to 6 (Saturday))
*/
function fetch_month_first_day($year, $month) {

	$date = parse_timestamp_gmt(dp_gmmktime($year, $month, 1));
	return intval($date[8]);
}

// ##########################################################################
/**
* Returns the number of the week in a year
*
* @access	public
*
* @param	integer	Year (2004)
* @param	integer	Month (12)
* @param	integer	Day (18)
*
* @return	integer	Week number
*/
function fetch_week_number($year, $month, $day) {

	$date = parse_timestamp_gmt(dp_gmmktime($year, $month, $day));
	return intval($date[7]);
}

// ##########################################################################
/**
* Returns the number of weeks in a month
*
* @access	public
*
* @param	integer	Year (2004)
* @param	integer	Month (8)
* @param	integer	First day of the week (default: Monday)
*
* @return	integer	Number of weeks
*/
function fetch_weeks_in_month($year, $month, $firstday = 1) {

	$monthfirst = fetch_month_first_day($year, $month);

	if ($monthfirst > $firstday) {
		$weekdays = 7 - $monthfirst + $firstday;
		$weeks = 0;
	} else {
		$weekdays = $firstday - $monthfirst;
		$weeks = 0;
	}

	$weekdays %= 7;

	$result = intval(ceil((fetch_days_in_month($year, $month) - $weekdays) / 7) + $weeks);

	if ($monthfirst == 0) {
		$result += 1;
	}

	return $result;
}

// ##########################################################################
/**
* Returns the number of the day of the week (0 = Sunday, etc.)
*
* @access	protected
*
* @param	integer	Year (2004)
* @param	integer	Month (8)
* @param	integer	Day (12)
*
* @return	integer	Day number
*/
function fetch_day_of_week($year, $month, $day) {

	$date = parse_timestamp_gmt(dp_gmmktime($year, $month, $day));
	return intval($date[8]);
}

// ##########################################################################
/**
* Returns information regarding the previous month
*
* @access	public
*
* @param	integer	Year (2004)
* @param	integer	Month
*
* @return	array	[month] => month (6), [year] => year (2004)
*/
function fetch_previous_month($year, $month) {

	if ($month == 1) {
		$month = 12;
		$year--;
	} else {
		$month--;
	}

	return array('month' => $month, 'year' => $year);
}

// ##########################################################################
/**
* Returns information about the next month
*
* @access	public
*
* @param	integer	Year (2004)
* @param	integer	Month (12)
*
* @return	array	[month] => month (4), [year] => year (2004)
*/
function fetch_next_month($year, $month) {

	if ($month == 12) {
		$month = 1;
		$year++;
	} else {
		$month++;
	}

	return array('month' => $month, 'year' => $year);
}

// ##########################################################################
/**
* Returns information regarding the previous day
*
* @access	public
*
* @param	integer	Year (2004)
* @param	integer	Month (11)
* @param	integer	Day (26)
*
* @return	array	Information regarding the previous day: month, day, year
*/
function fetch_previous_day($year, $month, $day) {

	if ($day == 1) {
		$newmonth = fetch_previous_month($year, $month);
		$day = fetch_days_in_month($newmonth['year'], $newmonth['month']);
		$month = $newmonth['month'];
		$year = $newmonth['year'];
	} else {
		$day--;
	}

	return array('month' => $month, 'day' => $day, 'year' => $year);
}

// ##########################################################################
/**
* Returns information about the next day
*
* @access	public
*
* @param	integer	Year (2004)
* @param	integer	Month (11)
* @param	integer	Day (26)
*
* @return	array	Information regarding the next day: month, day, year
*/
function fetch_next_day($year, $month, $day) {

	$totaldays = fetch_days_in_month($year, $month);
	if ($day == $totaldays) {
		$newmonth = fetch_next_month($year, $month);
		$day = 1;
		$month = $newmonth['month'];
		$year = $newmonth['year'];
	} else {
		$day++;
	}

	return array('month' => $month, 'day' => $day, 'year' => $year);
}

// ##########################################################################
/**
* Creates an HTML select widget containing a time zone select
*
* @access	public
*
* @param	integer	Selected time zone
*
* @return	string	HTML of a form select
*/
function construct_timezone_select($timezone = null, $array='') {

	global $settings;

	$zones = array(
	    '-12' => '(GMT -12:00) Eniwetok, Kwajalein',
        '-11' => '(GMT -11:00) Midway Island, Samoa',
        '-10' => '(GMT -10:00) Hawaii',
        '-9' => '(GMT -9:00) Alaska',
        '-8' => '(GMT -8:00) Pacific Time (US &amp; Canada)',
        '-7' => '(GMT -7:00) Mountain Time (US &amp; Canada)',
        '-6' => '(GMT -6:00) Central Time (US &amp; Canada), Mexico City',
        '-5' => '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima',
        '-4.5' => '(GMT -4:30) Caracas',
        '-4' => '(GMT -4:00) Atlantic Time (Canada), La Paz, Santiago',
        '-3.5' => '(GMT -3:30) Newfoundland',
        '-3' => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
        '-2' => '(GMT -2:00) Mid-Atlantic',
        '-1' => '(GMT -1:00 hour) Azores, Cape Verde Islands',
        '0' => '(GMT) Western Europe Time, London, Lisbon, Casablanca',
        '1' => '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris',
        '2' => '(GMT +2:00) Kaliningrad, South Africa',
        '3' => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
        '3.5' => '(GMT +3:30) Tehran',
        '4' => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
        '4.5' => '(GMT +4:30) Kabul',
        '5' => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
        '5.5' => '(GMT +5:30) Mumbai, Kolkata, Chennai, New Delhi',
        '5.75' => '(GMT +5:45) Kathmandu',
        '6' => '(GMT +6:00) Almaty, Dhaka, Colombo',
        '6.5' => '(GMT +6:30) Yangon, Cocos Islands',
        '7' => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
        '8' => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong',
        '9' => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
        '9.5' => '(GMT +9:30) Adelaide, Darwin',
        '10' => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
        '11' => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
        '12' => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka',
	);

	if ($timezone == null) {
		$timezone = $settings['timezone'];
	}

	if (defined('TECHZONE') OR defined('ADMINZONE')) {
		return form_select('timezone', $zones, $array, $timezone, '', '', '', '', true);
	} else {
		return form_select('timezone', $zones, $timezone, '', '', '', 1, '', true);
	}
}

#############################################################################
############################ DATE OUTPUT FUNCTIONS ############################
#############################################################################

// ##########################################################################
/**
* Function to format a date in a variety of ways. This is
* now how all dates and times should be
* formatted.
*
* @access	public
*
* @return	string	Formatted date string
*/
function dpdate($format, $stamp = TIMENOW, $adjust = true) {

	global $settings;

	if (!($stamp > 0 AND $stamp < 9999999999)) {
		return '';
	}

	if ($adjust) {
		$stamp = convert_gmt_to_local($stamp);
	}

	switch ($format) {

		case '__int__': $format = 'd-m-Y'; break;
		case '__day__': $format = $settings['date_day']; break;
		case '__full__': $format = $settings['date_full']; break;
		case '__time__': $format = $settings['date_time']; break;

	}

	return gmdate($format, $stamp);
}

#############################################################################
############################ FORM CONVERSION FUNCTIONS ############################
#############################################################################

// ##########################################################################
/**
* Takes user-submitted data, validates it, and then turns it
* into a GMT timestamp.
*
* @access	public
*
* @param	array	Array of date inforation
* @param	bool	Whether or not to set it to the end of the day, rather than the beginning
*
* @return	integer	GMT timestamp
*/
function convert_local_input_to_gmt_timestamp($date, $end = false) {

	if (!dpcheckdate($date)) {
		return 0;
	}

	if ($end) {
		$timestamp = dp_gmmktime($date['year'], $date['month'], $date['day'], 23, 59, 59);
	} else {
		$timestamp = dp_gmmktime($date['year'], $date['month'], $date['day'], $date['hours'], $date['minutes'], $date['seconds']);
	}

	return convert_local_to_gmt($timestamp);
}

// ##########################################################################
/**
* Takes a timestamp and formats it to the user's time zone
*
* @access	public
*
* @param	integer	GMT timestamp
*
* @return	array	Array of formatted data: day, month, year
*/
function convert_gmt_timestamp_to_local_input($timestamp) {

	$timestamp = convert_gmt_to_local($timestamp);

	$date = gmdate('Y n j', $timestamp);
	$date = sscanf($date, '%d %d %d');

	return array(
		'day' => $date[2],
		'month' => $date[1],
		'year' => $date[0]
	);
}

// ##########################################################################
/**
* Returns the amount of the greatest unit of time that the
* seconds goes evenly into, otherwise it rounds the division
* of it into minutes
*
* @access	public
*
* @param	integer	Number of seconds
*
* @return	integer	Amount of the greatest unit of time or number of minutes
*/
function convert_seconds_to_largest_unit($seconds) {

	if (!($seconds % TIMEYEAR)) {
		return array($seconds / TIMEYEAR, 'years');
	} else if (!($seconds % TIMEMONTH)) {
		return array($seconds / TIMEMONTH, 'months');
	} else if (!($seconds % TIMEWEEK)) {
		return array($seconds / TIMEWEEK, 'weeks');
	} else if (!($seconds % TIMEDAY)) {
		return array($seconds / TIMEDAY, 'days');
	} else if (!($seconds % TIMEHOUR)) {
		return array($seconds / TIMEHOUR, 'hours');
	} else if (!($seconds % TIMEMINUTE)) {
		return array($seconds / TIMEMINUTE, 'minutes');
	} else {
		return array(round($seconds / TIMEMINUTE), 'minutes');
	}
}

// ##########################################################################
/**
* Multiplies an interval of time by a given number
*
* @access	public
*
* @param	integer	Number to multiply by
* @param	string	Type of time to multiply by: minutes, hours, days, weeks, months, years
*
* @return	integer	Multiplied amount
*/
function fetch_time_amount($number, $datetype) {

	if ($datetype == 'minutes') {
		return $number * TIMEMINUTE;
	} else if ($datetype == 'hours') {
		return $number * TIMEHOUR;
	} else if ($datetype == 'days') {
		return $number * TIMEDAY;
	} else if ($datetype == 'weeks') {
		return $number * TIMEWEEK;
	} else if ($datetype == 'months') {
		return $number * TIMEMONTH;
	} else if ($datetype == 'years') {
		return $number * TIMEYEAR;
	} else {
		return 0;
	}
}

// ##########################################################################
/**
* Takes an array of date/time since information and returns
* an exact timestamp
*
* @access	public
*
* @param	array	Array of values to convert
* @param	bool	Whether or not to calculate for the end of the day
*
* @return	integer	UNIX timestamp
*/
function convert_multi_date_to_timestamp($values, $end = false) {

	if ($values['number'] AND $values['datetype']) {

		$number = fetch_time_amount($values['number'], $values['datetype']);

		if ($number) {
			return TIMENOW - $number;
		}

	} else if ($values['year']) {
		if (!$values['day']) $values['day'] = 1;
		if (!$values['month']) $values['month'] = 1;

		return convert_local_input_to_gmt_timestamp($values, $end);
	}
}

// ##########################################################################
/**
* Return textual description of time/form
*
* @access	public
*
* @param	array	Array of values to convert
* @param	bool	Whether or not to calculate for the end of the day
*
* @return	integer	UNIX timestamp
*/
function convert_multi_date_to_text($values, $end = false, $verb, $before) {

 	if (is_numeric($time)) {

		$date = dpdate('__day__', $time);

		if ($before) {
			return "$verb after $date";
		} else {
			return "$verb before $date";
		}

	} else if ($values['number'] AND $values['datetype']) {

		$time = $values['number'] . ' ' . $values['datetype'];

		if ($before) {
			return "$verb in the last $time";
		} else {
			return "$verb prior to $time ago";
		}

	} else if ($values['year']) {

		if (!$values['day']) $values['day'] = 1;
		if (!$values['month']) $values['month'] = 1;

		$stamp = convert_local_input_to_gmt_timestamp($values, $end);
		$date = dpdate('__day__', $stamp);

		if ($before) {
			return "$verb after $date";
		} else {
			return "$verb before $date";
		}

	}
}

#############################################################################
############################ UTILITY FUNCTIONS ############################
#############################################################################

function explodeDMY($dmy) {

	$parts = explode('-', $dmy);
	return array('days' => $parts[0], 'months' => $parts[1], 'years' => $parts[2]);

}

// ##########################################################################
/**
* Checks the validity of a date
*
* @access	public
*
* @param	array	Array of date information: month, day, year
*
* @return	bool	Validity
*/
function dpcheckdate($date) {
	return @checkdate($date['month'], $date['day'], $date['year']);
}

#############################################################################
############################ TO CHECK ############################
#############################################################################

// ##########################################################################
/**
* Takes a timestamp and returns an array of how many years,
* days, hours, minutes, and seconds have elapsed since.
*
* @access	public
*
* @param	integer	UNIX timestamp
*
* @return	array	Array of how man years, days, seconds, hours, and minutes have elapsed
*/
function convert_timestamp_to_array($time) {

	$years = intval($time / TIMEYEAR);
	$time -= $years * TIMEYEAR;

	$days = intval($time / TIMEDAY);
	$time -= $days * TIMEDAY;

	$hours = intval($time / TIMEHOUR);
	$time -= $hours * TIMEHOUR;

	$minutes = intval($time / TIMEMINUTE);

	$seconds = intval($time - ($minutes * TIMEMINUTE));

	return array('years' => $years, 'days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds);
}

// ##########################################################################
/**
* Returns a human-readable representation of a timestamp
* @access	public
*
* @param	integer	UNIX timestamp
* @param	bool	Whther or not to use shorthand
*
* @return	string	Date string
*/
function convert_timestamp_to_human_readable($timestamp, $short = false, $detail = 2) {

	if (!$timestamp) {
		if ($short) {
			return '0s';
		} else {
			return '0 seconds';
		}
	}

	$time = convert_timestamp_to_array($timestamp);

	if ($time['years']) {
		if ($short) {
			$hrstring .= $time['years'] . 'y ';
		} else {
			if ($time['years'] > 1) {
				$hrstring .= "$time[years] years ";
			} else {
				$hrstring .= '1 year ';
			}
		}
		$limit++;
	}

	if ($limit >= $detail) {
		return $hrstring;
	}

	if ($time['days']) {
		if ($short) {
			$hrstring .= $time['days'] . 'd ';
		} else {
			if ($time['days'] > 1) {
				$hrstring .= "$time[days] days ";
			} else {
				$hrstring .= '1 day ';
			}
		}
		$limit++;
	}

	if ($limit >= $detail) {
		return $hrstring;
	}

	if ($time['hours']) {
		if ($short) {
			$hrstring .= $time['hours'] . 'h ';
		} else {
			if ($time['hours'] > 1) {
				$hrstring .= "$time[hours] hours ";
			} else {
				$hrstring .= '1 hour ';
			}
		}
		$limit++;
	}

	if ($limit >= $detail) {
		return $hrstring;
	}

	if ($time['minutes']) {
		if ($short) {
			$hrstring .= $time['minutes'] . 'm ';
		} else {
			if ($time['minutes'] > 1) {
				$hrstring .= "$time[minutes] minutes ";
			} else {
				$hrstring .= '1 minute ';
			}
		}
		$limit++;
	}

	if ($limit >= $detail) {
		return $hrstring;
	}

	if ($time['seconds']) {
		if ($short) {
			$hrstring .= $time['seconds'] . 's ';
		} else {
			if ($time['seconds'] > 1) {
				$hrstring .= "$time[seconds] seconds ";
			} else {
				$hrstring .= '1 second ';
			}
		}
		$limit++;
	}

	return trim($hrstring);
}

?>