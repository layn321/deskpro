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

use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;

class TechNewsStep extends AbstractDeskpro3Step
{
	/**
	 * @var array
	 */
	public $agents;

	/**
	 * @var array
	 */
	public $tech_news;

	public static function getTitle()
	{
		return 'Import Tech News';
	}

	public function run($page = 1)
	{
		$this->tech_news = $this->getOldDb()->fetchAll("
			SELECT * FROM tech_news
			ORDER BY id ASC
		");

		if (!$this->tech_news) {
			return;
		}

		$this->agents = $this->getEm()->createQuery("
			SELECT p
			FROM DeskPRO:Person p INDEX BY p.id
			WHERE p.is_agent = 1
			ORDER BY p.id ASC
		");

		$this->getDb()->beginTransaction();

		try {
			foreach ($this->agents as $agent) {
				$this->importTechNews($agent);
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	public function importTechNews($agent)
	{
		$convo = new ChatConversation();
		$convo->is_agent = true;
		$convo->addParticipant($agent);
		$this->getEm()->persist($convo);
		$this->getEm()->flush();

		$have_ids = $agent->id;

		foreach ($this->tech_news as $news) {
			$agent_poster_id = $this->getMappedNewId('tech', $news['techid']);
			if (!$agent_poster_id) {
				continue;
			}

			$agent_poster = $this->agents[$agent_poster_id];

			if (!in_array($agent_poster->id, $have_ids)) {
				$have_ids[] = $agent_poster->id;
				$convo->addParticipant($agent_poster);
			}

			$chat_message = $convo->addNewMessage(
				strip_tags($news['message']),
				$agent_poster
			);
			$chat_message->date_created = new \DateTime('@' . $news['timestamp']);

			$this->getEm()->persist($chat_message);
			$this->getEm()->flush();
		}

		$this->getEm()->persist($convo);
		$this->getEm()->flush();
	}
}
