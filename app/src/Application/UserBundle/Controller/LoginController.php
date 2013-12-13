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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Controller;

use Application\DeskPRO\Auth\LoginProcessor;
use Application\DeskPRO\Controller\Helper\LoginHelper;
use Application\DeskPRO\Entity\TmpData;

use Application\DeskPRO\App;
use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Util\Strings;
use Orb\Validator\StringEmail;

class LoginController extends \Application\DeskPRO\Controller\AbstractController
{
	protected $tpl_prefix = 'UserBundle:Login';
	protected $route_prefix = 'user';

	/**
	 * @var \Application\DeskPRO\Controller\Helper\LoginHelper
	 */
	protected $login_helper;

	public function init()
	{
		parent::init();

		$this->login_helper = new LoginHelper(
			$this,
			$this->tpl_prefix,
			$this->route_prefix
		);
	}

	protected function loginViaToken()
	{
		if (($token = $this->in->getString('tok')) && strpos($token, '-')) {
			list($person_id, $login_token) = explode('-', $token, 2);
			$person = $this->em->find('DeskPRO:Person', $person_id);
			if ($person && $person->checkPassword($login_token)) {
				$set_active = false;
				if (!$person->date_last_login) {
					$set_active = true;
				}

				if ($set_active) {
					$person->setLastLoginAt();
				}

				$this->em->persist($person);
				$this->em->flush();

				$this->session->set('auth_person_id', $person->getId());
				$this->session->set('dp_interface', DP_INTERFACE);

				App::setCurrentPerson($person);

				// Announce if its an agent
				if ($set_active) {
					$this->session->set('active_status', 'available');
					$this->session->set('is_chat_available', 1);
				}

				$this->session->save();

				return true;
			}
		}

		return false;
	}

	/**
	 * Handles showing the login form, and on POST handles login credentials
	 * through the auth adapters.
	 */
	public function indexAction()
	{
		if ($this->loginViaToken()) {
			return $this->redirectRoute($this->route_prefix);
		}

		$return = $this->in->getStringFromGet('return');
		if ($return AND ($return[0] != '/' || strpos($return, '/validate-email/') !== false)) {
			// Always be a path on the current domain,
			// or else it might be a trick to go to some other domain etc
			$return = '';
		}

		if ($this->session->getPerson()->getId()) {
			if ($return) return $this->redirect($return);
			else return $this->redirectRoute('user');
		}

		$register = new \Application\UserBundle\Form\Model\Register();
		$tpl_globals = $this->container->get('templating.globals');
		if ($tpl_globals->getVariable('login_with_email')) {
			$register->email = $tpl_globals->getVariable('login_with_email');
		}

		$reg_formtype = new \Application\UserBundle\Form\RegisterType();
		$form = $this->get('form.factory')->create($reg_formtype, $register);

		$failed_login_name = false;
		if ($this->session->has('failed_login_name')) {
			$failed_login_name = $this->session->get('failed_login_name');
			$this->session->remove('failed_login_name');
			$this->session->save();
		}

		$account_disabled = false;
		if ($this->session->has('account_disabled')) {
			$account_disabled = $this->session->get('account_disabled');
			$this->session->remove('account_disabled');
			$this->session->save();
		}

		$captcha = null;
		if ($this->container->getSetting('user.register_captcha')) {
			$captcha = $this->container->getSystemObject('form_captcha', array('type' => 'user_reg'));
		}

		return $this->render($this->tpl_prefix . ':index.html.twig', array(
			'return' => $return,
			'route_prefix' => $this->route_prefix,
			'form' => $form->createView(),
			'failed_login_name' => $failed_login_name,
			'account_disabled' => $account_disabled,
			'captcha' => $captcha,
		));
	}

