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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Auth\LoginProcessor;

class MiscController extends AbstractController
{
	public function preAction($action, $arguments = null)
	{
		if ($action == 'tokenExchangeAction' || $action == 'helpdeskInfoAction') {
			return null;
		}

		return parent::preAction($action, $arguments);
	}

	protected function _checkRateLimit($action, $arguments = null)
	{
		if ($action == 'getRateLimitAction') {
			return null;
		}

		return parent::_checkRateLimit($action, $arguments);
	}

	protected function _updateRateLimit($action, $arguments = null)
	{
		if ($action == 'getRateLimitAction') {
			return;
		}

		parent::_updateRateLimit($action, $arguments);
	}

	public function helpdeskInfoAction()
	{
		$data = array(
			'helpdesk_url' => $this->settings->get('core.deskpro_url')
		);

		if ($this->settings->get('core.rewrite_urls')) {
			$data['api_url'] = $this->settings->get('core.deskpro_url') . '/api/';
		} else {
			$data['api_url'] = $this->settings->get('core.deskpro_url') . '/index.php/api/';
		}

		return $this->createApiResponse($data);
	}

	private function _authLocalInput($email, $password)
	{
		#------------------------------
		# Auth local
		#------------------------------

		$adapter = new \Application\DeskPRO\Auth\Adapter\Local(App::getOrm());
		$adapter->setCredentials($email, $password);
		$result = $adapter->authenticate();

		if ($result->isValid()) {
			return $result;
		}

		#------------------------------
		# Auth usersources that accept local input
		#------------------------------

		$usersources = $this->em->getRepository('DeskPRO:Usersource')->getLocalInputUsersources();
		foreach ($usersources as $us) {

			/** @var $us \Application\DeskPRO\Entity\Usersource */
			$adapter = $us->getAdapter()->getAuthAdapter();
			$adapter->setFormData(array(
				'username' => $email,
				'password' => $password
			));

			try {
				$result = $adapter->authenticate();
			} catch (\Exception $e) {
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

	public function tokenExchangeAction()
	{
		$result = $this->_authLocalInput($this->in->getString('email'), $this->in->getString('password'));

		if (!$result->isValid()) {

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
					'area'         => 'api',
					'is_success'   => 0,
					'ip_address'   => dp_get_user_ip_address(),
					'hostname'     => @gethostbyaddr(dp_get_user_ip_address()) ?: '',
					'user_agent'   => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
					'date_created' => date('Y-m-d H:i:s')
				));
			}

			return $this->createApiErrorResponse('invalid_login', 'Invalid login details', 403);
		}

		$identity = $result->getIdentity();

		$person = $identity['person'];

		if ($person->is_disabled || !$person->is_agent) {
			return $this->createApiErrorResponse('invalid_login', 'Cannot use the API with that person', 403);
		}

		App::setCurrentPerson($person);

		if ($person->getPref('agent_notif.login_attempt.email')) {
			$message = $this->container->getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_agent:login-alert.html.twig', array('success' => true, 'session' => $this->session->getEntity()));
			$message->setTo($person->getPrimaryEmailAddress(), $person->getDisplayName());
			$this->container->getMailer()->send($message);
		}

		// Login log
		$this->db->insert('login_log', array(
			'person_id'    => $person->getId(),
			'area'         => 'api',
			'is_success'   => 1,
			'ip_address'   => dp_get_user_ip_address(),
			'hostname'     => @gethostbyaddr(dp_get_user_ip_address()) ?: '',
			'user_agent'   => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
			'date_created' => date('Y-m-d H:i:s')
		));

		$token = $this->em->getRepository('DeskPRO:ApiToken')->getTokenForPerson($person);
		if (!$token) {
			$token = new \Application\DeskPRO\Entity\ApiToken();
			$token->person = $person;
		} else if ($token->date_expires && $token->date_expires->getTimestamp() < time()) {
			$token->regenerateToken();
		}
		$token->date_expires = null;

		$this->em->persist($token);
		$this->em->flush();

		$data = array(
			'success' => true,
			'api_token' => $token->getKeyString()
		);

		if ($this->in->getBool('return_info')) {
			$api_url = App::getSetting('core.deskpro_url');
			$api_url .= 'index.php/';

			if ($this->getRequest()->isSecure() && strpos($api_url, 'https://') !== 0 && !defined('DPC_IS_CLOUD')) {
				$api_url = preg_replace('#^http://#', 'https://', $api_url);
			}

			$data['api_url'] = $api_url;
			$data['helpdesk_info'] = array(
				'url'  => App::getSetting('core.deskpro_url'),
				'name' => App::getSetting('core.helpdesk_name')
			);
			$data['person_id']    = $person->getId();
			$data['person_info']  = $person->toApiData(true);
		}

		return $this->createApiResponse($data);
	}

