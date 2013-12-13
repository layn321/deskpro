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
*/

namespace Application\DeskPRO\Chat\UserChat;

use Application\DeskPRO\App;
use Orb\Util\Util;

use Application\DeskPRO\Entity\Session as SessionEntity;
use Application\DeskPRO\Entity\Visitor;

use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;
use Application\DeskPRO\Entity\ClientMessage;

/**
 * Manages how chats are assigned automatically
 */
class AutoAssigner
{
	const MODE_ROUND_ROBIN = 'round_robin';

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var string
	 */
	protected $mode;

	public function __construct($mode, EntityManager $em)
	{
		$this->em = $em;
		$this->mode = $mode;
	}

	public function getAgent(ChatConversation $convo)
	{
		switch ($this->mode) {
			case self::MODE_ROUND_ROBIN:
				$assign_agent = $this->em->getRepository('DeskPRO:Person')->getChatAgentRoundRobin();
				return $assign_agent;
				break;

			default:
				return null;
		}
	}
}
