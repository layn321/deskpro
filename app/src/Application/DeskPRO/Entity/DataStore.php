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

use Orb\Util\Strings;
use Orb\Util\Util;

/**
 * A general data store
 */
class DataStore extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * A string name to uniquely identify the record
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * The authcode to possibly verify with
	 *
	 * @var string
	 */
	protected $auth;

	/**
	 * Data
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * @param string $type
	 * @param array $data
	 * @return \Application\DeskPRO\Entity\TmpData
	 */
	public static function create($type, array $data = array())
	{
		$ds = new self();
		$ds->setType($type);

		foreach ($data as $k => $v) {
			$ds->setData($k, $v);
		}

		return $ds;
	}

	public function __construct()
	{
		$this->auth = Strings::random(15, Strings::CHARS_KEY);
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->getData('_type');
	}


	/**
	 * Set the type
	 *
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->setData('_type', $type);
	}


	/**
	 * Get some data from the extra array
	 */
	public function getData($key, $default = null)
	{
		return (isset($this->data[$key]) ? $this->data[$key] : $default);
	}


	/**
	 * Set some data on the extra array.
	 *
	 * @param  $key
	 * @param  $value
	 * @return void
	 */
	public function setData($key, $value)
	{
		$old = $this->data;
		if ($value === null) {
			unset($this->data[$key]);
		} else {
			$this->data[$key] = $value;
		}

		$this->_onPropertyChanged('data', $old, $this->data);
	}


	/**
	 * @return string
	 */
	public function getCode()
	{
		return Util::baseEncode($this->id, Util::LETTERS_ALPHABET) . '-' . $this->auth;
	}


	/**
	 * Splits a code into its id and auth
	 *
	 * @param  $code
	 * @return array
	 */
	public static function getPartsFromCode($code)
	{
		$parts = explode('-', $code, 2);
		if (count($parts) != 2) return null;

		$parts[0] = Util::baseDecode($parts[0], Util::LETTERS_ALPHABET);

		return array(
			'id' => $parts[0],
			'auth' => $parts[1],
		);
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\DataStore';
		$metadata->setPrimaryTable(array(
			'name' => 'datastore',
			'indexes' => array(
				'name_idx' => array('columns' => array('name'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'auth', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'auth', ));
		$metadata->mapField(array( 'fieldName' => 'data', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'data', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
