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

class TwitterAccount extends AbstractEntityRepository
{
	protected $_first = false;
	protected $_all;

	public function getAll()
	{
		if ($this->_all === null) {
			$this->_all = $this->getEntityManager()->createQuery("
				SELECT a, u
				FROM DeskPRO:TwitterAccount a INDEX BY a.id
				INNER JOIN a.user u
				ORDER BY u.name
			")->execute();
		}

		return $this->_all;
	}

	public function getAllForPerson(\Application\DeskPRO\Entity\Person $person = null)
	{
		if (!$person) {
			$person = App::getCurrentPerson();
		}

		$output = $this->getAll();
		$account_ids = $person->getTwitterAccountIds();
		foreach ($output AS $key => $value) {
			if (!in_array($key, $account_ids)) {
				unset($output[$key]);
			}
		}

		return $output;
	}

	public function getFirst()
	{
		if ($this->_first === false) {
			$this->_first = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:TwitterAccount a
				INNER JOIN a.user u
			")->setMaxResults(1)->getOneOrNullResult();
		}

		return $this->_first;
	}
}
