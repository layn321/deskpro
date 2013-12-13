<?php

namespace { require DP_ROOT.'/sys/autoload.php'; }

  



namespace Symfony\Component\DependencyInjection
{


interface ContainerAwareInterface
{
    
    public function setContainer(ContainerInterface $container = null);
}
}
 



namespace Symfony\Component\DependencyInjection
{


interface ContainerInterface
{
    const EXCEPTION_ON_INVALID_REFERENCE = 1;
    const NULL_ON_INVALID_REFERENCE      = 2;
    const IGNORE_ON_INVALID_REFERENCE    = 3;
    const SCOPE_CONTAINER                = 'container';
    const SCOPE_PROTOTYPE                = 'prototype';

    
    public function set($id, $service, $scope = self::SCOPE_CONTAINER);

    
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE);

    
    public function has($id);

    
    public function getParameter($name);

    
    public function hasParameter($name);

    
    public function setParameter($name, $value);

    
    public function enterScope($name);

    
    public function leaveScope($name);

    
    public function addScope(ScopeInterface $scope);

    
    public function hasScope($name);

    
    public function isScopeActive($name);
}
}
 



namespace Symfony\Component\DependencyInjection
{

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;


class Container implements ContainerInterface
{
    protected $parameterBag;
    protected $services;
    protected $scopes;
    protected $scopeChildren;
    protected $scopedServices;
    protected $scopeStacks;
    protected $loading = array();

    
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        $this->parameterBag = null === $parameterBag ? new ParameterBag() : $parameterBag;

        $this->services       = array();
        $this->scopes         = array();
        $this->scopeChildren  = array();
        $this->scopedServices = array();
        $this->scopeStacks    = array();

        $this->set('service_container', $this);
    }

    
    public function compile()
    {
        $this->parameterBag->resolve();

        $this->parameterBag = new FrozenParameterBag($this->parameterBag->all());
    }

    
    public function isFrozen()
    {
        return $this->parameterBag instanceof FrozenParameterBag;
    }

    
    public function getParameterBag()
    {
        return $this->parameterBag;
    }

    
    public function getParameter($name)
    {
        return $this->parameterBag->get($name);
    }

    
    public function hasParameter($name)
    {
        return $this->parameterBag->has($name);
    }

    
    public function setParameter($name, $value)
    {
        $this->parameterBag->set($name, $value);
    }

    
    public function set($id, $service, $scope = self::SCOPE_CONTAINER)
    {
        if (self::SCOPE_PROTOTYPE === $scope) {
            throw new \InvalidArgumentException('You cannot set services of scope "prototype".');
        }

        $id = strtolower($id);

        if (self::SCOPE_CONTAINER !== $scope) {
            if (!isset($this->scopedServices[$scope])) {
                throw new \RuntimeException('You cannot set services of inactive scopes.');
            }

            $this->scopedServices[$scope][$id] = $service;
        }

        $this->services[$id] = $service;
    }

    
    public function has($id)
    {
        $id = strtolower($id);

        return isset($this->services[$id]) || method_exists($this, 'get'.strtr($id, array('_' => '', '.' => '_')).'Service');
    }

    
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        $id = strtolower($id);

        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        if (isset($this->loading[$id])) {
            throw new ServiceCircularReferenceException($id, array_keys($this->loading));
        }

        if (method_exists($this, $method = 'get'.strtr($id, array('_' => '', '.' => '_')).'Service')) {
            $this->loading[$id] = true;

            try {
                $service = $this->$method();
            } catch (\Exception $e) {
                unset($this->loading[$id]);
                throw $e;
            }

            unset($this->loading[$id]);

            return $service;
        }

