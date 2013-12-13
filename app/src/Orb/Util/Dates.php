<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * Orb
 *
 * @package Orb
 * @category Util
 */

namespace Orb\Util;

use \Orb\Util\Numbers;

/**
 * Utility functions that work with dates and times.
 *
 * @static
 */
class Dates
{
	/**@#+
	 * How many seconds are in various units of time.
	 * @var int
	 */
	const SECS_MIN = 60;
	const SECS_HOUR = 3600;
	const SECS_DAY = 86400;
	const SECS_WEEK = 604800;
	const SECS_MONTH = 2419200;
	const SECS_YEAR = 29030400;
	/**#@-**/

	/**@#+
	 * Units of time
	 * @var int
	 */
	const UNIT_MINUTES = 'minutes';
	const UNIT_HOURS   = 'hours';
	const UNIT_DAYS    = 'days';
	const UNIT_WEEKS   = 'weeks';
	const UNIT_MONTHS  = 'months';
	const UNIT_YEARS   = 'years';
	/**#@-**/

	/**
	 * Check if a year is a leap year
	 *
	 * @param $year
	 * @return bool
	 */
	public static function checkLeapYear($year)
	{
		if (strlen($year) == 2) {
			if ($year == '00' OR $year < 20) {
				$year = '20' . $year;
			} else {
				$year = '19' . $year;
			}
		}

		$year = (int)$year;

		if ( $year % 400 == 0 OR ($year % 100 != 0 && $year % 4 == 0)) {
			return true;
		}

		return false;
	}

	/**
     * Get how many days are in a month.
     *
     * $year is required for checking of leap-years where Feb has 29 days. It can
     * be an integer like 2009, or an Orb_Date, or an array of date parts with an 'year' item.
     *
     * @param  int    $month  The month to check
     * @param  mixed  $year   The year to check in, defaults to this year.
     * @return int
     */
    public static function daysInMonth($month, $year = null)
    {
    	static $map = array(
    		1 => 31,
    		2 => 28,
    		3 => 31,
    		4 => 30,
    		5 => 31,
    		6 => 30,
    		7 => 31,
    		8 => 31,
    		9 => 30,
    		10 => 31,
    		11 => 30,
    		12 => 31,
    	);

		$month = (int)$month;
		$year  = (int)$year;

    	// Special case for leap years when Feb has 29 days
    	if ($month == 2) {
    		if (!$year) $year = date('Y');

    		if (self::checkLeapYear($year)) {
    			return 29;
    		}
    	}

    	if (!Numbers::inRange($month, 1, 12)) {
    		throw new \OutOfBoundsException("Invalid month `$month`. Must be 1-12.");
    	}

    	return $map[$month];
    }


    /**
     * Get a date object for the last day in a month. It will be the last second of the month,
     * useful for "end of month" boundries.
     *
     * @param  int  $month  The month to get, or null for current month
     * @param  int  $year   The year to get, or null for current year
     * @return \DateTime
     */
    public static function lastDayInMonth($month = null, $year = null)
    {
    	if ($month === null) $month = date('n');
    	if ($year === null) $year = date('Y');

		$month = (int)$month;
		$year  = (int)$year;

    	return new \DateTime('@' . mktime(23, 59, 59, $month+1, 0, $year));
    }


    /**
     * Get a date object for the first day in a month. It will be the first second of the month,
     * useful for "start of month" boundaries.
     *
     * @param  int  $month  The month to get, or null for current month
     * @param  int  $year   The year to get, or null for current year
     * @return \DateTime
     */
    public static function firstDayInMonth($month = null, $year = null)
    {
    	if ($month === null) $month = date('n');
    	if ($year === null) $year = date('Y');

		$month = (int)$month;
		$year  = (int)$year;

    	return new \DateTime('@' . mktime(0, 0, 0, $month, 1, $year));
    }


