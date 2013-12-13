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

use Orb\Util\Numbers;

class Sla extends AbstractEntityRepository
{
	protected $_all_slas = null;

	/**
	 * @return \Application\DeskPRO\Entity\Sla[]
	 */
	public function getAllSlas()
	{
		if ($this->_all_slas === null) {
			$this->_all_slas = $this->getEntityManager()->createQuery('
				SELECT s
				FROM DeskPRO:Sla s INDEX BY s.id
				ORDER BY s.title
			')->execute();
		}

		return $this->_all_slas;
	}

	public function clearSlaCache()
	{
		$this->_all_slas = null;
	}

	/**
	 * @return \Application\DeskPRO\Entity\Sla[]
	 */
	public function getPersonOrgAssociableSlas()
	{
		$slas = $this->getAllSlas();
		foreach ($slas AS $k => $sla) {
			if ($sla->apply_type != 'people_orgs') {
				unset($slas[$k]);
			}
		}

		return $slas;
	}

	/**
	 * @return \Application\DeskPRO\Entity\Sla[]
	 */
	public function getAddableSlas(Entity\Ticket $ticket)
	{
		$slas = $this->getAllSlas();
		if (!$slas) {
			return array();
		}

		foreach ($slas AS $key => $sla) {
			if ($sla->apply_type != 'manual') {
				unset($slas[$key]);
			}
		}

		return $slas;
	}

	public function getSlaTitles(array $ids = null)
	{
		$output = array();
		foreach ($this->getAllSlas() AS $sla) {
			if (!is_array($ids) || in_array($sla->id, $ids)) {
				$output[$sla->id] = $sla->title;
			}
		}

		return $output;
	}

	public function hasSlas()
	{
		return count($this->getAllSlas()) > 0;
	}

	public function doesSlaApplyToPerson(Entity\Sla $sla, Entity\Person $person)
	{
		$id = $this->getEntityManager()->getConnection()->fetchColumn('
			SELECT sla_id
			FROM sla_people
			WHERE sla_id = ? AND person_id = ?
		', array($sla->id, $person->id));

		return ($id ? true : false);
	}

	public function doesSlaApplyToOrganization(Entity\Sla $sla, Entity\Organization $organization)
	{
		$id = $this->getEntityManager()->getConnection()->fetchColumn('
			SELECT sla_id
			FROM sla_organizations
			WHERE sla_id = ? AND organization_id = ?
		', array($sla->id, $organization->id));

		return ($id ? true : false);
	}
}
