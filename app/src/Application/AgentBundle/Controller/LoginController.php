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

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\Auth\LoginProcessor;
use Application\DeskPRO\Controller\Helper\LoginHelper;
use Application\DeskPRO\HttpFoundation\UserAgentRequirementCheck;

use Application\DeskPRO\App;
use DeskPRO\Kernel\License;

class LoginController extends \Application\UserBundle\Controller\LoginController
{
	protected $tpl_prefix = 'AgentBundle:Login';
	protected $route_prefix = 'agent';

	/**
	 * Handles showing the login form, and on POST handles login credentials
	 * through the auth adapters.
	 */
	public function indexAction()
	{
		if ($this->loginViaToken()) {
			return $this->redirectRoute($this->route_prefix);
		}

		// Already logged in
		if ($this->session->getPerson() && $this->session->getPerson()->is_agent) {
			return $this->redirectRoute($this->route_prefix);
		}

		$has_done_reset = false;

		if ($code = $this->in->getString('reset_code')) {
			$code_data = $this->em->getRepository('DeskPRO:TmpData')->getByCode($code, 'reset-password');
			$person = null;
			if ($code_data) {
				$person = $this->em->find('DeskPRO:Person', $code_data->getData('person_id', 0));
			}

			if ($code_data AND $person) {
				if ($this->in->getString('new_password')) {
					$has_done_reset = true;

					$person->setPassword($this->in->getString('new_password'));
					$this->db->executeUpdate("
						UPDATE people
						SET
							is_user = 1,
							password_scheme = 'bcrypt',
							`password` = ?
						WHERE id = ?
					", array($person->password, $person->getId()));

					$token = App::getEntityRepository('DeskPRO:ApiToken')->getTokenForPerson($person);
					if ($token) {
						$token->regenerateToken();
						App::getOrm()->persist($token);
					}

					$this->db->delete('tmp_data', array('id' => $code_data->getId()));
				} else {
					return $this->render('AgentBundle:Login:reset-password.html.twig', array(
						'reset_code'    => $this->in->getString('reset_code'),
						'route_prefix'  => $this->route_prefix,
					));
				}
			} else {
				throw $this->createNotFoundException();
			}
		}

		$url = $this->in->getString('return');
		if (!$url) {
			$url = $this->generateUrl('agent', array(), true);
		}
		$has_logged_out = $this->in->checkIsset('o');

		$failed_login_name = false;
		if ($this->session->has('failed_login_name')) {
			$failed_login_name = $this->session->get('failed_login_name');
			$this->session->remove('failed_login_name');
			$this->session->save();
		}

		$logo_blob = null;
		if ($logo_blob_id = $this->settings->get('agent.login_logo_blob_id')) {
			$logo_blob = $this->em->find('DeskPRO:Blob', $logo_blob_id);
		}

		$browser_warnings = UserAgentRequirementCheck::getInterfaceWarnings();

		return $this->render('AgentBundle:Login:index.html.twig', array(
			'return'             => $url,
			'route_prefix'       => $this->route_prefix,
			'logo_blob'          => $logo_blob,
			'has_logged_out'     => $has_logged_out,
			'has_done_reset'     => $has_done_reset,
			'failed_login_name'  => $failed_login_name,
			'browser_warnings'   => $browser_warnings,
			'timeout'            => $this->in->getBool('timeout')
		));
	}

	public function preloadSourcesAction()
	{
		return $this->render('AgentBundle:Login:js-preload.html.twig');
	}

	public function browserRequirementsAction()
	{
		if (UserAgentRequirementCheck::passAgentInterface($this->container->get('browser_sniffer'))) {
			return $this->redirectRoute('agent');
		}

		$browser = $this->container->get('browser_sniffer');

		return $this->render('AgentBundle:Login:browser-requirements.html.twig', array(
			'is_ie' => $browser->isBrowser(\Browser::BROWSER_IE)
		));
	}

	public function ieCompatModeAction()
	{
		return $this->render('AgentBundle:Login:instruct-ie-compat-mode.html.twig', array(

		));
	}

	public function authAdminLoginAction($code)
	{
		$tmp = $this->em->getRepository('DeskPRO:TmpData')->getByCode($code);
		if (!$tmp) {
			return $this->createNotFoundException();
		}

		$admin = $this->container->getAgentData()->get($tmp->getData('admin_id'));
		$person = $this->container->getAgentData()->get($tmp->getData('agent_id'));

		if (!$admin || !$admin->can_admin || !$person || !$person->is_agent) {
			return $this->createNotFoundException();
		}

		$this->session->set('auth_person_id', $person->id);
		$this->session->set('dp_interface', DP_INTERFACE);
		$this->session->save();

		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dplogout')->send();
		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();

		$this->db->insert('login_log', array(
			'person_id'    => $person->getId(),
			'area'         => 'agent',
			'is_success'   => 1,
			'ip_address'   => dp_get_user_ip_address(),
			'hostname'     => @gethostbyaddr(dp_get_user_ip_address()) ?: '',
			'user_agent'   => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
			'note'         => "Admin login by Admin #{$admin->id} {$admin->display_name} <{$admin->email_address}>",
			'date_created' => date('Y-m-d H:i:s')
		));

		return $this->redirectRoute('agent');
	}
}
