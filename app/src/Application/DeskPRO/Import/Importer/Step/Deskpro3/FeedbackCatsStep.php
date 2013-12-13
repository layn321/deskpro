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

use Application\DeskPRO\Entity\FeedbackCategory;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\FeedbackComment;

class FeedbackCatsStep extends AbstractDeskpro3Step
{
	public $cat_field;
	public $cat_count = 0;

	public static function getTitle()
	{
		return 'Import Idea Categories';
	}

	public function run($page = 1)
	{
		if (!$this->importer->doesOldTableExist('user_idea_categories')) {
			return;
		}

		$this->getDb()->delete('custom_def_feedback', array('sys_name' => 'cat'));

		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM user_idea_categories");
		$this->logMessage(sprintf("Importing %d feedback categories", $count));

		$start_time = microtime(true);

		$this->getDb()->beginTransaction();
		try {
			// Create the field
			$this->cat_field = new \Application\DeskPRO\Entity\CustomDefFeedback();
			$this->cat_field->handler_class = 'Application\\DeskPRO\\CustomFields\\Handler\\Choice';
			$this->cat_field->title = 'Category';
			$this->cat_field->sys_name = 'cat';
			$this->cat_field->description = 'Category';
			$this->getEm()->persist($this->cat_field);
			$this->getEm()->flush();

			$this->processCategories(0);
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("Done all categories. Took %.3f seconds.", $end_time-$start_time));

		// Create the initial status categories
		foreach (array('planned' => 'Planning', 'started' => 'Started', 'review' => 'Under Review') as $type => $t) {
			$s = new \Application\DeskPRO\Entity\FeedbackStatusCategory();
			$s->status_type = 'active';
			$s->title = $t;
			$this->getEm()->persist($s);
			$this->getEm()->flush();

			$this->saveMappedId('ideas_cat_active', $type, $s->id);
		}

		foreach (array('completed' => 'Completed', 'duplidate' => 'Duplicate', 'declined' => 'Declined') as $type => $t) {
			$s = new \Application\DeskPRO\Entity\FeedbackStatusCategory();
			$s->status_type = 'closed';
			$s->title = $t;
			$this->getEm()->persist($s);
			$this->getEm()->flush();

			$this->saveMappedId('ideas_cat_closed', $type, $s->id);
		}
	}


	public function processCategories($parent_id, $processing_parent_id = 0, $depth = 0, $prefix = array())
	{
		if ($parent_id) {
			$cats = $this->getOldDb()->fetchAll("SELECT * FROM user_idea_categories WHERE parent_id = ?", array($parent_id));
		} else {
			$cats = $this->getOldDb()->fetchAll("SELECT * FROM user_idea_categories WHERE parent_id IS NULL");
		}
		if (!$cats) {
			return;
		}

		$new_parent = 0;
		if ($parent_id) {
			if ($depth >= 2) {
				$new_parent = $this->getMappedNewId('feedback_cat', $processing_parent_id);
			} else {
				$new_parent = $this->getMappedNewId('feedback_cat', $parent_id);
			}

			if (!$new_parent) {
				return;
			}
		}

		foreach ($cats as $cat) {

			$this->cat_count++;

			#------------------------------
			# Make sure we havent already done them
			#------------------------------

			$check_exist = $this->getMappedNewId('feedback_cat', $cat['id']);
			if ($check_exist) {
				$this->getLogger()->log("{$cat['id']} already mapped, skipping", 'DEBUG');
				continue;
			}

			if ($depth == 0) {
				$title = $cat['title'];
			} else {
				$prefix[] = $cat['title'];
				$title = implode(' > ', $prefix);
			}

			$child = $this->cat_field->createChild();
			$child->setTitle($title);
			$child->setDisplayOrder($this->cat_count);
			$child->setOption('parent_id', $new_parent);

			$this->getEm()->persist($child);
			$this->getEm()->flush();

			$this->saveMappedId('feedback_cat', $cat['id'], $child->getId());

			if (!$processing_parent_id) {
				$processing_parent_id = $cat['id'];
			}

			$this->processCategories($cat['id'], $processing_parent_id, $depth+1, $prefix);

			if ($depth >= 1) {
				array_pop($prefix);
			}
		}
	}
}
