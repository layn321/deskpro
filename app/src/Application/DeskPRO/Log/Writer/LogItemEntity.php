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
 * @subpackage Log
 */

namespace Application\DeskPRO\Log\Writer;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Util\Strings;
use Orb\Util\Arrays;

class LogItemEntity extends \Orb\Log\Writer\AbstractWriter
{
	public function _write(\Orb\Log\LogItem $log_item)
	{
		try {

			$message = $log_item->getMessage();
			$message_len = strlen($message);

			$data = $log_item->getExtra() ? serialize($log_item->getExtra()) : null;
			$data_len = $data ? strlen($data) : 0;

			$max_size = App::getDb()->getMaxPacketSize();
			if (($message_len + $data_len) * 2 >= $max_size) {
				$message = substr($message, 0, ($max_size - 50) /2);
				$message_len = strlen($message);

				if (($message_len + $data_len) * 2 >= $max_size) {
					$data = null;
					$data_len = 0;
				}
			}

			App::getDb()->insert('log_items', array(
				'log_name'         => $log_item->getLogName(),
				'session_name'     => $log_item->getSessionName(),
				'message'          => $message,
				'priority'         => $log_item->getPriority(),
				'priority_name'    => $log_item->getPriorityName(),
				'date_created'     => $log_item->getDatetime()->format('Y-m-d H:i:s'),
				'flag'             => $log_item->getFlag() ?: null,
				'data'             => $data,
			));
		} catch (\Exception $e) {
			KernelErrorHandler::logException($e, false);
		}
	}
}
