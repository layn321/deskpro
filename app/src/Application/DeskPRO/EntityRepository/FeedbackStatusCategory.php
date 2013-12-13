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

use Doctrine\ORM\EntityRepository;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\FeedbackStatusCategory as FeedbackStatusCategoryEntity;

class FeedbackStatusCategory extends AbstractEntityRepository
{
	protected $active_cats = null;
	protected $closed_cats = null;

	public function reload()
	{
		$this->active_cats = $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:FeedbackStatusCategory c INDEX BY c.id
			WHERE c.status_type = ?1
			ORDER BY c.display_order ASC
		")->setParameter(1, 'active')->execute();

		$this->closed_cats = $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:FeedbackStatusCategory c INDEX BY c.id
			WHERE c.status_type = ?1
			ORDER BY c.display_order ASC
		")->setParameter(1, 'closed')->execute();
	}

	public function getActiveCategories()
	{
		if ($this->active_cats === null) $this->reload();
		return $this->active_cats;
	}

	public function getClosedCategories()
	{
		if ($this->closed_cats === null) $this->reload();
		return $this->closed_cats;
	}

	public function getNames(array $for_ids = null)
	{
		$categories = $this->findAll();

		$ret = array();
		foreach ($categories as $category) {
			if ($for_ids === null || in_array($category->id, $for_ids)) {
				$ret[$category->id] = $category->title;
			}
		}

		return $ret;
	}
}
