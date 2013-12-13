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
 * @category Feedback
 */

namespace Application\DeskPRO\Feedback;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;

class ChangeTracker extends \Application\DeskPRO\Domain\ChangeTracker
{
	public function done()
	{
		// No notifications for hidden
		if ($this->entity['status'] == 'hidden') {
			return;
		}

		// Have no one to send notiifcations to
		if (!$this->entity['email']) {
			return;
		}

		$changed_status = $this->getChangedProperty('status');
		$changed_status_cat = $this->getChangedProperty('hidden_status');

		// Nothign happened work reporting
		if (!$changed_status AND !$changed_status_cat) {
			return;
		}

		$old_status = isset($changed_status['old']) ? $changed_status['old'] : null;
		$old_status_cat = isset($changed_status_cat['old']) ? $changed_status_cat['old'] : null;

		$show = null;
		if ($old_status == 'hidden' AND $this->entity['status'] != 'hidden') {
			$show = 'validated';
		} elseif ($this->entity['status'] == 'active') {
			$show = 'updated_active';
		} elseif ($this->entity['status'] == 'closed') {
			$show = 'updated_closed';
		}

		if ($show) {
			$message = App::getMailer()->createMessage();
			$message->setTo($this->entity['user_email'], $this->entity['user_email']);
			$message->setTemplate('DeskPRO:emails_user:feedback-updated.html.twig', array(
				'feedback' => $feedback,
				'changed_status' => $changed_status,
				'changed_status_cat' => $changed_status_cat,
				'show' => $show
			));
			$message->enableQueueHint();

			App::getMailer()->send($message);
		}
	}
}
