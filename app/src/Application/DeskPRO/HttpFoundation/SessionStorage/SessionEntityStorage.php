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

namespace Application\DeskPRO\HttpFoundation\SessionStorage;

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Util;


/**
 * This storage uses the Session entity for storing session info.
 */
class SessionEntityStorage implements \Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface
{
	static protected $sessionIdRegenerated = false;
    static protected $sessionStarted       = false;

	protected $options;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
    protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Application\DeskPRO\Entity\Session
	 */
	protected $session;

	protected $last_save_hash = null;

    public function __construct(\Doctrine\ORM\EntityManager $em, $options = null)
    {
        $this->em = $em;
		$this->db = $em->getConnection();

        $cookieDefaults = session_get_cookie_params();

		$cookie_name = 'dpsid';
		if (DP_INTERFACE == 'agent' || DP_INTERFACE == 'report' || DP_INTERFACE == 'billing') {
			$cookie_name .= '-agent';
		} elseif (DP_INTERFACE == 'admin') {
			$cookie_name .= '-admin';
		}

		$this->options['name'] = $cookie_name;

		$cookieDefaults['domain'] = App::getSetting('core.cookie_domain');
		$cookieDefaults['path']   = App::getSetting('core.cookie_path');

		if (\Orb\Util\Web::getRequestProtocol() == 'HTTPS') {
			$cookieDefaults['secure'] = true;
		}

        $this->options = array_merge(array(
            'name'          => $cookie_name,
            'lifetime'      => $cookieDefaults['lifetime'],
            'path'          => $cookieDefaults['path'],
            'domain'        => $cookieDefaults['domain'],
            'secure'        => $cookieDefaults['secure'],
            'httponly'      => isset($cookieDefaults['httponly']) ? $cookieDefaults['httponly'] : false,
        ), $options);

        session_name($this->options['name']);
    }



    /**
     * Starts the session.
     */
	public function start()
	{
		if (self::$sessionStarted) {
			return;
		}

		session_set_save_handler(
			array($this, 'sessionOpen'),
			array($this, 'sessionClose'),
			array($this, 'sessionRead'),
			array($this, 'sessionWrite'),
			array($this, 'sessionDestroy'),
			array($this, 'sessionGC')
		);

		// this is COOKIE liftime. We always want it to be a session cookie
		// (exists until browser closes). It shouldnt be the lifetime of the session,
		// that is a seprate matter. If this is say an hour, then the users cookie is removed
		// after an hour and they loose their session even though it's still valid on the server.
		$this->options['lifetime'] = 0;

		session_set_cookie_params(
			$this->options['lifetime'],
			$this->options['path'],
			$this->options['domain'],
			$this->options['secure'],
			$this->options['httponly']
		);

		// disable native cache limiter as this is managed by HeaderBag directly
		session_cache_limiter(false);

		// We want to use our own sessionid's, so we have to do this check
		// to see if we need to create a new entity
		$session_id = empty($_COOKIE[$this->options['name']]) ? null : $_COOKIE[$this->options['name']];
		if (isset($_REQUEST['__sid'])) {
			$session_id = $_REQUEST['__sid'];
		}
		$session = null;
		if ($session_id) {
			$session = $this->em->getRepository('DeskPRO:Session')->getSessionFromCode($session_id);
		}

		// Sessions are deleted on cron, but we'll also enforce it here
		$cutoff = time() - App::getSetting('core.sessions_lifetime');

		if (!$session OR $session['date_last']->getTimestamp() < $cutoff) {
			$session = new \Application\DeskPRO\Entity\Session();

			if (\Orb\Util\Web::isBotUseragent()) {
				$session->is_bot = true;
			}

			$this->em->persist($session);
			$this->em->flush();

			session_id($session->getSessionCode());
		}

		if ($session) {
			$this->session = $session;
		}

		session_start();

		self::$sessionStarted = true;
	}



    /**
     * Opens a session.
     *
     * @param  string $path  (ignored)
     * @param  string $name  (ignored)
     *
     * @return boolean true, if the session was opened, otherwise an exception is thrown
     */
    public function sessionOpen($path = null, $name = null)
    {
        return true;
    }



