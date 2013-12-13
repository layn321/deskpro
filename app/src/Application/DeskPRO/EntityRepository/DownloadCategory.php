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
use Application\DeskPRO\EntityRepository\Helper\CommentHelper;

use Application\DeskPRO\Entity\Person as PersonEntity;
use Application\DeskPRO\Searcher\DownloadSearch;

use Orb\Util\Arrays;
use Orb\Util\Strings;

class DownloadCategory extends AbstractCategoryRepository
{
	protected $all_cats = null;

	/**
	 * @var \Application\DeskPRO\EntityRepository\Helper\CommentHelper
	 */
	protected $_comment_helper = null;

	/**
	 * @return \Application\DeskPRO\EntityRepository\Helper\CommentHelper
	 */
	public function getCommentHelper()
	{
		if ($this->_comment_helper !== null) {
			return $this->_comment_helper;
		}

		$this->_comment_helper = new CommentHelper(
			$this->getEntityManager(),
			$this,
			$this->getEntityName(),
			$this->getClassMetadata(),
			'DeskPRO:DownlaodComment',
			'download_comments',
			'download_id'
		);

		return $this->_comment_helper;
	}

	public function getPermissionTableName()
	{
		return 'download_category2usergroup';
	}

	/**
	 * Get an array of categories
	 *
	 * @return array
	 */
	public function getCategoryOptions()
	{
		if (!$this->all_cats === null) return $this->all_cats;

		$this->all_cats = App::getDb()->fetchAllKeyed("
			SELECT id, parent_id title
			FROM download_categories
			ORDER BY display_order DESC
		", array(), 'id');

		return $this->all_cats;
	}

	public function getFullHierarchy()
	{
		if ($this->hierarchy !== null) return $this->hierarchy;

		$this->hierarchy = Arrays::intoHierarchy($this->getCategoryOptions());

		return $this->hierarchy;
	}

	public function getBySlug($slug)
	{
		$id = Strings::extractRegexMatch('#^([0-9]+)#', $slug, 1);
		if (!$id) return null;

		return $this->find($id);
	}

	public function getAllCounts(PersonEntity $person_context = null, $cache_name = 'portal')
	{
		$counts = array('0' => 0, '0_total' => 0);

		foreach ($this->getIds() as $cid) {
			$searcher = new DownloadSearch();
			$searcher->setPersonContext($person_context);
			$searcher->addTerm(DownloadSearch::TERM_CATEGORY_SPECIFIC, 'is', $cid);
			$searcher->addTerm(DownloadSearch::TERM_AGENT_LIST, 'is', 'published');

			$counts[$cid] = $searcher->getCount();
		}

		$counts = $this->getTotalCounts($counts);

		return $counts;
	}
}
