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
 * @subpackage
 */

namespace Application\AdminBundle\Controller;

class UpgradeController extends AbstractController
{
	public function startAction()
	{
		$waiting = $this->container->getSetting('core.upgrade_time');
		if ($waiting) {
			return $this->redirectRoute('admin_upgrade_watch');
		}

		$version_info = new \Orb\Util\OptionsArray(\Application\DeskPRO\Service\LicenseService::compareVersion());

		if ($this->in->getBool('start') && $version_info['count_behind']) {
			$mins = $this->in->getUint('minutes');
			if (!$mins) $mins = 0;

			$future = time() + $mins * 60;
			$this->container->getSettingsHandler()->setSetting('core.upgrade_time', $future);
			$this->container->getSettingsHandler()->setSetting('core.upgrade_set_at', time());
			$this->container->getSettingsHandler()->setSetting('core.upgrade_backup_files', $this->in->getInt('backup_files'));
			$this->container->getSettingsHandler()->setSetting('core.upgrade_backup_db', $this->in->getInt('backup_db'));
			$this->container->getSettingsHandler()->setSetting('core.upgrade_error_writeperm', null);
			$this->container->getSettingsHandler()->setSetting('core.upgrade_started', null);

			$this->container->getSettingsHandler()->setSetting('core.helpdesk_disabled_message', $this->in->getString('user_message'));
			@file_put_contents(dp_get_data_dir() . '/helpdesk-offline-message.txt', $this->in->getString('user_message'));

			if ($mins) {
				$agent_chat = new \Application\DeskPRO\Chat\AgentChat($this->person, $this->session->getEntity());
				$agent_ids = array_keys($this->em->getRepository('DeskPRO:Person')->getAgents());
				$agent_chat->sendAgentMessage("Warning: The helpdesk will go down for maintenance in 5 minutes.", $agent_ids, 0);
			}

			return $this->redirectRoute('admin_upgrade_watch');
		}

		return $this->render('AdminBundle:Upgrade:start.html.twig', array(
			'version_info' => $version_info,
			'current_version' => DP_BUILD_TIME
		));
	}

	public function checkStartedAction()
	{
		if ($this->container->getSetting('core.upgrade_started') || $this->container->getSetting('core.last_auto_upgrade_time') >= $this->in->getInt('start_time')) {
			return $this->createJsonResponse(array('started' => true));
		} elseif ($this->container->getSetting('core.upgrade_error_writeperm')) {
			return $this->createJsonResponse(array('write_perm_error' => true));
		} else {
			return $this->createJsonResponse(array('waiting' => true));
		}
	}

	public function watchAction()
	{
		$waiting = $this->container->getSetting('core.upgrade_time');
		if (!$waiting) {
			return $this->redirectRoute('admin_upgrade');
		}

		$config_hash = md5_file(DP_CONFIG_FILE);

		return $this->render('AdminBundle:Upgrade:watch.html.twig', array(
			'backup_path' => dp_get_backup_dir(),
			'config_hash' => $config_hash,
			'is_wincache' => extension_loaded('wincache')
		));
	}

	public function stopAction()
	{
		$waiting = $this->container->getSetting('core.upgrade_time');
		if (!$waiting) {
			return $this->redirectRoute('admin_upgrade');
		}

		// To late now
		$file = @file_get_contents(DP_WEB_ROOT . ' /auto-update-status.php');
		if ($file && strpos($file, 'STATUS(start)') !== null) {
			return $this->redirectRoute('admin_upgrade_watch');
		}

		$this->container->getSettingsHandler()->setSetting('core.upgrade_time', null);
		$this->container->getSettingsHandler()->setSetting('core.upgrade_set_at', null);
		$this->container->getSettingsHandler()->setSetting('core.upgrade_backup_files', null);
		$this->container->getSettingsHandler()->setSetting('core.upgrade_backup_db', null);
		return $this->redirectRoute('admin_upgrade');
	}
}