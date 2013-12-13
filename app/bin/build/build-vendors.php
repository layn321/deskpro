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

$only_vendor_id = false;
if (($k = array_search('--vendor-id', $_SERVER['argv'])) !== false) {
	$only_vendor_id = $_SERVER['argv'][$k+1];
}

###############################################################################
# Helpers
###############################################################################

function deskpro_build_exec_exit_error($cmd, $dir = null)
{
	global $output;
	$output->writeln("> $cmd");

	$proc = new \Process($cmd, $dir);
	$proc->setTimeout(600);
	$proc->run(function($type, $out) {
		if ($type == 'out') {
			echo $out;
		}
	});

	if (!$proc->isSuccessful()) {
		echo $proc->getErrorOutput();
		$output->writeln("<error>Error with command</error>");
		exit($proc->getExitCode());
	}

	return true;
}

class Output { function writeln($line) { echo "$line\n"; } }

class Process
{
    private $commandline;
    private $cwd;
    private $env;
    private $stdin;
    private $timeout;
    private $options;
    private $exitcode;
    private $status;
    private $stdout;
    private $stderr;

    public function __construct($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 60, array $options = array())
    {
        if (!function_exists('proc_open')) {
            throw new \RuntimeException('The Process class relies on proc_open, which is not available on your PHP installation.');
        }

        $this->commandline = $commandline;
        $this->cwd = null === $cwd ? getcwd() : $cwd;
        if (null !== $env) {
            $this->env = array();
            foreach ($env as $key => $value) {
                $this->env[(binary) $key] = (binary) $value;
            }
        } else {
            $this->env = null;
        }
        $this->stdin = $stdin;
        $this->timeout = $timeout;
        $this->options = array_merge(array('suppress_errors' => true, 'binary_pipes' => true, 'bypass_shell' => false), $options);
    }

    public function run($callback = null)
    {
        $this->stdout = '';
        $this->stderr = '';
        $that = $this;
        $callback = function ($type, $data) use ($that, $callback)
        {
            if ('out' == $type) {
                $that->addOutput($data);
            } else {
                $that->addErrorOutput($data);
            }

            if (null !== $callback) {
                call_user_func($callback, $type, $data);
            }
        };

        $descriptors = array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w'));

        $process = proc_open($this->commandline, $descriptors, $pipes, $this->cwd, $this->env, $this->options);

        if (!is_resource($process)) {
            throw new \RuntimeException('Unable to launch a new process.');
        }

        foreach ($pipes as $pipe) {
            stream_set_blocking($pipe, false);
        }

        if (null === $this->stdin) {
            fclose($pipes[0]);
            $writePipes = null;
        } else {
            $writePipes = array($pipes[0]);
            $stdinLen = strlen($this->stdin);
            $stdinOffset = 0;
        }
        unset($pipes[0]);

        while ($pipes || $writePipes) {
            $r = $pipes;
            $w = $writePipes;
            $e = null;

            $n = @stream_select($r, $w, $e, $this->timeout);

            if (false === $n) {
                break;
            } elseif ($n === 0) {
                proc_terminate($process);

                throw new \RuntimeException('The process timed out.');
            }

            if ($w) {
                $written = fwrite($writePipes[0], (binary) substr($this->stdin, $stdinOffset), 8192);
                if (false !== $written) {
                    $stdinOffset += $written;
                }
                if ($stdinOffset >= $stdinLen) {
                    fclose($writePipes[0]);
                    $writePipes = null;
                }
            }

            foreach ($r as $pipe) {
                $type = array_search($pipe, $pipes);
                $data = fread($pipe, 8192);
                if (strlen($data) > 0) {
                    call_user_func($callback, $type == 1 ? 'out' : 'err', $data);
                }
                if (false === $data || feof($pipe)) {
                    fclose($pipe);
                    unset($pipes[$type]);
                }
            }
        }

        $this->status = proc_get_status($process);

        $time = 0;
        while (1 == $this->status['running'] && $time < 1000000) {
            $time += 1000;
            usleep(1000);
            $this->status = proc_get_status($process);
        }

        $exitcode = proc_close($process);

        if ($this->status['signaled']) {
            throw new \RuntimeException(sprintf('The process stopped because of a "%s" signal.', $this->status['stopsig']));
        }

        return $this->exitcode = $this->status['running'] ? $exitcode : $this->status['exitcode'];
    }

    public function getOutput()
    {
        return $this->stdout;
    }

