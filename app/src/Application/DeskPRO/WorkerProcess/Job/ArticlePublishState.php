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
 * @subpackage WorkerProcess
 */

namespace Application\DeskPRO\WorkerProcess\Job;

use Application\DeskPRO\App;
use Application\DeskPRO\Log\Logger;
use Application\DeskPRO\Entity\Article;

/**
 * Goes through articles with a publish date that was set in the future (publish now),
 * or an end date set (deleting or archivng now).
 */
class ArticlePublishState extends AbstractJob
{
	const DEFAULT_INTERVAL = 1800; // 30 mins

	public function run()
	{
		$count_publish = 0;
		$count_unpublish = 0;

		$article_ids = App::getDb()->fetchAllCol("
			SELECT articles.id
			FROM articles
			WHERE hidden_status = 'unpublished'
			AND date_published < '" . date('Y-m-d H:i:s') . "'
		");

		$count_publish = count($article_ids);
		$this->processPublish($article_ids);

		$article_ids = App::getDb()->fetchAllCol("
			SELECT articles.id
			FROM articles
			WHERE status = 'published'
			AND date_end < '" . date('Y-m-d H:i:s') . "'
		");

		$count_unpublish = count($article_ids);
		$this->processUnpublish($article_ids);

		if ($count_publish OR $count_unpublish) {
			$part = array();
			if ($count_publish) {
				$part[] = "Published {$count_publish} articles";
			}
			if ($count_publish) {
				$part[] = "Unpublished {$count_unpublish} articles";
			}

			$msg = implode(' and ', $part);
			$this->logStatus($msg);
		}
	}

	protected function processPublish(array $article_ids)
	{
		if (!$article_ids) return;

		$batch = 0;
		foreach ($article_ids as $article_id) {
			$article = App::findEntity('DeskPRO:Article', $article_id);

			$article['status_code'] = Article::STATUS_PUBLISHED;

			App::getOrm()->persist($article);
			if ($batch++ >= 20) {
				App::getOrm()->flush();
				App::getOrm()->clear();
			}
		}

		App::getOrm()->flush();
		App::getOrm()->clear();
	}

	protected function processUnpublish(array $article_ids)
	{
		if (!$article_ids) return;

		$batch = 0;
		foreach ($article_ids as $article_id) {
			$article = App::findEntity('DeskPRO:Article', $article_id);

			if ($article['end_action'] == Article::END_ACTION_ARCHIVE) {
				$article['status_code'] = Article::STATUS_ARCHIVED;
			} else {
				$article['status_code'] = Article::STATUS_HIDDEN . '.' . Article::HIDDEN_STATUS_DELETED;
			}

			App::getOrm()->persist($article);
			if ($batch++ >= 20) {
				App::getOrm()->flush();
				App::getOrm()->clear();
			}
		}

		App::getOrm()->flush();
		App::getOrm()->clear();
	}
}
