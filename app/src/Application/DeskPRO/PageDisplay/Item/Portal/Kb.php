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

/**
 * Renders an AJAXy KB browser in the content section, or renders a simple
 * listing in the sidebar.
 *
 * For the content section, this makes use of partial from `UserBundle:Articles:quickBrowser`
 *
 * @option int  category_id         The initial category in the KB browser, or the category to fetch
 *                                  from in the sidebar (0 for all).
 * @option bool show_cat_switcher   When using content section, show cat switcher?
 */
class Kb extends PortalItemAbstract implements CacheableItem
{
	public function getCacheOptions()
	{
		return array('tags' => array('articles'));
	}

	public function init()
	{
		if (!$this->hasOption('show_cat_switcher')) {
			$this->setOption('show_cat_switcher', true);
		}
	}

	public function checkPermission()
	{
		return $this->person_context->hasPerm('articles.use');
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
			'UserBundle:Articles:browse',
			array(),
			array('_partial' => 'portal')
		);

		return $html;
	}

	public function getSidebarHtml()
	{
		$category = null;
		if ($this->getOption('category_id')) {
			$category = App::findEntity('DeskPRO:ArticleCategory', $this->getOption('category_id'));
		}

		$articles = App::getEntityRepository('DeskPRO:Article')->getNewest(
			$this->getValueOption('num_articles', 10),
			$category
		);

		$html = $this->renderView('UserBundle:Portal:kb-sidebar.html.twig', array(
			'articles' => $articles,
			'block_title' => $this->getOption('block_title'),
		));

		return $html;
	}

	public function getJsAssets()
	{
		if ($this->section == 'portal') {
			return array('javascripts/DeskPRO/User/ElementHandler/PortalKb.js');
		}

		return array();
	}
}
