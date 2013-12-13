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

use \Doctrine\ORM\EntityRepository;
use Application\DeskPRO\Entity\Person as PersonEntity;
use Orb\Util\Arrays;

class TicketFilterSubscription extends AbstractEntityRepository
{
	public function getForAgent(PersonEntity $person)
	{
		$results = $this->getEntityManager()->createQuery("
			SELECT s
			FROM DeskPRO:TicketFilterSubscription s
			LEFT JOIN s.filter f
			WHERE s.person = ?1
		")->execute(array(1=> $person));

		$ret = array();

		foreach ($results as $s) {
			$ret[$s->filter->id] = $s;
		}

		return $ret;
	}

	/**
	 * Return an array of subscription info for all agents in $people, optionally only for $filters.
	 *
	 * Returned array structure:
	 * <code>
	 * array(
	 *     // agend id => TicketFilterSubscription[]
	 *     123 => array(
	 *         14 => TicketFilterSubscription['email_new', ...],
	 *         // filter id => TicketFilterSubscription
	 *     )
	 * )
	 * </code>
	 *
	 * @param array $people
	 * @param array $filters
	 * @return array
	 */
	public function getForAgents(array $people, array $filters = null)
	{
		$people_ids = array();
		foreach ($people as $p) {
			if (is_numeric($p)) {
				$people_ids[] = $p;
			} else {
				$people_ids[] = $p->id;
			}
		}
		$filter_ids = array();
		foreach ($filters as $f) {
			if (is_numeric($f)) {
				$filter_ids[] = $f;
			} else {
				$filter_ids[] = $f->id;
			}
		}

		$people_ids = Arrays::removeFalsey($people_ids);
		$filter_ids = Arrays::removeFalsey($filter_ids);

		if (!$people_ids) {
			return array();
		}

		$people_ids = implode(',', $people_ids);
		$filter_ids = implode(',', $filter_ids);

		if ($filter_ids) {
			$results = $this->getEntityManager()->createQuery("
				SELECT s
				FROM DeskPRO:TicketFilterSubscription s
				LEFT JOIN s.filter f
				LEFT JOIN s.person a
				WHERE s.person IN ($people_ids) AND s.filter IN ($filter_ids)
			")->execute();
		} else {
			$results = $this->getEntityManager()->createQuery("
				SELECT s
				FROM DeskPRO:TicketFilterSubscription s
				LEFT JOIN s.filter f
				LEFT JOIN s.person a
				WHERE s.person IN ($people_ids)
			")->execute();
		}

		$ret = array();

		foreach ($results as $s) {
			$agent_id = $s->person->id;
			$filter_id = $s->filter->id;

			if (!isset($ret[$agent_id])) $ret[$agent_id] = array();

			$ret[$agent_id][$filter_id] = $s;
		}

		return $ret;
	}
}
