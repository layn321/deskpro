<?php if (!defined('DP_ROOT')) exit('No access');

################################################################################
# Language
################################################################################

##BEGIN:locale.language##
$l = new \Application\DeskPRO\Entity\Language();
$l['title'] = $translate->phrase('user.defaults.language_english');
$l['locale'] = 'en_US';
$l['sys_name'] = 'default';
$l['flag_image'] = 'us.png';
$l['lang_code'] = 'eng';
$l['language_package'] = 'DeskproLanguages\\LangPackage';
$em->persist($l);
$em->flush();


################################################################################
# Departments
################################################################################

##BEGIN:create_department.department2##
if (!$IMPORT_INSTALL) {
	$q = new \Application\DeskPRO\Entity\Department();
	$q['title'] = $translate->phrase('user.defaults.department_support');
	$q['is_tickets_enabled'] = true;
	$q['is_chat_enabled'] = false;
	$em->persist($q);
	$em->flush();
}

##BEGIN:create_department.department1##
if (!$IMPORT_INSTALL) {
	$q = new \Application\DeskPRO\Entity\Department();
	$q['title'] = $translate->phrase('user.defaults.department_sales');
	$q['is_tickets_enabled'] = true;
	$q['is_chat_enabled'] = false;
	$em->persist($q);
	$em->flush();
}

##BEGIN:create_department.department3##
if (!$IMPORT_INSTALL) {
	$q = new \Application\DeskPRO\Entity\Department();
	$q['title'] = $translate->phrase('user.defaults.department_support');
	$q['is_tickets_enabled'] = false;
	$q['is_chat_enabled'] = true;
	$em->persist($q);
	$em->flush();
}

##BEGIN:create_department.department4##
if (!$IMPORT_INSTALL) {
	$q = new \Application\DeskPRO\Entity\Department();
	$q['title'] = $translate->phrase('user.defaults.department_sales');
	$q['is_tickets_enabled'] = false;
	$q['is_chat_enabled'] = true;
	$em->persist($q);
	$em->flush();
}

################################################################################
# KB
################################################################################

##BEGIN:create_article.default##
if (!$IMPORT_INSTALL) {
	$DEFAULT_ARTICLE_CAT = new \Application\DeskPRO\Entity\ArticleCategory();
	$DEFAULT_ARTICLE_CAT['title'] = $translate->phrase('user.defaults.article_category_general');
	$em->persist($DEFAULT_ARTICLE_CAT);
	$em->flush();

	$DEFAULT_ARTICLE = new \Application\DeskPRO\Entity\Article();
	$DEFAULT_ARTICLE->person = $AGENT;
	$DEFAULT_ARTICLE->title = $translate->phrase('user.defaults.article_example_title');
	$DEFAULT_ARTICLE->content = $translate->phrase('user.defaults.article_example_content');
	$DEFAULT_ARTICLE->status = 'published';
	$DEFAULT_ARTICLE->addToCategory($DEFAULT_ARTICLE_CAT);
	$em->persist($DEFAULT_ARTICLE);
	$em->flush();
}


################################################################################
# Downloads
################################################################################

##BEGIN:create_download_cat.default##
if (!$IMPORT_INSTALL) {
	$q = new \Application\DeskPRO\Entity\DownloadCategory();
	$q['title'] = $translate->phrase('user.defaults.downloads_category_general');
	$em->persist($q);
	$em->flush();
}


################################################################################
# News
################################################################################

##BEGIN:create_news.default##
if (!$IMPORT_INSTALL) {
	$DEFAULT_NEWS_CAT = new \Application\DeskPRO\Entity\NewsCategory();
	$DEFAULT_NEWS_CAT['title'] = $translate->phrase('user.defaults.news_category_general');
	$em->persist($DEFAULT_NEWS_CAT);
	$em->flush();

	$DEFAULT_NEWS = new \Application\DeskPRO\Entity\News();
	$DEFAULT_NEWS->person = $AGENT;
	$DEFAULT_NEWS->title = $translate->phrase('user.defaults.news_example_title');
	$DEFAULT_NEWS->content = $translate->phrase('user.defaults.news_example_content');
	$DEFAULT_NEWS->status = 'published';
	$DEFAULT_NEWS->category = $DEFAULT_NEWS_CAT;
	$em->persist($DEFAULT_NEWS);
	$em->flush();
}


################################################################################
# Feedback
################################################################################

##BEGIN:create_feedback.default##
$DEFAULT_IDEA_CAT = new \Application\DeskPRO\Entity\FeedbackCategory();
$DEFAULT_IDEA_CAT['title'] = $translate->phrase('user.defaults.feedback_type_suggestion');
$em->persist($DEFAULT_IDEA_CAT);
$em->flush();

