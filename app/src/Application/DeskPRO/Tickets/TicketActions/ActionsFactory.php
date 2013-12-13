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
 * @subpackage Tickets
 */

namespace Application\DeskPRO\Tickets\TicketActions;

use Application\DeskPRO\App;
use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\Tickets\TicketActions\Mapper;
use Application\DeskPRO\Tickets\TicketActions\CollectionModifierInterface;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Validator\StringEmail;

/**
 * Creates action objects
 */
class ActionsFactory
{
	protected $global_options = array();

	protected $plugin_actions = null;

	public function addGlobalOption($name, $value)
	{
		$this->global_options[$name] = $value;
	}

	/**
	 * Create an action object from a posted form representation of an action.
	 * These are generally just an action name and a single value to represent the actions
	 * new value.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return object
	 */
	public function createFromForm($name, $value)
	{
		$name_id = null;
		$m = null;
		if (preg_match('#^(.*?)\[(.*?)\]$#', $name, $m)) {
			$name = $m[1];
			$name_id = $m[2];
		}

		if (!is_array($value)) {
			$value = array($name => $value);
		}

		$options = array();
		switch ($name) {
			case 'agent':
				$options['agent'] = $value['agent'];
				break;
			case 'agent_team':
				$options['agent_team'] = $value['agent_team'];
				break;
			case 'category':
				$options['category'] = $value['category'];
				break;
			case 'department':
				$options['department'] = $value['department'];
				break;
			case 'product':
				$options['product'] = $value['product'];
				break;
			case 'language':
				$options['language'] = $value['language'];
				break;
			case 'flag':
				$options['flag'] = $value['flag'];
				break;
			case 'priority':
				$options['priority'] = $value['priority'];
				break;
			case 'urgency':
				$options['num'] = $value['num'];
				break;
			case 'subject':
				$options['subject'] = $value['subject'];
				break;
			case 'urgency_set':
				$options['num'] = $value['num'];
				$options['allow_lower'] = isset($value['allow_lower']) && $value['allow_lower'] ? true : false;
				break;
			case 'workflow':
				$options['workflow'] = $value['workflow'];
				break;
			case 'status':
				$options['status'] = $value['status'];
				break;
			case 'hold':
				$options['is_hold'] = $value['is_hold'];
				break;
			case 'add_labels':
				$options['add_labels'] = array();
				if (is_array($value['labels'])) $options['add_labels'] = $value['labels'];
				elseif (!empty($value['labels'])) $options['add_labels'] = Strings::explodeTrim(',', $value['labels']);
				break;
			case 'remove_labels':
				$options['remove_labels'] = array();
				if (is_array($value['labels'])) $options['remove_labels'] = $value['labels'];
				elseif (!empty($value['labels'])) $options['remove_labels'] = Strings::explodeTrim(',', $value['labels']);
				break;
			case 'reply':
				if (empty($value['reply_text']) || !trim(strip_tags($value['reply_text']))) {
					return null;
				}
				$options['reply_text'] = $value['reply_text'];
				$options['attach_ids'] = !empty($value['attach_ids']) && is_array($value['attach_ids']) ? $value['attach_ids'] : array();
				$options['reply_pos']  = !empty($value['reply_pos']) ? $value['reply_pos'] : 'prepend';
				$options['person_id']  = !empty($value['person_id']) && $value['person_id'] ? $value['person_id'] : null;
				break;
			case 'reply_snippet':
				$options['snippet_id'] = $value['snippet_id'];
				$options['reply_pos'] = !empty($value['reply_pos']) ? $value['reply_pos'] : 'prepend';
				break;
			case 'add_participants':
				$options['add_participants'] = !empty($value['add_participants']) && is_array($value['add_participants']) ? $value['add_participants'] : array();
				break;
			case 'add_cc':
				$options['add_emails'] = !empty($value['add_emails']) ? $value['add_emails'] : '';
				break;
			case 'remove_participants':
				$options['remove_participants'] = !empty($value['remove_participants']) && is_array($value['remove_participants']) ? $value['remove_participantsq'] : array();
				break;
			case 'ticket_field':
				$field_manager = App::getSystemService('ticket_fields_manager');
				$field = $field_manager->getFieldFromId($name_id);

				if (!$field) {
					return null;
				}

				$options['field_manager'] = $field_manager;
				$options['field_def'] = $field;
				$options['set_value'] = $value;
				break;
			case 'people_field':
				$field_manager = App::getSystemService('person_fields_manager');
				$field = $field_manager->getFieldFromId($name_id);

				$options['field_manager'] = $field_manager;
				$options['field_def'] = $field;
				$options['set_value'] = $value;
				break;

			case 'set_gateway_address':
				$options['gateway_address_id'] = $value['gateway_address_id'];
				break;

			case 'set_from_address':
				$options['email_address'] = $value['email_address'];
				break;

			case 'set_from_name':
				$options['from_name'] = $value['name'];
				break;

			case 'set_from_address_agent':
				$options['email_address'] = $value['email_address'];
				break;

			case 'set_from_name_agent':
				$options['from_name'] = $value['name'];
				break;

			case 'set_initial_from_name':
				$options['pattern'] = $value['from_name'];
				$options['to_agent'] = true;
				$options['to_user'] = true;
				if (isset($value['to_whom']) && $value['to_whom']) {
					if ($value['to_whom'] == 'agent') {
						$options['to_user'] = false;
					} elseif ($value['to_whom'] == 'user') {
						$options['to_agent'] = false;
					}
				}
				break;

			case 'new_ticket':
				$options = array('mode' => isset($value['mode']) ? $value['mode'] : 'run');
				break;

			case 'add_agent_notify':
				$options = array('codes' => $value['codes']);
				break;

			case 'send_ticket_email':
				$options = array('message' => $value['message']);
				break;

			case 'call_webhook':
				$options = array('webhook_id' => $value['webhook_id']);
				break;

			case 'add_org_managers':
				$options = array();
				break;

			case 'send_org_managers_email':
				$options = array('message' => $value['message']);
				break;

			case 'send_autoclose_warn_email':
				$options = array('template_name' => $value['template_name']);
				break;

			case 'add_sla':
				$options = array('sla_id' => $value['sla_id']);
				break;

			case 'remove_sla':
				$options = array('sla_id' => $value['sla_id']);
				break;

			case 'set_sla_status':
				$options = array('sla_status' => $value['sla_status'], 'sla_id' => $value['sla_id']);
				break;

			case 'set_sla_complete':
				$options = array('sla_complete' => $value['sla_complete'], 'sla_id' => $value['sla_id']);
				break;

			case 'recalculate_sla_status':
				$options = array();
				break;

			case 'send_user_email':
				$options = array('template' => $value['template_name']);
				break;

			case 'send_agent_email':
				$options = array('template' => $value['template_name'], 'agents' => !empty($value['agents']) ? $value['agents'] : array());
				break;

			case 'set_user_email_template_newticket':
			case 'set_user_email_template_newticket_agent':
			case 'set_user_email_template_newticket_validate':
			case 'set_agent_email_template_newticket':
			case 'set_user_email_template_newticket_agent':
			case 'set_user_email_template_newreply_agent':
			case 'set_agent_email_template_newreply_agent':
			case 'set_user_email_template_newreply_user':
			case 'set_agent_email_template_newreply_user':
				$options = array('template' => $value['template_name']);
				break;

			default:
				if (strpos($name, 'set_email_template_') !== false) {
					$options = array(
						'tpl' => $value['tpl'],
						'tpl_type' => isset($value['tpl_type']) ? $value['tpl_type'] : ''
					);
				} else {
					$options = $value;
				}
		}

		return $this->create($name, $options);
	}

