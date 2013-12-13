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

namespace Application\DeskPRO\Notifications;

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\ClientMessage;

class NewFeedbackNotification extends AbstractAgentNotification
{
	/**
	 * @var \Application\DeskPRO\Entity\Feedback
	 */
	protected $feedback;

	public function __construct(Feedback $feedback)
	{
		parent::__construct();
		$this->feedback = $feedback;
	}

	public function shouldSendBrowserNotification(Person $person)
	{
		if ($this->feedback->getStatusCode() == 'hidden.validating' && $person->getPref('agent_notif.new_feedback_validate.alert')) {
			return true;
		} elseif ($this->feedback->status != 'hidden' && $person->getPref('agent_notif.new_feedback.alert')) {
			return true;
		}

		return false;
	}

	public function shouldSendEmailNotification(Person $person)
	{
		if ($this->feedback->getStatusCode() == 'hidden.validating' && $person->getPref('agent_notif.new_feedback_validate.email')) {
			return true;
		} elseif ($this->feedback->status != 'hidden' && $person->getPref('agent_notif.new_feedback.email')) {
			return true;
		}

		return false;
	}

	public function send()
	{
		$this->sendBrowserNotifications('AgentBundle:Feedback:alert-new-feedback.html.twig', array('feedback' => $this->feedback, 'notify_data' => array('notify_type' => 'new_feedback')));
		$this->sendEmailNotifications('DeskPRO:emails_agent:new-feedback.html.twig', array('feedback' => $this->feedback));

		$cm = new ClientMessage();
		$cm->fromArray(array(
			'channel' => 'agent.ui.new-feedback',
			'feedback_id' => $this->feedback->getId(),
			'created_by_client' => 'sys'
		));
		$this->em->persist($cm);
		$this->em->flush();
	}
}