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

use Application\DeskPRO\Entity\News;
use Application\DeskPRO\Entity\NewsRevision;
use Application\DeskPRO\Entity\NewsCategory;

class UserNewsStep extends AbstractDeskpro3Step
{
	/**
	 * @var \Application\DeskPRO\Entity\NewsCategory
	 */
	public $category;

	public static function getTitle()
	{
		return 'Import User News';
	}

	public function run($page = 1)
	{
		// If there arent any news besides the default, delete default data
		$default_check = $this->getDb()->fetchColumn("SELECT id FROM news ORDER BY id DESC LIMIT 1");
		if (!$default_check || $default_check == 1) {
			$this->getDb()->exec("DELETE FROM news");
			$this->getDb()->exec("DELETE FROM news_categories");
		}

		$this->category = $this->getEm()->createQuery("SELECT c FROM DeskPRO:NewsCategory c ORDER BY c.id ASC")->getOneOrNullResult();
		if (!$this->category) {
			$this->category = new NewsCategory();
			$this->category->title = "General";
			$this->getEm()->persist($this->category);
			$this->getEm()->flush();

			$this->getDb()->insert('news_category2usergroup', array(
				'category_id' => $this->category->id,
				'usergroup_id' => 1
			));
		}

		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM news");
		if (!$count) {
			return;
		}

		$this->logMessage(sprintf("Importing %d news entries", $count));
		$news_ids = $this->getOldDb()->fetchAllCol("SELECT id FROM news ORDER BY `timestamp` DESC");

		foreach ($news_ids as $nid) {
			$this->getDb()->beginTransaction();

			try {
				$this->processNews($nid);
				$this->getDb()->commit();
			} catch (\Exception $e) {
				$this->getDb()->rollback();
				throw $e;
			}
		}
	}

	public function processNews($news_id)
	{
		$news = $this->getOldDb()->fetchAssoc("SELECT * FROM news WHERE Id = ?", array($news_id));

		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('news', $news['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$news['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		// Skip empty ones
		if (!trim($news['title']) && !trim($news['details'])) {
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$new_person = $this->getEm()->find('DeskPRO:Person', $this->getMappedNewId('tech', $news['techid']));

		$new_news = new News();
		$new_news->category = $this->category;
		if ($news['logged_out']) {
			$new_news->setStatusCode(News::STATUS_PUBLISHED);
		} else {
			$new_news->setStatusCode(News::STATUS_HIDDEN . '.' . News::HIDDEN_STATUS_UNPUBLISHED);
		}
		$new_news->date_created = new \DateTime('@' . $news['timestamp']);
		$new_news->date_published = new \DateTime('@' . $news['timestamp']);
		$new_news->person = $new_person;
		$new_news->title = empty($news['title']) ? 'Untitled' : $news['title'];
		$new_news->content = empty($news['details']) ? '(empty)' : $news['details'];

		$this->getEm()->persist($new_news);
		$this->getEm()->flush();

		$this->saveMappedId('news', $news['id'], $new_news->id);

		$this->db->insert('import_datastore', array(
			'typename' => 'dp3_newsid_' . $news['id'],
			'data' => $new_news->id
		));

		#------------------------------
		# Create the first revision
		#------------------------------

		$revision = new NewsRevision();
		$revision->news = $new_news;
		$revision->title = $new_news->title;
		$revision->content = $new_news->content;
		$revision->person = $new_person;
		$revision->date_created = $new_news->date_created;

		$this->getEm()->persist($revision);
		$this->getEm()->flush();
	}
}
