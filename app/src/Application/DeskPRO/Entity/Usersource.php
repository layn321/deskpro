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

/**
 * Defines information about an external user source
 */
class Usersource extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 */
	protected $id = null;

	/**
	 * The title of this usersource
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * The type of usersource this is. This maps to an adapter class.
	 *
	 * @var string
	 */
	protected $source_type;

	/**
	 * @var string
	 */
	protected $lost_password_url = '';

	/**
	 * Options we'll pass to the adapter. These options should be set up with some installer.
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * The order in which to display this source
	 * @var int
	 */
	protected $display_order = 0;

	/**
	 * True if this usersource is enabled/usable.
	 *
	 * @var bool
	 */
	protected $is_enabled = true;

	/**
	 * @var \Application\DeskPRO\Entity\UsersourcePlugin|null
	 */
	protected $usersource_plugin = null;

	/**
	 * @var \Application\DeskPRO\Usersource\Adapter\AbstractAdapter
	 */
	protected $_adapter_instance = null;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the usersource adapter for this usersource.
	 *
	 * @return \Application\DeskPRO\Usersource\Adapter\AbstractAdapter
	 */
	public function getAdapter()
	{
		if ($this->_adapter_instance !== null) {
			return $this->_adapter_instance;
		}

		if (!$this->usersource_plugin) {
			$classname = 'Application\\DeskPRO\\Usersource\\Adapter\\' . $this->getTypeName();
		} else {
			$classname = $this->usersource_plugin->adapter_class;
		}
		if (!$classname || !class_exists($classname)) {
			throw new \RuntimeException("Unknown usersource type `$classname`");
		}

		$this->_adapter_instance = new $classname($this);

		return $this->_adapter_instance;
	}

	public function __call($name, $args)
	{
		return call_user_func_array(array($this->getAdapter(), $name), $args);
	}

	public function hasOption($name)
	{
		return isset($this->options[$name]);
	}

	public function getOption($name, $default = null)
	{
		return isset($this->options[$name]) ? $this->options[$name] : $default;
	}

	public function setOption($name, $value)
	{
		$old = $this->options;
		$this->options[$name] = $value;
		$this->_onPropertyChanged('options', $old, $this->options);
	}

	public function setOptions(array $options, $reset = false)
	{
		$old = $this->options;

		if ($reset) {
			$this->options = $options;
		} else {
			$this->options = array_merge($this->options, $options);
		}

		$this->_onPropertyChanged('options', $old, $this->options);
	}


	/**
	 * @return string
	 */
	public function getTypeName()
	{
		return ucfirst(Strings::underscoreToCamelCase($this->source_type));
	}

	public function getFormType()
	{
		if (!$this->usersource_plugin) {
			$type_name = $this->getTypeName();
			$class = 'Application\\AdminBundle\\Form\\Usersource\\Type\\' . $type_name . 'Type';
		} else {
			$class = $this->usersource_plugin->form_type_class;
		}

		return new $class();
	}

	public function getFormModel()
	{
		if (!$this->usersource_plugin) {
			$type_name = $this->getTypeName();
			$class = 'Application\\AdminBundle\\Form\\Usersource\\Model\\' . $type_name . 'Model';
		} else {
			$class = $this->usersource_plugin->form_model_class;
		}

		return new $class($this);
	}

	public function getFormTemplate()
	{
		if (!$this->usersource_plugin) {
			$template = 'AdminBundle:UserReg:usersource-edit-' . $this->source_type . '.html.twig';
		} else {
			$template = $this->usersource_plugin->form_template;
		}

		return $template;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Usersource';
		$metadata->setPrimaryTable(array( 'name' => 'usersources', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'source_type', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'source_type', ));
		$metadata->mapField(array( 'fieldName' => 'lost_password_url', 'type' => 'string', 'length' => 1000, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'lost_password_url', ));
		$metadata->mapField(array( 'fieldName' => 'options', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'options', ));
		$metadata->mapField(array( 'fieldName' => 'display_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'display_order', ));
		$metadata->mapField(array( 'fieldName' => 'is_enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_enabled', ));

		$metadata->mapManyToOne(array( 'fieldName' => 'usersource_plugin', 'targetEntity' => 'Application\\DeskPRO\\Entity\\UsersourcePlugin', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'usersource_plugin_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}

	/**
	 * @param int $display_order
	 */
	public function setDisplayOrder($display_order)
	{
		$this->setModelField('display_order', $display_order);
	}

	/**
	 * @return int
	 */
	public function getDisplayOrder()
	{
		return $this->display_order;
	}

	/**
	 * @param boolean $is_enabled
	 */
	public function setIsEnabled($is_enabled)
	{
		$this->setModelField('is_enabled', $is_enabled);
	}

	/**
	 * @return boolean
	 */
	public function getIsEnabled()
	{
		return $this->is_enabled;
	}

	/**
	 * @param string $lost_password_url
	 */
	public function setLostPasswordUrl($lost_password_url)
	{
		$this->setModelField('lost_password_url', $lost_password_url);
	}

	/**
	 * @return string
	 */
	public function getLostPasswordUrl()
	{
		return $this->lost_password_url;
	}

	/**
	 * @param string $source_type
	 */
	public function setSourceType($source_type)
	{
		$this->setModelField('source_type', $source_type);
	}

	/**
	 * @return string
	 */
	public function getSourceType()
	{
		return $this->source_type;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->setModelField('title', $title);
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
}
