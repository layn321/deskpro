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

use Application\DeskPRO\Entity;
use \Doctrine\ORM\EntityRepository;
use Orb\Util\Util;

class Visitor extends AbstractEntityRepository
{
	/**
	 * @return Visitor
	 */
	public function getVisitorFromCode($vis_code)
	{
		if (!strpos($vis_code, '-')) {
			return null;
		}

		list ($visitor_id, $auth) = explode('-', $vis_code, 2);

		$visitor = $this->find($visitor_id);
		if (!$visitor OR !$visitor->checkVisitorCode($vis_code)) {
			return null;
		}

		return $visitor;
	}



	/**
	 * @return Visitor
	 */
	public function getVisitorForPerson($person)
	{
		return $this->getEntityManager()->createQuery("
			SELECT v
			FROM DeskPRO:Visitor v
			WHERE v.person = ?1
			ORDER BY v.id DESC
		")->setMaxResults(1)
		  ->setParameter(1, $person)
		  ->getOneOrNullResult();
	}


	/**
	 * @param string $user_token
	 */
	public function getVisitorFromUserToken($user_token)
	{
		$datecut = new \DateTime('@' . (time() - 600));
		return $this->getEntityManager()->createQuery("
			SELECT v
			FROM DeskPRO:Visitor v
			WHERE v.date_last > ?0 AND v.user_token = ?1
		")->setParameters(array(
			$datecut,
			$user_token
		))->setMaxResults(1)->getOneOrNullResult();
	}
}
