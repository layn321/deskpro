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
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketTrigger;

abstract class AbstractAction implements ActionInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\TicketTrigger
	 */
	protected $trigger;

	/**
	 * @var array
	 */
	protected $metadata = array();

	/**
	 * @param \Application\DeskPRO\Entity\TicketTrigger $trigger
	 */
	public function setTrigger(TicketTrigger $trigger)
	{
		$this->trigger = $trigger;
	}

	/**
	 * @param array $metadata
	 * @return mixed
	 */
	public function setMetaData(array $metadata)
	{
		if ($this->metadata) {
			$metadata = array_merge($this->metadata, $metadata);
		}

		$this->metadata = $metadata;
	}


	/**
	 * @param string $k
	 * @param mixedd $v
	 */
	public function addMetaData($k, $v)
	{
		$this->metadata[$k] = $v;
	}

	/**
	 * @return array
	 */
	public function getMetaData($k = null, $default = null)
	{
		if ($k === null) {
			return $this->metadata;
		}

		return isset($this->metadata[$k]) ? $this->metadata[$k] : $default;
	}

	/**
	 * @return bool
	 */
	public function doPrepend()
	{
		return false;
	}


	/**
	 * @return string
	 */
	public function getActionName()
	{
		return get_class($this);
	}
}