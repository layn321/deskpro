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

namespace Application\DeskPRO\Log;

use Application\DeskPRO\DBAL\Connection;
use Application\DeskPRO\HttpFoundation\Session;

use Application\DeskPRO\Entity\PageViewLog;
use Application\DeskPRO\Entity\Article;
use Application\DeskPRO\Entity\Download;
use Application\DeskPRO\Entity\News;
use Application\DeskPRO\Entity\Feedback;

class ViewLog
{
	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Application\DeskPRO\HttpFoundation\Session
	 */
	protected $session;

	public function __construct(Connection $db, Session $session = null)
	{
		$this->db = $db;
		$this->session = $session;
	}


	/**
	 * Log a view on an object
	 *
	 * @param mixed $object
	 * @return int
	 * @throws \InvalidArgumentException
	 */
	public function view($object, $action = 1)
	{
		$type = null;
		if ($object instanceof Article) {
			$type = PageViewLog::TYPE_ARTICLE;
		} elseif ($object instanceof Download) {
			$type = PageViewLog::TYPE_DOWNLOAD;
		} elseif ($object instanceof News) {
			$type = PageViewLog::TYPE_NEWS;
		} elseif ($object instanceof Feedback) {
			$type = PageViewLog::TYPE_FEEDBACK;
		}

		if (!$type) {
			throw new \InvalidArgumentException("Invalid object type. Got `" . get_class($object) . "`");
		}

		$person_id = null;
		if ($this->session && $this->session->getEntity()->person && $this->session->getEntity()->person->getId()) {
			$person_id = $this->session->getEntity()->person->getId();
		}

		$this->db->insert('page_view_log', array(
			'object_type'   => $type,
			'object_id'     => $object->getId(),
			'view_action'   => $action,
			'person_id'     => $person_id,
			'date_created'  => date('Y-m-d H:i:s')
		));

		return $this->db->lastInsertId();
	}
}