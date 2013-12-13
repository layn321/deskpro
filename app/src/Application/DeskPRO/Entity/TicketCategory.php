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
 * Ticket categories
 *
 */
class TicketCategory extends \Application\DeskPRO\Domain\DomainObject implements HasPhraseName
{
	/**
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var TicketCategory
	 */
	protected $parent = null;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $children = null;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var int
	 */
	protected $display_order = 0;

	public function __construct()
	{
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getParentId()
	{
		if ($this->parent) {
			return $this->parent['id'];
		}

		return 0;
	}

	public function setParentId($id)
	{
		if ($id) {
			$this->setModelField('parent', App::getEntityRepository('DeskPRO:Department')->find($id));
		} else {
			$this->setModelField('parent', null);
		}
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
	 * Get the 'full' name
	 *
	 * @return string
	 */
	public function getFullTitle($sep = null)
	{
		if ($sep === null) $sep = ' > ';

		if (!$this->parent) {
			return $this->getTitle();
		}

		return $this->parent->getTitle() . $sep . $this->getTitle();
	}


	/**
	 * Add a child department
	 * @param Department $department
	 */
	public function addChild(TicketCategory $department)
	{
		$department['parent'] = $this;
		$this->children->add($department);
	}


	/**
	 * Get children
	 * @return Doctrine\Common\Collections\ArrayCollection
	 */
	public function getChildren()
	{
		if ($this->parent) {
			// empty collection
			return new \Doctrine\Common\Collections\ArrayCollection();
		}

		return $this->children;
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
	 * Get all children down the entire tree
	 *
	 * @return array
	 */
	public function getAllChildren()
	{
		return $this->getChildren();
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
		$phrase_name = 'obj_ticketcategory.' . $this->id . '_' . $property;

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
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketCategory';
		$metadata->setPrimaryTable(array( 'name' => 'ticket_categories', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'display_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'display_order', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'parent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketCategory', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'parent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'children', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketCategory', 'mappedBy' => 'parent',  'orderBy' => array( 'display_order' => 'ASC', ), ));
	}
}