        if (self::EXCEPTION_ON_INVALID_REFERENCE === $invalidBehavior) {
            throw new ServiceNotFoundException($id);
        }
    }

    
    public function getServiceIds()
    {
        $ids = array();
        $r = new \ReflectionClass($this);
        foreach ($r->getMethods() as $method) {
            if (preg_match('/^get(.+)Service$/', $method->name, $match)) {
                $ids[] = self::underscore($match[1]);
            }
        }

        return array_unique(array_merge($ids, array_keys($this->services)));
    }

    
    public function enterScope($name)
    {
        if (!isset($this->scopes[$name])) {
            throw new \InvalidArgumentException(sprintf('The scope "%s" does not exist.', $name));
        }

        if (self::SCOPE_CONTAINER !== $this->scopes[$name] && !isset($this->scopedServices[$this->scopes[$name]])) {
            throw new \RuntimeException(sprintf('The parent scope "%s" must be active when entering this scope.', $this->scopes[$name]));
        }

                                if (isset($this->scopedServices[$name])) {
            $services = array($this->services, $name => $this->scopedServices[$name]);
            unset($this->scopedServices[$name]);

            foreach ($this->scopeChildren[$name] as $child) {
                $services[$child] = $this->scopedServices[$child];
                unset($this->scopedServices[$child]);
            }

                        $this->services = call_user_func_array('array_diff_key', $services);
            array_shift($services);

                        if (!isset($this->scopeStacks[$name])) {
                $this->scopeStacks[$name] = new \SplStack();
            }
            $this->scopeStacks[$name]->push($services);
        }

        $this->scopedServices[$name] = array();
    }

    
    public function leaveScope($name)
    {
        if (!isset($this->scopedServices[$name])) {
            throw new \InvalidArgumentException(sprintf('The scope "%s" is not active.', $name));
        }

                        $services = array($this->services, $this->scopedServices[$name]);
        unset($this->scopedServices[$name]);
        foreach ($this->scopeChildren[$name] as $child) {
            if (!isset($this->scopedServices[$child])) {
                continue;
            }

            $services[] = $this->scopedServices[$child];
            unset($this->scopedServices[$child]);
        }
        $this->services = call_user_func_array('array_diff_key', $services);

                if (isset($this->scopeStacks[$name]) && count($this->scopeStacks[$name]) > 0) {
            $services = $this->scopeStacks[$name]->pop();
            $this->scopedServices += $services;

            array_unshift($services, $this->services);
            $this->services = call_user_func_array('array_merge', $services);
        }
    }

    
    public function addScope(ScopeInterface $scope)
    {
        $name = $scope->getName();
        $parentScope = $scope->getParentName();

        if (self::SCOPE_CONTAINER === $name || self::SCOPE_PROTOTYPE === $name) {
            throw new \InvalidArgumentException(sprintf('The scope "%s" is reserved.', $name));
        }
        if (isset($this->scopes[$name])) {
            throw new \InvalidArgumentException(sprintf('A scope with name "%s" already exists.', $name));
        }
        if (self::SCOPE_CONTAINER !== $parentScope && !isset($this->scopes[$parentScope])) {
            throw new \InvalidArgumentException(sprintf('The parent scope "%s" does not exist, or is invalid.', $parentScope));
        }

        $this->scopes[$name] = $parentScope;
        $this->scopeChildren[$name] = array();

                while ($parentScope !== self::SCOPE_CONTAINER) {
            $this->scopeChildren[$parentScope][] = $name;
            $parentScope = $this->scopes[$parentScope];
        }
    }

    
    public function hasScope($name)
    {
        return isset($this->scopes[$name]);
    }

    
    public function isScopeActive($name)
    {
        return isset($this->scopedServices[$name]);
    }

    
    public static function camelize($id)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $id);
    }

    
    public static function underscore($id)
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), strtr($id, '_', '.')));
    }
}
}
 



namespace Symfony\Component\HttpKernel
{

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


interface HttpKernelInterface
{
    const MASTER_REQUEST = 1;
    const SUB_REQUEST = 2;

    
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true);
}
}
 



namespace Symfony\Component\HttpKernel
{

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Config\Loader\LoaderInterface;


interface KernelInterface extends HttpKernelInterface, \Serializable
{
    
