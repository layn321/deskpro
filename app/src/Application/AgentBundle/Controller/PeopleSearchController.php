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

use Application\DeskPRO\BigMode;
use Application\DeskPRO\Searcher\TicketSearch;
use Application\DeskPRO\Entity\TicketFilter;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;

use Application\AgentBundle\Controller\Helper\PeopleResults;

use Application\DeskPRO\UI\RuleBuilder;
use Orb\Validator\StringEmail;

/**
 * Handles searching for people
 */
class PeopleSearchController extends AbstractController
{
	public function getSectionDataAction()
	{
		$data = array();

		#------------------------------
		# People labels
		#------------------------------

		$people_count = $this->settings->get('core_tablecounts.people');
		if ($people_count < 10000) {
			$people_count = $this->em->getRepository('DeskPRO:Person')->getCount(true);
		}
		$validating_count = $this->em->getRepository('DeskPRO:Person')->getValidatingCount();
		$validating_count_agent = $this->em->getRepository('DeskPRO:Person')->getAgentValidatingCount();

		$label_counts = $this->em->getRepository('DeskPRO:LabelDef')->getLabelCounts('people', 25);
		$cloud_gen = new \Application\DeskPRO\UI\TagCloud($label_counts);
		$people_tag_cloud = $cloud_gen->getCloud();

		$label_lister = new \Application\DeskPRO\Labels\LabelLister('people');
		$people_tag_index = $label_lister->getIndexList();

		#------------------------------
		# Agents and teams
		#------------------------------

		$team_names  = $this->em->getRepository('DeskPRO:AgentTeam')->getTeamNames();
		$team_counts = $this->em->getRepository('DeskPRO:AgentTeam')->getTeamCounts();
		$agent_count = count($this->em->getRepository('DeskPRO:Person')->getAgents());

		#------------------------------
		# Org labels
		#------------------------------

		$org_count = $this->em->getRepository('DeskPRO:Organization')->getCount();

		$label_counts = $this->em->getRepository('DeskPRO:LabelDef')->getLabelCounts('organizations', 25);
		$cloud_gen = new \Application\DeskPRO\UI\TagCloud($label_counts);
		$org_tag_cloud = $cloud_gen->getCloud();

		$label_lister = new \Application\DeskPRO\Labels\LabelLister('organizations');
		$org_tag_index = $label_lister->getIndexList();

		$usergroup_names      = $this->em->getRepository('DeskPRO:Usergroup')->getUsergroupNames();
		$usergroup_counts     = $this->em->getRepository('DeskPRO:Usergroup')->getCountsFor(array_keys($usergroup_names));

		$data['section_html'] = $this->renderView('AgentBundle:PeopleSearch:window-section.html.twig', array(
			'usergroup_names'      => $usergroup_names,
			'usergroup_counts'     => $usergroup_counts,

			'team_names'   => $team_names,
			'team_counts'  => $team_counts,
			'agent_count'  => $agent_count,

			'people_count'     => $people_count,
			'validating_count' => $validating_count,
			'validating_count_agent' => $validating_count_agent,
			'people_tag_cloud' => $people_tag_cloud,
			'people_tag_index' => $people_tag_index,
			'org_tag_cloud'    => $org_tag_cloud,
			'org_tag_index'    => $org_tag_index,
			'org_count'        => $org_count
		));

		return $this->createJsonResponse($data);
	}

	public function reloadCountsAction()
	{
		$people_count = $this->settings->get('core_tablecounts.people');
		if ($people_count < 10000) {
			$people_count = $this->em->getRepository('DeskPRO:Person')->getCount(true);
		}

		$data = array(
			'people_count'           => $people_count,
			'usergroup_counts'       => $this->em->getRepository('DeskPRO:Usergroup')->getCountsForAll(),
			'validating_count'       => $this->em->getRepository('DeskPRO:Person')->getValidatingCount(),
			'validating_count_agent' => $this->em->getRepository('DeskPRO:Person')->getAgentValidatingCount()
		);

		return $this->createJsonResponse($data);
	}

