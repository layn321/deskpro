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
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Application\DeskPRO\Plugin\PluginPackage\AbstractPluginPackage;

/**
 * A plugin is a group of event listeners and other resources.
 *
 */
class Plugin extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var string
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * The name of the class that describes the plugin and knows how to
	 * install/uninstall etc.
	 *
	 * @var string
	 */
	protected $package_class = null;

	/**
	 * The file of the package class
	 *
	 * @var string
	 */
	protected $package_class_file = null;

	/**
	 * The path where this plugins Resources directory can be found
	 *
	 * @var string
	 */
	protected $resources_path = null;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $listeners;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var bool
	 */
	protected $enabled = true;

	/**
	 * @var array
	 * @see getService
	 */
	protected $_services = array();


	public function __construct()
	{
		$this->listeners = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * @param PluginListener $plugin_listener
	 */
	public function addPluginListener(PluginListener $plugin_listener)
	{
		$this->listeners->add($plugin_listener);
		$plugin_listener->plugin = $this;
	}

	/**
	 * @return null|string
	 */
	public function getPackageClass()
	{
		return $this->package_class;
	}

	/**
	 * @return string
	 */
	public function getCanonicalPackageClassFile()
	{
		return str_replace('%PLUGINS%', AbstractPluginPackage::getBasePluginPath(), $this->package_class_file);
	}

	/**
	 * @return string
	 */
	public function getCanonicalResourcesPath()
	{
		return str_replace('%PLUGINS%', AbstractPluginPackage::getBasePluginPath(), $this->resources_path);
	}


	/**
	 * @return void
	 */
	public function importSyncData()
	{
		$resourcePath = $this->getCanonicalResourcesPath();
		if (is_dir("$resourcePath/sync")) {
			$finder = new \Symfony\Component\Finder\Finder();
			$finder->name('*.json')->in("$resourcePath/sync");

			foreach ($finder AS $file) {
				/** @var $file \SplFileInfo */
				$handler = $file->getBasename('.json');
				$class = '\Application\DeskPRO\DataSync\Plugin\\' . $handler;
				if (class_exists($class)) {
					/** @var $sync \Application\DeskPRO\DataSync\Plugin\AbstractPlugin */
					$sync = new $class($file->getRealPath(), $this);
					$sync->syncBaseToLive();
				}
			}
		}
	}


	/**
	 * @return void
	 */
	public function exportSyncData()
	{
		$resourcePath = $this->getCanonicalResourcesPath();
		if (!is_dir("$resourcePath/sync")) {
			mkdir("$resourcePath/sync");
		}

		$finder = new \Symfony\Component\Finder\Finder();
		$finder->name('*.php')->notName('*Abstract*')->in(DP_ROOT . '/src/Application/DeskPRO/DataSync/Plugin');

		foreach ($finder AS $file) {
			/** @var $file \SplFileInfo */
			$handler = $file->getBasename('.php');
			$class = '\Application\DeskPRO\DataSync\Plugin\\' . $handler;

			/** @var $sync \Application\DeskPRO\DataSync\Plugin\AbstractPlugin */
			$sync = new $class("$resourcePath/sync/$handler.json", $this);
			$sync->writeToBase();
		}
	}


	/**
	 * Gets a plugin service
	 *
	 * @param string $id
	 */
	public function getPluginService($id)
	{
		$id = preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $id);
		if (isset($this->_services[$id])) {
			return $this->_services[$id];
		}

		$class = $this->id . '\\DependencyInjection\\' . $id . 'Service';

		if (!class_exists($class, false)) {
			$dir = dirname($this->getCanonicalPackageClassFile()) . DIRECTORY_SEPARATOR . 'DependencyInjection' . DIRECTORY_SEPARATOR . $id . 'Service.php';
			require_once($dir);
		}

		$obj = $class::create(App::getContainer(), $this);
		$this->_services[$id] = $obj;

		return $obj;
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Plugin';
		$metadata->setPrimaryTable(array( 'name' => 'plugins', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'description', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'description', ));
		$metadata->mapField(array( 'fieldName' => 'version', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'version', ));
		$metadata->mapField(array( 'fieldName' => 'package_class', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'package_class', ));
		$metadata->mapField(array( 'fieldName' => 'package_class_file', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'package_class_file', ));
		$metadata->mapField(array( 'fieldName' => 'resources_path', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'resources_path', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'enabled', ));
		$metadata->mapOneToMany(array( 'fieldName' => 'listeners', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PluginListener', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'plugin',  ));
	}
}
