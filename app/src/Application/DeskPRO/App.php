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

namespace Application\DeskPRO;

use Application\DeskPRO\Entity;

use Application\DeskPRO\People\PersonGuest;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * A global singleton that facilitates fetching well known objects and values.
 *
 * @static
 */
class App
{
	const DEFAULT_NAME = '__default__';

	/**#@+
	 * Names of common services
	 */
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
	/**#@-*/

	/**
	 * An array of registered containers
	 * @var array
	 */
	protected static $_containers = array();

	/**
	 * An array of service=>containername
	 * @var array
	 */
	protected static $_service_to_container = array();

	/**
	 * The container we'll use by default when DEFAULT_NAME is specified
	 * @var string
	 */
	protected static $_default_contaner_name = 'default';

	/**
	 * An array of loaded config files.
	 * @var array
	 */
	protected static $_fileconfig = array();

	/**
	 * Currently booted environment
	 * @var string
	 */
	protected static $_environment = null;

	/**
	 * Is debug mode enabled?
	 * @var bool
	 */
	protected static $_debug = false;

	/**
	 * The Kernel
	 * @var Application\DeskPRO\Kernel\Kernel
	 */
	protected static $_kernel = null;

	/**
	 * Array of instantiated API handlers
	 * @var array
	 */
	protected static $_api_handlers = null;

	/**
	 * The person who is making the request, or the person who is authorizing
	 * the request.
	 *
	 * @var Person
	 */
	protected static $_current_person = null;

	/**
	 * Standard loggers
	 * @var array
	 */
	protected static $_standard_loggers = null;

	/**
	 * If true, will make sure that guest page caching is disable going ahead
	 *
	 * @var bool
	 */
	protected static $_skip_caching = false;

	/**
	 * If true, the current page will not be cached (but the user will still hit
	 * the cache where possible).
	 *
	 * @var bool
	 */
	protected static $_uncachable = false;


	/**
	 * Set the person who is making the request, or the person who is authorizing
	 * the request.
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public static function setCurrentPerson(Entity\Person $person = null)
	{
		if (!$person) {
			$person = new PersonGuest();
		}
		self::$_current_person = $person;
	}


	/**
	 * Get the person who is making the curent request.
	 *
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public static function getCurrentPerson()
	{
		return self::$_current_person;
	}


	/**
	 * Set the kernel
	 *
	 * @param \Application\DeskPRO\Kernel\Kernel $kernel
	 */
	public static function setKernel(\Symfony\Component\HttpKernel\Kernel $kernel)
	{
		self::$_kernel = $kernel;
		self::$_environment = $kernel->getEnvironment();
		self::$_debug = $kernel->isDebug();
	}


	/**
	 * Set a container we'll use in the App to fetch various services
	 *
	 * @param ContainerInterface $container The container
	 * @param string             $name      A name for the container to reference it (such as 'default')
	 */
	public static function setContainer(ContainerInterface $container, $name)
	{
		if (isset(self::$_containers[$name])) {
			throw new \InvalidArgumentException("The container with `$name` has already been set");
		}

		self::$_containers[$name] = $container;
	}


	/**
	 * Get the autoloader
	 *
	 * @var \Orb\Util\ClassLoader
	 */
	public static function getClassLoader()
	{
		if (isset($GLOBALS['DP_AUTOLOADER'])) {
			return $GLOBALS['DP_AUTOLOADER'];
		}

		return null;
	}


	/**
	 * Get a registered container.
	 *
	 * @param string $name
	 * @return \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
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


	/**
	 * Set the default container to use when using DEFAULT_NAME, or when no service-to-container
	 * map has been specified.
	 *
	 * @param string $name
	 */
	public static function setDefaultContainer($name)
	{
		self::$_default_contaner_name = $name;
	}


	/**
	 * Set the default container to fetch from when using DEFAULT_NAME with a specific
	 * service.
	 *
	 * @param string $service_name
	 * @param string $container_name
	 */
	public static function setDefaultContainerForService($service_name, $container_name)
	{
		self::$_service_to_container[$service_name] = $container_name;
	}


	/**
	 * Get a service from some container.
	 *
	 * Supply null as $container_name and we'll go through all registered containers
	 * and return the first found.
	 *
	 * @param string $service_name    The service to get
	 * @param string $container_name  The container to get it from.
	 * @return mixed
	 */
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


	/**
	 * Get a system service
	 *
	 * @param $service_name
	 * @param string $container_name
	 * @return mixed
	 */
	public static function getSystemService($service_name, $container_name = self::DEFAULT_NAME)
	{
		if ($container_name == self::DEFAULT_NAME AND isset(self::$_service_to_container[$service_name])) {
			$container_name = self::$_service_to_container[$service_name];
		}

		$container = self::getContainer($container_name);
		return $container->getSystemService($service_name);
	}

	/**
	 * @param string $id
	 * @return \Application\DeskPRO\DependencyInjection\SystemServices\BaseRepositoryService
	 */
	public static function getDataService($id)
	{
		return self::getContainer(self::DEFAULT_NAME)->getSystemService($id . 'Data');
	}