	public function reloadLabelDataAction()
	{
		// People
		$label_counts = $this->em->getRepository('DeskPRO:LabelDef')->getLabelCounts('people', 25);
		$cloud_gen = new \Application\DeskPRO\UI\TagCloud($label_counts);
		$people_tag_cloud = $cloud_gen->getCloud();

		$label_lister = new \Application\DeskPRO\Labels\LabelLister('people');
		$people_tag_index = $label_lister->getIndexList();

		// Orgs
		$label_counts = $this->em->getRepository('DeskPRO:LabelDef')->getLabelCounts('organizations', 25);
		$cloud_gen = new \Application\DeskPRO\UI\TagCloud($label_counts);
		$org_tag_cloud = $cloud_gen->getCloud();

		$label_lister = new \Application\DeskPRO\Labels\LabelLister('organizations');
		$org_tag_index = $label_lister->getIndexList();

		$data = array();
		$data['people_label_cloud'] = $this->renderView('AgentBundle:PeopleSearch:window-people-label-cloud.html.twig', array('people_tag_cloud' => $people_tag_cloud));
		$data['people_label_list'] = $this->renderView('AgentBundle:PeopleSearch:window-people-label-list.html.twig', array('people_tag_index' => $people_tag_index));
		$data['org_label_cloud'] = $this->renderView('AgentBundle:PeopleSearch:window-org-label-cloud.html.twig', array('org_tag_cloud' => $org_tag_cloud));
		$data['org_label_list'] = $this->renderView('AgentBundle:PeopleSearch:window-org-label-list.html.twig', array('org_tag_index' => $org_tag_index));

		return $this->createJsonResponse($data);
	}

