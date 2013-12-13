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
 * @subpackage WorkerProcess
 */

namespace Application\DeskPRO\WorkerProcess\Job;

use Application\DeskPRO\App;
use Application\DeskPRO\Log\Logger;

use Application\DeskPRO\Entity\TwitterAccount;
use Application\DeskPRO\Entity\TwitterAccountFriend;
use Application\DeskPRO\Entity\TwitterAccountFollower;
use Application\DeskPRO\Entity\TwitterAccountStatus;
use Application\DeskPRO\Entity\TwitterStatus;
use Application\DeskPRO\Entity\TwitterStatusMention;
use Application\DeskPRO\Entity\TwitterStatusTag;
use Application\DeskPRO\Entity\TwitterStatusUrl;
use Application\DeskPRO\Entity\TwitterUser;
/**
 * Processes Twitter stream events.
 */
class TwitterStream extends AbstractJob
{
	const DEFAULT_INTERVAL = 10;

	const EVENT_LIMIT = 50;

	/**
	 * @var \Application\DeskPRO\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Application\DeskPRO\Service\Twitter
	 */
	protected $twitter_service;

	protected $accounts = array();
	protected $twitter = array();

	public function run()
	{
		if (!App::getConfig('enable_twitter')) {
			return;
		}

		$this->db = App::getDb();
		$this->em = App::getOrm();
		$this->twitter_service = new \Application\DeskPRO\Service\Twitter();

		$events = $this->db->fetchAll(sprintf("
			SELECT *
			FROM twitter_stream
			WHERE account_id IS NOT NULL AND event != 'unknown'
			ORDER BY date_created ASC, id ASC
			LIMIT %d
		", self::EVENT_LIMIT));

		$processed = 0;
		foreach ($events as $event) {
			$method = 'process'.ucfirst($event['event']);
			if (!method_exists($this, $method)) {
				$this->logStatus('unknown event type', $event);
				continue;
			}

			$account = $this->getAccount($event['account_id']);
			if ($account) {
				$data = @unserialize($event['data']);
				if ($data) {
					try {
						$success = call_user_func(
							array($this, $method),
							$this->getAccount($event['account_id']),
							$data
						);
					} catch (\Exception $e) {
						$this->logStatus('exception caught: ' . $e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine());
						$success = false;
						\DeskPRO\Kernel\KernelErrorHandler::logException($e);
					}
				} else {
					// couldn't unserialize the data, so just get rid of this
					$success = true;
				}
			} else {
				// account isn't being processed anymore, just discard them
				$success = true;
			}

			if ($success) {
				$this->db->delete('twitter_stream', array(
					'id' => $event['id']
				));

				$processed++;
			}
		}
	}

	/**
	 * @param integer $id
	 * @return \EpiTwitter
	 */
	protected function getTwitter($id)
	{
		if (!isset($this->twitter[$id])) {
			$account = $this->getAccount($id);
			if ($account) {
				$this->twitter[$id] = $account->getTwitterApi();
			} else {
				$this->twitter[$id] = false;
			}
		}

		return $this->twitter[$id];
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\TwitterAccount
	 */
	protected function getAccount($id)
	{
		if (!isset($this->accounts[$id])) {
			$this->accounts[$id] = $this->em->getRepository('DeskPRO:TwitterAccount')->find($id);
		}

		return $this->accounts[$id];
	}

	/**
	 * @param integer $twitter_status_id
	 *
	 * @return \Application\DeskPRO\Entity\TwitterStatus
	 */
	protected function findStatus($twitter_status_id)
	{
		return $this->em->getRepository('DeskPRO:TwitterStatus')->getByTwitterStatusId($twitter_status_id);
	}

	/**
	 * @param integer $twitter_status_id
	 * @param TwitterAccount $account
	 *
	 * @return \Application\DeskPRO\Entity\TwitterAccountStatus
	 */
	protected function findAccountStatus($twitter_status_id, TwitterAccount $account)
	{
		return $this->em->getRepository('DeskPRO:TwitterAccountStatus')->getByTwitterStatusAndAccount($twitter_status_id, $account);
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\TwitterUser
	 */
	protected function findUser($id)
	{
		return $this->em->getRepository('DeskPRO:TwitterUser')->find($id);
	}

	/**
	 * @param \Application\DeskPRO\Entity\TwitterAccount $account
	 * @param object $data
	 * @return Boolean
	 */
	protected function processStatus(TwitterAccount $account, $data)
	{
		$account_status = $this->findAccountStatus($data->id_str, $account);
		if ($account_status && $account_status->status_type) {
			// won't have a status type if it didn't come through here,
			// but if it's now coming through here we need to change the type
			return true;
		}

		$status = $this->twitter_service->processStatus($this->getTwitter($account['id']), $data);
		$this->em->persist($status);

		if (!$account_status) {
			$account_status = new TwitterAccountStatus();
			$account_status->status = $status;
			$account_status->account = $account;
		}

		if ($data->user->id_str == $account->getUserId()) {
			$account_status->status_type = 'sent';
		} else if (!empty($data->retweeted_status) && $data->retweeted_status->user->id_str == $account->getUserId()) {
			$account_status->status_type = 'retweet';
		} else if (!empty($data->in_reply_to_user_id_str) && $data->in_reply_to_user_id_str == $account->getUserId()) {
			if (!empty($data->in_reply_to_status_id_str)) {
				$account_status->status_type = 'reply';
			} else {
				$account_status->status_type = 'mention';
			}
		} else {
			$account_status->status_type = 'timeline';

			if (isset($data->entities)) {
				foreach ($data->entities->user_mentions as $mention) {
					if ($mention->id_str == $account->getUserId()) {
						$account_status->status_type = 'mention';
						break;
					}
				}
			}
		}

		$reply_account_status = null;

		if (!empty($data->in_reply_to_status_id_str)) {
			$reply_account_status = $this->findAccountStatus($data->in_reply_to_status_id_str, $account);
			if ($reply_account_status && $account_status->status_type == 'sent') {
				$account_status->in_reply_to = $reply_account_status;
			}
		}

		$this->em->persist($account_status);
		$this->em->flush();

		$this->twitter_service->insertNewTweetClientMessage($account_status);

		if ($reply_account_status && $account_status->status_type != 'sent') {
			$notify = new \Application\DeskPRO\Notifications\TweetReplyNotification($account_status, $reply_account_status);
			$notify->send();
		}

		if ($account_status->status_type != 'sent') {
			$notify = new \Application\DeskPRO\Notifications\TweetNewNotification($account_status, $reply_account_status);
			$notify->send();
		}

		return true;
	}

	protected function processMessage(TwitterAccount $account, $data)
	{
		$dm = !empty($data->direct_message) ? $data->direct_message : $data;

		$account_status = $this->findAccountStatus($dm->id_str, $account);
		if ($account_status && $account_status->status_type) {
			// won't have a status type if it didn't come through here,
			// but if it's now coming through here we need to change the type
			return true;
		}

		$status = $this->twitter_service->processDm($this->getTwitter($account['id']), $data);
		$this->em->persist($status);

		if (!$account_status) {
			$account_status = new TwitterAccountStatus();
			$account_status->status = $status;
			$account_status->account = $account;
		}

		$account_status->status_type = 'direct';

		$this->em->persist($account_status);
		$this->em->flush();

		$this->twitter_service->insertNewTweetClientMessage($account_status);

		if (!$account_status->isFromSelf()) {
			$notify = new \Application\DeskPRO\Notifications\TweetNewNotification($account_status);
			$notify->send();
		}

		return true;
	}

	protected function processEvent(TwitterAccount $account, $data)
	{
		$source = $data->source;
		$target = $data->target;

		$eventType = $data->event;
		$createdAt = $data->created_at;

		// Check source user exists
		$sourceUser = $this->findUser($source->id_str);
		if (!$sourceUser) {
			$sourceUser = TwitterUser::createFromJson($source);
			$this->em->persist($sourceUser);
		} else {
			$sourceUser->updateFromJson($source);
			$this->em->persist($sourceUser);
		}

		// Check target user exists
		$targetUser = $this->findUser($target->id_str);
		if (!$targetUser) {
			$targetUser = TwitterUser::createFromJson($target);
			$this->em->persist($targetUser);
		} else {
			$targetUser->updateFromJson($target);
			$this->em->persist($targetUser);
		}

		$follower = null;
		$friend = null;

		if (isset($data->target_object) && isset($data->target_object->text)) {
			$targetObject = $data->target_object;

			// Get the status, or create it
			$status = $this->findAccountStatus($targetObject->id_str, $account);
			if (!$status) {
				$this->processStatus($account, $targetObject);
				$this->em->flush();
				$status = $this->findAccountStatus($targetObject->id_str, $account);
			}

			if (!$status) {
				return true;
			}

			if ($sourceUser->id == $account->getUserId()) {
				switch ($eventType) {
					// Process a favorite
					case 'favorite':
						$status->setIsFavorited(true);
						$this->em->persist($status);
						break;

					case 'unfavorite':
						$status->setIsFavorited(false);
						$this->em->persist($status);
						break;
				}
			}
		} else {
			switch ($eventType) {
				case 'follow':
					if ($sourceUser->id == $account->getUserId()) {
						// following someone
						if (!$this->em->getRepository('DeskPRO:TwitterAccountFriend')->findOneByAccountIdAndUserId($account->id, $targetUser->id)) {
							$friend = new TwitterAccountFriend();
							$friend->account = $account;
							$friend->user = $targetUser;
							$this->em->persist($friend);

							App::getDb()->insert('client_messages', array(
								'channel' => 'agent.twitter-friend',
								'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
								'date_created' => date('Y-m-d H:i:s'),
								'data' => serialize(array('action' => 'new', 'account_id' => $account->id)),
								'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
							));
						}
					} else if ($targetUser->id == $account->getUserId()) {
						// being followed
						if (!$this->em->getRepository('DeskPRO:TwitterAccountFollower')->findOneByAccountIdAndUserId($account->id, $sourceUser->id)) {
							$friend = $this->em->getRepository('DeskPRO:TwitterAccountFriend')->findOneByAccountIdAndUserId($account->id, $sourceUser->id);

							$follower = new TwitterAccountFollower();
							$follower->account = $account;
							$follower->user = $sourceUser;
							$follower->is_archived = $friend ? true : false;
							$this->em->persist($follower);

							App::getDb()->insert('client_messages', array(
								'channel' => 'agent.twitter-follower',
								'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
								'date_created' => date('Y-m-d H:i:s'),
								'data' => serialize(array('action' => ($friend ? 'new-archived' : 'new'), 'account_id' => $account->id)),
								'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
							));
						}
					}
					break;

				case 'unfollow':
					if ($sourceUser->id == $account->getUserId()) {
						// unfollowing someone
						$friend = $this->em->getRepository('DeskPRO:TwitterAccountFriend')->findOneByAccountIdAndUserId($account->id, $targetUser->id);
						if ($friend) {
							$this->em->remove($friend);
							$friend = null;

							App::getDb()->insert('client_messages', array(
								'channel' => 'agent.twitter-friend',
								'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
								'date_created' => date('Y-m-d H:i:s'),
								'data' => serialize(array('action' => 'removed', 'account_id' => $account->id)),
								'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
							));
						}
					}
					break;
			}
		}

		$this->em->flush();

		return true;
	}

	protected function processFriends(TwitterAccount $account, $data)
	{
		if (!count($data->friends)) {
			return true;
		}

		$diff = array_diff(array_unique($data->friends), $account->getFriendIds(false));
		foreach ($diff as $id) {
			if (!($user = $this->findUser($id))) {
				$user = TwitterUser::createStub($id);
				$this->em->persist($user);
			}

			$friend = new TwitterAccountFriend();
			$friend['account'] = $account;
			$friend['user'] = $user;
			$this->em->persist($friend);
		}

		$this->em->flush();

		return true;
	}

	protected function processDelete(TwitterAccount $account, $data)
	{
		if (isset($data->delete->status)) {
			return $this->processDeleteStatus($account, $data->delete->status);
		}

		return false;
	}

	protected function processDeleteStatus(TwitterAccount $account, $data)
	{
		// event could be removed if status does not exist
		if (!($status = $this->findStatus($data->id_str, $account))) {
			return true;
		}

		// delete long status
		if ($status['long']) {
			$this->em->remove($status['long']);
		}

		// delete mentions
		if ($status['mentions']->count()) {
			foreach ($status['mentions'] as $mention) {
				$this->em->remove($mention);
			}
		}

		// delete URLs
		if ($status['urls']->count()) {
			foreach ($status['urls'] as $url) {
				$this->em->remove($url);
			}
		}

		// delete tags
		if ($status['tags']->count()) {
			foreach ($status['tags'] as $tag) {
				$this->em->remove($tag);
			}
		}

		$this->em->remove($status);
		$this->em->flush();

		return true;
	}
}
