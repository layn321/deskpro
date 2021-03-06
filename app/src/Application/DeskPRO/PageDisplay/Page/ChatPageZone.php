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

use Application\DeskPRO\Entity\ChatPageDisplay;
use Application\DeskPRO\People\PersonContextInterface;

use Application\DeskPRO\Entity\PageDisplayAbstract;
use Application\DeskPRO\Entity\TicketPageDisplay;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Department;

class ChatPageZone extends BasicPage implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * The department context for all these pagedisplays
	 * @var \Application\DeskPRO\Entity\Department
	 */
	protected $department;

	/**
	 * The zone we're in
	 */
	protected $zone;

	/**
	 * @var string
	 */
	protected $interface;

	/**
	 * @param string $zone The zone (one of TicketPageDisplay::ZONE_*)
	 * @param Department $department The department context
	 */
	public function __construct($zone, Department $department = null)
	{
		$this->zone = $zone;
		$this->department = $department;

		$this->interface = defined('DP_INTERFACE') ? DP_INTERFACE : '';
	}


	/**
	 * @param $interace
	 */
	public function setInterface($interace)
	{
		$this->interface = $interace;
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
	 * @param \Application\DeskPRO\Entity\PageDisplayAbstract $page_display
	 * @return void
	 */
	public function addPageDisplay(PageDisplayAbstract $page_display)
	{
		if (!($page_display instanceof ChatPageDisplay)) {
			throw new \InvalidArgumentException('You can only add `ChatPageDisplay` types');
		}

		if ($this->zone != $page_display['zone']) {
			throw new \InvalidArgumentException('Invalid zone context. Must be: ' . $this->zone);
		}

		// Filter out agent_only items
		if ($this->interface == 'user') {
			$data = array();
			foreach ($page_display->data as $k => $d) {
				if (!isset($d['agent_only']) || !$d['agent_only']) {
					$data[$k] = $d;
				}
			}

			$page_display->data = $data;
		}

		parent::addPageDisplay($page_display);
	}


	/**
	 * Reads page displays from the TicketPageDisplay entity repository
	 * @return void
	 */
	public function addPageDisplaysFromDb()
	{
		$page_displays = App::getEntityRepository('DeskPRO:ChatPageDisplay')->getFromZone($this->zone, $this->department);
		$this->addPageDisplays($page_displays);
	}


	/**
	 * Get the zone
	 *
	 * @return string
	 */
	public function getZone()
	{
		return $this->zone;
	}


	/**
	 * Get the department
	 *
	 * @return \Application\DeskPRO\Entity\Department
	 */
	public function getDepartment()
	{
		return $this->department;
	}


	public function compileJs()
	{
		$part = array();

		$function_tokens = array();

		foreach ($this->page_displays as $ticket_page) {
			/** @var $ticket_page \Application\DeskPRO\Entity\ChatPageDisplay */
			$page_part = $this->compileChatPage($ticket_page, $function_tokens);

			if ($page_part) {
				$part = array_merge($part,$page_part);
			}
		}

		$parts = json_encode($part);

		foreach ($function_tokens as $token => $function) {
			$parts = str_replace("\"$token\"", $function, $parts);
		}

		return $parts;
	}

	public function compileChatPage($ticket_page, array &$function_tokens)
	{
		if (!$ticket_page['data']) {
			return false;
		}

		$parts = array();

		foreach ($ticket_page['data'] as $item) {
			if ($item['id'] == 'group') {
				if (empty($item['items'])) {
					continue;
				}

				$sub_item_parts = array();
				foreach ($item['items'] as $sub_item) {
					$sub_item_parts[] = $this->_compileArrayForItem($ticket_page, $sub_item, $function_tokens);
				}

				$parts[] = array('section' => $ticket_page['section'], 'id' => 'group', 'title' => $item['title'], 'items' => $sub_item_parts);
			} else {
				$parts[] = $this->_compileArrayForItem($ticket_page, $item, $function_tokens);
			}
		}

		if (!$parts) {
			return false;
		}

		return $parts;
	}

	protected function _compileArrayForItem($ticket_page, $item, array &$function_tokens)
	{
		$part = array();
		$part['section'] = $ticket_page['section'];
		$part['id'] = $item['id'];

		if (isset($item['field_type'])) {
			$part['field_type'] = $item['field_type'];
		}
		if (isset($item['field_id'])) {
			$part['field_id'] = $item['field_id'];
		}

		$part['check'] = false;

		return $part;
	}
}
