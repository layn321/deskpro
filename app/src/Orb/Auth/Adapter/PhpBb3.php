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

class PhpBb3 extends DbTable
{
	const OPT_TABLE_PREFIX = 'table_prefix';
	const OPT_CHECK_SERVICE_URL = 'check_service_url';
	const OPT_CHECK_SERVICE_KEY = 'check_service_key';

	protected function initOptions()
	{
		parent::initOptions();

		$this->options[self::OPT_TABLE]           = $this->options->get(self::OPT_TABLE_PREFIX, '') . 'users';
		$this->options[self::OPT_FIELD_ID]        = 'user_id';
		$this->options[self::OPT_FIELD_USERNAME]  = 'username';
		$this->options[self::OPT_FIELD_PASSWORD]  = 'user_password';
		$this->options[self::OPT_FIELD_EMAIL]     = 'user_email';
	}

	protected function isValidPassword(array $userinfo, $password_input)
	{
		$data = array(
			'key'       => $this->options->get(self::OPT_CHECK_SERVICE_KEY) ?: 'dp_login_check',
			'username'  => $this->set_username,
			'password'  => $this->set_password
		);

		$context = stream_context_create(array(
			'http' => array(
				'timeout'  => 10,
				'method'   => 'POST',
				'header'   => 'Content-type: application/x-www-form-urlencoded',
				'content'  => http_build_query($data, null, '&')
			)
		));

		$result = @file_get_contents($this->options->get(self::OPT_CHECK_SERVICE_URL), null, $context);

		if (!$result || strpos($result, 'LOGIN_SUCCESS') === false) {
			return false;
		}

		return true;
	}
}