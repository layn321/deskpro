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

use Application\DeskPRO\Searcher\DownloadSearch;
use Application\DeskPRO\UI\RuleBuilder;
use Application\DeskPRO\Entity\ResultCache;
use Application\DeskPRO\Entity\Download;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;

class DownloadResults
{
	/**
	 * @var Application\AgentBundle\Controller\AbstractController
	 */
	protected $controller;

	/**
	 * @var array
	 */
	protected $download_ids = array();

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
	 * @return \Application\AgentBundle\Controller\Helper\DownloadResults
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

			if (isset($options['category'])) {
				$terms = array(
					array('type' => 'category_specific', 'op' => 'is', 'options' => array('category' => $options['category']['id'])),
					array('type' => 'agent_list', 'op' => 'is', 'options' => 1),
				);

			} elseif (isset($options['show_all'])) {
				$terms = array(
					array('type' => 'agent_list', 'op' => 'is', 'options' => 1),
				);

			} else {
				$form_terms = $controller->in->getCleanValueArray('terms', 'raw' , 'string');
				$form_terms = Arrays::removeFalsey($form_terms);

				$terms = $term_rules->readForm($form_terms);
			}

			$searcher = new DownloadSearch();
			foreach ($terms as $term) {
				$searcher->addTerm($term['type'], $term['op'], $term['options']);
			}

			$order_by = $controller->in->getString('order_by');
			if (!$order_by) {
				$order_by = $controller->person->getPref('agent.ui.download-filter-order-by.0');
			}

			if ($order_by) {
				$searcher->setOrderByCode($order_by);
			} elseif (!empty($options['default_order_by'])) {
				$searcher->setOrderByCode($options['default_order_by']);
			} else {
				$order_by = 'downloads.date_created:desc';
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
	 * @return \Application\AgentBundle\Controller\Helper\DownloadResults
	 */
	public static function newFromResultCache($controller, ResultCache $result_cache)
	{
		$helper = new self($controller);
		$helper->setDownloadIds($result_cache['results']);

		return $helper;
	}


	public function __construct($controller, ResultCache $result_cache = null)
	{
		$this->controller = $controller;

		if ($result_cache) {
			$this->result_cache = $result_cache;
			$this->setDownloadIds($result_cache['results']);
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
	 * @param array $download_ids
	 */
	public function setDownloadIds(array $download_ids)
	{
		$this->download_ids = $download_ids;
	}


	/**
	 * @return array
	 */
	public function getDownloadIds()
	{
		return $this->download_ids;
	}


	/**
	 * @return array
	 */
	public function getDownloadsForPage($page, $per_page = 50)
	{
		return $this->_getPageFromDownloadIds($this->getDownloadIds(), $page, $per_page);
	}

	public function getForPage($page, $per_page = 50)
	{
		return $this->_getPageFromDownloadIds($this->getDownloadIds(), $page, $per_page);
	}


	protected function _getPageFromDownloadIds(array $download_ids, $page, $per_page)
	{
		$page_download_ids = Arrays::getPageChunk($download_ids, $page, $per_page);
		$downloads_raw = App::getEntityRepository('DeskPRO:Download')->getByResultIds($page_download_ids);

		$downloads = array();
		foreach ($download_ids as $tid) {
			if (isset($downloads_raw[$tid])) {
				$downloads[$tid] = $downloads_raw[$tid];
			}
		}

		return $downloads;
	}
}
