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

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Log of searches on userend
 *
 */
class SearchLog extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor = null;

	/**
	 * @var string
	 */
	protected $ip_address = '';

	/**
	 * @var string
	 */
	protected $email = null;

	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var string
	 */
	protected $query;

	/**
	 * @var string
	 */
	protected $num_results;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	public static function create($query, $num_results, $use_request = true)
	{
		$searchlog = new self();
		$searchlog->query = $query;
		$searchlog->num_results = $num_results;

		if ($use_request && App::has('request')) {
			if (!App::getCurrentPerson()->isGuest()) {
				$searchlog->person = App::getCurrentPerson();
			}

			$searchlog->visitor = App::getSession()->getVisitor();
		}

		return $searchlog;
	}

	public function __construct()
	{
		$this['date_created'] = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Sets the query after trying to normalize it a bit
	 *
	 * @param $query
	 */
	public function setQuery($query)
	{
		$query = trim($query);
		$query = preg_replace('# {2,}#', ' ', $query);
		$query = Strings::utf8_strtolower($query);
		$query = Strings:: utf8_accents_to_ascii($query);

		$this['query'] = $query;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\SearchLog';
		$metadata->setPrimaryTable(array(
			'name' => 'searchlog',
			'indexes' => array(
				'searchlog_query_idx' => array('columns' => array('query')),
				'num_results_idx' => array('columns' => array('num_results')),
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'ip_address', 'type' => 'string', 'length' => 30, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ip_address', ));
		$metadata->mapField(array( 'fieldName' => 'email', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'email', ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'query', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'query', ));
		$metadata->mapField(array( 'fieldName' => 'num_results', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'num_results', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'visitor', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Visitor', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'visitor_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
