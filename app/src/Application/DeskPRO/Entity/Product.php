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
use Application\DeskPRO\Translate\HasPhraseName;
use Application\DeskPRO\Translate\Translate;

/**
 * Products
 *
 */
class Product extends CategoryAbstract implements HasPhraseName
{
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $custom_data;

	/**
	 * @var \Application\DeskPRO\Entity\Product
	 */
	protected $parent;

	/**
	 * @var \Application\DeskPRO\Entity\Product
	 */
	protected $children;

	public function __construct()
	{
		$this->custom_data = new \Doctrine\Common\Collections\ArrayCollection();
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return App::getTranslator()->getPhraseObject($this, 'title');
	}


	/**
	 * @return string
	 */
	public function getRealTitle()
	{
		return $this->title;
	}

	/**
	 * Return a unique ID that we can use to look up translations for this object
	 *
	 * @param string $property If supplied, the property on the object we want to translate.
	 * @return string
	 */
	public function getPhraseName($property = null, Translate $translate)
	{
		if (!$property) {
			$property = 'title';
		}
		$phrase_name = 'obj_product.' . $this->id . '_' . $property;

		return $phrase_name;
	}


	/**
	 * Get the default value phrase for the object
	 *
	 * @param string $property If supplied, the property on the object we want to translate.
	 * @return string
	 */
	public function getPhraseDefault($property = null, Translate $translate)
	{
		if ($property == 'full') {
			return $this->getFullTitle();
		}
		return $this->title;
	}


	/**
	 * @return array
	 */
	public function getChildrenOrdered()
	{
		$children = $this->children->toArray();
		uasort($children, function($a, $b) {
			if ($a->display_order == $b->display_order) {
				return 0;
			}

			return ($a->display_order < $b->display_order) ? -1 : 1;
		});

		return $children;
	}


	/**
	 * Find an existing data record for a field id.
	 *
	 * @param int $field_id
	 * @return CustomDataProduct
	 */
	public function getCustomDataForField($field_id)
	{
		if ($field_id instanceof CustomDefProduct) {
			$field_id = $field_id['id'];
		}

		foreach ($this->custom_data as $data) {
			if ($data['field_id'] == $field_id) {
				return $data;
			}
		}

		return null;
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

			$field = App::getEntityRepository('DeskPRO:CustomDefProduct')->find($field_id);
			if (!$field) {
				throw new \Exception("Invalid field_id `$field_id`");
			}
			$custom_data = new CustomDataProduct();
			$custom_data['field'] = $field;
		}

		$field = $custom_data->field;
		if ($field->parent) {
			foreach ($this->custom_data as $d) {
				if ($d->field && $d->field->parent && $d->field->parent['id'] == $field->parent['id']) {
					$this->custom_data->removeElement($d);
				}
			}
		}

		$this->custom_data->removeElement($custom_data);

		if ($value === null) {
			$this->custom_data->removeElement($custom_data);
			return null;
		}

		if ($field->getTypeName() == 'choice') {

		}

		$custom_data[$value_type] = $value;

		if ($is_new) {
			$this->addCustomData($custom_data);
		}

		if ($this->id) {
			App::getEntityRepository('DeskPRO:Cache')->delete("product_custom_fields.{$this->id}");
		}

		return $custom_data;
	}

	public function removeCustomDataForField($field)
	{
		$parent_id = null;
		$field_id = $field['id'];
		if ($field->parent) {
			$parent_id = $field->parent['id'];
		}

		foreach ($this->custom_data as $data) {
			if ($data['field_id'] == $field_id OR $data['field_id'] == $parent_id) {
				$this->custom_data->removeElement($data);
			}
		}
	}

	/**
	 * Add a custom data item to this product
	 *
	 * @param CustomDataProduct $data
	 */
	public function addCustomData(CustomDataProduct $data)
	{
		$this->custom_data->add($data);
		$data['product'] = $this;
	}


	/**
	 * Check if this product has a custom field.
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

		foreach ($this->custom_data as $data) {
			if ($data->field->parent AND $data->field->parent['id'] == $field_id) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @return array
	 */
	public function getFieldDisplayArray()
	{
		$field_manager = App::getContainer()->getSystemService('product_fields_manager');
		return $field_manager->getDisplayArrayForObject($this);
	}



	/**
	 * @param bool $primary
	 * @param bool $deep
	 * @param array $visited
	 * @return array
	 */
	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);

		// Render custom fields to text values
		$field_manager = App::getContainer()->getSystemService('product_fields_manager');

		$values = $field_manager->getRenderedToTextForObject($this);
		foreach ($values as $fid => $v) {
			$data["field{$fid}"] = $v['rendered'];
		}

		return $data;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getFullTitle();
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Product';
		$metadata->setPrimaryTable(array( 'name' => 'products', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'display_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'display_order', ));
		$metadata->mapField(array( 'fieldName' => 'depth', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'depth', ));
		$metadata->mapField(array( 'fieldName' => 'root', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'root', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'parent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Product', 'mappedBy' => NULL, 'inversedBy' => 'children', 'joinColumns' => array( 0 => array( 'name' => 'parent_id', 'referencedColumnName' => 'id', ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'children', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Product', 'mappedBy' => 'parent',  'orderBy' => array( 'display_order' => 'ASC', ), ));
		$metadata->mapOneToMany(array( 'fieldName' => 'custom_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDataProduct', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'product', 'orphanRemoval' => true,  'dpApi' => false));
	}
}
