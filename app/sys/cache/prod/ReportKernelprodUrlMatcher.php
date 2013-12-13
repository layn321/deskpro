<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * ReportKernelprodUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class ReportKernelprodUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
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
		try {
			return $this->doMatch($pathinfo);
		} catch (ResourceNotFoundException $e) {
			// Try without trailing
			if (substr($pathinfo, -1) == '/') {
				$pathinfo = rtrim($pathinfo, '/');
				$match = $this->doMatch($pathinfo);
				return $this->redirect($pathinfo, $match['_route']);
			// Try with trailing slash
			} else {
				$pathinfo = $pathinfo . '/';
				$match = $this->doMatch($pathinfo);
				return $this->redirect($pathinfo, $match['_route']);
			}
		}
	}

	protected function doMatch($pathinfo)
    {
        $allow = array();
        $pathinfo = urldecode($pathinfo);

        // proxy
        if (0 === strpos($pathinfo, '/proxy') && preg_match('#^/proxy/(?P<key>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\DeskPRO\\Controller\\WidgetController::proxyAction',)), array('_route' => 'proxy'));
        }

        // serve_blob
        if (0 === strpos($pathinfo, '/file.php') && preg_match('#^/file\\.php/(?P<blob_auth_id>[^/]+?)/(?P<filename>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => '(see: serve_file.php)',)), array('_route' => 'serve_blob'));
        }

        // serve_blob_size
        if (0 === strpos($pathinfo, '/file.php/size') && preg_match('#^/file\\.php/size/(?P<s>[^/]+?)/(?P<blob_auth_id>[^/]+?)/(?P<filename>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => '(see: serve_file.php)',)), array('_route' => 'serve_blob_size'));
        }

        // serve_blob_sizefit
        if (0 === strpos($pathinfo, '/file.php/size') && preg_match('#^/file\\.php/size/(?P<s>[^/]+?)/size\\-fit/(?P<blob_auth_id>[^/]+?)/(?P<filename>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => '(see: serve_file.php)',)), array('_route' => 'serve_blob_sizefit'));
        }

        // serve_person_picture
        if (0 === strpos($pathinfo, '/file.php/avatar') && preg_match('#^/file\\.php/avatar/(?P<person_id>\\d+)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => '(see: serve_file.php)',  'size' => 0,)), array('_route' => 'serve_person_picture'));
        }

        // serve_person_picture_size
        if (0 === strpos($pathinfo, '/file.php/avatar') && preg_match('#^/file\\.php/avatar/(?P<person_id>\\d+)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => '(see: serve_file.php)',)), array('_route' => 'serve_person_picture_size'));
        }

        // serve_default_picture
        if (0 === strpos($pathinfo, '/file.php/avatar') && preg_match('#^/file\\.php/avatar/(?P<s>[^/]+?)/default\\.jpg$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => '(see: serve_file.php)',  'name' => 'default_picture',  's' => '0',)), array('_route' => 'serve_default_picture'));
        }

        // favicon
        if ($pathinfo === '/favicon.ico') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\BlobController::faviconAction',  '_route' => 'favicon',);
        }

        // serve_org_picture_default
        if ($pathinfo === '/file.php/o-avatar/default') {
            return array (  '_controller' => '(see: serve_file.php)',  '_route' => 'serve_org_picture_default',);
        }

        // serve_org_picture
        if (0 === strpos($pathinfo, '/file.php/o-avatar') && preg_match('#^/file\\.php/o\\-avatar/(?P<org_id>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => '(see: serve_file.php)',)), array('_route' => 'serve_org_picture'));
        }

        // sys_log_js_error
        if ($pathinfo === '/dp/log-js-error.json') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\DataController::logJsErrorAction',  '_route' => 'sys_log_js_error',);
        }

        // sys_report_error
        if ($pathinfo === '/dp/report-error.json') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\DataController::sendErrorReportAction',  '_route' => 'sys_report_error',);
        }

        // data_interface_data
        if (0 === strpos($pathinfo, '/data/interface-data') && preg_match('#^/data/interface\\-data(?:\\.(?P<_format>js))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\DeskPRO\\Controller\\DataController::interfaceDataAction',  '_format' => 'js',)), array('_route' => 'data_interface_data'));
        }

        // dp3_redirect_files_php
        if ($pathinfo === '/files.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::downloadCatAction',  '_route' => 'dp3_redirect_files_php',);
        }

        // dp3_redirect_attachment_files_php
        if ($pathinfo === '/attachment_files.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::downloadViewAction',  '_route' => 'dp3_redirect_attachment_files_php',);
        }

        // dp3_redirect_ideas_php
        if ($pathinfo === '/ideas.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::feedbackAction',  '_route' => 'dp3_redirect_ideas_php',);
        }

        // dp3_redirect_kb_article_php
        if ($pathinfo === '/kb_article.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::articleViewAction',  '_route' => 'dp3_redirect_kb_article_php',);
        }

        // dp3_redirect_kb_cat_php
        if ($pathinfo === '/kb_cat.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::articleCatAction',  '_route' => 'dp3_redirect_kb_cat_php',);
        }

        // dp3_redirect_kb_php
        if ($pathinfo === '/kb.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::articlesHomeAction',  '_route' => 'dp3_redirect_kb_php',);
        }

        // dp3_redirect_login_php
        if ($pathinfo === '/login.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::loginAction',  '_route' => 'dp3_redirect_login_php',);
        }

        // dp3_redirect_manual_php
        if ($pathinfo === '/manual.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::manualsAction',  '_route' => 'dp3_redirect_manual_php',);
        }

        // dp3_redirect_manual_rewritten
        if (0 === strpos($pathinfo, '/manual') && preg_match('#^/manual/(?P<manual_bit>[^/]+?)(?:/(?P<page_bit>[^/]+?))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::rewrittenManualsAction',  'page_bit' => '',)), array('_route' => 'dp3_redirect_manual_rewritten'));
        }

        // dp3_redirect_manual_download_php
        if ($pathinfo === '/manual_download.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::manualsAction',  '_route' => 'dp3_redirect_manual_download_php',);
        }

        // dp3_redirect_news_archive_php
        if ($pathinfo === '/news_archive.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::newsArchiveAction',  '_route' => 'dp3_redirect_news_archive_php',);
        }

        // dp3_redirect_news_full_php
        if ($pathinfo === '/news_full.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::newsViewAction',  '_route' => 'dp3_redirect_news_full_php',);
        }

        // dp3_redirect_news_php
        if ($pathinfo === '/news.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::newsViewAction',  '_route' => 'dp3_redirect_news_php',);
        }

        // dp3_redirect_newticket_php
        if ($pathinfo === '/newticket.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::newTicketAction',  '_route' => 'dp3_redirect_newticket_php',);
        }

        // dp3_redirect_profile_email_php
        if ($pathinfo === '/profile_email.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::profileAction',  '_route' => 'dp3_redirect_profile_email_php',);
        }

        // dp3_redirect_profile_password_php
        if ($pathinfo === '/profile_password.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::profileAction',  '_route' => 'dp3_redirect_profile_password_php',);
        }

        // dp3_redirect_profile_php
        if ($pathinfo === '/profile.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::profileAction',  '_route' => 'dp3_redirect_profile_php',);
        }

        // dp3_redirect_register_php
        if ($pathinfo === '/register.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::registerAction',  '_route' => 'dp3_redirect_register_php',);
        }

        // dp3_redirect_reset_php
        if ($pathinfo === '/reset.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::loginAction',  '_route' => 'dp3_redirect_reset_php',);
        }

        // dp3_redirect_ticketlist_php
        if ($pathinfo === '/ticketlist.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::ticketListAction',  '_route' => 'dp3_redirect_ticketlist_php',);
        }

        // dp3_redirect_ticketlist_company_php
        if ($pathinfo === '/ticketlist_company.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::ticketListAction',  '_route' => 'dp3_redirect_ticketlist_company_php',);
        }

        // dp3_redirect_ticketlist_participate_php
        if ($pathinfo === '/ticketlist_participate.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::ticketListAction',  '_route' => 'dp3_redirect_ticketlist_participate_php',);
        }

        // dp3_redirect_troubleshooter_php
        if ($pathinfo === '/troubleshooter.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::troublesAction',  '_route' => 'dp3_redirect_troubleshooter_php',);
        }

        // dp3_redirect_view_php
        if ($pathinfo === '/view.php') {
            return array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::ticketViewAction',  '_route' => 'dp3_redirect_view_php',);
        }

        // user
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'user');
            }
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\PortalController::portalAction',  '_locale' => 'en',  '_route' => 'user',);
        }

        // user_admin_rendertpl
        if (0 === strpos($pathinfo, '/admin-render-template') && preg_match('#^/admin\\-render\\-template/(?P<type>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::adminRenderTemplateAction',)), array('_route' => 'user_admin_rendertpl'));
        }

        // user_comment_form_login_partial
        if ($pathinfo === '/_misc/comment-form-login-partial') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::commentFormLoginPartialAction',  '_route' => 'user_comment_form_login_partial',);
        }

        // user_test
        if ($pathinfo === '/test') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\TestController::indexAction',  '_route' => 'user_test',);
        }

        // user_saverating
        if (0 === strpos($pathinfo, '/portal/save-rating') && preg_match('#^/portal/save\\-rating/(?P<object_type>[^/]+?)/(?P<object_id>[^/]+?)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_saverating;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\PortalController::saveRatingAction',)), array('_route' => 'user_saverating'));
        }
        not_user_saverating:

        // user_newcomment_finishlogin
        if (0 === strpos($pathinfo, '/portal/save-comment/login-finished') && preg_match('#^/portal/save\\-comment/login\\-finished/(?P<comment_type>[^/]+?)/(?P<comment_id>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\PortalController::newCommentFinishLoginAction',)), array('_route' => 'user_newcomment_finishlogin'));
        }

        // user_accept_upload
        if ($pathinfo === '/accept-temp-upload') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::acceptTempUploadAction',  '_route' => 'user_accept_upload',);
        }

        // user_validate_email
        if (0 === strpos($pathinfo, '/validate-email') && preg_match('#^/validate\\-email/(?P<id>[^/]+?)/(?P<auth>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::validateEmailAction',)), array('_route' => 'user_validate_email'));
        }

        // user_validate_ticket
        if (0 === strpos($pathinfo, '/validate-ticket-email') && preg_match('#^/validate\\-ticket\\-email/(?P<access_code>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::validateTicketEmailAction',)), array('_route' => 'user_validate_ticket'));
        }

        // user_jstell_login
        if (0 === strpos($pathinfo, '/login/jstell') && preg_match('#^/login/jstell/(?P<jstell>[^/]+?)/(?P<security_token>[^/]+?)/(?P<usersource_id>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::jstellLoginAction',)), array('_route' => 'user_jstell_login'));
        }

        // user_login
        if ($pathinfo === '/login') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::indexAction',  '_route' => 'user_login',);
        }

        // user_login_inline
        if ($pathinfo === '/login/inline-login') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::inlineLoginAction',  '_route' => 'user_login_inline',);
        }

        // user_login_usersource_sso
        if (0 === strpos($pathinfo, '/login/usersource-sso') && preg_match('#^/login/usersource\\-sso/(?P<usersource_id>\\d+)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::usersourceSsoAction',)), array('_route' => 'user_login_usersource_sso'));
        }

        // user_logout
        if (0 === strpos($pathinfo, '/logout') && preg_match('#^/logout/(?P<auth>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::logoutAction',)), array('_route' => 'user_logout'));
        }

        // user_login_authenticate_local
        if ($pathinfo === '/login/authenticate-password') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::authenticateLocalAction',  'usersource_id' => 0,  '_route' => 'user_login_authenticate_local',);
        }

        // user_login_authenticate
        if (0 === strpos($pathinfo, '/login/authenticate') && preg_match('#^/login/authenticate(?:/(?P<usersource_id>\\d+))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::authenticateAction',  'usersource_id' => 0,)), array('_route' => 'user_login_authenticate'));
        }

        // user_login_callback
        if (0 === strpos($pathinfo, '/login/authenticate-callback') && preg_match('#^/login/authenticate\\-callback/(?P<usersource_id>\\d+)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::authenticateCallbackAction',)), array('_route' => 'user_login_callback'));
        }

        // user_login_resetpass
        if ($pathinfo === '/login/reset-password') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::resetPasswordAction',  '_route' => 'user_login_resetpass',);
        }

        // user_login_resetpass_send
        if (0 === strpos($pathinfo, '/login/reset-password/send') && preg_match('#^/login/reset\\-password/send(?:\\.(?P<_format>[^/]+?))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::sendResetPasswordAction',  '_format' => 'html',)), array('_route' => 'user_login_resetpass_send'));
        }

        // user_login_resetpass_newpass
        if (0 === strpos($pathinfo, '/login/reset-password') && preg_match('#^/login/reset\\-password/(?P<code>[A-Za-z0-9\\-]{17,})$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::resetPasswordNewPassAction',)), array('_route' => 'user_login_resetpass_newpass'));
        }

        // user_login_agentlogin
        if (0 === strpos($pathinfo, '/login/agent-login') && preg_match('#^/login/agent\\-login/(?P<code>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::authAgentLoginAction',)), array('_route' => 'user_login_agentlogin'));
        }

        // user_register
        if ($pathinfo === '/register') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\RegisterController::registerAction',  '_route' => 'user_register',);
        }

        // user_profile
        if ($pathinfo === '/profile') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::indexAction',  '_route' => 'user_profile',);
        }

        // user_profile_setlang
        if ($pathinfo === '/profile/quick-set-language') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::quickSetLanguageAction',  '_route' => 'user_profile_setlang',);
        }

        // user_profile_associate_twitter
        if ($pathinfo === '/profile/associate-twitter') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::associateTwitterAction',  '_route' => 'user_profile_associate_twitter',);
        }

        // user_profile_twitter_remove
        if (0 === strpos($pathinfo, '/profile/twitter') && preg_match('#^/profile/twitter/(?P<account_id>\\d+)/remove$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::removeTwitterAction',)), array('_route' => 'user_profile_twitter_remove'));
        }

        // user_profile_changepassword
        if ($pathinfo === '/profile/change-password') {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_profile_changepassword;
            }
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::changePasswordAction',  '_route' => 'user_profile_changepassword',);
        }
        not_user_profile_changepassword:

        // user_profile_emails_new
        if ($pathinfo === '/profile/emails/new') {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_profile_emails_new;
            }
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::newEmailAction',  '_route' => 'user_profile_emails_new',);
        }
        not_user_profile_emails_new:

        // user_profile_emails_remove
        if (0 === strpos($pathinfo, '/profile/emails') && preg_match('#^/profile/emails/(?P<email_id>[^/]+?)/remove$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::removeEmailAction',)), array('_route' => 'user_profile_emails_remove'));
        }

        // user_profile_emails_validate_remove
        if (0 === strpos($pathinfo, '/profile/emails') && preg_match('#^/profile/emails/(?P<email_id>[^/]+?)/remove\\-validating$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::removeEmailValidatingAction',)), array('_route' => 'user_profile_emails_validate_remove'));
        }

        // user_profile_emails_validate_sendlink
        if (0 === strpos($pathinfo, '/profile/emails') && preg_match('#^/profile/emails/(?P<email_id>[^/]+?)/validate/send\\-link$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::sendValidateEmailLinkAction',)), array('_route' => 'user_profile_emails_validate_sendlink'));
        }

        // user_profile_emails_setdefault
        if (0 === strpos($pathinfo, '/profile/emails') && preg_match('#^/profile/emails/(?P<email_id>[^/]+?)/set\\-default$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::setDefaultEmailAction',)), array('_route' => 'user_profile_emails_setdefault'));
        }

        // user_search
        if ($pathinfo === '/search') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\SearchController::searchAction',  '_route' => 'user_search',);
        }

        // user_search_labels
        if (0 === strpos($pathinfo, '/search/labels') && preg_match('#^/search/labels(?:/(?P<type>[^/]+?)(?:/(?P<label>.*))?)?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\SearchController::labelSearchAction',  'label' => '',  'type' => 'all',)), array('_route' => 'user_search_labels'));
        }

        // user_search_omnisearch
        if (0 === strpos($pathinfo, '/search/omnisearch') && preg_match('#^/search/omnisearch/(?P<query>.+)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\SearchController::omnisearchAction',)), array('_route' => 'user_search_omnisearch'));
        }

        // user_search_similarto
        if (0 === strpos($pathinfo, '/search/similar-to') && preg_match('#^/search/similar\\-to/(?P<content_type>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\SearchController::similarToAction',)), array('_route' => 'user_search_similarto'));
        }

        // user_tickets
        if ($pathinfo === '/tickets') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::listAction',  '_route' => 'user_tickets',);
        }

        // user_tickets_organization
        if ($pathinfo === '/tickets/organization') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::listOrganizationAction',  '_route' => 'user_tickets_organization',);
        }

        // user_tickets_new
        if (0 === strpos($pathinfo, '/new-ticket') && preg_match('#^/new\\-ticket(?:/(?P<for_department_id>[^/]+?))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::newAction',  'format' => 'normal',  'for_department_id' => 0,)), array('_route' => 'user_tickets_new'));
        }

        // user_tickets_new_finishlogin
        if (0 === strpos($pathinfo, '/new-ticket/login-finish') && preg_match('#^/new\\-ticket/login\\-finish/(?P<ticket_id>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::newFinishLoginAction',)), array('_route' => 'user_tickets_new_finishlogin'));
        }

        // user_tickets_new_simple
        if (0 === strpos($pathinfo, '/tickets/new-simple') && preg_match('#^/tickets/new\\-simple(?:/(?P<for_department_id>[^/]+?))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::newAction',  'format' => 'iframe',  'for_department_id' => 0,)), array('_route' => 'user_tickets_new_simple'));
        }

        // user_tickets_new_savestatus
        if ($pathinfo === '/tickets/new/save-status') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::saveStatusAction',  '_route' => 'user_tickets_new_savestatus',);
        }

        // user_tickets_new_contentsolved_save
        if ($pathinfo === '/tickets/new/content-solved-save.json') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::contentSolvedSaveAction',  '_route' => 'user_tickets_new_contentsolved_save',);
        }

        // user_tickets_new_contentsolved
        if ($pathinfo === '/tickets/new/content-solved-redirect') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::contentSolvedRedirectAction',  '_route' => 'user_tickets_new_contentsolved',);
        }

        // user_tickets_new_thanks
        if (0 === strpos($pathinfo, '/tickets/new/thanks') && preg_match('#^/tickets/new/thanks/(?P<ticket_ref>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::thanksAction',)), array('_route' => 'user_tickets_new_thanks'));
        }

        // user_tickets_new_thanks_simple
        if (0 === strpos($pathinfo, '/tickets/new/thanks-simple') && preg_match('#^/tickets/new/thanks\\-simple/(?P<ticket_ref>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::simpleThanksAction',)), array('_route' => 'user_tickets_new_thanks_simple'));
        }

        // user_tickets_view
        if (0 === strpos($pathinfo, '/ticket') && preg_match('#^/ticket/(?P<ticket_ref>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketViewController::loadAction',)), array('_route' => 'user_tickets_view'));
        }

        // user_tickets_addreply
        if (0 === strpos($pathinfo, '/ticket-edit') && preg_match('#^/ticket\\-edit/(?P<ticket_ref>[^/]+?)/add\\-reply$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_tickets_addreply;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::addReplyAction',)), array('_route' => 'user_tickets_addreply'));
        }
        not_user_tickets_addreply:

        // user_tickets_participants
        if (0 === strpos($pathinfo, '/ticket-edit') && preg_match('#^/ticket\\-edit/(?P<ticket_ref>[^/]+?)/people$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::manageParticipantsAction',)), array('_route' => 'user_tickets_participants'));
        }

        // user_tickets_participants_add
        if (0 === strpos($pathinfo, '/ticket-edit') && preg_match('#^/ticket\\-edit/(?P<ticket_ref>[^/]+?)/people/add$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_tickets_participants_add;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::addParticipantAction',)), array('_route' => 'user_tickets_participants_add'));
        }
        not_user_tickets_participants_add:

        // user_tickets_participants_remove
        if (0 === strpos($pathinfo, '/ticket-edit') && preg_match('#^/ticket\\-edit/(?P<ticket_ref>[^/]+?)/people/remove/(?P<person_id>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::removeParticipantAction',)), array('_route' => 'user_tickets_participants_remove'));
        }

        // user_tickets_resolve
        if (0 === strpos($pathinfo, '/ticket-edit') && preg_match('#^/ticket\\-edit/(?P<ticket_ref>[^/]+?)/resolve$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::resolveAction',)), array('_route' => 'user_tickets_resolve'));
        }

        // user_tickets_unresolve
        if (0 === strpos($pathinfo, '/ticket-edit') && preg_match('#^/ticket\\-edit/(?P<ticket_ref>[^/]+?)/unresolve$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::unresolveAction',)), array('_route' => 'user_tickets_unresolve'));
        }

        // user_tickets_feedback
        if (0 === strpos($pathinfo, '/ticket-rate') && preg_match('#^/ticket\\-rate/(?P<ticket_ref>[^/]+?)/(?P<auth>[^/]+?)/(?P<message_id>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::feedbackAction',)), array('_route' => 'user_tickets_feedback'));
        }

        // user_tickets_feedback_save
        if (0 === strpos($pathinfo, '/ticket-rate') && preg_match('#^/ticket\\-rate/(?P<ticket_ref>[^/]+?)/(?P<auth>[^/]+?)/(?P<message_id>[^/]+?)/save$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_tickets_feedback_save;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::feedbackSaveAction',)), array('_route' => 'user_tickets_feedback_save'));
        }
        not_user_tickets_feedback_save:

        // user_tickets_feedback_closeticket
        if (0 === strpos($pathinfo, '/ticket-edit') && preg_match('#^/ticket\\-edit/(?P<ticket_ref>[^/]+?)/feedback/(?P<message_id>[^/]+?)/close\\-ticket$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::feedbackCloseTicketAction',)), array('_route' => 'user_tickets_feedback_closeticket'));
        }

        // user_articles_home
        if ($pathinfo === '/kb') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::browseAction',  'slug' => '',  '_route' => 'user_articles_home',);
        }

        // user_articles
        if (0 === strpos($pathinfo, '/kb') && preg_match('#^/kb(?:/(?P<slug>(\\d+(\\-.*?)?)?))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::browseAction',  'slug' => '',)), array('_route' => 'user_articles'));
        }

        // user_articles_article
        if (0 === strpos($pathinfo, '/kb/articles') && preg_match('#^/kb/articles/(?P<slug>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::articleAction',)), array('_route' => 'user_articles_article'));
        }

        // user_articles_article_togglesub
        if (0 === strpos($pathinfo, '/kb/article-subscription') && preg_match('#^/kb/article\\-subscription/(?P<article_id>[^/]+?)/(?P<auth>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::articleSubscriptionAction',)), array('_route' => 'user_articles_article_togglesub'));
        }

        // user_articles_cat_togglesub
        if (0 === strpos($pathinfo, '/kb/category-subscription') && preg_match('#^/kb/category\\-subscription/(?P<category_id>[^/]+?)/(?P<auth>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::categorySubscriptionAction',)), array('_route' => 'user_articles_cat_togglesub'));
        }

        // user_articles_unsub_all
        if (0 === strpos($pathinfo, '/kb/unsubscribe-all') && preg_match('#^/kb/unsubscribe\\-all/(?P<person_id>[^/]+?)/(?P<auth>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::unsubscribeAllAction',)), array('_route' => 'user_articles_unsub_all'));
        }

        // user_articles_article_agent_iframe
        if (0 === strpos($pathinfo, '/kb/articles/agent-iframe') && preg_match('#^/kb/articles/agent\\-iframe/(?P<article_id>\\d+)/(?P<agent_session_id>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::articleAgentIframeAction',)), array('_route' => 'user_articles_article_agent_iframe'));
        }

        // user_articles_newcomment
        if (0 === strpos($pathinfo, '/kb/new-comment') && preg_match('#^/kb/new\\-comment/(?P<article_id>[^/]+?)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_articles_newcomment;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::newCommentAction',)), array('_route' => 'user_articles_newcomment'));
        }
        not_user_articles_newcomment:

        // user_downloads_home
        if ($pathinfo === '/downloads') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::browseAction',  'slug' => '',  '_route' => 'user_downloads_home',);
        }

        // user_downloads
        if (0 === strpos($pathinfo, '/downloads') && preg_match('#^/downloads(?:/(?P<slug>(\\d+(\\-.*?)?)?))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::browseAction',  'slug' => '',)), array('_route' => 'user_downloads'));
        }

        // user_downloads_file
        if (0 === strpos($pathinfo, '/downloads/files') && preg_match('#^/downloads/files/(?P<slug>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::fileAction',)), array('_route' => 'user_downloads_file'));
        }

        // user_downloads_file_download
        if (0 === strpos($pathinfo, '/downloads/files') && preg_match('#^/downloads/files/(?P<slug>[^/]+?)/download$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::downloadFileAction',)), array('_route' => 'user_downloads_file_download'));
        }

        // user_downloads_newcomment
        if (0 === strpos($pathinfo, '/downloads/new-comment') && preg_match('#^/downloads/new\\-comment/(?P<download_id>[^/]+?)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_downloads_newcomment;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::newCommentAction',)), array('_route' => 'user_downloads_newcomment'));
        }
        not_user_downloads_newcomment:

        // user_news_home
        if (0 === strpos($pathinfo, '/news') && preg_match('#^/news(?:\\.(?P<_format>(html|rss)))?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewsController::browseAction',  'slug' => '',  'page' => 1,  '_format' => 'html',)), array('_route' => 'user_news_home'));
        }

        // user_news
        if (0 === strpos($pathinfo, '/news') && preg_match('#^/news(?:/(?P<slug>(\\d+(\\-.*?)?)?)(?:\\.(?P<_format>(html|rss)))?)?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewsController::browseAction',  'slug' => '',  'page' => 1,  '_format' => 'html',)), array('_route' => 'user_news'));
        }

        // user_news_view
        if (0 === strpos($pathinfo, '/news/view') && preg_match('#^/news/view/(?P<slug>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewsController::viewAction',)), array('_route' => 'user_news_view'));
        }

        // user_news_newcomment
        if (0 === strpos($pathinfo, '/news/new-comment') && preg_match('#^/news/new\\-comment/(?P<post_id>[^/]+?)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_news_newcomment;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\NewsController::newCommentAction',)), array('_route' => 'user_news_newcomment'));
        }
        not_user_news_newcomment:

        // user_feedback_home
        if ($pathinfo === '/feedback') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::filterAction',  'status' => 'open',  'slug' => 'all-categories',  'order_by' => 'popular',  '_route' => 'user_feedback_home',);
        }

        // user_feedback
        if (0 === strpos($pathinfo, '/feedback') && preg_match('#^/feedback(?:/(?P<order_by>(popular|newest|most\\-voted|i\\-voted))(?:/(?P<status>(open|any-status|gathering\\-feedback|active|closed)(\\.([0-9]+))?)(?:/(?P<slug>((\\d+(\\-.*?)?)?)|all\\-categories))?)?)?$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::filterAction',  'status' => 'open',  'slug' => 'all-categories',  'order_by' => 'popular',)), array('_route' => 'user_feedback'));
        }

        // user_feedback_new
        if ($pathinfo === '/feedback/new-feedback') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::filterAction',  'just_form' => 1,  'status' => 'any-status',  'slug' => 'all-categories',  'order_by' => 'popular',  '_route' => 'user_feedback_new',);
        }

        // user_feedback_view
        if (0 === strpos($pathinfo, '/feedback/view') && preg_match('#^/feedback/view/(?P<slug>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::viewAction',)), array('_route' => 'user_feedback_view'));
        }

        // user_feedback_newfeedback_finishlogin
        if (0 === strpos($pathinfo, '/feedback/new-feedback/login-finished') && preg_match('#^/feedback/new\\-feedback/login\\-finished/(?P<feedback_id>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::newFinishLoginAction',)), array('_route' => 'user_feedback_newfeedback_finishlogin'));
        }

        // user_feedback_newcomment
        if (0 === strpos($pathinfo, '/feedback/new-comment') && preg_match('#^/feedback/new\\-comment/(?P<feedback_id>[^/]+?)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_feedback_newcomment;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::newCommentAction',)), array('_route' => 'user_feedback_newcomment'));
        }
        not_user_feedback_newcomment:

        // user_feedback_vote
        if (0 === strpos($pathinfo, '/feedback/vote') && preg_match('#^/feedback/vote/(?P<feedback_id>[^/]+?)$#s', $pathinfo, $matches)) {
            if ($this->context->getMethod() != 'POST') {
                $allow[] = 'POST';
                goto not_user_feedback_vote;
            }
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::voteAction',)), array('_route' => 'user_feedback_vote'));
        }
        not_user_feedback_vote:

        // user_chat_initsession
        if ($pathinfo === '/chat/chat-session') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::chatSessionAction',  '_route' => 'user_chat_initsession',);
        }

        // user_chat_widgetisavail
        if ($pathinfo === '/dp.php/chat/is-available.js') {
            return array (  '_controller' => '(see: serve_dp.php)',  '_route' => 'user_chat_widgetisavail',);
        }

        // user_chat_poll
        if (0 === strpos($pathinfo, '/chat/poll') && preg_match('#^/chat/poll/(?P<session_code>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::pollAction',)), array('_route' => 'user_chat_poll'));
        }

        // user_chat_sendmessage
        if (0 === strpos($pathinfo, '/chat/send-message') && preg_match('#^/chat/send\\-message/(?P<session_code>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::sendMessageAction',)), array('_route' => 'user_chat_sendmessage'));
        }

        // user_chat_sendmessage_attach
        if (0 === strpos($pathinfo, '/chat/send-attach') && preg_match('#^/chat/send\\-attach/(?P<session_code>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::sendFileAction',)), array('_route' => 'user_chat_sendmessage_attach'));
        }

        // user_chat_sendusertyping
        if (0 === strpos($pathinfo, '/chat/user-typing') && preg_match('#^/chat/user\\-typing/(?P<session_code>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::userTypingAction',)), array('_route' => 'user_chat_sendusertyping'));
        }

        // user_chat_chatended
        if (0 === strpos($pathinfo, '/chat/chat-finished') && preg_match('#^/chat/chat\\-finished/(?P<session_code>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::chatEndedAction',)), array('_route' => 'user_chat_chatended'));
        }

        // user_chat_chatended_feedback
        if (0 === strpos($pathinfo, '/chat/chat-finished-feedback') && preg_match('#^/chat/chat\\-finished\\-feedback/(?P<session_code>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::chatEndedFeedbackAction',)), array('_route' => 'user_chat_chatended_feedback'));
        }

        // user_chatlogs
        if ($pathinfo === '/chat-logs') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatLogController::listAction',  '_route' => 'user_chatlogs',);
        }

        // user_chatlogs_view
        if (0 === strpos($pathinfo, '/chat-logs') && preg_match('#^/chat\\-logs/(?P<conversation_id>\\d+)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatLogController::viewAction',)), array('_route' => 'user_chatlogs_view'));
        }

        // user_widget_overlay
        if ($pathinfo === '/widget/overlay.html') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\WidgetController::overlayAction',  '_route' => 'user_widget_overlay',);
        }

        // user_widget_newticket
        if ($pathinfo === '/widget/new-ticket.json') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\WidgetController::newTicketAction',  '_route' => 'user_widget_newticket',);
        }

        // user_widget_newfeedback
        if ($pathinfo === '/widget/new-feedback.json') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\WidgetController::newFeedbackAction',  '_route' => 'user_widget_newfeedback',);
        }

        // user_widget_chat
        if ($pathinfo === '/widget/chat.html') {
            return array (  '_controller' => 'Application\\UserBundle\\Controller\\WidgetController::chatAction',  '_route' => 'user_widget_chat',);
        }

        // user_long_tweet_view
        if (0 === strpos($pathinfo, '/long-tweet') && preg_match('#^/long\\-tweet/(?P<long_id>\\d+)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\TwitterController::viewLongAction',)), array('_route' => 'user_long_tweet_view'));
        }

        // user_plugins_run
        if (0 === strpos($pathinfo, '/ext-plugins') && preg_match('#^/ext\\-plugins/(?P<plugin_id>[^/]+?)/run/(?P<action>[^/]+?)$#s', $pathinfo, $matches)) {
            return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\UserBundle\\Controller\\PluginsController::runAction',)), array('_route' => 'user_plugins_run'));
        }

        if (0 === strpos($pathinfo, '/reports')) {
            // report_login
            if ($pathinfo === '/reports/login') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\LoginController::indexAction',  '_route' => 'report_login',);
            }

            // report_logout
            if (0 === strpos($pathinfo, '/reports/logout') && preg_match('#^/reports/logout/(?P<auth>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\LoginController::logoutAction',)), array('_route' => 'report_logout'));
            }

            // report_login_authenticate_local
            if ($pathinfo === '/reports/login/authenticate-password') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\LoginController::authenticateLocalAction',  'usersource_id' => 0,  '_route' => 'report_login_authenticate_local',);
            }

            // report
            if (rtrim($pathinfo, '/') === '/reports') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', 'report');
                }
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\OverviewController::indexAction',  '_route' => 'report',);
            }

            // report_overview_update_stat
            if (0 === strpos($pathinfo, '/reports/overview/update-stat') && preg_match('#^/reports/overview/update\\-stat/(?P<type>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\OverviewController::updateStatAction',)), array('_route' => 'report_overview_update_stat'));
            }

            // report_agent_hours_index
            if ($pathinfo === '/reports/agent-hours') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentHoursController::indexAction',  '_route' => 'report_agent_hours_index',);
            }

            // report_agent_hours_list_date
            if (0 === strpos($pathinfo, '/reports/agent-hours') && preg_match('#^/reports/agent\\-hours/(?P<date>[^/]+?)(?:/(?P<date2>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentHoursController::listAction',  'date2' => '',)), array('_route' => 'report_agent_hours_list_date'));
            }

            // report_agent_activity_index
            if ($pathinfo === '/reports/agent-activity') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentActivityController::indexAction',  '_route' => 'report_agent_activity_index',);
            }

            // report_agent_activity_list
            if (0 === strpos($pathinfo, '/reports/agent-activity/list') && preg_match('#^/reports/agent\\-activity/list/(?P<agent_or_team_id>[^/]+?)/(?P<date>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentActivityController::listAction',)), array('_route' => 'report_agent_activity_list'));
            }

            // report_agent_feedback_summary
            if (0 === strpos($pathinfo, '/reports/agent-feedback/summary') && preg_match('#^/reports/agent\\-feedback/summary(?:/(?P<date>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentFeedbackController::summaryAction',  'date' => '',)), array('_route' => 'report_agent_feedback_summary'));
            }

            // report_agent_feedback_feed
            if (0 === strpos($pathinfo, '/reports/agent-feedback') && preg_match('#^/reports/agent\\-feedback(?:/(?P<page>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentFeedbackController::feedAction',  'page' => '0',)), array('_route' => 'report_agent_feedback_feed'));
            }

            // report_publish
            if ($pathinfo === '/reports/publish') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::indexAction',  '_route' => 'report_publish',);
            }

            // report_builder
            if ($pathinfo === '/reports/report-builder') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::indexAction',  '_route' => 'report_builder',);
            }

            // report_builder_query
            if ($pathinfo === '/reports/report-builder/query') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::queryAction',  '_route' => 'report_builder_query',);
            }

            // report_builder_parse
            if ($pathinfo === '/reports/report-builder/parse') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::parseAction',  '_route' => 'report_builder_parse',);
            }

            // report_builder_new
            if ($pathinfo === '/reports/report-builder/new') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::editAction',  'report_builder_id' => 0,  '_route' => 'report_builder_new',);
            }

            // report_builder_report
            if (0 === strpos($pathinfo, '/reports/report-builder') && preg_match('#^/reports/report\\-builder/(?P<report_builder_id>\\d+)/?$#s', $pathinfo, $matches)) {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', 'report_builder_report');
                }
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::reportAction',)), array('_route' => 'report_builder_report'));
            }

            // report_builder_edit
            if (0 === strpos($pathinfo, '/reports/report-builder') && preg_match('#^/reports/report\\-builder/(?P<report_builder_id>\\d+)/edit$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::editAction',)), array('_route' => 'report_builder_edit'));
            }

            // report_builder_delete
            if (0 === strpos($pathinfo, '/reports/report-builder') && preg_match('#^/reports/report\\-builder/(?P<report_builder_id>\\d+)/delete$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::deleteAction',)), array('_route' => 'report_builder_delete'));
            }

            // report_builder_favorite
            if (0 === strpos($pathinfo, '/reports/report-builder') && preg_match('#^/reports/report\\-builder/(?P<report_builder_id>\\d+)/favorite$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::favoriteAction',)), array('_route' => 'report_builder_favorite'));
            }

            // report_billing
            if ($pathinfo === '/reports/billing') {
                return array (  '_controller' => 'Application\\ReportBundle\\Controller\\BillingController::indexAction',  '_route' => 'report_billing',);
            }

            // report_billing_report
            if (0 === strpos($pathinfo, '/reports/billing') && preg_match('#^/reports/billing/(?P<report_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\ReportBundle\\Controller\\BillingController::reportAction',)), array('_route' => 'report_billing_report'));
            }

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
