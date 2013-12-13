<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;


/**
 * InstallKernelprodUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class InstallKernelprodUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    static private $declaredRouteNames = array(
       'install_checks' => true,
       'install_check_urls' => true,
       'install_license' => true,
       'install_configedit' => true,
       'install' => true,
       'install_verify_files' => true,
       'install_verify_files_do' => true,
       'install_create_tables' => true,
       'install_create_tables_do' => true,
       'install_install_data' => true,
       'install_install_data_save' => true,
       'install_install_done' => true,
       'install_send_install_report_error' => true,
    );

    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function generate($name, $parameters = array(), $absolute = false)
    {
        if (!isset(self::$declaredRouteNames[$name])) {
            throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
        }

        $escapedName = str_replace('.', '__', $name);

        list($variables, $defaults, $requirements, $tokens) = $this->{'get'.$escapedName.'RouteInfo'}();

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }

    private function getinstall_checksRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/server-checks',  ),));
    }

    private function getinstall_check_urlsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::installRewriteCheckAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/url-rewriting-check',  ),));
    }

    private function getinstall_licenseRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::licenseAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/',  ),));
    }

    private function getinstall_configeditRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::configEditorAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/config-editor',  ),));
    }

    private function getinstallRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::licenseAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/',  ),));
    }

    private function getinstall_verify_filesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::verifyFilesAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/verify-files',  ),));
    }

    private function getinstall_verify_files_doRouteInfo()
    {
        return array(array (  0 => 'batch',), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::doVerifyFilesAction',  'batch' => 0,), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'batch',  ),  1 =>   array (    0 => 'text',    1 => '/install/verify-files/do',  ),));
    }

    private function getinstall_create_tablesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::createTablesAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/install-database',  ),));
    }

    private function getinstall_create_tables_doRouteInfo()
    {
        return array(array (  0 => 'batch',), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::doCreateTablesAction',  'batch' => 0,), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'batch',  ),  1 =>   array (    0 => 'text',    1 => '/install/install-database/do',  ),));
    }

    private function getinstall_install_dataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::installDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/install-data',  ),));
    }

    private function getinstall_install_data_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::installDataSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/install-data/save',  ),));
    }

    private function getinstall_install_doneRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::installDoneAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/install-done',  ),));
    }

    private function getinstall_send_install_report_errorRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::sendInstallReportErrorAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/install/install-report-error',  ),));
    }
}
