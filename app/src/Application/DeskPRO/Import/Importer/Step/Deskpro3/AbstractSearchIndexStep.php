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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

use Orb\Util\Strings;
use Application\DeskPRO\App;
use Orb\Data\ContentTypes;

abstract class AbstractSearchIndexStep extends AbstractDeskpro3Step
{
	const PER_PAGE = 50;

	abstract public function getContentType();
	abstract public function getTable();
	abstract public function getEntity();

	public function countPages()
	{
		$count = $this->getDb()->fetchColumn("SELECT COUNT(*) FROM {$this->getTable()}");
		if (!$count) {
			return 1;
		}

		$pages = ceil($count / self::PER_PAGE);
		return $pages;
	}

	public function run($page = 1)
	{
		if ($page == 1) {
			App::getContainer()->getSearchAdapter()->deleteContentTypeFromIndex($this->getContentType());
		}

		$start = ($page-1) * self::PER_PAGE;
		$limit = self::PER_PAGE;
		$ids = $this->getContainer()->getDb()->fetchAllCol("
			SELECT id
			FROM {$this->getTable()}
			ORDER BY id ASC
			LIMIT $start, $limit
		");

		$batch = $this->getContainer()->getEm()->getRepository($this->getEntity())->getByIds($ids);
		if ($batch) {
			$this->getContainer()->getSearchAdapter()->updateObjectsInIndex($batch);
		}
	}
}
