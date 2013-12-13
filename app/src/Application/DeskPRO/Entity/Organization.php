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
use Orb\Util\Arrays;
use Orb\Util\Numbers;

use Application\DeskPRO\Entity;


/**
 * An organization is a grouping we put similar people into (eg companies).
 *
 */
class Organization extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * The org picture
	 *
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $picture_blob = null;

	/**
	 * The organization name
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * The summary field as filled in by agents
	 *
	 * @var string
	 */
	protected $summary = '';

	/**
	 * The org importance
	 *
	 * @var int
	 */
	protected $importance = 0;

	/**
	 */
	protected $custom_data;

	/**
	 * Usergroups the user belongs to
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $usergroups;

	/**
	 * Users who are set to automatically be added to tickets and other org things
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $auto_cc_people;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $labels;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $contact_data;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $email_domains;

	/**
	 * The date the org was inserted into the system
	 *
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $slas;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $twitter_users;

	protected $_label_manager = null;

	public function __construct()
	{
		$this->setModelField('email_domains'       , new \Doctrine\Common\Collections\ArrayCollection());
		$this->setModelField('custom_data'         , new \Doctrine\Common\Collections\ArrayCollection());
		$this->setModelField('labels'              , new \Doctrine\Common\Collections\ArrayCollection());
		$this->setModelField('contact_data'        , new \Doctrine\Common\Collections\ArrayCollection());
		$this->setModelField('usergroups'          , new \Doctrine\Common\Collections\ArrayCollection());
		$this->setModelField('twitter_users'       , new \Doctrine\Common\Collections\ArrayCollection());
		$this->setModelField('date_created'        , new \DateTime());
		$this->slas = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Set the default importance of people in this org
	 *
	 * @param int $importance
	 */
	public function setImportance($importance)
	{
		$old = $this->importance;
		$this->importance = Numbers::bound($importance, 0, 5);
		$this->_onPropertyChanged('importance', $old, $this->importance);
	}


	/**
	 * Find an existing data record for a field id.
	 *
	 * @param int $field_id
	 * @return CustomDataOrganization
	 */
	public function getCustomDataForField($field_id)
	{
		foreach ($this->custom_data as $data) {
			if ($data['field_id'] == $field_id) {
				return $data;
			}
		}

		$this->_onPropertyChanged('custom_data', $this->custom_data, $this->custom_data);

		return null;
	}

	/**
	 * Add contact data
	 *
	 * @param OrganizationContactData $contact_data
	 */
	public function addContactData(OrganizationContactData $contact_data)
	{
		$em = App::getOrm();

		$this['contact_data']->add($contact_data);

		$contact_data['organization'] = $this;
		$em->persist($contact_data);
		$this->_onPropertyChanged('contact_data', $this->contact_data, $this->contact_data);
	}


	/**
	 * @param null $type
	 * @return array
	 */
	public function getContactData($type = null)
	{
		if (!$type) {
			return $this->contact_data;
		}

		$ret = array();

		foreach ($this->contact_data as $cd) {
			if ($cd->contact_type == $type) {
				$ret[] = $cd;
			}
		}

		return $ret;
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

			$field = App::getEntityRepository('DeskPRO:CustomDefOrganization')->find($field_id);
			if (!$field) {
				throw new \Exception("Invalid field_id `$field_id`");
			}
			$custom_data = new CustomDataOrganization();
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

		$this->_onPropertyChanged('custom_data', $this->custom_data, $this->custom_data);

		return $custom_data;
	}

	/**
	 * Add a custom data item to this ticket
	 *
	 * @param CustomDataTicket $data
	 */
	public function addCustomData(CustomDataOrganization $data)
	{
		$this->custom_data->add($data);
		$data['organization'] = $this;
		$this->_onPropertyChanged('custom_data', $this->custom_data, $this->custom_data);
	}


	/**
	 * Render a custom field
	 */
	public function renderCustomField($field_id, $context = 'html')
	{
		$f_def = App::getEntityRepository('DeskPRO:CustomDefOrganization')->find($field_id);

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
	 * Gets a display array for a specific field
	 * @param $field_id
	 * @return array|mixed|null
	 */
	public function getCustomFieldDisplayArray($field_id)
	{
		$data = $this->getCustomDataForField($field_id);
		if (!$data) {
			return null;
		}

		$org_field_defs = App::getApi('custom_fields.organizations')->getEnabledFields();
		$org_data_structured = App::getApi('custom_fields.util')->createDataHierarchy(array($data), $org_field_defs);

		$custom_fields = App::getApi('custom_fields.organizations')->getFieldsDisplayArray(
			$org_field_defs,
			$org_data_structured
		);

		$custom_fields = array_pop($custom_fields);

		return $custom_fields;
	}


	/**
	 * Gets the URL to a picture for the org. If there is no picture for the org, a default one
	 * will be rendered. Use hasPicture if you need to know if a picture exists
	 *
	 * @return null|string
	 */
	public function getPictureUrl($size = 80, $secure = null)
	{
		// Null means detect
		if ($secure === null AND App::isWebRequest()) {
			$request = App::getRequest();
			if ($request->isSecure()) {
				$secure = true;
			}
		}

		$url = false;
		if ($this->picture_blob) {
			$url = App::get('router')->generate('serve_blob_sizefit', array(
				'blob_auth_id' => $this->picture_blob->getAuthId(),
				'filename' => $this->picture_blob->filename,
				's' => $size
			), true);
		}

		if (!$url) {
			$url = App::get('router')->generate('serve_org_picture_default', array(
				's' => $size,
				'size-fit' => 1,
			), true);
		}

		if ($secure) {
			$url = preg_replace('#^http:#', 'https:', $url);
		}

		return $url;
	}



	/**
	 * Cehck if the company has a picture uploaded
	 *
	 * @return bool
	 */
	public function hasPicture()
	{
		if ($this->picture_blob) {
			return true;
		}

		return false;
	}

	public function hasSla(Sla $sla)
	{
		foreach ($this->slas AS $org_sla) {
			if ($org_sla->id == $sla->id) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Add a label
	 * @param Entity\LabelOrganization $label
	 */
	public function addLabel(Entity\LabelOrganization $label)
	{
		$label['organization'] = $this;
		$this->labels->add($label);
		$this->_onPropertyChanged('labels', $this->labels, $this->labels);
	}

	public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:LabelOrganization');
		}

		return $this->_label_manager;
	}

	public function __toString()
	{
		return $this->name;
	}



	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		if ($deep) {
			$data['labels'] = array();
			foreach ($this->labels AS $label) {
				$data['labels'][] = $label['label'];
			}

			$data['email_domains'] = array();
			foreach ($this->email_domains AS $domain) {
				$data['email_domains'][] = $domain->domain;
			}
		}

		$data['member_count'] = App::getEntityRepository('DeskPRO:Organization')->countMembersFor($this);
		$data['picture_url'] = $this->getPictureUrl();

		$field_manager = App::getContainer()->getSystemService('org_fields_manager');
		$field_manager->addApiData($this, $data);

		return $data;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Organization';
		$metadata->setPrimaryTable(array( 'name' => 'organizations', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'summary', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'summary', ));
		$metadata->mapField(array( 'fieldName' => 'importance', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'importance', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'picture_blob', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'picture_blob_id', 'referencedColumnName' => 'id', 'unique' => true, 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'custom_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDataOrganization', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'organization', 'orphanRemoval' => true, 'dpApi' => true));
		$metadata->mapManyToMany(array( 'fieldName' => 'usergroups', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Usergroup', 'indexBy' => 'id', 'joinTable' => array( 'name' => 'organization2usergroups', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'organization_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'usergroup_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), 'dpApi' => true ));
		$metadata->mapManyToMany(array( 'fieldName' => 'auto_cc_people', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'joinTable' => array( 'name' => 'organizations_auto_cc', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'organization_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelOrganization', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'organization', 'orphanRemoval' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'contact_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\OrganizationContactData', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'organization', 'orphanRemoval' => true, 'indexBy' => 'id', 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'email_domains', 'targetEntity' => 'Application\\DeskPRO\\Entity\\OrganizationEmailDomain', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'organization', ));
		$metadata->mapManyToMany(array( 'fieldName' => 'slas', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Sla', 'cascade' => array('persist','merge'), 'mappedBy' => 'organizations', 'dpApi' => true));
		$metadata->mapOneToMany(array( 'fieldName' => 'twitter_users', 'targetEntity' => 'Application\\DeskPRO\\Entity\\OrganizationTwitterUser', 'mappedBy' => 'organization',  ));
	}
}
