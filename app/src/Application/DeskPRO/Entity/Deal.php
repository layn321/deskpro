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
use Application\DeskPRO\Entity\LabelDeal;
use Application\DeskPRO\App;

/**
 * Deal entity definition
 *
 */

abstract class Deal extends \Application\DeskPRO\Domain\DomainObject
{

    /**
     * Deal open status constant.
     * @var int
     */
    const DEAL_OPEN = 0;

    /**
     * Deal won status constant.
     * @var int
     */
    const DEAL_WON = 1;

    /**
     * Deal lost constant.
     * @var int
     */
    const DEAL_LOST = 2;

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
     * The unique ID
     *
     * @var int
     *
     */
    protected $id = null;

    /**

     * @var strint
     */
    protected $title;

    /**
     * Deal type
     *
     */
    protected $deal_type;

    /**
     * Deal type
     *
     */
    protected $deal_stage;

    /**
     * The deal status. On of: self::DEAL_OPEN,
     * self::DEAL_WON or self::DEAL_LOST.
     *
     * @var int
     */
    protected $status = 0;

    /**
     *
     *
     * @var Application\DeskPRO\Entity\Person
     */
    protected $person;

    /**
     * @var Application\DeskPRO\Entity\Person
     */
    protected $assigned_agent;

    /**
     * The deal probability
     *
     * @var float
     */
    protected $probability = 0.0;

    /**
     * The deal value
     *
     * @var float
     */
    protected $deal_value = 0.0;

    /**
     * Deal Currency type
     *
     */
    protected $deal_currency = null;

    /**
     */
    protected $labels;

    /**
     */
    protected $deal_mapper;

    /**
     * Deal Linked to Relevent peoples.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * )
     */
    protected $peoples;

    /**
     * Deal Linked to Relevent organization.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * )
     */
    protected $organizations;

    /**
     * The task's visibility. On of: self::PRIVATE_VISIBILITY
     * or self::PUBLIC_VISIBILITY.
     *
     * @var int
     */
    protected $visibility = 0;

    /**
     * @var \DateTime
     */
    protected $date_created;


    /**
     * Label manager for adding/removing labels
     * @var \Application\DeskPRO\Labels\LabelManager
     */
    protected $_label_manager = null;

    /**
     */
    protected $attachments;

    /**
     */
    protected $custom_data;

    /**
     * Creates a new deal
     */
    public function __construct()
    {
        $this->labels            = new \Doctrine\Common\Collections\ArrayCollection();
        $this->task_associations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->people = new \Doctrine\Common\Collections\ArrayCollection();
        $this->organizations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->task_associations      = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deal_mapper   = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->custom_data = new \Doctrine\Common\Collections\ArrayCollection();


        $this->date_created = new \DateTime();
    }

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

    /**
	 * Set custom field data for a particular field.
	 *
	 * @param int $field_id
	 * @param mixed $value
	 * @return mixed
	 */
	public function setCustomData($field_id, $value_type, $value)
	{
		$custom_data = $this->getCustomDataForField($field_id);
		$is_new = false;

		if (!$custom_data) {
			if ($value === null) return null;

			$is_new = true;

			$field = App::getEntityRepository('DeskPRO:CustomDefDeal')->find($field_id);
			if (!$field) {
				throw new \Exception("Invalid field_id `$field_id`");
			}
			$custom_data = new CustomDataPerson();
			$custom_data['field'] = $field;
		}

		if ($value === null) {
			$this['custom_data']->removeElement($custom_data);
			return null;
		}

		$custom_data[$value_type] = $value;

		if ($is_new) {
			$this->addCustomData($custom_data);
		}

		return $custom_data;
	}

	/**
	 * Add a custom data item to this deal
	 *
	 * @param CustomDataDeal $data
	 */
	public function addCustomData(CustomDataDeal $data)
	{
		$this->custom_data->add($data);
		$data['deal'] = $this;
	}



