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
 * Renders the downloads browser
 */
class Staff extends PortalItemAbstract
{
	public function getCacheOptions()
	{
		return array(
			'lifetime' => 1800, // 30 mins
			'user_indifferent' => true
		);
	}

	public function getHtml()
	{
		if ($this->section == 'sidebar') {
			return $this->getSidebarHtml();
		}

		return '';
	}

	public function getSidebarHtml()
	{
		if ($this->getOption('show_all')) {
			$staff = App::getEntityRepository('DeskPRO:Person')->getAgents();
		} else {
			$staff = App::getEntityRepository('DeskPRO:Person')->getActiveAgents();
			if (App::getCurrentPerson() && App::getCurrentPerson()->is_agent) {
				$staff[App::getCurrentPerson()->getId()] = App::getCurrentPerson();
			}
		}

		$html = $this->renderView('UserBundle:Portal:staff-sidebar.html.twig', array(
			'staff' => $staff,
			'title' => $this->getOption('title'),
		));

		return $html;
	}
}
