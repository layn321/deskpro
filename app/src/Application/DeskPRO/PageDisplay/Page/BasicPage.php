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

namespace Application\DeskPRO\PageDisplay\Page;

use Application\DeskPRO\Entity\PageDisplayAbstract;

/**
 * A Page wraps up handling of sections and their items.
 *
 * Descriptions for these pages are stored in `PageDisplay` tables. For example, `TicketPageDisplay`.
 *
 * = Terminology =
 *
 * A Page (this class) is a group of PageDisplay's. This just collects all PageDisplay's together into a
 * collection object.
 *
 * A PageDisplay is a part of a page. The part is denoted by it's `section`. For example, a PortalPage
 * has sections for header, footer, content and sidebar.
 *
 * Then a PageDisplay has a `data` field which is a PHP array composed of _items_ which describe the
 * actual things in a section (for example, a widget). The `data` field is free-form and can be proprietary
 * between different Page's, but generally the data structure below is observed.
 *
 * Usually `data` items map to Item classes to do certain work. But again, that is an implementation detail
 * up to the Page. The PortalPage for example maps items to classes that actual generate the HTML used on
 * the page.
 * 
 * = Data Structure =
 *
 * The data in a PageDisplay structure is as follows:
 *
 * array(
 *     // any page-specific information
 *     'items' => array(
 *         array(
 *             'type' => 'xxx',
 *              // any type-specific information
 *         )
 *     )
 * );
 */
class BasicPage
{
	const SECTION_DEFAULT = 'default';

	/**
	 * array[section] = page_display
	 * @var \Application\DeskPRO\Entity\PageDisplayAbstract[]
	 */
	protected $page_displays = array();


	/**
	 * @param \Application\DeskPRO\Entity\PageDisplayAbstract $page_display
	 * @return void
	 */
	public function addPageDisplay(PageDisplayAbstract $page_display)
	{
		$section = $page_display['section'];
		$this->page_displays[$section] = $page_display;
	}


	/**
	 * @param \Application\DeskPRO\Entity\PageDisplayAbstract[] $page_displays
	 * @return void
	 */
	public function addPageDisplays(array $page_displays)
	{
		foreach ($page_displays as $page_display) {
			$this->addPageDisplay($page_display);
		}
	}


	/**
	 * Check to see if a section has been set
	 *
	 * @param string $section
	 * @return bool
	 */
	public function hasPageDisplay($section)
	{
		return isset($this->page_displays[$section]);
	}

	
	/**
	 * Returns a section
	 *
	 * @param  $section
	 * @return \Application\DeskPRO\Entity\PageDisplayAbstract[]|null
	 */
	public function getPageDisplay($section)
	{
		if (!isset($this->page_displays[$section])) {
			return null;
		}

		return $this->page_displays[$section];
	}

	
	/**
	 * Get all page displays
	 * 
	 * @return \Application\DeskPRO\Entity\PageDisplayAbstract[]
	 */
	public function getPageDisplays()
	{
		return $this->page_displays;
	}
}
