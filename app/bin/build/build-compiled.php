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

require(DP_ROOT . '/bin/build/inc.php');

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Finder\Finder;

$cachefile = DP_ROOT.'/sys/compiled.php';
if (file_exists($cachefile)) {
    unlink($cachefile);
}

######################################################################
# Files that symfony thinks we should preload
######################################################################

$files = require DP_ROOT.'/sys/cache/prod/classes.map';

######################################################################
# Our files
######################################################################

$files = array_merge($files, array(
	'Orb\\Helper\\HelperManager',
	'Orb\\Helper\\ShortCallableInterface',

	'Orb\\Input\\Cleaner\\Cleaner',
	'Orb\\Input\\Reader\\Reader',
	'Orb\\Input\\Reader\\Source\\ArrayVal',
	'Orb\\Input\\Reader\\Source\\SourceInterface',
	'Orb\\Input\\Reader\\Source\\Superglobal',

	'Orb\\Templating\\Engine\\PhpVarEngine',
	'Orb\\Templating\\Engine\\PhpVarJsonEngine',

    'Orb\\Util\\Arrays',
    'Orb\\Util\\CapabilityInformerInterface',
    'Orb\\Util\\ChainCaller',
    'Orb\\Util\\Dates',
    'Orb\\Util\\Numbers',
    'Orb\\Util\\Strings',
    'Orb\\Util\\Util',
    'Orb\\Util\\Web',

	'Application\\DeskPRO\\App',
	'Application\\DeskPRO\\DBAL\\Connection',
	'Application\\DeskPRO\\DBAL\\ConnectionFactory',
	'Application\\DeskPRO\\DBAL\\DoctrineEvent',
	'Application\\DeskPRO\\DBAL\\SymfonyEventConnector',
	'Application\\DeskPRO\\Domain\\BasicDomainObject',
	'Application\\DeskPRO\\Domain\\ChangeTracker',
	'Application\\DeskPRO\\Domain\\DomainObject',
	'Application\\DeskPRO\\HttpFoundation\\Cookie',
	'Application\\DeskPRO\\HttpFoundation\\Request',

	'Application\\DeskPRO\\ORM\\Util\\Util',
	'Application\\DeskPRO\\ORM\\CollectionHelper',
	'Application\\DeskPRO\\ORM\\QueryPartial',

	'Application\\DeskPRO\\Settings\\Settings',
	'Application\\DeskPRO\\Settings\\SettingsLocator',

	'Application\\DeskPRO\\Templating\\Asset\\UrlPackage',
	'Application\\DeskPRO\\Templating\\GlobalVariables',

	'Application\\DeskPRO\\Translate\\Loader\\BundleLoader',
	'Application\\DeskPRO\\Translate\\Loader\\CombinationLoader',
	'Application\\DeskPRO\\Translate\\Loader\\DbLoader',
	'Application\\DeskPRO\\Translate\\Loader\\LoaderInterface',
	'Application\\DeskPRO\\Translate\\Loader\\PluginLoader',
	'Application\\DeskPRO\\Translate\\Loader\\SystemLoader',
	'Application\\DeskPRO\\Translate\\DelegatePhrase',
	'Application\\DeskPRO\\Translate\\DelegatePhraseInterface',
	'Application\\DeskPRO\\Translate\\DephrasifyTemplate',
	'Application\\DeskPRO\\Translate\\HasPhraseName',
	'Application\\DeskPRO\\Translate\\ObjectPhraseNamer',
	'Application\\DeskPRO\\Translate\\SystemLanguage',
	'Application\\DeskPRO\\Translate\\Translate',

	'Application\\DeskPRO\\Twig\\Extension\\TemplatingExtension',
	'Application\\DeskPRO\\Twig\\Loader\\HybridLoader',
));

ClassCollectionLoader::load($files, dirname($cachefile), basename($cachefile, '.php'), false, false, '.php');

$file = "<?php\n".substr(file_get_contents($cachefile), 5);
$file = str_replace('htmlspecialchars(', '@htmlspecialchars(', $file);
file_put_contents($cachefile, $file);
