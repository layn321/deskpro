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

namespace Application\DeskPRO\Import\Importer\Step;

use Application\DeskPRO\Import\Importer\AbstractImporter;

abstract class AbstractStep
{
	/**
	 * @var Application\DeskPRO\Import\Importer\AbstractImporter
	 */
	public $importer;

	/**
	 * @param \Application\DeskPRO\Import\Importer\AbstractImporter $impoter
	 */
	public function __construct(AbstractImporter $impoter)
	{
		$this->importer = $impoter;
		$this->init();
	}

	protected function init() {}


	/**
	 * Get a short title for the step
	 * @return string
	 */
	public static function getTitle()
	{
		return get_called_class();
	}


	/**
	 * Actually do the import work
	 */
	abstract public function run($page = 1);


	/**
	 * @return int
	 */
	public function countPages()
	{
		return 1;
	}


	/**
	 * Get the ID for this step
	 *
	 * @return string
	 */
	public function getId()
	{
		$basename = \Orb\Util\Util::getBaseClassname($this);
		$name = strtolower(str_replace('Step', '', $basename));

		return $this->importer->getId() . '_' . $name;
	}


	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		return $this->importer->getLogger();
	}


	/**
	 * @param $message
	 */
	public function logMessage($message)
	{
		return $this->importer->logMessage($message);
	}


	/**
	 * @return \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	public function getContainer()
	{
		return $this->importer->getContainer();
	}


	/**
	 * @return \Application\DeskPRO\DBAL\Connection
	 */
	public function getDb()
	{
		return $this->importer->getContainer()->getDb();
	}


	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEm()
	{
		return $this->importer->getContainer()->getEm();
	}


	/**
	 * @param $type
	 * @param $old_id
	 * @param $new_id
	 */
	public function saveMappedId($type, $old_id, $new_id, $buffer = false)
	{
		$this->importer->saveMappedId($type, $old_id, $new_id, $buffer);
	}



	public function flushSaveMappedIdBuffer()
	{
		$this->importer->flushSaveMappedIdBuffer();
	}


	/**
	 * @param $type
	 * @param $old_id
	 * @return mixed
	 */
	public function getMappedNewId($type, $old_id)
	{
		return $this->importer->getMappedNewId($type, $old_id);
	}


	/**
	 * @param string $type
	 * @param array $old_ids
	 * @return array
	 */
	public function getMappedNewIdsArray($type, array $old_ids)
	{
		return $this->importer->getMappedNewIdsArray($type, $old_ids);
	}


	/**
	 * @param $type
	 * @param $old_id
	 * @return mixed
	 */
	public function getMappedOldId($type, $new_id)
	{
		return $this->importer->getMappedOldId($type, $new_id);
	}
}
