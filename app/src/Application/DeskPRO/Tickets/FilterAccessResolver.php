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

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Doctrine\ORM\EntityManager;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\TicketFilter;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * This just looks at a filter and agents to determine who is able to use a filter,
 * and who is actually using it (based on prefs)
 */
class FilterAccessResolver
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $hidden_prefs;

	/**
	 * @var array
	 */
	protected $team_members;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->db = $em->getConnection();

		#------------------------------
		# Fetch agent team membership data
		#------------------------------

		$this->team_members = $this->em->getRepository('DeskPRO:AgentTeam')->getSortedMemberIds();


		#------------------------------
		# Fetch data about who has hidden filters
		#------------------------------

		$hidden_filters_data = $this->db->fetchAll("
			SELECT person_id, name
			FROM people_prefs
			WHERE name LIKE 'agent.ui.filter-visibility.%' AND (value_str = '0')
		");

		$hidden_prefs = array();
		foreach ($hidden_filters_data as $d) {
			$filter_id = str_replace('agent.ui.filter-visibility.', '', $d['name']);

			if (!isset($hidden_filters[$filter_id])) {
				$hidden_filters[$filter_id] = array();
			}

			$hidden_prefs[$filter_id][$d['person_id']] = true;
		}

		$this->hidden_prefs = $hidden_prefs;
	}


	/**
	 * Can a person use a particular filter/
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param \Application\DeskPRO\Entity\TicketFilter $filter
	 * @return bool
	 */
	public function canUse(Person $person, TicketFilter $filter)
	{
		if ($filter->is_global) {
			return true;
		}

		if ($filter->agent_team) {
			if (!isset($this->team_members[$filter->agent_team->id])) {
				return false;
			}
			return in_array($person->id, $this->team_members[$filter->agent_team->id]);
		}

		if ($filter->person && $filter->person->id == $person->id) {
			return true;
		}

		return false;
	}


	/**
	 * Does a person ignore a filter?
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param \Application\DeskPRO\Entity\TicketFilter $filter
	 * @return bool
	 */
	public function isIgnored(Person $person, TicketFilter $filter)
	{
		return isset($this->hidden_prefs[$filter->id][$person->id]);
	}


	/**
	 * Get all users who use a filter
	 *
	 * @param \Application\DeskPRO\Entity\TicketFilter $filter
	 * @return array
	 */
	public function getUsers(TicketFilter $filter, array $available_agents = null)
	{
		if ($available_agents === null) {
			$available_agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
		}

		$agents = array();

		foreach ($available_agents as $agent) {
			if (!$this->isIgnored($agent, $filter) and $this->canUse($agent, $filter)) {
				$agents[] = $agent;
			}
		}

		return $agents;
	}


	/**
	 * Get all users who use ignore filter
	 *
	 * @param \Application\DeskPRO\Entity\TicketFilter $filter
	 * @return array
	 */
	public function getIgnoreUsers(TicketFilter $filter, array $available_agents = null)
	{
		if ($available_agents === null) {
			$available_agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
		}

		$agents = array();

		foreach ($available_agents as $agent) {
			if ($this->isIgnored($agent, $filter) and $this->canUse($agent, $filter)) {
				$agents[] = $agent;
			}
		}

		return $agents;
	}
}
