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
 * @category HttpFoundation
 */

namespace Application\DeskPRO\HttpFoundation;

use Orb\Util\Strings;
use Orb\Util\Util;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Orb\Util\Web;

/**
 * Session is able to load up a user, their language etc.
 */
class Session extends \Symfony\Component\HttpFoundation\Session implements \ArrayAccess, \IteratorAggregate
{
	/**
	 * The person this session belongs to
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * The lang used for this user
	 * @var \Application\DeskPRO\Entity\Language
	 */
	protected $language;

	/**
	 * The current visitor
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor;

	/**
	 * True if this is the first page view of a session.
	 * @var bool
	 */
	protected $is_first_page = false;

	/**
	 * Starts the session storage.
	 */
	public function start()
	{
		if (true === $this->started) {
			return;
		}

		parent::start();

		$this->is_first_page = empty($_SESSION);

		if (DP_INTERFACE != 'admin' && (!empty($_COOKIE['dpreme']) && strpos($_COOKIE['dpreme'], '-') !== false) && (empty($_SESSION['_symfony2']['attributes']['auth_person_id']) || !$_SESSION['_symfony2']['attributes']['auth_person_id'])) {
			list ($person_id, $cookie_code) = explode('-', $_COOKIE['dpreme'], 2);

			$person = App::getEntityRepository('DeskPRO:Person')->find($person_id);
			if ($person && $person->validateRememberMeCookieCode($cookie_code)) {
				$this->_setCurrentPerson($person);

				// Set last login date
				App::getDb()->update('people', array('date_last_login' => date('Y-m-d H:i:s')), array('id' => $person->getId()));

				// Insert log
				if ($person->is_agent) {
					App::getDb()->insert('login_log', array(
						'person_id'    => $person_id,
						'area'         => DP_INTERFACE,
						'is_success'   => 1,
						'ip_address'   => dp_get_user_ip_address(),
						'hostname'     => @gethostbyaddr(dp_get_user_ip_address()) ?: '',
						'user_agent'   => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
						'date_created' => date('Y-m-d H:i:s'),
						'via_cookie'   => 1
					));
				}
			}
		}

		if (DP_INTERFACE == 'user' && $this->is_first_page && empty($_COOKIE['dplogout'])) {
			// user interface and a new session - we need to look through user sources for cookie handlers
			$sources = App::getEntityRepository('DeskPRO:Usersource')->getCookieInputUsersources();
			foreach ($sources AS $source) {
				/** @var $source \Application\DeskPRO\Entity\Usersource */
				$adapter = $source->getAdapter()->getAuthAdapter();

				if ($adapter instanceof \Orb\Auth\Adapter\CookieLoginInterface) {
					$userinfo = $adapter->authenticateCookie($_COOKIE);
					if (!$userinfo) {
						continue;
					}

					$identity = $adapter->getIdentityFromUserInfo($userinfo);

					$login_processor = new \Application\DeskPRO\Auth\LoginProcessor($source, $identity);
					$person = $login_processor->getPerson();

					$this->_setCurrentPerson($person);
					break;
				}
			}
		}

		// See if we need to carry an admin session
		if (DP_INTERFACE == 'user' && !$this->person && isset($_GET['admin_portal_controls'])) {
			$admin_session_code = !empty($_COOKIE['dpsid-admin']) ? $_COOKIE['dpsid-admin'] : false;
			$admin_session = null;
			if ($admin_session_code) {
				$admin_session = App::getEntityRepository('DeskPRO:Session')->getSessionFromCode($admin_session_code);
				if (!$admin_session || !$admin_session->person || !$admin_session->person->is_agent) {
					$admin_session = null;
				}

				if ($admin_session && $admin_session->person) {
					$this->_setCurrentPerson($admin_session->person);
				}
			}
		}

		$user_ip = dp_get_user_ip_address();

		$path = '';
		if (App::getContainer()->isScopeActive('request')) {
			$path = App::getRequest()->getPathInfo();
		}

		$url = '';
		if (App::getContainer()->isScopeActive('request')) {
			$url = App::getRequest()->getUri();
		}

		// Set this for SessionEntityStorage
		// which is usually dumb of the app, but we want to use
		// getClientIp method because we might be using a proxy-passed
		// IP, but dont want to tie the App/container/request into SessionEntityStorage
		$GLOBALS['DP_CURRENT_USER_IP'] = $user_ip;

		// Also make sure the user is a visitor
		$vis = null;
		if ($this->getEntity()->visitor) {
			$vis = $this->getEntity()->visitor;
		} else{
			$vis_id = empty($_COOKIE['dpvc']) ? null : $_COOKIE['dpvc'];
			if ($vis_id) {
				$vis = App::getEntityRepository('DeskPRO:Visitor')->getVisitorFromCode($vis_id);
			}
		}

		$user_token = null;
		if (isset($_COOKIE['dpvut'])) {
			$user_token = $_COOKIE['dpvut'];
		}

		if (!$vis) {
			if ($this->getEntity()->getPersonId()) {
				$vis = App::getEntityRepository('DeskPRO:Visitor')->getVisitorForPerson($this->getEntity()->getPersonId());
			}

			if (!$vis && $user_token) {
				$vis = App::getEntityRepository('DeskPRO:Visitor')->getVisitorFromUserToken($user_token);
			}
		}

		if (!Web::isBotUseragent()) {
			$is_new_vis = false;
			$soft_visitor_id = null;
			if (!$vis) {
				$is_new_vis = true;
				$vis = new Entity\Visitor();
				$vis['page_url']     = $url;
				$vis['ref_page_url'] = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
				$vis['ip_address']   = $user_ip;
				$vis['user_Agent']   = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';

				// If there have been multiple requests from the same ip
				// and those visitor counts arent increasing, it probably means
				// this is a bot or a user without cookies. So prevent the
				// track from being displayed to agents a bajillion times.
				$soft_visitor_id = App::getDb()->fetchColumn("
					SELECT v.id
					FROM visitors v
					LEFT JOIN visitor_tracks AS vt ON (vt.id = v.last_track_id)
					WHERE
						v.date_last > ?
						AND v.page_count = 1
						AND v.hint_hidden = 0
						AND vt.ip_address = ?
					LIMIT 1
				", array(
					date('Y-m-d H:i:s', time() - 600),
					$user_ip
				));

				if ($soft_visitor_id) {
					$vis->hint_hidden = true;
				}
			} else {
				// This was requested a second time, so the user is "real"
				// disbale the hidden hint if it was enabled
				if ($vis->hint_hidden) {
					$vis->hint_hidden = false;
				}

				// Clear out any soft links to this record
				// If there's a page2, then it means any soft-links
				// are not actually theirs.
				// (theyre sending the cookie etc so the "guess" wouldnt be neccessary)
				if ($vis->page_count < 4) {
					App::getDb()->executeUpdate("
						DELETE FROM visitor_tracks
						WHERE visitor_id = ? AND is_soft_track = 1
					", array($vis->getId()));
				}
			}

			$prev_date_last = $vis->date_last;

			if (!$vis->user_token) {
				$vis->user_token = Strings::random(8, Strings::CHARS_KEY);
			}

			$this->visitor = $vis;

			$is_ajax = false;
			if (App::getContainer()->isScopeActive('request')) {
				$is_ajax = App::getRequest()->isXmlHttpRequest();
			}

			// Insert tracks
			$track = null;
			if (!$vis->initial_track || (DP_INTERFACE == 'user' && $url && !preg_match('#/chat/#', $url) && !preg_match('#/widget/#', $url) && !$is_ajax)) {
				$track = array();
				$track['date_created'] = date('Y-m-d H:i:s');
				$track['visitor_id']   = $vis->getId();
				$track['page_url']     = $url;
				$track['ref_page_url'] = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
				$track['ip_address']   = $user_ip;
				$track['user_Agent']   = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';

				if (DP_INTERFACE == 'agent') {
					$track['page_url'] = preg_replace('#/agent/.*?$#', '/agent/', $track['page_url']);
				}

				if (!$vis->initial_track || $prev_date_last->getTimestamp() < time() - 900) {
					$track['is_new_visit'] = 1;
				} else {
					$track['is_new_visit'] = 0;
				}

				$geoip = App::getSystemService('geo_ip');
				$geo = $geoip->lookup($user_ip);

				if (!empty($geo['continent']))      $track['geo_continent'] = $geo['continent'];
				if (!empty($geo['country']))        $track['geo_country']   = $geo['country'];
				if (!empty($geo['region']))         $track['geo_region']    = $geo['region'];
				if (!empty($geo['city']))           $track['geo_city']      = $geo['city'];
				if (!empty($geo['longitude']))      $track['geo_long']      = $geo['longitude'];
				if (!empty($geo['latitude']))       $track['geo_lat']       = $geo['latitude'];
			}

			if ($is_new_vis) {
				App::getOrm()->persist($vis);
				App::getOrm()->flush();
			}

			if ($track) {
				App::getDb()->insert('visitor_tracks' , $track);
				$track['id'] = App::getDb()->lastInsertId();

				$set = array();
				$set_q = array();

				$set[] = "date_last = ?";
				$set_q[] = date('Y-m-d H:i:s');

				if ($vis->user_token) {
					$set[] = "user_token = ?";
					$set_q[] = $vis->user_token;
				}

				if (!$vis->initial_track) {
					$set[] = "initial_track_id = ?";
					$set_q[] = $track['id'];
				}

				if ($track['is_new_visit']) {
					$set[] = "visit_track_id = ?";
					$set_q[] = $track['id'];
				}

				$set[] = "last_track_id = ?";
				$set_q[] = $track['id'];

				if (!$vis->hint_hidden) {
					$set[] = "hint_hidden = 0";
					$set[] = "last_track_id_soft = NULL";
				}

				$set[] = "page_count = page_count + 1";

				foreach (array(
					'page_title',
					'page_url',
					'ref_page_url',
					'user_agent',
					'ip_address',
					'geo_continent',
					'geo_country'
				) as $field) {
					if (isset($track[$field])) {
						$set[] = "`$field` = ?";
						$set_q[] = $track[$field];
					}
				}

				App::getDb()->executeUpdate("
					UPDATE visitors
					SET " . implode(', ', $set) . "
					WHERE id = {$vis->getId()}
				", $set_q);

				$vis->new_track_id = $track['id'];
			}

			if ($track && $soft_visitor_id) {
				// If we suspect this is linked to a different visitor,
				// duplicate the track and set it as the soft link
				$track_dupe = $track;
				unset($track_dupe['id']);
				$track_dupe['visitor_id'] = $soft_visitor_id;
				$track_dupe['is_soft_track'] = 1;

				// Also update the last time so it appears in the agent list
				App::getDb()->insert('visitor_tracks', $track_dupe);
				$soft_track_id = App::getDb()->lastInsertId();

				try {
					App::getDb()->executeUpdate("
						UPDATE visitors
						SET date_last = ?, last_track_id_soft = ?
						WHERE id = ?
					", array(
						date('Y-m-d H:i:s'),
						$soft_track_id,
						$soft_visitor_id
					));
				} catch (\Exception $e) {
					// This could potentially fail with a FK failure
					// if the soft track we just inserted is deleted
					// in another request (theyre deleted once we "know" a user isnt using soft tracks)
				}
			}

			if (!$vis->id || !$this->getEntity()->visitor || $this->getEntity()->visitor->id != $vis->id) {
				$this->getEntity()->visitor = $vis;
				App::getOrm()->persist($this->getEntity());
			}

			if ($vis) {
				$this->set('dpvid', $vis['id']);

				\Application\DeskPRO\HttpFoundation\Cookie::makeCookie('dpvc', $vis['visitor_code'], 'never')->setPath('/')->send();
			} else {
				$this->remove('dpvid');
				\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dpvc')->send();
			}
		} else {
			$this->visitor = null;
			$this->getEntity()->visitor = null;
			$this->remove('dpvid');

			\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dpvc')->send();
		}

        if($this->getPerson() && $this->getPerson()->is_agent && !preg_match('#^/agent/(client-messages/|poller|.*/new)#', $path) && !preg_match('#\.json(\?.*?)?$#', $path)) {
            $agent = $this->getPerson();
            $date_active = new \DateTime();
            list($hour, $minute) = explode(':', $date_active->format('H:i'));
            $minute = intval($minute / 5) * 5;
            $date_active->setTime($hour, $minute, 0);

            App::getDb()->executeQuery('INSERT IGNORE INTO agent_activity(agent_id, date_active) VALUES(?,?)', array($agent['id'], $date_active->format('Y-m-d H:i:s')));
        }

		$this->set('dplast', time());
		if (defined('DP_INTERFACE')) {
			$this->set('dp_interface', DP_INTERFACE);
		}

		$me = $this;
		\DpShutdown::add(function() use ($me) {
			$me->save();
		});
	}


