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
 * @subpackage Addons
 */

namespace Application\DeskPRO\Publish;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\People\PersonContextInterface;

use Application\DeskPRO\Searcher\ArticleSearch;

use Orb\Util\Arrays;

/**
 * Helps fetch info related to structure of Publish
 */
class AgentHelper implements PersonContextInterface
{
	const ARTICLES  = 'articles';
	const DOWNLOADS = 'downloads';
	const NEWS      = 'news';
	const FEEDBACK  = 'feedback';

	protected $enabled_types = array('articles', 'downloads', 'news');

	public function setEnabledTypes(array $types)
	{
		$this->enabled_types = $types;
	}

	public function getSingleSpecificType()
	{
		$t = $this->enabled_types;
		if (count($t) == 1) {
			return array_pop($t);
		}

		return null;
	}

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}


	/**
	 * Get the category structure
	 *
	 * @param string $type
	 * @return array
	 */
	public function getCategoryStructure($type, $flat = false)
	{
		$entity_name = self::getCatEntityNameFor($type);

		return App::getEntityRepository($entity_name)->getRootNodes();
	}


	/**
	 * @param string $type
	 * @return int
	 */
	public function getCategoryCounts($type)
	{
		$entity_name = self::getCatEntityNameFor($type);
		$repos = App::getEntityRepository($entity_name);
		$counts = $repos->getAllCounts($this->person_context, null);

		return $counts;
	}


	/**
	 * Get an array of categories and their perms usergroups
	 *
	 * @param $type
	 * @return array
	 */
	public function getCategoryUsergroups($type)
	{
		$entity_name = self::getCatEntityNameFor($type);
		$repos = App::getEntityRepository($entity_name);
		$table = $repos->getPermissionTableName();

		if (!$table) {
			return array();
		}

		$cats_to_ugs = App::getDb()->fetchAllGrouped("
			SELECT category_id, usergroup_id
			FROM $table
		", array(), 'category_id', null, 'usergroup_id');

		return $cats_to_ugs;
	}


	############################################################################
	# Glossary
	############################################################################

	/**
	 * Get an array of glossary words, sorted into an alphabetically-indexed array
	 *
	 * @return array
	 */
	public function getGlossaryWordsIndex()
	{
		$glossary_words = App::getEntityRepository('DeskPRO:GlossaryWord')->getWords();
		$glossary_words = Arrays::sortIntoAlphabeticalIndex($glossary_words, null, true, true);

		return $glossary_words;
	}


	############################################################################
	# Validating Content
	############################################################################

	/**
	 * Counts all content that needs validating
	 *
	 * @return int
	 */
	public function getValidatingContentCount()
	{
		foreach ($this->enabled_types as $t) {
			$sql_parts[] = "(
				SELECT COUNT(*)
				FROM $t
				WHERE hidden_status = 'validating'
			) AS count_$t";
		}

		$sql =  "SELECT " . implode(', ', $sql_parts);

		$db = App::getDb();
		$results = $db->fetchAssoc($sql);

		return array_sum($results);
	}


	/**
	 * Get an array of all content awaiting validation from each content type.
	 *
	 * @return array
	 */
	public function getValidatingContent($limit = 25, $order_dir = 'ASC')
	{
		$results = $this->getValidatingContentInfo($limit, $order_dir);
		return $this->getContentFromInfo($results);
	}

	public function getValidatingContentInfo($limit = 25, $order_dir = 'ASC')
	{
		$sql_parts = array();

		if ($limit !== null && !is_array($limit)) {
			$limit = array(
				'max' => $limit,
				'offset' => 0
			);
		}

		$types = array(
			'articles'    => array('content_type' => 'articles',  'entity' => 'DeskPRO:Article',  'id_field' => 'article_id',  'rev_table' => 'article_revisions'),
			'downloads'   => array('content_type' => 'downloads', 'entity' => 'DeskPRO:Download', 'id_field' => 'download_id', 'rev_table' => 'download_revisions'),
			'news'        => array('content_type' => 'news',      'entity' => 'DeskPRO:News',     'id_field' => 'news_id',     'rev_table' => 'news_revisions'),
			'feedback'       => array('content_type' => 'feedback',     'entity' => 'DeskPRO:Feedback',     'id_field' => 'feedback_id',     'rev_table' => 'feedback_revisions'),
		);

		#------------------------------
		# Fetch from each comment table with a union
		#------------------------------

		foreach ($this->enabled_types as $t) {
			$t_info = $types[$t];
			$sql_parts[] = "(
				SELECT DISTINCT(c.id) as content_id, '{$t_info['content_type']}' as content_type, r.id AS revision_id, c.date_created
				FROM $t AS c
				LEFT JOIN {$t_info['rev_table']} r ON (c.id = r.{$t_info['id_field']})
				WHERE c.hidden_status = 'validating' OR r.status = 'validating'
			)";
		}

		$sql = implode(' UNION ', $sql_parts);
		if ($limit) {
			$sql .= " ORDER BY date_created $order_dir LIMIT {$limit['offset']}, {$limit['max']}";
		} else {
			$sql .= " ORDER BY date_created $order_dir";
		}

		$db = App::getDb();
		$results = $db->fetchAll($sql);

		return $results;
	}

	############################################################################
	# Validating Comments
	############################################################################

	public function getValidatingComments($limit = 25, $order_dir = 'ASC')
	{
		$sql_parts = array();

		if (!is_array($limit)) {
			$limit = array(
				'max' => $limit,
				'offset' => 0
			);
		}

		$types = $this->getCommentTypeInfo();

		#------------------------------
		# Fetch from each comment table with a union
		#------------------------------

		foreach ($this->enabled_types as $t) {
			$t_info = $types[$t];
			$sql_parts[] = "(
				SELECT id as comment_id, '{$t_info['content_type']}' as content_type, date_created
				FROM {$t_info['table']}
				WHERE status = 'validating' OR (status = 'visible' AND is_reviewed = 0)
			)";
		}

		$sql = implode(' UNION ', $sql_parts);
		$sql .= "ORDER BY date_created $order_dir LIMIT {$limit['offset']}, {$limit['max']}";

		$db = App::getDb();
		$results = $db->fetchAll($sql);

		if (!$results) return array();

		#------------------------------
		# Fetch each comment in the result
		#------------------------------

		$result_ids_typed = array();

		foreach ($results as $r) {
			if (!isset($result_ids_typed[$r['content_type']])) {
				$result_ids_typed[$r['content_type']] = array();
			}

			$result_ids_typed[$r['content_type']][] = $r['comment_id'];
		}

		$results_typed = array();

		foreach ($result_ids_typed as $t => $ids) {
			$t_info = $types[$t];
			$results_typed[$t] = App::getEntityRepository($t_info['entity'])->getByIds($ids);
		}

		#------------------------------
		# Put back into original sort order
		# as a combined array
		#------------------------------

		$results_ordered = array();

		foreach ($results as $r) {
			if (isset($results_typed[$r['content_type']][$r['comment_id']])) {
				$results_ordered[] = array(
					'info' => $r,
					'obj'  => $results_typed[$r['content_type']][$r['comment_id']]
				);
			}
		}

		return $results_ordered;
	}

	public function getValidatingCommentsCount()
	{
		$sql_parts = array();

		$types = $this->getCommentTypeInfo();

		foreach ($this->enabled_types as $t) {
			$t_info = $types[$t];
			$sql_parts[] = "(
				SELECT COUNT(*)
				FROM {$t_info['table']}
				WHERE status = 'validating' OR (status = 'visible' AND is_reviewed = 0)
			) AS count_$t";
		}

		$sql =  "SELECT " . implode(', ', $sql_parts);

		$db = App::getDb();
		$results = $db->fetchAssoc($sql);

		return array_sum($results);
	}

	############################################################################
	# All Comments
	############################################################################

	public function getComments($limit, $order_dir = 'DESC')
	{
		$sql_parts = array();

		if (!is_array($limit)) {
			$limit = array(
				'max' => $limit,
				'offset' => 0
			);
		}

		$types = $this->getCommentTypeInfo();

		#------------------------------
		# Fetch from each comment table with a union
		#------------------------------

		foreach ($this->enabled_types as $t) {
			$t_info = $types[$t];
			$sql_parts[] = "(
				SELECT id as comment_id, '{$t_info['content_type']}' as content_type, date_created
				FROM {$t_info['table']}
				WHERE status != 'deleted'
			)";
		}

		$sql = implode(' UNION ', $sql_parts);
		$sql .= "ORDER BY date_created $order_dir LIMIT {$limit['offset']}, {$limit['max']}";

		$db = App::getDb();
		$results = $db->fetchAll($sql);

		if (!$results) return array();

		#------------------------------
		# Fetch each comment in the result
		#------------------------------

		$result_ids_typed = array();

		foreach ($results as $r) {
			if (!isset($result_ids_typed[$r['content_type']])) {
				$result_ids_typed[$r['content_type']] = array();
			}

			$result_ids_typed[$r['content_type']][] = $r['comment_id'];
		}

		$results_typed = array();

		foreach ($result_ids_typed as $t => $ids) {
			$t_info = $types[$t];
			$results_typed[$t] = App::getEntityRepository($t_info['entity'])->getByIds($ids);
		}

		#------------------------------
		# Put back into original sort order
		# as a combined array
		#------------------------------

		$results_ordered = array();

		foreach ($results as $r) {
			if (isset($results_typed[$r['content_type']][$r['comment_id']])) {
				$results_ordered[] = array(
					'info' => $r,
					'obj'  => $results_typed[$r['content_type']][$r['comment_id']]
				);
			}
		}

		return $results_ordered;
	}

	public function getCommentsCountInfo()
	{
		$sql_parts = array();

		$types = $this->getCommentTypeInfo();

		foreach ($this->enabled_types as $t) {
			$t_info = $types[$t];
			$sql_parts[] = "(
				SELECT COUNT(*)
				FROM {$t_info['table']}
				WHERE status != 'deleted'
			) AS `$t`";
		}

		$sql =  "SELECT " . implode(', ', $sql_parts);

		$db = App::getDb();
		$results = $db->fetchAssoc($sql);

		$count_all = array_sum($results);

		$counts = $results;
		$counts['all'] = $count_all;

		return $counts;
	}

	public function getCommentTypeInfo($for_type = null, $prop = null)
	{
		static $types = array(
			'articles'     => array('content_type' => 'articles',     'table' => 'article_comments',        'entity' => 'DeskPRO:ArticleComment',       'id_field' => 'article_id'),
			'downloads'    => array('content_type' => 'downloads',    'table' => 'download_comments',       'entity' => 'DeskPRO:DownloadComment',      'id_field' => 'download_id'),
			'news'         => array('content_type' => 'news',         'table' => 'news_comments',           'entity' => 'DeskPRO:NewsComment',          'id_field' => 'news_id'),
			'feedback'     => array('content_type' => 'feedback',     'table' => 'feedback_comments',       'entity' => 'DeskPRO:FeedbackComment',      'id_field' => 'feedback_id'),
		);

		if ($for_type !== null) {
			if (!isset($types[$for_type])) {
				throw new \InvalidArgumentException("$for_type is an invalid comment type");
			}

			if ($prop !== null) {
				if (!isset($types[$for_type][$prop])) {
					throw new \InvalidArgumentException("$for_type.$prop is an invalid comment type property");
				}

				return $types[$for_type][$prop];
			}

			return $types[$for_type];
		}

		return $types;
	}

	############################################################################
	# Drafts
	############################################################################

	/**
	 * Get an array of all drafts
	 *
	 * @return array
	 */
	public function getDraftContent($limit = null, $order_dir = 'ASC', $all = false)
	{
		$results = $this->getDraftInfo($limit, $order_dir, $all);
		return $this->getContentFromInfo($results);
	}

	public function getDraftInfo($limit = null, $order_dir = 'ASC', $all = false)
	{
		$sql_parts = array();

		if ($limit !== null && !is_array($limit)) {
			$limit = array(
				'max' => $limit,
				'offset' => 0
			);
		}

		$types = array(
			'articles'    => array('content_type' => 'articles',  'entity' => 'DeskPRO:Article',  'id_field' => 'article_id',  'rev_table' => 'article_revisions'),
			'downloads'   => array('content_type' => 'downloads', 'entity' => 'DeskPRO:Download', 'id_field' => 'download_id', 'rev_table' => 'download_revisions'),
			'news'        => array('content_type' => 'news',      'entity' => 'DeskPRO:News',     'id_field' => 'news_id',     'rev_table' => 'news_revisions'),
			'feedback'       => array('content_type' => 'feedback',     'entity' => 'DeskPRO:Feedback',     'id_field' => 'feedback_id',     'rev_table' => 'feedback_revisions'),
		);

		#------------------------------
		# Fetch from each comment table with a union
		#------------------------------

		foreach ($this->enabled_types as $t) {
			$t_info = $types[$t];
			$person_sql = '';

			if(!$all) {
				$person_sql = " AND c.person_id = {$this->person_context['id']}";
			}

			$sql_parts[] = "(
				SELECT DISTINCT(c.id) as content_id, '{$t_info['content_type']}' as content_type, r.id AS revision_id, c.date_created
				FROM $t AS c
				LEFT JOIN {$t_info['rev_table']} r ON (c.id = r.{$t_info['id_field']})
				WHERE (c.status = 'hidden' AND c.hidden_status = 'draft' $person_sql) OR (r.status = 'draft' $person_sql)
				GROUP BY c.id
			)";
		}

		$sql = implode(' UNION ', $sql_parts);
		if ($limit) {
			$sql .= " ORDER BY date_created $order_dir LIMIT {$limit['offset']}, {$limit['max']}";
		} else {
			$sql .= " ORDER BY date_created $order_dir";
		}

		$db = App::getDb();
		$results = $db->fetchAll($sql);

		return $results;
	}


	/**
	 * Count how many drafts there are for this user
	 *
	 * @return int
	 */
	public function getDraftsCount($mine = true)
	{
		$types = array(
			'articles'    => array('content_type' => 'articles',  'entity' => 'DeskPRO:Article',  'id_field' => 'article_id',  'rev_table' => 'article_revisions'),
			'downloads'   => array('content_type' => 'downloads', 'entity' => 'DeskPRO:Download', 'id_field' => 'download_id', 'rev_table' => 'download_revisions'),
			'news'        => array('content_type' => 'news',      'entity' => 'DeskPRO:News',     'id_field' => 'news_id',     'rev_table' => 'news_revisions'),
			'feedback'       => array('content_type' => 'feedback',     'entity' => 'DeskPRO:Feedback',     'id_field' => 'feedback_id',     'rev_table' => 'feedback_revisions'),
		);

		$sql_parts = array();
		foreach ($this->enabled_types as $t) {
			$t_info = $types[$t];
			$person_sql = '';

			if($mine) {
				$person_sql = " AND c.person_id = {$this->person_context['id']}";
			}

			$sql_parts[] = "(
				SELECT COUNT(DISTINCT c.id)
				FROM $t c
				LEFT JOIN {$t_info['rev_table']} r ON (r.{$t_info['id_field']} = c.id)
				WHERE (c.status = 'hidden' AND c.hidden_status = 'draft' $person_sql) OR (r.status = 'draft' $person_sql)
			) AS count_$t";
		}

		$sql =  "SELECT " . implode(', ', $sql_parts);

		$db = App::getDb();
		$results = $db->fetchAssoc($sql);

		return array_sum($results);
	}


	############################################################################

	/**
	 * @param int $limit
	 * @return array
	 */
	public function getContentFromInfo($results)
	{
		$types = array(
			'articles'    => array('content_type' => 'articles',  'entity' => 'DeskPRO:Article',  'id_field' => 'article_id',  'rev_table' => 'article_revisions'),
			'downloads'   => array('content_type' => 'downloads', 'entity' => 'DeskPRO:Download', 'id_field' => 'download_id', 'rev_table' => 'download_revisions'),
			'news'        => array('content_type' => 'news',      'entity' => 'DeskPRO:News',     'id_field' => 'news_id',     'rev_table' => 'news_revisions'),
			'feedback'       => array('content_type' => 'feedback',     'entity' => 'DeskPRO:Feedback',     'id_field' => 'feedback_id',     'rev_table' => 'feedback_revisions'),
		);

		#------------------------------
		# Fetch each comment in the result
		#------------------------------

		$result_ids_typed = array();

		foreach ($results as $r) {
			if (!isset($result_ids_typed[$r['content_type']])) {
				$result_ids_typed[$r['content_type']] = array();
			}

			$result_ids_typed[$r['content_type']][] = $r['content_id'];
		}

		$results_typed = array();

		foreach ($result_ids_typed as $t => $ids) {
			$t_info = $types[$t];
			$results_typed[$t] = App::getEntityRepository($t_info['entity'])->getByIds($ids);
		}

		#------------------------------
		# Put back into original sort order
		# as a combined array
		#------------------------------

		$results_ordered = array();

		foreach ($results as $r) {
			if (isset($results_typed[$r['content_type']][$r['content_id']])) {
				$results_ordered[] = array(
					'info' => $r,
					'obj'  => $results_typed[$r['content_type']][$r['content_id']]
				);
			}
		}

		return $results_ordered;
	}


	/**
	 * Get the content entity for a publish type
	 *
	 * @throws \InvalidArgumentException
	 * @param $type
	 * @return string
	 */
	public function getEntityNameFor($type)
	{
		switch ($type) {
			case self::ARTICLES:
				return 'DeskPRO:Article';
				break;
			case self::DOWNLOADS:
				return 'DeskPRO:Download';
				break;
			case self::NEWS:
				return 'DeskPRO:News';
				break;
			case self::FEEDBACK:
				return 'DeskPRO:Feedback';
				break;
		}

		throw new \InvalidArgumentException("Unknown type `$type`");
	}


	/**
	 * Get the category entity for a publish type
	 *
	 * @throws \InvalidArgumentException
	 * @param $type
	 * @return string
	 */
	public static function getCatEntityNameFor($type)
	{
		switch ($type) {
			case self::ARTICLES:
				return 'DeskPRO:ArticleCategory';
				break;
			case self::DOWNLOADS:
				return 'DeskPRO:DownloadCategory';
				break;
			case self::NEWS:
				return 'DeskPRO:NewsCategory';
				break;
			case self::FEEDBACK:
				return 'DeskPRO:FeedbackCategory';
				break;
		}

		throw new \InvalidArgumentException("Unknown type `$type`");
	}
}
