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
 * @subpackage Log
 */

namespace Application\DeskPRO\Log;

class LogItem extends \Orb\Log\LogItem
{
	const LOG_NAME = 'log_name';
	const FLAG = 'flag';

	protected function init()
	{
		$this->_standard_fields[] = self::LOG_NAME;
		$this->_standard_fields[] = self::FLAG;

		if (!isset($this[self::LOG_NAME])) {
			$this[self::LOG_NAME] = 'general';
		}
		if (!isset($this[self::FLAG])) {
			$this[self::FLAG] = null;
		}
	}


	public function getLogName()
	{
		return $this[self::LOG_NAME];
	}

	public function getFlag()
	{
		return $this[self::FLAG];
	}
}
