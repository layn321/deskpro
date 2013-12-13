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

namespace Salesforce;

use Application\DeskPRO\Entity\Plugin;
use Application\DeskPRO\Plugin\PluginPackage as CorePluginPackage;
use Application\DeskPRO\Controller\AbstractController;
use Application\DeskPRO\App;

class PluginPackage extends CorePluginPackage\AbstractPluginPackage
{
	public function runAgentAction(AbstractController $controller, $action, Plugin $plugin)
	{
		switch ($action) {
			case 'call-api':
				$user = App::getSetting("$plugin->id.api_user");
				$password = App::getSetting("$plugin->id.api_password");
				$token = App::getSetting("$plugin->id.api_security_token");

				if (!$user || !$password || !$token) {
					return $controller->createJsonResponse(array('error' => 'API user, password or token missing. Please configure the plugin.'));
				}

				$matches = array();

				$email = $controller->in->getString('email');
				if ($email) {
					try {
						$error = error_reporting();
						error_reporting($error & ~E_WARNING);

						require_once(DP_ROOT . '/vendor/salesforce/SforcePartnerClient.php');
						$sforce = new \SforcePartnerClient();
						$sforce->createConnection(DP_ROOT . '/vendor/salesforce/partner.wsdl.xml');

						error_reporting($error);
					} catch (\SoapFault $e) {
						return $controller->createJsonResponse(array('error' => 'Invalid Salesforce URL.'));
					}

					try {
						$sforce->login($user, $password . $token);
					} catch (\SoapFault $e) {
						if ($e->getMessage()) {
							return $controller->createJsonResponse(array('error' => 'Salesforce error: ' . $e->getMessage()));
						}
						return $controller->createJsonResponse(array('error' => 'Invalid Salesforce API user, password, or token.'));
					}

					$response = $sforce->query("
						SELECT Id, FirstName, LastName, Title, Department, Email
						FROM Contact
						WHERE Email = '" . addslashes($email) . "'
					");
					foreach ($response->records AS $record) {
						if ($record->fields->Title && $record->fields->Department) {
							$departmentTitle = $record->fields->Department . ', ' . $record->fields->Title;
						} else {
							$departmentTitle = $record->fields->Department . $record->fields->Title;
						}

						$matches[] = array(
							'id' => $record->Id,
							'name' => $record->fields->FirstName . ' ' . $record->fields->LastName,
							'email' => $record->fields->Email,
							'title' => $record->fields->Title,
							'department' => $record->fields->Department,
							'departmentTitle' => $departmentTitle,
							'profile' => 'https://na8.salesforce.com/' . $record->Id
						);
					}
				}

				return $controller->createJsonResponse(array('matched' => count($matches), 'matches' => $matches));
		}

		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Unknown agent plugin action $action");
	}

	/**
	 * Ge the version
	 *
	 * @return mixed
	 */
	public function getVersion()
	{
		return '1.0';
	}

	
	/**
	 * Get the unique name for the plugin. Use a-zA-Z0-9 only (do not use underscores or settings will not be accessible).
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'Salesforce';
	}


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return "Salesforce";
	}

	public function getDescription()
	{
		return 'Integrates widgets to the ticket and profile pages in the agent interface to show information from Salesforce.';
	}

	public function getDeveloper()
	{
		return 'DeskPRO';
	}

	public function getDeveloperUrl()
	{
		return 'https://www.deskpro.com/integrations/salesforce/';
	}

	public function renderConfig(AbstractController $controller, Plugin $plugin, array $errors)
	{
		$widgets = App::getEntityRepository('DeskPRO:Widget')->findBy(array('plugin' => $plugin));

		$showTicket = false;
		$showProfile = false;

		foreach ($widgets AS $widget) {
			if ($widget->unique_key == 'Salesforce-ticket') {
				$showTicket = $widget->enabled;
			} else if ($widget->unique_key == 'Salesforce-profile') {
				$showProfile = $widget->enabled;
			}
		}

		return $controller->render($this->getName() . ':Admin:config.html.twig', array(
			'plugin' => $plugin,
			'info' => $this,
			'showTicket' => $showTicket,
			'showProfile' => $showProfile,
			'errors' => $errors
		));
	}

	public function processConfig(AbstractController $controller, Plugin $plugin, array &$errors)
	{
		$orm = App::getOrm();

		$showTicket = $controller->in->getBool('widget_ticket');
		$showProfile = $controller->in->getBool('widget_profile');

		$widgets = App::getEntityRepository('DeskPRO:Widget')->findBy(array('plugin' => $plugin));
		foreach ($widgets AS $widget) {
			if ($widget->unique_key == 'Salesforce-ticket') {
				$widget->enabled = $showTicket;
				$orm->persist($widget);
			} else if ($widget->unique_key == 'Salesforce-profile') {
				$widget->enabled = $showProfile;
				$orm->persist($widget);
			}
		}

		$orm->flush();

		return parent::processConfig($controller, $plugin, $errors);
	}
}