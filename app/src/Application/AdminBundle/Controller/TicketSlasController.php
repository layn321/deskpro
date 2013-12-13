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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\UI\RuleBuilder;

class TicketSlasController extends AbstractController
{
	public function listAction()
	{
		return $this->render('AdminBundle:TicketSlas:list.html.twig', array(
			'slas' => $this->_getSlaRepository()->getAllSlas(),
			'graph_data' => $this->em->getRepository('DeskPRO:TicketSla')->getTicketSlaAdminGraphData()
		));
	}

	public function editAction($sla_id = 0)
	{
		if ($sla_id) {
			$sla = $this->_getSlaOr404($sla_id);
		} else {
			$sla = new Entity\Sla();
			$sla->work_days = array(1,2,3,4,5);
			$sla->work_start = 9 * 3600; //9am
			$sla->work_end = 17 * 3600; //5pm
			$sla->work_timezone = App::getSetting('core.default_timezone');
		}

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken();

			$action_proc_info = array();

			$holidays = $this->in->getCleanValueArray('work_holidays', 'raw', 'discard');
			$add_all_holidays = array();
			foreach ($holidays AS $holiday) {
				if (!empty($holiday['add_all'])) {
					$add_all_holidays[] = $holiday;
				}
			}

			$sla->title = $this->in->getString('title');
			if (!$sla->title) {
				$sla->title = 'No Title';
			}
			$sla->sla_type = $this->in->getString('sla_type');
			$sla->active_time = $this->in->getString('active_time');
			if ($sla->active_time == 'work_hours') {
				$sla->work_start = $this->in->getUint('work_start_hour') * 3600 + $this->in->getUint('work_start_minute') * 60;
				$sla->work_end =  $this->in->getUint('work_end_hour') * 3600 + $this->in->getUint('work_end_minute') * 60;
				$sla->work_days = $this->in->getCleanValueArray('work_days', 'uint', 'discard');
				$sla->work_timezone = $this->in->getString('work_timezone');

				$sla->resetHolidays();
				foreach ($holidays AS $holiday) {
					$sla->addHoliday(
						$holiday['name'],
						$holiday['day'],
						$holiday['month'],
						$holiday['year']
					);
				}
			}

			$sla->apply_type = $this->in->getString('apply_type');

			if ($sla->apply_type == 'priority') {
				$apply_priority_id = $this->in->getUint('apply_priority_id');
				$sla->apply_priority = App::getEntityRepository('DeskPRO:TicketPriority')->find($apply_priority_id);
			} else {
				$sla->apply_priority = null;
			}

			if ($sla->apply_type == 'people_orgs') {
				$person_ids = preg_split('/,\s*/', $this->in->getString('person_ids'), -1, PREG_SPLIT_NO_EMPTY);
				$people = $this->em->getRepository('DeskPRO:Person')->getByIds($person_ids);
				$sla->setPeople($people);

				$organization_ids = preg_split('/,\s*/', $this->in->getString('organization_ids'), -1, PREG_SPLIT_NO_EMPTY);
				$organizations = $this->em->getRepository('DeskPRO:Organization')->getByIds($organization_ids);
				$sla->setOrganizations($organizations);
			} else {
				$sla->setPeople(array());
				$sla->setOrganizations(array());
			}

			$this->em->beginTransaction();

			$this->em->persist($sla);
			$this->em->flush();

			// setup warning trigger - must be done after saving as we need the ID
			if (!$sla->warning_trigger) {
				$warning_trigger = new Entity\TicketTrigger();
				$sla->warning_trigger = $warning_trigger;
			} else {
				$warning_trigger = $sla->warning_trigger;
				$warning_trigger->addPropertyChangedListener(App::getOrm()->getUnitOfWork());
			}

			$warning_trigger->title = $sla->title . " - SLA Warning";
			$warning_trigger->event_trigger = 'sla.warning';
			$time = $this->in->getString('sla_warning_time') . ' ' . $this->in->getString('sla_warning_scale');
			$warning_trigger->setEventTriggerOption('time', $time);
			$warning_trigger->terms = array(
				array('type' => 'sla_status', 'op' => 'is', 'options' => array('sla_status' => 'warn', 'sla_id' => $sla->id)),
			);

			$action_rules = RuleBuilder::newActionsBuilder();
			$actions = $action_rules->readForm($this->in->getCleanValueArray('warning_actions', 'raw' , 'discard'));
			$actions[] = array('type' => 'recalculate_sla_status', 'options' => array());

			$actions = Entity\TicketTrigger::passActionsArray($actions, $action_proc_info);

			$warning_trigger->actions = $actions;

			$this->em->persist($warning_trigger);

			// setup fail trigger - must be done after saving as we need the ID
			if (!$sla->fail_trigger) {
				$fail_trigger = new Entity\TicketTrigger();
				$sla->fail_trigger = $fail_trigger;
			} else {
				$fail_trigger = $sla->fail_trigger;
				$fail_trigger->addPropertyChangedListener(App::getOrm()->getUnitOfWork());
			}

			$fail_trigger->title = $sla->title . " - SLA Failure";
			$fail_trigger->event_trigger = 'sla.fail';
			$time = $this->in->getString('sla_fail_time') . ' ' . $this->in->getString('sla_fail_scale');
			$fail_trigger->setEventTriggerOption('time', $time);
			$fail_trigger->terms = array(
				array('type' => 'sla_status', 'op' => 'is', 'options' => array('sla_status' => 'fail', 'sla_id' => $sla->id)),
			);

			$warning_time_err = false;
			if ($warning_trigger->getOptionSeconds() >= $fail_trigger->getOptionSeconds()) {
				$warning_time_err = true;
				$time = ($this->in->getString('sla_fail_time')/2) . ' ' . $this->in->getString('sla_warning_scale');
				$warning_trigger->setEventTriggerOption('time', $time);
			}

			$action_rules = RuleBuilder::newActionsBuilder();
			$actions = $action_rules->readForm($this->in->getCleanValueArray('fail_actions', 'raw' , 'discard'));
			$actions[] = array('type' => 'recalculate_sla_status', 'options' => array());

			$actions = Entity\TicketTrigger::passActionsArray($actions, $action_proc_info);

			$fail_trigger->actions = $actions;

			$this->em->persist($fail_trigger);

			// setup apply trigger - must be done after saving as we need the ID
			if ($sla->apply_type == 'criteria') {
				if (!$sla->apply_trigger) {
					$apply_trigger = new Entity\TicketTrigger();
					$sla->apply_trigger = $apply_trigger;
				} else {
					$apply_trigger = $sla->apply_trigger;
					$apply_trigger->addPropertyChangedListener(App::getOrm()->getUnitOfWork());
				}

				$apply_trigger->title = "Apply SLA " . $sla->title;
				$apply_trigger->event_trigger = 'new';

				$term_rules = RuleBuilder::newTermsBuilder();
				$apply_trigger->terms = $term_rules->readForm($this->in->getCleanValueArray('apply_criteria', 'raw' , 'discard'));

				$apply_trigger->actions = array(
					array('type' => 'add_sla', 'options' => array('sla_id' => $sla->id)),
				);

				$this->em->persist($apply_trigger);
			} else {
				if ($sla->apply_trigger) {
					$this->em->remove($sla->apply_trigger);
				}
				$sla->apply_trigger = null;
			}

			$this->em->persist($sla);
			$this->em->flush();

			if ($add_all_holidays) {
				$add_slas = $this->_getSlaRepository()->getAllSlas();
				foreach ($add_slas AS $add_sla) {
					if ($add_sla->id == $sla->id) {
						continue;
					}

					foreach ($add_all_holidays AS $holiday) {
						$add_sla->addHoliday(
							$holiday['name'],
							$holiday['day'],
							$holiday['month'],
							$holiday['year']
						);
					}

					$this->em->persist($add_sla);
				}

				$this->em->flush();
			}

			$this->em->commit();

			$this->sendAgentReloadSignal();

			if ($warning_time_err) {
				return $this->redirectRoute('admin_tickets_sla_edit', array('sla_id' => $sla->id, 'show_warning_time_err' => 1));
			}

			if (!empty($action_proc_info['new_templates'])) {
				$first = array_shift($action_proc_info['new_templates']);
				return $this->redirectRoute('admin_templates_editemail', array('name' => $first));
			}

			return $this->redirectRoute('admin_tickets_slas');
		}

