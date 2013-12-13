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
use Application\DeskPRO\Entity;

class TicketFilter extends AbstractEntityRepository
{
	public function getAllForActiveAgents()
	{
		$online_agents = App::getEntityRepository('DeskPRO:Person')->getActiveAgents(true);
		if (!$online_agents) return array();

		return $this->getAllForAgents($online_agents);
	}

	public function getAllRecords()
	{
		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
		")->execute();

		return $filters;
	}

	public function getAll()
	{
		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
			LEFT JOIN q.person p
			WHERE p IS NULL OR (p.is_agent = true AND p.is_deleted = 0)
			ORDER BY q.id ASC
		")->execute();

		return $filters;
	}

	public function getAllForAgents($agents)
	{
		$agent_ids = array();
		foreach ($agents as $a) {
			if (is_object($a)) {
				$agent_ids[] = $a['id'];
			} else {
				$agent_ids[] = $a;
			}
		}

		if (!$agent_ids) {
			return array();
		}

		$teams = App::getEntityRepository('DeskPRO:AgentTeam')->getAllTeamIdsForAgents($agents);
		if (!$teams) $teams = array(0);

		$agent_ids = implode(',', $agent_ids);
		$teams = implode(',', $teams);

		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
			WHERE
				q.is_global = true
				OR q.person IN ($agent_ids)
				OR q.agent_team IN ($teams)
		")->execute();

		return $filters;
	}

	public function getPersonalFilters($agent)
	{
		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
			WHERE q.person = ?0
			ORDER BY q.title ASC
		")->execute(array($agent));

		return $filters;
	}

	public function getSharedFilters(Entity\Person $agent)
	{
		$agent->loadHelper('Agent');
		$teams = $agent->getTeams();

		if ($teams) {
			$teams = array_values($teams);
			$filters = $this->getEntityManager()->createQuery("
				SELECT q
				FROM DeskPRO:TicketFilter q INDEX BY q.id
				WHERE (q.person IS NULL OR q.person != ?0) AND (q.is_global = true OR q.agent_team IN (?1)) AND q.sys_name IS NULL
				ORDER BY q.title ASC
			")->execute(array($agent, $teams));
		} else {
			$filters = $this->getEntityManager()->createQuery("
				SELECT q
				FROM DeskPRO:TicketFilter q INDEX BY q.id
				WHERE (q.person IS NULL OR q.person != ?0) AND q.is_global = true AND q.sys_name IS NULL
				ORDER BY q.title ASC
			")->execute(array($agent));
		}

		return $filters;
	}

	/**
	 * Gets an array of all global filters.
	 *
	 * This is mainly used in admin for listing.
	 *
	 * @return array
	 */
	public function getAllGlobalFilters()
	{
		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
			WHERE q.is_global = true AND q.sys_name IS NULL
			ORDER BY q.title ASC
		")->execute();

		return $filters;
	}


	/**
	 * Gets an array of all team filters, grouped by agent team id.
	 *
	 * This is mainly used in admin for listing.
	 *
	 * @return array
	 */
	public function getAllTeamFilters()
	{
		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
			LEFT JOIN q.agent_team at
			WHERE q.agent_team IS NOT NULL
			ORDER BY at.name ASC, q.title ASC
		")->execute();

		$grouped_filters = array();

		foreach ($filters as $filter) {
			$team_id = $filter->agent_team['id'];

			if (!isset($grouped_filters[$team_id])) {
				$grouped_filters[$team_id] = array('team' => $filter->agent_team, 'filters' => array());
			}

			$grouped_filters[$team_id]['filters'][] = $filter;
		}

		return $grouped_filters;
	}


	/**
	 * Gets an array of all agent filters, grouped by agent id.
	 *
	 * This is mainly used in admin for listing.
	 *
	 * @return array
	 */
	public function getAllAgentFilters()
	{
		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
			LEFT JOIN q.person p
			WHERE q.agent_team IS NULL AND q.is_global = false
			ORDER BY p.name ASC, q.title ASC
		")->execute();

		$grouped_filters = array();

		foreach ($filters as $filter) {
			$agent_id = $filter->person['id'];

			if (!isset($grouped_filters[$agent_id])) {
				$grouped_filters[$agent_id] = array('person' => $filter->person, 'filters' => array());
			}

			$grouped_filters[$agent_id]['filters'][] = $filter;
		}

		return $grouped_filters;
	}


	/**
	 *
	 * @param  $type
	 * @return void
	 */
	public function getFiltersForType($type)
	{
		switch ($type) {
			case 'global':
				$filters = $this->getEntityManager()->createQuery("
					SELECT q
					FROM DeskPRO:TicketFilter q INDEX BY q.id
					WHERE q.is_global = true
					ORDER BY q.title ASC
				")->execute();
				break;

			case 'team':
				$filters = $this->getEntityManager()->createQuery("
					SELECT q
					FROM DeskPRO:TicketFilter q INDEX BY q.id
					WHERE q.is_global = true
					ORDER BY q.title ASC
				")->execute();
				break;
		}
	}

	public function getSystemFilters($person_id)
	{
		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
			WHERE q.sys_name IS NOT NULL AND (q.person = ?1 OR q.is_global = true)
			ORDER BY q.title ASC
		")->setParameter(1, $person_id)->execute();

		return $filters;
	}

	/**
	 * Find all ticket filters (system and custom) that a person can see.
	 *
	 * @param mixed $person_id
	 * @return array
	 */
	public function getFiltersForPerson($person)
	{
		/** @var $person \Application\DeskPRO\Entity\Person */

		if (!($person instanceof \Application\DeskPRO\Entity\Person)) {
			$person = $this->getEntityManager()->find('DeskPRO:Person', $person);
		}

		$person_id = $person->getId();

		$team_ids = array();
		if ($person->is_agent) {
			$person->loadHelper('Agent');
			$team_ids = $person->getHelper('Agent')->getTeamIds();
		}

		if ($team_ids) {
			$filters = $this->getEntityManager()->createQuery("
				SELECT q
				FROM DeskPRO:TicketFilter q INDEX BY q.id
				WHERE q.person = ?1 OR q.is_global = true OR q.agent_team IN (?2)
				ORDER BY q.title ASC
			")->setParameter(1, $person_id)->setParameter(2, $team_ids)->execute();
		} else {
			$filters = $this->getEntityManager()->createQuery("
				SELECT q
				FROM DeskPRO:TicketFilter q INDEX BY q.id
				WHERE q.person = ?1 OR q.is_global = true
				ORDER BY q.title ASC
			")->setParameter(1, $person_id)->execute();
		}

		return $filters;
	}

	/**
	 * @param $person_id
	 * @return
	 */
	public function getCustomFiltersForPerson($person_id)
	{
		$filters = $this->getEntityManager()->createQuery("
			SELECT q
			FROM DeskPRO:TicketFilter q INDEX BY q.id
			WHERE q.sys_name IS NULL AND (q.person = ?1 OR q.is_global = true)
			ORDER BY q.title ASC
		")->setParameter(1, $person_id)->execute();

		return $filters;
	}

	public function getTicketFilterFromVar($var)
	{
		$ticket_filter_id = null;

		if (is_int($var) OR ctype_digit($var)) {
			$ticket_filter_id = (int)$var;
		} elseif (\is_object($var)) {
			if ($var instanceof Entity\TicketFilter) {
				return $var;
			}
		} elseif (isset($var['ticket_filter'])) {
			return $var['ticket_filter'];
		}

		if ($ticket_filter_id) {
			return $this->find($ticket_filter_id);
		}

		return null;
	}
}
