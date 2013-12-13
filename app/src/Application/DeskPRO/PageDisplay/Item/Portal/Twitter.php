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
 * @subpackage PageDisplay
 */

namespace Application\DeskPRO\PageDisplay\Item\Portal;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\PortalPageDisplay;

use Orb\Util\Strings;

class Twitter extends PortalItemAbstract implements CacheableItem
{
	public function getCacheOptions()
	{
		return array(
			'lifetime' => 1800, // 30 minutes
			'user_indifferent' => true
		);
	}

	public function getHtml()
	{
		if (!App::getConfig('enable_twitter')) {
			return '';
		}

		if ($this->section == 'sidebar') {
			return $this->getSidebarHtml();
		}

		return '';
	}

	public function getSidebarHtml()
	{
		$twitter_name = $this->getOption('twitter_name');
		if (!$twitter_name) {
			$twitter_name = 'deskpro';
		}

		$max_items = $this->getOption('max_items');
		if ($max_items <= 0) {
			$max_items = 5;
		}

		$token = $this->getOption('token');
		$secret = $this->getOption('secret');

		if ($token && $secret) {
			$twitter = \Application\DeskPRO\Service\Twitter::getUserTwitterApi($token, $secret);

			$feed_items = array();

			try {
				$tweets = $twitter->get_statusesUser_timeline(array(
					'screen_name' => $twitter_name,
					'count' => $max_items
				));
				foreach ($tweets AS $tweet) {
					$date = new \DateTime($tweet->created_at);

					$feed_items[] = array(
						'id' => $tweet->id_str,
						'text' => $this->parseText($tweet->text),
						'date' => $date,
						'screen_name' => $tweet->user->screen_name,
						'name' => $tweet->user->name
					);
				}
			} catch (\Exception $e) {}
		} else {
			$feed_items = array();
		}

		return $this->renderView('UserBundle:Portal:twitter-sidebar.html.twig', array(
			'twitter_name' => $twitter_name,
			'feed_items' => $feed_items
		));
	}

	protected function parseText($text)
	{
		$text = htmlspecialchars($text);
		$text = Strings::autoLink($text, false);
		$text = preg_replace('#(^|\W)(@([a-zA-Z0-9]+))(\W|$)#', '$1<a href="http://twitter.com/$3">$2</a>$4', $text);

		return $text;
	}
}