$cat = new \Application\DeskPRO\Entity\FeedbackCategory();
$cat['title'] = $translate->phrase('user.defaults.feedback_type_feature-request');
$em->persist($cat);
$em->flush();

$cat = new \Application\DeskPRO\Entity\FeedbackCategory();
$cat['title'] = $translate->phrase('user.defaults.feedback_type_bug-report');
$em->persist($cat);
$em->flush();

// Statuses are done as part of FeedbackCatsStep so we can map id's
if (!$IMPORT_INSTALL) {
	foreach (array('planning', 'started', 'under-review') as $t) {
		$s = new \Application\DeskPRO\Entity\FeedbackStatusCategory();
		$s->status_type = 'active';
		$s->title = $translate->phrase('user.defaults.feedback_status_' . $t);
		$em->persist($s);
	}

	foreach (array('completed', 'duplicate', 'declined') as $t) {
		$s = new \Application\DeskPRO\Entity\FeedbackStatusCategory();
		$s->status_type = 'closed';
		$s->title = $translate->phrase('user.defaults.feedback_status_' . $t);
		$em->persist($s);
	}
	$em->flush();
}

if (!$IMPORT_INSTALL) {
	$DEFAULT_IDEA = new \Application\DeskPRO\Entity\Feedback();
	$DEFAULT_IDEA->person = $AGENT;
	$DEFAULT_IDEA->title = $translate->phrase('user.defaults.feedback_example_title');
	$DEFAULT_IDEA->content = $translate->phrase('user.defaults.feedback_example_content');
	$DEFAULT_IDEA->status = 'new';
	$DEFAULT_IDEA->category = $DEFAULT_IDEA_CAT;
	$em->persist($DEFAULT_IDEA);
	$em->flush();
}

################################################################################
# Filters
################################################################################

##BEGIN:create_filter.agent##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'My Tickets';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'agent';
$q['terms']      = array(array(
		'type'      => 'agent',
		'op'        => 'is',
		'options'   => array('agent'     => '-1',)
	),
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '0')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.agent_team##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'My Team\'s Tickets';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'agent_team';
$q['terms']      = array(array(
		'type'      => 'agent_team',
		'op'        => 'is',
		'options'   => array('agent_team' => '-1',)
	),
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '0')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.participant##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'TIckets I Follow';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'participant';
$q['terms']      = array(array(
		'type'      => 'participant',
		'op'        => 'is',
		'options'   => array('agent'     => '-1',)
	),
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '0')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.unassigned##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'Unassigned';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'unassigned';
$q['terms']      = array(array(
		'type'      => 'agent',
		'op'        => 'is',
		'options'   => array('agent'     => '0',)
	),
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '0')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.all##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'All';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'all';
$q['terms']      = array(
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '0')
	),
);
$em->persist($q);
$em->flush();

##BEGIN:create_filter.agent_w_hold##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'My Tickets';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'agent_w_hold';
$q['terms']      = array(array(
		'type'      => 'agent',
		'op'        => 'is',
		'options'   => array('agent'     => '-1',)
	),
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '1')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.agent_team_w_hold##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'My Team\'s Tickets';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'agent_team_w_hold';
$q['terms']      = array(array(
		'type'      => 'agent_team',
		'op'        => 'is',
		'options'   => array('agent_team' => '-1',)
	),
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '1')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.participant_w_hold##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'TIckets I Follow';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'participant_w_hold';
$q['terms']      = array(array(
		'type'      => 'participant',
		'op'        => 'is',
		'options'   => array('agent'     => '-1',)
	),
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '1')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.unassigned_w_hold##

$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'Unassigned';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'unassigned_w_hold';
$q['terms']      = array(array(
		'type'      => 'agent',
		'op'        => 'is',
		'options'   => array('agent'     => '0',)
	),
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '1')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.all_w_hold##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.urgency:desc';
$q['person']     = null;
$q['title']      = 'All';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'all_w_hold';
$q['terms']      = array(
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_agent',)
	),
	array(
		'type'      => 'is_hold',
		'op'        => 'is',
		'options'   => array('is_hold'   => '1')
	),
);
$em->persist($q);
$em->flush();


##BEGIN:create_filter.archive_awaiting_user##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.date_created:desc';
$q['person']     = null;
$q['title']      = 'Awaiting User';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'archive_awaiting_user';
$q['terms']      = array(
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'awaiting_user',)
	)
);
$em->persist($q);
$em->flush();

##BEGIN:create_filter.archive_resolved##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.date_created:desc';
$q['person']     = null;
$q['title']      = 'Resolved';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'archive_resolved';
$q['terms']      = array(
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'resolved',)
	)
);
$em->persist($q);
$em->flush();

