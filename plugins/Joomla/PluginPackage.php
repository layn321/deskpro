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

namespace Joomla;

use Application\DeskPRO\Entity\Plugin;
use Application\DeskPRO\Plugin\PluginPackage as CorePluginPackage;
use Application\DeskPRO\Controller\AbstractController;
use Application\DeskPRO\App;
use Orb\Util\Strings;

class PluginPackage extends CorePluginPackage\AbstractPluginPackage
{
	public function runUserAction(AbstractController $controller, $action, Plugin $plugin)
	{
		switch ($action) {
			case 'init_session':
				$data = isset($_REQUEST['DATA']) ? $_REQUEST['DATA'] : null;

				$m = null;
				if (!$data || !is_string($data) || !preg_match('#^(\d+)_([a-fA-F0-9]+)_(.*?)$#', $data, $m)) {
					return $controller->redirect(App::getSetting('Joomla.joomla_url'));
				}

				$time   = $m[1];
				$sign   = $m[2];
				$params = @json_decode(@base64_decode($m[3]), true);

				if (!$sign || !$params || $time < (time() - 300)) {
					return $controller->redirect(App::getSetting('Joomla.joomla_url'));
				}

				$sign_check = sha1($time . $m[3] . App::getSetting('Joomla.joomla_secret'));
				if ($sign != $sign_check) {
					return $controller->redirect(App::getSetting('Joomla.joomla_url'));
				}

				$person = App::getOrm()->getRepository('DeskPRO:Person')->findOneByEmail($params['email']);
				if (!$person) {
					/** @var \Application\DeskPRO\Usersource\UsersourceManager $usm */
					$usm = App::getContainer()->getSystemService('UsersourceManager');

					$joomla_usersources = $usm->getUsersourcesOfType('Joomla');
					foreach ($joomla_usersources as $us) {
						$adapter = $us->getAdapter();
						$identity = $adapter->findIdentityByInput($params['email']);
						if ($identity) {
							$login_processor = new \Application\DeskPRO\Auth\LoginProcessor($us, $identity);
							$person = $login_processor->getPerson();
							break;
						}
					}
				}

				if ($person) {
					$controller->session->set('auth_person_id', $person->getId());
					$controller->session->set('dp_interface', 'user');
					$controller->session->save();
					App::setCurrentPerson($person);
				}

				if (!empty($params['return'])) {
					if (!preg_match('#^https?://#i', $params['return'])) {
						$params['return'] = App::getSetting('Joomla.joomla_url') . '/' . $params['return'];
					}
					return $controller->redirect($params['return']);
				} else {
					return $controller->redirect(App::getSetting('Joomla.joomla_url'));
				}
				break;

			default:
				throw $controller->createNotFoundException();
		}
	}

	function __call($name, $arguments)
	{
		// TODO: Implement __call() method.
	}


	/**
	 * Called the first time the plugin is installed.
	 *
	 * @return CorePluginPackage\InstallerSimple
	 */
	public function getInstaller($install_controller, Plugin $plugin)
	{
		$initial_secret = Strings::random(20, Strings::CHARS_KEY_ALPHA);
		App::getDb()->executeUpdate("
			INSERT IGNORE INTO settings
			SET name = 'Joomla.joomla_secret', value = '$initial_secret'
		");
		return new CorePluginPackage\InstallerSimple($plugin, $install_controller);
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
		return 'Joomla';
	}


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return "Joomla";
	}

	public function getDescription()
	{
		return 'Enables the Joomla usersource option for single-signon with Joomla.';
	}

	public function getDeveloper()
	{
		return 'DeskPRO';
	}

	public function getDeveloperUrl()
	{
		return 'https://www.deskpro.com/integrations/joomla/';
	}

	public function renderConfig(AbstractController $controller, Plugin $plugin, array $errors)
	{
		return $controller->render($this->getName() . ':Admin:config.html.twig', array(
			'plugin' => $plugin,
			'info' => $this,
			'errors' => $errors
		));
	}

	public function processConfig(AbstractController $controller, Plugin $plugin, array &$errors)
	{
		if (parent::processConfig($controller, $plugin, $errors)) {
			if ($controller->in->getBool('configure_usersource')) {
				$us = App::getEntityRepository('DeskPRO:Usersource')->getByType('joomla');
				if ($us) {
					return $controller->generateUrl('admin_userreg_usersource_edit',
						array('id' => $us->id)
					);
				} else {
					return $controller->generateUrl('admin_userreg_usersource_edit',
						array('usersource' => array('source_type' => 'joomla'))
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