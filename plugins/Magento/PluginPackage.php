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

namespace Magento;

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
				$url = App::getSetting("$plugin->id.url");
				$user = App::getSetting("$plugin->id.api_user");
				$key = App::getSetting("$plugin->id.api_key");

				if (!$url || !$user || !$key) {
					return $controller->createJsonResponse(array('error' => 'API URL, user or key missing. Please configure the plugin.'));
				}

				if (!class_exists('\SoapClient')) {
					return $controller->createJsonResponse(array('error' => 'SOAP support missing from PHP.'));
				}

				$matches = array();

				$email = $controller->in->getString('email');
				if ($email) {
					try {
						$error = error_reporting();
						error_reporting($error & ~E_WARNING);
						$client = new \SoapClient($url . '/api?wsdl');
						error_reporting($error);
					} catch (\SoapFault $e) {
						return $controller->createJsonResponse(array('error' => 'Invalid Magento URL'));
					}

					try {
						$session = $client->login($user, $key);
					} catch (\SoapFault $e) {
						return $controller->createJsonResponse(array('error' => 'Invalid Magento API user or key'));
					}

					$results = $client->call($session, 'customer.list', array(
						array('email' => $email)
					));

					foreach ($results AS $record) {
						$sales = $client->call($session, 'sales_order.list', array(
							array('customer_id' => $record['customer_id'])
						));

						$orders = array();
						foreach ($sales AS $sale) {
							$orders[] = array(
								'id' => $sale['increment_id'],
								'order_id' => $sale['order_id'],
								'created_at' => $sale['created_at'],
								'grand_total' => number_format($sale['grand_total'], 2),
								'currency' => $sale['order_currency_code'],
								'status' => $sale['status'],
								'url' => $url . '/admin/sales_order/view/order_id/' . $sale['order_id'] . '/',
							);
						}

						$matches[] = array(
							'id' => $record['customer_id'],
							'name' => $record['firstname'] . ' ' . $record['lastname'],
							'email' => $record['email'],
							'profile' => $url . '/admin/customer/edit/id/' . $record['customer_id'] . '/',
							'orders' => $orders
						);
					}

					$client->endSession($session);
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
		return 'Magento';
	}


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return "Magento";
	}

	public function getDescription()
	{
		return 'Integrates widgets to the ticket and profile pages in the agent interface to show information from Magento. Also allows for a Magento user source and single sign-on with Magento users.';
	}

	public function getDeveloper()
	{
		return 'DeskPRO';
	}

	public function getDeveloperUrl()
	{
		return 'https://www.deskpro.com/integrations/magento/';
	}

	public function renderConfig(AbstractController $controller, Plugin $plugin, array $errors)
	{
		$widgets = App::getEntityRepository('DeskPRO:Widget')->findBy(array('plugin' => $plugin));

		$showTicket = false;
		$showProfile = false;

		foreach ($widgets AS $widget) {
			if ($widget->unique_key == 'Magento-ticket') {
				$showTicket = $widget->enabled;
			} else if ($widget->unique_key == 'Magento-profile') {
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
			if ($widget->unique_key == 'Magento-ticket') {
				$widget->enabled = $showTicket;
				$orm->persist($widget);
			} else if ($widget->unique_key == 'Magento-profile') {
				$widget->enabled = $showProfile;
				$orm->persist($widget);
			}
		}

		$orm->flush();

		if (parent::processConfig($controller, $plugin, $errors)) {
			if ($controller->in->getBool('configure_usersource')) {
				$us = App::getEntityRepository('DeskPRO:Usersource')->getByType('magento');
				if ($us) {
					return $controller->generateUrl('admin_userreg_usersource_edit',
						array('id' => $us->id)
					);
				} else {
					return $controller->generateUrl('admin_userreg_usersource_edit',
						array('usersource' => array('source_type' => 'magento'))
					);
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}