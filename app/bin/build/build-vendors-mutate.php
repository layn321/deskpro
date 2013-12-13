#!/usr/bin/env php
<?php
if (php_sapi_name() != 'cli') {
	echo "This script must only be run from the CLI.\n";
	echo "Contact support@deskpro.com if you require assistance.\n";
	exit(1);
}

define('DP_BUILDING', true);
define('DP_ROOT', realpath(__DIR__ . '/../../'));
define('DP_WEB_ROOT', realpath(__DIR__ . '/../../../'));
define('DP_CONFIG_FILE', DP_WEB_ROOT . '/config.php');

require DP_ROOT . '/bin/build/inc.php';
require DP_ROOT.'/sys/system.php';

/**
 * This script makes various modifications to vendor files as required.
 */
class VendorMutate
{
	/**
	 * This is the only way we can use our custom ProxyFactory. The developers of Doctrine will not open it up for
	 * modification because they want strict control over it.
	 *
	 * In DeskPRO we need to inject some custom code into the proxies to support our static reflection methods,
	 * so we need to do this workaround.
	 */
	public function mutateDoctrine()
	{
		$do_unprivate_classes = array(
			array(
				'class_file' => DP_ROOT.'/vendor/doctrine/lib/Doctrine/ORM/Proxy/ProxyFactory.php',
				'target_file' => DP_ROOT.'/src/Application/DeskPRO/ORM/Unprivate/UnprivateProxyFactory.php',
				'target_namespace' => 'Application\\DeskPRO\\ORM\\Unprivate',
				'target_classname' => 'UnprivateProxyFactory',
				'custom_pre' => array(
					'use Doctrine\ORM\Proxy\ProxyException;'
				)
			),
			array(
				'class_file' => DP_ROOT.'/vendor/doctrine/lib/Doctrine/ORM/EntityManager.php',
				'target_file' => DP_ROOT.'/src/Application/DeskPRO/ORM/Unprivate/UnprivateEntityManager.php',
				'target_namespace' => 'Application\\DeskPRO\\ORM\\Unprivate',
				'target_classname' => 'UnprivateEntityManager',
				'custom_pre' => array(
					'use Doctrine\ORM\Configuration, Doctrine\ORM\ORMException, Doctrine\ORM\UnitOfWork, Doctrine\ORM\Query, Doctrine\ORM\Internal, Doctrine\ORM\NativeQuery, Doctrine\ORM\QueryBuilder;'
				),
				'callback' => array($this, '_doctrineEmFixCreate'),
			),
			array(
				'class_file' => DP_ROOT.'/vendor/doctrine/lib/Doctrine/ORM/UnitOfWork.php',
				'target_file' => DP_ROOT.'/src/Application/DeskPRO/ORM/Unprivate/UnprivateUnitOfWork.php',
				'target_namespace' => 'Application\\DeskPRO\\ORM\\Unprivate',
				'target_classname' => 'UnprivateUnitOfWork',
				'custom_pre' => array(
					'use Doctrine\ORM\Configuration, Doctrine\ORM\Persisters, Doctrine\ORM\EntityManager, Doctrine\ORM\Events, Doctrine\ORM\Event, Doctrine\ORM\Query, Doctrine\ORM\Internal, Doctrine\ORM\NativeQuery, Doctrine\ORM\QueryBuilder, Doctrine\ORM\PersistentCollection, Doctrine\ORM\ORMInvalidArgumentException, Doctrine\ORM\ORMException, Doctrine\ORM\OptimisticLockException, Doctrine\ORM\TransactionRequiredException, Doctrine\ORM\EntityNotFoundException;'
				),
				'callback' => array($this, '_doctrineEmFixCreate'),
			),
		);

		foreach ($do_unprivate_classes as $unprivate_class) {
			if (!file_exists($unprivate_class['class_file'])) {
				throw new \InvalidArgumentException("Class file does not exist: " . $unprivate_class['class_file']);
			}

			$source = file_get_contents($unprivate_class['class_file']);

			$unp = new \Application\DeskPRO\Php\UnprivateClass($source);
			$unp->enableStripComments();

			$source = $unp->getCode();
			$source = preg_replace('#<\?php#', "$0\n\n/* This file has been auto-generated. See build-vendors-mutate.php */\n\n", $source, 1);

			if ($unprivate_class['custom_pre']) {
				$unprivate_class['custom_pre'] = "\n" . implode("\n", $unprivate_class['custom_pre']) . "\n";
			} else {
				$unprivate_class['custom_pre'] = '';
			}

			preg_match('#namespace(.*?);#', $source, $m);
			$orig_ns = trim($m[1]);

			$source = preg_replace('#class ([a-zA-Z0-9_])#', 'class ' . $unprivate_class['target_classname'] . ' extends \\\\' . $orig_ns . '\\\\$1', $source, 1);
			if ($unprivate_class['target_classname'] != 'UnprivateProxyFactory') {
				// proxy factory needs to create proxies that implement the doctrine Proxy class
				$source = preg_replace('#\s+implements.*#', '', $source, 1);
			}
			$source = preg_replace('#namespace(.*?);#', "namespace {$unprivate_class['target_namespace']};{$unprivate_class['custom_pre']}", $source, 1);

			if (isset($unprivate_class['callback'])) {
				$source = call_user_func($unprivate_class['callback'], $source);
			}

			$source = explode("\n", $source);
			foreach ($source as &$_line) {
				$_line = rtrim($_line);
				if (trim($_line) == '') {
					$_line = trim($_line);
				}
			}

			$source = implode("\n", $source);
			$source = preg_replace("#\n{2,}#", "\n", $source);

			file_put_contents($unprivate_class['target_file'], $source);
		}
	}

