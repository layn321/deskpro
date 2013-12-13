<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * CliKernelprodUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class CliKernelprodUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
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

        if (0 === strpos($pathinfo, '/agent')) {
            // agent
            if (rtrim($pathinfo, '/') === '/agent') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', 'agent');
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::indexAction',  '_route' => 'agent',);
            }

            // agent_savedom
            if ($pathinfo === '/agent/save-dom.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::saveDomAction',  '_route' => 'agent_savedom',);
            }

            // agent_combined_sectiondata
            if ($pathinfo === '/agent/get-combined-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::getCombinedSectionDataAction',  '_route' => 'agent_combined_sectiondata',);
            }

            // agent_load_recent_tabs
            if ($pathinfo === '/agent/ui/load-recent-tabs.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::loadRecentTabsAction',  '_route' => 'agent_load_recent_tabs',);
            }

            // agent_accept_upload
            if ($pathinfo === '/agent/misc/accept-upload') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::acceptTempUploadAction',  '_route' => 'agent_accept_upload',);
            }

            // agent_accept_redactor_image_upload
            if ($pathinfo === '/agent/misc/accept-redactor-image-upload') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::acceptRedactorImageUploadAction',  '_route' => 'agent_accept_redactor_image_upload',);
            }

            // agent_redactor_autosave
            if (0 === strpos($pathinfo, '/agent/misc/redactor-autosave') && preg_match('#^/agent/misc/redactor\\-autosave/(?P<content_type>[^/]+?)/(?P<content_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::redactorAutosaveAction',)), array('_route' => 'agent_redactor_autosave'));
            }

            // agent_submit_deskpro_feedback
            if ($pathinfo === '/agent/misc/submit-deskpro-feedback.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::submitDeskproFeedbackAction',  '_route' => 'agent_submit_deskpro_feedback',);
            }

            // agent_parse_vcard
            if ($pathinfo === '/agent/misc/parse-vcard') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::parseVCardAction',  '_route' => 'agent_parse_vcard',);
            }

            // agent_get_server_time
            if ($pathinfo === '/agent/misc/get-server-time') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::getServerTimeAction',  '_route' => 'agent_get_server_time',);
            }

            // agent_ajax_save_prefs
            if ($pathinfo === '/agent/misc/ajax-save-prefs') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::ajaxSavePrefsAction',  '_route' => 'agent_ajax_save_prefs',);
            }

            // agent_ajax_labels_autocomplete
            if (0 === strpos($pathinfo, '/agent/misc/ajax-labels') && preg_match('#^/agent/misc/ajax\\-labels/(?P<label_type>[a-z]+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::ajaxLabelsAutocompleteAction',)), array('_route' => 'agent_ajax_labels_autocomplete'));
            }

            // agent_interface_data_js
            if ($pathinfo === '/agent/misc/interface-data.js') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::getInterfaceDataAction',  '_route' => 'agent_interface_data_js',);
            }

            // agent_dismiss_help_message
            if (0 === strpos($pathinfo, '/agent/misc/dismiss-help-message') && preg_match('#^/agent/misc/dismiss\\-help\\-message/(?P<id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::dismissHelpMessageAction',)), array('_route' => 'agent_dismiss_help_message'));
            }

            // agent_set_agent_status
            if (0 === strpos($pathinfo, '/agent/misc/set-agent-status') && preg_match('#^/agent/misc/set\\-agent\\-status/(?P<status>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::setAgentStatusAction',)), array('_route' => 'agent_set_agent_status'));
            }

            // agent_proxy
            if ($pathinfo === '/agent/misc/proxy') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::proxyAction',  '_route' => 'agent_proxy',);
            }

            // agent_load_version_notice
            if (0 === strpos($pathinfo, '/agent/misc/version-notices') && preg_match('#^/agent/misc/version\\-notices/(?P<id>[^/]+?)/log\\.html$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::loadVersionNoticeAction',)), array('_route' => 'agent_load_version_notice'));
            }

            // agent_dismiss_version_notice
            if (0 === strpos($pathinfo, '/agent/misc/version-notices') && preg_match('#^/agent/misc/version\\-notices/(?P<id>[^/]+?)/dismiss\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::dismissVersionNoticeAction',)), array('_route' => 'agent_dismiss_version_notice'));
            }

            // agent_redirect_out
            if (0 === strpos($pathinfo, '/agent/redirect-out') && preg_match('#^/agent/redirect\\-out/(?P<url>.+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::redirectExternalAction',)), array('_route' => 'agent_redirect_out'));
            }

            // agent_redirect_out_info
            if (0 === strpos($pathinfo, '/agent/redirect-out-info') && preg_match('#^/agent/redirect\\-out\\-info/(?P<url>.+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::redirectExternalInfoAction',)), array('_route' => 'agent_redirect_out_info'));
            }

            // agent_password_confirm_code
            if ($pathinfo === '/agent/password-confirm-code.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::getPasswordConfirmCodeAction',  '_route' => 'agent_password_confirm_code',);
            }

            // agent_quicksearch
            if ($pathinfo === '/agent/quick-search.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::quickSearchAction',  '_route' => 'agent_quicksearch',);
            }

            // agent_search
            if ($pathinfo === '/agent/search/search') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SearchController::searchAction',  '_route' => 'agent_search',);
            }

            // agent_search_json
            if ($pathinfo === '/agent/search/search.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SearchController::searchResultsAction',  '_route' => 'agent_search_json',);
            }

            // agent_recyclebin
            if ($pathinfo === '/agent/recycle-bin') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\RecycleBinController::listAction',  '_route' => 'agent_recyclebin',);
            }

            // agent_recyclebin_more
            if (0 === strpos($pathinfo, '/agent/recycle-bin') && preg_match('#^/agent/recycle\\-bin/(?P<type>[^/]+?)/(?P<page>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\RecycleBinController::listMoreAction',)), array('_route' => 'agent_recyclebin_more'));
            }

            // agent_login_preload_sources
            if ($pathinfo === '/agent/login/preload-sources') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::preloadSourcesAction',  '_route' => 'agent_login_preload_sources',);
            }

            // agent_browser_requirements
            if ($pathinfo === '/agent/browser-requirements') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::browserRequirementsAction',  '_route' => 'agent_browser_requirements',);
            }

            // agent_browser_requirements_ie_compat
            if ($pathinfo === '/agent/browser-requirements/ie-compat-mode') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::ieCompatModeAction',  '_route' => 'agent_browser_requirements_ie_compat',);
            }

            // agent_login
            if ($pathinfo === '/agent/login') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::indexAction',  '_route' => 'agent_login',);
            }

            // agent_login_authenticate_local
            if ($pathinfo === '/agent/login/authenticate-password') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::authenticateLocalAction',  'usersource_id' => 0,  '_route' => 'agent_login_authenticate_local',);
            }

            // agent_login_adminlogin
            if (0 === strpos($pathinfo, '/agent/login/admin-login') && preg_match('#^/agent/login/admin\\-login/(?P<code>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::authAdminLoginAction',)), array('_route' => 'agent_login_adminlogin'));
            }

            // agent_send_lost
            if ($pathinfo === '/agent/login/send-lost.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::sendResetPasswordAction',  '_format' => 'json',  '_route' => 'agent_send_lost',);
            }

            // agent_settings
            if ($pathinfo === '/agent/settings') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::profileAction',  '_route' => 'agent_settings',);
            }

            // agent_settings_profile_save
            if ($pathinfo === '/agent/settings/profile/save.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::profileSaveAction',  '_route' => 'agent_settings_profile_save',);
            }

            // agent_settings_profile_savewelcome
            if ($pathinfo === '/agent/settings/profile/save-welcome.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::profileSaveWelcomeAction',  '_route' => 'agent_settings_profile_savewelcome',);
            }

            // agent_settings_signature
            if ($pathinfo === '/agent/settings/signature') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::signatureAction',  '_route' => 'agent_settings_signature',);
            }

            // agent_settings_signature_save
            if ($pathinfo === '/agent/settings/signature/save.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::signatureSaveAction',  '_route' => 'agent_settings_signature_save',);
            }

            // agent_settings_profile_updatetimezone
            if ($pathinfo === '/agent/settings/profile/update-timezone.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::updateTimezoneAction',  '_route' => 'agent_settings_profile_updatetimezone',);
            }

            // agent_settings_ticketnotif
            if ($pathinfo === '/agent/settings/ticket-notifications') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketNotificationsAction',  '_route' => 'agent_settings_ticketnotif',);
            }

            // agent_settings_ticketnotif_save
            if ($pathinfo === '/agent/settings/ticket-notifications/save.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketNotificationsSaveAction',  '_route' => 'agent_settings_ticketnotif_save',);
            }

            // agent_settings_othernotif
            if ($pathinfo === '/agent/settings/other-notifications') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::otherNotificationsAction',  '_route' => 'agent_settings_othernotif',);
            }

            // agent_settings_othernotif_save
            if ($pathinfo === '/agent/settings/other-notifications/save.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::otherNotificationsSaveAction',  '_route' => 'agent_settings_othernotif_save',);
            }

            // agent_settings_ticketmacros
            if ($pathinfo === '/agent/settings/ticket-macros') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacrosAction',  '_route' => 'agent_settings_ticketmacros',);
            }

            // agent_settings_ticketmacros_edit
            if (0 === strpos($pathinfo, '/agent/settings/ticket-macros') && preg_match('#^/agent/settings/ticket\\-macros/(?P<macro_id>\\d+)/edit$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacroEditAction',)), array('_route' => 'agent_settings_ticketmacros_edit'));
            }

            // agent_settings_ticketmacros_edit_save
            if (0 === strpos($pathinfo, '/agent/settings/ticket-macros') && preg_match('#^/agent/settings/ticket\\-macros/(?P<macro_id>\\d+)/save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacroEditSaveAction',)), array('_route' => 'agent_settings_ticketmacros_edit_save'));
            }

            // agent_settings_ticketmacros_new
            if ($pathinfo === '/agent/settings/ticket-macros/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacroEditAction',  'macro_id' => 0,  '_route' => 'agent_settings_ticketmacros_new',);
            }

            // agent_settings_ticketmacros_del
            if (0 === strpos($pathinfo, '/agent/settings/ticket-macros') && preg_match('#^/agent/settings/ticket\\-macros/(?P<macro_id>\\d+)/delete$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacroDeleteAction',)), array('_route' => 'agent_settings_ticketmacros_del'));
            }

            // agent_settings_ticketfilters
            if ($pathinfo === '/agent/settings/ticket-filters') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFiltersAction',  '_route' => 'agent_settings_ticketfilters',);
            }

            // agent_settings_ticketfilters_edit
            if (0 === strpos($pathinfo, '/agent/settings/ticket-filters') && preg_match('#^/agent/settings/ticket\\-filters/(?P<filter_id>\\d+)/edit$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFilterEditAction',)), array('_route' => 'agent_settings_ticketfilters_edit'));
            }

            // agent_settings_ticketfilters_edit_save
            if (0 === strpos($pathinfo, '/agent/settings/ticket-filters') && preg_match('#^/agent/settings/ticket\\-filters/(?P<filter_id>\\d+)/edit/save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFilterEditSaveAction',)), array('_route' => 'agent_settings_ticketfilters_edit_save'));
            }

            // agent_settings_ticketfilters_del
            if (0 === strpos($pathinfo, '/agent/settings/ticket-filters') && preg_match('#^/agent/settings/ticket\\-filters/(?P<filter_id>\\d+)/delete$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFilterDeleteAction',)), array('_route' => 'agent_settings_ticketfilters_del'));
            }

            // agent_settings_ticketfilters_new
            if ($pathinfo === '/agent/settings/ticket-filters/new-filter') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFilterEditAction',  'filter_id' => 0,  '_route' => 'agent_settings_ticketfilters_new',);
            }

            // agent_settings_ticketslas
            if ($pathinfo === '/agent/settings/ticket-slas') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketSlasAction',  '_route' => 'agent_settings_ticketslas',);
            }

            // agent_people_validate_email
            if (0 === strpos($pathinfo, '/agent/people/validate-email') && preg_match('#^/agent/people/validate\\-email/(?P<id>\\d+)/(?P<security_token>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::validateEmailAddressAction',)), array('_route' => 'agent_people_validate_email'));
            }

            // agent_people_view
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::viewAction',)), array('_route' => 'agent_people_view'));
            }

            // agent_people_view_basicjson
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>\\d+)/basic\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::getBasicInfoAction',)), array('_route' => 'agent_people_view_basicjson'));
            }

            // agent_people_viewsession
            if (0 === strpos($pathinfo, '/agent/people/session') && preg_match('#^/agent/people/session/(?P<session_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::viewSessionAction',)), array('_route' => 'agent_people_viewsession'));
            }

            // agent_people_validate_list
            if ($pathinfo === '/agent/people/validate/list') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::validateListAction',  '_route' => 'agent_people_validate_list',);
            }

            // agent_people_validate_approve
            if ($pathinfo === '/agent/people/validate/approve') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::validateApproveAction',  '_route' => 'agent_people_validate_approve',);
            }

            // agent_people_validate_delete
            if ($pathinfo === '/agent/people/validate/delete') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::validateDeleteAction',  '_route' => 'agent_people_validate_delete',);
            }

            // agent_people_new
            if ($pathinfo === '/agent/people/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::newPersonAction',  '_route' => 'agent_people_new',);
            }

            // agent_people_new_save
            if ($pathinfo === '/agent/people/new/save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::newPersonSaveAction',  '_route' => 'agent_people_new_save',);
            }

            // agent_people_ajaxsave
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>\\d+)/ajax\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveAction',)), array('_route' => 'agent_people_ajaxsave'));
            }

            // agent_people_savecontactdata
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>[^/]+?)/save\\-contact\\-data\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::saveContactDataAction',)), array('_route' => 'agent_people_savecontactdata'));
            }

            // agent_people_unban_email
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>[^/]+?)/unban\\-email/(?P<email_id>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::unbanEmailAction',)), array('_route' => 'agent_people_unban_email'));
            }

            // agent_people_merge_overlay
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>\\d+)/merge\\-overlay/(?P<other_person_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::mergeOverlayAction',)), array('_route' => 'agent_people_merge_overlay'));
            }

            // agent_people_merge
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>\\d+)/merge/(?P<other_person_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::mergeAction',)), array('_route' => 'agent_people_merge'));
            }

            // agent_people_delete
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>[^/]+?)/delete/(?P<security_token>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::deletePersonAction',)), array('_route' => 'agent_people_delete'));
            }

            // agent_people_login_as
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>[^/]+?)/login\\-as$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::loginAsAction',)), array('_route' => 'agent_people_login_as'));
            }

            // agent_people_changepicoverlay
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>\\d+)/change\\-picture\\-overlay$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::changePictureOverlayAction',)), array('_route' => 'agent_people_changepicoverlay'));
            }

            // agent_people_ajaxsave_note
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>\\d+)/ajax\\-save\\-note$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveNoteAction',)), array('_route' => 'agent_people_ajaxsave_note'));
            }

            // agent_people_ajaxsave_organization
            if (0 === strpos($pathinfo, '/agent/people') && preg_match('#^/agent/people/(?P<person_id>\\d+)/ajax\\-save\\-organization$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveOrganizationAction',)), array('_route' => 'agent_people_ajaxsave_organization'));
            }

            // agent_person_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/person') && preg_match('#^/agent/person/(?P<person_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveLabelsAction',)), array('_route' => 'agent_person_ajax_labels_save'));
            }

            // agent_person_ajaxsavecustomfields
            if (0 === strpos($pathinfo, '/agent/person') && preg_match('#^/agent/person/(?P<person_id>\\d+)/ajax\\-save\\-custom\\-fields$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveCustomFieldsAction',)), array('_route' => 'agent_person_ajaxsavecustomfields'));
            }

            // agent_peoplesearch_usergroup
            if (0 === strpos($pathinfo, '/agent/people-search/usergroup') && preg_match('#^/agent/people\\-search/usergroup/(?P<id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::showUsergroupAction',)), array('_route' => 'agent_peoplesearch_usergroup'));
            }

            // agent_peoplesearch_organization
            if (0 === strpos($pathinfo, '/agent/people-search/organization') && preg_match('#^/agent/people\\-search/organization/(?P<id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::showOrganizationMembersAction',)), array('_route' => 'agent_peoplesearch_organization'));
            }

            // agent_peoplesearch_customfilter
            if (0 === strpos($pathinfo, '/agent/people-search/search') && preg_match('#^/agent/people\\-search/search(?:/(?P<letter>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::searchAction',  'letter' => '*',)), array('_route' => 'agent_peoplesearch_customfilter'));
            }

            // agent_peoplesearch_getpage
            if ($pathinfo === '/agent/people-search/get-page') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::getPeoplePageAction',  '_route' => 'agent_peoplesearch_getpage',);
            }

            // agent_peoplesearch_performquick
            if ($pathinfo === '/agent/people-search/search-quick') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::performQuickSearchAction',  '_route' => 'agent_peoplesearch_performquick',);
            }

            // agent_peoplesearch_quickfind
            if ($pathinfo === '/agent/people-search/quick-find') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::quickFindAction',  '_route' => 'agent_peoplesearch_quickfind',);
            }

            // agent_peoplesearch_quickfind_search
            if ($pathinfo === '/agent/people-search/quick-find-search.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::quickFindSearchAction',  '_route' => 'agent_peoplesearch_quickfind_search',);
            }

            // agent_peoplesearch_getsectiondata
            if ($pathinfo === '/agent/people/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::getSectionDataAction',  '_route' => 'agent_peoplesearch_getsectiondata',);
            }

            // agent_peoplesearch_getsectiondata_reloadcounts
            if ($pathinfo === '/agent/people/get-section-data/reload-counts.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::reloadCountsAction',  '_route' => 'agent_peoplesearch_getsectiondata_reloadcounts',);
            }

            // agent_peoplesearch_reload_label_sectiondata
            if ($pathinfo === '/agent/people/get-section-data/labels.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::reloadLabelDataAction',  '_route' => 'agent_peoplesearch_reload_label_sectiondata',);
            }

            // agent_org_view
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::viewAction',)), array('_route' => 'agent_org_view'));
            }

            // agent_org_new
            if ($pathinfo === '/agent/organizations/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::newOrganizationAction',  '_route' => 'agent_org_new',);
            }

            // agent_org_new_save
            if ($pathinfo === '/agent/organizations/new/save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::newOrganizationSaveAction',  '_route' => 'agent_org_new_save',);
            }

            // agent_org_ajaxsave
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/ajax\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::ajaxSaveAction',)), array('_route' => 'agent_org_ajaxsave'));
            }

            // agent_org_domain_assign
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/assign\\-domain$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::assignDomainAction',)), array('_route' => 'agent_org_domain_assign'));
            }

            // agent_org_domain_unassign
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/unassign\\-domain$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::unassignDomainAction',)), array('_route' => 'agent_org_domain_unassign'));
            }

            // agent_org_domain_moveusers
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/domain/move\\-users$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::moveDomainUsersAction',)), array('_route' => 'agent_org_domain_moveusers'));
            }

            // agent_org_domain_moveusers_exist
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/domain/reassign\\-users$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::moveTakenDomainUsersAction',)), array('_route' => 'agent_org_domain_moveusers_exist'));
            }

            // agent_org_save_member_pos
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/save\\-member\\-pos/(?P<person_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::savePositionAction',)), array('_route' => 'agent_org_save_member_pos'));
            }

            // agent_org_save_member_manager
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/save\\-member\\-manager/(?P<person_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::saveManagerAction',)), array('_route' => 'agent_org_save_member_manager'));
            }

            // agent_org_savecontactdata
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>[^/]+?)/save\\-contact\\-data\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::saveContactDataAction',)), array('_route' => 'agent_org_savecontactdata'));
            }

            // agent_org_delete
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>[^/]+?)/delete/(?P<security_token>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::deleteOrganizationAction',)), array('_route' => 'agent_org_delete'));
            }

            // agent_org_ajaxsave_note
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/ajax\\-save\\-note$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::ajaxSaveNoteAction',)), array('_route' => 'agent_org_ajaxsave_note'));
            }

            // agent_org_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::ajaxSaveLabelsAction',)), array('_route' => 'agent_org_ajax_labels_save'));
            }

            // agent_org_ajaxsavecustomfields
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/ajax\\-save\\-custom\\-fields$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::ajaxSaveCustomFieldsAction',)), array('_route' => 'agent_org_ajaxsavecustomfields'));
            }

            // agent_org_changepicoverlay
            if (0 === strpos($pathinfo, '/agent/organizations') && preg_match('#^/agent/organizations/(?P<organization_id>\\d+)/change\\-picture\\-overlay$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::changePictureOverlayAction',)), array('_route' => 'agent_org_changepicoverlay'));
            }

            // agent_orgsearch_getpage
            if ($pathinfo === '/agent/organization-search/get-page') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationSearchController::getOrgPageAction',  '_route' => 'agent_orgsearch_getpage',);
            }

            // agent_orgsearch_customfilter
            if ($pathinfo === '/agent/organization-search/search') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationSearchController::searchAction',  '_route' => 'agent_orgsearch_customfilter',);
            }

            // agent_orgsearch_quicknamesearch
            if ($pathinfo === '/agent/organization-search/quick-name-search.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationSearchController::performQuickNameSearchAction',  '_route' => 'agent_orgsearch_quicknamesearch',);
            }

            // agent_orgsearch_namelookup
            if ($pathinfo === '/agent/organization-search/name-lookup.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationSearchController::checkNameAction',  '_route' => 'agent_orgsearch_namelookup',);
            }

            // agent_ticketsearch_getsectiondata
            if ($pathinfo === '/agent/ticket-search/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getSectionDataAction',  '_route' => 'agent_ticketsearch_getsectiondata',);
            }

            // agent_ticketsearch_getsection_reloadarchive
            if ($pathinfo === '/agent/ticket-search/get-section-data/reload-archive-section') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::reloadArchiveSectionAction',  '_route' => 'agent_ticketsearch_getsection_reloadarchive',);
            }

            // agent_ticketsearch_refreshsectiondata
            if (0 === strpos($pathinfo, '/agent/ticket-search/refresh-section-data') && preg_match('#^/agent/ticket\\-search/refresh\\-section\\-data/(?P<section>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::refreshSectionDataAction',)), array('_route' => 'agent_ticketsearch_refreshsectiondata'));
            }

            // agent_ticketsearch_getlabelssection
            if ($pathinfo === '/agent/ticket-search/get-section/labels') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getLabelsSectionAction',  '_route' => 'agent_ticketsearch_getlabelssection',);
            }

            // agent_ticketsearch_getfiltercounts
            if ($pathinfo === '/agent/ticket-search/get-filter-counts.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getFilterCountsAction',  '_route' => 'agent_ticketsearch_getfiltercounts',);
            }

            // agent_ticketsearch_getslacounts
            if ($pathinfo === '/agent/ticket-search/get-sla-counts.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getSlaCountsAction',  '_route' => 'agent_ticketsearch_getslacounts',);
            }

            // agent_ticketsearch_grouptickets
            if ($pathinfo === '/agent/ticket-search/group-tickets.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::groupTicketsAction',  '_route' => 'agent_ticketsearch_grouptickets',);
            }

            // agent_ticketsearch_getpage
            if ($pathinfo === '/agent/ticket-search/get-page') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getTicketPageAction',  '_route' => 'agent_ticketsearch_getpage',);
            }

            // agent_ticketsearch_getflaggedsectiondata
            if ($pathinfo === '/agent/tickets/get-flagged-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getFlaggedSectionDataAction',  '_route' => 'agent_ticketsearch_getflaggedsectiondata',);
            }

            // agent_ticketsearch_runcustomfilter
            if ($pathinfo === '/agent/ticket-search/custom-filter/run') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::runCustomFilterAction',  '_route' => 'agent_ticketsearch_runcustomfilter',);
            }

            // agent_ticketsearch_quicksearch
            if ($pathinfo === '/agent/ticket-search/quick-search') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::quickSearchAction',  '_route' => 'agent_ticketsearch_quicksearch',);
            }

            // agent_ticketsearch_singleticketrow
            if (0 === strpos($pathinfo, '/agent/ticket-search/single-ticket-row') && preg_match('#^/agent/ticket\\-search/single\\-ticket\\-row/(?P<content_type>[^/]+?)/(?P<content_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getSingleTicketRowAction',)), array('_route' => 'agent_ticketsearch_singleticketrow'));
            }

            // agent_ticketsearch_runfilter
            if (0 === strpos($pathinfo, '/agent/ticket-search/filter') && preg_match('#^/agent/ticket\\-search/filter/(?P<filter_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::runFilterAction',)), array('_route' => 'agent_ticketsearch_runfilter'));
            }

            // agent_ticketsearch_runnamedfilter
            if (0 === strpos($pathinfo, '/agent/ticket-search/filter') && preg_match('#^/agent/ticket\\-search/filter/(?P<filter_name>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::runNamedFilterAction',)), array('_route' => 'agent_ticketsearch_runnamedfilter'));
            }

            // agent_ticketsearch_runsla
            if (0 === strpos($pathinfo, '/agent/ticket-search/sla') && preg_match('#^/agent/ticket\\-search/sla/(?P<sla_id>\\d+)(?:/(?P<sla_status>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::runSlaAction',  'sla_status' => '',)), array('_route' => 'agent_ticketsearch_runsla'));
            }

            // agent_ticketsearch_ajax_get_macro
            if ($pathinfo === '/agent/ticket-search/ajax-get-macro') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxGetMacroAction',  '_route' => 'agent_ticketsearch_ajax_get_macro',);
            }

            // agent_ticketsearch_ajax_get_macro_actions
            if ($pathinfo === '/agent/ticket-search/ajax-get-macro-actions') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxGetMacroActionsAction',  '_route' => 'agent_ticketsearch_ajax_get_macro_actions',);
            }

            // agent_ticketsearch_ajax_save_actions
            if ($pathinfo === '/agent/ticket-search/ajax-save-actions') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxSaveActionsAction',  '_route' => 'agent_ticketsearch_ajax_save_actions',);
            }

            // agent_ticketsearch_ajax_delete_tickets
            if ($pathinfo === '/agent/ticket-search/ajax-delete-tickets') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxDeleteTicketsAction',  '_route' => 'agent_ticketsearch_ajax_delete_tickets',);
            }

            // agent_ticketsearch_ajax_release_locks
            if ($pathinfo === '/agent/ticket-search/ajax-release-locks') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxReleaseLocksAction',  '_route' => 'agent_ticketsearch_ajax_release_locks',);
            }

            // agent_ticket_new
            if ($pathinfo === '/agent/tickets/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::newAction',  '_route' => 'agent_ticket_new',);
            }

            // agent_ticket_new_save
            if ($pathinfo === '/agent/tickets/new/save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::newSaveAction',  '_route' => 'agent_ticket_new_save',);
            }

            // agent_ticket_new_getpersonrow
            if (0 === strpos($pathinfo, '/agent/tickets/new/get-person-row') && preg_match('#^/agent/tickets/new/get\\-person\\-row/(?P<person_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::newticketGetPersonRowAction',)), array('_route' => 'agent_ticket_new_getpersonrow'));
            }

            // agent_ticket_getmessagetpl
            if (0 === strpos($pathinfo, '/agent/tickets/get-message-template') && preg_match('#^/agent/tickets/get\\-message\\-template/(?P<id>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::getTicketMessageTemplateAction',)), array('_route' => 'agent_ticket_getmessagetpl'));
            }

            // agent_ticket_update_drafts
            if ($pathinfo === '/agent/tickets/update-drafts') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::updateDraftsAction',  '_route' => 'agent_ticket_update_drafts',);
            }

            // agent_ticket_getmessagetext
            if (0 === strpos($pathinfo, '/agent/tickets/messages') && preg_match('#^/agent/tickets/messages/(?P<message_id>[^/]+?)/get\\-message\\-text\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxGetMessageTextAction',)), array('_route' => 'agent_ticket_getmessagetext'));
            }

            // agent_ticket_getfullmessage
            if (0 === strpos($pathinfo, '/agent/tickets/messages') && preg_match('#^/agent/tickets/messages/(?P<message_id>[^/]+?)/get\\-full\\-message\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxGetFullMessageAction',)), array('_route' => 'agent_ticket_getfullmessage'));
            }

            // agent_ticket_savemessagetext
            if (0 === strpos($pathinfo, '/agent/tickets/messages') && preg_match('#^/agent/tickets/messages/(?P<message_id>[^/]+?)/save\\-message\\-text\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveMessageTextAction',)), array('_route' => 'agent_ticket_savemessagetext'));
            }

            // agent_ticket_setmessagenote
            if (0 === strpos($pathinfo, '/agent/tickets/messages') && preg_match('#^/agent/tickets/messages/(?P<message_id>[^/]+?)/set\\-message\\-note\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSetNoteAction',)), array('_route' => 'agent_ticket_setmessagenote'));
            }

            // agent_ticket_message_attachments
            if (0 === strpos($pathinfo, '/agent/tickets/messages') && preg_match('#^/agent/tickets/messages/(?P<message_id>[^/]+?)/attachments$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::getMessageAttachmentsAction',)), array('_route' => 'agent_ticket_message_attachments'));
            }

            // agent_ticket_message_attachment_delete
            if (0 === strpos($pathinfo, '/agent/tickets/messages') && preg_match('#^/agent/tickets/messages/(?P<message_id>[^/]+?)/attachments/(?P<attachment_id>[^/]+?)/delete$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_ticket_message_attachment_delete;
                }
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteMessageAttachmentAction',)), array('_route' => 'agent_ticket_message_attachment_delete'));
            }
            not_agent_ticket_message_attachment_delete:

            // agent_ticket_message_delete
            if (0 === strpos($pathinfo, '/agent/tickets/messages') && preg_match('#^/agent/tickets/messages/(?P<message_id>[^/]+?)/delete$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_ticket_message_delete;
                }
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteMessageAction',)), array('_route' => 'agent_ticket_message_delete'));
            }
            not_agent_ticket_message_delete:

            // agent_ticket_view
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::viewAction',)), array('_route' => 'agent_ticket_view'));
            }

            // agent_ticket_loadlogs
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/load\\-logs$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::loadTicketLogsAction',)), array('_route' => 'agent_ticket_loadlogs'));
            }

            // agent_ticket_loadattachlist
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/load\\-attach\\-list$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::loadAttachListAction',)), array('_route' => 'agent_ticket_loadattachlist'));
            }

            // agent_ticket_download_debug_report
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/download\\-debug\\-report$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::downloadTicketDebugAction',)), array('_route' => 'agent_ticket_download_debug_report'));
            }

            // agent_ticket_messagepage
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/message\\-page/(?P<page>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::getMessagePageAction',)), array('_route' => 'agent_ticket_messagepage'));
            }

            // agent_ticket_doupdate
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/update\\-views\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::updateViewsAction',)), array('_route' => 'agent_ticket_doupdate'));
            }

            // agent_ticket_lock
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/lock\\-ticket\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::lockTicketAction',)), array('_route' => 'agent_ticket_lock'));
            }

            // agent_ticket_unlock
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/unlock\\-ticket\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::unlockTicketAction',)), array('_route' => 'agent_ticket_unlock'));
            }

            // agent_ticket_release_lock
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/release\\-lock\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::releaseLockAction',)), array('_route' => 'agent_ticket_release_lock'));
            }

            // agent_ticket_split
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/split(?:/(?P<message_id>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::splitAction',  'message_id' => 0,)), array('_route' => 'agent_ticket_split'));
            }

            // agent_ticket_split_save
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/split\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::splitSaveAction',)), array('_route' => 'agent_ticket_split_save'));
            }

            // agent_ticket_merge_overlay
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/merge\\-overlay/(?P<other_ticket_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::mergeOverlayAction',)), array('_route' => 'agent_ticket_merge_overlay'));
            }

            // agent_ticket_fwd_overlay
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/forward/(?P<message_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::forwardOverlayAction',)), array('_route' => 'agent_ticket_fwd_overlay'));
            }

            // agent_ticket_fwd_send
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/forward/(?P<message_id>\\d+)/send$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_ticket_fwd_send;
                }
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::forwardSendAction',)), array('_route' => 'agent_ticket_fwd_send'));
            }
            not_agent_ticket_fwd_send:

            // agent_ticket_merge
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/merge/(?P<other_ticket_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::mergeAction',)), array('_route' => 'agent_ticket_merge'));
            }

            // agent_ticket_changeuser_overlay
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/change\\-user\\-overlay$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::changeUserOverlayAction',)), array('_route' => 'agent_ticket_changeuser_overlay'));
            }

            // agent_ticket_changeuser_overlay_preview
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/change\\-user\\-overlay/preview/(?P<new_person_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::changeUserOverlayPreviewAction',)), array('_route' => 'agent_ticket_changeuser_overlay_preview'));
            }

            // agent_ticket_changeuser
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/change\\-user$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::changeUserAction',)), array('_route' => 'agent_ticket_changeuser'));
            }

            // agent_ticket_ajaxsavecustomfields
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-save\\-custom\\-fields$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveCustomFieldsAction',)), array('_route' => 'agent_ticket_ajaxsavecustomfields'));
            }

            // agent_ticket_ajaxsavereply
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-save\\-reply$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveReplyAction',)), array('_route' => 'agent_ticket_ajaxsavereply'));
            }

            // agent_ticket_ajaxsavesubject
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-save\\-subject\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveSubjectAction',)), array('_route' => 'agent_ticket_ajaxsavesubject'));
            }

            // agent_ticket_ajaxchangeuseremail
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-change\\-email\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxChangeUserEmailAction',)), array('_route' => 'agent_ticket_ajaxchangeuseremail'));
            }

            // agent_ticket_ajaxsaveoptions
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-save\\-options$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveOptionsAction',)), array('_route' => 'agent_ticket_ajaxsaveoptions'));
            }

            // agent_ticket_ajaxsaveflagged
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-save\\-flagged$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveFlaggedAction',)), array('_route' => 'agent_ticket_ajaxsaveflagged'));
            }

            // agent_ticket_addpart
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>[^/]+?)/add\\-part$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::addParticipantAction',)), array('_route' => 'agent_ticket_addpart'));
            }

            // agent_ticket_set_agent_parts
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>[^/]+?)/set\\-agent\\-parts\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::setAgentParticipantsAction',)), array('_route' => 'agent_ticket_set_agent_parts'));
            }

            // agent_ticket_delpart
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>[^/]+?)/remove\\-part\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::removeParticipantAction',)), array('_route' => 'agent_ticket_delpart'));
            }

            // agent_ticket_ajaxtab_releated_content
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-tab\\-related\\-content$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxTabRelatedContentAction',)), array('_route' => 'agent_ticket_ajaxtab_releated_content'));
            }

            // agent_ticket_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveLabelsAction',)), array('_route' => 'agent_ticket_ajax_labels_save'));
            }

            // agent_ticket_ajax_get_macro
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-get\\-macro$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxGetMacroAction',)), array('_route' => 'agent_ticket_ajax_get_macro'));
            }

            // agent_ticket_ajax_apply_macro
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/(?P<macro_id>\\d+)/apply\\-macro\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::applyMacroAction',)), array('_route' => 'agent_ticket_ajax_apply_macro'));
            }

            // agent_ticket_ajax_save_actions
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/ajax\\-save\\-actions$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveActionsAction',)), array('_route' => 'agent_ticket_ajax_save_actions'));
            }

            // agent_ticket_message_raw
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/message\\-details/(?P<message_id>\\d+)/view\\-raw$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::viewRawMessageAction',)), array('_route' => 'agent_ticket_message_raw'));
            }

            // agent_ticket_message_window
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/message\\-details/(?P<message_id>\\d+)/window(?:/(?P<type>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::viewMessageWindowAction',  'type' => 'normal',)), array('_route' => 'agent_ticket_message_window'));
            }

            // agent_ticket_message_ajax_getquote
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/message\\-details/(?P<message_id>\\d+)/ajax\\-get\\-quote$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxGetMessageQuoteAction',)), array('_route' => 'agent_ticket_message_ajax_getquote'));
            }

            // agent_ticket_saveagentparts
            if (0 === strpos($pathinfo, '/agent/ticket') && preg_match('#^/agent/ticket/(?P<ticket_id>\\d+)/save\\-agent\\-parts$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::saveAgentPartsAction',)), array('_route' => 'agent_ticket_saveagentparts'));
            }

            // agent_ticket_addcharge
            if (0 === strpos($pathinfo, '/agent/ticket') && preg_match('#^/agent/ticket/(?P<ticket_id>\\d+)/add\\-charge$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::addChargeAction',)), array('_route' => 'agent_ticket_addcharge'));
            }

            // agent_ticket_chargedelete
            if (0 === strpos($pathinfo, '/agent/ticket') && preg_match('#^/agent/ticket/(?P<ticket_id>\\d+)/charge/(?P<charge_id>\\d+)/delete/(?P<security_token>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteChargeAction',)), array('_route' => 'agent_ticket_chargedelete'));
            }

            // agent_ticket_addsla
            if (0 === strpos($pathinfo, '/agent/ticket') && preg_match('#^/agent/ticket/(?P<ticket_id>\\d+)/add\\-sla$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::addSlaAction',)), array('_route' => 'agent_ticket_addsla'));
            }

            // agent_ticket_sladelete
            if (0 === strpos($pathinfo, '/agent/ticket') && preg_match('#^/agent/ticket/(?P<ticket_id>\\d+)/sla/(?P<sla_id>\\d+)/delete/(?P<security_token>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteSlaAction',)), array('_route' => 'agent_ticket_sladelete'));
            }

            // agent_ticket_delete
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/delete$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteAction',)), array('_route' => 'agent_ticket_delete'));
            }

            // agent_ticket_spam
            if (0 === strpos($pathinfo, '/agent/tickets') && preg_match('#^/agent/tickets/(?P<ticket_id>\\d+)/spam$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::spamAction',)), array('_route' => 'agent_ticket_spam'));
            }

            // agent_twitter_new
            if ($pathinfo === '/agent/twitter/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::newTweetAction',  '_route' => 'agent_twitter_new',);
            }

            // agent_twitter_new_save
            if ($pathinfo === '/agent/twitter/new/save') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_new_save;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::newTweetSaveAction',  '_route' => 'agent_twitter_new_save',);
            }
            not_agent_twitter_new_save:

            // agent_twitter_mine_list
            if (0 === strpos($pathinfo, '/agent/twitter/mine') && preg_match('#^/agent/twitter/mine/(?P<account_id>\\d+)(?:/(?P<group>[^/]+?)(?:/(?P<group_value>[^/]+?))?)?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listMineAction',  'group' => '',  'group_value' => '',)), array('_route' => 'agent_twitter_mine_list'));
            }

            // agent_twitter_team_list
            if (0 === strpos($pathinfo, '/agent/twitter/team') && preg_match('#^/agent/twitter/team/(?P<account_id>\\d+)(?:/(?P<group>[^/]+?)(?:/(?P<group_value>[^/]+?))?)?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listTeamAction',  'group' => '',  'group_value' => '',)), array('_route' => 'agent_twitter_team_list'));
            }

            // agent_twitter_unassigned_list
            if (0 === strpos($pathinfo, '/agent/twitter/unassigned') && preg_match('#^/agent/twitter/unassigned/(?P<account_id>\\d+)(?:/(?P<group>[^/]+?)(?:/(?P<group_value>[^/]+?))?)?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listUnassignedAction',  'group' => '',  'group_value' => '',)), array('_route' => 'agent_twitter_unassigned_list'));
            }

            // agent_twitter_all_list
            if (0 === strpos($pathinfo, '/agent/twitter/all') && preg_match('#^/agent/twitter/all/(?P<account_id>\\d+)(?:/(?P<group>[^/]+?)(?:/(?P<group_value>[^/]+?))?)?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listAllAction',  'group' => '',  'group_value' => '',)), array('_route' => 'agent_twitter_all_list'));
            }

            // agent_twitter_sent_list
            if (0 === strpos($pathinfo, '/agent/twitter/sent') && preg_match('#^/agent/twitter/sent/(?P<account_id>\\d+)(?:/(?P<group>[^/]+?)(?:/(?P<group_value>[^/]+?))?)?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listSentAction',  'group' => '',  'group_value' => '',)), array('_route' => 'agent_twitter_sent_list'));
            }

            // agent_twitter_timeline_list
            if (0 === strpos($pathinfo, '/agent/twitter/timeline') && preg_match('#^/agent/twitter/timeline/(?P<account_id>\\d+)(?:/(?P<group>[^/]+?)(?:/(?P<group_value>[^/]+?))?)?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listTimelineAction',  'group' => '',  'group_value' => '',)), array('_route' => 'agent_twitter_timeline_list'));
            }

            // agent_twitter_followers_list
            if (0 === strpos($pathinfo, '/agent/twitter/followers') && preg_match('#^/agent/twitter/followers/(?P<account_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::listFollowersAction',)), array('_route' => 'agent_twitter_followers_list'));
            }

            // agent_twitter_followers_list_new
            if (0 === strpos($pathinfo, '/agent/twitter/followers') && preg_match('#^/agent/twitter/followers/(?P<account_id>\\d+)/new$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::listNewFollowersAction',)), array('_route' => 'agent_twitter_followers_list_new'));
            }

            // agent_twitter_following_list
            if (0 === strpos($pathinfo, '/agent/twitter/following') && preg_match('#^/agent/twitter/following/(?P<account_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::listFollowingAction',)), array('_route' => 'agent_twitter_following_list'));
            }

            // agent_twitter_status_ajaxmasssave
            if ($pathinfo === '/agent/twitter/status/ajax-mass-save.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxmasssave;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxMassSaveAction',  '_route' => 'agent_twitter_status_ajaxmasssave',);
            }
            not_agent_twitter_status_ajaxmasssave:

            // agent_twitter_status_ajaxsave_note
            if ($pathinfo === '/agent/twitter/status/ajax-note.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxsave_note;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveNoteAction',  '_route' => 'agent_twitter_status_ajaxsave_note',);
            }
            not_agent_twitter_status_ajaxsave_note:

            // agent_twitter_status_ajaxsave_retweet
            if ($pathinfo === '/agent/twitter/status/ajax-retweet.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxsave_retweet;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveRetweetAction',  '_route' => 'agent_twitter_status_ajaxsave_retweet',);
            }
            not_agent_twitter_status_ajaxsave_retweet:

            // agent_twitter_status_ajaxsave_unretweet
            if ($pathinfo === '/agent/twitter/status/ajax-unretweet.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxsave_unretweet;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveUnretweetAction',  '_route' => 'agent_twitter_status_ajaxsave_unretweet',);
            }
            not_agent_twitter_status_ajaxsave_unretweet:

            // agent_twitter_status_ajaxsave_reply
            if ($pathinfo === '/agent/twitter/status/ajax-reply.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxsave_reply;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveReplyAction',  '_route' => 'agent_twitter_status_ajaxsave_reply',);
            }
            not_agent_twitter_status_ajaxsave_reply:

            // agent_twitter_status_ajaxsave_archive
            if ($pathinfo === '/agent/twitter/status/ajax-archive.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxsave_archive;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveArchiveAction',  '_route' => 'agent_twitter_status_ajaxsave_archive',);
            }
            not_agent_twitter_status_ajaxsave_archive:

            // agent_twitter_status_ajaxsave_delete
            if ($pathinfo === '/agent/twitter/status/ajax-delete.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxsave_delete;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveDeleteAction',  '_route' => 'agent_twitter_status_ajaxsave_delete',);
            }
            not_agent_twitter_status_ajaxsave_delete:

            // agent_twitter_status_ajaxsave_edit
            if ($pathinfo === '/agent/twitter/status/ajax-edit') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveEditAction',  '_route' => 'agent_twitter_status_ajaxsave_edit',);
            }

            // agent_twitter_status_ajaxsave_favorite
            if ($pathinfo === '/agent/twitter/status/ajax-favorite.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxsave_favorite;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveFavoriteAction',  '_route' => 'agent_twitter_status_ajaxsave_favorite',);
            }
            not_agent_twitter_status_ajaxsave_favorite:

            // agent_twitter_status_ajaxsave_assign
            if ($pathinfo === '/agent/twitter/status/ajax-assign.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_status_ajaxsave_assign;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveAssignAction',  '_route' => 'agent_twitter_status_ajaxsave_assign',);
            }
            not_agent_twitter_status_ajaxsave_assign:

            // agent_twitter_status_tweet_overlay
            if ($pathinfo === '/agent/twitter/status/tweet-overlay') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::tweetOverlayAction',  '_route' => 'agent_twitter_status_tweet_overlay',);
            }

            // agent_twitter_user
            if (0 === strpos($pathinfo, '/agent/twitter/user') && preg_match('#^/agent/twitter/user/(?P<user_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::viewAction',)), array('_route' => 'agent_twitter_user'));
            }

            // agent_twitter_user_statuses
            if (0 === strpos($pathinfo, '/agent/twitter/user') && preg_match('#^/agent/twitter/user/(?P<user_id>\\d+)/statuses$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::viewUserStatusesAction',)), array('_route' => 'agent_twitter_user_statuses'));
            }

            // agent_twitter_user_following
            if (0 === strpos($pathinfo, '/agent/twitter/user') && preg_match('#^/agent/twitter/user/(?P<user_id>\\d+)/following$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::viewUserFollowingAction',)), array('_route' => 'agent_twitter_user_following'));
            }

            // agent_twitter_user_followers
            if (0 === strpos($pathinfo, '/agent/twitter/user') && preg_match('#^/agent/twitter/user/(?P<user_id>\\d+)/followers$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::viewUserFollowersAction',)), array('_route' => 'agent_twitter_user_followers'));
            }

            // agent_twitter_user_find
            if ($pathinfo === '/agent/twitter/user/find') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::findAction',  '_route' => 'agent_twitter_user_find',);
            }

            // agent_twitter_user_message_overlay
            if (0 === strpos($pathinfo, '/agent/twitter/user') && preg_match('#^/agent/twitter/user/(?P<user_id>\\d+)/message\\-overlay$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::messageOverlayAction',)), array('_route' => 'agent_twitter_user_message_overlay'));
            }

            // agent_twitter_user_ajaxsave_follow
            if ($pathinfo === '/agent/twitter/user/ajax-follow.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_user_ajaxsave_follow;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveFollowAction',  '_route' => 'agent_twitter_user_ajaxsave_follow',);
            }
            not_agent_twitter_user_ajaxsave_follow:

            // agent_twitter_user_ajaxsave_unfollow
            if ($pathinfo === '/agent/twitter/user/ajax-unfollow.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_user_ajaxsave_unfollow;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveUnfollowAction',  '_route' => 'agent_twitter_user_ajaxsave_unfollow',);
            }
            not_agent_twitter_user_ajaxsave_unfollow:

            // agent_twitter_user_ajaxsave_message
            if ($pathinfo === '/agent/twitter/user/ajax-message.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_user_ajaxsave_message;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveMessageAction',  '_route' => 'agent_twitter_user_ajaxsave_message',);
            }
            not_agent_twitter_user_ajaxsave_message:

            // agent_twitter_user_ajaxsave_archive
            if ($pathinfo === '/agent/twitter/user/ajax-archive.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_user_ajaxsave_archive;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveArchiveAction',  '_route' => 'agent_twitter_user_ajaxsave_archive',);
            }
            not_agent_twitter_user_ajaxsave_archive:

            // agent_twitter_user_ajaxsave_person
            if ($pathinfo === '/agent/twitter/user/ajax-person.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_user_ajaxsave_person;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSavePersonAction',  '_route' => 'agent_twitter_user_ajaxsave_person',);
            }
            not_agent_twitter_user_ajaxsave_person:

            // agent_twitter_user_ajaxsave_organization
            if ($pathinfo === '/agent/twitter/user/ajax-organization.json') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_twitter_user_ajaxsave_organization;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveOrganizationAction',  '_route' => 'agent_twitter_user_ajaxsave_organization',);
            }
            not_agent_twitter_user_ajaxsave_organization:

            // agent_twitter_getsectiondata
            if ($pathinfo === '/agent/twitter/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::getSectionDataAction',  '_route' => 'agent_twitter_getsectiondata',);
            }

            // agent_twitter_updategrouping
            if ($pathinfo === '/agent/twitter/update-grouping.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::updateGroupingAction',  '_route' => 'agent_twitter_updategrouping',);
            }

            // agent_twitter_run_search
            if (0 === strpos($pathinfo, '/agent/twitter') && preg_match('#^/agent/twitter/(?P<account_id>\\d+)/search/(?P<search_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::runSearchAction',)), array('_route' => 'agent_twitter_run_search'));
            }

            // agent_twitter_search_delete
            if (0 === strpos($pathinfo, '/agent/twitter') && preg_match('#^/agent/twitter/(?P<account_id>\\d+)/search/delete/(?P<security_token>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::deleteSearchAction',)), array('_route' => 'agent_twitter_search_delete'));
            }

            // agent_twitter_new_search
            if (0 === strpos($pathinfo, '/agent/twitter') && preg_match('#^/agent/twitter/(?P<account_id>\\d+)/search/new$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::newSearchAction',)), array('_route' => 'agent_twitter_new_search'));
            }

            // agent_task_new
            if ($pathinfo === '/agent/tasks/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::newAction',  '_route' => 'agent_task_new',);
            }

            // agent_task_save
            if ($pathinfo === '/agent/tasks/save') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_task_save;
                }
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::createAction',  '_route' => 'agent_task_save',);
            }
            not_agent_task_save:

            // agent_task_delete
            if (0 === strpos($pathinfo, '/agent/tasks') && preg_match('#^/agent/tasks/(?P<task_id>\\d+)/delete$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::deleteTaskAction',)), array('_route' => 'agent_task_delete'));
            }

            // agent_tasksearch_getsectiondata
            if ($pathinfo === '/agent/tasks/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::getSectionDataAction',  '_route' => 'agent_tasksearch_getsectiondata',);
            }

            // agent_task_list
            if (0 === strpos($pathinfo, '/agent/tasks/list') && preg_match('#^/agent/tasks/list(?:/(?P<search_type>[^/]+?)(?:/(?P<search_categoty>[^/]+?))?)?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::taskListAction',  'search_type' => NULL,  'search_categoty' => NULL,)), array('_route' => 'agent_task_list'));
            }

            // agent_task_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/tasks') && preg_match('#^/agent/tasks/(?P<task_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::ajaxSaveLabelsAction',)), array('_route' => 'agent_task_ajax_labels_save'));
            }

            // agent_task_ajaxsave_comment
            if (0 === strpos($pathinfo, '/agent/tasks') && preg_match('#^/agent/tasks/(?P<task_id>\\d+)/ajax\\-save\\-comment$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::ajaxSaveCommentAction',)), array('_route' => 'agent_task_ajaxsave_comment'));
            }

            // agent_task_ajaxsave
            if (0 === strpos($pathinfo, '/agent/tasks') && preg_match('#^/agent/tasks/(?P<task_id>[^/]+?)/ajax\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::ajaxSaveAction',)), array('_route' => 'agent_task_ajaxsave'));
            }

            // agent_dealearch_getsectiondata
            if ($pathinfo === '/agent/deal/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::getSectionDataAction',  '_route' => 'agent_dealearch_getsectiondata',);
            }

            // agent_deal_list
            if (0 === strpos($pathinfo, '/agent/deals/list') && preg_match('#^/agent/deals/list(?:/(?P<owner_type>[^/]+?)(?:/(?P<deal_status>[^/]+?)(?:/(?P<deal_type_id>\\d+))?)?)?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::dealListAction',  'owner_type' => NULL,  'deal_status' => NULL,  'deal_type_id' => NULL,)), array('_route' => 'agent_deal_list'));
            }

            // agent_deal_view
            if (0 === strpos($pathinfo, '/agent/deal') && preg_match('#^/agent/deal/(?P<deal_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::viewAction',)), array('_route' => 'agent_deal_view'));
            }

            // agent_deal_ajaxsave_note
            if (0 === strpos($pathinfo, '/agent/deal') && preg_match('#^/agent/deal/(?P<deal_id>\\d+)/ajax\\-save\\-note$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::ajaxSaveNoteAction',)), array('_route' => 'agent_deal_ajaxsave_note'));
            }

            // agent_deal_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/deal') && preg_match('#^/agent/deal/(?P<deal_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::ajaxSaveLabelsAction',)), array('_route' => 'agent_deal_ajax_labels_save'));
            }

            // agent_deal_ajaxsavecustomfields
            if (0 === strpos($pathinfo, '/agent/deal') && preg_match('#^/agent/deal/(?P<deal_id>\\d+)/ajax\\-save\\-custom\\-fields$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::ajaxSaveCustomFieldsAction',)), array('_route' => 'agent_deal_ajaxsavecustomfields'));
            }

            // agent_deal_set_agent_parts
            if (0 === strpos($pathinfo, '/agent/deals') && preg_match('#^/agent/deals/(?P<deal_id>[^/]+?)/(?P<agent_id>[^/]+?)/set\\-agent\\-parts\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::setAgentParticipantsAction',)), array('_route' => 'agent_deal_set_agent_parts'));
            }

            // agent_deal_ajaxsave
            if (0 === strpos($pathinfo, '/agent/deals') && preg_match('#^/agent/deals/(?P<deal_id>\\d+)/ajax\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::ajaxSaveAction',)), array('_route' => 'agent_deal_ajaxsave'));
            }

            // agent_deal_new
            if ($pathinfo === '/agent/deals/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newAction',  '_route' => 'agent_deal_new',);
            }

            // agent_deal_new_save
            if ($pathinfo === '/agent/deals/new/save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newSaveAction',  '_route' => 'agent_deal_new_save',);
            }

            // agent_deal_new_getpersonrow
            if (0 === strpos($pathinfo, '/agent/deals/new/get-person-row') && preg_match('#^/agent/deals/new/get\\-person\\-row/(?P<person_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealGetPersonRowAction',)), array('_route' => 'agent_deal_new_getpersonrow'));
            }

            // agent_deal_new_getorganizationrow
            if (0 === strpos($pathinfo, '/agent/deals/new/get-organization-row') && preg_match('#^/agent/deals/new/get\\-organization\\-row/(?P<org_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealGetOrganizationRowAction',)), array('_route' => 'agent_deal_new_getorganizationrow'));
            }

            // agent_deal_create_setpersonrow
            if (0 === strpos($pathinfo, '/agent/deals/new/create-person-row') && preg_match('#^/agent/deals/new/create\\-person\\-row/(?P<person_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealCreatePersonRowAction',)), array('_route' => 'agent_deal_create_setpersonrow'));
            }

            // agent_deal_new_setpersonrow
            if (0 === strpos($pathinfo, '/agent/deals/new/set-person-row') && preg_match('#^/agent/deals/new/set\\-person\\-row/(?P<person_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealSetPersonRowAction',)), array('_route' => 'agent_deal_new_setpersonrow'));
            }

            // agent_deal_new_setorganizationrow
            if (0 === strpos($pathinfo, '/agent/deals/new/set-organization-row') && preg_match('#^/agent/deals/new/set\\-organization\\-row/(?P<org_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealSetOrganizationRowAction',)), array('_route' => 'agent_deal_new_setorganizationrow'));
            }

            // agent_deal_create_setorganizationrow
            if (0 === strpos($pathinfo, '/agent/deals/new/create-organization-row') && preg_match('#^/agent/deals/new/create\\-organization\\-row/(?P<org_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealCreateOrganizationRowAction',)), array('_route' => 'agent_deal_create_setorganizationrow'));
            }

            // agent_publish_getsectiondata
            if ($pathinfo === '/agent/publish/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::getSectionDataAction',  '_route' => 'agent_publish_getsectiondata',);
            }

            // agent_publish_ratingwhovoted
            if (0 === strpos($pathinfo, '/agent/publish/rating-who-voted') && preg_match('#^/agent/publish/rating\\-who\\-voted/(?P<object_type>[^/]+?)/(?P<object_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::ratingWhoVotedAction',)), array('_route' => 'agent_publish_ratingwhovoted'));
            }

            // agent_publish_whoviewed
            if (0 === strpos($pathinfo, '/agent/publish/who-viewed') && preg_match('#^/agent/publish/who\\-viewed/(?P<object_type>[^/]+?)/(?P<object_id>[^/]+?)(?:/(?P<view_action>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::whoViewedAction',  'view_action' => 1,)), array('_route' => 'agent_publish_whoviewed'));
            }

            // agent_publish_save_stickysearchwords
            if (0 === strpos($pathinfo, '/agent/publish/save-sticky-search-words') && preg_match('#^/agent/publish/save\\-sticky\\-search\\-words/(?P<type>[^/]+?)/(?P<content_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::saveStickySearchWordsAction',)), array('_route' => 'agent_publish_save_stickysearchwords'));
            }

            // agent_publish_validatingcontent
            if ($pathinfo === '/agent/publish/content/validating') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listValidatingContentAction',  '_route' => 'agent_publish_validatingcontent',);
            }

            // agent_feedback_validatingcontent
            if ($pathinfo === '/agent/feedback/content/validating') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listValidatingFeedbackContentAction',  '_route' => 'agent_feedback_validatingcontent',);
            }

            // agent_feedback_validatingcomments
            if ($pathinfo === '/agent/feedback/comments/validating') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listValidatingFeedbackCommentsAction',  '_route' => 'agent_feedback_validatingcomments',);
            }

            // agent_publish_validatingcontent_approve
            if (0 === strpos($pathinfo, '/agent/publish/content/approve') && preg_match('#^/agent/publish/content/approve/(?P<type>[^/]+?)/(?P<content_id>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::approveContentAction',)), array('_route' => 'agent_publish_validatingcontent_approve'));
            }

            // agent_publish_validatingcontent_disapprove
            if (0 === strpos($pathinfo, '/agent/publish/content/disapprove') && preg_match('#^/agent/publish/content/disapprove/(?P<type>[^/]+?)/(?P<content_id>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::disapproveContentAction',)), array('_route' => 'agent_publish_validatingcontent_disapprove'));
            }

            // agent_publish_validatingcontent_mass
            if (0 === strpos($pathinfo, '/agent/publish/content/validating-mass-actions') && preg_match('#^/agent/publish/content/validating\\-mass\\-actions/(?P<action>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::validatingMassActionsAction',)), array('_route' => 'agent_publish_validatingcontent_mass'));
            }

            // agent_publish_validatingcontent_next
            if (0 === strpos($pathinfo, '/agent/publish/content/get-next-validating') && preg_match('#^/agent/publish/content/get\\-next\\-validating/(?P<type>[^/]+?)/(?P<content_id>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::nextValidatingContentAction',)), array('_route' => 'agent_publish_validatingcontent_next'));
            }

            // agent_publish_listcomments
            if (0 === strpos($pathinfo, '/agent/publish/comments/list') && preg_match('#^/agent/publish/comments/list/(?P<type>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listCommentsAction',)), array('_route' => 'agent_publish_listcomments'));
            }

            // agent_publish_validatingcomments
            if ($pathinfo === '/agent/publish/comments/validating') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listValidatingCommentsAction',  '_route' => 'agent_publish_validatingcomments',);
            }

            // agent_publish_approve_comment
            if (0 === strpos($pathinfo, '/agent/publish/comments/approve') && preg_match('#^/agent/publish/comments/approve/(?P<typename>[^/]+?)/(?P<comment_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::approveCommentAction',)), array('_route' => 'agent_publish_approve_comment'));
            }

            // agent_publish_delete_comment
            if (0 === strpos($pathinfo, '/agent/publish/comments/delete') && preg_match('#^/agent/publish/comments/delete/(?P<typename>[^/]+?)/(?P<comment_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::deleteCommentAction',)), array('_route' => 'agent_publish_delete_comment'));
            }

            // agent_publish_comment_info
            if (0 === strpos($pathinfo, '/agent/publish/comments/info') && preg_match('#^/agent/publish/comments/info/(?P<typename>[^/]+?)/(?P<comment_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::commentInfoAction',)), array('_route' => 'agent_publish_comment_info'));
            }

            // agent_publish_comment_save
            if (0 === strpos($pathinfo, '/agent/publish/comments/save-comment') && preg_match('#^/agent/publish/comments/save\\-comment/(?P<typename>[^/]+?)/(?P<comment_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::saveCommentAction',)), array('_route' => 'agent_publish_comment_save'));
            }

            // agent_public_comment_newticketinfo
            if (0 === strpos($pathinfo, '/agent/publish/comments/new-ticket-info') && preg_match('#^/agent/publish/comments/new\\-ticket\\-info/(?P<typename>[^/]+?)/(?P<comment_id>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::getNewTicketCommentInfoAction',)), array('_route' => 'agent_public_comment_newticketinfo'));
            }

            // agent_publish_validatingcomments_mass
            if (0 === strpos($pathinfo, '/agent/publish/comments/validating-mass-actions') && preg_match('#^/agent/publish/comments/validating\\-mass\\-actions/(?P<action>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::validatingCommentsMassActionsAction',)), array('_route' => 'agent_publish_validatingcomments_mass'));
            }

            // agent_publish_savecats
            if (0 === strpos($pathinfo, '/agent/publish/save-categories') && preg_match('#^/agent/publish/save\\-categories/(?P<type>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::saveCategoriesAction',)), array('_route' => 'agent_publish_savecats'));
            }

            // agent_publish_cats_adddel
            if (0 === strpos($pathinfo, '/agent/publish/categories') && preg_match('#^/agent/publish/categories/(?P<type>[^/]+?)/delete\\-category$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::deleteCategoryAction',)), array('_route' => 'agent_publish_cats_adddel'));
            }

            // agent_publish_cats_addcat
            if (0 === strpos($pathinfo, '/agent/publish/categories') && preg_match('#^/agent/publish/categories/(?P<type>[^/]+?)/add\\-category$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::addCategoryAction',)), array('_route' => 'agent_publish_cats_addcat'));
            }

            // agent_publish_cats_updateorders
            if (0 === strpos($pathinfo, '/agent/publish/categories') && preg_match('#^/agent/publish/categories/(?P<type>[^/]+?)/update\\-orders$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::updateCategoryOrdersAction',)), array('_route' => 'agent_publish_cats_updateorders'));
            }

            // agent_publish_cats_updatetitles
            if (0 === strpos($pathinfo, '/agent/publish/categories') && preg_match('#^/agent/publish/categories/(?P<type>[^/]+?)/update\\-titles$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::updateCategoryTitlesAction',)), array('_route' => 'agent_publish_cats_updatetitles'));
            }

            // agent_publish_cats_update
            if (0 === strpos($pathinfo, '/agent/publish/categories') && preg_match('#^/agent/publish/categories/(?P<type>[^/]+?)/update/(?P<category_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::updateCategoryAction',)), array('_route' => 'agent_publish_cats_update'));
            }

            // agent_publish_cats_updatestructure
            if (0 === strpos($pathinfo, '/agent/publish/categories') && preg_match('#^/agent/publish/categories/(?P<type>[^/]+?)/update\\-structure$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::updateCategoryStructureAction',)), array('_route' => 'agent_publish_cats_updatestructure'));
            }

            // agent_publish_cats_newform
            if (0 === strpos($pathinfo, '/agent/publish/categories') && preg_match('#^/agent/publish/categories/(?P<type>[^/]+?)/new\\-form$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::addCategoryFormAction',)), array('_route' => 'agent_publish_cats_newform'));
            }

            // agent_publish_cats_newform_save
            if (0 === strpos($pathinfo, '/agent/publish/categories') && preg_match('#^/agent/publish/categories/(?P<type>[^/]+?)/new\\-form/save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::addCategoryFormSaveAction',)), array('_route' => 'agent_publish_cats_newform_save'));
            }

            // agent_public_drafts
            if (0 === strpos($pathinfo, '/agent/publish/drafts') && preg_match('#^/agent/publish/drafts/(?P<type>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listDraftsAction',)), array('_route' => 'agent_public_drafts'));
            }

            // agent_public_drafts_mass
            if (0 === strpos($pathinfo, '/agent/publish/drafts/mass-actions') && preg_match('#^/agent/publish/drafts/mass\\-actions/(?P<action>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::draftsMassActionsAction',)), array('_route' => 'agent_public_drafts_mass'));
            }

            // agent_publish_search
            if ($pathinfo === '/agent/publish/search') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::searchAction',  '_route' => 'agent_publish_search',);
            }

            // agent_kb_newarticle_save
            if ($pathinfo === '/agent/kb/article/new/save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::newArticleSaveAction',  '_route' => 'agent_kb_newarticle_save',);
            }

            // agent_kb_newarticle
            if ($pathinfo === '/agent/kb/article/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::newArticleAction',  '_route' => 'agent_kb_newarticle',);
            }

            // agent_kb_article
            if (0 === strpos($pathinfo, '/agent/kb/article') && preg_match('#^/agent/kb/article/(?P<article_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::viewArticleAction',)), array('_route' => 'agent_kb_article'));
            }

            // agent_kb_ajaxsavecustomfields
            if (0 === strpos($pathinfo, '/agent/kb/article') && preg_match('#^/agent/kb/article/(?P<article_id>\\d+)/ajax\\-save\\-custom\\-fields$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxSaveCustomFieldsAction',)), array('_route' => 'agent_kb_ajaxsavecustomfields'));
            }

            // agent_kb_article_info
            if (0 === strpos($pathinfo, '/agent/kb/article') && preg_match('#^/agent/kb/article/(?P<article_id>\\d+)/info$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::articleInfoAction',)), array('_route' => 'agent_kb_article_info'));
            }

            // agent_kb_article_revisionstab
            if (0 === strpos($pathinfo, '/agent/kb/article') && preg_match('#^/agent/kb/article/(?P<article_id>\\d+)/view\\-revisions$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::viewRevisionsAction',)), array('_route' => 'agent_kb_article_revisionstab'));
            }

            // agent_kb_article_ajaxsave
            if (0 === strpos($pathinfo, '/agent/kb/article') && preg_match('#^/agent/kb/article/(?P<article_id>\\d+)/ajax\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxSaveAction',)), array('_route' => 'agent_kb_article_ajaxsave'));
            }

            // agent_kb_ajax_save_comment
            if (0 === strpos($pathinfo, '/agent/kb/article') && preg_match('#^/agent/kb/article/(?P<article_id>\\d+)/ajax\\-save\\-comment$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxSaveCommentAction',)), array('_route' => 'agent_kb_ajax_save_comment'));
            }

            // agent_kb_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/kb/article') && preg_match('#^/agent/kb/article/(?P<article_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxSaveLabelsAction',)), array('_route' => 'agent_kb_ajax_labels_save'));
            }

            // agent_kb_comparerevs
            if (0 === strpos($pathinfo, '/agent/kb/compare-revs') && preg_match('#^/agent/kb/compare\\-revs/(?P<rev_old_id>[^/]+?)/(?P<rev_new_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::compareRevisionsAction',)), array('_route' => 'agent_kb_comparerevs'));
            }

            // agent_kb_newpending
            if ($pathinfo === '/agent/kb/pending-articles/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::newPendingArticleAction',  '_route' => 'agent_kb_newpending',);
            }

            // agent_kb_pending_remove
            if (0 === strpos($pathinfo, '/agent/kb/pending-articles') && preg_match('#^/agent/kb/pending\\-articles/(?P<pending_article_id>[^/]+?)/remove$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::removePendingArticleAction',)), array('_route' => 'agent_kb_pending_remove'));
            }

            // agent_kb_pending_info
            if (0 === strpos($pathinfo, '/agent/kb/pending-articles') && preg_match('#^/agent/kb/pending\\-articles/(?P<pending_article_id>[^/]+?)/info$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::pendingArticleInfoAction',)), array('_route' => 'agent_kb_pending_info'));
            }

            // agent_kb_pending
            if ($pathinfo === '/agent/kb/pending-articles') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::listPendingArticlesAction',  '_route' => 'agent_kb_pending',);
            }

            // agent_kb_pending_massactions
            if (0 === strpos($pathinfo, '/agent/kb/pending-articles/mass-actions') && preg_match('#^/agent/kb/pending\\-articles/mass\\-actions/(?P<action>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::pendingArticlesMassActionsAction',)), array('_route' => 'agent_kb_pending_massactions'));
            }

            // agent_kb_list
            if (0 === strpos($pathinfo, '/agent/kb/list') && preg_match('#^/agent/kb/list(?:/(?P<category_id>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::listAction',  'category_id' => '0',)), array('_route' => 'agent_kb_list'));
            }

            // agent_kb_cat
            if (0 === strpos($pathinfo, '/agent/kb/category') && preg_match('#^/agent/kb/category/(?P<category_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::listAction',)), array('_route' => 'agent_kb_cat'));
            }

            // agent_kb_mass_save
            if ($pathinfo === '/agent/kb/article/ajax-mass-save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxMassSaveAction',  '_route' => 'agent_kb_mass_save',);
            }

            // agent_glossary_newword_json
            if ($pathinfo === '/agent/glossary/new-word.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::glossaryNewWordJsonAction',  '_route' => 'agent_glossary_newword_json',);
            }

            // agent_glossary_word_json
            if (0 === strpos($pathinfo, '/agent/glossary') && preg_match('#^/agent/glossary/(?P<word_id>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::glossaryWordJsonAction',)), array('_route' => 'agent_glossary_word_json'));
            }

            // agent_glossary_saveword_json
            if (0 === strpos($pathinfo, '/agent/glossary') && preg_match('#^/agent/glossary/(?P<word_id>[^/]+?)/edit\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::glossarySaveWordJsonAction',)), array('_route' => 'agent_glossary_saveword_json'));
            }

            // agent_glossary_delword_json
            if (0 === strpos($pathinfo, '/agent/glossary') && preg_match('#^/agent/glossary/(?P<word_id>[^/]+?)/delete\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::glossaryDeleteWordJsonAction',)), array('_route' => 'agent_glossary_delword_json'));
            }

            // agent_glossary_word_tip
            if (0 === strpos($pathinfo, '/agent/glossary') && preg_match('#^/agent/glossary/(?P<word>[^/]+?)/tip$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::tipAction',)), array('_route' => 'agent_glossary_word_tip'));
            }

            // agent_news_list
            if (0 === strpos($pathinfo, '/agent/news/list') && preg_match('#^/agent/news/list(?:/(?P<category_id>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::listAction',  'category_id' => '0',)), array('_route' => 'agent_news_list'));
            }

            // agent_news_view
            if (0 === strpos($pathinfo, '/agent/news/post') && preg_match('#^/agent/news/post/(?P<news_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::viewAction',)), array('_route' => 'agent_news_view'));
            }

            // agent_news_revisionstab
            if (0 === strpos($pathinfo, '/agent/news/post') && preg_match('#^/agent/news/post/(?P<news_id>\\d+)/view\\-revisions$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::viewRevisionsAction',)), array('_route' => 'agent_news_revisionstab'));
            }

            // agent_news_save
            if (0 === strpos($pathinfo, '/agent/news/post') && preg_match('#^/agent/news/post/(?P<news_id>\\d+)/ajax\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::ajaxSaveAction',)), array('_route' => 'agent_news_save'));
            }

            // agent_news_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/news') && preg_match('#^/agent/news/(?P<news_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::ajaxSaveLabelsAction',)), array('_route' => 'agent_news_ajax_labels_save'));
            }

            // agent_news_ajax_save_comment
            if (0 === strpos($pathinfo, '/agent/news/post') && preg_match('#^/agent/news/post/(?P<news_id>\\d+)/ajax\\-save\\-comment$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::ajaxSaveCommentAction',)), array('_route' => 'agent_news_ajax_save_comment'));
            }

            // agent_news_new_save
            if ($pathinfo === '/agent/news/new/save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::newNewsSaveAction',  '_route' => 'agent_news_new_save',);
            }

            // agent_news_new
            if ($pathinfo === '/agent/news/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::newNewsAction',  '_route' => 'agent_news_new',);
            }

            // agent_news_comparerevs
            if (0 === strpos($pathinfo, '/agent/news/compare-revs') && preg_match('#^/agent/news/compare\\-revs/(?P<rev_old_id>[^/]+?)/(?P<rev_new_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::compareRevisionsAction',)), array('_route' => 'agent_news_comparerevs'));
            }

            // agent_downloads_list
            if (0 === strpos($pathinfo, '/agent/downloads/list') && preg_match('#^/agent/downloads/list(?:/(?P<category_id>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::listAction',  'category_id' => '0',)), array('_route' => 'agent_downloads_list'));
            }

            // agent_downloads_view
            if (0 === strpos($pathinfo, '/agent/downloads/file') && preg_match('#^/agent/downloads/file/(?P<download_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::viewAction',)), array('_route' => 'agent_downloads_view'));
            }

            // agent_downloads_info
            if (0 === strpos($pathinfo, '/agent/downloads/file') && preg_match('#^/agent/downloads/file/(?P<download_id>\\d+)/info$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::infoAction',)), array('_route' => 'agent_downloads_info'));
            }

            // agent_kb_downloads_revisionstab
            if (0 === strpos($pathinfo, '/agent/downloads/file') && preg_match('#^/agent/downloads/file/(?P<download_id>[^/]+?)/view\\-revisions$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::viewRevisionsAction',)), array('_route' => 'agent_kb_downloads_revisionstab'));
            }

            // agent_downloads_save
            if (0 === strpos($pathinfo, '/agent/downloads/file') && preg_match('#^/agent/downloads/file/(?P<download_id>\\d+)/ajax\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::ajaxSaveAction',)), array('_route' => 'agent_downloads_save'));
            }

            // agent_downloads_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/downloads/file') && preg_match('#^/agent/downloads/file/(?P<download_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::ajaxSaveLabelsAction',)), array('_route' => 'agent_downloads_ajax_labels_save'));
            }

            // agent_downloads_ajax_save_comment
            if (0 === strpos($pathinfo, '/agent/downloads/file') && preg_match('#^/agent/downloads/file/(?P<download_id>\\d+)/ajax\\-save\\-comment$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::ajaxSaveCommentAction',)), array('_route' => 'agent_downloads_ajax_save_comment'));
            }

            // agent_downloads_new_save
            if ($pathinfo === '/agent/downloads/new/save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::newDownloadSaveAction',  '_route' => 'agent_downloads_new_save',);
            }

            // agent_downloads_new
            if ($pathinfo === '/agent/downloads/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::newDownloadAction',  '_route' => 'agent_downloads_new',);
            }

            // agent_downloads_comparerevs
            if (0 === strpos($pathinfo, '/agent/downloads/compare-revs') && preg_match('#^/agent/downloads/compare\\-revs/(?P<rev_old_id>[^/]+?)/(?P<rev_new_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::compareRevisionsAction',)), array('_route' => 'agent_downloads_comparerevs'));
            }

            // agent_feedback_category
            if (0 === strpos($pathinfo, '/agent/feedback/category') && preg_match('#^/agent/feedback/category/(?P<category_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::categoryListAction',)), array('_route' => 'agent_feedback_category'));
            }

            // agent_feedback_status
            if (0 === strpos($pathinfo, '/agent/feedback/status') && preg_match('#^/agent/feedback/status/(?P<status>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::statusListAction',)), array('_route' => 'agent_feedback_status'));
            }

            // agent_feedback_label
            if (0 === strpos($pathinfo, '/agent/feedback/label') && preg_match('#^/agent/feedback/label/(?P<label>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::labelListAction',)), array('_route' => 'agent_feedback_label'));
            }

            // agent_feedback_filter
            if ($pathinfo === '/agent/feedback/filter') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::filterListAction',  '_route' => 'agent_feedback_filter',);
            }

            // agent_feedback_massactions
            if (0 === strpos($pathinfo, '/agent/feedback/filter/mass-actions') && preg_match('#^/agent/feedback/filter/mass\\-actions/(?P<action>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::massActionsAction',)), array('_route' => 'agent_feedback_massactions'));
            }

            // agent_feedback_getsectiondata
            if ($pathinfo === '/agent/feedback/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::getSectionDataAction',  '_route' => 'agent_feedback_getsectiondata',);
            }

            // agent_feedback_new
            if ($pathinfo === '/agent/feedback/new') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::newFeedbackAction',  '_route' => 'agent_feedback_new',);
            }

            // agent_feedback_new_save
            if ($pathinfo === '/agent/feedback/new/save') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::newFeedbackSaveAction',  '_route' => 'agent_feedback_new_save',);
            }

            // agent_feedback_view
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::viewAction',)), array('_route' => 'agent_feedback_view'));
            }

            // agent_feedback_comparerevs
            if (0 === strpos($pathinfo, '/agent/feedback/compare-revs') && preg_match('#^/agent/feedback/compare\\-revs/(?P<rev_old_id>[^/]+?)/(?P<rev_new_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::compareRevisionsAction',)), array('_route' => 'agent_feedback_comparerevs'));
            }

            // agent_feedback_ajaxsavecustomfields
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>\\d+)/ajax\\-save\\-custom\\-fields$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveCustomFieldsAction',)), array('_route' => 'agent_feedback_ajaxsavecustomfields'));
            }

            // agent_feedback_who_voted
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)/who\\-voted$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::whoVotedAction',)), array('_route' => 'agent_feedback_who_voted'));
            }

            // agent_feedback_save
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)/ajax\\-save$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveAction',)), array('_route' => 'agent_feedback_save'));
            }

            // agent_feedback_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveLabelsAction',)), array('_route' => 'agent_feedback_ajax_labels_save'));
            }

            // agent_feedback_ajax_save_comment
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)/ajax\\-save\\-comment$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveCommentAction',)), array('_route' => 'agent_feedback_ajax_save_comment'));
            }

            // agent_feedback_ajaxsavecomment
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)/ajax\\-save\\-comment$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveCommentAction',)), array('_route' => 'agent_feedback_ajaxsavecomment'));
            }

            // agent_feedback_ajaxsaveeditables
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)/ajax\\-save\\-editables$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveEditablesAction',)), array('_route' => 'agent_feedback_ajaxsaveeditables'));
            }

            // agent_feedback_ajaxupdatecat
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)/ajax\\-update\\-category/(?P<category_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxUpdateCategoryAction',)), array('_route' => 'agent_feedback_ajaxupdatecat'));
            }

            // agent_feedback_ajaxupdatestatus
            if (0 === strpos($pathinfo, '/agent/feedback/view') && preg_match('#^/agent/feedback/view/(?P<feedback_id>[^/]+?)/ajax\\-update\\-status/(?P<status_code>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxUpdateStatusAction',)), array('_route' => 'agent_feedback_ajaxupdatestatus'));
            }

            // agent_feedback_merge_overlay
            if (0 === strpos($pathinfo, '/agent/feedback/merge-overlay') && preg_match('#^/agent/feedback/merge\\-overlay/(?P<feedback_id>\\d+)/(?P<other_feedback_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::mergeOverlayAction',)), array('_route' => 'agent_feedback_merge_overlay'));
            }

            // agent_feedback_merge
            if (0 === strpos($pathinfo, '/agent/feedback/merge') && preg_match('#^/agent/feedback/merge/(?P<feedback_id>\\d+)/(?P<other_feedback_id>\\d+)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::mergeAction',)), array('_route' => 'agent_feedback_merge'));
            }

            // agent_agentchat_getonlineagents
            if ($pathinfo === '/agent/agent-chat/get-online-agents.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::getOnlineAgentsAction',  '_route' => 'agent_agentchat_getonlineagents',);
            }

            // agent_agentchat_get_last_convo
            if ($pathinfo === '/agent/agent-chat/get-last-convo') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::loadConvoMessagesAction',  '_route' => 'agent_agentchat_get_last_convo',);
            }

            // agent_agentchat_send_message
            if (0 === strpos($pathinfo, '/agent/agent-chat/send-message') && preg_match('#^/agent/agent\\-chat/send\\-message/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::sendMessageAction',)), array('_route' => 'agent_agentchat_send_message'));
            }

            // agent_agentchat_send_agent_message
            if (0 === strpos($pathinfo, '/agent/agent-chat/send-agent-message') && preg_match('#^/agent/agent\\-chat/send\\-agent\\-message/(?P<convo_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::sendAgentMessageAction',)), array('_route' => 'agent_agentchat_send_agent_message'));
            }

            // agent_agentchat_history
            if (0 === strpos($pathinfo, '/agent/agent-chat/agent-history') && preg_match('#^/agent/agent\\-chat/agent\\-history/(?P<agent_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::agentHistoryAction',)), array('_route' => 'agent_agentchat_history'));
            }

            // agent_agentchat_history_team
            if (0 === strpos($pathinfo, '/agent/agent-chat/agent-history/team') && preg_match('#^/agent/agent\\-chat/agent\\-history/team/(?P<agent_team_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::agentTeamHistoryAction',)), array('_route' => 'agent_agentchat_history_team'));
            }

            // agent_agentchat_view
            if (0 === strpos($pathinfo, '/agent/agent-chat/agent-transcript') && preg_match('#^/agent/agent\\-chat/agent\\-transcript/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::agentChatTranscriptAction',)), array('_route' => 'agent_agentchat_view'));
            }

            // agent_agentchat_getsectiondata
            if ($pathinfo === '/agent/agent-chat/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::getSectionDataAction',  '_route' => 'agent_agentchat_getsectiondata',);
            }

            // agent_userchat_view
            if (0 === strpos($pathinfo, '/agent/chat/view') && preg_match('#^/agent/chat/view/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::viewAction',)), array('_route' => 'agent_userchat_view'));
            }

            // agent_userchat_save_fields
            if (0 === strpos($pathinfo, '/agent/chat') && preg_match('#^/agent/chat/(?P<conversation_id>[^/]+?)/save\\-fields$#s', $pathinfo, $matches)) {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_agent_userchat_save_fields;
                }
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::saveFieldsAction',)), array('_route' => 'agent_userchat_save_fields'));
            }
            not_agent_userchat_save_fields:

            // agent_userchat_blockuser
            if (0 === strpos($pathinfo, '/agent/chat/block-user') && preg_match('#^/agent/chat/block\\-user/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::blockUserAction',)), array('_route' => 'agent_userchat_blockuser'));
            }

            // agent_userchat_unblockuser
            if (0 === strpos($pathinfo, '/agent/chat/unblock-user') && preg_match('#^/agent/chat/unblock\\-user/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::unblockUserAction',)), array('_route' => 'agent_userchat_unblockuser'));
            }

            // agent_userchat_ajax_labels_save
            if (0 === strpos($pathinfo, '/agent/chat') && preg_match('#^/agent/chat/(?P<conversation_id>\\d+)/ajax\\-save\\-labels$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::ajaxSaveLabelsAction',)), array('_route' => 'agent_userchat_ajax_labels_save'));
            }

            // agent_userchat_open_counts
            if ($pathinfo === '/agent/chat/open-counts.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::getOpenCountsAction',  '_route' => 'agent_userchat_open_counts',);
            }

            // agent_userchat_filterlist_group_counts
            if ($pathinfo === '/agent/chat/group-count.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::getGroupByCountsAction',  '_route' => 'agent_userchat_filterlist_group_counts',);
            }

            // agent_userchat_filterlist
            if (0 === strpos($pathinfo, '/agent/chat/filter') && preg_match('#^/agent/chat/filter/(?P<filter_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::filterAction',)), array('_route' => 'agent_userchat_filterlist'));
            }

            // agent_userchat_list_new
            if (0 === strpos($pathinfo, '/agent/chat/list-new') && preg_match('#^/agent/chat/list\\-new(?:/(?P<department_id>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::listNewChatsAction',  'department_id' => '-1',)), array('_route' => 'agent_userchat_list_new'));
            }

            // agent_userchat_list_active
            if (0 === strpos($pathinfo, '/agent/chat/list-active') && preg_match('#^/agent/chat/list\\-active(?:/(?P<agent_id>[^/]+?))?$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::listActiveChatsAction',  'agent_id' => '-1',)), array('_route' => 'agent_userchat_list_active'));
            }

            // agent_userchat_send_messageview
            if (0 === strpos($pathinfo, '/agent/chat/send-message') && preg_match('#^/agent/chat/send\\-message/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::sendMessageAction',)), array('_route' => 'agent_userchat_send_messageview'));
            }

            // agent_userchat_send_filemessage
            if (0 === strpos($pathinfo, '/agent/chat/send-file-message') && preg_match('#^/agent/chat/send\\-file\\-message/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::sendFileAction',)), array('_route' => 'agent_userchat_send_filemessage'));
            }

            // agent_userchat_assign
            if (0 === strpos($pathinfo, '/agent/chat/assign') && preg_match('#^/agent/chat/assign/(?P<conversation_id>[^/]+?)/(?P<agent_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::assignChatAction',)), array('_route' => 'agent_userchat_assign'));
            }

            // agent_userchat_syncpart
            if (0 === strpos($pathinfo, '/agent/chat/sync-parts') && preg_match('#^/agent/chat/sync\\-parts/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::syncPartsAction',)), array('_route' => 'agent_userchat_syncpart'));
            }

            // agent_userchat_addpart
            if (0 === strpos($pathinfo, '/agent/chat/add-part') && preg_match('#^/agent/chat/add\\-part/(?P<conversation_id>[^/]+?)/(?P<agent_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::addPartAction',)), array('_route' => 'agent_userchat_addpart'));
            }

            // agent_userchat_end
            if (0 === strpos($pathinfo, '/agent/chat/end-chat') && preg_match('#^/agent/chat/end\\-chat/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::endChatAction',)), array('_route' => 'agent_userchat_end'));
            }

            // agent_userchat_leave
            if (0 === strpos($pathinfo, '/agent/chat/leave') && preg_match('#^/agent/chat/leave/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::leaveChatAction',)), array('_route' => 'agent_userchat_leave'));
            }

            // agent_userchat_invite
            if (0 === strpos($pathinfo, '/agent/chat/invite') && preg_match('#^/agent/chat/invite/(?P<conversation_id>[^/]+?)/(?P<agent_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::sendInviteAction',)), array('_route' => 'agent_userchat_invite'));
            }

            // agent_userchat_changeprop
            if (0 === strpos($pathinfo, '/agent/chat/change-props') && preg_match('#^/agent/chat/change\\-props/(?P<conversation_id>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::changePropertiesAction',)), array('_route' => 'agent_userchat_changeprop'));
            }

            // agent_userchat_getsectiondata
            if ($pathinfo === '/agent/chat/get-section-data.json') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::getSectionDataAction',  '_route' => 'agent_userchat_getsectiondata',);
            }

            // agent_usertrack_winheadertable
            if ($pathinfo === '/agent/user-track/win-header-table.html') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserTrackController::winHeaderTableAction',  '_route' => 'agent_usertrack_winheadertable',);
            }

            // agent_usertrack_view
            if (0 === strpos($pathinfo, '/agent/user-track') && preg_match('#^/agent/user\\-track/(?P<visitor_id>\\d+)$#s', $pathinfo, $matches)) {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_agent_usertrack_view;
                }
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserTrackController::viewAction',)), array('_route' => 'agent_usertrack_view'));
            }
            not_agent_usertrack_view:

            // agent_mediamanager
            if ($pathinfo === '/agent/media-manager') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MediaManagerController::windowAction',  '_route' => 'agent_mediamanager',);
            }

            // agent_mediamanager_upload
            if ($pathinfo === '/agent/media-manager/upload') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MediaManagerController::uploadAction',  '_route' => 'agent_mediamanager_upload',);
            }

            // agent_mediamanager_browse
            if ($pathinfo === '/agent/media-manager/browse') {
                return array (  '_controller' => 'Application\\AgentBundle\\Controller\\MediaManagerController::browseAction',  '_route' => 'agent_mediamanager_browse',);
            }

            // agent_textsnippets_widget_shell
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/widget\\-shell\\.txt$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::getWidgetShellAction',)), array('_route' => 'agent_textsnippets_widget_shell'));
            }

            // agent_textsnippets_reloadclient
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/reload\\-client\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::reloadClientAction',)), array('_route' => 'agent_textsnippets_reloadclient'));
            }

            // agent_textsnippets_reloadclient_batch
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/reload\\-client/(?P<batch>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::reloadClientBatchAction',)), array('_route' => 'agent_textsnippets_reloadclient_batch'));
            }

            // agent_textsnippets_filtersnippets
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/filter\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::filterSnippetsAction',)), array('_route' => 'agent_textsnippets_filtersnippets'));
            }

            // agent_textsnippets_getsnippet
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/(?P<id>[^/\\.]+?)\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::getSnippetAction',)), array('_route' => 'agent_textsnippets_getsnippet'));
            }

            // agent_textsnippets_savesnippet
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/(?P<id>[^/]+?)/save\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::saveSnippetAction',)), array('_route' => 'agent_textsnippets_savesnippet'));
            }

            // agent_textsnippets_delsnippet
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/(?P<id>[^/]+?)/delete\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::deleteSnippetAction',)), array('_route' => 'agent_textsnippets_delsnippet'));
            }

            // agent_textsnippets_savecat
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/categories/(?P<id>[^/]+?)/save\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::saveCategoryAction',)), array('_route' => 'agent_textsnippets_savecat'));
            }

            // agent_textsnippets_delcat
            if (0 === strpos($pathinfo, '/agent/text-snippets') && preg_match('#^/agent/text\\-snippets/(?P<typename>[^/]+?)/categories/(?P<id>[^/]+?)/delete\\.json$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::deleteCategoryAction',)), array('_route' => 'agent_textsnippets_delcat'));
            }

            // agent_plugins_run
            if (0 === strpos($pathinfo, '/agent/plugins') && preg_match('#^/agent/plugins/(?P<plugin_id>[^/]+?)/run/(?P<action>[^/]+?)$#s', $pathinfo, $matches)) {
                return array_merge($this->mergeDefaults($matches, array (  '_controller' => 'Application\\AgentBundle\\Controller\\PluginsController::runAction',)), array('_route' => 'agent_plugins_run'));
            }

        }

        if (0 === strpos($pathinfo, '/dev')) {
            // dev
            if (rtrim($pathinfo, '/') === '/dev') {
                if (substr($pathinfo, -1) !== '/') {
                    return $this->redirect($pathinfo.'/', 'dev');
                }
                return array (  '_controller' => 'DevBundle:Main:index',  '_route' => 'dev',);
            }

            // dev_phpinfo
            if ($pathinfo === '/dev/php-info') {
                return array (  '_controller' => 'DevBundle:Main:phpInfo',  '_route' => 'dev_phpinfo',);
            }

            // dev_testemail_usernewticket
            if ($pathinfo === '/dev/test-email/user-new-ticket') {
                return array (  '_controller' => 'DevBundle:TestEmail:userNewTicket',  '_route' => 'dev_testemail_usernewticket',);
            }

            // dev_seefile
            if ($pathinfo === '/dev/see-file') {
                return array (  '_controller' => 'DevBundle:Main:seeFile',  '_route' => 'dev_seefile',);
            }

            // dev_run_workerjob
            if ($pathinfo === '/dev/run-worker-job') {
                return array (  '_controller' => 'DevBundle:Main:runWorkerJob',  '_route' => 'dev_run_workerjob',);
            }

            // dev_models
            if ($pathinfo === '/dev/models') {
                return array (  '_controller' => 'DevBundle:Models:index',  '_route' => 'dev_models',);
            }

            // dev_models_getsql
            if ($pathinfo === '/dev/models/get-sql') {
                return array (  '_controller' => 'DevBundle:Models:getSql',  '_route' => 'dev_models_getsql',);
            }

            // dev_models_regenerateproxies
            if ($pathinfo === '/dev/models/regenerate-proxies') {
                return array (  '_controller' => 'DevBundle:Models:regenerateProxies',  '_route' => 'dev_models_regenerateproxies',);
            }

            // dev_test
            if ($pathinfo === '/dev/test') {
                return array (  '_controller' => 'DevBundle:Test:index',  '_route' => 'dev_test',);
            }

            // dev_phptest
            if ($pathinfo === '/dev/php-test') {
                return array (  '_controller' => 'DevBundle:Main:phpTest',  '_route' => 'dev_phptest',);
            }

            // dev_phptest_run
            if ($pathinfo === '/dev/php-test/run') {
                return array (  '_controller' => 'DevBundle:Main:phpTestRun',  '_route' => 'dev_phptest_run',);
            }

            // dev_cm
            if ($pathinfo === '/dev/client-messages') {
                return array (  '_controller' => 'DevBundle:ClientMessages:index',  '_route' => 'dev_cm',);
            }

            // dev_lang_index
            if ($pathinfo === '/dev/lang/index') {
                return array (  '_controller' => 'DevBundle:Language:index',  '_route' => 'dev_lang_index',);
            }

            // dev_lang_mass_replace
            if ($pathinfo === '/dev/lang/mass/replace') {
                return array (  '_controller' => 'DevBundle:Language:batchReplace',  '_route' => 'dev_lang_mass_replace',);
            }

            // dev_lang_find_problems
            if ($pathinfo === '/dev/lang/find/problems') {
                return array (  '_controller' => 'DevBundle:Language:findProblems',  '_route' => 'dev_lang_find_problems',);
            }

            // dev_lang_show_useful
            if ($pathinfo === '/dev/lang/show/useful') {
                return array (  '_controller' => 'DevBundle:Language:showUseful',  '_route' => 'dev_lang_show_useful',);
            }

            // dev_lang_spell_check
            if ($pathinfo === '/dev/lang/spell/do') {
                return array (  '_controller' => 'DevBundle:Language:spellcheck',  '_route' => 'dev_lang_spell_check',);
            }

            // dev_lang_word_count
            if ($pathinfo === '/dev/lang/count/words') {
                return array (  '_controller' => 'DevBundle:Language:showWordCount',  '_route' => 'dev_lang_word_count',);
            }

            // dev_lang_reformat_langfiles
            if ($pathinfo === '/dev/lang/reformat/langfiles') {
                return array (  '_controller' => 'DevBundle:Language:reformatLanguageFiles',  '_route' => 'dev_lang_reformat_langfiles',);
            }

            // dev_lang_export_all_po
            if ($pathinfo === '/dev/lang/export/all/po') {
                return array (  '_controller' => 'DevBundle:Language:exportAllToPO',  '_route' => 'dev_lang_export_all_po',);
            }

            // dev_lang_phrase_user_context
            if ($pathinfo === '/dev/lang/context/user') {
                return array (  '_controller' => 'DevBundle:Language:showContextUser',  '_route' => 'dev_lang_phrase_user_context',);
            }

            // dev_emaildecodetest
            if ($pathinfo === '/dev/email-decode-test') {
                return array (  '_controller' => 'DevBundle:EmailDecodeTest:index',  '_route' => 'dev_emaildecodetest',);
            }

            // dev_emaildecodetest_run
            if ($pathinfo === '/dev/email-decode-test/run') {
                return array (  '_controller' => 'DevBundle:EmailDecodeTest:run',  '_route' => 'dev_emaildecodetest_run',);
            }

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
