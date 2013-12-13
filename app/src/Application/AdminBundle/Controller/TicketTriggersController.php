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

use Application\DeskPRO\Entity;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

use Application\DeskPRO\Entity\TicketTrigger;
use Application\AdminBundle\Form\EditTicketTriggerType;
use Application\DeskPRO\UI\RuleBuilder;

class TicketTriggersController extends AbstractController
{
	############################################################################
	# list-triggers
	############################################################################

	public function listTriggersAction($list_type)
	{
		switch ($list_type) {
			case 'new':
				$types = array(
					'new.email.user',
					'new.email.agent',
					'new.web.agent',
					'new.web.agent.portal',
					'new.web.user',
					'new.web.user.portal',
					'new.web.user.widget',
					'new.web.user.embed',
					'new.web.api',
					'new'
				);
				$list_tpl = 'AdminBundle:TicketTriggers:list-triggers-new.html.twig';
				break;

			case 'update':
				$types = array('update.agent', 'update.user', 'update.api');
				$list_tpl = 'AdminBundle:TicketTriggers:list-triggers-update.html.twig';
				break;

			default:
				return $this->redirectRoute('admin_tickettriggers', array('list_type' => 'new'));
		}

		$triggers = $this->em->getRepository('DeskPRO:TicketTrigger')->getGroupedTriggers($types);

		$triggers['new.web.user_any'] = array();
		if (!empty($triggers['new.web.user'])) $triggers['new.web.user_any'] = array_merge($triggers['new.web.user_any'], $triggers['new.web.user']);
		if (!empty($triggers['new.web.user.portal'])) $triggers['new.web.user_any'] = array_merge($triggers['new.web.user_any'], $triggers['new.web.user.portal']);
		if (!empty($triggers['new.web.user.widget'])) $triggers['new.web.user_any'] = array_merge($triggers['new.web.user_any'], $triggers['new.web.user.widget']);
		if (!empty($triggers['new.web.user.embed'])) $triggers['new.web.user_any'] = array_merge($triggers['new.web.user_any'], $triggers['new.web.user.embed']);

		// Need to sort the arary since we just messed up
		// ordering by merging all of the new.web* types
		usort($triggers['new.web.user_any'], function($a, $b) {
			return $a->run_order < $b->run_order ? -1 : 1;
		});

		$show_api_option = $this->em->getRepository('DeskPRO:ApiKey')->countApiKeys() > 0;

		return $this->render($list_tpl, array(
			'list_type'  => $list_type,
			'types'      => $types,
			'triggers'   => $triggers,
			'show_api_option' => $show_api_option
		));
	}

	public function listEscalationsAction()
	{
		$types = array(
			'time.open',
			'time.user_waiting',
			'time.total_user_waiting',
			'time.agent_waiting',
			'time.resolved',
			'sla.warning',
			'sla.fail'
		);
		$triggers = $this->em->getRepository('DeskPRO:TicketTrigger')->getGroupedTriggers($types);

		return $this->render('AdminBundle:TicketTriggers:list-escalations.html.twig', array(
			'triggers'   => $triggers,
		));
	}

	############################################################################
	# export triggers
	############################################################################

	public function exportTriggersAction()
	{
		return $this->render('AdminBundle:TicketTriggers:import-export.html.twig');
	}