	/**
	 * Get a system service
	 *
	 * @param $service_name
	 * @param string $container_name
	 * @return mixed
	 */
	public static function getSystemObject($service_name, array $options = array(), $container_name = self::DEFAULT_NAME)
	{
		if ($container_name == self::DEFAULT_NAME AND isset(self::$_service_to_container[$service_name])) {
			$container_name = self::$_service_to_container[$service_name];
		}

		$container = self::getContainer($container_name);
		return $container->getSystemObject($service_name, $options);
	}


	/**
	 * Check if a service exists..
	 *
	 * @param string $service_name    The service to get
	 * @param string $container_name  The container to get it from.
	 */
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


	/**
	 * Get the search adapter.
	 *
	 * @return \Application\DeskPRO\Search\Adapter\MysqlAdapter
	 */
	public static function getSearchAdapter()
	{
		return self::get(self::SERVICE_SEARCH);
	}


	/**
	 * Get the DB abstraction object.
	 *
	 * @return \Application\DeskPRO\DBAL\Connection
	 */
	public static function getDb()
	{
		return self::get(self::SERVICE_DB, self::DEFAULT_NAME);
	}

	protected static $db_read;

	/**
	 * Gets a read-only DB connect
	 *
	 * @return \Application\DeskPRO\DBAL\Connection
	 */
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


	/**
	 * Get the ORM entity manager.
	 *
	 * @return \Application\DeskPRO\ORM\EntityManager
	 */
	public static function getOrm()
	{
		return self::get(self::SERVICE_ORM);
	}


	/**
	 * Get the request
	 *
	 * @return \Symfony\Component\HttpFoundation\Request
	 */
	public static function getRequest()
	{
		return self::get(self::SERVICE_REQUEST);
	}


	/**
	 * Get the response
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public static function getResponse()
	{
		return self::get(self::SERVICE_RESPONSE);
	}


	/**
	 * Get the session
	 *
	 * @return \Application\DeskPRO\HttpFoundation\Session
	 */
	public static function getSession()
	{
		return self::get(self::SERVICE_SESSION);
	}


	/**
	 * Get the mailer
	 *
	 * @return \Application\DeskPRO\Mail\Mailer
	 */
	public static function getMailer()
	{
		return self::get(self::SERVICE_MAILER);
	}


	/**
	 * Get the translator
	 *
	 * @return \Application\DeskPRO\Translate\Translate
	 */
	public static function getTranslator()
	{
		return self::get(self::SERVICE_TRANSLATOR);
	}


	/**
	 * Get the current language in use
	 *
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public static function getLanguage()
	{
		return self::getTranslator()->getLanguage();
	}


	/**
	 * Get the templating service
	 *
	 * @return \Application\DeskPRO\Templating\Engine
	 */
	public static function getTemplating()
	{
		return self::get(self::SERVICE_TEMPLATING);
	}


	/**
	 * Get the router
	 *
	 * @return \Symfony\Component\Routing\Router
	 */
	public static function getRouter()
	{
		return self::get(self::SERVICE_ROUTER);
	}


	/**
	 * Get the app event dispatcher
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\ContainerAwareEventDispatcher
	 */
	public static function getEventDispatcher()
	{
		return self::get(self::SERVICE_EVENT_DISPATCHER);
	}


	/**
	 * Get the form factory
	 *
	 * @return \Symfony\Component\Form\FormFactory
	 */
	public static function getFormFactory()
	{
		return self::get(self::SERVICE_FORM_FACTORY);
	}


	/**
	 * Get the searcher
	 *
	 * @return \Application\DeskPRO\Search\Adapter\AbstractAdapter
	 */
	public static function getSearchEngine()
	{
		return self::get(self::SERVICE_SEARCH_ENGINE);
	}


	/**
	 * Get the person activity logger
	 *
	 * @return \Application\DeskPRO\People\ActivityLogger\ActivityLogger
	 */
	public static function getPersonActivityLogger()
	{
		return self::get(self::SERVICE_PERSON_ACTIVITY_LOGGER);
	}


	/**
	 * True if this is an http request. We should have a request and response object if so.
	 *
	 * @return bool
	 */
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


	/**
	 * Get a repository from the entity manager.
	 * This is a shortcut for App::getOrm()->getRepository().
	 *
	 * @param \Doctrine\ORM\EntityRepository
	 */
	public static function getEntityRepository($entity)
	{
		return self::get(self::SERVICE_ORM)->getRepository($entity);
	}


	/**
	 * Get a repository and find an entity.
	 *
	 * Shortcut for App::getEntityRepository($entity)->find($id);
	 *
	 * @return mixed
	 */
	public static function findEntity($entity, $id)
	{
		return self::getEntityRepository($entity)->find($id);
	}


