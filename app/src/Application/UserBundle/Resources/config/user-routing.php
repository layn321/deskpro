<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('user', new Route(
	'/',
	array('_controller' => 'UserBundle:Portal:portal', '_locale' => 'en'),
	array(),
	array()
));

$collection->add('user_admin_rendertpl', new Route(
	'/admin-render-template/{type}',
	array('_controller' => 'UserBundle:Main:adminRenderTemplate'),
	array(),
	array()
));

$collection->add('user_comment_form_login_partial', new Route(
	'/_misc/comment-form-login-partial',
	array('_controller' => 'UserBundle:Main:commentFormLoginPartial'),
	array(),
	array()
));


$collection->add('user_test', new Route(
	'/test',
	array('_controller' => 'UserBundle:Test:index'),
	array(),
	array()
));

$collection->add('user_saverating', new Route(
	'/portal/save-rating/{object_type}/{object_id}',
	array('_controller' => 'UserBundle:Portal:saveRating'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_newcomment_finishlogin', new Route(
	'/portal/save-comment/login-finished/{comment_type}/{comment_id}',
	array('_controller' => 'UserBundle:Portal:newCommentFinishLogin'),
	array(),
	array()
));

$collection->add('user_accept_upload', new Route(
	'/accept-temp-upload',
	array('_controller' => 'UserBundle:Main:acceptTempUpload'),
	array(),
	array()
));

$collection->add('user_validate_email', new Route(
	'/validate-email/{id}/{auth}',
	array('_controller' => 'UserBundle:Main:validateEmail'),
	array(),
	array()
));

$collection->add('user_validate_ticket', new Route(
	'/validate-ticket-email/{access_code}',
	array('_controller' => 'UserBundle:Main:validateTicketEmail'),
	array(),
	array()
));

$collection->add('user_jstell_login', new Route(
	'/login/jstell/{jstell}/{security_token}/{usersource_id}',
	array('_controller' => 'UserBundle:Login:jstellLogin'),
	array(),
	array()
));

$collection->add('user_login', new Route(
	'/login',
	array('_controller' => 'UserBundle:Login:index'),
	array(),
	array()
));

$collection->add('user_login_inline', new Route(
	'/login/inline-login',
	array('_controller' => 'UserBundle:Login:inlineLogin'),
	array(),
	array()
));

$collection->add('user_login_usersource_sso', new Route(
	'/login/usersource-sso/{usersource_id}',
	array('_controller' => 'UserBundle:Login:usersourceSso'),
	array('usersource_id' => '\d+'),
	array()
));

$collection->add('user_logout', new Route(
	'/logout/{auth}',
	array('_controller' => 'UserBundle:Login:logout'),
	array(),
	array()
));

$collection->add('user_login_authenticate_local', new Route(
	'/login/authenticate-password',
	array('_controller' => 'UserBundle:Login:authenticateLocal', 'usersource_id' => 0),
	array(),
	array()
));

$collection->add('user_login_authenticate', new Route(
	'/login/authenticate/{usersource_id}',
	array('_controller' => 'UserBundle:Login:authenticate', 'usersource_id' => 0),
	array('usersource_id' => '\\d+'),
	array()
));

$collection->add('user_login_callback', new Route(
	'/login/authenticate-callback/{usersource_id}',
	array('_controller' => 'UserBundle:Login:authenticateCallback'),
	array('usersource_id' => '\\d+'),
	array()
));

$collection->add('user_login_resetpass', new Route(
	'/login/reset-password',
	array('_controller' => 'UserBundle:Login:resetPassword'),
	array(),
	array()
));

$collection->add('user_login_resetpass_send', new Route(
	'/login/reset-password/send.{_format}',
	array('_controller' => 'UserBundle:Login:sendResetPassword', '_format' => 'html'),
	array(),
	array()
));

$collection->add('user_login_resetpass_newpass', new Route(
	'/login/reset-password/{code}',
	array('_controller' => 'UserBundle:Login:resetPasswordNewPass'),
	array('code' => '[A-Za-z0-9\\-]{17,}'),
	array()
));

$collection->add('user_login_agentlogin', new Route(
	'/login/agent-login/{code}',
	array('_controller' => 'UserBundle:Login:authAgentLogin'),
	array(),
	array()
));

$collection->add('user_register', new Route(
	'/register',
	array('_controller' => 'UserBundle:Register:register'),
	array(),
	array()
));

$collection->add('user_profile', new Route(
	'/profile',
	array('_controller' => 'UserBundle:Profile:index'),
	array(),
	array()
));

$collection->add('user_profile_setlang', new Route(
	'/profile/quick-set-language',
	array('_controller' => 'UserBundle:Main:quickSetLanguage'),
	array(),
	array()
));

$collection->add('user_profile_associate_twitter', new Route(
	'/profile/associate-twitter',
	array('_controller' => 'UserBundle:Profile:associateTwitter'),
	array(),
	array()
));

$collection->add('user_profile_twitter_remove', new Route(
	'/profile/twitter/{account_id}/remove',
	array('_controller' => 'UserBundle:Profile:removeTwitter'),
	array('account_id' => '\\d+'),
	array()
));

$collection->add('user_profile_changepassword', new Route(
	'/profile/change-password',
	array('_controller' => 'UserBundle:Profile:changePassword'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_profile_emails_new', new Route(
	'/profile/emails/new',
	array('_controller' => 'UserBundle:Profile:newEmail'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_profile_emails_remove', new Route(
	'/profile/emails/{email_id}/remove',
	array('_controller' => 'UserBundle:Profile:removeEmail'),
	array(),
	array()
));

$collection->add('user_profile_emails_validate_remove', new Route(
	'/profile/emails/{email_id}/remove-validating',
	array('_controller' => 'UserBundle:Profile:removeEmailValidating'),
	array(),
	array()
));

$collection->add('user_profile_emails_validate_sendlink', new Route(
	'/profile/emails/{email_id}/validate/send-link',
	array('_controller' => 'UserBundle:Profile:sendValidateEmailLink'),
	array(),
	array()
));

$collection->add('user_profile_emails_setdefault', new Route(
	'/profile/emails/{email_id}/set-default',
	array('_controller' => 'UserBundle:Profile:setDefaultEmail'),
	array(),
	array()
));

$collection->add('user_search', new Route(
	'/search',
	array('_controller' => 'UserBundle:Search:search'),
	array(),
	array()
));

$collection->add('user_search_labels', new Route(
	'/search/labels/{type}/{label}',
	array('_controller' => 'UserBundle:Search:labelSearch', 'label' => '', 'type' => 'all'),
	array('label' => '.*'),
	array()
));

$collection->add('user_search_omnisearch', new Route(
	'/search/omnisearch/{query}',
	array('_controller' => 'UserBundle:Search:omnisearch'),
	array('query' => '.+'),
	array()
));

$collection->add('user_search_similarto', new Route(
	'/search/similar-to/{content_type}',
	array('_controller' => 'UserBundle:Search:similarTo'),
	array(),
	array()
));

$collection->add('user_tickets', new Route(
	'/tickets',
	array('_controller' => 'UserBundle:Tickets:list'),
	array(),
	array()
));

$collection->add('user_tickets_organization', new Route(
	'/tickets/organization',
	array('_controller' => 'UserBundle:Tickets:listOrganization'),
	array(),
	array()
));

$collection->add('user_tickets_new', new Route(
	'/new-ticket/{for_department_id}',
	array('_controller' => 'UserBundle:NewTicket:new', 'format' => 'normal', 'for_department_id' => 0),
	array(),
	array()
));

$collection->add('user_tickets_new_finishlogin', new Route(
	'/new-ticket/login-finish/{ticket_id}',
	array('_controller' => 'UserBundle:NewTicket:newFinishLogin'),
	array(),
	array()
));

$collection->add('user_tickets_new_simple', new Route(
	'/tickets/new-simple/{for_department_id}',
	array('_controller' => 'UserBundle:NewTicket:new', 'format' => 'iframe', 'for_department_id' => 0),
	array(),
	array()
));

$collection->add('user_tickets_new_savestatus', new Route(
	'/tickets/new/save-status',
	array('_controller' => 'UserBundle:NewTicket:saveStatus'),
	array(),
	array()
));

$collection->add('user_tickets_new_contentsolved_save', new Route(
	'/tickets/new/content-solved-save.json',
	array('_controller' => 'UserBundle:NewTicket:contentSolvedSave'),
	array(),
	array()
));

$collection->add('user_tickets_new_contentsolved', new Route(
	'/tickets/new/content-solved-redirect',
	array('_controller' => 'UserBundle:NewTicket:contentSolvedRedirect'),
	array(),
	array()
));

$collection->add('user_tickets_new_thanks', new Route(
	'/tickets/new/thanks/{ticket_ref}',
	array('_controller' => 'UserBundle:NewTicket:thanks'),
	array(),
	array()
));

$collection->add('user_tickets_new_thanks_simple', new Route(
	'/tickets/new/thanks-simple/{ticket_ref}',
	array('_controller' => 'UserBundle:NewTicket:simpleThanks'),
	array(),
	array()
));

$collection->add('user_tickets_view', new Route(
	'/ticket/{ticket_ref}',
	array('_controller' => 'UserBundle:TicketView:load'),
	array(),
	array()
));

$collection->add('user_tickets_addreply', new Route(
	'/ticket-edit/{ticket_ref}/add-reply',
	array('_controller' => 'UserBundle:Tickets:addReply'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_tickets_participants', new Route(
	'/ticket-edit/{ticket_ref}/people',
	array('_controller' => 'UserBundle:Tickets:manageParticipants'),
	array(),
	array()
));

$collection->add('user_tickets_participants_add', new Route(
	'/ticket-edit/{ticket_ref}/people/add',
	array('_controller' => 'UserBundle:Tickets:addParticipant'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_tickets_participants_remove', new Route(
	'/ticket-edit/{ticket_ref}/people/remove/{person_id}',
	array('_controller' => 'UserBundle:Tickets:removeParticipant'),
	array(),
	array()
));

$collection->add('user_tickets_resolve', new Route(
	'/ticket-edit/{ticket_ref}/resolve',
	array('_controller' => 'UserBundle:Tickets:resolve'),
	array(),
	array()
));

$collection->add('user_tickets_unresolve', new Route(
	'/ticket-edit/{ticket_ref}/unresolve',
	array('_controller' => 'UserBundle:Tickets:unresolve'),
	array(),
	array()
));

$collection->add('user_tickets_feedback', new Route(
	'/ticket-rate/{ticket_ref}/{auth}/{message_id}',
	array('_controller' => 'UserBundle:Tickets:feedback'),
	array(),
	array()
));

$collection->add('user_tickets_feedback_save', new Route(
	'/ticket-rate/{ticket_ref}/{auth}/{message_id}/save',
	array('_controller' => 'UserBundle:Tickets:feedbackSave'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_tickets_feedback_closeticket', new Route(
	'/ticket-edit/{ticket_ref}/feedback/{message_id}/close-ticket',
	array('_controller' => 'UserBundle:Tickets:feedbackCloseTicket'),
	array(),
	array()
));

$collection->add('user_articles_home', new Route(
	'/kb',
	array('_controller' => 'UserBundle:Articles:browse', 'slug' => ''),
	array(),
	array()
));

$collection->add('user_articles', new Route(
	'/kb/{slug}',
	array('_controller' => 'UserBundle:Articles:browse', 'slug' => ''),
	array('slug' => '(\\d+(\\-.*?)?)?'),
	array()
));

$collection->add('user_articles_article', new Route(
	'/kb/articles/{slug}',
	array('_controller' => 'UserBundle:Articles:article'),
	array(),
	array()
));

$collection->add('user_articles_article_togglesub', new Route(
	'/kb/article-subscription/{article_id}/{auth}',
	array('_controller' => 'UserBundle:Articles:articleSubscription'),
	array(),
	array()
));

$collection->add('user_articles_cat_togglesub', new Route(
	'/kb/category-subscription/{category_id}/{auth}',
	array('_controller' => 'UserBundle:Articles:categorySubscription'),
	array(),
	array()
));

$collection->add('user_articles_unsub_all', new Route(
	'/kb/unsubscribe-all/{person_id}/{auth}',
	array('_controller' => 'UserBundle:Articles:unsubscribeAll'),
	array(),
	array()
));

$collection->add('user_articles_article_agent_iframe', new Route(
	'/kb/articles/agent-iframe/{article_id}/{agent_session_id}',
	array('_controller' => 'UserBundle:Articles:articleAgentIframe'),
	array('article_id' => '\\d+'),
	array()
));

$collection->add('user_articles_newcomment', new Route(
	'/kb/new-comment/{article_id}',
	array('_controller' => 'UserBundle:Articles:newComment'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_downloads_home', new Route(
	'/downloads',
	array('_controller' => 'UserBundle:Downloads:browse', 'slug' => ''),
	array(),
	array()
));


$collection->add('user_downloads', new Route(
	'/downloads/{slug}',
	array('_controller' => 'UserBundle:Downloads:browse', 'slug' => ''),
	array('slug' => '(\\d+(\\-.*?)?)?'),
	array()
));

$collection->add('user_downloads_file', new Route(
	'/downloads/files/{slug}',
	array('_controller' => 'UserBundle:Downloads:file'),
	array(),
	array()
));

$collection->add('user_downloads_file_download', new Route(
	'/downloads/files/{slug}/download',
	array('_controller' => 'UserBundle:Downloads:downloadFile'),
	array(),
	array()
));

$collection->add('user_downloads_newcomment', new Route(
	'/downloads/new-comment/{download_id}',
	array('_controller' => 'UserBundle:Downloads:newComment'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_news_home', new Route(
	'/news.{_format}',
	array('_controller' => 'UserBundle:News:browse', 'slug' => '', 'page' => 1, '_format' => 'html'),
	array('_format' => '(html|rss)'),
	array()
));

$collection->add('user_news', new Route(
	'/news/{slug}.{_format}',
	array('_controller' => 'UserBundle:News:browse', 'slug' => '', 'page' => 1, '_format' => 'html'),
	array('slug' => '(\\d+(\\-.*?)?)?', 'page' => '\\d+', '_format' => '(html|rss)'),
	array()
));

$collection->add('user_news_view', new Route(
	'/news/view/{slug}',
	array('_controller' => 'UserBundle:News:view'),
	array(),
	array()
));

$collection->add('user_news_newcomment', new Route(
	'/news/new-comment/{post_id}',
	array('_controller' => 'UserBundle:News:newComment'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_feedback_home', new Route(
	'/feedback',
	array('_controller' => 'UserBundle:Feedback:filter', 'status' => 'open', 'slug' => 'all-categories', 'order_by' => 'popular'),
	array(),
	array()
));

$collection->add('user_feedback', new Route(
	'/feedback/{order_by}/{status}/{slug}',
	array('_controller' => 'UserBundle:Feedback:filter', 'status' => 'open', 'slug' => 'all-categories', 'order_by' => 'popular'),
	array(
		'slug'   => '((\\d+(\\-.*?)?)?)|all\-categories',
		'status' => '(open|any-status|gathering\-feedback|active|closed)(\\.([0-9]+))?',
		'order_by'   => '(popular|newest|most\-voted|i\-voted)',
	),
	array()
));

$collection->add('user_feedback_new', new Route(
	'/feedback/new-feedback',
	array('_controller' => 'UserBundle:Feedback:filter', 'just_form' => 1,  'status' => 'any-status', 'slug' => 'all-categories', 'order_by' => 'popular'),
	array(),
	array()
));

$collection->add('user_feedback_view', new Route(
	'/feedback/view/{slug}',
	array('_controller' => 'UserBundle:Feedback:view'),
	array(),
	array()
));

$collection->add('user_feedback_newfeedback_finishlogin', new Route(
	'/feedback/new-feedback/login-finished/{feedback_id}',
	array('_controller' => 'UserBundle:Feedback:newFinishLogin'),
	array(),
	array()
));

$collection->add('user_feedback_newcomment', new Route(
	'/feedback/new-comment/{feedback_id}',
	array('_controller' => 'UserBundle:Feedback:newComment'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_feedback_vote', new Route(
	'/feedback/vote/{feedback_id}',
	array('_controller' => 'UserBundle:Feedback:vote'),
	array('_method' => 'POST'),
	array()
));

$collection->add('user_chat_initsession', new Route(
	'/chat/chat-session',
	array('_controller' => 'UserBundle:Chat:chatSession'),
	array(),
	array()
));

$collection->add('user_chat_widgetisavail', new Route(
	'/dp.php/chat/is-available.js',
	array('_controller' => '(see: serve_dp.php)'),
	array(),
	array()
));

$collection->add('user_chat_poll', new Route(
	'/chat/poll/{session_code}',
	array('_controller' => 'UserBundle:Chat:poll'),
	array(),
	array()
));

$collection->add('user_chat_sendmessage', new Route(
	'/chat/send-message/{session_code}',
	array('_controller' => 'UserBundle:Chat:sendMessage'),
	array(),
	array()
));

$collection->add('user_chat_sendmessage_attach', new Route(
	'/chat/send-attach/{session_code}',
	array('_controller' => 'UserBundle:Chat:sendFile'),
	array(),
	array()
));

$collection->add('user_chat_sendusertyping', new Route(
	'/chat/user-typing/{session_code}',
	array('_controller' => 'UserBundle:Chat:userTyping'),
	array(),
	array()
));

$collection->add('user_chat_chatended', new Route(
	'/chat/chat-finished/{session_code}',
	array('_controller' => 'UserBundle:Chat:chatEnded'),
	array(),
	array()
));

$collection->add('user_chat_chatended_feedback', new Route(
	'/chat/chat-finished-feedback/{session_code}',
	array('_controller' => 'UserBundle:Chat:chatEndedFeedback'),
	array(),
	array()
));

$collection->add('user_chatlogs', new Route(
	'/chat-logs',
	array('_controller' => 'UserBundle:ChatLog:list'),
	array(),
	array()
));

$collection->add('user_chatlogs_view', new Route(
	'/chat-logs/{conversation_id}',
	array('_controller' => 'UserBundle:ChatLog:view'),
	array('conversation_id' => '\d+'),
	array()
));

$collection->add('user_widget_overlay', new Route(
	'/widget/overlay.html',
	array('_controller' => 'UserBundle:Widget:overlay'),
	array(),
	array()
));

$collection->add('user_widget_newticket', new Route(
	'/widget/new-ticket.json',
	array('_controller' => 'UserBundle:Widget:newTicket'),
	array(),
	array()
));

$collection->add('user_widget_newfeedback', new Route(
	'/widget/new-feedback.json',
	array('_controller' => 'UserBundle:Widget:newFeedback'),
	array(),
	array()
));


$collection->add('user_widget_chat', new Route(
	'/widget/chat.html',
	array('_controller' => 'UserBundle:Widget:chat'),
	array(),
	array()
));

$collection->add('user_long_tweet_view', new Route(
	'/long-tweet/{long_id}',
	array('_controller' => 'UserBundle:Twitter:viewLong'),
	array('long_id' => '\\d+'),
	array()
));

################################################################################
# Plugins
################################################################################

$collection->add('user_plugins_run', new Route(
	'/ext-plugins/{plugin_id}/run/{action}',
	array('_controller' => 'UserBundle:Plugins:run'),
	array(),
	array()
));


return $collection;