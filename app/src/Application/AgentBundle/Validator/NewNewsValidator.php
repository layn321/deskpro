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

namespace Application\AgentBundle\Validator;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;
use Orb\Validator\AbstractValidator;
use Application\AgentBundle\Form\Model\SettingsProfile;

class NewNewsValidator extends AbstractValidator
{
	/**
	 * @param \Application\AgentBundle\Form\Model\NewNews $news
	 * @return bool
	 */
	protected function checkIsValid($news)
	{
		if (!$news->category_id) {
			$this->addError('category_id.invalid');
		} else {
			$cat = App::getOrm()->find('DeskPRO:NewsCategory', $news->category_id);
			if (!$cat) {
				$this->addError('category_id.invalid');
			}
		}

		if (!$news->title) {
			$this->addError('title.missing');
		}

		if (!$news->status) {
			$this->addError('status.invalid');
		}

		if ($this->errors) {
			return false;
		}

		return true;
	}
}
