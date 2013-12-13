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
use Application\DeskPRO\App;
use Orb\Util\Dates;

/**
 * Task entity definition
 *
 */
class Task extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * Private visibility constant.
	 * @var int
	 */
	const PRIVATE_VISIBILITY = 0;

	/**
	 * Public visibility constant.
	 * @var int
	 */
	const PUBLIC_VISIBILITY = 1;

	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * Whether this task is completed
	 *
	 * @var bool
	 */
	protected $is_completed = false;

	/**
	 * The task's title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * The task's visibility. On of: self::PRIVATE_VISIBILITY or self::PUBLIC_VISIBILITY.
	 *
	 * @var int
	 */
	protected $visibility = 1;

	/**
	 * The task's optional due date.
	 *
	 * @var \DateTime
	 */
	protected $date_due = null;

	/**
	 * The date the task was inserted into the system
	 *
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * The date the task was completed
	 *
	 * @var \DateTime
	 */
	protected $date_completed;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $assigned_agent;

	/**
	 * @var \Application\DeskPRO\Entity\AgentTeam
	 */
	protected $assigned_agent_team;



		/**
	 */
	protected $labels;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $comments;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $task_associations;

		/**
	 * Label manager for adding/removing labels
	 * @var \Application\DeskPRO\Labels\LabelManager
	 */
	protected $_label_manager = null;


	/**
	 * Creates a new Task
	 */
	public function __construct()
	{
		$this->labels            = new \Doctrine\Common\Collections\ArrayCollection();
		$this->comments          = new \Doctrine\Common\Collections\ArrayCollection();
		$this->task_associations = new \Doctrine\Common\Collections\ArrayCollection();

		$this['date_created'] = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	public function isOverdue()
	{
		if (!$this->date_due) {
			return false;
		}

		return ($this->date_due->getTimestamp() < time());
	}

	public function isDueToday(Person $person_context = null)
	{
		if (!$this->date_due) {
			return true;
		}

		if ($person_context) {
			$tz = $person_context->getDateTimezone();
		} else {
			$tz = Dates::tzUtc();
		}

		$tomorrow = new \DateTime('now', $tz);
		$tomorrow->setTime(0,0,0);

		$cmp_tomorrow = clone $this->date_due;
		$cmp_tomorrow->setTimezone($tz);

		return ($cmp_tomorrow->format('Y-m-d') == $tomorrow->format('Y-m-d'));
	}

	public function isDueTomorrow(Person $person_context = null)
	{
		if (!$this->date_due) {
			return true;
		}

		if ($person_context) {
			$tz = $person_context->getDateTimezone();
		} else {
			$tz = Dates::tzUtc();
		}

		$tomorrow = new \DateTime('now', $tz);
		$tomorrow->setTime(0,0,0);
		$tomorrow->modify('+1 day');

		$cmp_tomorrow = clone $this->date_due;
		$cmp_tomorrow->modify('+1 day');
		$cmp_tomorrow->setTimezone($tz);

		return ($cmp_tomorrow->format('Y-m-d') == $tomorrow->format('Y-m-d'));
	}

	/**
	 * Sets the task visibility.
	 *
	 * @param int $visibility One of: self::PRIVATE_VISIBILITY or self::PUBLIC_VISIBILITY
	 * @throws \InvalidArgumentException Thrown when the visibility is not valid.
	 */
	public function setVisibility($visibility)
	{
		if (! $this->isValidVisibility($visibility)) {
			throw new \InvalidArgumentException('Invalid visibility');
		}

		$old_visibility = $this->visibility;
		$this->visibility = $visibility;
		$this->_onPropertyChanged('visibility', $old_visibility, $visibility);
	}



	/**
	 * Returns whether the visibility is valid or not.
	 *
	 * @param int $visibility The visibility to check
	 * @return bool
	 */
	protected function isValidVisibility($visibility)
	{
		return in_array(
			$visibility,
			array(self::PRIVATE_VISIBILITY, self::PUBLIC_VISIBILITY)
		);
	}



	/**
	 * Returns whether the task has been delegated or not. Tasks assigned to its
	 * creator are not considered delegated.
	 *
	 * @return bool
	 */
	public function isDelegated()
	{
		if ($this->assigned_agent !== null) {
			return ($this->person['id'] !== $this->assigned_agent['id']);
		}

		return ($this->assigned_agent !== null);
	}



	/**
	 * Returns the task's person id.
	 *
	 * @return int
	 */
	public function getPersonId()
	{
		return $this->person['id'];
	}


	public function setCompleted($yes_no)
	{
		if ($yes_no) {
			$this->setModelField('is_completed', true);
			$this->setModelField('date_completed', new \DateTime());
		} else {
			$this->setModelField('is_completed', false);
			$this->setModelField('date_completed', null);
		}
	}



	/**
	 * Sets the task's person id.
	 *
	 * @param int id The agent's id.
	 * @throws \InvalidArgumentException Thrown when there's no preson with that
	 *                                   id or the person is not an agent.
	 */
	public function setPersonId($id)
	{
		if ($this->person['id'] == $id) {
			return;
		}

		$person = App::getEntityRepository('DeskPRO:Person')->find($id);

		if (! $person) {
			throw new \InvalidArgumentException('No agent for id ' . $id);
		}

		if (! $person->isAgent) {
			throw new \InvalidArgumentException(
				'The person with id ' . $id . ' is not an agent'
			);
		}

		$this->person->tasks->remove($this);
		$this['person'] = $person;
	}



	/**
	 * Returns the task's assigned agent's id.
	 *
	 * @return int
	 */
	public function getAsignedAgentId()
	{
		if (! $this->assigned_agent) {
			return 0;
		}

		return $this->assigned_agent['id'];
	}



	/**
	 * Sets the task's assigned agent's id.
	 *
	 * @param int id The agent's id.
	 * @throws \InvalidArgumentException Thrown when there's no preson with that
	 *                                   id or the person is not an agent.
	 */
	public function setAsignedAgentId($id)
	{
		$agent = App::getEntityRepository('DeskPRO:Person')->find($id);

		if (! $agent) {
			throw new \InvalidArgumentException('No agent for id ' . $id);
		}

		if (! $agent->is_agent) {
			throw new \InvalidArgumentException(
				'The person with id ' . $id . ' is not an agent'
			);
		}

		$this['assigned_agent'] = $agent;
		if ($agent) {
			$this->setModelField('assigned_agent_team', null);
		}
	}



	/**
	 * Returns the task's assigned agent team's id.
	 *
	 * @return int
	 */
	public function getAsignedAgentTeamId()
	{
		if (! $this->assigned_agent_team) {
			return 0;
		}

		return $this->assigned_agent_team['id'];
	}



	/**
	 * Sets the task's assigned agent team's id.
	 *
	 * @param int id The agent team's id.
	 * @throws \InvalidArgumentException Thrown when there's no team with that id
	 */
	public function setAsignedAgentTeamId($id)
	{
		$agent_team = App::getEntityRepository('DeskPRO:AgentTeam')->find($id);

		if (! $agent_team) {
			throw new \InvalidArgumentException('No agent team for id ' . $id);
		}

		$this['assigned_agent_team'] = $agent_team;
		if ($agent_team) {
			$this->setModelField('assigned_agent', null);
		}
	}



	/**
	 * Adds a label
	 * @param \Application\DeskPRO\Entity\LabelTask $label
	 */
	public function addLabel(LabelTask $label)
	{
		$label['task'] = $this;
		$this->labels->add($label);
	}



	/**
	 * Adds a comment to the task.
	 *
	 * @param \Application\DeskPRO\Entity\Person $author The comment's author
	 * @param string $comment_content The comment's content
	 */
	public function addComment(Person $author, $comment_content)
	{
		$comment = new TaskComment($author, $comment_content);
		$comment->task = $this;

		$this->comments->add($comment);
	}

	public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:LabelTask');
		}

		return $this->_label_manager;
	}


	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		if ($deep) {
			$data['labels'] = array();
			foreach ($this->labels AS $label) {
				$data['labels'][] = $label['label'];
			}
		}

		return $data;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Task';
		$metadata->setPrimaryTable(array( 'name' => 'tasks', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'is_completed', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_completed', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'visibility', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'visibility', ));
		$metadata->mapField(array( 'fieldName' => 'date_due', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_due', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_completed', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_completed', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'assigned_agent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => 'assigned_tasks', 'joinColumns' => array( 0 => array( 'name' => 'assigned_agent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'assigned_agent_team', 'targetEntity' => 'Application\\DeskPRO\\Entity\\AgentTeam', 'mappedBy' => NULL, 'inversedBy' => 'assigned_tasks', 'joinColumns' => array( 0 => array( 'name' => 'assigned_agent_team_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelTask', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'task', 'orphanRemoval' => true, ));
		$metadata->mapOneToMany(array( 'fieldName' => 'comments', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TaskComment', 'mappedBy' => 'task', 'dpApi' => true, 'dpApiDeep' => true, 'dpApiPrimary' => true  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'task_associations', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TaskAssociation', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'task', 'orphanRemoval' => true, 'dpApi' => true, 'dpApiDeep' => true ));
	}
}