    public function registerBundles();

    
    public function registerContainerConfiguration(LoaderInterface $loader);

    
    public function boot();

    
    public function shutdown();

    
    public function getBundles();

    
    public function isClassInActiveBundle($class);

    
    public function getBundle($name, $first = true);

    
    public function locateResource($name, $dir = null, $first = true);

    
    public function getName();

    
    public function getEnvironment();

    
    public function isDebug();

    
    public function getRootDir();

    
    public function getContainer();

    
    public function getStartTime();

    
    public function getCacheDir();

    
    public function getLogDir();
}
}
 



namespace Symfony\Component\HttpKernel
{

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\DependencyInjection\AddClassesToCachePass;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\ClassLoader\DebugUniversalClassLoader;


abstract class Kernel implements KernelInterface
{
    protected $bundles;
    protected $bundleMap;
    protected $container;
    protected $rootDir;
    protected $environment;
    protected $debug;
    protected $booted;
    protected $name;
    protected $startTime;
    protected $classes;

    const VERSION         = '2.0.17';
    const VERSION_ID      = '20017';
    const MAJOR_VERSION   = '2';
    const MINOR_VERSION   = '0';
    const RELEASE_VERSION = '17';
    const EXTRA_VERSION   = '';

    
    public function __construct($environment, $debug)
    {
        $this->environment = $environment;
        $this->debug = (Boolean) $debug;
        $this->booted = false;
        $this->rootDir = $this->getRootDir();
        $this->name = preg_replace('/[^a-zA-Z0-9_]+/', '', basename($this->rootDir));
        $this->classes = array();

        if ($this->debug) {
            $this->startTime = microtime(true);
        }

        $this->init();
    }

    public function init()
    {
        if ($this->debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);

            DebugUniversalClassLoader::enable();
            ErrorHandler::register();
            if ('cli' !== php_sapi_name()) {
                ExceptionHandler::register();
            }
        } else {
            ini_set('display_errors', 0);
        }
    }

    public function __clone()
    {
        if ($this->debug) {
            $this->startTime = microtime(true);
        }

        $this->booted = false;
        $this->container = null;
    }

    
    public function boot()
    {
        if (true === $this->booted) {
            return;
        }

                $this->initializeBundles();

                $this->initializeContainer();

        foreach ($this->getBundles() as $bundle) {
            $bundle->setContainer($this->container);
            $bundle->boot();
        }

        $this->booted = true;
    }

    
    public function shutdown()
    {
        if (false === $this->booted) {
            return;
        }

        $this->booted = false;

        foreach ($this->getBundles() as $bundle) {
            $bundle->shutdown();
            $bundle->setContainer(null);
        }

        $this->container = null;
    }

    
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (false === $this->booted) {
            $this->boot();
        }

