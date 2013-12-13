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

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\App;

use Orb\Util\Strings;

/**
 * A simple DB table cache for k=>v
 *
 */
class Cache extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $data = array();

	/**
	 * @var \DateTime
	 */
	protected $date_expire;

	public function setData($data)
	{
		if (!is_array($data)) {
			$this['data'] = array('VALUE' => $data);
		}
	}

	public function getData()
	{
		if (isset($this->data['VALUE'])) {
			return $this->data['VALUE'];
		}

		return $this->data;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Cache';
		$metadata->setPrimaryTable(array(
			'name' => 'cache',
			'indexes' => array(
				'date_expire_idx' => array('columns' => array('date_expire')),
			),
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'data', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'data', ));
		$metadata->mapField(array( 'fieldName' => 'date_expire', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_expire', ));
	}
}
