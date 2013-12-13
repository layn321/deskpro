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

namespace Magento\Usersource\Auth;

use Orb\Auth\Adapter;
use Orb\Auth\Identity;
use Orb\Auth\Result;
use Orb\Util\Arrays;

use Orb\Log\Logger;
use Orb\Log\Loggable;

class Magento implements Adapter\FormLoginInterface, Adapter\CookieLoginInterface,
	Adapter\JsSsoInterface, Adapter\UserInfoFetchableInterface, Loggable
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
			'url' => '',
			'api_user' => '',
			'api_key' => '',
			'website_id' => 1,
			'sso_cookie' => false,
			'magento_path' => '',
			'sso_js' => false
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
			$this->logger->log("START Magento::authenticate", Logger::DEBUG);
			$this->logger->log("Options: " . trim(Arrays::implodeTemplate("{KEY}({VAL}) ")), Logger::DEBUG);
			$this->logger->log("Request: {$this->set_username}:{$this->set_password}", Logger::DEBUG);
		}

		try {
			$pass = false;
			$userinfo = $this->getUserInfoForEmail($this->set_username);
			if ($userinfo) {
				$pass = $this->isValidPassword($userinfo, $this->set_password);
			}

			if (!$pass) {
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
			$this->logger->log(sprintf("END Magento::authenticate (took %.4fs)", microtime(true)-$time_start), Logger::DEBUG);
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

		// Map fields from the raw userinfo to common fields that most
		// auth adapters use by convention
		$map = array(
			'email'       => 'email_address',
			'first_name'  => 'first_name',
			'last_name'   => 'last_name',
		);

		foreach ($map as $field_key => $info_key) {
			$field = $this->options[$field_key];
			if (!$field || empty($userinfo[$field])) {
				continue;
			}

			$userinfo[$info_key] = $userinfo[$field];
		}

		$identity = new Identity($userinfo['customer_id'], $userinfo);

		return $identity;
	}

	protected function isValidPassword(array $userinfo, $password_input)
	{
		$parts = explode(':', $userinfo['password'], 2);
		if (count($parts) != 2) {
			return false;
		}

		return (md5($parts[1] . $password_input) === $parts[0]);
	}

	/**
	 * @return array
	 */
	public function getUserInfoFromIdentity($id, $id_type = null)
	{
		$try = array();
		if ($id_type === 'email' || (!$id_type && \Orb\Validator\StringEmail::isValueValid($id))) {
			$try[] = 'getUserInfoForEmail';
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

	public function getSsoHtmlLoaderOutput(
		\Application\DeskPRO\Entity\Usersource $source,
		\Application\DeskPRO\Twig\Extension\TemplatingExtension $extension,
		\Application\DeskPRO\Entity\Person $person,
		$is_first_page
	)
	{
		if (!$this->options->get('sso_js')) {
			return '';
		}

		$magento_url = $this->options['url'];

		return
			'<script type="text/javascript">
			    if ((!window.DESKPRO_PERSON_ID || window.DESKPRO_PERSON_ID === 0) && !$.cookie(\'dplogout\')) {
					window.dpMagentoLogin = function(login) {
						if (window.DeskPRO_Window && window.DeskPRO_Window.showAutoSignInOverlay) {
							window.DeskPRO_Window.showAutoSignInOverlay();
						}
						login(BASE_URL + \'login/usersource-sso/\' + ' . $source->id . '+ \'/\');
					};
					(function(d) {
						var s = d.createElement(\'script\'), ref = d.getElementsByTagName(\'script\')[0];
						s.async = true;
						s.src = \'' . $magento_url . '/dpsso/\';
						ref.parentNode.insertBefore(s, ref);
					})(document);
				}
			</script>';
	}

	public function getSsoLoginActionResult(\Application\DeskPRO\Controller\AbstractController $controller)
	{
		$id = intval($controller->getRequest()->get('id'));
		$key = strval($controller->getRequest()->get('key'));

		$record = $this->_callMagentoApi('dp_sso.validate', array('id' => $id, 'key' => $key));

		if (!empty($record['customer_id'])) {
			$results = array(
				'customer_id' => $record['customer_id'],
				'email' => $record['email'],
				'password' => $record['password_hash'],
				'first_name' => $record['firstname'],
				'last_name' => $record['lastname']
			);

			$identity = $this->getIdentityFromUserInfo($results);
			return new Result(Result::SUCCESS, $identity);
		} else {
			setcookie('dpmagento', 'skip', null, '/');
			return new Result(Result::FAILURE_INVALID_CREDS);
		}
	}

	protected function _callMagentoApi($method, array $params = array())
	{
		$url = $this->options['url'];
		$user = $this->options['api_user'];
		$key = $this->options['api_key'];

		try {
			$error = error_reporting();
			error_reporting($error & ~E_WARNING);
			$client = new \SoapClient($url . '/api?wsdl');
			error_reporting($error);
		} catch (\SoapFault $e) {
			return false;
		}

		try {
			$session = $client->login($user, $key);
		} catch (\SoapFault $e) {
			return false;
		}

		return $client->call($session, $method, $params ? array($params) : array());
	}

	public function getUserInfoForEmail($email)
	{
		$results = $this->_callMagentoApi('customer.list', array('email' => $email, 'website_id' => $this->options->get('website_id')));
		if ($results && is_array($results)) {
			$record = reset($results);

			return array(
				'customer_id' => $record['customer_id'],
				'email' => $record['email'],
				'password' => $record['password_hash'],
				'first_name' => $record['firstname'],
				'last_name' => $record['lastname']
			);
		}

		return null;
	}

	public function getUserInfoForId($id)
	{
		$record = $this->_callMagentoApi('customer.info', array('customerId' => $id));
		if ($record && is_array($record)) {
			return array(
				'customer_id' => $record['customer_id'],
				'email' => $record['email'],
				'password' => $record['password_hash'],
				'first_name' => $record['firstname'],
				'last_name' => $record['lastname']
			);
		}

		return null;
	}

	public function authenticateCookie(array $cookies)
	{
		if (!$this->options->get('sso_cookie')) {
			return false;
		}

		$magento_path = $this->options->get('magento_path');
		if (!$magento_path || !is_dir($magento_path)) {
			return false;
		}

		$cookie_name = 'frontend';
		if (empty($cookies[$cookie_name]) && !is_string($cookies[$cookie_name])) {
			return false;
		}

		$session_data = false;

		$session = preg_replace('/[^a-z0-9_]/i', '', $cookies[$cookie_name]);
		$session_file = $magento_path . '/var/session/sess_' . $session;

		if (file_exists($session_file) && is_readable($session_file)) {
			$session_data = file_get_contents($session_file);
		} else {
			$config_file = $magento_path . '/app/etc/local.xml';
			if (file_exists($config_file) && is_readable($config_file)) {
				$config = file_get_contents($config_file);

				preg_match_all(
					'#<(host|username|password|dbname|table_prefix)>(\<!\[CDATA\[)??(.*)(\]\]>)??</\\1>#siU',
					$config, $matches, PREG_SET_ORDER
				);

				$parts = array(
					'host' => 'localhost',
					'username' => 'root',
					'password' => '',
					'dbname' => '',
					'table_prefix' => ''
				);
				foreach ($matches AS $match) {
					$parts[$match[1]] = $match[3];
				}

				try {
					$pdo = new \PDO('mysql:host=' . $parts['host'] . ';dbname=' . $parts['dbname'], $parts['username'], $parts['password']);
					$session_data = $pdo->query('
						SELECT session_data
						FROM ' . $parts['table_prefix'] . 'core_session
						WHERE session_id = ' . $pdo->quote($session)
					)->fetchColumn();
				} catch (\Exception $e) {}
			}
		}

		$userinfo = false;

		if ($session_data) {
			$orig = $_SESSION;

			session_decode($session_data);
			$data = $_SESSION;

			$_SESSION = $orig;

			if (!empty($data['core']['visitor_data']['customer_id'])) {
				$customer_id = intval($data['core']['visitor_data']['customer_id']);
				$userinfo = $this->getUserInfoForId($customer_id);
			}
		}

		return ($userinfo ?: false);
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