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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

class CreateIndexesStep extends AbstractDeskpro3Step
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	public $steps = array(
		'content_search_fulltext',
		'content_search_indexes',
		'content_search_attribute_indexes',
		'tickets_indexes',
		'tickets_logs_indexes',
		'tickets_messages_indexes',
		'tickets_attachments_indexes',
		'tickets_participant_indexes',
		'custom_data_ticket_indexes',
		'tickets_search_active_indexes',
		'tickets_search_message_fulltext',
		'tickets_search_message_indexes',
		'tickets_search_message_active_fulltext',
		'tickets_search_message_active_indexes',
		'tickets_search_subject_indexes',
	);

	public static function getTitle()
	{
		return 'Create Indexes';
	}

	public function countPages()
	{
		return count($this->steps);
	}

	public function run($page = 1)
	{
		$k = $page-1;
		if (!isset($this->steps[$k])) {
			return;
		}

		$method = $this->steps[$k];
		$this->$method();
	}

	public function content_search_fulltext()
	{
		$this->getDb()->exec("CREATE FULLTEXT INDEX content ON content_search (content)");
	}

	public function content_search_indexes()
	{
		$this->importer->restoreTableIndexes('content_search');
	}

	public function content_search_attribute_indexes()
	{
		$this->importer->restoreTableIndexes('content_search_attribute');
	}

	public function tickets_indexes()
	{
		$this->importer->restoreTableIndexes('tickets');
	}

	public function tickets_logs_indexes()
	{
		$this->importer->restoreTableIndexes('tickets_logs');
	}

	public function tickets_messages_indexes()
	{
		$this->importer->restoreTableIndexes('tickets_messages');
	}

	public function tickets_attachments_indexes()
	{
		$this->importer->restoreTableIndexes('tickets_attachments');
	}

	public function tickets_participant_indexes()
	{
		$this->importer->restoreTableIndexes('tickets_participant');
	}

	public function custom_data_ticket_indexes()
	{
		$this->importer->restoreTableIndexes('custom_data_ticket');
	}

	public function tickets_search_active_indexes()
	{
		$this->importer->restoreTableIndexes('tickets_search_active');
	}

	public function tickets_search_message_fulltext()
	{
		$this->db->exec("CREATE FULLTEXT INDEX content ON tickets_search_message (content)");
	}

	public function tickets_search_message_indexes()
	{
		$this->importer->restoreTableIndexes('tickets_search_message');
	}

	public function tickets_search_message_active_fulltext()
	{
		$this->db->exec("CREATE FULLTEXT INDEX content ON tickets_search_message_active (content)");
	}

	public function tickets_search_message_active_indexes()
	{
		$this->importer->restoreTableIndexes('tickets_search_message_active');
	}

	public function tickets_search_subject_indexes()
	{
		$this->importer->restoreTableIndexes('tickets_search_subject');
	}
}
