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
use Application\DeskPRO\Entity;

/**
 * Twitter Status Mention
 *
 */
class TwitterStatusMention extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterStatus
	 */
	protected $status;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterUser
	 */
	protected $user;

	/**
	 * @var integer
	 */
	protected $starts = 0;

	/**
	 * @var integer
	 */
	protected $ends = 0;

	/**
	 * @return integer
	 */
	public function getStatusId()
	{
		if (null !== $this->status) {
			return $this->status->getId();
		}

		return 0;
	}

	/**
	 * @param integer $id
	 */
	public function setStatusId($id)
	{
		$this->status = null;

		if ($id && $status = App::getOrm()->getRepository('DeskPRO:TwitterStatus')->find($id)) {
			$this->status = $status;
		}
	}

	/**
	 * @return integer
	 */
	public function getUserId()
	{
		return null !== $this->user ? $this->user->getId() : null;
	}

	/**
	 * @param integer $id
	 */
	public function setUserId($id)
	{
		if ($id && $user = App::getOrm()->getRepository('DeskPRO:TwitterUser')->find($id)) {
			$this->user = $user;
		} else {
			$this->user = null;
		}
	}

	/**
	 * @param object $mention
	 * @return \Application\DeskPRO\Entity\TwitterStatusMention
	 */
	static public function createFromJson($mention)
	{
		$entity = new self();
		$entity['starts'] = $mention->indices[0];
		$entity['ends'] = $mention->indices[1];

		return $entity;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################


	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Basic';
		$metadata->setPrimaryTable(array( 'name' => 'twitter_statuses_mentions', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'starts', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'starts', ));
		$metadata->mapField(array( 'fieldName' => 'ends', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ends', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'status', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => NULL, 'inversedBy' => 'mentions', 'joinColumns' => array( 0 => array( 'name' => 'status_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'user', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUser', 'mappedBy' => NULL, 'inversedBy' => 'mentions', 'joinColumns' => array( 0 => array( 'name' => 'user_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
