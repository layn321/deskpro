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
 * @subpackage Tickets
 */

namespace Application\DeskPRO\Tickets\TicketChangeInspector;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Tickets\TicketActions\ActionsCollection;

use Orb\Util\Arrays;

/**
 * This is fired from the change tracker to update the search tables after a ticket is updated:
 *
 * - tickets_search_active
 * - tickets_search_message
 * - tickets_search_message_active
 * - tickets_search_subject
 */
class SearchUpdater
{
	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket;

	/**
	 * @var array
	 */
	protected $row_data = null;

	/**
	 * @var string
	 */
	protected $search_text = null;

	public function __construct($ticket)
	{
		$this->ticket = $ticket;
	}


	/**
	 * Return the raw ticket row directly from the database
	 *
	 * @return array
	 */
	public function getRowData()
	{
		if ($this->row_data !== null) {
			return $this->row_data;
		}

		$this->row_data = App::getDb()->fetchAssoc("SELECT * FROM tickets WHERE id = ?", array($this->ticket->id));
		return $this->row_data;
	}


	/**
	 * Run the updates
	 */
	public function run()
	{
		if (App::getSetting('core_tickets.use_archive')) {
			// A flag used in error handling
			// See Application\DeskPRO\DBAL\Connection::rollback
			$GLOBALS['DP_HAS_UPDATED_SEARCH_TABLES'] = true;
		}

		if ($this->ticket->_isRemoved) {
			App::getDb()->delete('tickets_search_active', array('id' => $this->ticket->_isRemoved));
			App::getDb()->delete('tickets_search_message_active', array('id' => $this->ticket->_isRemoved));
			App::getDb()->delete('tickets_search_message', array('id' => $this->ticket->_isRemoved));
			App::getDb()->delete('tickets_search_subject', array('id' => $this->ticket->_isRemoved));
		} elseif ($this->ticket->id && !$this->ticket->_isRemoved) {

			$clone_data_search = $this->getCloneData(true);
			$clone_data = $this->getCloneData(false);

			if (!$clone_data) {
				App::getDb()->delete('tickets_search_active', array('id' => $this->ticket->id));
				App::getDb()->delete('tickets_search_message_active', array('id' => $this->ticket->id));
				App::getDb()->delete('tickets_search_message', array('id' => $this->ticket->id));
				App::getDb()->delete('tickets_search_subject', array('id' => $this->ticket->id));
			} else {
				App::getDb()->replace('tickets_search_message', $clone_data_search);
				App::getDb()->replace('tickets_search_subject', array(
					'id' => $this->ticket->id,
					'subject' => $this->ticket->subject
				));

				if (!$this->ticket->isArchived()) {
					App::getDb()->replace('tickets_search_active', $clone_data);
					App::getDb()->replace('tickets_search_message_active', $clone_data_search);
				} else {
					App::getDb()->delete('tickets_search_active', array('id' => $this->ticket->id));
					App::getDb()->delete('tickets_search_message_active', array('id' => $this->ticket->id));
				}

				App::getSystemService('search_indexer')->update($this->ticket, 'update');
			}
		}
	}


	/**
	 * @return array
	 */
	public function getCloneData($with_search_content = false)
	{
		$set_data = array();
		$row_data = $this->getRowData();
		foreach ($this->getCloneFields() as $k) {
			$set_data[$k] = $row_data[$k];
		}

		if (!isset($set_data['id']) || empty($set_data['id'])) {
			return null;
		}

		if ($with_search_content) {
			$set_data['content'] = $this->getSearchContent();
		}

		return $set_data;
	}


	/**
	 * @return string
	 */
	public function getSearchContent()
	{
		if ($this->search_text !== null) {
			return $this->search_text;
		}

		$this->search_text = App::getDb()->fetchAllCol("
			SELECT message
			FROM tickets_messages
			WHERE ticket_id = ?
		", array($this->ticket->id));

		$this->search_text = implode(' ', $this->search_text);

		return $this->search_text;
	}


	/**
	 * @return array
	 */
	public function getCloneFields()
	{
		return array(
			'id', 'language_id', 'department_id', 'category_id', 'priority_id', 'workflow_id', 'product_id', 'person_id', 'agent_id',
			'agent_team_id', 'organization_id', 'email_gateway_id', 'creation_system', 'status', 'urgency', 'is_hold', 'date_created', 'date_resolved', 'date_first_agent_reply',
			'date_last_agent_reply', 'date_last_user_reply', 'date_agent_waiting', 'date_user_waiting', 'total_user_waiting', 'total_to_first_reply',
		);
	}
}