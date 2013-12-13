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

use Application\DeskPRO\Import\Importer\Step\AbstractStep;

abstract class AbstractZendeskStep extends AbstractStep
{
	public $on_run   = true;
	public $on_rerun = true;
	public $on_fast  = true;

	/**
	 * @var \Application\DeskPRO\Import\Importer\ZendeskImporter
	 */
	public $importer;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	public $db;

	/**
	 * @var \Application\DeskPRO\Import\Importer\ZendeskApi
	 */
	public $zd;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	public $em;

	/**
	 * @var array
	 */
	public $checked_ids = array();

	public function init()
	{
		$this->db = $this->importer->getDb();
		$this->zd = $this->importer->getZd();
	}
}
