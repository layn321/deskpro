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
 * @subpackage
 */

namespace Application\InstallBundle\Upgrade\Build;

class Build1374763389 extends AbstractBuild
{
	public function run()
	{
		$this->out("Clear out old result caches");
		$this->execMutateSql("TRUNCATE TABLE result_cache");

		$this->out("Fix possible invalid feedback statuses");
		$active_id = $this->container->getDb()->fetchColumn("SELECT id FROM feedback_status_categories WHERE status_type = 'active' ORDER BY display_order DESC LIMIT 1");
		if ($active_id) {
			$this->execMutateSql("
				UPDATE feedback
				SET status_category_id = $active_id
				WHERE status = 'active' AND status_category_id IS NULL
			");
		}

		$closed_id = $this->container->getDb()->fetchColumn("SELECT id FROM feedback_status_categories WHERE status_type = 'closed' ORDER BY display_order DESC LIMIT 1");
		if ($closed_id) {
			$this->execMutateSql("
				UPDATE feedback
				SET status_category_id = $closed_id
				WHERE status = 'closed' AND status_category_id IS NULL
			");
		}
	}
}