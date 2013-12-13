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

namespace Application\DeskPRO\Tickets\TicketChangeInspector\LogActions;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

class AgentTeam extends AbstractLogAction
{
	protected $old_agent_team;
	protected $new_agent_team;

	public function __construct($old_agent_team, $new_agent_team)
	{
		$this->old_agent_team = $old_agent_team;
		$this->new_agent_team = $new_agent_team;
	}

	public function getLogName()
	{
		return 'changed_agent_team';
	}

	public function getLogDetails()
	{
		if ($this->old_agent_team and $this->new_agent_team) {
			if ($this->old_agent_team->getId() == $this->new_agent_team->getId()) {
				return array();
			}
		} else if (!$this->old_agent_team and !$this->new_agent_team) {
			return array();
		}

		return array(
			'id_before' => $this->old_agent_team['id'] ?: null,
			'id_after'  => $this->new_agent_team['id'] ?: null,

			'old_agent_team_id'     => $this->old_agent_team ? $this->old_agent_team['id'] : 0,
			'old_agent_team_name'   => $this->old_agent_team ? $this->old_agent_team['name'] : '',
			'new_agent_team_id'     => $this->new_agent_team ? $this->new_agent_team['id'] : 0,
			'new_agent_team_name'   => $this->new_agent_team ? $this->new_agent_team['name'] : '',
		);
	}

	public function getEventType()
	{
		return 'property';
	}
}
