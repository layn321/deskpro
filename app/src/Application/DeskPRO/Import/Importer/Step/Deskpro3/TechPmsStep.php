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

class TechPmsStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Tech Private Messages';
	}

	public function run($page = 1)
	{
		$tech_ids = $this->getOldDb()->fetchAllCol("SELECT id FROM tech");

		$this->getDb()->beginTransaction();

		try {
			foreach ($tech_ids as $tech_id) {
				$this->importTechMessages($tech_id);
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	public function importTechMessages($tech_id)
	{
		$agent_id = $this->getMappedNewId('tech', $tech_id);
		if (!$agent_id) {
			return;
		}

		$agent = $this->getEm()->find('DeskPRO:Person', $agent_id);

		$messages = $this->getOldDb()->fetchAll("
			SELECT *
			FROM tech_pms
			WHERE fromid = ?
			ORDER BY id ASC
		", array($tech_id));

		if (!$messages) {
			return;
		}

		$this->logMessage(sprintf("-- Importing %d messages for tech %d (agent %d)", count($messages), $tech_id, $agent_id));

		$start_time = microtime(true);

		foreach ($messages as $message) {
			$other_agent_id = $this->getMappedNewId('tech', $message['toid']);
			if (!$other_agent_id || $other_agent_id == $agent_id) {
				continue;
			}

			$other_agent = $this->getEm()->find('DeskPRO:Person', $other_agent_id);

			$convo = $this->getEm()->getRepository('DeskPRO:ChatConversation')->getRecentForPeople(array($agent_id, $other_agent_id));
			if (!$convo || !$convo->is_agent) {
				$convo = new \Application\DeskPRO\Entity\ChatConversation();
				$convo->is_agent = true;
				$convo->date_created = new \DateTime('@' . $message['timestamp']);
				$convo->addParticipant($agent);
				$convo->addParticipant($other_agent);
				$this->getEm()->persist($convo);
				$this->getEm()->flush();
			}

			$new_msg = \Orb\Util\Strings::convertToUtf8($message['message'], 'ISO-8895-1');
			if ($new_msg) {
				$message['message'] = $new_msg;
			}

			$chat_message = $convo->addNewMessage(
				html_entity_decode(strip_tags($message['message']), \ENT_QUOTES),
				$agent
			);
			$chat_message['date_created'] = new \DateTime('@' . $message['timestamp']);

			$this->getEm()->persist($chat_message);
			$this->getEm()->flush();
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $end_time-$start_time));
	}
}
