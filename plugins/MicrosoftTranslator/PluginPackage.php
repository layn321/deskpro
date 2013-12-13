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

namespace MicrosoftTranslator;

use Application\DeskPRO\Entity\Plugin;
use Application\DeskPRO\Plugin\PluginPackage as CorePluginPackage;
use Application\DeskPRO\Controller\AbstractController;
use Application\DeskPRO\App;

class PluginPackage extends CorePluginPackage\AbstractPluginPackage
{
	public function runAgentAction(AbstractController $controller, $action, Plugin $plugin)
	{
		switch ($action) {
			case 'translate-ticket-message':
				require_once(__DIR__.'/Controller/TicketController.php');
				$c = new \MicrosoftTranslator\Controller\TicketController($controller->getContainer());
				$c->person = $controller->person;
				return $c->translateMessageAction(
					$controller->in->getUint('message_id'),
					$controller->in->getString('from'),
					$controller->in->getString('to')
				);
				break;

			case 'translate-text':
				require_once(__DIR__.'/Controller/TicketController.php');
				$c = new \MicrosoftTranslator\Controller\TicketController($controller->getContainer());
				$c->person = $controller->person;
				return $c->translateTextAction(
					$controller->in->getRaw('message_text'),
					$controller->in->getString('from'),
					$controller->in->getString('to')
				);
				break;
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
		return 'MicrosoftTranslator';
	}


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return "Microsoft Translator";
	}

	public function getDescription()
	{
		return 'Adds translation controls to ticket messages to translate messages between languages, and also allows agents to translate their replies before sending.';
	}

	public function getDeveloper()
	{
		return 'DeskPRO';
	}

	public function getDeveloperUrl()
	{
		return 'https://www.deskpro.com//';
	}

	public function renderConfig(AbstractController $controller, Plugin $plugin, array $errors)
	{
		$widgets = App::getEntityRepository('DeskPRO:Widget')->findBy(array('plugin' => $plugin));

		return $controller->render($this->getName() . ':Admin:config.html.twig', array(
			'plugin' => $plugin,
			'info' => $this,
			'errors' => $errors
		));
	}
}