<?php
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
class CliKernelContainer extends \Application\DeskPRO\DependencyInjection\DeskproContainer
{
    public function __construct()
    {
        $this->parameters = $this->getDefaultParameters();
        $this->services =
        $this->scopedServices =
        $this->scopeStacks = array();
        $this->set('service_container', $this);
        $this->scopes = array('request' => 'container');
        $this->scopeChildren = array('request' => array());
    }
    protected function getAnnotationReaderService()
    {
        return $this->services['annotation_reader'] = new \Doctrine\Common\Annotations\FileCacheReader(new \Doctrine\Common\Annotations\AnnotationReader(), DP_ROOT.'/sys/cache/prod/annotations', false);
    }
    protected function getBrowserSnifferService()
    {
        return $this->services['browser_sniffer'] = new \Browser();
    }
    protected function getCacheWarmerService()
    {
        $a = $this->get('kernel');
        $b = $this->get('templating.name_parser');
        $c = new \Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinder($a, $b, DP_ROOT.'/sys/Resources');
        return $this->services['cache_warmer'] = new \Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerAggregate(array(0 => new \Application\DeskPRO\CacheWarmer\TemplatePathsCacheWarmer($c, $this->get('templating.locator')), 1 => new \Symfony\Bundle\FrameworkBundle\CacheWarmer\RouterCacheWarmer($this->get('router')), 2 => new \Application\DeskPRO\Twig\CacheWarmer\TemplateCacheCacheWarmer($this, $c), 3 => new \Symfony\Bridge\Doctrine\CacheWarmer\ProxyCacheWarmer($this->get('doctrine'))));
    }
    protected function getDefaultResultCacheService()
    {
        $this->services['default_result_cache'] = $instance = new \Orb\Doctrine\Common\Cache\PreloadedMysqlCache($this->get('doctrine.dbal.default_connection'));
        $instance->setPrefix('dres', $this->get('deskpro.interface_value'));
        return $instance;
    }
    protected function getDeskpro_Cache_QueryListenerService()
    {
        return $this->services['deskpro.cache.query_listener'] = new \Application\DeskPRO\CacheInvalidator\QueryListener();
    }
    protected function getDeskpro_ConfigServiceLoaderService()
    {
        return $this->services['deskpro.config_service_loader'] = new \Application\DeskPRO\ConfigServiceLoader();
    }
    protected function getDeskpro_Core_InputCleanerService()
    {
        $this->services['deskpro.core.input_cleaner'] = $instance = new \Orb\Input\Cleaner\Cleaner();
        $instance->addCleaner($this->get('deskpro.core.input_cleaner_plugin_xss'));
        $instance->addCleaner($this->get('deskpro.core.input_cleaner_plugin_html_purifier'));
        return $instance;
    }
    protected function getDeskpro_Core_InputCleanerPluginHtmlPurifierService()
    {
        return $this->services['deskpro.core.input_cleaner_plugin_html_purifier'] = new \Orb\Input\Cleaner\CleanerPlugin\HtmlPurifier();
    }
    protected function getDeskpro_Core_InputCleanerPluginXssService()
    {
        return $this->services['deskpro.core.input_cleaner_plugin_xss'] = new \Orb\Input\Cleaner\CleanerPlugin\BasicXss();
    }
    protected function getDeskpro_Core_InputReaderService()
    {
        $this->services['deskpro.core.input_reader'] = $instance = new \Orb\Input\Reader\Reader($this->get('deskpro.core.input_cleaner'));
        $instance->addSource('req', $this->get('deskpro.core.input_reader_req'));
        $instance->addSource('post', $this->get('deskpro.core.input_reader_post'));
        $instance->addSource('get', $this->get('deskpro.core.input_reader_get'));
        $instance->addSource('cookie', $this->get('deskpro.core.input_reader_cookie'));
        $instance->setArrayStringSeparator('.');
        return $instance;
    }
    protected function getDeskpro_Core_InputReaderCookieService()
    {
        return $this->services['deskpro.core.input_reader_cookie'] = new \Orb\Input\Reader\Source\Superglobal('_COOKIE');
    }
    protected function getDeskpro_Core_InputReaderGetService()
    {
        return $this->services['deskpro.core.input_reader_get'] = new \Orb\Input\Reader\Source\Superglobal('_GET');
    }
    protected function getDeskpro_Core_InputReaderPostService()
    {
        return $this->services['deskpro.core.input_reader_post'] = new \Orb\Input\Reader\Source\Superglobal('_POST');
    }
    protected function getDeskpro_Core_InputReaderReqService()
    {
        return $this->services['deskpro.core.input_reader_req'] = new \Orb\Input\Reader\Source\Superglobal('_REQUEST');
    }
    protected function getDeskpro_Core_SettingsService()
    {
        $this->services['deskpro.core.settings'] = $instance = new \Application\DeskPRO\Settings\Settings(array('core' => DP_ROOT.'/src/Application/DeskPRO/Resources/settings', 'agent' => DP_ROOT.'/src/Application/AgentBundle/Resources/settings', 'user' => DP_ROOT.'/src/Application/UserBundle/Resources/settings', 'dev' => DP_ROOT.'/src/Application/DevBundle/Resources/settings'), $this->get('doctrine.dbal.default_connection'));
        $instance->loadGroups('core');
        return $instance;
    }
    protected function getDeskpro_Core_TranslateService()
    {
        $this->services['deskpro.core.translate'] = $instance = new \Application\DeskPRO\Translate\Translate($this->get('deskpro.core.translate_loader'), $this->get('event_dispatcher'));
        $instance->setSession($this->get('session'));
        return $instance;
    }
    protected function getDeskpro_Core_TranslateLoaderService()
    {
        $this->services['deskpro.core.translate_loader'] = $instance = new \Application\DeskPRO\Translate\Loader\DeskproLoader();
        $instance->setSystemLoader($this->get('deskpro.core.translate_loader_system'));
        $instance->setDbLoader($this->get('deskpro.core.translate_loader_db'));
        return $instance;
    }
    protected function getDeskpro_Core_TranslateLoaderDbService()
    {
        return $this->services['deskpro.core.translate_loader_db'] = new \Application\DeskPRO\Translate\Loader\DbLoader($this->get('doctrine.dbal.default_connection'));
    }
    protected function getDeskpro_Core_TranslateLoaderSystemService()
    {
        return $this->services['deskpro.core.translate_loader_system'] = new \Application\DeskPRO\Translate\Loader\SystemLoader(array(0 => DP_ROOT.'/languages'));
    }
    protected function getDeskpro_Dbal_Logger_CacheQueryListenerService()
    {
        return $this->services['deskpro.dbal.logger.cache_query_listener'] = new \Application\DeskPRO\DBAL\Logging\CacheExec($this->get('deskpro.cache.query_listener'));
    }
    protected function getDeskpro_Dbal_Logger_QueryLoggerService()
    {
        return $this->services['deskpro.dbal.logger.query_logger'] = new \Application\DeskPRO\DBAL\Logging\SysQueryLogger();
    }
    protected function getDeskpro_ExceptionLoggerService()
    {
        return $this->services['deskpro.exception_logger'] = new \Application\DeskPRO\HttpKernel\ExceptionListener();
    }
    protected function getDeskpro_InterfaceValueService()
    {
        return $this->services['deskpro.interface_value'] = new \Application\DeskPRO\InterfaceValue();
    }
    protected function getDeskpro_MailLoggerService()
    {
        return $this->services['deskpro.mail_logger'] = $this->get('service_container')->getSystemService('mail_logger');
    }
    protected function getDeskpro_PersonActivityLoggerService()
    {
        return $this->services['deskpro.person_activity_logger'] = new \Application\DeskPRO\People\ActivityLogger\ActivityLogger($this->get('doctrine.orm.default_entity_manager'));
    }
    protected function getDeskpro_PluginManagerService()
    {
        return $this->services['deskpro.plugin_manager'] = new \Application\DeskPRO\Plugin\PluginManager($this->get('doctrine.orm.default_entity_manager'));
    }
    protected function getDeskpro_Profiler_RequestMatcherService()
    {
        return $this->services['deskpro.profiler.request_matcher'] = new \Application\DeskPRO\Profiler\RequestMatcher();
    }
    protected function getDeskpro_Search_EntityListenerService()
    {
        return $this->services['deskpro.search.entity_listener'] = new \Application\DeskPRO\Search\EntityWatcher\EntityWatcher($this);
    }
    protected function getDeskpro_SearchAdapterService()
    {
        return $this->services['deskpro.search_adapter'] = call_user_func(array('Application\\DeskPRO\\StaticLoader\\SearchAdapter', 'getSearchAdapter'));
    }
    protected function getDeskpro_SearchAdapterEntityListenerService()
    {
        return $this->services['deskpro.search_adapter_entity_listener'] = new \Application\DeskPRO\Search\EntityListener($this->get('deskpro.search_adapter'));
    }
    protected function getDeskpro_SearchIndex_EntityUpdaterListenerService()
    {
        return $this->services['deskpro.search_index.entity_updater_listener'] = new \Application\DeskPRO\Entity\EventListener\SearchUpdater($this);
    }
    protected function getDeskpro_ServiceUrlsService()
    {
        $this->services['deskpro.service_urls'] = $instance = new \Application\DeskPRO\Settings\ServiceUrls();
        $instance->loadPack(DP_ROOT.'/sys/config/service-urls.php');
        return $instance;
    }
    protected function getDeskpro_SessionPersonService()
    {
        return $this->services['deskpro.session_person'] = $this->get('session')->getPerson();
    }
    protected function getDeskpro_SysEventsLoaderService()
    {
        $this->services['deskpro.sys_events_loader'] = $instance = new \Application\DeskPRO\StaticLoader\SystemEvents($this->get('event_dispatcher'));
        $instance->addNoPhraseEventListener();
        return $instance;
    }
    protected function getDeskpro_UserPortalPageService()
    {
        $this->services['deskpro.user_portal_page'] = $instance = new \Application\DeskPRO\PageDisplay\Page\PortalPage($this, $this->get('deskpro.session_person'));
        $instance->setLazyLoader($this->get('deskpro.user_portal_page_loader'));
        return $instance;
    }
    protected function getDeskpro_UserPortalPageLoaderService()
    {
        return $this->services['deskpro.user_portal_page_loader'] = new \Application\DeskPRO\PageDisplay\Page\PortalPageLoader($this->get('doctrine.orm.default_entity_manager'));
    }
    protected function getDoctrineService()
    {
        return $this->services['doctrine'] = new \Symfony\Bundle\DoctrineBundle\Registry($this, array('default' => 'doctrine.dbal.default_connection', 'read' => 'doctrine.dbal.read_connection'), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
    }
    protected function getDoctrine_Dbal_ConnectionFactoryService()
    {
        $this->services['doctrine.dbal.connection_factory'] = $instance = new \Application\DeskPRO\DBAL\ConnectionFactory(array());
        $instance->setContainer($this);
        return $instance;
    }
    protected function getDoctrine_Dbal_DefaultConnectionService()
    {
        $a = new \Doctrine\DBAL\Configuration();
        $a->setSQLLogger($this->get('doctrine.dbal.logger'));
        $b = new \Doctrine\Common\EventManager();
        $b->addEventSubscriber($this->get('deskpro.search_index.entity_updater_listener'));
        $b->addEventSubscriber($this->get('deskpro.search.entity_listener'));
        return $this->services['doctrine.dbal.default_connection'] = $this->get('doctrine.dbal.connection_factory')->createConnection(array('host' => 'from_user_config.db', 'port' => NULL, 'user' => 'root', 'password' => NULL, 'driver' => 'pdo_mysql', 'driverOptions' => array()), $a, $b, array());
    }
    protected function getDoctrine_Dbal_LoggerService()
    {
        $this->services['doctrine.dbal.logger'] = $instance = new \Application\DeskPRO\DBAL\Logging\DelegateLogger();
        $instance->addLogger($this->get('deskpro.dbal.logger.cache_query_listener'), 'cache_query_listener');
        $instance->addLogger($this->get('deskpro.dbal.logger.query_logger'), 'query_logger');
        return $instance;
    }
    protected function getDoctrine_Dbal_ReadConnectionService()
    {
        $a = new \Doctrine\DBAL\Configuration();
        $a->setSQLLogger($this->get('doctrine.dbal.logger'));
        $b = new \Doctrine\Common\EventManager();
        $b->addEventSubscriber($this->get('deskpro.search.entity_listener'));
        return $this->services['doctrine.dbal.read_connection'] = $this->get('doctrine.dbal.connection_factory')->createConnection(array('host' => 'from_user_config.db_read', 'port' => NULL, 'user' => 'root', 'password' => NULL, 'driver' => 'pdo_mysql', 'driverOptions' => array()), $a, $b, array());
    }
    protected function getDoctrine_Orm_DefaultEntityManagerService()
    {
        $a = new \Doctrine\Common\Cache\ArrayCache();
        $a->setNamespace('sf2orm_default_c19e66b77a4bb7be728c99ef7aac008c');
        $b = new \Doctrine\Common\Cache\ArrayCache();
        $b->setNamespace('sf2orm_default_c19e66b77a4bb7be728c99ef7aac008c');
        $c = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        $c->addDriver(new \Doctrine\ORM\Mapping\Driver\StaticPHPDriver(array(0 => DP_ROOT.'/src/Application/DeskPRO/Entity')), 'Application\\DeskPRO\\Entity');
        $d = new \Doctrine\ORM\Configuration();
        $d->setEntityNamespaces(array('DeskPRO' => 'Application\\DeskPRO\\Entity'));
        $d->setMetadataCacheImpl($a);
        $d->setQueryCacheImpl($this->get('doctrine.orm.default_query_cache'));
        $d->setResultCacheImpl($b);
        $d->setMetadataDriverImpl($c);
        $d->setProxyDir(DP_ROOT.'/sys/cache/prod/../doctrine-proxies');
        $d->setProxyNamespace('Proxies');
        $d->setAutoGenerateProxyClasses(false);
        $d->setClassMetadataFactoryName('Orb\\Doctrine\\ORM\\Mapping\\StaticClassMetadataFactory');
        return $this->services['doctrine.orm.default_entity_manager'] = call_user_func(array('Application\\DeskPRO\\ORM\\EntityManager', 'create'), $this->get('doctrine.dbal.default_connection'), $d);
    }
    protected function getDoctrine_Orm_DefaultQueryCacheService()
    {
        $this->services['doctrine.orm.default_query_cache'] = $instance = call_user_func(array('Application\\DeskPRO\\DependencyInjection\\SystemServices\\ArrayFileCacheFactory', 'create'), 'dql');
        $instance->registerShutdownCommit();
        return $instance;
    }
    protected function getDoctrine_Orm_Validator_UniqueService()
    {
        return $this->services['doctrine.orm.validator.unique'] = new \Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator($this->get('doctrine'));
    }
    protected function getDoctrine_Orm_ValidatorInitializerService()
    {
        return $this->services['doctrine.orm.validator_initializer'] = new \Symfony\Bridge\Doctrine\Validator\EntityInitializer($this->get('doctrine'));
    }
    protected function getDoctrine_QueryLoggerService()
    {
        return $this->services['doctrine.query_logger'] = new \Symfony\Bridge\Doctrine\Logger\DbalLogger($this->get('logger'));
    }
    protected function getEventDispatcherService()
    {
        $this->services['event_dispatcher'] = $instance = new \Symfony\Bundle\FrameworkBundle\ContainerAwareEventDispatcher($this);
        $instance->addListenerService('kernel.exception', array(0 => 'deskpro.exception_logger', 1 => 'onKernelException'), -128);
        $instance->addListenerService('kernel.request', array(0 => 'router_listener', 1 => 'onEarlyKernelRequest'), 255);
        $instance->addListenerService('kernel.request', array(0 => 'router_listener', 1 => 'onKernelRequest'), 0);
        $instance->addListenerService('kernel.response', array(0 => 'response_listener', 1 => 'onKernelResponse'), 0);
        $instance->addListenerService('kernel.request', array(0 => 'session_listener', 1 => 'onKernelRequest'), 128);
        $instance->addListenerService('kernel.exception', array(0 => 'twig.exception_listener', 1 => 'onKernelException'), -128);
        return $instance;
    }
    protected function getFileLocatorService()
    {
        return $this->services['file_locator'] = new \Application\DeskPRO\HttpKernel\Config\FileLocator($this->get('kernel'), DP_ROOT.'/sys/Resources');
    }
    protected function getFilesystemService()
    {
        return $this->services['filesystem'] = new \Symfony\Component\Filesystem\Filesystem();
    }
    protected function getForm_FactoryService()
    {
        return $this->services['form.factory'] = new \Symfony\Component\Form\FormFactory(array(0 => new \Symfony\Component\Form\Extension\DependencyInjection\DependencyInjectionExtension($this, array('field' => 'form.type.field', 'form' => 'form.type.form', 'birthday' => 'form.type.birthday', 'checkbox' => 'form.type.checkbox', 'choice' => 'form.type.choice', 'collection' => 'form.type.collection', 'country' => 'form.type.country', 'date' => 'form.type.date', 'datetime' => 'form.type.datetime', 'email' => 'form.type.email', 'file' => 'form.type.file', 'hidden' => 'form.type.hidden', 'integer' => 'form.type.integer', 'language' => 'form.type.language', 'locale' => 'form.type.locale', 'money' => 'form.type.money', 'number' => 'form.type.number', 'password' => 'form.type.password', 'percent' => 'form.type.percent', 'radio' => 'form.type.radio', 'repeated' => 'form.type.repeated', 'search' => 'form.type.search', 'textarea' => 'form.type.textarea', 'text' => 'form.type.text', 'time' => 'form.type.time', 'timezone' => 'form.type.timezone', 'url' => 'form.type.url', 'entity' => 'form.type.entity'), array('field' => array(0 => 'form.type_extension.field')), array(0 => 'form.type_guesser.validator', 1 => 'form.type_guesser.doctrine'))));
    }
    protected function getForm_Type_BirthdayService()
    {
        return $this->services['form.type.birthday'] = new \Symfony\Component\Form\Extension\Core\Type\BirthdayType();
    }
    protected function getForm_Type_CheckboxService()
    {
        return $this->services['form.type.checkbox'] = new \Symfony\Component\Form\Extension\Core\Type\CheckboxType();
    }
    protected function getForm_Type_ChoiceService()
    {
        return $this->services['form.type.choice'] = new \Symfony\Component\Form\Extension\Core\Type\ChoiceType();
    }
    protected function getForm_Type_CollectionService()
    {
        return $this->services['form.type.collection'] = new \Symfony\Component\Form\Extension\Core\Type\CollectionType();
    }
    protected function getForm_Type_CountryService()
    {
        return $this->services['form.type.country'] = new \Symfony\Component\Form\Extension\Core\Type\CountryType();
    }
    protected function getForm_Type_DateService()
    {
        return $this->services['form.type.date'] = new \Symfony\Component\Form\Extension\Core\Type\DateType();
    }
    protected function getForm_Type_DatetimeService()
    {
        return $this->services['form.type.datetime'] = new \Symfony\Component\Form\Extension\Core\Type\DateTimeType();
    }
    protected function getForm_Type_EmailService()
    {
        return $this->services['form.type.email'] = new \Symfony\Component\Form\Extension\Core\Type\EmailType();
    }
    protected function getForm_Type_EntityService()
    {
        return $this->services['form.type.entity'] = new \Symfony\Bridge\Doctrine\Form\Type\EntityType($this->get('doctrine'));
    }
    protected function getForm_Type_FieldService()
    {
        return $this->services['form.type.field'] = new \Symfony\Component\Form\Extension\Core\Type\FieldType($this->get('validator'));
    }
    protected function getForm_Type_FileService()
    {
        return $this->services['form.type.file'] = new \Symfony\Component\Form\Extension\Core\Type\FileType();
    }
    protected function getForm_Type_FormService()
    {
        return $this->services['form.type.form'] = new \Symfony\Component\Form\Extension\Core\Type\FormType();
    }
    protected function getForm_Type_HiddenService()
    {
        return $this->services['form.type.hidden'] = new \Symfony\Component\Form\Extension\Core\Type\HiddenType();
    }
    protected function getForm_Type_IntegerService()
    {
        return $this->services['form.type.integer'] = new \Symfony\Component\Form\Extension\Core\Type\IntegerType();
    }
    protected function getForm_Type_LanguageService()
    {
        return $this->services['form.type.language'] = new \Symfony\Component\Form\Extension\Core\Type\LanguageType();
    }
    protected function getForm_Type_LocaleService()
    {
        return $this->services['form.type.locale'] = new \Symfony\Component\Form\Extension\Core\Type\LocaleType();
    }
    protected function getForm_Type_MoneyService()
    {
        return $this->services['form.type.money'] = new \Symfony\Component\Form\Extension\Core\Type\MoneyType();
    }
    protected function getForm_Type_NumberService()
    {
        return $this->services['form.type.number'] = new \Symfony\Component\Form\Extension\Core\Type\NumberType();
    }
    protected function getForm_Type_PasswordService()
    {
        return $this->services['form.type.password'] = new \Symfony\Component\Form\Extension\Core\Type\PasswordType();
    }
    protected function getForm_Type_PercentService()
    {
        return $this->services['form.type.percent'] = new \Symfony\Component\Form\Extension\Core\Type\PercentType();
    }
    protected function getForm_Type_RadioService()
    {
        return $this->services['form.type.radio'] = new \Symfony\Component\Form\Extension\Core\Type\RadioType();
    }
    protected function getForm_Type_RepeatedService()
    {
        return $this->services['form.type.repeated'] = new \Symfony\Component\Form\Extension\Core\Type\RepeatedType();
    }
    protected function getForm_Type_SearchService()
    {
        return $this->services['form.type.search'] = new \Symfony\Component\Form\Extension\Core\Type\SearchType();
    }
    protected function getForm_Type_TextService()
    {
        return $this->services['form.type.text'] = new \Symfony\Component\Form\Extension\Core\Type\TextType();
    }
    protected function getForm_Type_TextareaService()
    {
        return $this->services['form.type.textarea'] = new \Symfony\Component\Form\Extension\Core\Type\TextareaType();
    }
    protected function getForm_Type_TimeService()
    {
        return $this->services['form.type.time'] = new \Symfony\Component\Form\Extension\Core\Type\TimeType();
    }
    protected function getForm_Type_TimezoneService()
    {
        return $this->services['form.type.timezone'] = new \Symfony\Component\Form\Extension\Core\Type\TimezoneType();
    }
    protected function getForm_Type_UrlService()
    {
        return $this->services['form.type.url'] = new \Symfony\Component\Form\Extension\Core\Type\UrlType();
    }
    protected function getForm_TypeExtension_FieldService()
    {
        return $this->services['form.type_extension.field'] = new \Symfony\Component\Form\Extension\Validator\Type\FieldTypeValidatorExtension($this->get('validator'));
    }
    protected function getForm_TypeGuesser_DoctrineService()
    {
        return $this->services['form.type_guesser.doctrine'] = new \Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser($this->get('doctrine'));
    }
    protected function getForm_TypeGuesser_ValidatorService()
    {
        return $this->services['form.type_guesser.validator'] = new \Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser($this->get('validator.mapping.class_metadata_factory'));
    }
    protected function getHttpKernelService()
    {
        return $this->services['http_kernel'] = new \Application\DeskPRO\HttpKernel\HttpKernel($this->get('event_dispatcher'), $this, new \Application\DeskPRO\HttpKernel\Controller\ControllerResolver($this, $this->get('controller_name_converter'), $this->get('monolog.logger.request')));
    }
    protected function getKernelService()
    {
        throw new \RuntimeException('You have requested a synthetic service ("kernel"). The DIC does not know how to construct this service.');
    }
    protected function getLoggerService()
    {
        $this->services['logger'] = $instance = new \Symfony\Bridge\Monolog\Logger('app');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getMonolog_Handler_MainService()
    {
        return $this->services['monolog.handler.main'] = new \Monolog\Handler\NullHandler(100, true);
    }
    protected function getMonolog_Logger_RequestService()
    {
        $this->services['monolog.logger.request'] = $instance = new \Symfony\Bridge\Monolog\Logger('request');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getMonolog_Logger_RouterService()
    {
        $this->services['monolog.logger.router'] = $instance = new \Symfony\Bridge\Monolog\Logger('router');
        $instance->pushHandler($this->get('monolog.handler.main'));
        return $instance;
    }
    protected function getRequestService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('request', 'request');
        }
        throw new \RuntimeException('You have requested a synthetic service ("request"). The DIC does not know how to construct this service.');
    }
    protected function getResponseService()
    {
        return $this->services['response'] = new \Symfony\Component\HttpFoundation\Response();
    }
    protected function getResponseListenerService()
    {
        return $this->services['response_listener'] = new \Symfony\Component\HttpKernel\EventListener\ResponseListener('UTF-8');
    }
    protected function getRouterService()
    {
        return $this->services['router'] = new \Application\DeskPRO\Routing\Router($this, DP_ROOT.'/sys/config/agent/routing.php', array('cache_dir' => DP_ROOT.'/sys/cache/prod/', 'debug' => false, 'generator_class' => 'Application\\DeskPRO\\Routing\\Generator\\UrlGenerator', 'generator_base_class' => 'Application\\DeskPRO\\Routing\\Generator\\UrlGenerator', 'generator_dumper_class' => 'Application\\DeskPRO\\Routing\\Generator\\Dumper\\PhpGeneratorDumper', 'generator_cache_class' => 'CliKernelprodUrlGenerator', 'matcher_class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher', 'matcher_base_class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher', 'matcher_dumper_class' => 'Application\\DeskPRO\\Routing\\Matcher\\Dumper\\PhpMatcherDumper', 'matcher_cache_class' => 'CliKernelprodUrlMatcher'));
    }
    protected function getRouterListenerService()
    {
        return $this->services['router_listener'] = new \Symfony\Bundle\FrameworkBundle\EventListener\RouterListener($this->get('router'), 80, 443, $this->get('monolog.logger.request'));
    }
    protected function getRouting_LoaderService()
    {
        $a = $this->get('file_locator');
        $b = new \Symfony\Component\Config\Loader\LoaderResolver();
        $b->addLoader(new \Symfony\Component\Routing\Loader\XmlFileLoader($a));
        $b->addLoader(new \Symfony\Component\Routing\Loader\YamlFileLoader($a));
        $b->addLoader(new \Symfony\Component\Routing\Loader\PhpFileLoader($a));
        return $this->services['routing.loader'] = new \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader($this->get('controller_name_converter'), $this->get('monolog.logger.router'), $b);
    }
    protected function getServiceContainerService()
    {
        throw new \RuntimeException('You have requested a synthetic service ("service_container"). The DIC does not know how to construct this service.');
    }
    protected function getSessionService()
    {
        return $this->services['session'] = new \Application\DeskPRO\HttpFoundation\Session($this->get('session.storage'), 'en');
    }
    protected function getSession_StorageService()
    {
        return $this->services['session.storage'] = new \Application\DeskPRO\HttpFoundation\SessionStorage\SessionEntityStorage($this->get('doctrine.orm.default_entity_manager'), array('lifetime' => 3600));
    }
    protected function getSessionListenerService()
    {
        return $this->services['session_listener'] = new \Symfony\Bundle\FrameworkBundle\EventListener\SessionListener($this, true);
    }
    protected function getSwiftmailer_MailerService()
    {
        return $this->services['swiftmailer.mailer'] = new \Application\DeskPRO\Mail\Mailer($this->get('swiftmailer.transport.dp_delegating'), $this->get('templating'), $this->get('deskpro.mail_logger'));
    }
    protected function getSwiftmailer_Plugin_MessageloggerService()
    {
        return $this->services['swiftmailer.plugin.messagelogger'] = new \Symfony\Bundle\SwiftmailerBundle\Logger\MessageLogger();
    }
    protected function getSwiftmailer_Transport_DpDelegatingService()
    {
        return $this->services['swiftmailer.transport.dp_delegating'] = new \Application\DeskPRO\Mail\Transport\DelegatingTransport(new \Swift_Events_SimpleEventDispatcher());
    }
    protected function getTemplatingService()
    {
        return $this->services['templating'] = new \Application\DeskPRO\Templating\Engine($this, array(0 => new \Application\DeskPRO\Twig\TwigEngine($this->get('twig'), $this->get('templating.name_parser'), $this->get('templating.globals')), 1 => $this->get('templating.engine.php'), 2 => $this->get('templating.engine.jsonphp')));
    }
    protected function getTemplating_Asset_DefaultPackage_HttpService()
    {
        return $this->services['templating.asset.default_package.http'] = new \Application\DeskPRO\Templating\Asset\UrlPackage(array(0 => 'CONFIG_HTTP'), NULL, NULL);
    }
    protected function getTemplating_Asset_DefaultPackage_SslService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('templating.asset.default_package.ssl', 'request');
        }
        return $this->services['templating.asset.default_package.ssl'] = $this->scopedServices['request']['templating.asset.default_package.ssl'] = new \Application\DeskPRO\Templating\Asset\PathPackage($this->get('request'), NULL, NULL);
    }
    protected function getTemplating_Asset_PackageFactoryService()
    {
        return $this->services['templating.asset.package_factory'] = new \Symfony\Bundle\FrameworkBundle\Templating\Asset\PackageFactory($this);
    }
    protected function getTemplating_Engine_JsonphpService()
    {
        return $this->services['templating.engine.jsonphp'] = new \Orb\Templating\Engine\PhpVarJsonEngine($this->get('templating.name_parser'), $this, $this->get('templating.loader'), $this->get('templating.globals'));
    }
    protected function getTemplating_GlobalsService()
    {
        return $this->services['templating.globals'] = new \Application\DeskPRO\Templating\GlobalVariables($this);
    }
    protected function getTemplating_Helper_ActionsService()
    {
        return $this->services['templating.helper.actions'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper($this->get('http_kernel'));
    }
    protected function getTemplating_Helper_AssetsService()
    {
        if (!isset($this->scopedServices['request'])) {
            throw new InactiveScopeException('templating.helper.assets', 'request');
        }
        return $this->services['templating.helper.assets'] = $this->scopedServices['request']['templating.helper.assets'] = new \Symfony\Component\Templating\Helper\CoreAssetsHelper($this->get('templating.asset.package_factory')->getPackage($this->get('request'), 'templating.asset.default_package.http', 'templating.asset.default_package.ssl'), array());
    }
    protected function getTemplating_Helper_CodeService()
    {
        return $this->services['templating.helper.code'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\CodeHelper(NULL, DP_ROOT.'/sys', 'UTF-8');
    }
    protected function getTemplating_Helper_FormService()
    {
        return $this->services['templating.helper.form'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper($this->get('templating.engine.php'), array(0 => 'FrameworkBundle:Form'));
    }
    protected function getTemplating_Helper_RequestService()
    {
        return $this->services['templating.helper.request'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\RequestHelper($this->get('request'));
    }
    protected function getTemplating_Helper_RouterService()
    {
        return $this->services['templating.helper.router'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper($this->get('router'));
    }
    protected function getTemplating_Helper_SessionService()
    {
        return $this->services['templating.helper.session'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\SessionHelper($this->get('request'));
    }
    protected function getTemplating_Helper_SlotsService()
    {
        return $this->services['templating.helper.slots'] = new \Symfony\Component\Templating\Helper\SlotsHelper();
    }
    protected function getTemplating_Helper_TranslatorService()
    {
        return $this->services['templating.helper.translator'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper($this->get('translator'));
    }
    protected function getTemplating_LoaderService()
    {
        return $this->services['templating.loader'] = new \Symfony\Bundle\FrameworkBundle\Templating\Loader\FilesystemLoader($this->get('templating.locator'));
    }
    protected function getTemplating_NameParserService()
    {
        return $this->services['templating.name_parser'] = new \Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser($this->get('kernel'));
    }
    protected function getTranslation_Loader_PhpService()
    {
        return $this->services['translation.loader.php'] = new \Symfony\Component\Translation\Loader\PhpFileLoader();
    }
    protected function getTranslation_Loader_XliffService()
    {
        return $this->services['translation.loader.xliff'] = new \Symfony\Component\Translation\Loader\XliffFileLoader();
    }
    protected function getTranslation_Loader_YmlService()
    {
        return $this->services['translation.loader.yml'] = new \Symfony\Component\Translation\Loader\YamlFileLoader();
    }
    protected function getTranslatorService()
    {
        return $this->services['translator'] = new \Symfony\Component\Translation\IdentityTranslator($this->get('translator.selector'));
    }
    protected function getTranslator_DefaultService()
    {
        return $this->services['translator.default'] = new \Symfony\Bundle\FrameworkBundle\Translation\Translator($this, $this->get('translator.selector'), array('translation.loader.php' => 'php', 'translation.loader.yml' => 'yml', 'translation.loader.xliff' => 'xliff'), array('cache_dir' => DP_ROOT.'/sys/cache/prod/translations', 'debug' => false), $this->get('session'));
    }
    protected function getTwigService()
    {
        $this->services['twig'] = $instance = new \Application\DeskPRO\Twig\Environment($this->get('twig.loader'), array('cache' => DP_ROOT.'/sys/cache/prod/../twig-compiled', 'charset' => 'UTF-8', 'debug' => false, 'auto_reload' => false));
        $instance->addExtension($this->get('twig.helpers.deskpro_templating'));
        $instance->addExtension($this->get('twig.helpers.deskpro_user_templating'));
        $instance->addExtension(new \Application\DeskPRO\Twig\Extension\TranslationExtension($this->get('translator')));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\AssetsExtension($this));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\ActionsExtension($this));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\CodeExtension($this));
        $instance->addExtension(new \Symfony\Bridge\Twig\Extension\RoutingExtension($this->get('router')));
        $instance->addExtension(new \Symfony\Bridge\Twig\Extension\YamlExtension());
        $instance->addExtension(new \Application\DeskPRO\Twig\Extension\FormExtension(array(0 => 'form_div_layout.html.twig', 1 => 'DeskPRO:Form:form_div_layout.html.twig')));
        return $instance;
    }
    protected function getTwig_ExceptionListenerService()
    {
        return $this->services['twig.exception_listener'] = new \Symfony\Component\HttpKernel\EventListener\ExceptionListener('Symfony\\Bundle\\TwigBundle\\Controller\\ExceptionController::showAction', $this->get('monolog.logger.request'));
    }
    protected function getTwig_Helpers_DeskproTemplatingService()
    {
        return $this->services['twig.helpers.deskpro_templating'] = new \Application\DeskPRO\Twig\Extension\TemplatingExtension($this);
    }
    protected function getTwig_Helpers_DeskproUserTemplatingService()
    {
        return $this->services['twig.helpers.deskpro_user_templating'] = new \Application\UserBundle\Twig\Extension\UserTemplatingExtension($this);
    }
    protected function getTwig_LoaderService()
    {
        $this->services['twig.loader'] = $instance = new \Application\DeskPRO\Twig\Loader\HybridLoader($this->get('templating.locator'), $this->get('templating.name_parser'));
        $instance->addPath(DP_ROOT.'/vendor/symfony/src/Symfony/Bridge/Twig/Resources/views/Form');
        return $instance;
    }
    protected function getValidatorService()
    {
        return $this->services['validator'] = new \Symfony\Component\Validator\Validator($this->get('validator.mapping.class_metadata_factory'), new \Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory($this, array('doctrine.orm.validator.unique' => 'doctrine.orm.validator.unique')), array(0 => $this->get('doctrine.orm.validator_initializer')));
    }
    protected function getValidator_Mapping_Loader_LoaderChainService()
    {
        return $this->services['validator.mapping.loader.loader_chain'] = new \Symfony\Component\Validator\Mapping\Loader\LoaderChain(array(0 => new \Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader()));
    }
    protected function getDatabaseConnectionService()
    {
        return $this->get('doctrine.dbal.default_connection');
    }
    protected function getDoctrine_Orm_EntityManagerService()
    {
        return $this->get('doctrine.orm.default_entity_manager');
    }
    protected function getMailerService()
    {
        return $this->get('swiftmailer.mailer');
    }
    protected function getSwiftmailer_TransportService()
    {
        return $this->get('swiftmailer.transport.dp_delegating');
    }
    protected function getControllerNameConverterService()
    {
        return $this->services['controller_name_converter'] = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser($this->get('kernel'));
    }
    protected function getTemplating_Engine_PhpService()
    {
        $this->services['templating.engine.php'] = $instance = new \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine($this->get('templating.name_parser'), $this, $this->get('templating.loader'), $this->get('templating.globals'));
        $instance->setCharset('UTF-8');
        $instance->setHelpers(array('slots' => 'templating.helper.slots', 'assets' => 'templating.helper.assets', 'request' => 'templating.helper.request', 'session' => 'templating.helper.session', 'router' => 'templating.helper.router', 'actions' => 'templating.helper.actions', 'code' => 'templating.helper.code', 'translator' => 'templating.helper.translator', 'form' => 'templating.helper.form'));
        return $instance;
    }
    protected function getTemplating_LocatorService()
    {
        return $this->services['templating.locator'] = new \Application\DeskPRO\Templating\Loader\TemplateLocator($this->get('file_locator'), DP_ROOT.'/sys/cache/prod/');
    }
    protected function getTranslator_SelectorService()
    {
        return $this->services['translator.selector'] = new \Symfony\Component\Translation\MessageSelector();
    }
    protected function getValidator_Mapping_ClassMetadataFactoryService()
    {
        return $this->services['validator.mapping.class_metadata_factory'] = new \Symfony\Component\Validator\Mapping\ClassMetadataFactory($this->get('validator.mapping.loader.loader_chain'), NULL);
    }
    public function getParameter($name)
    {
        $name = strtolower($name);
        if (!array_key_exists($name, $this->parameters)) {
            throw new \InvalidArgumentException(sprintf('The parameter "%s" must be defined.', $name));
        }
        return $this->parameters[$name];
    }
    public function hasParameter($name)
    {
        return array_key_exists(strtolower($name), $this->parameters);
    }
    public function setParameter($name, $value)
    {
        throw new \LogicException('Impossible to call set() on a frozen ParameterBag.');
    }
    public function getParameterBag()
    {
        if (null === $this->parameterBag) {
            $this->parameterBag = new FrozenParameterBag($this->parameters);
        }
        return $this->parameterBag;
    }
    protected function getDefaultParameters()
    {
        return array(
            'kernel.root_dir' => DP_ROOT.'/sys',
            'kernel.environment' => 'prod',
            'kernel.debug' => false,
            'kernel.name' => 'CliKernel',
            'kernel.cache_dir' => DP_ROOT.'/sys/cache/prod/',
            'kernel.logs_dir' => '',
            'kernel.bundles' => array(
                'FrameworkBundle' => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',
                'MonologBundle' => 'Symfony\\Bundle\\MonologBundle\\MonologBundle',
                'TwigBundle' => 'Symfony\\Bundle\\TwigBundle\\TwigBundle',
                'DoctrineBundle' => 'Symfony\\Bundle\\DoctrineBundle\\DoctrineBundle',
                'SwiftmailerBundle' => 'Symfony\\Bundle\\SwiftmailerBundle\\SwiftmailerBundle',
                'DeskPRO' => 'Application\\DeskPRO\\DeskPROBundle',
                'UserBundle' => 'Application\\UserBundle\\UserBundle',
                'AgentBundle' => 'Application\\AgentBundle\\AgentBundle',
                'DoctrineMigrationsBundle' => 'Symfony\\Bundle\\DoctrineMigrationsBundle\\DoctrineMigrationsBundle',
            ),
            'kernel.charset' => 'UTF-8',
            'kernel.container_class' => 'CliKernelContainer',
            'dp_root' => DP_ROOT.'',
            'kernel.include_core_classes' => false,
            'http_kernel.class' => 'Application\\DeskPRO\\HttpKernel\\HttpKernel',
            'controller_resolver.class' => 'Application\\DeskPRO\\HttpKernel\\Controller\\ControllerResolver',
            'session.class' => 'Application\\DeskPRO\\HttpFoundation\\Session',
            'swiftmailer.class' => 'Application\\DeskPRO\\Mail\\Mailer',
            'twig.loader.class' => 'Application\\DeskPRO\\Twig\\Loader\\HybridLoader',
            'twig.class' => 'Application\\DeskPRO\\Twig\\Environment',
            'file_locator.class' => 'Application\\DeskPRO\\HttpKernel\\Config\\FileLocator',
            'routing.file_locator.class' => 'Application\\DeskPRO\\HttpKernel\\Config\\FileLocator',
            'router.options.generator_class' => 'Application\\DeskPRO\\Routing\\Generator\\UrlGenerator',
            'router.options.generator_base_class' => 'Application\\DeskPRO\\Routing\\Generator\\UrlGenerator',
            'templating.globals.class' => 'Application\\DeskPRO\\Templating\\GlobalVariables',
            'templating.asset.url_package.class' => 'Application\\DeskPRO\\Templating\\Asset\\UrlPackage',
            'templating.asset.path_package.class' => 'Application\\DeskPRO\\Templating\\Asset\\PathPackage',
            'templating.cache_warmer.template_paths.class' => 'Application\\DeskPRO\\CacheWarmer\\TemplatePathsCacheWarmer',
            'router.class' => 'Application\\DeskPRO\\Routing\\Router',
            'router.options.generator_dumper_class' => 'Application\\DeskPRO\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'router.options.matcher_dumper_class' => 'Application\\DeskPRO\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'doctrine.data_collector.class' => 'Application\\DeskPRO\\Profiler\\DataCollector\\DoctrineDataCollector',
            'doctrine.orm.proxy_dir' => DP_ROOT.'/sys/cache/prod/../doctrine-proxies',
            'twig.options' => array(
                'cache' => DP_ROOT.'/sys/cache/prod/../twig-compiled',
                'charset' => 'UTF-8',
                'debug' => false,
                'auto_reload' => false,
            ),
            'doctrine_migrations.dir_name' => DP_ROOT.'/sys/Resources/DoctrineMigrations',
            'doctrine_migrations.table_name' => 'dev_migration_versions',
            'twig.extension.form.class' => 'Application\\DeskPRO\\Twig\\Extension\\FormExtension',
            'doctrine.orm.entity_manager.class' => 'Application\\DeskPRO\\ORM\\EntityManager',
            'templating.locator.class' => 'Application\\DeskPRO\\Templating\\Loader\\TemplateLocator',
            'templating.engine.twig.class' => 'Application\\DeskPRO\\Twig\\TwigEngine',
            'twig.cache_warmer.class' => 'Application\\DeskPRO\\Twig\\CacheWarmer\\TemplateCacheCacheWarmer',
            'twig.extension.trans.class' => 'Application\\DeskPRO\\Twig\\Extension\\TranslationExtension',
            'templating.engine.delegating.class' => 'Application\\DeskPRO\\Templating\\Engine',
            'router_listener.class' => 'Symfony\\Bundle\\FrameworkBundle\\EventListener\\RouterListener',
            'controller_name_converter.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerNameParser',
            'response_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\ResponseListener',
            'event_dispatcher.class' => 'Symfony\\Bundle\\FrameworkBundle\\ContainerAwareEventDispatcher',
            'filesystem.class' => 'Symfony\\Component\\Filesystem\\Filesystem',
            'cache_warmer.class' => 'Symfony\\Component\\HttpKernel\\CacheWarmer\\CacheWarmerAggregate',
            'translator.class' => 'Symfony\\Bundle\\FrameworkBundle\\Translation\\Translator',
            'translator.identity.class' => 'Symfony\\Component\\Translation\\IdentityTranslator',
            'translator.selector.class' => 'Symfony\\Component\\Translation\\MessageSelector',
            'translation.loader.php.class' => 'Symfony\\Component\\Translation\\Loader\\PhpFileLoader',
            'translation.loader.yml.class' => 'Symfony\\Component\\Translation\\Loader\\YamlFileLoader',
            'translation.loader.xliff.class' => 'Symfony\\Component\\Translation\\Loader\\XliffFileLoader',
            'kernel.secret' => 'mube224etsmhxky1gvwixc4b',
            'kernel.trust_proxy_headers' => false,
            'session.storage.native.class' => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\NativeSessionStorage',
            'session.storage.filesystem.class' => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\FilesystemSessionStorage',
            'session_listener.class' => 'Symfony\\Bundle\\FrameworkBundle\\EventListener\\SessionListener',
            'session.default_locale' => 'en',
            'session.storage.options' => array(
                'lifetime' => 3600,
            ),
            'form.extension.class' => 'Symfony\\Component\\Form\\Extension\\DependencyInjection\\DependencyInjectionExtension',
            'form.factory.class' => 'Symfony\\Component\\Form\\FormFactory',
            'form.type_guesser.validator.class' => 'Symfony\\Component\\Form\\Extension\\Validator\\ValidatorTypeGuesser',
            'validator.class' => 'Symfony\\Component\\Validator\\Validator',
            'validator.mapping.class_metadata_factory.class' => 'Symfony\\Component\\Validator\\Mapping\\ClassMetadataFactory',
            'validator.mapping.cache.apc.class' => 'Symfony\\Component\\Validator\\Mapping\\Cache\\ApcCache',
            'validator.mapping.cache.prefix' => '',
            'validator.mapping.loader.loader_chain.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\LoaderChain',
            'validator.mapping.loader.static_method_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\StaticMethodLoader',
            'validator.mapping.loader.annotation_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\AnnotationLoader',
            'validator.mapping.loader.xml_files_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\XmlFilesLoader',
            'validator.mapping.loader.yaml_files_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\YamlFilesLoader',
            'validator.validator_factory.class' => 'Symfony\\Bundle\\FrameworkBundle\\Validator\\ConstraintValidatorFactory',
            'validator.mapping.loader.xml_files_loader.mapping_files' => array(
                0 => DP_ROOT.'/vendor/symfony/src/Symfony/Component/Form/Resources/config/validation.xml',
            ),
            'validator.mapping.loader.yaml_files_loader.mapping_files' => array(
            ),
            'routing.loader.class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\DelegatingLoader',
            'routing.resolver.class' => 'Symfony\\Component\\Config\\Loader\\LoaderResolver',
            'routing.loader.xml.class' => 'Symfony\\Component\\Routing\\Loader\\XmlFileLoader',
            'routing.loader.yml.class' => 'Symfony\\Component\\Routing\\Loader\\YamlFileLoader',
            'routing.loader.php.class' => 'Symfony\\Component\\Routing\\Loader\\PhpFileLoader',
            'router.options.matcher_class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher',
            'router.options.matcher_base_class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\RedirectableUrlMatcher',
            'router.cache_warmer.class' => 'Symfony\\Bundle\\FrameworkBundle\\CacheWarmer\\RouterCacheWarmer',
            'router.options.matcher.cache_class' => 'CliKernelprodUrlMatcher',
            'router.options.generator.cache_class' => 'CliKernelprodUrlGenerator',
            'router.resource' => DP_ROOT.'/sys/config/agent/routing.php',
            'request_listener.http_port' => 80,
            'request_listener.https_port' => 443,
            'templating.name_parser.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\TemplateNameParser',
            'templating.loader.filesystem.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Loader\\FilesystemLoader',
            'templating.loader.cache.class' => 'Symfony\\Component\\Templating\\Loader\\CacheLoader',
            'templating.loader.chain.class' => 'Symfony\\Component\\Templating\\Loader\\ChainLoader',
            'templating.finder.class' => 'Symfony\\Bundle\\FrameworkBundle\\CacheWarmer\\TemplateFinder',
            'templating.engine.php.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\PhpEngine',
            'templating.helper.slots.class' => 'Symfony\\Component\\Templating\\Helper\\SlotsHelper',
            'templating.helper.assets.class' => 'Symfony\\Component\\Templating\\Helper\\CoreAssetsHelper',
            'templating.helper.actions.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\ActionsHelper',
            'templating.helper.router.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RouterHelper',
            'templating.helper.request.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RequestHelper',
            'templating.helper.session.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\SessionHelper',
            'templating.helper.code.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\CodeHelper',
            'templating.helper.translator.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\TranslatorHelper',
            'templating.helper.form.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\FormHelper',
            'templating.asset.package_factory.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Asset\\PackageFactory',
            'templating.helper.code.file_link_format' => NULL,
            'templating.helper.form.resources' => array(
                0 => 'FrameworkBundle:Form',
            ),
            'templating.loader.cache.path' => NULL,
            'templating.engines' => array(
                0 => 'twig',
                1 => 'php',
                2 => 'jsonphp',
            ),
            'annotations.reader.class' => 'Doctrine\\Common\\Annotations\\AnnotationReader',
            'annotations.cached_reader.class' => 'Doctrine\\Common\\Annotations\\CachedReader',
            'annotations.file_cache_reader.class' => 'Doctrine\\Common\\Annotations\\FileCacheReader',
            'monolog.logger.class' => 'Symfony\\Bridge\\Monolog\\Logger',
            'monolog.handler.stream.class' => 'Monolog\\Handler\\StreamHandler',
            'monolog.handler.fingers_crossed.class' => 'Monolog\\Handler\\FingersCrossedHandler',
            'monolog.handler.group.class' => 'Monolog\\Handler\\GroupHandler',
            'monolog.handler.buffer.class' => 'Monolog\\Handler\\BufferHandler',
            'monolog.handler.rotating_file.class' => 'Monolog\\Handler\\RotatingFileHandler',
            'monolog.handler.syslog.class' => 'Monolog\\Handler\\SyslogHandler',
            'monolog.handler.null.class' => 'Monolog\\Handler\\NullHandler',
            'monolog.handler.test.class' => 'Monolog\\Handler\\TestHandler',
            'monolog.handler.firephp.class' => 'Symfony\\Bridge\\Monolog\\Handler\\FirePHPHandler',
            'monolog.handler.debug.class' => 'Symfony\\Bridge\\Monolog\\Handler\\DebugHandler',
            'monolog.handler.swift_mailer.class' => 'Monolog\\Handler\\SwiftMailerHandler',
            'monolog.handler.native_mailer.class' => 'Monolog\\Handler\\NativeMailerHandler',
            'twig.extension.assets.class' => 'Symfony\\Bundle\\TwigBundle\\Extension\\AssetsExtension',
            'twig.extension.actions.class' => 'Symfony\\Bundle\\TwigBundle\\Extension\\ActionsExtension',
            'twig.extension.code.class' => 'Symfony\\Bundle\\TwigBundle\\Extension\\CodeExtension',
            'twig.extension.routing.class' => 'Symfony\\Bridge\\Twig\\Extension\\RoutingExtension',
            'twig.extension.yaml.class' => 'Symfony\\Bridge\\Twig\\Extension\\YamlExtension',
            'twig.exception_listener.class' => 'Symfony\\Component\\HttpKernel\\EventListener\\ExceptionListener',
            'twig.exception_listener.controller' => 'Symfony\\Bundle\\TwigBundle\\Controller\\ExceptionController::showAction',
            'twig.form.resources' => array(
                0 => 'form_div_layout.html.twig',
                1 => 'DeskPRO:Form:form_div_layout.html.twig',
            ),
            'doctrine.dbal.logger.debug.class' => 'Doctrine\\DBAL\\Logging\\DebugStack',
            'doctrine.dbal.logger.class' => 'Symfony\\Bridge\\Doctrine\\Logger\\DbalLogger',
            'doctrine.dbal.configuration.class' => 'Doctrine\\DBAL\\Configuration',
            'doctrine.dbal.connection.event_manager.class' => 'Doctrine\\Common\\EventManager',
            'doctrine.dbal.connection_factory.class' => 'Symfony\\Bundle\\DoctrineBundle\\ConnectionFactory',
            'doctrine.dbal.events.mysql_session_init.class' => 'Doctrine\\DBAL\\Event\\Listeners\\MysqlSessionInit',
            'doctrine.dbal.events.oracle_session_init.class' => 'Doctrine\\DBAL\\Event\\Listeners\\OracleSessionInit',
            'doctrine.class' => 'Symfony\\Bundle\\DoctrineBundle\\Registry',
            'doctrine.entity_managers' => array(
                'default' => 'doctrine.orm.default_entity_manager',
            ),
            'doctrine.default_entity_manager' => 'default',
            'doctrine.dbal.connection_factory.types' => array(
            ),
            'doctrine.connections' => array(
                'default' => 'doctrine.dbal.default_connection',
                'read' => 'doctrine.dbal.read_connection',
            ),
            'doctrine.default_connection' => 'default',
            'doctrine.orm.configuration.class' => 'Doctrine\\ORM\\Configuration',
            'doctrine.orm.cache.array.class' => 'Doctrine\\Common\\Cache\\ArrayCache',
            'doctrine.orm.cache.apc.class' => 'Doctrine\\Common\\Cache\\ApcCache',
            'doctrine.orm.cache.memcache.class' => 'Doctrine\\Common\\Cache\\MemcacheCache',
            'doctrine.orm.cache.memcache_host' => 'localhost',
            'doctrine.orm.cache.memcache_port' => 11211,
            'doctrine.orm.cache.memcache_instance.class' => 'Memcache',
            'doctrine.orm.cache.xcache.class' => 'Doctrine\\Common\\Cache\\XcacheCache',
            'doctrine.orm.metadata.driver_chain.class' => 'Doctrine\\ORM\\Mapping\\Driver\\DriverChain',
            'doctrine.orm.metadata.annotation.class' => 'Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver',
            'doctrine.orm.metadata.annotation_reader.class' => 'Symfony\\Bridge\\Doctrine\\Annotations\\IndexedReader',
            'doctrine.orm.metadata.xml.class' => 'Symfony\\Bridge\\Doctrine\\Mapping\\Driver\\XmlDriver',
            'doctrine.orm.metadata.yml.class' => 'Symfony\\Bridge\\Doctrine\\Mapping\\Driver\\YamlDriver',
            'doctrine.orm.metadata.php.class' => 'Doctrine\\ORM\\Mapping\\Driver\\PHPDriver',
            'doctrine.orm.metadata.staticphp.class' => 'Doctrine\\ORM\\Mapping\\Driver\\StaticPHPDriver',
            'doctrine.orm.proxy_cache_warmer.class' => 'Symfony\\Bridge\\Doctrine\\CacheWarmer\\ProxyCacheWarmer',
            'form.type_guesser.doctrine.class' => 'Symfony\\Bridge\\Doctrine\\Form\\DoctrineOrmTypeGuesser',
            'doctrine.orm.validator.unique.class' => 'Symfony\\Bridge\\Doctrine\\Validator\\Constraints\\UniqueEntityValidator',
            'doctrine.orm.validator_initializer.class' => 'Symfony\\Bridge\\Doctrine\\Validator\\EntityInitializer',
            'doctrine.orm.auto_generate_proxy_classes' => false,
            'doctrine.orm.proxy_namespace' => 'Proxies',
            'swiftmailer.transport.sendmail.class' => 'Swift_Transport_SendmailTransport',
            'swiftmailer.transport.mail.class' => 'Swift_Transport_MailTransport',
            'swiftmailer.transport.failover.class' => 'Swift_Transport_FailoverTransport',
            'swiftmailer.plugin.redirecting.class' => 'Swift_Plugins_RedirectingPlugin',
            'swiftmailer.plugin.impersonate.class' => 'Swift_Plugins_ImpersonatePlugin',
            'swiftmailer.plugin.messagelogger.class' => 'Symfony\\Bundle\\SwiftmailerBundle\\Logger\\MessageLogger',
            'swiftmailer.plugin.antiflood.class' => 'Swift_Plugins_AntiFloodPlugin',
            'swiftmailer.plugin.antiflood.threshold' => 99,
            'swiftmailer.plugin.antiflood.sleep' => 0,
            'swiftmailer.data_collector.class' => 'Symfony\\Bundle\\SwiftmailerBundle\\DataCollector\\MessageDataCollector',
            'swiftmailer.transport.smtp.encryption' => NULL,
            'swiftmailer.transport.smtp.port' => 25,
            'swiftmailer.transport.smtp.host' => 'localhost',
            'swiftmailer.transport.smtp.username' => NULL,
            'swiftmailer.transport.smtp.password' => NULL,
            'swiftmailer.transport.smtp.auth_mode' => NULL,
            'swiftmailer.spool.enabled' => false,
            'swiftmailer.sender_address' => NULL,
            'swiftmailer.single_address' => NULL,
            'doctrine_migrations.namespace' => 'Application\\Migrations',
            'doctrine_migrations.name' => 'Application Migrations',
        );
    }
}
