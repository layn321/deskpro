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

use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\Tickets\TicketActions\CollectionModifierInterface;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Tickets\TicketChangeTracker;

/**
 * A collection of ticket actions
 */
class ActionsCollection
{
	/**
	 * @var Application\DeskPRO\Tickets\TicketActions\ActionInterface[]
	 */
	protected $actions = array();

	/**
	 * @var Application\DeskPRO\Tickets\TicketActions\CollectionModifierInterface[]
	 */
	protected $applied_modifiers = array();

	/**
	 * Its possible a modifier of a single type to be added multiple times. This is
	 * an array of unique names.
	 *
	 * @var array
	 */
	protected $applied_modifier_types = array();

	/**
	 * @var bool
	 */
	protected $was_stopped = false;

	/**
	 * True when actions broke the chain early
	 *
	 * @return bool
	 */
	public function isBroken()
	{
		return $this->was_stopped;
	}

	public function add($action_or_modifier, array $metadata = array())
	{
		if ($action_or_modifier instanceof ActionInterface) {
			$this->addAction($action_or_modifier, $metadata);
		} elseif ($action_or_modifier instanceof CollectionModifierInterface) {
			$this->applyCollectionModifier($action_or_modifier);
		}
	}


	/**
	 * @return int
	 */
	public function countActions()
	{
		return count($this->actions);
	}


	/**
	 * Add a new action
	 *
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $action
	 */
	public function addAction(ActionInterface $action, array $metadata = array(), $prepend = false)
	{
		$name = $action->getActionName();

		if (isset($this->actions[$name])) {
			$old_action = $this->actions[$name];
			$new_action = $old_action->merge($action);
			if ($new_action && $new_action instanceof ActionInterface) {
				$action = $new_action;
			}
		}

		$action->setMetaData($metadata);

		$this->actions[$name] = $action;

		if (!$prepend && $action->doPrepend()) {
			$prepend = true;
		}

		if ($prepend) {
			unset($this->actions[$name]);
			\Orb\Util\Arrays::unshiftAssoc($this->actions, $name, $action);
		}
	}


	/**
	 * Apply a collection modifier
	 *
	 * @param \Application\DeskPRO\Tickets\TicketActions\CollectionModifierInterface $modifier
	 */
	public function applyCollectionModifier(CollectionModifierInterface $modifier)
	{
		$name = get_class($modifier);
		$this->applied_modifier_types[$name] = $name;
		$this->applied_modifiers[] = $modifier;
	}

	/**
	 * Applies all modifiers. Should be run when the collection is finalized
	 * to ensure that the order of modifiers doesn't affect anything.
	 */
	public function applyAllModifiers()
	{
		foreach ($this->applied_modifiers AS $modifier) {
			$modifier->modifyCollection($this);
		}
	}


	/**
	 * Check if a certain action type is set
	 *
	 * @return bool
	 */
	public function hasActionType($name)
	{
		if (strpos($name, '\\') === false) {
			$name = 'Application\\DeskPRO\\Tickets\\TicketActions\\' . $name . 'Action';
		}

		return isset($this->actions[$name]);
	}


	/**
	 * Check if a certain action type is set
	 *
	 * @return bool
	 */
	public function hasModifierType($name)
	{
		if (strpos($name, '\\') === false) {
			$name = 'Application\\DeskPRO\\Tickets\\TicketActions\\' . $name . 'Modifier';
		}

		return isset($this->applied_modifier_types[$name]);
	}


	/**
	 * Get a set action by name
	 *
	 * @throws \InvalidArgumentException When the action doesnt exist
	 * @param  $name
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function getActionType($name)
	{
		if (strpos($name, '\\') === false) {
			$name = 'Application\\DeskPRO\\Tickets\\TicketActions\\' . $name . 'Action';
		}

		if (!$this->hasActionType($name)) {
			throw new \InvalidArgumentException("No action type `$name` exists");
		}

		return $this->actions[$name];
	}


	/**
	 * Remove an action type from the collection, and return it
	 *
	 * @throws \InvalidArgumentException When action doesnt exist
	 * @param  string $name
	 * @return Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function removeActionType($name)
	{
		if (strpos($name, '\\') === false) {
			$name = 'Application\\DeskPRO\\Tickets\\TicketActions\\' . $name . 'Action';
		}

		if (!$this->hasActionType($name)) {
			throw new \InvalidArgumentException("No action type `$name` exists");
		}

		$action = $this->actions[$name];
		unset($this->actions[$name]);

		return $action;
	}


	/**
	 * Get an array of set action types
	 *
	 * @return array
	 */
	public function getActionTypeNames()
	{
		return array_keys($this->actions);
	}


	/**
	 * Get an array of ticket actions
	 *
	 * @return array
	 */
	public function getActions()
	{
		return $this->actions;
	}


