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

namespace Application\DeskPRO\ContentSearch;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;

use Orb\Util\Strings;

class RelatedContentFinder
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Entity\Article
	 */
	protected $entity;

	/**
	 * @var array
	 */
	protected $related_records;

	/**
	 * @var array
	 */
	protected $related_entities;

	/**
	 * @param \Application\DeskPRO\Entity\Person $person Person context to run the search from. Affects permissions.
	 * @param mixed $entity The entity we want to fetch related stuff for
	 */
	public function __construct(Person $person, $entity)
	{
		$this->person = $person;
		$this->entity = $entity;
		$this->entity_type = $entity->getTableName();
	}



	/**
	 * Get an array of object types and their related ID's.
	 *
	 * <code>
	 * array(
	 *     'some_type' => array(1,2,3),
	 *     'another_type' => array(4,5,6)
	 * )
	 * </code>
	 *
	 * @return array
	 */
	public function getRelatedRecords()
	{
		if ($this->related_records !== null) return $this->related_records;

		$this->related_records = array();

		$rels = App::getDb()->fetchAll("
			SELECT object_type, object_id
			FROM related_content
			WHERE rel_object_type = ? AND rel_object_id = ?

			UNION DISTINCT

			SELECT rel_object_type AS object_type, rel_object_id AS object_id
			FROM related_content
			WHERE related_content.object_type = ? AND related_content.object_id = ?
		", array($this->entity_type, $this->entity->getId(), $this->entity_type, $this->entity->getId()));

		foreach ($rels as $rel) {
			if (!isset($this->related_records[$rel['object_type']])) {
				$this->related_records[$rel['object_type']] = array();
			}

			$this->related_records[$rel['object_type']][] = $rel['object_id'];
		}

		return $this->related_records;
	}



	/**
	 * Processes related records and actually fetches the objects. Uses
	 * fetchers that will know how to do things like apply permissions
	 * based on the current user context.
	 *
	 * @return array
	 */
	public function getRelatedEntities()
	{
		if ($this->related_entities !== null) return $this->related_entities;

		$this->related_entities = array();

		foreach ($this->getRelatedRecords() as $type => $rels) {
			$rels = array_unique($rels);

			$class = $this->getFetcherClassFromType($type);
			if (!$class) {
				throw new \RuntimeException("Unknown content type `$type`");
			}

			$fetcher = new $class($this->person);

			$entities = $fetcher->getEntities($rels);

			if ($entities) {
				$this->related_entities[$type] = $entities;
			}
		}

		return $this->related_entities;
	}



	/**
	 * Get the fetcher class based on a typename.
	 *
	 * @param  $type
	 * @return string
	 */
	public function getFetcherClassFromType($type)
	{
		switch ($type) {
			case 'articles':
				return 'Application\\DeskPRO\\ContentSearch\\Fetcher\\ArticlesFetcher';
				break;

			case 'downloads':
				return 'Application\\DeskPRO\\ContentSearch\\Fetcher\\DownloadsFetcher';
				break;

			case 'feedback':
				return 'Application\\DeskPRO\\ContentSearch\\Fetcher\\FeedbackFetcher';
				break;

			case 'news':
				return 'Application\\DeskPRO\\ContentSearch\\Fetcher\\NewsFetcher';
				break;
                        case 'deals':
				return 'Application\\DeskPRO\\ContentSearch\\Fetcher\\DealsFetcher';
				break;
		}

		return null;
	}
}
