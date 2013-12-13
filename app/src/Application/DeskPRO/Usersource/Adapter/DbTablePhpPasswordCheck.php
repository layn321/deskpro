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

namespace Application\DeskPRO\Usersource\Adapter;

use Orb\Auth\Identity;
use Doctrine\DBAL\Connection;

class DbTablePhpPasswordCheck extends AbstractAdapter
{
	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

	public function getFieldsFromIdentity(Identity $identity)
	{
		$info = $identity->getRawData();
		return array(
			'name'             => isset($info['name']) ? $info['name'] : '',
			'first_name'       => isset($info['first_name']) ? $info['first_name'] : '',
			'last_name'        => isset($info['last_name']) ? $info['last_name'] : '',
			'email'            => isset($info['email_address']) ? $info['email_address'] : '',
			'email_confirmed'  => true,
		);
	}


	/**
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getDb()
	{
		if ($this->db) return $this->db;

		$pdo = new \PDO(
			$this->usersource->getOption('db_dsn'),
			$this->usersource->getOption('db_username'),
			$this->usersource->getOption('db_password')
		);

		$this->db = \Doctrine\DBAL\DriverManager::getConnection(array('pdo' => $pdo));

		return $this->db;
	}


	/**
	 * Find a user identity just by an email address.
	 *
	 * @param $id_input
	 * @return \Orb\Auth\Identity|null
	 */
	public function findIdentityByInput($id_input)
	{
		/** @var $adapter \Orb\Auth\Adapter\DbTable.php */
		$adapter = $this->getAuthAdapter();

		$userinfo = null;
		if (\Orb\Validator\StringEmail::isValueValid($id_input)) {
			$userinfo = $adapter->getUserInfoForEmail($id_input);
		}
		if (!$userinfo) {
			$userinfo = $adapter->getUserInfoForUsername($id_input);
		}

		if (!$userinfo) {
			return null;
		}

		$identify = $adapter->getIdentityFromUserInfo($userinfo);
		return $identify;
	}


	/**
	 * @return \Orb\Auth\Adapter\DbTablePhpPasswordCheck
	 */
	protected function _createAuthAdapterObject()
	{
		return new \Orb\Auth\Adapter\DbTablePhpPasswordCheck($this->getDb(), $this->usersource->options);
	}


	/**
	 * @return array
	 */
	public function getCapabilities()
	{
		return array(
			'form_login',
			'get_user_info',
			'find_identity'
		);
	}


	/**
	 * @param  mixed $capability
	 * @return bool
	 */
	public function isCapable($capability)
	{
		return in_array($capability, $this->getCapabilities());
	}
}