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

use Orb\Util\Arrays;

use Application\DeskPRO\App;
use \Doctrine\ORM\EntityRepository;

class Usergroup extends AbstractEntityRepository
{
	protected $_usergroup_names = null;
	protected $_agent_usergroup_names = null;


	/**
	 * @return \Application\DeskPRO\Entity\Usergroup[]
	 */
	public function getUserUsergroups()
	{
		return $this->_em->createQuery("
			SELECT ug
			FROM DeskPRO:Usergroup ug INDEX BY ug.id
			WHERE ug.is_agent_group = 0
			ORDER BY ug.title ASC
		")->execute();
	}


	/**
	 * @return \Application\DeskPRO\Entity\Usergroup[]
	 */
	public function getAgentUsergroups()
	{
		return $this->_em->createQuery("
			SELECT ug
			FROM DeskPRO:Usergroup ug INDEX BY ug.id
			WHERE ug.is_agent_group = true
			ORDER BY ug.title ASC
		")->execute();
	}


	/**
	 * Get an array of id=>name for usergroups.
	 *
	 * @return array
	 */
	public function getUsergroupNames($for_ids = null)
	{
		if ($this->_usergroup_names === null) {
			$db = $this->_em->getConnection();
			$this->_usergroup_names = $db->fetchAllKeyValue("
				SELECT id, title
				FROM usergroups
				WHERE is_agent_group = 0 AND sys_name IS NULL
				ORDER BY title ASC
			");
        }

        if ($for_ids === null) {
            return $this->_usergroup_names;
        }

        $ret = array();
		if (in_array(1, $for_ids)) {
			$ret[1] = App::getTranslator()->phrase('agent.general.everyone');
		}
		if (in_array(2, $for_ids)) {
			$ret[2] = App::getTranslator()->phrase('agent.general.registered');
		}
        foreach ($for_ids as $id) {
			if (isset($this->_usergroup_names[$id])) {
				$ret[$id] = $this->_usergroup_names[$id];
			}
        }

        return $ret;
	}



	/**
	 * Get an array of id=>name for agent usergroups.
	 *
	 * @return array
	 */
	public function getAgentUsergroupNames()
	{
		if ($this->_agent_usergroup_names !== null) return $this->_agent_usergroup_names;
		$db = App::getDb();
		$this->_agent_usergroup_names = $db->fetchAllKeyValue("
			SELECT id, title
			FROM usergroups
			WHERE is_agent_group = 0 AND sys_name IS NULL
			ORDER BY title ASC
		");

		return $this->_agent_usergroup_names;
	}

	public function getByIds(array $ids, $keep_order = false)
	{
		if (!$ids) return array();

		return $this->getEntityManager()->createQuery("
			SELECT u
			FROM DeskPRO:Usergroup u INDEX BY u.id
			WHERE u.id IN (" . implode(',', $ids) . ")
			ORDER BY u.id DESC
		")->execute();
	}


	/**
	 * get the counts for all usergroups
	 *
	 * @return array
	 */
	public function getCountsForAll()
	{
		$output = App::getDb()->fetchAllKeyValue("
			SELECT usergroup_id, COUNT(*)
			FROM person2usergroups
			GROUP BY usergroup_id
		");
		$output = array_map('intval', $output);

		$results = App::getDb()->fetchAll("
			SELECT o2u.usergroup_id, (SELECT COUNT(*) FROM people WHERE people.organization_id = o2u.organization_id) AS total
			FROM organization2usergroups AS o2u
		");
		foreach ($results AS $result) {
			if (!$result['total']) {
				continue;
			}
			if (isset($output[$result['usergroup_id']])) {
				$output[$result['usergroup_id']] += $result['total'];
			} else {
				$output[$result['usergroup_id']] = $result['total'];
			}
		}

		return $output;
	}


	/**
	 * Count the number of members in usergroups ($ids)
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getCountsFor(array $ids)
	{
		if (!$ids) return array();

		$ids_comma = implode(',', $ids);


		$output = App::getDb()->fetchAllKeyValue("
			SELECT usergroup_id, COUNT(*)
			FROM person2usergroups
			WHERE usergroup_id IN ($ids_comma)
			GROUP BY usergroup_id
		");
		$output = Arrays::castToType($output, 'int', 'int');

		// Org counts
		// Need to count all members of the org that are not part of the usergroup themselves
		$results = App::getDb()->fetchAll("
			SELECT o2u.usergroup_id, COUNT(*) AS total
			FROM people
			LEFT JOIN organization2usergroups AS o2u ON (o2u.organization_id = people.organization_id)
			LEFT JOIN person2usergroups AS p2u ON (p2u.person_id = people.id AND p2u.usergroup_id = o2u.usergroup_id)
			WHERE o2u.usergroup_id IN ($ids_comma) AND p2u.person_id IS NULL
			GROUP BY o2u.usergroup_id
		");

		if ($results) {
			foreach ($results AS $result) {
				if (!$result['total']) {
					continue;
				}
				if (isset($output[$result['usergroup_id']])) {
					$output[$result['usergroup_id']] += $result['total'];
				} else {
					$output[$result['usergroup_id']] = $result['total'];
				}
			}
			$output = Arrays::castToType($output, 'int', 'int');
		}

		return $output;
	}


	/**
	 * Count the number of organization members in usergroups ($ids)
	 *
	 * @param array $ids
	 * @return int
	 */
	public function getOrganizationCountsFor(array $ids)
	{
		if (!$ids) return array();

		$ids_comma = implode(',', $ids);

		return App::getDb()->fetchAllKeyValue("
			SELECT usergroup_id, COUNT(*)
			FROM organization2usergroups
			WHERE usergroup_id IN ($ids_comma)
			GROUP BY usergroup_id
		");
	}


	/**
	 * Get all agents of all teams, and sort them into an array keyed
	 * by team: array('teamid' => array('agentid', 'agentid'))
	 *
	 * @return array
	 */
	public function getSortedAgentIds()
	{
		return App::getDb()->fetchAllGrouped("
			SELECT person2usergroups.usergroup_id, person2usergroups.person_id
			FROM person2usergroups
			LEFT JOIN usergroups ON usergroups.id = person2usergroups.usergroup_id
			WHERE usergroups.is_agent_group = 1
		", array(), 'usergroup_id', null, 'person_id');
	}
}
