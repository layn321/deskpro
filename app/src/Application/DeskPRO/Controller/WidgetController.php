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

namespace Application\DeskPRO\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Util;
use Orb\Util\Strings;

class WidgetController extends AbstractController
{
	/**
	 * The $key is a md5 of the session ID and the app secret.
	 * This is just to verify that all proxy requests are done on purpose
	 * by us.
	 *
	 * @param  $key
	 */
	public function proxyAction($key)
	{
		if (!App::isDebug() OR $key != 'DBEUG') {
			$session = $this->session;
			$check_key = $this->session->getSessionSecret('proxy_key');

			if ($check_key != $key)  {
				return $this->createResponse('Invalid key', 403);
			}
		}

		$url = $this->in->getString('url');
		$urlinfo = @parse_url($url);
		if (!$url OR !$urlinfo OR empty($urlinfo['scheme']) OR !preg_match('#^https?#', $urlinfo['scheme'])) {
			return $this->createResponse('Bad url', 400);
		}

		$ch = curl_init($url);
		if ($this->isPostRequest()) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
		}

		if ($this->request->headers->get('X-DeskPRO-Proxy-Username') OR $this->request->headers->get('X-DeskPRO-Proxy-Password')) {
			curl_setopt($ch, CURLOPT_USERPWD, $this->request->headers->get('X-DeskPRO-Proxy-Username','').':'.$this->request->headers->get('X-DeskPRO-Proxy-Password',''));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}

		if (!empty($_SERVER['CONTENT_TYPE'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: ' . $_SERVER['CONTENT_TYPE']));
		}
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'DeskPRO AJAX Proxy');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		$contents = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		$response = $this->response;

		if ($info['content_type']) {
			$response->headers->set('Content-Type', $info['content_type']);
		}
		if ($info['http_code']) {
			$response->setStatusCode($info['http_code']);
		}

		$response->setContent($contents);

		return $response;
	}



	/**
	 * Saves user preferences for a particular widget.
	 *
	 * @param  $key
	 */
	public function saveUserPrefsAction($key, $widget_id)
	{
		$session = $this->session;

		if (!App::isDebug() OR $key != 'DBEUG') {
			$check_key = md5($session->getId() . App::getAppSecret());

			if ($check_key != $key)  {
				return $this->createResponse('Invalid key', 403);
			}
		}

		$widget = $this->em->getRepository('DeskPRO:Widget')->find($widget_id);
		$pref_prefix = 'widget.' . $widget_id['name_id'] . '.';

		$person = $session->getPerson();

		// If the user is logged in, we can save to prefs
		if ($person['id']) {
			foreach ($this->in->getCleanValueArray('prefs', 'raw', 'string') as $pref_name => $value)
			{
				$pref_name = $pref_prefix . $pref_name;
				$pref = $this->em->getRepository('DeskPRO:PersonPref')->find(array('person_id' => $this->person['id'], 'name' => $pref_name));
				if (!$pref) {
					$pref = new Entity\PersonPref();
					$pref['name'] = $pref_name;
					$this->person->addPreference($pref);
				}

				$pref['value'] = $value;
				$this->em->persist($pref);
			}

			$this->em->flush();

		// Otherwise save to session
		} else {
			foreach ($this->in->getCleanValueArray('prefs', 'raw', 'string') as $pref_name => $value)
			{
				$pref_name = $pref_prefix . $pref_name;
				$session->set($pref_name, $value);
			}
		}

		return $this->createJsonResponse(array(
			'success' => true
		));
	}
}