	protected function _logoutPerson()
	{
		// When an agent actually logs out, we should be clearing the state
		$person = $this->session->getPerson();
		if ($person['is_agent']) {
			$this->db->executeUpdate("
				DELETE FROM people_prefs
				WHERE person_id = ? AND name = ?
			", array($person['id'], 'agent.ui.state'));
		}

		$this->session->replace(array());
		$this->session->save();

		foreach (array('dpsid-agent', 'dpsid-admin', 'dpreme') as $cookie_name) {
			if (!empty($_COOKIE[$cookie_name])) {
				$sess2 = $this->em->getRepository('DeskPRO:Session')->getSessionFromCode($_COOKIE[$cookie_name]);
				if ($sess2) {
					$this->em->remove($sess2);
					$this->em->flush();
				}
			}

			$cookie = \Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie($cookie_name);
			$cookie->send();
		}

		App::setCurrentPerson(new \Application\DeskPRO\People\PersonGuest());

		\Application\DeskPRO\HttpFoundation\Cookie::makeCookie('dplogout', 1, 0)->send();
		// delete this, we'll recreate it as some settings may have changed
		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();
	}

	public function logoutAction($auth)
	{
		if (!\Orb\Util\Util::checkStaticSecurityToken($auth, md5(App::getAppSecret() . 'user_logout'))) {
			return $this->redirectRoute('user');
		}

		$this->_logoutPerson();

		if ($this->in->getString('quicklogout') == 'ajax') {
			if ($this->in->getString('callback')) {
				return $this->createJsonpResponse(array('logged_out' => true));
			} else {
				return $this->createJsonResponse(array('logged_out' => true));
			}
		} elseif ($this->in->getString('quicklogout') == 'pop') {
			$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript">
window.close();
window.onload = function() { window.close(); };
</script>
</head>
<body>
</body>
</html>
HTML;

			$this->createResponse($html);
		}

		if ($this->in->getString('to') == 'admin') {
			return $this->redirect($this->request->getBaseUrl() . '/admin/login?o');
		} elseif ($this->in->getString('to') == 'agent') {
			return $this->redirect($this->request->getBaseUrl() . '/agent/login?o');
		} else {
			if ($this->in->getString('via') == 'user_chat') {
				return $this->redirectRoute('user_widget_chat');
			}
			return $this->redirectRoute('user');
		}
	}

	public function authenticateLocalAction($usersource_id)
	{
		if ($this->request->getMethod() != 'POST') {
			return $this->redirectRoute('user_login');
		}

		if (!$this->in->getBool('agent_login') && !$this->consumeRequest('user_login')) {
			return $this->redirectRoute($this->route_prefix . '_login');
		}

		$return = $this->in->getString('return');
		if ($return AND ($return[0] != '/' || strpos($return, '/validate-email/') !== false)) {
			$return = '';
		}

		$result = $this->authLocalInput();

		// Form wasnt inputted (eg direct url)
		if (!$this->in->getString('email') || !$this->in->getString('password')) {

			if ($this->getRequest()->getMethod() == 'POST') {
				$this->session->set('failed_login_name', true);
				$this->session->save();
				return $this->redirectRoute($this->route_prefix . '_login', array('return' => $return));
			}

			return $this->redirectRoute($this->route_prefix . '_login', array('return' => $return));
		}

		if (!$result->isValid()) {

			// If this is an agent or admin and its an ldap error, show them an actual error page
			if (isset($GLOBALS['DP_AUTH_EXCEPTION']) && isset($GLOBALS['DP_AUTH_EXCEPTION_ADAPTER'])) {
				$adapter = $GLOBALS['DP_AUTH_EXCEPTION_ADAPTER'];
				if (DP_INTERFACE != 'user' && ($adapter instanceof \Application\DeskPRO\Usersource\Adapter\Ldap || $adapter instanceof \Application\DeskPRO\Usersource\Adapter\ActiveDirectory)) {
					if (!extension_loaded('ldap')) {
						return $this->render('UserBundle:Main:error-standard.html.twig', array(
							'error_message' => 'LDAP Extension Required',
							'error_title'   => 'Your server does not have the LDAP extension enabled so your login could not be processed. See: http://www.php.net/manual/en/ldap.installation.php',
						));
					}
				}
			}

			// Send alert
			$attempt_person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($this->in->getString('email'));
			if ($attempt_person && $attempt_person->getPref('agent_notif.login_attempt_fail.email')) {
				$message = $this->container->getMailer()->createMessage();
				$message->setTemplate('DeskPRO:emails_agent:login-alert.html.twig', array('success' => false, 'session' => $this->session->getEntity()));
				$message->setTo($attempt_person->getPrimaryEmailAddress(), $attempt_person->getDisplayName());
				$this->container->getMailer()->send($message);
			}

			// Save login log
			if ($attempt_person) {
				$this->db->insert('login_log', array(
					'person_id'    => $attempt_person->getId(),
					'area'         => defined('DP_INTERFACE') ? DP_INTERFACE : 'unknown',
					'is_success'   => 0,
					'ip_address'   => dp_get_user_ip_address(),
					'hostname'     => @gethostbyaddr(dp_get_user_ip_address()) ?: '',
					'user_agent'   => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
					'date_created' => date('Y-m-d H:i:s')
				));
			}

			$this->session->set('failed_login_name', $this->in->getString('email'));
			$this->session->save();
			return $this->redirectRoute($this->route_prefix . '_login', array('return' => $return));
		}

		$identity = $result->getIdentity();

		$person = $identity['person'];

		if ($person->is_disabled || $this->container->getSystemService('email_address_validator')->personHasBannedEmail($person)) {
			$this->session->set('account_disabled', $person->id);
			$this->session->save();
			return $this->redirectRoute($this->route_prefix . '_login', array('return' => $return));
		}

		if (!isset($GLOBALS['DP_LOGIN_VIA_TOKEN'])) {
			$person->setLastLoginAt();
		}
		$this->em->persist($person);
		$this->em->flush();

		$this->session->set('auth_person_id', $identity->getIdentity());
		$this->session->set('dp_interface', DP_INTERFACE);
		$this->session->save();

		App::setCurrentPerson($person);

		if ($person['is_agent']) {

			// Announce if its an agent
			if (!isset($GLOBALS['DP_LOGIN_VIA_TOKEN'])) {
				// Set their status to available by default
				$this->session->set('active_status', 'available');
				$this->session->set('is_chat_available', 1);

				$data = array(
					'agent_id'   => $person['id'],
					'agent_name' => $person['display_name'],
					'agent_short_name' => $person->getDisplayContactShort(4),
					'picture_url' => $person->getPictureUrl(10)
				);

				$cm = new \Application\DeskPRO\Entity\ClientMessage();
				$cm->fromArray(array(
					'channel' => 'agent.new-agent-online',
					'data' => $data,
					'created_by_client' => $this->session->getEntityId(),
				));

				// Send alert
				if ($person->getPref('agent_notif.login_attempt.email')) {
					$message = $this->container->getMailer()->createMessage();
					$message->setTemplate('DeskPRO:emails_agent:login-alert.html.twig', array('success' => true, 'session' => $this->session->getEntity()));
					$message->setTo($person->getPrimaryEmailAddress(), $person->getDisplayName());
					$this->container->getMailer()->send($message);
				}

				// Login log
				$this->db->insert('login_log', array(
					'person_id'    => $person->getId(),
					'area'         => defined('DP_INTERFACE') ? DP_INTERFACE : 'unknown',
					'is_success'   => 1,
					'ip_address'   => dp_get_user_ip_address(),
					'hostname'     => @gethostbyaddr(dp_get_user_ip_address()) ?: '',
					'user_agent'   => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
					'date_created' => date('Y-m-d H:i:s')
				));

				$this->em->persist($cm);
				$this->em->flush();
			}
		}

		// Remember me cookie
		if ($this->in->getBool('remember_me')) {
			$cookie = \Application\DeskPRO\HttpFoundation\Cookie::makeCookie(
				'dpreme',
				$person->getId() . '-' . $person->getRememberMeCookieCode(),
				'never',
				true,
				\Orb\Util\Web::getRequestProtocol() == 'HTTPS' ? true : false
			);
			$cookie->send();
		}

		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dplogout')->send();
		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();

		if ($login_validate_comments = App::getSession()->get('login_validate_comments')) {

			foreach ($login_validate_comments as $validate_info) {
				$comment = $this->em->find($validate_info[0], $validate_info[1]);
				if (!$comment) {
					continue;
				}

				$comment->status = 'validating';
				$this->em->getConnection()->beginTransaction();

				try {
					$this->em->persist($comment);
					$this->em->flush();

					$this->em->getConnection()->commit();
				} catch (\Exception $e) {
					$this->em->getConnection()->rollback();
					throw $e;
				}
			}

			App::getSession()->remove('login_validate_comments');
			App::getSession()->save();
		}

		$this->_doLoginSuccess();

		if ($return) {
			return $this->redirect($return);
		} else {
			return $this->redirectRoute($this->route_prefix);
		}
	}

	public function _doLoginSuccess()
	{
		return;
	}


	/**
	 * A generic landing page after the user has logged in which has JS to alert
	 * its parent that the user is now logged in.
	 *
	 * This is used when the auth happens in a popup, and then the page that spawned
	 * the popup needs to know the user is finished.
	 */
	public function jstellLoginAction($security_token, $usersource_id)
	{
		if (!$this->session->getEntity()->checkSecurityToken('jstell', $security_token)) {
			return $this->createResponse('');
		}

		return $this->render('UserBundle:Login:jstell.html.twig', array(
			'route_prefix' => $this->route_prefix,
		));
	}


	public function authLocalInput()
	{
		#------------------------------
		# Auth local
		#------------------------------

		if ($this->container->getSetting('core.deskpro_source_enabled') || DP_INTERFACE != 'user') {
			$adapter = new \Application\DeskPRO\Auth\Adapter\Local(App::getOrm());
			$adapter->setCredentials($this->in->getString('email'), $this->in->getString('password'));
			$result = $adapter->authenticate();

			if ($result->isValid()) {
				return $result;
			}
		}

		#------------------------------
		# Auth usersources that accept local input
		#------------------------------

		$usersources = $this->em->getRepository('DeskPRO:Usersource')->getLocalInputUsersources();
		foreach ($usersources as $us) {

			/** @var $us \Application\DeskPRO\Entity\Usersource */
			$adapter = $this->_initUserSourceAdapter($us);
			$adapter->setFormData(array(
				'username' => $this->in->getString('email'),
				'password' => $this->in->getString('password')
			));

			try {
				$result = $adapter->authenticate();
			} catch (\Exception $e) {
				KernelErrorHandler::logException($e, false);
				$GLOBALS['DP_AUTH_EXCEPTION_ADAPTER'] = $adapter;
				$GLOBALS['DP_AUTH_EXCEPTION'] = $e;
				continue;
			}

			if ($result->isValid()) {
				$login_processor = new LoginProcessor($us, $result->getIdentity());
				$person = $login_processor->getPerson();

				$identity = new \Orb\Auth\Identity($person->id, array('person' => $person));
				$result = new \Orb\Auth\Result(\Orb\Auth\Result::SUCCESS, $identity);

				return $result;
			}
		}

		return new \Orb\Auth\Result(\Orb\Auth\Result::FAILURE_INVALID_CREDS);
	}


	############################################################################
	# Usersource auth
	############################################################################

	public function authenticateAction($usersource_id)
	{
		$return = $this->in->getString('return');

		$usersource = $this->em->find('DeskPRO:Usersource', $usersource_id);
		if (!$usersource) {
			throw $this->createNotFoundException();
		}
		$adapter = $this->_initUserSourceAdapter($usersource, $this->in->getString('context'));

		#------------------------------
		# Callback types require us to redirect
		#------------------------------

		if ($adapter instanceof \Orb\Auth\Adapter\CallbackInterface) {
			$result = $adapter->authenticate();

			// The user is already logged in
			if ($result->isValid()) {

				$login_processor = new LoginProcessor($usersource, $result->getIdentity());
				$person = $login_processor->getPerson();
				$person->setLastLoginAt();

				$this->em->persist($person);
				$this->em->flush();

				$this->_setupUsersourceSession($usersource, $person, $result);

				if ($this->in->getString('js_tell')) {
					$return = $this->generateUrl('user_jstell_login', array(
						'jstell' => $this->in->getString('js_tell'),
						'security_token' => $this->session->getEntity()->generateSecurityToken('jstell'),
						'usersource_id' => $usersource_id
					));
					return $this->redirect($return);
				}

				if ($this->session->get('auth_return')) {
					$return = $this->session->get('auth_return');
					$this->session->remove('auth_return');
					$this->session->save();
					return $this->redirect($return);
				} else {
					return $this->redirectRoute($this->route_prefix);
				}

			// We expect a redirect to be rquired
			} elseif ($result->isRedirectRequired()) {

				$return = $this->in->getString('return');
				$this->session->set('auth_return', $return);

				if ($this->in->getString('js_tell')) {
					$return = $this->generateUrl('user_jstell_login', array(
						'jstell' => $this->in->getString('js_tell'),
						'security_token' => $this->session->getEntity()->generateSecurityToken('jstell'),
						'usersource_id' => $usersource_id
					), true);
					$this->session->set('auth_return', $return);
				}

				$this->session->save();

				return $this->redirect($result->getRedirectUrl());

			// Otherwise its an error
			} else {
				$this->session->setFlash('login_failed', true);
				return $this->redirectRoute($this->route_prefix . '_login', array('return' => $return));
			}

		#------------------------------
		# Other types should return a result right away
		#------------------------------

		} else {
			$result = $adapter->authenticate();

			// Valid
			if ($result->isValid()) {

				$login_processor = new LoginProcessor($usersource, $result->getIdentity());
				$person = $login_processor->getPerson();

				$this->_setupUsersourceSession($usersource, $person, $result);

				$return = $this->in->getString('return');
				if ($return) {
					return $this->redirect($return);
				} else {
					return $this->redirectRoute($this->route_prefix);
				}

			// Error, go back to login
			} else {
				$this->session->setFlash('login_failed', true);
				return $this->redirectRoute($this->route_prefix . '_login', array('return' => $return));
			}
		}
	}

	public function authenticateCallbackAction($usersource_id)
	{
		$return = $this->in->getString('return');
		$usersource = $this->em->find('DeskPRO:Usersource', $usersource_id);

		if (!$usersource) {
			throw $this->createNotFoundException();
		}

		$adapter = $this->_initUserSourceAdapter($usersource);

		// It must be a callback type to be here, so if not redirect back to login
		if (!($adapter instanceof \Orb\Auth\Adapter\CallbackInterface)) {
			$this->session->setFlash('login_failed', true);
			return $this->redirectRoute($this->route_prefix . '_login', array('return' => $return));
		}

		$adapter->setCallbackContext($_REQUEST);

		$result = $adapter->authenticate();

		// Valid
		if ($result->isValid()) {

			$login_processor = new LoginProcessor($usersource, $result->getIdentity());
			$person = $login_processor->getPerson();

			$this->_setupUsersourceSession($usersource, $person, $result);

			if ($this->session->get('auth_return')) {
				$return = $this->session->get('auth_return');
				$this->session->remove('auth_return');
				$this->session->save();
				return $this->redirect($return);
			} else {
				return $this->redirectRoute($this->route_prefix);
			}

		// Error, go back to login
		} else {
			$this->session->setFlash('login_failed', true);
			return $this->redirectRoute($this->route_prefix . '_login', array('return' => $return));
		}
	}

	public function resetPasswordAction($invalid_email = false, $invalid_code = false)
	{
		if ($this->session->getPerson()->getId()) {
			return $this->redirectRoute('user');
		}

		$register = new \Application\UserBundle\Form\Model\Register();

		$tpl_globals = $this->container->get('templating.globals');
		if ($tpl_globals->getVariable('login_with_email')) {
			$register->email = $tpl_globals->getVariable('login_with_email');
		}

		$reg_formtype = new \Application\UserBundle\Form\RegisterType();
		$form = $this->get('form.factory')->create($reg_formtype, $register);

		return $this->render($this->tpl_prefix . ':reset-password.html.twig', array(
			'route_prefix' => $this->route_prefix,
			'invalid_email' => $invalid_email,
			'invalid_code' => $invalid_code,
			'form' => $form->createView(),
			'invalid' => $this->in->getBool('inv'),
		));
	}

	protected function _initUserSourceAdapter($usersource, $context = null)
	{
		$adapter = $usersource->getAdapter()->getAuthAdapter();

		if (App::getConfig('debug.enable_usersource_log') && $adapter instanceof \Orb\Log\Loggable) {
			$adapter->setLogger($this->_getAdapterLogger());
		}

		if ($adapter instanceof \Orb\Auth\Adapter\FormLoginInterface) {
			$adapter->setFormData($_POST);
		}

		if ($context && $adapter instanceof \Orb\Auth\Adapter\DisplayContextInterface) {
			$adapter->setDisplayContext($context);
		}

		if ($adapter instanceof \Orb\Auth\Adapter\CallbackInterface) {
			$adapter->setCallbackUrl(
				rtrim($this->container->getSetting('core.deskpro_url'), '/') .
				$this->generateUrl('user_login_callback', array('usersource_id' => $usersource['id']), false)
			);
		}

		if ($adapter instanceof \Orb\Auth\Adapter\SessionStateInterface) {
			$auth_state = new \Orb\Auth\StateHandler\ArrayAccessWrapper($this->session);
			$auth_state->setClearStateMethod('clear');

			$adapter->setStateHandler($auth_state);
		}

		return $adapter;
	}

	protected function _getAdapterLogger()
	{
		static $logger = null;

		if ($logger === null) {
			$logger = new \Orb\Log\Logger();
			$logger->addWriter(new \Orb\Log\Writer\Stream($this->container->getLogDir() . '/usersource_log.log'));
		}

		return $logger;
	}

	############################################################################
	# Resetting passwords
	############################################################################

	public function sendResetPasswordAction($_format = 'html')
	{
		$email = $this->in->getString('email');

		if (!$email || !StringEmail::isValueValid($email)) {
			return $this->redirectRoute('user_login_resetpass', array('inv' => 1));
		}

		$person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($email);

		if (!$person) {

			// If no user was found in our database, then the account might not have
			// been set up yet. For adapters that support it, we can still see if we
			// can be helpful and redirect to another source they exist in
			$usersources = $this->em->getRepository('DeskPRO:Usersource')->getUserInfoFetchableUsersources();
			foreach ($usersources as $us) {
				$found = $us->findIdentityByInput($email);
				if ($found && $us->lost_password_url) {
					return $this->redirect($us->lost_password_url);
				}
			}

			if ($this->request->isXmlHttpRequest()) {
				return $this->createJsonResponse(array('error' => 'invalid_email'));
			}

			// Default is to just show standard message to not reveal if account exists
			return $this->render($this->tpl_prefix . ':reset-password-sent.html.twig', array(
				'route_prefix' => $this->route_prefix,
			));
		}

		// If they dont have a password, this either means they're not a user yet,
		// but could also mean they registered through a usersource which means they might
		// need to use a different reset URL
		if (!$person->password) {
			$associations = $this->em->getRepository('DeskPRO:PersonUsersourceAssoc')->getAssociationsForPerson($person);
			$us_names = array();

			foreach ($associations as $assoc) {
				$us_names[] = $assoc->usersource->getTitle();
				if ($assoc->usersource->lost_password_url) {
					if ($this->request->isXmlHttpRequest()) {
						return $this->createJsonResponse(array('status' => 'usersource_redirect', 'usersource_name' => $assoc->usersource->getTitle(), 'url' => $assoc->usersource->lost_password_url));
					}
					return $this->redirect($assoc->usersource->lost_password_url);
				}
			}

			if ($us_names) {
				if ($this->request->isXmlHttpRequest()) {
					return $this->createJsonResponse(array('status' => 'usersource_no_reset', 'usersource_name' => implode(', ', $us_names)));
				}

				// No other user sources for the user
				// Default is to just show standard message to not reveal if account exists
				return $this->render($this->tpl_prefix . ':reset-password-sent.html.twig', array(
					'route_prefix' => $this->route_prefix,
				));
			}
		}

		// Admins cant reset their password, but we dont want to reveal to this unknown user that we're an admin
		// Send an email instead
		if (!defined('DPC_IS_CLOUD')) {
			if ($person->can_admin) {
				$vars = array(
					'person' => $person,
					'email' => $email
				);

				$message = $this->container->getMailer()->createMessage();
				$message->setTemplate('DeskPRO:emails_agent:admin-noreset-password.html.twig', $vars);
				$message->setTo($email, $person->getDisplayName());
				$message->disableQueueHint();
				$this->container->getMailer()->send($message);

				if ($_format == 'json') {
					return $this->createJsonResponse(array('success' =>1 ));
				}

				return $this->render($this->tpl_prefix . ':reset-password-sent.html.twig', array(
					'route_prefix' => $this->route_prefix,
				));
			}
		}

		// If they're still here, then we just send them through the normal DeskPRO reset procedure

		$code_data = TmpData::create('reset-password', array('person_id' => $person['id'], 'interface' => DP_INTERFACE), '+3 days');
		$this->em->persist($code_data);
		$this->em->flush();

		$vars = array(
			'code'      => $code_data->getCode(),
			'person'    => $person,
			'email'     => $email,
			'interface' => DP_INTERFACE
		);

		$message = $this->container->getMailer()->createMessage();
		$message->setTemplate('DeskPRO:emails_user:reset-password.html.twig', $vars);
		$message->setTo($email, $person->getDisplayName());
		$message->disableQueueHint();

		$this->container->getMailer()->send($message);

		if ($_format == 'json') {
			return $this->createJsonResponse(array('success' =>1 ));
		}

		$this->session->remove('auth_person_id');
		$this->session->save();

		return $this->render($this->tpl_prefix . ':reset-password-sent.html.twig', array(
			'route_prefix' => $this->route_prefix,
		));
	}

	public function resetPasswordNewPassAction($code)
	{
		$code_data = $this->em->getRepository('DeskPRO:TmpData')->getByCode($code, 'reset-password');
		$person = null;
		if ($code_data) {
			$person = $this->em->find('DeskPRO:Person', $code_data->getData('person_id', 0));
		}

		if (!$code_data OR !$person OR $code_data->getData('is_used')) {
			return $this->render('UserBundle:Login:reset-password-badcode.html.twig', array(
				'route_prefix' => $this->route_prefix,
			));
		}

		$errors = array();
		if ($this->in->getBool('process')) {
			$pass = $this->in->getString('password');
			$pass2 = $this->in->getString('password2');

			if ($pass != $pass2) {
				$errors['password.mismatch'] = 1;
			} elseif (\Orb\Util\Strings::utf8_strlen($pass) < 5) {
				$errors['password.short'] = 1;
			}

			if (!$errors) {

				$is_new_user = !$person->is_user;

				$person->setPassword($pass);
				$code_data->setData('is_used', true);

				$this->em->transactional(function ($em) use ($person, $code_data) {
					$em->persist($person);
					$em->persist($code_data);
					$em->flush();
				});

				if ($is_new_user) {
					$user_rule_proc = new \Application\DeskPRO\People\UserRuleProcessor(App::getOrm());
					$user_rule_proc->newRegister($person);
				}

				$this->session->setFlash('password_reset', 1);

				$this->session->set('auth_person_id', $person->getId());
				$this->session->set('dp_interface', DP_INTERFACE);
				$this->session->save();

				if ($ticket_ref = $this->session->get('ticket_from_ptac_register')) {
					$this->session->remove('ticket_from_ptac_register');
					$this->session->save();

					return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket_ref));
				}

				return $this->redirectRoute($this->route_prefix . '_login');
			}
		}

