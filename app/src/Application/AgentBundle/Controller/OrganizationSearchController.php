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

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\Searcher\OrganizationSearch;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;

use Application\DeskPRO\UI\RuleBuilder;

/**
 * Handles searching for orgs
 */
class OrganizationSearchController extends AbstractController
{
	protected function _getResponseForOrgs($type, $type_id, $results_helper, array $vars = array())
	{
		$is_partial = false;
		$tpl = 'AgentBundle:OrganizationSearch:filter.html.twig';
		if ($this->in->getBool('partial')) {
			$is_partial = true;
			$tpl = 'AgentBundle:OrganizationSearch:filter-page.html.twig';
		}

		#------------------------------
		# Get the results to show
		#------------------------------

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$organizations = $results_helper->getOrgsForPage($page);

		#------------------------------
		# Send results
		#------------------------------

		if (!count($organizations) && $is_partial) {
			return $this->createJsonResponse(array('no_more_results' => true));
		}

		// person defs for columns
		$org_field_defs = App::getApi('custom_fields.organizations')->getEnabledFields();

		$result_display = new \Application\DeskPRO\Organizations\OrgResultsDisplay($organizations);
		$vars = array_merge($vars, array(
			'type'               => $type,
			'type_id'            => $type_id,
			'organizations'      => $organizations,
			'page'               => $page,
			'org_field_defs'     => $org_field_defs,
			'result_display'     => $result_display,
		));

		$html = $this->renderView($tpl, $vars);

		if ($is_partial) {
			return $this->createJsonResponse(array(
				'html'              => $html,
				'page'              => $page,
			));
		} else {
			return $this->createResponse($html);
		}
	}

	/**
	 * Render a new pageset.
	 */
	public function getOrgPageAction()
	{
		$org_ids = $this->in->getCleanValueArray('result_ids', 'uint', 'discard');
		$org_ids = Arrays::removeFalsey($org_ids);
		$org_ids = array_unique($org_ids);

		$organizations = $this->em->getRepository('DeskPRO:Organization')->getByIds($org_ids, true);

		$pref_display_fields = $this->person->getPref('agent.ui.org-filter-display-fields.0');
		if ($pref_display_fields) {
			$vars['display_fields'] = $pref_display_fields;
		} else {
			$vars['display_fields'] = array('members_count');
		}

		$org_field_defs = App::getApi('custom_fields.organizations')->getEnabledFields();

		$tpl = 'filter-page.html.twig';
		if ($this->in->getString('view_type') == 'list') {
			$tpl = 'filter-list-page.html.twig';
		}

		$result_display = new \Application\DeskPRO\Organizations\OrgResultsDisplay($organizations);
		return $this->render("AgentBundle:OrganizationSearch:$tpl", array(
			'organizations'    => $organizations,
			'display_fields'   => $pref_display_fields,
			'org_field_defs'   => $org_field_defs,
			'result_display'   => $result_display,
		));
	}


	############################################################################
	# search
	############################################################################