        return $this->getHttpKernel()->handle($request, $type, $catch);
    }

    
    protected function getHttpKernel()
    {
        return $this->container->get('http_kernel');
    }

    
    public function getBundles()
    {
        return $this->bundles;
    }

    
    public function isClassInActiveBundle($class)
    {
        foreach ($this->getBundles() as $bundle) {
            if (0 === strpos($class, $bundle->getNamespace())) {
                return true;
            }
        }

        return false;
    }

    
    public function getBundle($name, $first = true)
    {
        if (!isset($this->bundleMap[$name])) {
            throw new \InvalidArgumentException(sprintf('Bundle "%s" does not exist or it is not enabled. Maybe you forgot to add it in the registerBundles() function of your %s.php file?', $name, get_class($this)));
        }

        if (true === $first) {
            return $this->bundleMap[$name][0];
        }

        return $this->bundleMap[$name];
    }

    
    public function locateResource($name, $dir = null, $first = true)
    {
        if ('@' !== $name[0]) {
            throw new \InvalidArgumentException(sprintf('A resource name must start with @ ("%s" given).', $name));
        }

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $name));
        }

        $bundleName = substr($name, 1);
        $path = '';
        if (false !== strpos($bundleName, '/')) {
            list($bundleName, $path) = explode('/', $bundleName, 2);
        }

        $isResource = 0 === strpos($path, 'Resources') && null !== $dir;
        $overridePath = substr($path, 9);
        $resourceBundle = null;
        $bundles = $this->getBundle($bundleName, false);
        $files = array();

        foreach ($bundles as $bundle) {
            if ($isResource && file_exists($file = $dir.'/'.$bundle->getName().$overridePath)) {
                if (null !== $resourceBundle) {
                    throw new \RuntimeException(sprintf('"%s" resource is hidden by a resource from the "%s" derived bundle. Create a "%s" file to override the bundle resource.',
                        $file,
                        $resourceBundle,
                        $dir.'/'.$bundles[0]->getName().$overridePath
                    ));
                }

                if ($first) {
                    return $file;
                }
                $files[] = $file;
            }

            if (file_exists($file = $bundle->getPath().'/'.$path)) {
                if ($first && !$isResource) {
                    return $file;
                }
                $files[] = $file;
                $resourceBundle = $bundle->getName();
            }
        }

        if (count($files) > 0) {
            return $first && $isResource ? $files[0] : $files;
        }

        throw new \InvalidArgumentException(sprintf('Unable to find file "%s".', $name));
    }

    
    public function getName()
    {
        return $this->name;
    }

    
    public function getEnvironment()
    {
        return $this->environment;
    }

    
    public function isDebug()
    {
        return $this->debug;
    }

    
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace('\\', '/', dirname($r->getFileName()));
        }

        return $this->rootDir;
    }

    
    public function getContainer()
    {
        return $this->container;
    }

    
    public function loadClassCache($name = 'classes', $extension = '.php')
    {
        if (!$this->booted && file_exists($this->getCacheDir().'/classes.map')) {
            ClassCollectionLoader::load(include($this->getCacheDir().'/classes.map'), $this->getCacheDir(), $name, $this->debug, false, $extension);
        }
    }

    
    public function setClassCache(array $classes)
    {
        file_put_contents($this->getCacheDir().'/classes.map', sprintf('<?php return %s;', var_export($classes, true)));
    }

    
    public function getStartTime()
    {
        return $this->debug ? $this->startTime : -INF;
    }

    
    public function getCacheDir()
    {
        return $this->rootDir.'/cache/'.$this->environment;
    }

    
    public function getLogDir()
    {
        return $this->rootDir.'/logs';
    }

    
    protected function initializeBundles()
    {
                $this->bundles = array();
        $topMostBundles = array();
        $directChildren = array();

        foreach ($this->registerBundles() as $bundle) {
            $name = $bundle->getName();
            if (isset($this->bundles[$name])) {
                throw new \LogicException(sprintf('Trying to register two bundles with the same name "%s"', $name));
            }
            $this->bundles[$name] = $bundle;

            if ($parentName = $bundle->getParent()) {
                if (isset($directChildren[$parentName])) {
                    throw new \LogicException(sprintf('Bundle "%s" is directly extended by two bundles "%s" and "%s".', $parentName, $name, $directChildren[$parentName]));
                }
                if ($parentName == $name) {
                    throw new \LogicException(sprintf('Bundle "%s" can not extend itself.', $name));
                }
                $directChildren[$parentName] = $name;
            } else {
                $topMostBundles[$name] = $bundle;
            }
        }

                if (count($diff = array_values(array_diff(array_keys($directChildren), array_keys($this->bundles))))) {
            throw new \LogicException(sprintf('Bundle "%s" extends bundle "%s", which is not registered.', $directChildren[$diff[0]], $diff[0]));
        }

                $this->bundleMap = array();
        foreach ($topMostBundles as $name => $bundle) {
            $bundleMap = array($bundle);
            $hierarchy = array($name);

            while (isset($directChildren[$name])) {
                $name = $directChildren[$name];
                array_unshift($bundleMap, $this->bundles[$name]);
                $hierarchy[] = $name;
            }

            foreach ($hierarchy as $bundle) {
                $this->bundleMap[$bundle] = $bundleMap;
                array_pop($bundleMap);
            }
        }

    }

    
    protected function getContainerClass()
    {
        return $this->name.ucfirst($this->environment).($this->debug ? 'Debug' : '').'ProjectContainer';
    }

    
    protected function getContainerBaseClass()
    {
        return 'Container';
    }

    
    protected function initializeContainer()
    {
        $class = $this->getContainerClass();
        $cache = new ConfigCache($this->getCacheDir().'/'.$class.'.php', $this->debug);
        $fresh = true;
        if (!$cache->isFresh()) {
            $container = $this->buildContainer();
            $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

            $fresh = false;
        }

        require_once $cache;

        $this->container = new $class();
        $this->container->set('kernel', $this);

        if (!$fresh && $this->container->has('cache_warmer')) {
            $this->container->get('cache_warmer')->warmUp($this->container->getParameter('kernel.cache_dir'));
        }
    }

    
    protected function getKernelParameters()
    {
        $bundles = array();
        foreach ($this->bundles as $name => $bundle) {
            $bundles[$name] = get_class($bundle);
        }

        return array_merge(
            array(
                'kernel.root_dir'        => $this->rootDir,
                'kernel.environment'     => $this->environment,
                'kernel.debug'           => $this->debug,
                'kernel.name'            => $this->name,
                'kernel.cache_dir'       => $this->getCacheDir(),
                'kernel.logs_dir'        => $this->getLogDir(),
                'kernel.bundles'         => $bundles,
                'kernel.charset'         => 'UTF-8',
                'kernel.container_class' => $this->getContainerClass(),
            ),
            $this->getEnvParameters()
        );
    }

    
    protected function getEnvParameters()
    {
        $parameters = array();
        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'SYMFONY__')) {
                $parameters[strtolower(str_replace('__', '.', substr($key, 9)))] = $value;
            }
        }

        return $parameters;
    }

    
    protected function buildContainer()
    {
        foreach (array('cache' => $this->getCacheDir(), 'logs' => $this->getLogDir()) as $name => $dir) {
            if (!is_dir($dir)) {
                if (false === @mkdir($dir, 0777, true)) {
                    throw new \RuntimeException(sprintf("Unable to create the %s directory (%s)\n", $name, $dir));
                }
            } elseif (!is_writable($dir)) {
                throw new \RuntimeException(sprintf("Unable to write in the %s directory (%s)\n", $name, $dir));
            }
        }

        $container = new ContainerBuilder(new ParameterBag($this->getKernelParameters()));
        $extensions = array();
        foreach ($this->bundles as $bundle) {
            $bundle->build($container);

            if ($extension = $bundle->getContainerExtension()) {
                $container->registerExtension($extension);
                $extensions[] = $extension->getAlias();
            }

            if ($this->debug) {
                $container->addObjectResource($bundle);
            }
        }
        $container->addObjectResource($this);

                $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));

        if (null !== $cont = $this->registerContainerConfiguration($this->getContainerLoader($container))) {
            $container->merge($cont);
        }

        $container->addCompilerPass(new AddClassesToCachePass($this));
        $container->compile();

        return $container;
    }

    
    protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
    {
                $dumper = new PhpDumper($container);
        $content = $dumper->dump(array('class' => $class, 'base_class' => $baseClass));
        if (!$this->debug) {
            $content = self::stripComments($content);
        }

        $cache->write($content, $container->getResources());
    }

    
    protected function getContainerLoader(ContainerInterface $container)
    {
        $locator = new FileLocator($this);
        $resolver = new LoaderResolver(array(
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new ClosureLoader($container),
        ));

        return new DelegatingLoader($resolver);
    }

    
    public static function stripComments($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (!in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= $token[1];
            }
        }

                $output = preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $output);

        return $output;
    }

    public function serialize()
    {
        return serialize(array($this->environment, $this->debug));
    }

    public function unserialize($data)
    {
        list($environment, $debug) = unserialize($data);

        $this->__construct($environment, $debug);
    }
}
}
 