	protected function _setCurrentPerson(\Application\DeskPRO\Entity\Person $person)
	{
		if (DP_INTERFACE == 'user' && $person->is_disabled) {
			// can't login as this person
			return;
		}

		$this->person = $person;
		App::setCurrentPerson($person);
		if ($person->is_agent) {
			$this->attributes['active_status'] = 'available';
			$this->attributes['is_chat_available'] = 1;
		}

		$this->attributes['auth_person_id'] = $person->getId();

		if (!isset($_SESSION['_symfony2'])) {
			$_SESSION['_symfony2'] = array();
		}
		$_SESSION['_symfony2'] = array_merge($_SESSION['_symfony2'], $this->attributes);
	}


	/**
	 * Get the current visitor record
	 *
	 * @return \Application\DeskPRO\Entity\Visitor
	 */
	public function getVisitor()
	{
		return $this->visitor;
	}



	/**
	 * Get the logged in Person
	 *
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getPerson()
	{
		if ($this->person !== null) return $this->person;

		$person_id = $this->get('auth_person_id');
		$person = false;

		if ($person_id) {
			$person = $this->getEntity()->person;
		}

		if (DP_INTERFACE == 'user' && $person && $person->is_disabled) {
			$person = false;
		}

		if (!$person) {
			$person = new \Application\DeskPRO\People\PersonGuest();
		}

		App::setCurrentPerson($person);

		$this->person = $person;

		return $person;
	}



	/**
	 * Get the locale code. Note that this is the string code xx_XX.
	 *
	 * @return string
	 */
	public function getLocale()
	{
		return $this->getLanguage()->getLocale();
	}