	public function createFromInfo(array $action_info)
	{
		return $this->createFromForm($action_info['type'], $action_info['options']);
	}

	public function create($name, array $options)
	{
		$class = str_replace('_', '-', $name);
		$class = ucfirst(Strings::dashToCamelCase($class));
		$class = 'Application\\DeskPRO\\Tickets\\TicketActions\\' . $class;

		$options = array_merge($this->global_options, $options);

		$action_class = $class . 'Action';
		$modifier_class = $class . 'Modifier';

		if (class_exists($action_class)) {
			return $this->createActionObject($action_class, $options);
		} elseif (class_exists($modifier_class)) {
			return $this->createModifierObject($modifier_class, $options);
		}

		$plugin_action = $this->getPluginAction($name);
		if ($plugin_action) {
			$action_class = $plugin_action['action_class'];
			$options = $plugin_action->getSetupObject()->filterActionOptions($options);
			return $this->createActionObject($action_class, $options);
		}

		return new NullAction();
	}

	public function createActionObject($action_class, array $options)
	{
		$method_refl = new \ReflectionMethod($action_class, '__construct');
		$args = Util::getFunctionParamsFromArray($method_refl, $options);

		$obj = Util::callUserConstructorArray($action_class, $args);
		return $obj;
	}

	public function createModifierObject($action_class, array $options)
	{
		$method_refl = new \ReflectionMethod($action_class, '__construct');
		$args = Util::getFunctionParamsFromArray($method_refl, $options);

		$obj = Util::callUserConstructorArray($action_class, $args);
		return $obj;
	}

	protected function getPluginAction($name)
	{
		if ($this->plugin_actions === null) {
			$this->plugin_actions = App::getEntityRepository('DeskPRO:TicketTriggerPluginActions')->getActivePluginActions(true);
		}

		if (isset($this->plugin_actions[$name])) {
			return $this->plugin_actions[$name];
		} else {
			return false;
		}
	}
}
