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
 * Orb
 *
 * @package Orb
 * @subpackage Validator
 */

namespace Application\DeskPRO\Validator;

use Application\DeskPRO\App;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Person;

class TicketPriority extends AbstractPersonContextValidator
{
	/**
	 * Allow a non-selection?
	 * @var bool
	 */
	protected $allow_none = true;

	/**
	 * Sometimes a specific value or values might be white-listed
	 * even though they are denied by permissions.
	 *
	 * For example if an agent sets a category a user cant use,
	 * then the user modifying the ticket shouldn't trigger an error
	 * when it's not changed.
	 *
	 * @var array
	 */
	protected $whitelist = array();

	public function init()
	{
		parent::init();

		$this->allow_none = $this->getOption('allow_none', true);
		$this->whitelist  = (array)$this->getOption('whitelist', array());
	}

	/**
	 * Check $value to see if its valid.
	 *
	 * @return bool
	 */
	protected function checkIsValid($value)
	{
		$value = (int)$value;
		if (!$value) {
			if ($this->allow_none) {
				return true;
			}

			$this->addError('none');
			return false;
		}

		if ($value < 1) {
			return false;
		}

		$check_pri = App::getDb()->fetchColumn("SELECT id FROM ticket_priorities WHERE id = ?", array($value));

		if (!$check_pri) {
			$this->addError('not_exist');
			return false;
		}

		//if ($this->hasPerson()) {
		//	$person = $this->getPerson();
		//	$person->loadHelper('PermissionsManager');
		//	if (!$person->getPermsLoader('Departments')->isCategoryAllowed($value)) {
		//		return false;
		//	}
		//}

		return true;
	}
}