namespace Symfony\Component\ClassLoader
{


class ClassCollectionLoader
{
    private static $loaded;

    
    public static function load($classes, $cacheDir, $name, $autoReload, $adaptive = false, $extension = '.php')
    {
                if (isset(self::$loaded[$name])) {
            return;
        }

        self::$loaded[$name] = true;

        if ($adaptive) {
                        $classes = array_diff($classes, get_declared_classes(), get_declared_interfaces());

                        $name = $name.'-'.substr(md5(implode('|', $classes)), 0, 5);
        }

        $cache = $cacheDir.'/'.$name.$extension;

                $reload = false;
        if ($autoReload) {
            $metadata = $cacheDir.'/'.$name.$extension.'.meta';
            if (!file_exists($metadata) || !file_exists($cache)) {
                $reload = true;
            } else {
                $time = filemtime($cache);
                $meta = unserialize(file_get_contents($metadata));

                if ($meta[1] != $classes) {
                    $reload = true;
                } else {
                    foreach ($meta[0] as $resource) {
                        if (!file_exists($resource) || filemtime($resource) > $time) {
                            $reload = true;

                            break;
                        }
                    }
                }
            }
        }

        if (!$reload && file_exists($cache)) {
            require_once $cache;

            return;
        }

        $files = array();
        $content = '';
        foreach ($classes as $class) {
            if (!class_exists($class) && !interface_exists($class) && (!function_exists('trait_exists') || !trait_exists($class))) {
                throw new \InvalidArgumentException(sprintf('Unable to load class "%s"', $class));
            }

            $r = new \ReflectionClass($class);
            $files[] = $r->getFileName();

            $c = preg_replace(array('/^\s*<\?php/', '/\?>\s*$/'), '', file_get_contents($r->getFileName()));

                        if (!$r->inNamespace()) {
                $c = "\nnamespace\n{\n".self::stripComments($c)."\n}\n";
            } else {
                $c = self::fixNamespaceDeclarations('<?php '.$c);
                $c = preg_replace('/^\s*<\?php/', '', $c);
            }

            $content .= $c;
        }

                if (!is_dir(dirname($cache))) {
            mkdir(dirname($cache), 0777, true);
        }
        self::writeCacheFile($cache, '<?php '.$content);

        if ($autoReload) {
                        self::writeCacheFile($metadata, serialize(array($files, $classes)));
        }
    }

    
    public static function fixNamespaceDeclarations($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        $inNamespace = false;
        $tokens = token_get_all($source);

        for ($i = 0, $max = count($tokens); $i < $max; $i++) {
            $token = $tokens[$i];
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                                continue;
            } elseif (T_NAMESPACE === $token[0]) {
                if ($inNamespace) {
                    $output .= "}\n";
                }
                $output .= $token[1];

                                while (($t = $tokens[++$i]) && is_array($t) && in_array($t[0], array(T_WHITESPACE, T_NS_SEPARATOR, T_STRING))) {
                    $output .= $t[1];
                }
                if (is_string($t) && '{' === $t) {
                    $inNamespace = false;
                    --$i;
                } else {
                    $output .= "\n{";
                    $inNamespace = true;
                }
            } else {
                $output .= $token[1];
            }
        }

