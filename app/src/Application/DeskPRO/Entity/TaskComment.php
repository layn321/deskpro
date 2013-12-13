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
 * @copyright Copyright (c) 2011 DeskPRO (http://www.deskpro.com/)
 */

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Application\DeskPRO\Markdown;
use Application\DeskPRO\App;

/**
 * TaskComment entity definition
 *
 */
class TaskComment extends \Application\DeskPRO\Domain\DomainObject
{

	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * The comment's content
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * @var Application\DeskPRO\Entity\Task
	 * 	targetEntity="Task",
	 * 	inversedBy="comments",
	 * 	cascade={"persist", "remove", "merge"}
	 * )
	 */
	protected $task;

	/**
	 * @var Application\DeskPRO\Entity\Person
	 * 	targetEntity="Person",
	 * 	inversedBy="task_comments",
	 * 	cascade={"persist", "remove", "merge"}
	 * )
	 */
	protected $person;

	/**
	 * The date the comment was inserted into the system
	 *
	 * @var \DateTime
	 */
	protected $date_created;



	/**
	 * Creates a new comment with the provided content.
	 *
	 * @param \Application\DeskPRO\Entity\Person $creator The comment's creator.
	 * @param string $content The comment's content
	 */
	public function __construct(Person $creator, $content)
	{
		$this['person'] = $creator;
		$this['content'] = $content;

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
	 * Returns the creator's id.
	 *
	 * @return int
	 */
	public function getPersonId()
	{
		return $this->person['id'];
 	}



	/**
	 * Sets the task comment's creator id.
	 *
	 * @param int id The person's id.
	 * @throws \InvalidArgumentException Thrown when there's no preson with the
	 *                                   id is not in the databse.
	 */
	public function setPersonId($id)
	{
		if ($this->person['id'] == $id) {
			return;
		}

		$person = App::getEntityRepository('DeskPRO:Person')->find($id);

		if (! $person) {
			throw new \InvalidArgumentException('No person for id ' . $id);
		}

		$this->person->taskComments->remove($this);
		$this->person = $person;
	}

	public function getContentHtml()
	{
		return Markdown::format(htmlspecialchars($this->content, \ENT_NOQUOTES, 'UTF-8'));
	}

	public function getContentHtmlPlain()
	{
		return nl2br(htmlspecialchars($this->content));
	}

	public function getContentPlain()
	{
		if (!$this->content) {
			return '';
		}
		$content = Strings::standardEol($this->content);
		$content = preg_replace("#<br\s*/?><p>#", "<p>", $content);
		$content = preg_replace("#<p></p><br\s*/?>#", "<p>", $content);
		$content = preg_replace("#</p><br\s*/?>#", "</p>", $content);
		$content = preg_replace("#<br\s*/?></p>#", "</p>", $content);
		$content = preg_replace("#<br\s*/?>?#", "\n", $content);
		$content = preg_replace("#<p>\n?#", "\n", $content);
		$content = preg_replace("#\n?</p>#", "\n", $content);
		$content = html_entity_decode(strip_tags($content), \ENT_QUOTES, 'UTF-8');
		$content = trim($content);

		$lines_raw = explode("\n", $content);
		$lines = array();
		foreach ($lines_raw as $l) {
			$lines[] = trim($l);
		}

		$content = implode("\n", $lines);
		$content = preg_replace("#\n{3,}#", "\n\n", $content);

		return $content;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Basic';
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array( 'name' => 'task_comments', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'content', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'content', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'task', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Task', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => NULL, 'inversedBy' => 'comments', 'joinColumns' => array( 0 => array( 'name' => 'task_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => NULL, 'inversedBy' => 'task_comments', 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true  ));
	}
}
