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
 * @subpackage Dpql
 */

namespace Application\DeskPRO\Dpql\Placeholder;

use Application\DeskPRO\App;
use Application\DeskPRO\Dpql\Statement\Display;
use Application\DeskPRO\Dpql;
use Application\DeskPRO\Dpql\Statement\Part\Prepared;
use Application\DeskPRO\Dpql\Statement\Part\AbstractPart;

/**
 * Place holder for the previous week based on the current person's time zone.
 */
class LastWeek extends AbstractDateRange
{
	/**
	 * Gets the date range components (printable, start, end).
	 *
	 * @return string[int]
	 */
	protected function _getDateRange()
	{
		$person = App::getCurrentPerson();
		$tz = new \DateTimeZone($person->getTimezone());
		$date = new \DateTime('now', $tz);

		// find start of this week
		$currentDayOfWeek = $date->format('N');
		$startAdjust = $currentDayOfWeek - $person->getStartOfWeek();

		if ($startAdjust) {
			if ($startAdjust > 0) {
				$date->modify('-' . $startAdjust . ' days');
			} else {
				$date->modify('-' . (7 + $startAdjust) . ' days');
			}
		}

		// move to beginning of previous week
		$date->modify('-7 days');

		$start = $date->format('Y-m-d');

		$date->modify('+6 days'); // 7 days will take us to the next start of the week
		$end = $date->format('Y-m-d');

		return array("$start to $end", "$start 00:00:00", "$end 23:59:59");
	}
}