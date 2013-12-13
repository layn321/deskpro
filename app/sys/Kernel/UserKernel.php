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
 */

namespace DeskPRO\Kernel;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

use Application\DeskPRO\App;

class UserKernel extends AbstractKernel
{
	protected function registerAdditionalBundles()
	{
		$bundles = array(
			new \Application\AgentBundle\AgentBundle(), // so templates can work when notiying
		);

		return $bundles;
	}

	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		$loader->load(DP_ROOT.'/sys/config/user/config_'.$this->getEnvironment().'.php');
	}

	protected function preResponseHandled(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
	{
		if (
			!preg_match('#^/widget/#', $request->getPathInfo())
			&& !preg_match('#^/chat/#', $request->getPathInfo())
			&& !preg_match('#^/tickets/new-simple#', $request->getPathInfo())
			&& !preg_match('#^/tickets/new/thanks-simple/#', $request->getPathInfo())
			&& !preg_match('#^/accept-temp-upload$#', $request->getPathInfo())
			&& !preg_match('#^/logout#', $request->getPathInfo())
			&& !preg_match('#^/login#', $request->getPathInfo())
		) {
			try {
				if (!App::getSetting('user.portal_enabled')) {
					$response = new \Symfony\Component\HttpFoundation\Response('<!-- Portal Offline -->');
					return $response;
				}
			} catch (\Exception $e) {}
		}

		return parent::preResponseHandled($request, $type, $catch);
	}

	protected function shouldApplyUrlCorrections(Request $request)
	{
		// Dont auto-redirect these URLs that are used
		// in widgets and callbacks
		if (
			!preg_match('#^/widget/#', $request->getPathInfo())
			&& !preg_match('#^/chat/#', $request->getPathInfo())
			&& !preg_match('#^/tickets/new-simple#', $request->getPathInfo())
			&& !preg_match('#^/tickets/new/thanks-simple/#', $request->getPathInfo())
			&& !preg_match('#^/accept-temp-upload$#', $request->getPathInfo())
			&& !preg_match('#^/logout#', $request->getPathInfo())
			&& !preg_match('#^/login#', $request->getPathInfo())
		) {
			return false;
		}

		return true;
	}
}