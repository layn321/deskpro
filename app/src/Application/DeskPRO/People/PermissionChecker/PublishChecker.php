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

class PublishChecker extends AbstractChecker
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @param mixed $content
	 * @return bool
	 */
	public function canDelete($content)
	{
		return $this->person->hasPerm('agent_publish.delete');
	}

	/**
	 * @param mixed $content
	 * @return bool
	 */
	public function canEdit($content)
	{
		if ($this->person->hasPerm('agent_publish.edit')) {
			return true;
		}

		if ($content->person && $content->person->getId() == $this->person->getId()) {
			return true;
		}

		return false;
	}


	/**
	 * @param $content
	 */
	public function canValidate($content)
	{
		return $this->person->hasPerm('agent_publish.validate');
	}
}
