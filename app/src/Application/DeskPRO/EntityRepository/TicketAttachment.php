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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;

use Orb\Util\Arrays;

class TicketAttachment extends AbstractEntityRepository
{
	/**
	 * Get attachments for a ticket
	 *
	 * @param  $ticket
	 * @return array
	 */
	public function getTicketAttachments($ticket)
	{
		$attachments = $this->getEntityManager()->createQuery("
			SELECT a, p, m
			FROM DeskPRO:TicketAttachment a INDEX BY a.id
			LEFT JOIN a.person p
			LEFT JOIN a.message m
			LEFT JOIN a.blob b
			WHERE a.ticket = ?1
			ORDER BY a.id DESC
		")->setParameter(1, $ticket)->execute();

		return $attachments;
	}

	public function getAttachmentsForMessages($messages)
	{
		if (!$messages) {
			return array();
		}

		$message_ids = array();
		foreach ($messages as $m) {
			$message_ids[] = $m['id'];
		}

		$message_ids = implode(',', $message_ids);

		$attachments = $this->getEntityManager()->createQuery("
			SELECT a, b
			FROM DeskPRO:TicketAttachment a INDEX BY a.id
			LEFT JOIN a.blob b
			WHERE a.message IN ($message_ids)
			ORDER BY a.id DESC
		")->execute();

		return $attachments;
	}
}