	/**
	 * Add or remove months from a date. This differs from DateTime::modify(x) in that
	 * only the month changes. That is, 2012-11-15 +1 month is 2012-12-15 (e.g., always 15th).
	 *
	 * If the day is over the next months number of days, the day is reset to the last day
	 * of the month. E.g., Jan 30 +1 month becomes Feb 28.
	 *
	 * @param \DateTime $date
	 * @param int       $mod_months  Months to modify by, can be negative
	 */
	public static function modMonths(\DateTime $date, $mod_months)
	{
		$month = (int)$date->format('n');
		$year  = (int)$date->format('Y');
		$day   = (int)$date->format('j');

		$new_date = clone $date;

		$neg = false;
		if ($mod_months < 1) {
			$neg = true;
			$mod_months = abs($mod_months);
		}

		do {
			if ($neg) {
				$month--;
				if ($month < 1) {
					$month = 12;
					$year--;
				}
			} else {
				$month++;
				if ($month > 12) {
					$month = 1;
					$year++;
				}
			}
		} while (--$mod_months);

		$max_day = self::daysInMonth($month, $year);
		if ($day > $max_day) {
			$day = $max_day;
		}

		$new_date->setDate($year, $month, $day);
		return $new_date;
	}


	/**
	 * Adds or removes years from a date.
	 *
	 * @param \DateTime $date
	 * @param int       $months  Months to modify by, can be negative
	 */
	public static function modYears(\DateTime $date, $mod_years)
	{
		$month = (int)$date->format('n');
		$year  = (int)$date->format('Y');
		$day   = (int)$date->format('j');

		$new_date = clone $date;

		$year += $mod_years;

		$new_date->setDate($year, $month, $day);
		return $new_date;
	}


