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
 * @subpackage
 */

namespace Application\InstallBundle\Upgrade\Build;

class Build1356604502 extends AbstractBuild
{
	public function run()
	{
		$this->out("Improve label count speed");
		$this->execMutateSql("ALTER TABLE label_defs ADD total INT NOT NULL");

		$types = array(
			'article'             => 'labels_articles',
			'chat_conversation'   => 'labels_chat_conversations',
			'download'            => 'labels_downloads',
			'feedback'            => 'labels_feedback',
			'news'                => 'labels_news',
			'organization'        => 'labels_organizations',
			'person'              => 'labels_people',
			'task'                => 'labels_tasks',
			'ticket'              => 'labels_tickets',
		);

		$db = $this->container->getDb();
		foreach ($types AS $type => $table) {
			$totals = $db->fetchAllKeyValue("
				SELECT label, COUNT(*)
				FROM $table
				GROUP BY label
			");
			$db->beginTransaction();
			foreach ($totals AS $label => $total) {
				$db->executeUpdate("
					INSERT INTO label_defs (label_type, label, total)
					VALUES (?, ?, ?)
					ON DUPLICATE KEY UPDATE total = total + VALUES(total)
				", array($type . 's', $label, $total));
			}
			$db->commit();
		}

		$this->execMutateSql("CREATE INDEX type_total_idx ON label_defs (label_type, total)");
	}
}