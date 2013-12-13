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

use Application\DeskPRO\Searcher\TicketSearch;
use Application\DeskPRO\Entity\TicketFilter;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\ClientMessage;
use Application\DeskPRO\Entity;
use Application\DeskPRO\App;

use Application\DeskPRO\Tickets\TicketActions\ActionsFactory;
use Application\DeskPRO\Tickets\TicketActions\ActionsCollection;

use Application\DeskPRO\UI\RuleBuilder;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

/**
 * Handles ticket searches
 */
class TicketSearchController extends AbstractController
{
	public function getSectionDataAction()
	{
		$data = array();

		#------------------------------
		# Filters
		#------------------------------

		$filter_info      = App::getApi('tickets.filters')->getGroupedFiltersForPerson($this->person);
		$all_filters      = $filter_info['all_filters'];
		$sys_filters      = $filter_info['sys_filters'];
		$sys_filters_hold = $filter_info['sys_filters_hold'];
		$archive_filters  = $filter_info['archive_filters'];
		$custom_filters   = $filter_info['custom_filters'];

		$filter_id_matches = App::getApi('tickets.filters')->getAllIdsForFiltersCollection($all_filters, $this->person);
		$filter_id_matches = Arrays::castToTypeDeep($filter_id_matches, 'int', 'int');

		$archive_filter_counts = App::getApi('tickets.filters')->getAllCountsForFiltersCollection($archive_filters, $this->person);

		// Summary of terms for all filters
		$filters_summary = array();
		foreach ($all_filters as $filter) {
			$searcher = $filter->getSearcher();
			$filters_summary[$filter['id']] = $searcher->getSummary();
		}

		//agent.ui.filter
		$filter_show_options = $this->db->fetchAllKeyValue("
			SELECT name, value_str
			FROM people_prefs
			WHERE person_id = ? AND (name LIKE 'agent.ui.filter-visibility.%' OR name LIKE 'agent.ui.sla.filter-visibility.%')
		", array($this->person->id));

		#------------------------------
		# SLAs
		#------------------------------

		$sla_filter = $this->person->getPref('agent.ui.sla.ticket-filter', 'all');
		$slas = $this->em->getRepository('DeskPRO:Sla')->getAllSlas();
		$sla_counts = $this->em->getRepository('DeskPRO:TicketSla')->getTicketSlaCountsForAgentInterface($slas, $sla_filter);

		#------------------------------
		# Misc
		#------------------------------

		$flags = array('blue','green','orange','pink','purple','red','yellow');
		$flag_counts = $this->em->getRepository('DeskPRO:TicketFlagged')->getCountsForPerson($this->person);

		$label_lister = new \Application\DeskPRO\Labels\LabelLister('tickets');
		$index = $label_lister->getIndexList();

		$label_counts = $this->em->getRepository('DeskPRO:LabelDef')->getLabelCounts('ticket', 25);
		$cloud_gen = new \Application\DeskPRO\UI\TagCloud($label_counts);
		$cloud = $cloud_gen->getCloud();

		$initial_inbox_grouping = $this->em->getRepository('DeskPRO:PersonPref')->getPrefgroupForPersonId('agent.ui.ticket-source-grouping', $this->person->id);

		$term_options = App::getApi('tickets')->getTicketOptions($this->person);

		$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();
		$custom_fields = App::getApi('custom_fields.tickets')->getFieldsDisplayArray($ticket_field_defs);
		$term_options['custom_ticket_fields'] = $custom_fields;

		$data['section_html'] = $this->renderView('AgentBundle:TicketSearch:window-section.html.twig', array(
			'sys_filters' => $sys_filters,
			'sys_filters_hold' => $sys_filters_hold,
			'archive_filters' => $archive_filters,
			'archive_filter_counts' => $archive_filter_counts,
			'filter_id_matches' => $filter_id_matches,
			'filters_summary' => $filters_summary,
			'custom_filters' => $custom_filters,
			'flags' => $flags,
			'flag_counts' => $flag_counts,
			'filter_show_options' => $filter_show_options,
			'labels_index' => $index,
			'labels_cloud' => $cloud,
			'initial_inbox_grouping' => $initial_inbox_grouping,

			'slas' => $slas,
			'sla_counts' => $sla_counts,
			'sla_filter' => $sla_filter,

			'term_options' => $term_options,
		));

		$data['filter_id_matches'] = $filter_id_matches;

		return $this->createJsonResponse($data);
	}

	public function reloadArchiveSectionAction()
	{
		$archive_counts = $this->em->getRepository('DeskPRO:Ticket')->getArchiveCounts();

		return $this->render('AgentBundle:TicketSearch:window-section-archive.html.twig', array(
			'archive_counts' => $archive_counts
		));
	}

	public function refreshSectionDataAction($section)
	{
		switch ($section) {
			case 'labels':
				return $this->getLabelsSectionAction();

			case 'flagged':
				return $this->getFlaggedSectionAction();

			default:
				return $this->createResponse('');
		}
	}

	public function getLabelsSectionAction()
	{
		$label_lister = new \Application\DeskPRO\Labels\LabelLister('tickets');
		$index = $label_lister->getIndexList();

		$label_counts = $this->em->getRepository('DeskPRO:LabelDef')->getLabelCounts('ticket', 25);
		$cloud_gen = new \Application\DeskPRO\UI\TagCloud($label_counts);
		$cloud = $cloud_gen->getCloud();

		return $this->render('AgentBundle:TicketSearch:pane-labels-index.html.twig', array(
			'labels_index' => $index,
			'labels_cloud' => $cloud,
		));
	}

	public function getFilterCountsAction()
	{
		$all_counts = App::getApi('tickets.filters')->getAllCountsCustomFilters($this->person);

		return $this->createJsonResponse($all_counts);
	}

	public function getSlaCountsAction()
	{
		$sla_filter = $this->person->getPref('agent.ui.sla.ticket-filter', 'all');
		$slas = $this->em->getRepository('DeskPRO:Sla')->getAllSlas();
		$sla_counts = $this->em->getRepository('DeskPRO:TicketSla')->getTicketSlaCountsForAgentInterface($slas, $sla_filter);

		return $this->createJsonResponse(array(
			'counts' => $sla_counts,
			'sla_filter' => $sla_filter
		));
	}

	public function getFlaggedSectionAction()
	{
		$flags = array('blue','green','orange','pink','purple','red','yellow');
		$flag_counts = $this->em->getRepository('DeskPRO:TicketFlagged')->getCountsForPerson($this->person);

		return $this->render('AgentBundle:TicketSearch:window-flagged.html.twig', array(
			'flags' => $flags,
			'flag_counts' => $flag_counts
		));
	}

	public function quickSearchAction()
	{
		$limit = $this->in->getUint('limit');
		if (!$limit) $limit = 10;
		$limit = min($limit, 100);

		$searcher = new \Application\DeskPRO\Searcher\TicketSearch();
		$searcher->setPerson($this->person);
		$searcher->setOrderBy('ticket.date_created');

		if ($person_id = $this->in->getUint('person_id')) {
			$searcher->addTerm('person', 'is', array('person_id' => $person_id));
			$results = $searcher->getMatches();
			$results = Arrays::castToType($results, 'integer');
		} else {
			$q = $this->in->getString('q');
			if (!$q) {
				$q = $this->in->getString('term');
			}

			$searcher->addTerm('text', 'is', array('query' => $q));
			$results = $searcher->getMatches();
			$results = Arrays::castToType($results, 'integer');

			if (ctype_digit($q) || preg_match('/#^([0-9]+)$/', $q)) {
				if ($q[0] == '#') $q = substr($q, 1);
				array_unshift($results, $q);
			}
		}

		$results = array_slice($results, 0, $limit);

		$output = array();
		foreach (App::getEntityRepository('DeskPRO:Ticket')->getByIds($results, true) AS $ticket) {
			$output[] = array(
				'id'            => $ticket->id,
				'value'         => $ticket->id,
				'subject'       => $ticket->subject,
				'status'        => $ticket->getStatusCode(),
				'last_activity' => $ticket->getLastActivityDate()->getTimestamp()
			);
		}

		return $this->createJsonResponse($output);
	}

	/**
	 * Render a new pageset.
	 *
	 * The client has a full list of IDs from a search. When he wants the next page,
	 * he sends a set of new IDs in the result set and we return the HTML to inject
	 * into his view.
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function getTicketPageAction()
	{
		$ticket_ids = $this->in->getCleanValueArray('result_ids', 'uint', 'discard');
		$ticket_ids = Arrays::removeFalsey($ticket_ids);
		$ticket_ids = array_unique($ticket_ids);

		$tickets = $this->em->getRepository('DeskPRO:Ticket')->getTicketsResultsFromIds($ticket_ids, $this->person);
		$tickets = Arrays::orderIdArray($ticket_ids, $tickets);

		$display_fields = $this->in->getCleanValueArray('display_fields', 'string', 'discard');
		if (!$display_fields) {
			$display_fields = array('department', 'agent', 'agent_team');
		}

		$has_t_fields = false;
		$has_u_fields = false;

		foreach ($display_fields as $f) {
			if (strpos($f, 'ticket_fields[') === 0) $has_t_fields = true;
			if (strpos($f, 'person_fields[') === 0) $has_u_fields = true;
		}

		$all_custom_fields = array();
		$user_all_custom_fields = array();

		if ($has_t_fields || $has_u_fields) {
			$field_manager = $this->container->getSystemService('ticket_fields_manager');
			$user_field_manager = $this->container->getSystemService('person_fields_manager');

			foreach ($tickets as $t) {
				if ($has_t_fields) {
					$all_custom_fields[$t->id] = $field_manager->getDisplayArrayForObject($t);
				}

				if ($has_u_fields) {
					$p = $t->person;
					$user_all_custom_fields[$p->id] = $user_field_manager->getDisplayArrayForObject($p);
				}
			}
		}

		// Accept changes to apply for previewing
		// - We just apply the changes but dont save them, they'll be
		//   properly displayed in the listing.
		$collection = null;
		$changed_fields = array();

		if ($macro_id = $this->in->getUint('run_macro_id')) {
			$macro = $this->em->find('DeskPRO:TicketMacro', $macro_id);
			$actions = null;
			$collection = $macro->getActionsCollection();

			foreach ($collection->getActions() as $action) {
				if ($action instanceof \Application\DeskPRO\Tickets\TicketActions\ActionInterface) {
					$action->setMetaData(array('is_preview' => true));
				}
			}
		} else {
			$actions = $this->in->getCleanValueArray('actions', 'raw', 'string');
		}

		if (($actions || $collection) && $tickets) {
			if (!$collection) {
				$factory = new ActionsFactory();
				$collection = new ActionsCollection();
				foreach ($actions as $name => $opt) {
					$action = $factory->createFromForm($name, $opt);
					if ($action) {
						if ($action instanceof \Application\DeskPRO\Tickets\TicketActions\ActionInterface) {
							$action->setMetaData(array('is_preview' => true));
						}

						$collection->add($action);

						$display_fields[] = $name;
					}
				}
			}

			foreach ($tickets as $t) {
				$ticket_changes = $collection->getApplyActions($t, $this->person);
				$collection->apply(null, $t, $this->person);

				if ($ticket_changes) {
					$ticket_changed_fields = array();
					foreach ($ticket_changes as $change) {
						$ticket_changed_fields[$change['action']] = true;
						$changed_fields[$t['id']] = $ticket_changed_fields;
					}
				}
			}

			$display_fields = array_unique($display_fields);
		}

		$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();
		$person_field_defs = App::getApi('custom_fields.people')->getEnabledFields();

		$ticket_display = new \Application\DeskPRO\Tickets\TicketResultsDisplay($tickets);
		$ticket_display->setPersonContext($this->person);

		$tpl = 'part-results-simple-ext.html.twig';
		if ($this->in->getString('view_type') == 'list') {
			$tpl = 'part-results-list.html.twig';
		}

		return $this->render("AgentBundle:TicketSearch:$tpl", array(
			'ticket_display'    => $ticket_display,
			'tickets'           => $tickets,
			'display_fields'    => $display_fields,
			'ticket_field_defs' => $ticket_field_defs,
			'person_field_defs' => $person_field_defs,
			'changed_fields'    => $changed_fields,
			'all_custom_fields' => $all_custom_fields,
		));
	}


	/**
	 * We get in an array of ticket ID batches. Each batch is identified by some ID,
	 * usually a filter ID from Tickets.js section.
	 *
	 * array(
	 *     'batchId' => array('grouping' => 'xxx', 'ticket_ids' => array(x,x,x))
	 * )
	 *
	 * We group the batches, and return titles, search URL's, and counts for the batch
	 * using the same batch ID:
	 *
	 * array(
	 *     'batchId' => 'subgroup_html'
	 * )
	 */
	public function groupTicketsAction()
	{
		$ticket_batches = $this->in->getArrayValue('batches');
		$batches = array();

		$save_pref = $this->in->getBool('save_pref');
		$prefs = array();

		foreach ($ticket_batches as $batch_id => $ticket_batch) {

			if ($save_pref) {
				// note $batch_id is a filter ID
				$prefs['agent.ui.ticket-source-grouping.' . $batch_id] = $ticket_batch['grouping'];
			}

			if ($ticket_batch && !empty($ticket_batch['ticket_ids']) && is_string($ticket_batch['ticket_ids'])) {
				$ticket_batch['ticket_ids'] = explode(',', $ticket_batch['ticket_ids']);
				$ticket_batch['ticket_ids'] = Arrays::func($ticket_batch['ticket_ids'], 'trim');
				$ticket_batch['ticket_ids'] = Arrays::castToType($ticket_batch['ticket_ids'], 'int', 'discard');
				$ticket_batch['ticket_ids'] = Arrays::removeFalsey($ticket_batch['ticket_ids']);
			}

			if (!$ticket_batch || empty($ticket_batch['ticket_ids'])) {
				$batches[$batch_id] = '';
				continue;
			}

			$grouper = new \Application\DeskPRO\Tickets\GroupingCounter();
			$grouper->setGrouping($ticket_batch['grouping']);
			$grouper->setMode('specify', $ticket_batch['ticket_ids']);

            $grouped_info = $grouper->getDisplayArray();

            if($ticket_batch['grouping'] == 'urgency') {
                $grouped_info['group1_structure'] = array_reverse($grouped_info['group1_structure']);
            }

			$batches[$batch_id] = $this->renderView('AgentBundle:TicketSearch:window-filter-groupresult.html.twig', array(
				'grouping_var' => $ticket_batch['grouping'],
				'grouped_info' => $grouped_info,
			));
		}

		if ($prefs) {
			$this->em->getConnection()->beginTransaction();

			try {
				foreach ($prefs as $k => $v) {
					$p = $this->person->setPreference($k, $v);
					$this->em->persist($p);
				}

				$this->em->flush();
				$this->em->getConnection()->commit();
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}
		}

		return $this->createJsonResponse($batches);
	}


	public function getFlaggedSectionDataAction()
	{
		$data = array();
		$data['flag_counts'] = $this->em->getRepository('DeskPRO:TicketFlagged')->getCountsForPerson($this->person);

		return $this->createJsonResponse($data);
	}

	public function runCustomFilterAction()
	{
		$result_cache = false;
		if ($this->in->getUint('cache_id')) {
			$result_cache = $this->em->getRepository('DeskPRO:ResultCache')->find($this->in->getUint('cache_id'));
			if ($result_cache['person_id'] != $this->person['id']) {
				$result_cache = false;
			}
		}

		$do_run = false;

		$terms = array();
		$order_by = $this->person->getPref('agent.ui.ticket-basic-order-by.general');
		$group_by = $this->in->getString('group_by');
		$searcher = null;

		#------------------------------
		# If there's no result set, we're running it for the first time
		#------------------------------

		if (!$result_cache) {
			$term_rules = RuleBuilder::newTermsBuilder();
			$terms = $term_rules->readForm($this->in->getCleanValueArray('terms', 'raw' , 'discard'));

			$set_terms_map = array(
				'department'    => array('op' => 'contains', 'options' => array()),
				'status'        => array('op' => 'contains', 'options' => array()),
				'agent'         => array('op' => 'contains', 'options' => array()),
				'agent_team'    => array('op' => 'contains', 'options' => array()),
				'participant'   => array('op' => 'contains', 'options' => array()),
				'category'      => array('op' => 'contains', 'options' => array()),
				'product'       => array('op' => 'contains', 'options' => array()),
				'priority'      => array('op' => 'contains', 'options' => array()),
				'workflow'      => array('op' => 'contains', 'options' => array()),
				'organization'  => array('op' => 'contains', 'options' => array()),
				'language'      => array('op' => 'contains', 'options' => array()),
				'sla'           => array('op' => 'contains', 'options' => array()),
			);

			foreach ($set_terms_map as $name => $info) {
				$in_val = $this->container->getIn()->getCleanValueArray('set_term.'.$name, 'raw', 'discard');
				if ($in_val) {
					$new_term = $info;
					$new_term['options'] = $in_val;
					Arrays::unshiftAssoc($new_term, 'type', $name);
					$terms[] = $new_term;
				}
			}

			foreach ($this->container->getSystemService('ticket_fields_manager')->getFields() as $field) {
				$in_val = $this->container->getIn()->getValue('set_term.field_'.$field->getId());
				if ($in_val) {
					$terms[] = array('type' => 'ticket_field[' . $field->getId() . ']', 'op' => 'is', 'options' => array('value' => $in_val));
				}
			}

			foreach ($this->in->getCleanValueArray('terms_expanded', 'raw', 'raw') as $type => $info) {

				if (empty($info['options']) || empty($info['op'])) {
					continue;
				}

				$opts = $info['options'];

				foreach ($opts as &$_v) {
					if (is_array($_v)) {
						$_v = Arrays::func($_v, 'trim');
						$_v = Arrays::removeEmptyArray($_v);
					} else if (trim($_v) === "") {
						$_v = null;
					}
				}
				unset($_v);

				$opts = Arrays::removeValue($opts, null, true);
				$opts = Arrays::removeValue($opts, false, true);

				if (!$opts) {
					continue;
				}

				$terms[] = array('type' => $type, 'op' => $info['op'], 'options' => $opts);
			}

			if ($this->in->getString('query')) {
				$terms[] = array('type' => 'text', 'op' => 'is', 'options' => array('query' => $this->in->getString('query')));
			}

			// Search form: status
			if ($search_term = $this->in->getCleanValueArray('search_status', 'string', 'discard')) {
				$terms[] = array('type' => 'status', 'op' =>'is', 'options' => array('status' => $search_term));
			}

			// Search form: search_assigned
			if ($search_term = $this->in->getCleanValueArray('search_assigned', 'raw', 'discard')) {
				$agent_ids = array();
				$team_ids = array();

				foreach ($search_term as $t) {
					if (strpos($t, 'team.') === 0) {
						$t = Strings::extractRegexMatch('#^team\.(\d+)$#', $t);
						if ($t !== "") {
							$t = (int)$t;
							$team_ids[] = $t;
						}
					} else {
						$t = (int)$t;
						$agent_ids[] = $t;
					}
				}

				$agent_ids = array_unique($agent_ids);
				$team_ids = array_unique($team_ids);

				if ($agent_ids) {
					$terms[] = array('type' => 'agent', 'op' => 'is', 'options' => array('agent_ids' => $agent_ids));
				}
				if ($team_ids) {
					$terms[] = array('type' => 'agent_team', 'op' => 'is', 'options' => array('team_ids' => $team_ids));
				}
			}

			if ($search_person_id = $this->in->getUint('search_person_id')) {
				$terms[] = array('type' => 'person_id', 'op' => 'is', 'options' => array('person_id' => $search_person_id));
			}

			// Search form: status
			if ($search_term = $this->in->getCleanValueArray('search_status', 'string', 'discard')) {
				$terms[] = array('type' => 'status', 'op' =>'contains', 'options' => array('status' => $search_term));
			}

			// Search form: subject
			$search_term = $this->in->getCleanValueArray('search_subject_string', 'string', 'discard');
			if ($search_term && $search_term[0]) {
				foreach ($search_term as $k => $string) {
					$op   = $this->in->getString("search_subject_op.$k");
					$type = $this->in->getString("search_subject_type.$k");

					$terms[] = array('type' => 'subject_adv', 'op' => $op, 'options' => array('query' => $string, 'type' => $type));
				}
			} else if ($search_term = $this->in->getString('search_subject_simple')) {
				$terms[] = array('type' => 'subject', 'op' => 'contains', 'options' => array('query' => $search_term));
			}

			// Search form: message
			$search_term = $this->in->getCleanValueArray('search_message_string', 'string', 'discard');
			if ($search_term && $search_term[0]) {
				foreach ($search_term as $k => $string) {
					$op   = $this->in->getString("search_message_op.$k");
					$type = $this->in->getString("search_message_type.$k");
					$who  = $this->in->getString("search_message_who.$k");

					$date = null;
					if ($date_op = $this->in->getString("search_message_when_op.$k")) {
						$date = array(
							'date1'               => $this->in->getString("search_message_when.date1.$k"),
							'date2'               => $this->in->getString("search_message_when.date2.$k"),
							'date1_relative'      => $this->in->getString("search_message_when.date1_relative.$k"),
							'date2_relative'      => $this->in->getString("search_message_when.date2_relative.$k"),
							'date1_relative_type' => $this->in->getString("search_message_when.date1_relative_type.$k"),
							'date2_relative_type' => $this->in->getString("search_message_when.date2_relative_type.$k"),
						);
					}

					$terms[] = array(
						'type'    => 'ticket_message_adv',
						'op'      => $op,
						'options' => array(
							'query'   => $string,
							'type'    => $type,
							'who'     => $who,
							'date'    => $date,
							'date_op' => $date_op,
					));
				}
			} else if ($search_term = $this->in->getString('search_message_simple')) {
				$terms[] = array('type' => 'ticket_message', 'op' => 'contains', 'options' => array('query' => $search_term));
			}

			$do_run = true;
		}


		#------------------------------
		# Re-do search if we changed order
		#------------------------------

		if ($result_cache && $order_by && $result_cache->getExtraData('order_by') != $order_by) {
			$terms = $result_cache['criteria'];
			$do_run = true;
		}

		#------------------------------
		# Run a filter if we need to
		#------------------------------

		if ($do_run) {
			$searcher = new \Application\DeskPRO\Searcher\TicketSearch();
			$searcher->setPerson($this->person);
			if ($order_by) {
				$searcher->setOrderByCode($order_by);
			}

			$user_searcher  = new \Application\DeskPRO\Searcher\PersonSearch();
						$org_searcher   = new \Application\DeskPRO\Searcher\OrganizationSearch();
			$has_user_terms = false;
			$has_org_terms  = false;

			foreach ($terms as $term) {
				if (empty($term['op'])) {
					continue;
				}
				if (!isset($term['options'])) $term['options'] = array();
				if (strpos($term['type'], 'person_') === 0 && $term['type'] != 'person_id') {
					$user_searcher->addTerm($term['type'], $term['op'], $term['options']);
					$has_user_terms = true;
				} elseif (strpos($term['type'], 'org_') === 0) {
					$org_searcher->addTerm($term['type'], $term['op'], $term['options']);
					$has_org_terms = true;
				} else {
					$searcher->addTerm($term['type'], $term['op'], $term['options']);
				}
			}

			if ($has_user_terms) {
				$searcher->setPersonSearch($user_searcher);
			}
			if ($has_org_terms) {
				$searcher->setOrganizationSearch($org_searcher);
			}

			$results = $searcher->getMatches();
			$results = Arrays::castToType($results, 'integer');

			if (!$result_cache) {
				$result_cache = new \Application\DeskPRO\Entity\ResultCache();
				$result_cache->person = $this->person;
			}

			$needs_urgency = $searcher->needsUrgency();

			$result_cache->results = $results;
			$result_cache->criteria = $terms;
			$result_cache->num_results = count($results);
			$result_cache->setExtraData('order_by', $order_by);
			$result_cache->setExtraData('needs_urgency', $needs_urgency);
			$result_cache->setExtraData('terms_summary', $searcher->getSummary());
			$result_cache->setExtraData('order_by_summary', $searcher->getOrderBySummary());

			$this->em->persist($result_cache);
			$this->em->flush();
		}

		$helper = Helper\TicketResults::newFromResultCache($this, $result_cache);
		if ($group_by) {
			$helper->setGroupField($group_by);
		}

		$vars = array(
			'cache'               => $result_cache,
			'cache_id'            => $result_cache->id,
			'order_by_summary'    => $result_cache->getExtraData('order_by_summary'),
			'terms_summary'       => $result_cache->getExtraData('terms_summary'),
			'needs_urgency'       => $result_cache->getExtraData('needs_urgency'),
			'order_by'            => explode(':', $result_cache->getExtraData('order_by')),
			'ticket_ids'          => $result_cache->results,
			'view_name'           => $this->in->getString('view_name'),
			'view_extra'          => $this->in->getString('view_extra'),
		);

		$search_form = array(
			'terms'    => $result_cache->criteria,
			'order_by' => $result_cache->getExtraData('order_by')
		);
		$vars['search_form'] = $search_form;

		if ($this->in->getString('filtername')) {
			$vars['filtername'] = $this->in->getString('filtername');
		}

		if ($this->in->getString('view_name')) {
			$pref_display_fields = $this->person->getPref('agent.ui.ticket-filter-display-fields.name_' . $this->in->getString('view_name'));
		} else {
			$pref_display_fields = $this->person->getPref('agent.ui.ticket-basic-display-fields.general');
		}

		if ($pref_display_fields) {
			$vars['display_fields'] = $pref_display_fields;
		} else {
			// Default display fields based on the filter
			$vars['display_fields'] = $this->_suggestedDisplayFields($searcher);
		}

		return $this->_getResponseForTickets('custom-filter', $result_cache['id'], $helper, $vars);
	}

	public function runFilterAction($filter_id)
	{
        $view_type = $this->in->getString('view_type');

		/** @var $filter \Application\DeskPRO\Entity\TicketFilter */
		$filter = $this->em->getRepository('DeskPRO:TicketFilter')->find($filter_id);

		if (!$filter) {
			throw $this->createNotFoundException();
		}

		$searcher = $filter->getSearcher();
		$searcher->setPerson($this->person);

		$order_by = $this->in->getString('order_by');
		if (!$order_by) {
			$order_by = $this->person->getPref('agent.ui.ticket-filter-order-by.' . $filter['id']);
		}

		if (!$order_by AND $filter['order_by']) {
			$order_by = $filter['order_by'];
		}

		if ($order_by) {
			$searcher->setOrderByCode($order_by);
		}

        if($view_type == 'csv') {
            $searcher->setLimit(0);
        }

		$set_group_term = null;
		$set_group_option = null;
		if ($this->in->getString('set_group_term')) {
			$set_group_term = $this->in->getString('set_group_term');
			$set_group_option = $this->in->getString('set_group_option');

			$term = \Application\DeskPRO\Tickets\GroupingCounter::getSearchTerm($set_group_term, $set_group_option);
			if ($term) {
				$type = $term['type'];
				$op = $term['op'];
				$choice = $term;
				unset($choice['type'], $choice['op']);

				$searcher->addTerm($type, $op, $choice);
			}
		}

		$results = $searcher->getMatches();

		$results = Arrays::castToType($results, 'integer');

		$helper = new Helper\TicketResults($this);
		$helper->setTicketIds($results);

		if ($order_by) {
			$helper->setGroupOrderBy($order_by);
		}

		// Or if the user has their own
		$group_by = $this->person->getPref('agent.ui.ticket-filter-group-by.' . $filter['id']);

		if ($this->in->checkIsset('group_by')) {
			$group_by = $this->in->getString('group_by');

			App::getEntityRepository('DeskPRO:PersonPref')->savePref(
				$this->person,
				'agent.ui.ticket-filter-group-by.' . $filter['id'],
				$group_by
			);

		} elseif ($filter['group_by']) {
			$group_by = $filter['group_by'];
		}

		if ($group_by) {
			$helper->setGroupField($group_by);
		}

		$needs_urgency = $searcher->needsUrgency();

		$vars = array(
			'filter' => $filter,
			'filter_id' => $filter['id'],
			'needs_urgency' => $needs_urgency,
			'order_by_summary' => $searcher->getOrderBySummary(),
			'terms_summary' => $searcher->getSummary(),
			'set_group_term' => $set_group_term,
			'set_group_option' => $set_group_option,
			'ticket_ids' => $results,
            'order_by' => $searcher->getOrderBy(),
		);

		$pref_display_fields = $this->person->getPref('agent.ui.ticket-filter-display-fields.' . $filter['id']);
		if ($pref_display_fields) {
			$vars['display_fields'] = $pref_display_fields;
		} else {
			// Default display fields based on the filter
			$vars['display_fields'] = $this->_suggestedDisplayFields($filter->getSearcher());
		}

		$search_form = array(
			'terms' => $filter['terms'],
			'order_by' => $filter['order_by'],
		);
		$vars['search_form'] = $search_form;

		return $this->_getResponseForTickets('filter', $filter['id'], $helper, $vars);
	}

	public function runNamedFilterAction($filter_name)
	{
		$filter = $this->em->getRepository('DeskPRO:TicketFilter')->findOneBy(array('sys_name' => $filter_name));
		return $this->runFilterAction($filter['id']);
	}

	public function runSlaAction($sla_id, $sla_status = '')
	{
        $view_type = $this->in->getString('view_type');

		/** @var $sla \Application\DeskPRO\Entity\Sla */
		$sla = $this->em->getRepository('DeskPRO:Sla')->find($sla_id);

		if (!$sla) {
			throw $this->createNotFoundException();
		}

		$searcher = new \Application\DeskPRO\Searcher\TicketSearch();
		$searcher->setPerson($this->person);

		$sla_filter = $this->person->getPref('agent.ui.sla.ticket-filter', 'all');
		if ($sla_filter == 'agent') {
			$searcher->addTerm(\Application\DeskPRO\Searcher\TicketSearch::TERM_AGENT, 'is', $this->person->id);
		} else if ($sla_filter == 'team') {
			$searcher->addTerm(\Application\DeskPRO\Searcher\TicketSearch::TERM_AGENT_TEAM, 'is', $this->person->getAgentTeamIds());
		}

		$searcher->addTerm(\Application\DeskPRO\Searcher\TicketSearch::TERM_SLA_COMPLETED, 'is', array(
			'is_completed' => 0,
			'sla_id' => $sla_id
		));

		if ($sla_status) {
			$searcher->addTerm(\Application\DeskPRO\Searcher\TicketSearch::TERM_SLA_STATUS, 'is', array(
				'sla_status' => $sla_status,
				'sla_id' => $sla_id
			));
		}

		if ($sla->sla_type == \Application\DeskPRO\Entity\Sla::TYPE_WAITING_TIME) {
			$searcher->addTerm(\Application\DeskPRO\Searcher\TicketSearch::TERM_STATUS, 'is', 'awaiting_agent');
		} else if ($sla->sla_type == \Application\DeskPRO\Entity\Sla::TYPE_FIRST_RESPONSE) {
			$searcher->addTerm(\Application\DeskPRO\Searcher\TicketSearch::TERM_STATUS, 'is', 'awaiting_agent');
		} else {
			$searcher->addTerm(\Application\DeskPRO\Searcher\TicketSearch::TERM_STATUS, 'is', array('awaiting_agent', 'awaiting_user'));
		}

		$order_by = $this->in->getString('order_by');
		if (!$order_by) {
			$order_by = $this->person->getPref('agent.ui.ticket-sla-order-by.' . $sla['id'], 'ticket.sla_severity:desc');
		}

		if ($order_by) {
			$searcher->setOrderByCode($order_by);
		}

        if ($view_type == 'csv') {
            $searcher->setLimit(0);
        }

		$set_group_term = null;
		$set_group_option = null;
		if ($this->in->getString('set_group_term')) {
			$set_group_term = $this->in->getString('set_group_term');
			$set_group_option = $this->in->getString('set_group_option');

			$term = \Application\DeskPRO\Tickets\GroupingCounter::getSearchTerm($set_group_term, $set_group_option);
			if ($term) {
				$type = $term['type'];
				$op = $term['op'];
				$choice = $term;
				unset($choice['type'], $choice['op']);

				$searcher->addTerm($type, $op, $choice);
			}
		}

		$results = $searcher->getMatches();

		$results = Arrays::castToType($results, 'integer');

		$helper = new Helper\TicketResults($this);
		$helper->setTicketIds($results);

		// Or if the user has their own
		$group_by = $this->person->getPref('agent.ui.ticket-sla-group-by.' . $sla['id']);

		if ($this->in->getString('group_by')) {
			$group_by = $this->in->getString('group_by');

			App::getEntityRepository('DeskPRO:PersonPref')->savePref(
				$this->person,
				'agent.ui.ticket-sla-group-by.' . $sla['id'],
				$group_by
			);
		}

		if ($group_by) {
			$helper->setGroupField($group_by);
		}

		$needs_urgency = $searcher->needsUrgency();

		$vars = array(
			'sla' => $sla,
			'sla_id' => $sla->id,
			'sla_status' => $sla_status,
			'sla_filter' => $sla_filter,
			'needs_urgency' => $needs_urgency,
			'order_by_summary' => $searcher->getOrderBySummary(),
			'terms_summary' => $searcher->getSummary(),
			'set_group_term' => $set_group_term,
			'set_group_option' => $set_group_option,
			'ticket_ids' => $results,
            'order_by' => $searcher->getOrderBy(),
		);

		$pref_display_fields = $this->person->getPref('agent.ui.ticket-sla-display-fields.' . $sla['id']);
		if ($pref_display_fields) {
			$vars['display_fields'] = $pref_display_fields;
		} else {
			// Default display fields based on the filter
			$vars['display_fields'] = $this->_suggestedDisplayFields($searcher);
			if (!in_array('slas', $vars['display_fields'])) {
				$vars['display_fields'][] = 'slas';
			}
		}

		return $this->_getResponseForTickets('sla', $sla['id'], $helper, $vars);
	}

	protected function _getResponseForTickets($type, $type_id, $results_helper, array $vars = array())
	{
		$view_type = $this->in->getString('view_type');
		if (!$view_type OR !in_array($view_type, array('list', 'simple', 'simple-ext', 'csv'))) {
			$view_type = 'simple-ext';
		}

		$is_partial = false;
		$tpl = 'AgentBundle:TicketSearch:filter-results-'.$view_type.'.html.twig';
		if ($this->in->getBool('partial')) {
			$is_partial = true;
			$tpl = 'AgentBundle:TicketSearch:part-results-'.$view_type.'.html.twig';
		}

		$per_page = 50;
		if ($view_type == 'list') {
			$per_page = 50;
		}

		$vars['viewtpl'] = $type;

		#------------------------------
		# Get the tickets to show
		#------------------------------

		$grouped_info = null;
		if (!$is_partial AND $results_helper->isGroupable()) {
			$grouped_info = $results_helper->getGroupDisplayInfo();
		}

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

        $tickets = array();

		if (!$this->in->checkIsset('grouping_option') || $this->in->getString('grouping_option') == '-1' || $this->in->getString('grouping_option') == 'DP_NOT_SET') {
			// User looking at all results
			$is_grouping = false;
			$grouping_option = 'DP_NOT_SET';
            if($view_type != 'csv') {
			    $tickets = $results_helper->getTicketsForPage($page, $per_page);
            }
		} else {
			// User looking at just a group of results
			$is_grouping = true;
			$grouping_option = $this->in->getString('grouping_option');
			if ($grouping_option == 'DP_NOT_SET') {
				$grouping_option = -1;
			}

            if($view_type != 'csv') {
			    $tickets = $results_helper->getGroupedTicketsForPage($grouping_option, $page, $per_page);
			    $vars['ticket_ids'] = $results_helper->getGroupTicketIds($grouping_option);
            }
		}

		#------------------------------
		# Send results
		#------------------------------

		if (!count($tickets) && $is_partial && $view_type != 'csv') {
			return $this->createJsonResponse(array('no_more_results' => true));
		}

        if($view_type != 'csv') {
		    $flagged_tickets = $this->em->getRepository('DeskPRO:TicketFlagged')->getFlagsForTickets($tickets, $this->person);
        }
        else {
            $flagged_tickets = array();
        }

		if (empty($vars['display_fields'])) {
			$vars['display_fields'] = array('date_created', 'department');
		}

		$macros = null;
		$ticket_options = null;
		if (!$is_partial) {
			$macros = $this->em->getRepository('DeskPRO:TicketMacro')->getMacrosForPerson($this->person);
			$ticket_options = App::getApi('tickets')->getTicketOptions($this->person);

			$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();
			$custom_fields = App::getApi('custom_fields.tickets')->getFieldsDisplayArray($ticket_field_defs);
			$ticket_options['custom_ticket_fields'] = $custom_fields;

			// People stuff
			$ticket_options['people_organizations'] = $this->em->getRepository('DeskPRO:Organization')->getOrganizationNames();
			$people_field_defs = App::getApi('custom_fields.people')->getEnabledFields();
			$ticket_options['custom_people_fields'] = $custom_fields = App::getApi('custom_fields.people')->getFieldsDisplayArray($people_field_defs);
		}

		// ticket and person defs for columns
		$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();
		$person_field_defs = App::getApi('custom_fields.people')->getEnabledFields();

		$vars['display_fields'] = array_unique($vars['display_fields']);

		$pageinfo = Numbers::getPaginationPages($results_helper->getCount(), $page, $per_page);

		$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
		$agent_teams = $this->em->getRepository('DeskPRO:AgentTeam')->findAll();

		$has_t_fields = false;
		$has_u_fields = false;

		foreach ($vars['display_fields'] as $f) {
			if (strpos($f, 'ticket_fields[') === 0) $has_t_fields = true;
			if (strpos($f, 'person_fields[') === 0) $has_u_fields = true;
		}

		$all_custom_fields = array();
		$user_all_custom_fields = array();

		if ($has_t_fields || $has_u_fields) {
			$field_manager = $this->container->getSystemService('ticket_fields_manager');
			$user_field_manager = $this->container->getSystemService('person_fields_manager');

			foreach ($tickets as $t) {
				if ($has_t_fields) {
					$all_custom_fields[$t->id] = $field_manager->getDisplayArrayForObject($t);
				}

				if ($has_u_fields) {
					$p = $t->person;
					$user_all_custom_fields[$p->id] = $user_field_manager->getDisplayArrayForObject($p);
				}
			}
		}

		$ticket_display = new \Application\DeskPRO\Tickets\TicketResultsDisplay($tickets);
		$ticket_display->setPersonContext($this->person);

		if (!$this->container->getSetting('core.tickets.use_ref') && in_array('ref', $vars['display_fields'])) {
			$vars['display_fields'] = Arrays::removeValue($vars['display_fields'], 'ref');
		}

		$vars = array_merge($vars, array(
			'agents'             => $agents,
			'agent_teams'        => $agent_teams,
			'type'               => $type,
			'type_id'            => $type_id,
			'ticket_display'     => $ticket_display,
			'tickets'            => $tickets,
			'count'              => $results_helper->getCount(),
			'flagged_tickets'    => $flagged_tickets,
			'ticket_options'     => $ticket_options,
			'page'               => $page,
			'pageinfo'           => $pageinfo,
			'per_page'           => $per_page,
			'macros'             => $macros,
			'show_flag'          => true,
			'grouped_info'       => $grouped_info,
			'group_by'           => $results_helper->getGroupField(),
			'grouping_option'    => $grouping_option,
			'grouping_summary'   => $results_helper->getGroupingSummary(),
			'is_grouped_result'  => $is_grouping,
			'ticket_field_defs'  => $ticket_field_defs,
			'person_field_defs'  => $person_field_defs,
			'load_first'         => $this->in->getBool('load_first'),
			'all_custom_fields'  => $all_custom_fields,
			'user_all_custom_fields'  => $user_all_custom_fields
		));

        if($view_type == 'csv') {
            return $this->_outputCsv($vars, $results_helper);
        }

		$html = $this->renderView($tpl, $vars);

		if ($is_partial) {
			return $this->createJsonResponse(array(
				'html'              => $html,
				'page'              => $page,
				'is_grouped_result' => $is_grouping,
			));
		} else {
			return $this->createResponse($html);
		}
	}

    protected function _outputCsv($vars, $results_helper) {
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename=tickets.csv');
        $response->sendHeaders();

        $display_fields = array(
            'id',
            'language_id',
            'department_id',
            'category_id',
            'priority_id',
            'workflow_id',
            'product_id',
            'person_id',
            'person_email_id',
            'person_email_validating_id',
            'agent_id',
            'agent_team_id',
            'organization_id',
            'linked_chat_id',
            'email_gateway_id',
            'email_gateway_address_id',
            'locked_by_agent',
            'ref',
            'auth',
            'creation_system',
            'notify_email',
            'ticket_hash',
            'status',
            'hidden_status',
            'validating',
            'is_hold',
            'urgency',
            'date_created',
            'date_resolved',
            'date_closed',
            'date_first_agent_assign',
            'date_first_agent_reply',
            'date_last_agent_reply',
            'date_last_user_reply',
            'date_agent_waiting',
            'date_user_waiting',
            'date_status',
            'total_user_waiting',
            'total_to_first_reply',
            'date_locked',
            'has_attachments',
            'subject',
            'labels'
        );

		$field_manager = $this->container->getSystemService('ticket_fields_manager');
		foreach ($field_manager->getFields() as $f) {
			$display_fields[] = 'ticket_fields['.$f->id.']';
		}

        $temp = fopen('php://memory', 'rw');
        $row = array();

        foreach($display_fields as $display_field) {
            switch($display_field) {
				case 'id':
					$row[] = 'ticket_id';
					break;
                case 'language_id':
                case 'department_id':
                case 'priority_id':
                case 'category_id':
                case 'workflow_id':
                case 'product_id':
                case 'email_gateway_id':
                    $row[] = $display_field;
                    $row[] = preg_replace('/id$/' , 'title', $display_field);
                    break;
                case 'person_email_id':
                case 'person_email_validating_id':
                    $row[] = preg_replace('/id$/' , 'email', $display_field);
					break;
                case 'person_id':
                case 'agent_id':
                case 'agent_team_id':
                case 'organization_id':
                    $row[] = $display_field;
                    $row[] = preg_replace('/id$/' , 'name', $display_field);
                    break;
                default:
					if ($field_id = Strings::extractRegexMatch('#^ticket_fields\[(\d+)\]$#', $display_field)) {
						$row[] = $field_manager->getFieldFromId($field_id)->title;
					} else {
						$row[] = $display_field;
					}
                    break;
            }
        }

        fputcsv($temp, $row);
        rewind($temp);
        $response->setContent(fgets($temp));
        $response->sendContent();
        ftruncate($temp, 0);
        $chunk_size = 1024;
        $page = 1;
        $count = $results_helper->getCount();

        // This behaves unexpectly. If the total number of tickets is less than the page size it will always return all
        // of the tickets regardless of the page setting.
        if($vars['is_grouped_result']) {
            $tickets = $results_helper->getGroupedTicketsForPage($this->in->getString('grouping_option'), $page++, $chunk_size);
        }
        else {
            $tickets = $results_helper->getTicketsForPage($page++, $chunk_size);
        }

        $got = count($tickets);

        while(!empty($tickets)) {
            $ticket = array_shift($tickets);
			$custom_text_data = $field_manager->getRenderedToTextForObject($ticket);
            $row = array();

            foreach($display_fields as $display_field) {
                switch($display_field) {
                    case 'language_id':
                    case 'department_id':
                    case 'priority_id':
                    case 'category_id':
                    case 'workflow_id':
                    case 'product_id':
                    case 'email_gateway_id':
                        preg_match('/^(.*)_id$/', $display_field, $matches);
                        list(, $name) = $matches;
                        $entity = $ticket->{$name};

                        if($entity) {
                            $row[] = $entity->id;
                            $row[] = $entity->title;
                        }
                        else {
                            $row[] = $row[] = '';
                        }
                        break;
                    case 'person_id':
                    case 'agent_id':
                        preg_match('/^(.*)_id$/', $display_field, $matches);
                        list(, $name) = $matches;
                        $entity = $ticket->{$name};

                        if($entity) {
                            $row[] = $entity->id;
                            $row[] = $entity->display_name;
                        } else {
                            $row[] = '';
                            $row[] = '';
                        }
                        break;
					case 'agent_team_id':
					case 'organization_id':
						preg_match('/^(.*)_id$/', $display_field, $matches);
                        list(, $name) = $matches;
                        $entity = $ticket->{$name};

                        if($entity) {
                            $row[] = $entity->id;
                            $row[] = $entity->name;
                        } else {
                            $row[] = '';
                            $row[] = '';
                        }
						break;
                    case 'person_email_id':
                    case 'person_email_validating_id':
                        preg_match('/^(.*)_id$/', $display_field, $matches);
                        list(, $name) = $matches;
                        $entity = $ticket->{$name};

                        if($entity) {
                            $row[] = $entity->email;
                        }
                        else {
                            $row[] = '';
                        }
                        break;
                    case 'labels':
                        $row[] = implode('|', $vars['ticket_display']->getTicketLabels($ticket));
                        break;
                    default:
						if ($field_id = Strings::extractRegexMatch('#^ticket_fields\[(\d+)\]$#', $display_field)) {
							if (isset($custom_text_data[$field_id])) {
								$row[] = $custom_text_data[$field_id]['rendered'];
							} else {
								$row[] = '';
							}
						} elseif(preg_match('/^(.*)_id$/', $display_field, $matches)) {
                            list(, $name) = $matches;
                            $entity = $ticket->{$name};

                            if($entity) {
                                $row[] = $entity->id;
                            } else {
								$row[] = '';
							}
                        }
                        else {
                            $value = $ticket->{$display_field};

                            if(is_scalar($value)) {
                                $row[] = $value;
                            } elseif(is_object($value)) {
                                if($value instanceof \DateTime) {
                                    $row[] = $value->format('c');
                                } else {
									$row[] = '';
								}
                            } else {
								$row[] = '';
							}
                        }
                        break;
                }
            }

            fputcsv($temp, $row);
            rewind($temp);
            $response->setContent(fgets($temp));
            $response->sendContent();
            ftruncate($temp, 0);

            if(empty($tickets) && $got < $count) {
                $this->getDoctrine()->getEntityManager()->clear();

                if($vars['is_grouped_result']) {
                    $tickets = $results_helper->getGroupedTicketsForPage($this->in->getString('grouping_option'), $page++, $chunk_size);
                }
                else {
                    $tickets = $results_helper->getTicketsForPage($page++, $chunk_size);
                }

                $got += count($tickets);
            }
        }

        $response->setContent('');
        fclose($temp);

        return $response;
    }

	public function getSingleTicketRowAction($content_type, $content_id)
	{
		if ($content_type == 'sla') {
			$sla = $this->em->getRepository('DeskPRO:Sla')->find($content_id);
			$filter = null;
		} else {
			$filter = $this->em->getRepository('DeskPRO:TicketFilter')->find($content_id);
			$sla = null;
		}

		$ticket_id = $this->in->getUint('ticket_id');
		$ticket = $this->em->find('DeskPRO:Ticket', $ticket_id);

		if (!$ticket) {
			return $this->createResponse('');
		}

		$vars = array(
			'page' => -1,
			'tickets' => array($ticket),
			'filter' => $filter,
			'sla' => $sla
		);

		$view_type = $this->in->getString('view_type');
		if (!$view_type OR !in_array($view_type, array('list', 'simple', 'simple-ext'))) {
			$view_type = 'simple-ext';
		}

		if ($this->in->getString('view_name')) {
			$pref_display_fields = $this->person->getPref('agent.ui.ticket-filter-display-fields.name_' . $this->in->getString('view_name'));
		} elseif ($sla) {
			$pref_display_fields = $this->person->getPref('agent.ui.ticket-sla-display-fields.' . $sla['id']);
		} elseif ($filter) {
			$pref_display_fields = $this->person->getPref('agent.ui.ticket-filter-display-fields.' . $filter['id']);
		} else {
			$pref_display_fields = $this->person->getPref('agent.ui.ticket-basic-display-fields.general');
		}
		if ($pref_display_fields) {
			$vars['display_fields'] = $pref_display_fields;
		} else {
			// Default display fields based on the filter
			if ($filter) {
				$vars['display_fields'] = $this->_suggestedDisplayFields($filter->getSearcher());
			} else {
				$vars['display_fields'] = $this->_suggestedDisplayFields();
			}
		}

		$is_partial = true;
		$tpl = 'AgentBundle:TicketSearch:part-results-'.$view_type.'.html.twig';

		$ticket_display = new \Application\DeskPRO\Tickets\TicketResultsDisplay(array($ticket->id => $ticket));
		$ticket_display->setPersonContext($this->person);
		$vars['ticket_display'] = $ticket_display;

		return $this->render($tpl, $vars);
	}

	protected function _suggestedDisplayFields(TicketSearch $searcher = null)
	{
		$display_fields = array('subject', 'user', 'department', 'agent', 'agent_team');

		if ($searcher) {
			$specific_fields = $searcher->getSpecificFields();

			$max = 5;
			foreach ($searcher->getTermFields() as $term) {
				if (!in_array($term, $specific_fields)) {
					$display_fields[] = $term;
				}

				$display_fields = array_unique($display_fields);
				if (count($display_fields) >= $max) {
					break;
				}
			}
		}

		return $display_fields;
	}

	############################################################################
	# ajax-release-locks
	############################################################################

	public function ajaxReleaseLocksAction()
	{
		$ticket_ids = $this->in->getCleanValueArray('ticket_ids', 'uint', 'discard');
		$tickets = $this->em->getRepository('DeskPRO:Ticket')->getTicketsFromIds($ticket_ids);

		$this->em->beginTransaction();

		foreach ($tickets as $ticket) {
			$ticket->unlockTicket();

			$lock_cm = new ClientMessage();
			$lock_cm->fromArray(array(
				'channel' => 'agent-notification.tickets.unlocked',
				'data' => array(
					'ticket_id' => $ticket['id'],
					'agent_id' => $ticket['id'],
				),
				'created_by_client' => $this->session->getEntity()->getId(),
			));

			$this->em->persist($ticket);
			$this->em->persist($lock_cm);
		}

		$this->em->flush();
		$this->em->commit();

		return $this->createJsonResponse(array('success' => true));
	}


	############################################################################
	# ajax-delete-ticket
	############################################################################

	public function ajaxDeleteTicketsAction()
	{
		$ticket_ids = $this->in->getCleanValueArray('ticket_ids', 'uint', 'discard');
		$tickets = $this->em->getRepository('DeskPRO:Ticket')->getTicketsFromIds($ticket_ids);

		$deleted_tickets = array();

		$this->em->beginTransaction();
		foreach ($tickets as $ticket) {
			$deleted_tickets[] = $ticket['id'];
			$this->em->remove($ticket);
		}
		$this->em->flush();
		$this->em->commit();

		return $this->createJsonResponse(array('success' => true, 'deleted_tickets' => $deleted_tickets));
	}



	############################################################################
	# ajax-get-macro-actions
	############################################################################

	public function ajaxGetMacroAction()
	{
		$macro_id = $this->in->getUint('macro_id');
		$macro = $this->em->getRepository('DeskPRO:TicketMacro')->find($macro_id);

		$tickets = null;
		$ticket_ids = $this->in->getCleanValueArray('ticket_ids', 'uint', 'discard');
		if ($ticket_ids) {
			$tickets = $this->em->getRepository('DeskPRO:Ticket')->getTicketsFromIds($ticket_ids);
		}

		$actions = $macro->getActionsArrayForCollection($tickets);

		$data = array();
		$data['raw_actions'] = array();
		$data['ticket_actions'] = $actions;

		$raw_actions = $macro->getActionsArray();
		if (!empty($raw_actions['new_reply'])) {
			$data['raw_actions']['new_reply'] = $raw_actions['new_reply'];
		}

		return $this->createJsonResponse($data);
	}

	public function ajaxGetMacroActionsAction()
	{
		$macro_id = $this->in->getUint('macro_id');
		$macro = $this->em->getRepository('DeskPRO:TicketMacro')->find($macro_id);

		$descriptions = $macro->getActionDescriptions();

		return $this->createJsonResponse(array(
			'macro_id'      => $macro['id'],
			'macro_actions' => $macro['actions'],
			'descriptions'  => $descriptions,
		));
	}

	public function ajaxSaveActionsAction()
	{
		$ticket_ids = $this->in->getCleanValueArray('result_ids', 'uint', 'discard');

		// Accept changes to apply for previewing
		// - We just apply the changes but dont save them, they'll be
		//   properly displayed in the listing.
		$actions = $this->in->getCleanValueArray('actions', 'raw', 'string');

		$actions_builder = RuleBuilder::newTermsBuilder();
		$actions_set = $actions_builder->readForm($this->in->getCleanValueArray('actions_set', 'raw', 'raw'));

		$tickets = $this->em->getRepository('DeskPRO:Ticket')->getTicketsResultsFromIds($ticket_ids);

		$macro = false;
		if ($macro_id = $this->in->getUint('run_macro_id')) {
			$macro = $this->em->find('DeskPRO:TicketMacro', $macro_id);
		}

		$permission_errors = array();
		$success = array();

		if (($actions || $actions_set || $macro) && $tickets) {

			if ($macro) {
				foreach ($tickets as $ticket) {
					$actions_collection = $macro->getActionsCollection($ticket);

					$this->db->beginTransaction();
					try {
						if (!$actions_collection->applyCheckPermission($ticket, $this->person)) {
							$permission_errors[] = $ticket->getId();
							continue;
						}
						$actions_collection->apply($ticket->getTicketLogger(), $ticket, $this->person);
						$this->em->persist($ticket);
						$this->em->flush();
						$ticket->getTicketLogger()->done();
						$this->db->commit();
					} catch (\Exception $e) {
						$this->db->rollback();
						throw $e;
					}
				}
			} else {
				$factory = new ActionsFactory();
				$collection = new ActionsCollection();

				foreach ($actions as $name => $opt) {
					$action = $factory->createFromForm($name, $opt);
					$collection->add($action);
				}

				foreach ($actions_set as $info) {
					$action = $factory->createFromForm($info['type'], $info['options']);
					if ($action) {
						$collection->add($action);
					}
				}

				$collection->applyAllModifiers();

				foreach ($tickets as $ticket) {

					if (!$this->person->PermissionsManager->TicketChecker->canView($ticket)) {
						$permission_errors[] = $ticket->getId();
						continue;
					}

					$this->db->beginTransaction();
					try {
						if (!$collection->applyCheckPermission($ticket, $this->person)) {
							$permission_errors[] = $ticket->getId();
							continue;
						}
						$collection->apply(null, $ticket, $this->person);
						$this->em->persist($ticket);
						$ticket->_saveTicketLogs();
						$this->em->flush();
						$this->db->commit();

						$success[] = $ticket->getId();
					} catch (\Exception $e) {
						$this->em->rollback();
						throw $e;
					}
				}
			}
		}

		$client_messages = false;
		if ($this->in->getUint('client_messages_since') > 0) {
			$client_messages = $this->em->getRepository('DeskPRO:ClientMessage')->getMessageData(
				$this->person,
				$this->session,
				$this->in->getUint('client_messages_since')
			);
		}

		return $this->createJsonResponse(array(
			'success'          => true,
			'success_tickets' => $success,
			'failed_tickets'  => $permission_errors,
			'client_messages' => $client_messages,
		));
	}
}
