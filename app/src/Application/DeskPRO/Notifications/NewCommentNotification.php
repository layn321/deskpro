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
use Application\DeskPRO\Entity\CommentAbstract;

class NewCommentNotification extends AbstractAgentNotification
{
	/**
	 * @var \Application\DeskPRO\Entity\CommentAbstract
	 */
	protected $comment;

	public function __construct(CommentAbstract $comment)
	{
		parent::__construct();
		$this->comment = $comment;
	}

	public function shouldSendBrowserNotification(Person $person)
	{
		if ($this->comment->status == 'user_validating') {
			return false;
		}

		if ($this->comment->status == 'validating' && $person->getPref('agent_notif.new_comment_validate.alert')) {
			return true;
		} elseif ($this->comment->status != 'hidden' && $person->getPref('agent_notif.new_comment_validate.alert')) {
			return true;
		}

		return false;
	}

	public function shouldSendEmailNotification(Person $person)
	{
		if ($this->comment->status == 'user_validating') {
			return false;
		}

		if ($this->comment->status == 'validating' && $person->getPref('agent_notif.new_comment.alert')) {
			return true;
		} elseif ($this->comment->status != 'hidden' && $person->getPref('agent_notif.new_comment.alert')) {
			return true;
		}

		return false;
	}

	public function send()
	{
		$this->sendBrowserNotifications('AgentBundle:Publish:alert-new-comment.html.twig', array('comment' => $this->comment, 'notify_data' => array('notify_type' => 'new_comment')));
		$this->sendEmailNotifications('DeskPRO:emails_agent:new-comment.html.twig', array('comment' => $this->comment));
	}
}