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

use Application\DeskPRO\Entity;
use Application\DeskPRO\App;
use Orb\Util\Numbers;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

class SettingsController extends AbstractController
{
	############################################################################
	# list
	############################################################################

	public function settingsAction()
	{
		$format_exts = function($str) {
			$str = str_replace(' ', ',', $str);
			$parts = explode(',', $str);

			$ret = array();
			foreach ($parts as $p) {
				$p = trim($p);
				$p = trim($p, '.');

				if ($p) {
					$ret[] = $p;
				}
			}

			$ret = implode(', ', $ret);
			return $ret;
		};

		if ($this->in->getBool('process')) {

			if (empty($_POST['settings']['core.deskpro_url'])) {
				$_POST['settings']['core.deskpro_url'] = App::getSetting('core.deskpro_url');
			}
			$url = rtrim(preg_replace('#index\.php/?$#', '', $_POST['settings']['core.deskpro_url']), '/') . '/';
			if (!preg_match('#^https?://#', $url)) {
				$url = 'http://' . $url;
			}

			$gateway_max_email = intval(@$_POST['settings']['core.gateway_max_email']);
			if (!$gateway_max_email || $gateway_max_email < 1 || $gateway_max_email == 20) {
				$gateway_max_email = 0;
			}

			if ($gateway_max_email) {
				$gateway_max_email = Numbers::parseIniSize($gateway_max_email . 'M');
			}

			$update_settings = $this->in->getCleanValueArray('settings', 'string', 'str_simple');
			$update_settings = array_merge($update_settings, array(
				'core.deskpro_name'            => $_POST['settings']['core.deskpro_name'],
				'core.deskpro_url'             => $url,
				'core.site_name'               => $_POST['settings']['core.site_name'],
				'core.site_url'                => $_POST['settings']['core.site_url'],
				'core.helpdesk_disabled'       => empty($_POST['settings']['core.helpdesk_disabled']) ? 0 : 1,
				'core.cookie_path'             => $_POST['settings']['core.cookie_path'],
				'core.cookie_domain'           => $_POST['settings']['core.cookie_domain'],
				'core.use_gravatar'            => empty($_POST['settings']['core.use_gravatar']) ? 0 : 1,
				'core.rewrite_urls'            => empty($_POST['settings']['core.rewrite_urls']) ? 0 : 1,
				'core.redirect_correct_url'    => empty($_POST['settings']['core.redirect_correct_url']) ? 0 : 1,
				'core.default_timezone'        => empty($_POST['settings']['core.default_timezone']) ? "UTC" : $_POST['settings']['core.default_timezone'],
				'core.ga_property_id'          => trim($_POST['settings']['core.ga_property_id']),

				'core.attach_agent_maxsize'    => empty($_POST['settings']['core.attach_agent_maxsize']) ? 0 : (int)$_POST['settings']['core.attach_agent_maxsize'],
				'core.attach_agent_must_exts'  => $format_exts($_POST['settings']['core.attach_agent_must_exts']),
				'core.attach_agent_not_exts'   => $format_exts($_POST['settings']['core.attach_agent_not_exts']),

				'core.attach_user_maxsize'     => empty($_POST['settings']['core.attach_user_maxsize']) ? 0 : (int)$_POST['settings']['core.attach_user_maxsize'],
				'core.attach_user_must_exts'   => $format_exts($_POST['settings']['core.attach_user_must_exts']),
				'core.attach_user_not_exts'    => $format_exts($_POST['settings']['core.attach_user_not_exts']),

				'core.sendemail_attach_maxsize' => (int)$_POST['settings']['core.sendemail_attach_maxsize'],

				'core.gateway_max_email' => $gateway_max_email,

				'core.date_fulltime'    => $_POST['settings']['core.date_fulltime'],
				'core.date_full'    => $_POST['settings']['core.date_full'],
				'core.date_day'    => $_POST['settings']['core.date_day'],
				'core.date_day_short'    => $_POST['settings']['core.date_day_short'],
				'core.date_time'    => $_POST['settings']['core.date_time'],
			));
			array_walk($update_settings, 'trim');

			if ($update_settings['core.attach_user_not_exts']) {
				$update_settings['core.attach_user_must_exts'] = '';
			} else {
				$update_settings['core.attach_user_not_exts'] = '';
			}

			if ($update_settings['core.attach_agent_not_exts']) {
				$update_settings['core.attach_agent_must_exts'] = '';
			} else {
				$update_settings['core.attach_agent_not_exts'] = '';
			}

			$set_settings_keys = $this->in->getCleanValueArray('set_settings', 'str_simple', 'discard');

			// set_settings contains an array of names that should be set
			// If no value, it means its a null value (aka to be unset/set to default)
			foreach ($this->in->getCleanValueArray('set_settings_falseable', 'str_simple', 'discard') as $k) {
				if (!isset($update_settings[$k])) {
					$update_settings[$k] = 0;
				}
			}
			foreach ($set_settings_keys as $k) {
				if (!isset($update_settings[$k])) {
					$update_settings[$k] = null;
				}
			}

			foreach ($update_settings as $k => $v) {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting($k, $v);
			}
			$this->container->getSettingsHandler()->setTemporarySettingValues($update_settings);

			if (!$update_settings['core.helpdesk_disabled'] && file_exists(dp_get_data_dir().'/helpdesk-offline.trigger')) {
				unlink(dp_get_data_dir().'/helpdesk-offline.trigger');
			}

			if ($this->in->getString('offline_message')) {
				if (!@file_put_contents(dp_get_data_dir() . '/helpdesk-offline-message.txt', $this->in->getString('offline_message'))) {
					return $this->renderStandardError("Could not write to the data directory. Tried writing " . dp_get_data_dir() . '/helpdesk-offline-message.txt' . "<br/><br/>Ensure the data directory and all sub-files and sub-directories are writable.");
				}
			} else {
				if (file_exists(dp_get_data_dir() . '/helpdesk-offline-message.txt')) {
					@unlink(dp_get_data_dir() . '/helpdesk-offline-message.txt');
				}
			}

			$this->_postSaveSettings();

			$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
			$cache->invalidateLanguageCache();

			return $this->redirectRoute('admin_settings');
		}

		$max_filesize = \Orb\Util\Env::getEffectiveMaxUploadSize();

		$timezone_options = \DateTimeZone::listIdentifiers();

		$vars = array(
			'max_uploadsize' => $max_filesize,
			'max_uploadsize_readable' => \Orb\Util\Numbers::filesizeDisplay($max_filesize),
			'timezone_options' => $timezone_options,
		);

		if (file_exists(dp_get_data_dir() . '/helpdesk-offline-message.txt')) {
			$vars['offline_message'] = file_get_contents(dp_get_data_dir() . '/helpdesk-offline-message.txt');
		}

		$vars['outgoing_email'] = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:EmailTransport t
			WHERE t.match_type = 'all'
		")->getOneOrNullResult();

		return $this->render('@Settings:settings.html.twig', $vars);
	}

