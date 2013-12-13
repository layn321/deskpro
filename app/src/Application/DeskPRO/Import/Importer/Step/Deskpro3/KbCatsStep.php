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

class KbCatsStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Knowledgebase Categories';
	}

	public function run($page = 1)
	{
		// If there arent any articles besides our default, remove the default data
		$default_check = $this->getDb()->fetchColumn("SELECT id FROM articles ORDER BY id DESC LIMIT 1");
		if (!$default_check || $default_check == 1) {
			$this->getDb()->exec("DELETE FROM articles");
			$this->getDb()->exec("DELETE FROM article_categories");
		}

		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM faq_cats");
		if ($count) {
			$this->logMessage(sprintf("Importing %d knowledgebase categories", $count));

			$start_time = microtime(true);

			$this->getDb()->beginTransaction();
			try {
				$this->processCategories(0);
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
	 * Process all categories in a tree starting from by $parent_id.
	 * I.e., to process all categories start with a parent_id of 0
	 *
	 * @param $parent_id
	 */
	public function processCategories($parent_id)
	{
		$cats = $this->getOldDb()->fetchAll("SELECT * FROM faq_cats WHERE parent = ?", array($parent_id));
		if (!$cats) {
			return;
		}

		$new_parent = null;
		if ($parent_id) {
			$new_parent = $this->getEm()->find('DeskPRO:ArticleCategory', $this->getMappedNewId('faq_cat', $parent_id));
			if (!$new_parent) {
				return;
			}
		}

		foreach ($cats as $cat) {
			#------------------------------
			# Make sure we havent already done them
			#------------------------------

			$check_exist = $this->getMappedNewId('faq_cat', $cat['id']);
			if ($check_exist) {
				$this->getLogger()->log("{$cat['id']} already mapped, skipping", 'DEBUG');
				continue;
			}

			$new_cat = new ArticleCategory();
			$new_cat->title = $cat['name'];
			$new_cat->display_order = $cat['displayorder'];
			if ($new_parent) {
				$new_cat->parent = $new_parent;
			}

			$this->getEm()->persist($new_cat);
			$this->getEm()->flush();

			$this->saveMappedId('faq_cat', $cat['id'], $new_cat->id);

			$this->db->insert('import_datastore', array(
				'typename' => 'dp3_kbcatid_' . $cat['id'],
				'data' => $new_cat->id
			));

			$this->processCategories($cat['id']);
		}
	}
}
