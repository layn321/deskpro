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

class Department extends AbstractCategoryRepository
{
	public function getAll()
	{
		return $this->getRootNodes();
	}


	/**
	 * Get the default ticket department for a given context (ticket, chat)
	 *
	 * @param string $context
	 * @return \Application\DeskPRO\Entity\Department
	 */
	public function getDefaultDepartment($context)
	{
		switch ($context) {
			case 'ticket':
				$opt = 'core.tickets.default_department';
				$check_field = 'is_tickets_enabled';
				break;
			case 'chat':
				$opt = 'core.chat.default_department';
				$check_field = 'is_chat_enabled';
				break;
			default:
				throw new \InvalidArgumentException("Unknown context `$context`");
		}

		$dep_id = App::getSetting($opt);
		$dep = null;
		if ($dep_id) {
			$dep = $this->find($dep_id);
		}

		if (!$dep) {
			// There should always be a correct default set, but this is
			// error handling in case
			$dep_id = App::getDb()->fetchColumn("
				SELECT d.id
				FROM departments d
				LEFT JOIN departments AS subdep ON (subdep.parent_id = d.id)
				WHERE subdep.id IS NULL AND d.$check_field = 1
				ORDER BY d.display_order ASC
				LIMIT 1
			");

			if ($dep_id) {
				$dep = $this->find($dep_id);
			}
		}

		return $dep;
	}

	public function getChildDepartments($context)
	{
		switch ($context) {
			case 'ticket':
				$check_field = 'is_tickets_enabled';
				break;
			case 'chat':
				$check_field = 'is_chat_enabled';
				break;
			default:
				throw new \InvalidArgumentException("Unknown context `$context`");
		}

		return $this->getEntityManager()->createQuery("
			SELECT d
			FROM DeskPRO:Department d
			WHERE d.parent IS NOT NULL
				AND d.$check_field = 1
		")->execute();
	}


	/**
	 * Get deps that arent linked up to a gateway
	 *
	 * @return array
	 */
	public function getUnlinkedGatewayDepartments()
	{
		return $this->_em->createQuery("
			SELECT dep
			FROM DeskPRO:Department dep
			LEFT JOIN dep.email_gateway em
			WHERE em IS NULL
			ORDER BY dep.display_order ASC
		")->execute();
	}


	/**
	 * @param $department
	 * @param $email_gateway
	 */
	public function linkToGateway($department, $email_gateway = null)
	{
		$em = $this->_em;

		$old_email_gateway = $department->email_gateway;

		$queries = array();

		// Unlink old
		if ($old_email_gateway) {
			$old_email_gateway->department = null;
			$em->persist($old_email_gateway);

			$queries[] = "UPDATE email_gateways SET department_id = NULL WHERE department_id = {$department->getId()}";

			$department->email_gateway = null;
		}

		if ($email_gateway && $email_gateway->department) {
			$old_dep = $email_gateway->department;
			$old_dep->email_gateway = null;
			$em->persist($old_dep);

			$queries[] = "UPDATE departments SET email_gateway_id = NULL WHERE id = {$old_dep->getId()}";

			$email_gateway->department = null;
		}

		// Link new
		if ($email_gateway) {
			$department->email_gateway = $email_gateway;
			$email_gateway->department = $department;
			$em->persist($email_gateway);

			$queries[] = "UPDATE departments SET email_gateway_id = {$email_gateway->getId()} WHERE id = {$department->getId()}";
			$queries[] = "UPDATE email_gateways SET department_id = {$department->getId()} WHERE id = {$email_gateway->getId()}";
		}

		$em->persist($department);
		$em->flush();

		foreach ($queries as $q) {
			$em->getConnection()->executeUpdate($q);
		}

		// validate links
		if ($email_gateway) {
			$em->getConnection()->executeUpdate("
				UPDATE departments
				SET email_gateway_id = NULL
				WHERE id != ? AND email_gateway_id = ?
			", array($department->getId(), $email_gateway->getId()));
			$em->getConnection()->executeUpdate("
				UPDATE email_gateways
				SET department_id = NULL
				WHERE department_id = ? AND id != ?
			", array($department->getId(), $email_gateway->getId()));
		} else {
			$em->getConnection()->executeUpdate("
				UPDATE email_gateways
				SET department_id = NULL
				WHERE department_id = ?
			", array($department->getId()));
		}
	}
}
