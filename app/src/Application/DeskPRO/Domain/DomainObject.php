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

namespace Application\DeskPRO\Domain;

use Application\DeskPRO\App;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;

use Orb\Util\Util;

/**
 * The basic entity class
 */
abstract class DomainObject extends BasicDomainObject
{
	const API_MODE_OPT_OUT = 1;
	const API_MODE_OPT_IN = 2;

	protected $_api_mode = self::API_MODE_OPT_OUT;

	/**
	 * Causes error to be logged when the object is persisted.
	 *
	 * @var bool
	 */
	private $_no_persist = false;

	public $_presave_state = array();


	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	public static function getRepository()
	{
		$entity = get_called_class();
		$entity = explode('\\', $entity);
		$entity = array_pop($entity);

		$em = App::getOrm();

		return $em->getRepository("DeskPRO:$entity");
	}


	/**
	 * Get the table name for this entity
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return App::getOrm()->getClassMetadata(get_called_class())->getTableName();
	}


	/**
	 * @return string
	 */
	public static function getEntityName()
	{
		$name = Util::getBaseClassname(get_called_class());
		if (preg_match('#^ApplicationDeskPROEntity(.*?)Proxy$#', $name, $m)) {
			$name = $m[1];
		}

		$name = 'DeskPRO:' . $name;

		return $name;
	}


	/**
	 * Get an object ref for this entity. This is the table name and the entity ID.
	 * For example, "tickets.1234"
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	public function getObjectRef()
	{
		if (method_exists($this, 'getId')) {
			return $this->getTableName() . '.' . $this->getId();
		} elseif (method_exists($this, 'getRef')) {
			return $this->getTableName() . '.' . $this->getRef();
		} else {
			throw new \RuntimeException("Object does not implement getObjectRef");
		}
	}


	/**
	 * Sets the value of a field, and calls the property changed tracker
	 *
	 * @param $field
	 * @param $value
	 * @return void
	 */
	protected function setModelField($field, $value)
	{
		$old = $this->$field;

		// Detect fields that did not change
		if (is_null($value) && is_null($old)) {
			return;
		} elseif (is_scalar($value)) {
			if ($value == $old) {
				return;
			}
		} elseif ($value instanceof \DateTime) {
			if ($old instanceof \DateTime && $value->getTimestamp() == $old->getTimestamp()) {
				return;
			}
		} elseif (is_object($value) && isset($value->id) && is_object($old) && isset($old->id)) {
			if ($value->id == $old->id) {
				return;
			}
		}

		$this->$field = $value;

		$this->_onPropertyChanged($field, $old, $value);
	}


	/**
	 * Sets a model field value but does not mark it as changed so it wont be persisted.
	 *
	 * @param string $field
	 * @param mixed $value
	 */
	public function setUntrackedModelField($field, $value)
	{
		$this->$field = $value;
	}


	/**
	 * @param bool $primary
	 * @param bool $deep
	 * @param array $visited
	 * @return array
	 */
	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$repository = static::getRepository();
		if (!method_exists($repository, 'getFieldMappings')) {
			return array();
		}

		$values = array();
		$visited[] = $this;

		foreach ($repository->getFieldMappings() AS $name => $field) {
			if ($this->_api_mode == self::API_MODE_OPT_IN && empty($field['dpApi'])) {
				continue;
			} elseif ($this->_api_mode == self::API_MODE_OPT_OUT && isset($field['dpApi']) && !$field['dpApi']) {
				continue;
			}

			if (!empty($field['dpApiPrimary']) && !$primary) {
				continue;
			}

			$val = $this[$name];

			if ($val instanceof \DateTime) {
				$values[$name] = $val->format('Y-m-d H:i:s');
				$values["{$name}_ts"] = $val->getTimestamp();
				$values["{$name}_ts_ms"] = $val->getTimestamp() * 1000;
			} else {
				$values[$name] = $val;
			}
		}

		if ($deep) {
			foreach ($repository->getAssociationMappings() AS $name => $association) {
				if (empty($association['dpApi'])) {
					continue;
				}

				if (!empty($association['dpApiPrimary']) && !$primary) {
					continue;
				}

				$val = $this[$name];

				$subDeep = !empty($association['dpApiDeep']);
				if (in_array($val, $visited)) {
					$subDeep = false;
				}

				if ($val instanceof DomainObject) {
					$values[$name] = $val->toApiData(false, $subDeep, $visited);
				} else if (is_array($val) || $val instanceof \Traversable) {
					$output = array();

					foreach ($val AS $key => $sub) {
						if ($sub instanceof \Application\DeskPRO\Domain\DomainObject) {
							$output[$key] = $sub->toApiData(false, $subDeep, $visited);
						}
					}

					$values[$name] = $output;
				}
			}
		}

		return $values;
	}


	/**
	 * @return array
	 */
	public function getScalarData()
	{
		$repository = static::getRepository();
		if (!method_exists($repository, 'getFieldMappings')) {
			return array();
		}

		$values = array();

		foreach ($repository->getFieldMappings() AS $name => $field) {
			$val = $this[$name];

			if ($val instanceof \DateTime) {
				$values[$name] = $val->format('Y-m-d H:i:s');
			} else if (is_array($val)) {
				$values[$name] = serialize($val);
			} else {
				$values[$name] = $val;
			}
		}

		return $values;
	}


	/**
	 * Sets the special no persist flag that causes an error if this object is persisted
	 */
	public function _setNoPersist()
	{
		$this->_no_persist = true;
	}


	/**
	 * Check the current status of the no persist flag
	 * @return bool
	 */
	public function _isNoPersist()
	{
		return $this->_no_persist;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		$me = get_class($this);
		$me = explode('\\', $me);
		$me = array_pop($me);

		if (property_exists($this, 'id')) {
			if ($this->id) {
				return "<$me:#" . $this->id . ">";
			} else {
				return "<$me:#0:" . spl_object_hash($this) . ">";
			}
		} else {
			return "<$me:" . spl_object_hash($this) . ">";
		}
	}
}