	public function exportTriggersDownloadAction()
	{
		$data = $this->db->fetchAll("
			SELECT * FROM ticket_triggers
			ORDER BY id ASC
		");

		if (defined('JSON_PRETTY_PRINT')) {
			$data = json_encode($data, \JSON_PRETTY_PRINT);
		} else {
			$data = json_encode($data);
		}

		header('Content-Disposition: attachment; filename=triggers.json');
		header('Content-type: application/json; filename=triggers.json');
		$res = new \Symfony\Component\HttpFoundation\Response($data, 200);

		return $res;
	}

	public function importTriggersAction()
	{
		$clear = $this->in->getBool('clear');
		$keep_ids = $this->in->getBool('keep_ids');

		$exist_ids = null;
		if ($keep_ids && !$clear) {
			$exist_ids = $this->db->fetchAllCol("SELECT id FROM ticket_triggers");
			if ($exist_ids) {
				$exist_ids = array_combine($exist_ids, $exist_ids);
			}
		}

		$exist_sys = $this->db->fetchAllCol("SELECT sys_name FROM ticket_triggers WHERE sys_name IS NOT NULL");
		if ($exist_sys) {
			$exist_sys = array_combine($exist_sys, $exist_sys);
		}

		$file = $this->request->files->get('file-upload');

		if (!$file->isValid()) {
			return $this->redirectRoute('admin_tickettriggers');
		}

		$file_content = @file_get_contents($file->getRealPath());
		if (!$file_content) {
			return $this->redirectRoute('admin_tickettriggers');
		}

		$data = @json_decode($file_content, true);
		if (!$data) {
			throw $this->createNotFoundException("Invalid file");
		}

		$this->db->beginTransaction();

		try {
			$this->db->commit();

			if ($clear) {
				$this->db->executeUpdate("DELETE FROM ticket_triggers");
			}

			foreach ($data as $tr) {
				if (!empty($tr['sys_name'])) {
					if (!$clear && isset($exist_sys[$tr['sys_name']])) {
						continue;
					}
				}

				if ($keep_ids) {
					if ($keep_ids && isset($keep_ids[$tr['id']])) {
						continue;
					}
				} else {
					unset($tr['id']);
				}

				$this->db->insert('ticket_triggers', $tr);
			}

		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_tickettriggers');
	}

	############################################################################
	# edit trigger
	############################################################################

	public function editTriggerAction($id, $trigger_type = null)
	{
		if ($id) {
			$trigger = $this->em->find('DeskPRO:TicketTrigger', $id);
			if (!$trigger || $trigger->is_uneditable) {
				throw $this->createNotFoundException();
			}
		} else {
			$trigger = new TicketTrigger();

			if ($trigger_type == null) {
				return $this->redirectRoute('admin_tickettriggers_new', array('type' => 'new.email.user'));
			}

			$trigger->event_trigger = $trigger_type;
		}

		if ($trigger->getTriggerType() == 'escalation') {
			return $this->redirectRoute('admin_ticketescalations_edit', array('id' => $trigger->getId()));
		}

		return $this->render('AdminBundle:TicketTriggers:edit-trigger.html.twig', array(
			'trigger'      => $trigger,
			'term_options' => $this->em->getRepository('DeskPRO:TicketTrigger')->getTriggerTermOptions()
		));
	}

	public function editEscalationAction($id, $trigger_type = null)
	{
		if ($id) {
			$trigger = $this->em->find('DeskPRO:TicketTrigger', $id);
			if (!$trigger) {
				throw $this->createNotFoundException();
			}
		} else {
			$trigger = new TicketTrigger();

			if ($trigger_type == null) {
				return $this->redirectRoute('admin_ticketescalations_new', array('type' => 'time.open'));
			}

			$trigger->event_trigger = $trigger_type;
		}

		return $this->render('AdminBundle:TicketTriggers:edit-escalation.html.twig', array(
			'trigger'      => $trigger,
			'term_options' => $this->em->getRepository('DeskPRO:TicketTrigger')->getTriggerTermOptions(),
		));
	}

	public function saveTriggerAction($id)
	{
		if ($id) {
			$trigger = $this->em->find('DeskPRO:TicketTrigger', $id);
			if (!$trigger) {
				throw $this->createNotFoundException();
			}
		} else {
			$trigger = new TicketTrigger();
		}

		$trigger->title = $this->in->getString('trigger.title');
		$trigger->event_trigger = $this->in->getString('trigger.event_trigger');
		$trigger->event_trigger_options = $this->in->getCleanValueArray('trigger.event_trigger_options', 'string', 'discard');

		if ($this->in->getString('event_trigger_time')) {
			$time = $this->in->getString('event_trigger_time') . ' ' . $this->in->getString('event_trigger_scale');
			$trigger->setEventTriggerOption('time', $time);
		}

		$term_rules = RuleBuilder::newTermsBuilder();

		$trigger->terms = $term_rules->readForm($this->in->getCleanValueArray('terms', 'raw' , 'discard'));
		$trigger->terms_any = $term_rules->readForm($this->in->getCleanValueArray('terms_any', 'raw' , 'discard'));

		$action_rules = RuleBuilder::newActionsBuilder();
		$actions = $action_rules->readForm($this->in->getCleanValueArray('actions', 'raw' , 'discard'));

		$redirect_to = null;
		$tpl_types = array(
			'set_user_email_template_newticket' => 1,
			'user_newticket_agent' => 1,
			'set_user_email_template_newticket_validate' => 1,
			'set_agent_email_template_newticket' => 1,
			'set_user_email_template_newticket_agent' => 1,
			'set_user_email_template_newreply_agent' => 1,
			'set_agent_email_template_newreply_agent' => 1,
			'set_user_email_template_newreply_user' => 1,
			'set_agent_email_template_newreply_user' => 1,
			'send_user_email' => 1,
			'send_agent_email' => 1,
			'send_autoclose_warn_email' => 1,
		);
		foreach ($actions as &$_info) {
			if (isset($tpl_types[$_info['type']])) {
				if (isset($_info['options']['new_option']) && !empty($_info['options']['new_option'])) {
					$new_name = $_info['options']['new_option'];
					$new_name = preg_replace('#[^a-zA-Z0-9\-_]#', '_', $new_name);
					if (!$new_name) {
						$new_name = 'custom_template';
					}

					unset($_info['options']['new_option']);

					if (strpos($_info['type'], 'set_user_') !== false || strpos($_info['type'], 'send_user_email') !== false || strpos($_info['type'], 'send_agent_email') !== false || $_info['type'] == 'send_autoclose_warn_email') {
						$_info['options']['template_name'] = 'DeskPRO:emails_user:custom_' . $new_name . '.html.twig';
					} else {
						$_info['options']['template_name'] = 'DeskPRO:emails_agent:custom_' . $new_name . '.html.twig';
					}

					$variant = null;

					switch ($_info['type']) {
						case 'set_user_email_template_newticket': $variant = 'DeskPRO:emails_user:new-ticket.html.twig'; break;
						case 'user_newticket_agent': $variant = 'DeskPRO:emails_user:new-ticket-agent.html.twig'; break;
						case 'set_user_email_template_newticket_validate': $variant = 'DeskPRO:emails_user:new-ticket-validate.html.twig'; break;
						case 'set_agent_email_template_newticket': $variant = 'DeskPRO:emails_agent:new-ticket.html.twig'; break;
						case 'set_user_email_template_newticket_agent': $variant = 'DeskPRO:emails_user:new-ticket-agent.html.twig'; break;
						case 'set_user_email_template_newreply_agent': $variant = 'DeskPRO:emails_user:new-reply-agent.html.twig'; break;
						case 'set_agent_email_template_newreply_agent': $variant = 'DeskPRO:emails_agent:new-reply-agent.html.twig'; break;
						case 'set_user_email_template_newreply_user': $variant = 'DeskPRO:emails_user:new-reply-user.html.twig'; break;
						case 'set_agent_email_template_newreply_user': $variant = 'DeskPRO:emails_agent:new-reply-user.html.twig'; break;
						case 'send_user_email': $variant = 'DeskPRO:emails_user:blank.html.twig'; break;
						case 'send_agent_email': $variant = 'DeskPRO:emails_agent:blank.html.twig'; break;
						case 'send_autoclose_warn_email': $variant = 'DeskPRO:emails_user:ticket-autoclose-warn.html.twig'; break;
					}

					$redirect_to = $this->generateUrl('admin_templates_editemail', array('name' => $new_name, 'variant_of' => $variant));
				}
			}
		}

		$trigger->actions = $actions;

		$this->em->beginTransaction();
		$this->em->persist($trigger);
		$this->em->flush();
		$this->em->commit();

		if ($redirect_to) {
			return $this->redirect($redirect_to);
		}

		if ($trigger->getTriggerType() == 'escalation') {
			return $this->redirectRoute('admin_ticketescalations');
		} else {
			if (strpos($trigger->event_trigger, 'update.') === 0) {
				return $this->redirectRoute('admin_tickettriggers', array('list_type' => 'update'));
			} else {
				return $this->redirectRoute('admin_tickettriggers');
			}
		}
	}

	############################################################################
	# update-order
	############################################################################

	public function updateOrderAction()
	{
		$trigger_ids = $this->in->getCleanValueArray('trigger_ids', 'uint', 'discard');

		$x = 10;

		$this->db->beginTransaction();
		try {
			foreach ($trigger_ids as $id) {
				$this->db->update('ticket_triggers', array('run_order' => $x), array('id' => $id));
				$x += 10;
			}

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################
	# toggle-enabled
	############################################################################

	public function toggleEnabledAction()
	{
		$trigger = $this->em->find('DeskPRO:TicketTrigger', $this->in->getUint('trigger_id'));

		if ($trigger) {
			$trigger->is_enabled = $this->in->getBool('onoff');
			$this->em->persist($trigger);
			$this->em->flush();
		}

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################
	# delete
	############################################################################

	public function deleteAction($id, $auth)
	{
		$this->ensureAuthToken('delete_trigger', $auth);

		$trigger = $this->em->find('DeskPRO:TicketTrigger', $id);
		if (!$trigger || $trigger->sys_name) {
			throw $this->createNotFoundException();
		}

		$type = $trigger->getTriggerType();

		if ($trigger) {
			$this->db->beginTransaction();
			try {
				$this->em->remove($trigger);
				$this->em->flush();
				$this->db->commit();
			} catch (\Exception $e) {
				$this->db->rollback();
				throw $e;
			}
		}

		if ($type == 'escalation') {
			return $this->redirectRoute('admin_ticketescalations');
		} else {
			return $this->redirectRoute('admin_tickettriggers');
		}
	}
}