	protected function _getResponseForPeople($type, $type_id, PeopleResults $results_helper, array $vars = array())
	{
		$view_type = $this->in->getString('view_type');
		if (!$view_type OR !in_array($view_type, array('list', 'simple'))) {
			$view_type = 'simple';
		}

		$is_partial = false;
		$tpl = 'AgentBundle:PeopleSearch:'.$type . ($view_type != 'simple' ? '-'.$view_type : '') .'.html.twig';
		if ($this->in->getBool('partial')) {
			$is_partial = true;
			$tpl = 'AgentBundle:PeopleSearch:' . $type . '-page' . ($view_type != 'simple' ? '-'.$view_type : '') . '.html.twig';
		}

		#------------------------------
		# Get the tickets to show
		#------------------------------

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$people = $results_helper->getPeopleForPage($page);


		#------------------------------
		# Send results
		#------------------------------

		if (!count($people) && $is_partial) {
			return $this->createJsonResponse(array('no_more_results' => true));
		}

		$vars['display_fields'] = Arrays::removeFalsey($vars['display_fields']);
		$vars['display_fields'] = array_unique($vars['display_fields']);

		// person defs for columns
		$user_field_manager = $this->container->getSystemService('person_fields_manager');
		$person_field_defs = $user_field_manager->getFields();

		$result_display = new \Application\DeskPRO\People\PeopleResultsDisplay($people);

		$alphabet = $this->getAlphabet();
		$letters = array();

		$params = $_GET;
		$params['letter'] = '*';
		$letters[] = array('title'=>'*', 'params' => $params);
		$params['letter'] = '#';
		$letters[] = array('title'=>'#', 'params' => $params);

		foreach($alphabet as $letter)
		{
			$params['letter'] = $letter;
			$letters[] = array(
				'title' => $letter,
				'params' => $params
			);
		}

		$vars = array_merge($vars, array(
			'type'                    => $type,
			'type_id'                 => $type_id,
			'people'                  => $people,
			'page'                    => $page,
			'person_field_defs'       => $person_field_defs,
			'load_first'              => $this->in->getBool('load_first'),
			'result_display'          => $result_display,
			'alphabet'                => $letters
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
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function getPeoplePageAction()
	{
		$person_ids = $this->in->getCleanValueArray('result_ids', 'uint', 'discard');
		$person_ids = Arrays::removeFalsey($person_ids);
		$person_ids = array_unique($person_ids);

		$people = $this->em->getRepository('DeskPRO:Person')->getPeopleResultsFromIds($person_ids);
		$people = Arrays::orderIdArray($person_ids, $people);

		$display_fields = $this->in->getCleanValueArray('display_fields', 'string', 'discard');
		$display_fields = Arrays::removeFalsey($display_fields);
		$display_fields = array_unique($display_fields);

		$user_field_manager = $this->container->getSystemService('person_fields_manager');
		$person_field_defs = $user_field_manager->getFields();

		$tpl = 'list-page.html.twig';
		if ($this->in->getString('view_type') == 'list') {
			$tpl = 'list-list-page.html.twig';
		}

		$result_display = new \Application\DeskPRO\People\PeopleResultsDisplay($people);
		return $this->render("AgentBundle:PeopleSearch:$tpl", array(
			'people'                  => $people,
			'display_fields'          => $display_fields,
			'person_field_defs'       => $person_field_defs,
			'result_display'          => $result_display,
		));
	}


	############################################################################
	# search
	############################################################################

	public function searchAction($letter, $use_terms = null, $set_view_name = null)
	{
		$result_cache = false;
		if ($this->in->getUint('cache_id')) {
			$result_cache = $this->em->getRepository('DeskPRO:ResultCache')->find($this->in->getUint('cache_id'));
			if (!$result_cache OR $result_cache['person_id'] != $this->person['id']) {
				$result_cache = false;
			}
		}

		$user_letter = $this->getLetterFromUser($letter);

		#------------------------------
		# If there's no result set, we're running it for the first time
		#------------------------------

		if (!$result_cache || $user_letter != $result_cache['criteria']['selected_letter']) {

			$old_result_cache = false;
			if ($this->in->getUint('copy_display_options')) {
				$old_result_cache = $this->em->getRepository('DeskPRO:ResultCache')->find($this->in->getUint('copy_display_options'));
				if (!$old_result_cache OR $old_result_cache['person_id'] != $this->person['id']) {
					$old_result_cache = false;
				}
			}

			$term_rules = RuleBuilder::newTermsBuilder();
			$terms = $term_rules->readForm($this->in->getCleanValueArray('terms', 'raw' , 'discard'));

			$set_terms_map = array(
				'person_organization'       => array('op' => 'contains', 'options' => array()),
				'person_usergroup'          => array('op' => 'contains', 'options' => array()),
				'person_label'              => array('op' => 'contains', 'options' => array()),
				'person_name'               => array('op' => 'contains', 'options' => array()),
				'person_email'              => array('op' => 'contains', 'options' => array()),
				'person_contact_phone'      => array('op' => 'contains', 'options' => array()),
				'is_agent_confirmed'        => array('op' => 'is', 'options' => array()),
				'is_confirmed'              => array('op' => 'is', 'options' => array()),
			);

			foreach ($set_terms_map as $name => $info) {
				$in_val = $this->container->getIn()->getCleanValue('set_term.'.$name, 'raw');
				if (!$in_val && $use_terms && isset($use_terms[$name])) {
					$in_val = $use_terms[$name];
				}
				if (is_string($in_val)) {
					$in_val = trim($in_val);
				} elseif (is_array($in_val)) {
					$in_val = Arrays::removeEmptyString($in_val);
				}
				if ($in_val || isset($use_terms[$name])) {
					$new_term = $info;
					$new_term['options'] = $in_val;
					Arrays::unshiftAssoc($new_term, 'type', $name);
					$terms[] = $new_term;
				}
			}

			$set_custom_fields = $this->container->getIn()->getCleanValueArray('set_custom_field', 'raw', 'string');

			foreach ($set_custom_fields as $field_name => $field_value) {
				if (is_array($field_value)) {
					$field_value = Arrays::removeFalsey($field_value);
				}

				if (!$field_value) {
					continue;
				}

				$id = Strings::extractRegexMatch('#field_([0-9]+)#', $field_name, 1);
				if (!$id) {
					continue;
				}

				$new_term = array(
					'type' => "person_field[$id]",
					'op' => 'is',
					'options' => array('value' => $field_value)
				);

				$terms[] = $new_term;
			}

			$searcher = new \Application\DeskPRO\Searcher\PersonSearch();

			$selected_letter = $this->applyLetterToSearcher($user_letter, $searcher);

			if ($search_val = $this->in->getString('person_name')) {
				$searcher->addTerm('person_name', 'contains', $search_val);
			}
			if ($search_val = $this->in->getString('person_contact_phone')) {
				$searcher->addTerm('person_contact_phone', 'contains', $search_val);
			}
			if ($search_val = $this->in->getString('person_email')) {
				$searcher->addTerm('person_email', 'contains', $search_val);
			}
			if ($search_val = $this->in->getString('person_organization_name')) {
				$searcher->addTerm('person_organization_name', 'contains', $search_val);
			}
			if ($search_val = $this->in->getString('person_ip')) {
				$searcher->addTerm('person_ip', 'contains', $search_val);
			}
			if ($search_val = $this->in->getString('person_label')) {
				$search_val = explode(',', $search_val);
				$search_val = Arrays::func($search_val, 'trim');
				$search_val = Arrays::removeFalsey($search_val);

				if ($search_val) {
					$searcher->addTerm('person_label', 'contains', $search_val);
				}
			}
			if ($search_val = $this->in->getCleanValueArray('person_usergroup', 'uint', 'discard')) {
				$search_val = Arrays::removeFalsey($search_val);

				if ($search_val) {
					$searcher->addTerm('person_usergroup', 'is', $search_val);
				}
			}

			foreach ($terms as $term) {
				$searcher->addTerm($term['type'], $term['op'], $term['options']);
			}

			$order_by = $this->person->getPref('agent.ui.people-filter-order-by.0');

			if (!$order_by) {
				$order_by = 'people.id:asc';
			}

			if ($order_by) {
				$searcher->setOrderByCode($order_by);
			}

			$results = $searcher->getMatches();

			$result_cache = new Entity\ResultCache();
			$result_cache['person'] = $this->person;
			$result_cache['criteria'] = array('terms' => $searcher->getTerms(), 'order_by' => $order_by, 'selected_letter' => $selected_letter);
			$result_cache['results'] = $results;
			$result_cache['num_results'] = count($results);
			$result_cache->setExtraData('terms_summary', $searcher->getSummary());

			if ($old_result_cache) {
				$result_cache['extra'] = $old_result_cache['extra'];
			}

			$this->em->persist($result_cache);
			$this->em->flush();
		}

		#------------------------------
		# Re-do search if we changed order
		#------------------------------

		// Prefs are saved into extra[]. Of order_by doesn't match
		// the order_by in criteria, that means the user changed it
		// and we have to re-do the search

		$order_pref = $this->person->getPref('agent.ui.people-filter-order-by.' . 0);

		if (($order_pref && $order_pref != $result_cache['criteria']['order_by'])
		||  $user_letter != $result_cache['criteria']['selected_letter']) {
			$searcher = new \Application\DeskPRO\Searcher\PersonSearch();

			$criteria = $result_cache['criteria'];
			$criteria['order_by'] = $order_pref;
			$criteria['selected_letter'] = $this->applyLetterToSearcher($user_letter, $searcher);

			$result_cache['criteria'] = $criteria;

			$searcher->setTerms($result_cache['criteria']['terms']);
			$searcher->setOrderByCode($result_cache['criteria']['order_by']);

			$results = $searcher->getMatches();
			$result_cache['results'] = $results;
			$result_cache['num_results'] = count($results);
			$result_cache->setExtraData('terms_summary', $searcher->getSummary());

			$this->em->persist($result_cache);
			$this->em->flush();
		}

		#------------------------------
		# Serve results
		#------------------------------

		$results_helper = Helper\PeopleResults::newFromResultCache($this, $result_cache);

		$vars = array(
			'cache' => $result_cache,
			'cache_id' => $result_cache['id'],
			'person_ids' => $result_cache['results'],
			'terms_summary' => $result_cache->getExtraData('terms_summary'),
			'selected_letter' => $result_cache['criteria']['selected_letter']
		);

		if (!empty($result_cache['extra']['display_fields'])) {
			$vars['display_fields'] = $result_cache['extra']['display_fields'];
		}

		$pref_display_fields = $this->person->getPref('agent.ui.people-filter-display-fields.' . $result_cache['id']);
		if ($pref_display_fields) {
			$vars['display_fields'] = $pref_display_fields;
		} else {
			$pref_display_fields = $this->person->getPref('agent.ui.people-filter-display-fields.0');
			$vars['display_fields'] = $pref_display_fields;
		}

		if ($this->in->getString('page_title')) {
			$vars['page_title'] = $this->in->getString('page_title');
		}

		if (!$vars['display_fields']) {
			$vars['display_fields'] = array('name', 'email', 'org', 'org_pos', 'num_tickets');
		}

		$vars['preselect_terms'] = $result_cache['criteria'];
		$vars['num_results'] = $result_cache['num_results'];

		// Used in the search form again
        $titles = array();
        $titles['organizations'] = $this->container->getDataService('Organization')->getOrganizationNames();
        $titles['usergroups']    = $this->container->getDataService('Usergroup')->getUsergroupNames();

		if ($this->container->getDataService('Language')->isMultiLang()) {
			$titles['languages']     = $this->container->getDataService('Language')->getTitles();
		}

		$vars['titles'] = $titles;

		if (!$set_view_name) {
			$set_view_name = $this->in->getStrSimple('view_name');
		}

		if ($set_view_name) {
			$vars['view_name'] = $set_view_name;

			if (strpos($vars['view_name'], '.') !== false) {
				list ($view_name_type, $view_name_data) = explode('.', $vars['view_name'], 2);
				$vars['view_name_type'] = $view_name_type;
				$vars['view_name_data'] = (int)$view_name_data;
			}
		}

		return $this->_getResponseForPeople('list', $result_cache['id'], $results_helper, $vars);
	}

	public function showUsergroupAction($id)
	{
		$usergroup = $this->em->find('DeskPRO:Usergroup', $id);
		if (!$usergroup || $usergroup->is_agent_group) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		return $this->searchAction('*', array('person_usergroup' => $id), 'usergroup.' . $id);
	}

	public function showOrganizationMembersAction($id)
	{
		$organization = $this->em->find('DeskPRO:Organization', $id);
		if (!$organization) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		return $this->searchAction('*', array('person_organization' => $id), 'organization.' . $id);
	}

	protected function applyLetterToSearcher($letter, $searcher)
	{
		$selected_letter = '*';

		switch ($letter) {
			case '#':
				$selected_letter = '#';
				$searcher->addTerm('alphabetical', 'contains', $this->getAlphabet(true));
			case '*':
				break;
			default:
				$selected_letter = $letter;
				$searcher->addTerm('alphabetical', 'contains', array($letter, strtolower($letter)));
				break;
		}

		return $selected_letter;
	}

	protected function getLetterFromUser($letter = null)
	{
		if($letter === null) {
			$letter = $this->in->getString('letter');
		}

		if (is_string($letter) && strlen($letter) == 1) {
			if ($letter == '#') {
				return '#';
			}
			else {
				$alphabet = $this->getAlphabet();

				if (in_array($letter, $alphabet)) {
					return $letter;
				}
			}
		}

		return '*';
	}

	protected function getAlphabet($numbers = false)
	{
		if ($numbers) {
			$numbers = array();

			for($i = 0; $i < 10; $i++)
				$numbers[] = $i;

			return $numbers;
		}

		$letters = array();

		for ($i = ord('A'); $i <= ord('Z'); $i++)
			$letters[] = chr($i);

		return $letters;
	}

	/* TODO
	public function massActionsAction($action)
	{
		$this->em->beginTransaction();

		$people = $this->em->getRepository('DeskPRO:Person')->getByIds($this->in->getCleanValueArray('ids', 'uint', 'discard'));

		$organization = null;
		$usergroup = null;

		if ($this->in->getUint('organization_id')) {
			$organization = $this->em->find('DeskPRO:Organization', $this->in->getUint('organization_id'));
		}
		if ($this->in->getUint('usergroup_id')) {
			$usergroup = $this->em->find('DeskPRO:Usergroup', $this->in->getUint('usergroup_id'));
		}


		foreach ($feedback as $feedback) {
			switch ($action) {
				case 'add-to-organization':
					if ($organization) {
						foreach ($people as $p) {
							$p->organization = $organization;
							$this->em->persist($p);
						}
					}
					break;

				case 'del-from-organization':
					foreach ($people as $p) {
						if ($p->organization) {
							$p->organization = null;
							$this->em->persist($p);
						}
					}
					break;

				case 'add-to-usergroup':
					if ($usergroup) {
						foreach ($people as $p) {
							if (!isset($p->usergroups[$usergroup->id])) {
								$p->usergroups->add($usergroup);
								$this->em->persist($p);
							}
						}
					}
					break;

				case 'del-form-usergroup':
					if ($usergroup) {
						foreach ($people as $p) {
							if (isset($p->usergroups[$usergroup->id])) {
								$p->usergroups->remove($usergroup->id);
								$this->em->persist($p);
							}
						}
					}
					break;
			}
		}

		$this->em->flush();
		$this->em->commit();

		return $this->createJsonResponse(array(
			'success' => 1
		));
	}*/

	############################################################################
	# quick-find
	############################################################################

	public function quickFindAction()
	{
		return $this->render('AgentBundle:PeopleSearch:quick-find.html.twig');
	}

	public function quickFindSearchAction()
	{
		$term_rules = \Application\DeskPRO\UI\RuleBuilder::newTermsBuilder();
		$terms = $term_rules->readForm($this->in->getCleanValueArray('terms', 'raw' , 'discard'));

		$searcher = new \Application\DeskPRO\Searcher\PersonSearch();
		foreach ($terms as $term) {
			$searcher->addTerm($term['type'], $term['op'], $term['options']);
		}

		$results = $searcher->getMatches();

		$data = array();

		if (!$results) {
			$data['no_results'] = true;
		} else {
			$data['num_results'] = count($results);

			$helper = new Helper\PeopleResults($this);
			$helper->setPeopleIds($results);

			$people = $helper->getPeopleForPage(1, 100);

			$data['html'] = $this->renderView('AgentBundle:PeopleSearch:quick-find-results.html.twig', array(
				'people' => $people,
				'page' => 1
			));
		}

		return $this->createJsonResponse($data);
	}

	############################################################################
	# /agent/people-search/quick-search            agent_peoplesearch_performquick
	############################################################################

	public function performQuickSearchAction()
	{
		$q = $this->in->getString('q');
		if (!$q) {
			$q = $this->in->getString('term');
		}

		$agent_sql = ' p.is_agent = 0 AND ';
		if ($this->in->getBool('with_agents')) {
			$agent_sql = '';
		}

		$limit = $this->in->getUint('limit');
		if (!$limit) $limit = 10;
		$limit = min($limit, 100);

		$not_in_org = $this->in->getUint('exclude_org');

		if (BigMode::isBigMode(BigMode::PERSON_AUTOCOMPLETE)) {
			if (!$q && $this->in->getBool('start_with')) {
				$people_list = $this->db->fetchAllKeyed("
					SELECT p.id, p.first_name, p.last_name, e.email
					FROM people p
					LEFT JOIN people_emails e ON (e.person_id = p.id)
					WHERE $agent_sql
					" . ($not_in_org ? " p.organization_id != $not_in_org " : '1') . "
					ORDER BY p.id DESC
					LIMIT $limit
				");
			} else {
				$people_list = $this->db->fetchAllKeyed("
					SELECT p.id, p.first_name, p.last_name, e.email
					FROM people p
					LEFT JOIN people_emails e ON (e.person_id = p.id)
					WHERE
						$agent_sql
						e.email LIKE ?
						" . ($not_in_org ? " AND (p.organization_id IS NULL OR p.organization_id != $not_in_org) " : '') . "
					GROUP BY p.id
					ORDER BY p.date_last_login DESC, p.id DESC
					LIMIT $limit
				", array("$q%"));
			}
		} else {
			if (!$q && $this->in->getBool('start_with')) {
				$people_list = $this->db->fetchAllKeyed("
					SELECT p.id, p.first_name, p.last_name, e.email
					FROM people p
					LEFT JOIN people_emails e ON (e.person_id = p.id)
					WHERE $agent_sql
					" . ($not_in_org ? " p.organization_id != $not_in_org " : '1') . "
					ORDER BY p.name ASC
					LIMIT $limit
				");
			} else {

				$people_list = $this->db->fetchAllKeyed("
					SELECT p.id, p.first_name, p.last_name, e.email
					FROM people p
					LEFT JOIN people_emails e ON (e.person_id = p.id)
					WHERE
						$agent_sql
						(e.email LIKE ?
						OR p.name LIKE ?
						OR p.first_name LIKE ?
						OR p.last_name LIKE ?)
						" . ($not_in_org ? " AND (p.organization_id IS NULL OR p.organization_id != $not_in_org) " : '') . "
					GROUP BY p.id
					ORDER BY p.date_last_login DESC, p.id DESC
					LIMIT $limit
				", array("%$q%", "%$q%", "%$q%", "%$q%"));
			}
		}

		$format = $this->in->getString('format');

		if ($format == 'json' OR (!$format AND $this->in->getBool('ajax'))) {
			$tpl = "AgentBundle:PeopleSearch:search_results.json.jsonphp";
		} else {
			$tpl = "AgentBundle:PeopleSearch:search_results.html.twig";
			if ($format == 'simplelist') {
				$tpl = "AgentBundle:PeopleSearch:search-results-simplelist.html.twig";
			}
		}

		// If the string is an exact email, we can try and find the user in usersources as well
		if (StringEmail::isValueValid($q)) {
			$person = $this->container->getSystemService('UsersourceManager')->findPersonByEmail($q);
			if ($person && !isset($people_list[$person->getId()])) {
				$people_list[$person->getId()] = array(
					'id'         => $person->getId(),
					'first_name' => $person->first_name,
					'last_name'  => $person->last_name,
					'email'      => $person->getPrimaryEmailAddress()
				);
			}
		}

		return $this->render($tpl, array(
			'people_list' => $people_list
		));
	}

	############################################################################
	# validate-lists
	############################################################################

	public function validateListAction()
	{
		if ($this->in->getString('email_validating')) {
			return $this->searchAction(null, array(
				'is_confirmed' => 0
			), 'awaiting_validation');
		} else {
			return $this->searchAction(null, array(
				'is_agent_confirmed' => 0
			), 'awaiting_validation');
		}
	}

	public function validateApproveAction()
	{
		$people_ids = $this->in->getCleanValueArray('people_ids', 'uint', 'discard');
		$people = $this->em->getRepository('DeskPRO:Person')->getByIds($people_ids);

		$email_ids = array();

		$this->db->beginTransaction();
		try {
			$ids = array();
			foreach ($people as $person) {
				if (!$person->is_agent) {
					$ids[] = $person->getId();
					$person->is_agent_confirmed = true;
					$person->is_confirmed = true;
					$this->em->persist($person);

					if ($person->primary_email) {
						$email_ids[] = $person->primary_email->getId();
					}

					// Make visible any content now
					$ticket_ids = $this->db->fetchAllCol("
						SELECT id FROM tickets
						WHERE person_id = ? AND hidden_status = ?
					", array($person->getId(), 'validating'));

					foreach ($ticket_ids as $ticket_id) {
						$ticket = $this->em->find('DeskPRO:Ticket', $ticket_id);
						$ticket->setStatus('awaiting_agent');
						$this->em->persist($ticket);
					}
				}
			}

			$all_feedback = $this->container->getEm()->createQuery("
				SELECT f
				FROM DeskPRO:Feedback f
				WHERE f.person = ?0 AND f.hidden_status = 'user_validating'
			")->execute(array($person->getId()));
			foreach ($all_feedback as $feedback) {
				$feedback->setStatus('new');
				$this->em->persist($feedback);
			}

			foreach (array('ArticleComment', 'NewsComment', 'FeedbackComment', 'DownloadComment') as $rel) {
				$all_comments = $this->container->getEm()->createQuery("
					SELECT c
					FROM DeskPRO:$rel c
					WHERE c.person = ?0 AND c.status = 'user_validating'
				")->execute(array($person->getId()));
				foreach ($all_comments as $comment) {
					$comment->setStatus('visible');
					$this->em->persist($comment);
				}
			}

			if ($email_ids) {
				$email_ids = implode(',', $email_ids);
				$this->db->executeUpdate("UPDATE people_emails SET is_validated = 1 WHERE id IN ($email_ids)");
			}

			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array('confirmed_people_ids' => $ids));
	}

	public function validateDeleteAction()
	{
		$people_ids = $this->in->getCleanValueArray('people_ids', 'uint', 'discard');
		$people = $this->em->getRepository('DeskPRO:Person')->getByIds($people_ids);

		$this->db->beginTransaction();
		try {
			$ids = array();
			foreach ($people as $person) {
				if (!$person->is_agent && !$person->is_agent_confirmed) {
					$ids[] = $person->getId();

					foreach ($person->emails as $email) {
						$this->em->remove($email);
					}
					$this->em->remove($person);
				}
			}

			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array('deleted_people_ids' => $ids));
	}
}
