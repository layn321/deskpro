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
use Application\DeskPRO\Entity\ChatConversation;

use Orb\Util\Arrays;

class ChatChecker extends AbstractChecker
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return bool
	 */
	public function canView(ChatConversation $convo)
	{
		if (!$this->person->hasPerm('agent_chat.view_transcripts')) {
			return false;
		}

		// Cant be an agent chat, obviously
		if ($convo->is_agent) {
			return false;
		}

		#------------------------------
		# If the user is part of the chat
		# then we know right away they can view
		#------------------------------

		if ($convo->agent && $convo->agent->id == $this->person->id) {
			return true;
		}

		if (in_array($this->person->id, $convo->getParticipantIds())) {
			return true;
		}

		#------------------------------
		# Can't view certain deps
		#------------------------------

		if ($convo->department && !$this->person->getHelper('AgentPermissions')->isDepartmentAllowed($convo->department, 'chat')) {
			return false;
		}

		#------------------------------
		# Cant view unassigned
		#------------------------------

		if (!$convo->agent && !$this->person->hasPerm('agent_chat.view_unassigned')) {
			return false;
		}

		#------------------------------
		# Cant view others
		#------------------------------

		if ($convo->agent && !$this->person->hasPerm('agent_chat.view_others')) {
			return false;
		}

		// If we got here, then we're allowed
		return true;
	}


	/**
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return bool
	 */
	public function canDelete(ChatConversation $convo)
	{
		if (!$this->canView($convo) || !$this->person->hasPerm('agent_chat.delete')) {
			return false;
		}

		return false;
	}
}
