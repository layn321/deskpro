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

use Application\DeskPRO\Entity\Usersource;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

class UserRegController extends AbstractController
{
	############################################################################
	# options
	############################################################################

	public function optionsAction()
	{
		$usersources = $this->em->createQuery("
			SELECT us
			FROM DeskPRO:Usersource us
			ORDER BY us.display_order ASC, us.title ASC
		")->execute();

		$reg_triggers = $this->em->getRepository('DeskPRO:TicketTrigger')->getSystemTriggers('email_validation');

		$everyone_ug = $this->em->find('DeskPRO:Usergroup', 1);

		return $this->render('AdminBundle:UserReg:options.html.twig', array(
			'usersources'  => $usersources,
			'everyone_ug'  => $everyone_ug,
			'reg_triggers' => $reg_triggers,
		));
	}

	public function saveOptionsAction()
	{
		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.user_mode', $this->in->getString('mode'));
		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.reg_url', $this->in->getString('reg_url'));
		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.email_validation', $this->in->getBool('email_validation'));
		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.existing_account_login', $this->in->getBool('existing_account_login'));

		if ($this->in->getString('mode') != 'closed') {
			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.deskpro_source_enabled', true);
		}

		return $this->createJsonResponse(array('success'=> true));
	}


	############################################################################
	# deskpro
	############################################################################

	public function deskproSourceToggleAction()
	{
		$onoff = !$this->container->getSetting('core.deskpro_source_enabled');

		$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.deskpro_source_enabled', (int)($onoff));

		if (!$onoff) {
			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.user_mode', 'closed');
		}

		return $this->redirectRoute('admin_userreg_options');
	}


	############################################################################
	# facebook
	############################################################################

	public function facebookEditAction()
	{
		$facebook = $this->em->getRepository('DeskPRO:Usersource')->getByType('facebook');
		if (!$facebook) {
			$facebook = new Usersource();
			$facebook->is_enabled = false;
			$facebook->source_type = 'facebook';
			$facebook->title = 'Facebook';
			$facebook->lost_password_url = 'https://www.facebook.com/recover.php';

			$this->em->getConnection()->beginTransaction();
			try {
				$this->em->persist($facebook);
				$this->em->flush();
				$this->em->getConnection()->commit();
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}
		}

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken('facebook_setup');

			$facebook->setOptions(array(
				'app_key'    => $this->in->getString('facebook.app_key'),
				'app_secret' => $this->in->getString('facebook.app_secret'),
			));

			$facebook->is_enabled = true;

			$this->em->getConnection()->beginTransaction();
			try {
				$this->em->persist($facebook);
				$this->em->flush();

				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.facebook_source_enabled', (int)$facebook->is_enabled);

				$this->em->getConnection()->commit();
				return $this->redirectRoute('admin_userreg_options');
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}
		}

		return $this->render('AdminBundle:UserReg:facebook-edit.html.twig', array(
			'usersource' => $facebook,
			'options' => $facebook->options
		));
	}

	public function facebookToggleAction()
	{
		$facebook = $this->em->getRepository('DeskPRO:Usersource')->getByType('facebook');
		if (!$facebook || $facebook->hasOption('is_setup')) {
			return $this->redirectRoute('admin_userreg_facebook_edit');
		}

		if ($facebook->is_enabled) {
			$facebook->is_enabled = false;
		} else {
			$facebook->is_enabled = true;
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($facebook);
			$this->em->flush();

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.facebook_source_enabled', (int)$facebook->is_enabled);

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_userreg_options');
	}

	############################################################################
	# twitter
	############################################################################

	public function twitterEditAction()
	{
		$twitter = $this->em->getRepository('DeskPRO:Usersource')->getByType('twitter');
		if (!$twitter) {
			$twitter = new Usersource();
			$twitter->is_enabled = false;
			$twitter->source_type = 'twitter';
			$twitter->title = 'Twitter';
			$twitter->lost_password_url = 'https://twitter.com/account/resend_password';

			$twitter->setOptions(array(
				'consumer_key'    => App::getSetting('core.twitter_user_consumer_key'),
				'consumer_secret' => App::getSetting('core.twitter_user_consumer_secret'),
			));

			$this->em->getConnection()->beginTransaction();
			try {
				$this->em->persist($twitter);
				$this->em->flush();
				$this->em->getConnection()->commit();
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}
		}

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken('twitter_setup');

			$twitter->setOptions(array(
				'consumer_key'    => $this->in->getString('twitter.consumer_key'),
				'consumer_secret' => $this->in->getString('twitter.consumer_secret'),
			));

			$twitter->is_enabled = true;

			if ($this->in->getString('twitter.consumer_key') && $this->in->getString('twitter.consumer_secret')
				&& !App::getSetting('core.twitter_user_consumer_key') && !App::getSetting('core.twitter_user_consumer_secret')
			) {
				App::getContainer()->getSettingsHandler()->setSetting('core.twitter_user_consumer_key', $this->in->getString('twitter.consumer_key'));
				App::getContainer()->getSettingsHandler()->setSetting('core.twitter_user_consumer_secret', $this->in->getString('twitter.consumer_secret'));
			}

			$this->em->getConnection()->beginTransaction();
			try {
				$this->em->persist($twitter);
				$this->em->flush();

				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.twitter_source_enabled', (int)$twitter->is_enabled);

				$this->em->getConnection()->commit();
				return $this->redirectRoute('admin_userreg_options');
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}
		}

		return $this->render('AdminBundle:UserReg:twitter-edit.html.twig', array(
			'usersource' => $twitter,
			'options' => $twitter->options
		));
	}

