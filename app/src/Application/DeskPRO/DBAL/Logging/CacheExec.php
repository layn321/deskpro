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

use Application\DeskPRO\CacheInvalidator\QueryListener;

class CacheExec extends \Symfony\Bridge\Doctrine\Logger\DbalLogger
{
	/**
	 * @var \Application\DeskPRO\CacheInvalidator\QueryListener
	 */
	protected $query_listener;

	/**
	 * @var array
	 */
	protected $last_query;

	public function __construct(QueryListener $query_listener)
	{
		$this->query_listener = $query_listener;
	}

	public function startQuery($sql, array $params = null, array $types = null)
	{
		if ($params === null) $params = array();
		$this->last_query = array($sql, $params);
	}

	public function stopQuery()
	{
		if (!$this->last_query) return;

		$this->query_listener->handleQuery($this->last_query[0], $this->last_query[1]);

		$this->last_query = null;
	}
}