	/**
	 * Get the language object
	 *
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public function getLanguage()
	{
		if ($this->language !== null) return $this->language;

		$person = $this->getPerson();
		if ($person && !$person->isGuest()) {
			$this->language = $person->getLanguage();
		} elseif ($this->get('language_id')) {
			$this->language = App::getDataService('Language')->get($this->get('language_id'));
		} elseif (isset($_COOKIE['dplid'])) {
			$this->language = App::getDataService('Language')->get($_COOKIE['dplid']);
		}

		if (!$this->language) {
			$data = App::getDataService('Language');
			$languages = $data->getAll();
			$default_id = $data->getDefaultId();

			$locales = array('');
			foreach ($languages AS $language) {
				$locales[] = $language->locale;
			}

			try {
				// get the highest priority language if available
				$locale = App::getRequest()->getPreferredLanguage($locales);
				$accept_languages = App::getRequest()->getLanguages();
			} catch (\Symfony\Component\DependencyInjection\Exception\InactiveScopeException $e) {
				// the request may not be available, so use the default lang
				$locale = '';
				$accept_languages = array();
			}

			if ($locale) {
				// we have an exact locale match
				foreach ($languages AS $language) {
					if ($language->locale === $locale) {
						$this->language = $language;
						break;
					}
				}
			} else {
				// look for a language match (as there isn't an exact locale match)
				foreach ($accept_languages AS $accept_language) {
					$accept_language = substr($accept_language, 0, 2);
					foreach ($languages AS $language) {
						if (substr($language->locale, 0, 2) == $accept_language) {
							$this->language = $language;
							break 2;
						}
					}
				}
			}

			if (!$this->language && isset($languages[$default_id])) {
				$this->language = $languages[$default_id];
			}
		}

		// still no locale? we might be pre-install, lets use the fake one
		if (!$this->language) {
			$this->language = \Application\DeskPRO\Translate\SystemLanguage::getInstance();
		}

		// Make sure the language is complete for the interface we're seeing
		if (DP_INTERFACE == 'agent' && !$this->language->has_agent) {
			$this->language = \Application\DeskPRO\Translate\SystemLanguage::getInstance();
		} elseif ((DP_INTERFACE == 'admin' || DP_INTERFACE == 'reports' || DP_INTERFACE == 'billing') && (!$this->language->has_agent || !$this->language->has_admin)) {
			$this->language = \Application\DeskPRO\Translate\SystemLanguage::getInstance();
		}

		return $this->language;
	}

	/**
	 * Is this the first page of the session?
	 *
	 * @return bool
	 */
	public function isFirstPage()
	{
		return $this->is_first_page;
	}


