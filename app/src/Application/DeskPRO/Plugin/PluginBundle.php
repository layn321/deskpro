<?php

namespace Application\DeskPRO\Plugin;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\SwiftmailerBundle\DependencyInjection\Compiler\RegisterPluginsPass;

class PluginBundle extends Bundle
{
	protected $_plugin;
	protected $_package;
	protected $_reflectedPackage;

	public function __construct(\Application\DeskPRO\Entity\Plugin $plugin)
	{
		$this->_plugin = $plugin;

		$this->_package = new $plugin['package_class'];

		$name = get_class($this->_package);
		$pos = strrpos($name, '\\');

		$this->name = false === $pos ? $name :  substr($name, $pos + 1);
	}

	public function getNamespace()
	{
		if (null === $this->_reflectedPackage) {
			$this->_reflectedPackage = new \ReflectionObject($this->_package);
		}

		return $this->_reflectedPackage->getNamespaceName();
	}

	/**
	 * Gets the Bundle directory path.
	 *
	 * @return string The Bundle absolute path
	 *
	 * @api
	 */
	public function getPath()
	{
		if (null === $this->_reflectedPackage) {
			$this->_reflectedPackage = new \ReflectionObject($this->_package);
		}

		return dirname($this->_reflectedPackage->getFileName());
	}

	/**
	 * Returns the bundle parent name.
	 *
	 * @return string The Bundle parent name it overrides or null if no parent
	 *
	 * @api
	 */
	public function getParent()
	{
		return null;
	}
}