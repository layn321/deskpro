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

class KbSearchLogStep extends AbstractDeskpro3Step
{
	public $on_fast = false;

	const PERPAGE = 1000;

	public static function getTitle()
	{
		return 'Import Search Log';
	}

	public function countPages()
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM faq_searchlog");

		if (!$count) {
			return 1;
		}

		if ($count > 45000) {
			return 1;
		}

		return ceil($count / 1000);
	}

	public function preRunAll()
	{
		$this->importer->removeTableIndexes('searchlog');
		$this->importer->removeTableIndexes('ratings');
	}

	public function postRunAll()
	{
		$this->importer->restoreTableIndexes('searchlog');
		$this->importer->restoreTableIndexes('ratings');
	}

	public function run($page = 1)
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM faq_searchlog");

		if ($count > 45000) {
			$this->logMessage('Too many records, skipping');
			return;
		}

		if ($page == 1) {
			$this->preRunAll();
		}

		$batch = $this->getBatch($page);

		$this->getDb()->beginTransaction();
		try {
			foreach ($batch['faq_searchlog'] as $log) {
				$this->processLog(
					$log,
					isset($batch['faq_searchlog_solved'][$log['id']]) ? $batch['faq_searchlog_solved'][$log['id']] : array()
				);
			}

			$this->flushSaveMappedIdBuffer();
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}


		if ($page >= $this->countPages()) {
			$this->postRunAll();
		}
	}

	public function processLog($log, array $searchlog_solved)
	{
		if ($log['userid']) {
			$person_id = $this->getMappedNewId('user', $log['userid']);
			if (!$person_id) {
				$person_id = null;
			}
		}
		$person_id = null;

		#------------------------------
		# Save searchlog
		#------------------------------

		$this->getDb()->insert('searchlog', array(
			'person_id' => $person_id,
			'query' => $log['query'],
			'num_results' => (int)$log['total'],
			'date_created' => date('Y-m-d H:i:s', $log['timestamp'] ? $log['timestamp'] : time())
		));

		$search_id = $this->getDb()->lastInsertId();

		#------------------------------
		# Rated searches become ratings with linked searches
		#------------------------------

		foreach ($searchlog_solved as $solved) {
			if ($solved['userid']) {
				$person_id = $this->getMappedNewId('user', $solved['userid']);
				if (!$person_id) {
					continue;
				}
			} else {
				$person_id = null;
			}

			$article_id = $this->getMappedNewId('faq_article', $solved['articleid']);
			if (!$article_id) {
				continue;
			}

			$insert_rating = array();
			$insert_rating['object_type']  = 'article';
			$insert_rating['object_id']    = $article_id;
			$insert_rating['date_created'] = date('Y-m-d H:i:s');
			$insert_rating['rating']       = $solved['solved'] ? 1 : -1;
			$insert_rating['searchlog_id'] = $search_id;

			$this->getDb()->insert('ratings', $insert_rating);
		}
	}


	public function getBatch($page)
	{
		$start = (($page-1) * self::PERPAGE) + 1;
		$end   = $page * self::PERPAGE;

		$between_where = "BETWEEN $start AND $end";

		$batch = array(
			'faq_searchlog' => array(),
			'faq_searchlog_solved' => array(),
		);

		#------------------------------
		# Fetch faq_searchlog
		#------------------------------

		$q = $this->olddb->query("
			SELECT id, `timestamp`, query, total, userid
			FROM faq_searchlog
			WHERE id $between_where
		");
		$q->execute();

		$user_ids = array();
		while ($l = $q->fetch(\PDO::FETCH_ASSOC)) {
			$batch['faq_searchlog'][$l['id']] = $l;
			if ($l['userid']) {
				$user_ids[] = $l['userid'];
			}
		}

		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch faq_searchlog_solved
		#------------------------------

		$q = $this->olddb->query("
			SELECT id, articleid, searchid, userid, solved
			FROM faq_searchlog_solved
			WHERE searchid $between_where
		");
		$q->execute();

		$article_ids = array();
		while ($l = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch['faq_searchlog'][$l['searchid']])) {
				continue;
			}

			if (!isset($batch['faq_searchlog_solved'][$l['searchid']])) {
				$batch['faq_searchlog_solved'][$l['searchid']] = array();
			}

			$batch['faq_searchlog_solved'][$l['searchid']][] = $l;

			if ($l['userid']) {
				$userids[] = $l['userid'];
			}
			$article_ids[] = $l['articleid'];
		}

		$q->closeCursor();
		unset($q);


		#------------------------------
		# Cache user ids and article ids
		#------------------------------

		$user_ids    = array_unique($user_ids);
		$article_ids = array_unique($article_ids);

		$this->getMappedNewIdsArray('user', $user_ids);
		$this->getMappedNewIdsArray('faq_article', $article_ids);

		return $batch;
	}
}
