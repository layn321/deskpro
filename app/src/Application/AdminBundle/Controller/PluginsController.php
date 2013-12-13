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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Plugin\PluginFinder;
use Application\DeskPRO\Entity\Plugin;

class PluginsController extends AbstractController
{
	############################################################################
	# index
	############################################################################

	/**
	 * List installed plugins and available plugin
	 */
	public function listAction()
	{
		$finder = new PluginFinder();
		$available_plugins = $finder->findPlugins();

		$installed_plugins = $this->_getPluginRepository()->getInstalled();
		$installed_plugin_info = array();

		foreach (array_keys($installed_plugins) as $plugin_id) {
			if (isset($available_plugins[$plugin_id])) {
				$installed_plugin_info[$plugin_id] = $available_plugins[$plugin_id];
				unset($available_plugins[$plugin_id]);
			}
		}
		foreach ($available_plugins AS $plugin_id => $info) {
			if (!$info['is_available']) {
				unset($available_plugins[$plugin_id]);
			}
		}

		return $this->render('AdminBundle:Plugins:list.html.twig', array(
			'available_plugins' => $available_plugins,
			'installed_plugins' => $installed_plugins,
			'installed_plugin_info' => $installed_plugin_info
		));
	}

	public function configAction($plugin_id)
	{
		$plugin = $this->_getPluginOr404($plugin_id);
		$plugin_info = $this->_getPluginInfoOr404($plugin_id);

		$errors = array();

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken();

			$redirect = $plugin_info->processConfig($this, $plugin, $errors);
			if ($redirect) {
				if (is_string($redirect)) {
					return $this->redirect($redirect);
				} else {
					return $this->redirectRoute('admin_plugins');
				}
			}
		}

		return $plugin_info->renderConfig($this, $plugin, $errors);
	}

	public function runAction($plugin_id, $action)
	{
		$plugin = $this->_getPluginOr404($plugin_id);
		$plugin_info = $this->_getPluginInfoOr404($plugin_id);

		return $plugin_info->runAdminAction($this, $action, $plugin);
	}

	public function toggleAction()
	{
		$this->ensureRequestToken();

		$ids = $this->in->getArray('plugins');
		list($plugin_id, $enabled) = each($ids);

		$plugin = $this->_getPluginOr404($plugin_id);
		$plugin_info = $this->_getPluginInfoOr404($plugin_id);

		$plugin->enabled = (bool)$enabled;
		$this->em->persist($plugin);
		$this->em->flush();

		return $this->createJsonResponse(array(
			'ok' => 1
		));
	}


	############################################################################
	# install
	############################################################################

	/**
	 * Installs a plugin
	 */
	public function installAction($plugin_id, $step = 1)
	{
		$plugin_info = $this->_getPluginInfoOr404($plugin_id);

		$plugin = new \Application\DeskPRO\Entity\Plugin();
		$plugin['id']                     = $plugin_info->getName();
		$plugin['title']                  = $plugin_info->getTitle();
		$plugin['description']            = $plugin_info->getDescription();
		$plugin['version']                = $plugin_info->getVersion();
		$plugin['package_class']          = get_class($plugin_info);
		$plugin['package_class_file']     = $plugin_info->getFile();
		$plugin['resources_path']         = $plugin_info->getResourcesPath();

		$this->container->get('deskpro.plugin_manager')->addPlugin($plugin);

		$installer = $plugin_info->getInstaller($this, $plugin);
		return $installer->runStep($step);
	}


	############################################################################
	# uninstall
	############################################################################

	/**
	 * Installs a plugin
	 */
	public function uninstallAction($plugin_id)
	{
		$plugin = $this->_getPluginOr404($plugin_id);

		$finder = new PluginFinder();
		$plugin_info = $finder->getPluginInfo($plugin_id);

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken();

			$this->container->get('deskpro.plugin_manager')->initialize();

			if ($plugin_info) {
				$uninstaller = $plugin_info->getUninstaller($this, $plugin);
			} else {
				$uninstaller = new \Application\DeskPRO\Plugin\PluginPackage\UninstallerSimple($plugin, $this);
			}

			return $uninstaller->runStep(1);
		}

		return $this->render('AdminBundle:Plugins:uninstall.html.twig', array(
			'plugin' => $plugin,
			'info' => $plugin_info
		));
	}

	/**
	 * @param string $id
	 *
	 * @return \Application\DeskPRO\Entity\Plugin
	 */
	protected function _getPluginOr404($id)
	{
		$data = $this->_getPluginRepository()->find($id);
		if (!$data) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no plugin with ID $id");
		}

		return $data;
	}

	/**
	 * @param string $id
	 *
	 * @return \Application\DeskPRO\Plugin\PluginPackage\AbstractPluginPackage
	 */
	protected function _getPluginInfoOr404($id)
	{
		$finder = new PluginFinder();
		$data = $finder->getPluginInfo($id);
		if (!$data) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no plugin with ID $id");
		}

		return $data;
	}

	/**
	 * @return \Application\DeskPRO\EntityRepository\Plugin
	 */
	protected function _getPluginRepository()
	{
		return $this->em->getRepository('DeskPRO:Plugin');
	}
}
