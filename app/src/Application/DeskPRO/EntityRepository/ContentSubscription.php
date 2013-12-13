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
use Application\DeskPRO\Entity\Article as ArticleEntity;
use Application\DeskPRO\Entity\Download as DownloadEntity;
use Application\DeskPRO\Entity\News as NewsEntity;
use Application\DeskPRO\Entity\Feedback as FeedbackEntity;

use \Doctrine\ORM\EntityRepository;

class ContentSubscription extends AbstractEntityRepository
{
	/**
	 * Get a subscription for a type of content
	 *
	 * @param $content_object An Article, Feedback, News or Download
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return \Application\DeskPRO\Entity\ContentSubscription
	 */
	public function getSubscription($content_object, PersonEntity $person)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		$qb->select('s');
		$qb->from('DeskPRO:ContentSubscription', 's');
		$qb->andWhere("s.person = ?1");

		if ($content_object instanceof ArticleEntity) {
			$qb->andWhere("s.article = ?2");
		} elseif ($content_object instanceof DownloadEntity) {
			$qb->andWhere("s.download = ?2");
		} elseif ($content_object instanceof NewsEntity) {
			$qb->andWhere("s.news = ?2");
		} elseif ($content_object instanceof FeedbackEntity) {
			$qb->andWhere("s.feedback = ?2");
		} else {
			throw new \InvalidArgumentException("\$content_object must be Article, Download, News or Feedback. Got `" . get_class($content_object) . "`");
		}

		$qb->setParameters(array(1 => $person, 2 => $content_object));

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (\Exception $e) {
			return null;
		}
	}

	public function getSubscriptionsForPerson(PersonEntity $person)
	{
		return $this->getEntityManager()->createQuery("
			SELECT s
			FROM DeskPRO:ContentSubscription s
			WHERE s.person = ?1
		")->execute(array(1 => $person));
	}
}
