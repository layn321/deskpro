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
use \Orb\Log\LogItem;



/**
 * This formats the 'line' value of an item with other properties. This gives
 * a single, suitable value that can be written to a flat file for example.
 */
class SimpleLineFormatter extends \Orb\Filter\AbstractFilter
{
	const DEFAULT_FORMAT = '[%datetime% %priority_name%] %message%';
	const DEFAULT_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var string
     */
	protected $_format;

	protected $_time_format;

	public function __construct($format = self::DEFAULT_FORMAT, $time_format = self::DEFAULT_TIME_FORMAT)
	{
		$this->_format = $format;
		$this->_time_format = $time_format;
	}

	public function filter($log_item)
	{
		if (!$log_item) return null;

		$message = $log_item[LogItem::MESSAGE];
		$message_line = $this->_format;

		foreach ($log_item as $k => $v) {
			if ($v instanceof \DateTime) {
				$v = $v->format($this->_time_format);
			}

			if (is_scalar($v)) {
				$message_line = str_replace("%$k%", $v, $message_line);
			}
		}

		$log_item[LogItem::MESSAGE_LINE] = $message_line;

		return $log_item;
	}
}