##BEGIN:create_filter.archive_closed##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.date_created:desc';
$q['person']     = null;
$q['title']      = 'Resolved';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'archive_closed';
$q['terms']      = array(
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'closed',)
	)
);
$em->persist($q);
$em->flush();

##BEGIN:create_filter.archive_validating##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.date_created:desc';
$q['person']     = null;
$q['title']      = 'Awaiting Validation';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'archive_validating';
$q['terms']      = array(
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'hidden.validating')
	)
);
$em->persist($q);
$em->flush();

##BEGIN:create_filter.archive_spam##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.date_created:desc';
$q['person']     = null;
$q['title']      = 'Spam';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'archive_spam';
$q['terms']      = array(
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'hidden.spam')
	)
);
$em->persist($q);
$em->flush();

##BEGIN:create_filter.archive_deleted##
$q = new \Application\DeskPRO\Entity\TicketFilter();
$q['order_by']   = 'ticket.date_created:desc';
$q['person']     = null;
$q['title']      = 'Deleted';
$q['is_enabled'] = true;
$q['is_global']  = true;
$q['sys_name']   = 'archive_deleted';
$q['terms']      = array(
	array(
		'type'      => 'status',
		'op'        => 'is',
		'options'   => array('status'    => 'hidden.deleted')
	)
);
$em->persist($q);
$em->flush();

################################################################################
# Triggers
################################################################################

