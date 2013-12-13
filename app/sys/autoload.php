<?php if (!defined('DP_ROOT')) exit('No access');
set_include_path(
	DP_ROOT.'/vendor/zend/library'
	.PATH_SEPARATOR.
	DP_ROOT.'/vendor/ezcomponents'
	.PATH_SEPARATOR.
    DP_ROOT.'/vendor/pear/lib'
    .PATH_SEPARATOR.
	get_include_path()
);

$loader = new \Orb\Util\ClassLoader();

$loader->registerNamespaces(array(
	'Application'                  => DP_ROOT.'/src',
	'Cloud'                        => DP_ROOT.'/src',
    'Bundle'                       => DP_ROOT.'/src',
	'Orb'                          => DP_ROOT.'/src',

	'Assetic'                        => DP_ROOT.'/vendor/assetic/src',
	'Symfony'                        => DP_ROOT.'/vendor/symfony/src',
    'Doctrine\\Common'               => DP_ROOT.'/vendor/doctrine-common/lib',
	'Doctrine\\DBAL\\Migrations'     => DP_ROOT.'/vendor/doctrine-migrations/lib',
	'Doctrine\\DBAL'                 => DP_ROOT.'/vendor/doctrine-dbal/lib',
	'Doctrine'                       => DP_ROOT.'/vendor/doctrine/lib',
    'Monolog'                        => DP_ROOT.'/vendor/monolog/src',
	'Zend'                           => DP_ROOT.'/vendor/zend/library',
	'Elao'                           => DP_ROOT.'/vendor/profiler',
	'Profiler'                       => DP_ROOT.'/vendor/profiler',
	'Imagine'                        => DP_ROOT.'/vendor/imagine/lib',
	'Aws'                            => DP_ROOT.'/vendor/aws-sdk-php/src',
	'Guzzle'                         => DP_ROOT.'/vendor/guzzle/src',
	'Metadata'                       => DP_ROOT.'/vendor/metadata/src',
	'Spork'                          => DP_ROOT.'/vendor/spork/src',
	'Leth\\IPAddress'                => DP_ROOT.'/vendor/php_ipaddress/classes'
));
$loader->registerNamespaceFallbacks(array(DP_WEB_ROOT . '/plugins'));

$loader->registerPrefixes(array(
    'Twig_'       => DP_ROOT.'/vendor/twig/lib',
	'Pheanstalk'  => DP_ROOT.'/vendor/pheanstalk/classes',
	'Elastica_'   => DP_ROOT.'/vendor/Elastica/lib',
    'mPDF_'       => DP_ROOT.'/vendor/mpdf/lib',
    'File_'       => DP_ROOT.'/vendor/pear/lib',
    'PEAR_'       => DP_ROOT.'/vendor/pear/lib',
));

$loader->registerClassNames(array(
	'Akismet'                         => DP_ROOT.'/vendor/php5-akismet/src/main/php/net/achingbrain/Akismet.class.php',
	'Browser'                         => DP_ROOT.'/vendor/Browser/Browser.php',
	'CssMin'                          => DP_ROOT.'/vendor/cssmin/cssmin.php',
	'LightOpenID'                     => DP_ROOT.'/vendor/lightopenid/openid.php',
	'Facebook'                        => DP_ROOT.'/vendor/facebook/src/facebook.php',
	'FacebookApiException'            => DP_ROOT.'/vendor/facebook/src/facebook.php',
	'MimeMailParser'                  => DP_ROOT.'/vendor/php-mime-mail-parser/MimeMailParser.php',
	'MimeMailParser_attachment'       => DP_ROOT.'/vendor/php-mime-mail-parser/attachment.class.php',
	'Phirehose'                       => DP_ROOT.'/vendor/phirehose/Phirehose.php',
	'UserstreamPhirehose'             => DP_ROOT.'/vendor/phirehose/UserstreamPhirehose.php',
	'HipChatApi'                      => DP_ROOT.'/vendor/hipchat/HipChatApi.php',
	'Markdown_Parser'                 => DP_ROOT.'/vendor/php-markdown/markdown.php',
	'FineDiff'                        => DP_ROOT.'/vendor/PHP-FineDiff/finediff.php',
	'GoogleOpenID'                    => DP_ROOT.'/vendor/googleopenid/GoogleOpenID.php',
	'POParser'                        => DP_ROOT.'/vendor/simplepo/POParser.php',
	'TempPoMsgStore'                  => DP_ROOT.'/vendor/simplepo/POParser.php',
	'Emogrifier'                      => DP_ROOT.'/vendor/emogrifier/emogrifier.php',

	'Text_LanguageDetect'             => DP_ROOT.'/vendor/Text_LanguageDetect/lib/Text/LanguageDetect.php',
	'Text_LanguageDetect_Exception'   => DP_ROOT.'/vendor/Text_LanguageDetect/lib/Text/LanguageDetect/Exception.php',
	'Text_LanguageDetect_ISO639'      => DP_ROOT.'/vendor/Text_LanguageDetect/lib/Text/LanguageDetect/ISO639.php',
	'Text_LanguageDetect_Parser'      => DP_ROOT.'/vendor/Text_LanguageDetect/lib/Text/LanguageDetect/Parser.php',

	'EpiCurl'                         => DP_ROOT.'/vendor/twitter-async/EpiCurl.php',
	'EpiOAuth'                        => DP_ROOT.'/vendor/twitter-async/EpiOAuth.php',
	'EpiOSequence'                    => DP_ROOT.'/vendor/twitter-async/EpiOSequence.php',
	'EpiTwitter'                      => DP_ROOT.'/vendor/twitter-async/EpiTwitter.php',

	'phpthumb_ico'                    => DP_ROOT.'/vendor/phpthumb/phpthumb.ico.php',
	'PasswordHash'                    => DP_ROOT.'/vendor/phpass/PasswordHash.php',
));

spl_autoload_register(function($classname) {
	if (strpos($classname, 'DeskproLanguages') !== 0) return false;

	$classpath = str_replace('DeskproLanguages\\', '', $classname);
	$classpath = str_replace('\\', DIRECTORY_SEPARATOR, $classpath);
	$path = DP_ROOT . '/languages/' . $classpath . '.php';

	require($path);
	return true;
});

$loader->register();

define('QP_NO_AUTOLOADER', true);

$GLOBALS['DP_AUTOLOADER'] = $loader;

// ezC autoloading
require DP_ROOT.'/vendor/ezcomponents/Base/src/ezc_bootstrap.php';
spl_autoload_register(function($classname) {
	if (strpos($classname, 'ezc') !== 0) return false;
	return ezcBase::autoload($classname);
});

if (!defined('GEOIP_API_INC_PATH')) {
	define('GEOIP_API_INC_PATH', DP_ROOT.'/vendor/geoip-api');
}

// Needed for assetic build to work
class_exists('CssMin');

use Doctrine\Common\Annotations\AnnotationRegistry;
AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});
AnnotationRegistry::registerFile(DP_ROOT.'/vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

require DP_ROOT.'/vendor/swiftmailer/lib/swift_required.php';
\Swift_DependencyContainer::getInstance()->register('cache.disk')-> asSharedInstanceOf('Orb\\Mail\\KeyCache\\DiskKeyCache')->withDependencies(array('cache.inputstream', 'tempdir'));

require DP_ROOT.'/vendor/querypath/src/qp.php';