        if ($inNamespace) {
            $output .= "}\n";
        }

        return $output;
    }

    
    private static function writeCacheFile($file, $content)
    {
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            chmod($file, 0644);

            return;
        }

        throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }

    
    private static function stripComments($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (!in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= $token[1];
            }
        }

                $output = preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $output);

        return $output;
    }
}
}
 



namespace Symfony\Component\ClassLoader
{


class UniversalClassLoader
{
    private $namespaces = array();
    private $prefixes = array();
    private $namespaceFallbacks = array();
    private $prefixFallbacks = array();

    
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    
    public function getPrefixes()
    {
        return $this->prefixes;
    }

    
    public function getNamespaceFallbacks()
    {
        return $this->namespaceFallbacks;
    }

    
    public function getPrefixFallbacks()
    {
        return $this->prefixFallbacks;
    }

    
    public function registerNamespaceFallbacks(array $dirs)
    {
        $this->namespaceFallbacks = $dirs;
    }

    
    public function registerPrefixFallbacks(array $dirs)
    {
        $this->prefixFallbacks = $dirs;
    }

    
    public function registerNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace => $locations) {
            $this->namespaces[$namespace] = (array) $locations;
        }
    }

    
    public function registerNamespace($namespace, $paths)
    {
        $this->namespaces[$namespace] = (array) $paths;
    }

    
    public function registerPrefixes(array $classes)
    {
        foreach ($classes as $prefix => $locations) {
            $this->prefixes[$prefix] = (array) $locations;
        }
    }

    
    public function registerPrefix($prefix, $paths)
    {
        $this->prefixes[$prefix] = (array) $paths;
    }

    
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            require $file;
        }
    }

    
    public function findFile($class)
    {
        if ('\\' == $class[0]) {
            $class = substr($class, 1);
        }

        if (false !== $pos = strrpos($class, '\\')) {
                        $namespace = substr($class, 0, $pos);
            foreach ($this->namespaces as $ns => $dirs) {
                if (0 !== strpos($namespace, $ns)) {
                    continue;
                }

                foreach ($dirs as $dir) {
                    $className = substr($class, $pos + 1);
                    $file = $dir.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';
                    if (file_exists($file)) {
                        return $file;
                    }
                }
            }

            foreach ($this->namespaceFallbacks as $dir) {
                $file = $dir.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
                if (file_exists($file)) {
                    return $file;
                }
            }
        } else {
                        foreach ($this->prefixes as $prefix => $dirs) {
                if (0 !== strpos($class, $prefix)) {
                    continue;
                }

                foreach ($dirs as $dir) {
                    $file = $dir.DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
                    if (file_exists($file)) {
                        return $file;
                    }
                }
            }

            foreach ($this->prefixFallbacks as $dir) {
                $file = $dir.DIRECTORY_SEPARATOR.str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
                if (file_exists($file)) {
                    return $file;
                }
            }
        }
    }
}
}
 



