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

namespace Application\DeskPRO\DBAL;

use Application\DeskPRO\DBAL\Connection;

class DatabaseStats
{
	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $stats;

	public function __construct(Connection $db)
	{
		$this->db = $db;
	}

	protected function _initStats()
	{
		if ($this->stats !== null) {
			return;
		}

		$this->stats = array();

		$this->stats['ticket_count']              = $this->db->fetchColumn("SELECT COUNT(*) FROM tickets");
		$this->stats['ticket_active_count']       = $this->db->fetchColumn("SELECT COUNT(*) FROM tickets WHERE status IN ('awaiting_user', 'awaiting_agent')");
		$this->stats['ticket_message_count']      = $this->db->fetchColumn("SELECT COUNT(*) FROM tickets_messages");
		$this->stats['people_count']              = $this->db->fetchColumn("SELECT COUNT(*) FROM people");
		$this->stats['people_user_count']         = $this->db->fetchColumn("SELECT COUNT(*) FROM people WHERE is_user = 1");
		$this->stats['people_agent_count']        = $this->db->fetchColumn("SELECT COUNT(*) FROM people WHERE is_agent = 1");
		$this->stats['org_count']                 = $this->db->fetchColumn("SELECT COUNT(*) FROM organizations");
		$this->stats['chat_count']                = $this->db->fetchColumn("SELECT COUNT(*) FROM chat_conversations");
		$this->stats['chat_message_count']        = $this->db->fetchColumn("SELECT COUNT(*) FROM chat_messages");
		$this->stats['content_article_count']     = $this->db->fetchColumn("SELECT COUNT(*) FROM articles");
		$this->stats['content_feedback_count']    = $this->db->fetchColumn("SELECT COUNT(*) FROM feedback");
		$this->stats['content_download_count']    = $this->db->fetchColumn("SELECT COUNT(*) FROM downloads");
		$this->stats['content_news_count']        = $this->db->fetchColumn("SELECT COUNT(*) FROM news");
		$this->stats['daily_visitor_count']       = $this->db->fetchColumn("SELECT COUNT(*) FROM visitors WHERE date_last >= ?", array(date('Y-m-d H:i:s', strtotime('-24 hours'))));
		$this->stats['age_last_agent_login'] = $this->db->fetchColumn("
			SELECT UNIX_TIMESTAMP() - UNIX_TIMESTAMP(people.date_last_login)
			FROM people
			WHERE people.is_agent = 1 AND people.date_last_login IS NOT NULL
			ORDER BY people.date_last_login DESC
			LIMIT 1
		");
		$this->stats['age_last_user_message'] = $this->db->fetchColumn("
			SELECT UNIX_TIMESTAMP() - UNIX_TIMESTAMP(tickets_messages.date_created)
			FROM tickets_messages FORCE INDEX (PRIMARY)
			LEFT JOIN people ON (people.id = tickets_messages.person_id)
			WHERE people.is_agent = 0
			ORDER BY tickets_messages.id DESC
			LIMIT 1
		");
		$this->stats['age_last_agent_message'] = $this->db->fetchColumn("
			SELECT UNIX_TIMESTAMP() - UNIX_TIMESTAMP(tickets_messages.date_created)
			FROM tickets_messages FORCE INDEX (PRIMARY)
			LEFT JOIN people ON (people.id = tickets_messages.person_id)
			WHERE people.is_agent = 1
			ORDER BY tickets_messages.id DESC
			LIMIT 1
		");
	}

	public function getStats()
	{
		$this->_initStats();
		return $this->stats;
	}
}