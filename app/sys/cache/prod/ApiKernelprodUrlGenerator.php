<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;


/**
 * ApiKernelprodUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class ApiKernelprodUrlGenerator extends Application\DeskPRO\Routing\Generator\UrlGenerator
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
  'agent' => '/agent/',
  'agent_savedom' => '/agent/save-dom.json',
  'agent_combined_sectiondata' => '/agent/get-combined-section-data.json',
  'agent_load_recent_tabs' => '/agent/ui/load-recent-tabs.json',
  'agent_accept_upload' => '/agent/misc/accept-upload',
  'agent_accept_redactor_image_upload' => '/agent/misc/accept-redactor-image-upload',
  'agent_redactor_autosave' => '/agent/misc/redactor-autosave/{content_type}/{content_id}',
  'agent_submit_deskpro_feedback' => '/agent/misc/submit-deskpro-feedback.json',
  'agent_parse_vcard' => '/agent/misc/parse-vcard',
  'agent_get_server_time' => '/agent/misc/get-server-time',
  'agent_ajax_save_prefs' => '/agent/misc/ajax-save-prefs',
  'agent_ajax_labels_autocomplete' => '/agent/misc/ajax-labels/{label_type}',
  'agent_interface_data_js' => '/agent/misc/interface-data.js',
  'agent_dismiss_help_message' => '/agent/misc/dismiss-help-message/{id}',
  'agent_set_agent_status' => '/agent/misc/set-agent-status/{status}',
  'agent_proxy' => '/agent/misc/proxy',
  'agent_load_version_notice' => '/agent/misc/version-notices/{id}/log.html',
  'agent_dismiss_version_notice' => '/agent/misc/version-notices/{id}/dismiss.json',
  'agent_redirect_out' => '/agent/redirect-out/{url}',
  'agent_redirect_out_info' => '/agent/redirect-out-info/{url}',
  'agent_password_confirm_code' => '/agent/password-confirm-code.json',
  'agent_quicksearch' => '/agent/quick-search.json',
  'agent_search' => '/agent/search/search',
  'agent_search_json' => '/agent/search/search.json',
  'agent_recyclebin' => '/agent/recycle-bin',
  'agent_recyclebin_more' => '/agent/recycle-bin/{type}/{page}',
  'agent_login_preload_sources' => '/agent/login/preload-sources',
  'agent_browser_requirements' => '/agent/browser-requirements',
  'agent_browser_requirements_ie_compat' => '/agent/browser-requirements/ie-compat-mode',
  'agent_login' => '/agent/login',
  'agent_login_authenticate_local' => '/agent/login/authenticate-password',
  'agent_login_adminlogin' => '/agent/login/admin-login/{code}',
  'agent_send_lost' => '/agent/login/send-lost.json',
  'agent_settings' => '/agent/settings',
  'agent_settings_profile_save' => '/agent/settings/profile/save.json',
  'agent_settings_profile_savewelcome' => '/agent/settings/profile/save-welcome.json',
  'agent_settings_signature' => '/agent/settings/signature',
  'agent_settings_signature_save' => '/agent/settings/signature/save.json',
  'agent_settings_profile_updatetimezone' => '/agent/settings/profile/update-timezone.json',
  'agent_settings_ticketnotif' => '/agent/settings/ticket-notifications',
  'agent_settings_ticketnotif_save' => '/agent/settings/ticket-notifications/save.json',
  'agent_settings_othernotif' => '/agent/settings/other-notifications',
  'agent_settings_othernotif_save' => '/agent/settings/other-notifications/save.json',
  'agent_settings_ticketmacros' => '/agent/settings/ticket-macros',
  'agent_settings_ticketmacros_edit' => '/agent/settings/ticket-macros/{macro_id}/edit',
  'agent_settings_ticketmacros_edit_save' => '/agent/settings/ticket-macros/{macro_id}/save',
  'agent_settings_ticketmacros_new' => '/agent/settings/ticket-macros/new',
  'agent_settings_ticketmacros_del' => '/agent/settings/ticket-macros/{macro_id}/delete',
  'agent_settings_ticketfilters' => '/agent/settings/ticket-filters',
  'agent_settings_ticketfilters_edit' => '/agent/settings/ticket-filters/{filter_id}/edit',
  'agent_settings_ticketfilters_edit_save' => '/agent/settings/ticket-filters/{filter_id}/edit/save',
  'agent_settings_ticketfilters_del' => '/agent/settings/ticket-filters/{filter_id}/delete',
  'agent_settings_ticketfilters_new' => '/agent/settings/ticket-filters/new-filter',
  'agent_settings_ticketslas' => '/agent/settings/ticket-slas',
  'agent_people_validate_email' => '/agent/people/validate-email/{id}/{security_token}',
  'agent_people_view' => '/agent/people/{person_id}',
  'agent_people_view_basicjson' => '/agent/people/{person_id}/basic.json',
  'agent_people_viewsession' => '/agent/people/session/{session_id}',
  'agent_people_validate_list' => '/agent/people/validate/list',
  'agent_people_validate_approve' => '/agent/people/validate/approve',
  'agent_people_validate_delete' => '/agent/people/validate/delete',
  'agent_people_new' => '/agent/people/new',
  'agent_people_new_save' => '/agent/people/new/save',
  'agent_people_ajaxsave' => '/agent/people/{person_id}/ajax-save',
  'agent_people_savecontactdata' => '/agent/people/{person_id}/save-contact-data.json',
  'agent_people_unban_email' => '/agent/people/{person_id}/unban-email/{email_id}.json',
  'agent_people_merge_overlay' => '/agent/people/{person_id}/merge-overlay/{other_person_id}',
  'agent_people_merge' => '/agent/people/{person_id}/merge/{other_person_id}',
  'agent_people_delete' => '/agent/people/{person_id}/delete/{security_token}',
  'agent_people_login_as' => '/agent/people/{person_id}/login-as',
  'agent_people_changepicoverlay' => '/agent/people/{person_id}/change-picture-overlay',
  'agent_people_ajaxsave_note' => '/agent/people/{person_id}/ajax-save-note',
  'agent_people_ajaxsave_organization' => '/agent/people/{person_id}/ajax-save-organization',
  'agent_person_ajax_labels_save' => '/agent/person/{person_id}/ajax-save-labels',
  'agent_person_ajaxsavecustomfields' => '/agent/person/{person_id}/ajax-save-custom-fields',
  'agent_peoplesearch_usergroup' => '/agent/people-search/usergroup/{id}',
  'agent_peoplesearch_organization' => '/agent/people-search/organization/{id}',
  'agent_peoplesearch_customfilter' => '/agent/people-search/search/{letter}',
  'agent_peoplesearch_getpage' => '/agent/people-search/get-page',
  'agent_peoplesearch_performquick' => '/agent/people-search/search-quick',
  'agent_peoplesearch_quickfind' => '/agent/people-search/quick-find',
  'agent_peoplesearch_quickfind_search' => '/agent/people-search/quick-find-search.json',
  'agent_peoplesearch_getsectiondata' => '/agent/people/get-section-data.json',
  'agent_peoplesearch_getsectiondata_reloadcounts' => '/agent/people/get-section-data/reload-counts.json',
  'agent_peoplesearch_reload_label_sectiondata' => '/agent/people/get-section-data/labels.json',
  'agent_org_view' => '/agent/organizations/{organization_id}',
  'agent_org_new' => '/agent/organizations/new',
  'agent_org_new_save' => '/agent/organizations/new/save',
  'agent_org_ajaxsave' => '/agent/organizations/{organization_id}/ajax-save',
  'agent_org_domain_assign' => '/agent/organizations/{organization_id}/assign-domain',
  'agent_org_domain_unassign' => '/agent/organizations/{organization_id}/unassign-domain',
  'agent_org_domain_moveusers' => '/agent/organizations/{organization_id}/domain/move-users',
  'agent_org_domain_moveusers_exist' => '/agent/organizations/{organization_id}/domain/reassign-users',
  'agent_org_save_member_pos' => '/agent/organizations/{organization_id}/save-member-pos/{person_id}',
  'agent_org_save_member_manager' => '/agent/organizations/{organization_id}/save-member-manager/{person_id}',
  'agent_org_savecontactdata' => '/agent/organizations/{organization_id}/save-contact-data.json',
  'agent_org_delete' => '/agent/organizations/{organization_id}/delete/{security_token}',
  'agent_org_ajaxsave_note' => '/agent/organizations/{organization_id}/ajax-save-note',
  'agent_org_ajax_labels_save' => '/agent/organizations/{organization_id}/ajax-save-labels',
  'agent_org_ajaxsavecustomfields' => '/agent/organizations/{organization_id}/ajax-save-custom-fields',
  'agent_org_changepicoverlay' => '/agent/organizations/{organization_id}/change-picture-overlay',
  'agent_orgsearch_getpage' => '/agent/organization-search/get-page',
  'agent_orgsearch_customfilter' => '/agent/organization-search/search',
  'agent_orgsearch_quicknamesearch' => '/agent/organization-search/quick-name-search.json',
  'agent_orgsearch_namelookup' => '/agent/organization-search/name-lookup.json',
  'agent_ticketsearch_getsectiondata' => '/agent/ticket-search/get-section-data.json',
  'agent_ticketsearch_getsection_reloadarchive' => '/agent/ticket-search/get-section-data/reload-archive-section',
  'agent_ticketsearch_refreshsectiondata' => '/agent/ticket-search/refresh-section-data/{section}.json',
  'agent_ticketsearch_getlabelssection' => '/agent/ticket-search/get-section/labels',
  'agent_ticketsearch_getfiltercounts' => '/agent/ticket-search/get-filter-counts.json',
  'agent_ticketsearch_getslacounts' => '/agent/ticket-search/get-sla-counts.json',
  'agent_ticketsearch_grouptickets' => '/agent/ticket-search/group-tickets.json',
  'agent_ticketsearch_getpage' => '/agent/ticket-search/get-page',
  'agent_ticketsearch_getflaggedsectiondata' => '/agent/tickets/get-flagged-section-data.json',
  'agent_ticketsearch_runcustomfilter' => '/agent/ticket-search/custom-filter/run',
  'agent_ticketsearch_quicksearch' => '/agent/ticket-search/quick-search',
  'agent_ticketsearch_singleticketrow' => '/agent/ticket-search/single-ticket-row/{content_type}/{content_id}',
  'agent_ticketsearch_runfilter' => '/agent/ticket-search/filter/{filter_id}',
  'agent_ticketsearch_runnamedfilter' => '/agent/ticket-search/filter/{filter_name}',
  'agent_ticketsearch_runsla' => '/agent/ticket-search/sla/{sla_id}/{sla_status}',
  'agent_ticketsearch_ajax_get_macro' => '/agent/ticket-search/ajax-get-macro',
  'agent_ticketsearch_ajax_get_macro_actions' => '/agent/ticket-search/ajax-get-macro-actions',
  'agent_ticketsearch_ajax_save_actions' => '/agent/ticket-search/ajax-save-actions',
  'agent_ticketsearch_ajax_delete_tickets' => '/agent/ticket-search/ajax-delete-tickets',
  'agent_ticketsearch_ajax_release_locks' => '/agent/ticket-search/ajax-release-locks',
  'agent_ticket_new' => '/agent/tickets/new',
  'agent_ticket_new_save' => '/agent/tickets/new/save',
  'agent_ticket_new_getpersonrow' => '/agent/tickets/new/get-person-row/{person_id}',
  'agent_ticket_getmessagetpl' => '/agent/tickets/get-message-template/{id}.json',
  'agent_ticket_update_drafts' => '/agent/tickets/update-drafts',
  'agent_ticket_getmessagetext' => '/agent/tickets/messages/{message_id}/get-message-text.json',
  'agent_ticket_getfullmessage' => '/agent/tickets/messages/{message_id}/get-full-message.json',
  'agent_ticket_savemessagetext' => '/agent/tickets/messages/{message_id}/save-message-text.json',
  'agent_ticket_setmessagenote' => '/agent/tickets/messages/{message_id}/set-message-note.json',
  'agent_ticket_message_attachments' => '/agent/tickets/messages/{message_id}/attachments',
  'agent_ticket_message_attachment_delete' => '/agent/tickets/messages/{message_id}/attachments/{attachment_id}/delete',
  'agent_ticket_message_delete' => '/agent/tickets/messages/{message_id}/delete',
  'agent_ticket_view' => '/agent/tickets/{ticket_id}',
  'agent_ticket_loadlogs' => '/agent/tickets/{ticket_id}/load-logs',
  'agent_ticket_loadattachlist' => '/agent/tickets/{ticket_id}/load-attach-list',
  'agent_ticket_download_debug_report' => '/agent/tickets/{ticket_id}/download-debug-report',
  'agent_ticket_messagepage' => '/agent/tickets/{ticket_id}/message-page/{page}',
  'agent_ticket_doupdate' => '/agent/tickets/{ticket_id}/update-views.json',
  'agent_ticket_lock' => '/agent/tickets/{ticket_id}/lock-ticket.json',
  'agent_ticket_unlock' => '/agent/tickets/{ticket_id}/unlock-ticket.json',
  'agent_ticket_release_lock' => '/agent/tickets/{ticket_id}/release-lock.json',
  'agent_ticket_split' => '/agent/tickets/{ticket_id}/split/{message_id}',
  'agent_ticket_split_save' => '/agent/tickets/{ticket_id}/split-save',
  'agent_ticket_merge_overlay' => '/agent/tickets/{ticket_id}/merge-overlay/{other_ticket_id}',
  'agent_ticket_fwd_overlay' => '/agent/tickets/{ticket_id}/forward/{message_id}',
  'agent_ticket_fwd_send' => '/agent/tickets/{ticket_id}/forward/{message_id}/send',
  'agent_ticket_merge' => '/agent/tickets/{ticket_id}/merge/{other_ticket_id}',
  'agent_ticket_changeuser_overlay' => '/agent/tickets/{ticket_id}/change-user-overlay',
  'agent_ticket_changeuser_overlay_preview' => '/agent/tickets/{ticket_id}/change-user-overlay/preview/{new_person_id}',
  'agent_ticket_changeuser' => '/agent/tickets/{ticket_id}/change-user',
  'agent_ticket_ajaxsavecustomfields' => '/agent/tickets/{ticket_id}/ajax-save-custom-fields',
  'agent_ticket_ajaxsavereply' => '/agent/tickets/{ticket_id}/ajax-save-reply',
  'agent_ticket_ajaxsavesubject' => '/agent/tickets/{ticket_id}/ajax-save-subject.json',
  'agent_ticket_ajaxchangeuseremail' => '/agent/tickets/{ticket_id}/ajax-change-email.json',
  'agent_ticket_ajaxsaveoptions' => '/agent/tickets/{ticket_id}/ajax-save-options',
  'agent_ticket_ajaxsaveflagged' => '/agent/tickets/{ticket_id}/ajax-save-flagged',
  'agent_ticket_addpart' => '/agent/tickets/{ticket_id}/add-part',
  'agent_ticket_set_agent_parts' => '/agent/tickets/{ticket_id}/set-agent-parts.json',
  'agent_ticket_delpart' => '/agent/tickets/{ticket_id}/remove-part.json',
  'agent_ticket_ajaxtab_releated_content' => '/agent/tickets/{ticket_id}/ajax-tab-related-content',
  'agent_ticket_ajax_labels_save' => '/agent/tickets/{ticket_id}/ajax-save-labels',
  'agent_ticket_ajax_get_macro' => '/agent/tickets/{ticket_id}/ajax-get-macro',
  'agent_ticket_ajax_apply_macro' => '/agent/tickets/{ticket_id}/{macro_id}/apply-macro.json',
  'agent_ticket_ajax_save_actions' => '/agent/tickets/{ticket_id}/ajax-save-actions',
  'agent_ticket_message_raw' => '/agent/tickets/{ticket_id}/message-details/{message_id}/view-raw',
  'agent_ticket_message_window' => '/agent/tickets/{ticket_id}/message-details/{message_id}/window/{type}',
  'agent_ticket_message_ajax_getquote' => '/agent/tickets/{ticket_id}/message-details/{message_id}/ajax-get-quote',
  'agent_ticket_saveagentparts' => '/agent/ticket/{ticket_id}/save-agent-parts',
  'agent_ticket_addcharge' => '/agent/ticket/{ticket_id}/add-charge',
  'agent_ticket_chargedelete' => '/agent/ticket/{ticket_id}/charge/{charge_id}/delete/{security_token}',
  'agent_ticket_addsla' => '/agent/ticket/{ticket_id}/add-sla',
  'agent_ticket_sladelete' => '/agent/ticket/{ticket_id}/sla/{sla_id}/delete/{security_token}',
  'agent_ticket_delete' => '/agent/tickets/{ticket_id}/delete',
  'agent_ticket_spam' => '/agent/tickets/{ticket_id}/spam',
  'agent_twitter_new' => '/agent/twitter/new',
  'agent_twitter_new_save' => '/agent/twitter/new/save',
  'agent_twitter_mine_list' => '/agent/twitter/mine/{account_id}/{group}/{group_value}',
  'agent_twitter_team_list' => '/agent/twitter/team/{account_id}/{group}/{group_value}',
  'agent_twitter_unassigned_list' => '/agent/twitter/unassigned/{account_id}/{group}/{group_value}',
  'agent_twitter_all_list' => '/agent/twitter/all/{account_id}/{group}/{group_value}',
  'agent_twitter_sent_list' => '/agent/twitter/sent/{account_id}/{group}/{group_value}',
  'agent_twitter_timeline_list' => '/agent/twitter/timeline/{account_id}/{group}/{group_value}',
  'agent_twitter_followers_list' => '/agent/twitter/followers/{account_id}',
  'agent_twitter_followers_list_new' => '/agent/twitter/followers/{account_id}/new',
  'agent_twitter_following_list' => '/agent/twitter/following/{account_id}',
  'agent_twitter_status_ajaxmasssave' => '/agent/twitter/status/ajax-mass-save.json',
  'agent_twitter_status_ajaxsave_note' => '/agent/twitter/status/ajax-note.json',
  'agent_twitter_status_ajaxsave_retweet' => '/agent/twitter/status/ajax-retweet.json',
  'agent_twitter_status_ajaxsave_unretweet' => '/agent/twitter/status/ajax-unretweet.json',
  'agent_twitter_status_ajaxsave_reply' => '/agent/twitter/status/ajax-reply.json',
  'agent_twitter_status_ajaxsave_archive' => '/agent/twitter/status/ajax-archive.json',
  'agent_twitter_status_ajaxsave_delete' => '/agent/twitter/status/ajax-delete.json',
  'agent_twitter_status_ajaxsave_edit' => '/agent/twitter/status/ajax-edit',
  'agent_twitter_status_ajaxsave_favorite' => '/agent/twitter/status/ajax-favorite.json',
  'agent_twitter_status_ajaxsave_assign' => '/agent/twitter/status/ajax-assign.json',
  'agent_twitter_status_tweet_overlay' => '/agent/twitter/status/tweet-overlay',
  'agent_twitter_user' => '/agent/twitter/user/{user_id}',
  'agent_twitter_user_statuses' => '/agent/twitter/user/{user_id}/statuses',
  'agent_twitter_user_following' => '/agent/twitter/user/{user_id}/following',
  'agent_twitter_user_followers' => '/agent/twitter/user/{user_id}/followers',
  'agent_twitter_user_find' => '/agent/twitter/user/find',
  'agent_twitter_user_message_overlay' => '/agent/twitter/user/{user_id}/message-overlay',
  'agent_twitter_user_ajaxsave_follow' => '/agent/twitter/user/ajax-follow.json',
  'agent_twitter_user_ajaxsave_unfollow' => '/agent/twitter/user/ajax-unfollow.json',
  'agent_twitter_user_ajaxsave_message' => '/agent/twitter/user/ajax-message.json',
  'agent_twitter_user_ajaxsave_archive' => '/agent/twitter/user/ajax-archive.json',
  'agent_twitter_user_ajaxsave_person' => '/agent/twitter/user/ajax-person.json',
  'agent_twitter_user_ajaxsave_organization' => '/agent/twitter/user/ajax-organization.json',
  'agent_twitter_getsectiondata' => '/agent/twitter/get-section-data.json',
  'agent_twitter_updategrouping' => '/agent/twitter/update-grouping.json',
  'agent_twitter_run_search' => '/agent/twitter/{account_id}/search/{search_id}',
  'agent_twitter_search_delete' => '/agent/twitter/{account_id}/search/delete/{security_token}',
  'agent_twitter_new_search' => '/agent/twitter/{account_id}/search/new',
  'agent_task_new' => '/agent/tasks/new',
  'agent_task_save' => '/agent/tasks/save',
  'agent_task_delete' => '/agent/tasks/{task_id}/delete',
  'agent_tasksearch_getsectiondata' => '/agent/tasks/get-section-data.json',
  'agent_task_list' => '/agent/tasks/list/{search_type}/{search_categoty}',
  'agent_task_ajax_labels_save' => '/agent/tasks/{task_id}/ajax-save-labels',
  'agent_task_ajaxsave_comment' => '/agent/tasks/{task_id}/ajax-save-comment',
  'agent_task_ajaxsave' => '/agent/tasks/{task_id}/ajax-save',
  'agent_dealearch_getsectiondata' => '/agent/deal/get-section-data.json',
  'agent_deal_list' => '/agent/deals/list/{owner_type}/{deal_status}/{deal_type_id}',
  'agent_deal_view' => '/agent/deal/{deal_id}',
  'agent_deal_ajaxsave_note' => '/agent/deal/{deal_id}/ajax-save-note',
  'agent_deal_ajax_labels_save' => '/agent/deal/{deal_id}/ajax-save-labels',
  'agent_deal_ajaxsavecustomfields' => '/agent/deal/{deal_id}/ajax-save-custom-fields',
  'agent_deal_set_agent_parts' => '/agent/deals/{deal_id}/{agent_id}/set-agent-parts.json',
  'agent_deal_ajaxsave' => '/agent/deals/{deal_id}/ajax-save',
  'agent_deal_new' => '/agent/deals/new',
  'agent_deal_new_save' => '/agent/deals/new/save',
  'agent_deal_new_getpersonrow' => '/agent/deals/new/get-person-row/{person_id}',
  'agent_deal_new_getorganizationrow' => '/agent/deals/new/get-organization-row/{org_id}',
  'agent_deal_create_setpersonrow' => '/agent/deals/new/create-person-row/{person_id}',
  'agent_deal_new_setpersonrow' => '/agent/deals/new/set-person-row/{person_id}',
  'agent_deal_new_setorganizationrow' => '/agent/deals/new/set-organization-row/{org_id}',
  'agent_deal_create_setorganizationrow' => '/agent/deals/new/create-organization-row/{org_id}',
  'agent_publish_getsectiondata' => '/agent/publish/get-section-data.json',
  'agent_publish_ratingwhovoted' => '/agent/publish/rating-who-voted/{object_type}/{object_id}',
  'agent_publish_whoviewed' => '/agent/publish/who-viewed/{object_type}/{object_id}/{view_action}',
  'agent_publish_save_stickysearchwords' => '/agent/publish/save-sticky-search-words/{type}/{content_id}',
  'agent_publish_validatingcontent' => '/agent/publish/content/validating',
  'agent_feedback_validatingcontent' => '/agent/feedback/content/validating',
  'agent_feedback_validatingcomments' => '/agent/feedback/comments/validating',
  'agent_publish_validatingcontent_approve' => '/agent/publish/content/approve/{type}/{content_id}.json',
  'agent_publish_validatingcontent_disapprove' => '/agent/publish/content/disapprove/{type}/{content_id}.json',
  'agent_publish_validatingcontent_mass' => '/agent/publish/content/validating-mass-actions/{action}',
  'agent_publish_validatingcontent_next' => '/agent/publish/content/get-next-validating/{type}/{content_id}.json',
  'agent_publish_listcomments' => '/agent/publish/comments/list/{type}',
  'agent_publish_validatingcomments' => '/agent/publish/comments/validating',
  'agent_publish_approve_comment' => '/agent/publish/comments/approve/{typename}/{comment_id}',
  'agent_publish_delete_comment' => '/agent/publish/comments/delete/{typename}/{comment_id}',
  'agent_publish_comment_info' => '/agent/publish/comments/info/{typename}/{comment_id}',
  'agent_publish_comment_save' => '/agent/publish/comments/save-comment/{typename}/{comment_id}',
  'agent_public_comment_newticketinfo' => '/agent/publish/comments/new-ticket-info/{typename}/{comment_id}.json',
  'agent_publish_validatingcomments_mass' => '/agent/publish/comments/validating-mass-actions/{action}',
  'agent_publish_savecats' => '/agent/publish/save-categories/{type}',
  'agent_publish_cats_adddel' => '/agent/publish/categories/{type}/delete-category',
  'agent_publish_cats_addcat' => '/agent/publish/categories/{type}/add-category',
  'agent_publish_cats_updateorders' => '/agent/publish/categories/{type}/update-orders',
  'agent_publish_cats_updatetitles' => '/agent/publish/categories/{type}/update-titles',
  'agent_publish_cats_update' => '/agent/publish/categories/{type}/update/{category_id}',
  'agent_publish_cats_updatestructure' => '/agent/publish/categories/{type}/update-structure',
  'agent_publish_cats_newform' => '/agent/publish/categories/{type}/new-form',
  'agent_publish_cats_newform_save' => '/agent/publish/categories/{type}/new-form/save',
  'agent_public_drafts' => '/agent/publish/drafts/{type}',
  'agent_public_drafts_mass' => '/agent/publish/drafts/mass-actions/{action}',
  'agent_publish_search' => '/agent/publish/search',
  'agent_kb_newarticle_save' => '/agent/kb/article/new/save',
  'agent_kb_newarticle' => '/agent/kb/article/new',
  'agent_kb_article' => '/agent/kb/article/{article_id}',
  'agent_kb_ajaxsavecustomfields' => '/agent/kb/article/{article_id}/ajax-save-custom-fields',
  'agent_kb_article_info' => '/agent/kb/article/{article_id}/info',
  'agent_kb_article_revisionstab' => '/agent/kb/article/{article_id}/view-revisions',
  'agent_kb_article_ajaxsave' => '/agent/kb/article/{article_id}/ajax-save',
  'agent_kb_ajax_save_comment' => '/agent/kb/article/{article_id}/ajax-save-comment',
  'agent_kb_ajax_labels_save' => '/agent/kb/article/{article_id}/ajax-save-labels',
  'agent_kb_comparerevs' => '/agent/kb/compare-revs/{rev_old_id}/{rev_new_id}',
  'agent_kb_newpending' => '/agent/kb/pending-articles/new',
  'agent_kb_pending_remove' => '/agent/kb/pending-articles/{pending_article_id}/remove',
  'agent_kb_pending_info' => '/agent/kb/pending-articles/{pending_article_id}/info',
  'agent_kb_pending' => '/agent/kb/pending-articles',
  'agent_kb_pending_massactions' => '/agent/kb/pending-articles/mass-actions/{action}',
  'agent_kb_list' => '/agent/kb/list/{category_id}',
  'agent_kb_cat' => '/agent/kb/category/{category_id}',
  'agent_kb_mass_save' => '/agent/kb/article/ajax-mass-save',
  'agent_glossary_newword_json' => '/agent/glossary/new-word.json',
  'agent_glossary_word_json' => '/agent/glossary/{word_id}.json',
  'agent_glossary_saveword_json' => '/agent/glossary/{word_id}/edit.json',
  'agent_glossary_delword_json' => '/agent/glossary/{word_id}/delete.json',
  'agent_glossary_word_tip' => '/agent/glossary/{word}/tip',
  'agent_news_list' => '/agent/news/list/{category_id}',
  'agent_news_view' => '/agent/news/post/{news_id}',
  'agent_news_revisionstab' => '/agent/news/post/{news_id}/view-revisions',
  'agent_news_save' => '/agent/news/post/{news_id}/ajax-save',
  'agent_news_ajax_labels_save' => '/agent/news/{news_id}/ajax-save-labels',
  'agent_news_ajax_save_comment' => '/agent/news/post/{news_id}/ajax-save-comment',
  'agent_news_new_save' => '/agent/news/new/save',
  'agent_news_new' => '/agent/news/new',
  'agent_news_comparerevs' => '/agent/news/compare-revs/{rev_old_id}/{rev_new_id}',
  'agent_downloads_list' => '/agent/downloads/list/{category_id}',
  'agent_downloads_view' => '/agent/downloads/file/{download_id}',
  'agent_downloads_info' => '/agent/downloads/file/{download_id}/info',
  'agent_kb_downloads_revisionstab' => '/agent/downloads/file/{download_id}/view-revisions',
  'agent_downloads_save' => '/agent/downloads/file/{download_id}/ajax-save',
  'agent_downloads_ajax_labels_save' => '/agent/downloads/file/{download_id}/ajax-save-labels',
  'agent_downloads_ajax_save_comment' => '/agent/downloads/file/{download_id}/ajax-save-comment',
  'agent_downloads_new_save' => '/agent/downloads/new/save',
  'agent_downloads_new' => '/agent/downloads/new',
  'agent_downloads_comparerevs' => '/agent/downloads/compare-revs/{rev_old_id}/{rev_new_id}',
  'agent_feedback_category' => '/agent/feedback/category/{category_id}',
  'agent_feedback_status' => '/agent/feedback/status/{status}',
  'agent_feedback_label' => '/agent/feedback/label/{label}',
  'agent_feedback_filter' => '/agent/feedback/filter',
  'agent_feedback_massactions' => '/agent/feedback/filter/mass-actions/{action}',
  'agent_feedback_getsectiondata' => '/agent/feedback/get-section-data.json',
  'agent_feedback_new' => '/agent/feedback/new',
  'agent_feedback_new_save' => '/agent/feedback/new/save',
  'agent_feedback_view' => '/agent/feedback/view/{feedback_id}',
  'agent_feedback_comparerevs' => '/agent/feedback/compare-revs/{rev_old_id}/{rev_new_id}',
  'agent_feedback_ajaxsavecustomfields' => '/agent/feedback/view/{feedback_id}/ajax-save-custom-fields',
  'agent_feedback_who_voted' => '/agent/feedback/view/{feedback_id}/who-voted',
  'agent_feedback_save' => '/agent/feedback/view/{feedback_id}/ajax-save',
  'agent_feedback_ajax_labels_save' => '/agent/feedback/view/{feedback_id}/ajax-save-labels',
  'agent_feedback_ajax_save_comment' => '/agent/feedback/view/{feedback_id}/ajax-save-comment',
  'agent_feedback_ajaxsavecomment' => '/agent/feedback/view/{feedback_id}/ajax-save-comment',
  'agent_feedback_ajaxsaveeditables' => '/agent/feedback/view/{feedback_id}/ajax-save-editables',
  'agent_feedback_ajaxupdatecat' => '/agent/feedback/view/{feedback_id}/ajax-update-category/{category_id}',
  'agent_feedback_ajaxupdatestatus' => '/agent/feedback/view/{feedback_id}/ajax-update-status/{status_code}',
  'agent_feedback_merge_overlay' => '/agent/feedback/merge-overlay/{feedback_id}/{other_feedback_id}',
  'agent_feedback_merge' => '/agent/feedback/merge/{feedback_id}/{other_feedback_id}',
  'agent_agentchat_getonlineagents' => '/agent/agent-chat/get-online-agents.json',
  'agent_agentchat_get_last_convo' => '/agent/agent-chat/get-last-convo',
  'agent_agentchat_send_message' => '/agent/agent-chat/send-message/{conversation_id}',
  'agent_agentchat_send_agent_message' => '/agent/agent-chat/send-agent-message/{convo_id}',
  'agent_agentchat_history' => '/agent/agent-chat/agent-history/{agent_id}',
  'agent_agentchat_history_team' => '/agent/agent-chat/agent-history/team/{agent_team_id}',
  'agent_agentchat_view' => '/agent/agent-chat/agent-transcript/{conversation_id}',
  'agent_agentchat_getsectiondata' => '/agent/agent-chat/get-section-data.json',
  'agent_userchat_view' => '/agent/chat/view/{conversation_id}',
  'agent_userchat_save_fields' => '/agent/chat/{conversation_id}/save-fields',
  'agent_userchat_blockuser' => '/agent/chat/block-user/{conversation_id}',
  'agent_userchat_unblockuser' => '/agent/chat/unblock-user/{conversation_id}',
  'agent_userchat_ajax_labels_save' => '/agent/chat/{conversation_id}/ajax-save-labels',
  'agent_userchat_open_counts' => '/agent/chat/open-counts.json',
  'agent_userchat_filterlist_group_counts' => '/agent/chat/group-count.json',
  'agent_userchat_filterlist' => '/agent/chat/filter/{filter_id}',
  'agent_userchat_list_new' => '/agent/chat/list-new/{department_id}',
  'agent_userchat_list_active' => '/agent/chat/list-active/{agent_id}',
  'agent_userchat_send_messageview' => '/agent/chat/send-message/{conversation_id}',
  'agent_userchat_send_filemessage' => '/agent/chat/send-file-message/{conversation_id}',
  'agent_userchat_assign' => '/agent/chat/assign/{conversation_id}/{agent_id}',
  'agent_userchat_syncpart' => '/agent/chat/sync-parts/{conversation_id}',
  'agent_userchat_addpart' => '/agent/chat/add-part/{conversation_id}/{agent_id}',
  'agent_userchat_end' => '/agent/chat/end-chat/{conversation_id}',
  'agent_userchat_leave' => '/agent/chat/leave/{conversation_id}',
  'agent_userchat_invite' => '/agent/chat/invite/{conversation_id}/{agent_id}',
  'agent_userchat_changeprop' => '/agent/chat/change-props/{conversation_id}',
  'agent_userchat_getsectiondata' => '/agent/chat/get-section-data.json',
  'agent_usertrack_winheadertable' => '/agent/user-track/win-header-table.html',
  'agent_usertrack_view' => '/agent/user-track/{visitor_id}',
  'agent_mediamanager' => '/agent/media-manager',
  'agent_mediamanager_upload' => '/agent/media-manager/upload',
  'agent_mediamanager_browse' => '/agent/media-manager/browse',
  'agent_textsnippets_widget_shell' => '/agent/text-snippets/{typename}/widget-shell.txt',
  'agent_textsnippets_reloadclient' => '/agent/text-snippets/{typename}/reload-client.json',
  'agent_textsnippets_reloadclient_batch' => '/agent/text-snippets/{typename}/reload-client/{batch}.json',
  'agent_textsnippets_filtersnippets' => '/agent/text-snippets/{typename}/filter.json',
  'agent_textsnippets_getsnippet' => '/agent/text-snippets/{typename}/{id}.json',
  'agent_textsnippets_savesnippet' => '/agent/text-snippets/{typename}/{id}/save.json',
  'agent_textsnippets_delsnippet' => '/agent/text-snippets/{typename}/{id}/delete.json',
  'agent_textsnippets_savecat' => '/agent/text-snippets/{typename}/categories/{id}/save.json',
  'agent_textsnippets_delcat' => '/agent/text-snippets/{typename}/categories/{id}/delete.json',
  'agent_plugins_run' => '/agent/plugins/{plugin_id}/run/{action}',
  'api' => '/api/',
  'api_test' => '/api/test',
  'api_test_post' => '/api/test',
  'api_deskpro_time' => '/api/deskpro/time',
  'api_deskpro_info' => '/api/deskpro/info',
  'api_token_exchange' => '/api/token-exchange',
  'api_token_renew' => '/api/renew-token',
  'api_misc_upload' => '/api/misc/upload',
  'api_misc_session_person' => '/api/misc/session-person/{session_code}',
  'api_misc_rate_limit' => '/api/misc/rate-limit',
  'api_tickets_new' => '/api/tickets',
  'api_tickets_ticket' => '/api/tickets/{ticket_id}',
  'api_tickets_ticket_post' => '/api/tickets/{ticket_id}',
  'api_tickets_ticket_delete' => '/api/tickets/{ticket_id}',
  'api_tickets_ticket_logs' => '/api/tickets/{ticket_id}/logs',
  'api_tickets_ticket_messages' => '/api/tickets/{ticket_id}/messages',
  'api_tickets_ticket_messages_post' => '/api/tickets/{ticket_id}/messages',
  'api_tickets_ticket_message' => '/api/tickets/{ticket_id}/messages/{message_id}',
  'api_tickets_ticket_message_details' => '/api/tickets/{ticket_id}/messages/{message_id}/details',
  'api_tickets_ticket_undelete' => '/api/tickets/{ticket_id}/undelete',
  'api_tickets_ticket_split' => '/api/tickets/{ticket_id}/split',
  'api_tickets_ticket_claim' => '/api/tickets/{ticket_id}/claim',
  'api_tickets_ticket_merge' => '/api/tickets/{ticket_id}/merge/{merge_ticket_id}',
  'api_tickets_ticket_spam' => '/api/tickets/{ticket_id}/spam',
  'api_tickets_ticket_unspam' => '/api/tickets/{ticket_id}/unspam',
  'api_tickets_ticket_lock' => '/api/tickets/{ticket_id}/lock',
  'api_tickets_ticket_unlock' => '/api/tickets/{ticket_id}/unlock',
  'api_tickets_ticket_tasks' => '/api/tickets/{ticket_id}/tasks',
  'api_tickets_ticket_tasks_post' => '/api/tickets/{ticket_id}/tasks',
  'api_tickets_ticket_billing_charges' => '/api/tickets/{ticket_id}/billing-charges',
  'api_tickets_ticket_billing_charges_post' => '/api/tickets/{ticket_id}/billing-charges',
  'api_tickets_ticket_billing_charge' => '/api/tickets/{ticket_id}/billing-charges/{charge_id}',
  'api_tickets_ticket_billing_charge_delete' => '/api/tickets/{ticket_id}/billing-charges/{charge_id}',
  'api_tickets_ticket_slas' => '/api/tickets/{ticket_id}/slas',
  'api_tickets_ticket_slas_post' => '/api/tickets/{ticket_id}/slas',
  'api_tickets_ticket_sla' => '/api/tickets/{ticket_id}/slas/{ticket_sla_id}',
  'api_tickets_ticket_sla_delete' => '/api/tickets/{ticket_id}/slas/{ticket_sla_id}',
  'api_tickets_ticket_participants' => '/api/tickets/{ticket_id}/participants',
  'api_tickets_ticket_participants_post' => '/api/tickets/{ticket_id}/participants',
  'api_tickets_ticket_participant' => '/api/tickets/{ticket_id}/participants/{person_id}',
  'api_tickets_ticket_participant_delete' => '/api/tickets/{ticket_id}/participants/{person_id}',
  'api_tickets_ticket_labels' => '/api/tickets/{ticket_id}/labels',
  'api_tickets_ticket_labels_post' => '/api/tickets/{ticket_id}/labels',
  'api_tickets_ticket_label' => '/api/tickets/{ticket_id}/labels/{label}',
  'api_tickets_ticket_label_delete' => '/api/tickets/{ticket_id}/labels/{label}',
  'api_tickets_fields' => '/api/tickets/fields',
  'api_tickets_departments' => '/api/tickets/departments',
  'api_tickets_products' => '/api/tickets/products',
  'api_tickets_categories' => '/api/tickets/categories',
  'api_tickets_priorities' => '/api/tickets/priorities',
  'api_tickets_workflows' => '/api/tickets/workflows',
  'api_tickets_slas' => '/api/tickets/slas',
  'api_tickets_sla' => '/api/tickets/slas/{sla_id}',
  'api_tickets_sla_people' => '/api/tickets/slas/{sla_id}/people',
  'api_tickets_sla_organizations' => '/api/tickets/slas/{sla_id}/organizations',
  'api_textsnippets_list' => '/api/text-snippets/{typename}',
  'api_textsnippets_new' => '/api/text-snippets/{typename}',
  'api_textsnippets_edit' => '/api/text-snippets/{typename}/{snippet_id}',
  'api_textsnippets_del' => '/api/text-snippets/{typename}/{snippet_id}',
  'api_textsnippets_get' => '/api/text-snippets/{typename}/{snippet_id}',
  'api_textsnippets_cats_list' => '/api/text-snippets/{typename}/categories',
  'api_textsnippets_cats_new' => '/api/text-snippets/{typename}/categories',
  'api_textsnippets_cats_edit' => '/api/text-snippets/{typename}/categories/{category_id}',
  'api_textsnippets_cats_get' => '/api/text-snippets/{typename}/categories/{category_id}',
  'api_textsnippets_cats_del' => '/api/text-snippets/{typename}/categories/{category_id}',
  'api_open_tickets_newticketmessage' => '/api/open/tickets/new-ticket-message',
  'api_tickets' => '/api/tickets',
  'api_tickets_filters' => '/api/tickets/filters',
  'api_tickets_filter_counts' => '/api/tickets/filters/counts',
  'api_tickets_filter' => '/api/tickets/filters/{filter_id}',
  'api_people' => '/api/people',
  'api_people_post' => '/api/people',
  'api_people_person' => '/api/people/{person_id}',
  'api_people_person_post' => '/api/people/{person_id}',
  'api_people_person_delete' => '/api/people/{person_id}',
  'api_people_person_merge' => '/api/people/{person_id}/merge/{other_person_id}',
  'api_people_person_picture' => '/api/people/{person_id}/picture',
  'api_people_person_picture_post' => '/api/people/{person_id}/picture',
  'api_people_person_picture_delete' => '/api/people/{person_id}/picture',
  'api_people_person_emails' => '/api/people/{person_id}/emails',
  'api_people_person_emails_post' => '/api/people/{person_id}/emails',
  'api_people_person_email' => '/api/people/{person_id}/emails/{email_id}',
  'api_people_person_email_post' => '/api/people/{person_id}/emails/{email_id}',
  'api_people_person_email_delete' => '/api/people/{person_id}/emails/{email_id}',
  'api_people_person_vcard' => '/api/people/{person_id}/vcard',
  'api_people_person_tickets' => '/api/people/{person_id}/tickets',
  'api_people_person_chats' => '/api/people/{person_id}/chats',
  'api_people_person_activity_stream' => '/api/people/{person_id}/activity-stream',
  'api_people_person_reset_password' => '/api/people/{person_id}/reset-password',
  'api_people_person_clear_session' => '/api/people/{person_id}/clear-session',
  'api_people_person_slas' => '/api/people/{person_id}/slas',
  'api_people_person_slas_post' => '/api/people/{person_id}/slas',
  'api_people_person_sla' => '/api/people/{person_id}/slas/{sla_id}',
  'api_people_person_sla_delete' => '/api/people/{person_id}/slas/{sla_id}',
  'api_people_person_notes' => '/api/people/{person_id}/notes',
  'api_people_person_notes_post' => '/api/people/{person_id}/notes',
  'api_people_person_billing_charges' => '/api/people/{person_id}/billing-charges',
  'api_people_person_contact_details' => '/api/people/{person_id}/contact-details',
  'api_people_person_contact_details_post' => '/api/people/{person_id}/contact-details',
  'api_people_person_contact_detail' => '/api/people/{person_id}/contact-details/{contact_id}',
  'api_people_person_contact_detail_delete' => '/api/people/{person_id}/contact-details/{contact_id}',
  'api_people_person_groups' => '/api/people/{person_id}/groups',
  'api_people_person_groups_post' => '/api/people/{person_id}/groups',
  'api_people_person_group' => '/api/people/{person_id}/groups/{usergroup_id}',
  'api_people_person_group_delete' => '/api/people/{person_id}/groups/{usergroup_id}',
  'api_people_person_labels' => '/api/people/{person_id}/labels',
  'api_people_person_labels_post' => '/api/people/{person_id}/labels',
  'api_people_person_label' => '/api/people/{person_id}/labels/{label}',
  'api_people_person_label_delete' => '/api/people/{person_id}/labels/{label}',
  'api_people_fields' => '/api/people/fields',
  'api_people_groups' => '/api/people/groups',
  'api_organizations' => '/api/organizations',
  'api_organizations_post' => '/api/organizations',
  'api_organizations_organization' => '/api/organizations/{organization_id}',
  'api_organizations_organization_post' => '/api/organizations/{organization_id}',
  'api_organizations_organization_delete' => '/api/organizations/{organization_id}',
  'api_organizations_organization_picture' => '/api/organizations/{organization_id}/picture',
  'api_organizations_organization_picture_post' => '/api/organizations/{organization_id}/picture',
  'api_organizations_organization_picture_delete' => '/api/organizations/{organization_id}/picture',
  'api_organizations_organization_activity_stream' => '/api/organizations/{organization_id}/activity-stream',
  'api_organizations_organization_members' => '/api/organizations/{organization_id}/members',
  'api_organizations_organization_tickets' => '/api/organizations/{organization_id}/tickets',
  'api_organizations_organization_chats' => '/api/organizations/{organization_id}/chats',
  'api_organizations_organization_slas' => '/api/organizations/{organization_id}/slas',
  'api_organizations_organization_slas_post' => '/api/organizations/{organization_id}/slas',
  'api_organizations_organization_sla' => '/api/organizations/{organization_id}/slas/{sla_id}',
  'api_organizations_organization_sla_delete' => '/api/organizations/{organization_id}/slas/{sla_id}',
  'api_organizations_organization_billing_charges' => '/api/organizations/{organization_id}/billing-charges',
  'api_organizations_organization_email_domains' => '/api/organizations/{organization_id}/email-domains',
  'api_organizations_organization_email_domains_post' => '/api/organizations/{organization_id}/email-domains',
  'api_organizations_organization_email_domain' => '/api/organizations/{organization_id}/email-domains/{domain}',
  'api_organizations_organization_email_domain_move_users' => '/api/organizations/{organization_id}/email-domains/{domain}/move-users',
  'api_organizations_organization_email_domain_move_taken_users' => '/api/organizations/{organization_id}/email-domains/{domain}/move-taken-users',
  'api_organizations_organization_email_domain_delete' => '/api/organizations/{organization_id}/email-domains/{domain}',
  'api_organizations_organization_contact_details' => '/api/organizations/{organization_id}/contact-details',
  'api_organizations_organization_contact_details_post' => '/api/organizations/{organization_id}/contact-details',
  'api_organizations_organization_contact_detail' => '/api/organizations/{organization_id}/contact-details/{contact_id}',
  'api_organizations_organization_contact_detail_delete' => '/api/organizations/{organization_id}/contact-details/{contact_id}',
  'api_organizations_organization_groups' => '/api/organizations/{organization_id}/groups',
  'api_organizations_organization_groups_post' => '/api/organizations/{organization_id}/groups',
  'api_organizations_organization_group' => '/api/organizations/{organization_id}/groups/{usergroup_id}',
  'api_organizations_organization_group_delete' => '/api/organizations/{organization_id}/groups/{usergroup_id}',
  'api_organizations_organization_labels' => '/api/organizations/{organization_id}/labels',
  'api_organizations_organization_labels_post' => '/api/organizations/{organization_id}/labels',
  'api_organizations_organization_label' => '/api/organizations/{organization_id}/labels/{label}',
  'api_organizations_organization_label_delete' => '/api/organizations/{organization_id}/labels/{label}',
  'api_organizations_fields' => '/api/organizations/fields',
  'api_organizations_groups' => '/api/organizations/groups',
  'api_chats' => '/api/chats',
  'api_chats_chat' => '/api/chats/{chat_id}',
  'api_chats_chat_post' => '/api/chats/{chat_id}',
  'api_chats_chat_leave' => '/api/chats/{chat_id}/leave',
  'api_chats_chat_end' => '/api/chats/{chat_id}/end',
  'api_chats_chat_messages' => '/api/chats/{chat_id}/messages',
  'api_chats_chat_messages_post' => '/api/chats/{chat_id}/messages',
  'api_chats_chat_participants' => '/api/chats/{chat_id}/participants',
  'api_chats_chat_participants_post' => '/api/chats/{chat_id}/participants',
  'api_chats_chat_participant' => '/api/chats/{chat_id}/participants/{person_id}',
  'api_chats_chat_participant_delete' => '/api/chats/{chat_id}/participants/{person_id}',
  'api_chats_chat_labels' => '/api/chats/{chat_id}/labels',
  'api_chats_chat_labels_post' => '/api/chats/{chat_id}/labels',
  'api_chats_chat_label' => '/api/chats/{chat_id}/labels/{label}',
  'api_chats_chat_label_delete' => '/api/chats/{chat_id}/labels/{label}',
  'api_downloads' => '/api/downloads',
  'api_downloads_post' => '/api/downloads',
  'api_downloads_download' => '/api/downloads/{download_id}',
  'api_downloads_download_post' => '/api/downloads/{download_id}',
  'api_downloads_download_delete' => '/api/downloads/{download_id}',
  'api_downloads_download_comments' => '/api/downloads/{download_id}/comments',
  'api_downloads_download_comments_new' => '/api/downloads/{download_id}/comments',
  'api_downloads_download_comments_comment' => '/api/downloads/{download_id}/comments/{comment_id}',
  'api_downloads_download_comments_comment_post' => '/api/downloads/{download_id}/comments/{comment_id}',
  'api_downloads_download_comments_comment_delete' => '/api/downloads/{download_id}/comments/{comment_id}',
  'api_downloads_download_labels' => '/api/downloads/{download_id}/labels',
  'api_downloads_download_labels_post' => '/api/downloads/{download_id}/labels',
  'api_downloads_download_label' => '/api/downloads/{download_id}/labels/{label}',
  'api_downloads_download_label_delete' => '/api/downloads/{download_id}/labels/{label}',
  'api_downloads_validating_comments' => '/api/downloads/validating-comments',
  'api_downloads_categories' => '/api/downloads/categories',
  'api_downloads_categories_post' => '/api/downloads/categories',
  'api_downloads_category' => '/api/downloads/categories/{category_id}',
  'api_downloads_category_post' => '/api/downloads/categories/{category_id}',
  'api_downloads_category_delete' => '/api/downloads/categories/{category_id}',
  'api_downloads_category_downloads' => '/api/downloads/categories/{category_id}/downloads',
  'api_downloads_category_groups' => '/api/downloads/categories/{category_id}/groups',
  'api_downloads_category_groups_post' => '/api/downloads/categories/{category_id}/groups',
  'api_downloads_category_group' => '/api/downloads/categories/{category_id}/groups/{group_id}',
  'api_downloads_category_group_delete' => '/api/downloads/categories/{category_id}/groups/{group_id}',
  'api_news' => '/api/news',
  'api_news_post' => '/api/news',
  'api_news_news' => '/api/news/{news_id}',
  'api_news_news_post' => '/api/news/{news_id}',
  'api_news_news_delete' => '/api/news/{news_id}',
  'api_news_news_comments' => '/api/news/{news_id}/comments',
  'api_news_news_comments_new' => '/api/news/{news_id}/comments',
  'api_news_news_comments_comment' => '/api/news/{news_id}/comments/{comment_id}',
  'api_news_news_comments_comment_post' => '/api/news/{news_id}/comments/{comment_id}',
  'api_news_news_comments_comment_delete' => '/api/news/{news_id}/comments/{comment_id}',
  'api_news_news_labels' => '/api/news/{news_id}/labels',
  'api_news_news_labels_post' => '/api/news/{news_id}/labels',
  'api_news_news_label' => '/api/news/{news_id}/labels/{label}',
  'api_news_news_label_delete' => '/api/news/{news_id}/labels/{label}',
  'api_news_validating_comments' => '/api/news/validating-comments',
  'api_news_categories' => '/api/news/categories',
  'api_news_categories_post' => '/api/news/categories',
  'api_news_category' => '/api/news/categories/{category_id}',
  'api_news_category_post' => '/api/news/categories/{category_id}',
  'api_news_category_delete' => '/api/news/categories/{category_id}',
  'api_news_category_news' => '/api/news/categories/{category_id}/news',
  'api_news_category_groups' => '/api/news/categories/{category_id}/groups',
  'api_news_category_groups_post' => '/api/news/categories/{category_id}/groups',
  'api_news_category_group' => '/api/news/categories/{category_id}/groups/{group_id}',
  'api_news_category_group_delete' => '/api/news/categories/{category_id}/groups/{group_id}',
  'api_kb' => '/api/kb',
  'api_kb_post' => '/api/kb',
  'api_kb_article' => '/api/kb/{article_id}',
  'api_kb_article_post' => '/api/kb/{article_id}',
  'api_kb_article_delete' => '/api/kb/{article_id}',
  'api_kb_article_votes' => '/api/kb/{article_id}/votes',
  'api_kb_article_comments' => '/api/kb/{article_id}/comments',
  'api_kb_article_comments_new' => '/api/kb/{article_id}/comments',
  'api_kb_article_comments_comment' => '/api/kb/{article_id}/comments/{comment_id}',
  'api_kb_article_comments_comment_post' => '/api/kb/{article_id}/comments/{comment_id}',
  'api_kb_article_comments_comment_delete' => '/api/kb/{article_id}/comments/{comment_id}',
  'api_kb_article_attachments' => '/api/kb/{article_id}/attachments',
  'api_kb_article_attachments_post' => '/api/kb/{article_id}/attachments',
  'api_kb_article_attachment' => '/api/kb/{article_id}/attachments/{attachment_id}',
  'api_kb_article_attachment_delete' => '/api/kb/{article_id}/attachments/{attachment_id}',
  'api_kb_article_labels' => '/api/kb/{article_id}/labels',
  'api_kb_article_labels_post' => '/api/kb/{article_id}/labels',
  'api_kb_article_label' => '/api/kb/{article_id}/labels/{label}',
  'api_kb_article_label_delete' => '/api/kb/{article_id}/labels/{label}',
  'api_kb_validating_comments' => '/api/kb/validating-comments',
  'api_kb_categories' => '/api/kb/categories',
  'api_kb_categories_post' => '/api/kb/categories',
  'api_kb_category' => '/api/kb/categories/{category_id}',
  'api_kb_category_post' => '/api/kb/categories/{category_id}',
  'api_kb_category_delete' => '/api/kb/categories/{category_id}',
  'api_kb_category_articles' => '/api/kb/categories/{category_id}/articles',
  'api_kb_category_groups' => '/api/kb/categories/{category_id}/groups',
  'api_kb_category_groups_post' => '/api/kb/categories/{category_id}/groups',
  'api_kb_category_group' => '/api/kb/categories/{category_id}/groups/{group_id}',
  'api_kb_category_group_delete' => '/api/kb/categories/{category_id}/groups/{group_id}',
  'api_kb_fields' => '/api/kb/fields',
  'api_kb_products' => '/api/kb/products',
  'api_feedback' => '/api/feedback',
  'api_feedback_post' => '/api/feedback',
  'api_feedback_feedback' => '/api/feedback/{feedback_id}',
  'api_feedback_feedback_post' => '/api/feedback/{feedback_id}',
  'api_feedback_feedback_delete' => '/api/feedback/{feedback_id}',
  'api_feedback_feedback_votes' => '/api/feedback/{feedback_id}/votes',
  'api_feedback_feedback_comments' => '/api/feedback/{feedback_id}/comments',
  'api_feedback_feedback_comments_new' => '/api/feedback/{feedback_id}/comments',
  'api_feedback_feedback_comments_comment' => '/api/feedback/{feedback_id}/comments/{comment_id}',
  'api_feedback_feedback_comments_comment_post' => '/api/feedback/{feedback_id}/comments/{comment_id}',
  'api_feedback_feedback_comments_comment_delete' => '/api/feedback/{feedback_id}/comments/{comment_id}',
  'api_feedback_feedback_merge' => '/api/feedback/{feedback_id}/merge/{other_feedback_id}',
  'api_feedback_feedback_attachments' => '/api/feedback/{feedback_id}/attachments',
  'api_feedback_feedback_attachments_post' => '/api/feedback/{feedback_id}/attachments',
  'api_feedback_feedback_attachment' => '/api/feedback/{feedback_id}/attachments/{attachment_id}',
  'api_feedback_feedback_attachment_delete' => '/api/feedback/{feedback_id}/attachments/{attachment_id}',
  'api_feedback_feedback_labels' => '/api/feedback/{feedback_id}/labels',
  'api_feedback_feedback_labels_post' => '/api/feedback/{feedback_id}/labels',
  'api_feedback_feedback_label' => '/api/feedback/{feedback_id}/labels/{label}',
  'api_feedback_feedback_label_delete' => '/api/feedback/{feedback_id}/labels/{label}',
  'api_feedback_validating_comments' => '/api/feedback/validating-comments',
  'api_feedback_categories' => '/api/feedback/categories',
  'api_feedback_status_categories' => '/api/feedback/status-categories',
  'api_feedback_user_categories' => '/api/feedback/user-categories',
  'api_tasks' => '/api/tasks',
  'api_tasks_post' => '/api/tasks',
  'api_tasks_task' => '/api/tasks/{task_id}',
  'api_tasks_task_post' => '/api/tasks/{task_id}',
  'api_tasks_task_delete' => '/api/tasks/{task_id}',
  'api_tasks_task_associations' => '/api/tasks/{task_id}/associations',
  'api_tasks_task_associations_post' => '/api/tasks/{task_id}/associations',
  'api_tasks_task_associated_item' => '/api/tasks/{task_id}/associations/{assoc_id}',
  'api_tasks_task_comments' => '/api/tasks/{task_id}/comments',
  'api_tasks_task_comments_post' => '/api/tasks/{task_id}/comments',
  'api_tasks_task_comment' => '/api/tasks/{task_id}/comments/{comment_id}',
  'api_tasks_task_associated_item_delete' => '/api/tasks/{task_id}/comments/{comment_id}',
  'api_tasks_task_labels' => '/api/tasks/{task_id}/labels',
  'api_tasks_task_labels_post' => '/api/tasks/{task_id}/labels',
  'api_tasks_task_label' => '/api/tasks/{task_id}/labels/{label}',
  'api_tasks_task_label_delete' => '/api/tasks/{task_id}/labels/{label}',
  'api_glossary' => '/api/glossary',
  'api_glossary_lookup' => '/api/glossary/lookup',
  'api_glossary_post' => '/api/glossary',
  'api_glossary_word' => '/api/glossary/{word_id}',
  'api_glossary_word_delete' => '/api/glossary/{word_id}',
  'api_glossary_definition' => '/api/glossary/definitions/{definition_id}',
  'api_glossary_definition_post' => '/api/glossary/definitions/{definition_id}',
  'api_glossary_definition_delete' => '/api/glossary/definitions/{definition_id}',
  'api_dismiss_activity' => '/api/activity/dismiss',
  'api_get_activity' => '/api/activity/{since}',
);
static private $routeFragments = array (
  'agent_recyclebin' => 'recycle-bin',
  'agent_people_view' => 'p',
  'agent_peoplesearch_usergroup' => 'usergroup',
  'agent_peoplesearch_organization' => 'organization-members',
  'agent_peoplesearch_customfilter' => 'people',
  'agent_org_view' => 'o',
  'agent_orgsearch_customfilter' => 'orgs',
  'agent_ticketsearch_runfilter' => 'filter',
  'agent_ticketsearch_runnamedfilter' => 'inbox',
  'agent_ticketsearch_runsla' => 'sla',
  'agent_ticket_new' => 'nt',
  'agent_ticket_view' => 't',
  'agent_twitter_mine_list' => 'tw-own',
  'agent_twitter_team_list' => 'tw-team',
  'agent_twitter_unassigned_list' => 'tw-unassigned',
  'agent_twitter_all_list' => 'tw-all',
  'agent_twitter_sent_list' => 'tw-sent',
  'agent_twitter_timeline_list' => 'tw-timeline',
  'agent_twitter_followers_list' => 'tw-followers',
  'agent_twitter_followers_list_new' => 'tw-newfollowers',
  'agent_twitter_following_list' => 'tw-following',
  'agent_twitter_user' => 'twitter',
  'agent_twitter_run_search' => 'searches',
  'agent_task_new' => 'nt',
  'agent_task_list' => 'tasks',
  'agent_deal_view' => 'd',
  'agent_deal_new' => 'nt',
  'agent_publish_validatingcontent' => 'validating_content',
  'agent_feedback_validatingcontent' => 'fb_content',
  'agent_feedback_validatingcomments' => 'fb_comments',
  'agent_publish_validatingcontent_next' => 'pending',
  'agent_publish_listcomments' => 'list_comments',
  'agent_publish_validatingcomments' => 'validating_comments',
  'agent_public_drafts' => 'drafts',
  'agent_kb_article' => 'a',
  'agent_kb_pending' => 'pending',
  'agent_kb_list' => 'knowledgebase',
  'agent_news_list' => 'news',
  'agent_news_view' => 'n',
  'agent_downloads_list' => 'downloads',
  'agent_downloads_view' => 'd',
  'agent_feedback_category' => 'category',
  'agent_feedback_status' => 'status',
  'agent_feedback_label' => 'label',
  'agent_feedback_view' => 'i',
  'agent_userchat_view' => 'c',
  'agent_userchat_filterlist' => 'ended',
  'agent_userchat_list_new' => 'new',
  'agent_userchat_list_active' => 'active',
);
static private $fragmentNames = array (
  'recycle-bin' => 'agent_recyclebin',
  'p' => 'agent_people_view',
  'usergroup' => 'agent_peoplesearch_usergroup',
  'organization-members' => 'agent_peoplesearch_organization',
  'people' => 'agent_peoplesearch_customfilter',
  'o' => 'agent_org_view',
  'orgs' => 'agent_orgsearch_customfilter',
  'filter' => 'agent_ticketsearch_runfilter',
  'inbox' => 'agent_ticketsearch_runnamedfilter',
  'sla' => 'agent_ticketsearch_runsla',
  'nt' => 'agent_deal_new',
  't' => 'agent_ticket_view',
  'tw-own' => 'agent_twitter_mine_list',
  'tw-team' => 'agent_twitter_team_list',
  'tw-unassigned' => 'agent_twitter_unassigned_list',
  'tw-all' => 'agent_twitter_all_list',
  'tw-sent' => 'agent_twitter_sent_list',
  'tw-timeline' => 'agent_twitter_timeline_list',
  'tw-followers' => 'agent_twitter_followers_list',
  'tw-newfollowers' => 'agent_twitter_followers_list_new',
  'tw-following' => 'agent_twitter_following_list',
  'twitter' => 'agent_twitter_user',
  'searches' => 'agent_twitter_run_search',
  'tasks' => 'agent_task_list',
  'd' => 'agent_downloads_view',
  'validating_content' => 'agent_publish_validatingcontent',
  'fb_content' => 'agent_feedback_validatingcontent',
  'fb_comments' => 'agent_feedback_validatingcomments',
  'pending' => 'agent_kb_pending',
  'list_comments' => 'agent_publish_listcomments',
  'validating_comments' => 'agent_publish_validatingcomments',
  'drafts' => 'agent_public_drafts',
  'a' => 'agent_kb_article',
  'knowledgebase' => 'agent_kb_list',
  'news' => 'agent_news_list',
  'n' => 'agent_news_view',
  'downloads' => 'agent_downloads_list',
  'category' => 'agent_feedback_category',
  'status' => 'agent_feedback_status',
  'label' => 'agent_feedback_label',
  'i' => 'agent_feedback_view',
  'c' => 'agent_userchat_view',
  'ended' => 'agent_userchat_filterlist',
  'new' => 'agent_userchat_list_new',
  'active' => 'agent_userchat_list_active',
);
static private $fragmentTypes = array (
  'recycle-bin' => 'list',
  'p' => 'page',
  'usergroup' => 'list',
  'organization-members' => 'list',
  'people' => 'list',
  'o' => 'page',
  'orgs' => 'list',
  'filter' => 'list',
  'inbox' => 'list',
  'sla' => 'list',
  'nt' => 'page',
  't' => 'page',
  'tw-own' => 'list',
  'tw-team' => 'list',
  'tw-unassigned' => 'list',
  'tw-all' => 'list',
  'tw-sent' => 'list',
  'tw-timeline' => 'list',
  'tw-followers' => 'list',
  'tw-newfollowers' => 'list',
  'tw-following' => 'list',
  'twitter' => 'page',
  'searches' => 'list',
  'tasks' => 'list',
  'd' => 'page',
  'validating_content' => 'list',
  'fb_content' => 'list',
  'fb_comments' => 'list',
  'pending' => 'list',
  'list_comments' => 'list',
  'validating_comments' => 'list',
  'drafts' => 'list',
  'a' => 'page',
  'knowledgebase' => 'list',
  'news' => 'list',
  'n' => 'page',
  'downloads' => 'list',
  'category' => 'list',
  'status' => 'list',
  'label' => 'list',
  'i' => 'page',
  'c' => 'page',
  'ended' => 'list',
  'new' => 'list',
  'active' => 'list',
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
       'agent' => true,
       'agent_savedom' => true,
       'agent_combined_sectiondata' => true,
       'agent_load_recent_tabs' => true,
       'agent_accept_upload' => true,
       'agent_accept_redactor_image_upload' => true,
       'agent_redactor_autosave' => true,
       'agent_submit_deskpro_feedback' => true,
       'agent_parse_vcard' => true,
       'agent_get_server_time' => true,
       'agent_ajax_save_prefs' => true,
       'agent_ajax_labels_autocomplete' => true,
       'agent_interface_data_js' => true,
       'agent_dismiss_help_message' => true,
       'agent_set_agent_status' => true,
       'agent_proxy' => true,
       'agent_load_version_notice' => true,
       'agent_dismiss_version_notice' => true,
       'agent_redirect_out' => true,
       'agent_redirect_out_info' => true,
       'agent_password_confirm_code' => true,
       'agent_quicksearch' => true,
       'agent_search' => true,
       'agent_search_json' => true,
       'agent_recyclebin' => true,
       'agent_recyclebin_more' => true,
       'agent_login_preload_sources' => true,
       'agent_browser_requirements' => true,
       'agent_browser_requirements_ie_compat' => true,
       'agent_login' => true,
       'agent_login_authenticate_local' => true,
       'agent_login_adminlogin' => true,
       'agent_send_lost' => true,
       'agent_settings' => true,
       'agent_settings_profile_save' => true,
       'agent_settings_profile_savewelcome' => true,
       'agent_settings_signature' => true,
       'agent_settings_signature_save' => true,
       'agent_settings_profile_updatetimezone' => true,
       'agent_settings_ticketnotif' => true,
       'agent_settings_ticketnotif_save' => true,
       'agent_settings_othernotif' => true,
       'agent_settings_othernotif_save' => true,
       'agent_settings_ticketmacros' => true,
       'agent_settings_ticketmacros_edit' => true,
       'agent_settings_ticketmacros_edit_save' => true,
       'agent_settings_ticketmacros_new' => true,
       'agent_settings_ticketmacros_del' => true,
       'agent_settings_ticketfilters' => true,
       'agent_settings_ticketfilters_edit' => true,
       'agent_settings_ticketfilters_edit_save' => true,
       'agent_settings_ticketfilters_del' => true,
       'agent_settings_ticketfilters_new' => true,
       'agent_settings_ticketslas' => true,
       'agent_people_validate_email' => true,
       'agent_people_view' => true,
       'agent_people_view_basicjson' => true,
       'agent_people_viewsession' => true,
       'agent_people_validate_list' => true,
       'agent_people_validate_approve' => true,
       'agent_people_validate_delete' => true,
       'agent_people_new' => true,
       'agent_people_new_save' => true,
       'agent_people_ajaxsave' => true,
       'agent_people_savecontactdata' => true,
       'agent_people_unban_email' => true,
       'agent_people_merge_overlay' => true,
       'agent_people_merge' => true,
       'agent_people_delete' => true,
       'agent_people_login_as' => true,
       'agent_people_changepicoverlay' => true,
       'agent_people_ajaxsave_note' => true,
       'agent_people_ajaxsave_organization' => true,
       'agent_person_ajax_labels_save' => true,
       'agent_person_ajaxsavecustomfields' => true,
       'agent_peoplesearch_usergroup' => true,
       'agent_peoplesearch_organization' => true,
       'agent_peoplesearch_customfilter' => true,
       'agent_peoplesearch_getpage' => true,
       'agent_peoplesearch_performquick' => true,
       'agent_peoplesearch_quickfind' => true,
       'agent_peoplesearch_quickfind_search' => true,
       'agent_peoplesearch_getsectiondata' => true,
       'agent_peoplesearch_getsectiondata_reloadcounts' => true,
       'agent_peoplesearch_reload_label_sectiondata' => true,
       'agent_org_view' => true,
       'agent_org_new' => true,
       'agent_org_new_save' => true,
       'agent_org_ajaxsave' => true,
       'agent_org_domain_assign' => true,
       'agent_org_domain_unassign' => true,
       'agent_org_domain_moveusers' => true,
       'agent_org_domain_moveusers_exist' => true,
       'agent_org_save_member_pos' => true,
       'agent_org_save_member_manager' => true,
       'agent_org_savecontactdata' => true,
       'agent_org_delete' => true,
       'agent_org_ajaxsave_note' => true,
       'agent_org_ajax_labels_save' => true,
       'agent_org_ajaxsavecustomfields' => true,
       'agent_org_changepicoverlay' => true,
       'agent_orgsearch_getpage' => true,
       'agent_orgsearch_customfilter' => true,
       'agent_orgsearch_quicknamesearch' => true,
       'agent_orgsearch_namelookup' => true,
       'agent_ticketsearch_getsectiondata' => true,
       'agent_ticketsearch_getsection_reloadarchive' => true,
       'agent_ticketsearch_refreshsectiondata' => true,
       'agent_ticketsearch_getlabelssection' => true,
       'agent_ticketsearch_getfiltercounts' => true,
       'agent_ticketsearch_getslacounts' => true,
       'agent_ticketsearch_grouptickets' => true,
       'agent_ticketsearch_getpage' => true,
       'agent_ticketsearch_getflaggedsectiondata' => true,
       'agent_ticketsearch_runcustomfilter' => true,
       'agent_ticketsearch_quicksearch' => true,
       'agent_ticketsearch_singleticketrow' => true,
       'agent_ticketsearch_runfilter' => true,
       'agent_ticketsearch_runnamedfilter' => true,
       'agent_ticketsearch_runsla' => true,
       'agent_ticketsearch_ajax_get_macro' => true,
       'agent_ticketsearch_ajax_get_macro_actions' => true,
       'agent_ticketsearch_ajax_save_actions' => true,
       'agent_ticketsearch_ajax_delete_tickets' => true,
       'agent_ticketsearch_ajax_release_locks' => true,
       'agent_ticket_new' => true,
       'agent_ticket_new_save' => true,
       'agent_ticket_new_getpersonrow' => true,
       'agent_ticket_getmessagetpl' => true,
       'agent_ticket_update_drafts' => true,
       'agent_ticket_getmessagetext' => true,
       'agent_ticket_getfullmessage' => true,
       'agent_ticket_savemessagetext' => true,
       'agent_ticket_setmessagenote' => true,
       'agent_ticket_message_attachments' => true,
       'agent_ticket_message_attachment_delete' => true,
       'agent_ticket_message_delete' => true,
       'agent_ticket_view' => true,
       'agent_ticket_loadlogs' => true,
       'agent_ticket_loadattachlist' => true,
       'agent_ticket_download_debug_report' => true,
       'agent_ticket_messagepage' => true,
       'agent_ticket_doupdate' => true,
       'agent_ticket_lock' => true,
       'agent_ticket_unlock' => true,
       'agent_ticket_release_lock' => true,
       'agent_ticket_split' => true,
       'agent_ticket_split_save' => true,
       'agent_ticket_merge_overlay' => true,
       'agent_ticket_fwd_overlay' => true,
       'agent_ticket_fwd_send' => true,
       'agent_ticket_merge' => true,
       'agent_ticket_changeuser_overlay' => true,
       'agent_ticket_changeuser_overlay_preview' => true,
       'agent_ticket_changeuser' => true,
       'agent_ticket_ajaxsavecustomfields' => true,
       'agent_ticket_ajaxsavereply' => true,
       'agent_ticket_ajaxsavesubject' => true,
       'agent_ticket_ajaxchangeuseremail' => true,
       'agent_ticket_ajaxsaveoptions' => true,
       'agent_ticket_ajaxsaveflagged' => true,
       'agent_ticket_addpart' => true,
       'agent_ticket_set_agent_parts' => true,
       'agent_ticket_delpart' => true,
       'agent_ticket_ajaxtab_releated_content' => true,
       'agent_ticket_ajax_labels_save' => true,
       'agent_ticket_ajax_get_macro' => true,
       'agent_ticket_ajax_apply_macro' => true,
       'agent_ticket_ajax_save_actions' => true,
       'agent_ticket_message_raw' => true,
       'agent_ticket_message_window' => true,
       'agent_ticket_message_ajax_getquote' => true,
       'agent_ticket_saveagentparts' => true,
       'agent_ticket_addcharge' => true,
       'agent_ticket_chargedelete' => true,
       'agent_ticket_addsla' => true,
       'agent_ticket_sladelete' => true,
       'agent_ticket_delete' => true,
       'agent_ticket_spam' => true,
       'agent_twitter_new' => true,
       'agent_twitter_new_save' => true,
       'agent_twitter_mine_list' => true,
       'agent_twitter_team_list' => true,
       'agent_twitter_unassigned_list' => true,
       'agent_twitter_all_list' => true,
       'agent_twitter_sent_list' => true,
       'agent_twitter_timeline_list' => true,
       'agent_twitter_followers_list' => true,
       'agent_twitter_followers_list_new' => true,
       'agent_twitter_following_list' => true,
       'agent_twitter_status_ajaxmasssave' => true,
       'agent_twitter_status_ajaxsave_note' => true,
       'agent_twitter_status_ajaxsave_retweet' => true,
       'agent_twitter_status_ajaxsave_unretweet' => true,
       'agent_twitter_status_ajaxsave_reply' => true,
       'agent_twitter_status_ajaxsave_archive' => true,
       'agent_twitter_status_ajaxsave_delete' => true,
       'agent_twitter_status_ajaxsave_edit' => true,
       'agent_twitter_status_ajaxsave_favorite' => true,
       'agent_twitter_status_ajaxsave_assign' => true,
       'agent_twitter_status_tweet_overlay' => true,
       'agent_twitter_user' => true,
       'agent_twitter_user_statuses' => true,
       'agent_twitter_user_following' => true,
       'agent_twitter_user_followers' => true,
       'agent_twitter_user_find' => true,
       'agent_twitter_user_message_overlay' => true,
       'agent_twitter_user_ajaxsave_follow' => true,
       'agent_twitter_user_ajaxsave_unfollow' => true,
       'agent_twitter_user_ajaxsave_message' => true,
       'agent_twitter_user_ajaxsave_archive' => true,
       'agent_twitter_user_ajaxsave_person' => true,
       'agent_twitter_user_ajaxsave_organization' => true,
       'agent_twitter_getsectiondata' => true,
       'agent_twitter_updategrouping' => true,
       'agent_twitter_run_search' => true,
       'agent_twitter_search_delete' => true,
       'agent_twitter_new_search' => true,
       'agent_task_new' => true,
       'agent_task_save' => true,
       'agent_task_delete' => true,
       'agent_tasksearch_getsectiondata' => true,
       'agent_task_list' => true,
       'agent_task_ajax_labels_save' => true,
       'agent_task_ajaxsave_comment' => true,
       'agent_task_ajaxsave' => true,
       'agent_dealearch_getsectiondata' => true,
       'agent_deal_list' => true,
       'agent_deal_view' => true,
       'agent_deal_ajaxsave_note' => true,
       'agent_deal_ajax_labels_save' => true,
       'agent_deal_ajaxsavecustomfields' => true,
       'agent_deal_set_agent_parts' => true,
       'agent_deal_ajaxsave' => true,
       'agent_deal_new' => true,
       'agent_deal_new_save' => true,
       'agent_deal_new_getpersonrow' => true,
       'agent_deal_new_getorganizationrow' => true,
       'agent_deal_create_setpersonrow' => true,
       'agent_deal_new_setpersonrow' => true,
       'agent_deal_new_setorganizationrow' => true,
       'agent_deal_create_setorganizationrow' => true,
       'agent_publish_getsectiondata' => true,
       'agent_publish_ratingwhovoted' => true,
       'agent_publish_whoviewed' => true,
       'agent_publish_save_stickysearchwords' => true,
       'agent_publish_validatingcontent' => true,
       'agent_feedback_validatingcontent' => true,
       'agent_feedback_validatingcomments' => true,
       'agent_publish_validatingcontent_approve' => true,
       'agent_publish_validatingcontent_disapprove' => true,
       'agent_publish_validatingcontent_mass' => true,
       'agent_publish_validatingcontent_next' => true,
       'agent_publish_listcomments' => true,
       'agent_publish_validatingcomments' => true,
       'agent_publish_approve_comment' => true,
       'agent_publish_delete_comment' => true,
       'agent_publish_comment_info' => true,
       'agent_publish_comment_save' => true,
       'agent_public_comment_newticketinfo' => true,
       'agent_publish_validatingcomments_mass' => true,
       'agent_publish_savecats' => true,
       'agent_publish_cats_adddel' => true,
       'agent_publish_cats_addcat' => true,
       'agent_publish_cats_updateorders' => true,
       'agent_publish_cats_updatetitles' => true,
       'agent_publish_cats_update' => true,
       'agent_publish_cats_updatestructure' => true,
       'agent_publish_cats_newform' => true,
       'agent_publish_cats_newform_save' => true,
       'agent_public_drafts' => true,
       'agent_public_drafts_mass' => true,
       'agent_publish_search' => true,
       'agent_kb_newarticle_save' => true,
       'agent_kb_newarticle' => true,
       'agent_kb_article' => true,
       'agent_kb_ajaxsavecustomfields' => true,
       'agent_kb_article_info' => true,
       'agent_kb_article_revisionstab' => true,
       'agent_kb_article_ajaxsave' => true,
       'agent_kb_ajax_save_comment' => true,
       'agent_kb_ajax_labels_save' => true,
       'agent_kb_comparerevs' => true,
       'agent_kb_newpending' => true,
       'agent_kb_pending_remove' => true,
       'agent_kb_pending_info' => true,
       'agent_kb_pending' => true,
       'agent_kb_pending_massactions' => true,
       'agent_kb_list' => true,
       'agent_kb_cat' => true,
       'agent_kb_mass_save' => true,
       'agent_glossary_newword_json' => true,
       'agent_glossary_word_json' => true,
       'agent_glossary_saveword_json' => true,
       'agent_glossary_delword_json' => true,
       'agent_glossary_word_tip' => true,
       'agent_news_list' => true,
       'agent_news_view' => true,
       'agent_news_revisionstab' => true,
       'agent_news_save' => true,
       'agent_news_ajax_labels_save' => true,
       'agent_news_ajax_save_comment' => true,
       'agent_news_new_save' => true,
       'agent_news_new' => true,
       'agent_news_comparerevs' => true,
       'agent_downloads_list' => true,
       'agent_downloads_view' => true,
       'agent_downloads_info' => true,
       'agent_kb_downloads_revisionstab' => true,
       'agent_downloads_save' => true,
       'agent_downloads_ajax_labels_save' => true,
       'agent_downloads_ajax_save_comment' => true,
       'agent_downloads_new_save' => true,
       'agent_downloads_new' => true,
       'agent_downloads_comparerevs' => true,
       'agent_feedback_category' => true,
       'agent_feedback_status' => true,
       'agent_feedback_label' => true,
       'agent_feedback_filter' => true,
       'agent_feedback_massactions' => true,
       'agent_feedback_getsectiondata' => true,
       'agent_feedback_new' => true,
       'agent_feedback_new_save' => true,
       'agent_feedback_view' => true,
       'agent_feedback_comparerevs' => true,
       'agent_feedback_ajaxsavecustomfields' => true,
       'agent_feedback_who_voted' => true,
       'agent_feedback_save' => true,
       'agent_feedback_ajax_labels_save' => true,
       'agent_feedback_ajax_save_comment' => true,
       'agent_feedback_ajaxsavecomment' => true,
       'agent_feedback_ajaxsaveeditables' => true,
       'agent_feedback_ajaxupdatecat' => true,
       'agent_feedback_ajaxupdatestatus' => true,
       'agent_feedback_merge_overlay' => true,
       'agent_feedback_merge' => true,
       'agent_agentchat_getonlineagents' => true,
       'agent_agentchat_get_last_convo' => true,
       'agent_agentchat_send_message' => true,
       'agent_agentchat_send_agent_message' => true,
       'agent_agentchat_history' => true,
       'agent_agentchat_history_team' => true,
       'agent_agentchat_view' => true,
       'agent_agentchat_getsectiondata' => true,
       'agent_userchat_view' => true,
       'agent_userchat_save_fields' => true,
       'agent_userchat_blockuser' => true,
       'agent_userchat_unblockuser' => true,
       'agent_userchat_ajax_labels_save' => true,
       'agent_userchat_open_counts' => true,
       'agent_userchat_filterlist_group_counts' => true,
       'agent_userchat_filterlist' => true,
       'agent_userchat_list_new' => true,
       'agent_userchat_list_active' => true,
       'agent_userchat_send_messageview' => true,
       'agent_userchat_send_filemessage' => true,
       'agent_userchat_assign' => true,
       'agent_userchat_syncpart' => true,
       'agent_userchat_addpart' => true,
       'agent_userchat_end' => true,
       'agent_userchat_leave' => true,
       'agent_userchat_invite' => true,
       'agent_userchat_changeprop' => true,
       'agent_userchat_getsectiondata' => true,
       'agent_usertrack_winheadertable' => true,
       'agent_usertrack_view' => true,
       'agent_mediamanager' => true,
       'agent_mediamanager_upload' => true,
       'agent_mediamanager_browse' => true,
       'agent_textsnippets_widget_shell' => true,
       'agent_textsnippets_reloadclient' => true,
       'agent_textsnippets_reloadclient_batch' => true,
       'agent_textsnippets_filtersnippets' => true,
       'agent_textsnippets_getsnippet' => true,
       'agent_textsnippets_savesnippet' => true,
       'agent_textsnippets_delsnippet' => true,
       'agent_textsnippets_savecat' => true,
       'agent_textsnippets_delcat' => true,
       'agent_plugins_run' => true,
       'api' => true,
       'api_test' => true,
       'api_test_post' => true,
       'api_deskpro_time' => true,
       'api_deskpro_info' => true,
       'api_token_exchange' => true,
       'api_token_renew' => true,
       'api_misc_upload' => true,
       'api_misc_session_person' => true,
       'api_misc_rate_limit' => true,
       'api_tickets_new' => true,
       'api_tickets_ticket' => true,
       'api_tickets_ticket_post' => true,
       'api_tickets_ticket_delete' => true,
       'api_tickets_ticket_logs' => true,
       'api_tickets_ticket_messages' => true,
       'api_tickets_ticket_messages_post' => true,
       'api_tickets_ticket_message' => true,
       'api_tickets_ticket_message_details' => true,
       'api_tickets_ticket_undelete' => true,
       'api_tickets_ticket_split' => true,
       'api_tickets_ticket_claim' => true,
       'api_tickets_ticket_merge' => true,
       'api_tickets_ticket_spam' => true,
       'api_tickets_ticket_unspam' => true,
       'api_tickets_ticket_lock' => true,
       'api_tickets_ticket_unlock' => true,
       'api_tickets_ticket_tasks' => true,
       'api_tickets_ticket_tasks_post' => true,
       'api_tickets_ticket_billing_charges' => true,
       'api_tickets_ticket_billing_charges_post' => true,
       'api_tickets_ticket_billing_charge' => true,
       'api_tickets_ticket_billing_charge_delete' => true,
       'api_tickets_ticket_slas' => true,
       'api_tickets_ticket_slas_post' => true,
       'api_tickets_ticket_sla' => true,
       'api_tickets_ticket_sla_delete' => true,
       'api_tickets_ticket_participants' => true,
       'api_tickets_ticket_participants_post' => true,
       'api_tickets_ticket_participant' => true,
       'api_tickets_ticket_participant_delete' => true,
       'api_tickets_ticket_labels' => true,
       'api_tickets_ticket_labels_post' => true,
       'api_tickets_ticket_label' => true,
       'api_tickets_ticket_label_delete' => true,
       'api_tickets_fields' => true,
       'api_tickets_departments' => true,
       'api_tickets_products' => true,
       'api_tickets_categories' => true,
       'api_tickets_priorities' => true,
       'api_tickets_workflows' => true,
       'api_tickets_slas' => true,
       'api_tickets_sla' => true,
       'api_tickets_sla_people' => true,
       'api_tickets_sla_organizations' => true,
       'api_textsnippets_list' => true,
       'api_textsnippets_new' => true,
       'api_textsnippets_edit' => true,
       'api_textsnippets_del' => true,
       'api_textsnippets_get' => true,
       'api_textsnippets_cats_list' => true,
       'api_textsnippets_cats_new' => true,
       'api_textsnippets_cats_edit' => true,
       'api_textsnippets_cats_get' => true,
       'api_textsnippets_cats_del' => true,
       'api_open_tickets_newticketmessage' => true,
       'api_tickets' => true,
       'api_tickets_filters' => true,
       'api_tickets_filter_counts' => true,
       'api_tickets_filter' => true,
       'api_people' => true,
       'api_people_post' => true,
       'api_people_person' => true,
       'api_people_person_post' => true,
       'api_people_person_delete' => true,
       'api_people_person_merge' => true,
       'api_people_person_picture' => true,
       'api_people_person_picture_post' => true,
       'api_people_person_picture_delete' => true,
       'api_people_person_emails' => true,
       'api_people_person_emails_post' => true,
       'api_people_person_email' => true,
       'api_people_person_email_post' => true,
       'api_people_person_email_delete' => true,
       'api_people_person_vcard' => true,
       'api_people_person_tickets' => true,
       'api_people_person_chats' => true,
       'api_people_person_activity_stream' => true,
       'api_people_person_reset_password' => true,
       'api_people_person_clear_session' => true,
       'api_people_person_slas' => true,
       'api_people_person_slas_post' => true,
       'api_people_person_sla' => true,
       'api_people_person_sla_delete' => true,
       'api_people_person_notes' => true,
       'api_people_person_notes_post' => true,
       'api_people_person_billing_charges' => true,
       'api_people_person_contact_details' => true,
       'api_people_person_contact_details_post' => true,
       'api_people_person_contact_detail' => true,
       'api_people_person_contact_detail_delete' => true,
       'api_people_person_groups' => true,
       'api_people_person_groups_post' => true,
       'api_people_person_group' => true,
       'api_people_person_group_delete' => true,
       'api_people_person_labels' => true,
       'api_people_person_labels_post' => true,
       'api_people_person_label' => true,
       'api_people_person_label_delete' => true,
       'api_people_fields' => true,
       'api_people_groups' => true,
       'api_organizations' => true,
       'api_organizations_post' => true,
       'api_organizations_organization' => true,
       'api_organizations_organization_post' => true,
       'api_organizations_organization_delete' => true,
       'api_organizations_organization_picture' => true,
       'api_organizations_organization_picture_post' => true,
       'api_organizations_organization_picture_delete' => true,
       'api_organizations_organization_activity_stream' => true,
       'api_organizations_organization_members' => true,
       'api_organizations_organization_tickets' => true,
       'api_organizations_organization_chats' => true,
       'api_organizations_organization_slas' => true,
       'api_organizations_organization_slas_post' => true,
       'api_organizations_organization_sla' => true,
       'api_organizations_organization_sla_delete' => true,
       'api_organizations_organization_billing_charges' => true,
       'api_organizations_organization_email_domains' => true,
       'api_organizations_organization_email_domains_post' => true,
       'api_organizations_organization_email_domain' => true,
       'api_organizations_organization_email_domain_move_users' => true,
       'api_organizations_organization_email_domain_move_taken_users' => true,
       'api_organizations_organization_email_domain_delete' => true,
       'api_organizations_organization_contact_details' => true,
       'api_organizations_organization_contact_details_post' => true,
       'api_organizations_organization_contact_detail' => true,
       'api_organizations_organization_contact_detail_delete' => true,
       'api_organizations_organization_groups' => true,
       'api_organizations_organization_groups_post' => true,
       'api_organizations_organization_group' => true,
       'api_organizations_organization_group_delete' => true,
       'api_organizations_organization_labels' => true,
       'api_organizations_organization_labels_post' => true,
       'api_organizations_organization_label' => true,
       'api_organizations_organization_label_delete' => true,
       'api_organizations_fields' => true,
       'api_organizations_groups' => true,
       'api_chats' => true,
       'api_chats_chat' => true,
       'api_chats_chat_post' => true,
       'api_chats_chat_leave' => true,
       'api_chats_chat_end' => true,
       'api_chats_chat_messages' => true,
       'api_chats_chat_messages_post' => true,
       'api_chats_chat_participants' => true,
       'api_chats_chat_participants_post' => true,
       'api_chats_chat_participant' => true,
       'api_chats_chat_participant_delete' => true,
       'api_chats_chat_labels' => true,
       'api_chats_chat_labels_post' => true,
       'api_chats_chat_label' => true,
       'api_chats_chat_label_delete' => true,
       'api_downloads' => true,
       'api_downloads_post' => true,
       'api_downloads_download' => true,
       'api_downloads_download_post' => true,
       'api_downloads_download_delete' => true,
       'api_downloads_download_comments' => true,
       'api_downloads_download_comments_new' => true,
       'api_downloads_download_comments_comment' => true,
       'api_downloads_download_comments_comment_post' => true,
       'api_downloads_download_comments_comment_delete' => true,
       'api_downloads_download_labels' => true,
       'api_downloads_download_labels_post' => true,
       'api_downloads_download_label' => true,
       'api_downloads_download_label_delete' => true,
       'api_downloads_validating_comments' => true,
       'api_downloads_categories' => true,
       'api_downloads_categories_post' => true,
       'api_downloads_category' => true,
       'api_downloads_category_post' => true,
       'api_downloads_category_delete' => true,
       'api_downloads_category_downloads' => true,
       'api_downloads_category_groups' => true,
       'api_downloads_category_groups_post' => true,
       'api_downloads_category_group' => true,
       'api_downloads_category_group_delete' => true,
       'api_news' => true,
       'api_news_post' => true,
       'api_news_news' => true,
       'api_news_news_post' => true,
       'api_news_news_delete' => true,
       'api_news_news_comments' => true,
       'api_news_news_comments_new' => true,
       'api_news_news_comments_comment' => true,
       'api_news_news_comments_comment_post' => true,
       'api_news_news_comments_comment_delete' => true,
       'api_news_news_labels' => true,
       'api_news_news_labels_post' => true,
       'api_news_news_label' => true,
       'api_news_news_label_delete' => true,
       'api_news_validating_comments' => true,
       'api_news_categories' => true,
       'api_news_categories_post' => true,
       'api_news_category' => true,
       'api_news_category_post' => true,
       'api_news_category_delete' => true,
       'api_news_category_news' => true,
       'api_news_category_groups' => true,
       'api_news_category_groups_post' => true,
       'api_news_category_group' => true,
       'api_news_category_group_delete' => true,
       'api_kb' => true,
       'api_kb_post' => true,
       'api_kb_article' => true,
       'api_kb_article_post' => true,
       'api_kb_article_delete' => true,
       'api_kb_article_votes' => true,
       'api_kb_article_comments' => true,
       'api_kb_article_comments_new' => true,
       'api_kb_article_comments_comment' => true,
       'api_kb_article_comments_comment_post' => true,
       'api_kb_article_comments_comment_delete' => true,
       'api_kb_article_attachments' => true,
       'api_kb_article_attachments_post' => true,
       'api_kb_article_attachment' => true,
       'api_kb_article_attachment_delete' => true,
       'api_kb_article_labels' => true,
       'api_kb_article_labels_post' => true,
       'api_kb_article_label' => true,
       'api_kb_article_label_delete' => true,
       'api_kb_validating_comments' => true,
       'api_kb_categories' => true,
       'api_kb_categories_post' => true,
       'api_kb_category' => true,
       'api_kb_category_post' => true,
       'api_kb_category_delete' => true,
       'api_kb_category_articles' => true,
       'api_kb_category_groups' => true,
       'api_kb_category_groups_post' => true,
       'api_kb_category_group' => true,
       'api_kb_category_group_delete' => true,
       'api_kb_fields' => true,
       'api_kb_products' => true,
       'api_feedback' => true,
       'api_feedback_post' => true,
       'api_feedback_feedback' => true,
       'api_feedback_feedback_post' => true,
       'api_feedback_feedback_delete' => true,
       'api_feedback_feedback_votes' => true,
       'api_feedback_feedback_comments' => true,
       'api_feedback_feedback_comments_new' => true,
       'api_feedback_feedback_comments_comment' => true,
       'api_feedback_feedback_comments_comment_post' => true,
       'api_feedback_feedback_comments_comment_delete' => true,
       'api_feedback_feedback_merge' => true,
       'api_feedback_feedback_attachments' => true,
       'api_feedback_feedback_attachments_post' => true,
       'api_feedback_feedback_attachment' => true,
       'api_feedback_feedback_attachment_delete' => true,
       'api_feedback_feedback_labels' => true,
       'api_feedback_feedback_labels_post' => true,
       'api_feedback_feedback_label' => true,
       'api_feedback_feedback_label_delete' => true,
       'api_feedback_validating_comments' => true,
       'api_feedback_categories' => true,
       'api_feedback_status_categories' => true,
       'api_feedback_user_categories' => true,
       'api_tasks' => true,
       'api_tasks_post' => true,
       'api_tasks_task' => true,
       'api_tasks_task_post' => true,
       'api_tasks_task_delete' => true,
       'api_tasks_task_associations' => true,
       'api_tasks_task_associations_post' => true,
       'api_tasks_task_associated_item' => true,
       'api_tasks_task_comments' => true,
       'api_tasks_task_comments_post' => true,
       'api_tasks_task_comment' => true,
       'api_tasks_task_associated_item_delete' => true,
       'api_tasks_task_labels' => true,
       'api_tasks_task_labels_post' => true,
       'api_tasks_task_label' => true,
       'api_tasks_task_label_delete' => true,
       'api_glossary' => true,
       'api_glossary_lookup' => true,
       'api_glossary_post' => true,
       'api_glossary_word' => true,
       'api_glossary_word_delete' => true,
       'api_glossary_definition' => true,
       'api_glossary_definition_post' => true,
       'api_glossary_definition_delete' => true,
       'api_dismiss_activity' => true,
       'api_get_activity' => true,
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

    private function getagentRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/',  ),));
    }

    private function getagent_savedomRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::saveDomAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/save-dom.json',  ),));
    }

    private function getagent_combined_sectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::getCombinedSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/get-combined-section-data.json',  ),));
    }

    private function getagent_load_recent_tabsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::loadRecentTabsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ui/load-recent-tabs.json',  ),));
    }

    private function getagent_accept_uploadRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::acceptTempUploadAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/misc/accept-upload',  ),));
    }

    private function getagent_accept_redactor_image_uploadRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::acceptRedactorImageUploadAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/misc/accept-redactor-image-upload',  ),));
    }

    private function getagent_redactor_autosaveRouteInfo()
    {
        return array(array (  0 => 'content_type',  1 => 'content_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::redactorAutosaveAction',), array (  'content_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'content_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'content_type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/misc/redactor-autosave',  ),));
    }

    private function getagent_submit_deskpro_feedbackRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::submitDeskproFeedbackAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/misc/submit-deskpro-feedback.json',  ),));
    }

    private function getagent_parse_vcardRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::parseVCardAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/misc/parse-vcard',  ),));
    }

    private function getagent_get_server_timeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::getServerTimeAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/misc/get-server-time',  ),));
    }

    private function getagent_ajax_save_prefsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::ajaxSavePrefsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/misc/ajax-save-prefs',  ),));
    }

    private function getagent_ajax_labels_autocompleteRouteInfo()
    {
        return array(array (  0 => 'label_type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::ajaxLabelsAutocompleteAction',), array (  'label_type' => '[a-z]+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[a-z]+',    3 => 'label_type',  ),  1 =>   array (    0 => 'text',    1 => '/agent/misc/ajax-labels',  ),));
    }

    private function getagent_interface_data_jsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::getInterfaceDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/misc/interface-data.js',  ),));
    }

    private function getagent_dismiss_help_messageRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::dismissHelpMessageAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/misc/dismiss-help-message',  ),));
    }

    private function getagent_set_agent_statusRouteInfo()
    {
        return array(array (  0 => 'status',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::setAgentStatusAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'status',  ),  1 =>   array (    0 => 'text',    1 => '/agent/misc/set-agent-status',  ),));
    }

    private function getagent_proxyRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::proxyAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/misc/proxy',  ),));
    }

    private function getagent_load_version_noticeRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::loadVersionNoticeAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/log.html',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/misc/version-notices',  ),));
    }

    private function getagent_dismiss_version_noticeRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::dismissVersionNoticeAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/dismiss.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/misc/version-notices',  ),));
    }

    private function getagent_redirect_outRouteInfo()
    {
        return array(array (  0 => 'url',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::redirectExternalAction',), array (  'url' => '.+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '.+',    3 => 'url',  ),  1 =>   array (    0 => 'text',    1 => '/agent/redirect-out',  ),));
    }

    private function getagent_redirect_out_infoRouteInfo()
    {
        return array(array (  0 => 'url',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::redirectExternalInfoAction',), array (  'url' => '.+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '.+',    3 => 'url',  ),  1 =>   array (    0 => 'text',    1 => '/agent/redirect-out-info',  ),));
    }

    private function getagent_password_confirm_codeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MiscController::getPasswordConfirmCodeAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/password-confirm-code.json',  ),));
    }

    private function getagent_quicksearchRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MainController::quickSearchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/quick-search.json',  ),));
    }

    private function getagent_searchRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SearchController::searchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/search/search',  ),));
    }

    private function getagent_search_jsonRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SearchController::searchResultsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/search/search.json',  ),));
    }

    private function getagent_recyclebinRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\RecycleBinController::listAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/recycle-bin',  ),));
    }

    private function getagent_recyclebin_moreRouteInfo()
    {
        return array(array (  0 => 'type',  1 => 'page',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\RecycleBinController::listMoreAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'page',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/recycle-bin',  ),));
    }

    private function getagent_login_preload_sourcesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::preloadSourcesAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/login/preload-sources',  ),));
    }

    private function getagent_browser_requirementsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::browserRequirementsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/browser-requirements',  ),));
    }

    private function getagent_browser_requirements_ie_compatRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::ieCompatModeAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/browser-requirements/ie-compat-mode',  ),));
    }

    private function getagent_loginRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::indexAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/login',  ),));
    }

    private function getagent_login_authenticate_localRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::authenticateLocalAction',  'usersource_id' => 0,), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/login/authenticate-password',  ),));
    }

    private function getagent_login_adminloginRouteInfo()
    {
        return array(array (  0 => 'code',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::authAdminLoginAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'code',  ),  1 =>   array (    0 => 'text',    1 => '/agent/login/admin-login',  ),));
    }

    private function getagent_send_lostRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\LoginController::sendResetPasswordAction',  '_format' => 'json',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/login/send-lost.json',  ),));
    }

    private function getagent_settingsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::profileAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings',  ),));
    }

    private function getagent_settings_profile_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::profileSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/profile/save.json',  ),));
    }

    private function getagent_settings_profile_savewelcomeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::profileSaveWelcomeAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/profile/save-welcome.json',  ),));
    }

    private function getagent_settings_signatureRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::signatureAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/signature',  ),));
    }

    private function getagent_settings_signature_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::signatureSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/signature/save.json',  ),));
    }

    private function getagent_settings_profile_updatetimezoneRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::updateTimezoneAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/profile/update-timezone.json',  ),));
    }

    private function getagent_settings_ticketnotifRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketNotificationsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-notifications',  ),));
    }

    private function getagent_settings_ticketnotif_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketNotificationsSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-notifications/save.json',  ),));
    }

    private function getagent_settings_othernotifRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::otherNotificationsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/other-notifications',  ),));
    }

    private function getagent_settings_othernotif_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::otherNotificationsSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/other-notifications/save.json',  ),));
    }

    private function getagent_settings_ticketmacrosRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacrosAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-macros',  ),));
    }

    private function getagent_settings_ticketmacros_editRouteInfo()
    {
        return array(array (  0 => 'macro_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacroEditAction',), array (  'macro_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/edit',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'macro_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-macros',  ),));
    }

    private function getagent_settings_ticketmacros_edit_saveRouteInfo()
    {
        return array(array (  0 => 'macro_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacroEditSaveAction',), array (  'macro_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'macro_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-macros',  ),));
    }

    private function getagent_settings_ticketmacros_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacroEditAction',  'macro_id' => 0,), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-macros/new',  ),));
    }

    private function getagent_settings_ticketmacros_delRouteInfo()
    {
        return array(array (  0 => 'macro_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketMacroDeleteAction',), array (  'macro_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/delete',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'macro_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-macros',  ),));
    }

    private function getagent_settings_ticketfiltersRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFiltersAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-filters',  ),));
    }

    private function getagent_settings_ticketfilters_editRouteInfo()
    {
        return array(array (  0 => 'filter_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFilterEditAction',), array (  'filter_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/edit',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'filter_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-filters',  ),));
    }

    private function getagent_settings_ticketfilters_edit_saveRouteInfo()
    {
        return array(array (  0 => 'filter_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFilterEditSaveAction',), array (  'filter_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/edit/save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'filter_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-filters',  ),));
    }

    private function getagent_settings_ticketfilters_delRouteInfo()
    {
        return array(array (  0 => 'filter_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFilterDeleteAction',), array (  'filter_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/delete',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'filter_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-filters',  ),));
    }

    private function getagent_settings_ticketfilters_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketFilterEditAction',  'filter_id' => 0,), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-filters/new-filter',  ),));
    }

    private function getagent_settings_ticketslasRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\SettingsController::ticketSlasAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/settings/ticket-slas',  ),));
    }

    private function getagent_people_validate_emailRouteInfo()
    {
        return array(array (  0 => 'id',  1 => 'security_token',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::validateEmailAddressAction',), array (  'id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'security_token',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/people/validate-email',  ),));
    }

    private function getagent_people_viewRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::viewAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_view_basicjsonRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::getBasicInfoAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/basic.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_viewsessionRouteInfo()
    {
        return array(array (  0 => 'session_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::viewSessionAction',), array (  'session_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'session_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/people/session',  ),));
    }

    private function getagent_people_validate_listRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::validateListAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people/validate/list',  ),));
    }

    private function getagent_people_validate_approveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::validateApproveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people/validate/approve',  ),));
    }

    private function getagent_people_validate_deleteRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::validateDeleteAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people/validate/delete',  ),));
    }

    private function getagent_people_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::newPersonAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people/new',  ),));
    }

    private function getagent_people_new_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::newPersonSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people/new/save',  ),));
    }

    private function getagent_people_ajaxsaveRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_savecontactdataRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::saveContactDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/save-contact-data.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_unban_emailRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'email_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::unbanEmailAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'email_id',  ),  2 =>   array (    0 => 'text',    1 => '/unban-email',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  4 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_merge_overlayRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'other_person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::mergeOverlayAction',), array (  'person_id' => '\\d+',  'other_person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'other_person_id',  ),  1 =>   array (    0 => 'text',    1 => '/merge-overlay',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_mergeRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'other_person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::mergeAction',), array (  'person_id' => '\\d+',  'other_person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'other_person_id',  ),  1 =>   array (    0 => 'text',    1 => '/merge',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_deleteRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'security_token',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::deletePersonAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'security_token',  ),  1 =>   array (    0 => 'text',    1 => '/delete',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_login_asRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::loginAsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/login-as',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_changepicoverlayRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::changePictureOverlayAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/change-picture-overlay',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_ajaxsave_noteRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveNoteAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-note',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_people_ajaxsave_organizationRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveOrganizationAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-organization',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/people',  ),));
    }

    private function getagent_person_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveLabelsAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/person',  ),));
    }

    private function getagent_person_ajaxsavecustomfieldsRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PersonController::ajaxSaveCustomFieldsAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-custom-fields',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/person',  ),));
    }

    private function getagent_peoplesearch_usergroupRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::showUsergroupAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/people-search/usergroup',  ),));
    }

    private function getagent_peoplesearch_organizationRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::showOrganizationMembersAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/people-search/organization',  ),));
    }

    private function getagent_peoplesearch_customfilterRouteInfo()
    {
        return array(array (  0 => 'letter',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::searchAction',  'letter' => '*',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'letter',  ),  1 =>   array (    0 => 'text',    1 => '/agent/people-search/search',  ),));
    }

    private function getagent_peoplesearch_getpageRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::getPeoplePageAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people-search/get-page',  ),));
    }

    private function getagent_peoplesearch_performquickRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::performQuickSearchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people-search/search-quick',  ),));
    }

    private function getagent_peoplesearch_quickfindRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::quickFindAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people-search/quick-find',  ),));
    }

    private function getagent_peoplesearch_quickfind_searchRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::quickFindSearchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people-search/quick-find-search.json',  ),));
    }

    private function getagent_peoplesearch_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people/get-section-data.json',  ),));
    }

    private function getagent_peoplesearch_getsectiondata_reloadcountsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::reloadCountsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people/get-section-data/reload-counts.json',  ),));
    }

    private function getagent_peoplesearch_reload_label_sectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PeopleSearchController::reloadLabelDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/people/get-section-data/labels.json',  ),));
    }

    private function getagent_org_viewRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::viewAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::newOrganizationAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/organizations/new',  ),));
    }

    private function getagent_org_new_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::newOrganizationSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/organizations/new/save',  ),));
    }

    private function getagent_org_ajaxsaveRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::ajaxSaveAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_domain_assignRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::assignDomainAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/assign-domain',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_domain_unassignRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::unassignDomainAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/unassign-domain',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_domain_moveusersRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::moveDomainUsersAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/domain/move-users',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_domain_moveusers_existRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::moveTakenDomainUsersAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/domain/reassign-users',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_save_member_posRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::savePositionAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/save-member-pos',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_save_member_managerRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::saveManagerAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/save-member-manager',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_savecontactdataRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::saveContactDataAction',), array (  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/save-contact-data.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_deleteRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'security_token',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::deleteOrganizationAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'security_token',  ),  1 =>   array (    0 => 'text',    1 => '/delete',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_ajaxsave_noteRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::ajaxSaveNoteAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-note',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::ajaxSaveLabelsAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_ajaxsavecustomfieldsRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::ajaxSaveCustomFieldsAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-custom-fields',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_org_changepicoverlayRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationController::changePictureOverlayAction',), array (  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/change-picture-overlay',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/organizations',  ),));
    }

    private function getagent_orgsearch_getpageRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationSearchController::getOrgPageAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/organization-search/get-page',  ),));
    }

    private function getagent_orgsearch_customfilterRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationSearchController::searchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/organization-search/search',  ),));
    }

    private function getagent_orgsearch_quicknamesearchRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationSearchController::performQuickNameSearchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/organization-search/quick-name-search.json',  ),));
    }

    private function getagent_orgsearch_namelookupRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\OrganizationSearchController::checkNameAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/organization-search/name-lookup.json',  ),));
    }

    private function getagent_ticketsearch_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/get-section-data.json',  ),));
    }

    private function getagent_ticketsearch_getsection_reloadarchiveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::reloadArchiveSectionAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/get-section-data/reload-archive-section',  ),));
    }

    private function getagent_ticketsearch_refreshsectiondataRouteInfo()
    {
        return array(array (  0 => 'section',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::refreshSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'section',  ),  2 =>   array (    0 => 'text',    1 => '/agent/ticket-search/refresh-section-data',  ),));
    }

    private function getagent_ticketsearch_getlabelssectionRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getLabelsSectionAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/get-section/labels',  ),));
    }

    private function getagent_ticketsearch_getfiltercountsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getFilterCountsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/get-filter-counts.json',  ),));
    }

    private function getagent_ticketsearch_getslacountsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getSlaCountsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/get-sla-counts.json',  ),));
    }

    private function getagent_ticketsearch_groupticketsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::groupTicketsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/group-tickets.json',  ),));
    }

    private function getagent_ticketsearch_getpageRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getTicketPageAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/get-page',  ),));
    }

    private function getagent_ticketsearch_getflaggedsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getFlaggedSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/tickets/get-flagged-section-data.json',  ),));
    }

    private function getagent_ticketsearch_runcustomfilterRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::runCustomFilterAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/custom-filter/run',  ),));
    }

    private function getagent_ticketsearch_quicksearchRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::quickSearchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/quick-search',  ),));
    }

    private function getagent_ticketsearch_singleticketrowRouteInfo()
    {
        return array(array (  0 => 'content_type',  1 => 'content_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::getSingleTicketRowAction',), array (  'content_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'content_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'content_type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/ticket-search/single-ticket-row',  ),));
    }

    private function getagent_ticketsearch_runfilterRouteInfo()
    {
        return array(array (  0 => 'filter_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::runFilterAction',), array (  'filter_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'filter_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/ticket-search/filter',  ),));
    }

    private function getagent_ticketsearch_runnamedfilterRouteInfo()
    {
        return array(array (  0 => 'filter_name',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::runNamedFilterAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'filter_name',  ),  1 =>   array (    0 => 'text',    1 => '/agent/ticket-search/filter',  ),));
    }

    private function getagent_ticketsearch_runslaRouteInfo()
    {
        return array(array (  0 => 'sla_id',  1 => 'sla_status',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::runSlaAction',  'sla_status' => '',), array (  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'sla_status',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/ticket-search/sla',  ),));
    }

    private function getagent_ticketsearch_ajax_get_macroRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxGetMacroAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/ajax-get-macro',  ),));
    }

    private function getagent_ticketsearch_ajax_get_macro_actionsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxGetMacroActionsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/ajax-get-macro-actions',  ),));
    }

    private function getagent_ticketsearch_ajax_save_actionsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxSaveActionsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/ajax-save-actions',  ),));
    }

    private function getagent_ticketsearch_ajax_delete_ticketsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxDeleteTicketsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/ajax-delete-tickets',  ),));
    }

    private function getagent_ticketsearch_ajax_release_locksRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketSearchController::ajaxReleaseLocksAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/ticket-search/ajax-release-locks',  ),));
    }

    private function getagent_ticket_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::newAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/tickets/new',  ),));
    }

    private function getagent_ticket_new_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::newSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/tickets/new/save',  ),));
    }

    private function getagent_ticket_new_getpersonrowRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::newticketGetPersonRowAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/tickets/new/get-person-row',  ),));
    }

    private function getagent_ticket_getmessagetplRouteInfo()
    {
        return array(array (  0 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::getTicketMessageTemplateAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets/get-message-template',  ),));
    }

    private function getagent_ticket_update_draftsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::updateDraftsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/tickets/update-drafts',  ),));
    }

    private function getagent_ticket_getmessagetextRouteInfo()
    {
        return array(array (  0 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxGetMessageTextAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/get-message-text.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets/messages',  ),));
    }

    private function getagent_ticket_getfullmessageRouteInfo()
    {
        return array(array (  0 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxGetFullMessageAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/get-full-message.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets/messages',  ),));
    }

    private function getagent_ticket_savemessagetextRouteInfo()
    {
        return array(array (  0 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveMessageTextAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/save-message-text.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets/messages',  ),));
    }

    private function getagent_ticket_setmessagenoteRouteInfo()
    {
        return array(array (  0 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSetNoteAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/set-message-note.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets/messages',  ),));
    }

    private function getagent_ticket_message_attachmentsRouteInfo()
    {
        return array(array (  0 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::getMessageAttachmentsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/attachments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets/messages',  ),));
    }

    private function getagent_ticket_message_attachment_deleteRouteInfo()
    {
        return array(array (  0 => 'message_id',  1 => 'attachment_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteMessageAttachmentAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/delete',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'attachment_id',  ),  2 =>   array (    0 => 'text',    1 => '/attachments',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  4 =>   array (    0 => 'text',    1 => '/agent/tickets/messages',  ),));
    }

    private function getagent_ticket_message_deleteRouteInfo()
    {
        return array(array (  0 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteMessageAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/delete',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets/messages',  ),));
    }

    private function getagent_ticket_viewRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::viewAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_loadlogsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::loadTicketLogsAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/load-logs',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_loadattachlistRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::loadAttachListAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/load-attach-list',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_download_debug_reportRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::downloadTicketDebugAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/download-debug-report',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_messagepageRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'page',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::getMessagePageAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'page',  ),  1 =>   array (    0 => 'text',    1 => '/message-page',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_doupdateRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::updateViewsAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/update-views.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_lockRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::lockTicketAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/lock-ticket.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_unlockRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::unlockTicketAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/unlock-ticket.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_release_lockRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::releaseLockAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/release-lock.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_splitRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::splitAction',  'message_id' => 0,), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'message_id',  ),  1 =>   array (    0 => 'text',    1 => '/split',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_split_saveRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::splitSaveAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/split-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_merge_overlayRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'other_ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::mergeOverlayAction',), array (  'ticket_id' => '\\d+',  'other_ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'other_ticket_id',  ),  1 =>   array (    0 => 'text',    1 => '/merge-overlay',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_fwd_overlayRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::forwardOverlayAction',), array (  'ticket_id' => '\\d+',  'message_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'message_id',  ),  1 =>   array (    0 => 'text',    1 => '/forward',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_fwd_sendRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::forwardSendAction',), array (  'ticket_id' => '\\d+',  'message_id' => '\\d+',  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/send',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/forward',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  4 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_mergeRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'other_ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::mergeAction',), array (  'ticket_id' => '\\d+',  'other_ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'other_ticket_id',  ),  1 =>   array (    0 => 'text',    1 => '/merge',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_changeuser_overlayRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::changeUserOverlayAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/change-user-overlay',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_changeuser_overlay_previewRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'new_person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::changeUserOverlayPreviewAction',), array (  'ticket_id' => '\\d+',  'new_person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'new_person_id',  ),  1 =>   array (    0 => 'text',    1 => '/change-user-overlay/preview',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_changeuserRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::changeUserAction',), array (  'ticket_id' => '\\d+',  'new_person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/change-user',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajaxsavecustomfieldsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveCustomFieldsAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-custom-fields',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajaxsavereplyRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveReplyAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-reply',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajaxsavesubjectRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveSubjectAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-subject.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajaxchangeuseremailRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxChangeUserEmailAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-change-email.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajaxsaveoptionsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveOptionsAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-options',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajaxsaveflaggedRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveFlaggedAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-flagged',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_addpartRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::addParticipantAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/add-part',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_set_agent_partsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::setAgentParticipantsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/set-agent-parts.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_delpartRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::removeParticipantAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/remove-part.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajaxtab_releated_contentRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxTabRelatedContentAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-tab-related-content',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveLabelsAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajax_get_macroRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxGetMacroAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-get-macro',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajax_apply_macroRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'macro_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::applyMacroAction',), array (  'ticket_id' => '\\d+',  'macro_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/apply-macro.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'macro_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_ajax_save_actionsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxSaveActionsAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-actions',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_message_rawRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::viewRawMessageAction',), array (  'ticket_id' => '\\d+',  'message_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/view-raw',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/message-details',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  4 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_message_windowRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'message_id',  2 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::viewMessageWindowAction',  'type' => 'normal',), array (  'ticket_id' => '\\d+',  'message_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  1 =>   array (    0 => 'text',    1 => '/window',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'message_id',  ),  3 =>   array (    0 => 'text',    1 => '/message-details',  ),  4 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  5 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_message_ajax_getquoteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'message_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::ajaxGetMessageQuoteAction',), array (  'ticket_id' => '\\d+',  'message_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-get-quote',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/message-details',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  4 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_saveagentpartsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::saveAgentPartsAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/save-agent-parts',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/ticket',  ),));
    }

    private function getagent_ticket_addchargeRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::addChargeAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/add-charge',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/ticket',  ),));
    }

    private function getagent_ticket_chargedeleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'charge_id',  2 => 'security_token',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteChargeAction',), array (  'ticket_id' => '\\d+',  'charge_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'security_token',  ),  1 =>   array (    0 => 'text',    1 => '/delete',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'charge_id',  ),  3 =>   array (    0 => 'text',    1 => '/charge',  ),  4 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  5 =>   array (    0 => 'text',    1 => '/agent/ticket',  ),));
    }

    private function getagent_ticket_addslaRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::addSlaAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/add-sla',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/ticket',  ),));
    }

    private function getagent_ticket_sladeleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'sla_id',  2 => 'security_token',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteSlaAction',), array (  'ticket_id' => '\\d+',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'security_token',  ),  1 =>   array (    0 => 'text',    1 => '/delete',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  3 =>   array (    0 => 'text',    1 => '/sla',  ),  4 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  5 =>   array (    0 => 'text',    1 => '/agent/ticket',  ),));
    }

    private function getagent_ticket_deleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::deleteAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/delete',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_ticket_spamRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TicketController::spamAction',), array (  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/spam',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tickets',  ),));
    }

    private function getagent_twitter_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::newTweetAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/new',  ),));
    }

    private function getagent_twitter_new_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::newTweetSaveAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/new/save',  ),));
    }

    private function getagent_twitter_mine_listRouteInfo()
    {
        return array(array (  0 => 'account_id',  1 => 'group',  2 => 'group_value',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listMineAction',  'group' => '',  'group_value' => '',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group_value',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/twitter/mine',  ),));
    }

    private function getagent_twitter_team_listRouteInfo()
    {
        return array(array (  0 => 'account_id',  1 => 'group',  2 => 'group_value',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listTeamAction',  'group' => '',  'group_value' => '',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group_value',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/twitter/team',  ),));
    }

    private function getagent_twitter_unassigned_listRouteInfo()
    {
        return array(array (  0 => 'account_id',  1 => 'group',  2 => 'group_value',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listUnassignedAction',  'group' => '',  'group_value' => '',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group_value',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/twitter/unassigned',  ),));
    }

    private function getagent_twitter_all_listRouteInfo()
    {
        return array(array (  0 => 'account_id',  1 => 'group',  2 => 'group_value',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listAllAction',  'group' => '',  'group_value' => '',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group_value',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/twitter/all',  ),));
    }

    private function getagent_twitter_sent_listRouteInfo()
    {
        return array(array (  0 => 'account_id',  1 => 'group',  2 => 'group_value',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listSentAction',  'group' => '',  'group_value' => '',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group_value',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/twitter/sent',  ),));
    }

    private function getagent_twitter_timeline_listRouteInfo()
    {
        return array(array (  0 => 'account_id',  1 => 'group',  2 => 'group_value',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::listTimelineAction',  'group' => '',  'group_value' => '',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group_value',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'group',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/twitter/timeline',  ),));
    }

    private function getagent_twitter_followers_listRouteInfo()
    {
        return array(array (  0 => 'account_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::listFollowersAction',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/twitter/followers',  ),));
    }

    private function getagent_twitter_followers_list_newRouteInfo()
    {
        return array(array (  0 => 'account_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::listNewFollowersAction',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/new',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/twitter/followers',  ),));
    }

    private function getagent_twitter_following_listRouteInfo()
    {
        return array(array (  0 => 'account_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::listFollowingAction',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/twitter/following',  ),));
    }

    private function getagent_twitter_status_ajaxmasssaveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxMassSaveAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-mass-save.json',  ),));
    }

    private function getagent_twitter_status_ajaxsave_noteRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveNoteAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-note.json',  ),));
    }

    private function getagent_twitter_status_ajaxsave_retweetRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveRetweetAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-retweet.json',  ),));
    }

    private function getagent_twitter_status_ajaxsave_unretweetRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveUnretweetAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-unretweet.json',  ),));
    }

    private function getagent_twitter_status_ajaxsave_replyRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveReplyAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-reply.json',  ),));
    }

    private function getagent_twitter_status_ajaxsave_archiveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveArchiveAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-archive.json',  ),));
    }

    private function getagent_twitter_status_ajaxsave_deleteRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveDeleteAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-delete.json',  ),));
    }

    private function getagent_twitter_status_ajaxsave_editRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveEditAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-edit',  ),));
    }

    private function getagent_twitter_status_ajaxsave_favoriteRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveFavoriteAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-favorite.json',  ),));
    }

    private function getagent_twitter_status_ajaxsave_assignRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::ajaxSaveAssignAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/ajax-assign.json',  ),));
    }

    private function getagent_twitter_status_tweet_overlayRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterStatusController::tweetOverlayAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/status/tweet-overlay',  ),));
    }

    private function getagent_twitter_userRouteInfo()
    {
        return array(array (  0 => 'user_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::viewAction',), array (  'user_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'user_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/twitter/user',  ),));
    }

    private function getagent_twitter_user_statusesRouteInfo()
    {
        return array(array (  0 => 'user_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::viewUserStatusesAction',), array (  'user_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/statuses',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'user_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/twitter/user',  ),));
    }

    private function getagent_twitter_user_followingRouteInfo()
    {
        return array(array (  0 => 'user_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::viewUserFollowingAction',), array (  'user_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/following',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'user_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/twitter/user',  ),));
    }

    private function getagent_twitter_user_followersRouteInfo()
    {
        return array(array (  0 => 'user_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::viewUserFollowersAction',), array (  'user_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/followers',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'user_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/twitter/user',  ),));
    }

    private function getagent_twitter_user_findRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::findAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/user/find',  ),));
    }

    private function getagent_twitter_user_message_overlayRouteInfo()
    {
        return array(array (  0 => 'user_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::messageOverlayAction',), array (  'user_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/message-overlay',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'user_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/twitter/user',  ),));
    }

    private function getagent_twitter_user_ajaxsave_followRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveFollowAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/user/ajax-follow.json',  ),));
    }

    private function getagent_twitter_user_ajaxsave_unfollowRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveUnfollowAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/user/ajax-unfollow.json',  ),));
    }

    private function getagent_twitter_user_ajaxsave_messageRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveMessageAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/user/ajax-message.json',  ),));
    }

    private function getagent_twitter_user_ajaxsave_archiveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveArchiveAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/user/ajax-archive.json',  ),));
    }

    private function getagent_twitter_user_ajaxsave_personRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSavePersonAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/user/ajax-person.json',  ),));
    }

    private function getagent_twitter_user_ajaxsave_organizationRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterUserController::ajaxSaveOrganizationAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/user/ajax-organization.json',  ),));
    }

    private function getagent_twitter_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/get-section-data.json',  ),));
    }

    private function getagent_twitter_updategroupingRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::updateGroupingAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/twitter/update-grouping.json',  ),));
    }

    private function getagent_twitter_run_searchRouteInfo()
    {
        return array(array (  0 => 'account_id',  1 => 'search_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::runSearchAction',), array (  'account_id' => '\\d+',  'search_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'search_id',  ),  1 =>   array (    0 => 'text',    1 => '/search',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/twitter',  ),));
    }

    private function getagent_twitter_search_deleteRouteInfo()
    {
        return array(array (  0 => 'account_id',  1 => 'security_token',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::deleteSearchAction',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'security_token',  ),  1 =>   array (    0 => 'text',    1 => '/search/delete',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/twitter',  ),));
    }

    private function getagent_twitter_new_searchRouteInfo()
    {
        return array(array (  0 => 'account_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TwitterController::newSearchAction',), array (  'account_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/search/new',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'account_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/twitter',  ),));
    }

    private function getagent_task_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::newAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/tasks/new',  ),));
    }

    private function getagent_task_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::createAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/agent/tasks/save',  ),));
    }

    private function getagent_task_deleteRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::deleteTaskAction',), array (  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/delete',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tasks',  ),));
    }

    private function getagent_tasksearch_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/tasks/get-section-data.json',  ),));
    }

    private function getagent_task_listRouteInfo()
    {
        return array(array (  0 => 'search_type',  1 => 'search_categoty',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::taskListAction',  'search_type' => NULL,  'search_categoty' => NULL,), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'search_categoty',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'search_type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tasks/list',  ),));
    }

    private function getagent_task_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::ajaxSaveLabelsAction',), array (  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tasks',  ),));
    }

    private function getagent_task_ajaxsave_commentRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::ajaxSaveCommentAction',), array (  'task_id' => '\\d+',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-comment',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tasks',  ),));
    }

    private function getagent_task_ajaxsaveRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TaskController::ajaxSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/tasks',  ),));
    }

    private function getagent_dealearch_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/deal/get-section-data.json',  ),));
    }

    private function getagent_deal_listRouteInfo()
    {
        return array(array (  0 => 'owner_type',  1 => 'deal_status',  2 => 'deal_type_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::dealListAction',  'owner_type' => NULL,  'deal_status' => NULL,  'deal_type_id' => NULL,), array (  'deal_type_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'deal_type_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'deal_status',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'owner_type',  ),  3 =>   array (    0 => 'text',    1 => '/agent/deals/list',  ),));
    }

    private function getagent_deal_viewRouteInfo()
    {
        return array(array (  0 => 'deal_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::viewAction',), array (  'deal_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'deal_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/deal',  ),));
    }

    private function getagent_deal_ajaxsave_noteRouteInfo()
    {
        return array(array (  0 => 'deal_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::ajaxSaveNoteAction',), array (  'deal_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-note',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'deal_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/deal',  ),));
    }

    private function getagent_deal_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'deal_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::ajaxSaveLabelsAction',), array (  'deal_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'deal_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/deal',  ),));
    }

    private function getagent_deal_ajaxsavecustomfieldsRouteInfo()
    {
        return array(array (  0 => 'deal_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::ajaxSaveCustomFieldsAction',), array (  'deal_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-custom-fields',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'deal_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/deal',  ),));
    }

    private function getagent_deal_set_agent_partsRouteInfo()
    {
        return array(array (  0 => 'deal_id',  1 => 'agent_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::setAgentParticipantsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/set-agent-parts.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'deal_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/deals',  ),));
    }

    private function getagent_deal_ajaxsaveRouteInfo()
    {
        return array(array (  0 => 'deal_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::ajaxSaveAction',), array (  'deal_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'deal_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/deals',  ),));
    }

    private function getagent_deal_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/deals/new',  ),));
    }

    private function getagent_deal_new_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/deals/new/save',  ),));
    }

    private function getagent_deal_new_getpersonrowRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealGetPersonRowAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/deals/new/get-person-row',  ),));
    }

    private function getagent_deal_new_getorganizationrowRouteInfo()
    {
        return array(array (  0 => 'org_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealGetOrganizationRowAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'org_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/deals/new/get-organization-row',  ),));
    }

    private function getagent_deal_create_setpersonrowRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealCreatePersonRowAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/deals/new/create-person-row',  ),));
    }

    private function getagent_deal_new_setpersonrowRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealSetPersonRowAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/deals/new/set-person-row',  ),));
    }

    private function getagent_deal_new_setorganizationrowRouteInfo()
    {
        return array(array (  0 => 'org_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealSetOrganizationRowAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'org_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/deals/new/set-organization-row',  ),));
    }

    private function getagent_deal_create_setorganizationrowRouteInfo()
    {
        return array(array (  0 => 'org_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DealController::newdealCreateOrganizationRowAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'org_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/deals/new/create-organization-row',  ),));
    }

    private function getagent_publish_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/publish/get-section-data.json',  ),));
    }

    private function getagent_publish_ratingwhovotedRouteInfo()
    {
        return array(array (  0 => 'object_type',  1 => 'object_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::ratingWhoVotedAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'object_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'object_type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/rating-who-voted',  ),));
    }

    private function getagent_publish_whoviewedRouteInfo()
    {
        return array(array (  0 => 'object_type',  1 => 'object_id',  2 => 'view_action',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::whoViewedAction',  'view_action' => 1,), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'view_action',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'object_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'object_type',  ),  3 =>   array (    0 => 'text',    1 => '/agent/publish/who-viewed',  ),));
    }

    private function getagent_publish_save_stickysearchwordsRouteInfo()
    {
        return array(array (  0 => 'type',  1 => 'content_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::saveStickySearchWordsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'content_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/save-sticky-search-words',  ),));
    }

    private function getagent_publish_validatingcontentRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listValidatingContentAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/publish/content/validating',  ),));
    }

    private function getagent_feedback_validatingcontentRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listValidatingFeedbackContentAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/feedback/content/validating',  ),));
    }

    private function getagent_feedback_validatingcommentsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listValidatingFeedbackCommentsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/feedback/comments/validating',  ),));
    }

    private function getagent_publish_validatingcontent_approveRouteInfo()
    {
        return array(array (  0 => 'type',  1 => 'content_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::approveContentAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'content_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  3 =>   array (    0 => 'text',    1 => '/agent/publish/content/approve',  ),));
    }

    private function getagent_publish_validatingcontent_disapproveRouteInfo()
    {
        return array(array (  0 => 'type',  1 => 'content_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::disapproveContentAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'content_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  3 =>   array (    0 => 'text',    1 => '/agent/publish/content/disapprove',  ),));
    }

    private function getagent_publish_validatingcontent_massRouteInfo()
    {
        return array(array (  0 => 'action',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::validatingMassActionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'action',  ),  1 =>   array (    0 => 'text',    1 => '/agent/publish/content/validating-mass-actions',  ),));
    }

    private function getagent_publish_validatingcontent_nextRouteInfo()
    {
        return array(array (  0 => 'type',  1 => 'content_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::nextValidatingContentAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'content_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  3 =>   array (    0 => 'text',    1 => '/agent/publish/content/get-next-validating',  ),));
    }

    private function getagent_publish_listcommentsRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listCommentsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  1 =>   array (    0 => 'text',    1 => '/agent/publish/comments/list',  ),));
    }

    private function getagent_publish_validatingcommentsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listValidatingCommentsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/publish/comments/validating',  ),));
    }

    private function getagent_publish_approve_commentRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'comment_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::approveCommentAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'comment_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/comments/approve',  ),));
    }

    private function getagent_publish_delete_commentRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'comment_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::deleteCommentAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'comment_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/comments/delete',  ),));
    }

    private function getagent_publish_comment_infoRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'comment_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::commentInfoAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'comment_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/comments/info',  ),));
    }

    private function getagent_publish_comment_saveRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'comment_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::saveCommentAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'comment_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/comments/save-comment',  ),));
    }

    private function getagent_public_comment_newticketinfoRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'comment_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::getNewTicketCommentInfoAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'comment_id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  3 =>   array (    0 => 'text',    1 => '/agent/publish/comments/new-ticket-info',  ),));
    }

    private function getagent_publish_validatingcomments_massRouteInfo()
    {
        return array(array (  0 => 'action',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::validatingCommentsMassActionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'action',  ),  1 =>   array (    0 => 'text',    1 => '/agent/publish/comments/validating-mass-actions',  ),));
    }

    private function getagent_publish_savecatsRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::saveCategoriesAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  1 =>   array (    0 => 'text',    1 => '/agent/publish/save-categories',  ),));
    }

    private function getagent_publish_cats_adddelRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::deleteCategoryAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/delete-category',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/categories',  ),));
    }

    private function getagent_publish_cats_addcatRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::addCategoryAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/add-category',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/categories',  ),));
    }

    private function getagent_publish_cats_updateordersRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::updateCategoryOrdersAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/update-orders',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/categories',  ),));
    }

    private function getagent_publish_cats_updatetitlesRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::updateCategoryTitlesAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/update-titles',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/categories',  ),));
    }

    private function getagent_publish_cats_updateRouteInfo()
    {
        return array(array (  0 => 'type',  1 => 'category_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::updateCategoryAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/update',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  3 =>   array (    0 => 'text',    1 => '/agent/publish/categories',  ),));
    }

    private function getagent_publish_cats_updatestructureRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::updateCategoryStructureAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/update-structure',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/categories',  ),));
    }

    private function getagent_publish_cats_newformRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::addCategoryFormAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/new-form',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/categories',  ),));
    }

    private function getagent_publish_cats_newform_saveRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::addCategoryFormSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/new-form/save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  2 =>   array (    0 => 'text',    1 => '/agent/publish/categories',  ),));
    }

    private function getagent_public_draftsRouteInfo()
    {
        return array(array (  0 => 'type',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::listDraftsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'type',  ),  1 =>   array (    0 => 'text',    1 => '/agent/publish/drafts',  ),));
    }

    private function getagent_public_drafts_massRouteInfo()
    {
        return array(array (  0 => 'action',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::draftsMassActionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'action',  ),  1 =>   array (    0 => 'text',    1 => '/agent/publish/drafts/mass-actions',  ),));
    }

    private function getagent_publish_searchRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PublishController::searchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/publish/search',  ),));
    }

    private function getagent_kb_newarticle_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::newArticleSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/kb/article/new/save',  ),));
    }

    private function getagent_kb_newarticleRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::newArticleAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/kb/article/new',  ),));
    }

    private function getagent_kb_articleRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::viewArticleAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/kb/article',  ),));
    }

    private function getagent_kb_ajaxsavecustomfieldsRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxSaveCustomFieldsAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-custom-fields',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/article',  ),));
    }

    private function getagent_kb_article_infoRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::articleInfoAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/info',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/article',  ),));
    }

    private function getagent_kb_article_revisionstabRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::viewRevisionsAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/view-revisions',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/article',  ),));
    }

    private function getagent_kb_article_ajaxsaveRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxSaveAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/article',  ),));
    }

    private function getagent_kb_ajax_save_commentRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxSaveCommentAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-comment',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/article',  ),));
    }

    private function getagent_kb_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxSaveLabelsAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/article',  ),));
    }

    private function getagent_kb_comparerevsRouteInfo()
    {
        return array(array (  0 => 'rev_old_id',  1 => 'rev_new_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::compareRevisionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'rev_new_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'rev_old_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/compare-revs',  ),));
    }

    private function getagent_kb_newpendingRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::newPendingArticleAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/kb/pending-articles/new',  ),));
    }

    private function getagent_kb_pending_removeRouteInfo()
    {
        return array(array (  0 => 'pending_article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::removePendingArticleAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/remove',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'pending_article_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/pending-articles',  ),));
    }

    private function getagent_kb_pending_infoRouteInfo()
    {
        return array(array (  0 => 'pending_article_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::pendingArticleInfoAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/info',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'pending_article_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/kb/pending-articles',  ),));
    }

    private function getagent_kb_pendingRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::listPendingArticlesAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/kb/pending-articles',  ),));
    }

    private function getagent_kb_pending_massactionsRouteInfo()
    {
        return array(array (  0 => 'action',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::pendingArticlesMassActionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'action',  ),  1 =>   array (    0 => 'text',    1 => '/agent/kb/pending-articles/mass-actions',  ),));
    }

    private function getagent_kb_listRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::listAction',  'category_id' => '0',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/kb/list',  ),));
    }

    private function getagent_kb_catRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::listAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/kb/category',  ),));
    }

    private function getagent_kb_mass_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\KbController::ajaxMassSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/kb/article/ajax-mass-save',  ),));
    }

    private function getagent_glossary_newword_jsonRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::glossaryNewWordJsonAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/glossary/new-word.json',  ),));
    }

    private function getagent_glossary_word_jsonRouteInfo()
    {
        return array(array (  0 => 'word_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::glossaryWordJsonAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'word_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/glossary',  ),));
    }

    private function getagent_glossary_saveword_jsonRouteInfo()
    {
        return array(array (  0 => 'word_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::glossarySaveWordJsonAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/edit.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'word_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/glossary',  ),));
    }

    private function getagent_glossary_delword_jsonRouteInfo()
    {
        return array(array (  0 => 'word_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::glossaryDeleteWordJsonAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/delete.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'word_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/glossary',  ),));
    }

    private function getagent_glossary_word_tipRouteInfo()
    {
        return array(array (  0 => 'word',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\GlossaryController::tipAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/tip',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'word',  ),  2 =>   array (    0 => 'text',    1 => '/agent/glossary',  ),));
    }

    private function getagent_news_listRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::listAction',  'category_id' => '0',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/news/list',  ),));
    }

    private function getagent_news_viewRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::viewAction',), array (  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/news/post',  ),));
    }

    private function getagent_news_revisionstabRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::viewRevisionsAction',), array (  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/view-revisions',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/news/post',  ),));
    }

    private function getagent_news_saveRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::ajaxSaveAction',), array (  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/news/post',  ),));
    }

    private function getagent_news_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::ajaxSaveLabelsAction',), array (  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/news',  ),));
    }

    private function getagent_news_ajax_save_commentRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::ajaxSaveCommentAction',), array (  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-comment',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/news/post',  ),));
    }

    private function getagent_news_new_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::newNewsSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/news/new/save',  ),));
    }

    private function getagent_news_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::newNewsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/news/new',  ),));
    }

    private function getagent_news_comparerevsRouteInfo()
    {
        return array(array (  0 => 'rev_old_id',  1 => 'rev_new_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\NewsController::compareRevisionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'rev_new_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'rev_old_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/news/compare-revs',  ),));
    }

    private function getagent_downloads_listRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::listAction',  'category_id' => '0',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/downloads/list',  ),));
    }

    private function getagent_downloads_viewRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::viewAction',), array (  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/downloads/file',  ),));
    }

    private function getagent_downloads_infoRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::infoAction',), array (  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/info',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/downloads/file',  ),));
    }

    private function getagent_kb_downloads_revisionstabRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::viewRevisionsAction',), array (  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/view-revisions',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/downloads/file',  ),));
    }

    private function getagent_downloads_saveRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::ajaxSaveAction',), array (  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/downloads/file',  ),));
    }

    private function getagent_downloads_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::ajaxSaveLabelsAction',), array (  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/downloads/file',  ),));
    }

    private function getagent_downloads_ajax_save_commentRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::ajaxSaveCommentAction',), array (  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-comment',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/downloads/file',  ),));
    }

    private function getagent_downloads_new_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::newDownloadSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/downloads/new/save',  ),));
    }

    private function getagent_downloads_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::newDownloadAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/downloads/new',  ),));
    }

    private function getagent_downloads_comparerevsRouteInfo()
    {
        return array(array (  0 => 'rev_old_id',  1 => 'rev_new_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\DownloadsController::compareRevisionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'rev_new_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'rev_old_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/downloads/compare-revs',  ),));
    }

    private function getagent_feedback_categoryRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::categoryListAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/feedback/category',  ),));
    }

    private function getagent_feedback_statusRouteInfo()
    {
        return array(array (  0 => 'status',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::statusListAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'status',  ),  1 =>   array (    0 => 'text',    1 => '/agent/feedback/status',  ),));
    }

    private function getagent_feedback_labelRouteInfo()
    {
        return array(array (  0 => 'label',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::labelListAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/agent/feedback/label',  ),));
    }

    private function getagent_feedback_filterRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::filterListAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/feedback/filter',  ),));
    }

    private function getagent_feedback_massactionsRouteInfo()
    {
        return array(array (  0 => 'action',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::massActionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'action',  ),  1 =>   array (    0 => 'text',    1 => '/agent/feedback/filter/mass-actions',  ),));
    }

    private function getagent_feedback_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/feedback/get-section-data.json',  ),));
    }

    private function getagent_feedback_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::newFeedbackAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/feedback/new',  ),));
    }

    private function getagent_feedback_new_saveRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::newFeedbackSaveAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/feedback/new/save',  ),));
    }

    private function getagent_feedback_viewRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::viewAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_comparerevsRouteInfo()
    {
        return array(array (  0 => 'rev_old_id',  1 => 'rev_new_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::compareRevisionsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'rev_new_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'rev_old_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/compare-revs',  ),));
    }

    private function getagent_feedback_ajaxsavecustomfieldsRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveCustomFieldsAction',), array (  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-custom-fields',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_who_votedRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::whoVotedAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/who-voted',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_saveRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveAction',), array (  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveLabelsAction',), array (  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_ajax_save_commentRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveCommentAction',), array (  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-comment',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_ajaxsavecommentRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveCommentAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-comment',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_ajaxsaveeditablesRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxSaveEditablesAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-editables',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_ajaxupdatecatRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'category_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxUpdateCategoryAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/ajax-update-category',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_ajaxupdatestatusRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'status_code',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::ajaxUpdateStatusAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'status_code',  ),  1 =>   array (    0 => 'text',    1 => '/ajax-update-status',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/feedback/view',  ),));
    }

    private function getagent_feedback_merge_overlayRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'other_feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::mergeOverlayAction',), array (  'feedback_id' => '\\d+',  'other_feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'other_feedback_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/merge-overlay',  ),));
    }

    private function getagent_feedback_mergeRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'other_feedback_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\FeedbackController::mergeAction',), array (  'feedback_id' => '\\d+',  'other_feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'other_feedback_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/feedback/merge',  ),));
    }

    private function getagent_agentchat_getonlineagentsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::getOnlineAgentsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/agent-chat/get-online-agents.json',  ),));
    }

    private function getagent_agentchat_get_last_convoRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::loadConvoMessagesAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/agent-chat/get-last-convo',  ),));
    }

    private function getagent_agentchat_send_messageRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::sendMessageAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/agent-chat/send-message',  ),));
    }

    private function getagent_agentchat_send_agent_messageRouteInfo()
    {
        return array(array (  0 => 'convo_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::sendAgentMessageAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'convo_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/agent-chat/send-agent-message',  ),));
    }

    private function getagent_agentchat_historyRouteInfo()
    {
        return array(array (  0 => 'agent_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::agentHistoryAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/agent-chat/agent-history',  ),));
    }

    private function getagent_agentchat_history_teamRouteInfo()
    {
        return array(array (  0 => 'agent_team_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::agentTeamHistoryAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_team_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/agent-chat/agent-history/team',  ),));
    }

    private function getagent_agentchat_viewRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::agentChatTranscriptAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/agent-chat/agent-transcript',  ),));
    }

    private function getagent_agentchat_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\AgentChatController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/agent-chat/get-section-data.json',  ),));
    }

    private function getagent_userchat_viewRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::viewAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/view',  ),));
    }

    private function getagent_userchat_save_fieldsRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::saveFieldsAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/save-fields',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/chat',  ),));
    }

    private function getagent_userchat_blockuserRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::blockUserAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/block-user',  ),));
    }

    private function getagent_userchat_unblockuserRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::unblockUserAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/unblock-user',  ),));
    }

    private function getagent_userchat_ajax_labels_saveRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::ajaxSaveLabelsAction',), array (  'conversation_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/ajax-save-labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'conversation_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/chat',  ),));
    }

    private function getagent_userchat_open_countsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::getOpenCountsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/chat/open-counts.json',  ),));
    }

    private function getagent_userchat_filterlist_group_countsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::getGroupByCountsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/chat/group-count.json',  ),));
    }

    private function getagent_userchat_filterlistRouteInfo()
    {
        return array(array (  0 => 'filter_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::filterAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'filter_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/filter',  ),));
    }

    private function getagent_userchat_list_newRouteInfo()
    {
        return array(array (  0 => 'department_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::listNewChatsAction',  'department_id' => '-1',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'department_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/list-new',  ),));
    }

    private function getagent_userchat_list_activeRouteInfo()
    {
        return array(array (  0 => 'agent_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::listActiveChatsAction',  'agent_id' => '-1',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/list-active',  ),));
    }

    private function getagent_userchat_send_messageviewRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::sendMessageAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/send-message',  ),));
    }

    private function getagent_userchat_send_filemessageRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::sendFileAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/send-file-message',  ),));
    }

    private function getagent_userchat_assignRouteInfo()
    {
        return array(array (  0 => 'conversation_id',  1 => 'agent_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::assignChatAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/chat/assign',  ),));
    }

    private function getagent_userchat_syncpartRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::syncPartsAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/sync-parts',  ),));
    }

    private function getagent_userchat_addpartRouteInfo()
    {
        return array(array (  0 => 'conversation_id',  1 => 'agent_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::addPartAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/chat/add-part',  ),));
    }

    private function getagent_userchat_endRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::endChatAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/end-chat',  ),));
    }

    private function getagent_userchat_leaveRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::leaveChatAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/leave',  ),));
    }

    private function getagent_userchat_inviteRouteInfo()
    {
        return array(array (  0 => 'conversation_id',  1 => 'agent_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::sendInviteAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'agent_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  2 =>   array (    0 => 'text',    1 => '/agent/chat/invite',  ),));
    }

    private function getagent_userchat_changepropRouteInfo()
    {
        return array(array (  0 => 'conversation_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::changePropertiesAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'conversation_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/chat/change-props',  ),));
    }

    private function getagent_userchat_getsectiondataRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserChatController::getSectionDataAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/chat/get-section-data.json',  ),));
    }

    private function getagent_usertrack_winheadertableRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserTrackController::winHeaderTableAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/user-track/win-header-table.html',  ),));
    }

    private function getagent_usertrack_viewRouteInfo()
    {
        return array(array (  0 => 'visitor_id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\UserTrackController::viewAction',), array (  'visitor_id' => '\\d+',  '_method' => 'GET',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'visitor_id',  ),  1 =>   array (    0 => 'text',    1 => '/agent/user-track',  ),));
    }

    private function getagent_mediamanagerRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MediaManagerController::windowAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/media-manager',  ),));
    }

    private function getagent_mediamanager_uploadRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MediaManagerController::uploadAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/media-manager/upload',  ),));
    }

    private function getagent_mediamanager_browseRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\AgentBundle\\Controller\\MediaManagerController::browseAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/agent/media-manager/browse',  ),));
    }

    private function getagent_textsnippets_widget_shellRouteInfo()
    {
        return array(array (  0 => 'typename',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::getWidgetShellAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/widget-shell.txt',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_textsnippets_reloadclientRouteInfo()
    {
        return array(array (  0 => 'typename',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::reloadClientAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/reload-client.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_textsnippets_reloadclient_batchRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'batch',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::reloadClientBatchAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'batch',  ),  2 =>   array (    0 => 'text',    1 => '/reload-client',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  4 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_textsnippets_filtersnippetsRouteInfo()
    {
        return array(array (  0 => 'typename',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::filterSnippetsAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/filter.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_textsnippets_getsnippetRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::getSnippetAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/\\.]+?',    3 => 'id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  3 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_textsnippets_savesnippetRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::saveSnippetAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/save.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  3 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_textsnippets_delsnippetRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::deleteSnippetAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/delete.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  3 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_textsnippets_savecatRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::saveCategoryAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/save.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/categories',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  4 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_textsnippets_delcatRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'id',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\TextSnippetsController::deleteCategoryAction',), array (), array (  0 =>   array (    0 => 'text',    1 => '/delete.json',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'id',  ),  2 =>   array (    0 => 'text',    1 => '/categories',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  4 =>   array (    0 => 'text',    1 => '/agent/text-snippets',  ),));
    }

    private function getagent_plugins_runRouteInfo()
    {
        return array(array (  0 => 'plugin_id',  1 => 'action',), array (  '_controller' => 'Application\\AgentBundle\\Controller\\PluginsController::runAction',), array (), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'action',  ),  1 =>   array (    0 => 'text',    1 => '/run',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'plugin_id',  ),  3 =>   array (    0 => 'text',    1 => '/agent/plugins',  ),));
    }

    private function getapiRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TestController::AboutAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/',  ),));
    }

    private function getapi_testRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TestController::testAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/test',  ),));
    }

    private function getapi_test_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TestController::postTestAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/test',  ),));
    }

    private function getapi_deskpro_timeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DeskproController::timeAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/deskpro/time',  ),));
    }

    private function getapi_deskpro_infoRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\MiscController::helpdeskInfoAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/deskpro/info',  ),));
    }

    private function getapi_token_exchangeRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\MiscController::tokenExchangeAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/token-exchange',  ),));
    }

    private function getapi_token_renewRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\MiscController::renewTokenAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/renew-token',  ),));
    }

    private function getapi_misc_uploadRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\MiscController::uploadAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/misc/upload',  ),));
    }

    private function getapi_misc_session_personRouteInfo()
    {
        return array(array (  0 => 'session_code',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\MiscController::getSessionPersonAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'session_code',  ),  1 =>   array (    0 => 'text',    1 => '/api/misc/session-person',  ),));
    }

    private function getapi_misc_rate_limitRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\MiscController::getRateLimitAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/misc/rate-limit',  ),));
    }

    private function getapi_tickets_newRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::newTicketAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticketRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_postRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::postTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_deleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::deleteTicketAction',), array (  '_method' => 'DELETE',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_logsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketLogsAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/logs',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_messagesRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketMessagesAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/messages',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_messages_postRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::replyTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/messages',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_messageRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'message_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketMessageAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',  'message_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'message_id',  ),  1 =>   array (    0 => 'text',    1 => '/messages',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_message_detailsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'message_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketMessageDetailsAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',  'message_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/details',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'message_id',  ),  2 =>   array (    0 => 'text',    1 => '/messages',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  4 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_undeleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::undeleteTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/undelete',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_splitRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::splitTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/split',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_claimRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::claimTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/claim',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_mergeRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'merge_ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::mergeTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',  'merge_ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'merge_ticket_id',  ),  1 =>   array (    0 => 'text',    1 => '/merge',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_spamRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::spamTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/spam',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_unspamRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::unspamTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/unspam',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_lockRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::lockTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/lock',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_unlockRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::unlockTicketAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/unlock',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_tasksRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketTasksAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/tasks',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_tasks_postRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::postTicketTasksAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/tasks',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_billing_chargesRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketBillingChargesAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/billing-charges',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_billing_charges_postRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::postTicketBillingChargesAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/billing-charges',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_billing_chargeRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'charge_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketBillingChargeAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',  'charge_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'charge_id',  ),  1 =>   array (    0 => 'text',    1 => '/billing-charges',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_billing_charge_deleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'charge_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::deleteTicketBillingChargeAction',), array (  '_method' => 'DELETE',  'ticket_id' => '\\d+',  'charge_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'charge_id',  ),  1 =>   array (    0 => 'text',    1 => '/billing-charges',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_slasRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketSlasAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/slas',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_slas_postRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::postTicketSlasAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/slas',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_slaRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'ticket_sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getTicketSlaAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',  'ticket_sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_sla_id',  ),  1 =>   array (    0 => 'text',    1 => '/slas',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_sla_deleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'ticket_sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::deleteTicketSlaAction',), array (  '_method' => 'DELETE',  'ticket_id' => '\\d+',  'ticket_sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_sla_id',  ),  1 =>   array (    0 => 'text',    1 => '/slas',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_participantsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getParticipantsAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/participants',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_participants_postRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::postParticipantsAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/participants',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_participantRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getParticipantAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/participants',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_participant_deleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::deleteParticipantAction',), array (  '_method' => 'DELETE',  'ticket_id' => '\\d+',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/participants',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_labelsRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getLabelsAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_labels_postRouteInfo()
    {
        return array(array (  0 => 'ticket_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::postLabelsAction',), array (  '_method' => 'POST',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_labelRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getLabelAction',), array (  '_method' => 'GET',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_ticket_label_deleteRouteInfo()
    {
        return array(array (  0 => 'ticket_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::deleteLabelAction',), array (  '_method' => 'DELETE',  'ticket_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'ticket_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_fieldsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getFieldsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/fields',  ),));
    }

    private function getapi_tickets_departmentsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getDepartmentsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/departments',  ),));
    }

    private function getapi_tickets_productsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getProductsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/products',  ),));
    }

    private function getapi_tickets_categoriesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getCategoriesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/categories',  ),));
    }

    private function getapi_tickets_prioritiesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getPrioritiesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/priorities',  ),));
    }

    private function getapi_tickets_workflowsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getWorkflowsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/workflows',  ),));
    }

    private function getapi_tickets_slasRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getSlasAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/slas',  ),));
    }

    private function getapi_tickets_slaRouteInfo()
    {
        return array(array (  0 => 'sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getSlaAction',), array (  '_method' => 'GET',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/tickets/slas',  ),));
    }

    private function getapi_tickets_sla_peopleRouteInfo()
    {
        return array(array (  0 => 'sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getSlaPeopleAction',), array (  '_method' => 'GET',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/people',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets/slas',  ),));
    }

    private function getapi_tickets_sla_organizationsRouteInfo()
    {
        return array(array (  0 => 'sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketController::getSlaOrganizationsAction',), array (  '_method' => 'GET',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/organizations',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tickets/slas',  ),));
    }

    private function getapi_textsnippets_listRouteInfo()
    {
        return array(array (  0 => 'typename',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::filterSnippetsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  1 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_newRouteInfo()
    {
        return array(array (  0 => 'typename',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::saveSnippetAction',  'snippet_id' => '0',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  1 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_editRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'snippet_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::saveSnippetAction',), array (  '_method' => 'POST',  'snippet_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'snippet_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_delRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'snippet_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::deleteSnippetAction',), array (  '_method' => 'DELETE',  'snippet_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'snippet_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_getRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'snippet_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::getSnippetAction',), array (  '_method' => 'GET',  'snippet_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'snippet_id',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_cats_listRouteInfo()
    {
        return array(array (  0 => 'typename',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::listCategoriesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/categories',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_cats_newRouteInfo()
    {
        return array(array (  0 => 'typename',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::saveCategoryAction',  'category_id' => '0',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/categories',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  2 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_cats_editRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::saveCategoryAction',), array (  '_method' => 'POST',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/categories',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  3 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_cats_getRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::getCategoryAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/categories',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  3 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_textsnippets_cats_delRouteInfo()
    {
        return array(array (  0 => 'typename',  1 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TextSnippetsController::deleteCategoryAction',), array (  '_method' => 'DELETE',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/categories',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'typename',  ),  3 =>   array (    0 => 'text',    1 => '/api/text-snippets',  ),));
    }

    private function getapi_open_tickets_newticketmessageRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OpenTicketController::newTicketMessageAction',), array (  '_method' => 'POST',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/api/open/tickets/new-ticket-message',  ),));
    }

    private function getapi_ticketsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketSearchController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets',  ),));
    }

    private function getapi_tickets_filtersRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketSearchController::getFiltersAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/filters',  ),));
    }

    private function getapi_tickets_filter_countsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketSearchController::getFilterCountsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tickets/filters/counts',  ),));
    }

    private function getapi_tickets_filterRouteInfo()
    {
        return array(array (  0 => 'filter_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TicketSearchController::getFilterAction',), array (  '_method' => 'GET',  'filter_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'filter_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/tickets/filters',  ),));
    }

    private function getapi_peopleRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::newPersonAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_personRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_postRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_deleteRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::deletePersonAction',), array (  '_method' => 'DELETE',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_mergeRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'other_person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::mergePersonAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',  'other_person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'other_person_id',  ),  1 =>   array (    0 => 'text',    1 => '/merge',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_pictureRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonPictureAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/picture',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_picture_postRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonPictureAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/picture',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_picture_deleteRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::deletePersonPictureAction',), array (  '_method' => 'DELETE',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/picture',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_emailsRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonEmailsAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/emails',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_emails_postRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonEmailsAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/emails',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_emailRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'email_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonEmailAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',  'email_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'email_id',  ),  1 =>   array (    0 => 'text',    1 => '/emails',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_email_postRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'email_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonEmailAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',  'email_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'email_id',  ),  1 =>   array (    0 => 'text',    1 => '/emails',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_email_deleteRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'email_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::deletePersonEmailAction',), array (  '_method' => 'DELETE',  'person_id' => '\\d+',  'email_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'email_id',  ),  1 =>   array (    0 => 'text',    1 => '/emails',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_vcardRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonVcardAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/vcard',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_ticketsRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonTicketsAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/tickets',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_chatsRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonChatsAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/chats',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_activity_streamRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonActivityStreamAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/activity-stream',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_reset_passwordRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::resetPasswordAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/reset-password',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_clear_sessionRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::clearSessionAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/clear-session',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_slasRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonSlasAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/slas',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_slas_postRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonSlasAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/slas',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_slaRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonSlaAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  1 =>   array (    0 => 'text',    1 => '/slas',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_sla_deleteRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::deletePersonSlaAction',), array (  '_method' => 'DELETE',  'person_id' => '\\d+',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  1 =>   array (    0 => 'text',    1 => '/slas',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_notesRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonNotesAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/notes',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_notes_postRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonNotesAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/notes',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_billing_chargesRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonBillingChargesAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/billing-charges',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_contact_detailsRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonContactDetailsAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/contact-details',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_contact_details_postRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonContactDetailsAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/contact-details',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_contact_detailRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'contact_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonContactDetailAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',  'contact_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'contact_id',  ),  1 =>   array (    0 => 'text',    1 => '/contact-details',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_contact_detail_deleteRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'contact_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::deletePersonContactDetailAction',), array (  '_method' => 'DELETE',  'person_id' => '\\d+',  'contact_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'contact_id',  ),  1 =>   array (    0 => 'text',    1 => '/contact-details',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_groupsRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonGroupsAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_groups_postRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonGroupsAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_groupRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'usergroup_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonGroupAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',  'usergroup_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'usergroup_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_group_deleteRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'usergroup_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::deletePersonGroupAction',), array (  '_method' => 'DELETE',  'person_id' => '\\d+',  'usergroup_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'usergroup_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_labelsRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonLabelsAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_labels_postRouteInfo()
    {
        return array(array (  0 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::postPersonLabelsAction',), array (  '_method' => 'POST',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_labelRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getPersonLabelAction',), array (  '_method' => 'GET',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_person_label_deleteRouteInfo()
    {
        return array(array (  0 => 'person_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::deletePersonLabelAction',), array (  '_method' => 'DELETE',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/people',  ),));
    }

    private function getapi_people_fieldsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getFieldsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/people/fields',  ),));
    }

    private function getapi_people_groupsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\PersonController::getGroupsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/people/groups',  ),));
    }

    private function getapi_organizationsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::newOrganizationAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organizationRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_postRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_deleteRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::deleteOrganizationAction',), array (  '_method' => 'DELETE',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_pictureRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationPictureAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/picture',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_picture_postRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationPictureAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/picture',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_picture_deleteRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::deleteOrganizationPictureAction',), array (  '_method' => 'DELETE',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/picture',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_activity_streamRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationActivityStreamAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/activity-stream',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_membersRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationMembersAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/members',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_ticketsRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationTicketsAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/tickets',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_chatsRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationChatsAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/chats',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_slasRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationSlasAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/slas',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_slas_postRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationSlasAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/slas',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_slaRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationSlaAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  1 =>   array (    0 => 'text',    1 => '/slas',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_sla_deleteRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'sla_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::deleteOrganizationSlaAction',), array (  '_method' => 'DELETE',  'organization_id' => '\\d+',  'sla_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'sla_id',  ),  1 =>   array (    0 => 'text',    1 => '/slas',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_billing_chargesRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationBillingChargesAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/billing-charges',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_email_domainsRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationEmailDomainsAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/email-domains',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_email_domains_postRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationEmailDomainsAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/email-domains',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_email_domainRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'domain',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationEmailDomainAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'domain',  ),  1 =>   array (    0 => 'text',    1 => '/email-domains',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_email_domain_move_usersRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'domain',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationEmailDomainMoveUsersAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/move-users',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'domain',  ),  2 =>   array (    0 => 'text',    1 => '/email-domains',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  4 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_email_domain_move_taken_usersRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'domain',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationEmailDomainMoveTakenUsersAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/move-taken-users',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'domain',  ),  2 =>   array (    0 => 'text',    1 => '/email-domains',  ),  3 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  4 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_email_domain_deleteRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'domain',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::deleteOrganizationEmailDomainAction',), array (  '_method' => 'DELETE',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'domain',  ),  1 =>   array (    0 => 'text',    1 => '/email-domains',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_contact_detailsRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationContactDetailsAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/contact-details',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_contact_details_postRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationContactDetailsAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/contact-details',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_contact_detailRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'contact_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationContactDetailAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',  'contact_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'contact_id',  ),  1 =>   array (    0 => 'text',    1 => '/contact-details',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_contact_detail_deleteRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'contact_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::deleteOrganizationContactDetailAction',), array (  '_method' => 'DELETE',  'organization_id' => '\\d+',  'contact_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'contact_id',  ),  1 =>   array (    0 => 'text',    1 => '/contact-details',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_groupsRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationGroupsAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_groups_postRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationGroupsAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_groupRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'usergroup_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationGroupAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',  'usergroup_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'usergroup_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_group_deleteRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'usergroup_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::deleteOrganizationGroupAction',), array (  '_method' => 'DELETE',  'organization_id' => '\\d+',  'usergroup_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'usergroup_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_labelsRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationLabelsAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_labels_postRouteInfo()
    {
        return array(array (  0 => 'organization_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::postOrganizationLabelsAction',), array (  '_method' => 'POST',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_labelRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getOrganizationLabelAction',), array (  '_method' => 'GET',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_organization_label_deleteRouteInfo()
    {
        return array(array (  0 => 'organization_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::deleteOrganizationLabelAction',), array (  '_method' => 'DELETE',  'organization_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'organization_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/organizations',  ),));
    }

    private function getapi_organizations_fieldsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getFieldsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/organizations/fields',  ),));
    }

    private function getapi_organizations_groupsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\OrganizationController::getGroupsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/organizations/groups',  ),));
    }

    private function getapi_chatsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chatRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::getChatAction',), array (  '_method' => 'GET',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_postRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::postChatAction',), array (  '_method' => 'POST',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_leaveRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::leaveChatAction',), array (  '_method' => 'POST',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/leave',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_endRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::endChatAction',), array (  '_method' => 'POST',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/end',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_messagesRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::getMessagesAction',), array (  '_method' => 'GET',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/messages',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_messages_postRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::newMessageAction',), array (  '_method' => 'POST',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/messages',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_participantsRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::getParticipantsAction',), array (  '_method' => 'GET',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/participants',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_participants_postRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::postParticipantsAction',), array (  '_method' => 'POST',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/participants',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_participantRouteInfo()
    {
        return array(array (  0 => 'chat_id',  1 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::getParticipantAction',), array (  '_method' => 'GET',  'chat_id' => '\\d+',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/participants',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_participant_deleteRouteInfo()
    {
        return array(array (  0 => 'chat_id',  1 => 'person_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::deleteParticipantAction',), array (  '_method' => 'DELETE',  'chat_id' => '\\d+',  'person_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'person_id',  ),  1 =>   array (    0 => 'text',    1 => '/participants',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_labelsRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::getChatLabelsAction',), array (  '_method' => 'GET',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_labels_postRouteInfo()
    {
        return array(array (  0 => 'chat_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::postChatLabelsAction',), array (  '_method' => 'POST',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_labelRouteInfo()
    {
        return array(array (  0 => 'chat_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::getChatLabelAction',), array (  '_method' => 'GET',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_chats_chat_label_deleteRouteInfo()
    {
        return array(array (  0 => 'chat_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ChatController::deleteChatLabelAction',), array (  '_method' => 'DELETE',  'chat_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'chat_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/chats',  ),));
    }

    private function getapi_downloadsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::newDownloadAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_downloadRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getDownloadAction',), array (  '_method' => 'GET',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_postRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::postDownloadAction',), array (  '_method' => 'POST',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_deleteRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::deleteDownloadAction',), array (  '_method' => 'DELETE',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_commentsRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getDownloadCommentsAction',), array (  '_method' => 'GET',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_comments_newRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::newDownloadCommentAction',), array (  '_method' => 'POST',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_comments_commentRouteInfo()
    {
        return array(array (  0 => 'download_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getDownloadCommentAction',), array (  '_method' => 'GET',  'download_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_comments_comment_postRouteInfo()
    {
        return array(array (  0 => 'download_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::postDownloadCommentAction',), array (  '_method' => 'POST',  'download_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_comments_comment_deleteRouteInfo()
    {
        return array(array (  0 => 'download_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::deleteDownloadCommentAction',), array (  '_method' => 'DELETE',  'download_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_labelsRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getDownloadLabelsAction',), array (  '_method' => 'GET',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_labels_postRouteInfo()
    {
        return array(array (  0 => 'download_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::postDownloadLabelsAction',), array (  '_method' => 'POST',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_labelRouteInfo()
    {
        return array(array (  0 => 'download_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getDownloadLabelAction',), array (  '_method' => 'GET',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_download_label_deleteRouteInfo()
    {
        return array(array (  0 => 'download_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::deleteDownloadLabelAction',), array (  '_method' => 'DELETE',  'download_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'download_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/downloads',  ),));
    }

    private function getapi_downloads_validating_commentsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getValidatingCommentsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/downloads/validating-comments',  ),));
    }

    private function getapi_downloads_categoriesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getCategoriesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_categories_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::postCategoriesAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_categoryRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getCategoryAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_category_postRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::postCategoryAction',), array (  '_method' => 'POST',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_category_deleteRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::deleteCategoryAction',), array (  '_method' => 'DELETE',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_category_downloadsRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getCategoryDownloadsAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/downloads',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_category_groupsRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getCategoryGroupsAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_category_groups_postRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::postCategoryGroupsAction',), array (  '_method' => 'POST',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_category_groupRouteInfo()
    {
        return array(array (  0 => 'category_id',  1 => 'group_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::getCategoryGroupAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',  'group_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'group_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_downloads_category_group_deleteRouteInfo()
    {
        return array(array (  0 => 'category_id',  1 => 'group_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\DownloadController::deleteCategoryGroupAction',), array (  '_method' => 'DELETE',  'category_id' => '\\d+',  'group_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'group_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/downloads/categories',  ),));
    }

    private function getapi_newsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::newNewsAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_newsRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getNewsAction',), array (  '_method' => 'GET',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_postRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::postNewsAction',), array (  '_method' => 'POST',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_deleteRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::deleteNewsAction',), array (  '_method' => 'DELETE',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_commentsRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getNewsCommentsAction',), array (  '_method' => 'GET',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_comments_newRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::newNewsCommentAction',), array (  '_method' => 'POST',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_comments_commentRouteInfo()
    {
        return array(array (  0 => 'news_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getNewsCommentAction',), array (  '_method' => 'GET',  'news_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_comments_comment_postRouteInfo()
    {
        return array(array (  0 => 'news_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::postNewsCommentAction',), array (  '_method' => 'POST',  'news_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_comments_comment_deleteRouteInfo()
    {
        return array(array (  0 => 'news_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::deleteNewsCommentAction',), array (  '_method' => 'DELETE',  'news_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_labelsRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getNewsLabelsAction',), array (  '_method' => 'GET',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_labels_postRouteInfo()
    {
        return array(array (  0 => 'news_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::postNewsLabelsAction',), array (  '_method' => 'POST',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_labelRouteInfo()
    {
        return array(array (  0 => 'news_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getNewsLabelAction',), array (  '_method' => 'GET',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_news_label_deleteRouteInfo()
    {
        return array(array (  0 => 'news_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::deleteNewsLabelAction',), array (  '_method' => 'DELETE',  'news_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'news_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/news',  ),));
    }

    private function getapi_news_validating_commentsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getValidatingCommentsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/news/validating-comments',  ),));
    }

    private function getapi_news_categoriesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getCategoriesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_categories_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::postCategoriesAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_categoryRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getCategoryAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_category_postRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::postCategoryAction',), array (  '_method' => 'POST',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_category_deleteRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::deleteCategoryAction',), array (  '_method' => 'DELETE',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_category_newsRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getCategoryNewsAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/news',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_category_groupsRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getCategoryGroupsAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_category_groups_postRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::postCategoryGroupsAction',), array (  '_method' => 'POST',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_category_groupRouteInfo()
    {
        return array(array (  0 => 'category_id',  1 => 'group_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::getCategoryGroupAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',  'group_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'group_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_news_category_group_deleteRouteInfo()
    {
        return array(array (  0 => 'category_id',  1 => 'group_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\NewsController::deleteCategoryGroupAction',), array (  '_method' => 'DELETE',  'category_id' => '\\d+',  'group_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'group_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/news/categories',  ),));
    }

    private function getapi_kbRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::newArticleAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_articleRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getArticleAction',), array (  '_method' => 'GET',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_postRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::postArticleAction',), array (  '_method' => 'POST',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_deleteRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::deleteArticleAction',), array (  '_method' => 'DELETE',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_votesRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getArticleVotesAction',), array (  '_method' => 'GET',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/votes',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_commentsRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getArticleCommentsAction',), array (  '_method' => 'GET',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_comments_newRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::newArticleCommentAction',), array (  '_method' => 'POST',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_comments_commentRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getArticleCommentAction',), array (  '_method' => 'GET',  'article_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_comments_comment_postRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::postArticleCommentAction',), array (  '_method' => 'POST',  'article_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_comments_comment_deleteRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::deleteArticleCommentAction',), array (  '_method' => 'DELETE',  'article_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_attachmentsRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getArticleAttachmentsAction',), array (  '_method' => 'GET',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/attachments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_attachments_postRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::newArticleAttachmentAction',), array (  '_method' => 'POST',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/attachments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_attachmentRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'attachment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getArticleAttachmentAction',), array (  '_method' => 'GET',  'article_id' => '\\d+',  'attachment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'attachment_id',  ),  1 =>   array (    0 => 'text',    1 => '/attachments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_attachment_deleteRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'attachment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::deleteArticleAttachmentAction',), array (  '_method' => 'DELETE',  'article_id' => '\\d+',  'attachment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'attachment_id',  ),  1 =>   array (    0 => 'text',    1 => '/attachments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_labelsRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getArticleLabelsAction',), array (  '_method' => 'GET',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_labels_postRouteInfo()
    {
        return array(array (  0 => 'article_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::postArticleLabelsAction',), array (  '_method' => 'POST',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_labelRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getArticleLabelAction',), array (  '_method' => 'GET',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_article_label_deleteRouteInfo()
    {
        return array(array (  0 => 'article_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::deleteArticleLabelAction',), array (  '_method' => 'DELETE',  'article_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'article_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb',  ),));
    }

    private function getapi_kb_validating_commentsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getValidatingCommentsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/kb/validating-comments',  ),));
    }

    private function getapi_kb_categoriesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getCategoriesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_categories_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::postCategoriesAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_categoryRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getCategoryAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_category_postRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::postCategoryAction',), array (  '_method' => 'POST',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_category_deleteRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::deleteCategoryAction',), array (  '_method' => 'DELETE',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_category_articlesRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getCategoryArticlesAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/articles',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_category_groupsRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getCategoryGroupsAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_category_groups_postRouteInfo()
    {
        return array(array (  0 => 'category_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::postCategoryGroupsAction',), array (  '_method' => 'POST',  'category_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/groups',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_category_groupRouteInfo()
    {
        return array(array (  0 => 'category_id',  1 => 'group_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getCategoryGroupAction',), array (  '_method' => 'GET',  'category_id' => '\\d+',  'group_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'group_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_category_group_deleteRouteInfo()
    {
        return array(array (  0 => 'category_id',  1 => 'group_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::deleteCategoryGroupAction',), array (  '_method' => 'DELETE',  'category_id' => '\\d+',  'group_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'group_id',  ),  1 =>   array (    0 => 'text',    1 => '/groups',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'category_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/kb/categories',  ),));
    }

    private function getapi_kb_fieldsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getFieldsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/kb/fields',  ),));
    }

    private function getapi_kb_productsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\KbController::getProductsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/kb/products',  ),));
    }

    private function getapi_feedbackRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::newFeedbackAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedbackRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getFeedbackAction',), array (  '_method' => 'GET',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_postRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::postFeedbackAction',), array (  '_method' => 'POST',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_deleteRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::deleteFeedbackAction',), array (  '_method' => 'DELETE',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_votesRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getFeedbackVotesAction',), array (  '_method' => 'GET',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/votes',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_commentsRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getFeedbackCommentsAction',), array (  '_method' => 'GET',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_comments_newRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::newFeedbackCommentAction',), array (  '_method' => 'POST',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_comments_commentRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getFeedbackCommentAction',), array (  '_method' => 'GET',  'feedback_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_comments_comment_postRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::postFeedbackCommentAction',), array (  '_method' => 'POST',  'feedback_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_comments_comment_deleteRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::deleteFeedbackCommentAction',), array (  '_method' => 'DELETE',  'feedback_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_mergeRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'other_feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::mergeFeedbackAction',), array (  '_method' => 'POST',  'feedback_id' => '\\d+',  'other_feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'other_feedback_id',  ),  1 =>   array (    0 => 'text',    1 => '/merge',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_attachmentsRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getFeedbackAttachmentsAction',), array (  '_method' => 'GET',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/attachments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_attachments_postRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::newFeedbackAttachmentAction',), array (  '_method' => 'POST',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/attachments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_attachmentRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'attachment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getFeedbackAttachmentAction',), array (  '_method' => 'GET',  'feedback_id' => '\\d+',  'attachment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'attachment_id',  ),  1 =>   array (    0 => 'text',    1 => '/attachments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_attachment_deleteRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'attachment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::deleteFeedbackAttachmentAction',), array (  '_method' => 'DELETE',  'feedback_id' => '\\d+',  'attachment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'attachment_id',  ),  1 =>   array (    0 => 'text',    1 => '/attachments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_labelsRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getFeedbackLabelsAction',), array (  '_method' => 'GET',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_labels_postRouteInfo()
    {
        return array(array (  0 => 'feedback_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::postFeedbackLabelsAction',), array (  '_method' => 'POST',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_labelRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getFeedbackLabelAction',), array (  '_method' => 'GET',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_feedback_label_deleteRouteInfo()
    {
        return array(array (  0 => 'feedback_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::deleteFeedbackLabelAction',), array (  '_method' => 'DELETE',  'feedback_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'feedback_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/feedback',  ),));
    }

    private function getapi_feedback_validating_commentsRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getValidatingCommentsAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/feedback/validating-comments',  ),));
    }

    private function getapi_feedback_categoriesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getCategoriesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/feedback/categories',  ),));
    }

    private function getapi_feedback_status_categoriesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getStatusCategoriesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/feedback/status-categories',  ),));
    }

    private function getapi_feedback_user_categoriesRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\FeedbackController::getUserCategoriesAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/feedback/user-categories',  ),));
    }

    private function getapi_tasksRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::searchAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::newTaskAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_taskRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::getTaskAction',), array (  '_method' => 'GET',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_postRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::postTaskAction',), array (  '_method' => 'POST',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_deleteRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::deleteTaskAction',), array (  '_method' => 'DELETE',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_associationsRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::getTaskAssociationsAction',), array (  '_method' => 'GET',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/associations',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_associations_postRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::postTaskAssociationsAction',), array (  '_method' => 'POST',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/associations',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_associated_itemRouteInfo()
    {
        return array(array (  0 => 'task_id',  1 => 'assoc_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::getTaskAssociationAction',), array (  '_method' => 'GET',  'task_id' => '\\d+',  'assoc_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'assoc_id',  ),  1 =>   array (    0 => 'text',    1 => '/associations',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_commentsRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::getTaskCommentsAction',), array (  '_method' => 'GET',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_comments_postRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::postTaskCommentsAction',), array (  '_method' => 'POST',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/comments',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_commentRouteInfo()
    {
        return array(array (  0 => 'task_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::getTaskCommentAction',), array (  '_method' => 'GET',  'task_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_associated_item_deleteRouteInfo()
    {
        return array(array (  0 => 'task_id',  1 => 'comment_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::deleteTaskCommentAction',), array (  '_method' => 'DELETE',  'task_id' => '\\d+',  'comment_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'comment_id',  ),  1 =>   array (    0 => 'text',    1 => '/comments',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_labelsRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::getTaskLabelsAction',), array (  '_method' => 'GET',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_labels_postRouteInfo()
    {
        return array(array (  0 => 'task_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::postTaskLabelsAction',), array (  '_method' => 'POST',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'text',    1 => '/labels',  ),  1 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  2 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_labelRouteInfo()
    {
        return array(array (  0 => 'task_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::getTaskLabelAction',), array (  '_method' => 'GET',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_tasks_task_label_deleteRouteInfo()
    {
        return array(array (  0 => 'task_id',  1 => 'label',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\TaskController::deleteTaskLabelAction',), array (  '_method' => 'DELETE',  'task_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '[^/]+?',    3 => 'label',  ),  1 =>   array (    0 => 'text',    1 => '/labels',  ),  2 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'task_id',  ),  3 =>   array (    0 => 'text',    1 => '/api/tasks',  ),));
    }

    private function getapi_glossaryRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\GlossaryController::listAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/glossary',  ),));
    }

    private function getapi_glossary_lookupRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\GlossaryController::lookupAction',), array (  '_method' => 'GET',), array (  0 =>   array (    0 => 'text',    1 => '/api/glossary/lookup',  ),));
    }

    private function getapi_glossary_postRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\GlossaryController::newWordAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/glossary',  ),));
    }

    private function getapi_glossary_wordRouteInfo()
    {
        return array(array (  0 => 'word_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\GlossaryController::getWordAction',), array (  '_method' => 'GET',  'word_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'word_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/glossary',  ),));
    }

    private function getapi_glossary_word_deleteRouteInfo()
    {
        return array(array (  0 => 'word_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\GlossaryController::deleteWordAction',), array (  '_method' => 'DELETE',  'word_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'word_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/glossary',  ),));
    }

    private function getapi_glossary_definitionRouteInfo()
    {
        return array(array (  0 => 'definition_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\GlossaryController::getDefinitionAction',), array (  '_method' => 'GET',  'definition_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'definition_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/glossary/definitions',  ),));
    }

    private function getapi_glossary_definition_postRouteInfo()
    {
        return array(array (  0 => 'definition_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\GlossaryController::postDefinitionAction',), array (  '_method' => 'POST',  'definition_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'definition_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/glossary/definitions',  ),));
    }

    private function getapi_glossary_definition_deleteRouteInfo()
    {
        return array(array (  0 => 'definition_id',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\GlossaryController::deleteDefinitionAction',), array (  '_method' => 'DELETE',  'definition_id' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'definition_id',  ),  1 =>   array (    0 => 'text',    1 => '/api/glossary/definitions',  ),));
    }

    private function getapi_dismiss_activityRouteInfo()
    {
        return array(array (), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ActivityController::dismissAction',), array (  '_method' => 'POST',), array (  0 =>   array (    0 => 'text',    1 => '/api/activity/dismiss',  ),));
    }

    private function getapi_get_activityRouteInfo()
    {
        return array(array (  0 => 'since',), array (  '_controller' => 'Application\\ApiBundle\\Controller\\ActivityController::getActivityAction',), array (  '_method' => 'GET',  'since' => '\\d+',), array (  0 =>   array (    0 => 'variable',    1 => '/',    2 => '\\d+',    3 => 'since',  ),  1 =>   array (    0 => 'text',    1 => '/api/activity',  ),));
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