namespace Symfony\Component\HttpKernel\Bundle
{

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;


abstract class Bundle extends ContainerAware implements BundleInterface
{
    protected $name;
    protected $reflected;
    protected $extension;

    
    public function boot()
    {
    }

    
    public function shutdown()
    {
    }

    
    public function build(ContainerBuilder $container)
    {
    }

    
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $basename = preg_replace('/Bundle$/', '', $this->getName());

            $class = $this->getNamespace().'\\DependencyInjection\\'.$basename.'Extension';
            if (class_exists($class)) {
                $extension = new $class();

                                $expectedAlias = Container::underscore($basename);
                if ($expectedAlias != $extension->getAlias()) {
                    throw new \LogicException(sprintf(
                        'The extension alias for the default extension of a '.
                        'bundle must be the underscored version of the '.
                        'bundle name ("%s" instead of "%s")',
                        $expectedAlias, $extension->getAlias()
                    ));
                }

                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }

        if ($this->extension) {
            return $this->extension;
        }
    }

    
    public function getNamespace()
    {
        if (null === $this->reflected) {
            $this->reflected = new \ReflectionObject($this);
        }

        return $this->reflected->getNamespaceName();
    }

    
    public function getPath()
    {
        if (null === $this->reflected) {
            $this->reflected = new \ReflectionObject($this);
        }

        return dirname($this->reflected->getFileName());
    }

    
    public function getParent()
    {
        return null;
    }

    
    final public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $name = get_class($this);
        $pos = strrpos($name, '\\');