		$action_trigger = new \Application\DeskPRO\Entity\TicketTrigger();
		$action_trigger->event_trigger = 'sla.warning';

		$criteria_trigger = new \Application\DeskPRO\Entity\TicketTrigger();
		$criteria_trigger->event_trigger = 'new';

		$current_year = date('Y');
		$years = array($current_year, $current_year + 1, $current_year + 2, $current_year + 3);

		$selected_people = array();
		foreach ($sla->people AS $person) {
			$selected_people[$person->id] = $person->display_name;
		}

		$selected_organizations = array();
		foreach ($sla->organizations AS $organization) {
			$selected_organizations[$organization->id] = $organization->name;
		}

		$translator = App::getTranslator();

		$default_work_hours = unserialize(App::getSetting('core_tickets.work_hours'));

		return $this->render('AdminBundle:TicketSlas:edit.html.twig', array(
			'sla' => $sla,

			'selected_people' => $selected_people,
			'selected_organizations' => $selected_organizations,

			'action_trigger' => $action_trigger,
			'criteria_trigger' => $criteria_trigger,
			'term_options' => $this->em->getRepository('DeskPRO:TicketTrigger')->getTriggerTermOptions(),

			'priorities' => $this->em->getRepository('DeskPRO:TicketPriority')->getNames(),

			'default_work_hours' => $default_work_hours,
			'show_warning_time_err' => $this->in->getBool('show_warning_time_err'),

			'years' => $years,
			'months' => array(
				1 => $translator->phrase('agent.time.short-month_january'),
				2 => $translator->phrase('agent.time.short-month_february'),
				3 => $translator->phrase('agent.time.short-month_march'),
				4 => $translator->phrase('agent.time.short-month_april'),
				5 => $translator->phrase('agent.time.short-month_may'),
				6 => $translator->phrase('agent.time.short-month_june'),
				7 => $translator->phrase('agent.time.short-month_july'),
				8 => $translator->phrase('agent.time.short-month_august'),
				9 => $translator->phrase('agent.time.short-month_september'),
				10 => $translator->phrase('agent.time.short-month_october'),
				11 => $translator->phrase('agent.time.short-month_november'),
				12 => $translator->phrase('agent.time.short-month_december'),
			),
			'timezones' => \DateTimeZone::listIdentifiers()
		));
	}

	public function deleteAction($sla_id)
	{
		$this->ensureRequestToken('delete_sla');

		$sla = $this->_getSlaOr404($sla_id);

		$this->em->remove($sla);
		$this->em->flush();

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_tickets_slas');
	}

	############################################################################

	/**
	 * @param integer $id
	 *
	 * @return \Application\DeskPRO\Entity\Sla
	 */
	protected function _getSlaOr404($id)
	{
		$data = $this->em->getRepository('DeskPRO:Sla')->find($id);
		if (!$data) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no SLA with ID $id");
		}

		return $data;
	}

	/**
	 * @return \Application\DeskPRO\EntityRepository\Sla
	 */
	protected function _getSlaRepository()
	{
		return $this->em->getRepository('DeskPRO:Sla');
	}
}
