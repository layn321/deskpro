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
use Application\DeskPRO\Entity\Person as PersonEntity;

class LoginLog extends AbstractEntityRepository
{
	/**
	 * Gets the last successful login
	 *
	 * @return \Application\DeskPRO\Entity\LoginLog
	 */
	public function getLast(PersonEntity $person)
	{
		// The last login will be the one before the last,
		// since the absolute last will be the current login

		$last_login = $this->_em->createQuery("
			SELECT l
			FROM DeskPRO:LoginLog l
			WHERE l.person = ?0 AND l.is_success = true
			ORDER BY l.id DESC
		")->setMaxResults(1)->setFirstResult(1)->setParameter(0, $person)->getOneOrNullResult();

		return $last_login;
	}
}
