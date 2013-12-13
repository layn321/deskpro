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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller\Helper;

use Application\DeskPRO\Searcher\ArticleSearch;
use Application\DeskPRO\UI\RuleBuilder;
use Application\DeskPRO\Entity\ResultCache;
use Application\DeskPRO\Entity\Article;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;

class ArticleResults
{
	/**
	 * @var Application\AgentBundle\Controller\AbstractController
	 */
	protected $controller;

	/**
	 * @var array
	 */
	protected $article_ids = array();

	/**
	 * @var array
	 */
	protected $order_by = null;

	/**
	 * @var \Application\DeskPRO\Entity\ResultCache
	 */
	protected $result_cache;

	/**
	 * $options can have:
	 * - default_terms: For when viewing the page that you havent submitted
	 * - specific_terms: Always added to the search
	 * - default_order_by: The default order by for a page you havent submitted
	 *
	 * @param  $controller
	 * @param array $options
	 * @return \Application\AgentBundle\Controller\Helper\ArticleResults
	 */
	public static function newFromRequest($controller, array $options = array())
	{
		$result_cache = false;
		if ($controller->in->getUint('cache_id')) {
			$result_cache = App::getEntityRepository('DeskPRO:ResultCache')->find($controller->in->getUint('cache_id'));
			if ($result_cache['person_id'] != $controller->person['id']) {
				$result_cache = false;
			}
		}

		#------------------------------
		# If there's no result set, we're running it for the first time
		#------------------------------

		if (!$result_cache) {
			$term_rules = RuleBuilder::newTermsBuilder();

			// A category with this action is a shortcut for searching on the category,
			// and published
			if (isset($options['category'])) {
				$terms = array(
					array('type' => 'category_specific', 'op' => 'is', 'options' => array('category' => $options['category']['id'])),
					array('type' => 'agent_list', 'op' => 'is', 'options' => 1),
				);

			} elseif (isset($options['pending_translate'])) {

				$terms = array(
					array('type' => 'status', 'op' => 'is', 'options' => array('status' => 'published')),
					array('type' => 'pending_translate', 'op' => 'id', 'options' => array(
						'language_id' => isset($options['pending_translate_lang']) ? $options['pending_translate_lang'] : 0
					))
				);

			// "all" is published but no category term
			} elseif (isset($options['show_all'])) {
				$terms = array(
					array('type' => 'agent_list', 'op' => 'is', 'options' => 1),
				);

			// Otherwise its a user filter with custom terms
			} else {
				$form_terms = $controller->in->getCleanValueArray('terms', 'raw' , 'string');
				$form_terms = Arrays::removeFalsey($form_terms);

				$terms = $term_rules->readForm($form_terms);
			}

			$searcher = new ArticleSearch();
			foreach ($terms as $term) {
				$searcher->addTerm($term['type'], $term['op'], $term['options']);
			}

			$order_by = $controller->in->getString('order_by');

			if (!$order_by) {
				$order_by = 'article.date_created:desc';
			}

			if ($order_by) {
				$searcher->setOrderByCode($order_by);
			}

			$results = $searcher->getMatches();

			$result_cache = new ResultCache();
			$result_cache['person'] = $controller->person;
			$result_cache['criteria'] = array('terms' => $searcher->getTerms(), 'order_by' => $order_by);
			$result_cache['extra'] = array('summary' => $searcher->getSummary());
			$result_cache['results'] = $results;
			$result_cache['num_results'] = count($results);

			$controller->em->persist($result_cache);
			$controller->em->flush();
		}

		return new self($controller, $result_cache);
	}

	/**
	 * @return \Application\AgentBundle\Controller\Helper\ArticleResults
	 */
	public static function newFromResultCache($controller, ResultCache $result_cache)
	{
		$helper = new self($controller);
		$helper->setArticleIds($result_cache['results']);

		return $helper;
	}


	public function __construct($controller, ResultCache $result_cache = null)
	{
		$this->controller = $controller;

		if ($result_cache) {
			$this->result_cache = $result_cache;
			$this->setArticleIds($result_cache['results']);
		}
	}


	/**
	 * @return \Application\DeskPRO\Entity\ResultCache
	 */
	public function getResultCache()
	{
		return $this->result_cache;
	}


	/**
	 * @param array $article_ids
	 */
	public function setArticleIds(array $article_ids)
	{
		$this->article_ids = $article_ids;
	}


	/**
	 * @return array
	 */
	public function getArticleIds()
	{
		return $this->article_ids;
	}


	/**
	 * @return array
	 */
	public function getArticlesForPage($page, $per_page = 50)
	{
		return $this->_getPageFromArticleIds($this->getArticleIds(), $page, $per_page);
	}

	public function getForPage($page, $per_page = 50)
	{
		return $this->_getPageFromArticleIds($this->getArticleIds(), $page, $per_page);
	}


	protected function _getPageFromArticleIds(array $article_ids, $page, $per_page)
	{
		$page_article_ids = Arrays::getPageChunk($article_ids, $page, $per_page);
		$articles_raw = App::getEntityRepository('DeskPRO:Article')->getByResultIds($page_article_ids);

		// - We'll get a page of results, but that actual page isn't going to be
		// sorted the way we want, because MySQL was just sent a list of ID's.
		// - So we'll re-create the array here according to the order they're supposed to be in.
		$articles = array();
		foreach ($article_ids as $tid) {
			if (isset($articles_raw[$tid])) {
				$articles[$tid] = $articles_raw[$tid];
			}
		}

		return $articles;
	}
}