        return $this->name = false === $pos ? $name :  substr($name, $pos + 1);
    }

    
    public function registerCommands(Application $application)
    {
        if (!$dir = realpath($this->getPath().'/Command')) {
            return;
        }

        $finder = new Finder();
        $finder->files()->name('*Command.php')->in($dir);

        $prefix = $this->getNamespace().'\\Command';
        foreach ($finder as $file) {
            $ns = $prefix;
            if ($relativePath = $file->getRelativePath()) {
                $ns .= '\\'.strtr($relativePath, '/', '\\');
            }
            $r = new \ReflectionClass($ns.'\\'.$file->getBasename('.php'));
            if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract()) {
                $application->add($r->newInstance());
            }
        }
    }
}
}
 



namespace Symfony\Component\HttpKernel\Bundle
{

use Symfony\Component\DependencyInjection\ContainerBuilder;


interface BundleInterface
{
    
    public function boot();

    
    public function shutdown();

    
    public function build(ContainerBuilder $container);

    
    public function getContainerExtension();

    
    public function getParent();

    
    public function getName();

    
    public function getNamespace();

    
    public function getPath();
}
}
 



namespace Symfony\Component\Config
{


class ConfigCache
{
    private $debug;
    private $file;

    
    public function __construct($file, $debug)
    {
        $this->file = $file;
        $this->debug = (Boolean) $debug;
    }

    
    public function __toString()
    {
        return $this->file;
    }

    
    public function isFresh()
    {
        if (!file_exists($this->file)) {
            return false;
        }

        if (!$this->debug) {
            return true;
        }

        $metadata = $this->file.'.meta';
        if (!file_exists($metadata)) {
            return false;
        }

        $time = filemtime($this->file);
        $meta = unserialize(file_get_contents($metadata));
        foreach ($meta as $resource) {
            if (!$resource->isFresh($time)) {
                return false;
            }
        }

        return true;
    }

    
    public function write($content, array $metadata = null)
    {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException(sprintf('Unable to create the %s directory', $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new \RuntimeException(sprintf('Unable to write in the %s directory', $dir));
        }

        $tmpFile = tempnam(dirname($this->file), basename($this->file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $this->file)) {
            chmod($this->file, 0666);
        } else {
            throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $this->file));
        }

        if (null !== $metadata && true === $this->debug) {
            $file = $this->file.'.meta';
            $tmpFile = tempnam(dirname($file), basename($file));
            if (false !== @file_put_contents($tmpFile, serialize($metadata)) && @rename($tmpFile, $file)) {
                chmod($file, 0666);
            }
        }
    }
}
}
 




namespace Orb\Util
{


class ClassLoader extends \Symfony\Component\ClassLoader\UniversalClassLoader
{
	
	protected $class_map = array();

	
	protected $namespace_callback = array();


	
	public function getClassNameMap()
	{
		return $this->class_map;
	}


	
	public function registerNamespaceCallback($namespace, $callback)
	{
		$this->namespace_callback[$namespace] = $callback;
	}


	
	public function registerClassName($class_name, $path)
	{
		$this->class_map[$class_name] = $path;
	}



	
	public function registerClassNames(array $class_names)
	{
		$this->class_map = array_merge($this->class_map, $class_names);
	}


	public function findFile($class_name)
	{
		if (isset($this->class_map[$class_name])) {
			$file = $this->class_map[$class_name];
			if (file_exists($file)) {
				return $file;
			}
		}

		$file = parent::findFile($class_name);

		if (!$file) {
			$m = null;
			$ns_parts = explode('\\', $class_name, 2);
			if (count($ns_parts) == 2) {
				$ns = $ns_parts[0];
				if (isset($this->namespace_callback[$ns])) {
					$callback = $this->namespace_callback[$ns];
					$file = call_user_func($callback, $class_name);
				}
			}
		}

		return $file;
	}
}
}