	public function twitterToggleAction()
	{
		$twitter = $this->em->getRepository('DeskPRO:Usersource')->getByType('twitter');
		if (!$twitter || $twitter->hasOption('is_setup')) {
			return $this->redirectRoute('admin_userreg_twitter_edit');
		}

		if ($twitter->is_enabled) {
			$twitter->is_enabled = false;
		} else {
			$twitter->is_enabled = true;
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($twitter);
			$this->em->flush();

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.twitter_source_enabled', (int)$twitter->is_enabled);

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_userreg_options');
	}

	############################################################################
	# google
	############################################################################

	public function googleToggleAction()
	{
		$google = $this->em->getRepository('DeskPRO:Usersource')->getByType('google');
		if (!$google) {
			$google = new Usersource();
			$google->is_enabled = false;
			$google->source_type = 'google';
			$google->title = 'Google';
			$google->lost_password_url = 'https://www.google.com/accounts/recovery';
		}

		if ($google->is_enabled) {
			$google->is_enabled = false;
		} else {
			$google->is_enabled = true;
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($google);
			$this->em->flush();

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.google_source_enabled', (int)$google->is_enabled);

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_userreg_options');
	}

	############################################################################
	# usersource-new-choose
	############################################################################

	public function usersourceNewChooseAction()
	{
		$plugin_sources = $this->em->getRepository('DeskPRO:UsersourcePlugin')->getPluginUsersources();

		return $this->render('AdminBundle:UserReg:usersource-new-choose.html.twig', array(
			'plugin_sources' => $plugin_sources
		));
	}


	############################################################################
	# usersource-edit
	############################################################################

	public function usersourceEditAction($id = 0)
	{
		if ($id) {
			$usersource = $this->em->find('DeskPRO:Usersource', $id);
			if (!$usersource) {
				return $this->redirectRoute('admin_userreg_options');
			}
		} else {
			$usersource = new Usersource();
			$usersource->source_type = $this->in->getString('usersource.source_type');

			if (!$usersource->source_type) {
				return $this->redirectRoute('admin_userreg_usersource_choose');
			}

			$source_plugin = $this->em->getRepository('DeskPRO:UsersourcePlugin')->getByUniqueKey($usersource->source_type);
			if ($source_plugin) {
				$usersource->usersource_plugin = $source_plugin;
			}
		}

		$typename    = $usersource->getTypeName();

		if ($typename == 'db_table_php_password_check' && App::getSetting('core.usersource_db_table_disabled')) {
			return $this->redirectRoute('admin_userreg_usersource_choose');
		}

		$editfield = $usersource->getFormModel();
		$formtype = $usersource->getFormType();
		$form      = $this->get('form.factory')->create($formtype, $editfield);

		if ($formtype instanceof \Application\AdminBundle\Form\Usersource\Type\ActiveDirectoryType || $formtype instanceof \Application\AdminBundle\Form\Usersource\Type\LdapType) {
			if (!extension_loaded('ldap')) {
				return $this->render('AdminBundle:UserReg:usersource-require-ldap.html.twig');
			}

			// Make sure ldap appears in CLI phpini as well
			if (file_exists(dp_get_data_dir() .'/cli-phpinfo.html')) {
				$cli_phpinfo = file_get_contents(dp_get_data_dir() .'/cli-phpinfo.html');
				if (stripos($cli_phpinfo, 'ldap') === false) {
					return $this->render('AdminBundle:UserReg:usersource-require-ldap.html.twig', array('missing_cli' => true));
				}
			}
		}

		if ($this->request->isPost() && $this->in->getBool('process')) {
			$this->em->getConnection()->beginTransaction();

			try {
				$form->bindRequest($this->get('request'));
				$editfield->save($this->em);
				$this->em->getConnection()->commit();
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}

			return $this->redirectRoute('admin_userreg_usersource_edit', array('id' => $usersource->id));
		}

		$formView = $form->createView();

		return $this->render('AdminBundle:UserReg:usersource-edit.html.twig', array(
			'usersource' => $usersource,
			'form'       => $formView
		));
	}

	############################################################################
	# usersource-toggle
	############################################################################

	public function usersourceToggleAction($id)
	{
		$usersource = $this->em->find('DeskPRO:Usersource', $id);
		if (!$usersource) {
			throw $this->createNotFoundException("Unknown usersource");
		}

		$usersource->is_enabled = !$usersource->is_enabled;

		if ($usersource->is_enabled && ($usersource->getTypeName() == 'ActiveDirectory' || $usersource->getTypeName() == 'Ldap')) {
			if (!extension_loaded('ldap')) {
				return $this->render('AdminBundle:UserReg:usersource-require-ldap.html.twig');
			}
		}

		$this->db->beginTransaction();
		try {
			$this->em->persist($usersource);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_userreg_options');
	}

	############################################################################
	# usersource-delete
	############################################################################

	public function usersourceDeleteAction($id, $security_token)
	{
		if (!$this->session->getEntity()->checkSecurityToken('delete_usersource', $security_token)) {
			echo 'invalid security token';
			exit;
		}

		$usersource = $this->em->find('DeskPRO:Usersource', $id);
		if (!$usersource) {
			return $this->redirectRoute('admin_userreg_options');
		}

		$this->db->beginTransaction();
		try {
			$this->em->remove($usersource);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_userreg_options');
	}

	############################################################################
	# usersource-test
	############################################################################

	public function usersourceTestAction($id)
	{
		/** @var $usersource \Application\DeskPRO\Entity\Usersource */
		$usersource = $this->em->find('DeskPRO:Usersource', $id);
		if (!$usersource || !$usersource->isCapable('form_login')) {
			return $this->redirectRoute('admin_userreg_options');
		}

		if ($this->getRequest()->getMethod() == 'POST') {

			$adapter = $usersource->getAdapter()->getAuthAdapter();

			$logger = new \Orb\Log\Logger();
			$arr_wr = new \Orb\Log\Writer\ArrayWriter();
			$logger->addWriter($arr_wr);
			$adapter->setLogger($logger);

			/** @var $us \Application\DeskPRO\Entity\Usersource */

			$adapter->setFormData(array(
				'username' => $this->in->getString('email_address'),
				'password' => $this->in->getString('password')
			));
			$result = $adapter->authenticate();

			$log = implode("\n", $arr_wr->getMessages());

			if ($result && $result->isValid() && $result->getIdentity()) {
				$result_raw = "DATA RECORD:\n=======================================================\n";
				$result_raw .= var_export($result->getIdentity()->getRawData(), true);
				$result_raw .= "\n\n\n\n";
				$result_raw .= "RAW RESULT:\n=======================================================\n";
				$result_raw .= print_r($result, true);
			} else {
				$result_raw = "No Identity\n\n\n";
				$result_raw .= print_r($result, true);
			}

			$clean = @htmlspecialchars($result_raw, \ENT_QUOTES, 'ISO-8895-1');
			if (!$clean) {
				$result_raw = Strings::utf8_bad_strip($result_raw);
				$clean = @htmlspecialchars($result_raw, \ENT_QUOTES, 'ISO-8895-1');
				if (!$clean) {
					$clean = "[Data contains invalid characters]";
				}
			}

			$result_raw = $clean;

			return $this->render('AdminBundle:UserReg:usersource-test-result.html.twig', array(
				'usersource'    => $usersource,
				'log'           => $log,
				'raw_user_data' => $result_raw,
				'is_valid'      => $result->isValid()
			));
		}

		return $this->render('AdminBundle:UserReg:usersource-test.html.twig', array(
			'usersource' => $usersource,
		));
	}
}