	/**
	 * Get the classname for an entity
	 *
	 * @return string
	 */
	public static function getEntityClass($entity)
	{
		list ($namespace, $entity) = explode(':', $entity, 2);

		$class = "Application\\$namespace\\Entity\\$entity";

		return $class;
	}


	/**
	 * Get the kernel
	 *
	 * @return \Application\DeskPRO\Kernel\Kernel
	 */
	public static function getKernel()
	{
		if (!self::$_kernel) {
			throw new \RuntimeException('No kernel has been set yet');
		}

		return self::$_kernel;
	}


	/**
	 * Get the type of kernel being used:
	 * - agent
	 * - cli
	 * - sys
	 * - user
	 *
	 * @return string
	 */
	public static function getKernelType()
	{
		$kernel = self::getKernel();

		$type = get_class($kernel);
		$type = preg_replace('#^.*?\\\\([a-zA-Z]+)Kernel$#', '$1', $type);
		$type = strtolower($type);

		return $type;
	}


	/**
	 * Get a secret key used for various hashing.
	 *
	 * @return string
	 */
	public static function getAppSecret()
	{
		$secret = self::getSetting('core.app_secret');
		if (!$secret) {
			$secret = 'secret';
		}

		return $secret;
	}


	/**
	 * Get the reference generator
	 *
	 * @return \Application\DeskPRO\RefGenerator\RefGeneratorInterface
	 */
	public static function getRefGenerator()
	{
		return self::getContainer()->getSystemService('RefGenerator');
	}


	/**
	 * Get the value of a setting.
	 *
	 * @param string $name The name of the setting to get
	 * @return string
	 */
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

	/**
	 * Get an API handler. It will be instantiated if it hasn't been already.
	 * This is a DeskPRO-specific way to instantiate services that doesn't use
	 * the usual DI handler used in Symfony.
	 *
	 * They will likely move to a dedicated DI later. For now, just hard-code
	 * them in here.
	 *
	 * @deprecated All of these should be services, or created as "system services"
	 * @param string $name Name of the API handler
	 */
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


	/**
	 * Get the filesystem directory where we want to store cache files.
	 *
	 * @return string
	 */
	public static function getCacheDir()
	{
		return self::$_kernel->getCacheDir();
	}


	/**
	 * Get the filesystem directory where log files are stored.
	 *
	 * @return string
	 */
	public static function getLogDir()
	{
		if (!self::$_kernel) return '';
		return self::$_kernel->getLogDir();
	}


	/**
	 * Get the current env
	 *
	 * @return string
	 */
	public static function getEnvironment()
	{
		return self::$_environment;
	}


	/**
	 * Is debug mode enabled?
	 *
	 * @return bool
	 */
	public static function isDebug()
	{
		return self::$_debug;
	}


	/**
	 * Check if we're currently running in CLI
	 *
	 * @return bool
	 */
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


	/**
	 * Loads userconfig from the filesystem
	 * @param string $name The name of the user config
	 */
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


	/**
	 * Read a config array from a standardly named config file.
	 *
	 * @throws \RuntimeException|\UnexpectedValueException
	 * @param string $name
	 * @return array
	 */
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


	/**
	 * Get a config value from config.
	 *
	 * If $config_name is null, then entire config array from the file will be returned.
	 * $config_name can use dot notation to denote deep array keys.
	 *
	 * @param string $config_name  The config value to get
	 * @param mixed  $default      The value to return if no such key exists
	 * @param string $file_name    The file to fetch it form
	 */
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


	/**
	 * Get a new logger for some kind of thing/session
	 *
	 * @param string $log_name
	 * @param string $session_name
	 * @return \Application\DeskPRO\Log\Logger
	 */
	public static function createNewLogger($log_name, $session_name)
	{
		if ($log_name == 'error_log') {
			$logger = new \Application\DeskPRO\Log\DbErrorLogger();
		} else {
			$logger = new \Application\DeskPRO\Log\Logger();
		}
		$logger->setLogName($log_name);
		$logger->setSessionName($session_name);

		// Indent formatter by default
		$indent_filter = new \Orb\Log\Filter\IndentFilter();
		$logger->addFilter($indent_filter);

		// Writer to the DB
		if (strpos($log_name, 'worker_job') !== 0 || !defined('DP_DISABLE_DBCRONLOG')) {
			$writer = new \Application\DeskPRO\Log\Writer\LogItemEntity();
			$writer->addFilter(new \Orb\Log\Filter\PriorityFilter(\Orb\Log\Logger::INFO));
			$logger->addWriter($writer);
		}

		return $logger;
	}


	/**
	 * Log a single error message to the standard error_log with subtype $type.
	 *
	 * @param string $type A type identifier
	 * @param int|string $priority The priority
	 * @param string $message The error message
	 * @param array $data Additional debug info
	 */
	public static function logErrorMessage($type, $priority, $message, array $data = array())
	{
		$logger = self::createNewLogger('error_log.'.$type, null);
		$logger->log($message, $priority, $data);
	}


	/**
	 * Get information about the DeskPRO system bundles.
	 *
	 * @static
	 * @return array
	 */
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
