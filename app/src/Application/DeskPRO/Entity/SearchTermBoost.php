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

/**
 * Stores which documents have been boosted, and by which terms
 *
 */
class SearchTermBoost extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * A 'voted' boost means the boost comes from a user
	 * upvoting a particular document after coming from a search.
	 */
	const METHOD_VOTE = 'vote';

	/**
	 * An 'agent' boost means an agent has manually entered a boost term
	 */
	const METHOD_AGENT = 'agent';



	/**
	 * @var string
	 */
	protected $object_type;

	/**
	 * @var int
	 */
	protected $object_id = null;

	/**
	 * Is this an agent bossted term?
	 *
	 * If not, then the b
	 *
	 * @var bool
	 */
	protected $boosted_method = false;

	/**
	 * @var string
	 */
	protected $boosted_terms;



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array( 'name' => 'search_term_boosters', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'object_type', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_type', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'object_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'boosted_method', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_user', ));
		$metadata->mapField(array( 'fieldName' => 'boosted_terms', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'boosted_terms', ));
	}
}
