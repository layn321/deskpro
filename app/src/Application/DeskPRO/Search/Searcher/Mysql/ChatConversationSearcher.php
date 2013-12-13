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
 * @category Search
 */

namespace Application\DeskPRO\Search\Searcher\Mysql;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\People\PersonContextInterface;

use Application\DeskPRO\Search\Adapter\MysqlAdapter;
use Application\DeskPRO\Search\Searcher\TicketSearcherInterface;

use Application\DeskPRO\Search\SearcherResult\ResultSet;
use Application\DeskPRO\Search\SearcherResult\Result;

/**
 * The content searcher searches: tickets
 */
class ChatConversationSearcher implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function setPersonContext(Person $person)
	{
		$this->person = $person;
	}

	public function query($query, $per_page = 25, $page = 1, $top = false)
	{
		$where = "
			object_type = 'chat_conversation'
			AND MATCH (content) AGAINST (?)
		";

		$count_query = "
			SELECT COUNT(*)
			FROM content_search
			WHERE $where
		";

		$start = ($page - 1) * $per_page;
		$select_query = "
			SELECT object_type, object_id, MATCH (content) AGAINST (?) AS _rel
			FROM content_search
			WHERE $where
			ORDER BY _rel DESC
			LIMIT $start, $per_page
		";

		if ($top) {
			$total = null;
		} else {
			$total = App::getDbRead()->fetchColumn($count_query, array($query));
		}
		$results_raw  = App::getDbRead()->fetchAll($select_query, array($query, $query));
		$results      = array();

		foreach ($results_raw as $result_raw) {
			$result = Result::newFromArray(array(
				'id' => $result_raw['object_id'],
				'content_type' => $result_raw['object_type'],
			));

			$results[] = $result;
		}

		if ($total === null) {
			$total = count($results);
		}

		$result_set = new ResultSet($total, $results);

		return $result_set;
	}

	public function similar(Ticket $ticket)
	{
		throw new \BadMethodCallException('Similar ticket matching not supported with the Mysql search adapter');
	}
}
