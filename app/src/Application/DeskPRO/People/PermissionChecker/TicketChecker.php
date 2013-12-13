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
 * @category Tickets
 */

namespace Application\DeskPRO\People\PermissionChecker;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Ticket;

use Orb\Util\Arrays;

class TicketChecker extends AbstractChecker
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	protected function init()
	{
		$this->person->loadHelper('Agent');
	}

	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return bool
	 */
	public function canView(Ticket $ticket)
	{
		if (!$this->person->hasPerm('agent_tickets.use')) {
			return false;
		}

		#------------------------------
		# If the user is part of the ticket
		# then we know right away they can view
		#------------------------------

		if ($ticket->agent && $ticket->agent->id == $this->person->id) {
			return true;
		}

		if ($ticket->agent_team && $this->person->getHelper('Agent')->isTeamMember($ticket->agent_team->id)) {
			return true;
		}

		if ($ticket->hasParticipantPerson($this->person)) {
			return true;
		}

		#------------------------------
		# Can't view certain deps
		#------------------------------

		if ($ticket->department && !$this->person->getHelper('AgentPermissions')->isDepartmentAllowed($ticket->department)) {
			return false;
		}

		#------------------------------
		# Cant view unassigned
		#------------------------------

		if (!$ticket->agent && !$this->person->hasPerm('agent_tickets.view_unassigned')) {
			return false;
		}

		#------------------------------
		# Cant view others
		#------------------------------

		if ($ticket->agent && !$this->person->hasPerm('agent_tickets.view_others')) {
			return false;
		}

		// If we got here, then we're allowed
		return true;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return bool
	 */
	public function canDelete(Ticket $ticket)
	{
		if (!$this->canView($ticket)) {
			return false;
		}

		#------------------------------
		# Can delete own
		#------------------------------

		if ($this->person->hasPerm('agent_tickets.delete_own')) {
			if ($ticket->agent && $ticket->agent->id == $this->person->id) {
				return true;
			}

			if ($ticket->agent_team && $this->person->getHelper('Agent')->isTeamMember($ticket->agent_team->id)) {
				return true;
			}
		}

		#------------------------------
		# Can delete unassigned
		#------------------------------

		if (!$ticket->agent && $this->person->hasPerm('agent_tickets.delete_unassigned')) {
			return true;
		}

		#------------------------------
		# Can delete others
		#------------------------------

		if ($ticket->agent && $this->person->hasPerm('agent_tickets.delete_assigned')) {
			return true;
		}

		#------------------------------
		# Can delete others
		#------------------------------

		if ($ticket->agent && $this->person->hasPerm('agent_tickets.delete_others')) {
			return true;
		}

        #------------------------------
        # Can delete followed
        #------------------------------

        if ($ticket->hasParticipantPerson($this->person) && $this->person->hasPerm('agent_tickets.delete_followed')) {
            return true;
        }


		#------------------------------
		# Cant delete
		#------------------------------

		return false;
	}


	/**
	 * @return bool
	 */
	public function canDeleteAny()
	{
		return ($this->person->hasPerm('agent_tickets.delete_own') || $this->person->hasPerm('agent_tickets.delete_unassigned') || $this->person->hasPerm('agent_tickets.delete_assigned') || $this->person->hasPerm('agent_tickets.delete_followed'));
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return bool
	 */
	public function canReply(Ticket $ticket)
	{
		if (!$this->canView($ticket)) {
			return false;
		}

		#------------------------------
		# Can delete own
		#------------------------------

		if ($this->person->hasPerm('agent_tickets.reply_own')) {
			if ($ticket->agent && $ticket->agent->id == $this->person->id) {
				return true;
			}

			if ($ticket->agent_team && $this->person->getHelper('Agent')->isTeamMember($ticket->agent_team->id)) {
				return true;
			}
		}

		#------------------------------
		# Can delete unassigned
		#------------------------------

		if (!$ticket->agent && $this->person->hasPerm('agent_tickets.reply_unassigned')) {
			return true;
		}

		#------------------------------
		# Can delete others
		#------------------------------

		if ($ticket->agent && $this->person->hasPerm('agent_tickets.reply_others')) {
			return true;
		}

        #------------------------------
        # Can reply to followed
        #------------------------------

        if ($ticket->hasParticipantPerson($this->person) && $this->person->hasPerm('agent_tickets.reply_to_followed')) {
            return true;
        }

		#------------------------------
		# Cant delete
		#------------------------------

		return false;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function canSetClosed(Ticket $ticket)
	{
		if (!$this->person->hasPerm('agent_tickets.modify_set_closed')) {
			return false;

		}
		if ($ticket->status == 'resolved' AND ($this->canModify($ticket, 'modify_set_awaiting_user') || $this->canModify($ticket, 'modify_set_awaiting_agent'))) {
			return true;
		} elseif ($this->canModify($ticket, 'modify_set_resolved')) {
			return true;
		}

		return false;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return bool
	 */
	public function canModify(Ticket $ticket, $op)
	{
		if (!$this->canView($ticket)) {
			return false;
		}


		#------------------------------
		# Figure out which set of permissions
		# the current ticket falls into
		#------------------------------

		// Own tickets
		if (($ticket->agent && $ticket->agent->id == $this->person->id) || $ticket->agent_team && $this->person->getHelper('Agent')->isTeamMember($ticket->agent_team->id)) {
			$set_suffix = 'own';

		// Unassigned tickets
		} elseif (!$ticket->agent && !$ticket->agent_team) {
			$set_suffix = 'unassigned';

		// Other
		} else if($ticket->hasParticipantPerson($this->person)) {
            $set_suffix = 'followed';
        }
        else {
			$set_suffix = 'others';
		}

		$perm_gloabl   = 'agent_tickets.modify_' . $set_suffix;
		$perm_specific = 'agent_tickets.modify_' . $op . '_' . $set_suffix;

		if ($this->person->hasPerm($perm_gloabl) || $this->person->hasPerm($perm_specific)) {
			return true;
		}

		return false;
	}


	/**
	 * Check if the user can modify (or delete) a message
	 *
	 * @param Ticket $ticket
	 */
	public function canEditMessages(Ticket $ticket)
	{
		if (!$this->canView($ticket)) {
			return false;
		}

		#------------------------------
		# Can delete own
		#------------------------------

		if ($this->person->hasPerm('agent_tickets.modify_messages_own')) {
			if ($ticket->agent && $ticket->agent->id == $this->person->id) {
				return true;
			}

			if ($ticket->agent_team && $this->person->getHelper('Agent')->isTeamMember($ticket->agent_team->id)) {
				return true;
			}
		}

		#------------------------------
		# Can delete unassigned
		#------------------------------

		if (!$ticket->agent && $this->person->hasPerm('agent_tickets.modify_messages_unassigned')) {
			return true;
		}

		#------------------------------
		# Can delete others
		#------------------------------

		if ($ticket->agent && $this->person->hasPerm('agent_tickets.modify_messages_assigned')) {
			return true;
		}

		#------------------------------
		# Can delete others
		#------------------------------

		if ($ticket->agent && $this->person->hasPerm('agent_tickets.modify_messages_others')) {
			return true;
		}

        #------------------------------
        # Can delete followed
        #------------------------------

        if ($ticket->hasParticipantPerson($this->person) && $this->person->hasPerm('agent_tickets.modify_messages_followed')) {
            return true;
        }


		#------------------------------
		# Cant delete
		#------------------------------

		return false;
	}


	/**
	 * Check if two tickets can be merged. To be able to merge, both tickets must give try for the 'merge' permission.
	 *
	 * @param Ticket $ticket1
	 * @param Ticket $ticket2
	 * @return bool
	 */
	public function canMerge(Ticket $ticket1, Ticket $ticket2)
	{
		foreach (array($ticket1, $ticket2) as $ticket) {
			if (($ticket->agent && $ticket->agent->id == $this->person->id) || $ticket->agent_team && $this->person->getHelper('Agent')->isTeamMember($ticket->agent_team->id)) {
				$set_suffix = 'own';
			} elseif (!$ticket->agent && !$ticket->agent_team) {
				$set_suffix = 'unassigned';
			} else if($ticket->hasParticipantPerson($this->person)) {
				$set_suffix = 'followed';
			} else {
				$set_suffix = 'others';
			}

			if (!$this->person->hasPerm("agent_tickets.modify_merge_{$set_suffix}")) {
				return false;
			}
		}

		return true;
	}
}
