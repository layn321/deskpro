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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;

use \Doctrine\ORM\EntityRepository;

class BanEmail extends AbstractEntityRepository
{
	/**
	 * Get a list of emails suitable for display
	 */
	public function getList()
	{
		$list = App::getDb()->fetchAllCol("
			SELECT banned_email
			FROM ban_emails
			ORDER BY banned_email ASC
		");

		return $list;
	}

	public function getPatterns($reload = false)
	{
		static $list;

		if ($reload || !$list) {
			$list = App::getDb()->fetchAllCol("
				SELECT banned_email
				FROM ban_emails
				WHERE is_pattern = 1
				ORDER BY banned_email ASC
			");
		}

		return $list;
	}

	/**
	 * Check if an email address is banned
	 *
	 * @param $email
	 * @return bool
	 */
	public function isEmailBanned($email, &$match = null)
	{
		$email = strtolower(trim($email));

		$banned_email = App::getDb()->fetchColumn("
			SELECT banned_email
			FROM ban_emails
			WHERE banned_email = ?
		", array($email));

		if ($banned_email) {
			$match = $banned_email;
			return true;
		}

		$patterns = $this->getPatterns();
		foreach ($patterns as $pattern) {
			if (\Orb\Util\Strings::isStarMatch($pattern, $email)) {
				$match = $pattern;
				return true;
			}
		}

		return false;
	}
}
