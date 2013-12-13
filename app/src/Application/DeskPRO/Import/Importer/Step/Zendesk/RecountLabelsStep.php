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

namespace Application\DeskPRO\Import\Importer\Step\Zendesk;

use Application\DeskPRO\Entity\Person;

class RecountLabelsStep extends AbstractZendeskStep
{
	public static function getTitle()
	{
		return 'Recount Labels';
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);

		$this->db->exec("TRUNCATE TABLE label_defs");

		$types = array(
			'articles', 'blobs', 'chat_conversations',
			'downloads', 'feedback', 'news', 'organizations',
			'people', 'tasks', 'tickets'
		);

		$insert_defs = array();

		foreach ($types as $t) {
			$counts = $this->db->fetchAllKeyValue("
				SELECT label, COUNT(*)
				FROM `labels_$t`
				GROUP BY label
			");

			foreach ($counts as $l => $c) {
				$insert_defs[] = array(
					'label_type' => $t,
					'label'      => $l,
					'total'      => $c
				);
			}
		}

		if ($insert_defs) {
			$this->db->batchInsert('label_defs', $insert_defs, true);
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}
}