	public function renewTokenAction()
	{
		$person = $this->person;

		$token = $this->em->getRepository('DeskPRO:ApiToken')->getTokenForPerson($person);
		if (!$token) {
			$token = new \Application\DeskPRO\Entity\ApiToken();
			$token->person = $person;
		} else if ($token->date_expires && $token->date_expires->getTimestamp() < time()) {
			$token->regenerateToken();
		}
		$token->date_expires = null;

		$this->em->persist($token);
		$this->em->flush();

		$data = array(
			'success' => true,
			'api_token' => $token->getKeyString()
		);

		if ($this->in->getBool('return_info')) {
			$api_url = App::getSetting('core.deskpro_url');

			if (!dp_get_config('rewrite_urls') && strpos($api_url, 'index.php') === false) {
				$api_url .= 'index.php/';
			}

			$data['api_url'] = $api_url;
			$data['helpdesk_info'] = array(
				'url'  => App::getSetting('core.deskpro_url'),
				'name' => App::getSetting('core.helpdesk_name')
			);
			$data['person_id']    = $person->getId();
			$data['person_info']  = $person->toApiData(true);
		}

		return $this->createApiResponse($data);
	}

	public function uploadAction()
	{
		$file = $this->request->files->get('file');
		$accept = $this->container->getAttachmentAccepter();

		$error = $accept->getError($file, 'agent');
		if (!$error && $this->in->getBool('is_image')) {
			$set = new \Application\DeskPRO\Attachments\RestrictionSet();
			$set->setAllowedExts(array('gif', 'png', 'jpg', 'jpeg'));
			$accept->addRestrictionSet('only_images', $set);
			$error = $accept->getError($file, 'only_images');
		}
		if ($error) {
			$message = $this->container->getTranslator()->phrase('agent.general.attach_error_' . $error['error_code'], $error);
			return $this->createApiErrorResponse($error['error_code'], $message);
		}

		$blob = $accept->accept($file);

		return $this->createApiResponse(array('blob' => $blob->toApiData()));
	}

	public function getSessionPersonAction($session_code)
	{
		$session = $this->em->getRepository('DeskPRO:Session')->getSessionFromCode($session_code);
		if (!$session) {
			return $this->createApiErrorResponse('no_session', 'session could not be found or could not be validated');
		}

		if ($session->person && $session->person->id) {
			return $this->createApiResponse(array('person' => $session->person->toApiData()));
		} else {
			return $this->createApiResponse(array('person' => false));
		}
	}

	public function getRateLimitAction()
	{
		if (!App::getSetting('core.api_rate_limit')) {
			return $this->createApiResponse(array(
				'limit' => 0
			));
		}

		if ($this->apikey) {
			$this->rate_info = $this->em->getRepository('DeskPRO:ApiKey')->getRateLimitInfo($this->apikey);
		} else {
			$this->rate_info = $this->em->getRepository('DeskPRO:ApiToken')->getRateLimitInfo($this->api_token);
		}

		return $this->createApiResponse(array(
			'limit' => App::getSetting('core.api_rate_limit'),
			'remaining' => max(0, App::getSetting('core.api_rate_limit') - $this->rate_info['hits']),
			'reset_stamp' => $this->rate_info['reset_stamp'],
			'reset_date' => gmdate('r', $this->rate_info['reset_stamp'])
		));
	}
}
