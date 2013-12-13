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

class Build1355915880 extends AbstractBuild
{
	public function run()
	{
		$this->out("Copy default transports to gateways without defined transports");

		$missing_gateway_ids = $this->container->getDb()->fetchAllCol("
			SELECT id
			FROM email_gateways
			WHERE linked_transport_id IS NULL
		");

		if (!$missing_gateway_ids) {
			return;
		}

		$first_id = $this->container->getDb()->fetchColumn("
			SELECT linked_transport_id
			FROM email_gateways
			WHERE linked_transport_id IS NOT NULL
			ORDER BY id ASC
			LIMIT 1
		");

		if (!$first_id) {
			$first_id = $this->container->getDb()->fetchColumn("
				SELECT id
				FROM email_transports
				WHERE match_type = 'all'
				ORDER BY id ASC
				LIMIT 1
			");
		}

		if (!$first_id) {
			return;
		}

		$trans_info = $this->container->getDb()->fetchAssoc("
			SELECT *
			FROM email_transports
			WHERE id = ?
		", array($first_id));
		unset($trans_info['id']);

		foreach ($missing_gateway_ids as $gid) {

			if ($trans_info['match_type'] == 'all') {
				$trans_info['match_type'] = 'exact';
				$trans_info['match_pattern'] = $this->container->getDb()->fetchColumn("SELECT match_pattern FROM email_gateway_addresses WHERE email_gateway_id = ?", array($gid));
			}

			$this->container->getDb()->insert('email_transports', $trans_info);
			$new_id = $this->container->getDb()->lastInsertId();

			$this->container->getDb()->update('email_gateways', array(
				'linked_transport_id' => $new_id
			), array('id' => $gid));
		}
	}
}