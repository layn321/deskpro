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

class EzPublish extends DbTable
{
	protected function initOptions()
	{
		parent::initOptions();

		$this->options[self::OPT_TABLE]           = 'ezuser';
		$this->options[self::OPT_FIELD_ID]        = 'contentobject_id';
		$this->options[self::OPT_FIELD_USERNAME]  = 'login';
		$this->options[self::OPT_FIELD_PASSWORD]  = 'password_hash';
		$this->options[self::OPT_FIELD_EMAIL]     = 'email';
	}

	protected function isValidPassword(array $userinfo, $password_input)
	{
		switch ($userinfo['password_hash_type']) {

			// EZ_USER_PASSWORD_HASH_MD5_PASSWORD
			case 2:
				$password_check = md5("{$userinfo['login']}\n{$password_check}");
				break;

			// EZ_USER_PASSWORD_HASH_MYSQL
			case 4:
				global $db;
				$password_check = $db->query_return_first("SELECT PASSWORD('" . $db->escape($password_check) . "')");
				break;

			// EZ_USER_PASSWORD_HASH_PLAINTEXT
			case 5:
				$password_check = $password_check;
				break;

			// EZ_USER_PASSWORD_HASH_CRYPT
			case 6:
				$password_check = crypt($password_check);
				break;

			// EZ_USER_PASSWORD_HASH_MD5_PASSWORD
			case 1:
			default:
				$password_check = md5($password_check);
		}

		if ($userinfo[$this->options[self::OPT_FIELD_PASSWORD]] == $password_check) {
			return true;
		}

		return false;
	}
}