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
use Orb\Util\Arrays;

/**
 * Sends article and category notifications to users with subscriptions
 */
class KbSubscriptions extends AbstractJob
{
	const DEFAULT_INTERVAL = 7200;

	public function run()
	{
		$last_time = App::getSetting('user.kb_subscriptions_last');

		App::getDb()->replace('settings', array(
			'name'  => 'user.kb_subscriptions_last',
			'value' => time()
		));

		if (!App::getSetting('user.kb_subscriptions')) {
			return;
		}

		if (!$last_time) {
			return;
		}

		$last_date = new \DateTime("@$last_time");

		#------------------------------
		# Find articles
		#------------------------------

		$published = App::getOrm()->createQuery("
			SELECT a
			FROM DeskPRO:Article a INDEX BY a.id
			LEFT JOIN a.categories cat
			WHERE a.status = 'published' AND a.date_published > :date
			ORDER BY a.date_published DESC
		")->setMaxResults(250)->execute(array('date' => $last_date));

		$updated = App::getOrm()->createQuery("
			SELECT a
			FROM DeskPRO:Article a INDEX BY a.id
			LEFT JOIN a.categories cat
			WHERE a.status = 'published' AND (a.date_updated > :date OR a.date_last_comment > :date)
			ORDER BY a.date_updated DESC
		")->setMaxResults(250)->execute(array('date' => $last_date));

		if (!$published && !$updated) {
			return;
		}

		#------------------------------
		# Get subscriptions
		#------------------------------

		$structure = App::getContainer()->getSystemService('publish_structure');
		$helper = $structure->getArticleCategoryHelper();

		$category_ids = array();
		$article_ids  = array();

		foreach ($published as $a) {
			foreach ($a->categories as $c) {
				$category_ids[] = $c->getId();
			}
		}
		foreach ($updated as $a) {
			$article_ids[] = $a->getId();
		}

		$category_ids = array_unique($category_ids);
		$article_ids = array_unique($article_ids);

		$cat_subs     = array();
		$article_subs = array();

		if ($category_ids) {

			// Users can be subscribed to a category higher-up,
			// so for each article need to include subs for the whole path
			$add_ids = array();
			foreach ($category_ids as $cid) {
				$parents = $helper->getPath(array('id' => $cid));
				foreach ($parents as $c) {
					$add_ids[] = $c['id'];
				}
			}

			$category_ids = array_merge($category_ids, $add_ids);
			$category_ids = array_unique($category_ids);

			$cat_subs = App::getDb()->fetchAllGrouped("
				SELECT person_id, category_id
				FROM kb_subscriptions
				WHERE category_id IN (" . implode(',', $category_ids) . ")
			", array(), 'person_id', null, 'category_id');
		}

		if ($article_ids) {
			$article_subs = App::getDb()->fetchAllGrouped("
				SELECT person_id, article_id
				FROM kb_subscriptions
				WHERE article_id IN (" . implode(',', $article_ids) . ")
			", array(), 'person_id', null, 'article_id');
		}

		#------------------------------
		# Sort subscriptions into users
		#------------------------------

		$user_to_articles = array();

		foreach ($cat_subs as $person_id => $cids) {
			foreach ($published as $article) {
				foreach ($article->categories as $cat) {
					$path = $helper->getPathIds($cat);
					$path[] = $cat->getId();

					if (Arrays::isIn($path, $cids)) {
						if (!isset($user_to_articles[$person_id])) $user_to_articles[$person_id] = array();
						$user_to_articles[$person_id][$article->getId()] = $article;
					}
				}
			}
		}

		foreach ($article_subs as $person_id => $aids) {
			foreach ($aids as $aid) {
				if (!isset($updated[$aid])) continue;

				if (!isset($user_to_articles[$person_id])) $user_to_articles[$person_id] = array();
				$user_to_articles[$person_id][$aid] = $updated[$aid];
			}
		}

		if (!$user_to_articles) {
			return;
		}

		#------------------------------
		# Verify permissions
		#------------------------------

		$user_groupmembers = App::getDb()->fetchAllGrouped("
			SELECT person_id, usergroup_id
			FROM person2usergroups
			WHERE person_id IN (" . implode(',', array_keys($user_to_articles)) . ")
		", array(), 'person_id', null, 'usergroup_id');

		$cat_groups = App::getDb()->fetchAllGrouped("
			SELECT category_id, usergroup_id
			FROM article_category2usergroup
		", array(), 'category_id', null, 'usergroup_id');

		$all_user_to_articles = $user_to_articles;
		$user_to_articles = array();

		foreach ($all_user_to_articles as $person_id => $articles) {

			$person_ugs = isset($user_groupmembers[$person_id]) ? $user_groupmembers[$person_id] : array();
			$person_ugs[] = 1; // Everyone

			foreach ($articles as $article) {
				$add = false;
				foreach ($article->categories as $cat) {
					$cat_ugs = isset($cat_groups[$cat->getId()]) ? $cat_groups[$cat->getId()] : array();
					if (Arrays::isIn($person_ugs, $cat_ugs)) {
						$add = true;
						break;
					}
				}

				if ($add) {
					if (!isset($user_to_articles[$person_id])) $user_to_articles[$person_id] = array();
					$user_to_articles[$person_id][$article->getId()] = $article;
				}
			}
		}

		unset($all_user_to_articles);

		#------------------------------
		# Now send the emails (they are queued)
		#------------------------------

		foreach ($user_to_articles as $person_id => $articles) {

			$person = App::getOrm()->find('DeskPRO:Person', $person_id);
			if (!$person) continue;

			$new_articles     = array();
			$updated_articles = array();

			foreach ($articles as $article) {
				if ($article->date_published > $last_date) {
					$new_articles[] = $article;
				} else {
					$updated_articles[] = $article;
				}
			}

			$message = App::getMailer()->createMessage();
			$message->setToPerson($person);
			$message->setTemplate('DeskPRO:emails_user:kb-subscription.html.twig', array(
				'person'           => $person,
				'new_articles'     => $new_articles,
				'updated_articles' => $updated_articles,
				'unsub_auth'       => \Orb\Util\Util::generateStaticSecurityToken(App::getSetting('core.app_secret') . $person->getId() . $person->secret_string)
			));
			$message->enableQueueHint(1);

			App::getMailer()->sendNow($message);

			// Saves mem
			App::getOrm()->detach($person);
		}

		if ($user_to_articles) {
			$this->logStatus("Send " . count($user_to_articles) . " notifications");
		}
	}
}
