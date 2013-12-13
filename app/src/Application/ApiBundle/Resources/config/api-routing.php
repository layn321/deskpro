<?php if (!defined('DP_ROOT')) exit('No access');

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

################################################################################
# Misc
################################################################################

$collection->add('api', new Route(
	'/',
	array('_controller' => 'ApiBundle:Test:About'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_test', new Route(
	'/test',
	array('_controller' => 'ApiBundle:Test:test'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_test_post', new Route(
	'/test',
	array('_controller' => 'ApiBundle:Test:postTest'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_deskpro_time', new Route(
	'/deskpro/time',
	array('_controller' => 'ApiBundle:Deskpro:time'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_deskpro_info', new Route(
	'/deskpro/info',
	array('_controller' => 'ApiBundle:Misc:helpdeskInfo'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_token_exchange', new Route(
	'/token-exchange',
	array('_controller' => 'ApiBundle:Misc:tokenExchange'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_token_renew', new Route(
	'/renew-token',
	array('_controller' => 'ApiBundle:Misc:renewToken'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_misc_upload', new Route(
	'/misc/upload',
	array('_controller' => 'ApiBundle:Misc:upload'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_misc_session_person', new Route(
	'/misc/session-person/{session_code}',
	array('_controller' => 'ApiBundle:Misc:getSessionPerson'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_misc_rate_limit', new Route(
	'/misc/rate-limit',
	array('_controller' => 'ApiBundle:Misc:getRateLimit'),
	array('_method' => 'GET'),
	array()
));

################################################################################
# Tickets
################################################################################

$collection->add('api_tickets_new', new Route(
	'/tickets',
	array('_controller' => 'ApiBundle:Ticket:newTicket'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_tickets_ticket', new Route(
	'/tickets/{ticket_id}',
	array('_controller' => 'ApiBundle:Ticket:getTicket'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_post', new Route(
	'/tickets/{ticket_id}',
	array('_controller' => 'ApiBundle:Ticket:postTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_delete', new Route(
	'/tickets/{ticket_id}',
	array('_controller' => 'ApiBundle:Ticket:deleteTicket'),
	array('_method' => 'DELETE', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_logs', new Route(
	'/tickets/{ticket_id}/logs',
	array('_controller' => 'ApiBundle:Ticket:getTicketLogs'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_messages', new Route(
	'/tickets/{ticket_id}/messages',
	array('_controller' => 'ApiBundle:Ticket:getTicketMessages'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_messages_post', new Route(
	'/tickets/{ticket_id}/messages',
	array('_controller' => 'ApiBundle:Ticket:replyTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_message', new Route(
	'/tickets/{ticket_id}/messages/{message_id}',
	array('_controller' => 'ApiBundle:Ticket:getTicketMessage'),
	array('_method' => 'GET', 'ticket_id' => '\\d+', 'message_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_message_details', new Route(
	'/tickets/{ticket_id}/messages/{message_id}/details',
	array('_controller' => 'ApiBundle:Ticket:getTicketMessageDetails'),
	array('_method' => 'GET', 'ticket_id' => '\\d+', 'message_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_undelete', new Route(
	'/tickets/{ticket_id}/undelete',
	array('_controller' => 'ApiBundle:Ticket:undeleteTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_split', new Route(
	'/tickets/{ticket_id}/split',
	array('_controller' => 'ApiBundle:Ticket:splitTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_claim', new Route(
	'/tickets/{ticket_id}/claim',
	array('_controller' => 'ApiBundle:Ticket:claimTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_merge', new Route(
	'/tickets/{ticket_id}/merge/{merge_ticket_id}',
	array('_controller' => 'ApiBundle:Ticket:mergeTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+', 'merge_ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_spam', new Route(
	'/tickets/{ticket_id}/spam',
	array('_controller' => 'ApiBundle:Ticket:spamTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_unspam', new Route(
	'/tickets/{ticket_id}/unspam',
	array('_controller' => 'ApiBundle:Ticket:unspamTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_lock', new Route(
	'/tickets/{ticket_id}/lock',
	array('_controller' => 'ApiBundle:Ticket:lockTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_unlock', new Route(
	'/tickets/{ticket_id}/unlock',
	array('_controller' => 'ApiBundle:Ticket:unlockTicket'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_tasks', new Route(
	'/tickets/{ticket_id}/tasks',
	array('_controller' => 'ApiBundle:Ticket:getTicketTasks'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_tasks_post', new Route(
	'/tickets/{ticket_id}/tasks',
	array('_controller' => 'ApiBundle:Ticket:postTicketTasks'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_billing_charges', new Route(
	'/tickets/{ticket_id}/billing-charges',
	array('_controller' => 'ApiBundle:Ticket:getTicketBillingCharges'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_billing_charges_post', new Route(
	'/tickets/{ticket_id}/billing-charges',
	array('_controller' => 'ApiBundle:Ticket:postTicketBillingCharges'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_billing_charge', new Route(
	'/tickets/{ticket_id}/billing-charges/{charge_id}',
	array('_controller' => 'ApiBundle:Ticket:getTicketBillingCharge'),
	array('_method' => 'GET', 'ticket_id' => '\\d+', 'charge_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_billing_charge_delete', new Route(
	'/tickets/{ticket_id}/billing-charges/{charge_id}',
	array('_controller' => 'ApiBundle:Ticket:deleteTicketBillingCharge'),
	array('_method' => 'DELETE', 'ticket_id' => '\\d+', 'charge_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_slas', new Route(
	'/tickets/{ticket_id}/slas',
	array('_controller' => 'ApiBundle:Ticket:getTicketSlas'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_slas_post', new Route(
	'/tickets/{ticket_id}/slas',
	array('_controller' => 'ApiBundle:Ticket:postTicketSlas'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_sla', new Route(
	'/tickets/{ticket_id}/slas/{ticket_sla_id}',
	array('_controller' => 'ApiBundle:Ticket:getTicketSla'),
	array('_method' => 'GET', 'ticket_id' => '\\d+', 'ticket_sla_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_sla_delete', new Route(
	'/tickets/{ticket_id}/slas/{ticket_sla_id}',
	array('_controller' => 'ApiBundle:Ticket:deleteTicketSla'),
	array('_method' => 'DELETE', 'ticket_id' => '\\d+', 'ticket_sla_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_participants', new Route(
	'/tickets/{ticket_id}/participants',
	array('_controller' => 'ApiBundle:Ticket:getParticipants'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_participants_post', new Route(
	'/tickets/{ticket_id}/participants',
	array('_controller' => 'ApiBundle:Ticket:postParticipants'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_participant', new Route(
	'/tickets/{ticket_id}/participants/{person_id}',
	array('_controller' => 'ApiBundle:Ticket:getParticipant'),
	array('_method' => 'GET', 'ticket_id' => '\\d+', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_participant_delete', new Route(
	'/tickets/{ticket_id}/participants/{person_id}',
	array('_controller' => 'ApiBundle:Ticket:deleteParticipant'),
	array('_method' => 'DELETE', 'ticket_id' => '\\d+', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_labels', new Route(
	'/tickets/{ticket_id}/labels',
	array('_controller' => 'ApiBundle:Ticket:getLabels'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_labels_post', new Route(
	'/tickets/{ticket_id}/labels',
	array('_controller' => 'ApiBundle:Ticket:postLabels'),
	array('_method' => 'POST', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_label', new Route(
	'/tickets/{ticket_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Ticket:getLabel'),
	array('_method' => 'GET', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_ticket_label_delete', new Route(
	'/tickets/{ticket_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Ticket:deleteLabel'),
	array('_method' => 'DELETE', 'ticket_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_fields', new Route(
	'/tickets/fields',
	array('_controller' => 'ApiBundle:Ticket:getFields'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_departments', new Route(
	'/tickets/departments',
	array('_controller' => 'ApiBundle:Ticket:getDepartments'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_products', new Route(
	'/tickets/products',
	array('_controller' => 'ApiBundle:Ticket:getProducts'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_categories', new Route(
	'/tickets/categories',
	array('_controller' => 'ApiBundle:Ticket:getCategories'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_priorities', new Route(
	'/tickets/priorities',
	array('_controller' => 'ApiBundle:Ticket:getPriorities'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_workflows', new Route(
	'/tickets/workflows',
	array('_controller' => 'ApiBundle:Ticket:getWorkflows'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_slas', new Route(
	'/tickets/slas',
	array('_controller' => 'ApiBundle:Ticket:getSlas'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_sla', new Route(
	'/tickets/slas/{sla_id}',
	array('_controller' => 'ApiBundle:Ticket:getSla'),
	array('_method' => 'GET', 'sla_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_sla_people', new Route(
	'/tickets/slas/{sla_id}/people',
	array('_controller' => 'ApiBundle:Ticket:getSlaPeople'),
	array('_method' => 'GET', 'sla_id' => '\\d+'),
	array()
));

$collection->add('api_tickets_sla_organizations', new Route(
	'/tickets/slas/{sla_id}/organizations',
	array('_controller' => 'ApiBundle:Ticket:getSlaOrganizations'),
	array('_method' => 'GET', 'sla_id' => '\\d+'),
	array()
));

################################################################################
# Text Snippets
################################################################################

$collection->add('api_textsnippets_list', new Route(
	'/text-snippets/{typename}',
	array('_controller' => 'ApiBundle:TextSnippets:filterSnippets'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_textsnippets_new', new Route(
	'/text-snippets/{typename}',
	array('_controller' => 'ApiBundle:TextSnippets:saveSnippet', 'snippet_id' => '0'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_textsnippets_edit', new Route(
	'/text-snippets/{typename}/{snippet_id}',
	array('_controller' => 'ApiBundle:TextSnippets:saveSnippet'),
	array('_method' => 'POST', 'snippet_id' => '\d+'),
	array()
));

$collection->add('api_textsnippets_del', new Route(
	'/text-snippets/{typename}/{snippet_id}',
	array('_controller' => 'ApiBundle:TextSnippets:deleteSnippet'),
	array('_method' => 'DELETE', 'snippet_id' => '\d+'),
	array()
));

$collection->add('api_textsnippets_get', new Route(
	'/text-snippets/{typename}/{snippet_id}',
	array('_controller' => 'ApiBundle:TextSnippets:getSnippet'),
	array('_method' => 'GET', 'snippet_id' => '\d+'),
	array()
));

$collection->add('api_textsnippets_cats_list', new Route(
	'/text-snippets/{typename}/categories',
	array('_controller' => 'ApiBundle:TextSnippets:listCategories'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_textsnippets_cats_new', new Route(
	'/text-snippets/{typename}/categories',
	array('_controller' => 'ApiBundle:TextSnippets:saveCategory', 'category_id' => '0'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_textsnippets_cats_edit', new Route(
	'/text-snippets/{typename}/categories/{category_id}',
	array('_controller' => 'ApiBundle:TextSnippets:saveCategory'),
	array('_method' => 'POST', 'category_id' => '\d+'),
	array()
));

$collection->add('api_textsnippets_cats_get', new Route(
	'/text-snippets/{typename}/categories/{category_id}',
	array('_controller' => 'ApiBundle:TextSnippets:getCategory'),
	array('_method' => 'GET', 'category_id' => '\d+'),
	array()
));

$collection->add('api_textsnippets_cats_del', new Route(
	'/text-snippets/{typename}/categories/{category_id}',
	array('_controller' => 'ApiBundle:TextSnippets:deleteCategory'),
	array('_method' => 'DELETE', 'category_id' => '\d+'),
	array()
));

################################################################################
# Open Ticket Controller
################################################################################

$collection->add('api_open_tickets_newticketmessage', new Route(
	'/open/tickets/new-ticket-message',
	array('_controller' => 'ApiBundle:OpenTicket:newTicketMessage'),
	array('_method' => 'POST', 'sla_id' => '\\d+'),
	array()
));

################################################################################
# Ticket filters
################################################################################

$collection->add('api_tickets', new Route(
	'/tickets',
	array('_controller' => 'ApiBundle:TicketSearch:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_filters', new Route(
	'/tickets/filters',
	array('_controller' => 'ApiBundle:TicketSearch:getFilters'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_filter_counts', new Route(
	'/tickets/filters/counts',
	array('_controller' => 'ApiBundle:TicketSearch:getFilterCounts'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tickets_filter', new Route(
	'/tickets/filters/{filter_id}',
	array('_controller' => 'ApiBundle:TicketSearch:getFilter'),
	array('_method' => 'GET', 'filter_id' => '\\d+'),
	array()
));

################################################################################
# People
################################################################################

$collection->add('api_people', new Route(
	'/people',
	array('_controller' => 'ApiBundle:Person:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_people_post', new Route(
	'/people',
	array('_controller' => 'ApiBundle:Person:newPerson'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_people_person', new Route(
	'/people/{person_id}',
	array('_controller' => 'ApiBundle:Person:getPerson'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_post', new Route(
	'/people/{person_id}',
	array('_controller' => 'ApiBundle:Person:postPerson'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_delete', new Route(
	'/people/{person_id}',
	array('_controller' => 'ApiBundle:Person:deletePerson'),
	array('_method' => 'DELETE', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_merge', new Route(
	'/people/{person_id}/merge/{other_person_id}',
	array('_controller' => 'ApiBundle:Person:mergePerson'),
	array('_method' => 'POST', 'person_id' => '\\d+', 'other_person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_picture', new Route(
	'/people/{person_id}/picture',
	array('_controller' => 'ApiBundle:Person:getPersonPicture'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_picture_post', new Route(
	'/people/{person_id}/picture',
	array('_controller' => 'ApiBundle:Person:postPersonPicture'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_picture_delete', new Route(
	'/people/{person_id}/picture',
	array('_controller' => 'ApiBundle:Person:deletePersonPicture'),
	array('_method' => 'DELETE', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_emails', new Route(
	'/people/{person_id}/emails',
	array('_controller' => 'ApiBundle:Person:getPersonEmails'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_emails_post', new Route(
	'/people/{person_id}/emails',
	array('_controller' => 'ApiBundle:Person:postPersonEmails'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_email', new Route(
	'/people/{person_id}/emails/{email_id}',
	array('_controller' => 'ApiBundle:Person:getPersonEmail'),
	array('_method' => 'GET', 'person_id' => '\\d+', 'email_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_email_post', new Route(
	'/people/{person_id}/emails/{email_id}',
	array('_controller' => 'ApiBundle:Person:postPersonEmail'),
	array('_method' => 'POST', 'person_id' => '\\d+', 'email_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_email_delete', new Route(
	'/people/{person_id}/emails/{email_id}',
	array('_controller' => 'ApiBundle:Person:deletePersonEmail'),
	array('_method' => 'DELETE', 'person_id' => '\\d+', 'email_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_vcard', new Route(
	'/people/{person_id}/vcard',
	array('_controller' => 'ApiBundle:Person:getPersonVcard'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_tickets', new Route(
	'/people/{person_id}/tickets',
	array('_controller' => 'ApiBundle:Person:getPersonTickets'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_chats', new Route(
	'/people/{person_id}/chats',
	array('_controller' => 'ApiBundle:Person:getPersonChats'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_activity_stream', new Route(
	'/people/{person_id}/activity-stream',
	array('_controller' => 'ApiBundle:Person:getPersonActivityStream'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_reset_password', new Route(
	'/people/{person_id}/reset-password',
	array('_controller' => 'ApiBundle:Person:resetPassword'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_clear_session', new Route(
	'/people/{person_id}/clear-session',
	array('_controller' => 'ApiBundle:Person:clearSession'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_slas', new Route(
	'/people/{person_id}/slas',
	array('_controller' => 'ApiBundle:Person:getPersonSlas'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_slas_post', new Route(
	'/people/{person_id}/slas',
	array('_controller' => 'ApiBundle:Person:postPersonSlas'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_sla', new Route(
	'/people/{person_id}/slas/{sla_id}',
	array('_controller' => 'ApiBundle:Person:getPersonSla'),
	array('_method' => 'GET', 'person_id' => '\\d+', 'sla_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_sla_delete', new Route(
	'/people/{person_id}/slas/{sla_id}',
	array('_controller' => 'ApiBundle:Person:deletePersonSla'),
	array('_method' => 'DELETE', 'person_id' => '\\d+', 'sla_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_notes', new Route(
	'/people/{person_id}/notes',
	array('_controller' => 'ApiBundle:Person:getPersonNotes'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_notes_post', new Route(
	'/people/{person_id}/notes',
	array('_controller' => 'ApiBundle:Person:postPersonNotes'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_billing_charges', new Route(
	'/people/{person_id}/billing-charges',
	array('_controller' => 'ApiBundle:Person:getPersonBillingCharges'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_contact_details', new Route(
	'/people/{person_id}/contact-details',
	array('_controller' => 'ApiBundle:Person:getPersonContactDetails'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_contact_details_post', new Route(
	'/people/{person_id}/contact-details',
	array('_controller' => 'ApiBundle:Person:postPersonContactDetails'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_contact_detail', new Route(
	'/people/{person_id}/contact-details/{contact_id}',
	array('_controller' => 'ApiBundle:Person:getPersonContactDetail'),
	array('_method' => 'GET', 'person_id' => '\\d+', 'contact_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_contact_detail_delete', new Route(
	'/people/{person_id}/contact-details/{contact_id}',
	array('_controller' => 'ApiBundle:Person:deletePersonContactDetail'),
	array('_method' => 'DELETE', 'person_id' => '\\d+', 'contact_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_groups', new Route(
	'/people/{person_id}/groups',
	array('_controller' => 'ApiBundle:Person:getPersonGroups'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_groups_post', new Route(
	'/people/{person_id}/groups',
	array('_controller' => 'ApiBundle:Person:postPersonGroups'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_group', new Route(
	'/people/{person_id}/groups/{usergroup_id}',
	array('_controller' => 'ApiBundle:Person:getPersonGroup'),
	array('_method' => 'GET', 'person_id' => '\\d+', 'usergroup_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_group_delete', new Route(
	'/people/{person_id}/groups/{usergroup_id}',
	array('_controller' => 'ApiBundle:Person:deletePersonGroup'),
	array('_method' => 'DELETE', 'person_id' => '\\d+', 'usergroup_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_labels', new Route(
	'/people/{person_id}/labels',
	array('_controller' => 'ApiBundle:Person:getPersonLabels'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_labels_post', new Route(
	'/people/{person_id}/labels',
	array('_controller' => 'ApiBundle:Person:postPersonLabels'),
	array('_method' => 'POST', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_label', new Route(
	'/people/{person_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Person:getPersonLabel'),
	array('_method' => 'GET', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_person_label_delete', new Route(
	'/people/{person_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Person:deletePersonLabel'),
	array('_method' => 'DELETE', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_people_fields', new Route(
	'/people/fields',
	array('_controller' => 'ApiBundle:Person:getFields'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_people_groups', new Route(
	'/people/groups',
	array('_controller' => 'ApiBundle:Person:getGroups'),
	array('_method' => 'GET'),
	array()
));

################################################################################
# Organizations
################################################################################

$collection->add('api_organizations', new Route(
	'/organizations',
	array('_controller' => 'ApiBundle:Organization:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_organizations_post', new Route(
	'/organizations',
	array('_controller' => 'ApiBundle:Organization:newOrganization'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_organizations_organization', new Route(
	'/organizations/{organization_id}',
	array('_controller' => 'ApiBundle:Organization:getOrganization'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_post', new Route(
	'/organizations/{organization_id}',
	array('_controller' => 'ApiBundle:Organization:postOrganization'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_delete', new Route(
	'/organizations/{organization_id}',
	array('_controller' => 'ApiBundle:Organization:deleteOrganization'),
	array('_method' => 'DELETE', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_picture', new Route(
	'/organizations/{organization_id}/picture',
	array('_controller' => 'ApiBundle:Organization:getOrganizationPicture'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_picture_post', new Route(
	'/organizations/{organization_id}/picture',
	array('_controller' => 'ApiBundle:Organization:postOrganizationPicture'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_picture_delete', new Route(
	'/organizations/{organization_id}/picture',
	array('_controller' => 'ApiBundle:Organization:deleteOrganizationPicture'),
	array('_method' => 'DELETE', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_activity_stream', new Route(
	'/organizations/{organization_id}/activity-stream',
	array('_controller' => 'ApiBundle:Organization:getOrganizationActivityStream'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_members', new Route(
	'/organizations/{organization_id}/members',
	array('_controller' => 'ApiBundle:Organization:getOrganizationMembers'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_tickets', new Route(
	'/organizations/{organization_id}/tickets',
	array('_controller' => 'ApiBundle:Organization:getOrganizationTickets'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_chats', new Route(
	'/organizations/{organization_id}/chats',
	array('_controller' => 'ApiBundle:Organization:getOrganizationChats'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_slas', new Route(
	'/organizations/{organization_id}/slas',
	array('_controller' => 'ApiBundle:Organization:getOrganizationSlas'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_slas_post', new Route(
	'/organizations/{organization_id}/slas',
	array('_controller' => 'ApiBundle:Organization:postOrganizationSlas'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_sla', new Route(
	'/organizations/{organization_id}/slas/{sla_id}',
	array('_controller' => 'ApiBundle:Organization:getOrganizationSla'),
	array('_method' => 'GET', 'organization_id' => '\\d+', 'sla_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_sla_delete', new Route(
	'/organizations/{organization_id}/slas/{sla_id}',
	array('_controller' => 'ApiBundle:Organization:deleteOrganizationSla'),
	array('_method' => 'DELETE', 'organization_id' => '\\d+', 'sla_id' => '\\d+'),
	array()
));

/*$collection->add('api_organizations_organization_notes', new Route(
	'/organizations/{organization_id}/notes',
	array('_controller' => 'ApiBundle:Organization:getOrganizationNotes'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_notes_post', new Route(
	'/organizations/{organization_id}/notes',
	array('_controller' => 'ApiBundle:Organization:postOrganizationNotes'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_notes_note', new Route(
	'/organizations/{organization_id}/notes/{note_id}',
	array('_controller' => 'ApiBundle:Organization:getOrganizationNote'),
	array('_method' => 'GET', 'organization_id' => '\\d+', 'note_id' => '\\d+'),
	array()
));*/

$collection->add('api_organizations_organization_billing_charges', new Route(
	'/organizations/{organization_id}/billing-charges',
	array('_controller' => 'ApiBundle:Organization:getOrganizationBillingCharges'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_email_domains', new Route(
	'/organizations/{organization_id}/email-domains',
	array('_controller' => 'ApiBundle:Organization:getOrganizationEmailDomains'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_email_domains_post', new Route(
	'/organizations/{organization_id}/email-domains',
	array('_controller' => 'ApiBundle:Organization:postOrganizationEmailDomains'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_email_domain', new Route(
	'/organizations/{organization_id}/email-domains/{domain}',
	array('_controller' => 'ApiBundle:Organization:getOrganizationEmailDomain'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_email_domain_move_users', new Route(
	'/organizations/{organization_id}/email-domains/{domain}/move-users',
	array('_controller' => 'ApiBundle:Organization:postOrganizationEmailDomainMoveUsers'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_email_domain_move_taken_users', new Route(
	'/organizations/{organization_id}/email-domains/{domain}/move-taken-users',
	array('_controller' => 'ApiBundle:Organization:postOrganizationEmailDomainMoveTakenUsers'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_email_domain_delete', new Route(
	'/organizations/{organization_id}/email-domains/{domain}',
	array('_controller' => 'ApiBundle:Organization:deleteOrganizationEmailDomain'),
	array('_method' => 'DELETE', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_contact_details', new Route(
	'/organizations/{organization_id}/contact-details',
	array('_controller' => 'ApiBundle:Organization:getOrganizationContactDetails'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_contact_details_post', new Route(
	'/organizations/{organization_id}/contact-details',
	array('_controller' => 'ApiBundle:Organization:postOrganizationContactDetails'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_contact_detail', new Route(
	'/organizations/{organization_id}/contact-details/{contact_id}',
	array('_controller' => 'ApiBundle:Organization:getOrganizationContactDetail'),
	array('_method' => 'GET', 'organization_id' => '\\d+', 'contact_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_contact_detail_delete', new Route(
	'/organizations/{organization_id}/contact-details/{contact_id}',
	array('_controller' => 'ApiBundle:Organization:deleteOrganizationContactDetail'),
	array('_method' => 'DELETE', 'organization_id' => '\\d+', 'contact_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_groups', new Route(
	'/organizations/{organization_id}/groups',
	array('_controller' => 'ApiBundle:Organization:getOrganizationGroups'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_groups_post', new Route(
	'/organizations/{organization_id}/groups',
	array('_controller' => 'ApiBundle:Organization:postOrganizationGroups'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_group', new Route(
	'/organizations/{organization_id}/groups/{usergroup_id}',
	array('_controller' => 'ApiBundle:Organization:getOrganizationGroup'),
	array('_method' => 'GET', 'organization_id' => '\\d+', 'usergroup_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_group_delete', new Route(
	'/organizations/{organization_id}/groups/{usergroup_id}',
	array('_controller' => 'ApiBundle:Organization:deleteOrganizationGroup'),
	array('_method' => 'DELETE', 'organization_id' => '\\d+', 'usergroup_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_labels', new Route(
	'/organizations/{organization_id}/labels',
	array('_controller' => 'ApiBundle:Organization:getOrganizationLabels'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_labels_post', new Route(
	'/organizations/{organization_id}/labels',
	array('_controller' => 'ApiBundle:Organization:postOrganizationLabels'),
	array('_method' => 'POST', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_label', new Route(
	'/organizations/{organization_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Organization:getOrganizationLabel'),
	array('_method' => 'GET', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_organization_label_delete', new Route(
	'/organizations/{organization_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Organization:deleteOrganizationLabel'),
	array('_method' => 'DELETE', 'organization_id' => '\\d+'),
	array()
));

$collection->add('api_organizations_fields', new Route(
	'/organizations/fields',
	array('_controller' => 'ApiBundle:Organization:getFields'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_organizations_groups', new Route(
	'/organizations/groups',
	array('_controller' => 'ApiBundle:Organization:getGroups'),
	array('_method' => 'GET'),
	array()
));

################################################################################
# Chats
################################################################################

$collection->add('api_chats', new Route(
	'/chats',
	array('_controller' => 'ApiBundle:Chat:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_chats_chat', new Route(
	'/chats/{chat_id}',
	array('_controller' => 'ApiBundle:Chat:getChat'),
	array('_method' => 'GET', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_post', new Route(
	'/chats/{chat_id}',
	array('_controller' => 'ApiBundle:Chat:postChat'),
	array('_method' => 'POST', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_leave', new Route(
	'/chats/{chat_id}/leave',
	array('_controller' => 'ApiBundle:Chat:leaveChat'),
	array('_method' => 'POST', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_end', new Route(
	'/chats/{chat_id}/end',
	array('_controller' => 'ApiBundle:Chat:endChat'),
	array('_method' => 'POST', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_messages', new Route(
	'/chats/{chat_id}/messages',
	array('_controller' => 'ApiBundle:Chat:getMessages'),
	array('_method' => 'GET', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_messages_post', new Route(
	'/chats/{chat_id}/messages',
	array('_controller' => 'ApiBundle:Chat:newMessage'),
	array('_method' => 'POST', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_participants', new Route(
	'/chats/{chat_id}/participants',
	array('_controller' => 'ApiBundle:Chat:getParticipants'),
	array('_method' => 'GET', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_participants_post', new Route(
	'/chats/{chat_id}/participants',
	array('_controller' => 'ApiBundle:Chat:postParticipants'),
	array('_method' => 'POST', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_participant', new Route(
	'/chats/{chat_id}/participants/{person_id}',
	array('_controller' => 'ApiBundle:Chat:getParticipant'),
	array('_method' => 'GET', 'chat_id' => '\\d+', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_participant_delete', new Route(
	'/chats/{chat_id}/participants/{person_id}',
	array('_controller' => 'ApiBundle:Chat:deleteParticipant'),
	array('_method' => 'DELETE', 'chat_id' => '\\d+', 'person_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_labels', new Route(
	'/chats/{chat_id}/labels',
	array('_controller' => 'ApiBundle:Chat:getChatLabels'),
	array('_method' => 'GET', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_labels_post', new Route(
	'/chats/{chat_id}/labels',
	array('_controller' => 'ApiBundle:Chat:postChatLabels'),
	array('_method' => 'POST', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_label', new Route(
	'/chats/{chat_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Chat:getChatLabel'),
	array('_method' => 'GET', 'chat_id' => '\\d+'),
	array()
));

$collection->add('api_chats_chat_label_delete', new Route(
	'/chats/{chat_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Chat:deleteChatLabel'),
	array('_method' => 'DELETE', 'chat_id' => '\\d+'),
	array()
));

################################################################################
# Downloads
################################################################################

$collection->add('api_downloads', new Route(
	'/downloads',
	array('_controller' => 'ApiBundle:Download:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_downloads_post', new Route(
	'/downloads',
	array('_controller' => 'ApiBundle:Download:newDownload'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_downloads_download', new Route(
	'/downloads/{download_id}',
	array('_controller' => 'ApiBundle:Download:getDownload'),
	array('_method' => 'GET', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_post', new Route(
	'/downloads/{download_id}',
	array('_controller' => 'ApiBundle:Download:postDownload'),
	array('_method' => 'POST', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_delete', new Route(
	'/downloads/{download_id}',
	array('_controller' => 'ApiBundle:Download:deleteDownload'),
	array('_method' => 'DELETE', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_comments', new Route(
	'/downloads/{download_id}/comments',
	array('_controller' => 'ApiBundle:Download:getDownloadComments'),
	array('_method' => 'GET', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_comments_new', new Route(
	'/downloads/{download_id}/comments',
	array('_controller' => 'ApiBundle:Download:newDownloadComment'),
	array('_method' => 'POST', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_comments_comment', new Route(
	'/downloads/{download_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Download:getDownloadComment'),
	array('_method' => 'GET', 'download_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_comments_comment_post', new Route(
	'/downloads/{download_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Download:postDownloadComment'),
	array('_method' => 'POST', 'download_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_comments_comment_delete', new Route(
	'/downloads/{download_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Download:deleteDownloadComment'),
	array('_method' => 'DELETE', 'download_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_labels', new Route(
	'/downloads/{download_id}/labels',
	array('_controller' => 'ApiBundle:Download:getDownloadLabels'),
	array('_method' => 'GET', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_labels_post', new Route(
	'/downloads/{download_id}/labels',
	array('_controller' => 'ApiBundle:Download:postDownloadLabels'),
	array('_method' => 'POST', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_label', new Route(
	'/downloads/{download_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Download:getDownloadLabel'),
	array('_method' => 'GET', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_download_label_delete', new Route(
	'/downloads/{download_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Download:deleteDownloadLabel'),
	array('_method' => 'DELETE', 'download_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_validating_comments', new Route(
	'/downloads/validating-comments',
	array('_controller' => 'ApiBundle:Download:getValidatingComments'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_downloads_categories', new Route(
	'/downloads/categories',
	array('_controller' => 'ApiBundle:Download:getCategories'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_downloads_categories_post', new Route(
	'/downloads/categories',
	array('_controller' => 'ApiBundle:Download:postCategories'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_downloads_category', new Route(
	'/downloads/categories/{category_id}',
	array('_controller' => 'ApiBundle:Download:getCategory'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_category_post', new Route(
	'/downloads/categories/{category_id}',
	array('_controller' => 'ApiBundle:Download:postCategory'),
	array('_method' => 'POST', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_category_delete', new Route(
	'/downloads/categories/{category_id}',
	array('_controller' => 'ApiBundle:Download:deleteCategory'),
	array('_method' => 'DELETE', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_category_downloads', new Route(
	'/downloads/categories/{category_id}/downloads',
	array('_controller' => 'ApiBundle:Download:getCategoryDownloads'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_category_groups', new Route(
	'/downloads/categories/{category_id}/groups',
	array('_controller' => 'ApiBundle:Download:getCategoryGroups'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_category_groups_post', new Route(
	'/downloads/categories/{category_id}/groups',
	array('_controller' => 'ApiBundle:Download:postCategoryGroups'),
	array('_method' => 'POST', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_category_group', new Route(
	'/downloads/categories/{category_id}/groups/{group_id}',
	array('_controller' => 'ApiBundle:Download:getCategoryGroup'),
	array('_method' => 'GET', 'category_id' => '\\d+', 'group_id' => '\\d+'),
	array()
));

$collection->add('api_downloads_category_group_delete', new Route(
	'/downloads/categories/{category_id}/groups/{group_id}',
	array('_controller' => 'ApiBundle:Download:deleteCategoryGroup'),
	array('_method' => 'DELETE', 'category_id' => '\\d+', 'group_id' => '\\d+'),
	array()
));

################################################################################
# News
################################################################################

$collection->add('api_news', new Route(
	'/news',
	array('_controller' => 'ApiBundle:News:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_news_post', new Route(
	'/news',
	array('_controller' => 'ApiBundle:News:newNews'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_news_news', new Route(
	'/news/{news_id}',
	array('_controller' => 'ApiBundle:News:getNews'),
	array('_method' => 'GET', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_post', new Route(
	'/news/{news_id}',
	array('_controller' => 'ApiBundle:News:postNews'),
	array('_method' => 'POST', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_delete', new Route(
	'/news/{news_id}',
	array('_controller' => 'ApiBundle:News:deleteNews'),
	array('_method' => 'DELETE', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_comments', new Route(
	'/news/{news_id}/comments',
	array('_controller' => 'ApiBundle:News:getNewsComments'),
	array('_method' => 'GET', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_comments_new', new Route(
	'/news/{news_id}/comments',
	array('_controller' => 'ApiBundle:News:newNewsComment'),
	array('_method' => 'POST', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_comments_comment', new Route(
	'/news/{news_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:News:getNewsComment'),
	array('_method' => 'GET', 'news_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_comments_comment_post', new Route(
	'/news/{news_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:News:postNewsComment'),
	array('_method' => 'POST', 'news_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_comments_comment_delete', new Route(
	'/news/{news_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:News:deleteNewsComment'),
	array('_method' => 'DELETE', 'news_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_labels', new Route(
	'/news/{news_id}/labels',
	array('_controller' => 'ApiBundle:News:getNewsLabels'),
	array('_method' => 'GET', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_labels_post', new Route(
	'/news/{news_id}/labels',
	array('_controller' => 'ApiBundle:News:postNewsLabels'),
	array('_method' => 'POST', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_label', new Route(
	'/news/{news_id}/labels/{label}',
	array('_controller' => 'ApiBundle:News:getNewsLabel'),
	array('_method' => 'GET', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_news_label_delete', new Route(
	'/news/{news_id}/labels/{label}',
	array('_controller' => 'ApiBundle:News:deleteNewsLabel'),
	array('_method' => 'DELETE', 'news_id' => '\\d+'),
	array()
));

$collection->add('api_news_validating_comments', new Route(
	'/news/validating-comments',
	array('_controller' => 'ApiBundle:News:getValidatingComments'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_news_categories', new Route(
	'/news/categories',
	array('_controller' => 'ApiBundle:News:getCategories'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_news_categories_post', new Route(
	'/news/categories',
	array('_controller' => 'ApiBundle:News:postCategories'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_news_category', new Route(
	'/news/categories/{category_id}',
	array('_controller' => 'ApiBundle:News:getCategory'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_news_category_post', new Route(
	'/news/categories/{category_id}',
	array('_controller' => 'ApiBundle:News:postCategory'),
	array('_method' => 'POST', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_news_category_delete', new Route(
	'/news/categories/{category_id}',
	array('_controller' => 'ApiBundle:News:deleteCategory'),
	array('_method' => 'DELETE', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_news_category_news', new Route(
	'/news/categories/{category_id}/news',
	array('_controller' => 'ApiBundle:News:getCategoryNews'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_news_category_groups', new Route(
	'/news/categories/{category_id}/groups',
	array('_controller' => 'ApiBundle:News:getCategoryGroups'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_news_category_groups_post', new Route(
	'/news/categories/{category_id}/groups',
	array('_controller' => 'ApiBundle:News:postCategoryGroups'),
	array('_method' => 'POST', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_news_category_group', new Route(
	'/news/categories/{category_id}/groups/{group_id}',
	array('_controller' => 'ApiBundle:News:getCategoryGroup'),
	array('_method' => 'GET', 'category_id' => '\\d+', 'group_id' => '\\d+'),
	array()
));

$collection->add('api_news_category_group_delete', new Route(
	'/news/categories/{category_id}/groups/{group_id}',
	array('_controller' => 'ApiBundle:News:deleteCategoryGroup'),
	array('_method' => 'DELETE', 'category_id' => '\\d+', 'group_id' => '\\d+'),
	array()
));

################################################################################
# Knowledgebase
################################################################################

$collection->add('api_kb', new Route(
	'/kb',
	array('_controller' => 'ApiBundle:Kb:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_kb_post', new Route(
	'/kb',
	array('_controller' => 'ApiBundle:Kb:newArticle'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_kb_article', new Route(
	'/kb/{article_id}',
	array('_controller' => 'ApiBundle:Kb:getArticle'),
	array('_method' => 'GET', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_post', new Route(
	'/kb/{article_id}',
	array('_controller' => 'ApiBundle:Kb:postArticle'),
	array('_method' => 'POST', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_delete', new Route(
	'/kb/{article_id}',
	array('_controller' => 'ApiBundle:Kb:deleteArticle'),
	array('_method' => 'DELETE', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_votes', new Route(
	'/kb/{article_id}/votes',
	array('_controller' => 'ApiBundle:Kb:getArticleVotes'),
	array('_method' => 'GET', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_comments', new Route(
	'/kb/{article_id}/comments',
	array('_controller' => 'ApiBundle:Kb:getArticleComments'),
	array('_method' => 'GET', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_comments_new', new Route(
	'/kb/{article_id}/comments',
	array('_controller' => 'ApiBundle:Kb:newArticleComment'),
	array('_method' => 'POST', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_comments_comment', new Route(
	'/kb/{article_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Kb:getArticleComment'),
	array('_method' => 'GET', 'article_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_comments_comment_post', new Route(
	'/kb/{article_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Kb:postArticleComment'),
	array('_method' => 'POST', 'article_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_comments_comment_delete', new Route(
	'/kb/{article_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Kb:deleteArticleComment'),
	array('_method' => 'DELETE', 'article_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_attachments', new Route(
	'/kb/{article_id}/attachments',
	array('_controller' => 'ApiBundle:Kb:getArticleAttachments'),
	array('_method' => 'GET', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_attachments_post', new Route(
	'/kb/{article_id}/attachments',
	array('_controller' => 'ApiBundle:Kb:newArticleAttachment'),
	array('_method' => 'POST', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_attachment', new Route(
	'/kb/{article_id}/attachments/{attachment_id}',
	array('_controller' => 'ApiBundle:Kb:getArticleAttachment'),
	array('_method' => 'GET', 'article_id' => '\\d+', 'attachment_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_attachment_delete', new Route(
	'/kb/{article_id}/attachments/{attachment_id}',
	array('_controller' => 'ApiBundle:Kb:deleteArticleAttachment'),
	array('_method' => 'DELETE', 'article_id' => '\\d+', 'attachment_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_labels', new Route(
	'/kb/{article_id}/labels',
	array('_controller' => 'ApiBundle:Kb:getArticleLabels'),
	array('_method' => 'GET', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_labels_post', new Route(
	'/kb/{article_id}/labels',
	array('_controller' => 'ApiBundle:Kb:postArticleLabels'),
	array('_method' => 'POST', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_label', new Route(
	'/kb/{article_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Kb:getArticleLabel'),
	array('_method' => 'GET', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_article_label_delete', new Route(
	'/kb/{article_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Kb:deleteArticleLabel'),
	array('_method' => 'DELETE', 'article_id' => '\\d+'),
	array()
));

$collection->add('api_kb_validating_comments', new Route(
	'/kb/validating-comments',
	array('_controller' => 'ApiBundle:Kb:getValidatingComments'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_kb_categories', new Route(
	'/kb/categories',
	array('_controller' => 'ApiBundle:Kb:getCategories'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_kb_categories_post', new Route(
	'/kb/categories',
	array('_controller' => 'ApiBundle:Kb:postCategories'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_kb_category', new Route(
	'/kb/categories/{category_id}',
	array('_controller' => 'ApiBundle:Kb:getCategory'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_kb_category_post', new Route(
	'/kb/categories/{category_id}',
	array('_controller' => 'ApiBundle:Kb:postCategory'),
	array('_method' => 'POST', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_kb_category_delete', new Route(
	'/kb/categories/{category_id}',
	array('_controller' => 'ApiBundle:Kb:deleteCategory'),
	array('_method' => 'DELETE', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_kb_category_articles', new Route(
	'/kb/categories/{category_id}/articles',
	array('_controller' => 'ApiBundle:Kb:getCategoryArticles'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_kb_category_groups', new Route(
	'/kb/categories/{category_id}/groups',
	array('_controller' => 'ApiBundle:Kb:getCategoryGroups'),
	array('_method' => 'GET', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_kb_category_groups_post', new Route(
	'/kb/categories/{category_id}/groups',
	array('_controller' => 'ApiBundle:Kb:postCategoryGroups'),
	array('_method' => 'POST', 'category_id' => '\\d+'),
	array()
));

$collection->add('api_kb_category_group', new Route(
	'/kb/categories/{category_id}/groups/{group_id}',
	array('_controller' => 'ApiBundle:Kb:getCategoryGroup'),
	array('_method' => 'GET', 'category_id' => '\\d+', 'group_id' => '\\d+'),
	array()
));

$collection->add('api_kb_category_group_delete', new Route(
	'/kb/categories/{category_id}/groups/{group_id}',
	array('_controller' => 'ApiBundle:Kb:deleteCategoryGroup'),
	array('_method' => 'DELETE', 'category_id' => '\\d+', 'group_id' => '\\d+'),
	array()
));

$collection->add('api_kb_fields', new Route(
	'/kb/fields',
	array('_controller' => 'ApiBundle:Kb:getFields'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_kb_products', new Route(
	'/kb/products',
	array('_controller' => 'ApiBundle:Kb:getProducts'),
	array('_method' => 'GET'),
	array()
));

################################################################################
# Feedback
################################################################################

$collection->add('api_feedback', new Route(
	'/feedback',
	array('_controller' => 'ApiBundle:Feedback:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_feedback_post', new Route(
	'/feedback',
	array('_controller' => 'ApiBundle:Feedback:newFeedback'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_feedback_feedback', new Route(
	'/feedback/{feedback_id}',
	array('_controller' => 'ApiBundle:Feedback:getFeedback'),
	array('_method' => 'GET', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_post', new Route(
	'/feedback/{feedback_id}',
	array('_controller' => 'ApiBundle:Feedback:postFeedback'),
	array('_method' => 'POST', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_delete', new Route(
	'/feedback/{feedback_id}',
	array('_controller' => 'ApiBundle:Feedback:deleteFeedback'),
	array('_method' => 'DELETE', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_votes', new Route(
	'/feedback/{feedback_id}/votes',
	array('_controller' => 'ApiBundle:Feedback:getFeedbackVotes'),
	array('_method' => 'GET', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_comments', new Route(
	'/feedback/{feedback_id}/comments',
	array('_controller' => 'ApiBundle:Feedback:getFeedbackComments'),
	array('_method' => 'GET', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_comments_new', new Route(
	'/feedback/{feedback_id}/comments',
	array('_controller' => 'ApiBundle:Feedback:newFeedbackComment'),
	array('_method' => 'POST', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_comments_comment', new Route(
	'/feedback/{feedback_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Feedback:getFeedbackComment'),
	array('_method' => 'GET', 'feedback_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_comments_comment_post', new Route(
	'/feedback/{feedback_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Feedback:postFeedbackComment'),
	array('_method' => 'POST', 'feedback_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_comments_comment_delete', new Route(
	'/feedback/{feedback_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Feedback:deleteFeedbackComment'),
	array('_method' => 'DELETE', 'feedback_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_merge', new Route(
	'/feedback/{feedback_id}/merge/{other_feedback_id}',
	array('_controller' => 'ApiBundle:Feedback:mergeFeedback'),
	array('_method' => 'POST', 'feedback_id' => '\\d+', 'other_feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_attachments', new Route(
	'/feedback/{feedback_id}/attachments',
	array('_controller' => 'ApiBundle:Feedback:getFeedbackAttachments'),
	array('_method' => 'GET', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_attachments_post', new Route(
	'/feedback/{feedback_id}/attachments',
	array('_controller' => 'ApiBundle:Feedback:newFeedbackAttachment'),
	array('_method' => 'POST', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_attachment', new Route(
	'/feedback/{feedback_id}/attachments/{attachment_id}',
	array('_controller' => 'ApiBundle:Feedback:getFeedbackAttachment'),
	array('_method' => 'GET', 'feedback_id' => '\\d+', 'attachment_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_attachment_delete', new Route(
	'/feedback/{feedback_id}/attachments/{attachment_id}',
	array('_controller' => 'ApiBundle:Feedback:deleteFeedbackAttachment'),
	array('_method' => 'DELETE', 'feedback_id' => '\\d+', 'attachment_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_labels', new Route(
	'/feedback/{feedback_id}/labels',
	array('_controller' => 'ApiBundle:Feedback:getFeedbackLabels'),
	array('_method' => 'GET', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_labels_post', new Route(
	'/feedback/{feedback_id}/labels',
	array('_controller' => 'ApiBundle:Feedback:postFeedbackLabels'),
	array('_method' => 'POST', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_label', new Route(
	'/feedback/{feedback_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Feedback:getFeedbackLabel'),
	array('_method' => 'GET', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_feedback_label_delete', new Route(
	'/feedback/{feedback_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Feedback:deleteFeedbackLabel'),
	array('_method' => 'DELETE', 'feedback_id' => '\\d+'),
	array()
));

$collection->add('api_feedback_validating_comments', new Route(
	'/feedback/validating-comments',
	array('_controller' => 'ApiBundle:Feedback:getValidatingComments'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_feedback_categories', new Route(
	'/feedback/categories',
	array('_controller' => 'ApiBundle:Feedback:getCategories'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_feedback_status_categories', new Route(
	'/feedback/status-categories',
	array('_controller' => 'ApiBundle:Feedback:getStatusCategories'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_feedback_user_categories', new Route(
	'/feedback/user-categories',
	array('_controller' => 'ApiBundle:Feedback:getUserCategories'),
	array('_method' => 'GET'),
	array()
));

################################################################################
# Tasks
################################################################################

$collection->add('api_tasks', new Route(
	'/tasks',
	array('_controller' => 'ApiBundle:Task:search'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_tasks_post', new Route(
	'/tasks',
	array('_controller' => 'ApiBundle:Task:newTask'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_tasks_task', new Route(
	'/tasks/{task_id}',
	array('_controller' => 'ApiBundle:Task:getTask'),
	array('_method' => 'GET', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_post', new Route(
	'/tasks/{task_id}',
	array('_controller' => 'ApiBundle:Task:postTask'),
	array('_method' => 'POST', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_delete', new Route(
	'/tasks/{task_id}',
	array('_controller' => 'ApiBundle:Task:deleteTask'),
	array('_method' => 'DELETE', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_associations', new Route(
	'/tasks/{task_id}/associations',
	array('_controller' => 'ApiBundle:Task:getTaskAssociations'),
	array('_method' => 'GET', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_associations_post', new Route(
	'/tasks/{task_id}/associations',
	array('_controller' => 'ApiBundle:Task:postTaskAssociations'),
	array('_method' => 'POST', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_associated_item', new Route(
	'/tasks/{task_id}/associations/{assoc_id}',
	array('_controller' => 'ApiBundle:Task:getTaskAssociation'),
	array('_method' => 'GET', 'task_id' => '\\d+', 'assoc_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_associated_item_delete', new Route(
	'/tasks/{task_id}/associations/{assoc_id}',
	array('_controller' => 'ApiBundle:Task:deleteTaskAssociation'),
	array('_method' => 'DELETE', 'task_id' => '\\d+', 'assoc_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_comments', new Route(
	'/tasks/{task_id}/comments',
	array('_controller' => 'ApiBundle:Task:getTaskComments'),
	array('_method' => 'GET', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_comments_post', new Route(
	'/tasks/{task_id}/comments',
	array('_controller' => 'ApiBundle:Task:postTaskComments'),
	array('_method' => 'POST', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_comment', new Route(
	'/tasks/{task_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Task:getTaskComment'),
	array('_method' => 'GET', 'task_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_associated_item_delete', new Route(
	'/tasks/{task_id}/comments/{comment_id}',
	array('_controller' => 'ApiBundle:Task:deleteTaskComment'),
	array('_method' => 'DELETE', 'task_id' => '\\d+', 'comment_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_labels', new Route(
	'/tasks/{task_id}/labels',
	array('_controller' => 'ApiBundle:Task:getTaskLabels'),
	array('_method' => 'GET', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_labels_post', new Route(
	'/tasks/{task_id}/labels',
	array('_controller' => 'ApiBundle:Task:postTaskLabels'),
	array('_method' => 'POST', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_label', new Route(
	'/tasks/{task_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Task:getTaskLabel'),
	array('_method' => 'GET', 'task_id' => '\\d+'),
	array()
));

$collection->add('api_tasks_task_label_delete', new Route(
	'/tasks/{task_id}/labels/{label}',
	array('_controller' => 'ApiBundle:Task:deleteTaskLabel'),
	array('_method' => 'DELETE', 'task_id' => '\\d+'),
	array()
));

################################################################################
# Glossary
################################################################################

$collection->add('api_glossary', new Route(
	'/glossary',
	array('_controller' => 'ApiBundle:Glossary:list'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_glossary_lookup', new Route(
	'/glossary/lookup',
	array('_controller' => 'ApiBundle:Glossary:lookup'),
	array('_method' => 'GET'),
	array()
));

$collection->add('api_glossary_post', new Route(
	'/glossary',
	array('_controller' => 'ApiBundle:Glossary:newWord'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_glossary_word', new Route(
	'/glossary/{word_id}',
	array('_controller' => 'ApiBundle:Glossary:getWord'),
	array('_method' => 'GET', 'word_id' => '\\d+'),
	array()
));

$collection->add('api_glossary_word_delete', new Route(
	'/glossary/{word_id}',
	array('_controller' => 'ApiBundle:Glossary:deleteWord'),
	array('_method' => 'DELETE', 'word_id' => '\\d+'),
	array()
));

$collection->add('api_glossary_definition', new Route(
	'/glossary/definitions/{definition_id}',
	array('_controller' => 'ApiBundle:Glossary:getDefinition'),
	array('_method' => 'GET', 'definition_id' => '\\d+'),
	array()
));

$collection->add('api_glossary_definition_post', new Route(
	'/glossary/definitions/{definition_id}',
	array('_controller' => 'ApiBundle:Glossary:postDefinition'),
	array('_method' => 'POST', 'definition_id' => '\\d+'),
	array()
));

$collection->add('api_glossary_definition_delete', new Route(
	'/glossary/definitions/{definition_id}',
	array('_controller' => 'ApiBundle:Glossary:deleteDefinition'),
	array('_method' => 'DELETE', 'definition_id' => '\\d+'),
	array()
));

################################################################################
# Activity
################################################################################

$collection->add('api_dismiss_activity', new Route(
	'/activity/dismiss',
	array('_controller' => 'ApiBundle:Activity:dismiss'),
	array('_method' => 'POST'),
	array()
));

$collection->add('api_get_activity', new Route(
	'/activity/{since}',
	array('_controller' => 'ApiBundle:Activity:getActivity'),
	array('_method' => 'GET', 'since' => '\\d+'),
	array()
));

return $collection;
