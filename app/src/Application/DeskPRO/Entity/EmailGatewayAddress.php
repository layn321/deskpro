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
use Orb\Util\Strings;

/**
 * Describes which addresses an email gateway expects
 *
 */
class EmailGatewayAddress extends \Application\DeskPRO\Domain\DomainObject
{
	const MATCH_TYPE_EXACT  = 'exact';
	const MATCH_TYPE_DOMAIN = 'domain';
	const MATCH_TYPE_REGEX  = 'regex';

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\EmailGateway
	 */
	protected $gateway;

	/**
	 * @var string
	 */
	protected $match_type = 'exact';

	/**
	 * @var string
	 */
	protected $match_pattern = '';

	/**
	 * @var int
	 */
	protected $run_order = 0;

	/**
	 * @static
	 * @param \Application\DeskPRO\Entity\EmailGateway $gateway
	 * @param string $email_address
	 * @return \Application\DeskPRO\Entity\EmailGatewayAddress
	 */
	public static function newEmailAddress(EmailGateway $gateway, $email_address)
	{
		$addr = new self();
		$addr->gateway = $gateway;
		$addr->match_type = 'exact';
		$addr->match_pattern = $email_address;

		return $addr;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getTitle()
	{
		switch ($this->match_type) {
			case self::MATCH_TYPE_EXACT: return $this->match_pattern;
			case self::MATCH_TYPE_DOMAIN: return '*@' . $this->match_pattern;
			case self::MATCH_TYPE_REGEX: return $this->match_pattern;
		}

		return '';
	}

	public function __toString()
	{
		return $this->getTitle();
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\EmailGatewayAddress';
		$metadata->setPrimaryTable(array( 'name' => 'email_gateway_addresses', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'match_type', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'match_type', ));
		$metadata->mapField(array( 'fieldName' => 'match_pattern', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'match_pattern', ));
		$metadata->mapField(array( 'fieldName' => 'run_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'run_order', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'gateway', 'targetEntity' => 'Application\\DeskPRO\\Entity\\EmailGateway', 'mappedBy' => NULL, 'inversedBy' => 'addresses', 'joinColumns' => array( 0 => array( 'name' => 'email_gateway_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
