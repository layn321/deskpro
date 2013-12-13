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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;
use Doctrine\ORM\EntityRepository;

use Orb\Util\Arrays;

class SearchLog extends AbstractEntityRepository
{
	public function getRatedSearchesFor($object_type, $object_id, $structure = 'all')
	{
		$search_ids_to_rating = APp::getDb()->fetchAllKeyValue("
			SELECT searchlog_id, rating
			FROM ratings
			WHERE object_type = ? AND object_id = ? AND searchlog_id IS NOT NULL
		", array($object_type, $object_id));

		$logs = $this->getByIds(array_keys($search_ids_to_rating));

		if (!$logs) {
			return array();
		}

		if ($structure == 'all') {
			return $logs;
		}

		if ($structure == 'grouped') {
			$ret = array('helpful' => array(), 'unhelpful' => array());
			foreach ($logs as $l) {
				if ($search_ids_to_rating[$l['id']] >= 1) {
					$ret['helpful'] = $l;
				} else {
					$ret['unhelpful'] = $l;
				}
			}

			return $ret;

		} elseif ($structure == 'counted') {

			$ret = array('helpful' => array(), 'unhelpful' => array());
			foreach ($logs as $l) {

				if ($search_ids_to_rating[$l['id']] >= 1) {
					if (!isset($ret['helpful'][$l['query']])) {
						$ret['helpful'][$l['query']] = 0;
					}
					$ret['helpful'][$l['query']]++;
				} else {
					if (!isset($ret['unhelpful'][$l['query']])) {
						$ret['unhelpful'][$l['query']] = 0;
					}
					$ret['unhelpful'][$l['query']]++;
				}
			}

			asort($ret['helpful'], \SORT_NUMERIC);
			asort($ret['unhelpful'], \SORT_NUMERIC);

			return $ret;
		}

		return null;
	}

	public function popularSearchTerms($limit = 100)
	{
		return App::getDb()->fetchAll("
			SELECT COUNT(*) AS num_searches, num_results, query
			FROM searchlog
			GROUP BY query
			ORDER BY num_searches DESC, num_results ASC
			LIMIT $limit
		");
	}

	public function popularSearchTermsLowHits($limit = 100, $max_hits = 0)
	{
		return App::getDb()->fetchAll("
			SELECT COUNT(*) AS num_searches, num_results, query
			FROM searchlog
			WHERE num_results <= $max_hits
			GROUP BY query
			ORDER BY num_results ASC, num_searches DESC
			LIMIT $limit
		");
	}

	public function recentSearchTerms($limit = 100)
	{
		return App::getDb()->fetchAll("
			SELECT COUNT(*) AS num_searches, num_results, query
			FROM searchlog
			GROUP BY query
			ORDER BY id DESC
			LIMIT $limit
		");
	}

	public function getByIds(array $ids, $keep_order = false)
	{
		$ids = Arrays::castToType($ids, 'int');
		if (!$ids) {
			return array();
		}

		return $this->getEntityManager()->createQuery("
			SELECT l
			FROM DeskPRO:SearchLog l
			WHERE l.id IN (" . implode(',', $ids) . ")
			ORDER BY l.id DESC
		")->execute();
	}
}