    /**
     * Closes a session.
     *
     * @return boolean true, if the session was closed, otherwise false
     */
    public function sessionClose()
    {
        return true;
    }



    /**
     * Destroys a session.
     *
     * @param  string $id  A session ID
     *
     * @return bool   true, if the session was destroyed, otherwise an exception is thrown
     *
     * @throws \RuntimeException If the session cannot be destroyed
     */
    public function sessionDestroy($id)
    {
		if ($this->session && $this->session->getSessionCode() == $id) {
			$session = $this->session;
		} else {
			$session = $this->em->getRepository('DeskPRO:Session')->getSessionFromCode($id);
		}

		$this->em->remove($session);
		$this->em->flush();

        return true;
    }



    /**
     * Cleans up old sessions. This is a noop, sessions are cleaned on cron.
     *
     * @param  int $lifetime  The lifetime of a session in seconds
     * @return bool true
     * @throws \RuntimeException If any old sessions cannot be cleaned
     */
    public function sessionGC($lifetime)
    {
        return true;
    }



    /**
     * Reads a session.
     *
     * @param  string $id  A session ID
     *
     * @return string      The session data if the session was read or created, otherwise an exception is thrown
     *
     * @throws \RuntimeException If the session cannot be read
     */
    public function sessionRead($id)
    {
		$sid = self::getIdFromCode($id);
		if ($this->session && $this->session->getSessionCode() == $id) {
			$session = $this->session;
		} else {
			$session = $this->em->getRepository('DeskPRO:Session')->getSessionFromCode($id);
		}

		if ($session) {
			$this->session = $session;
			return $session['data'];
		}

		return '';
    }



    /**
     * Writes session data.
     *
     * @param  string $id    A session ID
     * @param  string $data  A serialized chunk of session data
     *
     * @return bool true, if the session was written, otherwise an exception is thrown
     *
     * @throws \RuntimeException If the session data cannot be written
     */
    public function sessionWrite($id, $data)
    {
		// Because of when the session is written, we cant use the ORM here,
		// because the manager has lost its reference to the session state
		$id = self::getIdFromCode($id);

		$save_hash = md5($id . $data);

		// No changes were made to the session
		if ($this->last_save_hash && $this->last_save_hash == $save_hash) {
			return true;
		}

		$this->last_save_hash = $save_hash;

		$sess_rec = array();
		$sess_rec['data'] = $data;
		$sess_rec['date_last'] = isset($_SESSION['_symfony2']['attributes']['dplast']) ? date('Y-m-d H:i:s', $_SESSION['_symfony2']['attributes']['dplast']) : date('Y-m-d H:i:s', time());
		$sess_rec['is_person'] = 0;
		$sess_rec['person_id'] = null;
		$sess_rec['visitor_id'] = (isset($_SESSION['_symfony2']['attributes']['dpvid']) ? $_SESSION['_symfony2']['attributes']['dpvid'] : null);

		if (!empty($GLOBALS['DP_CURRENT_USER_IP'])) {
			$sess_rec['ip_address'] = $GLOBALS['DP_CURRENT_USER_IP'];
		} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$sess_rec['ip_address'] = $_SERVER['REMOTE_ADDR'];
		}

		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			$sess_rec['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		}

		if (!empty($_SESSION['_symfony2']['attributes']['auth_person_id'])) {
			$sess_rec['is_person'] = 1;
			$sess_rec['person_id'] = $_SESSION['_symfony2']['attributes']['auth_person_id'];

			if (!empty($_SESSION['_symfony2']['attributes']['dp_interface']) && $_SESSION['_symfony2']['attributes']['dp_interface'] == 'agent') {
				if (!empty($_SESSION['_symfony2']['attributes']['active_status'])) {
					$sess_rec['active_status'] = $_SESSION['_symfony2']['attributes']['active_status'];
				} else {
					$sess_rec['active_status'] = '';
				}
				if ($sess_rec['active_status'] == 'available') {
					$sess_rec['is_chat_available'] = isset($_SESSION['_symfony2']['attributes']['is_chat_available']) ? (int)$_SESSION['_symfony2']['attributes']['is_chat_available'] : 0;
				} else {
					$sess_rec['is_chat_available'] = 0;
				}
			}
		} else {
			$sess_rec['active_status'] = '';
			$sess_rec['is_chat_available'] = 0;
		}

		if (isset($_SESSION['_symfony2']['attributes']['dp_interface'])) {
			$sess_rec['interface'] = $_SESSION['_symfony2']['attributes']['dp_interface'];

			if ($sess_rec['interface'] != 'agent') {
				$sess_rec['active_status'] = '';
				$sess_rec['is_chat_available'] = 0;
			}
		} else {
			$sess_rec['interface'] = '';
		}

		if (isset($GLOBALS['DP_NON_HELPDESK_SESSION']) && !$this->session->is_helpdesk) {
			$sess_rec['is_helpdesk'] = 0;
		} else {
			$sess_rec['is_helpdesk'] = 1;
		}

		try {
			$this->db->update('sessions', $sess_rec, array('id' => $id));
		} catch (\Exception $e) {
			// Cron periodically clears things like visitor tracks, so there could in rare
			// cases be an update where the visitor is no longer valid by the time this session is written
			unset($sess_rec['visitor_id']);
			$this->db->update('sessions', $sess_rec, array('id' => $id));
		}

        return true;
    }