##BEGIN:create_trigger.email_validation_email##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->sys_name = 'email_validation.email';
$q->event_trigger = 'new.email.user';
$q->is_enabled = 0;
$q->terms = array();
$q->actions = array(
	array (
		'type' => 'force_email_validation',
		'options' => array('force_email_validation' => '1'),
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.newticket_confirm_email_user##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->sys_name = 'newticket_confirm.email_user';
$q->event_trigger = 'new.email.user';
$q->is_enabled = 1;
$q->terms = array();
$q->actions = array(
	array (
		'type' => 'enable_new_ticket_confirmation',
		'options' => array('enabled' => '1'),
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.newticket_confirm_web_user##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->sys_name = 'newticket_confirm.web_user';
$q->event_trigger = 'new.web.user';
$q->is_enabled = 1;
$q->terms = array();
$q->actions = array(
	array (
		'type' => 'enable_new_ticket_confirmation',
		'options' => array('enabled' => '1'),
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.newticket_confirm_email_agent##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->sys_name = 'newticket_confirm.web_user';
$q->event_trigger = 'new.email.agent';
$q->is_enabled = 0;
$q->terms = array();
$q->actions = array(
	array (
		'type' => 'enable_new_ticket_confirmation',
		'options' => array('enabled' => '1'),
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.email_validation_email##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->sys_name = 'email_validation.email';
$q->event_trigger = 'new.email.user';
$q->is_enabled = 0;
$q->terms = array();
$q->actions = array(
	array (
		'type' => 'force_email_validation',
		'options' => array('force_email_validation' => '1'),
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.email_validation_web##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->sys_name = 'email_validation.web';
$q->event_trigger = 'new.web.user.portal';
$q->is_enabled = 0;
$q->terms = array();
$q->actions = array(
	array (
		'type' => 'force_email_validation',
		'options' => array('force_email_validation' => '1'),
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.email_validation_widget##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->sys_name = 'email_validation.widget';
$q->event_trigger = 'new.web.user.widget';
$q->is_enabled = 0;
$q->terms = array();
$q->actions = array(
	array (
		'type' => 'force_email_validation',
		'options' => array('force_email_validation' => '1'),
	)
);

$em->persist($q);
$em->flush();


##BEGIN:create_trigger.urgency_up1##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->event_trigger = 'time.user_waiting';
$q->setEventTriggerOption('time', '1 days');
$q->is_enabled = 0;
$q->terms = array();
$q->actions = array(
	array(
		'type' => 'urgency_set',
		'options' => array(
			'num' => 2
		)
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.urgency_up2##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->event_trigger = 'time.user_waiting';
$q->setEventTriggerOption('time', '2 days');
$q->is_enabled = 1;
$q->terms = array();
$q->actions = array(
	array(
		'type' => 'urgency_set',
		'options' => array(
			'num' => 3
		)
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.urgency_up3##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = '';
$q->event_trigger = 'time.user_waiting';
$q->setEventTriggerOption('time', '3 days');
$q->is_enabled = 1;
$q->terms = array();
$q->actions = array(
	array(
		'type' => 'urgency_set',
		'options' => array(
			'num' => 3
		)
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.auto_close_resolve_user_reply##
// When a ticket has been awaiting agent for 2 months, set it to resolved
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->event_trigger = 'time.user_waiting';
$q->setEventTriggerOption('time', '2 months');
$q->is_enabled = 1;
$q->terms = array(
	array (
		'type' => 'status',
		'op' => 'is',
		'options' => array (
			'status' => 'awaiting_agent',
		),
	)
);
$q->actions = array(
	array(
		'type' => 'status',
		'options' => array(
			'status' => 'resolved'
		)
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.auto_close_resolve_agent_reply##
// When a ticket has been awaiting user for 5 days, set it to resolved
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->event_trigger = 'time.agent_waiting';
$q->setEventTriggerOption('time', '5 days');
$q->is_enabled = 0;
$q->terms = array(
	array (
		'type' => 'status',
		'op' => 'is',
		'options' => array (
			'status' => 'awaiting_agent',
		),
	)
);
$q->actions = array(
	array(
		'type' => 'status',
		'options' => array(
			'status' => 'resolved'
		)
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.warn_auto_close##
// When a ticket has been awaiting user for 3 days, warn the user it will be closed
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = 'auto_close.warn_user';
$q->event_trigger = 'time.agent_waiting';
$q->setEventTriggerOption('time', '3 days');
$q->is_enabled = 0;
$q->terms = array(
	array (
		'type' => 'status',
		'op' => 'is',
		'options' => array (
			'status' => 'awaiting_agent',
		),
	)
);
$q->actions = array(
	array(
		'type' => 'send_autoclose_warn_email',
		'options' => array(
			'template_name' => '',
		)
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.enable_autoreply_gateway##
$q = new \Application\DeskPRO\Entity\TicketTrigger();
$q->title = 'response.reply_confirm';
$q->sys_name = 'response.reply_confirm';
$q->event_trigger = 'update.user';
$q->is_enabled = 0;
$q->terms = array(
	array(
		'type' => 'new_reply_user',
		'op' => 'is',
		'options' => array('do' => '1')
	)
);
$q->actions = array(
	array(
		'type' => 'enable_user_notification_new_reply_user',
		'options' => array ('enable' => '1')
	)
);

$em->persist($q);
$em->flush();

##BEGIN:create_trigger.default_set##
$em->getConnection()->exec("
INSERT INTO `ticket_triggers` (`id`, `sys_name`, `title`, `event_trigger`, `is_enabled`, `terms`, `actions`, `run_order`, `date_created`, `event_trigger_options`, `terms_any`)
VALUES
	(NULL, 'setdep.newemail_user', '', 'new.email.user', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:10:\"department\";s:7:\"options\";a:1:{s:10:\"department\";s:13:\"email_account\";}}}', 0, '2012-10-22 19:56:52', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setgateway.newweb_user', '', 'new.web.user', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:19:\"set_gateway_address\";s:7:\"options\";a:1:{s:18:\"gateway_address_id\";s:10:\"department\";}}}', 0, '2012-10-22 19:57:20', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setdep.newemail_agent', '', 'new.email.agent', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:10:\"department\";s:7:\"options\";a:1:{s:10:\"department\";s:13:\"email_account\";}}}', 0, '2012-10-22 19:57:36', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setgateway.newweb_agent', '', 'new.web.agent.portal', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:19:\"set_gateway_address\";s:7:\"options\";a:1:{s:18:\"gateway_address_id\";s:10:\"department\";}}}', 0, '2012-10-22 19:59:50', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setgateway.update_agent', '', 'update.agent', 1, 'a:1:{i:0;a:3:{s:4:\"type\";s:10:\"department\";s:2:\"op\";s:7:\"changed\";s:7:\"options\";a:1:{s:10:\"department\";s:1:\"1\";}}}', 'a:1:{i:0;a:2:{s:4:\"type\";s:19:\"set_gateway_address\";s:7:\"options\";a:1:{s:18:\"gateway_address_id\";s:10:\"department\";}}}', 0, '2012-10-22 20:00:25', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setgateway.update_user', '', 'update.user', 1, 'a:1:{i:0;a:3:{s:4:\"type\";s:10:\"department\";s:2:\"op\";s:7:\"changed\";s:7:\"options\";a:1:{s:10:\"department\";s:1:\"1\";}}}', 'a:1:{i:0;a:2:{s:4:\"type\";s:19:\"set_gateway_address\";s:7:\"options\";a:1:{s:18:\"gateway_address_id\";s:10:\"department\";}}}', 0, '2012-10-22 20:03:33', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.reply_agent', '', 'update.agent', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:1:\"0\";}}}', 0, '2012-10-24 14:10:26', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.reply_user', '', 'update.user', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:1:\"0\";}}}', 0, '2012-10-24 14:12:46', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.newemail_user', '', 'new.email.user', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:5:\"agent\";}}}', 0, '2012-10-24 14:14:05', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.newweb_user', '', 'new.web.user', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:5:\"agent\";}}}', 0, '2012-10-24 14:14:05', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.newemail_agent', '', 'new.email.agent', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:1:\"0\";}}}', 0, '2012-10-24 14:14:05', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.newweb_agent', '', 'new.web.agent.portal', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:1:\"0\";}}}', 0, '2012-10-24 14:14:05', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.newemail_user_str', '', 'new.email.user', 0, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:8:\"Helpdesk\";s:7:\"to_whom\";s:5:\"agent\";}}}', 0, '2012-11-15 10:43:35', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.newemail_user_dep', '', 'new.email.user', 0, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:21:\"{{ticket.department}}\";s:7:\"to_whom\";s:5:\"agent\";}}}', 0, '2012-11-15 10:44:02', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.newweb_user_str', '', 'new.web.user', 0, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:8:\"Helpdesk\";s:7:\"to_whom\";s:5:\"agent\";}}}', 0, '2012-11-15 10:43:35', 'a:0:{}', 'a:0:{}'),
	(NULL, 'setfrom.newweb_user_dep', '', 'new.web.user', 0, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:21:\"{{ticket.department}}\";s:7:\"to_whom\";s:5:\"agent\";}}}', 0, '2012-11-15 10:44:02', 'a:0:{}', 'a:0:{}')
");

$em->getConnection()->exec("
INSERT INTO `ticket_triggers` (`id`, `title`, `event_trigger`, `event_trigger_options`, `is_enabled`, `terms`, `terms_any`, `actions`, `sys_name`, `run_order`, `date_created`)
VALUES
	(NULL, '', 'new.email.agent', 'a:0:{}', 1, 'a:0:{}', 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:5:\"agent\";s:7:\"options\";a:1:{s:5:\"agent\";s:1:\"0\";}}}', 'agent_email_fwd_assignment', 120, '2012-11-28 10:26:06')
");

$em->getConnection()->exec("
INSERT INTO `ticket_triggers` (`id`, `title`, `event_trigger`, `event_trigger_options`, `is_enabled`, `terms`, `terms_any`, `actions`, `sys_name`, `run_order`, `date_created`)
VALUES
	(33, '', 'update.agent', 'a:0:{}', 1, 'a:2:{i:0;a:3:{s:4:\"type\";s:18:\"is_via_email_reply\";s:2:\"op\";s:2:\"is\";s:7:\"options\";a:1:{s:2:\"do\";s:1:\"1\";}}i:1;a:3:{s:4:\"type\";s:5:\"agent\";s:2:\"op\";s:2:\"is\";s:7:\"options\";a:1:{s:5:\"agent\";s:1:\"0\";}}}', 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:5:\"agent\";s:7:\"options\";a:1:{s:5:\"agent\";s:2:\"-1\";}}}', 'agent_email_reply_assignment', 30, '2012-11-28 10:44:17')
");

##BEGIN:create_style.master##

$s = new \Application\DeskPRO\Entity\Style();
$s['title'] = $translate->phrase('agent.defaults.default_style');
$s['note'] = $translate->phrase('agent.defaults.default_style');
$s['css_dir'] = 'stylesheets/user';
$em->persist($s);
$em->flush();


################################################################################
# Cron Jobs
################################################################################

##BEGIN:create_jobs.cleanup_always##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'cleanup_always';
$j['worker_group'] = 'cleanup';
$j['title'] = 'Cleanup: Always';
$j['description'] = 'Cleanup that runs every minute';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\CleanupAlways';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\CleanupAlways::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.cleanup_quarter_hourly##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'cleanup_quarter_hourly';
$j['worker_group'] = 'cleanup';
$j['title'] = 'Cleanup: Quarter Hourly';
$j['description'] = 'Cleanup that runs every 15 minutes';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\CleanupQuarterHourly';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\CleanupQuarterHourly::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.cleanup_hourly##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'cleanup_hourly';
$j['worker_group'] = 'cleanup';
$j['title'] = 'Cleanup: Hourly';
$j['description'] = 'Cleanup that runs every hour';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\CleanupHourly';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\CleanupHourly::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.cleanup_daily##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'cleanup_daily';
$j['worker_group'] = 'cleanup';
$j['title'] = 'Cleanup: Daily';
$j['description'] = 'Cleanup that runs every day';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\CleanupDaily';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\CleanupDaily::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.cleanup_weekly##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'cleanup_weekly';
$j['worker_group'] = 'cleanup';
$j['title'] = 'Cleanup: Weekly';
$j['description'] = 'Cleanup that runs every week';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\CleanupWeekly';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\CleanupWeekly::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.archive_tickets##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'archive_tickets';
$j['worker_group'] = 'archive_tickets';
$j['title'] = 'Archive Tickets';
$j['description'] = 'Archives old tickets';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\ArchiveTickets';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\ArchiveTickets::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.article_publish_state##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'article_publish_state';
$j['worker_group'] = 'article_publish_state';
$j['title'] = 'Article Publish State';
$j['description'] = 'Goes through articles with a publish date that was set in the future (publish now), or an end date set (deleting or archivng now).';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\ArticlePublishState';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\ArticlePublishState::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.chat_ping_timeout##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'chat_ping_timeout';
$j['worker_group'] = 'chat';
$j['title'] = 'Chat Ping Timeout';
$j['description'] = 'Timesout chats where both parties are not longer participating';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\ChatPingTimeout';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\ChatPingTimeout::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.chat_transcripts##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'chat_transcripts';
$j['worker_group'] = 'chat';
$j['title'] = 'Send Chat Transcripts';
$j['description'] = 'Send chat transcripts';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\ChatTranscripts';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\ChatTranscripts::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.sitemap_file##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'sitemap_file';
$j['worker_group'] = 'sitemap_file';
$j['title'] = 'Generate Sitemap';
$j['description'] = 'Generates the sitemap.xml file';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\SitemapFile';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\SitemapFile::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.ensure_search_tables##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'ensure_search_tables';
$j['worker_group'] = 'ensure_search_tables';
$j['title'] = 'Ensure Search Tables';
$j['description'] = 'Checks to make sure volatile search tables are fileld (i.e., in event of a reboot they are re-filled)';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\EnsureSearchTables';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\EnsureSearchTables::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.hard_delete_tickets##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'hard_delete_tickets';
$j['worker_group'] = 'hard_delete_tickets';
$j['title'] = 'Hard Delete Tickets';
$j['description'] = 'Processes tickets that were soft-deleted long ago and permanantly deletes them';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\HardDeleteTickets';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\HardDeleteTickets::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.search_index_update##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'search_index_update';
$j['worker_group'] = 'search';
$j['title'] = 'Search Index Update';
$j['description'] = 'Updates the search index with updated objects';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\SearchIndexUpdate';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\SearchIndexUpdate::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.sendmail_queue##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'sendmail_queue';
$j['worker_group'] = 'sendmail_queue';
$j['title'] = 'Sendmail Queue';
$j['description'] = 'Attempts to send queued mail, or re-send fail mail';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\SendmailQueue';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\SendmailQueue::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.heartbeat##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'heartbeat';
$j['worker_group'] = 'heartbeat';
$j['title'] = 'Heartbeat';
$j['description'] = 'Send heartbeat ping';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\Heartbeat';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\Heartbeat::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.kb_subscriptions##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'kb_subscriptions';
$j['worker_group'] = 'kb_subscriptions';
$j['title'] = 'KB Subscriptions';
$j['description'] = 'Sends notifications to users who are subscribed to articles or categories';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\KbSubscriptions';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\KbSubscriptions::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.move_blobs##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'move_blobs';
$j['worker_group'] = 'move_blobs';
$j['title'] = 'Move Blobs';
$j['description'] = 'When the storage mechanism is changed, this job moves existing blobs to the new mechanism a bit at a time';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\MoveBlobs';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\MoveBlobs::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.process_email_gateways##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'process_email_gateways';
$j['worker_group'] = 'process_email_gateways';
$j['title'] = 'Process Email Gateways';
$j['description'] = 'Runs through the email gateways and processes new messages';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\ProcessEmailGateways';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\ProcessEmailGateways::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.delete_spam_tickets##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'delete_spam_tickets';
$j['worker_group'] = 'delete_spam_tickets';
$j['title'] = 'Delete Spam Tickets';
$j['description'] = 'Runs through old spammed tickets and deletes them';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\DeleteSpamTickets';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\DeleteSpamTickets::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.agent_mode_ticket_reasssign##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'agent_mode_ticket_reasssign';
$j['worker_group'] = 'agent_mode_ticket_reasssign';
$j['title'] = 'Reassign tickets of vacation or deleted agents';
$j['description'] = 'When an agent enters vacation mode or is deleted, we need to batch-update their tickets to unassigned';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\AgentModeTicketReassign';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\AgentModeTicketReassign::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();


##BEGIN:create_jobs.ticket_triggers##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'ticket_triggers';
$j['worker_group'] = 'ticket_triggers';
$j['title'] = 'Ticket Triggers';
$j['description'] = 'Executes time-based ticket triggers';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\TicketTriggers';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\TicketTriggers::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.ticket_slas##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'ticket_slas';
$j['worker_group'] = 'ticket_slas';
$j['title'] = 'Ticket SLAs';
$j['description'] = 'Updates ticket SLA status';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\TicketSlas';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\TicketSlas::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.article_publish_state##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'update_view_counts';
$j['worker_group'] = 'update_view_counts';
$j['title'] = 'Update View Counts';
$j['description'] = 'Updates view counts on objects';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\UpdateViewCounts';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\UpdateViewCounts::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.run_queued_tasks##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'run_queued_tasks';
$j['worker_group'] = 'run_queued_tasks';
$j['title'] = 'Run Queued Tasks';
$j['description'] = 'Runs any general-purpose queued tasks';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\RunQueuedTasks';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\RunQueuedTasks::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.task_reminders##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'task_reminders';
$j['worker_group'] = 'task_reminders';
$j['title'] = 'Task Reminders';
$j['description'] = 'Sends task reminder notifications';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\TaskReminders';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\TaskReminders::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

##BEGIN:create_jobs.twitter_stream##
$j = new \Application\DeskPRO\Entity\WorkerJob();
$j['id'] = 'twitter_stream';
$j['worker_group'] = 'twitter_stream';
$j['title'] = 'Twitter Stream';
$j['description'] = 'Imports tweets from the Twitter stream';
$j['job_class'] = 'Application\\DeskPRO\\WorkerProcess\\Job\\TwitterStream';
$j['interval'] = \Application\DeskPRO\WorkerProcess\Job\TwitterStream::DEFAULT_INTERVAL;
$em->persist($j);
$em->flush();

################################################################################
# Portal Blocks
################################################################################

##BEGIN:create_portal_block.news##
$b = new \Application\DeskPRO\Entity\PortalPageDisplay();
$b->section = 'portal';
$b->type = 'news';
$b->is_enabled = true;
$em->persist($b);
$em->flush();

##BEGIN:create_portal_block.userinfo_sidebar##
$b = new \Application\DeskPRO\Entity\PortalPageDisplay();
$b->section = 'sidebar';
$b->type = 'userinfo';
$b->is_enabled = true;
$em->persist($b);
$em->flush();

##BEGIN:create_portal_block.kb_cat_list##
$b = new \Application\DeskPRO\Entity\PortalPageDisplay();
$b->section = 'sidebar';
$b->type = 'kb_cat_list';
$b->is_enabled = true;
$em->persist($b);
$em->flush();

##BEGIN:create_portal_block.feedback_cat_list##
$b = new \Application\DeskPRO\Entity\PortalPageDisplay();
$b->section = 'sidebar';
$b->type = 'feedback_cat_list';
$b->is_enabled = true;
$em->persist($b);
$em->flush();

##BEGIN:create_portal_block.downloads_cat_list##
$b = new \Application\DeskPRO\Entity\PortalPageDisplay();
$b->section = 'sidebar';
$b->type = 'downloads_cat_list';
$b->is_enabled = true;
$em->persist($b);
$em->flush();

##BEGIN:create_portal_block.staff_sidebar##
$b = new \Application\DeskPRO\Entity\PortalPageDisplay();
$b->section = 'sidebar';
$b->type = 'staff';
$b->is_enabled = true;
$em->persist($b);
$em->flush();

##BEGIN:create_portal_block.twitter_sidebar##
$b = new \Application\DeskPRO\Entity\PortalPageDisplay();
$b->section = 'sidebar';
$b->type = 'twitter';
$b->is_enabled = false;
$em->persist($b);
$em->flush();


################################################################################
# Agent Teams
################################################################################

##BEGIN:agent_teams.default1##
$t = new \Application\DeskPRO\Entity\AgentTeam();
$t['name'] = $translate->phrase('agent.defaults.team_support_managers');
$em->persist($t);
$em->flush();

##BEGIN:agent_teams.default2##
$t = new \Application\DeskPRO\Entity\AgentTeam();
$t['name'] = $translate->phrase('agent.defaults.team_lvl1_support');
$em->persist($t);
$em->flush();

##BEGIN:agent_teams.default3##
$t = new \Application\DeskPRO\Entity\AgentTeam();
$t['name'] = $translate->phrase('agent.defaults.team_lvl2_support');
$em->persist($t);
$em->flush();

##BEGIN:agent_teams.setting##
$t = new \Application\DeskPRO\Entity\Setting();
$t['name'] = 'core.use_agent_team';
$t['value'] = '1';
$em->persist($t);
$em->flush();

################################################################################
# Usergroups
################################################################################

##BEGIN:usergroups.everyone##
$g = new \Application\DeskPRO\Entity\Usergroup();
$g['title'] = $translate->phrase('agent.defaults.usergroup_everyone');
$g['note'] = $translate->phrase('agent.defaults.usergroup_everyone_note');
$g['sys_name'] = \Application\DeskPRO\Entity\Usergroup::EVERYONE_NAME;
$em->persist($g);
$em->flush();
$USERGROUP_EVERYONE = $g;

##BEGIN:usergroups.register##
$g = new \Application\DeskPRO\Entity\Usergroup();
$g['title'] = $translate->phrase('agent.defaults.usergroup_registered');
$g['note'] = $translate->phrase('agent.defaults.usergroup_registered_note');
$g['sys_name'] = \Application\DeskPRO\Entity\Usergroup::REG_NAME;
$em->persist($g);
$em->flush();
$USERGROUP_REG = $g;

##BEGIN:usergroups.agent_all##
$AGENTGROUP_ALL = new \Application\DeskPRO\Entity\Usergroup();
$AGENTGROUP_ALL['title'] = $translate->phrase('agent.defaults.usergroup_agent_all_perms');
$AGENTGROUP_ALL['note'] = $translate->phrase('agent.defaults.usergroup_agent_all_perms_note');
$AGENTGROUP_ALL['is_agent_group'] = true;
$em->persist($AGENTGROUP_ALL);
$em->flush();

##BEGIN:usergroups.agent_all_nondestructive##
$AGENTGROUP_ALL_ND = new \Application\DeskPRO\Entity\Usergroup();
$AGENTGROUP_ALL_ND['title'] = $translate->phrase('agent.defaults.usergroup_agent_all_non_destructive');
$AGENTGROUP_ALL_ND['note'] = $translate->phrase('agent.defaults.usergroup_agent_all_non_destructive_note');
$AGENTGROUP_ALL_ND['is_agent_group'] = true;
$em->persist($AGENTGROUP_ALL_ND);
$em->flush();

// Permissions for ND group
$ugid = $AGENTGROUP_ALL_ND->getId();
$em->getConnection()->executeUpdate("
	INSERT INTO `permissions` (`usergroup_id`, `person_id`, `value`, `name`)
	VALUES
		($ugid, NULL, '1', 'agent_tickets.use'),
		($ugid, NULL, '1', 'agent_tickets.create'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_closed'),
		($ugid, NULL, '1', 'agent_tickets.reply_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_department_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_fields_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_agent_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_team_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_self_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_cc_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_merge_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_labels_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_notes_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_hold_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_awaiting_user_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_awaiting_agent_own'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_resolved_own'),
		($ugid, NULL, '1', 'agent_tickets.reply_to_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_department_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_fields_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_agent_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_team_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_self_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_cc_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_merge_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_labels_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_notes_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_hold_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_awaiting_user_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_awaiting_agent_followed'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_resolved_followed'),
		($ugid, NULL, '1', 'agent_tickets.view_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.reply_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_department_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_fields_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_agent_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_team_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_self_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_merge_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_labels_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_notes_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_hold_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_awaiting_user_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_awaiting_agent_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_resolved_unassigned'),
		($ugid, NULL, '1', 'agent_tickets.view_others'),
		($ugid, NULL, '1', 'agent_tickets.reply_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_department_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_fields_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_agent_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_team_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_assign_self_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_merge_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_labels_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_notes_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_hold_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_awaiting_user_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_awaiting_agent_others'),
		($ugid, NULL, '1', 'agent_tickets.modify_set_resolved_others'),
		($ugid, NULL, '1', 'agent_people.use'),
		($ugid, NULL, '1', 'agent_people.create'),
		($ugid, NULL, '1', 'agent_people.edit'),
		($ugid, NULL, '1', 'agent_people.validate'),
		($ugid, NULL, '1', 'agent_people.manage_emails'),
		($ugid, NULL, '1', 'agent_people.reset_password'),
		($ugid, NULL, '1', 'agent_people.notes'),
		($ugid, NULL, '1', 'agent_people.disable'),
		($ugid, NULL, '1', 'agent_org.create'),
		($ugid, NULL, '1', 'agent_org.edit'),
		($ugid, NULL, '1', 'agent_chat.use'),
		($ugid, NULL, '1', 'agent_chat.view_unassigned'),
		($ugid, NULL, '1', 'agent_chat.view_others'),
		($ugid, NULL, '1', 'agent_publish.create'),
		($ugid, NULL, '1', 'agent_publish.edit'),
		($ugid, NULL, '1', 'agent_publish.validate'),
		($ugid, NULL, '1', 'agent_general.signature'),
		($ugid, NULL, '1', 'agent_general.signature_rte')
");