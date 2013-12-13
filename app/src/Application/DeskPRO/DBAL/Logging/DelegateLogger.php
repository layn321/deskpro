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
 * @subpackage DBAL
 */

namespace Application\DeskPRO\DBAL\Logging;

/**
 * Allows you to hook up multiple loggers and they all get called
 * for the logger requests.
 */
class DelegateLogger implements \Doctrine\DBAL\Logging\SQLLogger
{
	/**
	 * @var \Doctrine\DBAL\Logging\SQLLogger[]
	 */
	protected $registered_loggers = array();

	public function addLogger(\Doctrine\DBAL\Logging\SQLLogger $logger, $identifier)
	{
		$this->registered_loggers[$identifier] = $logger;
	}

	public function removeLogger($identifier)
	{
		unset($this->registered_loggers[$identifier]);
	}

	public function getLoggers()
	{
		return $this->registered_loggers;
	}
	
	public function getLogger($identifier)
	{
		if (isset($this->registered_loggers[$identifier])) {
			return $this->registered_loggers[$identifier];
		}
		else {
			return null;
		}
	}
	
	public function startQuery($sql, array $params = null, array $types = null)
	{
		foreach ($this->registered_loggers as $logger) {
			$logger->startQuery($sql, $params, $types);
		}
	}

	public function stopQuery()
	{
		foreach ($this->registered_loggers as $logger) {
			$logger->stopQuery();
		}
	}
}
