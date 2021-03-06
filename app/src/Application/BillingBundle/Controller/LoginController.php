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

namespace Application\BillingBundle\Controller;

use Application\DeskPRO\Auth\LoginProcessor;
use Application\DeskPRO\Controller\Helper\LoginHelper;

use Application\DeskPRO\App;
use DeskPRO\Kernel\License;

class LoginController extends \Application\UserBundle\Controller\LoginController
{
	protected $tpl_prefix = 'BillingBundle:Login';
	protected $route_prefix = 'billing';

	/**
	 * Handles showing the login form, and on POST handles login credentials
	 * through the auth adapters.
	 */
	public function indexAction()
	{
		if ($this->loginViaToken()) {
			return $this->redirectRoute($this->route_prefix);
		}

		$agent_session_code = $this->in->getString('dpsid-agent');
		$agent_session = null;
		if ($agent_session_code) {
			$agent_session = $this->em->getRepository('DeskPRO:Session')->getSessionFromCode($agent_session_code);
			if (!$agent_session || !$agent_session->person || !$agent_session->person->is_agent) {
				$agent_session = null;
			}
		}

		$url = $this->generateUrl('billing', array(), true);
		return $this->render('BillingBundle:Login:index.html.twig', array('return' => $url, 'agent_session' => $agent_session));
	}

	public function verifyMaLoginRequestAction($license_id, $code)
	{
		$ma_token = $this->em->getRepository('DeskPRO:TmpData')->getByCode($code, 'ma_login');

		if ($ma_token && License::getLicense()->getLicenseId() == $license_id) {
			return $this->createJsonResponse(array("success" => true, "email_address" => $ma_token->getData('email_address')));
		}

		return $this->createJsonResponse(array("error" => true));
	}
}
