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
*/

namespace Application\DeskPRO\Service\Phirehose;

/**
 * Concrete Twitter API User Stream consuming class.
 *
 */
class UserStream extends \UserstreamPhirehose
{
	const URL_BASE         = 'https://userstream.twitter.com/1.1/';

	/**
	 * @var array
	 */
	protected $account;

	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $connection;

	/**
	 * @var \Closure|null
	 */
	protected $write_callback;

	/**
	 * @var \Closure
	 */
	protected $callback;

	/**
	 * @var array
	 */
	protected $log = array();

	/**
	 * Suppress Phirehose @error_log output.
	 *
	 * @param string $message
	 * @return void
	 */
	protected function log($message)
	{
		$this->log[] = $message;
	}

	/**
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * @param \Doctrine\DBAL\Connection
	 * @return void
	 */
	public function setConnection(\Doctrine\DBAL\Connection $connection = null)
	{
		$this->connection = $connection;
	}

	/**
	 * @param \Closure|null $callback
	 */
	public function setWriteCallback(\Closure $callback = null)
	{
		$this->write_callback = $callback;
	}

	/**
	 * @return \Closure|null
	 */
	public function getWriteCallback()
	{
		return $this->write_callback;
	}

	/**
	 * @return array
	 */
	public function getAccount()
	{
		return $this->account;
	}

	/**
	 * @param array $account
	 * @return void
	 */
	public function setAccount(array $account)
	{
		$this->account = $account;
	}

	/**
	 * @return \Closure|null
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * @param \Closure|null $callback
	 */
	public function setCallback(\Closure $callback = null)
	{
		$this->callback = $callback;
	}

	/**
	 * Process raw streaming data.
	 *
	 * @param string $status
	 * @return void
	 */
	public function enqueueStatus($status)
	{
		try {
			if ($this->callback) {
				$callback = $this->callback;
				$status = $callback($status, $this);
			}

			// skip "ping -> pong"
			if (null === $status || !strlen(trim($status))) {
				return false;
			}

			$status = trim($status);

			// decode json
			$status = json_decode($status);
			$event = 'unknown';

			// check if status is a tweet
			if (isset($status->text)) {
				$event = 'status';
			}

			// check direct message
			if (isset($status->direct_message)) {
				$event = 'message';
			}

			// check event
			if (isset($status->event)) {
				$event = 'event';
			}

			// check friend list
			if (isset($status->friends)) {
				$event = 'friends';
			}

			if (isset($status->delete)) {
				$event = 'delete';
			}

			$data = array(
				'account_id' => $this->account['id'],
				'event' => $event,
				'data' => serialize($status),
				'date_created' => gmdate('Y-m-d H:i:s')
			);

			if ($this->write_callback) {
				$callback = $this->write_callback;
				$callback($data);
			} else if ($this->connection) {
				$this->connection->insert('twitter_stream', $data);
			} else {
				throw new \Exception("No connection or write callback - can't process");
			}
		} catch (\Exception $e) {
			\DeskPRO\Kernel\KernelErrorHandler::handleException($e, false);
		}
	}
}
