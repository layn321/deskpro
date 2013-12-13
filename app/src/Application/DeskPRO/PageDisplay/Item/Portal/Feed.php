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

use Zend\Feed\Reader\Reader;
use Application\DeskPRO\App;
use Application\DeskPRO\Entity\PortalPageDisplay;

class Feed extends PortalItemAbstract implements CacheableItem
{
	public function getCacheOptions()
	{
		return array(
			'lifetime' => 43200, /*12 hours*/
			'force_cache' => true
		);
	}

	public function getHtml()
	{
		try {
			$channel = Reader::import($this->getOption('feed_url'));
		} catch (\Exception $e) {
			return '';
		}

		$feed_info = array(
			'title'       => $channel->getTitle(),
			'description' => $channel->getDescription(),
			'link'        => $channel->getLink(),
		);

		$feed_items = array();
		foreach ($channel as $item) {
			$feed_items[] = array(
				'title'       => $item->getTitle(),
				'link'        => $item->getLink(),
				'description' => $item->getDescription(),
				'author'      => $item->getAuthor(),
				'date'        => $item->getDateCreated(),
			);

			if (count($feed_items) >= $this->getOption('max_items', 5)) {
				break;
			}
		}

		$feed_items = $this->processItems($feed_items);

		$tpl = $this->getOption('tpl');
		if (!$tpl) {
			$tpl = 'UserBundle:Portal:feed-' . $this->section;
		}

		$vars = $this->getTplVars();
		$vars = array_merge($vars, array(
			'section'    => $this->section,
			'options'    => $this->options,
			'feed_info'  => $feed_info,
			'feed_items' => $feed_items
		));

		$html = $this->renderView($tpl, $vars);

		return $html;
	}

	public function getTplVars()
	{
		return array();
	}

	protected function processItems(array $feed_items)
	{
		return $feed_items;
	}
}