		return $this->render($this->tpl_prefix . ':reset-password-newpass.html.twig', array(
			'code' => $code_data->getCode(),
			'route_prefix' => $this->route_prefix,
			'errors' => $errors
		));
	}

	public function resetPasswordNewPassQueryCode()
	{
		return $this->resetPasswordNewPassAction($this->in->getString('reset_code'));
	}


	############################################################################
	# Inline login
	############################################################################

	public function inlineLoginAction()
	{
		$result = $this->authLocalInput();

		if (!$result->isValid()) {
			$html = $this->renderView('UserBundle:Common:form-email-login-row.html.twig', array('login_error' => true, 'mode' => $this->in->getString('mode')));
			return $this->createJsonResponse(array(
				'html' => $html,
			));
		}

		$identity = $result->getIdentity();

		$this->session->set('auth_person_id', $identity->getIdentity());

		$person = $identity['person'];
		$person->setLastLoginAt();
		$person->loadHelper('FeedbackVotes', array(
			'visitor' => $this->session->getVisitor()
		));
		$person->loadHelper('HelpdeskUser', array(
			'session' => $this->session,
			'visitor' => $this->session->getVisitor()
		));

		$this->person = $person;

		App::setCurrentPerson($person);

		$this->em->persist($person);
		$this->em->flush();

		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dplogout')->send();
		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();

		$html = $this->renderView('UserBundle:Common:form-email-login-row.html.twig', array('person' => $person, 'mode' => $this->in->getString('mode')));

		return $this->createJsonResponse(array(
			'html' => $html,
			'sections_replace' => array(

			),
			'person_id' => $person['id'],
			'name' => $person['name']
		));
	}

	############################################################################
	# agent-login
	############################################################################

	public function authAgentLoginAction($code)
	{
		$tmp = $this->em->getRepository('DeskPRO:TmpData')->getByCode($code);
		if (!$tmp) {
			return $this->createNotFoundException();
		}

		$agent  = $this->container->getAgentData()->get($tmp->getData('agent_id'));
		$person = $this->em->getRepository('DeskPRO:Person')->find($tmp->getData('person_id'));

		if (!$agent || !$agent->is_agent || !$agent->hasPerm('agent_people.login_as') || !$person || $person->is_agent) {
			return $this->createNotFoundException();
		}

		$this->session->set('auth_person_id', $person->id);
		$this->session->set('dp_interface', DP_INTERFACE);
		$this->session->save();

		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dplogout')->send();
		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();

		$this->db->insert('login_log', array(
			'person_id'    => $person->getId(),
			'area'         => 'user',
			'is_success'   => 1,
			'ip_address'   => dp_get_user_ip_address(),
			'hostname'     => @gethostbyaddr(dp_get_user_ip_address()) ?: '',
			'user_agent'   => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
			'note'         => "Agent login by Admin #{$agent->id} {$agent->display_name} <{$agent->email_address}>",
			'date_created' => date('Y-m-d H:i:s')
		));

		// Log to activity log
		$this->db->insert('person_activity', array(
			'person_id'    => $person->id,
			'action_type'  => 'agent_login_as',
			'date_created' => date('Y-m-d H:i:s'),
			'details'      => serialize(array(
				'agent_id'    => $agent->id,
				'agent_name'  => $agent->display_name,
				'agent_email' => $agent->email_address
			))
		));

		return $this->redirectRoute('user_profile');
	}

	############################################################################
	# Usersource SSO
	############################################################################

	public function usersourceSsoAction($usersource_id)
	{
		/** @var $source \Application\DeskPRO\Entity\Usersource */
		$source = $this->em->getRepository('DeskPRO:Usersource')->findOneById($usersource_id);

		$available = ($source && $source->is_enabled);
		if ($available) {
			$available = $source->getAdapter()->isCapable('js_sso');
		}

		if (!$available) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$adapter = $source->getAdapter()->getAuthAdapter();
		$result = $adapter->getSsoLoginActionResult($this);

		if ($result->isValid()) {
			$login_processor = new LoginProcessor($source, $result->getIdentity());
			$person = $login_processor->getPerson();

			$this->_setupUsersourceSession($source, $person, $result);
		}

		$return = $this->in->getString('return');
		if ($return) {
			return $this->redirect($return);
		} else {
			return $this->redirectRoute($this->route_prefix);
		}
	}

	protected function _setupUsersourceSession(
		\Application\DeskPRO\Entity\Usersource $usersource,
		\Application\DeskPRO\Entity\Person $person,
		\Orb\Auth\Result $result
	)
	{
		$this->session->set('auth_person_id', $person['id']);
		$this->session->set('dp_interface', DP_INTERFACE);
		$this->session->set('auth_usersource_id', $usersource->id);
		$this->session->set('auth_usersource_type', $usersource->source_type);
		$this->session->set('usersource_display_name', $usersource->getAdapter()->getDisplayName($result->getIdentity()->getRawData()));
		$this->session->set('usersource_display_link', $usersource->getAdapter()->getDisplayLink($result->getIdentity()->getRawData()));
		$this->session->save();

		App::setCurrentPerson($person);

		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dplogout')->send();
		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();
	}
}
