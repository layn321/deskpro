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

use Application\DeskPRO\App;

use Application\DeskPRO\People\PersonContextInterface;

use Application\DeskPRO\Entity\PageDisplayAbstract;
use Application\DeskPRO\Entity\ChatPageDisplay;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Department;

use Orb\Util\Arrays;

class ChatPageZoneCollection implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * @var string
	 */
	protected $zone;

	/**
	 * department_pages[dep_id] = array(ChatPageZone)
	 * @var \Application\DeskPRO\PageDisplay\Page\ChatPageZone
	 */
	protected $department_pages = array();

	/**
	 * @param string $zone
	 */
	public function __construct($zone)
	{
		$this->zone = $zone;
	}

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return void
	 */
	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}


	/**
	 * Read all ChatPageDisplay records from the database, initialize ChatPageZone's,
	 * and then add them to this collection.
	 */
	public function addPagesFromDb($interface = null)
	{
		if ($this->zone == 'agent') {
			$this->generateAgentZone();
			return;
		}

		$deps = App::getDataService('Department')->getRootNodes();
		foreach ($deps as $d) {
			if (count($d->children)) {
				foreach ($d->children as $dc) {
					$page_data = App::getEntityRepository('DeskPRO:ChatPageDisplay')->getSectionDataResolve($dc, $this->zone);

					$page = new ChatPageDisplay();
					$page->zone = $this->zone;
					$page->department = $dc;
					$page->data = $page_data;

					$chat_page_zone = new ChatPageZone($this->zone, $dc);
					$chat_page_zone->addPageDisplay($page);
					$this->addPage($chat_page_zone);
				}
			} else {
				$page_data = App::getEntityRepository('DeskPRO:ChatPageDisplay')->getSectionDataResolve($d, $this->zone);

				$page = new ChatPageDisplay();
				$page->zone = $this->zone;
				$page->department = $d;
				$page->data = $page_data;

				$chat_page_zone = new ChatPageZone($this->zone, $d);
				$chat_page_zone->addPageDisplay($page);
				$this->addPage($chat_page_zone);
			}
		}

		$page_data = App::getEntityRepository('DeskPRO:ChatPageDisplay')->getSectionDataResolve(null, $this->zone);
		$page = new ChatPageDisplay();
		$page->zone = $this->zone;
		$page->department = null;
		$page->data = $page_data;

		$chat_page_zone = new ChatPageZone($this->zone, null);
		if ($interface) {
			$chat_page_zone->setInterface($interface);
		}
		$chat_page_zone->addPageDisplay($page);
		$this->addPage($chat_page_zone);
	}


	/**
	 * The agent zone isnt configurable yet. Lets generate it on the fly using data from the database.
	 */
	public function generateAgentZone()
	{
		$page_display = new ChatPageDisplay();
		$page_display->zone = 'agent';

		$options = array();

		$chat_field_defs = App::getApi('custom_fields.chats')->getEnabledFields();
		foreach ($chat_field_defs as $def) {
			$options[] = array('id' => "chat_field[{$def->id}]");
		}

		$page_display->setData($options);

		$chat_page_zone = new ChatPageZone($this->zone, null);
		$chat_page_zone->setInterface('agent');
		$chat_page_zone->addPageDisplays(array($page_display));
		$this->addPage($chat_page_zone);
	}


	public function generateUserZone()
	{
		$page_display = new ChatPageDisplay();
		$page_display->zone = 'create';

		$options[] = array('id' => 'person_name');
		$options[] = array('id' => 'person_email');
		$options[] = array('id' => 'chat_department');

		$chat_field_defs = App::getApi('custom_fields.chats')->getEnabledFields();
		foreach ($chat_field_defs as $def) {
			$options[] = array('id' => "chat_field[{$def->id}]");
		}

		$page_display->setData($options);

		$chat_page_zone = new ChatPageZone($this->zone, null);
		$chat_page_zone->addPageDisplays(array($page_display));
		$this->addPage($chat_page_zone);
	}


	public function getDefaultPage()
	{
		if (isset($this->department_pages[0])) {
			return $this->department_pages[0];
		}

		return null;
	}


	public function getDepartmentPage($dep_id)
	{
		if (isset($this->department_pages[$dep_id])) {
			return $this->department_pages[$dep_id];
		}

		return $this->getDefaultPage();
	}


	/**
	 * @param \Application\DeskPRO\PageDisplay\Page\ChatPageZone $page
	 * @return void
	 */
	public function addPage(ChatPageZone $page)
	{
		if ($page->getZone() != $this->zone) {
			throw new \InvalidArgumentException('Invalid zone context. Must be: ' . $this->zone);
		}

		$dep_id = $page->getDepartment() ? $page->getDepartment()->getId() : 0;
		$this->department_pages[$dep_id] = $page;
	}


	/**
	 * Add an array of pages at once
	 *
	 * @param \Application\DeskPRO\PageDisplay\Page\ChatPageZone[] $pages
	 */
	public function addPages(array $pages)
	{
		foreach ($pages as $page) {
			$this->addPage($page);
		}
	}


	/**
	 * Check if we havea  zone set for a department
	 *
	 * @param int|Department $department
	 * @return bool
	 */
	public function hasPage($department)
	{
		if (is_object($department)) $department = $department['id'];

		return isset($this->department_pages[$department]);
	}


	/**
	 * Get the page for a department
	 *
	 * @param int|Department $department
	 * @return array|null
	 */
	public function getPage($department)
	{
		if (is_object($department)) $department = $department['id'];

		if (!isset($this->department_pages[$department])) {
			return null;
		}

		return $this->department_pages[$department];
	}



	/**
	 * @return ChatPageZone|array
	 */
	public function getPagesData()
	{
		$data = array();

		foreach ($this->department_pages as $dep_id => $page) {
			$data[$dep_id] = $page->getPageDisplay('default')->data;
		}
		return $data;
	}


	public function compileJs()
	{
		$part = array();

		$page_display = new ChatPageDisplay();
		$page_display->zone = $this->zone;

		$options = array();

		$chat_field_defs = App::getApi('custom_fields.chats')->getEnabledFields();
		foreach ($chat_field_defs as $def) {
			$options[] = array('id' => "chats_field[{$def->id}]");
		}

		$page_display->setData($options);

		$chat_page_zone = new ChatPageZone($this->zone, null);
		$chat_page_zone->addPageDisplays(array($page_display));

		$part[] = "\"all\": " . $chat_page_zone->compileJs();

		foreach ($this->department_pages as $dep_id => $page_zone) {
			$part[] = "$dep_id: " . $page_zone->compileJs();
		}

		$part = "{\n" . implode(",\n", $part) . "\n}";
		return $part;
	}
}