	public function clear()
	{
		//$this->attributes = array('_flash' => $this->attributes['_flash'], '_locale' => $this->attributes['_locale']);
	}


	public function getEntityId()
    {
		if ($this->storage instanceof \Application\DeskPRO\HttpFoundation\SessionStorage\SessionEntityStorage) {
        	return $this->storage->getEntityId();
		} else {
			return 0;
		}
    }


	/**
	 * Get a secret string
	 *
	 * @param string $secret
	 * @return string
	 */
	public function getSessionSecret($secret = '')
	{
		return $this->getEntity()->getSessionSecret($secret);
	}


	/**
	 * Check if a security token is valid
	 *
	 * @param $name
	 * @param $token
	 * @return bool
	 */
	public function checkSecurityToken($name, $token)
	{
		return $this->getEntity()->checkSecurityToken($name, $token);
	}


	/**
	 * Generate a new security token
	 *
	 * @param $name
	 * @param int $timeout
	 * @return string
	 */
	public function generateSecurityToken($name, $timeout = 43200)
	{
		return $this->getEntity()->generateSecurityToken($name, $timeout);
	}


	/**
	 * @return \Application\DeskPRO\Entity\Session
	 */
	public function getEntity()
	{
		return $this->storage->getEntity();
	}


	/**
	 * @param string $k
	 * @param mixed $v
	 */
	public function set($k, $v)
	{
		if ($k == 'language_id') {
			$this->language = null;
			$this->getLanguage();
		}

		return parent::set($k, $v);
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->attributes);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetExists($offset)
	{
		return $this->has($offset);
	}

	public function __destruct()
    {
		// We save on our own shutdown caller set up in the constructor,
		// rather than destruct where other objects might've been cleaned up already
    }
}
