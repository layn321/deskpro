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
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage
 */

namespace Application\ReportBundle\OverviewStat;

class TimeTitles
{
	const LAST_TIME_MARKER   = 1893456000;

	public static $time_phrases = array(
		300        => '< 5 minutes',
		900        => '5 - 15 minutes',
		1800       => '15 - 30 minutes',
		3600       => '30 - 60 minutes',
		7200       => '1 - 2 hours',
		10800      => '2 - 3 hours',
		14400      => '3 - 4 hours',
		21600      => '4 - 6 hours',
		43200      => '6 - 12 hours',
		86400      => '12 - 24 hours',
		172800     => '1 - 2 days',
		259200     => '2 - 3 days',
		345600     => '3 - 4 days',
		432000     => '4 - 5 days',
		518400     => '5 - 6 days',
		604800     => '6 - 7 days',
		1209600    => '1 - 2 weeks',
		1814400    => '2 - 3 weeks',
		2419200    => '3 - 4 weeks',
		4838400    => '1 - 2 months',
		7257600    => '2 - 3 months',
		9676800    => '3 - 4 months',
		12096000   => '4 - 5 months',
		14515200   => '5 - 6 months',
		self::LAST_TIME_MARKER => '> 6 months'
	);

	public static function getValuesArray($values)
	{
		$new_values = array();

		foreach ($values as $group => $v) {
			$new_values[$group] = self::selectTimeGroup($v);
		}

		return $new_values;
	}

	public static function selectTimeGroup($time)
	{
		$time_phrases = self::$time_phrases;

		foreach ($time_phrases as $min => $phrase) {
			if ($time <= $min) {
				return $phrase;
			}
		}

		return 'bad time';
	}

	public static function makeTimeFieldSelect($field)
	{
		$times = array_keys(TimeTitles::$time_phrases);

		$sql = "CASE ";

		$parts = array();
		foreach ($times as $t) {
			$parts[] = " WHEN $field <= $t THEN $t ";
		}

		$sql .= implode('', $parts) . " ELSE ".self::LAST_TIME_MARKER." END AS time_group";

		return $sql;
	}
}