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
 * @subpackage Scraper
 */

namespace Orb\Scraper;

/**
 * Scrapes a users twitter feed. A user must have a public feed for this to work.
 */
class TwitterFeed
{
	/**
	 * @param int $user_id The users ID
	 * @return ItemInterface
	 */
	function getData($user_id)
	{
		$twitter_url = 'http://twitter.com/statuses/user_timeline/' . $user_id . '.json';

		$data = file_get_contents($url);
		$data = json_decode($data, true);

		$userinfo = null;
		$tweets = array();

		foreach ($data as $item) {
			if ($userinfo === null) {
				$userinfo = $item['user'];
			}
			unset($item['user']);
			$tweers[] = $item;
		}

		$item = new \Orb\Scraper\Item(
			$userinfo['id'],
			$userinfo['screen_name'],
			array('userinfo' => $userinfo, 'tweets' => $tweets)
		);

		return $item;
	}
}
