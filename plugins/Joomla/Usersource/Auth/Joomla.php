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

namespace Joomla\Usersource\Auth;

use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Auth\Adapter;
use Orb\Auth\Identity;
use Orb\Auth\Result;
use Orb\Util\Arrays;

use Orb\Log\Logger;
use Orb\Log\Loggable;

class Joomla implements Adapter\FormLoginInterface,	Adapter\UserInfoFetchableInterface, Loggable
{
	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var string
	 */
	protected $set_username;

	/**
	 * @var string
	 */
	protected $set_password;

	/**
	 * @var \Orb\Util\OptionsArray
	 */
	protected $options;

	public function __construct(array $options)
	{
		$this->initOptions();
		$this->options->setArray($options);
	}

	protected function initOptions()
	{
		$this->options = new \Orb\Util\OptionsArray(array(
			'joomla_url' => '',
			'joomla_secret' => '',
		));
	}

	/**
	 * @param string $username
	 * @param string $password
	 */
	public function setFormData(array $form_data)
	{
		$this->set_username = !empty($form_data['username']) ? (string)$form_data['username'] : '';
		$this->set_password = !empty($form_data['password']) ? (string)$form_data['password'] : '';
	}

	public function authenticate()
	{
		if (!$this->set_username) {
			return new Result(Result::FAILURE, null, array('error_code' => 'missing_input_username', 'error_message' => 'No username provided'));
		}
		if (!$this->set_password) {
			return new Result(Result::FAILURE, null, array('error_code' => 'missing_input_password', 'error_message' => 'No password provided'));
		}

		$time_start = microtime(true);
		if ($this->logger) {
			$this->logger->log("START Joomla::authenticate", Logger::DEBUG);
			$this->logger->log("Options: " . trim(Arrays::implodeTemplate("{KEY}({VAL}) ")), Logger::DEBUG);
			$this->logger->log("Request: {$this->set_username}:{$this->set_password}", Logger::DEBUG);
		}

		try {
			$record = $this->_callJoomlaPlugin(array('action' => 'auth', 'username' => $this->set_username, 'password' => $this->set_password));
			if ($record && isset($record['user_info']) && $record['user_info']) {
				$userinfo = $record['user_info'];
			} else {
				return new Result(Result::FAILURE_INVALID_CREDS);
			}
		} catch (\Exception $e) {
			if ($this->logger) {
				$this->logger->log("Exception: {$e->getCode()} {$e->getMessage()}\n{$e->getTraceAsString()}", Logger::ERR);
				return new Result(Result::FAILURE_EXCEPTION, null, array('error_code' => 'exception', 'error_message' => 'An exception occurred', 'exception' => $e));
			}
			return new Result(Result::FAILURE_INVALID_CREDS);
		}

		$identity = $this->getIdentityFromUserInfo($userinfo);

		if ($this->logger) {
			$this->logger->log("Found user " . $identity->getIdentity(), Logger::DEBUG);
			$this->logger->log(sprintf("END Joomla::authenticate (took %.4fs)", microtime(true)-$time_start), Logger::DEBUG);
		}

		return new Result(Result::SUCCESS, $identity);
	}


	/**
	 * Get an Identity from a userinfo array
	 *
	 * @param array $userinfo
	 * @return \Orb\Auth\Identity
	 */
	public function getIdentityFromUserInfo(array $userinfo)
	{
		$userinfo = Arrays::removeEmptyString($userinfo);
		$identity = new Identity($userinfo['id'], $userinfo);
		$identity->setFriendlyIdentity($userinfo['username']);

		return $identity;
	}


	/**
	 * @param mixed $id
	 * @param string|null $id_type
	 * @return array
	 */
	public function getUserInfoFromIdentity($id, $id_type = null)
	{
		$try = array();
		if ($id_type === 'email' || (!$id_type && \Orb\Validator\StringEmail::isValueValid($id))) {
			$try[] = 'getUserInfoForEmail';
		}

		if (!$id_type || $id_type == 'username') {
			$try[] = 'getUserInfoForUsername';
		}

		if ($id_type == 'id' || (!$id_type && \Orb\Util\Numbers::isInteger($id))) {
			$try[] = 'getUserInfoForId';
		}

		$userinfo = null;
		foreach ($try as $m) {
			$userinfo = $this->$m($this->set_username);
			if ($userinfo) {
				break;
			}
		}

		return $userinfo;
	}


	/**
	 * @param $id
	 * @return array|null
	 */
	public function getUserInfoForEmail($email)
	{
		$record = $this->_callJoomlaPlugin(array('action' => 'lookup_user_email', 'user_email' => $email));
		if ($record && is_array($record) && isset($record['user_info'])) {
			return $record['user_info'];
		}

		return null;
	}


	/**
	 * @param $id
	 * @return array|null
	 */
	public function getUserInfoForUsername($username)
	{
		$record = $this->_callJoomlaPlugin(array('action' => 'lookup_user_username', 'user_username' => $username));
		if ($record && is_array($record) && isset($record['user_info'])) {
			return $record['user_info'];
		}

		return null;
	}


	/**
	 * @param $id
	 * @return array|null
	 */
	public function getUserInfoForId($id)
	{
		$record = $this->_callJoomlaPlugin(array('action' => 'lookup_user_id', 'user_id' => $id));
		if ($record && is_array($record) && isset($record['user_info'])) {
			return $record['user_info'];
		}

		return null;
	}


	public function getSsoShareSessionHtml($user_id)
	{
		$time = time();
		$params = array('action' => 'init_session', 'user_id' => $user_id);
		$params = base64_encode(json_encode($params));
		$params = $time . '_' . sha1($time . $params . $this->options->get('joomla_secret')) . '_' . $params;

		$req_params = array('DATA' => $params, '__dp_call' => 1);
		$url = $this->options->get('joomla_url') . '/index.php?' . http_build_query($req_params);

		return '<iframe width="1" height="0" border="0" frameborder="0" style="width:1px; height: 1px; overflow: hidden; border: none; opacity: 0; position: absolute; top: 0; left: 0; margin: 0; padding: 0; background: transparent;" src="'.$url.'"></iframe>';
	}


	/**
	 * @param $id
	 * @return array
	 */
	public function _callJoomlaPlugin(array $params)
	{
		$time = time();
		$params = base64_encode(json_encode($params));
		$params = $time . '_' . sha1($time . $params . $this->options->get('joomla_secret')) . '_' . $params;

		$req_params = array('DATA' => $params, '__dp_call' => 1);

		try {
			require_once(DP_ROOT . '/src/Application/DeskPRO/LowUtil/RemoteRequest.php');
			$result = \DeskPRO_LowUtil_RemoteRequester::create()->request($this->options->get('joomla_url') . '/index.php', $req_params);
			$result = @json_decode($result, true);
			if (!$result) {
				$result = array();
			}

			return $result;
		} catch (\Exception $e) {
			KernelErrorHandler::logException($e, false, 'joomla_call_err');
			return array();
		}
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
}