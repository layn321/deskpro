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
 * @subpackage Auth
 */

namespace Application\DeskPRO\Auth\Adapter;

use Orb\Auth\Result;



/**
 * The Local adapter handles local logins using an email address or username and a password.
 */
class Local implements \Orb\Auth\Adapter\AdapterInterface
{
	/**
	 * Entity manager
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;

	protected $email = '';
	protected $password = '';

	public function __construct(\Doctrine\ORM\EntityManager $em)
	{
		$this->em = $em;
	}

	public function setCredentials($email, $password)
	{
		$this->email = $email;
		$this->password = $password;
	}

	/**
	 * Authenticate a user.
	 *
	 * @return
	 */
	public function authenticate()
	{
		$qb = $this->em->createQueryBuilder();
		$qb->select('p')
			->from('DeskPRO:Person', 'p')
			->leftJoin('p.emails', 'e')
			->where('p.is_user = 1 AND p.is_deleted = 0')
			->setMaxResults(1);

		$qb->andWhere('e.email = ?2');
		$qb->setParameter(2, $this->email);

		$person = null;

		try {
			$person = $qb->getQuery()->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {}

		if (!$person OR !$person->checkPassword($this->password)) {
			return new Result(Result::FAILURE_INVALID_CREDS);
		}

		$identity = new \Orb\Auth\Identity($person['id'], array('person' => $person));
		$result = new Result(Result::SUCCESS, $identity);

		return $result;
	}
}
