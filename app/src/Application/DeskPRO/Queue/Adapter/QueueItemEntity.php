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
 * @subpackage Queue
 */

namespace Application\DeskPRO\Queue\Adapter;

use Orb\Util\Strings;
use Orb\Util\Util;
use Application\DeskPRO\Entity\QueueItem;


use \Zend\Queue\Queue;
use \Zend\Queue\Exception as QueueException;
use \Zend\Queue\Message;

/**
 * Adapter to use the QueueItemEntity
 */
class QueueItemEntity extends \Zend\Queue\Adapter\AbstractAdapter
{
	/**
	 * Plain database connection for raw queries
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	public function __construct($options, Queue $queue = null)
	{
		$this->em = $options['em'];
		unset($options['em']);

		$this->db = $this->em->getConnection();

		parent::__construct($options, $queue);

		$this->_queues = null;
	}


	/**
	 * @return \Application\DeskPRO\DBAL\Connection
	 */
	public function getDb()
	{
		return $this->db;
	}


	/**
	 * Check to see if a queue exists.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function isExists($name)
	{
		if ($this->_queues === null) {
			$this->getQueues();
		}

		return in_array($name, $this->_queues);
	}



	/**
	 * Get an array of queues
	 * @return array
	 */
	public function getQueues()
	{
		if ($this->_queues === null) {
			$this->_queues = $this->db->fetchAllCol("SELECT DISTINCT(groupname) FROM queue_items");
		}

		return $this->_queues;
	}


	/**
	 * Create a new queue. Always works because we dont create queue per-se; to create
	 * a queue you just insert a job with the groupname name you want.
	 *
	 * @param string $name
	 * @param int $timeout
	 * @return bool
	 */
	public function create($name, $timeout=null)
	{
		$this->_queues[] = $name;

		return true;
	}



	/**
	 * Delete a queue and all jobs in it
	 */
	public function delete($name)
	{
		$this->db->delete('queue_items', array('groupname' => $name));

		return true;
	}



	/**
	 * Get how many jobs belong to a queue
	 *
	 * @param Queue\Queue $queue
	 * @return int
	 */
	public function count(Queue $queue=null)
	{
		return $this->db->fetchColumn("SELECT COUNT(*) FROM queue_items WHERE groupname = ?", array($queue->getName()));
	}



	/**
	 * Put a job onto the queue.
	 *
	 * @param string $message
	 * @param Queue\Queue $queue
	 * @return classname
	 */
	public function send($message, Queue $queue=null)
	{
		if ($queue === null) {
			$queue = $this->_queue;
		}

		if (is_string($message)) {
			$message = array('message' => $message);
		}

		$item = array(
			'groupname'   => $queue->getName(),
			'created_at'  => date('Y-m-d H:i:s'),
			'priority'    => 0,
			'delay_until' => null,
			'ttr'         => 60,
			'is_ready'    => 1,
			'is_dataonly' => 0,
			'is_ignored'  => 0,
			'reserved_at' => null,
			'timeout_at'  => null,
		);

		foreach (array('is_ready', 'is_ignored', 'priority', 'delay_until', 'ttr') as $k) {
			if (isset($message[$k])) {
				$item[$k] = $message[$k];
				unset($message[$k]);
			}
		}

		$item['data'] = serialize($message);
		$this->db->insert('queue_items', $item);

		$message['qi_id'] = $this->db->lastInsertId();

		$options = array(
			'queue' => $queue,
			'data'  => $message,
		);
		$classname = $queue->getMessageClass();

		$classname = $queue->getMessageClass();
		return new $classname($options);
	}



	/**
	 * Reserve one or more jobs from the queue.
	 *
	 * @param int $maxMessages
	 * @param int $timeout
	 * @param Queue\Queue $queue
	 * @return classname
	 */
	public function receive($maxMessages=null, $timeout=null, Queue $queue=null)
	{
		if ($maxMessages === null) {
			$maxMessages = 1;
		}

		if ($timeout === null) {
			$timeout = self::RECEIVE_TIMEOUT_DEFAULT;
		}
		if ($queue === null) {
			$queue = $this->_queue;
		}

		$msgs = array();
		if ($maxMessages > 0 ) {

			$timenow = date('Y-m-d H:i:s');
			$results = $this->db->fetchAll("
				SELECT *
				FROM queue_items
				WHERE
					is_dataonly = 0
					AND is_ignored = false
					AND is_ready = true
					AND (reserved_at IS NULL OR reserved_at OR timeout_at < ?)
					AND (delay_until IS NULL OR delay_until < ?)
				ORDER BY priority DESC, id ASC
				LIMIT $maxMessages
			", array($timenow, $timenow));

			foreach ($results as $item) {
				$data = @unserialize($item['data']);
				if (!$data) $data = array();

				$msgs[] = array_merge($data, array('qi_id' => $item['id']));

				$this->db->update('queue_items', array(
					'reserved_at' => $timenow,
					'timeout_at'  => date('Y-m-d H:i:s', time() + $item['ttr'])
				), array('id' => $item['id']));
			}
		}

		$options = array(
			'queue'        => $queue,
			'data'         => $msgs,
			'messageClass' => $queue->getMessageClass(),
		);
		$classname = $queue->getMessageSetClass();
		return new $classname($options);
	}


	/**
	 * Delete a message from the queue
	 * @param Message $message
	 */
	public function deleteMessage(Message $message)
	{
		$this->db->delete('queue_items', array('id' => $message->qi_id));
		return true;
	}

	public function getCapabilities()
	{
		return array(
			'create'        => true,
			'delete'        => true,
			'send'          => true,
			'receive'       => true,
			'deleteMessage' => true,
			'getQueues'     => true,
			'count'         => true,
			'isExists'      => true,
		);
	}
}
