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
 * @subpackage Elastica
 */

namespace Application\DeskPRO\Elastica\Searcher;

use Application\DeskPRO\Elastica\ElasticaManager;
use Application\DeskPRO\Entity\Person;

use Orb\Util\Strings;

/**
 * Searchers create queries against the elasticsearch server.
 */
abstract class AbstractSearcher
{
	/**
	 * @var \Application\DeskPRO\Elastica\ElasticaManager
	 */
	protected $manager;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Elastica\ElasticaManager $manager
	 * @var \Application\DeskPRO\Entity\Person $person
	 */
	public function __construct(ElasticaManager $manager, Person $person)
	{
		$this->manager = $manager;
		$this->person = $person;
	}

	

	/**
	 * Convert an array of document results into an array that includes their real object entities.
	 * 
	 * @param array $documents
	 * @return array
	 */
	public function documentsToResults(array $documents)
	{
		$results = array();

		foreach ($documents as $doc) {
			$type = Strings::dashToCamelCase($doc->getType());
			$class  = 'Application\\DeskPRO\\Elastica\\Type\\' . ucfirst($type) . 'Type';

			$type = new $class($this->manager);
			$object = $type->transformToType($doc);
			if ($object) {
				$results[] = array('object' => $object, 'document' => $doc);
			}
		}

		return $results;
	}
}
