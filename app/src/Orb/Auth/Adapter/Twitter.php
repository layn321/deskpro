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
 * @category Auth
 */

namespace Orb\Auth\Adapter;

use Orb\Auth\Adapter\SessionStateInterface;
use Orb\Auth\Adapter\CallbackInterface;
use Orb\Auth\StateHandler\StateHandlerInterface;
use Orb\Auth\Result;
use Orb\Log\Loggable;

class Twitter extends AbstractCallbackAdatper implements Loggable
{
	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	protected $consumer_key;
	protected $consumer_secret;

	/**
	 * @param string $consumer_key     Your Twitter consumer key
	 * @param string $consumer_secret  Your Twitter consumer secret
	 */
	public function __construct($consumer_key, $consumer_secret)
	{
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
	}


	/**
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(\Orb\Log\Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}


	/**
	 * Initialize the auth process by setting state, and returning a redirect result.
	 *
	 * @return Orb\Auth\Result
	 */
	protected function authenticateInitialize(StateHandlerInterface $state)
	{
		$oauth = $this->getOauthConsumer();

		try {
			$token = $oauth->getRequestToken();
		} catch (\Zend\OAuth\Exception $e) {
			if ($this->logger) {
				$this->logger->log("[Twitter] authenticateInitialize exception: {$e->getCode()} {$e->getMessage()}", 'ERR');
			}
			$result = new Result(Result::FAILURE_EXCEPTION, null, array(Result::MSG_EXCEPTION => $e));
			return $result;
		}

		$state['orb_oauth_twitter_rtoken'] = $token;

		$redirect_url = $oauth->getRedirectUrl();

		$result = new Result(Result::REQUIRES_REDIRECT, null, array(Result::MSG_REDIRECT => $redirect_url));

		if ($this->logger) {
			$this->logger->log("[Twitter] authenticateInitialize success: Token({$token}) Redirect({$redirect_url})", 'DEBUG');
		}

		return $result;
	}



	/**
	 * Process the callback and return a final result.
	 *
	 * @return Orb\Auth\Result
	 */
	protected function authenticateCallback(array $callback_data, StateHandlerInterface $state)
	{
		$oauth = $this->getOauthConsumer();

		if (!isset($state['orb_oauth_twitter_rtoken'])) {
			if ($this->logger) {
				$this->logger->log("[Twitter] authenticateCallback fail: Missing token", 'DEBUG');
			}
			return new Result(Result::FAILURE, null, array('error_code' => 'invalid_token', 'error_message' => 'Invalid verify token'));
		}

		if ($this->logger) {
			$this->logger->log("[Twitter] authenticateCallback token: {$state['orb_oauth_twitter_rtoken']}", 'ERR');
		}

		$access_token = $oauth->getAccessToken($callback_data, $state['orb_oauth_twitter_rtoken']);
		unset($state['orb_oauth_twitter_rtoken']);

		$client = $access_token->getHttpClient($this->getOauthConfig());
		$client->setUri('https://api.twitter.com/1.1/account/verify_credentials.json');
		$client->setMethod(\Zend\Http\Request::METHOD_GET);
		$response = $client->send();

		if ($this->logger) {
			$this->logger->log("[Twitter] authenticateCallback verify_credentials: {$response->getBody()}", 'DEBUG');
		}

		$account_data = @json_decode($response->getBody(), true);

		if (!$account_data OR !isset($account_data['id'])) {
			if ($this->logger) {
				$this->logger->log("[Twitter] authenticateCallback failed_verify_credentials", 'DEBUG');
			}
			return new Result(Result::FAILURE, null, array('error_code' => 'failed_verify_credentials', 'error_message' => 'Failed to call API service to verify credentials'));
		}

		$raw_userinfo = array(
			'access_token'        => $access_token->getToken(),
			'access_token_secret' => $access_token->getTokenSecret(),
			'identity'            => $account_data['id_str'],
			'identity_friendly'   => $account_data['screen_name'],
			'fullname'            => $account_data['name'],
			'url'                 => $account_data['url'],
			'nickname'            => $account_data['screen_name'],
			'raw'                 => $account_data,
		);

		$identity = new \Orb\Auth\Identity($account_data['id'], $raw_userinfo);
		$identity->setFriendlyIdentity($account_data['screen_name']);

		$result = new Result(Result::SUCCESS, $identity);

		if ($this->logger) {
			$this->logger->log("[Twitter] authenticateCallback success: {$account_data['screen_name']}", 'DEBUG');
		}

		return $result;
	}

	/**
	 * @return \Zend\OAuth\Consumer
	 */
	public function getOauthConsumer()
	{
		return new \Zend\OAuth\Consumer($this->getOauthConfig());
	}

	public function getOauthConfig()
	{
		return array(
			'callbackUrl' => $this->getCallbackUrl(),
			'siteUrl' => 'https://api.twitter.com/oauth',
			'consumerKey' => $this->consumer_key,
			'consumerSecret' => $this->consumer_secret,
		);
	}

}
