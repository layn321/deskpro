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
*/

namespace Cloud\AdminBundle\Controller;

use Application\AdminBundle\Controller\TwitterAccountController as BaseTwitterAccountController;
use Application\DeskPRO\Entity\TwitterAccount;
use Application\DeskPRO\App;

class TwitterAccountController extends BaseTwitterAccountController
{
	/**
	 * @return \Doctrine\DBAL\Connection|bool
	 */
	protected function _getCloudDb()
	{
		if (App::getConfig('twitter.cloud_db_host')) {
			$db_conf = array(
				'host' => App::getConfig('twitter.cloud_db_host'),
				'user' => App::getConfig('twitter.cloud_db_user'),
				'password' => App::getConfig('twitter.cloud_db_password'),
				'dbname' => App::getConfig('twitter.cloud_db_dbname'),
				'driver' => 'pdo_mysql'
			);
			return \Doctrine\DBAL\DriverManager::getConnection($db_conf);
		} else {
			return false;
		}
	}

	protected function _accountCreate(TwitterAccount $account, $existed)
	{
		$cloud_db = $this->_getCloudDb();
		if ($cloud_db) {
			$cloud_db->insert('cloud_twitter_messages', array(
				'message_type' => 'add',
				'db' => App::getConfig('db.dbname'),
				'user_id' => $account->user->id,
				'account_id' => $account->id,
				'data' => serialize(array(
					'oauth_token' => $account->oauth_token,
					'oauth_token_secret' => $account->oauth_token_secret
				))
			));
			$cloud_db->close();
		}
	}

	protected function _accountRemove(TwitterAccount $account)
	{
		$cloud_db = $this->_getCloudDb();
		if ($cloud_db) {
			$cloud_db->insert('cloud_twitter_messages', array(
				'message_type' => 'remove',
				'db' => App::getConfig('db.dbname'),
				'user_id' => $account->user->id,
				'account_id' => $account->id
			));
			$cloud_db->close();
		}
	}
}