	public function getId()
	{
		if (!self::$sessionStarted) {
			throw new \RuntimeException('The session must be started before reading its ID');
		}

		return session_id();
	}

	public function getEntityId()
	{
		$id = $this->getId();
		list($entity_id, ) = explode('-', $id, 2);
		$entity_id = Util::baseDecode($entity_id, 'base36');

		return $entity_id;
	}

	public function getEntity()
	{
		if (!$this->session) {
			$this->session = App::getEntityRepository('DeskPRO:Session')->find($this->getEntityId());
		}

		return $this->session;
	}

    /**
     * Reads data from this storage.
     *
     * The preferred format for a key is directory style so naming conflicts can be avoided.
     *
     * @param string $key A unique key identifying your data
     *
     * @return mixed Data associated with the key
     */
    public function read($key, $default = null)
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    /**
     * Removes data from this storage.
     *
     * The preferred format for a key is directory style so naming conflicts can be avoided.
     *
     * @param  string $key  A unique key identifying your data
     *
     * @return mixed Data associated with the key
     */
    public function remove($key)
    {
        $retval = null;

        if (isset($_SESSION[$key])) {
            $retval = $_SESSION[$key];
            unset($_SESSION[$key]);
        }

        return $retval;
    }

    /**
     * Writes data to this storage.
     *
     * The preferred format for a key is directory style so naming conflicts can be avoided.
     *
     * @param string $key   A unique key identifying your data
     * @param mixed  $data  Data associated with your key
     *
     */
    public function write($key, $data)
    {
        $_SESSION[$key] = $data;
    }

    /**
     * Regenerates id that represents this storage.
     *
     * @param  Boolean $destroy Destroy session when regenerating?
     *
     * @return Boolean True if session regenerated, false if error
     *
     */
    public function regenerate($destroy = false)
    {
        if (self::$sessionIdRegenerated) {
            return;
        }

        session_regenerate_id($destroy);

        self::$sessionIdRegenerated = true;
    }


	/**
	 * @static
	 * @param string $sess_code
	 * @return int|null
	 */
	public static function getIdFromCode($sess_code)
	{
		if (!strpos($sess_code, '-')) return null;

		list ($session_id, ) = explode('-', $sess_code, 2);

		$alphabet = str_split('0123456789abcdefghijklmnopqrstuvwxyz');
		$base     = sizeof($alphabet);
		$strlen   = strlen($session_id);
		$num = 0;
		$idx = 0;

		$s = str_split($session_id);
		$tebahpla = array_flip($alphabet);

		foreach ($s as $char) {
			// Invalid character found in string
			if (!isset($tebahpla[$char])) {
				return null;
			}
			$power = ($strlen - ($idx + 1));
			$num += $tebahpla[$char] * (pow($base, $power));
			$idx += 1;
		}
		return $num;
	}
}
