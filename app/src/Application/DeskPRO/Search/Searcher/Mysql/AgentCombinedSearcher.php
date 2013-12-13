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
use Application\DeskPRO\Search\Searcher\ContentSearcherInterface;

use Application\DeskPRO\Search\SearcherResult\ResultSet;
use Application\DeskPRO\Search\SearcherResult\Result;

/**
 * The combined searcher searches everything: tickets, chats, articles, news, downloads, feedback
 */
class AgentCombinedSearcher
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

	public function query($query_text, $per_page = 25, $page = 1, array $limit_types = null, $top = true)
	{
		$limit_types = \Orb\Util\Arrays::removeFalsey((array)$limit_types);

		// Incase they are label matches, try encoding those as labels
		$words = explode(' ', $query_text);
		foreach ($words as $w) {
			$query_text .= " " . MysqlAdapter::encodeLabel(strtolower($w));
		}

		if ($limit_types) {
			$limit_types = "'" . implode('\',\'', $limit_types) . "'";
			$where = "
				object_type IN ($limit_types)
				AND MATCH (content) AGAINST (? IN BOOLEAN MODE)
			";
		} else {
			$where = "
				MATCH (content) AGAINST (? IN BOOLEAN MODE)
			";
		}


		$total = null;
		if (!$top) {
			$count_query = "
				SELECT COUNT(*)
				FROM content_search
				WHERE $where
			";
			$total = App::getDbRead()->fetchColumn($count_query, array($query_text));
		}

		$start = ($page - 1) * $per_page;
		$select_query = "
			SELECT object_type, object_id, MATCH (content) AGAINST (?) AS _relevancy
			FROM content_search
			WHERE $where
			ORDER BY _relevancy DESC
			LIMIT $start, $per_page
		";

		$results_raw  = App::getDbRead()->fetchAll($select_query, array($query_text, $query_text));
		$results      = array();

		foreach ($results_raw as $result_raw) {
			$result = Result::newFromArray(array(
				'id' => $result_raw['object_id'],
				'content_type' => $result_raw['object_type'],
			));

			$results[] = $result;
		}

		if ($total === null) {
			$total = count($result);
		}

		$result_set = new ResultSet($total, $results);

		return $result_set;
	}
}
