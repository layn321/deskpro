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

use Application\DeskPRO\Entity\EmailGateway;
use Application\DeskPRO\Entity\EmailGatewayAddress;

class SaveDataMiscStep extends AbstractDeskpro3Step
{
	public $tables = array(
		'faq_cats_related',
		'faq_subscriptions',
		'user_plans',
		'payment_gateways',
		'billing_rules',
		'billing_credit_bundles',
		'manual_manual_styles',
		'manual_manuals_perms',
		'manual_manuals',
		'ticket_fielddisplay',
		'gateway_spam',
		'calendar_def',
	);

	public static function getTitle()
	{
		return 'Save Data: Misc';
	}

	public function countPages()
	{
		return count($this->tables);
	}

	public function run($page = 1)
	{
		$table = $this->tables[$page-1];

		if (!$this->importer->doesOldTableExist($table)) {
			return;
		}

		$this->getDb()->beginTransaction();

		try {
			$data = $this->getOldDb()->fetchAll("SELECT * FROM $table");
			foreach ($data as $r) {
				$x = uniqid('', true);
				$this->getDb()->insert('import_datastore', array(
					'typename' => "table_{$table}_$x",
					'data' => serialize($r)
				));
			}
			$this->getDb()->commit();

		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}
}
