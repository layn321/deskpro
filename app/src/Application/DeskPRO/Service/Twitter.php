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

namespace Application\DeskPRO\Service;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\TwitterAccount;
use Application\DeskPRO\Entity\TwitterAccountStatus;
use Application\DeskPRO\Entity\TwitterUser;
use Application\DeskPRO\Entity\TwitterStatus;
use Application\DeskPRO\Entity\TwitterStatusMention;
use Application\DeskPRO\Entity\TwitterStatusTag;
use Application\DeskPRO\Entity\TwitterStatusUrl;

class Twitter
{
	protected $_user_cache = array();
	protected $_tweet_cache = array();

	/**
	 * @var \Application\DeskPRO\ORM\EntityManager
	 */
	protected $em;

	public function __construct()
	{
		$this->em = App::getOrm();
	}

	/**
	 * Retrieve Twitter Application OAuth Consumer Key.
	 *
	 * @return string
	 */
	public static function getAgentConsumerKey()
	{
		if (App::getConfig('twitter.agent_consumer_key')) {
			return App::getConfig('twitter.agent_consumer_key');
		} else {
			return App::getSetting('core.twitter_agent_consumer_key');
		}
	}

	/**
	 * Retrieve Twitter Application OAuth Consumer Secret.
	 *
	 * @return string
	 */
	public static function getAgentConsumerSecret()
	{
		if (App::getConfig('twitter.agent_consumer_key')) {
			return App::getConfig('twitter.agent_consumer_secret');
		} else {
			return App::getSetting('core.twitter_agent_consumer_secret');
		}
	}

	public static function getAgentTwitterApi($token = null, $secret = null)
	{
		$api = new \EpiTwitter(self::getAgentConsumerKey(), self::getAgentConsumerSecret());
		if ($token && $secret) {
			$api->setToken($token, $secret);
		}

		return $api;
	}

	/**
	 * Retrieve Twitter Application OAuth Consumer Key.
	 *
	 * @return string
	 */
	public static function getUserConsumerKey()
	{
		if (App::getConfig('twitter.user_consumer_key')) {
			return App::getConfig('twitter.user_consumer_key');
		} else if (App::getSetting('core.twitter_user_consumer_key')) {
			return App::getSetting('core.twitter_user_consumer_key');
		} else {
			return self::getAgentConsumerKey();
		}
	}

	/**
	 * Retrieve Twitter Application OAuth Consumer Secret.
	 *
	 * @return string
	 */
	public static function getUserConsumerSecret()
	{
		if (App::getConfig('twitter.user_consumer_key')) {
			return App::getConfig('twitter.user_consumer_secret');
		} else if (App::getSetting('core.twitter_user_consumer_key')) {
			return App::getSetting('core.twitter_user_consumer_secret');
		} else {
			return self::getAgentConsumerSecret();
		}
	}

