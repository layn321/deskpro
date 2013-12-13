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

class ChatSnippetsStep extends AbstractDeskpro3Step
{
	public $cat_map = array();

	public static function getTitle()
	{
		return 'Import Ticket Snippets';
	}

	public function run($page = 1)
	{
		// Import categories first
		$cats = $this->getOldDb()->fetchAll("
			SELECT DISTINCT category, techid
			FROM chat_canned GROUP BY techid
		");

		$quick_replies = $this->getOldDb()->fetchAll("SELECT * FROM chat_canned");

		$this->getDb()->beginTransaction();

		try {
			foreach ($cats as $c) {
				if ($c['techid']) {
					$is_global = 0;
					$agent_id = $this->getMappedNewId('tech', $c['techid']);
					if (!$agent_id) continue;
				} else {
					$is_global = 1;
					$agent_id = $this->getMappedNewId('first_admin', 0);
				}

				$this->getDb()->insert('text_snippet_categories', array(
					'person_id' => $agent_id,
					'is_global' => $is_global,
					'typename' => 'chat'
				));

				if (!isset($this->cat_map[$agent_id])) {
					$this->cat_map[$agent_id] = array();
				}
				$this->cat_map[$agent_id][$c['category']] = $this->getDb()->lastInsertId();

				$this->getDb()->insert('object_lang', array(
					'language_id' => 1,
					'ref'         => 'text_snippet_categories.'.$this->cat_map[$agent_id][$c['category']],
					'prop_name'   => 'title',
					'value'       => $c['category'],
					'ref_type'    => 'text_snippet_categories',
					'ref_id'      => $this->cat_map[$agent_id][$c['category']]
				));
			}
			unset($cats);

			// Import snippets now

			foreach ($quick_replies as $qr) {
				if ($c['techid']) {
					$agent_id = $this->getMappedNewId('tech', $c['techid']);
					if (!$agent_id) continue;
				} else {
					$agent_id = $this->getMappedNewId('first_admin', 0);
				}

				if (!isset($this->cat_map[$agent_id][$qr['category']])) {
					continue;
				}

				$this->getDb()->insert('text_snippets', array(
					'person_id' => $agent_id,
					'category_id' => $this->cat_map[$agent_id][$qr['category']]
				));

				$snippet_id = $this->db->lastInsertId();

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
					'value'       => nl2br(htmlspecialchars($qr['content'])),
					'ref_type'    => 'text_snippets',
					'ref_id'      => $snippet_id
				));
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}
}
