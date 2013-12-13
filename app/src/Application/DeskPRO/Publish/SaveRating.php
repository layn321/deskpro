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
 */

namespace Application\DeskPRO\Publish;

use Application\DeskPRO\App;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Person;
use Application\UserBundle\Controller\Helper\ContentRating;

class SaveRating implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(Person $person)
	{
		$this->person = $person;
		$this->em = App::getOrm();
	}

	public function setPersonContext(Person $person)
	{
		$this->person = $this->person;
	}

	public function save($object_type, $object_id, $rating)
	{
		$entity_name = 'DeskPRO:' . ucfirst($object_type);
		$content_object = $this->em->find($entity_name, $object_id);

		if (!$content_object) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$perm_name = false;
		switch ($entity_name) {
			case 'DeskPRO:Article':  $perm_name = 'articles.rate'; break;
			case 'DeskPRO:Download': $perm_name = 'downloads.rate'; break;
			case 'DeskPRO:News':     $perm_name = 'news.rate'; break;
			case 'DeskPRO:Feedback': $perm_name = 'feedback.rate'; break;
		}

		if ($perm_name) {
			if (!$this->person->hasPerm($perm_name)) {
				throw new \InvalidArgumentException();
			}
		}

		if ($content_object instanceof \Application\DeskPRO\Entity\Feedback) {
			if ($content_object == 'closed') {
				throw new \InvalidArgumentException();
			}
		}

		$content_rating = new ContentRating($content_object, $this->person, App::getSession()->getVisitor());
		$content_rating->setRequest(App::getRequest());

		$this->em->beginTransaction();
		$content_rating->setRating(
			$rating,
			0
		);
		$this->em->flush();
		$this->em->commit();
	}
}