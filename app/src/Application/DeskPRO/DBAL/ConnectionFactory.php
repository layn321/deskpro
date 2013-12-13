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

namespace Application\DeskPRO\DBAL;

use Application\DeskPRO\App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;

/**
 * Custom loading database creds from config.php
 */
class ConnectionFactory extends \Symfony\Bundle\DoctrineBundle\ConnectionFactory implements ContainerAwareInterface
{
	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
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
				$conf = array('bogus'); // Dont need dbinfo
			}

			if (!$conf) {
				throw new \Exception("Invalid database key $key");
			}
			$params = array_merge($params, $conf);
			if (empty($params['driver'])) {
				$params['driver'] = 'pdo_mysql';
			}
		}

		// Sometimes in a pre-boot handler like serve_file.php we might
		// already have a connection, so use that PDO object
		if (isset($GLOBALS['DP_DEFAULT_CONNECTION_PDO']) && $host === 'from_user_config.db') {
			$params['pdo'] = $GLOBALS['DP_DEFAULT_CONNECTION_PDO'];
		}

		/** @var $conn \Doctrine\DBAL\Connection */
		$conn = parent::createConnection($params, $config, $eventManager, $mappingTypes);

		$evm = $conn->getEventManager();

		if ($this->container && $this->container->has('event_dispatcher')) {
			$evm->addEventSubscriber(new SymfonyEventConnector($this->container->get('event_dispatcher')));
		}

		$conn->getDatabasePlatform()->registerDoctrineTypeMapping('BLOB', 'dpblob');

		return $conn;
	}
}
