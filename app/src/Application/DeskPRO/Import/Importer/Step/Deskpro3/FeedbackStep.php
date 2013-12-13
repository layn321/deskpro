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
use Application\DeskPRO\Entity\FeedbackRevision;
use Application\DeskPRO\Entity\FeedbackComment;

class FeedbackStep extends AbstractDeskpro3Step
{
	public $feedback_category;
	public $user_cat_field;

	public static function getTitle()
	{
		return 'Import Feedback';
	}

	public function countPages()
	{
		if (!$this->importer->doesOldTableExist('user_ideas')) {
			return 1;
		}
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM user_ideas");
		if (!$count) {
			return 1;
		}

		return ceil($count / 50);
	}

	/**
	 * @param $page
	 * @return array
	 */
	public function getIdsBatch($page)
	{
		$start = $page * 50;
		$ids = $this->getOldDb()->fetchAllCol("SELECT id FROM user_ideas ORDER BY created_at ASC LIMIT $start, 50");

		return $ids;
	}

	public function run($page = 1)
	{
		if (!$this->importer->doesOldTableExist('user_ideas')) {
			return;
		}

		$this->feedback_category = $this->getEm()->find('DeskPRO:FeedbackCategory', 1);
		$this->user_cat_field    = $this->getEm()->getRepository('DeskPRO:CustomDefFeedback')->findOneBy(array('sys_name' => 'cat'));
		$batch = $this->getIdsBatch($page - 1);

		$ids = implode(',', $batch);
		if (!$ids) $ids = '0';

		$ideas = $this->getOldDb()->fetchAll("SELECT * FROM user_ideas WHERE id IN ($ids)");
		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		$this->getDb()->beginTransaction();
		try {
			foreach ($ideas as $i) {
				$this->processFeedback($i);
			}
			$this->flushSaveMappedIdBuffer();
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}


	/**
	 * Process an feedback
	 */
	public function processFeedback($feedback)
	{
		$feedback_id = $feedback['id'];

		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('feedback', $feedback['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$feedback['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$new_category = $this->feedback_category;

		$new_person = null;
		if ($feedback['user_id']) {
			$new_person = $this->getEm()->find('DeskPRO:Person', $this->getMappedNewId('user', $feedback['user_id']));
		}
		if (!$new_person) {
			$new_person = $this->getEm()->getRepository('DeskPRO:Person')->findOneBy(array('can_admin' => true));
		}

		$new_feedback = new Feedback();
		$new_feedback->category = $new_category;
		if ($feedback['status'] == 'new') {
			$new_feedback->setStatusCode(Feedback::STATUS_NEW);
		} elseif ($feedback['status'] == 'accepted') {
			if ($feedback['completion_status'] == 'planned') {
				$id = $this->getMappedNewId('ideas_cat_active', 'planned');
			} elseif ($feedback['completion_status'] == 'started') {
				$id = $this->getMappedNewId('ideas_cat_active', 'started');
			} else {
				$id = $this->getMappedNewId('ideas_cat_active', 'review');
			}
			$new_feedback->setStatusCode(Feedback::STATUS_ACTIVE . ".$id");
		} elseif ($feedback['status'] == 'completed') {
			$id = $this->getMappedNewId('ideas_cat_closed', 'completed');
			$new_feedback->setStatusCode(Feedback::STATUS_CLOSED . ".$id");
		} else {
			$id = $this->getMappedNewId('ideas_cat_closed', 'declined');
			$new_feedback->setStatusCode(Feedback::STATUS_CLOSED . ".$id");
		}

		$new_feedback->person = $new_person;
		$new_feedback->title = $feedback['title'] ?: 'Untitled';
		$new_feedback->content = nl2br(htmlspecialchars($feedback['message'], \ENT_QUOTES, 'UTF-8'));
		$new_feedback->date_created = new \DateTime('@' . $feedback['created_at']);
		$new_feedback->date_published = new \DateTime('@' . $feedback['created_at']);

		$this->getEm()->persist($new_feedback);
		$this->getEm()->flush();

		$this->saveMappedId('feedback', $feedback['id'], $new_feedback->id, true);

		$user_cat_id = $this->getMappedNewId('feedback_cat', $feedback['category_id']);
		if ($user_cat_id) {
			$this->db->insert('custom_data_feedback', array(
				'feedback_id' => $new_feedback->id,
				'field_id'    => $user_cat_id,
				'value'       => 1,
				'input'       => ''
			));
		}

		$this->db->insert('import_datastore', array(
			'typename' => 'dp3_ideaid_' . $feedback['id'],
			'data' => $new_feedback->id
		));

		#------------------------------
		# Create the first revision
		#------------------------------

		$revision = new FeedbackRevision();
		$revision->feedback = $new_feedback;
		$revision->title = $new_feedback->title;
		$revision->content = $new_feedback->content;
		$revision->person = $new_person;
		$revision->date_created = $new_feedback->date_created;

		$this->getEm()->persist($revision);
		$this->getEm()->flush();

		#------------------------------
		# Import ratings
		#------------------------------

		$total_rating = 0;
		$count_rating = 0;

		$ratings = $this->getOldDb()->fetchAll("SELECT * FROM user_idea_votes WHERE idea_id = ?", array($feedback['id']));
		foreach ($ratings as $r) {

			if (!$r['created_at']) $r['created_at'] = time();

			$insert_rating = array();
			$insert_rating['object_type']  = 'idea';
			$insert_rating['object_id']    = $new_feedback->id;
			$insert_rating['ip_address']   = $r['user_ip'];
			$insert_rating['date_created'] = date('Y-m-d H:i:s', $r['created_at']);
			$insert_rating['rating'] = 1;

			if ($r['user_id']) {
				$person_id = $this->getMappedNewId('user', $r['user_id']);
				if ($person_id) {
					$insert_rating['person_id'] = $person_id;
				}
			}

			$total_rating++;
			$count_rating++;

			$this->getDb()->insert('ratings', $insert_rating);
		}

		#------------------------------
		# Comments
		#------------------------------

		$comments = $this->getOldDb()->fetchAll("SELECT * FROM user_idea_comments WHERE idea_id = ?", array($feedback['id']));
		$count_comment = 0;
		foreach ($comments as $comment) {
			$new_comment = new FeedbackComment();
			$new_comment->date_created = new \DateTime('@' . $comment['created_at']);
			if ($comment['user_id']) {
				$new_comment->person = $this->getEm()->find('DeskPRO:Person', $this->getMappedNewId('user', $comment['user_id']));
			} elseif ($comment['tech_id']) {
				$new_comment->person = $this->getEm()->find('DeskPRO:Person', $this->getMappedNewId('tech', $comment['tech_id']));
			}
			if (!$new_comment->person) {
				continue;
			}
			if ($comment['user_ip']) {
				$new_comment->ip_address = $comment['user_ip'];
			}

			$new_comment->content = $comment['message'];
			$new_comment->is_reviewed = true;

			$this->getEm()->persist($new_comment);
			$this->getEm()->flush();

			$count_comment++;
		}

		$this->getDb()->update('feedback', array(
			'num_ratings' => $count_rating,
			'total_rating' => $total_rating,
			'num_comments' => $count_comment
		), array('id' => $new_feedback->id));
	}
}
