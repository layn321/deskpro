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

class Xenforo extends DbTable
{
	protected function initOptions()
	{
		parent::initOptions();

		$this->options[self::OPT_TABLE]           = 'xf_user';
		$this->options[self::OPT_FIELD_ID]        = 'user_id';
		$this->options[self::OPT_FIELD_USERNAME]  = 'username';
		$this->options[self::OPT_FIELD_EMAIL]     = 'email';
	}

	protected function getUserInfo($where, $param)
	{
		$sql = "
			SELECT user.user_id, user.username, user.email,
				auth.scheme_class, auth.data
			FROM xf_user AS user
			INNER JOIN xf_user_authenticate AS auth ON (user.user_id = auth.user_id)
			WHERE $where
		";

		$result = $this->db->fetchAssoc($sql, array($param));
		if (!$result) {
			return null;
		}

		return $result;
	}

	public function getUserInfoForUsername($username)
	{
		return $this->getUserInfo("user.username = ?", $username);
	}

	public function getUserInfoForEmail($email)
	{
		return $this->getUserInfo("user.email = ?", $email);
	}

	public function getUserInfoForId($id)
	{
		return $this->getUserInfo("user.user_id = ?", $id);
	}

	protected function isValidPassword(array $userinfo, $password_input)
	{
		$data = @unserialize($userinfo['data']);
		if (!$data) {
			return false;
		}

		switch ($userinfo['scheme_class']) {
			case 'XenForo_Authentication_Core':
			case 'XenForo_Authentication_Default':
				if ($data['hashFunc'] == 'sha256') {
					$hash = hash('sha256', hash('sha256', $password_input) . $data['salt']);
				} else {
					$hash = sha1(sha1($password_input) . $data['salt']);
				}
				return $data['hash'] === $hash;

			case 'XenForo_Authentication_Core12':
				$hasher = new \PasswordHash(8, true);
				return $hasher->CheckPassword($password_input, $data['hash']);

			case 'XenForo_Authentication_IPBoard':
				$hash = md5(md5($data['salt']) . md5($password_input));
				return $data['hash'] === $hash;

			case 'XenForo_Authentication_PhpBb3':
				$hasher = new \PasswordHash(8, true);
				return $hasher->CheckPassword($password_input, $data['hash']);

			case 'XenForo_Authentication_vBulletin':
				$hash = md5(md5($password_input) . $data['salt']);
				return $data['hash'] === $hash;

			default:
				return false;
		}
	}
}