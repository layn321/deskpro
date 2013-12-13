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

use Application\DeskPRO\Entity\TicketPriority;

class TicketSnippetsStep extends AbstractDeskpro3Step
{
	public $cat_map = array();

	public static function getTitle()
	{
		return 'Import Ticket Snippets';
	}

	public function run($page = 1)
	{
		// Import categories first
		$this->getDb()->insert('text_snippet_categories', array(
			'person_id' => $this->getDb()->fetchColumn("SELECT id FROM people WHERE can_admin = 1 ORDER BY id ASC LIMIT 1"),
			'is_global' => 1
		));
		$this->cat_map[0] = $this->getDb()->lastInsertId();

		$this->getDb()->insert('object_lang', array(
			'language_id' => 1,
			'ref'         => 'text_snippet_categories.'.$this->cat_map[0],
			'prop_name'   => 'title',
			'value'       => 'General',
			'ref_type'    => 'text_snippet_categories',
			'ref_id'      => $this->cat_map[0]
		));

		$cats = $this->getOldDb()->fetchAll("SELECT * FROM quickreply_cat");
		foreach ($cats as $c) {
			$agent_id = $this->getMappedNewId('tech', $c['techid']);
			if (!$agent_id) continue;
			$this->getDb()->insert('text_snippet_categories', array(
				'person_id' => $agent_id,
				'typename' => 'tickets',
				'is_global' => $c['global']
			));

			$this->cat_map[$c['id']] = $this->getDb()->lastInsertId();

			$this->getDb()->insert('object_lang', array(
				'language_id' => 1,
				'ref'         => 'text_snippet_categories.'.$this->cat_map[$c['id']],
				'prop_name'   => 'title',
				'value'       => $c['name'],
				'ref_type'    => 'text_snippet_categories',
				'ref_id'      => $this->cat_map[$c['id']]
			));
		}
		unset($cats);

		$replace = array(
			'###USER::username###'  => '{{ user.email }}',
			'###USER::email###'     => '{{ user.email }}',
			'###USER::name###'      => '{{ user.name }}',
			'###TICKET::subject###' => '{{ ticket.subject }}',
			'###TICKET::id###'      => '{{ ticket.id }}',
			'###TICKET::status###'  => '{{ ticket.status }}',
		);

		// Import snippets now
		$quick_replies = $this->getOldDb()->fetchAll("SELECT * FROM quickreply");
		foreach ($quick_replies as $qr) {
			$agent_id = $this->getMappedNewId('tech', $qr['techid']);
			if (!$agent_id) continue;

			if (!isset($this->cat_map[$qr['category']])) {
				continue;
			}

			$qr['response'] = str_replace(array_keys($replace), array_values($replace), $qr['response']);

			$this->getDb()->insert('text_snippets', array(
				'person_id' => $agent_id,
				'category_id' => $this->cat_map[$qr['category']],
			));

			$snippet_id = $this->getDb()->lastInsertId();

			$this->getDb()->insert('object_lang', array(
				'language_id' => 1,
				'ref'         => 'text_snippets.'.$snippet_id,
				'prop_name'   => 'title',
				'value'       => $qr['name'],
				'ref_type'    => 'text_snippets',
				'ref_id'      => $snippet_id
			));
			$this->getDb()->insert('object_lang', array(
				'language_id' => 1,
				'ref'         => 'text_snippets.'.$snippet_id,
				'prop_name'   => 'snippet',
				'value'       => nl2br(htmlspecialchars($qr['response'])),
				'ref_type'    => 'text_snippets',
				'ref_id'      => $snippet_id
			));
		}
	}
}
