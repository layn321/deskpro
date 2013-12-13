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

use Application\DeskPRO\App;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class ObjectLang extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $ref;

	/**
	 * @var string
	 */
	protected $ref_type;

	/**
	 * @var string
	 */
	protected $ref_id;

	/**
	 * @var Language
	 */
	protected $language;

	/**
	 * @var string
	 */
	protected $prop_name;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var object
	 */
	protected $_set_object;

	/**
	 * Create a new lang object
	 *
	 * @param Language|int  $lang      The lang ID of a lang or the lang itself
	 * @param object        $object    The domain object to set the lang for. This is any object that has getObjectRef
	 * @param string        $prop_name  The property ID of the thing we are translating
	 * @param string        $value     The value ID of the thing we are translating
	 * @return \Application\DeskPRO\Entity\ObjectLang
	 * @throws \InvalidArgumentException
	 */
	public static function createObjectLang($lang, $object, $prop_name, $value)
	{
		if ($lang === 0 || $lang === null || $lang === 'default') {
			$lang = App::getContainer()->getDataService('language')->getDefault();
		} else if (!is_object($lang)) {
			$lang = App::getContainer()->getDataService('language')->get($lang);
		}

		if (!$lang || !($lang instanceof Language)) {
			throw new \InvalidArgumentException("Invalid language");
		}

		$ol = new self();
		if (is_object($object)) {
			$ol->setObject($object);
		} else {
			$ol->setRef($object);
		}

		$ol->setPropName($prop_name);
		$ol->setValue($value);
		$ol->setLanguage($lang);

		return $ol;
	}


	/**
	 * @param object $object
	 */
	public function setObject($object)
	{
		$this->_set_object = $object;
		$this->setRef($object->getObjectRef());
	}


	/**
	 * @param string $ref
	 */
	public function setRef($ref)
	{
		$this->setModelField('ref', $ref);

		if (strpos($ref, '.') !== false) {
			list ($type, $id) = explode('.', $ref, 2);
			$this->setModelField('ref_type', $type);
			$this->setModelField('ref_id', $id);
		} else {
			$this->setModelField('ref_type', null);
			$this->setModelField('ref_id', null);
		}
	}


	/**
	 * @param string $prop_name
	 */
	public function setPropName($prop_name)
	{
		$this->setModelField('prop_name', strtolower($prop_name));
	}

	public function _resetRefCode()
	{
		if ($this->_set_object) {
			$this->setModelField('ref', $this->_set_object->getObjectRef());
			$this->_set_object = null;
		}
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array(
			'name' => 'object_lang',
			'indexes' => array(
				'prop_ref_type' => array('columns' => array('ref_type', 'ref_id'))
			),
			'uniqueConstraints' => array(
				'prop_ref' => array('columns' => array('ref', 'prop_name', 'language_id'))
			)
		));
		$metadata->addLifecycleCallback('_resetRefCode', 'prePersist');
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'ref', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ref', ));
		$metadata->mapField(array( 'fieldName' => 'ref_type', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'ref_type', ));
		$metadata->mapField(array( 'fieldName' => 'ref_id', 'type' => 'integer', 'nullable' => true, 'columnName' => 'ref_id', ));
		$metadata->mapField(array( 'fieldName' => 'prop_name', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'prop_name', ));
		$metadata->mapField(array( 'fieldName' => 'value', 'type' => 'text', 'nullable' => false, 'columnName' => 'value', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'language', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Language', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'language_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}