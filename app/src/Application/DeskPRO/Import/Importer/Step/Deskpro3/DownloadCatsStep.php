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

use Application\DeskPRO\Entity\DownloadCategory;

class DownloadCatsStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Download Categories';
	}

	public function run($page = 1)
	{
		// If there arent any downloads, delete default download cats
		$default_check = $this->getDb()->fetchColumn("SELECT id FROM downloads LIMIT 1");
		if (!$default_check) {
			$this->getDb()->exec("DELETE FROM downloads");
			$this->getDb()->exec("DELETE FROM download_categories");
		}

		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM files_cats");
		if ($count) {
			$this->logMessage(sprintf("Importing %d download categories", $count));

			$start_time = microtime(true);

			$this->getDb()->beginTransaction();
			try {
				$this->processCategories();
				$this->getDb()->commit();
			} catch (\Exception $e) {
				$this->getDb()->rollback();
				throw $e;
			}

			$end_time = microtime(true);
			$this->logMessage(sprintf("Done all categories. Took %.3f seconds.", $end_time-$start_time));
		}
	}


	/**
	 * Process all categories
	 */
	public function processCategories()
	{
		$cats = $this->getOldDb()->fetchAll("SELECT * FROM files_cats ORDER BY id ASC");
		if (!$cats) {
			$cats = array();
		}

		// If there are any files in 'top category', then we'll create a new cat called 'Files'
		$top_files = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM files WHERE category = 0 LIMIT 1");

		if ($top_files) {
			$this->getLogger()->log("Adding top-level category", 'DEBUG');
			$cat = array(
				'id' => 0,
				'name' => 'Files',
				'displayorder' => 0
			);
			array_unshift($cats, $cat);
		}

		foreach ($cats as $cat) {
			#------------------------------
			# Make sure we havent already done them
			#------------------------------

			$check_exist = $this->getMappedNewId('file_cat', $cat['id']);
			if ($check_exist) {
				$this->getLogger()->log("{$cat['id']} already mapped, skipping", 'DEBUG');
				continue;
			}

			#------------------------------
			# Create it
			#------------------------------

			$new_cat = new DownloadCategory();
			$new_cat->title = $cat['name'];
			$new_cat->display_order = $cat['displayorder'];

			$this->getEm()->persist($new_cat);
			$this->getEm()->flush();

			$this->saveMappedId('file_cat', $cat['id'], $new_cat->id);
		}
	}
}
