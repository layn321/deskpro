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
use Application\DeskPRO\Entity\ArticleRevision;
use Application\DeskPRO\Entity\ArticleComment;
use Orb\Util\Arrays;
use Orb\Util\Strings;

class KbStep extends AbstractDeskpro3Step
{
	/**
	 * @var array
	 */
	public $custom_field_info = array();

	/**
	 * @var \Application\DeskPRO\CustomFields\FieldManager
	 */
	public $fieldmanager;

	public static function getTitle()
	{
		return 'Import Knowledgebase';
	}

	public function countPages()
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM faq_articles");
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
		$ids = $this->getOldDb()->fetchAllCol("SELECT id FROM faq_articles ORDER BY timestamp_made ASC LIMIT $start, 50");

		return $ids;
	}

	public function preRunAll()
	{
		$this->importer->removeTableIndexes('articles');
		$this->importer->removeTableIndexes('article_comments');
		$this->importer->removeTableIndexes('article_revisions');
		$this->importer->removeTableIndexes('article_categories');
		$this->importer->removeTableIndexes('article_attachments');
	}

	public function postRunAll()
	{
		$this->importer->restoreTableIndexes('articles');
		$this->importer->restoreTableIndexes('article_comments');
		$this->importer->restoreTableIndexes('article_revisions');
		$this->importer->restoreTableIndexes('article_categories');
		$this->importer->restoreTableIndexes('article_attachments');
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		if ($page == 1) {
			$this->preRunAll();
		}

		$batch = $this->getIdsBatch($page - 1);

		$ids = implode(',', $batch);
		if (!$ids) $ids = '0';

		$articles = $this->getOldDb()->fetchAll("SELECT * FROM faq_articles WHERE id IN ($ids)");

		$this->custom_field_info = $this->getOldDb()->fetchAll("SELECT * FROM faq_def");
		$this->fieldmanager = $this->getContainer()->getSystemService('article_fields_manager');
		$this->fieldmanager->getFields();

		foreach ($articles as $a) {
			$this->getDb()->beginTransaction();

			try {
				$this->processArticle($a);
				$this->getDb()->commit();
			} catch (\Exception $e) {
				$this->getDb()->rollback();
				throw $e;
			}
		}

		if ($page >= $this->countPages()) {
			$this->postRunAll();
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}


	/**
	 * Process an article
	 */
	public function processArticle($article)
	{
		$article_id = $article['id'];

		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('faq_article', $article['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$article['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$new_category = $this->getEm()->find('DeskPRO:ArticleCategory', $this->getMappedNewId('faq_cat', $article['category']));
		if (!$new_category) {
			$this->logMessage("{$article['id']} has an invalid category, skipping");
			return;
		}

		$new_person = null;
		if ($article['techid_made']) {
			$new_person = $this->getEm()->find('DeskPRO:Person', $this->getMappedNewId('tech', $article['techid_made']));
		} elseif ($article['userid']) {
			$new_person = $this->getEm()->find('DeskPRO:Person', $this->getMappedNewId('user', $article['userid']));
		}
		if (!$new_person) {
			$new_person = $this->getEm()->getRepository('DeskPRO:Person')->findOneBy(array('can_admin' => true));
		}

		$new_article = new Article();
		$new_article->addToCategory($new_category);
		if ($article['published']) {
			$new_article->setStatusCode(Article::STATUS_PUBLISHED);
		} else {
			$new_article->setStatusCode(Article::STATUS_HIDDEN . '.' . Article::HIDDEN_STATUS_UNPUBLISHED);
		}

		if ($article['title']) {
			$article['title'] = \Orb\Util\Strings::htmlEntityDecodeUtf8($article['title'], false);
		}

		$new_article->person = $new_person;
		$new_article->title = $article['title'] ?: 'Untitled';
		$new_article->content = '<div class="dp-question">'.$article['question'].'</div>' . $article['answer'];
		$new_article->date_created = new \DateTime('@' . $article['timestamp_made']);
		$new_article->date_published = new \DateTime('@' . $article['timestamp_made']);

		// Try a bit of cleanup to remove common whitespace
		$new_article->content = str_replace(
			array('<p></p>', '<p>&nbsp;</p>'),
			array('<br />', '<br />'),
		$new_article->content);

		$this->getEm()->persist($new_article);
		$this->getEm()->flush();

		$this->saveMappedId('faq_article', $article['id'], $new_article->id);

		$this->db->insert('import_datastore', array(
			'typename' => 'dp3_kbref_' . $article['ref'],
			'data' => $new_article->id
		));

		#------------------------------
		# Attachments
		#------------------------------

		$attachments = $this->getOldDb()->fetchAll("SELECT * FROM faq_attachments WHERE articleid = ?", array($article['id']));

		foreach ($attachments as $attach_info) {

			$pid = $this->getMappedNewId('tech', $attach_info['techid']);
			if (!$pid) {
				continue;
			}

			$blob_id = $this->getMappedNewId('faq_attachments-blob', $attach_info['id']);
			if (!$blob_id) {
				continue;
			}

			$insert_attach = array();
			$insert_attach['article_id'] = $new_article->id;
			$insert_attach['person_id'] = $pid;
			$insert_attach['blob_id'] = $blob_id;

			$this->getDb()->insert('article_attachments', $insert_attach);
		}


		#------------------------------
		# Images
		#------------------------------

		$attachments = $this->getOldDb()->fetchAll("SELECT * FROM images WHERE content_type = ? AND content_id = ?", array('faq_article', $article['id']));

		$article_updated = true;
		foreach ($attachments as $attach_info) {
			$blob_id = $this->getMappedNewId('images-blob', $attach_info['id']);
			if (!$blob_id) {
				continue;
			}

			$article_updated = true;
			$insert_attach = array();
			$insert_attach['article_id'] = $new_article->id;
			$insert_attach['person_id'] = $new_person->id;
			$insert_attach['blob_id'] = $blob_id;

			$this->getDb()->insert('article_attachments', $insert_attach);

			$blob = $this->getDb()->fetchAssoc("SELECT * FROM blobs WHERE id = ?", array($blob_id));

			// Rewrite the old getimage.php to for attachments to go through file.php
			$new_article->content = preg_replace(
				'#https?://(.*?)/getimage\.php\?id='.$attach_info['id'].'#',
				"![attach:{$blob['authcode']}:{$blob['filename']}]",
				$new_article->content
			);
		}

		if ($article_updated) {
			$this->getEm()->persist($new_article);
			$this->getEm()->flush();
		}

		#------------------------------
		# Create the first revision
		#------------------------------

		$revision = new ArticleRevision();
		$revision->article = $new_article;
		$revision->title = $new_article->title;
		$revision->content = $new_article->content;
		$revision->person = $new_person;
		$revision->date_created = $new_article->date_created;

		$this->getEm()->persist($revision);
		$this->getEm()->flush();

		#------------------------------
		# Import ratings
		#------------------------------

		$total_rating = 0;
		$count_rating = 0;

		$ratings = $this->getOldDb()->fetchAll("SELECT * FROM faq_rating WHERE faqid = ?", array($article['id']));
		foreach ($ratings as $r) {

			if (!$r['timestamp']) $r['timestamp'] = time();

			$insert_rating = array();
			$insert_rating['object_type']  = 'article';
			$insert_rating['object_id']    = $new_article->id;
			$insert_rating['ip_address']   = $r['ipaddress'];
			$insert_rating['date_created'] = date('Y-m-d H:i:s', $r['timestamp']);

			if ($r['rating'] == '60' || $r['rating'] == '40') {
				continue;
			}

			if ($r['rating'] == '100' || $r['rating'] == '80') {
				$insert_rating['rating'] = 1;
				$total_rating++;
			} else {
				$insert_rating['rating'] = -1;
			}

			$count_rating++;

			$this->getDb()->insert('ratings', $insert_rating);
		}

		#------------------------------
		# Keywords as sticky words
		#------------------------------

		$words = $this->getOldDb()->fetchAllCol("
			SELECT w.word
			FROM faq_keywords_articles a
			LEFT JOIN faq_keywords_words AS w ON (w.wordid = a.wordid)
			WHERE a.articleid = ? AND w.word IS NOT NULL
		", array($article['id']));

		foreach ($words as &$_w) {
			$_w = Strings::utf8_strtolower($_w);
		}

		$words = Arrays::removeFalsey($words);
		$words = array_unique($words);

		foreach ($words as $w) {
			$this->getDb()->replace('search_sticky_result', array(
				'word'        => $w,
				'object_type' => 'DeskPRO:Article',
				'object_id'   => $new_article->id
			));
		}

		#------------------------------
		# Comments
		#------------------------------

		$comments = $this->getOldDb()->fetchAll("SELECT * FROM faq_comments WHERE articleid = ? AND published = 1", array($article['id']));
		$count_comment = 0;

		foreach ($comments as $comment) {
			$new_comment = new ArticleComment();
			$new_comment->date_created = new \DateTime('@' . $comment['timestamp_created']);
			if ($comment['userid']) {
				$new_comment->person = $this->getEm()->find('DeskPRO:Person', $this->getMappedNewId('user', $comment['userid']));
			}
			if ($comment['useremail']) {
				$new_comment->email = $comment['useremail'];
			}
			$new_comment->content = $comment['comments'];
			$new_comment->is_reviewed = true;

			$this->getEm()->persist($new_comment);
			$this->getEm()->flush();

			$count_comment++;
		}

		$this->getDb()->update('articles', array(
			'num_ratings' => $count_rating,
			'total_rating' => $total_rating,
			'num_comments' => $count_comment
		), array('id' => $new_article->id));

		#------------------------------
		# Custom fields
		#------------------------------

		foreach ($this->custom_field_info as $field_info) {
			$name = $field_info['name'];
			if (!isset($article[$name]) || !$article[$name]) {
				continue;
			}

			$field = $this->fieldmanager->getFieldFromId($this->getMappedNewId('kb_def', $field_info['id']));
			if (!$field) {
				continue;
			}

			$data = null;
			switch ($field->handler_class) {
				case 'Application\\DeskPRO\\CustomFields\\Handler\\Text':
				case 'Application\\DeskPRO\\CustomFields\\Handler\\Textarea':
					$this->getDb()->insert('custom_data_article', array(
						'article_id' => $new_article->id,
						'field_id' => $field->id,
						'input' => $article[$name]
					));
					break;

				case 'Application\\DeskPRO\\CustomFields\\Handler\\Choice':
					$vals = explode('|||', $article[$name]);
					foreach ($vals as $val) {
						$new_val = $this->getMappedNewId('kb_def_choice', $field_info['id'].'_'.$val);
						if ($new_val) {
							$this->getDb()->insert('custom_data_article', array(
								'article_id' => $new_article->id,
								'field_id' => $new_val,
								'value' => 1
							));
						}
					}
					break;
			}
		}
	}
}