	public static function getUserTwitterApi($token = null, $secret = null)
	{
		$api = new \EpiTwitter(self::getUserConsumerKey(), self::getUserConsumerSecret());
		if ($token && $secret) {
			$api->setToken($token, $secret);
		}

		return $api;
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\TwitterUser
	 */
	protected function findUser($id)
	{
		if (!array_key_exists($id, $this->_user_cache)) {
			$this->_user_cache[$id] = $this->em->getRepository('DeskPRO:TwitterUser')->find($id);
		}

		return $this->_user_cache[$id];
	}

	/**
	 * @param integer $twitter_status_id
	 *
	 * @return \Application\DeskPRO\Entity\TwitterStatus
	 */
	protected function findStatus($id)
	{
		if (!array_key_exists($id, $this->_tweet_cache)) {
			$this->_tweet_cache[$id] = $this->em->getRepository('DeskPRO:TwitterStatus')->find($id);
		}

		return $this->_tweet_cache[$id];
	}

	public function processStatus(\EpiTwitter $api, $data, $do_persist = true, $depth = 0)
	{
		$status = $this->findStatus($data->id_str);
		if ($status) {
			return $status;
		}

		$status = TwitterStatus::createFromJson($data);
		if ($do_persist) {
			$this->_tweet_cache[$data->id_str] = $status;
		}

		$user = $this->findUser($data->user->id_str);
		if (!$user) {
			$user = TwitterUser::createFromJson($data->user);
			if ($do_persist) {
				$this->em->persist($user);
				$this->_user_cache[$data->user->id_str] = $user;
			}
		} else {
			$user->updateFromJson($data->user);
			if ($do_persist) {
				$this->em->persist($user);
			}
		}

		$status->user = $user;

		// retweet
		if (!empty($data->retweeted_status)) {
			$status['retweet'] = $this->processStatus($api, $data->retweeted_status, $do_persist, $depth);
			if ($do_persist) {
				$this->em->persist($status['retweet']);
			}
		}

		// reply
		if (!empty($data->in_reply_to_status_id_str) && $depth < 1) {
			// todo: if we're not fetching, create a stub for it for use later
			$reply = $this->findStatus($data->in_reply_to_status_id_str);
			if (!$reply) {
				try {
					$reply_result = $api->get_statusesShow(array(
						'id' => $data->in_reply_to_status_id_str,
						'include_entities' => true
					));

					if (!empty($reply_result->id_str)) {
						$reply = $this->processStatus($api, $reply_result, $do_persist, $depth + 1);
						if ($do_persist) {
							$this->em->persist($reply);
						}
					}
				} catch (\EpiTwitterException $e) {
					// likely, the status was private so we can't grab it
				}
			}

			if ($reply) {
				$status['in_reply_to_status'] = $reply;
				$status['in_reply_to_user'] = $reply->user;
			}
		}

		// fetch mentions
		if (isset($data->entities)) {
			foreach ($data->entities->user_mentions as $mention) {
				$mention_entity = $this->processStatusMention($api, $status, $mention, $do_persist);
				if ($mention_entity) {
					$status->addMention($mention_entity);
				}
			}

			// fetch hashtags
			foreach ($data->entities->hashtags as $hashtag) {
				$status->addTag($this->processStatusTag($status, $hashtag, $do_persist));
			}

			// fetch urls
			foreach ($data->entities->urls as $url) {
				$status->addUrl($this->processStatusUrl($status, $url, $do_persist));
			}
		}

		if ($do_persist) {
			$this->em->persist($status);
		}

		return $status;
	}

	public function processDm(\EpiTwitter $api, $data, $do_persist = true)
	{
		$dm = !empty($data->direct_message) ? $data->direct_message : $data;

		$status = $this->findStatus($dm->id_str);
		if ($status) {
			return $status;
		}

		$status = TwitterStatus::createFromDmJson($dm);
		if ($do_persist) {
			$this->_tweet_cache[$dm->id_str] = $status;
		}

		$user = $this->findUser($dm->sender->id_str);
		if (!$user) {
			$user = TwitterUser::createFromJson($dm->sender);
			if ($do_persist) {
				$this->em->persist($user);
				$this->_user_cache[$dm->sender->id_str] = $user;
			}
		} else {
			$user->updateFromJson($dm->sender);
			if ($do_persist) {
				$this->em->persist($user);
			}
		}

		$status->user = $user;

		$recipient = $this->findUser($dm->recipient->id_str);
		if (!$recipient) {
			$recipient = TwitterUser::createFromJson($dm->recipient);
			if ($do_persist) {
				$this->em->persist($recipient);
				$this->_user_cache[$dm->recipient->id_str] = $recipient;
			}
		} else {
			$recipient->updateFromJson($dm->recipient);
			if ($do_persist) {
				$this->em->persist($recipient);
			}
		}

		$status->recipient = $recipient;

		// fetch mentions
		if (isset($data->entities)) {
			foreach ($data->entities->user_mentions as $mention) {
				$mention_entity = $this->processStatusMention($api, $status, $mention, $do_persist);
				if ($mention_entity) {
					$status->addMention($mention_entity);
				}
			}

			// fetch hashtags
			foreach ($data->entities->hashtags as $hashtag) {
				$status->addTag($this->processStatusTag($status, $hashtag, $do_persist));
			}

			// fetch urls
			foreach ($data->entities->urls as $url) {
				$status->addUrl($this->processStatusUrl($status, $url, $do_persist));
			}
		}

		if ($do_persist) {
			$this->em->persist($status);
		}

		return $status;
	}

	public function processStatusMention(\EpiTwitter $api, TwitterStatus $status, $mention, $do_persist = true)
	{
		if ($mention->id_str == '-1') {
			return false;
		}

		$entity = TwitterStatusMention::createFromJson($mention);
		$entity['status'] = $status;

		$user = $this->findUser($mention->id_str);
		if (!$user) {
			$user = TwitterUser::createStub($mention->id_str, $mention->screen_name, $mention->name);
			if ($do_persist) {
				$this->em->persist($user);
				$this->_user_cache[$mention->id_str] = $user;
			}
		}

		$entity['user'] = $user;

		if ($do_persist) {
			$this->em->persist($entity);
		}

		return $entity;
	}

	public function processStatusTag(TwitterStatus $status, $tag, $do_persist = true)
	{
		$entity = TwitterStatusTag::createFromJson($tag);
		$entity['status'] = $status;

		if ($do_persist) {
			$this->em->persist($entity);
		}

		return $entity;
	}

	public function processStatusUrl(TwitterStatus $status, $url, $do_persist = true)
	{
		$entity = TwitterStatusUrl::createFromJson($url);
		$entity['status'] = $status;

		if ($do_persist) {
			$this->em->persist($entity);
		}

		return $entity;
	}

	public function getTextAtTwitterLength($text)
	{
		$text = str_replace("\r", '', $text);
		$text = str_replace("\t", ' ', $text);
		$text = trim($text);

		$http_length = 22;
		$https_length = 23;
		$replacements = array();

		$text = preg_replace_callback('/(https?):\/\/(?>[^ \t\r\n[\]#]+)(?!#)/i', function($match) use(&$replacements, $http_length, $https_length) {
			$id = count($replacements);
			$replacements[$id] = $match[0];

			$placeholder = "\x1A$id";
			if (strtolower($match[1]) == 'https') {
				$placeholder .= str_repeat("\x1A", $https_length - strlen($placeholder));
			} else {
				$placeholder .= str_repeat("\x1A", $http_length - strlen($placeholder));
			}

			return $placeholder;
		}, $text);

		return array(
			'text' => $text,
			'replacements' => $replacements
		);
	}

	public function countStatusLength($text)
	{
		$results = $this->getTextAtTwitterLength($text);
		return \Orb\Util\Strings::utf8_strlen($results['text']);
	}

	public function splitStatusText($text, $look_for_prefix = true)
	{
		$replaced = $this->getTextAtTwitterLength($text);
		$replaced['text'] = preg_replace('/\s/', ' ', $replaced['text']);

		if ($look_for_prefix && preg_match('/^(@[a-z0-9_]+\s+)+/i', $replaced['text'], $match)) {
			$prefix = $match[0];
			$split_text = substr($replaced['text'], strlen($prefix));
		} else {
			$prefix = '';
			$split_text = $replaced['text'];
		}

		$part_max_length = 140 - \Orb\Util\Strings::utf8_strlen($prefix);
		$text_parts = array();
		$first_added = false;

		do {
			if (\Orb\Util\Strings::utf8_strlen($split_text) <= $part_max_length) {
				if ($text_parts) {
					$split_text = "...$split_text";
				}

				$text_parts[] = $prefix . $split_text;
				break;
			} else {
				$test_text = substr($split_text, 0, $part_max_length - 3); // -3 for the appended ...
				$last_space = strrpos($test_text, ' ');
				if ($last_space !== false && $last_space >= 0) {
					// have a space
					$text_parts[] = $prefix
						. ($text_parts ? '...' : '')
						. substr($split_text, 0, $last_space)
						. '...';
					$split_text = ltrim(substr($split_text, $last_space + 1));
				} else {
					// no space - include whole thing
					$text_parts[] = $prefix
						. ($text_parts ? '...' : '')
						. $test_text
						. '...';
					$split_text = ltrim(substr($split_text, strlen($test_text)));
				}
			}

			if (!$first_added) {
				$part_max_length -= 3; // for the prepended ...
				$first_added = true;
			}
		} while (strlen($split_text));

		foreach ($text_parts AS &$text_part) {
			$text_part = preg_replace_callback('/\x1A(\d+)\x1A+/', function($match) use($replaced) {
				return isset($replaced['replacements'][$match[1]])
					? $replaced['replacements'][$match[1]]
					: '';
			}, $text_part);
			$text_part = str_replace("\x1A", '', $text_part);
		}

		return $text_parts;
	}

	public function sendAccountMessage($type, $text, $split, TwitterAccount $account, TwitterAccountStatus $reply = null, TwitterUser $user = null)
	{
		$error = null;
		$new_account_statuses = array();

		$api = $account->getTwitterApi();
		$em = App::getOrm();

		$text = trim(str_replace("\r", '', $text));
		$text = str_replace("\t", ' ', $text);

		if (strlen($text) === 0) {
			throw new \Exception('No text given');
		}

		if ($user) {
			$to_user = $user;
		} else if ($reply) {
			$to_user = $reply->status->user;
		} else {
			$to_user = null;
		}

		try {
			if ($type == 'public') {
				$long_status = null;

				if ($this->countStatusLength($text) > 140) {
					if ($split) {
						$text_parts = $this->splitStatusText($text);
					} else {
						$long_status = $this->_addLongStatus($text, true, $to_user);

						if ($to_user) {
							$long_text = "@$to_user->screen_name I have sent you a long message: ";
						} else {
							$long_text = 'Read my long message: ';
						}
						$long_text .= App::getRouter()->generate('user_long_tweet_view', array(
							'long_id' => $long_status->id
						), true);

						$text_parts = array($long_text);
					}
				} else {
					$text_parts = array($text);
				}

				$new_account_statuses = $this->_sendStatus(
					$api, $text_parts, $account, $reply, $long_status
				);
			} else {
				if (!$to_user) {
					throw new \Exception('No user to send private message to.');
				}

				$long_status = null;

				if ($this->countStatusLength($text) > 140) {
					if ($split) {
						$text_parts = $this->splitStatusText($text, false);
					} else {
						$long_status = $this->_addLongStatus($text, false, $to_user);

						$long_text = "I have sent you a long, private message. Sign in to see it. "
							. App::getRouter()->generate('user_long_tweet_view', array(
								'long_id' => $long_status->id
							), true);
						$text_parts = array($long_text);
					}
				} else {
					$text_parts = array($text);
				}

				$new_account_statuses = $this->_sendDm(
					$api, $to_user->id, $text_parts, $account, $reply, $long_status
				);
				if (!$new_account_statuses) {
					// couldn't send a DM, send a regular status
					if ($long_status) {
						$text_parts[0] = "@$to_user->screen_name $text_parts[0]";
					} else {
						$long_status = $this->_addLongStatus($text, false, $to_user);

						$long_text = "@$to_user->screen_name I have sent you a private message. Sign in to see it. "
							. App::getRouter()->generate('user_long_tweet_view', array(
								'long_id' => $long_status->id
							), true);
						$text_parts = array($long_text);
					}

					$new_account_statuses = $this->_sendStatus(
						$api, $text_parts, $account, $reply, $long_status
					);
				}
			}
		} catch (\EpiTwitterException $e) {
			$error = $this->getTwitterError($e);
		}  catch (\EpiOAuthException $e) {
			$error = $this->getTwitterError($e);
		}

		return array(
			'success' => !$error && !empty($new_account_statuses),
			'error' => $error,
			'new_account_statuses' => $new_account_statuses
		);
	}

	public function sendRetweet(TwitterAccount $account, TwitterAccountStatus $account_status)
	{
		$api = $account->getTwitterApi();
		$error = null;

		try {
			$response = $api->post("/statuses/retweet/{$account_status->status->id}.json");
			if (!empty($response->error)) {
				$error = $response->error;
			} else {
				$new_status = $this->processStatus($api, $response);

				$new_account_status = new TwitterAccountStatus();
				$new_account_status->status = $new_status;
				$new_account_status->account = $account;
				$new_account_status->status_type = 'sent';
				$new_account_status->action_agent =  App::getCurrentPerson();

				$account_status->retweeted = $new_account_status;

				$this->em->persist($new_status);
				$this->em->persist($new_account_status);
				$this->em->persist($account_status);
				$this->em->flush();

				$this->insertNewTweetClientMessage($new_account_status,
					array('retweeted' => $account_status->id)
				);
				$this->insertUpdatedTweetClientMessage($account_status,
					array('retweeted' => true)
				);
			}
		} catch (\EpiTwitterException $e) {
			$error = $this->getTwitterError($e);
		}  catch (\EpiOAuthException $e) {
			$error = $this->getTwitterError($e);
		}

		return array(
			'success' => !$error,
			'error' => $error
		);
	}

	public function unsendRetweet(TwitterAccount $account, TwitterAccountStatus $account_status)
	{
		$error = null;

		$account_retweet = $account_status->retweeted;

		if ($account_retweet && $account_retweet->status->getUserId() != $account->getUserId()) {
			throw new \Exception('Trying to delete tweet for non-account user.');
		}

		$id = $account_retweet->id;

		if ($account_retweet) {
			try {
				$response = $account->getTwitterApi()->post("/statuses/destroy/{$account_retweet->status->id}.json");
				if (!empty($response->error)) {
					$error = $response->error;
				} else {
					$this->em->remove($account_retweet);
					$this->em->remove($account_retweet->status);
					if ($account_retweet->status->long) {
						$this->em->remove($account_retweet->status->long);
					}
					$this->em->flush();

					$this->insertUpdatedTweetClientMessage($account_retweet,
						array('deleted' => true, 'account_status_id' => $id)
					);
					$this->insertUpdatedTweetClientMessage($account_status,
						array('unretweeted' => true)
					);
				}
			} catch (\EpiTwitterException $e) {
				$error = $this->getTwitterError($e);
			}  catch (\EpiOAuthException $e) {
				$error = $this->getTwitterError($e);
			}
		}

		return array(
			'success' => !$error,
			'error' => $error
		);
	}

	public function deleteStatus(TwitterAccount $account, TwitterAccountStatus $account_status)
	{
		if ($account_status->retweeted) {
			return $this->unsendRetweet($account, $account_status);
		}

		if ($account_status->status->recipient) {
			if ($account_status->status->getRecipientId() != $account->getUserId()) {
				return array(
					'success' => false,
					'error' => 'Direct messages may not be deleted after they have been sent.'
				);
			}
		} else {
			if ($account_status->status->getUserId() != $account->getUserId()) {
				throw new \Exception('Trying to delete tweet for non-account user.');
			}
		}

		$error = null;
		$id = $account_status->id;

		try {
			if ($account_status->status->recipient) {
				$response = $account->getTwitterApi()->post("/direct_messages/destroy/{$account_status->status->id}.json");
			} else {
				$response = $account->getTwitterApi()->post("/statuses/destroy/{$account_status->status->id}.json");
			}
			if (!empty($response->error)) {
				$error = $response->error;
			} else {
				$this->em->remove($account_status);
				$this->em->remove($account_status->status);
				if ($account_status->status->long) {
					$this->em->remove($account_status->status->long);
				}
				$this->em->flush();

				$this->insertUpdatedTweetClientMessage($account_status,
					array('deleted' => true, 'account_status_id' => $id)
				);
			}
		} catch (\EpiTwitterException $e) {
			$error = $this->getTwitterError($e);
		}  catch (\EpiOAuthException $e) {
			$error = $this->getTwitterError($e);
		}

		return array(
			'success' => !$error,
			'error' => $error
		);
	}

	public function setFavorite(TwitterAccount $account, TwitterAccountStatus $account_status, $is_favorite)
	{
		$api = $account->getTwitterApi();
		$error = null;

		if (!$account_status->status->isMessage()) {
			try {
				if ($is_favorite) {
					$response = $api->post_favoritesCreate(array('id' => $account_status->status->id));
				} else {
					$response = $api->post_favoritesDestroy(array('id' => $account_status->status->id));
				}
				if (!empty($response->error)) {
					$error = $response->error;
				}
			} catch (\EpiTwitterException $e) {
				// likely already in the state that we want, so set it to that
			}  catch (\EpiOAuthException $e) {
				$error = $this->getTwitterError($e);
			}
		}

		if (empty($error)) {
			$account_status['is_favorited'] = $is_favorite;

			$this->em->persist($account_status);
			$this->em->flush();

			$this->insertUpdatedTweetClientMessage($account_status,
				$is_favorite ? array('favorited' => true) : array('unfavorited' => true)
			);
		}

		return array(
			'success' => !$error,
			'error' => $error
		);
	}

	protected function _addLongStatus($text, $public, $to_user = null)
	{
		$long_status = new \Application\DeskPRO\Entity\TwitterStatusLong();
		$long_status->text = $text;
		$long_status->is_public = $public;
		$long_status->for_user = $to_user;

		$em = App::getOrm();
		$em->persist($long_status);
		$em->flush();

		$long_status->ensureDefaultPropertyChangedListener();

		return $long_status;
	}

	protected function _sendStatus($api, array $text_parts, $account, $reply = null, $long_status = null)
	{
		$em = App::getOrm();

		$new_account_statuses = array();

		foreach ($text_parts AS $part) {
			$params = array(
				'status' => $part
			);
			if ($reply && !$reply->status->recipient) {
				// only if not a DM
				$params['in_reply_to_status_id'] = $reply->status->id;
			}

			$response = $api->post_statusesUpdate($params);
			if (!empty($response->error)) {
				$error = $response->error;
			} else {
				$new_status = $this->processStatus($api, $response);

				$new_account_status = new TwitterAccountStatus();
				$new_account_status->status = $new_status;
				$new_account_status->account = $account;
				$new_account_status->status_type = 'sent';
				$new_account_status->in_reply_to = $reply;
				$new_account_status->action_agent = App::getCurrentPerson();

				if ($long_status) {
					$long_status->status = $new_status;
					$em->persist($long_status);

					$new_status->long = $long_status;
				}

				$em->persist($new_status);
				$em->persist($new_account_status);
				$em->flush();

				$new_account_statuses[] = $new_account_status;

				$this->insertNewTweetClientMessage($new_account_status);
			}
		}

		return $new_account_statuses;
	}

	protected function _sendDm($api, $user_id, array $text_parts, $account, $reply = null, $long_status = null)
	{
		$em = App::getOrm();

		$new_account_statuses = array();

		foreach ($text_parts AS $part) {
			try {
				$response = $api->post_direct_messagesNew(array(
					'user_id' => $user_id,
					'text' => $part
				));
				if (!empty($response->error)) {
					$error = $response->error;
				} else {
					$twitter_service = new \Application\DeskPRO\Service\Twitter();
					$new_status = $twitter_service->processDm($api, $response);

					$new_account_status = new TwitterAccountStatus();
					$new_account_status->status = $new_status;
					$new_account_status->account = $account;
					$new_account_status->status_type = 'direct';
					$new_account_status->in_reply_to = $reply;
					$new_account_status->action_agent = App::getCurrentPerson();

					if ($long_status) {
						$long_status->status = $new_status;
						$em->persist($long_status);

						$new_status->long = $long_status;
					}

					$em->persist($new_status);
					$em->persist($new_account_status);
					$em->flush();

					$new_account_statuses[] = $new_account_status;

					$this->insertNewTweetClientMessage($new_account_status);
				}
			} catch (\EpiTwitterException $e) {
				// user isn't following so we can't send a DM
				break; // no point trying again
			}
		}

		return $new_account_statuses;
	}

	public function insertNewTweetClientMessage(TwitterAccountStatus $account_status)
	{
		App::getDb()->insert('client_messages', array(
			'channel' => 'agent.tweet-added',
			'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
			'date_created' => date('Y-m-d H:i:s'),
			'data' => serialize($this->_getCmBaseData($account_status)),
			'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
		));
	}

	public function insertUpdatedTweetClientMessage(TwitterAccountStatus $account_status, array $changes)
	{
		$data = array_merge($this->_getCmBaseData($account_status), $changes);

		App::getDb()->insert('client_messages', array(
			'channel' => 'agent.tweet-updated',
			'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
			'date_created' => date('Y-m-d H:i:s'),
			'data' => serialize($data),
			'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
		));
	}

	protected function _getCmBaseData(TwitterAccountStatus $account_status)
	{
		$tweet_html = App::getTemplating()->render('AgentBundle:TwitterStatus:list-row.html.twig', array(
			'account_status' => $account_status
		));

		if ($account_status->agent) {
			$assignment = 'agent:' . $account_status->agent->id;
		} else if ($account_status->agent_team) {
			$assignment = 'agent_team:' . $account_status->agent_team->id;
		} else {
			$assignment = '';
		}

		return array(
			'account_status_id' => $account_status->id,
			'account_id' => $account_status->account->id,
			'status_type' => $account_status->status_type,
			'status_id' => $account_status->status->id,
			'is_from_self' => $account_status->account->user->id == $account_status->status->user->id,
			'is_archived' => $account_status->is_archived ? 1 : 0,
			'is_favorited' => $account_status->is_favorited ? 1 : 0,
			'assignment' => $assignment,
			'agent_id' => $account_status->agent ? $account_status->agent->id : 0,
			'agent_team_id' => $account_status->agent_team ? $account_status->agent_team->id : 0,
			'tweet_html' => $tweet_html,
			'trigger_user_id' => App::getCurrentPerson() ? App::getCurrentPerson()->getId() : 0
		);
	}

	public function getTwitterError(\Exception $e)
	{
		$json = @json_decode($e->getMessage());
		if ($json && !empty($json->errors[0]->message)) {
			return "Twitter error message: " . $json->errors[0]->message;
		} else {
			return $e->getMessage();
		}
	}
}