	public function searchAction()
	{
		$result_cache = false;
		if ($this->in->getUint('cache_id')) {
			$result_cache = $this->em->getRepository('DeskPRO:ResultCache')->find($this->in->getUint('cache_id'));
			if ($result_cache['person_id'] != $this->person['id']) {
				$result_cache = false;
			}
		}

		$order_by = $this->person->getPref('agent.ui.org-filter-order-by.0');

		#------------------------------
		# If there's no result set, we're running it for the first time
		#------------------------------

		if (!$result_cache) {

			$term_rules = RuleBuilder::newTermsBuilder();
			$terms = $term_rules->readForm($this->in->getCleanValueArray('terms', 'raw' , 'discard'));

			$set_terms_map = array(
				'org_name'              => array('op' => 'contains', 'options' => array()),
				'org_label'             => array('op' => 'contains', 'options' => array()),
				'org_email_domain'      => array('op' => 'contains', 'options' => array()),
				'org_contact_phone'     => array('op' => 'contains', 'options' => array()),
			);
			foreach ($set_terms_map as $name => $info) {
				$in_val = $this->container->getIn()->getCleanValue('set_term.'.$name, 'raw');
				if (is_string($in_val)) {
					$in_val = trim($in_val);
				} elseif (is_array($in_val)) {
					$in_val = Arrays::removeEmptyString($in_val);
				}
				if ($in_val) {
					$new_term = $info;
					$new_term['options'] = $in_val;
					Arrays::unshiftAssoc($new_term, 'type', $name);
					$terms[] = $new_term;
				}
			}

			$searcher = new \Application\DeskPRO\Searcher\OrganizationSearch();
			foreach ($terms as $term) {
				$searcher->addTerm($term['type'], $term['op'], $term['options']);
			}

			if ($search_val = $this->in->getString('org_name')) {
				$searcher->addTerm('org_name', 'contains', $search_val);
			}
			if ($search_val = $this->in->getString('org_email_domain')) {
				$searcher->addTerm('org_email_domain', 'contains', $search_val);
			}
			if ($search_val = $this->in->getString('org_label')) {
				$search_val = explode(',', $search_val);
				$search_val = Arrays::func($search_val, 'trim');
				$search_val = Arrays::removeFalsey($search_val);

				if ($search_val) {
					$searcher->addTerm('org_label', 'contains', $search_val);
				}
			}

			if ($order_by) {
				$searcher->setOrderByCode($order_by);
			}

			$results = $searcher->getMatches();

			$result_cache = new Entity\ResultCache();
			$result_cache['person'] = $this->person;
			$result_cache['criteria'] = array('terms' => $searcher->getTerms(), 'order_by' => $order_by);
			$result_cache['results'] = $results;
			$result_cache['num_results'] = count($results);

			$this->em->persist($result_cache);
			$this->em->flush();
		}

		#------------------------------
		# Re-do search if we changed order
		#------------------------------

		// Prefs are saved into extra[]. Of order_by doesn't match
		// the order_by in criteria, that means the user changed it
		// and we have to re-do the search

		if ($order_by && (empty($result_cache['criteria']['order_by']) || $result_cache['criteria']['order_by'] != $order_by)) {
			$criteria = $result_cache['criteria'];
			$criteria['order_by'] = $order_by;

			$result_cache['criteria'] = $criteria;

			$searcher = new \Application\DeskPRO\Searcher\OrganizationSearch();
			$searcher->setTerms($result_cache['criteria']['terms']);
			$searcher->setOrderByCode($order_by);

			$results = $searcher->getMatches();
			$result_cache['results'] = $results;
			$result_cache['num_results'] = count($results);

			$this->em->persist($result_cache);
			$this->em->flush();
		}

		#------------------------------
		# Serve results
		#------------------------------

		$results_helper = Helper\OrganizationResults::newFromResultCache($this, $result_cache);

		$vars = array(
			'cache' => $result_cache,
			'cache_id' => $result_cache['id'],
			'org_ids' => $result_cache['results'],
		);

		$pref_display_fields = $this->person->getPref('agent.ui.org-filter-display-fields.0');
		if ($pref_display_fields) {
			$vars['display_fields'] = $pref_display_fields;
		} else {
			// Default display fields based on the filter
			$vars['display_fields'] = array('members_count');
		}

		if ($this->in->getString('page_title')) {
			$vars['page_title'] = $this->in->getString('page_title');
		}

		return $this->_getResponseForOrgs('custom-filter', $result_cache['id'], $results_helper, $vars);
	}

	public function performQuickNameSearchAction()
	{
		$limit = $this->in->getUint('limit');
		if (!$limit) $limit = 20;

		$q = $this->in->getString('q');
		if (!$q) {
			$q = $this->in->getString('term');
		}

		$ids = $this->in->getCleanValueArray('ids', 'uint');

		if ($ids) {
			$orgs_list = $this->em->createQuery("
				SELECT o
				FROM DeskPRO:Organization o
				WHERE o.id IN (?0)
				ORDER BY o.name ASC
			")->execute(array($ids));
		} else if ($q) {
			$orgs_list = $this->em->createQuery("
				SELECT o
				FROM DeskPRO:Organization o
				WHERE o.name LIKE ?1
				ORDER BY o.name ASC
			")->setParameter(1, "%$q%")->setMaxResults($limit)->getResult();
		} else {
			$orgs_list = $this->em->createQuery("
				SELECT o
				FROM DeskPRO:Organization o
				ORDER BY o.name ASC
			")->setMaxResults($limit)->getResult();
		}

		$json = array('results' => array(), 'exact' => false);

		foreach ($orgs_list as $org) {
			$json['results'][] = array(
				'id' => $org['id'],
				'name' => $org['name'],
				'value' => $org['name'],
				'label' => $org['name']
			);
		}

		$org = $this->em->createQuery("
			SELECT o
			FROM DeskPRO:Organization o
			WHERE o.name = ?1
		")->setParameter(1, $this->in->getString('term'))
		  ->setMaxResults(1)
		  ->getOneOrNullResult();

		if ($org) {
			$json['exact'] = $org->id;
		}

		if ($this->in->getString('format') == 'json') {
			$json = $json['results'];
		}

		return $this->createJsonResponse($json);
	}

	public function checkNameAction()
	{
		$org = $this->em->createQuery("
			SELECT o
			FROM DeskPRO:Organization o
			WHERE o.name = ?1
		")->setParameter(1, $this->in->getString('name'))
		  ->setMaxResults(1)
		  ->getOneOrNullResult();

		if ($org) {
			return $this->createJsonResponse(array('organization_id' => $org->id));
		} else {
			return $this->createJsonResponse(array('invalid' => true));
		}
	}
}
