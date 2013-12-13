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
use Doctrine\ORM\EntityRepository;
use Application\DeskPRO\EmailGateway\Reader\EzcReader;

class EmailGateway extends AbstractEntityRepository
{
	protected $_gateway_names = null;

	public function getGatewayNames(array $for_ids = null)
	{
		if ($this->_gateway_names === null) {
			$this->_gateway_names = array();

			$recs = App::getDb()->fetchAll("
				SELECT id, title
				FROM email_gateways
				ORDER BY title DESC
			");
			foreach ($recs as $rec) {
				$this->_gateway_names[$rec['id']] = $rec['title'];
			}
		}

		if ($for_ids) {
			$names = array();
			foreach ($for_ids as $id) {
				if (isset($this->_gateway_names[$id])) {
					$names[$id] = $this->_gateway_names[$id];
				}
			}

			return $names;
		}

		return $this->_gateway_names;
	}

	public function getGatewayFromAddress($address)
	{
		$address = (array)$address;

		foreach ($address as $addr) {
			try {
				$gateway = $this->getEntityManager()->createQuery("
					SELECT g
					FROM DeskPRO:EmailGateway g
					WHERE g.address = ?1
				")->setParameter(1, $addr)->setMaxResults(1)->getSingleResult();

				if ($gateway) {
					return $gateway;
				}
			} catch (\Doctrine\ORM\NoResultException $e) {}
		}


		return null;
	}

	/**
	 * Get gateways that arent linked up to a department
	 *
	 * @return array
	 */
	public function getUnlinkedGateways()
	{
		return $this->_em->createQuery("
			SELECT g
			FROM DeskPRO:EmailGateway g
			LEFT JOIN g.department dep
			WHERE dep IS NULL AND g.is_enabled = true
			ORDER BY g.title ASC
		")->execute();
	}


	/**
	 * Get all gateway accounts that are enabled
	 *
	 * @return array
	 */
	public function getAllEnabled()
	{
		return $this->findBy(array('is_enabled' => true));
	}
}
