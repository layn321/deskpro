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
 * @category People
 */

namespace Application\DeskPRO\People\ActivityLogger;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonActivity;

use Application\DeskPRO\People\ActivityLogger\ActionType\ActionTypeAbstract;

use Orb\Util\Arrays;
use Orb\Util\Util;
use Orb\Util\Strings;

class ActivityLogger
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * Save any action details
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param string $action_type
	 * @param array $details
	 * @return \Application\DeskPRO\Entity\PersonActivity|array
	 */
	public function saveActionDetails(Person $person, $action_type, array $details)
	{
		$activity = new PersonActivity();
		$activity->person = $person;
		$activity['action_type'] = $action_type;
		$activity['details'] = $details;

		$this->em->getConnection()->beginTransaction();
		try {
			$this->em->persist($activity);
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $activity;
	}


	/**
	 * Save an action object
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param \Application\DeskPRO\People\ActivityLogger\ActionTypeAbstract $action
	 * @return \Application\DeskPRO\Entity\PersonActivity|array
	 */
	public function saveAction(ActionTypeAbstract $action)
	{
		$action_type = Util::getBaseClassname($action);
		$action_type = Strings::camelCaseToUnderscore($action_type);

		return $this->saveActionDetails($action->getPersonContext(), $action_type, $action->getDetails());
	}
}
