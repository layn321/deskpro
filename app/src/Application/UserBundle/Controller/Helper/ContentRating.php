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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Controller\Helper;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Visitor;
use Application\DeskPRO\Entity\ContentAbstract;
use Application\DeskPRO\Entity\SearchLog;
use Application\DeskPRO\Entity\Rating;

use Symfony\Component\HttpFoundation\Request;

use Orb\Util\Arrays;
use Orb\Util\Numbers;

class ContentRating
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\Entity\ContentAbstract
	 */
	protected $content_object;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor;

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	/**
	 * @var \Application\DeskPRO\HttpFoundation\Session
	 */
	protected $session;

	/**
	 * Null when we dont know, false when there is none,
	 * Rating when there is.
	 *
	 * @var \Application\DeskPRO\Entity\Rating
	 */
	protected $rating = null;

	/**
	 * @param \Application\DeskPRO\Entity\ContentAbstract $content_object
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param \Application\DeskPRO\Entity\Visitor $visitor
	 */
	public function __construct(ContentAbstract $content_object, Person $person, Visitor $visitor = null)
	{
		$this->person = $person;
		$this->visitor = $visitor;
		$this->content_object = $content_object;

		$this->em = App::getOrm();
	}


	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
		$this->session = $request->getSession();
	}


	/**
	 * Get this users existing rating
	 *
	 * @return \Application\DeskPRO\Entity\Rating
	 */
	public function getRating()
	{
		if ($this->rating !== null) {
			if ($this->rating) return null;
			else return null;
		}

		$em = App::getOrm();

		$res = null;
		if ($this->person) {
			if ($this->visitor) {
				$res = $em->createQuery("
					SELECT r
					FROM DeskPRO:Rating r
					WHERE
						r.object_type = ?1 AND r.object_id = ?2
						AND (r.visitor = ?3 OR r.person = ?4)
				")->setParameter(1, $this->content_object->getContentType())
				  ->setParameter(2, $this->content_object->getId())
				  ->setParameter(3, $this->visitor)
				  ->setParameter(4, $this->person)
				  ->execute();
			} else {
				$res = $em->createQuery("
					SELECT r
					FROM DeskPRO:Rating r
					WHERE
						r.object_type = ?1 AND r.object_id = ?2
						AND (r.person = ?3)
				")->setParameter(1, $this->content_object->getContentType())
				  ->setParameter(2, $this->content_object->getId())
				  ->setParameter(3,$this->person)
				  ->execute();
			}
		} elseif ($this->visitor) {
			$res = $em->createQuery("
				SELECT r
				FROM DeskPRO:Rating r
				WHERE
					r.object_type = ?1 AND r.object_id = ?2
					AND r.visitor = ?3
			")->setParameter(1, $this->content_object->getContentType())
			  ->setParameter(2, $this->content_object->getId())
			  ->setParameter(3, $this->visitor)
			  ->execute();
		}

		if ($res AND count($res)) {
			$this->rating = $res[0];
			return $this->rating;
		} else {
			$this->rating = false;
			return null;
		}
	}


	/**
	 * Get a search log ID that sholud be recorded if the user were to vote on the next page
	 *
	 * @return int
	 */
	public function getSearchLogId()
	{
		if ($this->request AND $this->session AND $this->session->has('last_searchlog_id')) {
			$ref = $this->request->server->get('HTTP_REFERER');
			$search_url = App::getRouter()->generate('user', array(), true) . 'search';
			$search_url = preg_replace('#^https?://#', '', $search_url);

			if ($ref && preg_match('#' . preg_quote($search_url, '#') . '(\?.*?)?$#', $ref)) {
				$searchlog = App::findEntity('DeskPRO:SearchLog', $this->session->has('last_searchlog_id'));
				if ($searchlog->visitor && $this->visitor && $searchlog->visitor == $this->visitor) {
					return $searchlog['id'];
				}
			}
		}

		return 0;
	}


	/**
	 * Set the user rating on this
	 *
	 * @param $user_rating
	 * @return \Application\DeskPRO\Entity\Rating|
	 */
	public function setRating($user_rating, $search_log_id = 0)
	{
		if (!$user_rating) {
			return null;
		}

		$rating = $this->getRating();
		if ($rating) {
			// Remove the rating to undo its effect on the counters
			$this->content_object->removeRating($rating);
		} else {
			$rating = Rating::create($user_rating, true);
		}

		if ($search_log_id) {
			$searchlog = App::findEntity('DeskPRO:SearchLog', $search_log_id);
			if ($searchlog->visitor && $this->visitor && $searchlog->visitor['id'] == $this->visitor['id']) {
				$rating->searchlog = $searchlog;
			}
		} else {
			if ($this->session->get('from_search')) {
				$searchlog = App::findEntity('DeskPRO:SearchLog', $this->session->get('last_searchlog_id'));
				$rating->searchlog = $searchlog;

				$this->session->remove('from_search');
				$this->session->save();
			}
		}

		$this->content_object->addRating($rating);

		$this->em->beginTransaction();
		$this->em->persist($rating);
		$this->em->persist($this->content_object);
		$this->em->flush();
		$this->em->commit();

		$this->rating = $rating;

		return $rating;
	}
}
