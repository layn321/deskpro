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
 * @category Controller
 */

namespace Application\DeskPRO\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Application\DeskPRO\App;

/**
 * The abstract controller sets up some default objects.
 *
 * @property \Doctrine\ORM\EntityManager $em
 * @property \Application\DeskPRO\DBAL\Connection $db
 * @property \Orb\Input\Reader\Reader $in
 * @property \Orb\Input\Cleaner\Cleaner $cleaner
 * @property \Application\DeskPRO\Templating\Engine $tpl
 * @property \Application\DeskPRO\Settings\Settings $settings
 * @property \Application\DeskPRO\HttpFoundation\Session $session
 * @property \Application\DeskPRO\Plugin\PluginRepository $plugins
 */
abstract class AbstractController extends \Application\DeskPRO\HttpKernel\Controller\Controller
{
	public function __get($prop)
	{
		switch ($prop) {
			case 'em': return $this->get('doctrine.orm.entity_manager');
			case 'db': return $this->get('database_connection');
			case 'in': return $this->get('deskpro.core.input_reader');
			case 'cleaner': return $this->get('deskpro.core.input_cleaner');
			case 'settings': return $this->get('deskpro.core.settings');
			case 'session': return $this->get('session');
			case 'tpl': return $this->get('templating');
			case 'plugins': return $this->getContainer()->getSystemService('plugins');
			default:
				throw new \InvalidArgumentException("Unknown property {$prop}");
		}
	}

	/**
	 * Is this a POST request?
	 *
	 * @return bool
	 */
	public function isPostRequest()
	{
		return ($this->get('request')->getMethod() == 'POST');
	}


	/**
	 * Checks a request token in a form
	 *
	 * @param string $name
	 * @param string $field_name
	 * @return bool
	 */
	public function checkRequestToken($name = '', $field_name = '_dp_security_token')
	{
		if (empty($_REQUEST[$field_name])) {
			return false;
		}

		return $this->session->getEntity()->checkSecurityToken($name, $_REQUEST[$field_name]);
	}


	/**
	 * Checks the standard request token.
	 *
	 * @return bool
	 */
	public function checkStandardRequestToken()
	{
		return $this->checkRequestToken('request_token', '_rt');
	}


	/**
	 * Protects against double-submitted requests. If an exact form is submitted a second time, then this method
	 * returns true.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function consumeRequest($name = '')
	{
		$hash = md5($name . App::getRequest()->getUri());
		if (App::getRequest()->getMethod() == 'POST') {
			$hash = md5($hash . serialize($_GET + $_POST));
		}

		$used = $this->session->get('consumed_tokens', array());
		if (in_array($hash, $used)) {
			return false;
		}

		$used[] = $hash;

		while (count($used) > 100) {
			array_shift($used);
		}

		$this->session->set('consumed_tokens', $used);
		$this->session->save();

		return true;
	}


	/**
	 * Just like checkRequestToken but this shows an error for you if its bad
	 *
	 * @param string $name
	 * @param string $field_name
	 */
	public function ensureRequestToken($name = '', $field_name = '_dp_security_token')
	{
		if (!$this->checkRequestToken($name, $field_name)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('invalid_request_token');
		}
	}


	/**
	 * Just like checkRequestToken but this shows an error for you if its bad
	 *
	 * @param string $name
	 * @param string $field_name
	 */
	public function ensureStandardRequestToken()
	{
		return $this->ensureRequestToken('request_token', '_rt');
	}


	/**
	 * Checks a request token $token
	 *
	 * @param string $name
	 * @param string $token
	 * @return bool
	 */
	public function checkAuthToken($name, $token)
	{
		return $this->session->getEntity()->checkSecurityToken($name, $token);
	}


	/**
	 * Just like checkAuthToken but this shows an error for you if its bad
	 *
	 * @param string $name
	 * @param string $field_name
	 */
	public function ensureAuthToken($name, $token)
	{
		if (!$this->checkAuthToken($name, $token)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('invalid_auth_token');
		}
	}


	/**
	 * Just enables 'smart view resoltion' when the at sign is used.
	 *
	 * When the at sign is used, the bundle and optionally the sub-directory can be inferred from the calling controller.
	 * @list.html.twig will get SomeBundle:MyController:list.html.
	 *
	 * @param string $view
	 * @param array $parameters
	 * @param \Symfony\Component\HttpFoundation\Response $response
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function render($view, array $parameters = array(), Response $response = null)
	{
		if ($view[0] == '@') {
			$m = null;
			if (!preg_match('#(Application|Cloud)\\\\([A-Za-z0-9_\-]+)\\\\#', get_class($this), $m)) {
				throw new \InvalidArgumentException("Cannot resolve bundle name with @ notation in `$view`");
			}

			if ($m[1] == 'Cloud') {
				$bundle = 'Cloud' . $m[2];
			} else {
				$bundle = $m[2];
			}

			$c = substr_count($view, ':');
			if ($c == 1) {
				$pre = "$bundle:";
			} else {
				$controller = \Orb\Util\Strings::extractRegexMatch('#\\\\([A-Za-z0-9_\-]+)Controller$#', get_class($this), 1);
				$pre = "$bundle:$controller:";
			}

			$view = preg_replace('#^@#', $pre, $view);
		}

		return parent::render($view, $parameters, $response);
	}
}
