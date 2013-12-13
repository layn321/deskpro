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
 * @subpackage Organizations
 */
namespace Application\DeskPRO\Organizations;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Organization;
use Orb\Util\Arrays;

class OrgResultsDisplay
{
	/**
	 * @var \Application\DeskPRO\Entity\Organization[]
	 */
	protected $orgs;

	/**
	 * @var array
	 */
	protected $org_ids;

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
	protected $orgs_count;

	/**
	 * @var array
	 */
	protected $all_labels;

	/**
	 * @var array
	 */
	protected $org_member_counts;

	/**
	 * @param \Application\DeskPRO\Entity\Organization[] $orgs
	 */
	public function __construct(array $orgs)
	{
		$this->people = $orgs;
		$this->orgs_count = count($orgs);
		$this->org_ids = Arrays::flattenToIndex($this->people, 'id');


		$this->em = App::getOrm();
		$this->db = $this->em->getConnection();
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->orgs_count;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Organization[]
	 */
	public function getOrganizations()
	{
		return $this->orgs;
	}


	/**
	 * @return array
	 */
	public function getAllLabels()
	{
		if ($this->all_labels !== null) return $this->all_labels;

		if (!$this->orgs_count) {
			$this->all_labels = array();
			return $this->all_labels;
		}

		$org_ids = implode(',', $this->org_ids);

		$this->all_labels = $this->db->fetchAllGrouped("
			SELECT organization_id, label
			FROM labels_organizations
			WHERE organization_id IN ($org_ids)
		", array(), 'organization_id', null, 'label');

		return $this->all_labels;
	}


	/**
	 * Get an array of labels applied to an org
	 *
	 * @param \Application\DeskPRO\Entity\Organization $org
	 * @return array
	 */
	public function getOrgLabels(Organization $org)
	{
		$this->getAllLabels();
		return empty($this->all_labels[$org->id]) ? array() : $this->all_labels[$org->id];
	}


	/**
	 * Check if an org has labels
	 *
	 * @param \Application\DeskPRO\Entity\Organization $org
	 * @return bool
	 */
	public function hasOrgLabels(Organization $org)
	{
		$this->getAllLabels();
		return !empty($this->all_labels[$org->id]);
	}


	/**
	 * @return array
	 */
	public function getAllOrgMemberCounts()
	{
		if ($this->org_member_counts !== null) return $this->org_member_counts;

		$org_ids = implode(',', $this->org_ids);

		$this->org_member_counts = $this->db->fetchAllKeyValue("
			SELECT organization_id, COUNT(*)
			FROM people
			WHERE organization_id IN($org_ids) AND is_disabled = 0 AND is_deleted = 0
			GROUP BY organization_id
		");

		return $this->org_member_counts;
	}


	/**
	 * Get the number of tickets submitted by a user.
	 *
	 * @param \Application\DeskPRO\Entity\Organization $org
	 * @return int
	 */
	public function getOrgMemberCount(Organization $org)
	{
		$this->getAllOrgMemberCounts();
		return isset($this->org_member_counts[$org->id]) ? $this->org_member_counts[$org->id] : 0;
	}
}