	/**
	 * Checks to see if the $person_context person can perform all of the actions in the collection
	 *
	 * @param \Application\DeskPRO\Tickets\TicketChangeTracker $ticket_tracker
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @param \Application\DeskPRO\Entity\Person $person_context
	 * @param null $logger
	 * @return bool
	 */
	public function applyCheckPermission(Ticket $ticket, Person $person_context)
	{
		// Load up common helpers
		$person_context->loadHelper('Agent');
		$person_context->loadHelper('AgentTeam');
		$person_context->loadHelper('AgentPermissions');
		$person_context->loadHelper('PermissionsManager');
		$person_context->loadHelper('HelpMessages');
		$person_context->loadHelper('AgentPrefs');

		foreach ($this->actions as $action) {
			if ($action instanceof PersonContextInterface) {
				$action->setPersonContext($person_context);
			}

			if ($action instanceof PermissionableAction) {
				if (!$action->checkPermission($ticket, $person_context)) {
					return false;
				}
			}
		}

		return true;
	}


	/**
	 * Apply actions in this collection to $ticket, using $person_context as
	 * the context on actions that require it.
	 *
	 * @param \Application\DeskPRO\Tickets\TicketChangeTracker
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @param \Application\DeskPRO\Entity\Person $person_context
	 */
	public function apply(TicketChangeTracker $ticket_tracker = null, Ticket $ticket, Person $person_context = null, $logger = null)
	{
		$this->was_stopped = false;

		$this->applyAllModifiers();

		foreach ($this->actions as $action) {
			if ($person_context && $action instanceof PersonContextInterface) {
				$action->setPersonContext($person_context);
			}

			$time = microtime(true);

			$metadata = $action->getMetaData();
			if ($ticket_tracker && isset($metadata['trigger'])) {
				$ticket_tracker->setApplyingTrigger($metadata['trigger']);
			}
			if ($ticket_tracker && isset($metadata['sla'])) {
				$sla_status = isset($metadata['sla_status']) ? $metadata['sla_status'] : null;
				$ticket_tracker->setApplyingSla($metadata['sla'], $sla_status);
			}

			try {
				$action->apply($ticket);

				if ($ticket_tracker && isset($metadata['trigger'])) {
					$ticket_tracker->setApplyingTrigger(null);
				}
				if ($ticket_tracker && isset($metadata['sla'])) {
					$ticket_tracker->setApplyingSla(null, null);
				}
			} catch (\Exception $e) {
				$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);

				if ($logger) {
					$name = \Orb\Util\Util::getBaseClassname($action);
					$logger->log(sprintf("[$name] EXCEPTION (%s): %s %s", $einfo['session_name'], $einfo['exception_type'], $einfo['summary']), \Orb\Log\Logger::DEBUG);
				}

				throw $e;
			}

			if ($logger) {
				$name = \Orb\Util\Util::getBaseClassname($action);
				$logger->log(sprintf("[$name] Took %.4f seconds", microtime(true) - $time), \Orb\Log\Logger::DEBUG);
			}

			if ($action instanceof BreakableAction) {
				if ($action->shouldBreakAction()) {
					$this->was_stopped = true;
					break;
				}
			}
		}
	}


	/**
	 * Apply actions in this collection to a collection of $tickets, using $person_context
	 * as the context on actions that require it.
	 *
	 * @param \Application\DeskPRO\Entity\Ticket[] $tickets
	 * @param \Application\DeskPRO\Entity\Person $person_context
	 */
	public function applyToCollection(array $tickets, Person $person_context)
	{
		foreach ($tickets as $t) {
			$this->apply($t, $person_context);
		}
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @param \Application\DeskPRO\Entity\Person $person_context
	 */
	public function getApplyActions(Ticket $ticket, Person $person_context)
	{
		$actions = array();

		foreach ($this->actions as $action) {
			if ($action instanceof PersonContextInterface) {
				$action->setPersonContext($person_context);
			}

			$actions = array_merge($actions, $action->getApplyActions($ticket));
		}

		return $actions;
	}


	/**
	 * @param array $order
	 */
	public function sortActions(array $order)
	{
		if (!isset($order['default'])) {
			$order['default'] = 0;
		}

		uasort($this->actions, function ($a, $b) use ($order) {
			$a_name = \Orb\Util\Util::getBaseClassname($a);
			$b_name = \Orb\Util\Util::getBaseClassname($b);

			$a_default = $a->doPrepend() ? $order['prepend'] : $order['default'];
			$b_default = $b->doPrepend() ? $order['prepend'] : $order['default'];

			$a_order = isset($order[$a_name]) ? $order[$a_name] : $a_default;
			$b_order = isset($order[$b_name]) ? $order[$b_name] : $b_default;

			if ($a_order == $b_order) return 0;
			return $a_order < $b_order ? -1 : 1;
		});
	}


	public function getDescriptions($as_html)
	{
		$desc = array();
		foreach ($this->actions as $action) {
			$desc[] = $action->getDescription($as_html);
		}

		foreach ($this->applied_modifiers as $mod) {
			$desc[] = $mod->getDescription($as_html);
		}

		$desc = \Orb\Util\Arrays::removeFalsey($desc);

		if ($as_html) {
			foreach ($desc as &$d) {
				$d = str_replace(
					array('<error>', '</error>'),
					array('<span class="term-error">', '</span>'),
					$d
				);
			}
		}

		return $desc;
	}
}
