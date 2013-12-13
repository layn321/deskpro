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

namespace Cloud\AdminBundle\Controller;

use Application\AdminBundle\Controller\SettingsController as BaseSettingsController;

class SettingsController extends BaseSettingsController
{
	/**
	 * @var string
	 */
	protected $old_domain = null;

	public function settingsAction()
	{
		$this->old_domain = $this->container->getSetting('core.cloud_custom_domain');
		return parent::settingsAction();
	}

	public function setCustomDomainAction()
	{
		$this->old_domain = $this->container->getSetting('core.cloud_custom_domain');

		$custom_domain = strtolower($this->in->getString('custom_domain'));
		$custom_domain = preg_replace('#^www\.#', '', $custom_domain);

		$update_settings = array(
			'core.cloud_custom_domain' => $custom_domain ?: null,
			'core.deskpro_url' => 'http://' . $custom_domain . '/',
		);

		foreach ($update_settings as $k => $v) {
			$this->em->getRepository('DeskPRO:Setting')->updateSetting($k, $v);
		}
		$this->container->getSettingsHandler()->setTemporarySettingValues($update_settings);

		$this->_postSaveSettings();
		return $this->createJsonResponse(array('success' => true));
	}

	protected function _postSaveSettings()
	{
		$url_type = $this->in->getString('cloud_domain');

		if ($url_type == 'default') {
			if ($this->in->getString('cloud_domain_ssl') == 'https') {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.deskpro_url', 'https://' . DPC_SITE_DOMAIN . '/');
			} else {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.deskpro_url', 'http://' . DPC_SITE_DOMAIN . '/');
			}

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.cloud_custom_domain', null);
			$this->container->getSettingsHandler()->setTemporarySettingValues(array('core.cloud_custom_domain' => null));
		} else {
			$new_domain = strtolower($this->container->getSetting('core.cloud_custom_domain'));
			if ($new_domain && (!preg_match('#^[a-z0-9\-\.]+$#', $new_domain) || preg_match('#\.deskpro\.com$#', $new_domain))) {
				$new_domain = '';
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.cloud_custom_domain', null);
				$this->container->getSettingsHandler()->setTemporarySettingValues(array('core.cloud_custom_domain' => null));
			}

			if ($new_domain != $this->old_domain) {
				$set = $new_domain;

				$tmpdata = new \Application\DeskPRO\Entity\TmpData();
				$tmpdata->setType('dpc_set_domain');
				$tmpdata->setData('by_person', $this->person->getId());
				$tmpdata->setData('set_domain', $set);
				$tmpdata->date_expire = new \DateTime('+30 minutes');

				$this->em->persist($tmpdata);
				$this->em->flush();

				$url = DP_MA_SERVER . '/cloud/call/'.DPC_SITE_ID.'/'. $tmpdata->getCode();

				try {
					$client = new \Zend\Http\Client(null, array('timeout' => 10));
					$client->setMethod(\Zend\Http\Request::METHOD_GET);
					$client->setUri($url);
					$r = $client->send();
				} catch (\Exception $e) {
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
				}

				if ($new_domain) {
					$this->container->getSettingsHandler()->setSetting('core.deskpro_url', 'http://' . $new_domain . '/');
				}
			}

			// Make sure the master domain is set correctly if the custom domain was removed
			if (!$new_domain) {
				$master_domain = @parse_url($this->container->getSetting('core.deskpro_url'));
				if ($master_domain && $master_domain['host'] == $this->old_domain) {
					$this->container->getSettingsHandler()->setSetting('core.deskpro_url', 'http://' . DPC_SITE_DOMAIN . '/');
				}
			}
		}
	}

	public function changeCustomDomains()
	{

	}
}
