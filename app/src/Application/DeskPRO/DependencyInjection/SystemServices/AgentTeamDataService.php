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

class AgentTeamDataService
{
	protected $has_init = false;

	/**
	 * @var \Application\DeskPRO\Entity\AgentTeam[]
	 */
	public $agent_teams = array();

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

		$this->agent_teams = $this->em->getRepository('DeskPRO:AgentTeam')->getTeams();
		foreach ($this->agent_teams as $a) {
			$this->ids[] = $a->getId();
		}
	}


	/**
	 * @return \Application\DeskPRO\Entity\AgentTeam[]
	 */
	public function getTeams()
	{
		$this->preload();
		return $this->agent_teams;
	}


	/**
	 * @param array $for_ids
	 */
	public function getNames(array $for_ids = null)
	{
		$ret = array();

		foreach ($this->getTeams() as $a) {
			if ($for_ids === null || in_array($a->getId(), $for_ids)) {
				$ret[$a->getId()] = $a->getName();
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

		if (isset($this->agent_teams[$id])) {
			return $this->agent_teams[$id];
		}

		return null;
	}


	public function __call($name, $args)
	{
		$repos = $this->em->getRepository('DeskPRO:AgentTeam');
		return call_user_func_array(array($repos, $name), $args);
	}
}