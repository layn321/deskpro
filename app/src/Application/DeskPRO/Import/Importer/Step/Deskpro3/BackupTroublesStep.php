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

use Application\DeskPRO\Entity\ArticleCategory;
use Application\DeskPRO\Entity\Article;
use Application\DeskPRO\Entity\ArticleComment;

class BackupTroublesStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Backup Troubleshooters';
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);
		$this->logMessage("Saving troubleshooter data");

		$troubles = $this->getOldDb()->fetchAll("SELECT * FROM trouble");

		foreach ($troubles as $t) {
			$this->getDb()->beginTransaction();

			try {
				$this->processTrouble($t);
				$this->getDb()->commit();
			} catch (\Exception $e) {
				$this->getDb()->rollback();
				throw $e;
			}
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}

	public function processTrouble($trouble)
	{
		$trouble['_comments']     = $this->getOldDb()->fetchAll("SELECT * FROM trouble_comments WHERE troubleid = ?", array($trouble['id']));
		$trouble['_permissions']  = $this->getOldDb()->fetchAll("SELECT * FROM trouble_permissions WHERE troubleid = ?", array($trouble['id']));
		$trouble['_questions']    = $this->getOldDb()->fetchAll("SELECT * FROM trouble_questions WHERE troubleid = ?", array($trouble['id']));
		$trouble['_rating']       = $this->getOldDb()->fetchAll("SELECT * FROM trouble_rating WHERE troubleid = ?", array($trouble['id']));

		$this->getDb()->replace('import_datastore', array('typename' => 'trouble.' . $trouble['id'], 'data' => serialize($trouble)));
	}
}