	/**
	 * Render a custom field
	 *
	 * !depreciated
	 */
	public function renderCustomField($field_id, $context = 'html')
	{
		$f_def = App::getEntityRepository('DeskPRO:CustomDefDeal')->find($field_id);

		$data_structured = App::getApi('custom_fields.util')->createDataHierarchy($this->custom_data, array($f_def));

		$value = !empty($data_structured[$f_def['id']]) ? $data_structured[$f_def['id']] : null;
		$rendered = $value ? $f_def->getHandler()->renderContext($context, $value) : null;

		return $rendered;
	}


	/**
	 * Check if this ticket has a custom field.
	 *
	 * @param $field_id
	 * @return bool
	 */
	public function hasCustomField($field_id)
	{
		foreach ($this->custom_data as $data) {
			if ($data->field['id'] == $field_id) {
				return true;
			}
		}

		return false;
	}

        /**
	 * Add a label
	 * @param \Application\DeskPRO\Entity\LabelDeal $label
	 */
	public function addLabel(LabelDeal $label)
	{
		$label['deal'] = $this;
		$this->labels->add($label);
	}

        public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:LabelDeal');
		}

		return $this->_label_manager;
	}

        /**
	 * Returns the task's assigned agent's id.
	 *
	 * @return int
	 */
	public function getPersonId()
	{
		if (! $this->person) {
			return 0;
		}

		return $this->person['id'];
	}

	/**
	 * Sets the task's assigned agent's id.
	 *
	 * @param int id The agent's id.
	 * @throws \InvalidArgumentException Thrown when there's no preson with that
	 *                                   id or the person is not an agent.
	 */
        public function setPersonId($id)
	{
		if(!$id || $id == null){
                    $this->person = null;
                    return;
                }


                $person = App::getEntityRepository('DeskPRO:Person')->find($id);

		if (! $person) {
			throw new \InvalidArgumentException('No agent for id ' . $id);
		}
		$this->person = $person;
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
		if(!$id || $id == null){
                    $this->assigned_agent = null;
                    return;
                }


                $agent = App::getEntityRepository('DeskPRO:Person')->find($id);

		if (! $agent) {
			throw new \InvalidArgumentException('No agent for id ' . $id);
		}

		if (! $agent->is_agent) {
			throw new \InvalidArgumentException(
				'The person with id ' . $id . ' is not an agent'
			);
		}

		$this->assigned_agent = $agent;
	}


        /**
	 * Returns the task's assigned agent's id.
	 *
	 * @return int
	 */
	public function getDealTypeId()
	{
		if (! $this->deal_type) {
			return 0;
		}

		return $this->deal_type['id'];
	}

	/**
	 * Sets the task's assigned agent's id.
	 *
	 * @param int id The agent's id.
	 * @throws \InvalidArgumentException Thrown when there's no preson with that
	 *                                   id or the person is not an agent.
	 */
        public function setDealTypeId($id)
	{
		if(!$id || $id == null){
                    $this->deal_type = null;
                    return;
                }

                $deal_type = App::getEntityRepository('DeskPRO:DealType')->find($id);
		$this->deal_type = $deal_type;
	}


        /**
	 * Returns the task's assigned agent's id.
	 *
	 * @return int
	 */
	public function getDealStageId()
	{
		if (! $this->deal_stage) {
			return 0;
		}

		return $this->deal_stage['id'];
	}

	/**
	 * Sets the task's assigned agent's id.
	 *
	 * @param int id The agent's id.
	 * @throws \InvalidArgumentException Thrown when there's no preson with that
	 *                                   id or the person is not an agent.
	 */
        public function setDealStageId($id)
	{
		if(!$id || $id == null){
                    $this->deal_stage = null;
                    return;
                }

                $deal_stage = App::getEntityRepository('DeskPRO:DealStage')->find($id);
		$this->deal_stage = $deal_stage;
	}


         /**
	 * Returns the deal's currency id
	 *
	 * @return int
	 */
	public function getDealCurrencyId()
	{
		if (! $this->deal_currency) {
			return 0;
		}

		return $this->deal_currency['id'];
	}

	/**
	 * Sets the deal's currency id
	 *
	 * @param int $id
	 * @throws \InvalidArgumentException Thrown when there's no currenct with that id
	 */
        public function setDealCurrencyId($id)
	{
		if(!$id || $id == null){
                    $this->deal_currency = null;
                    return;
                }

                $deal_currency = App::getEntityRepository('DeskPRO:Currency')->find($id);
		$this->deal_currency = $deal_currency;
	}



        public function deletePeople(\Application\DeskPRO\Entity\Person $person)
        {
            $this->peoples->removeElement($person);
        }

        public function deleteOrganization(\Application\DeskPRO\Entity\Organization $organization)
        {
            $this->organizations->removeElement($organization);
        }

        /**
	 * Add a label
	 * @param \Application\DeskPRO\Entity\People $people
	 */
	public function addPeoples(\Application\DeskPRO\Entity\Person $people)
	{
		$this->peoples[] = $people;
	}


        /**
         * Get peoples
         *
         * @return \Doctrine\Common\Collections\ArrayCollection
         */
        public function getPeoples()
        {
            return $this->peoples;
        }

        /**
	 * Add organizations
         *
	 * @param \Application\DeskPRO\Entity\Organizations $organizations
	 */
	public function addOrganizations(\Application\DeskPRO\Entity\Organization $organizations)
	{
		$this->organizations[] = $organizations;
	}

        /**
         * Get organizations
         *
         * @return \Doctrine\Common\Collections\ArrayCollection
         */
        public function getOrganizations()
        {
            return $this->organizations;
        }

        /**
	 * Add  attachments
         *
	 * @param \Application\DeskPRO\Entity\DealAttachment $attachments
	 */
	public function addAttachments(\Application\DeskPRO\Entity\DealAttachment $attachments)
	{
		$this->attachments[] = $attachments;
	}

        /**
         * Get attachments
         *
         * @return \Doctrine\Common\Collections\ArrayCollection
         */
        public function getAttachments()
        {
            return $this->attachments;
        }



	############################################################################
	# Doctrine Metadata
	############################################################################


	public static function x_loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Deal';
		$metadata->setPrimaryTable(array(
			'name' => 'deals',
			'indexes' => array(
				'status_idx' => array('columns' => array('status')),
			),
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'status', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'status', ));
		$metadata->mapField(array( 'fieldName' => 'probability', 'type' => 'float', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'probability', ));
		$metadata->mapField(array( 'fieldName' => 'deal_value', 'type' => 'float', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'deal_value', ));
		$metadata->mapField(array( 'fieldName' => 'visibility', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'visibility', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'deal_type', 'targetEntity' => 'Application\\DeskPRO\\Entity\\DealType', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'deal_type_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'deal_stage', 'targetEntity' => 'Application\\DeskPRO\\Entity\\DealStage', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'deal_stage_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => 'deal', 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'assigned_agent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'assigned_agent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'deal_currency', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Currency', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'currency_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelDeal', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'deal', 'orphanRemoval' => true, ));
		$metadata->mapOneToMany(array( 'fieldName' => 'deal_mapper', 'targetEntity' => 'Application\\DeskPRO\\Entity\\DealMapper', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'deal', 'orphanRemoval' => true, ));
		$metadata->mapManyToMany(array( 'fieldName' => 'peoples', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'joinTable' => array( 'name' => 'deal_people', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'deal_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), ));
		$metadata->mapManyToMany(array( 'fieldName' => 'organizations', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Organization', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'joinTable' => array( 'name' => 'deal_organizations', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'deal_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'organization_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), ));
		$metadata->mapOneToMany(array( 'fieldName' => 'attachments', 'targetEntity' => 'Application\\DeskPRO\\Entity\\DealAttachment', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'deal',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'custom_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDataDeal', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'deal', 'orphanRemoval' => true, ));
	}
}
