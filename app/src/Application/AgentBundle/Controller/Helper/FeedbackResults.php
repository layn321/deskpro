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

use Application\DeskPRO\Searcher\FeedbackSearch;
use Application\DeskPRO\UI\RuleBuilder;
use Application\DeskPRO\Entity\ResultCache;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;

class FeedbackResults
{
	/**
	 * @var \Application\AgentBundle\Controller\AbstractController
	 */
	protected $controller;

	/**
	 * @var array
	 */
	protected $feedback_ids = array();

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
	 * @return \Application\AgentBundle\Controller\Helper\FeedbackResults
	 */
	public static function newFromRequest($controller, array $options = array())
	{
		$result_cache = false;
		if ($controller->in->getUint('cache_id')) {
			$result_cache = $controller->em->getRepository('DeskPRO:ResultCache')->find($controller->in->getUint('cache_id'));
			if ($result_cache['person_id'] != $controller->person['id']) {
				$result_cache = false;
			}
		}

		#------------------------------
		# If there's no result set, we're running it for the first time
		#------------------------------

		if (!$result_cache) {
			$term_rules = RuleBuilder::newTermsBuilder();

			$form_terms = $controller->in->getCleanValueArray('terms', 'raw' , 'string');
			$form_terms = Arrays::removeFalsey($form_terms);

			if (!$form_terms AND !empty($options['default_terms'])) {
				$form_terms = $options['default_terms'];
			}

			if (!empty($options['specific_terms'])) {
				$form_terms = array_merge($form_terms, $options['specific_terms']);
			}

			$terms = $term_rules->readForm($form_terms);

			$searcher = new FeedbackSearch();
			foreach ($terms as $term) {
				$searcher->addTerm($term['type'], $term['op'], $term['options']);
			}

			$order_by = $controller->in->getString('order_by');
			if (!$order_by) {
				$order_by = $controller->person->getPref('agent.ui.feedback-filter-order-by.0');
			}

			if ($order_by) {
				$searcher->setOrderByCode($order_by);
			} elseif (!empty($options['default_order_by'])) {
				$searcher->setOrderByCode($options['default_order_by']);
			} else {
				$order_by = 'id:desc';
			}

			$results = $searcher->getMatches();

			$result_cache = new ResultCache();
			$result_cache['person'] = $controller->person;
			$result_cache['criteria'] = array('terms' => $searcher->getTerms(), 'order_by' => $order_by);
			$result_cache['results'] = $results;
			$result_cache['num_results'] = count($results);

			/*
			 * Usually search forms terms are keyed arbitrarily (usually numerically).
			 * The keys are discarded when read in by the RuleBuilder class above.
			 * But in the FeedbackController and template, we set specific keys
			 * for terms so the values can be easily plugged back into the form.
			 *
			 * (See FeedbackController setting 'specific_terms', and the 'filter-searhc-form' template)
			 *
			 * Usually search forms are made with the RuleBuilder JS widget, which
			 * adds terms dynamically. But when we want a static form and just want
			 * to plug values back in, we do it this way.
			 */
			$result_cache['extra'] = array();

			$em = $controller->em;
			$em->persist($result_cache);
			$em->flush();
		}

		return new self($controller, $result_cache);
	}

	/**
	 * @return \Application\AgentBundle\Controller\Helper\FeedbackResults
	 */
	public static function newFromResultCache($controller, ResultCache $result_cache)
	{
		$helper = new self($controller);
		$helper->setFeedbackIds($result_cache['results']);

		return $helper;
	}

	public function __construct($controller, ResultCache $result_cache = null)
	{
		$this->controller = $controller;

		if ($result_cache) {
			$this->result_cache = $result_cache;
			$this->setFeedbackIds($result_cache['results']);
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
	 * Set ticket IDs for the search results
	 * @param array $feedback_ids
	 */
	public function setFeedbackIds(array $feedback_ids)
	{
		$this->feedback_ids = $feedback_ids;
	}


	/**
	 * Get ticket IDs
	 *
	 * @return array
	 */
	public function getFeedbackIds()
	{
		return $this->feedback_ids;
	}


	/**
	 * Get tickets for a particular page
	 *
	 * @return array
	 */
	public function getFeedbackForPage($page, $per_page = 50)
	{
		return $this->_getPageFromFeedbackIds($this->getFeedbackIds(), $page, $per_page);
	}

	public function getForPage($page, $per_page = 50)
	{
		return $this->_getPageFromFeedbackIds($this->getFeedbackIds(), $page, $per_page);
	}


	protected function _getPageFromFeedbackIds(array $feedback_ids, $page, $per_page)
	{
		$page_feedback_ids = Arrays::getPageChunk($feedback_ids, $page, $per_page);
		$feedback_raw = $this->controller->em->getRepository('DeskPRO:Feedback')->getByIds($page_feedback_ids);

		// - We'll get a page of results, but that actual page isn't going to be
		// sorted the way we want, because MySQL was just sent a list of ID's.
		// - So we'll re-create the array here according to the order they're supposed to be in.
		$feedback = array();
		foreach ($feedback_ids as $tid) {
			if (isset($feedback_raw[$tid])) {
				$feedback[$tid] = $feedback_raw[$tid];
			}
		}

		return $feedback;
	}
}
