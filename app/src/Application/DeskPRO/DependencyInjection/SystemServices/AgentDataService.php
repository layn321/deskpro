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
 * @subpackage
 */

namespace Application\DeskPRO\DependencyInjection\SystemServices;

use Doctrine\ORM\EntityManager;
use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Application\DeskPRO\Entity\Person;
use Orb\Util\Arrays;

class AgentDataService
{
	protected $has_init = false;

	/**
	 * @var \Application\DeskPRO\Entity\Person[]
	 */
	public $agents = array();

	/**
	 * @var array
	 */
	public $online_agent_ids;

	/**
	 * @var int[]
	 */
	public $ids = array();

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var int
	 */
	protected $agent_timeout = 20;

	public static function create(DeskproContainer $container, array $options = null)
	{
		$em = $container->getEm();
		$o = new static($em);
		return $o;
	}

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->db = $em->getConnection();
	}

	protected function preload()
	{
		if ($this->has_init) {
			return;
		}
		$this->has_init = true;

		$this->agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
		foreach ($this->agents as $a) {
			$this->ids[] = $a->getId();
		}
	}


	/**
	 * @return \Application\DeskPRO\Entity\Person[]
	 */
	public function getAgents()
	{
		$this->preload();
		return $this->agents;
	}


	/**
	 * @param array $for_ids
	 */
	public function getNames(array $for_ids = null)
	{
		$ret = array();

		if ($for_ids) {
			foreach ($this->getAgents() as $agent) {
				if ($for_ids === null || in_array($agent->getId(), $for_ids)) {
					$ret[$agent->getId()] = $agent->getDisplayName();
				}
			}
		}

		return $ret;
	}


	/**
	 * @return int[]
	 */
	public function getIds()
	{
		$this->preload();
		return $this->ids;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function get($id)
	{
		$this->preload();

		if (isset($this->agents[$id])) {
			return $this->agents[$id];
		}

		return null;
	}


	/**
	 * @return bool
	 */
	public function has($id)
	{
		$this->preload();

		return isset($this->agents[$id]);
	}


	/**
	 * Get an array of agents by ids
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getByIds($ids)
	{
		$this->preload();

		$agents = array();

		foreach ($ids as $id) {
			$id = (int)$id;
			if (isset($this->agents[$id])) {
				$agents[$id] = $this->agents[$id];
			}
		}

		return $agents;
	}


	/**
	 * Returns an array of valid agent IDs in $ids. Optionally
	 * specify $invalid and all invalid IDs will be put into it.
	 *
	 * @param array $ids
	 * @param null $invalid
	 * @return array
	 */
	public function confirmAgentIds(array $ids, &$invalid_ids = null)
	{
		$this->preload();

		$valid_ids = array();
		if (!isset($invalid_ids) || !$invalid_ids) {
			$invalid_ids = array();
		}

		foreach ($ids as $id) {
			if (isset($this->agents[$id])) {
				$valid_ids[] = $id;
			} else {
				$invalid_ids[] = $id;
			}
		}

		return $valid_ids;
	}


	/**
	 * @param string $email
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getByEmail($email)
	{
		foreach ($this->getAgents() as $agent) {
			if ($agent->hasEmailAddress($email)) {
				return $agent;
			}
		}

		return null;
	}


	/**
	 * Get an array of agents who are online now (have active sessions).
	 *
	 * @return int[]
	 */
	public function getOnlineAgentIds()
	{
		if ($this->online_agent_ids !== null) {
			return $this->online_agent_ids;
		}
		$cutoff = date('Y-m-d H:i:s', time() - $this->agent_timeout);

		$this->online_agent_ids = $this->db->fetchAllKeyValue("
			SELECT DISTINCT s.person_id
			FROM sessions s
			INNER JOIN people p ON (s.person_id = p.id)
			WHERE p.is_agent = 1 AND p.is_deleted = 0 AND s.date_last > ?
		", array($cutoff), 0, 0);

		return $this->online_agent_ids;
	}


	/**
	 * @return array
	 */
	public function getOnlineAgents()
	{
		$this->getOnlineAgentIds();

		$agents = array();
		foreach ($this->online_agent_ids as $id) {
			$agents[$id] = $this->get($id);
		}

		return $agents;
	}


	/**
	 * Check if an agent is online
	 *
	 * @param int|Person $id_or_agent
	 * @return bool
	 */
	public function isAgentOnline($id_or_agent)
	{
		$this->getOnlineAgentIds();
		$id = is_object($id_or_agent) ? $id_or_agent->getId() : $id_or_agent;

		return isset($this->online_agent_ids[$id]);
	}


	/**
	 * Count how many agents are currently online
	 *
	 * @return int
	 */
	public function countOnlineAgents()
	{
		return count($this->online_agent_ids);
	}
}