	public function _doctrineEmFixCreate($source)
	{
		$source = str_replace('return new EntityManager(', 'return new static(', $source);
		return $source;
	}

	public function mutateGeoipApi()
	{
		$path = DP_ROOT.'/vendor/geoip-api/geoipcity.inc';
		$file = file_get_contents($path);

		$file = str_replace("require_once 'geoip.inc';", "require_once DP_ROOT.'/vendor/geoip-api/geoip.inc';", $file);
		$file = str_replace("require_once 'geoipregionvars.php';", "require_once DP_ROOT.'/vendor/geoip-api/geoipregionvars.php';", $file);

		file_put_contents($path, $file);
	}

	public function mutateSymfony()
	{
		$path = DP_ROOT.'/vendor/symfony/src/Symfony/Component/HttpFoundation/File/MimeType/FileBinaryMimeTypeGuesser.php';
		$file = file_get_contents($path);
		$file = str_replace('passthru(', '@passthru(', $file);
		file_put_contents($path, $file);
	}

	public function mutateDoctrineDrivers()
	{
		$data_file = file_get_contents(DP_ROOT.'/sys/Resources/DoctrineData/PDODblib.dat');

		$all_m = null;
		if (preg_match_all('#\^\^\^DP_BEGIN:(.*?)\^\^\^(.*?)\^\^\^DP_END:\\1\^\^\^#s', $data_file, $all_m, \PREG_SET_ORDER)) {
			foreach ($all_m as $m) {
				$filename    = $m[1];
				$filecontent = trim($m[2]);
				$target_file = DP_ROOT . DIRECTORY_SEPARATOR . $filename;
				$target_dir  = dirname($target_file);

				if (!is_dir($target_dir)) {
					mkdir($target_dir, 0644, true);
				}

				file_put_contents($target_file, $filecontent);
			}
		}

		// Add the driver to the driver map
		$driver_map_file = DP_ROOT . '/vendor/doctrine-dbal/lib/Doctrine/DBAL/DriverManager.php';
		$file_contents = file_get_contents($driver_map_file);
		$file_contents = str_replace('$_driverMap = array(', '$_driverMap = array(' . "\n            'pdo_dblib' => 'Doctrine\\DBAL\\Driver\\PDODblib\\Driver',", $file_contents);
		file_put_contents($driver_map_file, $file_contents);
	}
}

$mutate = new VendorMutate();
$mutate->mutateDoctrine();
$mutate->mutateGeoipApi();
$mutate->mutateSymfony();
$mutate->mutateDoctrineDrivers();