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
use Application\DeskPRO\Entity\Organization as OrganizationEntity;

use Orb\Util\Numbers;

class Organization extends AbstractEntityRepository
{
	protected $_organization_names = null;

	public function findOneByName($name)
	{
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('o')
            ->from('DeskPRO:Organization', 'o')
            ->where('o.name = :name')
            ->setParameter('name', $name);

            $query = $qb->getQuery();
            return $query->getOneOrNullResult();
	}

	/**
	 * @return array
	 */
	public function getOrganizationNames($for_ids = null)
	{
		if ($this->_organization_names == null) {
			$db = App::getDb();
			$this->_organization_names = $db->fetchAllKeyValue("
				SELECT id, name
				FROM organizations
				ORDER BY name ASC
			");
        }

        if ($for_ids === null) {
		    return $this->_organization_names;
        }

        $ret = array();
        foreach ((array)$for_ids as $id) {
			if (isset($this->_organization_names[$id])) {
            	$ret[$id] = $this->_organization_names[$id];
			}
        }

        return $ret;
	}


	public function getOrganizationsFromIds(array $ids)
	{
		// Only valid ID's please :)
		// Do this because Doctrine doesnt have proper IN()
		// escaping until 2.1
		$ids = array_filter($ids, function ($val) {
			if (Numbers::isInteger($val)) {
				return true;
			}
			return false;
		});

		if (!$ids) return array();

		$orgs = $this->getEntityManager()->createQuery("
			SELECT o
			FROM DeskPRO:Organization o INDEX BY o.id
			WHERE o.id IN(" . implode(',', $ids) . ")
			ORDER BY o.id ASC
		")->execute();

		return $orgs;
	}

	/**
	 * Get a count of how many orgs there are
	 *
	 * @return int
	 */
	public function getCount()
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM organizations
		");
	}

	/**
	 * Count how many people there are in an organization
	 *
	 * @param \Application\DeskPRO\Entity\Organization $org
	 * @return int
	 */
	public function countMembersFor(OrganizationEntity $org)
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM people
			WHERE organization_id = {$org['id']}
		");
	}

	/**
	 * Gets the list of organization managers
	 *
	 * @param \Application\DeskPRO\Entity\Organization $org
	 *
	 * @return \Application\DeskPRO\Entity\Person[]
	 */
	public function getManagers(OrganizationEntity $org)
	{
		return $this->getEntityManager()->createQuery('
			SELECT p
			FROM DeskPRO:Person p
			WHERE p.organization = ?1 AND p.organization_manager = 1
			ORDER BY p.last_name, p.first_name
		')->execute(array(1 => $org));
	}


	/**
	 * Fetch an organization by its name.
	 *
	 * @param string $name
	 * @return \Application\DeskPRO\Entity\Organization
	 */
	public function getByName($name)
	{
		$name = trim($name);

		return $this->getEntityManager()->createQuery("
			SELECT o
			FROM DeskPRO:Organization o
			WHERE
				o.name = ?1
		")->setParameter(1, $name)->setMaxResults(1)->getOneOrNullResult();
	}


	/**
	 * @param $q
	 * @param null $limit
	 * @return mixed
	 */
	public function search($q, $limit = null)
	{
		$q = '%' . str_replace(array('%', '_'), array('\\\\%', '\\\\_'), $q) . '%';

		return $this->getEntityManager()->createQuery("
			SELECT o
			FROM DeskPRO:Organization o
			WHERE o.name LIKE ?1
			ORDER BY o.name ASC
		")->setParameters(array(1=> $q))->setMaxResults($limit)->execute();
	}
}
