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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Validator;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;
use Orb\Validator\AbstractValidator;

class NewTicketReplyValidator extends AbstractValidator
{
	protected $run_validators = array();

	/**
	 * @var \Application\UserBundle\Tickets\NewReply
	 */
	protected $newreply;

	/**
	 * Check $value to see if its valid.
	 *
	 * @param \Application\UserBundle\Tickets\NewReply $newreply
	 * @return bool
	 */
	protected function checkIsValid($newreply)
	{
		$this->newreply = $newreply;

		$validator = new \Orb\Validator\StringLength(array('min' => 5));
		if (!$validator->isValid($this->newreply->message)) {
			$this->addError('message.short');
		}

		if ($this->errors) {
			return false;
		}

		return true;
	}

	protected function _traverseItems(array $items)
	{
		foreach ($items as $item) {
			if ($item['item_type'] == 'group') {
				if (empty($item['items'])) {
					continue;
				}

				$this->_traverseItems($item['items']);
			} else {
				$this->_validateItem($item);
			}
		}
	}
}
