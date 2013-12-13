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
 * @category DependencyInjection
 */

namespace Application\DeskPRO\DependencyInjection;

use Orb\Util\Arrays;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * This is an extension to the DI container that knows how to initialize
 * some services if they aren't registered and if they have a corresponding
 * factory in SystemServices.
 *
 * These services and factories use already registered services such as settings or database connections
 * to create themselves lazily.
 */
class DeskproContainer extends Container
{
	/**#@+
	 * Names of common services
	 */
	const SERVICE_DB                 = 'database_connection';
	const SERVICE_ORM                = 'doctrine.orm.entity_manager';
	const SERVICE_EM                 = 'doctrine.orm.entity_manager';
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
	 * @var \DeskPRO\Kernel\BaseAbstractKernel
	 */
	public $kernel;

	/**
	 * @var array
	 */
	protected $system_services = array();


	/**
	 * @return \DeskPRO\Kernel\BaseAbstractKernel
	 */
	public function getKernel()
	{
		return $this->kernel;
	}


	public function __construct(ParameterBagInterface $parameterBag = null)
	{
		$GLOBALS['DP_CONTAINER'] = $this;
		parent::__construct($parameterBag);
	}


	/**
	 * Checks if a service has been initialized.
	 *
	 * has() checks if a service has been initialized OR if it has a definition to create it.
	 * This just checks if a service has been initialized. You use this when you want to see
	 * if a certain service has been created already, and you'd use has() to see if a service can be used.
	 *
	 * @param $id
	 * @return bool
	 */
	public function isServiceInitialized($id)
	{
		return isset($this->services[$id]);
	}


	/**
	 * This returns a reference to a system service.
	 *
	 * @throws \InvalidArgumentException
	 * @param string $id
	 * @return mixed
	 */
	public function getSystemService($id)
	{
		if (isset($this->system_services[$id])) {
			return $this->system_services[$id];
		}

		$classname = 'Application\\DeskPRO\\DependencyInjection\\SystemServices\\' . $this->camelize($id) . 'Service';
		$options = null;

		if (!class_exists($classname)) {
			if ($ent = \Orb\Util\Strings::extractRegexMatch('#^(.*?)Data$#', $id, 1)) {
				$classname = 'Application\\DeskPRO\\DependencyInjection\\SystemServices\\BaseRepositoryService';
				$options = array('entity' => 'DeskPRO:' . ucfirst($ent));
			} else {
				throw new \InvalidArgumentException("Invalid service `$id`, tried class `$classname`");
			}
		}

		if ($options) {
			$obj = $classname::create($this, $options);
		} else {
			$obj = $classname::create($this);
		}

		$this->system_services[$id] = $obj;

		return $obj;
	}


	/**
	 * @return \Application\DeskPRO\Plugin\PluginRepository
	 */
	public function getPlugins()
	{
		return $this->getSystemService('plugins');
	}


	/**
	 * @param string $id
	 * @return \Application\DeskPRO\DependencyInjection\SystemServices\BaseRepositoryService
	 */
	public function getDataService($id)
	{
		return $this->getSystemService($id . 'Data');
	}


	/**
	 * This calls a system factory and returns a new instance of some kind of object.
	 *
	 * @throws \InvalidArgumentException
	 * @param string $id
	 * @return mixed
	 */
	public function getSystemObject($id, array $options = array())
	{
		$classname = 'Application\\DeskPRO\\DependencyInjection\\SystemServices\\' . $this->camelize($id) . 'Factory';

		if (!class_exists($classname)) {
			throw new \InvalidArgumentException("Invalid factory `$id`");
		}

		$options = new \Orb\Util\CheckedOptionsArray($options);
		$obj = $classname::create($this, $options);
		return $obj;
	}


	/**
	 * Get the autoloader
	 *
	 * @var \Orb\Util\ClassLoader
	 */
	public function getClassLoader()
	{
		if (isset($GLOBALS['DP_AUTOLOADER'])) {
			return $GLOBALS['DP_AUTOLOADER'];
		}

		return null;
	}


