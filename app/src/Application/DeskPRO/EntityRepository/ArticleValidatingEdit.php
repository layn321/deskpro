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

use Doctrine\ORM\EntityRepository;

class ArticleValidatingEdit extends AbstractEntityRepository
{
	public function getEditForArticle(ArticleEntity $article, PersonEntity $person = null)
	{
		try {
			if ($person) {
				$edit = $this->getEntityManager()->createQuery("
					SELECT e
					FROM DeskPRO:ArticleValidatingEdit e
					WHERE e.article = ?1 AND e.person = ?2
				")->setParameter(1, $article)
				  ->setParameter(2, $person)
				  ->getSingleResult();
			} else {
				$edit = $this->getEntityManager()->createQuery("
					SELECT e
					FROM DeskPRO:ArticleValidatingEdit e
					WHERE e.article = ?1
				")->setParameter(1, $article)
				  ->getSingleResult();
			}

			return $edit;
		} catch (\Exception $e) {
			return null;
		}
	}

	public function getValidatingEdit()
	{
		$validating_edits = $this->getEntityManager()->createQuery("
			SELECT e, a
			FROM DeskPRO:ArticleValidatingEdit e
			LEFT JOIN e.article a
		")->execute();

		return $validating_edits;
	}
}
