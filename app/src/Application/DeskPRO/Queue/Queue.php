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
 * @subpackage Queue
 */

namespace Application\DeskPRO\Queue;

use Orb\Util\Strings;
use Orb\Util\Util;
use Application\DeskPRO\Entity\QueueItem;

use \Zend\Queue\Queue as ZendQueue;
use \Zend\Queue\Exception as QueueException;
use \Zend\Queue\Message as ZendMessage;

/**
 * Some changes to Queue to seamlessly handle messages that point to QueueItem datas.
 * With the database-driven adapter, nothing is done. But with others when data
 * exceeds a certain amount, the jobs contain a pointer to a QueueItem.
 */
class Queue extends ZendQueue
{
	protected $_messageClass = 'Application\DeskPRO\Queue\Message';

	public function send($message)
	{
		$max_size = $this->getOption('queueitem_threshold');
		if (!$max_size) {
			$max_size = 500;
		}

		#------------------------------
		# If the message is not too big, we can just store it in the queue store
		#------------------------------

		if ((is_string($message) && strlen($message) < $max_size) OR $this->getAdapter() instanceof \Application\DeskPRO\Queue\Adapter\QueueItemEntity) {
			return $this->getAdapter()->send($message);
		}


		#------------------------------
		# Otherwise we'll go and create a QueueItem, and change the message
		# to point to it.
		# - The Message item will correctly decode these and load the real data later
		#------------------------------

		// Note: important NOT to use the Em for creating QueueItems!
		// Sometimes the queue is used onFlush event, which means
		// persisting isn't so simple

		$db = $this->getOption('em')->getConnection();

		$item = array();
		$item['created_at'] = date('Y-m-d H:i:s');
		$item['is_dataonly'] = true;
		$item['data'] = $message;

		try {
			$db->insert('queue_items', $item);
			$item['id'] = $db->lastInsertId();

			$message = '<QueueItem:' . $item['id'] . '>';

			$success = $this->getAdapter()->send($message);
			$e = null;
		} catch (\Exception $e) {
			$success = false;
		}

		if (!$success) {
			try {
				$db->delete('queue_items', array('id' => $item['id']));
			} catch (\Exception $e) {}

			if ($e) {
				throw $e;
			}
		}

		return $success;
	}


	public function deleteMessage(ZendMessage $message)
	{
		if ($this->getAdapter() instanceof \Application\DeskPRO\Queue\Adapter\QueueItemEntity) {
			return $this->getAdapter()->deleteMessage($message);
		}

		if (isset($message->qi_id)) {
			$db = \Application\DeskPRO\App::getDb();
			$db->beginTransaction();
			try {
				$db = $this->getOption('em')->getConnection();
				$db->delete('queue_items', array('id' => $message->qi_id));
				$this->getAdapter()->deleteMessage($message);
				$db->commit();
			} catch (\Exception $e) {
				$db->rollback();
				throw $e;
			}
		}

		return true;
	}
}
