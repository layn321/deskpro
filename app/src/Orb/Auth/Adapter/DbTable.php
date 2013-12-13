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

namespace Orb\Auth\Adapter;

use Orb\Auth\Identity;
use Orb\Auth\Result;
use Orb\Util\Arrays;

use Orb\Log\Logger;
use Orb\Log\Loggable;

use Doctrine\DBAL\Connection;

class DbTable implements FormLoginInterface, UserInfoFetchableInterface, Loggable
{
	const OPT_TABLE              = 'table';
	const OPT_FIELD_ID           = 'field_id';
	const OPT_FIELD_USERNAME     = 'field_username';
	const OPT_FIELD_EMAIL        = 'field_email';
	const OPT_FIELD_PASSWORD     = 'field_password';
	const OPT_FIELD_FIRST_NAME   = 'field_first_name';
	const OPT_FIELD_LAST_NAME    = 'field_last_name';
	const OPT_FIELD_NAME         = 'field_name';
	const OPT_PASSWORD_HASH      = 'password_hash_scheme';
	const OPT_PASSWORD_CHECK_CALLBACK = 'password_check_callback';

	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

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

	public function __construct(Connection $db, array $options)
	{
		$this->db = $db;

		$this->initOptions();
		$this->options->setArray($options);
	}

	protected function initOptions()
	{
		$opts = array(
			self::OPT_TABLE            => '',
			self::OPT_FIELD_ID         => 'id',
			self::OPT_FIELD_USERNAME   => null,
			self::OPT_FIELD_EMAIL      => null,
			self::OPT_FIELD_PASSWORD   => 'password',
			self::OPT_FIELD_FIRST_NAME => null,
			self::OPT_FIELD_LAST_NAME  => null,
			self::OPT_FIELD_NAME       => null,
		);
		$this->options = new \Orb\Util\OptionsArray($opts);
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
			$this->logger->log("START DbTable::authenticate", Logger::DEBUG);

			$log_opt = $this->options->all();
			$log_opt['db_password'] = '***';
			$this->logger->log("Options: " . trim(Arrays::implodeTemplate($log_opt, "{KEY}: {VAL}\n")), Logger::DEBUG);
			$this->logger->log("Request: {$this->set_username}:{$this->set_password}", Logger::DEBUG);
		}

		try {

			$try = array();
			if (\Orb\Validator\StringEmail::isValueValid($this->set_username)) {
				$try[] = 'getUserInfoForEmail';
			}
			$try[] = 'getUserInfoForUsername';

			$userinfo = null;
			foreach ($try as $m) {
				$userinfo = $this->$m($this->set_username);
				if (!$userinfo) {
					continue;
				}

				if (!empty($this->options[self::OPT_PASSWORD_CHECK_CALLBACK])) {
					$pass = call_user_func($this->options[self::OPT_PASSWORD_CHECK_CALLBACK], $userinfo, $this->set_password);
				} else {
					$pass = $this->isValidPassword($userinfo, $this->set_password);
				}

				if ($pass) {
					break;
				} else {
					$userinfo = null;
				}
			}

			if (!$userinfo) {
				return new Result(Result::FAILURE_INVALID_CREDS);
			}

		} catch (\Exception $e) {
			if ($this->logger) {
				$this->logger->log("Exception: {$e->getCode()} {$e->getMessage()}\n{$e->getTraceAsString()}", Logger::ERR);
			}
			return new Result(Result::FAILURE_EXCEPTION, null, array('error_code' => 'exception', 'error_message' => 'An exception occurred', 'exception' => $e));
		}

		$identity = $this->getIdentityFromUserInfo($userinfo);

		if ($this->logger) {

			if ($userinfo) {
				$this->logger->log("Found user " . $identity->getIdentity(), Logger::DEBUG);
			} else {
				$this->logger->log("No user found", Logger::DEBUG);
			}

			$this->logger->log(sprintf("END DbTable::authenticate (took %.4fs)", microtime(true)-$time_start), Logger::DEBUG);
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
			self::OPT_FIELD_EMAIL       => 'email_address',
			self::OPT_FIELD_FIRST_NAME  => 'first_name',
			self::OPT_FIELD_LAST_NAME   => 'last_name',
			self::OPT_FIELD_NAME        => 'name',
		);

		foreach ($map as $field_key => $info_key) {
			$field = $this->options[$field_key];
			if (!$field || empty($userinfo[$field])) {
				continue;
			}

			$userinfo[$info_key] = $userinfo[$field];
		}

		$identity = new Identity($userinfo[$this->options[self::OPT_FIELD_ID]], $userinfo);

		return $identity;
	}


	/**
	 * Checks an inputted password against a found user info record to see if it matches
	 *
	 * @param string $userinfo
	 * @param string $password_input
	 * @return bool
	 */
	protected function isValidPassword(array $userinfo, $password_input)
	{
		$field = $this->options[self::OPT_FIELD_PASSWORD];

		switch ($this->options[self::OPT_PASSWORD_HASH]) {
			case 'md5':
				$password_compare = md5($password_input);
				break;

			case 'sha1':
				$password_compare = sha1($password_input);
				break;

			default:
				$password_compare = $password_input;
				break;
		}

		return ($userinfo[$field] == $password_compare);
	}


	/**
	 * Get user info from a username
	 *
	 * @param $username
	 * @return array
	 */
	public function getUserInfoForUsername($username)
	{
		if (!$this->options[self::OPT_FIELD_USERNAME]) {
			return null;
		}

		$table = $this->options[self::OPT_TABLE];
		$field = $this->options[self::OPT_FIELD_USERNAME];
		$driver =  $this->db->getDriver()->getName();
		if ( $driver == 'pdo_dblib'|| $driver == 'pdo_sqlsrv' ){
          $sql = "SELECT TOP 1 * FROM $table WHERE $field = ? ";
        }
        else {
          $sql = "SELECT * FROM $table WHERE $field = ? LIMIT 1";
        }

		$result = $this->db->fetchAssoc($sql, array($username));
		if (!$result) {
			return null;
		}

		return $result;
	}


	/**
	 * Get user info from an email address
	 *
	 * @return array
	 */
	public function getUserInfoForEmail($email)
	{
		if (!$this->options[self::OPT_FIELD_EMAIL]) {
			return null;
		}

		$table = $this->options[self::OPT_TABLE];
		$field = $this->options[self::OPT_FIELD_EMAIL];
        $driver =  $this->db->getDriver()->getName();
        if ( $driver == 'pdo_dblib'|| $driver == 'pdo_sqlsrv' ){
          $sql = "SELECT TOP 1 * FROM $table WHERE $field = ? ";
        }
        else {
          $sql = "SELECT * FROM $table WHERE $field = ? LIMIT 1";
        }

		$result = $this->db->fetchAssoc($sql, array($email));
		if (!$result) {
			return null;
		}

		return $result;
	}


	/**
	 * Get user info from an email address
	 *
	 * @return array
	 */
	public function getUserInfoForId($id)
	{
		$table = $this->options[self::OPT_TABLE];
		$field = $this->options[self::OPT_FIELD_ID];
		$driver =  $this->db->getDriver()->getName();
		if ( $driver == 'pdo_dblib'|| $driver == 'pdo_sqlsrv' ){
          $sql = "SELECT TOP 1 * FROM $table WHERE $field = ? ";
        }
        else {
			$sql = "SELECT * FROM $table WHERE $field = ? LIMIT 1";
		}
		$result = $this->db->fetchAssoc($sql, array($id));
		if (!$result) {
			return null;
		}

		return $result;
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

		if ($id_type == 'username' || !$id_type) {
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
