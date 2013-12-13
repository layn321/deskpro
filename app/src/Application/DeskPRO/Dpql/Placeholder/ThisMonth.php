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
 * Placeholder for the current month (first to last day), based on the current person's time zone.
 */
class ThisMonth extends AbstractDateRange
{
	/**
	 * Gets the date range components (printable, start, end).
	 *
	 * @return string[int]
	 */
	protected function _getDateRange()
	{
		$tz = new \DateTimeZone(App::getCurrentPerson()->getTimezone());
		$date = new \DateTime('now', $tz);

		$start = $date->format('Y-m');

		$endDate = new \DateTime("$start-01", $tz);
		$endDate->modify('+1 month')->modify('-1 day');

		$endDateValue = $endDate->format('Y-m-d');

		return array("$start-01 to $endDateValue", "$start-01 00:00:00", "$endDateValue 23:59:59");
	}
}