	/**
	 * @return \Orb\Input\Reader\Reader
	 */
	public function getIn()
	{
		return $this->get(self::SERVICE_INPUT_READER);
	}


	/**
	 * @return \Orb\Input\Cleaner\Cleaner
	 */
	public function getInputCleaner()
	{
		return $this->get(self::SERVICE_INPUT_CLEANER);
	}


	/**
	 * Get the search adapter.
	 *
	 * @return \Application\DeskPRO\Search\Adapter\AbstractAdapter
	 */
	public function getSearchAdapter()
	{
		return $this->get(self::SERVICE_SEARCH);
	}


	/**
	 * Get the DB abstraction object.
	 *
	 * @return \Application\DeskPRO\DBAL\Connection
	 */
	public function getDb()
	{
		return $this->get(self::SERVICE_DB);
	}


	/**
	 * @deprecated Use getEm instead.
	 */
	public function getOrm()
	{
		return $this->get(self::SERVICE_ORM);
	}



	/**
	 * Get the entity manager.
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEm()
	{
		return $this->get(self::SERVICE_EM);
	}



	/**
	 * Get the request
	 *
	 * @return \Symfony\Component\HttpFoundation\Request
	 */
	public function getRequest()
	{
		return $this->get(self::SERVICE_REQUEST);
	}


	/**
	 * Get the response
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getResponse()
	{
		return $this->get(self::SERVICE_RESPONSE);
	}


	/**
	 * Get the session
	 *
	 * @return \Application\DeskPRO\HttpFoundation\Session
	 */
	public function getSession()
	{
		return $this->get(self::SERVICE_SESSION);
	}


	/**
	 * Get the mailer
	 *
	 * @return \Application\DeskPRO\Mail\Mailer
	 */
	public function getMailer()
	{
		return $this->get(self::SERVICE_MAILER);
	}


	/**
	 * Get the translator
	 *
	 * @return \Application\DeskPRO\Translate\Translate
	 */
	public function getTranslator()
	{
		return $this->get(self::SERVICE_TRANSLATOR);
	}


	/**
	 * Get the templating service
	 *
	 * @return \Application\DeskPRO\Templating\Engine
	 */
	public function getTemplating()
	{
		return $this->get(self::SERVICE_TEMPLATING);
	}


	/**
	 * Get the router
	 *
	 * @return \Application\DeskPRO\Routing\Router
	 */
	public function getRouter()
	{
		return $this->get(self::SERVICE_ROUTER);
	}


	/**
	 * Get the app event dispatcher
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\ContainerAwareEventDispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->get(self::SERVICE_EVENT_DISPATCHER);
	}


	/**
	 * Get the form factory
	 *
	 * @return \Symfony\Component\Form\FormFactory
	 */
	public function getFormFactory()
	{
		return $this->get(self::SERVICE_FORM_FACTORY);
	}


	/**
	 * Get the searcher
	 *
	 * @return \Application\DeskPRO\Search\Adapter\AbstractAdapter
	 */
	public function getSearchEngine()
	{
		return $this->get(self::SERVICE_SEARCH_ENGINE);
	}


	/**
	 * @return \Imagine\Image\ImagineInterface
	 */
	public function getImagine()
	{
		return $this->getSystemService('imagine');
	}


	/**
	 * Get the person activity logger
	 *
	 * @return \Application\DeskPRO\People\ActivityLogger\ActivityLogger
	 */
	public function getPersonActivityLogger()
	{
		return $this->get(self::SERVICE_PERSON_ACTIVITY_LOGGER);
	}


	/**
	 * Get the reference generator
	 *
	 * @return \Application\DeskPRO\RefGenerator\RefGeneratorInterface
	 */
	public function getRefGenerator()
	{
		return $this->getSystemService('RefGenerator');
	}


