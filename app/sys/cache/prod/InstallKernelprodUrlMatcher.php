<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * InstallKernelprodUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class InstallKernelprodUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = urldecode($pathinfo);

        if (0 === strpos($pathinfo, '/install')) {
            // install_checks
            if ($pathinfo === '/install/server-checks') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::indexAction',  '_route' => 'install_checks',);
            }

            // install_check_urls
            if ($pathinfo === '/install/url-rewriting-check') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::installRewriteCheckAction',  '_route' => 'install_check_urls',);
            }

            // install_license
            if (rtrim($pathinfo, '/') === '/install') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', 'install_license');
                }
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::licenseAction',  '_route' => 'install_license',);
            }

            // install_configedit
            if ($pathinfo === '/install/config-editor') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::configEditorAction',  '_route' => 'install_configedit',);
            }

            // install
            if (rtrim($pathinfo, '/') === '/install') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', 'install');
                }
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::licenseAction',  '_route' => 'install',);
            }

            // install_verify_files
            if ($pathinfo === '/install/verify-files') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::verifyFilesAction',  '_route' => 'install_verify_files',);
            }

            // install_verify_files_do
            if (0 === strpos($pathinfo, '/install/verify-files/do') && preg_match('#^/install/verify\\-files/do(?:/(?P<batch>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::doVerifyFilesAction',  'batch' => 0,)), array('_route' => 'install_verify_files_do'));
            }

            // install_create_tables
            if ($pathinfo === '/install/install-database') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::createTablesAction',  '_route' => 'install_create_tables',);
            }

            // install_create_tables_do
            if (0 === strpos($pathinfo, '/install/install-database/do') && preg_match('#^/install/install\\-database/do(?:/(?P<batch>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::doCreateTablesAction',  'batch' => 0,)), array('_route' => 'install_create_tables_do'));
            }

            // install_install_data
            if ($pathinfo === '/install/install-data') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::installDataAction',  '_route' => 'install_install_data',);
            }

            // install_install_data_save
            if ($pathinfo === '/install/install-data/save') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::installDataSaveAction',  '_route' => 'install_install_data_save',);
            }

            // install_install_done
            if ($pathinfo === '/install/install-done') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::installDoneAction',  '_route' => 'install_install_done',);
            }

            // install_send_install_report_error
            if ($pathinfo === '/install/install-report-error') {
                return array (  '_controller' => 'Application\\InstallBundle\\Controller\\InstallController::sendInstallReportErrorAction',  '_route' => 'install_send_install_report_error',);
            }

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
