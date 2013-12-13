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
 * @subpackage Log
 */

namespace Orb\Log\Filter;

use Orb\Log\LogItem;
use Orb\Log\Logger;


/**
 * This wraps the message in console outputer formatting tags depending on its
 * priority.
 */
class ConsoleOutputFormatter extends \Orb\Filter\AbstractFilter
{
	public function filter($log_item)
	{
		if (!$log_item) return null;

		$message = $log_item[LogItem::MESSAGE];

		switch ($log_item[LogItem::PRIORITY]) {
			case Logger::ERR:
			case Logger::WARN:
			case Logger::CRIT:
			case Logger::EMERG:
			case Logger::ALERT:
				$message = '<error>'.$message.'</error>';
				break;

			case Logger::NOTICE:
				$message = '<info>'.$message.'</info>';
				break;

			case Logger::DEBUG:
				$message = '<comment>'.$message.'</comment>';
				break;
		}

		$log_item[LogItem::MESSAGE_LINE] = $message;

		return $log_item;
	}
}
