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
 * @subpackage
 */

namespace Application\ReportBundle\OverviewStat;

use Application\DeskPRO\App;

class ChatsCreated extends AbstractTableOverviewStat
{
	/**
	 * @var ChatGroupingField
	 */
	protected $grouping_field;

	/**
	 * @var \DateTime
	 */
	protected $date_start;

	/**
	 * @var \DateTime
	 */
	protected $date_end;

	/**
	 * @var int[]
	 */
	protected $values = null;

	/**
	 * @var array
	 */
	protected $titles = null;

	public function __construct(ChatGroupingField $grouping_field, \DateTime $date_start, \DateTime $date_end)
	{
		$this->grouping_field = $grouping_field;
		$this->date_start     = \Orb\Util\Dates::convertToUtcDateTime($date_start);
		$this->date_end       = \Orb\Util\Dates::convertToUtcDateTime($date_end);
	}


	/**
	 * @return string[]
	 */
	public function getTitles()
	{
		return $this->grouping_field->getTitles($this->getValues());
	}


	/**
	 * @return int[]
	 */
	public function getValues()
	{
		if ($this->values !== null) {
			return $this->values;
		}

		$group_field = $this->grouping_field->getFieldInfo();

		$d1 = $this->date_start->format('Y-m-d H:i:s');
		$d2 = $this->date_end->format('Y-m-d H:i:s');

		$sql = "
			SELECT {$group_field['select']}, COUNT(*)
			FROM chat_conversations
			{$group_field['join']}
			WHERE chat_conversations.is_agent = 0 AND chat_conversations.date_created BETWEEN '$d1' AND '$d2' {$group_field['where']}
			GROUP BY {$group_field['group_by']}
		";

		$this->logger->logDebug("[ChatsCreated] $sql");
		$this->logger->startTimer('ChatsCreated');
		$this->values = App::getDb()->fetchAllKeyValue($sql);
		$this->logger->logToatlTime('ChatsCreated');

		return $this->values;
	}
}