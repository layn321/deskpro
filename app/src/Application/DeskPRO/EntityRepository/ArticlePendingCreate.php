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
use Doctrine\ORM\EntityRepository;

use Orb\Util\Arrays;

class ArticlePendingCreate extends AbstractEntityRepository
{
	public function getPendingArticles()
	{
		$pending_articles = $this->getEntityManager()->createQuery("
			SELECT a, t, p
			FROM DeskPRO:ArticlePendingCreate a
			LEFT JOIN a.ticket t
			LEFT JOIN a.person p
			ORDER BY a.date_created DESC
		")->execute();

		return $pending_articles;
	}


	public function getByIds(array $ids, $keep_order = false)
	{
		$ids = Arrays::castToType($ids, 'int');
		$ids = Arrays::removeFalsey($ids);

		if (!$ids) {
			return array();
		}
		$ids = implode(',', $ids);

		return $this->getEntityManager()->createQuery("
			SELECT a
			FROM DeskPRO:ArticlePendingCreate a INDEX BY a.id
			WHERE a.id IN ($ids)
		")->execute();
	}
}
