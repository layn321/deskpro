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

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Plugin\PluginFinder;
use Application\DeskPRO\Entity\Plugin;

class PluginsController extends AbstractController
{
	public function runAction($plugin_id, $action)
	{
		$plugin = $this->_getPluginOr404($plugin_id);
		$plugin_info = $this->_getPluginInfoOr404($plugin_id);

		return $plugin_info->runAgentAction($this, $action, $plugin);
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
