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

use Orb\Util\Numbers;

class Widget extends AbstractEntityRepository
{
	/**
	 * Gets all widgets, grouped by the page they belong to
	 *
	 * @return array
	 */
	public function getPageGroupedWidgets()
	{
		$results = $this->getEntityManager()->createQuery('
			SELECT w
			FROM DeskPRO:Widget w
			ORDER BY w.page, w.description
		')->execute();

		$output = array();
		foreach ($results AS $widget) {
			$output[$widget->page][] = $widget;
		}

		return $output;
	}

	/**
	 * Gets all widgets that are enabled for a particular page, grouped by location and insert position
	 *
	 * @param string $page
	 *
	 * @return array
	 */
	public function getEnabledPageWidgetsGrouped($page)
	{
		$results = $this->getEntityManager()->createQuery('
			SELECT w
			FROM DeskPRO:Widget w
			LEFT JOIN w.plugin p
			WHERE w.page = :page AND w.enabled = 1
				AND (p.enabled = 1 OR p.enabled IS NULL)
		')->execute(array('page' => $page));

		$output = array();
		foreach ($results AS $widget) {
			$output[$widget->page_location][$widget->insert_position][] = $widget;
		}

		return $output;
	}

	/**
	 * Gets a list of all pages that can have widgets.
	 *
	 * @return array
	 */
	public function getPages()
	{
		return array(
			'ticket' => 'Ticket View',
			'profile' => 'Profile View',
			'organization' => 'Organization View',
			'chat' => 'Chat View',
		);
	}

	/**
	 * Gets a list of all widget locations on each page.
	 *
	 * @return array
	 */
	public function getPageLocations()
	{
		return array(
			'ticket' => array(
				'header' => array('Page Header', 'below'),
				'properties' => array('Properties', ''),
				'messages' => array('Messages and Notes', ''),
				'reply' => array('Reply Box', 'above,below'),
				'footer' => array('Page Footer', 'above'),
			),
			'profile' => array(
				'header' => array('Page Header', 'below'),
				'summary' => array('Summary', ''),
				'properties' => array('Properties', ''),
				'interactions' => array('Interactions', ''),
				'info' => array('Information', ''),
				'agent' => array('Agent', ''),
				'contact' => array('Contact Information', ''),
				'organization' => array('Organization', ''),
				'usergroups' => array('Usergroups', ''),
				'footer' => array('Page Footer', 'above')
			),
			'organization' => array(
				'header' => array('Page Header', 'below'),
				'summary' => array('Summary', ''),
				'members' => array('Members', ''),
				'notes' => array('Notes and Activity Stream', ''),
				'contact' => array('Contact Information', ''),
				'properties' => array('Properties', ''),
				'email_assoc' => array('Email Association', ''),
				'usergroups' => array('Usergroups', ''),
				'footer' => array('Page Footer', 'above')
			),
			'chat' => array(
				'header' => array('Page Header', 'below'),
				'people' => array('People', ''),
				'assignments' => array('Assignments', ''),
				'chat' => array('Chat Box', 'above')
			)
		);
	}

	/**
	 * @return boolean
	 */
	public function canEditWidgetPlugin()
	{
		return (bool)App::getConfig('debug.dev');
	}
}
