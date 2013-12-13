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

use Orb\Util\Arrays;

use Application\DeskPRO\App;
use \Doctrine\ORM\EntityRepository;

class EmailGatewayAddress extends AbstractEntityRepository
{
	public function getOptions($for_ids = null)
	{
		if ($for_ids) {
			$for_ids = (array)$for_ids;
			$for_ids = Arrays::castToType($for_ids, 'int');
			$for_ids = implode(',', $for_ids);

			if (!$for_ids) {
				return array();
			}

			$opts = $this->getEntityManager()->getConnection()->fetchAllKeyValue("
				SELECT id, match_pattern
				FROM email_gateway_addresses
				WHERE id IN ($for_ids)
			");
		} else {
			$opts = $this->getEntityManager()->getConnection()->fetchAllKeyValue("
				SELECT id, match_pattern
				FROM email_gateway_addresses
			");
		}

		return $opts;
	}


	/**
	 * Gets exact email addresses. That is, matches of type 'exact'
	 *
	 * @return array
	 */
	public function getEmailAddresses()
	{
		return $this->_em->createQuery("
			SELECT a
			FROM DeskPRO:EmailGatewayAddress a
			WHERE a.match_type = 'exact'
			ORDER BY a.match_pattern ASC
		")->execute();
	}

	/**
	 * @return string
	 */
	public function getDefaultTicketAddress()
	{
		return App::getDb()->fetchColumn("
			SELECT email_gateway_addresses.match_pattern
			FROM email_gateway_addresses
			LEFT JOIN email_gateways ON email_gateways.id = email_gateway_addresses.email_gateway_id
			WHERE email_gateway_addresses.match_type = 'exact' AND email_gateways.gateway_type = 'tickets'
			ORDER BY email_gateway_addresses.run_order ASC, email_gateway_addresses.id ASC
			LIMIT 1
		");
	}
}
