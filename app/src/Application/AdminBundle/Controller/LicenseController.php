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

use DeskPRO\Kernel\License;
use Application\DeskPRO\Entity;
use Application\DeskPRO\App;

use Application\AdminBundle\Form\EditAgentType;
use Application\AdminBundle\FormModel as AdminFormModel;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

use Symfony\Component\Form;

class LicenseController extends AbstractController
{
	protected function init()
	{
		parent::init();
		License::getLicense();
	}

	############################################################################
	# request-demo
	############################################################################

	public function requestDemoAction()
	{
		$errors = array();
		$email_address = $this->in->getString('email_address');
		if ($this->in->getBool('process')) {

			$set_website_name = $this->container->getSetting('core.site_name');
			$set_website_url = $this->container->getSetting('core.site_url');
			if ($this->in->getString('website_name')) {
				if (!$set_website_name) {
					$this->container->get('deskpro.core.settings')->setSetting('core.site_name', $set_website_name);
				}
				$set_website_name = $this->in->getString('website_name');
			}
			if ($this->in->getString('website_url')) {
				if (!$set_website_url) {
					$this->container->get('deskpro.core.settings')->setSetting('core.site_url', $set_website_url);
				}
				$set_website_url = $this->in->getString('website_url');
			}

			if (!$email_address || !\Orb\Validator\StringEmail::isValueValid($email_address)) {
				$errors['email'] = true;
			}

			if (!$set_website_name) {
				$errors['site_name'] = true;
			}
			if (!$set_website_url) {
				$errors['site_url'] = true;
			}

			if (!$errors) {
				$client = new \Zend\Http\Client(null, array('timeout' => 15, 'strictredirects' => true));
				$client->setMethod(\Zend\Http\Request::METHOD_POST);
				$client->setUri(DP_MA_SERVER . '/api/license/request-demo.json');

				$data = array(
					'install_key'   => $this->settings->get('core.install_key'),
					'install_token' => $this->settings->get('core.install_token'),
					'email_address' => $email_address,
					'org_name'      => $set_website_name,
					'org_url'       => $set_website_url,
					'url'           => $this->request->getUriForPath('/'),
				);

				$instance_data = $this->container->getSysConfig('instance_data.report');
				if ($instance_data) {
					$data = array_merge($instance_data, $data);
				}

				$client->getRequest()->post()->fromArray($data);

				$hostname = gethostname();

				if ($hostname) {
					$client->getRequest()->post()->set('hostname', $hostname);

					$ip_address = gethostbyname($hostname);
					if ($ip_address) {
						$client->getRequest()->post()->set('ip_address', $ip_address);
					}
				}

				$failed = false;
				try {
					$result = $client->send();

					if ($result->isServerError()) {
						$failed = 'server_error';
					} elseif ($result->isClientError()) {
						$failed = true;
					} else {
						$data = @json_decode($result->getBody(), true);
						if (!$data) {
							$failed = 'server_error';
						} else {
							if (isset($data['error'])) {
								if ($data['error_code']) {
									$errors['email'] = true;
								} else {
									$errors['request_error'] = $data['error_code'];
								}
							} else {
								if ($this->request->isXmlHttpRequest()) {
									return $this->createJsonResponse(array(
										'success' => true
									));
								}
								return $this->redirectRoute('admin', array('from_demo' => 1));
							}
						}
					}

				} catch (\Zend\Http\Client\Adapter\Exception $e) {
					if ($e->getCode() == \Zend\Http\Client\Adapter\Exception\TimeoutException::READ_TIMEOUT) {
						$failed = 'timeout';
					} else {
						$failed = true;
					}
				} catch (\Exception $e) {
					$failed = true;
				}

				if ($failed) {
					if ($failed === true) {
						$errors['unknown_request_error'] = true;
					} else {
						$errors[$failed] = true;
					}
				}
			}
		}

		return $this->createJsonResponse(array(
			'error' => true,
			'error_codes' => $errors,
		));
	}


	############################################################################
	# input
	############################################################################

	public function saveNewLicenseAction()
	{
		$license_code = $this->in->getString('license_code');

		$lic = License::create($license_code, $this->settings->get('core.install_key'));
		if ($lic->isLicenseCodeError()) {

			if ($this->request->isXmlHttpRequest()) {
				return $this->createJsonResponse(array(
					'error' => true,
					'error_code' => $lic->getLicenseCodeError()
				));
			}

			return $this->redirectRoute('admin');
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->settings->setSetting('core.license', $license_code);
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		if (!$lic->isDemo() && !isset($GLOBALS['DP_DISABLE_SENDREPORTS'])) {
			try {
				$client = new \Zend\Http\Client(null, array('timeout' => 8));
				$client->setMethod(\Zend\Http\Request::METHOD_GET);
				$client->setUri(License::getLicServer() . '/api/license/set-license.json');
				$client->setParameterGet(array(
					'license_id'    => $lic->getLicenseId(),
					'install_key'   => App::getSetting('core.install_key'),
					'install_token' => App::getSetting('core.install_token')
				));
				$client->send();
			} catch (\Exception $e) {}
		}

		if ($this->request->isXmlHttpRequest()) {
			return $this->createJsonResponse(array(
				'success'       => true,
				'license_id'    => $lic->getLicenseId(),
				'install_key'   => App::getSetting('core.install_key'),
				'install_token' => App::getSetting('core.install_token')
			));
		}

		$this->session->setFlash('saved', "License code");
		return $this->redirectRoute('admin');
	}

	############################################################################
	# key-file
	############################################################################

	public function keyFileAction()
	{
		$email_address = $this->in->getString('email_address');
		if (!$email_address) {
			$email_address = $this->person->getPrimaryEmailAddress();
		}

		$install_data = array();
		$install_data['install_key'] = $this->settings->get('core.install_key');
		$install_data['install_token'] = $this->settings->get('core.install_token');
		$install_data['email_address'] = $email_address;
		$install_data['url'] = $this->request->getUriForPath('/');
		$install_data = json_encode($install_data);
		$install_data = base64_encode($install_data);

		$file = <<<FILE
Email this file to support@deskpro.com and our agents will generate a license code for you
==============================DP_INSTALLKEY_BGN==============================
$install_data
==============================DP_INSTALLKEY_END==============================
FILE;

		$res = $this->createResponse($file);
		$res->headers->set('Content-Type', 'application/octet-stream; filename=deskpro-license-sign.key');
		return $res;
	}
}