	/**
	 * Get the queuer
	 *
	 * @return \Application\DeskPRO\Queue\Queue
	 */
	public function getQueue($name)
	{
		$adapter = new \Application\DeskPRO\Queue\Adapter\QueueItemEntity(array('em' => $this->getEm(), 'name' => $name));
		$queue = new \Application\DeskPRO\Queue\Queue($adapter, array('name' => $name));

		return $queue;
	}


	/**
	 * @return \Application\DeskPRO\Attachments\AcceptAttachment
	 */
	public function getAttachmentAccepter()
	{
		return $this->getSystemService('attachment_accepter');
	}


	/**
	 * @return \Application\DeskPRO\BlobStorage\DeskproBlobStorage
	 */
	public function getBlobStorage()
	{
		return $this->getSystemService('blob_storage');
	}


	/**
	 * @return \Application\DeskPRO\AgentAlert\AlertSender
	 */
	public function getAgentAlertSender()
	{
		return $this->getSystemService('agent_alert_sender');
	}


	/**
	 * Get the value of a setting.
	 *
	 * @param string $name The name of the setting to get
	 * @return string
	 */
	public function getSetting($name, $default = null)
	{
		$settings = $this->get(self::SERVICE_SETTINGS);
		return $settings->get($name, $default);
	}


	/**
	 * Get the settings object
	 *
	 * @return \Application\DeskPRO\Settings\Settings
	 */
	public function getSettingsHandler()
	{
		$settings = $this->get(self::SERVICE_SETTINGS);
		return $settings;
	}


	/**
	 * Get a value from the main system configuration
	 *
	 * @param string $name
	 * @param mixed  $default
	 * @return mixed
	 */
	public function getSysConfig($name, $default = null)
	{
		if ($name == '*') {
			return $GLOBALS['DP_CONFIG'];
		}

		$value = dp_get_config($name, $default);

		return $value;
	}


	/**
	 * @return \Application\DeskPRO\DependencyInjection\SystemServices\AgentDataService
	 */
	public function getAgentData()
	{
		return $this->getDataService('Agent');
	}


	/**
	 * @return \Application\DeskPRO\DependencyInjection\SystemServices\LanguageDataService
	 */
	public function getLanguageData()
	{
		return $this->getDataService('Language');
	}


	/**
	 * @return \Application\DeskPRO\Translate\ObjectLangRepository
	 */
	public function getObjectLangRepository()
	{
		return $this->getSystemService('object_lang_repository');
	}


	/**
	 * @return \Orb\GeoIp\AbstractGeoIp
	 */
	public function getGeoIp()
	{
		return $this->getSystemService('geo_ip');
	}


	/**
	 * Get the path to PHP executable used on the CLI.
	 *
	 * Returns false if the path could not be found and if 'php_path' in config is not set.
	 *
	 * @return string
	 */
	public function getPhpBinaryPath()
	{
		return dp_get_php_path();
	}


	/**
	 * Get the path to mysqldump executable used on the CLI.
	 *
	 * Returns false if the path could not be found and if 'mysqldump_path' in config is not set.
	 *
	 * @return string
	 */
	public function getMysqldumpBinaryPath()
	{
		return dp_get_mysqldump_path();
	}


	/**
	 * Gets the path to the 'mysql' binary.
	 *
	 * * Returns false if the path could not be found and if 'mysql_path' in config is not set.
	 *
	 * @return string
	 */
	public function getMysqlBinaryPath()
	{
		return dp_get_mysql_path();
	}


	/**
	 * @return string
	 */
	public function getLogDir()
	{
		return $this->kernel->getLogDir();
	}


	/**
	 * @return \Application\DeskPRO\Log\LoggerManager
	 */
	public function getLoggerManager()
	{
		return $this->getSystemService('LoggerManager');
	}


	/**
	 * @param string $id
	 * @return \Orb\Log\Logger
	 */
	public function getLogger($id)
	{
		return $this->getLoggerManager()->get($id);
	}


	/**
	 * @return string
	 */
	public function getBlobDir()
	{
		return $this->kernel->getBlobDir();
	}


	/**
	 * @return string
	 */
	public function getBackupDir()
	{
		return $this->kernel->getBackupDir();
	}
}