    public function getErrorOutput()
    {
        return $this->stderr;
    }

    public function getExitCode()
    {
        return $this->exitcode;
    }

    public function isSuccessful()
    {
        return 0 == $this->exitcode;
    }

    public function hasBeenSignaled()
    {
        return $this->status['signaled'];
    }

    public function getTermSignal()
    {
        return $this->status['termsig'];
    }

    public function hasBeenStopped()
    {
        return $this->status['stopped'];
    }

    public function getStopSignal()
    {
        return $this->status['stopsig'];
    }

    public function addOutput($line)
    {
        $this->stdout .= $line;
    }

    public function addErrorOutput($line)
    {
        $this->stderr .= $line;
    }

    public function getCommandLine()
    {
        return $this->commandline;
    }

    public function setCommandLine($commandline)
    {
        $this->commandline = $commandline;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function getWorkingDirectory()
    {
        return $this->cwd;
    }

    public function setWorkingDirectory($cwd)
    {
        $this->cwd = $cwd;
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function setEnv(array $env)
    {
        $this->env = $env;
    }

    public function getStdin()
    {
        return $this->stdin;
    }

    public function setStdin($stdin)
    {
        $this->stdin = $stdin;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}

###############################################################################
# Cleanup functions
###############################################################################

function deskpro_build_cleanvendors_assetic($dir)
{
	deskpro_build_exec_exit_error("rm -rf docs tests .gitignore CHANGELOG phpunit.xml.dist README.md", $dir);
}

function deskpro_build_cleanvendors_aws_sdk_php($dir)
{
	deskpro_build_exec_exit_error("rm -rf build/ docs/ tests/ .gitignore CHANGELOG.md CONTRIBUTING.md NOTICE.md README.md UPGRADING.md build.xml composer.json phpunit.functional.xml.dist phpunit.xml.dist test_services.json.dist", $dir);
	deskpro_build_exec_exit_error("rm -rf AutoScaling/ CloudFormation/ CloudSearch/ CloudWatch/ DataPipeline/ DirectConnect/ DynamoDb/ Ec2/ ElastiCache/ ElasticBeanstalk/ ElasticLoadBalancing/ ElasticTranscoder/ Emr/ Glacier/ Iam/ ImportExport/ OpsWorks/ Rds/ Redshift/ Route53/ Ses/ SimpleDb/ Sns/ Sqs/ StorageGateway/ Sts/ Swf/", $dir.'/src/Aws');
}

function deskpro_build_cleanvendors_elastica($dir)
{
	deskpro_build_exec_exit_error("rm -rf test .gitignore build.xml changes.txt README.markdown", $dir);
}

function deskpro_build_cleanvendors_doctrine($dir)
{
	deskpro_build_exec_exit_error("rm -rf bin tests tools .travis.yml .gitignore .gitmodules composer.json build.properties build.properties.dev build.xml doctrine-mapping.xsd phpunit.xml.dist README.markdown run-all.sh UPGRADE_TO_2_0 UPGRADE_TO_2_1 UPGRADE_TO_2_2 UPGRADE_TO_ALPHA3 UPGRADE_TO_ALPHA4", $dir);
}

function deskpro_build_cleanvendors_doctrine_common($dir)
{
	deskpro_build_exec_exit_error("rm -rf tests .travis.yml .gitignore .gitmodules build.properties.dev build.xml phpunit.xml.dist UPGRADE_TO_2_1 UPGRADE_TO_2_2 README.md composer.json build.properties", $dir);
}

function deskpro_build_cleanvendors_doctrine_dbal($dir)
{
	deskpro_build_exec_exit_error("rm -rf bin tests .travis.yml .gitignore .gitmodules composer.json build.properties build.properties.dev build.xml phpunit.xml.dist README.md run-all.sh UPGRADE", $dir);
}

function deskpro_build_cleanvendors_doctrine_migrations($dir)
{
	deskpro_build_exec_exit_error("rm -rf tests .travis.yml .gitignore .gitmodules build.properties.dev build.xml phpunit.xml.dist README.markdown composer.json package.php phpunit.xml.dist phar-cli-stub.php lib/vendor", $dir);
}

function deskpro_build_cleanvendors_facebook($dir)
{
	deskpro_build_exec_exit_error("rm -rf examples tests readme.md", $dir);
}

function deskpro_build_cleanvendors_geoip_api($dir)
{
	deskpro_build_exec_exit_error("rm -rf admin timezone/ ChangeLog benchmark.php sample-v6.php sample.php sample_asn-v6.php sample_city-v6.php sample_city.php sample_distributed.php sample_domain.php sample_netspeed.php sample_netspeedcell.php sample_org.php sample_region.php", $dir);
}

function deskpro_build_cleanvendors_guzzle($dir)
{
	deskpro_build_exec_exit_error("rm -rf phing/ tests/ .gitignore .travis.yml CHANGELOG.md README.md UPGRADING.md build.xml composer.json phar-stub.php phpunit.xml.dist", $dir);
}

function deskpro_build_cleanvendors_idbstore($dir)
{
	deskpro_build_exec_exit_error("rm -rf example/ lib/ .gitignore CHANGELOG LICENSE Makefile README.md component.json idbstore.js package.json", $dir);
}

function deskpro_build_cleanvendors_imagine($dir)
{
	deskpro_build_exec_exit_error("rm -rf docs tests .gitignore .travis.yml composer.json composer.lock Gemfile imagine.phar phpunit.xml.dist Rakefile README.md", $dir);
}

function deskpro_build_cleanvendors_metadata($dir)
{
	deskpro_build_exec_exit_error("rm -rf tests .gitignore phpunit.xml.dist README.rst CHANGELOG.md", $dir);
}

function deskpro_build_cleanvendors_monolog($dir)
{
	deskpro_build_exec_exit_error("rm -rf doc tests CHANGELOG.mdown .gitignore composer.json phpunit.xml.dist README.mdown", $dir);
}

function deskpro_build_cleanvendors_php5_akismet($dir)
{
	deskpro_build_exec_exit_error("rm -rf .gitignore README.markdown pom.xml src/test src/site", $dir);
}

function deskpro_build_cleanvendors_pheanstalk($dir)
{
	deskpro_build_exec_exit_error("rm -rf doc tests .gitmodules .gitignore .gitattributes .travis.yml composer.json pheanstalk_init.php phpunit.xml.dist README.md scripts/", $dir);
}

function deskpro_build_cleanvendors_php_ipaddress($dir)
{
	deskpro_build_exec_exit_error("rm -rf tests/ .travis.yml README.md composer.json lgpl-3.0.txt phpunit.xml.dist", $dir);
}

function deskpro_build_cleanvendors_webprofilerextra($dir)
{
	deskpro_build_exec_exit_error("rm -rf README.md screen.png", $dir);
}

function deskpro_build_cleanvendors_profilerlive($dir)
{
	deskpro_build_exec_exit_error("rm -rf .gitignore README.rd", $dir);
}

function deskpro_build_cleanvendors_swiftmailer($dir)
{
	deskpro_build_exec_exit_error("rm -rf doc notes test-suite tests .gitignore build.xml CHANGES composer.json create_pear_package.php package.xml.tpl README README.git VERSION", $dir);
}

function deskpro_build_cleanvendors_symfony($dir)
{
	deskpro_build_exec_exit_error("rm -rf tests .gitignore .travis.yml autoload.php.dist CHANGELOG-2.0.md check_cs composer.json CONTRIBUTORS.md phpunit.xml.dist README.md UPDATE.ja.md UPDATE.md vendors.php", $dir);

	// Need to add a newline to this file to fix Windows/PHP < 5.3.10/Apache bug where a crash happens on files of exactly 4096 size
	$fp = fopen($dir . '/src/Symfony/Bundle/TwigBundle/TwigEngine.php', 'a');
	fwrite($fp, "\n// --\n");
	fclose($fp);
}

function deskpro_build_cleanvendors_symfony_doctrine_migrations($dir)
{
	deskpro_build_exec_exit_error("rm -rf README.markdown", $dir);
}

function deskpro_build_cleanvendors_spork($dir)
{
	deskpro_build_exec_exit_error("rm -rf .gitignore .travis.yml README.md composer.json phpunit.xml.dist tests/", $dir);
}

function deskpro_build_cleanvendors_twig($dir)
{
	deskpro_build_exec_exit_error("rm -rf bin doc ext test AUTHORS CHANGELOG composer.json package.xml.tpl phpunit.xml.dist README.markdown .travis.yml .editorconfig .gitignore", $dir);
}

function deskpro_build_cleanvendors_twig_js($dir)
{
	deskpro_build_exec_exit_error("rm -rf twig.min.js package.json README.md Makefile .travis.yml .npmignore .gitmodules .gitignore test/ test-ext/ src/ lib/ docs/ demos/ bin/", $dir);
}

function deskpro_build_cleanvendors_zend($dir)
{
	deskpro_build_exec_exit_error("rm -rf bin demos documentation resources tests tools working .gitignore .gitmodules INSTALL.txt README-DEV.txt README-GIT.txt README.txt ext/twig/.gitignore modules", $dir);

	$remove_modules = array(
		'Acl',
		'Amf',
		'Application',
		'Authentication/Adapter/Http',
		'Authentication/Adapter/DbTable.php',
		'Authentication/Adapter/Digest.php',
		'Authentication/Adapter/Http.php',
		'Authentication/AuthenticationService.php',
		'Barcode',
		'Captcha',
		'Cloud',
		'CodeGenerator',
		'Code',
		'Console',
		'Controller',
		'Currency',
		'Db',
		'Di',
		'Docbook',
		'Dom',
		'Dojo',
		'Form',
		'GData',
		'InfoCard',
		'Layout',
		'Markup',
		'Measure',
		'Memory',
		'Mvc',
		'Navigation',
		'Pagination',
		'Paginator',
		'Pdf',
		'ProgressBar',
		'Search',
		'Serializer',
		'Service/Akismet',
		'Service/Amazon/Sqs',
		'Service/Amazon/SimpleDb',
		'Service/Amazon/Ec2',
		'Service/Amazon/SimilarProduct.php',
		'Service/Amazon/ListmaniaList.php',
		'Service/Amazon/EditorialReview.php',
		'Service/Amazon/CustomerReview.php',
		'Service/AgileZen',
		'Service/Audioscrobbler',
		'Service/Delicious',
		'Service/DeveloperGarden',
		'Service/Flickr',
		'Service/GoGrid',
		'Service/LiveDocx',
		'Service/Nirvanix',
		'Service/Rackspace',
		'Service/ReCaptcha',
		'Service/Simpy',
		'Service/SlideShare',
		'Service/StrikeIron',
		'Service/Technorati',
		'Service/Twitter',
		'Service/WindowsAzure',
		'Service/Yahoo',
		'Service/Flickr.php',
		'Service/Simpy.php',
		'Service/StrikeIron.php',
		'Service/Technorati.php',
		'Service/Yahoo.php',
		'Server',
		'Soap',
		'Tag',
		'Test',
		'Text',
		'TimeSync',
		'Tool',
		'Translator',
		'View',
		'Wildfire',
		'XmlRpc',
	);

	foreach ($remove_modules as $d) {
		deskpro_build_exec_exit_error('rm -rf library/Zend/' . $d, $dir);
	}
}

function deskpro_build_cleanvendors_querypath($dir)
{
	deskpro_build_exec_exit_error("rm -rf .gitignore API build.xml composer.json config.doxy INSTALL Makefile package.xml package_compatible.xml pear-summary.txt quickstart-guide.md README.md RELEASE bin/ examples/ patches/ phar/ test/ tutorials/ src/QueryPath/Extension/QPDB.php src/QueryPath/Extension/QPTPL.php src/QueryPath/Extension/QPList.php", $dir);
}

###############################################################################
# Run
###############################################################################

$output = new \Output();

$all_vendors_config = require DP_ROOT.'/sys/config/vendors.php';
foreach ($all_vendors_config as $vendor_id => $vendors_config) {

	if ($only_vendor_id && $vendor_id != $only_vendor_id) {
		continue;
	}

	$output->writeln("\n$vendor_id\nRepository: {$vendors_config['repos']}\nVersion: {$vendors_config['version']}\nInto: {$vendors_config['into']}");

	if (file_exists($vendors_config['into'])) {
		$output->writeln('<info>Removing existing directory</info>');
		deskpro_build_exec_exit_error("rm -rf {$vendors_config['into']}", null);
	}

	deskpro_build_exec_exit_error("mkdir -p {$vendors_config['into']}", null);

	deskpro_build_exec_exit_error("git clone {$vendors_config['repos']} .", $vendors_config['into']);
	deskpro_build_exec_exit_error("git checkout {$vendors_config['version']}", $vendors_config['into']);
	deskpro_build_exec_exit_error("rm -rf .git", $vendors_config['into']);

	$fn = "deskpro_build_cleanvendors_{$vendor_id}";
	if (function_exists($fn)) {
		$fn($vendors_config['into']);
	}
}
