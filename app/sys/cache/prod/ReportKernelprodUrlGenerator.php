<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;


/**
 * ReportKernelprodUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class ReportKernelprodUrlGenerator extends Application\DeskPRO\Routing\Generator\UrlGenerator
{
static private $routePatterns = array (
  'proxy' => '/proxy/{key}',
  'serve_blob' => '/file.php/{blob_auth_id}/{filename}',
  'serve_blob_size' => '/file.php/size/{s}/{blob_auth_id}/{filename}',
  'serve_blob_sizefit' => '/file.php/size/{s}/size-fit/{blob_auth_id}/{filename}',
  'serve_person_picture' => '/file.php/avatar/{person_id}',
  'serve_person_picture_size' => '/file.php/avatar/{person_id}',
  'serve_default_picture' => '/file.php/avatar/{s}/default.jpg',
  'favicon' => '/favicon.ico',
  'serve_org_picture_default' => '/file.php/o-avatar/default',
  'serve_org_picture' => '/file.php/o-avatar/{org_id}',
  'sys_log_js_error' => '/dp/log-js-error.json',
  'sys_report_error' => '/dp/report-error.json',
  'data_interface_data' => '/data/interface-data.{_format}',
  'dp3_redirect_files_php' => '/files.php',
  'dp3_redirect_attachment_files_php' => '/attachment_files.php',
  'dp3_redirect_ideas_php' => '/ideas.php',
  'dp3_redirect_kb_article_php' => '/kb_article.php',
  'dp3_redirect_kb_cat_php' => '/kb_cat.php',
  'dp3_redirect_kb_php' => '/kb.php',
  'dp3_redirect_login_php' => '/login.php',
  'dp3_redirect_manual_php' => '/manual.php',
  'dp3_redirect_manual_rewritten' => '/manual/{manual_bit}/{page_bit}',
  'dp3_redirect_manual_download_php' => '/manual_download.php',
  'dp3_redirect_news_archive_php' => '/news_archive.php',
  'dp3_redirect_news_full_php' => '/news_full.php',
  'dp3_redirect_news_php' => '/news.php',
  'dp3_redirect_newticket_php' => '/newticket.php',
  'dp3_redirect_profile_email_php' => '/profile_email.php',
  'dp3_redirect_profile_password_php' => '/profile_password.php',
  'dp3_redirect_profile_php' => '/profile.php',
  'dp3_redirect_register_php' => '/register.php',
  'dp3_redirect_reset_php' => '/reset.php',
  'dp3_redirect_ticketlist_php' => '/ticketlist.php',
  'dp3_redirect_ticketlist_company_php' => '/ticketlist_company.php',
  'dp3_redirect_ticketlist_participate_php' => '/ticketlist_participate.php',
  'dp3_redirect_troubleshooter_php' => '/troubleshooter.php',
  'dp3_redirect_view_php' => '/view.php',
  'user' => '/',
  'user_admin_rendertpl' => '/admin-render-template/{type}',
  'user_comment_form_login_partial' => '/_misc/comment-form-login-partial',
  'user_test' => '/test',
  'user_saverating' => '/portal/save-rating/{object_type}/{object_id}',
  'user_newcomment_finishlogin' => '/portal/save-comment/login-finished/{comment_type}/{comment_id}',
  'user_accept_upload' => '/accept-temp-upload',
  'user_validate_email' => '/validate-email/{id}/{auth}',
  'user_validate_ticket' => '/validate-ticket-email/{access_code}',
  'user_jstell_login' => '/login/jstell/{jstell}/{security_token}/{usersource_id}',
  'user_login' => '/login',
  'user_login_inline' => '/login/inline-login',
  'user_login_usersource_sso' => '/login/usersource-sso/{usersource_id}',
  'user_logout' => '/logout/{auth}',
  'user_login_authenticate_local' => '/login/authenticate-password',
  'user_login_authenticate' => '/login/authenticate/{usersource_id}',
  'user_login_callback' => '/login/authenticate-callback/{usersource_id}',
  'user_login_resetpass' => '/login/reset-password',
  'user_login_resetpass_send' => '/login/reset-password/send.{_format}',
  'user_login_resetpass_newpass' => '/login/reset-password/{code}',
  'user_login_agentlogin' => '/login/agent-login/{code}',
  'user_register' => '/register',
  'user_profile' => '/profile',
  'user_profile_setlang' => '/profile/quick-set-language',
  'user_profile_associate_twitter' => '/profile/associate-twitter',
  'user_profile_twitter_remove' => '/profile/twitter/{account_id}/remove',
  'user_profile_changepassword' => '/profile/change-password',
  'user_profile_emails_new' => '/profile/emails/new',
  'user_profile_emails_remove' => '/profile/emails/{email_id}/remove',
  'user_profile_emails_validate_remove' => '/profile/emails/{email_id}/remove-validating',
  'user_profile_emails_validate_sendlink' => '/profile/emails/{email_id}/validate/send-link',
  'user_profile_emails_setdefault' => '/profile/emails/{email_id}/set-default',
  'user_search' => '/search',
  'user_search_labels' => '/search/labels/{type}/{label}',
  'user_search_omnisearch' => '/search/omnisearch/{query}',
  'user_search_similarto' => '/search/similar-to/{content_type}',
  'user_tickets' => '/tickets',
  'user_tickets_organization' => '/tickets/organization',
  'user_tickets_new' => '/new-ticket/{for_department_id}',
  'user_tickets_new_finishlogin' => '/new-ticket/login-finish/{ticket_id}',
  'user_tickets_new_simple' => '/tickets/new-simple/{for_department_id}',
  'user_tickets_new_savestatus' => '/tickets/new/save-status',
  'user_tickets_new_contentsolved_save' => '/tickets/new/content-solved-save.json',
  'user_tickets_new_contentsolved' => '/tickets/new/content-solved-redirect',
  'user_tickets_new_thanks' => '/tickets/new/thanks/{ticket_ref}',
  'user_tickets_new_thanks_simple' => '/tickets/new/thanks-simple/{ticket_ref}',
  'user_tickets_view' => '/ticket/{ticket_ref}',
  'user_tickets_addreply' => '/ticket-edit/{ticket_ref}/add-reply',
  'user_tickets_participants' => '/ticket-edit/{ticket_ref}/people',
  'user_tickets_participants_add' => '/ticket-edit/{ticket_ref}/people/add',
  'user_tickets_participants_remove' => '/ticket-edit/{ticket_ref}/people/remove/{person_id}',
  'user_tickets_resolve' => '/ticket-edit/{ticket_ref}/resolve',
  'user_tickets_unresolve' => '/ticket-edit/{ticket_ref}/unresolve',
  'user_tickets_feedback' => '/ticket-rate/{ticket_ref}/{auth}/{message_id}',
  'user_tickets_feedback_save' => '/ticket-rate/{ticket_ref}/{auth}/{message_id}/save',
  'user_tickets_feedback_closeticket' => '/ticket-edit/{ticket_ref}/feedback/{message_id}/close-ticket',
  'user_articles_home' => '/kb',
  'user_articles' => '/kb/{slug}',
  'user_articles_article' => '/kb/articles/{slug}',
  'user_articles_article_togglesub' => '/kb/article-subscription/{article_id}/{auth}',
  'user_articles_cat_togglesub' => '/kb/category-subscription/{category_id}/{auth}',
  'user_articles_unsub_all' => '/kb/unsubscribe-all/{person_id}/{auth}',
  'user_articles_article_agent_iframe' => '/kb/articles/agent-iframe/{article_id}/{agent_session_id}',
  'user_articles_newcomment' => '/kb/new-comment/{article_id}',
  'user_downloads_home' => '/downloads',
  'user_downloads' => '/downloads/{slug}',
  'user_downloads_file' => '/downloads/files/{slug}',
  'user_downloads_file_download' => '/downloads/files/{slug}/download',
  'user_downloads_newcomment' => '/downloads/new-comment/{download_id}',
  'user_news_home' => '/news.{_format}',
  'user_news' => '/news/{slug}.{_format}',
  'user_news_view' => '/news/view/{slug}',
  'user_news_newcomment' => '/news/new-comment/{post_id}',
  'user_feedback_home' => '/feedback',
  'user_feedback' => '/feedback/{order_by}/{status}/{slug}',
  'user_feedback_new' => '/feedback/new-feedback',
  'user_feedback_view' => '/feedback/view/{slug}',
  'user_feedback_newfeedback_finishlogin' => '/feedback/new-feedback/login-finished/{feedback_id}',
  'user_feedback_newcomment' => '/feedback/new-comment/{feedback_id}',
  'user_feedback_vote' => '/feedback/vote/{feedback_id}',
  'user_chat_initsession' => '/chat/chat-session',
  'user_chat_widgetisavail' => '/dp.php/chat/is-available.js',
  'user_chat_poll' => '/chat/poll/{session_code}',
  'user_chat_sendmessage' => '/chat/send-message/{session_code}',
  'user_chat_sendmessage_attach' => '/chat/send-attach/{session_code}',
  'user_chat_sendusertyping' => '/chat/user-typing/{session_code}',
  'user_chat_chatended' => '/chat/chat-finished/{session_code}',
  'user_chat_chatended_feedback' => '/chat/chat-finished-feedback/{session_code}',
  'user_chatlogs' => '/chat-logs',
  'user_chatlogs_view' => '/chat-logs/{conversation_id}',
  'user_widget_overlay' => '/widget/overlay.html',
  'user_widget_newticket' => '/widget/new-ticket.json',
  'user_widget_newfeedback' => '/widget/new-feedback.json',
  'user_widget_chat' => '/widget/chat.html',
  'user_long_tweet_view' => '/long-tweet/{long_id}',
  'user_plugins_run' => '/ext-plugins/{plugin_id}/run/{action}',
  'report_login' => '/reports/login',
  'report_logout' => '/reports/logout/{auth}',
  'report_login_authenticate_local' => '/reports/login/authenticate-password',
  'report' => '/reports/',
  'report_overview_update_stat' => '/reports/overview/update-stat/{type}',
  'report_agent_hours_index' => '/reports/agent-hours',
  'report_agent_hours_list_date' => '/reports/agent-hours/{date}/{date2}',
  'report_agent_activity_index' => '/reports/agent-activity',
  'report_agent_activity_list' => '/reports/agent-activity/list/{agent_or_team_id}/{date}',
  'report_agent_feedback_summary' => '/reports/agent-feedback/summary/{date}',
  'report_agent_feedback_feed' => '/reports/agent-feedback/{page}',
  'report_publish' => '/reports/publish',
  'report_builder' => '/reports/report-builder',
  'report_builder_query' => '/reports/report-builder/query',
  'report_builder_parse' => '/reports/report-builder/parse',
  'report_builder_new' => '/reports/report-builder/new',
  'report_builder_report' => '/reports/report-builder/{report_builder_id}/',
  'report_builder_edit' => '/reports/report-builder/{report_builder_id}/edit',
  'report_builder_delete' => '/reports/report-builder/{report_builder_id}/delete',
  'report_builder_favorite' => '/reports/report-builder/{report_builder_id}/favorite',
  'report_billing' => '/reports/billing',
  'report_billing_report' => '/reports/billing/{report_id}',
);
static private $routeFragments = array (
);
static private $fragmentNames = array (
);
static private $fragmentTypes = array (
);

    static private $declaredRouteNames = array(
       'proxy' => true,
       'serve_blob' => true,
       'serve_blob_size' => true,
       'serve_blob_sizefit' => true,
       'serve_person_picture' => true,
       'serve_person_picture_size' => true,
       'serve_default_picture' => true,
       'favicon' => true,
       'serve_org_picture_default' => true,
       'serve_org_picture' => true,
       'sys_log_js_error' => true,
       'sys_report_error' => true,
       'data_interface_data' => true,
       'dp3_redirect_files_php' => true,
       'dp3_redirect_attachment_files_php' => true,
       'dp3_redirect_ideas_php' => true,
       'dp3_redirect_kb_article_php' => true,
       'dp3_redirect_kb_cat_php' => true,
       'dp3_redirect_kb_php' => true,
       'dp3_redirect_login_php' => true,
       'dp3_redirect_manual_php' => true,
       'dp3_redirect_manual_rewritten' => true,
       'dp3_redirect_manual_download_php' => true,
       'dp3_redirect_news_archive_php' => true,
       'dp3_redirect_news_full_php' => true,
       'dp3_redirect_news_php' => true,
       'dp3_redirect_newticket_php' => true,
       'dp3_redirect_profile_email_php' => true,
       'dp3_redirect_profile_password_php' => true,
       'dp3_redirect_profile_php' => true,
       'dp3_redirect_register_php' => true,
       'dp3_redirect_reset_php' => true,
       'dp3_redirect_ticketlist_php' => true,
       'dp3_redirect_ticketlist_company_php' => true,
       'dp3_redirect_ticketlist_participate_php' => true,
       'dp3_redirect_troubleshooter_php' => true,
       'dp3_redirect_view_php' => true,
       'user' => true,
       'user_admin_rendertpl' => true,
       'user_comment_form_login_partial' => true,
       'user_test' => true,
       'user_saverating' => true,
       'user_newcomment_finishlogin' => true,
       'user_accept_upload' => true,
       'user_validate_email' => true,
       'user_validate_ticket' => true,
       'user_jstell_login' => true,
       'user_login' => true,
       'user_login_inline' => true,
       'user_login_usersource_sso' => true,
       'user_logout' => true,
       'user_login_authenticate_local' => true,
       'user_login_authenticate' => true,
       'user_login_callback' => true,
       'user_login_resetpass' => true,
       'user_login_resetpass_send' => true,
       'user_login_resetpass_newpass' => true,
       'user_login_agentlogin' => true,
       'user_register' => true,
       'user_profile' => true,
       'user_profile_setlang' => true,
       'user_profile_associate_twitter' => true,
       'user_profile_twitter_remove' => true,
       'user_profile_changepassword' => true,
       'user_profile_emails_new' => true,
       'user_profile_emails_remove' => true,
       'user_profile_emails_validate_remove' => true,
       'user_profile_emails_validate_sendlink' => true,
       'user_profile_emails_setdefault' => true,
       'user_search' => true,
       'user_search_labels' => true,
       'user_search_omnisearch' => true,
       'user_search_similarto' => true,
       'user_tickets' => true,
       'user_tickets_organization' => true,
       'user_tickets_new' => true,
       'user_tickets_new_finishlogin' => true,
       'user_tickets_new_simple' => true,
       'user_tickets_new_savestatus' => true,
       'user_tickets_new_contentsolved_save' => true,
       'user_tickets_new_contentsolved' => true,
       'user_tickets_new_thanks' => true,
       'user_tickets_new_thanks_simple' => true,
       'user_tickets_view' => true,
       'user_tickets_addreply' => true,
       'user_tickets_participants' => true,
       'user_tickets_participants_add' => true,
       'user_tickets_participants_remove' => true,
       'user_tickets_resolve' => true,
       'user_tickets_unresolve' => true,
       'user_tickets_feedback' => true,
       'user_tickets_feedback_save' => true,
       'user_tickets_feedback_closeticket' => true,
       'user_articles_home' => true,
       'user_articles' => true,
       'user_articles_article' => true,
       'user_articles_article_togglesub' => true,
       'user_articles_cat_togglesub' => true,
       'user_articles_unsub_all' => true,
       'user_articles_article_agent_iframe' => true,
       'user_articles_newcomment' => true,
       'user_downloads_home' => true,
       'user_downloads' => true,
       'user_downloads_file' => true,
       'user_downloads_file_download' => true,
       'user_downloads_newcomment' => true,
       'user_news_home' => true,
       'user_news' => true,
       'user_news_view' => true,
       'user_news_newcomment' => true,
       'user_feedback_home' => true,
       'user_feedback' => true,
       'user_feedback_new' => true,
       'user_feedback_view' => true,
       'user_feedback_newfeedback_finishlogin' => true,
       'user_feedback_newcomment' => true,
       'user_feedback_vote' => true,
       'user_chat_initsession' => true,
       'user_chat_widgetisavail' => true,
       'user_chat_poll' => true,
       'user_chat_sendmessage' => true,
       'user_chat_sendmessage_attach' => true,
       'user_chat_sendusertyping' => true,
       'user_chat_chatended' => true,
       'user_chat_chatended_feedback' => true,
       'user_chatlogs' => true,
       'user_chatlogs_view' => true,
       'user_widget_overlay' => true,
       'user_widget_newticket' => true,
       'user_widget_newfeedback' => true,
       'user_widget_chat' => true,
       'user_long_tweet_view' => true,
       'user_plugins_run' => true,
       'report_login' => true,
       'report_logout' => true,
       'report_login_authenticate_local' => true,
       'report' => true,
       'report_overview_update_stat' => true,
       'report_agent_hours_index' => true,
       'report_agent_hours_list_date' => true,
       'report_agent_activity_index' => true,
       'report_agent_activity_list' => true,
       'report_agent_feedback_summary' => true,
       'report_agent_feedback_feed' => true,
       'report_publish' => true,
       'report_builder' => true,
       'report_builder_query' => true,
       'report_builder_parse' => true,
       'report_builder_new' => true,
       'report_builder_report' => true,
       'report_builder_edit' => true,
       'report_builder_delete' => true,
       'report_builder_favorite' => true,
       'report_billing' => true,
       'report_billing_report' => true,
    );

    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->setContext($context);
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

    private function getproxyRouteInfo()
    {
        return array(array (  0 => 'key',), array (  '_controller' => 'Application\\DeskPRO\\Controller\\WidgetController::proxyAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'key',  ),  1 =>   array (    0 => 'text',    1 => '/proxy',  ),));
    }

    private function getserve_blobRouteInfo()
    {
        return array(array (  0 => 'blob_auth_id',  1 => 'filename',), array (  '_controller' => '(see: serve_file.php)',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'filename',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'blob_auth_id',  ),  2 =>   array (    0 => 'text',    1 => '/file.php',  ),));
    }

    private function getserve_blob_sizeRouteInfo()
    {
        return array(array (  0 => 's',  1 => 'blob_auth_id',  2 => 'filename',), array (  '_controller' => '(see: serve_file.php)',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'filename',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'blob_auth_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 's',  ),  3 =>   array (    0 => 'text',    1 => '/file.php/size',  ),));
    }

    private function getserve_blob_sizefitRouteInfo()
    {
        return array(array (  0 => 's',  1 => 'blob_auth_id',  2 => 'filename',), array (  '_controller' => '(see: serve_file.php)',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'filename',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'blob_auth_id',  ),  2 =>   array (    0 => 'text',    1 => '/size-fit',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 's',  ),  4 =>   array (    0 => 'text',    1 => '/file.php/size',  ),));
    }

    private function getserve_person_pictureRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => '(see: serve_file.php)',  'size' => 0,), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/file.php/avatar',  ),));
    }

    private function getserve_person_picture_sizeRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => '(see: serve_file.php)',), array (  'person_id' => '\\d+',  'size' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/file.php/avatar',  ),));
    }

    private function getserve_default_pictureRouteInfo()
    {
        return array(array (  0 => 's',), array (  '_controller' => '(see: serve_file.php)',  'name' => 'default_picture',  's' => '0',), array (), array (  0 =>   array (    0 => 'text',    1 => '/default.jpg',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 's',  ),  2 =>   array (    0 => 'text',    1 => '/file.php/avatar',  ),));
    }

    private function getfaviconRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\BlobController::faviconAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/favicon.ico',  ),));
    }

    private function getserve_org_picture_defaultRouteInfo()
    {
        return array(array (), array (  '_controller' => '(see: serve_file.php)',), array (), array (  0 =>   array (    0 => 'text',    1 => '/file.php/o-avatar/default',  ),));
    }

    private function getserve_org_pictureRouteInfo()
    {
        return array(array (  0 => 'org_id',), array (  '_controller' => '(see: serve_file.php)',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'org_id',  ),  1 =>   array (    0 => 'text',    1 => '/file.php/o-avatar',  ),));
    }

    private function getsys_log_js_errorRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\DataController::logJsErrorAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/dp/log-js-error.json',  ),));
    }

    private function getsys_report_errorRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\DataController::sendErrorReportAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/dp/report-error.json',  ),));
    }

    private function getdata_interface_dataRouteInfo()
    {
        return array(array (  0 => '_format',), array (  '_controller' => 'Application\\DeskPRO\\Controller\\DataController::interfaceDataAction',  '_format' => 'js',), array (  '_format' => 'js',), array (  0 =>   array (    0 => 'variable',    1 => '.',    2 => 'js',    3 => '_format',  ),  1 =>   array (    0 => 'text',    1 => '/data/interface-data',  ),));
    }

    private function getdp3_redirect_files_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::downloadCatAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/files.php',  ),));
    }

    private function getdp3_redirect_attachment_files_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::downloadViewAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/attachment_files.php',  ),));
    }

    private function getdp3_redirect_ideas_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::feedbackAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/ideas.php',  ),));
    }

    private function getdp3_redirect_kb_article_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::articleViewAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/kb_article.php',  ),));
    }

    private function getdp3_redirect_kb_cat_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::articleCatAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/kb_cat.php',  ),));
    }

    private function getdp3_redirect_kb_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::articlesHomeAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/kb.php',  ),));
    }

    private function getdp3_redirect_login_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::loginAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/login.php',  ),));
    }

    private function getdp3_redirect_manual_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::manualsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/manual.php',  ),));
    }

    private function getdp3_redirect_manual_rewrittenRouteInfo()
    {
        return array(array (  0 => 'manual_bit',  1 => 'page_bit',), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::rewrittenManualsAction',  'page_bit' => '',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'page_bit',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'manual_bit',  ),  2 =>   array (    0 => 'text',    1 => '/manual',  ),));
    }

    private function getdp3_redirect_manual_download_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::manualsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/manual_download.php',  ),));
    }

    private function getdp3_redirect_news_archive_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::newsArchiveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/news_archive.php',  ),));
    }

    private function getdp3_redirect_news_full_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::newsViewAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/news_full.php',  ),));
    }

    private function getdp3_redirect_news_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::newsViewAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/news.php',  ),));
    }

    private function getdp3_redirect_newticket_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::newTicketAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/newticket.php',  ),));
    }

    private function getdp3_redirect_profile_email_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::profileAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/profile_email.php',  ),));
    }

    private function getdp3_redirect_profile_password_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::profileAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/profile_password.php',  ),));
    }

    private function getdp3_redirect_profile_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::profileAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/profile.php',  ),));
    }

    private function getdp3_redirect_register_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::registerAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/register.php',  ),));
    }

    private function getdp3_redirect_reset_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::loginAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reset.php',  ),));
    }

    private function getdp3_redirect_ticketlist_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::ticketListAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/ticketlist.php',  ),));
    }

    private function getdp3_redirect_ticketlist_company_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::ticketListAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/ticketlist_company.php',  ),));
    }

    private function getdp3_redirect_ticketlist_participate_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::ticketListAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/ticketlist_participate.php',  ),));
    }

    private function getdp3_redirect_troubleshooter_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::troublesAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/troubleshooter.php',  ),));
    }

    private function getdp3_redirect_view_phpRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\DeskPRO\\Controller\\Deskpro3RedirectController::ticketViewAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/view.php',  ),));
    }

    private function getuserRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\PortalController::portalAction',  '_locale' => 'en',), array (), array (  0 =>   array (    0 => 'text',    1 => '/',  ),));
    }

    private function getuser_admin_rendertplRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::adminRenderTemplateAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  1 =>   array (    0 => 'text',    1 => '/admin-render-template',  ),));
    }

    private function getuser_comment_form_login_partialRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::commentFormLoginPartialAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/_misc/comment-form-login-partial',  ),));
    }

    private function getuser_testRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\TestController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/test',  ),));
    }

    private function getuser_saveratingRouteInfo()
    {
        return array(array (  0 => 'object_type',  1 => 'object_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\PortalController::saveRatingAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'object_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'object_type',  ),  2 =>   array (    0 => 'text',    1 => '/portal/save-rating',  ),));
    }

    private function getuser_newcomment_finishloginRouteInfo()
    {
        return array(array (  0 => 'comment_type',  1 => 'comment_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\PortalController::newCommentFinishLoginAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'comment_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'comment_type',  ),  2 =>   array (    0 => 'text',    1 => '/portal/save-comment/login-finished',  ),));
    }

    private function getuser_accept_uploadRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::acceptTempUploadAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/accept-temp-upload',  ),));
    }

    private function getuser_validate_emailRouteInfo()
    {
        return array(array (  0 => 'id',  1 => 'auth',), array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::validateEmailAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'auth',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/validate-email',  ),));
    }

    private function getuser_validate_ticketRouteInfo()
    {
        return array(array (  0 => 'access_code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::validateTicketEmailAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'access_code',  ),  1 =>   array (    0 => 'text',    1 => '/validate-ticket-email',  ),));
    }

    private function getuser_jstell_loginRouteInfo()
    {
        return array(array (  0 => 'jstell',  1 => 'security_token',  2 => 'usersource_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::jstellLoginAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'usersource_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'security_token',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'jstell',  ),  3 =>   array (    0 => 'text',    1 => '/login/jstell',  ),));
    }

    private function getuser_loginRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/login',  ),));
    }

    private function getuser_login_inlineRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::inlineLoginAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/login/inline-login',  ),));
    }

    private function getuser_login_usersource_ssoRouteInfo()
    {
        return array(array (  0 => 'usersource_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::usersourceSsoAction',), array (  'usersource_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'usersource_id',  ),  1 =>   array (    0 => 'text',    1 => '/login/usersource-sso',  ),));
    }

    private function getuser_logoutRouteInfo()
    {
        return array(array (  0 => 'auth',), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::logoutAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'auth',  ),  1 =>   array (    0 => 'text',    1 => '/logout',  ),));
    }

    private function getuser_login_authenticate_localRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::authenticateLocalAction',  'usersource_id' => 0,), array (), array (  0 =>   array (    0 => 'text',    1 => '/login/authenticate-password',  ),));
    }

    private function getuser_login_authenticateRouteInfo()
    {
        return array(array (  0 => 'usersource_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::authenticateAction',  'usersource_id' => 0,), array (  'usersource_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'usersource_id',  ),  1 =>   array (    0 => 'text',    1 => '/login/authenticate',  ),));
    }

    private function getuser_login_callbackRouteInfo()
    {
        return array(array (  0 => 'usersource_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::authenticateCallbackAction',), array (  'usersource_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'usersource_id',  ),  1 =>   array (    0 => 'text',    1 => '/login/authenticate-callback',  ),));
    }

    private function getuser_login_resetpassRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::resetPasswordAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/login/reset-password',  ),));
    }

    private function getuser_login_resetpass_sendRouteInfo()
    {
        return array(array (  0 => '_format',), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::sendResetPasswordAction',  '_format' => 'html',), array (), array (  0 =>   array (    0 => 'variable',    1 => '.',    2 => '[^/]+?',    3 => '_format',  ),  1 =>   array (    0 => 'text',    1 => '/login/reset-password/send',  ),));
    }

    private function getuser_login_resetpass_newpassRouteInfo()
    {
        return array(array (  0 => 'code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::resetPasswordNewPassAction',), array (  'code' => '[A-Za-z0-9\\-]{17,}',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[A-Za-z0-9\\-]{17,}',    3 => 'code',  ),  1 =>   array (    0 => 'text',    1 => '/login/reset-password',  ),));
    }

    private function getuser_login_agentloginRouteInfo()
    {
        return array(array (  0 => 'code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\LoginController::authAgentLoginAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'code',  ),  1 =>   array (    0 => 'text',    1 => '/login/agent-login',  ),));
    }

    private function getuser_registerRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\RegisterController::registerAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/register',  ),));
    }

    private function getuser_profileRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/profile',  ),));
    }

    private function getuser_profile_setlangRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\MainController::quickSetLanguageAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/profile/quick-set-language',  ),));
    }

    private function getuser_profile_associate_twitterRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::associateTwitterAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/profile/associate-twitter',  ),));
    }

    private function getuser_profile_twitter_removeRouteInfo()
    {
        return array(array (  0 => 'account_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::removeTwitterAction',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/remove',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  2 =>   array (    0 => 'text',    1 => '/profile/twitter',  ),));
    }

    private function getuser_profile_changepasswordRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::changePasswordAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/profile/change-password',  ),));
    }

    private function getuser_profile_emails_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::newEmailAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/profile/emails/new',  ),));
    }

    private function getuser_profile_emails_removeRouteInfo()
    {
        return array(array (  0 => 'email_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::removeEmailAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/remove',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'email_id',  ),  2 =>   array (    0 => 'text',    1 => '/profile/emails',  ),));
    }

    private function getuser_profile_emails_validate_removeRouteInfo()
    {
        return array(array (  0 => 'email_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::removeEmailValidatingAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/remove-validating',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'email_id',  ),  2 =>   array (    0 => 'text',    1 => '/profile/emails',  ),));
    }

    private function getuser_profile_emails_validate_sendlinkRouteInfo()
    {
        return array(array (  0 => 'email_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::sendValidateEmailLinkAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/validate/send-link',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'email_id',  ),  2 =>   array (    0 => 'text',    1 => '/profile/emails',  ),));
    }

    private function getuser_profile_emails_setdefaultRouteInfo()
    {
        return array(array (  0 => 'email_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ProfileController::setDefaultEmailAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/set-default',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'email_id',  ),  2 =>   array (    0 => 'text',    1 => '/profile/emails',  ),));
    }

    private function getuser_searchRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\SearchController::searchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/search',  ),));
    }

    private function getuser_search_labelsRouteInfo()
    {
        return array(array (  0 => 'type',  1 => 'label',), array (  '_controller' => 'Application\\UserBundle\\Controller\\SearchController::labelSearchAction',  'label' => '',  'type' => 'all',), array (  'label' => '.*',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '.*',    3 => 'label',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/search/labels',  ),));
    }

    private function getuser_search_omnisearchRouteInfo()
    {
        return array(array (  0 => 'query',), array (  '_controller' => 'Application\\UserBundle\\Controller\\SearchController::omnisearchAction',), array (  'query' => '.+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '.+',    3 => 'query',  ),  1 =>   array (    0 => 'text',    1 => '/search/omnisearch',  ),));
    }

    private function getuser_search_similartoRouteInfo()
    {
        return array(array (  0 => 'content_type',), array (  '_controller' => 'Application\\UserBundle\\Controller\\SearchController::similarToAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'content_type',  ),  1 =>   array (    0 => 'text',    1 => '/search/similar-to',  ),));
    }

    private function getuser_ticketsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/tickets',  ),));
    }

    private function getuser_tickets_organizationRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::listOrganizationAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/tickets/organization',  ),));
    }

    private function getuser_tickets_newRouteInfo()
    {
        return array(array (  0 => 'for_department_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::newAction',  'format' => 'normal',  'for_department_id' => 0,), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'for_department_id',  ),  1 =>   array (    0 => 'text',    1 => '/new-ticket',  ),));
    }

    private function getuser_tickets_new_finishloginRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::newFinishLoginAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_id',  ),  1 =>   array (    0 => 'text',    1 => '/new-ticket/login-finish',  ),));
    }

    private function getuser_tickets_new_simpleRouteInfo()
    {
        return array(array (  0 => 'for_department_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::newAction',  'format' => 'iframe',  'for_department_id' => 0,), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'for_department_id',  ),  1 =>   array (    0 => 'text',    1 => '/tickets/new-simple',  ),));
    }

    private function getuser_tickets_new_savestatusRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::saveStatusAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/tickets/new/save-status',  ),));
    }

    private function getuser_tickets_new_contentsolved_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::contentSolvedSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/tickets/new/content-solved-save.json',  ),));
    }

    private function getuser_tickets_new_contentsolvedRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::contentSolvedRedirectAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/tickets/new/content-solved-redirect',  ),));
    }

    private function getuser_tickets_new_thanksRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::thanksAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  1 =>   array (    0 => 'text',    1 => '/tickets/new/thanks',  ),));
    }

    private function getuser_tickets_new_thanks_simpleRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewTicketController::simpleThanksAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  1 =>   array (    0 => 'text',    1 => '/tickets/new/thanks-simple',  ),));
    }

    private function getuser_tickets_viewRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketViewController::loadAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  1 =>   array (    0 => 'text',    1 => '/ticket',  ),));
    }

    private function getuser_tickets_addreplyRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::addReplyAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/add-reply',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  2 =>   array (    0 => 'text',    1 => '/ticket-edit',  ),));
    }

    private function getuser_tickets_participantsRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::manageParticipantsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/people',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  2 =>   array (    0 => 'text',    1 => '/ticket-edit',  ),));
    }

    private function getuser_tickets_participants_addRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::addParticipantAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/people/add',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  2 =>   array (    0 => 'text',    1 => '/ticket-edit',  ),));
    }

    private function getuser_tickets_participants_removeRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',  1 => 'person_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::removeParticipantAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/people/remove',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  3 =>   array (    0 => 'text',    1 => '/ticket-edit',  ),));
    }

    private function getuser_tickets_resolveRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::resolveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/resolve',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  2 =>   array (    0 => 'text',    1 => '/ticket-edit',  ),));
    }

    private function getuser_tickets_unresolveRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::unresolveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/unresolve',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  2 =>   array (    0 => 'text',    1 => '/ticket-edit',  ),));
    }

    private function getuser_tickets_feedbackRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',  1 => 'auth',  2 => 'message_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::feedbackAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'auth',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  3 =>   array (    0 => 'text',    1 => '/ticket-rate',  ),));
    }

    private function getuser_tickets_feedback_saveRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',  1 => 'auth',  2 => 'message_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::feedbackSaveAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'auth',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  4 =>   array (    0 => 'text',    1 => '/ticket-rate',  ),));
    }

    private function getuser_tickets_feedback_closeticketRouteInfo()
    {
        return array(array (  0 => 'ticket_ref',  1 => 'message_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TicketsController::feedbackCloseTicketAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/close-ticket',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/feedback',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_ref',  ),  4 =>   array (    0 => 'text',    1 => '/ticket-edit',  ),));
    }

    private function getuser_articles_homeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::browseAction',  'slug' => '',), array (), array (  0 =>   array (    0 => 'text',    1 => '/kb',  ),));
    }

    private function getuser_articlesRouteInfo()
    {
        return array(array (  0 => 'slug',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::browseAction',  'slug' => '',), array (  'slug' => '(\\d+(\\-.*?)?)?',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '(\\d+(\\-.*?)?)?',    3 => 'slug',  ),  1 =>   array (    0 => 'text',    1 => '/kb',  ),));
    }

    private function getuser_articles_articleRouteInfo()
    {
        return array(array (  0 => 'slug',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::articleAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'slug',  ),  1 =>   array (    0 => 'text',    1 => '/kb/articles',  ),));
    }

    private function getuser_articles_article_togglesubRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'auth',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::articleSubscriptionAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'auth',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/kb/article-subscription',  ),));
    }

    private function getuser_articles_cat_togglesubRouteInfo()
    {
        return array(array (  0 => 'category_id',  1 => 'auth',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::categorySubscriptionAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'auth',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/kb/category-subscription',  ),));
    }

    private function getuser_articles_unsub_allRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'auth',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::unsubscribeAllAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'auth',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/kb/unsubscribe-all',  ),));
    }

    private function getuser_articles_article_agent_iframeRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'agent_session_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::articleAgentIframeAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_session_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/kb/articles/agent-iframe',  ),));
    }

    private function getuser_articles_newcommentRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ArticlesController::newCommentAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'article_id',  ),  1 =>   array (    0 => 'text',    1 => '/kb/new-comment',  ),));
    }

    private function getuser_downloads_homeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::browseAction',  'slug' => '',), array (), array (  0 =>   array (    0 => 'text',    1 => '/downloads',  ),));
    }

    private function getuser_downloadsRouteInfo()
    {
        return array(array (  0 => 'slug',), array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::browseAction',  'slug' => '',), array (  'slug' => '(\\d+(\\-.*?)?)?',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '(\\d+(\\-.*?)?)?',    3 => 'slug',  ),  1 =>   array (    0 => 'text',    1 => '/downloads',  ),));
    }

    private function getuser_downloads_fileRouteInfo()
    {
        return array(array (  0 => 'slug',), array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::fileAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'slug',  ),  1 =>   array (    0 => 'text',    1 => '/downloads/files',  ),));
    }

    private function getuser_downloads_file_downloadRouteInfo()
    {
        return array(array (  0 => 'slug',), array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::downloadFileAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/download',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'slug',  ),  2 =>   array (    0 => 'text',    1 => '/downloads/files',  ),));
    }

    private function getuser_downloads_newcommentRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\DownloadsController::newCommentAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'download_id',  ),  1 =>   array (    0 => 'text',    1 => '/downloads/new-comment',  ),));
    }

    private function getuser_news_homeRouteInfo()
    {
        return array(array (  0 => '_format',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewsController::browseAction',  'slug' => '',  'page' => 1,  '_format' => 'html',), array (  '_format' => '(html|rss)',), array (  0 =>   array (    0 => 'variable',    1 => '.',    2 => '(html|rss)',    3 => '_format',  ),  1 =>   array (    0 => 'text',    1 => '/news',  ),));
    }

    private function getuser_newsRouteInfo()
    {
        return array(array (  0 => 'slug',  1 => '_format',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewsController::browseAction',  'slug' => '',  'page' => 1,  '_format' => 'html',), array (  'slug' => '(\\d+(\\-.*?)?)?',  'page' => '\\d+',  '_format' => '(html|rss)',), array (  0 =>   array (    0 => 'variable',    1 => '.',    2 => '(html|rss)',    3 => '_format',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '(\\d+(\\-.*?)?)?',    3 => 'slug',  ),  2 =>   array (    0 => 'text',    1 => '/news',  ),));
    }

    private function getuser_news_viewRouteInfo()
    {
        return array(array (  0 => 'slug',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewsController::viewAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'slug',  ),  1 =>   array (    0 => 'text',    1 => '/news/view',  ),));
    }

    private function getuser_news_newcommentRouteInfo()
    {
        return array(array (  0 => 'post_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\NewsController::newCommentAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'post_id',  ),  1 =>   array (    0 => 'text',    1 => '/news/new-comment',  ),));
    }

    private function getuser_feedback_homeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::filterAction',  'status' => 'open',  'slug' => 'all-categories',  'order_by' => 'popular',), array (), array (  0 =>   array (    0 => 'text',    1 => '/feedback',  ),));
    }

    private function getuser_feedbackRouteInfo()
    {
        return array(array (  0 => 'order_by',  1 => 'status',  2 => 'slug',), array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::filterAction',  'status' => 'open',  'slug' => 'all-categories',  'order_by' => 'popular',), array (  'slug' => '((\\d+(\\-.*?)?)?)|all\\-categories',  'status' => '(open|any-status|gathering\\-feedback|active|closed)(\\.([0-9]+))?',  'order_by' => '(popular|newest|most\\-voted|i\\-voted)',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '((\\d+(\\-.*?)?)?)|all\\-categories',    3 => 'slug',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '(open|any-status|gathering\\-feedback|active|closed)(\\.([0-9]+))?',    3 => 'status',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '(popular|newest|most\\-voted|i\\-voted)',    3 => 'order_by',  ),  3 =>   array (    0 => 'text',    1 => '/feedback',  ),));
    }

    private function getuser_feedback_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::filterAction',  'just_form' => 1,  'status' => 'any-status',  'slug' => 'all-categories',  'order_by' => 'popular',), array (), array (  0 =>   array (    0 => 'text',    1 => '/feedback/new-feedback',  ),));
    }

    private function getuser_feedback_viewRouteInfo()
    {
        return array(array (  0 => 'slug',), array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::viewAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'slug',  ),  1 =>   array (    0 => 'text',    1 => '/feedback/view',  ),));
    }

    private function getuser_feedback_newfeedback_finishloginRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::newFinishLoginAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  1 =>   array (    0 => 'text',    1 => '/feedback/new-feedback/login-finished',  ),));
    }

    private function getuser_feedback_newcommentRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::newCommentAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  1 =>   array (    0 => 'text',    1 => '/feedback/new-comment',  ),));
    }

    private function getuser_feedback_voteRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\FeedbackController::voteAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  1 =>   array (    0 => 'text',    1 => '/feedback/vote',  ),));
    }

    private function getuser_chat_initsessionRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::chatSessionAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/chat/chat-session',  ),));
    }

    private function getuser_chat_widgetisavailRouteInfo()
    {
        return array(array (), array (  '_controller' => '(see: serve_dp.php)',), array (), array (  0 =>   array (    0 => 'text',    1 => '/dp.php/chat/is-available.js',  ),));
    }

    private function getuser_chat_pollRouteInfo()
    {
        return array(array (  0 => 'session_code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::pollAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'session_code',  ),  1 =>   array (    0 => 'text',    1 => '/chat/poll',  ),));
    }

    private function getuser_chat_sendmessageRouteInfo()
    {
        return array(array (  0 => 'session_code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::sendMessageAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'session_code',  ),  1 =>   array (    0 => 'text',    1 => '/chat/send-message',  ),));
    }

    private function getuser_chat_sendmessage_attachRouteInfo()
    {
        return array(array (  0 => 'session_code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::sendFileAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'session_code',  ),  1 =>   array (    0 => 'text',    1 => '/chat/send-attach',  ),));
    }

    private function getuser_chat_sendusertypingRouteInfo()
    {
        return array(array (  0 => 'session_code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::userTypingAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'session_code',  ),  1 =>   array (    0 => 'text',    1 => '/chat/user-typing',  ),));
    }

    private function getuser_chat_chatendedRouteInfo()
    {
        return array(array (  0 => 'session_code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::chatEndedAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'session_code',  ),  1 =>   array (    0 => 'text',    1 => '/chat/chat-finished',  ),));
    }

    private function getuser_chat_chatended_feedbackRouteInfo()
    {
        return array(array (  0 => 'session_code',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatController::chatEndedFeedbackAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'session_code',  ),  1 =>   array (    0 => 'text',    1 => '/chat/chat-finished-feedback',  ),));
    }

    private function getuser_chatlogsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatLogController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/chat-logs',  ),));
    }

    private function getuser_chatlogs_viewRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\ChatLogController::viewAction',), array (  'conversation_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/chat-logs',  ),));
    }

    private function getuser_widget_overlayRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\WidgetController::overlayAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/widget/overlay.html',  ),));
    }

    private function getuser_widget_newticketRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\WidgetController::newTicketAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/widget/new-ticket.json',  ),));
    }

    private function getuser_widget_newfeedbackRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\WidgetController::newFeedbackAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/widget/new-feedback.json',  ),));
    }

    private function getuser_widget_chatRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\UserBundle\\Controller\\WidgetController::chatAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/widget/chat.html',  ),));
    }

    private function getuser_long_tweet_viewRouteInfo()
    {
        return array(array (  0 => 'long_id',), array (  '_controller' => 'Application\\UserBundle\\Controller\\TwitterController::viewLongAction',), array (  'long_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'long_id',  ),  1 =>   array (    0 => 'text',    1 => '/long-tweet',  ),));
    }

    private function getuser_plugins_runRouteInfo()
    {
        return array(array (  0 => 'plugin_id',  1 => 'action',), array (  '_controller' => 'Application\\UserBundle\\Controller\\PluginsController::runAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'action',  ),  1 =>   array (    0 => 'text',    1 => '/run',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'plugin_id',  ),  3 =>   array (    0 => 'text',    1 => '/ext-plugins',  ),));
    }

    private function getreport_loginRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\LoginController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/login',  ),));
    }

    private function getreport_logoutRouteInfo()
    {
        return array(array (  0 => 'auth',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\LoginController::logoutAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'auth',  ),  1 =>   array (    0 => 'text',    1 => '/reports/logout',  ),));
    }

    private function getreport_login_authenticate_localRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\LoginController::authenticateLocalAction',  'usersource_id' => 0,), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/login/authenticate-password',  ),));
    }

    private function getreportRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\OverviewController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/',  ),));
    }

    private function getreport_overview_update_statRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\OverviewController::updateStatAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  1 =>   array (    0 => 'text',    1 => '/reports/overview/update-stat',  ),));
    }

    private function getreport_agent_hours_indexRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentHoursController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/agent-hours',  ),));
    }

    private function getreport_agent_hours_list_dateRouteInfo()
    {
        return array(array (  0 => 'date',  1 => 'date2',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentHoursController::listAction',  'date2' => '',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'date2',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'date',  ),  2 =>   array (    0 => 'text',    1 => '/reports/agent-hours',  ),));
    }

    private function getreport_agent_activity_indexRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentActivityController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/agent-activity',  ),));
    }

    private function getreport_agent_activity_listRouteInfo()
    {
        return array(array (  0 => 'agent_or_team_id',  1 => 'date',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentActivityController::listAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'date',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_or_team_id',  ),  2 =>   array (    0 => 'text',    1 => '/reports/agent-activity/list',  ),));
    }

    private function getreport_agent_feedback_summaryRouteInfo()
    {
        return array(array (  0 => 'date',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentFeedbackController::summaryAction',  'date' => '',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'date',  ),  1 =>   array (    0 => 'text',    1 => '/reports/agent-feedback/summary',  ),));
    }

    private function getreport_agent_feedback_feedRouteInfo()
    {
        return array(array (  0 => 'page',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\AgentFeedbackController::feedAction',  'page' => '0',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'page',  ),  1 =>   array (    0 => 'text',    1 => '/reports/agent-feedback',  ),));
    }

    private function getreport_publishRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/publish',  ),));
    }

    private function getreport_builderRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/report-builder',  ),));
    }

    private function getreport_builder_queryRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::queryAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/report-builder/query',  ),));
    }

    private function getreport_builder_parseRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::parseAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/report-builder/parse',  ),));
    }

    private function getreport_builder_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::editAction',  'report_builder_id' => 0,), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/report-builder/new',  ),));
    }

    private function getreport_builder_reportRouteInfo()
    {
        return array(array (  0 => 'report_builder_id',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::reportAction',), array (  'report_builder_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'report_builder_id',  ),  2 =>   array (    0 => 'text',    1 => '/reports/report-builder',  ),));
    }

    private function getreport_builder_editRouteInfo()
    {
        return array(array (  0 => 'report_builder_id',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::editAction',), array (  'report_builder_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/edit',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'report_builder_id',  ),  2 =>   array (    0 => 'text',    1 => '/reports/report-builder',  ),));
    }

    private function getreport_builder_deleteRouteInfo()
    {
        return array(array (  0 => 'report_builder_id',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::deleteAction',), array (  'report_builder_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/delete',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'report_builder_id',  ),  2 =>   array (    0 => 'text',    1 => '/reports/report-builder',  ),));
    }

    private function getreport_builder_favoriteRouteInfo()
    {
        return array(array (  0 => 'report_builder_id',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\ReportBuilderController::favoriteAction',), array (  'report_builder_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/favorite',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'report_builder_id',  ),  2 =>   array (    0 => 'text',    1 => '/reports/report-builder',  ),));
    }

    private function getreport_billingRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ReportBundle\\Controller\\BillingController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reports/billing',  ),));
    }

    private function getreport_billing_reportRouteInfo()
    {
        return array(array (  0 => 'report_id',), array (  '_controller' => 'Application\\ReportBundle\\Controller\\BillingController::reportAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'report_id',  ),  1 =>   array (    0 => 'text',    1 => '/reports/billing',  ),));
    }

	public function getRoutePattern($route_name)
	{
		return isset(self::$routePatterns[$route_name]) ? self::$routePatterns[$route_name] : null;
	}

	public function getRoutePatterns()
	{
		return self::$routePatterns;
	}

	public function getFragmentNames()
	{
		return array_keys(self::$fragmentNames);
	}

	public function getTypeForFragment($fragment_name)
	{
		return isset(self::$fragmentTypes[$fragment_name]) ? self::$fragmentTypes[$fragment_name] : null;
	}

	public function getRouteForFragment($fragment_name)
	{
		return isset(self::$fragmentNames[$fragment_name]) ? self::$fragmentNames[$fragment_name] : null;
	}

	public function getPatternForFragment($fragment_name)
	{
		$route_name = $this->getRouteForFragment($fragment_name);
		if (!$route_name) return null;

		return $this->getRoutePattern($route_name);
	}

	public function getFragmentPatternMap()
	{
		$map = array();
		foreach ($this->getFragmentNames() as $fragment_name) {
			$map[$fragment_name] = $this->getPatternForFragment($fragment_name);
		}

		return $map;
	}

	public function getFragmentInforArray()
	{
		$map = array();
		foreach ($this->getFragmentNames() as $fragment_name) {
			$map[$fragment_name] = array(
				'pattern' => $this->getPatternForFragment($fragment_name),
				'type'    => $this->getTypeForFragment($fragment_name),
			);
		}

		return $map;
	}

	public function getFragmentForRoute($route_name)
	{
		return isset(self::$routeFragments[$route_name]) ? self::$routeFragments[$route_name] : null;
	}

	public function generateFragment($route_name, $parameters = array())
	{
		$fragment_name = $this->getFragmentForRoute($route_name);
		if ($fragment_name === null) {
			throw new \InvalidArgumentException(sprintf('Fragment "%s" does not exist.', $route_name));
		}

		if ($parameters) {
			$fragment = $fragment_name . ':' . implode(':', $parameters);
		} else {
			$fragment = $fragment_name;
		}

		return $fragment;
	}
}