	protected function _postSaveSettings()
	{

	}

	public function settingsSaveFormAction($type, $auth)
	{
		$auth_id = 'settings_' . $type;
		$this->ensureAuthToken($auth_id, $auth);

		$set_settings = $this->in->getCleanValueArray('settings', 'string', 'str_simple');
		$set_settings_keys = $this->in->getCleanValueArray('set_settings', 'str_simple', 'discard');

		// set_settings contains an array of names that should be set
		// If no value, it means its a null value (aka to be unset/set to default)
		foreach ($this->in->getCleanValueArray('set_settings_falseable', 'str_simple', 'discard') as $k) {
			if (!isset($set_settings[$k])) {
				$set_settings[$k] = 0;
			}
		}
		foreach ($set_settings_keys as $k) {
			if (!isset($set_settings[$k])) {
				$set_settings[$k] = null;
			}
		}

		$this->db->beginTransaction();
		try {
			foreach ($set_settings as $k => $v) {
				$this->container->getSettingsHandler()->setSetting($k, $v);
			}
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		if (!$this->getRequest()->isXmlHttpRequest()) {
			$this->session->setFlash('saved_settings', 1);
			$this->session->save();
		}

		// may have changed the default language
		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateLanguageCache();

		if (in_array('core_tickets.use_archive', $set_settings_keys)) {
			if (isset($set_settings['core_tickets.use_archive']) && $set_settings['core_tickets.use_archive']) {
				// Enabled
			} else {
				// Disabled
				App::getDb()->executeUpdate("UPDATE tickets SET status = 'resolved' WHERE status = 'closed'");
			}
		}

		if ($this->getRequest()->isXmlHttpRequest()) {
			return $this->createJsonResponse(array('success' => true));
		}

		$return = $this->in->getString('return');
		if ($return) {
			return $this->redirect($return);
		}

		return $this->redirectRoute('admin');
	}

	############################################################################
	# advanced
	############################################################################

	/**
	 * View a plain list of settings
	 */
	public function advancedAction()
	{
		$settings_files = new \Application\DeskPRO\ResourceScanner\AdvancedSettings();
		$show_settings = $settings_files->getAllSettings();

		if ($this->session->checkSecurityToken('revert_all', $this->in->getString('revert_all'))) {
			$this->db->beginTransaction();
			try {

				foreach ($show_settings as $k => $v) {
					$this->db->delete('settings', array('name' => $k));
				}

				$this->db->commit();
			} catch (\Exception $e) {
				$this->db->rollback();
				throw $e;
			}

			return $this->redirectRoute('admin_settings_adv');
		}

		foreach ($show_settings as $k => &$v) {
			$set = $this->container->getSetting($k);
			$v = array('default' => $v, 'set' => $set);
		}

		return $this->render('AdminBundle:Settings:advanced.html.twig', array(
			'show_settings' => $show_settings
		));
	}

	/**
	 * Set a specific setting
	 */
	public function advancedSetAction($name)
	{
		$value = $this->in->getValue('value');
		$this->em->getRepository('DeskPRO:Setting')->updateSetting($name, $value);

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################
	# labels
	############################################################################

	public function labelsAction($label_type)
	{
		/** @var $ldm \Application\DeskPRO\Labels\LabelDefManager */
		$ldm = $this->container->getSystemService('label_def_manager');

		$def_counts = $ldm->countDefs();

		$type = null;
		if ($label_type != 'all') {
			$type = array($label_type);
		}

		$order_by = 'alpha';
		if ($this->in->getString('order_by') == 'count') {
			$order_by = 'count';
		}
		$labels = $ldm->getLabelsAndCounts($type, $order_by);

		return $this->render('AdminBundle:Settings:labels.html.twig', array(
			'label_type'  => $label_type,
			'order_by'    => $order_by,
			'def_counts'  => $def_counts,
			'labels'      => $labels
		));
	}

	public function labelsAjaxNewAction()
	{
		$label_str = strtolower($this->in->getString('label'));

		/** @var $ldm \Application\DeskPRO\Labels\LabelDefManager */
		$ldm = $this->container->getSystemService('label_def_manager');

		$types = $this->in->getCleanValueArray('types', 'str_simple', 'discard');
		$display_type = null;
		if ($this->in->getString('display_type') != 'all') {
			$display_type = $this->in->getString('display_type');
		}

		$ldm->createLabelDef($label_str, $types);

		$label_count = $ldm->countLabelUsages($label_str, $display_type);
		$def_counts  = $ldm->countDefs();

		$html = $this->renderView('AdminBundle:Settings:labels-row.html.twig', array('label' => $label_str, 'count' => $label_count));

		return $this->createJsonResponse(array(
			'row_html' => $html,
			'def_counts' => $def_counts
		));
	}

	public function labelsAjaxDeleteAction($label_type)
	{
		$label_str = strtolower($this->in->getString('label'));

		/** @var $ldm \Application\DeskPRO\Labels\LabelDefManager */
		$ldm = $this->container->getSystemService('label_def_manager');
		$ldm->deleteLabelDef($label_str);

		$def_counts  = $ldm->countDefs();

		return $this->createJsonResponse(array(
			'success' => 1,
			'def_counts' => $def_counts
		));
	}

	public function renameLabelAction($label_type)
	{
		$old_label_str = strtolower($this->in->getString('old_label'));
		$new_label_str = strtolower($this->in->getString('new_label'));

		$type = null;
		if ($label_type != 'all') {
			$type = $label_type;
		}

		/** @var $ldm \Application\DeskPRO\Labels\LabelDefManager */
		$ldm = $this->container->getSystemService('label_def_manager');
		$ldm->renameLabelDef($old_label_str, $new_label_str, $type);

		$label_count = $ldm->countLabelUsages($new_label_str, $type);
		$html = $this->renderView('AdminBundle:Settings:labels-row.html.twig', array('label' => $new_label_str, 'count' => $label_count));

		$def_counts  = $ldm->countDefs();

		return $this->createJsonResponse(array(
			'success' => 1,
			'row_html' => $html,
			'def_counts' => $def_counts
		));
	}

	############################################################################
	# save-setting
	############################################################################

	public function saveSingleSettingAction($setting_name, $security_token)
	{
		if (!$this->session->getEntity()->checkSecurityToken('set_setting', $security_token)) {
			return $this->renderStandardTokenError();
		}

		$this->em->getRepository('DeskPRO:Setting')->updateSetting($setting_name, $this->in->getRaw('value'));

		if ($setting_name && (!$this->in->getRaw('value') && file_exists(dp_get_data_dir().'/helpdesk-offline.trigger'))) {
			unlink(dp_get_data_dir().'/helpdesk-offline.trigger');
		}

		if ($setting_name == 'core.default_language_id') {
			$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
			$cache->invalidateLanguageCache();
		}

		if ($this->getRequest()->isXmlHttpRequest()) {
			return $this->createJsonResponse(array('success' => true));
		}

		return $this->redirectRoute('admin');
	}


	############################################################################
	# quick-setup
	############################################################################

	public function quickSetupAction()
	{
		$is_import = $this->container->getSetting('core.deskpro3importer') ?: false;
		$zd_is_import = $this->container->getSetting('core.zendeskimporter') ?: false;
		$skip_cron_check = $this->container->getSysConfig('instance_data.install_flags.skip_cron_check');

		$server_check = new \Application\InstallBundle\Install\ServerChecks();
		$server_check->checkServer();
		if ($server_check->hasFatalErrors()) {
			return $this->render('AdminBundle:Settings:quick-server-errors.html.twig', array(
				'errors' => $server_check->getErrors(),
				'data_dir' => dp_get_data_dir(),
			));
		}

		if (!$this->container->getSetting('core.done_data_initializer')) {
			$data_init = new \Application\InstallBundle\Data\DataInitializer($this->container);
			if ($is_import || $zd_is_import) {
				$data_init->setImportMode();
			}

			$this->db->beginTransaction();
			try {
				$data_init->run();
				$this->container->getSettingsHandler()->setSetting('core.done_data_initializer', 1);
				$this->db->commit();
			} catch (\Exception $e) {
				$this->db->rollback();
				throw $e;
			}
		}

		$default_transport = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:EmailTransport t
			WHERE t.match_type = 'all'
		")->getOneOrNullResult();
		$outgoing_email_form = $this->forward('AdminBundle:EmailTransports:editAccount', array('id' => $default_transport ? $default_transport->getId() : '0'), array('_partial' => 'setup'))->getContent();

		$initial_pop = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:EmailGateway t
			WHERE t.is_enabled = true
			ORDER BY t.id ASC
		")->setMaxResults(1)->getOneOrNullResult();
		$incoming_email_form = $this->forward('AdminBundle:EmailGateways:editAccount', array('id' => $initial_pop ? $initial_pop->getId() : '0'), array('_partial' => 'setup'))->getContent();

		// Mark as done
		if ($this->in->getBool('done') || defined('DPC_IS_CLOUD')) {
			$pass = true;
			if (!$default_transport) {
				$pass = false;
			}
			if (!$this->container->getSetting('core.last_cron_run') && !$skip_cron_check) {
				$pass = false;
			}
			if (!$this->container->getSetting('core.license')) {
				$pass = false;
			}

			// Offer a flag to force pass
			if ($this->in->getBool('force') || defined('DPC_IS_CLOUD')) {
				$pass = true;
			}

			if ($pass) {

				if (!$this->container->getSetting('core.rewrite_urls')) {
					try {
						$client = new \Zend\Http\Client(null, array('timeout' => 5));
						$client->setMethod(\Zend\Http\Request::METHOD_GET);
						$client->setUri($this->container->getSetting('core.deskpro_url') . '__checkurlrewrite/path');
						$result = $client->send();
						if ($result->isSuccess() && strpos($result->getBody(), 'dp_check_okay') !== false) {
							$this->db->replace('settings', array(
								'name' => 'core.rewrite_urls',
								'value' => '1',
							));
						}
					} catch (\Exception $e) {}
				}

				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.setup_initial', '1');

				if ($this->container->getSysConfig('instance_data.install_flags.attach_store_files')) {
					$this->container->getEm()->getRepository('DeskPRO:Setting')->updateSetting('core.filestorage_method', 'fs');
				}

				\Application\InstallBundle\Data\DataInitializer::newDefaultTicket($this->person);

				return $this->redirectRoute('admin');
			}
		}

		$php_path = $this->container->getPhpBinaryPath();
		$php_path_set = dp_get_config('php_path');

		$licdata_report = dp_get_config('instance_data.report');
		$calling_countries = \Orb\Data\Countries::getCallingCodeOptions();

		return $this->render('AdminBundle:Settings:quick-setup.html.twig', array(
			'outgoing_email_form' => $outgoing_email_form,
			'incoming_email_form' => $incoming_email_form,
			'is_import' => $is_import,
			'zd_is_import' => $zd_is_import,
			'php_path' => $php_path,
			'php_path_set' => $php_path_set,
			'skip_cron_check' => $skip_cron_check,

			// Existing values
			'license_code' => $this->container->getSetting('core.license'),
			'last_cron_run' => $this->container->getSetting('core.last_cron_run'),
			'default_transport' => $default_transport,
			'initial_pop' => $initial_pop,

			'ma_server' => \DeskPRO\Kernel\License::getLicServer(),
			'install_token' => App::getSetting('core.install_token'),
			'licdata_report' => $licdata_report,
			'calling_countries' => $calling_countries,
		));
	}

	public function setSilentSettingsAction()
	{
		$is_import = $this->container->getSetting('core.deskpro3importer') ?: false;

		$timezone = $this->in->getString('timezone');
		$url = $this->in->getString('url');

		if ($timezone) {
			$this->container->get('deskpro.core.settings')->setSetting('core.default_timezone', $timezone);

			// Also update our own tz
			if ($this->in->getBool('set_admin_tz')) {
				$this->db->executeUpdate("
					UPDATE people SET timezone = ?
					WHERE id = ?
				", array($timezone, $this->person->getId()));
			}
		}
		if ($url && (!$this->container->getSetting('core.deskpro_url') || $is_import)) {
			$url = preg_replace('#index\.php/?(.*?)$#', '', $url);
			$this->container->get('deskpro.core.settings')->setSetting('core.deskpro_url', rtrim(str_replace('index.php', '', $url), '/') . '/');
		}

		if ($this->container->getSetting('core.app_secret') == 'APP_SECRET') {
			$this->container->get('deskpro.core.settings')->setSetting('core.app_secret', Strings::random(50, Strings::CHARS_ALPHANUM_IU));
		}

		$url = $this->request->getUriForPath('/__checkurlrewrite/path');
		$url_noindex = str_replace('/index.php/', '/', $url);

		try {
			$client = new \Zend\Http\Client(null, array('timeout' => 5));
			$client->setMethod(\Zend\Http\Request::METHOD_GET);
			$client->setUri($url_noindex);
			$result = $client->send();
			if ($result->isSuccess() && strpos($result->getBody(), 'dp_check_ok') !== false) {
				$this->db->replace('settings', array(
					'name' => 'core.rewrite_urls',
					'value' => '1',
				));
			}

			$this->db->replace('settings', array(
				'name' => 'core.done_rewrite_urls_check',
				'value' => time(),
			));
		} catch (\Exception $e) {}

		return $this->createJsonResponse(array('success' => true));
	}

	public function checkCronAction()
	{
		if ($this->container->getSetting('core.last_cron_run')) {
			return $this->createJsonResponse(array('cron_okay' => true));
		}

		// Check for error db record
		$error_message = $this->db->fetchColumn("SELECT data FROM install_data WHERE build = 1 AND name = 'cron_run_errors'");
		if (!$error_message) {
			// Check for a logged message
			if (file_exists(dp_get_log_dir().'/cron-boot-errors.log')) {
				$error_message = file_get_contents(dp_get_log_dir().'/cron-boot-errors.log');
			}
		}

		$cron_errors = false;
		if ($error_message) {
			$split = explode('###', $error_message);
			$codes_string = array_pop($split);
			$codes_string = trim($codes_string);

			$ini_path = Strings::extractRegexMatch('#^ini_path:(.*?)$#m', $codes_string, 1);

			$error_codes = array();
			if (preg_match_all('#^error:(.*?)$#m', $codes_string, $m, \PREG_PATTERN_ORDER)) {
				$error_codes = $m[1];
			}

			$web_ini_path = \Orb\Util\Env::getPhpIniPath();
			$is_zendserver = false;
			if ($web_ini_path) {
				$is_zendserver = strpos($web_ini_path, 'ZendServer') !== false;
			}

			$cron_errors = $this->renderView('AdminBundle:Settings:quick-setup-cron-errors.html.twig', array(
				'error_codes'   => $error_codes,
				'ini_path'      => $ini_path,
				'is_zendserver' => $is_zendserver,
				'web_ini_path'  => $web_ini_path,
				'data_dir'      => dp_get_data_dir(),
				'error_log'     => @file_get_contents(dp_get_log_dir() . '/error.log') . "\n\n\n" . @file_get_contents(dp_get_log_dir() . '/cli-phperr.log')
			));
		}

		return $this->createJsonResponse(array(
			'cron_okay' => false,
			'cron_errors' => $cron_errors,
		));
	}

	############################################################################
	# cron-info
	############################################################################

	public function cronAction()
	{
		$setup_initial = $this->container->getSetting('core.setup_initial');

		if ($this->in->getBool('complete')) {
			return $this->redirectRoute('admin');
		}

		$got = $this->container->getPhpBinaryPath();
		if (!$got) {
			$got = '/path/to/php';
		}

		$last_run = $this->container->getSetting('core.cron_last_run');
		$path = realpath(DP_ROOT.'/../');

		return $this->render('AdminBundle:Settings:cron.html.twig', array(
			'last_run' => $last_run,
			'path' => $path,
			'php_path' => $got,
			'found_php_path' => $got != '/path/to/php',
			'show_complete_form' => ($setup_initial < 30)
		));
	}

	############################################################################
	# apps
	############################################################################

	public function appsAction()
	{
		return $this->render('AdminBundle:Settings:apps.html.twig', array(

		));
	}

	public function appToggleAction()
	{
		$name = 'core.apps_' . $this->in->getString('app');
		$on = $this->in->getIbool('enable');

		$this->container->getSettingsHandler()->setSetting($name, $on);

		return $this->redirectRoute('admin');
	}
}
