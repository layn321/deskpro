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
 * Similar to Content except this just takes a template name and renders it.
 *
 * @option string  tpl   The template name
 */
class OmniSearch extends PortalItemAbstract
{
	public function getHtml()
	{
		$query = '';
		if ($this->container->has('request')) {
			$request = $this->container->get('request');
			if ($request->attributes->get('_controller') == 'Application\UserBundle\Controller\SearchController::searchAction') {
				$query = $request->query->get('query', '');
			}
		}

		$html = $this->renderView('UserBundle:Portal:omnisearch-topsection.html.twig', array(
			'section' => $this->section,
			'options' => $this->options,
			'query'   => $query
		));

		return $html;
	}

	public function getJsAssets()
	{
		return array(
			'javascripts/DeskPRO/User/ElementHandler/OmniSearch.js'
		);
	}
}
