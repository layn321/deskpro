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

use Application\DeskPRO\Entity\TwitterAccountStatus;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\App;

class TweetNewNotification extends AbstractAgentNotification
{
	/**
	 * @var \Application\DeskPRO\Entity\TwitterAccountStatus
	 */
	protected $account_status;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterAccountStatus
	 */
	protected $reply_account_status;

	public function __construct(TwitterAccountStatus $account_status, TwitterAccountStatus $reply_account_status = null)
	{
		parent::__construct();
		$this->account_status = $account_status;
		$this->reply_account_status = $reply_account_status;
	}

	public function shouldSendBrowserNotification(Person $agent)
	{
		if ($this->reply_account_status) {
			if ($agent->getPref('agent_notif.tweet_reply.alert')
				&& $this->reply_account_status->action_agent
				&& $this->reply_account_status->action_agent->id == $agent->id
			) {
				// already got an alert for this
				return false;
			}
		}

		switch ($this->account_status->status_type) {
			case 'direct': return $agent->getPref('agent_notif.tweet_new_dm.alert');
			case 'reply': return $agent->getPref('agent_notif.tweet_new_reply.alert');
			case 'mention': return $agent->getPref('agent_notif.tweet_new_mention.alert');
			case 'retweet': return $agent->getPref('agent_notif.tweet_new_retweet.alert');
			default: return false;
		}
	}

	public function shouldSendEmailNotification(Person $agent)
	{
		if ($this->reply_account_status) {
			if ($agent->getPref('agent_notif.tweet_reply.email')
				&& $this->reply_account_status->action_agent
				&& $this->reply_account_status->action_agent->id == $agent->id
			) {
				// already got an email for this
				return false;
			}
		}

		switch ($this->account_status->status_type) {
			case 'direct': return $agent->getPref('agent_notif.tweet_new_dm.email');
			case 'reply': return $agent->getPref('agent_notif.tweet_new_reply.email');
			case 'mention': return $agent->getPref('agent_notif.tweet_new_mention.email');
			case 'retweet': return $agent->getPref('agent_notif.tweet_new_retweet.email');
			default: return false;
		}
	}

	public function send()
	{
		$this->sendBrowserNotifications('AgentBundle:TwitterStatus:notify-row-new.html.twig', array(
			'account_status' => $this->account_status,
			'notify_data' => array('notify_type' => 'twitter')
		));
		$this->sendEmailNotifications('DeskPRO:emails_agent:tweet-new.html.twig', array(
			'account_status' => $this->account_status
		));
	}
}