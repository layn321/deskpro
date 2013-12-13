<?php
  



namespace Symfony\Bundle\FrameworkBundle\EventListener
{

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;


class SessionListener
{
    private $container;
    private $autoStart;

    public function __construct(ContainerInterface $container, $autoStart = false)
    {
        $this->container = $container;
        $this->autoStart = $autoStart;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (!$this->container->has('session')) {
            return;
        }

        $request = $event->getRequest();
        if ($request->hasSession()) {
            return;
        }

        $request->setSession($session = $this->container->get('session'));

        if ($this->autoStart || $request->hasPreviousSession()) {
            $session->start();
        }
    }
}
}
 



namespace Symfony\Component\HttpFoundation\SessionStorage
{


interface SessionStorageInterface
{
    
    public function start();

    
    public function getId();

    
    public function read($key);

    
    public function remove($key);

    
    public function write($key, $data);

    
    public function regenerate($destroy = false);
}
}
 




namespace Application\DeskPRO\HttpFoundation
{

use Orb\Util\Strings;
use Orb\Util\Util;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Orb\Util\Web;


class Session extends \Symfony\Component\HttpFoundation\Session implements \ArrayAccess, \IteratorAggregate
{
	
	protected $person;

	
	protected $language;

	
	protected $visitor;

	
	protected $is_first_page = false;

	
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

								App::getDb()->update('people', array('date_last_login' => date('Y-m-d H:i:s')), array('id' => $person->getId()));

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
						$sources = App::getEntityRepository('DeskPRO:Usersource')->getCookieInputUsersources();
			foreach ($sources AS $source) {
				
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

										$GLOBALS['DP_CURRENT_USER_IP'] = $user_ip;

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
												if ($vis->hint_hidden) {
					$vis->hint_hidden = false;
				}

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
												$track_dupe = $track;
				unset($track_dupe['id']);
				$track_dupe['visitor_id'] = $soft_visitor_id;
				$track_dupe['is_soft_track'] = 1;

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


	
	public function getVisitor()
	{
		return $this->visitor;
	}



	
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



	
	public function getLocale()
	{
		return $this->getLanguage()->getLocale();
	}


	
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
								$locale = App::getRequest()->getPreferredLanguage($locales);
				$accept_languages = App::getRequest()->getLanguages();
			} catch (\Symfony\Component\DependencyInjection\Exception\InactiveScopeException $e) {
								$locale = '';
				$accept_languages = array();
			}

			if ($locale) {
								foreach ($languages AS $language) {
					if ($language->locale === $locale) {
						$this->language = $language;
						break;
					}
				}
			} else {
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

				if (!$this->language) {
			$this->language = \Application\DeskPRO\Translate\SystemLanguage::getInstance();
		}

				if (DP_INTERFACE == 'agent' && !$this->language->has_agent) {
			$this->language = \Application\DeskPRO\Translate\SystemLanguage::getInstance();
		} elseif ((DP_INTERFACE == 'admin' || DP_INTERFACE == 'reports' || DP_INTERFACE == 'billing') && (!$this->language->has_agent || !$this->language->has_admin)) {
			$this->language = \Application\DeskPRO\Translate\SystemLanguage::getInstance();
		}

		return $this->language;
	}

	
	public function isFirstPage()
	{
		return $this->is_first_page;
	}


	public function clear()
	{
			}


	public function getEntityId()
    {
		if ($this->storage instanceof \Application\DeskPRO\HttpFoundation\SessionStorage\SessionEntityStorage) {
        	return $this->storage->getEntityId();
		} else {
			return 0;
		}
    }


	
	public function getSessionSecret($secret = '')
	{
		return $this->getEntity()->getSessionSecret($secret);
	}


	
	public function checkSecurityToken($name, $token)
	{
		return $this->getEntity()->checkSecurityToken($name, $token);
	}


	
	public function generateSecurityToken($name, $timeout = 43200)
	{
		return $this->getEntity()->generateSecurityToken($name, $timeout);
	}


	
	public function getEntity()
	{
		return $this->storage->getEntity();
	}


	
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
				    }
}
}
 



namespace Symfony\Component\HttpFoundation\SessionStorage
{


class NativeSessionStorage implements SessionStorageInterface
{
    protected static $sessionIdRegenerated = false;
    protected static $sessionStarted       = false;

    protected $options;

    
    public function __construct(array $options = array())
    {
        $cookieDefaults = session_get_cookie_params();

        $this->options = array_merge(array(
            'lifetime' => $cookieDefaults['lifetime'],
            'path'     => $cookieDefaults['path'],
            'domain'   => $cookieDefaults['domain'],
            'secure'   => $cookieDefaults['secure'],
            'httponly' => isset($cookieDefaults['httponly']) ? $cookieDefaults['httponly'] : false,
        ), $options);

                if (isset($this->options['name'])) {
            session_name($this->options['name']);
        }
    }

    
    public function start()
    {
        if (self::$sessionStarted) {
            return;
        }

        session_set_cookie_params(
            $this->options['lifetime'],
            $this->options['path'],
            $this->options['domain'],
            $this->options['secure'],
            $this->options['httponly']
        );

                session_cache_limiter(false);

        if (!ini_get('session.use_cookies') && isset($this->options['id']) && $this->options['id'] && $this->options['id'] != session_id()) {
            session_id($this->options['id']);
        }

        session_start();

        self::$sessionStarted = true;
    }

    
    public function getId()
    {
        if (!self::$sessionStarted) {
            throw new \RuntimeException('The session must be started before reading its ID');
        }

        return session_id();
    }

    
    public function read($key, $default = null)
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    
    public function remove($key)
    {
        $retval = null;

        if (isset($_SESSION[$key])) {
            $retval = $_SESSION[$key];
            unset($_SESSION[$key]);
        }

        return $retval;
    }

    
    public function write($key, $data)
    {
        $_SESSION[$key] = $data;
    }

    
    public function regenerate($destroy = false)
    {
        if (self::$sessionIdRegenerated) {
            return;
        }

        session_regenerate_id($destroy);

        self::$sessionIdRegenerated = true;
    }
}
}
 



namespace Symfony\Component\Routing\Matcher
{

use Symfony\Component\Routing\RequestContextAwareInterface;


interface UrlMatcherInterface extends RequestContextAwareInterface
{
    
    public function match($pathinfo);
}
}
 



namespace Symfony\Component\Routing\Generator
{

use Symfony\Component\Routing\RequestContextAwareInterface;


interface UrlGeneratorInterface extends RequestContextAwareInterface
{
    
    public function generate($name, $parameters = array(), $absolute = false);
}
}
 



namespace Symfony\Component\Routing
{

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;


interface RouterInterface extends UrlMatcherInterface, UrlGeneratorInterface
{
}
}
 



namespace Symfony\Component\Routing\Matcher
{

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;


class UrlMatcher implements UrlMatcherInterface
{
    protected $context;
    protected $allow;

    private $routes;

    
    public function __construct(RouteCollection $routes, RequestContext $context)
    {
        $this->routes = $routes;
        $this->context = $context;
    }

    
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    
    public function getContext()
    {
        return $this->context;
    }

    
    public function match($pathinfo)
    {
        $this->allow = array();

        if ($ret = $this->matchCollection($pathinfo, $this->routes)) {
            return $ret;
        }

        throw 0 < count($this->allow)
            ? new MethodNotAllowedException(array_unique(array_map('strtoupper', $this->allow)))
            : new ResourceNotFoundException();
    }

    protected function matchCollection($pathinfo, RouteCollection $routes)
    {
        $pathinfo = urldecode($pathinfo);

        foreach ($routes as $name => $route) {
            if ($route instanceof RouteCollection) {
                if (false === strpos($route->getPrefix(), '{') && $route->getPrefix() !== substr($pathinfo, 0, strlen($route->getPrefix()))) {
                    continue;
                }

                if (!$ret = $this->matchCollection($pathinfo, $route)) {
                    continue;
                }

                return $ret;
            }

            $compiledRoute = $route->compile();

                        if ('' !== $compiledRoute->getStaticPrefix() && 0 !== strpos($pathinfo, $compiledRoute->getStaticPrefix())) {
                continue;
            }

            if (!preg_match($compiledRoute->getRegex(), $pathinfo, $matches)) {
                continue;
            }

                        if ($req = $route->getRequirement('_method')) {
                                if ('HEAD' === $method = $this->context->getMethod()) {
                    $method = 'GET';
                }

                if (!in_array($method, $req = explode('|', strtoupper($req)))) {
                    $this->allow = array_merge($this->allow, $req);

                    continue;
                }
            }

            return array_merge($this->mergeDefaults($matches, $route->getDefaults()), array('_route' => $name));
        }
    }

    protected function mergeDefaults($params, $defaults)
    {
        $parameters = $defaults;
        foreach ($params as $key => $value) {
            if (!is_int($key)) {
                $parameters[$key] = rawurldecode($value);
            }
        }

        return $parameters;
    }
}
}
 



namespace Symfony\Component\Routing\Generator
{

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;


class UrlGenerator implements UrlGeneratorInterface
{
    protected $context;
    protected $decodedChars = array(
                '%2F' => '/',
    );

    protected $routes;
    protected $cache;

    
    public function __construct(RouteCollection $routes, RequestContext $context)
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->cache = array();
    }

    
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    
    public function getContext()
    {
        return $this->context;
    }

    
    public function generate($name, $parameters = array(), $absolute = false)
    {
        if (null === $route = $this->routes->get($name)) {
            throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
        }

        if (!isset($this->cache[$name])) {
            $this->cache[$name] = $route->compile();
        }

        return $this->doGenerate($this->cache[$name]->getVariables(), $route->getDefaults(), $route->getRequirements(), $this->cache[$name]->getTokens(), $parameters, $name, $absolute);
    }

    
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute)
    {
        $variables = array_flip($variables);

        $originParameters = $parameters;
        $parameters = array_replace($this->context->getParameters(), $parameters);
        $tparams = array_replace($defaults, $parameters);

                if ($diff = array_diff_key($variables, $tparams)) {
            throw new MissingMandatoryParametersException(sprintf('The "%s" route has some missing mandatory parameters ("%s").', $name, implode('", "', array_keys($diff))));
        }

        $url = '';
        $optional = true;
        foreach ($tokens as $token) {
            if ('variable' === $token[0]) {
                if (false === $optional || !array_key_exists($token[3], $defaults) || (isset($parameters[$token[3]]) && (string) $parameters[$token[3]] != (string) $defaults[$token[3]])) {
                    if (!$isEmpty = in_array($tparams[$token[3]], array(null, '', false), true)) {
                                                if ($tparams[$token[3]] && !preg_match('#^'.$token[2].'$#', $tparams[$token[3]])) {
                            throw new InvalidParameterException(sprintf('Parameter "%s" for route "%s" must match "%s" ("%s" given).', $token[3], $name, $token[2], $tparams[$token[3]]));
                        }
                    }

                    if (!$isEmpty || !$optional) {
                        $url = $token[1].strtr(rawurlencode($tparams[$token[3]]), $this->decodedChars).$url;
                    }

                    $optional = false;
                }
            } elseif ('text' === $token[0]) {
                $url = $token[1].$url;
                $optional = false;
            }
        }

        if (!$url) {
            $url = '/';
        }

                $extra = array_diff_key($originParameters, $variables, $defaults);
        if ($extra && $query = http_build_query($extra, '', '&')) {
            $url .= '?'.$query;
        }

        $url = $this->context->getBaseUrl().$url;

        if ($this->context->getHost()) {
            $scheme = $this->context->getScheme();
            if (isset($requirements['_scheme']) && ($req = strtolower($requirements['_scheme'])) && $scheme != $req) {
                $absolute = true;
                $scheme = $req;
            }

            if ($absolute) {
                $port = '';
                if ('http' === $scheme && 80 != $this->context->getHttpPort()) {
                    $port = ':'.$this->context->getHttpPort();
                } elseif ('https' === $scheme && 443 != $this->context->getHttpsPort()) {
                    $port = ':'.$this->context->getHttpsPort();
                }

                $url = $scheme.'://'.$this->context->getHost().$port.$url;
            }
        }

        return $url;
    }
}
}
 



namespace Symfony\Component\Routing\Matcher
{


interface RedirectableUrlMatcherInterface
{
    
    public function redirect($path, $route, $scheme = null);
}
}
 



namespace Symfony\Component\Routing
{


interface RequestContextAwareInterface
{
    
    public function setContext(RequestContext $context);
}
}
 



namespace Symfony\Component\Routing
{


class RequestContext
{
    private $baseUrl;
    private $method;
    private $host;
    private $scheme;
    private $httpPort;
    private $httpsPort;
    private $parameters;

    
    public function __construct($baseUrl = '', $method = 'GET', $host = 'localhost', $scheme = 'http', $httpPort = 80, $httpsPort = 443)
    {
        $this->baseUrl = $baseUrl;
        $this->method = strtoupper($method);
        $this->host = $host;
        $this->scheme = strtolower($scheme);
        $this->httpPort = $httpPort;
        $this->httpsPort = $httpsPort;
        $this->parameters = array();
    }

    
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    
    public function getMethod()
    {
        return $this->method;
    }

    
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }

    
    public function getHost()
    {
        return $this->host;
    }

    
    public function setHost($host)
    {
        $this->host = $host;
    }

    
    public function getScheme()
    {
        return $this->scheme;
    }

    
    public function setScheme($scheme)
    {
        $this->scheme = strtolower($scheme);
    }

    
    public function getHttpPort()
    {
        return $this->httpPort;
    }

    
    public function setHttpPort($httpPort)
    {
        $this->httpPort = $httpPort;
    }

    
    public function getHttpsPort()
    {
        return $this->httpsPort;
    }

    
    public function setHttpsPort($httpsPort)
    {
        $this->httpsPort = $httpsPort;
    }

    
    public function getParameters()
    {
        return $this->parameters;
    }

    
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    
    public function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    
    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    
    public function setParameter($name, $parameter)
    {
        $this->parameters[$name] = $parameter;
    }
}
}
 



namespace Symfony\Component\Routing
{

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\ConfigCache;


class Router implements RouterInterface
{
    protected $matcher;
    protected $generator;
    protected $defaults;
    protected $context;
    protected $loader;
    protected $collection;
    protected $resource;
    protected $options;

    
    public function __construct(LoaderInterface $loader, $resource, array $options = array(), RequestContext $context = null, array $defaults = array())
    {
        $this->loader = $loader;
        $this->resource = $resource;
        $this->context = null === $context ? new RequestContext() : $context;
        $this->defaults = $defaults;
        $this->setOptions($options);
    }

    
    public function setOptions(array $options)
    {
        $this->options = array(
            'cache_dir'              => null,
            'debug'                  => false,
            'generator_class'        => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'generator_base_class'   => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'generator_cache_class'  => 'ProjectUrlGenerator',
            'matcher_class'          => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_base_class'     => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_dumper_class'   => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'matcher_cache_class'    => 'ProjectUrlMatcher',
            'resource_type'          => null,
        );

                $invalid = array();
        $isInvalid = false;
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $isInvalid = true;
                $invalid[] = $key;
            }
        }

        if ($isInvalid) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the following options: "%s".', implode('\', \'', $invalid)));
        }
    }

    
    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;
    }

    
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    
    public function getRouteCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->loader->load($this->resource, $this->options['resource_type']);
        }

        return $this->collection;
    }

    
    public function setContext(RequestContext $context)
    {
        $this->context = $context;

        $this->getMatcher()->setContext($context);
        $this->getGenerator()->setContext($context);
    }

    
    public function getContext()
    {
        return $this->context;
    }

    
    public function generate($name, $parameters = array(), $absolute = false)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }

    
    public function match($url)
    {
        return $this->getMatcher()->match($url);
    }

    
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        if (null === $this->options['cache_dir'] || null === $this->options['matcher_cache_class']) {
            return $this->matcher = new $this->options['matcher_class']($this->getRouteCollection(), $this->context, $this->defaults);
        }

        $class = $this->options['matcher_cache_class'];
        $cache = new ConfigCache($this->options['cache_dir'].'/'.$class.'.php', $this->options['debug']);
        if (!$cache->isFresh($class)) {
            $dumper = new $this->options['matcher_dumper_class']($this->getRouteCollection());

            $options = array(
                'class'      => $class,
                'base_class' => $this->options['matcher_base_class'],
            );

            $cache->write($dumper->dump($options), $this->getRouteCollection()->getResources());
        }

        require_once $cache;

        return $this->matcher = new $class($this->context, $this->defaults);
    }

    
    public function getGenerator()
    {
        if (null !== $this->generator) {
            return $this->generator;
        }

        if (null === $this->options['cache_dir'] || null === $this->options['generator_cache_class']) {
            return $this->generator = new $this->options['generator_class']($this->getRouteCollection(), $this->context, $this->defaults);
        }

        $class = $this->options['generator_cache_class'];
        $cache = new ConfigCache($this->options['cache_dir'].'/'.$class.'.php', $this->options['debug']);
        if (!$cache->isFresh($class)) {
            $dumper = new $this->options['generator_dumper_class']($this->getRouteCollection());

            $options = array(
                'class'      => $class,
                'base_class' => $this->options['generator_base_class'],
            );

            $cache->write($dumper->dump($options), $this->getRouteCollection()->getResources());
        }

        require_once $cache;

        return $this->generator = new $class($this->context, $this->defaults);
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Routing
{

use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher as BaseMatcher;


class RedirectableUrlMatcher extends BaseMatcher
{
    
    public function redirect($path, $route, $scheme = null)
    {
        return array(
            '_controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController::urlRedirectAction',
            'path'        => $path,
            'permanent'   => true,
            'scheme'      => $scheme,
            'httpPort'    => $this->context->getHttpPort(),
            'httpsPort'   => $this->context->getHttpsPort(),
            '_route'      => $route,
        );
    }
}
}
 




namespace Application\DeskPRO\Routing
{

use Orb\Util\Strings;

class Router extends \Symfony\Bundle\FrameworkBundle\Routing\Router
{
	public function setOptions(array $options)
	{
		if (isset($options['debug'])) {
			$options['debug'] = false;
		}

		return parent::setOptions($options);
	}

	public function generateUrl($name, $parameters = array())
	{
		return $this->getGenerator()->generateUrl($name, $parameters);
	}


	
	public function getIdFromSlug($slug)
	{
		$id = Strings::extractRegexMatch('#^([0-9]+)#', $slug, 1);

		if (!$id) {
			return 0;
		}

		return (int)$id;
	}
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Templating
{

use Symfony\Component\DependencyInjection\ContainerInterface;


class GlobalVariables
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    
    public function getSecurity()
    {
        if ($this->container->has('security.context')) {
            return $this->container->get('security.context');
        }
    }

    
    public function getUser()
    {
        if (!$security = $this->getSecurity()) {
            return;
        }

        if (!$token = $security->getToken()) {
            return;
        }

        $user = $token->getUser();
        if (!is_object($user)) {
            return;
        }

        return $user;
    }

    
    public function getRequest()
    {
        if ($this->container->has('request') && $request = $this->container->get('request')) {
            return $request;
        }
    }

    
    public function getSession()
    {
        if ($request = $this->getRequest()) {
            return $request->getSession();
        }
    }

    
    public function getEnvironment()
    {
        return $this->container->getParameter('kernel.environment');
    }

    
    public function getDebug()
    {
        return (Boolean) $this->container->getParameter('kernel.debug');
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Templating
{

use Symfony\Component\Templating\EngineInterface as BaseEngineInterface;
use Symfony\Component\HttpFoundation\Response;


interface EngineInterface extends BaseEngineInterface
{
    
    public function renderResponse($view, array $parameters = array(), Response $response = null);
}
}
 



namespace Symfony\Component\Templating
{


interface TemplateNameParserInterface
{
    
    public function parse($name);
}
}
 



namespace Symfony\Component\Templating
{

use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Component\Templating\TemplateReference;


class TemplateNameParser implements TemplateNameParserInterface
{
    
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        $engine = null;
        if (false !== $pos = strrpos($name, '.')) {
            $engine = substr($name, $pos + 1);
        }

        return new TemplateReference($name, $engine);
    }
}
}
 



namespace Symfony\Component\Templating
{


interface EngineInterface
{
    
    public function render($name, array $parameters = array());

    
    public function exists($name);

    
    public function supports($name);
}
}
 



namespace Symfony\Component\Config
{


interface FileLocatorInterface
{
    
    public function locate($name, $currentPath = null, $first = true);
}
}
 



namespace Symfony\Component\Templating
{


interface TemplateReferenceInterface
{
    
    public function all();

    
    public function set($name, $value);

    
    public function get($name);

    
    public function getPath();

    
    public function getLogicalName();
}
}
 



namespace Symfony\Component\Templating
{


class TemplateReference implements TemplateReferenceInterface
{
    protected $parameters;

    public function __construct($name = null, $engine = null)
    {
        $this->parameters = array(
            'name'   => $name,
            'engine' => $engine,
        );
    }

    public function __toString()
    {
        return $this->getLogicalName();
    }

    
    public function set($name, $value)
    {
        if (array_key_exists($name, $this->parameters)) {
            $this->parameters[$name] = $value;
        } else {
            throw new \InvalidArgumentException(sprintf('The template does not support the "%s" parameter.', $name));
        }

        return $this;
    }

    
    public function get($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        throw new \InvalidArgumentException(sprintf('The template does not support the "%s" parameter.', $name));
    }

    
    public function all()
    {
        return $this->parameters;
    }

    
    public function getPath()
    {
        return $this->parameters['name'];
    }

    
    public function getLogicalName()
    {
        return $this->parameters['name'];
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Templating
{

use Symfony\Component\Templating\TemplateReference as BaseTemplateReference;


class TemplateReference extends BaseTemplateReference
{
    public function __construct($bundle = null, $controller = null, $name = null, $format = null, $engine = null)
    {
        $this->parameters = array(
            'bundle'     => $bundle,
            'controller' => $controller,
            'name'       => $name,
            'format'     => $format,
            'engine'     => $engine,
        );
    }

    
    public function getPath()
    {
        $controller = str_replace('\\', '/', $this->get('controller'));

        $path = (empty($controller) ? '' : $controller.'/').$this->get('name').'.'.$this->get('format').'.'.$this->get('engine');

        return empty($this->parameters['bundle']) ? 'views/'.$path : '@'.$this->get('bundle').'/Resources/views/'.$path;
    }

    
    public function getLogicalName()
    {
        return sprintf('%s:%s:%s.%s.%s', $this->parameters['bundle'], $this->parameters['controller'], $this->parameters['name'], $this->parameters['format'], $this->parameters['engine']);
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Templating
{

use Symfony\Component\Templating\TemplateNameParser as BaseTemplateNameParser;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Component\HttpKernel\KernelInterface;


class TemplateNameParser extends BaseTemplateNameParser
{
    protected $kernel;
    protected $cache;

    
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->cache = array();
    }

    
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        } elseif (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

                $name = str_replace(':/', ':', preg_replace('#/{2,}#', '/', strtr($name, '\\', '/')));

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('Template name "%s" contains invalid characters.', $name));
        }

        $parts = explode(':', $name);
        if (3 !== count($parts)) {
            throw new \InvalidArgumentException(sprintf('Template name "%s" is not valid (format is "bundle:section:template.format.engine").', $name));
        }

        $elements = explode('.', $parts[2]);
        if (3 > count($elements)) {
            throw new \InvalidArgumentException(sprintf('Template name "%s" is not valid (format is "bundle:section:template.format.engine").', $name));
        }
        $engine = array_pop($elements);
        $format = array_pop($elements);

        $template = new TemplateReference($parts[0], $parts[1], implode('.', $elements), $format, $engine);

        if ($template->get('bundle')) {
            try {
                $this->kernel->getBundle($template->get('bundle'));
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(sprintf('Template name "%s" is not valid.', $name), 0, $e);
            }
        }

        return $this->cache[$name] = $template;
    }

    
    public function parseFromFilename($file)
    {
        $parts = explode('/', strtr($file, '\\', '/'));

        $elements = explode('.', array_pop($parts));
        if (3 > count($elements)) {
            return false;
        }
        $engine = array_pop($elements);
        $format = array_pop($elements);

        return new TemplateReference('', implode('/', $parts), implode('.', $elements), $format, $engine);
    }

}
}
 




namespace Application\DeskPRO\Templating\Loader
{

use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator as BaseTemplateLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class TemplateLocator extends BaseTemplateLocator
{
	protected $locator;
	protected $cache = array();
	protected $loaded_list = array();

	public function __construct(FileLocatorInterface $locator, $cacheDir = null)
	{
		$cache_file = DP_ROOT.'/sys/template-map.php';
		if (is_file($cache_file)) {
			$this->cache = require $cache_file;
		}

		$this->locator = $locator;
	}

	public function locate($template, $currentPath = null, $first = true)
	{
		if (!$template instanceof TemplateReferenceInterface) {
			throw new \InvalidArgumentException("The template must be an instance of TemplateReferenceInterface.");
		}

		$key = $template->getLogicalName();

		if (isset($this->cache[$key])) {
			$this->logUsedTemplate($key, $this->cache[$key]['path']);
			return $this->cache[$key]['path'];
		}

		try {
			$this->cache[$key] = array(
				'path' => $this->locator->locate($template->getPath(), $currentPath)
			);
			$this->logUsedTemplate($key, $this->cache[$key]['path']);
			return $this->cache[$key]['path'];
		} catch (\InvalidArgumentException $e) {
			throw new \InvalidArgumentException(sprintf('Unable to find template "%s" : "%s".', $template, $e->getMessage()), 0, $e);
		}
	}

	protected function logUsedTemplate($key, $path)
	{
		if (defined('DEBUG_BACKTRACE_IGNORE_ARGS') && isset($GLOBALS['DP_CONFIG']['debug']['enable_log_tpl_use']) && $GLOBALS['DP_CONFIG']['debug']['enable_log_tpl_use']) {
			$back = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
		} else {
			$back = debug_backtrace();
		}
		$guess_origin = 'unknown';

		foreach ($back as $b) {
			if (!isset($b['file']) || !isset($b['line'])) {
				continue;
			}

			if (
				strpos($b['file'], '/Templating/') === false
				&& strpos($b['file'], '/TwigBundle/') === false
				&& strpos($b['file'], '/Twig/Loader') === false
				&& strpos($b['file'], '/DeskPRO/Twig') === false
				&& strpos($b['file'], '/lib/Twig/') === false
				&& strpos($b['file'], '/symfony/src/') === false
			) {
				$guess_origin = $b['file'] . ' line ' . $b['line'];
				break;
			}
		}

		$this->loaded_list[] = array(
			'key' => $key,
			'path' => $path,
			'origin' => $guess_origin
		);
	}

	public function getLoadedTemplates()
	{
		return $this->loaded_list;
	}
}}
 



namespace Symfony\Component\Templating
{

use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Templating\Storage\StringStorage;
use Symfony\Component\Templating\Helper\HelperInterface;
use Symfony\Component\Templating\Loader\LoaderInterface;


class PhpEngine implements EngineInterface, \ArrayAccess
{
    protected $loader;
    protected $current;
    protected $helpers;
    protected $parents;
    protected $stack;
    protected $charset;
    protected $cache;
    protected $escapers;
    protected $globals;
    protected $parser;

    
    public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader, array $helpers = array())
    {
        $this->parser  = $parser;
        $this->loader  = $loader;
        $this->parents = array();
        $this->stack   = array();
        $this->charset = 'UTF-8';
        $this->cache   = array();
        $this->globals = array();

        $this->setHelpers($helpers);

        $this->initializeEscapers();
        foreach ($this->escapers as $context => $escaper) {
            $this->setEscaper($context, $escaper);
        }
    }

    
    public function render($name, array $parameters = array())
    {
        $storage = $this->load($name);
        $key = md5(serialize($storage));
        $this->current = $key;
        $this->parents[$key] = null;

                $parameters = array_replace($this->getGlobals(), $parameters);
                if (false === $content = $this->evaluate($storage, $parameters)) {
            throw new \RuntimeException(sprintf('The template "%s" cannot be rendered.', $this->parser->parse($name)));
        }

                if ($this->parents[$key]) {
            $slots = $this->get('slots');
            $this->stack[] = $slots->get('_content');
            $slots->set('_content', $content);

            $content = $this->render($this->parents[$key], $parameters);

            $slots->set('_content', array_pop($this->stack));
        }

        return $content;
    }

    
    public function exists($name)
    {
        try {
            $this->load($name);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    
    public function supports($name)
    {
        $template = $this->parser->parse($name);

        return 'php' === $template->get('engine');
    }

    
    protected function evaluate(Storage $template, array $parameters = array())
    {
        $__template__ = $template;

        if (isset($parameters['__template__'])) {
            throw new \InvalidArgumentException('Invalid parameter (__template__)');
        }

        if ($__template__ instanceof FileStorage) {
            extract($parameters, EXTR_SKIP);
            $view = $this;
            ob_start();
            require $__template__;

            return ob_get_clean();
        } elseif ($__template__ instanceof StringStorage) {
            extract($parameters, EXTR_SKIP);
            $view = $this;
            ob_start();
            eval('; ?>'.$__template__.'<?php ;');

            return ob_get_clean();
        }

        return false;
    }

    
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    
    public function offsetExists($name)
    {
        return isset($this->helpers[$name]);
    }

    
    public function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    
    public function offsetUnset($name)
    {
        throw new \LogicException(sprintf('You can\'t unset a helper (%s).', $name));
    }

    
    public function addHelpers(array $helpers)
    {
        foreach ($helpers as $alias => $helper) {
            $this->set($helper, is_int($alias) ? null : $alias);
        }
    }

    
    public function setHelpers(array $helpers)
    {
        $this->helpers = array();
        $this->addHelpers($helpers);
    }

    
    public function set(HelperInterface $helper, $alias = null)
    {
        $this->helpers[$helper->getName()] = $helper;
        if (null !== $alias) {
            $this->helpers[$alias] = $helper;
        }

        $helper->setCharset($this->charset);
    }

    
    public function has($name)
    {
        return isset($this->helpers[$name]);
    }

    
    public function get($name)
    {
        if (!isset($this->helpers[$name])) {
            throw new \InvalidArgumentException(sprintf('The helper "%s" is not defined.', $name));
        }

        return $this->helpers[$name];
    }

    
    public function extend($template)
    {
        $this->parents[$this->current] = $template;
    }

    
    public function escape($value, $context = 'html')
    {
        return call_user_func($this->getEscaper($context), $value);
    }

    
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    
    public function getCharset()
    {
        return $this->charset;
    }

    
    public function setEscaper($context, $escaper)
    {
        $this->escapers[$context] = $escaper;
    }

    
    public function getEscaper($context)
    {
        if (!isset($this->escapers[$context])) {
            throw new \InvalidArgumentException(sprintf('No registered escaper for context "%s".', $context));
        }

        return $this->escapers[$context];
    }

    
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;
    }

    
    public function getGlobals()
    {
        return $this->globals;
    }

    
    protected function initializeEscapers()
    {
        $that = $this;

        $this->escapers = array(
            'html' =>
                
                function ($value) use ($that) {
                                                            return is_string($value) ? @htmlspecialchars($value, ENT_QUOTES, $that->getCharset(), false) : $value;
                },

            'js' =>
                
                function ($value) use ($that) {
                    if ('UTF-8' != $that->getCharset()) {
                        $value = $that->convertEncoding($value, 'UTF-8', $that->getCharset());
                    }

                    $callback = function ($matches) use ($that) {
                        $char = $matches[0];

                                                if (!isset($char[1])) {
                            return '\\x'.substr('00'.bin2hex($char), -2);
                        }

                                                $char = $that->convertEncoding($char, 'UTF-16BE', 'UTF-8');

                        return '\\u'.substr('0000'.bin2hex($char), -4);
                    };

                    if (null === $value = preg_replace_callback('#[^\p{L}\p{N} ]#u', $callback, $value)) {
                        throw new \InvalidArgumentException('The string to escape is not a valid UTF-8 string.');
                    }

                    if ('UTF-8' != $that->getCharset()) {
                        $value = $that->convertEncoding($value, $that->getCharset(), 'UTF-8');
                    }

                    return $value;
                },
        );
    }

    
    public function convertEncoding($string, $to, $from)
    {
        if (function_exists('iconv')) {
            return iconv($from, $to, $string);
        } elseif (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($string, $to, $from);
        }

        throw new \RuntimeException('No suitable convert encoding function (use UTF-8 as your encoding or install the iconv or mbstring extension).');
    }

    
    public function getLoader()
    {
        return $this->loader;
    }

    
    protected function load($name)
    {
        $template = $this->parser->parse($name);

        $key = $template->getLogicalName();
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $storage = $this->loader->load($template);

        if (false === $storage) {
            throw new \InvalidArgumentException(sprintf('The template "%s" does not exist.', $template));
        }

        return $this->cache[$key] = $storage;
    }
}
}
 



namespace Symfony\Component\Templating\Loader
{

use Symfony\Component\Templating\TemplateReferenceInterface;


interface LoaderInterface
{
    
    public function load(TemplateReferenceInterface $template);

    
    public function isFresh(TemplateReferenceInterface $template, $time);
}
}
 



namespace Symfony\Component\Templating\Storage
{


abstract class Storage
{
    protected $template;

    
    public function __construct($template)
    {
        $this->template = $template;
    }

    
    public function __toString()
    {
        return (string) $this->template;
    }

    
    abstract public function getContent();
}
}
 



namespace Symfony\Component\Templating\Storage
{


class FileStorage extends Storage
{
    
    public function getContent()
    {
        return file_get_contents($this->template);
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Templating
{

use Symfony\Component\Templating\PhpEngine as BasePhpEngine;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;


class PhpEngine extends BasePhpEngine implements EngineInterface
{
    protected $container;

    
    public function __construct(TemplateNameParserInterface $parser, ContainerInterface $container, LoaderInterface $loader, GlobalVariables $globals = null)
    {
        $this->container = $container;

        parent::__construct($parser, $loader);

        if (null !== $globals) {
            $this->addGlobal('app', $globals);
        }
    }

    
    public function get($name)
    {
        if (!isset($this->helpers[$name])) {
            throw new \InvalidArgumentException(sprintf('The helper "%s" is not defined.', $name));
        }

        if (is_string($this->helpers[$name])) {
            $this->helpers[$name] = $this->container->get($this->helpers[$name]);
            $this->helpers[$name]->setCharset($this->charset);
        }

        return $this->helpers[$name];
    }

    
    public function setHelpers(array $helpers)
    {
        $this->helpers = $helpers;
    }

    
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->render($view, $parameters));

        return $response;
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Templating\Loader
{

use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;


class FilesystemLoader implements LoaderInterface
{
    protected $locator;

    
    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    
    public function load(TemplateReferenceInterface $template)
    {
        try {
            $file = $this->locator->locate($template);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return new FileStorage($file);
    }

    
    public function isFresh(TemplateReferenceInterface $template, $time)
    {
        if (false === $storage = $this->load($template)) {
            return false;
        }

        if (!is_readable((string) $storage)) {
            return false;
        }

        return filemtime((string) $storage) < $time;
    }
}
}
 



namespace Symfony\Component\HttpFoundation
{


class ParameterBag
{
    protected $parameters;

    
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    
    public function all()
    {
        return $this->parameters;
    }

    
    public function keys()
    {
        return array_keys($this->parameters);
    }

    
    public function replace(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    
    public function add(array $parameters = array())
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    
    public function get($path, $default = null, $deep = false)
    {
        if (!$deep || false === $pos = strpos($path, '[')) {
            return array_key_exists($path, $this->parameters) ? $this->parameters[$path] : $default;
        }

        $root = substr($path, 0, $pos);
        if (!array_key_exists($root, $this->parameters)) {
            return $default;
        }

        $value = $this->parameters[$root];
        $currentKey = null;
        for ($i = $pos, $c = strlen($path); $i < $c; $i++) {
            $char = $path[$i];

            if ('[' === $char) {
                if (null !== $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "[" at position %d.', $i));
                }

                $currentKey = '';
            } elseif (']' === $char) {
                if (null === $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "]" at position %d.', $i));
                }

                if (!is_array($value) || !array_key_exists($currentKey, $value)) {
                    return $default;
                }

                $value = $value[$currentKey];
                $currentKey = null;
            } else {
                if (null === $currentKey) {
                    throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "%s" at position %d.', $char, $i));
                }

                $currentKey .= $char;
            }
        }

        if (null !== $currentKey) {
            throw new \InvalidArgumentException(sprintf('Malformed path. Path must end with "]".'));
        }

        return $value;
    }

    
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    
    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }

    
    public function remove($key)
    {
        unset($this->parameters[$key]);
    }

    
    public function getAlpha($key, $default = '', $deep = false)
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default, $deep));
    }

    
    public function getAlnum($key, $default = '', $deep = false)
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default, $deep));
    }

    
    public function getDigits($key, $default = '', $deep = false)
    {
        return preg_replace('/[^[:digit:]]/', '', $this->get($key, $default, $deep));
    }

    
    public function getInt($key, $default = 0, $deep = false)
    {
        return (int) $this->get($key, $default, $deep);
    }
}
}
 



namespace Symfony\Component\HttpFoundation
{


class HeaderBag
{
    protected $headers;
    protected $cacheControl;

    
    public function __construct(array $headers = array())
    {
        $this->cacheControl = array();
        $this->headers = array();
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    
    public function __toString()
    {
        if (!$this->headers) {
            return '';
        }

        $beautifier = function ($name) {
            return preg_replace_callback('/\-(.)/', function ($match) { return '-'.strtoupper($match[1]); }, ucfirst($name));
        };

        $max = max(array_map('strlen', array_keys($this->headers))) + 1;
        $content = '';
        ksort($this->headers);
        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                $content .= sprintf("%-{$max}s %s\r\n", $beautifier($name).':', $value);
            }
        }

        return $content;
    }

    
    public function all()
    {
        return $this->headers;
    }

    
    public function keys()
    {
        return array_keys($this->headers);
    }

    
    public function replace(array $headers = array())
    {
        $this->headers = array();
        $this->add($headers);
    }

    
    public function add(array $headers)
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    
    public function get($key, $default = null, $first = true)
    {
        $key = strtr(strtolower($key), '_', '-');

        if (!array_key_exists($key, $this->headers)) {
            if (null === $default) {
                return $first ? null : array();
            }

            return $first ? $default : array($default);
        }

        if ($first) {
            return count($this->headers[$key]) ? $this->headers[$key][0] : $default;
        }

        return $this->headers[$key];
    }

    
    public function set($key, $values, $replace = true)
    {
        $key = strtr(strtolower($key), '_', '-');

        $values = (array) $values;

        if (true === $replace || !isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }

        if ('cache-control' === $key) {
            $this->cacheControl = $this->parseCacheControl($values[0]);
        }
    }

    
    public function has($key)
    {
        return array_key_exists(strtr(strtolower($key), '_', '-'), $this->headers);
    }

    
    public function contains($key, $value)
    {
        return in_array($value, $this->get($key, null, false));
    }

    
    public function remove($key)
    {
        $key = strtr(strtolower($key), '_', '-');

        unset($this->headers[$key]);

        if ('cache-control' === $key) {
            $this->cacheControl = array();
        }
    }

    
    public function getDate($key, \DateTime $default = null)
    {
        if (null === $value = $this->get($key)) {
            return $default;
        }

        if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $value)) {
            throw new \RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $value));
        }

        return $date;
    }

    public function addCacheControlDirective($key, $value = true)
    {
        $this->cacheControl[$key] = $value;

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl);
    }

    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->cacheControl) ? $this->cacheControl[$key] : null;
    }

    public function removeCacheControlDirective($key)
    {
        unset($this->cacheControl[$key]);

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    protected function getCacheControlHeader()
    {
        $parts = array();
        ksort($this->cacheControl);
        foreach ($this->cacheControl as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
                    $value = '"'.$value.'"';
                }

                $parts[] = "$key=$value";
            }
        }

        return implode(', ', $parts);
    }

    
    protected function parseCacheControl($header)
    {
        $cacheControl = array();
        preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $header, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $cacheControl[strtolower($match[1])] = isset($match[2]) && $match[2] ? $match[2] : (isset($match[3]) ? $match[3] : true);
        }

        return $cacheControl;
    }
}
}
 



namespace Symfony\Component\HttpFoundation
{

use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileBag extends ParameterBag
{
    private static $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');

    
    public function __construct(array $parameters = array())
    {
        $this->replace($parameters);
    }

    
    public function replace(array $files = array())
    {
        $this->parameters = array();
        $this->add($files);
    }

    
    public function set($key, $value)
    {
        if (is_array($value) || $value instanceof UploadedFile) {
            parent::set($key, $this->convertFileInformation($value));
        } else {
            throw new \InvalidArgumentException('An uploaded file must be an array or an instance of UploadedFile.');
        }
    }

    
    public function add(array $files = array())
    {
        foreach ($files as $key => $file) {
            $this->set($key, $file);
        }
    }

    
    protected function convertFileInformation($file)
    {
        if ($file instanceof UploadedFile) {
            return $file;
        }

        $file = $this->fixPhpFilesArray($file);
        if (is_array($file)) {
            $keys = array_keys($file);
            sort($keys);

            if ($keys == self::$fileKeys) {
                if (UPLOAD_ERR_NO_FILE == $file['error']) {
                    $file = null;
                } else {
                    $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
                }
            } else {
                $file = array_map(array($this, 'convertFileInformation'), $file);
            }
        }

        return $file;
    }

    
    protected function fixPhpFilesArray($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $keys = array_keys($data);
        sort($keys);

        if (self::$fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
            return $data;
        }

        $files = $data;
        foreach (self::$fileKeys as $k) {
            unset($files[$k]);
        }

        foreach (array_keys($data['name']) as $key) {
            $files[$key] = $this->fixPhpFilesArray(array(
                'error'    => $data['error'][$key],
                'name'     => $data['name'][$key],
                'type'     => $data['type'][$key],
                'tmp_name' => $data['tmp_name'][$key],
                'size'     => $data['size'][$key]
            ));
        }

        return $files;
    }
}
}
 



namespace Symfony\Component\HttpFoundation
{


class ServerBag extends ParameterBag
{
    public function getHeaders()
    {
        $headers = array();
        foreach ($this->parameters as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            }
                        elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'))) {
                $headers[$key] = $value;
            }
        }

        if (isset($this->parameters['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $this->parameters['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = isset($this->parameters['PHP_AUTH_PW']) ? $this->parameters['PHP_AUTH_PW'] : '';
        } else {
            

            $authorizationHeader = null;
            if (isset($this->parameters['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->parameters['HTTP_AUTHORIZATION'];
            } elseif (isset($this->parameters['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->parameters['REDIRECT_HTTP_AUTHORIZATION'];
            }

                        if ((null !== $authorizationHeader) && (0 === stripos($authorizationHeader, 'basic'))) {
                $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)));
                if (count($exploded) == 2) {
                    list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
                }
            }
        }

                if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic '.base64_encode($headers['PHP_AUTH_USER'].':'.$headers['PHP_AUTH_PW']);
        }

        return $headers;
    }
}
}
 



namespace Symfony\Component\HttpFoundation
{


class Request
{
    protected static $trustProxy = false;

    
    public $attributes;

    
    public $request;

    
    public $query;

    
    public $server;

    
    public $files;

    
    public $cookies;

    
    public $headers;

    protected $content;
    protected $languages;
    protected $charsets;
    protected $acceptableContentTypes;
    protected $pathInfo;
    protected $requestUri;
    protected $baseUrl;
    protected $basePath;
    protected $method;
    protected $format;
    protected $session;

    protected static $formats;

    
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    
    public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->request = new ParameterBag($request);
        $this->query = new ParameterBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new ParameterBag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $this->content = $content;
        $this->languages = null;
        $this->charsets = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }

    
    public static function createFromGlobals()
    {
        $request = new static($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER);

        if (0 === strpos($request->server->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE'))
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return $request;
    }

    
    public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {
        $defaults = array(
            'SERVER_NAME'          => 'localhost',
            'SERVER_PORT'          => 80,
            'HTTP_HOST'            => 'localhost',
            'HTTP_USER_AGENT'      => 'Symfony/2.X',
            'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
            'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'REMOTE_ADDR'          => '127.0.0.1',
            'SCRIPT_NAME'          => '',
            'SCRIPT_FILENAME'      => '',
            'SERVER_PROTOCOL'      => 'HTTP/1.1',
            'REQUEST_TIME'         => time(),
        );

        $components = parse_url($uri);
        if (isset($components['host'])) {
            $defaults['SERVER_NAME'] = $components['host'];
            $defaults['HTTP_HOST'] = $components['host'];
        }

        if (isset($components['scheme'])) {
            if ('https' === $components['scheme']) {
                $defaults['HTTPS'] = 'on';
                $defaults['SERVER_PORT'] = 443;
            }
        }

        if (isset($components['port'])) {
            $defaults['SERVER_PORT'] = $components['port'];
            $defaults['HTTP_HOST'] = $defaults['HTTP_HOST'].':'.$components['port'];
        }

        if (!isset($components['path'])) {
            $components['path'] = '';
        }

        if (in_array(strtoupper($method), array('POST', 'PUT', 'DELETE'))) {
            $request = $parameters;
            $query = array();
            $defaults['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        } else {
            $request = array();
            $query = $parameters;
            if (false !== $pos = strpos($uri, '?')) {
                $qs = substr($uri, $pos + 1);
                parse_str($qs, $params);

                $query = array_merge($params, $query);
            }
        }

        $queryString = isset($components['query']) ? html_entity_decode($components['query']) : '';
        parse_str($queryString, $qs);
        if (is_array($qs)) {
            $query = array_replace($qs, $query);
        }

        $uri = $components['path'].($queryString ? '?'.$queryString : '');

        $server = array_replace($defaults, $server, array(
            'REQUEST_METHOD'       => strtoupper($method),
            'PATH_INFO'            => '',
            'REQUEST_URI'          => $uri,
            'QUERY_STRING'         => $queryString,
        ));

        return new static($query, $request, array(), $cookies, $files, $server, $content);
    }

    
    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        $dup = clone $this;
        if ($query !== null) {
            $dup->query = new ParameterBag($query);
        }
        if ($request !== null) {
            $dup->request = new ParameterBag($request);
        }
        if ($attributes !== null) {
            $dup->attributes = new ParameterBag($attributes);
        }
        if ($cookies !== null) {
            $dup->cookies = new ParameterBag($cookies);
        }
        if ($files !== null) {
            $dup->files = new FileBag($files);
        }
        if ($server !== null) {
            $dup->server = new ServerBag($server);
            $dup->headers = new HeaderBag($dup->server->getHeaders());
        }
        $dup->languages = null;
        $dup->charsets = null;
        $dup->acceptableContentTypes = null;
        $dup->pathInfo = null;
        $dup->requestUri = null;
        $dup->baseUrl = null;
        $dup->basePath = null;
        $dup->method = null;
        $dup->format = null;

        return $dup;
    }

    
    public function __clone()
    {
        $this->query      = clone $this->query;
        $this->request    = clone $this->request;
        $this->attributes = clone $this->attributes;
        $this->cookies    = clone $this->cookies;
        $this->files      = clone $this->files;
        $this->server     = clone $this->server;
        $this->headers    = clone $this->headers;
    }

    
    public function __toString()
    {
        return
            sprintf('%s %s %s', $this->getMethod(), $this->getRequestUri(), $this->server->get('SERVER_PROTOCOL'))."\r\n".
            $this->headers."\r\n".
            $this->getContent();
    }

    
    public function overrideGlobals()
    {
        $_GET = $this->query->all();
        $_POST = $this->request->all();
        $_SERVER = $this->server->all();
        $_COOKIE = $this->cookies->all();
        
        foreach ($this->headers->all() as $key => $value) {
            $key = strtoupper(str_replace('-', '_', $key));
            if (in_array($key, array('CONTENT_TYPE', 'CONTENT_LENGTH'))) {
                $_SERVER[$key] = implode(', ', $value);
            } else {
                $_SERVER['HTTP_'.$key] = implode(', ', $value);
            }
        }

                        $_REQUEST = array_merge($_GET, $_POST);
    }

    
    public static function trustProxyData()
    {
        self::$trustProxy = true;
    }

    
    public function get($key, $default = null, $deep = false)
    {
        return $this->query->get($key, $this->attributes->get($key, $this->request->get($key, $default, $deep), $deep), $deep);
    }

    
    public function getSession()
    {
        return $this->session;
    }

    
    public function hasPreviousSession()
    {
                return $this->cookies->has(session_name()) && null !== $this->session;
    }

    
    public function hasSession()
    {
        return null !== $this->session;
    }

    
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    
    public function getClientIp($proxy = false)
    {
        if ($proxy) {
            if ($this->server->has('HTTP_CLIENT_IP')) {
                return $this->server->get('HTTP_CLIENT_IP');
            } elseif (self::$trustProxy && $this->server->has('HTTP_X_FORWARDED_FOR')) {
                $clientIp = explode(',', $this->server->get('HTTP_X_FORWARDED_FOR'), 2);

                return isset($clientIp[0]) ? trim($clientIp[0]) : '';
            }
        }

        return $this->server->get('REMOTE_ADDR');
    }

    
    public function getScriptName()
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }

    
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }

        return $this->pathInfo;
    }

    
    public function getBasePath()
    {
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }

        return $this->basePath;
    }

    
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }

        return $this->baseUrl;
    }

    
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    
    public function getPort()
    {
        if (self::$trustProxy && $this->headers->has('X-Forwarded-Port')) {
            return $this->headers->get('X-Forwarded-Port');
        }

        return $this->server->get('SERVER_PORT');
    }

    
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port   = $this->getPort();

        if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
            return $this->getHost();
        }

        return $this->getHost().':'.$port;
    }

    
    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }

        return $this->requestUri;
    }

    
    public function getUri()
    {
        $qs = $this->getQueryString();
        if (null !== $qs) {
            $qs = '?'.$qs;
        }

        return $this->getScheme().'://'.$this->getHttpHost().$this->getBaseUrl().$this->getPathInfo().$qs;
    }

    
    public function getUriForPath($path)
    {
        return $this->getScheme().'://'.$this->getHttpHost().$this->getBaseUrl().$path;
    }

    
    public function getQueryString()
    {
        if (!$qs = $this->server->get('QUERY_STRING')) {
            return null;
        }

        $parts = array();
        $order = array();

        foreach (explode('&', $qs) as $segment) {
            if (false === strpos($segment, '=')) {
                $parts[] = $segment;
                $order[] = $segment;
            } else {
                $tmp = explode('=', rawurldecode($segment), 2);
                $parts[] = rawurlencode($tmp[0]).'='.rawurlencode($tmp[1]);
                $order[] = $tmp[0];
            }
        }
        array_multisort($order, SORT_ASC, $parts);

        return implode('&', $parts);
    }

    
    public function isSecure()
    {
        return (
            (strtolower($this->server->get('HTTPS')) == 'on' || $this->server->get('HTTPS') == 1)
            ||
            (self::$trustProxy && strtolower($this->headers->get('SSL_HTTPS')) == 'on' || $this->headers->get('SSL_HTTPS') == 1)
            ||
            (self::$trustProxy && strtolower($this->headers->get('X_FORWARDED_PROTO')) == 'https')
        );
    }

    
    public function getHost()
    {
        if (self::$trustProxy && $host = $this->headers->get('X_FORWARDED_HOST')) {
            $elements = explode(',', $host);

            $host = trim($elements[count($elements) - 1]);
        } else {
            if (!$host = $this->headers->get('HOST')) {
                if (!$host = $this->server->get('SERVER_NAME')) {
                    $host = $this->server->get('SERVER_ADDR', '');
                }
            }
        }

                $host = preg_replace('/:\d+$/', '', $host);

        return trim($host);
    }

    
    public function setMethod($method)
    {
        $this->method = null;
        $this->server->set('REQUEST_METHOD', $method);
    }

    
    public function getMethod()
    {
        if (null === $this->method) {
            $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
            if ('POST' === $this->method) {
                $this->method = strtoupper($this->headers->get('X-HTTP-METHOD-OVERRIDE', $this->request->get('_method', 'POST')));
            }
        }

        return $this->method;
    }

    
    public function getMimeType($format)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        return isset(static::$formats[$format]) ? static::$formats[$format][0] : null;
    }

    
    public function getFormat($mimeType)
    {
        if (false !== $pos = strpos($mimeType, ';')) {
            $mimeType = substr($mimeType, 0, $pos);
        }

        if (null === static::$formats) {
            static::initializeFormats();
        }

        foreach (static::$formats as $format => $mimeTypes) {
            if (in_array($mimeType, (array) $mimeTypes)) {
                return $format;
            }
        }

        return null;
    }

    
    public function setFormat($format, $mimeTypes)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        static::$formats[$format] = is_array($mimeTypes) ? $mimeTypes : array($mimeTypes);
    }

    
    public function getRequestFormat($default = 'html')
    {
        if (null === $this->format) {
            $this->format = $this->get('_format', $default);
        }

        return $this->format;
    }

    
    public function setRequestFormat($format)
    {
        $this->format = $format;
    }

    public function setLocale($locale)
    {
        if (!$this->hasSession()) {
            throw new \LogicException('Forward compatibility for Request::setLocale() requires the session to be set.');
        }

        $this->session->setLocale($locale);
    }

    public function getLocale()
    {
        if (!$this->hasSession()) {
            throw new \LogicException('Forward compatibility for Request::getLocale() requires the session to be set.');
        }

        return $this->session->getLocale();
    }

    
    public function isMethodSafe()
    {
        return in_array($this->getMethod(), array('GET', 'HEAD'));
    }

    
    public function getContent($asResource = false)
    {
        if (false === $this->content || (true === $asResource && null !== $this->content)) {
            throw new \LogicException('getContent() can only be called once when using the resource return type.');
        }

        if (true === $asResource) {
            $this->content = false;

            return fopen('php://input', 'rb');
        }

        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    
    public function getETags()
    {
        return preg_split('/\s*,\s*/', $this->headers->get('if_none_match'), null, PREG_SPLIT_NO_EMPTY);
    }

    public function isNoCache()
    {
        return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
    }

    
    public function getPreferredLanguage(array $locales = null)
    {
        $preferredLanguages = $this->getLanguages();

        if (empty($locales)) {
            return isset($preferredLanguages[0]) ? $preferredLanguages[0] : null;
        }

        if (!$preferredLanguages) {
            return $locales[0];
        }

        $preferredLanguages = array_values(array_intersect($preferredLanguages, $locales));

        return isset($preferredLanguages[0]) ? $preferredLanguages[0] : $locales[0];
    }

    
    public function getLanguages()
    {
        if (null !== $this->languages) {
            return $this->languages;
        }

        $languages = $this->splitHttpAcceptHeader($this->headers->get('Accept-Language'));
        $this->languages = array();
        foreach ($languages as $lang => $q) {
            if (strstr($lang, '-')) {
                $codes = explode('-', $lang);
                if ($codes[0] == 'i') {
                                                                                if (count($codes) > 1) {
                        $lang = $codes[1];
                    }
                } else {
                    for ($i = 0, $max = count($codes); $i < $max; $i++) {
                        if ($i == 0) {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_'.strtoupper($codes[$i]);
                        }
                    }
                }
            }

            $this->languages[] = $lang;
        }

        return $this->languages;
    }

    
    public function getCharsets()
    {
        if (null !== $this->charsets) {
            return $this->charsets;
        }

        return $this->charsets = array_keys($this->splitHttpAcceptHeader($this->headers->get('Accept-Charset')));
    }

    
    public function getAcceptableContentTypes()
    {
        if (null !== $this->acceptableContentTypes) {
            return $this->acceptableContentTypes;
        }

        return $this->acceptableContentTypes = array_keys($this->splitHttpAcceptHeader($this->headers->get('Accept')));
    }

    
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
    }

    
    public function splitHttpAcceptHeader($header)
    {
        if (!$header) {
            return array();
        }

        $values = array();
        foreach (array_filter(explode(',', $header)) as $value) {
                        if (preg_match('/;\s*(q=.*$)/', $value, $match)) {
                $q     = (float) substr(trim($match[1]), 2);
                $value = trim(substr($value, 0, -strlen($match[0])));
            } else {
                $q = 1;
            }

            if (0 < $q) {
                $values[trim($value)] = $q;
            }
        }

        arsort($values);
        reset($values);

        return $values;
    }

    

    protected function prepareRequestUri()
    {
        $requestUri = '';

        if ($this->headers->has('X_REWRITE_URL') && false !== stripos(PHP_OS, 'WIN')) {
                        $requestUri = $this->headers->get('X_REWRITE_URL');
        } elseif ($this->server->get('IIS_WasUrlRewritten') == '1' && $this->server->get('UNENCODED_URL') != '') {
                        $requestUri = $this->server->get('UNENCODED_URL');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');
                        $schemeAndHttpHost = $this->getScheme().'://'.$this->getHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
                        $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ($this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
        }

        return $requestUri;
    }

    protected function prepareBaseUrl()
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME'));

        if (basename($this->server->get('SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF')) === $filename) {
            $baseUrl = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('ORIG_SCRIPT_NAME');         } else {
                                    $path    = $this->server->get('PHP_SELF', '');
            $file    = $this->server->get('SCRIPT_FILENAME', '');
            $segs    = explode('/', trim($file, '/'));
            $segs    = array_reverse($segs);
            $index   = 0;
            $last    = count($segs);
            $baseUrl = '';
            do {
                $seg     = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
        }

                $requestUri = $this->getRequestUri();

        if ($baseUrl && 0 === strpos($requestUri, $baseUrl)) {
                        return $baseUrl;
        }

        if ($baseUrl && 0 === strpos($requestUri, dirname($baseUrl))) {
                        return rtrim(dirname($baseUrl), '/');
        }

        $truncatedRequestUri = $requestUri;
        if (($pos = strpos($requestUri, '?')) !== false) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);
        if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
                        return '';
        }

                                if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return rtrim($baseUrl, '/');
    }

    
    protected function prepareBasePath()
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }

        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }

        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }

        return rtrim($basePath, '/');
    }

    
    protected function preparePathInfo()
    {
        $baseUrl = $this->getBaseUrl();

        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }

        $pathInfo = '/';

                if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if ((null !== $baseUrl) && (false === ($pathInfo = substr(urldecode($requestUri), strlen(urldecode($baseUrl)))))) {
                        return '/';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }

        return (string) $pathInfo;
    }

    
    protected static function initializeFormats()
    {
        static::$formats = array(
            'html' => array('text/html', 'application/xhtml+xml'),
            'txt'  => array('text/plain'),
            'js'   => array('application/javascript', 'application/x-javascript', 'text/javascript'),
            'css'  => array('text/css'),
            'json' => array('application/json', 'application/x-json'),
            'xml'  => array('text/xml', 'application/xml', 'application/x-xml'),
            'rdf'  => array('application/rdf+xml'),
            'atom' => array('application/atom+xml'),
        );
    }
}
}
 



namespace Symfony\Component\HttpFoundation
{


class Response
{
    
    public $headers;

    protected $content;
    protected $version;
    protected $statusCode;
    protected $statusText;
    protected $charset;

    public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->headers = new ResponseHeaderBag($headers);
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
        if (!$this->headers->has('Date')) {
            $this->setDate(new \DateTime(null, new \DateTimeZone('UTC')));
        }
    }

    
    public function __toString()
    {
        $this->prepare();

        return
            sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText)."\r\n".
            $this->headers."\r\n".
            $this->getContent();
    }

    
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }

    
    public function prepare()
    {
        if ($this->isInformational() || in_array($this->statusCode, array(204, 304))) {
            $this->setContent('');
        }

                $charset = $this->charset ?: 'UTF-8';
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/html; charset='.$charset);
        } elseif (0 === strpos($this->headers->get('Content-Type'), 'text/') && false === strpos($this->headers->get('Content-Type'), 'charset')) {
                        $this->headers->set('Content-Type', $this->headers->get('Content-Type').'; charset='.$charset);
        }

                if ($this->headers->has('Transfer-Encoding')) {
            $this->headers->remove('Content-Length');
        }
    }

    
    public function sendHeaders()
    {
                if (headers_sent()) {
            return;
        }

        $this->prepare();

                header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));

                foreach ($this->headers->all() as $name => $values) {
            foreach ($values as $value) {
                header($name.': '.$value, false);
            }
        }

                foreach ($this->headers->getCookies() as $cookie) {
            setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
        }
    }

    
    public function sendContent()
    {
        echo $this->content;
    }

    
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    
    public function setContent($content)
    {
        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))) {
            throw new \UnexpectedValueException('The Response content must be a string or object implementing __toString(), "'.gettype($content).'" given.');
        }

        $this->content = (string) $content;
    }

    
    public function getContent()
    {
        return $this->content;
    }

    
    public function setProtocolVersion($version)
    {
        $this->version = $version;
    }

    
    public function getProtocolVersion()
    {
        return $this->version;
    }

    
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = $code = (int) $code;
        if ($this->isInvalid()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }

        if (null === $text) {
            $this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : '';

            return;
        }

        if (false === $text) {
            $this->statusText = '';

            return;
        }

        $this->statusText = $text;
    }

    
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    
    public function getCharset()
    {
        return $this->charset;
    }

    
    public function isCacheable()
    {
        if (!in_array($this->statusCode, array(200, 203, 300, 301, 302, 404, 410))) {
            return false;
        }

        if ($this->headers->hasCacheControlDirective('no-store') || $this->headers->getCacheControlDirective('private')) {
            return false;
        }

        return $this->isValidateable() || $this->isFresh();
    }

    
    public function isFresh()
    {
        return $this->getTtl() > 0;
    }

    
    public function isValidateable()
    {
        return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
    }

    
    public function setPrivate()
    {
        $this->headers->removeCacheControlDirective('public');
        $this->headers->addCacheControlDirective('private');
    }

    
    public function setPublic()
    {
        $this->headers->addCacheControlDirective('public');
        $this->headers->removeCacheControlDirective('private');
    }

    
    public function mustRevalidate()
    {
        return $this->headers->hasCacheControlDirective('must-revalidate') || $this->headers->has('proxy-revalidate');
    }

    
    public function getDate()
    {
        return $this->headers->getDate('Date');
    }

    
    public function setDate(\DateTime $date)
    {
        $date->setTimezone(new \DateTimeZone('UTC'));
        $this->headers->set('Date', $date->format('D, d M Y H:i:s').' GMT');
    }

    
    public function getAge()
    {
        if ($age = $this->headers->get('Age')) {
            return $age;
        }

        return max(time() - $this->getDate()->format('U'), 0);
    }

    
    public function expire()
    {
        if ($this->isFresh()) {
            $this->headers->set('Age', $this->getMaxAge());
        }
    }

    
    public function getExpires()
    {
        return $this->headers->getDate('Expires');
    }

    
    public function setExpires(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->remove('Expires');
        } else {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Expires', $date->format('D, d M Y H:i:s').' GMT');
        }
    }

    
    public function getMaxAge()
    {
        if ($age = $this->headers->getCacheControlDirective('s-maxage')) {
            return $age;
        }

        if ($age = $this->headers->getCacheControlDirective('max-age')) {
            return $age;
        }

        if (null !== $this->getExpires()) {
            return $this->getExpires()->format('U') - $this->getDate()->format('U');
        }

        return null;
    }

    
    public function setMaxAge($value)
    {
        $this->headers->addCacheControlDirective('max-age', $value);
    }

    
    public function setSharedMaxAge($value)
    {
        $this->setPublic();
        $this->headers->addCacheControlDirective('s-maxage', $value);
    }

    
    public function getTtl()
    {
        if ($maxAge = $this->getMaxAge()) {
            return $maxAge - $this->getAge();
        }

        return null;
    }

    
    public function setTtl($seconds)
    {
        $this->setSharedMaxAge($this->getAge() + $seconds);
    }

    
    public function setClientTtl($seconds)
    {
        $this->setMaxAge($this->getAge() + $seconds);
    }

    
    public function getLastModified()
    {
        return $this->headers->getDate('Last-Modified');
    }

    
    public function setLastModified(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->remove('Last-Modified');
        } else {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s').' GMT');
        }
    }

    
    public function getEtag()
    {
        return $this->headers->get('ETag');
    }

    
    public function setEtag($etag = null, $weak = false)
    {
        if (null === $etag) {
            $this->headers->remove('Etag');
        } else {
            if (0 !== strpos($etag, '"')) {
                $etag = '"'.$etag.'"';
            }

            $this->headers->set('ETag', (true === $weak ? 'W/' : '').$etag);
        }
    }

    
    public function setCache(array $options)
    {
        if ($diff = array_diff(array_keys($options), array('etag', 'last_modified', 'max_age', 's_maxage', 'private', 'public'))) {
            throw new \InvalidArgumentException(sprintf('Response does not support the following options: "%s".', implode('", "', array_values($diff))));
        }

        if (isset($options['etag'])) {
            $this->setEtag($options['etag']);
        }

        if (isset($options['last_modified'])) {
            $this->setLastModified($options['last_modified']);
        }

        if (isset($options['max_age'])) {
            $this->setMaxAge($options['max_age']);
        }

        if (isset($options['s_maxage'])) {
            $this->setSharedMaxAge($options['s_maxage']);
        }

        if (isset($options['public'])) {
            if ($options['public']) {
                $this->setPublic();
            } else {
                $this->setPrivate();
            }
        }

        if (isset($options['private'])) {
            if ($options['private']) {
                $this->setPrivate();
            } else {
                $this->setPublic();
            }
        }
    }

    
    public function setNotModified()
    {
        $this->setStatusCode(304);
        $this->setContent(null);

                foreach (array('Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified') as $header) {
            $this->headers->remove($header);
        }
    }

    
    public function hasVary()
    {
        return (Boolean) $this->headers->get('Vary');
    }

    
    public function getVary()
    {
        if (!$vary = $this->headers->get('Vary')) {
            return array();
        }

        return is_array($vary) ? $vary : preg_split('/[\s,]+/', $vary);
    }

    
    public function setVary($headers, $replace = true)
    {
        $this->headers->set('Vary', $headers, $replace);
    }

    
    public function isNotModified(Request $request)
    {
        $lastModified = $request->headers->get('If-Modified-Since');
        $notModified = false;
        if ($etags = $request->getEtags()) {
            $notModified = (in_array($this->getEtag(), $etags) || in_array('*', $etags)) && (!$lastModified || $this->headers->get('Last-Modified') == $lastModified);
        } elseif ($lastModified) {
            $notModified = $lastModified == $this->headers->get('Last-Modified');
        }

        if ($notModified) {
            $this->setNotModified();
        }

        return $notModified;
    }

        
    public function isInvalid()
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    
    public function isInformational()
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    
    public function isSuccessful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    
    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    
    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    
    public function isOk()
    {
        return 200 === $this->statusCode;
    }

    
    public function isForbidden()
    {
        return 403 === $this->statusCode;
    }

    
    public function isNotFound()
    {
        return 404 === $this->statusCode;
    }

    
    public function isRedirect($location = null)
    {
        return in_array($this->statusCode, array(201, 301, 302, 303, 307)) && (null === $location ?: $location == $this->headers->get('Location'));
    }

    
    public function isEmpty()
    {
        return in_array($this->statusCode, array(201, 204, 304));
    }
}
}
 



namespace Symfony\Component\HttpFoundation
{


class ResponseHeaderBag extends HeaderBag
{
    const COOKIES_FLAT  = 'flat';
    const COOKIES_ARRAY = 'array';

    protected $computedCacheControl = array();
    protected $cookies              = array();

    
    public function __construct(array $headers = array())
    {
        parent::__construct($headers);

        if (!isset($this->headers['cache-control'])) {
            $this->set('cache-control', '');
        }
    }

    
    public function __toString()
    {
        $cookies = '';
        foreach ($this->getCookies() as $cookie) {
            $cookies .= 'Set-Cookie: '.$cookie."\r\n";
        }

        return parent::__toString().$cookies;
    }

    
    public function replace(array $headers = array())
    {
        parent::replace($headers);

        if (!isset($this->headers['cache-control'])) {
            $this->set('cache-control', '');
        }
    }

    
    public function set($key, $values, $replace = true)
    {
        parent::set($key, $values, $replace);

                if (in_array(strtr(strtolower($key), '_', '-'), array('cache-control', 'etag', 'last-modified', 'expires'))) {
            $computed = $this->computeCacheControlValue();
            $this->headers['cache-control'] = array($computed);
            $this->computedCacheControl = $this->parseCacheControl($computed);
        }
    }

    
    public function remove($key)
    {
        parent::remove($key);

        if ('cache-control' === strtr(strtolower($key), '_', '-')) {
            $this->computedCacheControl = array();
        }
    }

    
    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->computedCacheControl);
    }

    
    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->computedCacheControl) ? $this->computedCacheControl[$key] : null;
    }

    
    public function setCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
    }

    
    public function removeCookie($name, $path = '/', $domain = null)
    {
        if (null === $path) {
            $path = '/';
        }

        unset($this->cookies[$domain][$path][$name]);

        if (empty($this->cookies[$domain][$path])) {
            unset($this->cookies[$domain][$path]);

            if (empty($this->cookies[$domain])) {
                unset($this->cookies[$domain]);
            }
        }
    }

    
    public function getCookies($format = self::COOKIES_FLAT)
    {
        if (!in_array($format, array(self::COOKIES_FLAT, self::COOKIES_ARRAY))) {
            throw new \InvalidArgumentException(sprintf('Format "%s" invalid (%s).', $format, implode(', ', array(self::COOKIES_FLAT, self::COOKIES_ARRAY))));
        }

        if (self::COOKIES_ARRAY === $format) {
            return $this->cookies;
        }

        $flattenedCookies = array();
        foreach ($this->cookies as $path) {
            foreach ($path as $cookies) {
                foreach ($cookies as $cookie) {
                    $flattenedCookies[] = $cookie;
                }
            }
        }

        return $flattenedCookies;
    }

    
    public function clearCookie($name, $path = '/', $domain = null)
    {
        $this->setCookie(new Cookie($name, null, 1, $path, $domain));
    }

    
    protected function computeCacheControlValue()
    {
        if (!$this->cacheControl && !$this->has('ETag') && !$this->has('Last-Modified') && !$this->has('Expires')) {
            return 'no-cache';
        }

        if (!$this->cacheControl) {
                        return 'private, must-revalidate';
        }

        $header = $this->getCacheControlHeader();
        if (isset($this->cacheControl['public']) || isset($this->cacheControl['private'])) {
            return $header;
        }

                if (!isset($this->cacheControl['s-maxage'])) {
            return $header.', private';
        }

        return $header;
    }
}
}
 



namespace Symfony\Component\Config
{


class FileLocator implements FileLocatorInterface
{
    protected $paths;

    
    public function __construct($paths = array())
    {
        $this->paths = (array) $paths;
    }

    
    public function locate($name, $currentPath = null, $first = true)
    {
        if ($this->isAbsolutePath($name)) {
            if (!file_exists($name)) {
                throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $name));
            }

            return $name;
        }

        $filepaths = array();
        if (null !== $currentPath && file_exists($file = $currentPath.DIRECTORY_SEPARATOR.$name)) {
            if (true === $first) {
                return $file;
            }
            $filepaths[] = $file;
        }

        foreach ($this->paths as $path) {
            if (file_exists($file = $path.DIRECTORY_SEPARATOR.$name)) {
                if (true === $first) {
                    return $file;
                }
                $filepaths[] = $file;
            }
        }

        if (!$filepaths) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist (in: %s%s).', $name, null !== $currentPath ? $currentPath.', ' : '', implode(', ', $this->paths)));
        }

        return array_values(array_unique($filepaths));
    }

    
    private function isAbsolutePath($file)
    {
        if ($file[0] == '/' || $file[0] == '\\'
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && $file[1] == ':'
                && ($file[2] == '\\' || $file[2] == '/')
            )
        ) {
            return true;
        }

        return false;
    }
}
}
 



namespace Symfony\Component\EventDispatcher
{


interface EventDispatcherInterface
{
    
    public function dispatch($eventName, Event $event = null);

    
    public function addListener($eventName, $listener, $priority = 0);

    
    public function addSubscriber(EventSubscriberInterface $subscriber);

    
    public function removeListener($eventName, $listener);

    
    public function removeSubscriber(EventSubscriberInterface $subscriber);

    
    public function getListeners($eventName = null);

    
    public function hasListeners($eventName = null);
}
}
 



namespace Symfony\Component\EventDispatcher
{


class EventDispatcher implements EventDispatcherInterface
{
    private $listeners = array();
    private $sorted = array();

    
    public function dispatch($eventName, Event $event = null)
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        if (null === $event) {
            $event = new Event();
        }

        $this->doDispatch($this->getListeners($eventName), $eventName, $event);
    }

    
    public function getListeners($eventName = null)
    {
        if (null !== $eventName) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }

            return $this->sorted[$eventName];
        }

        foreach (array_keys($this->listeners) as $eventName) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }

        return $this->sorted;
    }

    
    public function hasListeners($eventName = null)
    {
        return (Boolean) count($this->getListeners($eventName));
    }

    
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    
    public function removeListener($eventName, $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners))) {
                unset($this->listeners[$eventName][$priority][$key], $this->sorted[$eventName]);
            }
        }
    }

    
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, array($subscriber, $params));
            } else {
                $this->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            }
        }
    }

    
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            $this->removeListener($eventName, array($subscriber, is_string($params) ? $params : $params[0]));
        }
    }

    
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            call_user_func($listener, $event);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

    
    private function sortListeners($eventName)
    {
        $this->sorted[$eventName] = array();

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }
}
}
 



namespace Symfony\Component\EventDispatcher
{


class Event
{
    
    private $propagationStopped = false;

    
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }
}
}
 



namespace Symfony\Component\EventDispatcher
{


interface EventSubscriberInterface
{
    
    public static function getSubscribedEvents();
}
}
 



namespace Symfony\Component\HttpKernel
{

use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class HttpKernel implements HttpKernelInterface
{
    private $dispatcher;
    private $resolver;

    
    public function __construct(EventDispatcherInterface $dispatcher, ControllerResolverInterface $resolver)
    {
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
    }

    
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        try {
            return $this->handleRaw($request, $type);
        } catch (\Exception $e) {
            if (false === $catch) {
                throw $e;
            }

            return $this->handleException($e, $request, $type);
        }
    }

    
    private function handleRaw(Request $request, $type = self::MASTER_REQUEST)
    {
                $event = new GetResponseEvent($this, $request, $type);
        $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }

                if (false === $controller = $this->resolver->getController($request)) {
            throw new NotFoundHttpException(sprintf('Unable to find the controller for path "%s". Maybe you forgot to add the matching route in your routing configuration?', $request->getPathInfo()));
        }

        $event = new FilterControllerEvent($this, $controller, $request, $type);
        $this->dispatcher->dispatch(KernelEvents::CONTROLLER, $event);
        $controller = $event->getController();

                $arguments = $this->resolver->getArguments($request, $controller);

                $response = call_user_func_array($controller, $arguments);

                if (!$response instanceof Response) {
            $event = new GetResponseForControllerResultEvent($this, $request, $type, $response);
            $this->dispatcher->dispatch(KernelEvents::VIEW, $event);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }

            if (!$response instanceof Response) {
                $msg = sprintf('The controller must return a response (%s given).', $this->varToString($response));

                                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }
                throw new \LogicException($msg);
            }
        }

        return $this->filterResponse($response, $request, $type);
    }

    
    private function filterResponse(Response $response, Request $request, $type)
    {
        $event = new FilterResponseEvent($this, $request, $type, $response);

        $this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);

        return $event->getResponse();
    }

    
    private function handleException(\Exception $e, $request, $type)
    {
        $event = new GetResponseForExceptionEvent($this, $request, $type, $e);
        $this->dispatcher->dispatch(KernelEvents::EXCEPTION, $event);

        if (!$event->hasResponse()) {
            throw $e;
        }

        try {
            return $this->filterResponse($event->getResponse(), $request, $type);
        } catch (\Exception $e) {
            return $event->getResponse();
        }
    }

    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }

            return sprintf("Array(%s)", implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string) $var;
    }
}
}
 



namespace Symfony\Component\HttpKernel\EventListener
{

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;


class ResponseListener
{
    private $charset;

    public function __construct($charset)
    {
        $this->charset = $charset;
    }

    
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ('HEAD' === $request->getMethod()) {
                        $length = $response->headers->get('Content-Length');
            $response->setContent('');
            if ($length) {
                $response->headers->set('Content-Length', $length);
            }
        }

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (null === $response->getCharset()) {
            $response->setCharset($this->charset);
        }

        if ($response->headers->has('Content-Type')) {
            return;
        }

        $format = $request->getRequestFormat();
        if ((null !== $format) && $mimeType = $request->getMimeType($format)) {
            $response->headers->set('Content-Type', $mimeType);
        }
    }
}
}
 



namespace Symfony\Component\HttpKernel\Controller
{

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;


class ControllerResolver implements ControllerResolverInterface
{
    private $logger;

    
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    
    public function getController(Request $request)
    {
        if (!$controller = $request->attributes->get('_controller')) {
            if (null !== $this->logger) {
                $this->logger->warn('Unable to look for the controller as the "_controller" parameter is missing');
            }

            return false;
        }

        if (is_array($controller) || (is_object($controller) && method_exists($controller, '__invoke'))) {
            return $controller;
        }

        if (false === strpos($controller, ':') && method_exists($controller, '__invoke')) {
            return new $controller;
        }

        list($controller, $method) = $this->createController($controller);

        if (!method_exists($controller, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s" does not exist.', get_class($controller), $method));
        }

        return array($controller, $method);
    }

    
    public function getArguments(Request $request, $controller)
    {
        if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        } elseif (is_object($controller) && !$controller instanceof \Closure) {
            $r = new \ReflectionObject($controller);
            $r = $r->getMethod('__invoke');
        } else {
            $r = new \ReflectionFunction($controller);
        }

        return $this->doGetArguments($request, $controller, $r->getParameters());
    }

    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        $attributes = $request->attributes->all();
        $arguments = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            } elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = $request;
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                if (is_array($controller)) {
                    $repr = sprintf('%s::%s()', get_class($controller[0]), $controller[1]);
                } elseif (is_object($controller)) {
                    $repr = get_class($controller);
                } else {
                    $repr = $controller;
                }

                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->name));
            }
        }

        return $arguments;
    }

    
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        return array(new $class(), $method);
    }
}
}
 



namespace Symfony\Component\HttpKernel\Controller
{

use Symfony\Component\HttpFoundation\Request;


interface ControllerResolverInterface
{
    
    public function getController(Request $request);

    
    public function getArguments(Request $request, $controller);
}
}
 



namespace Symfony\Component\HttpKernel\Event
{

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;


class KernelEvent extends Event
{
    
    private $kernel;

    
    private $request;

    
    private $requestType;

    public function __construct(HttpKernelInterface $kernel, Request $request, $requestType)
    {
        $this->kernel = $kernel;
        $this->request = $request;
        $this->requestType = $requestType;
    }

    
    public function getKernel()
    {
        return $this->kernel;
    }

    
    public function getRequest()
    {
        return $this->request;
    }

    
    public function getRequestType()
    {
        return $this->requestType;
    }
}
}
 



namespace Symfony\Component\HttpKernel\Event
{

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;


class FilterControllerEvent extends KernelEvent
{
    
    private $controller;

    public function __construct(HttpKernelInterface $kernel, $controller, Request $request, $requestType)
    {
        parent::__construct($kernel, $request, $requestType);

        $this->setController($controller);
    }

    
    public function getController()
    {
        return $this->controller;
    }

    
    public function setController($controller)
    {
                if (!is_callable($controller)) {
            throw new \LogicException(sprintf('The controller must be a callable (%s given).', $this->varToString($controller)));
        }

        $this->controller = $controller;
    }

    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }

            return sprintf("Array(%s)", implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string) $var;
    }
}
}
 



namespace Symfony\Component\HttpKernel\Event
{

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class FilterResponseEvent extends KernelEvent
{
    
    private $response;

    public function __construct(HttpKernelInterface $kernel, Request $request, $requestType, Response $response)
    {
        parent::__construct($kernel, $request, $requestType);

        $this->setResponse($response);
    }

    
    public function getResponse()
    {
        return $this->response;
    }

    
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
}
 



namespace Symfony\Component\HttpKernel\Event
{

use Symfony\Component\HttpFoundation\Response;


class GetResponseEvent extends KernelEvent
{
    
    private $response;

    
    public function getResponse()
    {
        return $this->response;
    }

    
    public function setResponse(Response $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    
    public function hasResponse()
    {
        return null !== $this->response;
    }
}
}
 



namespace Symfony\Component\HttpKernel\Event
{

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;


class GetResponseForControllerResultEvent extends GetResponseEvent
{
    
    private $controllerResult;

    public function __construct(HttpKernelInterface $kernel, Request $request, $requestType, $controllerResult)
    {
        parent::__construct($kernel, $request, $requestType);

        $this->controllerResult = $controllerResult;
    }

    
    public function getControllerResult()
    {
        return $this->controllerResult;
    }
}
}
 



namespace Symfony\Component\HttpKernel\Event
{

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;


class GetResponseForExceptionEvent extends GetResponseEvent
{
    
    private $exception;

    public function __construct(HttpKernelInterface $kernel, Request $request, $requestType, \Exception $e)
    {
        parent::__construct($kernel, $request, $requestType);

        $this->setException($e);
    }

    
    public function getException()
    {
        return $this->exception;
    }

    
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}
}
 



namespace Symfony\Component\HttpKernel
{


final class KernelEvents
{
    
    const REQUEST = 'kernel.request';

    
    const EXCEPTION = 'kernel.exception';

    
    const VIEW = 'kernel.view';

    
    const CONTROLLER = 'kernel.controller';

    
    const RESPONSE = 'kernel.response';
}
}
 



namespace Symfony\Component\HttpKernel\Config
{

use Symfony\Component\Config\FileLocator as BaseFileLocator;
use Symfony\Component\HttpKernel\KernelInterface;


class FileLocator extends BaseFileLocator
{
    private $kernel;
    private $path;

    
    public function __construct(KernelInterface $kernel, $path = null, array $paths = array())
    {
        $this->kernel = $kernel;
        $this->path = $path;
        $paths[] = $path;

        parent::__construct($paths);
    }

    
    public function locate($file, $currentPath = null, $first = true)
    {
        if ('@' === $file[0]) {
            return $this->kernel->locateResource($file, $this->path, $first);
        }

        return parent::locate($file, $currentPath, $first);
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\EventListener
{

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;


class RouterListener
{
    private $router;
    private $logger;
    private $httpPort;
    private $httpsPort;

    public function __construct(RouterInterface $router, $httpPort = 80, $httpsPort = 443, LoggerInterface $logger = null)
    {
        $this->router = $router;
        $this->httpPort = $httpPort;
        $this->httpsPort = $httpsPort;
        $this->logger = $logger;
    }

    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $context = $this->router->getContext();

                        $context->setBaseUrl($request->getBaseUrl());
        $context->setMethod($request->getMethod());
        $context->setHost($request->getHost());
        $context->setScheme($request->getScheme());
        $context->setHttpPort($request->isSecure() ? $this->httpPort : $request->getPort());
        $context->setHttpsPort($request->isSecure() ? $request->getPort() : $this->httpsPort);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_controller')) {
                        return;
        }

                try {
            $parameters = $this->router->match($request->getPathInfo());

            if (null !== $this->logger) {
                $this->logger->info(sprintf('Matched route "%s" (parameters: %s)', $parameters['_route'], $this->parametersToString($parameters)));
            }

            $request->attributes->add($parameters);
        } catch (ResourceNotFoundException $e) {
            $message = sprintf('No route found for "%s %s"', $request->getMethod(), $request->getPathInfo());

            throw new NotFoundHttpException($message, $e);
        } catch (MethodNotAllowedException $e) {
            $message = sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $request->getMethod(), $request->getPathInfo(), strtoupper(implode(', ', $e->getAllowedMethods())));

            throw new MethodNotAllowedHttpException($e->getAllowedMethods(), $message, $e);
        }

        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $context = $this->router->getContext();
            $session = $request->getSession();
            if ($locale = $request->attributes->get('_locale')) {
                if ($session) {
                    $session->setLocale($locale);
                }
                $context->setParameter('_locale', $locale);
            } elseif ($session) {
                $context->setParameter('_locale', $session->getLocale());
            }
        }
    }

    private function parametersToString(array $parameters)
    {
        $pieces = array();
        foreach ($parameters as $key => $val) {
            $pieces[] = sprintf('"%s": "%s"', $key, (is_string($val) ? $val : json_encode($val)));
        }

        return implode(', ', $pieces);
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Controller
{

use Symfony\Component\HttpKernel\KernelInterface;


class ControllerNameParser
{
    protected $kernel;

    
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    
    public function parse($controller)
    {
        if (3 != count($parts = explode(':', $controller))) {
            throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid a:b:c controller string.', $controller));
        }

        list($bundle, $controller, $action) = $parts;
        $class = null;
        $logs = array();
        foreach ($this->kernel->getBundle($bundle, false) as $b) {
            $try = $b->getNamespace().'\\Controller\\'.$controller.'Controller';
            if (!class_exists($try)) {
                $logs[] = sprintf('Unable to find controller "%s:%s" - class "%s" does not exist.', $bundle, $controller, $try);
            } else {
                $class = $try;

                break;
            }
        }

        if (null === $class) {
            $this->handleControllerNotFoundException($bundle, $controller, $logs);
        }

        return $class.'::'.$action.'Action';
    }

    private function handleControllerNotFoundException($bundle, $controller, array $logs)
    {
                if (1 == count($logs)) {
            throw new \InvalidArgumentException($logs[0]);
        }

                $names = array();
        foreach ($this->kernel->getBundle($bundle, false) as $b) {
            $names[] = $b->getName();
        }
        $msg = sprintf('Unable to find controller "%s:%s" in bundles %s.', $bundle, $controller, implode(', ', $names));

        throw new \InvalidArgumentException($msg);
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle\Controller
{

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;


class ControllerResolver extends BaseControllerResolver
{
    protected $container;
    protected $parser;

    
    public function __construct(ContainerInterface $container, ControllerNameParser $parser, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->parser = $parser;

        parent::__construct($logger);
    }

    
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            $count = substr_count($controller, ':');
            if (2 == $count) {
                                $controller = $this->parser->parse($controller);
            } elseif (1 == $count) {
                                list($service, $method) = explode(':', $controller, 2);

                return array($this->container->get($service), $method);
            } else {
                throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
            }
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $controller = new $class();
        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return array($controller, $method);
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle
{

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;


class ContainerAwareEventDispatcher extends EventDispatcher
{
    
    private $container;

    
    private $listenerIds = array();

    
    private $listeners = array();

    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    
    public function addListenerService($eventName, $callback, $priority = 0)
    {
        if (!is_array($callback) || 2 !== count($callback)) {
            throw new \InvalidArgumentException('Expected an array("service", "method") argument');
        }

        $this->listenerIds[$eventName][] = array($callback[0], $callback[1], $priority);
    }

    public function removeListener($eventName, $listener)
    {
        $this->lazyLoad($eventName);

        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $key => $l) {
                foreach ($this->listenerIds[$eventName] as $i => $args) {
                    list($serviceId, $method, $priority) = $args;
                    if ($key === $serviceId.'.'.$method) {
                        if ($listener === array($l, $method)) {
                            unset($this->listeners[$eventName][$key]);
                            if (empty($this->listeners[$eventName])) {
                                unset($this->listeners[$eventName]);
                            }
                            unset($this->listenerIds[$eventName][$i]);
                            if (empty($this->listenerIds[$eventName])) {
                                unset($this->listenerIds[$eventName]);
                            }
                        }
                    }
                }
            }
        }

        parent::removeListener($eventName, $listener);
    }

    
    public function hasListeners($eventName = null)
    {
        if (null === $eventName) {
            return (Boolean) count($this->listenerIds) || (Boolean) count($this->listeners);
        }

        if (isset($this->listenerIds[$eventName])) {
            return true;
        }

        return parent::hasListeners($eventName);
    }

    
    public function getListeners($eventName = null)
    {
        if (null === $eventName) {
            foreach (array_keys($this->listenerIds) as $serviceEventName) {
                $this->lazyLoad($serviceEventName);
            }
        } else {
            $this->lazyLoad($eventName);
        }

        return parent::getListeners($eventName);
    }

    
    public function dispatch($eventName, Event $event = null)
    {
        $this->lazyLoad($eventName);

        parent::dispatch($eventName, $event);
    }

    
    protected function lazyLoad($eventName)
    {
        if (isset($this->listenerIds[$eventName])) {
            foreach ($this->listenerIds[$eventName] as $args) {
                list($serviceId, $method, $priority) = $args;
                $listener = $this->container->get($serviceId);

                $key = $serviceId.'.'.$method;
                if (!isset($this->listeners[$eventName][$key])) {
                    $this->addListener($eventName, array($listener, $method), $priority);
                } elseif ($listener !== $this->listeners[$eventName][$key]) {
                    parent::removeListener($eventName, array($this->listeners[$eventName][$key], $method));
                    $this->addListener($eventName, array($listener, $method), $priority);
                }

                $this->listeners[$eventName][$key] = $listener;
            }
        }
    }
}
}
 



namespace Symfony\Bundle\FrameworkBundle
{

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernel as BaseHttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class HttpKernel extends BaseHttpKernel
{
    private $container;
    private $esiSupport;

    public function __construct(EventDispatcherInterface $dispatcher, ContainerInterface $container, ControllerResolverInterface $controllerResolver)
    {
        parent::__construct($dispatcher, $controllerResolver);

        $this->container = $container;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $request->headers->set('X-Php-Ob-Level', ob_get_level());

        $this->container->enterScope('request');
        $this->container->set('request', $request, 'request');

        try {
            $response = parent::handle($request, $type, $catch);
        } catch (\Exception $e) {
            $this->container->leaveScope('request');

            throw $e;
        }

        $this->container->leaveScope('request');

        return $response;
    }

    
    public function forward($controller, array $attributes = array(), array $query = array())
    {
        $attributes['_controller'] = $controller;
        $subRequest = $this->container->get('request')->duplicate($query, null, $attributes);

        return $this->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    
    public function render($controller, array $options = array())
    {
        $options = array_merge(array(
            'attributes'    => array(),
            'query'         => array(),
            'ignore_errors' => !$this->container->getParameter('kernel.debug'),
            'alt'           => array(),
            'standalone'    => false,
            'comment'       => '',
        ), $options);

        if (!is_array($options['alt'])) {
            $options['alt'] = array($options['alt']);
        }

        if (null === $this->esiSupport) {
            $this->esiSupport = $this->container->has('esi') && $this->container->get('esi')->hasSurrogateEsiCapability($this->container->get('request'));
        }

        if ($this->esiSupport && $options['standalone']) {
            $uri = $this->generateInternalUri($controller, $options['attributes'], $options['query']);

            $alt = '';
            if ($options['alt']) {
                $alt = $this->generateInternalUri($options['alt'][0], isset($options['alt'][1]) ? $options['alt'][1] : array(), isset($options['alt'][2]) ? $options['alt'][2] : array());
            }

            return $this->container->get('esi')->renderIncludeTag($uri, $alt, $options['ignore_errors'], $options['comment']);
        }

        $request = $this->container->get('request');

                if (0 === strpos($controller, '/')) {
            $subRequest = Request::create($request->getUriForPath($controller), 'get', array(), $request->cookies->all(), array(), $request->server->all());
            if ($session = $request->getSession()) {
                $subRequest->setSession($session);
            }
        } else {
            $options['attributes']['_controller'] = $controller;

            if (!isset($options['attributes']['_format'])) {
                $options['attributes']['_format'] = $request->getRequestFormat();
            }

            $options['attributes']['_route'] = '_internal';
            $subRequest = $request->duplicate($options['query'], null, $options['attributes']);
            $subRequest->setMethod('GET');
        }

        $level = ob_get_level();
        try {
            $response = $this->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);

            if (!$response->isSuccessful()) {
                throw new \RuntimeException(sprintf('Error when rendering "%s" (Status code is %s).', $request->getUri(), $response->getStatusCode()));
            }

            return $response->getContent();
        } catch (\Exception $e) {
            if ($options['alt']) {
                $alt = $options['alt'];
                unset($options['alt']);
                $options['attributes'] = isset($alt[1]) ? $alt[1] : array();
                $options['query'] = isset($alt[2]) ? $alt[2] : array();

                return $this->render($alt[0], $options);
            }

            if (!$options['ignore_errors']) {
                throw $e;
            }

                        while (ob_get_level() > $level) {
                ob_get_clean();
            }
        }
    }

    
    public function generateInternalUri($controller, array $attributes = array(), array $query = array())
    {
        if (0 === strpos($controller, '/')) {
            return $controller;
        }

        $path = http_build_query($attributes, '', '&');
        $uri = $this->container->get('router')->generate('_internal', array(
            'controller' => $controller,
            'path'       => $path ?: 'none',
            '_format'    => $this->container->get('request')->getRequestFormat(),
        ));

        if ($queryString = http_build_query($query, '', '&')) {
            $uri .= '?'.$queryString;
        }

        return $uri;
    }
}
}
 



namespace Monolog\Formatter
{


interface FormatterInterface
{
    
    function format(array $record);

    
    function formatBatch(array $records);
}
}
 



namespace Monolog\Formatter
{

use Monolog\Logger;


class LineFormatter implements FormatterInterface
{
    const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
    const SIMPLE_DATE = "Y-m-d H:i:s";

    protected $format;
    protected $dateFormat;

    
    public function __construct($format = null, $dateFormat = null)
    {
        $this->format = $format ?: static::SIMPLE_FORMAT;
        $this->dateFormat = $dateFormat ?: static::SIMPLE_DATE;
    }

    
    public function format(array $record)
    {
        $vars = $record;
        $vars['datetime'] = $vars['datetime']->format($this->dateFormat);

        $output = $this->format;
        foreach ($vars['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.'.$var.'%')) {
                $output = str_replace('%extra.'.$var.'%', $this->convertToString($val), $output);
                unset($vars['extra'][$var]);
            }
        }
        foreach ($vars as $var => $val) {
            $output = str_replace('%'.$var.'%', $this->convertToString($val), $output);
        }

        return $output;
    }

    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    protected function convertToString($data)
    {
        if (null === $data || is_scalar($data)) {
            return (string) $data;
        }

        return stripslashes(json_encode($this->normalize($data)));
    }

    protected function normalize($data)
    {
        if (null === $data || is_scalar($data)) {
            return $data;
        }

        if (is_array($data) || $data instanceof \Traversable) {
            $normalized = array();

            foreach ($data as $key => $value) {
                $normalized[$key] = $this->normalize($value);
            }

            return $normalized;
        }

        if (is_resource($data)) {
            return '[resource]';
        }

        return sprintf("[object] (%s: %s)", get_class($data), json_encode($data));
    }
}
}
 



namespace Monolog\Handler
{

use Monolog\Formatter\FormatterInterface;


interface HandlerInterface
{
    
    function isHandling(array $record);

    
    function handle(array $record);

    
    function handleBatch(array $records);

    
    function pushProcessor($callback);

    
    function popProcessor();

    
    function setFormatter(FormatterInterface $formatter);

    
    function getFormatter();
}
}
 



namespace Monolog\Handler
{

use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;


abstract class AbstractHandler implements HandlerInterface
{
    protected $level = Logger::DEBUG;
    protected $bubble = false;

    
    protected $formatter;
    protected $processors = array();

    
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        $this->level = $level;
        $this->bubble = $bubble;
    }

    
    public function isHandling(array $record)
    {
        return $record['level'] >= $this->level;
    }

    
    public function handleBatch(array $records)
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    
    public function close()
    {
    }

    
    public function pushProcessor($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), '.var_export($callback, true).' given');
        }
        array_unshift($this->processors, $callback);
    }

    
    public function popProcessor()
    {
        if (!$this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }
        return array_shift($this->processors);
    }

    
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    
    public function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }

        return $this->formatter;
    }

    
    public function setLevel($level)
    {
        $this->level = $level;
    }

    
    public function getLevel()
    {
        return $this->level;
    }

    
    public function setBubble($bubble)
    {
        $this->bubble = $bubble;
    }

    
    public function getBubble()
    {
        return $this->bubble;
    }

    public function __destruct()
    {
        $this->close();
    }

    
    protected function getDefaultFormatter()
    {
        return new LineFormatter();
    }
}
}
 



namespace Monolog\Handler
{

use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;


abstract class AbstractProcessingHandler extends AbstractHandler
{
    
    public function handle(array $record)
    {
        if ($record['level'] < $this->level) {
            return false;
        }

        $record = $this->processRecord($record);

        $record['formatted'] = $this->getFormatter()->format($record);

        $this->write($record);

        return false === $this->bubble;
    }

    
    abstract protected function write(array $record);

    
    protected function processRecord(array $record)
    {
        if ($this->processors) {
            foreach ($this->processors as $processor) {
                $record = call_user_func($processor, $record);
            }
        }

        return $record;
    }
}
}
 



namespace Monolog\Handler
{

use Monolog\Formatter\SimpleFormatter;
use Monolog\Logger;


class StreamHandler extends AbstractProcessingHandler
{
    protected $stream;
    protected $url;

    
    public function __construct($stream, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        if (is_resource($stream)) {
            $this->stream = $stream;
        } else {
            $this->url = $stream;
        }
    }

    
    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
    }

    
    protected function write(array $record)
    {
        if (null === $this->stream) {
            if (!$this->url) {
                throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
            }
            $this->stream = @fopen($this->url, 'a');
            if (!is_resource($this->stream)) {
                $this->stream = null;
                throw new \UnexpectedValueException(sprintf('The stream or file "%s" could not be opened; it may be invalid or not writable.', $this->url));
            }
        }
        fwrite($this->stream, (string) $record['formatted']);
    }
}
}
 



namespace Monolog\Handler
{

use Monolog\Logger;


class FingersCrossedHandler extends AbstractHandler
{
    protected $handler;
    protected $actionLevel;
    protected $buffering = true;
    protected $bufferSize;
    protected $buffer = array();
    protected $stopBuffering;

    
    public function __construct($handler, $actionLevel = Logger::WARNING, $bufferSize = 0, $bubble = true, $stopBuffering = true)
    {
        $this->handler = $handler;
        $this->actionLevel = $actionLevel;
        $this->bufferSize = $bufferSize;
        $this->bubble = $bubble;
        $this->stopBuffering = $stopBuffering;
    }

    
    public function isHandling(array $record)
    {
        return true;
    }

    
    public function handle(array $record)
    {
        if ($this->buffering) {
            $this->buffer[] = $record;
            if ($this->bufferSize > 0 && count($this->buffer) > $this->bufferSize) {
                array_shift($this->buffer);
            }
            if ($record['level'] >= $this->actionLevel) {
                if ($this->stopBuffering) {
                    $this->buffering = false;
                }
                if (!$this->handler instanceof HandlerInterface) {
                    $this->handler = call_user_func($this->handler, $record, $this);
                }
                if (!$this->handler instanceof HandlerInterface) {
                    throw new \RuntimeException("The factory callback should return a HandlerInterface");
                }
                $this->handler->handleBatch($this->buffer);
                $this->buffer = array();
            }
        } else {
            $this->handler->handle($record);
        }

        return false === $this->bubble;
    }

    
    public function reset()
    {
        $this->buffering = true;
    }
}
}
 



namespace Monolog\Handler
{

use Monolog\Logger;


class TestHandler extends AbstractProcessingHandler
{
    protected $records = array();
    protected $recordsByLevel = array();

    public function getRecords()
    {
        return $this->records;
    }

    public function hasAlert($record)
    {
        return $this->hasRecord($record, Logger::ALERT);
    }

    public function hasCritical($record)
    {
        return $this->hasRecord($record, Logger::CRITICAL);
    }

    public function hasError($record)
    {
        return $this->hasRecord($record, Logger::ERROR);
    }

    public function hasWarning($record)
    {
        return $this->hasRecord($record, Logger::WARNING);
    }

    public function hasInfo($record)
    {
        return $this->hasRecord($record, Logger::INFO);
    }

    public function hasDebug($record)
    {
        return $this->hasRecord($record, Logger::DEBUG);
    }

    public function hasAlertRecords()
    {
        return isset($this->recordsByLevel[Logger::ALERT]);
    }

    public function hasCriticalRecords()
    {
        return isset($this->recordsByLevel[Logger::CRITICAL]);
    }

    public function hasErrorRecords()
    {
        return isset($this->recordsByLevel[Logger::ERROR]);
    }

    public function hasWarningRecords()
    {
        return isset($this->recordsByLevel[Logger::WARNING]);
    }

    public function hasInfoRecords()
    {
        return isset($this->recordsByLevel[Logger::INFO]);
    }

    public function hasDebugRecords()
    {
        return isset($this->recordsByLevel[Logger::DEBUG]);
    }

    protected function hasRecord($record, $level)
    {
        if (!isset($this->recordsByLevel[$level])) {
            return false;
        }

        if (is_array($record)) {
            $record = $record['message'];
        }

        foreach ($this->recordsByLevel[$level] as $rec) {
            if ($rec['message'] === $record) {
                return true;
            }
        }

        return false;
    }

    
    protected function write(array $record)
    {
        $this->recordsByLevel[$record['level']][] = $record;
        $this->records[] = $record;
    }
}}
 



namespace Monolog
{

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;


class Logger
{
    
    const DEBUG = 100;

    
    const INFO = 200;

    
    const WARNING = 300;

    
    const ERROR = 400;

    
    const CRITICAL = 500;

    
    const ALERT = 550;

    protected static $levels = array(
        100 => 'DEBUG',
        200 => 'INFO',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
    );

    protected $name;

    
    protected $handlers = array();

    protected $processors = array();

    
    public function __construct($name)
    {
        $this->name = $name;
    }

    
    public function getName()
    {
        return $this->name;
    }

    
    public function pushHandler(HandlerInterface $handler)
    {
        array_unshift($this->handlers, $handler);
    }

    
    public function popHandler()
    {
        if (!$this->handlers) {
            throw new \LogicException('You tried to pop from an empty handler stack.');
        }
        return array_shift($this->handlers);
    }

    
    public function pushProcessor($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), '.var_export($callback, true).' given');
        }
        array_unshift($this->processors, $callback);
    }

    
    public function popProcessor()
    {
        if (!$this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }
        return array_shift($this->processors);
    }

    
    public function addRecord($level, $message, array $context = array())
    {
        if (!$this->handlers) {
            $this->pushHandler(new StreamHandler('php://stderr', self::DEBUG));
        }
        $record = array(
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'level_name' => self::getLevelName($level),
            'channel' => $this->name,
            'datetime' => new \DateTime(),
            'extra' => array(),
        );
                $handlerKey = null;
        foreach ($this->handlers as $key => $handler) {
            if ($handler->isHandling($record)) {
                $handlerKey = $key;
                break;
            }
        }
                if (null === $handlerKey) {
            return false;
        }
                foreach ($this->processors as $processor) {
            $record = call_user_func($processor, $record);
        }
        while (isset($this->handlers[$handlerKey]) &&
            false === $this->handlers[$handlerKey]->handle($record)) {
            $handlerKey++;
        }

        return true;
    }

    
    public function addDebug($message, array $context = array())
    {
        return $this->addRecord(self::DEBUG, $message, $context);
    }

    
    public function addInfo($message, array $context = array())
    {
        return $this->addRecord(self::INFO, $message, $context);
    }

    
    public function addWarning($message, array $context = array())
    {
        return $this->addRecord(self::WARNING, $message, $context);
    }

    
    public function addError($message, array $context = array())
    {
        return $this->addRecord(self::ERROR, $message, $context);
    }

    
    public function addCritical($message, array $context = array())
    {
        return $this->addRecord(self::CRITICAL, $message, $context);
    }

    
    public function addAlert($message, array $context = array())
    {
        return $this->addRecord(self::ALERT, $message, $context);
    }

    
    public static function getLevelName($level)
    {
        return self::$levels[$level];
    }

    
    
    public function debug($message, array $context = array())
    {
        return $this->addRecord(self::DEBUG, $message, $context);
    }

    
    public function info($message, array $context = array())
    {
        return $this->addRecord(self::INFO, $message, $context);
    }

    
    public function notice($message, array $context = array())
    {
        return $this->addRecord(self::INFO, $message, $context);
    }

    
    public function warn($message, array $context = array())
    {
        return $this->addRecord(self::WARNING, $message, $context);
    }

    
    public function err($message, array $context = array())
    {
        return $this->addRecord(self::ERROR, $message, $context);
    }

    
    public function crit($message, array $context = array())
    {
        return $this->addRecord(self::CRITICAL, $message, $context);
    }

    
    public function alert($message, array $context = array())
    {
        return $this->addRecord(self::ALERT, $message, $context);
    }

    
    public function emerg($message, array $context = array())
    {
        return $this->addRecord(self::ALERT, $message, $context);
    }
}
}
 



namespace Symfony\Bridge\Monolog
{

use Monolog\Logger as BaseLogger;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;


class Logger extends BaseLogger implements LoggerInterface, DebugLoggerInterface
{
    
    public function getLogs()
    {
        if ($logger = $this->getDebugLogger()) {
            return $logger->getLogs();
        }
    }

    
    public function countErrors()
    {
        if ($logger = $this->getDebugLogger()) {
            return $logger->countErrors();
        }
    }

    
    private function getDebugLogger()
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof DebugLoggerInterface) {
                return $handler;
            }
        }
    }
}
}
 



namespace Symfony\Bridge\Monolog\Handler
{

use Monolog\Logger;
use Monolog\Handler\TestHandler;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;


class DebugHandler extends TestHandler implements DebugLoggerInterface
{
    
    public function getLogs()
    {
        $records = array();
        foreach ($this->records as $record) {
            $records[] = array(
                'timestamp'    => $record['datetime']->getTimestamp(),
                'message'      => $record['message'],
                'priority'     => $record['level'],
                'priorityName' => $record['level_name'],
                'context'      => $record['context'],
            );
        }

        return $records;
    }

    
    public function countErrors()
    {
        $cnt = 0;
        $levels = array(Logger::ERROR, Logger::CRITICAL, Logger::ALERT);
        if (defined('Monolog\Logger::EMERGENCY')) {
            $levels[] = Logger::EMERGENCY;
        }
        foreach ($levels as $level) {
            if (isset($this->recordsByLevel[$level])) {
                $cnt += count($this->recordsByLevel[$level]);
            }
        }

        return $cnt;
    }
}
}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Stores the Twig configuration.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Twig_Environment
{
    const VERSION = '1.12.3';
    protected $charset;
    protected $loader;
    protected $debug;
    protected $autoReload;
    protected $cache;
    protected $lexer;
    protected $parser;
    protected $compiler;
    protected $baseTemplateClass;
    protected $extensions;
    protected $parsers;
    protected $visitors;
    protected $filters;
    protected $tests;
    protected $functions;
    protected $globals;
    protected $runtimeInitialized;
    protected $extensionInitialized;
    protected $loadedTemplates;
    protected $strictVariables;
    protected $unaryOperators;
    protected $binaryOperators;
    protected $templateClassPrefix = '__TwigTemplate_';
    protected $functionCallbacks;
    protected $filterCallbacks;
    protected $staging;
    /**
     * Constructor.
     *
     * Available options:
     *
     *  * debug: When set to true, it automatically set "auto_reload" to true as
     *           well (default to false).
     *
     *  * charset: The charset used by the templates (default to utf-8).
     *
     *  * base_template_class: The base template class to use for generated
     *                         templates (default to Twig_Template).
     *
     *  * cache: An absolute path where to store the compiled templates, or
     *           false to disable compilation cache (default).
     *
     *  * auto_reload: Whether to reload the template is the original source changed.
     *                 If you don't provide the auto_reload option, it will be
     *                 determined automatically base on the debug value.
     *
     *  * strict_variables: Whether to ignore invalid variables in templates
     *                      (default to false).
     *
     *  * autoescape: Whether to enable auto-escaping (default to html):
     *                  * false: disable auto-escaping
     *                  * true: equivalent to html
     *                  * html, js: set the autoescaping to one of the supported strategies
     *                  * PHP callback: a PHP callback that returns an escaping strategy based on the template "filename"
     *
     *  * optimizations: A flag that indicates which optimizations to apply
     *                   (default to -1 which means that all optimizations are enabled;
     *                   set it to 0 to disable).
     *
     * @param Twig_LoaderInterface $loader  A Twig_LoaderInterface instance
     * @param array                $options An array of options
     */
    public function __construct(Twig_LoaderInterface $loader = null, $options = array())
    {
        if (null !== $loader) {
            $this->setLoader($loader);
        }
        $options = array_merge(array(
            'debug'               => false,
            'charset'             => 'UTF-8',
            'base_template_class' => 'Twig_Template',
            'strict_variables'    => false,
            'autoescape'          => 'html',
            'cache'               => false,
            'auto_reload'         => null,
            'optimizations'       => -1,
        ), $options);
        $this->debug              = (bool) $options['debug'];
        $this->charset            = $options['charset'];
        $this->baseTemplateClass  = $options['base_template_class'];
        $this->autoReload         = null === $options['auto_reload'] ? $this->debug : (bool) $options['auto_reload'];
        $this->strictVariables    = (bool) $options['strict_variables'];
        $this->runtimeInitialized = false;
        $this->setCache($options['cache']);
        $this->functionCallbacks = array();
        $this->filterCallbacks = array();
        $this->addExtension(new Twig_Extension_Core());
        $this->addExtension(new Twig_Extension_Escaper($options['autoescape']));
        $this->addExtension(new Twig_Extension_Optimizer($options['optimizations']));
        $this->extensionInitialized = false;
        $this->staging = new Twig_Extension_Staging();
    }
    /**
     * Gets the base template class for compiled templates.
     *
     * @return string The base template class name
     */
    public function getBaseTemplateClass()
    {
        return $this->baseTemplateClass;
    }
    /**
     * Sets the base template class for compiled templates.
     *
     * @param string $class The base template class name
     */
    public function setBaseTemplateClass($class)
    {
        $this->baseTemplateClass = $class;
    }
    /**
     * Enables debugging mode.
     */
    public function enableDebug()
    {
        $this->debug = true;
    }
    /**
     * Disables debugging mode.
     */
    public function disableDebug()
    {
        $this->debug = false;
    }
    /**
     * Checks if debug mode is enabled.
     *
     * @return Boolean true if debug mode is enabled, false otherwise
     */
    public function isDebug()
    {
        return $this->debug;
    }
    /**
     * Enables the auto_reload option.
     */
    public function enableAutoReload()
    {
        $this->autoReload = true;
    }
    /**
     * Disables the auto_reload option.
     */
    public function disableAutoReload()
    {
        $this->autoReload = false;
    }
    /**
     * Checks if the auto_reload option is enabled.
     *
     * @return Boolean true if auto_reload is enabled, false otherwise
     */
    public function isAutoReload()
    {
        return $this->autoReload;
    }
    /**
     * Enables the strict_variables option.
     */
    public function enableStrictVariables()
    {
        $this->strictVariables = true;
    }
    /**
     * Disables the strict_variables option.
     */
    public function disableStrictVariables()
    {
        $this->strictVariables = false;
    }
    /**
     * Checks if the strict_variables option is enabled.
     *
     * @return Boolean true if strict_variables is enabled, false otherwise
     */
    public function isStrictVariables()
    {
        return $this->strictVariables;
    }
    /**
     * Gets the cache directory or false if cache is disabled.
     *
     * @return string|false
     */
    public function getCache()
    {
        return $this->cache;
    }
     /**
      * Sets the cache directory or false if cache is disabled.
      *
      * @param string|false $cache The absolute path to the compiled templates,
      *                            or false to disable cache
      */
    public function setCache($cache)
    {
        $this->cache = $cache ? $cache : false;
    }
    /**
     * Gets the cache filename for a given template.
     *
     * @param string $name The template name
     *
     * @return string The cache file name
     */
    public function getCacheFilename($name)
    {
        if (false === $this->cache) {
            return false;
        }
        $class = substr($this->getTemplateClass($name), strlen($this->templateClassPrefix));
        return $this->getCache().'/'.substr($class, 0, 2).'/'.substr($class, 2, 2).'/'.substr($class, 4).'.php';
    }
    /**
     * Gets the template class associated with the given string.
     *
     * @param string  $name  The name for which to calculate the template class name
     * @param integer $index The index if it is an embedded template
     *
     * @return string The template class name
     */
    public function getTemplateClass($name, $index = null)
    {
        return $this->templateClassPrefix.md5($this->getLoader()->getCacheKey($name)).(null === $index ? '' : '_'.$index);
    }
    /**
     * Gets the template class prefix.
     *
     * @return string The template class prefix
     */
    public function getTemplateClassPrefix()
    {
        return $this->templateClassPrefix;
    }
    /**
     * Renders a template.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function render($name, array $context = array())
    {
        return $this->loadTemplate($name)->render($context);
    }
    /**
     * Displays a template.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     */
    public function display($name, array $context = array())
    {
        $this->loadTemplate($name)->display($context);
    }
    /**
     * Loads a template by name.
     *
     * @param string  $name  The template name
     * @param integer $index The index if it is an embedded template
     *
     * @return Twig_TemplateInterface A template instance representing the given template name
     */
    public function loadTemplate($name, $index = null)
    {
        $cls = $this->getTemplateClass($name, $index);
        if (isset($this->loadedTemplates[$cls])) {
            return $this->loadedTemplates[$cls];
        }
        if (!class_exists($cls, false)) {
            if (false === $cache = $this->getCacheFilename($name)) {
                eval('?>'.$this->compileSource($this->getLoader()->getSource($name), $name));
            } else {
                if (!is_file($cache) || ($this->isAutoReload() && !$this->isTemplateFresh($name, filemtime($cache)))) {
                    $this->writeCacheFile($cache, $this->compileSource($this->getLoader()->getSource($name), $name));
                }
                require_once $cache;
            }
        }
        if (!$this->runtimeInitialized) {
            $this->initRuntime();
        }
        return $this->loadedTemplates[$cls] = new $cls($this);
    }
    /**
     * Returns true if the template is still fresh.
     *
     * Besides checking the loader for freshness information,
     * this method also checks if the enabled extensions have
     * not changed.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     *
     * @return Boolean true if the template is fresh, false otherwise
     */
    public function isTemplateFresh($name, $time)
    {
        foreach ($this->extensions as $extension) {
            $r = new ReflectionObject($extension);
            if (filemtime($r->getFileName()) > $time) {
                return false;
            }
        }
        return $this->getLoader()->isFresh($name, $time);
    }
    public function resolveTemplate($names)
    {
        if (!is_array($names)) {
            $names = array($names);
        }
        foreach ($names as $name) {
            if ($name instanceof Twig_Template) {
                return $name;
            }
            try {
                return $this->loadTemplate($name);
            } catch (Twig_Error_Loader $e) {
            }
        }
        if (1 === count($names)) {
            throw $e;
        }
        throw new Twig_Error_Loader(sprintf('Unable to find one of the following templates: "%s".', implode('", "', $names)));
    }
    /**
     * Clears the internal template cache.
     */
    public function clearTemplateCache()
    {
        $this->loadedTemplates = array();
    }
    /**
     * Clears the template cache files on the filesystem.
     */
    public function clearCacheFiles()
    {
        if (false === $this->cache) {
            return;
        }
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cache), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if ($file->isFile()) {
                @unlink($file->getPathname());
            }
        }
    }
    /**
     * Gets the Lexer instance.
     *
     * @return Twig_LexerInterface A Twig_LexerInterface instance
     */
    public function getLexer()
    {
        if (null === $this->lexer) {
            $this->lexer = new Twig_Lexer($this);
        }
        return $this->lexer;
    }
    /**
     * Sets the Lexer instance.
     *
     * @param Twig_LexerInterface A Twig_LexerInterface instance
     */
    public function setLexer(Twig_LexerInterface $lexer)
    {
        $this->lexer = $lexer;
    }
    /**
     * Tokenizes a source code.
     *
     * @param string $source The template source code
     * @param string $name   The template name
     *
     * @return Twig_TokenStream A Twig_TokenStream instance
     */
    public function tokenize($source, $name = null)
    {
        return $this->getLexer()->tokenize($source, $name);
    }
    /**
     * Gets the Parser instance.
     *
     * @return Twig_ParserInterface A Twig_ParserInterface instance
     */
    public function getParser()
    {
        if (null === $this->parser) {
            $this->parser = new Twig_Parser($this);
        }
        return $this->parser;
    }
    /**
     * Sets the Parser instance.
     *
     * @param Twig_ParserInterface A Twig_ParserInterface instance
     */
    public function setParser(Twig_ParserInterface $parser)
    {
        $this->parser = $parser;
    }
    /**
     * Parses a token stream.
     *
     * @param Twig_TokenStream $tokens A Twig_TokenStream instance
     *
     * @return Twig_Node_Module A Node tree
     */
    public function parse(Twig_TokenStream $tokens)
    {
        return $this->getParser()->parse($tokens);
    }
    /**
     * Gets the Compiler instance.
     *
     * @return Twig_CompilerInterface A Twig_CompilerInterface instance
     */
    public function getCompiler()
    {
        if (null === $this->compiler) {
            $this->compiler = new Twig_Compiler($this);
        }
        return $this->compiler;
    }
    /**
     * Sets the Compiler instance.
     *
     * @param Twig_CompilerInterface $compiler A Twig_CompilerInterface instance
     */
    public function setCompiler(Twig_CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }
    /**
     * Compiles a Node.
     *
     * @param Twig_NodeInterface $node A Twig_NodeInterface instance
     *
     * @return string The compiled PHP source code
     */
    public function compile(Twig_NodeInterface $node)
    {
        return $this->getCompiler()->compile($node)->getSource();
    }
    /**
     * Compiles a template source code.
     *
     * @param string $source The template source code
     * @param string $name   The template name
     *
     * @return string The compiled PHP source code
     */
    public function compileSource($source, $name = null)
    {
        try {
            return $this->compile($this->parse($this->tokenize($source, $name)));
        } catch (Twig_Error $e) {
            $e->setTemplateFile($name);
            throw $e;
        } catch (Exception $e) {
            throw new Twig_Error_Runtime(sprintf('An exception has been thrown during the compilation of a template ("%s").', $e->getMessage()), -1, $name, $e);
        }
    }
    /**
     * Sets the Loader instance.
     *
     * @param Twig_LoaderInterface $loader A Twig_LoaderInterface instance
     */
    public function setLoader(Twig_LoaderInterface $loader)
    {
        $this->loader = $loader;
    }
    /**
     * Gets the Loader instance.
     *
     * @return Twig_LoaderInterface A Twig_LoaderInterface instance
     */
    public function getLoader()
    {
        if (null === $this->loader) {
            throw new LogicException('You must set a loader first.');
        }
        return $this->loader;
    }
    /**
     * Sets the default template charset.
     *
     * @param string $charset The default charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    /**
     * Gets the default template charset.
     *
     * @return string The default charset
     */
    public function getCharset()
    {
        return $this->charset;
    }
    /**
     * Initializes the runtime environment.
     */
    public function initRuntime()
    {
        $this->runtimeInitialized = true;
        foreach ($this->getExtensions() as $extension) {
            $extension->initRuntime($this);
        }
    }
    /**
     * Returns true if the given extension is registered.
     *
     * @param string $name The extension name
     *
     * @return Boolean Whether the extension is registered or not
     */
    public function hasExtension($name)
    {
        return isset($this->extensions[$name]);
    }
    /**
     * Gets an extension by name.
     *
     * @param string $name The extension name
     *
     * @return Twig_ExtensionInterface A Twig_ExtensionInterface instance
     */
    public function getExtension($name)
    {
        if (!isset($this->extensions[$name])) {
            throw new Twig_Error_Runtime(sprintf('The "%s" extension is not enabled.', $name));
        }
        return $this->extensions[$name];
    }
    /**
     * Registers an extension.
     *
     * @param Twig_ExtensionInterface $extension A Twig_ExtensionInterface instance
     */
    public function addExtension(Twig_ExtensionInterface $extension)
    {
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to register extension "%s" as extensions have already been initialized.', $extension->getName()));
        }
        $this->extensions[$extension->getName()] = $extension;
    }
    /**
     * Removes an extension by name.
     *
     * This method is deprecated and you should not use it.
     *
     * @param string $name The extension name
     *
     * @deprecated since 1.12 (to be removed in 2.0)
     */
    public function removeExtension($name)
    {
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to remove extension "%s" as extensions have already been initialized.', $name));
        }
        unset($this->extensions[$name]);
    }
    /**
     * Registers an array of extensions.
     *
     * @param array $extensions An array of extensions
     */
    public function setExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }
    /**
     * Returns all registered extensions.
     *
     * @return array An array of extensions
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
    /**
     * Registers a Token Parser.
     *
     * @param Twig_TokenParserInterface $parser A Twig_TokenParserInterface instance
     */
    public function addTokenParser(Twig_TokenParserInterface $parser)
    {
        if ($this->extensionInitialized) {
            throw new LogicException('Unable to add a token parser as extensions have already been initialized.');
        }
        $this->staging->addTokenParser($parser);
    }
    /**
     * Gets the registered Token Parsers.
     *
     * @return Twig_TokenParserBrokerInterface A broker containing token parsers
     */
    public function getTokenParsers()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        return $this->parsers;
    }
    /**
     * Gets registered tags.
     *
     * Be warned that this method cannot return tags defined by Twig_TokenParserBrokerInterface classes.
     *
     * @return Twig_TokenParserInterface[] An array of Twig_TokenParserInterface instances
     */
    public function getTags()
    {
        $tags = array();
        foreach ($this->getTokenParsers()->getParsers() as $parser) {
            if ($parser instanceof Twig_TokenParserInterface) {
                $tags[$parser->getTag()] = $parser;
            }
        }
        return $tags;
    }
    /**
     * Registers a Node Visitor.
     *
     * @param Twig_NodeVisitorInterface $visitor A Twig_NodeVisitorInterface instance
     */
    public function addNodeVisitor(Twig_NodeVisitorInterface $visitor)
    {
        if ($this->extensionInitialized) {
            throw new LogicException('Unable to add a node visitor as extensions have already been initialized.', $extension->getName());
        }
        $this->staging->addNodeVisitor($visitor);
    }
    /**
     * Gets the registered Node Visitors.
     *
     * @return Twig_NodeVisitorInterface[] An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        return $this->visitors;
    }
    /**
     * Registers a Filter.
     *
     * @param string|Twig_SimpleFilter               $name   The filter name or a Twig_SimpleFilter instance
     * @param Twig_FilterInterface|Twig_SimpleFilter $filter A Twig_FilterInterface instance or a Twig_SimpleFilter instance
     */
    public function addFilter($name, $filter = null)
    {
        if (!$name instanceof Twig_SimpleFilter && !($filter instanceof Twig_SimpleFilter || $filter instanceof Twig_FilterInterface)) {
            throw new LogicException('A filter must be an instance of Twig_FilterInterface or Twig_SimpleFilter');
        }
        if ($name instanceof Twig_SimpleFilter) {
            $filter = $name;
            $name = $filter->getName();
        }
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to add filter "%s" as extensions have already been initialized.', $name));
        }
        $this->staging->addFilter($name, $filter);
    }
    /**
     * Get a filter by name.
     *
     * Subclasses may override this method and load filters differently;
     * so no list of filters is available.
     *
     * @param string $name The filter name
     *
     * @return Twig_Filter|false A Twig_Filter instance or false if the filter does not exist
     */
    public function getFilter($name)
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        }
        foreach ($this->filters as $pattern => $filter) {
            $pattern = str_replace('\\*', '(.*?)', preg_quote($pattern, '#'), $count);
            if ($count) {
                if (preg_match('#^'.$pattern.'$#', $name, $matches)) {
                    array_shift($matches);
                    $filter->setArguments($matches);
                    return $filter;
                }
            }
        }
        foreach ($this->filterCallbacks as $callback) {
            if (false !== $filter = call_user_func($callback, $name)) {
                return $filter;
            }
        }
        return false;
    }
    public function registerUndefinedFilterCallback($callable)
    {
        $this->filterCallbacks[] = $callable;
    }
    /**
     * Gets the registered Filters.
     *
     * Be warned that this method cannot return filters defined with registerUndefinedFunctionCallback.
     *
     * @return Twig_FilterInterface[] An array of Twig_FilterInterface instances
     *
     * @see registerUndefinedFilterCallback
     */
    public function getFilters()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        return $this->filters;
    }
    /**
     * Registers a Test.
     *
     * @param string|Twig_SimpleTest             $name The test name or a Twig_SimpleTest instance
     * @param Twig_TestInterface|Twig_SimpleTest $test A Twig_TestInterface instance or a Twig_SimpleTest instance
     */
    public function addTest($name, $test = null)
    {
        if (!$name instanceof Twig_SimpleTest && !($test instanceof Twig_SimpleTest || $test instanceof Twig_TestInterface)) {
            throw new LogicException('A test must be an instance of Twig_TestInterface or Twig_SimpleTest');
        }
        if ($name instanceof Twig_SimpleTest) {
            $test = $name;
            $name = $test->getName();
        }
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to add test "%s" as extensions have already been initialized.', $name));
        }
        $this->staging->addTest($name, $test);
    }
    /**
     * Gets the registered Tests.
     *
     * @return Twig_TestInterface[] An array of Twig_TestInterface instances
     */
    public function getTests()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        return $this->tests;
    }
    /**
     * Gets a test by name.
     *
     * @param string $name The test name
     *
     * @return Twig_Test|false A Twig_Test instance or false if the test does not exist
     */
    public function getTest($name)
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        if (isset($this->tests[$name])) {
            return $this->tests[$name];
        }
        return false;
    }
    /**
     * Registers a Function.
     *
     * @param string|Twig_SimpleFunction                 $name     The function name or a Twig_SimpleFunction instance
     * @param Twig_FunctionInterface|Twig_SimpleFunction $function A Twig_FunctionInterface instance or a Twig_SimpleFunction instance
     */
    public function addFunction($name, $function = null)
    {
        if (!$name instanceof Twig_SimpleFunction && !($function instanceof Twig_SimpleFunction || $function instanceof Twig_FunctionInterface)) {
            throw new LogicException('A function must be an instance of Twig_FunctionInterface or Twig_SimpleFunction');
        }
        if ($name instanceof Twig_SimpleFunction) {
            $function = $name;
            $name = $function->getName();
        }
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to add function "%s" as extensions have already been initialized.', $name));
        }
        $this->staging->addFunction($name, $function);
    }
    /**
     * Get a function by name.
     *
     * Subclasses may override this method and load functions differently;
     * so no list of functions is available.
     *
     * @param string $name function name
     *
     * @return Twig_Function|false A Twig_Function instance or false if the function does not exist
     */
    public function getFunction($name)
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        if (isset($this->functions[$name])) {
            return $this->functions[$name];
        }
        foreach ($this->functions as $pattern => $function) {
            $pattern = str_replace('\\*', '(.*?)', preg_quote($pattern, '#'), $count);
            if ($count) {
                if (preg_match('#^'.$pattern.'$#', $name, $matches)) {
                    array_shift($matches);
                    $function->setArguments($matches);
                    return $function;
                }
            }
        }
        foreach ($this->functionCallbacks as $callback) {
            if (false !== $function = call_user_func($callback, $name)) {
                return $function;
            }
        }
        return false;
    }
    public function registerUndefinedFunctionCallback($callable)
    {
        $this->functionCallbacks[] = $callable;
    }
    /**
     * Gets registered functions.
     *
     * Be warned that this method cannot return functions defined with registerUndefinedFunctionCallback.
     *
     * @return Twig_FunctionInterface[] An array of Twig_FunctionInterface instances
     *
     * @see registerUndefinedFunctionCallback
     */
    public function getFunctions()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        return $this->functions;
    }
    /**
     * Registers a Global.
     *
     * New globals can be added before compiling or rendering a template;
     * but after, you can only update existing globals.
     *
     * @param string $name  The global name
     * @param mixed  $value The global value
     */
    public function addGlobal($name, $value)
    {
        if ($this->extensionInitialized || $this->runtimeInitialized) {
            if (null === $this->globals) {
                $this->globals = $this->initGlobals();
            }
            /* This condition must be uncommented in Twig 2.0
            if (!array_key_exists($name, $this->globals)) {
                throw new LogicException(sprintf('Unable to add global "%s" as the runtime or the extensions have already been initialized.', $name));
            }
            */
        }
        if ($this->extensionInitialized || $this->runtimeInitialized) {
            // update the value
            $this->globals[$name] = $value;
        } else {
            $this->staging->addGlobal($name, $value);
        }
    }
    /**
     * Gets the registered Globals.
     *
     * @return array An array of globals
     */
    public function getGlobals()
    {
        if (!$this->runtimeInitialized && !$this->extensionInitialized) {
            return $this->initGlobals();
        }
        if (null === $this->globals) {
            $this->globals = $this->initGlobals();
        }
        return $this->globals;
    }
    /**
     * Merges a context with the defined globals.
     *
     * @param array $context An array representing the context
     *
     * @return array The context merged with the globals
     */
    public function mergeGlobals(array $context)
    {
        // we don't use array_merge as the context being generally
        // bigger than globals, this code is faster.
        foreach ($this->getGlobals() as $key => $value) {
            if (!array_key_exists($key, $context)) {
                $context[$key] = $value;
            }
        }
        return $context;
    }
    /**
     * Gets the registered unary Operators.
     *
     * @return array An array of unary operators
     */
    public function getUnaryOperators()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        return $this->unaryOperators;
    }
    /**
     * Gets the registered binary Operators.
     *
     * @return array An array of binary operators
     */
    public function getBinaryOperators()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }
        return $this->binaryOperators;
    }
    public function computeAlternatives($name, $items)
    {
        $alternatives = array();
        foreach ($items as $item) {
            $lev = levenshtein($name, $item);
            if ($lev <= strlen($name) / 3 || false !== strpos($item, $name)) {
                $alternatives[$item] = $lev;
            }
        }
        asort($alternatives);
        return array_keys($alternatives);
    }
    protected function initGlobals()
    {
        $globals = array();
        foreach ($this->extensions as $extension) {
            $globals = array_merge($globals, $extension->getGlobals());
        }
        return array_merge($globals, $this->staging->getGlobals());
    }
    protected function initExtensions()
    {
        if ($this->extensionInitialized) {
            return;
        }
        $this->extensionInitialized = true;
        $this->parsers = new Twig_TokenParserBroker();
        $this->filters = array();
        $this->functions = array();
        $this->tests = array();
        $this->visitors = array();
        $this->unaryOperators = array();
        $this->binaryOperators = array();
        foreach ($this->extensions as $extension) {
            $this->initExtension($extension);
        }
        $this->initExtension($this->staging);
    }
    protected function initExtension(Twig_ExtensionInterface $extension)
    {
        // filters
        foreach ($extension->getFilters() as $name => $filter) {
            if ($name instanceof Twig_SimpleFilter) {
                $filter = $name;
                $name = $filter->getName();
            } elseif ($filter instanceof Twig_SimpleFilter) {
                $name = $filter->getName();
            }
            $this->filters[$name] = $filter;
        }
        // functions
        foreach ($extension->getFunctions() as $name => $function) {
            if ($name instanceof Twig_SimpleFunction) {
                $function = $name;
                $name = $function->getName();
            } elseif ($function instanceof Twig_SimpleFunction) {
                $name = $function->getName();
            }
            $this->functions[$name] = $function;
        }
        // tests
        foreach ($extension->getTests() as $name => $test) {
            if ($name instanceof Twig_SimpleTest) {
                $test = $name;
                $name = $test->getName();
            } elseif ($test instanceof Twig_SimpleTest) {
                $name = $test->getName();
            }
            $this->tests[$name] = $test;
        }
        // token parsers
        foreach ($extension->getTokenParsers() as $parser) {
            if ($parser instanceof Twig_TokenParserInterface) {
                $this->parsers->addTokenParser($parser);
            } elseif ($parser instanceof Twig_TokenParserBrokerInterface) {
                $this->parsers->addTokenParserBroker($parser);
            } else {
                throw new LogicException('getTokenParsers() must return an array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances');
            }
        }
        // node visitors
        foreach ($extension->getNodeVisitors() as $visitor) {
            $this->visitors[] = $visitor;
        }
        // operators
        if ($operators = $extension->getOperators()) {
            if (2 !== count($operators)) {
                throw new InvalidArgumentException(sprintf('"%s::getOperators()" does not return a valid operators array.', get_class($extension)));
            }
            $this->unaryOperators = array_merge($this->unaryOperators, $operators[0]);
            $this->binaryOperators = array_merge($this->binaryOperators, $operators[1]);
        }
    }
    protected function writeCacheFile($file, $content)
    {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new RuntimeException(sprintf("Unable to create the cache directory (%s).", $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new RuntimeException(sprintf("Unable to write in the cache directory (%s).", $dir));
        }
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content)) {
            // rename does not work on Win32 before 5.2.6
            if (@rename($tmpFile, $file) || (@copy($tmpFile, $file) && unlink($tmpFile))) {
                @chmod($file, 0666 & ~umask());
                return;
            }
        }
        throw new RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }
}

}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Interface implemented by extension classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface Twig_ExtensionInterface
{
    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(Twig_Environment $environment);
    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers();
    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return array An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors();
    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters();
    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return array An array of tests
     */
    public function getTests();
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions();
    /**
     * Returns a list of operators to add to the existing list.
     *
     * @return array An array of operators
     */
    public function getOperators();
    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals();
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName();
}

}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class Twig_Extension implements Twig_ExtensionInterface
{
    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(Twig_Environment $environment)
    {
    }
    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array();
    }
    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return array An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        return array();
    }
    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array();
    }
    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return array An array of tests
     */
    public function getTests()
    {
        return array();
    }
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array();
    }
    /**
     * Returns a list of operators to add to the existing list.
     *
     * @return array An array of operators
     */
    public function getOperators()
    {
        return array();
    }
    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        return array();
    }
}

}

namespace
{

if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}
/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extension_Core extends Twig_Extension
{
    protected $dateFormats = array('F j, Y H:i', '%d days');
    protected $numberFormat = array(0, '.', ',');
    protected $timezone = null;
    /**
     * Sets the default format to be used by the date filter.
     *
     * @param string $format             The default date format string
     * @param string $dateIntervalFormat The default date interval format string
     */
    public function setDateFormat($format = null, $dateIntervalFormat = null)
    {
        if (null !== $format) {
            $this->dateFormats[0] = $format;
        }
        if (null !== $dateIntervalFormat) {
            $this->dateFormats[1] = $dateIntervalFormat;
        }
    }
    /**
     * Gets the default format to be used by the date filter.
     *
     * @return array The default date format string and the default date interval format string
     */
    public function getDateFormat()
    {
        return $this->dateFormats;
    }
    /**
     * Sets the default timezone to be used by the date filter.
     *
     * @param DateTimeZone|string $timezone The default timezone string or a DateTimeZone object
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
    }
    /**
     * Gets the default timezone to be used by the date filter.
     *
     * @return DateTimeZone The default timezone currently in use
     */
    public function getTimezone()
    {
        if (null === $this->timezone) {
            $this->timezone = new DateTimeZone(date_default_timezone_get());
        }
        return $this->timezone;
    }
    /**
     * Sets the default format to be used by the number_format filter.
     *
     * @param integer $decimal      The number of decimal places to use.
     * @param string  $decimalPoint The character(s) to use for the decimal point.
     * @param string  $thousandSep  The character(s) to use for the thousands separator.
     */
    public function setNumberFormat($decimal, $decimalPoint, $thousandSep)
    {
        $this->numberFormat = array($decimal, $decimalPoint, $thousandSep);
    }
    /**
     * Get the default format used by the number_format filter.
     *
     * @return array The arguments for number_format()
     */
    public function getNumberFormat()
    {
        return $this->numberFormat;
    }
    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
            new Twig_TokenParser_For(),
            new Twig_TokenParser_If(),
            new Twig_TokenParser_Extends(),
            new Twig_TokenParser_Include(),
            new Twig_TokenParser_Block(),
            new Twig_TokenParser_Use(),
            new Twig_TokenParser_Filter(),
            new Twig_TokenParser_Macro(),
            new Twig_TokenParser_Import(),
            new Twig_TokenParser_From(),
            new Twig_TokenParser_Set(),
            new Twig_TokenParser_Spaceless(),
            new Twig_TokenParser_Flush(),
            new Twig_TokenParser_Do(),
            new Twig_TokenParser_Embed(),
        );
    }
    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        $filters = array(
            // formatting filters
            new Twig_SimpleFilter('date', 'twig_date_format_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('date_modify', 'twig_date_modify_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('format', 'sprintf'),
            new Twig_SimpleFilter('replace', 'strtr'),
            new Twig_SimpleFilter('number_format', 'twig_number_format_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('abs', 'abs'),
            // encoding
            new Twig_SimpleFilter('url_encode', 'twig_urlencode_filter'),
            new Twig_SimpleFilter('json_encode', 'twig_jsonencode_filter'),
            new Twig_SimpleFilter('convert_encoding', 'twig_convert_encoding'),
            // string filters
            new Twig_SimpleFilter('title', 'twig_title_string_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('capitalize', 'twig_capitalize_string_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('upper', 'strtoupper'),
            new Twig_SimpleFilter('lower', 'strtolower'),
            new Twig_SimpleFilter('striptags', 'strip_tags'),
            new Twig_SimpleFilter('trim', 'trim'),
            new Twig_SimpleFilter('nl2br', 'nl2br', array('pre_escape' => 'html', 'is_safe' => array('html'))),
            // array helpers
            new Twig_SimpleFilter('join', 'twig_join_filter'),
            new Twig_SimpleFilter('split', 'twig_split_filter'),
            new Twig_SimpleFilter('sort', 'twig_sort_filter'),
            new Twig_SimpleFilter('merge', 'twig_array_merge'),
            new Twig_SimpleFilter('batch', 'twig_array_batch'),
            // string/array filters
            new Twig_SimpleFilter('reverse', 'twig_reverse_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('length', 'twig_length_filter', array('needs_environment' => true)),
            new Twig_SimpleFilter('slice', 'twig_slice', array('needs_environment' => true)),
            new Twig_SimpleFilter('first', 'twig_first', array('needs_environment' => true)),
            new Twig_SimpleFilter('last', 'twig_last', array('needs_environment' => true)),
            // iteration and runtime
            new Twig_SimpleFilter('default', '_twig_default_filter', array('node_class' => 'Twig_Node_Expression_Filter_Default')),
            new Twig_SimpleFilter('keys', 'twig_get_array_keys_filter'),
            // escaping
            new Twig_SimpleFilter('escape', 'twig_escape_filter', array('needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe')),
            new Twig_SimpleFilter('e', 'twig_escape_filter', array('needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe')),
        );
        if (function_exists('mb_get_info')) {
            $filters[] = new Twig_SimpleFilter('upper', 'twig_upper_filter', array('needs_environment' => true));
            $filters[] = new Twig_SimpleFilter('lower', 'twig_lower_filter', array('needs_environment' => true));
        }
        return $filters;
    }
    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('range', 'range'),
            new Twig_SimpleFunction('constant', 'twig_constant'),
            new Twig_SimpleFunction('cycle', 'twig_cycle'),
            new Twig_SimpleFunction('random', 'twig_random', array('needs_environment' => true)),
            new Twig_SimpleFunction('date', 'twig_date_converter', array('needs_environment' => true)),
            new Twig_SimpleFunction('include', 'twig_include', array('needs_environment' => true, 'needs_context' => true)),
        );
    }
    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return array An array of tests
     */
    public function getTests()
    {
        return array(
            new Twig_SimpleTest('even', null, array('node_class' => 'Twig_Node_Expression_Test_Even')),
            new Twig_SimpleTest('odd', null, array('node_class' => 'Twig_Node_Expression_Test_Odd')),
            new Twig_SimpleTest('defined', null, array('node_class' => 'Twig_Node_Expression_Test_Defined')),
            new Twig_SimpleTest('sameas', null, array('node_class' => 'Twig_Node_Expression_Test_Sameas')),
            new Twig_SimpleTest('none', null, array('node_class' => 'Twig_Node_Expression_Test_Null')),
            new Twig_SimpleTest('null', null, array('node_class' => 'Twig_Node_Expression_Test_Null')),
            new Twig_SimpleTest('divisibleby', null, array('node_class' => 'Twig_Node_Expression_Test_Divisibleby')),
            new Twig_SimpleTest('constant', null, array('node_class' => 'Twig_Node_Expression_Test_Constant')),
            new Twig_SimpleTest('empty', 'twig_test_empty'),
            new Twig_SimpleTest('iterable', 'twig_test_iterable'),
        );
    }
    /**
     * Returns a list of operators to add to the existing list.
     *
     * @return array An array of operators
     */
    public function getOperators()
    {
        return array(
            array(
                'not' => array('precedence' => 50, 'class' => 'Twig_Node_Expression_Unary_Not'),
                '-'   => array('precedence' => 500, 'class' => 'Twig_Node_Expression_Unary_Neg'),
                '+'   => array('precedence' => 500, 'class' => 'Twig_Node_Expression_Unary_Pos'),
            ),
            array(
                'or'     => array('precedence' => 10, 'class' => 'Twig_Node_Expression_Binary_Or', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'and'    => array('precedence' => 15, 'class' => 'Twig_Node_Expression_Binary_And', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'b-or'   => array('precedence' => 16, 'class' => 'Twig_Node_Expression_Binary_BitwiseOr', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'b-xor'  => array('precedence' => 17, 'class' => 'Twig_Node_Expression_Binary_BitwiseXor', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'b-and'  => array('precedence' => 18, 'class' => 'Twig_Node_Expression_Binary_BitwiseAnd', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '=='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Equal', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '!='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_NotEqual', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '<'      => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Less', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '>'      => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Greater', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '>='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_GreaterEqual', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '<='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_LessEqual', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'not in' => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_NotIn', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'in'     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_In', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '..'     => array('precedence' => 25, 'class' => 'Twig_Node_Expression_Binary_Range', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '+'      => array('precedence' => 30, 'class' => 'Twig_Node_Expression_Binary_Add', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '-'      => array('precedence' => 30, 'class' => 'Twig_Node_Expression_Binary_Sub', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '~'      => array('precedence' => 40, 'class' => 'Twig_Node_Expression_Binary_Concat', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '*'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Mul', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '/'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Div', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '//'     => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_FloorDiv', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '%'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Mod', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'is'     => array('precedence' => 100, 'callable' => array($this, 'parseTestExpression'), 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'is not' => array('precedence' => 100, 'callable' => array($this, 'parseNotTestExpression'), 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '**'     => array('precedence' => 200, 'class' => 'Twig_Node_Expression_Binary_Power', 'associativity' => Twig_ExpressionParser::OPERATOR_RIGHT),
            ),
        );
    }
    public function parseNotTestExpression(Twig_Parser $parser, $node)
    {
        return new Twig_Node_Expression_Unary_Not($this->parseTestExpression($parser, $node), $parser->getCurrentToken()->getLine());
    }
    public function parseTestExpression(Twig_Parser $parser, $node)
    {
        $stream = $parser->getStream();
        $name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
        $arguments = null;
        if ($stream->test(Twig_Token::PUNCTUATION_TYPE, '(')) {
            $arguments = $parser->getExpressionParser()->parseArguments(true);
        }
        $class = $this->getTestNodeClass($parser, $name, $node->getLine());
        return new $class($node, $name, $arguments, $parser->getCurrentToken()->getLine());
    }
    protected function getTestNodeClass(Twig_Parser $parser, $name, $line)
    {
        $env = $parser->getEnvironment();
        $testMap = $env->getTests();
        if (!isset($testMap[$name])) {
            $message = sprintf('The test "%s" does not exist', $name);
            if ($alternatives = $env->computeAlternatives($name, array_keys($env->getTests()))) {
                $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
            }
            throw new Twig_Error_Syntax($message, $line, $parser->getFilename());
        }
        if ($testMap[$name] instanceof Twig_SimpleTest) {
            return $testMap[$name]->getNodeClass();
        }
        return $testMap[$name] instanceof Twig_Test_Node ? $testMap[$name]->getClass() : 'Twig_Node_Expression_Test';
    }
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'core';
    }
}
/**
 * Cycles over a value.
 *
 * @param ArrayAccess|array $values   An array or an ArrayAccess instance
 * @param integer           $position The cycle position
 *
 * @return string The next value in the cycle
 */
function twig_cycle($values, $position)
{
    if (!is_array($values) && !$values instanceof ArrayAccess) {
        return $values;
    }
    return $values[$position % count($values)];
}
/**
 * Returns a random value depending on the supplied parameter type:
 * - a random item from a Traversable or array
 * - a random character from a string
 * - a random integer between 0 and the integer parameter
 *
 * @param Twig_Environment                 $env    A Twig_Environment instance
 * @param Traversable|array|integer|string $values The values to pick a random item from
 *
 * @throws Twig_Error_Runtime When $values is an empty array (does not apply to an empty string which is returned as is).
 *
 * @return mixed A random value from the given sequence
 */
function twig_random(Twig_Environment $env, $values = null)
{
    if (null === $values) {
        return mt_rand();
    }
    if (is_int($values) || is_float($values)) {
        return $values < 0 ? mt_rand($values, 0) : mt_rand(0, $values);
    }
    if ($values instanceof Traversable) {
        $values = iterator_to_array($values);
    } elseif (is_string($values)) {
        if ('' === $values) {
            return '';
        }
        if (null !== $charset = $env->getCharset()) {
            if ('UTF-8' != $charset) {
                $values = twig_convert_encoding($values, 'UTF-8', $charset);
            }
            // unicode version of str_split()
            // split at all positions, but not after the start and not before the end
            $values = preg_split('/(?<!^)(?!$)/u', $values);
            if ('UTF-8' != $charset) {
                foreach ($values as $i => $value) {
                    $values[$i] = twig_convert_encoding($value, $charset, 'UTF-8');
                }
            }
        } else {
            return $values[mt_rand(0, strlen($values) - 1)];
        }
    }
    if (!is_array($values)) {
        return $values;
    }
    if (0 === count($values)) {
        throw new Twig_Error_Runtime('The random function cannot pick from an empty array.');
    }
    return $values[array_rand($values, 1)];
}
/**
 * Converts a date to the given format.
 *
 * <pre>
 *   {{ post.published_at|date("m/d/Y") }}
 * </pre>
 *
 * @param Twig_Environment             $env      A Twig_Environment instance
 * @param DateTime|DateInterval|string $date     A date
 * @param string                       $format   A format
 * @param DateTimeZone|string          $timezone A timezone
 *
 * @return string The formatted date
 */
function twig_date_format_filter(Twig_Environment $env, $date, $format = null, $timezone = null)
{
    if (null === $format) {
        $formats = $env->getExtension('core')->getDateFormat();
        $format = $date instanceof DateInterval ? $formats[1] : $formats[0];
    }
    if ($date instanceof DateInterval) {
        return $date->format($format);
    }
    return twig_date_converter($env, $date, $timezone)->format($format);
}
/**
 * Returns a new date object modified
 *
 * <pre>
 *   {{ post.published_at|date_modify("-1day")|date("m/d/Y") }}
 * </pre>
 *
 * @param Twig_Environment  $env      A Twig_Environment instance
 * @param DateTime|string   $date     A date
 * @param string            $modifier A modifier string
 *
 * @return DateTime A new date object
 */
function twig_date_modify_filter(Twig_Environment $env, $date, $modifier)
{
    $date = twig_date_converter($env, $date, false);
    $date->modify($modifier);
    return $date;
}
/**
 * Converts an input to a DateTime instance.
 *
 * <pre>
 *    {% if date(user.created_at) < date('+2days') %}
 *      {# do something #}
 *    {% endif %}
 * </pre>
 *
 * @param Twig_Environment    $env      A Twig_Environment instance
 * @param DateTime|string     $date     A date
 * @param DateTimeZone|string $timezone A timezone
 *
 * @return DateTime A DateTime instance
 */
function twig_date_converter(Twig_Environment $env, $date = null, $timezone = null)
{
    // determine the timezone
    if (!$timezone) {
        $defaultTimezone = $env->getExtension('core')->getTimezone();
    } elseif (!$timezone instanceof DateTimeZone) {
        $defaultTimezone = new DateTimeZone($timezone);
    } else {
        $defaultTimezone = $timezone;
    }
    if ($date instanceof DateTime) {
        $date = clone $date;
        if (false !== $timezone) {
            $date->setTimezone($defaultTimezone);
        }
        return $date;
    }
    $asString = (string) $date;
    if (ctype_digit($asString) || (!empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1)))) {
        $date = '@'.$date;
    }
    $date = new DateTime($date, $defaultTimezone);
    if (false !== $timezone) {
        $date->setTimezone($defaultTimezone);
    }
    return $date;
}
/**
 * Number format filter.
 *
 * All of the formatting options can be left null, in that case the defaults will
 * be used.  Supplying any of the parameters will override the defaults set in the
 * environment object.
 *
 * @param Twig_Environment    $env          A Twig_Environment instance
 * @param mixed               $number       A float/int/string of the number to format
 * @param integer             $decimal      The number of decimal points to display.
 * @param string              $decimalPoint The character(s) to use for the decimal point.
 * @param string              $thousandSep  The character(s) to use for the thousands separator.
 *
 * @return string The formatted number
 */
function twig_number_format_filter(Twig_Environment $env, $number, $decimal = null, $decimalPoint = null, $thousandSep = null)
{
    $defaults = $env->getExtension('core')->getNumberFormat();
    if (null === $decimal) {
        $decimal = $defaults[0];
    }
    if (null === $decimalPoint) {
        $decimalPoint = $defaults[1];
    }
    if (null === $thousandSep) {
        $thousandSep = $defaults[2];
    }
    return number_format((float) $number, $decimal, $decimalPoint, $thousandSep);
}
/**
 * URL encodes a string as a path segment or an array as a query string.
 *
 * @param string|array $url A URL or an array of query parameters
 * @param bool         $raw true to use rawurlencode() instead of urlencode
 *
 * @return string The URL encoded value
 */
function twig_urlencode_filter($url, $raw = false)
{
    if (is_array($url)) {
        return http_build_query($url, '', '&');
    }
    if ($raw) {
        return rawurlencode($url);
    }
    return urlencode($url);
}
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    /**
     * JSON encodes a variable.
     *
     * @param mixed   $value   The value to encode.
     * @param integer $options Not used on PHP 5.2.x
     *
     * @return mixed The JSON encoded value
     */
    function twig_jsonencode_filter($value, $options = 0)
    {
        if ($value instanceof Twig_Markup) {
            $value = (string) $value;
        } elseif (is_array($value)) {
            array_walk_recursive($value, '_twig_markup2string');
        }
        return json_encode($value);
    }
} else {
    /**
     * JSON encodes a variable.
     *
     * @param mixed   $value   The value to encode.
     * @param integer $options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT
     *
     * @return mixed The JSON encoded value
     */
    function twig_jsonencode_filter($value, $options = 0)
    {
        if ($value instanceof Twig_Markup) {
            $value = (string) $value;
        } elseif (is_array($value)) {
            array_walk_recursive($value, '_twig_markup2string');
        }
        return json_encode($value, $options);
    }
}
function _twig_markup2string(&$value)
{
    if ($value instanceof Twig_Markup) {
        $value = (string) $value;
    }
}
/**
 * Merges an array with another one.
 *
 * <pre>
 *  {% set items = { 'apple': 'fruit', 'orange': 'fruit' } %}
 *
 *  {% set items = items|merge({ 'peugeot': 'car' }) %}
 *
 *  {# items now contains { 'apple': 'fruit', 'orange': 'fruit', 'peugeot': 'car' } #}
 * </pre>
 *
 * @param array $arr1 An array
 * @param array $arr2 An array
 *
 * @return array The merged array
 */
function twig_array_merge($arr1, $arr2)
{
    if (!is_array($arr1) || !is_array($arr2)) {
        throw new Twig_Error_Runtime('The merge filter only works with arrays or hashes.');
    }
    return array_merge($arr1, $arr2);
}
/**
 * Slices a variable.
 *
 * @param Twig_Environment $env          A Twig_Environment instance
 * @param mixed            $item         A variable
 * @param integer          $start        Start of the slice
 * @param integer          $length       Size of the slice
 * @param Boolean          $preserveKeys Whether to preserve key or not (when the input is an array)
 *
 * @return mixed The sliced variable
 */
function twig_slice(Twig_Environment $env, $item, $start, $length = null, $preserveKeys = false)
{
    if ($item instanceof Traversable) {
        $item = iterator_to_array($item, false);
    }
    if (is_array($item)) {
        return array_slice($item, $start, $length, $preserveKeys);
    }
    $item = (string) $item;
    if (function_exists('mb_get_info') && null !== $charset = $env->getCharset()) {
        return mb_substr($item, $start, null === $length ? mb_strlen($item, $charset) - $start : $length, $charset);
    }
    return null === $length ? substr($item, $start) : substr($item, $start, $length);
}
/**
 * Returns the first element of the item.
 *
 * @param Twig_Environment $env  A Twig_Environment instance
 * @param mixed            $item A variable
 *
 * @return mixed The first element of the item
 */
function twig_first(Twig_Environment $env, $item)
{
    $elements = twig_slice($env, $item, 0, 1, false);
    return is_string($elements) ? $elements[0] : current($elements);
}
/**
 * Returns the last element of the item.
 *
 * @param Twig_Environment $env  A Twig_Environment instance
 * @param mixed            $item A variable
 *
 * @return mixed The last element of the item
 */
function twig_last(Twig_Environment $env, $item)
{
    $elements = twig_slice($env, $item, -1, 1, false);
    return is_string($elements) ? $elements[0] : current($elements);
}
/**
 * Joins the values to a string.
 *
 * The separator between elements is an empty string per default, you can define it with the optional parameter.
 *
 * <pre>
 *  {{ [1, 2, 3]|join('|') }}
 *  {# returns 1|2|3 #}
 *
 *  {{ [1, 2, 3]|join }}
 *  {# returns 123 #}
 * </pre>
 *
 * @param array  $value An array
 * @param string $glue  The separator
 *
 * @return string The concatenated string
 */
function twig_join_filter($value, $glue = '')
{
    if ($value instanceof Traversable) {
        $value = iterator_to_array($value, false);
    }
    return implode($glue, (array) $value);
}
/**
 * Splits the string into an array.
 *
 * <pre>
 *  {{ "one,two,three"|split(',') }}
 *  {# returns [one, two, three] #}
 *
 *  {{ "one,two,three,four,five"|split(',', 3) }}
 *  {# returns [one, two, "three,four,five"] #}
 *
 *  {{ "123"|split('') }}
 *  {# returns [1, 2, 3] #}
 *
 *  {{ "aabbcc"|split('', 2) }}
 *  {# returns [aa, bb, cc] #}
 * </pre>
 *
 * @param string  $value     A string
 * @param string  $delimiter The delimiter
 * @param integer $limit     The limit
 *
 * @return array The split string as an array
 */
function twig_split_filter($value, $delimiter, $limit = null)
{
    if (empty($delimiter)) {
        return str_split($value, null === $limit ? 1 : $limit);
    }
    return null === $limit ? explode($delimiter, $value) : explode($delimiter, $value, $limit);
}
// The '_default' filter is used internally to avoid using the ternary operator
// which costs a lot for big contexts (before PHP 5.4). So, on average,
// a function call is cheaper.
function _twig_default_filter($value, $default = '')
{
    if (twig_test_empty($value)) {
        return $default;
    }
    return $value;
}
/**
 * Returns the keys for the given array.
 *
 * It is useful when you want to iterate over the keys of an array:
 *
 * <pre>
 *  {% for key in array|keys %}
 *      {# ... #}
 *  {% endfor %}
 * </pre>
 *
 * @param array $array An array
 *
 * @return array The keys
 */
function twig_get_array_keys_filter($array)
{
    if (is_object($array) && $array instanceof Traversable) {
        return array_keys(iterator_to_array($array));
    }
    if (!is_array($array)) {
        return array();
    }
    return array_keys($array);
}
/**
 * Reverses a variable.
 *
 * @param Twig_Environment         $env          A Twig_Environment instance
 * @param array|Traversable|string $item         An array, a Traversable instance, or a string
 * @param Boolean                  $preserveKeys Whether to preserve key or not
 *
 * @return mixed The reversed input
 */
function twig_reverse_filter(Twig_Environment $env, $item, $preserveKeys = false)
{
    if (is_object($item) && $item instanceof Traversable) {
        return array_reverse(iterator_to_array($item), $preserveKeys);
    }
    if (is_array($item)) {
        return array_reverse($item, $preserveKeys);
    }
    if (null !== $charset = $env->getCharset()) {
        $string = (string) $item;
        if ('UTF-8' != $charset) {
            $item = twig_convert_encoding($string, 'UTF-8', $charset);
        }
        preg_match_all('/./us', $item, $matches);
        $string = implode('', array_reverse($matches[0]));
        if ('UTF-8' != $charset) {
            $string = twig_convert_encoding($string, $charset, 'UTF-8');
        }
        return $string;
    }
    return strrev((string) $item);
}
/**
 * Sorts an array.
 *
 * @param array $array An array
 */
function twig_sort_filter($array)
{
    asort($array);
    return $array;
}
/* used internally */
function twig_in_filter($value, $compare)
{
    if (is_array($compare)) {
        return in_array($value, $compare, is_object($value));
    } elseif (is_string($compare)) {
        if (!strlen($value)) {
            return empty($compare);
        }
        return false !== strpos($compare, (string) $value);
    } elseif ($compare instanceof Traversable) {
        return in_array($value, iterator_to_array($compare, false), is_object($value));
    }
    return false;
}
/**
 * Escapes a string.
 *
 * @param Twig_Environment $env        A Twig_Environment instance
 * @param string           $string     The value to be escaped
 * @param string           $strategy   The escaping strategy
 * @param string           $charset    The charset
 * @param Boolean          $autoescape Whether the function is called by the auto-escaping feature (true) or by the developer (false)
 */
function twig_escape_filter(Twig_Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = false)
{
    if ($autoescape && is_object($string) && $string instanceof Twig_Markup) {
        return $string;
    }
    if (!is_string($string) && !(is_object($string) && method_exists($string, '__toString'))) {
        return $string;
    }
    if (null === $charset) {
        $charset = $env->getCharset();
    }
    $string = (string) $string;
    switch ($strategy) {
        case 'js':
            // escape all non-alphanumeric characters
            // into their \xHH or \uHHHH representations
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }
            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new Twig_Error_Runtime('The string to escape is not a valid UTF-8 string.');
            }
            $string = preg_replace_callback('#[^a-zA-Z0-9,\._]#Su', '_twig_escape_js_callback', $string);
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }
            return $string;
        case 'css':
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }
            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new Twig_Error_Runtime('The string to escape is not a valid UTF-8 string.');
            }
            $string = preg_replace_callback('#[^a-zA-Z0-9]#Su', '_twig_escape_css_callback', $string);
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }
            return $string;
        case 'html_attr':
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }
            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new Twig_Error_Runtime('The string to escape is not a valid UTF-8 string.');
            }
            $string = preg_replace_callback('#[^a-zA-Z0-9,\.\-_]#Su', '_twig_escape_html_attr_callback', $string);
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }
            return $string;
        case 'html':
            // see http://php.net/htmlspecialchars
            // Using a static variable to avoid initializing the array
            // each time the function is called. Moving the declaration on the
            // top of the function slow downs other escaping strategies.
            static $htmlspecialcharsCharsets = array(
                'iso-8859-1' => true, 'iso8859-1' => true,
                'iso-8859-15' => true, 'iso8859-15' => true,
                'utf-8' => true,
                'cp866' => true, 'ibm866' => true, '866' => true,
                'cp1251' => true, 'windows-1251' => true, 'win-1251' => true,
                '1251' => true,
                'cp1252' => true, 'windows-1252' => true, '1252' => true,
                'koi8-r' => true, 'koi8-ru' => true, 'koi8r' => true,
                'big5' => true, '950' => true,
                'gb2312' => true, '936' => true,
                'big5-hkscs' => true,
                'shift_jis' => true, 'sjis' => true, '932' => true,
                'euc-jp' => true, 'eucjp' => true,
                'iso8859-5' => true, 'iso-8859-5' => true, 'macroman' => true,
            );
            if (isset($htmlspecialcharsCharsets[strtolower($charset)])) {
                return @htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
            }
            $string = twig_convert_encoding($string, 'UTF-8', $charset);
            $string = @htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            return twig_convert_encoding($string, $charset, 'UTF-8');
        case 'url':
            if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                return str_replace('%7E', '~', rawurlencode($string));
            }
            return rawurlencode($string);
        default:
            throw new Twig_Error_Runtime(sprintf('Invalid escaping strategy "%s" (valid ones: html, js, url, css, and html_attr).', $strategy));
    }
}
/* used internally */
function twig_escape_filter_is_safe(Twig_Node $filterArgs)
{
    foreach ($filterArgs as $arg) {
        if ($arg instanceof Twig_Node_Expression_Constant) {
            return array($arg->getAttribute('value'));
        }
        return array();
    }
    return array('html');
}
if (function_exists('mb_convert_encoding')) {
    function twig_convert_encoding($string, $to, $from)
    {
        return mb_convert_encoding($string, $to, $from);
    }
} elseif (function_exists('iconv')) {
    function twig_convert_encoding($string, $to, $from)
    {
        return iconv($from, $to, $string);
    }
} else {
    function twig_convert_encoding($string, $to, $from)
    {
        throw new Twig_Error_Runtime('No suitable convert encoding function (use UTF-8 as your encoding or install the iconv or mbstring extension).');
    }
}
function _twig_escape_js_callback($matches)
{
    $char = $matches[0];
    // \xHH
    if (!isset($char[1])) {
        return '\\x'.strtoupper(substr('00'.bin2hex($char), -2));
    }
    // \uHHHH
    $char = twig_convert_encoding($char, 'UTF-16BE', 'UTF-8');
    return '\\u'.strtoupper(substr('0000'.bin2hex($char), -4));
}
function _twig_escape_css_callback($matches)
{
    $char = $matches[0];
    // \xHH
    if (!isset($char[1])) {
        $hex = ltrim(strtoupper(bin2hex($char)), '0');
        if (0 === strlen($hex)) {
            $hex = '0';
        }
        return '\\'.$hex.' ';
    }
    // \uHHHH
    $char = twig_convert_encoding($char, 'UTF-16BE', 'UTF-8');
    return '\\'.ltrim(strtoupper(bin2hex($char)), '0').' ';
}
/**
 * This function is adapted from code coming from Zend Framework.
 *
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
function _twig_escape_html_attr_callback($matches)
{
    /*
     * While HTML supports far more named entities, the lowest common denominator
     * has become HTML5's XML Serialisation which is restricted to the those named
     * entities that XML supports. Using HTML entities would result in this error:
     *     XML Parsing Error: undefined entity
     */
    static $entityMap = array(
        34 => 'quot', /* quotation mark */
        38 => 'amp',  /* ampersand */
        60 => 'lt',   /* less-than sign */
        62 => 'gt',   /* greater-than sign */
    );
    $chr = $matches[0];
    $ord = ord($chr);
    /**
     * The following replaces characters undefined in HTML with the
     * hex entity for the Unicode replacement character.
     */
    if (($ord <= 0x1f && $chr != "\t" && $chr != "\n" && $chr != "\r") || ($ord >= 0x7f && $ord <= 0x9f)) {
        return '&#xFFFD;';
    }
    /**
     * Check if the current character to escape has a name entity we should
     * replace it with while grabbing the hex value of the character.
     */
    if (strlen($chr) == 1) {
        $hex = strtoupper(substr('00'.bin2hex($chr), -2));
    } else {
        $chr = twig_convert_encoding($chr, 'UTF-16BE', 'UTF-8');
        $hex = strtoupper(substr('0000'.bin2hex($chr), -4));
    }
    $int = hexdec($hex);
    if (array_key_exists($int, $entityMap)) {
        return sprintf('&%s;', $entityMap[$int]);
    }
    /**
     * Per OWASP recommendations, we'll use hex entities for any other
     * characters where a named entity does not exist.
     */
    return sprintf('&#x%s;', $hex);
}
// add multibyte extensions if possible
if (function_exists('mb_get_info')) {
    /**
     * Returns the length of a variable.
     *
     * @param Twig_Environment $env   A Twig_Environment instance
     * @param mixed            $thing A variable
     *
     * @return integer The length of the value
     */
    function twig_length_filter(Twig_Environment $env, $thing)
    {
        return is_scalar($thing) ? mb_strlen($thing, $env->getCharset()) : count($thing);
    }
    /**
     * Converts a string to uppercase.
     *
     * @param Twig_Environment $env    A Twig_Environment instance
     * @param string           $string A string
     *
     * @return string The uppercased string
     */
    function twig_upper_filter(Twig_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtoupper($string, $charset);
        }
        return strtoupper($string);
    }
    /**
     * Converts a string to lowercase.
     *
     * @param Twig_Environment $env    A Twig_Environment instance
     * @param string           $string A string
     *
     * @return string The lowercased string
     */
    function twig_lower_filter(Twig_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtolower($string, $charset);
        }
        return strtolower($string);
    }
    /**
     * Returns a titlecased string.
     *
     * @param Twig_Environment $env    A Twig_Environment instance
     * @param string           $string A string
     *
     * @return string The titlecased string
     */
    function twig_title_string_filter(Twig_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_convert_case($string, MB_CASE_TITLE, $charset);
        }
        return ucwords(strtolower($string));
    }
    /**
     * Returns a capitalized string.
     *
     * @param Twig_Environment $env    A Twig_Environment instance
     * @param string           $string A string
     *
     * @return string The capitalized string
     */
    function twig_capitalize_string_filter(Twig_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtoupper(mb_substr($string, 0, 1, $charset), $charset).
                         mb_strtolower(mb_substr($string, 1, mb_strlen($string, $charset), $charset), $charset);
        }
        return ucfirst(strtolower($string));
    }
}
// and byte fallback
else {
    /**
     * Returns the length of a variable.
     *
     * @param Twig_Environment $env   A Twig_Environment instance
     * @param mixed            $thing A variable
     *
     * @return integer The length of the value
     */
    function twig_length_filter(Twig_Environment $env, $thing)
    {
        return is_scalar($thing) ? strlen($thing) : count($thing);
    }
    /**
     * Returns a titlecased string.
     *
     * @param Twig_Environment $env    A Twig_Environment instance
     * @param string           $string A string
     *
     * @return string The titlecased string
     */
    function twig_title_string_filter(Twig_Environment $env, $string)
    {
        return ucwords(strtolower($string));
    }
    /**
     * Returns a capitalized string.
     *
     * @param Twig_Environment $env    A Twig_Environment instance
     * @param string           $string A string
     *
     * @return string The capitalized string
     */
    function twig_capitalize_string_filter(Twig_Environment $env, $string)
    {
        return ucfirst(strtolower($string));
    }
}
/* used internally */
function twig_ensure_traversable($seq)
{
    if ($seq instanceof Traversable || is_array($seq)) {
        return $seq;
    }
    return array();
}
/**
 * Checks if a variable is empty.
 *
 * <pre>
 * {# evaluates to true if the foo variable is null, false, or the empty string #}
 * {% if foo is empty %}
 *     {# ... #}
 * {% endif %}
 * </pre>
 *
 * @param mixed $value A variable
 *
 * @return Boolean true if the value is empty, false otherwise
 */
function twig_test_empty($value)
{
    if ($value instanceof Countable) {
        return 0 == count($value);
    }
    return '' === $value || false === $value || null === $value || array() === $value;
}
/**
 * Checks if a variable is traversable.
 *
 * <pre>
 * {# evaluates to true if the foo variable is an array or a traversable object #}
 * {% if foo is traversable %}
 *     {# ... #}
 * {% endif %}
 * </pre>
 *
 * @param mixed $value A variable
 *
 * @return Boolean true if the value is traversable
 */
function twig_test_iterable($value)
{
    return $value instanceof Traversable || is_array($value);
}
/**
 * Renders a template.
 *
 * @param string  template       The template to render
 * @param array   variables      The variables to pass to the template
 * @param Boolean with_context   Whether to pass the current context variables or not
 * @param Boolean ignore_missing Whether to ignore missing templates or not
 * @param Boolean sandboxed      Whether to sandbox the template or not
 *
 * @return string The rendered template
 */
function twig_include(Twig_Environment $env, $context, $template, $variables = array(), $withContext = true, $ignoreMissing = false, $sandboxed = false)
{
    if ($withContext) {
        $variables = array_merge($context, $variables);
    }
    if ($isSandboxed = $sandboxed && $env->hasExtension('sandbox')) {
        $sandbox = $env->getExtension('sandbox');
        if (!$alreadySandboxed = $sandbox->isSandboxed()) {
            $sandbox->enableSandbox();
        }
    }
    try {
        return $env->resolveTemplate($template)->display($variables);
    } catch (Twig_Error_Loader $e) {
        if (!$ignoreMissing) {
            throw $e;
        }
    }
    if ($isSandboxed && !$alreadySandboxed) {
        $sandbox->disableSandbox();
    }
}
/**
 * Provides the ability to get constants from instances as well as class/global constants.
 *
 * @param string      $constant The name of the constant
 * @param null|object $object   The object to get the constant from
 *
 * @return string
 */
function twig_constant($constant, $object = null)
{
    if (null !== $object) {
        $constant = get_class($object).'::'.$constant;
    }
    return constant($constant);
}
/**
 * Batches item.
 *
 * @param array   $items An array of items
 * @param integer $size  The size of the batch
 * @param string  $fill  A string to fill missing items
 *
 * @return array
 */
function twig_array_batch($items, $size, $fill = null)
{
    if ($items instanceof Traversable) {
        $items = iterator_to_array($items, false);
    }
    $size = ceil($size);
    $result = array_chunk($items, $size, true);
    if (null !== $fill) {
        $last = count($result) - 1;
        $result[$last] = array_merge(
            $result[$last],
            array_fill(0, $size - count($result[$last]), $fill)
        );
    }
    return $result;
}

}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extension_Escaper extends Twig_Extension
{
    protected $defaultStrategy;
    public function __construct($defaultStrategy = 'html')
    {
        $this->setDefaultStrategy($defaultStrategy);
    }
    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array(new Twig_TokenParser_AutoEscape());
    }
    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return array An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        return array(new Twig_NodeVisitor_Escaper());
    }
    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('raw', 'twig_raw_filter', array('is_safe' => array('all'))),
        );
    }
    /**
     * Sets the default strategy to use when not defined by the user.
     *
     * The strategy can be a valid PHP callback that takes the template
     * "filename" as an argument and returns the strategy to use.
     *
     * @param mixed $defaultStrategy An escaping strategy
     */
    public function setDefaultStrategy($defaultStrategy)
    {
        // for BC
        if (true === $defaultStrategy) {
            $defaultStrategy = 'html';
        }
        $this->defaultStrategy = $defaultStrategy;
    }
    /**
     * Gets the default strategy to use when not defined by the user.
     *
     * @param string $filename The template "filename"
     *
     * @return string The default strategy to use for the template
     */
    public function getDefaultStrategy($filename)
    {
        // disable string callables to avoid calling a function named html or js,
        // or any other upcoming escaping strategy
        if (!is_string($this->defaultStrategy) && is_callable($this->defaultStrategy)) {
            return call_user_func($this->defaultStrategy, $filename);
        }
        return $this->defaultStrategy;
    }
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'escaper';
    }
}
/**
 * Marks a variable as being safe.
 *
 * @param string $string A PHP variable
 */
function twig_raw_filter($string)
{
    return $string;
}

}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extension_Optimizer extends Twig_Extension
{
    protected $optimizers;
    public function __construct($optimizers = -1)
    {
        $this->optimizers = $optimizers;
    }
    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return array(new Twig_NodeVisitor_Optimizer($this->optimizers));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'optimizer';
    }
}

}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Interface all loaders must implement.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface Twig_LoaderInterface
{
    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The template source code
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSource($name);
    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getCacheKey($name);
    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     *
     * @return Boolean true if the template is fresh, false otherwise
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function isFresh($name, $time);
}

}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Marks a content as safe.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Twig_Markup implements Countable
{
    protected $content;
    protected $charset;
    public function __construct($content, $charset)
    {
        $this->content = (string) $content;
        $this->charset = $charset;
    }
    public function __toString()
    {
        return $this->content;
    }
    public function count()
    {
        return function_exists('mb_get_info') ? mb_strlen($this->content, $this->charset) : strlen($this->content);
    }
}

}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Interface implemented by all compiled templates.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
interface Twig_TemplateInterface
{
    const ANY_CALL    = 'any';
    const ARRAY_CALL  = 'array';
    const METHOD_CALL = 'method';
    /**
     * Renders the template with the given context and returns it as string.
     *
     * @param array $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function render(array $context);
    /**
     * Displays the template with the given context.
     *
     * @param array $context An array of parameters to pass to the template
     * @param array $blocks  An array of blocks to pass to the template
     */
    public function display(array $context, array $blocks = array());
    /**
     * Returns the bound environment for this template.
     *
     * @return Twig_Environment The current environment
     */
    public function getEnvironment();
}

}

namespace
{

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Default base class for compiled templates.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Twig_Template implements Twig_TemplateInterface
{
    protected static $cache = array();
    protected $parent;
    protected $parents;
    protected $env;
    protected $blocks;
    protected $traits;
    /**
     * Constructor.
     *
     * @param Twig_Environment $env A Twig_Environment instance
     */
    public function __construct(Twig_Environment $env)
    {
        $this->env = $env;
        $this->blocks = array();
        $this->traits = array();
    }
    /**
     * Returns the template name.
     *
     * @return string The template name
     */
    abstract public function getTemplateName();
    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->env;
    }
    /**
     * Returns the parent template.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @return Twig_TemplateInterface|false The parent template or false if there is no parent
     */
    public function getParent(array $context)
    {
        if (null !== $this->parent) {
            return $this->parent;
        }
        $parent = $this->doGetParent($context);
        if (false === $parent) {
            return false;
        } elseif ($parent instanceof Twig_Template) {
            $name = $parent->getTemplateName();
            $this->parents[$name] = $parent;
            $parent = $name;
        } elseif (!isset($this->parents[$parent])) {
            $this->parents[$parent] = $this->env->loadTemplate($parent);
        }
        return $this->parents[$parent];
    }
    protected function doGetParent(array $context)
    {
        return false;
    }
    public function isTraitable()
    {
        return true;
    }
    /**
     * Displays a parent block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name    The block name to display from the parent
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     */
    public function displayParentBlock($name, array $context, array $blocks = array())
    {
        $name = (string) $name;
        if (isset($this->traits[$name])) {
            $this->traits[$name][0]->displayBlock($name, $context, $blocks);
        } elseif (false !== $parent = $this->getParent($context)) {
            $parent->displayBlock($name, $context, $blocks);
        } else {
            throw new Twig_Error_Runtime(sprintf('The template has no parent and no traits defining the "%s" block', $name), -1, $this->getTemplateName());
        }
    }
    /**
     * Displays a block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name    The block name to display
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     */
    public function displayBlock($name, array $context, array $blocks = array())
    {
        $name = (string) $name;
        if (isset($blocks[$name])) {
            $b = $blocks;
            unset($b[$name]);
            call_user_func($blocks[$name], $context, $b);
        } elseif (isset($this->blocks[$name])) {
            call_user_func($this->blocks[$name], $context, $blocks);
        } elseif (false !== $parent = $this->getParent($context)) {
            $parent->displayBlock($name, $context, array_merge($this->blocks, $blocks));
        }
    }
    /**
     * Renders a parent block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name    The block name to render from the parent
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     *
     * @return string The rendered block
     */
    public function renderParentBlock($name, array $context, array $blocks = array())
    {
        ob_start();
        $this->displayParentBlock($name, $context, $blocks);
        return ob_get_clean();
    }
    /**
     * Renders a block.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @param string $name    The block name to render
     * @param array  $context The context
     * @param array  $blocks  The current set of blocks
     *
     * @return string The rendered block
     */
    public function renderBlock($name, array $context, array $blocks = array())
    {
        ob_start();
        $this->displayBlock($name, $context, $blocks);
        return ob_get_clean();
    }
    /**
     * Returns whether a block exists or not.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * This method does only return blocks defined in the current template
     * or defined in "used" traits.
     *
     * It does not return blocks from parent templates as the parent
     * template name can be dynamic, which is only known based on the
     * current context.
     *
     * @param string $name The block name
     *
     * @return Boolean true if the block exists, false otherwise
     */
    public function hasBlock($name)
    {
        return isset($this->blocks[(string) $name]);
    }
    /**
     * Returns all block names.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @return array An array of block names
     *
     * @see hasBlock
     */
    public function getBlockNames()
    {
        return array_keys($this->blocks);
    }
    /**
     * Returns all blocks.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * @return array An array of blocks
     *
     * @see hasBlock
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
    /**
     * {@inheritdoc}
     */
    public function display(array $context, array $blocks = array())
    {
        $this->displayWithErrorHandling($this->env->mergeGlobals($context), $blocks);
    }
    /**
     * {@inheritdoc}
     */
    public function render(array $context)
    {
        $level = ob_get_level();
        ob_start();
        try {
            $this->display($context);
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }
        return ob_get_clean();
    }
    protected function displayWithErrorHandling(array $context, array $blocks = array())
    {
        try {
            $this->doDisplay($context, $blocks);
        } catch (Twig_Error $e) {
            if (!$e->getTemplateFile()) {
                $e->setTemplateFile($this->getTemplateName());
            }
            // this is mostly useful for Twig_Error_Loader exceptions
            // see Twig_Error_Loader
            if (false === $e->getTemplateLine()) {
                $e->setTemplateLine(-1);
                $e->guess();
            }
            throw $e;
        } catch (Exception $e) {
            throw new Twig_Error_Runtime(sprintf('An exception has been thrown during the rendering of a template ("%s").', $e->getMessage()), -1, null, $e);
        }
    }
    /**
     * Auto-generated method to display the template with the given context.
     *
     * @param array $context An array of parameters to pass to the template
     * @param array $blocks  An array of blocks to pass to the template
     */
    abstract protected function doDisplay(array $context, array $blocks = array());
    /**
     * Returns a variable from the context.
     *
     * This method is for internal use only and should never be called
     * directly.
     *
     * This method should not be overridden in a sub-class as this is an
     * implementation detail that has been introduced to optimize variable
     * access for versions of PHP before 5.4. This is not a way to override
     * the way to get a variable value.
     *
     * @param array   $context           The context
     * @param string  $item              The variable to return from the context
     * @param Boolean $ignoreStrictCheck Whether to ignore the strict variable check or not
     *
     * @return The content of the context variable
     *
     * @throws Twig_Error_Runtime if the variable does not exist and Twig is running in strict mode
     */
    final protected function getContext($context, $item, $ignoreStrictCheck = false)
    {
        if (!array_key_exists($item, $context)) {
            if ($ignoreStrictCheck || !$this->env->isStrictVariables()) {
                return null;
            }
            throw new Twig_Error_Runtime(sprintf('Variable "%s" does not exist', $item), -1, $this->getTemplateName());
        }
        return $context[$item];
    }
    /**
     * Returns the attribute value for a given array/object.
     *
     * @param mixed   $object            The object or array from where to get the item
     * @param mixed   $item              The item to get from the array or object
     * @param array   $arguments         An array of arguments to pass if the item is an object method
     * @param string  $type              The type of attribute (@see Twig_TemplateInterface)
     * @param Boolean $isDefinedTest     Whether this is only a defined check
     * @param Boolean $ignoreStrictCheck Whether to ignore the strict attribute check or not
     *
     * @return mixed The attribute value, or a Boolean when $isDefinedTest is true, or null when the attribute is not set and $ignoreStrictCheck is true
     *
     * @throws Twig_Error_Runtime if the attribute does not exist and Twig is running in strict mode and $isDefinedTest is false
     */
    protected function getAttribute($object, $item, array $arguments = array(), $type = Twig_TemplateInterface::ANY_CALL, $isDefinedTest = false, $ignoreStrictCheck = false)
    {
        $item = ctype_digit((string) $item) ? (int) $item : (string) $item;
        // array
        if (Twig_TemplateInterface::METHOD_CALL !== $type) {
            if ((is_array($object) && array_key_exists($item, $object))
                || ($object instanceof ArrayAccess && isset($object[$item]))
            ) {
                if ($isDefinedTest) {
                    return true;
                }
                return $object[$item];
            }
            if (Twig_TemplateInterface::ARRAY_CALL === $type) {
                if ($isDefinedTest) {
                    return false;
                }
                if ($ignoreStrictCheck || !$this->env->isStrictVariables()) {
                    return null;
                }
                if (is_object($object)) {
                    throw new Twig_Error_Runtime(sprintf('Key "%s" in object (with ArrayAccess) of type "%s" does not exist', $item, get_class($object)), -1, $this->getTemplateName());
                } elseif (is_array($object)) {
                    throw new Twig_Error_Runtime(sprintf('Key "%s" for array with keys "%s" does not exist', $item, implode(', ', array_keys($object))), -1, $this->getTemplateName());
                } else {
                    throw new Twig_Error_Runtime(sprintf('Impossible to access a key ("%s") on a "%s" variable', $item, gettype($object)), -1, $this->getTemplateName());
                }
            }
        }
        if (!is_object($object)) {
            if ($isDefinedTest) {
                return false;
            }
            if ($ignoreStrictCheck || !$this->env->isStrictVariables()) {
                return null;
            }
            throw new Twig_Error_Runtime(sprintf('Item "%s" for "%s" does not exist', $item, is_array($object) ? 'Array' : $object), -1, $this->getTemplateName());
        }
        $class = get_class($object);
        // object property
        if (Twig_TemplateInterface::METHOD_CALL !== $type) {
            if (isset($object->$item) || array_key_exists($item, $object)) {
                if ($isDefinedTest) {
                    return true;
                }
                if ($this->env->hasExtension('sandbox')) {
                    $this->env->getExtension('sandbox')->checkPropertyAllowed($object, $item);
                }
                return $object->$item;
            }
        }
        // object method
        if (!isset(self::$cache[$class]['methods'])) {
            self::$cache[$class]['methods'] = array_change_key_case(array_flip(get_class_methods($object)));
        }
        $lcItem = strtolower($item);
        if (isset(self::$cache[$class]['methods'][$lcItem])) {
            $method = $item;
        } elseif (isset(self::$cache[$class]['methods']['get'.$lcItem])) {
            $method = 'get'.$item;
        } elseif (isset(self::$cache[$class]['methods']['is'.$lcItem])) {
            $method = 'is'.$item;
        } elseif (isset(self::$cache[$class]['methods']['__call'])) {
            $method = $item;
        } else {
            if ($isDefinedTest) {
                return false;
            }
            if ($ignoreStrictCheck || !$this->env->isStrictVariables()) {
                return null;
            }
            throw new Twig_Error_Runtime(sprintf('Method "%s" for object "%s" does not exist', $item, get_class($object)), -1, $this->getTemplateName());
        }
        if ($isDefinedTest) {
            return true;
        }
        if ($this->env->hasExtension('sandbox')) {
            $this->env->getExtension('sandbox')->checkMethodAllowed($object, $method);
        }
        $ret = call_user_func_array(array($object, $method), $arguments);
        // useful when calling a template method from a template
        // this is not supported but unfortunately heavily used in the Symfony profiler
        if ($object instanceof Twig_TemplateInterface) {
            return $ret === '' ? '' : new Twig_Markup($ret, $this->env->getCharset());
        }
        return $ret;
    }
    /**
     * This method is only useful when testing Twig. Do not use it.
     */
    public static function clearCache()
    {
        self::$cache = array();
    }
}

}
 




namespace Orb\Helper
{

use \Orb\Util\Util;


class HelperManager
{
	
	protected $_helpers = array();

	
	protected $_callable_names = array();



	
	public function addHelper($object, $name = null, $prefix_callable = false)
	{
		if (!$name) {
			$name = Util::getBaseClassname($object);
		}

		$name = strtolower($name);

		if (isset($this->_helpers[$name])) {
			throw new \InvalidArgumentException("`$name` has already been registered.");
		}

		if (method_exists($object, '__invoke')) {
			$this->_callable_names[str_replace('_', '', $name)] = array($object, '__invoke');
		}

		if ($object instanceof ShortCallableInterface) {
			foreach ($object->getShortCallableNames() as $short_name => $method) {
				if ($prefix_callable) {
					$short_name = $prefix_callable . $short_name;
				}

				$short_name = strtolower($short_name);

				$this->_callable_names[$short_name] = array($object, $method);
			}
		}

		$this->_helpers[$name] = $object;
	}



	
	public function findHelperOfType($typename, $exact = false)
	{
		if ($exact) {
			foreach ($this->_helpers as $name => $object) {
				if (get_class($object) == $typename) {
					return $name;
				}
			}
		} else {
			foreach ($this->_helpers as $object) {
				if (is_a($object, $typename)) {
					return $name;
				}
			}
		}

		return false;
	}



	
	public function hasHelper($name)
	{
		$name = strtolower($name);
		return isset($this->_helpers[$name]);
	}



	
	public function getHelper($name)
	{
		$name = strtolower($name);
		if (!isset($this->_helpers[$name])) {
			throw new \OutOfBoundsException("No helper `$name` is registered");
		}

		return $this->_helpers[$name];
	}



	
	public function removeHelper($name)
	{
		$name = strtolower($name);
		if (!isset($this->_helpers[$name])) {
			throw new \OutOfBoundsException("No helper `$name` is registered");
		}

		unset($this->_helpers[$name]);
	}



	
	public function isNameCallable($name)
	{
		if (isset($this->_callable_names[$name])) {
			return true;
		}

		return false;
	}



	
	public function callName($name, array $args)
	{
		return call_user_func_array($this->_callable_names[$name], $args);
	}
}
}
 




namespace Orb\Helper
{

interface ShortCallableInterface
{
	
	public function getShortCallableNames();
}
}
 




namespace Orb\Input\Cleaner
{

use Orb\Input\Cleaner\CleanerPlugin\CleanerPlugin;


class Cleaner
{
	
	protected $cleaner_type_map = array();

	
	protected $cleaners = array();

	public function __construct()
	{
		$basic = new \Orb\Input\Cleaner\CleanerPlugin\Basic();
		$basic->enableUtfHandling();

		$this->addCleaner($basic);
	}


	
	public function addCleaner(CleanerPlugin $cleaner)
	{
		$this->cleaners[$cleaner->getCleanerId()] = $cleaner;

		foreach ($cleaner->getCleanerTypes() as $t) {
			$this->cleaner_type_map[$t] = $cleaner->getCleanerId();
		}
	}


	
	public function getCleaner($id)
	{
		return $this->cleaners[$id];
	}


	
	public function hasCleaner($id)
	{
		return isset($this->cleaners[$id]);
	}


	
	public function supportsType($type)
	{
		return isset($this->cleaner_type_map[$type]);
	}


	
	public function getCleanerForType($type)
	{
		if (!isset($this->cleaner_type_map[$type])) {
			return null;
		}

		$id = $this->cleaner_type_map[$type];
		return $this->getCleaner($id);
	}


	
	public function clean($value, $type = 'raw', $options = null)
	{
		if (!$options) $options = array();

		if (!isset($this->cleaner_type_map[$type])) {
			throw new \InvalidArgumentException("Invalid cleaner type `$type`");
		}

		return $this->getCleanerForType($type)->cleanValue($value, $type, $options, $this);
	}



	
	public function cleanArray($array, $type_val = 'raw', $type_key = 'raw', $options_val = null, $options_key = null)
	{
	    if (!is_array($array)) {
	        $array = (array)$array;
	    }

	    $ret_array = array();

		foreach ($array as $k => $v) {
			$k = $this->clean($k, $type_key, $options_key);
			$v = $this->clean($v, $type_val, $options_val);

			if ($type_key == 'discard') {
				$ret_array[] = $v;
			} else {
				$ret_array[$k] = $v;
			}
		}

		return $ret_array;
	}
}
}
 




namespace Orb\Input\Reader
{

use Orb\Input\Reader\Source\SourceInterface;
use Orb\Input\Cleaner\Cleaner;
use Orb\Util\Strings;


class Reader
{
	
	protected $sources = array();

	
	protected $source_aliases = array();

	
	protected $default_source_name = null;

	
	protected $call_cache = array();

	
	protected $cleaner;

	
	protected $array_name_sep = null;



	
	public function __construct(Cleaner $cleaner = null)
	{
		if (!$cleaner) {
			$cleaner = new Cleaner();
		}

		$this->cleaner = $cleaner;
	}



	
	public function getValue($name, $source_name = null)
	{
		$source = $this->getSource($source_name);

		if ($this->array_name_sep !== null AND is_string($name) AND Strings::isIn($this->array_name_sep, $name)) {
			$name = explode($this->array_name_sep, $name);
		}

		return $source->getValue($name);
	}



	
	public function getArrayValue($name, $source_name = null)
	{
		$value = $this->getValue($name, $source_name);

		if (!is_array($value)) {
	        $value = (array)$value;
	    }

		return $value;
	}



	
	public function getCleanValue($name, $clean_type = 'raw', $source_name = null, $clean_options = null)
	{
		$value = $this->getValue($name, $source_name);

		return $this->cleaner->clean($value, $clean_type, $clean_options);
	}



	
	public function getCleanValueArray($name, $clean_val_type = 'raw', $clean_key_type = 'raw', $source_name = null, $clean_val_options = null, $clean_key_options = null)
	{
		$value = $this->getValue($name, $source_name);

		return $this->cleaner->cleanArray($value, $clean_val_type, $clean_key_type, $clean_val_options, $clean_key_options);
	}



	
	public function checkIsset($name, $source_name = null)
	{
		$source = $this->getSource($source_name);

		if ($this->array_name_sep !== null AND is_string($name) AND Strings::isIn($this->array_name_sep, $name)) {
			$name = explode($this->array_name_sep, $name);
		}

		return $source->checkIsset($name);
	}



	
	public function addSource($name, SourceInterface $source)
	{
		if (is_array($name)) {
			$names = $name;
			$name = $names[0];
		} else {
			$names = array($name);
		}

		$this->sources[$name] = $source;

		foreach ($names as $n) {
			$this->source_aliases[$n] = $this->sources[$name];
		}

		if (!$this->default_source_name) {
			$this->default_source_name = $name;
		}

		return $this;
	}



	
	public function hasSource($name)
	{
		return isset($this->source_aliases);
	}



	
	public function setDefaultSourceName($name)
	{
		if (!isset($this->sources[$name])) {
			throw new Exception('Source has not been added to this reader: '.$name);
		}

		$this->default_source_name = $name;
	}



	
	public function getDefaultSourceName()
	{
		return $this->default_source_name;
	}



	
	public function getSource($name = null)
	{
		if ($name === null) $name = $this->default_source_name;

		if (!isset($this->source_aliases[$name])) {
			throw new \Exception('Unknown source: ' . $name);
		}

		return $this->source_aliases[$name];
	}



	
	public function __call($method_name, $method_args)
	{
		$match = null;

		$name = null;
		$type = null;
		$from = null;
		$options = null;

										
		if (isset($this->call_cache[$method_name])) {
			$call_info = $this->call_cache[$method_name];

						if ($call_info['call_type'] == 'getTypeFromSource') {
				$name = $method_args[0];
				$type = $call_info['type'];
				$from = $call_info['from'];
				if (isset($method_args[1])) {
					$options = $method_args[1];
				}

						} else {
				$name = $method_args[0];
				$type = $call_info['type'];
				if (isset($method_args[1])) {
					$from = $method_args[1];
				}
				if (isset($method_args[2])) {
					$options = $method_args[2];
				}
			}

						
		} else {

									
			if (preg_match('#^get(.*?)From(.*?)$#', $method_name, $match)) {

				$name = $method_args[0];

				$type = $match[1];
				$type = Strings::camelCaseToDash($type);
				$type = str_replace('-', '_', $type);

				$from = strtolower($match[2]);

				if (isset($method_args[1])) {
					$options = $method_args[1];
				}

				$this->call_cache[$method_name] = array(
					'call_type' => 'getTypeFromSource',
					'type' => $type,
					'from' => $from
				);


									
			} elseif (preg_match('#^get(.*?)$#', $method_name, $match)) {

				$name = $method_args[0];

				$type = $match[1];
				$type = Strings::camelCaseToDash($type);
				$type = str_replace('-', '_', $type);

				if (isset($method_args[1])) {
					$from = $method_args[1];
				}

				if (isset($method_args[2])) {
					$options = $method_args[2];
				}

				$this->call_cache[$method_name] = array(
					'call_type' => 'getType',
					'type' => $type
				);

									
			} else {
				throw new \BadMethodCallException("Unknown method $method_name");
			}
		}

		return $this->getCleanValue($name, $type, $from, $options);
	}



	
	public function getCleaner()
	{
		return $this->cleaner;
	}



	
	public function setArrayStringSeparator($sep = '.')
	{
		$this->array_name_sep = $sep;
	}
}
}
 




namespace Orb\Input\Reader\Source
{


class ArrayVal implements SourceInterface
{
	
	protected $array;



	
	public function __construct($array)
	{
		$this->array = $array;
	}



	
	public function getValue($name, $options = null)
	{
		$parts = array();
		if (is_array($name)) {
			$parts = $name;
			$name = array_shift($parts);
		}

		if (isset($this->array[$name])) {
			$value = $this->array[$name];
		} else {
			$value = null;
		}

		if ($parts) {
			foreach ($parts as $part) {

				if (!is_array($value) OR !isset($value[$part])) {
					$value = null;
					break;
				}

				$value = $value[$part];
			}
		}

		return $value;
	}



	
	public function checkIsset($name, $options = null)
	{
		return ($this->getValue($name, $options) === null ? false : true);
	}



	
	public function getArray()
	{
		return $this->array;
	}



	
	public function setArray($array)
	{
		$this->array = $array;
	}
}
}
 




namespace Orb\Input\Reader\Source
{


interface SourceInterface
{
	
	public function getValue($name);



	
	public function checkIsset($name);
}
}
 




namespace Orb\Input\Reader\Source
{


class Superglobal implements SourceInterface
{
	
	protected $superglobal;

	
	protected $array = null;

	
	public function __construct($sg_name)
	{
		$this->superglobal = $sg_name;
	}



	
	public function getValue($name, $options = null)
	{
		$this->_initArray();

		$parts = array();
		if (is_array($name)) {
			$parts = $name;
			$name = array_shift($parts);
		}

		if (isset($this->array[$name])) {
			$value = $this->array[$name];
		} else {
			return null;
		}

		if ($parts) {
			foreach ($parts as $part) {

				if (!is_array($value) OR !isset($value[$part])) {
					$value = null;
					break;
				}

				$value = $value[$part];
			}
		}

		return $value;
	}

	protected function _initArray()
	{
		if ($this->array !== null) return; 
				if ($this->superglobal == '_REQUEST') {
			$this->array = \array_merge($_GET, $_POST);
		} else {
			$this->array = $GLOBALS[$this->superglobal];
		}
		if (!$this->array) $this->array = array();
	}



	
	public function checkIsset($name, $options = null)
	{
		return ($this->getValue($name, $options) === null ? false : true);
	}



	
	public function getSuperglobalName()
	{
		return $this->superglobal;
	}
}
}
 




namespace Orb\Templating\Engine
{

use \Symfony\Component\Templating\Storage\Storage;
use \Symfony\Component\Templating\Storage\FileStorage;
use \Symfony\Component\Templating\Storage\StringStorage;
use \Symfony\Component\Templating\Helper\HelperInterface;
use \Symfony\Component\Templating\Loader\LoaderInterface;


class PhpVarEngine extends \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine
{
	public function evaluate(Storage $template, array $parameters = array())
	{
		$OUTPUT = $this->_preProcess($template, $parameters);
		$__template__ = $template;

		extract($parameters, EXTR_SKIP);
		$view = $this;

		ob_start();

		if ($__template__ instanceof FileStorage) {
			extract($parameters);
			$view = $this;
			require $__template__;
		} elseif ($__template__ instanceof StringStorage) {
			eval('; ?>'.$__template__.'<?php ;');
		}

		ob_end_clean();

		if (!isset($OUTPUT)) {
			$OUTPUT = '';
		}

		return $this->_postProcess($OUTPUT);
	}

	protected function _preProcess(Storage $template, array $parameters = array())
	{
		return '';
	}

	protected function _postProcess($OUTPUT)
	{
		if (is_array($OUTPUT)) {
			$OUTPUT = implode('', $OUTPUT);
		}

		return (string)$OUTPUT;
	}

	public function supports($name)
	{
		return false !== strpos($name, '.phpv');
	}
}
}
 




namespace Orb\Templating\Engine
{

use \Symfony\Component\Templating\Storage\Storage;



class PhpVarJsonEngine extends PhpVarEngine
{
	protected function _preProcess(Storage $template, array $parameters = array())
	{
		return array();
	}

	protected function _postProcess($OUTPUT)
	{
		if (!is_array($OUTPUT)) {
			$OUTPUT = array((string)$OUTPUT);
		}

		return json_encode($OUTPUT);
	}

	public function supports($name)
	{
		return false !== strpos($name, '.jsonphp');
	}
}
}
 




namespace Orb\Util
{


class Arrays
{
	private function __construct() {  }

	
	const FUNC_ARR_VAL = '___ORB_ARR_VALUE___';

	
	const REDUCE_IGNORE_UNSET = '___ORB_IGNORE_UNSET___';

	
	const ARR_KEY_NOT_SET = '___ORB_ARR_KEY_NOT_SET___';

	
	const LOWERKEY_DUPE_OVERWRITE = 1;

	
	const LOWERKEY_DUPE_ADD_ARRAY = 2;



	
	public static function flatten($array)
	{
		if (!is_array($array)) {
			return (array)$array;
		}

		$new_array = array();

		foreach ($array as $k => $v) {
			if (!is_array($v)) {
			    if (is_int($k)) {
			        $new_array[] = $v;
			    } else {
			        $new_array[$k] = $v;
			    }
			} else {
			    $v = self::flatten($v);
				$new_array = array_merge($new_array, $v);
			}
		}

		return $new_array;
	}


	
	public static function flattenWithKeys($array, $sep = '.')
	{
		return self::_flattenWithKeys($array, $sep);
	}

	protected static function _flattenWithKeys($array, $sep = '.', array $key_parts = array())
	{
		$new_array = array();

		if ($key_parts) {
			$key_prefix = implode('.', $key_parts) . '.';
		} else {
			$key_prefix = '';
		}

		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$key_parts[] = $k;
				$v = self::_flattenWithKeys($v, $sep, $key_parts);
				$new_array = array_merge($new_array, $v);
				array_pop($key_parts);
			} else {
				$k = $key_prefix . $k;
				$new_array[$k] = $v;
			}
		}

		return $new_array;
	}



	
	public static function func($array, $func, $params = array(), $run_on_keys = false)
	{
		if (!is_array($array)) {
			return self::_func_run_func($func, $params, $array);
		}

		foreach ($array as $k => $v) {
			if ($run_on_keys) {
				$k = self::_func_run_func($func, $params, $v);
			}
			$array[$k] = Arrays::func($v, $func, $params);
		}

		return $array;
	}

	protected static function _func_run_func($func, $params, $val)
	{
		$key = array_search(self::FUNC_ARR_VAL, $params, true);

		if ($key === false) {
			if (array_key_exists(0, $params)) {
				array_unshift($params, '');
			}

			$key = 0;
		}

		$params[$key] = $val;

		return call_user_func_array($func, $params);
	}



	
	public static function mergeAssoc()
	{
		$new_array = (array)func_get_arg(0);

		for ($i = 1, $size = func_num_args(); $i < $size; $i++) {

			$arr = (array)func_get_arg($i);

			foreach ($arr as $key => $val) {
				$new_array[$key] = $val;
			}
		}

		return $new_array;
	}



	
	public static function mergeDeep()
	{
		$args = func_get_args();

		if (!$args) {
			return array();
		}
		if (sizeof($args) == 1) {
			return $args[0];
		}

		$array = array_shift($args);

		while (($other_array = array_shift($args)) !== null) {
			$array = self::_mergeDeepHelper($array, $other_array);
		}

		return $array;
	}

	
	protected static function _mergeDeepHelper(array $array1, $array2 = null)
	{
		if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key]))
                                  ? self::_mergeDeepHelper($array1[$key], $array2[$key])
                                  : $array2[$key];
                } else {
                    $array1[$key] = $val;
                }
            }
        }

        return $array1;
	}



	
	public static function uniqueDeep(array $array, $sort_flags = SORT_STRING)
	{
		$args = func_get_args();

		if (!$args) {
			return array();
		}
		if (sizeof($args) == 1) {
			return $args[0];
		}

		return self::_uniqueDeep($array, $sort_flags);
	}

	protected static function _uniqueDeep(array $array, $sort_flags)
	{
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$array[$k] = array_unique(self::_uniqueDeep($v, $sort_flags), $sort_flags);
			} else {
				$array[$k] = $v;
			}
		}

		$array = array_unique($array);
	}



	
	public static function unshiftAssoc(&$array, $key, $value = false)
	{
		if (!is_array($array)) {
			$array = (array)$array;
		}

		$old_array = $array;
		$array = array($key => $value);

				if (isset($old_array[$key])) {
			unset($old_array[$key]);
		}

		foreach ($old_array as $k => $v) {
			$array[$k] = $v;
		}

		return sizeof($array);
	}



	
	public static function unshiftAssocReturn($array, $key, $value)
	{
		self::unshiftAssoc($array, $key, $value);

		return $array;
	}



	
	public static function removeFalsey($array)
	{
		if (!is_array($array)) {
			$array = (array)$array;
		}

		foreach (array_keys($array) as $k) {
			if (!$array[$k]) {
				unset($array[$k]);
			}
		}

		return $array;
	}


	
	public static function removeButKey(array $array, $keys, $recursive = false, $ignore_numeric = false)
	{
		$new = array();

		if (!is_array($keys)) {
			$keys = array($keys);
		}

		$keys = array_combine($keys, $keys);

		foreach ($array as $k => $v) {
			if (isset($keys[$k]) || ($ignore_numeric && is_numeric($k))) {
				if ($recursive && is_array($v)) {
					$v = self::removeButKey($v, $keys, true, $ignore_numeric);
					if ($v) {
						$new[$k] = $v;
					}
				} else {
					$new[$k] = $v;
				}
			}
		}

		return $new;
	}


	
	public static function multiRenameKey(array $array, $old_key, $new_key, $max_depth = -1, $_cur_depth = 0)
	{
		$new = array();

		foreach ($array as $k => $v) {
			if ($k == $old_key) {
				$k = $new_key;
			}

			if (is_array($v) && $max_depth == -1 || $_cur_depth < $max_depth) {
				$v = self::multiRenameKey($v, $old_key, $new_key, $max_depth, $_cur_depth+1);
			}

			$new[$k] = $v;
		}

		return $new;
	}


	
	public static function assocToNumericArary(array $array, $recursive_key = false)
	{
		$new = array();

		foreach ($array as $v) {
			if ($recursive_key && isset($v[$recursive_key]) && is_array($v[$recursive_key])) {
				$v[$recursive_key] = self::assocToNumericArary($v[$recursive_key], $recursive_key);
			}

			$new[] = $v;
		}

		return $new;
	}


	
	public static function removeEmptyString($array)
	{
	    if (!is_array($array)) {
	        $array = (array)$array;
	    }

	    foreach (array_keys($array) as $k) {
	        if (is_string($array[$k]) AND trim($array[$k]) === '') {
	            unset($array[$k]);
	        }
	    }

	    return $array;
	}


	
	public static function removeEmptyArray($array)
	{
	    if (!is_array($array)) {
	        $array = (array)$array;
	    }

	    foreach (array_keys($array) as $k) {
	        if (is_array($array[$k]) AND !$array[$k]) {
	            unset($array[$k]);
	        }
	    }

	    return $array;
	}



	
	public static function keyFromData($array, $key_index = 0, $val_index = false)
	{
		$new_array = array();

		if (!$val_index) {
			foreach ($array as $sub_array) {
				$new_array[$sub_array[$key_index]] = $sub_array;
			}
		} else {
			foreach ($array as $sub_array) {
				$new_array[$sub_array[$key_index]] = $sub_array[$val_index];
			}
		}

		return $new_array;
	}



	
	public static function pushUnique(&$array, $val)
	{
		$num = func_num_args();

		for ($i = 1; $i < $num; $i++) {
			$val = func_get_arg($i);

			if (!in_array($val, $array)) {
				array_push($array, $val);
			}
		}

		return sizeof($array);
	}



	
	public static function pushUniqueStrict(&$array, $val)
	{
		$num = func_num_args();

		for ($i = 1; $i < $num; $i++) {
			$val = func_get_arg($i);

			if (!in_array($val, $array, true)) {
				array_push($array, $val);
			}
		}

		return sizeof($array);
	}



	
	public static function keyAsPath($array, $path, $path_sep = '/', $default = null)
	{
		if (!$path_sep) {
			return null;
		}

				if (strpos($path, $path_sep) === false) {
			return isset($array[$path]) ? $array[$path] : $default;
		}

				if (Strings::startsWith($path_sep, $path)) {
			$path = substr($path, 1);
		}

		if (Strings::endsWith($path_sep, $path)) {
			$path = substr($path, 0, strlen($path) - 1);
		}


		$parts = explode($path_sep, $path);

		if (!$parts) {
			return $default;
		}

		while (($key = array_shift($parts)) !== null) {
			if (!isset($array[$key])) {
				return $default;
			}

			$array = $array[$key];
		}

		return $array;
	}



	
	public static function getValue($array, $key, $default = null)
	{
		return self::keyAsPath($array, $key, '.', $default);
	}



	
	public static function implodeTemplate($array, $tpl = '<li>{VAL}</li>')
	{
	    if (!is_array($array)) {
	        $array = (array)$array;
	    }


	    $string = '';

	    foreach ($array as $k => $v) {
	        $string .= str_replace(array('{KEY}', '{VAL}'), array($k, $v), $tpl);
	    }

	    return $string;
	}



	
	public static function intoHierarchy($array, $top_id = 0, $parent_key = 'parent_id', $child_key = 'children', &$store_ids = null)
	{
		$store_ids = array();
		return self::_intoHierarchy($array, $top_id, $parent_key, $child_key, $store_ids);
	}

			protected static function _intoHierarchy(&$array, $top_id = 0, $parent_key, $child_key, &$store_ids = null)
	{
		$new_array = array();

		foreach (array_keys($array) as $id) {

			if (!isset($array[$id]) OR $array[$id][$parent_key] != $top_id) {
				continue;
			}

			$store_ids[] = $id;

			$new_array[$id] = $array[$id];

			unset($array[$id]);
			$new_array[$id][$child_key] = Arrays::intoHierarchy($array, $id, $parent_key, $child_key, $store_ids);
		}

		return $new_array;
	}



	
	public static function flattenHierarchy(array $array, $index_key = 'id', $child_key = 'children', $depth_key = 'depth')
	{
	    $new_array = array();

	    self::_flattenHierarcy($new_array, $array, $index_key, $child_key, $depth_key, 0);

	    return $new_array;
	}

	protected static function _flattenHierarcy(array &$new_array, $array, $index_key, $child_key, $depth_key, $current_depth = 0, &$count = 0)
	{
	    foreach ($array as $arr) {
	        if ($index_key !== null) {
	            $index = $arr[$index_key];
	        } else {
	            $index = $count;
	        }

	        $count++;

	        $new_array[$index] = $arr;
	        $new_array[$index]['depth'] = $current_depth;

	        if (isset($arr[$child_key]) AND $arr[$child_key]) {
				$sub_array = $arr[$child_key];
				if (!is_array($sub_array)) {
					$sub_array = iterator_to_array($sub_array);
				}
	            self::_flattenHierarcy($new_array, $sub_array, $index_key, $child_key, $depth_key, $current_depth+1, $count);
	        }
	    }
	}



	
	public static function selectArrayFromHierarchy($array, $index_key = 'id', $title_key = 'title', $indent = '--')
	{
		if (!is_array($array)) {
			$deps = iterator_to_array($array);
		}
		$flat = self::flattenHierarchy($array);

		$options = array();
		foreach ($flat as $i) {
			$indent = '';
			if (!empty($i['depth']) AND $i['depth'] > 0) {
				$indent = str_repeat($indent, $i['depth']) . ' ';
			}
			$options[$i[$index_key]] = $indent . $i[$title_key];
		}

		return $options;
	}



	
	public static function reduceToKeys(array $array, array $keys, $default = self::REDUCE_IGNORE_UNSET)
	{
	    $ret = array();

	    foreach ($keys as $k) {
	        if (isset($array[$k])) {
	            $ret[$k] = $array[$k];
	        } elseif ($default != self::REDUCE_IGNORE_UNSET) {
	            $ret[$k] = $default;
	        }
	    }

	    return $ret;
	}



	
	public static function reduceToKeysMulti(array $mutli_array, array $keys, $default = self::REDUCE_IGNORE_UNSET)
	{
		$ret = array();
		foreach ($mutli_array as $k => $v) {
			$ret[$k] = self::reduceToKeys($v, $keys, $default);
		}

		return $ret;
	}



	
	public static function flattenToIndex($array, $index = 0, $ignore_keys = false)
	{
	    $ret = array();

		if ($ignore_keys) {
			foreach ($array as $sub_array) {
				if (isset($sub_array[$index])) {
					$ret[] = $sub_array[$index];
				}
			}
		} else {
			foreach ($array as $k => $sub_array) {
				if (isset($sub_array[$index])) {
					$ret[$k] = $sub_array[$index];
				}
			}
		}

	    return $ret;
	}



	
	public static function castToType(array $array, $val_type = 'string', $key_type = null)
	{
	    $ret = array();

	    foreach ($array as $k => $v) {
	        if ($key_type !== null && $key_type != 'discard') {
	            settype($k, $key_type);
	        }

	        if ($val_type !== null) {
	            settype($v, $val_type);
	        }

			if ($key_type === 'discard') {
				$ret[] = $v;
			} else {
				$ret[$k] = $v;
			}
	    }

	    return $ret;
	}


	
	public static function castToTypeDeep(array $array, $val_type = 'string', $key_type = null)
	{
	    $ret = array();

	    foreach ($array as $k => $v) {
	        if ($key_type !== null) {
	            settype($k, $key_type);
	        }

	        if ($val_type !== null) {
				if (is_array($v)) {
					$v = self::castToTypeDeep($v, $val_type, $key_type);
				} else {
					settype($v, $val_type);
				}
	        }

	        $ret[$k] = $v;
	    }

	    return $ret;
	}



	
	public static function getNthKey($array, $num = 0)
	{
		if (sizeof($array) < $num) {
			return null;
		}

		reset($array);
		for ($i = 0; $i < $num; $i++) {
			next($array);
		}
		$k = key($array);

		return $k;
	}



	
	public static function getNthItem($array, $num = 0)
	{
		$k = self::getNthKey($array, $num);

		if ($k === null) return null;

		return $array[$k];
	}



	
	public static function getFirstKey($array)
	{
		return self::getNthKey($array, 0);
	}



	
	public static function getLastKey($array)
	{
		return self::getNthKey($array, 0);
	}



	
	public static function getFirstItem($array)
	{
		return self::getNthItem($array, 0);
	}



	
	public static function getLastItem($array)
	{
		return self::getNthItem($array, sizeof($array)-1);
	}



	
	public static function isIn($items, $array, $all = false, $strict = false)
	{
		if (!$array) {
			return false;
		}

		$items = (array)$items;

		foreach ($items as $val) {
			if (in_array($val, $array, $strict)) {
				if (!$all) return true;
			} else {
				if ($all) return false;
			}
		}

		if ($all) {
			return true;
		}

		return false;
	}



	
	public static function isKeyIn($keys, $array, $all = false)
	{
		if (!$array) {
			return false;
		}

		$keys = (array)$keys;

		foreach ($keys as $k) {
			if (isset($array[$k])) {
				if (!$all) return true;
			} else {
				if ($all) return false;
			}
		}

		if ($all) {
			return true;
		}

		return false;
	}



	
	public static function searchAll($array, $search, $strict = false)
	{
		$found_keys = array();

		if ($strict) {
			foreach ($array as $k => $v) {
				if ($search === $v) {
					$found_keys[] = $k;
				}
			}
		} else {
			foreach ($array as $k => $v) {
				if ($search == $v) {
					$found_keys[] = $k;
				}
			}
		}

		return $found_keys;
	}



	
	public static function lowercaseKeys($array, $dupe_mode = LOWERKEY_DUPE_OVERWRITE)
	{
		foreach ($array as $key => $value) {
			$lower_key = strtowloer($key);
			if ($lower_key == $key) continue; 
			unset($array[$key]);

						if ($dupe_mode == self::LOWERKEY_DUPE_ADD_ARRAY) {
								if (!isset($array[$lower_key])) {
					$array[$lower_key] = $value;
								} elseif (is_array($array[$lower_key])) {
					$array[$lower_key][] = $value;
								} else {
					$array[$lower_key] = array($array[$lower_key], $value);
				}
						} else {
				$array[$lower_key] = $value;
			}
		}

		return $array;
	}



	
	public static function toEqualsLines(array $array)
	{
		$lines = array();

		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $subv) {
					$lines[] = "$k = $subv";
				}
			} else {
				$lines[] = "$k = $v";
			}
		}

		return implode("\n", $lines);
	}



	
	public static function generateHash(array $array, $keys_significant = true)
	{
		return self::_generateHashHelper($array, $keys_significant);
	}

	protected static function _generateHashHelper(array $array, $keys_significant = true)
	{
		$data_str = array();

		if ($keys_significant) {
			ksort($array, SORT_REGULAR);
		} else {
			sort($array, SORT_REGULAR);
		}

		foreach ($array as $k => $v) {
			if ($keys_significant) {
				$data_str[] = $k;
			}

			switch (gettype($v)) {
				case 'array':
					$v = self::_generateHashHelper($v, false);
					break;

				case 'object':
					if (method_exists($v, 'toString')) {
						$v = $v->toString();
					} elseif (method_exists($v, '__toString')) {
						$v = $v->__toString();
					} elseif (method_exists($v, 'toArray')) {
						$v = $v->toArray();
						$v = self::_generateHashHelper($v, false);
					} else {
						$v = serialize($v);
						$v = md5($v);
					}
					break;

				case 'resource':
					$v = 'resource';
					break;
			}

			$data_str[] = $v;
		}

		$data_str = implode('', $data_str);

		return md5($data_str);
	}



	
	public static function unsetKey(array &$array, $keys)
	{
		$keys = (array)$keys;

				if (count($keys) == 1) {
			$keys = array_pop($keys);
			unset($array[$keys]);
			return true;
		}

		$last_key = array_pop($keys);
		$subarray = &$array;

		foreach ($keys as $key) {
			if (!is_array($subarray) OR !isset($subarray[$key])) {
				return false;
			}

			$subarray = &$subarray[$key];
		}

		unset($subarray[$last_key]);

		return true;
	}



	
	public static function flattenCodeArray(array $array, $add_any = false, $prefix = '')
	{
		$new = array();

		foreach ($array as $k => $v) {

						if (!$v) continue;

			$k = $prefix.$k.'_';

			if ($add_any) {
				$new[$k.$add_any] = true;
			}

			foreach ($v as $code) {
				if (is_array($code)) {
					$new = $new + self::flattenCodeArray($code, $add_any, $k);
				} else {
					$new[$k.$code] = true;
				}
			}
		}

		return $new;
	}



	
	public static function removeValue(array $array, $value, $strict = false)
	{
		if (!is_array($value)) $value = array($value);

		foreach ($value as $v) {
			while (($k = array_search($v, $array, $strict)) !== false) {
				unset($array[$k]);
			}
		}

		return $array;
	}


	
	public static function replaceValue(array $value, $find, $replace)
	{
		$new = $value;
		foreach ($new as &$v) {
			if ($v == $find) {
				$v = $replace;
			}
		}

		return $new;
	}


	
	public static function mergeSubArrays(array $array_of_arrays, $levels = 1)
	{
		return self::_mergeSubArray_helper($array_of_arrays, $levels, 1);
	}

	protected static function _mergeSubArray_helper(array $array_of_arrays, $max_level, $cur_level)
	{
		$array = array();

		foreach ($array_of_arrays as $sub_array) {
			if ($cur_level < $max_level AND is_array($sub_array)) {
				$sub_array = self::_mergeSubArray_helper($sub_array, $levels, $cur_level+1);
			}

			$array = array_merge($array, $sub_array);
		}

		return $array;
	}



	
	public static function shuffleAssoc(array &$array)
	{
		if (!$array) return false;

		$old_array = $array;
		$array = array();
		$keys = array_keys($old_array);
		shuffle($keys);

		foreach ($keys as $k) {
			$array[$k] = $old_array[$k];
		}

		return true;
	}



	
	public static function getPageChunk(array $array, $page, $per_page)
	{
				if (!$per_page) return $array;

		$count = count($array);

		$page = max(0, $page);
		$start = ($page - 1) * $per_page;

				if ($start > $count) return array();

		return array_slice($array, $start, $per_page);
	}



	
	public static function groupItems($array, $group_key, $preserve_keys = false, $mutator_callback = null)
	{
		$ret = array();

		foreach ($array as $k => $v) {
			$group = $v[$group_key];

			if ($mutator_callback) {
				$group = $mutator_callback($group);
			}

			if (!isset($ret[$group])) $ret[$group] = array();

			if ($preserve_keys) {
				$ret[$group][$k] = $v;
			} else {
				$ret[$group][] = $v;
			}
		}

		return $ret;
	}



	
	public static function checkAll($array, $callback)
	{
		foreach ($array as $k => $v) {
			if (!call_user_func($callback, $v, $k)) {
				return false;
			}
		}

		return true;
	}



	
	public static function walkKeys($array, $callback)
	{
		$keys = array_keys($array);
		$new_keys = array();

		foreach ($keys as $k) {
			call_user_func($callback, $k, $array[$k]);
			$new_keys[] = $k;
		}

		$values = array_values($array);

		$array = array_combine($new_keys, $values);

		return $array;
	}


	
	public static function spliceAssoc($array, $start, $length = null)
	{
		$new_array = array();

		foreach ($array as $k => $v) {
			if ($start) {
				$start--;
				continue;
			}

			$new_array[$k] = $v;

			if ($length !== null) {
				$length--;
				if ($length == 0) {
					break;
				}
			}
		}

		return $new_array;
	}


	
	public static function sortIntoAlphabeticalIndex($array, $word_index = null, $maintain_keys = false, $empty_letters = false)
	{
		$aindex = array();

		if ($empty_letters) {
			$aindex = array('@' => array(), '#' => array());
			foreach (range('A','Z') as $l) {
				$aindex[$l] = array();
			}
		}

		foreach ($array as $k => $item) {

			if (is_array($word_index)) {
				$label = $item[$word_index];
			} else {
				$label = $item;
			}

			$first = Strings::utf8_substr($label, 0, 1);
			$first = Strings::utf8_accents_to_ascii($first);
			$first = Strings::utf8_strtoupper($first);

			if (is_numeric($first)) {
				$first = '#';
			} elseif (!preg_match('#[A-Z]#', $first)) {
				$first = '@';
			}

			if (!isset($aindex[$first])) {
				$aindex[$first] = array();
			}

			if ($maintain_keys) {
				$aindex[$first][$k] = $item;
			} else {
				$aindex[$first][] = $item;
			}
		}

		ksort($aindex, SORT_STRING);

				if (isset($aindex['@']) AND isset($aindex['#'])) {
			$a = $aindex['@'];
			$b = $aindex['#'];

			unset($aindex['@'], $aindex['#']);

			self::unshiftAssoc($aindex, '#', $b);
			self::unshiftAssoc($aindex, '@', $a);
		}

		return $aindex;
	}


	
	public static function sortMulti(array &$array, $k, $sort_flags = \SORT_REGULAR)
	{
		usort($array, function($a, $b) use ($k, $sort_flags) {
			$a = $a[$k];
			$b = $b[$k];

			if ($sort_flags == \SORT_NUMERIC) {
				$a += 0.0;
				$b += 0.0;
			} elseif ($sort_flags == \SORT_STRING) {
				$a .= '';
				$b .= '';
			}

			if ($a == $b) {
				return 0;
			}

			return ($a < $b) ? -1 : 1;
		});
	}


	
	public static function countMulti(array &$array)
	{
		$count = 0;

		foreach ($array as $sub) {
			$count += count($sub);
		}

		return $count;
	}


	
	public static function valueCount(array $array)
	{
		$count = 0;

		foreach ($array as $sub) {
			if (is_array($sub)) {
				$count += self::valueCount($sub);
			} else {
				++$count;
			}
		}

		return $count;
	}


	
	public static function orderIdArray(array $ordered_ids, array $unordeded_data, $append_remain = false)
	{
		$data = array();

		foreach ($ordered_ids as $id) {
			if (isset($unordeded_data[$id])) {
				$data[$id] = $unordeded_data[$id];
			}
		}

		if ($append_remain && count($ordered_ids) != count($unordeded_data)) {
			$keys = array_keys($unordeded_data);
			$append_keys = array_diff($keys, $ordered_ids);

			if ($append_keys) {
				foreach ($append_keys as $k) {
					$data[$k] = $unordeded_data[$k];
				}
			}
		}

		return $data;
	}


	
	public static function filter(array $array, $fn)
	{
		$new_array = array();

		foreach ($array as $k => $v) {
			if ($fn($v, $k) !== false) {
				$new_array[$k] = $v;
			}
		}

		return $new_array;
	}
}
}
 




namespace Orb\Util
{


interface CapabilityInformerInterface
{
	
	public function getCapabilities();


	
	public function isCapable($capability);
}
}
 




namespace Orb\Util
{


class ChainCaller
{
	protected $_objects = array();

	
	public function addObject($object)
	{
		$this->_objects[] = $object;
	}

	
	
	
	public function getObjects()
	{
		return $this->_objects;
	}

	

	
	public function __call($name, $arguments)
	{
		foreach ($this->_objects as $obj) {
			$value = call_user_func_array(array($obj, $name), $arguments);
		}

		return $value;
	}
}
}
 




namespace Orb\Util
{

use \Orb\Util\Numbers;


class Dates
{
	
	const SECS_MIN = 60;
	const SECS_HOUR = 3600;
	const SECS_DAY = 86400;
	const SECS_WEEK = 604800;
	const SECS_MONTH = 2419200;
	const SECS_YEAR = 29030400;
	

	
	const UNIT_MINUTES = 'minutes';
	const UNIT_HOURS   = 'hours';
	const UNIT_DAYS    = 'days';
	const UNIT_WEEKS   = 'weeks';
	const UNIT_MONTHS  = 'months';
	const UNIT_YEARS   = 'years';
	

	
	public static function checkLeapYear($year)
	{
		if (strlen($year) == 2) {
			if ($year == '00' OR $year < 20) {
				$year = '20' . $year;
			} else {
				$year = '19' . $year;
			}
		}

		$year = (int)$year;

		if ( $year % 400 == 0 OR ($year % 100 != 0 && $year % 4 == 0)) {
			return true;
		}

		return false;
	}

	
    public static function daysInMonth($month, $year = null)
    {
    	static $map = array(
    		1 => 31,
    		2 => 28,
    		3 => 31,
    		4 => 30,
    		5 => 31,
    		6 => 30,
    		7 => 31,
    		8 => 31,
    		9 => 30,
    		10 => 31,
    		11 => 30,
    		12 => 31,
    	);

		$month = (int)$month;
		$year  = (int)$year;

    	    	if ($month == 2) {
    		if (!$year) $year = date('Y');

    		if (self::checkLeapYear($year)) {
    			return 29;
    		}
    	}

    	if (!Numbers::inRange($month, 1, 12)) {
    		throw new \OutOfBoundsException("Invalid month `$month`. Must be 1-12.");
    	}

    	return $map[$month];
    }


    
    public static function lastDayInMonth($month = null, $year = null)
    {
    	if ($month === null) $month = date('n');
    	if ($year === null) $year = date('Y');

		$month = (int)$month;
		$year  = (int)$year;

    	return new \DateTime('@' . mktime(23, 59, 59, $month+1, 0, $year));
    }


    
    public static function firstDayInMonth($month = null, $year = null)
    {
    	if ($month === null) $month = date('n');
    	if ($year === null) $year = date('Y');

		$month = (int)$month;
		$year  = (int)$year;

    	return new \DateTime('@' . mktime(0, 0, 0, $month, 1, $year));
    }


	
	public static function modMonths(\DateTime $date, $mod_months)
	{
		$month = (int)$date->format('n');
		$year  = (int)$date->format('Y');
		$day   = (int)$date->format('j');

		$new_date = clone $date;

		$neg = false;
		if ($mod_months < 1) {
			$neg = true;
			$mod_months = abs($mod_months);
		}

		do {
			if ($neg) {
				$month--;
				if ($month < 1) {
					$month = 12;
					$year--;
				}
			} else {
				$month++;
				if ($month > 12) {
					$month = 1;
					$year++;
				}
			}
		} while (--$mod_months);

		$max_day = self::daysInMonth($month, $year);
		if ($day > $max_day) {
			$day = $max_day;
		}

		$new_date->setDate($year, $month, $day);
		return $new_date;
	}


	
	public static function modYears(\DateTime $date, $mod_years)
	{
		$month = (int)$date->format('n');
		$year  = (int)$date->format('Y');
		$day   = (int)$date->format('j');

		$new_date = clone $date;

		$year += $mod_years;

		$new_date->setDate($year, $month, $day);
		return $new_date;
	}


	
	public static function secsToPartsArray($seconds)
	{
		$years = intval($seconds / self::SECS_YEAR);
		$seconds -= $years * self::SECS_YEAR;

		$days = intval($seconds / self::SECS_DAY);
		$seconds -= $days * self::SECS_DAY;

		$hours = intval($seconds / self::SECS_HOUR);
		$seconds -= $hours * self::SECS_HOUR;

		$minutes = intval($seconds / self::SECS_MIN);

		$seconds = intval($seconds - ($minutes * self::SECS_MIN));

		return array('years' => $years, 'days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds);
	}


	
	public static function dateToAgo(\DateTime $date, $detail = 2, $lang = null)
	{
		$ts = $date->getTimestamp();
		return self::secsToReadable(time() - $ts, $detail, $lang);
	}


	
	public static function secsToReadable($seconds, $detail = 2, $lang = null)
	{
		static $lang_en = array(
			'seconds' => '%d seconds',
			'minutes' => '%d minutes',
			'hours' => '%d hours',
			'days' => '%d days',
			'years' => '%d years',
			'sep' => ' ',
		);

		static $lang_en_short = array(
			'seconds' => '%ds',
			'minutes' => '%dm',
			'hours' => '%dh',
			'days' => '%dd',
			'years' => '%dy',
			'sep' => ' ',
		);

		if (!$lang OR $lang == 'long') {
			$lang = $lang_en;
		} elseif ($lang == 'short') {
			$lang = $lang_en_short;
		} elseif (!is_array($lang)) {
			throw new \Exception('Language must be long, short or an array of phrases');
		}

		$parts = self::secsToPartsArray($seconds);
		$limit = 0;
		$str_parts = array();

		if ($parts['years']) {
			$str_parts[] = sprintf($lang['years'], $parts['years']);
			++$limit;
		}

		if ($limit < $detail) {
			if ($limit) ++$limit;
			if ($parts['days']) {
				$str_parts[] = sprintf($lang['days'], $parts['days']);
			}
		}

		if ($limit < $detail) {
			if ($limit) ++$limit;
			if ($parts['hours']) {
				$str_parts[] = sprintf($lang['hours'], $parts['hours']);
			}
		}

		if ($limit < $detail) {
			if ($limit) ++$limit;
			if ($parts['minutes']) {
				$str_parts[] = sprintf($lang['minutes'], $parts['minutes']);
			}
		}

		if ($limit < $detail) {
			if ($parts['seconds']) {
				$str_parts[] = sprintf($lang['seconds'], $parts['seconds']);
			}
		}

		return implode($lang['sep'], $str_parts);
	}


	
	public static function convertToUtcDateTime(\DateTime $datetime)
	{
		$datetime2 = clone $datetime;
		$datetime2->setTimezone(self::tzUtc());

		return self::makeUtcDateTime($datetime2);
	}


	
	public static function makeUtcDateTime(\DateTime $datetime)
	{
		$utc_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
		return $utc_datetime;
	}


	
	public static function tzUtc()
	{
		static $tz;
		if (!$tz) {
			$tz = new \DateTimeZone('UTC');
		}

		return $tz;
	}

	
	public static function timezoneOffsetToName($offset, $dst = null)
	{
		$offset *= 3600;

		if ($dst === null) {
			$dst = (bool)((int)date('I'));
		}

		$timezone = timezone_name_from_abbr('', $offset, $dst);

		if ($timezone !== false) {
			return $timezone;
		}
		foreach (timezone_abbreviations_list() as $abbr) {
			foreach ($abbr as $city) {
				if ((bool)$city['dst'] === $dst && $city['timezone_id'] && $city['offset'] == $offset) {
					return $city['timezone_id'];
				}
			}
		}

		return false;
    }


	
	public static function getTimezoneOffset($tz)
	{
		if (is_string($tz)) {
			$tz = new \DateTimeZone($tz);
		}

		$tz_utc = new \DateTimeZone('UTC');

		$now = new \DateTime('now', $tz_utc);

		$offset = $tz->getOffset($now);
		return $offset;
	}


	
	public static function getTimezoneOffsetString($tz)
	{
		$offset = self::getTimezoneOffset($tz);

		if ($offset == 0) {
			return 'UTC';
		}

		$hours = $offset / 60 / 60;

		if ($hours < 0) {
			return "UTC" . $hours;
		} else {
			return "UTC+" . $hours;
		}
	}


	
	public static function getUnitInSeconds($num, $unit)
	{
		switch ($unit) {
			case self::UNIT_MINUTES:
				return $num * 60;
			case self::UNIT_HOURS:
				return $num * 60 * 60;
			case self::UNIT_DAYS:
				return $num * 60 * 60 * 24;
			case self::UNIT_WEEKS:
				return $num * 60 * 60 * 24 * 7;
			case self::UNIT_MONTHS:
				return $num * 60 * 60 * 24 * 7 * 30;
			case self::UNIT_YEARS:
				return $num * 60 * 60 * 24 * 365;
		}

		throw new \InvalidArgumentException("$unit is not a known unit");
	}
}
}
 




namespace Orb\Util
{


class Numbers
{
	const ROUND_MULTIPLE_NEAR = 1;
	const ROUND_MULTIPLE_UP   = 2;
	const ROUND_MULTIPLE_DOWN = 3;



	
	public static function isInteger($value)
	{
		if (!is_scalar($value) OR is_array($value)) {
			return false;
		}

		if (is_int($value) OR ((string)((int)$value)) == (string)$value) {
			return true;
		}

		return false;
	}


	
	public static function bound($num, $min, $max)
	{
		if ($num < $min) $num = $min;
		if ($num > $max) $num = $max;
		return $num;
	}


	
	public static function inRange($what, $min = 0, $max = 10)
	{
	    if ($what >= $min AND $what <= $max) {
	        return true;
	    }

	    return false;
	}



	
	public static function romanNumerals($num)
	{
		static $map = array(
			'M' => 1000,
			'CM' => 900,
			'D' => 500,
			'CD' => 400,
			'C' => 100,
			'XC' => 90,
			'L' => 50,
			'XL' => 40,
			'X' => 10,
			'IX' => 9,
			'V' => 5,
			'IV' => 4,
			'I' => 1,
		);

		$num = intval($num);
		$res = '';

		foreach ($map as $roman => $value) {
			$res .= str_repeat($roman, (int)$num/$value);
			$num %= $value;
		}

		return $res;
	}



	
	public static function filesizeDisplay($bytes, $mode = 'auto')
	{
		if ($mode == 'auto') {
			$parts = self::getFilesizeDisplayParts($bytes, 'si');
			$parts['number'] = sprintf('%.2f', $parts['number']);
			if (!strpos($parts['number'], '.00')) {
				$parts = self::getFilesizeDisplayParts($bytes, 'iec');
			}
		} else {
			$parts = self::getFilesizeDisplayParts($bytes, $mode);
		}

		return sprintf('%.2f %s', $parts['number'], $parts['symbol']);
	}



	
	public static function getFilesizeDisplayParts($bytes, $mode = 'si')
	{
		if (!$bytes OR $bytes < 1) {
			return array('number' => 0, 'symbol' => 'B');
	    }

		$x = $mode == 'si' ? 1000 : 1024;

	    $all_symbols = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $exp = floor(log($bytes)/log($x));
        $val = $bytes/pow($x, floor($exp));

        $sym = '';
        if (isset($all_symbols[$exp])) {
            $sym = $all_symbols[$exp];
        }

		return array(
			'number' => $val,
			'symbol' => $sym
		);

	}



	
	public static function roundToMultiple($number, $multiple, $mode = self::ROUND_MULTIPLE_NEAREST)
	{
		if ($mode == self::ROUND_MULTIPLE_NEAR) {
			return round($number / $multiple) * $multiple;
		} elseif ($mode == self::ROUND_MULTIPLE_DOWN) {
			return floor(floor($number) / $multiple) * $multiple;
		} else {
			return ceil(ceil($number) / $multiple) * $multiple;
		}
	}



	
	public static function getPaginationPages($num_results, $page, $per_page, $pad = 5)
	{
		$info = array();

		$num_pages = ceil($num_results / $per_page);
		if (!$num_pages) $num_pages = 1;

		$range_start = max(1, $page - floor(($pad-1) / 2));
		$range_end = max(min($num_pages, $page + floor(($pad-1) / 2)), $pad);

		if ($range_end > $num_pages) {
			$range_end = $num_pages;
		}

		$info['per_page'] = $per_page;
		$info['pages'] = range($range_start, $range_end);
		$info['prev'] = ($page != 1) ? $page-1 : false;
		$info['next'] = ($page < $num_pages) ? $page+1 : false;
		$info['first'] = 1;
		$info['last'] = $num_pages;
		$info['curpage'] = $page;
		$info['total_results'] = $num_results;
		$info['first_result'] = (($page-1) * $per_page) + 1;
		$info['last_result'] = (($page-1) * $per_page) + $per_page;

		$info['curpage'] = self::bound($info['curpage'], 1, $info['last']);

		$info['cursor'] = $page;
		$info['limit'] = $per_page;

		return $info;
	}


	
	public static function parseIniSize($val)
	{
		$val = trim($val);
		$last = strtoupper($val[strlen($val)-1]);

				if (ctype_digit($last)) {
			return (int)$val;
		}

		$val = (int)$val;

		if ($last != 'G' && $last != 'M' && $last != 'K') {
			throw new \InvalidArgumentException("Invalid size string `$val`");
		}

		switch($last) {
			case 'G':
				$val *= 1024;
			case 'M':
				$val *= 1024;
			case 'K':
				$val *= 1024;
		}

		return $val;
	}


	
	public static function hex2rgb($hex)
	{
		return Colors::hex2rgb($hex);
	}


	
	public static function ordinalSuffix($number)
	{
		if (!$number) {
			return '';
		}

		if ($number % 100 > 10 && $number % 100 < 14) {
			$suffix = 'th';
		} else {
			switch(substr($number, -1, 1)) {
				case '1': $suffix = 'st'; break;
				case '2': $suffix = 'nd'; break;
				case '3': $suffix = 'rd'; break;
				default:  $suffix = 'th';
			}
    	}

		return $suffix;
	}


	
	public static function isTimestamp($input)
	{
		return self::isInteger($input) && strlen($input) <= 10 && ctype_digit($input);
	}
}
}
 




namespace Orb\Util
{

use Orb\Util\DOMDocument;


class Strings
{
	private function __construct() {  }

	
	const CHARS_ALPHANUM     = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	const CHARS_ALPHANUM_I   = '0123456789abcdefghijklmnopqrstuvwxyz';
	const CHARS_ALPHANUM_IU  = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const CHARS_NUM          = '0123456789';
	const CHARS_ALPHA        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	const CHARS_ALPHA_I      = 'abcdefghijklmnopqrstuvwxyz';
	const CHARS_ALPHA_IU     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const CHARS_SECURE       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()-_=+{}|[]:;,./<>?';
	const CHARS_KEY          = '23456789ABCDGHJKMNPQRSTWXYZ';
	const CHARS_KEY_ALPHA    = 'ABCDGHJKMNPQRSTWXYZ';
	const CHARS_KEY_NUM      = '23456789';
	


	
	const EOL_LF = "\n";
	const EOL_CRLF = "\r\n";
	const EOL_CR = "\r";
	


	
	const BOUNDARY_APPEND = 1;
	const BOUNDARY_ARRAY = 2;
	const BOUNDARY_FIRST = 3;
	

	
	const ZERO_WIDTH_SPACE = "\xE2\x80\x8B";
	const SOFT_HYPHEN = "\xC2\xAD";
	

	
	const EQUALSLINES_DUPE_OVERWRITE = 1;


	
	const EQUALSLINES_DUPE_ADD_ARRAY = 2;

	
	protected static $php_utf8_dir = null;



	
	public static function addslashesJs($string)
	{
		$str = str_replace(array('\\', '\'', '"', "\n", "\r"), array('\\\\', "\'", '\\"', "\\n", "\\r"), trim($string));

								$str = preg_replace('#<(\s*/script\s*>)#i', '\\x3C\\1', $str);

		return $str;
	}



	
	public static function random($len = 8, $chars = null)
	{
		if (!$chars) {
			$chars = self::CHARS_ALPHANUM;
		}

		$string = '';
		$max_range = strlen($chars) - 1;

		for ($i = 0; $i < $len; $i++) {
			$string .= $chars[mt_rand(0, $max_range)];
		}

		return $string;
	}



	
	public static function randomPronounceable($len = 10)
	{
		static $vowels, $cons, $num_vowels, $num_cons;

		if (!$vowels) {
			$vowels = array('a', 'e', 'i', 'o', 'u');
			$cons = array(
				'b', 'c', 'd', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr',
				'cr', 'br', 'fr', 'th', 'dr', 'ch', 'ph', 'wr', 'st', 'sp', 'sw', 'pr', 'sl', 'cl'
				);

				$num_vowels = count($vowels);
				$num_cons = count($cons);
		}

		$string = '';
		for($i = 0; $i < $len; $i++){
			$string .= $cons[mt_rand(0, $num_cons - 1)] . $vowels[mt_rand(0, $num_vowels - 1)];
		}

		return substr($string, 0, $len);
	}



	
	public static function standardEol($string, $eol = self::EOL_LF)
	{
		return preg_replace('#\n|\r\n|\r#', $eol, $string);
	}


	
	public static function removeLineBreaks($string)
	{
		return preg_replace('#\n|\r\n|\r#', ' ', $string);
	}


	
	public static function getFirstLine($string)
	{
		$string = self::standardEol($string);

		if (($pos = strpos($string, "\n")) !== false) {
			$string = substr($string, 0, $pos);
		}

		return $string;
	}



	
	public static function getLastLine($string)
	{
		$string = self::standardEol($string);

		$lines = explode("\n", $string);

		return array_pop($lines);
	}



	
	public static function isIn($needle, $haystack, $any_needle = false)
	{
		if (is_array($needle)) {
			foreach ($needle as $n) {
				if (self::isIn($n, $haystack)) {
					if ($any_needle) {
						return true;
					}
				} else {
					if (!$any_needle) {
						return false;
					}
				}
			}

			return true;
		}

		return (strpos($haystack, $needle) !== false);
	}



	
	public static function startsWith($needle, $haystack)
	{
		if (is_array($needle)) {
			foreach ($needle as $n) {
				if (self::startsWith($n, $haystack)) {
					return true;
				}
			}

			return false;
		}

		if ($needle == $haystack OR $haystack == '') {
			return true;
		}

		return (strpos($haystack, $needle) === 0);
	}



	
	public static function endsWith($needle, $haystack)
	{
		if (is_array($needle)) {
			foreach ($needle as $n) {
				if (self::endsWith($n, $haystack)) {
					return true;
				}
			}

			return false;
		}

		return Strings::startsWith(strrev($needle), strrev($haystack));
	}



	
	public static function getFromStart($string, $num = 1)
	{
		return substr($string, 0, $num);
	}



	
	public static function getFromEnd($string, $num = 1)
	{
		return substr($string, strlen($string) - $num);
	}



	
	public static function delFromEnd($string, $num = 1)
	{
		return substr($string, 0, strlen($string) - $num);
	}



	
	public static function delFromStart($string, $num = 1)
	{
		return substr($string, $num);
	}



	
	public static function getFromIndex($string, $index_start = 0, $index_end = null)
	{
		if ($index_end === null) {
			return substr($string, $index_start);
		}

		$length = ($index_end - $index_start);

		return substr($string, $index_start, $length);
	}



	
	public static function format()
	{
		$args = func_get_args();

						
		$string = array_shift($args);

				$string = str_replace('%', '%%', $string);

				$count = 0;
		$string = preg_replace('#\{([0-9]+)\}#', '%\\1$s', $string, -1, $count);

		if (!$count) {
			return $string;
		}

						$args = array_pad($args, $count, '');


						
		return vsprintf($string, $args);
	}



	
	public static function getExtension($string)
	{
		$matches = null;
		if (preg_match('#\.([a-zA-Z0-9]+)$#', $string, $matches)) {
			return strtolower($matches[1]);
		}

		return '';
	}



	
	public static function getAboveBoundary($string, $boundary)
	{
		$pos = strpos($string, $boundary);

		if ($pos === false) return '';

		return substr($string, 0, $pos);
	}



	
	public static function getBelowBoundary($string, $boundary)
	{
		$pos = strpos($string, $boundary);

		if ($pos === false) return '';

		return substr($string, $pos+strlen($boundary));
	}



	
	public static function getBetweenBoundary($string, $boundary_start, $boundary_end = null, $mode = self::BOUNDARY_APPEND)
	{
		if (!$boundary_end) $boundary_end = $boundary_start;

		$boundary_start = preg_quote($boundary_start, '#');
		$boundary_end = preg_quote($boundary_end, '#');
		$regex = "#$boundary_start(.*?)$boundary_end#ms";

		$matches = array();
		if (!preg_match_all($regex, $string, $matches)) {
			if ($mode == self::BOUNDARY_ARRAY) {
				return array();
			} else {
				return '';
			}
		}

				if ($mode == self::BOUNDARY_ARRAY) {
			return $matches[1];
		}

				if ($mode == self::BOUNDARY_FIRST) {
			return $matches[1][0];
		}

				$res = array();
		foreach ($matches[1] as $m) {
			$res[] = $m;
		}

		return implode('', $res);
	}



	
	public static function dashToCamelCase($str)
	{
		$new_str = '';
		$str = strtolower($str);

				$do_upper = false;
		for ($i = 0; $i < strlen($str); $i++) {
			if ($str[$i] == '-') {
				$do_upper = true;
			} elseif ($do_upper) {
				$new_str .= strtoupper($str[$i]);
				$do_upper = false;
			} else {
				$new_str .= $str[$i];
			}
		}

		return $new_str;
	}


	
	public static function underscoreToCamelCase($str)
	{
		return self::dashToCamelCase(str_replace('_', '-', $str));
	}


	
	public static function camelCaseToDash($str)
	{
		return strtolower(preg_replace('#([a-z0-9])([A-Z])#', '$1-$2', $str));
	}



	
	public static function camelCaseToUnderscore($str)
	{
		return strtolower(preg_replace('#([a-z0-9])([A-Z])#', '$1_$2', $str));
	}



	
	public static function parseEqualsLines($str, $dupe_mode = self::EQUALSLINES_DUPE_OVERWRITE)
	{
		if (!is_array($str)) {
			$str = self::standardEol($str);
			$str = explode("\n", $str);
		}

		$values = array();

		foreach ($str as $line) {

						if (!$line) continue;

						if ($line[0] == '#') continue;

			$vals = explode('=', $line, 2);
			if (!isset($vals[1])) continue; 
			$key = trim($vals[0]);
			$val = trim($vals[1]);

			if (Numbers::isInteger($val)) {
				$val = (int)$val;
			}

						if ($dupe_mode == self::EQUALSLINES_DUPE_OVERWRITE) {
				$values[$key] = $val;

						} else {
				if (isset($values[$key])) {
					if (is_array($values[$key])) {
						$values[$key][] = $val;
					} else {
						$values[$key] = array($values[$key], $val);
					}
				} else {
					$values[$key] = $val;
				}
			}
		}

		return $values;
	}



	
	public static function quotedPrintableEncode($input, $line_max = 75)
	{
		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines = preg_split("/(?:\r\n|\r|\n)/", $input);
		$linebreak = "=0D=0A=\r\n";

		$line_max = $line_max - strlen($linebreak);
		$escape = "=";
		$output = "";
		$cur_conv_line = "";
		$length = 0;
		$whitespace_pos = 0;
		$addtl_chars = 0;

		for ($j=0; $j<count($lines); $j++) {
			$line = $lines[$j];
			$linlen = strlen($line);

			for ($i = 0; $i < $linlen; $i++) {
				$c = substr($line, $i, 1);
				$dec = ord($c);

				$length++;

				if ($dec == 32) {
										if (($i == ($linlen - 1))) {
						$c = "=20";
						$length += 2;
					}

					$addtl_chars = 0;
					$whitespace_pos = $i;
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) {
					$h2 = floor($dec/16); $h1 = floor($dec%16);
					$c = $escape . $hex["$h2"] . $hex["$h1"];
					$length += 2;
					$addtl_chars += 2;
				}

								if ($length >= $line_max) {
					$cur_conv_line .= $c;

										$whitesp_diff = $i - $whitespace_pos + $addtl_chars;
					$output .= substr($cur_conv_line, 0,
					(strlen($cur_conv_line) - $whitesp_diff)) .
					$linebreak;

					
					$i =  $i - $whitesp_diff + $addtl_chars;

					$cur_conv_line = "";
					$length = 0;
					$whitespace_pos = 0;
				} else {
										$cur_conv_line .= $c;
				}
			} 
			$length = 0;
			$whitespace_pos = 0;
			$output .= $cur_conv_line;
			$cur_conv_line = "";

			if ($j<=count($lines)-1) {
				$output .= $linebreak;
			}
		}

		return trim($output);
	}



	
	static public function extractRegexMatch($regex, $string, $index = 1, $flags = null, $offset = null)
	{
		$matches = null;
		if (!preg_match($regex, $string, $matches, $flags, $offset)) {
			return null;
		}

		if ($index == -1 OR $index === null) {
			return $matches;
		}

		return isset($matches[$index]) ? $matches[$index] : null;
	}


	
	static public function smartWordWrap($string, $max_len = 75, $break = ' ', $split_chars = " \t\n")
	{
		$string = self::standardEol($string);

		$r_split_chars = preg_quote($split_chars, '#');
		$segs = preg_split('#([' . $r_split_chars . '])#', $string, -1, \PREG_SPLIT_DELIM_CAPTURE);
		$string = '';

		foreach ($segs as $seg) {
			if (strlen($seg) > $max_len && self::utf8_strlen($seg) > $max_len) {
				$chars = self::utf8_str_split($seg);
				$chars = array_chunk($chars, $max_len);

				foreach ($chars as $chunk) {
					$string .= implode('', $chunk) . $break;
				}
			} else {
				$string .= $seg;
			}
		}

		$string = preg_replace('#' . $r_split_chars . '$#', '', $string);

		return $string;
	}


	
	static public function strReplaceLimit($search, $replace, $subject, $limit = 1, $reverse = false)
	{
		if (is_array($search)) {
			foreach ($search as $k => $s) {
				if (is_array($replace)) {
					$r = $replace[$k];
				} else {
					$r = $replace;
				}

				$subject = self::strReplaceLimit($s, $r, $subject, $limit, $reverse);
			}
		} else {
			$x = 0;
			while ($x++ < $limit) {
				if ($reverse) {
					$pos = strrpos($subject, $search);
				} else {
					$pos = strpos($subject, $search);
				}
				if ($pos === false) break;
				$subject = substr_replace($subject, $replace, $pos, strlen($search));
			}
		}

		return $subject;
	}


	
	static public function cut($string, $cut_start, $cut_end)
	{
		if ($cut_start == 0) {
			return substr($string, $cut_end);
		}

		return substr($string, 0, $cut_start) . substr($string, $cut_end);
	}


	
	static public function inject($string, $inject_string, $inject_at)
	{
		if ($inject_at == 0) {
			return $inject_string . $string;
		} elseif ($inject_at > 0 && !isset($string[$inject_at])) {
			return $string . $inject_string;
		}

		return substr($string, 0, $inject_at) . $inject_string . substr($string, $inject_at);
	}



	
	static public function getInputRegexPattern($input)
	{
				if (@preg_match($input, 'test') === false) {
			$input = "/" . str_replace('/', '\\/', $input) . "/";
		}

				if (@preg_match($input, 'test') === false) {
			return false;
		}

		$delim = $input[0];
		if (($pos = strrpos($input, $delim)) === false) {
						switch ($delim) {
				case '{': $delim = '}'; break;
				case '<': $delim = '>'; break;
				case '[': $delim = ']'; break;
				case '(': $delim = ')'; break;
			}
			$pos = strrpos($input, $delim);
		}

		if ($pos === false) {
			return false;
		}

		$modifiers = substr($input, $pos+1);
		if (strpos($modifiers, 'e') !== false) {
			return false;
		}

		return $input;
	}



	
	static public function slugifyTitle($string)
	{
		$string = preg_replace('#[^a-zA-Z0-9]#', '-', $string);
		$string = preg_replace('#\-{2,}#', '-', $string); 		$string = preg_replace('#^\-+#', '', $string); 		$string = preg_replace('#\-+$#', '', $string); 		$string = strtolower($string);

		return $string;
	}



	
	public static function nl2p($string)
	{
		$string = '<p>' . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p><p>', $string) . '</p>';
		$string = str_replace('<p></p>', '', $string);
		$string = nl2br($string);

		return $string;
	}



	
	public static function urlencodeFull($string)
	{
		$ret = '';
		$len = strlen($string);
		for ($i = 0; $i < $len; $i++) {
			$hex = hexdec(ord($string[$i]));
			if ($hex) {
				$ret .= isset($hex[1]) ? '%' . strtoupper($hex) : '%0' . strtoupper($hex);
			} else {
				$ret .= rawurlencode($string[$i]);
			}
		}

		return $ret;
	}


	
	public static function autoLink($text, $short = true)
	{
		$pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
		$callback = function($matches) use ($short) {
			$url       = array_shift($matches);

			$text = parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);
			$text = preg_replace("/^www./", "", $text);

			if ($short) {
				$last = -(strlen(strrchr($text, "/"))) + 1;
				if ($last < 0) {
				   $text = substr($text, 0, $last) . "&hellip;";
				}
			}

			return sprintf('<a href="%s">%s</a>', $url, $text);
		};

		return preg_replace_callback($pattern, $callback, $text);
	}



	
	public static function htmlentitiesFull($string)
	{
		$ret = '';
		$len = strlen($string);

		for ($i = 0; $i < $len; $i++) {
			$enc = htmlentities($string[$i], ENT_QUOTES);
			$ret .= $string[$i] == $enc[0] ? '&#' . ord($string[$i]) : $enc;
		}

		return $ret;
	}



	
	public static function htmlAttributes(array $attributes, $do_escape = true)
	{
		$attr = array();

		foreach ($attributes as $k => $v) {
			if ($v === false OR $v === null OR ($v === '' AND $k != 'value')) {
				continue;
			}

			if ($v === true) {
				$attr[] = "$k=\"$k\"";
			} else {
				$attr[] = $k . '="' . ($do_escape ? @htmlspecialchars((string)$v, ENT_QUOTES) : $v) . '"';
			}
		}

		return implode(' ', $attr);
	}



	
	public static function rexplode($delim, $string, $count = 2)
	{
		$parts = explode($delim, $string);
		$len = count($parts);
		if ($len <= $count) {
			return $parts;
		}

		$offset = $len - $count + 1;
		$parts = array_merge(
			array(implode($delim, array_slice($parts, 0, $offset))),
			array_slice($parts, $offset)
		);

		return $parts;
	}



	
	public static function isStarMatch($pattern, $test, &$matches = null)
	{
				if (strpos($pattern, '*') === false) {
			return ($pattern == $test);
		}

		$pattern = preg_quote($pattern, '#');
		$pattern = str_replace('\\*', '(.*?)', $pattern);
		$pattern = "#^$pattern$#";

		return preg_match($pattern, $test, $matches);
	}


	
	public static function trimHtml($string)
	{
				do {
			$old_string = $string;

			$string = trim($string);

						$string = preg_replace('#^\s*(<div[^>]*>)\s*(<br>|<br />|<p></p>|<p>\s*</p>|<p><br\s*/?></p>|<p>&nbsp;</p>|&nsbp;)\s*#iu', '$1', $string);
			$string = preg_replace('#^\s*(<br>|<br />|<p></p>|<p>\s*</p>|<p><br\s*/?></p>|<p>&nbsp;</p>|&nsbp;)\s*#iu', '', $string);

						$string = preg_replace('#\s*(<br>|<br />|<p></p>|<p>\s*</p>|<p><br\s*/?></p>|<p>&nbsp;</p>|<p>&\#xA0;</p>|<p>'.Strings::chrUni(160).'</p>|&nsbp;)\s*</div>$#iu', '</div>', $string);
			$string = preg_replace('#(<br>|<br />|<p></p>|<p>\s*</p>|<p><br\s*/?></p>|<p>&nbsp;</p>|<p>&\#xA0;</p>|<p>'.Strings::chrUni(160).'</p>|&nsbp;)$#i', '', $string);

			$string = preg_replace('#^(\s|<br>|<br />|<br/>|<p>\s*</p>)#iu', '', $string);
			$string = preg_replace('#(\s|<br>|<br />|<br/>|<p>\s*</p>)$#iu', '', $string);

			$string = preg_replace('#(<hr />|<hr>|<hr></hr>)+$#iu', '', $string);
			$string = preg_replace('#(<hr />|<hr>|<hr></hr>)+$#iu', '', $string);
		} while ($string != $old_string);

		return $string;
	}


	
	public static function trimHtmlAdvanced($html)
	{
		$html = Strings::extractBodyTag($html);
		$html = str_replace('<span></span>', '', $html);

						$html = '<body>' . $html . '</body>';

		do {
			$changed = false;
			$newhtml = preg_replace('#<span[^>]*>( | |&nbsp;|&\#xA0;)*</span>#i', '', $html);
			$newhtml = preg_replace('#<p[^>]*>( | |&nbsp;|&\#xA0;)*</p>#i', '<br />', $newhtml);
			$newhtml = preg_replace('#<div[^>]*>( | |&nbsp;|&\#xA0;)*</div>#i', '', $newhtml);

			if ($newhtml != $html) {
				$changed = true;
				$html = $newhtml;
			}
		} while ($changed);

		$qp = \QueryPath::withHTML($html, null, array('convert_to_encoding' => null));

				do {
			$changed = false;
			$qp->top()->find('div');
			foreach ($qp as $div) {
				if (!trim($div->text())) {
					$changed = true;
					$children = $div->branch();
					$children->children();
					foreach ($children as $child) {
						@$div->before($child);
					}
					@$div->remove();
					break;
				}
			}

			$qp->top();
		} while ($changed);

		ob_start();
		$qp->writeXHTML();
		$html = ob_get_clean();
		$html = Strings::extractBodyTag($html);

				do {
			$qp = \QueryPath::withHTML($html, null, array('convert_to_encoding' => null));
			$changed = false;

			
			$div = $qp->top()->find('body > *');
			if ($div->length == 1 && ($div->first() && ($div->tag() == 'div' || $div->tag() == 'p' || $div->tag() == 'span')) && !trim($div->textBefore().$div->textAfter())) {
				$changed = true;
				$html = $div->html();
				$html = trim($html);

				if ($div->tag() == 'div') {
					$html = preg_replace('#^<div.*?>#', '', $html);
					$html = preg_replace('#</div>$#', '', $html);
				} elseif ($div->tag() == 'span') {
					$html = preg_replace('#^<span.*?>#', '', $html);
					$html = preg_replace('#</span>$#', '', $html);
				} else {
					$html = preg_replace('#</p>$#', '', $html);
					$html = preg_replace('#^<p.*?>#', '', $html);
				}

				$html = Strings::extractBodyTag($html);
				$html = '<body>' . $html . '</body>';
			}

			$qp->top();
		} while($changed);

		ob_start();
		$qp->writeXHTML();
		$html = ob_get_clean();

		$html = Strings::extractBodyTag($html);
		$html = str_replace('<br></br>', '<br />', $html);

		$html_before = $html;
		$html = self::trimHtml($html);

		if (!$html) {
			$html = $html_before;
		}

				$html = \Orb\Util\Strings::decodeUnicodeEntities($html);

		return $html;
	}


	
	public static function linkify($text, $attr = '')
	{
		$search_replace = array();

		$text = preg_replace_callback('#(?<!\=(\'|")mailto:)([a-zA-Z0-9\-\._]+)@([a-zA-Z0-9\-\.]+)\.([a-zA-Z]+)\b#iu',function($m) use (&$search_replace, $attr) {
			$email = $m[2] . '@' . $m[3] . '.' . $m[4];
			$key = md5(mt_rand(0,9999) . microtime());
			$search_replace[$key] = '<a href="mailto:' . $email . '" '.$attr.'>' . @htmlspecialchars($email, \ENT_QUOTES, 'UTF-8') . '</a>';
			return $key;
		}, $text);

		$text = preg_replace_callback('#(?<!\=(\'|"))(https?:\/\/[^\s<>]+([a-zA-Z0-9\?_\-]))#iu',function($m) use (&$search_replace, $attr) {
			$url = $m[2];
			$key = md5(mt_rand(0,9999) . microtime());
			$search_replace[$key] = '<a href="' . $url . '" '.$attr.'>' . @htmlspecialchars($m[2], \ENT_QUOTES, 'UTF-8') . '</a>';
			return $key;
		}, $text);

		$text = preg_replace_callback('#(?<!\=(\'|"))(https?://|mailto:)?([a-zA-Z0-9\.\-]+\.(com|net|org|co\.uk)[^\s<>]*)#iu',function($m) use (&$search_replace, $attr) {
			if ($m[2]) return $m[0];

			$url = ($m[2] ? $m[2] : 'http://') . $m[3];
			$key = md5(mt_rand(0,9999) . microtime());
			$search_replace[$key] = '<a href="' . $url . '" '.$attr.'>' . @htmlspecialchars($m[3], \ENT_QUOTES, 'UTF-8') . '</a>';
			return $key;
		}, $text);

		$text = str_replace(array_keys($search_replace), array_values($search_replace), $text);

		return $text;
	}


	
	public static function linkifyHtml($html, $new_window = false)
	{
		libxml_use_internal_errors(true);

		$orig_html = $html;

		$html = self::extractBodyTag($html);
		$html = self::preDomDocument($html);

		$dom = new DOMDocument('1.0', 'UTF-8');
		if (strpos($html, '<body') === false) {
			$html = "<body>$html</body>";
		}
		if (strpos($html, '<?xml') === false) {
			$html = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".$html;
		}

		if (!$dom->loadHTML($html)) {
			return $orig_html;
		}

		$xpath = new \DOMXPath($dom);

		foreach ($xpath->query('//text()') as $text)
		{
			if (strpos($text->getNodePath(), '/a/') !== false) {
				continue;
			}

			$origText = $text->nodeValue;
			$newText  = self::linkify(self::postDomDocument($origText), '');
			$newText  = self::preDomDocument($newText);

			if ($origText != $newText) {
				$frag = new DOMDocument('1.0', 'UTF-8');
				$frag->loadHTML('<?xml encoding="UTF-8" version="1.0" ?><body>' . $newText . '</body>');
				$xpath2 = new \DOMXPath($frag);

				foreach ($xpath2->query('body')->item(0)->childNodes as $node) {
					$node2 = $dom->importNode($node, true);
					if ($node2) {
						$text->parentNode->insertBefore($node2, $text);
					}
				}
				$text->parentNode->removeChild($text);
			}
		}

		$html = $dom->saveHTML();
		$html = self::extractBodyTag($html);
		$html = self::postDomDocument($html);

		if ($new_window) {
			$html = str_replace('<a', '<a target="_blank"', $html);
		}

								$html = preg_replace('#<a([^>]*)href=("|\')(?![a-zA-Z0-9]+:)#', '<a$1href=$2http://', $html);

		return $html;
	}


	
	public static function preDomDocument($string)
	{
						$string = self::decodeUnicodeEntities($string);

		$string = str_replace(array('&lt;', '&gt;', '&amp;', '&nbsp;'), array('__DP_AMP_LT__', '__DP_AMP_GT__', '__DP_AMP_AMP__', '__DP_AMP_NBSP__'), $string);
		$string = self::htmlEntityEncodeUtf8($string, '__DPUNI_%s_DPUNI__');
		$string = str_replace('__DPUNI_194_DPUNI____DPUNI_160_DPUNI__', '__DP_AMP_NBSP__', $string);
		$string = str_replace('__DPUNI_160_DPUNI__', '__DP_AMP_NBSP__', $string);

		return $string;
	}


	
	public static function postDomDocument($string)
	{
				$string = preg_replace_callback('#__DPUNI_([0-9]+)_DPUNI__#', function ($m) {
			return Strings::chrUtf8($m[1]);
		}, $string);

		$string = str_replace(array('__DP_AMP_LT__', '__DP_AMP_GT__', '__DP_AMP_AMP__', '__DP_AMP_NBSP__'), array('&lt;', '&gt;', '&amp;', '&nbsp;'), $string);

		return $string;
	}


	
	public static function extractBodyTag($value)
	{
		$value = preg_replace('#(<body[^>]*>)#i', '<body>', $value);
		$count = substr_count($value, '<body>');

		if (!$count) {
			return $value;
		}

				if ($count == 1) {
			do {
				$changed = false;

				$pos = strpos($value, "<body");
				if ($pos !== false) {
					$changed = true;
					$value = substr($value, $pos);

										$pos = strpos($value, ">");
					$value = substr($value, $pos+1);
				}
			} while($changed);

			$pos = strpos($value, '</body>');
			if ($pos !== false) {
				$value = substr($value, 0, $pos);
			}

				} else {
			$value = preg_replace('#<(style|head|meta)[^>]*>.*?</\\1>#is', '', $value);
			$value = preg_replace('#<(html|body)[^>]*>#is', '', $value);
			$value = preg_replace('#</(html|body)>#is', '', $value);
			$value = str_replace('<?xml version="1.0" encoding="UTF-8"??>', '', $value);
			$value = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $value);
		}

		$value = trim($value);

		return $value;
	}

	
	public static function parseImageDataUrls($string, $raw = false)
	{
		$matches = null;
		if (!preg_match_all('#<img[^>]*/?>#i', $string, $matches[0])) {
			return array('string' => $string, 'tokens' => array());
		}

		$files = array();

		foreach ($matches[0] as $m) {
			$url_m = null;
			if (!preg_match('#src=(?:\'|")(data:[A-Za-z0-9+/=:;,]+)#i', $m[0], $url_m)) {
				continue;
			}

			$tok = '__DP_TOK_' . self::random(20, self::CHARS_ALPHANUM_IU) . '__';

			$new_str = str_replace($url_m[1], $tok, $m[0]);

			$string = str_replace(
				$m[0],
				$new_str,
				$string
			);

			if ($raw) {
				$files[] = array(
					'token'    => $tok,
					'raw_data' => $url_m[1]
				);
			} else {
				$info = self::decodeDataUrl($url_m[1]);
				$files[] = array(
					'token' => $tok,
					'type'  => $info['type'],
					'data'  => $info['data']
				);
				unset($info);
			}
		}

		return array(
			'string' => $string,
			'files'  => $files
		);
	}


	
	public static function decodeDataUrl($data_url)
	{
		if ($data_url[0] === ' ') {
			$data_url = trim($data_url);
		}

		if (substr($data_url, 0, 5) == 'data:') {
			$data_url = substr($data_url, 5);
		}

		$colon_pos  = strpos($data_url, ';');
		$comma_pos  = strpos($data_url, ',');
		$mime_type  = substr($data_url, 0, $colon_pos);
		$data      = substr($data_url, $comma_pos+1);
		$data      = @base64_decode($data);

		return array('type' => $mime_type, 'data' => $data);
	}


	
	public static function explodeTrim($delim, $string, $limit = null)
	{
		$array = explode($delim, $string, $limit);
		array_walk($array, 'trim');

		return $array;
	}


	
	public static function removeInvisibleCharacters($string)
	{
		$string = preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+#S', '', $string);
		return $string;
	}


	
	public static function convertToUtf8($string, $from_charset)
	{
				static $charset_map = array(
			'KS_C_5601-1987' => 'CP949',
        	'ISO-8859-8-I'   => 'ISO-8859-8'
		);

		$from_charset_u = strtoupper($from_charset);

		if (isset($charset_map[$from_charset_u])) {
			$from_charset   = $charset_map[$from_charset_u];
			$from_charset_u = $charset_map[$from_charset_u];
		}

		if ($from_charset_u == 'UTF-8') {
			$string = self::utf8_bad_strip($string);
			return $string;
		}

				if (strpos($from_charset, '.')) {
			$parts = explode('.', $from_charset, 2);
			$from_charset = $parts[1];
		}

				if (preg_match('#^\{(.*?)\}$#', $from_charset, $m)) {
			$from_charset = $m[1];
		}

		$new = '';
		if (function_exists('iconv')) {
			$new = @iconv($from_charset, 'UTF-8//IGNORE//TRANSLIT', $string);
		} elseif (function_exists('mb_convert_encoding')) {
			$new = mb_convert_encoding($string, 'UTF-8', $from_charset);
		} else if (strtoupper($from_charset) == 'ISO-8859-1') {
			$new = utf8_encode($string);
		}

		$new = self::utf8_bad_strip($new);

		return $new;
	}


	
	public static function chrUtf8($code)
	{
		$code = (int)$code;

				if ($code < 0) {
			return false;
		}

				if ($code < 128) {
			return chr($code);
		}

				if ($code < 160) {
			if ($code==128) $code=8364;
			elseif ($code==129) $code=160; 			elseif ($code==130) $code=8218;
			elseif ($code==131) $code=402;
			elseif ($code==132) $code=8222;
			elseif ($code==133) $code=8230;
			elseif ($code==134) $code=8224;
			elseif ($code==135) $code=8225;
			elseif ($code==136) $code=710;
			elseif ($code==137) $code=8240;
			elseif ($code==138) $code=352;
			elseif ($code==139) $code=8249;
			elseif ($code==140) $code=338;
			elseif ($code==141) $code=160; 			elseif ($code==142) $code=381;
			elseif ($code==143) $code=160; 			elseif ($code==144) $code=160; 			elseif ($code==145) $code=8216;
			elseif ($code==146) $code=8217;
			elseif ($code==147) $code=8220;
			elseif ($code==148) $code=8221;
			elseif ($code==149) $code=8226;
			elseif ($code==150) $code=8211;
			elseif ($code==151) $code=8212;
			elseif ($code==152) $code=732;
			elseif ($code==153) $code=8482;
			elseif ($code==154) $code=353;
			elseif ($code==155) $code=8250;
			elseif ($code==156) $code=339;
			elseif ($code==157) $code=160; 			elseif ($code==158) $code=382;
			elseif ($code==159) $code=376;
		}

		if ($code < 2048) {
			return chr(192 | ($code >> 6)) . chr(128 | ($code & 63));
		} elseif ($code < 65536) {
			return chr(224 | ($code >> 12)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
		} else {
			return chr(240 | ($code >> 18)) . chr(128 | (($code >> 12) & 63)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
		}
	}


	
	public static function htmlEntityDecodeUtf8($string, $escape_html = false)
	{
		$fn = function($matches) {
			if ($matches[2]) {
				return Strings::chrUtf8(hexdec($matches[3]));
			} elseif ($matches[1]) {
				return Strings::chrUtf8($matches[3]);
			}

			return '';
		};

				$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');

				$string = preg_replace_callback('/&(#(x?))?([^;]+);/', $fn, $string);

		if ($escape_html) {
			$string = @htmlspecialchars($string, \ENT_QUOTES, 'UTF-8', false);
		}

		return $string;
	}


	
	public static function htmlEntityEncodeUtf8($string, $encodeString = null)
	{
		if (!$string) {
			return $string;
		}

		$new_string = preg_replace_callback('/[^\x00-\x7F]/u', function($match) use ($encodeString) {
			$string = $match[0];
			$c1 = ord($string[0]);
			if ($c1 < 0x80) {
				return $c1;
			}

			$code = null;

			if (($c1 & 0xF8) == 0xF0) {
								$code = (($c1 & 0x07) << 18) | ((ord($string[1]) & 0x3F) << 12) | ((ord($string[2]) & 0x3F) << 6) | (ord($string[3]) & 0x3F);
			} else if (($c1 & 0xF0) == 0xE0) {
								$code = (($c1 & 0x0F) << 12) | ((ord($string[1]) & 0x3F) << 6) | (ord($string[2]) & 0x3F);
			} else if (($c1 & 0xE0) == 0xC0) {
								$code = (($c1 & 0x1F) << 6) | (ord($string[1]) & 0x3F);
			}

			if ($code) {
				if ($encodeString) {
					return sprintf($encodeString, $code);
				} else {
					return '&#' . $code . ';';
				}
			} else {
				return '?';
			}
		}, $string);

		if (!$new_string) {
			return $string;
		}

		return $new_string;
	}


	
	public static function strReplaceOne($find, $replace, $string, $reverse = false)
	{
		return self::strReplaceLimit($find, $replace, $string, 1, $reverse);
	}


	
	public static function trimLines($string, $chars = null, $mode = 'trim')
	{
		if ($mode != 'trim' && $mode != 'rtrim' && $mode != 'ltrim') {
			throw new \InvalidArgumentException("Invalid trim mode. Must be trim, rtrim or ltrim");
		}

		$string = explode("\n", $string);
		if ($chars !== null) {
			foreach ($string as &$l) $l = $mode($l, $chars);
		} else {
			foreach ($string as &$l) {
				$l = $mode($l);

								if ($mode == 'trim') {
					$l = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '$1', $l);
				} elseif ($mode == 'ltrim') {
					$l = preg_replace('/^[\pZ\pC]+/u', '$1', $l);
				} elseif ($mode == 'rtrim') {
					$l = preg_replace('/[\pZ\pC]+$/u', '$1', $l);
				}
			}
		}
		return implode("\n", $string);
	}


	
	public static function trimWhitespace($string)
	{
		$string = trim($string);
		$string = trim($string, "\x7f..\xff\x0..\x1f");
		return $string;
	}


	
	public static function html2Text($string)
	{
		$body = self::standardEol($string);
		$body = str_replace("\n", '', $body);
		$body = preg_replace('#<br[^>]*>#i', "\n", $body);
		$body = preg_replace('#<p[^>]*>#i', "\n", $body);
		$body = strip_tags($body);
		$body = Strings::decodeHtmlEntities($body);
		$body = preg_replace('#\x{00a0}#u', ' ', $body); 		$body = trim($body);

		return $body;
	}


	
	public static function removeEmptyLines($string)
	{
		$string = explode("\n", $string);

		$ret = array();
		foreach ($string as $l) {
			if (trim($l) !== '') {
				$ret[] = $l;
			}
		}

		$ret = implode("\n", $ret);
		return $ret;
	}


	
	public static function modifyLines($string, $prefix = '', $suffix = '', $trim = false)
	{
		$string = explode("\n", $string);
		foreach ($string as &$l) {
			$l = $prefix . ($trim ? trim($l) : $l) . $suffix;
		}

		return implode("\n", $string);
	}


	
	public static function asciiTable($array, array $titles = null, $line_sep = false)
	{
		$lines = array();
		$lens = array();

		if ($titles) {
			foreach ($titles as $idx => $t) {
				$tmp = Strings::utf8_strlen($t);
				if (!isset($lens[$idx]) || $tmp > $lens[$idx]) {
					$lens[$idx] = $tmp;
				}
			}
		}

		foreach ($array as $row) {
			foreach ($row as $idx => $t) {
				$tmp = Strings::utf8_strlen($t);
				if (!isset($lens[$idx]) || $tmp > $lens[$idx]) {
					$lens[$idx] = $tmp;
				}
			}
		}

		$fn_line_sep = function() use ($lens) {
			$l = array();
			foreach ($lens as $len) {
				$l[] = str_repeat('-', $len);
			}

			return '+-' . implode('-+-', $l) . '-+';
		};

		$fn_line = function($cells) use ($lens) {
			$l = array();
			foreach ($cells as $idx => $t) {
				$l[] = Strings::utf8_str_pad($t, $lens[$idx], ' ');
			}

			return '| ' . implode(' | ', $l) . ' |';
		};

		if ($titles) {
			$lines[] = $fn_line_sep();
			$lines[] = $fn_line($titles);
		}

		$lines[] = $fn_line_sep();

		foreach ($array as $row) {
			$lines[] = $fn_line($row);

			if ($line_sep) {
				$lines[] = $fn_line_sep();
			}
		}

		if (!$line_sep) {
			$lines[] = $fn_line_sep();
		}

		$lines = implode("\n", $lines);

		return $lines;
	}


	
	public static function keyValueAsciiTable(array $array, $key_title = '', $val_title = '', $line_sep = false)
	{
		$new_array = array();

		foreach ($array as $k => $v) {
			$new_array[] = array($k, $v);
		}

		$titles = array();
		if ($key_title || $val_title) {
			$titles = array($key_title, $val_title);
		}

		return self::asciiTable($new_array, $titles, $line_sep);
	}


	
	public static function prepareWysiwygHtml($html)
	{
		$html = str_replace(array('<p', '</p>'), array('<div', '</div>'), $html);
		$html = preg_replace('#(<br\s*/?>)\s*</div>#', '</div>', $html);
		$html = preg_replace('#<div[^>]*>\s*(<br\s*/?>)?\s*</div>\s*#i', "<br />\n", $html);
		do {
			$original = $html;
			$html = preg_replace('#<div>(.*)</div>\s*?#siU', "\\1<br />\n", $html);
					} while ($original != $html);

		$html = preg_replace('#(<br\s*/?>\s*)+$#', '', $html);

		return trim($html);
	}

	
	public static function convertWysiwygHtmlToText($html, $p_one_line = true)
	{
		$html = preg_replace('#</p>\s*#', $p_one_line ? "\n" : "\n\n", $html);
		$html = preg_replace('#</(div|ul|ol|li)>\s*#', "\n", $html);
		$html = preg_replace('#<br\s*/?>\s*#', "\n", $html);

		return trim(htmlspecialchars_decode(strip_tags($html)));
	}

	
	public static function compareHtml($html1, $html2)
	{
		return ($html1 == $html2 || self::_prepareCompareHtml($html1) == self::_prepareCompareHtml($html2));
	}

	protected static function _prepareCompareHtml($html)
	{
		$replace = array(
			'<br />' => '<br>',
			'<div' => '<p',
			'</div>' => '</p>'
		);
		$html = str_replace(array_keys($replace), $replace, $html);

				$html = preg_replace('#>\s+#', '>', $html);
		$html = preg_replace('#\s+#', ' ', $html);
		$html = trim($html);

		$html = preg_replace('#^<[^>]+>#', '', $html);
		$html = preg_replace('#<[^>]+>$#', '', $html);

		return trim($html);
	}


	
	public static function splitWords($string)
	{
		$split = preg_split('/\b([\(\).,\-\',:!\?;"\{\}\[\]„“»«‘\r\n]*)/u', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		return array_filter($split, function ($v) {
			if ($v = trim($v)) {
				return $v;
			}
			return false;
		});
	}


	
	public static function decodeWhitespaceHtmlEntities($string)
	{
				$repl = array(
			'&#10;'  => "\n",
			'&#xa;'  => "\n",
			'&#13;'  => "\r",
			'&#xd;'  => "\r",
			'&#9;'   => "	",
			'&#x9;'  => "	",
			'&#32;'  => ' ',
			'&#x20;' => ' ',
			'&#160;' => '&nbsp;',
			'&#xa0;' => '&nbsp;',
		);
		$string = str_ireplace(array_keys($repl), array_values($repl), $string);

		return $string;
	}


	
	public static function decodeHtmlEntities($html)
	{
		$html = self::decodeUnicodeEntities($html);

				$html = html_entity_decode($html, \ENT_QUOTES, 'UTF-8');

		return $html;
	}


	
	public static function removeBom($string)
	{
		static $bom = null;

		if ($bom === null) {
			$bom = pack('CCC', 0xEF, 0xBB, 0xBF);
		}

		if (substr($string, 0, 3) === $bom) {
			$string = substr($string, 3);
		}

		return $string;
	}


	
	public static function decodeUnicodeEntities($html)
	{
				$skip_chars = array(
			34  => true, 			39  => true, 			38  => true, 			60  => true, 			62  => true, 			160 => true, 		);

		$html = preg_replace_callback('/&#([0-9]+);/', function($m) use ($skip_chars) {
			if (isset($skip_chars[$m[1]])) {
				return $m[0];
			}
			return Strings::chrUni($m[1]);
		}, $html);

		$html = preg_replace_callback('/&#x([0-9A-F]+);/', function($m) use ($skip_chars) {
			$int = hexdec($m[1]);
			if (isset($skip_chars[$int])) {
				return $m[0];
			}
			return Strings::chrUni($int);
		}, $html);

		return $html;
	}


	
	public static function chrUni($val)
	{
		$val = intval($val);
		switch ($val) {
			case 0: return chr(0);
			case ($val & 0x7F): return chr($val);
			case ($val & 0x7FF): return chr(0xC0 | (($val >> 6) & 0x1F)) . chr(0x80 | ($val & 0x3F));
			case ($val & 0xFFFF): return chr(0xE0 | (($val >> 12) & 0x0F)) . chr(0x80 | (($val >> 6) & 0x3F)) . chr (0x80 | ($val & 0x3F));
			case ($val & 0x1FFFFF): return chr(0xF0 | ($val >> 18)) . chr(0x80 | (($val >> 12) & 0x3F)) . chr(0x80 | (($val >> 6) & 0x3F)) . chr(0x80 | ($val & 0x3F));
		}

		return '';
	}


	
	public static function utf8_bad_strip($string)
	{
		if (function_exists('iconv')) {
			return @iconv('UTF-8', 'UTF-8//IGNORE', $string);
		} elseif (function_exists('mb_convert_encoding')) {
			return @mb_convert_encoding($string, 'UTF-8', 'UTF-8');
		} else {

			$time = time();

						$UTF8_BAD =
				'([\x00-\x7F]'.                          				'|[\xC2-\xDF][\x80-\xBF]'.               				'|\xE0[\xA0-\xBF][\x80-\xBF]'.           				'|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}'.    				'|\xED[\x80-\x9F][\x80-\xBF]'.           				'|\xF0[\x90-\xBF][\x80-\xBF]{2}'.        				'|[\xF1-\xF3][\x80-\xBF]{3}'.            				'|\xF4[\x80-\x8F][\x80-\xBF]{2}'.        				'|(.{1}))';                              			ob_start();
			while (preg_match('/'.$UTF8_BAD.'/S', $string, $matches)) {
				if ( !isset($matches[2])) {
					echo $matches[0];
				}
				$string = substr($string,strlen($matches[0]));

								if (time() - $time > 6) {
					return '';
				}
			}
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}
	}


	
	public static function setPhpUtf8Dir($dir)
	{
		self::$php_utf8_dir = $dir;
	}

	public static function __callStatic($name, $args)
	{
		if (!self::$php_utf8_dir) {
			if (defined('ORB_STRINGS_UTF8_DIR')) {
				self::$php_utf8_dir = \ORB_STRINGS_UTF8_DIR;
			} else {
				throw new \BadMethodCallException('Unknown method `'.$name.'`');
			}
		}

		static $funcmap = array(
			'utf8_strlen'                      => '__CORE__',
			'utf8_strpos'                      => '__CORE__',
			'utf8_strrpos'                     => '__CORE__',
			'utf8_substr'                      => '__CORE__',
			'utf8_strtolower'                  => '__CORE__',
			'utf8_strtoupper'                  => '__CORE__',
			'utf8_ord'                         => 'ord.php',
			'utf8_ireplace'                    => 'str_ireplace.php',
			'utf8_str_pad'                     => 'str_pad.php',
			'utf8_str_split'                   => 'str_split.php',
			'utf8_strcasecmp'                  => 'strcasecmp.php',
			'utf8_strcspn'                     => 'strcspn.php',
			'utf8_stristr'                     => 'stristr.php',
			'utf8_strrev'                      => 'strrev.php',
			'utf8_strspn'                      => 'strspn.php',
			'utf8_substr_replace'              => 'substr_replace.php',
			'utf8_ltrim'                       => 'trim.php',
			'utf8_rtrim'                       => 'trim.php',
			'utf8_trim'                        => 'trim.php',
			'utf8_ucfirst'                     => 'ucfirst.php',
			'utf8_ucwords'                     => 'ucwords.php',
			'utf8_is_ascii'                    => 'utils/ascii.php',
			'utf8_is_ascii_ctrl'               => 'utils/ascii.php',
			'utf8_strip_non_ascii'             => 'utils/ascii.php',
			'utf8_strip_ascii_ctrl'            => 'utils/ascii.php',
			'utf8_strip_non_ascii_ctrl'        => 'utils/ascii.php',
			'utf8_accents_to_ascii'            => 'utils/ascii.php',
			'utf8_bad_find'                    => 'utils/bad.php',
			'utf8_bad_findall'                 => 'utils/bad.php',
			'utf8_bad_strip'                   => 'utils/bad.php',
			'utf8_bad_replace'                 => 'utils/bad.php',
			'utf8_bad_identify'                => 'utils/bad.php',
			'utf8_bad_explain'                 => 'utils/bad.php',
			'utf8_byte_position'               => 'utils/position.php',
			'utf8_locate_current_chr'          => 'utils/position.php',
			'utf8_locate_next_chr'             => 'utils/position.php',
			'utf8_specials_pattern'            => 'utils/specials.php',
			'utf8_is_word_chars'               => 'utils/specials.php',
			'utf8_strip_specials'              => 'utils/specials.php',
			'utf8_to_unicode'                  => 'utils/unicode.php',
			'utf8_from_unicode'                => 'utils/unicode.php',
			'utf8_is_valid'                    => 'utils/validation.php',
			'utf8_compliant'                   => 'utils/validation.php',
		);

		if (isset($funcmap[$name])) {
			require_once(self::$php_utf8_dir . '/ORB_LOAD.php');
			if ($funcmap[$name] !== '__CORE__') {
				require_once(self::$php_utf8_dir . '/' . $funcmap[$name]);
			}
			return call_user_func_array($name, $args);
		} else {
			throw new \BadMethodCallException('Unknown method `'.$name.'`');
		}
	}
}
}
 




namespace Orb\Util
{



class Util
{
	const BASE62_ALPHABET  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const BASE36_ALPHABET  = '0123456789abcdefghijklmnopqrstuvwxyz';
	const LETTERS_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';


	
	public static function typeof($var)
	{
		$type = gettype($var);

		if ($type == 'object') {
			$type .= ':' . get_class($var);
		}

		return $type;
	}



	
	public static function ifsetor(&$param, $or = null)
	{
		if (isset($param)) {
			return $param;
		}

		return $or;
	}



	
	public static function ifvalor($param, $or = null)
	{
		if ($param) {
			return $param;
		}
		return $or;
	}



	
	public static function defaultVal(&$param, $default = null)
	{
		if (!isset($param) or !$param) {
			$param = $default;
		}
	}



	
	public static function iff($cond, $true = true, $false = false)
	{
		return $cond ? $true : $false;
	}



	
	public static function coalesce()
	{
		foreach (func_get_args() as $v) {
			if ($v) {
				return $v;
			}
		}

		return func_get_arg(func_num_args() - 1);
	}



	
	public static function baseEncode($num, $alphabet)
	{
		if ($alphabet == 'base62') $alphabet = self::BASE62_ALPHABET;
		elseif ($alphabet == 'base36') $alphabet = self::BASE36_ALPHABET;
		elseif ($alphabet == 'letters') $alphabet = self::LETTERS_ALPHABET;

		if ($num == 0) {
			return $alphabet[0];
		}

		$arr = array();
		$base = strlen($alphabet);

		while ($num) {
			$rem = $num % $base;
			$num = (int)($num / $base);
			$arr[] = $alphabet[$rem];
		}

		$arr = array_reverse($arr);
		return implode('', $arr);
	}



	
	public static function baseDecode($string, $alphabet)
	{
		if ($alphabet == 'base62') $alphabet = self::BASE62_ALPHABET;
		elseif ($alphabet == 'base36') $alphabet = self::BASE36_ALPHABET;
		elseif ($alphabet == 'letters') $alphabet = self::LETTERS_ALPHABET;

		$alphabet = str_split($alphabet);
		$base = sizeof($alphabet);
		$strlen = strlen($string);
		$num = 0;
		$idx = 0;

		$s = str_split($string);
		$tebahpla = array_flip($alphabet);

		foreach ($s as $char) {
						if (!isset($tebahpla[$char])) {
				return null;
			}
			$power = ($strlen - ($idx + 1));
			$num += $tebahpla[$char] * (pow($base, $power));
			$idx += 1;
		}
		return $num;
	}



	
	public static function signedSerialize($data, $sign_key = 'orb_util_sign_key')
	{
		$ser = base64_encode(serialize($data));
		$ser = rtrim($ser, '=');
		$md5 = md5($sign_key . $ser);

						return $md5 . ':' . $ser;
	}



	
	public static function signedUnserialize($string, $sign_key = 'orb_util_sign_key')
	{
		$md5 = substr($string, 0, 32);
		$ser = substr($string, 33);

		$md5_check = md5($sign_key . $ser);
		if ($md5 != $md5_check) {
			throw new \Exception('Invalid data or sign key.');
		}

		return @unserialize(@base64_decode($ser));
	}



	
	public static function callUserConstructorArray($classname, array $args)
	{
		$args = array_values($args);

		switch (count($args)) {
						case 0:  $obj = new $classname(); break;
			case 1:  $obj = new $classname($args[0]); break;
			case 2:  $obj = new $classname($args[0], $args[1]); break;
			case 3:  $obj = new $classname($args[0], $args[1], $args[2]); break;
			case 4:  $obj = new $classname($args[0], $args[1], $args[2], $args[3]); break;
			case 5:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4]); break;
			case 6:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]); break;
			case 7:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]); break;
			case 8:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]); break;
			case 9:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]); break;

						default:
				$ref = new \ReflectionClass($classname);
				$obj = $ref->newInstanceArgs($args);
				break;
		}

		return $obj;
	}



	
	public static function callUserConstructor($classname)
	{
		$args = func_get_args();
		array_shift($args); 
		return self::callUserConstructorArray($classname, $args);
	}



	
	public static function requestUniqueId()
	{
		static $x = 0;

		return ++$x;
	}


	
	public static function requestUniqueIdString($prefix = 'id')
	{
		$str = $prefix . '_' . substr(time(), -4) . '_' . self::requestUniqueId();

		return $str;
	}



	
	public static function generateStaticSecurityToken($secret, $timeout = 0)
	{
		if ($timeout) {
									$expire_time = time() + $timeout + mt_rand(1, 10);
			$expire_time_enc = base_convert($expire_time, 10, 36);
		} else {
			$expire_time = 0;
			$expire_time_enc = 0;
		}

		$rand_str = Strings::random(10, Strings::CHARS_ALPHA_I);

		$token = $expire_time_enc . '-' . $rand_str . '-' . sha1($secret . $expire_time_enc . $rand_str);

		return $token;
	}



	
	public static function checkStaticSecurityToken($token, $secret)
	{
				if (substr_count($token, '-') != 2) {
			return false;
		}

		list($expire_time_enc, $rand_str, $hash) = explode('-', $token, 3);

				$check_hash = sha1($secret . $expire_time_enc . $rand_str);

		if ($check_hash != $hash) {
			return false;
		}

				if ($expire_time_enc != '0') {
			$expire_time = base_convert($expire_time_enc, 36, 10);
			if (time() > $expire_time) {
				return false;
			}
		}

		return true;
	}



	
	public static function getClassnameParts($obj_or_classname)
	{
		$classname = $obj_or_classname;
		if (is_object($classname)) {
			$classname = get_class($classname);
		}

		return explode('\\', $classname);
	}


	
	public static function getClassNamespace($obj_or_classname)
	{
		$parts = self::getClassnameParts($obj_or_classname);
		array_pop($parts);

		return implode('\\', $parts);
	}



	
	public static function getBaseClassname($obj)
	{
		$parts = self::getClassnameParts($obj);
		return array_pop($parts);
	}



	
	public static function uuid4()
	{
		$bits = self::randomData(16);

		$time_low = bin2hex(substr($bits, 0, 4));
		$time_mid = bin2hex(substr($bits, 4, 2));

		$time_hi_and_version = bin2hex(substr($bits, 6, 2));
		$time_hi_and_version = hexdec($time_hi_and_version);
		$time_hi_and_version = $time_hi_and_version >> 4;
		$time_hi_and_version = $time_hi_and_version | 0x4000;

		$clock_seq_hi_and_reserved = bin2hex(substr($bits, 8, 2));
		$clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
		$clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
		$clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

		$node = bin2hex(substr($bits,10, 6));

		return sprintf(
			'%08s-%04s-%04x-%04x-%012s',
			$time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node
		);
	}


	
	public static function hex2bin($hex_string)
	{
				if (function_exists('hex2bin')) {
			return @hex2bin($hex_string);
		}

		$len = strlen($hex_string);
		$bin_string = '';

        $pos = 0;
        while($pos < $len) {
			$bin_string .= pack("H*", substr($hex_string, $pos, 2));
            $pos += 2;
        }

        return $bin_string;
	}



	
	public static function randomData($len = 250)
	{
		$data = '';
		if (function_exists('openssl_random_pseudo_bytes')) {
			$data = openssl_random_pseudo_bytes($len);
		} else {
			$fp = @fopen('/dev/urandom','rb');
			if ($fp !== false) {
				$data = fread($fp, $len);
				fclose($fp);
			} else {
								for($x=0; $x < $len; $x++){
					$data .= chr(mt_rand(0, 255));
				}
			}
		}

		return $data;
	}



	
	public static function encodeNumberSegments(array $parts, $alphabet = 'base36')
	{
														
		$enc_numbers = array();
		foreach ($parts as $num) {
			$enc_numbers[] = self::baseEncode($num, $alphabet);
		}

		$enc_string = array();
		foreach ($enc_numbers as $enc_num) {
			$len = strlen($enc_num);
			$len_enc = self::baseEncode($len, $alphabet);
			$enc_string[] = "{$len_enc}{$enc_num}";
		}

		return implode('', $enc_string);
	}



	
	public static function decodeNumberSegments($encoded_string, $alphabet = 'base36')
	{
		$encoded_string = strtolower($encoded_string);

				if (!preg_match('#^[a-z0-9]+$#', $encoded_string)) {
			return array();
		}

		$parts = array();
		$len = strlen($encoded_string);
		$pos = 0;
		$state = 0; 		$read_len = 0;

		while ($pos < $len) {
			if ($state == 0) {
				$read_len = self::baseDecode($encoded_string[$pos], $alphabet);
				$state = 1;
				$pos++;
			} elseif ($state == 1) {

				$read = '';
				for ($i = 0; $i < $read_len; $i++) {
					$read .= $encoded_string[$pos];
					$pos++;
					if ($pos > $len) return array(); 				}

				$num = self::baseDecode($read, $alphabet);
				$parts[] = $num;

				$state = 0;
				$read_len = 0;
			}
		}

		return $parts;
	}


	
	public static function getFunctionParamsFromArray(\ReflectionFunctionAbstract $func_refl, array $options)
	{
		$ret = array();

		$params = $func_refl->getParameters();
		foreach ($params as $param) {
			$name = $param->getName();
			if (isset($options[$name])) {
				$ret[] = $options[$name];
			} else {
				if ($param->isDefaultValueAvailable()) {
					$ret[] = $param->getDefaultValue();
				} else {
										break;
				}
			}
		}

		return $ret;
	}


	
	public static function getClassFilename($classname)
	{
		if ($classname instanceof \ReflectionClass) {
			$relf = $classname;
		} else {
			$refl = new \ReflectionClass($classname);
		}

		return $refl->getFileName();
	}


	
	public static function debugVar($var, $d = 0)
	{
		if (is_object($var)) {
			if (method_exists($var, '__tostring')) {
				return str_repeat("\t", $d) . "[" . get_class($var) . ":" . $var->__tostring() . "]";
			} else {
				return str_repeat("\t", $d) . "[" . get_class($var) . "]";
			}
		} else if (is_array($var)) {
			$str = array();
			$str[] = str_repeat("\t", $d) . "array(";
			foreach ($var as $k => $v) {
				$str[] = str_repeat("\t", $d+1) . "$k: " . self::debugVar($v, $d + 1);
			}
			$str[] = str_repeat("\t", $d) . ")";
			return implode("\n", $str);
		} else {
			return str_repeat("\t", $d) . $var;
		}
	}
}
}
 




namespace Orb\Util
{


class Web
{
	const HTTP_STATUS_OK = 200;
	const HTTP_STATUS_BAD_REQUEST = 400;
	const HTTP_STATUS_NOT_FOUND = 404;
	const HTTP_STATUS_FORBIDDEN = 403;
	const HTTP_STATUS_MOVED_PERM = 301;
	const HTTP_STATUS_SERVER_ERR = 500;

	
	static public function redirect($url)
	{
				if (headers_sent()) {
			$html = '<meta http-equiv="refresh" content="0;url='.$url.'" />';
			$html .= '<script type="text/javascript">window.location="'.Orb_String::addslashesJs($url).'";</script>';
			echo $html;

				} else {
			header('Location: ' . Strings::getFirstLine($url));
		}

		exit;
	}



	
	static function sendHttpStatus($type)
	{
		if (headers_sent()) return false;

		switch ($type) {
			case self::HTTP_STATUS_OK: header('HTTP/1.1 200 OK'); break;
			case self::HTTP_STATUS_BAD_REQUEST: header('HTTP/1.1 400 Bad Request'); break;
			case self::HTTP_STATUS_NOT_FOUND: header('HTTP/1.1 404 Not Found'); break;
			case self::HTTP_STATUS_FORBIDDEN: header('HTTP/1.1 403 Forbidden'); break;
			case self::HTTP_STATUS_MOVED_PERM: header('HTTP/1.1 301 Moved Permanently'); break;
			case self::HTTP_STATUS_SERVER_ERR: header('HTTP/1.1 500 Internal Server Error'); break;
			default: return false; break;
		}

		return true;
	}



	
	public function getAttachmentHeaders($filename, $is_inline = false, $mimetype = null, $filesize = null)
	{
		$headers = array();

		if (!$filename) {
			$filename = 'file';
		}

		$disp = 'inline';
		if (!$is_inline) {
			$disp = 'attachment';
		}

		$headers['Content-Disposition'] = 'inline; filename="' . str_replace('"', '\\"', $filename) . '"';

		if ($mimetype !== null) {
			$headers['Content-Type'] = $mimetype;
		}

		if ($filesize !== null) {
			$headers['Content-Length'] = $filesize;
		}

		return $headers;
	}



	
	static public function setCookie($name, $value, $expire = 'next week', $httponly = true, $secure = null, $path = null, $domain = null)
	{
				if ($expire === null) {
			$expire = false;
		} else {
			if (!ctype_digit($expire)) {
				if ($expire == 'never') {
					$expire = mktime(0, 0, 0, 0, 0, 2020);
				} else {
					$expire = strtotime($expire);

					if (!$expire) {
						$expire = null;
						throw new \Exception('Unknown expire format: ' . $expire);
					}
				}
			}
		}

		if ($path === null) {
			$path = '/';
		}

		return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}



	
	public static function getRequestProtocol()
	{
		static $request_protocol = null;

		if ($request_protocol === null) {
			if (isset($_SERVER['HTTPS']) AND !empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] != 'off') {
				$request_protocol = 'HTTPS';
			} else {
				$request_protocol = 'HTTP';
			}
		}

		return $request_protocol;
	}



	
	public static function getUserHostname()
	{
		return @gethostbyaddr(self::getUserIp());
	}



	
	public static function getUserIp()
	{
		return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
	}



	
	public static function getUserIpAlt()
	{
		$alt_ip = null;

		if ($alt_ip === null) {

			if (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$alt_ip = $_SERVER['HTTP_CLIENT_IP'];
			}

			if (!$alt_ip AND isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip_arr = array();

				if (preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $ip_arr)) {
					foreach($ip_arr[0] AS $ip) {
						if (!preg_match("#^(10|172\.16|192\.168)\.#", $ip)) {
							$alt_ip = $ip;
							break;
						}
					}
				}
			}

			if (!$alt_ip AND isset($_SERVER['HTTP_FROM'])) {
				$alt_ip = $_SERVER['HTTP_FROM'];
			}

			if (!$alt_ip AND isset($_SERVER['REMOTE_ADDR'])) {
				$alt_ip = $_SERVER['REMOTE_ADDR'];
			}
		}

		return $alt_ip;
	}



	
	public static function getUserAgent()
	{
		return (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
	}



	
	public static function getScriptReferrer()
	{
		return (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
	}



	
	public static function getScriptPath()
	{
				if (isset($_SERVER['REQUEST_URI']) AND $_SERVER['REQUEST_URI']) {
			$script_path = $_SERVER['REQUEST_URI'];
		} else	{
			if (isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO']) {
				$script_path = $_SERVER['PATH_INFO'];

			} elseif (isset($_SERVER['REDIRECT_URL']) AND $_SERVER['REDIRECT_URL']) {
				$script_path = $_SERVER['REDIRECT_URL'];

			} elseif (isset($_SERVER['PHP_SELF']) AND $_SERVER['PHP_SELF']) {
				$script_path = $_SERVER['PHP_SELF'];
			}

			if (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING']) {
				$script_path .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		$quest_pos = strpos($script_path, '?');

		if ($quest_pos !== false) {
			$script = urldecode(substr($script_path, 0, $quest_pos));
			$script_path = $script . substr($script_path, $quest_pos);
		} else {
			$script_path = urldecode($script_path);
		}

		return $script_path;
	}



	
	public static function getRequestMethod()
	{
		return strtoupper((isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : ''));
	}



	
	public static function getCountryFromIp($ip = null)
	{
		if (!$ip) {
			$ip = Web::getUserIp();
		}

		$country = null;
		if (function_exists('geoip_country_code_by_name')) {
			$country = @geoip_country_code_by_name($ip);
		}

		if (!$country) {
			return null;
		}

		return strtoupper($country);
	}



	
	public static function urlExists($url)
	{
		$url_parts = @parse_url($url);

		if (empty($url_parts['host'])) {
			return false;
		}

		if (empty($url_parts['path'])) {
			$url_parts['path'] = '/';
		}

		if (empty($url_parts['query'])) {
			$url_parts['query'] = '';
		} else {
			$url_parts['query'] = '?' . $url_parts['query'];
		}

		if (empty($url_parts['port'])) {
			$url_parts['port'] = '80';
		}

		$errno = $errstr = null;
		$socket = @fsockopen(
			$url_parts['host'],
			$url_parts['port'],
			$errno,
			$errstr,
			10
		);

		if (!$socket) {
			return false;
		}

		@fwrite($socket, "HEAD {$url_parts['path']}{$url_parts['query']} HTTP/1.0\r\nHost: {$url_parts['host']}\r\n\r\n");
		$http_response = @fgets($socket, 22);

		$ret = false;
		if (Strings::isIn('200 OK', $http_response)) {
			$ret = true;
		}

		@fclose($socket);

		return $ret;
	}


	
	public static function isBotUseragent($useragent = null)
	{
		if ($useragent === null) {
			if (!isset($_SERVER['HTTP_USER_AGENT'])) {
				return false;
			}

			$useragent = $_SERVER['HTTP_USER_AGENT'];
		}

		$bot_strings = array(
			'AdsBot-Google', 'Googlebot-Image', 'Googlebot-Mobile', 'Googlebot',
			'Yahoo! Slurp', 'Yahoo! Slurp China', 'Yahoo-MMCrawler',
			'Openbot',
			'msnbot', 'msnbot-NewsBlogs',
			'ia_archiver',
			'Lycos',
			'Scooter',
			'AltaVista',
			'Ask Jeeves/Teoma', 'Teoma',
			'Gigabot',
			'bingbot'
		);

		foreach ($bot_strings as $bot) {
			if (strpos($useragent, $bot) !== false) {
				return true;
			}
		}

		return false;
	}
}
}
 




namespace Application\DeskPRO
{

use Application\DeskPRO\Entity;

use Application\DeskPRO\People\PersonGuest;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Orb\Util\Strings;
use Orb\Util\Arrays;


class App
{
	const DEFAULT_NAME = '__default__';

	
	const SERVICE_DB                 = 'database_connection';
	const SERVICE_ORM                = 'doctrine.orm.entity_manager';
	const SERVICE_INPUT_READER       = 'deskpro.core.input_reader';
	const SERVICE_INPUT_CLEANER      = 'deskpro.core.input_cleaner';
	const SERVICE_SETTINGS           = 'deskpro.core.settings';
	const SERVICE_SESSION            = 'session';
	const SERVICE_ROUTER             = 'router';
	const SERVICE_REQUEST            = 'request';
	const SERVICE_RESPONSE           = 'response';
	const SERVICE_MAILER             = 'mailer';
	const SERVICE_TRANSLATOR         = 'deskpro.core.translate';
	const SERVICE_EVENT_DISPATCHER   = 'event_dispatcher';
	const SERVICE_FORM_FACTORY       = 'form.factory';
	const SERVICE_SEARCH_ENGINE      = 'deskpro.search_engine';
	const SERVICE_TEMPLATING         = 'templating';
	const SERVICE_SEARCH             = 'deskpro.search_adapter';
	const SERVICE_PERSON_ACTIVITY_LOGGER = 'deskpro.person_activity_logger';
	

	
	protected static $_containers = array();

	
	protected static $_service_to_container = array();

	
	protected static $_default_contaner_name = 'default';

	
	protected static $_fileconfig = array();

	
	protected static $_environment = null;

	
	protected static $_debug = false;

	
	protected static $_kernel = null;

	
	protected static $_api_handlers = null;

	
	protected static $_current_person = null;

	
	protected static $_standard_loggers = null;

	
	protected static $_skip_caching = false;

	
	protected static $_uncachable = false;


	
	public static function setCurrentPerson(Entity\Person $person = null)
	{
		if (!$person) {
			$person = new PersonGuest();
		}
		self::$_current_person = $person;
	}


	
	public static function getCurrentPerson()
	{
		return self::$_current_person;
	}


	
	public static function setKernel(\Symfony\Component\HttpKernel\Kernel $kernel)
	{
		self::$_kernel = $kernel;
		self::$_environment = $kernel->getEnvironment();
		self::$_debug = $kernel->isDebug();
	}


	
	public static function setContainer(ContainerInterface $container, $name)
	{
		if (isset(self::$_containers[$name])) {
			throw new \InvalidArgumentException("The container with `$name` has already been set");
		}

		self::$_containers[$name] = $container;
	}


	
	public static function getClassLoader()
	{
		if (isset($GLOBALS['DP_AUTOLOADER'])) {
			return $GLOBALS['DP_AUTOLOADER'];
		}

		return null;
	}


	
	public static function getContainer($name = self::DEFAULT_NAME)
	{
		if ($name == self::DEFAULT_NAME) {
			$name = self::$_default_contaner_name;
		}
		if (!isset(self::$_containers[$name])) {
			throw new \OutOfBoundsException("There is no container set with name `$name`");
		}

		return self::$_containers[$name];
	}


	
	public static function setDefaultContainer($name)
	{
		self::$_default_contaner_name = $name;
	}


	
	public static function setDefaultContainerForService($service_name, $container_name)
	{
		self::$_service_to_container[$service_name] = $container_name;
	}


	
	public static function get($service_name, $container_name = self::DEFAULT_NAME)
	{
		if ($container_name !== null) {
			if ($container_name == self::DEFAULT_NAME AND isset(self::$_service_to_container[$service_name])) {
				$container_name = self::$_service_to_container[$service_name];
			}

			$container = self::getContainer($container_name);
			return $container->get($service_name);
		}

		foreach (self::$_containers as $container) {
			if ($container->has($service_name)) {
				return $container->get($service_name);
			}
		}

		throw new \OutOfBoundsException("There is no container with the service `$service_name`");
	}


	
	public static function getSystemService($service_name, $container_name = self::DEFAULT_NAME)
	{
		if ($container_name == self::DEFAULT_NAME AND isset(self::$_service_to_container[$service_name])) {
			$container_name = self::$_service_to_container[$service_name];
		}

		$container = self::getContainer($container_name);
		return $container->getSystemService($service_name);
	}

	
	public static function getDataService($id)
	{
		return self::getContainer(self::DEFAULT_NAME)->getSystemService($id . 'Data');
	}


	
	public static function getSystemObject($service_name, array $options = array(), $container_name = self::DEFAULT_NAME)
	{
		if ($container_name == self::DEFAULT_NAME AND isset(self::$_service_to_container[$service_name])) {
			$container_name = self::$_service_to_container[$service_name];
		}

		$container = self::getContainer($container_name);
		return $container->getSystemObject($service_name, $options);
	}


	
	public static function has($service_name, $container_name = self::DEFAULT_NAME)
	{
		if ($container_name !== null) {
			if ($container_name == self::DEFAULT_NAME AND isset(self::$_service_to_container[$service_name])) {
				$container_name = self::$_service_to_container[$service_name];
			}

			$container = self::getContainer($container_name);
			return $container->has($service_name);
		}

		foreach (self::$_containers as $container) {
			if ($container->has($service_name)) {
				return true;
			}
		}

		return false;
	}


	
	public static function getSearchAdapter()
	{
		return self::get(self::SERVICE_SEARCH);
	}


	
	public static function getDb()
	{
		return self::get(self::SERVICE_DB, self::DEFAULT_NAME);
	}

	protected static $db_read;

	
	public static function getDbRead()
	{
		if (self::$db_read === null) {
			$read = self::getConfig('db_read');
			if ($read && !empty($read['host']) & !empty($read['dbname'])) {
				self::$db_read = self::getContainer()->get('doctrine.dbal.connection_factory')->createConnection(array(
					'driver'        => 'pdo_mysql',
					'host'          => $read['host'],
					'user'          => $read['user'],
					'password'      => $read['password'],
					'dbname'        => $read['dbname']
				));
			} else {
				self::$db_read = self::getDb();
			}
		}

		return self::$db_read;
	}


	
	public static function getOrm()
	{
		return self::get(self::SERVICE_ORM);
	}


	
	public static function getRequest()
	{
		return self::get(self::SERVICE_REQUEST);
	}


	
	public static function getResponse()
	{
		return self::get(self::SERVICE_RESPONSE);
	}


	
	public static function getSession()
	{
		return self::get(self::SERVICE_SESSION);
	}


	
	public static function getMailer()
	{
		return self::get(self::SERVICE_MAILER);
	}


	
	public static function getTranslator()
	{
		return self::get(self::SERVICE_TRANSLATOR);
	}


	
	public static function getLanguage()
	{
		return self::getTranslator()->getLanguage();
	}


	
	public static function getTemplating()
	{
		return self::get(self::SERVICE_TEMPLATING);
	}


	
	public static function getRouter()
	{
		return self::get(self::SERVICE_ROUTER);
	}


	
	public static function getEventDispatcher()
	{
		return self::get(self::SERVICE_EVENT_DISPATCHER);
	}


	
	public static function getFormFactory()
	{
		return self::get(self::SERVICE_FORM_FACTORY);
	}


	
	public static function getSearchEngine()
	{
		return self::get(self::SERVICE_SEARCH_ENGINE);
	}


	
	public static function getPersonActivityLogger()
	{
		return self::get(self::SERVICE_PERSON_ACTIVITY_LOGGER);
	}


	
	public static function isWebRequest()
	{
		if (defined('DP_INTERFACE') && DP_INTERFACE == 'cli') {
			return false;
		}

		if (self::has(self::SERVICE_REQUEST) AND self::has(self::SERVICE_RESPONSE)) {
			return true;
		}

		return false;
	}


	
	public static function getEntityRepository($entity)
	{
		return self::get(self::SERVICE_ORM)->getRepository($entity);
	}


	
	public static function findEntity($entity, $id)
	{
		return self::getEntityRepository($entity)->find($id);
	}


	
	public static function getEntityClass($entity)
	{
		list ($namespace, $entity) = explode(':', $entity, 2);

		$class = "Application\\$namespace\\Entity\\$entity";

		return $class;
	}


	
	public static function getKernel()
	{
		if (!self::$_kernel) {
			throw new \RuntimeException('No kernel has been set yet');
		}

		return self::$_kernel;
	}


	
	public static function getKernelType()
	{
		$kernel = self::getKernel();

		$type = get_class($kernel);
		$type = preg_replace('#^.*?\\\\([a-zA-Z]+)Kernel$#', '$1', $type);
		$type = strtolower($type);

		return $type;
	}


	
	public static function getAppSecret()
	{
		$secret = self::getSetting('core.app_secret');
		if (!$secret) {
			$secret = 'secret';
		}

		return $secret;
	}


	
	public static function getRefGenerator()
	{
		return self::getContainer()->getSystemService('RefGenerator');
	}


	
	public static function getSetting($name)
	{
		$settings = self::get(self::SERVICE_SETTINGS);
		return $settings->get($name);
	}


	protected static $_api_handler_names = array(
		'tickets'                    => 'Application\\DeskPRO\\Tickets\\Tickets',
		'tickets.filters'            => 'Application\\DeskPRO\\Tickets\\Filters',
		'tickets.edit'               => 'Application\\DeskPRO\\Tickets\\TicketEdit',
		'tickets.search'             => 'Application\\DeskPRO\\Tickets\\TicketSearch',
		'custom_fields.chats'        => 'Application\\DeskPRO\\CustomFields\\ChatFields',
		'custom_fields.people'       => 'Application\\DeskPRO\\CustomFields\\PeopleFields',
		'custom_fields.tickets'      => 'Application\\DeskPRO\\CustomFields\\TicketFields',
		'custom_fields.articles'     => 'Application\\DeskPRO\\CustomFields\\ArticleFields',
		'custom_fields.feedback'      => 'Application\\DeskPRO\\CustomFields\\FeedbackFields',
		'custom_fields.organizations' => 'Application\\DeskPRO\\CustomFields\\OrganizationFields',
		'custom_fields.products'      => 'Application\\DeskPRO\\CustomFields\\ProductFields',
		'custom_fields.util'          => 'Application\\DeskPRO\\CustomFields\\Util',
		'filestorage'                 => '',
	);

	
	public static function getApi($name)
	{
		if (isset(self::$_api_handlers[$name])) {
			return self::$_api_handlers[$name];
		}

		if (!isset(self::$_api_handler_names[$name])) {
			throw new \OutOfBoundsException("API handler does not exist");
		}

		$classname = self::$_api_handler_names[$name];
		self::$_api_handlers[$name] = new $classname();

		return self::$_api_handlers[$name];
	}


	
	public static function getCacheDir()
	{
		return self::$_kernel->getCacheDir();
	}


	
	public static function getLogDir()
	{
		if (!self::$_kernel) return '';
		return self::$_kernel->getLogDir();
	}


	
	public static function getEnvironment()
	{
		return self::$_environment;
	}


	
	public static function isDebug()
	{
		return self::$_debug;
	}


	
	public static function isCli()
	{
		static $is_cli = null;

		if ($is_cli === null) {
			$is_cli = false;
			if (self::getKernel() instanceof \DeskPRO\Kernel\CliKernel) {
				$is_cli = true;
			}
		}

		return $is_cli;
	}

	public static function setSkipCache($val)
	{
		self::$_skip_caching = (bool)$val;
	}

	public static function isCacheSkipped()
	{
		return self::$_skip_caching;
	}

	public static function setUncachableResult()
	{
		self::$_uncachable = true;
	}

	public static function isUncachableResult()
	{
		return self::$_uncachable;
	}


	
	protected static function _loadConfig($name = null)
	{
		if ($name != self::DEFAULT_NAME) {
			$name = preg_replace('#[^a-zA-Z0-9\-_]#', '', $name);
			$filename = 'config.' . $name . '.php';
			$filepath = DP_ROOT . "/sys/config/$filename";

			require($filepath);
			if (!isset($CONFIG)) {
				throw new \UnexpectedValueException("$filename does not define \$CONFIG");
			}

			self::$_fileconfig[$name] = $CONFIG;
		} else {
			global $DP_CONFIG;
			$name = self::DEFAULT_NAME;
			self::$_fileconfig[$name] = $DP_CONFIG;
		}
	}


	
	public static function getConfigFromFile($name)
	{
		if (!$name OR $name != self::DEFAULT_NAME) {
			$name = preg_replace('#[^a-zA-Z0-9\-_]#', '', $name);
			$filename = 'config.' . $name . '.php';
		} else {
			$filename = 'config.php';
		}

		$filepath = DP_ROOT . "/sys/config/$filename";

		if (!file_exists($filepath)) {
			throw new \RuntimeException("$filename does not exist");
		}

		require($filepath);
		if (!isset($CONFIG)) {
			throw new \UnexpectedValueException("$filename does not define \$CONFIG");
		}

		return $CONFIG;
	}


	
	public static function getConfig($config_name, $default = null, $file_name = self::DEFAULT_NAME)
	{
		if ($config_name == 'enable_twitter' && !(isset($GLOBALS['DP_CONFIG']['enable_twitter']) && !$GLOBALS['DP_CONFIG']['enable_twitter']) && !defined('DPC_IS_CLOUD')) {
			return true;
		}

		if (!isset(self::$_fileconfig[$file_name])) {
			self::_loadConfig($file_name);
		}

		$value = Arrays::getValue(self::$_fileconfig[$file_name], $config_name);
		if ($value === null) $value = $default;

		return $value;
	}


	
	public static function createNewLogger($log_name, $session_name)
	{
		if ($log_name == 'error_log') {
			$logger = new \Application\DeskPRO\Log\DbErrorLogger();
		} else {
			$logger = new \Application\DeskPRO\Log\Logger();
		}
		$logger->setLogName($log_name);
		$logger->setSessionName($session_name);

				$indent_filter = new \Orb\Log\Filter\IndentFilter();
		$logger->addFilter($indent_filter);

				if (strpos($log_name, 'worker_job') !== 0 || !defined('DP_DISABLE_DBCRONLOG')) {
			$writer = new \Application\DeskPRO\Log\Writer\LogItemEntity();
			$writer->addFilter(new \Orb\Log\Filter\PriorityFilter(\Orb\Log\Logger::INFO));
			$logger->addWriter($writer);
		}

		return $logger;
	}


	
	public static function logErrorMessage($type, $priority, $message, array $data = array())
	{
		$logger = self::createNewLogger('error_log.'.$type, null);
		$logger->log($message, $priority, $data);
	}


	
	public static function getApplicationBundleInfo()
	{
		return array(
			'AdminBundle' => array(
				'shortname' => 'admin',
				'bundle' => 'AdminBundle',
				'namespace' => 'Application\\AdminBundle',
				'path' => DP_ROOT . '/src/Application/AdminBundle'
			),
			'ApiBundle' => array(
				'shortname' => 'api',
				'bundle' => 'ApiBundle',
				'namespace' => 'Application\\ApiBundle',
				'path' => DP_ROOT . '/src/Application/ApiBundle'
			),
			'DeskPRO' => array(
				'shortname' => 'core',
				'bundle' => 'DeskPRO',
				'namespace' => 'Application\\DeskPRO',
				'path' => DP_ROOT . '/src/Application/DeskPRO'
			),
			'AgentBundle' => array(
				'shortname' => 'agent',
				'bundle' => 'AgentBundle',
				'namespace' => 'Application\\AgentBundle',
				'path' => DP_ROOT . '/src/Application/AgentBundle'
			),
			'UserBundle' => array(
				'shortname' => 'user',
				'bundle' => 'UserBundle',
				'namespace' => 'Application\\UserBundle',
				'path' => DP_ROOT . '/src/Application/UserBundle'
			),
		);
	}

	public static function getBundleFromShortname($shortname)
	{
		static $map = array(
			'admin' => 'AdminBundle',
			'api' => 'ApiBundle',
			'core' => 'DeskPRO',
			'agent' => 'AgentBundle',
			'user' => 'UserBundle'
		);

		return $map[$shortname];
	}
}
}
 




namespace Application\DeskPRO\DBAL
{

use DeskPRO\Kernel\KernelErrorHandler;
use PDO;
use Orb\Log\Logger;


class Connection extends \Doctrine\DBAL\Connection
{
	const EVENT_POST_COMMIT   = 'onPostCommit';
	const EVENT_POST_ROLLBACK = 'onPostRollback';

	
	protected $_max_packet_size = null;

	
	protected $transaction_logger = false;

	
	protected $running_trans_event = false;

	
	protected $names_charset = 'UTF8';

	
	protected $trans_ids = array();

	
	protected $trans_count = 0;

	
	protected $has_run_avoid = false;

	public function __construct(array $params, \Doctrine\DBAL\Driver $driver, \Doctrine\DBAL\Configuration $config = null, \Doctrine\Common\EventManager $eventManager = null)
	{
		if (!isset($params['driverOptions'])) {
			$params['driverOptions'] = array();
		}

		$params['driverOptions'][PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$params['driverOptions'][PDO::ATTR_EMULATE_PREPARES] = true;

		if (!isset($params['platform'])) {
			$params['platform'] = new \Application\DeskPRO\DBAL\Platforms\MySqlPlatform();
		}

		$m = null;
		if (isset($params['host']) && preg_match('#^(.*?):([0-9]+)$#', $params['host'], $m)) {
			$params['host'] = $m[1];
			$params['port'] = $m[2];
		}

		if (isset($params['names_charset'])) {
			$this->names_charset = $params['names_charset'];
			unset($params['names_charset']);
		}

		parent::__construct($params, $driver, $config, $eventManager);

		$db = $this;

		if (isset($GLOBALS['DP_CONFIG']['debug']['enable_transaction_log']) && $GLOBALS['DP_CONFIG']['debug']['enable_transaction_log']) {
			$this->transaction_logger = new Logger();
			$this->transaction_logger->addWriter(new \Orb\Log\Writer\Stream(dp_get_log_dir().'/db-transactions.log'));
			$this->transaction_logger->logDebug("--- BEGIN PAGE ---");
			$this->transaction_logger->logDebug("URL: " . $_SERVER['PHP_SELF']);
		}
	}

	public function connect()
	{
		if (parent::connect()) {
			$this->exec("SET sql_mode='', time_zone='+00:00'");

			if ($this->names_charset) {
				$this->exec("SET NAMES '{$this->names_charset}'");
			}

			return true;
		}

		return false;
	}


	
	public function avoidTimeout()
	{
		if (!$this->has_run_avoid) {
			try {
				$this->exec("SET SESSION wait_timeout = 1800");
			} catch (\Exception $e) {}
			$this->has_run_avoid = true;
		}

		try {
			$this->fetchColumn("SELECT 1");
		} catch (\Exception $e) {}
	}


	
	public function getMaxPacketSize()
	{
		if ($this->_max_packet_size !== null) return $this->_max_packet_size;

		$result = $this->fetchAssoc("SHOW variables LIKE 'max_allowed_packet'");
		$this->_max_packet_size = $result['Value'];

		return $this->_max_packet_size;
	}



	
	public function fetchAllKeyed($statement, array $params = array(), $index = 'id')
	{
		$statement = $this->executeQuery($statement, $params);
		$array = array();

		while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			$array[$row[$index]] = $row;
		}


		return $array;
	}



	
	public function fetchAllGrouped($statement, array $params = array(), $group_key, $index_key = null, $col_key = null)
	{
		$statement = $this->executeQuery($statement, $params);
		$array = array();

		while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			if (!isset($array[$row[$group_key]])) $array[$row[$group_key]] = array();

			$val = $row;
			if ($col_key !== null) {
				$val = $row[$col_key];
			}

			if ($index_key !== null) {
				$array[$row[$group_key]][$row[$index_key]] = $val;
			} else {
				$array[$row[$group_key]][] = $val;
			}
		}

		return $array;
	}



	
	public function fetchAllKeyValue($statement, array $params = array(), $key_index = 0, $val_index = 1, $mode = PDO::FETCH_NUM, $nullkey = 0)
	{
		$statement = $this->executeQuery($statement, $params);
		$array = array();

		while ($row = $statement->fetch($mode)) {
			if ($row[$key_index] === null) {
				$row[$key_index] = $nullkey;
			}

			$array[$row[$key_index]] = $row[$val_index];
		}

		return $array;
	}



	
	public function fetchAllCol($statement, array $params = array(), $index = 0, $mode = PDO::FETCH_NUM)
	{
		$statement = $this->executeQuery($statement, $params);
		$array = array();

		while ($row = $statement->fetch($mode)) {
			$array[] = $row[$index];
		}

		return $array;
	}


	
	public function batchInsert($table, array $multiple_values, $ignore = false)
	{
		$cols = null;
		$cols_count = 0;
		$params = array();

		$value_parts = array();
		$value_tpl = '';

		if (!$multiple_values) {
			throw new \InvalidArgumentException("No values");
		}

						
		foreach ($multiple_values as $vals) {
			if ($cols === null) {
				foreach (array_keys($vals) as $k) {
					$cols[] = $k;
				}
				$cols_count = count($cols);
				$value_tpl = '(' . implode(',', array_fill(0, $cols_count, '?')) . ')';
			}

			if (count($vals) != $cols_count) {
				throw new \InvalidArgumentException("A value row has more columns than it should");
			}

			foreach ($cols as $c) {
				if (!array_key_exists($c, $vals)) {
					throw new \InvalidArgumentException("A value row is missing the `$c` column");
				}

				$params[] = $vals[$c];
			}

			$value_parts[] = $value_tpl;
		}

						
		$sql = "INSERT " . ($ignore ? 'IGNORE' : '') . " INTO `$table` (`" . implode('`,`', $cols) ."`) VALUES " . implode(',', $value_parts);

		return $this->executeUpdate($sql, $params);
	}


	
	public function quoteIn(array $values, $type = null)
	{
		$quoted = array();

		foreach ($values as $val) {
			$quoted[] = $this->quote($val, $type);
		}

		$quoted = implode(',', $quoted);

		return $quoted;
	}


	
	public function replace($tableName, array $data, array $types = array())
	{
		$this->connect();

				$cols = array();
		$placeholders = array();

		foreach ($data as $columnName => $value) {
			$cols[] = $columnName;
			$placeholders[] = '?';
		}

		$query = 'REPLACE INTO ' . $tableName
			   . ' (' . implode(', ', $cols) . ')'
			   . ' VALUES (' . implode(', ', $placeholders) . ')';

		return $this->executeUpdate($query, array_values($data), $types);
	}


	
	public function count($tableName, $where = null)
	{
		$this->connect();

		$sql = "SELECT COUNT(*) FROM `$tableName`";

		$params = array();

		if ($where) {
			if (is_array($where)) {
				$placeholders = array();

				foreach ($where as $columnName => $value) {
					$params[] = $value;
					$placeholders[] = $columnName . ' = ?';
				}

				$sql .= " WHERE " . implode(" AND ", $placeholders);
			} else {
				$sql .= " WHERE $where";
			}
		}

		return $this->fetchColumn($sql, $params);
	}


	
	public function executeQuery($query, array $params = array(), $types = array(), \Doctrine\DBAL\Cache\QueryCacheProfile $qcp = null)
	{
		try {
			return parent::executeQuery($query, $params, $types, $qcp);
		} catch (\PDOException $e) {
			$e->_dp_query = $query;
			$e->_dp_query_params = $params;
			throw $e;
		}
	}


	
	public function executeUpdate($query, array $params = array(), array $types = array(), $is_retry = 0)
	{
		if (!$is_retry && isset($GLOBALS['DP_CONFIG']['debug']['log_delete_queries']) && $GLOBALS['DP_CONFIG']['debug']['log_delete_queries'] && preg_match('#^\s*DELETE|TRUNCATE|DROP#', $query)) {
			$this->_writeDeleteQuery($query, $params);
		}

		try {
			return parent::executeUpdate($query, $params, $types);
		} catch (\PDOException $e) {

			if ($is_retry <= 2 && stripos($e->getMessage(), 'deadlock') !== false) {
				usleep(500000);
				return $this->executeUpdate($query, $params, $types, $is_retry+1);
			}

			$e->_dp_query = $query;
			$e->_dp_query_params = $params;
			throw $e;
		}
	}

	private function _writeDeleteQuery($query, $query_params = null) {

				if (preg_match('#agent_activity|cache|chat_conversation_pings|client_messages|content_search|datastore|department_permissions|drafts|login_log|log_items|page_view_log|people_prefs|permissions|permissions_cache|queue_items|result_cache|searchlog|sendmail_queue|sendmail_queue_part|sessions|stat|stat_value|stat_value_group|ticket_access_codes|tickets_search|tmp_data|visitors#', $query)) {
			return;
		}

		if (!function_exists('dp_get_log_dir')) {
			return;
		}

		$params = array();
		if ($query_params && is_array($query_params)) {
			foreach ($query_params as $v) {
				if (is_numeric($v) || ctype_digit($v)) {
					$params[] = $v;
				} elseif (is_string($v)) {
					$v = str_replace(array("\r\n", "\n", "\t"), ' ', $v);
					$v = preg_replace('# {2,}#', ' ', $v);

					if (strlen($v) > 100) {
						$v = substr($v, 0, 100);
					}

					$params[] = 'string:' . $v;
				} elseif ($v === null) {
					$params[] = 'NULL';
				} elseif (is_array($v)) {
					$params[] = substr(\DeskPRO\Kernel\KernelErrorHandler::varToString($v), 0, 200);
				} elseif (is_object($v)) {
					$params[] = get_class($v);
				} else {
					$params[] = gettype($v);
				}
			}
		}

		$write = array();
		$write[] = "[" . date("Y-m-d H:i:s") . "]";

		if (defined('DP_REQUEST_URL')) {
			$write[] = "Page_Url: " . DP_REQUEST_URL;
			if (!empty($_SERVER['REQUEST_METHOD'])) {
				$writep[] = "Method: " . $_SERVER['REQUEST_METHOD'];
			}
		} elseif (defined('DP_INTERFACE') && DP_INTERFACE == 'cli') {
			$write[] = "Command: " . implode(' ', $_SERVER['argv']);
		} else {
			$write[] = "UnknownPage";
		}

		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$write[] = "IP: " . $_SERVER['REMOTE_ADDR'];
		}

		$write[] = "Query: " . $query;
		if ($params) {
			$write[] = "Params: " . implode($params);
		}

		$write = implode("\t", $write);
		$write .= "\n";

		@file_put_contents(dp_get_log_dir() . '/db_delete.log', $write, \FILE_APPEND);
	}

	
	public function deleteIn($table, array $ids, $field = 'id', $not = false)
	{
		if (!$ids) {
			return 0;
		}

		if ($not) {
			$not = ' NOT ';
		} else {
			$not = '';
		}

		return $this->executeUpdate("DELETE FROM `$table` WHERE `$field` $not IN (" . $this->quoteIn($ids) . ")");
	}


	
	public function updateIn($table, array $data, array $ids, $field = 'id', array $types = array())
	{
		if (!$ids) {
			return 0;
		}

        $set = array();
        foreach ($data as $columnName => $value) {
            $set[] = $columnName . ' = ?';
        }

        $params = array_values($data);

		$sql = "UPDATE `$table` SET " . implode(', ', $set) . " WHERE `$field` IN (" . $this->quoteIn($ids) . ")";

        return $this->executeUpdate($sql, $params, $types);
	}


	
	public function exec($statement)
	{
		try {
			return parent::exec($statement);
		} catch (\PDOException $e) {
			$e->_dp_query = is_string($statement) ? $statement : null;
			$e->_dp_query_params = array();
			throw $e;
		}
	}


	
	public function prepare($statement)
	{
		$this->connect();

		return new Statement($statement, $this);
	}

	public function beginTransaction()
	{
		parent::beginTransaction();
		if ($this->transaction_logger) {
			$e = new \Exception();
			$backtrace = \DeskPRO\Kernel\KernelErrorHandler::formatBacktrace($e->getTrace());
			$level = $this->getTransactionNestingLevel();
			$trans_id = \Orb\Util\Util::baseEncode($this->trans_count++, \Orb\Util\Strings::CHARS_ALPHA_IU);
			$this->trans_ids[] = $trans_id;
			$backtrace = \Orb\Util\Strings::modifyLines($backtrace, str_repeat("\t\t", $level) . "\t\t");
			$this->transaction_logger->logDebug("==> Level $level :: <$trans_id>\n" . str_repeat("\t\t", $level) . "TRANSACTION BEGIN\n$backtrace");
		}
	}

	public function commit()
	{
		$level = $this->getTransactionNestingLevel();
		parent::commit();

		if (!$this->running_trans_event && $this->_eventManager->hasListeners(self::EVENT_POST_COMMIT)) {
			$this->running_trans_event = true;
			$eventArgs = new Event\PostCommit($this);
			$this->_eventManager->dispatchEvent(self::EVENT_POST_COMMIT, $eventArgs);
			$this->running_trans_event = false;
		}

		if ($this->transaction_logger) {
			$e = new \Exception();
			$trans_id = array_pop($this->trans_ids);
			$backtrace = \DeskPRO\Kernel\KernelErrorHandler::formatBacktrace($e->getTrace());
			$backtrace = \Orb\Util\Strings::modifyLines($backtrace, str_repeat("\t\t", $level) . "\t\t");
			$this->transaction_logger->logDebug("<== Level $level :: <$trans_id>\n" . str_repeat("\t\t", $level) . "TRANSACTION COMMITTED\n$backtrace");
		}

		if (!$this->getTransactionNestingLevel()) {

												unset($GLOBALS['DP_HAS_UPDATED_SEARCH_TABLES']);

			\DpShutdown::run('db_done_trans');
			\DpShutdown::run('db_done_trans_commit');
		}
	}

	public function rollback()
	{
		try {
			parent::rollback();
		} catch (\Exception $e) {
			$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
			\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);
			return;
		}

										if (isset($GLOBALS['DP_HAS_UPDATED_SEARCH_TABLES'])) {
			unset($GLOBALS['DP_HAS_UPDATED_SEARCH_TABLES']);
			try {
				$this->executeUpdate("REPLACE INTO settings SET name = 'core.do_searchtables_refill', value = '1'");

				$e = new \RuntimeException("Rollback will result in corrupted search tables");
				KernelErrorHandler::logException($e, false);
			} catch (\Exception $e) {}
		}

		$level = $this->getTransactionNestingLevel();

		if (!$this->running_trans_event && $this->_eventManager->hasListeners(self::EVENT_POST_ROLLBACK)) {
			$this->running_trans_event = true;
			$eventArgs = new Event\PostCommit($this);
			$this->_eventManager->dispatchEvent(self::EVENT_POST_ROLLBACK, $eventArgs);
			$this->running_trans_event = false;
		}

		if ($this->transaction_logger) {
			$e = new \Exception();
			$backtrace = \DeskPRO\Kernel\KernelErrorHandler::formatBacktrace($e->getTrace());
			$backtrace = \Orb\Util\Strings::modifyLines($backtrace, str_repeat("\t", $level) . "\t");
			$this->transaction_logger->logDebug(str_repeat("\t", $level) . "TRANSACTION ROLLED BACK\n$backtrace");
		}

		if (!$level) {
			\DpShutdown::run('db_done_trans');
			\DpShutdown::run('db_done_trans_rollback');
		}
	}
}
}
 




namespace Application\DeskPRO\DBAL
{

use Application\DeskPRO\App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;


class ConnectionFactory extends \Symfony\Bundle\DoctrineBundle\ConnectionFactory implements ContainerAwareInterface
{
	
	protected $container = null;

	public function __construct(array $typesConfig)
	{
		parent::__construct($typesConfig);
		\Doctrine\DBAL\Types\Type::addType('dpblob', 'Application\\DeskPRO\\DBAL\\Types\\DpBlobType');
		\Doctrine\DBAL\Types\Type::addType('dpblob_file', 'Application\\DeskPRO\\DBAL\\Types\\DpBlobFileType');
		\Doctrine\DBAL\Types\Type::overrideType('array', 'Application\\DeskPRO\\DBAL\\Types\\DpArrayType');
	}

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function createConnection(array $params, Configuration $config = null, EventManager $eventManager = null, array $mappingTypes = array())
	{
		$params['wrapperClass'] = 'Application\\DeskPRO\\DBAL\\Connection';

		$host = $params['host'];
		$m = null;
		if (preg_match('#^from_user_config.(.*?)$#', $host, $m)) {
			$key = $m[1];
			unset($params['host']);

			$conf = App::getConfig($key);
			if (!$conf && defined('DP_BUILDING')) {
				$conf = array('bogus'); 			}

			if (!$conf) {
				throw new \Exception("Invalid database key $key");
			}
			$params = array_merge($params, $conf);
			if (empty($params['driver'])) {
				$params['driver'] = 'pdo_mysql';
			}
		}

						if (isset($GLOBALS['DP_DEFAULT_CONNECTION_PDO']) && $host === 'from_user_config.db') {
			$params['pdo'] = $GLOBALS['DP_DEFAULT_CONNECTION_PDO'];
		}

		
		$conn = parent::createConnection($params, $config, $eventManager, $mappingTypes);

		$evm = $conn->getEventManager();

		if ($this->container && $this->container->has('event_dispatcher')) {
			$evm->addEventSubscriber(new SymfonyEventConnector($this->container->get('event_dispatcher')));
		}

		$conn->getDatabasePlatform()->registerDoctrineTypeMapping('BLOB', 'dpblob');

		return $conn;
	}
}
}
 




namespace Application\DeskPRO\DBAL
{

use Symfony\Bundle\FrameworkBundle\ContainerAwareEventDispatcher;

use \Doctrine\ORM\Event\LifecycleEventArgs;
use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use \Doctrine\ORM\Event\PreUpdateEventArgs;
use \Doctrine\ORM\Event\OnFlushEventArgs;


class DoctrineEvent extends \Symfony\Component\EventDispatcher\Event
{
	
	protected $doctrine_event;

	
	protected $event_type;

	
	protected $entity;

	
	protected $entity_manager;

	public function __construct($event_type, $doctrine_event)
	{
		$this->event_type     = $event_type;
		$this->doctrine_event = $doctrine_event;
		$this->entity_manager = $doctrine_event->getEntityManager();
		$this->entity         = null;

		if ($doctrine_event instanceof LifecycleEventArgs OR $doctrine_event instanceof PreUpdateEventArgs) {
			$this->entity = $doctrine_event->getEntity();
		}
	}


	
	public function getEntity()
	{
		return $this->entity;
	}


	
	public function getEntityManager()
	{
		return $this->entity_manager;
	}


	
	public function getEventType()
	{
		return $this->event_type;
	}


	
	public function getDoctrineEvent()
	{
		return $this->doctrine_event;
	}
}
}
 




namespace Application\DeskPRO\DBAL
{

use Symfony\Bundle\FrameworkBundle\ContainerAwareEventDispatcher;

use \Doctrine\ORM\Event\LifecycleEventArgs;
use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use \Doctrine\ORM\Event\PreUpdateEventArgs;
use \Doctrine\ORM\Event\OnFlushEventArgs;


class SymfonyEventConnector implements \Doctrine\Common\EventSubscriber
{
	
	protected $event_dispatcher;

	
	public function __construct(ContainerAwareEventDispatcher $event_dispatcher)
	{
		$this->event_dispatcher = $event_dispatcher;
	}

	public function preRemove($event)
	{
		$event = new DoctrineEvent('preRemove', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPreRemove', $event);
	}

	public function postRemove($event)
	{
		$event = new DoctrineEvent('postRemove', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPostRemove', $event);
	}

	public function prePersist($event)
	{
		$event = new DoctrineEvent('prePersist', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPrePersist', $event);
	}

	public function postPersist($event)
	{
		$event = new DoctrineEvent('postPersist', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPostPersist', $event);
	}

	public function preUpdate($event)
	{
		$event = new DoctrineEvent('preUpdate', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPreUpdate', $event);
	}

	public function postUpdate($event)
	{
		$event = new DoctrineEvent('postUpdate', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPostUpdate', $event);
	}

	public function postLoad($event)
	{
		$event = new DoctrineEvent('postLoad', $event);
		$this->event_dispatcher->dispatch('Doctrine_onPostLoad', $event);
	}

	public function loadClassMetadata($event)
	{
		$event = new DoctrineEvent('loadClassMetadata', $event);
		$this->event_dispatcher->dispatch('Doctrine_onLoadClassMetadata', $event);
	}

	public function onFlush($event)
	{
		$event = new DoctrineEvent('onFlush', $event);
		$this->event_dispatcher->dispatch('Doctrine_onFlush', $event);
	}

	public function getSubscribedEvents()
	{
		return array(
			'preRemove',
			'postRemove',
			'prePersist',
			'postPersist',
			'preUpdate',
			'postUpdate',
			'postLoad',
			'loadClassMetadata',
			'onFlush'
		);
	}
}
}
 




namespace Application\DeskPRO\Domain
{

use Application\DeskPRO\App;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;

use Orb\Util\Util;


abstract class BasicDomainObject implements \ArrayAccess, NotifyPropertyChanged
{
	const TOARRAY_NOOP = 1;
	const TOARRAY_DEEP = 2;
	const TOARRAY_ONLY_PRIMATIVES = 4;
	const TOARRAY_LOAD_UNLOADED = 8;

	
	private $_listeners = array();

	
	private $_custom_callables = array();

	
	public $__dp_is_preloaded_repos = null;


	
	public function fromArray(array $values)
	{
		foreach ($values as $k => $v) {
			$this[$k] = $v;
		}
	}



	
	public function toArray($mode = self::TOARRAY_NOOP)
	{
		$values = array();

		$only_real = true;

		foreach ($this->getKeys() as $name) {

			if ($only_real) {
				if (!property_exists($this, $name)) {
					continue;
				}
			}

			$val = $this[$name];

			if (!($mode & self::TOARRAY_LOAD_UNLOADED)) {
								if (!is_scalar($val) AND !is_array($val) AND is_null($val) AND ($val instanceof \DateTime) AND !\Application\DeskPRO\ORM\Util\Util::isCollectionInitialized($val)) {
					continue;
				}
			}

			if ($mode & self::TOARRAY_NOOP) {

				$values[$name] = $val;

			} elseif ($mode & self::TOARRAY_ONLY_PRIMATIVES) {
				if (is_scalar($val) OR is_array($val) OR is_null($val)) {
					$values[$name] = $val;
				} elseif ($val instanceof \DateTime) {
					$values[$name] = $val->format('Y-m-d H:i:s');
				}

			} elseif ($mode & self::TOARRAY_DEEP) {
				if (is_object($val) AND method_exists($val, 'toArray')) {
										if ($this->$name instanceof DomainObject) {
						$val = $val->toArray($mode);
										} else {
						$val = $val->toArray();
					}
				}
				$values[$name] = $val;
			}
		}

		return $values;
	}



	
	public function getKeys()
	{
		return $this->getFieldKeys();
	}



	
	public function getFieldKeys()
	{
		$r = new \ReflectionObject($this);
		$props = $r->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED);

		$keys = array();
		foreach ($props as $prop) {
						if ($prop->name[0] === '_') continue;

			$keys[] = $prop->name;
		}

		return $keys;
	}



	
	public function propertyFieldExists($field)
	{
				if ($field[0] === '_') return false;

		try {
			$r = new \ReflectionObject($this);
			$prop = $r->getProperty($field);
		} catch (\ReflectionException $e) {
			return false;
		}

		if ($prop->isProtected() OR $prop->isPrivate()) {
			return true;
		}

		return false;
	}



	
	public function get($name)
	{
		return $this->offsetGet($name);
	}



	
	public function set($name, $value)
	{
		$this->offsetSet($name, $value);
	}



	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	public function __unset($name)
	{
		return $this->offsetUnset($name);
	}



	
	public function __call($name, $arguments)
	{
		$name_l = strtolower($name);
		if (isset($this->_custom_callables[$name_l])) {
			return call_user_func($this->_custom_callables[$name_l][0], $this->_custom_callables[$name_l][1], $arguments);
		}

		$orig_name = $name;
		$name = preg_replace('#([A-Z])#', '_$1', $name);

		$match = null;
		if (!preg_match('#^(get|set|is)_([a-zA-Z0-9_]+)$#', $name, $match)) {
			return $this->_onNotCallable($name, $arguments);
		}

		list(, $type, $prop) = $match;
		$prop = strtolower($prop);

				if ($type == 'is') {
			return $this["is_$prop"];
		} elseif ($type == 'get') {
			return $this[$prop];

				} else {
			if (!isset($arguments[0])) {
				$arguments = array(null);
			}

			if (isset($this->$prop)) {
				$old_val = $this->$prop;
			}

			$this[$prop] = $arguments[0];
		}
	}


	
	protected function _onNotCallable($name, $arguments)
	{
		if (isset($GLOBALS['DP_IS_RENDERING_TPL']) && $GLOBALS['DP_IS_RENDERING_TPL']) {
			return '[$name is not defined]';
		}
		throw new \BadMethodCallException("Method `$name` is undefined");
	}


			
	public function offsetExists($offset)
	{
		if (strpos($offset, 'is_') !== false) {
			$func = str_replace('_', '', $offset);
		} else {
			$func = "get" . str_replace('_', '', $offset);
		}
		if (method_exists($this, $func)) {
			return true;
		} elseif (property_exists($this, $offset) AND $offset[0] != '_') {
			return true;
		} else {
						if (substr($offset, -3) === '_id') {
				$func = substr($func, 0, -3);
				$offset = substr($offset, 0, -3);
			}

			if (method_exists($this, $func) || isset($this->_custom_callables['get'.strtolower($offset)])) {
				return true;
			} elseif (property_exists($this, $offset) AND $offset[0] != '_') {
				return true;
			}

			return false;
		}
	}



	public function offsetSet($offset, $value)
	{
		$old_value = isset($this[$offset]) ? $this[$offset] : null;

		$func = "set" . str_replace('_', '', $offset);
		if (method_exists($this, $func) || isset($this->_custom_callables[strtolower($func)])) {
			$this->$func($value);
		} else {
			$this->$offset = $value;
			$this->_onPropertyChanged($offset, $old_value, $value);
		}
	}



	public function offsetGet($offset)
	{
		if (strpos($offset, 'is_') !== false) {
			$func = str_replace('_', '', $offset);
		} else {
			$func = "get" . str_replace('_', '', $offset);
		}
		if (method_exists($this, $func) || isset($this->_custom_callables[strtolower($func)])) {
			return $this->$func();
		} elseif (property_exists($this, $offset) AND $offset[0] != '_') {
			return $this->$offset;
		} else {

						if (substr($offset, -3) === '_id') {
				$func = substr($func, 0, -3);
				$offset = substr($offset, 0, -3);
			}

			if (method_exists($this, $func)) {
				$obj = $this->$func();
				if ($obj) {
					return $obj['id'];
				} else {
					return 0;
				}
			} elseif (property_exists($this, $offset) AND $offset[0] != '_') {
				$obj = $this->$offset;
				if ($obj) {
					return $obj['id'];
				} else {
					return 0;
				}
			}

									return $this->$func();
		}
	}



	public function offsetUnset($offset)
	{
		$this->offsetSet($offset, null);
	}



	
    public function addPropertyChangedListener(PropertyChangedListener $listener)
	{
		if (empty($this->_listeners['property'])) $this->_listeners['property'] = array();

        $this->_listeners['property'][] = $listener;
    }


	
    public function removePropertyChangedListener(PropertyChangedListener $listener)
	{
		if (empty($this->_listeners['property'])) return;

		foreach ($this->_listeners['property'] as $k => $l) {
			if ($l == $listener) {
				unset($this->_listeners['property'][$k]);
				break;
			}
		}
    }

	
	public function addCustomCallable($name, $fn, $args = null)
	{
		$this->_custom_callables[$name] = array($fn, $args);
	}

	public function ensureDefaultPropertyChangedListener()
	{
		$uow = App::getOrm()->getUnitOfWork();

		if (!empty($this->_listeners['property'])) {
			foreach ($this->_listeners['property'] AS $listener) {
				if ($listener === $uow) {
					return false;
				}
			}
		}

		$this->addPropertyChangedListener($uow);
		return false;
	}

	public function __clone()
	{
		$this->_listeners = array();
	}


	
	protected function _onPropertyChanged($prop, $old, $new)
	{
        if (!empty($this->_listeners['property'])) {
            foreach ($this->_listeners['property'] as $listener) {
                $listener->propertyChanged($this, $prop, $old, $new);
            }
        }
    }

	public function __getPropValue__($k) { return $this->$k; }
	public function __setPropValue__($k, $v) { $this->$k = $v; }
	public function __hasRunLoad__() { return true; }
}
}
 




namespace Application\DeskPRO\Domain
{

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;


abstract class ChangeTracker implements \Doctrine\Common\PropertyChangedListener
{
	protected $entity;
	protected $changes = array();
	public $extra = array();

	public function __construct($entity)
	{
		$this->entity = $entity;
	}


	
	public function getEntity()
	{
		return $this->entity;
	}



	public function propertyChanged($sender, $prop, $old_val, $new_val)
	{
		$this->recordPropertyChanged($prop, $old_val, $new_val);
	}

	
	public function recordPropertyChanged($prop, $old_val, $new_val)
	{
				if (is_null($new_val) && is_null($old_val)) {
			return;
		} elseif (is_scalar($new_val)) {
			if ($new_val == $old_val) {
				return;
			}
		} elseif ($new_val instanceof \DateTime) {
			if ($old_val instanceof \DateTime && $new_val->getTimestamp() == $old_val->getTimestamp()) {
				return;
			}
		} elseif (is_object($new_val) && isset($new_val->id) && is_object($old_val)) {
			if ($new_val->id == $old_val->id) {
				return;
			}
		}

				if (isset($this->changes[$prop])) {
			$old_val = $this->changes[$prop]['old'];
		}

		$this->changes[$prop] = $this->getChangeData($prop, $old_val, $new_val);
	}



	
	public function recordMultiPropertyChanged($prop, $old_val, $new_val)
	{
		if (!isset($this->changes[$prop])) $this->changes[$prop] = array();

		$this->changes[$prop][] = $this->getChangeData($prop, $old_val, $new_val);
	}


	
	public function getChangeData($prop, $old_val, $new_val)
	{
		return array('old' => $old_val, 'new' => $new_val);
	}


	
	public function getChangedProperty($prop)
	{
		return isset($this->changes[$prop]) ? $this->changes[$prop] : null;
	}



	
	public function getAllChangedProperties()
	{
		return $this->changes;
	}



	
	public function getAllChangedPropertyNames()
	{
		return array_keys($this->changes);
	}



	
	public function isPropertyChanged($prop)
	{
		return isset($this->changes[$prop]);
	}



	
	public function recordExtra($key, $value)
	{
		$this->extra[$key] = $value;
	}


	
	public function recordExtraMulti($key, $value)
	{
		if (!isset($this->extra[$key])) {
			$this->extra[$key] = array();
		}

		$this->extra[$key][] = $value;
	}



	
	public function getExtra($key)
	{
		return isset($this->extra[$key]) ? $this->extra[$key] : null;
	}



	
	public function getAllExtra()
	{
		return $this->extra;
	}



	
	public function isExtraSet($key)
	{
		return isset($this->extra[$key]);
	}



	
	abstract public function done();
}
}
 




namespace Application\DeskPRO\Domain
{

use Application\DeskPRO\App;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;

use Orb\Util\Util;


abstract class DomainObject extends BasicDomainObject
{
	const API_MODE_OPT_OUT = 1;
	const API_MODE_OPT_IN = 2;

	protected $_api_mode = self::API_MODE_OPT_OUT;

	
	private $_no_persist = false;

	public $_presave_state = array();


	
	public static function getRepository()
	{
		$entity = get_called_class();
		$entity = explode('\\', $entity);
		$entity = array_pop($entity);

		$em = App::getOrm();

		return $em->getRepository("DeskPRO:$entity");
	}


	
	public static function getTableName()
	{
		return App::getOrm()->getClassMetadata(get_called_class())->getTableName();
	}


	
	public static function getEntityName()
	{
		$name = Util::getBaseClassname(get_called_class());
		if (preg_match('#^ApplicationDeskPROEntity(.*?)Proxy$#', $name, $m)) {
			$name = $m[1];
		}

		$name = 'DeskPRO:' . $name;

		return $name;
	}


	
	public function getObjectRef()
	{
		if (method_exists($this, 'getId')) {
			return $this->getTableName() . '.' . $this->getId();
		} elseif (method_exists($this, 'getRef')) {
			return $this->getTableName() . '.' . $this->getRef();
		} else {
			throw new \RuntimeException("Object does not implement getObjectRef");
		}
	}


	
	protected function setModelField($field, $value)
	{
		$old = $this->$field;

				if (is_null($value) && is_null($old)) {
			return;
		} elseif (is_scalar($value)) {
			if ($value == $old) {
				return;
			}
		} elseif ($value instanceof \DateTime) {
			if ($old instanceof \DateTime && $value->getTimestamp() == $old->getTimestamp()) {
				return;
			}
		} elseif (is_object($value) && isset($value->id) && is_object($old) && isset($old->id)) {
			if ($value->id == $old->id) {
				return;
			}
		}

		$this->$field = $value;

		$this->_onPropertyChanged($field, $old, $value);
	}


	
	public function setUntrackedModelField($field, $value)
	{
		$this->$field = $value;
	}


	
	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$repository = static::getRepository();
		if (!method_exists($repository, 'getFieldMappings')) {
			return array();
		}

		$values = array();
		$visited[] = $this;

		foreach ($repository->getFieldMappings() AS $name => $field) {
			if ($this->_api_mode == self::API_MODE_OPT_IN && empty($field['dpApi'])) {
				continue;
			} elseif ($this->_api_mode == self::API_MODE_OPT_OUT && isset($field['dpApi']) && !$field['dpApi']) {
				continue;
			}

			if (!empty($field['dpApiPrimary']) && !$primary) {
				continue;
			}

			$val = $this[$name];

			if ($val instanceof \DateTime) {
				$values[$name] = $val->format('Y-m-d H:i:s');
				$values["{$name}_ts"] = $val->getTimestamp();
				$values["{$name}_ts_ms"] = $val->getTimestamp() * 1000;
			} else {
				$values[$name] = $val;
			}
		}

		if ($deep) {
			foreach ($repository->getAssociationMappings() AS $name => $association) {
				if (empty($association['dpApi'])) {
					continue;
				}

				if (!empty($association['dpApiPrimary']) && !$primary) {
					continue;
				}

				$val = $this[$name];

				$subDeep = !empty($association['dpApiDeep']);
				if (in_array($val, $visited)) {
					$subDeep = false;
				}

				if ($val instanceof DomainObject) {
					$values[$name] = $val->toApiData(false, $subDeep, $visited);
				} else if (is_array($val) || $val instanceof \Traversable) {
					$output = array();

					foreach ($val AS $key => $sub) {
						if ($sub instanceof \Application\DeskPRO\Domain\DomainObject) {
							$output[$key] = $sub->toApiData(false, $subDeep, $visited);
						}
					}

					$values[$name] = $output;
				}
			}
		}

		return $values;
	}


	
	public function getScalarData()
	{
		$repository = static::getRepository();
		if (!method_exists($repository, 'getFieldMappings')) {
			return array();
		}

		$values = array();

		foreach ($repository->getFieldMappings() AS $name => $field) {
			$val = $this[$name];

			if ($val instanceof \DateTime) {
				$values[$name] = $val->format('Y-m-d H:i:s');
			} else if (is_array($val)) {
				$values[$name] = serialize($val);
			} else {
				$values[$name] = $val;
			}
		}

		return $values;
	}


	
	public function _setNoPersist()
	{
		$this->_no_persist = true;
	}


	
	public function _isNoPersist()
	{
		return $this->_no_persist;
	}


	
	public function __toString()
	{
		$me = get_class($this);
		$me = explode('\\', $me);
		$me = array_pop($me);

		if (property_exists($this, 'id')) {
			if ($this->id) {
				return "<$me:#" . $this->id . ">";
			} else {
				return "<$me:#0:" . spl_object_hash($this) . ">";
			}
		} else {
			return "<$me:" . spl_object_hash($this) . ">";
		}
	}
}
}
 




namespace Application\DeskPRO\HttpFoundation
{

use Symfony\Component\HttpFoundation\Cookie as BaseCookie;

use Application\DeskPRO\App;

class Cookie extends BaseCookie
{
	const EXPIRE_NEVER = 'never';
	const EXPIRE_DELETE = 'delete';

	public static function makeDeleteCookie($name)
	{
		return new self($name, '', 'delete');
	}

	public static function makeCookie($name, $value, $expire, $httpOnly = false, $secure = false)
	{
		return new self($name, $value, $expire, null, null, $secure, $httpOnly);
	}

	public function __construct($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = false)
    {
		if ($path === null) {
			$path = App::getSetting('core.cookie_path');
			if (!$path) {
				$path = '/';
			}
		}

		if ($domain === null) {
			$domain = App::getSetting('core.cookie_domain');
			if (!$domain) {
				$domain = null;
			}
		}

		if ($expire === self::EXPIRE_NEVER) {
			$expire = '+5 years';
		} elseif ($expire === self::EXPIRE_DELETE) {
			$expire = '-1 week';
		}

	    parent::__construct($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}

	public function __toString()
	{
		$str = urlencode($this->getName()).'=';

		if ('' === (string) $this->getValue()) {
			$str .= 'deleted; expires='.gmdate("D, d-M-Y H:i:s T", time() - 31536001);
		} else {
			$str .= urlencode($this->getValue());

			if ($this->getExpiresTime() !== 0) {
				$str .= '; expires='.gmdate("D, d-M-Y H:i:s T", $this->getExpiresTime());
			}
		}

		if (null !== $this->path) {
			$str .= '; path='.$this->path;
		}

		if (null !== $this->getDomain()) {
			$str .= '; domain='.$this->getDomain();
		}

		if (true === $this->isSecure()) {
			$str .= '; secure';
		}

		if (true === $this->isHttpOnly()) {
			$str .= '; httponly';
		}

		return $str;
	}

	public function setDomain($domain)
	{
		$this->domain = $domain;
		return $this;
	}

	public function setExpire($expire)
	{
		$this->expire = $expire;
		return $this;
	}

	public function setHttpOnly($httpOnly)
	{
		$this->httpOnly = $httpOnly;
		return $this;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	public function setSecure($secure)
	{
		$this->secure = $secure;
		return $this;
	}

	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	public function send()
	{
		header('Set-Cookie: ' . $this->__toString(), false);
	}
}
}
 




namespace Application\DeskPRO\HttpFoundation
{

use Symfony\Component\HttpFoundation\SessionStorage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Request extends \Symfony\Component\HttpFoundation\Request
{
	const PARTIAL_REQUEST_KEY = '_partial';

	protected $url_locale = null;

	
	public function isPartialRequest()
	{
		$val = false;

		if ($this->query->has(self::PARTIAL_REQUEST_KEY)) {
			$val = $this->query->get(self::PARTIAL_REQUEST_KEY);
			if (!$val) $val = 'partial';
		} elseif ($this->request->has(self::PARTIAL_REQUEST_KEY)) {
			$val = $this->request->get(self::PARTIAL_REQUEST_KEY);
			if (!$val) $val = 'partial';
		}

		return $val;
	}

	public function isPost()
	{
		return $this->getMethod() == 'POST';
	}

	public function isGet()
	{
		return $this->getMethod() == 'GET';
	}

	
	public function getUrlLocale()
	{
		if ($this->url_locale !== null) return $this->url_locale;

		$this->url_locale = false;

						
		$nocheck_sections = array(
			'/agent',
			'/admin',
			'/dev',
			'/api'
		);

		$check_for_locale = true;
		foreach ($nocheck_sections as $s) {
			if (strpos($pathinfo, $s) === 0) {
				$check_for_locale = false;
			}
		}

		if ($check_for_locale) {
			$locale = Strings::extractRegexMatch('#^/([a-z]{2})/#', $pathinfo, 1);
			if ($locale) {
				$locale = Strings::extractRegexMatch('#^/([a-z]{2}_[A-Z]{2}/#', $pathinfo, 1);
			}

			if ($locale) {
				$this->url_locale = $locale;
			}
		}

		$this->attributes->set('_locale', $this->url_locale);

		return $this->url_locale;
	}

	
	protected function prepareBaseUrl()
    {
				if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
			return parent::prepareBaseUrl();
		}

				        $filename = strtolower(basename($this->server->get('SCRIPT_FILENAME')));

        if (strtolower(basename($this->server->get('SCRIPT_NAME'))) === $filename) {
            $baseUrl = $this->server->get('SCRIPT_NAME');
        } elseif (strtolower(basename($this->server->get('PHP_SELF'))) === $filename) {
            $baseUrl = $this->server->get('PHP_SELF');
        } elseif (strtolower(basename($this->server->get('ORIG_SCRIPT_NAME'))) === $filename) {
            $baseUrl = $this->server->get('ORIG_SCRIPT_NAME');         } else {
                                    $path    = $this->server->get('PHP_SELF', '');
            $file    = $this->server->get('SCRIPT_FILENAME', '');
            $segs    = explode('/', trim($file, '/'));
            $segs    = array_reverse($segs);
            $index   = 0;
            $last    = count($segs);
            $baseUrl = '';
            do {
                $seg     = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while (($last > $index) && (false !== ($pos = stripos($path, $baseUrl))) && (0 != $pos));
        }

                $requestUri = $this->getRequestUri();

        if ($baseUrl && 0 === strpos($requestUri, $baseUrl)) {
                        return $baseUrl;
        }

        if ($baseUrl && 0 === stripos($requestUri, dirname($baseUrl))) {
                        return rtrim(dirname($baseUrl), '/');
        }

        $truncatedRequestUri = $requestUri;
        if (($pos = strpos($requestUri, '?')) !== false) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);
        if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
                        return '';
        }

                                if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return rtrim($baseUrl, '/');
    }
}
}
 




namespace Application\DeskPRO\ORM\Util
{

use Doctrine\ORM\PersistentCollection;
use Application\DeskPRO\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Application\DeskPRO\App;


class Util
{
	private function __construct() {  }



	
	public static function isCollectionInitialized($collection)
	{
		if ($collection instanceof PersistentCollection AND $collection->isInitialized()) {
			return true;
		}

		return false;
	}


	
	public static function getUpdateSchemaSql(EntityManager $em = null)
	{
		if ($em === null) {
			$em = App::getOrm();
		}

		$metadata = $em->getMetadataFactory()->getAllMetadata();
		$tool = new SchemaTool($em);

		$arr = $tool->getUpdateSchemaSql($metadata, true);
		$lines = array();
		foreach ($arr as $a) {
						if ($a != 'ALTER TABLE email_uids CHANGE id id VARCHAR(100) NOT NULL') {
				if (strpos($a, 'CREATE TABLE') !== false) {
					$a .= ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
				}

				$lines[] = $a;
			}
		}

		return $lines;
	}
}
}
 




namespace Application\DeskPRO\ORM
{

class CollectionHelper
{
	protected $entity;
	protected $prop;

	public function __construct($entity, $prop)
	{
		$this->entity = $entity;
		$this->prop = $prop;
	}

	
	public function getAddRemoveForSet(array $set)
	{
		$prop = $this->prop;

		$have_ids = array();
		$want_ids = array();

		foreach ($this->entity->$prop as $item) {
			$have_ids[] = $item->id;
		}

		foreach ($set as $item) {
			$want_ids[] = $item->id;
		}

		$add_ids = array_diff($want_ids, $have_ids);
		$del_ids = array_diff($have_ids, $want_ids);

		return array(
			'add' => $add_ids,
			'del' => $del_ids,
		);
	}


	
	public function setCollection(array $set)
	{
		$prop = $this->prop;

		$info = $this->getAddRemoveForSet($set);
		$add_ids = $info['add'];
		$del_ids = $info['del'];

		foreach ($del_ids as $id) {
			foreach ($this->entity->$prop as $k => $item) {
				if ($item->id == $id) {
					$this->entity->$prop->remove($k);
					break;
				}
			}
		}

		foreach ($set as $item) {
			if (in_array($item->id, $add_ids)) {
				$this->entity->$prop->add($item);
			}
		}
	}
}
}
 




namespace Application\DeskPRO\ORM
{

class QueryPartial
{
	protected $order_by = null;
	protected $order_dir = 'ASC';
	protected $first_result = null;
	protected $max_results = null;

	public function __construct() {}

	public function setOrderBy($order_by, $order_dir)
	{
		$this->order_by = $order_by;
		$this->order_dir = $order_dir;
		return $this;
	}

	public function setFirstResult($first_result)
	{
		$this->first_result = $first_result;
		return $this;
	}

	public function setMaxResults($max_results)
	{
		$this->max_results = $max_results;
		return $this;
	}

	public function getOrderBy()
	{
		return array($this->order_by, $this->order_dir);
	}

	public function getFirstResult()
	{
		return $this->first_result;
	}

	public function getMaxResults()
	{
		return $this->max_results;
	}

	public function applyToQuery(\Doctrine\ORM\Query $query)
	{
		if ($this->max_results) {
			$query->setMaxResults($this->max_results);
		}

		if ($this->first_result) {
			$query->setFirstResult($this->first_result);
		}
	}

	public function applyToQueryBuilder(\Doctrine\ORM\QueryBuilder $qb)
	{
		if ($this->max_results) {
			$qb->setMaxResults($this->max_results);
		}

		if ($this->first_result) {
			$qb->setFirstResult($this->first_result);
		}

		if ($this->order_by) {
			$qb->orderBy($this->order_by, $this->order_dir);
		}
	}
}
}
 




namespace Application\DeskPRO\Settings
{

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Arrays;


class Settings implements \ArrayAccess
{
	
	protected $settings_paths = array();

	
	protected $default_settings = array();

	
	protected $db;


	
	protected $settings = array();

	
	protected $default_timezone;


	
	protected $_pending_groups = array();


	
	protected $_loaded_groups = array();

	
	protected $_has_loaded_db = false;

	
	protected $virtual_settings = array();



	public function __construct(array $settings_paths, \Application\DeskPRO\DBAL\Connection $db = null)
	{
		$this->settings_paths = new SettingsLocator($settings_paths);
		$this->db = $db;

		$this->virtual_settings['core.interact_require_login'] = function($settings) {
			return in_array($settings->get('core.user_mode'), array('require_reg', 'require_reg_agent_validation', 'closed'));
		};

		$this->virtual_settings['default_timezone'] = function($settings) {
			return $settings->getDefaultTimezone();
		};

		$this->virtual_settings['tickets_enable_like_search'] = function($settings) {
			if ($settings['core_tickets.enable_like_search_mode'] == 'auto') {
				if ($settings['core_tickets.enable_like_search_auto']) {
					return true;
				} else {
					return false;
				}
			} else {
				if ($settings['core_tickets.enable_like_search_mode'] && $settings['core_tickets.enable_like_search_mode'] != 'off') {
					return true;
				} else {
					return false;
				}
			}
		};
	}


	
	public function getSettingsLocator()
	{
		return $this->settings_paths;
	}


	
	public function get($name)
	{
		if (!$name) return '';

		if (!isset($this->settings[$name])) {

			if (isset($this->virtual_settings[$name])) {
				return call_user_func($this->virtual_settings[$name], $this, $name);
			}

			$check_group = $this->getGroupFromName($name);

			if (!in_array($check_group, $this->_loaded_groups)) {
				$this->_pending_groups[] = $check_group;
				$this->_loadPendingGroups();
				if (!isset($this->settings[$name])) {
					return null;
				}
				return $this->settings[$name];
			}

			return null;
		}

		return $this->settings[$name];
	}


	
	public function getDefault($name)
	{
		$group = $this->getGroupFromName($name);
		$this->getDefaultGroup($group);

		if (isset($this->default_settings[$group][$name])) {
			return $this->default_settings[$group][$name];
		}

		return null;
	}


	
	public function getDefaultGroup($group)
	{
		if (!isset($this->default_settings[$group])) {
			$group_file = $this->getGroupFile($group);

			if ($group_file) {
				$group_settings = require($group_file);
			} else {
				$group_settings = array();
			}

			$this->default_settings[$group] = (array)$group_settings;
		}

		return $this->default_settings[$group];
	}


	
	public function getGroup($group)
	{
		if (!in_array($group, $this->_loaded_groups)) {
			$this->_pending_groups[] = $group;
			$this->_loadPendingGroups();
		}

		$ret = array();

		$off = strlen($group) + 1;
		foreach ($this->settings as $k => $v) {
			if (strpos($k, $group) === 0) {
				$new_k = substr($k, $off);
				$ret[$new_k] = $v;
			}
		}

		return $ret;
	}



	
	public function setTemporarySettingValues(array $settings)
	{
		$this->settings = array_merge($this->settings, $settings);
	}


	
	public function setSetting($setting, $value)
	{
		$this->db->beginTransaction();
		try {

			if ($value !== null) {
				$this->db->executeUpdate("
					INSERT INTO settings
						(name, value)
					VALUES
						(?, ?)
					ON DUPLICATE KEY UPDATE
						value = VALUES(value)
				", array($setting, $value));
			} else {
				$this->db->delete('settings', array('name' => $setting));
			}

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		$this->settings[$setting] = $value;
	}



	
	public function loadGroups($group)
	{
		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$group = func_get_arg($i);
			if (!in_array($group, $this->_loaded_groups)) {
				$this->pending_load[] = $group;
			}
		}
	}


	
	public function getGroupFile($group)
	{
		if (strpos($group, '_') !== false) {
			list($key, $name) = Strings::rexplode('_', $group, 2);
		} else {
			$key = $group;
			$name = $group;
		}

				if (!isset($this->settings_paths[$key])) {
			trigger_error("Unknown settings group `$group`", \E_USER_WARNING);
			return null;
		}

		$path = $this->settings_paths[$key] . '/' . $name . '.php';

		return $path;
	}


	
	protected function _loadPendingGroups()
	{
		if (!$this->_pending_groups) {
			return;
		}

		$this->_pending_groups = array_unique($this->_pending_groups);
		$this->_pending_groups = Arrays::removeFalsey($this->_pending_groups);

						
		foreach ($this->_pending_groups as $group) {
			$path = $this->getGroupFile($group);
			if (!$path || !file_exists($path)) continue;

			$group_settings = require($path);
			$this->settings = array_merge($group_settings, $this->settings);
		}

		unset($group_settings);

						
		if (!$this->_has_loaded_db) {

			$this->_has_loaded_db = true;

			$db_settings = $this->db->fetchAllKeyValue("
				SELECT name, value
				FROM settings
			");

			$this->settings = array_merge($this->settings, $db_settings);

			if (!empty($GLOBALS['DP_CONFIG']['SETTINGS']) && is_array($GLOBALS['DP_CONFIG']['SETTINGS'])) {
				$this->settings = array_merge($this->settings, $GLOBALS['DP_CONFIG']['SETTINGS']);
			}
		}

		$this->_loaded_groups = array_merge($this->_loaded_groups, $this->_pending_groups);
		$this->_pending_groups = array();
	}



	
	public function getGroupFromName($name)
	{
		$pos = strpos($name, '.');
		if ($pos === false) {
			return false;
		}

		return substr($name, 0, $pos);
	}


	
	public function getDefaultTimezone()
	{
		if ($this->default_timezone !== null) {
			return $this->default_timezone;
		}

		try {
			$this->default_timezone = new \DateTimeZone($this->get('core.default_timezone'));
		} catch (\Exception $e) {
			$this->default_timezone = new \DateTimeZone('UTC');
		}

		return $this->default_timezone;
	}


	public function offsetExists($offset)
	{
		return $this->get($offset) !== null;
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException('You cannot set settings');
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException('You cannot unset settings');
	}
}
}
 




namespace Application\DeskPRO\Settings
{

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Arrays;


class SettingsLocator implements \ArrayAccess
{
	protected $settings_paths = array();
	protected $failed_paths = array();

	
	public function __construct(array $settings_paths = array())
	{
		$this->settings_paths = $settings_paths;
	}


	
	public function initPath($key)
	{
				if (isset($this->settings_paths[$key]) OR isset($this->failed_paths[$key])) {
			return;
		}

		$plugin_manager = App::get('deskpro.plugin_manager');
		if ($plugin_manager->hasPlugin($key)) {
			$this->settings_paths[$key] = $plugin_manager->getResourcesPath($key) . '/settings';
		} else {
			$this->failed_paths[$key] = true;
		}
	}


	public function offsetExists($offset)
	{
		$this->initPath($offset);
		return isset($this->settings_paths);
	}

	public function offsetGet($offset)
	{
		$this->initPath($offset);
		return $this->settings_paths[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->settings_paths[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->settings_paths[$offset]);
	}
}
}
 




namespace Application\DeskPRO\Templating\Asset
{

use Symfony\Component\Templating\Asset\UrlPackage as BaseUrlPackage;

use Application\DeskPRO\App;

class UrlPackage extends BaseUrlPackage
{
	public function __construct($baseUrls = array(), $version = null, $format = null)
    {
		$real = array();
		foreach ((array)$baseUrls as $burl) {
			if (!$burl OR $burl == 'CONFIG_HTTP' OR $burl == 'CONFIG_SSL') {
				$type = $burl;
				$burl = false;
				if (!$type) {
					$type = 'CONFIG_HTTP';
				}

				if ($type == 'CONFIG_SSL') {
					$burl = App::getConfig('static_ssl_path');
				}

				if (!$burl) {
					$burl = App::getConfig('static_path');
				}
			}

			if (!$burl AND App::has('request')) {
				$request = App::get('request');
				$burl = $request->getBasePath() . '/web';
			}

			if ($burl) {
				$real[] = $burl;
			}
		}

        parent::__construct($real, $version, $format);
    }
}
}
 




namespace Application\DeskPRO\Templating
{

use Application\DeskPRO\App;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as BaseGlobalVariables;

class GlobalVariables extends BaseGlobalVariables
{
	protected $variables = array();

	public function setVariable($name, $value)
	{
		$this->variables[$name] = $value;
	}

	public function getLicense()
	{
		return \DeskPRO\Kernel\License::getLicense();
	}

	public function getVariable($name)
	{
		return isset($this->variables[$name]) ? $this->variables[$name] : null;
	}

	public function getUser()
	{
		return App::getCurrentPerson();
	}

	public function getSetting($name)
	{
		return App::getSetting($name);
	}

	public function getSettingGroup($group)
	{
		$group_vars = App::get('deskpro.core.settings')->getGroup($group);

		if ($group == 'user_style') {
			if (defined('DPC_IS_CLOUD')) {
								$group_vars['static_path'] = 'https://' . DPC_SITE_DOMAIN . '/web';
			} else {
								if (!App::getConfig('static_path') && App::getContainer()->getBlobStorage()->getPreferredAdapterId() == 's3') {
					$url = App::getSetting('core.deskpro_url');
					$url = str_replace('index.php', '', $url);
					$url = trim($url, '/');

					$group_vars['static_path'] = $url . '/web';
				} else {
										if (App::getConfig('static_path')) {
						$group_vars['static_path'] = rtrim(App::getConfig('static_path'), '/');

										} else {
						$group_vars['static_path'] = rtrim('../..' . (App::getConfig('static_path') ?: '/web/'), '/');
					}
				}
			}
		}

		return $group_vars;
	}

	public function getConfig($name, $default = null)
	{
		return App::getConfig($name, $default);
	}

	public function getSession()
	{
		return App::getSession();
	}

	public function getVisitor()
	{
		return App::getSession()->getVisitor();
	}

	public function getLanguage()
	{
		return App::getLanguage();
	}

	public function isDebug()
	{
		return App::isDebug();
	}

	public function getStyle()
	{
		return App::getSystemService('style');
	}

	public function getLogoBlob()
	{
		return App::getSystemService('logo_blob');
	}

	public function getUsersourceManager()
	{
		return App::getSystemService('UsersourceManager');
	}

	public function getTicketFieldManager()
	{
		return App::getSystemService('TicketFieldsManager');
	}

	public function getPersonFieldManager()
	{
		return App::getSystemService('PersonFieldsManager');
	}

	public function getOrgFieldManager()
	{
		return App::getSystemService('OrgFieldsManager');
	}

	
	public function getDataRepository($ent)
	{
		return App::getSystemService("{$ent}Data");
	}

	public function getDataService($ent)
	{
		return App::getDataService($ent);
	}

	public function getDepartments()
	{
		return App::getDataService('Department');
	}

	public function getAgents()
	{
		return App::getDataService('Agent');
	}

	public function agent_teams()
	{
		return App::getDataService('AgentTeam');
	}
	public function getAgentTeams()
	{
		return App::getDataService('AgentTeam');
	}

	public function getUsersources()
	{
		return App::getDataService('Usersource');
	}

	public function getUsergroups()
	{
		return App::getDataService('Usergroup');
	}

	public function getLanguages()
	{
		return App::getDataService('Language');
	}

	public function getProducts()
	{
		return App::getDataService('Product');
	}

	public function getCustomFieldManager($type)
	{
		switch ($type) {
			case 'tickets':
				return App::getSystemService('ticket_fields_manager');
			case 'people':
				return App::getSystemService('person_fields_manager');
		}

		return null;
	}

	public function getBrowserSniffer()
	{
		return App::get('browser_sniffer');
	}

	public function get($name)
	{
		return $this->__get($name);
	}

	public function __get($name)
	{
		if (isset($this->variables[$name])) {
			return $this->variables[$name];
		}

		if (method_exists($this, $name)) {
			return $this->$name;
		}
		if (method_exists($this, "get$name")) {
			return $this->{"get$name"};
		}

		if ($ent = \Orb\Util\Strings::extractRegexMatch('#^(.*?)Data$#', $name, 1)) {
			return App::getContainer()->getSystemService(ucfirst($ent) . 'Data');
		}

		return null;
	}

	public function __call($method, $args)
	{
		if ($var = \Orb\Util\Strings::extractRegexMatch('#^get(.*?)$#', $method, 1)) {
			return $this->__get(ucfirst($method));
		}

		return null;
	}

	public function __isset($name)
	{
		return isset($this->variables[$name]);
	}

	public function getLastException()
	{
		if (!App::has('deskpro.exception_logger')) {
			return null;
		}

		$logger = App::get('deskpro.exception_logger');
		return $logger->getLastException();
	}

	public function getTimezoneList()
	{
		static $tz = null;

		if ($tz === null) {
			$tz = array_combine(\DateTimeZone::listIdentifiers(), \DateTimeZone::listIdentifiers());
		}

		return $tz;
	}

	public function getReturnUrl()
	{
		$request = App::getRequest();

				if ($request->getMethod() == 'POST') {
			return App::getSetting('core.deskpro_url');
		}

		return $request->getRequestUri();
	}

	public function isCloud()
	{
		return defined('DPC_IS_CLOUD');
	}

	public function isPluginInstalled($id)
	{
		return App::getContainer()->getPlugins()->isPluginInstalled($id);
	}

	public function getPluginService($id)
	{
		return App::getContainer()->getPlugins()->getPluginService($id);
	}

	public function __toString()
	{
		return '[app]';
	}
}
}
 




namespace Application\DeskPRO\Translate\Loader
{


class BundleLoader implements LoaderInterface
{
	protected $bundle_paths;

	
	public function __construct(array $bundle_paths = array())
	{
		$this->bundle_paths = array();

		foreach ($bundle_paths as $name => $path) {

						$name = strtolower($name);
			$name = str_replace('bundle', '', $name);

			$this->bundle_paths[$name] = $path;
		}
	}



	public function load($groups, $language)
	{
				
		if (!is_array($groups)) $groups = array($groups);

		$phrases = array();

		foreach ($groups as $group) {

			$group = strtolower($group);

			$bundle_parts = explode('_', $group, 2);
			if (!isset($bundle_parts[1])) $bundle_parts[1] = $bundle_parts[0];

			list($bundle_name, $name) = $bundle_parts;

			if (!isset($this->bundle_paths[$bundle_name])) {
				continue;
			}

			$filepath = $this->bundle_paths[$bundle_name] . '/' . $name . '.php';

			if (!is_file($filepath)) {
				return array();
			}

			$phrases[$group] = include($filepath);
		}

		return $phrases;
	}
}
}
 




namespace Application\DeskPRO\Translate\Loader
{

use Orb\Util\Arrays;


class CombinationLoader implements LoaderInterface
{
	
	protected $loaders = array();

	
	protected $cache;

	
	protected $cache_prefix;



	
	public function setCache(\Zend\Cache\Frontend $cache, $cache_prefix = '')
	{
		if ($cache === null) {
			$this->cache = null;
			$this->cache_prefix = null;
			return;
		}

		$this->cache = $cache;
		$this->cache_prefix = $cache_prefix;
	}



	
	public function getCache()
	{
		return $this->cache;
	}



	
	public function getCachePrefix()
	{
		return $this->cache_prefix;
	}



	
	public function addLoader(LoaderInterface $loader)
	{
		$this->loaders[] = $loader;
	}



	
	public function load($groups, $language)
	{
						
		$cached_phrases = array();
		if ($this->cache) {

						
			foreach (array_keys($groups) as $k) {
				$loaded_cache_phrases = $this->_loadGroupFromCache($groups[$k]);
				if (is_array($loaded_cache_phrases)) {
					$cached_phrases = array_merge($cached_phrases, $loaded_cache_phrases);

															unset($groups[$k]);
				}
			}

			unset($loaded_cache_phrases);
		}

						
		$phrases = array();
		if ($groups) {
			$grouped_phrases = array();

			foreach ($this->loaders as $loader) {
				try {
					$loader_phrases = $loader->load($groups, $language);

															
					foreach ($loader_phrases as $group => $groupphrases) {
						if (!isset($grouped_phrases[$group])) {
							$grouped_phrases[$group] = $groupphrases;
						} else {
							$grouped_phrases[$group] = array_merge($grouped_phrases[$group], $groupphrases);
						}
					}
				} catch (Exception $e) {}
			}

						if ($this->cache) {
				$this->_saveGroupedPhrasesToCache($grouped_phrases);
			}

									$phrases = Arrays::mergeSubArrays($grouped_phrases);
		}

				if ($cached_phrases) {
			$phrases = array_merge($cached_phrases, $phrases);
		}

		return $phrases;
	}



	
	protected function _loadGroupFromCache($group)
	{
		$id = $this->cache_prefix . $group;

		$phrases = $this->cache->load($id);

		if (!is_array($phrases)) {
			return false;
		}

		return $phrases;
	}



	
	protected function _saveGroupedPhrasesToCache(array $grouped_phrases)
	{
		foreach ($grouped_phrases as $group => $phrases) {
			$id = $this->cache_prefix . $group;
			$this->cache->save($phrases, $id);
		}
	}



	
	public function invalidateCachedGroup($group)
	{
		if (!$this->cache) {
			throw new \RuntimeException('No cache object was set, cannot invalidate');
		}
		$id = $this->cache_prefix . $group;

		$this->cache->remove($id);
	}
}
}
 




namespace Application\DeskPRO\Translate\Loader
{


class DbLoader implements LoaderInterface
{
	
	protected $dbconn;

	
	protected $loaded_langs = array();

	
	public function __construct(\Application\DeskPRO\DBAL\Connection $dbconn)
	{
		$this->dbconn = $dbconn;
	}

	public function load($groups, $language)
	{
						if (!$language OR !$language['id']) {
			return array();
		}

								if (isset($this->loaded_langs[$language['id']])) {
			return $this->loaded_langs[$language['id']];
		}

		$this->loaded_langs[$language['id']] = array();

		$langs = array();
		$langs[] = 1; 
		if ($language) {
			$langs[] = $language->getId(); 		}

				$langs[] = '0';

		$specific_lang_ids = array(0);
		if ($language) {
			$specific_lang_ids[] = $language->getId();
		}
		$specific_lang_ids = implode(',', $specific_lang_ids);

		$langs = array_unique($langs, \SORT_STRING);

		$lang_in = implode(',', $langs);

				if (DP_INTERFACE == 'admin' || DP_INTERFACE == 'cron' || DP_INTERFACE == 'cli' || (defined('DP_BOOT_MODE') && DP_BOOT_MODE == 'dp')) {
			$sql = "
				SELECT name, phrase, original_phrase
				FROM phrases
				WHERE language_id IN ($lang_in)
				ORDER BY language_id ASC
			";
		} elseif (DP_INTERFACE == 'agent') {
			$sql = "
				SELECT name, phrase, original_phrase
				FROM phrases
				WHERE
					language_id IN ($lang_in) AND (
						groupname LIKE 'agent.%' OR groupname LIKE 'user.%' OR groupname LIKE \"obj_%\" OR groupname = \"custom\"
					)
				ORDER BY language_id ASC
			";
		} else {
			$sql = "
				SELECT name, phrase, original_phrase
				FROM phrases
				WHERE
					language_id IN ($lang_in) AND (
						groupname LIKE 'agent.%' OR groupname LIKE 'user.%' OR groupname LIKE \"obj_%\" OR groupname = \"custom\"
					)
				ORDER BY language_id ASC
			";
		}

		$q = $this->dbconn->query($sql);

		$phrases = array();
		while ($r = $q->fetch()) {

			if ($r['name'] == 'user.general.helpdesk_by') {
				continue;
			}

			$phrase_text = $r['phrase'];
			if (empty($phrase_text)) {
				$phrase_text = $r['original_phrase'];
			}

			$phrases[$r['name']] = $phrase_text;
		}

		$this->loaded_langs[$language['id']] = $phrases;

		return $phrases;
	}
}
}
 




namespace Application\DeskPRO\Translate\Loader
{


interface LoaderInterface
{
	
	public function load($groups, $language);
}
}
 




namespace Application\DeskPRO\Translate\Loader
{


class PluginLoader implements LoaderInterface
{
	protected $plugin_manager;

	public function __construct($plugin_manager)
	{
		$this->plugin_manager = $plugin_manager;
	}



	public function load($groups, $language)
	{
				
		if (!is_array($groups)) $groups = array($groups);

		$phrases = array();

		foreach ($groups as $group) {

			$group = strtolower($group);

			$name_parts = explode('_', $group, 2);
			if (!isset($name_parts[1])) $name_parts[1] = $name_parts[0];

			list($plugin_name, $name) = $name_parts;

			if (!$this->plugin_manager->hasPlugin($plugin_name)) {
				continue;
			}

			$filepath = $this->plugin_manager->getResourcesPath($plugin_name) . '/language/' . $name . '.php';

			if (!is_file($filepath)) {
				return array();
			}

			$phrases[$group] = include($filepath);
		}

		return $phrases;
	}
}
}
 




namespace Application\DeskPRO\Translate\Loader
{

use Orb\Util\Arrays;


class SystemLoader implements LoaderInterface
{
	
	protected $loaded_files = array();

	public function load($groups, $language)
	{
		$lang_packs = array();

				$lang_packs[] = DP_ROOT . '/languages/default';

		if ($language && $language->base_filepath) {
			$lang_packs[] = str_replace('%DP_ROOT%', DP_ROOT, $language->base_filepath);
		}

		$lang_packs = array_unique($lang_packs);
		$lang_packs = Arrays::removeFalsey($lang_packs);

		$phrases = array();

		foreach ($lang_packs as $path) {
			foreach ($groups as $group) {
				$group_parts = explode('.', $group, 2);

								if (count($group_parts) == 2) {
					$file = $path . '/' . $group_parts[0] . '/' . $group_parts[1] . '.php';
								} else {
					$file = $path . '/' . $group_parts[0] . '/' . $group_parts[0] . '.php';
				}

				$file_phrases = $this->loadFile($file);
				if ($file_phrases) {
					$phrases = array_merge($phrases, $file_phrases);
				}
			}
		}

		return $phrases;
	}

	
	public function loadFile($file)
	{
		if (isset($this->loaded_files[$file])) {
			return $this->loaded_files[$file];
		}

		if (is_file($file)) {
			$file_phrases = include($file);
			if ($file_phrases && is_array($file_phrases)) {
				$this->loaded_files[$file] = $file_phrases;
			}
		}

		if (!isset($this->loaded_files[$file])) {
			$this->loaded_files[$file] = array();
		}

		return $this->loaded_files[$file];
	}
}
}
 




namespace Application\DeskPRO\Translate
{

use Application\DeskPRO\Entity\Language;

class DelegatePhrase implements DelegatePhraseInterface
{
	protected $phrase_name;
	protected $phrase_vars = array();

	public function __construct($phrase_name, array $phrase_vars = array())
	{
		$this->phrase_name = $phrase_name;
		$this->phrase_vars = $phrase_vars;
	}


	
	public function getPhrase(Translate $translator, Language $language = null)
	{
		return $translator->phrase($this->phrase_name, $this->phrase_vars, $language);
	}


	
	public function getPhraseName()
	{
		return $this->phrase_name;
	}


	
	public function getPhraseVars()
	{
		return $this->phrase_vars;
	}


	public function __toString()
	{
		return $this->phrase_name;
	}
}
}
 




namespace Application\DeskPRO\Translate
{

use Application\DeskPRO\Translate\Translate;


interface DelegatePhraseInterface
{
	
	public function getPhrase(Translate $translator);
}
}
 




namespace Application\DeskPRO\Translate
{

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

use Application\DeskPRO\Translate\Translate;


class DephrasifyTemplate
{
	protected $translate;

	public function __construct(Translate $translate)
	{
		$this->translate = $translate;
	}


	
	public function expand($string)
	{
		$string = $this->expandSimplePhrases($string);
		$string = $this->expandVariablePhrases($string);

		return $string;
	}


	
	public function expandSubphrases($string)
	{
		$matches = null;
		if (!preg_match_all('#\{\{\s*phrase\.([a-zA-Z0-9\.\-_]+)\s\}\}#', $string, $matches, PREG_SET_ORDER)) {
			return $string;
		}

		foreach ($matches as $match) {
			$phrase = $match[1];

						
			$phrase_text = $this->translate->phrase($phrase);
			$string = str_replace($match[0], $phrase_text, $string);
		}

		return $string;
	}


	
	public function expandSimplePhrases($string)
	{
		$matches = null;
		if (!preg_match_all('#\{\{\s*phrase\((\'|\")([a-zA-Z0-9\.\-_]+)(\'|\")\)\s*\}\}#', $string, $matches, PREG_SET_ORDER)) {
			return $string;
		}

		foreach ($matches as $match) {
			$phrase = $match[2];
			$phrase_text = $this->translate->phrase($phrase);
			$phrase_text = $this->expandSubphrases($phrase_text);

			$string = str_replace($match[0], $phrase_text, $string);
		}

		return $string;
	}


	
	public function expandVariablePhrases($string)
	{
		$matches = null;
		if (!preg_match_all('#\{\{\s*phrase\((\'|\")([a-zA-Z0-9\.\-_]+)(\'|\")\s*,\s*\{(.*?)\}\s*\)\s*\}\}#', $string, $matches, PREG_SET_ORDER)) {
			return $string;
		}

		foreach ($matches as $match) {
			$line = $match[0];
			$phrase = $match[2];
			$hash_string = "{ " . $match[4] . " }";
			$phrase_text = $this->translate->phrase($phrase);
			$phrase_text = $this->expandSubphrases($phrase_text);

			$var_places = null;
			preg_match_all('#\{\{\s*([a-zA-Z0-9_]+)\s*\}\}#', $phrase_text, $var_places, PREG_SET_ORDER);

									if (!$phrase_text OR !$var_places) {
				$string = str_replace($match[0], $phrase_text, $string);
				continue;
			}

			$found_all = true;
			$find_replace = array();

			foreach ($var_places as $var) {
				$varname = $var[1];
				$val_expr = $this->_findVarInHashString($varname, $hash_string);
				if ($val_expr === false) {
					$found_all = false;
					break;
				}

				$find_replace[$var[0]] = $val_expr;
			}

			if (!$found_all) {
				continue;
			}

			foreach ($find_replace as $k => $v) {
				$phrase_text = str_replace($k, $v, $phrase_text);
			}

			$string = str_replace($line, $phrase_text, $string);
		}

		return $string;
	}


	
	protected function _findVarInHashString($varname, $hash_string)
	{
		$m = null;
		$varname_q = preg_quote($varname, '#');

		$key_string = Strings::extractRegexMatch("#(\'|\")$varname_q(\'|\")\s*:\s*#", $hash_string, 0);

				if (!$key_string) {
			return null;
		}

		$value_expr = null;
		if (!preg_match("#(\'|\")$varname_q(\'|\")\s*:\s*(((\'|\")(?P<quoted>.*?)(\'|\"))|((?P<expr>.*?)(\s|,|\})))#", $hash_string, $value_expr)) {
			return null;
		}

		if (isset($value_expr['quoted'])) {
			return $value_expr['quoted'];
		} else {
			return "{{ " . $value_expr['expr'] . " }}";
		}
	}
}
}
 




namespace Application\DeskPRO\Translate
{



interface HasPhraseName
{
	
	public function getPhraseName($property = null, Translate $translate);

	
	public function getPhraseDefault($property = null, Translate $translate);
}
}
 




namespace Application\DeskPRO\Translate
{

use Application\DeskPRO\App;

use Orb\Util\Util;


class ObjectPhraseNamer
{
	public function getPhraseName($object, $property = null)
	{
		$id = null;
		if (method_exists($object, 'getId')) {
			$id = $object->getId();
		} elseif ($object instanceof \ArrayAccess AND isset($object['id'])) {
			$id = $object['id'];
		}

		if ($id) {
			$baseclass = Util::getBaseClassname($object);
			$prefix = 'obj_' . strtolower($baseclass) . '.';
			$name = $prefix . $id;
			if ($property) {
				$name .= '_' . $property;
			}
			return $name;
		}

		return null;
	}

	public function getPhraseDefault($object, $property = null)
	{
		if ($object instanceof \ArrayAccess) {
			if ($property === null) {
				if (isset($object['full_title'])) {
					return $object['full_title'];
				} elseif (isset($object['title'])) {
					return $object['title'];
				} elseif (isset($object['name'])) {
					return $object['title'];
				}
			}

			if (isset($object[$property])) {
				return $object[$property];
			}
		}

		return null;
	}
}
}
 




namespace Application\DeskPRO\Translate
{


class SystemLanguage extends \Application\DeskPRO\Entity\Language
{
	protected static $instance = null;
	public static function getInstance()
	{
		if (self::$instance !== null) return self::$instance;

		self::$instance = new self();

		return self::$instance;
	}

	protected function __construct()
	{
		$this->id            = 0;
		$this->sys_name      = 'default';
		$this->lang_code     = 'eng';
		$this->locale        = 'en_US';
		$this->title         = "English";
		$this->base_filepath = DP_ROOT.'/languages/default';
		$this->has_user      = true;
		$this->has_admin     = true;
		$this->has_agent     = true;
	}
}
}
 




namespace Application\DeskPRO\Translate
{

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\Language;
use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

use Application\DeskPRO\Translate\Loader\LoaderInterface;
use Application\DeskPRO\Entity\Language as LanguageEntity;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\EventDispatcher\DataEvent;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Application\DeskPRO\HttpFoundation\Session;


class Translate implements PersonContextInterface
{
	const EVENT_NO_PHRASE = 'DeskPRO_onTranslateNoPhrase';

	
	protected $_phrases = array();

	
	protected $_pending_groups = array();

	
	protected $_loaded_groups = array();

	
	protected $_loaded_languages = array();

	
	protected $_default_language = null;

	
	protected $_language = null;

	
	protected $_phrase_selector = null;

	
	protected $_phrase_object_namer = null;

	
	protected $_person_context;

	
	protected $_default_person_context;

	
	protected $_event_dispatcher = null;
    
    protected static $_missing_phrases = array();


	
	public function __construct(LoaderInterface $loader, EventDispatcher $event_dispatcher = null)
	{
		$this->setLanguage(SystemLanguage::getInstance(), false);
		$this->loader = $loader;

        if(App::getConfig('debug.language_report_missing')) {
            \DpShutdown::add(array($this, 'reportMissingPhrases'));
        }

		$this->_event_dispatcher = $event_dispatcher;
	}


	
	public function setSession(Session $session = null)
	{
		if (!$session) {
			return;
		}

		$this->setLanguage($session->getLanguage());
		$this->setDefaultLanguage($session->getLanguage());
	}


	
	public function setPersonContext(Person $person = null, $load_previous_groups = true)
	{
		if (!$person) {
			$person = $this->_default_person_context;
		}

		$this->_person_context = $person;

		if (!$this->_default_person_context) {
			$this->_default_person_context = $person;
		}

		if ($this->_language['id'] != $this->_person_context['id']) {
			$this->setLanguage($person->getLanguage(), $load_previous_groups);
		}
	}


	
	public function setTemporaryPersonContext(Person $person, $func)
	{
		$this->setPersonContext($person);

		$e = null;
		try {
			$func($this, $person->getLanguage());
		} catch (\Exception $e) {}

		$this->setPersonContext();

		if ($e) {
			throw $e;
		}
	}


	
	public function resetToDefaultPersonContext()
	{
		$this->setPersonContext(null);
	}


	
	public function setDefaultPersonContext(Person $person)
	{
		$this->_default_person_context = $person;
	}


	
	public function getPersonContext()
	{
		return $this->_person_context;
	}


	
	public function setLanguage(LanguageEntity $language = null, $load_previous_groups = true)
	{
				if ($language AND $this->_language === null) {
			$this->_default_language = $language;
		}

		if (!$language) {
			$language = $this->_default_language;
		}

		$last_id = null;
		if ($this->_language) {
			$last_id = $this->_language['id'];
		}

		$this->_language = $language;
		$this->_loaded_languages[$language['id']] = $language;

		if ($last_id AND $load_previous_groups AND isset($this->_loaded_groups[$last_id])) {
			$this->loadPhraseGroups($this->_loaded_groups[$last_id], $language);
		}
	}


	
	public function resetToDefaultLanguage()
	{
		return $this->setLanguage(null);
	}


	
	public function setTemporaryLanguage(LanguageEntity $language = null, $func)
	{
		$this->setLanguage($language);

		$e = null;
		try {
			$func($this, $language);
		} catch (\Exception $e) {}

		$this->setLanguage();

		if ($e) {
			throw $e;
		}
	}


	
	public function setDefaultLanguage(LanguageEntity $language)
	{
		$this->_default_language = $language;
	}



	
	public function getLanguage()
	{
		return $this->_language;
	}



	
	public function loadPhraseGroups($groups, LanguageEntity $language = null)
	{
		if (!is_array($groups)) {
			$groups = array($groups);
		}

		if (!$language) {
			$language = $this->_language;
		}

		$language_id = $language->id;

		if (!isset($this->_pending_groups[$language_id])) $this->_pending_groups[$language_id] = array();

		foreach ($groups as $group) {
			if (!in_array($group, $this->_loaded_groups)) {
				$this->_pending_groups[$language_id][] = $group;
			}
		}
	}



	
	protected function _loadPendingPhraseGroups()
	{
		if (!$this->_pending_groups) {
			return;
		}

		foreach ($this->_pending_groups as $language_id => $groups) {
			$groups = array_unique($groups);
			$groups = Arrays::removeFalsey($groups);

			if (!isset($this->_loaded_languages[$language_id])) {
				$this->_loaded_languages[$language_id] = App::getEntityRepository('DeskPRO:Language')->find($language_id);
			}
			$language = $this->_loaded_languages[$language_id];

			if (!isset($this->_phrases[$language_id])) $this->_phrases[$language_id] = array();
			$this->_phrases[$language_id] = array_merge($this->_phrases[$language_id], $this->loader->load($groups, $language));

			if (!isset($this->_loaded_groups[$language_id])) $this->_loaded_groups[$language_id] = array();
			$this->_loaded_groups[$language_id] = array_merge($this->_loaded_groups[$language_id], $groups);
		}

		$this->_pending_groups = array();
	}



	
	public function getPhraseGroupFromName($phrase_name)
	{
		if (!is_string($phrase_name)) {
			return false;
		}

		$pos = strpos($phrase_name, '.');
		if ($pos === false) {
			return '__default__';
		}

		$parts = Strings::rexplode('.', $phrase_name, 2);

		return $parts[0];
	}



	
	public function getPhraseText($phrase_name, $language = null, $null_on_notfound = false)
	{
		if ($language === null) $language = $this->_language;
		if (!$phrase_name) return '';
		if (!is_string($phrase_name)) return '(' . gettype($phrase_name) . ')';

		if (Numbers::isInteger($language)) {
			$language_id = $language;
		} else {
			$language_id = $language['id'];
		}

		if (!isset($this->_phrases[$language_id][$phrase_name])) {
			$check_group = $this->getPhraseGroupFromName($phrase_name);

			if (!isset($this->_loaded_groups[$language_id]) OR !in_array($check_group, $this->_loaded_groups[$language_id])) {
				if (!isset($this->_pending_groups[$language_id])) $this->_pending_groups[$language_id] = array();
				$this->_pending_groups[$language_id][] = $check_group;

				$this->_loadPendingPhraseGroups();
				return $this->getPhraseText($phrase_name, $language_id, $null_on_notfound);
			}

			if ($null_on_notfound) {
				return null;
			} else {
				if (strpos($phrase_name, 'obj') === false) {
					$e = new \InvalidArgumentException("Missing phrase: $phrase_name");

					if (dp_get_config('debug.dev')) {
						KernelErrorHandler::logException($e, false);
					} else {
						KernelErrorHandler::logException($e, true, 'missing_phrase_' . $phrase_name);
					}
				}
			}

			return $this->_noPhrase($phrase_name, $language);
		}

		return $this->_phrases[$language_id][$phrase_name];
	}

    public static function reportMissingPhrases() {
        if(count(self::$_missing_phrases)) {
			try {
				$logger = App::createNewLogger('missing_phrase_logger', null);
				$message = "'The following phrases are missing:\n";

				foreach(self::$_missing_phrases as $phrase) {
					$message .= "{$phrase}\n";
				}

				$logger->log($message, 'WARN', array(
					'subject'    => '[DeskPRO Missing Phrases]',
					'message'    => $message
				));
			} catch(\Exception $e) {}
        }
    }

	
	protected function _noPhrase($phrase_name, $language)
	{
		$phrase = null;

        if(!isset(self::$_missing_phrases[$phrase_name])) {
            self::$_missing_phrases[$phrase_name] = $phrase_name;
        }

		if ($this->_event_dispatcher) {
			$evdata = new DataEvent(array(
				'phrase_name' => $phrase_name,
				'language' => $language,
				'return' => $phrase,
			));
			$this->_event_dispatcher->dispatch(self::EVENT_NO_PHRASE, $evdata);

			$phrase = $evdata->return;
		}

		return $phrase;
	}


	
	public function getPhraseTextCount($phrase_name, $count, $language = null)
	{
		$phrase_text = $this->getPhraseText($phrase_name);
		if (!$phrase_text) {
			return null;
		}

		if ($language === null) {
			$language = $this->_language;
		} elseif (Numbers::isInteger($language) && isset($this->_loaded_languages[$language])) {
			$language = $this->_loaded_languages[$language];
		}

		try {
			$phrase = $this->getCountPhraseSelector()->choose($phrase_text, $count, $language->getLocale());
		} catch (\InvalidArgumentException $e) {
			try {
																$phrase = $this->getCountPhraseSelector()->choose($phrase_text, $count, 'en_US');
			} catch (\InvalidArgumentException $e) {
				$phrase = $e->getMessage();
			}
		}

		return $phrase;
	}



	
	public function getPhraseObject($object, $property = null, $language = null)
	{
						
		if ($object instanceof DelegatePhraseInterface) {
			return $object->getPhrase($this, $language);

		} else if ($object instanceof HasPhraseName) {
			$phrase_name = $object->getPhraseName($property, $this);
			$phrase_text = false;

			if ($phrase_name && $this->hasPhrase($phrase_name, $language)) {
				$phrase_text = $this->phrase($phrase_name, array(), $language);
			}

			if (!$phrase_text) {
				$phrase_text = $object->getPhraseDefault($property, $this);
			}

			if ($phrase_text) return $phrase_text;

			return '';
		}

						
		$namer = $this->getObjectPhraseNamer();
		$phrase_name = $namer->getPhraseName($object, $property);
		$phrase_text = false;

		if ($phrase_name && $this->hasPhrase($phrase_name, $language)) {
			$phrase_text = $this->phrase($phrase_name, array(), $language);
		}

		if (!$phrase_text) {
			$phrase_text = $this->getObjectPhraseNamer()->getPhraseDefault($object, $property);
		}

		if ($phrase_text) return $phrase_text;

				return '';
	}



	
	public function phrase($phrase_name, array $vars = array(), $language = null)
	{
        $debug = App::getConfig('debug.language_test_mode');

		if (!$debug && defined('DP_INTERFACE') && DP_INTERFACE == 'agent' && strpos($phrase_name, 'agent') === 0) {
			try {
				$debug = App::getSetting('core.agent_translate_debug');
			} catch (\Exception $e) {}
		}

		if (!$debug && isset($_COOKIE['dp_dev_langdebug'])) {
			$debug = $_COOKIE['dp_dev_langdebug'];
		}

		if ($debug == 'user' AND $phrase_name != 'agent.general.x_is_y') {
			if (substr($phrase_name, 0, 4) != 'user') {
				echo $phrase_name;
				die();
			}
		}

		if ($debug == 'japanese') {

						$chars = array(
				'一', '丁', '丂', '七', '丄', '丅', '万', '丈', '三', '上', '下', '丌', '不', '与', '丏', '丐', '丑', '丒',
				'且', '丕', '世', '丗', '丘', '丙', '丞', '丟', '両', '丣', '两', '並', '丨', '丩', '个', '丫', '丬', '中',
				'丮', '丯', '丰', '丱', '串', '丳', '临', '丵', '丶', '丸', '丹', '主', '丼', '丿', '乀', '乁', '乂', '乃',
				'乄', '久', '乇', '么', '之', '乍', '乎', '乏', '乑', '乕', '乖', '乗', '乘', '乙', '乚', '乜', '九', '乞',
				'也', '乢', '乣', '乨', '乩', '乱', '乳', '乴', '乵', '乹', '乾', '乿', '亀', '亂', '了', '予', '争', '亊',
				'事', '二', '亍', '于');

			$output = '';

			for($i = 0; $i < 4; $i++) {
				$output .= $chars[rand(0, count($chars) - 1)];
			}

			return $output;
		}

		if (is_object($phrase_name) OR (is_array($phrase_name) AND is_object($phrase_name[0]))) {
			if (is_array($phrase_name)) {
				list ($object, $property) = $phrase_name;
			} else {
				$object = $phrase_name;
				$property = null;
			}

			$phrase_text = $this->getPhraseObject($object, $property, $language);
		} elseif (isset($vars['count'])) {
			try {
				$phrase_text = $this->getPhraseTextCount($phrase_name, $vars['count'], $language);
			} catch (\Exception $e) {
												$phrase_text = $this->getPhraseText($phrase_name, $language);
			}
		} else {
			$phrase_text = $this->getPhraseText($phrase_name, $language);
		}

		if (!$phrase_text) $phrase_text = '';

		$phrase_text = $this->replaceVarsInString($phrase_text, $vars);

				$m = null;
		if (preg_match_all('#{{phrase\.([a-zA-Z0-9\-_\.]+)}}#', $phrase_text, $m)) {
			foreach ($m[1] as $sub_phrase_name) {
				if ($sub_phrase_name == $phrase_name) continue; 				$sub_phrase_text = $this->phrase($sub_phrase_name, $vars, $language);
				$phrase_text = str_replace("{{phrase.$sub_phrase_name}}", $sub_phrase_text, $phrase_text);
			}
		}

		if ($debug == 'double_length') {
			return $phrase_text . ' ' . $phrase_text;
		} else if ($debug == 'half_length') {
			$length = strlen($phrase_text);
			return substr($phrase_text, round($length / 2));
		} else if($debug == 'package') {
			if(!is_string($phrase_name)) {
				return '!'.strtoupper(typeof($phrase_name)).'!';
			}
			else {
				$parts = explode('.', $phrase_name);
				return '!'.strtoupper($parts[0]).'!';
			}
		} else if($debug == 'package_prefix') {
			$p_prefixes = array(
				'agent' => 'Ѯ',
				'admin' => 'Ѿ',
				'user' => 'Ѱ',
				'object' => 'Ѳ',
				'resource' => 'Ѻ',
				'array' => 'Г',
				'unknown' => 'Ц',
			);

			if(!is_scalar($phrase_name)) {
				$package = typeof($phrase_name);
			}
			else {
				$parts = explode('.', $phrase_name);
				$package = $parts[0];
			}

			if(!isset($p_prefixes[$package])) {
				$package = 'unknown';
			}

			return $p_prefixes[$package].$phrase_text;
		} else if ($debug == 'prefix') {
			return '@'.$phrase_text;
		} else if ($debug == 'wrap') {
			return '^'.$phrase_text . '^';
		}

		return $phrase_text;
	}


	
	public function replaceVarsInString($phrase_text, array $vars = array())
	{
		if ($vars) {
			$phrase_text = preg_replace_callback('#\{\{\s*([a-zA-Z0-9_]+)\s*\}\}#', function ($m) use ($vars) {
				$name = $m[1];

				if (isset($vars[$name])) {
					return $vars[$name];
				} elseif (isset($vars['_context'][$name])) {
					return $vars['_context'][$name];
				}

				return '';
			}, $phrase_text);

			$phrase_text = preg_replace_callback('#\{\{\s*([a-zA-Z0-9_]+)\.([a-zA-Z0-9_]+)\s*\}\}#', function ($m) use ($vars) {
				$name = $m[1];
				$prop = $m[2];

				if (isset($vars[$name])) {
					if (isset($vars[$name][$prop])) {
						return $vars[$name][$prop];
					} elseif (isset($vars[$name]->$prop)) {
						return $vars[$name]->$prop;
					}
				} elseif (isset($vars['_context'][$name])) {
					if (isset($vars['_context'][$name][$prop])) {
						return $vars['_context'][$name][$prop];
					} elseif (isset($vars['_context'][$name]->$prop)) {
						return $vars['_context'][$name]->$prop;
					}
				}

				return '';
			}, $phrase_text);
		}

		return $phrase_text;
	}


	
	public function date($format, $date_or_ts = null, $prefix = 'user.time.')
	{
		if (!$date_or_ts) {
			$date_or_ts = time();
		}

		$ts = $date_or_ts;
		if ($ts instanceof \DateTime) {
			$tz_offset = $ts->format('P');

									$ts = \Orb\Util\Dates::makeUtcDateTime($date_or_ts);
			$ts = $ts->getTimestamp();
		} else {
			$tz_offset = '+00:00';
		}

								
		$format = preg_replace('#(?<!\\\\)([DlFMP])#', '\\\\D\\\\P-\\\\$1', $format);
		$date = date($format, $ts);

		$tr = $this;
		$date = preg_replace_callback('#DP\-([DlFMP])#', function($m) use ($prefix, $tr, $ts, $tz_offset) {

			switch ($m[1]) {
				case 'D':
					$phrase_name = $prefix . 'short-day_' . strtolower(date('l', $ts));
					break;
				case 'l':
					$phrase_name = $prefix . 'long-day_' . strtolower(date('l', $ts));
					break;
				case 'F':
					$phrase_name = $prefix . 'long-month_' . strtolower(date('F', $ts));
					break;
				case 'M':
					$phrase_name = $prefix . 'short-month_' . strtolower(date('F', $ts));
					break;
				case 'P':
					return $tz_offset;
				default:
										return 'unkown segment';
			}

			return $tr->getPhraseText($phrase_name);
		},  $date);

		return $date;
	}


	
	public function hasPhrase($phrase_name, $language = null)
	{
		if ($this->getPhraseText($phrase_name, $language, true) !== null) {
			return true;
		}

		return false;
	}



	
	public function getCountPhraseSelector()
	{
		if ($this->_phrase_selector !== null) return $this->_phrase_selector;

		$this->_phrase_selector = new \Symfony\Component\Translation\MessageSelector();
		return $this->_phrase_selector;
	}

	
	public function getObjectPhraseNamer()
	{
		if ($this->_phrase_object_namer !== null) return $this->_phrase_object_namer;

		$this->_phrase_object_namer = new \Application\DeskPRO\Translate\ObjectPhraseNamer();

		return $this->_phrase_object_namer;
	}


	
	public function objectChoosePhraseText($object, $property, $lang_priority)
	{
		$args = func_get_args();
		array_shift($args);
		array_shift($args);

						
		
		$lang_priority = array();
		foreach ($args as $arg) {
			if (!is_array($arg)) {
				$arg = array($arg);
			}

			foreach ($arg as $l) {
				if (!$l) continue;

				if (is_numeric($l)) {
					$l = App::getContainer()->getLanguageData()->get($l);
				}

				if ($l instanceof Language) {
					$lang_priority[] = $l;
				}
			}
		}

						
		$obj_lang_repos = App::getContainer()->getObjectLangRepository();

		foreach ($lang_priority as $lang) {
			$obj_lang_repos->preloadObject($lang, $object);
		}

		$rec = $obj_lang_repos->getRec($lang_priority, $object, $property, true);

		if (!$rec) {
			return '';
		}

		return $rec->value;
	}
}
}
 




namespace Application\DeskPRO\Twig\Extension
{

use Orb\Data\Countries;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Application\DeskPRO\App;

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Dates;

class TemplatingExtension extends \Twig_Extension
{
    protected $container;
	protected $counter_registry;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getTemplating()
    {
        return $this->container->get('templating');
    }

    public function getFunctions()
    {
        return array(
			'constant'                         => new \Twig_Function_Method($this, 'getConstant', array()),
			'phrase'                           => new \Twig_Function_Method($this, 'getPhrase', array('is_safe' => array('html'), 'needs_context' => true)),
			'phrase_code'                      => new \Twig_Function_Method($this, 'getPhraseText', array()),
			'has_phrase'                       => new \Twig_Function_Method($this, 'hasPhrase', array('is_safe' => array('html'))),
			'phrase_object'                    => new \Twig_Function_Method($this, 'getPhraseObject'),
			'phrase_dev'                       => new \Twig_Function_Method($this, 'getPhraseDev'),
			'language_html_attr'               => new \Twig_Function_Method($this, 'getLanguageHtmlAttributes', array('is_safe' => array('html'))),
			'language_arrow'                   => new \Twig_Function_Method($this, 'getLanguageArrow', array('is_safe' => array('html'))),
			'is_rtl'                           => new \Twig_Function_Method($this, 'isRtl'),
			'url_fragment'                     => new \Twig_Function_Method($this, 'urlFragment'),
			'asset_full'                       => new \Twig_Function_Method($this, 'assetFull'),
			'asset_url'                        => new \Twig_Function_Method($this, 'assetFull'),
			'url_full'                         => new \Twig_Function_Method($this, 'urlFull'),
			'url_display'                      => new \Twig_Function_Method($this, 'urlDisplay'),
			'helpdesk_url'                     => new \Twig_Function_Method($this, 'helpdeskUrl'),
			'deskpro_debug'                    => new \Twig_Function_Method($this, 'isDebugMode'),
			'render_custom_field'              => new \Twig_Function_Method($this, 'renderCustomField', array('is_safe' => array('html'))),
			'render_custom_field_text'         => new \Twig_Function_Method($this, 'renderCustomFieldText'),
			'render_custom_field_form'         => new \Twig_Function_Method($this, 'renderCustomFieldForm', array('is_safe' => array('html'))),
			'el_uid'                           => new \Twig_Function_Method($this, 'elUid', array('is_safe' => array('html'))),
			'rand'                             => new \Twig_Function_Method($this, 'rand', array('is_safe' => array('html'))),
			'is_partial_request'               => new \Twig_Function_Method($this, 'isPartialRequest'),
			'str_repeat'                       => new \Twig_Function_Method($this, 'strRepeat'),
			'is_user_guest'                    => new \Twig_Function_Method($this, 'isUserGuest'),
			'is_user_loggedin'                 => new \Twig_Function_Method($this, 'isUserUser'),
			'is_user_agent'                    => new \Twig_Function_Method($this, 'isUserAgent'),
			'is_user_admin'                    => new \Twig_Function_Method($this, 'isUserAdmin'),
			'flash_message'                    => new \Twig_Function_Method($this, 'flashMessage'),
			'compare_type'                     => new \Twig_Function_Method($this, 'compareType'),
			'object_path'                      => new \Twig_Function_Method($this, 'getObjectPath'),
			'object_path_agent'                => new \Twig_Function_Method($this, 'getObjectPathAgent'),
			'get_type'                         => new \Twig_Function_Method($this, 'getType'),
			'debug_var'                        => new \Twig_Function_Method($this, 'debugVar'),
			'security_token'                   => new \Twig_Function_Method($this, 'securityToken'),
			'static_security_token'            => new \Twig_Function_Method($this, 'staticSecurityToken'),
			'static_security_token_secret'     => new \Twig_Function_Method($this, 'staticSecurityTokenSecret'),
			'render_usersource'                => new \Twig_Function_Method($this, 'renderUsersource', array('is_safe' => array('html'))),
			'get_data'                         => new \Twig_Function_Method($this, 'getData'),
			'dp_asset'                         => new \Twig_Function_Method($this, 'getAssetic'),
			'dp_asset_raw'                     => new \Twig_Function_Method($this, 'getAsseticRaw'),
			'dp_asset_html'                    => new \Twig_Function_Method($this, 'htmlGetAssetic', array('is_safe' => array('html'))),
			'start_counter'                    => new \Twig_Function_Method($this, 'startCounter'),
			'get_counter'                      => new \Twig_Function_Method($this, 'getCounter'),
			'inc_counter'                      => new \Twig_Function_Method($this, 'incCounter'),
			'form_token'                       => new \Twig_Function_Method($this, 'formToken', array('is_safe' => array('html'))),
			'relative_time'                    => new \Twig_Function_Method($this, 'relativeTime', array('is_safe' => array('html'))),
			'get_service_url'                  => new \Twig_Function_Method($this, 'getServiceUrl', array('is_safe' => array('html'))),
			'get_service_url_raw'              => new \Twig_Function_Method($this, 'getServiceUrlRaw', array('is_safe' => array('html'))),
			'get_instance_ability'             => new \Twig_Function_Method($this, 'getInstanceAbility'),
			'is_array'                         => new \Twig_Function_Method($this, 'isArray'),
			'gravatar_for_email'               => new \Twig_Function_Method($this, 'gravatar'),
			'time_group_phrase'                => new \Twig_Function_Method($this, 'getTimeGroupPhrase'),
			'captcha_html'                     => new \Twig_Function_Method($this, 'captchaHtml', array('is_safe' => array('html'))),
			'include_file'                     => new \Twig_Function_Method($this, 'includeFile', array('is_safe' => array('html'))),
			'include_php_file'                 => new \Twig_Function_Method($this, 'includePhpFile', array('is_safe' => array('html'))),
			'var_dump'                         => new \Twig_Function_Method($this, 'dumpVar'),
			'dp_copyright'                     => new \Twig_Function_Function('DeskPRO\\Kernel\\License::staticGetUserCopyrightHtml', array('is_safe' => array('html'))),
			'dp_widgets'                       => new \Twig_Function_Method($this, 'getWidgets', array('is_safe' => array('html'))),
			'dp_widgets_raw'                   => new \Twig_Function_Method($this, 'getWidgetsRaw'),
			'dp_widget_id'                     => new \Twig_Function_Method($this, 'getWidgetHtmlId'),
			'dp_widget_tabs_header'            => new \Twig_Function_Method($this, 'getWidgetTabsHeader', array('is_safe' => array('html'))),
			'dp_widget_tabs'                   => new \Twig_Function_Method($this, 'getWidgetTabsBody', array('is_safe' => array('html'))),
			'dp_js_sso_loader'                 => new \Twig_Function_Method($this, 'getJsSsoLoader', array('is_safe' => array('html'))),
			'dp_js_sso_share'                  => new \Twig_Function_Method($this, 'getJsSsoShare', array('is_safe' => array('html'))),
			'base_template_name'               => new \Twig_Function_Method($this, 'getBaseTemplateName', array('is_safe' => array('html'))),
			'array_attr'                       => new \Twig_Function_Method($this, 'getArrayAttribute'),
			'min'                              => new \Twig_Function_Method($this, 'min'),
			'max'                              => new \Twig_Function_Method($this, 'max'),
			'match'                            => new \Twig_Function_Method($this, 'match'),
			'set_tplvar'                       => new \Twig_Function_Method($this, 'set_tplvar', array('is_safe' => array('html'), 'needs_context' => true)),
			'tpl_source'                       => new \Twig_Function_Method($this, 'getTplSourceTemplate', array('is_safe' => array('html'))),

						'url'  => new \Twig_Function_Method($this, 'getUrl'),
            'path' => new \Twig_Function_Method($this, 'getPath'),
        );
    }

	public function getFilters()
    {
        return array(
			'safe_link_urls'         => new \Twig_Filter_Method($this, 'safeLinkUrls', array('is_safe' => array('html'))),
			'safe_link_urls_html'    => new \Twig_Filter_Method($this, 'safeLinkUrlsHtml', array('is_safe' => array('html'))),
			'link_agent_short_code_html'  => new \Twig_Filter_Method($this, 'linkAgentShortCodeHtml', array('is_safe' => array('html'))),
			'raw_url_encode'         => new \Twig_Filter_Method($this, 'rawUrlEncode', array('is_safe' => array('html'))),
			'repeat'                 => new \Twig_Filter_Method($this, 'strRepeat'),
			'trim'                   => new \Twig_Filter_Method($this, 'strTrim'),
			'encode_number'          => new \Twig_Filter_Method($this, 'encNum', array('is_safe' => array('html'))),
			'decode_number'          => new \Twig_Filter_Method($this, 'decNum', array('is_safe' => array('html'))),
			'md5_hash'               => new \Twig_Filter_Method($this, 'getMd5', array('is_safe' => array('html'))),
			'date'                   => new \Twig_Filter_Method($this, 'userDate', array('needs_context' => true)),
			'time_length'            => new \Twig_Filter_Method($this, 'timeLength'),
			'slugify'                => new \Twig_Filter_Method($this, 'slugify'),
			'emphasize_words'        => new \Twig_Filter_Method($this, 'emphasizeWords', array('is_safe' => array('html'))),
			'strip_linebreaks'       => new \Twig_Filter_Method($this, 'stripLinebreaks'),
			'explode'                => new \Twig_Filter_Method($this, 'explodeString'),
			'split'                  => new \Twig_Filter_Method($this, 'explodeString'),
			'join'                   => new \Twig_Filter_Method($this, 'implodeArray'),
			'implode'                => new \Twig_Filter_Method($this, 'implodeArray'),
			'crc32'                  => new \Twig_Filter_Method($this, 'crc32'),
			'url_domain'             => new \Twig_Filter_Method($this, 'getUrlDomain'),
			'truncate'               => new \Twig_Filter_Method($this, 'strTruncate'),
			'first'                  => new \Twig_Filter_Method($this, 'getFirst'),
			'last'                   => new \Twig_Filter_Method($this, 'getLast'),
			'filesize_display'       => new \Twig_Filter_Method($this, 'filesizeDisplay'),
			'url_trim_scheme'        => new \Twig_Filter_Method($this, 'urlTrimScheme'),
			'country_name'           => new \Twig_Filter_Method($this, 'countryName'),
			'count_lines'            => new \Twig_Filter_Method($this, 'countLines'),
			'smart_wrap'             => new \Twig_Filter_Method($this, 'smartWrap'),

			'hex2rgb'                => new \Twig_Filter_Method($this, 'hex2rgb'),

			'trans'                  => new \Twig_Filter_Function('\\Application\\DeskPRO\\Twig\\Extension\\deskpro_twig_filter_dummy'),
			'transchoice'            => new \Twig_Filter_Function('\\Application\\DeskPRO\\Twig\\Extension\\deskpro_twig_filter_dummy'),
			'plain_template_filter'  => new \Twig_Filter_Method($this, 'plain_template_filter'),

						'upper'                  => new \Twig_Filter_Method($this, 'strUpper'),
			'lower'                  => new \Twig_Filter_Method($this, 'strLower'),
        );
    }

	public function getPath($name, $parameters = array())
    {
		try {
        	return App::getRouter()->generate($name, $parameters, false);
		} catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
			if (App::isDebug()) {
				throw $e;
			}
			return '';
		}
    }

    public function getUrl($name, $parameters = array())
    {
		try {
        	return App::getRouter()->generate($name, $parameters, true);
		} catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
			if (App::isDebug()) {
				throw $e;
			}
			return '';
		}
    }

	public function getBaseTemplateName($name)
	{
		$parts = explode(':', $name);
		$name = array_pop($parts);
		$name = str_replace('.html.twig', '', $name);
		return $name;
	}

	public function getConstant($name = '')
	{
		static $whitelist = array(
			'DP_BUILD_NUM'          => true,
			'DP_BUILD_TIME'         => true,
			'DPC_SITE_ID'           => true,
			'DPC_SITE_DOMAIN'       => true,
			'DPC_SITE_DOMAIN_ALT'   => true,
			'DPC_SITE_BUILD_NUM'    => true,
			'DPC_ACCOUNT_ID'        => true,
			'DPC_BILL_OVERDUE'      => true,
			'DPC_BILL_DATE'         => true,
			'DP_ENABLE_AGENT_LANG'  => true,
		);

		if (!$name || !defined($name) || !isset($whitelist[$name])) {
			return '';
		}

		return constant($name);
	}

	public function filesizeDisplay($size)
	{
		if ($size < 0) {
			return 'n/a';
		}

		return \Orb\Util\Numbers::filesizeDisplay($size);
	}

	public function getTimeGroupPhrase($time)
	{
		static $time_phrases = array(
			300        => '< 5 minutes',
			900        => '5 - 15 minutes',
			1800       => '15 - 30 minutes',
			3600       => '30 - 60 minutes',
			7200       => '1 - 2 hours',
			10800      => '2 - 3 hours',
			14400      => '3 - 4 hours',
			21600      => '4 - 6 hours',
			43200      => '6 - 12 hours',
			86400      => '12 - 24 hours',
			172800     => '1 - 2 days',
			259200     => '2 - 3 days',
			345600     => '3 - 4 days',
			432000     => '4 - 5 days',
			518400     => '5 - 6 days',
			604800     => '6 - 7 days',
			1209600    => '1 - 2 weeks',
			1814400    => '2 - 3 weeks',
			2419200    => '3 - 4 weeks',
			4838400    => '1 - 2 months',
			7257600    => '2 - 3 months',
			9676800    => '3 - 4 months',
			12096000   => '4 - 5 months',
			14515200   => '5 - 6 months',
		);

		foreach ($time_phrases as $min => $phrase) {
			if ($time <= $min) {
				return $phrase;
			}
		}

		return '> 6 months';
	}

	public function gravatar($email, $size = 80)
	{
		$hash = strtolower(md5($email));
		$url = 'http://www.gravatar.com/avatar/' . $hash . '?';
		$url .= '&d=' . App::get('router')->generate('serve_default_picture', array('s' => $size), true);

		return $url;
	}

	public function getFirst($var)
	{
		if (!$var) return null;
		return \Orb\Util\Arrays::getFirstItem($var);
	}

	public function getLast($var)
	{
		if (!$var) return null;
		return \Orb\Util\Arrays::getLastItem($var);
	}

	public function isArray($var)
	{
		return is_array($var);
	}

	public function getInstanceAbility($method)
	{
		$method = Strings::underscoreToCamelCase($method);
		return $this->container->getSystemService('instance_ability')->$method();
	}

	public function getServiceUrl($name, $params = null, $named_params = null, $html = true)
	{
		if (!$params || !is_array($params)) {
			$params = null;
		}
		if (!$named_params || !is_array($named_params)) {
			$params = null;
		}

		return $this->container->get('deskpro.service_urls')->get($name, $params, $named_params, $html);
	}

	public function getServiceUrlRaw($name, $params = null, $named_params = null)
	{
		return $this->getServiceUrl($name, $params, $named_params, false);
	}

	public function relativeTime($secs, $detail = 2)
	{
		return Dates::secsToReadable($secs, $detail);
	}

	public function strTruncate($str, $width = 80, $dots = true)
	{
		if (strlen($str) <= $width) {
			return $str;
		}

		if ($dots) {
			if ($dots === true) {
				$dots = '...';
			}
			return trim(substr($str, 0, $width). $dots);
		} else {
			return trim(substr($str, 0, $width));
		}
	}

	public function startCounter($name = 'default', $start = 1)
	{
		$this->counter_registry[$name] = 1;
		return '';
	}

	public function getCounter($name = 'default')
	{
		return isset($this->counter_registry[$name]) ? $this->counter_registry[$name] : 0;
	}

	public function incCounter($name = 'default')
	{
		if (!isset($this->counter_registry[$name])) {
			$this->counter_registry[$name] = 0;
		}

		$v = $this->counter_registry[$name]++;
		return $v;
	}

	public function getUrlDomain($string)
	{
		$urlinfo = @parse_url($string);
		if (!$urlinfo) {
			return $string;
		}

		return @$urlinfo['host'];
	}

	public function crc32($string)
	{
		$string = (string)$string;

		return sprintf("%u", crc32($string));
	}

	public function safeLinkUrlsHtml($text)
	{
		return Strings::linkifyHtml($text, true);
	}

	public function safeLinkUrls($text)
	{
		$text = @htmlspecialchars($text);
		return Strings::linkifyHtml($text, true);
	}

	public function linkAgentShortCodeHtml($html)
	{
		$id_map = array(
			't' => array('Ticket', 'agent/#app.tickets,t.o:'),
			'p' => array('Person', 'agent/#app.people,p.o:'),
			'o' => array('Organization', 'agent/#app.people.orgs,o.o:'),
			'a' => array('Article', 'agent/#app.publish,a.o:'),
			'n' => array('News', 'agent/#app.publish,n.o:'),
			'd' => array('Download', 'agent/#app.publish,d.o:'),
			'i' => array('Feedback', 'agent/#app.feedback,i.o:'),
			'tw' => array('Tweet', 'agent/#app.twitter,tw.o:'),
		);

		$url = App::getSetting('core.deskpro_url');

		foreach ($id_map AS $prefix => $info) {
			$html = preg_replace(
				'/\{\{\s*' . $prefix . '-(\d+)\s*\}\}/',
				'<a href="' . $url . $info[1] . '$1">' . $info[0] . ' #$1</a>',
				$html
			);
		}

		return $html;
	}

	public function getAssetic($name)
	{
		$assetic_manager = $this->container->getSystemService('assetic_manager');
		return $assetic_manager->getUrl($name);
	}

	public function getAsseticRaw($name)
	{
		$assetic_manager = $this->container->getSystemService('assetic_manager');
		return $assetic_manager->getRawUrls($name);
	}

	public function implodeArray($array, $sep = ', ')
	{
		if (!$array || !is_array($array)) {
			return '';
		}
		return implode($array, $sep);
	}

	public function explodeString($string, $del = ',') {
		$ret = array();
		$string = (string)$string;

		foreach (explode($del, $string) as $p) {
			$ret[] = trim($p);
		}

		return $ret;
	}

	public function stripLinebreaks($str)
	{
		$str = str_replace(array("\r\n", "\n"), " ", $str);
		$str = str_replace(array("<br />", "<br/>", "<br>"), " ", $str);
		$str = str_replace(array("<p>", "</p>", "<p />", "<p/>"), " ", $str);

		return $str;
	}

	public function htmlGetAssetic($name, $options = array())
	{
		$raw_packs = App::getConfig('debug.raw_assets', array());
		$less_use_css = App::getConfig('debug.less_use_css_dir', false);
		$disable_client_cache = App::getConfig('debug.disable_client_cache', false);

		if ($raw_packs && (in_array($name, $raw_packs) OR in_array('all', $raw_packs) OR (in_array('all -vendors', $raw_packs) && $name != 'agent_vendors'))) {
			$urls = $this->getAsseticRaw($name);
		} else {
			$urls = array($this->getAssetic($name));
		}

		$qs_append = ($disable_client_cache ? time() : DP_BUILD_TIME);
		$html = array();

		foreach ($urls as $url) {
			$type = Strings::getExtension($url);

			$url .= '?' . $qs_append;

			switch ($type) {
				case 'js':
					$html[] = '<script type="text/javascript" src="' . $url . '"></script>';
					break;
				case 'css':
					if (!isset($options['media'])) {
						$options['media'] = 'screen,print';
					}
					$html[] = '<link rel="stylesheet" type="text/css" media="' . $options['media'] .'" href="' . $url .'" />';
					break;
				case 'less':
					if (!isset($options['media'])) {
						$options['media'] = 'screen,print';
					}

					if ($less_use_css) {
						$url = str_replace('/stylesheets-less/', '/stylesheets/', $url);
						$url = str_replace('.less', '.css', $url);
						$html[] = '<link rel="stylesheet" type="text/css" media="' . $options['media'] .'" href="' . $url .'" />';
					} else {
						$html[] = '<link rel="stylesheet/less" type="text/css" media="' . $options['media'] .'" href="' . $url .'" />';
					}
					break;
			}
		}

		return implode("\n", $html);
	}

	public function getData($id)
	{
		switch ($id) {
			case 'country_names':
				return \Orb\Data\Countries::getCountryNames();
				break;
			default:
				return null;
		}
	}

	public function emphasizeWords($string, $words)
	{
		if (!is_array($words)) {
			$words = Strings::splitWords($words);
		}

		if (!$words) {
			return $string;
		}

		$string = @htmlspecialchars($string);
		foreach ($words as $w) {
			$w = @htmlspecialchars($w);
			$string = preg_replace('#(\\b)(' . preg_quote($w, '#') . ')(\\b)#iu', '$1<em>$2</em>$3', $string);
		}

		return $string;
	}

	public function renderUsersource($usersource, $type, array $params = array())
	{
		return App::getSystemService('usersource_manager')->renderView($usersource, $type, $params);
	}

	public function slugify($str)
	{
		return Strings::slugifyTitle($str);
	}

	public function userDate($context, $date, $format = 'F j, Y H:i', $timezone = null)
	{
				if (!is_array($context)) {
			$args = func_get_args();
			if (!isset($args[1])) $args[1] = 'F j, Y H:i';
			if (!isset($args[2])) $args[2] = null;

			list ($date, $format, $timezone) = $args;
			$context = null;
		}

		switch ($format) {
			case 'full':
								$format = App::getSetting('core.date_full');
				break;

			case 'fulltime':
								$format = App::getSetting('core.date_fulltime');
				break;

			case 'day':
								$format = App::getSetting('core.date_day');
				break;

			case 'day_short':
								$format = App::getSetting('core.date_day_short');
				break;

			case 'time':
								$format = App::getSetting('core.date_time');
				break;
		}

		if (!($date instanceof \DateTime)) {
			if (ctype_digit((string) $date)) {
				$date = new \DateTime('@'.$date);
				$date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
			} else {
				try {
					$date_str = $date;
					$date = new \DateTime($date_str);
				} catch (\Exception $e) {}
			}
		}

		if (!($date instanceof \DateTime)) {
			$date_str = (string)$date;
			return "invalid_date($date_str)";
		}

		if ($timezone === null && $context && isset($context['context']['person_timezone'])) {
			$timezone = $context['context']['person_timezone'];
		}

		if ($timezone === null && App::getCurrentPerson()) {
			$timezone = App::getCurrentPerson();
		}

		if ($timezone instanceof \Application\DeskPRO\Entity\Person) {
			$timezone = $timezone->getDateTimezone();
		}

		if (null !== $timezone) {
			if (!($timezone instanceof \DateTimeZone)) {
				$timezone = new \DateTimeZone($timezone);
			}
		}

		if (!$timezone || $timezone == 'UTC') {
			$timezone = new \DateTimeZone('UTC');
		}

		$date->setTimezone($timezone);

		$prefix = 'user.time.';
		if (DP_INTERFACE == 'admin' || DP_INTERFACE == 'agent') {
			$prefix = 'agent.time.';
		}

		return $this->container->getTranslator()->date($format, $date, $prefix);
	}

	public function timeLength($length, $max_unit = null)
	{
		return \Application\DeskPRO\Util::getPrintableTimeLength($length, $max_unit);
	}

	public function formToken($name = '', $field_name = '_dp_security_token')
	{
		$html = '<input type="hidden" name="'.$field_name.'" value="' . App::getSession()->getEntity()->generateSecurityToken($name, 43200) . '" />';
		$html .= '<input type="hidden" name="_rt" value="' . App::getSession()->getEntity()->generateSecurityToken('request_token', 10800) . '" class="dp_request_token" />';

		return $html;
	}

	public function securityToken($name = '', $timeout = 43200)
	{
		return App::getSession()->getEntity()->generateSecurityToken($name, $timeout);
	}

	public function staticSecurityToken($name = '', $timeout = 43200)
	{
		return Util::generateStaticSecurityToken(md5(App::getAppSecret() . $name), $timeout);
	}

	public function staticSecurityTokenSecret($secret, $timeout = 43200)
	{
		return Util::generateStaticSecurityToken($secret, $timeout);
	}

	public function debugVar($var)
	{
		ob_start();
		var_dump($var, true);
		$str = ob_end_clean();

		return $str;
	}

	public function getType($var, $basename = true)
	{
				if (!is_object($var)) {
			$var_type = gettype($var);
				} else {
			$var_type = get_class($var);

			if ($basename) {
				$var_type = Util::getBaseClassname($var_type);
			}

			if ($var instanceof \Doctrine\ORM\Proxy\Proxy) {
				$var_type = preg_replace('#(^|\\\\)ApplicationDeskPROEntity(.*?)Proxy$#', '$2', $var_type);
			}
		}

		return $var_type;
	}

	public function getObjectPath($object, array $params = array(), $context = 'user')
	{
		$generator = $this->container->get('router')->getGenerator();
		return $generator->generateObjectUrl($object, $params, $context);
	}

	public function getObjectPathAgent($object, array $params = array())
	{
		return $this->getObjectPath($object, $params, 'agent');
	}

	public function compareType($var, $type)
	{
				if (!is_object($var)) {
			$var_type = gettype($var);
			return strpos($var_type, $type) !== false;

				} else {
			$var_type = get_class($var);

						return (strpos($var_type, $type) !== false AND Util::getBaseClassname($var_type) == Util::getBaseClassname($type));
		}
	}

	public function flashMessage($name)
	{
		$session = $this->container->get('session');
		return $session->getFlash($name, null);
	}

	public function encNum($num)
	{
		return Util::baseEncode((int)$num, Util::LETTERS_ALPHABET);
	}

	public function decNum($num)
	{
		return Util::baseDecode((int)$num, Util::LETTERS_ALPHABET);
	}

	public function rand($min = 1, $max = 10)
	{
		return mt_rand((int)$min, (int)$max);
	}

	public function isUserGuest($person = null)
	{
		if (!$person) {
			$person = $this->container->get('deskpro.session_person');
		}

		if (!$person['id']) {
			return true;
		}

		return false;
	}

	public function isUserUser($person = null)
	{
		if (!$person) {
			$person = $this->container->get('deskpro.session_person');
		}

		if ($person['id']) {
			return true;
		}

		return false;
	}

	public function isUserAgent($person)
	{
		if (!$person) {
			$person = $this->container->get('deskpro.session_person');
		}

		if ($person['is_agent']) {
			return true;
		}

		return false;
	}

	public function isUserAdmin($person)
	{
		if (!$person) {
			$person = $this->container->get('deskpro.session_person');
		}

		if ($person['is_admin']) {
			return true;
		}

		return false;
	}

	
	public function isPartialRequest()
	{
		return $this->container->get('request')->isPartialRequest();
	}

	public function strRepeat($str, $count = 1)
	{
		return str_repeat($str, $count);
	}

	public function strTrim($str)
	{
		return trim($str);
	}

	
	public function elUid($prefix = 'dp_')
	{
		return $prefix
			   . Util::baseEncode(time() - strtotime('-15 days'), 'base36') 			   . Util::baseEncode(mt_rand(36, 1295), 'base36') 			   . Util::baseEncode(Util::requestUniqueId(), 'base36'); 	}

	
	public function urlDisplay($name, array $parameters = array())
	{
		$url = $this->urlFull($name, $parameters);
		$url = preg_replace('#^https?://(www\.)?#i', '', $url);

		return $url;
	}

	public function urlFull($name, array $parameters = array())
	{
		return $this->container->get('router')->getGenerator()->generateUrl($name, $parameters, false);
	}

	public function helpdeskUrl($path)
	{
		return App::getSetting('core.deskpro_url') . ltrim($path, '/');
	}

	public function urlFragment($name, array $parameters = array())
	{
		return $this->container->get('router')->getGenerator()->generateFragment($name, $parameters, false);
	}

	public function renderCustomField($display_array, array $vars = array())
	{
		$handler = $display_array['handler'];

		if (is_object($display_array)) {
			$display_array = $display_array->toArray();
		}
		$vars = array_merge($display_array, $vars);

		return $handler->renderHtml($display_array['value'], $vars);
	}

	public function renderCustomFieldForm($display_array, array $vars = array())
	{
		$handler = $display_array['handler'];
		$formView = $display_array['formView'];

		if (is_object($display_array)) {
			$display_array = $display_array->toArray();
		}
		$vars = array_merge($display_array, $vars);

		return $handler->renderFormHtml($formView, $vars);
	}

	public function renderCustomFieldText($display_array, array $vars = array())
	{
		$handler = $display_array['handler'];

		if (is_object($display_array)) {
			$display_array = $display_array->toArray();
		}
		$vars = array_merge($display_array, $vars);

		return $handler->renderText($display_array['value'], $vars);
	}

	public function getLanguageHtmlAttributes($language = null)
	{
		if (!($language instanceof \Application\DeskPRO\Entity\Language)) {
			$language = App::getLanguage();
		}

		$attributes = array(
			'dir' => ($language->is_rtl ? 'dir="rtl"' : 'dir="ltr"'),
			'lang' => 'lang="' . @htmlspecialchars(substr($language->locale, 0, 2), \ENT_QUOTES, 'UTF-8') . '"'
		);

		return implode(' ', $attributes);
	}

	public function getLanguageArrow($ltr, $rtl = null, $language = null)
	{
		if ($rtl === null) {
			switch ($ltr) {
				case 'right': $ltr = '&rarr;'; $rtl = '&larr;'; break;
				case 'left': $ltr = '&larr;'; $rtl = '&rarr;'; break;
				default: return 'unknown';
			}
		}

		if (!($language instanceof \Application\DeskPRO\Entity\Language)) {
			$language = App::getLanguage();
		}

		if ($language->is_rtl) {
			return $rtl;
		} else {
			return $ltr;
		}
	}

	public function isRtl($language = null)
	{
		if (!($language instanceof \Application\DeskPRO\Entity\Language)) {
			$language = App::getLanguage();
		}

		return $language->is_rtl;
	}

	public function getPhraseDev($phrase_name, array $vars = array())
	{
		return $this->container->get('deskpro.core.translate')->replaceVarsInString($phrase_name, $vars);
	}

	public function hasPhrase($phrase_name)
	{
		return $this->container->get('deskpro.core.translate')->hasPhrase($phrase_name);
	}

	public function getPhraseText($phrase_name)
	{
		$p = $this->container->get('deskpro.core.translate')->getPhraseText($phrase_name);
		if ($p && dp_get_config('debug.language_test_mode')) {
			$p = "^$p^";
		}

		return $p;
	}

	public function getPhrase($context, $phrase_name, $vars = null, $raw = false)
	{
		if (!$vars || !is_array($vars)) {
			$vars = array();
		}

		if (!$raw) {
			foreach ($vars as &$v) {
				$v = @htmlspecialchars($v, \ENT_QUOTES, 'UTF-8');
			}
		}

		$vars['_context'] = $context;

		return $this->container->get('deskpro.core.translate')->phrase($phrase_name, $vars);
	}

	public function getPhraseObject($phrase_name, $property = null)
	{
		return $this->container->get('deskpro.core.translate')->getPhraseObject($phrase_name, $property);
	}

	public function isDebugMode()
	{
		return App::isDebug();
	}

	public function getMd5($string)
	{
		return md5($string);
	}

	public function assetFull($location)
	{
		$url = App::getSetting('core.deskpro_assets_full_url');
		if (!$url) {
			$url = App::getSetting('core.deskpro_url');
			$url = trim(str_replace('/index.php', '', $url), '/');
			$url .= (App::getConfig('static_path') ?: '/web') . '/';
		}
		return $url . ltrim($location, '/');
	}

	public function rawUrlEncode($str)
	{
		return rawurlencode($str);
	}

	public function strUpper($str)
	{
		return Strings::utf8_strtoupper($str);
	}

	public function strLower($str)
	{
		return Strings::utf8_strtolower($str);
	}

	public function hex2rgb($hex)
	{
		$hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex);
		$rgb = array();
		if (strlen($hex) == 6) {
			$color_val = hexdec($hex);
			$rgb['red'] = 0xFF & ($color_val >> 0x10);
			$rgb['green'] = 0xFF & ($color_val >> 0x8);
			$rgb['blue'] = 0xFF & $color_val;
		} elseif (strlen($hex) == 3) {
			$rgb['red'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
			$rgb['green'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
			$rgb['blue'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
		} else {
			return false;
		}

		return $rgb;
	}

	public function captchaHtml($type = 'default')
	{
		$captcha = $this->container->getSystemObject('form_captcha', array('type' => $type));
		return $captcha->getHtml();
	}

    
    public function getName()
    {
        return 'deskpro_templating';
    }

	public function includeFile($path)
	{
		if (!dp_get_config('enable_include_file')) {
			return '';
		}

		if (!file_exists($path)) {
			$e = new \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException("File does not exist: " . $path);
			\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($e);
			return '';
		}

		return file_get_contents($path);
	}

	public function includePhpFile($path, array $with = null)
	{
		if (!dp_get_config('enable_include_file')) {
			return '';
		}

		if ($with !== null) {
			extract($with, \EXTR_SKIP);
		}

		if (!file_exists($path)) {
			$e = new \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException("File does not exist: " . $path);
			\DeskPRO\Kernel\KernelErrorHandler::logException($e, false, 'tpl_include_php_file');
			return '';
		}

		ob_start();
		include($path);
		$content = ob_get_clean();

		return $content;
	}

	public function dumpVar($var)
	{
		return \DeskPRO\Kernel\KernelErrorHandler::varToString($var);
	}

	public function urlTrimScheme($url, $trim_adv = false)
	{
		$ret = preg_replace('#^https?://#i', '', $url);

		if ($trim_adv) {
			$ret = preg_replace('#^www\.#i', '', $ret);

						$ret = preg_replace('#([a-zA-Z0-9\-_]+)=([a-zA-Z0-9]{32})&?#', '', $ret);
			$ret = preg_replace('#([a-zA-Z0-9\-_]+)=([a-zA-Z0-9]{40})&?#', '', $ret);
			$ret = preg_replace('#([a-zA-Z0-9\-_]+)=([a-zA-Z0-9]{6})\-([a-zA-Z]{10})\-([a-zA-Z0-9]{40})&?#', '', $ret);

			$ret = trim($ret, '/?#&');

						$ret = preg_replace('#/index\.(html|php)#', '', $ret);
		}

		return $ret;
	}

	public function countryName($code)
	{
		$name = Countries::getCountryFromCode($code);
		if (!$name) {
			return $code;
		}

		return $name;
	}

	protected $_widgetCache = array();

	public function getWidgets($baseId, $page, $location, $position = '*', $data = array())
	{
		$widgets = $this->_getPageLocationWidgets($page, $location, $position);
		if (!$widgets) {
			return '';
		}

		$output = '';
		foreach ($widgets AS $widget) {
			$output .= $this->_insertWidget($baseId, $widget,
				'<div class="profile-box-container" id="{id}_container">'
					. '<header><h4 id="{id}_tab">{title}</h4></header>'
					. '<section class="widget-content" id="{id}" data-widget="{widget}">{html}</section>'
				. '</div>',
				$data
			);
		}

		return $output;
	}

	public function getWidgetsRaw($page, $location, $position = '')
	{
		return $this->_getPageLocationWidgets($page, $location, $position);
	}

	protected function _getPageLocationWidgets($page, $location, $position = '')
	{
		if (!array_key_exists($page, $this->_widgetCache)) {
			$this->_widgetCache[$page] = App::getEntityRepository('DeskPRO:Widget')->getEnabledPageWidgetsGrouped($page);
		}

		if (empty($this->_widgetCache[$page][$location])) {
			return array();
		} else {
			if ($position === '') {
				$output = array();
				foreach ($this->_widgetCache[$page][$location] AS $widgets) {
					foreach ($widgets AS $widget) {
						$output[] = $widget;
					}
				}
				return $output;
			} else if (!empty($this->_widgetCache[$page][$location][$position])) {
				return $this->_widgetCache[$page][$location][$position];
			} else {
				return array();
			}
		}
	}

	public function getWidgetHtmlId($baseId, \Application\DeskPRO\Entity\Widget $widget)
	{
		return "{$baseId}-widget-{$widget->id}";
	}

	protected function _insertWidget($baseId, \Application\DeskPRO\Entity\Widget $widget, $wrapper, $data = array())
	{
		$jsOnly = !$widget->page_location;
		$htmlId = ($jsOnly ? '' : $this->getWidgetHtmlId($baseId, $widget));

		if (!is_array($data) && !($data instanceof \ArrayAccess)) {
			$data = array();
		}
		$data['base_id'] = $baseId;
		$data['html_id'] = $htmlId;
		$data['settings'] = App::get(App::SERVICE_SETTINGS);

		if ($jsOnly) {
			$output = '';
		} else {
			$output = strtr($wrapper, array(
				'{id}' => $htmlId,
				'{widget}' => $widget->id,
				'{html}' => $this->_replaceWidgetPlaceholders($widget->html, $data, 'html'),
				'{title}' => $widget->title
			));
		}

		if ($widget->css) {
			$css = $this->_replaceWidgetPlaceholders($widget->css, $data, 'css');
			$hash = md5($css);
			$output .= '<style type="text/css" data-widget="' . $widget->id . '" data-hash="' . $hash . '">' . $css . '</style>';
		}
		if ($widget->js) {
			$js = $this->_replaceWidgetPlaceholders($widget->js, $data, 'js');
			$output .= '<script type="text/javascript" data-widget="' . $widget->id . '" data-html-id="' . $htmlId . '">'
				. $js . '</script>';
		}

		return $output;
	}

	protected function _replaceWidgetPlaceholders($content, $data, $context)
	{
		return preg_replace_callback('/\{\{\s*([a-z0-9_.]+)\s*\}\}/i', function (array $match) use ($data, $context) {
			$parts = explode('.', $match[1]);
			$reference = $data;
			while (($part = array_shift($parts)) !== null) {
				if ($part == '') {
					continue;
				}

				if (!is_array($reference) && !($reference instanceof \ArrayAccess)) {
					$reference = '';
					break;
				}

				if (isset($reference[$part])) {
					$reference = $reference[$part];

					if ($reference instanceof \Application\DeskPRO\Settings\Settings) {
						$reference = ($parts ? $reference[implode('.', $parts)] : '');
						break;
					}
				} else {
					$reference = '';
					break;
				}
			}

			$reference = strval($reference);

			switch ($context) {
				case 'html':
					return @htmlspecialchars($reference);

				case 'js':
					return strtr($reference, array(
						'"' => '\\"',
						"'" => "\\'",
						"\n" => '\n',
						"\r" => '\r',
						'\\' => '\\\\',
						'</script>' => '<\\/script>'
					));

				default:
					return $reference;
			}
		}, $content);
	}

	public function getWidgetTabsHeader($baseId, $page, $location, array $tabs)
	{
		$originalCount = count($tabs);

		foreach ($this->_getPageLocationWidgets($page, $location, 'tab') AS $widget) {
			$htmlId = $this->getWidgetHtmlId($baseId, $widget);
			$tabs[$htmlId] = $widget->title;
		}

		foreach ($tabs AS $key => $title) {
			if ($title === false) {
				unset($tabs[$key]);
			}
		}

		if (!$tabs) {
			return '';
		} else if (count($tabs) == 1 && $originalCount == 1) {
			return '<h4>' . reset($tabs) . '</h4>';
		} else {
			$tabHtml = array();
			$on = false;
			foreach ($tabs AS $id => $title) {
				if (!$on) {
					$onHtml = ' class="on"';
					$on = true;
				} else {
					$onHtml = '';
				}
				$tabHtml[] = '<li data-tab-for="#' . $id . '" id="' . $id . '_tab"' . $onHtml . '>' . $title . '</li>';
			}
			return '<nav data-element-handler="DeskPRO.ElementHandler.SimpleTabs"><ul>' . implode('', $tabHtml) . '</ul></nav>';
		}
	}

	public function getWidgetTabsBody($baseId, $page, $location, $wrapper, $data = array())
	{
		$output = '';
		foreach ($this->_getPageLocationWidgets($page, $location, 'tab') AS $widget) {
			$output .= $this->_insertWidget($baseId, $widget,
				'<' . $wrapper . ' class="widget-content" id="{id}" data-widget="{widget}" style="display: none">{html}</' . $wrapper . '>',
				$data
			);
		}

		return $output;
	}

	public function getJsSsoLoader()
	{
		$person = App::getCurrentPerson();
		$is_first_page = App::getSession()->isFirstPage();

		$sources = App::getEntityRepository('DeskPRO:Usersource')->getJsSsoUsersources();
		$output = array();
		foreach ($sources AS $source) {
			$adapter = $source->getAdapter()->getAuthAdapter();
			$output[] = $adapter->getSsoHtmlLoaderOutput($source, $this, $person, $is_first_page);
		}

		return implode("\n\n", $output);
	}

	public function getJsSsoShare()
	{
		$person = App::getCurrentPerson();

		if (!$person || $person->isGuest()) {
			return '';
		}

		$output = array();
		foreach ($person->usersource_assoc as $assoc) {
			$us = $assoc->usersource;
			if (!$us->isCapable('share_session')) {
				continue;
			}

			$adapter = $us->getAdapter()->getAuthAdapter();
			$output[] = $adapter->getSsoShareSessionHtml($assoc->identity);
		}

		return implode("\n\n", $output);
	}

	
	public function getArrayAttribute($array, $key)
	{
		return array_key_exists($key, $array) ? $array[$key] : null;
	}


	public function countLines($str)
	{
		if (is_object($str) && method_exists($str, '__toString')) {
			$str = (string)$str;
		}
		if (!is_scalar($str)) {
			return 0;
		}

		$str = Strings::standardEol($str);
		return substr_count($str, "\n") + 1;
	}

	public function min()
	{
		$args = func_get_args();
		return call_user_func_array('min', $args);
	}

	public function max()
	{
		$args = func_get_args();
		return call_user_func_array('max', $args);
	}

	public function plain_template_filter($content)
	{
		$content = preg_replace('#<\s*script#i', '<deskpro_script', $content);
		$content = preg_replace('#<\s*/\s*script#i', '</deskpro_script', $content);
		return $content;
	}

	public function match($str, $regex)
	{
		$regex = Strings::getInputRegexPattern($regex);
		if (!$regex) {
			return false;
		}

		return preg_match($regex, $str);
	}

	public function set_tplvar($context, $k, $v)
	{
		if (!isset($context['tplvars'])) {
			$context['tplvars'] = new \stdClass();
		}

		$context['tplvars']->$k = $v;
		return;
	}

	public function getTplSourceTemplate($id, $name)
	{
		$source = App::getContainer()->getTemplating()->getSource($name);
		$source = str_replace('<script>',  '%startScript%', $source);
		$source = str_replace('</script>',  '%endScript%', $source);
		$source = '<script type="text/x-deskpro-tmpl" id="'.$id.'">' . $source . '</script>';
		return $source;
	}

	public function smartWrap($string, $len = 50, $break = null)
	{
		if ($break === null) {
			$break = Strings::ZERO_WIDTH_SPACE;
		}
		return Strings::smartWordWrap($string, $len, $break);
	}
}

function deskpro_twig_filter_dummy($ret) {
	return $ret;
}}
 




namespace Application\DeskPRO\Twig\Loader
{

use Application\DeskPRO\App;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Config\FileLocatorInterface;


class HybridLoader extends \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader
{
	
	protected $style = null;

	
	protected $style_template_info = null;

	
	protected $crashed_custom_templates = array();

	public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser)
	{
		parent::__construct($locator, $parser);
	}

	protected function _initStyle()
	{
				if ($this->style !== null) return;

		if (!defined('DP_BUILDING')) {
			$this->style = App::getSystemService('style');
			$this->style_template_info = App::getDb()->fetchAllKeyed("
				SELECT id, name, UNIX_TIMESTAMP(date_updated) AS date_updated
				FROM templates
				WHERE style_id = ?
			", array($this->style['id']), 'name');
		} else {
			$this->style = new \Application\DeskPRO\Entity\Style();
		}
	}

	public function markCustomTemplateAsCrashed($name)
	{
		$this->crashed_custom_templates[$name] = true;
	}

	public function dbHasTemplate($name)
	{
		if (isset($this->crashed_custom_templates[(string)$name])) {
			return false;
		}

		$this->_initStyle();
		if (isset($this->style_template_info[(string)$name])) {
			return true;
		}
		return false;
	}

	public function isFresh($name, $time)
    {
		$this->_initStyle();

		$str_name = (string)$name;

						if (!isset($this->crashed_custom_templates[$str_name]) && isset($this->style_template_info[$str_name])) {
			return true;
		}

        return parent::isFresh($name, $time);
    }

	public function getCacheKey($name)
    {
		$this->_initStyle();
		return md5((string)$name);
    }

	public function getSource($name)
    {
		$this->_initStyle();

		$str_name = (string)$name;
		if (!isset($this->crashed_custom_templates[$str_name]) && isset($this->style_template_info[$str_name])) {
			return App::getDb()->fetchColumn("
				SELECT template_code
				FROM templates
				WHERE id = ?
			", array($this->style_template_info[$name]['id']));
		}

		$source = file_get_contents($this->findTemplate($name));

		if (strpos($name, 'DeskPRO:emails_') !== false) {
			$proc = new \Application\DeskPRO\Twig\PreProcessor\EmailPreProcessor();
			$source = $proc->process($source, $str_name);
		}

		return $source;
    }

	protected function findTemplate($template)
	{
		$this->_initStyle();

		$logicalName = (string)$template;

		if (!isset($this->crashed_custom_templates[$logicalName]) && isset($this->style_template_info[$logicalName])) {
			return false;
		}

		return parent::findTemplate($template);
	}
}
}