	/**
	 * Takes a number of seconds and returns an array of details
	 * of how many years, minutes, hours, days and years it is.
	 *
	 * @param int $seconds
	 * @return array
	 */
	public static function secsToPartsArray($seconds)
	{
		$years = intval($seconds / self::SECS_YEAR);
		$seconds -= $years * self::SECS_YEAR;

		$days = intval($seconds / self::SECS_DAY);
		$seconds -= $days * self::SECS_DAY;

		$hours = intval($seconds / self::SECS_HOUR);
		$seconds -= $hours * self::SECS_HOUR;

		$minutes = intval($seconds / self::SECS_MIN);

		$seconds = intval($seconds - ($minutes * self::SECS_MIN));

		return array('years' => $years, 'days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds);
	}


	/**
	 * Take some date show readable form of seconds/minutes/hours/days/years ago
	 *
	 * @param  int    $seconds  The seconds
	 * @param  int    $detail   How much detail to go into, 1-5
	 * @param  array  $lang     Phrases to use for each unit
	 * @return string
	 */
	public static function dateToAgo(\DateTime $date, $detail = 2, $lang = null)
	{
		$ts = $date->getTimestamp();
		return self::secsToReadable(time() - $ts, $detail, $lang);
	}


	/**
	 * Take some secondsand show readable form of seconds/minutes/hours/days/years.s
	 *
	 * @param  int    $seconds  The seconds
	 * @param  int    $detail   How much detail to go into, 1-5
	 * @param  array  $lang     Phrases to use for each unit
	 * @return string
	 */
	public static function secsToReadable($seconds, $detail = 2, $lang = null)
	{
		static $lang_en = array(
			'seconds' => '%d seconds',
			'minutes' => '%d minutes',
			'hours' => '%d hours',
			'days' => '%d days',
			'years' => '%d years',
			'sep' => ' ',
		);

		static $lang_en_short = array(
			'seconds' => '%ds',
			'minutes' => '%dm',
			'hours' => '%dh',
			'days' => '%dd',
			'years' => '%dy',
			'sep' => ' ',
		);

		if (!$lang OR $lang == 'long') {
			$lang = $lang_en;
		} elseif ($lang == 'short') {
			$lang = $lang_en_short;
		} elseif (!is_array($lang)) {
			throw new \Exception('Language must be long, short or an array of phrases');
		}

		$parts = self::secsToPartsArray($seconds);
		$limit = 0;
		$str_parts = array();

		if ($parts['years']) {
			$str_parts[] = sprintf($lang['years'], $parts['years']);
			++$limit;
		}

		if ($limit < $detail) {
			if ($limit) ++$limit;
			if ($parts['days']) {
				$str_parts[] = sprintf($lang['days'], $parts['days']);
			}
		}

		if ($limit < $detail) {
			if ($limit) ++$limit;
			if ($parts['hours']) {
				$str_parts[] = sprintf($lang['hours'], $parts['hours']);
			}
		}

		if ($limit < $detail) {
			if ($limit) ++$limit;
			if ($parts['minutes']) {
				$str_parts[] = sprintf($lang['minutes'], $parts['minutes']);
			}
		}

		if ($limit < $detail) {
			if ($parts['seconds']) {
				$str_parts[] = sprintf($lang['seconds'], $parts['seconds']);
			}
		}

		return implode($lang['sep'], $str_parts);
	}


	/**
	 * Converts a timezone into a UTC timezone. This does actual time conversion between timezones.
	 *
	 * @param \DateTime $datetime
	 * @return \DateTime
	 */
	public static function convertToUtcDateTime(\DateTime $datetime)
	{
		$datetime2 = clone $datetime;
		$datetime2->setTimezone(self::tzUtc());

		return self::makeUtcDateTime($datetime2);
	}


	/**
	 * Creates a 'true' UTC time with an adjusted timestamp. This does NOT do any time conversions,
	 * it just re-creates a datetime object with the same date and time but with a UTC timezone. If you need
	 * to convert between timezones, use convertToUtcDateTime.
	 *
	 * When using setTimezone on a datetime, it doesn't change the internal representation of the date, only the output.
	 * (eg if you were to getTimezone() on each, they'd be the same value).
	 *
	 * So if you want a "real" UTC datetime object with the timestamp adjusted, you need to do the conversion based on setting
	 * the date.
	 *
	 * @param \DateTime $datetime
	 * @return \DateTime
	 */
	public static function makeUtcDateTime(\DateTime $datetime)
	{
		$utc_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
		return $utc_datetime;
	}


	/**
	 * @return \DateTimeZone
	 */
	public static function tzUtc()
	{
		static $tz;
		if (!$tz) {
			$tz = new \DateTimeZone('UTC');
		}

		return $tz;
	}

	/**
	 * Tries to find the name of the timezone for a given offset. Returns false if no timezone found.
	 *
	 * @param  float $offset
	 * @param  null $dst
	 * @return bool|string
	 */
	public static function timezoneOffsetToName($offset, $dst = null)
	{
		$offset *= 3600;

		if ($dst === null) {
			$dst = (bool)((int)date('I'));
		}

		$timezone = timezone_name_from_abbr('', $offset, $dst);

		if ($timezone !== false) {
			return $timezone;
		}
		foreach (timezone_abbreviations_list() as $abbr) {
			foreach ($abbr as $city) {
				if ((bool)$city['dst'] === $dst && $city['timezone_id'] && $city['offset'] == $offset) {
					return $city['timezone_id'];
				}
			}
		}

		return false;
    }


	/**
	 * Get offset in seconds
	 *
	 * @param $tz
	 * @return int
	 */
	public static function getTimezoneOffset($tz)
	{
		if (is_string($tz)) {
			$tz = new \DateTimeZone($tz);
		}

		$tz_utc = new \DateTimeZone('UTC');

		$now = new \DateTime('now', $tz_utc);

		$offset = $tz->getOffset($now);
		return $offset;
	}


	/**
	 * Get offset as a string
	 *
	 * @param $tz
	 * @return string
	 */
	public static function getTimezoneOffsetString($tz)
	{
		$offset = self::getTimezoneOffset($tz);

		if ($offset == 0) {
			return 'UTC';
		}

		$hours = $offset / 60 / 60;

		if ($hours < 0) {
			return "UTC" . $hours;
		} else {
			return "UTC+" . $hours;
		}
	}


	/**
	 * Convert unit of time into seconds (years, days, hours etc to seconds).
	 *
	 * @param int $num
	 * @param string $unit
	 * @return int
	 * @throws \InvalidArgumentException
	 */
	public static function getUnitInSeconds($num, $unit)
	{
		switch ($unit) {
			case self::UNIT_MINUTES:
				return $num * 60;
			case self::UNIT_HOURS:
				return $num * 60 * 60;
			case self::UNIT_DAYS:
				return $num * 60 * 60 * 24;
			case self::UNIT_WEEKS:
				return $num * 60 * 60 * 24 * 7;
			case self::UNIT_MONTHS:
				return $num * 60 * 60 * 24 * 7 * 30;
			case self::UNIT_YEARS:
				return $num * 60 * 60 * 24 * 365;
		}

		throw new \InvalidArgumentException("$unit is not a known unit");
	}
}
