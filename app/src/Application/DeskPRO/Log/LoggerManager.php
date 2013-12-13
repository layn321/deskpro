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

namespace Application\DeskPRO\Log;

class LoggerManager
{
	/**
	 * @var \Orb\Log\Logger[]
	 */
	protected $loggers;

	/**
	 * @var string
	 */
	protected $default_log_dir;


	/**
	 * @param string $default_log_dir
	 */
	public function __construct($default_log_dir)
	{
		$this->default_log_dir = $default_log_dir;
	}


	/**
	 * @param $id
	 * @param \Orb\Log\Logger $logger
	 */
	public function set($id, \Orb\Log\Logger $logger)
	{
		$this->loggers[$id] = $logger;
	}


	/**
	 * Gets a registered logger or creates a default logger that writes to the log dir
	 *
	 * @param string $id
	 */
	public function get($id)
	{
		if (isset($this->loggers[$id])) {
			return $this->loggers[$id];
		}

		$l = new \Orb\Log\Logger();

		$id_name = preg_replace('#[^a-z0-9_\-\.]#', '_', strtolower($id));
		$wr = new \Orb\Log\Writer\Stream($this->default_log_dir . DIRECTORY_SEPARATOR . $id_name . '.log');

		$l->addWriter($wr);

		$this->loggers[$id] = $l;
		return $this->loggers[$id];
	}


	/**
	 * @param string $id
	 * @return bool
	 */
	public function has($id)
	{
		return isset($this->loggers[$id]);
	}


	/**
	 * @param string $id
	 */
	public function remove($id)
	{
		unset($this->loggers[$id]);
	}
}