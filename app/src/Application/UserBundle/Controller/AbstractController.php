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

namespace Application\UserBundle\Controller;

use Application\DeskPRO\App;
use Orb\Util\Strings;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class AbstractController extends \Application\DeskPRO\Controller\AbstractController
{
	/**
	 * The currently logged in person.
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public $person;

	protected $search_query = '';

	protected function init()
	{
		parent::init();

		$tpl_globals = $this->container->get('templating.globals');
		if (!$tpl_globals->getVariable('usersources')) {
			 $tpl_globals->setVariable('usersources', $this->em->getRepository('DeskPRO:Usersource')->getAllUsersources());
		}

		if ($this->in->getString('q')) {
			$this->search_query = $this->in->getString('q');
		} else {
			$referrer = $this->request->headers->get('Referer');
			if ($referrer && ($q = Strings::extractRegexMatch('#search\?q=(.*?)(&|$)#', $referrer, 1))) {
				$this->search_query = urldecode($q);
			}
		}
		$tpl_globals->setVariable('search_query', $this->search_query);
	}

	/**
	 * Check if the global request token check is required for the request
	 */
	public function requireRequestToken($action, $arguments = null)
	{
		if ($this->request->getMethod() == 'POST') {
			global $DP_CONFIG;
			if (!empty($DP_CONFIG['cache']['page_cache']['enable']) && (!$this->person || !$this->person->getId())) {
				return false;
			}

			return true;
		}
	}

	public function preAction($action, $arguments = null)
	{
		$this->person = $this->session->getPerson();

		if (
			($set_lang_id = $this->in->getString('language_id'))
			&& ($set_lang = $this->container->getDataService('Language')->get($set_lang_id))
		) {
			// Set cookie too so it lasts after session expires
			$cookie = \Application\DeskPRO\HttpFoundation\Cookie::makeCookie('dplid', $set_lang_id, 'never', true);
			$cookie->send();

			$this->person->language = $set_lang;
			App::getTranslator()->setLanguage($set_lang);

			if (!$this->person->isGuest()) {
				$this->em->persist($this->person);
				$this->em->flush();
			}

			$this->session->set('language_id', $set_lang->getId());
			$this->session->save();
		}

		if ($this->in->getBool('admin_portal_controls')) {
			if ($this->person->id && !$this->person->can_admin) {
				$this->person = new \Application\DeskPRO\People\PersonGuest();;
			}

			$cas = new \Application\AgentBundle\Controller\Helper\CarryAdminSession($this);
			$cas->process();

			// With admin portal controls, give permission to the sections even if we dont usually
			$this->person->getPermissionsManager()->enableAdminMode();
		}

		$this->person->loadHelper('FeedbackVotes', array(
			'visitor' => $this->session->getVisitor()
		));
		$this->person->loadHelper('HelpdeskUser', array(
			'session' => $this->session,
			'visitor' => $this->session->getVisitor()
		));

		if ($this instanceof RequireUserInterface) {
			if (!$this->person['id']) {
				if ($this->isPostRequest()) {
					$return = $this->get('router')->generate('user');
				} else {
					$return = $this->request->getRequestUri();
				}

				$redirect_url = $this->get('router')->generate('user_login', array('return' => $return));
				return $this->redirect($redirect_url);
			}
		}

		static $done_pcheck;
		if (!$done_pcheck && $action != 'articleAgentIframeAction') {
			$done_pcheck = true;
			if (!$this->sectionPermissionCheck()) {
				return $this->renderLoginOrPermissionError();
			}
		}

		$tpl_globals = $this->container->get('templating.globals');
		if ($this->in->getBool('admin_portal_controls') && $this->person->can_admin) {
			$tpl_globals->setVariable('admin_portal_controls', true);
			$tpl_globals->setVariable('custom_templates', $this->db->fetchAllKeyValue("SELECT name,id FROM templates"));
		}

		if (
			!($this instanceof LoginController || $this instanceof MainController || $this instanceof ProfileController || $this instanceof PortalController)
			AND !($this instanceof TicketsController && preg_match('#^feedback#', $action))
			AND !$this->person->HelpdeskUser->canDoAnything()
			AND !$tpl_globals->getVariable('admin_portal_controls')
			AND $this->request_type == HttpKernelInterface::MASTER_REQUEST
		) {

			if ($this instanceof PortalController) {
				return $this->redirectRoute('user_profile');
			}

			if ($this->isPostRequest()) {
				$return = $this->get('router')->generate('user');
			} else {
				$return = $this->request->getRequestUri();
			}

			return $this->renderLoginOrPermissionError($return);
		}

		#------------------------------
		# Portal display order
		#------------------------------

		if (!$tpl_globals->getVariable('portal_tabs_order')) {
			$val = App::getSetting('user.portal_tabs_order');

			if ($val) {
				$val = explode(',', $val);
				$val = \Orb\Util\Arrays::removeFalsey($val);
			} else {
				$val = array();
			}

			$val = array_merge($val, array(
				'articles',
				'news',
				'feedback',
				'downloads',
				'newticket'
			));

			$val = array_unique($val);

			$admin_controls = $tpl_globals->getVariable('admin_portal_controls');
			foreach ($val as &$tabtype) {
				switch ($tabtype) {
					case 'news':
						if (!($admin_controls || ($this->container->getSetting('user.portal_tab_news') && $this->person->hasPerm('news.use')))) {
							$tabtype = false;
						}
						break;
					case 'articles':
						if (!($admin_controls || ($this->container->getSetting('user.portal_tab_articles') && $this->person->hasPerm('articles.use')))) {
							$tabtype = false;
						}
						break;
					case 'feedback':
						if (!($admin_controls || ($this->container->getSetting('user.portal_tab_feedback') && $this->person->hasPerm('feedback.use')))) {
							$tabtype = false;
						}
						break;
					case 'downloads':
						if (!($admin_controls || ($this->container->getSetting('user.portal_tab_downloads') && $this->person->hasPerm('downloads.use')))) {
							$tabtype = false;
						}
						break;
					case 'newticket':
						if (!($admin_controls || ($this->container->getSetting('user.portal_tab_tickets') && $this->person->hasPerm('tickets.use')))) {
							$tabtype = false;
						}
						break;
				}
			}

			$val = \Orb\Util\Arrays::removeFalsey($val);

			$tpl_globals->setVariable('portal_tabs_order', $val);
		}

		if ($this->requireRequestToken($action, $arguments) && !$this->checkRequestToken('request_token', '_rt')) {
			if ($this->request->isXmlHttpRequest()) {
				$data = array(
					'error' => 'invalid_request_token',
					'redirect_login' => $this->generateUrl('agent_login')
				);

				return $this->createJsonResponse($data, 403);
			} else {
				return $this->renderStandardError('The form you are trying to submit has expired. Please go back and try again.');
			}
		}
	}


	/**
	 * Method called after getting session. Meant to be used in controllers as a top-level check to see if they
	 * can use a resource. Eg. can use articles.
	 *
	 * @return bool
	 */
	public function sectionPermissionCheck()
	{
		return true;
	}


	/**
	 * Renders the login form if the user isn't logged in, or a standard permission error if they are already logged in.
	 */
	public function renderLoginOrPermissionError($return_url = '', $type = 'login')
	{
		if ($this->person->getId()) {
			return $this->renderStandardError('@user.error.permission-denied');
		}


		$act = 'UserBundle:Login:index';
		if ($type == 'reset') {
			$act = 'UserBundle:Login:resetPassword';
		} elseif ($type == 'register') {
			$act = 'UserBundle:Register:register';
		}

		return $this->forward($act, array(), array('return' => $return_url));
	}


	/**
	 * Render a standard error message.
	 *
	 * @param string $error_message
	 * @param string $error_title
	 * @return Response
	 */
	public function renderStandardError($error_message = '', $error_title = '', $code = 200, array $vars = array())
	{
		if ($error_message AND $error_message[0] == '@') {
			$error_message = App::getTranslator()->getPhraseText(substr($error_message, 1));
		}

		if ($error_title AND $error_title[0] == '@') {
			$error_title = App::getTranslator()->getPhraseText(substr($error_title, 1));
		}

		return $this->standardErrorResponse($error_message, $error_title, $code, $vars);
	}


	/**
	 * @param string $error_message
	 * @param string $error_title
	 * @param int $code
	 * @param array $vars
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function standardErrorResponse($error_message = '', $error_title = '', $code = 200, array $vars = array())
	{
		$tpl_standard = 'UserBundle:Main:error-standard.html.twig';
		$tpl_specific = "UserBundle:Main:error-{$code}.html.twig";

		$tpl = $tpl_standard;
		if (App::getTemplating()->exists($tpl_specific)) {
			$tpl = $tpl_specific;
		}

		$vars = array_merge($vars, array(
			'error_message' => $error_message,
			'error_title'   => $error_title
		));

		$res = $this->render($tpl, $vars);

		$res->setStatusCode($code);

		return $res;
	}

	/**
	 * @return Response
	 */
	public function renderStandardTokenError()
	{
		return $this->renderStandardError('@user.error.expired-token');
	}
}
