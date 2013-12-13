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

namespace Highrise;

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
				$url = App::getSetting("$plugin->id.api_url");
				$token = App::getSetting("$plugin->id.api_token");

				if (!$url || !$token) {
					return $controller->createJsonResponse(array('error' => 'API token or URL missing. Please configure the plugin.'));
				}

				$parts = parse_url($url);
				$url = $parts['scheme'] . '://' . $parts['host'];

				$matches = array();

				$email = $controller->in->getString('email');
				if ($email) {
					$highrise = new \Orb\Service\Highrise\Highrise($url, $token);
					$personApi = new \Orb\Service\Highrise\Resource\Person($highrise);
					try {
						$error = error_reporting();
						error_reporting($error & ~E_WARNING);

						$output = $personApi->findPeopleWithCriteria(array('email' => $email));

						error_reporting($error);
					} catch (\Exception $e) {
						return $controller->createJsonResponse(array('error' => 'Invalid Highrise API URL or token.'));
					}

					foreach ($output AS $person) {
						if (isset($person['first-name'], $person['last-name'])) {
							$name = $person['first-name'] . ' ' . $person['last-name'];
						} else if (isset($person['first-name'])) {
							$name = $person['first-name'];
						} else if (isset($person['last-name'])) {
							$name = $person['last-name'];
						} else {
							$name = 'Unknown';
						}

						if (isset($person['contact-data']['email-addresses'][0]['address'])) {
							$email = $person['contact-data']['email-addresses'][0]['address'];
						} else {
							$email = 'Unknown';
						}

						if (isset($person['title'], $person['company-name'])) {
							$companyTitle = $person['title'] . ' @ ' . $person['company-name'];
						} else if (isset($person['title'])) {
							$companyTitle = $person['title'];
						} else if (isset($person['company-name'])) {
							$companyTitle = $person['company-name'];
						} else {
							$companyTitle = false;
						}

						$matches[] = array(
							'id' => $person['id'],
							'name' => $name,
							'email' => $email,
							'title' => isset($person['title']) ? $person['title'] : false,
							'company' => isset($person['company-name']) ? $person['company-name'] : false,
							'companyTitle' => $companyTitle,
							'profile' => $url . '/people/' . $person['id']
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
		return 'Highrise';
	}


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return "Highrise";
	}

	public function getDescription()
	{
		return 'Integrates widgets to the ticket and profile pages in the agent interface to show information from Highrise.';
	}

	public function getDeveloper()
	{
		return 'DeskPRO';
	}

	public function getDeveloperUrl()
	{
		return 'https://www.deskpro.com/integrations/highrise/';
	}

	public function renderConfig(AbstractController $controller, Plugin $plugin, array $errors)
	{
		$widgets = App::getEntityRepository('DeskPRO:Widget')->findBy(array('plugin' => $plugin));

		$showTicket = false;
		$showProfile = false;

		foreach ($widgets AS $widget) {
			if ($widget->unique_key == 'Highrise-ticket') {
				$showTicket = $widget->enabled;
			} else if ($widget->unique_key == 'Highrise-profile') {
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
			if ($widget->unique_key == 'Highrise-ticket') {
				$widget->enabled = $showTicket;
				$orm->persist($widget);
			} else if ($widget->unique_key == 'Highrise-profile') {
				$widget->enabled = $showProfile;
				$orm->persist($widget);
			}
		}

		$orm->flush();

		return parent::processConfig($controller, $plugin, $errors);
	}
}