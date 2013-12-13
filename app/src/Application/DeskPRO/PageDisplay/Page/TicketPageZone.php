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
use Application\DeskPRO\Entity\TicketPageDisplay;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Department;

/**
 * Ticket layouts have three components:
 *
 * ZONE: This is the top-level grouping of ticket page descriptions. Zones include:
 * create, view, modify and agent.
 *
 * SECTION: This is a specific part of the page in a zone. So you might have
 * top tabs, bottom tabs, body, etc. Usually this doesnt matter and is 'default'
 *
 * PageDisplay's are items to display on the page.
 */
class TicketPageZone extends BasicPage implements PersonContextInterface
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
		if (!($page_display instanceof TicketPageDisplay)) {
			throw new \InvalidArgumentException('You can only add `TicketPageDisplay` types');
		}

		if ($this->zone != $page_display['zone']) {
			throw new \InvalidArgumentException('Invalid zone context. Must be: ' . $this->zone);
		}

		// Filter out agent_only items
		if ($this->interface == 'user') {
			$data = array();
			foreach ($page_display->data as $k => $d) {
				if (!isset($d['agent_only']) || !$d['agent_only']) {
					if (!empty($d['field_type']) && $d['field_type'] == 'ticket_field') {
						$field = App::getSystemService('ticket_fields_manager')->getFieldFromId($d['field_id']);
						if (!$field) {
							continue;
						}
						if ($field->is_agent_field && (!isset($d['not_agent_only']) || !$d['not_agent_only'])) {
							continue;
						}
					}

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
		$page_displays = App::getEntityRepository('DeskPRO:TicketPageDisplay')->getFromZone($this->zone, $this->department);
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
			/** @var $ticket_page \Application\DeskPRO\Entity\TicketPageDisplay */
			$page_part = $this->compileTicketPage($ticket_page, $function_tokens);

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

	public function compileTicketPage($ticket_page, array &$function_tokens)
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

		$check = array('ticket_categories', 'ticket_workflows', 'ticket_priorities', 'ticket_products');
		foreach ($check as $k) {
			if (isset($item[$k])) {
				$part[$k] = $item[$k];
			}
		}

		if (empty($item['rules'])) {
			$part['check'] = false;
		} else {
			$token = '%' . microtime(true) . mt_rand(1000, 9999) . mt_rand(1000, 9999) . '%';
			$function = $this->_compileFunctionCheck($item);

			$function_tokens[$token] = $function;

			$part['check'] = $token;
		}

		return $part;
	}

	protected function _compileFunctionCheck($item)
	{
		$function = array();
		$function[] = 'function (ticket) {';

		$terms_compiler = new \Application\DeskPRO\Tickets\TicketTerms($item['rules']);
		$function[] = $terms_compiler->compileTermsToJavascript($item['rule_match_type']);

		$function[] = '}';

		return implode(' ', $function);
	}
}
