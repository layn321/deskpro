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

use Application\DeskPRO\Entity\PortalPageDisplay;

use Application\DeskPRO\App;

class News extends PortalItemAbstract
{
	public function checkPermission()
	{
		return $this->person_context->hasPerm('news.use');
	}

	public function getHtml()
	{
		if ($this->section == 'portal') {
			return $this->getContentHtml();
		} else {
			return $this->getSidebarHtml();
		}
	}

	public function getContentHtml()
	{
		$html = $this->renderForward(
			'UserBundle:News:browse',
			array(),
			array('_partial' => 'portal', 'per_page' => $this->getOption('per_page', 2))
		);

		return $html;
	}

	public function getSidebarHtml()
	{
		$category = null;
		if ($this->getOption('category_id')) {
			$category = App::findEntity('DeskPRO:NewsCategory', $this->getOption('category_id'));
		}

		$news_entries = App::getEntityRepository('DeskPRO:News')->getNewest(
			$this->getValueOption('num_articles', 3),
			$category
		);

		$html = $this->renderView('UserBundle:Portal:news-sidebar.html.twig', array(
			'news_entries' => $news_entries,
			'block_title' => $this->getOption('block_title'),
		));

		return $html;
	}

	public function getJsAssets()
	{
		if (0 and $this->section == 'portal') {
			return array('javascripts/DeskPRO/User/ElementHandler/PortalNews.js');
		}

		return array();
	}
}
